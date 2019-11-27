<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAM\UAMPageAttributes;
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
     * SSL ciphers to use when establishing SSL connection.
     * NOTE: Removes problematic ciphers which cause the captcha page to show up.
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
     * Headers required to bypass CloudFlare.
     *
     * @var array
     */
    const DEFAULT_HEADERS =
        [
            "Upgrade-Insecure-Requests",
            "User-Agent",
            "Accept",
            "Accept-Language"
        ];


    /**
     * TRUE if clearance is found.
     *
     * @var bool
     */
    private $clearance;

    /**
     * UAM page
     *
     * @var UAMPageImpl
     */
    private $uamPage;

    /**
     * Logger
     *
     * @var LoggerImpl
     */
    private $logger;

    public function __construct()
    {
        $this->uamPage = new UAMPageImpl();
        $this->logger  = new LoggerImpl();
    }

    public function exec($curlHandle, UAMOptions $uamOptions, int $retry = 0, bool $keepHandle = false): string
    {
        if (!$keepHandle) {
            curl_setopt($curlHandle, CURLOPT_COOKIELIST, "");
        }

        $page = curl_exec($curlHandle);
        $info = curl_getinfo($curlHandle);

        if (UAMPage::isUAMPage($info['http_code'], $page)) {
            $this->clearance = false;
            $cloneCurlHandle = $keepHandle ? $curlHandle : $this->cloneCurlHandle($curlHandle);
            $this->bypassIUAMPage($cloneCurlHandle, $page, $info, $retry, $uamOptions, $keepHandle);

            if (!$keepHandle) {
                $this->copyCookies($cloneCurlHandle, $curlHandle);
                $page = curl_exec($curlHandle);
            }
        }

        if ($info['http_code'] === 403 && strpos($page, "captcha")) {
            $this->logger->error(sprintf("Captcha (retry: %s) -> not supported!", $retry));
            throw new \ErrorException("Captcha page is not supported!");
        }

        return $page;
    }

    public function bypassIUAMPage($curlHandle, string $page, array $info, int $retry, UAMOptions $uamOptions, bool $keepHandle): string
    {
        if (!$keepHandle) {
            $this->logger->enable($uamOptions->isVerbose());

            if (!$this->checkForCorrectHeaders($info['request_header'])) {
                throw new \ErrorException("cURL handle does not contain correct headers (user-agent, accept, accept-language, upgrade-insecure-requests)");
            }
        }

        $scheme = parse_url($info['url'], PHP_URL_SCHEME);
        $host   = parse_url($info['url'], PHP_URL_HOST);
        $time   = microtime(true) * 1000000;

        if ($scheme === "https") {
            $this->logger->info(sprintf("UAM (retry: %s) -> applying HTTPS settings", $retry));
            curl_setopt($curlHandle, CURLOPT_SSL_CIPHER_LIST, implode(":", self::DEFAULT_CIPHERS));

            if (strpos($info['request_header'], 'HTTP/2') !== false) {
                $headers   = array_filter(array_slice(preg_split("/\r\n|\n/", $info['request_header']), 1));
                $headers[] = "sec-fetch-mode: navigate";
                $headers[] = "sec-fetch-site: none";
                $headers[] = "sec-fetch-user: ?1";

                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
            }

            $page = curl_exec($curlHandle);
        }

        $this->logger->info(sprintf("UAM (retry: %s) -> waiting for %s seconds!", $retry, $uamOptions->getDelay()));
        usleep(max(($uamOptions->getDelay() * 1000000) - ((microtime(true) * 1000000) - $time), 0));
        $this->logger->info(sprintf("UAM (retry: %s) -> getting form params...", $retry));

        $pageAttributes = new UAMPageAttributes($scheme, $host, $page);
        $formParams     = $pageAttributes->getFormParams();

        $this->logger->info(sprintf("UAM (retry: %s) -> (r param: %s)", $retry, $formParams->getR()));
        $this->logger->info(sprintf("UAM (retry: %s) -> (jschl_vc param: %s)", $retry, $formParams->getJschlVc()));
        $this->logger->info(sprintf("UAM (retry: %s) -> (pass param: %s)", $retry, $formParams->getPass()));
        $this->logger->info(sprintf("UAM (retry: %s) -> (jschl_answer param: %s)", $retry, $formParams->getJschlAnswer()));

        curl_setopt($curlHandle, CURLOPT_URL, $this->uamPage->getClearanceUrl($pageAttributes));
        curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $pageAttributes->getFormParams()->getQueryString());

        $clearancePage    = $this->exec($curlHandle, $uamOptions, $retry + 1, true);
        $clearanceCookies = curl_getinfo($curlHandle, CURLINFO_COOKIELIST);

        if (!$this->clearance) {
            foreach ($clearanceCookies as $clearanceCookie) {
                if (strpos($clearanceCookie, "cf_clearance") !== false) {
                    $this->logger->info(sprintf("UAM (retry: %s) -> clearance cookie found!", $retry));
                    $this->clearance = true;
                    break;
                }
            }

            $this->logger->info(print_r($clearanceCookies, true));

            if (!$this->clearance) {
                $this->logger->error(sprintf("UAM (retry: %s) -> clearance cookie missing!", $retry));
                throw new \ErrorException("CF clearance could not be found!");
            }
        }

        return $clearancePage;
    }

    /**
     * Clones cURL handle.
     *
     * @param mixed $curlHandle cURL handle
     * @return mixed new cURL handle
     */
    private function cloneCurlHandle($curlHandle)
    {
        $cloneCurlHandle = curl_copy_handle($curlHandle);
        curl_setopt($cloneCurlHandle, CURLINFO_HEADER_OUT, true);
        $this->copyCookies($curlHandle, $cloneCurlHandle);

        return $cloneCurlHandle;
    }

    /**
     * Checks if cURL handle has correct headers to bypass CloudFlare.
     *
     * @param string $headers Page request headers.
     * @return bool TRUE if cURL handle contains correct headers
     */
    private function checkForCorrectHeaders(string $headers): bool
    {
        foreach (self::DEFAULT_HEADERS as $header) {
            if (stripos($headers, $header.':') === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Copy cookies from one cURL handle to another.
     *
     * @param mixed $curlHandleFrom cURL handle to copy cookies from.
     * @param mixed $curlHandleTo cURL handle to copy cookies to.
     */
    private function copyCookies($curlHandleFrom, $curlHandleTo)
    {
        $cookies = curl_getinfo($curlHandleFrom, CURLINFO_COOKIELIST);

        foreach ($cookies as $cookie) {
            curl_setopt($curlHandleTo, CURLOPT_COOKIELIST, $cookie);
        }
    }
}