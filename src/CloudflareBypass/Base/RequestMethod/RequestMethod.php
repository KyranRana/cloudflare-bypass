<?php
namespace CloudflareBypass\Base\RequestMethod;

/**
 * All request methods should implement this interface.
 * This interface states all methods required by CFBypasser.
 *
 * @author Kyran Rana
 */
interface RequestMethod {

    // {{{ Misc

    /**
     * Closes the current request.
     *
     * @access public
     */
    public function close();


    // ------------------------------------------------------------------------------------------------

    // {{{ Getters

    /**
     * Gets page for current request.
     * -> Should update cookies
     * -> Should update http code 
     * -> Should clear and update request headers.
     * -> Should clear and update response headers.
     *
     * @access public
     * @return string  page contents.
     */
    public function getPage();


    /**
     * Gets http code for current request.
     *
     * @access public
     * @return integer  http code.
     */
    public function getHttpCode();


    /**
     * Gets url for current request.
     *
     * @access public
     * @return string  url.
     */
    public function getUrl();


    /**
     * Gets all cookies for current request.
     *
     * @access public
     * @return object  all cookies.
     *
     * Format
     *
     * cURL:
     * {
     *  "cookie1": "Hostname=<string>;? Subdomain=<true|false>; Path=path;? Secure=<true|false>;? Expires=<date>;? Name=<string>; Value=<string>;",
     *  "cookie2": "Hostname=<string>;? Subdomain=<true|false>; Path=path;? Secure=<true|false>;? Expires=<date>;? Name=<string>; Value=<string>;"
     * }
     *
     * Stream context:
     * {
     *  "cookie1": "Name=<string>; Value=<string>;",
     *  "cookie2": "Name=<string>; Value=<string>;"
     * }
     *
     */
    public function getCookies();


    /**
     * Gets cookie named :cookie for current request.
     *
     * @access public
     * @param string $cookie  cookie name
     * @return mixed  cookie value | null
     *
     * Format
     *
     * cURL:
     * "Hostname=<string>;? Subdomain=<true|false>; Path=<string>;? Secure=<true|false>;? Expires=<date>;? Name=<string>; Value=<string>;"
     *
     * Stream context:
     * "Name=<string>; Value=<string>;"
     */
    public function getCookie( $cookie );


    /**
     * Gets request handle.
     *
     * @access public
     * @return object  request handle.
     */
    public function getResource();


    /**
     * Gets CF request handle.
     * - Handle with all required options set to bypass CF.
     *
     * @access public
     * @return object  cf request handle
     */
    public function getCFResource();


    /**
     * Gets request headers for current request.
     *
     * @access public
     * @return array  request headers.
     *
     * Format:
     * [
     *   "user-agent: <user agent>",
     *   "cookie: <cookie>",
     *   "no-cache: <cache header>"
     * ]
     */
    public function getRequestHeaders();


    /**
     * Gets request header :header for current request.
     *
     * @access public
     * @param string $header  header name
     * @return mixed  header value | null
     */
    public function getRequestHeader( $request_header );


    /**
     * Gets response headers for current request.
     *
     * @access public
     * @return array  response headers.
     * 
     * Format:
     * [
     *   "HTTP/1.1 200 OK",
     *   "Accept-Ranges: bytes",
     *   "Set-Cookie: <set cookie>"
     * ]
     */
    public function getResponseHeaders();

    // }}}

    // ------------------------------------------------------------------------------------------------

    // {{{ Setters

    /**
     * Sets url for current request.
     *
     * @access public
     * @param string $url  current url.
     * @return void
     */
    public function setUrl( $url );
    

    /**
     * Sets cookie for current request.
     *
     * @access public
     * @param string $name  cookie name.
     * @param string $value  cookie value.
     * @return void
     */
    public function setCookie( $name, $value );


    /**
     * Sets follow location for current request.
     *
     * @access public
     * @param boolean $follow  TRUE to enable follow location.
     * @return void
     */
    public function setFollowLocation( $follow );


    /**
     * Sets request method for current request.
     * 
     * @access public
     * @param string $request_method  request method.
     * @return void
     */
    public function setRequestMethod( $request_method );

    // }}}
}