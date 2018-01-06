<?php
class cloudflare {

	// {{{ Definitons

	/**
	* Will temporarily store amount of attempts taken to get the cloudflare clearance cookie
	*
	* @var integer
	* @access private
	*/
	private static $cfBypassAttempts = 0;
	
	/**
	 * User agent which will be used for all of our cURL requests
	 *
	 * @var string
	 * @access private
	 */
	private static $userAgent;
		 

	// }}}
	// {{{ useUserAgent()
	
	/**
	 * Assigns given user agent string to bypass requests (Required)
	 * Note: Make this the same user agent you use with your cURL requests
	 *
	 * @param string $userAgent  User Agent String 
	 *
	 * @return void  Sets user agent for request
	 */
	public static function useUserAgent($userAgent) {
		self::$userAgent = $userAgent;
	}

	// }}}
	// {{{ bypass()
	
	/**
	 * Bypasses the cloudflare anti-ddos page given the website link
	 *
	 * @param string $siteLink  URL of request
	 *
	 * @return string  Clearance Cookie (if succeeded)
	 */
	public static function bypass($siteLink) {
		// extract site host from site link
		$siteNetLoc = self::getSiteHost($siteLink);
		// try to get clearance cookie from storage
		$cfClearanceCookie = self::getCookie($siteLink);
		// create cookie storage directory if it doesn't exist
		if(!is_dir('cf-cookies')) mkdir('cf-cookies', 0777);
		// if cookie doesn't exist in storage
		if(!$cfClearanceCookie) {
			// create new cookie file to store website's clearance cookie
			self::bypassCloudFlare($siteLink, $siteNetLoc);
		} else {
			// test cookie to see if it still works
			$cfTest = self::getPage($siteLink, $siteNetLoc, array(
				'cookie: '.$cfClearanceCookie
			));	
			// clear cookie log
			unlink('cf-cookies/cookies.txt');
			// if cookie has expired
			if(strpos($cfTest['content'], 'chk_jschl') !== false) {
				// create new cookie file with new clearance cookie
				self::bypassCloudFlare($siteLink, $siteNetLoc);
			}
		}
		// return clearance cookie
		return self::getCookie($siteLink);
	}

	// }}}
	// {{{ bypassCloudFlare()

	/**
	 * Solves the javascript challenge on the anti-ddos page until a clearance cookie is fetched 
	 *
	 * @param string $siteLink  Website link
	 * @param string $siteNetLoc  Website host
	 *
	 * @return string  Clearance Cookie
	 */
	private static function bypassCloudFlare($siteLink, $siteNetLoc) {	
		// request anti-bot page again with referrer as site hostname
		$ddosPage = self::getPage($siteLink, $siteNetLoc);
		// cloudflare user id
		$cfUserId = self::getPageCookie($ddosPage['headers'], '__cfduid');
		// solve javascript challenge in ddos protection page
		if($cfAnswerParams = self::solveJavaScriptChallenge($siteNetLoc, $ddosPage['content'])) {
			// construct clearance link
			$cfClearanceLink = $siteNetLoc.'/cdn-cgi/l/chk_jschl?'.http_build_query($cfAnswerParams);
			// attempt to get cloudflare clearance cookie
			$cfClearanceResp = self::getPage($cfClearanceLink, $siteNetLoc);
			// clear cookie log
			unlink('cf-cookies/cookies.txt');
			// if we fail to get the clearance cookie
			if(!$cfClearanceCookie = self::getPageCookie($cfClearanceResp['headers'], 'cf_clearance')) {
				// if we haven't exceeded the max attempts
				if(self::$cfBypassAttempts < 5) {
					// re-attempt to get the clearance cookie
					self::$cfBypassAttempts++;
					$cfClearanceCookie = self::bypass($siteLink);
				}
			}
			if($cfClearanceCookie) {
				// store cookie data away in a text file 
				self::storeCookie($siteLink, $cfClearanceCookie.$cfUserId);
			}
		}
	}

	// }}}
	// {{{ getCookie()
	
