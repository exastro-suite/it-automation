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
//    ・Ansible（Legacy Role）代入値自動登録設定
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900000");

/*
Ansible（Legacy Role）代入値自動登録設定
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

    $table = new TableControlAgent('D_ANS_LRL_VAL_ASSIGN','COLUMN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900001"), 'D_ANS_LRL_VAL_ASSIGN_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['COLUMN_ID']->setSequenceID('B_ANS_LRL_VAL_ASSIGN_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANS_LRL_VAL_ASSIGN_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_ANS_LRL_VAL_ASSIGN');
    $table->setDBJournalTableHiddenID('B_ANS_LRL_VAL_ASSIGN_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900003"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----

    ////////////////////////////////////////////////////////////
    // ColumnGroup:パラメータシート 開始
    ////////////////////////////////////////////////////////////
    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900301"));

        ////////////////////////////////////////////////////////////
        // カラムグループ メニューグループ(一覧のみ表示)
        ////////////////////////////////////////////////////////////
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900004"));

            ////////////////////////////////////////////////////////////
            // メニューグループID
            ////////////////////////////////////////////////////////////
            $c = new IDColumn('MENU_GROUP_ID', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900005"), 'A_MENU_GROUP_LIST', 'MENU_GROUP_ID', 'MENU_GROUP_ID', '', array('OrderByThirdColumn'=>'MENU_GROUP_ID'));
            $c->addClass("number");
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900006"));
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

            ////////////////////////////////////////////////////////////
            // メニューグループ名
            ////////////////////////////////////////////////////////////
            $c = new TextColumn('MENU_GROUP_NAME', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900007"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900008"));
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

        ////////////////////////////////////////////////////////////
        // カラムグループ メニュー(一覧のみ表示)
        ////////////////////////////////////////////////////////////
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900009"));

            ////////////////////////////////////////////////////////////
            // メニューID
            ////////////////////////////////////////////////////////////
            $c = new IDColumn('MENU_ID_CLONE', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900010"), "D_MENU_LIST", 'MENU_ID', "MENU_ID", '', array('OrderByThirdColumn'=>'MENU_ID'));
            $c->addClass("number");
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900011"));
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

            $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

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

            ////////////////////////////////////////////////////////////
            // メニュー名
            ////////////////////////////////////////////////////////////
            $c = new TextColumn('MENU_NAME', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900012"));
            $c->setHiddenMainTableColumn(false);
            $c->setAllowSendFromFile(false);
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900013"));
            //----登録更新関係から隠す
            $c->setHiddenMainTableColumn(false);
            $c->getOutputType("update_table")->setVisible(false);
            $c->getOutputType("register_table")->setVisible(false);
            $c->getOutputType("excel")->setVisible(false);
            $c->getOutputType("csv")->setVisible(false);

            $c->getOutputType('json')->setVisible(false); //RestAPIでは隠す

            //登録更新関係から隠す----
            $cg->addColumn($c);

        $cgg->addColumn($cg);
        // カラムグループ メニュー----

        ////////////////////////////////////////////////////////////
        // メニューID
        ////////////////////////////////////////////////////////////
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

        // メニュー
        $c = new IDColumn('MENU_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900100"),'D_CMDB_MENU_LIST','MENU_ID','MENU_PULLDOWN','',array('OrderByThirdColumn'=>'MENU_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900101"));

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

        ////////////////////////////////////////////////////////////
        //カラムタイトル名
        ////////////////////////////////////////////////////////////
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

        $c = new IDColumn('COLUMN_LIST_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900120"),'B_CMDB_MENU_COLUMN','COLUMN_LIST_ID','COL_TITLE','',array('SELECT_ADD_FOR_ORDER'=>array('COL_TITLE_DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1') );

        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900121"));

        $c->setHiddenMainTableColumn(true); //更新対象カラム

        $c->setAllowSendFromFile(false);//エクセル/CSVからのアップロードを禁止する。

        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        $c->getOutputType('json')->setVisible(false); // RestAPIでは隠す

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

        $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900262");
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


        ////////////////////////////////////////////////////////////
        //Excel/CSV/RestAPI用  カラムタイトル名
        ////////////////////////////////////////////////////////////
        // Excel/CSV/RestAPI 用カラムタイトル名

        $c = new IDColumn('REST_COLUMN_LIST_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900125"),'D_CMDB_MG_MU_COL_LIST','COLUMN_LIST_ID','MENU_COL_TITLE_PULLDOWN','',array('SELECT_ADD_FOR_ORDER'=>array('MENU_ID','COL_TITLE_DISP_SEQ'),'ORDER'=>'ORDER BY ADD_SELECT_1,ADD_SELECT_2') );

        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900126"));

        $c->setHiddenMainTableColumn(false); //更新対象カラム


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

    ////////////////////////////////////////////////////////////
    //登録方式
    ////////////////////////////////////////////////////////////
    $c = new IDColumn('COL_TYPE',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900130"),'B_CMDB_MENU_COL_TYPE','COLUMN_TYPE_ID','COLUMN_TYPE_NAME','',array('OrderByThirdColumn'=>'COLUMN_TYPE_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900131"));

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
    $cgg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900302"));

        ////////////////////////////////////////////////////////////
        //作業パターン
        ////////////////////////////////////////////////////////////
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

        $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900140"),'E_ANSIBLE_LRL_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900141"));

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

        $c->setEvent('update_table', 'onchange', 'pattern_upd');
        $c->setEvent('register_table', 'onchange', 'pattern_reg');

        $cgg->addColumn($c);

        //////////////////////////////////////////////////
        // ColumnGroup:Key変数
        //////////////////////////////////////////////////
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900210"));

            //////////////////////////////////////////////////
            // 親Key変数
            //////////////////////////////////////////////////
            // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したKEY_VARS_LINK_IDを設定する。
            $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                        global    $g;
                        $boolRet = true;
                        $intErrorType = null;
                        $aryErrMsgBody = array();
                        $strErrMsg = "";
                        $strErrorBuf = "";

                        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                            if(strlen($g['KEY_VARS_LINK_ID_UPDATE_VALUE']) !== 0){
                                $exeQueryData[$objColumn->getID()] = $g['KEY_VARS_LINK_ID_UPDATE_VALUE'];
                            }
                        }else if( $modeValue=="DTUP_singleRecDelete" ){
                        }
                        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                        return $retArray;
            };

            $c = new IDColumn('KEY_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900220"),'D_ANS_LRL_PTN_VARS_LINK','VARS_LINK_ID','VARS_LINK_PULLDOWN','D_ANS_LRL_PTN_VARS_LINK_VFP');

            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900221"));//エクセル・ヘッダでの説明

            $c->setHiddenMainTableColumn(true); //更新対象カラム

            $c->setJournalTableOfMaster('D_ANS_LRL_PTN_VARS_LINK_JNL');
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

            $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900222");
            $objVarBFmtUpd = new SelectTabBFmt();
            $objVarBFmtUpd->setFADJsEvent('onChange','key_vars_upd');

            // フォームの表示直後、変更反映カラムの既存値が、選べる選択肢の中になかった場合のメッセージ
            $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);

            // フォームの表示直後、選択できる選択肢リストを作成する関数指定
            $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

            $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);

            $objOTForUpd->setJsEvent('onChange','key_vars_upd');

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

            $objVarBFmtReg = new SelectTabBFmt();

            $objVarBFmtReg->setFADJsEvent('onChange','key_vars_reg');

            // フォームの表示直後、トリガーカラムが選ばれていない場合のメッセージ
            $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

            $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

            $c->setOutputType('update_table',$objOTForUpd);

            $c->setOutputType('register_table',$objOTForReg);

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

            $cg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);
            unset($objFunction03);

            //////////////////////////////////////////////////
            // Keyメンバー変数名                            //
            //////////////////////////////////////////////////
            // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したKEY_CHILD_VARS_LINK_IDを設定する。
            $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                        global    $g;
                        $boolRet = true;
                        $intErrorType = null;
                        $aryErrMsgBody = array();
                        $strErrMsg = "";
                        $strErrorBuf = "";

                        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                            if(strlen($g['KEY_COL_SEQ_COMBINATION_ID_UPDATE_VALUE']) !== 0){
                                $exeQueryData[$objColumn->getID()] = $g['KEY_COL_SEQ_COMBINATION_ID_UPDATE_VALUE'];
                            }
                        }else if( $modeValue=="DTUP_singleRecDelete" ){
                        }
                        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                        return $retArray;
            };

            $c = new IDColumn('KEY_COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900230"),'D_ANS_LRL_MEMBER_COL_COMB','COL_SEQ_COMBINATION_ID','COMBINATION_MEMBER','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900231"));

            $c->setHiddenMainTableColumn(true); //更新対象カラム

            $c->setJournalTableOfMaster('D_ANS_LRL_MEMBER_COL_COMB_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
            $c->setJournalDispIDOfMaster('COMBINATION_MEMBER');

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数
            $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){

                global $g;
                $retBool = false;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $aryDataSet = array();
                $aryAddResultData = array();

                $strFxName = "";

                $strVarsLinkIdNumeric = $aryVariant['KEY_VARS_LINK_ID'];
                $strColSeqCombinationId = $aryVariant['KEY_COL_SEQ_COMBINATION_ID'];

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
                        unset($tmpAryRow);
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
                               ." AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID ";

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

                        $aryResult = getChildVars($strVarsLinkIdNumeric, $strColSeqCombinationId);
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

            // フォームの表示直後、選択できる選択肢リストを作成する関数
            $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
                global $g;
                $retBool = false;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $aryDataSet = array();

                $strFxName = "";

                $strVarsLinkIdNumeric = $rowData['KEY_VARS_LINK_ID'];

                $strQuery = "SELECT "
                           ." TAB_1.COL_SEQ_COMBINATION_ID KEY_COLUMN "
                           .",TAB_1.COMBINATION_MEMBER     DISP_COLUMN "
                           ."FROM "
                           ." D_ANS_LRL_MEMBER_COL_COMB TAB_1 "
                           ." LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_2 ON ( TAB_1.VARS_NAME_ID = TAB_2.VARS_NAME_ID ) "
                           ."WHERE "
                           ."     TAB_1.DISUSE_FLAG IN ('0') "
                           ." AND TAB_2.DISUSE_FLAG IN ('0') "
                           ." AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID ";

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
                $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900240");

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
                $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900241");

                $strOptionBodies = "";
                $strNoOptionMessageText = "";

                $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

                $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();

                //条件付き必須なので、出現するときは、空白選択させない
                $boolWhiteKeyAdd = false;

                $strFxName = "";

                $aryAddResultData = array();

                $strVarsLinkIdNumeric = $rowData['KEY_VARS_LINK_ID'];

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

            $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900242");

            $objVarBFmtUpd = new SelectTabBFmt();

            $objVarBFmtUpd->setFADJsEvent('onChange','key_chlVar_upd');     // 更新時のonChange設定

            // フォームの表示直後、変更反映カラムの既存値が、選べる選択肢の中になかった場合のメッセージ
            $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);

            // フォームの表示直後、選択できる選択肢リストを作成する関数指定
            $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

            $objVarBFmtUpd->setFunctionForGetFADMainDataOverride($objFunction04);

            $objVarBFmtUpd->setFunctionForGetMainDataOverride($objFunction05);

            $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);

            $objOTForUpd->setJsEvent('onChange','key_chlVar_upd');

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

            $objVarBFmtReg = new SelectTabBFmt();

            $objVarBFmtReg->setFADJsEvent('onChange','key_chlVar_reg');     // 登録時のonChange設定

            // フォームの表示直後、トリガーカラムが選ばれていない場合のメッセージ
            $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

            $objVarBFmtReg->setFunctionForGetFADMainDataOverride($objFunction04);

            $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

            $c->setOutputType('update_table',$objOTForUpd);

            $c->setOutputType('register_table',$objOTForReg);

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

            $cg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);
            unset($objFunction03);
            unset($objFunction04);
            unset($objFunction05);

            ////////////////////////////////////////////////////////
            //REST/excel/csv入力用 Key変数　Movement+変数名
            ////////////////////////////////////////////////////////
            // REST/excel/csv入力用 Key変数　Movement+変数名
            $c = new IDColumn('REST_KEY_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-502065"),'E_ANS_LRL_PTN_VAR_LIST','VARS_LINK_ID','PTN_VAR_PULLDOWN','',array('OrderByThirdColumn'=>'VARS_LINK_ID'));
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-503030"));
            $c->setJournalTableOfMaster('E_ANS_LRL_PTN_VAR_LIST_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
            $c->setJournalDispIDOfMaster('PTN_VAR_PULLDOWN');

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

            //コンテンツのソースがヴューの場合、登録/更新の対象外
            $c->setHiddenMainTableColumn(false);

            //エクセル/CSVからのアップロード対象
            $c->setAllowSendFromFile(true);

            //登録/更新時には、必須でない
            $c->setRequired(false);

            $cg->addColumn($c);

            ////////////////////////////////////////////////////////
            //REST/excel/csv入力用 Keyメンバー変数　変数名+メンバー変数
            ////////////////////////////////////////////////////////
            $c = new IDColumn('REST_KEY_COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900230"),'E_ANS_LRL_VAR_MEMBER_LIST','COL_SEQ_COMBINATION_ID','VAR_MEMBER_PULLDOWN','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900231"));

            $c->setJournalTableOfMaster('E_ANS_LRL_VAR_MEMBER_LIST_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
            $c->setJournalDispIDOfMaster('VAR_MEMBER_PULLDOWN');

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

            //コンテンツのソースがヴューの場合、登録/更新の対象外
            $c->setHiddenMainTableColumn(false);

            //エクセル/CSVからのアップロード対象
            $c->setAllowSendFromFile(true);

            //登録/更新時には、必須でない
            $c->setRequired(false);

            $cg->addColumn($c);

            //////////////////////////////////////////////////
            // 代入順序
            //////////////////////////////////////////////////
            $objFunction01 = function($strTagInnerBody,$objCellFormatter,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite){
                global $g;

                //入力不要
                $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900241");

                list($strVarsLinkIdNumeric,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array('KEY_VARS_LINK_ID'),null);
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
                                if(0 < strlen($rowData['KEY_ASSIGN_SEQ'])){
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

            $c = new NumColumn('KEY_ASSIGN_SEQ',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900260"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900261"));

            $c->setHiddenMainTableColumn(true); //更新対象カラム

            $c->setOutputType('update_table',$objOTForUpd);
            $c->setOutputType('register_table',$objOTForReg);
            $c->setSubtotalFlag(false);
            $c->setValidator(new IntNumValidator(1,null));

            $cg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);

        //////////////////////////////////////////////////
        // ColumnGroup:Key変数 終了                     //
        //////////////////////////////////////////////////
        $cgg->addColumn($cg);

        //////////////////////////////////////////////////
        // ColumnGroup:Value変数
        //////////////////////////////////////////////////
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900150"));

            //////////////////////////////////////////////////
            // 親Value変数
            //////////////////////////////////////////////////
            // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したVAL_VARS_LINK_IDを設定する。
            $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                        global    $g;
                        $boolRet = true;
                        $intErrorType = null;
                        $aryErrMsgBody = array();
                        $strErrMsg = "";
                        $strErrorBuf = "";

                        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                            if(strlen($g['VAL_VARS_LINK_ID_UPDATE_VALUE']) !== 0){
                                $exeQueryData[$objColumn->getID()] = $g['VAL_VARS_LINK_ID_UPDATE_VALUE'];
                            }
                        }else if( $modeValue=="DTUP_singleRecDelete" ){
                        }
                        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                        return $retArray;
            };

            $c = new IDColumn('VAL_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900160"),'D_ANS_LRL_PTN_VARS_LINK','VARS_LINK_ID','VARS_LINK_PULLDOWN','D_ANS_LRL_PTN_VARS_LINK_VFP');
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900161"));

            $c->setHiddenMainTableColumn(true); //更新対象カラム

            $c->setJournalTableOfMaster('D_ANS_LRL_PTN_VARS_LINK_JNL');
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

            $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900162");
            $objVarBFmtUpd = new SelectTabBFmt();
            $objVarBFmtUpd->setFADJsEvent('onChange','vars_upd');

            // フォームの表示直後、変更反映カラムの既存値が、選べる選択肢の中になかった場合のメッセージ
            $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);

            // フォームの表示直後、選択できる選択肢リストを作成する関数指定
            $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

            $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);

            $objOTForUpd->setJsEvent('onChange','vars_upd');

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

            $objVarBFmtReg = new SelectTabBFmt();

            $objVarBFmtReg->setFADJsEvent('onChange','vars_reg');

            // フォームの表示直後、トリガーカラムが選ばれていない場合のメッセージ
            $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

            $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

            $c->setOutputType('update_table',$objOTForUpd);
            $c->setOutputType('register_table',$objOTForReg);

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

            $cg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);
            unset($objFunction03);

            //////////////////////////////////////////
            // メンバー変数名
            //////////////////////////////////////////
            // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したKEY_CHILD_VARS_LINK_IDを設定する。
            $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                        global    $g;
                        $boolRet = true;
                        $intErrorType = null;
                        $aryErrMsgBody = array();
                        $strErrMsg = "";
                        $strErrorBuf = "";

                        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                            if(strlen($g['VAL_COL_SEQ_COMBINATION_ID_UPDATE_VALUE']) !== 0){
                                $exeQueryData[$objColumn->getID()] = $g['VAL_COL_SEQ_COMBINATION_ID_UPDATE_VALUE'];
                            }
                        }else if( $modeValue=="DTUP_singleRecDelete" ){
                        }
                        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                        return $retArray;
            };

            // $c = new IDColumn('VAL_CHILD_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900170"),'D_ANS_LRL_CHILD_VARS','CHILD_VARS_NAME_ID','CHILD_VARS_PULLDOWN','');
            $c = new IDColumn('VAL_COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900230"),'D_ANS_LRL_MEMBER_COL_COMB','COL_SEQ_COMBINATION_ID','COMBINATION_MEMBER','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900171"));

            $c->setHiddenMainTableColumn(true); //更新対象カラム

            $c->setJournalTableOfMaster('D_ANS_LRL_MEMBER_COL_COMB_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
            $c->setJournalDispIDOfMaster('COMBINATION_MEMBER');

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数
            $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
                global $g;
                $retBool = false;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $aryDataSet = array();
                $aryAddResultData = array();

                $strFxName = "";

                $strVarsLinkIdNumeric = $aryVariant['VAL_VARS_LINK_ID'];
                $strColSeqCombinationId = $aryVariant['VAL_COL_SEQ_COMBINATION_ID'];

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
                        unset($tmpAryRow);
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
                               ." AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID ";

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

                        $aryResult = getChildVars($strVarsLinkIdNumeric, $strColSeqCombinationId);

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

            // フォームの表示直後、選択できる選択肢リストを作成する関数
            $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
                global $g;
                $retBool = false;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $aryDataSet = array();

                $strFxName = "";

                $strVarsLinkIdNumeric = $rowData['VAL_VARS_LINK_ID'];
                $strQuery = "SELECT "
                           ." TAB_1.COL_SEQ_COMBINATION_ID KEY_COLUMN "
                           .",TAB_1.COMBINATION_MEMBER     DISP_COLUMN "
                           ."FROM "
                           ." D_ANS_LRL_MEMBER_COL_COMB TAB_1 "
                           ." LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_2 ON ( TAB_1.VARS_NAME_ID = TAB_2.VARS_NAME_ID ) "
                           ."WHERE "
                           ."     TAB_1.DISUSE_FLAG IN ('0') "
                           ." AND TAB_2.DISUSE_FLAG IN ('0') "
                           ." AND TAB_2.VARS_LINK_ID = :VARS_LINK_ID ";

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
                $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900180");

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
                $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900181");

                $strOptionBodies = "";
                $strNoOptionMessageText = "";

                $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

                $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();

                //条件付き必須なので、出現するときは、空白選択させない
                $boolWhiteKeyAdd = false;

                $strFxName = "";

                $aryAddResultData = array();

                $strVarsLinkIdNumeric = $rowData['VAL_VARS_LINK_ID'];

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

            $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900182");

            $objVarBFmtUpd = new SelectTabBFmt();

            $objVarBFmtUpd->setFADJsEvent('onChange','val_chlVar_upd');     // 更新時のonChange設定

            // フォームの表示直後、変更反映カラムの既存値が、選べる選択肢の中になかった場合のメッセージ
            $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);

            // フォームの表示直後、選択できる選択肢リストを作成する関数指定
            $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

            $objVarBFmtUpd->setFunctionForGetFADMainDataOverride($objFunction04);

            $objVarBFmtUpd->setFunctionForGetMainDataOverride($objFunction05);

            $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);

            $objOTForUpd->setJsEvent('onChange','val_chlVar_upd');

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

            $objVarBFmtReg = new SelectTabBFmt();

            $objVarBFmtReg->setFADJsEvent('onChange','val_chlVar_reg');     // 登録時のonChange設定

            // フォームの表示直後、トリガーカラムが選ばれていない場合のメッセージ
            $objVarBFmtReg->setSelectWaitingText($strSetInnerText);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたが、選べる選択肢がなかった場合のメッセージ
            $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);

            $objVarBFmtReg->setFunctionForGetFADMainDataOverride($objFunction04);

            $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

            // フォームの表示後、ユーザによりトリガーカラムが選ばれたとき、選べる選択肢リストを作成する関数を指定
            $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

            $c->setOutputType('update_table',$objOTForUpd);

            $c->setOutputType('register_table',$objOTForReg);

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

            $cg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);
            unset($objFunction03);
            unset($objFunction04);
            unset($objFunction05);

            ////////////////////////////////////////////////////////
            //REST/excel/csv入力用 Val変数　Movement+変数名
            ////////////////////////////////////////////////////////
            // REST/excel/csv入力用 Value変数　Movement+変数名
            $c = new IDColumn('REST_VAL_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-502066"),'E_ANS_LRL_PTN_VAR_LIST','VARS_LINK_ID','PTN_VAR_PULLDOWN','',array('OrderByThirdColumn'=>'VARS_LINK_ID'));
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-503030"));
            $c->setJournalTableOfMaster('E_ANS_LRL_PTN_VAR_LIST_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('VARS_LINK_ID');
            $c->setJournalDispIDOfMaster('PTN_VAR_PULLDOWN');

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

            //コンテンツのソースがヴューの場合、登録/更新の対象外
            $c->setHiddenMainTableColumn(false);

            //エクセル/CSVからのアップロード対象
            $c->setAllowSendFromFile(true);

            //登録/更新時には、必須でない
            $c->setRequired(false);

            $cg->addColumn($c);

            ////////////////////////////////////////////////////////
            //REST/excel/csv入力用 Valメンバー変数　変数名+メンバー変数
            ////////////////////////////////////////////////////////
            $c = new IDColumn('REST_VAL_COL_SEQ_COMBINATION_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900170"),'E_ANS_LRL_VAR_MEMBER_LIST','COL_SEQ_COMBINATION_ID','VAR_MEMBER_PULLDOWN','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900171"));
            $c->setJournalTableOfMaster('E_ANS_LRL_VAR_MEMBER_LIST_JNL');
            $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
            $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
            $c->setJournalKeyIDOfMaster('COL_SEQ_COMBINATION_ID');
            $c->setJournalDispIDOfMaster('VAR_MEMBER_PULLDOWN');

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

            //コンテンツのソースがヴューの場合、登録/更新の対象外
            $c->setHiddenMainTableColumn(false);

            //エクセル/CSVからのアップロード対象
            $c->setAllowSendFromFile(true);

            //登録/更新時には、必須でない
            $c->setRequired(false);

            $cg->addColumn($c);

            //////////////////////////////////////////////////
            // 代入順序
            //////////////////////////////////////////////////
            $objFunction01 = function($strTagInnerBody,$objCellFormatter,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite){
                global $g;

                //入力不要
                $strMsgBody01 = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900180");

                list($strVarsLinkIdNumeric,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($rowData,array('VAL_VARS_LINK_ID'),null);
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
                                if(0 < strlen($rowData['VAL_ASSIGN_SEQ'])){  
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

            $c = new NumColumn('VAL_ASSIGN_SEQ',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900200"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1900201"));

            $c->setHiddenMainTableColumn(true); //更新対象カラム

            $c->setOutputType('update_table',$objOTForUpd);
            $c->setOutputType('register_table',$objOTForReg);
            $c->setSubtotalFlag(false);
            $c->setValidator(new IntNumValidator(1,null));

            $cg->addColumn($c);

            unset($objFunction01);
            unset($objFunction02);

        //////////////////////////////////////////////////
        // ColumnGroup:Value変数 終了                   //
        //////////////////////////////////////////////////
        $cgg->addColumn($cg);

    //////////////////////////////////////////////////
    // ColumnGroup:IaC変数 終了                     //
    //////////////////////////////////////////////////
    $table->addColumn($cgg);

    ////////////////////////////////////////////////////////////////////
    // パラメータシートの具体値がNULLでも代入値管理に登録するかのフラグ
    ////////////////////////////////////////////////////////////////////
    $c = new IDColumn('NULL_DATA_HANDLING_FLG',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-6000000"),'B_VALID_INVALID_MASTER','FLAG_ID','FLAG_NAME','', array('OrderByThirdColumn'=>'FLAG_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-6000001"));
    $c->setHiddenMainTableColumn(true); //更新対象カラム

    $c->setRequired(false);

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

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
                       ."WHERE ROW_ID IN (2100020006) ";

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
    $tmpAryColumn['COLUMN_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

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
        $pattan_tbl = "E_ANSIBLE_LRL_PATTERN";

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
            $rg_key_col_seq_comb_id    = isset($arrayVariant['edit_target_row']['KEY_COL_SEQ_COMBINATION_ID'])?
                                               $arrayVariant['edit_target_row']['KEY_COL_SEQ_COMBINATION_ID']:null;
            $rg_key_assign_seq         = isset($arrayVariant['edit_target_row']['KEY_ASSIGN_SEQ'])?
                                               $arrayVariant['edit_target_row']['KEY_ASSIGN_SEQ']:null;
            $rg_val_vars_link_id       = isset($arrayVariant['edit_target_row']['VAL_VARS_LINK_ID'])?
                                               $arrayVariant['edit_target_row']['VAL_VARS_LINK_ID']:null;
            $rg_val_col_seq_comb_id    = isset($arrayVariant['edit_target_row']['VAL_COL_SEQ_COMBINATION_ID'])?
                                               $arrayVariant['edit_target_row']['VAL_COL_SEQ_COMBINATION_ID']:null;
            $rg_val_assign_seq         = isset($arrayVariant['edit_target_row']['VAL_ASSIGN_SEQ'])?
                                               $arrayVariant['edit_target_row']['VAL_ASSIGN_SEQ']:null;

            $rg_rest_column_list_id    =    isset($arrayVariant['edit_target_row']['REST_COLUMN_LIST_ID'])?
                                                  $arrayVariant['edit_target_row']['REST_COLUMN_LIST_ID']:null;
            $rg_rest_val_vars_link_id  =    isset($arrayVariant['edit_target_row']['REST_VAL_VARS_LINK_ID'])?
                                                  $arrayVariant['edit_target_row']['REST_VAL_VARS_LINK_ID']:null;
            $rg_rest_key_vars_link_id  =    isset($arrayVariant['edit_target_row']['REST_KEY_VARS_LINK_ID'])?
                                                  $arrayVariant['edit_target_row']['REST_KEY_VARS_LINK_ID']:null;
            $rg_rest_val_col_seq_comb_id  = isset($arrayVariant['edit_target_row']['REST_VAL_COL_SEQ_COMBINATION_ID'])?
                                                  $arrayVariant['edit_target_row']['REST_VAL_COL_SEQ_COMBINATION_ID']:null;
            $rg_rest_key_col_seq_comb_id  = isset($arrayVariant['edit_target_row']['REST_KEY_COL_SEQ_COMBINATION_ID'])?
                                                  $arrayVariant['edit_target_row']['REST_KEY_COL_SEQ_COMBINATION_ID']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                //----復活の場合
                if( strlen($rg_rest_column_list_id) === 0 ||  strlen($rg_col_type) === 0 || strlen($rg_pattern_id) === 0 ){
                    $boolSystemErrorFlag = true;
                }
                //復活の場合----

                $columnId = $strNumberForRI;
            }
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            //登録・更新する各カラムの値を取得
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
            $rg_key_col_seq_comb_id    = array_key_exists('KEY_COL_SEQ_COMBINATION_ID',$arrayRegData) ?
                                            $arrayRegData['KEY_COL_SEQ_COMBINATION_ID']:null;
            $rg_key_assign_seq         = array_key_exists('KEY_ASSIGN_SEQ',$arrayRegData) ?
                                            $arrayRegData['KEY_ASSIGN_SEQ']:null;
            $rg_val_vars_link_id       = array_key_exists('VAL_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['VAL_VARS_LINK_ID']:null;
            $rg_val_col_seq_comb_id    = array_key_exists('VAL_COL_SEQ_COMBINATION_ID',$arrayRegData) ?
                                            $arrayRegData['VAL_COL_SEQ_COMBINATION_ID']:null;
            $rg_val_assign_seq         = array_key_exists('VAL_ASSIGN_SEQ',$arrayRegData) ?
                                            $arrayRegData['VAL_ASSIGN_SEQ']:null;

            $rg_rest_column_list_id    = array_key_exists('REST_COLUMN_LIST_ID',$arrayRegData) ?
                                            $arrayRegData['REST_COLUMN_LIST_ID']:null;
            $rg_rest_val_vars_link_id  = array_key_exists('REST_VAL_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['REST_VAL_VARS_LINK_ID']:null;
            $rg_rest_key_vars_link_id  = array_key_exists('REST_KEY_VARS_LINK_ID',$arrayRegData) ?
                                            $arrayRegData['REST_KEY_VARS_LINK_ID']:null;
            $rg_rest_val_col_seq_comb_id  = array_key_exists('REST_VAL_COL_SEQ_COMBINATION_ID',$arrayRegData) ?
                                               $arrayRegData['REST_VAL_COL_SEQ_COMBINATION_ID']:null;
            $rg_rest_key_col_seq_comb_id  = array_key_exists('REST_KEY_COL_SEQ_COMBINATION_ID',$arrayRegData) ?
                                               $arrayRegData['REST_KEY_COL_SEQ_COMBINATION_ID']:null;

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
        // MENU_ID;未設定 COLUMN_LIST_ID:未設定 REST_COLUMN_LIST_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($rg_menu_id)             === 0) && 
               (strlen($rg_column_list_id)      === 0) &&
               (strlen($rg_rest_column_list_id) !== 0)){
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
                $aryForBind['COLUMN_LIST_ID'] = $rg_rest_column_list_id;
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
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90060");
                            $retBool = false;
                            $boolExecuteContinue = false;
                        }else{
                            $boolSystemErrorFlag = true;
                        }
                    }else if( $row['MENU_CNT'] == '0' ){
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90059");
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

        //メニューと項目の組み合わせチェック----
        //登録方式のチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            $boolExecuteContinue = false;
            if(strlen($rg_col_type) == 0){
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90061");
                $retBool = false;
                $boolExecuteContinue = false;
            }
            else{
                switch($rg_col_type){
                case '1':   // Value
                case '2':   // Key
                case '3':   // Key-Value
                    $boolExecuteContinue = true;
                    break;
                default:
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90062");
                    $retBool = false;
                    $boolExecuteContinue = false;
                    break;
                }
            }
        }

        $g['PATTERN_ID_UPDATE_VALUE']        = "";
        $g['KEY_VARS_LINK_ID_UPDATE_VALUE']  = "";
        $g['VAL_VARS_LINK_ID_UPDATE_VALUE']  = "";
        $g['VAL_COL_SEQ_COMBINATION_ID_UPDATE_VALUE'] = "";
        $g['KEY_COL_SEQ_COMBINATION_ID_UPDATE_VALUE'] = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // PATTERN_ID;未設定 KEY_VARS_LINK_ID:未設定 REST_KEY_VARS_LINK_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        $chk_pattern_id = $rg_pattern_id;
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($chk_pattern_id)              === 0) &&
               (strlen($rg_key_vars_link_id)         === 0) &&
               (($rg_col_type == '2') || ($rg_col_type == '3')) &&
               (strlen($rg_rest_key_vars_link_id)    !== 0)){
                $strColType = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90104");
                $ret = chkVarsAssociate($strColType ,$rg_rest_key_vars_link_id,$rg_rest_key_col_seq_comb_id,
                                        $rg_pattern_id, $rg_key_vars_link_id, $rg_key_col_seq_comb_id,
                                        $retStrBody,    $boolSystemErrorFlag);
                if($ret === false){
                    $retBool = false;
                    $boolExecuteContinue = false;
                }
                else{
                    $g['PATTERN_ID_UPDATE_VALUE']                  = $rg_pattern_id;
                    $g['KEY_VARS_LINK_ID_UPDATE_VALUE']            = $rg_key_vars_link_id;
                    $g['KEY_COL_SEQ_COMBINATION_ID_UPDATE_VALUE']  = $rg_key_col_seq_comb_id;
                }
            }
        }
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($chk_pattern_id)              === 0) &&
               (strlen($rg_val_vars_link_id)         === 0) &&
               (($rg_col_type == '1') || ($rg_col_type == '3')) &&
               (strlen($rg_rest_val_vars_link_id)    !== 0)){
                $strColType = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90105");
                $ret = chkVarsAssociate($strColType ,$rg_rest_val_vars_link_id,$rg_rest_val_col_seq_comb_id,
                                        $rg_pattern_id, $rg_val_vars_link_id, $rg_val_col_seq_comb_id,
                                        $retStrBody,    $boolSystemErrorFlag);
                if($ret === false){
                    $retBool = false;
                    $boolExecuteContinue = false;
                }
                else{
                    // Movementが一致しているか判定
                    if(@strlen($g['PATTERN_ID_UPDATE_VALUE']) != 0){
                        if($g['PATTERN_ID_UPDATE_VALUE'] != $rg_pattern_id){
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90082");
                            $retBool = false;
                            $boolExecuteContinue = false;
                        }
                    }
                    $g['PATTERN_ID_UPDATE_VALUE']                  = $rg_pattern_id;
                    $g['VAL_VARS_LINK_ID_UPDATE_VALUE']            = $rg_val_vars_link_id;
                    $g['VAL_COL_SEQ_COMBINATION_ID_UPDATE_VALUE']  = $rg_val_col_seq_comb_id;
                }
            }
        }

        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( strlen($rg_menu_id) === 0 || strlen($rg_column_list_id) === 0 ) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90129");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($rg_col_type) === 0) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90061");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($rg_pattern_id) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90130");
                $boolExecuteContinue = false;
                $retBool = false;
            }    
        }

        //----メニューと項目の組み合わせチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
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
                        $boolExecuteContinue = true;
                    }else if( $row['COLUMN_CNT'] == '0' ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90060");
                        $retBool = false;
                        $boolExecuteContinue = false;
                    }else{
                        $boolSystemErrorFlag = true;
                    }
                }else if( $row['MENU_CNT'] == '0' ){
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90059");
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
        //メニューと項目の組み合わせチェック----
        //----作業パターンのチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
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
                    $boolExecuteContinue = true;
                }else if( $row['PATTAN_CNT'] == '0' ){
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90063");
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
        //作業パターンのチェックの組み合わせチェック----

        //----変数部分のチェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){

            // key部分とvalue部分の2回チェックを回す
            for($i = 0; $i < 2; $i++){
                if(0 === $i && in_array($rg_col_type, array('2', '3'))){
                    $intVarsLinkId          = $rg_key_vars_link_id;
                    $intColSeqCombId        = $rg_key_col_seq_comb_id;
                    $intSeqOfAssign         = $rg_key_assign_seq;
                    $strColType = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90104");

                }
                else if(1 === $i && in_array($rg_col_type, array('1', '3'))){
                    $intVarsLinkId          = $rg_val_vars_link_id;
                    $intColSeqCombId        = $rg_val_col_seq_comb_id;
                    $intSeqOfAssign         = $rg_val_assign_seq;
                    $strColType = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90105");

                    if(in_array($rg_col_type, array('1', '3'))){
                        $strKeyVar = $rg_key_vars_link_id . "|" . $rg_key_col_seq_comb_id . "|" . $rg_key_assign_seq;
                        $strValVar = $rg_val_vars_link_id . "|" . $rg_val_col_seq_comb_id . "|" . $rg_val_assign_seq;
                        if($strKeyVar === $strValVar){
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90128");
                            $retBool = false;
                            $boolExecuteContinue = false;
                            break;
                        }
                    }
                }
                else{
                    continue;
                }

                //----変数タイプを取得
                $intVarType = -1;
                $strQuery = "SELECT "
                           ." TAB_1.VARS_LINK_ID "
                           .",TAB_1.VARS_ATTRIBUTE_01 "
                           ."FROM "
                           ." D_ANS_LRL_PTN_VARS_LINK_VFP TAB_1 "
                           ."WHERE "
                           ." TAB_1.DISUSE_FLAG IN ('0') "
                           ." AND TAB_1.VARS_LINK_ID = :VARS_LINK_ID "
                           ." AND TAB_1.PATTERN_ID = :PATTERN_ID ";

                $aryForBind = array();
                $aryForBind['VARS_LINK_ID'] = $intVarsLinkId;
                $aryForBind['PATTERN_ID']   = $rg_pattern_id;
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
                            break;
                        }
                        unset($tmpRow);
                    }else{
                        $strMsgId = ($i === 0?"ITAANSIBLEH-ERR-90084":"ITAANSIBLEH-ERR-90085");
                        $retStrBody = $g['objMTS']->getSomeMessage($strMsgId);
                        $retBool = false;
                        $boolExecuteContinue = false;
                        break;
                    }
                    unset($tmpAryRow);
                    unset($objQuery);
                }else{
                    $boolSystemErrorFlag = true;
                    break;
                }
                unset($retArray);
                //変数タイプを取得----

                // 変数の種類ごとに、バリデーションチェック
                // 変数タイプが「一般変数」の場合
                if(1 == $intVarType){

                    // メンバー変数名のチェック
                    if( 0 < strlen($intColSeqCombId) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90107", array($strColType));
                        break;
                    }
                    // 代入順序のチェック
                    if( 0 < strlen($intSeqOfAssign) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90109", array($strColType));
                        break;
                    }
                }
               // 変数タイプが「複数具体値変数」の場合
                else if(2 == $intVarType){

                    // メンバー変数名のチェック
                    if( 0 < strlen($intColSeqCombId) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90116", array($strColType));
                        break;
                    }
                    // 代入順序のチェック
                    if( 0 === strlen($intSeqOfAssign) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90118", array($strColType));
                        break;
                    }
                }
                // 変数タイプが「多次元変数」の場合
                else if(3 == $intVarType){

                    // メンバー変数名のチェック
                    if( 0 === strlen($intColSeqCombId) ){
                        $retBool = false;
                        $boolExecuteContinue = false;
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90120", array($strColType));
                        break;
                    }
                    else {
                        // メンバー変数管理テーブル取得
                        $aryResult = getChildVars($intVarsLinkId, $intColSeqCombId);
                        if("array" === gettype($aryResult) && 1 === count($aryResult)){
                            $childData = $aryResult[0];
                        }
                        else if("array" === gettype($aryResult) && 0 === count($aryResult)){
                            $retBool = false;
                            $boolExecuteContinue = false;
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90112", array($strColType));
                            break;
                        }
                        else{
                            $boolSystemErrorFlag = true;
                            break;
                        }
                    }
                    // 代入順序のチェック
                    $intAssignSeqNeed = $childData['ASSIGN_SEQ_NEED'];

                    // 代入順序の有無が有の場合
                    if(1 ==  $intAssignSeqNeed){
                        if( 0 === strlen($intSeqOfAssign) ){
                            $retBool = false;
                            $boolExecuteContinue = false;
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90123", array($strColType));
                            break;
                        }
                    }
                    // 代入順序の有無が無の場合
                    else{
                        if( 0 < strlen($intSeqOfAssign) ){
                            $retBool = false;
                            $boolExecuteContinue = false;
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90124", array($strColType));
                            break;
                        }
                    }
                }
            }
        }
        //変数部分のチェック----

        // 代入値自動登録設定テーブルの重複レコードチック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false ){
            $strQuery =   "SELECT "
                        . "  COLUMN_ID "
                        . "FROM "
                        . " B_ANS_LRL_VAL_ASSIGN "
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
                $strQuery .= "KEY_VARS_LINK_ID = :KEY_VARS_LINK_ID_1 ";
                $strQuery .= ("" != $rg_key_col_seq_comb_id? " AND KEY_COL_SEQ_COMBINATION_ID   = :KEY_COL_SEQ_COMBINATION_ID_1 ":null);
                $strQuery .= ("" != $rg_key_assign_seq?      " AND KEY_ASSIGN_SEQ               = :KEY_ASSIGN_SEQ_1 ":null);
                $strQuery .= " ) OR (";
                $strQuery .= "VAL_VARS_LINK_ID = :VAL_VARS_LINK_ID_1 ";
                $strQuery .= ("" != $rg_key_col_seq_comb_id? " AND VAL_COL_SEQ_COMBINATION_ID   = :VAL_COL_SEQ_COMBINATION_ID_1 ":null);
                $strQuery .= ("" != $rg_key_assign_seq?      " AND VAL_ASSIGN_SEQ               = :VAL_ASSIGN_SEQ_1 ":null);
                $strQuery .= " ) ";
                $aryForBind['KEY_VARS_LINK_ID_1']           = $rg_key_vars_link_id;
                $aryForBind['VAL_VARS_LINK_ID_1']           = $rg_key_vars_link_id;
                if("" != $rg_key_col_seq_comb_id){
                    $aryForBind['KEY_COL_SEQ_COMBINATION_ID_1'] = $rg_key_col_seq_comb_id;
                    $aryForBind['VAL_COL_SEQ_COMBINATION_ID_1'] = $rg_key_col_seq_comb_id;
                }
                if("" != $rg_key_assign_seq){
                    $aryForBind['VAL_ASSIGN_SEQ_1']         = $rg_key_assign_seq;
                    $aryForBind['KEY_ASSIGN_SEQ_1']         = $rg_key_assign_seq;
                }
            }

            if(in_array($rg_col_type, array(3))){
                $strQuery .= " OR ";
            }

            // Value変数が必須の場合
            if(in_array($rg_col_type, array(1, 3))){
                $strQuery .= " ( ";
                $strQuery .= "KEY_VARS_LINK_ID = :KEY_VARS_LINK_ID_2 ";
                $strQuery .= ("" != $rg_val_col_seq_comb_id? " AND KEY_COL_SEQ_COMBINATION_ID   = :KEY_COL_SEQ_COMBINATION_ID_2 ":null);
                $strQuery .= ("" != $rg_val_assign_seq?         " AND KEY_ASSIGN_SEQ            = :KEY_ASSIGN_SEQ_2 ":null);
                $strQuery .= " ) OR (";
                $strQuery .= "VAL_VARS_LINK_ID = :VAL_VARS_LINK_ID_2 ";
                $strQuery .= ("" != $rg_val_col_seq_comb_id? " AND VAL_COL_SEQ_COMBINATION_ID   = :VAL_COL_SEQ_COMBINATION_ID_2 ":null);
                $strQuery .= ("" != $rg_val_assign_seq?      " AND VAL_ASSIGN_SEQ               = :VAL_ASSIGN_SEQ_2 ":null);
                $strQuery .= " ) ";
                $aryForBind['KEY_VARS_LINK_ID_2']           = $rg_val_vars_link_id;
                $aryForBind['VAL_VARS_LINK_ID_2']           = $rg_val_vars_link_id;
                if("" != $rg_val_col_seq_comb_id){
                    $aryForBind['KEY_COL_SEQ_COMBINATION_ID_2'] = $rg_val_col_seq_comb_id;
                    $aryForBind['VAL_COL_SEQ_COMBINATION_ID_2'] = $rg_val_col_seq_comb_id;
                }
                if("" != $rg_val_assign_seq){
                    $aryForBind['VAL_ASSIGN_SEQ_2']         = $rg_val_assign_seq;
                    $aryForBind['KEY_ASSIGN_SEQ_2']         = $rg_val_assign_seq;
                }
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
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90127",array($dupnostr));
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

/*
 * メンバー変数管理テーブル取得
 */
