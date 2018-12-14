<?php
namespace CloudflareBypass\Util;

/**
 * String formatting utility.
 * @author Kyran Rana
 */
class StringFormatter {

    /**
     * Formats string into a rectangular block of text.
     * 
     * @access public
     * @param string $content  The string.
     * @param string $prefix  The prefix per line.
     * @param integer $length  The length per line.
     * @return string  Formatted content.
     */
    public static function formatContent( $content, $prefix, $length ) {
        return implode( "", array_map(  
            function( $elem ) use ( $prefix ) {
                return $prefix . implode( "", $elem ) . "\n";
            }, 
            array_chunk( str_split( $content ), $length ) 
        ) );
    }
}