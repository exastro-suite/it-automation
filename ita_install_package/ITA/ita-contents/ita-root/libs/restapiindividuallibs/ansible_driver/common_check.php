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
    //      $this->intResultStatusCode
    //      Type        : integer
    //      Description : httpレスポンス・ステータス
    //         400：Bad Request：渡されたパラメータが異なるなど、要求が正しくない場合に返却される
    //         401：Unauthorized：適切な認証情報を提供せず、保護されたリソースに対しアクセスをした場合に返却される
    //         404：指定されたリソースが見つからない場合に返却される
    //         405：Method Not Allowed：要求したリソースがサポートしていない HTTP メソッドを利用した場合に返却される
    //         500：Internal Server Error：API 実行時に予期しないエラーが発生した場合に返却される
    //
    //     $this->arySuccessInfo
    //      Type        : array
    //      Description : 成功時のステータス
    //
    //     $this->aryErrorInfo
    //      Type        : array
    //      Description : エラー発生時のステータス
    //         Error     ：Error message
    //         Exception ：Exception classname
    //         StackTrace：Error StackTrace
    //
    //     $boolExeContinue
    //      Type        : boolean
    //      Description : 
    //
    //////////////////////////////////////////////////////////////////////

    global $vg_log_level;

    if($vg_log_level == 'DEBUG')    $this->RestAPI_log("START[".basename(__FILE__)."]");

    //----全おけ＆3REST・共通
    $intNumPadding      = 10;

    $boolFocusValue     = null;
    $intRefErrorCode    = null;

    $root_dir_path      = $this->getApplicationRootDirPath();

    //----引数が渡されてきているか
    $aryReceptData          = $this->getReceptData();
    if( $boolExeContinue === true ){
        if( array_key_exists('DATA_RELAY_STORAGE_TRUNK',$aryReceptData) ){
            $strDataRelayStorageTrunkPathNS   = $aryReceptData['DATA_RELAY_STORAGE_TRUNK'];
        }
        else{
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:'DATA_RELAY_STORAGE_TRUNK' is not found.");
        }
    }
    
    if( $boolExeContinue === true ){
        if( array_key_exists('ORCHESTRATOR_SUB_ID',$aryReceptData) ){
            $strOrchestratorSub_Id           = $aryReceptData['ORCHESTRATOR_SUB_ID'];
        }
        else{
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:'ORCHESTRATOR_SUB_ID' is not found.");
        }
    }
    if( $boolExeContinue === true ){
        if( array_key_exists('EXE_NO',$aryReceptData) ){
            $strExeNo                        = $aryReceptData['EXE_NO'];
        }
        else{
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:'EXE_NO' is not found.");
        }
    }
    //引数が渡されてきているか----

    // ----共通バリデーションチェック(NULLバイト等の攻撃
    if( $boolExeContinue === true ){
        $boolFocusValue = $this->checkBasicValid($strDataRelayStorageTrunkPathNS, $intRefErrorCode);
        if( $boolFocusValue === false ){
            // NULLバイト等の攻撃と判定された場合
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:It is unjust character string.'strDataRelayStorageTrunkPathNS'");
        }
        else{
            $strRestApiLogPerRestNS = "{$strDataRelayStorageTrunkPathNS}";
        }
    }
    
    if( $boolExeContinue === true ){
        $boolFocusValue = $this->checkBasicValid($strOrchestratorSub_Id, $intRefErrorCode);
        if( $boolFocusValue === false ){
            // NULLバイト等の攻撃と判定された場合
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:It is unjust character string.'strOrchestratorSub_Id'");
        }
    }
    
    if( $boolExeContinue === true ){
        $boolFocusValue = $this->checkBasicValid($strExeNo             , $intRefErrorCode);
        if( $boolFocusValue === false ){
            // NULLバイト等の攻撃と判定された場合
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:It is unjust character string.'strExeNo'");
        }
    }
    // 共通バリデーションチェック(NULLバイト等の攻撃----

    // ----型別バリデーションチェック（数値か、存在するオーケストレータか）

    // ----渡されてきた【オーケストレータ】が存在しているかを確認する
    if( $boolExeContinue === true ){
        $aryOrchestratorList = array('LEGACY_NS'=>'legacy_ns','PIONEER_NS'=>'pioneer_ns','LEGACY_RL'=>'legacy_rl');
        $boolFocusValue = array_key_exists($strOrchestratorSub_Id, $aryOrchestratorList);
        if( $boolFocusValue === false ){
            // 存在しないオーケストレータが指定された
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:It is unjust character string.'Orchestrator'");
        }
        else{
            $aryOcheSubDir          = explode("_",$aryOrchestratorList[$strOrchestratorSub_Id]);
            $strRestApiLogPerOcheNS = "{$strRestApiLogPerRestNS}/{$aryOcheSubDir[0]}/{$aryOcheSubDir[1]}";
        }
    }
    // 渡されてきた【オーケストレータ】が存在しているかを確認する----

    // ----渡されてきた【作業ID】は、整数型か？
    if( $boolExeContinue === true ){
        $boolFocusValue = ctype_digit(strval($strExeNo));
        if( $boolFocusValue === false ){
            // 整数型以外
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:It is unjust character string.'strExeNo'");
        }
    }
    // 渡されてきた【作業ID】は、整数型か？----

    //----渡されてきた【作業ID】の、対象のディレクトリが存在しているか、を確認する
    if( $boolExeContinue === true ){
        $strPadExeNo = str_pad( $strExeNo, $intNumPadding, "0", STR_PAD_LEFT );
        //----ansibleへのin情報が格納されたディレクトリへのパス
        $strDRStorageDirExeNoNS = "{$strRestApiLogPerOcheNS}/{$strPadExeNo}/in";
        // ディレクトリ存在チェック。エラー時は処理終了。
        $boolFocusValue = is_dir($strDRStorageDirExeNoNS);
        if( $boolFocusValue === false ){
            // 対象ディレクトリが存在しないと判定された場合
            $boolExeContinue           = false;
            $this->intResultStatusCode = 400;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Error:A directory does not exit.'{$strRestApiLogPerOcheNS}/{$strPadExeNo}/in'");
        }
    }
    

    if( $boolExeContinue === true ){
        //----PID等を格納するディレクトリへのパス
        $strRestApiLogPerExeNoNS = "{$strRestApiLogPerOcheNS}/{$strPadExeNo}/out";
        // ディレクトリ存在チェック。エラー時はディレクトリ作成／パーミッション変更を実施する。
        $boolFocusValue = is_dir($strRestApiLogPerExeNoNS);

        if( $boolFocusValue === false ){
            // 対象ディレクトリが存在しないと判定された場合
            $boolMkDir = mkdir($strRestApiLogPerExeNoNS,0755,true);
            if( $boolMkDir === true ){
                $boolChmod = chmod($strRestApiLogPerExeNoNS,0777);
                if( $boolChmod === false ){
                    // パーミッション変更失敗と判定された場合
                    $boolExeContinue           = false;
                    $this->intResultStatusCode = 500;
                    $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
                    $this->RestAPI_log("Error:Failed in a change of directory permission.'{$strRestApiLogPerOcheNS}/{$strPadExeNo}/out'");
                }
            }
            else{
                // ディレクトリ作成失敗と判定された場合
                $boolExeContinue           = false;
                $this->intResultStatusCode = 500;
                $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
                $this->RestAPI_log("Error:Failed in the \"mkdir\".'{$strRestApiLogPerOcheNS}/{$strPadExeNo}/out'");
            }
        }
        else{
            // ディレクトリが存在している場合。
            // パーミッションが0777かどうか
            $permission = substr(sprintf('%o', fileperms($strRestApiLogPerExeNoNS)), -4);

            if ( $permission !== '0777' ){
                // パーミッションが0777以外、パーミッション変更をおこなう
                $boolChmod = chmod($strRestApiLogPerExeNoNS,0777);
                if( $boolChmod === false ){
                    // パーミッション変更失敗と判定された場合
                    $boolExeContinue           = false;
                    $this->intResultStatusCode = 500;
                    $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
                    $this->RestAPI_log("Error:Failed in a change of directory permission.'{$strRestApiLogPerOcheNS}/{$strPadExeNo}/out'");
                }
            }
        }
    }

    if( $boolExeContinue === true ){
        if($vg_log_level == 'DEBUG'){
            $this->RestAPI_log("ORCHESTRATOR_SUB_ID:".$strOrchestratorSub_Id);
            $this->RestAPI_log("EXE_NO:".$strExeNo);
            $this->RestAPI_log("DRStoragePathPerExeNo:".$strDRStorageDirExeNoNS);
            $this->RestAPI_log("RestApiLogPerRest:".$strRestApiLogPerRestNS);
            $this->RestAPI_log("RestApiLogPerOrche:".$strRestApiLogPerOcheNS);
            $this->RestAPI_log("RestApiLogPerExeNo:".$strRestApiLogPerExeNoNS);
        }
    }

    if($vg_log_level == 'DEBUG') $this->RestAPI_log(print_r($aryReceptData, true));

    //----渡されてきた値のチェック

    //全おけ＆3REST・共通----

    if($vg_log_level == 'DEBUG') $this->RestAPI_log("END[".basename(__FILE__)."]");

?>
