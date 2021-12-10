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
//    ・インターフェース情報画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102010");

/*
TERRAFORMインタフェース情報
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

    $table = new TableControlAgent('D_TERRAFORM_IF_INFO','TERRAFORM_IF_INFO_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102020"), 'D_TERRAFORM_IF_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['TERRAFORM_IF_INFO_ID']->setSequenceID('B_TERRAFORM_IF_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_IF_INFO_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_TERRAFORM_IF_INFO');
    $table->setDBJournalTableHiddenID('B_TERRAFORM_IF_INFO_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102030'));

    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102040'));

    $table->setAccessAuth(true);    // データごとのRBAC設定
    $table->setNoRegisterFlg(true);    // 登録画面無し


    //************************************************************************************
    //----Hostname
    //************************************************************************************
    $c = new TextColumn('TERRAFORM_HOSTNAME', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102050'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102060'));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setValidator(new SingleTextValidator(1,256,false));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //************************************************************************************
    //----トークン
    //************************************************************************************
    $c = new PasswordColumn('TERRAFORM_TOKEN', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102070'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102080'));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $objVldt = new SingleTextValidator(1,256,false);
    $c->setValidator($objVldt);
    $c->setEncodeFunctionName("ky_encrypt");
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

////メモ：メッセージから参照
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102150'));
        //************************************************************************************
        //----Proxyアドレス
        //************************************************************************************
        $c = new TextColumn('TERRAFORM_PROXY_ADDRESS', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102160'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102170'));//エクセル・ヘッダでの説明
        $c->setDescription("プロキシサーバのアドレス");//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
        $c->setValidator(new SingleTextValidator(0,128,false));
        $cg->addColumn($c);

        //************************************************************************************
        //----Proxyポート
        //************************************************************************************
        $c = new NumColumn('TERRAFORM_PROXY_PORT', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102180'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102190'));//エクセル・ヘッダでの説明
        $c->setDescription("プロキシサーバのポート");//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
        $c->setSubtotalFlag(false);
        $c->setValidator(new IntNumValidator(1,65535));
        $cg->addColumn($c);

    $table->addColumn($cg);


    //************************************************************************************
    //----状態監視周期(単位ミリ秒)
    //************************************************************************************
    $c = new NumColumn('TERRAFORM_REFRESH_INTERVAL', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102090'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102100'));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1000,null));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //************************************************************************************
    //----進行状態表示行数
    //************************************************************************************
    $c = new NumColumn('TERRAFORM_TAILLOG_LINES',$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102110'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102120'));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(null,null));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    //************************************************************************************
    //----パラメータシートの具体値がNULLでも代入値管理に登録するかのフラグ
    //************************************************************************************
    $c = new IDColumn('NULL_DATA_HANDLING_FLG',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102130"),'B_VALID_INVALID_MASTER','FLAG_ID','FLAG_NAME','', array('OrderByThirdColumn'=>'FLAG_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102140"));
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)
    $c->setRequired(true);//登録/更新時には、入力必須
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('NULL_DATA_HANDLING_FLG');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_VALID_INVALID_MASTER_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'FLAG_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'FLAG_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);
    //パラメータシートの具体値がNULLでも代入値管理に登録するかのフラグ----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    $table->setGeneObject('webSetting', $arrayWebSetting);

    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setResultCount(
        array(
         'update'  =>array('name'=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12203"), 'ct'=>0),
         'error'   =>array('name'=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12206"), 'ct'=>0)
        )
    );
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setCommandArrayForEdit(
        array(
            2=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12203")
        )
    );
    // 廃止ボタン
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('print_journal_table')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['DISUSE_FLAG']->getOutputType('json')->setVisible(false);

    // 複製ボタン
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('filter_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_journal_table')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('excel')->setVisible(false);
    $tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('json')->setVisible(false);

    return $table;

};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
