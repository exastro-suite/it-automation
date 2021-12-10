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
//    ・Ansible 共通 Ansible Tower インスタンス一覧
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

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001000");
/*
Ansible 共通 Ansible Tower インスタンス一覧
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

    $table = new TableControlAgent('B_ANS_TWR_HOST','ANSTWR_HOST_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001001"), 'B_ANS_TWR_HOST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ANSTWR_HOST_ID']->setSequenceID('B_ANS_TWR_HOST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANS_TWR_HOST_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //----ホスト名
    $objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('ANSTWR_HOSTNAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001010"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001011"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setUnique(true);  //登録/更新時には、DB上ユニークキー
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //ホスト名----

    //----認証方式
    $c = new IDColumn('ANSTWR_LOGIN_AUTH_TYPE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001020"),'D_TOWER_LOGIN_AUTH_TYPE','LOGIN_AUTH_TYPE_ID','LOGIN_AUTH_TYPE_NAME','',array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001021"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('ANSTWR_LOGIN_AUTH_TYPE');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_TOWER_LOGIN_AUTH_TYPE_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'LOGIN_AUTH_TYPE_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'LOGIN_AUTH_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);
    //認証方式----

    //----ログインユーザー
    $objVldt = new SingleTextValidator(0,30,false);
    $c = new TextColumn('ANSTWR_LOGIN_USER',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001030"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001031"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //ログインユーザー----

    //----ログインパスワード
    $objVldt = new SingleTextValidator(0,128,false);
    $c = new PasswordColumn('ANSTWR_LOGIN_PASSWORD',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001041"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(false);        // 必須チャックはDB登録前処理で実施
    $c->setUpdateRequireExcept(1); // 1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setEncodeFunctionName("ky_encrypt");

    $table->addColumn($c);
    //ログインパスワード----

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001055"));
      //----秘密鍵ファイル
      $c = new FileUploadColumn('ANSTWR_LOGIN_SSH_KEY_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001050"));
      $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001051"));
      $c->setMaxFileSize(4*1024*1024*1024);//単位はバイト
      $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
      $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
      $c->setFileHideMode(true);
      // CONN_SSH_KEY_FILEをアップロード時に「ky__encrypt」で暗号化する設定
      $c->setFileEncryptFunctionName("ky_file_encrypt");
      $cg->addColumn($c);
      //秘密鍵ファイル----

      //----秘密鍵ファイル パスフレーズ
      $objVldt = new SingleTextValidator(0,256,false);
      $c = new PasswordColumn('ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001052"));
      $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001053"));
      $c->setEncodeFunctionName("ky_encrypt");

      $c->setValidator($objVldt);

      $cg->addColumn($c);
      //秘密鍵ファイル パスフレーズ----
    $table->addColumn($cg);

    //----isolated Tower
    $c = new IDColumn('ANSTWR_ISOLATED_TYPE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001060"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001061"));//エクセル・ヘッダでの説明
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('ANSTWR_ISOLATED_TYPE');
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
    $table->addColumn($c);
    //isolated Tower----

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
            $strhostname   = array_key_exists('ANSTWR_HOSTNAME',$arrayRegData)?
                                $arrayRegData['ANSTWR_HOSTNAME']:null;

            // 認証方式の設定値取得
            $strAuthMode   = array_key_exists('ANSTWR_LOGIN_AUTH_TYPE',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_AUTH_TYPE']:null;
            // パスワードの設定値取得
            $strPasswd     = array_key_exists('ANSTWR_LOGIN_PASSWORD',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_PASSWORD']:null;
            // パスフレーズの設定値取得
            $strPassphrase = array_key_exists('ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE']:null;
            // 公開鍵ファイルの設定値取得
            $strsshKeyFile = array_key_exists('ANSTWR_LOGIN_SSH_KEY_FILE',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_SSH_KEY_FILE']:null;

        } elseif ($strModeId == "DTUP_singleRecDelete") {

            // ホスト名
            $strhostname        = isset($arrayVariant['edit_target_row']['ANSTWR_HOSTNAME'])?
                                        $arrayVariant['edit_target_row']['ANSTWR_HOSTNAME']:null;

        } elseif ($strModeId == "DTUP_singleRecUpdate") {
            // ホスト名
            $strhostname   = array_key_exists('ANSTWR_HOSTNAME',$arrayRegData)?
                                $arrayRegData['ANSTWR_HOSTNAME']:null;

            // 認証方式の設定値取得
            $strAuthMode   = array_key_exists('ANSTWR_LOGIN_AUTH_TYPE',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_AUTH_TYPE']:null;

            // パスワードの設定値取得
            // PasswordColumnはデータの更新がないと$arrayRegDataの設定は空になっているので
            // パスワードが更新されているか判定
            // 更新されていない場合は設定済みのパスワード($arrayVariant['edit_target_row'])取得
            $strPasswd     = array_key_exists('ANSTWR_LOGIN_PASSWORD',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_PASSWORD']:null;
            if($strPasswd == "") {
                $strPasswd     = isset($arrayVariant['edit_target_row']['ANSTWR_LOGIN_PASSWORD'])?
                                       $arrayVariant['edit_target_row']['ANSTWR_LOGIN_PASSWORD']:null;
            }
            // パスフレーズの設定値取得
            // PasswordColumnはデータの更新がないと$arrayRegDataの設定は空になっているので
            // パスフレーズが更新されているか判定
            // 更新されていない場合は設定済みのパスフレーズ($arrayVariant['edit_target_row'])取得
            $strPassphrase = array_key_exists('ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE']:null;
            if($strPassphrase== "") {
                $strPassphrase = isset($arrayVariant['edit_target_row']['ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE'])?
                                       $arrayVariant['edit_target_row']['ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE']:null;
            }
            // 公開鍵ファイルの設定値取得
            // FileUploadColumnはファイルの更新がないと$arrayRegDataの設定は空になっているので
            // ダウンロード済みのファイルが削除されていると$arrayRegData['del_flag_COL_IDSOP_xx']がonになる
            // 更新されていない場合は設定済みのファイル名($arrayVariant['edit_target_row'])を取得
            $strsshKeyFileDel  = array_key_exists('del_flag_COL_IDSOP_13',$arrayRegData)?
                                    $arrayRegData['del_flag_COL_IDSOP_13']:null;
            if($strsshKeyFileDel == 'on') {
                $strsshKeyFile = "";
            } else {
                // 公開鍵ファイルが更新されているか判定
                $strsshKeyFile = array_key_exists('ANSTWR_LOGIN_SSH_KEY_FILE',$arrayRegData)?
                                     $arrayRegData['ANSTWR_LOGIN_SSH_KEY_FILE']:null;
                if($strsshKeyFile == "") {
                    $strsshKeyFile= isset($arrayVariant['edit_target_row']['ANSTWR_LOGIN_SSH_KEY_FILE'])?
                                          $arrayVariant['edit_target_row']['ANSTWR_LOGIN_SSH_KEY_FILE']:null;
                }
            }
        }
        //ホスト名が数値文字列か判定
        if(is_numeric($strhostname) === true) {
            $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-MNU-101081");
            $objClientValidator->setValidRule($retStrBody);
            $retBool = false;
            return $retBool;
        }

        switch($strModeId) {
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
            $errMsgParameterAry = array();
            $chkobj = new AuthTypeParameterRequiredCheck();

            $del_password_arr = array();
            
            if(isset($arrayRegData['del_password_flag_COL_IDSOP_12']) && $arrayRegData['del_password_flag_COL_IDSOP_12'] == "on"){
                $del_password_arr[] = "del_password_flag_COL_IDSOP_12";
            }

            if(isset($arrayRegData['del_password_flag_COL_IDSOP_14']) && $arrayRegData['del_password_flag_COL_IDSOP_14'] == "on"){
                $del_password_arr[] = "del_password_flag_COL_IDSOP_14";
            }

            $retStrBody = $chkobj->TowerHostListAuthTypeRequiredParameterCheck($chkobj->chkType_Loadtable_TowerHostList,$g['objMTS'],$errMsgParameterAry,$strAuthMode,$strPasswd,$strsshKeyFile,$strPassphrase,$del_password_arr);
            
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
