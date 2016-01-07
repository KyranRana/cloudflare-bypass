<?php	
class httpProxy {

	// {{{ Definitions
	 
	/** 
	 * @var boolean $enableJsonP|false Enables requests to be returned in JSONP format (with an temp
	 * callback name)
	 * @see 'JSONP format' and 'Configuration Options' topics for usage
	 */
	private $enableJsonP = false; 
	
	/**
	 * @var boolean $enableNative|true Enables requests to be returned in native format, where request
	 * headers are sent as raw http headers.
	 * @see 'Native requests' and 'Configration Options' topics for usage
   	 */ 
	private $enableNative = false;  
	
	/** 
	 * @var string $urlValidationRegex|/.asterisk/ Used to check if the url provided on each request 
	 * is valid 
	 * @see 'Configuration Options' for explanation
	 */
	private $urlValidationRegex = "/.*/"; 
	
	/** 
	 * @var string $userAgent|"proxyFactory" Modifies global user agent string used for each request
	 * @see 'Configuration Options' for explanation and usage
	 */
	private $userAgent = "proxyFactory";
	
	/**
	 * @var array $validReturnTypes|array("native", "json", "jsonp") return types supported by this
	 * class
	 * @see 'Configuration Options' for explanation and usage
	 */
	private $validReturnTypes = array('native', 'json', 'jsonp');
	
	/** 
	 * @var boolean $enableSSL|false Enables SSL certificates to be validated on SSL based requests 
	 * @see 'Configuration Options' topics for explanation and usage
	 */
	private $enableSSL = false;
	
	/** 
	 * @var boolean $enableSSLVersionEdits|false Enables SSL version to be selected on each request 
	 */
	private $enableSSLVersionEdits = false;
	
	/**
	 * @var array $validSSLCertTypes|array("DER", "ENG", "PEM") SSL certificate types supported by PHP
	 */
	private $validSSLCertTypes = array('PEM', 'DER', 'ENG');


	// }}}	
	
	// ----- Constructor ----- //
	
	public function __construct() { }
	
	// ----- Globals ----- //

	// {{{ global__enableNative()
	
	/**
	 * Enables native mode. Take caution when allowing this as with native set, this will cause http
	 * proxy to output responses exactly as they are received.
	 *
	 * @return void
	 */
	public function global__enableNative() { 
		$this->enableNative = true;
	}
	  
	// }}}
	// {{{ global__disableNative()
	 
	/**
	 * This will prohibit the use of native mode in http proxy. Any direct usage of Native with this
	 * mode disabled will result in an exception.
	 *
	 * @return void
	 */	 
	public function global__disableNative() { 
		$this->enableNative = false;
	} 
	 
	// }}}
	// {{{  global__enableJsonP()
	
	/**
	 * Enables jsonp mode. When setting the mode to 'jsonp' for a request, make sure to also set a
	 * 'callback' for http proxy to use
	 *
	 * @return void
	 */
	public function global__enableJsonP() { 
		$this->enableJsonP = true;
	}
	
	// }}}
	// {{{  global__disableJsonP()
	
	/**
	 * This will prohibit the use of jsonp mode in http proxy. Any direct usage of JSONP with this
	 * mode disabled will result in an exception.
	 *
	 * @return void
	 */
	public function global__disableJsonP() { 
		$this->enableJsonP = false;
	}
	
	// }}}
	// {{{  global__enableSSL()
	
	/**
	 * Enables ssl mode. This will allow the SSL support built in to http proxy, enabling users to
	 * properly authorize themselves with SSL servers.
	 *
	 * @return void
	 */
	public function global__enableSSL() { 
		$this->enableSSL = true;
	}
	
	// }}}
	// {{{  global__disableSSL()
	
	/**
	 * This will disable the use of authentic SSL connection in http proxy; SSL host and peer 
	 * verification will still be prevented to allow free connection to SSL servers which are not
	 * as strict.
	 *
	 * @return void
	 */
	public function global__disableSSL() { 
		$this->enableSSL = false;
	}
	
	// }}}
	// {{{  global__enableSSLVersionEdits()
	
