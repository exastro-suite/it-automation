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
    //    legacy-Roleの代入値自動登録に必要な変数の初期値設定
    //
    //////////////////////////////////////////////////////////////////////

    // カラムタイプ
    define("DF_COL_TYPE_VAL",               "1");   //Value型
    define("DF_COL_TYPE_KEY",               "2");   //Key型
    define("DF_COL_TYPE_KEYVAL",            "3");   //Key-Value型

    // 代入値紐付メニューSELECT時のITA独自カラム名
    define("DF_ITA_LOCAL_OPERATION_CNT"     , "__ITA_LOCAL_COLUMN_1__");
    define("DF_ITA_LOCAL_HOST_CNT"          , "__ITA_LOCAL_COLUMN_2__");
    define("DF_ITA_LOCAL_DUP_CHECK_ITEM"    , "__ITA_LOCAL_COLUMN_3__");
    define("DF_ITA_LOCAL_PKEY"              , "__ITA_LOCAL_COLUMN_4__");

    // VARS_ATTRIBUTE_01 の 具体値定義
    const GC_VARS_ATTR_STD          = '1'; // 一般変数
    const GC_VARS_ATTR_LIST         = '2'; // 複数具体値
    const GC_VARS_ATTR_M_ARRAY      = '3'; // 多次元変数

    // DB更新ユーザー設定
    $db_valautostup_user_id      = -100019;

    ////////////////////////////////
    // 機能関連テーブル一覧       //
    ////////////////////////////////
    // 代入値自動登録設定テーブル名
    $lv_val_assign_tbl      = 'B_ANS_LRL_VAL_ASSIGN';
    // 作業パターン詳細テーブル名
    $lv_pattern_link_tbl    = 'B_ANSIBLE_LRL_PATTERN_LINK';
    // 変数一覧テーブル名
    $lv_vars_master_tbl     = 'B_ANSIBLE_LRL_VARS_MASTER';
    // 作業パターン変数紐付テーブル名
    $lv_ptn_vars_link_tbl   = 'B_ANS_LRL_PTN_VARS_LINK';
    // 多次元変数メンバー管理テーブル名
    $lv_array_member_tbl    = 'B_ANS_LRL_ARRAY_MEMBER';
    // 多次元変数配列組合せ管理テーブル名
    $lv_member_col_comb_tbl = 'B_ANS_LRL_MEMBER_COL_COMB';
    // 代入値管理テーブル名
    $lv_vars_assign_tbl     = 'B_ANSIBLE_LRL_VARS_ASSIGN';
    // 作業対象ホストテーブル名
    $lv_pho_link_tbl        = 'B_ANSIBLE_LRL_PHO_LINK';
    // CMDB代入値紐付対象メニューリストテーブル名
    $lv_cmdb_menu_list_tbl  = 'B_CMDB_MENU_LIST';
    // CMDB代入値紐付対象メニュー管理テーブル名
    $lv_cmdb_menu_table_tbl = 'B_CMDB_MENU_TABLE';
    // CMDB代入値紐付対象メニューカラム管理テーブル名
    $lv_cmdb_menu_col_tbl   = 'B_CMDB_MENU_COLUMN';

    ////////////////////////////////////////////////////////////////
    //----代入値自動登録設定
    ////////////////////////////////////////////////////////////////
    $strCurTableValAss      = $lv_val_assign_tbl;
    $strJnlTableValAss      = $strCurTableValAss . "_JNL";
    $strSeqOfCurTableValAss = $strCurTableValAss . "_RIC";
    $strSeqOfJnlTableValAss = $strCurTableValAss . "_JSQ";

    $arrayConfigOfValAss = array(
        "JOURNAL_SEQ_NO"=>""              ,
        "JOURNAL_ACTION_CLASS"=>""        ,
        "JOURNAL_REG_DATETIME"=>""        ,
        "COLUMN_ID"=>""                   ,
        "MENU_ID"=>""                     ,
        "COLUMN_LIST_ID"=>""              ,
        "COL_TYPE"=>""                    ,
        "PATTERN_ID"=>""                  ,
        "VAL_VARS_LINK_ID"=>""            ,
        "VAL_COL_SEQ_COMBINATION_ID"=>""  ,
        "VAL_ASSIGN_SEQ"=>""              ,
        "KEY_VARS_LINK_ID"=>""            ,
        "KEY_COL_SEQ_COMBINATION_ID"=>""  ,
        "KEY_ASSIGN_SEQ"=>""              ,
        "DISP_SEQ"=>""                    ,
        "DISUSE_FLAG"=>""                 ,
        "NOTE"=>""                        ,
        "LAST_UPDATE_TIMESTAMP"=>""       ,
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOfValAss = $arrayConfigOfValAss;

    ////////////////////////////////////////////////////////////////
    //----作業パターン詳細
    ////////////////////////////////////////////////////////////////
    $strCurTablePtnLnk           = $lv_pattern_link_tbl;
    $strJnlTablePtnLnk           = $strCurTablePtnLnk . "_JNL";
    $strSeqOfCurTablePtnLnk      = $strCurTablePtnLnk . "_RIC";
    $strSeqOfJnlTablePtnLnk      = $strCurTablePtnLnk . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----変数一覧
    ////////////////////////////////////////////////////////////////
    $strCurTableVarsMst          = $lv_vars_master_tbl;
    $strJnlTableVarsMst          = $strCurTableVarsMst . "_JNL";
    $strSeqOfCurTableVarsMst     = $strCurTableVarsMst . "_RIC";
    $strSeqOfJnlTableVarsMst     = $strCurTableVarsMst . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----作業パターン変数紐付
    ////////////////////////////////////////////////////////////////
    // #3079 2018/05/24 Update
    $strCurTablePtnVarsLnk       = $lv_ptn_vars_link_tbl;
    $strJnlTablePtnVarsLnk       = $strCurTablePtnVarsLnk . "_JNL";
    $strSeqOfCurTablePtnVarsLnk  = $strCurTablePtnVarsLnk . "_RIC";
    $strSeqOfJnlTablePtnVarsLnk  = $strCurTablePtnVarsLnk . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----多次元変数メンバー管理
    ////////////////////////////////////////////////////////////////
    $strCurTableArrMem           = $lv_array_member_tbl;
    $strJnlTableArrMem           = $strCurTableArrMem . "_JNL";
    $strSeqOfCurTableArrMem      = $strCurTableArrMem . "_RIC";
    $strSeqOfJnlTableArrMem      = $strCurTableArrMem . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----多次元変数配列組合せ管理
    ////////////////////////////////////////////////////////////////
    $strCurTableMemColComb       = $lv_member_col_comb_tbl;
    $strJnlTableMemColComb       = $strCurTableMemColComb . "_JNL";
    $strSeqOfCurTableMemColComb  = $strCurTableMemColComb . "_RIC";
    $strSeqOfJnlTableMemColComb  = $strCurTableMemColComb . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----代入値管理
    ////////////////////////////////////////////////////////////////
    $strCurTableVarsAss          = $lv_vars_assign_tbl;
    $strJnlTableVarsAss          = $strCurTableVarsAss . "_JNL";
    $strSeqOfCurTableVarsAss     = $strCurTableVarsAss . "_RIC";
    $strSeqOfJnlTableVarsAss     = $strCurTableVarsAss . "_JSQ";

    $arrayConfigOfVarAss = array(
        "JOURNAL_SEQ_NO"=>""          ,
        "JOURNAL_ACTION_CLASS"=>""    ,
        "JOURNAL_REG_DATETIME"=>""    ,
        "ASSIGN_ID"=>""               ,
        "OPERATION_NO_UAPK"=>""       ,
        "PATTERN_ID"=>""              ,
        "SYSTEM_ID"=>""               ,
        "VARS_LINK_ID"=>""            ,
        "COL_SEQ_COMBINATION_ID"=>""  ,
        "VARS_ENTRY"=>""              ,
        "ASSIGN_SEQ"=>""              ,
        "DISP_SEQ"=>""                ,
        "DISUSE_FLAG"=>""             ,
        "NOTE"=>""                    ,
        "LAST_UPDATE_TIMESTAMP"=>""   ,
        "LAST_UPDATE_USER"=>""
    );
    $arrayValueTmplOfVarAss = $arrayConfigOfVarAss;

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
    $strCurTableMenu           = $lv_cmdb_menu_list_tbl;
    $strJnlTableMenu           = $strCurTableMenu . "_JNL";
    $strSeqOfCurTableMenu      = $strCurTableMenu . "_RIC";
    $strSeqOfJnlTableMenu      = $strCurTableMenu . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----CMDB代入値紐付対象メニュー管理
    ////////////////////////////////////////////////////////////////
    $strCurTableMenuTbl        = $lv_cmdb_menu_table_tbl;
    $strJnlTableMenuTbl        = $strCurTableMenuTbl . "_JNL";
    $strSeqOfCurTableMenuTbl   = $strCurTableMenuTbl . "_RIC";
    $strSeqOfJnlTableMenuTbl   = $strCurTableMenuTbl . "_JSQ";

    ////////////////////////////////////////////////////////////////
    //----CMDB代入値紐付対象メニューカラム管理
    ////////////////////////////////////////////////////////////////
    $strCurTableMenuCol        = $lv_cmdb_menu_col_tbl;
    $strJnlTableMenuCol        = $strCurTableMenuCol . "_JNL";
    $strSeqOfCurTableMenuCol   = $strCurTableMenuCol . "_RIC";
    $strSeqOfJnlTableMenuCol   = $strCurTableMenuCol . "_JSQ";
?>
