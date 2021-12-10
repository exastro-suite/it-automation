<?php
//   Copyright 2020 NEC Corporation
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
require_once $root_dir_path.'/libs/webindividuallibs/systems/2100000216/simpleTableControlAgent_class.php';
require_once $root_dir_path.'/libs/webindividuallibs/systems/2100000216/column_class.php';

$tmpFx = function (&$aryVariant=[], &$arySetting=[]) {
    global $g;

    $arrayWebSetting = [];
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230001");

    $aryVariant['TT_SYS_06_LUP_USER_SET'] = false; // LAST_UPDATE_USERを使用しない

    // 主キー：シーケンス名
    $table = new simpleTableControlAgent('D_SEQUENCE', 'NAME', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230011"), null, $aryVariant);

    $table->setNoRegisterFlg(true);    // 登録画面無し

    // Table settings
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230002"));
    $table->setGeneObject('AutoSearchStart',true); //('',true,false)
    $table->setDBSortKey(["DISP_SEQ"=>"ASC","NAME"=>"ASC"]); // SORT条件を指定
    $table->getFormatter('print_table')->setGeneValue("linkExcelHidden",true); // Excel出力ボタンを非表示
    $table->setGeneObject('webSetting', $arrayWebSetting);
    $table->setDBMainTableHiddenID('A_SEQUENCE');

    // 設定値
    $c = new NumColumn('VALUE',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230021"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230022"));
    $c->setRequired(true);
    $c->setValidator(new IntNumValidator(-2147483648, 2147483646, false));
    $c->setHiddenMainTableColumn(true);
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    // MENU_GROUP
    $c = new IDColumn('MENU_GROUP_ID',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230031"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_NAME');
    $c->setRequired(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230032"));
    $c->setHiddenMainTableColumn(false);
    $c->setOutputType('update_table'  , new IDOutputType(new ReqTabHFmt(), new TextTabBFmt()));
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('MENU_GROUP_ID');
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
    $table->addColumn($c);

    // MENU
    $c = new IDColumn('MENU_ID',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230041"), 'D_MENU_LIST', 'MENU_ID', 'MENU_NAME');
    $c->setRequired(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230042"));
    $c->setHiddenMainTableColumn(false);
    $c->setOutputType('update_table'  , new IDOutputType(new ReqTabHFmt(), new TextTabBFmt()));
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_MENU_LIST_JNL',
    	    'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
	        'TTT_GET_TARGET_COLUMN_ID'=>'MENU_NAME',
        	'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
	        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        	'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
    	)
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    // 表示順序
    $c = new NumColumn('DISP_SEQ',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230051"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230052"));
    $c->setRequired(false);
    $c->setValidator(new IntNumValidator(-2147483648, 2147483647, false));
    $c->setHiddenMainTableColumn(true);
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $table->fixColumn($aryVariant);

    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['NOTE']->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1230061")); // 備考のdefautの説明文から「廃止」「復活」に関する部分の削除
    // ----非表示項目設定
    // 廃止ボタン
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('update_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('register_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('excel')->setVisible(false);
    // 最終更新日時
    $tmpAryColumn['LAST_UPDATE_TIMESTAMP']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['LAST_UPDATE_TIMESTAMP']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['LAST_UPDATE_TIMESTAMP']->getOutputType('update_table')->setVisible(false);
    $tmpAryColumn['LAST_UPDATE_TIMESTAMP']->getOutputType('register_table')->setVisible(false);
    $tmpAryColumn['LAST_UPDATE_TIMESTAMP']->getOutputType('excel')->setVisible(false);
    // 非表示項目設定----

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);

