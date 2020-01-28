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

/* ルートディレクトリの取得 */
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php');
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/FileUploadColumnFileAccess.php');

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-105090");
/*
Ansibleテンプレート
*/
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

    $table = new TableControlAgent('B_ANS_TEMPLATE_FILE','ANS_TEMPLATE_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106010"), 'B_ANS_TEMPLATE_FILE_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ANS_TEMPLATE_ID']->setSequenceID('B_ANS_TEMPLATE_FILE_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANS_TEMPLATE_FILE_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106020"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106030"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----


    $objVldt = new TextValidator(1, 256, false, '/^TPF_[_a-zA-Z0-9]+$/', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106045"));
    $objVldt->setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");
    $c = new TextColumn('ANS_TEMPLATE_VARS_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106050"));//エクセル・ヘッダでの説明
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    $c = new FileUploadColumn('ANS_TEMPLATE_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106060"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106070"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(20971520);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)

    $c->setRequired(true);//登録/更新時には、入力必須

    $table->addColumn($c);

    /* 変数定義 */
    $objVldt = new MultiTextValidator(0,4000,false);
    $c = new MultiTextColumn('VARS_LIST',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106075"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106076"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(false);
    $table->addColumn($c);

    $updObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global $g;
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $modeValue     = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if(isset($g['ROLE_ONLY_FLAG'])) {
            if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ) {
                $exeQueryData[$objColumn->getID()] = $g['ROLE_ONLY_FLAG'];
            } elseif( $modeValue=="DTUP_singleRecDelete") {
                $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                if( $modeValue_sub == 'off' ) {
                    $exeQueryData[$objColumn->getID()] = $g['ROLE_ONLY_FLAG'];
                }
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    /* 多段変数や読替変数定義有無 */
    $c = new TextColumn('ROLE_ONLY_FLAG',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106077"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-106078"));//エクセル・ヘッダでの説明
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);
    $c->setFunctionForEvent('beforeTableIUDAction',$updObjFunction);

    $table->addColumn($c);

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
                       ."WHERE ROW_ID IN (2100020001,2100020003,2100020005) ";

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
    $tmpAryColumn['ANS_TEMPLATE_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);


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

        $query = "";

        $boolExecuteContinue = true;
        $boolSystemErrorFlag = false;

        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        unset($g['ROLE_ONLY_FLAG']);

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $strVarsList    = isset($arrayVariant['edit_target_row']['VARS_LIST'])?
                                    $arrayVariant['edit_target_row']['VARS_LIST']:null;
            $strVarName     = isset($arrayVariant['edit_target_row']['ANS_TEMPLATE_VARS_NAME'])?
                                    $arrayVariant['edit_target_row']['ANS_TEMPLATE_VARS_NAME']:null;
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            $PkeyID = $strNumberForRI;
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $strVarsList  = array_key_exists('VARS_LIST',$arrayRegData)?
                               $arrayRegData['VARS_LIST']:null;
            $strVarName   = array_key_exists('ANS_TEMPLATE_VARS_NAME',$arrayRegData)?
                               $arrayRegData['ANS_TEMPLATE_VARS_NAME']:null;
            if($strModeId == "DTUP_singleRecUpdate") {
                $PkeyID = $strNumberForRI;
            } else {
                $PkeyID = array_key_exists('ANS_TEMPLATE_ID',$arrayRegData)?$arrayRegData['ANS_TEMPLATE_ID']:null;
            }
        }

        $LCA_vars_use     = false;
        $GBL_vars_info    = array();
        $Array_vars_use   = false;
        $Vars_list        = array();
        $Array_vars_list  = array();
        $VarVal_list      = array();

        switch($strModeId) {
        case "DTUP_singleRecDelete":
            if($modeValue_sub == 'off') {

                // 変数定義の解析結果を取得
                $fileObj = new TemplateVarsStructAnalFileAccess($g['objMTS'],$g['objDBCA']);

                // 変数定義の解析結果をファイルから取得
                // ファイルがない場合は、変数定義を解析し解析結果をファイルに保存
                $ret = $fileObj->getVarStructAnalysis($PkeyID,
                                                      $strVarName,
                                                      $strVarsList,
                                                      $Vars_list,
                                                      $Array_vars_list,
                                                      $LCA_vars_use,
                                                      $Array_vars_use,
                                                      $GBL_vars_info,
                                                      $VarVal_list);
                if($ret === false) {
                    $errmsg = $fileObj->GetLastError();
                    $retStrBody = $errmsg[0];
                    $retBool = false;
                    $boolExecuteContinue = false;
                }
                unset($fileObj);
            }
            break;
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
            // 変数定義を解析しファイルに保存
            $fileObj = new TemplateVarsStructAnalFileAccess($g['objMTS'],$g['objDBCA']);
            $ret = $fileObj->putVarStructAnalysis($PkeyID,
                                              $strVarName,
                                              $strVarsList,
                                              $Vars_list,
                                              $Array_vars_list,
                                              $LCA_vars_use,
                                              $Array_vars_use,
                                              $GBL_vars_info,
                                              $VarVal_list);

            if($ret === false) {
                $errmsg = $fileObj->GetLastError();
                $retStrBody = $errmsg[0];
                $retBool = false;
                $boolExecuteContinue = false;
            }
            unset($fileObj);
            break;
        }
        // 各テンプレート変数の定義変数で変数の構造に差異がないか確認
        if($retBool === true) {
            if(( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ) ||
               ( $strModeId == "DTUP_singleRecDelete" && $modeValue_sub == 'off')) {
                $dbObj = new CommonDBAccessCoreClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);
                $dbObj->ClearLastErrorMsg();
                // 各テンプレート変数の変数構造解析結果を取得
                $sqlBody = "SELECT * FROM B_ANS_TEMPLATE_FILE WHERE DISUSE_FLAG='0'";
                $arrayBind = array();
                $objQuery  = "";
                $ret = $dbObj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
                if($ret === false) {
                    web_log($dbObj->GetLastErrorMsg());
                    $retBool = false;
                    $retStrBody = $dbObj->GetLastErrorMsg();
                } else {
                    while($row = $objQuery->resultFetch()) {
                        // 自レコードはスキップ
                        if($row['ANS_TEMPLATE_ID'] == $PkeyID) {
                            continue;
                        }
                        $chk_PkeyID           = $row['ANS_TEMPLATE_ID'];
                        $chk_strVarName       = $row['ANS_TEMPLATE_VARS_NAME'];
                        $chk_strVarsList      = $row['VARS_LIST'];
                        $chk_LCA_vars_use     = false;
                        $chk_GBL_vars_info    = array();
                        $chk_Array_vars_use   = false;
                        $chk_Vars_list        = array();
                        $chk_Array_vars_list  = array();
                        $chk_VarVal_list      = array();

                        // 変数定義の解析結果を取得
                        $fileObj = new TemplateVarsStructAnalFileAccess($g['objMTS'],$g['objDBCA']);

                        // 変数定義の解析結果をファイルから取得
                        // ファイルがない場合は、変数定義を解析し解析結果をファイルに保存
                        $ret = $fileObj->getVarStructAnalysis($chk_PkeyID,
                                                              $chk_strVarName,
                                                              $chk_strVarsList,
                                                              $chk_Vars_list,
                                                              $chk_Array_vars_list,
                                                              $chk_LCA_vars_use,
                                                              $chk_Array_vars_use,
                                                              $chk_GBL_vars_info,
                                                              $chk_VarVal_list);
                        if($ret === false) {
                            $errmsg = $fileObj->GetLastError();
                            $retStrBody = $errmsg[0];
                            $retBool = false;
                            $boolExecuteContinue = false;
                        }
                        unset($fileObj);
                        if($retBool === true) {
                            $cmp_vars_list          = array();
                            $cmp_Array_vars_list    = array();

                            // 変数構造解析結果
                            $cmp_Vars_list[$chk_strVarName]['dummy']       = $chk_Vars_list;
                            $cmp_Array_vars_list[$chk_strVarName]['dummy'] = $chk_Array_vars_list;

                            // 自レコードの変数構造解析結果
                            $cmp_Vars_list[$strVarName]['dummy']           = $Vars_list;
                            $cmp_Array_vars_list[$strVarName]['dummy']     = $Array_vars_list;
    
                            $chkObj = new DefaultVarsFileAnalysis($objMTS);

                            $err_vars_list = array();

                            $ret = $chkObj->chkallVarsStruct($cmp_Vars_list, $cmp_Array_vars_list, $err_vars_list);
                            if($ret === false){
                                // 変数構造が一致していない変数があるか確認
                                foreach ($err_vars_list as $err_var_name=>$dummy){
                                    if(strlen($retStrBody)!=0) $retStrBody .= "\n";
                                    $retStrBody .= $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-6000046",array($err_var_name,$chk_strVarName));
                                    $retBool = false;
                                    $boolExecuteContinue = false;
                                }
                            }
                            unset($chkObj);
                        }
                        if($retBool === false) {
                            break;
                        }
                    }
                }    
                unset($objQuery);
                unset($dbObj);
            }
        }
        // 各テンプレート変数の定義変数で変数の構造に差異がないか確認
        if($retBool === true) {
            if(( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ) ||
               ( $strModeId == "DTUP_singleRecDelete" && $modeValue_sub == 'off')) {
                $global_vars_master_list = array();
                $template_master_list    = array();
                $obj = new VarStructAnalysisFileAccess($g['objMTS'],
                                                       $g['objDBCA'],
                                                       $global_vars_master_list,
                                                       $template_master_list,
                                                       '',
                                                       false,
                                                       true); // RolePackageAnalysisは変数構造の取得のみ

                // ロールパッケージで同じ変数を使用している場合に、変数定義が一致しているか判定
                $def_vars_list       = array();
                $def_vars_list["__ITA_DUMMY_ROLE_NAME__"]       = $Vars_list;
                $def_array_vars_list = array();
                $def_array_vars_list["__ITA_DUMMY_ROLE_NAME__"] = $Array_vars_list;
                $ret = $obj->AllRolePackageAnalysis(-1,
                                                    "__ITA_DUMMY_ROLE_PACKAGE_NAME__",
                                                    $def_vars_list,
                                                    $def_array_vars_list,
                                                    "ITAANSIBLEH-ERR-6000062");
                if($ret === false) {
                    $retBool    = false;
                    $errmsg     = $obj->getlasterror();
                    $retStrBody = $errmsg[0];
                    $boolExecuteContinue = false;
                }
                unset($obj);
            }
        }
        // LCA/多段変数を定義している場合、ロール以外でテンプレートファイルを使用していないか判定
        // 廃止以外は呼び出す
        if($retBool === true) {
            if(( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ) ||
               ( $strModeId == "DTUP_singleRecDelete" && $modeValue_sub == 'off')) {
                if($Array_vars_use === true || $LCA_vars_use === true) {

                    $g['ROLE_ONLY_FLAG'] = '1';

                    $dbObj = new CommonDBAccessCoreClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);
                    $dbObj->ClearLastErrorMsg();
                    // legacy/pionnerのplaybookでテンプレート変数を使用しているか確認
                    $sqlBody = sprintf("SELECT * FROM %s WHERE VAR_NAME='%s' AND FILE_ID in ('1','2') AND DISUSE_FLAG='0'"
                                       ,'B_ANS_COMVRAS_USLIST'
                                       ,$strVarName);
                    $arrayBind = array();
                    $objQuery  = "";
                    $ret = $dbObj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
                    if($ret === false) {
                        web_log($dbObj->GetLastErrorMsg());
                        $retBool = false;
                        $retStrBody = $dbObj->GetLastErrorMsg();
                    } else {
                        if($objQuery->effectedRowCount() != 0) {
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-6000022");
                            $retBool = false;
                            $boolExecuteContinue = false;
                            while($row = $objQuery->resultFetch()) {
                                switch($row['FILE_ID']) {
                                case '1':   // Legacy pllaybook
                                    $msgid = 'ITAANSIBLEH-ERR-6000023';
                                    break;
                                case '2':   // Pioneer 対話ファイル
                                    $msgid = 'ITAANSIBLEH-ERR-6000024';
                                    break;
                                }
                                if(strlen($retStrBody)!=0) $retStrBody .= "\n";
                                $retStrBody .= $g['objMTS']->getSomeMessage($msgid,array($row['CONTENTS_ID']));
                                $retBool = false;
                                $boolExecuteContinue = false;
                            }
                        }
                    }
                } else {
                    $g['ROLE_ONLY_FLAG'] = '0';
                }
            }
        }
        // グローバル変数が定義されている場合に共通変数利用リストを更新
        $FileID = '4';
        if($retBool === true) {
            if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ) {
                // 0件で廃止するレコードがある場合があるので、CommnVarsUsedListUpdateをCall
                $dbObj = new WebDBAccessClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);
                $ret = $dbObj->CommnVarsUsedListUpdate($PkeyID,$FileID,$GBL_vars_info);
                if($ret === false) {
                    web_log($dbObj->GetLastErrorMsg());
                    $retBool = false;
                    $retStrBody = $dbObj->GetLastErrorMsg();
                }
                unset($dbObj);
            }
            elseif($strModeId == "DTUP_singleRecDelete"){
                switch($modeValue_sub) {
                case 'on':
                case 'off':
                    // 廃止の場合、関連レコードを廃止
                    // 復活の場合、関連レコードを復活
                    $dbObj = new WebDBAccessClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);
                    $ret = $dbObj->CommnVarsUsedListDisuseSet($PkeyID,$FileID,$modeValue_sub);
                    if($ret === false) {
                        web_log($dbObj->GetLastErrorMsg());
                        $retBool = false;
                        $retStrBody = $dbObj->GetLastErrorMsg();
                    }
                    unset($dbObj);
                    break;
                }
            }
        }
        if( $boolSystemErrorFlag === true ){
            $retBool = false;
            //----システムエラー
            $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
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
