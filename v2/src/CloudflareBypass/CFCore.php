<?php
namespace CloudflareBypass;

class CFCore extends CFBypass
{
    /**
     * Maximum retries allowed.
     * @var integer
     */ 
    protected $max_retries = 5;

    /**
     * Configuration. 
     * @var array
     */
    protected $config;

    /**
     * Caching mechanism.
     * @var bool
     */
    protected $cache = null;


    /**
     * Configuration properties:
     *
     * Key                  Sets                                        Definiton
     * -----------------------------------------------------------------------------------------------------------------------------
     * "cache"              $this->cache_mech (to Storage class)        Caches clearance tokens so subsequent requests are quicker.
     * "max_retries"        $this->max_retries (to value given)         Maximum number of retries allowed for bypassing CloudFlare.
     * "verbose"            $this->verbose (to value given)             Enables verbose mode for chosen request type.
     *
     * @access public
     * @param array $config  Config containing any of the properties above.
     * @throws \ErrorException  if "max_retries" IS NOT an integer
     */
    public function __construct( $config = array() )
    {
        $cache = isset($config['cache']) ? $config['cache'] : true;
        $cache_path = isset($config['cache_path']) ? $config['cache_path'] : sys_get_temp_dir()."/cf-bypass";

        if ($cache === true) {
            $this->cache = new Storage($cache_path);
        }

        $this->config = $config;
        $this->verbose = isset($config['verbose']) && $config['verbose'];

        if (isset($config['max_retries'])) {
            if (!is_numeric($config['max_retries'])) {
                throw new \ErrorException('"max_retries" should be an integer!');
            }

            $this->max_retries = $config['max_retries'];
        }

        $this->debug("VERBOSE: ON");
    }

    /**
     * Outputs debug message if verbose mode is enabled.
     *
     * @param string $message
     * @return void
     */
    public function debug( $message )
    {
        $this->verbose && print_r("* ".$message."\n");
    }
}