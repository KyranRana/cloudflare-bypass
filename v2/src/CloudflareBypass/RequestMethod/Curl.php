<?php
namespace CloudflareBypass\RequestMethod;

/**
 * cURL wrapper.
 * @author Kyran Rana
 */
class Curl implements \CloudflareBypass\Base\RequestMethod\RequestMethod
{
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
        if (!is_resource($ch))
            throw new \ErrorException('Curl handle is required!');

        $this->ch = $ch;
    }



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
    public function setopt( $opt, $val )
    {
        return curl_setopt( $this->ch, $opt, $val );
    }


    /**
     * Gets page.
     *
     * @access public
     * @see http://php.net/curl_exec
     * @return string  Page contents
     */
    public function getPage()
    {
        $this->request_headers = array();

        $response           = curl_exec( $this->ch );
        $response_info      = curl_getinfo( $this->ch );

        if (isset( $response_info['request_header'] )) {
            // Converts string full of headers into an array of headers.
            $headers = preg_split( "/\r\n|\n/", $response_info['request_header'] );

            $this->setRequestHeaders( $headers );
        }

        return $response;
    }


    /**
     * Get page info
     *
     * @access public
     * @param mixed $opt
     * @return array
     */
    public function getPageInfo( $opt = null )
    {
        $args = [ $this->ch ];

        if (func_num_args())
            $args[] = $opt;
        
        return call_user_func_array( 'curl_getinfo', $args );
    }


    /**
     * Closes cURL request.
     *
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
     * Gets all cookies.
     * 
     * @access public
     * @return array  All cookies.
     */
    public function getCookies()
    {
        return $this->getPageInfo( CURLINFO_COOKIELIST );
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
        foreach( $this->getCookies() as $cookieString ) {
            // Reference: https://curl.haxx.se/libcurl/c/CURLOPT_COOKIELIST.html
            list( $hostname, $subdomains, $path, $secure, $expiry, $name, $value ) = explode( "\t", $cookieString );

            if (strtolower( $name ) === strtolower( $cookie ))
                return $value;
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

        if (isset( $this->request_headers[$header] ))
            return $this->request_headers[$header];

        return null;
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
        $this->setopt( CURLOPT_URL, $url );
    }


    /**
     * Sets follow location for current request.
     *
     * @access public
     * @param boolean $follow_location  Follow location.
     */
    public function setFollowLocation( $follow_location )
    {
        $this->setopt( CURLOPT_FOLLOWLOCATION, $follow_location );
    }


    /**
     * Set cookie for current request.
     *
     * @access public
     * @param string $cookie  Cookie to set.
     */
    public function setCookie( $cookie )
    {
        $this->setopt( CURLOPT_COOKIELIST, $cookie );
    }


    /**
     * Sets verbose mode.
     * 
     * @access public
     * @param boolean $verbose_mode  Verbose mode.
     */
    public function setVerboseMode( $verbose_mode )
    {
        $this->setopt( CURLOPT_VERBOSE, $verbose_mode );
    }


    /**
     * Sets request method.
     * 
     * @access public
     * @param string $method  Request method.
     */
    public function setRequestMethod( $request_method )
    {
        if ($request_method === "GET")
            $this->setopt( CURLOPT_HTTPGET, true );
    
        $this->setopt( CURLOPT_CUSTOMREQUEST, $request_method );
    }

    // }}}



    // {{{ Private Setters

    /**
     * Sets request headers for current request.
     *
     * @access private 
     * @param string $headers  Request headers.
     */
    private function setRequestHeaders( $headers )
    {
        foreach ($headers as $header) {
            if (strpos( $header, ':' ) !== false) {
                list( $name, $value ) = explode( ':', $header );
                
                $this->request_headers[strtolower( $name )] = strtolower( $value );
            }
        }
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
        $this->setopt( CURLINFO_HEADER_OUT, true );
    }

    // }}}
}