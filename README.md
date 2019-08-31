# Cloudflare Bypass

A new and improved PHP library which bypasses the Cloudflare IUAM page using cURL.


#### Installation

With composer:

`composer require kyranrana/cloudflare-bypass`


#### Usage with cURL

Use cURL how you normally would but instead of using `curl_exec` to execute the 
request we provide a class called `CFCurlImpl`. `CFCurlImpl` provides an `exec` method which takes your cURL handle and 
executes it - handling the IUAM page if it appears.

  
Method definition:
  
```

CFCurlImpl->exec(resource $curlHandle, UAMOptions $uamOptions)

    Parameters:
        $curlHandle -> cURL Handle
        $uamOptions -> Extra UAM options
        
            -> getDelay()
            -> setDelay(int delay)                  // number of seconds to wait before requesting clearance
            
            -> isVerbose()
            -> setVerbose(bool verbose)             // TRUE to display extra debug about the bypass process
            
            -> getExtraHeaders()
            -> setExtraHeaders(array headers)       // Use this option to specify extra headers instead of CURLOPT_HTTPHEADER

``` 


Example:

```php
<?php
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
```