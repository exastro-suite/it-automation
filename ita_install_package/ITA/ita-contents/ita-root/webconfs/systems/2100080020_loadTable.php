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
//    ・Terraform最大繰り返し数管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109509");

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
		'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP',
		'TT_SYS_08_DUPLICATE_ID'=>'WEB_BUTTON_DUPLICATE'
	);

    $table = new TableControlAgent('B_TERRAFORM_LRL_MAX_MEMBER_COL', 'MAX_COL_SEQ_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109500"), 'B_TERRAFORM_LRL_MAX_MEMBER_COL_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MAX_COL_SEQ_ID']->setSequenceID('B_TERRAFORM_LRL_MAX_MEMBER_COL_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_LRL_MAX_MEMBER_COL_JSQ');

	// ファイルアップロードで廃止／復活を無効にする。
	$strResultType01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12202");   //登録
	$strResultType02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12203");   //更新
	$strResultType03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12204");   //廃止
	$strResultType04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12205");   //復活
	$strResultType99 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12206");   //エラー

	$tmpAryColumn['ROW_EDIT_BY_FILE']->setResultCount(array( 'register'=>array('name'=>$strResultType01  ,'ct'=>0)
															,'update'  =>array('name'=>$strResultType02  ,'ct'=>0)
															,'error'   =>array('name'=>$strResultType99  ,'ct'=>0)
															)
														);
	$tmpAryColumn['WEB_BUTTON_DUPLICATE']->getOutputType('print_table')->setVisible(false);
	$tmpAryColumn['ROW_EDIT_BY_FILE']->setCommandArrayForEdit(array( 1=>$strResultType01
																	,2=>$strResultType02
																	)
														);
	// 廃止フラグを表示しない
	$outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
	$tmpAryColumn['DISUSE_FLAG']->setOutputType("print_table", $outputType);

	// ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
	// 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
	// VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

	unset($tmpAryColumn);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109501"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109502"));

    $table->setAccessAuth(true);    // データごとのRBAC設定
	$table->setNoRegisterFlg(true);    // 登録画面無し

    // 変数名
    $c = new IDColumn('VARS_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109503"), 'B_TERRAFORM_MODULE_VARS_LINK', 'MODULE_VARS_LINK_ID', 'VARS_NAME');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109504")); //エクセル・ヘッダでの説明

    //エクセル/CSVからのアップロードは不可能
    $c->setAllowSendFromFile(false);
    //更新対象カラム
    $c->setHiddenMainTableColumn(true);

    $c->getOutputType('filter_table')->setVisible(true);
    $c->getOutputType('print_table')->setVisible(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(true);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    // 入力禁止設定
    $c->setOutputType('update_table'  , new IDOutputType(new ReqTabHFmt(), new TextTabBFmt()));

	$table->addColumn($c);

    // メンバー変数名
    $c = new IDColumn('MEMBER_VARS_ID', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109505"), 'D_TERRAFORM_VAR_MEMBER', 'CHILD_MEMBER_VARS_ID', 'CHILD_MEMBER_VARS_NEST');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109506")); //エクセル・ヘッダでの説明
	$c->setJournalTableOfMaster('D_TERRAFORM_VAR_MEMBER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('CHILD_MEMBER_VARS_ID');
    $c->setJournalDispIDOfMaster('CHILD_MEMBER_VARS_NEST');

    //エクセル/CSVからのアップロードは不可能
    $c->setAllowSendFromFile(false);
    //更新対象カラム
    $c->setHiddenMainTableColumn(true);

    $c->getOutputType('filter_table')->setVisible(true);
    $c->getOutputType('print_table')->setVisible(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(true);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    // 入力禁止設定
    $c->setOutputType('update_table'  , new IDOutputType(new ReqTabHFmt(), new TextTabBFmt()));


	$table->addColumn($c);

    // 最大繰り返し数
    $c = new NumColumn('MAX_COL_SEQ', $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109507"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109508")); //エクセル・ヘッダでの説明
    //更新対象カラム
    $c->setHiddenMainTableColumn(true);
    //エクセル/CSVからのアップロードは可能
    $c->setAllowSendFromFile(true);

    $c->setSubtotalFlag(false);
    $c->setValidator(new IntNumValidator(1, 99999999));
    $c->setRequired(true);//登録/更新時には、入力必須
	$table->addColumn($c);

	// 登録/更新/廃止/復活があった場合、データベースを更新した事をマークする。
	$tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
		$boolRet = true;
		$intErrorType = null;
		$aryErrMsgBody = array();
		$strErrMsg = "";
		$strErrorBuf = "";
		$strFxName = "";

		$modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
		if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" || $modeValue=="DTUP_singleRecDelete" ){

			$strQuery = "UPDATE A_PROC_LOADED_LIST "
						."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
						."WHERE ROW_ID IN (2100080001) ";

			$aryForBind = array();

			$aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

			if( $aryRetBody[0] !== true ){
				$boolRet = false;
				$strErrMsg = $aryRetBody[2];
				$intErrorType = 500;
			}
		}
		$retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
		return $retArray;
	};
	$tmpAryColumn = $table->getColumns();
	$tmpAryColumn['MAX_COL_SEQ_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

	//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('VARS_ID','MEMBER_VARS_ID'));
	//tail of setting [multi-set-unique]----

    $table->fixColumn();


    $table->setGeneObject('webSetting', $arrayWebSetting);

	$tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setResultCount(
        array(
         'update'  =>array('name'=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12203"), 'ct'=>0),
         'error'   =>array('name'=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12206"), 'ct'=>0)
        )
    );
    $tmpAryColumn['ROW_EDIT_BY_FILE']->setCommandArrayForEdit(
        array(
            2=>$g['objMTS']->getSomeMessage("ITAWDCH-STD-12203")
        )
    );
    //廃止・復活ボタンを隠す
    $outputType = new OutputType(new TabHFmt(), new DelTabBFmt());
    $tmpAryColumn['DISUSE_FLAG']->setOutputType("print_table", $outputType);

    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
