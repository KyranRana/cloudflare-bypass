<?php

namespace CloudflareBypass\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    /**
     * Urls to test
     *
     * @var string
     */
    protected $urls = [
        "https://thebot.net/",
        "http://predb.me/?search=test"
    ];
    
	/**
 	 * Set up.
 	 */
    public function setUp()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__.'/..', '.env');
        $dotenv->load();
    }
    
    /**
     * Get a dummy agent.
     *
     * @return string
     */
    public function getAgent()
    {
    	return "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";
    }

    /**
     * Get a proxy server.
     *
     * @return string
     */
    public function getProxyServer()
    {
    	return getenv('PROXY_SERVER', null);
    }

    /**
     * Retrieve options from Stream
     *
     * @return array
     */
    public function getOptions()
    {

        $opts = array(
            'http' => array(
                'method'         => "GET",
                'header'         => "User-Agent:".$this->getAgent(),
                'followlocation' => true,
                'request_fulluri'=> true,
                'proxy'          => $this->getProxyServer() ? 'tcp://' . $this->getProxyServer() : null,
            ),
            'curl' => $this->getCurlOptions(),
        );

        return $opts;
    }

    /** 
     * Retrieve curl options
     *
     * @return array
     */
    public function getCurlOptions()
    {
        return array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_PROXY => $this->getProxyServer()
        );
    }
}
