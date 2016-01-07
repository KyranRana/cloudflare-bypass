<?php		
ini_set('display_errors', 1);
	
require_once 'libraries/httpProxyClass.php';
require_once 'libraries/cloudflareClass.php';

$httpProxy   = new httpProxy();
$httpProxyUA = 'proxyFactory';

$requestLink = 'https://coinkite.com';
$requestPage = json_decode($httpProxy->performRequest($requestLink));

// if page is protected by cloudflare
if($requestPage->status->http_code == 503) {
	if($clearanceCookie = cloudflare::bypass($requestLink, $httpProxyUA)) {
		// use clearance cookie to bypass page
		$requestPage = $httpProxy->performRequest($requestLink, 'GET', null, array(
			'cookies' => $clearanceCookie
		));
		// return real page content
		$requestPage = json_decode($requestPage);
		echo $requestPage->content;
	} else {
		// could not fetch clearance cookie
		echo 'Could not fetch CloudFlare clearance cookie (most likely due to excessive requests)';
	}	
}



