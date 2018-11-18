<?php
namespace CloudflareBypass\RequestMethod;

class CFStreamContext extends \CloudflareBypass\CFCore
{
    /**
     * Given a URL and context (stream / array) if URL is protected by Cloudflare,
     * this method will add the "__cfduid" and "cf_clearance" cookies to the "Cookie" header 
     * (or update them if they exist).
     *
     * @access public
     * @param string $url Request URL
     * @param mixed $context Stream / array of context options
     * @param resource $stream Stream context; used in retry process (DONT MODIFY)
     * @param bool $root_scope Used in retry process (DON'T MODIFY)
     * @param integer $retry   Used in retry process (DON'T MODIFY)
     * @throws \ErrorException if $url is not a valid URL
     * @throws \ErrorException if $context is not a valid context
     * @return resource $context
     */
    public function create($url, $context, $stream = null, $root_scope = true, $retry = 1)
    {
        $config = [ 
            'max_retries'   => $this->max_retries,
            'cache'         => isset($this->config['cache']) ? $this->config['cache'] : true
        ];

        if (isset($this->config['cache_path'])) {
            $config['cache_path'] = $this->config['cache_path'];
        }

        if (isset($this->config['verbose'])) {
            $config['verbose'] = $this->config['verbose'];
        }

        $stream_cf_wrapper = new CFStream($config);
        $stream = $stream_cf_wrapper->create($url, $context, $stream, $root_scope, $retry);

        return $stream->getContext();
    }
}
