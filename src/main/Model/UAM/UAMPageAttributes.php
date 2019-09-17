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
     * @var string $protocol UAM cloudflare page protocol
     */
    private $protocol;

    /**
     * UAM page host
     *
     * @var string $host UAM cloudflare page host
     */
    private $host;

    /**
     * UAM page.
     *
     * @var string $page UAM page.
     */
    private $page;

    /**
     * UAM page form params.
     *
     * @var UAMPageFormParams $formParams UAM page form params.
     */
    private $formParams;

    /**
     * Initialises UAM page.
     *
     * @param string $protocol UAM protocol
     * @param string $host UAM host
     * @param string $page UAM page
     * @throws \ErrorException If unable to get answer to UAM challenge
     */
    public function __construct(string $protocol, string $host, string $page)
    {
        $this->protocol     = $protocol;
        $this->host         = $host;
        $this->page         = $page;
        $this->formParams   = UAMPageFormParams::getParamsFromPage($this);
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
     * Returns UAM page.
     *
     * @return string UAM page.
     */
    public function getPage(): string
    {
        return $this->page;
    }

    /**
     * Returns UAM page form params.
     *
     * @return UAMPageFormParams UAM page form params
     */
    public function getFormParams(): UAMPageFormParams
    {
        return $this->formParams;
    }
}