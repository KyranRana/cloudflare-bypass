<?php

namespace CloudflareBypass\Model\UAM;

use SimpleJavaScriptCompilation\DeclarationInterpreterImpl;
use SimpleJavaScriptCompilation\ExpressionInterpreterImpl;
use SimpleJavaScriptCompilation\Model\Context;
use SimpleJavaScriptCompilation\Model\DataType;
use SimpleJavaScriptCompilation\Model\DataType\CustomString;

/**
 * Class UAMPageFormParams
 *      - UAM page form parameters.
 *      - Part of UAMPageAttributes model.
 *
 * @package CloudflareBypass\Model\UAM
 * @author Kyran Rana
 */
class UAMPageFormParams
{
    /**
     * Gets all input parameter values from UAM page.
     *
     * @param UAMPageAttributes $pageAttributes UAM page attributes
     * @return UAMPageFormParams UAM page form params.
     * @throws \ErrorException if JS evaluation fails
     */
    public static function getParamsFromPage(UAMPageAttributes $pageAttributes): UAMPageFormParams
    {
        $page = $pageAttributes->getPage();

        preg_match('/name="r" value="([^"]*)"/', $page, $sMatches);
        preg_match('/name="jschl_vc" value="([^"]+)"/', $page, $jschlVcMatches);
        preg_match('/name="pass" value="([^"]+)"/', $page, $passMatches);
        preg_match('/action="([^"]+)"/', $page, $urlMatches);

        return new UAMPageFormParams(
            $sMatches[1],
            $jschlVcMatches[1],
            $passMatches[1],
            self::getJschlAnswerFromPage($pageAttributes),
            $urlMatches[1]
        );
    }

    /**
     * Gets answer to JavaScript challenge.
     *
     * @param UAMPageAttributes $pageAttributes UAM page attributes.
     * @return string Answer to JavaScript challenge
     * @throws \ErrorException if JS evaluation fails
     */
    public static function getJschlAnswerFromPage(UAMPageAttributes $pageAttributes): string
    {
        $codeSnippets = UAMPageChallengeCode::getSnippetsFromPage($pageAttributes->getPage());

        $ctx = new Context();
        $ctx->setCtxFunc('e', 'SimpleJavaScriptCompilation\Model\FunctionMap\GlobalFunctionMap::atob');
        $ctx->setCtxFunc('g', 'SimpleJavaScriptCompilation\Model\FunctionMap\GlobalFunctionMap::stringFromCharCode');
        $ctx->setCtxVar('t', new CustomString(new DataType(['value' => '"' . $pageAttributes->getHost() . '"'])));

        if ($codeSnippets->getSecondaryChallengeCode() !== "") {
            $ctx->setCtxVar('k', ExpressionInterpreterImpl::instance()->interpretExpression($codeSnippets->getSecondaryChallengeCode(), new Context()));
        }

        $ctx = DeclarationInterpreterImpl::instance()->interpretDeclarations($codeSnippets->getChallengeCode(), $ctx);
        return substr($ctx->getCtxVar("answer")->getDataType()->getValue(), 1, -1);
    }

    // -------------------------------------------------------------------------------------------------------

    /**
     * R param
     *
     * @var string $r
     */
    private $r;

    /**
     * JSCHL VC param
     *
     * @var string $jschlVc
     */
    private $jschlVc;

    /**
     * PASS param
     *
     * @var string $pass
     */
    private $pass;

    /**
     * JSCHL answer param
     *
     * @var string $jschlAnswer
     */
    private $jschlAnswer;

    /**
     * FORM ACTION param
     *
     * @var string $action
     */
    private $action;

    public function __construct(string $r, string $jschlVc, string $pass, string $jschlAnswer, string $action)
    {
        $this->r                = $r;
        $this->jschlVc          = $jschlVc;
        $this->pass             = $pass;
        $this->jschlAnswer      = $jschlAnswer;
        $this->action           = $action;
    }

    /**
     * Gets R param
     *
     * @return string R param
     */
    public function getR(): string
    {
        return $this->r;
    }

    /**
     * Gets form action param
     *
     * @return string form action param
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Gets JSCHL VC param.
     *
     * @return string JSCHL VC param
     */
    public function getJschlVc(): string
    {
        return $this->jschlVc;
    }

    /**
     * Gets PASS param
     *
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * Gets JSCHL ANSWER param.
     *
     * @return string
     */
    public function getJschlAnswer(): string
    {
        return $this->jschlAnswer;
    }

    /**
     * Gets query string.
     *
     * @return string Query string.
     */
    public function getQueryString(): string
    {
        return http_build_query([
            'r'             => $this->getR(),
            'jschl_vc'      => $this->getJschlVc(),
            'pass'          => $this->getPass(),
            'jschl_answer'  => $this->getJschlAnswer()
        ]);
    }
}