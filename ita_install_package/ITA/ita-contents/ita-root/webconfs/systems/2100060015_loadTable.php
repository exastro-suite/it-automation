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
//    ・DSC代入値自動登録設定
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITADSCH-MNU-902000");
/*
DSC代入値自動登録設定
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

    $table = new TableControlAgent('D_DSC_VAL_ASSIGN','COLUMN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902001"), 'D_DSC_VAL_ASSIGN_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['COLUMN_ID']->setSequenceID('B_DSC_VAL_ASSIGN_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_DSC_VAL_ASSIGN_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_DSC_VAL_ASSIGN');
    $table->setDBJournalTableHiddenID('B_DSC_VAL_ASSIGN_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITADSCH-MNU-902002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902003"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    ////////////////////////////////////////////////////////////
    // ColumnGroup:パラメータシート 開始
    ////////////////////////////////////////////////////////////
    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITADSCH-MNU-902301"));

        // カラムグループ メニューグループ(一覧のみ表示)
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITADSCH-MNU-902004"));

            $c = new IDColumn('MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITADSCH-MNU-902005"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID'));
            $c->addClass("number");
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902006"));
            $c->getOutputType("update_table")->setVisible(false);
            $c->getOutputType("register_table")->setVisible(false);
            $c->getOutputType("excel")->setVisible(false);
            $c->getOutputType("csv")->setVisible(false);
            $c->setDeleteOffBeforeCheck(false);

            $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

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

            $c = new TextColumn('MENU_GROUP_NAME', $g['objMTS']->getSomeMessage("ITADSCH-MNU-902007"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902008"));
            $c->getOutputType("update_table")->setVisible(false);
            $c->getOutputType("register_table")->setVisible(false);
            $c->getOutputType("excel")->setVisible(false);
            $c->getOutputType("csv")->setVisible(false);

            $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

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

        $cgg->addColumn($cg);
        // カラムグループ（メニューグループ）----

        // カラムグループ メニュー(一覧のみ表示)
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITADSCH-MNU-902009"));

            // メニューID
            $c = new IDColumn('MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITADSCH-MNU-902010"), "D_MENU_LIST", 'MENU_ID', "MENU_ID", '', array('OrderByThirdColumn'=>'MENU_ID'));
            $c->addClass("number");
            $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902011"));
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

            $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

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
            $c = new TextColumn('MENU_NAME', $g['objMTS']->getSomeMessage("ITADSCH-MNU-902012"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902013"));
            //----登録更新関係から隠す
            $c->setHiddenMainTableColumn(false);
            $c->getOutputType("update_table")->setVisible(false);
            $c->getOutputType("register_table")->setVisible(false);
            $c->getOutputType("excel")->setVisible(false);
            $c->getOutputType("csv")->setVisible(false);

            $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

            //登録更新関係から隠す----
            $cg->addColumn($c);

        $cgg->addColumn($cg);
        // カラムグループ メニュー----

        // メニュー
        // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したMENU_IDを設定する。
        $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                    global    $g;
                    $boolRet = true;
                    $intErrorType = null;
                    $aryErrMsgBody = array();
                    $strErrMsg = "";
                    $strErrorBuf = "";

                    // シナリオタイプをSCRABに設定する。
                    $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                    if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                        if(strlen($g['MENU_ID_UPDATE_VALUE']) !== 0){
                            $exeQueryData[$objColumn->getID()] = $g['MENU_ID_UPDATE_VALUE'];
                        }
                    }else if( $modeValue=="DTUP_singleRecDelete" ){
                    }
                    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                    return $retArray;
        };

        $c = new IDColumn('MENU_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902100"),'D_CMDB_MENU_LIST','MENU_ID','MENU_PULLDOWN','',array('OrderByThirdColumn'=>'MENU_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902101"));

        $c->setHiddenMainTableColumn(true); //更新対象カラム

        $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。

        $c->getOutputType('filter_table')->setVisible(false);
        $c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('print_journal_table')->setVisible(false);

        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

        $c->setEvent('update_table', 'onchange', 'menu_upd');
        $c->setEvent('register_table', 'onchange', 'menu_reg');

        $c->setJournalTableOfMaster('D_CMDB_MENU_LIST_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('MENU_ID');
        $c->setJournalDispIDOfMaster('MENU_PULLDOWN');

        $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

        $cgg->addColumn($c);

        unset($tmpObjFunction);

        //カラムタイトル名
        // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したCOLUMN_LIST_IDを設定する。
        $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                    global    $g;

                    $boolRet = true;
                    $intErrorType = null;
                    $aryErrMsgBody = array();
                    $strErrMsg = "";
                    $strErrorBuf = "";

                    // シナリオタイプをSCRABに設定する。
                    $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                    if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                        if(strlen($g['COLUMN_LIST_ID_UPDATE_VALUE']) !== 0){
                            $exeQueryData[$objColumn->getID()] = $g['COLUMN_LIST_ID_UPDATE_VALUE'];
                        }
                    }else if( $modeValue=="DTUP_singleRecDelete" ){
                    }
                    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                    return $retArray;
        };

        $c = new IDColumn('COLUMN_LIST_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902120"),'B_CMDB_MENU_COLUMN','COLUMN_LIST_ID','COL_TITLE','',array('SELECT_ADD_FOR_ORDER'=>array('COL_TITLE_DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1') );
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902121"));

        $c->setHiddenMainTableColumn(true); //更新対象カラム

        $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。

        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        $c->getOutputType('json')->setVisible(false); // Append RestAPIでは隠す

        $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
            global $g;
            $retBool = false;
            $intErrorType = null;
            $aryErrMsgBody = array();
            $strErrMsg = "";
            $aryDataSet = array();

            $strFxName = "";

            $strMenuIDNumeric = $aryVariant['MENU_ID'];

            $strQuery = "SELECT "
                       ." TAB_1.COLUMN_LIST_ID  KEY_COLUMN "
                       .",TAB_1.COL_TITLE       DISP_COLUMN "
                       ."FROM "
                       ." B_CMDB_MENU_COLUMN TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.MENU_ID = :MENU_ID "
                       ."ORDER BY COL_TITLE_DISP_SEQ";

            $aryForBind['MENU_ID'] = $strMenuIDNumeric;

            if( 0 < strlen($strMenuIDNumeric) ){
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

            $strMenuIDNumeric = $rowData['MENU_ID'];

            $strQuery = "SELECT "
                       ." TAB_1.COLUMN_LIST_ID  KEY_COLUMN "
                       .",TAB_1.COL_TITLE       DISP_COLUMN "
                       ."FROM "
                       ." B_CMDB_MENU_COLUMN TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG IN ('0') "
                       ." AND TAB_1.MENU_ID = :MENU_ID "
                       ."ORDER BY COL_TITLE_DISP_SEQ";

            $aryForBind['MENU_ID'] = $strMenuIDNumeric;

            if( 0 < strlen($strMenuIDNumeric) ){
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

        $strSetInnerText = $g['objMTS']->getSomeMessage("ITADSCH-MNU-902262");
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

        $c->setJournalTableOfMaster('B_CMDB_MENU_COLUMN_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('COLUMN_LIST_ID');
        $c->setJournalDispIDOfMaster('COL_TITLE');

        $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

        $cgg->addColumn($c);

        unset($tmpObjFunction);

        unset($objFunction01);
        unset($objFunction02);
        unset($objFunction03);

        // Excel/CSV/RestAPI 用カラムタイトル名
        $c = new IDColumn('MENU_COLUMN_LIST_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-900125"),'D_CMDB_MG_MU_COL_LIST','COLUMN_LIST_ID','MENU_COL_TITLE_PULLDOWN','',array('SELECT_ADD_FOR_ORDER'=>array('MENU_ID','COL_TITLE_DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1,ADD_SELECT_2') );
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-900126"));

        $c->setHiddenMainTableColumn(false); //更新対象カラム

        $c->getOutputType('filter_table')->setVisible(false);
        $c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('print_journal_table')->setVisible(false);

        $c->setJournalTableOfMaster('D_CMDB_MG_MU_COL_LIST_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('COLUMN_LIST_ID');
        $c->setJournalDispIDOfMaster('MENU_COL_TITLE_PULLDOWN');

        $cgg->addColumn($c);

    ////////////////////////////////////////////////////////////
    // ColumnGroup:パラメータシート 終了
    ////////////////////////////////////////////////////////////
    $table->addColumn($cgg);

    //登録方式
    $c = new IDColumn('COL_TYPE',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902130"),'B_CMDB_MENU_COL_TYPE','COLUMN_TYPE_ID','COLUMN_TYPE_NAME','',array('OrderByThirdColumn'=>'COLUMN_TYPE_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902131"));

    $c->setHiddenMainTableColumn(true); //更新対象カラム

    $c->setJournalTableOfMaster('B_CMDB_MENU_COL_TYPE_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COLUMN_TYPE_ID');
    $c->setJournalDispIDOfMaster('COLUMN_TYPE_NAME');

    $c->setRequired(true);//登録/更新時には、入力必須

    $table->addColumn($c);

    ////////////////////////////////////////////////////////////
    // ColumnGroup:IaC変数 開始
    ////////////////////////////////////////////////////////////
    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITADSCH-MNU-902302"));

        //----作業パターン
        $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902140"),'E_DSC_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902141"));

        $c->setHiddenMainTableColumn(true); //更新対象カラム

        $c->setJournalTableOfMaster('E_DSC_PATTERN_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('PATTERN_ID');
        $c->setJournalDispIDOfMaster('PATTERN');

        $c->setRequired(true);//登録/更新時には、入力必須

        $c->setEvent('update_table', 'onchange', 'pattern_upd');
        $c->setEvent('register_table', 'onchange', 'pattern_reg');

        $cgg->addColumn($c);

        //////////////////////////////////////////////////
        // Key変数 開始                                 //
        //////////////////////////////////////////////////
        // 変数
        $c = new IDColumn('KEY_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902220"),'D_DSC_PTN_VARS_LINK','VARS_LINK_ID','VARS_LINK_PULLDOWN','D_DSC_PTN_VARS_LINK_VFP');

        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902221"));//エクセル・ヘッダでの説明

        $c->setHiddenMainTableColumn(true); //更新対象カラム

        $c->setJournalTableOfMaster('D_DSC_PTN_VARS_LINK_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
        $c->setJournalDispIDOfMaster('VARS_LINK_PULLDOWN');

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数
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

        // フォームの表示直後、選択できる選択肢リストを作成する関数
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

        $strSetInnerText = $g['objMTS']->getSomeMessage("ITADSCH-MNU-902222");
        $objVarBFmtUpd = new SelectTabBFmt();

        // フォームの表示直後、変更反映カラムの既存値が、選べる選択肢の中になかった場合のメッセージ
        $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
        $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);

        // フォームの表示直後、選択できる選択肢リストを作成する関数指定
        $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

        $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
        $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

        $objVarBFmtReg = new SelectTabBFmt();

        // フォームの表示直後、トリガーカラムが選ばれていない場合のメッセージ
        $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
        $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

        $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
        $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

        $c->setOutputType('update_table',$objOTForUpd);

        $c->setOutputType('register_table',$objOTForReg);

        $cgg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);
            unset($objFunction03);

        //////////////////////////////////////////////////
        // Key変数 終了                                 //
        //////////////////////////////////////////////////

        //////////////////////////////////////////////////
        // Value変数 開始                               //
        //////////////////////////////////////////////////
        // 変数
        $c = new IDColumn('VAL_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITADSCH-MNU-902160"),'D_DSC_PTN_VARS_LINK','VARS_LINK_ID','VARS_LINK_PULLDOWN','D_DSC_PTN_VARS_LINK_VFP');
        $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-902161"));

        $c->setHiddenMainTableColumn(true); //更新対象カラム

        $c->setJournalTableOfMaster('D_DSC_PTN_VARS_LINK_JNL');
        $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
        $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
        $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
        $c->setJournalDispIDOfMaster('VARS_LINK_PULLDOWN');

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数
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

        // フォームの表示直後、選択できる選択肢リストを作成する関数
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

        $strSetInnerText = $g['objMTS']->getSomeMessage("ITADSCH-MNU-902162");
        $objVarBFmtUpd = new SelectTabBFmt();

        // フォームの表示直後、変更反映カラムの既存値が、選べる選択肢の中になかった場合のメッセージ
        $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
        $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);

        // フォームの表示直後、選択できる選択肢リストを作成する関数指定
        $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

        $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
        $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

        $objVarBFmtReg = new SelectTabBFmt();

        // フォームの表示直後、トリガーカラムが選ばれていない場合のメッセージ
        $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
        $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

        $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

        // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
        $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

        $c->setOutputType('update_table',$objOTForUpd);
        $c->setOutputType('register_table',$objOTForReg);

        $cgg->addColumn($c);

        unset($objFunction01);
        unset($objFunction02);
        unset($objFunction03);

        //////////////////////////////////////////////////
        // Value変数 終了                               //
        //////////////////////////////////////////////////

    //////////////////////////////////////////////////
    // ColumnGroup:IaC変数 終了                     //
    //////////////////////////////////////////////////
    $table->addColumn($cgg);

    ////////////////////////////////////////////////////////////////////
    // パラメータシートの具体値がNULLでも代入値管理に登録するかのフラグ
    ////////////////////////////////////////////////////////////////////
    $c = new IDColumn('NULL_DATA_HANDLING_FLG',$g['objMTS']->getSomeMessage("ITADSCH-MNU-204100"),'B_VALID_INVALID_MASTER','FLAG_ID','FLAG_NAME','', array('OrderByThirdColumn'=>'FLAG_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITADSCH-MNU-204110"));
    $c->setHiddenMainTableColumn(true); //更新対象カラム

    $c->setRequired(false);

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(true);

    $table->addColumn($c);


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

        // --UPD--
        $pattan_tbl         = "E_DSC_PATTERN";
        $ptn_vars_link_view = "D_DSC_PTN_VARS_LINK_VFP";
        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $rg_menu_id                = isset($arrayVariant['edit_target_row']['MENU_ID'])?
                                               $arrayVariant['edit_target_row']['MENU_ID']:null;
            $rg_column_list_id         = isset($arrayVariant['edit_target_row']['COLUMN_LIST_ID'])?
                                               $arrayVariant['edit_target_row']['COLUMN_LIST_ID']:null;
            $rg_col_type               = isset($arrayVariant['edit_target_row']['COL_TYPE'])?
                                               $arrayVariant['edit_target_row']['COL_TYPE']:null;
            $rg_pattern_id             = isset($arrayVariant['edit_target_row']['PATTERN_ID'])?
                                               $arrayVariant['edit_target_row']['PATTERN_ID']:null;
            $rg_key_vars_link_id       = isset($arrayVariant['edit_target_row']['KEY_VARS_LINK_ID'])?
                                               $arrayVariant['edit_target_row']['KEY_VARS_LINK_ID']:null;
            $rg_key_child_vars_link_id = isset($arrayVariant['edit_target_row']['KEY_CHILD_VARS_LINK_ID'])?
                                               $arrayVariant['edit_target_row']['KEY_CHILD_VARS_LINK_ID']:null;
            $rg_key_child_vars_col_seq = isset($arrayVariant['edit_target_row']['KEY_CHILD_VARS_COL_SEQ'])?
                                               $arrayVariant['edit_target_row']['KEY_CHILD_VARS_COL_SEQ']:null;
            $rg_key_assign_seq         = isset($arrayVariant['edit_target_row']['KEY_ASSIGN_SEQ'])?
                                               $arrayVariant['edit_target_row']['KEY_ASSIGN_SEQ']:null;
            $rg_val_vars_link_id       = isset($arrayVariant['edit_target_row']['VAL_VARS_LINK_ID'])?
                                               $arrayVariant['edit_target_row']['VAL_VARS_LINK_ID']:null;
            $rg_val_child_vars_link_id = isset($arrayVariant['edit_target_row']['VAL_CHILD_VARS_LINK_ID'])?
                                               $arrayVariant['edit_target_row']['VAL_CHILD_VARS_LINK_ID']:null;
            $rg_val_child_vars_col_seq = isset($arrayVariant['edit_target_row']['VAL_CHILD_VARS_COL_SEQ'])?
                                               $arrayVariant['edit_target_row']['VAL_CHILD_VARS_COL_SEQ']:null;
            $rg_val_assign_seq         = isset($arrayVariant['edit_target_row']['VAL_ASSIGN_SEQ'])?
                                               $arrayVariant['edit_target_row']['VAL_ASSIGN_SEQ']:null;
            $rg_menu_column_list_id    = isset($arrayVariant['edit_target_row']['MENU_COLUMN_LIST_ID'])?
                                               $arrayVariant['edit_target_row']['MENU_COLUMN_LIST_ID']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                //----復活の場合
                if( strlen($rg_menu_column_list_id) === 0 ||  strlen($rg_col_type) === 0 || strlen($rg_pattern_id) === 0 ){
                    $boolSystemErrorFlag = true;
                }
                //復活の場合----

                $columnId = $strNumberForRI; 

            }
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $rg_menu_id                = array_key_exists('MENU_ID',$arrayRegData) ?
                                            $arrayRegData['MENU_ID']:null;
            $rg_column_list_id         = array_key_exists('COLUMN_LIST_ID',$arrayRegData) ?
                                            $arrayRegData['COLUMN_LIST_ID']:null;
            $rg_col_type               = array_key_exists('COL_TYPE',$arrayRegData) ?
                                            $arrayRegData['COL_TYPE']:null;
            $rg_pattern_id             = array_key_exists('PATTERN_ID',$arrayRegData) ?
                                            $arrayRegData['PATTERN_ID']:null;
            $rg_key_vars_link_id       = array_key_exists('KEY_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['KEY_VARS_LINK_ID']:null;
            $rg_key_child_vars_link_id = array_key_exists('KEY_CHILD_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['KEY_CHILD_VARS_LINK_ID']:null;
            $rg_key_child_vars_col_seq = array_key_exists('KEY_CHILD_VARS_COL_SEQ',$arrayRegData) ?
                                            $arrayRegData['KEY_CHILD_VARS_COL_SEQ']:null;
            $rg_key_assign_seq         = array_key_exists('KEY_ASSIGN_SEQ',$arrayRegData) ?
                                            $arrayRegData['KEY_ASSIGN_SEQ']:null;
            $rg_val_vars_link_id       = array_key_exists('VAL_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['VAL_VARS_LINK_ID']:null;
            $rg_val_child_vars_link_id = array_key_exists('VAL_CHILD_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['VAL_CHILD_VARS_LINK_ID']:null;
            $rg_val_child_vars_col_seq = array_key_exists('VAL_CHILD_VARS_COL_SEQ',$arrayRegData) ?
                                            $arrayRegData['VAL_CHILD_VARS_COL_SEQ']:null;
            $rg_val_assign_seq         = array_key_exists('VAL_ASSIGN_SEQ',$arrayRegData) ?
                                            $arrayRegData['VAL_ASSIGN_SEQ']:null;
            $rg_menu_column_list_id    = array_key_exists('MENU_COLUMN_LIST_ID',$arrayRegData) ?
                                            $arrayRegData['MENU_COLUMN_LIST_ID']:null;

            // 主キーの値を取得する。
            if( $strModeId == "DTUP_singleRecUpdate" ){
                // 更新処理の場合
                $columnId = $strNumberForRI;
            }
            else{
                // 登録処理の場合
                $columnId = array_key_exists('COLUMN_ID',$arrayRegData)?$arrayRegData['COLUMN_ID']:null;
            }

        }

        $g['MENU_ID_UPDATE_VALUE']        = "";
        $g['COLUMN_LIST_ID_UPDATE_VALUE'] = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // MENU_ID;未設定 COLUMN_LIST_ID:未設定 MENU_COLUMN_LIST_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($rg_menu_id)             === 0) && 
               (strlen($rg_column_list_id)      === 0) &&
               (strlen($rg_menu_column_list_id) !== 0)){
                $query =  "SELECT                                             "
                         ."  TBL_A.COLUMN_LIST_ID,                            "
                         ."  TBL_A.MENU_ID,                                   "
                         ."  COUNT(*) AS COLUMN_LIST_ID_CNT,                  "
                         ."  (                                                "
                         ."    SELECT                                         "
                         ."      COUNT(*)                                     "
                         ."    FROM                                           "
                         ."      B_CMDB_MENU_LIST TBL_B                       "
                         ."    WHERE                                          "
                         ."      TBL_B.MENU_ID      = TBL_A.MENU_ID AND       "
                         ."      TBL_B.DISUSE_FLAG  = '0'                     "
                         ."  ) AS MENU_CNT                                    "
                         ."FROM                                               "
                         ."  B_CMDB_MENU_COLUMN TBL_A                         "
                         ."WHERE                                              "
                         ."  TBL_A.COLUMN_LIST_ID  = :COLUMN_LIST_ID   AND    "
                         ."  TBL_A.DISUSE_FLAG     = '0'                      ";
                $aryForBind = array();
                $aryForBind['COLUMN_LIST_ID'] = $rg_menu_column_list_id;
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intCount = 0;
                    $row = $objQuery->resultFetch();
                    if( $row['MENU_CNT'] == '1' ){
                        if( $row['COLUMN_LIST_ID_CNT'] == '1' ){
                            $rg_menu_id               = $row['MENU_ID'];
                            $rg_column_list_id        = $row['COLUMN_LIST_ID'];
                            $g['MENU_ID_UPDATE_VALUE']        = $rg_menu_id;
                            $g['COLUMN_LIST_ID_UPDATE_VALUE'] = $rg_column_list_id;
                            if($boolExecuteContinue === true){
                                $boolExecuteContinue = true;
                            }
                        }else if( $row['COLUMN_LIST_ID_CNT'] == '0' ){
                            $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90060");
                            $retBool = false;
                            $boolExecuteContinue = false;
                        }else{
                            $boolSystemErrorFlag = true;
                        }
                    }else if( $row['MENU_CNT'] == '0' ){
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90059");
                        $retBool = false;
                        $boolExecuteContinue = false;
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
        }
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( strlen($rg_menu_id) === 0 || strlen($rg_column_list_id) === 0 ) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90129");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($rg_col_type) === 0) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90061");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($rg_pattern_id) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90130");
                $boolExecuteContinue = false;
                $retBool = false;
            }    
        }

        //----メニューと項目の組み合わせチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $retBool = false;
            $boolExecuteContinue = false;
            $query = " SELECT "
                     ."   COUNT(*) AS MENU_CNT, "
                     ."   ( "
                     ."     SELECT  "
                     ."       COUNT(*) "
                     ."     FROM "
                     ."       B_CMDB_MENU_COLUMN TBL_B "
                     ."     WHERE "
                     ."       TBL_B.MENU_ID        = :MENU_ID          AND "
                     ."       TBL_B.COLUMN_LIST_ID = :COLUMN_LIST_ID   AND "
                     ."       TBL_B.DISUSE_FLAG  = '0' "
                     ."   ) AS COLUMN_CNT "
                     ." FROM "
                     ."   B_CMDB_MENU_LIST TBL_A  "
                     ." WHERE "
                     ."   TBL_A.MENU_ID      = :MENU_ID   AND "
                     ."   TBL_A.DISUSE_FLAG  = '0' ";

            $aryForBind = array();
            $aryForBind['MENU_ID']        = $rg_menu_id;
            $aryForBind['COLUMN_LIST_ID'] = $rg_column_list_id;

            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
            if( $retArray[0] === true ){
                $objQuery =& $retArray[1];
                $intCount = 0;
                $row = $objQuery->resultFetch();
                if( $row['MENU_CNT'] == '1' ){
                    if( $row['COLUMN_CNT'] == '1' ){
                        $retBool = true;
                        $boolExecuteContinue = true;
                    }else if( $row['COLUMN_CNT'] == '0' ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90060");
                    }else{
                        $boolSystemErrorFlag = true;
                    }
                    $retBool = true;
                }else if( $row['MENU_CNT'] == '0' ){
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90059");
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
        //メニューと項目の組み合わせチェック----
        //登録方式のチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $retBool = false;
            $boolExecuteContinue = false;
            if(strlen($rg_col_type) == 0){
                $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90061");
            }
            else{
                switch($rg_col_type){
                case '1':   // Value
                case '2':   // Key
                case '3':   // Key-Value
                    $retBool = true;
                    $boolExecuteContinue = true;
                    break;
                default:
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90062");
                    break;
                }
            }
        }

        //----作業パターンのチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $retBool = false;
            $boolExecuteContinue = false;
            $query = " SELECT "
                     ."   COUNT(*) AS PATTAN_CNT "
                     ." FROM "
                     ."   $pattan_tbl TBL_A  "
                     ." WHERE "
                     ."   TBL_A.PATTERN_ID   = :PATTERN_ID   AND "
                     ."   TBL_A.DISUSE_FLAG  = '0' ";

            $aryForBind = array();
            $aryForBind['PATTERN_ID']     = $rg_pattern_id;

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
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90063");
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
        //作業パターンのチェックの組み合わせチェック----

        //----Key変数の種類ごとに、バリデーションチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            switch($rg_col_type){
            case '2':   // Key
            case '3':   // Key-Value
                // --UPD--
                $vars_link_id          = $rg_key_vars_link_id;
                // Key変数入力チェック
                if(strlen($vars_link_id) == 0){
                    // --UPD--
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90064");
                    $retBool = false;
                    $boolExecuteContinue = false;
                    break;
                }
                // --UPD--
                $strQuery = "SELECT "
                       ." TAB_1.VARS_LINK_ID "
                       ."FROM "
                       ." $ptn_vars_link_view TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG = '0' "
                       ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID ";

                $aryForBind = array();
                $aryForBind['VARS_LINK_ID'] = $vars_link_id;

                $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(VARS_TYPE_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery = $retArray[1];
                    $tmpAryRow = array();
                    while($row = $objQuery->resultFetch() ){
                        $tmpAryRow[]= $row;
                    }

                    if( count($tmpAryRow) === 0 ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90084");
                        $retBool = false;
                        $boolExecuteContinue = false;
                    }
                    unset($tmpAryRow);
                    unset($objQuery);
                }
                unset($retArray);

                break;
            }
        }
        //----Value変数の種類ごとに、バリデーションチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            switch($rg_col_type){
            case '1':   // Value
            case '3':   // Key-Value
                // --UPD--
                $vars_link_id          = $rg_val_vars_link_id;
                $assign_seq            = $rg_val_assign_seq;
                // Key変数入力チェック
                if(strlen($vars_link_id) == 0){
                    // --UPD--
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90065");
                    $retBool = false;
                    $boolExecuteContinue = false;
                    break;
                }
                // --UPD--
                $strQuery = "SELECT "
                       ." TAB_1.VARS_LINK_ID "
                       ."FROM "
                       ." $ptn_vars_link_view TAB_1 "
                       ."WHERE "
                       ." TAB_1.DISUSE_FLAG = '0' "
                       ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID ";

                $aryForBind = array();
                $aryForBind['VARS_LINK_ID'] = $vars_link_id;

                $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(VARS_TYPE_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery = $retArray[1];
                    $tmpAryRow = array();
                    while($row = $objQuery->resultFetch() ){
                        $tmpAryRow[]= $row;
                    }

                    if( count($tmpAryRow) === 0 ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90085");
                        $retBool = false;
                        $boolExecuteContinue = false;
                    }

                    unset($tmpAryRow);
                    unset($objQuery);
                }
                unset($retArray);

                break;
            }
        }
        //変数の種類ごとに、バリデーションチェック----

        // 代入値自動登録設定テーブルの重複レコードチック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
            $strQuery =   "SELECT "
                        . "  COLUMN_ID "
                        . "FROM "
                        . " B_DSC_VAL_ASSIGN "
                        . "WHERE  "
                        . " COLUMN_ID   <> :COLUMN_ID AND "
                        . " MENU_ID     =  :MENU_ID  AND "
                        . " PATTERN_ID  =  :PATTERN_ID  AND "
                        . " DISUSE_FLAG =  '0'"
                        . " AND(";

            $aryForBind = array();
            $aryForBind['COLUMN_ID']    = $columnId;
            $aryForBind['MENU_ID']      = $rg_menu_id;
            $aryForBind['PATTERN_ID']   = $rg_pattern_id;

            // Key変数が必須の場合
            if(in_array($rg_col_type, array(2, 3))){
                $strQuery .= " ( ";
                $strQuery .= "COL_TYPE in (2,3) AND KEY_VARS_LINK_ID = :KEY_VARS_LINK_ID_1 ";
                $strQuery .= " ) OR (";
                $strQuery .= "COL_TYPE in (1,3) AND VAL_VARS_LINK_ID = :VAL_VARS_LINK_ID_1 ";
                $strQuery .= " ) ";
                $aryForBind['KEY_VARS_LINK_ID_1']           = $rg_key_vars_link_id;
                $aryForBind['VAL_VARS_LINK_ID_1']           = $rg_key_vars_link_id;
            }

            if(in_array($rg_col_type, array(3))){
                $strQuery .= " OR ";
            }

            // Value変数が必須の場合
            if(in_array($rg_col_type, array(1, 3))){
                $strQuery .= " ( ";
                $strQuery .= "COL_TYPE in (2,3) AND KEY_VARS_LINK_ID = :KEY_VARS_LINK_ID_2 ";
                $strQuery .= " ) OR (";
                $strQuery .= "COL_TYPE in (1,3) AND VAL_VARS_LINK_ID = :VAL_VARS_LINK_ID_2 ";
                $strQuery .= " ) ";
                $aryForBind['KEY_VARS_LINK_ID_2']           = $rg_val_vars_link_id;
                $aryForBind['VAL_VARS_LINK_ID_2']           = $rg_val_vars_link_id;
            }
            $strQuery .= " ) ";
            $retArray = singleSQLExecuteAgent($strQuery, $aryForBind, "NONAME_FUNC(VALASSIGN_DUP_CHECK)");
            if( $retArray[0] === true ){
                $objQuery = $retArray[1];
                $dupnostr = "";
                while($row = $objQuery->resultFetch() ){
                    $dupnostr = $dupnostr . "[" . $row['COLUMN_ID'] . "]";
                }
                if( strlen($dupnostr) != 0 ){
                    $retBool = false;
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITADSCH-ERR-90127",array($dupnostr));
                }
                unset($objQuery);
            }
            else{
                $boolSystemErrorFlag = true;
            }
            unset($retArray);
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
