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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = '★★★INFO★★★';

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

    $table = new TableControlAgent('G_★★★TABLE★★★_H','ROW_ID', 'No', 'G_★★★TABLE★★★_H_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_★★★TABLE★★★_H_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_★★★TABLE★★★_H_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_★★★TABLE★★★_H');
    $table->setDBJournalTableHiddenID('F_★★★TABLE★★★_H_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // マルチユニーク制約
    //$table->addUniqueColumnSet(array('HOST_ID','OPERATION_ID'));
     
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel('★★★MENU★★★');
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', '★★★MENU★★★');

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    // 検索機能の制御----

    //$cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102612"));

★★★ITEM★★★

    //$table->addColumn($cg);
    //$table->addColumn($cg1);

    $table->fixColumn();

    // 登録/更新/廃止/復活があった場合、代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global $g;
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

            if(file_exists($g['root_dir_path'] . "/libs/release/ita_ansible-driver")){

                $strQuery = "UPDATE A_PROC_LOADED_LIST "
                           ."SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                           ."WHERE ROW_ID IN (2100020002, 2100020004, 2100020006)";

                $g['objDBCA']->setQueryTime();
                $aryForBind = array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $g['objDBCA']->getQueryTime());

                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] !== true ){
                    $boolRet = $aryRetBody[0];
                    $strErrMsg = $aryRetBody[2];
                    $intErrorType = 500;
                }
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn[$table->getDBTablePK()]->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
