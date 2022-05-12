<?php
//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
    
////////////////////////////////////////////////////////////////////////
// 処理概要
// ・シングルサインオン(SSO)認証を行う
// ・ログイン画面からgrp、noのパラメータを受け取り
//   sessionに保存してログイン成功後にリダイレクトする
// ・SSO認証方式は現状oauth2(OAuth version2)のみ 
////////////////////////////////////////////////////////////////////////

// ルートディレクトリを取得
if (empty($root_dir_path)) {
    $root_dir_temp = [];
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = "{$root_dir_temp[0]}ita-root";
}

try{
    // DBアクセスを伴う処理を開始
    include $root_dir_path."/libs/commonlibs/common_php_req_gate.php";
} catch (Exception $e){
    // DBアクセス例外処理パーツ
    include $root_dir_path."/libs/webcommonlibs/web_parts_db_access_exception.php";
}

// ----ユーザー認証class
require $root_dir_path."/libs/webcommonlibs/web_auth_class.php";
// ユーザー認証class----
// ----SSO認証関数
require $root_dir_path."/libs/webcommonlibs/web_functions_for_sso_auth.php";
// SSO認証関数----

$getCopy = $_GET;
unset($getCopy['oauth2']);
unset($getCopy['providerId']);
unset($getCopy['login']);
$get_parameter = "";
if("" != http_build_query($getCopy)){
    $get_parameter = "?" . http_build_query($getCopy);
}
$get_parameter = str_replace('+', '%20', $get_parameter);

$nextUrl = "/";
if("" != $get_parameter){
    $nextUrl = "/common/common_auth.php{$get_parameter}";
}

//csrf対策
if( $_POST["csrf_token"] != $_SESSION["csrf_token"] ){
  webRequestForceQuitFromEveryWhere(403);
  exit();
}

$nextUrl = getRequestProtocol().getRequestHost().$nextUrl;
// リダイレクト用パラメータ保存----

$strAuthClassName = '';
if (isset($_GET['oauth2'])) {
    $strAuthClassName = 'OAuth2';
    $strAuthType = 'oauth2';
}
if (!empty($strAuthClassName)) {
    $objAuth = new $strAuthClassName;
    $objAuth->setSessionName('ITA_SESSION_'.md5(getRequestHost()));
    $objAuth->setConfigFunction('getConfigSsoAuth');
    $objAuth->setFindUserFunction('findUser');
    $objAuth->setRegistFunction('autoRegistUser');
    $objAuth->setRegistFormFunction('registFormFunction');
    $objAuth->setNextUri($nextUrl);
    $scriptName = htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES, "UTF-8");
    $objAuth->setRedirectUri(getRequestProtocol().getRequestHost()."{$scriptName}?{$strAuthType}&callback");
    if (isset($_GET['debug'])) {
        $objAuth->isDebug = true;
    }
    $objAuth->start();
    if ($objAuth->isError) {
        // 認証エラー
        web_log("RESULT:ERROR sso auth faild[CLASS]".$strAuthClassName.",[CODE]".$objAuth->errCode.",[MESSAGE]".$objAuth->errMsg);
        $strErrorMsg = "[CODE][".$strAuthClassName."]".$objAuth->errCode."<br>[MESSAGE]".$objAuth->errMsg;
        if ($objAuth->isDebug && !empty($objAuth->errDetail)) {
            $strErrorMsg .= "<br>[TRACE]<pre>".$objAuth->errDetail."</pre>";
        }
        //webRequestForceQuitFromEveryWhere(401,10000401);
        error_notice($strErrorMsg);
        exit;
    }
    // RESULT:Success
    web_log($objMTS->getSomeMessage("ITAWDCH-STD-603"));
} else {
    //指定のパラメーターなし
    // RESULT:UNEXPECTED_ERROR
    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-2001","Missing parameters"));
    // 想定外エラー(400 BadRequest)
    // AuthClassが選択されていない
    //エラーページへのリダイレクト
    webRequestForceQuitFromEveryWhere(400,10000400);
    exit;
}
