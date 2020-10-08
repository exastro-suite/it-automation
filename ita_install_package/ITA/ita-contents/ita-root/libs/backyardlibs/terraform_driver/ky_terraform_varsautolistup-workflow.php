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
    $hostvar_search_php  = '/libs/backyardlibs/terraform_driver/WrappedStringReplaceAdmin.php';
    $db_access_user_id   = -101803; //Terraform変数更新プロシージャ

    // WrappedStringReplaceAdmin.phpで使用する変数定義
    // ユーザーホスト変数名の先頭文字
    //define("DF_HOST_VAR_HED"               ,"var."); //ky_terraform_common_setenv.phpで定義

    //----変数名テーブル関連
    $strCurTableTerraformVarsTable = $vg_terraform_module_vars_link_table_name;
    $strJnlTableTerraformVarsTable = $strCurTableTerraformVarsTable."_JNL";
    $strSeqOfCurTableTerraformVars = $strCurTableTerraformVarsTable."_RIC";
    $strSeqOfJnlTableTerraformVars = $strCurTableTerraformVarsTable."_JSQ";

    $arrayConfigOfTerraformVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MODULE_VARS_LINK_ID"=>"",
        "MODULE_MATTER_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_DESCRIPTION"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayValueTmplOfTerraformVarsTable = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MODULE_VARS_LINK_ID"=>"",
        "MODULE_MATTER_ID"=>"",
        "VARS_NAME"=>"",
        "VARS_DESCRIPTION"=>"",
        "DISP_SEQ"=>"",
        "DISUSE_FLAG"=>"",
        "NOTE"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    //変数名テーブル関連----

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $db_update_flg              = false;    // DB更新フラグ
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
                     ." {$strColumnIdOfMatterFile} MATTER_FILE "
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
                               $aryVarName);
            if($ret === false){
                // リソース(Module素材)で使用している変数抜出で一部エラーがあった。
                $warning_flag = 1;
            }

            //変数名でループ
            foreach($aryVarName as $varName=>$dummy){
                $addData = array(
                    'moduleID' => $intMatterId,
                    'varName' => $varName
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
        foreach($aryModuleVarsData as $data){
            $intModuleVarLinkId = null;
            $boolLoopNext = false;
            $strSqlType = null;
            $boolExistFlat = false;
            $moduleID = $data['moduleID'];
            $strVarName = $data['varName'];

            // Module素材各ファイルで使用している変数がModule変数紐付けにあるか確認
            foreach($aryRowFromTerraformVarsTable as $row){
                if($moduleID == $row['MODULE_MATTER_ID'] && $strVarName == $row['VARS_NAME']){
                    $boolExistFlat = true;
                    $aryRowOfTableUpdate = $row;
                    break;
                }
            }

            //Module素材各ファイルで使用している変数が、Module変数紐付け管理テーブルに存在しない場合
            if($boolExistFlat == true){
                //----活性中('0')ならそのまま、廃止('1')されているなら復活、そのほかなら想定外エラーに倒す。
                if( $aryRowOfTableUpdate["DISUSE_FLAG"] == "0" ){
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
                //テーブルにないので、新たに挿入する。----
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


        //----------------------------------------------
        // 実際にない変数名を廃止する
        //----------------------------------------------
        foreach($aryRowFromTerraformVarsTable as $row){
            $boolLoopNext = false;
            $strSqlType = null;
            $boolExistFlat = false;
            $rowModuleID = $row['MODULE_MATTER_ID'];
            $rowStrVarName = $row['VARS_NAME'];

            // Module素材各ファイルで使用している変数がModule変数紐付けにあるか確認
            foreach($aryModuleVarsData as $data){
                if($rowModuleID == $data['moduleID'] && $rowStrVarName == $data['varName']){
                    $boolExistFlat = true;
                    $aryRowOfTableUpdate = $row;
                    break;
                }
            }

            //Module紐付け管理テーブルに、Module素材各ファイルで使用していない変数が存在する場合
            if($boolExistFlat == false){
                //----廃止する
                $aryRowOfTableUpdate = $row;
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
                         &$ina_vars){
        global          $objMTS;
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

        // リソースファイル（Module素材）の内容読込
        $dataString = file_get_contents($file_name);

        // リソースファイル（Module素材）に登録されている変数を抜出。
        $local_vars = array();
        $objWSRA = new WrappedStringReplaceAdmin(DF_HOST_VAR_HED,$dataString,$local_vars);

        $aryResultParse = $objWSRA->getParsedResult();
        unset($objWSRA);

        // リソースファイル（Module素材）に登録されている変数退避
        foreach( $aryResultParse[1] as $var_name ){
            // 変数名を一意にする。
            $ina_vars[$var_name] = 1;
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


?>