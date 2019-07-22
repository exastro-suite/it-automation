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
//    ・ファイル管理画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-800300");
/*
DSCコンフィグデータファイル管理
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

    $table = new TableControlAgent('B_DSC_CONFIGDATA_FILE','CONFIGDATA_FILE_ID', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800301"), 'B_DSC_CONFIGDATA_FILE_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CONFIGDATA_FILE_ID']->setSequenceID('B_DSC_CONFIGDATA_FILE_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DSC_CONFIGDATA_FILE_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-800302"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800303"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----


    //************************************************************************************
    //----ConfigData設定素材名
    //************************************************************************************
    $objVldt = new TextValidator(1, 32, false, '/^[a-zA-Z0-9_]+$/', $g['objMTS']->getSomeMessage("ITADSCH-MNU-800312"));
    $c = new TextColumn('CONFIGDATA_NAME',$g['objMTS']->getSomeMessage("ITADSCH-MNU-800310"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-800311"));//エクセル・ヘッダでの説明
	$c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $table->addColumn($c);

    //************************************************************************************
    //----ConfigData設定ファイル
    //************************************************************************************
    $c = new FileUploadColumn('CONFIGDATA_FILE',$g['objMTS']->getSomeMessage("ITADSCH-MNU-800320"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-800321"));//エクセル・ヘッダでの説明
    $c->setMaxFileSize(20971520);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
