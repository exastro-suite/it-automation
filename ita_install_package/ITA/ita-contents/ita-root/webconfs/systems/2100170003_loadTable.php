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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100201");

    $table = new TableControlAgent('F_HOST_LINK','ROW_ID', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100202"), 'F_HOST_LINK_JNL' );
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_ID']->setSequenceID('F_HOST_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('F_HOST_LINK_JSQ');
    unset($tmpAryColumn);

    // エクセルのブック名
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100203"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100204"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //---- マルチユニーク制約
    $table->addUniqueColumnSet(array('HOSTGROUP_NAME','OPERATION_ID','HOSTNAME'));

    // ホストグループ名
    $c = new IDColumn('HOSTGROUP_NAME',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100205"),'F_HOSTGROUP_LIST','ROW_ID','HOSTGROUP_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100206"));
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('HOSTGROUP_NAME');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'F_HOSTGROUP_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'ROW_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'HOSTGROUP_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    // オペレーション
    $c = new IDColumn('OPERATION_ID',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100207"),'G_OPERATION_LIST','OPERATION_ID','OPERATION_ID_N_NAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100208"));
    $table->addColumn($c);

    // ホスト名
    $c = new IDColumn('HOSTNAME',$g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100209"),'C_STM_LIST','SYSTEM_ID','HOSTNAME','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAHOSTGROUP-MNU-100210"));
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('HOSTNAME');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'C_STM_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'SYSTEM_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'HOSTNAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

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
