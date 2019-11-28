<?php

use CloudflareBypass\Model\UAM\UAMPageAttributes;
use CloudflareBypass\Model\UAM\UAMPageFormParams;
use PHPUnit\Framework\TestCase;

class UAMPageFormParamsTest extends TestCase
{
    public function getParamsFromPage_testCases(): array
    {
        return [
            [
                (function (): UAMPageAttributes {
                    $page = <<<DOC

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
        var s,t,o,p,b,r,e,a,k,i,n,g,f, piZsIQG={"MUr":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+!![])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]))};
        g = String.fromCharCode;
        o = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        e = function(s) {
          s += "==".slice(2 - (s.length & 3));
          var bm, r = "", r1, r2, i = 0;
          for (; i < s.length;) {
              bm = o.indexOf(s.charAt(i++)) << 18 | o.indexOf(s.charAt(i++)) << 12
                      | (r1 = o.indexOf(s.charAt(i++))) << 6 | (r2 = o.indexOf(s.charAt(i++)));
              r += r1 === 64 ? g(bm >> 16 & 255)
                      : r2 === 64 ? g(bm >> 16 & 255, bm >> 8 & 255)
                      : g(bm >> 16 & 255, bm >> 8 & 255, bm & 255);
          }
          return r;
        };
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1); 
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;piZsIQG.MUr-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]));piZsIQG.MUr-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]));piZsIQG.MUr-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));piZsIQG.MUr-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]));piZsIQG.MUr-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(+[]));piZsIQG.MUr+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]));piZsIQG.MUr*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]));piZsIQG.MUr+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]));piZsIQG.MUr+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![])+(!+[]+!![]+!![]));a.value = (+piZsIQG.MUr + t.length).toFixed(10); '; 121'
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
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> a24.biz.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/login?__cf_chl_jschl_tk__=4de841528b5a33938b9ed640da04c655ca06e947-1574843786-0-AeFe6hbOtL--t-HhrPWR1GD0BRXMUcYg4Sx6jhppfwiWXtT6SmRu49scptUKCYHKfWvTki5ZSQPwhJQBi5pWnOLz95HkCpUVm9-OQFAOLXoYzQ0LfhRsuP_VZ6BqaTU5AvQczS6XMoQ9hrzYI6wl9WsHNtOXthkqpquATqwe1WwjgUDVFg5utnHMJgTVsz9nkhYsBogwVjQLJ3twOeWI0IA" method="POST" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="r" value=""></input>
    <input type="hidden" name="jschl_vc" value="42596b68cad9eb9138d52fc7d2bdc835"/>
    <input type="hidden" name="pass" value="1574843790.299-egULhGNuX4"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
  
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 53c2c6405ecb8f25
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

