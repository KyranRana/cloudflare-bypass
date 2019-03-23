<?php
namespace CloudflareBypass\RequestMethod;

/**
 * cURL wrapper.
 * @author Kyran Rana
 */
class Curl implements \CloudflareBypass\Base\RequestMethod\RequestMethod
{
    /**
     * Request headers.
     *
     * @var array
     */
    private $request_headers = array();

    /**
     * Response headers.
     *
     * @var array
     */
    private $response_headers = array();

    /**
     * cURL handle.
     *
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
    public function __construct( $ch=null )
    {
        if (!is_resource( $ch ))
            throw new \ErrorException('Curl handle is required!');

        $this->ch = $ch;

        // secretly enable cookie engine.
        $this->setOpt( CURLOPT_COOKIELIST, "" );
    }


    // ------------------------------------------------------------------------------------------------

    // {{{ cURL Functions 

    /**
     * Sets a cURL option.
     *
     * @access public
     * @see http://php.net/curl_setopt
     * @param integer $opt
     * @param mixed $val
     * @return bool
     */
    public function setOpt( $opt, $val )
    {
        return curl_setopt( $this->ch, $opt, $val );
    }


    /**
     * Gets cURL info object.
     *
     * @access public
     * @see http://php.net/curl_getinfo
     * @param resource $ch
     * @param mixed $opt
     * @return object
     */
    public function getInfo( $opt=null )
    {
        $args = array();
        $args[] = $this->ch;

        if (func_num_args()) {
            $args[] = $opt;
        }

        return call_user_func_array( 'curl_getinfo', $args );
    }


    /**
     * Executes cURL request.
     * 
     * @access public
     * @see http://php.net/curl_exec
     * @return string
     */
    public function exec()
    {
        return curl_exec( $this->ch );
    }


    /**
     * Closes cURL request.
     *
     * @access public
     * @see http://php.net/curl_close
     */
    public function close()
    {
        if ($this->ch != null) {
            curl_close( $this->ch );
        }
        
        $this->ch = null;
    }

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Getters

    // {{{ RequestMethod getters

    /**
     * Executes current request.
     *
     * @access public
     * @return string  page contents
     */
    public function getPage()
    {
        // clear request headers
        $this->request_headers = array();

        // clear response headers
        $this->response_headers = array();

        $response   = $this->exec();
        $info       = $this->getInfo();

        if (isset( $info['request_header'] )) {
            // set request headers
            $this->request_headers = preg_split( "/\r\n|\n/", $info['request_header'] );
        }

        return $response;
    }


    /**
     * Gets http code for current request.
     *
     * @access public
     * @return integer  http code
     */
    public function getHttpCode()
    {
        return $this->getInfo( CURLINFO_HTTP_CODE );
    }


    /**
     * Gets url for current request.
     *
     * @access public
     * @return string  url.
     */
    public function getUrl()
    {
        $info = $this->getInfo();

        return $info['url'];
    }


    /**
     * Gets all cookies for current request.
     * 
     * @access public
     * @return array  all cookies.
     */
    public function getCookies()
    {
        $cookies        = $this->getInfo( CURLINFO_COOKIELIST );
        $new_cookies    = array();

        foreach ($cookies as $cookie) {
            // Reference: https://curl.haxx.se/libcurl/c/CURLOPT_COOKIELIST.html
            list( $hostname, $subdomain, $path, $secure, $expiry, $name, $value ) = explode( "\t", $cookie );

            $new_cookies[strtolower( $name )] = sprintf( 
                "Hostname=%s; Subdomain=%s; Path=%s; Secure=%s; Expiry=%s; Name=%s; Value=%s;",

                $hostname, $subdomain, $path, $secure, $expiry, $name, $value );
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
        $cookies    = $this->getCookies();
        $cookie     = strtolower( $cookie );

        if (isset( $cookies[$cookie] )) {
            return $cookies[$cookie];
        }

        return null;
    }


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
     * Gets CF handle.
     *
     * @access public
     * @return resource  CF handle.
     */
    public function getCFResource()
    {
        $ch = new Curl( curl_copy_handle( $this->ch ) );

        $ch->setOpt( CURLINFO_HEADER_OUT,       true );    
        $ch->setOpt( CURLOPT_HEADERFUNCTION,    array( $ch, 'setResponseHeader' ) );

        // secretly enable cookie engine.
        $ch->setOpt( CURLOPT_COOKIELIST, "" );

        return $ch;
    }


    /**
     * Get request headers for current request.
     *
     * @access public
     * @return array  request headers
     */
    public function getRequestHeaders()
    {
        return $this->request_headers;
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
        foreach ($this->request_headers as $rheader) {
            if (strpos( $rheader, ':' ) !== false) {
                list( $name, $value ) = explode( ':', $rheader );

                if (strtolower( $name ) === strtolower( $header )) {
                    return $value;
                }
            }
        }

        return null;
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

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Setters

    // {{{ RequestMethod setters

    /**
     * Sets url for current request.
     *
     * @access public
     * @param string $url  new url.
     * @return void
     */
    public function setUrl( $url )
    {
        $this->setOpt( CURLOPT_URL, $url );
    }


    /**
     * Sets follow location for current request.
     *
     * @access public
     * @param boolean $follow  TRUE to follow location.
     * @return void
     */
    public function setFollowLocation( $follow )
    {
        $this->setOpt( CURLOPT_FOLLOWLOCATION, $follow );
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
        $cookie = implode( "\t", array_map( function( $elem ) {
            
            list( $_, $val ) = explode( '=', $elem );
            return substr( $val, 0, -1 );

        }, explode( " ", $value )) );

        $this->setOpt( CURLOPT_COOKIELIST, $cookie );
    }


    /**
     * Sets request method.
     * 
     * @access public
     * @param string $method  Request method.
     */
    public function setRequestMethod( $request_method )
    {
        if ($request_method === "GET") {
            $this->setOpt( CURLOPT_HTTPGET, true );
        }

        $this->setOpt( CURLOPT_CUSTOMREQUEST, $request_method );
    }

    // }}}

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Private Setters

    /**
     * Adds response header to response header array for current request.
     *
     * @access private 
     * @param resource $ch  cURL handle.
     * @param string $header  response header.
     * @return integer  response header length.
     */
    public function setResponseHeader( $ch, $header )
    {
        if (trim( $header ) !== "") {
            $this->response_headers[] = trim( $header );
        }

        return mb_strlen( $header );
    }

    // }}}
}