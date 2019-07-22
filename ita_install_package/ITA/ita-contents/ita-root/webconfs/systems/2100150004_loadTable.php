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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101001");

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

    $table = new TableControlAgent('G_FILE_MANAGEMENT_NEWEST','FILE_M_ID', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101002"), 'G_FILE_MANAGEMENT_NEWEST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['FILE_M_ID']->setSequenceID('F_FILE_MANAGEMENT_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_FILE_MANAGEMENT_JSQ');

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_FILE_MANAGEMENT');
    $table->setDBJournalTableHiddenID('F_FILE_MANAGEMENT_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101003"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101004"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    $c = new IDColumn('FILE_ID',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101005"),'G_FILE_MASTER','FILE_ID','FILE_NAME_FULLPATH','G_FILE_MASTER',array('OrderByThirdColumn'=>'FILE_NAME_FULLPATH'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101006"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $table->addColumn($c);

    $c = new FileUploadColumn('RETURN_FILE',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101007"),"{$g['scheme_n_authority']}/default/menu/05_preupload.php?no={$g['page_dir']}","/uploadfiles/2100150101/RETURN_FILE");
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101008"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(20971520);//単位はバイト
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->setFileHideMode(true);
    $table->addColumn($c);



    $c = new DateColumn('CLOSE_DATE',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101009"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101010"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
	$c->setValidator(new DateValidator(null,null));
    $table->addColumn($c);

    $c = new IDColumn('RETURN_USER_ID',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101011"),'A_ACCOUNT_LIST','USER_ID','USERNAME_JP','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101012"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $table->addColumn($c);

	$objVldt = new SingleTextValidator(0,64,false);
    $c = new TextColumn('CLOSE_REVISION',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101013"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101014"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
	$c->setValidator($objVldt);
    $table->addColumn($c);

    $c = new TextColumn('NEWEST_FLAG',$g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101015"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAMATERIAL-MNU-101016"));//エクセル・ヘッダでの説明
    $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
    $c->setDBColumn(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $table->addColumn($c);

//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
