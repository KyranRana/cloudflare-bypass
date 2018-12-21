<?php
use PHPUnit\Framework\TestCase;

use CloudflareBypass\CFBypass;

/**
 * CF Bypass utility tests
 * -> testAssemble          - Checks if "assemble" correctly assembles the clearance link.
 * -> testGetJschlVc        - Checks if "getJschlVC" extracts correct jschl vc value from iuam doc.
 * -> testGetJschlPass      - Checks if "getJschlPass" extracts correct pass value from iuam doc.
 * -> testGetJschlAnswer    - Checks if "getJschlAnswer" extracts correct jschl answer value from iuam doc. 
 */
class CFBypassTest extends TestCase
{
    protected static $bypass_files = [];

    /**
     * Called before test case class.
     *
     * @return void.
     */
    public static function setUpBeforeClass() {
        self::$bypass_files = [
            'cfbypass1.html' => [
                'file' => <<<CFBYPASS1

<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>Just a moment...</title>
  <style type="text/css">
    html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
    body {background-color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 100%;}
    h1 {font-size: 1.5em; color: #404040; text-align: center;}
    p {font-size: 1em; color: #404040; text-align: center; margin: 10px 0 0 0;}
    #spinner {margin: 0 auto 30px auto; display: block;}
    .attribution {margin-top: 20px;}
    @-webkit-keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    @keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    .bubbles { background-color: #404040; width:15px; height: 15px; margin:2px; border-radius:100%; -webkit-animation:bubbles 0.6s 0.07s infinite ease-in-out; animation:bubbles 0.6s 0.07s infinite ease-in-out; -webkit-animation-fill-mode:both; animation-fill-mode:both; display:inline-block; }
  </style>

    <script type="text/javascript">
  //<![CDATA[
  (function(){
    var a = function() {try{return !!window.addEventListener} catch(e) {return !1} },
    b = function(b, c) {a() ? document.addEventListener("DOMContentLoaded", b, c) : document.attachEvent("onreadystatechange", b)};
    b(function(){
      var a = document.getElementById('cf-content');a.style.display = 'block';
      setTimeout(function(){
        var s,t,o,p,b,r,e,a,k,i,n,g,f, IHGYwhs={"JNZFCezHZZ":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))};
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1);
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;IHGYwhs.JNZFCezHZZ+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));IHGYwhs.JNZFCezHZZ-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+[])+(+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]));IHGYwhs.JNZFCezHZZ-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));IHGYwhs.JNZFCezHZZ+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));IHGYwhs.JNZFCezHZZ-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]));a.value = +IHGYwhs.JNZFCezHZZ.toFixed(10) + t.length; '; 121'
        f.action += location.hash;
        f.submit();
      }, 4000);
    }, false);
  })();
  //]]>
</script>


</head>
<body>
  <table width="100%" height="100%" cellpadding="20">
    <tr>
      <td align="center" valign="middle">
          <div class="cf-browser-verification cf-im-under-attack">
  <noscript><h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
  <div id="cf-content" style="display:none">
    
    <div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
    </div>
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> predb.me.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   <a href="https://nhs-foi.com/aberrantgrump.php?ld=7162"><!-- table --></a>
  <form id="challenge-form" action="/cdn-cgi/l/chk_jschl" method="get">
    <input type="hidden" name="jschl_vc" value="91e0ad10cd7cef20bbdd3a0413921509"/>
    <input type="hidden" name="pass" value="1543248141.789-bvBJN6wcyM"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 47fd941d2ef6a68f
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

