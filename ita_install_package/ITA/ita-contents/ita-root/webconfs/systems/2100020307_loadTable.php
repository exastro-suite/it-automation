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
//    ・Ansible（Legacy Role）作業パターン詳細 
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207080");
/*
Ansible（Legacy Role）作業パターン詳細
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

    $table = new TableControlAgent('D_B_ANSIBLE_LRL_PATTERN_LINK','LINK_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1207090"), 'D_B_ANSIBLE_LRL_PATTERN_LINK_JNL', $tmpAry);

    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['LINK_ID']->setSequenceID('B_ANSIBLE_LRL_PATTERN_LINK_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_ANSIBLE_LRL_PATTERN_LINK_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_ANSIBLE_LRL_PATTERN_LINK');
    $table->setDBJournalTableHiddenID('B_ANSIBLE_LRL_PATTERN_LINK_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    $table->setJsEventNamePrefix(true);
    
    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208010"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208020"));

    //---- 検索機能の制御
    $table->setGeneObject('AutoSearchStart',true);  //('',true,false)
    // 検索機能の制御----


    $c = new IDColumn('PATTERN_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208030"),'E_ANSIBLE_LRL_PATTERN','PATTERN_ID','PATTERN','',array('OrderByThirdColumn'=>'PATTERN_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208040"));//エクセル・ヘッダでの説明

    $c->setRequired(true);//登録/更新時には、入力必須

    //コンテンツのソースがヴューの場合、登録/更新の対象とする
    $c->setHiddenMainTableColumn(true);

    $table->addColumn($c);

    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したROLE_PACKAGE_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['ROLE_PACKAGE_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['ROLE_PACKAGE_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };

    $c = new IDColumn('ROLE_PACKAGE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208045"),
             'D_ANSIBLE_LRL_ROLE_LIST',
             'ROLE_PACKAGE_ID',
             'ROLE_PACKAGE_NAME_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208046"));//エクセル・ヘッダでの説明

    $c->setEvent('update_table', 'onchange', 'package_upd');
    $c->setEvent('register_table', 'onchange', 'package_reg');

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


    // RestAPI/Excel/CSVからの登録の場合に組み合わせバリデータで退避したROLE_IDを設定する。
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['ROLE_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['ROLE_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
    };

    $c = new IDColumn('ROLE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208050"),
             'D_ANSIBLE_LRL_ROLE_LIST',
             'ROLE_ID',
             'ROLE_NAME_PULLDOWN','');
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208060"));//エクセル・ヘッダでの説明
	
    $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
        global $g;
        $retBool = false;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryDataSet = array();

        $strFxName = "";

        $strPackageIdNumeric = $aryVariant['ROLE_PACKAGE_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.ROLE_ID            KEY_COLUMN "
                   .",TAB_1.ROLE_NAME_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." D_ANSIBLE_LRL_ROLE_LIST TAB_1 "
                   ."WHERE "
                   ."     TAB_1.PACKAGE_DISUSE_FLAG  = '0' "
                   ." AND TAB_1.ROLE_DISUSE_FLAG     = '0' "
                   ." AND TAB_1.ROLE_PACKAGE_ID      = :ROLE_PACKAGE_ID "
                   ."ORDER BY KEY_COLUMN ";
                   
        $aryForBind['ROLE_PACKAGE_ID']        = $strPackageIdNumeric;

        if( 0 < strlen($strPackageIdNumeric) ){
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

        $strPackageIdNumeric = $aryVariant['ROLE_PACKAGE_ID'];

        $strQuery = "SELECT "
                   ." TAB_1.ROLE_ID            KEY_COLUMN "
                   .",TAB_1.ROLE_NAME_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." D_ANSIBLE_LRL_ROLE_LIST TAB_1 "
                   ."WHERE "
                   ."     TAB_1.PACKAGE_DISUSE_FLAG  = '0' "
                   ." AND TAB_1.ROLE_DISUSE_FLAG     = '0' "
                   ." AND TAB_1.ROLE_PACKAGE_ID      = :ROLE_PACKAGE_ID "
                   ."ORDER BY KEY_COLUMN ";

        $aryForBind['ROLE_PACKAGE_ID']        = $strPackageIdNumeric;

        if( 0 < strlen($strPackageIdNumeric) ){
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

        $strPackageIdNumeric = $rowData['ROLE_PACKAGE_ID'];
        
        $strQuery = "SELECT "
                   ." TAB_1.ROLE_ID            KEY_COLUMN "
                   .",TAB_1.ROLE_NAME_PULLDOWN DISP_COLUMN "
                   ."FROM "
                   ." D_ANSIBLE_LRL_ROLE_LIST TAB_1 "
                   ."WHERE "
                   ."     TAB_1.PACKAGE_DISUSE_FLAG  = '0' "
                   ." AND TAB_1.ROLE_DISUSE_FLAG     = '0' "
                   ." AND TAB_1.ROLE_PACKAGE_ID      = :ROLE_PACKAGE_ID "
                   ."ORDER BY KEY_COLUMN ";

        $aryForBind['ROLE_PACKAGE_ID']        = $strPackageIdNumeric;

        if( 0 < strlen($strPackageIdNumeric) ){
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

    $strSetInnerText = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1209011");
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

    // REST/excel/csv入力用 ロールパッケージ+ロール
    $c = new IDColumn('REST_ROLE_ID',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208065"),'E_ANS_LRL_PKG_ROLE_LIST','ROLE_ID','ROLE_PACKAGE_PULLDOWN','',array('OrderByThirdColumn'=>'ROLE_ID'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208066"));

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

    //登録/更新時には、必須でない
    $c->setRequired(false);

    $table->addColumn($c);


    $c = new NumColumn('INCLUDE_SEQ',$g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208070"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1208080"));//エクセル・ヘッダでの説明
    $c->setSubtotalFlag(false);
	$c->setValidator(new IntNumValidator(null,null));
    $c->setRequired(true);//登録/更新時には、入力必須

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
                       ."WHERE ROW_ID IN (2100020005) ";

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
    $tmpAryColumn['LINK_ID']->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

//----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('PATTERN_ID','ROLE_ID','INCLUDE_SEQ'));
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

        $pattan_tbl         = "E_ANSIBLE_LRL_PATTERN";
        
        $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        if($strModeId == "DTUP_singleRecDelete"){
            //----更新前のレコードから、各カラムの値を取得
            $rg_link_id                = isset($arrayVariant['edit_target_row']['LINK_ID'])?
                                               $arrayVariant['edit_target_row']['LINK_ID']:null;
            $rg_pattern_id             = isset($arrayVariant['edit_target_row']['PATTERN_ID'])?
                                               $arrayVariant['edit_target_row']['PATTERN_ID']:null;
            $rg_role_package_id        = isset($arrayVariant['edit_target_row']['ROLE_PACKAGE_ID'])?
                                               $arrayVariant['edit_target_row']['ROLE_PACKAGE_ID']:null;
            $rg_role_id                = isset($arrayVariant['edit_target_row']['ROLE_ID'])?
                                               $arrayVariant['edit_target_row']['ROLE_ID']:null;
            $rg_include_seq            = isset($arrayVariant['edit_target_row']['INCLUDE_SEQ'])?
                                               $arrayVariant['edit_target_row']['INCLUDE_SEQ']:null;
            $rg_rest_role_id           = isset($arrayVariant['edit_target_row']['REST_ROLE_ID'])?
                                               $arrayVariant['edit_target_row']['REST_ROLE_ID']:null;

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if( $modeValue_sub == "on" ){
                //----廃止の場合はチェックしない
                $boolExecuteContinue = false;
                //廃止の場合はチェックしない----
            }else{
                //----復活の場合  REST/excelで隠していない必須項目にデータが設定されていることを確認
                //    REST_ROLE_IDはROLE_IDのクローン
                if( strlen($rg_rest_role_id) === 0 || strlen($rg_pattern_id) === 0 ||  strlen($rg_include_seq) === 0 ){
                    $boolSystemErrorFlag = true;
                }
                //復活の場合----

                $columnId = $strNumberForRI;
            }
            //更新前のレコードから、各カラムの値を取得----
        }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
            $rg_link_id                = array_key_exists('LINK_ID',$arrayRegData) ?
                                            $arrayRegData['LINK_ID']:null;
            $rg_pattern_id             = array_key_exists('PATTERN_ID',$arrayRegData) ?
                                            $arrayRegData['PATTERN_ID']:null;
            $rg_role_package_id        = array_key_exists('ROLE_PACKAGE_ID',$arrayRegData) ?
                                            $arrayRegData['ROLE_PACKAGE_ID']:null;
            $rg_role_id                = array_key_exists('ROLE_ID',$arrayRegData) ?
                                            $arrayRegData['ROLE_ID']:null;
            $rg_include_seq            = array_key_exists('INCLUDE_SEQ',$arrayRegData) ?
                                            $arrayRegData['INCLUDE_SEQ']:null;
            $rg_rest_role_id           = array_key_exists('REST_ROLE_ID',$arrayRegData) ?
                                            $arrayRegData['REST_ROLE_ID']:null;

            // 主キーの値を取得する。
            if( $strModeId == "DTUP_singleRecUpdate" ){
                // 更新処理の場合
                $columnId = $strNumberForRI;
            }
            else{
                // 登録処理の場合
                $columnId = array_key_exists('LINK_ID',$arrayRegData)?$arrayRegData['LINK_ID']:null;
            }
        }

        $g['ROLE_PACKAGE_ID_UPDATE_VALUE']        = "";
        $g['ROLE_ID_UPDATE_VALUE']                = "";
        //----呼出元がUIがRestAPI/Excel/CSVかを判定
        // ROLE_PACKAGE_ID;未設定 ROLE_ID:未設定 REST_ROLE_ID:設定 => RestAPI/Excel/CSV
        // その他はUI
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if((strlen($rg_role_package_id)  === 0) && 
               (strlen($rg_role_id)          === 0) &&
               (strlen($rg_rest_role_id)     !== 0)){
                $query =  "SELECT                                             "
                         ."  TBL_A.ROLE_PACKAGE_ID,                           "
                         ."  TBL_A.ROLE_ID,                                   "
                         ."  COUNT(*) AS ROLE_ID_CNT                          "
                         ."FROM                                               "
                         ."  E_ANS_LRL_PKG_ROLE_LIST TBL_A                    "
                         ."WHERE                                              "
                         ."  TBL_A.ROLE_ID         = :ROLE_ID   AND           "
                         ."  TBL_A.DISUSE_FLAG     = '0'                      ";
                $aryForBind = array();
                $aryForBind['ROLE_ID'] = $rg_rest_role_id;
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $intCount = 0;
                    $row = $objQuery->resultFetch();
                    if( $row['ROLE_ID_CNT'] == '1' ){
                        $rg_role_package_id                = $row['ROLE_PACKAGE_ID'];
                        $rg_role_id                        = $row['ROLE_ID'];
                        $g['ROLE_PACKAGE_ID_UPDATE_VALUE'] = $rg_role_package_id;
                        $g['ROLE_ID_UPDATE_VALUE']         = $rg_role_id;
                    }else if( $row['ROLE_ID_CNT'] == '0' ){
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90066");
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
        //呼出元がUIがRestAPI/Excel/CSVかを判定----
        
        //----必須入力チェック
        if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
            if( strlen($rg_role_package_id) === 0 || strlen($rg_role_id) === 0 ) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90068");
                $boolExecuteContinue = false;
                $retBool = false;
            }
            else if( strlen($rg_pattern_id) === 0 ){
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90130");
                $boolExecuteContinue = false;
                $retBool = false;
            }    
            else if( strlen($rg_include_seq) === 0) {
                $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-90069");
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

        //----ロールパッケージとロールの組合せチェック
        if( $boolExecuteContinue === true ){
            $retBool = false;
            $query = "SELECT "
                    ." COUNT(*) REC_COUNT "
                    ."FROM "
                    ." E_ANS_LRL_PKG_ROLE_LIST "
                    ."WHERE "
                    ."    ROLE_PACKAGE_ID      = :ROLE_PACKAGE_ID "
                    ."AND ROLE_ID              = :ROLE_ID ";

            $aryForBind = array();
            $aryForBind['ROLE_PACKAGE_ID'] = $rg_role_package_id;
            $aryForBind['ROLE_ID']         = $rg_role_id;

            $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK1)");
            if( $retArray[0] === true ){
                $objQuery =& $retArray[1];
                $intCount = 0;
                $aryDiscover = array();
                $row = $objQuery->resultFetch();
                unset($objQuery);
                if( $row['REC_COUNT'] == '1' ){
                    $retBool = true;
                }else if( $row['REC_COUNT'] == '0' ){
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAANSIBLEH-MNU-1209012");
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $boolSystemErrorFlag = true;
                }
            }else{
                web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                $boolSystemErrorFlag = true;
            }
        }
        //ロールパッケージとロールの組合せチェック----

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
