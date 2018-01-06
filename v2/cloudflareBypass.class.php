<?php
/**
 * cloudflare bypass v2.0
 *
 * A simple class which is intended to help you bypass the CloudFlare UAM page (Under Attack Mode) 
 * without the extra dependencies :)
 *
 * This is a rewrite of the original I programmed 2 years ago. This version should hopefully be
 * more light-weight than the first, easy to integrate, and more configurable for one-time requests.
 */
class CloudflareBypass
{
    /**
     * Max attempts at trying to bypass Cloudflare
     * @var integer
     */
    private $max_attempts = 5;
    
    // {{{  cURL Integration
    
    /**
     * Cookies set per cURL request
     * @var array
     */
    private $curl_cookies = array();
    
    /**
     * Global exceptions applied to cURL request
     * @var array
     */
    private $curl_exceptions = array();
    
    /**
     * Set global cURL exceptions
     *
     * @access public
     * @param $exceptions
     */
    public function setCurlConfig($exceptions)
    {
        $this->curl_exceptions = $exceptions;
    }
    
    /**
     * Bypass cloudflare using a cURL handle
     *
     * Given a curl handle this method will behave like "curl_exec" however it will take 
     * care of the cloudflare UAM page if it pops up. This method will temporarily set the 
     * following flags to do the job:
     *
     * - CURLOPT_RETURNTRANSFER=true
     * - CURLINFO_HEADER_OUT=true
     * - CURLOPT_HEADERFUNCTION      
     *
     * Since libcurl in PHP does not have "curl_getopts" or anything of the sort appropriate 
     * options have been added to the $exceptions config (in case you want to retain settings
     * you have applied for these options)
     *
     * Dependencies:
     * - CURLOPT_USERAGENT needs to be set!
     *
     * @access public
     * @param $curl_handle
     * @param $exceptions - Array with options:
     * "returntransfer_flag"    => value of CURLOPT_RETURNTRANSFER (default: false)
     * "headerfunc_flag"        => value of CURLOPT_HEADERFUNCTION (default: null)
     * "header_out_flag"        => value of CURLINFO_HEADER_OUT (default: false)
     *
     * @return request info
     */
    public function curlExec($curl_handle, $exceptions = [], $attempt = 1, $ua_check = true)
    {
        curl_setopt_array($curl_handle, array(
            CURLINFO_HEADER_OUT     => true,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADERFUNCTION  => array($this, '_getCurlHeaders')
        ));
        
        $uam_page    = curl_exec($curl_handle);
        $uam_headers = curl_getinfo($curl_handle);
        
        if ($uam_headers['http_code'] !== 503)
            return $uam_page;
        
        //
        // 1. Check if user agent is set in cURL handle
        //
        if ($ua_check && !$this->_getCurlCookie($uam_headers['request_header'], 'User-Agent'))
                throw new \ErrorException('curlExec -> CURLOPT_USERAGENT is a mandatory field!');
        
        //
        // 2. Extract "__cfuid" cookie
        //
        if(!($cfduid_cookie = $this->_getCurlCookie($uam_headers['request_header'], '__cfuid')))
            return $uam_page;

        curl_setopt($curl_handle, CURLOPT_COOKIELIST, $cfduid_cookie);
        
        //
        // 3. Solve challenge and request clearance link
        //
        curl_setopt($curl_handle, CURLOPT_URL, $this->_getClearanceLink($uam_page, $uam_headers['url']));
        
        $this->curl_cookies = array();
        
        $clearance_page     = curl_exec($curl_handle);
        $clearance_headers  = curl_getinfo($curl_handle);
        
        //
        // 4. Extract "cf_clearance" cookie
        //
        if(!($cfclearance_cookie = $this->_getCurlCookie($clearance_headers['request_header'], 'cf_clearance'))) {
            // Too many attempts at fetching clearance cookie...
            if ($attempts > $this->max_attempts)
                throw new \ErrorException("curlExec -> Too many attempts to get CF clearance!");

            list($cfuid_cookie, $cfclearance_cookie) = $this->curlExec($curl_handle, $exceptions, $attempts + 1, false);
        }

        if ($cfclearance_cookie && !$ua_check)
            return array($cfduid_cookie, $cfclearance_cookie);
        
        curl_setopt($curl_handle, CURLOPT_COOKIELIST, $cfclearance_cookie);
        
        //
        // 5. Request url again if follow location is not set
        //
        curl_setopt($curl_handle, CURLOPT_URL, $uam_headers['url']);
        
        if ($clearance_headers['http_code'] === 302)
            $clearance_page = curl_exec($curl_handle);
        
        //
        // 6. Revert cURL options
        //
        $this->_setDefaultCurlException($exceptions, 'headerfunc_flag', NULL);
        $this->_setDefaultCurlException($exceptions, 'returntransfer_flag', TRUE);
        $this->_setDefaultCurlException($exceptions, 'header_out_flag', FALSE);
        
        curl_setopt($curl_handle, CURLOPT_URL, $uam_headers['url']);
        curl_setopt($curl_handle, CURLOPT_HEADERFUNCTION, $exceptions['headerfunc_flag']);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, $exceptions['returntransfer_flag']);
        curl_setopt($curl_handle, CURLINFO_HEADER_OUT, $exceptions['header_out_flag']);
        
