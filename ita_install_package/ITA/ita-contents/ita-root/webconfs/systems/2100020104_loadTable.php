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
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/AnsibleCommonLib.php');
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/WrappedStringReplaceAdmin.php' );
require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php' );

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301030");
/*
Ansible(Legacy(NS))プレイブック素材集
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

    $table = new TableControlAgent('B_ANSIBLE_LNS_PLAYBOOK','PLAYBOOK_MATTER_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301040"), 'B_ANSIBLE_LNS_PLAYBOOK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['PLAYBOOK_MATTER_ID']->setSequenceID('B_ANSIBLE_LNS_PLAYBOOK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANSIBLE_LNS_PLAYBOOK_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301050"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301060"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('PLAYBOOK_MATTER_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301080"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    // FileUpload時にファイルの内容をチェック
    $objFunction = function($objColumn, $functionCaller, $strTempFileFullname, $strOrgFileName, $aryVariant, $arySetting){

        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        // 共通変数を抜き出す。
        $obj = new AnsibleCommonLibs(LC_RUN_MODE_VARFILE);
        $outFilename = $root_dir_path . "/temp/file_up_column/" . basename($strTempFileFullname) . "_vars_list";
        $retArray = $obj->CommonVarssAanalys($strTempFileFullname,$outFilename);
        unset($obj);
        return $retArray;
    };

    $c = new FileUploadColumn('PLAYBOOK_MATTER_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-301090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-302010"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(268435456);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)

    // FileUpload時にZIPファイルの内容をチェックするモジュール登録
    $c->setFunctionForEvent('checkTempFileBeforeMoveOnPreLoad',$objFunction);

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
                       ."WHERE ROW_ID in (2100020001) ";

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
    $tmpAryColumn['PLAYBOOK_MATTER_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

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

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        $tmpFile        = '';
        $TPFVarListfile = '';
        $FileID         = '1';
        if($strModeId == "DTUP_singleRecDelete"){
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            $PkeyID = $strNumberForRI;
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            if($strModeId == "DTUP_singleRecUpdate") {
                $PkeyID = $strNumberForRI;
            } else {
                $PkeyID = array_key_exists('PLAYBOOK_MATTER_ID',$arrayRegData)?$arrayRegData['PLAYBOOK_MATTER_ID']:null;
            }
            $tmpFile      = array_key_exists('tmp_file_COL_IDSOP_8',$arrayRegData)?
                               $arrayRegData['tmp_file_COL_IDSOP_8']:null;
            $TPFVarListfile = $root_dir_path . "/temp/file_up_column/" . $tmpFile . "_vars_list";
            $tmpfilepath    = $root_dir_path . "/temp/file_up_column/" . $tmpFile;
        }

        // 一時ファイルから使用しているテンプレート変数のリストを取得
        unset($g['COM_VARS_LIST_VALUE']);
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ) {
                if(strlen($tmpFile) != 0) {
                    // FileUpload後に登録・更新でエラーが発生した場合、tmp_file_COL_IDSOP_8が別ファイルになる対応
                    if( ! file_exists($TPFVarListfile)) {
                        // 共通変数を抜き出す。
                        $obj = new AnsibleCommonLibs(LC_RUN_MODE_VARFILE);
                        $retArray = $obj->CommonVarssAanalys($tmpfilepath,$TPFVarListfile);
                        unset($obj);
                    }
                    $json_VarsAry = file_get_contents($TPFVarListfile);
                    $g['COM_VARS_LIST_VALUE'] = $json_VarsAry;
                    @unlink($TPFVarListfile);
                    $VarsAry = json_decode($json_VarsAry,true);
                    $dbObj = new WebDBAccessClass($g['db_model_ch'],$g['objDBCA'],$g['objMTS'],$g['login_id']);
                    $ret = $dbObj->CommnVarsUsedListUpdate($PkeyID,$FileID,$VarsAry);
                    if($ret === false) {
                        web_log($dbObj->GetLastErrorMsg());
                        $retBool = false;
                        $retStrBody = $dbObj->GetLastErrorMsg();
                    }
                    unset($dbObj);
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