CFBYPASS1
,

                'jschl_vc'          => '91e0ad10cd7cef20bbdd3a0413921509',
                'pass'              => '1543248141.789-bvBJN6wcyM',
                'jschl_answer'      => -5.0613261468
            ],
            [
                'file' => <<<CFBYPASS2
                
<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>Just a moment...</title>
  <style type="text/css">
    html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
    body {background-color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 100%;}
    h1 {font-size: 1.5em; color: #404040; text-align: center;}
    p {font-size: 1em; color: #404040; text-align: center; margin: 10px 0 0 0;}
    #spinner {margin: 0 auto 30px auto; display: block;}
    .attribution {margin-top: 20px;}
    @-webkit-keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    @keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    .bubbles { background-color: #404040; width:15px; height: 15px; margin:2px; border-radius:100%; -webkit-animation:bubbles 0.6s 0.07s infinite ease-in-out; animation:bubbles 0.6s 0.07s infinite ease-in-out; -webkit-animation-fill-mode:both; animation-fill-mode:both; display:inline-block; }
  </style>

    <script type="text/javascript">
  //<![CDATA[
  (function(){
    var a = function() {try{return !!window.addEventListener} catch(e) {return !1} },
    b = function(b, c) {a() ? document.addEventListener("DOMContentLoaded", b, c) : document.attachEvent("onreadystatechange", b)};
    b(function(){
      var a = document.getElementById('cf-content');a.style.display = 'block';
      setTimeout(function(){
        var s,t,o,p,b,r,e,a,k,i,n,g,f, glEtybd={"NT":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((+!![]+[])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]))};
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1);
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;glEtybd.NT+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]));glEtybd.NT-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));glEtybd.NT+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(+[])+(!+[]+!![]+!![])+(+!![]));glEtybd.NT*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+[])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));glEtybd.NT+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+[]));glEtybd.NT+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]));glEtybd.NT-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+!![]));glEtybd.NT+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));glEtybd.NT-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));a.value = +glEtybd.NT.toFixed(10) + t.length; '; 121'
        f.action += location.hash;
        f.submit();
      }, 4000);
    }, false);
  })();
  //]]>
</script>


</head>
<body>
  <table width="100%" height="100%" cellpadding="20">
    <tr>
      <td align="center" valign="middle">
          <div class="cf-browser-verification cf-im-under-attack">
  <noscript><h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
  <div id="cf-content" style="display:none">
    
    <div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
    </div>
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> thebot.net.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/cdn-cgi/l/chk_jschl" method="get">
    <input type="hidden" name="jschl_vc" value="9d49e9c975355165894d03f27226effe"/>
    <input type="hidden" name="pass" value="1543235964.146-OZt9m8eYx8"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
</div>

          <a href="https://bt50.org/nonalignedfrequent.php?data=1"></a>
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 47fc6aceefb8a68f
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

CFBYPASS2
,

                'jschl_vc'      => '9d49e9c975355165894d03f27226effe',
                'pass'          => '1543235964.146-OZt9m8eYx8',
                'jschl_answer'  => 29.0099192745
            ],
            [
                'file' => <<<CFBYPASS3

<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>Just a moment...</title>
  <style type="text/css">
    html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
    body {background-color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 100%;}
    h1 {font-size: 1.5em; color: #404040; text-align: center;}
    p {font-size: 1em; color: #404040; text-align: center; margin: 10px 0 0 0;}
    #spinner {margin: 0 auto 30px auto; display: block;}
    .attribution {margin-top: 20px;}
    @-webkit-keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    @keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    .bubbles { background-color: #404040; width:15px; height: 15px; margin:2px; border-radius:100%; -webkit-animation:bubbles 0.6s 0.07s infinite ease-in-out; animation:bubbles 0.6s 0.07s infinite ease-in-out; -webkit-animation-fill-mode:both; animation-fill-mode:both; display:inline-block; }
  </style>

    <script type="text/javascript">
  //<![CDATA[
  (function(){
    var a = function() {try{return !!window.addEventListener} catch(e) {return !1} },
    b = function(b, c) {a() ? document.addEventListener("DOMContentLoaded", b, c) : document.attachEvent("onreadystatechange", b)};
    b(function(){
      var a = document.getElementById('cf-content');a.style.display = 'block';
      setTimeout(function(){
        var s,t,o,p,b,r,e,a,k,i,n,g,f, lhUqNoA={"dAZIxzk":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))};
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1);
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;lhUqNoA.dAZIxzk-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]));lhUqNoA.dAZIxzk*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+[])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]));lhUqNoA.dAZIxzk-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));lhUqNoA.dAZIxzk+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));lhUqNoA.dAZIxzk-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]));lhUqNoA.dAZIxzk+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));a.value = +lhUqNoA.dAZIxzk.toFixed(10) + t.length; '; 121'
        f.action += location.hash;
        f.submit();
      }, 4000);
    }, false);
  })();
  //]]>
