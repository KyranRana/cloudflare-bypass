<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAMOptions;

/**
 * Interface CFCurl
 *      - Used for executing cURL requests.
 *
 * @package CloudflareBypass
 */
interface CFCurl
{
    /**
     * Executes cURL request and handles UAM page if it appears.
     * NOTE: This method will enable cookie engine on the cURL handle by setting CURLOPT_COOKIELIST to an empty string.
     *
     * Prerequisites:
     *      CURLOPT_HEADER_OUT is true.
     *      CURLOPT_VERBOSE is false
     *
     * @param mixed $curlHandle cURL handle
     * @param UAMOptions $uamOptions UAM options
     * @param int $retry Current retry (INTERNAL)
     * @param bool $keepHandle TRUE to keep original cURL handle (INTERNAL).
     * @return string cURL response body
     * @throws \ErrorException if JS evaluation fails
     * @throws \ErrorException if captcha page is shown
     * @throws \ErrorException if correct headers are not set.
     * @throws \ErrorException if clearance cookie is not found.
     */
    public function exec($curlHandle, UAMOptions $uamOptions, int $retry = 0, bool $keepHandle = false): string;

    /**
     * Handles UAM page if it appears.
     *
     * @param mixed $curlHandle cURL handle
     * @param UAMOptions $uamOptions UAM options
     * @param string $page Page contents
     * @param array $info Page info
     * @param int $retry Current retry (INTERNAL)
     * @param bool $keepHandle TRUE to keep original cURL handle (INTERNAL).
     * @return string cURL response body
     * @throws \ErrorException if JS evaluation fails
     * @throws \ErrorException if captcha page is shown
     * @throws \ErrorException if correct headers are not set.
     * @throws \ErrorException if clearance cookie is not found.
     */
    public function bypassIUAMPage($curlHandle, string $page, array $info, int $retry, UAMOptions $uamOptions, bool $keepHandle): string;
}