DOC;

                    return new UAMPageAttributes("https", "a24.biz", $page);
                })(),
                new UAMPageFormParams(
                    '',
                    '42596b68cad9eb9138d52fc7d2bdc835',
                    '1574843790.299-egULhGNuX4',
                    '11.6169183387',
                    '/login?__cf_chl_jschl_tk__=4de841528b5a33938b9ed640da04c655ca06e947-1574843786-0-AeFe6hbOtL--t-HhrPWR1GD0BRXMUcYg4Sx6jhppfwiWXtT6SmRu49scptUKCYHKfWvTki5ZSQPwhJQBi5pWnOLz95HkCpUVm9-OQFAOLXoYzQ0LfhRsuP_VZ6BqaTU5AvQczS6XMoQ9hrzYI6wl9WsHNtOXthkqpquATqwe1WwjgUDVFg5utnHMJgTVsz9nkhYsBogwVjQLJ3twOeWI0IA'
                )
            ],
            [
                (function (): UAMPageAttributes {
                    $page = <<<DOC

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
        var s,t,o,p,b,r,e,a,k,i,n,g,f, pLCKLNx={"CEUKHbwTCG":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]))};
        g = String.fromCharCode;
        o = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        e = function(s) {
          s += "==".slice(2 - (s.length & 3));
          var bm, r = "", r1, r2, i = 0;
          for (; i < s.length;) {
              bm = o.indexOf(s.charAt(i++)) << 18 | o.indexOf(s.charAt(i++)) << 12
                      | (r1 = o.indexOf(s.charAt(i++))) << 6 | (r2 = o.indexOf(s.charAt(i++)));
              r += r1 === 64 ? g(bm >> 16 & 255)
                      : r2 === 64 ? g(bm >> 16 & 255, bm >> 8 & 255)
                      : g(bm >> 16 & 255, bm >> 8 & 255, bm & 255);
          }
          return r;
        };
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1); 
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;pLCKLNx.CEUKHbwTCG*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));a.value = (+pLCKLNx.CEUKHbwTCG + t.length).toFixed(10); '; 121'
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
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> a24.biz.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/login?__cf_chl_jschl_tk__=bdacda0f40153e5d7f1dc281a82f4e0e99ad6b4f-1574843794-0-ATa8KTGdrGnxEv4cfyWB6D-MwAE4F5cCr39T9mVXlQEBmiXZB3VKS5Y0JyDeChZjp9ra5G7wzxOrfr1VnMEZ0SsZeslOOHV2Ndrdfe3uC6NzOhrLDTh5CMsQXJ14lCONmTiGy5rY8RV02YqhodWGIa19d5mZk8GNp1Y0_bmzfOA3Ah9KJdCL9kdbyKj8Sbf3gMu7KkLbvbgASzgoVoVPk54" method="POST" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="r" value=""></input>
    <input type="hidden" name="jschl_vc" value="1c7ad243187469f98cddab032459f228"/>
    <input type="hidden" name="pass" value="1574843798.522-TrURyxAEGl"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
  
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 53c2c673cc898f25
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

DOC;

                    return new UAMPageAttributes("https", "a24.biz", $page);
                })(),
                new UAMPageFormParams(
                    '',
                    '1c7ad243187469f98cddab032459f228',
                    '1574843798.522-TrURyxAEGl',
                    '9.2981893047',
                    '/login?__cf_chl_jschl_tk__=bdacda0f40153e5d7f1dc281a82f4e0e99ad6b4f-1574843794-0-ATa8KTGdrGnxEv4cfyWB6D-MwAE4F5cCr39T9mVXlQEBmiXZB3VKS5Y0JyDeChZjp9ra5G7wzxOrfr1VnMEZ0SsZeslOOHV2Ndrdfe3uC6NzOhrLDTh5CMsQXJ14lCONmTiGy5rY8RV02YqhodWGIa19d5mZk8GNp1Y0_bmzfOA3Ah9KJdCL9kdbyKj8Sbf3gMu7KkLbvbgASzgoVoVPk54'
                )
            ],
            [
                (function (): UAMPageAttributes {
                    $page = <<<DOC

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
        var s,t,o,p,b,r,e,a,k,i,n,g,f, bHdzerN={"J":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))};
        g = String.fromCharCode;
        o = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        e = function(s) {
          s += "==".slice(2 - (s.length & 3));
          var bm, r = "", r1, r2, i = 0;
          for (; i < s.length;) {
              bm = o.indexOf(s.charAt(i++)) << 18 | o.indexOf(s.charAt(i++)) << 12
                      | (r1 = o.indexOf(s.charAt(i++))) << 6 | (r2 = o.indexOf(s.charAt(i++)));
              r += r1 === 64 ? g(bm >> 16 & 255)
                      : r2 === 64 ? g(bm >> 16 & 255, bm >> 8 & 255)
                      : g(bm >> 16 & 255, bm >> 8 & 255, bm & 255);
          }
          return r;
        };
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1); 
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;bHdzerN.J*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));bHdzerN.J+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+[])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]));bHdzerN.J+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+[]));bHdzerN.J+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]));bHdzerN.J+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));a.value = (+bHdzerN.J + t.length).toFixed(10); '; 121'
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
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> extreme-down.xyz.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/rss.xml?1241878191&__cf_chl_jschl_tk__=1f480117fd95eb9ab33aa66d98d5c668a4d141fd-1574845929-0-AQG9RuGh_GaJKhqHYHBLx1471_ORFrqMaedaDW964fL2pZzQCFWXaUJg-eM4LJQ4tiUML990r1EgRI1xlvc3BFtnBcLw0PyKdBx28dN4SRvxKhJ33GVf_BgrXmid1el5C2T-pFGY5nDgrAS7Hvh8W7DywznWGKKxQkN322_gn5NVB4SfVq3ij9TTmkSNHAVZETiuAeW3j4uSWCFjFwXhCecb3LnrywFN8IUaeTTM5gez" method="POST" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="r" value=""></input>
    <input type="hidden" name="jschl_vc" value="614c24887ead5ab9077c780d82799928"/>
    <input type="hidden" name="pass" value="1574845933.714-6uSjspYzFY"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
  
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 53c2fa94be904e6a
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

