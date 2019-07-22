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
//    ・作業対象ホスト画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-204080");
/*
DSC作業対象ホスト
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

    $table = new TableControlAgent('B_DSC_PHO_LINK','PHO_LINK_ID', $g['objMTS']->getSomeMessage("ITADSCH-MNU-204090"), 'B_DSC_PHO_LINK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['PHO_LINK_ID']->setSequenceID('B_DSC_PHO_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DSC_PHO_LINK_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-205010"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITADSCH-MNU-205020"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    //************************************************************************************
    //----オペレーション
    //************************************************************************************
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITADSCH-MNU-205030"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-205040"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');			// mysql_ita_model-a.sqlに存在
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //************************************************************************************
    //----作業パターン
    //************************************************************************************
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-205050"),'E_DSC_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-205060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_DSC_PATTERN_JNL');				// mysql_ita_model-g.sqlに存在
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);



    //************************************************************************************
    //----ホスト
    //************************************************************************************
    $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-205070"),'E_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-205080"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_STM_LIST_JNL');					// mysql_ita_model-a.sqlに存在
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);




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
