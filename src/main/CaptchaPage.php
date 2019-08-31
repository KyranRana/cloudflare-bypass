<?php

namespace CloudflareBypass;

/**
 * Class CaptchaPage
 *      - Represents a captcha page.
 *
 * @package CloudflareBypass
 * @author Kyran Rana
 */
abstract class CaptchaPage
{
    public static function isCaptchaPageForCurl(string $page, array $info)
    {
        return
            $info['http_code'] === 503
            && strpos($page, "captcha") !== false;
    }

    /**
     * Gets clearance url for captcha page.
     * - Throws exception as this is not possible.
     * - Implementation exists as may be implemented later (using a captcha service maybe)
     *
     * @param string $logPrefix Log prefix.
     * @throws \ErrorException
     */
    public abstract function getClearanceUrl(string $logPrefix);
}