DOC;

                    return new UAMPageAttributes("https", "wvw.extreme-down.xyz", $page);
                })(),
                new UAMPageFormParams(
                    '',
                    '614c24887ead5ab9077c780d82799928',
                    '1574845933.714-6uSjspYzFY',
                    '31.6301907550',
                    '/rss.xml?1241878191&__cf_chl_jschl_tk__=1f480117fd95eb9ab33aa66d98d5c668a4d141fd-1574845929-0-AQG9RuGh_GaJKhqHYHBLx1471_ORFrqMaedaDW964fL2pZzQCFWXaUJg-eM4LJQ4tiUML990r1EgRI1xlvc3BFtnBcLw0PyKdBx28dN4SRvxKhJ33GVf_BgrXmid1el5C2T-pFGY5nDgrAS7Hvh8W7DywznWGKKxQkN322_gn5NVB4SfVq3ij9TTmkSNHAVZETiuAeW3j4uSWCFjFwXhCecb3LnrywFN8IUaeTTM5gez'
                )
            ],
            [
                (function (): UAMPageAttributes {
                    $page = <<<DOC

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
        var s,t,o,p,b,r,e,a,k,i,n,g,f, QnRnMaz={"FqsjER":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]))};
        g = String.fromCharCode;
        o = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        e = function(s) {
          s += "==".slice(2 - (s.length & 3));
          var bm, r = "", r1, r2, i = 0;
          for (; i < s.length;) {
              bm = o.indexOf(s.charAt(i++)) << 18 | o.indexOf(s.charAt(i++)) << 12
                      | (r1 = o.indexOf(s.charAt(i++))) << 6 | (r2 = o.indexOf(s.charAt(i++)));
              r += r1 === 64 ? g(bm >> 16 & 255)
                      : r2 === 64 ? g(bm >> 16 & 255, bm >> 8 & 255)
                      : g(bm >> 16 & 255, bm >> 8 & 255, bm & 255);
          }
          return r;
        };
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1); 
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;QnRnMaz.FqsjER+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));QnRnMaz.FqsjER+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[]));QnRnMaz.FqsjER-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));QnRnMaz.FqsjER*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]));a.value = (+QnRnMaz.FqsjER + t.length).toFixed(10); '; 121'
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
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> extreme-down.xyz.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/rss.xml?1241878191&__cf_chl_jschl_tk__=68a9c61afb0d73e3e86e730ab94dc447d964e987-1574846360-0-AUfBOxdi3VrSz3wfe4XFMSJgKnIT-d0jp1rmNghEuJwXYuMbKZA8WaQNa474g7uAmBgt1aRZ3HNbVm-ICpaqAMe_FSi5CcvfLzMszdiU6z0Tg-EflF6fuGdFOKP4eDpU1iLxaF1WCXSzud-ZBMd7Z8ozRo4O_kiDwD2lpC-BNayLUVL_Z8TZQKLmxvtVwfpPhRjbJXaKDeRw8sWpNPqzVdTdTstRbhGX8UE8d2qtkos0" method="POST" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="r" value=""></input>
    <input type="hidden" name="jschl_vc" value="662b582aa3d1d0ba622b4f37026a6fe4"/>
    <input type="hidden" name="pass" value="1574846364.186-u474wzx7mu"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
  
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 53c3051729b08fa3
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

