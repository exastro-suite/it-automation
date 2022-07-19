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
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103610");
/*
Terraform代入値管理
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

    $table = new TableControlAgent('D_TERRAFORM_VARS_ASSIGN','ASSIGN_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103620"), 'D_TERRAFORM_VARS_ASSIGN_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ASSIGN_ID']->setSequenceID('B_TERRAFORM_VARS_ASSIGN_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_TERRAFORM_VARS_ASSIGN_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_TERRAFORM_VARS_ASSIGN');
    $table->setDBJournalTableHiddenID('B_TERRAFORM_VARS_ASSIGN_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    //動的プルダウンの作成用
    $table->setJsEventNamePrefix(true);


    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103630"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103640"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //************************************************************************************
    //----オペレーション
    //************************************************************************************
    //----オペレーション
    $c = new IDColumn('OPERATION_NO_UAPK',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103650"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','E_OPE_FOR_PULLDOWN_TERRAFORM',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103660"));//エクセル・ヘッダでの説明

    $c->setJournalTableOfMaster('E_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION');
    $c->setRequired(true);//登録/更新時には、入力必須
    $table->addColumn($c);
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    //オペレーション----


    //************************************************************************************
    //----作業パターン
    //************************************************************************************
    //----作業パターン
    // REST/excel/csv入力用 Movement+変数名
    $c = new IDColumn('REST_MODULE_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103670"),'E_TERRAFORM_PTN_VAR_LIST','MODULE_PTN_LINK_ID','PTN_VAR_PULLDOWN','',array('OrderByThirdColumn'=>'MODULE_PTN_LINK_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103680"));

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

    $c->setJournalTableOfMaster('E_TERRAFORM_PTN_VAR_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('MODULE_VARS_LINK_ID');
    $c->setJournalDispIDOfMaster('PTN_VAR_PULLDOWN');
    //登録/更新時には、必須でない
    $c->setRequired(false);
    $c->setRequiredMark(true);//必須マークのみ付与

    $table->addColumn($c);
    

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

    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103690"),'E_TERRAFORM_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103700"));//エクセル・ヘッダでの説明

    $c->setEvent('update_table', 'onchange', 'pattern_upd');
    $c->setEvent('register_table', 'onchange', 'pattern_reg');

    $c->setJournalTableOfMaster('E_TERRAFORM_PATTERN_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('PATTERN_ID');
    $c->setJournalDispIDOfMaster('PATTERN');

    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);
    $c->setRequiredMark(true);//必須マークのみ付与

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
    //作業パターン----


    //************************************************************************************
    //----変数名
    //************************************************************************************
    //----変数名
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
                   ." TAB_1.MODULE_VARS_LINK_ID KEY_COLUMN "
                   .",TAB_1.VARS_LINK_PULLDOWN DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   ."FROM "
                   ." D_TERRAFORM_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['PATTERN_ID']        = $strPatternIdNumeric;

        if( 0 < strlen($strPatternIdNumeric) ){
            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                $intErrorType = 500;
                $retBool = false;
            }

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
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
                   ." TAB_1.MODULE_VARS_LINK_ID KEY_COLUMN "
                   .",TAB_1.VARS_LINK_PULLDOWN DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   ."FROM "
                   ." D_TERRAFORM_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['PATTERN_ID']        = $strPatternIdNumeric;

        if( 0 < strlen($strPatternIdNumeric) ){
            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                $intErrorType = 500;
                $retBool = false;
            }

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
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

    $objFunction03 = function($objCellFormatter, $rowData, $aryVariant){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strPatternIdNumeric = null;
        if(is_array($rowData) && array_key_exists('PATTERN_ID', $rowData)){
            $strPatternIdNumeric = $rowData['PATTERN_ID'];
        }

        $strQuery = "SELECT "
                   ." TAB_1.MODULE_VARS_LINK_ID KEY_COLUMN "
                   .",TAB_1.VARS_LINK_PULLDOWN DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   .",TAB_1.ACCESS_AUTH_01 ACCESS_AUTH_01 "
                   .",TAB_1.ACCESS_AUTH_02 ACCESS_AUTH_02 "
                   ."FROM "
                   ." D_TERRAFORM_PTN_VARS_LINK_VFP TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
                   ." AND TAB_1.PATTERN_ID = :PATTERN_ID "
                   ."ORDER BY KEY_COLUMN ASC ";

        $aryForBind['PATTERN_ID']        = $strPatternIdNumeric;

        if( 0 < strlen($strPatternIdNumeric) ){
            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                $intErrorType = 500;
                $retBool = false;
            }

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
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


    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したMODULE_VARS_LINK_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['MODULE_VARS_LINK_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['MODULE_VARS_LINK_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };

    $c = new IDColumn('MODULE_VARS_LINK_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103710"),'D_TERRAFORM_PTN_VARS_LINK','MODULE_VARS_LINK_ID','VARS_LINK_PULLDOWN','D_TERRAFORM_PTN_VARS_LINK_VFP');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103720"));//エクセル・ヘッダでの説明

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103730");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setFADJsEvent('onChange','module_vars_upd'); 
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADJsEvent('onChange','module_vars_reg'); 
    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setEvent('update_table', 'onchange', 'module_vars_upd');
    $c->setEvent('register_table', 'onchange', 'module_vars_reg');

    $c->setJournalTableOfMaster('D_TERRAFORM_PTN_VARS_LINK_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('MODULE_VARS_LINK_ID');
    $c->setJournalDispIDOfMaster('VARS_LINK_PULLDOWN');

    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);//登録/更新時には、入力必須
    $c->setRequiredMark(true);//必須マークのみ付与

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(false);

    //Filter/Print/deleteのみ無効
    $c->getOutputType('filter_table')->setVisible(false);
    $c->getOutputType('print_table')->setVisible(false);
    $c->getOutputType('delete_table')->setVisible(false);

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

    //----変数名(Webページ表示用)
    $c = new IDColumn('VARS_PTN_LINK_ID',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103710"),'D_TERRAFORM_PTN_VAR_LIST','MODULE_PTN_LINK_ID','VARS_LINK_PULLDOWN','',array('OrderByThirdColumn'=>'MODULE_PTN_LINK_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103720"));//エクセル・ヘッダでの説明

    $c->setHiddenMainTableColumn(false); //更新対象カラム

    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(false);

    //エクセル/CSVからのアップロードを禁止する。
    $c->setAllowSendFromFile(false);

    // Filter/Print/delete以外無効
    $c->getOutputType('filter_table')->setVisible(true);
    $c->getOutputType('print_table')->setVisible(true);
    $c->getOutputType('delete_table')->setVisible(true);
    $c->getOutputType('update_table')->setVisible(false);
    $c->getOutputType('register_table')->setVisible(false);
    $c->getOutputType('print_journal_table')->setVisible(false);
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    $table->addColumn($c);
    //変数名(Webページ表示用)----

    //************************************************************************************
    //----HCL設定
    //************************************************************************************
    $c = new IDColumn('HCL_FLAG',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103780"), 'B_TERRAFORM_HCL_FLAG', 'HCL_FLAG', 'HCL_FLAG_SELECT', '', array('SELECT_ADD_FOR_ORDER'=>array('HCL_FLAG'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103790")); //エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_TERRAFORM_HCL_FLAG_JNL');
    $c->setDefaultValue("register_table", 1); //デフォルト値で1(OFF)
    $c->setRequired(true); //登録/更新時には、入力必須
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $c->setEvent('update_table', 'onchange', 'hcl_upd');
    $c->setEvent('register_table', 'onchange', 'hcl_reg');


    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('HCL_FLAG');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_TERRAFORM_HCL_FLAG_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'HCL_FLAG',
        'TTT_GET_TARGET_COLUMN_ID'=>'HCL_FLAG_SELECT',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);


    //************************************************************************************
    //----メンバー変数名
    //************************************************************************************
    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();
        $aryTypeSet = array();
        $aryTypeMaster = array();
        $aryAddResultData = array();    
        $aryAddResultData[0] = ""; //表示フラグタイプ格納
        $aryAddResultData[1] = ""; //デフォルト値格納
        $strFxName = "";
        $strModuleVarsLinkId = $aryVariant['MODULE_VARS_LINK_ID']; //選択されている変数名のID
        $strMemberVarsId = $aryVariant['MEMBER_VARS']; //選択されているメンバ変数のID
        $strhclId = $aryVariant['HCL_FLAG']; //選択されているHCLのID

        if( 0 < strlen($strModuleVarsLinkId) ){
            if(0 < strlen($strMemberVarsId)){
                //選択されているメンバ変数のタイプを取得
                $strQuery = "SELECT "
                        ." TAB_A.CHILD_VARS_TYPE_ID TYPE_ID "
                        .",TAB_A.CHILD_MEMBER_VARS_VALUE DEFAULT_VALUE "
                        .",TAB_A.ACCESS_AUTH ACCESS_AUTH "
                        ."FROM "
                        ." D_TERRAFORM_VAR_MEMBER TAB_A "
                        ."WHERE "
                        ." TAB_A.DISUSE_FLAG = ('0') "
                        ." AND TAB_A.VARS_ASSIGN_FLAG = ('1') "
                        ." AND TAB_A.CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID ";
                $aryForBind['CHILD_MEMBER_VARS_ID'] = $strMemberVarsId;
            }else{
                //選択されている変数名のタイプを取得
                $strQuery = "SELECT "
                        ." TAB_A.TYPE_ID TYPE_ID "
                        .",TAB_A.VARS_VALUE DEFAULT_VALUE "
                        .",TAB_A.ACCESS_AUTH ACCESS_AUTH "
                        ."FROM "
                        ." B_TERRAFORM_MODULE_VARS_LINK TAB_A "
                        ."WHERE "
                        ." TAB_A.DISUSE_FLAG = ('0') "
                        ." AND TAB_A.MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";
                $aryForBind['MODULE_VARS_LINK_ID'] = $strModuleVarsLinkId;
            }


            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                $intErrorType = 500;
                $retBool = false;
            }

            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    }else{
                        if($permission === true){
                            $aryTypeSet[]= $row;
                        }
                    }
                }
                unset($aryForBind);
                unset($objQuery);
                $retBool = true;
            }else{
                $intErrorType = 500;
                $intRowLength = -1;
            }

            if(count($aryTypeSet) == 1){
                //表示用デフォルト値をセット
                $aryAddResultData[1] = $aryTypeSet[0]['DEFAULT_VALUE'];

                //タイプIDをセット
                $typeId = $aryTypeSet[0]['TYPE_ID'];
                if($typeId == ""){
                    //TYPE_IDが空の場合$arydataSetを空のままreturn
                    $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet,$aryAddResultData);
                    return $retArray;
                }

                //タイプ管理テーブルから、MEMBER_VARS_FLAGとASSIGN_SEQ_FLAGを取得
                $strQuery = "SELECT "
                            ." TAB_A.MEMBER_VARS_FLAG MEMBER_VARS_FLAG"
                            .",TAB_A.ASSIGN_SEQ_FLAG ASSIGN_SEQ_FLAG "
                            ."FROM "
                            ." B_TERRAFORM_TYPES_MASTER TAB_A "
                            ."WHERE "
                            ." TAB_A.DISUSE_FLAG = ('0') "
                            ." AND TAB_A.TYPE_ID = :TYPE_ID ";
                $aryForBind['TYPE_ID'] = $typeId;

                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];

                    while($row = $objQuery->resultFetch()){
                        $aryTypeMaster[] = $row;
                    }
                    unset($objQuery);
                    $retBool = true;
                }else{
                    $intErrorType = 500;
                    $intRowLength = -1;
                }

                //各フラグをセット
                $memberVarsFlg = $aryTypeMaster[0]['MEMBER_VARS_FLAG'];
                $assignSeqFlg  = $aryTypeMaster[0]['ASSIGN_SEQ_FLAG'];
                if(0 == $memberVarsFlg &&  0 == $assignSeqFlg){
                    $aryAddResultData[0] = "NO_FLAG_VAL";
                }
                elseif(1 == $memberVarsFlg &&  0 == $assignSeqFlg){
                    $aryAddResultData[0]  = "MEMBER_FLAG_VAL";
                }
                elseif(0 == $memberVarsFlg &&  1 == $assignSeqFlg){
                    $aryAddResultData[0] = "ASSIGN_FLAG_VAL";
                }
                elseif(1 == $memberVarsFlg &&  1 == $assignSeqFlg){
                    $aryAddResultData[0]  = "MEMBER_FLAG_VAL";
                }
                else{
                    $intErrorType = 501;
                }
            }else{
                $intErrrorType = 502;
            }

            unset($aryRetBody);
            unset($strQuery);
            unset($aryForBind);

            //メンバ変数テーブルから選択した変数名のIDと一致するレコードをSELECT
            $strQuery = "SELECT "
                    ." TAB_1.CHILD_MEMBER_VARS_ID KEY_COLUMN "
                    .",TAB_1.CHILD_MEMBER_VARS_NEST DISP_COLUMN "
                    .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                    ."FROM "
                    ." D_TERRAFORM_VAR_MEMBER TAB_1 "
                    ."WHERE "
                    ." TAB_1.DISUSE_FLAG = ('0') "
                    ." AND TAB_1.VARS_ASSIGN_FLAG = ('1') "
                    ." AND TAB_1.PARENT_VARS_ID = :PARENT_VARS_ID "
                    ."ORDER BY DISP_COLUMN ASC ";
        
            $aryForBind['PARENT_VARS_ID']        = $strModuleVarsLinkId;

            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                $intErrorType = 500;
                $retBool = false;
            }
    
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
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

        }else{
            $aryAddResultData[0] = "NO_SELECT_VARS";
        }

        if($strhclId == 2){
            $aryAddResultData[0] = "NONE_VAL";
        }

        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet,$aryAddResultData);
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

        $strModuleVarsLinkId = null;
        if(is_array($rowData) && array_key_exists('MODULE_VARS_LINK_ID', $rowData)){
            $strModuleVarsLinkId = $rowData['MODULE_VARS_LINK_ID'];
        }

        $strQuery = "SELECT "
                   ." TAB_1.CHILD_MEMBER_VARS_ID KEY_COLUMN "
                   .",TAB_1.CHILD_MEMBER_VARS_NEST DISP_COLUMN "
                   .",TAB_1.ACCESS_AUTH ACCESS_AUTH "
                   ."FROM "
                   ." D_TERRAFORM_VAR_MEMBER TAB_1 "
                   ."WHERE "
                   ." TAB_1.DISUSE_FLAG = ('0') "
                   ." AND TAB_1.VARS_ASSIGN_FLAG = ('1') "
                   ." AND TAB_1.PARENT_VARS_ID = :PARENT_VARS_ID "
                   ."ORDER BY KEY_COLUMN ASC ";
    
        $aryForBind['PARENT_VARS_ID']        = $strModuleVarsLinkId;
    
        if( 0 < strlen($strModuleVarsLinkId) ){
            // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
            $obj = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $obj->getAccountInfo($g['login_id']);
            if($ret === false) {
                $intErrorType = 500;
                $retBool = false;
            }
    
            $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
            if( $aryRetBody[0] === true ){
                $objQuery = $aryRetBody[1];
                while($row = $objQuery->resultFetch() ){
                    // レコード毎のアクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
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
        $aryRetBody = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
        return $aryRetBody;
    };

    $objFunction04 = function($objCellFormatter, $arraySelectElement,$data,$boolWhiteKeyAdd,$varAddResultData,&$aryVariant,&$arySetting,&$aryOverride){
        global $g;
        $aryRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        //入力不要
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109300");
        $strMsgBody02 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109301");
        $strMsgBody03 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109302");

        $strOptionBodies = "";
        $strNoOptionMessageText = "";

        $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

        $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();

        if( is_array($varAddResultData) === true ){
            if( array_key_exists(0,$varAddResultData) === true ){
                if(in_array($varAddResultData[0], array("MEMBER_FLAG_VAL", "FLAG_VAL"))){
                    //セレクトボックスを生成
                    $strOptionBodies = makeSelectOption($arraySelectElement, $data, true, "", true);
                }else if(in_array($varAddResultData[0], array("NO_FLAG_VAL", "ASSIGN_FLAG_VAL"))){
                    //入力不要
                    $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody01;
                }else if(in_array($varAddResultData[0], array("NO_SELECT_VARS"))){
                    //変数名を選択してください
                    $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody02;
                }else if(in_array($varAddResultData[0], array("NONE_VAL"))){
                    //入力不要※HCLがONの場合
                    $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody03;
                }else{
                    //入力不要
                    $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody01;
                }
            }else{
                //入力不要
                $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody01;
            }
        }
        $aryRetBody['optionBodies'] = $strOptionBodies;
        $aryRetBody['NoOptionMessageText'] = $strNoOptionMessageText;
        $retArray = array($aryRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;
	};
    //一覧から更新ボタンを押したときの処理
    $objFunction05 = function($objCellFormatter, $arraySelectElement,$data,$boolWhiteKeyAdd,$rowData,$aryVariant){
        global $g;
        $aryRetBody = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();
        $aryTypeSet = array();

        //入力不要
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109303");
        $strMsgBody03 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109304");
        
        $strModuleVarsLinkId = null; //選択されている変数名のID
        if(is_array($rowData) && array_key_exists('MODULE_VARS_LINK_ID', $rowData)){
            $strModuleVarsLinkId = $rowData['MODULE_VARS_LINK_ID'];
        }

        $strMemberVarsId = null; //選択されているメンバ変数のID
        if(is_array($rowData) && array_key_exists('MEMBER_VARS', $rowData)){
            $strMemberVarsId = $rowData['MEMBER_VARS'];
        }

        $strhclId = null;
        if(is_array($rowData) && array_key_exists('HCL_FLAG', $rowData)){
            $strhclId = $rowData['HCL_FLAG'];
        }

        $strOptionBodies = "";
        $strNoOptionMessageText = "";

        $strHiddenInputBody = "<input type=\"hidden\" name=\"".$objCellFormatter->getFSTNameForIdentify()."\" value=\"\"/>";

        $strNoOptionMessageText = $strHiddenInputBody.$objCellFormatter->getFADNoOptionMessageText();

        //条件付き必須なので、出現するときは、空白選択させない
        $tmpBoolWhiteKeyAdd = false;
        $strFxName = "";
        $aryAddResultData = array();
        $aryAddResultData[0] = "NO_FLAG_VAL";
        if(0 < strlen($strModuleVarsLinkId)){

            $strQuery = "SELECT "
                        ." TAB_A.TYPE_ID TYPE_ID "
                        .",TAB_A.VARS_VALUE DEFAULT_VALUE "
                        .",TAB_A.ACCESS_AUTH ACCESS_AUTH "
                        ."FROM "
                        ." B_TERRAFORM_MODULE_VARS_LINK TAB_A "
                        ."WHERE "
                        ." TAB_A.DISUSE_FLAG = ('0') "
                        ." AND TAB_A.MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";
            $aryForBind['MODULE_VARS_LINK_ID'] = $strModuleVarsLinkId;
                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }
    
                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];
                    while($row = $objQuery->resultFetch() ){
                        // レコード毎のアクセス権を判定
                        list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                        if($ret === false) {
                            $intErrorType = 500;
                            $retBool = false;
                        }else{
                            if($permission === true){
                                $aryTypeSet[]= $row;
                            }
                        }
                    }
                    unset($aryForBind);
                    unset($objQuery);
                    $retBool = true;
                }else{
                    $intErrorType = 500;
                    $intRowLength = -1;
                }
    
                if(count($aryTypeSet) == 1 && isset($aryTypeSet[0]['TYPE_ID'])){
                    //表示用デフォルト値をセット
                    $aryAddResultData[1] = $aryTypeSet[0]['DEFAULT_VALUE'];
                    //タイプIDをセット
                    $typeId = $aryTypeSet[0]['TYPE_ID'];
                    if($typeId == ""){
                        //TYPE_IDが空の場合$arydataSetを空のままreturn
                        $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet,$aryAddResultData);
                        return $retArray;
                    }
    
                    //タイプ管理テーブルから、MEMBER_VARS_FLAGとASSIGN_SEQ_FLAGを取得
                    $strQuery = "SELECT "
                                ." TAB_A.MEMBER_VARS_FLAG MEMBER_VARS_FLAG"
                                .",TAB_A.ASSIGN_SEQ_FLAG ASSIGN_SEQ_FLAG "
                                ."FROM "
                                ." B_TERRAFORM_TYPES_MASTER TAB_A "
                                ."WHERE "
                                ." TAB_A.DISUSE_FLAG = ('0') "
                                ." AND TAB_A.TYPE_ID = :TYPE_ID ";
                    $aryForBind['TYPE_ID'] = $typeId;
    
                    $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
    
                    if( $aryRetBody[0] === true ){
                        $objQuery = $aryRetBody[1];
    
                        while($row = $objQuery->resultFetch()){
                            $aryTypeMaster[] = $row;
                        }
                        unset($objQuery);
                        $retBool = true;
                    }else{
                        $intErrorType = 500;
                        $intRowLength = -1;
                    }
    
                    //各フラグをセット
                    $memberVarsFlg = $aryTypeMaster[0]['MEMBER_VARS_FLAG'];
                    $assignSeqFlg  = $aryTypeMaster[0]['ASSIGN_SEQ_FLAG'];
                    if(0 == $memberVarsFlg &&  0 == $assignSeqFlg){
                        $aryAddResultData[0] = "NO_FLAG_VAL";
                    }
                    elseif(1 == $memberVarsFlg &&  0 == $assignSeqFlg){
                        $aryAddResultData[0]  = "MEMBER_FLAG_VAL";
                    }
                    elseif(0 == $memberVarsFlg &&  1 == $assignSeqFlg){
                        $aryAddResultData[0] = "ASSIGN_FLAG_VAL";
                    }
                    elseif(1 == $memberVarsFlg &&  1 == $assignSeqFlg){
                        $aryAddResultData[0]  = "MEMBER_FLAG_VAL";
                    }
                    else{
                        $intErrorType = 501;
                    }
                }else{
                    $intErrrorType = 502;
                }
        }
        $type_Flg = $aryAddResultData[0];

        unset($aryRetBody);
        unset($strQuery);
        unset($aryForBind);


        if( !strlen($strMemberVarsId) ){
            if($strhclId == 2){
                $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody03;
            }else{
                if($type_Flg == "MEMBER_FLAG_VAL" ||  $type_Flg == "FLAG_VAL"){
                    $strOptionBodies = makeSelectOption($arraySelectElement, $data, $tmpBoolWhiteKeyAdd, "", true);
                }else{
                    $strNoOptionMessageText = $strHiddenInputBody.$strMsgBody01;
                }
            }    
        }else{
            $strOptionBodies = makeSelectOption($arraySelectElement, $data, $tmpBoolWhiteKeyAdd, "", true);
        }

        $aryRetBody['optionBodies'] = $strOptionBodies;
        $aryRetBody['NoOptionMessageText'] = $strNoOptionMessageText;
        $retArray = array($aryRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
        return $retArray;


    };

    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したMEMBER_VARSを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global    $g;
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
            if(array_key_exists('MEMBER_VARS_UPDATE_VALUE',$g)){
                $exeQueryData[$objColumn->getID()] = $g['MEMBER_VARS_UPDATE_VALUE'];
            }
        }else if( $modeValue=="DTUP_singleRecDelete" ){
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };

    //----メンバー変数名
    $c = new IDColumn('MEMBER_VARS',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109170"),'D_TERRAFORM_VAR_MEMBER','CHILD_MEMBER_VARS_ID','CHILD_MEMBER_VARS_NEST');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109180"));//エクセル・ヘッダでの説明
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);


    $strSetInnerText = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109305");
    $objVarBFmtUpd = new SelectTabBFmt();
    $objVarBFmtUpd->setFADJsEvent('onChange','member_vars_upd'); 
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);
    $objVarBFmtUpd->setFunctionForGetFADMainDataOverride($objFunction04);
    $objVarBFmtUpd->setFunctionForGetMainDataOverride($objFunction05);
    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setFADJsEvent('onChange','member_vars_reg'); 
    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);
    $objVarBFmtReg->setFunctionForGetFADMainDataOverride($objFunction04);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

    $c->setEvent('update_table', 'onchange', 'member_vars_upd');
    $c->setEvent('register_table', 'onchange', 'member_vars_reg');

    $c->setJournalTableOfMaster('D_TERRAFORM_VAR_MEMBER_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('CHILD_MEMBER_VARS_ID');
    $c->setJournalDispIDOfMaster('CHILD_MEMBER_VARS_NEST');

    $c->setRequired(false);//登録/更新時には、入力必須
    $c->setRequiredMark(true);//必須マークのみ付与

    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // データベース更新前のファンクション登録
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->addColumn($c);
    
    unset($tmpObjFunction);

    unset($objFunction01);
    unset($objFunction02);
    unset($objFunction03);
    unset($objFunction04);
    unset($objFunction05);

    // REST/excel/csv入力用 メンバー変数名
    $c = new IDColumn('REST_MEMBER_VARS',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109170"),'E_TERRAFORM_VAR_MEMBER_LIST','CHILD_MEMBER_VARS_ID','VAR_MEMBER_PULLDOWN','',array('OrderByThirdColumn'=>'CHILD_MEMBER_VARS_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109180"));

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

    $c->setJournalTableOfMaster('E_TERRAFORM_VAR_MEMBER_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('CHILD_MEMBER_VARS_ID');
    $c->setJournalDispIDOfMaster('VAR_MEMBER_PULLDOWN');
    //登録/更新時には、必須でない
    $c->setRequired(false);
    $c->setRequiredMark(true);//必須マークのみ付与

    $table->addColumn($c);
    
    //メンバー変数名----


    //************************************************************************************
    //----代入順序
    //************************************************************************************
    $objFunction01 = function($strTagInnerBody,$objCellFormatter,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite){
        global $g;
        $aryTypeSet = array();

        //メッセージ
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109306");
        $strMsgBody02 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109307");
        $strMsgBody03 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109308");

        $assignSeq = null; //入力されている代入順序の値
        if(is_array($rowData) && array_key_exists('ASSIGN_SEQ', $rowData)){
            $assignSeq = $rowData['ASSIGN_SEQ'];
        }
        $strModuleVarsLinkId = $rowData['MODULE_VARS_LINK_ID']; //選択されている変数名のID
        if(is_array($rowData) && array_key_exists('MODULE_VARS_LINK_ID', $rowData)){
            $strModuleVarsLinkId = $rowData['MODULE_VARS_LINK_ID'];
        }
        $strMemberVarsId = $rowData['MEMBER_VARS']; //選択されているメンバ変数のID
        if(is_array($rowData) && array_key_exists('MEMBER_VARS', $rowData)){
            $strMemberVarsId = $rowData['MEMBER_VARS'];
        }
        $strHclID = $rowData['HCL_FLAG'];
        if(is_array($rowData) && array_key_exists('HCL_FLAG', $rowData)){
            $strHclID = $rowData['HCL_FLAG'];
        }
        $pattern = "input"; //デフォルトのパターン

        if(!$assignSeq){
            //ASSIGN_SEQがセットされていない場合、「変数名」「メンバ変数名」のタイプから表示状態を決める
            if( 0 < strlen($strModuleVarsLinkId) ){
                $aryTypeSet = array();
                $strFxName = "";
                if(0 < strlen($strMemberVarsId)){
                    //選択されているメンバ変数のタイプを取得
                    $strQuery = "SELECT "
                            ." TAB_A.CHILD_VARS_TYPE_ID TYPE_ID "
                            .",TAB_A.ACCESS_AUTH ACCESS_AUTH "
                            ."FROM "
                            ." D_TERRAFORM_VAR_MEMBER TAB_A "
                            ."WHERE "
                            ." TAB_A.DISUSE_FLAG = ('0') "
                            ." AND TAB_A.VARS_ASSIGN_FLAG = ('1') "
                            ." AND TAB_A.CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID ";
                    $aryForBind['CHILD_MEMBER_VARS_ID'] = $strMemberVarsId;
                }else{
                    //選択されている変数名のタイプを取得
                    $strQuery = "SELECT "
                            ." TAB_A.TYPE_ID TYPE_ID "
                            .",TAB_A.ACCESS_AUTH ACCESS_AUTH "
                            ."FROM "
                            ." B_TERRAFORM_MODULE_VARS_LINK TAB_A "
                            ."WHERE "
                            ." TAB_A.DISUSE_FLAG = ('0') "
                            ." AND TAB_A.MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";
                    $aryForBind['MODULE_VARS_LINK_ID'] = $strModuleVarsLinkId;
                }

                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    $intErrorType = 500;
                    $retBool = false;
                }

                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];
                    while($row = $objQuery->resultFetch() ){
                        // レコード毎のアクセス権を判定
                        list($ret,$permission) = $obj->chkOneRecodeMultiAccessPermission($row);
                        if($ret === false) {
                            $intErrorType = 500;
                            $retBool = false;
                        }else{
                            if($permission === true){
                                $aryTypeSet[]= $row;
                            }
                        }
                    }
                    unset($aryForBind);
                    unset($objQuery);
                    $retBool = true;
                }else{
                    $intErrorType = 500;
                    $intRowLength = -1;
                }

                if(count($aryTypeSet) == 1){
                    //タイプIDをセット
                    $typeId = $aryTypeSet[0]['TYPE_ID'];
                    if($typeId != ""){
                        $aryTypeMaster = array();
                        //タイプ管理テーブルから、MEMBER_VARS_FLAGとASSIGN_SEQ_FLAGを取得
                        $strQuery = "SELECT "
                                    ."TAB_A.ASSIGN_SEQ_FLAG ASSIGN_SEQ_FLAG "
                                    ."FROM "
                                    ." B_TERRAFORM_TYPES_MASTER TAB_A "
                                    ."WHERE "
                                    ." TAB_A.DISUSE_FLAG = ('0') "
                                    ." AND TAB_A.TYPE_ID = :TYPE_ID ";
                        $aryForBind['TYPE_ID'] = $typeId;
        
                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
        
                        if( $aryRetBody[0] === true ){
                            $objQuery = $aryRetBody[1];
        
                            while($row = $objQuery->resultFetch()){
                                $aryTypeMaster[] = $row;
                            }
                            unset($objQuery);
                            $retBool = true;
                        }else{
                            $intErrorType = 500;
                            $intRowLength = -1;
                        }

                        $assignSeqFlg  = $aryTypeMaster[0]['ASSIGN_SEQ_FLAG'];
                        if($assignSeqFlg == 1){
                            //代入順序の入力フラグが1の場合、入力欄を表示
                            $pattern = "input";
                        }else{
                            //フラグが1ではない(0)の場合「入力不要」メッセージ
                            $pattern = "noRequired";
                        }

                    }else{
                        //TYPE_IDが空の場合「入力不要」メッセージ
                        $pattern = "noRequired";
                    }
                }else{
                    //レコードが無い場合「変数名を選択してください」メッセージ
                    $pattern = "noSelectVars";
                }
            }else{
                //「変数名を選択してください」メッセージ
                $pattern = "noSelectVars";
            }
        }else{
            //ASSIGN_SEQがセットされている場合、入力欄を表示
            $pattern = "input";
        }

        if($strHclID == 2){
            $pattern = "noneVars";
        };

        //$patternを元にBodyを生成
        switch($pattern){
            case "input":
                $retBody = "<input style=\"\" {$objCellFormatter->printAttrs($aryAddOnDefault,$aryOverWrite)} {$objCellFormatter->printJsAttrs($rowData)} {$objCellFormatter->getTextTagLastAttr()}>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg1_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody01."</div>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg2_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody02."</div>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg3_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody03."</div>";
                break;

            case "noSelectVars":
                $retBody = "<input style=\"display:none\" {$objCellFormatter->printAttrs($aryAddOnDefault,$aryOverWrite)} {$objCellFormatter->printJsAttrs($rowData)} {$objCellFormatter->getTextTagLastAttr()}>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg1_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody01."</div>";
                $retBody = $retBody."<div style=\"\" id=\"msg2_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody02."</div>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg3_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody03."</div>";
                break;

            case "noRequired":
                $retBody = "<input style=\"display:none\" {$objCellFormatter->printAttrs($aryAddOnDefault,$aryOverWrite)} {$objCellFormatter->printJsAttrs($rowData)} {$objCellFormatter->getTextTagLastAttr()}>";
                $retBody = $retBody."<div style=\"\" id=\"msg1_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody01."</div>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg2_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody02."</div>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg3_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody03."</div>";
                break;

            case "noneVars":
                $retBody = "<input style=\"display:none\" {$objCellFormatter->printAttrs($aryAddOnDefault,$aryOverWrite)} {$objCellFormatter->printJsAttrs($rowData)} {$objCellFormatter->getTextTagLastAttr()}>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg1_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody01."</div>";
                $retBody = $retBody."<div style=\"display:none\" id=\"msg2_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody02."</div>";
                $retBody = $retBody."<div style=\"\" id=\"msg3_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody03."</div>";
                break;
        }
        return $retBody;
    };

    $objFunction02 = function($strTagInnerBody,$objCellFormatter,$rowData,$aryVariant,$aryAddOnDefault,$aryOverWrite){
        global $g;

        //メッセージ
        $strMsgBody01 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109309");
        $strMsgBody02 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109310");
        $strMsgBody03 = $g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109311");

        //「登録」パターン
        $retBody = "<input style=\"display:none\" {$objCellFormatter->printAttrs($aryAddOnDefault,$aryOverWrite)} {$objCellFormatter->printJsAttrs($rowData)} {$objCellFormatter->getTextTagLastAttr()}>";
        $retBody = $retBody."<div style=\"display:none\" id=\"msg1_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody01."</div>";
        $retBody = $retBody."<div style=\"\" id=\"msg2_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody02."</div>";
        $retBody = $retBody."<div style=\"display:none\" id=\"msg3_".$objCellFormatter->getFSTIDForIdentify()."\">".$strMsgBody03."</div>";

        return $retBody;
    };


    $objVarBFmtUpd = new NumInputTabBFmt(0,false);
    $objVarBFmtUpd->setFunctionForReturnOverrideGetData($objFunction01);
    $objVarBFmtReg = new NumInputTabBFmt(0,false);
    $objVarBFmtReg->setFunctionForReturnOverrideGetData($objFunction02);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);

    //----代入順序
    $c = new NumColumn('ASSIGN_SEQ',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109312"));    
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109313"));//エクセル・ヘッダでの説明
    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg); 
    $c->setSubtotalFlag(false);
    // 必須チェックは組合せバリデータで行う。
    $c->setRequired(false);//登録/更新時には、入力必須

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);
    $c->setValidator(new IntNumValidator(1,null));

    $table->addColumn($c);


    //代入順序----


    //************************************************************************************
    //----デフォルト値
    //************************************************************************************
    $c = new Column('DEFAULT_VALUE',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109314"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-109315"));//エクセル・ヘッダでの説明
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



    //************************************************************************************
    //----Sensitive設定
    //************************************************************************************
    $c = new IDColumn('SENSITIVE_FLAG',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103760"), 'B_SENSITIVE_FLAG', 'VARS_SENSITIVE', 'VARS_SENSITIVE_SELECT', '', array('SELECT_ADD_FOR_ORDER'=>array('VARS_SENSITIVE'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103770")); //エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('B_SENSITIVE_FLAG_JNL');
    $c->setDefaultValue("register_table", 1); //デフォルト値で1(OFF)
    $c->setRequired(true); //登録/更新時には、入力必須
    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('SENSITIVE_FLAG');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_SENSITIVE_FLAG_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'VARS_SENSITIVE',
        'TTT_GET_TARGET_COLUMN_ID'=>'VARS_SENSITIVE_SELECT',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);

    $table->addColumn($c);

    //************************************************************************************
    //----具体値
    //************************************************************************************
    $objVldt = new MultiTextValidator(0,8192,false);
    $c = new SensitiveMultiTextColumn('VARS_ENTRY',$g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103740"), 'SENSITIVE_FLAG');
    $c->setDescription($g['objMTS']->getSomeMessage("ITATERRAFORM-MNU-103750"));//エクセル・ヘッダでの説明
    $c->setValidator($objVldt);
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
            if( $modeValue=="DTUP_singleRecDelete" ){
                // 廃止の場合のみ
                $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
                if( $modeValue_sub == "on" ){

                    $strQuery = "UPDATE A_PROC_LOADED_LIST "
                               ."SET LOADED_FLG='0' ,LAST_UPDATE_TIMESTAMP = NOW(6) "
                               ."WHERE ROW_ID IN (2100080002) ";

                    $aryForBind = array();

                    $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                    if( $aryRetBody[0] !== true ){
                        $boolRet = false;
                        $strErrMsg = $aryRetBody[2];
                        $intErrorType = 500;
                    }
                }
            }
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['ASSIGN_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);


//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('OPERATION_NO_UAPK','PATTERN_ID','MODULE_VARS_LINK_ID','HCL_FLAG','MEMBER_VARS','ASSIGN_SEQ'));
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

        $pattan_tbl         = "E_TERRAFORM_PATTERN";      // モード毎

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
            $intVarsLinkId          = isset($arrayVariant['edit_target_row']['MODULE_VARS_LINK_ID'])?
                                        $arrayVariant['edit_target_row']['MODULE_VARS_LINK_ID']:null;
            $intRestVarsLinkId      = isset($arrayVariant['edit_target_row']['REST_MODULE_VARS_LINK_ID'])?
                                        $arrayVariant['edit_target_row']['REST_MODULE_VARS_LINK_ID']:null;
            $intMemberVarsId        = isset($arrayVariant['edit_target_row']['MEMBER_VARS'])?
                                        $arrayVariant['edit_target_row']['MEMBER_VARS']:null;
            $intAssignSeqId         = isset($arrayVariant['edit_target_row']['ASSIGN_SEQ'])?
                                        $arrayVariant['edit_target_row']['ASSIGN_SEQ']:null;
            $intHclId               = isset($arrayVariant['edit_target_row']['HCL_FLAG'])?
                                        $arrayVariant['edit_target_row']['HCL_FLAG']:null;
            $intSensitiveId         = isset($arrayVariant['edit_target_row']['SENSITIVE_FLAG'])?
                                        $arrayVariant['edit_target_row']['SENSITIVE_FLAG']:null;
            $intRestMemberVarsId    = isset($arrayVariant['edit_target_row']['REST_MEMBER_VARS'])?
                                        $arrayVariant['edit_target_row']['REST_MEMBER_VARS']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                if( strlen($intOperationNoUAPK) === 0 || strlen($intPatternId) === 0 || strlen($intVarsLinkId) === 0 ){
                    $boolSystemErrorFlag = true;
                }
                //復活の場合----

                $columnId = $strNumberForRI;

            }
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $intOperationNoUAPK       = array_key_exists('OPERATION_NO_UAPK',$arrayRegData)?
                                           $arrayRegData['OPERATION_NO_UAPK']:null;
            $intPatternId             = array_key_exists('PATTERN_ID',$arrayRegData)?
                                           $arrayRegData['PATTERN_ID']:null;
            $intVarsLinkId            = array_key_exists('MODULE_VARS_LINK_ID',$arrayRegData)?
                                           $arrayRegData['MODULE_VARS_LINK_ID']:null;
            $intRestVarsLinkId        = array_key_exists('REST_MODULE_VARS_LINK_ID',$arrayRegData)?
                                           $arrayRegData['REST_MODULE_VARS_LINK_ID']:null;
            $intVarsEntry             = array_key_exists('VARS_ENTRY',$arrayRegData)?
                                           $arrayRegData['VARS_ENTRY']:null;
            $intMemberVarsId          = array_key_exists('MEMBER_VARS',$arrayRegData)?
                                           $arrayRegData['MEMBER_VARS']:null;
            $intAssignSeqId           = array_key_exists('ASSIGN_SEQ',$arrayRegData)?
                                           $arrayRegData['ASSIGN_SEQ']:null;
            $intHclId                 = array_key_exists('HCL_FLAG',$arrayRegData)?
                                           $arrayRegData['HCL_FLAG']:null;
            $intSensitiveId           = array_key_exists('SENSITIVE_FLAG',$arrayRegData)?
                                           $arrayRegData['SENSITIVE_FLAG']:null;
            $intRestMemberVarsId      = array_key_exists('REST_MEMBER_VARS',$arrayRegData)?
                                           $arrayRegData['REST_MEMBER_VARS']:null;
            // 主キーの値を取得する。
            if( $strModeId == "DTUP_singleRecUpdate" ){
                // 更新処理の場合
                $columnId = $strNumberForRI;
            }
            else{
                // 登録処理の場合
                $columnId = array_key_exists('ASSIGN_ID',$arrayRegData)?$arrayRegData['ASSIGN_ID']:null;
            }

        }

        $g['PATTERN_ID_UPDATE_VALUE']        = "";
        $g['MODULE_VARS_LINK_ID_UPDATE_VALUE']      = "";
        $rest_call = false;
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // PATTERN_ID;未設定 MODULE_VARS_LINK_ID:未設定 REST_MODULE_VARS_LINK_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($intPatternId)          === 0) &&
               (strlen($intVarsLinkId)         === 0) &&
               (strlen($intRestVarsLinkId)     !== 0)){
                $rest_call = true;
                $query =  "SELECT                                             "
                         ."  TBL_A.MODULE_PTN_LINK_ID,                        "
                         ."  TBL_A.MODULE_VARS_LINK_ID,                              "
                         ."  TBL_A.PATTERN_ID,                                "
                         ."  COUNT(*) AS MODULE_VARS_LINK_ID_CNT                     "
                         ."FROM                                               "
                         ."  E_TERRAFORM_PTN_VAR_LIST TBL_A                     "      //モード毎
                         ."WHERE                                              "
                         ."  TBL_A.MODULE_PTN_LINK_ID    = :MODULE_PTN_LINK_ID   AND      "
                         ."  TBL_A.DISUSE_FLAG     = '0'                      ";
                $aryForBind = array();
                $aryForBind['MODULE_PTN_LINK_ID'] = $intRestVarsLinkId;
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intCount = 0;
                    $row = $objQuery->resultFetch();
                    if( $row['MODULE_VARS_LINK_ID_CNT'] == '1' ){
                        $intVarsLinkId                     = $row['MODULE_VARS_LINK_ID'];
                        $intPatternId                      = $row['PATTERN_ID'];
                        $g['PATTERN_ID_UPDATE_VALUE']      = $intPatternId;
                        $g['MODULE_VARS_LINK_ID_UPDATE_VALUE']    = $intVarsLinkId;
                    }else if( $row['MODULE_VARS_LINK_ID_CNT'] == '0' ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201010");
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

        if($rest_call === true){
            $intMemberVarsId  = $intRestMemberVarsId;
            $g['MEMBER_VARS_UPDATE_VALUE'] = $intMemberVarsId;
        }

        //----必須入力チェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( strlen($intPatternId) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201020");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($intVarsLinkId) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201030");
                $boolExecuteContinue = false;
                $retBool = false;
            }
        }
        //必須入力チェック----

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
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201040");
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

        if( $boolExecuteContinue === true ){
            $retBool = false;
            $query = "SELECT "
                    ." COUNT(*) REC_COUNT "
                    ."FROM "
                    ." D_TERRAFORM_PTN_VARS_LINK_VFP TAB_1 "
                    ."WHERE "
                    ." TAB_1.DISUSE_FLAG = '0' "
                    ."AND TAB_1.PATTERN_ID = :PATTERN_ID "
                    ."AND TAB_1.MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";

            //$aryForBind['OPERATION_NO_UAPK'] = $intOperationNoUAPK;
            $aryForBind['PATTERN_ID'] = $intPatternId;
            $aryForBind['MODULE_VARS_LINK_ID'] = $intVarsLinkId;

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
                    $boolExecuteContinue = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201050");
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
            }else{
                web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                $boolSystemErrorFlag = true;
            }
        }
        //HCL設定ONの場合の組み合わせバリデーション----
        if( $boolExecuteContinue === true ){
            if($intHclId == 1){
                $retBool = true;
            }elseif($intHclId == 2){
                if(0 < strlen($intMemberVarsId) || 0 < strlen($intAssignSeqId)){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201070");
                    $boolExecuteContinue = false;
                    $retBool = false;
                }
            }else{
                web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                $boolSystemErrorFlag = true;
            }
        }

        //HCL設定ONの場合の重複チェック----
        if( $boolExecuteContinue === true ){
            $query = "SELECT "
                    ."HCL_FLAG "
                    .",SENSITIVE_FLAG "
                    ."FROM "
                    ."D_TERRAFORM_VARS_ASSIGN "
                    ."WHERE "
                    ."DISUSE_FLAG = '0' "
                    ."AND ASSIGN_ID   <> :ASSIGN_ID "
                    ."AND OPERATION_NO_UAPK   = :OPERATION_NO_UAPK "
                    ."AND PATTERN_ID          = :PATTERN_ID "
                    ."AND MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";

            $aryForBind = array();
            $aryForBind['OPERATION_NO_UAPK'] = $intOperationNoUAPK;
            $aryForBind['PATTERN_ID'] = $intPatternId;                
            $aryForBind['MODULE_VARS_LINK_ID'] = $intVarsLinkId;
            $aryForBind['ASSIGN_ID']    = $columnId;

            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
            
            if( $retArray[0] === true ){
                $objQuery = $retArray[1];
                $hcltypearray = [];
                $sensitivetypearray = [];
                while($row = $objQuery->resultFetch() ){
                    $hcltypearray[] = $row['HCL_FLAG'];
                    $sensitivetypearray[] = $row['SENSITIVE_FLAG'];
                }
                //HCL設定の混在不可
                if(($intHclId == 1 && in_array(2, $hcltypearray)) || ($intHclId == 2 && in_array(1, $hcltypearray))){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201080");
                    $boolExecuteContinue = false;
                    $retBool = false;
                }

                // Sensitive設定の統一
                if($intSensitiveId == 1 && in_array(2,$sensitivetypearray) || $intSensitiveId == 2 && in_array(1,$sensitivetypearray)){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201110");
                    $boolExecuteContinue = false;
                    $retBool = false;
                }
            }else{
                web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                $boolSystemErrorFlag = true;
            }
            unset($objQuery);
        }

        //変数でのメンバー変数、代入順序表示有無-------------------------------------------------------------------------
        if( $boolExecuteContinue === true && $intHclId == 1){
            $retBool = false;
            $boolExecuteContinue = false;
            $query = "SELECT "
                    ."TYPE_ID "
                    ."FROM "
                    ."B_TERRAFORM_MODULE_VARS_LINK  "
                    ."WHERE "
                    ."DISUSE_FLAG = '0' "
                    ."AND MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";

            $aryForBind = array();
            $aryForBind['MODULE_VARS_LINK_ID'] = $intVarsLinkId;

            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
            
            if( $retArray[0] === true ){
                $objQuery =& $retArray[1];
                $intCount = 0;
                $aryDiscover = array();
                $row = $objQuery->resultFetch();
                unset($objQuery);
                if(isset($row['TYPE_ID'])){
                  $typeId = $row['TYPE_ID'];
                }else{
                  $typeId = "";
                }
                
                if(0 < strlen($typeId)){
                    $query = "SELECT "
                    ."MEMBER_VARS_FLAG  "
                    .",ASSIGN_SEQ_FLAG  "
                    ."FROM "
                    ."B_TERRAFORM_TYPES_MASTER  "
                    ."WHERE "
                    ."DISUSE_FLAG = '0' "
                    ."AND TYPE_ID = :TYPE_ID ";

                    $aryForBind = array();
                    $aryForBind['TYPE_ID'] = $typeId;
                    $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $intCount = 0;
                        $aryDiscover = array();
                        $typeRow = $objQuery->resultFetch();

                        $member_vars_flg = $typeRow['MEMBER_VARS_FLAG'];
                        $assign_seq_flg = $typeRow['ASSIGN_SEQ_FLAG'];
                        if($member_vars_flg == 0 && $assign_seq_flg == 0){
                            if(!strlen($intMemberVarsId) && !strlen($intAssignSeqId)){
                                $retBool = true;
                                $boolExecuteContinue = true;
                            }else{
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201120");
                            }

                            if($typeId == 7 && $intHclId == 1){
                                $retBool = false;
                                $boolExecuteContinue = false;
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201220");
                            }
                        }elseif($member_vars_flg == 1 && $assign_seq_flg == 0 ){

                            if(0 < strlen($intMemberVarsId)){
                                $retBool = true;
                                $boolExecuteContinue = true;
                            }elseif(0 < strlen($intAssignSeqId) && 1 > strlen($intMemberVarsId)){
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201130");
                            }elseif(1 >strlen($intMemberVarsId)){
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201140");
                            }
                        }elseif($member_vars_flg == 0 && $assign_seq_flg == 1){
                            if( 0 < strlen($intAssignSeqId) && !strlen($intMemberVarsId)){
                                $retBool = true;
                                $boolExecuteContinue = true;
                            }elseif(1 > strlen($intAssignSeqId)){
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201150");
                            }elseif(0 < strlen($intMemberVarsId)){ 
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201160");
                            }
                        }elseif($member_vars_flg == 1 && $assign_seq_flg == 1){
                            if( strlen($intMemberVarsId)){
                                $retBool = true;
                                $boolExecuteContinue = true;
                            }else{
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201210");
                            }
                        }
                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $boolSystemErrorFlag = true;
                    }
                    unset($objQuery);
                }elseif(1 > strlen($intMemberVarsId) && 1 > strlen($intAssignSeqId) ){
                    $retBool = true;
                }elseif(0 < strlen($intMemberVarsId) || 0 < strlen($intAssignSeqId)){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201170");
                    $retBool = false;
                    $boolExecuteContinue = false;
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
            }
        }

        //変数名、メンバー変数の組み合わせチェック
        if( $boolExecuteContinue === true && $intHclId == 1){
            if( 0 < strlen($intMemberVarsId)){
                $retBool = false;
                $query = "SELECT "
                        ." COUNT(*) REC_COUNT "
                        ."FROM "
                        ." D_TERRAFORM_VAR_MEMBER  "
                        ."WHERE "
                        ." DISUSE_FLAG = '0' "
                        ." AND VARS_ASSIGN_FLAG = '1' "
                        ."AND PARENT_VARS_ID = :PARENT_VARS_ID "
                        ."AND CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID ";
    
                $aryForBind = array();
                $aryForBind['PARENT_VARS_ID'] = $intVarsLinkId;
                $aryForBind['CHILD_MEMBER_VARS_ID'] = $intMemberVarsId;

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
                        $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201060");
                        $retBool = false;
                        $boolExecuteContinue = false;
                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $boolSystemErrorFlag = true;
                    }
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
            }
        }

        // メンバー変数での代入順序表示有無
        if( $boolExecuteContinue === true ){
            if( 0 < strlen($intMemberVarsId)){
                $retBool = false;
                $query = "SELECT "
                        ."CHILD_VARS_TYPE_ID "
                        ."FROM "
                        ."D_TERRAFORM_VAR_MEMBER  "
                        ."WHERE "
                        ."DISUSE_FLAG = '0' "
                        ."AND VARS_ASSIGN_FLAG = '1' "
                        ."AND CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID ";
    
                $aryForBind = array();
                $aryForBind['CHILD_MEMBER_VARS_ID'] = $intMemberVarsId;

                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intCount = 0;
                    $aryDiscover = array();
                    $row = $objQuery->resultFetch();

                    unset($objQuery);
                    unset($aryForBind);
                    unset($retArray);

                    if( 0 < strlen($row['CHILD_VARS_TYPE_ID'])){
                        $childTypeID = $row['CHILD_VARS_TYPE_ID'];
                        $query = "SELECT "
                                ."ASSIGN_SEQ_FLAG  "
                                ."FROM "
                                ."B_TERRAFORM_TYPES_MASTER  "
                                ."WHERE "
                                ."DISUSE_FLAG = '0' "
                                ."AND TYPE_ID = :TYPE_ID ";
    
                        $aryForBind = array();
                        $aryForBind['TYPE_ID'] = $childTypeID;
                        $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                        if( $retArray[0] === true ){
                            $objQuery =& $retArray[1];
                            $intCount = 0;
                            $aryDiscover = array();
                            $typeRow = $objQuery->resultFetch();

                            $type_Flg = $typeRow['ASSIGN_SEQ_FLAG'];
                            //代入順序入力不可　で代入順序に入力がある場合
                            if($type_Flg == 0 && 1 > strlen($intAssignSeqId)){
                                $retBool = true;
                            }elseif($type_Flg == 1){
                                if( 0 < strlen($intAssignSeqId)){
                                    $retBool = true;
                                }else{
                                    $retBool = false;
                                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201180");
                                }
                            }elseif($type_Flg == 0 && 0 < strlen($intAssignSeqId)){
                                $retBool = false;
                                $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201190");
                            }else{
                                web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                                $boolSystemErrorFlag = true;
                            }
                        }else{
                            web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                            $boolSystemErrorFlag = true;
                        }

                    }
                }elseif(1 > strlen($intAssignSeqId)){
                    $retBool = true;
                }elseif(0 < strlen($intAssignSeqId)){
                    $retBool = false;
                    $retStrBody = $g['objMTS']->getSomeMessage("ITATERRAFORM-ERR-201200");
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;

                }
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