	/**
	 * This will allow the SSL version number used for each request to be manually changed. Prevent
	 * enabling this option when possible, as manually setting the SSL version can cause security 
	 * issues.
	 *
	 * @return void
	 */
	public function global__enableSSLVersionEdits() { 
		$this->enableSSLVersionEdits = true;
	}
	
	// }}}
	// {{{  global__disableSSLVersion()
	
	/**
	 * This will prohibit the use of ssl version editing on each request. Any direct attempt at 
	 * editing the ssl version number with this option disabled will throw an exception.
	 *
	 * @return void
	 */
	public function global__disableSSLVersion() { 
		$this->enableSSLVersionEdits = false;
	}
	
	
	// }}}
	// {{{  global__updateURLValidationRegex()
	
	/**
	 * Changes the regex pattern which is used to verify the url on each request. Take caution as
	 * this will globally affect how URLs are interpreted by http proxy.
	 *
	 * @param $newRegex new regular expression
	 *
	 * @return void
	 */
	public function global__updateURLValidationRegex($newRegex) { 
		$this->urlValidationRegex = $newRegex;
	}
	
	// }}}
	// {{{  global__updateUserAgent()
	
	/**
	 * Globally changes the user agent which http proxy uses on each request. Default user agent
	 * upon class instantiation is 'proxyFactory'
	 *
	 * @param $userAgent new user agent string
	 *
	 * @return void
	 */
	public function global__updateUserAgent($userAgent) { 
		$this->userAgent = $userAgent;
	}
	
	// }}}
	
	// ----- Helper Methods ----- //
	
	// {{{ isValidUrl()
	
	/**
	 * Used on every request to check if a URL is valid, verifying it is not empty and correctly
	 * matches the pattern specified in global__modifyURLRegex()
	 *
	 * @param string $url  URL string to validate
	 *
	 * @return boolean
	 */
	private function isValidUrl($url) {
		// compare url against valid url regex
		return !empty($url) && preg_match($this->urlValidationRegex, $url);
	}
	
	// }}}
	// {{{ throwError()
	
	/**
	 * Throws an exception in cases of error. This method will construct a user friendly JSON object
	 * with the exception message you give to it.
	 *
	 * @param string $message Exception message
	 *
	 * @return void 
	 */
	private function throwError($message) {
		// create http error object with message
		$errorMessage = '{{ "http_code":"error" }, "contents":"'.$message.'" }';
		// throw http error object
		echo $errorMessage; 
		exit(0);
	}
	
	// }}}
	// {{{ generateCookieString()
	
	/**
	 * Generates a valid cookie string given an array of key and values, and enables session data 
	 * to be appended to the cookie string.
	 *
	 * @param array   $cookies          Array of cookies
	 * @param boolean $sessionInCookie  Allows session data to be appended to cookie
	 * 
	 * @return string  Formatted cookie string
	 */
	private function generateCookieString($cookies, $sessionInCookie) {
		// to store our modified cookies
		$cookiesArray = array();
		// populate cookies into cookie array
	    foreach($cookies as $property => $value) {
		    // append cookie property and value to cookie array
	    	$cookiesArray[] = $property.'='.$value;
	    }
	    // populate session data in cookie array if required
	    if ($sessionInCookie) {
	    	$cookiesArray[] = SID;
	    }
	    // create formatted cookie string from cookie array
	    $cookieString = implode('; ', $cookiesArray); 
	    // return formatted cookie string
	    return $cookieString; 
	}	
		
	// }}}	
	// {{{ sendAsRawHttpHeaders()
	
	/**
	 * Sends page headers given as raw http headers to the webpage using PHP's built-in header
	 * method - handling exceptions during the process.
	 *
	 * @param string  $headers           Page headers
	 * @param boolean $headersToExclude  Page headers to exclude (optional)
	 *
	 * @return void  Will only send raw http headers
	 */
	private function sendAsRawHttpHeaders($headers, $headersToExclude = array()) {
		try {
			// split headers into an array
			$httpHeaders = preg_split('/[\r\n]+/', $headers);
			// convert blacklisted headers into a regex OR pattern
			$headersToExcludeRegex = implode($headersToExclude, '|');
			// iterate through each of the headers
			foreach($httpHeaders as $httpHeader) {
				// send as raw http header if unblacklisted
				if(preg_match('/^(?:'.$headersToExcludeRegex.'):/i', $httpHeader)) {
					header($httpHeader);
				}
			}
		} catch(Exception $ex) {
			$this->throwError($ex->getMessage());
		}
	}
	
