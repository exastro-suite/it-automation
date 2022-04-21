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

    $strLrWebRootToThisPageDir = substr(basename(__FILE__), 0, 10);

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-308090");
/*
Ansible(Pioneer)作業管理
*/
    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP',
        'TT_SYS_08_DUPLICATE_ID'=>'WEB_BUTTON_DUPLICATE'
    );

    $table = new TableControlAgent('C_ANSIBLE_PNS_EXE_INS_MNG','EXECUTION_NO',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309010"), 'C_ANSIBLE_PNS_EXE_INS_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['EXECUTION_NO']->setSequenceID('C_ANSIBLE_PNS_EXE_INS_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_ANSIBLE_PNS_EXE_INS_MNG_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309020"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309030"));

    $table->setAccessAuth(true);    // データごとのRBAC設定
    $table->setNoRegisterFlg(true);    // 登録画面無し

    $table->setDBSortKey(array("EXECUTION_NO"=>"DESC"));

    //----- 2:作業状態確認(ボタン)
    $strTextBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309040");
    $c = new LinkButtonColumn( 'MonitorExecution', $strTextBody, $strTextBody, 'monitor_execution', array( ":EXECUTION_NO" ) );
    $c->setDBColumn(false);
    $table->addColumn($c);

    //実行種別
    $c = new IDColumn('RUN_MODE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309045"),'D_ANSIBLE_PNS_INS_RUN_MODE','RUN_MODE_ID','RUN_MODE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309046"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_ANSIBLE_PNS_INS_RUN_MODE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('RUN_MODE_ID');
    $c->setJournalDispIDOfMaster('RUN_MODE_NAME');
    $table->addColumn($c);

    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309050"),'D_ANSIBLE_PNS_INS_STATUS','STATUS_ID','STATUS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_ANSIBLE_PNS_INS_STATUS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('STATUS_ID');
    $c->setJournalDispIDOfMaster('STATUS_NAME');
    $table->addColumn($c);
    
    /* 実行区分 */
    $c = new IDColumn('EXEC_MODE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1203065"),'B_ANSIBLE_EXEC_MODE','ID','NAME','', array('OrderByThirdColumn'=>'ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1203066"));
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('EXEC_MODE');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_ANSIBLE_EXEC_MODE_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //シンフォニークラス
    $c = new TextColumn('SYMPHONY_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309120"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309130"));//エクセル・ヘッダでの説明
    $table->addColumn($c);
    
    //コンダクタクラス
    $c = new TextColumn('CONDUCTOR_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309121"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309131"));//エクセル・ヘッダでの説明
    $table->addColumn($c);
    
    //実行ユーザ
    $c = new TextColumn('EXECUTION_USER',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309110"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    //----作業パターン
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309065") );

    $c = new TextColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309080"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $cg->addColumn($c);

    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('I_PATTERN_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-309090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401010"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);
    
    $c = new NumColumn('I_TIME_LIMIT',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402080"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $cg->addColumn($c);
    
    // Ansible利用情報
    $cg2 = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402081") );

    // ホスト指定形式
    $c = new IDColumn('I_ANS_HOST_DESIGNATE_TYPE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402082"),'B_HOST_DESIGNATE_TYPE_LIST','HOST_DESIGNATE_TYPE_ID','HOST_DESIGNATE_TYPE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402083"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('I_ANS_HOST_DESIGNATE_TYPE_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_HOST_DESIGNATE_TYPE_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'HOST_DESIGNATE_TYPE_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'HOST_DESIGNATE_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $cg2->addColumn($c);

    // 並列実行数
    $c = new NumColumn('I_ANS_PARALLEL_EXE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401031"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401032"));//エクセル・ヘッダでの説明
    $c->setValidator(new IntNumValidator(0,null));
    $c->setSubtotalFlag(false);
    $cg2->addColumn($c);

    $cg->addColumn($cg2);

    // Ansible Engine利用情報
    $cg3 = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000040") );

        /* Ansible virtualenv path*/
        $c = new TextColumn('I_ENGINE_VIRTUALENV_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000027"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000028"));
        $cg3->addColumn($c);
    $cg->addColumn($cg3);

    // Tower利用情報
    $cg4 = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000013") );

        // virtualenv
        $c = new TextColumn('I_VIRTUALENV_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000029"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000030"));
        $cg4->addColumn($c);
    $cg->addColumn($cg4);

    // ansible automation controller利用情報
    $cg5 = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000035") );

        // 実行環境
        $c = new TextColumn('I_EXECUTION_ENVIRONMENT_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000036"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000037")); //エクセル・ヘッダでの説明
        $cg5->addColumn($c);

    $cg->addColumn($cg5);

    $c = new FileUploadColumn('I_ANSIBLE_CONFIG_FILE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000038"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000039"));//エクセル・ヘッダでの説明
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);

    $table->addColumn($cg);
    //作業パターン----

    //----オペレーション
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401015") );

    $c = new TextColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401020"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401030"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $cg->addColumn($c);

    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401050"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);

    $c = new TextColumn('I_OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401055"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401056"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $cg->addColumn($c);

    $table->addColumn($cg);

    //----入力データ
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401059") );

    $c = new FileUploadColumn( "FILE_INPUT", $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401060"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401070"));
    $c->setMaxFileSize(4*1024*1024*1024);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //----入力データ
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401079") );

    $c = new FileUploadColumn( "FILE_RESULT", $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401080"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-401090"));
    $c->setMaxFileSize(4*1024*1024*1024);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //----作業状況
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402009") );

    $c = new DateTimeColumn('TIME_BOOK',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402010"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402020"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402030"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402040"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-402060"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);
    $table->addColumn($cg);


    //収集状況
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207100"));
    //ステータス
    $c = new IDColumn('COLLECT_STATUS',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207101"),'B_COLLECT_STATUS','COLLECT_STATUS_ID','COLLECT_STATUS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207102"));//エクセル・ヘッダでの説明
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('COLLECT_STATUS');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_COLLECT_STATUS_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'COLLECT_STATUS_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'COLLECT_STATUS_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $cg->addColumn($c);

    //収集ログ
    $c = new FileUploadColumn( 'COLLECT_LOG', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207103"), "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207104"));
    $c->setMaxFileSize(4*1024*1024*1024);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);
    

    $table->fixColumn();

    // 廃止・更新・複製ボタンを隠す
    $tmpAryColumn= $table->getColumns();
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_UPDATE']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_table')->setVisible(false);

    // ----非表示項目設定
    // 備考
    $tmpAryColumn['NOTE']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['NOTE']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['NOTE']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['NOTE']->getOutputType('print_journal_table')->setVisible(false);
    $tmpAryColumn['NOTE']->getOutputType('delete_table')->setVisible(false);
    $tmpAryColumn['NOTE']->getOutputType('csv')->setVisible(false);
    $tmpAryColumn['NOTE']->getOutputType('json')->setVisible(false);
    // ----非表示項目設定
    unset($tmpAryColumn);

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
