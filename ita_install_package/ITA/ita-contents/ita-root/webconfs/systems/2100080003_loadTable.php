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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102410");
/*--------↑
投入オペレーション一覧情報
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

    $table = new TableControlAgent('B_TERRAFORM_WORKSPACES','WORKSPACE_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102420"), 'B_TERRAFORM_WORKSPACES_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['WORKSPACE_ID']->setSequenceID('B_TERRAFORM_WORKSPACES_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_WORKSPACES_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102430'));

    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102440'));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //Organization ID
    $url = "01_browse.php?no=2100080002&filter=on&Filter1Tbl_2=";
    $c = new LinkIDColumn('ORGANIZATION_ID', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102450'), 'B_TERRAFORM_ORGANIZATIONS', 'ORGANIZATION_ID', 'ORGANIZATION_NAME', $url, false, false, '', '', '', '');
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102460')); //エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_TERRAFORM_ORGANIZATIONS_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('ORGANIZATION_ID');
    $c->setJournalDispIDOfMaster('ORGANIZATION_NAME');
    $c->setRequired(true); //登録/更新時には、入力必須
    $table->addColumn($c);

    //Workspaces Name
    $c = new TextColumn('WORKSPACE_NAME', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102470'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102480'));//エクセル・ヘッダでの説明
    $c->setRequired(true);//登録/更新時には、入力必須
    $c->setValidator(new TextValidator(1, 90, false, '/^[a-zA-Z0-9_-]+$/', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102485")));
    $table->addColumn($c);

    //Terraform Version
    $c = new TextColumn('TERRAFORM_VERSION', $g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102510'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-102520'));//エクセル・ヘッダでの説明
    $c->setValidator(new TextValidator(0, 32, false, '/^(|[0-9.]+)$/', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102525")));
    $table->addColumn($c);

    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102530"));
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-106380"));
        //Terraform連携状態チェック
        $c = new LinkButtonColumn('CHECK', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102540"), $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102550"), '', array());
        $outputType = new OutputType(new TabHFmt(), new LinkButtonTabBFmt());
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106400')); //エクセル・ヘッダでの説明
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "checkWorkspace", array('this', ':WORKSPACE_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        //連携状態の表示
        $c = new TextColumn('CHECK_RESULT',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102560"));
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //フィルタ・登録・更新・変更履歴時は非表示
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106410')); //エクセル・ヘッダでの説明
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
        $c = new LinkButtonColumn('REGISTER', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102570"), $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102580"), '', array());
        $outputType = new OutputType(new TabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array("")));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106420')); //エクセル・ヘッダでの説明
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "registerWorkspace", array('this', ':WORKSPACE_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        //Terraform更新ボタン
        $c = new LinkButtonColumn('UPDATE', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102590"), $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102600"), '', array());
        $outputType = new OutputType(new TabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array("")));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106430')); //エクセル・ヘッダでの説明
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "updateWorkspace", array('this', ':WORKSPACE_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        //Terraform削除ボタン
        $c = new LinkButtonColumn('DELETE', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102610"), $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-102620"), '', array());
        $outputType = new OutputType(new TabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array("")));
        $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106440')); //エクセル・ヘッダでの説明
        $c->setOutputType("print_table", $outputType);
        $c->setEvent("print_table", "onClick", "deleteWorkspace", array('this', ':WORKSPACE_ID')); //ボタン押下時のイベント
        $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
        $outputType->setVisible(false); //登録・更新時は非表示
        $c->setOutputType("update_table", $outputType);
        $c->setOutputType("register_table", $outputType);
        $c->setDBColumn(false);
        $cg->addColumn($c);

        $cgg->addColumn($cg);
    // $table->addColumn($cgg);

    //workspaceのdestroyボタン
    $c = new LinkButtonColumn('DESTROY', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-106370"), $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-106390"), '', array());
    $outputType = new OutputType(new TabHFmt(), new LinkButtonTabBFmt(0, array($objFunction), array("")));
    $c->setDescription($g['objMTS']->getSomeMessage('ITATERRAFORM-MNU-106450')); //エクセル・ヘッダでの説明
    $c->setOutputType("print_table", $outputType);
    $c->setEvent("print_table", "onClick", "destroyWorkspaceInsRegister", array('this', ':WORKSPACE_ID', ':WORKSPACE_NAME')); //ボタン押下時のイベント
    $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
    $outputType->setVisible(false); //登録・更新時は非表示
    $c->setOutputType("update_table", $outputType);
    $c->setOutputType("register_table", $outputType);
    $c->setDBColumn(false);

    $cgg->addColumn($c);
    $table->addColumn($cgg);

    // PolicySet-Workspace紐付管理へのリンクボタン
    $strLabelText = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-107010");
    $c = new LinkButtonColumn('ethWakeOrder',$strLabelText, $strLabelText, 'dummy');
    $c->setDBColumn(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setEvent("print_table", "onClick", "newOpenWindow", array('this',':WORKSPACE_NAME'), true);
    $table->addColumn($c);

    // PolicySet-Workspace紐付管理へのリンクボタン
    $strLabelText = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-107050");
    $c = new LinkButtonColumn('ethWakeOrder2',$strLabelText, $strLabelText, 'dummy');
    $c->setDBColumn(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setEvent("print_table", "onClick", "newOpenWindow2", array('this',':WORKSPACE_NAME'), true);
    $table->addColumn($c);



//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('ORGANIZATION_ID', 'WORKSPACE_NAME'));

//tail of setting [multi-set-unique]----

    $table->fixColumn();

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;

};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
