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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAOPENST-MNU-100010");

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

    $table = new TableControlAgent('D_OPENST_IF_INFO','OPENST_IF_INFO_ID', $g['objMTS']->getSomeMessage("ITAOPENST-MNU-100020"), 'D_OPENST_IF_INFO_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['OPENST_IF_INFO_ID']->setSequenceID('B_OPENST_IF_INFO_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_OPENST_IF_INFO_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_OPENST_IF_INFO');
    $table->setDBJournalTableHiddenID('B_OPENST_IF_INFO_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAOPENST-MNU-100030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-100030"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----




	$objVldt = new SingleTextValidator(1,8,false);
    $c = new TextColumn('OPENST_PROTOCOL',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-100040"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-100050"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('OPENST_HOSTNAME',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-100060"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-100070"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    $objVldt = new SingleTextValidator(1,128,false);
    $c = new TextColumn('OPENST_USER',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-100100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-100110"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $objVldt = new SingleTextValidator(1,64,false);
    $c = new PasswordColumn('OPENST_PASSWORD',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-100120"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-100130"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUpdateRequireExcept(1);//1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setEncodeFunctionName("ky_encrypt");
    $table->addColumn($c);



    $c = new NumColumn('OPENST_REFRESH_INTERVAL',$g['objMTS']->getSomeMessage("ITAOPENST-MNU-100140"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAOPENST-MNU-100150"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setSubtotalFlag(false);
	$c->setValidator(new IntNumValidator(1000,null));
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->setGeneObject('webSetting', $arrayWebSetting);
    $table->fixColumn();
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
    //廃止・復活ボタンを隠す
    $outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
    $tmpAryColumn['DISUSE_FLAG']->setOutputType("print_table", $outputType);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
