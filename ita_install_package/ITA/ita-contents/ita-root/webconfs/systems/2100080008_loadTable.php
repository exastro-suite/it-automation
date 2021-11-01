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

        $strPatternIdNumeric = $rowData['PATTERN_ID'];

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
    $objVarBFmtUpd->setNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtUpd->setFunctionForGetSelectList($objFunction03);

    $objOTForUpd = new OutputType(new ReqTabHFmt(), $objVarBFmtUpd);
    $objOTForUpd->setFunctionForGetFADSelectList($objFunction01);

    $objVarBFmtReg = new SelectTabBFmt();
    $objVarBFmtReg->setSelectWaitingText($strSetInnerText);
    $objVarBFmtReg->setFADNoOptionMessageText($strSetInnerText);
    $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);
    $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
    $objOTForReg->setFunctionForGetFADSelectList($objFunction02);

    $c->setOutputType('update_table',$objOTForUpd);
    $c->setOutputType('register_table',$objOTForReg);

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

    // REST/excel/csvで項目無効
    $c->getOutputType('excel')->setVisible(false);
    $c->getOutputType('csv')->setVisible(false);
    $c->getOutputType('json')->setVisible(false);

    // データベース更新前のファンクション登録
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

    $table->addColumn($c);
    //変数名----

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
    $table->addUniqueColumnSet(array('OPERATION_NO_UAPK','PATTERN_ID','MODULE_VARS_LINK_ID'));
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
            $intOperationNoUAPK = isset($arrayVariant['edit_target_row']['OPERATION_NO_UAPK'])?
                                        $arrayVariant['edit_target_row']['OPERATION_NO_UAPK']:null;
            $intPatternId       = isset($arrayVariant['edit_target_row']['PATTERN_ID'])?
                                        $arrayVariant['edit_target_row']['PATTERN_ID']:null;
            $intVarsLinkId      = isset($arrayVariant['edit_target_row']['MODULE_VARS_LINK_ID'])?
                                        $arrayVariant['edit_target_row']['MODULE_VARS_LINK_ID']:null;
            $intRestVarsLinkId  = isset($arrayVariant['edit_target_row']['REST_MODULE_VARS_LINK_ID'])?
                                        $arrayVariant['edit_target_row']['REST_MODULE_VARS_LINK_ID']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                if( strlen($intOperationNoUAPK) === 0 || strlen($intPatternId) === 0 || strlen($intVarsLinkId) === 0 ){
                    $boolSystemErrorFlag = true;
                }
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

        }

        $g['PATTERN_ID_UPDATE_VALUE']        = "";
        $g['MODULE_VARS_LINK_ID_UPDATE_VALUE']      = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // PATTERN_ID;未設定 MODULE_VARS_LINK_ID:未設定 REST_MODULE_VARS_LINK_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($intPatternId)          === 0) &&
               (strlen($intVarsLinkId)         === 0) &&
               (strlen($intRestVarsLinkId)     !== 0)){
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
