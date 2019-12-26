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

// Symphony/オペレーションエクスポート/インポート管理

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900055');

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

    $table = new TableControlAgent('B_DP_SYM_OPE_STATUS','TASK_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-900013'), 'B_DP_SYM_OPE_STATUS_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['TASK_ID']->setSequenceID('B_DP_SYM_OPE_STATUS_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DP_SYM_OPE_STATUS_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITABASEH-MNU-900008'));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage('ITABASEH-MNU-900008'));

    //---- 検索機能の制御
    //---- $table->setGeneObject('binaryDistinctOnDTiS',false);  //('',true,false)
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    //---- $table->setGeneObject('AutoSearchUserControl',false);  //('',true,false)
    // 検索機能の制御----

    // ステータス
    $c = new IDColumn('TASK_STATUS',$g['objMTS']->getSomeMessage('ITABASEH-MNU-900014'),'B_DP_STATUS_MASTER','TASK_ID','TASK_STATUS','');
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-900016'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // 処理種別
    $c = new IDColumn('DP_TYPE',$g['objMTS']->getSomeMessage('ITABASEH-MNU-900022'),'B_DP_TYPE','ROW_ID','DP_TYPE','');
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-900023'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);

    // ファイル
    $c = new FileUploadColumn('FILE_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-900015"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-900017"));
    $c->setMaxFileSize(20971520);//単位はバイト
    $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
    $c->setFileHideMode(true);
    $table->addColumn($c);

    $table->getFormatter('print_table')->setGeneValue("linkExcelHidden",true);//Excel出力廃止

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
