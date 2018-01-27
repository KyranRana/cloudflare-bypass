<?php
namespace CloudflareBypass;

class Storage
{
    /**
     * Creates Cache/ directory if it does NOT exist
     *
     * @access public
     * @throws \ErrorException if cache directory CAN NOT be created
     */
    public function __construct()
    {
        // Create Cache/ directory if it does not exist.
        $dir = __DIR__ . '/Cache';
        
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777)) {
                throw new \ErrorException('Unable to create Cache directory!');
            }
        }
    }

    /**
     * Returns clearance tokens from the specified cache file.
     *
     * @access public
     * @param $siteHost Site host.
     * @return array Clearance tokens or FALSE
     */
    public function fetch($siteHost)
    {
        // Construct cache file endpoint.
        $filename = __DIR__ . '/Cache/' . md5($siteHost);

        if (file_exists($filename)) {
            return json_decode(file_get_contents($filename), true);
        }

        // Try again?
        if (preg_match('/^www./', $siteHost)) {
            $filename = __DIR__ . '/Cache/' . md5(substr($siteHost, 4));

            if (file_exists($filename)) {
                return json_decode(file_get_contents($filename), true);
            }
        }

        return false;
    }

    /**
     * Stores clearance tokens into a cache file in cache folder.
     *
     * File name:           Data:
     * -------------------------------------------
     * md5( file name )     {"__cfduid":"<cfduid>", "cf_clearance":"<cf_clearance>"}
     *
     * @access public
     * @param string $siteHost site host name.
     * @param array $clearanceTokens Associative array containing "__cfduid" and "cf_clearance" cookies
     * @throws \ErrorException if $siteHost IS empty.
     * @throws \ErrorException if $clearanceTokens IS missing token fields, OR contains rubbish.
     * @throws \ErrorException if file_put_contents FAILS to write to file.
     */
    public function store($siteHost, $clearanceTokens)
    {
        if (trim($siteHost) === "") {
            throw new \ErrorException("Site host should not be empty!");
        }

        if (!(
            is_array($clearanceTokens) && 
            count($clearanceTokens) === 2 &&
            isset($clearanceTokens['__cfduid']) &&
            isset($clearanceTokens['cf_clearance'])
        )) {
            throw new \ErrorException("Clearance tokens not in a valid format!");
        }

        // Construct cache file endpoint.
        $filename = __DIR__ . '/Cache/' . md5($siteHost);

        // Perform data retention duties.
        $this->retention();

        if (!file_put_contents($filename, json_encode($clearanceTokens))) {
            // Remove file if it exists.
            if (file_exists($filename)) {
                unlink($filename);
            }
        }
    }

    /**
     * Deletes files from cache folder which are older than 24 hours.
     *
     * @access private
     */
    private function retention()
    {
        $dir = __DIR__ . '/Cache/';

        if ($handle = opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                // Skip special directories.
                if ('.' === $file || '..' === $file || strpos($file, '.') === 0) {
                    continue;
                }
        
                // Delete file if last accessed over 24 hours ago.
                if (time()-fileatime($dir . $file) > 86400) {
                    unlink($dir . $file);
                }
            }
        }
    }
}