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
//      CI/CD For IaC 資材管理 
//
//////////////////////////////////////////////////////////////////////

/* ルートディレクトリの取得 */
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020000");

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

    $table = new TableControlAgent('B_CICD_MATERIAL_LIST','MATL_ROW_ID', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020001"), 'B_CICD_MATERIAL_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MATL_ROW_ID']->setSequenceID('B_CICD_MATERIAL_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CICD_MATERIAL_LIST_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    /////////////////////////////////////////////////////////
    // リポジトリ(名)  必須入力:true ユニーク:false
    ///////////////////////////////////////////////////////// 
    $c = new IDColumn('REPO_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020100"),'B_CICD_REPOSITORY_LIST','REPO_ROW_ID','REPO_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('REPO_ROW_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020101"));
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('REPO_ROW_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_CICD_REPOSITORY_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'REPO_ROW_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'REPO_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    /////////////////////////////////////////////////////////
    // 資材ファイルパス  必須入力:true ユニーク:false
    ///////////////////////////////////////////////////////// 
    $objVldt = new SingleTextValidator(1,4096,false);
    $c = new TextColumn('MATL_FILE_PATH',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020200"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020201"));
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $table->addColumn($c);


//    /////////////////////////////////////////////////////////
//    // 資材タイプ  必須入力:true ユニーク:false
//    ///////////////////////////////////////////////////////// 
//    $c = new IDColumn('MATL_FILE_TYPE_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020300"),'B_CICD_MATERIAL_FILE_TYPE_NAME','MATL_FILE_TYPE_ROW_ID','MATL_FILE_TYPE_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
//    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200020301"));
//    $c->setRequired(true);
//    $table->addColumn($c);

    //----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('REPO_ROW_ID', 'MATL_FILE_PATH'));
    //tail of setting [multi-set-unique]----

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
