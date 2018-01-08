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
     * Bypass cloudflare using a cURL handle
     *
     * Given a curl handle this method will behave like "curl_exec" however it will take 
     * care of the cloudflare UAM page if it pops up. This method creates a copy of the
     * cURL handle passed through and assigns necessary options to that.
     *
     * Dependencies:
     * - CURLOPT_USERAGENT needs to be set!
     *
     * @access public
     * @param $curl_handle_orig
     * @param $attempt
     * @param $ua_check
     *
     * @return mixed
     */
    public function curlExec($curl_handle_orig, $attempt = 1, $root = true)
    {
        if($root)
        {
            /*
             * Request original page and see if it is protected by Cloudflare
             */
            $uam_page    = curl_exec($curl_handle_orig);
            $uam_headers = curl_getinfo($curl_handle_orig);

            if (!$this->_isProtected($uam_page, $uam_headers)) return $page;
       
            /*
             * Clone cURL handle and assign necessary options to copy so we do not change the original!
             */
            $curl_handle = curl_copy_handle($curl_handle_orig);
            
            curl_setopt_array($curl_handle, array(
                CURLINFO_HEADER_OUT     => true,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_HEADERFUNCTION  => array($this, '_getCurlHeaders')
            ));
        }
        else 
            /*
             * Not in root scope so $curl_handle_orig is a copy; set it as $curl_handle
             */
            $curl_handle = $curl_handle_orig;

        
        $uam_page    = curl_exec($curl_handle);
        $uam_headers = curl_getinfo($curl_handle);

        /*
         * 1. Check if user agent is set in cURL handle
         */
        if ($root && !$this->_getCurlCookie($uam_headers['request_header'], 'User-Agent'))
            throw new \ErrorException('curlExec -> CURLOPT_USERAGENT is a mandatory field!');
        
        /*
         * 2. Extract "__cfuid" cookie
         */
        if (!($cfduid_cookie = $this->_getCurlCookie($uam_headers['request_header'], '__cfuid'))) return $uam_page;
        curl_setopt($curl_handle, CURLOPT_COOKIELIST, $cfduid_cookie);
        
        /*
         * 3. Solve challenge and request clearance link
         */
        $this->curl_cookies = array();
        curl_setopt($curl_handle, CURLOPT_URL, $this->_getClearanceLink($uam_page, $uam_headers['url']));        
        $clearance_page     = curl_exec($curl_handle);
        $clearance_headers  = curl_getinfo($curl_handle);
        
        /*
         * 4. Extract "cf_clearance" cookie
         */
        if (!($cfclearance_cookie = $this->_getCurlCookie($clearance_headers['request_header'], 'cf_clearance'))) {
            // Make sure we have not tried too many times...
            if ($attempts > $this->max_attempts) throw new \ErrorException("curlExec -> Too many attempts to get CF clearance!");   
            
            // Repeat CF process but skip root-scope checks
            list($cfuid_cookie, $cfclearance_cookie) = $this->curlExec($curl_handle, $attempts + 1, false);
        }

        if ($cfclearance_cookie && !$root) 
            return array($cfduid_cookie, $cfclearance_cookie);
        
        curl_setopt($curl_handle, CURLOPT_COOKIELIST, $cfclearance_cookie);
        
        /*
         * 5. Request url again if follow location is not set
         */
        curl_setopt($curl_handle, CURLOPT_URL, $uam_headers['url']);
        if ($clearance_headers['http_code'] === 302) $clearance_page = curl_exec($curl_handle);
        
        /*
         * 6. Set "__cfduid" and "cf_clearance" in original cURL handle
         */
        curl_setopt($curl_handle_orig, CURLOPT_COOKIELIST, $cfduid_cookie);
        curl_setopt($curl_handle_orig, CURLOPT_COOKIELIST, $cfclearance_cookie);
        $this->curl_cookies = array();
        
        return curl_exec($curl_handle); 
    }
    
    /**
     * cURL header function
     *
     * @access private
     * @param $curl_handle
     * @param $header
     *
     * @return int
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
            if (isset($matches[1])) $cookie = "Set-Cookie: $cookie=" . $matches[1];
        }
        return $cookie;
    }

    // }}}

    /**
     * Check for UAM page 
     *
     * Given page contents and headers, will confirm if page is protected by CloudFlare
     * (to my best of judgment; not perfect).
     *
     * @access private
     * @param $content
     * @param $headers
     *
     * @return bool 
     */
    private function _isProtected($content, $headers)
    {
        /*
         * 1. Cloudflare UAM page always throw a 503
         */
        if ($headers['http_code'] !== 503) return false;

        /*
         * 2. Cloudflare UAM page contains the following strings:
         * "jschl_vc", "pass", "jschl_answer", "/cdn-cgi/l/chk_jschl"
         */
        if (!(
            strpos($content, "jschl_vc")                !== false &&
            strpos($content, "pass")                    !== false &&
            strpos($content, "jschl_answer")            !== false &&
            strpos($content, "/cdn-cgi/l/chk_jschl")    !== false
        ))
            return false;

        return true;
    }

    
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
     * @return string
     */
    private function _getClearanceLink($content, $url)
    {
        /*
         * 1. Mimic waiting process
         */
        sleep(4);
        
        /*
         * 2. Extract "jschl_vc" and "pass" params
         */
        preg_match_all('/name="\w+" value="(.+?)"/', $content, $matches);
        if (!isset($matches[1]) || !isset($matches[1][1]))
            throw new \ErrorException('Unable to fetch jschl_vc and pass values; maybe not protected?');
        
        $params = array();
        list($params['jschl_vc'], $params['pass']) = $matches[1];
        
        /*
         * 3. Extract JavaScript challenge logic
         */
        preg_match_all('/:[!\[\]+()]+|[-*+\/]?=[!\[\]+()]+/', $content, $matches);
        if (!isset($matches[0]) || !isset($matches[0][0]))
            throw new \ErrorException('Unable to find javascript challenge logic; maybe not protected?');
        
        try {
            /*
             * 4. Convert challenge logic to PHP
             */
            $php_code = "";
            foreach ($matches[0] as $js_code) {
                // [] causes "invalid operator" errors; convert to integer equivalents
                $js_code = str_replace(array(")+(",  "![]", "!+[]",  "[]"), array(").(", "(!1)", "(!0)", "(0)"), $js_code);
                $php_code .= '$params[\'jschl_answer\']' . ($js_code[0] == ':' ? '=' . substr($js_code, 1) : $js_code) . ';';
            }
            
            /*
             * 5. Eval PHP and get solution
             */
            eval($php_code);
            $uri = parse_url($url);
            $params['jschl_answer'] += strlen($uri['host']);
            
            /*
             * 6. Construct clearance link
             */
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
