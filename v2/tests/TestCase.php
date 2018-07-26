<?php

namespace CloudflareBypass\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

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
    	return getenv('PROXY_SERVER');
    }
}
