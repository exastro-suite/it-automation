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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070001");

    // ユーザID
    $table = new TableControlAgent('D_ACCOUNT_LIST','USER_ID', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070101"),'D_ACCOUNT_LIST_JNL');
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070002"));
    $table->getFormatter("excel")->setGeneValue("sheetNameForEditByFile",$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070003"));
    
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    
    $tmpAryObjColumn = $table->getColumns();
    $tmpAryObjColumn['USER_ID']->setSequenceID('SEQ_A_ACCOUNT_LIST');
    $table->setJsEventNamePrefix(true);

    $table->setGeneObject("webSetting", $arrayWebSetting);

    $table->setDBMainTableHiddenID('A_ACCOUNT_LIST');
    $table->setDBJournalTableHiddenID('A_ACCOUNT_LIST_JNL');

    // ログインID
    
    $c = new TextColumn('USERNAME',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070201"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setUnique(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070202"));
    $objVldt = new TextValidator(4, 30, false, '/^[a-zA-Z0-9-!#$%&\'()*+.\/;<=>?@[\]^\\_`{|}~]+$/', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070203"));
    $objVldt->setMinLength(1,"DTiS_filterDefault");
    $c->setValidator($objVldt);
    $table->addColumn($c);

    // ログインPW
    $c = new PasswordColumn('PASSWORD',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070301"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070302"));
    $c->setUpdateRequireExcept(1);//1は空白の場合は維持、それ以外はNULL扱いで更新
    $c->setValidator( new TextValidator(8, 30, false, '/^[a-zA-Z0-9-!"#$%&\'()*+,.\/:;<=>?@[\]^\\_`{|}~]+$/', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070303") ) );
    $table->addColumn($c);

    // ユーザ名
    $c = new TextColumn('USERNAME_JP',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070401"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070402"));
    $c->setValidator(new SingleTextValidator(1, 64, false));
    $table->addColumn($c);

    // メールアドレス
    $c = new TextColumn('MAIL_ADDRESS',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070501"));
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070502"));
    $objVldt = new TextValidator(1, 256, false, '/^[-_+=\.a-zA-Z0-9]+@[-a-zA-Z0-9\.]+$/', $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070503"));
    $objVldt->setRegexp("/^[^\r\n]*$/s","DTiS_filterDefault");
    $c->setValidator($objVldt);

    $table->addColumn($c);

    // ロール情報
    $strLabelText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070601");
    $c = new LinkButtonColumn('RoleInfo',$strLabelText, $strLabelText, 'edit_role_list', array(0, ':USER_ID')); 
    $c->setDBColumn(false);
    $c->setHiddenMainTableColumn(false);
    $table->addColumn($c);

    // PW最終更新日時
    $c = new DateTimeColumn('PW_LAST_UPDATE_TIME',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070701"));
    $c->setHiddenMainTableColumn(true);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070702"));
    $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
    $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
    $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
    $table->addColumn($c);

    // PWカウンタ
    $c = new NumColumn('MISS_INPUT_COUNTER',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070801"));
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070802"));
    $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070803");
    $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
    $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setSubtotalFlag(false);
    $table->addColumn($c);

    // ロック日時
    $c = new DateTimeColumn('LOCKED_TIMESTAMP',$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070901"));
    $c->setHiddenMainTableColumn(false);
    $c->setAllowSendFromFile(false);
    $c->setDescription($g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070902"));
    $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070903");
    $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
    $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
    $c->getOutputType('print_journal_table')->setVisible(false);
    $table->addColumn($c);

    if( $g['privilege'] === '1' ){
        $objFunction = function($rowData){
            $retLinkable = "disabled";
            if( array_key_exists('LOCKED_TIMESTAMP', $rowData) === true && array_key_exists('MISS_INPUT_COUNTER', $rowData) ){
                global $pwl_expiry,$pwl_threshold;
                $boolCheck = saLoginLockCheckInExpiry($pwl_expiry,$rowData['LOCKED_TIMESTAMP']);
                if( $boolCheck === true ){
                    if( $pwl_threshold <= $rowData['MISS_INPUT_COUNTER'] ){
                        $retLinkable = "";
                    }
                }
            }
            return $retLinkable;
        };

        // ロック解除
        $strLabelText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1071001");
        $c = new LinkButtonColumn('LockRemove',$strLabelText, $strLabelText, 'dummy');
        $c->setDBColumn(false);
        $c->setHiddenMainTableColumn(false);
        $c->setOutputType('print_table', new OutputType(new SortedTabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array(""))));
        $c->setEvent("print_table", "onClick", "lock_remove", array(':USER_ID'));
        $c->getOutputType('print_journal_table')->setVisible(false);
        $table->addColumn($c);
    }

    $table->fixColumn();
    
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
