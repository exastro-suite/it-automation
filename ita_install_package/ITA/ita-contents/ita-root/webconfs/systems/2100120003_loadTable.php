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
//      CI/CD For IaC 資材紐付け管理
//
//////////////////////////////////////////////////////////////////////

/* ルートディレクトリの取得 */
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}
require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');
require_once ($root_dir_path . '/libs/commonlibs/common_CICD_for_IaC_functions.php');
require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_db_access.php');
require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/table_definition.php');

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030000");

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

    $table = new TableControlAgent('B_CICD_MATERIAL_LINK_LIST','MATL_LINK_ROW_ID', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030001"), 'B_CICD_MATERIAL_LINK_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MATL_LINK_ROW_ID']->setSequenceID('B_CICD_MATERIAL_LINK_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CICD_MATERIAL_LINK_LIST_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定

    /////////////////////////////////////////////////////////
    // 紐付け先資材名 必須入力:true ユニーク:true
    ///////////////////////////////////////////////////////// 
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('MATL_LINK_NAME',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030101"));
    $c->getOutputType('filter_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('register_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->getOutputType('update_table')->setTextTagLastAttr('style = "ime-mode :active"');
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $table->addColumn($c);

    /////////////////////////////////////////////////////////////
    // IaC(From)
    /////////////////////////////////////////////////////////////
    $cg1 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030200"));

        /////////////////////////////////////////////////////////
        // リポジトリ名  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new IDColumn('REPO_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030300"),'B_CICD_REPOSITORY_LIST','REPO_ROW_ID','REPO_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('REPO_NAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030201"));
        $c->setEvent('update_table', 'onchange', 'repo_upd');
        $c->setEvent('register_table', 'onchange', 'repo_reg');

        $c->setRequired(true);
        $cg1->addColumn($c);

        /////////////////////////////////////////////////////////
        // 資材パス名  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new IDColumn('MATL_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030400"),'B_CICD_MATERIAL_LIST','MATL_ROW_ID','MATL_FILE_PATH','', array('SELECT_ADD_FOR_ORDER'=>array('MATL_FILE_PATH'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030401"));


            /////////////////////////////////////////////////////////
            // リポジトリ名と資材パス名のプルダウンリスト連携設定
            /////////////////////////////////////////////////////////
            $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
                global $g;
                $retBool = false;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $aryDataSet = array();
        
                $strFxName = "";

                $RepoRowID = $aryVariant['REPO_ROW_ID'];
        
                // RBAC対応 ----
                $strQuery = "SELECT "
                           ." TAB_1.MATL_ROW_ID     KEY_COLUMN "
                           .",TAB_1.MATL_FILE_PATH  DISP_COLUMN "
                           .",TAB_1.ACCESS_AUTH     ACCESS_AUTH "
                           ."FROM "
                           ." B_CICD_MATERIAL_LIST TAB_1 "
                           ."WHERE "
                           ."     TAB_1.DISUSE_FLAG = '0' "
                           ." AND TAB_1.REPO_ROW_ID = :REPO_ROW_ID "
                           ."ORDER BY KEY_COLUMN ";
        
                $aryForBind['REPO_ROW_ID'] = $RepoRowID;
        
                if( 0 < strlen($RepoRowID) ){
                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    } else {
                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                        if( $aryRetBody[0] === true ){
                            $objQuery = $aryRetBody[1];
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
                            $retBool = false;
                        }
                    }
                }
                // ---- RBAC対応
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

                $RepoRowID = $rowData['REPO_ROW_ID'];

                $strQuery = "SELECT "
                           ." TAB_1.MATL_ROW_ID        KEY_COLUMN "
                           .",TAB_1.MATL_FILE_PATH     DISP_COLUMN "
                           .",TAB_1.ACCESS_AUTH        ACCESS_AUTH "
                           ."FROM "
                           ." B_CICD_MATERIAL_LIST TAB_1 "
                           ."WHERE "
                           ."     TAB_1.DISUSE_FLAG = '0' "
                           ." AND TAB_1.REPO_ROW_ID = :REPO_ROW_ID "
                           ."ORDER BY KEY_COLUMN ";
        
                $aryForBind['REPO_ROW_ID']  = $RepoRowID;
        
                if( 0 < strlen($RepoRowID) ){
                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    } else {
                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                        if( $aryRetBody[0] === true ){
                            $objQuery = $aryRetBody[1];
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
                            $retBool = false;
                        }
                    }
                }
                $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
                return $aryRetBody;
            };
        
            $strSetInnerText = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200035001");
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
        
            //コンテンツのソースがヴューの場合、登録/更新の対象とする
            $c->setHiddenMainTableColumn(true);
        
            //エクセル/CSVからのアップロードを禁止する。
            $c->setAllowSendFromFile(false);

            // REST/excel/csvで項目無効
            $c->getOutputType('excel')->setVisible(false);
            $c->getOutputType('csv')->setVisible(false);
            $c->getOutputType('json')->setVisible(false);
        
        $c->setRequired(true);
        $cg1->addColumn($c);

    $table->addColumn($cg1);

    /////////////////////////////////////////////////////////////
    // IaC(To)
    /////////////////////////////////////////////////////////////
    $cg2 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030500"));

        /////////////////////////////////////////////////////////
        // 資材タイプ  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new IDColumn('MATL_TYPE_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030600"),'B_CICD_MATERIAL_TYPE_NAME','MATL_TYPE_ROW_ID','MATL_TYPE_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('MATL_TYPE_ROW_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030601"));

        $c->setEvent('update_table', 'onchange', 'matl_type_upd');
        $c->setEvent('register_table', 'onchange', 'matl_type_reg');

        $c->setRequired(true);
        $cg2->addColumn($c);

        /////////////////////////////////////////////////////////////
        // テンプレート管理
        /////////////////////////////////////////////////////////////
        $cg3 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032300"));

            /////////////////////////////////////////////////////////
            // 変数定義  必須入力:false ユニーク:false
            /////////////////////////////////////////////////////////
            $objVldt = new MultiTextValidator(0,8192,false);
            $c = new MultiTextColumn('TEMPLATE_FILE_VARS_LIST',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032400"));
            $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032401"));
            $c->setValidator($objVldt);
            $cg3->addColumn($c);

        $cg2->addColumn($cg3);


        /////////////////////////////////////////////////////////////
        // Ansible-Pioneer
        /////////////////////////////////////////////////////////////
        $cg3 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030700"));

            /////////////////////////////////////////////////////////
            // 対話種別  必須入力:false ユニーク:false
            /////////////////////////////////////////////////////////
            $c = new IDColumn('DIALOG_TYPE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030800"),'B_ANSIBLE_PNS_DIALOG_TYPE','DIALOG_TYPE_ID','DIALOG_TYPE_NAME','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030801"));
            $cg3->addColumn($c);

            /////////////////////////////////////////////////////////
            // OS種別  必須入力:false ユニーク:false
            /////////////////////////////////////////////////////////
            $c = new IDColumn('OS_TYPE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030900"),'B_OS_TYPE','OS_TYPE_ID','OS_TYPE_NAME','');
            $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030901"));
            $cg3->addColumn($c);

        $cg2->addColumn($cg3);


        /////////////////////////////////////////////////////////
        // Restユーザー   必須入力:true ユニーク:false
        /////////////////////////////////////////////////////////
        $c = new IDColumn('ACCT_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032000"),'D_CICD_ACCT_LINK','ACCT_ROW_ID','USERNAME','', array('SELECT_ADD_FOR_ORDER'=>array('USERNAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032001"));
        $c->setRequired(true);
        $cg2->addColumn($c);

        /////////////////////////////////////////////////////////
        // アクセス許可ロール   必須入力:true ユニーク:false
        /////////////////////////////////////////////////////////
        $c = new IDColumn('RBAC_FLG_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032200"),'B_CICD_RBAC_FLG_NAME','RBAC_FLG_ROW_ID','RBAC_FLG_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('RBAC_FLG_ROW_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032201"));
        $c->setRequired(true);
        $cg2->addColumn($c);

    $table->addColumn($cg2);

    /////////////////////////////////////////////////////////
    // 素材同期情報
    /////////////////////////////////////////////////////////
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031000"));

        /////////////////////////////////////////////////////////
        // 自動同期   必須入力:false ユニーク:false
        /////////////////////////////////////////////////////////
        $c = new IDColumn('AUTO_SYNC_FLG',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031100"),'B_VALID_INVALID_MASTER','FLAG_ID','FLAG_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031101"));
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 状態  必須入力:false ユニーク:false
        /////////////////////////////////////////////////////////
        // 更新時の初期値設定
        $beforeObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecUpdate" ){
                    if(@count($exeQueryData[$objColumn->getID()]) != 0) {
                        // 更新時、状態が再開以外は空白に設定
                        if($exeQueryData[$objColumn->getID()] != TD_B_CICD_REPO_SYNC_STATUS_NAME::C_SYNC_STATUS_ROW_ID_RESTART) {
                            $exeQueryData[$objColumn->getID()] = "";
                        }
                    }
                }
        };
        // 更新時の履歴初期値設定
        $afterObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $strFxName = basename(__FILE__) . __LINE__;
                        $strQuery = "UPDATE B_CICD_REPOSITORY_LIST "
                                   ."SET SYNC_STATUS_ROW_ID = null "
                                   ."WHERE REPO_ROW_ID = :REPO_ROW_ID";
                        $aryForBind = array('REPO_ROW_ID'=>$aryVariant['edit_target_row']['REPO_ROW_ID']);

                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                        if( $aryRetBody[0] !== true ){
                            $boolRet = false;
                            $strErrMsg = $aryRetBody[2];
                            $intErrorType = 500;
                        }
                        if($boolRet === true) {
                            $strQuery = "UPDATE B_CICD_MATERIAL_LINK_LIST_JNL "
                                       ."SET SYNC_STATUS_ROW_ID = null "
                                       ."WHERE JOURNAL_SEQ_NO = :JOURNAL_SEQ_NO";
                            $aryForBind = array('JOURNAL_SEQ_NO'=>$aryVariant['arySqlExe_delete_table']['JOURNAL_SEQ_NO']['JNL']);

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

        $c = new IDColumn('SYNC_STATUS_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031200"),'B_CICD_REPO_SYNC_STATUS_NAME','SYNC_STATUS_ROW_ID','SYNC_STATUS_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031201"));
        // OutputType一覧  ----
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        //$c->getOutputType('update_table')->setVisible(false);
        //$c->getOutputType('register_table')->setVisible(false);
        //$c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
// comment out
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setFunctionForEvent('afterTableIUDAction',$afterObjFunction);
        $c->setAllowSendFromFile(false);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 詳細情報   必須入力:false ユニーク:false
        /////////////////////////////////////////////////////////
        $objVldt = new MultiTextValidator(0,8192,false);
        $c = new MultiTextColumn('SYNC_ERROR_NOTE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031300"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031301"));
        $c->setValidator($objVldt);
// comment out
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 最終同期日時  表示のみ
        /////////////////////////////////////////////////////////
        $c = new DateTimeColumn('SYNC_LAST_TIME',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031400"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031401"));
        $c->setValidator(new DateTimeValidator(null,null));
// comment out
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $cg->addColumn($c);

        ////////////////////////////////////////////////////
        // 最終更新者ID  必須入力:false ユニーク:false
        ////////////////////////////////////////////////////
        $c = new IDColumn('SYNC_LAST_UPDATE_USER',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032800"),'A_ACCOUNT_LIST','USER_ID','USERNAME','', array('SELECT_ADD_FOR_ORDER'=>array('USERNAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032801"));//エクセル・ヘッダでの説明
// comment out
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 再開ボタン
        /////////////////////////////////////////////////////////
        if( $g['privilege'] === '1' ){
            $objFunction = function($rowData){
                $retLinkable = "disabled";
                // 再開ボタン 活性・非活性制御
                if( array_key_exists('SYNC_STATUS_ROW_ID', $rowData) === true &&
                    array_key_exists('AUTO_SYNC_FLG', $rowData)      === true &&
                    array_key_exists('DISUSE_FLAG', $rowData)      === true ) {
                    // 同期状態が異常かつ廃止レコードでない場合
                    if(($rowData['SYNC_STATUS_ROW_ID'] == TD_B_CICD_REPO_SYNC_STATUS_NAME::C_SYNC_STATUS_ROW_ID_ERROR) &&
                       ($rowData['DISUSE_FLAG'] == 0)) {
                        // 自動同期　有効(未選択)の場合
                        if($rowData['AUTO_SYNC_FLG'] != TD_B_CICD_MATERIAL_LINK_LIST::C_AUTO_SYNC_FLG_OFF) {
                            $retLinkable = "";
                        }
                    }
                }
                return $retLinkable;
            };
            $strLabelText1 = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011900");
            $strLabelText2 = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011900");
            $c = new LinkButtonColumn('RestartCallback',$strLabelText1, $strLabelText2, 'dummy');
            $c->setDBColumn(false);
            $c->setHiddenMainTableColumn(false);
            $c->setOutputType('print_table', new OutputType(new SortedTabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array(""))));
            $c->setEvent("print_table", "onClick", "RestartCallback", array('this',':MATL_LINK_ROW_ID',':UPD_UPDATE_TIMESTAMP'));
            $c->getOutputType('print_journal_table')->setVisible(false);
            $cg->addColumn($c);
        }

    $table->addColumn($cg);

    /////////////////////////////////////////////////////////
    // デリバリ情報
    /////////////////////////////////////////////////////////
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031500"));

        /////////////////////////////////////////////////////////
        // オペレーション
        /////////////////////////////////////////////////////////
        $c = new IDColumn('DEL_OPE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031600"),'E_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION','',array('OrderByThirdColumn'=>'OPERATION_NO_UAPK'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031601"));//エクセル・ヘッダでの説明

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // Movement
        /////////////////////////////////////////////////////////
        $c = new IDColumn('DEL_MOVE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031700"),'C_PATTERN_PER_ORCH','PATTERN_ID','PATTERN_NAME','',array('OrderByThirdColumn'=>'PATTERN_ID'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031701"));//エクセル・ヘッダでの説明

            /////////////////////////////////////////////////////////
            // 紐付け先素材タイプとMovementのプルダウンリスト連携設定
            /////////////////////////////////////////////////////////
            $objFunction01 = function($objOutputType, $aryVariant, $arySetting, $aryOverride, $objColumn){
                global $g;
                $retBool = false;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $aryDataSet = array();
        
                $strFxName = "";

                $MatlTypeRowId = $aryVariant['MATL_TYPE_ROW_ID'];
                switch($MatlTypeRowId) {
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:      //Playbook素材集
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:     //対話ファイル素材集
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:        //ロールパッケージ管理
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:     //ファイル管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:   //テンプレート管理
                     $ExtSimId1 = sprintf("'%s'",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY);
                     $ExtSimId2 = sprintf("'%s'",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER);
                     $ExtSimId3 = sprintf("'%s'",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE);
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in (%s,%s,%s)",$ExtSimId1,$ExtSimId2,$ExtSimId3);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:      //Module素材
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:      //Policy管理
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_TERRAFORM);
                     break;
                default:
                     $ExtSimIdstr = "";
                     break;
                }
                // RBAC対応 ----
                $strQuery = "SELECT "
                           ." TAB_1.PATTERN_ID      KEY_COLUMN "
                           .",TAB_1.PATTERN_NAME    DISP_COLUMN "
                           .",TAB_1.ACCESS_AUTH     ACCESS_AUTH "
                           ."FROM "
                           ." C_PATTERN_PER_ORCH TAB_1 "
                           ."WHERE "
                           ."     TAB_1.DISUSE_FLAG = '0' "
                           ."     $ExtSimIdstr "
                           ."ORDER BY KEY_COLUMN ";

                $aryForBind = array();

                if( 0 < strlen($MatlTypeRowId) ){
                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    } else {
                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                        if( $aryRetBody[0] === true ){
                            $objQuery = $aryRetBody[1];
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
                            $retBool = false;
                        }
                    }
                }
                // ---- RBAC対応
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

                $MatlTypeRowId = $rowData['MATL_TYPE_ROW_ID'];
                switch($MatlTypeRowId) {
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:      //Playbook素材集
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:     //対話ファイル素材集
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:        //ロールパッケージ管理
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:     //ファイル管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:   //テンプレート管理
                     $ExtSimId1 = sprintf("'%s'",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY);
                     $ExtSimId2 = sprintf("'%s'",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER);
                     $ExtSimId3 = sprintf("'%s'",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE);
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in (%s,%s,%s)",$ExtSimId1,$ExtSimId2,$ExtSimId3);
                     break;
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:      //Module素材
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:      //Policy管理
                     $ExtSimIdstr = sprintf("AND TAB_1.ITA_EXT_STM_ID in ('%s')",TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_TERRAFORM);
                     break;
                default:
                     $ExtSimIdstr = "";
                     break;
                }

                // RBAC対応 ----
                $strQuery = "SELECT "
                           ." TAB_1.PATTERN_ID      KEY_COLUMN "
                           .",TAB_1.PATTERN_NAME    DISP_COLUMN "
                           .",TAB_1.ACCESS_AUTH     ACCESS_AUTH "
                           ."FROM "
                           ." C_PATTERN_PER_ORCH TAB_1 "
                           ."WHERE "
                           ."     TAB_1.DISUSE_FLAG = '0' "
                           ."     $ExtSimIdstr "
                           ."ORDER BY KEY_COLUMN ";
        
                $aryForBind = array();

                if( 0 < strlen($MatlTypeRowId) ){
                    // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                    $obj = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $obj->getAccountInfo($g['login_id']);
                    if($ret === false) {
                        $intErrorType = 500;
                        $retBool = false;
                    } else {
                        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                        if( $aryRetBody[0] === true ){
                            $objQuery = $aryRetBody[1];
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
                            $retBool = false;
                        }
                    }
                }
                $aryRetBody = array($retBool, $intErrorType, $aryErrMsgBody, $strErrMsg, $aryDataSet);
                return $aryRetBody;
            };
        
            $strSetInnerText = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200035002");
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
        
            //コンテンツのソースがヴューの場合、登録/更新の対象とする
            $c->setHiddenMainTableColumn(true);
        
            //エクセル/CSVからのアップロードを禁止する。
            $c->setAllowSendFromFile(false);

            // REST/excel/csvで項目無効
            $c->getOutputType('excel')->setVisible(false);
            $c->getOutputType('csv')->setVisible(false);
            $c->getOutputType('json')->setVisible(false);
        
        // 最後に対応
        //    // データベース更新前のファンクション登録
        //    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // ドライラン
        /////////////////////////////////////////////////////////
        $c = new IDColumn('DEL_EXEC_TYPE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031800"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031801"));//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);//コンテンツのソースがヴューの場合、登録/更新の対象とする際に、trueとすること。setDBColumn(true)であることも必要。
        $cg->addColumn($c);


        /////////////////////////////////////////////////////////
        // 詳細情報   必須入力:false ユニーク:false
        /////////////////////////////////////////////////////////
        $objVldt = new MultiTextValidator(0,8192,false);
        $c = new MultiTextColumn('DEL_ERROR_NOTE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032100"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032101"));
        $c->setValidator($objVldt);
// comment out
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 結果
        /////////////////////////////////////////////////////////
        $objVldt = new SingleTextValidator(1,256,false);
        $c = new TextColumn('DEL_URL',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031900"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031901"));
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $cg->addColumn($c);

    $table->addColumn($cg);

    //tail of setting [multi-set-unique]----
    $table->addUniqueColumnSet(array('DIALOG_TYPE_ID','OS_TYPE_ID'));
    //----head of setting [multi-set-unique]

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){

        $getColumnDataFunction = function($strModeId,$columnName,$Type,$arrayVariant,$arrayRegData) {
            $UIbase = "";
            $DBbase = "";
            switch($strModeId){
            case "DTUP_singleRecUpdate":
            case "DTUP_singleRecRegister":
            case "DTUP_singleRecDelete":
                $UIbase   = array_key_exists($columnName,$arrayRegData)?$arrayRegData[$columnName]:null;
                break;
            }
            switch($strModeId){
            case "DTUP_singleRecUpdate":
            case "DTUP_singleRecRegister":
            case "DTUP_singleRecDelete":
                $DBbase   = isset($arrayVariant['edit_target_row'][$columnName])?$arrayVariant['edit_target_row'][$columnName]:null;
                break;
            }
            $ret_array = array();
            $ret_array['UI'] = $UIbase;
            $ret_array['DB'] = $DBbase;
            // DBに反映されるデータ
            // PasswordColumnの場合
            // 更新されていない場合はarrayRegDataはNullになるので設定済みのパスワード($arrayVariant['edit_target_row'])取得
            if($Type == "PasswordCloumn") {
                if(strlen($ret_array['UI'])==0) {
                    $ret_array['COMMIT'] = $ret_array['DB'];
                } else {
                    $ret_array['COMMIT'] = $ret_array['UI'];
                }
            } else {
                $ret_array['COMMIT'] = $ret_array['UI'];
            }
            return $ret_array;
        };

        global $g;
        global $root_dir_path;
        $retBool = true;
        $retStrBody = '';

        $strModeId = "";
        $modeValue_sub = "";

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }
        // Pkey取得
        $inputCheck = false;
        switch($strModeId) {
        case "DTUP_singleRecDelete":
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            $PkeyID = $strNumberForRI;
            break;
        case "DTUP_singleRecUpdate":
            $PkeyID = $strNumberForRI;
            break;
        case "DTUP_singleRecRegister":
            $PkeyID = array_key_exists('MATL_LINK_ROW_ID',$arrayRegData)?$arrayRegData['MATL_LINK_ROW_ID']:null;
            break;
        }

        // リモート・ローカルリポジトリが変更になったか確認
        $ColumnArray = array('MATL_LINK_NAME'=>'',
                             'MATL_TYPE_ROW_ID'=>'', 
                             'TEMPLATE_FILE_VARS_LIST'=>'', 
                             'DIALOG_TYPE_ID'=>'', 
                             'OS_TYPE_ID'=>'',
                             'DEL_OPE_ID'=>'',
                             'DEL_MOVE_ID'=>'');
        foreach($ColumnArray as $ColumnName=>$Type) {
            // $arrayRegDataはUI入力ベースの情報
            // $arrayVariant['edit_target_row']はDBに登録済みの情報
            $ColumnValueArray[$ColumnName] = $getColumnDataFunction($strModeId,$ColumnName,$Type,$arrayVariant,$arrayRegData);
        }

        switch($strModeId) {
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
            $MatlTypeColumnName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030600"); // 紐付先資材タイプ
            $MatlLinkColumnName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030100"); // 紐付先資材名
            // 紐付け先 素材集タイプ毎の必須入力チェック
            switch($ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT']) {
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
                break;
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
                $MatlTypeRowName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2022");
                if(strlen($ColumnValueArray['DIALOG_TYPE_ID']['COMMIT']) == 0) {
                    $ColumnName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030800");
                    // {}が{}の場合は必須項目です。(項目:{})
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2025",array($MatlTypeColumnName,$MatlTypeRowName,$ColumnName));
                    $retBool = false;
                }
                if(strlen($ColumnValueArray['OS_TYPE_ID']['COMMIT']) == 0) {
                    $ColumnName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030900");
                    // {}が{}の場合は必須項目です。(項目:{})
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2025",array($MatlTypeColumnName,$MatlTypeRowName,$ColumnName));
                    $retBool = false;
                }
                break;
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
                break;
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
                $MatlTypeRowName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2023");
                if(strlen($ColumnValueArray['MATL_LINK_NAME']['COMMIT']) != 0) {
                    $ret = preg_match("/^CPF_[_a-zA-Z0-9]+$/", $ColumnValueArray['MATL_LINK_NAME']['COMMIT']);
                    if($ret !== 1) {
                        // "{}が{}の場合の{}:正規表記(/^{}_[_a-zA-Z0-9]+$/)に一致するデータを入力してください。";
                        if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2027",array($MatlLinkColumnName,$MatlTypeColumnName,$MatlTypeRowName,'CPF'));
                        $retBool = false;
                    }
                }
                break;
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
                $MatlTypeRowName = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2024");
                if(strlen($ColumnValueArray['MATL_LINK_NAME']['COMMIT']) != 0) {
                    $ret = preg_match("/^TPF_[_a-zA-Z0-9]+$/", $ColumnValueArray['MATL_LINK_NAME']['COMMIT']);
                    if($ret !== 1) {
                        // "{}が{}の場合の{}:正規表記(/^{}_[_a-zA-Z0-9]+$/)に一致するデータを入力してください。";
                        if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2027",array($MatlLinkColumnName,$MatlTypeColumnName,$MatlTypeRowName,'TPF'));
                        $retBool = false;
                    }
                }
                break;
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
            case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
                break;
            }  
    
            // オペレーションIDとMovementの未入力チェック
            $ColumnNameOpe  = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031600");
            $ColumnNameMove = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031700");
            if(strlen($ColumnValueArray['DEL_OPE_ID']['COMMIT']) != 0) {
                if(strlen($ColumnValueArray['DEL_MOVE_ID']['COMMIT']) == 0) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                    //"{}が入力されている場合は必須項目です。(項目:{})";
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2026",array($ColumnNameOpe,$ColumnNameMove));
                    $retBool = false;
                }
            } 
            if(strlen($ColumnValueArray['DEL_MOVE_ID']['COMMIT']) != 0) {
                if(strlen($ColumnValueArray['DEL_OPE_ID']['COMMIT']) == 0) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                    //"{}が入力されている場合は必須項目です。(項目:{})";
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2026",array($ColumnNameMove,$ColumnNameOpe));
                    $retBool = false;
                }
            } 
            break;
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
