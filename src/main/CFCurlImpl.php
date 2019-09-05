<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAM\UAMPageAttributes;
use CloudflareBypass\Model\UAM\UAMPageFormParams;
use CloudflareBypass\Model\UAMOptions;

/**
 * Class CFCurlImpl
 *      - Implementation of CFCurl
 *
 * @package CloudflareBypass
 */
class CFCurlImpl implements CFCurl
{
    /**
     * Default Headers
     *
     * @var array
     */
    const DEFAULT_HEADERS =
        [
            "accept"            => "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3",
            "accept-language"   => "Accept-Language: en-US,en;q=0.9",
        ];

    /**
     * SSL Ciphers
     * Credits to: https://github.com/Anorov/cloudflare-scrape
     *
     * @var array
     */
    const DEFAULT_CIPHERS =
        [
            "ECDHE+AESGCM",
            "ECDHE+CHACHA20",
            "DHE+AESGCM",
            "DHE+CHACHA20",
            "ECDH+AESGCM",
            "DH+AESGCM",
            "ECDH+AES",
            "DH+AES",
            "RSA+AESGCM",
            "RSA+AES",
            "!aNULL",
            "!eNULL",
            "!MD5",
            "!DSS",
            "!ECDHE+SHA",
            "!AES128-SHA",
            "!DHE"
        ];

    /**
     * UAM page
     *
     * @var UAMPageImpl
     */
    private $uamPage;

    /**
     * Captcha page
     *
     * @var CaptchaPageImpl
     */
    private $captchaPage;


    public function __construct()
    {
        $this->uamPage          = new UAMPageImpl();
        $this->captchaPage      = new CaptchaPageImpl();
    }

    public function exec($curlHandle, UAMOptions $uamOptions, bool $keepHandle = false, string $logPrefix = "--> ",
                         array $httpHeaders = []): string
    {
        if (!$keepHandle) {
            curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
            curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);
        }

        $page   = curl_exec($curlHandle);
        $info   = curl_getinfo($curlHandle);

        if ($httpHeaders === []) {
            $url  = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);
            $host = parse_url($url, PHP_URL_HOST);

