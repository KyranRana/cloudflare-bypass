<?php

namespace CloudflareBypass\Tests;

use CloudflareBypass\RequestMethod\CFStream;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class GuzzleHttpTest extends TestCase
{
    /**
     * Retrieve the client
     *
     * @return Client
     */
    public function getClient()
    {   
        $opts = [
            'headers' => array(
                'User-Agent' => $this->getAgent()
            ),
            'verify'        => false,
            'curl'          => $this->getCurlOptions(),
            'proxy'         => 'tcp://'.$this->getProxyServer(),
            'http_errors'   => false,
            'debug'         => false
        ];

        $client = new Client( $opts );

        return $client;
    }

    /**
     * Test 503 (without bypass)
     *
     * @return void
     */
    public function test503()
    {
        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Make sure each site is protected by CF.
            $response = $client->request( 'GET', $url, [] );

            $this->assertEquals( $url.": "."503", $url.": ".$response->getStatusCode() );
        }
    }

    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200WithCache() 
    {
         // Initialize CFStream.
        $stream_cf_wrapper = new CFStream(array(
            'cache'         => true,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => true
        ));
      
        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Parse url into components.
            $url_components = parse_url($url);

            $cache_file = __DIR__ . "/../var/cache/" . md5( $url_components['host'] );

            $opts = $this->getOptions();

            $opts['http']['header'][]   = "accept: */*";
            $opts['http']['header'][]   = "host: " . $url_components['host'];

            // Bypass each site using CFStream wrapper.
            $stream     = $stream_cf_wrapper->contextCreate( $url, stream_context_create( $opts ) );
            $opts       = stream_context_get_options( $stream );

            $cookies_header   = $this->getCookieHeader( $opts['http']['header'] );
            $cookies          = $this->getCookiesAsArray( $cookies_header );

            $cookie_jar = CookieJar::fromArray( $cookies, $url_components['host'] );

            $response   = $client->request( 'GET', $url, [
                'cookies' => $cookie_jar,
            ] );

            $this->assertEquals( $url.": "."200", $url.": ".$response->getStatusCode() );

            $this->assertEquals( true, file_exists( $cache_file ) );
            $this->assertEquals( true, strpos( file_get_contents( $cache_file ), "cf_clearance") );

            // Remove the file from cache.
            unlink($cache_file);
        }
    }


    /**
     * Test 200 (with bypass)
     *
     * @return void
     */
    public function test200WithNoCache()
    {
        // Initialize CFStream.
        $stream_cf_wrapper = new CFStream(array(
            'cache'         => false,
            'cache_path'    => __DIR__."/../var/cache",
            'verbose'       => true
        ));

        $client = $this->getClient();

        foreach ($this->urls as $url) {
            // Parse url into components.
            $url_components = parse_url( $url );

            $opts = $this->getOptions();

            $opts['http']['header'][]   = "accept: */*";
            $opts['http']['header'][]   = "host: " . $url_components['host'];

            // Bypass each site using CFStream wrapper.
            $stream     = $stream_cf_wrapper->contextCreate( $url, stream_context_create( $opts ) );
            $opts       = stream_context_get_options( $stream );

            $cookies_header   = $this->getCookieHeader( $opts['http']['header'] );
            $cookies          = $this->getCookiesAsArray( $cookies_header );

            $cookie_jar = CookieJar::fromArray( $cookies, $url_components['host'] );

            $response   = $client->request( 'GET', $url, [
                'cookies' => $cookie_jar,
            ] );

            // Get cache file (path included).
            $cache_file = __DIR__ . '/../var/cache/' . md5( $url_components['host'] );

            $this->assertEquals( $url.": "."200", $url.": ".$response->getStatusCode() );
            $this->assertEquals( false, file_exists( $cache_file ) );
        }
    }


    /**
     * Get cookies header. 
     *
     * @access private
     * @param mixed $headers  Request headers.
     * @return string  Cookies header.
     */
    private function getCookieHeader( $headers ) {
        if (isset( $headers['cookie'] )) {
            return $headers['cookie'];
        }

        if (!is_array( $headers )) {
            $headers = preg_split( '/\r\n|\n/', $headers );
        }

        foreach ($headers as $header) {
            if (stripos( $header, 'cookie' ) !== false) {
                list( $_, $cookie_value ) = explode( ':', $header );

                return trim( $cookie_value );
            }
        }

        return null;
    }


    /**
     * Get cookies as array.
     *
     * @access private
     * @param string $cookie  Cookie header.
     * @return array  Cookies as array.
     */
    private function getCookiesAsArray( $cookie_string ) {
        $cookies        = explode( ';', $cookie_string );
        $new_cookies    = [];

        foreach ($cookies as $cookie) {
            list( $cookie_name, $cookie_value ) = explode( '=', $cookie );

            $new_cookies[strtolower( $cookie_name )] = strtolower( $cookie_value );
        }

        return $new_cookies;
    }
}
