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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104310");
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

    $table = new TableControlAgent('B_TERRAFORM_MODULE_VARS_LINK','MODULE_VARS_LINK_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104320"), 'B_TERRAFORM_MODULE_VARS_LINK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MODULE_VARS_LINK_ID']->setSequenceID('B_TERRAFORM_MODULE_VARS_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_MODULE_VARS_LINK_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104330"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104340"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //モジュール素材
    $c = new IDColumn('MODULE_MATTER_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104350"), 'B_TERRAFORM_MODULE', 'MODULE_MATTER_ID', 'MODULE_MATTER_NAME', '');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104360")); //エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_TERRAFORM_MODULE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('MODULE_MATTER_ID');
    $c->setJournalDispIDOfMaster('MODULE_MATTER_NAME');
    $c->setRequired(true); //登録/更新時には、入力必須
    $table->addColumn($c);

    //変数名
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('VARS_NAME',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104370"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104380"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);
    $table->addColumn($c);

    //変数説明
    $objVldt = new SingleTextValidator(0,256,false);
    $c = new TextColumn('VARS_DESCRIPTION',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104390"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104400"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $table->addColumn($c);

    //タイプID
    $c = new IDColumn('TYPE_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104410"), 'B_TERRAFORM_TYPES_MASTER', 'TYPE_ID', 'TYPE_NAME', '');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104420")); //エクセル・ヘッダでの説明
    $table->addColumn($c);

    //デフォルト値
    $c = new TextColumn('VARS_VALUE', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-104430"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109510")); //エクセル・ヘッダでの説明
    $table->addColumn($c);


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
