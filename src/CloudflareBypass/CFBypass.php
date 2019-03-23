<?php
namespace CloudflareBypass;

use \CloudflareBypass\Util\Logger;

/**
 * CF Bypass utility.
 * Bypasses CF (solves JS challenge)
 * @author Kyran Rana
 */
class CFBypass
{
    /**
     * Given page content and headers, will check if page is protected by CF.
     * (This method is NOT accurate and may fail in rare cases) 
     *
     * @access public
     * @param string $content  Response body
     * @param array $http_code  Response http code
     * @return bool 
     */
    public static function isBypassable( $content, $http_code )
    {
        // IUAM page should have a 503 error code.
        if ((int)$http_code !== 503)
            return false;

        /* IUAM page should have the following strings:
         * - jschl_vc, jschl_answer, pass, /cdn-cgi/l/chk_jschl
         */ 
        $required_fields = [ "s", "jschl_vc", "jschl_answer", "pass"];

        foreach ( $required_fields as $field ) {
            if ( strpos( $content, $field ) === false )
                return false;
        }

        return true;
    }


    /**
     * Assembles clearance link.
     * 
     * @access public
     * @param string $uri  URL components
     * @param string $s S code.
     * @param string $jschl_vc  JSCHL VC value
     * @param string $pass  Pass value
     * @param string $jschl_answer  JSCHL answer value
     */
    public static function assemble( $uri, $s, $jschl_vc, $pass, $jschl_answer ) {
        $query = [];
    
        if (isset( $uri['query'] ))
            parse_str( $uri['query'], $query );

        return sprintf("%s://%s/cdn-cgi/l/chk_jschl?%s", 
            $uri['scheme'], 
            $uri['host'],

            // add user params and cf params.
            http_build_query(array_merge( 
            [
                's'                 => $s, 
                'jschl_vc'          => $jschl_vc,
                'pass'              => $pass, 
                'jschl_answer'      => $jschl_answer             
            ], 
            $query )));;
    }


    /**
     * Solves JS challenge on the IUAM page and returns the following fields: 
     * - jschl_vc
     * - pass
     * - jschl_answer.
     *
     * @access public
     * @param string $iuam  CF IUAM page.
     * @param string $url  Request URL
     * @param boolean $verbose_mode  TRUE to enable verbose mode.
     * @throws \ErrorException  if "jschl_vc" and "pass" input values CAN NOT be extracted.
     * @throws \ErrorException  if JS challenge code CAN NOT be extracted
     * @throws \ErrorException  if PHP evaluation of JS challenge code FAILS
     * @return array  jschl_vc, pass, jschl_answer
     */
    public static function bypass( $iuam, $url, $verbose_mode=false )
    {
        // -- 1. Wait for 5 seconds.

        sleep(5);

        // Debug
        if ($verbose_mode)
            Logger::info("CFBypass 1. Waiting for 4 seconds...");
        


        try {

            // -- 2. Extract "s", "jschl_vc" and "pass" input values.

            $s          = self::getInputValue( $iuam, 's' );
            $jschl_vc   = self::getInputValue( $iuam, 'jschl_vc' );
            $pass       = self::getInputValue( $iuam, 'pass' );

            if ($jschl_vc === null || $pass === null) {
                throw new \ErrorException("Unable to fetch \"jschl_vc\" and \"pass\" parameters!");
            }

            // Debug
            if ($verbose_mode) {
                Logger::info("CFBypass 2. Fetching parameters...");
                Logger::info(sprintf( "\t\ts:\t%s", $s ));
                Logger::info(sprintf( "\t\tjschl_vc:\t%s", $jschl_vc ));
                Logger::info(sprintf( "\t\tpass:\t\t%s", $pass ));
            }



            // -- 3. Calculate JS challenge answer.

            $uri = parse_url( $url );

            $jschl_answer = self::getJschlAnswer( $iuam ) + mb_strlen( $uri['host'] );

            // Debug
            if ($verbose_mode) {
                Logger::info("CFBypass 3. Calculating JS challenge answer...");
                Logger::info(sprintf( "\t\tjschl_answer:\t%s", $jschl_answer ));
            }
        
            return array( $s, $jschl_vc, $pass, $jschl_answer );

        } catch( Exception $ex ) {

            // Debug
            if ($verbose_mode)
                Logger::error(sprintf( "CFBypass ERROR: %s", $ex->getMessage() ));

            throw new \ErrorException( $ex );
        }
    }



