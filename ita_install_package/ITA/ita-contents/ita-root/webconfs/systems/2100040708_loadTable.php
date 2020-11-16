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
    $c = new IDColumn('ANSTWR_LOGIN_AUTH_TYPE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001020"),'B_LOGIN_AUTH_TYPE','LOGIN_AUTH_TYPE_ID','LOGIN_AUTH_TYPE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001021"));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
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
    $objVldt = new SingleTextValidator(0,30,false);
    $c = new PasswordColumn('ANSTWR_LOGIN_PASSWORD',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001041"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(false);        // 必須チャックはDB登録前処理で実施
    $c->setUpdateRequireExcept(1); // 1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setEncodeFunctionName("ky_encrypt");
    $table->addColumn($c);
    //ログインパスワード----

    //----ssh鍵認証ファイル
    $c = new FileUploadColumn('ANSTWR_LOGIN_SSH_KEY_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001051"));
    $c->setMaxFileSize(4*1024*1024*1024);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
    $c->setFileHideMode(true);
    $table->addColumn($c);
    //ssh鍵認証ファイル----

    //----isolated Tower
    $c = new IDColumn('ANSTWR_ISOLATED_TYPE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001060"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001061"));//エクセル・ヘッダでの説明
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

        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $strAuthMode   = isset($arrayVariant['edit_target_row']['ANSTWR_LOGIN_AUTH_TYPE'])?
                                   $arrayVariant['edit_target_row']['ANSTWR_LOGIN_AUTH_TYPE']:null;
            $strPasswd     = isset($arrayVariant['edit_target_row']['ANSTWR_LOGIN_PASSWORD'])?
                                   $arrayVariant['edit_target_row']['ANSTWR_LOGIN_PASSWORD']:null;
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            $PkeyID = $strNumberForRI;
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $strAuthMode   = array_key_exists('ANSTWR_LOGIN_AUTH_TYPE',$arrayRegData)?
                                 $arrayRegData['ANSTWR_LOGIN_AUTH_TYPE']:null;
            // PasswordColumn
            $strPasswd     = isset($arrayVariant['edit_target_row']['ANSTWR_LOGIN_PASSWORD'])?
                                   $arrayVariant['edit_target_row']['ANSTWR_LOGIN_PASSWORD']:null;
            if(strlen($strPasswd)==0) {
                $strPasswd = array_key_exists('ANSTWR_LOGIN_PASSWORD',$arrayRegData)?
                                $arrayRegData['ANSTWR_LOGIN_PASSWORD']:null;
            }
        }

        switch($strModeId) {
        case "DTUP_singleRecDelete":
            break;
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
            $retStrBody = "";
            // パスワード認証の場合の、パスワードの必須入力チェック
            if($strAuthMode == '2') {
                if(trim(strlen($strPasswd)) == 0) {
                    $item = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010001040");
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000018",array($item));
                }
            }
            if(strlen($retStrBody) != 0) {
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
