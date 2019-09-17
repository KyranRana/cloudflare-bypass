<?php

namespace CloudflareBypass;

/**
 * Interface Logger
 * @package CloudflareBypass
 * @author Kyran Rana
 */
interface Logger
{
    /**
     * Logs a general message.
     *
     * @param string $message General message.
     */
    public function info(string $message);

    /**
     * Logs an error.
     *
     * @param string $message Error message.
     */
    public function error(string $message);
}