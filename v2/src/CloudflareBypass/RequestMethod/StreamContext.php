<?php
namespace CloudflareBypass\RequestMethod;

class StreamContext
{
    /**
     * Cookies set per request.
     * @var array
     */
    private $cookies = array();

    /**
     * Request headers per request.
     * @var array
     */
    private $request_headers = array();

    /**
     * Response headers per request.
     * @var array
     */
    private $response_headers = array();

    /**
     * Context options.
     * @var array
     */
    private $context = array();

    /**
     * Request URL.
     * @var string
     */
    private $url;

    /**
     * Sets $this->URL to request URL.
     * Sets $this->context to given context options array.
     * Populates cookies in context options array into $this->cookies.
     * 
     * @access public
     * @param string $url Request URL
     * @param array $context Array of context options
     * @throws \ErrorException if $url is not a valid URL
     * @throws \ErrorException if $context is not a valid context
     */
    public function __construct($url, $context)
    {
        if (!is_string($url) || !parse_url($url)) {
            throw new \ErrorException('Url is not valid!');
        }

        if (!is_array($context) || !isset($context['http'])) {
            throw new \ErrorException('Context is not valid!');
        }

        $this->url = $url;
        $this->context = $context;

        $this->updateRequestHeaders();
        $this->updateCookies();
    }
    
    /**
     * Populates response headers into $this->response_headers.
     * Populates cookies set in response headers into $this->cookies.
     *
     * @access public
     * @see http://php.net/file-get-contents
     * @return string
     */
    public function fileGetContents()
    {
        // Convert headers into format compatible with cURL
        $http_headers = explode("\r\n", $this->context['http']['header']);

        // User agent can be set in 2 places, header and user_agent.
        if (strpos($this->context['http']['header'], 'User-Agent') === false) {
            if (isset($this->context['http']['user_agent'])) {
                $http_headers[] = 'User-Agent: ' . $this->context['http']['user_agent'];
            }
        }

        $follow_location = isset($this->context['http']['follow_location']) ? $this->context['http']['follow_location'] : 1;
        $method = isset($this->context['http']['method']) ? $this->context['http']['method'] : 'GET';

        // Unfortunately file_get_contents doesn't return contents of a 503 page.
        // Please advise if there is a better way to do this.
        $ch = curl_init($this->url);

        // Set options to match context.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $follow_location);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Get request body.
        $content = curl_exec($ch);
        curl_close($ch);

        // To populate $http_response_headers.
        @file_get_contents($this->url, false, stream_context_create($this->context));

        foreach ($http_response_header as $header) {
            if (strpos($header, 'HTTP') === 0) {
                // Store HTTP code as its own response header.
                $matches = explode(' ', $header);
                $this->response_headers['http_code'] = $matches[1];
            } elseif (strpos($header, 'Set-Cookie') !== false) {
                // Extract response cookie.
                list($_, $cookie) = explode(':', $header);
                list($name, $val) = explode('=', $cookie);
                // Ignore other config options.
                $val = substr($val, 0, strpos($val, ';'));
                // Store cookie.
                $this->cookie[$name] = $val;
            } elseif (strpos($header, ':') !== false) {
                // Store response header.
                list($name, $val) = explode(':', $header);
                $this->response_headers[$name] = $val;
            }
        }

        // Set cookies.
        foreach ($http_response_header as $header) {
            if (strpos($header, 'Set-Cookie') !== false) {
                // Match cookie name and value.
                preg_match('/Set-Cookie: (\w+)(.+)/', $header, $matches);
                $this->cookies[$matches[1]] = $matches[1] . $matches[2];
            }
        }

        return $content;
    }

    /**
     * Returns context array.
     *
     * @access public
     */
    public function getContext()
    {
        return $this->context;
    }
 
