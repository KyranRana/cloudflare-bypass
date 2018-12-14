<?php
namespace CloudflareBypass\Base\RequestMethod;

interface RequestMethod {

    // {{{ Getters

    public function getPage();                                  // Gets page results.
    public function getPageInfo();                              // Gets page headers (http_code, url).
    
    public function getResource();                              // Gets resource.
    public function getCopyOfResource();                        // Gets copy of resource.

    public function getCookies();                               // Gets cookies for current request.
    public function getCookie( $cookie );                       // Gets cookie for current request.
    
    public function getRequestHeader( $request_header );        // Gets request header.

    // }}}



    // {{{ Setters

    public function setUrl( $url );                             // Sets url.
    public function setCookie( $cookie );                       // Sets cookie.
    public function setVerboseMode( $verbose_mode );            // Sets verbose mode. 
    public function setFollowLocation( $follow_location );      // Sets follow location.

    // }}}
 


    // {{{ Showers

    public function showRequestHeaders();                       // Enables request headers to be shown

    // }}}
}