	// }}}
	// {{{ populateArrayWithRequestHeaders()
	
	/**
	 * Will convert page headers given into a key value array, and store this array in our
	 * data array under key 'headers'
	 *
	 * @param string  $headers     Page headers
	 * @param boolean $dataTarget  Data array to store header information (reference)
	 *
	 * @return void  Will parse headers and store them in our data target
	 */
	private function populateArrayWithRequestHeaders($headers, &$dataTarget) {
		// create our header array for storing
		$dataTarget['headers'] = array();
		// split headers into array
		$httpHeaders = preg_split('/[\r\n]+/', $headers);
		// iterate through each of the headers
		foreach($httpHeaders as $httpHeader) {
			// extract header property and value
			preg_match('/^(.+?):\s+(.*)$/', $httpHeader, $matches);	
			// if header is not null
			if(isset($matches[1]) && trim($matches[1]) != '') {
				// store http header in header array
				$dataTarget['headers'][$matches[1]] = $matches[2];
			}
		}
	}
	
	// }}}
	// {{{ populateArrayWithRequestData()
		
	/**
	 * Will populate all request information (including status and content) into data target array:
	 * 
	 * - Request status object will be stored under 'status' key; this will include only http 
	 *   code unless $storeFullStatus is set to true. This option will force http proxy to 
	 *   store the entire status object.
	 *
	 * - Request content will be stored under 'contents' key; http proxy will try to JSON decode 
	 *   this value in cases of it being a json object.
	 *
	 * @param string  $content          Response contents
	 * @param array   $status           Response status object
	 * @param boolean $storeFullStatus  Whether to store full status object in data target or not
	 * @param array   $dataTarget       Data array to store request data (reference)
	 *
	 * @return void Will only store request data inside data target
	 */
	private function populateArrayWithRequestData($contents, $status, $storeFullStatus, &$dataTarget) {
	 	// populate status object storing full status object if set
		if($storeFullStatus){
			$dataTarget['status'] = $status;
		} else {
		    $dataTarget['status'] = array();
		    $dataTarget['status']['http_code'] = $status['http_code'];
		}
		// try decoding page contents as json if possible before sending it
		$decodedContent = json_decode($contents);
		$decodedContent = $decodedContent ? $decodedContent : $contents;
		// if content could be parsed as json object
		if(json_last_error_msg() == 'No error') {
			// store object as is
			$dataTarget['content'] = $decodedContent;
		} else {
			// utf8 encode object before storing
			$dataTarget['content'] = utf8_encode($decodedContent);	
		}
	}
	
	// }}}
	// {{{ outputPageAsJsonP()
	
	/**
	 * Outputs page response in jsonP callback object where the callback is defined by the user.
	 * If jsonP mode is not enabled this method will throw an exception.
	 *
	 * @param $jsonString     Json object containing page response
	 * @param $jsonPCallback  JsonP callback defined by user
	 *
	 * @return string  jsonP object containing page response
	 */
	private function outputPageAsJsonP($jsonString, $jsonPCallback) {
		if(!$this->enableJsonP) {
			$this->throwError('jsonP mode is not enabled');
		}
		// return jsonp object with callback
		$jsonPCallback = isset($jsonPCallback) ? $jsonPCallback : null;
		return $jsonPCallback.'('.$jsonString.')';
	}
	
	// }}}
	// {{{ serializeJsonArray()
	
	/**
	 * Returns data array serialized in json or jsonp depending on which mode has been selected
	 *
	 * @param array  $dataTarget  Data array to serialize as json
	 * @param string $headers     Request Configuration Object
	 * 
	 * @return string  Encoded json / jsonp array with page response data
	 */
	private function serializeArrayAsJson($dataTarget, $headers) {
		// serialize data target
		$jsonString = json_encode($dataTarget, JSON_HEX_QUOT | JSON_HEX_TAG);				
		// return jsonp callback object if mode is jsonp
		if($headers['mode'] == 'jsonp') {
			return $this->outputPageAsJsonP($jsonString, $headers['callback']);
		}
		return $jsonString;
	}
	
