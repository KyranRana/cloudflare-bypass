<?php
namespace CloudflareBypass\RequestMethod;

use \CloudflareBypass\CFBypasser;


/**
 * CF cURL.
 * Bypasses CF using cURL.
 * @author Kyran Rana
 */
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
        return CFBypasser::exec( new Curl( $ch ), 'CFCurl',
            [
                'max_retries'       => $this->max_retries,
                'verbose_mode'      => $this->verbose_mode,
                'cache'             => $this->cache
            ] );
    }
}