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

    //----クライアントからの受信解析用
    //----動的区切り解析用
    function getArrayBySafeSeparator($strStream, $strTailMark="")
    {
        $retVar = "";
        if( $strTailMark == "" )
        {
            $strTailMark = ";";
        }
        $varPos = mb_strpos($strStream, $strTailMark, 0,"UTF-8");
        if( $varPos !== false )
        {
            $strSepa = mb_substr($strStream, 0, $varPos + mb_strlen($strTailMark, 'UTF-8'));
            $intSepaLen = mb_strlen($strSepa, 'UTF-8');
            $strData = mb_substr($strStream, $intSepaLen, mb_strlen($strStream, 'UTF-8') - $intSepaLen );
            $retVar = explode($strSepa, $strData);
        }
        else
        {
            $retVar = $strStream;
        }
        return $retVar;
    }
    //動的区切り解析用----
    //クライアントからの受信解析用----

    //----クライアントへの送信用
    function makeAjaxProxyResultStream($aryResultElement)
    {
        $strSafeSepa = makeSafeSeparator($aryResultElement);
        return $strSafeSepa.implode($strSafeSepa,$aryResultElement);
    }

    function makeSafeSeparator($varCheckDataSourceBody,$miStrEscAfterHead="ss",$miStrEscAfterTail=";",$boolRandom=true)
    {
        $miBoolEscRequire=false;
        $miRandomNum  ="";
        if( $boolRandom === true )
        {
            $miRandomNum = rand(1,256);
        }
        
        if( is_string($varCheckDataSourceBody) === true )
        {
            $aryForCheck = array($varCheckDataSourceBody);
        }
        else if( is_array($varCheckDataSourceBody) === true )
        {
            $aryForCheck = $varCheckDataSourceBody;
        }
        
        $miStrForbiddenStr = $miStrEscAfterHead.$miRandomNum.$miStrEscAfterTail;
        foreach($aryForCheck as $miCheckDataSourceBody)
        {
            if( mb_strpos($miCheckDataSourceBody, $miStrForbiddenStr, 0,"UTF-8") !== false )
            {
                //----含まれていた場合
                $miBoolEscRequire=true;
                //含まれていた場合---
            }
        }
        if( $miBoolEscRequire === true )
        {
            $miSearchCount = 0;
            do{
                $miSearchCount+=1;
                $miSearchPattern = $miStrEscAfterHead.$miRandomNum.strval($miSearchCount).$miStrEscAfterTail;
                foreach($aryForCheck as $miCheckDataSourceBody)
                {
                    $miSearchResult=mb_strpos($miCheckDataSourceBody, $miSearchPattern, 0,"UTF-8");
                    if( $miSearchResult !== false )
                    {
                        break;
                    }
                }
            }while( $miSearchResult !== false );
            $miStrForbiddenStr = $miSearchPattern;
        }
        return $miStrForbiddenStr;
    }
    //クライアントへの送信用----

    function getJscriptMessageTemplate($aryImportFilePath, &$objMTS)
    {
        //----メッセージテンプレートの素材を収集
        $aryJsMsgOrgBody = array();
        foreach($aryImportFilePath as $strTmplFilePath)
        {
            $aryJsMsgOrgBody = array_merge($aryJsMsgOrgBody,$objMTS->getArrayFromTemplate($strTmplFilePath));
        }
        //メッセージテンプレートの素材を収集----
        
        $aryJsMsgData = array();
        foreach($aryJsMsgOrgBody as $key=>$val)
        {
            $key = str_replace("-","",$key);
            $aryJsMsgData[] = $key.":".$val;
        }
        //----動的にデリミッターを計算
        $strSepaHead = "dysp";
        $intCheck = 0;
        do
        {
            $strCheckDelimiter = $strSepaHead.$intCheck.";";
            foreach($aryJsMsgData as $val)
            {
                if( mb_strpos($val, $strCheckDelimiter, 0, "UTF-8" ) !== false )
                {
                    $intCheck += 1;
                    break;
                }
            }
            if( $strCheckDelimiter == $strSepaHead.$intCheck.";" )
            {
                $intCheck = false;
                break;
            }
        } while( $intCheck !== false );
        //動的にデリミッターを計算----
        return $strCheckDelimiter.implode($strCheckDelimiter,$aryJsMsgData);
    }
    //00-開発者領域画面そのほかシステム用----

    function printHeaderForProvideFileStream($strProvideFilename,$strContentType="application/vnd.ms-excel",$varContentLength=null)
    {
        // excelまたはcsv出力用httpレスポンスヘッダ出力
        ky_printHeaderForProvideBinaryStream($strProvideFilename,$strContentType,$varContentLength);
    }

    function dev_log($textBody, $intPointDetailLevel=1, $boolEveryone=false)
    {
        // グローバル変数の利用宣言
        global $g;
        $intReqClientDevFlag = isset($g['dev_log_developer'])?$g['dev_log_developer']:0;
        if( 0 < $intReqClientDevFlag || $boolEveryone === true )
        {
            if( isset($g['root_dir_path']) )
            {
                $lc_root_dir_path = $g['root_dir_path'];
            }
            else
            {
                $lc_root_dir_path = getApplicationRootDirPath();
            }

            $stampTime = time();
            $filePrefix=str_replace(".","_",getSourceIPAddress());
            $filename=$filePrefix."_debug_dev_log_".date("Ymd",$stampTime).".log";
            $set_dir_path = $lc_root_dir_path."/logs/dev_log";
            if( is_dir($set_dir_path) === false )
            {
                //----構成破壊
                $boolOutput = false;
                //構成破壊----
            }
            else
            {
                //----＜-エラー制御演算子@を付加＞
                $boolOutput = @file_put_contents($set_dir_path."/".$filename, date("Y/m/d H:i:s",$stampTime)." ".$textBody."\n", FILE_APPEND );
                //＜-エラー制御演算子@を付加＞----
            }
            if( $boolOutput === false )
            {
                web_log("Dev_log error is occured on directory [{$set_dir_path}]. Dev_log text is [{$textBody}].");
                // 想定外エラー通知画面にリダイレクト
                webRequestForceQuitFromEveryWhere(500,null);
                exit();
            }
        }
    }

    function webRequestForceQuitFromEveryWhere($intDefaultResutStatusCode=500,$intForceQuitDatailCode=null,$aryAppendix=array()){
        // グローバル変数の利用宣言
        global $g;
        list($aryReqByREST,$tmpBool)=isSetInArrayNestThenAssign($g,array('requestByREST'),null);
        if( is_array($aryReqByREST) === true )
        {
            //----RestAPIからのアクセスの場合
            $strException = 'Generic error';
            switch($intDefaultResutStatusCode)
            {
                case 400: // 要求が正しくない
                    $strErrorType = "Bad Request";
                    break;
                case 401: // 認証が必要である
                    $strErrorType = "Unauthorized";
                    break;
                case 403: // 禁止されている（アクセス権がない、ホストがアクセスすることを拒否された）
                    $strErrorType = "Forbidden";
                    break;
                case 404: // リソースがみつからなかった
                    $strErrorType = "Not Found";
                    break;
                case 500: // サーバ内部エラー
                    $strErrorType = "Internal Server Error";
                    break;
                case 501: // 実装されていないメソッド
                    $strErrorType = "Not Implemented";
                    break;
                case 502: // 不正なゲートウェイ
                    $strErrorType = "Bad Gateway";
                    break;
                case 503: // サービス利用不可（過負荷、メンテナンス中による）
                    $strErrorType = "Service Unavaliable";
                    break;
                default:
                    $intDefaultResutStatusCode = 500;
                    $strErrorType = "Unexpected error";
                    break;
            }
            switch($intForceQuitDatailCode){
                case 11410201: // 権限がなかった
                    $strException = "No Privillege Access Error";
                    break;
                case 10410301: // IPアドレス（ホワイトリスト）に登録されていない
                    $strException = "Access Forbidden Error";
                    break;
                case 11410401: // 未認証アクセス
                    $strException = "Access Forbidden Error";
                    break;
                case 11410501: // パスワード有効期限切れ
                    $strException = "Password Expired Error";
                    break;
                case 11410601: // アカウントロック
                    $strException = "Account Locked Error";
                    break;
                case 11410701: // 開発者によるメンテナンス(中の通知)画面
                    $strException = "In Maintenance Mode, Access Forbidden Error";
                    break;
                case 11510801: // 不正なリクエスト（形式の不正）
                case 11510802: // 不正なリクエスト（形式の不正）
                    $strException = "Content-type Is Not Correct Error";
                    break;
                default: // システムエラー
                    break;
            }
            list($varStackTrace, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('StackTrace'),false);
            if( is_array($varStackTrace) === false && is_string($varStackTrace) === false )
            {
                $varStackTrace = 'none';
            }
            $intResultStatusCode = $intDefaultResutStatusCode;
            $aryResponsContents  = array('Error'=>$strErrorType,
                                         'Exception'=>$strException,
                                         'StackTrace'=>$varStackTrace);

            list($boolOverrideByGlobalVars, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('OverrideByGlobalVars'),false);
            if( $boolOverrideByGlobalVars === true )
            {
                list($intResultStatusCode, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($g,array('requestByREST','resultStatusCode'),$intResultStatusCode);
                list($aryResponsContents , $tmpBoolKeyExists) = isSetInArrayNestThenAssign($g,array('requestByREST','preResponsContents','errorInfo'),$aryResponsContents);
            }

            header('Content-Type: application/json; charset=utf-8', true, $intResultStatusCode);
            $objJSONOfResultData = @json_encode($aryResponsContents);

            exit($objJSONOfResultData);

            //RestAPIからのアクセスの場合----
        }
        else
        {
            //----その他のリクエストの場合
            switch($intDefaultResutStatusCode)
            {
                case 400: // 要求が正しくない
                case 401: // 認証が必要である
                case 403: // 禁止されている（アクセス権がない、ホストがアクセスすることを拒否された）
                case 404: // リソースがみつからなかった
                case 500: // サーバ内部エラー
                case 501: // 実装されていないメソッド
                case 502: // 不正なゲートウェイ
                case 503: // サービス利用不可（過負荷、メンテナンス中による）
                    break;
            }

            list($intInsideRedirectMode, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('InsideRedirectMode'),1); // 0

            //MDC(NNN)+
            switch($intForceQuitDatailCode)
            {
                case 10310201: // 不正操作によるアクセス警告画面にリダイレクト
                case 10610201: // 不正操作によるアクセス警告画面にリダイレクト
                case 10810201: // 不正操作によるアクセス警告画面にリダイレクト
                case 11210201: // 不正操作によるアクセス警告画面にリダイレクト
                case 11310201: // 不正操作によるアクセス警告画面にリダイレクト
                case 20110201: // 不正操作によるアクセス警告画面にリダイレクト
                case 20310201: // 不正操作によるアクセス警告画面にリダイレクト
                case 20310202: // 不正操作によるアクセス警告画面にリダイレクト
                case 20410201: // 不正操作によるアクセス警告画面にリダイレクト
                    insideRedirectCodePrint("/common/common_illegal_access.php",$intInsideRedirectMode);
                    break;
                case 10410301: // IPアドレス（ホワイトリスト）に登録されていない
                    insideRedirectCodePrint("/common/common_access_filter.php",$intInsideRedirectMode);
                    break;
                case 10610401: // 認証画面にリダイレクト
                    list($strMenuIdNumeric, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('MenuID'),null);
                    list($aryValueForPost,  $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('ValueForPost'),array());
                    insideRedirectCodePrint("/common/common_auth.php?login&no={$strMenuIdNumeric}",$intInsideRedirectMode,$aryValueForPost);
                    break;
                case 10710501: // パスワード変更画面にリダイレクト
                    list($strMenuIdNumeric, $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('MenuID'),null);
                    list($aryValueForPost,  $tmpBoolKeyExists) = isSetInArrayNestThenAssign($aryAppendix,array('ValueForPost'),array());
                    insideRedirectCodePrint("/common/common_change_password_form.php?login&no={$strMenuIdNumeric}",$intInsideRedirectMode,$aryValueForPost);
                    break;
                case 10310601: // アカウントロック画面にリダイレクト
                case 10310602: // アカウントロック画面にリダイレクト
                    insideRedirectCodePrint("/common/common_account_locked_error.php",$intInsideRedirectMode);
                    break;
                case 10610701: // 開発者によるメンテナンス(中の通知)画面
                    insideRedirectCodePrint("/common/common_dev_maintenace.php",$intInsideRedirectMode);
                    break;
                default: // システムエラー
                    insideRedirectCodePrint("/common/common_unexpected_error.php",$intInsideRedirectMode);
                    break;
            }
            //その他のリクエストの場合----
        }
        exit();
    }

    function insideRedirectCodePrint($strUrlOfInside="", $mode=0, $aryPostData=array())
    {
        // グローバル変数の利用宣言
        global $g;
        // URLのスキーム＆オーソリティを取得
        $scheme_n_authority = getSchemeNAuthority();
        $strRediretTo = $scheme_n_authority.$strUrlOfInside;
        list($strReqByHA,$tmpBool)=isSetInArrayNestThenAssign($g,array('requestByHA'),"");
        if( 0 == strlen($strReqByHA) )
        {
            //----HTML/AJAX経由ではない場合
            switch($mode)
            {
                case 1:
                    $hiddenInputBody = "";
                    foreach($aryPostData as $key=>$val)
                    {
                        $hiddenInputBody .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$val}\">";
                    }
                    print 
<<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<form method="POST" id="redirectAgent" name="redirectAgent" action="{$strRediretTo}">
{$hiddenInputBody}
</form>
<script type="text/javascript">
window.onload = function(){
var obj = document.getElementById('redirectAgent');
if( obj === null ){
}else if( obj === undefined ) {
}else{
}
obj.submit();
}
</script>
</body>
</html>
EOD;
                    break;
                default:
                    header("Location: " . $strRediretTo);
                    exit();
                    break;
            }

            //HTML/AJAX経由ではない場合----
        }
        else
        {
            //----HTML/AJAX経由の場合
            if( $strReqByHA == "forHADAC" )
            {
                $strOrder = "redirectOrderForHADACClient";
            }
            else
            {
                $strOrder = "redirectOrderForHAGClient";
            }
            $arrayResult = array($strOrder,$mode,$strRediretTo);
            foreach($aryPostData as $key=>$val)
            {
                $arrayResult[] = $key;
                $arrayResult[] = $val;
            }
            print makeAjaxProxyResultStream($arrayResult);
            exit();
            //HTML/AJAX経由の場合----
        }
    }

    function web_log($FREE_LOG){
        // グローバル変数の利用宣言
        global $root_dir_path,$p_login_name,$g;
        $aryAppliOrg = array();
        $aryContent = array();
        $aryPickItems = array();

        $strColDelimiter = "\t";
        $strLineDelimiter = "\n";

        $p_LOGIN_ID = "";
        try
        {
            if ( empty($root_dir_path) )
            {
                $root_dir_path = getApplicationRootDirPath();
            }
            $lc_root_dir_path = $root_dir_path;

            // ----ログとして出力する項目
            $aryPickItems = array(
                'APP_LOG_PRINT_TIME'=>1,
                'APP_SOURCE_IP'=>1,
                'APP_SOURCE_IP_INFOBASE'=>1,
                'REQUEST_METHOD'=>0,
                'HTTP_HOST'=>0,
                'PHP_SELF'=>0,
                'QUERY_STRING'=>0,
                'HTTP_REFERER'=>0,
                'APP_LOGIN_ID'=>1,
                'APP_FREE_LOG'=>1
            );
            // ログとして出力する項目----

            // ----アクセス元IPを準備
            $tmpAryIPInfo = getSourceIPAddress(false);
            $aryAppliOrg['APP_SOURCE_IP'] = $tmpAryIPInfo[0];
            $aryAppliOrg['APP_SOURCE_IP_INFOBASE'] = $tmpAryIPInfo[1];
            unset($tmpArray);

            // ----ログインIDを準備
            if ( isset($p_login_name) )
            {
                $p_LOGIN_ID = $p_login_name;
            }
            else
            {
                if ( isset($g['login_name']) )
                {
                    $p_LOGIN_ID = $g['login_name'];
                }
                else
                {
                    $p_LOGIN_ID = "";
                }
            }
            $aryAppliOrg['APP_LOGIN_ID'] = $p_LOGIN_ID;
            // ログインIDを準備----

            // ----フリーログを準備
            if ( isset($FREE_LOG) )
            {
                $aryAppliOrg['APP_FREE_LOG'] = $FREE_LOG;
            }
            // フリーログを準備----

            // ----ログ出力時刻
            $tmpTimeStamp = time();
            $logtime = date("Y/m/d H:i:s",$tmpTimeStamp);
            $aryAppliOrg['APP_LOG_PRINT_TIME'] = $logtime;
            // ログ出力時刻----

            $intElementLength1 = count($aryPickItems);
            $intElementCount1 = 0;
            foreach( $aryPickItems as $strKey=>$intVal )
            {
                //----
                $aryBottomElement = array();
                $intElementCount1 += 1;
                $varAddElement = "";
                $strFocusElement = "";
                if( $intVal == 0 )
                {
                    $varAddElement = isset($_SERVER[$strKey])?$_SERVER[$strKey]:"";
                }
                else
                {
                    $varAddElement = isset($aryAppliOrg[$strKey])?$aryAppliOrg[$strKey]:"";
                }
                //
                if( is_array($varAddElement) === false )
                {
                    $aryBottomElement = array();
                    if( is_string($varAddElement)===true )
                    {
                        $aryBottomElement = array($varAddElement);
                    }
                }
                else
                {
                    $aryBottomElement = $varAddElement;
                }
                
                $intElementLength2 = count($aryBottomElement);
                $intElementCount2 = 0;
                
                foreach( $aryBottomElement as $strAddElement )
                {
                    $intElementCount2 += 1;
                    if( $intElementLength2 == $intElementCount2 )
                    {
                        $strAddElement = "\"{$strAddElement}\"";
                    }
                    else
                    {
                        $strAddElement = "\"{$strAddElement}\"{$strColDelimiter}";
                    }
                    $aryContent[] = $strAddElement;
                }
                if( $intElementLength1 != $intElementCount1 )
                {
                    $aryContent[] = $strColDelimiter;
                }
                else
                {
                    $aryContent[] = $strLineDelimiter;
                }
            }

            $set_dir_path = $lc_root_dir_path . "/logs/webaplogs";
            
            if( is_dir($set_dir_path) === false )
            {
                // 例外処理へ
                throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $filepointer = @fopen(  $lc_root_dir_path . "/logs/webaplogs/webap_" . date("Ymd", $tmpTimeStamp) . ".log", "a");
            if( @flock($filepointer, LOCK_EX) === false )
            {
                // 例外処理へ
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            foreach( $aryContent as $value )
            {
                if( @fputs($filepointer, $value) === false )
                {
                     // 例外処理へ
                     throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }            
            }
            if( @flock($filepointer, LOCK_UN) === false )
            {
                // 例外処理へ
                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            if( @fclose($filepointer) === false )
            {
                // 例外処理へ
                throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
        catch (Exception $e)
        {
            $textBody = implode($aryContent,"");
            syslog(LOG_CRIT,"Web_log error is occured on directory [{$set_dir_path}]. Wev_log text is [{$textBody}].");
            exit();
        }
    }

    function getSourceIPAddress($boolValueForIpCheck=true)
    {
        //----ipv4のみ(
        //----XFFに基本的にはある、というスタンス。その他を調べるのはオマケ
        $strPattern = "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
        $retVarValue = "";
        $p_SOURCE_IP = "";
        $aryRemoteAddressInfo = array();

        // 8項目
        $aryCheckKey = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_VIA',
            'HTTP_SP_HOST',
            'HTTP_FROM',
            'HTTP_FORWARDED',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        );

        foreach($aryCheckKey as $strFocusCheckKey)
        {
            $strTmpValue = "";
            if( array_key_exists($strFocusCheckKey, $_SERVER ) )
            {
                $strTmpValue = $_SERVER[$strFocusCheckKey];
                $aryExploded = explode(",", $strTmpValue);
                $strCheckValue = $aryExploded[0];
                $strCheckValue = str_replace(" ","", $strCheckValue);
                if( preg_match($strPattern, $strCheckValue)===1 )
                {
                    if($p_SOURCE_IP == "" )
                    {
                        $p_SOURCE_IP = $strCheckValue;
                    }
                }
            }
            $aryRemoteAddressInfo[] = $strTmpValue;
        }
        if( $boolValueForIpCheck === false )
        {
            // ----ログ用
            $retVarValue = array();
            $retVarValue[0] = $p_SOURCE_IP;
            $retVarValue[1] = $aryRemoteAddressInfo;
            // ログ用----
        }
        else
        {
            // ----IPチェック用
            $retVarValue = $p_SOURCE_IP;
            // IPチェック用----
        }
        return $retVarValue;
    }

    function error_log_wrapper($strErrorBody="", $speFILE="", $speLINE="", $arrayErrorBodyHead = array())
    {
         // グローバル変数の利用宣言
         global $g;
         if( isset($g['login_id']) === true )
         {
             $arrayErrorBodyHead[] .= "(login_id:=".$g['login_id'].")";
         }
         if( $speFILE != "")
         {
             $arrayErrorBodyHead[] = "(Source:=".$speFILE.")";
             if( $speLINE != "")
             {
                 $arrayErrorBodyHead[] = "(Line:=".$speLINE.")";
             }
         }
         $strErrorBodyHead = implode(".",$arrayErrorBodyHead);
         if( $strErrorBodyHead != "" )
         {
             $strErrorBodyHead .= ":";
         }
         $strErrorStream = $strErrorBodyHead.$strErrorBody;
         error_log($strErrorStream);
         return $strErrorStream;
    }

    // ----ここから業務色を排除した汎用系関数

    function getRequestProtocol()
    {
        if ( isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on' )
        {
            $lcStrProtocol = 'https://';
        }
        else
        {
            $lcStrProtocol = 'http://';
        }
        return $lcStrProtocol;
    }

    function getSchemeNAuthority()
    {
        // グローバル変数の利用宣言
        global $root_dir_path,$g;
        $retStrValue = "";
        if ( empty($root_dir_path) )
        {
            $root_dir_path = getApplicationRootDirPath();
        }
        $strContent = "";
        if( file_exists($root_dir_path."/confs/webconfs/L7Protocol.txt")===true )
        {
            $strContent = @file_get_contents ( $root_dir_path."/confs/webconfs/L7Protocol.txt" );
        }
        if( $strContent == "http" || $strContent == "https" )
        {
            $retStrValue = $strContent.":/"."/".$_SERVER['HTTP_HOST'];
        }
        else if( $strContent != "" )
        {
            web_log("Setting of L7Protocol is not collect.");
            exit();
        }
        if( $retStrValue == "" )
        {
            $protocol = getRequestProtocol();

            // 起動元がバックヤードかWebを判定
            $arrayReqInfo = requestTypeAnalyze();
            if( $arrayReqInfo[0] == "web" )
                $retStrValue = $protocol . $_SERVER['HTTP_HOST'];
            else
                $retStrValue = '';
        }
        return $retStrValue;
    }

    function ky_printHeaderForProvideBinaryStream($strProvideFilename,$strContentType="",$varContentLength=null,$boolFileNameUTF8=true)
    {
        if( $boolFileNameUTF8 === true )
        {
            //----RFC6266が適用されたブラウザのみ有効
            $strCDABody = 'Content-Disposition: attachment; filename*=UTF-8\'\''.rawurlencode($strProvideFilename);
        }
        else
        {
            $strCDABody = 'Content-Disposition: attachment; filename="'.$strProvideFilename.'"';
        }
        // 標準ヘッダー出力
        header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        header('Content-Description: File Transfer');
        header('Content-Type: '.$strContentType);
        header($strCDABody);
        header('Content-Transfer-Encoding: binary');
        if( $varContentLength === null )
        {
            // なにもしない
        }
        else
        {
            header('Content-Length: '.$varContentLength);
        }
        header("Cache-Control: public");
        header("Pragma: public");
    }

    // Webからtail -fできるファンクション
    function read_tail($file, $lines)
    {
        $handle = fopen($file, "r");
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = array();
        while ($linecounter> 0)
        {
            $t = " ";
            while ($t != "\n")
            {
                if(fseek($handle, $pos, SEEK_END) == -1)
                {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos --;
            }
            $linecounter --;
            if ($beginning)
            {
                rewind($handle);
            }
            $text[$lines-$linecounter-1] = fgets($handle);
            if ($beginning) break;
        }
        fclose ($handle);
        return array_reverse($text);
    }

    // ここまで業務色を排除した汎用系関数----

?>
