<?php

namespace CloudflareBypass\Model\UAM;

/**
 * Class UAMPageChallengeCode
 *      - Contains UAM page challenge codes.
 *      - Part of UAMPageFormParams model.
 *
 * @package CloudflareBypass\Model
 * @author Kyran Rana
 */
class UAMPageChallengeCode
{
    /**
     * Gets code snippets from UAM page and creates a <pre>UAMPageChallengeCode</pre> object.
     *
     * @param string $page UAM page
     * @return UAMPageChallengeCode Code snippets
     */
    public static function getSnippetsFromPage(string $page): UAMPageChallengeCode
    {
        $challengeCode          = self::getChallengeCodeFromPage($page);
        $secondaryChallengeCode = self::getSecondaryChallengeCodeFromPage($page, $challengeCode);

        $challengeCode = preg_replace("/k = '([^']+)';/", "", $challengeCode);
        $challengeCode = str_replace("\n", "", $challengeCode);
        $challengeCode = preg_replace("/; +;/", ";", $challengeCode);

        return new UAMPageChallengeCode($challengeCode, $secondaryChallengeCode);
    }

    /**
     * Gets challenge code from UAM page.
     *
     * @param string $page UAM challenge page
     * @return string UAM challenge page code
     */
    public static function getChallengeCodeFromPage(string $page): string
    {
        $code = substr($page, strpos($page, 'setTimeout(function(){') + 22);
        $code = trim(substr($code, 0, strpos($code, "}, 4000);")));

        $code = str_replace("var s,t,o,p,b,r,e,a,k,i,n,g,f, ", "", $code);
        $code = preg_replace("/e = function\(s\) {(.|\\n)+?};/", "", $code);
        $code = preg_replace("/(t|a|f|r|o|g) = [^;]+;/", "", $code);
        $code = str_replace("a.value", "answer", $code);
        $code = preg_replace('/t\.innerHTML="[^"]+";/', "", $code);
        $code = str_replace(["f.action += location.hash;", "f.submit()"], "", $code);
        $code = str_replace("'; 121'", "", $code);
        preg_match("/(\w+)={\"([^\"]+)\":/", $code, $matches);

        $code = preg_replace("/(\w+)={\"([^\"]+)\":([^}]+)};/", "answer=$3;", $code);
        $code = str_replace($matches[1] . '.' . $matches[2], "answer", $code);
        $code = trim(str_replace('Function("return escape")()', "escape", $code));

        return $code;
    }

    /**
     * Gets secondary challenge code from UAM page (k).
     *
     * @param string $page UAM page
     * @param string $code UAM page challenge code
     * @return string Secondary challenge code
     */
    public static function getSecondaryChallengeCodeFromPage(string $page, string $code): string
    {
        preg_match('/k = \'([^\']+)\';/', $code, $kMatches);
        preg_match('/id="' . ($kMatches[1] ?? "") . '">([^<]+)</', $page, $kElemMatches);

        return $kElemMatches[1] ?? "";
    }

    // -------------------------------------------------------------------------------------------------------

    /**
     * Main challenge code
     *
     * @var string $challengeCode Main challenge code
     */
    private $challengeCode;

    /**
     * Secondary challenge code (k)
     *
     * @var string $secondaryChallengeCode Secondary c hallenge code
     */
    private $secondaryChallengeCode;

    public function __construct(string $challengeCode, string $secondaryChallengeCode)
    {
        $this->challengeCode          = $challengeCode;
        $this->secondaryChallengeCode = $secondaryChallengeCode;
    }

    /**
     * Gets main challenge code
     *
     * @return string Main challenge code
     */
    public function getChallengeCode(): string
    {
        return $this->challengeCode;
    }

    /**
     * Gets secondary challenge code (k).
     *
     * @return string Secondary challenge code.
     */
    public function getSecondaryChallengeCode(): string
    {
        return $this->secondaryChallengeCode;
    }
}