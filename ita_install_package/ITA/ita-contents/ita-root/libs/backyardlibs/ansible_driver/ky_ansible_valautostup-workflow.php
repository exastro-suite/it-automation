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
    //  【概要】
    //      代入値紐付を元に代入値管理を更新
    //      ●●●●●●●●●●●●●●●●●●●●●●●●●
    //      ※Legacy Roleの処理が入っているが、
    //        Legacy Roleは別ファイルで処理している。
    //      ●●●●●●●●●●●●●●●●●●●●●●●●●
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
    //      $lva_child_vars_ass_chk_list   オペ+作業+ホスト+配列変数+メンバ変数の組合せの列順序退避
    //                                      [$in_operation_id]
    //                                      [$in_patten_id]
    //                                      [$in_host_id]
    //                                      [$in_vars_link_id]
    //                                      [$in_child_vars_link_id]
    //                                      [$in_child_vars_col_seq] = true/false:重複あり;
    //
    //      $lva_child_vars_ass_list       配列変数で代入値管理の登録に必要な情報退避
    //                                      [$in_table_name][$in_col_name] = array(
    //                                      ['OPERATION_NO_UAPK']=>$in_operation_id,
    //                                      ['PATTERN_ID']=>$in_patten_id,
    //                                      ['SYSTEM_ID']=>$in_host_id,
    //                                      ['VARS_LINK_ID']=>$in_vars_link_id,
    //                                      ['CHILD_VARS_LINK_ID']=>$in_child_vars_link_id,
    //                                      ['CHILD_VARS_COL_SEQ']=>$in_child_vars_col_seq,
    //                                      ['VARS_ENTRY']=>$col_val,
    //                                      ['VAR_TYPE']=>$in_var_type);
    //      $lva_vars_ass_chk_list:       オペ+作業+ホスト+変数の組合せの列順序退避
    //                                      PIONEERの場合
    //                                        [$in_operation_id]
    //                                        [$in_patten_id]
    //                                        [$in_host_id]
    //                                        [$in_vars_link_id] = true/false:重複あり;
    //                                      PIONEER以外の場合
    //                                        [$in_operation_id]
    //                                        [$in_patten_id]
    //                                        [$in_host_id]
    //                                        [$in_vars_link_id]
    //                                        [$in_vars_assign_seq] = true/false:重複あり;
    //      $lva_vars_ass_list            変数で代入値管理の登録に必要な情報退避
    //                                      PIONEERの場合
    //                                        [$in_table_name][$in_col_name] = array(
    //                                        ['OPERATION_NO_UAPK']=>$in_operation_id,
    //                                        ['PATTERN_ID']=>$in_patten_id,
    //                                        ['SYSTEM_ID']=>$in_host_id,
    //                                        ['VARS_LINK_ID']=>$in_vars_link_id,
    //                                        ['VARS_ENTRY']=>$col_val,
    //                                        ['VAR_TYPE']=>$in_var_type);
    //                                      PIONEER以外の場合
    //                                        [$in_table_name][$in_col_name] = array(
    //                                        ['OPERATION_NO_UAPK']=>$in_operation_id,
    //                                        ['PATTERN_ID']=>$in_patten_id,
    //                                        ['SYSTEM_ID']=>$in_host_id,
    //                                        ['VARS_LINK_ID']=>$in_vars_link_id,
    //                                        ['ASSIGN_SEQ']=>$in_vars_assign_seq,
    //                                        ['VARS_ENTRY']=>$col_val,
    //                                        ['VAR_TYPE']=>$in_var_type);
    //
    //  F0001  readValAssDB
    //  F0002  ColVarInfoAnalysis
    //  F0003  AssSeqColSeqInfoAnalysis
    //  F0004  makeMenuSelectSQL
    //  F0006  GetMenuData
    //  F0007  DBGetMenuData
    //  F0008  makeVarsAssData
    //  F0009  chkVarsAssData 
    //  F0010  addStg1VarsAssDB
    //  F0012  delVarsAssDB
    //  F0013  addStg1PhoLnkDB
    //  F0014  delPhoLnkDB
    //  F0015  addStg2VarsAssDB
    //  F0016  addStg2PhoLnkDB
    //  F0017  getIFInfoDB
    //  F0018  getNullDataHandlingID
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
    // 作業実行単位のログ出力設定 //
    ////////////////////////////////
    $log_exec_workflow_flg = false;
    $log_exec_workflow_dir = "";

    switch($vg_driver_name){
    case DF_LEGACY_DRIVER:
        // DB更新ユーザー設定
        $db_access_user_id      = -100017;
        // 代入値紐付テーブル名
        $lv_val_assign_tbl      = 'B_ANS_LNS_VAL_ASSIGN';
        // 作業パターン詳細テーブル名
        $lv_pattern_link_tbl    = 'B_ANSIBLE_LNS_PATTERN_LINK';
        // 変数一覧テーブル名
        $lv_vars_master_tbl     = 'B_ANSIBLE_LNS_VARS_MASTER';
        // メンバー変数一覧テーブル名
        $lv_child_vars_tbl      = '';
        // 作業パターン変数紐付テーブル名
        $lv_ptn_vars_link_tbl   = 'B_ANS_LNS_PTN_VARS_LINK';
        // 代入値管理テーブル名
        $lv_vars_assign_tbl     = 'B_ANSIBLE_LNS_VARS_ASSIGN';
        // 作業対象ホストテーブル名
        $lv_pho_link_tbl        = 'B_ANSIBLE_LNS_PHO_LINK';

        $lv_a_proc_loaded_list_varsetup_pkey = 2100020001;
        $lv_a_proc_loaded_list_valsetup_pkey = 2100020002;
        break;
    case DF_PIONEER_DRIVER:
        // DB更新ユーザー設定
        $db_access_user_id      = -100018;
        // 代入値紐付テーブル名
        $lv_val_assign_tbl      = 'B_ANS_PNS_VAL_ASSIGN';
        // 作業パターン詳細テーブル名
        $lv_pattern_link_tbl    = 'B_ANSIBLE_PNS_PATTERN_LINK';
        // 変数一覧テーブル名
        $lv_vars_master_tbl     = 'B_ANSIBLE_PNS_VARS_MASTER';
        // メンバー変数一覧テーブル名
        $lv_child_vars_tbl      = '';
        // 作業パターン変数紐付テーブル名
        $lv_ptn_vars_link_tbl   = 'B_ANS_PNS_PTN_VARS_LINK';
        // 代入値管理テーブル名
        $lv_vars_assign_tbl     = 'B_ANSIBLE_PNS_VARS_ASSIGN';
        // 作業対象ホストテーブル名
        $lv_pho_link_tbl        = 'B_ANSIBLE_PNS_PHO_LINK';

        $lv_a_proc_loaded_list_varsetup_pkey = 2100020003;
        $lv_a_proc_loaded_list_valsetup_pkey = 2100020004;
        break;
    }

    ////////////////////////////////////////////////////////////////
    //----変数一覧
    ////////////////////////////////////////////////////////////////
    $strCurTableVarsMst          = $lv_vars_master_tbl;
    $strJnlTableVarsMst          = $strCurTableVarsMst . "_JNL";
    $strSeqOfCurTableVarsMst     = $strCurTableVarsMst . "_RIC";
    $strSeqOfJnlTableVarsMst     = $strCurTableVarsMst . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----メンバー変数一覧
    ////////////////////////////////////////////////////////////////
    $strCurTableChlVarsMst       = $lv_child_vars_tbl;
    $strJnlTableChlVarsMst       = $strCurTableChlVarsMst . "_JNL";
    $strSeqOfCurTableChlVarsMst  = $strCurTableChlVarsMst . "_RIC";
    $strSeqOfJnlTableChlVarsMst  = $strCurTableChlVarsMst . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----作業パターン詳細
    ////////////////////////////////////////////////////////////////
    $strCurTablePtnLnk           = $lv_pattern_link_tbl;
    $strJnlTablePtnLnk           = $strCurTablePtnLnk . "_JNL";
    $strSeqOfCurTablePtnLnk      = $strCurTablePtnLnk . "_RIC";
    $strSeqOfJnlTablePtnLnk      = $strCurTablePtnLnk . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----作業パターン変数紐付
    ////////////////////////////////////////////////////////////////
    $strCurTablePtnVarsLnk       = $lv_ptn_vars_link_tbl;
    $strJnlTablePtnVarsLnk       = $strCurTablePtnVarsLnk . "_JNL";
    $strSeqOfCurTablePtnVarsLnk  = $strCurTablePtnVarsLnk . "_RIC";
    $strSeqOfJnlTablePtnVarsLnk  = $strCurTablePtnVarsLnk . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----代入値管理
    ////////////////////////////////////////////////////////////////
    $strCurTableVarsAss          = $lv_vars_assign_tbl;
    $strJnlTableVarsAss          = $strCurTableVarsAss . "_JNL";
    $strSeqOfCurTableVarsAss     = $strCurTableVarsAss . "_RIC";
    $strSeqOfJnlTableVarsAss     = $strCurTableVarsAss . "_JSQ";

    switch($vg_driver_name){
    case DF_LEGACY_DRIVER:
        $arrayConfigOfVarAss = array(
            "JOURNAL_SEQ_NO"=>""          ,
            "JOURNAL_ACTION_CLASS"=>""    ,
            "JOURNAL_REG_DATETIME"=>""    ,
            "ASSIGN_ID"=>""               ,
            "OPERATION_NO_UAPK"=>""       ,
            "PATTERN_ID"=>""              ,
            "SYSTEM_ID"=>""               ,
            "VARS_LINK_ID"=>""            ,
            "VARS_ENTRY"=>""              ,
            "ASSIGN_SEQ"=>""              ,
            "DISP_SEQ"=>""                ,
            "DISUSE_FLAG"=>""             ,
            "NOTE"=>""                    ,
            "LAST_UPDATE_TIMESTAMP"=>""   ,
            "LAST_UPDATE_USER"=>""
        );
        $arrayValueTmplOfVarAss = $arrayConfigOfVarAss;
        break;
    case DF_PIONEER_DRIVER:
        $arrayConfigOfVarAss = array(
            "JOURNAL_SEQ_NO"=>""          ,
            "JOURNAL_ACTION_CLASS"=>""    ,
            "JOURNAL_REG_DATETIME"=>""    ,
            "ASSIGN_ID"=>""               ,
            "OPERATION_NO_UAPK"=>""       ,
            "PATTERN_ID"=>""              ,
            "SYSTEM_ID"=>""               ,
            "VARS_LINK_ID"=>""            ,
            "VARS_ENTRY"=>""              ,
            "ASSIGN_SEQ"=>""              ,
            "DISP_SEQ"=>""                ,
            "DISUSE_FLAG"=>""             ,
            "NOTE"=>""                    ,
            "LAST_UPDATE_TIMESTAMP"=>""   ,
            "LAST_UPDATE_USER"=>""
        );
        $arrayValueTmplOfVarAss = $arrayConfigOfVarAss;
        break;
    case DF_ROLE_DRIVER:
        $arrayConfigOfVarAss = array(
            "JOURNAL_SEQ_NO"=>""          ,
            "JOURNAL_ACTION_CLASS"=>""    ,
            "JOURNAL_REG_DATETIME"=>""    ,
            "ASSIGN_ID"=>""               ,
            "OPERATION_NO_UAPK"=>""       ,
            "PATTERN_ID"=>""              ,
            "SYSTEM_ID"=>""               ,
            "VARS_LINK_ID"=>""            ,
            "CHILD_VARS_LINK_ID"=>""      ,
            "VARS_ENTRY"=>""              ,
            "ASSIGN_SEQ"=>""              ,
            "CHILD_VARS_COL_SEQ"=>""      ,
            "DISP_SEQ"=>""                ,
            "DISUSE_FLAG"=>""             ,
            "NOTE"=>""                    ,
            "LAST_UPDATE_TIMESTAMP"=>""   ,
            "LAST_UPDATE_USER"=>""
        );
        $arrayValueTmplOfVarAss = $arrayConfigOfVarAss;
        break;
    }

    ////////////////////////////////////////////////////////////////
    //----作業対象ホスト
    ////////////////////////////////////////////////////////////////
    $strCurTablePhoLnk           = $lv_pho_link_tbl;
    $strJnlTablePhoLnk           = $strCurTablePhoLnk . "_JNL";
    $strSeqOfCurTablePhoLnk      = $strCurTablePhoLnk . "_RIC";
    $strSeqOfJnlTablePhoLnk      = $strCurTablePhoLnk . "_JSQ";

    $arrayConfigOfPhoLnk = array(
            "JOURNAL_SEQ_NO"=>""          ,
            "JOURNAL_ACTION_CLASS"=>""    ,
            "JOURNAL_REG_DATETIME"=>""    ,
            "PHO_LINK_ID"=>""             ,
            "OPERATION_NO_UAPK"=>""       ,
            "PATTERN_ID"=>""              ,
            "SYSTEM_ID"=>""               ,
            "DISP_SEQ"=>""                ,
            "DISUSE_FLAG"=>""             ,
            "NOTE"=>""                    ,
            "LAST_UPDATE_TIMESTAMP"=>""   ,
            "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOfPhoLnk = $arrayConfigOfPhoLnk;

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
        "VAL_CHILD_VARS_LINK_ID"=>""  ,
        "VAL_ASSIGN_SEQ"=>""          ,
        "VAL_CHILD_VARS_COL_SEQ"=>""  ,
        "KEY_VARS_LINK_ID"=>""        ,
        "KEY_CHILD_VARS_LINK_ID"=>""  ,
        "KEY_ASSIGN_SEQ"=>""          ,
        "KEY_CHILD_VARS_COL_SEQ"=>""  ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
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
        "VAL_CHILD_VARS_LINK_ID"=>""  ,
        "VAL_ASSIGN_SEQ"=>""          ,
        "VAL_CHILD_VARS_COL_SEQ"=>""  ,
        "KEY_VARS_LINK_ID"=>""        ,
        "KEY_CHILD_VARS_LINK_ID"=>""  ,
        "KEY_ASSIGN_SEQ"=>""          ,
        "KEY_CHILD_VARS_COL_SEQ"=>""  ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
        "NOTE"=>""                    ,
        "LAST_UPDATE_TIMESTAMP"=>""   ,
        "LAST_UPDATE_USER"=>""
    );
    //CMDB代入値紐付対象メニュー----

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php       = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php     = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php       = '/libs/commonlibs/common_db_connect.php';

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    $g_null_data_handling_def   = "";

    $db_update_flg              = false;    // DB更新フラグ

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


        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースが更新されバックヤード処理が必要か判定
        ///////////////////////////////////////////////////////////////////////////
        $lv_UpdateRecodeInfo        = array();
        $ret = chkBackyardExecute($lv_a_proc_loaded_list_valsetup_pkey,
                                  $lv_UpdateRecodeInfo);

        if($ret === false) {
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90303");
            throw new Exception($errorMsg);
        }

        if(count($lv_UpdateRecodeInfo) == 0) {
            // トレースメッセージ
            if($log_level === "DEBUG") {
                $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70053");
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            exit(0);
        }

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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70015");
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

        $lva_child_vars_ass_list     = array();
        $lva_child_vars_ass_chk_list = array();
        $lva_vars_ass_list           = array();
        $lva_vars_ass_chk_list       = array();
        $lva_table_nameTOPkeyname_list = array();

        $ret = readValAssDB($vg_driver_name,
                            $lv_val_assign_tbl,
                            $lv_pattern_link_tbl,  
                            $lv_vars_master_tbl,   
                            $lv_child_vars_tbl,    
                            $lv_ptn_vars_link_tbl, 
                            $lva_table_nameTOid_list,    
                            $lva_table_colnameTOid_list, 
                            $lva_table_col_list,         
                            $lva_error_column_id_list,
                            $lva_table_nameTOPkeyname_list);
        if($ret === false){
            $error_flag = 1;
     
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90032");
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70016");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ////////////////////////////////////////////////////////////////////////////////
        // P0004
        //   紐付メニューから具体値を取得する。
        ////////////////////////////////////////////////////////////////////////////////
        $ret = GetMenuData($vg_driver_name,
                           $lva_table_nameTOsql_list,
                           $lva_table_nameTOid_list,
                           $lva_table_col_list,
                           $lva_child_vars_ass_list,
                           $lva_child_vars_ass_chk_list,
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

        ////////////////////////////////////////////////////////////////////////////////
        // トランザクション開始
        ////////////////////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        if($objDBCA->transactionStart()===false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITAANSIBLEH-ERR-80001"));
        }


        ////////////////////////////////////////////////////////////////////////////////
        // 代入値管理のA_SEQUENCEレコードをロックする。
        ////////////////////////////////////////////////////////////////////////////////
        $ret = SequenceTableLock(array($strSeqOfCurTableVarsAss,$strSeqOfJnlTableVarsAss));
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80002");
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
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90301");
            throw new Exception($errorMsg);
        }

        $lva_pho_link_list        = array();

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70018");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        ////////////////////////////////////////////////////////////////////////////////
        //  一般変数を紐付けている紐付メニューの具体値を代入値管理に登録
        ////////////////////////////////////////////////////////////////////////////////
        foreach($lva_vars_ass_list as $vars_ass_list){
            // 処理対象外のデータかを判定
            if($vars_ass_list['STATUS'] === false){
                continue;
            }

            // 代入値管理に具体値を登録
            $ret = addStg1VarsAssDB($vars_ass_list,$lv_VarsAssignRecodes);
            if($ret === false){
                // 異常フラグON
                $error_flag = 1;
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90051");
                throw new Exception( $FREE_LOG );
            }
            // 作業対象ホストに登録が必要な情報を退避
            $lva_pho_link_list[$vars_ass_list['OPERATION_NO_UAPK']]
                              [$vars_ass_list['PATTERN_ID']]
                              [$vars_ass_list['SYSTEM_ID']] = 1;
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70019");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70020");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ////////////////////////////////////////////////////////////////////////////////
        //   代入値管理から不要なデータを削除する
        ////////////////////////////////////////////////////////////////////////////////
        $ret = delVarsAssDB($lv_VarsAssignRecodes);
        if($ret === false){
            // 異常フラグON
            $error_flag = 1;
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90053");
            throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if(!$r) {
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80003");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション終了                                       //
        ////////////////////////////////////////////////////////////////
        $objDBCA->transactionExit();

        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60002");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70021");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション開始                                       //
        ////////////////////////////////////////////////////////////////
        // トレースメッセージ
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60001");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        if($objDBCA->transactionStart()===false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITAANSIBLEH-ERR-80001"));
        }

        ////////////////////////////////////////////////////////////////
        // 対象ホスト管理のA_SEQUENCEレコードをロックする。           //
        ////////////////////////////////////////////////////////////////
        $ret = SequenceTableLock(array($strSeqOfCurTablePhoLnk,$strSeqOfJnlTablePhoLnk));
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80002");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////
        // 対象ホスト管理のデータを全件読込                           //
        ////////////////////////////////////////////////////////////////
        $lv_PhoLinkRecodes = array();
        $ret = getPhoLinkRecodes($lv_PhoLinkRecodes);
        if($ret === false) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90302");
            throw new Exception($errorMsg);
        }

        ////////////////////////////////////////////////////////////////////////////////
        //   代入値管理で登録したオペ+作業パターン+ホストが作業対象ホストに登録されている
        //   か判定し、未登録の場合は登録する。
        ////////////////////////////////////////////////////////////////////////////////
        foreach($lva_pho_link_list as $ope_id=>$ptn_list){
            foreach($ptn_list as $ptn_id=>$host_list){
                foreach($host_list as $host_id=>$dummy){
                    $pho_link_list = array('OPERATION_NO_UAPK'=>$ope_id,
                                           'PATTERN_ID'=>$ptn_id,
                                           'SYSTEM_ID'=>$host_id);
                    $out_pho_link_id = '';
                    $ret = addStg1PhoLnkDB( $pho_link_list, $lv_PhoLinkRecodes);

                    if($ret === false){
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90054");
                        throw new Exception( $FREE_LOG );
                    }
                }
            }
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70022");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
        ////////////////////////////////////////////////////////////////////////////////
        //   作業対象ホストから不要なデータを削除する
        ////////////////////////////////////////////////////////////////////////////////
        $ret = delPhoLnkDB($lv_PhoLinkRecodes);
        if($ret === false){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90055");
            throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80003"));
        }

        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60002");
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースの更新反映を登録
        ///////////////////////////////////////////////////////////////////////////
        if($log_level === "DEBUG") {
            $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70054");
            LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
        }

        $ret = setBackyardExecuteComplete($lv_UpdateRecodeInfo);
        if($ret === false) {
            $error_flag = 1;
            $ary[90304] = "関連データベースの更新の反映完了の登録に失敗しました。";
            $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90304");
            throw new Exception($errorMsg);
        }

        if($vg_driver_name == DF_LEGACY_DRIVER) {
            ///////////////////////////////////////////////////////////////////////////
            // 関連データベースを更新している場合、変数刈取りのバックヤード起動を登録
            ///////////////////////////////////////////////////////////////////////////
            if($db_update_flg === true) {
                if($log_level === "DEBUG") {
                    $traceMsg = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70056");
                    LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
                }
                $ret = setBackyardExecute($lv_a_proc_loaded_list_varsetup_pkey);
                if($ret === false) {
                    $error_flag = 1;
                    $errorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90306");
                    throw new Exception($errorMsg);
                }
            }
        }


    }
    catch (Exception $e){
        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80004");
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60004");
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80005");
            }
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-60002");
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80006");
            }
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
        }
    }

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
    //   $in_driver_name:                ドライバ区分
    //   $in_val_assign_tbl:             代入値紐付テーブル名
    //   $in_pattern_link_tbl:           作業パターン詳細テーブル名
    //   $in_vars_master_tbl:            変数一覧テーブル名
    //   $in_child_vars_tbl:             メンバー変数一覧テーブル名
    //   $in_ptn_vars_link_tbl:          作業パターン変数紐付テーブル名
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
    function readValAssDB($in_driver_name,       
                          $in_val_assign_tbl,    
                          $in_pattern_link_tbl,  
                          $in_vars_master_tbl,   
                          $in_child_vars_tbl,    
                          $in_ptn_vars_link_tbl, 
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

        $sql =            " SELECT                                                           \n";
        $sql = $sql .     "   TBL_A.COLUMN_ID                                             ,  \n";
        $sql = $sql .     "   TBL_A.MENU_ID                                               ,  \n";
        $sql = $sql .     "   TBL_C.TABLE_NAME                                            ,  \n";
        $sql = $sql .     "   TBL_C.PKEY_NAME                                             ,  \n";
        $sql = $sql .     "   TBL_C.DISUSE_FLAG  AS TBL_DISUSE_FLAG                       ,  \n";
        $sql = $sql .     "   TBL_A.COLUMN_LIST_ID                                        ,  \n";
        $sql = $sql .     "   TBL_B.COL_NAME                                              ,  \n";
        $sql = $sql .     "   TBL_B.COL_TITLE                                             ,  \n";
        $sql = $sql .     "   TBL_B.REF_TABLE_NAME                                        ,  \n";
        $sql = $sql .     "   TBL_B.REF_PKEY_NAME                                         ,  \n";
        $sql = $sql .     "   TBL_B.REF_COL_NAME                                          ,  \n";
        $sql = $sql .     "   TBL_B.DISUSE_FLAG  AS COL_DISUSE_FLAG                       ,  \n";
        $sql = $sql .     "   TBL_A.COL_TYPE                                              ,  \n";
        // 代入値管理データ連携フラグ
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
        $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
        $sql = $sql .     "         SELECT                                                   \n";
        $sql = $sql .     "           VARS_NAME_ID                                           \n";
        $sql = $sql .     "         FROM                                                     \n";
        $sql = $sql .     "           $in_ptn_vars_link_tbl                                  \n";
        $sql = $sql .     "         WHERE                                                    \n";
        $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
        $sql = $sql .     "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
        $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
        $sql = $sql .     "                        )                                         \n";
        $sql = $sql .     "       AND                                                        \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS VAL_VARS_NAME                                           , \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_ptn_vars_link_tbl                                      \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       PATTERN_ID    = TBL_A.PATTERN_ID        AND                \n";
        $sql = $sql .     "       VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND                \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS VAL_PTN_VARS_LINK_CNT                                  ,  \n";
        $sql = $sql .     "   TBL_A.VAL_CHILD_VARS_LINK_ID                                ,  \n";
        // ROLEの場合のみ該当メンバー変数のメンバー変数一覧の登録確認
        if($in_driver_name == DF_ROLE_DRIVER){
            $sql = $sql . "   (                                                              \n";
            $sql = $sql . "     SELECT                                                       \n";
            $sql = $sql . "       CHILD_VARS_NAME                                            \n";
            $sql = $sql . "     FROM                                                         \n";
            $sql = $sql . "       $in_child_vars_tbl                                         \n";
            $sql = $sql . "     WHERE                                                        \n";
            $sql = $sql . "       PARENT_VARS_NAME_ID IN (                                   \n";
            $sql = $sql . "         SELECT                                                   \n";
            $sql = $sql . "           VARS_NAME_ID                                           \n";
            $sql = $sql . "         FROM                                                     \n";
            $sql = $sql . "           $in_ptn_vars_link_tbl                                  \n";
            $sql = $sql . "         WHERE                                                    \n";
            $sql = $sql . "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
            $sql = $sql . "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
            $sql = $sql . "           DISUSE_FLAG   = '0'                                    \n";
            $sql = $sql . "         )                                                        \n";
            $sql = $sql . "       AND                                                        \n";
            $sql = $sql . "       CHILD_VARS_NAME_ID  = TBL_A.VAL_CHILD_VARS_LINK_ID AND     \n";
            $sql = $sql . "       DISUSE_FLAG = '0'                                          \n";
            $sql = $sql . "   ) AS VAL_CHILD_VARS_NAME                                     , \n";
        }
        else{
            $sql = $sql . "   NULL AS VAL_CHILD_VARS_NAME                                  , \n";
        }
        // 変数一覧管理の変数タイプを取得　legacy/pioneerにはないのでnull設定
        if($in_driver_name == DF_ROLE_DRIVER){
            $sql = $sql . "   (                                                              \n";
            $sql = $sql . "     SELECT                                                       \n";
            $sql = $sql . "       VARS_ATTRIBUTE_01                                          \n";
            $sql = $sql . "     FROM                                                         \n";
            $sql = $sql . "       $in_vars_master_tbl                                        \n";
            $sql = $sql . "     WHERE                                                        \n";
            $sql = $sql . "       VARS_NAME_ID IN (                                          \n";
            $sql = $sql . "         SELECT                                                   \n";
            $sql = $sql . "           VARS_NAME_ID                                           \n";
            $sql = $sql . "         FROM                                                     \n";
            $sql = $sql . "           $in_ptn_vars_link_tbl                                  \n";
            $sql = $sql . "         WHERE                                                    \n";
            $sql = $sql . "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
            $sql = $sql . "           VARS_LINK_ID  = TBL_A.VAL_VARS_LINK_ID  AND            \n";
            $sql = $sql . "           DISUSE_FLAG   = '0'                                    \n";
            $sql = $sql . "                        )                                         \n";
            $sql = $sql . "       AND                                                        \n";
            $sql = $sql . "       DISUSE_FLAG   = '0'                                        \n";
            $sql = $sql . "   ) AS VARS_ATTRIBUTE_01                                       , \n";
        }
        else{
            $sql = $sql . "   NULL AS VARS_ATTRIBUTE_01                                    , \n";
        }
        $sql = $sql .     "                                                                  \n";
        $sql = $sql . "   TBL_A.VAL_ASSIGN_SEQ                                             , \n";
        $sql = $sql .     "   TBL_A.VAL_CHILD_VARS_COL_SEQ                                 , \n";
        $sql = $sql .     "   TBL_A.KEY_VARS_LINK_ID                                       , \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       VARS_NAME                                                  \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_vars_master_tbl                                        \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       VARS_NAME_ID IN (                                          \n";
        $sql = $sql .     "         SELECT                                                   \n";
        $sql = $sql .     "           VARS_NAME_ID                                           \n";
        $sql = $sql .     "         FROM                                                     \n";
        $sql = $sql .     "           $in_ptn_vars_link_tbl                                  \n";
        $sql = $sql .     "         WHERE                                                    \n";
        $sql = $sql .     "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
        $sql = $sql .     "           VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND            \n";
        $sql = $sql .     "           DISUSE_FLAG   = '0'                                    \n";
        $sql = $sql .     "                        )                                         \n";
        $sql = $sql .     "       AND                                                        \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS KEY_VARS_NAME                                           , \n";
        $sql = $sql .     "   (                                                              \n";
        $sql = $sql .     "     SELECT                                                       \n";
        $sql = $sql .     "       COUNT(*)                                                   \n";
        $sql = $sql .     "     FROM                                                         \n";
        $sql = $sql .     "       $in_ptn_vars_link_tbl                                      \n";
        $sql = $sql .     "     WHERE                                                        \n";
        $sql = $sql .     "       PATTERN_ID    = TBL_A.PATTERN_ID        AND                \n";
        $sql = $sql .     "       VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND                \n";
        $sql = $sql .     "       DISUSE_FLAG   = '0'                                        \n";
        $sql = $sql .     "   ) AS KEY_PTN_VARS_LINK_CNT                                   , \n";
        $sql = $sql .     "   TBL_A.KEY_CHILD_VARS_LINK_ID                                 , \n";
        // ROLEの場合のみ該当メンバー変数のメンバー変数一覧の登録確認
        if($in_driver_name == DF_ROLE_DRIVER){
            $sql = $sql . "   (                                                              \n";
            $sql = $sql . "     SELECT                                                       \n";
            $sql = $sql . "       CHILD_VARS_NAME                                            \n";
            $sql = $sql . "     FROM                                                         \n";
            $sql = $sql . "       $in_child_vars_tbl                                         \n";
            $sql = $sql . "     WHERE                                                        \n";
            $sql = $sql . "       PARENT_VARS_NAME_ID IN (                                   \n";
            $sql = $sql . "         SELECT                                                   \n";
            $sql = $sql . "           VARS_NAME_ID                                           \n";
            $sql = $sql . "         FROM                                                     \n";
            $sql = $sql . "           $in_ptn_vars_link_tbl                                  \n";
            $sql = $sql . "         WHERE                                                    \n";
            $sql = $sql . "           PATTERN_ID    = TBL_A.PATTERN_ID        AND            \n";
            $sql = $sql . "           VARS_LINK_ID  = TBL_A.KEY_VARS_LINK_ID  AND            \n";
            $sql = $sql . "           DISUSE_FLAG   = '0'                                    \n";
            $sql = $sql . "         )                                                        \n";
            $sql = $sql . "       AND                                                        \n";
            $sql = $sql . "       CHILD_VARS_NAME_ID  = TBL_A.KEY_CHILD_VARS_LINK_ID AND     \n";
            $sql = $sql . "       DISUSE_FLAG = '0'                                          \n";
            $sql = $sql . "   ) AS KEY_CHILD_VARS_NAME                                     , \n";
        }
        else{
            $sql = $sql . "   NULL AS KEY_CHILD_VARS_NAME                                  , \n";
        }
        $sql = $sql . "   TBL_A.KEY_ASSIGN_SEQ                                             , \n";
        $sql = $sql .     "   TBL_A.KEY_CHILD_VARS_COL_SEQ                                   \n";
        $sql = $sql .     " FROM                                                             \n";
        $sql = $sql .     "   $in_val_assign_tbl TBL_A                                       \n";
        $sql = $sql .     "   LEFT JOIN B_CMDB_MENU_COLUMN TBL_B ON                          \n";
        $sql = $sql .     "          (TBL_A.COLUMN_LIST_ID = TBL_B.COLUMN_LIST_ID)           \n";
        $sql = $sql .     "   LEFT JOIN B_CMDB_MENU_TABLE  TBL_C ON                          \n";
        $sql = $sql .     "          (TBL_A.MENU_ID        = TBL_C.MENU_ID)                  \n";
        $sql = $sql .     " WHERE                                                            \n";
        $sql = $sql .     "   TBL_A.DISUSE_FLAG='0'                                          \n";
        $sql = $sql .     " ORDER BY TBL_A.COLUMN_ID                                         \n";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            unset($objQuery);
            return false;
        }
        //$lva_assign_seq_list{作業パターン][変数][代入順序]=COLUMN_LIST_ID
        $lva_assign_seq_list        = array();
        //$lva_col_seq_list{作業パターン][変数][メンバー変数][列順序]=COLUMN_LIST_ID
        $lva_var_col_seq_list       = array();

        while ( $row = $objQuery->resultFetch() ){

            // Value型変数の変数タイプを一般変数に設定
            $val_child_var_type = false;
            // Key型変数の変数タイプを一般変数に設定
            $key_child_var_type = false;

            // CMDB代入値紐付メニューが廃止されているか判定
            if($row['TBL_DISUSE_FLAG'] != '0'){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90014",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューのカラムが廃止されているか判定
            if($row['COL_DISUSE_FLAG'] != '0'){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90016",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // 作業パターン詳細に作業パターンが未登録
            if($row['PATTERN_CNT'] == 0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90013",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }


            // CMDB代入値紐付メニューが登録されているか判定
            if(@strlen($row['TABLE_NAME']) == 0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90015",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューの主キーが登録されているか判定
            if(@strlen($row['PKEY_NAME']) == 0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90086",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }

            // CMDB代入値紐付メニューのカラムが未登録か判定
            if(@strlen($row['COL_NAME']) == 0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90017",array($row['COLUMN_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // 次のカラムへ
                continue;
            }
            
            // CMDB代入値紐付メニューのカラムタイトルが未登録か判定
            if(@strlen($row['COL_TITLE']) == 0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90018",array($row['COLUMN_ID']));
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
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90019",array($row['COLUMN_ID'],$row['COL_TYPE']));
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
                $ret = ColVarInfoAnalysis($in_driver_name,
                                          "Value",
                                          $val_child_var_type,
                                          $row,
                                          "VAL_VARS_LINK_ID",
                                          "VAL_VARS_NAME",
                                          "VAL_PTN_VARS_LINK_CNT",
                                          "VARS_ATTRIBUTE_01",
                                          "VAL_CHILD_VARS_LINK_ID",
                                          "VAL_CHILD_VARS_NAME",
                                          "VAL_ASSIGN_SEQ",
                                          "VAL_CHILD_VARS_COL_SEQ");
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
                $ret = ColVarInfoAnalysis($in_driver_name,
                                          "Key",
                                          $key_child_var_type,
                                          $row,
                                          "KEY_VARS_LINK_ID",
                                          "KEY_VARS_NAME",
                                          "KEY_PTN_VARS_LINK_CNT",
                                          "VARS_ATTRIBUTE_01",
                                          "KEY_CHILD_VARS_LINK_ID",
                                          "KEY_CHILD_VARS_NAME",
                                          "KEY_ASSIGN_SEQ",
                                          "KEY_CHILD_VARS_COL_SEQ");
                if($ret === false){
                    // 次のカラムへ
                    continue 2;
                }
                break;
            }
            //カラムタイプにより処理分岐
            switch($col_type){
            case DF_COL_TYPE_VAL:
            case DF_COL_TYPE_KEYVAL:
                //Value型変数を場合の代入順序と列順序をチェック
                $ret = AssSeqColSeqInfoAnalysis("Value",
                                                $val_child_var_type,
                                                $row,
                                                $lva_var_assign_seq_list,
                                                $lva_var_col_seq_list,
                                                $ina_error_column_id_list,
                                                "PATTERN_ID",
                                                "VAL_VARS_LINK_ID",
                                                "VAL_CHILD_VARS_LINK_ID",
                                                "VAL_ASSIGN_SEQ",
                                                "VAL_CHILD_VARS_COL_SEQ");
                if($ret === false){
                    // 次のカラムへ
                    continue 2;
                }
                break;
            }
            switch($col_type){
            case DF_COL_TYPE_KEY:
            case DF_COL_TYPE_KEYVAL:
                //key型変数を場合の代入順序と列順序をチェック
                $ret = AssSeqColSeqInfoAnalysis("Key",
                                                $key_child_var_type,
                                                $row,
                                                $lva_var_assign_seq_list,
                                                $lva_var_col_seq_list,
                                                $ina_error_column_id_list,
                                                "PATTERN_ID",
                                                "KEY_VARS_LINK_ID",
                                                "KEY_CHILD_VARS_LINK_ID",
                                                "KEY_ASSIGN_SEQ",
                                                "KEY_CHILD_VARS_COL_SEQ");
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
                                     'COL_TITLE'=>$row['COL_TITLE'],
                                     'REF_TABLE_NAME'=>$row['REF_TABLE_NAME'],
                                     'REF_PKEY_NAME'=>$row['REF_PKEY_NAME'],
                                     'REF_COL_NAME'=>$row['REF_COL_NAME'],
                                     'PATTERN_ID'=>$row['PATTERN_ID'],
                                     'VAL_VARS_LINK_ID'=>$row['VAL_VARS_LINK_ID'],
                                     'VAL_VARS_NAME'=>$row['VAL_VARS_NAME'],
                                     'VAL_CHILD_VARS_LINK_ID'=>$row['VAL_CHILD_VARS_LINK_ID'],
                                     'VAL_CHILD_VARS_NAME'=>$row['VAL_CHILD_VARS_NAME'],
                                     'VAL_ASSIGN_SEQ'=>$row['VAL_ASSIGN_SEQ'],
                                     'VAL_CHILD_VARS_COL_SEQ'=>$row['VAL_CHILD_VARS_COL_SEQ'],
                                     'KEY_VARS_LINK_ID'=>$row['KEY_VARS_LINK_ID'],
                                     'KEY_VARS_NAME'=>$row['KEY_VARS_NAME'],
                                     'KEY_CHILD_VARS_LINK_ID'=>$row['KEY_CHILD_VARS_LINK_ID'],
                                     'KEY_CHILD_VARS_NAME'=>$row['KEY_CHILD_VARS_NAME'],
                                     'KEY_ASSIGN_SEQ'=>$row['KEY_ASSIGN_SEQ'],
                                     'KEY_CHILD_VARS_COL_SEQ'=>$row['KEY_CHILD_VARS_COL_SEQ'],
                                     'VAL_VAR_TYPE'=>$val_child_var_type,
                                     'KEY_VAR_TYPE'=>$key_child_var_type,
                                     'NULL_DATA_HANDLING_FLG'=>$row['NULL_DATA_HANDLING_FLG']);

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
    //   代入値紐付からカラム情報を取得する。
    //   
    // パラメータ
    //   $in_driver_name:         ドライバ区分
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
    //   $in_vars_attribute_01:   クエリ配列内の変数タイプ(Roleの場合のみ有効)
    //                            VARS_ATTRIBUTE_01
    //   $in_child_vars_link_id:  クエリ配列内のKey/Value型のメンバー変数IDキー
    //                            VAL_CHILD_VARS_LINK_ID/KEY_CHILD_VARS_LINK_ID
    //   $in_child_vars_name:     クエリ配列内のKey/Value型のメンバー変数名キー
    //                            VAL_CHILD_VARS_NAME/KEY_CHILD_VARS_NAME
    //   $in_assign_seq:          クエリ配列内のKey/Value型の代入順序キー
    //                            VAL_ASSIGN_SEQ/KEY_ASSIGN_SEQ
    //   $in_child_vars_col_seq:  クエリ配列内のKey/Value型の列順序キー
    //                            VAL_CHILD_VARS_COL_SEQ/KEY_CHILD_VARS_COL_SEQ
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function ColVarInfoAnalysis($in_driver_name,
                                $in_col_type,           //カラムタイプ Value/Key
                                &$in_child_var_type,
                                $row,
                                $in_vars_link_id,
                                $in_vars_name,
                                $in_ptn_vars_link_cnt,
                                $in_vars_attribute_01,
                                $in_child_vars_link_id,
                                $in_child_vars_name,
                                $in_assign_seq,
                                $in_child_vars_col_seq){
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        //変数の選択判定
        if(@strlen($row[$in_vars_link_id]) == 0){
            if ( $log_level === 'DEBUG' ){
                // 代入値紐付（項番:｛｝）のValue型の変数が未選択。
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90033",array($row['COLUMN_ID'],$in_col_type));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // エラーリターン
            return false;
        }

        // 変数が作業パターン変数紐付にあるか判定
        if(@strlen($row[$in_ptn_vars_link_cnt]) == 0){
            if ( $log_level === 'DEBUG' ){
                switch($in_driver_name){
                case DF_LEGACY_DRIVER:
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90023",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    break;
                case DF_PIONEER_DRIVER:
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90024",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    break;
                case DF_ROLE_DRIVER:
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90025",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    break;
                }
            }
            // エラーリターン
            return false;
        }
        // 設定されている変数が変数一覧にあるか判定
        if(@strlen($row[$in_vars_name]) == 0){
            if ( $log_level === 'DEBUG' ){
                switch($in_driver_name){
                case DF_LEGACY_DRIVER:
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90020",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    break;
                case DF_PIONEER_DRIVER:
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90021",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    break;
                case DF_ROLE_DRIVER:
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90022",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    break;
                }
            }
            // エラーリターン
            return false;
        }
            
        // メンバー変数がメンバー変数一覧にあるか判定
        $in_child_var_type = false;
        if($in_driver_name == DF_ROLE_DRIVER){
            //メンバー変数の選択判定
            if(@strlen($row[$in_child_vars_link_id]) != 0){
                // カラムタイプ型の変数タイプを配列変数に設定
                $in_child_var_type = true;

                // カラムタイプ型に設定されているメンバー変数がメンバー変数一覧にあるか判定
                if(@strlen($row[$in_child_vars_name]) == 0){
                    if ( $log_level === 'DEBUG' ){
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90026",array($row['COLUMN_ID'],$in_col_type));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    // エラーリターン
                    return false;
                }
            }
        }
        // 変数一覧管理の変数タイプと一致しているか判定
        if($in_driver_name == DF_ROLE_DRIVER){
            if(@strlen($row[$in_vars_attribute_01]) == 0){
                $vars_attribute_01 = false;
            }
            else{
                $vars_attribute_01 = true;
            }
            if($in_child_var_type != $vars_attribute_01){
                if ( $log_level === 'DEBUG' ){
                    if($in_vars_attribute_01 === false){
                        $msgid = "ITAANSIBLEH-ERR-90102";
                    }
                    else{
                        $msgid = "ITAANSIBLEH-ERR-90103";
                    }
                    $msgstr = $objMTS->getSomeMessage($msgid,array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // エラーリターン
                return false;
            }
        }
        
        if($in_child_var_type === false){
        }
        else{
            if(@strlen($row[$in_child_vars_col_seq])===0){
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90028",array($row['COLUMN_ID'],$in_col_type));
                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // エラーリターン
                return false;
            }
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 処理内容
    //   代入値紐付からカラム情報を取得する。
    //   
    // パラメータ
    //   $in_col_type:              カラムタイプ Value/Key
    //   $in_child_var_type:        配列変数区分 
    //                              true:配列変数   false:一般変数
    //   $row:                      クエリ配列
    //   $ina_var_assign_seq_list:  作業パターン+変数名+代入順序の重複チェック配列
    //   $ina_var_col_seq_list:     作業パターン+変数名+メンバー変数+列順序の重複チェック配列     
    //   $ina_error_column_id_list: 代入値紐付でエラーが発生している代入値紐付の主キーリスト
    //   $in_vars_link_id:          クエリ配列内のKey/Value型の変数IDキー 
    //                              VAL_VARS_LINK_ID/KEY_VARS_LINK_ID
    //   $in_vars_name:             クエリ配列内のKey/Value型の変数名キー
    //                              VAL_VARS_NAME/KEY_VARS_NAME
    //   $in_ptn_vars_link_cnt:     クエリ配列内のKey/Value型の作業パターン+変数の
    //                              作業パターン変数紐付の登録件数
    //                              VAL_PTN_VARS_LINK_CNT/KEY_PTN_VARS_LINK_CNT
    //   $in_child_vars_link_id:    クエリ配列内のKey/Value型のメンバー変数IDキー
    //                              VAL_CHILD_VARS_LINK_ID/KEY_CHILD_VARS_LINK_ID
    //   $in_child_vars_name:       クエリ配列内のKey/Value型のメンバー変数名キー
    //                              VAL_CHILD_VARS_NAME/KEY_CHILD_VARS_NAME
    //   $in_assign_seq:            クエリ配列内のKey/Value型の代入順序キー
    //                              VAL_ASSIGN_SEQ/KEY_ASSIGN_SEQ
    //   $in_child_vars_col_seq:    クエリ配列内のKey/Value型の列順序キー
    //                              VAL_CHILD_VARS_COL_SEQ/KEY_CHILD_VARS_COL_SEQ
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function AssSeqColSeqInfoAnalysis($in_col_type,
                                      $in_child_var_type,
                                      $row,
                                      &$ina_var_assign_seq_list,
                                      &$ina_var_col_seq_list,
                                      &$ina_error_column_id_list,
                                      $in_pattern_id,                    
                                      $in_vars_link_id,                  
                                      $in_child_vars_link_id,            
                                      $in_assign_seq,                    
                                      $in_child_vars_col_seq){
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        // 一般変数を判定
        if($in_child_var_type === false){
            if( isset($ina_var_assign_seq_list[$row[$in_pattern_id]]
                                              [$row[$in_vars_link_id]]
                                              [$row[$in_assign_seq]])){
                $column_id = $ina_var_assign_seq_list[$row[$in_pattern_id]]
                                                     [$row[$in_vars_link_id]]
                                                     [$row[$in_assign_seq]];

                //重複しているのでエラーリストに代入値紐付の主キーを退避
                $ina_error_column_id_list[$column_id]        = 1;
                $ina_error_column_id_list[$row['COLUMN_ID']] = 1;

                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90029",array($row['COLUMN_ID'],$column_id,$in_col_type));

                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // エラーリターン
                return false;
            }
            //代入順序退避
            $ina_var_assign_seq_list[$row[$in_pattern_id]]
                                    [$row[$in_vars_link_id]]
                                    [$row[$in_assign_seq]] = $row['COLUMN_ID'];

        }
        else{
            //配列変数の場合は列順序の重複チェック
            if( isset($ina_var_col_seq_list[$row[$in_pattern_id]]
                                           [$row[$in_vars_link_id]]
                                           [$row[$in_child_vars_link_id]]
                                           [$row[$in_child_vars_col_seq]])){
                $column_id = $ina_var_col_seq_list[$row[$in_pattern_id]]
                                                       [$row[$in_vars_link_id]]
                                                       [$row[$in_child_vars_link_id]]
                                                       [$row[$in_child_vars_col_seq]];

                //重複しているのでエラーリストに代入値紐付の主キーを退避
                $ina_error_column_id_list[$column_id]        = 1;
                $ina_error_column_id_list[$row['COLUMN_ID']] = 1;

                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90030",array($row['COLUMN_ID'],$column_id,$in_col_type));

                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                }
                // エラーリターン
                return false;
            }
            $ina_var_col_seq_list[$row[$in_pattern_id]]
                                 [$row[$in_vars_link_id]]
                                 [$row[$in_child_vars_link_id]]
                                 [$row[$in_child_vars_col_seq]] = $row['COLUMN_ID'];
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0004
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
        // 機器一覧にホストが登録されているかを判定するSQL
        $hostid_chk_sql = "( SELECT                                       \n" .
                          "    COUNT(*)                                   \n" .
                          "  FROM                                         \n" .
                          "    C_STM_LIST                                 \n" .
                          "  WHERE                                        \n" .
                          "    SYSTEM_ID   = TBL_A.HOST_ID     AND        \n" .
                          "    DISUSE_FLAG = '0'                          \n" .
                          ") AS " . DF_ITA_LOCAL_HOST_CNT  . ",           \n";

             
        // テーブル名+カラム名配列からテーブル名と配列名を取得
        foreach($ina_table_colnameTOid_list as $table_name=>$col_list){
            $pkey_name = $ina_table_nameTOPkeyname_list[$table_name];

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
                    $make_sql = "SELECT "                                               . "\n" .
                                $opeid_chk_sql . $hostid_chk_sql                        . "\n" .
                                "  TBL_A." . $pkey_name . " AS " . DF_ITA_LOCAL_PKEY    . "\n" .
                                ", TBL_A.HOST_ID "                                      . "\n" .
                                ", TBL_A." . $col_name . " \n";
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
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90035",array($ina_table_nameTOid_list[$table_name]));
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
    // F0005
    // 処理内容
    //   CMDB代入値紐付対象メニューから具体値を取得する。
    //
    // パラメータ
    //   $in_driver_name:               ドライバ区分
    //   $ina_table_nameTOsql_list:     代入値紐付メニュー毎のSELECT文配列
    //                                  [テーブル名][SELECT文]
    //   $ina_table_nameTOid_list:      テーブル名配列
    //                                  [テーブル名]=MENU_ID
    //   $ina_table_col_list:           カラム情報配列
    //                                  [テーブル名][カラム名]=>array("代入値紐付のカラム名"=>値)
    //   $warning_flag:                 警告フラグ
    //   $ina_child_vars_ass_list:      配列変数用 代入値登録情報配列
    //   $ina_child_vars_ass_chk_list:  配列変数用 列順序重複チェック配列
    //   $ina_vars_ass_list:            一般変数用 代入値登録情報配列
    //   $ina_vars_ass_chk_list:        一般変数用 代入順序重複チェック配列
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function GetMenuData($in_driver_name,
                         $ina_table_nameTOsql_list,
                         $ina_table_nameTOid_list,
                         $ina_table_col_list,
                         &$ina_child_vars_ass_list,
                         &$ina_child_vars_ass_chk_list,
                         &$ina_vars_ass_list,
                         &$ina_vars_ass_chk_list,
                         $ina_error_column_id_list,
                         &$warning_flags){
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        foreach($ina_table_nameTOsql_list as $table_name=>$sql){
            if ( $log_level === 'DEBUG' ){
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70017",array($ina_table_nameTOid_list[$table_name]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            // 代入値紐付メニューがデータを取出す
            $total_row = array();
            $ret = DBGetMenuData($sql,$total_row);
            if($ret === false){
                //DBアクセスエラー
                if ( $log_level === 'DEBUG' ){
                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90036",array($ina_table_nameTOid_list[$table_name]));
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
                        $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90048",array($ina_table_nameTOid_list[$table_name]));
                        LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                    }
                    //次のテーブルへ
                    continue;
                }


                foreach($total_row as $row){
                    // 代入値紐付メニューに登録されているホストIDの紐付確認
                    if($row[DF_ITA_LOCAL_HOST_CNT] == 0){
                        // ホストIDの紐付不正
                        if ( $log_level === 'DEBUG' ){
                            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90038",array($ina_table_nameTOid_list[$table_name],$row[DF_ITA_LOCAL_PKEY],$row['HOST_ID']));
                            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }

                        $warning_flag = true;
                        //次のデータへ
                        continue;
                    }

                    // 代入値紐付メニューに登録されているオペレーションIDを確認
                    if(@strlen($row['OPERATION_ID']) == 0){
                        //オペレーションID未登録
                        if ( $log_level === 'DEBUG' ){
                            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90040",array($ina_table_nameTOid_list[$table_name],$row[DF_ITA_LOCAL_PKEY]));
                            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        
                        $warning_flag = true;
                        //次のデータへ
                        continue;
                    }
                    $operation_id = $row['OPERATION_ID'];
                    
                    // 代入値紐付メニューに登録されているホストIDを確認
                    if(@strlen($row['HOST_ID']) == 0){
                        //ホストID未登録
                        if ( $log_level === 'DEBUG' ){
                            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90041",array($ina_table_nameTOid_list[$table_name],$row[DF_ITA_LOCAL_PKEY]));
                            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                        }
                        
                        $warning_flag = true;
                        //次のデータへ
                        continue;
                    }
                    $host_id = $row['HOST_ID'];

                    // 代入値紐付に登録されている変数に対応する具体値を取得する。
                    foreach($row as $col_name=>$col_val){

                        $col_val_key = $col_val;

                        switch($col_name){
                        // 具体値カラム以外を除外
                        case DF_ITA_LOCAL_OPERATION_CNT:
                        case DF_ITA_LOCAL_HOST_CNT:
                        case DF_ITA_LOCAL_DUP_CHECK_ITEM:
                        case 'OPERATION_ID':
                        case 'HOST_ID':
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
                                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                                    unset($objQuery);
                                    continue;
                                }

                                $objQuery->sqlBind(array($ina_col_list['REF_PKEY_NAME'] => $col_val_key));

                                $r = $objQuery->sqlExecute();
                                if (!$r){
                                    $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                                    LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$sql);
                                    LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

                                    unset($objQuery);
                                    continue;
                                }

                                // fetch行数を取得
                                $count = $objQuery->effectedRowCount();

                                // 1件ではない場合
                                if(1 != $count){
                                    continue;
                                }
                                // fetch行を取得
                                $tgt_row = $objQuery->resultFetch();
                                $col_val = $tgt_row[$ina_col_list['REF_COL_NAME']];
                                unset($objQuery);
                            }

                            // 代入値管理の登録に必要な情報を生成                    
                            makeVarsAssData($in_driver_name,
                                            $table_name,
                                            $col_name,
                                            $col_val,
                                            $ina_col_list['NULL_DATA_HANDLING_FLG'],
                                            $operation_id,
                                            $host_id,
                                            $ina_col_list,
                                            $ina_vars_ass_list,
                                            $ina_vars_ass_chk_list,
                                            $ina_child_vars_ass_list,
                                            $ina_child_vars_ass_chk_list,
                                            $ina_table_nameTOid_list[$table_name],
                                            $row[DF_ITA_LOCAL_PKEY]);
                            //戻り値は判定しない
                        }
                    }
                }
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////
    // F0007
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
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$in_sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
    // F0008
    // 処理内容
    //   CMDB代入値紐付対象メニューから具体値を取得する。
    //
    // パラメータ
    //   $in_driver_name:               ドライバ区分
    //   $in_table_name:                テーブル名
    //   $in_col_name:                  カラム名
    //   $in_col_val:                   カラムの具体値
    //   $in_null_data_handling_flg     代入値管理へのNULLデータ連携フラグ
    //   $in_operation_id:              オペレーションID
    //   $in_host_id:                   ホストID
    //   $ina_col_list:                 カラム情報配列
    //   $ina_child_vars_ass_list:      配列変数用 代入値登録情報配列
    //   $ina_child_vars_ass_chk_list:  配列変数用 列順序重複チェック配列
    //   $ina_vars_ass_list:            一般変数用 代入値登録情報配列
    //   $ina_vars_ass_chk_list:        一般変数用 代入順序重複チェック配列
    //   $in_menu_id:                   紐付メニューID
    //   $in_row_id:                    紐付テーブル主キー値
    //
    // 戻り値
    //   true:   正常
    //   false:  異常
    ////////////////////////////////////////////////////////////////////////////////
    function makeVarsAssData($in_driver_name,
                             $in_table_name,
                             $in_col_name,
                             $in_col_val,
                             $in_null_data_handling_flg,
                             $in_operation_id,
                             $in_host_id,
                             $ina_col_list,
                             &$ina_vars_ass_list,
                             &$ina_vars_ass_chk_list,
                             &$ina_child_vars_ass_list,
                             &$ina_child_vars_ass_chk_list,
                             $in_menu_id,
                             $in_row_id){
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
            //具体値が空白または1024バイト以上ないか判定
            $ret = chkValueTypeColValue($in_col_val,
                                        $in_null_data_handling_flg,
                                        $in_table_name,$in_row_id,$ina_col_list['COL_TITLE']);
            if($ret === false){
                return;
            }
            // Value型カラムの場合
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_driver_name,
                           $in_table_name,
                           $in_col_name,
                           $ina_col_list['VAL_VAR_TYPE'],
                           $in_operation_id,
                           $in_host_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['VAL_VARS_LINK_ID'],
                           $ina_col_list['VAL_CHILD_VARS_LINK_ID'],
                           $ina_col_list['VAL_CHILD_VARS_COL_SEQ'],
                           $ina_col_list['VAL_ASSIGN_SEQ'],
                           $in_col_val,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $ina_child_vars_ass_list,
                           $ina_child_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           'Value',
                           $in_row_id);
            break;
        case DF_COL_TYPE_KEY:
            // Key型カラムの場合
            //具体値が空白か判定
            $ret = chkKeyTypeColValue($in_col_val,$in_table_name,$in_row_id,$ina_col_list['COL_TITLE']);
            if($ret === false){
                // 空白の場合処理対象外
                return;
            }
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_driver_name,
                           $in_table_name,
                           $in_col_name,
                           $ina_col_list['KEY_VAR_TYPE'],
                           $in_operation_id,
                           $in_host_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['KEY_VARS_LINK_ID'],
                           $ina_col_list['KEY_CHILD_VARS_LINK_ID'],
                           $ina_col_list['KEY_CHILD_VARS_COL_SEQ'],
                           $ina_col_list['KEY_ASSIGN_SEQ'],
                           $col_name,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $ina_child_vars_ass_list,
                           $ina_child_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           'Key',
                           $in_row_id);
            break;
        case DF_COL_TYPE_KEYVAL:
            //具体値が空白または1024バイト以上ないか判定
            $ret = chkValueTypeColValue($in_col_val,
                                        $in_null_data_handling_flg,
                                        $in_table_name,$in_row_id,$ina_col_list['COL_TITLE']);
            if($ret === false){
                return;
            }
            // Key-Value型カラムの場合
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_driver_name,
                           $in_table_name,
                           $in_col_name,
                           $ina_col_list['VAL_VAR_TYPE'],
                           $in_operation_id,
                           $in_host_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['VAL_VARS_LINK_ID'],
                           $ina_col_list['VAL_CHILD_VARS_LINK_ID'],
                           $ina_col_list['VAL_CHILD_VARS_COL_SEQ'],
                           $ina_col_list['VAL_ASSIGN_SEQ'],
                           $in_col_val,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $ina_child_vars_ass_list,
                           $ina_child_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           'Value',
                           $in_row_id);
                           
            // chkVarsAssDataの戻りは判定しない。
            chkVarsAssData($in_driver_name,
                           $in_table_name,
                           $in_col_name,
                           $ina_col_list['KEY_VAR_TYPE'],
                           $in_operation_id,
                           $in_host_id,
                           $ina_col_list['PATTERN_ID'],
                           $ina_col_list['KEY_VARS_LINK_ID'],
                           $ina_col_list['KEY_CHILD_VARS_LINK_ID'],
                           $ina_col_list['KEY_CHILD_VARS_COL_SEQ'],
                           $ina_col_list['KEY_ASSIGN_SEQ'],
                           $col_name,
                           $ina_vars_ass_list,
                           $ina_vars_ass_chk_list,
                           $ina_child_vars_ass_list,
                           $ina_child_vars_ass_chk_list,
                           $in_menu_id,
                           $ina_col_list['COLUMN_ID'],
                           'Key',
                           $in_row_id);
            break;
        }
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0009
    // 処理内容
    //   CMDB代入値紐付対象メニューの情報から代入値管理に登録する情報を生成
    //
    // パラメータ
    //   $in_driver_name:               ドライバ区分
    //   $in_table_name:                テーブル名
    //   $in_col_name:                  カラム名
    //   $in_var_type:                  変数タイプ　
    //                                  true:配列/false:一般
    //   $in_operation_id:              オペレーションID
    //   $in_host_id:                   ホスト名
    //   $in_patten_id:                 パターンID
    //   $in_vars_link_id:              変数ID
    //   $in_child_vars_link_id:        メンバー変数ID
    //   $in_child_vars_col_seq:        列順序
    //   $in_vars_assign_seq:           代入順序
    //   $in_col_val:                   具体値
    //   $ina_child_vars_ass_list:      配列変数用 代入値登録情報配列
    //   $ina_child_vars_ass_chk_list:  配列変数用 列順序重複チェック配列
    //   $ina_vars_ass_list:            一般変数用 代入値登録情報配列
    //   $ina_vars_ass_chk_list:        一般変数用 代入順序重複チェック配列
    //   $in_menu_id:                   紐付メニューID
    //   $in_column_id:                 代入値自動設定登録           
    //   $in_key_value_vars_id          Value/Key
    //   $in_row_id:                    紐付テーブル主キー値
    //
    // 戻り値
    //   なし
    ////////////////////////////////////////////////////////////////////////////////
    function chkVarsAssData($in_driver_name,
                            $in_table_name,
                            $in_col_name,
                            $in_var_type,
                            $in_operation_id,
                            $in_host_id,
                            $in_patten_id,
                            $in_vars_link_id,
                            $in_child_vars_link_id,
                            $in_child_vars_col_seq,
                            $in_vars_assign_seq,
                            $in_col_val,
                            &$ina_vars_ass_list,
                            &$ina_vars_ass_chk_list,
                            &$ina_child_vars_ass_list,
                            &$ina_child_vars_ass_chk_list,
                            $in_menu_id,
                            $in_column_id,
                            $in_key_value_vars_id,
                            $in_row_id){
        global $log_level;
        global $objMTS;
        global $objDBCA;

        $chk_status = false;
        //変数のタイプを判定(true:配列/false:一般)
        if($in_var_type === true){

            //配列変数
            //オペ+作業+ホスト+配列変数+メンバ変数の組合せで列順序が重複していないか判定
            if( isset($ina_child_vars_ass_chk_list[$in_operation_id]
                                                  [$in_patten_id]
                                                  [$in_host_id]
                                                  [$in_vars_link_id]
                                                  [$in_child_vars_link_id]
                                                  [$in_child_vars_col_seq])){
                $dup_info = $ina_child_vars_ass_chk_list[$in_operation_id]
                                                        [$in_patten_id]
                                                        [$in_host_id]
                                                        [$in_vars_link_id]
                                                        [$in_child_vars_link_id]
                                                        [$in_child_vars_col_seq];

                // 既に登録されている
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90049",array( $in_menu_id,
                                                                                 $in_row_id,
                                                                                 $in_operation_id,
                                                                                 $in_patten_id,
                                                                                 $in_host_id,
                                                                                 $in_column_id,
                                                                                 $in_key_value_vars_id,
                                                                                 $dup_info['MENU_ID'],
                                                                                 $dup_info[DF_ITA_LOCAL_PKEY]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            else{
                $chk_status = true;
                // オペ+作業+ホスト+配列変数+メンバ変数の組合せの列順序退避
                $ina_child_vars_ass_chk_list[$in_operation_id]
                                            [$in_patten_id]
                                            [$in_host_id]
                                            [$in_vars_link_id]
                                            [$in_child_vars_link_id]
                                            [$in_child_vars_col_seq] = array('MENU_ID'=>$in_menu_id,DF_ITA_LOCAL_PKEY=>$in_row_id);
            }
            // 代入値管理の登録に必要な情報退避
            $ina_child_vars_ass_list[] = array('TABLE_NAME'=>$in_table_name,
                                                      'COL_NAME'=>$in_col_name,
                                                      'OPERATION_NO_UAPK'=>$in_operation_id,
                                                      'PATTERN_ID'=>$in_patten_id,
                                                      'SYSTEM_ID'=>$in_host_id,
                                                      'VARS_LINK_ID'=>$in_vars_link_id,
                                                      'CHILD_VARS_LINK_ID'=>$in_child_vars_link_id,
                                                      'CHILD_VARS_COL_SEQ'=>$in_child_vars_col_seq,
                                                      'VARS_ENTRY'=>$in_col_val,
                                                      'VAR_TYPE'=>$in_var_type,
                                                      'STATUS'=>$chk_status);
        }
        else{

            //一般変数
            // オペ+作業+ホスト+変数の組合せで代入順序が重複していないか判定
            if( isset($ina_vars_ass_chk_list[$in_operation_id]
                                            [$in_patten_id]
                                            [$in_host_id]
                                            [$in_vars_link_id]
                                            [$in_vars_assign_seq])){
                // 既に登録されている
                $dup_info = $ina_vars_ass_chk_list[$in_operation_id]
                                                  [$in_patten_id]
                                                  [$in_host_id]
                                                  [$in_vars_link_id]
                                                  [$in_vars_assign_seq];
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90050",array( $in_menu_id,
                                                                                 $in_row_id,
                                                                                 $in_operation_id,
                                                                                 $in_patten_id,
                                                                                 $in_host_id,
                                                                                 $in_column_id,
                                                                                 $in_key_value_vars_id,
                                                                                 $dup_info['MENU_ID'],
                                                                                 $dup_info[DF_ITA_LOCAL_PKEY]));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            }
            else{
                $chk_status = true;
                // オペ+作業+ホスト+配列変数+メンバ変数の組合せの代入順序退避
                $ina_vars_ass_chk_list[$in_operation_id]
                                      [$in_patten_id]
                                      [$in_host_id]
                                      [$in_vars_link_id]
                                      [$in_vars_assign_seq]       = array('MENU_ID'=>$in_menu_id,DF_ITA_LOCAL_PKEY=>$in_row_id);
            }
            // 代入値管理の登録に必要な情報退避
            $ina_vars_ass_list[] = array('TABLE_NAME'=>$in_table_name,
                                         'COL_NAME'=>$in_col_name,
                                         'OPERATION_NO_UAPK'=>$in_operation_id,
                                         'PATTERN_ID'=>$in_patten_id,
                                         'SYSTEM_ID'=>$in_host_id,
                                         'VARS_LINK_ID'=>$in_vars_link_id,
                                         'VARS_ENTRY'=>$in_col_val,
                                         'ASSIGN_SEQ'=>$in_vars_assign_seq,
                                         'VAR_TYPE'=>$in_var_type,
                                         'STATUS'=>$chk_status);
        }
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
    function addStg1VarsAssDB($ina_varsass_list,&$in_VarsAssignRecodes) {
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global    $db_update_flg;
        global    $vg_driver_name;

        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss;
        $strJnlTable      = $strJnlTableVarsAss;
        $arrayConfig      = $arrayConfigOfVarAss;
        $arrayValue       = $arrayValueTmplOfVarAss;
        $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
        $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;

        $key = $ina_varsass_list['OPERATION_NO_UAPK'] . "_" .
               $ina_varsass_list['PATTERN_ID']        . "_" .
               $ina_varsass_list['SYSTEM_ID']         . "_" .
               $ina_varsass_list['VARS_LINK_ID']      . "_" .
               $ina_varsass_list['ASSIGN_SEQ']        . "_" .
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
                if ( $log_level === 'DEBUG' )
                {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70026",
                                                  array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                        $ina_varsass_list['PATTERN_ID'],
                                                        $ina_varsass_list['SYSTEM_ID'],
                                                        $ina_varsass_list['VARS_LINK_ID'],
                                                        $ina_varsass_list['ASSIGN_SEQ']));
                     LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                 }

                 //同一みなので処理終了
                 return true;
            }

            // 最終更新者が自分でない場合、更新処理はスキップする。
            if($tgt_row["LAST_UPDATE_USER"] != $db_access_user_id){


                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70036",
                                                        array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                              $ina_varsass_list['PATTERN_ID'],
                                                              $ina_varsass_list['SYSTEM_ID'],
                                                              $ina_varsass_list['VARS_LINK_ID'],
                                                              $ina_varsass_list['ASSIGN_SEQ']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                //更新処理はスキップ
                return true;
            }

            if($vg_driver_name == DF_LEGACY_DRIVER) {
                // 具体値にテンプレート変数が記述されているか判定
                if( $db_update_flg === false) {
                    $var_match = array();
                    $val_list[] = $tgt_row["VARS_ENTRY"];
                    $val_list[] = $ina_varsass_list['VARS_ENTRY'];
                    foreach($val_list as $val) {
                        $ret = preg_match_all("/{{(\s)" . "TPF_" . "[a-zA-Z0-9_]*(\s)}}/",$val,$var_match);
                        if(($ret !== false) && ($ret > 0)){
                            // テンプレート変数が記述されていることを記録
                            $db_update_flg = true;
                            break;
                        }
                    }
                }
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70025",
                                            array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                  $ina_varsass_list['PATTERN_ID'],
                                                  $ina_varsass_list['SYSTEM_ID'],
                                                  $ina_varsass_list['VARS_LINK_ID'],
                                                  $ina_varsass_list['ASSIGN_SEQ']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
        }
        if($action == "UPDATE"){


            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["VARS_ENTRY"]       = $ina_varsass_list['VARS_ENTRY'];
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
 
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
    // F0012
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
        global    $vg_driver_name;


        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss;
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70038",
                                                             array($tgt_row['ASSIGN_ID']));
                }
    
                //更新処理はスキップ
                continue;
            }
                
            if($vg_driver_name == DF_LEGACY_DRIVER) {
                // 具体値にテンプレート変数が記述されているか判定
                if( $db_update_flg === false) {
                    $var_match = array();
                    $ret = preg_match_all("/{{(\s)" . "TPF_" . "[a-zA-Z0-9_]*(\s)}}/",$tgt_row["VARS_ENTRY"],$var_match);
                    if(($ret !== false) && ($ret > 0)){
                        // テンプレート変数が記述されていることを記録
                        $db_update_flg = true;
                    }
                }
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70031",
                                                       array($tgt_row['ASSIGN_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }
    
    
            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                 $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                 return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
    
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
    
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    
                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }
    
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
        
    ////////////////////////////////////////////////////////////////////////////////
    // F0013
    // 処理内容
    //   作業対象ホスト（一般変数）を更新する。
    //   
    // パラメータ
    //   $in_phoLinkData:             作業対象ホスト更新情報配列
    //   $in_PhoLinkRecodes:          作業対象ホストの全データ配列
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addStg1PhoLnkDB($in_phoLinkData,&$in_PhoLinkRecodes) {

        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global $db_access_user_id;
        global $strCurTablePhoLnk;
        global $strJnlTablePhoLnk;
        global $strSeqOfCurTablePhoLnk;
        global $strSeqOfJnlTablePhoLnk;
        global $arrayConfigOfPhoLnk;
        global $arrayValueTmplOfPhoLnk;

        $strCurTable      = $strCurTablePhoLnk;
        $strJnlTable      = $strJnlTablePhoLnk;
        $arrayConfig      = $arrayConfigOfPhoLnk;
        $arrayValue       = $arrayValueTmplOfPhoLnk;
        $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
        $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;


        $key = $in_phoLinkData["OPERATION_NO_UAPK"] . "_" .
               $in_phoLinkData["PATTERN_ID"]        . "_" .
               $in_phoLinkData["SYSTEM_ID"]         . "_" .
               '0';
        if(! isset($in_PhoLinkRecodes[$key])) {
            // 廃止レコードを復活または新規レコード追加
            return addStg2PhoLnkDB($in_phoLinkData, $in_PhoLinkRecodes);
        } else {


            unset($in_PhoLinkRecodes[$key]);
            //同一なので処理終了
            return true;
        }
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // F0014
    // 処理内容
    //   作業管理対象ホスト管理から不要なレコードを廃止
    //   
    // パラメータ
    //   $in_phoLinkData:             作業対象ホスト更新情報配列
    //   $in_PhoLinkRecodes:          作業対象ホストの全データ配列
    // 
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function delPhoLnkDB(&$in_PhoLinkRecodes) {

        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global $db_access_user_id;
        global $strCurTablePhoLnk;
        global $strJnlTablePhoLnk;
        global $strSeqOfCurTablePhoLnk;
        global $strSeqOfJnlTablePhoLnk;
        global $arrayConfigOfPhoLnk;
        global $arrayValueTmplOfPhoLnk;

        $strCurTable      = $strCurTablePhoLnk;
        $strJnlTable      = $strJnlTablePhoLnk;
        $arrayConfig      = $arrayConfigOfPhoLnk;
        $arrayValue       = $arrayValueTmplOfPhoLnk;
        $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
        $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;

        $strPkey                = "PHO_LINK_ID";

        foreach($in_PhoLinkRecodes as $key=>$tgt_row) {
            if($tgt_row['DISUSE_FLAG'] == '1')
            {

                //廃止レコードはなにもしない。
                continue;
            }
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                 $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70034",
                                                     array($tgt_row['PHO_LINK_ID']));
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }

            // 最終更新者が自分でない場合、廃止処理はスキップする。
            if($tgt_row["LAST_UPDATE_USER"] != $db_access_user_id){
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70039",
                                                        array($tgt_row['PHO_LINK_ID']));
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }


                //更新処理はスキップ
                continue;
            }

            // 登録されていない場合は廃止レコードにする。
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                $FREE_LOG = $objQueryUtn->getLastError();
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                unset($objQueryUtn);
                unset($objQueryJnl);
                return false;
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
                     $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90056",
                                          array($lva_table_nameTOid_list[$in_table_name],$in_row_id,$in_menu_title));
                     LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                 }
                 return false;
            }
        }
        //具体値が1024バイト以上の場合
        if(strlen($in_col_val)>1024){
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90057",
                                     array($lva_table_nameTOid_list[$in_table_name],$in_row_id,$in_menu_title));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }

            return false;
        }
        return true;
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // F0015
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
        global    $vg_driver_name;

        global $db_access_user_id;
        global $strCurTableVarsAss;
        global $strJnlTableVarsAss;
        global $strSeqOfCurTableVarsAss;
        global $strSeqOfJnlTableVarsAss;
        global $arrayConfigOfVarAss;
        global $arrayValueTmplOfVarAss;

        $strCurTable      = $strCurTableVarsAss;
        $strJnlTable      = $strJnlTableVarsAss;
        $arrayConfig      = $arrayConfigOfVarAss;
        $arrayValue       = $arrayValueTmplOfVarAss;
        $strSeqOfCurTable = $strSeqOfCurTableVarsAss;
        $strSeqOfJnlTable = $strSeqOfJnlTableVarsAss;


        $key = $ina_varsass_list['OPERATION_NO_UAPK'] . "_" .
               $ina_varsass_list['PATTERN_ID']        . "_" .
               $ina_varsass_list['SYSTEM_ID']         . "_" .
               $ina_varsass_list['VARS_LINK_ID']      . "_" .
               $ina_varsass_list['ASSIGN_SEQ']        . "_" .
               "1";

        if(! isset($in_VarsAssignRecodes[$key]))
        {
             $action  = "INSERT";
             $tgt_row = $arrayValue;

             // トレースメッセージ
             if ( $log_level === 'DEBUG' ){
                 $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70023",
                                                     array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                           $ina_varsass_list['PATTERN_ID'],
                                                           $ina_varsass_list['SYSTEM_ID'],
                                                           $ina_varsass_list['VARS_LINK_ID'],
                                                           $ina_varsass_list['ASSIGN_SEQ']));
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70024",
                                                array($ina_varsass_list['OPERATION_NO_UAPK'],
                                                      $ina_varsass_list['PATTERN_ID'],
                                                      $ina_varsass_list['SYSTEM_ID'],
                                                      $ina_varsass_list['VARS_LINK_ID'],
                                                      $ina_varsass_list['ASSIGN_SEQ']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }


        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["VARS_ENTRY"]       = $ina_varsass_list['VARS_ENTRY'];
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
            
        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // 登録する情報設定
            $tgt_row["ASSIGN_ID"]        = $retArray[0];
            $tgt_row['OPERATION_NO_UAPK'] = $ina_varsass_list['OPERATION_NO_UAPK'];
            $tgt_row['PATTERN_ID']        = $ina_varsass_list['PATTERN_ID'];
            $tgt_row['SYSTEM_ID']         = $ina_varsass_list['SYSTEM_ID'];
            $tgt_row['VARS_LINK_ID']      = $ina_varsass_list['VARS_LINK_ID'];
            $tgt_row['ASSIGN_SEQ']        = $ina_varsass_list['ASSIGN_SEQ'];
            $tgt_row["VARS_ENTRY"]        = $ina_varsass_list['VARS_ENTRY'];
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
            $tgt_row["DISUSE_FLAG"]      = '0';

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            
            // ロール管理ジャーナルに登録する情報設定
            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            $tgt_row["LAST_UPDATE_USER"]     = $db_access_user_id;

        }

        if($vg_driver_name == DF_LEGACY_DRIVER) {
            // 具体値にテンプレート変数が記述されているか判定
            if( $db_update_flg === false) {
                $var_match = array();
                $ret = preg_match_all("/{{(\s)" . "TPF_" . "[a-zA-Z0-9_]*(\s)}}/",$tgt_row["VARS_ENTRY"],$var_match);
                if(($ret !== false) && ($ret > 0)){
                    // テンプレート変数が記述されていることを記録
                    $db_update_flg = true;
                }
            }
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
 
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
    // F0016
    // 処理内容
    //   作業対象ホスト（一般変数）を更新する。
    //   
    // パラメータ
    //   $in_phoLinkData:             作業対象ホスト更新情報配列
    //   $in_PhoLinkRecodes:          作業対象ホストの全データ配列
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function addStg2PhoLnkDB($in_phoLinkData,&$in_PhoLinkRecodes) {

        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;

        global $db_access_user_id;
        global $strCurTablePhoLnk;
        global $strJnlTablePhoLnk;
        global $strSeqOfCurTablePhoLnk;
        global $strSeqOfJnlTablePhoLnk;
        global $arrayConfigOfPhoLnk;
        global $arrayValueTmplOfPhoLnk;

        $strCurTable      = $strCurTablePhoLnk;
        $strJnlTable      = $strJnlTablePhoLnk;

        $arrayConfig      = $arrayConfigOfPhoLnk;
        $arrayValue       = $arrayValueTmplOfPhoLnk;

        $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
        $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;


        $key = $in_phoLinkData["OPERATION_NO_UAPK"] . "_" .
               $in_phoLinkData["PATTERN_ID"]        . "_" .
               $in_phoLinkData["SYSTEM_ID"]         . "_" .
               '1';
        if(! isset($in_PhoLinkRecodes[$key])) {
            $action  = "INSERT";
            $tgt_row = $arrayValue;

             // トレースメッセージ
             if ( $log_level === 'DEBUG' ){
                 $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70032",
                                                     array($in_phoLinkData['OPERATION_NO_UAPK'],
                                                           $in_phoLinkData['PATTERN_ID'],
                                                           $in_phoLinkData['SYSTEM_ID']));
                 LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
             }


        }
        else{
            // 廃止なので復活する。
            $action = "UPDATE";
            $tgt_row = $in_PhoLinkRecodes[$key];

            unset($in_PhoLinkRecodes[$key]);

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-70033",
                                                    array($in_phoLinkData['OPERATION_NO_UAPK'],
                                                          $in_phoLinkData['PATTERN_ID'],
                                                          $in_phoLinkData['SYSTEM_ID']));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }

        }
        if($action == "UPDATE"){
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["DISUSE_FLAG"]      = '0';
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
            
        }
        else{
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスをロック                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfCurTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // テーブルシーケンスを採番                                   //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfCurTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }

            // 登録する情報設定
            $tgt_row["PHO_LINK_ID"]       = $retArray[0];
            $tgt_row['OPERATION_NO_UAPK'] = $in_phoLinkData['OPERATION_NO_UAPK'];
            $tgt_row['PATTERN_ID']        = $in_phoLinkData['PATTERN_ID'];
            $tgt_row['SYSTEM_ID']         = $in_phoLinkData['SYSTEM_ID'];
            $tgt_row["LAST_UPDATE_USER"]  = $db_access_user_id;
            $tgt_row["DISUSE_FLAG"]       = '0';

            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスをロック                               //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceLockInTrz($strSeqOfJnlTable,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

                return false;
            }
            ////////////////////////////////////////////////////////////////
            // ジャーナルシーケンスを採番                                 //
            ////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($strSeqOfJnlTable, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
                                             "PHO_LINK_ID",
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
 
            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
            $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rUtn = $objQueryUtn->sqlExecute();
        if($rUtn!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            $FREE_LOG = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);

            unset($objQueryUtn);
            unset($objQueryJnl);
            return false;
        }
        
        $rJnl = $objQueryJnl->sqlExecute();
        if($rJnl!=true){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90058",
                                     array($lva_table_nameTOid_list[$in_table_name],$in_row_id,$in_menu_title));
                LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
            }

            return false;
        }
        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0017
    // 処理内容
    //   インターフェース情報を取得する。
    //
    // パラメータ
    //   $ina_ans_if_info:        インターフェース情報
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
        $sql = "SELECT * FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG = '0'";

        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            $in_error_msg  = $msgstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
            LocalLogPrint(basename(__FILE__),__LINE__,$sql);
            LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());

            return false;
        }

        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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

        // レコード無しの場合は「ANSIBLEインタフェース情報」が登録されていない
        if( $num_of_rows === 0 ){
            if ( $log_level === 'DEBUG' ){
                //ANSIBLEインタフェース情報レコード無し
                $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56000");
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
    // F0018
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
    
        $strCurTable      = $strCurTableVarsAss;
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
           $key = $row["OPERATION_NO_UAPK"] . "_" .
                  $row["PATTERN_ID"]        . "_" .
                  $row["SYSTEM_ID"]         . "_" .
                  $row["VARS_LINK_ID"]      . "_" .
                  $row["ASSIGN_SEQ"]        . "_" .
                  $row["DISUSE_FLAG"];
           $in_VarsAssignRecodes[$key] = $row;
        }
        unset($objQueryUtn_sel);
        return true;
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // F0020
    // 処理内容
    //   作業対象ホストの全データ読込
    //
    // パラメータ
    //   $in_PhoLinkRecodes:     作業対象ホストの全データ配列
    //
    // 戻り値
    //   True:正常　　False:異常
    ////////////////////////////////////////////////////////////////////////////////
    function getPhoLinkRecodes(&$in_PhoLinkRecodes) {
    
        global    $db_model_ch;
        global    $objMTS;
        global    $objDBCA;
        global    $log_level;
    
        global $db_access_user_id;
        global $strCurTablePhoLnk;
        global $strJnlTablePhoLnk;
        global $strSeqOfCurTablePhoLnk;
        global $strSeqOfJnlTablePhoLnk;
        global $arrayConfigOfPhoLnk;
        global $arrayValueTmplOfPhoLnk;
    
        $strCurTable      = $strCurTablePhoLnk;
        $strJnlTable      = $strJnlTablePhoLnk;
    
        $arrayConfig      = $arrayConfigOfPhoLnk;
        $arrayValue       = $arrayValueTmplOfPhoLnk;
    
        $strSeqOfCurTable = $strSeqOfCurTablePhoLnk;
        $strSeqOfJnlTable = $strSeqOfJnlTablePhoLnk;
    
        // 廃止レコードも含めてる。 WHERE句がないと全件とれない模様
        $temp_array = array('WHERE'=>"PHO_LINK_ID <> '0'");
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             "PHO_LINK_ID",
                                             $strCurTable,
                                             $strJnlTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array);
    
        $sqlUtnBody = $retArray[1];
    
        $arrayUtnBind = array();

        $objQueryUtn = recordSelect($sqlUtnBody, $arrayUtnBind);
        if($objQueryUtn == null) {
            return false;
        }
    
        while($row = $objQueryUtn->resultFetch()) {
           $key = $row["OPERATION_NO_UAPK"] . "_" .
                  $row["PATTERN_ID"]        . "_" .
                  $row["SYSTEM_ID"]         . "_" .
                  $row["DISUSE_FLAG"];
           $in_PhoLinkRecodes[$key] = $row;
        }
        unset($objQueryUtn);
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
        $sql = $sql .     " WHERE  ROW_ID = $in_a_proc_loaded_list_pkey \n";

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
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $objQueryUtn->getLastError();
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }
        $errstr = $objQueryUtn->sqlBind($arrayUtnBind);
        if($errstr != "") {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $errstr;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            $msgstr = $sqlUtnBody . "\n" . $arrayUtnBind;
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            return null;
        }

        $r = $objQueryUtn->sqlExecute();
        if(!$r) {
            $msgstr = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-80000",array(basename(__FILE__),__LINE__));
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
?>
