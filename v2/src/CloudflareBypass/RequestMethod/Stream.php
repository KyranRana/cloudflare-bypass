<?php
namespace CloudflareBypass\RequestMethod;

class Stream implements \CloudflareBypass\Base\RequestMethod\RequestMethod
{
    /**
     * Response headers of current request.
     * @var array
     */
    private $response_headers = array();

    /**
     * Request url.
     * @var string
     */
    private $url;

    /**
     * Http code of current request.
     * @var integer
     */
    private $http_code;

    /**
     * Stream context.
     * @var resource
     */
    private $ctx;



    /**
     * Initialises stream.
     *
     * @access public
     * @param string $url  request url
     * @param resource $ctx  stream context
     * @throws \ErrorException if $ctx is not a valid stream context
     */
    public function __construct( $url, $ctx = null )
    {
        $this->url = $url;
        $this->ctx = $ctx;
    }



    // {{{ Stream Context Functions 

    /**
     * Gets options set within stream context.
     *
     * @access public
     * @see http://php.net/manual/en/function.stream-context-get-options.php
     * @return array
     */
    public function getOptions()
    {
        return stream_context_get_options( $this->ctx );
    }

    
    /**
     * Gets page.
     *
     * @access public
     * @return string  Page contents
     */
    public function getPage()
    {
        $opts           = $this->getOptions();
        $http_code      = 0;

        $opts['http']['ignore_errors']      = true;     // fetch contents of 503
        $opts['http']['request_fulluri']    = true;     // request full uri

        $contents = file_get_contents( $this->url, false, stream_context_create( $opts ) );

        foreach ($http_response_header as $header) {
            // set cookie
            if (stripos( $header, 'set-cookie' ) !== false) {

                list( $_, $cookie_value ) = explode( ':', $header );
                list( $cookie ) = explode( ';', $cookie_value );

                $this->setCookie( $cookie );
            }

            // set http code
            if (stripos( $header, 'HTTP' ) === 0) {
                preg_match( '/\d{3}/', $header, $matches );

                $this->http_code = $matches[0];
            }
        }

        return $contents;
    }


    /**
     * Gets page info.
     *
     * @access public
     * @return array
     */
    public function getPageInfo()
    {
        return [
            'url'           => $this->url,
            'http_code'     => $this->http_code,
            'options'       => $this->getOptions()
        ];
    }

    // }}}



    // {{{ Getters

    /**
     * Get stream.
     *
     * @access public
     * @return resource  Stream context.
     */
    public function getResource()
    {
        return $this->ctx;
    }


    /**
     * Gets copy of stream context.
     *
     * @access public
     * @return resource  Copy of stream context.
     */
    public function getCopyOfResource()
    {
        return new Stream( $this->url, stream_context_create( $this->getOptions() ) );
    }


    /**
     * Gets all cookies.
     *
     * @access public
     * @return array  All cookies.
     */
    public function getCookies()
    {
        $cookie_string = $this->getHeader( "cookie" );

        if ($cookie_string === null)
            return array();

        $cookies = array_map(
            function( $elem ) { 
                return trim( $elem ); 
            },
            explode( ';', $cookie_string ) );

        return array_filter( 
            $cookies,            
            function( $elem ){
                return strlen( $elem ) > 0;
            } );
    }


    /**
     * Get cookie set for current request.
     * 
     * @access public
     * @param string $cookie  Cookie name.
     * @return mixed  Cookie value | null
     */
    public function getCookie( $cookie )
    {
        $cookies = $this->getCookies();

        foreach ($cookies as $cookie_string) {
            if (stripos( $cookie_string, $cookie ) !== false) {
                list( $cookie_name, $cookie_value ) = explode( '=', $cookie_string );

                return trim( $cookie_value );
            }
        }

        return null;
    }


    /**
     * Get header set for current request.
     * 
     * @access public
     * @param string $header  Header name.
     * @return mixed  Header value | null
     */
    public function getHeader( $header )
    {
        $opts = $this->getOptions();

        $header = strtolower( $header );

        if (isset( $opts['http']['header'] )) {
            if (is_array( $opts['http']['header'] )) {
                if (isset( $opts['http']['header'][$header] )) {
                    // associative array
                    return $opts['http']['header'][$header];
                }
            }

            $headers = $opts['http']['header'];

            if (!is_array( $headers )) {
                // string
                $headers = array_filter( 
                    preg_split( '/\r\n|\n/', $headers ),
                    function( $elem ){
                        return strlen( $elem ) > 0;
                    } );
            }

            foreach ($headers as $header_string) {
                if (stripos( $header_string, $header ) !== false) {
                    list( $_, $header_value ) = explode( ':', $header_string );

                    return trim( $header_value );
                }
            }
        }

        return null;
    }


    /**
     * Get request header for current request.
     *
     * @access public
     * @param string $header  Request header
     * @return mixed  Request header | null
     */
    public function getRequestHeader( $header )
    {
        $header = strtolower( $header );

        if (strtolower( $header ) === 'user-agent') {
            return $this->getUserAgent();
        }

        return $this->getHeader( $header );
    }


