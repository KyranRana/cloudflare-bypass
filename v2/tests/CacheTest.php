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
     * @expectedException CloudflareBypass\Exceptions\CFCoreConfigMissingCachePathException
     */
    public function testCFCoreConfigMissingCachePathException()
    {
        new CFCore(array(
            'cache' => true, 
        ));
    }

    /**
     * Test with CFCurl
     *
     * @return void
     */
    public function test()
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
}
