<?php
namespace CloudflareBypass;

use \CloudflareBypass\Util\Logger;
use \CloudflareBypass\Util\StringFormatter;

/**
 * CF Bypass Logic
 * @author Kyran Rana
 */
class CFBypasser {

    public static function exec( $cf_request_method, $type, $config )
    {
        // -- 1. Request the original page and confirm its protected.

        /* If       caching is enabled
         * Then     check if clearance cookies exist in cache for site.
         */
        if (isset( $config['cache'] ) && $config['cache']) {
            $components = parse_url( $cf_request_method->getUrl() );

            /*
             * If       clearance cookies exist for site.
             * Then     assign clearance cookies to request handle.
             */
            if (($cached_cookies = $config['cache']->fetch( $components['host'] )) !== false) {

                // Debug
                if ($config['verbose_mode'])
                    Logger::info(sprintf( "Found clearance cookies in cache for host: %s", $components['host'] ));
            
                foreach ($cached_cookies as $cookie_name => $cookie_value) {
                    $cf_request_method->setCookie( $cookie_name, $cookie_value );
                }
            }
        }
        
        // Request original page.
        $response       = $cf_request_method->getPage();
        $http_code      = $cf_request_method->getHttpCode();

        /* If       original page is not protected by CF
         * Then     return original page / context
         */
        if (!CFBypass::isBypassable( $response, $http_code )) {
            if (method_exists( $cf_request_method, 'getContext' )) {
                $response = $cf_request_method->getContext();
            }

            return $response;
        }

        // Debug
        if ($config['verbose_mode'])
            Logger::info(sprintf( "%s 1. handle checked and validated as protected.", $type ));



        // -- 2. Setup a copy of the handle and set the required options (aka CF handle).

        // Get CF handle.
        $cf_request_method_copy = $cf_request_method->getCFResource();

        // Debug
        if ($config['verbose_mode'])
            Logger::info(sprintf( "%s 2. got cloudflare handle.", $type ));


        try {
            // -- 3. Attempt to bypass the CF UAM page.

            $first_attempt      = true;     // TRUE if its the first time trying to bypass CF.
            $try_counter        = 0;        // How many times the bypass process has been tried.

            while ( $try_counter < $config['max_retries'] ) {

                // -- 3.1. Request UAM page again with new handle.

                $uam_response   = $cf_request_method_copy->getPage();
                $uam_http_code  = $cf_request_method_copy->getHttpCode();
                $uam_url        = $cf_request_method_copy->getUrl();

                /* If       original page is not protected by CF.
                 * Then     return original page / context.
                 */
                if (!CFBypass::isBypassable( $uam_response, $uam_http_code )) {
                    if (method_exists( $cf_request_method, 'getContext' )) {
                        $uam_response = $cf_request_method_copy->getContext();
                    }

                    // close copy of cURL handle
                    $cf_request_method_copy->close();

                    return $uam_response;
                }

                // Debug
                if ($config['verbose_mode']) {
                    Logger::info(sprintf(
                        "%s 3.1. (try: %s  first_attempt: %s) uam page requested with new cURL handle.\n"
                        . "\trequest headers:\n\t%s\n"
                        . "\tresponse headers:\n\t%s\n", 
                        
                        $type, $try_counter, $first_attempt, implode( "\n\t", $cf_request_method_copy->getRequestHeaders() ), implode( "\n\t", $cf_request_method_copy->getResponseHeaders() ) ));
                }



                // -- 3.2. Validate handle has user agent and "__cfduid" cookie set. (only on first attempt)

                /* If       its the first attempt at trying to bypass CF.
                 * And      handle has NO user agent set.
                 * Then     throw exception.
                 */
                if ($first_attempt && !$cf_request_method_copy->getRequestHeader('User-Agent')) {
                    throw new \ErrorException('You need to set a user agent!');
                }

                /* If       handle has NO cookie named "__cfduid".
                 * Then     throw exception.
                 */
                if (!($cfduid_cookie = $cf_request_method_copy->getCookie('__cfduid'))) {
                    throw new \ErrorException('The cookie named "__cfduid" does not exist!');
                }

                // Debug
                if ($config['verbose_mode']) {
                    Logger::info(sprintf( "%s 3.2. (try: %s  first_attempt: %s) __cfduid cookie is: %s", $type, $try_counter, $first_attempt, $cfduid_cookie ));
                }



                // -- 3.3. Solve the JS challenge on the CF UAM page.

                // Solve JS challange.
                list( $s, $jschl_vc, $pass, $jschl_answer ) = CFBypass::bypass( $uam_response, $uam_url, $config['verbose_mode'] );

                // Get clearance link.
                $clearance_link = CFBypass::assemble( parse_url( $uam_url ), $s, $jschl_vc, $pass, $jschl_answer );

                // Access clearance link.
                $cf_request_method_copy->setUrl( $clearance_link );
                $cf_request_method_copy->setFollowLocation( true );
                $cf_request_method_copy->setRequestMethod( "GET" );
                $cf_request_method_copy->getPage();

                // Debug
                if ($config['verbose_mode']) {
                    Logger::info(sprintf( 
                        "%s 3.3. Requesting clearance link: %s\n"
                        . "\trequest headers:\n\t%s\n"
                        . "\tresponse headers:\n\t%s\n",

                        $type, $clearance_link, implode( "\n\t", $cf_request_method_copy->getRequestHeaders() ), implode( "\n\t", $cf_request_method_copy->getResponseHeaders() ) ));
                }

                // Check if we are successful in bypassing cloudflare.
                if ($cfclearance_cookie = $cf_request_method_copy->getCookie('cf_clearance')) {

                    // Debug
                    if ($config['verbose_mode']) {
                        Logger::info(sprintf( "%s 3.3. (try: %s  first_attempt: %s) cf_clearance cookie is: %s", $type, $try_counter, $first_attempt, $cfclearance_cookie ));
                    }

                    break;
                }

                // Set request url back to uam page.
                $cf_request_method_copy->setUrl( $uam_url );

                // No longer the first attempt.
                $first_attempt = false;

                $try_counter++;
            }

            /* If       number of times we have tried to bypass CF has exceeded the maximum number of retries.
             * Then     throw exception.
             */
            if ($try_counter === $config['max_retries']) {
                throw new \ErrorException('Exceeded maximum number of retries at getting cookie "cf_clearance".');
            }



            // 4. Apply clearance cookies to original handle.

            $cookies = [];  // For caching cookies (if enabled).

            // Debug
            if ($config['verbose_mode'])
                Logger::info(sprintf( "%s 4. Setting cookies on original handle:", $type ));

            foreach ( $cf_request_method_copy->getCookies() as $cookie_name => $cookie_value ) {
                $cf_request_method->setCookie( $cookie_name, $cookie_value );

                // Debug
                if ($config['verbose_mode'])
                    Logger::info(sprintf( "\t\t%s", $cookie_value ));

                $cookies[$cookie_name] = $cookie_value;
            }
        }
        finally {
            // close copy of cURL handle
            $cf_request_method_copy->close();
        }

        /* If       caching is enabled
         * Then     store clearance cookies in cache for site     
         */
        if (isset( $config['cache'] ) && $config['cache']) {
            $components = parse_url( $cf_request_method->getUrl() );

            // Debug
            if ($config['verbose_mode'])
                Logger::info(sprintf( "%s 4. Stored clearance cookies in cache for site: %s", $type, $uam_url ));

            $config['cache']->store( $components['host'], $cookies );
        }



        // 5. Request actual website.

        if (method_exists( $cf_request_method, 'getContext' )) {
            
            // Debug
            if (isset($config['verbose_mode']) && $config['verbose_mode']) {
                Logger::info(sprintf( "%s 5. Returned context", $type ));
            }

            return $cf_request_method->getContext();
        }

        $success = $cf_request_method->getPage();
        
        // Debug
        if ($config['verbose_mode']) {
            Logger::info(sprintf(
                "%s 5. Requested original page using clearance cookies.\n"
                . "\trequest headers:\n\t%s\n"
                . "\tresponse headers:\n\t%s\n",

                $type, implode( "\n\t", $cf_request_method->getRequestHeaders() ), implode( "\n\t", $cf_request_method->getResponseHeaders() ) ));
        }

        return $success;
    }
}
