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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-120021");

    // 主キー：シーケンス名
    $table = new simpleTableControlAgent_2100000327('D_ER_DATA', 'ROW_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211001"), null, $aryVariant);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('B_ER_DATA_RIC');

    // // Table settings
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-120022"));
    $table->setGeneObject('webSetting', $arrayWebSetting);
    $table->setDBMainTableHiddenID('B_ER_DATA');

    $table->setAccessAuth(true);    // データごとのRBAC設定

    // マルチユニーク設定
    $table->addUniqueColumnSet(array('MENU_TABLE_LINK_ID','COLUMN_ID'));



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
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('MENU_GROUP_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_MENU_GROUP_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
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
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('MENU_GROUP_ID_CLONE');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_MENU_GROUP_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

    $table->addColumn($cg);
    // カラムグループ（メニューグループ）----

    // ----カラムグループ（メニュー）
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-211009"));

        // ID（表示のみ）
        $c = new IDColumn('MENU_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040301"), 'A_MENU_LIST', 'MENU_ID', 'MENU_ID');
        $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080402"));
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->getOutputType("json")->setVisible(false);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_ID',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

        // 名称（表示のみ）
        $c = new IDColumn('MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040401"), 'A_MENU_LIST', 'MENU_ID', 'MENU_NAME');
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080402"));
        $c->setHiddenMainTableColumn(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->getOutputType("json")->setVisible(false);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('MENU_ID_CLONE');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

    $table->addColumn($cg);
    // カラムグループ（メニュー）----

    // メニューグループ:メニュー
    $c = new IDColumn('MENU_TABLE_LINK_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211014"), 'D_ER_MENU_TABLE_LINK_LIST', 'ROW_ID', 'MENU_PULLDOWN', '', array('OrderByThirdColumn'=>'MENU_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040502"));
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("print_table")->setVisible(false);
    $c->getOutputType("delete_table")->setVisible(false);
    $c->getOutputType("print_journal_table")->setVisible(false);
    $c->setAllowSendFromFile(true);
    $c->setRequired(true);
    $table->addColumn($c);

    // 表示順序
    $c = new NumColumn('DISP_SEQ', $g['objMTS']->getSomeMessage("ITABASEH-MNU-104050"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040502"));
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    // カラムID
    $c = new TextColumn('COLUMN_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120001"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120011"));
    $table->addColumn($c);

    // カラムタイプ
    $c = new IDColumn('COLUMN_TYPE', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120002"), 'B_ER_COLUMN_TYPE', 'COLUMN_TYPE_ID', 'COLUMN_TYPE_NAME', '', array('OrderByThirdColumn'=>'COLUMN_TYPE_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120012"));
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('COLUMN_TYPE');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_ER_COLUMN_TYPE_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'COLUMN_TYPE_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'COLUMN_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    // 親カラムID
    $c = new TextColumn('PARENT_COLUMN_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120003"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120013"));
    $c->setRequired(false);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 物理名
    $c = new TextColumn('PHYSICAL_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120004"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120014"));
    $c->setRequired(false);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 論理名
    $c = new MultiTextColumn('LOGICAL_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120005"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120015"));
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 関連テーブル名
    $c = new TextColumn('RELATION_TABLE_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120006"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120016"));
    $c->setRequired(false);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 関連カラムID
    $c = new TextColumn('RELATION_COLUMN_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-120007"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-120017"));
    $c->setRequired(false);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);


    $table->fixColumn($aryVariant);

    $tmpAryColumn = $table->getColumns();

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);

