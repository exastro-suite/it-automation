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
    //----webroot(05)
    function noRetFileWithColumnAccessAgent($objTable, $aryVariant=array(), &$arySetting=array()){
        //----旧名「fileUpLoadColumnPreUpload」
        global $g;

        $intControlDebugLevel01 = 250;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $boolDownloadMode = false;
        if( array_key_exists("mode",$_GET) === true ){
            if( $_GET['mode']=="dl" ){
                $boolDownloadMode = true;
            }
        }
        if( $boolDownloadMode === true ){
            hidddenFileDownload($objTable, $aryVariant, $arySetting);
        }
        else{
            //----関数内で処理が終了(exit)する
            noRetFileWithColumnUploadPrepare($objTable, $aryVariant, $arySetting);
        }
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        exit();
    }
    
    function noRetFileWithColumnUploadPrepare($objTable, $aryVariant=array(), &$arySetting=array()){
        global $g;

        $intControlDebugLevel01 = 250;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $ret_str = "";
        $retIntError = 0;

        $intErrorType = null;
        $varErrorOfFileupload = 0;
        $varErrorOfFrameProccess = 0;
        $aryErrMsgElement = array();

        $strTempFileFullname = "";
        $strTempFileBasename = "";
        
        $strOrgFileName = "";

        $errorSecureMsgBody = "";
        
        $ACRCM_id = "UNKNOWN";
        if( array_key_exists("menu_id", $g) === true ){
            $ACRCM_id = $g['menu_id'];
        }
        
        try{
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }

            //----テーブル設定の調査
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorType = 501;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            //テーブル設定の調査----
            
            if( array_key_exists("ary_forbidden_upload",$g) === true ){
                $aryForbiddenUpLoad = $g['ary_forbidden_upload'];
            }
            else{
                // デフォルトすら設定されていない
                $intErrorType = 601;
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            //----PHPシステム用一時ディレクトリ
            $strPhpSysTempDir = sys_get_temp_dir();
            //PHPシステム用一時ディレクトリ----

            if( count($_POST) == 0 && count($_FILES) == 0 ){
                //----1:php.iniによるファイルサイズ超過
                $intErrorType = 602;
                $varErrorOfFileupload = 1;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //1:php.iniによるファイルサイズ超過----
            }

            $varErrorOfFileupload = $_FILES['file']['error'];
            if( $_FILES['file']['error'] == 1 || $_FILES['file']['error'] == 2 ){
                //----1:php.iniによるファイルサイズ超過/2:name属性MAX_FILE_SIZEによるファイルサイズ超過
                $intErrorType = 602;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //1:php.iniによるファイルサイズ超過/2:name属性MAX_FILE_SIZEによるファイルサイズ超過----
            }
            else{
                if( is_uploaded_file($_FILES['file']['tmp_name']) ){
                    
                    
                }
                else{
                    $intErrorType = 603;
                    throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            $dst_file = "";

            //----受信時に作成された一時ファイル名(ディレクトリパス付＆ファイル名のみ)
            $strTempFileFullname = $_FILES['file']['tmp_name'];
            $strTempFileBasename = basename($strTempFileFullname);
            //受信時に作成された一時ファイル名(ディレクトリパス付＆ファイル名のみ)----

            //----送信前）ローカルでのファイル名
            $strOrgFileName = $_FILES['file']['name'];
            //送信前）ローカルでのファイル名----

            //----拡張子のチェック[loadTable個別設定条件によるチェック]
            $puCollectRequest = false;
            $puPOST_keys = array_keys($_POST);

            //----カラム種類のチェック
            $puColumns = $objTable->getColumns();

            //----ファイルアップロードカラムクラスか？
            foreach($puColumns as $key => $objColumn){
                if( is_a($objColumn, "FileUploadColumn") === true ){
                    $puUploadTgtColName = "file_id_".$objColumn->getID();
                    $puUploadFormatter = "frmFmt_".$objColumn->getID();
                    if( $objColumn->getColumnIDHidden() === true ){
                        $puUploadTgtColName = "file_id_".$objColumn->getIDSOP();
                        $puUploadFormatter = "frmFmt_".$objColumn->getIDSOP();
                    }
                    if( in_array($puUploadTgtColName, $puPOST_keys) === true ){
                        $objFileUpCol = $objColumn;
                        //
                        $dst_file = $objFileUpCol->getLAPathToPreUploadSave()."/".$strTempFileBasename;
                        $puCollectRequest = true;
                        //
                        break;
                    }
                }
            }
            //
            if( $puCollectRequest === false ){
                // ----ファイルアップロードカラムがなかった
                $intErrorType = 604;
                throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //カラム種類のチェック----

            //----一時ディレクトリの設定をチェック
            $intSettingOfTempDir = $objFileUpCol->checkDirectorySetting($objFileUpCol->getLAPathToPreUploadSave(),array("0777"));
            if( $intSettingOfTempDir === 1 ){
                //----一時ディレクトリの設定が不正
                $intErrorType = 605;
                throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            else if( $intSettingOfTempDir === 2 ){
                $intErrorType = 606;
                throw new Exception( '00000800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            else if( $intSettingOfTempDir === 3 ){
                $intErrorType = 607;
                throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //一時ディレクトリの設定をチェック----

            //----一時ファイルの大きさ、をチェック
            
            $intTempFilesize = filesize($strTempFileFullname);
            $intConfSetFilesize = $objFileUpCol->getMaxFileSize();
            
            if( $intTempFilesize <= $intConfSetFilesize ){
            }else{
                //----ロードテーブルで設定された以上のサイズだった
                $intErrorType = 608;
                $aryErrMsgElement[] = $intConfSetFilesize;
                throw new Exception( '00001000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //一時ファイルの大きさ、をチェック-----

            //----受信したファイル拡張子のチェック
            
            //
            //----個別ブラックリストチェック
            $postFix_flag = true;
            $aryBlackList = $objFileUpCol->getForbiddenFileTypes();
            $aryPostFix = array();
            $aryCheckType = array();
            foreach($aryBlackList as $aryValue){
                $strTargetType = $aryValue[0];
                $aryPostFix[] = $strTargetType;
                $aryCheckType[$strTargetType] = $aryValue[1];
            }
            $puFileTypes = implode(",", $aryPostFix);
            if( $puFileTypes == "" ){
                //----ブラックリストなし
                //ブラックリストなし----
            }
            else{
                //----ブラックリストの設定あり
                foreach($aryPostFix as $postFix){
                    $strCheckPostFix = $postFix;
                    $strCheckFileName = $strOrgFileName;
                    if($aryCheckType[$postFix]===true){
                        //----アルファベット部分（ロケールにより変動ある）を小文字へ変換
                        $strCheckFileName = strtolower($strOrgFileName);
                        $strCheckPostFix = strtolower($postFix);
                        //アルファベット部分（ロケールにより変動ある）を小文字へ変換----
                    }
                    if( preg_match('/'.$strCheckPostFix.'$/', $strCheckFileName) === 1 ){
                        $postFix_flag = false;
                        break;
                    }
                }
                if( $postFix_flag == false ){
                    $intErrorType = 619;
                    $aryErrMsgElement[] = $puFileTypes;
                    throw new Exception( '00001050-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                //----ブラックリストの設定あり----
            }
            //個別ブラックリストチェック----
            //
            //----ホワイトリストチェック
            $postFix_flag = false;
            $aryPostFix = $objFileUpCol->getAcceptFileTypes();
            $puFileTypes = implode(",", $aryPostFix);
            //
            if( $puFileTypes == "" ){
                $postFix_flag = true;
            }
            else{
                foreach($aryPostFix as $postFix){
                    if( preg_match('/'.$postFix.'$/', $strOrgFileName) === 1 ){
                        $postFix_flag = true;
                        break;
                    }
                }
                if( $postFix_flag == false ){
                    $intErrorType = 609;
                    $aryErrMsgElement[] = $puFileTypes;
                    throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            //ホワイトリストチェック----
            
            //受信したファイル拡張子のチェック----

            //拡張子のチェック[loadTable個別設定条件によるチェック]----

            //----ここから共通系ブラックリスト拡張子かどうかのチェック
            if( isset($aryForbiddenUpLoad) === true ){
                for($puFnv1 = 0; $puFnv1 < count($aryForbiddenUpLoad); $puFnv1 ++ ){
                    $strCheckFileName = strtolower($strOrgFileName);
                    $strCheckPostFix = strtolower($aryForbiddenUpLoad[$puFnv1]);
                    
                    if(strpos(strrev($strCheckFileName),strrev($strCheckPostFix)) === 0){
                        $intErrorType = 610;
                        $aryErrMsgElement[] = $aryForbiddenUpLoad[$puFnv1];
                        throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        break;
                    }
                    
                }
            }
            //----ここまで共通系ブラックリスト拡張子かどうかのチェック

            if( array_key_exists($puUploadFormatter, $_POST) === true ){
                $formatter_id = htmlspecialchars($_POST[$puUploadFormatter], ENT_QUOTES, "UTF-8");
            }
            else{
                $intErrorType = 611;
                throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $objListFormatter = $objTable->getFormatter($formatter_id);
            
            if( $objListFormatter === null ){
                $intErrorType = 612;
                throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( is_a($objListFormatter, "TableFormatter") !== true ){
                // ----テーブルフォーマッタクラスではない
                $intErrorType = 613;
                throw new Exception( '00001500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // テーブルフォーマッタクラスではない----
            }
            if( is_a($objListFormatter, "RegisterTableFormatter") !== true && is_a($objListFormatter, "UpdateTableFormatter") !== true ){
                // ----RegisterTableFormatterクラスでもUpdateTableFormatterクラスではない
                $intErrorType = 501;
                throw new Exception( '00001600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // RegisterTableFormatterクラスでもUpdateTableFormatterクラスではない----
            }
            
            //----ファイル名のチェック
            //[1]ディレクトリトラバーサル・チェック・・・冒頭でbasenameを利用済
            //[2]ヌル文字が入っていないこと
            $objMultiValidator = $objFileUpCol->getValidator();
            $tmpAryVariant = array();
            $tmpAryVariant['mode'] = $formatter_id;
            $tmpAryReqRegisterData = array();
            $tmpIntRINo = null;
            if($objMultiValidator->isValid($strOrgFileName, $tmpIntRINo, $tmpAryReqRegisterData, $tmpAryVariant)===true){
            }else{
                $intErrorType = 614;
                throw new Exception( '00001700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($tmpAryVariant);
            unset($tmpAryReqRegisterData);
            unset($tmpIntRINo);
            //ファイル名のチェック----
            //
            //----元ファイル名を、テキストファイルで保存
            $strFileFullnameOfOrgname = $objFileUpCol->getLAPathToPreUploadSave()."/fn_".$strTempFileBasename;
            $boolFileWrite = $objTable->writeAllToFileOnce($strFileFullnameOfOrgname, $strOrgFileName);
            if( $boolFileWrite === false ){
                $intErrorType = 615;
                throw new Exception( '00001800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $boolResultOfChmod = chmod($strFileFullnameOfOrgname, 0644);
            if( $boolResultOfChmod === false ){
                $intErrorType = 616;
                throw new Exception( '00001900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            //元ファイル名を、テキストファイルで保存----
            //
            $aryRetBodyOfTempFileCheck = $objFileUpCol->checkTempFileBeforeMoveOnPreLoad($strTempFileFullname, $strOrgFileName, $aryVariant, $arySetting);
            if( $aryRetBodyOfTempFileCheck[0] !== true || $aryRetBodyOfTempFileCheck[1] !== null ){
                $intErrorType = 401;
                throw new Exception( '00001950-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            //----正常系
            $boolResultOfFileMove = move_uploaded_file($strTempFileFullname, $dst_file);
            if( $boolResultOfFileMove === false ){
                $intErrorType = 617;
                throw new Exception( '00002000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            // ダウンロードしたファイルの暗号化が必要か判定
            $FileEncryptFunctionName = $objFileUpCol->getFileEncryptFunctionName();
            if($FileEncryptFunctionName !== false) {
                // ダウンロードしたファイルの暗号化
                $ret = $FileEncryptFunctionName($dst_file,$dst_file);
                if($ret === false) {
                    $intErrorType = 620;
                    throw new Exception( '00002000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                 
            }
            
            $boolResultOfChmod = chmod($dst_file, 0644);
            if( $boolResultOfFileMove === false ){
                $intErrorType = 618;
                throw new Exception( '00002100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $strColId = $objFileUpCol->getID();
            $strColMark = $strColId;
            if( $objFileUpCol->getColumnIDHidden() === true ){
                $strColMark = $objFileUpCol->getIDSOP();
            }

            $strOrgFileName = htmlspecialchars($strOrgFileName);
            $hiddenInputTag = "<input type=\"hidden\" name=\"{$strColMark}\" value=\"{$strOrgFileName}\" >";

            $ret_str  = $g['objMTS']->getSomeMessage("ITAWDCH-STD-471",array($strOrgFileName,$_FILES['file']['size']));
            $ret_str .= $hiddenInputTag;

            // アクセスログへ記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-472",array($ACRCM_id,$strColMark,$strTempFileBasename,$strOrgFileName)));

        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            $retIntError = $intErrorType;
            // ----一般訪問ユーザに見せてよいメッセージを作成
            
            switch($intErrorType){
                case 620: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-519");break;

                case 614: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-513",$aryErrMsgElement);break;
                case 610: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-509",$aryErrMsgElement);break;
                case 619: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-518",$aryErrMsgElement);break;
                case 609: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-508",$aryErrMsgElement);break;
                case 608: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-507",$aryErrMsgElement);break;
                case 603: $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-502");break;
                case 602:
                    switch($varErrorOfFileupload){
                        case "4" : $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-554");break;
                        case "3" : $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-553");break;
                        case "2" : $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-552");break;
                        case "1" : $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-551");break;
                        default  : $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
                    }
                    $retIntError = $varErrorOfFileupload;
                    break;
                case 401: $ret_str = $aryRetBodyOfTempFileCheck[3];break;
                default : $ret_str = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");break;
            }
            
            // 一般訪問ユーザに見せてよいメッセージを作成----
            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイズ向けメッセージを作成
                $tmp_DevStr = "";
                switch($intErrorType){
                    case 618: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-517");break;
                    case 617: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-516");break;
                    case 616: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-515");break;
                    case 615: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-514");break;
                    case 614: break;
                    case 613: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-512");break;
                    case 612: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-511");break;
                    case 611: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-510");break;
                    case 619: break;
                    case 610: break;
                    case 609: break;
                    case 608: break;
                    case 607: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-506");break;
                    case 606: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-505");break;
                    case 605: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-504");break;
                    case 604: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-503");break;
                    case 603: break;
                    case 602:
                        switch($varErrorOfFileupload){
                            case "8" : $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-557");break;
                            case "7" : $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-556");break;
                            case "6" : $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-555",$strPhpSysTempDir);break;
                            default  ; break;
                        }
                        break;
                    case 601: $tmp_DevStr .= $g['objMTS']->getSomeMessage("ITAWDCH-ERR-501");break;
                    case 401: 
                        foreach($aryRetBodyOfTempFileCheck[2] as $tmpStrMsg){
                            $tmp_DevStr .= $tmpStrMsg;
                        }
                        break;
                }
                if( 0 < strlen($tmp_DevStr) ) dev_log($tmp_DevStr, $intControlDebugLevel01);
                unset($tmp_DevStr);
                //ロードテーブルカスタマイズ向けメッセージを作成----
            }
            
            // アクセスログへ記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-349",array($ACRCM_id,$intErrorType,$varErrorOfFileupload)));
        }

        $file = array();
        $file['text'] = $ret_str;
        $file['error'] = $retIntError;
        $file['tmp_file_name'] = $strTempFileBasename;
        $file['org_file_name'] = $strOrgFileName;

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
        exit(htmlspecialchars(json_encode($file)));
    }
    //webroot(05)----
    function hidddenFileDownload($objTable, $aryVariant=array(), &$arySetting=array()){
        global $g;
        $intControlDebugLevel01 = 250;

        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        $boolProcessExecute = true;
        $strErrorMsg = "";

        $arrayColumn = $objTable->getColumns();

        $strRIColumnId = $objTable->getRowIdentifyColumnID();
        $strJnlSeqNoColId = $objTable->getRequiredJnlSeqNoColumnID();

        $ACRCM_id = "UNKNOWN";
        if( array_key_exists("menu_id", $g) !== true ){
            $ACRCM_id = $g['menu_id'];
        }

        try{
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }
            
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorType = 501;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }            
            
            if(array_key_exists("rin",$_GET)===true){
                $strRIN = htmlspecialchars($_GET['rin'], ENT_QUOTES, "UTF-8");
            }
            else{
                $intErrorType = 601;
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $arrayResult = selectRowForUpdate($objTable, $strRIN, 0, 0);
            $selectRowLength = $arrayResult[0];
            $editTgtRow = $arrayResult[1];
            $intErrorType = $arrayResult[2];
            
            if($selectRowLength != 1){
                $intErrorType = 602;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            // ----履歴のファイルが欲しい場合は、対象行を取り直し
            if( array_key_exists('jsn', $_GET) === true ){
                $strJSN = htmlspecialchars($_GET['jsn'], ENT_QUOTES, "UTF-8");
                $arrayColumn[$strJnlSeqNoColId]->setDBColumn(true);
                
                $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
                if( $objIntNumVali->isValid($strJSN) === false ){
                    $intErrorType = 603;
                    throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $objRIColumn = $arrayColumn[$strRIColumnId];
                $objRIColumn->addFilterValueForDTiS($strRIN, null);
                
                // RBAC アクセス権による絞り込みは不要
                $sql = generateJournalSelectSQL(2,$objTable,true);  
                $arrayFileterBody = $objTable->getFilterArray(true);
                $retArray = singleSQLExecuteAgent($sql, $arrayFileterBody, $strFxName);
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intTmpRowCount = 0;
                    
                    while ( $row = $objQuery->resultFetch() ){
                        // ----該当する履歴を回して、履歴通番を探す
                        if( $row[$strJnlSeqNoColId] == $strJSN ){
                            $intTmpRowCount += 1;
                            $editTgtRow = $row;
                        }
                        // 該当する履歴を回して、履歴通番を探す----
                    }
                    unset($objQuery);
                    if( $intTmpRowCount != 1 ){
                        $intErrorType = 604;
                        throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                else{
                    $intErrorType = 605;
                    throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            else{
                dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
            }
            // 履歴のファイルが欲しい場合は、対象行を取り直し----

            // ----どのカラムなのかを特定する
            if(array_key_exists("csn",$_GET)===true){
                $strColumnSeqNo = htmlspecialchars($_GET['csn'], ENT_QUOTES, "UTF-8");
                
                $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
                if( $objIntNumVali->isValid($strColumnSeqNo) === false ){
                    $intErrorType = 606;
                    throw new Exception( '00000800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $intVal = intval($strColumnSeqNo);
                
                $arrayKeys = array_keys($arrayColumn);
                
                $objCheckColumn = null;
                for($fnv1=0; $fnv1<= count($arrayColumn)-1 ; $fnv1++){
                    if($fnv1 == $intVal){
                        $objCheckColumn = $arrayColumn[$arrayKeys[$fnv1]];
                        break;
                    }
                }
                
                if($objCheckColumn === null){
                    $intErrorType = 607;
                    throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                else{
                    if(is_a($objCheckColumn,'FileUploadColumn')===true){
                        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
                    }
                    else{
                        $intErrorType = 608;
                        throw new Exception( '00001000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
            }
            else{
                $intErrorType = 609;
                throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            // どのカラムなのかを特定する----

            // ----ファイル名を確認する
            if( array_key_exists("fn",$_GET) === true ){
                $strReceptFileName = htmlspecialchars($_GET['fn'], ENT_QUOTES, "UTF-8");
                $strSavedFileName = $editTgtRow[$objCheckColumn->getID()];

                $strFilenameForSendBinary = rawurlencode($strSavedFileName);

                if( $strSavedFileName != $strReceptFileName ){
                    $intErrorType = 610;
                    throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            else{
                $intErrorType = 611;
                throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            // ファイル名を確認する----
            
            $strFilePath = $objCheckColumn->getLAPathToFUCItemPerRow($editTgtRow);
            
            if( file_exists($strFilePath) === false ){
                $intErrorType = 612;
                throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // ファイルダウンロード
            $content_length = filesize($strFilePath);
            header('Content-Disposition: attachment; filename*=UTF-8\'\''.rawurlencode(basename($strFilePath)));
            header("Content-Length: ".$content_length);
            header("Content-Type: application/octet-stream");
            header('Content-Transfer-Encoding: binary');
            header("Connection: close");
            ob_end_flush();
            readfile($strFilePath);

            // アクセスログへ記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4051",array($strFxName,$strFilePath)));
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                //----システムエラーが発生しました。
                default : $strErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001",$intErrorType);break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----
            if( 0 < $g['dev_log_developer'] ){
                //----ロードテーブルカスタマイザー向けメッセージを作成
                //ロードテーブルカスタマイザー向けメッセージを作成----
            }
            
            header("Content-Type: text/html; charset=UTF-8");
            print 
<<< EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<script language="JavaScript">
window.onload = function(){
    var strMsg = document.getElementById('msgbody').innerHTML;
    alert(strMsg);
}
</script>
<div id="msgbody" style="display:none">
{$strErrMsgBody}
</div>
</body>
</html>
EOD;
            // アクセスログへ記録
            web_log($g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody)));
        }

        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    }
?>
