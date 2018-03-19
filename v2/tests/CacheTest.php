<?php

use PHPUnit\Framework\TestCase;

use CloudflareBypass\RequestMethod\CFCurl;

class CacheTest extends TestCase
{
    
    /**
     * Url to test
     *
     * @var string
     */
    protected $url = "https://coinkite.com";

    /**
     * Make a new request
     *
     * @param CFCurl $cf
     */
    public function makeRequest(CFCurl $cf)
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');

        $cf->exec($ch);

        return $ch;
    }

    /**
     * Test with a cache_path
     *
     * @return void
     */
    public function testWithCachePath()
    {
        $url_components = parse_url($this->url);

        $cache_file = __DIR__ . '/../var/cache/' . md5($url_components['host']);
        file_exists($cache_file) && unlink($cache_file);

        $ch = $this->makeRequest(new CFCurl(array(
            'cache'         => true,
            'cache_path'    => __DIR__."/../var/cache",
        )));

        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $this->assertEquals(true, file_exists($cache_file));
        $this->assertEquals(true, isset(json_decode(file_get_contents($cache_file))->cf_clearance));

        unlink($cache_file);
        
        curl_close($ch);
    }


    /**
     * Test without a cache_path
     *
     * @return void
     */
    public function testWithoutCachePath()
    {
        $url_components = parse_url($this->url);

        $cache_file = sys_get_temp_dir() . "/cf-bypass/" . md5($url_components['host']);
        file_exists($cache_file) && unlink($cache_file);
        
        $ch = $this->makeRequest(new CFCurl(array(
            'cache'         => true,
        )));

        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $this->assertEquals(true, file_exists($cache_file));
        $this->assertEquals(true, isset(json_decode(file_get_contents($cache_file))->cf_clearance));

        unlink($cache_file);
        
        curl_close($ch);
    }

    /**
     * Test no cache 
     *
     * @return void
     */
    public function testNoCache()
    {
        $url_components = parse_url($this->url);

        $cache_file = sys_get_temp_dir() . "/cf-bypass/" . md5($url_components['host']);
        file_exists($cache_file) && unlink($cache_file);

        $ch = $this->makeRequest(new CFCurl(array(
            'cache' => false
        )));

        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        $this->assertEquals(false, file_exists($cache_file));

        curl_close($ch);
    }
}
