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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100701");

    $table = new TableControlAgent('G_SPLIT_TARGET','ROW_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100702"), 'G_SPLIT_TARGET_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_SPLIT_TARGET_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_SPLIT_TARGET_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_SPLIT_TARGET');
    $table->setDBJournalTableHiddenID('F_SPLIT_TARGET_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // エクセルのファイル名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100703"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100704"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true );


    // ----カラムグループ（分割対象メニュー）
    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100705"));

        // ----カラムグループ（メニューグループ）
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100706"));

            $c = new IDColumn('INPUT_MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100707"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID'));
            $c->addClass("number");
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100708"));
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
                )
            );

            $objOT->setTraceQuery($aryTraceQuery);
            $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
            $c->setOutputType('print_journal_table',$objOT);
            $c->setMasterDisplayColumnType(0);
            $cg->addColumn($c);

            $c = new TextColumn('INPUT_MENU_GROUP_NAME', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100709"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100710"));
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

        $cgg->addColumn($cg);
        // カラムグループ（メニューグループ）----

        // ----カラムグループ（メニュー本体）
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100711"));

            // ID
            $c = new IDColumn('INPUT_MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100712"), 'D_MENU_LIST', 'MENU_ID', 'MENU_ID', '', array('OrderByThirdColumn'=>'MENU_ID'));
            $c->addClass("number");
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100713"));

            $c->setHiddenMainTableColumn(false);
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
            $cg->addColumn($c);

            // 名称
            $c = new TextColumn('INPUT_MENU_NAME', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100714"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100715"));
            $c->getOutputType("update_table")->setVisible(false);
            $c->getOutputType("register_table")->setVisible(false);
            $c->getOutputType("excel")->setVisible(false);
            $c->getOutputType("csv")->setVisible(false);

            $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
            $aryTraceQuery = array(
                array(
                    'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'MENU_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                )
            );
            $objOT->setTraceQuery($aryTraceQuery);
            $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
            $c->setOutputType('print_journal_table',$objOT);
            $cg->addColumn($c);

        $cgg->addColumn($cg);
        // カラムグループ（メニュー本体）----

        // ----エクセルでの入力用
        $c = new IDColumn('INPUT_MENU_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100716"), 'D_MENU_LIST', 'MENU_ID', 'MENU_PULLDOWN', '', array('OrderByThirdColumn'=>'MENU_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100717"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("print_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("print_journal_table")->setVisible(false);
        $c->setHiddenMainTableColumn(true);
        $c->setAllowSendFromFile(true);
        $c->setRequired(true);
        $cgg->addColumn($c);
        // エクセルでの入力用----

    $table->addColumn($cgg);
    // カラムグループ（分割対象メニュー）----


    // ----カラムグループ（分割データ登録メニュー）
    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100718"));

        // ----カラムグループ（メニューグループ）
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100719"));

            $c = new IDColumn('OUTPUT_MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100720"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID'));
            $c->addClass("number");
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-1007021"));
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
                )
            );

            $objOT->setTraceQuery($aryTraceQuery);
            $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
            $c->setOutputType('print_journal_table',$objOT);
            $c->setMasterDisplayColumnType(0);
            $cg->addColumn($c);

            $c = new TextColumn('OUTPUT_MENU_GROUP_NAME', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100722"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100723"));
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

        $cgg->addColumn($cg);
        // カラムグループ（メニューグループ）----

        // ----カラムグループ（メニュー本体）
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100724"));

            // ID
            $c = new IDColumn('OUTPUT_MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100725"), 'D_MENU_LIST', 'MENU_ID', 'MENU_ID', '', array('OrderByThirdColumn'=>'MENU_ID'));
            $c->addClass("number");
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100726"));

            $c->setHiddenMainTableColumn(false);
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
            $cg->addColumn($c);

            // 名称
            $c = new TextColumn('OUTPUT_MENU_NAME', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100727"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100728"));
            $c->getOutputType("update_table")->setVisible(false);
            $c->getOutputType("register_table")->setVisible(false);
            $c->getOutputType("excel")->setVisible(false);
            $c->getOutputType("csv")->setVisible(false);

            $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
            $aryTraceQuery = array(
                array(
                    'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'MENU_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                )
            );
            $objOT->setTraceQuery($aryTraceQuery);
            $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
            $c->setOutputType('print_journal_table',$objOT);
            $cg->addColumn($c);

        $cgg->addColumn($cg);
        // カラムグループ（メニュー本体）----

        // ----エクセルでの入力用
        $c = new IDColumn('OUTPUT_MENU_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100729"), 'D_MENU_LIST', 'MENU_ID', 'MENU_PULLDOWN', '', array('OrderByThirdColumn'=>'MENU_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100730"));
        $c->getOutputType("filter_table")->setVisible(false);
        $c->getOutputType("print_table")->setVisible(false);
        $c->getOutputType("delete_table")->setVisible(false);
        $c->getOutputType("print_journal_table")->setVisible(false);
        $c->setHiddenMainTableColumn(true);
        $c->setAllowSendFromFile(true);
        $c->setRequired(true);
        $cgg->addColumn($c);
        // エクセルでの入力用----

    $table->addColumn($cgg);
    // カラムグループ（分割対象メニュー）----

    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){
            $exeQueryData[$objColumn->getID()] = "0";
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    // 分割済みフラグ
    $c = new TextColumn('DIVIDED_FLG', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100731"));
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100732"));
    $c->getOutputType("print_journal_table")->setVisible(false);
    $c->getOutputType("filter_table")->setVisible(false);
    $c->getOutputType("update_table")->setVisible(false);
    $c->getOutputType("register_table")->setVisible(false);
    $c->getOutputType("excel")->setVisible(false);
    $c->getOutputType("csv")->setVisible(false);
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
    $table->addColumn($c);



    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