	// }}}
	// {{{ outputPageAsNative()
	
	/**
	* Outputs page response the same way it has been fetched from the given endpoint - this method
	* forces the browser to mimic response headers.
	*
	* @param string $header   Page headers
	* @param string $content  Page content
	*
	* @return void  Outputs page response in the same way it was received
	*/
	private function outputPageAsNative($pageHeaders, $pageContents) {
		if(!$this->enableNative) {
			$this->throwError('native mode not enabled');	
		}
		$headersToExclude = array(
			'set-cookie',
			'content-type', 
			'content-language'
		);
		// send all headers as raw http headers apart from blacklisted headers
		$this->sendAsRawHttpHeaders($pageHeaders, $headersToExclude);
		// print raw content to current page
		echo $pageContents; 
	}
	
	// }}}
	// {{{ outputPageAsJson()
	
	/**
	* Outputs page headers and content in a JSON or JSONP object, depending on which mode has been
	* selected on request
	*
	* @param string $pageHeaders  Page headers
	* @param string $pageContents Page content
	* @param object $httpStatus   Http Status Object
	* @param object $headers      Request Configuration
	*
	* @return string  Encoded JSON object containing page headers and content
	*/
	private function outputPageAsJson($pageHeaders, $pageContents, $httpStatus, $headers) {
		// $data will be serialized into JSON data.
		$data = array();
		// Propagate all HTTP headers into the JSON data object.
		$this->populateArrayWithRequestHeaders($pageHeaders, $data);
		// Propagate all cURL request / response info to the JSON data object.
		$this->populateArrayWithRequestData($pageContents, $httpStatus, $headers['fullStatus'], $data);	    
		// return JSON/JSONP string
		return $this->serializeArrayAsJson($data, $headers);
	}
	
	// }}}

	// ----- Request Helpers ----- //
	
	// {{ configureRequestCookies()
	
	/**
 	 * Configures cookies for the performRequest() function given the curl resource, cookies, and 
 	 * session cookie parameters - validation included.
	 *
	 * @param string $cookies          Request cookies
	 * @param string $sessionInCookie  Whether to include session in cookie or not
	 * @param object $curlResource     cURL Resource (reference)
	 *
	 * @return void  Assigns cookie to curl resource
	 */
	private function configureRequestCookies($cookies, $sessionInCookie, &$curlResource) {
		// if request cookies have been given
		if (isset($cookies) && is_string($cookies)) {
			// if cookies are given as an array
			if(is_array($cookies)) {
				// $sessionInCookie : defaults to false
				$sessionInCookie = isset($sessionInCookie) ? $sessionInCookie : false;
				// generate formatted cookie string from array of cookies
				$cookies = $this->generateCookieString($cookies, $sessionInCookie);
			}
			// add cookie settings to curl resource
			curl_setopt($curlResource, CURLOPT_COOKIE, $cookies); 
		}  			
	}
	
	// }}}
	// {{{ configureSSLHost()
	
	/**
	 * Checks if user has configured SSL host validation, if not setting it to true as default via
	 * CURLOPT_SSL_VERIFYHOST property
	 * 
	 * @param mixed  $sslHostValue  Value specified for SSL host validation in request object
	 * @param object $curlResource  cURL resource (reference)
	 *
	 * @return void  Sets SSL host validation setting in cURL resource
	 */
	private function configureSSLHost($sslHostValue, &$curlResource) {
		// decide appropriate perm for ssl host
		$verifySSLHost = isset($sslHostValue) ? $sslHostValue : 0;
		$verifySSLHost =  $verifySSLHost == 1 ? 2 : 0;
		curl_setopt($curlResource, CURLOPT_SSL_VERIFYHOST, $verifySSLHost);
	}
	
	
	// }}}
	// {{{ configureSSLPeer()
	
