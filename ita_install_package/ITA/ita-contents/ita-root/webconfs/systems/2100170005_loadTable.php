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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100301");
    
    $table = new TableControlAgent('F_HOSTGROUP_VAR','ROW_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100302"), 'F_HOSTGROUP_VAR_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_HOSTGROUP_VAR_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_HOSTGROUP_VAR_JSQ');
    unset($tmpAryColumn);

    // エクセルのブック名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100303"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100304"));
    
    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    
    //---- マルチユニーク制約
    $table->addUniqueColumnSet(array('HOSTGROUP_NAME','VARS_NAME','HOSTNAME'));

    // ホストグループ名
    $c = new IDColumn('HOSTGROUP_NAME',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100305"),'F_HOSTGROUP_LIST','ROW_ID','HOSTGROUP_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100306"));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // ホストグループ変数名
    $objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('VARS_NAME',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100307"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100308"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // ホスト名
    $c = new IDColumn('HOSTNAME',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100309"),'C_STM_LIST','SYSTEM_ID','HOSTNAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100310"));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->fixColumn();
    
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>