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
//    ・Organizationsの管理ページ
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102210");
/*
Organizations管理
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

    $table = new TableControlAgent('B_TERRAFORM_ORGANIZATIONS','ORGANIZATION_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102220"), 'B_TERRAFORM_ORGANIZATIONS_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ORGANIZATION_ID']->setSequenceID('B_TERRAFORM_ORGANIZATIONS_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_ORGANIZATIONS_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102230'));

    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102240'));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //Organization name
    $c = new TextColumn('ORGANIZATION_NAME', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102250'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102260'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setUnique(true);
    $c->setValidator(new TextValidator(1, 40, false, '/^[a-zA-Z0-9_-]+$/', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102265")));
    $table->addColumn($c);

    //Email adddress
    $c = new TextColumn('EMAIL_ADDRESS',$g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102270'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102280'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $objVldt = new TextValidator(1, 128, false, '/^[a-zA-Z0-9_+-]+(\.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070503"));
    $objVldt->setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");
    $c->setValidator($objVldt);
    $table->addColumn($c);


    $cg = new ColumnGroup($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102290'));
        //TFE連携状態チェック
        $c = new LinkButtonColumn('CHECK', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102300'), $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102310'), '', array());
        $outputType = new OutputType(new TabHFmt(), new LinkButtonTabBFmt());
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106400'));
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "checkOrganization", array('this', ':ORGANIZATION_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        //連携状態の表示
        $c = new TextColumn('CHECK_RESULT', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102320'));
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //フィルタ・登録・更新・変更履歴時は非表示
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106410'));
        $c->setOutputType("filter_table", $outputType);
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setOutputType("print_journal_table", $outputType);
        $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。
        $cg->addColumn($c);

        //デフォルトでボタンを非活性にする
        $objFunction = function(){
            return "disabled";
        };

        //Terraform登録ボタン
        $c = new LinkButtonColumn('REGISTER', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102330'), $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102340'), '', array());
        $outputType = new OutputType(new TabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array("")));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106470'));
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "registerOrganization", array('this', ':ORGANIZATION_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        //Terraform更新ボタン
        $c = new LinkButtonColumn('UPDATE', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102350'), $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102360'), '', array());
        $outputType = new OutputType(new TabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array("")));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106480'));
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "updateOrganization", array('this', ':ORGANIZATION_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        //Terraform削除ボタン
        $c = new LinkButtonColumn('DELETE', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102370'), $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102380'), '', array());
        $outputType = new OutputType(new TabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array("")));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106490'));
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "deleteOrganization", array('this', ':ORGANIZATION_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

    $table->addColumn($cg);

    // Workspaces管理へのリンクボタン
    $strLabelText = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-107020");
    $c = new LinkButtonColumn('ethWakeOrder',$strLabelText, $strLabelText, 'dummy');
    $c->setDBColumn(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setEvent("print_table", "onClick", "newOpenWindow", array(':ORGANIZATION_NAME'), true);
    $table->addColumn($c);

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);


    return $table;

};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
