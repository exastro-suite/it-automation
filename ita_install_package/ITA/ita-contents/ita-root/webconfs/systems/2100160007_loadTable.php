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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102701");

    $table = new TableControlAgent('G_OTHER_MENU_LINK','LINK_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102702"), 'G_OTHER_MENU_LINK_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['LINK_ID']->setSequenceID('F_OTHER_MENU_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_OTHER_MENU_LINK_JSQ');
    unset($tmpAryColumn);
    
    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_OTHER_MENU_LINK');
    $table->setDBJournalTableHiddenID('F_OTHER_MENU_LINK_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // エクセルのファイル名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102703"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102704"));


    // メニューグループ
    $c = new IDColumn('MENU_GROUP_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102705"),'A_MENU_GROUP_LIST','MENU_GROUP_ID','MENU_GROUP_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102706"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);
    $table->addColumn($c);


    // メニューグループ:メニュー（登録用）
    $c = new IDColumn('MENU_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102719"),'D_MENU_LIST','MENU_ID','MENU_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102720"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // メニュー（表示用）
    $c = new IDColumn('MENU_ID_CLONE',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102707"),'A_MENU_LIST','MENU_ID','MENU_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102708"));//エクセル・ヘッダでの説明
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // 項目名
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('COLUMN_DISP_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102709"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102710"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // メニューグループ：メニュー：項目名
    $objVldt = new SingleTextValidator(1,1024,false);
    $c = new TextColumn('LINK_PULLDOWN',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102711"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102712"));//エクセル・ヘッダでの説明
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);
    $c->setValidator($objVldt);
    $table->addColumn($c);


    // テーブル名
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('TABLE_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102713"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102714"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // 主キー
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('PRI_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102715"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102716"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    // カラム名
    $objVldt = new SingleTextValidator(1,64,false);
    $c = new TextColumn('COLUMN_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102717"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102718"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :inactive"');
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->fixColumn();
    
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
