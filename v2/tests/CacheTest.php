<?php

use PHPUnit\Framework\TestCase;

use CloudflareBypass\CFCore;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
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
     * Test with a cache_path
     *
     * @return void
     */
    public function testWithDirectory()
    {
        foreach(glob(__DIR__."/../var/cache") as $file) {
            is_file($file) && unlink($file); 
        }

        $curl_cf_wrapper = new CFCurl(array(
            'cache'         => true,
            'cache_path'    => __DIR__."/../var/cache",
            'max_attempts'  => 5
        ));
        
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
        $response = $curl_cf_wrapper->exec($ch);

 
        $this->assertEquals(true, count(scandir(__DIR__."/../var/cache")) > 0);
        curl_close($ch);
    }


    /**
     * Test without a cache_path
     *
     * @return void
     */
    public function testWithoutAPath()
    {
        foreach(glob(sys_get_temp_dir()."/cf-bypass") as $file) {
            is_file($file) && unlink($file); 
        }

        $curl_cf_wrapper = new CFCurl(array(
            'cache'         => true,
            'max_attempts'  => 5
        ));
        
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
        $response = $curl_cf_wrapper->exec($ch);

 
        $this->assertEquals(true, count(scandir(sys_get_temp_dir()."/cf-bypass")) > 0);
        curl_close($ch);
    }
}
