<?php
//   Copyright 2020 NEC Corporation
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
//      CI/CD For IaC Restユーザー管理
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050000");
/*
Restユーザー管理
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

    $table = new TableControlAgent('B_CICD_REST_ACCOUNT_LIST','ACCT_ROW_ID', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050001"), 'B_CICD_REST_ACCOUNT_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ACCT_ROW_ID']->setSequenceID('B_CICD_REST_ACCOUNT_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CICD_REST_ACCOUNT_LIST_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $cg1 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050004"));

        ////////////////////////////////////////////////////
        //RESTユーザー 必須入力:true ユニーク:true
        ////////////////////////////////////////////////////
        $c = new IDColumn('USER_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050100"),'D_CICD_USER_ACCT_LIST','USERNAME_PULLKEY','USERNAME_PULLDOWN','', array('SELECT_ADD_FOR_ORDER'=>array('USERNAME_PULLKEY'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050101"));//エクセル・ヘッダでの説明
        $c->setRequired(true);
        $c->setUnique(true);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('USER_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CICD_USER_ACCT_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'USERNAME_PULLKEY',
            'TTT_GET_TARGET_COLUMN_ID'=>'USERNAME_PULLDOWN',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg1->addColumn($c);

        ////////////////////////////////////////////////////
        //RESTパスワード 必須入力:true ユニーク:false
        ////////////////////////////////////////////////////
        $c = new PasswordColumn('LOGIN_PW',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050200"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050201"));//エクセル・ヘッダでの説明
        $c->setValidator( new TextValidator(8, 30, false, '/^[a-zA-Z0-9-!"#$%&\'()*+,.\/:;<=>?@[\]^\\_`{|}~]+$/', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200050202")));
        $c->setRequired(true);
        $c->setUpdateRequireExcept(1);//1は空白の場合は維持、それ以外はNULL扱いで更新
        $c->setEncodeFunctionName("ky_encrypt");
        $cg1->addColumn($c);

    $table->addColumn($cg1);

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
        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        // $arrayRegDataはUI入力ベースの情報
        // $arrayVariant['edit_target_row']はDBに登録済みの情報
        if ($strModeId == "DTUP_singleRecUpdate") {
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
            // パスワード未入力の場合はエラー
            if(strlen($strPasswd) == 0) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2009");
                $retBool = false;
            }
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
