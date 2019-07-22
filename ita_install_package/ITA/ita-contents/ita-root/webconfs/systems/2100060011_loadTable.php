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

    $strLrWebRootToThisPageDir = substr(basename(__FILE__), 0, 10);

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-308090");
/*
Ansible(Dsc)作業管理
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
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('C_DSC_EXE_INS_MNG','EXECUTION_NO',$g['objMTS']->getSomeMessage("ITADSCH-MNU-309010"), 'C_DSC_EXE_INS_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['EXECUTION_NO']->setSequenceID('C_DSC_EXE_INS_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_DSC_EXE_INS_MNG_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-309020"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITADSCH-MNU-309030"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $table->setDBSortKey(array("EXECUTION_NO"=>"DESC"));

    //*************************************************************************************************************
    //----- 2:作業状態確認(ボタン)
    //*************************************************************************************************************
    $strTextBody = $g['objMTS']->getSomeMessage("ITADSCH-MNU-309040");
    $c = new LinkButtonColumn( 'MonitorExecution', $strTextBody, $strTextBody, 'monitor_execution', array( ":EXECUTION_NO" ) );
    $c->setDBColumn(false);
    $table->addColumn($c);

    //*************************************************************************************************************
    //----実行種別
    //*************************************************************************************************************
    $c = new IDColumn('RUN_MODE',$g['objMTS']->getSomeMessage("ITADSCH-MNU-309045"),'D_DSC_INS_RUN_MODE','RUN_MODE_ID','RUN_MODE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-309046"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_DSC_INS_RUN_MODE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('RUN_MODE_ID');
    $c->setJournalDispIDOfMaster('RUN_MODE_NAME');
    $table->addColumn($c);

    //*************************************************************************************************************
    //----ステータス
    //*************************************************************************************************************
    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-309050"),'D_DSC_INS_STATUS','STATUS_ID','STATUS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-309060"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_DSC_INS_STATUS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('STATUS_ID');
    $c->setJournalDispIDOfMaster('STATUS_NAME');
    $table->addColumn($c);

    //*************************************************************************************************************
    //----シンフォニークラス
    //*************************************************************************************************************
    $c = new TextColumn('SYMPHONY_NAME',$g['objMTS']->getSomeMessage("ITADSCH-MNU-401120"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401130"));//エクセル・ヘッダでの説明
    $table->addColumn($c);
    
    //*************************************************************************************************************
    //----実行ユーザ
    //*************************************************************************************************************
    $c = new TextColumn('EXECUTION_USER',$g['objMTS']->getSomeMessage("ITADSCH-MNU-401100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401110"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    //*************************************************************************************************************
    //----作業パターン
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITADSCH-MNU-309065") );

    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-309070"),'E_DSC_PATTERN','PATTERN_ID','PATTERN_ID','');
    $c->setMasterDisplayColumnType(0);
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-309080"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->setJournalTableOfMaster('E_DSC_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN_ID');
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----作業パターン名
    //*************************************************************************************************************
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('I_PATTERN_NAME',$g['objMTS']->getSomeMessage("ITADSCH-MNU-309090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401010"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);
    
    //*************************************************************************************************************
    //----遅延タイマー
    //*************************************************************************************************************
    $c = new NumColumn('I_TIME_LIMIT',$g['objMTS']->getSomeMessage("ITADSCH-MNU-402070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-402080"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----リトライタイムアウト
    //*************************************************************************************************************
    $c = new NumColumn('I_DSC_RETRY_TIMEOUT',$g['objMTS']->getSomeMessage("ITADSCH-MNU-402090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-402100"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $cg->addColumn($c);
    
    //*************************************************************************************************************
    //----オペレーション
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITADSCH-MNU-401015") );

    //*************************************************************************************************************
    //----オペレーションNo
    //*************************************************************************************************************
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITADSCH-MNU-401020"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NO_UAPK','');
    $c->setMasterDisplayColumnType(0);
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401030"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
   $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION_NO_UAPK');
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----オペレーション名
    //*************************************************************************************************************
    $objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITADSCH-MNU-401040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401050"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----オペレーションID
    //*************************************************************************************************************
    $c = new IDColumn('I_OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage("ITADSCH-MNU-401055"),'E_OPERATION_LIST','OPERATION_NO_IDBH','OPERATION_NO_IDBH','');
    $c->setMasterDisplayColumnType(0);
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401056"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_IDBH');
    $c->setJournalDispIDOfMaster('OPERATION_NO_IDBH');
    $cg->addColumn($c);

    $table->addColumn($cg);

    //*************************************************************************************************************
    //----入力データ
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITADSCH-MNU-401059") );

    $c = new FileUploadColumn( "FILE_INPUT", $g['objMTS']->getSomeMessage("ITADSCH-MNU-401060"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401070"));
    $c->setMaxFileSize(1024*1024*20);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //*************************************************************************************************************
    //----出力データ
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITADSCH-MNU-401079") );

    $c = new FileUploadColumn( "FILE_RESULT", $g['objMTS']->getSomeMessage("ITADSCH-MNU-401080"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-401090"));
    $c->setMaxFileSize(1024*1024*20);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //*************************************************************************************************************
    //----作業状況
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITADSCH-MNU-402009") );

    $c = new DateTimeColumn('TIME_BOOK',$g['objMTS']->getSomeMessage("ITADSCH-MNU-402010"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-402020"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----開始日時
    //*************************************************************************************************************
    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITADSCH-MNU-402030"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-402040"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----終了日時
    //*************************************************************************************************************
    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITADSCH-MNU-402050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-402060"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);
    $table->addColumn($cg);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
