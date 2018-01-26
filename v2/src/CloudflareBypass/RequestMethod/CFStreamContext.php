<?php
namespace CloudflareBypass\RequestMethod;

class CFStreamContext extends \CloudflareBypass\CFCore
{
    /**
     * Given a URL and a context (stream / array), if URL is protected by the Cloudflare,
     * this method will add the "__cfduid" and "cf_clearance" cookies to the "Cookie" header 
     * (or update them if they exist).
     *
     * @access public
     * @param string $url Request URL
     * @param mixed $context Stream / array of context options
     * @throws \ErrorException if $url is not a valid URL
     * @throws \ErrorException if $context if not valid context.
     * @return resource $context
     */
    public function create($url, $context, $stream = null, $root_scope = true, $attempt = 1)
    {
        $method = null;
        $follow_location = 0;

        if ($root_scope) {
            // Extract array if context is a resource.
            if (is_resource($context)) {
                $context = stream_context_get_options($context);
            }

            // Store original request method.
            $method = isset($context['http']['method']) ? $context['http']['method'] : 'GET';

            // Store original follow location.
            $follow_location = isset($context['http']['follow_location']) ? $context['http']['follow_location'] : 1;        

            $stream = new StreamContext($url, $context);

            // Check if clearance tokens exists in a cache file.
            if (isset($this->cache)) {
                $components = parse_url($url);

                if (($cached = $this->cache->fetch($components['host'])) !== false) {
                    // Use cached clearance tokens.
                    $stream->setCookie('__cfduid', str_replace('__cfduid=', '', $cached['__cfduid']));
                    $stream->setCookie('cf_clearance', str_replace('cf_clearance=', '', $cached['cf_clearance']));
                }
            }

            // Set to GET request.
            $stream->setHttpContextOption('method', 'GET');
        }

        // Request page.
        $response = $stream->fileGetContents();
        $response_info = array(
            'http_code'     => $stream->getResponseHeader('http_code')
        );

        // Check if page is protected by Cloudflare.
        if (!$this->isProtected($response, $response_info)) {
            // Set original request method.
            $stream->setHttpContextOption('method', $method);

            return stream_context_create($stream->getContext());
        }

        /*
         * 1. Check if user agent is set in context
         */
        if ($root_scope && !$stream->getRequestHeader('User-Agent')) {
            throw new \ErrorException('User agent needs to be set in context!');
        }

        /*
         * 2. Extract "__cfuid" cookie
         */
        if (!($cfduid_cookie = $stream->getCookie('__cfduid'))) {
            // Set original request method.
            $stream->setHttpContextOption('method', $method);

            return stream_context_create($stream->getContext());
        }

        $stream->setCookie('__cfduid', str_replace('__cfduid=', '', $cfduid_cookie));

        /*
         * 3. Solve challenge and request clearance link
         */
        $stream->setURL($this->getClearanceLink($response, $url));
        $stream->setHttpContextOption('follow_location', 0);

        // Request clearance page.
        $stream->fileGetContents();

        /*
         * 4. Extract "cf_clearance" cookie
         */
        if (!($cfclearance_cookie = $stream->getCookie('cf_clearance'))) {
            if ($retry > $this->max_retries) {
                throw new \ErrorException("Exceeded maximum retries trying to get CF clearance!");   
            }
            
            list($cfduid_cookie, $cfclearance_cookie) = $this->create($url, false, $stream, false, $retry+1);
        }

        if ($cfclearance_cookie && !$root_scope) {
            return array($cfduid_cookie, $cfclearance_cookie);
        }

        if (isset($this->cache)) {
            $components = parse_url($url);

            // Store cookies in cache            
            $this->cache->store($components['host'], array(
                '__cfduid'      => $cfduid_cookie,
                'cf_clearance'  => $cfclearance_cookie
            ));
        }

        /*
         * 5. Revert all stream options.
         */

        // Set original URL.
        $stream->setURL($url);

        // Set original follow location.
        $stream->setHttpContextOption('follow_location', $follow_location);

        // Set original request method.
        $stream->setHttpContextOption('method', $method);

        // Set clearance tokens.
        $stream->setCookie('__cfduid', str_replace('__cfduid=', '', $cfduid_cookie));
        $stream->setCookie('cf_clearance', str_replace('cf_clearance=', '', $cfclearance_cookie));

        return stream_context_create($stream->getContext());
    }
}