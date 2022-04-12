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
    //      Terraform変数自動更新
    //
    //////////////////////////////////////////////////////////////////////

    // 起動しているshellの起動判定を正常にするための待ち時間
    sleep(1);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php      = '/libs/commonlibs/common_db_connect.php';
    $hostvar_search_php  = '/libs/commonlibs/common_terraform_hcl2json_parse.php';
    $db_access_user_id   = -101803; //Terraform変数更新プロシージャ

    //----変数名テーブル関連
    $strCurTableTerraformVarsTable = $vg_terraform_module_vars_link_table_name;
    $strJnlTableTerraformVarsTable = $strCurTableTerraformVarsTable."_JNL";
    $strSeqOfCurTableTerraformVars = $strCurTableTerraformVarsTable."_RIC";
    $strSeqOfJnlTableTerraformVars = $strCurTableTerraformVarsTable."_JSQ";


    $arrayConfigOfTerraformVarsTable = array(
        "JOURNAL_SEQ_NO"        => "",
        "JOURNAL_ACTION_CLASS"  => "",
        "JOURNAL_REG_DATETIME"  => "",
        "MODULE_VARS_LINK_ID"   => "",
        "MODULE_MATTER_ID"      => "",
        "VARS_NAME"             => "",
        "VARS_DESCRIPTION"      => "",
        "TYPE_ID"               => "",
        "VARS_VALUE"            => "",
        "DISP_SEQ"              => "",
        "ACCESS_AUTH"           => "",
        "DISUSE_FLAG"           => "",
        "NOTE"                  => "",
        "LAST_UPDATE_TIMESTAMP" => "",
        "LAST_UPDATE_USER"      => ""
    );

    $arrayValueTmplOfTerraformVarsTable = array(
        "JOURNAL_SEQ_NO"        => "",
        "JOURNAL_ACTION_CLASS"  => "",
        "JOURNAL_REG_DATETIME"  => "",
        "MODULE_VARS_LINK_ID"   => "",
        "MODULE_MATTER_ID"      => "",
        "VARS_NAME"             => "",
        "VARS_DESCRIPTION"      => "",
        "TYPE_ID"               => "",
        "VARS_VALUE"            => "",
        "DISP_SEQ"              => "",
        "ACCESS_AUTH"           => "",
        "DISUSE_FLAG"           => "",
        "NOTE"                  => "",
        "LAST_UPDATE_TIMESTAMP" => "",
        "LAST_UPDATE_USER"      => ""
    );
    //変数名テーブル関連----

    //----メンバー変数テーブル関連
    $strCurTableTerraformMemberVarsTable = $vg_terraform_var_member_table_name;
    $strJnlTableTerraformMemberVarsTable = $strCurTableTerraformMemberVarsTable."_JNL";
    $strSeqOfCurTableTerraformMemberVars = $strCurTableTerraformMemberVarsTable . "_RIC";
    $strSeqOfJnlTableTerraformMemberVars = $strCurTableTerraformMemberVarsTable . "_JSQ";

    $arrayConfigOfTerraformMemberVarsTable = array(
        "JOURNAL_SEQ_NO"          => "",
        "JOURNAL_ACTION_CLASS"    => "",
        "JOURNAL_REG_DATETIME"    => "",
        "CHILD_MEMBER_VARS_ID"    => "",
        "PARENT_VARS_ID"          => "",
        "PARENT_MEMBER_VARS_ID"   => "",
        "CHILD_MEMBER_VARS_KEY"   => "",
        "CHILD_MEMBER_VARS_NEST"  => "",
        "ARRAY_NEST_LEVEL"        => "",
        "CHILD_MEMBER_VARS_VALUE" => "",
        "CHILD_VARS_TYPE_ID"      => "",
        "ASSIGN_SEQ"              => "",
        "DISP_SEQ"                => "",
        "ACCESS_AUTH"             => "",
        "NOTE"                    => "",
        "DISUSE_FLAG"             => "",
        "LAST_UPDATE_TIMESTAMP"   => "",
        "LAST_UPDATE_USER"        => "",
    );

    $arrayValueTmplOfTerraformMemberVarsTable = array(
        "JOURNAL_SEQ_NO"          => "",
        "JOURNAL_ACTION_CLASS"    => "",
        "JOURNAL_REG_DATETIME"    => "",
        "CHILD_MEMBER_VARS_ID"    => "",
        "PARENT_VARS_ID"          => "",
        "PARENT_MEMBER_VARS_ID"   => "",
        "CHILD_MEMBER_VARS_KEY"   => "",
        "CHILD_MEMBER_VARS_NEST"  => "",
        "ARRAY_NEST_LEVEL"        => "",
        "CHILD_MEMBER_VARS_VALUE" => "",
        "CHILD_VARS_TYPE_ID"      => "",
        "ASSIGN_SEQ"              => "",
        "DISP_SEQ"                => "",
        "ACCESS_AUTH"             => "",
        "NOTE"                    => "",
        "DISUSE_FLAG"             => "",
        "LAST_UPDATE_TIMESTAMP"   => "",
        "LAST_UPDATE_USER"        => "",
    );
    //メンバー変数テーブル関連----

    //----変数ネスト管理テーブル関連
    $strCurTableTerraformMaxMemberColTable = $vg_terraform_max_member_col_table_name;
    $strJnlTableTerraformMaxMemberColTable = $strCurTableTerraformMaxMemberColTable."_JNL";
    $strSeqOfCurTableTerraformMaxMemberCol = $strCurTableTerraformMaxMemberColTable . "_RIC";
    $strSeqOfJnlTableTerraformMaxMemberCol = $strCurTableTerraformMaxMemberColTable . "_JSQ";
    //変数ネスト管理テーブル関連----
    $arrayConfigOfTerraformMaxMemberColTable = array(
        "JOURNAL_SEQ_NO"          => "",
        "JOURNAL_ACTION_CLASS"    => "",
        "JOURNAL_REG_DATETIME"    => "",
        "MAX_COL_SEQ_ID"          => "", // 項番
        "VARS_ID"                 => "", // 変数ID
        "MEMBER_VARS_ID"          => "", // メンバー変数ID
        "MAX_COL_SEQ"             => "", // 最大繰り返し数
        "DISP_SEQ"                => "",
        "ACCESS_AUTH"             => "",
        "NOTE"                    => "",
        "DISUSE_FLAG"             => "",
        "LAST_UPDATE_TIMESTAMP"   => "",
        "LAST_UPDATE_USER"        => ""
    );

    $arrayValueTmplOfTerraformMaxMemberColTable = array(
        "JOURNAL_SEQ_NO"          => "",
        "JOURNAL_ACTION_CLASS"    => "",
        "JOURNAL_REG_DATETIME"    => "",
        "MAX_COL_SEQ_ID"          => "", // 項番
        "VARS_ID"                 => "", // 変数ID
        "MEMBER_VARS_ID"          => "", // メンバー変数ID
        "MAX_COL_SEQ"             => "", // 最大繰り返し数
        "DISP_SEQ"                => "",
        "ACCESS_AUTH"             => "",
        "NOTE"                    => "",
        "DISUSE_FLAG"             => "",
        "LAST_UPDATE_TIMESTAMP"   => "",
        "LAST_UPDATE_USER"        => ""
    );

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $db_update_flg              = false;    // DB更新フラグ
    $aryAccessAuth              = array();  //アクセス許可情報
    $lv_a_proc_loaded_list_varsetup_pkey = 2100080001;
    $lv_a_proc_loaded_list_valsetup_pkey = 2100080002;

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        require_once ($root_dir_path . $hostvar_search_php);

        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-90001");
            require ($root_dir_path . $log_output_php );
        }

        // ITA側で管理している moduleファイル格納先ディレクトリ
        $vg_module_contents_dir = $vg_terraform_module_contents_dir;

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-90003");
            require ($root_dir_path . $log_output_php );
        }

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
        $ret = chkBackyardExecute($lv_a_proc_loaded_list_varsetup_pkey,
                                  $lv_UpdateRecodeInfo);

        if($ret === false) {
            $error_flag = 1;
            //「関連データベースの変更があるか確認に失敗しました。」
            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-161010");
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

        ////////////////////////////////
        // トランザクション開始             //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000100")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]トランザクション開始
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-100001");
            require ($root_dir_path . $log_output_php );
        }

        //----デッドロック防止のために、昇順でロック
        $aryTgtOfSequenceLock = array(
            $strSeqOfCurTableTerraformVars,
            $strSeqOfJnlTableTerraformVars
        );

        // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
        asort($aryTgtOfSequenceLock);
        foreach($aryTgtOfSequenceLock as $strSeqName){
            //ジャーナルのシーケンス
            $retArray = getSequenceLockInTrz($strSeqName,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000200")) );
            }
        }
        //デッドロック防止のために、昇順でロック----

        //ModuleIDと素材ファイル名を格納する配列を宣言
        $aryMatterFilePerMatterId = array();

        //ModuleIDとModule内で利用されている変数を管理する配列を宣言
        $aryModuleVarsData = array();

        $intFetchedFromTerraformTmpl = null;

        $intFetchedFromTerraformMatterFile = null;

        $strTableCurTerraformMatter    = "B_TERRAFORM_MODULE";
        $strColumnIdOfMatterId   = "MODULE_MATTER_ID";
        $strColumnIdOfMatterFile = "MODULE_MATTER_FILE";

        ////////////////////////////////////////////////////////////////
        // Module素材管理から必要なデータを取得
        ////////////////////////////////////////////////////////////////

        //----------------------------------------------
        // SQL生成 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $sqlUtnBody = "SELECT "
                     ." {$strColumnIdOfMatterId} MATTER_ID,"
                     ." {$strColumnIdOfMatterFile} MATTER_FILE, "
                     ." ACCESS_AUTH "
                     ."FROM {$strTableCurTerraformMatter} "     // リソーステーブル(B_TERRAFORM_MODULE)
                     ."WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000300")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000400")) );
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000500")) );
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ( $row = $objQueryUtn->resultFetch() ){
           // moduleIDをkeyにし、module素材ファイル名を配列に追加
            $aryMatterFilePerMatterId[$row["MATTER_ID"]] = $row["MATTER_FILE"];
            //アクセス許可情報追加
            $aryAccessAuth[$row["MATTER_ID"]] = $row["ACCESS_AUTH"];
        }
        // fetch行数を取得
        $intFetchedFromTerraformMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);


        //----リソース(Module素材)ごとにループ。
        //----------------------------------------------
        // リソース(Module素材)
        //----------------------------------------------
        foreach($aryMatterFilePerMatterId as $intMatterId=>$strMatterFile){
            $aryVarName = array();
            $addData = array();
            $aryVarMember = array();

            // リソース(Module素材)が未登録の場合は処理スキップ
            if(strlen($strMatterFile)===0){
                // Moduleファイルが未登録です。処理をスキップします。(Module:{})
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-151010",array($intMatterId));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                continue;
            }

            //******************************************************************
            // ******リソース(Module素材)で使用している変数を抜出す。*****
            //******************************************************************
            $ret = getHostVars($intMatterId,
                               $strMatterFile,
                               $aryVarName,
                               $aryVarMember);
            if($ret === false){
                // リソース(Module素材)で使用している変数抜出で一部エラーがあった。
                $warning_flag = 1;
                $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-151010",array($intMatterId));
                LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);
                continue;
            }

            foreach($aryVarName as $variable_block){
                $addData = array(
                    'moduleID'   => $intMatterId,
                    'varName'    => $variable_block["variable"],
                    'typeID'     => getTypeID($variable_block["typeStr"]),
                    'default'    => $variable_block["default"],
                    'memberVars' => array(
                        "type" => $variable_block["type"],
                        "default" => $variable_block["default"]
                    ),
                    'accessAuth' => $aryAccessAuth[$intMatterId]
                );
                array_push($aryModuleVarsData, $addData);
            }
        }

        $intFetchedFromTerraformVarsTable = null;

        //B_TERRAFORM_MODULE_VARS_LINKテーブルのレコードを格納する配列を宣言
        $aryRowFromTerraformVarsTable = array();

        $arrayConfig = $arrayConfigOfTerraformVarsTable;
        $arrayValue = $arrayValueTmplOfTerraformVarsTable;

        $temp_array = array('WHERE'=>" DISUSE_FLAG IN ('0','1') ");

        //----------------------------------------------
        // SQL作成  Module変数紐付けテーブル  B_TERRAFORM_MODULE_VARS_LINK
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT",
                                             "MODULE_VARS_LINK_ID",
                                             $strCurTableTerraformVarsTable,
                                             $strJnlTableTerraformVarsTable,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if( $objQueryUtn->getStatus()===false ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000600")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000700")) );
        }
        //----------------------------------------------
        // SQL実行
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000800")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            array_push($aryRowFromTerraformVarsTable, $row);
        }
        // fetch行数を取得
        $intFetchedFromTerraformVarsTable = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        //----------------------------------------------
        // 変数名テーブル作成
        //----------------------------------------------
        $moduleVarsLinkIDArray = [];
        // tfファイルから取得したメンバー変数
        $existMemberVarsArray = [];
        foreach($aryModuleVarsData as $data){
            $intModuleVarLinkId = null;
            $boolLoopNext = false;
            $strSqlType = null;
            $boolExistFlat = false;
            $moduleID = $data['moduleID'];
            $strVarName = $data['varName'];
            $variableTypeID  = $data['typeID'];
            $variableDefault = $data['default'];
            $memberVars = $data["memberVars"];
            $type_array = $memberVars["type"];
            $default_array = $memberVars["default"];
            $access_auth = $data['accessAuth'];

            if (is_array($default_array)) {
                $variableDefault = encodeHCL($default_array);
            }

            // Module素材各ファイルで使用している変数がModule変数紐付けにあるか確認
            foreach($aryRowFromTerraformVarsTable as $row){
                if($moduleID == $row['MODULE_MATTER_ID'] && $strVarName == $row['VARS_NAME'] && ($variableTypeID == $row['TYPE_ID'] || $row['TYPE_ID'] == NULL)){
                    $boolExistFlat = true;
                    $aryRowOfTableUpdate = $row;
                    break;
                }
            }

            //Module素材各ファイルで使用している変数が、Module変数紐付け管理テーブルに存在しない場合
            if($boolExistFlat == true){
                if ($row['TYPE_ID'] == NULL && $variableTypeID != $row['TYPE_ID']) {
                    $aryRowOfTableUpdate["TYPE_ID"] = $variableTypeID;
                    $aryRowOfTableUpdate["VARS_VALUE"] = $variableDefault;
                }
                else if ($variableDefault != $row['VARS_VALUE']) {
                    $aryRowOfTableUpdate["VARS_VALUE"] = $variableDefault;
                }
                //----活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                    //----SQLを発行せずループを抜けるフラグ、を立てる
                    $boolLoopNext = true;
                    //SQLを発行せずループを抜けるフラグ、を立てる----
                }
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                    //----SQLを発行するので、フラグは立てないまま維持する。
                    //$boolLoopNext = false;
                    //SQLを発行するので、フラグは立てないまま維持する。----
                }
                else{
                    //----存在しないはずの、値なので、想定外エラーに倒す。
                    // 異常フラグON
                    $error_flag = 1;
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000900")) );
                    //存在しないはずの、値なので、想定外エラーに倒す。----
                }

                $strSqlType = "UPDATE";
                $intModuleVarLinkId = $aryRowOfTableUpdate["MODULE_VARS_LINK_ID"];

                //活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。----
            }else{
                //----テーブルにないので、新たに挿入する。
                $aryRowOfTableUpdate = $arrayValueTmplOfTerraformVarsTable;

                // テーブルロック
                $retArray = getSequenceLockInTrz($strSeqOfCurTableTerraformVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001000")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfCurTableTerraformVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001100")) );
                }
                $intModuleVarLinkId = $retArray[0];

                $strSqlType = "INSERT";
                $aryRowOfTableUpdate["MODULE_VARS_LINK_ID"] = $intModuleVarLinkId;
                $aryRowOfTableUpdate["MODULE_MATTER_ID"] = $moduleID;
                $aryRowOfTableUpdate["VARS_NAME"] = $strVarName;
                $aryRowOfTableUpdate["TYPE_ID"] = $variableTypeID;
                $aryRowOfTableUpdate["VARS_VALUE"] = $variableDefault;
                //テーブルにないので、新たに挿入する。----
            }
            $moduleVarsLinkIDArray[] = [
                "intModuleVarLinkId"     => $intModuleVarLinkId,
                "moduleVarLinkTypeId"    => $variableTypeID,
                "type_array"             => $type_array,
                "default_array"          => $default_array
            ];

            // メンバー変数テーブルの作成
            if (is_array($type_array)) {
                $child_vars_id = 0;
                $parent_vars_id = 0;
                $array_nest_level = 0;
                $m = 0;

                // データの整形（配列の作成ローカル番号）
                createMemberArray($intModuleVarLinkId, $child_vars_id, $parent_vars_id, $tmp_member_data_array, $type_array, $default_array);

                // Module変数紐付管理登録対象が変数ネスト管理対象か判別
                foreach($tmp_member_data_array as $tmp_member_data) {
                    if ($tmp_member_data["module_regist_flag"] == true) {
                        // 変数ネスト管理対象の場合
                        if ($tmp_member_data["max_col_seq"] > 0) {
                            // 変数ネスト管理に既に登録されてあるか検索
                            $registedMaxMemberColData = getRegistMaxModuleColData($intModuleVarLinkId);
                            // 未登録の場合
                            if (!$registedMaxMemberColData["isRegist"]) {
                                $res = registMaxMemberCol($intModuleVarLinkId, NULL, $tmp_member_data["max_col_seq"], $access_auth);
                                if (!$res) {
                                    // 「変数ネスト管理の最大繰り返し数の登録に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221040", array(__FILE__, __LINE__));
                                    throw new Exception($errorMsg);
                                }
                            }
                            // 登録がある場合
                            else {
                                // 登録済みの最大繰り返し数と取得した要素数に差がある且つ最終更新者がシステムの場合
                                if ($registedMaxMemberColData["maxColSeq"] - $tmp_member_data["max_col_seq"] != 0 && $registedMaxMemberColData["isSystem"] == true) {
                                    $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tmp_member_data["max_col_seq"], $access_auth);
                                    if (!$res) {
                                        // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                        $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050");
                                        throw new Exception($errorMsg);
                                    }
                                }
                                // 最終更新者がシステム且つ廃止状態の場合
                                elseif ($registedMaxMemberColData["DISUSE_FLAG"] == 1 && $registedMaxMemberColData["isSystem"] == true)
                                {
                                    $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tmp_member_data["max_col_seq"], $access_auth);
                                    if (!$res) {
                                        // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                        $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                        throw new Exception($errorMsg);
                                    }
                                }
                                //アクセス許可情報が変更されている場合
                                elseif ($registedMaxMemberColData["ACCESS_AUTH"] != $access_auth) {
                                  $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $registedMaxMemberColData["maxColSeq"], $access_auth);
                                  if (!$res) {
                                      // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                      $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050");
                                      throw new Exception($errorMsg);
                                  }
                                }
                                // 最終更新者がユーザの場合
                                elseif ($registedMaxMemberColData["isSystem"] == false) {
                                    $tmp_member_data_array[$m]["max_col_seq"] = $registedMaxMemberColData["maxColSeq"];
                                    $tmp_member_data_array[$m]["force_max_col_seq_flag"] = true;
                                }
                            }
                        }
                        else {
                            // 変数ネスト管理に既に登録されてあるか検索
                            $registedMaxMemberColData = getRegistMaxModuleColData($intModuleVarLinkId);
                            if ($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 0) {
                                $res = deleteMaxMemberCol($registedMaxMemberColData["MAX_COL_SEQ_ID"]);
                                if (!$res) {
                                    // 「最大繰り返し数の廃止に失敗しました。」
                                    $error_flag = 1;
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221040", array(__FILE__, __LINE__));
                                    throw new Exception($errorMsg);
                                }
                            }
                        }
                    }
                    $m++;
                }
                $tmp_member_data_array = [];
                $tmp_member_data_array_2 = [];
                $tmp_member_data_array_3 = [];
                $member_data_array = [];

                // 変数ネスト管理に合わせたタイプの整形
                $type_array = adjustMemberTypeArrayByMaxColSeq($type_array, $tmp_member_data_array);

                $child_vars_id = 0;
                $parent_vars_id = 0;
                $array_nest_level = 0;
                createMemberArray($intModuleVarLinkId, $child_vars_id, $parent_vars_id, $tmp_member_data_array_2, $type_array, $default_array);
                // -----------------------------------------------
                // ローカルID→項番に差し替え、親項番の取得、変数ネスト管理テーブルから最大繰り返し数の取得
                $tmp_member_data_array_3 = createMemberArrayForRegist($tmp_member_data_array_2);
                // 変数ネスト管理に合わせてタイプの整形
                $type_array = adjustMemberTypeArrayByMaxColSeq($type_array, $tmp_member_data_array_3, true);

                // ここでローカル番号に振り替えられてしまうので注意
                $child_vars_id = 0;
                $parent_vars_id = 0;
                $array_nest_level = 0;
                createMemberArray($intModuleVarLinkId, $child_vars_id, $parent_vars_id, $member_data_array, $type_array, $default_array);
                $member_data_array = createMemberArrayForRegist($member_data_array);
                // レコードに登録可能な配列に整形
                $member_array = partMemberArrayForRegist($member_data_array);
                // -----------------------------------------------
                $existMemberVarsArray = array_merge($existMemberVarsArray, $member_array["update"]);
                $existMemberVarsArray = array_merge($existMemberVarsArray, $member_array["skip"]);
                $existMemberVarsArray = array_merge($existMemberVarsArray, $member_array["restore"]);
                $existMemberVarsArray = array_merge($existMemberVarsArray, $member_array["regist"]);

                // ---------------------ここから実際の登録------------
                // １．メンバー変数の登録
                if (!empty($member_array["regist"])) {
                    foreach ($member_array["regist"] as $member_data) {
                        // 既にB_TERRAFORM_VAR_MEMBERテーブルにあるレコードを取得
                        // $row_member_data = getMemberVarsByMemberData($member_data);
                        /*  PARENT_VARS_ID、PARENT_MEMBER_VARS_ID、CHILD_MEMBER_VARS_NEST、
                        CHILD_MEMBER_VARS_KEY、CHILD_VARS_TYPE_ID、ARRAY_NEST_LEVEL、ASSIGN_SEQ
                        が一致するレコードを検索
                        */
                        $res = registMemberVars($member_data);
                        if (!$res) {
                            $error_flag = 1;
                            // 「メンバー変数の登録に失敗しました。」
                            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221060", array(__FILE__, __LINE__, $member_data["CHILD_MEMBER_VARS_ID"]));
                            throw new Exception($errorMsg);
                        }
                    // typeが変数ネスト管理の対象である
                    if ($member_data["MAX_COL_SEQ"] > 0) {
                        $registedMaxMemberColData = getRegistMaxMemberColData($member_data["CHILD_MEMBER_VARS_ID"]);
                        // 変数ネスト管理に登録された要素数
                        $registedMaxColseq = $registedMaxMemberColData["maxColSeq"];
                        // tfファイルのdefaultから要素数を特定
                        $tfMaxColseq = $member_data["MAX_COL_SEQ"];
                        // 変数ネスト管理とtfファイルから取得した最大繰り返し数に差分があり、最終更新者がシステムの場合は変数ネスト管理の値を更新
                        if ($registedMaxMemberColData["isRegist"] == true && $registedMaxColseq != $tfMaxColseq && $registedMaxMemberColData["isSystem"] == true) {
                            $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tfMaxColseq, $access_auth);
                            if (!$res) {
                                // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                throw new Exception($errorMsg);
                            }
                        }
                        elseif($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 1 && $registedMaxMemberColData["isSystem"] == true) {
                            $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tfMaxColseq, $access_auth);
                            if (!$res) {
                                // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                throw new Exception($errorMsg);
                            }
                        }
                        elseif ($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 1 && $registedMaxMemberColData["isSystem"] == false) {
                            $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $registedMaxColseq, $access_auth);
                            if (!$res) {
                                // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                throw new Exception($errorMsg);
                            }
                        }
                        // 最大繰り返し数の登録
                        if ($registedMaxMemberColData["isRegist"] == false) {
                            $res = registMaxMemberCol($member_data["PARENT_VARS_ID"], $member_data["CHILD_MEMBER_VARS_ID"], $member_data["MAX_COL_SEQ"], $access_auth);

                            if (!$res) {
                                $error_flag = 1;
                                // 「変数ネスト管理の最大繰り返し数の登録に失敗しました。」
                                $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221040", array(__FILE__, __LINE__));
                                throw new Exception($errorMsg);
                            }
                        }
                    }
                    }
                }
                // ２．メンバー変数の更新
                if (!empty($member_array["update"])) {
                    foreach ($member_array["update"] as $member_data) {
                        $res = updateMemberVars($member_data["CHILD_MEMBER_VARS_ID"], $member_data["PARENT_MEMBER_VARS_ID"], $member_data["CHILD_MEMBER_VARS_VALUE"], $member_data["CHILD_VARS_TYPE_ID"]);
                        if (!$res) {
                            $error_flag = 1;
                            // 「メンバー変数の更新に失敗しました。」
                            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221070", array(__FILE__, __LINE__));
                            throw new Exception($errorMsg);
                        }
                        // 変数ネスト管理とtfファイルから取得した最大繰り返し数に差分があり、最終更新者がシステムの場合は変数ネスト管理の値を更新
                        if ($member_data["MAX_COL_SEQ"] > 0) {
                            $registedMaxMemberColData = getRegistMaxMemberColData($member_data["CHILD_MEMBER_VARS_ID"]);
                            // 変数ネスト管理に登録された要素数
                            $registedMaxColseq = $registedMaxMemberColData["maxColSeq"];
                            // tfファイルのdefaultから要素数を特定
                            $tfMaxColseq = $member_data["MAX_COL_SEQ"];
                            // 変数ネスト管理とtfファイルから取得した最大繰り返し数に差分があり、最終更新者がシステムの場合は変数ネスト管理の値を更新
                            if ($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 0  && $registedMaxColseq != $tfMaxColseq && $registedMaxMemberColData["isSystem"] == true) {
                                $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tfMaxColseq, $access_auth);
                                if (!$res) {
                                    // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $member_data["MAX_COL_SEQ"]));
                                    throw new Exception($errorMsg);
                                }
                            }
                        }
                    }
                }
                // ３．廃止されたレコードの復活
                if (!empty($member_array["restore"])) {
                    foreach ($member_array["restore"] as $member_data) {
                        $res = restoreMemberVars($member_data["CHILD_MEMBER_VARS_ID"]);
                        if (!$res) {
                            $error_flag = 1;
                            // 「メンバー変数の更新に失敗しました。」
                            $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221070", array(__FILE__, __LINE__));
                            throw new Exception($errorMsg);
                        }
                        // 変数ネスト管理とtfファイルから取得した最大繰り返し数に差分があり、最終更新者がシステムの場合は変数ネスト管理の値を更新
                        if ($member_data["MAX_COL_SEQ"] > 0) {
                            $registedMaxMemberColData = getRegistMaxMemberColData($member_data["CHILD_MEMBER_VARS_ID"]);
                            // 変数ネスト管理に登録された要素数
                            $registedMaxColseq = $registedMaxMemberColData["maxColSeq"];
                            // tfファイルのdefaultから要素数を特定
                            $tfMaxColseq = $member_data["MAX_COL_SEQ"];
                            // 復活させる
                            if ($registedMaxMemberColData["isRegist"]) {
                                $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tfMaxColseq, $access_auth);
                                if(!$res) {
                                    // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221060", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                    throw new Exception($errorMsg);
                                }
                            }
                            else {
                                $res = registMaxMemberCol($member_data["PARENT_VARS_ID"], $member_data["CHILD_MEMBER_VARS_ID"], $member_data["MAX_COL_SEQ"], $access_auth);
                                if (!$res) {
                                    $error_flag = 1;
                                    // 「変数ネスト管理の最大繰り返し数の登録に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $member_data["MAX_COL_SEQ"]));
                                    throw new Exception($errorMsg);
                                }
                            }
                        }
                    }
                }
                // ４．スキップの場合は変数ネスト管理の更新のみ
                if (!empty($member_array["skip"])) {
                    foreach ($member_array["skip"] as $member_data) {
                        // 変数ネスト管理とtfファイルから取得した最大繰り返し数に差分があり、最終更新者がシステムの場合は変数ネスト管理の値を更新
                        if ($member_data["MAX_COL_SEQ"] > 0) {
                            $registedMaxMemberColData = getRegistMaxMemberColData($member_data["CHILD_MEMBER_VARS_ID"]);
                            // 変数ネスト管理に登録された要素数
                            $registedMaxColseq = $registedMaxMemberColData["maxColSeq"];
                            // tfファイルのdefaultから要素数を特定
                            $tfMaxColseq = $member_data["MAX_COL_SEQ"];
                            // 変数ネスト管理とtfファイルから取得した最大繰り返し数に差分があり、最終更新者がシステムの場合は変数ネスト管理の値を更新
                            if ($registedMaxMemberColData["isRegist"] == true && $registedMaxColseq != $tfMaxColseq && $registedMaxMemberColData["isSystem"] == true) {
                                $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tfMaxColseq, $access_auth);
                                if (!$res) {
                                    // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage(
                                "ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                    throw new Exception($errorMsg);
                                }
                            }
                            elseif($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 1 && $registedMaxMemberColData["isSystem"] == true) {
                                $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $tfMaxColseq, $access_auth);
                                if (!$res) {
                                    // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                    throw new Exception($errorMsg);
                                }
                            }
                            elseif ($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 1 && $registedMaxMemberColData["isSystem"] == false) {
                                $res = updateMaxColSeq($registedMaxMemberColData["MAX_COL_SEQ_ID"], $registedMaxColseq, $access_auth);
                                if (!$res) {
                                    // 「変数ネスト管理の最大繰り返し数の更新に失敗しました。」
                                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221050", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                                    throw new Exception($errorMsg);
                                }
                            }
                        }
                    }
                }
                // ---------------------ここまで実際の登録------------
            }

            if( $boolLoopNext === true ){
                //----すでにレコードがあり、活性化済('0')なので、次のループへ
                continue;
                //すでにレコードがあり、活性化済('0')なので、次のループへ----
            }

            $retArray = getSequenceLockInTrz($strSeqOfJnlTableTerraformVars,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001200")) );
            }
            // テーブル シーケンスNoを採番
            $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformVars, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001300")) );
            }
            $intJournalSeqNo = $retArray[0];

            $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $intJournalSeqNo;
            $aryRowOfTableUpdate["DISUSE_FLAG"]      = "0";
            $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

            $arrayConfig = $arrayConfigOfTerraformVarsTable;
            $arrayValue = $aryRowOfTableUpdate;
            $temp_array = array();

            // DEBUGログに変更
            if ( $log_level === 'DEBUG' ){
                // 更新ログ
                ob_start();
                var_dump($arrayValue);
                $msgstr = ob_get_contents();
                ob_clean();
                LocalLogPrint(basename(__FILE__),__LINE__, $objMTS->getSomeMessage("ITATERRAFORM-STD-90004") . "\n" . $msgstr);
            }

            //データベース更新フラグをたてる
            $db_update_flg = true;

            //----------------------------------------------
            // SQL作成  Module変数紐付けテーブル  B_TERRAFORM_MODULE_VARS_LINK
            //----------------------------------------------
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 $strSqlType,
                                                 "MODULE_VARS_LINK_ID",
                                                 $strCurTableTerraformVarsTable,
                                                 $strJnlTableTerraformVarsTable,
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            //----------------------------------------------
            // クエリー生成
            //----------------------------------------------
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001400")) );
            }
            if( $objQueryJnl->getStatus()===false ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001500")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001600")) );
            }
            if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001700")) );
            }
            //----------------------------------------------
            // SQL実行  objQueryUtn
            //----------------------------------------------
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );

                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001800")) );
            }

            //----------------------------------------------
            // SQL実行  objQueryJnl
            //----------------------------------------------
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryJnl->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001900")) );
            }

            // DBアクセス事後処理
            unset($objQueryUtn);
            unset($objQueryJnl);
        }

        // 廃止状態 OR 存在しないModule変数IDリスト
        $disusedModuleIDArray = [];
        //----------------------------------------------
        // 実際にない変数名を廃止する & TYPE_IDが違っているものを廃止する
        //----------------------------------------------
        foreach($aryRowFromTerraformVarsTable as $row){
            $boolLoopNext = false;
            $strSqlType = null;
            $boolExistFlat = false;
            $rowModuleID = intval($row['MODULE_MATTER_ID']);
            $rowStrVarName = $row['VARS_NAME'];
            $rowTypeID = intval($row['TYPE_ID']);

            // Module素材各ファイルで使用している変数がModule変数紐付けにあるか確認
            foreach($aryModuleVarsData as $data){
                if($rowModuleID == intval($data['moduleID']) && $rowStrVarName == $data['varName'] && $rowTypeID == intval($data["typeID"])){
                    $boolExistFlat = true;
                    $aryRowOfTableUpdate = $row;
                    break;
                }
            }

            //Module紐付け管理テーブルに、Module素材各ファイルで使用していない変数が存在する場合
            if($boolExistFlat == false){
                //----廃止する
                $aryRowOfTableUpdate = $row;
                $disusedModuleIDArray[] = $row["MODULE_VARS_LINK_ID"];
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
                    //----廃止する
                    $strSqlType = "UPDATE";
                    //廃止する----
                }
                else if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "1" ){
                    //----廃止するべきレコードで、すでに廃止されている。
                    continue;
                    //廃止するべきレコードで、すでに廃止されている。----
                }
                else{
                    //----想定外エラー
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002000")) );
                    //想定外エラー----
                }

                // テーブル ロック
                $retArray = getSequenceLockInTrz($strSeqOfJnlTableTerraformVars,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002100")) );
                }
                // テーブル シーケンスNoを採番
                $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformVars, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002200")) );
                }
                $aryRowOfTableUpdate['JOURNAL_SEQ_NO']   = $retArray[0];
                $aryRowOfTableUpdate["DISUSE_FLAG"]      = "1";
                $aryRowOfTableUpdate["LAST_UPDATE_USER"] = $db_access_user_id;

                $strSqlType = "UPDATE";

                $arrayConfig = $arrayConfigOfTerraformVarsTable;
                $arrayValue  = $aryRowOfTableUpdate;
                $temp_array  = array();

                // DEBUGログに変更
                if ( $log_level === 'DEBUG' ){
                    // 更新ログ
                    ob_start();
                    var_dump($arrayValue);
                    $msgstr = ob_get_contents();
                    ob_clean();
                    LocalLogPrint(basename(__FILE__),__LINE__, $objMTS->getSomeMessage("ITATERRAFORM-STD-90005") . "\n" . $msgstr);
                }

                //データベース更新フラグをたてる
                $db_update_flg = true;

                //----------------------------------------------
                // SQL作成  Module変数紐付けテーブル  B_TERRAFORM_MODULE_VARS_LINK
                //----------------------------------------------
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     $strSqlType,
                                                     "MODULE_VARS_LINK_ID",
                                                     $strCurTableTerraformVarsTable,
                                                     $strJnlTableTerraformVarsTable,
                                                     $arrayConfig,
                                                     $arrayValue,
                                                     $temp_array );

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];

                //----------------------------------------------
                // クエリー生成
                //----------------------------------------------
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

                if( $objQueryUtn->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002300")) );
                }
                if( $objQueryJnl->getStatus()===false ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002400")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002500")) );
                }

                if( $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002600")) );
                }
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002700")) );
                }
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryJnl->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002800")) );
                }
                //廃止する----
                // DBアクセス事後処理
                unset($objQueryUtn);
                unset($objQueryJnl);
            }
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002900")) );
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]コミット;
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-100003");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // トランザクション終了              //
        ////////////////////////////////
        $objDBCA->transactionExit();
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]トランザクション終了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-100002");
            require ($root_dir_path . $log_output_php );
        }

        ///////////////////////////////////////////////////////////////////////////
        // 関連データベースの更新反映完了を登録
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
        // 関連データベースを更新している場合、代入値自動登録設定のバックヤード起動を登録
        ///////////////////////////////////////////////////////////////////////////
        if($db_update_flg === true) {
            if($log_level === "DEBUG") {
                //[処理]関連データベースを更新したのでバックヤード処理(valautostup-workflow)の起動を登録
                $traceMsg = $objMTS->getSomeMessage("ITATERRAFORM-STD-110004");
                LocalLogPrint(basename(__FILE__),__LINE__,$traceMsg);
            }
            $ret = setBackyardExecute($lv_a_proc_loaded_list_valsetup_pkey);
            if($ret === false) {
                $error_flag = 1;
                //バックヤード処理(valautostup-workflow)起動の登録に失敗しました。
                $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-161030");
                throw new Exception($errorMsg);
            }
        }

        // 存在しないModuleIDをPARENT_VARS_IDに持つメンバー変数管理と変数ネスト紐付管理のレコードは削除
        foreach ($disusedModuleIDArray as $disabledModuleID) {
            // メンバー変数
            $disabledMemberVarsArray = getMemberVarsByModuleID($disabledModuleID);
            foreach ($disabledMemberVarsArray as $disabledMemberVars) {
                $res = deleteMemberVars($disabledMemberVars["CHILD_MEMBER_VARS_ID"]);
                if (!$res) {
                    $error_flag = 1;
                    // 「メンバー変数の廃止に失敗しました。」
                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221090", array(__FILE__, __LINE__, $disabledMemberVars["CHILD_MEMBER_VARS_ID"]));
                    throw new Exception($errorMsg);
                }
            }
            // Module変数紐付管理が変数ネスト管理の対象
            $disabledMaxColSeqVarsArray = getMaxColSeqVarsByModuleID($disabledModuleID);
            if (!empty($disabledMaxColSeqVarsArray)) {
                foreach ($disabledMaxColSeqVarsArray as $disabledMaxColSeqVars) {
                    $res = deleteMaxMemberCol($disabledMaxColSeqVars["MAX_COL_SEQ_ID"]);
                    if (!$res) {
                        $error_flag = 1;
                        // 「最大繰り返し数の廃止に失敗しました。」
                        $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221060", array(__FILE__, __LINE__, $disabledMaxColSeqVars["MAX_COL_SEQ_ID"]));
                        throw new Exception($errorMsg);
                    }
                }
            }
        }

        $memberVarsArray = getAllMemberVars();
        // メンバー変数テーブルの作成
        $memberVarsIndex = 0;
        foreach ($memberVarsArray as $memberVars) {
            $deleteFlag = true;
            if (!empty($existMemberVarsArray)) {
                foreach ($existMemberVarsArray as $existMemberVars) {
                    if ($memberVars["CHILD_MEMBER_VARS_ID"] == $existMemberVars["CHILD_MEMBER_VARS_ID"]) {
                        $deleteFlag = false;
                    }
                }
            }
            if ($deleteFlag) {
                $res = deleteMemberVars($memberVars["CHILD_MEMBER_VARS_ID"]);
                if (!$res) {
                    $error_flag = 1;
                    // 「メンバー変数の廃止に失敗しました。」
                    $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221080", array(__FILE__, __LINE__, $memberVars["CHILD_MEMBER_VARS_ID"]));
                    throw new Exception($errorMsg);
                }
                // 変数ネスト管理に対象メンバー変数IDを持つレコードがあれば削除
                $registedMaxMemberColData = getRegistMaxMemberColData($memberVars["CHILD_MEMBER_VARS_ID"]);
                if ($registedMaxMemberColData["isRegist"] == true && $registedMaxMemberColData["DISUSE_FLAG"] == 0) {
                    $res = deleteMaxMemberCol($registedMaxMemberColData["MAX_COL_SEQ_ID"]);
                    if (!$res) {
                        // 「最大繰り返し数の廃止に失敗しました。」
                        $error_flag = 1;
                        $errorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221060", array(__FILE__, __LINE__, $registedMaxMemberColData["MAX_COL_SEQ_ID"]));
                        throw new Exception($errorMsg);
                    }
                }
            }
        }
    }
    catch (Exception $e){

        // 例外発生
        $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-151020");
        require ($root_dir_path . $log_output_php );

        // 例外メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );

        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);

        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                // [処理]ロールバック
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-100004");
            }
            else{
                // ロールバックに失敗しました
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101070");
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                // トランザクション終了
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-100002");
            }
            else{
                // トランザクションの終了時に異常が発生しました
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101030");
            }
            require ($root_dir_path . $log_output_php );
        }

    }


    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(異常)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101090");
            require ($root_dir_path . $log_output_php );
        }

        exit(0);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(警告)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101100");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(正常)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-90002");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }

    function LocalLogPrint($p1,$p2,$p3){
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $root_dir_path;
        global $log_output_php;
        $FREE_LOG = "FILE:$p1 LINE:$p2 $p3";
        echo $FREE_LOG . "\n";
        require ($root_dir_path . $log_output_php);
    }



    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   リソースファイル（Module素材）で使用している変数を取得
    //
    // パラメータ
    //   $in_filename:       リソースファイル（Module素材）名(Terraform)
    //   $in_pkey:           リソースファイル（Module素材） Pkey
    //   $ina_vars:          リソースファイル内の変数配列返却
    //                       [変数名]
    //
    // 戻り値
    //   boolean
    ////////////////////////////////////////////////////////////////////////////////
    function getHostVars($in_pkey,
                         $in_filename,
                         &$ina_vars,
                         &$member_vars){
        global          $objMTS, $root_dir_path;
        global          $vg_module_contents_dir;

        $ina_vars     = array();
        $intNumPadding = 10;

        //////////////////////////////////////////////
        // Module素材に登録されている変数を抜出す。
        //////////////////////////////////////////////
        // リソースファイル（Module素材）取得
        // リソースファイル（Module素材）名は $vg_module_contents_dir/Pkey(10桁)/Module素材ファイル名 する。
        $file_name = sprintf("%s/%s/%s",
                             $vg_module_contents_dir,
                             str_pad( $in_pkey, $intNumPadding, "0", STR_PAD_LEFT ),
                             $in_filename);

        // リソースファイル（Module素材）名の存在チェック
        if( file_exists($file_name) === false ){
            // システムで管理しているModuleファイルが存在しません。(ModuleID:{}  ファイル名:{})
            $msgstr = $objMTS->getSomeMessage("ITATERRAFORM-ERR-151030",array($in_pkey,basename($in_filename)));
            LocalLogPrint(basename(__FILE__),__LINE__,$msgstr);

            //これ以上処理続行できない
            return false;
        }

        // リソースファイル（Module素材）に登録されている変数を抜出。
        $local_vars = array();
        $objWSRA = new CommonTerraformHCL2JSONParse($root_dir_path, $file_name);

        $aryResultParse = $objWSRA->getParsedResult();
        unset($objWSRA);

        if (!$aryResultParse["res"]) {
            return false;
        }

        $ina_vars = $aryResultParse["variables"];
        // $ina_vars = $aryResultParse[1];

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
        $sql = $sql .     " WHERE  ROW_ID = $in_a_proc_loaded_list_pkey and (LOADED_FLG is NULL or LOADED_FLG <> '1') \n";

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

    //*******************************************************************************************
    //----ExecuteしてFetch前のDBアクセスオブジェクトを返却
    //*******************************************************************************************
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
    //*******************************************************************************************
    //----デフォルト値の取得
    //*******************************************************************************************
    function searchChildMemberVarsValueInDefault($trg_default_key_array, $default_array, $typeID)
    {
        $default = NULL;
        // 仮変数に入れる
        $_default_array = $default_array;
        // キー一覧を回してデフォルト値を追う
        foreach ($trg_default_key_array as $default_key) {
            if (isset($_default_array[$default_key])) {
                $default = $_default_array[$default_key];
            } else {
                $default = NULL;
            }
            $_default_array = $default;
        }
        // HCLにエンコードフラグが立っていたらエンコードする
        $typeInfo = getTypeInfo($typeID);
        if ($typeInfo["ENCODE_FLAG"] == 1) {
            if (is_array($default)) {
                $default = encodeHCL($default);
            }
        } else if ($typeInfo["MEMBER_VARS_FLAG"] == 1) {
            $default = NULL;
        }
        return $default;
    }

    //*******************************************************************************************
    //----テーブル登録用レコード作成前のデータ整形
    //*******************************************************************************************
    function createMemberArray($module_id, &$child_vars_id, &$parent_vars_id, &$member_data_array, $type_array, $default_array, $type_nest_array = array(), $array_nest_level = 0, $trg_default_key_array = array())
    {
        $err_flag               = 0;    // エラー検知
        $type_key               = "";
        $child_member_vars_nest = "";
        $module_regist_flag     = true; // Module変数管理に登録するかどうかのフラグ
        $assign_seq             = 0;    // 代入順序
        $child_member_vars_key  = NULL;
        $trg_default_key        = NULL;

        if (is_array($type_array)) {
            // タイプを分解（配列構造を崩す）
            foreach ($type_array as $type_key => $type_value) {
                // メンバー変数(キーのみ)を特定
                $type_nest_array[$array_nest_level] = $type_key;

                // 配列なら再帰でもう一周する
                if (is_array($type_value)) {
                    // ${type}からtype_idの取得
                    $type_id = getTypeID($type_key);
                    // 階層の管理
                    $array_nest_level++;
                    // もう一周
                    createMemberArray($module_id, $child_vars_id, $parent_vars_id, $member_data_array, $type_value, $default_array, $type_nest_array, $array_nest_level);
                    // 階層の管理
                    $array_nest_level--;
                }
                // 最端でループから抜ける
                else {
                    // boolを文字列に書き換え
                    if (is_bool($type_value)) $type_value = ((true === $type_value) ? 'true' : 'false');

                    // ${type}からtype_idの取得 ここは必ず$type_valueが${type}の形になる
                    $type_id = getTypeID($type_value);
                }
                //------------------------------
                // child_vars_type_idの特定
                //------------------------------
                // type_idをchild_vars_type_idに代入
                $child_vars_type_id = $type_id;

                if (($type_key != "" || $type_key == 0) && $child_vars_type_id != "") {
                    $parent_vars_id = $child_vars_id;
                    // ----------------------------
                    // メンバー変数(フル)の文字列取得
                    // ----------------------------
                    foreach ($type_nest_array as $type_nest_key) {
                        // タイプ名はメンバー変数に含まないのでカット
                        if (!preg_match('/^\$\{(.*?)\}$/', $type_nest_key)) {
                            // 数値なら[]で囲む
                            if (is_numeric($type_nest_key)) {
                                $type_nest_key = strval($type_nest_key);
                                $child_member_vars_nest == "" ? $child_member_vars_nest .= "[$type_nest_key]" : $child_member_vars_nest .= ".[$type_nest_key]";
                                $child_member_vars_key = "[$type_nest_key]";
                            }
                            // 文字列の場合
                            else {
                                $child_member_vars_nest == "" ? $child_member_vars_nest .= $type_nest_key : $child_member_vars_nest .= ".$type_nest_key";
                                $child_member_vars_key = $type_nest_key;
                            }
                            // デフォルト値を特定するためのキー
                            $trg_default_key = $type_nest_key;
                        }
                    }

                    // Module変数紐付けに登録するレコードかどうか判定
                    if ($array_nest_level > 0) {
                        $module_regist_flag = false;
                    }

                    // デフォルト値特定
                    $trg_default_key_array = [];
                    foreach ($type_nest_array as $type_nest) {
                        if (!preg_match('/^\$\{(.*?)\}$/', $type_nest)) {
                            $trg_default_key_array[] = $type_nest;
                        }
                    }
                    $child_member_vars_value = NULL; // デフォルト値
                    $child_member_vars_value = searchChildMemberVarsValueInDefault($trg_default_key_array, $default_array, $child_vars_type_id);
                    // ローカルIDの割り当て
                    $child_vars_id++;

                    // Module変数から最大繰り返し数を取得
                    $max_col_seq = countMaxColSeqByModule($trg_default_key_array, $default_array, $child_vars_type_id, $module_regist_flag);

                    // boolean型の場合、文字列に変換
                    if (is_bool($child_member_vars_value)) $child_member_vars_value = ((true === $child_member_vars_value) ? 'true' : 'false');

                    $res = [
                        "module_id"               => $module_id,               // Module変数紐付けの項番
                        "child_member_vars_id"    => $child_vars_id,           // 自分のローカルID
                        "parent_member_vars_id"   => $parent_vars_id,          // 親のローカルID
                        "child_member_vars_nest"  => $child_member_vars_nest,  // メンバー変数(フル)
                        "child_member_vars_key"   => $child_member_vars_key,   // メンバー変数のキーのみ
                        "child_member_vars_value" => $child_member_vars_value, // デフォルト値
                        "array_nest_level"        => $array_nest_level,        // 階層
                        "trg_default_key"         => $trg_default_key,
                        "child_vars_type_id"      => $child_vars_type_id,      // タイプID
                        "assign_seq"              => $assign_seq,              // 代入順序
                        "module_regist_flag"      => $module_regist_flag,      // Module変数紐付けに代入するかどうか

                        "type_nest_array"         => $type_nest_array,         // 自分までのキー/インデックス一覧
                        "max_col_seq"             => $max_col_seq,
                    ];

                    // 代入順序の管理
                    $assign_seq++;

                    // まとめ配列に詰める
                    $member_data_array[] = $res;
                } else if (($type_key != "" || $type_key == 0) && $child_vars_type_id == "") {
                    $member_data_array[$child_vars_id - 1]["array_nest_level"] = $array_nest_level;
                    $member_data_array[$child_vars_id - 1]["assign_seq"] = $assign_seq;
                    // 代入順序の管理
                    $assign_seq++;
                }
                $child_member_vars_nest = ""; // メンバー変数(フル)をリセット
            }
        } else {
            // タイプの特定
            $child_vars_type_id = NULL;
            $child_member_vars_value = NULL;
            if (preg_match('/^\$\{(.*?)\}$/', $type_array, $match)) {
                $child_vars_type_id = getTypeID($match[1]);
                $typeInfo = getTypeInfo($child_vars_type_id);
                if ($typeInfo["ENCODE_FLAG"] == 1) {
                    $child_member_vars_value = encodeHCL($default_array);
                }
                else if ($typeInfo["MEMBER_VARS_FLAG"] == 1) {
                    $child_member_vars_value = NULL;
                }
            }
            // boolean型の場合、文字列に変換
            if (is_bool($child_member_vars_value)) $child_member_vars_value = ((true === $child_member_vars_value) ? 'true' : 'false');

            $child_member_vars_key = "";
            $trg_default_key = "";

            // Module変数から最大繰り返し数を取得
            $max_col_seq = countMaxColSeqByModule($trg_default_key_array, $default_array, $child_vars_type_id, $module_regist_flag);

            $res = [
                "module_id"               => $module_id,               // Module変数紐付けの項番
                "child_member_vars_id"    => $child_vars_id,           // 自分のローカルID
                "parent_member_vars_id"   => $parent_vars_id,          // 親のローカルID
                "child_member_vars_nest"  => $child_member_vars_nest,  // メンバー変数(フル)
                "child_member_vars_key"   => $child_member_vars_key,   // メンバー変数のキーのみ
                "child_member_vars_value" => $child_member_vars_value, // デフォルト値
                "array_nest_level"        => $array_nest_level - 1,    // 階層
                "trg_default_key"         => $trg_default_key,
                "child_vars_type_id"      => $child_vars_type_id,      // タイプID
                "assign_seq"              => $assign_seq,              // 代入順序
                "module_regist_flag"      => $module_regist_flag,      // Module変数紐付けに代入するかどうか

                "type_nest_array"         => $type_nest_array,         // 自分までのキー/インデックス一覧
                "max_col_seq"             => $max_col_seq,
            ];
            $member_data_array[] = $res;
        }
    }


    //*******************************************************************************************
    //----メンバー変数をレコードに登録できる情報に振り分ける
    //*******************************************************************************************
    function partMemberArrayForRegist($memberInfoArray, $updateSequenceFlag = true)
    {
        global $root_dir_path, $strSeqOfCurTableTerraformMemberVars;
        $return = [
            "regist" => [],
            "update" => [],
            "skip" => [],
            "restore" => [],
        ];

        // 階層の振り直し
        // キーの取得
        $array_nest_level_keys = array_column($memberInfoArray, 'array_nest_level');
        // 昇順に並び替え
        // array_multisort($array_nest_level_keys, SORT_ASC, $memberInfoArray);
        // ネストを全て取得
        $array_nest_level_list = array_column($memberInfoArray, "array_nest_level");
        // 重複を削除
        $array_nest_level_list = array_unique($array_nest_level_list);
        // 昇順に並び替え
        sort($array_nest_level_list);
        $child_array_index = 0;

        // ネストの振り直し
        $new_array_nest_level = 0;
        $_memberInfoArray = $memberInfoArray;
        foreach ($array_nest_level_list as $array_nest_level_key) {
            $trg_flag = false;
            for ($i = 0; $i < count($memberInfoArray); $i++) {
                if ($array_nest_level_key == $memberInfoArray[$i]["array_nest_level"]) {
                    $_memberInfoArray[$i]["array_nest_level"] = $new_array_nest_level;
                    $trg_flag = true;
                }
            }
            if ($trg_flag) {
                $new_array_nest_level++;
            }
        }
        $memberInfoArray = $_memberInfoArray;

        // 列順序の振り直し
        // キーの取得
        $assign_seq_keys = array_column($memberInfoArray, 'assign_seq');
        // 昇順に並び替え
        array_multisort($array_nest_level_keys, SORT_ASC, $assign_seq_keys, SORT_ASC, $memberInfoArray);

        // ローカルIDをシーケンスIDに振り返る
        $child_array_index = 0;
        // 項番の更新
        foreach ($memberInfoArray as $memberInfo) {
            // 既存レコードの検索
            $searchData =
                [
                    "PARENT_VARS_ID"          => $memberInfo["module_id"],               // Module変数ID
                    "CHILD_MEMBER_VARS_KEY"   => $memberInfo["child_member_vars_key"],   // メンバー変数
                    "CHILD_MEMBER_VARS_NEST"  => $memberInfo["child_member_vars_nest"],  // メンバー変数(フルパス)
                    "ARRAY_NEST_LEVEL"        => $memberInfo["array_nest_level"],        // 階層
                    "CHILD_VARS_TYPE_ID"      => $memberInfo["child_vars_type_id"],      // タイプID
                    "ASSIGN_SEQ"              => $memberInfo["assign_seq"],              // 代入順序
                ];
            // 既存レコード
            $duplicatedMemberVars = getMemberVarsByMemberData($searchData);
            if (count($duplicatedMemberVars) < 1 && $updateSequenceFlag == true) {
                if ($memberInfo["module_regist_flag"] == false) {
                    // シーケンスの取得
                    // シーケンス番号を項番に変更する
                    $memberInfoArray[$child_array_index]["child_member_vars_id"] = getSequenceID($strSeqOfCurTableTerraformMemberVars);
                    // シーケンスの更新
                    $res = updateSequenceID($strSeqOfCurTableTerraformMemberVars);
                    if (!$res) {
                        return false;
                    }
                }
            }
            elseif (count($duplicatedMemberVars) > 0) {
                // 対応なし
                // 具体値の比較
                if ($duplicatedMemberVars["CHILD_MEMBER_VARS_VALUE"] != $memberInfo["child_member_vars_value"]) {
                    $memberInfoArray[$child_array_index]["update_flag"] = 1;
                }
                // 親メンバー変数IDに更新がある場合
                elseif ($duplicatedMemberVars["PARENT_MEMBER_VARS_ID"] != $memberInfo["parent_member_vars_id"]) {
                    $memberInfoArray[$child_array_index]["update_flag"] = 1;
                }
                // タイプに差分がある場合(NULL => 値を代入する場合のみ)
                elseif ($duplicatedMemberVars["CHILD_VARS_TYPE_ID"] != $memberInfo["child_vars_type_id"]) {
                    $memberInfoArray[$child_array_index]["update_flag"] = 1;
                }
                // 具体値に差分がなく、廃止状態なら復活させる
                elseif ($duplicatedMemberVars["DISUSE_FLAG"] == 1) {
                    $memberInfoArray[$child_array_index]["restore_flag"] = 1;
                }
                else {
                    $memberInfoArray[$child_array_index]["skip_flag"] = 1;
                }
                $memberInfoArray[$child_array_index]["child_member_vars_id"] = $duplicatedMemberVars["CHILD_MEMBER_VARS_ID"];
            }
            $child_array_index++;
        }

        // 親変数を検索
        $child_array_index = 0;
        foreach ($memberInfoArray as $memberInfo) {
            if ($memberInfo["module_regist_flag"] == false) {
                // 一番後ろのキーを削除して親を探す
                $parentMemberArray = $memberInfo["type_nest_array"];
                if (is_array($parentMemberArray) && count($parentMemberArray) > 0) {
                    array_pop($parentMemberArray);
                }
                // メンバー変数だと2こ前までキーを削除
                if (isset($memberInfo["child_vars_type_id"]) && getTypeInfo($memberInfo["child_vars_type_id"])["MEMBER_VARS_FLAG"] == 1) {
                    if (count($parentMemberArray) > 0) {
                        array_pop($parentMemberArray);
                    }
                }

                if (isset($memberInfo["array_nest_level"])) {
                    if ($memberInfo["array_nest_level"] != 1) {
                        $parent_array_index = array_search($parentMemberArray, array_column($memberInfoArray, "type_nest_array"));
                        if ($parent_array_index !== false) {
                            $memberInfo["parent_member_vars_id"] = $memberInfoArray[$parent_array_index]["child_member_vars_id"];
                        } else {
                            $memberInfo["parent_member_vars_id"] = NULL;
                        }
                    }
                    // ネストが1であれば親がいないので探さない
                    else {
                        $memberInfo["parent_member_vars_id"] = NULL;
                    }
                }

                if (isset($memberInfo["type_nest_array"])) {
                    // メンバー変数に登録する用配列に整形
                    $_return = [
                        "CHILD_MEMBER_VARS_ID"    => $memberInfo["child_member_vars_id"],    // メンバー変数ID
                        "PARENT_VARS_ID"          => $memberInfo["module_id"],               // Module変数ID
                        "PARENT_MEMBER_VARS_ID"   => $memberInfo["parent_member_vars_id"],   // 親のメンバー変数ID
                        "CHILD_MEMBER_VARS_KEY"   => $memberInfo["child_member_vars_key"],   // メンバー変数
                        "CHILD_MEMBER_VARS_NEST"  => $memberInfo["child_member_vars_nest"],  // メンバー変数(フルパス)
                        "CHILD_MEMBER_VARS_VALUE" => $memberInfo["child_member_vars_value"], // 具体値
                        "ARRAY_NEST_LEVEL"        => $memberInfo["array_nest_level"],        // 階層
                        "CHILD_VARS_TYPE_ID"      => $memberInfo["child_vars_type_id"],      // タイプID
                        "ASSIGN_SEQ"              => $memberInfo["assign_seq"],              // 代入順序
                        "MAX_COL_SEQ"             => $memberInfo["max_col_seq"],             // 最大繰り返し数
                    ];
                    if (isset($memberInfo["regist_flag"]) && isset($memberInfo["regist_flag"]) == 1) {
                        $return["regist"][] = $_return;
                    }
                    elseif (isset($memberInfo["update_flag"]) && isset($memberInfo["update_flag"]) == 1) {
                        $return["update"][] = $_return;
                    }
                    elseif (isset($memberInfo["restore_flag"]) && isset($memberInfo["restore_flag"]) == 1) {
                        $return["restore"][] = $_return;
                    }
                    elseif (isset($memberInfo["skip_flag"]) && $memberInfo["skip_flag"] == 1) {
                        $return["skip"][] = $_return;
                    }
                    else {
                        $return["regist"][] = $_return;
                    }
                }
            }
            $child_array_index++;
        }
        return $return;
    }

    //*******************************************************************************************
    //----メンバー変数をレコードに登録できる配列にする
    //    ・array_nest_levelの整理
    //    ・ローカルIDをシーケンスIDに振り替え
    //    ・親IDの取得
    //*******************************************************************************************
    function createMemberArrayForRegist($memberInfoArray, $updateSequenceFlag = true)
    {
        global $root_dir_path, $strSeqOfCurTableTerraformMemberVars;

        // 階層の振り直し
        // キーの取得
        $array_nest_level_keys = array_column($memberInfoArray, 'array_nest_level');
        // 昇順に並び替え
        // array_multisort($array_nest_level_keys, SORT_ASC, $memberInfoArray);
        // ネストを全て取得
        $array_nest_level_list = array_column($memberInfoArray, "array_nest_level");
        // 重複を削除
        $array_nest_level_list = array_unique($array_nest_level_list);
        // 昇順に並び替え
        sort($array_nest_level_list);
        $child_array_index = 0;

        // ネストの振り直し
        $new_array_nest_level = 0;
        $_memberInfoArray = $memberInfoArray;
        foreach ($array_nest_level_list as $array_nest_level_key) {
            $trg_flag = false;
            for ($i = 0; $i < count($memberInfoArray); $i++) {
                if (isset($memberInfoArray[$i])) {
                    if ($array_nest_level_key == $memberInfoArray[$i]["array_nest_level"]) {
                        $_memberInfoArray[$i]["array_nest_level"] = $new_array_nest_level;
                        $trg_flag = true;
                    }
                }
            }
            if ($trg_flag) {
                $new_array_nest_level++;
            }
        }
        $memberInfoArray = $_memberInfoArray;

        // 列順序の振り直し
        // キーの取得
        $assign_seq_keys = array_column($memberInfoArray, 'assign_seq');
        // 昇順に並び替え
        array_multisort($array_nest_level_keys, SORT_ASC, $assign_seq_keys, SORT_ASC, $memberInfoArray);

        $tmpMemberInfoArray = [];
        foreach ($memberInfoArray as $memberInfo) {
            if (isset($memberInfo["module_id"])) {
                $tmpMemberInfoArray[] = $memberInfo;
            }
        }
        $memberInfoArray = $tmpMemberInfoArray;

        // ローカルIDをシーケンスIDに振り返る
        $child_array_index = 0;
        // 項番の更新
        foreach ($memberInfoArray as $memberInfo) {
            // 既存レコードの検索
            $searchData =
                [
                    "PARENT_VARS_ID"          => $memberInfo["module_id"],               // Module変数ID
                    "CHILD_MEMBER_VARS_KEY"   => $memberInfo["child_member_vars_key"],   // メンバー変数
                    "CHILD_MEMBER_VARS_NEST"  => $memberInfo["child_member_vars_nest"],  // メンバー変数(フルパス)
                    "ARRAY_NEST_LEVEL"        => $memberInfo["array_nest_level"],        // 階層
                    "CHILD_VARS_TYPE_ID"      => $memberInfo["child_vars_type_id"],      // タイプID
                    "ASSIGN_SEQ"              => $memberInfo["assign_seq"],              // 代入順序
                ];
            $duplicatedMemberVars = getMemberVarsByMemberData($searchData);
            if (count($duplicatedMemberVars) < 1 && $updateSequenceFlag == true) {
                if ($memberInfo["module_regist_flag"] == false) {
                    // シーケンスの取得
                    // シーケンス番号を項番に変更する
                    $memberInfoArray[$child_array_index]["child_member_vars_id"] = getSequenceID($strSeqOfCurTableTerraformMemberVars);
                    // シーケンスの更新
                    $res = updateSequenceID($strSeqOfCurTableTerraformMemberVars);
                    if (!$res) {
                        return false;
                    }
                }
            } elseif (count($duplicatedMemberVars) > 0) {
                // 対応なし
                // 具体値の比較
                if ($duplicatedMemberVars["CHILD_MEMBER_VARS_VALUE"] != $memberInfo["child_member_vars_value"]) {
                    $memberInfoArray[$child_array_index]["update_flag"] = 1;
                }
                // タイプに差分がある場合(NULL => 値を代入する場合のみ)
                elseif ($duplicatedMemberVars["CHILD_VARS_TYPE_ID"] != $memberInfo["child_vars_type_id"]) {
                    $memberInfoArray[$child_array_index]["update_flag"] = 1;
                }
                // 具体値に差分がなく、廃止状態なら復活させる
                elseif ($duplicatedMemberVars["DISUSE_FLAG"] == 1) {
                    $memberInfoArray[$child_array_index]["restore_flag"] = 1;
                }
                else {
                    $memberInfoArray[$child_array_index]["skip_flag"] = 1;
                }
                $memberInfoArray[$child_array_index]["child_member_vars_id"] = $duplicatedMemberVars["CHILD_MEMBER_VARS_ID"];
            }
            $child_array_index++;
        }
        // 親変数を検索
        $child_array_index = 0;
        foreach ($memberInfoArray as $memberInfo) {
            if ($memberInfo["module_regist_flag"] == false) {
                // 一番後ろのキーを削除して親を探す
                $parentMemberArray = $memberInfo["type_nest_array"];
                if (!empty($parentMemberArray)) {
                    array_pop($parentMemberArray);
                }
                // メンバー変数だと2こ前までキーを削除
                if (getTypeInfo($memberInfo["child_vars_type_id"])["MEMBER_VARS_FLAG"] == 1) {
                    if (!empty($parentMemberArray)) {
                        array_pop($parentMemberArray);
                    }
                }

                if ($memberInfo["array_nest_level"] != 1) {
                    $parent_array_index = array_search($parentMemberArray, array_column($memberInfoArray, "type_nest_array"));
                    if ($parent_array_index !== false) {
                        $memberInfoArray[$child_array_index]["parent_member_vars_id"] = $memberInfoArray[$parent_array_index]["child_member_vars_id"];
                    } else {
                        $memberInfoArray[$child_array_index]["parent_member_vars_id"] = NULL;
                    }
                }
                // ネストが1であれば親がいないので探さない
                else {
                    $memberInfoArray[$child_array_index]["parent_member_vars_id"] = NULL;
                }
                // メンバー変数に登録する用配列に整形
            }
            // 変数ネスト管理が存在するか検索
            $res = getRegistMaxMemberColData($memberInfo["child_member_vars_id"]);
            // 変数ネスト管理に対象レコードが存在し、最終更新者がユーザの場合は最大繰り返し数を取得する。
            if ($res["isRegist"] == true && $res["isSystem"] == false) {
                $memberInfoArray[$child_array_index]["max_col_seq"] = $res["maxColSeq"];
            }
            $child_array_index++;
        }
        return $memberInfoArray;
    }

    //*******************************************************************************************
    //----メンバー変数の削除
    //*******************************************************************************************
    function deleteMemberVars($member_vars_id) {
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strCurTableTerraformMemberVarsTable, $strJnlTableTerraformMemberVarsTable;
        global $arrayConfigOfTerraformMemberVarsTable, $arrayValueTmplOfTerraformMemberVarsTable, $db_model_ch;
        global $db_access_user_id, $strSeqOfJnlTableTerraformMemberVars;

        $trg_record_array = [];

        //
        $sqlUtnBody = "SELECT * FROM {$strCurTableTerraformMemberVarsTable} WHERE CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID AND DISUSE_FLAG = 0";
        $arrayUtnBind = array("CHILD_MEMBER_VARS_ID" => $member_vars_id);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $trg_record_array[] = $row;
        }
        // fetch行数を取得
        $intFetchedFromTerraformMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);


        foreach ($trg_record_array as $trg_record) {

            // テーブル シーケンスNoを採番
            $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMemberVars, 'A_SEQUENCE', FALSE);
            if ($retArray[1] != 0) {
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
            }

            $arrayConfig = $arrayConfigOfTerraformMemberVarsTable;
            $arrayValue  = $arrayValueTmplOfTerraformMemberVarsTable;

            $arrayValue = $trg_record;

            $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
            $arrayValue['JOURNAL_REG_DATETIME']    = "";
            $arrayValue['JOURNAL_ACTION_CLASS']    = "UPDATE";
            $arrayValue["DISUSE_FLAG"]             = "1";

            $temp_array = array('WHERE' => " DISUSE_FLAG = 0 AND CHILD_MEMBER_VARS_ID = $member_vars_id");

            //----------------------------------------------
            // SQL作成  Module変数紐付けテーブル  B_TERRAFORM_MODULE_VARS_LINK
            //----------------------------------------------
            $retArray = makeSQLForUtnTableUpdate(
                $db_model_ch,
                "UPDATE",
                "CHILD_MEMBER_VARS_ID",
                $strCurTableTerraformMemberVarsTable,
                $strJnlTableTerraformMemberVarsTable,
                $arrayConfig,
                $arrayValue,
                $temp_array
            );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            //----------------------------------------------
            // クエリー生成
            //----------------------------------------------
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if ($objQueryUtn->getStatus() === false) {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
            }
            if ($objQueryJnl->getStatus() === false) {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
            }
            if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
            }
            if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
            }
            //----------------------------------------------
            // SQL実行  objQueryUtn
            //----------------------------------------------
            $rUtn = $objQueryUtn->sqlExecute();
            if ($rUtn != true) {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
            }

            //----------------------------------------------
            // SQL実行  objQueryJnl
            //----------------------------------------------
            $rJnl = $objQueryJnl->sqlExecute();
            if ($rJnl != true) {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
            }

            // DBアクセス事後処理
            unset($objQueryUtn);
            unset($objQueryJnl);

        }
        return true;
    }

    //*******************************************************************************************
    //----メンバー変数を登録する
    //*******************************************************************************************
    function registMemberVars($record_data)
    {
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strCurTableTerraformMemberVarsTable, $strJnlTableTerraformMemberVarsTable;
        global $arrayConfigOfTerraformMemberVarsTable, $arrayValueTmplOfTerraformMemberVarsTable, $db_model_ch;
        global $db_access_user_id, $strSeqOfJnlTableTerraformMemberVars;

        // シーケンスの取得

        //B_TERRAFORM_VAR_MEMBERテーブルのレコードを格納する配列を宣言
        // $aryRowFromTerraformMemberVarsTable = array();
        // テーブル シーケンスNoを採番
        $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMemberVars, 'A_SEQUENCE', FALSE);
        if ($retArray[1] != 0) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
        }

        // 最大繰り返し数を削除
        unset($record_data["MAX_COL_SEQ"]);

        // 親IDから親のtypeを検索、メンバー変数を取らないタイプか判定

        $arrayConfig = $arrayConfigOfTerraformMemberVarsTable;
        $arrayValue  = $arrayValueTmplOfTerraformMemberVarsTable;

        $arrayValue = $record_data;

        $arrayValue["LAST_UPDATE_USER"]        = $db_access_user_id;

        $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
        $arrayValue['JOURNAL_ACTION_CLASS']    = "INSERT";
        $arrayValue["DISUSE_FLAG"]             = "0";


        $temp_array = array();

        //----------------------------------------------
        // SQL作成  メンバー変数紐付け  B_TERRAFORM_VAR_MEMBER
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "INSERT",
                                            "CHILD_MEMBER_VARS_ID",
                                            $strCurTableTerraformMemberVarsTable,
                                            $strJnlTableTerraformMemberVarsTable,
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
        }
        if ($objQueryJnl->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__, $objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
        }
        if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
        }
        //----------------------------------------------
        // SQL実行  objQueryUtn
        //----------------------------------------------
        $rUtn = $objQueryUtn->sqlExecute();
        if ($rUtn != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
        }

        //----------------------------------------------
        // SQL実行  objQueryJnl
        //----------------------------------------------
        $rJnl = $objQueryJnl->sqlExecute();
        if ($rJnl != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
        }

        // DBアクセス事後処理
        unset($objQueryUtn);
        unset($objQueryJnl);

        return true;
    }

    //*******************************************************************************************
    //----メンバー変数を復活させる
    //*******************************************************************************************
    function restoreMemberVars($child_member_vars_id)
    {
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strCurTableTerraformMemberVarsTable, $strJnlTableTerraformMemberVarsTable;
        global $arrayConfigOfTerraformMemberVarsTable, $arrayValueTmplOfTerraformMemberVarsTable, $db_model_ch;
        global $db_access_user_id, $strSeqOfJnlTableTerraformMemberVars;

        $trg_record = [];
        // update対象のレコード情報を取得する
        $sqlUtnBody = "SELECT * FROM {$strCurTableTerraformMemberVarsTable} WHERE CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID";
        $arrayUtnBind = array("CHILD_MEMBER_VARS_ID" => $child_member_vars_id);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }

        while ($row = $objQueryUtn->resultFetch()) {
            $trg_record = $row;
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        //B_TERRAFORM_VAR_MEMBERテーブルのレコードを格納する配列を宣言
        $aryRowFromTerraformMemberVarsTable = array();
        // テーブル シーケンスNoを採番
        $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMemberVars, 'A_SEQUENCE', FALSE);
        if ($retArray[1] != 0) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
        }

        // 親IDから親のtypeを検索、メンバー変数を取らないタイプか判定

        $arrayConfig = $arrayConfigOfTerraformMemberVarsTable;
        $arrayValue  = $arrayValueTmplOfTerraformMemberVarsTable;

        $arrayValue = $trg_record;

        $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
        $arrayValue['JOURNAL_ACTION_CLASS']    = "UPDATE";
        $arrayValue["DISUSE_FLAG"]             = "0";

        $temp_array = array();

        //----------------------------------------------
        // SQL作成 メンバー変数紐付け  B_TERRAFORM_VAR_MEMBER
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate(
            $db_model_ch,
            "UPDATE",
            "CHILD_MEMBER_VARS_ID",
            $strCurTableTerraformMemberVarsTable,
            $strJnlTableTerraformMemberVarsTable,
            $arrayConfig,
            $arrayValue,
            $temp_array
        );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
        }
        if ($objQueryJnl->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
        }
        if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
        }
        //----------------------------------------------
        // SQL実行  objQueryUtn
        //----------------------------------------------
        $rUtn = $objQueryUtn->sqlExecute();
        if ($rUtn != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
        }

        //----------------------------------------------
        // SQL実行  objQueryJnl
        //----------------------------------------------
        $rJnl = $objQueryJnl->sqlExecute();
        if ($rJnl != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
        }

        // DBアクセス事後処理
        unset($objQueryUtn);
        unset($objQueryJnl);

        return true;
    }
    //*******************************************************************************************
    //----メンバー変数の親メンバー変数ID/デフォルト値を更新する
    //*******************************************************************************************
    function updateMemberVars($child_member_vars_id, $parent_member_vars_id, $default, $type_id)
    {
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strCurTableTerraformMemberVarsTable, $strJnlTableTerraformMemberVarsTable;
        global $arrayConfigOfTerraformMemberVarsTable, $arrayValueTmplOfTerraformMemberVarsTable, $db_model_ch;
        global $db_access_user_id, $strSeqOfJnlTableTerraformMemberVars;

        $trg_record = [];
        // update対象のレコード情報を取得する
        $sqlUtnBody = "SELECT * FROM {$strCurTableTerraformMemberVarsTable} WHERE CHILD_MEMBER_VARS_ID = :CHILD_MEMBER_VARS_ID";
        $arrayUtnBind = array("CHILD_MEMBER_VARS_ID" => $child_member_vars_id);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        // fetch行数を取得
        $rowCount = $objQueryUtn->effectedRowCount();
        if ($rowCount < 1) {
            return false;
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $trg_record = $row;
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        //B_TERRAFORM_VAR_MEMBERテーブルのレコードを格納する配列を宣言
        $aryRowFromTerraformMemberVarsTable = array();
        // テーブル シーケンスNoを採番
        $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMemberVars, 'A_SEQUENCE', FALSE);
        if ($retArray[1] != 0) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
        }

        // 親IDから親のtypeを検索、メンバー変数を取らないタイプか判定

        $arrayConfig = $arrayConfigOfTerraformMemberVarsTable;
        $arrayValue  = $arrayValueTmplOfTerraformMemberVarsTable;

        $arrayValue = $trg_record;

        $arrayValue["CHILD_VARS_TYPE_ID"]      = $type_id;
        $arrayValue["PARENT_MEMBER_VARS_ID"]   = $parent_member_vars_id;
        $arrayValue["CHILD_MEMBER_VARS_VALUE"] = $default;

        $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
        $arrayValue['JOURNAL_ACTION_CLASS']    = "UPDATE";
        $arrayValue["DISUSE_FLAG"]             = "0";

        $temp_array = array();

        //----------------------------------------------
        // SQL作成 メンバー変数紐付け  B_TERRAFORM_VAR_MEMBER
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate(
            $db_model_ch,
            "UPDATE",
            "CHILD_MEMBER_VARS_ID",
            $strCurTableTerraformMemberVarsTable,
            $strJnlTableTerraformMemberVarsTable,
            $arrayConfig,
            $arrayValue,
            $temp_array
        );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
        }
        if ($objQueryJnl->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
        }
        if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
        }
        //----------------------------------------------
        // SQL実行  objQueryUtn
        //----------------------------------------------
        $rUtn = $objQueryUtn->sqlExecute();
        if ($rUtn != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
        }

        //----------------------------------------------
        // SQL実行  objQueryJnl
        //----------------------------------------------
        $rJnl = $objQueryJnl->sqlExecute();
        if ($rJnl != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
        }

        // DBアクセス事後処理
        unset($objQueryUtn);
        unset($objQueryJnl);

        return true;
    }

    //*******************************************************************************************
    //----対象のメンバー変数を取得する
    //*******************************************************************************************
    function getMemberVarsByMemberData($member_data)
    {
        global $objDBCA, $objMTS, $strCurTableTerraformMemberVarsTable, $log_output_php;
        global $root_dir_path;
        $res = [];
        $_res = [];


        if (isset($member_data["PARENT_MEMBER_VARS_ID"])) {
            $sqlUtnBody = "SELECT * "
            . "FROM {$strCurTableTerraformMemberVarsTable} "     // メンバー変数テーブル(B_TERRAFORM_VAR_MEMBER)
            . "WHERE "
                . "PARENT_VARS_ID = :PARENT_VARS_ID "// or is null
                . "AND PARENT_MEMBER_VARS_ID <=> :PARENT_MEMBER_VARS_ID "
                . "AND CHILD_MEMBER_VARS_NEST <=> :CHILD_MEMBER_VARS_NEST "
                . "AND CHILD_MEMBER_VARS_KEY <=> :CHILD_MEMBER_VARS_KEY "
                . "AND (CHILD_VARS_TYPE_ID <=> :CHILD_VARS_TYPE_ID OR CHILD_VARS_TYPE_ID IS NULL)"
                . "AND ARRAY_NEST_LEVEL <=> :ARRAY_NEST_LEVEL "
                . "AND ASSIGN_SEQ <=> :ASSIGN_SEQ "
                // . "AND DISUSE_FLAG = 0 "
                . "ORDER BY LAST_UPDATE_TIMESTAMP ASC";

                $arrayUtnBind = array(
                    "PARENT_VARS_ID"         => $member_data["PARENT_VARS_ID"],
                    "PARENT_MEMBER_VARS_ID"  => $member_data["PARENT_MEMBER_VARS_ID"],
                    "CHILD_MEMBER_VARS_NEST" => $member_data["CHILD_MEMBER_VARS_NEST"],
                    "CHILD_MEMBER_VARS_KEY"  => $member_data["CHILD_MEMBER_VARS_KEY"],
                    "CHILD_VARS_TYPE_ID"     => $member_data["CHILD_VARS_TYPE_ID"],
                    "ARRAY_NEST_LEVEL"       => $member_data["ARRAY_NEST_LEVEL"],
                    "ASSIGN_SEQ"             => $member_data["ASSIGN_SEQ"],
                );
            } else {
                $sqlUtnBody = "SELECT * "
                . "FROM {$strCurTableTerraformMemberVarsTable} "     // メンバー変数テーブル(B_TERRAFORM_VAR_MEMBER)
                . "WHERE "
                . "PARENT_VARS_ID <=> :PARENT_VARS_ID "// or is null
                . "AND CHILD_MEMBER_VARS_NEST <=> :CHILD_MEMBER_VARS_NEST "
                . "AND CHILD_MEMBER_VARS_KEY <=> :CHILD_MEMBER_VARS_KEY "
                . "AND (CHILD_VARS_TYPE_ID <=> :CHILD_VARS_TYPE_ID OR CHILD_VARS_TYPE_ID IS NULL)"
                . "AND ARRAY_NEST_LEVEL <=> :ARRAY_NEST_LEVEL "
                . "AND ASSIGN_SEQ <=> :ASSIGN_SEQ "
                // . "AND DISUSE_FLAG = 0 "
                . "ORDER BY LAST_UPDATE_TIMESTAMP ASC";

                $arrayUtnBind = array(
                    "PARENT_VARS_ID"         => $member_data["PARENT_VARS_ID"],
                    "CHILD_MEMBER_VARS_NEST" => $member_data["CHILD_MEMBER_VARS_NEST"],
                    "CHILD_MEMBER_VARS_KEY"  => $member_data["CHILD_MEMBER_VARS_KEY"],
                    "CHILD_VARS_TYPE_ID"     => $member_data["CHILD_VARS_TYPE_ID"],
                    "ARRAY_NEST_LEVEL"       => $member_data["ARRAY_NEST_LEVEL"],
                    "ASSIGN_SEQ"             => $member_data["ASSIGN_SEQ"],
                );
        }


        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 メンバー変数管理(B_TERRAFORM_VAR_MEMBER)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $_res[] = $row;
        }
        // fetch行数を取得
        $rowCount = $objQueryUtn->effectedRowCount();

        // 検索対象が1件の場合
        if ($rowCount == 1) {
            $res = $_res[0];
        }
        // 検索対象が複数の場合
        /**
         * 優先順位
         * １．DISUSE_FLAGが0のもの
         * ２．どちらのDISUSE_FLAGも同じだった場合、最終更新日が新しいほう
         */
        elseif (count($_res) > 1) {
            // 日時順に取得しているので最後のレコードが最も新しいものになる
            $disuse_flag_0_ary = [];
            $disuse_flag_1_ary = [];
            foreach ($_res as $record) {
                if ($record["DISUSE_FLAG"] == 0) {
                    $disuse_flag_0_ary = $record;
                } else {
                    $disuse_flag_1_ary = $record;
                }
            }
            if (!empty($disuse_flag_0_ary)) {
                $res = $disuse_flag_0_ary;
            } else {
                $res = $disuse_flag_1_ary;
            }
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----ModuleIDからメンバー変数を取得
    //*******************************************************************************************
    function getMemberVarsByModuleID($moduleID)
    {
        global $objDBCA, $objMTS, $strCurTableTerraformMemberVarsTable, $log_output_php;
        global $root_dir_path;
        $res = [];

        $sqlUtnBody = "SELECT * "
            . "FROM {$strCurTableTerraformMemberVarsTable} "     // メンバー変数テーブル(B_TERRAFORM_VAR_MEMBER)
            . "WHERE DISUSE_FLAG = '0' "
            . "AND PARENT_VARS_ID = :PARENT_VARS_ID "; // or is null

        $arrayUtnBind = array(
            "PARENT_VARS_ID" => $moduleID,
        );

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 メンバー変数管理(B_TERRAFORM_VAR_MEMBER)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $res[] = $row;
        }
        // fetch行数を取得
        $intFetchedFromTerraformMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----ModuleIDから変数ネスト管理のレコードを取得
    //*******************************************************************************************
    function getMaxColSeqVarsByModuleID($moduleID)
    {
        global $objDBCA, $objMTS, $strCurTableTerraformMaxMemberColTable, $log_output_php;
        global $root_dir_path;
        $res = [];

        $sqlUtnBody = "SELECT * "
            . "FROM {$strCurTableTerraformMaxMemberColTable} "     // メンバー変数テーブル(B_TERRAFORM_LRL_MAX_MEMBER_COL)
            . "WHERE DISUSE_FLAG = '0' "
            . "AND VARS_ID = :VARS_ID "; // or is null

        $arrayUtnBind = array(
            "VARS_ID" => $moduleID,
        );

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 メンバー変数管理(B_TERRAFORM_VAR_MEMBER)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $res[] = $row;
        }
        // fetch行数を取得
        $intFetchedFromTerraformMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----メンバー変数をすべて取得
    //*******************************************************************************************
    function getAllMemberVars()
    {
        global $objDBCA, $objMTS, $strCurTableTerraformMemberVarsTable, $log_output_php;
        global $root_dir_path;
        $res = [];

        $sqlUtnBody = "SELECT * "
            . "FROM {$strCurTableTerraformMemberVarsTable} "     // メンバー変数テーブル(B_TERRAFORM_VAR_MEMBER)
            . "WHERE DISUSE_FLAG = '0' ";

        $arrayUtnBind = array();

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 メンバー変数管理(B_TERRAFORM_VAR_MEMBER)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $row["EXIST_FLAG"] = 0;
            $res[] = $row;
        }
        // fetch行数を取得
        $intFetchedFromTerraformMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----TypeのIDを取得する
    //*******************************************************************************************
    function getTypeID($strType)
    {
        global $objDBCA, $objMTS, $vg_terraform_types_master;
        global $root_dir_path;

        $type_id = "";

        // sql
        $sqlUtnBody = "SELECT "
        . " TYPE_ID "
        . "FROM {$vg_terraform_types_master} "     // タイプマスタ(B_TERRAFORM_TYPES_MASTER)
        . "WHERE DISUSE_FLAG = '0' "
        . "AND TYPE_NAME = :TYPE_NAME ";
        if (preg_match('/^\$\{(.*?)\}$/', $strType, $match_1)) {
            $strType = $match_1[1];
        }
        $arrayUtnBind = array("TYPE_NAME" => $strType);
        if (preg_match('/^(.*?)\(.*?\)$/', $strType, $match_2)) {
            // $strType = $match_2[1];
            $sqlUtnBody = "SELECT "
            . " TYPE_ID, TYPE_NAME "
            . "FROM {$vg_terraform_types_master} "     // タイプマスタ(B_TERRAFORM_TYPES_MASTER)
            . "WHERE DISUSE_FLAG = '0' "
            . "AND (TYPE_NAME = :TYPE_NAME1 OR TYPE_NAME = :TYPE_NAME2)";
            $arrayUtnBind = array("TYPE_NAME1" => $match_2[1], "TYPE_NAME2" => $strType);
        }

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        $rowCount = $objQueryUtn->effectedRowCount();

        if ($rowCount > 1) {
            while ($row = $objQueryUtn->resultFetch()) {
                if (trim($row["TYPE_NAME"]) == trim($strType)) {
                    $type_id = $row["TYPE_ID"];
                }
            }
        } else {
            while ($row = $objQueryUtn->resultFetch()) {
                $type_id = $row["TYPE_ID"];
            }
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $type_id;
    }

    //*******************************************************************************************
    //----Typeの情報を取得する
    //*******************************************************************************************
    function getTypeInfo($typeID)
    {
        global $objDBCA, $objMTS, $vg_terraform_types_master;
        global $root_dir_path;

        $typeInfo = [];

        $sqlUtnBody = "SELECT "
            . " * "
            . "FROM {$vg_terraform_types_master} "     // リソーステーブル(B_TERRAFORM_MODULE)
            . "WHERE DISUSE_FLAG = '0' "
            . "AND TYPE_ID = :TYPE_ID ";

        $arrayUtnBind = array("TYPE_ID" => $typeID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $typeInfo = $row;
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $typeInfo;
    }

    //*******************************************************************************************
    //----Module変数のTypeのIDを取得する(B_TERRAFORM_MODULE_VARS_LINK)
    //*******************************************************************************************
    function getModuleVarsLinkTypeInfo($moduleLinkVarsID)
    {
        global $objDBCA, $objMTS, $vg_terraform_module_vars_link_table_name;
        global $root_dir_path;

        $typeInfo = false;

        $sqlUtnBody = "SELECT "
            . " * "
            . "FROM {$vg_terraform_module_vars_link_table_name} "     // リソーステーブル(B_TERRAFORM_MODULE_VARS_LINK)
            . "WHERE DISUSE_FLAG = '0' "
            . "AND MODULE_VARS_LINK_ID = :MODULE_VARS_LINK_ID ";

        $arrayUtnBind = array("MODULE_VARS_LINK_ID" => $moduleLinkVarsID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // type情報格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $typeInfo = $row;
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $typeInfo;
    }

    //*******************************************************************************************
    //----jsonをHCL化する
    //*******************************************************************************************
    function encodeHCL($array) {
        $res = NULL;
        if (is_array($array)) {
            $json = json_encode($array, JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES);
            $res = preg_replace('/\"(.*?)\"\:(.*?)/', '"${1}" = ${2}', $json);
        }
        return $res;
    }

    //*******************************************************************************************
    //----変数ネスト管理にレコードを登録する
    //*******************************************************************************************
    function registMaxMemberCol($vars_id, $member_vars_id, $max_col_seq, $access_auth) {
        $res = false;
        global $strCurTableTerraformMaxMemberColTable; // B_TERRAFORM_LRL_MAX_MEMBER_COL
        // VARS_ID, MEMBER_VARS_ID, MAX_COL_SEQ
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strJnlTableTerraformMaxMemberColTable, $strSeqOfCurTableTerraformMaxMemberCol, $strSeqOfJnlTableTerraformMaxMemberCol;
        global $arrayConfigOfTerraformMaxMemberColTable, $arrayValueTmplOfTerraformMaxMemberColTable;
        global $db_model_ch, $db_access_user_id;

        // シーケンスの取得

        // テーブル シーケンスNoを採番
        $retArray = getSequenceValueFromTable($strSeqOfCurTableTerraformMaxMemberCol, 'A_SEQUENCE', FALSE);
        if ($retArray[1] != 0) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001300")));
        };
        $max_col_seq_id = $retArray[0];
        // テーブル シーケンスNoを採番
        $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMaxMemberCol, 'A_SEQUENCE', FALSE);
        if ($retArray[1] != 0) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
        }

        $arrayConfig = $arrayConfigOfTerraformMaxMemberColTable;
        $arrayValue  = $arrayValueTmplOfTerraformMaxMemberColTable;


        $arrayValue["LAST_UPDATE_USER"]        = $db_access_user_id;

        $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
        $arrayValue['JOURNAL_ACTION_CLASS']    = "INSERT";
        $arrayValue["DISUSE_FLAG"]             = "0";

        $arrayValue["MAX_COL_SEQ_ID"]          = $max_col_seq_id;// 項番
        $arrayValue["VARS_ID"]                 = $vars_id; // 変数ID
        if ($member_vars_id != NULL) {
            $arrayValue["MEMBER_VARS_ID"]      = $member_vars_id; // メンバー変数ID
        }
        $arrayValue["MAX_COL_SEQ"]             = $max_col_seq; // 最大繰り返し数
        
        $arrayValue["ACCESS_AUTH"]             = $access_auth;

        // $arrayConfig["MAX_COL_SEQ_ID"]         = $max_col_seq_id;// 項番

        $temp_array = array();

        //----------------------------------------------
        // SQL作成  変数ネスト管理  B_TERRAFORM_LRL_MAX_MEMBER_COL
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "INSERT",
                                            "MAX_COL_SEQ_ID",
                                            $strCurTableTerraformMaxMemberColTable,
                                            $strJnlTableTerraformMaxMemberColTable,
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
        }
        if ($objQueryJnl->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
        }
        if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
        }
        //----------------------------------------------
        // SQL実行  objQueryUtn
        //----------------------------------------------
        $rUtn = $objQueryUtn->sqlExecute();
        if ($rUtn != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
        }

        //----------------------------------------------
        // SQL実行  objQueryJnl
        //----------------------------------------------
        $rJnl = $objQueryJnl->sqlExecute();
        if ($rJnl != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
        }

        // DBアクセス事後処理
        unset($objQueryUtn);
        unset($objQueryJnl);

        $res = true;

        return $res;
    }

    //*******************************************************************************************
    //----変数ネスト管理情報の取得(Module変数紐付管理IDが登録済みかどうか/最終更新者/最大繰り返し数)
    //*******************************************************************************************
    function getRegistMaxModuleColData($moduleLinkVarsID) {
        global $objMTS;
        $res = [
            "isRegist"  => false, // 登録済みフラグ
            "isSystem"  => true,  // 最終更新者がシステムフラグ
            "maxColSeq" => 0,
            "MAX_COL_SEQ_ID" => NULL, // 項番
            "ACCESS_AUTH" => NULL, //アクセス許可情報
            "DISUSE_FLAG" => NULL,
        ];
        global $root_dir_path, $objDBCA, $vg_terraform_max_member_col_table_name;

        $sqlUtnBody = "SELECT "
            . " * "
            . "FROM {$vg_terraform_max_member_col_table_name} "     // 変数ネスト管理(B_TERRAFORM_LRL_MAX_MEMBER_COL)
            . "WHERE VARS_ID = :VARS_ID "
            . "AND MEMBER_VARS_ID IS NULL";

        $arrayUtnBind = array("VARS_ID" => $moduleLinkVarsID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行  変数ネスト管理(B_TERRAFORM_LRL_MAX_MEMBER_COL)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        $count = $objQueryUtn->effectedRowCount();
        if ($count > 0) {
            $res["isRegist"] = true;
            while ($row = $objQueryUtn->resultFetch()) {
                if ($row["LAST_UPDATE_USER"] > 0) {
                    $res["isSystem"] = false;
                }
                $res["maxColSeq"] = $row["MAX_COL_SEQ"];
                $res["MAX_COL_SEQ_ID"] = $row["MAX_COL_SEQ_ID"];
                $res["ACCESS_AUTH"] = $row["ACCESS_AUTH"];
                $res["DISUSE_FLAG"] = $row["DISUSE_FLAG"];
            }
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----変数ネスト管理情報の取得(メンバー変数が登録済みかどうか/最終更新者/最大繰り返し数)
    //*******************************************************************************************
    function getRegistMaxMemberColData($memberVarsID) {
        global $objMTS;
        $res = [
            "isRegist"  => false, // 登録済みフラグ
            "isSystem"  => true,  // 最終更新者がシステムフラグ
            "maxColSeq" => 0,
            "MAX_COL_SEQ_ID" => NULL, // 項番
            "DISUSE_FLAG" => NULL,
        ];
        global $root_dir_path, $objDBCA, $vg_terraform_max_member_col_table_name;

        $sqlUtnBody = "SELECT "
            . " * "
            . "FROM {$vg_terraform_max_member_col_table_name} "     // 変数ネスト管理(B_TERRAFORM_LRL_MAX_MEMBER_COL)
            . "WHERE MEMBER_VARS_ID = :MEMBER_VARS_ID ";

        $arrayUtnBind = array("MEMBER_VARS_ID" => $memberVarsID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行  変数ネスト管理(B_TERRAFORM_LRL_MAX_MEMBER_COL)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        $count = $objQueryUtn->effectedRowCount();
        if ($count > 0) {
            $res["isRegist"] = true;
            while ($row = $objQueryUtn->resultFetch()) {
                if ($row["LAST_UPDATE_USER"] > 0) {
                    $res["isSystem"] = false;
                }
                $res["maxColSeq"] = $row["MAX_COL_SEQ"];
                $res["MAX_COL_SEQ_ID"] = $row["MAX_COL_SEQ_ID"];
                $res["DISUSE_FLAG"] = $row["DISUSE_FLAG"];
            }
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----変数ネスト管理の最大繰り返し数を取得する
    //*******************************************************************************************
    function getMaxColSeq($maxMemberColID) {
        $res = false;
        global $root_dir_path, $objDBCA, $objMTS, $vg_terraform_max_member_col_table_name;

        $sqlUtnBody = "SELECT "
            . " MAX_COL_SEQ "
            . "FROM {$vg_terraform_max_member_col_table_name} "     // 変数ネスト管理(B_TERRAFORM_LRL_MAX_MEMBER_COL)
            . "WHERE DISUSE_FLAG = '0' "
            . "AND MAX_COL_SEQ_ID = :MAX_COL_SEQ_ID ";

        $arrayUtnBind = array("MAX_COL_SEQ_ID" => $maxMemberColID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行  変数ネスト管理(B_TERRAFORM_LRL_MAX_MEMBER_COL)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }

        //----------------------------------------------
        // 最大繰り返し数格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $res = $row["MAX_COL_SEQ"];
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        return $res;
    }

    //*******************************************************************************************
    //----変数ネスト管理の最大繰り返し数を更新する
    //*******************************************************************************************
    function updateMaxColSeq($maxMemberColID, $maxColSeq, $access_auth) {
        $res = false;
        global $strCurTableTerraformMaxMemberColTable; // B_TERRAFORM_LRL_MAX_MEMBER_COL
        // MAX_COL_SEQ_ID, VARS_ID, MEMBER_VARS_ID, MAX_COL_SEQ
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strJnlTableTerraformMaxMemberColTable, $strSeqOfCurTableTerraformMaxMemberCol, $strSeqOfJnlTableTerraformMaxMemberCol;
        global $arrayConfigOfTerraformMaxMemberColTable, $arrayValueTmplOfTerraformMaxMemberColTable;
        global $db_model_ch, $db_access_user_id;

        $trg_record = [];
        // update対象のレコード情報を取得する
        $sqlUtnBody = "SELECT * FROM {$strCurTableTerraformMaxMemberColTable} WHERE MAX_COL_SEQ_ID = :MAX_COL_SEQ_ID";
        $arrayUtnBind = array("MAX_COL_SEQ_ID" => $maxMemberColID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 変数ネスト管理テーブル(B_TERRAFORM_LRL_MAX_MEMBER_COL)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // 対象レコード格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $trg_record = $row;
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        //B_TERRAFORM_LRL_MAX_MEMBER_COLテーブルのレコードを格納する配列を宣言
        $aryRowFromTerraformMaxMemberColTable = array();
        // テーブル シーケンスNoを採番
        $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMaxMemberCol, 'A_SEQUENCE', FALSE);
        if ($retArray[1] != 0) {
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
        }

        $arrayConfig = $arrayConfigOfTerraformMaxMemberColTable;
        $arrayValue  = $arrayValueTmplOfTerraformMaxMemberColTable;

        $arrayValue = $trg_record;

        $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
        $arrayValue['JOURNAL_ACTION_CLASS']    = "UPDATE";
        $arrayValue["DISUSE_FLAG"]             = "0";

        $arrayValue["MAX_COL_SEQ"]             = $maxColSeq;      // 最大繰り返し数

        $arrayValue["LAST_UPDATE_USER"]        = $db_access_user_id;      // 最大繰り返し数
        
        $arrayValue["ACCESS_AUTH"]             = $access_auth;    // アクセス許可情報

        $temp_array = array();

        //----------------------------------------------
        // SQL作成 メンバー変数紐付け  B_TERRAFORM_VAR_MEMBER
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate(
            $db_model_ch,
            "UPDATE",
            "MAX_COL_SEQ_ID",
            $strCurTableTerraformMaxMemberColTable,
            $strJnlTableTerraformMaxMemberColTable,
            $arrayConfig,
            $arrayValue,
            $temp_array
        );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
        }
        if ($objQueryJnl->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
        }
        if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
        }
        //----------------------------------------------
        // SQL実行  objQueryUtn
        //----------------------------------------------
        $rUtn = $objQueryUtn->sqlExecute();
        if ($rUtn != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
        }

        //----------------------------------------------
        // SQL実行  objQueryJnl
        //----------------------------------------------
        $rJnl = $objQueryJnl->sqlExecute();
        if ($rJnl != true) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
        }

        // DBアクセス事後処理
        unset($objQueryUtn);
        unset($objQueryJnl);

        return true;
    }

    //*******************************************************************************************
    //----変数ネスト管理の対象か判定する
    //*******************************************************************************************
    function isMaxMemberColTrg($memberVarsID, $memberVarsTypeID, $memberVarsRecords, $memberFlag=true) {
        $res = false;
        global $root_dir_path, $objDBCA, $vg_terraform_max_member_col_table_name;

        // タイプの判定
        $memberVarsInfo = getTypeInfo($memberVarsTypeID);
        if ($memberVarsInfo["ASSIGN_SEQ_FLAG"] == 1 && $memberVarsInfo["MEMBER_VARS_FLAG"] == 1 && $memberVarsInfo["ENCODE_FLAG"] == 0) {
            // メンバー変数なら枝の末端でないかの確認
            if ($memberFlag == true) {
                // テーブルに登録される前の場合
                foreach($memberVarsRecords as $memberVarsRecord) {
                    if ($memberVarsRecord["PARENT_MEMBER_VARS_ID"] == $memberVarsID) {
                        $res = true;
                        break;
                    }
                }
            }
            // Module変数なら確実に変数ネスト管理対象
            else {
                $res = true;
            }
        }

        return $res;
    }

    //*******************************************************************************************
    //----対象IDのレコードを削除する（変数ネスト管理）
    //*******************************************************************************************
    function deleteMaxMemberCol($maxMemberID) {
        global $g, $objMTS, $objDBCA;
        global $root_dir_path, $log_output_php;
        global $strCurTableTerraformMaxMemberColTable, $strJnlTableTerraformMaxMemberColTable;
        global $arrayConfigOfTerraformMaxMemberColTable, $arrayValueTmplOfTerraformMaxMemberColTable, $db_model_ch;
        global $db_access_user_id, $strSeqOfJnlTableTerraformMaxMemberCol;

        $trg_record_array = [];

        //
        $sqlUtnBody = "SELECT * FROM {$strCurTableTerraformMaxMemberColTable} WHERE MAX_COL_SEQ_ID = :MAX_COL_SEQ_ID AND DISUSE_FLAG = 0";
        $arrayUtnBind = array("MAX_COL_SEQ_ID" => $maxMemberID);

        //----------------------------------------------
        // クエリー生成
        //----------------------------------------------
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        if ($objQueryUtn->getStatus() === false) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000300")));
        }
        if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000400")));
        }
        //----------------------------------------------
        // SQL実行 Module素材テーブル(B_TERRAFORM_MODULE)
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00000500")));
        }
        //----------------------------------------------
        // リソース（Module素材)ファイル名格納
        //----------------------------------------------
        while ($row = $objQueryUtn->resultFetch()) {
            $trg_record_array[] = $row;
        }
        // fetch行数を取得
        $intFetchedFromTerraformMatterFile = $objQueryUtn->effectedRowCount();

        // DBアクセス事後処理
        unset($objQueryUtn);


        foreach ($trg_record_array as $trg_record) {

            // テーブル シーケンスNoを採番
            $retArray = getSequenceValueFromTable($strSeqOfJnlTableTerraformMaxMemberCol, 'A_SEQUENCE', FALSE);
            if ($retArray[1] != 0) {
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00002200")));
            }

            $arrayConfig = $arrayConfigOfTerraformMaxMemberColTable;
            $arrayValue  = $arrayValueTmplOfTerraformMaxMemberColTable;

            $arrayValue = $trg_record;

            $arrayValue['JOURNAL_SEQ_NO']          = $retArray[0];
            $arrayValue['JOURNAL_REG_DATETIME']    = "";
            $arrayValue['JOURNAL_ACTION_CLASS']    = "UPDATE";
            $arrayValue["DISUSE_FLAG"]             = "1";

            $temp_array = array('WHERE' => " DISUSE_FLAG = 0 AND MAX_COL_SEQ_ID = $maxMemberID");

            //----------------------------------------------
            // SQL作成  Module変数紐付けテーブル  B_TERRAFORM_MODULE_VARS_LINK
            //----------------------------------------------
            $retArray = makeSQLForUtnTableUpdate(
                $db_model_ch,
                "UPDATE",
                "MAX_COL_SEQ_ID",
                $strCurTableTerraformMaxMemberColTable,
                $strJnlTableTerraformMaxMemberColTable,
                $arrayConfig,
                $arrayValue,
                $temp_array
            );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            //----------------------------------------------
            // クエリー生成
            //----------------------------------------------
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if ($objQueryUtn->getStatus() === false) {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001400")));
            }
            if ($objQueryJnl->getStatus() === false) {
                LocalLogPrint(basename(__FILE__),__LINE__, $objQueryJnl->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001500")));
            }
            if ($objQueryUtn->sqlBind($arrayUtnBind) != "") {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001600")));
            }
            if ($objQueryJnl->sqlBind($arrayJnlBind) != "") {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryJnl->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001700")));
            }
            //----------------------------------------------
            // SQL実行  objQueryUtn
            //----------------------------------------------
            $rUtn = $objQueryUtn->sqlExecute();
            if ($rUtn != true) {
                LocalLogPrint(basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001800")));
            }

            //----------------------------------------------
            // SQL実行  objQueryJnl
            //----------------------------------------------
            $rJnl = $objQueryJnl->sqlExecute();
            if ($rJnl != true) {
                LocalLogPrint(basename(__FILE__),__LINE__, $objQueryJnl->getLastError());
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-101010", array(__FILE__, __LINE__, "00001900")));
            }

            // DBアクセス事後処理
            unset($objQueryUtn);
            unset($objQueryJnl);

        }
        return true;
    }

    //*******************************************************************************************
    //----最大繰り返し数をModule変数から取得する
    //*******************************************************************************************
    function countMaxColSeqByModule($trg_default_key_array, $default_array, $typeID, $module_regist_flag=false) {

        $default = NULL;
        $max_col_seq = 0;
        // 変数ネスト管理対象か判定
        $typeInfo = getTypeInfo($typeID);
        if ($typeInfo != false) {
            if ($module_regist_flag == true && $typeInfo["MEMBER_VARS_FLAG"] == 1 && $typeInfo["ASSIGN_SEQ_FLAG"] == 1 && $typeInfo["ENCODE_FLAG"] == 0) {
                if (is_array($default_array)) {
                    $max_col_seq = count($default_array);
                }else{
                  $max_col_seq = 1;
                }
            }
            elseif ($typeInfo["MEMBER_VARS_FLAG"] == 1 && $typeInfo["ASSIGN_SEQ_FLAG"] == 1 && $typeInfo["ENCODE_FLAG"] == 0) {
                // 仮変数に入れる
                $_default_array = $default_array;
                // キー一覧を回してデフォルト値を追う
                foreach ($trg_default_key_array as $default_key) {
                    if (isset($_default_array[$default_key])) {
                        $default = $_default_array[$default_key];
                    } else {
                        $default = NULL;
                    }
                    $_default_array = $default;
                }
                if (is_array($default)) {
                    $max_col_seq = count($default);
                }else{
                  $max_col_seq = 1;
                }
            }
        }

        return $max_col_seq;
    }

    //*******************************************************************************************
    //----最大繰り返し数を適用させたtypeを返却する
    //*******************************************************************************************
    function generateMemberVarsTypeArray($member_vars_array, $trg_cnt, $member_vars_value, $typeInfo, $map)
    {
        $res = [];
        $member_vars_key = $map[count($map)-1];
        array_pop($map);

        if (empty($map)) {
            $temp_array = [];
            for ($i = 0; $i < $trg_cnt; $i++) {
                $temp_array[] = $member_vars_value;
            }
            // 仮配列と返却用配列をマージ
            $res[$member_vars_key] = $temp_array;
        }
        else {
            // 返却用配列
            $res = [];
            // 仮配列
            $temp_array = [];
            $ref = &$temp_array;

            // 多次元配列作成
            foreach ($map as $key) {
                $ref = &$ref[$key];
            }

            $temp = [];
            for ($i=0; $i < $trg_cnt; $i++) {
                $temp[] = $member_vars_value;
            }

            // メンバー変数を設定・具体値を代入
            if ($typeInfo["ENCODE_FLAG"] == 1) {
                $member_vars_value = decodeHCL($member_vars_value);
            }

            if ($typeInfo["MEMBER_VARS_FLAG"] == 1 && $typeInfo["MEMBER_VARS_FLAG"] != 1 ) {
                $ref[$member_vars_key] = [];
            } else {
                $ref[$member_vars_key] = $temp;
            }

            // 仮配列と返却用配列をマージ
            $res = array_replace_recursive($member_vars_array, $temp_array);
        }

        return $res;
    }

    //*******************************************************************************************
    //----最大繰り返し数を適用させ配列を返却する
    //*******************************************************************************************
    function adjustMemberTypeArrayByMaxColSeq($type_array, $member_data_array, $max_col_seq_search_flag=false) {
        $member_vars_array = [];
        foreach ($member_data_array as $member_data) {
            if (isset($member_data["max_col_seq"]) && $member_data["max_col_seq"] > 0 && isset($member_data["child_vars_type_id"])) {
                $trg_max_col_seq = $member_data["max_col_seq"];
                // タイプ情報
                $typeID = $member_data["child_vars_type_id"];
                $typeInfo = getTypeInfo($typeID);
                if ($typeInfo != false && $typeInfo["MEMBER_VARS_FLAG"] == 1 && $typeInfo["ASSIGN_SEQ_FLAG"] == 1 && $typeInfo["ENCODE_FLAG"] == 0) {
                    $trg_default_key_array = $member_data["type_nest_array"];
                    // 仮配列
                    $temp_array = [];
                    $ref = &$temp_array;

                    $_type_array = $type_array;
                    foreach ($trg_default_key_array as $trg_default_key) {
                        if (isset($_type_array[$trg_default_key])) {
                            $_type_array = $_type_array[$trg_default_key];
                        }
                    }

                    // 変数ネスト管理対象
                    $trg_nest_type_array = $_type_array;
                    if ($max_col_seq_search_flag == true) {
                        if (isset($member_data["module_regist_flag"]) && $member_data["module_regist_flag"] == true) {
                            $res = getRegistMaxModuleColData($member_data["module_id"]);
                            if ($res["isRegist"] == true && $res["isSystem"] == false) {
                                $trg_max_col_seq = $res["maxColSeq"];
                            }
                        }
                        else {
                            $res = getRegistMaxMemberColData($member_data["child_member_vars_id"]);
                            // 変数ネスト管理に対象レコードが存在し、最終更新者がユーザの場合は最大繰り返し数を取得する。
                            if ($res["isRegist"] == true && $res["isSystem"] == false) {
                                $trg_max_col_seq = $res["maxColSeq"];
                            }
                        }
                    }

                    // 変数ネスト管理対象の現在の最大繰り返し数
                    $nest_type_count = 0;
                    if (is_array($trg_nest_type_array)) {
                        $nest_type_count = count($trg_nest_type_array);
                    }
                    // else if(!is_array($trg_nest_type_array) && $trg_nest_type_array != "") {
                    //     $nest_type_count = 1;
                    // }

                    // 0番目が変数ネスト管理対象
                    $trg_nest_type_array_0 = "";
                    if (isset($trg_nest_type_array[0])) {
                        $trg_nest_type_array_0 = $trg_nest_type_array[0];
                    }

                    // // 変数ネスト管理対象の現在の最大繰り返し数
                    // $nest_type_count = count($trg_nest_type_array);

                    // // 0番目が変数ネスト管理対象
                    // $trg_nest_type_array_0 = "";
                    // if (isset($trg_nest_type_array[0])) {
                    //     $trg_nest_type_array_0 = $trg_nest_type_array[0];
                    // }

                    // 変更後の最大繰り返し数と元々の最大繰り返し数の差分
                    $max_col_seq_diff = $trg_max_col_seq - $nest_type_count;

                    // -------------------------------------
                    $made_type_array = NULL;

                    $__type_array = $type_array;
                    $member_vars_array = $type_array;

                    // マップ作製
                    $map = $trg_default_key_array;
                    // -------------------------------------

                    // 変更後の最大繰り返し数と元々の最大繰り返し数が一致していない場合
                    if ($max_col_seq_diff != 0 || isset($member_data["force_max_col_seq_flag"])) {
                        // 最大繰り返し数調整
                        $member_vars_array = generateMemberVarsTypeArray($member_vars_array, $trg_max_col_seq, $trg_nest_type_array_0, $typeInfo, $map);
                    }
                }
            }
        }
        if(empty($member_vars_array)) {
            $member_vars_array = $type_array;
        }

        return $member_vars_array;
    }

    //----------------------------------------------
    // HCLから配列にdecodeする
    //----------------------------------------------
    function decodeHCL($hcl)
    {
        $res = false;
        if (!is_array($hcl)) {
            $json = preg_replace('/\"(.*?)\"\ = \"(.*?)\"/', '"${1}": "${2}"', $hcl);
            $res = json_decode($json);
        }
        if (!$res) {
            $res = $hcl;
        }
        return $res;
    }


?>