    // {{{ Getters

    /**
     * Get input value.
     *
     * @access public
     * @param string $iuam  CF IUAM page.
     * @param string name  input name
     * @return string  value.
     */
    public static function getInputValue( $iuam, $name )
    {
        preg_match( '/name="' . $name . '" +value="(.+?)"/', $iuam, $matches );

        return isset( $matches[1] ) ? $matches[1] : null;
    }


    /**
     * Gets jschl answer.
     *
     * @access public
     * @param string $iuam  CF IUAM page.
     * @return float  jschl answer.
     */
    public static function getJschlAnswer( $iuam )
    {
        // -- 1. Extract JavaScript challenge from IUAM page.

        $iuam_jschl = "";
        
        preg_match( '/(?<=s,t,o,p,b,r,e,a,k,i,n,g,f,\s)(\w+)={"(\w+)":(.+?)(?=})/', $iuam, $iuam_jschl_def_matches );

        list( $_, $var1, $var2, $code ) = $iuam_jschl_def_matches;

        preg_match_all( '/' . $var1 . '\.' . $var2 . '[+\-*\/]?=.+?;/', $iuam, $iuam_jschl_matches );

        $iuam_jschl .= "\$jschl_answer=$code;\n";

        foreach ( $iuam_jschl_matches[0] as $jschl_match ) {
            $iuam_jschl .= str_replace( "$var1.$var2", '$jschl_answer', $jschl_match ) . "\n";
        }



        // -- 2. Solve JavaScript challenge.

        $iuam_jschl = str_replace( ']+[]', '].""', $iuam_jschl );
        $iuam_jschl = str_replace( array( '![]', '+[]' ), 0, $iuam_jschl );

        while( preg_match_all( '/\([^()]+\)/', $iuam_jschl, $iuam_jschl_eq_matches ) ) {
            
            foreach ( $iuam_jschl_eq_matches[0] as $eq_match ) {
                if ( strpos( $eq_match, '.""' ) !== false ) {
                    
                    $eq_answer = '"' . implode( "", array_map( function($match){
                        
                        // Calculate equation and return as string.
                        return eval('return ' . str_replace( array( '(', ')' ), "", $match ) . ';');
                        
                    }, array_filter( explode( '.""', $eq_match ), function($elem){
                        
                        // Remove empty strings resulting from split.
                        return trim(str_replace( array( '(', ')' ), "", $elem )) !== "";
                    
                    } ) ) ) . '"';
                    
                    $iuam_jschl = str_replace( $eq_match, $eq_answer, $iuam_jschl );
                    
                } else {
                    
                    if (strpos( $eq_match, '"' ) !== false) {
                        $eq_answer = implode( '.', array_map( function($match){
                            
                            return strpos( $match, '"' ) !== false ? $match : '"' . $match . '"';
                            
                        }, explode( '+', $eq_match ) ) );                
                    } else {
                        $eq_answer = $eq_match;
                    }

                    // Calculate equation.
                    $eq_answer  = eval('return ' . str_replace( array( '(', ')' ), "", $eq_answer ) . ';');
                    
                    $iuam_jschl = str_replace( $eq_match, $eq_answer, $iuam_jschl );
            
                }
            }
        }



        // -- 3. Get JavaScript answer.

        eval( $iuam_jschl );

        return round( $jschl_answer, 10 );
    }

    // }}}
}
