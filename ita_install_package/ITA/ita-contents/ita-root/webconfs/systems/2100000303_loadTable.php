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
//  【処理概要】
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

// 共通モジュールをロード
require_once ($root_dir_path . "/libs/commonlibs/common_required_check.php");

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;
    $root_dir_path = $g['root_dir_path'];

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-101020");

    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('C_STM_LIST','SYSTEM_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-101030"), 'C_STM_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['SYSTEM_ID']->setSequenceID('C_STM_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_STM_LIST_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);
    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-101040"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-101050"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $c = new IDColumn('HARDAWRE_TYPE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-101060"),'B_HARDAWRE_TYPE','HARDAWRE_TYPE_ID','HARDAWRE_TYPE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-101070"));//エクセル・ヘッダでの説明
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('HARDAWRE_TYPE_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_HARDAWRE_TYPE_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'HARDAWRE_TYPE_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'HARDAWRE_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    $objVldt = new TextValidator(1, 128, false, '/^[\._a-zA-Z0-9-]+$/', "");
    $objVldt->setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");
    $c = new TextColumn('HOSTNAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-101080"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-101090"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);
    $table->addColumn($c);

    $c = new TextColumn('IP_ADDRESS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102010"));
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102020"));//エクセル・ヘッダでの説明
    $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
    $objVldt = new TextValidator(7, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
    $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
    $c->setValidator($objVldt);
    $c->setUnique(true);
    $table->addColumn($c);

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102110"));

        if( $g['privilege'] === '1' ){
            $objFunction = function($rowData){
                $retLinkable = "disabled";
                if( array_key_exists('ETH_WOL_MAC_ADDRESS', $rowData) === true
                    && array_key_exists('ETH_WOL_NET_DEVICE', $rowData) === true ){
                    if( 0 < strlen($rowData['ETH_WOL_MAC_ADDRESS']) 
                        && 0 < strlen($rowData['ETH_WOL_NET_DEVICE']) ){
                        $retLinkable = "";
                    }
                }
                return $retLinkable;
            };

            // ロック解除
            $strLabelText1 = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102120");
            $strLabelText2 = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102130");
            $c = new LinkButtonColumn('ethWakeOrder',$strLabelText1, $strLabelText2, 'dummy');
            $c->setDBColumn(false);
            $c->setHiddenMainTableColumn(false);
            $c->setOutputType('print_table', new OutputType(new SortedTabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array(""))));
            $c->setEvent("print_table", "onClick", "ethWakeOrderSend", array('this',':SYSTEM_ID',':UPD_UPDATE_TIMESTAMP'));
            $c->getOutputType('print_journal_table')->setVisible(false);
            $cg->addColumn($c);
        }

        $strPattern = "/^(([0-9A-Za-z][0-9A-Za-z][:]){5}[0-9A-Za-z][0-9A-Za-z])*$/";
        $objVldt = new TextValidator(0, 17, false, $strPattern, "(xx:xx:xx:xx:xx:xx)");
        $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
        $c = new TextColumn('ETH_WOL_MAC_ADDRESS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102140"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102150"));//エクセル・ヘッダでの説明
        $c->setValidator($objVldt);
        $cg->addColumn($c);

        $objVldt = new SingleTextValidator(0,256,false);
        $c = new TextColumn('ETH_WOL_NET_DEVICE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102160"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102170"));//エクセル・ヘッダでの説明
        $c->setValidator($objVldt);
        $cg->addColumn($c);
    $table->addColumn($cg);

    $objVldt = new SingleTextValidator(0,30,false);
    $c = new TextColumn('LOGIN_USER',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102060"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    // ログイン
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102061"));
        //----ログインパスワード/管理のバリデーター定義
        $objFunction01 = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
            global $g;
            $retBool = true;
            $retStrBody = '';
            $strModeId = "";

            if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
                if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                    $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                    $strModeId = $aryTcaAction["ACTION_MODE"];
                }
            }

            // パスワードの取得
            $strLoginPw = "";
            if($strModeId == "DTUP_singleRecRegister"){
                list($strLoginPw ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayRegData,array('LOGIN_PW') , "");
            }
            else if( $strModeId == "DTUP_singleRecUpdate"){
                list($strLoginPw ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayRegData,array('LOGIN_PW') , "");
                if(strlen($strLoginPw) === 0){
                    list($strLoginPw ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW') ,"");
                }
            }
            else if($strModeId == "DTUP_singleRecDelete"){
                list($strLoginPw   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW')          ,"");
            }

            if( $strModeId == "DTUP_singleRecDelete" || $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
                if($value == 1){
                    if(strlen($strLoginPw) === 0 || (isset($arrayRegData["del_password_flag_COL_IDSOP_17"]) && $arrayRegData["del_password_flag_COL_IDSOP_17"] == "on")){
                        $retBool = false;
                        // [102071] = "ログインパスワード管理を●とする場合、ログインパス ワードの入力は必須です。"
                        $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102071");
                    }
                }
                else if(strlen($value) === 0){
                    // 何もしない
                }
                else{
                    //----想定外の値の場合
                    $retBool = false;
                    // [11404] = "利用できない値です。";
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11404");
                    //想定外の値の場合----
                }
            }
            $objClientValidator->setValidRule($retStrBody);
            return $retBool;
        };
        //ログインパスワード/管理のバリデーター定義----

        $objVarVali = new VariableValidator();
        $objVarVali->setFunctionForIsValid($objFunction01);

        $c = new IDColumn('LOGIN_PW_HOLD_FLAG',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102062"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102063"));//エクセル・ヘッダでの説明
        $c->addValidator($objVarVali);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('LOGIN_PW_HOLD_FLAG');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_FLAG_LIST_01_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'FLAG_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'FLAG_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

        $objFunction02 = function($objColumn, $strCallerName, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
            $boolRet = true;
            $intErrorType = null;
            $aryErrMsgBody = array();
            $strErrMsg = "";
            $strErrorBuf = "";
                   
            if( array_key_exists($objColumn->getID(), $exeQueryData) === true ){
                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if( $exeQueryData[$objColumn->getID()] != "" ){
                        //----値の入力があった場合
                        $strEncodeFunctionName = $objColumn->getEncodeFunctionName();
                        if( $strEncodeFunctionName != "" ){
                            $strEncodedValue = $strEncodeFunctionName($exeQueryData[$objColumn->getID()]);
                        }else{
                            $strEncodedValue = $exeQueryData[$objColumn->getID()];
                        }
                        $exeQueryData[$objColumn->getID()] = $strEncodedValue;
                        //値の入力があった場合----
                    }else{
                        //----値の入力がなかった場合
                        if( $modeValue=="DTUP_singleRecUpdate" ){
                            list($intPwHoldFlag       ,$boolRefKeyExists) = isSetInArrayNestThenAssign($exeQueryData,array('LOGIN_PW_HOLD_FLAG'),"");
                            if( $intPwHoldFlag == 1 ){
                                //----パスワード管理が●の場合
                                list($strLoginPwEnCoded   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('edit_target_row','LOGIN_PW')          ,"");
                                $exeQueryData[$objColumn->getID()] = $strLoginPwEnCoded;
                                //パスワード管理が●の場合----
                            }else{
                                //----パスワード管理が●ではない場合
                                $exeQueryData[$objColumn->getID()] = "";
                                //パスワード管理が●ではない場合----
                            }
                        }
                        //値の入力がなかった場合----
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }else{
                }
            }
            if($boolRet !== true) {
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
            }

            // 変更前と変更後のパスワードを判定し、違う場合にansible-vaultで暗号化した文字列を初期化
            global $g;
            $root_dir_path = $g['root_dir_path'];
            if ( empty($root_dir_path) ){
                $root_dir_temp = array();
                $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
                $root_dir_path = $root_dir_temp[0] . "ita-root";
            }

            require_once($root_dir_path . '/libs/commonlibs/common_php_functions.php');
            $boolRet = true;
            $intErrorType = null;
            $aryErrMsgBody = array();
            $strErrMsg = "";
            $strErrorBuf = "";
            $strFxName = "";
            if( array_key_exists($objColumn->getID(), $exeQueryData) === true ){
                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                switch($modeValue) {
                //case "DTUP_singleRecRegister":
                case "DTUP_singleRecUpdate":
                    $db_update = false;
                    // 変更前
                    $befor_pw = array_key_exists('LOGIN_PW',$aryVariant['edit_target_row'])?
                                                            $aryVariant['edit_target_row']['LOGIN_PW']:null;
                    // 変更後
                    $after_pw = array_key_exists('LOGIN_PW',$aryVariant['arySqlExe_update_table'])?
                                                            $aryVariant['arySqlExe_update_table']['LOGIN_PW']:null;

                    // パスワードの初期化は認証方式は関係ない
                    // ログインパスワードが管理でない場合にパスワードがクリア。管理の場合は残る
                    // 変更前と変更後のパスワードを判定して、違う場合にansible-vaultで暗号化した文字列を初期化
                    if($befor_pw != $after_pw) {
                        $db_update = true;
                    }
                    if($db_update === true) {
                        // ansible-vaultで暗号化した文字列を初期化
                        $strQuery = "UPDATE C_STM_LIST SET LOGIN_PW_ANSIBLE_VAULT = '' "
                                   ."WHERE SYSTEM_ID = " . $exeQueryData['SYSTEM_ID'];

                        $aryForBind = array();

                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                        if( $aryRetBody[0] !== true ){
                            $boolRet = false;
                            $intErrorType = 2;
                            $strErrMsg = $aryRetBody[2];
                        }
                    }
                }
            }
            $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
            return $retArray;
        };

        $objVldt = new SingleTextValidator(0,128,false);
        $c = new PasswordColumn('LOGIN_PW',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102070"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102080"));//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);
        $c->setValidator($objVldt);
        $c->setEncodeFunctionName("ky_encrypt");
        $c->setFunctionForEvent('beforeTableIUDAction',$objFunction02);
        $cg->addColumn($c);
    $table->addColumn($cg);

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-109011"));
        $c = new FileUploadColumn('CONN_SSH_KEY_FILE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-109006"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-109007"));
        $c->setMaxFileSize(4*1024*1024*1024);//単位はバイト
        $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
        $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
        $c->setFileHideMode(true);

        // CONN_SSH_KEY_FILEをアップロード時に「ky__encrypt」で暗号化する設定
        $c->setFileEncryptFunctionName("ky_file_encrypt");

        $cg->addColumn($c);

        $objVldt = new SingleTextValidator(0,256,false);
        $c = new PasswordColumn('SSH_KEY_FILE_PASSPHRASE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-109008"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-109009"));
        $c->setHiddenMainTableColumn(true);
        $c->setValidator($objVldt);
        $c->setEncodeFunctionName("ky_encrypt");

      $cg->addColumn($c);

    $table->addColumn($cg);

    $wanted_filename = "ita_ansible-driver";
    if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
        // Ansible利用情報
        $cg2 = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102024"));

            // WinRM接続プロトコル追加 
            // Ansible-Legacy/Legacy-Role利用情報
            $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102085"));
                //----汎用性の必要がないバリデーター定義
                $objFunction01 = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
                    global $g;
                    $retBool = true;
                    $retStrBody = '';
                    $strModeId = "";
                    
                    if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
                        if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                            $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                            $strModeId = $aryTcaAction["ACTION_MODE"];
                        }
                    }
                    
                    if($strModeId == "DTUP_singleRecDelete"){
                        $strLoginPw = "";
                        list($strLoginPwEnCoded   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW')          ,"");
                        if( $boolRefKeyExists === true ){
                            $strLoginPw = ky_decrypt($strLoginPwEnCoded);
                        }
                        list($intPwHoldFlag       ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW_HOLD_FLAG'),"");
                        list($value               ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_AUTH_TYPE')   ,"");
                    }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
                        list($strLoginPw   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayRegData,array('LOGIN_PW')          ,"");
                        list($intPwHoldFlag,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayRegData,array('LOGIN_PW_HOLD_FLAG'),"");
                    }

                    if( $strModeId == "DTUP_singleRecDelete" || $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
                        $boolPasswordInput = false;
                        $strErrorMsgPreBody = "";
                       
                        //if( strlen($value) == 0 || $value == 1 ){
                        if((strlen($value) == 0) || ($value == 1) || ($value == 3) || ($value == 4)){
                            //----鍵認証系の場合
                            if( $intPwHoldFlag == 1 ){
                                //----パスワード管理が●とされている場合
                                // パスワード管理側でも同等のチェックをしているので、チェックはしない
                                $boolPasswordInput = false;
                                //[102071] = "ログインパスワード管理を●とする場合、ログインパス ワードの入力は必須です。";
                                $strErrorMsgPreBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102071");
                                //パスワード管理が●とされている場合----
                            }else{
                                //[102072] = "ログインパスワード管理を●としない場合、ログインパ スワードの入力は禁止です。";
                                $strErrorMsgPreBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102072");
                            }
                            //入力値がない場合または鍵認証の場合----
                        }else if(( $value == 2 ) || ($value == 5)) {

                            //----パスワード認証の場合
                            if( $intPwHoldFlag == 1 ){
                                //----パスワード管理が●とされている場合
                                // パスワード管理側でも同等のチェックをしているので、チェックはしない
                                $boolPasswordInput = false;
                                //[102073] = "認証方式がパスワード認証の場合、ログインパスワード の入力は必須です。";
                                $strErrorMsgPreBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102073");
                                //パスワード管理が●とされている場合----
                            }else{
                                //----パスワード管理が●とされていない場合
                                $retBool = false;
                                //[102074] = "認証方式がパスワード認証の場合、ログインパスワード の管理は必須です。";
                                $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102074");
                                //パスワード管理が●とされていない場合----
                            }
                            //パスワード認証の場合----
                        }else{
                            //----想定外の値の場合
                            $retBool = false;
                            //[102075] = "認証方式の入力値が不正です。";
                            $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-102075");
                            //想定外の値の場合----
                        }
                        
                        if( $retBool === true ){
                            if( $boolPasswordInput === true ){
                                //----ログインパスワードが必須入力の場合
                                if( strlen($strLoginPw) == 0 ){
                                    $retBool = false;
                                    $retStrBody = $strErrorMsgPreBody;
                                }
                                
                                //----更新の場合（敗者復活的なチェック）
                                if( $retBool === false && $strModeId == "DTUP_singleRecUpdate" ){
                                    //----直近入力値は0文字だった場合
                                    list($strLoginPwEnCoded   ,$boolRefKeyExists) = isSetInArrayNestThenAssign($arrayVariant,array('edit_target_row','LOGIN_PW')          ,"");
                                    if( $boolRefKeyExists === true ){
                                        $strLoginPw = ky_decrypt($strLoginPwEnCoded);
                                        if( 0 < strlen($strLoginPw) ){
                                            $retBool = true;
                                        }
                                    }
                                    //直近入力値は0文字だった場合----
                                }
                                //更新の場合（敗者復活的なチェック）----
                                
                                //ログインパスワードが必須入力の場合----
                            }
                            else{
                                //----ログインパスワードが必須入力ではない場合
                                // 何もしない
                                //ログインパスワードが必須入力ではない場合----
                            }
                        }
                    }
                    $objClientValidator->setValidRule($retStrBody);
                    return $retBool;
                };
                //汎用性の必要がないバリデーター定義----
                
                $objVarVali = new VariableValidator();
                //----廃止/復活時にも、バリデーションチェックを走らせるためのフラグをON
                $objVarVali->setCheckType("DeleteTableFormatterOff");
                $objVarVali->setErrShowPrefix(false);
                $objVarVali->setFunctionForIsValid($objFunction01);
                
                $c = new IDColumn('LOGIN_AUTH_TYPE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102088"),'B_LOGIN_AUTH_TYPE','LOGIN_AUTH_TYPE_ID','LOGIN_AUTH_TYPE_NAME','',array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1') );
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102089"));//エクセル・ヘッダでの説明
                $c->addValidator($objVarVali);
                $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
                $objOT->setFirstSearchValueOwnerColumnID('LOGIN_AUTH_TYPE');
                $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_LOGIN_AUTH_TYPE_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'LOGIN_AUTH_TYPE_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'LOGIN_AUTH_TYPE_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                    )
                );
                $objOT->setTraceQuery($aryTraceQuery);
                $c->setOutputType('print_journal_table',$objOT);
                $cg->addColumn($c);

                // WinRM接続情報
                $cg3 = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-104600"));
                    // ポート番号
                    $c = new NumColumn('WINRM_PORT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104605"));
                    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104606"));
                    $c->setSubtotalFlag(false);
                    $c->setValidator(new IntNumValidator(1,65535));

                    $cg3->addColumn($c);

                    // サーバー証明書
                    $c = new FileUploadColumn('WINRM_SSL_CA_FILE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104610"));
                    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104611"));
                    $c->setMaxFileSize(4*1024*1024*1024);//単位はバイト
                    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
                    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
                    $c->setFileHideMode(true);

                    // WINRM_SSL_CA_FILEをアップロード時に「ky_encrypt」で暗号化する設定
                    $c->setFileEncryptFunctionName("ky_file_encrypt");

                  $cg3->addColumn($c);

                $cg->addColumn($cg3);

        $cg2->addColumn($cg);

            $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-102025") );

                $c = new IDColumn('PROTOCOL_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102030"),'B_PROTOCOL','PROTOCOL_ID','PROTOCOL_NAME','');
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102040"));//エクセル・ヘッダでの説明
                $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
                $objOT->setFirstSearchValueOwnerColumnID('PROTOCOL_ID');
                $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_PROTOCOL_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'PROTOCOL_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'PROTOCOL_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                    )
                );
                $objOT->setTraceQuery($aryTraceQuery);
                $c->setOutputType('print_journal_table',$objOT);
                $cg->addColumn($c);

                $c = new IDColumn('OS_TYPE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102090"),'B_OS_TYPE','OS_TYPE_ID','OS_TYPE_NAME','');
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103010"));//エクセル・ヘッダでの説明
                $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
                $objOT->setFirstSearchValueOwnerColumnID('OS_TYPE_ID');
                $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_OS_TYPE_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'OS_TYPE_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'OS_TYPE_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                    )
                );
                $objOT->setTraceQuery($aryTraceQuery);
                $c->setOutputType('print_journal_table',$objOT);
                $cg->addColumn($c);

                $c = new IDColumn('PIONEER_LANG_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-102100"),'B_ANS_PNS_LANG_MASTER','ID','NAME','',array('SELECT_ADD_FOR_ORDER'=>array('ID'),'ORDER'=>'ORDER BY ADD_SELECT_1') );
                $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-102101"));//エクセル・ヘッダでの説明
                $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
                $objOT->setFirstSearchValueOwnerColumnID('PIONEER_LANG_ID');
                $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_ANS_PNS_LANG_MASTER_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                    )
                );
                $objOT->setTraceQuery($aryTraceQuery);
                $c->setOutputType('print_journal_table',$objOT);
                $cg->addColumn($c);

        $cg2->addColumn($cg);

        $objVldt = new SingleTextValidator(0,512,false);
        $c = new TextColumn('SSH_EXTRA_ARGS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104615"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104616"));
        $c->setValidator($objVldt);
        $cg2->addColumn($c);

        $objVldt = new MultiTextValidator(0,512,false);
        $c = new MultiTextColumn('HOSTS_EXTRA_ARGS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104620"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104621"));
        $c->setValidator($objVldt);
        $cg2->addColumn($c);

        // AnsibleTower利用情報
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-102026"));

           // インスタンスグループ
           $c = new IDColumn('ANSTWR_INSTANCE_GROUP_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104630"),
                              'B_ANS_TWR_INSTANCE_GROUP', 'INSTANCE_GROUP_NAME', 'INSTANCE_GROUP_NAME','');
           $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104631"));
           $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
           $objOT->setFirstSearchValueOwnerColumnID('ANSTWR_INSTANCE_GROUP_NAME');
           $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_ANS_TWR_INSTANCE_GROUP_JNL',
               'TTT_SEARCH_KEY_COLUMN_ID'=>'INSTANCE_GROUP_NAME',
               'TTT_GET_TARGET_COLUMN_ID'=>'INSTANCE_GROUP_NAME',
               'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
               'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
               'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
               )
           );
           $objOT->setTraceQuery($aryTraceQuery);
           $c->setOutputType('print_journal_table',$objOT);
           $cg->addColumn($c);

           // 認証情報　接続タイプ
           $c = new IDColumn('CREDENTIAL_TYPE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103051"),
                              'B_ANS_TWR_CREDENTIAL_TYPE', 'CREDENTIAL_TYPE_ID', 'CREDENTIAL_TYPE_NAME','');
           $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103052"));
           //$c->getOutputType('update_table')->setOverrideInputValue(1);
           $c->setDefaultValue("register_table", 1);
           $c->setRequired(true);//登録/更新時には、入力必須
           $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
           $objOT->setFirstSearchValueOwnerColumnID('CREDENTIAL_TYPE_ID');
           $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_ANS_TWR_CREDENTIAL_TYPE_JNL',
               'TTT_SEARCH_KEY_COLUMN_ID'=>'CREDENTIAL_TYPE_ID',
               'TTT_GET_TARGET_COLUMN_ID'=>'CREDENTIAL_TYPE_NAME',
               'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
               'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
               'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
               )
           );
           $objOT->setTraceQuery($aryTraceQuery);
           $c->setOutputType('print_journal_table',$objOT);
           $cg->addColumn($c);

        $cg2->addColumn($cg);

        $table->addColumn($cg2);
    }

    $wanted_filename = "ita_cobbler-driver";
    if( file_exists($root_dir_path."/libs/release/".$wanted_filename) ){
        $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITABASEH-MNU-103015") );

            $c = new IDColumn('COBBLER_PROFILE_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103020"),'C_COBBLER_PROFILE','COBBLER_PROFILE_ID','COBBLER_PROFILE_NAME','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103030"));//エクセル・ヘッダでの説明
            $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
            $objOT->setFirstSearchValueOwnerColumnID('COBBLER_PROFILE_ID');
            $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'C_COBBLER_PROFILE_JNL',
                'TTT_SEARCH_KEY_COLUMN_ID'=>'COBBLER_PROFILE_ID',
                'TTT_GET_TARGET_COLUMN_ID'=>'COBBLER_PROFILE_NAME',
                'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                )
            );
            $objOT->setTraceQuery($aryTraceQuery);
            $c->setOutputType('print_journal_table',$objOT);
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,256,false);
            $c = new TextColumn('INTERFACE_TYPE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103040"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103050"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            $strPattern = "/^(([0-9A-Za-z][0-9A-Za-z][:]){5}[0-9A-Za-z][0-9A-Za-z])*$/";
            $objVldt = new TextValidator(0, 17, false, $strPattern, "(xx:xx:xx:xx:xx:xx)");
            $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
            $c = new TextColumn('MAC_ADDRESS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103060"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103070"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $c->setUnique(true);
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,15,false);
            $c = new TextColumn('NETMASK',$g['objMTS']->getSomeMessage("ITABASEH-MNU-103080"));
            $c->setRequired(false); // 非必須
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-103090"));//エクセル・ヘッダでの説明
            $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
            $objVldt = new TextValidator(0, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
            $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            $objVldt = new SingleTextValidator(0,15,false);
            $c = new TextColumn('GATEWAY',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104010"));
            $c->setRequired(false); // 非必須
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104020"));//エクセル・ヘッダでの説明
            $strPattern = "/^$|^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
            $objVldt = new TextValidator(0, 15, false, $strPattern, "xxx.xxx.xxx.xxx");
            $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            $objVldt = new TextValidator(0, 1, false, "/^$|^(0|1)$/", " [0|1] ");
            $c = new TextColumn('STATIC',$g['objMTS']->getSomeMessage("ITABASEH-MNU-104030"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-104040"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);

        $table->addColumn($cg);
    }

    // 登録/更新/廃止/復活があった場合、データベースを更新した事をマークする。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

            $strQuery = "UPDATE A_PROC_LOADED_LIST "
                       ."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
                       ."WHERE ROW_ID IN (2100020001,2100020005,2100020002,2100020004,2100020006,2100080002) ";

            $aryForBind = array();

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

            if( $aryRetBody[0] !== true ){
                $boolRet = false;
                $strErrMsg = $aryRetBody[2];
                $intErrorType = 500;
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['SYSTEM_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){

        global $g;
        global $root_dir_path;
        $retBool = true;
        $retStrBody = '';

        $strModeId = "";
        $modeValue_sub = "";

        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        // $arrayRegDataはUI入力ベースの情報
        // $arrayVariant['edit_target_row']はDBに登録済みの情報
        if($strModeId == "DTUP_singleRecRegister") {

            // ホスト名
            $strhostname   = array_key_exists('HOSTNAME',$arrayRegData)?
                                $arrayRegData['HOSTNAME']:null;

            // 認証方式の設定値取得
            $strAuthMode   = array_key_exists('LOGIN_AUTH_TYPE',$arrayRegData)?
                                $arrayRegData['LOGIN_AUTH_TYPE']:null;

            // ユーザーIDの設定値取得
            $strLoginUser  = array_key_exists('LOGIN_USER',$arrayRegData)?
                                $arrayRegData['LOGIN_USER']:null;

            // パスワード管理の設定値取得
            $strPasswdHoldFlag  = array_key_exists('LOGIN_PW_HOLD_FLAG',$arrayRegData)?
                                     $arrayRegData['LOGIN_PW_HOLD_FLAG']:null;

            // パスワードの設定値取得
            $strPasswd     = array_key_exists('LOGIN_PW',$arrayRegData)?
                                $arrayRegData['LOGIN_PW']:null;

            // パスフレーズの設定値取得
            $strPassphrase = array_key_exists('SSH_KEY_FILE_PASSPHRASE',$arrayRegData)?
                                $arrayRegData['SSH_KEY_FILE_PASSPHRASE']:null;

            // 公開鍵ファイルの設定値取得
            $strsshKeyFile = array_key_exists('CONN_SSH_KEY_FILE',$arrayRegData)?
                                $arrayRegData['CONN_SSH_KEY_FILE']:null;

            // Pioneerプロトコルの設定値取得
            $strProtocolID = array_key_exists('PROTOCOL_ID',$arrayRegData)?
                                $arrayRegData['PROTOCOL_ID']:null;

        } elseif ($strModeId == "DTUP_singleRecDelete") {

            // ホスト名
            $strhostname        = isset($arrayVariant['edit_target_row']['HOSTNAME'])?
                                        $arrayVariant['edit_target_row']['HOSTNAME']:null;

            // 認証方式の設定値取得
            $strAuthMode        = isset($arrayVariant['edit_target_row']['LOGIN_AUTH_TYPE'])?
                                        $arrayVariant['edit_target_row']['LOGIN_AUTH_TYPE']:null;

            // ユーザーIDの設定値取得
            $strLoginUser       = isset($arrayVariant['edit_target_row']['LOGIN_USER'])?
                                        $arrayVariant['edit_target_row']['LOGIN_USER']:null;

            // パスワード管理の設定値取得
            $strPasswdHoldFlag  = isset($arrayVariant['edit_target_row']['LOGIN_PW_HOLD_FLAG'])?
                                        $arrayVariant['edit_target_row']['LOGIN_PW_HOLD_FLAG']:null;

            // パスワードの設定値取得
            $strPasswd          = isset($arrayVariant['edit_target_row']['LOGIN_PW'])?
                                        $arrayVariant['edit_target_row']['LOGIN_PW']:null;

            // パスフレーズの設定値取得
            $strPassphrase      = isset($arrayVariant['edit_target_row']['SSH_KEY_FILE_PASSPHRASE'])?
                                        $arrayVariant['edit_target_row']['SSH_KEY_FILE_PASSPHRASE']:null;

            // 公開鍵ファイルの設定値取得
            $strsshKeyFile      = isset($arrayVariant['edit_target_row']['CONN_SSH_KEY_FILE'])?
                                        $arrayVariant['edit_target_row']['CONN_SSH_KEY_FILE']:null;

            // Pioneerプロトコルの設定値取得
            $strProtocolID      = isset($arrayVariant['edit_target_row']['PROTOCOL_ID'])?
                                        $arrayVariant['edit_target_row']['PROTOCOL_ID']:null;

        } elseif ($strModeId == "DTUP_singleRecUpdate") {

            // ホスト名
            $strhostname   = array_key_exists('HOSTNAME',$arrayRegData)?
                                $arrayRegData['HOSTNAME']:null;

            // 認証方式の設定値取得
            $strAuthMode   = array_key_exists('LOGIN_AUTH_TYPE',$arrayRegData)?
                                $arrayRegData['LOGIN_AUTH_TYPE']:null;

            // ユーザーIDの設定値取得
            $strLoginUser  = array_key_exists('LOGIN_USER',$arrayRegData)?
                                $arrayRegData['LOGIN_USER']:null;

            // パスワード管理の設定値取得
            $strPasswdHoldFlag  = array_key_exists('LOGIN_PW_HOLD_FLAG',$arrayRegData)?
                                     $arrayRegData['LOGIN_PW_HOLD_FLAG']:null;

            // パスワードの設定値取得
            // PasswordColumnはデータの更新がないと$arrayRegDataの設定は空になっているので
            // パスワードが更新されているか判定
            // 更新されていない場合は設定済みのパスワード($arrayVariant['edit_target_row'])取得
            $strPasswd     = array_key_exists('LOGIN_PW',$arrayRegData)?
                                $arrayRegData['LOGIN_PW']:null;
            if($strPasswd == "") {
                $strPasswd     = isset($arrayVariant['edit_target_row']['LOGIN_PW'])?
                                       $arrayVariant['edit_target_row']['LOGIN_PW']:null;
            }
            // パスフレーズの設定値取得
            // PasswordColumnはデータの更新がないと$arrayRegDataの設定は空になっているので
            // パスフレーズが更新されているか判定
            // 更新されていない場合は設定済みのパスフレーズ($arrayVariant['edit_target_row'])取得
            $strPassphrase = array_key_exists('SSH_KEY_FILE_PASSPHRASE',$arrayRegData)?
                                $arrayRegData['SSH_KEY_FILE_PASSPHRASE']:null;
            if($strPassphrase== "") {
                $strPassphrase = isset($arrayVariant['edit_target_row']['SSH_KEY_FILE_PASSPHRASE'])?
                                       $arrayVariant['edit_target_row']['SSH_KEY_FILE_PASSPHRASE']:null;
            }
            // 公開鍵ファイルの設定値取得
            // FileUploadColumnはファイルの更新がないと$arrayRegDataの設定は空になっているので
            // ダウンロード済みのファイルが削除されていると$arrayRegData['del_flag_COL_IDSOP_xx']がonになる
            // 更新されていない場合は設定済みのファイル名($arrayVariant['edit_target_row'])を取得
            $strsshKeyFileDel  = array_key_exists('del_flag_COL_IDSOP_18',$arrayRegData)?
                                    $arrayRegData['del_flag_COL_IDSOP_18']:null;
            if($strsshKeyFileDel == 'on') {
                $strsshKeyFile = "";
            } else {
                // 公開鍵ファイルが更新されているか判定
                $strsshKeyFile = array_key_exists('CONN_SSH_KEY_FILE',$arrayRegData)?
                                    $arrayRegData['CONN_SSH_KEY_FILE']:null;
                if($strsshKeyFile == "") {
                    $strsshKeyFile= isset($arrayVariant['edit_target_row']['CONN_SSH_KEY_FILE'])?
                                          $arrayVariant['edit_target_row']['CONN_SSH_KEY_FILE']:null;
                }
            }

            // Pioneerプロトコルの設定値取得
            $strProtocolID = array_key_exists('PROTOCOL_ID',$arrayRegData)?
                                $arrayRegData['PROTOCOL_ID']:null;
        }

        switch($strModeId) {
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
        case "DTUP_singleRecDelete":
            //ホスト名が数値文字列か判定
            if(is_numeric($strhostname) === true) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-101081");
                $objClientValidator->setValidRule($retStrBody);
                $retBool = false;
                return $retBool;
            }
            // 選択されている認証方式に応じた必須入力をチェック
            // 但し、パスワード管理・パスワードは既存のチェック処理で必須入力判定
            $errMsgParameterAry = array();
            $DriverID = "";
            $chkobj = new AuthTypeParameterRequiredCheck();
            $retStrBody = $chkobj->DeviceListAuthTypeRequiredParameterCheck($chkobj->chkType_Loadtable_TowerHostList,$g['objMTS'],$errMsgParameterAry,$strAuthMode,$strLoginUser,$strPasswdHoldFlag,$strPasswd,$strsshKeyFile,$strPassphrase,$DriverID,$strProtocolID);
            if($retStrBody === true) {
                $retStrBody = "";
            } else {
                $retBool = false;
            }
            break;
        }
        if($retBool===false){
            $objClientValidator->setValidRule($retStrBody);
        }
        return $retBool;
    };

    $objVarVali = new VariableValidator();
    $objVarVali->setErrShowPrefix(false);
    $objVarVali->setFunctionForIsValid($objFunction);
    $objVarVali->setVariantForIsValid(array());

    $objLU4UColumn->addValidator($objVarVali);
    //組み合わせバリデータ----

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
