<?php
namespace CloudflareBypass\RequestMethod;

class Curl
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
     * cURL handle.
     * @var resource
     */
    private $ch;





    /**
     * Initialises cURL handle.
     *
     * @access public
     * @param resource $curl cURL handle
     * @throws \ErrorException if $ch IS NOT a cURL handle
     */
    public function __construct($ch = null)
    {
        if (!is_resource($ch)) {
            throw new \ErrorException('Curl handle is required!');
        }

        $this->ch = $ch;
    }





    // {{{ cURL Functions 

    /**
     * @access public
     * @see http://php.net/curl-setopt
     * @param integer $opt
     * @param mixed $val
     */
    public function setopt( $opt, $val )
    {
        curl_setopt( $this->ch, $opt, $val );
    }


    /**
     * @access public
     * @see http://php.net/curl-getinfo
     * @param integer $opt (optional)
     * @return mixed $val
     */
    public function getInfo($opt = null)
    {
        $args = [ $this->ch ];

        if (func_num_args())
            $args[] = $opt;
        
        return call_user_func_array( 'curl_getinfo', $args );
    }


    /**
     * Executes cURL request.
     *
     * @access public
     * @see http://php.net/curl_exec
     * @return mixed
     */
    public function exec()
    {
        $this->cookies              = array();
        $this->request_headers      = array();

        $res    = curl_exec( $this->ch );
        $info   = curl_getinfo( $this->ch );

        if (isset($info['request_header'])) {
            $headers = explode( "\n", $info['request_header'] );

            foreach ( $headers as $header ) {
                // set request header
                if (strpos( $header, ':' ) !== false)
                    $this->setRequestHeader( $this->ch, $header );

                // set request cookie
                if (strpos( $header, 'Cookie' ) !== false)
                    $this->setRequestCookie( $this->ch, $header );
            }
        }

        return $res;
    }


    /**
     * @access public
     * @see http://php.net/curl_close
     */
    public function close()
    {
        curl_close( $this->ch );
    }

    // }}}





    // {{{ Getters

    /**
     * Get cURL handle
     *
     * @access public
     * @return resource  cURL handle.
     */
    public function getResource()
    {
        return $this->ch;
    }


    /**
     * Gets copy of cURL handle
     *
     * @access public
     * @return resource  Copy of cURL handle.
     */
    public function getCopyOfResource()
    {
        $ch = curl_copy_handle( $this->ch );

        return new Curl( $ch );
    }


    /**
     * Get request headers set for current request.
     *
     * @access public
     * @return array  Request headers.
     */
    public function getRequestHeaders()
    {
        return $this->request_headers;
    }


    /**
     * Get cookies set for current request.
     *
     * @access public
     * @return {array}  Cookies.
     */
    public function getCookies()
    {
        return $this->cookies;
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
        if (isset($this->cookies[$cookie]))
            return $this->cookies[$cookie];

        return null;
    }


    /**
     * Get response header for current request.
     *
     * @access public
     * @param string $header  Request header
     * @return mixed  Request header | null
     */
    public function getRequestHeader( $header )
    {
        if (isset($this->request_headers[$header]))
            return $this->request_headers[$header];

        return null;
    }

    // }}}





    // {{{ Setters

    /**
     * Sets request header for current request.
     *
     * @access public 
     * @param resource $ch  cURL handle
     * @param string $header  Request header
     */
    public function setRequestHeader( $ch, $header )
    {
        list( $name, $val ) = explode( ':', $header );

        $this->request_headers[$name] = $val;
    }


    /**
     * Sets cookie from request headers for current request.
     *
     * @access public
     * @param resource $ch  cURL handle
     * @param string $header  Request header
     */
    public function setRequestCookie( $ch, $header )
    {
        $value      = substr( $header, strpos( $header, ':' )+1 );
        $cookies    = explode( ';', $value );

        foreach ( $cookies as $cookie ) {
            // Trim cookie.
            $cookie = trim( $cookie );

            list( $cookie, $val ) = explode( '=', $cookie );

            $this->cookies[$cookie] = $val;
        }
    }


    /**
     * Set cookies from response headers for current request.
     *
     * @access public
     * @param resource $ch  cURL handle
     * @param string $header  Response header.
     * @return integer  Length of response header.
     */
    public function setResponseHeader( $ch, $header )
    {
        if (strpos($header, 'Set-Cookie') !== false) {
            
            preg_match('/Set\-Cookie: ([^=]+)(.+)/', $header, $matches);
    
            $this->cookies[$matches[1]] = $matches[1] . $matches[2];
        }

        return strlen($header);
    }

    // }}}
}