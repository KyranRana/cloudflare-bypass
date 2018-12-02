<?php
namespace CloudflareBypass;

use \CloudflareBypass\Util\Logger;
use \CloudflareBypass\Util\StringFormatter;

class CFBypasser {

    public static function exec( $cf_request_method, $type, $config )
    {
        // -- 1. Request the original page and confirm its protected.

        /* If       caching is enabled
         * Then     Add the original cookies to request method
         */
        if (isset( $config['cache'] ) && $config['cache']) {
            $info           = $cf_request_method->getPageInfo();
            $components     = parse_url( $info['url'] );

            if (($cached = $config['cache']->fetch( $components['host'] )) !== false) {

                // Debug
                if ($config['verbose_mode'])
                    Logger::info(sprintf( "Found clearance cookies in cache for site: %s", $info['url'] ));
            
                foreach ($cached as $cookieString) {
                    $cf_request_method->setCookie( $cookieString );
                }
            }
        }

        // Enable cookie engine.
        $cf_request_method->setCookie("");
        
        // Request original page.
        $response           = $cf_request_method->getPage();
        $response_info      = $cf_request_method->getPageInfo();

        /* If       original page is not protected by CloudFlare
         * Then     return original page
         */
        if (!CFBypass::isBypassable( $response, $response_info )) {
            if ($type === 'CFStreamContext')
                return stream_context_create( $response_info['options'] );
            else
                return $response;
        }

        // Debug
        if ($config['verbose_mode'])
            Logger::info(sprintf( "%s 1. handle checked and validated as protected.", $type ));



        // -- 2. Setup a copy of the handle and set the required options.

        // Copy handle.
        $cf_request_method_copy = $cf_request_method->getCopyOfResource();

        // Set required options for new handle:
        $cf_request_method_copy->setVerboseMode( $config['verbose_mode'] );
        $cf_request_method_copy->showRequestHeaders();

        // Debug
        if ($config['verbose_mode'])
            Logger::info(sprintf( "%s 2. handle copied and required options set.", $type ));



        // -- 3. Attempt to bypass the CloudFlare IUAM page.

        $first_attempt      = true;     // TRUE if its the first time trying to bypass CF.
        $try_counter        = 0;        // How many times the bypass process has been tried.

        while ( $try_counter < $config['max_retries'] ) {

            // -- 3.1. Request UAM page again with new handle.

            $uam_response           = $cf_request_method_copy->getPage();
            $uam_response_info      = $cf_request_method_copy->getPageInfo();

            /* If       original page is not protected by CloudFlare
             * Then     return original page
             */
            if (!CFBypass::isBypassable( $uam_response, $uam_response_info )) {
                if ($type === 'CFStreamContext')
                    return stream_context_create( $uam_response_info['options'] );
                else
                    return $uam_response;
            }

            // Debug
            if ($config['verbose_mode'])
                Logger::info(sprintf( "%s 3.1. (try: %s  first_attempt: %s) uam page requested with new cURL handle.\n\tcontent (base64):\n%s\n\n\tcontext (base64):\n%s\n", $type, $try_counter, $first_attempt, StringFormatter::formatContent( base64_encode($uam_response), "\t", 88 ), StringFormatter::formatContent( base64_encode(json_encode($uam_response_info)), "\t", 88 )));



            // -- 3.2. Validate handle has user agent and "__cfduid" cookie set. (only on first attempt)

            /* If       its the first attempt at trying to bypass CF.
             * And      handle has NO user agent set.
             * Then     throw exception.
             */
            if ($first_attempt && !$cf_request_method_copy->getRequestHeader('User-Agent')) {

                // Debug
                if ($config['verbose_mode'])
                    Logger::error(sprintf( "%s 3.2. You need to set a user agent!", $type ));

                throw new \ErrorException('You need to set a user agent!');
            }

            /* If       handle has NO cookie named "__cfduid".
             * Then     throw exception.
             */
            if ($first_attempt && !($cfduid_cookie = $cf_request_method_copy->getCookie('__cfduid'))) {

                // Debug
                if ($config['verbose_mode'])
                    Logger::error(sprintf( "%s 3.2 The cookie named \"__cfduid\" does not exist!", $type ));

                throw new \ErrorException('The cookie named "__cfduid" does not exist!');
            }

            // Debug
            if ($config['verbose_mode']) {
                Logger::info(sprintf( "%s 3.2. (try: %s  first_attempt: %s) __cfduid cookie is: %s", $type, $try_counter, $first_attempt, $cfduid_cookie ));
            }



            // -- 3.3. Solve the JS challenge on the CF IUAM page.

            // Solve JS challange.
            list( $jschl_vc, $pass, $jschl_answer ) = CFBypass::bypass( $uam_response, $uam_response_info['url'], $config['verbose_mode'] );

            // Get clearance link.
            $uri    = parse_url( $uam_response_info['url'] );
            $query  = [];

            if (isset( $uri['query'] ))
                parse_str( $uri['query'], $query );

            $params = array( 
                'jschl_vc'      => $jschl_vc,
                'pass'          => $pass, 
                'jschl_answer'  => $jschl_answer 
            );

            $clearance_link = sprintf("%s://%s/cdn-cgi/l/chk_jschl?%s", 
                $uri['scheme'], 
                $uri['host'], 
                http_build_query(array_merge( $params, $query )));

            if ($config['verbose_mode']) {
                Logger::info(sprintf( "%s 3.3. Requesting clearance link: %s", $type, $clearance_link ));
            }

            // Access clearance link.
            $cf_request_method_copy->setUrl( $clearance_link );
            $cf_request_method_copy->setFollowLocation( true );
            $cf_request_method_copy->setRequestMethod( "GET" );
            $cf_request_method_copy->getPage();

            // Check if we are successful in bypassing cloudflare.
            if ($cfclearance_cookie = $cf_request_method_copy->getCookie('cf_clearance')) {

                // Debug
                if ($config['verbose_mode']) {
                    Logger::info(sprintf( "%s 3.3. (try: %s  first_attempt: %s) cf_clearance cookie is: %s", $type, $try_counter, $first_attempt, $cfclearance_cookie ));
                }

                break;
            }

            // set request url back to uam page.
            $cf_request_method_copy->setUrl( $uam_response_info['url'] );

            $first_attempt = false;

            $try_counter++;
        }

        /* If       number of times we have tried to bypass CF has exceeded the maximum number of retries.
         * Then     throw exception.
         */
        if ($try_counter === $config['max_retries']) {

            // Debug
            if ($config['verbose_mode'])
                Logger::error(sprintf( "%s 3.3 Exceeded maximum number of retries at getting cookie \"cf_clearance\".", $type ));

            throw new Exception('Exceeded maximum number of retries at getting cookie "cf_clearance".');
        }



        // 4. Apply clearance cookies to original handle.

        $cookies = [];  // For caching cookies (if enabled).

        // Debug
        if ($config['verbose_mode'])
            Logger::info(sprintf( "%s 4. Setting cookies on original handle:", $type ));

        foreach ( $cf_request_method_copy->getCookies() as $cookieString ) {
            $cf_request_method->setCookie( $cookieString );

            // Debug
            if ($config['verbose_mode'])
                Logger::info(sprintf( "\t\t%s", $cookieString ));

            $cookies[] = $cookieString;
        }



        // Store new clearance cookies in cache if caching is enabled.
        if (isset( $config['cache'] ) && $config['cache']) {
            $components = parse_url( $uam_response_info['url'] );

            // Debug
            if ($config['verbose_mode'])
                Logger::info(sprintf( "%s 4. Stored clearance cookies in cache for site: %s", $type, $uam_response_info['url'] ));

            $config['cache']->store( $components['host'], $cookies );
        }

        // 5. Request actual website.
        if ($type === 'CFStreamContext') {
            $success_info = $cf_request_method->getPageInfo();

            Logger::info(sprintf( "%s 5. Returned context.\n\tcontext (base64):\n%s\n", $type, StringFormatter::formatContent( base64_encode(json_encode($success_info)), "\t", 88 ) ));

            return stream_context_create( $success_info['options'] );
        }
    
        $success        = $cf_request_method->getPage();
        $success_info   = $cf_request_method->getPageInfo();

        // Debug
        if ($config['verbose_mode'])
            Logger::info(sprintf( "%s 5. Requested original page using new cookies.\n\tcontent (base64):\n%s\n\n\tcontext (base64):\n%s\n", $type, StringFormatter::formatContent( base64_encode($success), "\t", 88 ), StringFormatter::formatContent( base64_encode(json_encode($success_info)), "\t", 88 )));


        return $success;
    }
}