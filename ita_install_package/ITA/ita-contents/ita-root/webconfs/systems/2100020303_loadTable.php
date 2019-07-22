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



    $objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('ROLE_PACKAGE_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605060"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);
    $table->addColumn($c);

    // FileUpload時にZIPファイルの内容をチェック
    $objFunction = function($objColumn, $functionCaller, $strTempFileFullname, $strOrgFileName, $aryVariant, $arySetting){
        //$strTempFileFullname一時ファイルのフルパス
        //$strOrgFileName：ローカルであった時のファイル名
        global $g;

        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }

        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/WrappedStringReplaceAdmin.php' );
        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/CheckAnsibleRoleFiles.php' );

        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = null;
        $arysystemvars = array();

        // ロールパッケージファイル(ZIP)を解析するクラス生成
        $roleObj = new CheckAnsibleRoleFiles($g['objMTS']);

        // ロールパッケージファイル(ZIP)の解凍先
        $outdir  = "/tmp/LegacyRoleZipFileUpload_" . getmypid();

        // ロールパッケージファイル(ZIP)の解凍
        if($roleObj->ZipextractTo($strTempFileFullname,$outdir) === false){
            $boolRet = false;
            $arryErrMsg = $roleObj->getlasterror();
            $strErrMsg = $arryErrMsg[0];

        }
        else{
            $def_vars_list = array();
            $err_vars_list = array();

            $def_varsval_list = array();

            $cpf_vars_list = array();

            $ITA2User_var_list = array();
            $User2ITA_var_list = array();
            $comb_err_vars_list = array();
            
            // ロールパッケージファイル(ZIP)の解析
            //CM if($roleObj->chkRolesDirectory($outdir,$arysystemvars,true) === false)
            $ret = $roleObj->chkRolesDirectory($outdir,$arysystemvars,
                                           "",
                                           $def_vars_list,$err_vars_list,
                                           $def_varsval_list,
                                           $def_array_vars_list,
                                           true,
                                           $cpf_vars_list,
                                           $ITA2User_var_list,
                                           $User2ITA_var_list,
                                           $comb_err_vars_list,
                                           true);
            if($ret === false){
                // ロール内の読替表で読替変数と任意変数の組合せが一致していない
                if(@count($comb_err_vars_list) !== 0){
                    $msgObj = new DefaultVarsFileAnalysis($g['objMTS']);
                    $strErrMsg  = $msgObj->TranslationTableCombinationErrmsgEdit(false,$comb_err_vars_list);
                    unset($msgObj);
                    $boolRet = false;
                }

                // defaults定義ファイルに定義されている変数で形式が違う変数がある場合
                else if(@count($err_vars_list) !== 0){
                    // エラーメッセージ編集
                    $msgObj = new DefaultVarsFileAnalysis($g['objMTS']);
                    $strErrMsg  = $msgObj->VarsStructErrmsgEdit($err_vars_list);
                    unset($msgObj);
                    $boolRet = false;
                }
                else{
                    $boolRet = false;
                    $arryErrMsg = $roleObj->getlasterror();
                    $strErrMsg = $arryErrMsg[0];
                }
            }
            exec("/bin/rm -rf " . $outdir);

            if($boolRet === true){
                $strErrMsg = "";;
                $strErrDetailMsg = "";
                $objLibs = new AnsibleCommonLibs();
                // copy変数がファイル管理に登録されているか判定
                $boolRet = $objLibs->chkCPFVarsMasterReg($g['objMTS'],$g['objDBCA'],$cpf_vars_list,$strErrMsg,$strErrDetailMsg);
                unset($objLibs);
                if($boolRet === false){
                    if($strErrDetailMsg != ""){
                        web_log($strErrDetailMsg);
                    }
                }
            }
        }
        unset($roleObj);

        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
    };

    $c = new FileUploadColumn('ROLE_PACKAGE_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1605080"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(268435456);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    // 必須入力にはしない

    // FileUpload時にZIPファイルの内容をチェックするモジュール登録
    $c->setFunctionForEvent('checkTempFileBeforeMoveOnPreLoad',$objFunction);

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

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
