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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103810");
/*
Terraform作業管理
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

    $table = new TableControlAgent('C_TERRAFORM_EXE_INS_MNG','EXECUTION_NO',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103820"), 'C_TERRAFORM_EXE_INS_MNG_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['EXECUTION_NO']->setSequenceID('C_TERRAFORM_EXE_INS_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_TERRAFORM_EXE_INS_MNG_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103830"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103840"));

    $table->setAccessAuth(true);    // データごとのRBAC設定
    $table->setNoRegisterFlg(true);    // 登録画面無し


    $table->setDBSortKey(array("EXECUTION_NO"=>"DESC"));

    //*************************************************************************************************************
    //----- 2:作業状態確認(ボタン)
    //*************************************************************************************************************
    $strTextBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103850");
    $c = new LinkButtonColumn( 'MonitorExecution', $strTextBody, $strTextBody, 'monitor_execution', array( ":EXECUTION_NO" ) );
    $c->setDBColumn(false);
    $table->addColumn($c);

    //*************************************************************************************************************
    //----実行種別
    //*************************************************************************************************************
    $c = new IDColumn('RUN_MODE',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103860"),'D_TERRAFORM_INS_RUN_MODE','RUN_MODE_ID','RUN_MODE_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103870"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_TERRAFORM_INS_RUN_MODE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('RUN_MODE_ID');
    $c->setJournalDispIDOfMaster('RUN_MODE_NAME');
    $table->addColumn($c);

    //*************************************************************************************************************
    //----ステータス
    //*************************************************************************************************************
    $c = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103880"),'D_TERRAFORM_INS_STATUS','STATUS_ID','STATUS_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103890"));//エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('D_TERRAFORM_INS_STATUS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('STATUS_ID');
    $c->setJournalDispIDOfMaster('STATUS_NAME');
    $table->addColumn($c);

    //*************************************************************************************************************
    //----シンフォニークラス
    //*************************************************************************************************************
    $c = new TextColumn('SYMPHONY_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103900"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103910"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    //*************************************************************************************************************
    //----コンダクタークラス
    //*************************************************************************************************************
    $c = new TextColumn('CONDUCTOR_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104260"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104270"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    //*************************************************************************************************************
    //----実行ユーザ
    //*************************************************************************************************************
    $c = new TextColumn('EXECUTION_USER',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103920"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103930"));//エクセル・ヘッダでの説明
    $table->addColumn($c);


    //*************************************************************************************************************
    //----作業パターン
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103940") );

    $c = new TextColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103950"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103960"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----作業パターン名
    //*************************************************************************************************************
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('I_PATTERN_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103970"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103980"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);
    
    //*************************************************************************************************************
    //----遅延タイマー
    //*************************************************************************************************************
    $c = new NumColumn('I_TIME_LIMIT',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103990"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104000"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $cg->addColumn($c);

        //*************************************************************************************************************
        //----Terraform利用情報
        //*************************************************************************************************************
        $cg2 = new ColumnGroup( $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104010") );
            //*************************************************************************************************************
            //----Organization:Workspace
            //*************************************************************************************************************
            $c = new TextColumn('I_TERRAFORM_ORGANIZATION_WORKSPACE',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104020"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104030"));//エクセル・ヘッダでの説明
            $c->setRequired(true);//登録/更新時には、入力必須
            $cg2->addColumn($c);

            //*************************************************************************************************************
            //----TFE RUN ID
            //*************************************************************************************************************
            $c = new TextColumn('I_TERRAFORM_RUN_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104040"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104050"));//エクセル・ヘッダでの説明
        $cg2->addColumn($c);

    $cg->addColumn($cg2);
    $table->addColumn($cg);
    
    //*************************************************************************************************************
    //----オペレーション
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104060") );

    //*************************************************************************************************************
    //----オペレーションNo
    //*************************************************************************************************************
    $c = new TextColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104080"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----オペレーション名
    //*************************************************************************************************************
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('I_OPERATION_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104100"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----オペレーションID
    //*************************************************************************************************************
    $c = new TextColumn('I_OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104120"));//エクセル・ヘッダでの説明
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $cg->addColumn($c);

    $table->addColumn($cg);

    //*************************************************************************************************************
    //----入力データ
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104130") );

    $c = new FileUploadColumn( "FILE_INPUT", $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104140"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104150"));
    $c->setMaxFileSize(4*1024*1024*1024);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //*************************************************************************************************************
    //----出力データ
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104160") );

    $c = new FileUploadColumn( "FILE_RESULT", $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104170"), 
                               "{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$strLrWebRootToThisPageDir}");
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104180"));
    $c->setMaxFileSize(4*1024*1024*1024);
    $c->setFileHideMode(true);
    $c->setHiddenMainTableColumn(true);
    $cg->addColumn($c);
    $table->addColumn($cg);

    //*************************************************************************************************************
    //----作業状況
    //*************************************************************************************************************
    $cg = new ColumnGroup( $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104190") );

    $c = new DateTimeColumn('TIME_BOOK',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104200"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104210"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----開始日時
    //*************************************************************************************************************
    $c = new DateTimeColumn('TIME_START',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104220"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104230"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
    $cg->addColumn($c);

    //*************************************************************************************************************
    //----終了日時
    //*************************************************************************************************************
    $c = new DateTimeColumn('TIME_END',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104240"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104250"));//エクセル・ヘッダでの説明
    $c->setValidator(new DateTimeValidator(null,null));
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
