<?php
namespace CloudflareBypass\RequestMethod;

class CFCurl extends \CloudflareBypass\CFCore
{
    /**
     * Bypasses cloudflare using a curl handle. Given a curl handle this method will behave 
     * like "curl_exec" however it will take care of the IUAM page if it pops up. This method 
     * creates a copy of the curl handle passed through for CF process.
     *
     * @access public
     * @param resource $ch cURL handle
     * @param bool $root_scope Used in retry process (DON'T MODIFY)
     * @param integer $retry   Used in retry process (DON'T MODIFY)
     * @throws \ErrorException if "CURLOPT_USERAGENT" IS NOT set
     * @throws \ErrorException if retry process FAILS more than 4 times consecutively
     * @return string Response body
     */
    public function exec($ch, $root_scope = true, $retry = 1)
    {
        if ($root_scope) {
            $ch = new Curl($ch);
            
            // Check if clearance tokens exists in a cache file. 
            if (isset($this->cache)) {
                $info = $ch->getinfo();
                $components = parse_url($info['url']);

                if (($cached = $this->cache->fetch($components['host'])) !== false) {
                    // Use cached clearance tokens.
                    $ch->setopt(CURLOPT_COOKIELIST, 'Set-Cookie: ' . $cached['__cfduid']);
                    $ch->setopt(CURLOPT_COOKIELIST, 'Set-Cookie: ' . $cached['cf_clearance']);
                }
            }

            // Request original page.
            $response = $ch->exec();
            $response_info = $ch->getinfo();

            // Check if page is protected by Cloudflare.
            if (!$this->isProtected($response, $response_info)) {
                return $response;
            }

            // Clone curl object handle.
            $ch_copy = $ch->copyHandle();

            // Enable response header and cookie storage.
            $ch_copy->enableResponseStorage();

            // Assign neccessary options.
            $ch_copy->setopt(CURLINFO_HEADER_OUT, true);
            $ch_copy->setopt(CURLOPT_RETURNTRANSFER, true);
            $ch_copy->setopt(CURLOPT_CUSTOMREQUEST, 'GET');
        } else {
            // Not in root scope so $ch is a clone.
            $ch_copy = $ch;
        }

        // Request UAM page with necessary settings.
        $uam_response = $ch_copy->exec();
        $uam_response_info = $ch_copy->getinfo();

        /*
         * 1. Check if user agent is set in cURL handle
         */
        if ($root_scope && !$ch_copy->getRequestHeader('User-Agent')) {
            throw new \ErrorException('CURLOPT_USERAGENT is a mandatory field!');
        }

        /*
         * 2. Extract "__cfuid" cookie
         */
        if (!($cfduid_cookie = $ch_copy->getCookie('__cfduid'))) {
            return $response;
        }
        
        $ch_copy->setopt(CURLOPT_COOKIELIST, $cfduid_cookie);
        
        /*
         * 3. Solve challenge and request clearance link
         */
        $ch_copy->setopt(CURLOPT_URL, $this->getClearanceLink($uam_response, $uam_response_info['url']));        
        $ch_copy->setopt(CURLOPT_FOLLOWLOCATION, false);

        // Request clearance page
        $ch_copy->exec();

        /*
         * 4. Extract "cf_clearance" cookie
         */
        if (!($cfclearance_cookie = $ch_copy->getCookie('cf_clearance'))) {
            if ($retry > $this->max_retries) {
                throw new \ErrorException("Exceeded maximum retries trying to get CF clearance!");   
            }
            
            list($cfduid_cookie, $cfclearance_cookie) = $this->exec($ch, false, $retry+1);
        }

        if ($cfclearance_cookie && !$root_scope) {
            return array($cfduid_cookie, $cfclearance_cookie);
        }

        if (isset($this->cache)) {
            $components = parse_url($uam_response_info['url']);

            // Store cookies in cache            
            $this->cache->store($components['host'], array(
                '__cfduid'      => $cfduid_cookie,
                'cf_clearance'  => $cfclearance_cookie
            ));
        }
       
        /*
         * 5. Set "__cfduid" and "cf_clearance" in original cURL handle
         */
        $ch->setopt(CURLOPT_COOKIELIST, 'Set-Cookie: ' . $cfduid_cookie);
        $ch->setopt(CURLOPT_COOKIELIST, 'Set-Cookie: ' . $cfclearance_cookie);
        
        return $ch->exec();
    }
}