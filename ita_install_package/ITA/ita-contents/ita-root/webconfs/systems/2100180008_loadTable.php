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
//    ・基本コンソール　Symphony紐付Movement一覧
//      OASE専用ユーザーからRESTAPIでアクセス
//      OASE専用ユーザー以外はメニュー・ロール紐付で廃止 
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;


    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-308100");
/*
Symphony紐付Movementの一覧
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

    $table = new TableControlAgent('C_NODE_TERMINALS_EDIT_CLASS_MNG','TERMINAL_CLASS_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308101"), 'C_NODE_TERMINALS_EDIT_CLASS_MNG_JNL', $tmpAry); 
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['TERMINAL_CLASS_NO']->setSequenceID('C_NODE_TERMINALS_EDIT_CLASS_MNG_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_NODE_TERMINALS_EDIT_CLASS_MNG_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-308102"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-308102"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    $aryTermClassColumn = array(
        "TERMINAL_CLASS_NAME"=>"",
        "TERMINAL_TYPE_ID"=>"",
        "NODE_CLASS_NO"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONNECTED_NODE_NAME"=>"",
        "TERMINAL_NAME"=>"",
        "CONDITIONAL_ID"=>"",
        "CASE_NO"=>"",
    );

    $c = new NumColumn('TERMINAL_TYPE_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308104"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308104"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('TERMINAL_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308105"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308105"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('NODE_CLASS_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308106"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308106"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('CONDUCTOR_CLASS_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308107"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308107"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('CONNECTED_NODE_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308108"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308108"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new TextColumn('CONDITIONAL_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308109"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308109"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    $c = new NumColumn('CASE_NO', $g['objMTS']->getSomeMessage("ITABASEH-MNU-308110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-308110"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $table->addColumn($c);
    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