            // re-order http headers on original cURL handle.
            $httpHeaders = $this->getHttpHeaders($this->getCurlHeadersAsMap($info['request_header']), $host);
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $httpHeaders);
        }

        if (UAMPage::isUAMPageForCurl($page, $info)) {
            $page = $this->requestForClearanceFromIUAM($curlHandle, $uamOptions, $keepHandle, $logPrefix, $httpHeaders);
        }

        if (CaptchaPage::isCaptchaPageForCurl($page, $info)) {
            $page = $this->requestForClearanceFromCaptcha($logPrefix);
        }

        return $page;
    }

    /**
     * Request for clearance using cURL
     *
     * Steps:
     *  1. setup clone cURL handle with correct headers.
     *  2. request UAM page
     *  3. solve JS challenge as well as timing operation.
     *  4. wait until 5 seconds is up.
     *  5. request for clearance
     *  6. attach clearance cookies to original cURL handle
     *
     * @param resource $curlHandle cURL handle
     * @param UAMOptions $uamOptions UAM options
     * @param bool $keepHandle Keep using same handle (INTERNAL USE)
     * @param string $logPrefix Verbose log prefix (INTERNAL USE)
     * @param array $httpHeaders Array of HTTP headers (INTERNAL USE)
     * @return string Clearance page response
     * @throws \ErrorException if captcha page is shown
     */
    private function requestForClearanceFromIUAM($curlHandle, UAMOptions $uamOptions, bool $keepHandle, string $logPrefix,
                                                 array $httpHeaders): string
    {
        $verbose = $uamOptions->isVerbose();

        // 1. setup clone cURL handle with correct headers

        $cloneCurlHandle = $keepHandle ? $curlHandle : curl_copy_handle($curlHandle);

        $info       = curl_getinfo($curlHandle);
        $scheme     = parse_url($info['url'], PHP_URL_SCHEME);

        curl_setopt($cloneCurlHandle, CURLINFO_HEADER_OUT, false);
        curl_setopt($cloneCurlHandle, CURLOPT_AUTOREFERER, true);
        curl_setopt($cloneCurlHandle, CURLOPT_RETURNTRANSFER, true);

        // 1.1 remove problematic ciphers which cause captcha page

        if ($scheme === "https") {
            curl_setopt($cloneCurlHandle, CURLOPT_SSL_CIPHER_LIST, implode(":", self::DEFAULT_CIPHERS));

            if (strpos($info['request_header'], "HTTP/2") !== false) {
                curl_setopt($cloneCurlHandle, CURLOPT_HTTPHEADER, array_merge($httpHeaders,
                    [
                        "sec-fetch-mode: navigate",
                        "sec-fetch-site: none",
                        "sec-fetch-user: ?1"
                    ]));
            }
        }

        curl_setopt($cloneCurlHandle, CURLOPT_COOKIELIST, "");

        if ($verbose) {
            curl_setopt($cloneCurlHandle, CURLOPT_VERBOSE, $verbose);

            if (!$keepHandle) {
                printf("%s [UAM] 1. Set up copy of existing cURL handle with correct settings\r\n", $logPrefix);
            } else {
                printf("%s [UAM] 1. Using existing cURL handle\r\n", $logPrefix);
            }
        }

        // 2. request uam page

        $uamPage    = curl_exec($cloneCurlHandle);
        $uamInfo    = curl_getinfo($cloneCurlHandle);

        if ($verbose) {
            printf("%s [UAM] 2. Requested UAM page:\r\n", $logPrefix);
            printf("%s [UAM] 2. UAM Page\t\t\t-> %s\r\n", $logPrefix, base64_encode($uamPage));
            printf("%s [UAM] 2. UAM Info\t\t\t-> %s\r\n", $logPrefix, base64_encode(json_encode($uamInfo)));
        }

        // 3. solve JS challenge as well as timing operation

        $time = microtime(true) * 1000000;

        $scheme     = parse_url($uamInfo['url'], PHP_URL_SCHEME);
        $host       = parse_url($uamInfo['url'], PHP_URL_HOST);

        $pageAttributes     = new UAMPageAttributes($scheme, $host, $uamPage);
        $pageFormParams     = UAMPageFormParams::getParamsFromPage($pageAttributes);

        if ($verbose) {
            printf("%s [UAM] 3. Solved JS challenge\r\n", $logPrefix);
            printf("%s [UAM] 3. S\t\t\t\t\t-> %s\r\n", $logPrefix, $pageFormParams->getS());
            printf("%s [UAM] 3. JSCHL_VC\t\t\t-> %s\r\n", $logPrefix, $pageFormParams->getJschlVc());
            printf("%s [UAM] 3. PASS\t\t\t\t-> %s\r\n", $logPrefix, $pageFormParams->getPass());
            printf("%s [UAM] 3. JSCHL_ANSWER\t\t-> %s\r\n", $logPrefix, $pageFormParams->getJschlAnswer());
        }

        // 4. wait until 5 seconds is up

        usleep(($uamOptions->getDelay() * 1000000) - ((microtime(true) * 1000000) - $time));

        if ($verbose) {
            printf("%s [UAM] 4. five seconds are up!\r\n", $logPrefix);
        }

        // 5. request for clearance

        curl_setopt($cloneCurlHandle, CURLOPT_URL, $this->uamPage->getClearanceUrl($pageAttributes, $pageFormParams));
        curl_setopt($cloneCurlHandle, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($cloneCurlHandle, CURLOPT_CUSTOMREQUEST, "GET");

        $clearancePage = $this->exec($cloneCurlHandle, $uamOptions, true, $logPrefix . " --> ", $httpHeaders);
        $clearanceInfo = curl_getinfo($cloneCurlHandle);

        if ($verbose) {
            printf("%s [UAM] 5. Requested clearance page\r\n", $logPrefix);
            printf("%s [UAM] 5. Clearance page\t-> %s\r\n", $logPrefix, base64_encode($clearancePage));
            printf("%s [UAM] 5. Clearance info\t-> %s\r\n", $logPrefix, base64_encode(json_encode($clearanceInfo)));
        }

        if (!$keepHandle) {

            // 6. attach ordered http headers to original curl handle

            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $httpHeaders);

            // 6. attach clearance cookies to original curl handle

            $clearanceCookies = curl_getinfo($cloneCurlHandle, CURLINFO_COOKIELIST);

            foreach ($clearanceCookies as $clearanceCookie) {
                curl_setopt($curlHandle, CURLOPT_COOKIELIST, $clearanceCookie);

                if ($verbose) {
                    printf("%s [UAM] 6. Set clearance cookie on original cURL handle: %s\r\n", $logPrefix, $clearanceCookie);
                }
            }

            curl_close($cloneCurlHandle);

            if ($verbose) {
                printf("%s [UAM] 6. UAM page bypassed! :)\r\n", $logPrefix);
            }
        }

        return $keepHandle ? $clearancePage : $this->exec($curlHandle, $uamOptions, $keepHandle, $logPrefix . " --> ", $httpHeaders);
    }

    /**
     * Request for clearance from captcha page.
     * - Not implemented yet.
     *
     * @param string $logPrefix Log prefix.
     * @throws \ErrorException
     */
    private function requestForClearanceFromCaptcha(string $logPrefix)
    {
        $this->captchaPage->getClearanceUrl($logPrefix);
    }

    /**
     * Get HTTP headers to send with cURL
     *
     * @param array $requestHeaderMap cURL request header map
     * @param string $host cURL request host
     * @return array Request headers to send with cURL
     */
    private function getHttpHeaders(array $requestHeaderMap, string $host)
    {
        $requestHeaders = [];
        $requestHeaders[] = sprintf("Host: %s", $host);
        $requestHeaders[] = "Connection: keep-alive";
        $requestHeaders[] = "Upgrade-Insecure-Requests: 1";
        $requestHeaders[] = sprintf("User-Agent: %s", $requestHeaderMap['user-agent']);
        $requestHeaders[] = sprintf("Accept: %s", $requestHeaderMap["accept"] ?? self::DEFAULT_HEADERS['accept']);
        $requestHeaders[] = sprintf("Accept-Language: %s", $requestHeaderMap["accept-language"] ?? self::DEFAULT_HEADERS['accept-language']);

        // remove general request headers from map

        unset(
            $requestHeaderMap["host"],
            $requestHeaderMap["connection"],
            $requestHeaderMap["upgrade-insecure-requests"],
            $requestHeaderMap["user-agent"],
            $requestHeaderMap["accept"],
            $requestHeaderMap["accept-language"],
            $requestHeaderMap["accept-encoding"]
        );

        foreach ($requestHeaderMap as $header => $value) {
            $requestHeaders[] = sprintf("%s: %s", $header, $value);
        }

        return $requestHeaders;
    }

    /**
     * Parses cURL request headers into a map.
     *
     * @param string $requestHeaders cURL request headers.
     * @return array cURL request headers as associative array
     */
    private function getCurlHeadersAsMap(string $requestHeaders)
    {
        $requestHeaders     = explode(PHP_EOL, $requestHeaders);
        $requestHeaderMap   = [];

        foreach ($requestHeaders as $requestHeader) {
            if (strpos($requestHeader, ":") !== false) {
                list($name, $value) = explode(":", $requestHeader);
                $requestHeaderMap[strtolower($name)] = trim($value);
            }
        }

        return $requestHeaderMap;
    }
}