	/**
	 * Checks if user has configured SSL client validation, if not setting it to true as default
	 * via CURLOPT_SSL_VERIFYPEER property
	 * 
	 * @param mixed  $sslPeerValue  Value specified for SSL peer validation in request object
	 * @param object $curlResource  cURL resource (reference)
	 *
	 * @return void  Sets SSL peer validation setting in cURL resource
	 */
	private function configureSSLPeer($sslPeerValue, &$curlResource) {
		// decide appropriate perm for ssl peer 
		$verifySSLPeer = isset($sslPeerValue) ? $sslPeerValue : 0;
		curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, $verifySSLPeer);
	}
	
	// }}}
	// {{{ configureSSLCertificate()
	
	/**
	 * Checks if user has configured the SSL certificate, and certificate type properties via
	 * request configuration object. If not, this function will avoid setting the certificate
	 * param, however will set the certificate type to PEM (default option in PHP)
	 *
	 * @param mixed  $sslCertValue      SSL Certificate to use for request
	 * @param mixed  $sslCertTypeValue  SSL Certificate Type to use for request
	 * @param object $curlResource      cURL resource (reference)
	 *
	 * @return void  Sets SSL certification and certification type for cURL resource
	 */
	private function configureSSLCertificate($sslCertValue, $sslCertTypeValue, &$curlResource) {
		// determine ssl certificate type 
		$sslCertType = isset($sslCertTypeValue) ? $sslCertTypeValue : 'PEM';
		// assign ssl certificate type to curl resource
		curl_setopt($curlResource, CURLOPT_SSLCERTTYPE, $sslCertType);
		// if ssl certificate path has been set
		if(isset($sslCertValue) && is_string($sslCertValue)) {
			// assign ssl certificate path to curl resource
			curl_setopt($curlResource, CURLOPT_SSLCERT, $sslCertValue);		
		}
	}
	
	// }}}
	// {{{ configureSSLVersion()
	
	/**
	 * Checks if a value has been set for the SSL version number, and if SSL version editing
	 * is enabled will assign that version number to CURLOPT_SSLVERSION property.
	 *
	 * @param mixed  $sslVersionNumber  SSL version number to use for request
	 * @param object $curlResource      cURL resource (reference)
	 *
	 * @return void  Sets SSL version number for cURL resource 
	 */
	private function configureSSLVersion($sslVersionNumber, &$curlResource) {
		// if ssl version number has been given
		if(isset($sslVersionNumber) && is_numeric($sslVersionNumber)) {
			// throw error if ssl version edits is disabled
			if(!$this->enableSSLVersionEdits) {
				$this->throwError('SSL version edits not enabled');
			}
			// assign version number to cURL resource
			curl_setopt($curlResource, CURLOPT_SSLVERSION, $sslVersionNumber);
		}
	} 
	
	// }}}
	// {{{ throwErrorIfInvalidSSLCertType()
	
	/**
	 * Throws an error if SSL certificate type provided doesn't match a SSL certificate type
	 * supported by PHP
	 *
	 * @param mixed  $sslCertTypeValue   SSL certificate type 
	 * 
	 * @return void  Throws an error if SSL certificate type is invalid
	 */
	private function throwErrorIfInvalidSSLCertType($sslCertTypeValue) {
		// if ssl certificate type has been set
		if(isset($sslCertType) && is_string($sslCertType)) {	
			// if ssl certificate type is invalid
			if(!in_array(strtoupper($sslCertTypeValue), $this->sslCertTypes)) {
				// get supported ssl cert types
				$sslCertTypesSupported = implode($this->sslCertTypes, ', ');
				// throw error with supported types
				$this->throwError('SSL Cert Types supported: '.$sslCertTypesSupported);
			}
		}
	}
	
	// }}} 
	// {{{ configureSSL()
	
	/**
	 * Configures SSL settings for request if they have been specified in the request config
	 *
	 * @param object $sslConfig     SSL settings specified in request configuration object
	 * @param object $curlResource  cURL resource (reference)
	 *
	 * @return void  Configures relevant settings for SSL based connection
	 */
	private function configureSSL($sslConfig, &$curlResource) {
		// if ssl based connections has been enabled
		if($this->enableSSL) {
			// store ssl certificate type as its being used twice
			$sslCertType = $sslConfig['certType'];
			// throw error if ssl cert type is not supported
			$this->throwErrorIfInvalidSSLCertType($sslCertType);	
			// configure ssl host and peer
			$this->configureSSLHost($sslConfig['host'], $curlResource);
			$this->configureSSLPeer($sslConfig['peer'], $curlResource);
			// configure ssl certificates
			$this->configureSSLCertificate($sslConfig['cert'], $sslCertType, $curlResource);		
			// configure ssl version numbers
			$this->configureSSLVersion($sslConfig['version'], $curlResource);
		} else {
			// error: SSL not enabled
			$this->throwError('SSL validation is not enabled');
		}
	}
	
	// }}}	
	// {{{ disableSSL()

	/**
	 * Disables SSL host and peer verification in cURL; this will still allow the user to 
	 * connect to SSL servers which don't require validation.
	 *
	 * @param object $curlResource  cURL resource (reference)
	 *
	 * @return void  Disables SSL verification for host and peer
	 */
	private function disableSSL(&$curlResource) {
		// disable ssl host and peer verification
		curl_setopt($curlResource, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curlResource, CURLOPT_SSL_VERIFYPEER, false); 
	}
	
	// }}}
	// {{{ configureSSLForRequest()
	
	/**
	 * Configures all general SSL settings for performRequest(), given the cURL resource,
	 * and SSL configuration
	 *
	 * @param object $sslConfig     SSL settings specified in request configuration object
	 * @param object $curlResource  cURL resource (reference)
	 *
	 * @return void  Configures relevant settings for SSL 
	 */
	private function configureSSLForRequest($sslConfig, &$curlResource) {
		// if ssl settings have been configured in request config
		if(!isset($sslConfig) || !is_array($sslConfig)) {
			// disable ssl verification
			$this->disableSSL($curlResource);
		} else {
			// configure ssl settings
			$this->configureSSL($sslConfig, $curlResource);
		}
	}
	
	// }}} 
	// {{{ configureRequestType()
	
	/**
	 * Configures request type for performRequest(), given the request type and cURL resource.
	 * This function will validate if the given request type is valid, and if its not an error
	 * will be thrown.
	 *
	 * @param string $requestTypeValue  Type of HTTP request
	 * @param object $curlResource      cURL resource (reference)
	 *
	 * @return void  Sets request type for cURL resource
	 */
	private function configureRequestType($requestTypeValue, &$curlResource) {
		// valid requests types
		$validRequestTypes = array('HEAD', 'POST', 'PUT', 'GET', 'DELETE', 'OPTIONS', 'TRACE');
		// if request type is invalid 
		if(!in_array(strtoupper($requestTypeValue), $validRequestTypes)) {
			// get supported request types
			$validRequestTypes = implode($validRequestTypes, ', ');
			// error: show valid request types
			$this->throwError('Request types supported: '.$validRequestTypes);
		}
		// assign request type to cURL resource
		curl_setopt($curlResource, CURLOPT_CUSTOMREQUEST, $requestTypeValue);
	}
	
	// }}}
	// {{{ configureRequestFieldsForPost()
	
	/**
	 * Configures post fields for performRequest() if given post fields to work with as well as
	 * a cURL resource to assign those fields to. 
	 *
	 * @param string   $requestTypeValue  Type of request 
	 * @param mixed[]  $postFields        Post fields to send with request
	 * @param object   $curlResource      cURL resource (reference)
	 *
	 * @return void  Sets post fields for cURL resource 
	 */
	private function configureRequestFieldsIfPost($requestTypeValue, $postFields, &$curlResource) {
		// if a post request is being performed
		if (strtolower($requestTypeValue) == 'post') {
			// if post fields have been given
			if (!is_array($postFields) && mb_strlen($postFields, 'utf8') > 0 || sizeof($postFields) > 0) {
				// attach post fields to request
				curl_setopt($curlResource, CURLOPT_POSTFIELDS, $postFields);
			}
		}	
	}
	

	// }}}
	// {{{ throwErrorIfInvalidReturnType()
	
	/**
	 * Throws an error if return type does not match neither of the 3 valid return types; check 
	 * $validReturnTypes at top of the class.
	 *
	 * @param string $returnTypeValue  Return type value
	 *
	 * @return void  Throws an error if return type is invalid
	 */
	private function throwErrorIfInvalidReturnType($returnTypeValue) {
		// if a return type has been set
		if(isset($returnTypeValue) && is_string($returnTypeValue)) {
			// if an invalid return type has been given
			if(!in_array(strtolower($returnTypeValue), $this->validReturnTypes)) {
				// get supported return types
				$validReturnTypes = implode($this->validReturnTypes, ', ');
				// throw error with supported types 
				$this->throwError('Supported return types: '.$validReturnTypes);
			}
		}
	}
	
	// }}}
	// {{{ configureReturnSettingsForRequest()
	
	/**
	 * Configures all return settings for performRequest(), this including if cURL should follow
	 * temporary redirects and if so how many times (max redirects)
	 *
	 * @param boolean $returnResponse   Whether cURL should return response data back 
	 * @param boolean $followRedirects  Whether cURL should trail the request
	 * @param integer $maxRedirects     How many times cURL should follow a redirect
	 * @param object  $curlResource     cURL resource (reference)
	 *
	 * @return void  Sets all return type settings for cURL resource
	 */
	private function configureRedirectsForRequest($followRedirects, $maxRedirects, &$curlResource) {
		// determine best setting for redirects
		$followRedirects = isset($followRedirects) ? $followRedirects : true;
		curl_setopt($curlResource, CURLOPT_FOLLOWLOCATION, $followRedirects);
		// if value has been given for max redirect
		if(isset($maxRedirects) && is_numeric($maxRedirects)) {
			// attach max redirect value to cURL resource
			curl_setopt($curlResource, CURLOPT_MAXREDIRS, $maxRedirects);
		}
	}
	
	// }}}
	// {{{ configureRequestHeaders()
	
	/**
	 * Configures all header settings for performRequest(), this including configuration of raw 
	 * headers, user agents, and response headers. 
	 * 
	 * @param array   $headersToSend          Raw HTTP headers to send with request
	 * @param string  $requestType            Type of request being performed
	 * @param object  $curlResource           cURL resource (reference)
	 *
	 * @return void  Sets general header settings for cURL resource    
	 */	 
	private function configureRequestHeaders($headersToSend, $requestType, &$curlResource) {
		// assign headers to cURL resource
		curl_setopt($curlResource, CURLOPT_HEADER, true);
		// assign header output signal to resource
		curl_setopt($curlResource, CURLINFO_HEADER_OUT, true);
		// assign user agent to cURL resource
		curl_setopt($curlResource, CURLOPT_USERAGENT, $this->userAgent);		
		// if raw http headers are given
		if(isset($headersToSend) && is_array($headersToSend)) {
			// send raw http headers with request
			curl_setopt($curlResource, CURLOPT_HTTPHEADER, $headersToSend);
		}
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
	private function extractPageHeadersContent($pageResponse){
		// headers we should follow
		$headersToFollow = array('HTTP/1.1 100', 'HTTP/1.1 302', 'HTTP/1.1 301');
		// get page contents...
		$delimiterRegex = '/([\r\n][\r\n])\\1/';
		$pageDataArray  = preg_split($delimiterRegex, $pageResponse, 2);
		// get http code portion out of page headers
		$pageHeaders = substr($pageDataArray[0], 0, 12);
		// simulate page redirect for as long as the page redirects
		if(in_array($pageHeaders, $headersToFollow)) {
			$pageDataArray = $this->extractPageHeadersContent($pageDataArray[1]);
		}
		return $pageDataArray;
	}
	
	// }}}
	// {{{ populateHeaderOptions()
	
	/**
	 * Fills undefined header indexes for performRequest(), preventing notice errors
	 *
	 * @param $headers  Header options 
	 *
	 * @return array  Array with all header settings defined
	 */
	private function populateHeaderOptions(&$headers) {
		// all header options
		$headerOptions = array('mode', 'cookies', 'sessionInCookie', 'ssl', 'followRedirects', 'maxRedirects', 'fullStatus', 'raw');
		// Propagate all header options as keys in headers array
		foreach($headerOptions as $headerOption) {
			// append header option as a key if it doesn't exist as a key property
			if(!isset($headers[$headerOption])) {
				$headers[$headerOption] = null;
			}
		}
	}
	
	// }}}
	// {{{ outputPageResponse()
	
	/**
	 * Will perform output duties for performRequest(), this includes checking if page response 
	 * needs to be outputted natively or in a json object.
	 *
	 * @param $pageHeaders   Page Headers
	 * @param $pageContents  Page Contents
	 * @param $httpStatus    Page Status Object
	 * @param $headers       Request Configuration Object
	 * 
	 * @return void  Will output page natively or in json depending on mode given
	 */
	private function outputPageResponse($pageHeaders, $pageContents, $httpStatus, $headers) {
		// if return type specified is native
		if($headers['mode'] == 'native') {
			// output page as native
			$this->outputPageAsNative($pageHeaders, $pageContents);
		} else {
			// return page as json
			return $this->outputPageAsJson($pageHeaders, $pageContents, $httpStatus, $headers);
		}
	}
	
	// ----- Request Method ----- //
		
	// }}}
	// {{{ peformRequest()
	
	/**
	 * Gets external HTML, JSON, JSONP, XML data and more! This function is fully equiped allowing
	 * all types of requests, in addition it also supports SSL, cookies, sessions, and proxy based
	 * connections.
	 *
	 * Header options
	 * --------------
	 * SSL
	 * ---
	 * - host      - allows host verification - defaults to true
	 * - peer      - allows peer verification - defaults to true
	 * - cert_type - SSL cert type to use - defaults to PEM
	 * - cert      - SSL Certificate to use for request - required
	 * - version   - SSL version to use - optional (take caution)
	 *
	 * GLOBAL
	 * ------
	 * - cookie          - cookies you want to attach to the request	
	 * - sessionInCookie - set to true if you want session data to be appended in cookie array
	 * - mode            - native, json, jsonp
	 *
	 * @param string  $url         URL to request
	 * @param string  $request     Which type of request to peform i.e. GET, POST, PUT, DELETE, HEAD
	 * @param mixed[] $postFields  Fields you want to send with a POST request (Specifically for POST)
	 * @param array   $headers     Headers you want to attach to the request
	 *
	 * @return string JSON or JSONP encoded string containing page response data
	 */
	public function performRequest($url, $requestType = 'GET', $postFields = array(), $headers = array()){	
		// populate header options which have not been filled
		$this->populateHeaderOptions($headers);	
		// validate return type if one has been provided
		$this->throwErrorIfInvalidReturnType($headers['mode']);
		// if url is invalid
		if(!$this->isValidUrl($url)) {	
			// throw invalid url exception		
			$this->throwError('URL provided is not valid');
		}
		// initialize curl request for given url
		$curlResource = curl_init($url);	
		// set type of request 
		$this->configureRequestType($requestType, $curlResource);
		// set post fields if type of request is post
	  	$this->configureRequestFieldsIfPost($requestType, $postFields, $curlResource);
		// set cookies to use in request
		$this->configureRequestCookies($headers['cookies'], $headers['sessionInCookie'], $curlResource);			
		// configure ssl settings for request
		$this->configureSSLForRequest($headers['ssl'], $curlResource);
		// configure redirect settings for request
		$this->configureRedirectsForRequest($headers['followRedirects'], $headers['maxRedirects'], $curlResource);
		// configure header settings for request
		$this->configureRequestHeaders($headers['raw'], $requestType, $curlResource);
		// data will always be returned
		curl_setopt($curlResource, CURLOPT_RETURNTRANSFER, true);
		// extracting page headers and content from page response
		$pageResponse = curl_exec($curlResource);
		list($pageHeaders, $pageContents) = $this->extractPageHeadersContent($pageResponse);	
		// extracting status object
		$status = curl_getinfo($curlResource); 
		// disposing connection object
		curl_close($curlResource);
		// outputting response for page request
		return $this->outputPageResponse($pageHeaders, $pageContents, $status, $headers);
	}
	
	// }}}	 
}
?>
