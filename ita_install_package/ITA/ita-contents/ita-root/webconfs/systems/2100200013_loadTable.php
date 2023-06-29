<?php
//   Copyright 2022 NEC Corporation
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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120010");
/*
Terraform-CLI メンバー変数管理
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

    $table = new TableControlAgent('D_TERRAFORM_CLI_VAR_MEMBER', 'CHILD_MEMBER_VARS_ID', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120020"), 'D_TERRAFORM_CLI_VAR_MEMBER_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CHILD_MEMBER_VARS_ID']->setSequenceID('B_TERRAFORM_CLI_VAR_MEMBER_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_CLI_VAR_MEMBER_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_TERRAFORM_CLI_VAR_MEMBER');
    $table->setDBJournalTableHiddenID('B_TERRAFORM_CLI_VAR_MEMBER_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120030"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120040"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    // 元変数
    $c = new IDColumn('PARENT_VARS_ID', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120050"), 'B_TERRAFORM_CLI_MODULE_VARS_LINK', 'MODULE_VARS_LINK_ID', 'VARS_NAME');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120060")); //エクセル・ヘッダでの説明
    $c->setRequired(true); //登録/更新時には、入力必須
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 親メンバ変数のID
    $c = new IDColumn('PARENT_MEMBER_VARS_ID', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120060"), 'D_TERRAFORM_CLI_VAR_MEMBER', 'CHILD_MEMBER_VARS_ID', 'CHILD_MEMBER_VARS_ID');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120080")); //エクセル・ヘッダでの説明
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 子メンバ変数のKEY
    $c = new TextColumn('CHILD_MEMBER_VARS_KEY', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120090"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120100")); //エクセル・ヘッダでの説明
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 階層を含めたメンバー変数名
    $c = new TextColumn('CHILD_MEMBER_VARS_NEST', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120120")); //エクセル・ヘッダでの説明
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 変数型のタイプ
    $c = new IDColumn('CHILD_VARS_TYPE_ID', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120130"), 'B_TERRAFORM_CLI_TYPES_MASTER', 'TYPE_ID', 'TYPE_NAME');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120140")); //エクセル・ヘッダでの説明
    $c->setRequired(true); //登録/更新時には、入力必須
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 子メンバ変数の階層
    $c = new NumColumn('ARRAY_NEST_LEVEL', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120150"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120160")); //エクセル・ヘッダでの説明
    $c->setRequired(true); //登録/更新時には、入力必須
    $c->setSubtotalFlag(false);
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 子メンバ変数のVALUE
    $c = new TextColumn('CHILD_MEMBER_VARS_VALUE', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120170"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120180")); //エクセル・ヘッダでの説明
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 列順序
    $c = new NumColumn('ASSIGN_SEQ', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120190"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120200")); //エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    // 代入値管理系の表示有無
    $c = new NumColumn('VARS_ASSIGN_FLAG', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120210"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-120220")); //エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
    $c->setAllowSendFromFile(false);
    $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
    $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
    $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
    $table->addColumn($c);

    $table->fixColumn();


$tmpAryColumn = $table->getColumns();
    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