DOC;

                    return new UAMPageAttributes("https", "wvw.extreme-down.xyz", $page);
                })(),
                new UAMPageFormParams(
                    '',
                    '662b582aa3d1d0ba622b4f37026a6fe4',
                    '1574846364.186-u474wzx7mu',
                    '23.4045172129',
                    '/rss.xml?1241878191&__cf_chl_jschl_tk__=68a9c61afb0d73e3e86e730ab94dc447d964e987-1574846360-0-AUfBOxdi3VrSz3wfe4XFMSJgKnIT-d0jp1rmNghEuJwXYuMbKZA8WaQNa474g7uAmBgt1aRZ3HNbVm-ICpaqAMe_FSi5CcvfLzMszdiU6z0Tg-EflF6fuGdFOKP4eDpU1iLxaF1WCXSzud-ZBMd7Z8ozRo4O_kiDwD2lpC-BNayLUVL_Z8TZQKLmxvtVwfpPhRjbJXaKDeRw8sWpNPqzVdTdTstRbhGX8UE8d2qtkos0'
                )
            ],
            [
                (function (): UAMPageAttributes {
                    $page = <<<DOC
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
        var s,t,o,p,b,r,e,a,k,i,n,g,f, yCVaYol={"SXKGE":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(+!![])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]))};
        g = String.fromCharCode;
        o = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        e = function(s) {
          s += "==".slice(2 - (s.length & 3));
          var bm, r = "", r1, r2, i = 0;
          for (; i < s.length;) {
              bm = o.indexOf(s.charAt(i++)) << 18 | o.indexOf(s.charAt(i++)) << 12
                      | (r1 = o.indexOf(s.charAt(i++))) << 6 | (r2 = o.indexOf(s.charAt(i++)));
              r += r1 === 64 ? g(bm >> 16 & 255)
                      : r2 === 64 ? g(bm >> 16 & 255, bm >> 8 & 255)
                      : g(bm >> 16 & 255, bm >> 8 & 255, bm & 255);
          }
          return r;
        };
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1); k = 'cf-dn-rMeAHMeX';
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;yCVaYol.SXKGE-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]));yCVaYol.SXKGE-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/(+(+((!+[]+!![]+!![]+[])+(+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![])+(+!![])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])))+(function(p){return eval((true+"")[0]+".ch"+(false+"")[1]+(true+"")[1]+Function("return escape")()(("")["italics"]())[2]+"o"+(undefined+"")[2]+(true+"")[3]+"A"+(true+"")[0]+"("+p+")")}(+((+[]+[])))));yCVaYol.SXKGE*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]));yCVaYol.SXKGE-=function(p){var p = eval(eval(e("ZG9jdW1l")+(undefined+"")[1]+(true+"")[0]+(+(+!+[]+[+!+[]]+(!![]+[])[!+[]+!+[]+!+[]]+[!+[]+!+[]]+[+[]])+[])[+!+[]]+g(103)+(true+"")[3]+(true+"")[0]+"Element"+g(66)+(NaN+[Infinity])[10]+"Id("+g(107)+")."+e("aW5uZXJIVE1M"))); return +(p)}();yCVaYol.SXKGE-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]));yCVaYol.SXKGE*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));yCVaYol.SXKGE-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]));yCVaYol.SXKGE+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[]));yCVaYol.SXKGE+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+[])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[]));yCVaYol.SXKGE*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((+!![]+[])+(+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));a.value = (+yCVaYol.SXKGE).toFixed(10); '; 121'
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
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> rmz.cr.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   <div style="display: none;"><a href="http://purpleisp.net/frozentalented.php?fdate=903">table</a></div>
  <form id="challenge-form" action="/?__cf_chl_jschl_tk__=23fbe751b23d3b4d74dfe854ecb7ce423a70bf76-1574941362-0-Adl9tfFWIRLt3KfRX4ZY25_Oby7-AzwdGQDW1h0MRRMqlvstijQp8evwoIBpFiEGVpFTmL88YfMmYarqQIvCJG7fLOuwZrnW-Dk2VfWOhzrhYbYylkcW_IFc60KkwkusZUIsNHRo4ZKvKWTqFsZ-pM2XfqgUTLTz2OWYKAgDFpN7VT4kzahgSnKbCFz5hqMUGxUIx3vd9gydPrlLOHYBfHsEsDH2uMNNH_hTd_9RmTRjGqZdZcW9-sMNfXTa-prTQWorp3TogUW9OwTuYYshfus" method="POST" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="r" value="4a2422c375e5cd036ecc48897b271847436ccc92-1574941362-0-AU3V0i9snsmVeiOhiii76fZzc17nnvvStmjrwLLG1n3qZNwroOOJjmKt10/OADi/LiBirrrWpTH8QiKreWD38+N6oLvb9Iw1BGMoMYk5kHNrV4TSI7yX1osembNkTufXgl8+mg1CaqjlPT2MUvcSwLpf6QN3b4xhM3sEEuhnBmeEXoXFaNEZ1BDxvZQoLpQzvq/olbp1jTT+jlL7QKLKYF5icTlPT2CPhnw9D0+FCFV/8qwjTJq0u0FByKxNS/fYXs3/37+6yespkDTSGMiDtY/uWKYUrF0E1IrI20e8qLdHR4+LGFOCsS2lx3iRvfs8a0oLi6bVpCnpGrHTbfY4O8Q/RMamHKAQFvDs3dvNZt/fULnjPEwQ7WVP023fjPYhNu+AFyUCYpujg2qhLDIBrQmMCAZlCujvWAZR3ysQHC7cVXuG+epyIdHuhfFNbZIdwNAksk6w0jyXTKlvdnbqVgcYhODCeVYQPGxnmJOeWE72xw3Pi1pD39oC0qX4ySUyG6U3zBrEINBeKqASieVOTQplvxaFvoglLYR2fTbpou1QsDiibFILYVtxA+TQv4qsEhXr5ej87Lvz9gqKdGrWS7uYA+x5SnbV7XAR8WEpW6PgbHKMAmDSu0JXe1lFyVw4vABiuk39JhpBL4a63fQjHLu5+cSbH6JOENyDMwV0L9JxZkiRyin/NWSKCf4N25CV3oTEwoD6fF5LCA9thaypgGKGWx1B0SZ3+QXheimiSL7E"></input>
    <input type="hidden" name="jschl_vc" value="e5a57bfb4fe35a7675967ba71d4aa69e"/>
    <input type="hidden" name="pass" value="1574941366.439-O9xaG4k9nB"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
  
  <div style="display:none;visibility:hidden;" id="cf-dn-rMeAHMeX">+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))</div>
  
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 53cc147b3bf79814
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

