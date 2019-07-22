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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100501");
    
    $table = new TableControlAgent('F_HG_VAR_LINK_LEGACYROLE','ROW_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100502"), 'F_HG_VAR_LINK_LEGACYROLE_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_HG_VAR_LINK_LEGACYROLE_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_HG_VAR_LINK_LEGACYROLE_JSQ');
    unset($tmpAryColumn);

    // エクセルのブック名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100503"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100504"));
    
    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    
    //---- マルチユニーク制約
    $table->addUniqueColumnSet(array('OPERATION_NO_UAPK','PATTERN_ID','SYSTEM_ID','VARS_NAME'));

    //----オペレーション
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100505"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100506"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //オペレーション----

    //----作業パターン
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100507"),'E_ANSIBLE_LRL_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100508"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_ANSIBLE_LRL_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //作業パターン----

    //----ホスト名
    $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100509"),'E_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100510"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('E_STM_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //ホスト名---


    //----ホストグループ変数名
    $c = new IDColumn('VARS_NAME',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100511"),'F_HOSTGROUP_VAR','VARS_NAME','VARS_NAME','',array('OrderByThirdColumn'=>'VARS_NAME'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100512"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //ホストグループ変数名----

    $table->fixColumn();
    
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>