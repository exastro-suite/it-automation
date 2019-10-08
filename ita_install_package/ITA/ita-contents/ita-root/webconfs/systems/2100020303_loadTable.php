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
//    ・Ansible（Legacy Role）ロールパッケージ名管理
//
//////////////////////////////////////////////////////////////////////

if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

// 共通モジュールをロード
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php');


$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605010");
/*
Ansible（Legacy Role）ロールパッケージ一覧
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

    $table = new TableControlAgent('B_ANSIBLE_LRL_ROLE_PACKAGE','ROLE_PACKAGE_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605020"), 'B_ANSIBLE_LRL_ROLE_PACKAGE_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANSIBLE_LRL_ROLE_PACKAGE_JSQ');
    $tmpAryColumn['ROLE_PACKAGE_ID']->setSequenceID('B_ANSIBLE_LRL_ROLE_PACKAGE_RIC');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605040"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----


    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('ROLE_PACKAGE_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605060"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);
    $table->addColumn($c);

    $c = new FileUploadColumn('ROLE_PACKAGE_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605080"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(268435456);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);

    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)

    $c->setRequired(true);//登録/更新時には、入力必須

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
                       ."WHERE ROW_ID IN (2100020005) ";

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
    $tmpAryColumn['ROLE_PACKAGE_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
        global $g;
        global $root_dir_path;
        $retBool       = true;
        $retStrBody    = '';
        $intErrorType  = 0;
        $aryErrMsgBody = array();

        $strModeId = "";
        $modeValue_sub = "";

        $query = "";

        $boolExecuteContinue = true;
        $boolSystemErrorFlag = false;

        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        $tmpFile        = '';
        $TPFVarListfile = '';
        $FileID         = '3';

        $modeValue_sub = "";
        if($strModeId == "DTUP_singleRecDelete"){
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            // off:復活　on:廃止
            $PkeyID = $strNumberForRI;
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            if($strModeId == "DTUP_singleRecUpdate") {
                $PkeyID = $strNumberForRI;
                $role_package_name = array_key_exists('ROLE_PACKAGE_NAME',$arrayRegData)?$arrayRegData['ROLE_PACKAGE_NAME']:null;
            } else {
                $PkeyID = array_key_exists('ROLE_PACKAGE_ID',$arrayRegData)?$arrayRegData['ROLE_PACKAGE_ID']:null;
                $role_package_name = array_key_exists('ROLE_PACKAGE_ID',$arrayRegData)?$arrayRegData['ROLE_PACKAGE_NAME']:null;
            }
            $tmpFile      = array_key_exists('tmp_file_COL_IDSOP_8',$arrayRegData)?
                               $arrayRegData['tmp_file_COL_IDSOP_8']:null;
            $strTempFileFullname = $root_dir_path . "/temp/file_up_column/" . $tmpFile;
        }
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php');
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php');

        $def_vars_list        = array();
        $def_varsval_list     = array();
        $def_array_vars_list  = array();
        $cpf_vars_chk         = array();
        $cpf_vars_list        = array();
        $tpf_vars_chk         = array();
        $tpf_vars_list        = array();
        $gbl_vars_list        = array();
        $ITA2User_var_list    = array();
        $User2ITA_var_list    = array();
        $save_vars_array      = array();
        $disuse_role_chk      = true;
        
        $global_vars_master_list = array();
        $template_master_list    = array();
        $obj = new VarStructAnalysisFileAccess($g['objMTS'],
                                               $g['objDBCA'],
                                               $global_vars_master_list,
                                               $template_master_list,
                                               '',
                                               false,
                                               false);

        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ) {
                if(strlen($tmpFile) != 0) {
                    // ロールパッケージ解析
                    list($retBool,
                         $intErrorType,
                         $aryErrMsgBody,
                         $retStrBody) = $obj->RolePackageAnalysis($strTempFileFullname,
                                                                  $role_package_name,
                                                                  $PkeyID,
                                                                  $disuse_role_chk,
                                                                  $def_vars_list,
                                                                  $def_varsval_list,
                                                                  $def_array_vars_list,
                                                                  true,
                                                                  $cpf_vars_list,
                                                                  true,
                                                                  $tpf_vars_list,
                                                                  $gbl_vars_list,
                                                                  $ITA2User_var_list,
                                                                  $User2ITA_var_list,
                                                                  $save_vars_array);
                    if($retBool === true) {
                        // 変数構造解析結果を退避
                        // 退避ディレクトリ作成・確認
                        $dir = $obj->CreateVarStructAnalJsonStringFileDir($PkeyID);

                        // 退避ファイル名取得
                        $path = $obj->getVarStructAnalJsonStringFileName($PkeyID);

                        // ファイルに退避
                        $ret = $obj->putVarStructAnalJsonStringFileInfo($path,
                                                                        $def_vars_list,
                                                                        $def_array_vars_list,
                                                                        $tpf_vars_list,
                                                                        $ITA2User_var_list,
                                                                        $gbl_vars_list);
                        if($ret === false)
                        {
                            $retBool    = false;
                            $retStrBody = $g['objMTS']->getSomeMessage('ITAANSIBLEH-ERR-6000018');
                        } 
                    }
                    if($retBool === true) {
                        $dbObj = new WebDBAccessClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);
                        $ret = $dbObj->CommnVarsUsedListUpdate($PkeyID,$FileID,$save_vars_array);
                        if($ret === false) {
                            web_log($dbObj->GetLastErrorMsg());
                            $retBool = false;
                            $retStrBody = $dbObj->GetLastErrorMsg();
                        }
                        unset($dbObj);
                    }
                }
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
        if($retBool === true) {
            // 登録・更新・復活の場合
            if( ($strModeId == "DTUP_singleRecUpdate")   || 
                ($strModeId == "DTUP_singleRecRegister") ||
               (($strModeId == "DTUP_singleRecDelete")   &&
                ($modeValue_sub == 'off'))) {
                if(strlen($tmpFile) == 0) {
                    // 変数構造解析結果を取得
                    $zipfile           = $arrayVariant['edit_target_row']['ROLE_PACKAGE_FILE'];
                    $role_package_name = $arrayVariant['edit_target_row']['ROLE_PACKAGE_NAME'];
                    $ret = $obj->getVarStructAnalInfo($PkeyID,
                                                      $role_package_name,
                                                      $zipfile,
                                                      $def_vars_list,
                                                      $def_array_vars_list,
                                                      $tpf_vars_list,
                                                      $ITA2User_var_list,
                                                      $gbl_vars_list);
                    if($ret === false) {
                        $retBool    = false;
                        $errmsg     = $obj->getlasterror();
                        $retStrBody = $errmsg[0];
                    }
                }
                if($retBool === true) {
                    unset($obj);
                    $obj = new VarStructAnalysisFileAccess($g['objMTS'],
                                                           $g['objDBCA'],
                                                           $global_vars_master_list,
                                                           $template_master_list,
                                                           '',
                                                           false,
                                                           true);
                    // 他ロールパッケージで同じ変数を使用している場合に、変数定義が一致しているか判定
                    $ret = $obj->AllRolePackageAnalysis($PkeyID,
                                                        $role_package_name,
                                                        $def_vars_list,$def_array_vars_list);
                    if($ret === false) {
                        $retBool    = false;
                        $errmsg     = $obj->getlasterror();
                        $retStrBody = $errmsg[0];
                    }
                }
            }
        }
        unset($obj);
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