DOC;

                    return new UAMPageAttributes("https", "rmz.cr", $page);
                })(),
                new UAMPageFormParams(
                    '4a2422c375e5cd036ecc48897b271847436ccc92-1574941362-0-AU3V0i9snsmVeiOhiii76fZzc17nnvvStmjrwLLG1n3qZNwroOOJjmKt10/OADi/LiBirrrWpTH8QiKreWD38+N6oLvb9Iw1BGMoMYk5kHNrV4TSI7yX1osembNkTufXgl8+mg1CaqjlPT2MUvcSwLpf6QN3b4xhM3sEEuhnBmeEXoXFaNEZ1BDxvZQoLpQzvq/olbp1jTT+jlL7QKLKYF5icTlPT2CPhnw9D0+FCFV/8qwjTJq0u0FByKxNS/fYXs3/37+6yespkDTSGMiDtY/uWKYUrF0E1IrI20e8qLdHR4+LGFOCsS2lx3iRvfs8a0oLi6bVpCnpGrHTbfY4O8Q/RMamHKAQFvDs3dvNZt/fULnjPEwQ7WVP023fjPYhNu+AFyUCYpujg2qhLDIBrQmMCAZlCujvWAZR3ysQHC7cVXuG+epyIdHuhfFNbZIdwNAksk6w0jyXTKlvdnbqVgcYhODCeVYQPGxnmJOeWE72xw3Pi1pD39oC0qX4ySUyG6U3zBrEINBeKqASieVOTQplvxaFvoglLYR2fTbpou1QsDiibFILYVtxA+TQv4qsEhXr5ej87Lvz9gqKdGrWS7uYA+x5SnbV7XAR8WEpW6PgbHKMAmDSu0JXe1lFyVw4vABiuk39JhpBL4a63fQjHLu5+cSbH6JOENyDMwV0L9JxZkiRyin/NWSKCf4N25CV3oTEwoD6fF5LCA9thaypgGKGWx1B0SZ3+QXheimiSL7E',
                    'e5a57bfb4fe35a7675967ba71d4aa69e',
                    '1574941366.439-O9xaG4k9nB',
                    '-87.0050380394',
                    '/?__cf_chl_jschl_tk__=23fbe751b23d3b4d74dfe854ecb7ce423a70bf76-1574941362-0-Adl9tfFWIRLt3KfRX4ZY25_Oby7-AzwdGQDW1h0MRRMqlvstijQp8evwoIBpFiEGVpFTmL88YfMmYarqQIvCJG7fLOuwZrnW-Dk2VfWOhzrhYbYylkcW_IFc60KkwkusZUIsNHRo4ZKvKWTqFsZ-pM2XfqgUTLTz2OWYKAgDFpN7VT4kzahgSnKbCFz5hqMUGxUIx3vd9gydPrlLOHYBfHsEsDH2uMNNH_hTd_9RmTRjGqZdZcW9-sMNfXTa-prTQWorp3TogUW9OwTuYYshfus'
                )
            ],
            [
                (function (): UAMPageAttributes {
                    $page = <<<DOC
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
        var s,t,o,p,b,r,e,a,k,i,n,g,f, AVSbIAc={"MFmLiHNg":+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]))};
        g = String.fromCharCode;
        o = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        e = function(s) {
          s += "==".slice(2 - (s.length & 3));
          var bm, r = "", r1, r2, i = 0;
          for (; i < s.length;) {
              bm = o.indexOf(s.charAt(i++)) << 18 | o.indexOf(s.charAt(i++)) << 12
                      | (r1 = o.indexOf(s.charAt(i++))) << 6 | (r2 = o.indexOf(s.charAt(i++)));
              r += r1 === 64 ? g(bm >> 16 & 255)
                      : r2 === 64 ? g(bm >> 16 & 255, bm >> 8 & 255)
                      : g(bm >> 16 & 255, bm >> 8 & 255, bm & 255);
          }
          return r;
        };
        t = document.createElement('div');
        t.innerHTML="<a href='/'>x</a>";
        t = t.firstChild.href;r = t.match(/https?:\/\//)[0];
        t = t.substr(r.length); t = t.substr(0,t.length-1); 
        a = document.getElementById('jschl-answer');
        f = document.getElementById('challenge-form');
        ;AVSbIAc.MFmLiHNg-=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(+[]));AVSbIAc.MFmLiHNg*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));AVSbIAc.MFmLiHNg*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]));AVSbIAc.MFmLiHNg*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]));AVSbIAc.MFmLiHNg*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+[])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));AVSbIAc.MFmLiHNg*=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]));AVSbIAc.MFmLiHNg+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(+[])+(!+[]+!![]+!![]))/+((!+[]+!![]+[])+(+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![])+(!+[]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]));AVSbIAc.MFmLiHNg+=+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![])+(+!![])+(+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(+!![]))/+((!+[]+!![]+!![]+!![]+!![]+!![]+!![]+[])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![]+!![]+!![])+(!+[]+!![]+!![])+(+!![])+(!+[]+!![]+!![]+!![]+!![]+!![]+!![]+!![]+!![])+(!+[]+!![])+(+[])+(+[]));a.value = (+AVSbIAc.MFmLiHNg + t.length).toFixed(10); '; 121'
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
    <h1><span data-translate="checking_browser">Checking your browser before accessing</span> a24.biz.</h1>
    
    <p data-translate="process_is_automatic">This process is automatic. Your browser will redirect to your requested content shortly.</p>
    <p data-translate="allow_5_secs">Please allow up to 5 seconds&hellip;</p>
  </div>
   
  <form id="challenge-form" action="/login?__cf_chl_jschl_tk__=85c5dd2386e25fe996521fc8f48ca44f07a8174a-1574941758-0-AWJfsd6c9UfE0ZEIJ2X5hBDB2JjRXvkAvzeEkw2HpqpXgd6qQabvjG8HjNNsk2MwnXUNztacyD0Bk6GVAagfsLQ1WjN4UXN2O126zbSF6O7amgRY3vdvN9dy5e3Sw1siUBW6Q4iRsj9A5lkRc3Ib4LJ1ibvyIyaW6RJXRvgevuixemxcjQOqNH9jK7QzinxmVgebb2Fo3uJnwGMLOZll4swtlObwlLmYxZhqvpFMbeviRu5pUBsPCG5hVqom1YIczWgCIxjRYNPz66Ud8GtfmiQ" method="POST" enctype="application/x-www-form-urlencoded">
    <input type="hidden" name="r" value="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"></input>
    <input type="hidden" name="jschl_vc" value="02ee1a7a19d32267524b2f46f27c4dcc"/>
    <input type="hidden" name="pass" value="1574941762.472-Ec+pAGmMYR"/>
    <input type="hidden" id="jschl-answer" name="jschl_answer"/>
  </form>
  
