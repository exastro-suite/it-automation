<?php
//   Copyright 2021 NEC Corporation
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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108010");
/*
Terraform Module変数紐付管理
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

    $table = new TableControlAgent('D_TERRAFORM_PTN_VAR_LIST','MODULE_PTN_LINK_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108020"), 'D_TERRAFORM_PTN_VAR_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108040"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    //Movement
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108050"),'E_TERRAFORM_PATTERN','PATTERN_ID','PATTERN');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108060")); //エクセル・ヘッダでの説明
    $table->addColumn($c);

    //変数名
    $c = new IDColumn('MODULE_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108070"),'B_TERRAFORM_MODULE_VARS_LINK','MODULE_VARS_LINK_ID','VARS_NAME');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-108080"));//エクセル・ヘッダでの説明
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
