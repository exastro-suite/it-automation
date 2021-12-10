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
//      CI/CD For IaC リモートリポジトリ管理
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){

    global $root_dir_path;
    /* ルートディレクトリの取得 */
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    require_once ( $root_dir_path . "/libs/backyardlibs/CICD_for_IaC/local_functions.php");
    require_once ( $root_dir_path . "/libs/backyardlibs/CICD_for_IaC/table_definition.php");

    global $g;

    $SyncStatusNameobj = new TD_SYNC_STATUS_NAME_DEFINE($g['objMTS']);

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010000");

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

    $table = new TableControlAgent('D_CICD_REPOLIST_SYNCSTS_LINK','REPO_ROW_ID', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010001"), 'D_CICD_REPOLIST_SYNCSTS_LINK_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['REPO_ROW_ID']->setSequenceID('B_CICD_REPOSITORY_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('B_CICD_REPOSITORY_LIST_JSQ');
    unset($tmpAryColumn);

    // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
    $table->setDBMainTableHiddenID('B_CICD_REPOSITORY_LIST');
    $table->setDBJournalTableHiddenID('B_CICD_REPOSITORY_LIST_JNL');
    // 利用時は、更新対象カラムに、「$c->setHiddenMainTableColumn(true);」を付加すること
    // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010002"));
    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010003"));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    /////////////////////////////////////////////////////////
    // リポジトリ(名)  必須入力:true ユニーク:true
    ///////////////////////////////////////////////////////// 
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('REPO_NAME',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010100"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010101"));
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $c->setUnique(true);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);


    /////////////////////////////////////////////////////////
    // リポジトリ(URL)  必須入力:true ユニーク:falue
    ///////////////////////////////////////////////////////// 
    $objVldt = new SingleTextValidator(1,256,false);
    $c = new TextColumn('REMORT_REPO_URL',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010200"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010201"));
    $c->setValidator($objVldt);
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    /////////////////////////////////////////////////////////
    // ブランチ名  必須入力:false ユニーク:false
    ///////////////////////////////////////////////////////// 
    $objVldt = new SingleTextValidator(0,256,false);
    $c = new TextColumn('BRANCH_NAME',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010300"));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010301"));
    $c->setValidator($objVldt);
    $c->setHiddenMainTableColumn(true);
    $table->addColumn($c);

    /////////////////////////////////////////////////////////
    // プロトコル   必須入力:true ユニーク:false
    ///////////////////////////////////////////////////////// 
    $c = new IDColumn('GIT_PROTOCOL_TYPE_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010400"),'B_CICD_GIT_PROTOCOL_TYPE_NAME','GIT_PROTOCOL_TYPE_ROW_ID','GIT_PROTOCOL_TYPE_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010401"));
    $c->setRequired(true);
    $c->setHiddenMainTableColumn(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('GIT_PROTOCOL_TYPE_ROW_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'GIT_PROTOCOL_TYPE_ROW_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'GIT_PROTOCOL_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    /////////////////////////////////////////////////////////
    // Visibilityタイプ   必須入力:false ユニーク:false
    ///////////////////////////////////////////////////////// 
    $c = new IDColumn('GIT_REPO_TYPE_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010500"),'B_CICD_GIT_REPOSITORY_TYPE_NAME','GIT_REPO_TYPE_ROW_ID','GIT_REPO_TYPE_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010501"));
    $c->setHiddenMainTableColumn(true);
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('GIT_REPO_TYPE_ROW_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_CICD_GIT_REPOSITORY_TYPE_NAME_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'GIT_REPO_TYPE_ROW_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'GIT_REPO_TYPE_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    /////////////////////////////////////////////////////////
    // Git アカウント
    ///////////////////////////////////////////////////////// 
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010600"));

        /////////////////////////////////////////////////////////
        // Git ユーザー  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $objVldt = new SingleTextValidator(0,128,false);
        $c = new TextColumn('GIT_USER',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010700"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010701"));
        $c->setValidator($objVldt);
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // Git パスワード  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $objVldt = new SingleTextValidator(0,128,false);
        $c = new PasswordColumn('GIT_PASSWORD',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010800"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010801"));
        $c->setValidator($objVldt);
        $c->setUpdateRequireExcept(1); // 1は空白の場合は維持、それ以外はNULL扱いで更新
        $c->setEncodeFunctionName("ky_encrypt");
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

    /////////////////////////////////////////////////////////
    // ssh接続情報
    ///////////////////////////////////////////////////////// 
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010810"));
        /////////////////////////////////////////////////////////
        // ssh パスワード  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $objVldt = new SingleTextValidator(0,128,false);
        $c = new PasswordColumn('SSH_PASSWORD',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010820"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010821"));
        $c->setValidator($objVldt);
        $c->setUpdateRequireExcept(1); // 1は空白の場合は維持、それ以外はNULL扱いで更新
        $c->setEncodeFunctionName("ky_encrypt");
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // ssh パスフレーズ  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $objVldt = new SingleTextValidator(0,128,false);
        $c = new PasswordColumn('SSH_PASSPHRASE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010830"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010831"));
        $c->setValidator($objVldt);
        $c->setUpdateRequireExcept(1); // 1は空白の場合は維持、それ以外はNULL扱いで更新
        $c->setEncodeFunctionName("ky_encrypt");
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);
    
        /////////////////////////////////////////////////////////
        // ssh core.sshCommand
        ///////////////////////////////////////////////////////// 
        $objVldt = new SingleTextValidator(0,512,false);
        $c = new TextColumn('SSH_EXTRA_ARGS',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010840"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010841"));
        $c->setValidator($objVldt);
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);
    $table->addColumn($cg);

    /////////////////////////////////////////////////////////
    // Proxy
    ///////////////////////////////////////////////////////// 
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200010900"));

        /////////////////////////////////////////////////////////
        //  Proxyサーバのアドレス 必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $objVldt = new SingleTextValidator(0,128,false);
        $c = new TextColumn('PROXY_ADDRESS',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011000"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011001"));
        $c->setValidator($objVldt);
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // Proxyサーバのポート 必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new NumColumn('PROXY_PORT',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011100"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011101"));//エクセル・ヘッダでの説明
        $c->setSubtotalFlag(false);
        $c->setValidator(new IntNumValidator(1,65535));
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

    /////////////////////////////////////////////////////////
    // リモートリポジトリ同期情報
    ///////////////////////////////////////////////////////// 
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011200"));

        /////////////////////////////////////////////////////////
        // 自動同期   必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 

        $c = new IDColumn('AUTO_SYNC_FLG',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011300"),'B_VALID_INVALID_MASTER','FLAG_ID','FLAG_NAME','', array('SELECT_ADD_FOR_ORDER'=>array('DISP_SEQ'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011301"));
        $c->setHiddenMainTableColumn(true);
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
        // 周期　単位:秒  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new NumColumn('SYNC_INTERVAL',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011400"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011401"));//エクセル・ヘッダでの説明
        $c->setSubtotalFlag(false);
        $c->setValidator(new IntNumValidator(1,2147483647));
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

    /////////////////////////////////////////////////////////
    // リモートリポジトリ同期状態
    ///////////////////////////////////////////////////////// 
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011500"));
    
        /////////////////////////////////////////////////////////
        // 状態  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        // 更新時の初期値設定
        $beforeObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";
                $update_flg = false;
                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecUpdate" ){
                    $exeQueryData[$objColumn->getID()] = "";
                    $update_flg = true;
                }
                if( $modeValue=="DTUP_singleRecDelete" ){
                    $modeValue_sub = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];
                    if($modeValue_sub == "off") {
                        $exeQueryData[$objColumn->getID()] = "";
                        $update_flg = true;
                    }
                }
                if($update_flg === true) {
                    // 同期時刻を更新
                    $strFxName = "[FILE]:" . basename(__FILE__) . " [LINE]:" . __LINE__;
                    $strQuery = "UPDATE T_CICD_SYNC_STATUS SET SYNC_LAST_TIMESTAMP = null "
                               ." WHERE ROW_ID = :ROW_ID ";
                    $aryForBind = array('ROW_ID'=>$aryVariant['edit_target_row']['REPO_ROW_ID']);
                    $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);

                    if( $aryRetBody[0] !== true ){
                        web_log($strFxName . " [DB Access error]");
                        $boolRet = false;
                        $strErrMsg = $aryRetBody[2];
                        $intErrorType = 500;
                    }
                }
                $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
                return $retArray;

        };
        $c = new TextColumn('SYNC_STATUS_ROW_ID',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011600"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011601"));
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
        $c->setAllowSendFromFile(false);
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        $c->setHiddenMainTableColumn(true);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 詳細情報   必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        // 更新時の初期値設定
        $beforeObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
                global $g;
                $boolRet = true;
                $intErrorType = null;
                $aryErrMsgBody = array();
                $strErrMsg = "";
                $strErrorBuf = "";
                $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
                if( $modeValue=="DTUP_singleRecUpdate" ){
                    // 更新時、空白に設定
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
        $c = new MultiTextColumn('SYNC_ERROR_NOTE',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011700"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011701"));
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
        $c->setFunctionForEvent('beforeTableIUDAction',$beforeObjFunction);
        // ----  エクセル/CSVからのアップロードを禁止する。
        $c->setAllowSendFromFile(false);
        $c->setHiddenMainTableColumn(true);

        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // 最終同期日時  表示のみ
        ///////////////////////////////////////////////////////// 
        $c = new DateTimeColumn('SYNC_LAST_TIMESTAMP',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011800"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200011801"));
        $c->setValidator(new DateTimeValidator(null,null));
        // OutputType一覧  ----
        // filter 一覧のみ表示
        //$c->getOutputType('filter_table')->setVisible(false);
        //$c->getOutputType('print_table')->setVisible(false);
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
        $c->setHiddenMainTableColumn(false);   // DB更新外
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
                        if($rowData['AUTO_SYNC_FLG'] != TD_B_CICD_REPOSITORY_LIST::C_AUTO_SYNC_FLG_OFF) {
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
            $c->setEvent("print_table", "onClick", "RestartCallback", array('this',':REPO_ROW_ID',':UPD_UPDATE_TIMESTAMP'));
            $c->getOutputType('print_journal_table')->setVisible(false);
            $cg->addColumn($c);
        }

    $table->addColumn($cg);

    /////////////////////////////////////////////////////////
    // Git通信リトライ情報
    ///////////////////////////////////////////////////////// 
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200012000"));
    
        /////////////////////////////////////////////////////////
        // リトライ回数  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new NumColumn('RETRAY_COUNT',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200012100"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200012101"));
        $c->setValidator(new IntNumValidator(0,65535));
        $c->setSubtotalFlag(false);
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

        /////////////////////////////////////////////////////////
        // リトライ周期  必須入力:false ユニーク:false
        ///////////////////////////////////////////////////////// 
        $c = new NumColumn('RETRAY_INTERVAL',$g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200012200"));
        $c->setDescription($g['objMTS']->getSomeMessage("ITACICDFORIAC-MNU-1200012201"));
        $c->setValidator(new IntNumValidator(0,65535));
        $c->setSubtotalFlag(false);
        $c->setHiddenMainTableColumn(true);
        $cg->addColumn($c);

    $table->addColumn($cg);

    //----head of setting [multi-set-unique]
    $table->addUniqueColumnSet(array('REMORT_REPO_URL', 'BRANCH_NAME'));
    //tail of setting [multi-set-unique]----

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
        switch($strModeId) {
        case "DTUP_singleRecDelete":
            $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            $PkeyID = $strNumberForRI;
            break;
        case "DTUP_singleRecUpdate":
            $PkeyID = $strNumberForRI;
            break;
        case "DTUP_singleRecRegister":
            $PkeyID = array_key_exists('REPO_ROW_ID',$arrayRegData)?$arrayRegData['REPO_ROW_ID']:null;
            break;
        }
      
        // パスワード削除フラグ
        $password_del = false;
        if(isset($arrayRegData['del_password_flag_COL_IDSOP_15']) && $arrayRegData['del_password_flag_COL_IDSOP_15'] == "on"){
            $password_del = true;
        }
        // sshパスワード削除フラグ
        $ssh_password_del = false;
        if(isset($arrayRegData['del_password_flag_COL_IDSOP_16']) && $arrayRegData['del_password_flag_COL_IDSOP_16'] == "on"){
            $ssh_password_del = true;
        }
        // sshパスフレーズ削除フラグ
        $ssh_passphrase_del = false;
        if(isset($arrayRegData['del_password_flag_COL_IDSOP_17']) && $arrayRegData['del_password_flag_COL_IDSOP_17'] == "on"){
            $ssh_passphrase_del = true;
        }

        $ColumnArray = array('GIT_PROTOCOL_TYPE_ROW_ID'=>'','GIT_REPO_TYPE_ROW_ID'=>'','GIT_USER'=>'','GIT_PASSWORD'=>'PasswordCloumn','AUTO_SYNC_FLG'=>'','SYNC_INTERVAL'=>'','SSH_PASSWORD'=>'PasswordCloumn','SSH_PASSPHRASE'=>'PasswordCloumn');
        foreach($ColumnArray as $ColumnName=>$Type) {
            // $arrayRegDataはUI入力ベースの情報
            // $arrayVariant['edit_target_row']はDBに登録済みの情報
            $ColumnValueArray[$ColumnName] = $getColumnDataFunction($strModeId,$ColumnName,$Type,$arrayVariant,$arrayRegData);
        }
        // プロトコルタイプ
        switch($ColumnValueArray['GIT_PROTOCOL_TYPE_ROW_ID']['COMMIT']) {
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_HTTPS:     // https
            // リポジトリタイプ
            if(strlen($ColumnValueArray['GIT_REPO_TYPE_ROW_ID']['COMMIT'])==0) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttpsを選択している場合は、入力が必須な項目です。(項目:Visibilityタイプ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2080");
                $retBool = false;
            } else {
               switch($ColumnValueArray['GIT_REPO_TYPE_ROW_ID']['COMMIT']) {
               case TD_B_CICD_GIT_REPOSITORY_TYPE_NAME::C_GIT_REPO_TYPE_ROW_ID_PUBLIC:  // Public
                   if(strlen($ColumnValueArray['GIT_USER']['COMMIT']) != 0) {
                       if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                       // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合以外は、入力が不要な項目です。(項目:Gitユーザ)
                       $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2076");
                       $retBool = false;
                   }
                   if((strlen($ColumnValueArray['GIT_PASSWORD']['COMMIT']) != 0) && ($password_del === false)) {
                       if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                       $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2077");
                       $retBool = false;
                   }
                   break;
               case TD_B_CICD_GIT_REPOSITORY_TYPE_NAME::C_GIT_REPO_TYPE_ROW_ID_PRIVATE: // Private
                   if(strlen($ColumnValueArray['GIT_USER']['COMMIT']) == 0) {
                       if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                       // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合は、入力が必須な項目です。(項目:Gitユーザ)
                       $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2081");
                       $retBool = false;
                   }
                   if((strlen($ColumnValueArray['GIT_PASSWORD']['COMMIT']) == 0) || ($password_del === true)) {
                       if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                       // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合は、入力が必須な項目です。(項目:Gitパスワード)
                       $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2082");
                       $retBool = false;
                   }
                   break;
               default:
                   if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                   // 選択されているVisibilityタイプが不正です。
                   $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2013");
                   $retBool = false;
                   break;
               }
            }
            // sshパスワード/sshパスフレーズ未入力チェック
            if((strlen($ColumnValueArray['SSH_PASSWORD']['COMMIT']) != 0) && ($ssh_password_del === false)) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでsshパスワード認証以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスワード)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2073");
                $retBool = false;
            }
            if((strlen($ColumnValueArray['SSH_PASSPHRASE']['COMMIT']) != 0) && ($ssh_passphrase_del === false)) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでssh鍵認証(パスフレーズあり)以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスフレーズ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2074");
                $retBool = false;
            }
            break;
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_LOCAL:     // Local
            if(strlen($ColumnValueArray['GIT_REPO_TYPE_ROW_ID']['COMMIT'])!=0) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttps以外を選択して いる場合は、入力が不要な項目です。(項目:Visibilityタイプ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2075");
                $retBool = false;
            }
            if(strlen($ColumnValueArray['GIT_USER']['COMMIT']) != 0) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合以外は、入力が不要な項目です。(項目:Gitユーザ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2076");
                $retBool = false;
            }
            if((strlen($ColumnValueArray['GIT_PASSWORD']['COMMIT']) != 0) && ($password_del === false)) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合以外は、入力が不要な項目です。(項目:Gitパスワード)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2077");
                $retBool = false;
            }
            // sshパスワード/sshパスフレーズ未入力チェック
            if((strlen($ColumnValueArray['SSH_PASSWORD']['COMMIT']) != 0) && ($ssh_password_del === false)) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでsshパスワード認証以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスワード)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2073");
                $retBool = false;
            }
            if((strlen($ColumnValueArray['SSH_PASSPHRASE']['COMMIT']) != 0) && ($ssh_passphrase_del === false)) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでssh鍵認証(パスフレーズあり)以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスフレーズ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2074");
                $retBool = false;
            }
            break;
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_PASS:        //ssh(パスワード認証)
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY:         //ssh(鍵認証パスフレーズあり)
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY_NOPASS:  //ssh(鍵認証パスフレーズなし)
            if(strlen($ColumnValueArray['GIT_REPO_TYPE_ROW_ID']['COMMIT'])!=0) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttps以外を選択して いる場合は、入力が不要な項目です。(項目:Visibilityタイプ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2075");
                $retBool = false;
            }
            if(strlen($ColumnValueArray['GIT_USER']['COMMIT']) != 0) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合以外は、入力が不要な項目です。(項目:Gitユーザ)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2076");
                $retBool = false;
            }
            if((strlen($ColumnValueArray['GIT_PASSWORD']['COMMIT']) != 0) && ($password_del === false)) {
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // プロトコルでhttpsを選択し、VisibilityタイプでPrivateを選択している場合以外は、入力が不要な項目です。(項目:Gitパスワード)
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2077");
                $retBool = false;
            }
            switch($ColumnValueArray['GIT_PROTOCOL_TYPE_ROW_ID']['COMMIT']) {
            case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_PASS:        //ssh(パスワード認証)
                // sshパスワード入力/sshパスフレーズ未入力チェック
                if((strlen($ColumnValueArray['SSH_PASSWORD']['COMMIT']) == 0) || ($ssh_password_del === true)) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    // プロトコルでsshパスワード認証を選択している場合は、入力は必須な項目です。(項目:ssh接続情報/パスワード)
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2078");
                    $retBool = false;
                }
                if((strlen($ColumnValueArray['SSH_PASSPHRASE']['COMMIT']) != 0) && ($ssh_passphrase_del === false)) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    // プロトコルでssh鍵認証(パスフレーズあり)以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスフレーズ)
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2074");
                    $retBool = false;
                }
                break;
            case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY:         //ssh(鍵認証パスフレーズあり)
                // sshパスワード未入力/sshパスフレーズ入力チェック
                if((strlen($ColumnValueArray['SSH_PASSWORD']['COMMIT']) != 0) && ($ssh_password_del === false)) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    //プロトコルでsshパスワード認証以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスワード)
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2073");
                    $retBool = false;
                }
                if((strlen($ColumnValueArray['SSH_PASSPHRASE']['COMMIT']) == 0) || ($ssh_passphrase_del === true)) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    //プロトコルでssh鍵認証(パスフレーズあり)を選択している場合は、入力が必須な項目です。(項目:ssh接続情報/パスフレーズ)
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2079");
                    $retBool = false;
                }
                break;
            case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY_NOPASS:  //ssh(鍵認証パスフレーズなし)
                // sshパスワード/sshパスフレーズ未入力チェック
                if((strlen($ColumnValueArray['SSH_PASSWORD']['COMMIT']) != 0) && ($ssh_password_del === false)) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    //プロトコルでsshパスワード認証以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスワード)
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2073");
                    $retBool = false;
                }
                if((strlen($ColumnValueArray['SSH_PASSPHRASE']['COMMIT']) != 0) && ($ssh_passphrase_del === false)) {
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    //プロトコルでssh鍵認証(パスフレーズあり)以外を選択している場合は、入力が不要な項目です。(項目:ssh接続情報/パスフレーズ)
                    $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2074");
                    $retBool = false;
                }
                break;
            default:
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                // 選択されているプロトコルが不正です。
                $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2012");
                $retBool = false;
                break;
            }
            break;
        default:
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
            // 選択されているプロトコルが不正です。
            $retStrBody .= $g['objMTS']->getSomeMessage("ITACICDFORIAC-ERR-2012");
            $retBool = false;
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
