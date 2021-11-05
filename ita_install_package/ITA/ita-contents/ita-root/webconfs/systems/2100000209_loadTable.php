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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080001");

    // 項番
    $table = new TableControlAgent('D_ROLE_MENU_LINK_LIST','LINK_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080101"), 'D_ROLE_MENU_LINK_LIST_JNL');
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080002"));
    $table->getFormatter("excel")->setGeneValue("sheetNameForEditByFile",$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    $table->setGeneObject("webSetting", $arrayWebSetting);

    $table->setDBMainTableHiddenID('A_ROLE_MENU_LINK_LIST');
    $table->setDBJournalTableHiddenID('A_ROLE_MENU_LINK_LIST_JNL');

    $tmpAryObjColumn = $table->getColumns();
    $c = $tmpAryObjColumn['LINK_ID'];
    $c->setSequenceID('SEQ_A_ROLE_MENU_LINK_LIST');

    $c = $tmpAryObjColumn['DISUSE_FLAG'];
    $c->setDefaultValue("filter_table",array(""));

    $table->addUniqueColumnSet(array('ROLE_ID','MENU_ID'));

    // ----カラムグループ（ロール）
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081101"));

	    // ロールID
      $arrUrl = array();
      $arrUrl[0] = "01_browse.php?no=2100000207&filter=on&Filter1Tbl_1__S=";
      $arrUrl[1] = "&Filter1Tbl_1__E=";
	    $c = new LinkIDColumn('ROLE_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080201"), 'A_ROLE_LIST', 'ROLE_ID', 'ROLE_ID', $arrUrl, false, false, '', '', '', '', array('OrderByThirdColumn'=>'ROLE_ID'));
	    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080202"));
    	$c->getOutputType("update_table")->setVisible(false);
    	$c->getOutputType("register_table")->setVisible(false);
    	$c->getOutputType("excel")->setVisible(false);
    	$c->getOutputType("csv")->setVisible(false);
	    $c->setMasterDisplayColumnType(0);
	    //----復活時に二重チェックになるので付加
	    $c->setDeleteOffBeforeCheck(false);
	    //復活時に二重チェックになるので付加----
	    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
	    $aryTraceQuery = array(
	        array(
	            'TRACE_TARGET_TABLE'=>'A_ROLE_LIST_JNL',
	            'TTT_SEARCH_KEY_COLUMN_ID'=>'ROLE_ID',
	            'TTT_GET_TARGET_COLUMN_ID'=>'ROLE_ID',
	            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
	            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
	            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
	        )
	    );
	    $objOT->setTraceQuery($aryTraceQuery);
	    $objOT->setFirstSearchValueOwnerColumnID('ROLE_ID');
	    $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

	    // ロール名称
      $url = "01_browse.php?no=2100000207&filter=on&Filter1Tbl_2=";
	    $c = new LinkIDColumn('ROLE_ID_CLONE_02', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081201"), 'A_ROLE_LIST', 'ROLE_ID', 'ROLE_NAME', $url);
	    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081202"));
	    $c->getOutputType("update_table")->setVisible(false);
	    $c->getOutputType("register_table")->setVisible(false);
	    $c->getOutputType("excel")->setVisible(false);
	    $c->getOutputType("csv")->setVisible(false);
	    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
	    $aryTraceQuery = array(
	        array(
	            'TRACE_TARGET_TABLE'=>'A_ROLE_LIST_JNL',
	            'TTT_SEARCH_KEY_COLUMN_ID'=>'ROLE_ID',
	            'TTT_GET_TARGET_COLUMN_ID'=>'ROLE_NAME',
	            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
	            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
	            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
	        )
	    );
	    $objOT->setTraceQuery($aryTraceQuery);
	    $objOT->setFirstSearchValueOwnerColumnID('ROLE_ID');
	    $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

    $table->addColumn($cg);

    // ロールID:名称
    $c = new IDColumn('ROLE_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081301"), "D_ROLE_LIST", 'ROLE_ID', "ROLE_PULLDOWN", '', array('OrderByThirdColumn'=>'ROLE_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081302"));
    $c->getOutputType("delete_table")->setVisible(false);
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("print_table")->setVisible(false);
    $c->getOutputType("print_journal_table")->setVisible(false);
    $table->addColumn($c);

    // ----カラムグループ（メニューグループ）
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080801"));

    $arrUrl[0] = "01_browse.php?no=2100000204&filter=on&Filter1Tbl_1__S=";
    $arrUrl[1] = "&Filter1Tbl_1__E=";
    $c = new LinkIDColumn('MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080901"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', $arrUrl, false, false, '', '', '', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID'));
    $c->addClass("number");
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080902"));
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    $c->setDeleteOffBeforeCheck(false);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $aryTraceQuery = array(
        array(
            'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );

    $objOT->setTraceQuery($aryTraceQuery);
    $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
    $c->setOutputType('print_journal_table',$objOT);
    $c->setMasterDisplayColumnType(0);
    $cg->addColumn($c);

    $url = "01_browse.php?no=2100000204&filter=on&Filter1Tbl_2=";
    $c = new LinkIDColumn('MENU_GROUP_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081001"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_NAME', $url);
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1081002"));
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);

    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $aryTraceQuery = array(
        array(
            'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        ),
        array(
            'TRACE_TARGET_TABLE'=>'A_MENU_GROUP_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
    $c->setOutputType('print_journal_table',$objOT);

    $cg->addColumn($c);

    $table->addColumn($cg);
    // カラムグループ（メニューグループ）----

    // ----カラムグループ（メニュー本体）
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080301"));

    // ID
    $arrUrl[0] = "01_browse.php?no=2100000205&filter=on&Filter1Tbl_1__S=";
    $arrUrl[1] = "&Filter1Tbl_1__E=";
    $c = new LinkIDColumn('MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080401"), "A_MENU_LIST", 'MENU_ID', "MENU_ID", $arrUrl, false, false, '', '', '', '', array('OrderByThirdColumn'=>'MENU_ID'));
    $c->addClass("number");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080402"));
    $c->setJournalTableOfMaster('A_MENU_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalKeyIDOfMaster('MENU_ID');
    $c->setJournalDispIDOfMaster('MENU_NAME');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    //----登録更新関係から隠す
    $c->setHiddenMainTableColumn(false);
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    //----復活時に二重チェックになるので付加
    $c->setDeleteOffBeforeCheck(false);
    //復活時に二重チェックになるので付加----
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $aryTraceQuery = array(
        array(
            'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_ID',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
    $c->setOutputType('print_journal_table',$objOT);
    //登録更新関係から隠す----
    $c->setMasterDisplayColumnType(0);
    $cg->addColumn($c);

    // 名称
    $url = "01_browse.php?no=2100000205&filter=on&Filter1Tbl_4=";
    $c = new LinkIDColumn('MENU_ID_CLONE_02', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080501"), 'A_MENU_LIST', 'MENU_ID', 'MENU_NAME', $url);
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080502"));
    //----登録更新関係から隠す
    $c->setHiddenMainTableColumn(false);
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    //登録更新関係から隠す----

    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('MENU_ID_CLONE_02');
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
    // カラムグループ（メニュー本体）----

    // ----エクセルでの入力用

    $c = new IDColumn('MENU_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080601"), 'D_MENU_LIST', 'MENU_ID', 'MENU_PULLDOWN', '', array('OrderByThirdColumn'=>'MENU_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080602"));
    //----表示関係からのみ隠す
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("print_table")->setVisible(false);
    $c->getOutputType("delete_table")->setVisible(false);
    $c->getOutputType("print_journal_table")->setVisible(false);
    //表示関係からのみ隠す----
    $c->setHiddenMainTableColumn(true);
    $c->setAllowSendFromFile(true);
    $c->setRequired(true);
    $table->addColumn($c);
    // エクセルでの入力用----

    //----紐付け
    $c = new IDColumn('PRIVILEGE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080701"), 'A_PRIVILEGE_LIST', 'FLAG', 'NAME', NULL );
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1080702"));

    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);

    $c->setJournalTableOfMaster('A_PRIVILEGE_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalKeyIDOfMaster('FLAG');
    $c->setJournalDispIDOfMaster('NAME');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');

    $table->addColumn($c);

    $table->fixColumn();

	return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
