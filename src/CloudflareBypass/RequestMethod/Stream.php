<?php
namespace CloudflareBypass\RequestMethod;

/**
 * Stream context wrapper.
 * @author Kyran Rana
 */
class Stream implements \CloudflareBypass\Base\RequestMethod\RequestMethod
{
    /**
     * Response headers.
     *
     * @var array
     */
    private $response_headers = array();

    /**
     * Url.
     *
     * @var string
     */
    private $url;

    /**
     * Stream context.
     *
     * @var resource
     */
    private $ctx;

    /**
     * Follow location.
     *
     * @var boolean
     */
    private $follow;



    /**
     * Initialises stream.
     *
     * @access public
     * @param string $url  request url
     * @param resource $ctx  stream context
     * @throws \ErrorException  if $ctx is not a valid stream context
     */
    public function __construct( $url, $ctx = null )
    {
        $this->url = $url;
        $this->ctx = $ctx;
    }


    // ------------------------------------------------------------------------------------------------

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
     * Closes request.
     *
     * @access public
     */
    public function close()
    {
        // Not implemented...
    }

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Getters

    // {{{ RequestMethod getters

    /**
     * Gets page.
     *
     * @access public
     * @return string  Page contents
     */
    public function getPage()
    {
        $opts = $this->getOptions();

        $opts['http']['ignore_errors']      = true;     // fetch contents of 503.
        $opts['http']['request_fulluri']    = true;     // request full uri.
        $opts['http']['follow_location']    = 0;        // disable built-in follow location.

        $contents = file_get_contents( $this->url, false, stream_context_create( $opts ) );

        // set response headers.
        $this->response_headers = $http_response_header;

        // set cookies.
        $this->setCookies( array_filter(
            $this->response_headers,

            // only extract cookies from response headers.
            function( $elem ) { return stripos( $elem, 'set-cookie' ) !== false; } ) );

        // follow location logic.
        if ($this->follow) {
            if (preg_match( '/301|302|100/', $this->getHttpCode() )) {
                foreach ($this->response_headers as $header) {
                    if (stripos( $header, 'location:' ) === 0) {
                        // fetch redirect url.
                        $path = trim(preg_replace( '/location:/i', '', $header ));
                        
                        preg_match( '/.+?:\/\/.+?(?=\/|$)/', $this->getUrl(), $matches );

                        // a hacky fix.
                        if ( strpos( $path, "://" ) === false ) {
                            $this->setUrl( $matches[0] . $path );
                        } else {
                            $this->setUrl( $path );
                        }

                        $this->getPage();

                        break;
                    }
                }
            }
        }

        return $contents;
    }


    /**
     * Gets http code for current request.
     *
     * @access public
     * @return integer  http code
     */
    public function getHttpCode()
    {
        preg_match( '/\d{3}/', $this->response_headers[0], $matches );

        return $matches[0];
    }


    /**
     * Gets url for current request.
     *
     * @access public
     * @return string  url.
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Gets all cookies for current request.
     * 
     * @access public
     * @return array  all cookies.
     */
    public function getCookies()
    {
        $cookie_string = $this->getHeader( "cookie" );

        if ($cookie_string === null)
            return array();

        $cookies = array_filter( 
            array_map(
                // trim all elements.
                function( $elem ) { return trim( $elem ); },
                
                explode( ';', $cookie_string ) ),

            // ignore empty elements.
            function( $elem ) { return strlen( $elem ) > 0; } );


        $new_cookies = [];

        foreach ($cookies as $cookie) {
            list( $name, $value ) = explode( '=', $cookie );

            $new_cookies[strtolower( $name )] = sprintf(
                "Name=%s; Value=%s;",

                $name, $value );
        }

        return $new_cookies;
    }


    /**
     * Get cookie set for current request.
     * 
     * @access public
     * @param string $cookie  cookie name.
     * @return mixed  cookie value | null
     */
    public function getCookie( $cookie )
    {
        $cookies = $this->getCookies();

        if (isset( $cookies[$cookie] )) {
            return $cookies[$cookie];
        }

        return null;
    }


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
     * Gets CF stream.
     *
     * @access public
     * @return resource  CF handle.
     */
    public function getCFResource()
    {
        return new Stream( $this->getUrl(), stream_context_create( $this->getOptions() ) );
    }