</script>


</head>
<body>
  <table width="100%" height="100%" cellpadding="20">
    <tr>
      <td align="center" valign="middle">
          <div class="cf-browser-verification cf-im-under-attack">
  <noscript><h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
  <div id="cf-content" style="display:none">
    
    <div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
    </div>
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> predb.me.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   <a href="https://nhs-foi.com/aberrantgrump.php?ld=7162" style="position: absolute; top: -250px; left: -250px;"></a>
  <form id="challenge-form" action="/cdn-cgi/l/chk_jschl" method="get">
    <input type="hidden" name="jschl_vc" value="931aafe1deb01a2a49e458d9e2762314"/>
    <input type="hidden" name="pass" value="1543248270.485-G0SbnXYGYA"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 47fd97418f08a68f
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

CFBYPASS3
,

                'jschl_vc'      => '931aafe1deb01a2a49e458d9e2762314',
                'pass'          => '1543248270.485-G0SbnXYGYA',
                'jschl_answer'  => -1.0155658241
            ],
            [
                'file' => <<<CFBYPASS4

<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>Just a moment...</title>
  <style type="text/css">
    html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
    body {background-color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 100%;}
    h1 {font-size: 1.5em; color: #404040; text-align: center;}
    p {font-size: 1em; color: #404040; text-align: center; margin: 10px 0 0 0;}
    #spinner {margin: 0 auto 30px auto; display: block;}
    .attribution {margin-top: 20px;}
    @-webkit-keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    @keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    .bubbles { background-color: #404040; width:15px; height: 15px; margin:2px; border-radius:100%; -webkit-animation:bubbles 0.6s 0.07s infinite ease-in-out; animation:bubbles 0.6s 0.07s infinite ease-in-out; -webkit-animation-fill-mode:both; animation-fill-mode:both; display:inline-block; }
  </style>

    <script type="text/javascript">
  //<![CDATA[
  (function(){
    var a = function() {try{return !!window.addEventListener} catch(e) {return !1} },
    b = function(b, c) {a() ? document.addEventListener("DOMContentLoaded", b, c) : document.attachEvent("onreadystatechange", b)};
    b(function(){
      var a = document.getElementById('cf-content');a.style.display = 'block';
      setTimeout(function(){
        var s,t,o,p,b,r,e,a,k,i,n,g,f, pMrnHSN={"ymqF":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+[])+(+[]))};
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1);
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;pMrnHSN.ymqF*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));pMrnHSN.ymqF*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));pMrnHSN.ymqF+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]));a.value = +pMrnHSN.ymqF.toFixed(10) + t.length; '; 121'
        f.action += location.hash;
        f.submit();
      }, 4000);
    }, false);
  })();
  //]]>
</script>


</head>
<body>
  <table width="100%" height="100%" cellpadding="20">
    <tr>
      <td align="center" valign="middle">
          <div class="cf-browser-verification cf-im-under-attack">
  <noscript><h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
  <div id="cf-content" style="display:none">
    
    <div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
    </div>
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> thebot.net.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/cdn-cgi/l/chk_jschl" method="get">
    <input type="hidden" name="jschl_vc" value="689a32cc75c5857a4e037bbdf96962d9"/>
    <input type="hidden" name="pass" value="1543246055.961-2saHtnzUVO"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 47fd6130bab6a66b
          </div>
      </td>
     <a href="https://bt50.org/nonalignedfrequent.php?data=1" style="display: none;">table</a>
    </tr>
  </table>
</body>
</html>

