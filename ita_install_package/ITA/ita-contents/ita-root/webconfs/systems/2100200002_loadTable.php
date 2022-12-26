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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-102010");
/*
Terraform-CLIワークスペース
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

    $table = new TableControlAgent('B_TERRAFORM_CLI_WORKSPACES','WORKSPACE_ID', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-102020"), 'B_TERRAFORM_CLI_WORKSPACES_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['WORKSPACE_ID']->setSequenceID('B_TERRAFORM_CLI_WORKSPACES_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_CLI_WORKSPACES_JSQ');
    unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITATERRAFORMCLI-MNU-102030'));

    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage('ITATERRAFORMCLI-MNU-102040'));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    //Workspaces Name
    $c = new TextColumn('WORKSPACE_NAME', $g['objMTS']->getSomeMessage('ITATERRAFORMCLI-MNU-102050'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORMCLI-MNU-102060'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setValidator(new TextValidator(1, 90, false, '/^[a-zA-Z0-9_-]+$/', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-102070")));
    $table->addColumn($c);

    //workspaceのdestroyボタン
    $c = new LinkButtonColumn('DESTROY', $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-102090"), $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-102100"), '', array());
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORMCLI-MNU-102110')); //エクセル・ヘッダでの説明
    $c->setEvent("print_table", "onClick", "destroyWorkspaceInsRegister", array('this', ':WORKSPACE_ID', ':WORKSPACE_NAME')); //ボタン押下時のイベント
    $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
    $outputType->setVisible(false); //登録・更新時は非表示
    $c->setOutputType("update_table", $outputType);
    $c->setOutputType("register_table", $outputType);
    $c->setDBColumn(false);
    $table->addColumn($c);

    // Movement一覧へのリンクボタン
    $strLabelText = $g['objMTS']->getSomeMessage("ITATERRAFORMCLI-MNU-102080");
    $c = new LinkButtonColumn('ethWakeOrder',$strLabelText, $strLabelText, 'dummy');
    $c->setDBColumn(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setEvent("print_table", "onClick", "newOpenWindow", array(':WORKSPACE_NAME'), true);
    $table->addColumn($c);


    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;

};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
