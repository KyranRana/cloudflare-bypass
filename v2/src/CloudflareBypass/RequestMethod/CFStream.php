<?php
namespace CloudflareBypass\RequestMethod;

use \CloudflareBypass\CFBypasser;


/**
 * CF stream context.
 * Bypasses CF using stream context.
 * @author Kyran Rana
 */
class CFStream extends \CloudflareBypass\CFCore
{
    /**
     * Given a URL and a context (stream / array), if URL is protected by the Cloudflare,
     * this method will add the "__cfduid" and "cf_clearance" cookies to the "Cookie" header 
     * (or update them if they exist).
     *
     * Requirements:
     * - Stream context should have a user agent set.
     *
     * @access public
     * @param string $url Request URL
     * @param mixed $context Stream / array of context options
     * @throws \ErrorException if $url is not a valid URL
     * @throws \ErrorException if $context if not valid context
     * @return resource $context
     */
    public function contextCreate( $url, $context )
    {
        return CFBypasser::exec( new Stream( $url, $context ), 'CFStreamContext',
            [
                'max_retries'       => $this->max_retries,
                'verbose_mode'      => $this->verbose_mode,
                'cache'             => $this->cache
            ] );
    }
}