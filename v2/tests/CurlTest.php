<?php 
 
use PHPUnit\Framework\TestCase;
 
use CloudflareBypass\RequestMethod\CFCurl;

class CurlTest extends TestCase
{
 
    /**
     * Url to test
     *
     * @var string
     */
    protected $url = "http://thebot.net/";
    
    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
 
        $this->assertEquals(503, curl_getinfo($ch, CURLINFO_HTTP_CODE));
    }

    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200()
    {
        $curl_cf_wrapper = new CFCurl(array(
            'cache'         => true,   // Caching now enabled by default; stores clearance tokens in Cache folder
            'max_attempts'  => 5       // Max attempts to try and get CF clearance
        ));
 
        // Get Example: 1
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
 
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
        $response = $curl_cf_wrapper->exec($ch); // Done! NOTE: HEAD requests not supported!
 
        $this->assertEquals(200, curl_getinfo($ch, CURLINFO_HTTP_CODE));
        curl_close($ch);
    }
}
