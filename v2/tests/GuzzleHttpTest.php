<?php

namespace CloudflareBypass\Tests;

use CloudflareBypass\RequestMethod\CFStream;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class GuzzleHttpTest extends TestCase
{
    /**
     * Retrieve the client
     *
     * @return Client
     */
    public function getClient()
    {   
        $opts = [
            'headers' => array(
                'User-Agent' => $this->getAgent()
            ),
            'verify'        => false,
            'curl' => $this->getCurlOptions(),
            'proxy' => 'tcp://'.$this->getProxyServer(),
            'http_errors'   => false,
            'debug'         => false
        ];

        $client = new Client($opts);

        return $client;
    }

    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {
        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Make sure each site is protected by CF.
            $response = $client->request('GET', $url, []);

            $this->assertEquals($url.": "."503", $url.": ".$response->getStatusCode());
        }
    }

    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200WithCache() 
    {
         // Initialize CFStream.
        $stream_cf_wrapper = new CFStream(array(
            'cache'         => true,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => false
        ));

        $opts = $this->getOptions();
      
        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Parse url into components.
            $url_components = parse_url($url);

            $cache_file = __DIR__ . "/../var/cache/" . md5($url_components['host']);

            // Bypass each site using CFStream wrapper.
            $stream     = $stream_cf_wrapper->create($url, $opts);
            $cookie_jar = CookieJar::fromArray($stream->getCookiesOriginal(), $url_components['host']);

            $response = $client->request('GET', $url, [
                'cookies' => $cookie_jar,
            ]);

            $this->assertEquals($url.": "."200", $url.": ".$response->getStatusCode());
            $this->assertEquals(true, file_exists($cache_file));
            $this->assertEquals(true, isset(json_decode(file_get_contents($cache_file))->cf_clearance));

            // Remove the file from cache.
            unlink($cache_file);
        }
    }


    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200WithNoCache()
    {
        // Initialize CFStream.
        $stream_cf_wrapper = new CFStream(array(
            'cache'         => false,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => false
        ));

        $opts = $this->getOptions();
        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Parse url into components.
            $url_components = parse_url($url);

            // Bypass each site using CFStream wrapper.
            $stream     = $stream_cf_wrapper->create($url, $opts);
            $cookie_jar = CookieJar::fromArray($stream->getCookiesOriginal(), $url_components['host']);

            $response = $client->request('GET', $url, [
                'cookies' => $cookie_jar,
            ]);

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../var/cache/' . md5($url_components['host']);

            $this->assertEquals($url.": "."200", $url.": ".$response->getStatusCode());
            $this->assertEquals(false, file_exists($cache_file));
        }
    }

}
