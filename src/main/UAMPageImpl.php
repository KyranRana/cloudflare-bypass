<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAM\UAMPageAttributes;

/**
 * Class UAMPageImpl
 *      - Implementation of UAMPage.
 *
 * @package CloudflareBypass
 */
class UAMPageImpl extends UAMPage
{
    public function getClearanceUrl(UAMPageAttributes $pageAttributes): string
    {
        return sprintf("%s://%s%s",
            $pageAttributes->getProtocol(),
            $pageAttributes->getHost(),
            $pageAttributes->getFormParams()->getAction());
    }
}