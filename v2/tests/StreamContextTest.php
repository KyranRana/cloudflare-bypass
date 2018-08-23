<?php 

namespace CloudflareBypass\Tests;
 
use CloudflareBypass\RequestMethod\CFStreamContext;

class StreamContextTest extends TestCase
{
    /**
     * Urls to test
     *
     * @var string
     */
    protected $urls = [
        "https://thebot.net/",
        "http://dll.anime47.com/",
        "http://predb.me/?search=test"
    ];
    
    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {
        // Make sure each site is protected by CF.
        $opts = array(
            'http' => array(
                'method'            => "GET",
                'request_fulluri'   => true,
                'header'            =>
                    "User-Agent: " . $this->getAgent() . "\r\n"
            )
        );

        // Set proxy server if one is available.
        $proxy = $this->getProxyServer();
        if ($proxy != 'null')
            $opts['proxy'] = $proxy;

        foreach ($this->urls as $url) {

            @file_get_contents($url);
            
            $this->assertEquals($http_response_header[0], "HTTP/1.1 503 Service Temporarily Unavailable");
        }
    }

    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200WithCache()
    {
        // Initialize CFStreamContext wrapper.
        $wrapper = new CFStreamContext(array(
            'cache'         => true,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => false
        ));

        foreach ($this->urls as $url) {

            // Parse url into components.
            $url_components = parse_url($url);

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../var/cache/' . md5($url_components['host']);
            file_exists($cache_file) && unlink($cache_file);

            // Bypass each site using CFStreamContext wrapper.
            $opts = array(
                'http' => array(
                    'method'            => "GET",
                    'request_fulluri'   => true,
                    'header'            =>
                        "User-Agent:" . $this->getAgent() . "\r\n"
                )
            );

            // Set proxy server if one is available.
            $proxy = $this->getProxyServer();
            if ($proxy != 'null')
                $opts['proxy'] = $proxy;

            @file_get_contents($url, false, $wrapper->create($url, $opts));

            $this->assertEquals($url.": HTTP/1.1 200 OK", $url.": ". $http_response_header[0]);
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
        $wrapper = new CFStreamContext(array(
            'cache'         => false,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => false
        ));

        foreach ($this->urls as $url) {

            // Bypass each site using CFStreamContext wrapper.
            $opts = array(
                'http' => array(
                    'method'            => "GET",
                    'request_fulluri'   => true,
                    'header'            =>
                        "User-Agent:" . $this->getAgent() . "\r\n"
                )
            );

            // parse url into components.
            $url_components = parse_url($url);

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../var/cache/' . md5($url_components['host']);

            // Set proxy server if one is available.
            $proxy = $this->getProxyServer();
            if ($proxy != 'null')
                $opts['proxy'] = $proxy;

            @file_get_contents($url, false, $wrapper->create($url, $opts));

            $this->assertEquals($url.": HTTP/1.1 200 OK", $url.": ".$http_response_header[0]);
            $this->assertEquals(false, file_exists($cache_file));
        }
    }
}
