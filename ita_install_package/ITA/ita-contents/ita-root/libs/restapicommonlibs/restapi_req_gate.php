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
    //////////////////////////////////////////////////////////////////////
    //
    //  【返却パラメータ】
    //     $intResultStatusCode
    //      Type        : integer
    //      Description : httpレスポンス・ステータス
    //         400：Bad Request：渡されたパラメータが異なるなど、要求が正しくない場合に返却される
    //         401：Unauthorized：適切な認証情報を提供せず、保護されたリソースに対しアクセスをした場合に返却される
    //         404：指定されたリソースが見つからない場合に返却される
    //         405：Method Not Allowed：要求したリソースがサポートしていない HTTP メソッドを利用した場合に返却される
    //         500：Internal Server Error：API 実行時に予期しないエラーが発生した場合に返却される
    //
    //     $aryResponsContents
    //      Type        : array
    //      Description : 結果の詳細についてのステータス
    //
    //     $arySuccnessInfo
    //      Type        : array
    //      Description : 成功時のステータス
    //
    //     $aryErrorInfo
    //      Type        : array
    //      Description : エラー発生時のステータス
    //         Error     ：Error message
    //         Exception ：Exception classname
    //         StackTrace：Error StackTrace
    //
    //////////////////////////////////////////////////////////////////////

    global $vg_log_level;

    //----変数の宣言
    $objRAIA = null;
    
    $aryCallList = null;
    
    $aryResponsContents = array();
    $intResultStatusCode = 200;
    $boolExeContinue = true;
    
    // ステータス文字列を統一する。
    $arySuccessInfo = array('status'=>'SUCCEED','resultdata'=>'none');

    $aryErrorInfo = array('Error'=>'Unexpcted Error', 'Exception'=>'Generic error', 'StackTrace'=>'none');
    
    $aryReqHeaderRaw = array();
    $aryReqHeaderData = array();
    $aryReqAuthData = array();
    
    $strRequestURIOnRest = $_SERVER['PHP_SELF'];
    
    $objJSON = array();
    
    $strAccessKeyIdOnRest = '';
    $strSecretAccessKeyOnRest = '';
    $strDRStorageTrunkPathOnRest = '';
    
    $strTempData = '';
    $strTempJsonString = '';
    //変数の宣言----

    @require_once(dirname(__FILE__)."/restapi_php_classes.php");

    //----クラスが定義済かどうか、を確認する
    if( class_exists('RestAPIInfoAdmin') ){
        $tmpKeyDirName = 'ita-root';
        $objRAIA = new RestAPIInfoAdmin($strRequestURIOnRest, $arySuccessInfo, $aryErrorInfo, $intResultStatusCode, $tmpKeyDirName);
        unset($tmpKeyDirName);
        $boolExeContinue = $objRAIA->checkSafeMode($boolExeContinue);
        $boolExeContinue = $objRAIA->checkCallSetting($boolExeContinue, $aryCallSetting);
        $boolExeContinue = $objRAIA->checkFileSetting($boolExeContinue);
        
        $boolExeContinue = $objRAIA->authExecute($boolExeContinue);
        
        $boolExeContinue = $objRAIA->receptDataImport($boolExeContinue);
        $aryCallList = $objRAIA->getCallList();

        $boolExeContinue     = $objRAIA->callSubModules($boolExeContinue, $aryCallList);

        $intResultStatusCode = $objRAIA->getResultStatusCode();

        //----ここから返し値の最終代入
        if( $boolExeContinue === true ){
            $aryResponsContents = $objRAIA->getSuccessInfo();
        }
        else{
            $aryResponsContents = $objRAIA->getErrorInfo();
        }
        //ここまで返し値の最終代入----

        $strFreeLogForRequestLast = $objRAIA->getFreeLogForRequestLast();

        if( $strFreeLogForRequestLast=="" ){
            $strFreeLogForRequestLast = "";
            if( is_array($aryResponsContents)===true ){
                $tmpArray = array();
                foreach($aryResponsContents as $strKey=>$strValue){
                    if( ( is_string($strKey)===true || is_int($strKey) ) && ( is_string($strValue)===true || is_int($strValue) ) ){
                        $tmpArray[] = "[{$strKey}] {$strValue}";
                    }
                }
                $strFreeLogForRequestLast .= implode(",", $tmpArray);
                unset($tmpArray);
            }
        }
        if($vg_log_level == 'DEBUG')   $objRAIA->RestAPI_log($strFreeLogForRequestLast); 
        // RestAPIログへ出力
    }
    else{
        $intResultStatusCode = 500;
        $aryErrorInfo['Exception'] = 'PHP class RestAPIInfoAdmin is not declared';
        $aryResponsContents = $aryErrorInfo;

        // クラス定義ファイルがないという異常事態なので、syslogへ出力
        syslog(LOG_CRIT,"RestAPI[{$strRequestURIOnRest}] required source is not exists.");
    }
    //クラスが定義済かどうか、を確認する----


    //----ここからレスポンス

    $objJSONOfResultData = @json_encode($aryResponsContents);

    //----念のため[$intResultStatusCode]で上書き
    header('Content-Type: application/json; charset=utf-8', true, $intResultStatusCode);
    //念のため[$intResultStatusCode]で上書き----

    //----JSON形式で返す
    exit($objJSONOfResultData);
    //JSON形式で返す----

    //ここまでレスポンス----
?>
