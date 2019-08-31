<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAM\UAMPageAttributes;
use CloudflareBypass\Model\UAM\UAMPageFormParams;

/**
 * Interface UAMPage
 *      - Interface used to mark a UAM page.
 *
 * @package CloudflareBypass
 * @author Kyran Rana
 */
abstract class UAMPage
{
    public static function isUAMPageForCurl(string $page, array $info)
    {
        return
            $info['http_code'] === 503
            && strpos($page, "jschl_vc") !== false
            && strpos($page, "jschl_answer") !== false;
    }

    /**
     * Gets clearance url.
     *
     * @param UAMPageAttributes $pageAttributes UAM page attributes
     * @param UAMPageFormParams $formParams UAM page form params
     * @return string Clearance url
     */
    public abstract function getClearanceUrl(UAMPageAttributes $pageAttributes, UAMPageFormParams $formParams): string;
}