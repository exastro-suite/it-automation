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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040001");

    // メニューID
    $table = new TableControlAgent('D_MENU_LIST','MENU_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040101"), 'D_MENU_LIST_JNL');
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040002"));
    $table->getFormatter("excel")->setGeneValue("sheetNameForEditByFile",$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040003"));
    
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    
    $table->setJsEventNamePrefix(true);

    $table->setGeneObject("webSetting", $arrayWebSetting);

    $table->setDBMainTableHiddenID('A_MENU_LIST');
    $table->setDBJournalTableHiddenID('A_MENU_LIST_JNL');

    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['MENU_ID']->setSequenceID('SEQ_A_MENU_LIST');

    $table->addUniqueColumnSet(array('MENU_GROUP_ID','MENU_NAME'));

    // メニューグループ
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040201"));

    $c = new IDColumn('MENU_GROUP_ID_CLONE', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040301"), 'D_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID') );
    $c->addClass("number");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040302"));
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    $c->setMasterDisplayColumnType(0);
    $c->setDeleteOffBeforeCheck(false);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $aryTraceQuery = array(
        array(
            'TRACE_TARGET_TABLE'=>'A_MENU_GROUP_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_ID',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $objOT->setFirstSearchValueOwnerColumnID('MENU_GROUP_ID');
    $c->setOutputType('print_journal_table',$objOT);
    $cg->addColumn($c);

    // 名称
    $c = new TextColumn('MENU_GROUP_NAME', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040401"));
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040402"));
    $c->setOutputType('update_table',new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt('<div id="sel_menu_group_name_chg" style="width: 200px; word-wrap:break-word; white-space:pre-wrap;" ></div>')));
    $c->setOutputType('register_table',new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt('<div id="sel_menu_group_name_reg" style="width: 200px; word-wrap:break-word; white-space:pre-wrap;" ></div>')));
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $aryTraceQuery = array(
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
    $objOT->setFirstSearchValueOwnerColumnID('MENU_GROUP_ID');
    $c->setOutputType('print_journal_table',$objOT);
    $cg->addColumn($c);
    $table->addColumn($cg);

    $c = new IDColumn('MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040501"), 'D_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_PULLDOWN', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID') );
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040502"));
    $c->getOutputType("delete_table")->setVisible(false);
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("print_table")->setVisible(false);
    $c->getOutputType("print_journal_table")->setVisible(false);
    $c->setHiddenMainTableColumn(true);
    $c->setAllowSendFromFile(true);
    $c->setRequired(true);
    $table->addColumn($c);
    
    // メニュー名称
    $c = new TextColumn('MENU_NAME',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040601"));
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040602"));
    $c->getOutputType('update_table')->setAttr('size','60');
    $c->getOutputType('register_table')->setAttr('size','60');
    $c->setValidator(new SingleTextValidator(1, 64, false));
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);
    
    // 認証要否
    $c = new IDColumn('LOGIN_NECESSITY', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040701"), 'A_LOGIN_NECESSITY_LIST', 'FLAG', 'NAME', NULL );
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040702"));
    $c->setHiddenMainTableColumn(true);

    $c->setJournalTableOfMaster('A_LOGIN_NECESSITY_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalKeyIDOfMaster('FLAG');
    $c->setJournalDispIDOfMaster('NAME');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');

    $table->addColumn($c);

    // サービス状態
    $c = new IDColumn('SERVICE_STATUS', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040801"), 'A_SERVICE_STATUS_LIST', 'FLAG', 'NAME', NULL);
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040802"));
    $c->setHiddenMainTableColumn(true);

    $c->setJournalTableOfMaster('A_SERVICE_STATUS_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalKeyIDOfMaster('FLAG');
    $c->setJournalDispIDOfMaster('NAME');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');

    $table->addColumn($c);

    // 表示順序
    $c = new NumColumn('DISP_SEQ', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040901"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040902"));
    $c->setSubtotalFlag(false);
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);
    
    // ロール情報
    $strLabelText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041001");
    $c = new LinkButtonColumn('RoleInfo',$strLabelText, $strLabelText, 'edit_role_list', array(0, ':MENU_ID')); 
    $c->setDBColumn(false);
    $table->addColumn($c);
    
    // オートフィルタチェック
    $c = new IDColumn('AUTOFILTER_FLG',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041501"),'A_TODO_MASTER','TODO_ID','TODO_STATUS','', array('OrderByThirdColumn'=>'TODO_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041502"));//エクセル・ヘッダでの説明
    $c->setRequired(true);
    $table->addColumn($c);

    // 初回フィルタ
    $c = new IDColumn('INITIAL_FILTER_FLG',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041601"),'A_TODO_MASTER','TODO_ID','TODO_STATUS','', array('OrderByThirdColumn'=>'TODO_ID'));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041602"));//エクセル・ヘッダでの説明
    $c->setRequired(true);
    $table->addColumn($c);

    // Web表示上限行数
    $c = new NumColumn('WEB_PRINT_LIMIT', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041201"));
    $c->setHiddenMainTableColumn(true);    
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041202"));
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(0, 2147483647));

    $table->addColumn($c);
    
    // Web表示確認行数
    $c = new NumColumn('WEB_PRINT_CONFIRM', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041301"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041302"));
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(0, 2147483647));
    $table->addColumn($c);
    
    // エクセル表示行数
    $c = new NumColumn('XLS_PRINT_LIMIT', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041401"));
    $c->setHiddenMainTableColumn(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1041402"));
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(-1, 2147483647));
    $table->addColumn($c);
    
    $table->fixColumn();
    
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
