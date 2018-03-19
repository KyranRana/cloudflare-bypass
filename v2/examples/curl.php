<?php
require __DIR__ . '/../vendor/autoload.php';

use CloudflareBypass\RequestMethod\CFCurl;

$curl_cf_wrapper = new CFCurl(array(
    'cache'         => true,   // Caching now enabled by default; stores clearance tokens in Cache folder
    'max_retries'   => 5       // Max attempts to try and get CF clearance
));

// Get Example: 1
$ch = curl_init("http://thebot.net/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36');
echo $curl_cf_wrapper->exec($ch); // Done! NOTE: HEAD requests not supported!
curl_close($ch);