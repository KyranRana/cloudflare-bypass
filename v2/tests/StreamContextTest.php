<?php 

namespace CloudflareBypass\Tests;
 
use CloudflareBypass\RequestMethod\CFStreamContext;

class StreamContextTest extends TestCase
{
    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {

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
            $opts = $this->getOptions();

            @file_get_contents($url, false, $wrapper->create($url, $opts));

            $status_code = $this->getStatusCodeFromResponseHeader($http_response_header[0]);


            $this->assertEquals($url.": 200", $url.": ". $status_code);
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

            // parse url into components.
            $url_components = parse_url($url);

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../var/cache/' . md5($url_components['host']);

            $opts = $this->getOptions();

            @file_get_contents($url, false, $wrapper->create($url, $opts));
            $status_code = $this->getStatusCodeFromResponseHeader($http_response_header[0]);

            $this->assertEquals($url.": 200", $url.": ". $status_code);
            $this->assertEquals(false, file_exists($cache_file));
        }
    }


    /**
     * Convert "HTTP/1.1 200" OK to "200"
     *
     * @param string $header
     * @return string
     */
    public function getStatusCodeFromResponseHeader($header)
    {
        preg_match_all('#HTTP/\d+\.\d+ (\d+)#', $header, $results);

        return $results[1][0];
    }
}
