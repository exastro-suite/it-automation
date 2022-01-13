<?php
//   Copyright 2021 NEC Corporation
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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310100");

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

    $table = new TableControlAgent('D_CONTRAST_DETAIL','CONTRAST_DETAIL_ID', 'No', 'D_CONTRAST_DETAIL_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['CONTRAST_DETAIL_ID']->setSequenceID('A_CONTRAST_DETAIL_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('A_CONTRAST_DETAIL_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('A_CONTRAST_DETAIL');
    $table->setDBJournalTableHiddenID('A_CONTRAST_DETAIL_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // マルチユニーク制約
    $table->addUniqueColumnSet(array('CONTRAST_LIST_ID','CONTRAST_COL_TITLE','CONTRAST_COL_ID_1','CONTRAST_COL_ID_2'));
     
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-310101"));//'比較定義詳細'
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITABASEH-MNU-310101"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);
    // 検索機能の制御----

    $table->setAccessAuth(true);    // データごとのRBAC設定

    //'比較定義名'
    $c = new IDColumn('CONTRAST_LIST_ID',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310102"),'D_CONTRAST_LIST','CONTRAST_LIST_ID','PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310103"));//エクセル・ヘッダでの説明
    $c->setEvent('update_table', 'onchange', 'Mix1_1_contrast_menu_upd');
    $c->setEvent('register_table', 'onchange', 'Mix2_1_contrast_menu_reg');
    $c->setHiddenMainTableColumn(true);
    $c->setRequired(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('CONTRAST_LIST_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CONTRAST_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'CONTRAST_LIST_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'PULLDOWN',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //'表示項目名'
    $objVldt = new SingleTextValidator(0,256,false);
    $c = new TextColumn('CONTRAST_COL_TITLE',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310104"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310105"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
    $c->setRequired(true);
    
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);


    //対象カラム1
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
                    if(strlen($g['CONTRAST_COL_ID_1_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['CONTRAST_COL_ID_1_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };

    $c = new IDColumn('CONTRAST_COL_ID_1',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310106"),'D_CMDB_MG_MU_COL_LIST_CONTRAST ','COLUMN_LIST_ID','MENU_COL_TITLE_PULLDOWN','' );

    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310108"));

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
                   .",TAB_1.MENU_COL_TITLE_PULLDOWN       DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   .",TAB_1.ACCESS_AUTH_03 ACCESS_AUTH_03 "
                   ."FROM "
                   ." D_CMDB_MG_MU_COL_LIST_CONTRAST  TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.MENU_ID = :MENU_ID "
                   ."ORDER BY COL_TITLE_DISP_SEQ";

        $aryForBind['MENU_ID'] = $strMenuIDNumeric;

        if( 0 < strlen($strMenuIDNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                        break;
                    }else{
                        if($permission === true){
                            $aryDataSet[]= $row;
                        }
                    }
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

        $strMenuIDNumeric = null;
        if(is_array($rowData) && array_key_exists('CONTRAST_LIST_ID', $rowData)){
            $strMenuIDNumeric = $rowData['CONTRAST_LIST_ID'];
        }

        $strQuery = "SELECT *"
                   ."FROM "
                   ." D_CONTRAST_LIST  TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                   ."ORDER BY CONTRAST_LIST_ID";

        $aryForBind['CONTRAST_LIST_ID'] = $strMenuIDNumeric;

        $rows=array();
        if( 0 < strlen($strMenuIDNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];

                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                        break;
                    }else{
                        if($permission === true){
                            $rows[]=$row;
                        }
                    }
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }

        $strMenuIDNumeric="";
        if( count($rows) == 1) {
            $strMenuIDNumeric = $rows[0]['CONTRAST_MENU_ID_1'];
        }

        $strQuery = "SELECT "
                   ." TAB_1.COLUMN_LIST_ID  KEY_COLUMN "
                   .",TAB_1.MENU_COL_TITLE_PULLDOWN       DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   .",TAB_1.ACCESS_AUTH_03 ACCESS_AUTH_03 "
                   ."FROM "
                   ." D_CMDB_MG_MU_COL_LIST_CONTRAST  TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.MENU_ID = :CONTRAST_MENU_ID_1 "
                   ."ORDER BY COL_TITLE_DISP_SEQ";
        $aryForBind=array();
        $aryForBind['CONTRAST_MENU_ID_1'] = $strMenuIDNumeric;

        if( 0 < strlen($strMenuIDNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                        break;
                    }else{
                        if($permission === true){
                            $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                        }
                    }
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

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310109");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);


    $c->setJournalTableOfMaster('D_CMDB_MG_MU_COL_LIST_CONTRAST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COLUMN_LIST_ID');
    $c->setJournalDispIDOfMaster('MENU_COL_TITLE_PULLDOWN');

    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->addColumn($c);

    unset($tmpObjFunction);

    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);


    $c = new IDColumn('REST_CONTRAST_COL_ID_1',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310107"),'D_CMDB_MG_MU_COL_LIST_CONTRAST ','COLUMN_LIST_ID','MENU_COL_TITLE_PULLDOWN','' );

    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310108"));

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

    $c->setJournalTableOfMaster('D_CMDB_MG_MU_COL_LIST_CONTRAST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COLUMN_LIST_ID');
    $c->setJournalDispIDOfMaster('MENU_COL_TITLE_PULLDOWN');

    $table->addColumn($c);

    ////////////////////////////////////////////////////////////
    //対象カラム2
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
                    if(strlen($g['CONTRAST_COL_ID_2_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['CONTRAST_COL_ID_2_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };
    $c = new IDColumn('CONTRAST_COL_ID_2',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310107"),'D_CMDB_MG_MU_COL_LIST_CONTRAST ','COLUMN_LIST_ID','MENU_COL_TITLE_PULLDOWN','' );

    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310108"));

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
                   .",TAB_1.MENU_COL_TITLE_PULLDOWN       DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   .",TAB_1.ACCESS_AUTH_03 ACCESS_AUTH_03 "
                   ."FROM "
                   ." D_CMDB_MG_MU_COL_LIST_CONTRAST  TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.MENU_ID = :MENU_ID "
                   ."ORDER BY COL_TITLE_DISP_SEQ";

        $aryForBind['MENU_ID'] = $strMenuIDNumeric;

        if( 0 < strlen($strMenuIDNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                        break;
                    }else{
                        if($permission === true){
                            $aryDataSet[]= $row;
                        }
                    }
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

        $strMenuIDNumeric = null;
        if(is_array($rowData) && array_key_exists('CONTRAST_LIST_ID', $rowData)){
            $strMenuIDNumeric = $rowData['CONTRAST_LIST_ID'];
        }

        $strQuery = "SELECT *"
                   ."FROM "
                   ." D_CONTRAST_LIST  TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                   ."ORDER BY CONTRAST_LIST_ID";

        $aryForBind['CONTRAST_LIST_ID'] = $strMenuIDNumeric;
        $rows=array();
        if( 0 < strlen($strMenuIDNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];

                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                        break;
                    }else{
                        if($permission === true){
                            $rows[]=$row;
                        }
                    }
                }
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }
        }

        $strMenuIDNumeric="";
        if( count($rows) == 1) {
            $strMenuIDNumeric = $rows[0]['CONTRAST_MENU_ID_2'];
        }

        $strQuery = "SELECT "
                   ." TAB_1.COLUMN_LIST_ID  KEY_COLUMN "
                   .",TAB_1.MENU_COL_TITLE_PULLDOWN       DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   .",TAB_1.ACCESS_AUTH_03 ACCESS_AUTH_03 "
                   ."FROM "
                   ." D_CMDB_MG_MU_COL_LIST_CONTRAST  TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG IN ('0') "
                   ." AND TAB_1.MENU_ID = :CONTRAST_MENU_ID_2 "
                   ."ORDER BY COL_TITLE_DISP_SEQ";
        $aryForBind=array();
        $aryForBind['CONTRAST_MENU_ID_2'] = $strMenuIDNumeric;

        if( 0 < strlen($strMenuIDNumeric) ){
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                        break;
                    }else{
                        if($permission === true){
                            $aryDataSet[$row['KEY_COLUMN']]= $row['DISP_COLUMN'];
                        }
                    }
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

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310109");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);

    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);


    $c->setJournalTableOfMaster('D_CMDB_MG_MU_COL_LIST_CONTRAST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COLUMN_LIST_ID');
    $c->setJournalDispIDOfMaster('MENU_COL_TITLE_PULLDOWN');

    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->addColumn($c);



    $c = new IDColumn('REST_CONTRAST_COL_ID_2',$g['objMTS']->getSomeMessage("ITABASEH-MNU-310107"),'D_CMDB_MG_MU_COL_LIST_CONTRAST ','COLUMN_LIST_ID','MENU_COL_TITLE_PULLDOWN','' );

    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310108"));

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

    $c->setJournalTableOfMaster('D_CMDB_MG_MU_COL_LIST_CONTRAST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('COLUMN_LIST_ID');
    $c->setJournalDispIDOfMaster('MENU_COL_TITLE_PULLDOWN');

    $table->addColumn($c);

    unset($tmpObjFunction);

    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);



    // 表示順序
    $c = new NumColumn('DISP_SEQ', $g['objMTS']->getSomeMessage("ITABASEH-MNU-310110"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-310111"));
    $c->setSubtotalFlag(false);
    $c->setHiddenMainTableColumn(true);
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

        $pattan_tbl         = "D_CMDB_MG_MU_COL_LIST_CONTRAST";      // モード毎

        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $intContrastId = isset($arrayVariant['edit_target_row']['CONTRAST_LIST_ID'])?
                                        $arrayVariant['edit_target_row']['CONTRAST_LIST_ID']:null;
            $intContratColId1       = isset($arrayVariant['edit_target_row']['CONTRAST_COL_ID_1'])?
                                        $arrayVariant['edit_target_row']['CONTRAST_COL_ID_1']:null;
            $intContratColId2        = isset($arrayVariant['edit_target_row']['CONTRAST_COL_ID_2'])?
                                        $arrayVariant['edit_target_row']['CONTRAST_COL_ID_2']:null;

            $intRestContratColId1    =    isset($arrayVariant['edit_target_row']['REST_CONTRAST_COL_ID_1'])?
                                                              $arrayVariant['edit_target_row']['REST_CONTRAST_COL_ID_1']:null;
            $intRestContratColId2    =    isset($arrayVariant['edit_target_row']['REST_CONTRAST_COL_ID_2'])?
                                                              $arrayVariant['edit_target_row']['REST_CONTRAST_COL_ID_2']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                if( strlen($intContrastId) === 0 ){
                    $boolSystemErrorFlag = true;
                }
            }
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $intContrastId       = array_key_exists('CONTRAST_LIST_ID',$arrayRegData)?
                                           $arrayRegData['CONTRAST_LIST_ID']:null;
            $intContratColId1             = array_key_exists('CONTRAST_COL_ID_1',$arrayRegData)?
                                           $arrayRegData['CONTRAST_COL_ID_1']:null;
            $intContratColId2              = array_key_exists('CONTRAST_COL_ID_2',$arrayRegData)?
                                           $arrayRegData['CONTRAST_COL_ID_2']:null;
            $intRestContratColId1    = array_key_exists('REST_CONTRAST_COL_ID_1',$arrayRegData) ?
                                            $arrayRegData['REST_CONTRAST_COL_ID_1']:null;
            $intRestContratColId2    = array_key_exists('REST_CONTRAST_COL_ID_2',$arrayRegData) ?
                                            $arrayRegData['REST_CONTRAST_COL_ID_2']:null;
        }

        $g['CONTRAST_COL_ID_1_UPDATE_VALUE'] = "";
        $g['CONTRAST_COL_ID_2_UPDATE_VALUE'] = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定

        // CONTRAST_MENU_ID_1/2;未設定 REST_CONTRAST_MENU_ID_1/2:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( (strlen($intContratColId1) === 0) && (strlen($intRestContratColId1) !== 0) 
                &&  (strlen($intContratColId2) === 0) && (strlen($intRestContratColId2) !== 0)  
                ){

                    $query = " SELECT "
                             ." * "
                             ."FROM "
                             ."   D_CONTRAST_LIST TBL_A  "
                             ." WHERE "
                             ."   TBL_A.CONTRAST_LIST_ID    = :CONTRAST_LIST_ID AND "
                             ."   TBL_A.DISUSE_FLAG  = '0' ";

                    $aryForBind = array();
                    $aryForBind['CONTRAST_LIST_ID']     = $intContrastId;
                    $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(COLUMN_LIST_ID_1_CHECK)");

                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $intCount = 0;
                        $row = $objQuery->resultFetch();

                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $boolSystemErrorFlag = true;
                    }
                    unset($retArray);

                    if( isset($row['CONTRAST_MENU_ID_1']) === true && isset($row['CONTRAST_MENU_ID_2']) === true  ){
                        $intmenuId1 = $row['CONTRAST_MENU_ID_1'];
                        $intmenuId2 = $row['CONTRAST_MENU_ID_2'];
                    }

                    $arrColList = array(
                        'CONTRAST_COL_ID_1_UPDATE_VALUE' => $intRestContratColId1,
                        'CONTRAST_COL_ID_2_UPDATE_VALUE' => $intRestContratColId2
                    );

                    $arrMenuist = array(
                        'CONTRAST_COL_ID_1_UPDATE_VALUE' => $intmenuId1,
                        'CONTRAST_COL_ID_2_UPDATE_VALUE' => $intmenuId2
                    );
                    unset($row);
                    unset($objQuery);

                foreach ( $arrColList as $intColkey=> $intContratColId ) {
                    $retBool = false;
                    $boolExecuteContinue = false;

                    $query = " SELECT "
                             ."   COUNT(*) AS COLUMN_LIST_ID_CNT "
                             ."FROM "
                             ."   D_CMDB_MG_MU_COL_LIST_CONTRAST TBL_A  "
                             ." WHERE "
                             ."   TBL_A.COLUMN_LIST_ID    = :COLUMN_LIST_ID AND "
                             ."   TBL_A.MENU_ID    = :MENU_ID AND "
                             ."   TBL_A.DISUSE_FLAG  = '0' ";

                    $aryForBind = array();
                    $aryForBind['COLUMN_LIST_ID'] = $intContratColId;
                    $aryForBind['MENU_ID']        = $arrMenuist[$intColkey];

                    $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(COLUMN_LIST_ID_1_CHECK)");

                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $intCount = 0;
                        $row = $objQuery->resultFetch();

                        if( $row['COLUMN_LIST_ID_CNT'] == '1' ){
                            $g[$intColkey] = $intContratColId;
                            $retBool = true;
                            $boolExecuteContinue = true;
                        }else if( $row['COLUMN_LIST_ID_CNT'] == '0' ){
                            $boolExecuteContinue = false;
                            $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-310100");
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
        }

        //呼出元がUIがRestAPI/Excel/CSVかを判定----
        //----必須入力チェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( strlen($intContrastId) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-310101");
                $boolExecuteContinue = false;
                $retBool = false;
            }elseif( $intRestContratColId1 == "" && $intRestContratColId2 == ""  ){
                //WEB
                if( $intContratColId1 == "" || $intContratColId2 == ""  ){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-310100");
                    $boolExecuteContinue = false;
                    $retBool = false;                    
                }
            }elseif( $intContratColId1 == "" && $intContratColId2 == ""  ){
                //RestAPI/Excel/CSV 
                if( $intRestContratColId1 == "" || $intRestContratColId2 == ""  ){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-310100");
                    $boolExecuteContinue = false;
                    $retBool = false;                    
                }
            }
        }
        //必須入力チェック----

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
