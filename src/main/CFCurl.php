<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAMOptions;

/**
 * Interface CurlCloudflareWrapper
 *      - Used for executing cURL requests.
 *
 * @package CloudflareBypass
 */
interface CFCurl
{
    /**
     * Executes cURL request.
     *
     * @param mixed $curlHandle cURL handle
     * @param UAMOptions $uamOptions UAM options
     * @param bool $keepHandle Keep using same handle (INTERNAL USE)
     * @param string $logPrefix Verbose log prefix (INTERNAL USE)
     * @param array $httpHeaders Array of HTTP headers (INTERNAL USE)
     * @return string cURL response body
     * @throws \ErrorException if JS evaluation fails
     * @throws \ErrorException if captcha page is shown
     */
    public function exec($curlHandle, UAMOptions $uamOptions, bool $keepHandle = false, string $logPrefix = "--> ",
                         array $httpHeaders = []): string;
}