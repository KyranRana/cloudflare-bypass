<?php
require "../../../vendor/autoload.php";

use CloudflareBypass\CFCurlImpl;
use CloudflareBypass\Model\UAMOptions;

/*
 * Prerequisites
 *
 * Set the following request headers:
 *
 * - Upgrade-Insecure-Requests
 * - User-Agent
 * - Accept
 * - Accept-Language
 *
 * Set the following options:
 *
 * - CURLINFO_HEADER_OUT    true
 * - CURLOPT_VERBOSE        false
 *
 */
$url = "http://noreferrer.cc";
$ch = curl_init($url);

// Want to cache clearance cookies ?
//curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
//curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLINFO_HEADER_OUT, true);
curl_setopt($ch, CURLOPT_HTTPHEADER,
    array(
        "Upgrade-Insecure-Requests: 1",
        "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3",
        "Accept-Language: en-US,en;q=0.9"
    ));

$cfCurl = new CFCurlImpl();

$cfOptions = new UAMOptions();
$cfOptions->setVerbose(true);
// $cfOptions->setDelay(5);

try {
    $page = $cfCurl->exec($ch, $cfOptions);

    // Want to get clearance cookies ?
    //$cookies = curl_getinfo($ch, CURLINFO_COOKIELIST);

} catch (ErrorException $ex) {
    echo "Unknown error -> " . $ex->getMessage();
}
