<?php
require 'cloudflareBypass.class.php';

$cloudflare_bypass = new CloudflareBypass();
$cloudflare_bypass->setCurlConfig(array(
    'returntransfer_flag' => true
));

if($curl_handle = curl_init("https://anime47.com")) 
{
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');

    echo "Request begin!\n";
    $res = $cloudflare_bypass->curlExec($curl_handle);
    echo "Request ended with: " . curl_getinfo($curl_handle)['http_code'] . "\n";
    
    curl_close($curl_handle);
}