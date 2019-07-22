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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100101");

    $table = new TableControlAgent('F_HOST_LINK_LIST','ROW_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100102"), 'F_HOST_LINK_LIST_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_HOST_LINK_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_HOST_LINK_LIST_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('F_HOST_LINK_LIST');
    $table->setDBJournalTableHiddenID('F_HOST_LINK_LIST_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // エクセルのブック名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100103"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100104"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)

    //---- マルチユニーク制約
    $table->addUniqueColumnSet(array('PA_HOSTGROUP','CH_HOSTGROUP'));

    // ループアラーム
    $c = new IDColumn('LOOPALARM',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100105"),'G_FLAG_MASTER','FLAG_ID','FLAG_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100106"));
    $c->setAllowSendFromFile(false);    //エクセル/CSVからのアップロードを禁止する。
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $table->addColumn($c);

    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100107"));

        $c = new IDColumn('PA_HOSTGROUP',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100108"),'F_HOSTGROUP_LIST','ROW_ID','HOSTGROUP_NAME','');
        $c->setHiddenMainTableColumn(true);        
        $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100109"));
        $c->setRequired(true);
        $cg->addColumn($c);

        $c = new IDColumn('CH_HOSTGROUP',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100110"),'F_HOSTGROUP_LIST','ROW_ID','HOSTGROUP_NAME','');
        $c->setHiddenMainTableColumn(true);        
        $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100111"));
        $c->setRequired(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

    $table->fixColumn();

    // 登録/更新/廃止/復活があった場合、ホストグループ分割対象の分割済みフラグをOFFにする
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global $g;
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

            $strQuery = "UPDATE F_SPLIT_TARGET "
                       ."SET DIVIDED_FLG = :DIVIDED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                       ."WHERE DISUSE_FLAG = :DISUSE_FLAG";

            $g['objDBCA']->setQueryTime();
            $aryForBind = array('DIVIDED_FLG'=>"0", 'LAST_UPDATE_TIMESTAMP'=>$g['objDBCA']->getQueryTime(), 'DISUSE_FLAG'=>"0");

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] !== true ){
                $boolRet = $aryRetBody[0];
                $strErrMsg = $aryRetBody[2];
                $intErrorType = 500;
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn[$table->getDBTablePK()]->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