	/**
	 * Attempts to get cloudflare cookie file for given site name, if unsuccessfulr3 returning false
	 *
	 * @param string $siteNetLoc  Site host link
	 *
	 * @return mixed  Will either return the file name or false
	 */
	private static function getCookie($siteNetLoc) {
		// see we have clearance cookie in storage
		$cfSessionFile = 'cf-cookies/'.md5($siteNetLoc);
		$cfCookieData  = @file_get_contents($cfSessionFile);
		// return cloudflare clearance cookie if we have it
		if($cfCookieData) {
			return $cfCookieData;
		}
		return false;
	}

	// }}}
	// {{{ storeCookie()
	
	/**
	 * Generates a base64 file for current website, and will assign cookie data to that file
	 *
	 * @param string $siteNetLoc  Website host (including scheme)
	 * @param string $cookieData  Cookie data to assign to the website
	 *
	 * @return void  Generate a base64 file for the site, storing the cloudflare clearance
	 *               cookie data inside that file
	 */
	private static function storeCookie($siteNetLoc, $cookieData) {
		// generate clearance cookie information to store in file
		$cfSessionFile = 'cf-cookies/'.md5($siteNetLoc);
		$cfAuthCookies = $cookieData;
		// append cookie for 24 hour use
		file_put_contents($cfSessionFile, $cfAuthCookies);
		// return cookie file
		return $cfSessionFile;
	}

	// }}}
	// {{{ getSiteHost()

	/**
	* Gets site host from website link including scheme
	* 
	* @return string 
	*/
	private static function getSiteHost($siteLink) {
		// parse url and get different components
		$siteParts = parse_url($siteLink);
		// extract full host components and return host
		return $siteParts['scheme'].'://'.$siteParts['host'];
	}

	// }}}
	// {{{ getInputValue()

	/**
	 * Gets value of given input element name from HTML markup
	 *
	 * @param string $response  CloudFlare DDoS page
	 * @param string $value     Name of HTML input element
	 *
	 * @return string - value of element 
	 */
	private static function getInputValue($response, $value) {
		// get value of input with name of $value
		$cfParam = substr($response, strpos($response, $value));
		// store value
		$cfParam = substr($cfParam, strpos($cfParam, 'value="') + mb_strlen('value="', 'utf8'));
		$cfParam = substr($cfParam, 0, strpos($cfParam, '"'));
		// return value
		return $cfParam;
	}

	// }}}
	// {{{ extractPageHeadersContent()
	
	/**
	 * Will extract page headers and content from cURL execution object
	 *
	 * @param string $pageResponse  page response data
	 *
	 * @return array  Array containing page headers and content 
	 */
	private static function extractPageHeadersContent($pageResponse) {
		// headers we should follow
		$headersToFollow = array('HTTP/1.1 100');
		// get page contents...
		$delimiterRegex = '/([\r\n][\r\n])\\1/';
		$pageDataArray  = preg_split($delimiterRegex, $pageResponse, 2);
		// get http code portion out of page headers
		$pageHeaders = substr($pageDataArray[0], 0, 12);
		// simulate page redirect for as long as the page redirects
		if(in_array($pageHeaders, $headersToFollow)) {
			$pageDataArray = self::extractPageHeadersContent($pageDataArray[1]);
		}
		return $pageDataArray;
	}

	// }}}
	// {{{ solveJavaScriptChallenge()

