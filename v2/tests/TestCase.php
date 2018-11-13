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
        "https://thebot.net/"
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
        $this->cache = new FilesystemCache('', 3600);
        $this->setProxyServer($this->detectProxyServer());

        echo "selected: ".$this->getProxyServer()."\n";
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
        echo "trying $proxyServer...\n";

        $timeout = 1;
        $splited = explode(':',$proxyServer);
        
        $con = @fsockopen($splited[0], $splited[1], $errorNumber, $errorMessage, $timeout);

        if (!$con) {
            return false;
        }


        $client = new Client();

        foreach ($this->urls as $url) {

            try {

                $start = microtime(true);

                $response = $client->request(
                    'GET', 
                    $url, 
                    [
                        'proxy' => 'tcp://'.$proxyServer,
                        'http_errors'   => false,
                    ]
                );

                if ((microtime(true) - $start)/1000 > 0.8) {
                    return false;
                }
            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                return false;
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                return false;
            }

            if ($response->getStatusCode() !== 503) {
                return false;
            }
        }

        return true;
    }

    /**
     * Find proxy servers
     *
     * @return array
     */
    public function findProxyServers()
    {

        $client = new Client();

        $response = $client->request('GET', 'https://raw.githubusercontent.com/clarketm/proxy-list/master/proxy-list.txt');

        $body = (string) $response->getBody();

        preg_match_all("/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\:?([0-9]{1,5})/", $body, $matches);

        return $matches[0];
    }

    /**
     * Find a valid proxy
     *
     * @return string
     */
    public function detectProxyServer()
    {
        // if ($this->cache->has('cf-bypass-proxy-server')) {
            // return $this->cache->get('cf-bypass-proxy-server');
        // }

        $proxyServers = $this->findProxyServers();

        foreach ($proxyServers as $proxyServer) {
            if ($this->isProxyServerWorking($proxyServer)) {

                // $this->cache->set('cf-bypass-proxy-server', $proxyServer);

                return $proxyServer;
            }
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
