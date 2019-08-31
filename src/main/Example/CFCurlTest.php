<?php
require "../../../vendor/autoload.php";

use CloudflareBypass\CFCurlImpl;
use CloudflareBypass\Model\UAMOptions;

$ch = curl_init("http://predb.me/?search=720p");

// Want to cache clearance cookies ?
// curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");
// curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.100 Safari/537.36");

$cfCurl = new CFCurlImpl();

$cfOptions = new UAMOptions();
// $cfOptions->setVerbose(true);
// $cfOptions->setDelay(5);
// $cfOptions->setExtraHeaders(["header: value"])

$page = $cfCurl->exec($ch, $cfOptions);


// Want to get clearance cookies ?
// $cookies = curl_getinfo($ch, CURLINFO_COOKIELIST);

echo $page;