	/**
	 * Gets the answer + pass tokens from CloudFlare DDoS Protection Page by extracting the 
	 * CloudFlare javascript challenge code, converting it to PHP code, and evaluating it.
	 *
	 * @param string $siteLink  Site Host Link
	 * @param string $response  CloudFlare DDoS Protection Page
	 *
	 * @return array
	 * - jsch1 verification code
	 * - pass token
	 * - jsch1 answer
	 */
	private static function solveJavaScriptChallenge($siteLink, $response){
		// sleep 4 seconds to mimic waiting process
		sleep(4);
		// get values from js verification code and pass code inputs
		$jschl_vc = self::getInputValue($response, 'jschl_vc');
		$pass     = self::getInputValue($response, 'pass');
		// extract javascript challenge code from CloudFlare script
		$siteLen = mb_strlen(substr($siteLink, strpos($siteLink,'/')+2), 'utf8');
		$script  = substr($response, strpos($response, 'var s,t,o,p,b,r,e,a,k,i,n,g,f,') + mb_strlen('var s,t,o,p,b,r,e,a,k,i,n,g,f,', 'utf8'));
		$varname = trim(substr($script, 0, strpos($script, '=')));
		$script  = substr($script, strpos($script, $varname));
		// removing form submission event
		$script  = substr($script, 0, strpos($script, 'f.submit()'));
		// structuring javascript code for PHP conversion
		$script  = str_replace(array('t.length', 'a.value'), array($siteLen, '$answer'), $script);
		$script  = str_replace(array("\n", " "), "", $script);
		$script  = str_replace(array(";;", ";"), array(";", ";\n"), $script);
		// convert challenge code variables to PHP variables
		$script  = preg_replace("/[^answe]\b(a|f|t|r)\b(.innerhtml)?=.*?;/i", '', $script);
		$script  = preg_replace("/(\w+).(\w+)(\W+)=(\W+);/i", '$$1_$2$3=$4;', $script);
		$script  = preg_replace("/(parseInt)?\((\w+).(\w+),.*?\)/", 'intval($$2_$3)', $script);
		$script  = preg_replace("/(\w+)={\"(\w+)\":(\W+)};/i", '$$1_$2=$3;', $script);
		// convert javascript array matrix in equations to binary which PHP can understand
		$script  = str_replace(array("!![]", "!+[]"), 1, $script);
		$script  = str_replace(array("![]", "[]"), 0, $script);
		$script  = str_replace(array(")+", ").$siteLen"), array(").", ")+$siteLen"), $script);	
		// take out any source of javascript comment code - #JS Comment Fix
		$script  = preg_replace("/'[^']+'/", "", $script);
		// Fix
		$script  = str_replace('f.action+=location.hash;', '', $script);
		// evaluate PHP script
		eval($script);
		// if cloudflare answer has been found, store it 
		if(is_numeric($answer)) {
			// return verification values
			return array(
				'jschl_vc'      => $jschl_vc, 
				'pass'          => str_replace('+', '%2', $pass),
				'jschl_answer'  => $answer
			);
		}
		return false;
	}
	
	// }}} 
	// {{{ getPageCookie()

	/**
	* Extracts property from cookie string within given page response
	* 
	* @param string $cookie   String containing cookie information
	* @param string $property Property name
	*
	* @return string
	*/
	public static function getPageCookie($cookie, $property){
		// if property exists in cookie
		if(strpos($cookie, $property) !== false){
			// get cookie property and value
			$property = str_replace("{$property}=", "|{$property}=", $cookie);
			$property = substr($property, strpos($property, '|')    + 1); 
			$property = substr($property, 0, strpos($property, ';') + 1);
			// return value stored inside cookie property
			return $property;
		}
		return false;
	}
	
	// }}}
	// {{{ getPage()

	/**
	* Fetches data from webpage given the URL, referrer, and array of headers to send
	*
	* @param string  $url       URL of request
	* @param string  $referer   Referrer of Request
	* @param string  $headers   Request headers to send
	*
	* @return string  Webpage markup
	*/
	private static function getPage($link, $referer, $headers = array()){
    		// use cURL
		if($curlResource = curl_init($link)){
    			// header settings
    			curl_setopt($curlResource, CURLOPT_HEADER, 1);
    			curl_setopt($curlResource, CURLOPT_REFERER, $referer.'/'); 
        		// user agent settings
        		curl_setopt($curlResource, CURLOPT_USERAGENT, self::$userAgent);
        		// add headers if they are given
        		if(sizeof($headers) > 0) {
        		    curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headers);
        		}
        		// session cookies
        		curl_setopt($curlResource, CURLOPT_COOKIEJAR,  'cf-cookies/cookies.txt');
        		curl_setopt($curlResource, CURLOPT_COOKIEFILE, 'cf-cookies/cookies.txt');
        		// return settings
        		curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
        		curl_setopt($curlResource, CURLOPT_FOLLOWLOCATION, true);
        		// ssl settings
        		curl_setopt($curlResource, CURLOPT_SSL_VERIFYHOST, false);
        		curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, false);
        		// post settings
        		curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, 'GET');
        		// fetching response
        		$response = curl_exec($curlResource);
        		// close connection
        		curl_close($curlResource);
        		// extracting page headers and content 
    			list($pageHeaders, $pageContents) = self::extractPageHeadersContent($response);
        		// returning response
        		return array(
        			'headers' => $pageHeaders,
        			'content' => $pageContents
        		);
		}
		return false;
	}
}
