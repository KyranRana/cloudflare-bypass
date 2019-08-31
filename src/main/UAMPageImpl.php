<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAM\UAMPageAttributes;
use CloudflareBypass\Model\UAM\UAMPageFormParams;

/**
 * Class UAMPageImpl
 *      - Implementation of UAMPage.
 *
 * @package CloudflareBypass
 */
class UAMPageImpl extends UAMPage
{
    public function getClearanceUrl(UAMPageAttributes $pageAttributes, UAMPageFormParams $challengeParams): string
    {
        return sprintf("%s://%s/cdn-cgi/l/chk_jschl?%s",
            $pageAttributes->getProtocol(),
            $pageAttributes->getHost(),
            $challengeParams->getQueryString());
    }
}