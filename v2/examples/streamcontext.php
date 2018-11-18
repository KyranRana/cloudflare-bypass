<?php
require __DIR__ . '/../vendor/autoload.php';

use CloudflareBypass\RequestMethod\CFStreamContext;

$stream_cf_wrapper = new CFStreamContext(array(
    'max_retries'   => 5,                       // How many times to try and get clearance?
    'cache'         => true,                    // Enable caching?
    'cache_path'    => __DIR__ . '/cache',      // Where to cache cookies? (Default: system tmp directory)
    'verbose'       => false                    // Enable verbose? (Good for debugging issues - doesn't effect context)
));

// Get Example: 1
$opts = array(
    'http' => array(
        'method' => "GET",
        'header' =>
            "User-Agent:Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36\r\n"
    )
);

$url = "https://thebot.net";

echo file_get_contents($url, false, $stream_cf_wrapper->create($url, $opts)); // As easy as that!