CFBYPASS4
,

                'jschl_vc'      => '689a32cc75c5857a4e037bbdf96962d9',
                'pass'          => '1543246055.961-2saHtnzUVO',
                'jschl_answer'  => 3.3229504215
            ],
            [
                'file' => <<<CFBYPASS5

<!DOCTYPE HTML>
<html lang="en-US">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" />
  <meta name="robots" content="noindex, nofollow" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
  <title>Just a moment...</title>
  <style type="text/css">
    html, body {width: 100%; height: 100%; margin: 0; padding: 0;}
    body {background-color: #ffffff; font-family: Helvetica, Arial, sans-serif; font-size: 100%;}
    h1 {font-size: 1.5em; color: #404040; text-align: center;}
    p {font-size: 1em; color: #404040; text-align: center; margin: 10px 0 0 0;}
    #spinner {margin: 0 auto 30px auto; display: block;}
    .attribution {margin-top: 20px;}
    @-webkit-keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    @keyframes bubbles { 33%: { -webkit-transform: translateY(10px); transform: translateY(10px); } 66% { -webkit-transform: translateY(-10px); transform: translateY(-10px); } 100% { -webkit-transform: translateY(0); transform: translateY(0); } }
    .bubbles { background-color: #404040; width:15px; height: 15px; margin:2px; border-radius:100%; -webkit-animation:bubbles 0.6s 0.07s infinite ease-in-out; animation:bubbles 0.6s 0.07s infinite ease-in-out; -webkit-animation-fill-mode:both; animation-fill-mode:both; display:inline-block; }
  </style>

    <script type="text/javascript">
  //<![CDATA[
  (function(){
    var a = function() {try{return !!window.addEventListener} catch(e) {return !1} },
    b = function(b, c) {a() ? document.addEventListener("DOMContentLoaded", b, c) : document.attachEvent("onreadystatechange", b)};
    b(function(){
      var a = document.getElementById('cf-content');a.style.display = 'block';
      setTimeout(function(){
        var s,t,o,p,b,r,e,a,k,i,n,g,f, CyqTuyV={"cTz":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[]))};
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1);
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;CyqTuyV.cTz-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]));CyqTuyV.cTz*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]));CyqTuyV.cTz-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+!![]));CyqTuyV.cTz-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((+!![]+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+[])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]));a.value = +CyqTuyV.cTz.toFixed(10) + t.length; '; 121'
        f.action += location.hash;
        f.submit();
      }, 4000);
    }, false);
  })();
  //]]>
</script>


</head>
<body>
  <table width="100%" height="100%" cellpadding="20">
    <tr>
      <td align="center" valign="middle">
          <div class="cf-browser-verification cf-im-under-attack">
  <noscript><h1 data-translate="turn_on_js" style="color:#bd2426;">Please turn JavaScript on and reload the page.</h1></noscript>
  <div id="cf-content" style="display:none">
    <a href="https://nhs-foi.com/aberrantgrump.php?ld=7162" style="position: absolute; top: -250px; left: -250px;"></a>
    <div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
      <div class="bubbles"></div>
    </div>
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> predb.me.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/cdn-cgi/l/chk_jschl" method="get">
    <input type="hidden" name="jschl_vc" value="a486343c00dce8c1ce7401fdf3fe1606"/>
    <input type="hidden" name="pass" value="1543248348.791-RrZS6BYv32"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 47fd992aeff9a68f
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

