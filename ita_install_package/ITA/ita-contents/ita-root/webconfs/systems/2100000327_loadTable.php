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
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・独自シーケンスの管理を行う
//
//////////////////////////////////////////////////////////////////////

$root_dir_path = preg_replace('|^(.*/ita-root)/.*$|', '$1', __FILE__);
require_once $root_dir_path.'/libs/webindividuallibs/systems/2100000327/simpleTableControlAgent_class.php';
require_once $root_dir_path.'/libs/webindividuallibs/systems/2100000327/column_class.php';

$tmpFx = function (&$aryVariant=[], &$arySetting=[]) {
    global $g;

    $arrayWebSetting = [];
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-120020");

    // 主キー：シーケンス名
    $table = new simpleTableControlAgent_2100000327('D_ER_MENU_TABLE_LINK_LIST', 'ROW_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211001"), null, $aryVariant);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('B_ER_MENU_TABLE_LINK_LIST_RIC');

    // Table settings
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-120010"));
    $table->setGeneObject('webSetting', $arrayWebSetting);
    $table->setDBMainTableHiddenID('B_ER_MENU_TABLE_LINK_LIST');

    $table->setAccessAuth(true);    // データごとのRBAC設定



    // ----カラムグループ（メニューグループ）
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040201"));

        // ID（表示のみ）
        $c = new IDColumn('MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040301"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID');
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040402"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->getOutputType("json")->setVisible(false);
        $cg->addColumn($c);

        // 名称（表示のみ）
        $c = new IDColumn('MENU_GROUP_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040401"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_NAME');
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040402"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->getOutputType("json")->setVisible(false);
        $cg->addColumn($c);

    $table->addColumn($cg);
    // カラムグループ（メニューグループ）----

    // ----カラムグループ（メニュー）
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080301"));

        // ID（表示のみ）
        $c = new IDColumn('MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040301"), 'A_MENU_LIST', 'MENU_ID', 'MENU_ID');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080402"));
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->getOutputType("json")->setVisible(false);
        $cg->addColumn($c);

        // 名称（表示のみ）
        $c = new IDColumn('MENU_ID_CLONE_02', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040401"), 'A_MENU_LIST', 'MENU_ID', 'MENU_NAME');
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080402"));
        $c->setHiddenMainTableColumn(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->getOutputType("json")->setVisible(false);
        $cg->addColumn($c);

    $table->addColumn($cg);
    // カラムグループ（メニュー）----

    // メニューグループ:メニュー
    $c = new IDColumn('MENU_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211014"), 'D_MENU_LIST', 'MENU_ID', 'MENU_PULLDOWN', '', array('OrderByThirdColumn'=>'MENU_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040502"));
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("print_table")->setVisible(false);
    $c->getOutputType("delete_table")->setVisible(false);
    $c->getOutputType("print_journal_table")->setVisible(false);
    $c->setAllowSendFromFile(true);
    $c->setRequired(true);
    $c->setUnique(true);
    $table->addColumn($c);

    // テーブル名
    $c = new TextColumn('TABLE_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120008"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120018"));
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // ビューのテーブル名
    $c = new TextColumn('VIEW_TABLE_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120009"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120019"));
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    $table->fixColumn($aryVariant);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);

