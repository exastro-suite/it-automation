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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102401");

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

    $table = new TableControlAgent('F_CREATE_MENU_STATUS','MM_STATUS_ID', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102402"), 'F_CREATE_MENU_STATUS_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MM_STATUS_ID']->setSequenceID('F_CREATE_MENU_STATUS_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_CREATE_MENU_STATUS_JSQ');
    unset($tmpAryColumn);


    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102403"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102404"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    // メニュー名
    $c01 = new IDColumn('CREATE_MENU_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102405"),'F_CREATE_MENU_INFO','CREATE_MENU_ID','MENU_NAME','');
    $c01->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102406"));//エクセル・ヘッダでの説明
    $c01->setRequired(true);//登録/更新時には、入力必須
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('CREATE_MENU_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'F_CREATE_MENU_INFO_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'CREATE_MENU_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'MENU_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c01->setOutputType('print_journal_table',$objOT);

    // ステータス
    $c02 = new IDColumn('STATUS_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102407"),'F_CM_STATUS_MASTER','STATUS_ID','STATUS_NAME','',array('OrderByThirdColumn'=>'STATUS_ID'));
    $c02->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102408"));//エクセル・ヘッダでの説明
    $c02->setRequired(true);//登録/更新時には、入力必須
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('STATUS_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'F_CM_STATUS_MASTER_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'STATUS_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'STATUS_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c02->setOutputType('print_journal_table',$objOT);

    // メニュー作成タイプ
    $c05 = new IDColumn('MENU_CREATE_TYPE_ID',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102413"),'F_MENU_CREATE_TYPE','MENU_CREATE_TYPE_ID','MENU_CREATE_TYPE_NAME','',array('OrderByThirdColumn'=>'MENU_CREATE_TYPE_ID'));
    $c05->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102414"));//エクセル・ヘッダでの説明
    $c05->setRequired(true);//登録/更新時には、入力必須
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('MENU_CREATE_TYPE_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'F_MENU_CREATE_TYPE_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_CREATE_TYPE_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'MENU_CREATE_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c05->setOutputType('print_journal_table',$objOT);

    // メニュー資材
    $c03 = new FileUploadColumn('FILE_NAME',$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102409"));
    $c03->setDescription($g['objMTS']->getSomeMessage("ITACREPAR-MNU-102410"));//エクセル・ヘッダでの説明
    $c03->setMaxFileSize(4*1024*1024*1024);//単位はバイト
    $c03->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c03->setFileHideMode(true);
    $c03->setAllowUploadColmnSendRestApi(true);   //REST APIからのアップロード可否。FileUploadColumnのみ有効(default:false)
    $table->addColumn($c01);

    $table->addColumn($c02);

    $table->addColumn($c03);

    $table->addColumn($c05);

    // 項目作成情報へのリンク
    $c04 = new LinkButtonColumn('detail_show', $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102411"), $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102412"), 'jumpToCreateMenu', array(':FILE_NAME')); 
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