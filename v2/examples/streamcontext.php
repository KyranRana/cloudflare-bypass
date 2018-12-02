<?php
require __DIR__ . '/../vendor/autoload.php';

use CloudflareBypass\RequestMethod\CFStream;

$stream_cf_wrapper = new CFStream(array(
    'max_retries'   => 5,                       // How many times to try and get clearance?
    'cache'         => false,                   // Enable caching?
    'cache_path'    => __DIR__ . '/cache',      // Where to cache cookies? (Default: system tmp directory)
    'verbose'       => true                     // Enable verbose? (Good for debugging issues - doesn't effect context)
));

// Get Example: 1
$opts = array(
    'http' => array(
        'method' => "GET",
        'header' => array(
            'accept: */*',       // required
            'host: predb.me',    // required
            'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.110 Safari/537.36'
        )
    )
);

$url = "https://predb.me/?search=720p";
$ctx = $stream_cf_wrapper->contextCreate( $url, stream_context_create( $opts ) );

echo file_get_contents( $url, false, $ctx );