        $this->curl_cookies = array();
        
        //
        // 7. Get output
        //
        if (!$exceptions['returntransfer_flag'])
            echo $clearance_page;
        else
            return $clearance_page;
    }
    
    /**
     * cURL header function
     *
     * @access private
     * @param $curl_handle
     * @param $header
     *
     * @return bytes in $header
     */
    private function _getCurlHeaders($curl_handle, $header)
    {
        if (strpos($header, 'Set-Cookie') !== false) {
            preg_match('/Set-Cookie: (\w+)(.+)/', $header, $matches);
            $this->curl_cookies[$matches[1]] = $matches[1] . $matches[2];
        }
        return strlen($header);
    }
    
    /**
     * cURL set default exception
     *
     * Exception will be set to global default or $default (if not set)
     *
     * @access private
     * @param &$exception
     * @param $option
     * @param $default
     */
    private function _setDefaultCurlException(&$exception, $option, $default)
    {
        if (!isset($exceptions[$option])) {
            if (isset($this->curl_exceptions[$option]))
                $exceptions[$option] = $this->curl_exceptions[$option];
            else
                $exceptions[$option] = $default;
        }
    }

    /**
     * Get cURL full "Set-Cookie" header for cookie name
     *
     * @access private
     * @param $headers
     * @param $cookie
     *
     * @return mixed string|bool
     */
    private function _getCurlCookie($headers, $cookie)
    {
        $cookie = '';
        if (isset($this->curl_cookies[$cookie]))
            $cookie = $this->curl_cookies[$cookie];
        else {
            preg_match("/${cookie}[:=] (.+)/", $headers, $matches);

            if (isset($matches[1]))
                $cookie = "Set-Cookie: $cookie=" . $matches[1];
        }
        return $cookie === '' ? false : $cookie;
    }

    // }}}
    
    /**
     * Get Clearance Link 
     *
     * Given under attack mode page contents, will solve JS challenge and return clearance link
     * e.g. http://test/cdn-cgi/l/chk_jschl?jschl_vc=X&pass=X&jschl_answer=X
     *
     * @access private
     * @param $content
     * @param $url
     *
     * @return clearance link
     */
    private function _getClearanceLink($content, $url)
    {
        //
        // 1. Mimic waiting process
        //
        sleep(4);
        
        //
        // 2. Extract "jschl_vc" and "pass" params
        //
        preg_match_all('/name="\w+" value="(.+?)"/', $content, $matches);
        
        if (!isset($matches[1]) || !isset($matches[1][1]))
            throw new \ErrorException('Unable to fetch jschl_vc and pass values; maybe not protected?');
        
        $params = array();
        list($params['jschl_vc'], $params['pass']) = $matches[1];
        
        //
        // 3. Extract JavaScript challenge logic
        //
        preg_match_all('/:[!\[\]+()]+|[-*+\/]?=[!\[\]+()]+/', $content, $matches);
        
        if (!isset($matches[0]) || !isset($matches[0][0]))
            throw new \ErrorException('Unable to find javascript challenge logic; maybe not protected?');
        
        try {
            //
            // 4. Convert challenge logic to PHP
            //
            $php_code = "";
            foreach ($matches[0] as $js_code) {
                $js_code = str_replace(array(
                    ")+(",
                    "![]",
                    "!+[]",
                    "[]"
                ), array(
                    ").(",
                    "(!1)",
                    "(!0)",
                    "(0)"
                ), $js_code);
                
                $php_code .= '$params[\'jschl_answer\']' . ($js_code[0] == ':' ? '=' . substr($js_code, 1) : $js_code) . ';';
            }
            
            //
            // 5. Eval PHP and get solution
            //
            eval($php_code);
            $uri = parse_url($url);
            $params['jschl_answer'] += strlen($uri['host']);
            
            //
            // 6. Construct clearance link
            //
            $clearance_link = $uri['scheme'] . '://' . $uri['host'];
            $clearance_link .= '/cdn-cgi/l/chk_jschl?' . http_build_query($params);
            return $clearance_link;
        }
        catch (Exception $ex) {
            // PHP evaluation bug; inform user to report bug
            throw new \ErrorException('Something went wrong! Please report an issue: ' . $ex->getMessage());
        }
    }
}