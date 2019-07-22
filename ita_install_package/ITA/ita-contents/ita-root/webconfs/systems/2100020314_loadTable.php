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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108020");
/*
Ansible(Legacy)作業管理
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

    $table = new TableControlAgent('C_ANSIBLE_LRL_EXE_INS_MNG','EXECUTION_NO',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108030"), 'C_ANSIBLE_LRL_EXE_INS_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['EXECUTION_NO']->setSequenceID('C_ANSIBLE_LRL_EXE_INS_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_ANSIBLE_LRL_EXE_INS_MNG_JSQ');
    unset($tmpAryColumn);

    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108040"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108050"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    $table->setDBSortKey(array("EXECUTION_NO"=>"DESC"));

    //----- 2:作業状態確認(ボタン)
    $strTextBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108060");
    $c = new LinkButtonColumn( 'MonitorExecution', $strTextBody, $strTextBody, 'monitor_execution', array( ":EXECUTION_NO" ) );
    $c->setDBColumn(false);
    $table->addColumn($c);

    //実行種別
    $c = new IDColumn('RUN_MODE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108065"),'D_ANSIBLE_LRL_INS_RUN_MODE','RUN_MODE_ID','RUN_MODE_NAME','');
    //$c->setDescription('任意カラム01・ラベル・エクセル説明');//エクセル・ヘッダでの説明
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108066"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_ANSIBLE_LRL_INS_RUN_MODE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('RUN_MODE_ID');
    $c->setJournalDispIDOfMaster('RUN_MODE_NAME');
    $table->addColumn($c);

    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108070"),'D_ANSIBLE_LRL_INS_STATUS','STATUS_ID','STATUS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108080"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_ANSIBLE_LRL_INS_STATUS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('STATUS_ID');
    $c->setJournalDispIDOfMaster('STATUS_NAME');
    $table->addColumn($c);
    
    //シンフォニークラス
    $c = new TextColumn('SYMPHONY_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108120"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108130"));//エクセル・ヘッダでの説明
    $table->addColumn($c);
    
    //実行ユーザ
    $c = new TextColumn('EXECUTION_USER',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108110"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    //----作業パターン
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108085") );

    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1108090"),'E_ANSIBLE_LRL_PATTERN','PATTERN_ID','PATTERN_ID','');
    $c->setMasterDisplayColumnType(0);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109010"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->setJournalTableOfMaster('E_ANSIBLE_LRL_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN_ID');
    $cg->addColumn($c);

    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('I_PATTERN_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109020"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109030"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);

    $c = new NumColumn('I_TIME_LIMIT',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202010"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $cg->addColumn($c);

    // Ansible利用情報
    $cg2 = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202011") );

    // ホスト指定形式
    $c = new IDColumn('I_ANS_HOST_DESIGNATE_TYPE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202012"),'B_HOST_DESIGNATE_TYPE_LIST','HOST_DESIGNATE_TYPE_ID','HOST_DESIGNATE_TYPE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202013"));//エクセル・ヘッダでの説明
    $cg2->addColumn($c);

    // 並列実行数
    $c = new NumColumn('I_ANS_PARALLEL_EXE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202016"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202017"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $cg2->addColumn($c);

    // WinRM接続
    $c = new IDColumn('I_ANS_WINRM_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202014"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1202015"));//エクセル・ヘッダでの説明
    $cg2->addColumn($c);

    // セクションヘッダー
    $c = new MultiTextColumn('I_ANS_PLAYBOOK_HED_DEF',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000008"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-9010000009"));//エクセル・ヘッダでの説明
    $cg2->addColumn($c);

    $cg->addColumn($cg2);
    $table->addColumn($cg);

    //----オペレーション
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109035") );

    //no.
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109040"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NO_UAPK','');
    $c->setMasterDisplayColumnType(0);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109050"));//エクセル・ヘッダでの説明
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


    $objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109080"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);


    // ID
    $c = new IDColumn('I_OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109085"),'E_OPERATION_LIST','OPERATION_NO_IDBH','OPERATION_NO_IDBH','');
    $c->setMasterDisplayColumnType(0);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109086"));//エクセル・ヘッダでの説明
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

    //入力データ
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109089") );

    //"投入データ", 
    $c = new FileUploadColumn( 'FILE_INPUT', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1109090"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1509060"));
    $c->setMaxFileSize(1024*1024*20);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //出力データ
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201009") );
    
    $c = new FileUploadColumn( "FILE_RESULT", $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201010"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201020"));
    $c->setMaxFileSize(1024*1024*20);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //作業状況
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201029") );

    $c = new DateTimeColumn('TIME_BOOK',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201030"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201040"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201050"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201060"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1201080"));//エクセル・ヘッダでの説明
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
