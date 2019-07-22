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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103401");

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

    $table = new TableControlAgent('F_CREATE_MST_MENU_STATUS','MM_STATUS_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103402"), 'F_CREATE_MST_MENU_STATUS_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MM_STATUS_ID']->setSequenceID('F_CREATE_MST_MENU_STATUS_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_CREATE_MST_MENU_STATUS_JSQ');
    unset($tmpAryColumn);


    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103403"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103404"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',false);  //('',true,false)
    // 検索機能の制御----


    // メニュー名
    $c01 = new IDColumn('CREATE_MENU_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103405"),'F_CREATE_MST_MENU_INFO','CREATE_MENU_ID','MENU_NAME','');
    $c01->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103406"));//エクセル・ヘッダでの説明
    $c01->setRequired(true);//登録/更新時には、入力必須

    // ステータス
    $c02 = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103407"),'F_CM_STATUS_MASTER','STATUS_ID','STATUS_NAME','',array('OrderByThirdColumn'=>'STATUS_ID'));
    $c02->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103408"));//エクセル・ヘッダでの説明
    $c02->setRequired(true);//登録/更新時には、入力必須

    // メニュー資材
    $c03 = new FileUploadColumn('FILE_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-103409"));
    $c03->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-103410"));//エクセル・ヘッダでの説明
    $c03->setMaxFileSize(20971520);//単位はバイト
    $c03->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c03->setFileHideMode(true);
    $c03->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
    $table->addColumn($c01);

    $table->addColumn($c02);

    $table->addColumn($c03);

    // 項目作成情報へのリンク
    $c04 = new LinkButtonColumn('detail_show', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103411"), $g['objMTS']->getSomeMessage("ITACREPAR-MNU-103412"), 'jumpToCreateMenu', array(':FILE_NAME')); 
    $table->addColumn($c04);


//----head of setting [multi-set-unique]

//tail of setting [multi-set-unique]----


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>