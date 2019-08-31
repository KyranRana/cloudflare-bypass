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
    private const DEFAULT_HEADERS =
        [
            "Connection: keep-alive",
            "Upgrade-Insecure-Requests: 1",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3",
            "Accept-Language: en-US,en;q=0.9",
            "Accept-Encoding: gzip, deflate"
        ];

    /**
     * SSL Ciphers
     * Credits to: https://github.com/Anorov/cloudflare-scrape
     *
     * @var array
     */
    private const DEFAULT_CIPHERS =
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


    public function __construct()
    {
        $this->uamPage = new UAMPageImpl();
    }


    public function exec($curlHandle, UAMOptions $uamOptions, bool $keepHandle = false, string $logPrefix = "--> "): string
    {
        $page   = curl_exec($curlHandle);
        $info   = curl_getinfo($curlHandle);

        if (UAMPage::isUAMPageForCurl($page, $info)) {
            $page = $this->requestForClearanceFromIUAM($curlHandle, $uamOptions, $keepHandle, $logPrefix);
        }

        if (CaptchaPage::isCaptchaPageForCurl($page, $info)) {
            // ultimately throws exception
            (new CaptchaPageImpl())->getClearanceUrl($logPrefix);
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
     * @return string Clearance page response
     * @throws \ErrorException if JS evaluation fails
     * @throws \ErrorException if captcha page is shown
     */
    private function requestForClearanceFromIUAM($curlHandle, UAMOptions $uamOptions, bool $keepHandle, string $logPrefix): string
    {
        $verbose = $uamOptions->isVerbose();

        // 1. setup clone cURL handle with correct headers

        $cloneCurlHandle = $keepHandle ? $curlHandle : curl_copy_handle($curlHandle);

        $url = curl_getinfo($curlHandle, CURLINFO_EFFECTIVE_URL);

        // 1.1 remove problematic ciphers which cause captcha page

        $scheme = parse_url($url, PHP_URL_SCHEME);

        curl_setopt($cloneCurlHandle, CURLOPT_AUTOREFERER, true);
        curl_setopt($cloneCurlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cloneCurlHandle, CURLOPT_HTTPHEADER, array_merge(self::DEFAULT_HEADERS, $uamOptions->getExtraHeaders()));


        if ($scheme === "https") {
            curl_setopt($cloneCurlHandle, CURLOPT_SSL_CIPHER_LIST, implode(":", self::DEFAULT_CIPHERS));
            curl_setopt($cloneCurlHandle, CURLOPT_HTTPHEADER, array_merge(self::DEFAULT_HEADERS,
                $uamOptions->getExtraHeaders(),
                [
                    "sec-fetch-mode: navigate",
                    "sec-fetch-site: none",
                    "sec-fetch-user: ?1"
                ]));
        }

        curl_setopt($cloneCurlHandle,CURLOPT_ENCODING , "gzip");
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

        $clearancePage = $this->exec($cloneCurlHandle, $uamOptions, $keepHandle, $logPrefix . " --> ");
        $clearanceInfo = curl_getinfo($cloneCurlHandle);

        if ($verbose) {
            printf("%s [UAM] 5. Requested clearance page\r\n", $logPrefix);
            printf("%s [UAM] 5. Clearance page\t-> %s\r\n", $logPrefix, base64_encode($clearancePage));
            printf("%s [UAM] 5. Clearance info\t-> %s\r\n", $logPrefix, base64_encode(json_encode($clearanceInfo)));
        }

        // 6. attach clearance cookies to original cURL handle

        if (!$keepHandle) {
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

        return $keepHandle ? $clearancePage : curl_exec($curlHandle);
    }
}