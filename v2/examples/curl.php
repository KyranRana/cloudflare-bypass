<?php
require __DIR__ . '/../vendor/autoload.php';

use CloudflareBypass\RequestMethod\CFCurl;

$curl_cf_wrapper = new CFCurl(array(
    'max_retries'   => 5,                   // How many times to try and get clearance?
    'cache'         => true,                // Enable caching?
    'cache_path'    => __DIR__ . '/cache',  // Where to cache cookies? (Default: system tmp directory)
    'verbose'       => false                // Enable verbose? (Good for debugging issues - doesn't effect cURL handle)
));

// Get Example: 1
$ch = curl_init("http://thebot.net/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36');
echo $curl_cf_wrapper->exec($ch); // Done! NOTE: HEAD requests not supported!
curl_close($ch);