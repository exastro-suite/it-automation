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
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010001");

    // 項番
    $table = new TableControlAgent('A_SYSTEM_CONFIG_LIST','ITEM_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010101"), 'A_SYSTEM_CONFIG_LIST_JNL');
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010002"));
    $table->getFormatter("excel")->setGeneValue("sheetNameForEditByFile",$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010003"));
    
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    
    $table->setJsEventNamePrefix(true);
    $table->setGeneObject("webSetting", $arrayWebSetting);

    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['ITEM_ID']->setSequenceID('SEQ_A_SYSTEM_CONFIG_LIST');

    // 識別ID
    $c = new TextColumn('CONFIG_ID',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010201"));
    $c->setRequired(true);
    $c->setUnique(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010202"));
    $c->setValidator(new SingleTextValidator(1, 32, false));
    $table->addColumn($c);

    // 項目名
    $c = new MultiTextColumn('CONFIG_NAME',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010301"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010302"));
    $c->setValidator(new MultiTextValidator(0, 64, false));
    $table->addColumn($c);

    // 設定値
    $c = new MultiTextColumn('VALUE',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010401"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1010402"));
    $c->setValidator(new MultiTextValidator(0, 1024, false));
    $table->addColumn($c);

    $table->fixColumn();

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
