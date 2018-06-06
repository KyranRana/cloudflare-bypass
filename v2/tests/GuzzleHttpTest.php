<?php

use PHPUnit\Framework\TestCase;

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
        "https://coinkite.com/",
        "http://dll.anime47.com/imgur/",
        "https://predb.me/?search=test"
    ];

    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {
        $agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";

        $client = new Client();

        foreach ($this->urls as $url) {
            // Make sure each site is protected by CF.
            $response = $client->request('GET', $url, [
                'headers' => [
                    'User-Agent' => "$agent",
                ],
                'http_errors' => false,
                'debug' => true
            ]);

            $this->assertEquals(503, $response->getStatusCode());
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

        $agent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36";

        $opts = array(
            'http' => array(
                'method'    => "GET",
                'header'    => "User-Agent:$agent"
            )
        );

        $client = new Client();

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
                'headers' => [
                    'User-Agent' => "$agent",
                ],
                'cookies' => $cookie_jar,
                'debug' => true
            ]);

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertEquals(true, file_exists($cache_file));
            $this->assertEquals(true, isset(json_decode(file_get_contents($cache_file))->cf_clearance));

            // Remove the file from cache.
            unlink($cache_file);
        }
    }
}
