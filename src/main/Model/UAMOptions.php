<?php

namespace CloudflareBypass\Model;

/**
 * Class Options
 *      - Extra options for bypassing.
 *
 * @package CloudflareBypass\Model
 * @author Kyran Rana
 */
class UAMOptions
{
    /**
     * TRUE if verbose mode is enabled
     *
     * @var bool $verbose TRUE if verbose mode is enabled
     */
    private $verbose = false;

    /**
     * Delay to clearance.
     *
     * @var integer $delay Delay to clearance.
     */
    private $delay = 5;

    public function __construct(){}

    /**
     * Returns TRUE if verbose mode is enabled.
     *
     * @return bool TRUE if verbose mode is enabled.
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * Set to TRUE to enable verbose mode.
     *
     * @param bool $verbose TRUE to enable verbose mode.
     */
    public function setVerbose(bool $verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * Gets delay to clearance
     *
     * @return int Delay to clearance
     */
    public function getDelay(): int
    {
        return $this->delay;
    }

    /**
     * Set delay to clearance
     *
     * @param int $delay Delay to clearance.
     */
    public function setDelay(int $delay)
    {
        $this->delay = $delay;
    }
}