    /**
     * Get request headers for current request.
     *
     * @access public
     * @return mixed  request headers | null
     */
    public function getRequestHeaders()
    {
        $options = $this->getOptions();

        if (isset( $options['http']['header'] )) {
            if (is_array( $options['http']['header'] )) {
                $cookies = [];

                if (isset( $options['http']['header'][0] )) {
                    // normal array
                    $cookies = array_filter(
                        $options['http']['header'],

                        // ignore empty elements.
                        function( $elem ) { return strlen( $elem ) > 0; } );

                    $cookies[count( $cookies )-1] .= "\n";

                } else {
                    // associative array
                    $cookies = array_map( 
                        // convert to string
                        function( $key ) { return "$key: " . $options['http']['header'][$key]; },

                        array_keys( $options['http']['header'] ) );

                    end( $cookies );                       // move internal pointer to last key
                    $cookies[key( $cookies )] .= "\n";
                    reset( $cookies );                     // reset internal pointer to first key (to prevent bugged iterations)
                }

                return $cookies;

            } else {
                // string form
                $cookies = array_filter(
                    preg_split( "/\r\n|\n/", $options['http']['header'] ),

                    // ignore empty elements.
                    function( $elem ) { return strlen( $elem ) > 0; } );

                $cookies[count( $cookies )-1] .= "\n";

                return $cookies;
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

        if ($header === 'user-agent') {
            return $this->getUserAgent();
        }

        return $this->getHeader( $header );
    }


    /**
     * Get response headers for current request.
     *
     * @access public
     * @return array  response headers.
     */
    public function getResponseHeaders()
    {
        return $this->response_headers;
    }

    // }}}


    /**
     * Get context for current request.
     *
     * @return resource $ctx
     */
    public function getContext()
    {
        return $this->getResource();
    }

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Private Getters

    /**
     * Gets user agent for current request.
     * 
     * @access public
     * @return string  User agent string.
     */
    private function getUserAgent()
    {
        $opts = $this->getOptions();

        if (isset( $opts['http']['user_agent'] ))
            return $opts['http']['user_agent'];
        else {
            return $this->getHeader('user-agent');
        }
    }


    /**
     * Get header set for current request.
     * 
     * @access public
     * @param string $header  Header name.
     * @return mixed  Header value | null
     */
    private function getHeader( $header )
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
      
            $headers = array_filter( 
                ( !is_array( $opts['http']['header'] ) ?
                    // normal array
                    preg_split( '/\r\n|\n/', $opts['http']['header'] ) :
                    
                    // string form
                    $opts['http']['header'] ),

                // ignore empty elements
                function( $elem ) { return strlen( $elem ) > 0; } );

            foreach ($headers as $rheader) {
                if (stripos( $rheader, $header ) !== false) {
                    list( $header_name, $header_value ) = explode( ':', $rheader );

                    return trim( $header_value );
                }
            }
        }

        return null;
    }

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Setters

    // {{{ RequestMethod setters

    /**
     * Sets url for current request.
     *
     * @access public
     * @param string $url  new url.
     */
    public function setUrl( $url )
    {
        $this->url = $url;
    }


    /**
     * Set cookie for current request.
     *
     * @access public
     * @param string $name  cookie to set.
     * @param string $value  value for cookie.
     * @return void
     */
    public function setCookie( $name, $value )
    {
        $cookies = $this->getCookies();

        $cookies[$name] = $value;

        $new_cookies = implode( " ", array_map( 
            // extract name and value.
            function( $elem ) {

                preg_match( '/Name=(.+?); Value=(.+?);/i', $elem, $matches );
                return $matches[1] . '=' . $matches[2] . ';';
            
            },
            $cookies ) );

        $this->setCookieInContext( $new_cookies );
    }


    /**
     * Set follow location 
     *
     * @access public
     * @param boolean $follow  follow location.
     */
    public function setFollowLocation( $follow )
    {
        $opts = $this->getOptions();

        $this->follow = $follow;

        $this->ctx = stream_context_create( $opts );
    }


    /**
     * Sets request method for current request.
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

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Private Setters

    /**
     * Set context for current request.
     *
     * @access public
     * @param resource $ctx  New context.
     */
    private function setContext( $ctx )
    {
        $this->ctx = $ctx;
    }


    /**
     * Sets cookies for current request.
     *
     * @access public
     * @param array $cookies  set-cookie headers.
     * @return void
     */
    private function setCookies( $cookies )
    {
        foreach ($cookies as $cookie) {
            preg_match( '/set\-cookie:\s*(\w+)=(.+?);/i', $cookie, $matches );

            $this->setCookie( $matches[1], 'Name=' . $matches[1] . '; Value=' . $matches[2] . ';' );
        }
    }


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
                    // associative array.
                    $opts['http']['header']['cookie'] = $cookie;

                    $cookie_found = 2;
                }
            } 

            if ($cookie_found !== 2) {
                $headers = $opts['http']['header'];

                $is_string = false;

                if (!is_array( $headers )) {
                    // string form.
                    $headers = array_filter( 
                        preg_split( '/\r\n|\n/', $headers ),

                        // ignore empty elements.
                        function( $elem ) { return strlen( $elem ) > 0; } );

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
                        // string form.
                        $opts['http']['header'] = implode( "\r\n", $headers ) . "\r\n";
                    } else {
                        // normal array. 
                        $opts['http']['header'] = $headers;
                    }
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
                // associative array.
                $opts['http']['header']['cookie'] = $cookie;
            } else {
                // normal array.
                $opts['http']['header'][] = "cookie: $cookie";
            }
        } else {
            // string form.
            $opts['http']['header'] .= "cookie: $cookie\r\n";
        }

        return $opts;
    }

    // }}}
}
