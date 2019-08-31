<?php

namespace CloudflareBypass\Model\UAM;

/**
 * Class UAMPageAttributes
 *      - Contains UAM page attributes
 *
 * @package CloudflareBypass\Model
 * @author Kyran Rana
 */
class UAMPageAttributes
{
    /**
     * UAM page protocol
     *
     * @var string UAM cloudflare page protocol
     */
    private $protocol;

    /**
     * UAM page host
     *
     * @var string UAM cloudflare page host
     */
    private $host;

    /**
     * UAM page
     *
     * @var string UAM cloudflare page
     */
    private $page;

    public function __construct(string $protocol, string $host, string $page)
    {
        $this->protocol     = $protocol;
        $this->host         = $host;
        $this->page         = $page;
    }

    /**
     * Returns UAM page protocol
     *
     * @return string UAM cloudflare page protocol
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * Returns UAM page host
     *
     * @return string UAM page host
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Returns UAM page
     *
     * @return string UAM page
     */
    public function getPage(): string
    {
        return $this->page;
    }
}