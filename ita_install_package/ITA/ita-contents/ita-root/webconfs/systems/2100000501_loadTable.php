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
//    ・紐付対象メニュー
//
//////////////////////////////////////////////////////////////////////
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-211000");
/*
紐付対象メニュー
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
    $table = new TableControlAgent('D_CMDB_MENU_LIST','MENU_LIST_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211001") , 'D_CMDB_MENU_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MENU_LIST_ID']->setSequenceID('B_CMDB_MENU_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CMDB_MENU_LIST_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_CMDB_MENU_LIST');
    $table->setDBJournalTableHiddenID('B_CMDB_MENU_LIST_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-211002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITABASEH-MNU-211003"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----


    // カラムグループ メニューグループ(一覧のみ表示)
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-211004"));

        $c = new IDColumn('MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211005"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID'));
        $c->addClass("number");
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-211006"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        $c->setDeleteOffBeforeCheck(false);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $aryTraceQuery = array(
            array(
                'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
                'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
                'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_ID',
                'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
    
        $objOT->setTraceQuery($aryTraceQuery);
        $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
        $c->setOutputType('print_journal_table',$objOT);
        $c->setMasterDisplayColumnType(0);

        $cg->addColumn($c);
    
        $c = new TextColumn('MENU_GROUP_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211007"));
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-211008"));
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
    
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $aryTraceQuery = array(
            array(
                    'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_ID',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                ),
            array(
                    'TRACE_TARGET_TABLE'=>'A_MENU_GROUP_LIST_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_GROUP_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'MENU_GROUP_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
        $c->setOutputType('print_journal_table',$objOT);

    $cg->addColumn($c);

    $table->addColumn($cg);
    // カラムグループ（メニューグループ）----

    // カラムグループ メニュー(一覧のみ表示)
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-211009"));
        // メニューID
        $c = new IDColumn('MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211010"), "D_MENU_LIST", 'MENU_ID', "MENU_ID", '', array('OrderByThirdColumn'=>'MENU_ID'));
        $c->addClass("number");
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-211011"));
        $c->setJournalTableOfMaster('A_MENU_LIST_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalKeyIDOfMaster('MENU_ID');
        $c->setJournalDispIDOfMaster('MENU_NAME');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setHiddenMainTableColumn(false);
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        //----復活時に二重チェックになるので付加
        $c->setDeleteOffBeforeCheck(false);
        //復活時に二重チェックになるので付加----
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $aryTraceQuery = array(
            array(
                'TRACE_TARGET_TABLE'=>'A_MENU_LIST_JNL',
                'TTT_SEARCH_KEY_COLUMN_ID'=>'MENU_ID',
                'TTT_GET_TARGET_COLUMN_ID'=>'MENU_ID',
                'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $objOT->setFirstSearchValueOwnerColumnID('MENU_ID');
        $c->setOutputType('print_journal_table',$objOT);
        //登録更新関係から隠す----
        $c->setMasterDisplayColumnType(0);
        $cg->addColumn($c);

        // 名称
        $c = new TextColumn('MENU_NAME', $g['objMTS']->getSomeMessage("ITABASEH-MNU-211012"));
        $c->setHiddenMainTableColumn(false);
        $c->setAllowSendFromFile(false);
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-211013"));
        //----登録更新関係から隠す
        $c->getOutputType("update_table")->setVisible(false);
        $c->getOutputType("register_table")->setVisible(false);
        $c->getOutputType("excel")->setVisible(false);
        $c->getOutputType("csv")->setVisible(false);
        //登録更新関係から隠す----
        $cg->addColumn($c);
    
    $table->addColumn($cg);
    // カラムグループ メニュー----

    //メニュー
    $c = new IDColumn('MENU_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-211014"),'D_CMDB_TARGET_MENU_LIST','MENU_ID','MENU_PULLDOWN','',array('OrderByThirdColumn'=>'MENU_ID'));
    $c->setDescription('');//エクセル・ヘッダでの説明

    $c->setHiddenMainTableColumn(true); //更新対象カラム

    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->setJournalTableOfMaster('D_CMDB_TARGET_MENU_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('MENU_ID');
    $c->setJournalDispIDOfMaster('MENU_PULLDOWN');
    $c->setUnique(true);//登録/更新時には、DB上ユニークな入力であること必須
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);


    $table->fixColumn();

    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";

		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){
			$exeQueryData[$objColumn->getID()] = "0";
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
    };

    // 登録/更新/廃止/復活があった場合、backyard処理(ky_cmdbmenuanalysis-workflow)の処理済みフラグを0にする
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

            $strQuery = "UPDATE A_PROC_LOADED_LIST "
                       ."SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
                       ."WHERE ROW_ID = :ROW_ID";

            $g['objDBCA']->setQueryTime();
            $aryForBind = array('LOADED_FLG'=>"0", 'LAST_UPDATE_TIMESTAMP'=>$g['objDBCA']->getQueryTime(), 'ROW_ID'=>2100000501);

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
