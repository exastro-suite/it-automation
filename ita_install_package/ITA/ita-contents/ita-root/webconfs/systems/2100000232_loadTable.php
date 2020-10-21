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
//    ・SSO(シングルサインオン)の基本情報以外の項目の設定を行う
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220001");

    $table = new TableControlAgent('D_PROVIDER_ATTRIBUTE_LIST','PROVIDER_ATTRIBUTE_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220011"), 'D_PROVIDER_ATTRIBUTE_LIST_JNL');

    // PROVIDER_ATTRIBUTE_ID(項目ID)
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220012"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220013"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['PROVIDER_ATTRIBUTE_ID']->setSequenceID('SEQ_A_PROVIDER_ATTRIBUTE_LIST');
    $table->addUniqueColumnSet(array('PROVIDER_ID','NAME'));
    $table->setDBMainTableHiddenID('A_PROVIDER_ATTRIBUTE_LIST');
    $table->setDBJournalTableHiddenID('A_PROVIDER_ATTRIBUTE_LIST_JNL');

    // プロバイダー
    $c = new IDColumn('PROVIDER_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220021"), 'A_PROVIDER_LIST', 'PROVIDER_ID', 'PROVIDER_NAME', null);
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setUnique(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220022"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    // 設定項目名
    $c = new IDColumn('NAME', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220031"), 'A_PROVIDER_ATTRIBUTE_NAME_LIST', 'NAME', 'NAME', null);
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setUnique(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220032"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    // 設定内容
    $c = new TextColumn('VALUE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220041"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(false);
    $c->setUnique(false);
    $c->setValidator(new TextValidator(0, 256, false));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1220042"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