</div>

          
          <div class="attribution">
            <a href="https://www.cloudflare.com/5xx-error-landing?utm_source=iuam" target="_blank" style="font-size: 12px;">DDoS protection by Cloudflare</a>
            <br>
            Ray ID: 53cc1e266cbec83b
          </div>
      </td>
     
    </tr>
  </table>
</body>
</html>

DOC;

                    return new UAMPageAttributes("https", "a24.biz", $page);
                })(),
                new UAMPageFormParams(
                    '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',
                    '02ee1a7a19d32267524b2f46f27c4dcc',
                    '1574941762.472-Ec+pAGmMYR',
                    '10.7265030140',
                    '/login?__cf_chl_jschl_tk__=85c5dd2386e25fe996521fc8f48ca44f07a8174a-1574941758-0-AWJfsd6c9UfE0ZEIJ2X5hBDB2JjRXvkAvzeEkw2HpqpXgd6qQabvjG8HjNNsk2MwnXUNztacyD0Bk6GVAagfsLQ1WjN4UXN2O126zbSF6O7amgRY3vdvN9dy5e3Sw1siUBW6Q4iRsj9A5lkRc3Ib4LJ1ibvyIyaW6RJXRvgevuixemxcjQOqNH9jK7QzinxmVgebb2Fo3uJnwGMLOZll4swtlObwlLmYxZhqvpFMbeviRu5pUBsPCG5hVqom1YIczWgCIxjRYNPz66Ud8GtfmiQ'
                )
            ],
        ];
    }

    /**
     * @dataProvider getParamsFromPage_testCases
     * @param UAMPageAttributes $pageAttributes
     * @param UAMPageFormParams $expectedFormParams
     * @throws ErrorException
     */
    public function testGetParamsFromPage(UAMPageAttributes $pageAttributes, UAMPageFormParams $expectedFormParams)
    {
        $this->assertEquals($expectedFormParams, UAMPageFormParams::getParamsFromPage($pageAttributes));
    }
}
