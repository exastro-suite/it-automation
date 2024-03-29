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
//    ・Ansible(Legacy Role)作業対象ホスト管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1204080");
/*
Ansible(Legacy Role)作業対象ホスト管理
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

    $table = new TableControlAgent('B_ANSIBLE_LRL_PHO_LINK','PHO_LINK_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1204090"), 'B_ANSIBLE_LRL_PHO_LINK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['PHO_LINK_ID']->setSequenceID('B_ANSIBLE_LRL_PHO_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANSIBLE_LRL_PHO_LINK_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205010"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205020"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205030"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205040"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $url = "01_browse.php?no=2100020307&filter=on&Filter1Tbl_2=";
    $c = new LinkIDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205050"),'E_ANSIBLE_LRL_PATTERN','PATTERN_ID','PATTERN',$url,true,false,'','','','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_ANSIBLE_LRL_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);



    $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205070"),'E_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1205080"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_STM_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 代入地管理へのリンクボタン
    $strLabelText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207318");
    $c = new LinkButtonColumn('ethWakeOrder',$strLabelText, $strLabelText, 'dummy');
    $c->setDBColumn(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setEvent("print_table", "onClick", "newOpenWindow", array('this'), true);
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
                       ."WHERE ROW_ID in (2100020005,2100020006) ";

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
    $tmpAryColumn['PHO_LINK_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);


//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('OPERATION_NO_UAPK','PATTERN_ID','SYSTEM_ID'));
//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
