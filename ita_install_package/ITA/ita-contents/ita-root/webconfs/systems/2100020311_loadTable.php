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
//    ・Ansible（Legacy Role）代入値管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302040");
/*
Ansible（Legacy Role）代入値管理
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

    $table = new TableControlAgent('D_B_ANSIBLE_LRL_VARS_ASSIGN','ASSIGN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302050"), 'D_B_ANSIBLE_LRL_VARS_ASSIGN_JNL', $tmpAry);

    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ASSIGN_ID']->setSequenceID('B_ANSIBLE_LRL_VARS_ASSIGN_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANSIBLE_LRL_VARS_ASSIGN_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_ANSIBLE_LRL_VARS_ASSIGN');
    $table->setDBJournalTableHiddenID('B_ANSIBLE_LRL_VARS_ASSIGN_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----


    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);


    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302060"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302070"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



////////////////////////////////////////////////////////
//----オペレーション
////////////////////////////////////////////////////////
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302080"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','E_OPE_FOR_PULLDOWN_LRL',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302090"));//エクセル・ヘッダでの説明

    $c->setEvent('update_table', 'onchange', 'operation_upd');
    $c->setEvent('register_table', 'onchange', 'operation_reg');

    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    $table->addColumn($c);
    //オペレーション----



////////////////////////////////////////////////////////
//----REST/excel/csv入力用 ホスト名
////////////////////////////////////////////////////////
    // REST/excel/csv入力用 ホスト名
    $c = new IDColumn('REST_SYSTEM_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303040"),'E_ANS_LRL_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303050"));//エクセル・ヘッダでの説明

    //コンテンツのソースがヴューの場合、登録/更新の対象外
    $c->setHiddenMainTableColumn(false);

    //エクセル/CSVからのアップロード対象
    $c->setAllowSendFromFile(true);

    //REST/excel/csv以外は非表示
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    $c->setJournalTableOfMaster('E_ANS_LRL_STM_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');
    //登録/更新時には、必須でない
    $c->setRequired(false);

    $table->addColumn($c);


////////////////////////////////////////////////////////
//----REST/excel/csv入力用 Movement+変数名
////////////////////////////////////////////////////////
    // REST/excel/csv入力用 Movement+変数名
    $c = new IDColumn('REST_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1302095"),'E_ANS_LRL_PTN_VAR_LIST','VARS_LINK_ID','PTN_VAR_PULLDOWN','',array('OrderByThirdColumn'=>'VARS_LINK_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303080"));

    //コンテンツのソースがヴューの場合、登録/更新の対象外
    $c->setHiddenMainTableColumn(false);

    //エクセル/CSVからのアップロード対象
    $c->setAllowSendFromFile(true);

    //REST/excel/csv以外は非表示
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    $c->setJournalTableOfMaster('E_ANS_LRL_PTN_VAR_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
    $c->setJournalDispIDOfMaster('PTN_VAR_PULLDOWN');
    //登録/更新時には、必須でない
    $c->setRequired(false);

    $table->addColumn($c);


////////////////////////////////////////////////////////
//----REST/excel/csv入力用 メンバー変数名
////////////////////////////////////////////////////////
    // REST/excel/csv入力用 メンバー変数名
    $c = new IDColumn('REST_COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303091"),'E_ANS_LRL_VAR_MEMBER_LIST','COL_SEQ_COMBINATION_ID','VAR_MEMBER_PULLDOWN','',array('OrderByThirdColumn'=>'COL_SEQ_COMBINATION_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303092"));

    //コンテンツのソースがヴューの場合、登録/更新の対象外
    $c->setHiddenMainTableColumn(false);

    //エクセル/CSVからのアップロード対象
    $c->setAllowSendFromFile(true);

    //REST/excel/csv以外は非表示
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(true);
    $c->getOutputType('csv')->setVisible(true);
    $c->getOutputType('json')->setVisible(true);

    $c->setJournalTableOfMaster('E_ANS_LRL_VAR_MEMBER_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
    $c->setJournalDispIDOfMaster('VAR_MEMBER_PULLDOWN');
    //登録/更新時には、必須でない
    $c->setRequired(false);

    $table->addColumn($c);



////////////////////////////////////////////////////////
//----作業パターン
////////////////////////////////////////////////////////
    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $aryVariant['OPERATION_NO_UAPK'];
        $strQuery = "SELECT "
                   ." TAB_1.PATTERN_ID KEY_COLUMN "
                   .",TAB_2.PATTERN    DISP_COLUMN "
                   ."FROM "
                   ." B_ANSIBLE_LRL_PHO_LINK          TAB_1 "
                   ." LEFT JOIN E_ANSIBLE_LRL_PATTERN TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ."ORDER BY KEY_COLUMN ";

        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;

        if( 0 < strlen($strOperationNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[]= $row;
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
        return $retArray;
    };

    $objFunction02 = $objFunction01;

    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $rowData['OPERATION_NO_UAPK'];

        $strQuery = "SELECT "
                   ." TAB_1.PATTERN_ID KEY_COLUMN "
                   .",TAB_2.PATTERN    DISP_COLUMN "
                   ."FROM "
                   ." B_ANSIBLE_LRL_PHO_LINK          TAB_1 "
                   ." LEFT JOIN E_ANSIBLE_LRL_PATTERN TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ."ORDER BY KEY_COLUMN ";

        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;

        if( 0 < strlen($strOperationNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
        return $aryRetBody;
    };

    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したPATTERN_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['PATTERN_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['PATTERN_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };
    
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303010"),'E_ANSIBLE_LRL_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303020"));//エクセル・ヘッダでの説明

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303030");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setFADJsEvent('onChange','pattern_upd');
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADJsEvent('onChange','pattern_reg');
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setEvent('update_table','onChange','pattern_upd',array());

    $c->setJournalTableOfMaster('E_ANSIBLE_LRL_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');

    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(false);

    // REST/excel/csvで項目無効
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // データベース更新前のファンクション登録
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->addColumn($c);
    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);
    //作業パターン----



////////////////////////////////////////////////////////
//----ホスト
////////////////////////////////////////////////////////

    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $aryVariant['OPERATION_NO_UAPK'];
        $strPatternIdNumeric = $aryVariant['PATTERN_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.SYSTEM_ID     KEY_COLUMN "
                   .",TAB_2.HOST_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." B_ANSIBLE_LRL_PHO_LINK TAB_1 "
                   ." LEFT JOIN E_STM_LIST   TAB_2 ON (TAB_1.SYSTEM_ID = TAB_2.SYSTEM_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;
        $aryForBind['PATTERN_ID'] = $strPatternIdNumeric;

        if( 0 < strlen($strOperationNumeric) && 0 < strlen($strPatternIdNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[]= $row;
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
        return $retArray;
    };

    $objFunction02 = $objFunction01;

    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $rowData['OPERATION_NO_UAPK'];
        $strPatternIdNumeric = $rowData['PATTERN_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.SYSTEM_ID     KEY_COLUMN "
                   .",TAB_2.HOST_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." B_ANSIBLE_LRL_PHO_LINK TAB_1 "
                   ." LEFT JOIN E_STM_LIST   TAB_2 ON (TAB_1.SYSTEM_ID = TAB_2.SYSTEM_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['OPERATION_NO_UAPK'] = $strOperationNumeric;
        $aryForBind['PATTERN_ID'] = $strPatternIdNumeric;

        if( 0 < strlen($strOperationNumeric) && 0 < strlen($strPatternIdNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
        return $aryRetBody;
    };

    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したSYSTEM_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['SYSTEM_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['SYSTEM_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };
    
    $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303040"),'E_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303050"));//エクセル・ヘッダでの説明

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303060");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setJournalTableOfMaster('E_STM_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');

    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);//登録/更新時には、入力必須

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(false);

    // REST/excel/csvで項目無効
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // データベース更新前のファンクション登録
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);
    $table->addColumn($c);
    //ホスト----



////////////////////////////////////////////////////////
//----変数名
////////////////////////////////////////////////////////

    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();
        $strFxName = "";

        $strPatternIdNumeric = $aryVariant['PATTERN_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.VARS_LINK_ID       KEY_COLUMN "
                   .",TAB_1.VARS_LINK_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['PATTERN_ID']        = $strPatternIdNumeric;

        if( 0 < strlen($strPatternIdNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[]= $row;
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
        return $retArray;
    };

    $objFunction02 = $objFunction01;

    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strPatternIdNumeric = $rowData['PATTERN_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.VARS_LINK_ID       KEY_COLUMN "
                   .",TAB_1.VARS_LINK_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['PATTERN_ID']        = $strPatternIdNumeric;

        if( 0 < strlen($strPatternIdNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }
        $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
        return $aryRetBody;
    };

    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したVARS_LINK_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['VARS_LINK_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['VARS_LINK_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };

    $c = new IDColumn('VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303070"),'D_ANS_LRL_PTN_VARS_LINK','VARS_LINK_ID','VARS_LINK_PULLDOWN','D_ANS_LRL_PTN_VARS_LINK_VFP');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303080"));//エクセル・ヘッダでの説明

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303090");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setFADJsEvent('onChange','vars_upd');
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setJsEvent('onChange','vars_upd');
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADJsEvent('onChange','vars_reg');
    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setJournalTableOfMaster('D_ANS_LRL_PTN_VARS_LINK_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
    $c->setJournalDispIDOfMaster('VARS_LINK_PULLDOWN');

    $c->setRequired(false);//登録/更新時には、入力必須

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(false);

    // REST/excel/csvで項目無効
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // データベース更新前のファンクション登録
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->addColumn($c);
    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);
    //変数名----


////////////////////////////////////////////////////////
//----メンバー変数名
////////////////////////////////////////////////////////

    //----OutputType向け関数
    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();
        $aryAddResultData = array();
        $strFxName = "";
        $strVarsLinkIdNumeric = $aryVariant['VARS_LINK_ID'];
        $strColSeqCombinationId = $aryVariant['COL_SEQ_COMBINATION_ID'];

        //----親変数かどうか、を調べる
        $intVarType = -1;
        if( 0 < strlen($strVarsLinkIdNumeric) ){
            $strQuery = "SELECT "
                       ." TAB_1.VARS_LINK_ID "
                       .",TAB_1.VARS_ATTRIBUTE_01 "
                       ."FROM "
                       ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID ";

            $aryForBind['VARS_LINK_ID'] = $strVarsLinkIdNumeric;

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];

                $tmpAryRow = array();
                while($row = $objQuery->resultFetch() ){
                    $tmpAryRow[]= $row;
                }
                if( count($tmpAryRow) === 1 ){
                    $tmpRow = $tmpAryRow[0];
                    if(1 == $tmpRow['VARS_ATTRIBUTE_01']){
                        $intVarType = 0;
                        $aryAddResultData[] = "NORMAL_VAR_0";
                    }
                    else if(2 == $tmpRow['VARS_ATTRIBUTE_01']){
                        $intVarType = 0;
                        $aryAddResultData[] = "NORMAL_VAR_1";
                    }
                    else if(3 == $tmpRow['VARS_ATTRIBUTE_01']){
                        $intVarType = 1;
                        $aryAddResultData[] = "PARENT_VAR";
                    }
                    else {
                        $intErrorType = 501;
                    }
                }else{
                    $intErrorType = 502;
                }
                unset($objQuery);
            }else{
                $intErrorType = 503;
            }
        }
        //親変数かどうか、を調べる----
        //----親変数だった場合、リストを作成する
        if( $intVarType === 1 ){
            $strQuery = "SELECT "
                       ." TAB_1.COL_SEQ_COMBINATION_ID KEY_COLUMN "
                       .",TAB_1.COMBINATION_MEMBER     DISP_COLUMN "
                       ."FROM "
                       ." D_ANS_LRL_MEMBER_COL_COMB TAB_1 "
                       ." LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_2 ON ( TAB_1.VARS_NAME_ID = TAB_2.VARS_NAME_ID ) "
                       ."WHERE "
                       ."     TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_2.DISUSE_FLAG IN ('0') "
                       ." AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID "
                       ." ORDER BY TAB_1.VARS_NAME_ID, TAB_1.COL_COMBINATION_MEMBER_ALIAS ";


            $aryForBind['VARS_LINK_ID'] = $strVarsLinkIdNumeric;

            if( 0 < strlen($strVarsLinkIdNumeric) ){
                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];
                    while($row = $objQuery->resultFetch() ){
                        $aryDataSet[]= $row;
                    }
                    unset($objQuery);
                    $retBool = true;
                }else{
                    $intErrorType = 504;
                }
            }
            if(3 == $tmpRow['VARS_ATTRIBUTE_01'] && 0 < strlen($strColSeqCombinationId)){

                // function名を一意にする。
                $aryResult = getChildVars_vars_assign($strVarsLinkIdNumeric, $strColSeqCombinationId);

                if("array" === gettype($aryResult) && 1 === count($aryResult)){
                    if( $aryResult[0]['VARS_LINK_ID'] == $strVarsLinkIdNumeric){
                        if(1 == $aryResult[0]['ASSIGN_SEQ_NEED']){
                            $aryAddResultData[0] = "MEMBER_VAR_1";
                        }
                        else {
                            $aryAddResultData[0] = "MEMBER_VAR_0";
                        }
                    }
                }
                else if(false === $aryResult){
                    $intErrorType = 505;
                }
            }
        }

        //親変数だった場合、リストを作成する----
        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet,$aryAddResultData);
        return $retArray;
    };

    $objFunction02 = $objFunction01;
    //OutputType向け関数----

    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strVarsLinkIdNumeric = $rowData['VARS_LINK_ID'];
        $strQuery = "SELECT "
                   ." TAB_1.COL_SEQ_COMBINATION_ID KEY_COLUMN "
                   .",TAB_1.COMBINATION_MEMBER     DISP_COLUMN "
                   ."FROM "
                   ." D_ANS_LRL_MEMBER_COL_COMB TAB_1 "
                   ." LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_2 ON ( TAB_1.VARS_NAME_ID = TAB_2.VARS_NAME_ID ) "
                   ."WHERE "
                   ."     TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID "
                   ." ORDER BY TAB_1.VARS_NAME_ID, TAB_1.COL_COMBINATION_MEMBER_ALIAS ";

        $aryForBind['VARS_LINK_ID'] = $strVarsLinkIdNumeric;


        if( 0 < strlen($strVarsLinkIdNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 501;
            }
        }
        $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
        return $aryRetBody;
    };

    $objFunction04 = function($objCellFormatter, $arraySelectElement,$data,$boolWhiteKeyAdd,$varAddResultData,&$aryVariant,&$arySetting,&$aryOverride){
        global $g;
        $aryRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        //入力不要
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303096");

        $strOptionBodies = "";
        $strNoOptionMessageText = "";

        $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

        $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();
        //条件付き必須なので、出現するときは、空白選択させない
        $boolWhiteKeyAdd = false;

        if( is_array($varAddResultData) === true ){
            if( array_key_exists(0,$varAddResultData) === true ){
                if(in_array($varAddResultData[0], array("PARENT_VAR"))){
                    $strOptionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);
                }else if(in_array($varAddResultData[0], array("NORMAL_VAR_0", "NORMAL_VAR_1"))){
                    $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody01;
                }
            }
        }
        $aryRetBody['optionBodies'] = $strOptionBodies;
        $aryRetBody['NoOptionMessageText'] = $strNoOptionMessageText;
        $retArray = array($aryRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
	};

    $objFunction05 = function($objCellFormatter, $arraySelectElement,$data,$boolWhiteKeyAdd,$rowData,$aryVariant){
        global $g;
        $aryRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        //入力不要
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303097");

        $strOptionBodies = "";
        $strNoOptionMessageText = "";

        $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

        $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();

        //条件付き必須なので、出現するときは、空白選択させない
        $boolWhiteKeyAdd = false;

        $strFxName = "";

        $aryAddResultData = array();

        $strVarsLinkIdNumeric = $rowData['VARS_LINK_ID'];

        //----親変数かどうか、を調べる
        $intVarType = -1;
        if( 0 < strlen($strVarsLinkIdNumeric) ){
            $strQuery = "SELECT "
                       ." TAB_1.VARS_LINK_ID "
                       .",TAB_1.VARS_ATTRIBUTE_01 "
                       ."FROM "
                       ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID ";

            $aryForBind['VARS_LINK_ID'] = $strVarsLinkIdNumeric;

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];

                $tmpAryRow = array();
                while($row = $objQuery->resultFetch() ){
                    $tmpAryRow[]= $row;
                }
                if( count($tmpAryRow) === 1 ){
                    $tmpRow = $tmpAryRow[0];
                    if(3 == $tmpRow['VARS_ATTRIBUTE_01']){
                        $intVarType = 1;
                    }
                    else {
                        $intVarType = 0;
                    }
               }else{
                    $intErrorType = 502;
                }
                unset($tmpRow);
                unset($tmpAryRow);
                unset($objQuery);
            }else{
                $intErrorType = 503;
            }
        }
        //親変数かどうか、を調べる----                

        if( $intVarType == 1 ){
            $strOptionBodies = makeSelectOption($arraySelectElement, $data, $boolWhiteKeyAdd, "", true);
        }else if( $intVarType === 0 ){
            $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody01;
        }
        $aryRetBody['optionBodies'] = $strOptionBodies;
        $aryRetBody['NoOptionMessageText'] = $strNoOptionMessageText;
        $retArray = array($aryRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
	};

    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したCOL_SEQ_COMBINATION_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['COL_SEQ_COMBINATION_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['COL_SEQ_COMBINATION_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };


    $c = new IDColumn('COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303091"),'D_ANS_LRL_MEMBER_COL_COMB','COL_SEQ_COMBINATION_ID','COMBINATION_MEMBER','',array('ORDER'=>'ORDER BY VARS_NAME_ID, COL_COMBINATION_MEMBER_ALIAS'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303092"));//エクセル・ヘッダでの説明

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303093");
    $objVarBFmtUpd = new SelectTabBFmt();

    // 該当変数の具体値を表示する為のトリガー設定
    $objVarBFmtUpd->setFADJsEvent('onChange', 'view_val_upd');

    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);
    $objVarBFmtUpd->setFunctionForGetFADMainDataOverride($objFunction04);
    $objVarBFmtUpd->setFunctionForGetMainDataOverride($objFunction05);
    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setJsEvent('onChange','view_val_upd');
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();

    // 該当変数の具体値を表示する為のトリガー設定
    $objVarBFmtReg->setFADJsEvent('onChange', 'view_val_reg');

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtReg->setFunctionForGetFADMainDataOverride($objFunction04);

    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setJournalTableOfMaster('D_ANS_LRL_MEMBER_COL_COMB_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
    $c->setJournalDispIDOfMaster('COMBINATION_MEMBER');

    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);//登録/更新時には、入力必須

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(false);

    // REST/excel/csvで項目無効
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // データベース更新前のファンクション登録
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
    
    $table->addColumn($c);
    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);
    unset($objFunction04);
    unset($objFunction05);
    //メンバー変数名----



////////////////////////////////////////////////////////
//----具体値
////////////////////////////////////////////////////////
    $objVldt = new SingleTextValidator(0,1024,false);

    $c = new TextColumn('VARS_ENTRY',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304010"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304020"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);

    $c->setRequired(false);     //登録/更新時には、任意入力

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    $table->addColumn($c);



////////////////////////////////////////////////////////
//----代入順序
////////////////////////////////////////////////////////
    $objFunction01 = function($strTagInnerBody,$objCellFormatter,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite){
        global $g;

        //入力不要
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1303099");
        list($strVarsLinkIdNumeric,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array('VARS_LINK_ID'),null);
        $strFxName = "";
        //----親変数かどうか、を調べる
        $intVarType = -1;
        if( 0 < strlen($strVarsLinkIdNumeric) ){
            $strQuery = "SELECT "
                       ." TAB_1.VARS_LINK_ID "
                       .",TAB_1.VARS_ATTRIBUTE_01 "
                       ."FROM "
                       ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID ";

            $aryForBind['VARS_LINK_ID'] = $strVarsLinkIdNumeric;

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];

                $tmpAryRow = array();
                while($row = $objQuery->resultFetch() ){
                    $tmpAryRow[]= $row;
                }
                if( count($tmpAryRow) === 1 ){
                    $tmpRow = $tmpAryRow[0];
                    if(2 == $tmpRow['VARS_ATTRIBUTE_01']){
                        $intVarType = 1;
                    }
                    else if(3 == $tmpRow['VARS_ATTRIBUTE_01']){
                        if(0 < strlen($rowData['ASSIGN_SEQ'])){
                            $intVarType = 1;
                        }
                        else{
                            $intVarType = 0;
                        }
                    }
                    else{
                        $intVarType = 0;
                    }
                }else{
                    $intErrorType = 502;
                }
                unset($tmpRow);
                unset($tmpAryRow);
                unset($objQuery);
            }else{
                $intErrorType = 503;
            }
        }
        //親変数かどうか、を調べる---- 
        if( $intVarType === 1 ){
            //親変数の場合
            //$aryOverWrite["value"] = "";
        }else{
            //親変数ではない場合
            $aryOverWrite["value"] = "";
        }
        $retBody = "<input {$objCellFormatter->printAttrs($aryAddOnDefault,$aryOverWrite)} {$objCellFormatter->printJsAttrs($rowData)} {$objCellFormatter->getTextTagLastAttr()}>";
        $retBody = $retBody."<div style=\"display:none\" id=\"after_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody01."</div><br/>";
        $retBody = $retBody."<div style=\"display:none\" id=\"init_var_type_".$objCellFormatter->getFSTIDForIdentify()."\">".$intVarType."</div>";
        return $retBody;
    };
    $objFunction02 = $objFunction01;

    $objVarBFmtUpd = new NumInputTabBFmt(0,false);
    $objVarBFmtUpd->setFunctionForReturnOverrideGetData($objFunction01);
    $objVarBFmtReg = new NumInputTabBFmt(0,false);
    $objVarBFmtReg->setFunctionForReturnOverrideGetData($objFunction02);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

    $c = new NumColumn('ASSIGN_SEQ',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304051"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304052"));//エクセル・ヘッダでの説明
    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg); 
    $c->setSubtotalFlag(false);

    // 代入順序の入力 1～ に設定
    $c->setValidator(new IntNumValidator(1,null));

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    $table->addColumn($c);
    //代入順序----



////////////////////////////////////////////////////////
//----デフォルト値
////////////////////////////////////////////////////////
    $c = new Column('VAR_VALUE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304041"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304042"));//エクセル・ヘッダでの説明
    $c->setDBColumn(false);
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->setOutputType('update_table',new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt('<div id="dummy_upd" style="width: 200px; word-wrap:break-word; white-space:pre-wrap;" ></div>')));
    $c->setOutputType('register_table',new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt('<div id="dummy_reg" style="width: 200px; word-wrap:break-word; white-space:pre-wrap;" ></div>')));
    $c->getOutputType('delete_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);    
    $table->addColumn($c);
    //デフォルト値----

    // 登録/更新/廃止/復活があった場合、データベースを更新した事をマークする。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        $strFxName = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecDelete" ){
            // 廃止の場合のみ
            $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                $strQuery = "UPDATE A_PROC_LOADED_LIST "
                           ."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
                           ."WHERE ROW_ID in (2100020006) ";

                $aryForBind = array();
    
                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                if( $aryRetBody[0] !== true ){
                    $boolRet = false;
                    $strErrMsg = $aryRetBody[2];
                    $intErrorType = 500;
                }
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ASSIGN_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);


    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
        global $g;
        $retBool = true;
        $retStrBody = '';

        $strModeId = "";
        $modeValue_sub = "";

        $query = "";

        $boolExecuteContinue = true;
        $boolSystemErrorFlag = false;

        $pattan_tbl         = "E_ANSIBLE_LRL_PATTERN";      // モード毎

        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $intOperationNoUAPK     = isset($arrayVariant['edit_target_row']['OPERATION_NO_UAPK'])?
                                            $arrayVariant['edit_target_row']['OPERATION_NO_UAPK']:null;
            $intPatternId           = isset($arrayVariant['edit_target_row']['PATTERN_ID'])?
                                            $arrayVariant['edit_target_row']['PATTERN_ID']:null;
            $intSystemId            = isset($arrayVariant['edit_target_row']['SYSTEM_ID'])?
                                            $arrayVariant['edit_target_row']['SYSTEM_ID']:null;
            $intVarsLinkId          = isset($arrayVariant['edit_target_row']['VARS_LINK_ID'])?
                                            $arrayVariant['edit_target_row']['VARS_LINK_ID']:null;
            $intColSeqCombId        = isset($arrayVariant['edit_target_row']['COL_SEQ_COMBINATION_ID'])?
                                            $arrayVariant['edit_target_row']['COL_SEQ_COMBINATION_ID']:null;
            $intSeqOfAssign         = isset($arrayVariant['edit_target_row']['ASSIGN_SEQ'])?
                                            $arrayVariant['edit_target_row']['ASSIGN_SEQ']:null;
            $intRestVarsLinkId      = isset($arrayVariant['edit_target_row']['REST_VARS_LINK_ID'])?
                                            $arrayVariant['edit_target_row']['REST_VARS_LINK_ID']:null;
            $intRestSystemId        = isset($arrayVariant['edit_target_row']['REST_SYSTEM_ID'])?
                                            $arrayVariant['edit_target_row']['REST_SYSTEM_ID']:null;
            $intRestColSeqCombId    = isset($arrayVariant['edit_target_row']['REST_COL_SEQ_COMBINATION_ID'])?
                                            $arrayVariant['edit_target_row']['REST_COL_SEQ_COMBINATION_ID']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                //----復活の場合
                if( strlen($intOperationNoUAPK) === 0 || strlen($intPatternId) === 0 ||  strlen($intSystemId) === 0 || strlen($intVarsLinkId) === 0 ){
                    $boolSystemErrorFlag = true;
                }
                //復活の場合----
            }
            // 廃止からの復活で画面フリーズ　Pkey退避
            $intAssignId = $strNumberForRI;

            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $intOperationNoUAPK      = array_key_exists('OPERATION_NO_UAPK',$arrayRegData)?
                                          $arrayRegData['OPERATION_NO_UAPK']:null;
            $intPatternId            = array_key_exists('PATTERN_ID',$arrayRegData)?
                                          $arrayRegData['PATTERN_ID']:null;
            $intSystemId             = array_key_exists('SYSTEM_ID',$arrayRegData)?
                                          $arrayRegData['SYSTEM_ID']:null;
            $intVarsLinkId           = array_key_exists('VARS_LINK_ID',$arrayRegData)?
                                          $arrayRegData['VARS_LINK_ID']:null;
            $intColSeqCombId         = array_key_exists('COL_SEQ_COMBINATION_ID',$arrayRegData)?
                                          $arrayRegData['COL_SEQ_COMBINATION_ID']:null;
            $intSeqOfAssign          = array_key_exists('ASSIGN_SEQ',$arrayRegData)?
                                          $arrayRegData['ASSIGN_SEQ']:null;
            $intRestVarsLinkId       = array_key_exists('REST_VARS_LINK_ID',$arrayRegData)?
                                          $arrayRegData['REST_VARS_LINK_ID']:null;
            $intRestSystemId         = array_key_exists('REST_SYSTEM_ID',$arrayRegData)?
                                          $arrayRegData['REST_SYSTEM_ID']:null;
            $intRestColSeqCombId     = array_key_exists('REST_COL_SEQ_COMBINATION_ID',$arrayRegData)?
                                          $arrayRegData['REST_COL_SEQ_COMBINATION_ID']:null;
            // 主キーの値を取得する。
            if( $strModeId == "DTUP_singleRecUpdate" ){
                // 更新処理の場合
                $intAssignId = $strNumberForRI;
            }
            else{
                // 登録処理の場合
                $intAssignId = array_key_exists('ASSIGN_ID',$arrayRegData)?$arrayRegData['ASSIGN_ID']:null;
            }
        }

        $g['PATTERN_ID_UPDATE_VALUE']        = "";
        $g['VARS_LINK_ID_UPDATE_VALUE']      = "";
        $rest_call = false;
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // PATTERN_ID;未設定 VARS_LINK_ID:未設定 REST_VARS_LINK_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($intPatternId)          === 0) &&
               (strlen($intVarsLinkId)         === 0) &&
               (strlen($intRestVarsLinkId)     !== 0)){
                $rest_call = true;
                $query =  "SELECT                                             "
                         ."  TBL_A.VARS_LINK_ID,                              "
                         ."  TBL_A.PATTERN_ID,                                "
                         ."  COUNT(*) AS VARS_LINK_ID_CNT                     "
                         ."FROM                                               "
                         ."  E_ANS_LRL_PTN_VAR_LIST TBL_A                     "      //モード毎
                         ."WHERE                                              "
                         ."  TBL_A.VARS_LINK_ID    = :VARS_LINK_ID   AND      "
                         ."  TBL_A.DISUSE_FLAG     = '0'                      ";
                $aryForBind = array();
                $aryForBind['VARS_LINK_ID'] = $intRestVarsLinkId;
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intCount = 0;
                    $row = $objQuery->resultFetch();
                    if( $row['VARS_LINK_ID_CNT'] == '1' ){
                        $intVarsLinkId                     = $row['VARS_LINK_ID'];
                        $intPatternId                      = $row['PATTERN_ID'];
                        $g['PATTERN_ID_UPDATE_VALUE']      = $intPatternId;
                        $g['VARS_LINK_ID_UPDATE_VALUE']    = $intVarsLinkId;
                    }else if( $row['VARS_LINK_ID_CNT'] == '0' ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90071");
                        $retBool = false;
                        $boolExecuteContinue = false;
                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $boolSystemErrorFlag = true;
                    }
                    unset($row);
                    unset($objQuery);
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
                unset($retArray);
            }
        }
        $g['SYSTEM_ID_UPDATE_VALUE']        = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // SYSTEM_ID;未設定 REST_SYSTEM_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($intSystemId)         === 0) &&
               (strlen($intRestSystemId)     !== 0)){
                $retBool = false;
                $boolExecuteContinue = false;
                $query = "SELECT "
                         ."   COUNT(*) AS HOST_CNT "
                         ."FROM "
                         ."   C_STM_LIST TBL_A  "
                         ." WHERE "
                         ."   TBL_A.SYSTEM_ID    = :SYSTEM_ID AND "
                         ."   TBL_A.DISUSE_FLAG  = '0' ";

                $aryForBind = array();
                $aryForBind['SYSTEM_ID']     = $intRestSystemId;
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intCount = 0;
                    $row = $objQuery->resultFetch();
                    if( $row['HOST_CNT'] == '1' ){
                        $intSystemId                 = $intRestSystemId;
                        $g['SYSTEM_ID_UPDATE_VALUE'] = $intRestSystemId;
                        $retBool = true;
                        $boolExecuteContinue = true;
                    }else if( $row['HOST_CNT'] == '0' ){
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90075");
                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $boolSystemErrorFlag = true;
                    }
                    unset($row);
                    unset($objQuery);
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
                unset($retArray);
            }
        }
        $g['COL_SEQ_COMBINATION_ID_UPDATE_VALUE'] = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // COL_SEQ_COMBINATION_ID;未設定 REST_COL_SEQ_COMBINATION_ID:設定 => RestAPI/Excel/CSV
        // その他はUI  
        // REST_COL_SEQ_COMBINATION_ID未入力のケースがあるのでMovemwnt+変数の入力有無で判定する。
        //呼出元がUIがRestAPI/Excel/CSVかを判定----
        if($rest_call === true){
            $intColSeqCombId                          = $intRestColSeqCombId;
            $g['COL_SEQ_COMBINATION_ID_UPDATE_VALUE'] = $intColSeqCombId;
        }

        //----作業パターンのチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $retBool = false;
            $boolExecuteContinue = false;
            $query =  " SELECT "
                     ."   COUNT(*) AS PATTAN_CNT "
                     ." FROM "
                     ."   $pattan_tbl TBL_A  "
                     ." WHERE "
                     ."   TBL_A.PATTERN_ID   = :PATTERN_ID   AND "
                     ."   TBL_A.DISUSE_FLAG  = '0' ";

            $aryForBind = array();
            $aryForBind['PATTERN_ID']     = $intPatternId;
            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
            if( $retArray[0] === true ){
                $objQuery =& $retArray[1];
                $intCount = 0;
                $row = $objQuery->resultFetch();
                if( $row['PATTAN_CNT'] == '1' ){
                    $retBool = true;
                    $boolExecuteContinue = true;
                }else if( $row['PATTAN_CNT'] == '0' ){
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90063");
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
                unset($row);
                unset($objQuery);
            }else{
                web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                $boolSystemErrorFlag = true;
            }
            unset($retArray);
        }
        //作業パターンのチェック----

        //----必須入力チェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( strlen($intPatternId) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90130");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($intSystemId) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90076");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($intVarsLinkId) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90070");
                $boolExecuteContinue = false;
                $retBool = false;
            }
        }

        //----オペレーションから変数名までの、組み合わせチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $query = "SELECT "
                    ." COUNT(*) REC_COUNT "
                    ."FROM "
                    ." B_ANSIBLE_LRL_PHO_LINK TAB_1 "
                    ." LEFT JOIN D_ANS_LRL_PTN_VARS_LINK_VFP TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
                    ."WHERE "
                    ." TAB_1.DISUSE_FLAG IN ('0') "
                    ."AND TAB_2.DISUSE_FLAG IN ('0') "
                    ."AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                    ."AND TAB_1.PATTERN_ID = :PATTERN_ID "
                    ."AND TAB_1.SYSTEM_ID = :SYSTEM_ID "
                    ."AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID ";

            $aryForBind = array();
            $aryForBind['OPERATION_NO_UAPK'] = $intOperationNoUAPK;
            $aryForBind['PATTERN_ID'] = $intPatternId;
            $aryForBind['SYSTEM_ID'] = $intSystemId;
            $aryForBind['VARS_LINK_ID'] = $intVarsLinkId;

            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
            if( $retArray[0] === true ){
                $objQuery =& $retArray[1];
                $intCount = 0;
                $row = $objQuery->resultFetch();
                if( $row['REC_COUNT'] == '1' ){
                    // OK
                }else if( $row['REC_COUNT'] == '0' ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304050");
                }else{
                    $boolSystemErrorFlag = true;
                }
                unset($row);
                unset($objQuery);
            }else{
                $boolSystemErrorFlag = true;
            }
            unset($retArray);
        }
        //オペレーションから変数名までの、組み合わせチェック----

        $intVarType = -1;
        //----変数タイプを取得
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $strQuery = "SELECT "
                       ." TAB_1.VARS_LINK_ID "
                       .",TAB_1.VARS_ATTRIBUTE_01 "
                       ."FROM "
                       ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID ";

            $aryForBind = array();
            $aryForBind['VARS_LINK_ID'] = $intVarsLinkId;

            $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(VARS_TYPE_CHECK)");
            if( $retArray[0] === true ){
                $objQuery = $retArray[1];
                $tmpAryRow = array();
                while($row = $objQuery->resultFetch() ){
                    $tmpAryRow[]= $row;
                }
                if( count($tmpAryRow) === 1 ){
                    $tmpRow = $tmpAryRow[0];
                    if(in_array($tmpRow['VARS_ATTRIBUTE_01'], array(1, 2, 3))){
                        $intVarType = $tmpRow['VARS_ATTRIBUTE_01'];
                    }else{
                        $boolSystemErrorFlag = true;
                    }
                    unset($tmpRow);
                }else{
                    $boolSystemErrorFlag = true;
                }
                unset($tmpAryRow);
                unset($objQuery);
            }else{
                $boolSystemErrorFlag = true;
            }
            unset($retArray);
        }

        // 変数の種類ごとに、バリデーションチェック
        // 変数タイプが「一般変数」の場合
        if(1 == $intVarType){

            // メンバー変数名のチェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
                if( 0 < strlen($intColSeqCombId) ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304111");
                }
            }

            // 代入順序のチェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
                if( 0 < strlen($intSeqOfAssign) ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304113");
                }
            }
        }

        // 変数タイプが「複数具体値変数」の場合
        else if(2 == $intVarType){

            // メンバー変数名のチェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
                if( 0 < strlen($intColSeqCombId) ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304141");
                }
            }

            // 代入順序のチェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
                if( 0 === strlen($intSeqOfAssign) ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304143");
                }
            }
        }
        // 変数タイプが「多次元変数」の場合
        else if(3 == $intVarType){

            // メンバー変数名のチェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
                if( 0 === strlen($intColSeqCombId) ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304151");
                }
                else {
                    // メンバー変数管理テーブル取得
                    // function名を一意にする。
                    $aryResult = getChildVars_vars_assign($intVarsLinkId, $intColSeqCombId);

                    if("array" === gettype($aryResult) && 1 === count($aryResult)){
                        $childData = $aryResult[0];
                    }
                    else if("array" === gettype($aryResult) && 0 === count($aryResult)){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304054");
                    }
                    else{
                        $boolSystemErrorFlag = true;
                    }
                }
            }

            // 代入順序のチェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
                $intAssignSeqNeed = $childData['ASSIGN_SEQ_NEED'];
                // 代入順序の有無が有の場合
                if(1 ==  $intAssignSeqNeed){
                    if( 0 === strlen($intSeqOfAssign) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304154");
                    }
                }
                // 代入順序の有無が無の場合
                else{
                    if( 0 < strlen($intSeqOfAssign) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $ary[1304155] = "対象のメンバー変数は、代入順序の入力はできません。";
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304155");
                    }
                }
            }
        }

        // 代入値管理テーブルの重複レコードチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
            $strQuery =   "SELECT  "
                        . "  ASSIGN_ID  "
                        . "FROM  "
                        . "  B_ANSIBLE_LRL_VARS_ASSIGN  "
                        . "WHERE  "
                        . "  ASSIGN_ID          <> :ASSIGN_ID          AND  "
                        . "  OPERATION_NO_UAPK  =  :OPERATION_NO_UAPK  AND  "
                        . "  PATTERN_ID         =  :PATTERN_ID         AND  "
                        . "  SYSTEM_ID          =  :SYSTEM_ID          AND  "
                        . "  VARS_LINK_ID       =  :VARS_LINK_ID       AND  "
                        . "  DISUSE_FLAG        =  '0'";

            $aryForBind = array();
            $aryForBind['ASSIGN_ID']          = $intAssignId;
            $aryForBind['OPERATION_NO_UAPK']  = $intOperationNoUAPK;
            $aryForBind['PATTERN_ID']         = $intPatternId;
            $aryForBind['SYSTEM_ID']          = $intSystemId;
            $aryForBind['VARS_LINK_ID']       = $intVarsLinkId;

            // メンバー変数が必須の場合
            if(3 == $intVarType){
                $strQuery .= " AND COL_SEQ_COMBINATION_ID = :COL_SEQ_COMBINATION_ID ";
                $aryForBind['COL_SEQ_COMBINATION_ID'] = $intColSeqCombId;
            }
            // 代入順序が必須の場合
            if(2 == $intVarType ||
               (3 == $intVarType && 1 ==  $intAssignSeqNeed)){
                $strQuery .= " AND ASSIGN_SEQ = :ASSIGN_SEQ ";
                $aryForBind['ASSIGN_SEQ'] = $intSeqOfAssign;
            }

            $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(ARRAYVARS_DUP_CHECK)");
            if( $retArray[0] === true ){
                $objQuery = $retArray[1];
                $dupnostr = "";
                while($row = $objQuery->resultFetch() ){
                    $dupnostr = $dupnostr . "[" . $row['ASSIGN_ID'] . "]";
                }
                if( strlen($dupnostr) != 0 ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304200",array($dupnostr));
                    if(3 == $intVarType){
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304201",array($dupnostr));
                    }
                    if(2 == $intVarType ||
                       (3 == $intVarType && 1 ==  $intAssignSeqNeed)){
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1304203",array($dupnostr));
                    }
                }
                unset($objQuery);
            }
            else{
                $boolSystemErrorFlag = true;
            }
            unset($retArray);
        }
        // 代入値管理テーブルの重複レコードチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
            $ret = CheckDefaultValueSameDefine($g['objDBCA'], $g['objMTS'], $intPatternId, $intVarsLinkId, $intColSeqCombId, $intSeqOfAssign, $strOutputStream);
            if($ret === false){
                $retBool = false;
                $boolExecuteContinue = false;
                $retStrBody = $strOutputStream;
            }
        }

        if( $boolSystemErrorFlag === true ){
            $retBool = false;
            //----システムエラー
            $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
        }

        if($retBool===false){
            $objClientValidator->setValidRule($retStrBody);
        }
        return $retBool;
    };

    $objVarVali = new VariableValidator();
    $objVarVali->setErrShowPrefix(false);
    $objVarVali->setFunctionForIsValid($objFunction);
    $objVarVali->setVariantForIsValid(array());

    $objLU4UColumn->addValidator($objVarVali);
    //組み合わせバリデータ----

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);

/*
 * メンバー変数管理テーブル取得
 */
