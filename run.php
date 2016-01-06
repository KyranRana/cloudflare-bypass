<?php		
require_once 'httpProxyClass.php';
require_once 'cloudflareClass.php';

$httpProxy = new httpProxy();

$requestLink = 'https://coinkite.com';
$requestPage = json_decode($httpProxy->performRequest($requestLink));

// if page is protected by cloudflare
if($requestPage->status->http_code == 503) {
	// use clearance cookie to bypass page
	if(!$clearanceCookie = cloudflare::bypass($requestLink, $requestPage->content)) {
		echo 'Cloud not fetch cookie';
	}
	
	// Currently not working - will need to debug

}


