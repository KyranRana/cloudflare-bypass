v3.3.0

- Scrapped logic which orders request headers and added check into CFCurl to ensure correct headers are provided for bypass.
- Removed CaptchaPage classes and moved exception handling for captcha into CFCurl
- CFCurl flow improvements

v3.2.0-3.2.1

- Added logic to order request headers when bypassing using cURL
- Fixed bug in function which extracts cURL headers from `curl_getinfo`
- Added retry capabilities if UAM page pops up after requesting clearance
    - Still need to implement a retry limit.

v3.1.0-3.1.4

- Added support for old IUAM challenge page.
- Refactored CFCurl to send general request headers first. 

v3.0.0

- Refactored Cloudflare Bypass again due to an update breaking previous versions.
- Added support for cURL natively. Included example in README.

v2.1.0 (broken)

- Added extra challenge parameter 's' to bypass code.

v2.0.0 (broken)

- Refactored Cloudflare Bypass to include cURL and stream context support.
- Separated bypass code from request code. v1.0.0 including everything into a single class.
- Added support for caching.
- Added support for verbosity.

v1.0.0 (broken)
- Initial version of Cloudflare Bypass
