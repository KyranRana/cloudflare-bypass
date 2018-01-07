<?php
require 'cloudflareBypass.class.php';

$cloudflare_bypass = new CloudflareBypass();

/**
 * cURL example
 */
if($curl_handle = curl_init("https://coinkite.com")) 
{
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);    
    curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');

    $res = $cloudflare_bypass->curlExec($curl_handle);

    curl_close($curl_handle);
}