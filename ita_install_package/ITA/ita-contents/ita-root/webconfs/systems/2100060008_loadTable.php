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
//    ・代入値管理画面のロードテーブル処理。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-302040");
/*
DSC代入値管理
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

    $table = new TableControlAgent('B_DSC_VARS_ASSIGN','ASSIGN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-302050"), 'B_DSC_VARS_ASSIGN_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ASSIGN_ID']->setSequenceID('B_DSC_VARS_ASSIGN_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DSC_VARS_ASSIGN_JSQ');
    unset($tmpAryColumn);


    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);


    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-302060"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITADSCH-MNU-302070"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----



    //************************************************************************************
    //----オペレーション
    //************************************************************************************
    //----オペレーション
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITADSCH-MNU-302080"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','E_OPE_FOR_PULLDOWN_DSC',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-302090"));//エクセル・ヘッダでの説明

    $c->setEvent('update_table', 'onchange', 'operation_upd');
    $c->setEvent('register_table', 'onchange', 'operation_reg');

    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //オペレーション----



    //************************************************************************************
    //----作業パターン
    //************************************************************************************
    //----作業パターン
    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-303010"),'E_DSC_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-303020"));//エクセル・ヘッダでの説明

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
                   ." B_DSC_PHO_LINK          TAB_1 "
                   ." LEFT JOIN E_DSC_PATTERN TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
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

    $objFunction02 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
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
                   ." B_DSC_PHO_LINK          TAB_1 "
                   ." LEFT JOIN E_DSC_PATTERN TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
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
                   ." B_DSC_PHO_LINK          TAB_1 "
                   ." LEFT JOIN E_DSC_PATTERN TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
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

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITADSCH-MNU-303030");
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

    $c->setJournalTableOfMaster('E_DSC_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //作業パターン----



    //************************************************************************************
    //----ホスト
    //************************************************************************************
    //----ホスト
    $c = new IDColumn('SYSTEM_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-303040"),'E_STM_LIST','SYSTEM_ID','HOST_PULLDOWN','',array('OrderByThirdColumn'=>'SYSTEM_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-303050"));//エクセル・ヘッダでの説明

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
                   ." TAB_1.SYSTEM_ID     KEY_COLUMN "
                   .",TAB_2.HOST_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." B_DSC_PHO_LINK TAB_1 "
                   ." LEFT JOIN E_STM_LIST   TAB_2 ON (TAB_1.SYSTEM_ID = TAB_2.SYSTEM_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ."ORDER BY KEY_COLUMN ASC ";

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

    $objFunction02 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strOperationNumeric = $aryVariant['OPERATION_NO_UAPK'];

        $strQuery = "SELECT "
                   ." TAB_1.SYSTEM_ID     KEY_COLUMN "
                   .",TAB_2.HOST_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." B_DSC_PHO_LINK TAB_1 "
                   ." LEFT JOIN E_STM_LIST   TAB_2 ON (TAB_1.SYSTEM_ID = TAB_2.SYSTEM_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ."ORDER BY KEY_COLUMN ASC ";

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
                   ." TAB_1.SYSTEM_ID     KEY_COLUMN "
                   .",TAB_2.HOST_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." B_DSC_PHO_LINK TAB_1 "
                   ." LEFT JOIN E_STM_LIST   TAB_2 ON (TAB_1.SYSTEM_ID = TAB_2.SYSTEM_ID) "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_2.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                   ."ORDER BY KEY_COLUMN ASC ";

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

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITADSCH-MNU-303060");
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

    $c->setJournalTableOfMaster('E_STM_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYSTEM_ID');
    $c->setJournalDispIDOfMaster('HOST_PULLDOWN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //ホスト----



    //************************************************************************************
    //----変数名
    //************************************************************************************
    //----変数名
    $c = new IDColumn('VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-303070"),'D_DSC_PTN_VARS_LINK','VARS_LINK_ID','VARS_LINK_PULLDOWN','D_DSC_PTN_VARS_LINK_VFP');
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-303080"));//エクセル・ヘッダでの説明

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
                   ." D_DSC_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
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

    $objFunction02 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
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
                   ." D_DSC_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
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
                   ." D_DSC_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
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

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITADSCH-MNU-303090");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setJournalTableOfMaster('D_DSC_PTN_VARS_LINK_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
    $c->setJournalDispIDOfMaster('VARS_LINK_PULLDOWN');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //変数名----

    //************************************************************************************
    //----具体値
    //************************************************************************************
    $objVldt = new SingleTextValidator(0,1024,false);
    $c = new TextColumn('VARS_ENTRY',$g['objMTS']->getSomeMessage("ITADSCH-MNU-304010"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-304020"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(false);     //登録/更新時には、任意入力
    $table->addColumn($c);


//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('OPERATION_NO_UAPK','PATTERN_ID','SYSTEM_ID','VARS_LINK_ID','VARS_ENTRY'));
//tail of setting [multi-set-unique]----

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

        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $intOperationNoUAPK = isset($arrayVariant['edit_target_row']['OPERATION_NO_UAPK'])?$arrayVariant['edit_target_row']['OPERATION_NO_UAPK']:null;
            $intPatternId = isset($arrayVariant['edit_target_row']['PATTERN_ID'])?$arrayVariant['edit_target_row']['PATTERN_ID']:null;
            $intSystemId = isset($arrayVariant['edit_target_row']['SYSTEM_ID'])?$arrayVariant['edit_target_row']['SYSTEM_ID']:null;
            $intVarsLinkId = isset($arrayVariant['edit_target_row']['VARS_LINK_ID'])?$arrayVariant['edit_target_row']['VARS_LINK_ID']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                if( strlen($intOperationNoUAPK) === 0 || strlen($intPatternId) === 0 ||  strlen($intSystemId) === 0 || strlen($intVarsLinkId) === 0 ){
                    $boolSystemErrorFlag = true;
                }
            }
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $intOperationNoUAPK = array_key_exists('OPERATION_NO_UAPK',$arrayRegData)?$arrayRegData['OPERATION_NO_UAPK']:null;
            $intPatternId = array_key_exists('PATTERN_ID',$arrayRegData)?$arrayRegData['PATTERN_ID']:null;
            $intSystemId = array_key_exists('SYSTEM_ID',$arrayRegData)?$arrayRegData['SYSTEM_ID']:null;
            $intVarsLinkId = array_key_exists('VARS_LINK_ID',$arrayRegData)?$arrayRegData['VARS_LINK_ID']:null;
        }

        if( strlen($intOperationNoUAPK) === 0 || strlen($intPatternId) === 0 ||  strlen($intSystemId) === 0 || strlen($intVarsLinkId) === 0 ){
            $boolExecuteContinue = false;
        }

        if( $boolExecuteContinue === true ){
            $retBool = false;
            $query = "SELECT "
                    ." COUNT(*) REC_COUNT "
                    ."FROM "
                    ." B_DSC_PHO_LINK TAB_1 "
                    ." LEFT JOIN D_DSC_PTN_VARS_LINK_VFP TAB_2 ON (TAB_1.PATTERN_ID = TAB_2.PATTERN_ID) "
                    ."WHERE "
                    ." TAB_1.DISUSE_FLAG = '0' "
                    ."AND TAB_2.DISUSE_FLAG = '0' "
                    ."AND TAB_1.OPERATION_NO_UAPK = :OPERATION_NO_UAPK "
                    ."AND TAB_1.PATTERN_ID = :PATTERN_ID "
                    ."AND TAB_1.SYSTEM_ID = :SYSTEM_ID "
                    ."AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID ";

            $aryForBind['OPERATION_NO_UAPK'] = $intOperationNoUAPK;
            $aryForBind['PATTERN_ID'] = $intPatternId;
            $aryForBind['SYSTEM_ID'] = $intSystemId;
            $aryForBind['VARS_LINK_ID'] = $intVarsLinkId;

            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
            if( $retArray[0] === true ){
                $objQuery =& $retArray[1];
                $intCount = 0;
                $aryDiscover = array();
                $row = $objQuery->resultFetch();
                unset($objQuery);
                if( $row['REC_COUNT'] == '1' ){
                    $retBool = true;
                }else if( $row['REC_COUNT'] == '0' ){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-MNU-304050");
                }else{
                    $boolSystemErrorFlag = true;
                }
            }else{
                $boolSystemErrorFlag = true;
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
?>