    /**
     * Gets user agent for current request.
     * 
     * @access public
     * @return string  User agent string.
     */
    public function getUserAgent()
    {
        $opts = $this->getOptions();

        if (isset( $opts['http']['user_agent'] ))
            return $opts['http']['user_agent'];
        else {
            return $this->getHeader('user-agent');
        }
    }

    // }}}



    // {{{ Setters

    /**
     * Sets url for current request.
     *
     * @access public
     * @param string $url  New url.
     */
    public function setUrl( $url )
    {
        $this->url = $url;
    }


    /**
     * Set context for current request.
     *
     * @access public
     * @param resource $ctx  New context.
     */
    public function setContext( $ctx )
    {
        $this->ctx = $ctx;
    }


    /**
     * Set follow location 
     *
     * @access public
     * @param boolean $follow_location  Follow location.
     */
    public function setFollowLocation( $follow_location )
    {
        $opts = $this->getOptions();

        $opts['http']['follow_location'] = $follow_location;

        $this->ctx = stream_context_create( $opts );
    }


    /**
     * Set cookie for current request.
     *
     * @access public
     * @param string $cookie  Cookie to set.
     */
    public function setCookie( $cookie )
    {
        if ($cookie === "")
            return false;

        $cookies = $this->getCookies();

        list( $tcookie_name, $_ ) = explode( '=', $cookie );

        $cookie_found = false;

        foreach ($cookies as $key => $cookie_string) {
            if (strpos( $cookie_string, trim( $tcookie_name ) ) !== false) {
                // cookie found! update cookie!
                $cookies[$key]  = $cookie;
                $cookie_found   = true;

                break;
            }
        }

        if (!$cookie_found) 
            // add as a new cookie
            $cookies[] = $cookie;

        $new_cookie_string = implode( ';', $cookies );

        $this->setCookieInContext( $new_cookie_string );
    }


    /**
     * Sets verbose mode.
     * 
     * @access public
     * @param boolean $verbose_mode  Verbose mode.
     */
    public function setVerboseMode( $verbose_mode )
    {
        // Verbose mode needs to be decided...
    }


    /**
     * Sets request method.
     * 
     * @access public
     * @param string $method  Request method.
     */
    public function setRequestMethod( $request_method )
    {
        $opts = $this->getOptions();

        $opts['http']['method'] = $request_method;

        $this->setContext(stream_context_create( $opts ));
    }


    // }}}



    // {{{ Private Setters

    /**
     * Updates cookie in current context.
     *
     * @access private
     * @param string $cookie  New cookie.
     */
    private function setCookieInContext( $cookie )
    {
        $opts = $this->getOptions();

        $cookie_found = 0;

        if (isset( $opts['http']['header'] )) {
            if (is_array( $opts['http']['header'] )) {
                if (isset( $opts['http']['header']['cookie'] )) {
                    // associative array
                    $opts['http']['header']['cookie'] = $cookie;

                    $cookie_found = 2;
                }
            } 

            $headers = $opts['http']['header'];

            $is_string = false;

            if (!is_array( $headers )) {
                // string
                $headers = array_filter( 
                    preg_split( '/\r\n|\n/', $headers ), 
                    function( $elem ) {
                        return strlen( $elem ) > 0;
                    } );

                $is_string = true;
            }

            foreach ($headers as $key => $header) {
                if (stripos( $header, 'cookie' ) !== false) {
                    $headers[$key] = "cookie: $cookie";

                    $cookie_found = 1;
                    
                    break;
                }
            }

            if ($cookie_found === 1) {
                if ($is_string) {
                    // string
                    $opts['http']['header'] = implode( "\r\n", $headers ) . "\r\n";
                } else {
                    // normal array
                    $opts['http']['header'] = $headers;
                }
            }

            if (!$cookie_found) {
                $opts = $this->addCookieInContext( $opts, $cookie );
            }

            $this->setContext(stream_context_create( $opts ));
        }
    }


    /**
     * Adds new cookie to context provided.
     * 
     * @access private
     * @param array $opts  Context options.
     * @param string $cookie  Cookie to add.
     * @return array  Context options with new cookie field.
     */
    private function addCookieInContext( $opts, $cookie )
    {
        if (is_array( $opts['http']['header'] )) {
            if (count( $opts['http']['header'] ) > 0 && !isset( $opts['http']['header'][0] )) {
                // associative array
                $opts['http']['header']['cookie'] = $cookie;
            } else {
                // normal array
                $opts['http']['header'][] = "cookie: $cookie";
            }
        } else {
            // string
            $opts['http']['header'] .= "cookie: $cookie\r\n";
        }

        return $opts;
    }

    // }}}



    // {{{ Showers 

    /**
     * Enables request headers to be shown in info object.
     * 
     * @access public
     */
    public function showRequestHeaders()
    {
        // Not implemented...       
    }

    // }}}
}
