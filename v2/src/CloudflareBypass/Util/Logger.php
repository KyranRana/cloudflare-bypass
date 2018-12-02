<?php
namespace CloudflareBypass\Util;

/**
 * Logging utility.
 * @author Kyran Rana
 */
class Logger {

    /**
     * Outputs log message.
     *
     * @access public
     * @param string $msg  The message.
     */
    public static function info( $msg ) {
        self::log( "INFO", $msg );
    }


    /**
     * Outputs error message.
     * 
     * @access public
     * @param string $msg  The message.
     */
    public static function error( $msg ) {
        self::log( "ERROR", $msg );
    }


    /**
     * Outputs any type of message.
     * Format: [datetime] [type] -> [msg]
     *
     * @access private
     * @param string $type  The type of message.
     * @param string $msg  The message.
     */
    private static function log( $type, $msg ) {
        echo "[" . (new \DateTime())->format("Y-m-d H:i:s") . "][$type] -> $msg\n";
    }
}