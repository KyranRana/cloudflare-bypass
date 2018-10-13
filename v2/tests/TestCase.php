<?php

namespace CloudflareBypass\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;

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
     * Proxy Server address
     *
     * @var string
     */
    protected $proxyServer;

    /**
     * Fylesystem Cache
     *
     * @var \Symfony\Component\Cache\Simple\FilesystemCache
     */
    protected $cache;
    
	/**
 	 * Set up.
 	 */
    public function setUp()
    {
        $this->cache = new FilesystemCache('', 60*60*24);
        $this->iniProxyServer();
    }

    /**
     * Set proxy server
     *
     * @var string $proxyServer
     */
    public function setProxyServer($proxyServer)
    {
        $this->proxyServer = $proxyServer;
    } 

    /**
     * Get a proxy server.
     *
     * @return string
     */
    public function getProxyServer()
    {
        return $this->proxyServer;
    }

    /**
     * Is proxy server not down
     * 
     * @param string $proxyServer
     * 
     * @return bool
     */
    public function isProxyServerWorking(string $proxyServer)
    {
        $timeout = 5;
        $splited = explode(':',$proxyServer);
        
        $con = @fsockopen($splited[0], $splited[1], $errorNumber, $errorMessage, $timeout);

        return $con;
    }

    /**
     * Find new proxy
     *
     * @return string
     */
    public function findNewProxyServer()
    {
        $client = new Client();

        # Attention to the rate limit
        $response = $client->request('GET', 'https://api.getproxylist.com/proxy');

        $body = json_decode((string) $response->getBody());

        return $body->ip.":".$body->port;
    }

    /**
     * Ini proxy server
     */
    public function iniProxyServer()
    {
        $proxyServer = $this->cache->get('proxy_server');

        if (!$proxyServer || !$this->isProxyServerWorking($proxyServer)) {
            $this->detectProxyServer();
        } 
    }

    /**
     * Find a valid proxy
     *
     * @return string
     */
    public function detectProxyServer()
    {
        $tries = 3;
        while ($tries > 0) {
            $proxyServer = $this->findNewProxyServer();

            if ($this->isProxyServerWorking($proxyServer)) {
                $this->cache->set('proxy_server', $proxyServer);
                $this->setProxyServer($proxyServer);

                return;
            }

            $tries--;
        }

        throw new \Exception("Cannot find a valid proxy server");
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
