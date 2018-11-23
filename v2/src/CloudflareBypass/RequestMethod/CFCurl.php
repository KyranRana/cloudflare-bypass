<?php
namespace CloudflareBypass\RequestMethod;

class CFCurl extends \CloudflareBypass\CFCore
{
    /**
     * Bypasses cloudflare using a curl handle. Given a curl handle this method will behave 
     * like "curl_exec" however it will take care of the IUAM page if it pops up. 
     * 
     * Requirements:
     * - cURL handle will need to have a user agent set.
     *
     * @access public
     * @param resource $ch  cURL handle
     * @throws \ErrorException  if "CURLOPT_USERAGENT" IS NOT set
     * @throws \ErrorException  if retry process FAILS more than 4 times consecutively
     * @return string  Response body
     */
    public function exec( $ch )
    {
        // -- 1. Request the original page and confirm its protected.

        $ch = new Curl($ch);

        // Add the original cookies to cURL if they exist in cache.
        if (isset( $this->cache ) && $this->cache) {
            $info           = $ch->getinfo();
            $components     = parse_url( $info['url'] );

            $this->debug( sprintf( "Found clearance cookie in cache for site: %s", $info['url'] ) );
            
            if (($cached = $this->cache->fetch( $components['host'] )) !== false) {
                foreach ($cached as $cookieString) {
                    $ch->setopt( CURLOPT_COOKIELIST, $cookieString );
                }
            }
        }
        
        // Request original page.
        $response           = $ch->exec();
        $response_info      = $ch->getinfo();

        /* If       original page is not protected by CloudFlare
         * Then     return original page
         */
        if (!$this->isProtected( $response, $response_info ))
            return $response;

        // Debug
        $this->debug("CFCurl 1. cURL handle checked and validated as protected.");



        // -- 2. Setup a copy of the cURL handle and set the required options.

        // Copy cURL handle.
        $ch_copy = $ch->getCopyOfResource();

        /* Set required options for new cURL handle:
         *
         * Toggle-able:
         *      CURLOPT_VERBOSE             Turns on verbose mode if $this->verbose is TRUE.
         *                                  - Shows debug information for each request made during the bypass.
         *
         * Always set:
         *      CURLOPT_HEADERFUNCTION      To allow us to retrieve response headers and cookies.
         *      CURLINFO_HEADER_OUT         To allow us to retrieve request headers.
         */
        $ch_copy->setopt( CURLOPT_VERBOSE, $this->verbose );
        $ch_copy->setopt( CURLINFO_HEADER_OUT, true );
        $ch_copy->setopt( CURLOPT_HEADERFUNCTION, [
            $ch_copy, 
            'setResponseHeader' 
        ] );

        // Debug
        $this->debug("CFCurl 2. cURL handle copied and required options set.");



        // -- 3. Attempt to bypass the CloudFlare IUAM page.

        $first_attempt      = true;     // TRUE if its the first time trying to bypass CF.
        $try_counter        = 0;        // How many times the bypass process has been tried.

        while ( $try_counter < $this->max_retries ) {

            // -- 3.1. Request UAM page again with new cURL handle.

            $uam_response           = $ch_copy->exec();
            $uam_response_info      = $ch_copy->getinfo();

            // Debug
            $this->debug( sprintf( "CFCurl 3.1. (try %s  first_attempt: %s) uam page requested with new cURL handle", $try_counter, $first_attempt ) );



            // -- 3.2. Validate cURL handle has user agent and "__cfduid" cookie set. (only on first attempt)

            /* If       its the first attempt at trying to bypass CF.
             * And      cURL handle has NO user agent set.
             * Then     throw exception.
             */
            if ($first_attempt && !$ch_copy->getRequestHeader('User-Agent'))
                throw new \ErrorException('CFCurl -> You need to set a user agent!');

            /* If       cURL handle has NO cookie named "__cfduid".
             * Then     throw exception.
             */
            if ($first_attempt && !($cfduid_cookie = $ch_copy->getCookie('__cfduid')))
                throw new \ErrorException('CFCurl -> The cookie named "__cfduid" does not exist!');

            // Debug
            $this->debug( sprintf( "CFCurl 3.2. (try: %s  first_attempt: %s) __cfduid cookie is %s", $try_counter, $first_attempt, $cfduid_cookie ) );



            // -- 3.3. Solve the JS challenge on the CF IUAM page.

            // Get clearance link.
            $clearance_link = $this->getClearanceLink( $uam_response, $uam_response_info['url'] );

            // Access clearance link.
            $ch_copy->setopt( CURLOPT_URL, $clearance_link );
            $ch_copy->setopt( CURLOPT_FOLLOWLOCATION, true );
            $ch_copy->setopt( CURLOPT_CUSTOMREQUEST, 'GET' );
            $ch_copy->setopt( CURLOPT_HTTPGET, true );
            $ch_copy->exec();

            // Check if we are successful in bypassing cloudflare.
            if ($cfclearance_cookie = $ch_copy->getCookie('cf_clearance')) {

                // Debug
                $this->debug( sprintf( "CFCurl 3.3. (try: %s  first_attempt: %s) cf_clearance cookie is %s", $try_counter, $first_attempt, $cfclearance_cookie ) );

                break;
            }


            $first_attempt = false;

            $try_counter++;
        }

        /* If       number of times we have tried to bypass CF has exceeded the maximum number of retries.
         * Then     throw exception.
         */
        if ($try_counter === $this->max_retries)
            throw new Exception('CFCurl -> Exceeded maximum number of retries at getting cookie "cf_clearance".');



        // 4. Apply clearance cookies to original cURL handle.

        $cookies = [];  // For caching cookies (if enabled).

        foreach ( $ch_copy->getInfo( CURLINFO_COOKIELIST ) as $cookie ) {
            $ch->setopt( CURLOPT_COOKIELIST, $cookie );

            $cookies[] = $cookie;
        }

        // Store new clearance tokens in cache if caching is enabled.
        if (isset( $this->cache ) && $this->cache) {
            $components = parse_url( $uam_response_info['url'] );

            $this->cache->store( $components['host'], $cookies );
        }
    
        // Request actual website.
        $success            = $ch->exec();
        $success_info       = $ch->getinfo();

        return $success;
    }
}