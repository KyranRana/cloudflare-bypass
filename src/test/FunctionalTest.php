<?php

use CloudflareBypass\CFCurlImpl;
use CloudflareBypass\Model\UAMOptions;
use PHPUnit\Framework\TestCase;

class FunctionalTest extends TestCase
{
    public function testMedium()
    {
        $url = "http://medium.com";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Accept-Language: *',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0',
                'Accept: */*',
                'Upgrade-Insecure-Requests: 1'
            ));

        $cfCurl = new CFCurlImpl();

        $cfOptions = new UAMOptions();
        $this->assertContains('Medium – Get smarter about what matters to you', $cfCurl->exec($ch, $cfOptions));
    }

    public function testTorrentz()
    {
        $url = "https://torrentz2.eu/";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            array(
                'Accept-Language: *',
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:54.0) Gecko/20100101 Firefox/54.0',
                'Accept: */*',
                'Upgrade-Insecure-Requests: 1'
            ));

        $cfCurl = new CFCurlImpl();

        $cfOptions = new UAMOptions();
        $this->assertContains('Torrentz2 Search Engine', $cfCurl->exec($ch, $cfOptions));
    }
}
