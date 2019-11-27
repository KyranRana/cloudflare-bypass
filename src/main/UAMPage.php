<?php

namespace CloudflareBypass;

use CloudflareBypass\Model\UAM\UAMPageAttributes;

/**
 * Interface UAMPage
 *      - Interface used to mark a UAM page.
 *
 * @package CloudflareBypass
 * @author Kyran Rana
 */
abstract class UAMPage
{
    /**
     * Checks if page is a UAM page.
     *
     * @param int $httpCode Alleged page http code
     * @param string $page Alleged page
     * @return bool TRUE if page is a UAM page.
     */
    public static function isUAMPage(int $httpCode, string $page): bool {
        return $httpCode === 503
            && strpos($page, '"r"') !== false
            && strpos($page, '"pass"') !== false
            && strpos($page, '"jschl_vc"') !== false
            && strpos($page, '"jschl_answer"') !== false;
    }

    /**
     * Gets clearance url.
     *
     * @param UAMPageAttributes $pageAttributes UAM page attributes
     * @return string Clearance url
     */
    public abstract function getClearanceUrl(UAMPageAttributes $pageAttributes): string;
}