    /**
     * Returns full config for specified cookie name.
     *
     * @access public
     * @param string $cookie Cookie name
     * @return string Cookie value or NULL
     */
    public function getCookie($cookie)
    {
        if (isset($this->cookies[$cookie])) {
            return $this->cookies[$cookie];
        }

        return null;
    }

    /**
     * Returns value of specified request header.
     *
     * @access public
     * @param string $header Request header
     * @return string Request header or NULL
     */
    public function getRequestHeader($header)
    {
        if (isset($this->request_headers[$header])) {
            return $this->request_headers[$header];
        }

        if ($header == 'User-Agent' && isset($this->context['http']['user_agent'])) {
            return $this->context['http']['user_agent'];
        }

        return null;
    }

    /**
     * Returns value of specified response header.
     *
     * @access public
     * @param string $header Response header
     * @return string Response header or NULL
     */
    public function getResponseHeader($header)
    {
        if (isset($this->response_headers[$header])) {
            return $this->response_headers[$header];
        }
    }

    /**
     * Set Request URL.
     *
     * @access public
     * @param string $url Request URL.
     * @throws \ErrorException if $url is not a valid URL
     */
    public function setURL($url)
    {
        if (!is_string($url) || !parse_url($url)) {
            throw new \ErrorException('Url is not valid!');
        }

        $this->url = $url;
    }

    /**
     * Sets option in HTTP context array.
     *
     * @access public
     * @param string $name Option name.
     * @param string $val Option value.
     */
    public function setHttpContextOption($name, $val)
    {
        $this->context['http'][$name] = $val;
    }

    /**
     * Set Cookie in context array.
     *
     * @access public
     * @param string $name Cookie name.
     * @param string $val Cookie value.
     */
    public function setCookie($name, $val)
    {
        $settings = explode(';', $val);

        if (isset($this->request_headers['Cookie'])) {
            if (strpos($this->request_headers['Cookie'], $name . '=') !== false) {
                // Update value for specified cookie.
                $this->context['http']['header'] = preg_replace(
                    "/(Cookie:.+?)$name=(.+?);/", 
                    '$1' . $name . '=' . $settings[0] . ';', 
                    $this->context['http']['header']
                );
            } else {
                // Add cookie to cookie list.
                $this->context['http']['header'] = preg_replace(
                    "/(Cookie:.+?)\r\n/",
                    '$1' . $name . '=' . $settings[0] . ";\r\n",
                    $this->context['http']['header']
                );
            }
        } else {
            if (empty($this->request_headers)) {
                $this->context['http']['header'] = "";
            } elseif (substr($this->context['http']['header'], -2) !== "\r\n") {
                $this->context['http']['header'] .= "\r\n";
            }

            // Add cookie header with new cookie.
            $this->context['http']['header'] .= 'Cookie:' . $name . '=' . $settings[0] . ";\r\n";
        }

        $this->updateRequestHeaders();
        $this->updateCookies();
    }

    /**
     * Updates $this->request_headers to match with context array. 
     *
     * @access private
     */
    private function updateRequestHeaders()
    {
        // Extract request headers.
        $headers = explode("\r\n", $this->context['http']['header']);
        $headers_count = count($headers);

        // Set request headers.
        for ($i=0; $i<$headers_count; $i++) {
            if (strpos($headers[$i], ':') !== false) {
                list($name, $val) = explode(':', $headers[$i]);
                $this->request_headers[$name] = $val;
            }
        }
    }

    /**
     * Updates $this->cookies to match with context array.
     *
     * @access private
     */
    private function updateCookies()
    {
        // Extract cookies.        
        if (isset($this->request_headers['Cookie'])) {
            $cookies = explode(';', $this->request_headers['Cookie']);
            $cookies_count = count($cookies);

            // Set cookies.
            for ($i=0; $i<$cookies_count; $i++) {
                if (strpos($cookies[$i], '=') !== false) {
                    list($name, $val) = explode('=', $cookies[$i]);
                    $this->cookies[$name] = $val;
                }
            }
        }
    }
}