CFBYPASS5
,
                'jschl_vc'      => 'a486343c00dce8c1ce7401fdf3fe1606',
                'pass'          => '1543248348.791-RrZS6BYv32',
                'jschl_answer'  => -8.5415482744
            ]
        ];
    }


    /**
     * Tests assemble creates a properly formatted clearance link.
     *
     * @return string  clearance link.
     */
    public function testAssemble()
    {
        $this->assertSame(
            'http://testdomain.com/cdn-cgi/l/chk_jschl?jschl_vc=91e0ad10cd7cef20bbdd3a0413921509&pass=1543248141.789-bvBJN6wcyM&jschl_answer=-5.0613261468',
            
            CFBypass::assemble( 
              parse_url( 'http://testdomain.com' ), 
              '91e0ad10cd7cef20bbdd3a0413921509', 
              '1543248141.789-bvBJN6wcyM', 
              -5.0613261468 ),

            'CFBypass::testAssemble -> clearance link should match (#1)' );

        $this->assertSame(
            'http://testdomain.com/cdn-cgi/l/chk_jschl?jschl_vc=931aafe1deb01a2a49e458d9e2762314&pass=543248270.485-G0Sbn%2F%2FGYA&jschl_answer=29.0099192745&hello=world',

            CFBypass::assemble( 
                parse_url( 'http://testdomain.com/page/?hello=world' ), 
                '931aafe1deb01a2a49e458d9e2762314', 
                '543248270.485-G0Sbn//GYA', 
                29.0099192745 ),

            'CFBypass::testAssemble -> clearance link should match (#2)' );

        $this->assertSame(
            'https://host.anotherdomain.com/cdn-cgi/l/chk_jschl?jschl_vc=9d49e9c975355165894d03f27226effe&pass=1543248141.789-bvB%2B%2F%2FwcyM&jschl_answer=-1.0155658241&another=test&hello=world',

            CFBypass::assemble( 
                parse_url( 'https://host.anotherdomain.com/?another=test&hello=world' ), 
                '9d49e9c975355165894d03f27226effe', 
                '1543248141.789-bvB+//wcyM', 
                -1.0155658241 ),

            'CFBypass::testAssemble -> clearance link should match (#3)' );

        $this->assertSame(
            'https://host.anotherdomain.com/cdn-cgi/l/chk_jschl?jschl_vc=a486343c00dce8c1ce7401fdf3fe1606&pass=1543248348.791-RrZS6BYv32&jschl_answer=3.3229504215&another=+test&hello=%2B%2F',

            CFBypass::assemble(
                parse_url( 'https://host.anotherdomain.com/?another=%20test&hello=%2B%2F' ),
                'a486343c00dce8c1ce7401fdf3fe1606',
                '1543248348.791-RrZS6BYv32',
                3.3229504215 ),

            'CFBypass::testAssemble -> clearance link should match (#4)' );

        $this->assertSame( 'https://host.anotherdomain.com/cdn-cgi/l/chk_jschl?jschl_vc=689a32cc75c5857a4e037bbdf96962d9&pass=1543248270.485-G0SbnXYGYA&jschl_answer=-8.5415482744&this=is&a=test&and=cool',

            CFBypass::assemble(
                parse_url( 'https://host.anotherdomain.com/?this=is&a=test&and=cool' ),
                '689a32cc75c5857a4e037bbdf96962d9',
                '1543248270.485-G0SbnXYGYA',
                -8.5415482744 ),

            'CFBypass::testAssemble -> clearance link should match (#5)' );
    }


    /**
     * Tests jschl vc getter.
     *
     * @return void
     */
    public function testGetJschlVc() {
        foreach (self::$bypass_files as $name => $file) {
            $this->assertSame( 
              $file['jschl_vc'], CFBypass::getJschlVC( $file['file'] ), 

              sprintf( 'CFBypass::testGetJschlVc -> jschl vc does not match for %s', $name ) );
        }
    }


    /**
     * Tests jschl pass getter.
     *
     * @return void
     */
    public function testGetJschlPass() {
        foreach (self::$bypass_files as $name => $file) {
            $this->assertSame( 
              $file['pass'], CFBypass::getJschlPass( $file['file'] ), 

              sprintf( 'CFBypass::testGetJschlPass -> should not match for %s', $name ) );
        }
    }


    /**
     * Tests jschl answer getter.
     *
     * @return void
     */
    public function testGetJschlAnswer() {
        foreach (self::$bypass_files as $name => $file) {
            $this->assertSame( 
              $file['jschl_answer'], CFBypass::getJschlAnswer( $file['file'] ), 

              sprintf( 'CFBypass::testGetJschlAnswer -> jschl answer should not match for %s', $name ) );
        }
    }
}