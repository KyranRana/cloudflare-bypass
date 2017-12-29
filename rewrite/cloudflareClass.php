<?php
/**
 * cloudflare bypass v2.0
 *
 * A simple class which is intended to help you bypass the CloudFlare UAM page (Under Attack Mode) 
 * without the extra dependencies :)
 *
 * This is a rewrite of the original I programmed 2 years ago. This version should hopefully be
 * more light-weight than the first, easy to integrate, and more configurable for one-time requests.
 *
 */
class cloudflare 
{
	/**
	 * list of random user agents to use for requests
	 * @var array
	 */
	private static $userAgents = array(
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.5; rv:2.0.1) Gecko/20100101 Firefox/4.0.1 Camino/2.2.1",
		"Mozilla/5.0 (Windows NT x.y; rv:10.0) Gecko/20100101 Firefox/10.0",
		"Mozilla/5.0 (Windows NT x.y; Win64; x64; rv:10.0) Gecko/20100101 Firefox/10.0",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X x.y; rv:10.0) Gecko/20100101 Firefox/10.0",
	);

	/**
	 * _getRandomUserAgent - gets random user agent from list of available user agents
	 *
	 * @return <string> 	user agent
	 */
	private static function _getRandomUserAgent()
		{ return self::$userAgents[mt_rand( 0, count(self::$userAgents)-1 )]; }

	/**
	 * _isUserAgentSetForCurlHandle - checks if user agent is set in cURL resource
	 *
	 * @param <resource> 	$curlHandle
	 *
	 * @return <bool>	true if user agent is set
	 */
	private static function _isUserAgentSetForCurlHandle( $curlHandle ) 
	{
		# perform test request
		# assume url is protected by cloudflare and options are set
		curl_exec( $curlHandle );

		# extract request headers
		$headers = curl_getinfo( $curlHandle );

		# check if user agent is set
		return ( stripos( $headers['request_headers'], 'User-Agent' ) !== false );
	}

	/**
	 * assignClearanceCookieToCurlHandle - Assigns clearance cookie directly to cURL handle
	 *
	 * for convience also returns the user agent, clearance tokens, and their expiry date
	 *
	 * NOTE: CURLOPT_VERBOSE, CURLOPT_HEADER_OUT, and CURLOPT_RETURNTRANSFER will temporarily 
	 * be set to TRUE to ensure the user agent string has been set. Since there is no logical 
	 * way to check the previous values assigned to these options, 'verbose_flag_set',
	 * 'headerout_flag_set', 'returntransfer_set' have been added as extra keys to $config.
	 *
	 * @param <resource> 	$curlHandle 
	 * @param <array> 	$config
	 * - verbose_flag_set 	=> <bool> CURLOPT_VERBOSE already set ?
	 * - returntransfer_set => <bool> CURLOPT_RETURNTRANSFER already set ?
	 * - headerout_flag_set => <bool> CURLINFO_HEADER_OUT already set ?
	 * - random_user_agent 	=> <bool> use a random user agent for CURLOPT_USERAGENT ?
	 *
	 * @return <array> 
	 * - <string> 	"clearance" token
	 * - <string> 	"__cfuid" token
	 * - <string> 	user agent
	 * - <integer> 	expiry date of pair
	 */
	public static function assignClearanceCookieToCurlHandle( $curlHandle, $config = [] )
	{
		# set verbose flag if not set
		if( !isset($config['verbose_flag_set']) && !$config['verbose_flag_set'] )
			curl_setopt( $curlHandle, CURLOPT_VERBOSE, true );

		# set header out flag if not set
		if( !isset($config['headerout_flag_set']) && !$config['headerout_flag_set'] )
			curl_setopt( $curlHandle, CURLINFO_HEADER_OUT, true );

		# set returntransfer flag if not set
		if( !isset($config['returntransfer_set']) && !$config['returntransfer_set'] )
			curl_setopt( $curlHandle, CURLOPT_RETURNTRANSFER, true );

		# assign random user agent to cURL handle if requested
		if( !isset($config['random_user_agent']) && !$config['random_user_agent'] )
			curl_setopt( $curlHandle, CURLOPT_USERAGENT, self::_getRandomUserAgent() );
		# OR validate if a user agent has been set in cURL handle
		else if(!self::_isUserAgentSetForCurlHandle( $curlHandle ))
			throw new Exception("CURLOPT_USERAGENT needs to be set!");
		
	}

}
