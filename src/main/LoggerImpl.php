<?php

namespace CloudflareBypass;

/**
 * Class LoggerImpl
 *      - Implementation of Logger
 *
 * @package CloudflareBypass
 * @author Kyran Rana
 */
class LoggerImpl implements Logger
{
    /**
     * TRUE to enable logger.
     *
     * @var bool $enable
     */
    private $enabled = false;

    /**
     * TRUE to enable logger.
     *
     * @param bool $enabled TRUE to enable logger.
     */
    public function enable(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    public function info(string $message)
    {
        if ($this->enabled) {
            printf("[INFO] %s\n", $message);
        }
    }

    public function error(string $message)
    {
        if ($this->enabled) {
            printf("[ERROR] %s\n", $message);
        }
    }
}