// function名を一意にする。
function getChildVars_vars_assign($strVarsLinkIdNumeric, $strColSeqCombinationId){

    $strQuery = "SELECT "
               ." TAB_1.VARS_LINK_ID "
               .",TAB_1.COL_SEQ_NEED "
               .",TAB_1.ASSIGN_SEQ_NEED "
               .",TAB_1.VARS_ATTRIBUTE_01 "
               ."FROM "
               ." D_ANS_LRL_CHILD_VARS_VFP TAB_1 "
               ." LEFT JOIN B_ANS_LRL_MEMBER_COL_COMB TAB_2 ON ( TAB_1.ARRAY_MEMBER_ID = TAB_2.ARRAY_MEMBER_ID ) "
               ."WHERE "
               ."     TAB_1.DISUSE_FLAG IN ('0') "
               ." AND TAB_2.DISUSE_FLAG IN ('0') "
               ." AND TAB_1.VARS_LINK_ID           = :VARS_LINK_ID "
               ." AND TAB_2.COL_SEQ_COMBINATION_ID = :COL_SEQ_COMBINATION_ID ";

    $aryForBind = array();
    $aryForBind['VARS_LINK_ID']             = $strVarsLinkIdNumeric;
    $aryForBind['COL_SEQ_COMBINATION_ID']   = $strColSeqCombinationId;

    $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(VARS_RELATION_CHECK)");
    if( $retArray[0] === true ){
        $objQuery = $retArray[1];
        $tmpAryRow = array();
        while($row = $objQuery->resultFetch() ){
            $tmpAryRow[]= $row;
        }
        return $tmpAryRow;
    }
    else{
        return false;
    }
}
//
// 同等の処理が02_access.phpあり。修正注意
//
function CheckDefaultValueSameDefine($objDBCA, $objMTS, $objPtnID, $objVarID, $objChlVarID, $objAssSeqID, &$errmsg){
    $errmsg = "";

    // システム設定のデフォルト値定義のチェック区分を取得
    // 未定義または 1 以外はチェック無の扱いとす。
    $strQuery  =  "SELECT                                    ";
    $strQuery .=  "  VALUE                                   "; 
    $strQuery .=  "FROM                                      ";
    $strQuery .=  "  A_SYSTEM_CONFIG_LIST                    ";
    $strQuery .=  "WHERE                                     ";
    $strQuery .=  " CONFIG_ID   = 'ANSIBLE_DEF_VAL_CHK' AND  ";
    $strQuery .=  " DISUSE_FLAG = '0';                       ";

    $aryForBind = array();
    $defval_chk = "";
    $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(VARS_RELATION_CHECK)");
    if( $retArray[0] === true ){
        $objQuery = $retArray[1];
        while($row = $objQuery->resultFetch() ){
            $defval_chk = $row['VALUE'];
        }
        unset($objQuery);
    }
    else{
        $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(basename(__FILE__),__LINE__,"ExecuteAgent"));
        web_log($errmsg);
        web_log($objQuery->getLastError());
        unset($objQuery);
        return false;
    }

    // 未定義または 1 以外はチェック無の扱いとす。
    if($defval_chk != "1"){
        return true;
    }

    $sql =        "SELECT                                                         \n";
    $sql = $sql . "  TBL_A.ROLE_PACKAGE_ID M_ROLE_PACKAGE_ID,                     \n";
    $sql = $sql . "  TBL_A.ROLE_ID         M_ROLE_ID,                             \n";
    $sql = $sql . "  TBL_B.ROLE_PACKAGE_ID,                                       \n";
    $sql = $sql . "  TBL_B.ROLE_ID,                                               \n";
    $sql = $sql . "  TBL_B.VAR_TYPE,                                              \n";
    $sql = $sql . "  TBL_B.VARS_NAME_ID,                                          \n";
    $sql = $sql . "  TBL_B.COL_SEQ_COMBINATION_ID,                                \n";
    $sql = $sql . "  TBL_B.ASSIGN_SEQ,                                            \n";
    $sql = $sql . "  TBL_B.VARS_VALUE                                             \n";
    $sql = $sql . "FROM                                                           \n";
    $sql = $sql . "  (                                                            \n";
    $sql = $sql . "    SELECT                                                     \n";
    $sql = $sql . "      ROLE_PACKAGE_ID,                                         \n";
    $sql = $sql . "      ROLE_ID                                                  \n";
    $sql = $sql . "    FROM                                                       \n";
    $sql = $sql . "      B_ANSIBLE_LRL_PATTERN_LINK                               \n";
    $sql = $sql . "    WHERE                                                      \n";
    $sql = $sql . "      PATTERN_ID  = :PATTERN_ID AND                            \n";
    $sql = $sql . "      DISUSE_FLAG = '0'                                        \n";
    $sql = $sql . "  ) TBL_A                                                      \n";
    $sql = $sql . "  LEFT OUTER JOIN                                              \n";
    $sql = $sql . "  (                                                            \n";
    $sql = $sql . "    SELECT DISTINCT                                            \n";
    $sql = $sql . "      TBL_3.ROLE_PACKAGE_ID,                                   \n";
    $sql = $sql . "      TBL_3.ROLE_ID,                                           \n";
    $sql = $sql . "      TBL_3.VAR_TYPE,                                          \n";
    $sql = $sql . "      TBL_3.VARS_NAME_ID,                                      \n";
    $sql = $sql . "      TBL_3.COL_SEQ_COMBINATION_ID,                            \n";
    $sql = $sql . "      TBL_3.ASSIGN_SEQ,                                        \n";
    $sql = $sql . "      TBL_3.VARS_VALUE                                         \n";
    $sql = $sql . "    FROM                                                       \n";
    $sql = $sql . "      (                                                        \n";
    $sql = $sql . "        SELECT                                                 \n";
    $sql = $sql . "          TBL_2.ROLE_PACKAGE_ID,                               \n";
    $sql = $sql . "          TBL_2.ROLE_ID,                                       \n";
    $sql = $sql . "          TBL_2.VAR_TYPE,                                      \n";
    $sql = $sql . "          TBL_2.VARS_NAME_ID,                                  \n";
    $sql = $sql . "          TBL_2.COL_SEQ_COMBINATION_ID,                        \n";
    $sql = $sql . "          TBL_2.ASSIGN_SEQ,                                    \n";
    $sql = $sql . "          TBL_2.VARS_VALUE                                     \n";
    $sql = $sql . "        FROM                                                   \n";
    $sql = $sql . "          (                                                    \n";
    $sql = $sql . "            SELECT                                             \n";
    $sql = $sql . "              ROLE_PACKAGE_ID,                                 \n";
    $sql = $sql . "              ROLE_ID                                          \n";
    $sql = $sql . "            FROM                                               \n";
    $sql = $sql . "              B_ANSIBLE_LRL_PATTERN_LINK                       \n";
    $sql = $sql . "            WHERE                                              \n";
    $sql = $sql . "              PATTERN_ID  = :PATTERN_ID AND                    \n";
    $sql = $sql . "              DISUSE_FLAG = '0'                                \n";
    $sql = $sql . "          ) TBL_1                                              \n";
    $sql = $sql . "          LEFT  OUTER JOIN  B_ANS_LRL_ROLE_VARSVAL TBL_2 ON    \n";
    $sql = $sql . "                            (TBL_1.ROLE_PACKAGE_ID =           \n";
    $sql = $sql . "                             TBL_2.ROLE_PACKAGE_ID) AND        \n";
    $sql = $sql . "                            (TBL_1.ROLE_ID         =           \n";
    $sql = $sql . "                             TBL_2.ROLE_ID)                    \n";
    $sql = $sql . "        WHERE                                                  \n";
    $sql = $sql . "          TBL_2.DISUSE_FLAG = '0'                              \n";
    $sql = $sql . "      ) TBL_3                                                  \n";
    $sql = $sql . "    WHERE                                                      \n";
    $sql = $sql . "      TBL_3.VARS_NAME_ID IN                                    \n";
    $sql = $sql . "      (                                                        \n";
    $sql = $sql . "        SELECT                                                 \n";
    $sql = $sql . "          VARS_NAME_ID                                         \n";
    $sql = $sql . "        FROM                                                   \n";
    $sql = $sql . "          B_ANS_LRL_PTN_VARS_LINK                              \n";
    $sql = $sql . "        WHERE                                                  \n";
    $sql = $sql . "          PATTERN_ID    = :PATTERN_ID    AND                   \n";
    $sql = $sql . "          VARS_LINK_ID  = :VARS_LINK_ID  AND                   \n";
    $sql = $sql . "          DISUSE_FLAG   = '0'                                  \n";
    $sql = $sql . "      )                                                        \n";
    $sql = $sql . "      AND                                                      \n";
    if(strlen($objChlVarID) != 0){
        $sql = $sql . " TBL_3.COL_SEQ_COMBINATION_ID = :COL_SEQ_COMBINATION_ID    \n";
    }
    else{
        $sql = $sql . " TBL_3.COL_SEQ_COMBINATION_ID IS NULL                      \n";
    }
    $sql = $sql . "  ) TBL_B ON (TBL_A.ROLE_PACKAGE_ID =                          \n";
    $sql = $sql . "              TBL_B.ROLE_PACKAGE_ID) AND                       \n";
    $sql = $sql . "             (TBL_A.ROLE_ID         =                          \n";
    $sql = $sql . "              TBL_B.ROLE_ID);                                  \n";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false){
        $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(basename(__FILE__),__LINE__,"Prepare"));
        web_log($errmsg);
        web_log($objQuery->getLastError());
        unset($objQuery);
        return false;
    }
    if(strlen($objChlVarID) == 0){
        $objQuery->sqlBind( array('PATTERN_ID'=>$objPtnID,
                                  'VARS_LINK_ID'=>$objVarID));

    }
    else{
        $objQuery->sqlBind( array('PATTERN_ID'=>$objPtnID,
                                  'VARS_LINK_ID'=>$objVarID,
                                  'COL_SEQ_COMBINATION_ID'=>$objChlVarID));
    }
    $r = $objQuery->sqlExecute();
    if (!$r){
        $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(basename(__FILE__),__LINE__,"Execute"));
        web_log($errmsg);
        web_log($objQuery->getLastError());
        unset($objQuery);
        return false;
    }
    // FETCH行数を取得
    $num_of_rows = $objQuery->effectedRowCount();

    $var_type  = "";
    $tgt_row = array();

    $errmsg    = "";
    $undef_cnt = 0;
    $def_cnt   = 0;
    $arr_type_def_list = array();
    $pkg_id    = "";
    while ( $row = $objQuery->resultFetch() ){
        $tgt_row[] =  $row;
        // 各ロールで変数が定義されているか判定
        // 複数具体値変数で具体値が未定義の場合は該当ロールの変数情報が具体値管理に登録されない。
        if(strlen($row['ROLE_ID'])==0){
            $undef_cnt++;
        }
        else{
            $def_cnt++;
        }
        // 同じロールパッケージが紐付てあるか判定
        if($pkg_id == ""){
            $pkg_id = $row['M_ROLE_PACKAGE_ID'];
        }
        else{
            if($pkg_id != $row['M_ROLE_PACKAGE_ID']){
                // DBアクセス事後処理
                unset($objQuery);
                $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90081");
                return false;
            }
        }
    }
    unset($objQuery);

    // 全てのロールで具体値未定義を判定
    if($def_cnt == 0){
        return true;
    }
    // 一部のロールで具体値未定義を判定
    if(($def_cnt != 0) && ($undef_cnt != 0)){
        // 一部のロールで具体値未定義
        $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90080");
        return false;
    }
    for($idx=0;$idx<count($tgt_row);$idx++){
        // 変数の属性を判定
        if($var_type == ""){
            $var_type = $tgt_row[$idx]['VAR_TYPE'];
        }
        else{
            if($var_type != $tgt_row[$idx]['VAR_TYPE']){
                $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(basename(__FILE__),__LINE__,"VAR_TYPE Error"));
                return false;
            }
        }
        // 複数具体値変数の場合、ロール毎の具体値をカウントしておく
        if($var_type == '2'){
            if(@count($arr_type_def_list[$tgt_row[$idx]['ROLE_ID']]) == 0){
                $arr_type_def_list[$tgt_row[$idx]['ROLE_ID']] = 1;
            }
            else{
                $arr_type_def_list[$tgt_row[$idx]['ROLE_ID']]++;
            }
        }
    }
    // 複数具体値変数の場合、ロール毎の具体値の数が一致しているか判定
    $val_cnt = "";
    foreach($arr_type_def_list as $role_id=>$role_val_cnt){
        if($val_cnt == ""){
            $val_cnt = $role_val_cnt;
        }
        else{
            if($val_cnt != $role_val_cnt){
                $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90079");
                return false;
            }
        }
    }
    $wk_varval = array();
    $varval    = "";

    // 一般変数の場合
    if('1' == $var_type){
        if(0 === count($tgt_row)){
            return true;
        }
        else if(1 === count($tgt_row)){
            return true;
        }
        else{
            // 各ロールのデフォルト値が同じか確認する。同じ場合は表示する。
            $varval = $tgt_row[0]['VARS_VALUE'];
            for($idx=0;$idx<count($tgt_row);$idx++){
                if($varval != $tgt_row[$idx]['VARS_VALUE']){
                    $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90079");
                    return false;
                }
            }
        }
    }
    // 複数具体値変数の場合
    else if('2' == $var_type){
        if(0 !== count($tgt_row)){
            foreach($tgt_row as $row){
                // 各ロールのデフォルト値が同じか判定
                if(@count($wk_varval[$row['ASSIGN_SEQ']]) != 0){
                    if($wk_varval[$row['ASSIGN_SEQ']] != $row['VARS_VALUE']){
                        $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90079");
                        return false;
                    }
                }
                else{
                    $wk_varval[$row['ASSIGN_SEQ']] = $row['VARS_VALUE'];
                }
            }
        }
    }
    else{
        $errmsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(basename(__FILE__),__LINE__,"VAR_TYPE Error"));
        return false;
    }
    return true;
}
?>
