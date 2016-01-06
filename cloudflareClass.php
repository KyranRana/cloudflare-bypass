<?php
class cloudflare {

	// {{{ Definitons

	/**
	* CloudFlare Protection Level
	*
	* @var integer
	* @access private
	*/
	private static $cfLevel = 0;

	// }}}
	// {{{ bypass()

	/**
	 * Bypasses CloudFlare and returns cf clearance cookie
	 *
	 * @param string $siteLink  Site Host Link
	 * @param string $response  CloudFlare DDoS page
	 *
	 * @return string - Clearance Cookie
	 */
	public static function bypass($siteLink, $response){
		// get website host
		$siteHost = self::getSiteHost($siteLink);
		// get cloudflare answer
		if($cfAnswer = self::getAnswer($siteHost, $response)){			
			// attempt to solve cloudflare equation
			$cfResponse  = $siteHost.'/cdn-cgi/l/chk_jschl?jschl_vc='.$cfAnswer['jschl_vc'].'&pass='.$cfAnswer['pass'].'&jschl_answer='.$cfAnswer['answer'];			
			$cfNewResponse = self::getPage($cfResponse, false, $siteHost);								
			// if refresh cookie exists
			if(!$cfClearance = self::getPageCookie($cfNewResponse, 'cf_clearance')){
				// if maximum amount of attempts has not exceeded
				if(++self::$cfLevel < 5) {
					$cfClearance = self::bypass($siteLink, $cfNewResponse);
				} else {
					return false;
				}
			}
			if(!$cfClearance) return false;
			// store cf user id
			$cfUserId = self::getPageCookie($cfNewResponse, '__cfduid');
			// return clearance cookie
			return $cfClearance.$cfUserId;
		}
	}

	// }}}
	// {{{ getSiteHost()

	/**
	* Gets site host from website link including scheme
	* 
	* @return string 
	*/
	private static function getSiteHost($siteLink){
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
	// {{{ getPageCookie()

	/**
	* Gets given property from cookie string from page response information
	* 
	* @param string $cookie  Page response containing headers
	* @param string $prop    Name of field e.g. 'PHPSESSID'
	*
	* @return string
	*/
	public static function getPageCookie($cookie, $prop){
		// if property exists in cookie
		if(strpos($cookie, $prop) !== false){
			// get cookie property and value
			$prop = str_replace("{$prop}=", "|{$prop}=", $cookie);
			$prop = substr($prop, strpos($prop, '|')    + 1); 
			$prop = substr($prop, 0, strpos($prop, ';') + 1);
			return $prop;
		}
		return false;
	}

	// }}}
	// {{{ getAnswer()

	/**
	 * Gets the answer + pass tokens from CloudFlare DDoS Protection Page by
	 * extracting the CloudFlare javascript challenge code, converting it to
	 * PHP code, and evaluating it.
	 *
	 * @param string $siteLink  Site Host Link
	 * @param string $response  CloudFlare DDoS Protection Page
	 *
	 * @return array
	 * - jsch1 verification code
	 * - pass token
	 * - jsch1 answer
	 */
	private static function getAnswer($siteLink, $response){
		// sleep 4 seconds to mimic waiting process
		sleep(4);
		// get values from js verification code and pass code inputs
		$jschl_vc = self::getInputValue($response, 'jschl_vc');
		$pass     = self::getInputValue($response, 'pass');
		// extract javascript challenge code from CloudFlare script
		$siteLen = mb_strlen(substr($siteLink, strpos($siteLink,'/')+2), 'utf8');
		$script  = substr($response, strpos($response, 'var t,r,a,f,') + mb_strlen('var t,r,a,f,', 'utf8'));		
		$varname = trim(substr($script, 0, strpos($script, '=')));
		$script  = substr($script, strpos($script, $varname));
		$script  = substr($script, 0, strpos($script, 'f.submit()'));
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
		// evaluate PHP script
		eval($script);
		// if cloudflare answer has been found, store it 
		if(is_numeric($answer)) {
			// return verification values
			return array(
				'jschl_vc' => $jschl_vc, 
				'pass'     => $pass, 
				'answer'   => $answer+2
			);
		}
		return false;
	}

	// }}}
	// {{{ getPage()

	/**
	* Fetches data from given webpage using specified header and POST information
	*
	* @param string  $url         Webpage URL e.g. https://facebook.com
	* @param string  $cookie      Cookie data 
	* @param string  $referrer    Referrer URL e.g. https://fbcdn.net
	* @param integer $post        Type of Request (0 - GET, 1 - POST)
	* @param string  $postFields  Post fields 
	*
	* @return string  Webpage markup / data
	*/
	private static function getPage($url, $cookie = false, $referrer = false, $post = 0, $postFields = false){
		// use cURL 
		if($curl = curl_init($url)){
			// settings
			$headers = array(
				'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36',
				'referrer:'.$referrer.'/', 
				'x-requested-with: XMLHttpRequest'
			);	
			if($post===1) $headers[] = 'content-type: application/x-www-form-urlencoded; charset=UTF-8;';
			
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	    	// cookie settings
			if($cookie) {
				curl_setopt($curl, CURLOPT_COOKIE, $cookie);
			}
			// return settings
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
			// ssl settings
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			// post settings
			curl_setopt($curl, CURLOPT_POST, $post);
			// post fields
			if(strlen($postFields) > 0) {
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
			}
			// fetching response
			$response = curl_exec($curl);
			// returning response
			return $response;
		}
		return false;
	}
}

