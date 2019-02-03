<?php
namespace CloudflareBypass;

use \CloudflareBypass\Util\Logger;

/**
 * CF Core
 * Core logic for all request methods.
 * @author Kyran Rana
 */
class CFCore
{
    /**
     * Maximum retries at bypassing CF.
     * @var integer
     */ 
    protected $max_retries;

    /**
     * Verbose mode.
     * @var bool
     */
    protected $verbose_mode;

    /**
     * Caching mechanism | null
     * @var bool
     */
    protected $cache = null;


    /**
     * Configuration properties:
     *
     * Key                  Sets                                        Definiton
     * -----------------------------------------------------------------------------------------------------------------------------
     * "cache"              $this->cache (to Storage class)             Caches clearance tokens so subsequent requests are quicker.
     * "max_retries"        $this->max_retries (to value given)         Maximum number of retries allowed at bypassing CF.
     * "verbose"            $this->verbose (to value given)             Enables verbose mode for chosen request type.
     *
     * @access public
     * @param array $config  Config containing any of the properties above.
     * @throws \ErrorException  if "max_retries" IS NOT an integer
     */
    public function __construct( $config = array() )
    {
        $config['cache_path'] = isset($config['cache_path']) ? $config['cache_path'] : sys_get_temp_dir()."/cf-bypass";
        
        if (!isset( $config['cache'] ) || $config['cache'])
            $this->cache = new Storage( $config['cache_path'] );

        $this->verbose_mode = isset( $config['verbose'] ) ? $config['verbose'] : false;
        $this->max_retries  = isset( $config['max_retries'] ) ? $config['max_retries'] : 5;

        // Debug
        if ($this->verbose_mode)
            Logger::info("VERBOSE: ON");
    }
}
