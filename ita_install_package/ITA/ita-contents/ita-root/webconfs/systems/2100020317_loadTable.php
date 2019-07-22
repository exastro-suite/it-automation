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
//    ・Ansible（Legacy Role）変数具体値管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704010");

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

    $table = new TableControlAgent('B_ANS_LRL_ROLE_VARSVAL','VARSVAL_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704020"), 'B_ANS_LRL_ROLE_VARSVAL_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['VARSVAL_ID']->setSequenceID('B_ANS_LRL_ROLE_VARSVAL_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANS_LRL_ROLE_VARSVAL_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704040"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----


    $c = new IDColumn('ROLE_PACKAGE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704050"),'B_ANSIBLE_LRL_ROLE_PACKAGE','ROLE_PACKAGE_ID','ROLE_PACKAGE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_ANSIBLE_LRL_ROLE_PACKAGE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('ROLE_PACKAGE_ID');
    $c->setJournalDispIDOfMaster('ROLE_PACKAGE_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new IDColumn('ROLE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704070"),'B_ANSIBLE_LRL_ROLE','ROLE_ID','ROLE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704080"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_ANSIBLE_LRL_ROLE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('ROLE_ID');
    $c->setJournalDispIDOfMaster('ROLE_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new NumColumn('VAR_TYPE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704100"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(null,null));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new IDColumn('VARS_NAME_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704110"),'B_ANSIBLE_LRL_VARS_MASTER','VARS_NAME_ID','VARS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704120"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_ANSIBLE_LRL_VARS_MASTER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('VARS_NAME_ID');
    $c->setJournalDispIDOfMaster('VARS_NAME');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $c = new IDColumn('COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704130"),'D_ANS_LRL_MEMBER_COL_COMB','COL_SEQ_COMBINATION_ID','COMBINATION_MEMBER','', array('OrderByThirdColumn'=>'COL_SEQ_COMBINATION_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704140"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_ANS_LRL_MEMBER_COL_COMB_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
    $c->setJournalDispIDOfMaster('COMBINATION_MEMBER');
    $table->addColumn($c);

    $c = new NumColumn('ASSIGN_SEQ',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704150"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704160"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(null,null));
    $table->addColumn($c);

    $objVldt = new SingleTextValidator(1,1024,false);
    $c = new TextColumn('VARS_VALUE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704190"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1704200"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
