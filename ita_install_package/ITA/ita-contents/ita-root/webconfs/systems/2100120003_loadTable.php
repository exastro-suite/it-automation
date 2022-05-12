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
//      CI/CD For IaC 資材紐付管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    global $root_dir_path;

    // メニュー作成経由対応
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_functions.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_db_access.php');
    require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/table_definition.php');

    $SyncStatusNameobj = new TD_SYNC_STATUS_NAME_DEFINE($g['objMTS']);

    $wanted_filename = "ita_ansible-driver";
    $ansible_driver  = false;
    if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
        $ansible_driver = true;
    }
    $wanted_filename = "ita_terraform-driver";    
    $terraform_driver  = false;
    if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
        $terraform_driver = true;
    }

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

    $table = new TableControlAgent('D_CICD_MATERIAL_LINK_LIST','MATL_LINK_ROW_ID', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030001"), 'D_CICD_MATERIAL_LINK_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['MATL_LINK_ROW_ID']->setSequenceID('B_CICD_MATERIAL_LINK_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CICD_MATERIAL_LINK_LIST_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_CICD_MATERIAL_LINK_LIST');
    $table->setDBJournalTableHiddenID('B_CICD_MATERIAL_LINK_LIST_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること


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
    $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする

    $table->addColumn($c);

    /////////////////////////////////////////////////////////////
    // IaC(From)
    /////////////////////////////////////////////////////////////
    $cg1 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030200"));

        /////////////////////////////////////////////////////////
        // Excel/Rest用　リモート名+資材パス名  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new IDColumn('REST_MATL_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030400"),'D_CICD_MATL_FILE_LIST','MATL_FILE_PATH_PULLKEY','MATL_FILE_PATH_PULLDOWN','', array('SELECT_ADD_FOR_ORDER'=>array('MATL_FILE_PATH_PULLDOWN'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030401"));
        $c->getOutputType('filter_table')->setVisible(false);
        $c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('print_journal_table')->setVisible(false);
        $c->setHiddenMainTableColumn(false);  //コンテンツのソースがヴューの場合、登録/更新の対象外
        // 組み合わせバリデータで必須チェック
        //$c->setRequired(true);
        $c->setRequiredMark(true);//必須マークのみ付与
        $cg1->addColumn($c);

        /////////////////////////////////////////////////////////
        // リポジトリ名  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        // Excel/Restからの場合、Excel/Restで設定されたリポジトリ名を設定
        $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['REPO_ROW_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['REPO_ROW_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };

        $c = new IDColumn('REPO_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030300"),'B_CICD_REPOSITORY_LIST','REPO_ROW_ID','REPO_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('REPO_NAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030201"));
        $c->setEvent('update_table', 'onchange', 'repo_upd');
        $c->setEvent('register_table', 'onchange', 'repo_reg');
        // 組み合わせバリデータで必須チェック
        //$c->setRequired(true);
        $c->setRequiredMark(true);//必須マークのみ付与
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        //エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);

        // REST/excel/csvで項目無効
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);

        $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('REPO_ROW_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_CICD_REPOSITORY_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'REPO_ROW_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'REPO_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);

        $cg1->addColumn($c);

        /////////////////////////////////////////////////////////
        // 資材パス名  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        // Excel/Restからの場合、Excel/Restで設定されたリポジトリ名を設定
        $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['MATL_ROW_ID_UPDATE_VALUE']) !== 0){
                        $exeQueryData[$objColumn->getID()] = $g['MATL_ROW_ID_UPDATE_VALUE'];
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };

        $c = new IDColumn('MATL_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030400"),'D_CICD_MATL_PATH_LIST','MATL_ROW_ID','MATL_FILE_PATH','', array('SELECT_ADD_FOR_ORDER'=>array('MATL_FILE_PATH'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
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
                $strQuery = " SELECT
                                 TAB_1.MATL_ROW_ID     KEY_COLUMN
                                ,TAB_1.MATL_FILE_PATH  DISP_COLUMN
                                ,TAB_1.ACCESS_AUTH     ACCESS_AUTH
                                ,TAB_2.ACCESS_AUTH     ACCESS_AUTH_01
                              FROM
                                          B_CICD_MATERIAL_LIST     TAB_1
                                LEFT JOIN B_CICD_REPOSITORY_LIST  TAB_2 ON (TAB_1.REPO_ROW_ID = TAB_2.REPO_ROW_ID)
                              WHERE
                                    TAB_1.DISUSE_FLAG = '0'
                                AND TAB_2.DISUSE_FLAG = '0'
                                AND TAB_1.REPO_ROW_ID = :REPO_ROW_ID 
                              ORDER BY DISP_COLUMN ";
        
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

                $RepoRowID = null;
                if(is_array($rowData) && array_key_exists('REPO_ROW_ID', $rowData)){
                    $RepoRowID = $rowData['REPO_ROW_ID'];
                }

                $strQuery = " SELECT
                                 TAB_1.MATL_ROW_ID     KEY_COLUMN
                                ,TAB_1.MATL_FILE_PATH  DISP_COLUMN
                                ,TAB_1.ACCESS_AUTH     ACCESS_AUTH
                                ,TAB_2.ACCESS_AUTH     ACCESS_AUTH_01
                              FROM
                                          B_CICD_MATERIAL_LIST     TAB_1
                                LEFT JOIN B_CICD_REPOSITORY_LIST  TAB_2 ON (TAB_1.REPO_ROW_ID = TAB_2.REPO_ROW_ID)
                              WHERE
                                    TAB_1.DISUSE_FLAG = '0'
                                AND TAB_2.DISUSE_FLAG = '0'
                                AND TAB_1.REPO_ROW_ID = :REPO_ROW_ID 
                              ORDER BY DISP_COLUMN ";
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
        // 複製対応
        $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);
        $objOTForReg = new OutputType(new ReqTabHFmt(), $objVarBFmtReg);
        $objOTForReg->setFunctionForGetFADSelectList($objFunction02);
        
        $c->setOutputType('update_table',$objOTForUpd);
        $c->setOutputType('register_table',$objOTForReg);
        
        //エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);

        // REST/excel/csvで項目無効
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        
        $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

        // 組み合わせバリデータで必須チェック
        //$c->setRequired(true);
        $c->setRequiredMark(true);//必須マークのみ付与
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする

        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('MATL_ROW_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CICD_MATL_PATH_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MATL_ROW_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MATL_FILE_PATH',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);

        $cg1->addColumn($c);

    $table->addColumn($cg1);

    /////////////////////////////////////////////////////////////
    // IaC(To)
    /////////////////////////////////////////////////////////////
    $cg2 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030500"));

        // インストールされているドライバを判断し資材タイプに紐付るViewを決定
        $chkarry               = array();
        $chkarry[true][true]   = "B_CICD_MATERIAL_TYPE_NAME";
        $chkarry[true][false]  = "B_CICD_MATERIAL_TYPE_NAME_ANS";
        $chkarry[false][true]  = "B_CICD_MATERIAL_TYPE_NAME_TERRA";
        $chkarry[false][false] = "B_CICD_MATERIAL_TYPE_NAME_NULL";
        $view_name = $chkarry[$ansible_driver][$terraform_driver];
        /////////////////////////////////////////////////////////
        // 資材タイプ  必須入力:true ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new IDColumn('MATL_TYPE_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030600"),$view_name,'MATL_TYPE_ROW_ID','MATL_TYPE_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('MATL_TYPE_ROW_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030601"));

        $c->setEvent('update_table', 'onchange', 'matl_type_upd');
        $c->setEvent('register_table', 'onchange', 'matl_type_reg');

        $c->setRequired(true);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('MATL_TYPE_ROW_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>$view_name.'_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'MATL_TYPE_ROW_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'MATL_TYPE_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg2->addColumn($c);

        if($ansible_driver === true) {
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
                $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
                $cg3->addColumn($c);
    
            $cg2->addColumn($cg3);


            /////////////////////////////////////////////////////////////
            // Ansible-Pioneer
            /////////////////////////////////////////////////////////////
            $cg3 = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030700"));
    
                /////////////////////////////////////////////////////////
                // 対話種別  必須入力:false ユニーク:false
                /////////////////////////////////////////////////////////
                $c = new IDColumn('DIALOG_TYPE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030800"),'B_ANSIBLE_PNS_DIALOG_TYPE','DIALOG_TYPE_ID','DIALOG_TYPE_NAME','',array('SELECT_ADD_FOR_ORDER'=>array('DIALOG_TYPE_NAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
                $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030801"));
                $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
                $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
                $objOT->setFirstSearchValueOwnerColumnID('DIALOG_TYPE_ID');
                $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_ANSIBLE_PNS_DIALOG_TYPE_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'DIALOG_TYPE_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'DIALOG_TYPE_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                    )
                );
                $objOT->setTraceQuery($aryTraceQuery);
                $c->setOutputType('print_journal_table',$objOT);
                $cg3->addColumn($c);
    
                /////////////////////////////////////////////////////////
                // OS種別  必須入力:false ユニーク:false
                /////////////////////////////////////////////////////////
                $c = new IDColumn('OS_TYPE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030900"),'B_OS_TYPE','OS_TYPE_ID','OS_TYPE_NAME','',array('SELECT_ADD_FOR_ORDER'=>array('OS_TYPE_NAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
                $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200030901"));
                $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
                $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
                $objOT->setFirstSearchValueOwnerColumnID('OS_TYPE_ID');
                $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_OS_TYPE_JNL',
                    'TTT_SEARCH_KEY_COLUMN_ID'=>'OS_TYPE_ID',
                    'TTT_GET_TARGET_COLUMN_ID'=>'OS_TYPE_NAME',
                    'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
                    'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
                    'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
                    )
                );
                $objOT->setTraceQuery($aryTraceQuery);
                $c->setOutputType('print_journal_table',$objOT);
                $cg3->addColumn($c);
    
            $cg2->addColumn($cg3);
        }

        /////////////////////////////////////////////////////////
        // Restユーザー   必須入力:true ユニーク:false
        /////////////////////////////////////////////////////////
        $c = new IDColumn('ACCT_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032000"),'D_CICD_UACC_RUCC_LINKLINK','USERNAME_PULLKEY','USERNAME_PULLDOWN','', array('SELECT_ADD_FOR_ORDER'=>array('USERNAME_PULLKEY'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032001"));
        $c->setRequired(true);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('ACCT_ROW_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CICD_UACC_RUCC_LINKLINK_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'USERNAME_PULLKEY',
            'TTT_GET_TARGET_COLUMN_ID'=>'USERNAME_PULLDOWN',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg2->addColumn($c);

        /////////////////////////////////////////////////////////
        // アクセス許可ロール   必須入力:false ユニーク:false
        /////////////////////////////////////////////////////////
        $c = new IDColumn('RBAC_FLG_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032200"),'B_CICD_RBAC_FLG_NAME','RBAC_FLG_ROW_ID','RBAC_FLG_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('RBAC_FLG_ROW_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032201"));
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('RBAC_FLG_ROW_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_CICD_RBAC_FLG_NAME_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'RBAC_FLG_ROW_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'RBAC_FLG_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
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
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $c->setDefaultValue("register_table", TD_B_CICD_MATERIAL_LINK_LIST::C_AUTO_SYNC_FLG_ON);
        $c->setRequired(true);
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('AUTO_SYNC_FLG');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_VALID_INVALID_MASTER_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'FLAG_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'FLAG_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
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
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $c = new TextColumn('SYNC_STATUS_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031200"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031201"));
        $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
        $c->setOutputType('update_table',   new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
        // OutputType一覧  ----
        // filter 一覧 excel関連のみ表示
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setAllowSendFromFile(false);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 詳細情報  必須入力:false ユニーク:false
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
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $objVldt = new MultiTextValidator(0,8192,false);
        $c = new MultiTextColumn('SYNC_ERROR_NOTE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031300"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031301"));
        $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
        $c->setOutputType('update_table',   new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
        $c->setValidator($objVldt);
        // OutputType一覧  ----
        // filter 一覧 excel関連のみ表示
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 最終同期日時  表示のみ
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
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $c = new DateTimeColumn('SYNC_LAST_TIME',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031400"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031401"));
        $c->setValidator(new DateTimeValidator(null,null));
        // OutputType一覧  ----
        // filter 一覧 excel関連のみ表示
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $cg->addColumn($c);

        ////////////////////////////////////////////////////
        // 最終更新者ID  必須入力:false ユニーク:false
        ////////////////////////////////////////////////////
        // 更新時の初期値設定
        $beforeObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecUpdate" ){
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $c = new IDColumn('SYNC_LAST_UPDATE_USER',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032800"),'D_CICD_UACC_RUCC_LINKLINK','USERNAME_PULLKEY','USERNAME_PULLDOWN','', array('SELECT_ADD_FOR_ORDER'=>array('USERNAME_PULLKEY'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032801"));//エクセル・ヘッダでの説明
        // OutputType一覧  ----
        // filter 一覧 excel関連のみ表示
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('SYNC_LAST_UPDATE_USER');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_CICD_UACC_RUCC_LINKLINK_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'USERNAME_PULLKEY',
            'TTT_GET_TARGET_COLUMN_ID'=>'USERNAME_PULLDOWN',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 再開ボタン
        /////////////////////////////////////////////////////////
        if( $g['privilege'] === '1' ){
            $objFunction = function($rowData){
                global $g;
                $SyncStatusNameobj = new TD_SYNC_STATUS_NAME_DEFINE($g['objMTS']);

                $retLinkable = "disabled";
                // 再開ボタン 活性・非活性制御
                if( array_key_exists('SYNC_STATUS_ROW_ID', $rowData) === true &&
                    array_key_exists('AUTO_SYNC_FLG', $rowData)      === true &&
                    array_key_exists('DISUSE_FLAG', $rowData)      === true ) {
                    // 同期状態が異常かつ廃止レコードでない場合
                    if(($rowData['SYNC_STATUS_ROW_ID'] == $SyncStatusNameobj->ERROR()) &&
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
        $c = new IDColumn('DEL_OPE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031600"),'C_OPERATION_LIST','OPERATION_NO_UAPK','OPERATION_NAME','',array('SELECT_ADD_FOR_ORDER'=>array('OPERATION_NAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031601"));//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('DEL_OPE_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'C_OPERATION_LIST_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'OPERATION_NO_UAPK',
            'TTT_GET_TARGET_COLUMN_ID'=>'OPERATION_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // Excel/Rest用 Movement  必須入力:false  ユニーク:false
        ///////////////////////////////////////////////////////// 
        // インストールされているドライバを判断し資材タイプに紐付るViewを決定
        $chkarry               = array();
        $chkarry[true][true]   = "D_CICD_MATL_PATTERN_LIST_ALL";
        $chkarry[true][false]  = "D_CICD_MATL_PATTERN_LIST_ANS";
        $chkarry[false][true]  = "D_CICD_MATL_PATTERN_LIST_TERRA";
        $chkarry[false][false] = "D_CICD_MATL_PATTERN_LIST_NULL";
        $view_name = $chkarry[$ansible_driver][$terraform_driver];

        $c = new IDColumn('REST_DEL_MOVE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031700"),$view_name,'MATL_PTN_NAME_PULLKEY','MATL_PTN_NAME_PULLDOWN','',array('SELECT_ADD_FOR_ORDER'=>array('MATL_PTN_NAME_PULLDOWN'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031701"));//エクセル・ヘッダでの説明
        $c->getOutputType('filter_table')->setVisible(false);
        $c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('print_journal_table')->setVisible(false);
        $c->setHiddenMainTableColumn(false);  //コンテンツのソースがヴューの場合、登録/更新の対象外
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // Movement
        /////////////////////////////////////////////////////////
        // Excel/Restからの場合、Excel/Restで設定されたリポジトリ名を設定
        $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global    $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";

                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                    if(strlen($g['DEL_MOVE_ID_UPDATE_VALUE']) !== 0){
                        if($g['DEL_MOVE_ID_UPDATE_VALUE'] == "NULL") {
                            $exeQueryData[$objColumn->getID()] = "";
                        } else {
                            $exeQueryData[$objColumn->getID()] = $g['DEL_MOVE_ID_UPDATE_VALUE'];
                        }
                    }
                }else if( $modeValue=="DTUP_singleRecDelete" ){
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };

        $c = new IDColumn('DEL_MOVE_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031700"),'C_PATTERN_PER_ORCH','PATTERN_ID','PATTERN_NAME','',array('SELECT_ADD_FOR_ORDER'=>array('PATTERN_NAME'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
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
                 // ありえない値を設定
                 $ExtSimIdstr = "AND TAB_1.ITA_EXT_STM_ID in ('1000')";
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

            $MatlTypeRowId = null;
            if(is_array($rowData) && array_key_exists('MATL_TYPE_ROW_ID', $rowData)){
                $MatlTypeRowId = $rowData['MATL_TYPE_ROW_ID'];
            }

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
                 // ありえない値を設定
                 $ExtSimIdstr = "AND TAB_1.ITA_EXT_STM_ID in ('1000')";
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
        // 複製対応
        $objVarBFmtReg->setFunctionForGetSelectList($objFunction03);
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
    
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする

        $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction);

        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('DEL_MOVE_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'C_PATTERN_PER_ORCH_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'PATTERN_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'PATTERN_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // ドライラン
        /////////////////////////////////////////////////////////
        $c = new IDColumn('DEL_EXEC_TYPE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031800"),'D_FLAG_LIST_01','FLAG_ID','FLAG_NAME','');
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031801"));//エクセル・ヘッダでの説明
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('DEL_EXEC_TYPE');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_FLAG_LIST_01_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'FLAG_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'FLAG_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);


        /////////////////////////////////////////////////////////
        // 詳細情報   必須入力:false ユニーク:false
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
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $objVldt = new MultiTextValidator(0,8192,false);
        $c = new MultiTextColumn('DEL_ERROR_NOTE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032100"));
        $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
        $c->setOutputType('update_table',   new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032101"));
        $c->setValidator($objVldt);
        // OutputType一覧  ----
        // filter 一覧 excel関連のみ表示
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 作業インスタンスNo
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
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $c = new TextColumn('DEL_EXEC_INS_NO',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031900"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200031901"));
        $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
        $c->setOutputType('update_table',   new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
        // OutputType一覧  ----
        // 隠しカラム
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        //$c->getOutputType('print_journal_table')->setVisible(false);
        //$c->getOutputType('excel')->setVisible(false);
        //$c->getOutputType('csv')->setVisible(false);
        //$c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 作業実行 ドライバー区分
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
                    $exeQueryData[$objColumn->getID()] = "";
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;
        };
        $c = new TextColumn('DEL_MENU_NO',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032500"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032501"));
        $strWebUIText = $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1070703");
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText)));
        $c->setOutputType('update_table',   new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText,true)));
        // OutputType一覧  ----
        // 隠しカラム
        $c->getOutputType('filter_table')->setVisible(false);
        $c->getOutputType('print_table')->setVisible(false);
        $c->getOutputType('update_table')->setVisible(false);
        $c->getOutputType('register_table')->setVisible(false);
        $c->getOutputType('delete_table')->setVisible(false);
        $c->getOutputType('print_journal_table')->setVisible(false);
        $c->getOutputType('excel')->setVisible(false);
        $c->getOutputType('csv')->setVisible(false);
        $c->getOutputType('json')->setVisible(false);
        // ----  OutputType一覧
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);  //コンテンツのソースがヴューの場合、登録/更新の対象とする
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 作業状態ボタン
        /////////////////////////////////////////////////////////
        if( $g['privilege'] === '1' ){
            $objFunction = function($rowData){
                $retLinkable = "disabled";
                // 再開ボタン 活性・非活性制御
                if( array_key_exists('DEL_EXEC_INS_NO', $rowData) === true &&
                    array_key_exists('DEL_MENU_NO', $rowData)  === true &&
                    array_key_exists('DISUSE_FLAG', $rowData)      === true ) {
                    // 作業インスタンスNoと作業実行ドライバ区分が設定されているか判定
                    if((strlen($rowData['DEL_EXEC_INS_NO']) != 0) &&
                       (strlen($rowData['DEL_MENU_NO']) != 0) &&
                       ($rowData['DISUSE_FLAG'] == 0)) {
                        // 自動同期　有効(未選択)の場合
                            $retLinkable = "";
                    }
                }
                return $retLinkable;
            };
            $strLabelText1 = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032600");
            $strLabelText2 = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200032601");
            $c = new LinkButtonColumn('CheckExecStatus',$strLabelText1, $strLabelText2, 'dummy');
            $c->setDBColumn(false);
            $c->setHiddenMainTableColumn(false);
            $c->setOutputType('print_table', new OutputType(new SortedTabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array(""))));
            $c->setEvent("print_table", "onClick", "monitor_execution", array('this',':DEL_EXEC_INS_NO',':DEL_MENU_NO'));
            $c->setOutputType('print_journal_table', new OutputType(new SortedTabHFmt(),new LinkButtonTabBFmt(0,array($objFunction),array(""))));
            $c->setEvent("print_journal_table", "onClick", "monitor_execution", array('this',':DEL_EXEC_INS_NO',':DEL_MENU_NO'));
            //$c->getOutputType('print_journal_table')->setVisible(false);
            $cg->addColumn($c);
        }

    $table->addColumn($cg);

    if($ansible_driver === true) {
        //tail of setting [multi-set-unique]----
        $table->addUniqueColumnSet(array('DIALOG_TYPE_ID','OS_TYPE_ID'));
        $table->addUniqueColumnSet(array('MATL_LINK_NAME','MATL_TYPE_ROW_ID'));
        //----head of setting [multi-set-unique]
    }

    $table->fixColumn();

    //----組み合わせバリデータ----
    $tmpAryColumn = $table->getColumns();
    $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

    $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
        global $root_dir_path;

        // メニュー作成経由対応
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_functions.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_db_access.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/table_definition.php');
        $wanted_filename = "ita_ansible-driver";
        $ansible_driver  = false;
        if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
            $ansible_driver = true;
        }
        $wanted_filename = "ita_terraform-driver";    
        $terraform_driver  = false;
        if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
            $terraform_driver = true;
        }

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
        if($ansible_driver === true) {
            $ColumnArray = array('MATL_LINK_NAME'=>'',
                                 'MATL_TYPE_ROW_ID'=>'', 
                                 'REST_MATL_ROW_ID'=>'',
                                 'REST_DEL_MOVE_ID'=>'',
                                 'REPO_ROW_ID'=>'',
                                 'MATL_ROW_ID'=>'',
                                 'MATL_TYPE_ROW_ID'=>'',
                                 'TEMPLATE_FILE_VARS_LIST'=>'', 
                                 'DIALOG_TYPE_ID'=>'', 
                                 'OS_TYPE_ID'=>'',
                                 'DEL_OPE_ID'=>'',
                                 'DEL_MOVE_ID'=>'');
        } else {
            $ColumnArray = array('MATL_LINK_NAME'=>'',
                                 'MATL_TYPE_ROW_ID'=>'', 
                                 'REST_MATL_ROW_ID'=>'',
                                 'REST_DEL_MOVE_ID'=>'',
                                 'REPO_ROW_ID'=>'',
                                 'MATL_ROW_ID'=>'',
                                 'MATL_TYPE_ROW_ID'=>'',
                                 'DEL_OPE_ID'=>'',
                                 'DEL_MOVE_ID'=>'');
        }
        // Pkey取得
        $inputCheck = false;
        $ColumnValueArray = array();
        switch($strModeId) {
        case "DTUP_singyyleRecDelete":
            foreach($ColumnArray as $ColumnName=>$Type) {
                $ColumnValueArray[$ColumnName]['COMMIT']  = isset($arrayVariant['edit_target_row'][$ColumnName])?
                                                                   $arrayVariant['edit_target_row'][$ColumnName]:null;
            }

            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            if($modeValue_sub == "off") {
                $inputCheck = true;
            }
            $PkeyID = $strNumberForRI;
            break;
        case "DTUP_singleRecUpdate":
            foreach($ColumnArray as $ColumnName=>$Type) {
                $ColumnValueArray[$ColumnName]['COMMIT'] = array_key_exists($ColumnName,$arrayRegData)?
                                                               $arrayRegData[$ColumnName]:null;
            }

            $PkeyID = $strNumberForRI;
            $inputCheck = true;
            break;
        case "DTUP_singleRecRegister":
            foreach($ColumnArray as $ColumnName=>$Type) {
                $ColumnValueArray[$ColumnName]['COMMIT'] = array_key_exists($ColumnName,$arrayRegData)?
                                                               $arrayRegData[$ColumnName]:null;
            }

            $PkeyID = array_key_exists('MATL_LINK_ROW_ID',$arrayRegData)?$arrayRegData['MATL_LINK_ROW_ID']:null;
            $inputCheck = true;
            break;
        }

        // 廃止の場合はチェックを行わない
        if($inputCheck === true) {
            $g['REPO_ROW_ID_UPDATE_VALUE'] = "";
            $g['MATL_ROW_ID_UPDATE_VALUE'] = "";
            $g['DEL_MOVE_ID_UPDATE_VALUE'] = "";
            // REPO_ROW_ID:未設定 MATL_ROW_ID:未設定 REST_MATL_ROW_ID:設定 => RestAPI/Excel/CSV
            if((strlen($ColumnValueArray['REPO_ROW_ID']['COMMIT']) == 0) &&
               (strlen($ColumnValueArray['MATL_ROW_ID']['COMMIT']) == 0) &&
               (strlen($ColumnValueArray['REST_MATL_ROW_ID']['COMMIT']) != 0)) {

                // Excel/Restで選択されているMovementをUI側の変数に退避
                if(strlen($ColumnValueArray['REST_DEL_MOVE_ID']['COMMIT']) == 0) {
                    $g['DEL_MOVE_ID_UPDATE_VALUE'] = "NULL";
                } else {
                    $g['DEL_MOVE_ID_UPDATE_VALUE'] = $ColumnValueArray['REST_DEL_MOVE_ID']['COMMIT'];
                }
                $ColumnValueArray['DEL_MOVE_ID']['COMMIT'] = $ColumnValueArray['REST_DEL_MOVE_ID']['COMMIT'];

                // Excel/Restで選択されている資材パスからリモートリポジトリを取得
                $query = "SELECT COUNT(*) AS REPO_COUNT,REPO_ROW_ID FROM B_CICD_MATERIAL_LIST WHERE DISUSE_FLAG='0' AND MATL_ROW_ID=:MATL_ROW_ID";
                $aryForBind = array();
                $aryForBind['MATL_ROW_ID'] = $ColumnValueArray['REST_MATL_ROW_ID']['COMMIT'];
                $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                if( $retArray[0] === true ){
                    $objQuery =& $retArray[1];
                    $row = $objQuery->resultFetch();
                    if( $row['REPO_COUNT'] == '1' ){
                        // Excel/Restで選択されている資材パスとリモートリポジトリをUI側の変数に退避
                        $g['REPO_ROW_ID_UPDATE_VALUE']     = $row['REPO_ROW_ID'];
                        $g['MATL_ROW_ID_UPDATE_VALUE']     = $ColumnValueArray['REST_MATL_ROW_ID']['COMMIT'];
                        $ColumnValueArray['REPO_ROW_ID']['COMMIT'] = $g['REPO_ROW_ID_UPDATE_VALUE'];
                        $ColumnValueArray['MATL_ROW_ID']['COMMIT'] = $g['MATL_ROW_ID_UPDATE_VALUE'];
                    }else if( $row['REPO_COUNT'] == '0' ){
                        // 資材パスからリモートリポジトリを特定できませんでした。
                        $retStrBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2068");
                        $objClientValidator->setValidRule($retStrBody);
                        return false;
                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $retBool = false;
                    }
                    unset($row);
                    unset($objQuery);
                }else{
                    web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                    $retBool = false;
                }
                unset($retArray);
                if($retBool === false) {
                    //----システムエラー
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
                    $objClientValidator->setValidRule($retStrBody);
                    return false;
                }
            } else {
                // リモートリポジトリと資材パスは必須入力の設定ができないので、必須入力チェックする。
                if((strlen($ColumnValueArray['REPO_ROW_ID']['COMMIT']) == 0) &&
                   (strlen($ColumnValueArray['MATL_ROW_ID']['COMMIT']) == 0) &&
                   (strlen($ColumnValueArray['REST_MATL_ROW_ID']['COMMIT']) == 0)) {
                    // 必須入力です。(項目：リモートリポジトリ、資材パス)
                    $retStrBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2067");
                    $objClientValidator->setValidRule($retStrBody);
                    return false;
                } else {
                    if(strlen($ColumnValueArray['REPO_ROW_ID']['COMMIT']) == 0) {
                        // 必須入力です。(項目：リモートリポジトリ)";
                        $retStrBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2070");
                        $objClientValidator->setValidRule($retStrBody);
                        return false;
                    } 
                    if(strlen($ColumnValueArray['MATL_ROW_ID']['COMMIT']) == 0) {
                        // 必須入力です。(項目：資材パス)
                        $retStrBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2071");
                        $objClientValidator->setValidRule($retStrBody);
                        return false;
                    }
                }
            }
            if($ansible_driver === true) {
                // 紐付け先 素材集タイプ毎の未入力チェック
                switch($ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT']) {
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
                    if(strlen($ColumnValueArray['OS_TYPE_ID']['COMMIT']) != 0) {
                        // 資材紐付先タイプがAnsible-Pioneerコンソール/対話ファイル素材集以外の場合は入力不要な項目です。(項目:OS種別)
                        if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2065");
                        $retBool = false;
                    }
                    if(strlen($ColumnValueArray['DIALOG_TYPE_ID']['COMMIT']) != 0) {
                        // 資材紐付先タイプがAnsible-Pioneerコンソール/対話ファイル素材集以外の場合は入力不要な項目です。(項目:対話種別)
                        if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2066");
                        $retBool = false;
                    }
                    break;
                }
                switch($ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT']) {
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
                case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
                    if(strlen($ColumnValueArray['TEMPLATE_FILE_VARS_LIST']['COMMIT']) != 0) {
                        // 資材紐付先タイプがAnsible共通コンソール/テンプレート管理以外の場合は入力不要な項目です。(項目:変数定義)
                        if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                        $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2064");
                        $retBool = false;
                    }
                    break;
                }
            }
            if($retBool === true) {
                // 必須項目で関連項目をチェックしていて項目が未入力の場合は花園でエラーにする。
                if((strlen($ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT']) == 0) ||
                   (strlen($ColumnValueArray['REPO_ROW_ID']['COMMIT']) == 0)      ||
                   (strlen($ColumnValueArray['MATL_ROW_ID']['COMMIT']) == 0)) {
                    return true;
                } 
                // 資材パスと紐付先資材タイプの妥当性確認
                $strQuery = "SELECT * FROM B_CICD_MATERIAL_LIST WHERE MATL_ROW_ID=:MATL_ROW_ID";
                $aryForBind = array();
                $aryForBind['MATL_ROW_ID'] = $ColumnValueArray['MATL_ROW_ID']['COMMIT'];
                $strFxName = "[FILE]:". basename(__FILE__) . " [LINE]:" . __LINE__;
                $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
                if( $aryRetBody[0] === true ){
                    $objQuery = $aryRetBody[1];
                    $row = $objQuery->resultFetch();
                    // 資材パスと紐付先資材タイプの妥当性確認
                    $retBool = MatlLinkColumnValidator2($ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT'],$row['MATL_FILE_TYPE_ROW_ID'],$g['objMTS'],$ColumnValueArray['REPO_ROW_ID']['COMMIT'],$PkeyID,$retStrBody);
                } else {
                    $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
                    $retBool = false;
                }        
                if($retBool===true){  
                    // 選択されている紐付先資材タイプとインストールされているドライバーの妥当性チェック
                    $retBool = MatlLinkColumnValidator4($ColumnValueArray['REPO_ROW_ID']['COMMIT'],$PkeyID,$ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT'],$g['objMTS'],$retStrBody,$ansible_driver,$terraform_driver);
                }
                if($retBool===true){  
                    // 入力チェック処理
                    $retBool = MatlLinkColumnValidator1($ColumnValueArray,$ColumnValueArray['REPO_ROW_ID']['COMMIT'],$PkeyID,$g['objMTS'],$retStrBody,$ansible_driver,$terraform_driver);
                }
            }
            if($retBool === true) {
                if(strlen($ColumnValueArray['DEL_MOVE_ID']['COMMIT']) != 0) {

                    $ExtStMId = "";
                    $query = "SELECT COUNT(*) AS PTN_COUNT, ITA_EXT_STM_ID FROM C_PATTERN_PER_ORCH WHERE DISUSE_FLAG='0' AND PATTERN_ID=:PATTERN_ID";
                    $aryForBind = array();
                    $aryForBind['PATTERN_ID'] = $ColumnValueArray['DEL_MOVE_ID']['COMMIT'];
                    $retArray = singleSQLExecuteAgent($query, $aryForBind, "NONAME_FUNC(VARS_MULTI_CHECK)");
                    if( $retArray[0] === true ){
                        $objQuery =& $retArray[1];
                        $row = $objQuery->resultFetch();
                        if( $row['PTN_COUNT'] == '1' ){
                            $ExtStMId = $row['ITA_EXT_STM_ID'];
                        }else if( $row['REPO_COUNT'] == '0' ){
                            $retStrBody = $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2069");
                            $objClientValidator->setValidRule($retStrBody);
                            return false;
                        }else{
                            web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                            $retBool = false;
                        }
                        unset($row);
                        unset($objQuery);
                    }else{
                        web_log("DB Access error file:" . basename(__FILE__) . " line:" . __LINE__);
                        $retBool = false;
                    }
                    unset($retArray);
                    if($retBool === false) {
                        //----システムエラー
                        $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
                        $objClientValidator->setValidRule($retStrBody);
                        return false;
                    }
                    // 資材紐付管理 紐付先資材タイプとMovementタイプの組み合わせチェック
                    $retBool = MatlLinkColumnValidator5($ColumnValueArray['REPO_ROW_ID']['COMMIT'],
                                                        $PkeyID,
                                                        $ExtStMId,
                                                        $ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT'],
                                                        $g['objMTS'],
                                                        $retStrBody);
                }
            }
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