function getChildVars($strVarsLinkIdNumeric, $strColSeqCombinationId){

    global $g;

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

function chkVarsAssociate($in_type,$in_vars_link_id,$in_col_seq_comb_id,
                         &$out_pattern_id, &$out_vars_link_id, &$out_col_seq_comb_id,
                         &$retStrBody, &$boolSystemErrorFlag){
    global $g;

    $query_step1 =  "SELECT                                                           "
                   ."  TBL_A.VARS_LINK_ID,                                            "
                   ."  TBL_A.PATTERN_ID,                                              "
                   ."  TBL_A.VARS_NAME_ID,                                            "
                   ."  COUNT(*) AS VARS_LINK_ID_CNT,                                  "
                   ."  ( SELECT                                                       "
                   ."      VARS_ATTRIBUTE_01                                          "
                   ."    FROM                                                         "
                   ."      B_ANSIBLE_LRL_VARS_MASTER TBL_B                            "
                   ."    WHERE                                                        "
                   ."      TBL_B.VARS_NAME_ID = TBL_A.VARS_NAME_ID AND                "
                   ."      TBL_B.DISUSE_FLAG  = '0'                                   "
                   ."  ) AS VARS_ATTRIBUTE_01                                         "
                   ."FROM                                                             "
                   ."  E_ANS_LRL_PTN_VAR_LIST TBL_A                                   "
                   ."WHERE                                                            "
                   ."  TBL_A.VARS_LINK_ID    = :VARS_LINK_ID   AND                    "
                   ."  TBL_A.DISUSE_FLAG     = '0'                                    ";

    $query_step2 =  "SELECT                                                           "
                   ."  COUNT(*) AS MEMBER_CNT                                         "
                   ."FROM                                                             "
                   ."  E_ANS_LRL_VAR_MEMBER_LIST TBL_A                                "
                   ."WHERE                                                            "
                   ."  TBL_A.VARS_NAME_ID             = :VARS_NAME_ID             AND "
                   ."  TBL_A.COL_SEQ_COMBINATION_ID   = :COL_SEQ_COMBINATION_ID   AND "
                   ."  TBL_A.DISUSE_FLAG     = '0'                                    ";

    $aryForBind = array();
    $aryForBind['VARS_LINK_ID'] = $in_vars_link_id;
    $retArray = singleSQLExecuteAgent($query_step1, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
    if( $retArray[0] === true ){
        $objQuery =& $retArray[1];
        $row = $objQuery->resultFetch();
        unset($objQuery);
        if( $row['VARS_LINK_ID_CNT'] == '0' ){
            $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90071");
            $retBool = false;
        }
        if( $row['VARS_LINK_ID_CNT'] == '1' ){
            $out_pattern_id                       = $row['PATTERN_ID'];
            $out_vars_link_id                     = $row['VARS_LINK_ID'];
            $var_name_id                          = $row['VARS_NAME_ID'];

            switch($row['VARS_ATTRIBUTE_01']){
            case "1":
            case "2":
                if($in_col_seq_comb_id != ""){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90131",array($in_type));
                    return false;
                }
                break;
            case "3":
                $out_col_seq_comb_id   = $in_col_seq_comb_id;
                if($in_col_seq_comb_id == ""){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90132",array($in_type));
                    return false;
                }
                // 変数とメンバー変数の組合せ判定
                $query_step2 =  "SELECT                                                           "
                               ."  COUNT(*) AS MEMBER_CNT                                         "
                               ."FROM                                                             "
                               ."  B_ANS_LRL_MEMBER_COL_COMB TBL_A                                "
                               ."WHERE                                                            "
                               ."  TBL_A.VARS_NAME_ID             = :VARS_NAME_ID             AND "
                               ."  TBL_A.COL_SEQ_COMBINATION_ID   = :COL_SEQ_COMBINATION_ID   AND "
                               ."  TBL_A.DISUSE_FLAG     = '0'                                    ";
                $aryForBind = array();
                $aryForBind['VARS_NAME_ID']           = $var_name_id;
                $aryForBind['COL_SEQ_COMBINATION_ID'] = $in_col_seq_comb_id;
                $retMemberArray = singleSQLExecuteAgent($query_step2, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retMemberArray[0] === true ){
                    $objMemberQuery =& $retMemberArray[1];
                    $MemberRow = $objMemberQuery->resultFetch();
                    unset($objMemberQuery);
                    if( $MemberRow['MEMBER_CNT'] == '0' ){
                         $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90133",array($in_type));
                         return false;
                    }
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                    return false;
                }
                break;
            default:
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90071");
                return false;
            }
        }else{
            web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
            $boolSystemErrorFlag = true;
            return false;
        }
    }else{
        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
        $boolSystemErrorFlag = true;
        return false;
    }
    return true;
}

?>
