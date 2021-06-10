<?php
//   Copyright 2021 NEC Corporation
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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-105034");

    $table = new TableControlAgent('F_FLAG_ALT_MASTER','FLAG_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-105035"), 'F_FLAG_ALT_MASTER_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['FLAG_ID']->setSequenceID('F_FLAG_ALT_MASTER_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_FLAG_ALT_MASTER_JSQ');
    unset($tmpAryColumn);
    
    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_FLAG_ALT_MASTER');
    $table->setDBJournalTableHiddenID('F_FLAG_ALT_MASTER_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // エクセルのファイル名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-105047"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-105048"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    // YES NO
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('YESNO_STATUS',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-105038"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-105039"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    // TRUE FALSE
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('TRUEFALSE_STATUS',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-105040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-105041"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    $table->fixColumn();
    
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
