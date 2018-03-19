<?php 
 
use PHPUnit\Framework\TestCase;
 
use CloudflareBypass\RequestMethod\CFCurl;

class CurlTest extends TestCase
{
    /**
     * Urls to test
     *
     * @var string
     */
    protected $urls = [
        "https://thebot.net/",
        "https://coinkite.com/",
        "http://dll.anime47.com/imgur/"
    ];
    
    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {
        foreach ($this->urls as $url) {
            // Make sure each site is protected by CF.
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_exec($ch);

            $this->assertEquals(503, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }
    }

    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200()
    {
        // Initialize CFCurl wrapper.
        $curl_cf_wrapper = new CFCurl(array(
            'cache'         => true
            'cache_path'    => __DIR__."/../var/cache",
        ));

        foreach ($this->urls as $url) {
            // Bypass each site using CFCurl wrapper.
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');

            $response = $curl_cf_wrapper->exec($ch);

            // Parse url into components.
            $url_components = parse_url($url);

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../src/CloudflareBypass/Cache/' . md5($url_components['host']);

            $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
            $this->assertEquals(true, file_exists($cache_file));
            $this->assertEquals(true, isset(json_decode(file_get_contents($cache_file))->cf_clearance));

            // Remove the file from cache.
            unlink($cache_file);

            curl_close($ch);
        }
    }
}
