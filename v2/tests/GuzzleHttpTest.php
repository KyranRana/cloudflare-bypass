<?php

namespace CloudflareBypass\Tests;

use CloudflareBypass\RequestMethod\CFStream;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class GuzzleHttpTest extends TestCase
{

    /**
     * Urls to test
     *
     * @var string
     */
    protected $urls = [
        "https://thebot.net/",
        "http://dll.anime47.com/",
        "https://predb.me/?search=test",
        "https://torrentz2.eu/"
    ];

    /**
     * Retrieve the client
     *
     * @return Client
     */
    public function getClient()
    {
        $client = new Client([
            'headers' => array(
                'User-Agent' => $this->getAgent()
            ),
            'curl' => array(
                CURLOPT_PROXY => $this->getProxyServer()
            ),
            'http_errors' => false,
            'debug' => true
        ]);

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
            $response = $client->request('GET', $url, [
            ]);

            $this->assertEquals($url.": "."503", $url.": ".$response->getStatusCode());
        }
    }

    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200()
    {
        // Initialize CFStream.
        $stream_cf_wrapper = new CFStream(array(
            'cache'         => true,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => true
        ));

        $opts = array(
            'http' => array(
                'method'         => "GET",
                'header'         => "User-Agent:".$this->getAgent(),
                'followlocation' => true
            ),
            'curl' => array(
                CURLOPT_PROXY => $this->getProxyServer()
            ),
        );

        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Parse url into components.
            $url_components = parse_url($url);

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../var/cache/' . md5($url_components['host']);
            file_exists($cache_file) && unlink($cache_file);
            
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
}
