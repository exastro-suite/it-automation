<?php
//   Copyright 2020 NEC Corporation
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
    //  【概要】
    //      代入値紐付を元に代入値管理を更新
    //
    //  主要配列
    //
    //      $lva_table_nameTOid_list:      テーブル名配列
    //                                      [テーブル名]=MENU_ID
    //      $lva_table_nameTOPkeyname_list:テーブル 主キー名配列
    //                                      [テーブル名]=主キー名
    //      $lva_table_colnameTOid_list:   テーブル名+カラム名配列
    //                                      [テーブル名][カラム名]=代入値紐付主キー
    //      $lva_table_col_list:           カラム情報配列
    //                                      [テーブル名][カラム名]=>array("代入値紐付のカラム名"=>値)
    //      $lva_error_column_id_list:     代入値紐付の登録に不備がある主キーの配列
    //                                      [代入値紐付主キー]=1
    //      $lva_table_nameTOsql_list:     代入値紐付メニュー毎のSELECT文配列
    //                                      [テーブル名]=[SELECT文]
    //      $lva_vars_ass_chk_list:       オペ+作業+変数の組合せの列順序退避
    //                                        [$in_operation_id]
    //                                        [$in_patten_id]
    //                                        [$in_vars_link_id] = true/false:重複あり;
    //      $lva_vars_ass_list            変数で代入値管理の登録に必要な情報退避
    //                                        [$in_table_name][$in_col_name] = array(
    //                                        ['OPERATION_NO_UAPK']=>$in_operation_id,
    //                                        ['PATTERN_ID']=>$in_patten_id,
    //                                        ['MODULE_VARS_LINK_ID']=>$in_vars_link_id,
    //                                        ['VARS_ENTRY']=>$col_val,
    //                                        ['VAR_TYPE']=>$in_var_type,
    //                                        ['HCL_FLAG']=>$in_hcl_flag,
    //
    //  F0001  readValAssDB
    //  F0002  ColVarInfoAnalysis
    //  F0003  makeMenuSelectSQL
    //  F0004  GetMenuData
    //  F0005  DBGetMenuData
    //  F0006  makeVarsAssData
    //  F0007  chkVarsAssData
    //  F0008  addStg1VarsAssDB
    //  F0009  delVarsAssDB
    //  F0010  addStg2VarsAssDB
    //  F0011  chkKeyTypeColValue
    //  F0012  getIFInfoDB
    //  F0013  getNullDataHandlingID
    //
    ///////////////////////////////////////////////////////////////////////

    // 起動しているshellの起動判定を正常にするための待ち時間
    sleep(1);

    // カラムタイプ
    define("DF_COL_TYPE_VAL",               "1");   //Value型
    define("DF_COL_TYPE_KEY",               "2");   //Key型
    define("DF_COL_TYPE_KEYVAL",            "3");   //Key-Value型

    // 代入値紐付メニューSELECT時のITA独自カラム名
    define("DF_ITA_LOCAL_OPERATION_CNT"     ,"__ITA_LOCAL_COLUMN_1__");
    define("DF_ITA_LOCAL_HOST_CNT"          ,"__ITA_LOCAL_COLUMN_2__");
    define("DF_ITA_LOCAL_DUP_CHECK_ITEM"    ,"__ITA_LOCAL_COLUMN_3__");
    define("DF_ITA_LOCAL_PKEY"              ,"__ITA_LOCAL_COLUMN_4__");

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php      = '/libs/commonlibs/common_db_connect.php';
    $db_access_user_id   = -101804; //Terraform代入値自動登録設定プロシージャ

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $db_update_flg              = false;    // DB更新フラグ
    $g_null_data_handling_def   = "";
    $lv_a_proc_loaded_list_varsetup_pkey = 2100080001;
    $lv_a_proc_loaded_list_valsetup_pkey = 2100080002;

    // 代入値自動登録設定テーブル名(B_TERRAFORM_VAL_ASSIGN)
    $lv_val_assign_tbl      = $vg_terraform_val_assign_table_name;
    // 代入値自動登録設定VIEW名(D_TERRAFORM_VAL_ASSIGN)
    $lv_val_assign_view     = $vg_terraform_val_assign_view_name;
    // 作業パターン詳細テーブル名(B_TERRAFORM_PATTERN_LINK)
    $lv_pattern_link_tbl    = $vg_terraform_pattern_link_table_name;
    // 変数一覧テーブル名(B_TERRAFORM_MODULE_VARS_LINK)
    $lv_vars_master_tbl     = $vg_terraform_module_vars_link_table_name;
    // 作業パターン変数紐付VIEW名
    $lv_ptn_vars_link_view    = $vg_terraform_ptn_vars_link_view_name;
    // 代入値管理テーブル名(B_TERRAFORM_VARS_ASSIGN)
    $lv_vars_assign_tbl     = $vg_terraform_vars_assign_table_name;

    ////////////////////////////////
    // 作業実行単位のログ出力設定 //
    ////////////////////////////////
    $log_exec_workflow_flg = false;
    $log_exec_workflow_dir = "";

    ////////////////////////////////////////////////////////////////
    //----変数一覧(B_TERRAFORM_MODULE_VARS_LINK)
    ////////////////////////////////////////////////////////////////
    $strCurTableVarsMst          = $lv_vars_master_tbl;
    $strJnlTableVarsMst          = $strCurTableVarsMst . "_JNL";
    $strSeqOfCurTableVarsMst     = $strCurTableVarsMst . "_RIC";
    $strSeqOfJnlTableVarsMst     = $strCurTableVarsMst . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----作業パターン詳細(B_TERRAFORM_PATTERN_LINK)
    ////////////////////////////////////////////////////////////////
    $strCurTablePtnLnk           = $lv_pattern_link_tbl;
    $strJnlTablePtnLnk           = $strCurTablePtnLnk . "_JNL";
    $strSeqOfCurTablePtnLnk      = $strCurTablePtnLnk . "_RIC";
    $strSeqOfJnlTablePtnLnk      = $strCurTablePtnLnk . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----代入値管理
    ////////////////////////////////////////////////////////////////
    $strCurTableVarsAss          = $lv_vars_assign_tbl;
    $strJnlTableVarsAss          = $strCurTableVarsAss . "_JNL";
    $strSeqOfCurTableVarsAss     = $strCurTableVarsAss . "_RIC";
    $strSeqOfJnlTableVarsAss     = $strCurTableVarsAss . "_JSQ";


    $arrayConfigOfVarAss = array(
        "JOURNAL_SEQ_NO"        => "",
        "JOURNAL_ACTION_CLASS"  => "",
        "JOURNAL_REG_DATETIME"  => "",
        "ASSIGN_ID"             => "",
        "OPERATION_NO_UAPK"     => "",
        "PATTERN_ID"            => "",
        "MODULE_VARS_LINK_ID"   => "",
        "VARS_ENTRY"            => "",
        "HCL_FLAG"              => "",
        "SENSITIVE_FLAG"        => "",
        "ASSIGN_SEQ"            => "",
        "MEMBER_VARS"           => "",
        "DISP_SEQ"              => "",
        "DISUSE_FLAG"           => "",
        "ACCESS_AUTH"           => "",
        "NOTE"                  => "",
        "LAST_UPDATE_TIMESTAMP" => "",
        "LAST_UPDATE_USER"      => ""
    );
    $arrayValueTmplOfVarAss = $arrayConfigOfVarAss;

    ////////////////////////////////////////////////////////////////
    //----CMDB代入値紐付対象メニューリスト
    ////////////////////////////////////////////////////////////////
    $strCurTableMenu           = "B_CMDB_MENU_LIST";
    $strJnlTableMenu           = $strCurTableMenu . "_JNL";
    $strSeqOfCurTableMenu      = $strCurTableMenu . "_RIC";
    $strSeqOfJnlTableMenu      = $strCurTableMenu . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----CMDB代入値紐付対象メニュー管理
    ////////////////////////////////////////////////////////////////
    $strCurTableMenuTbl        = "B_CMDB_MENU_TABLE";
    $strJnlTableMenuTbl        = $strCurTableMenuTbl . "_JNL";
    $strSeqOfCurTableMenuTbl   = $strCurTableMenuTbl . "_RIC";
    $strSeqOfJnlTableMenuTbl   = $strCurTableMenuTbl . "_JSQ";

    $arrayConfigOfMenuTbl = array(
        "JOURNAL_SEQ_NO"=>""          ,
        "JOURNAL_ACTION_CLASS"=>""    ,
        "JOURNAL_REG_DATETIME"=>""    ,
        "TABLE_ID"=>""            ,
        "MENU_ID"=>""                 ,
        "TABLE_NAME"=>""              ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
        "NOTE"=>""                    ,
        "LAST_UPDATE_TIMESTAMP"=>""   ,
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmplOfMenuTbl = array(
        "JOURNAL_SEQ_NO"=>""          ,
        "JOURNAL_ACTION_CLASS"=>""    ,
        "JOURNAL_REG_DATETIME"=>""    ,
        "TABLE_ID"=>""            ,
        "MENU_ID"=>""                 ,
        "TABLE_NAME"=>""              ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
        "NOTE"=>""                    ,
        "LAST_UPDATE_TIMESTAMP"=>""   ,
        "LAST_UPDATE_USER"=>""
    );
    //CMDB代入値紐付対象メニュー管理

    ////////////////////////////////////////////////////////////////
    //----CMDB代入値紐付対象メニューカラム管理
    ////////////////////////////////////////////////////////////////
    $strCurTableMenuCol      = "B_CMDB_MENU_COLUMN";
    $strJnlTableMenuCol      = $strCurTableMenuCol . "_JNL";
    $strSeqOfCurTableMenuCol = $strCurTableMenuCol . "_RIC";
    $strSeqOfJnlTableMenuCol = $strCurTableMenuCol . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----CMDB代入値紐付テーブル
    ////////////////////////////////////////////////////////////////
    $strCurTableValAss      = $lv_val_assign_tbl;
    $strJnlTableValAss      = $strCurTableValAss . "_JNL";
    $strSeqOfCurTableValAss = $strCurTableValAss . "_RIC";
    $strSeqOfJnlTableValAss = $strCurTableValAss . "_JSQ";

    $arrayConfigOfValAss = array(
        "JOURNAL_SEQ_NO"=>""          ,
        "JOURNAL_ACTION_CLASS"=>""    ,
        "JOURNAL_REG_DATETIME"=>""    ,
        "COLUMN_ID"=>""               ,
        "MENU_ID"=>""                 ,
        "COLUMN_LIST_ID"=>""          ,
        "COL_TYPE"=>""                ,
        "PATTERN_ID"=>""              ,
        "VAL_VARS_LINK_ID"=>""        ,
        "KEY_VARS_LINK_ID"=>""        ,
        "HCL_FLAG"=>""                ,
        "NULL_DATA_HANDLING_FLG"=>""  ,
        "KEY_ASSIGN_SEQ"=>""          ,
        "VAL_ASSIGN_SEQ"=>""          ,
        "KEY_MEMBER_VARS"=>""         ,
        "VAL_MEMBER_VARS"=>""         ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
        "ACCESS_AUTH"=>""             ,
        "NOTE"=>""                    ,
        "LAST_UPDATE_TIMESTAMP"=>""   ,
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmplOfValAss = array(
        "JOURNAL_SEQ_NO"=>""          ,
        "JOURNAL_ACTION_CLASS"=>""    ,
        "JOURNAL_REG_DATETIME"=>""    ,
        "COLUMN_ID"=>""               ,
        "MENU_ID"=>""                 ,
        "COLUMN_LIST_ID"=>""          ,
        "COL_TYPE"=>""                ,
        "PATTERN_ID"=>""              ,
        "VAL_VARS_LINK_ID"=>""        ,
        "KEY_VARS_LINK_ID"=>""        ,
        "HCL_FLAG"=>""                ,
        "NULL_DATA_HANDLING_FLG"=>""  ,
        "KEY_ASSIGN_SEQ"=>""          ,
        "VAL_ASSIGN_SEQ"=>""          ,
        "KEY_MEMBER_VARS"=>""         ,
        "VAL_MEMBER_VARS"=>""         ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
        "ACCESS_AUTH"=>""             ,
        "NOTE"=>""                    ,
        "LAST_UPDATE_TIMESTAMP"=>""   ,
        "LAST_UPDATE_USER"=>""
    );
    //CMDB代入値紐付対象メニュー----


    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require_once ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require_once ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50003");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースが更新されバックヤード処理が必要か判定
        ///////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            //[処理]関連データベースに変更があるか確認
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-110001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $lv_UpdateRecodeInfo        = array();
        $ret = chkBackyardExecute($lv_a_proc_loaded_list_valsetup_pkey,
                                  $lv_UpdateRecodeInfo);

        if($ret === false) {
            $error_flag = 1;
            //関連データベースの変更があるか確認に失敗しました。
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-161010");
            throw new Exception($errorMsg);
        }

        if(count($lv_UpdateRecodeInfo) == 0) {
            // トレースメッセージ
            if($log_level === "DEBUG") {
                //[処理]関連データベースに変更がないので処理終了
                $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-110002");
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            exit(0);
        }

        // 投入オペレーション・Movement一覧のアクセス許可ロールを取得
        $lva_OpeAccessAuth_list     = array();
        $lva_PatternAccessAuth_list = array();
        $ret = getMasterAccessAuth($lva_OpeAccessAuth_list,$lva_PatternAccessAuth_list);
        if($ret === false) {
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171310"); //マスタデータのアクセス許可ロールの取得に失敗しました。
            throw new Exception($errorMsg);
        }

        // メニュー紐付けのメニュー・カラム情報取得
        $lva_CMDBMenuColumn_list = array();
        $lva_CMDBMenu_list       = array();
        $ret = getCMDBMenuMaster($lva_CMDBMenuColumn_list,$lva_CMDBMenu_list);
        if($ret === false) {
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171310"); //マスタデータのアクセス許可ロールの取得に失敗しました。
            throw new Exception($errorMsg);
        }

        $lv_RBAC = new RoleBasedAccessControl($objDBCA);

        //////////////////////////////////////////////////////////////////////////////////////
        // インターフェース情報からNULLデータを代入値管理に登録するかのデフォルト値を取得する。
        //////////////////////////////////////////////////////////////////////////////////////
        $lv_if_info = array();
        $error_msg   = "";
        $ret = getIFInfoDB($lv_if_info,$error_msg);
        if($ret === false) {
            $error_flag = 1;
            throw new Exception( $error_msg );
        }
        $g_null_data_handling_def = $lv_if_info["NULL_DATA_HANDLING_FLG"];

        ///////////////////////////////////////////////////////////////////////////
        // 代入値紐付管理からカラム毎の変数の情報を取得
        ///////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //「[処理]代入値自動登録設定からカラム毎の変数の情報を取得」
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120001");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        // テーブル名配列
        $lva_table_nameTOid_list    = array();
        // テーブル名+カラム名配列
        $lva_table_colnameTOid_list = array();
        // カラム情報配列
        $lva_table_col_list         = array();
        // 代入値紐付の登録に不備がある主キーの配列
        $lva_error_column_id_list   = array();

        // 代入値紐付メニュー毎のSELECT文配列
        $lva_table_nameTOsql_list = array();

        $lva_vars_ass_list           = array();
        $lva_vars_ass_chk_list       = array();
        $lva_table_nameTOPkeyname_list = array();

        $ret = readValAssDB($lv_val_assign_view,
                            $lv_pattern_link_tbl,
                            $lv_vars_master_tbl,
                            $lv_ptn_vars_link_view,
                            $lva_table_nameTOid_list,
                            $lva_table_colnameTOid_list,
                            $lva_table_col_list,
                            $lva_error_column_id_list,
                            $lva_table_nameTOPkeyname_list);
        if($ret === false){
            $error_flag = 1;
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171010");
            throw new Exception( $FREE_LOG );
        }

        ///////////////////////////////////////////////////////////////////////////////
        // P0003
        //   紐付メニューへのSELECT文を生成する。
        ///////////////////////////////////////////////////////////////////////////////
        makeMenuSelectSQL($lva_table_colnameTOid_list,
                          $lva_table_nameTOid_list,
                          $lva_error_column_id_list,
                          $lva_table_nameTOsql_list,
                          $lva_table_nameTOPkeyname_list);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //メッセージ修正「[処理]紐付対象メニューから具体値を取得」
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120002");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // P0004
        //   紐付メニューから具体値を取得する。
        ////////////////////////////////////////////////////////////////////////////////
        GetMenuData($lva_table_nameTOsql_list,
                    $lva_table_nameTOid_list,
                    $lva_table_col_list,
                    $lva_vars_ass_list,
                    $lva_vars_ass_chk_list,
                    $lva_error_column_id_list,
                    $warning_flag);

        // 不要となった配列変数を開放
        unset($lva_table_nameTOsql_list);
        unset($lva_table_nameTOid_list);
        unset($lva_table_col_list);
        unset($lva_error_column_id_list);
        unset($lva_table_nameTOPkeyname_list);
        unset($lva_table_colnameTOid_list);

        // メモリ最適化
        $ret = gc_mem_caches();

        ////////////////////////////////////////////////////////////////////////////////
        // トランザクション開始
        ////////////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            //[処理]トランザクション開始
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-130001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        if($objDBCA->transactionStart()===false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            //トランザクションスタートが失敗しました。
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-171250"));
        }


        ////////////////////////////////////////////////////////////////////////////////
        // 代入値管理のA_SEQUENCEレコードをロックする。
        ////////////////////////////////////////////////////////////////////////////////
        $ret = SequenceTableLock(array($strSeqOfCurTableVarsAss,$strSeqOfJnlTableVarsAss));
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            //シーケンスロックに失敗しました。
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171260");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // 代入値管理のデータを全件読み込む
        ////////////////////////////////////////////////////////////////////////////////
        $lv_VarsAssignRecodes = array();
        $ret = getVarsAssignRecodes($lv_VarsAssignRecodes);
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            //代入値管理の読込に失敗しました。
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171020");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////////////////////
        //  一般変数を紐付けている紐付メニューの具体値を代入値管理に登録
        ////////////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]一般変数を紐付けている紐付対象メニューの具体値を代入値管理に登録
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120003");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        $lva_ResultAccessAuthAndStr    = array();
        foreach($lva_vars_ass_list as $vars_ass_list){
            // 処理対象外のデータかを判定
            if($vars_ass_list['STATUS'] === false){
                continue;
            }

            // 代入値管理・作業対象ホストのアクセス許可ロールは、
            // オペレーション・機器一覧・Movement一覧のアクセス許可ロールのAND値の設定に変更
            $ope  = $vars_ass_list['OPERATION_NO_UAPK'];
            $mov  = $vars_ass_list['PATTERN_ID'];

            if(@count($lva_ResultAccessAuthAndStr[$ope][$mov]) != 0) {
                $ResultAccessAuthStr = $lva_ResultAccessAuthAndStr[$ope][$mov];
            } else {
                $AccessAuthAry   = array();
                $AccessAuthAry[] = $lva_OpeAccessAuth_list[$ope]['ACCESS_AUTH'];
                $AccessAuthAry[] = $lva_PatternAccessAuth_list[$mov]['ACCESS_AUTH'];
                $ResultAccessAuthStr = "";
                $ret = $lv_RBAC->AccessAuthExclusiveAND($AccessAuthAry,$ResultAccessAuthStr);
                if($ret === false) {
                    $ResultAccessAuthStr  = false;
                    $lva_ResultAccessAuthAndStr[$ope][$mov]  = false;
                } else {
                    $lva_ResultAccessAuthAndStr[$ope][$mov]  = $ResultAccessAuthStr;
                }
            }
            if($ResultAccessAuthStr === false) {
                if($log_level === "DEBUG") {
                    $OpeAccessAuthStr     = implode(",", $lva_OpeAccessAuth_list[$ope]['ACCESS_AUTH']);
                    $PatternAccessAuthStr = implode(",", $lva_PatternAccessAuth_list[$mov]['ACCESS_AUTH']);
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171320",
                                                         array($lva_CMDBMenu_list[$vars_ass_list['TABLE_NAME']],
                                                               $lva_CMDBMenuColumn_list[$vars_ass_list['TABLE_NAME']][$vars_ass_list['COL_NAME']],
                                                               $lva_OpeAccessAuth_list[$ope]['NAME'],
                                                               $OpeAccessAuthStr,
                                                               $lva_PatternAccessAuth_list[$mov]['NAME'],
                                                               $PatternAccessAuthStr));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                continue;
            }

            // 代入値管理に設定するアクセス許可ロールを上書き
            $vars_ass_list['ACCESS_AUTH'] = $ResultAccessAuthStr;

            // 代入値管理に具体値を登録
            $ret = addStg1VarsAssDB($vars_ass_list,$lv_VarsAssignRecodes);
            if($ret === false){
                // 異常フラグON
                $error_flag = 1;
                //代入値管理への変数の具体値登録に失敗しました。
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171030");
                throw new Exception( $FREE_LOG );
            }
        }

        ////////////////////////////////////////////////////////////////////////////////
        //   代入値管理から不要なデータを削除する
        ////////////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]代入値管理から不要なデータを削除
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120004");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        $ret = delVarsAssDB($lv_VarsAssignRecodes);
        if($ret === false){
            // 異常フラグON
            $error_flag = 1;
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171040");
            throw new Exception( $FREE_LOG );
        }

        unset($lva_vars_ass_list);
        unset($lva_vars_ass_chk_list);
        unset($lv_VarsAssignRecodes);

        // メモリ最適化
        $ret = gc_mem_caches();

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            //[処理]コミット
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-130003");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $r = $objDBCA->transactionCommit();
        if(!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            //トランザクションのコミットに失敗しました。
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171270");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション終了                                       //
        ////////////////////////////////////////////////////////////////
        $objDBCA->transactionExit();

        // トレースメッセージ
        if($log_level === "DEBUG") {
            //[処理]トランザクション終了
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-130002");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション開始                                       //
        ////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            //[処理]トランザクション開始
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-130001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        if($objDBCA->transactionStart()===false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-171250"));
        }

        // メモリ最適化
        $ret = gc_mem_caches();

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            //[処理]コミット
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-130003");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            //トランザクションのコミットに失敗しました。
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-171270"));
        }

        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]トランザクション終了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-130002");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースの更新反映を登録
        ///////////////////////////////////////////////////////////////////////////
        if($log_level === "DEBUG") {
            //[処理]関連データベースの更新の反映完了を登録
            $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-110003");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $ret = setBackyardExecuteComplete($lv_UpdateRecodeInfo);
        if($ret === false) {
            $error_flag = 1;
            //関連データベースの更新の反映完了の登録に失敗しました。
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-161020");
            throw new Exception($errorMsg);
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースを更新している場合、変数刈取りのバックヤード起動を登録
        ///////////////////////////////////////////////////////////////////////////
        if($db_update_flg === true) {
            if($log_level === "DEBUG") {
                //[処理]関連データベースを更新したのでバックヤード処理(varsautolistup-workflow)の起動を登録
                $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-110005");
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            $ret = setBackyardExecute($lv_a_proc_loaded_list_varsetup_pkey);
            if($ret === false) {
                $error_flag = 1;
                //バックヤード処理(varsautolistup-workflow)起動の登録に失敗しました。
                $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-161040");
                throw new Exception($errorMsg);
            }
        }
    }
    catch (Exception $e){
        //例外が発生しました。
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171280");
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        // 例外メッセージ出力
        $FREE_LOG = $e->getMessage();
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);

        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                //[処理]ロールバック
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-130004");
            }
            else{
                //ロールバックに失敗しました。
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171290");
            }
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-130002");
            }
            else{
                //トランザクションの終了時に異常が発生しました。
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171300");
            }
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
    }

    //メモリ使用量確認
    //$FREE_LOG = 'memory_get_peak_usage:[' . memory_get_peak_usage(true) . "/" . memory_get_usage() . ']';
    //LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        exit(0);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        exit(0);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            //プロシージャ終了(正常)
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        exit(0);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   代入値紐付からカラム情報を取得する。
    //
    // パラメータ
    //   $in_val_assign_view:            代入値自動登録VIEW名
    //   $in_pattern_link_tbl:           作業パターン詳細テーブル名
    //   $in_vars_master_tbl:            変数一覧テーブル名
    //   $in_ptn_vars_link_view          作業パターン変数紐付VIEW名
    //   &$ina_table_nameTOid_list:      テーブル名配列
    //                                   [テーブル名]=MENU_ID
    //   &$ina_table_colnameTOid_list:   テーブル名+カラム名配列
    //                                   [テーブル名][カラム名]=代入値紐付主キー
    //   &$ina_table_col_list:           カラム情報配列
    //                                   [テーブル名][カラム名]=>array("代入値紐付のカラム名"=>値)
    //   &$ina_error_column_id_list:     代入値紐付の登録に不備がある主キーの配列
    //                                   [代入値紐付主キー]=1
    //   &$ina_table_nameTOPkeyname_list:テーブル主キー名配列
    //                                   [テーブル名]=主キー名
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function readValAssDB($in_val_assign_view,
                          $in_pattern_link_tbl,
                          $in_vars_master_tbl,
                          $in_ptn_vars_link_view,
                          &$ina_table_nameTOid_list,
                          &$ina_table_colnameTOid_list,
                          &$ina_table_col_list,
                          &$ina_error_column_id_list,
                          &$ina_table_nameTOPkeyname_list)
    {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        $cmdb_menu_column_tbl = 'B_CMDB_MENU_COLUMN';
        $in_ptn_vars_link_view_vfp = $in_ptn_vars_link_view . "_VFP";

        $sql =            " SELECT                                                           \n";
        $sql = $sql .     "   TBL_A.COLUMN_ID                                             ,  \n";
        $sql = $sql .     "   TBL_A.MENU_ID                                               ,  \n";
        $sql = $sql .     "   TBL_C.TABLE_NAME                                            ,  \n";
        $sql = $sql .     "   TBL_C.PKEY_NAME                                             ,  \n";
        $sql = $sql .     "   TBL_C.DISUSE_FLAG  AS TBL_DISUSE_FLAG                       ,  \n";
        $sql = $sql .     "   TBL_A.COLUMN_LIST_ID                                        ,  \n";
        $sql = $sql .     "   TBL_B.COL_NAME                                              ,  \n";
        $sql = $sql .     "   TBL_B.COL_CLASS                                             ,  \n";
        $sql = $sql .     "   TBL_B.COL_TITLE                                             ,  \n";
        $sql = $sql .     "   TBL_B.REF_TABLE_NAME                                        ,  \n";
        $sql = $sql .     "   TBL_B.REF_PKEY_NAME                                         ,  \n";
        $sql = $sql .     "   TBL_B.REF_COL_NAME                                          ,  \n";
        $sql = $sql .     "   TBL_B.DISUSE_FLAG  AS COL_DISUSE_FLAG                       ,  \n";
        $sql = $sql .     "   TBL_A.COL_TYPE                                              ,  \n";
        $sql = $sql .     "   TBL_A.NULL_DATA_HANDLING_FLG                                ,  \n";
        // 該当作業パターンの作業パターン詳細の登録確認
        $sql = $sql .     "   TBL_A.PATTERN_ID                                            ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_pattern_link_tbl                                       \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       TBL_A.PATTERN_ID  = PATTERN_ID AND                         \n";
        $sql = $sql .     "       DISUSE_FLAG = '0'                                          \n";
        $sql = $sql .     "   ) AS PATTERN_CNT                                            ,  \n";
        $sql = $sql .     "                                                                  \n";
        // 該当変数の変数一覧の登録確認
        $sql = $sql .     "   TBL_A.VAL_VARS_LINK_ID                                      ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       VARS_NAME                                                  \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_vars_master_tbl                                        \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       MODULE_VARS_LINK_ID = TBL_A.VAL_VARS_LINK_ID AND           \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS VAL_VARS_NAME                                          ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_ptn_vars_link_view                                     \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       PATTERN_ID    = TBL_A.PATTERN_ID        AND                \n";
        $sql = $sql .     "       MODULE_VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND         \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS VAL_PTN_VARS_LINK_CNT                                  ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_ptn_vars_link_view_vfp                                 \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       MODULE_PTN_LINK_ID = TBL_A.VAL_VARS_PTN_LINK_ID            \n";
        $sql = $sql .     "   ) AS UNIQUE_VAL_PTN_VARS_LINK_CNT                           ,  \n";
        $sql = $sql .     "   TBL_A.KEY_VARS_LINK_ID                                      ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       VARS_NAME                                                  \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_vars_master_tbl                                        \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       MODULE_VARS_LINK_ID = TBL_A.KEY_VARS_LINK_ID AND           \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS KEY_VARS_NAME                                          ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_ptn_vars_link_view                                     \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       PATTERN_ID    = TBL_A.PATTERN_ID        AND                \n";
        $sql = $sql .     "       MODULE_VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND         \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS KEY_PTN_VARS_LINK_CNT                                  ,  \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_ptn_vars_link_view_vfp                                 \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       MODULE_PTN_LINK_ID = TBL_A.KEY_VARS_PTN_LINK_ID            \n";
        $sql = $sql .     "   ) AS UNIQUE_KEY_PTN_VARS_LINK_CNT                           ,  \n";
        $sql = $sql .     "   TBL_A.HCL_FLAG                                              ,  \n";
        // 追記
        $sql = $sql .     "   TBL_A.KEY_ASSIGN_SEQ                                        ,  \n";
        $sql = $sql .     "   TBL_A.KEY_MEMBER_VARS                                       ,  \n";
        $sql = $sql .     "   TBL_A.VAL_ASSIGN_SEQ                                        ,  \n";
        $sql = $sql .     "   TBL_A.VAL_MEMBER_VARS                                          \n";
        // 追記
        $sql = $sql .     " FROM                                                             \n";
        $sql = $sql .     "   $in_val_assign_view TBL_A                                      \n";
        $sql = $sql .     "   LEFT JOIN $cmdb_menu_column_tbl TBL_B ON                       \n";
        $sql = $sql .     "          (TBL_A.COLUMN_LIST_ID = TBL_B.COLUMN_LIST_ID)           \n";
        $sql = $sql .     "   LEFT JOIN B_CMDB_MENU_TABLE  TBL_C ON                          \n";
        $sql = $sql .     "          (TBL_A.MENU_ID        = TBL_C.MENU_ID)                  \n";
        $sql = $sql .     " WHERE                                                            \n";
        $sql = $sql .     "   TBL_A.DISUSE_FLAG='0'                                          \n";
        $sql = $sql .     " ORDER BY TBL_A.COLUMN_ID                                         \n";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        while ( $row = $objQuery->resultFetch() ){

            // Value型変数の変数タイプを一般変数に設定
            $val_child_var_type = false;
            // Key型変数の変数タイプを一般変数に設定
            $key_child_var_type = false;

            // CMDB代入値紐付メニューが廃止されているか判定
            if($row['TBL_DISUSE_FLAG'] != '0'){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に登録されている紐付対象メニューが廃止されています。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171050",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューのカラムが廃止されているか判定
            if($row['COL_DISUSE_FLAG'] != '0'){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に登録されている紐付対象メニューの項目情報が廃止されています。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171060",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // 作業パターン詳細に作業パターンが未登録
            if($row['PATTERN_CNT'] == 0){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に登録されているMovementがMovement詳細に登録されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171070",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }


            // CMDB代入値紐付メニューが登録されているか判定
            if(@strlen($row['TABLE_NAME']) == 0){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に登録されている紐付対象メニューのテーブル名が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171080",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューの主キーが登録されているか判定
            if(@strlen($row['PKEY_NAME']) == 0){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に紐付く紐付対象メニューの主キー名が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171090",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューのカラムが未登録か判定
            if(@strlen($row['COL_NAME']) == 0){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に登録されている紐付対象メニューの項目情報が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171100",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューのカラムタイトルが未登録か判定
            if(@strlen($row['COL_TITLE']) == 0){
                if ( $log_level === 'DEBUG' ){
                    //代入値自動登録設定に登録されている紐付対象メニューの項目名が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171110",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }
            //カラムタイプ判定
            $val_type_chk = false;
            $key_type_chk = false;
            $col_type     = $row['COL_TYPE'];
            switch($col_type){
            case DF_COL_TYPE_VAL:
                $val_type_chk = true;
                break;
            case DF_COL_TYPE_KEY:
                $key_type_chk = true;
                break;
            case DF_COL_TYPE_KEYVAL:
                $val_type_chk = true;
                $key_type_chk = true;
                break;
            default:
                if ( $log_level === 'DEBUG' ){
                    //
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171120",array($row['COLUMN_ID'],$row['COL_TYPE']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue 2;
            }
            //カラムタイプにより処理分岐
            switch($col_type){
            case DF_COL_TYPE_VAL:
            case DF_COL_TYPE_KEYVAL:
                //Value型に設定されている変数設定確認
                $ret = ColVarInfoAnalysis("Value",
                                          $val_child_var_type,
                                          $row,
                                          "VAL_VARS_LINK_ID",
                                          "VAL_VARS_NAME",
                                          "VAL_PTN_VARS_LINK_CNT",
                                          "UNIQUE_VAL_PTN_VARS_LINK_CNT"
                                          );
                if($ret === false){
                    // 次のカラムへ
                    continue 2;
                }
                break;
            }
            switch($col_type){
            case DF_COL_TYPE_KEY:
            case DF_COL_TYPE_KEYVAL:
                //Key型に設定されている変数設定確認
                $ret = ColVarInfoAnalysis("Key",
                                          $key_child_var_type,
                                          $row,
                                          "KEY_VARS_LINK_ID",
                                          "KEY_VARS_NAME",
                                          "KEY_PTN_VARS_LINK_CNT",
                                          "UNIQUE_KEY_PTN_VARS_LINK_CNT"
                                          );
                if($ret === false){
                    // 次のカラムへ
                    continue 2;
                }
                break;
            }
            $ina_table_nameTOid_list[$row['TABLE_NAME']]=$row['MENU_ID'];
            $ina_table_colnameTOid_list[$row['TABLE_NAME']][$row['COL_NAME']][]=$row['COLUMN_ID'];
            // #1207 Update start 同じカラムに複数の変数を割り当てた場合の対応
            $ina_table_col_list[$row['TABLE_NAME']][$row['COL_NAME']][] =
                               array('COLUMN_ID'=>$row['COLUMN_ID'],
                                     'COL_TYPE'=>$row['COL_TYPE'],
                                     'COL_CLASS'=>$row['COL_CLASS'],
                                     'COL_TITLE'=>$row['COL_TITLE'],
                                     'REF_TABLE_NAME'=>$row['REF_TABLE_NAME'],
                                     'REF_PKEY_NAME'=>$row['REF_PKEY_NAME'],
                                     'REF_COL_NAME'=>$row['REF_COL_NAME'],
                                     'PATTERN_ID'=>$row['PATTERN_ID'],
                                     'VAL_VARS_LINK_ID'=>$row['VAL_VARS_LINK_ID'],
                                     'VAL_VARS_NAME'=>$row['VAL_VARS_NAME'],
                                     'KEY_VARS_LINK_ID'=>$row['KEY_VARS_LINK_ID'],
                                     'KEY_VARS_NAME'=>$row['KEY_VARS_NAME'],
                                     'VAL_VAR_TYPE'=>$val_child_var_type,
                                     'KEY_VAR_TYPE'=>$key_child_var_type,
                                     'KEY_ASSIGN_SEQ'=>$row['KEY_ASSIGN_SEQ'],
                                     'KEY_MEMBER_VARS'=>$row['KEY_MEMBER_VARS'],
                                     'VAL_ASSIGN_SEQ'=>$row['VAL_ASSIGN_SEQ'],
                                     'VAL_MEMBER_VARS'=>$row['VAL_MEMBER_VARS'],
                                     'NULL_DATA_HANDLING_FLG'=>$row['NULL_DATA_HANDLING_FLG'],
                                     'HCL_FLAG'=>$row['HCL_FLAG']
                               );

            // テーブルの主キー名退避
            $ina_table_nameTOPkeyname_list[$row['TABLE_NAME']]=$row['PKEY_NAME'];

        }
        // DBアクセス事後処理
        unset($objQuery);
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   設定されている変数設定確認する。
    //
    // パラメータ
    //   $in_col_type:            カラムタイプ Value/Key
    //   $in_child_var_type:      配列変数区分
    //                            true:配列変数   false:一般変数
    //   $row:                    クエリ配列
    //   $in_vars_link_id:        クエリ配列内のKey/Value型の変数IDキー
    //                            VAL_VARS_LINK_ID/KEY_VARS_LINK_ID
    //   $in_vars_name:           クエリ配列内のKey/Value型の変数名キー
    //                            VAL_VARS_NAME/KEY_VARS_NAME
    //   $in_ptn_vars_link_cnt:   クエリ配列内のKey/Value型の作業パターン+変数の
    //                            作業パターン変数紐付の登録件数
    //                            VAL_PTN_VARS_LINK_CNT/KEY_PTN_VARS_LINK_CNT
    //
    //   $unique_in_ptn_vars_link_cnt:   クエリ配列内のKey/Value型の作業パターン*変数の
    //                                   作業パターン変数紐付の登録件数
    //                                   UNIQUE_VAL_PTN_VARS_LINK_CNT/UNIQUE_KEY_PTN_VARS_LINK_CNT
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function ColVarInfoAnalysis($in_col_type,           //カラムタイプ Value/Key
                                &$in_child_var_type,
                                $row,
                                $in_vars_link_id,
                                $in_vars_name,
                                $in_ptn_vars_link_cnt,
                                $in_unique_ptn_vars_link_cnt
                               ){
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        //変数の選択判定
        if(@strlen($row[$in_vars_link_id]) == 0){
            if ( $log_level === 'DEBUG' ){
                // 代入値紐付（項番:｛｝）のValue型の変数が未選択。
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171130",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }

        // 変数がModule変数紐付け管理にあるか判定
        if($row[$in_ptn_vars_link_cnt] == 0){
            if ( $log_level === 'DEBUG' ){
                //代入値自動登録設定に登録されている変数とMovementの組合せはMovement詳細でMovementを紐付けていないか、Movement詳細で紐付けているModuleでは使用されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{} 変数区分:{})
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171140",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }

        // Movementと変数の組み合わせが利用可能かどうか判定
        if($row[$in_unique_ptn_vars_link_cnt] == 0){
            if ( $log_level === 'DEBUG' ){
                //代入値自動登録設定に登録されている変数とMovementの組合せはMovement詳細でMovementを紐付けていないか、Movement詳細で紐付けているModuleでは使用されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{} 変数区分:{})
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171140",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }

        // 設定されている変数が変数一覧にあるか判定
        if(@strlen($row[$in_vars_name]) == 0){
            if ( $log_level === 'DEBUG' ){
                //代入値自動登録設定に登録されている変数はModuleに登録されているでは使用されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{} 変数区分:{})
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171150",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   代入値紐付メニューへのSELECT文を生成する。
    //
    // パラメータ
    //   $ina_table_colnameTOid_list:   テーブル名+カラム名配列
    //                                  [テーブル名][カラム名]=代入値紐付主キー
    //   $ina_table_nameTOid_list:      テーブル名配列
    //                                  [テーブル名]=MENU_ID
    //   $ina_error_column_id_list:     代入値紐付の登録に不備がある主キーの配列
    //                                  [代入値紐付主キー]=1
    //   $ina_table_nameTOsql_list:     代入値紐付メニュー毎のSELECT文配列
    //                                  [テーブル名][SELECT文]
    //   $ina_table_nameTOPkeyname_list:テーブル主キー名配列
    //                                  [テーブル名]=主キー名
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function makeMenuSelectSQL( $ina_table_colnameTOid_list,
                                $ina_table_nameTOid_list,
                                $ina_error_column_id_list,
                               &$ina_table_nameTOsql_list,
                                $ina_table_nameTOPkeyname_list)
    {
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        $ina_table_nameTOsql_list = array();

        // 投入オペレーションにオペーレーションIDが登録されているかを判定するSQL
        $opeid_chk_sql  = "( SELECT                                       \n" .
                          "    OPERATION_NO_UAPK                          \n" .
                          "  FROM                                         \n" .
                          "    C_OPERATION_LIST                           \n" .
                          "  WHERE                                        \n" .
                          "    OPERATION_NO_IDBH = TBL_A.OPERATION_ID     \n" .
                          ") AS  OPERATION_ID ,                           \n";

        // テーブル名+カラム名配列からテーブル名と配列名を取得
        foreach($ina_table_colnameTOid_list as $table_name=>$col_list){
            $pkey_name = $ina_table_nameTOPkeyname_list[$table_name];

            //B_CMDB_MENU_LISTから対象のMENU_IDのACCESS_AUTH_FLGを取得する
            $access_auth_flg ="";
            $access_auth_flg_chk_sql = "SELECT "                                            . "\n" .
                                       "ACCESS_AUTH_FLG "                                   . "\n" .
                                       "FROM "                                              . "\n" .
                                       "B_CMDB_MENU_LIST "                                  . "\n" .
                                       "WHERE "                                             . "\n" .
                                       "MENU_ID = " . $ina_table_nameTOid_list[$table_name];

            $objQuery = $objDBCA->sqlPrepare($access_auth_flg_chk_sql);
            if($objQuery->getStatus()===false){
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                LocalLogPrint(basename(__FILE__),__LINE__,$access_auth_flg_chk_sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                unset($objQuery);
                continue;
            }

            $r = $objQuery->sqlExecute();
            if (!$r){
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                LocalLogPrint(basename(__FILE__),__LINE__,$access_auth_flg_chk_sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                unset($objQuery);
                continue;
            }

            // fetch行数を取得
            $count = $objQuery->effectedRowCount();

            $col_val = "";
            // 0件ではない場合
            if(0 != $count){
                // fetch行を取得
                $tgt_row = $objQuery->resultFetch();
                $access_auth_flg = $tgt_row['ACCESS_AUTH_FLG'];
            }
            unset($objQuery);

            $make_sql = "";
            $col_name_sql = "";
            foreach($col_list as $col_name=>$col_id_list){
                foreach($col_id_list as $col_id){
                    // テーブル名+カラム名の情報にエラーがないか判定
                    if( isset($ina_error_column_id_list[$col_id])){
                        //次のカラムへ
                        continue;

                    }
                    if($col_name_sql == ""){
                        $col_name_sql =  ", TBL_A." . $col_name . " \n";
                    }
                }
                // SELECT文を生成
                if($make_sql == ""){
                    if($access_auth_flg == 1){
                        $make_sql = "SELECT "                                               . "\n" .
                                    $opeid_chk_sql                                          . "\n" .
                                    "  TBL_A." . $pkey_name . " AS " . DF_ITA_LOCAL_PKEY    . "\n" .
                                    ", TBL_A." . $col_name . " \n".
                                    ", TBL_A." . "ACCESS_AUTH" . " \n";
                    }else{
                        $make_sql = "SELECT "                                               . "\n" .
                                    $opeid_chk_sql                                          . "\n" .
                                    "  TBL_A." . $pkey_name . " AS " . DF_ITA_LOCAL_PKEY    . "\n" .
                                    ", TBL_A." . $col_name . " \n";
                    }
                }
                else{
                    if($col_name_sql != ""){
                        $make_sql = $make_sql . ", TBL_A." . $col_name . " \n";
                    }
                }
            }
            if($make_sql == ""){
                //SELECT対象の項目なし
                //エラーがあるのでスキップ
                if ( $log_level === 'DEBUG' ){
                    //カラム情報がない紐付対象メニューです。この紐付対象メニューは処理対象外にします。(MENU_ID:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171160",array($ina_table_nameTOid_list[$table_name]));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                //次のテーブルへ
                continue;
            }
            else{
                $make_sql = $make_sql . " FROM " . $table_name . " TBL_A \n";
                $make_sql = $make_sql . " WHERE DISUSE_FLAG = '0'";
            }
            //メニューテーブルのSELECT SQL文退避
            $ina_table_nameTOsql_list[$table_name] = $make_sql;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004
    // 処理内容
    //   CMDB代入値紐付対象メニューから具体値を取得する。
    //
    // パラメータ
    //   $ina_table_nameTOsql_list:     代入値紐付メニュー毎のSELECT文配列
    //                                  [テーブル名][SELECT文]
    //   $ina_table_nameTOid_list:      テーブル名配列
    //                                  [テーブル名]=MENU_ID
    //   $ina_table_col_list:           カラム情報配列
    //                                  [テーブル名][カラム名]=>array("代入値紐付のカラム名"=>値)
    //   $ina_vars_ass_list:            一般変数用 代入値登録情報配列
    //   $ina_vars_ass_chk_list:        一般変数用 代入順序重複チェック配列
    //   $warning_flag:                 警告フラグ
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function GetMenuData($ina_table_nameTOsql_list,
                         $ina_table_nameTOid_list,
                         $ina_table_col_list,
                         &$ina_vars_ass_list,
                         &$ina_vars_ass_chk_list,
                         $ina_error_column_id_list,
                         &$warning_flags){

        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        foreach($ina_table_nameTOsql_list as $table_name=>$sql){
            if ( $log_level === 'DEBUG' ){
                //[処理]紐付対象メニューから具体値を取得（MENU_ID:）
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-STD-120005",array($ina_table_nameTOid_list[$table_name]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }

            // 代入値紐付メニューがデータを取出す
            $total_row = array();
            $ret = DBGetMenuData($sql,$total_row);
            if($ret === false){
                //DBアクセスエラー
                if ( $log_level === 'DEBUG' ){
                    //紐付対象メニューの情報取得に失敗しました。この紐付対象メニューは処理対象外にします。(MENU_ID:{})
                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171170",array($ina_table_nameTOid_list[$table_name]));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }

                $warning_flag = true;
                //次のテーブルへ
                continue;
            }
            else{

                // 代入値紐付メニューに具体値の登録なし
                if( ! isset($total_row)){
                    //DBアクセスエラー
                    if ( $log_level === 'DEBUG' ){
                        //紐付対象メニューにデータが登録されていません。(MENU_ID:{})
                        $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171180",array($ina_table_nameTOid_list[$table_name]));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    //次のテーブルへ
                    continue;
                }

                foreach($total_row as $row){
                    // 代入値紐付メニューに登録されているオペレーションIDを確認
                    if(@strlen($row['OPERATION_ID']) == 0){
                        //オペレーションID未登録
                        if ( $log_level === 'DEBUG' ){
                            //紐付対象メニューにオペレーションIDのカラムが設定されていません。このレコードを処理対象外とします。(MENU_ID:{} 紐付対象メニュー 項番:{})
                            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171190",array($ina_table_nameTOid_list[$table_name],$row[DF_ITA_LOCAL_PKEY]));
                            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }

                        $warning_flag = true;
                        //次のデータへ
                        continue;
                    }
                    $operation_id = $row['OPERATION_ID'];

                    // 代入値管理・作業対象ホストのアクセス許可ロールは、
                    // オペレーション・機器一覧・Movement一覧のアクセス許可ロールのAND値の設定に変更
                    $access_auth = $row['ACCESS_AUTH'];

                    // 代入値紐付に登録されている変数に対応する具体値を取得する。
                    foreach($row as $col_name=>$col_val){

                        $col_val_key = $col_val;

                        switch($col_name){
                        // 具体値カラム以外を除外
                        case DF_ITA_LOCAL_OPERATION_CNT:
                        case DF_ITA_LOCAL_HOST_CNT:
                        case DF_ITA_LOCAL_DUP_CHECK_ITEM:
                        case 'OPERATION_ID':
                        case DF_ITA_LOCAL_PKEY:
                            continue 2;
                        }
                        //再度カラムをチェック
                        if( ! isset($ina_table_col_list[$table_name][$col_name])){
                            continue;
                        }
                        foreach($ina_table_col_list[$table_name][$col_name] as $ina_col_list){
                            if( isset($ina_error_column_id_list[$ina_col_list['COLUMN_ID']])){
                                continue;
                            }

                            // IDcolumnの場合は参照元から具体値を取得する
                            if("" != $ina_col_list['REF_TABLE_NAME']){
                                $sql = "";
                                $sql = $sql . "SELECT " . $ina_col_list['REF_COL_NAME'] . " ";
                                $sql = $sql . "FROM   " . $ina_col_list['REF_TABLE_NAME'] . " ";
                                $sql = $sql . "WHERE " . $ina_col_list['REF_PKEY_NAME'] . "=:" . $ina_col_list['REF_PKEY_NAME'] . " ";
                                $sql = $sql . " AND DISUSE_FLAG='0'";

                                $objQuery = $objDBCA->sqlPrepare($sql);
                                if($objQuery->getStatus()===false){
                                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                                    unset($objQuery);
                                    continue;
                                }

                                $objQuery->sqlBind(array($ina_col_list['REF_PKEY_NAME'] => $col_val_key));

                                $r = $objQuery->sqlExecute();
                                if (!$r){
                                    $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                                    unset($objQuery);
                                    continue;
                                }

                                // fetch行数を取得
                                $count = $objQuery->effectedRowCount();

                                $col_val = "";
                                // 0件ではない場合
                                if(0 != $count){
                                    // fetch行を取得
                                    $tgt_row = $objQuery->resultFetch();
                                    $col_val = $tgt_row[$ina_col_list['REF_COL_NAME']];
                                }
                                unset($objQuery);
                            }

                            // 代入値管理の登録に必要な情報を生成
                            makeVarsAssData($table_name,
                                            $col_name,
                                            $col_val,
                                            $ina_col_list['NULL_DATA_HANDLING_FLG'],
                                            $operation_id,
                                            $ina_col_list,
                                            $ina_vars_ass_list,
                                            $ina_vars_ass_chk_list,
                                            $ina_table_nameTOid_list[$table_name],
                                            $row[DF_ITA_LOCAL_PKEY],
                                            $access_auth);
                            //戻り値は判定しない
                        }
                    }
                }
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0005
    // 処理内容
    //   CMDB内の代入値紐付対象メニューからデータを取得
    //
    // パラメータ
    //   $in_sql:       代入値紐付対象メニューからデータを抽出するSQL文
    //   $ina_row:      抽出したデータ
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function DBGetMenuData($in_sql,&$ina_row){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $objMTS;
        global $objDBCA;

        $ina_row = array();
        $objQuery = $objDBCA->sqlPrepare($in_sql);
        if($objQuery->getStatus()===false){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$in_sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$in_sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }
        // FETCH行数を取得
        while ( $row = $objQuery->resultFetch() ){
            $ina_row[] = $row;
        }
        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0006
    // 処理内容
    //   CMDB代入値紐付対象メニューから具体値を取得する。
    //
    // パラメータ
    //   $in_table_name:                テーブル名
    //   $in_col_name:                  カラム名
    //   $in_col_val:                   カラムの具体値
    //   $in_null_data_handling_flg     代入値管理へのNULLデータ連携フラグ
    //   $in_operation_id:              オペレーションID
    //   $ina_col_list:                 カラム情報配列
    //   $ina_vars_ass_list:            一般変数用 代入値登録情報配列
    //   $ina_vars_ass_chk_list:        一般変数用 代入順序重複チェック配列
    //   $in_menu_id:                   紐付メニューID
    //   $in_row_id:                    紐付テーブル主キー値
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function makeVarsAssData($in_table_name,
                             $in_col_name,
                             $in_col_val,
                             $in_null_data_handling_flg,
                             $in_operation_id,
                             $ina_col_list,
                             &$ina_vars_ass_list,
                             &$ina_vars_ass_chk_list,
                             $in_menu_id,
                             $in_row_id,
                             $in_access_auth){
        global $log_level;
        global $objMTS;
        global $objDBCA;

        // 外部CMDBメニューでColumnGroupを使用したカラムを読取ると
        // ロードテーブルより読み取るとColumnGroup名/ColumnTitleになる。
        // 代入値管理への登録はColumnTitleのみにする。
        $col_name_array = explode("/",$ina_col_list['COL_TITLE']);
        if(($col_name_array === false) ||
           (count($col_name_array) == 1)){
            $col_name = $ina_col_list['COL_TITLE'];
        }
        else{
            $idx = count($col_name_array);
            $idx = $idx - 1;
            $col_name = $col_name_array[$idx];
        }

        //カラムタイプを判定
        switch($ina_col_list['COL_TYPE']){
        case DF_COL_TYPE_VAL:
            //具体値が空白か判定
            $ret = chkValueTypeColValue($in_col_val,
                                        $in_null_data_handling_flg,
                                        $in_table_name,$in_row_id,$ina_col_list['COL_TITLE']);
            if($ret === false){
                return;
            }

            // Value型カラムの場合
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_table_name,
                           $in_col_name,
                           $ina_col_list['VAL_VAR_TYPE'],
                           $in_operation_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['VAL_VARS_LINK_ID'],
                           $ina_col_list['VAL_MEMBER_VARS'],
                           $ina_col_list['VAL_ASSIGN_SEQ'],
                           $in_col_val,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           $ina_col_list['COL_CLASS'],
                           $ina_col_list['HCL_FLAG'],
                           'Value',
                           $in_row_id,
                           $in_access_auth);
            break;
        case DF_COL_TYPE_KEY:
            // Key型カラムの場合
            //具体値が空白か判定
            $ret = chkKeyTypeColValue($in_col_val,
                                      $in_table_name,
                                      $in_row_id,
                                      $ina_col_list['COL_TITLE']);
            if($ret === false){
                // 空白の場合処理対象外
                return;
            }
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_table_name,
                           $in_col_name,
                           $ina_col_list['KEY_VAR_TYPE'],
                           $in_operation_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['KEY_VARS_LINK_ID'],
                           $ina_col_list['KEY_MEMBER_VARS'],
                           $ina_col_list['KEY_ASSIGN_SEQ'],
                           $col_name,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           $ina_col_list['COL_CLASS'],
                           "1", // HCL_FLAGを固定で1
                           'Key',
                           $in_row_id,
                           $in_access_auth);
            break;
        case DF_COL_TYPE_KEYVAL:
            //具体値が空白か判定
            $ret = chkValueTypeColValue($in_col_val,
                                        $in_null_data_handling_flg,
                                        $in_table_name,$in_row_id,$ina_col_list['COL_TITLE']);
            if($ret === false){
                return;
            }
            // Key-Value型カラムの場合
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_table_name,
                           $in_col_name,
                           $ina_col_list['VAL_VAR_TYPE'],
                           $in_operation_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['VAL_VARS_LINK_ID'],
                           $ina_col_list['VAL_MEMBER_VARS'],
                           $ina_col_list['VAL_ASSIGN_SEQ'],
                           $in_col_val,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           $ina_col_list['COL_CLASS'],
                           $ina_col_list['HCL_FLAG'],
                           'Value',
                           $in_row_id,
                           $in_access_auth);

            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_table_name,
                           $in_col_name,
                           $ina_col_list['KEY_VAR_TYPE'],
                           $in_operation_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['KEY_VARS_LINK_ID'],
                           $ina_col_list['KEY_MEMBER_VARS'],
                           $ina_col_list['KEY_ASSIGN_SEQ'],
                           $col_name,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           $ina_col_list['COL_CLASS'],
                           "1", // HCL_FLAGを固定で1
                           'Key',
                           $in_row_id,
                           $in_access_auth);
            break;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0007
    // 処理内容
    //   CMDB代入値紐付対象メニューの情報から代入値管理に登録する情報を生成
    //
    // パラメータ
    //   $in_table_name:                テーブル名
    //   $in_col_name:                  カラム名
    //   $in_var_type:                  変数タイプ　
    //                                  true:配列/false:一般
    //   $in_operation_id:              オペレーションID
    //   $in_patten_id:                 パターンID
    //   $in_vars_link_id:              変数ID
    //   $in_child_vars_link_id:        メンバー変数ID
    //   $in_vars_assign_seq:           代入順序
    //   $in_col_val:                   具体値
    //   $ina_vars_ass_list:            一般変数用 代入値登録情報配列
    //   $ina_vars_ass_chk_list:        一般変数用 代入順序重複チェック配列
    //   $in_menu_id:                   紐付メニューID
    //   $in_column_id:                 代入値自動設定登録
    //   $in_hcl_flag                   HCL設定
    //   $in_key_value_vars_id          Value/Key
    //   $in_row_id:                    紐付テーブル主キー値
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function chkVarsAssData($in_table_name,
                            $in_col_name,
                            $in_var_type,
                            $in_operation_id,
                            $in_patten_id,
                            $in_vars_link_id,
                            $in_child_vars_link_id,
                            $in_vars_assign_seq,
                            $in_col_val,
                            &$ina_vars_ass_list,
                            &$ina_vars_ass_chk_list,
                            $in_menu_id,
                            $in_column_id,
                            $in_col_class,
                            $in_hcl_flag,
                            $in_key_value_vars_id,
                            $in_row_id,
                            $in_access_auth){
        global $log_level;
        global $objMTS;
        global $objDBCA;

        $chk_status = false;

        //オペ+作業+変数の組み合わせが重複していないか判断
        // メモ：オペ＋作業＋変数＋メンバー変数＋代入順序
        if( isset($ina_vars_ass_chk_list[$in_operation_id]
                                        [$in_patten_id]
                                        [$in_vars_link_id]
                                        [$in_child_vars_link_id]
                                        [$in_vars_assign_seq]
                                        )){

            // 既に登録されている
            $dup_info = $ina_vars_ass_chk_list[$in_operation_id]
                                              [$in_patten_id]
                                              [$in_vars_link_id]
                                              [$in_child_vars_link_id]
                                              [$in_vars_assign_seq];
            //代入値自動登録設定の項番:{}と項番:{}のオペレーションとホストが重複しています。代入値自動登録設定の項番:{}を処理対象外にしました。(オペレーションID:{} 変数区分:{})
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171200",array( $dup_info['COLUMN_ID'],
                                                                             $in_column_id,
                                                                             $in_column_id,
                                                                             $in_operation_id,
                                                                             $in_key_value_vars_id,
                                                                             $in_child_vars_link_id,
            $in_vars_assign_seq
                                                                            ));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
        }
        else{

            $chk_status = true;
            //オペ+作業+変数の組み合わせを退避
            $ina_vars_ass_chk_list[$in_operation_id]
                                  [$in_patten_id]
                                  [$in_vars_link_id]
                                  [$in_child_vars_link_id]
                                  [$in_vars_assign_seq]           = array('COLUMN_ID'=>$in_column_id);

        }
        // 代入値管理の登録に必要な情報退避
        $ina_vars_ass_list[] = array('TABLE_NAME'=>$in_table_name,
                                     'COL_NAME'=>$in_col_name,
                                     'COL_CLASS'=>$in_col_class,
                                     'OPERATION_NO_UAPK'=>$in_operation_id,
                                     'PATTERN_ID'=>$in_patten_id,
                                     'MODULE_VARS_LINK_ID'=>$in_vars_link_id,
                                     'VARS_ENTRY'=>$in_col_val,
                                     'VAR_TYPE'=>$in_var_type,
                                     'MEMBER_VARS' => $in_child_vars_link_id,
                                     'ASSIGN_SEQ' => $in_vars_assign_seq,
                                     'HCL_FLAG'=>$in_hcl_flag,
                                     'STATUS'=>$chk_status,
                                     'KEY_VALUE_VARS_ID'=>$in_key_value_vars_id,
                                     'ACCESS_AUTH'=>$in_access_auth);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0008
    // 処理内容
    //   代入値管理（一般変数）を更新する。
    //
    // パラメータ
    //   $in_varsAssignList:              代入値管理更新情報配列
    //   $in_VarsAssignRecodes:           代入値管理の全テータ配列
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addStg1VarsAssDB($ina_varsass_list,&$in_VarsAssignRecodes) {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global    $db_update_flg;

        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss; //B_TERRAFORM_VARS_ASSIGN
        $strJnlTable      = $strJnlTableVarsAss;
        $arrayConfig      = $arrayConfigOfVarAss;
        $arrayValue       = $arrayValueTmplOfVarAss;
        $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
        $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

        $key = $ina_varsass_list['OPERATION_NO_UAPK']   . "_" .
               $ina_varsass_list['PATTERN_ID']          . "_" .
               $ina_varsass_list['MODULE_VARS_LINK_ID'] . "_" .
               $ina_varsass_list['HCL_FLAG']            . "_" .
               $ina_varsass_list['MEMBER_VARS']         . "_" .
               $ina_varsass_list['ASSIGN_SEQ']          . "_" .
               $ina_varsass_list['ACCESS_AUTH']         . "_" .
               "0";

        // 代入値管理に登録されているか判定
        if( ! isset($in_VarsAssignRecodes[$key])) {
            return addStg2VarsAssDB($ina_varsass_list,$in_VarsAssignRecodes);
        }
        else{
            $action = "UPDATE";
            $tgt_row = $in_VarsAssignRecodes[$key];

            // 代入値管理に必要なレコードを削除
            unset($in_VarsAssignRecodes[$key]);


            // 具体値が変更になっているか判定する。
            if($tgt_row["VARS_ENTRY"]  == $ina_varsass_list['VARS_ENTRY']){
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    //[処理]代入値管理 更新不要 (OPERATION_ID:{} PATTERN_ID:{} MODULE_VARS_LINK_ID:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120009",
                                                  array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                        $ina_varsass_list['PATTERN_ID'],
                                                        $ina_varsass_list['MODULE_VARS_LINK_ID'],
                                                        $ina_varsass_list['MEMBER_VARS'],
                                                        $ina_varsass_list['ASSIGN_SEQ']
                                                  ));
                     LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }

                 //同一みなので処理終了
                 return true;
            }

            // 最終更新者が自分でない場合、更新処理はスキップする。
            if($tgt_row["LAST_UPDATE_USER"] != $db_access_user_id){
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    //[処理]代入値管理 最終更新者が自分でないので更新スキップ (OPERATION_ID:{} PATTERN_ID:{} MODULE_VARS_LINK_ID:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120012",
                                                        array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                              $ina_varsass_list['PATTERN_ID'],
                                                              $ina_varsass_list['MODULE_VARS_LINK_ID'],
                                                              $ina_varsass_list['MEMBER_VARS'],
                                                              $ina_varsass_list['ASSIGN_SEQ']
                                                        ));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                //更新処理はスキップ
                return true;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //[処理]代入値管理 更新 (OPERATION_ID:{} PATTERN_ID:{} MODULE_VARS_LINK_ID:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120008",
                                            array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                  $ina_varsass_list['PATTERN_ID'],
                                                  $ina_varsass_list['MODULE_VARS_LINK_ID'],
                                                  $ina_varsass_list['MEMBER_VARS'],
                                                  $ina_varsass_list['ASSIGN_SEQ']
                                            ));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }

        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["VARS_ENTRY"]       = $ina_varsass_list['VARS_ENTRY'];
            $tgt_row["HCL_FLAG"]         = $ina_varsass_list['HCL_FLAG'];
            $tgt_row["ACCESS_AUTH"]      = $ina_varsass_list['ACCESS_AUTH'];
            //パスワードカラムの場合、Sensitive設定をON(2)にする
            if($ina_varsass_list['COL_CLASS'] == "PasswordColumn" && $ina_varsass_list['KEY_VALUE_VARS_ID'] == "Value"){
                $tgt_row["SENSITIVE_FLAG"]   = 2; //ON
            }else{
                $tgt_row["SENSITIVE_FLAG"]   = 1; //OFF
            }
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
        }

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "ASSIGN_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $tgt_row,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if( $objQueryUtn->getStatus()===false ||
            $objQueryJnl->getStatus()===false ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   代入値管理から不要なレコードを廃止
    //
    // パラメータ
    //   $ina_VarsAssignRecodes:           代入値管理の全テータ配列
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function delVarsAssDB($in_VarsAssignRecodes) {

        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global    $db_update_flg;

        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss; //B_TERRAFORM_VARS_ASSIGN
        $strJnlTable      = $strJnlTableVarsAss;
        $arrayConfig      = $arrayConfigOfVarAss;
        $arrayValue       = $arrayValueTmplOfVarAss;
        $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
        $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

        $strPkey                = "ASSIGN_ID";

        foreach($in_VarsAssignRecodes as $key=>$tgt_row) {
            if($tgt_row['DISUSE_FLAG'] == '1')
            {
                //廃止レコードはなにもしない。
                continue;
            }

            // 最終更新者が自分でない場合、廃止処理はスキップする。
            if($tgt_row["LAST_UPDATE_USER"] != $db_access_user_id){
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    //[処理]代入値管理 最終更新者が自分でないので廃止スキップ (ASSIGN_ID:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120011",
                                                             array($tgt_row['ASSIGN_ID']));
                }

                //更新処理はスキップ
                continue;
            }

            // 具体値にテンプレート変数が記述されているか判定
            if($db_update_flg === false) {
               // テンプレート変数が記述されていることを記録
               $db_update_flg = true;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //[処理]代入値管理 廃止 (ASSIGN_ID:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120010",
                                                       array($tgt_row['ASSIGN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }


            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                 $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                 return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '1';
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;

            $temp_array = array();
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 $strPkey,
                                                 $strCurTable,
                                                 $strJnlTable,
                                                 $arrayConfig,
                                                 $tgt_row,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
            unset($objQueryUtn);
            unset($objQueryJnl);
        }
        return true;
    }

    function chkValueTypeColValue($in_col_val,
                                  $in_null_data_handling_flg,
                                  $in_table_name,$in_row_id,$in_menu_title){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global $lva_table_nameTOid_list;
        //具体値が空白の場合
        if(strlen($in_col_val)==0){
            // 具体値が空でも代入値管理NULLデータ連携が有効か判定する
            if(getNullDataHandlingID($in_null_data_handling_flg) != '1')
            {
                 // トレースメッセージ
                 if ( $log_level === 'DEBUG' ){
                    //紐付対象メニューの具体値が設定されていません。このレコードを処理対象外とします。(MENU_ID:{} 紐付対象メニュー 項番:{} 項目名:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171210",
                                          array($lva_table_nameTOid_list[$in_table_name],$in_row_id,$in_menu_title));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                 }
                 return false;
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0010
    // 処理内容
    //   代入値管理（一般変数）を更新する。
    //
    // パラメータ
    //   $in_varsAssignList:              代入値管理更新情報配列
    //   $in_VarsAssignRecodes:           代入値管理の全テータ配列
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addStg2VarsAssDB($ina_varsass_list,&$in_VarsAssignRecodes) {

        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global    $db_update_flg;

        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss; //B_TERRAFORM_VARS_ASSIGN
        $strJnlTable      = $strJnlTableVarsAss;
        $arrayConfig      = $arrayConfigOfVarAss;
        $arrayValue       = $arrayValueTmplOfVarAss;
        $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
        $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;


        $key = $ina_varsass_list['OPERATION_NO_UAPK']   . "_" .
               $ina_varsass_list['PATTERN_ID']          . "_" .
               $ina_varsass_list['MODULE_VARS_LINK_ID'] . "_" .
               $ina_varsass_list['MEMBER_VARS']         . "_" .
               $ina_varsass_list['ASSIGN_SEQ']          . "_" .
               $ina_varsass_list['HCL_FLAG']            . "_" .
               $ina_varsass_list['ACCESS_AUTH']         . "_" .
               "1";

        if(! isset($in_VarsAssignRecodes[$key]))
        {
             $action  = "INSERT";
             $tgt_row = $arrayValue;

             // トレースメッセージ
             if ( $log_level === 'DEBUG' ){
                //[処理]代入値管理 追加 (OPERATION_ID:{} PATTERN_ID:{} MODULE_VARS_LINK_ID:{})
                 $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120006",
                                                     array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                           $ina_varsass_list['PATTERN_ID'],
                                                           $ina_varsass_list['MODULE_VARS_LINK_ID'],
                                                           $ina_varsass_list['MEMBER_VARS'],
                                                           $ina_varsass_list['ASSIGN_SEQ']
                                                     ));
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }


        }
        else{
            // 廃止なので復活する。
            $action = "UPDATE";

            $tgt_row = $in_VarsAssignRecodes[$key];

            unset($in_VarsAssignRecodes[$key]);

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //[処理]代入値管理 復活 (OPERATION_ID:{} PATTERN_ID:{} MODULE_VARS_LINK_ID:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-120007",
                                                array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                      $ina_varsass_list['PATTERN_ID'],
                                                      $ina_varsass_list['MODULE_VARS_LINK_ID'],
                                                      $ina_varsass_list['MEMBER_VARS'],
                                                      $ina_varsass_list['ASSIGN_SEQ']
                                                ));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }


        }

        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["VARS_ENTRY"]       = $ina_varsass_list['VARS_ENTRY'];
            $tgt_row["MEMBER_VARS"]      = $ina_varsass_list['MEMBER_VARS'];
            $tgt_row["ASSIGN_SEQ"]       = $ina_varsass_list['ASSIGN_SEQ'];
            $tgt_row["HCL_FLAG"]         = $ina_varsass_list['HCL_FLAG'];
            $tgt_row["ACCESS_AUTH"]      = $ina_varsass_list['ACCESS_AUTH'];
            //パスワードカラムの場合、Sensitive設定をON(2)にする
            if($ina_varsass_list['COL_CLASS'] == "PasswordColumn" && $ina_varsass_list['KEY_VALUE_VARS_ID'] == "Value"){
                $tgt_row["SENSITIVE_FLAG"]   = 2; //ON
            }else{
                $tgt_row["SENSITIVE_FLAG"]   = 1; //OFF
            }
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // 登録する情報設定
            $tgt_row["ASSIGN_ID"]           = $retArray[0];
            $tgt_row['OPERATION_NO_UAPK']   = $ina_varsass_list['OPERATION_NO_UAPK'];
            $tgt_row['PATTERN_ID']          = $ina_varsass_list['PATTERN_ID'];
            $tgt_row['MODULE_VARS_LINK_ID'] = $ina_varsass_list['MODULE_VARS_LINK_ID'];
            $tgt_row["VARS_ENTRY"]          = $ina_varsass_list['VARS_ENTRY'];
            $tgt_row["MEMBER_VARS"]         = $ina_varsass_list['MEMBER_VARS'];
            $tgt_row["ASSIGN_SEQ"]          = $ina_varsass_list['ASSIGN_SEQ'];
            $tgt_row["HCL_FLAG"]            = $ina_varsass_list['HCL_FLAG'];
            $tgt_row["ACCESS_AUTH"]         = $ina_varsass_list['ACCESS_AUTH'];
            //パスワードカラムの場合、Sensitive設定をON(2)にする
            if($ina_varsass_list['COL_CLASS'] == "PasswordColumn" && $ina_varsass_list['KEY_VALUE_VARS_ID'] == "Value"){
                $tgt_row["SENSITIVE_FLAG"]   = 2; //ON
            }else{
                $tgt_row["SENSITIVE_FLAG"]   = 1; //OFF
            }
            $tgt_row["LAST_UPDATE_USER"]    = $db_access_user_id;
            $tgt_row["DISUSE_FLAG"]         = '0';


            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $db_access_user_id;

        }

        $temp_array = array();
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             $action,
                                             "ASSIGN_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $tgt_row,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if( $objQueryUtn->getStatus()===false ||
            $objQueryJnl->getStatus()===false ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }

        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        unset($objQueryUtn);
        unset($objQueryJnl);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0011
    // 処理内容
    //   具体値が空白かどうかを判定
    //
    // パラメータ
    //   $in_col_val:        具体値
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkKeyTypeColValue($in_col_val,$in_table_name,$in_row_id,$in_menu_title){
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global $lva_table_nameTOid_list;

        //具体値が空白の場合
        if(strlen($in_col_val)==0){
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                //紐付対象メニューの具体値が空白です。(MENU_ID:{} 紐付対象メニュー 項番:{} 項目名:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171230",
                                     array($lva_table_nameTOid_list[$in_table_name],$in_row_id,$in_menu_title));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }

            return false;
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0012
    // 処理内容
    //   インターフェース情報を取得する。
    //
    // パラメータ
    //   $ina_if_info:        インターフェース情報
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getIFInfoDB(&$ina_if_info,&$in_error_msg)
    {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        // SQL作成
        $sql = "SELECT * FROM B_TERRAFORM_IF_INFO WHERE DISUSE_FLAG = '0'";

        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $ina_if_info = $row;
        }
        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        // レコード無しの場合は「Terraformインタフェース情報」が登録されていない
        if( $num_of_rows === 0 ){
            if ( $log_level === 'DEBUG' ){
                //Terraformインタフェース情報レコード無し
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-171240");
                $in_error_msg  = $msgstr;
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            unset($objQuery);
            return false;
        }

        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0013
    // 処理内容
    //   パラメータシートの具体値がNULLの場合でも代入値管理ら登録するかを判定
    //
    // パラメータ
    //   $in_null_data_handling_flg:    代入値自動登録設定のNULL登録フラグ
    // 戻り値
    //   '1':有効    '2':無効
    ////////////////////////////////////////////////////////////////////////////////
    function getNullDataHandlingID($in_null_data_handling_flg)
    {
        global $g_null_data_handling_def;
        //代入値自動登録設定のNULL登録フラグ判定
        switch($in_null_data_handling_flg) {
        case '1':   // 有効
            $id = '1'; break;
        case '2':   // 無効
            $id = '2'; break;
        default:    // インターフェース情報に従う
            // インターフェース情報のNULL登録フラグ判定
            switch($g_null_data_handling_def) {
            case '1':   // 有効
                $id = '1'; break;
            case '2':   // 無効
                $id = '2'; break;
            }
        }
        return($id);
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0019
    // 処理内容
    //   代入値管理の情報を取得
    //
    // パラメータ
    //   $in_VarsAssignRecodes:      代入値管理に登録されている変数リスト
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getVarsAssignRecodes(&$in_VarsAssignRecodes) {

        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss; //B_TERRAFORM_VARS_ASSIGN
        $strJnlTable      = $strJnlTableVarsAss;

        $arrayConfig      = $arrayConfigOfVarAss;
        $arrayValue       = $arrayValueTmplOfVarAss;

        $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
        $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

        $strPkey                = "ASSIGN_ID";

        // 廃止レコードも含めてる。 WHERE句がないと全件とれない模様
        $temp_array = array('WHERE'=>"$strPkey<>'0'");

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $strPkey,
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array);

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn_sel = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQueryUtn_sel == null) {
            return false;
        }

        while($row = $objQueryUtn_sel->resultFetch()) {
            $key = $row["OPERATION_NO_UAPK"]   . "_" .
                   $row["PATTERN_ID"]          . "_" .
                   $row["MODULE_VARS_LINK_ID"] . "_" .
                   $row['HCL_FLAG']            . "_" .
                   $row["MEMBER_VARS"]         . "_" .
                   $row["ASSIGN_SEQ"]          . "_" .
                   $row['ACCESS_AUTH']         . "_" .
                   $row["DISUSE_FLAG"];
            $in_VarsAssignRecodes[$key] = $row;
        }

        unset($objQueryUtn_sel);
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   指定されたA_SEQUENCEのレコードをロックする。
    //
    // パラメータ
    //   $aryTgtOfSequenceLock:  ロック対象のシーケンス名のリスト
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function SequenceTableLock($aryTgtOfSequenceLock) {

        // デッドロック防止のために、昇順でロック
        asort($aryTgtOfSequenceLock);

        foreach($aryTgtOfSequenceLock as $strSeqName) {
            //ジャーナルのシーケンス
            $retArray = getSequenceLockInTrz($strSeqName, "A_SEQUENCE");
            if($retArray[1] != 0) {
                return false;
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   関連するデータベースが更新されバックヤード処理を実行する必要があるか判定
    //
    // パラメータ
    //   $in_a_proc_loaded_list_pkey: A_PROC_LOADED_LISTのROW_ID
    //   &$inout_UpdateRecodeInfo:    バックヤード処理を実行する必要がある場合
    //                                A_PROC_LOADED_LISTのROW_IDとLAST_UPDATE_TIMESTAMPを待避
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function chkBackyardExecute($in_a_proc_loaded_list_pkey,&$inout_UpdateRecodeInfo)
    {
        $inout_UpdateRecodeInfo = array();

        $sql =            " SELECT                                                            \n";
        $sql = $sql .     "   ROW_ID                                                      ,   \n";
        $sql = $sql .     "   LOADED_FLG                                                  ,   \n";
        $sql = $sql .     "   DATE_FORMAT(LAST_UPDATE_TIMESTAMP,'%Y%m%d%H%i%s%f') LAST_UPDATE_TIMESTAMP \n";
        $sql = $sql .     " FROM                                                              \n";
        $sql = $sql .     "   A_PROC_LOADED_LIST                                              \n";
        $sql = $sql .     " WHERE  ROW_ID = $in_a_proc_loaded_list_pkey  and (LOADED_FLG is NULL or LOADED_FLG <> '1') \n";


        $sqlUtnBody = $sql;
        $arrayUtnBind = array();
        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            return false;
        }

        while($row = $objQuery->resultFetch()) {
            // 代入値自動登録設定で更新されたレコード情報待避
            $inout_UpdateRecodeInfo['ROW_ID']                = $row['ROW_ID'];
            $inout_UpdateRecodeInfo['LAST_UPDATE_TIMESTAMP'] = $row['LAST_UPDATE_TIMESTAMP'];
        }
        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   関連するデータベースが更新さりれバックヤード処理が完了したことを記録
    //
    // パラメータ
    //   &$inout_UpdateRecodeInfo:    バックヤード処理が完了したことを記録する情報
    //                                A_PROC_LOADED_LISTのROW_IDとLAST_UPDATE_TIMESTAMP
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function setBackyardExecuteComplete($inout_UpdateRecodeInfo)
    {
        $sql =            " UPDATE A_PROC_LOADED_LIST SET                              \n";
        $sql = $sql .     "   LOADED_FLG = '1' ,LAST_UPDATE_TIMESTAMP = NOW(6)         \n";
        $sql = $sql .     " WHERE                                                      \n";
        $sql = $sql .     "   ROW_ID = :ROW_ID AND                                     \n";
        $sql = $sql .     "   DATE_FORMAT(LAST_UPDATE_TIMESTAMP,'%Y%m%d%H%i%s%f') = :LAST_UPDATE_TIMESTAMP \n";

        $sqlUtnBody = $sql;
        $arrayUtnBind = array("ROW_ID"=>$inout_UpdateRecodeInfo['ROW_ID'],
                              "LAST_UPDATE_TIMESTAMP"=>$inout_UpdateRecodeInfo['LAST_UPDATE_TIMESTAMP']);

        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            return false;
        }

        unset($objQuery);

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   バックヤード処理の起動が必要なことを記録
    //
    // パラメータ
    //   $row_id:                      バックヤード処理ID
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function setBackyardExecute($row_id)
    {
        $sql =            " UPDATE A_PROC_LOADED_LIST SET                              \n";
        $sql = $sql .     "   LOADED_FLG = '0' ,LAST_UPDATE_TIMESTAMP = NOW(6)         \n";
        $sql = $sql .     " WHERE                                                      \n";
        $sql = $sql .     "   ROW_ID = :ROW_ID                                         \n";

        $sqlUtnBody = $sql;
        $arrayUtnBind = array("ROW_ID"=>$row_id);

        $objQuery = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQuery == null) {
            return false;
        }

        unset($objQuery);

        return true;
    }
    // ExecuteしてFetch前のDBアクセスオブジェクトを返却
    function recordSelect($sqlUtnBody, $arrayUtnBind) {

        global    $objMTS;
        global    $objDBCA;

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if($objQueryUtn->getStatus()===false) {
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }
        $errstr = $objQueryUtn->sqlBind($arrayUtnBind);
        if($errstr != "") {
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $errstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }

        $r = $objQueryUtn->sqlExecute();
        if(!$r) {
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }

        return $objQueryUtn;
    }

    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        require ($root_dir_path . $log_output_php);
    }

    $beforeTime = 0;

    function TimeStampPrint($logdata)
    {
        global $beforeTime;
        $arrayStr = explode(" ", microtime());
        $micro = substr(str_replace("0.","",$arrayStr[0]),0,4);
        $strtime = date('Y-m-d H:i:s', $arrayStr[1]) . '.' . $micro;
        $unixtime = $arrayStr[1] . '.' . $micro;
        if($beforeTime != 0)
        {
            $difftime = "\t" . round(round($unixtime,4) - round($beforeTime,4),4);
            if($difftime == "0")
            {
                $difftime = "\t" . "0.0000";
            }
        }
        else
        {
            $difftime = "\t" . "0.0000";
        }
        $beforeTime  = $unixtime;
        LocalLogPrint("","","$strtime,$difftime," . $logdata);
        return array($strtime,$unixtime);
    }

    function getMasterAccessAuth(&$lva_OpeAccessAuth_list,&$lva_PatternAccessAuth_list) {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;
        $sqlAry          = array();
        $sqlKeyName      = array();
        $resultDataAry   = array();
        $sqlAry[] = "SELECT %s,%s,ACCESS_AUTH  FROM C_OPERATION_LIST";
        $sqlAry[] = "SELECT %s,%s,ACCESS_AUTH  FROM C_PATTERN_PER_ORCH";
        $sqlKeyName[] = "OPERATION_NAME";
        $sqlKeyName[] = "PATTERN_NAME";
        $sqlKeyId[] = "OPERATION_NO_UAPK";
        $sqlKeyId[] = "PATTERN_ID";
        $lva_OpeAccessAuth_list     = array();
        $lva_PatternAccessAuth_list = array();
        $resultDataAry[] = &$lva_OpeAccessAuth_list;
        $resultDataAry[] = &$lva_PatternAccessAuth_list;

        foreach($sqlAry as $no=>$sql) {

            // SQL準備
            $sql = sprintf($sql,$sqlKeyId[$no],$sqlKeyName[$no]);
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__)); //DBアクセス異常が発生しました。(file:{}line:{})
                $in_error_msg  = $msgstr;
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                return false;
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__)); //DBアクセス異常が発生しました。(file:{}line:{})
                $in_error_msg  = $msgstr;
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                unset($objQuery);
                return false;
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                $AccessAuthAry = array();
                if($row["ACCESS_AUTH"] != "") {
                    $AccessAuthAry = explode(",",$row["ACCESS_AUTH"]);
                }
                $resultDataAry[$no][$row[$sqlKeyId[$no]]] = array("NAME"=>$row[$sqlKeyName[$no]],
                                                                  "ACCESS_AUTH"=>$AccessAuthAry);
            }
        }
        return true;
    }

    function getCMDBMenuMaster(&$lva_CMDBMenuColumn_list,&$lva_CMDBMenu_list) {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;
        $lva_CMDBMenuColumn_list    = array();
        $lva_CMDBMenu_list          = array();
        // SQL準備
        $sql = " SELECT TAB_D.TABLE_NAME, "
             . "        TAB_E.COL_NAME,   "
             . "        TAB_E.COL_TITLE,  "
             . "        CONCAT(TAB_C.MENU_GROUP_NAME,':',TAB_B.MENU_NAME) MENU_NAME "
             . " FROM B_CMDB_MENU_LIST TAB_A "
             . " LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID) "
             . " LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID) "
             . " LEFT JOIN B_CMDB_MENU_TABLE TAB_D ON (TAB_A.MENU_ID = TAB_D.MENU_ID) "
             . " LEFT JOIN B_CMDB_MENU_COLUMN TAB_E ON (TAB_A.MENU_ID = TAB_E.MENU_ID) ";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__)); //DBアクセス異常が発生しました。(file:{}line:{})
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-152010",array(basename(__FILE__),__LINE__)); //DBアクセス異常が発生しました。(file:{}line:{})
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }

        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $lva_CMDBMenuColumn_list[$row['TABLE_NAME']][$row['COL_NAME']] = $row['COL_TITLE'];
            $lva_CMDBMenu_list[$row['TABLE_NAME']] = $row['MENU_NAME'];
        }
        return true;
    }

?>
