<?php

namespace CloudflareBypass;

/**
 * Class CaptchaPageImpl
 *      - Implementation of CaptchaPage
 *
 * @package CloudflareBypass
 * @author Kyran Rana
 */
class CaptchaPageImpl extends CaptchaPage
{
    public function getClearanceUrl(string $logPrefix): void
    {
        throw new \ErrorException(sprintf("%s [Captcha] ERROR. captcha page found. No implementation exists for this yet!\r\n", $logPrefix));
    }
}