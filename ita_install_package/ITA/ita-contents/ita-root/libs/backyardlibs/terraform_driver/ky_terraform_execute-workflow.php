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
    //      Terraform 作業インスタンス実行    
    //      対象：ステータス「未実施」および「準備中」
    //      処理：対象のインスタンスについて、TFEに作業登録およびplan/applyの実行。
    //          　作業登録完了後、ステータスを「実行中」とする。
    // 
    /////////////////////////////////////////////////////////////////////

    //----------------------------------------------
    // 定数定義
    //----------------------------------------------
    $log_output_php                   = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php                 = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                   = '/libs/commonlibs/common_db_connect.php';
    $db_access_php                    = '/libs/backyardlibs/common/common_db_access.php';
    $terraform_restapi_php            = '/libs/commonlibs/common_terraform_restapi.php';

    //----------------------------------------------
    // ローカル変数(全体)宣言
    //----------------------------------------------
    $warning_flag                     = 0; // 警告フラグ(1：警告発生)
    $error_flag                       = 0; // 異常フラグ(1：異常発生)
    $db_access_user_id                = -101802; // Terraform作業実行プロシージャ
    $tgt_execution_no_array           = array();    // 処理対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_array_without_2 = array();    // 処理対象から準備中を除くEXECUTION_NOのリストを格納
    //$tgt_run_mode_array               = array();    // 処理対象のドライランモードのリストを格納
    //$tgt_run_mode_no_array_without_2  = array();    // 処理対象から準備中を除くドライランモードのリストを格納
    $tgt_exec_count_array             = array();    // 処理対象の並列実行数のリストを格納
    $tgt_exec_count_array_without_2   = array();    // 処理対象から準備中を除く並列実行数のリストを格納
    $tgt_operation_id_array           = array();    // 処理対象のOPERATION_NO_UAPKのリストを格納
    $tgt_row_array                    = array();    // 処理対象のレコードまるごと格納
    $tgt_row_array_without_2          = array();    // 処理対象から準備中を除くレコードまるごと格納
    $num_of_tgt_execution_no          = 0;          // 処理対象のEXECUTION_NOの個数を格納
    $tgt_config_dir                   = "";          // 処理対象のEXECUTION_NOの個数を格納
    $tgt_Contents                     = array();    // REST API Contents領域
    $aryTerraformWorkingDir           = array();    // フォルダパス格納領域
    $tgt_symphony_instance_no_array   = array();    // 処理対象のSymphonyインス>タンス番号のリストを格納
    $intNumPadding                    = 10;
    $apiRetryCount                    = 3;          //API失敗時にリトライする最大数

    //----------------------------------------------
    // REST API接続function定義 
    //----------------------------------------------
    require_once ($root_dir_path . $terraform_restapi_php);

    //----------------------------------------------
    // 業務処理開始
    //----------------------------------------------
    // トランザクションフラグ(初期値はfalse)
    $transaction_flag = false;
    try{
        //----------------------------------------------
        // 共通モジュールの呼び出し
        //----------------------------------------------
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require_once ($root_dir_path . $php_req_gate_php );
        require_once ($root_dir_path . $db_access_php);

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50001");
            require ($root_dir_path . $log_output_php );
        }


        //----------------------------------------------
        // DBコネクト
        //----------------------------------------------
        require ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // DBコネクト完了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);


        //----------------------------------------------
        // TERRAFORMインタフェース情報取得
        //----------------------------------------------
        //SQL作成
        $sql = "SELECT *
                FROM   $vg_info_table_name
                WHERE  DISUSE_FLAG = '0' ";

        //SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000100")) );
        }

        //SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000200")) );
        }

        //廃止フラグOFFの全レコード処理(FETCH
        while ( $row = $objQuery->resultFetch() ){
            $lv_terraform_if_info = $row;
        }
        //FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        //レコード無しの場合は「TERRAFORMインタフェース情報」が登録されていないので以降の処理をスキップ
        if( $num_of_rows === 0 ){
            // 警告フラグON
            $warning_flag = 1;
            // 例外処理へ：TERRAFORMインタフェース情報レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-111010") );
        }
        //「TERRAFORMインタフェース情報」が重複登録されている場合も以降の処理をスキップ
        else if( $num_of_rows > 1 ){
            // 異常フラグON
            $warning_flag = 1;
            // 例外処理へ：TERRAFORMインタフェース情報レコードが単一行でない
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-111020") );
        }

        // DBアクセス事後処理
        unset($objQuery);

        //----------------------------------------------
        // TERRAFORMインタフェース情報をローカル変数に格納
        //----------------------------------------------
        $lv_terraform_hostname                 = $lv_terraform_if_info['TERRAFORM_HOSTNAME'];
        $lv_terraform_token                    = ky_decrypt($lv_terraform_if_info['TERRAFORM_TOKEN']);

        //----------------------------------------------
        // トランザクション開始
        //----------------------------------------------
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000300")) );
        }

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // トランザクション開始(ステータスを「準備中」に変更する処理)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60001");
            require ($root_dir_path . $log_output_php );
        }

        //----------------------------------------------
        // 作業インスタンス情報 configのSQl生成
        //----------------------------------------------
        $arrayConfig_terraform = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "EXECUTION_NO"=>"",
            "EXECUTION_USER"=>"",
            "SYMPHONY_NAME"=>"",
            "STATUS_ID"=>"",
            "SYMPHONY_INSTANCE_NO"=>"",
            "PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_TIME_LIMIT"=>"",
            "I_TERRAFORM_RUN_ID"=>"",
            "I_TERRAFORM_WORKSPACE_ID"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "I_OPERATION_NO_IDBH"=>"",
            "CONDUCTOR_NAME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "FILE_INPUT"=>"",
            "FILE_RESULT"=>"",
            "RUN_MODE"=>"",
            "DISUSE_FLAG"=>"",
            "NOTE"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        //----------------------------------------------
        // 作業インスタンス情報のステータス条件設定(1:未実行)か(9:未実行(予約))
        //----------------------------------------------
        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND
                                      (
                                       ( TIME_BOOK IS NULL AND STATUS_ID = 1 ) OR
                                       ( STATUS_ID = 2 ) OR
                                       ( TIME_BOOK <= :KY_DB_DATETIME(6): AND STATUS_ID = 9 )
                                      )");

        //----------------------------------------------
        // 作業インスタンス情報 ValueのSQl生成
        //----------------------------------------------
        $arrayValue_terraform = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "EXECUTION_NO"=>"",
            "EXECUTION_USER"=>"",
            "SYMPHONY_NAME"=>"",
            "STATUS_ID"=>"",
            "SYMPHONY_INSTANCE_NO"=>"",
            "PATTERN_ID"=>"",
            "I_PATTERN_NAME"=>"",
            "I_TIME_LIMIT"=>"",
            "I_TERRAFORM_RUN_ID"=>"",
            "I_TERRAFORM_WORKSPACE_ID"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_OPERATION_NAME"=>"",
            "I_OPERATION_NO_IDBH"=>"",
            "CONDUCTOR_NAME"=>"",
            "CONDUCTOR_INSTANCE_NO"=>"",
            "TIME_BOOK"=>"DATETIME",
            "TIME_START"=>"DATETIME",
            "TIME_END"=>"DATETIME",
            "FILE_INPUT"=>"",
            "FILE_RESULT"=>"",
            "RUN_MODE"=>"",
            "DISUSE_FLAG"=>"",
            "NOTE"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        //----------------------------------------------
        // 作業インスタンス情報をSELECT
        //----------------------------------------------
        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             "EXECUTION_NO",
                                             $vg_exe_ins_msg_table_name,
                                             $vg_exe_ins_msg_table_jnl_name,
                                             $arrayConfig_terraform,
                                             $arrayValue_terraform,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000400")) );
        }

        //SQL実行
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000500")) );
        }

        //----------------------------------------------
        // 作業インスタンス情報を読込み
        //----------------------------------------------
        while ( $row = $objQueryUtn->resultFetch() ){
            //fetch行の情報をarrayに追加
            array_push( $tgt_execution_no_array, $row['EXECUTION_NO'] );
            array_push( $tgt_row_array, $row );         //作業インスタンス情報をレコードまるごと格納

            //symphonyインスタンス番号を退避
            $tgt_symphony_instance_no_array[$row['EXECUTION_NO']] = $row['SYMPHONY_INSTANCE_NO'];

            //ステータス＝準備中（２）チェック
            if( $row['STATUS_ID'] != 2 ){
                // fetch行の情報をarrayに追加
                array_push( $tgt_execution_no_array_without_2, $row['EXECUTION_NO'] );
                array_push( $tgt_row_array_without_2, $row );        //処理対象から準備中を除くレコードまるごと格納
            }
        }

        //----------------------------------------------
        // 作業インスタンス情報件数を取得
        //----------------------------------------------
        $num_of_tgt_execution_no = $objQueryUtn->effectedRowCount();   //処理対象のEXECUTION_NOの個数を格納

        // 処理対象レコードが0件の場合は処理終了へ
        if( $num_of_tgt_execution_no < 1 ){
            // トランザクション終了
            if( $objDBCA->transactionExit()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000600")) );
            }

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // トランザクション終了(ステータスを「準備中」に変更する処理)
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60002");
                require ($root_dir_path . $log_output_php );
            }

            // 例外処理へ(例外ではないが・・・) 処理対象レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-STD-50004") );
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコード検出(EXECUTION_NO:{$tgt_execution_no_array})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50005",implode(",", $tgt_execution_no_array ));
            require ($root_dir_path . $log_output_php );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // ステータス「準備中」へのUPDATEループ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50006");
            require ($root_dir_path . $log_output_php );
        }


        //----------------------------------------------
        // 処理対象から準備中ステータスを除いたEXECUTION_NOだけループ
        //----------------------------------------------
        foreach( $tgt_row_array_without_2 as $tgt_row ){
            //----------------------------------------------
            // 「C_TERRAFORM_EXE_INS_MNG」の処理対象レコードのステータスを準備中にUPDATE
            //----------------------------------------------
            $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000800")) );
            }
            $tgt_execution_no = $tgt_row["EXECUTION_NO"];

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            //----------------------------------------------
            $tgt_row["STATUS_ID"]        = 2;                           // 準備中ステータス(２)設定
            //----------------------------------------------
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 "EXECUTION_NO",
                                                 $vg_exe_ins_msg_table_name,
                                                 $vg_exe_ins_msg_table_jnl_name,
                                                 $arrayConfig_terraform,
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
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000900")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001000")) );
            }

            //SQL実行
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001100")) );
            }

            //SQL実行(JNL)
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001200")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]UPDATE実行(ステータス＝「準備中」)(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50007", $tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // ステータス「準備中」へのUPDATEループ終了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50008");
            require ($root_dir_path . $log_output_php );
        }

        //----------------------------------------------
        // コミット(レコードロックを解除)
        //----------------------------------------------
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001300")) );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // コミット(ステータスを「準備中」に変更する処理)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50009");
            require ($root_dir_path . $log_output_php );
        }

        //----------------------------------------------
        // トランザクション終了
        //----------------------------------------------
        $objDBCA->transactionExit();

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = false;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // トランザクション終了(ステータスを「準備中」に変更する処理)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60002");
            require ($root_dir_path . $log_output_php );
        }

        //----------------------------------------------
        // ローカル変数(ループ)宣言
        //----------------------------------------------
        $tgt_execution_row           = array();  // 単一行SELECTの結果を格納
        $lv_terraform_pattern_link   = array();  // 単一行SELECTの結果を格納
        $RequestContents             = array();  // REST API向けのリクエストコンテンツ(JSON)を格納

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコードの処理ループ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50010");
            require ($root_dir_path . $log_output_php );
        }


        //----------------------------------------------
        // 準備中に変更(＋最初から準備中)のEXECUTION_NOだけループ
        //----------------------------------------------
        foreach( $tgt_execution_no_array as $tgt_execution_no ){
            // ループ内で利用するローカル変数を初期化
            unset($tgt_execution_row);
            $tgt_execution_row = array();
            unset($lv_terraform_pattern_link);
            $lv_terraform_pattern_link = array();
            unset($RequestContents);
            $RequestContents = array();
            $operation_no = "";
            $pattern_id = "";
            $run_mode = "";
            $organization_id = ""; //組織ID
            $organization_name = ""; //組織名
            $workspace_id = ""; //ワークスペースID
            $workspace_name = ""; //ワークスペース名
            $tfe_workspace_id = ""; //TFE側で管理するワークスペースのID
            $tfe_auto_apply = false; //TFE側で管理するワークスペースでのApply方法（falseならApply前で作業を停止、trueならApplyを自動実行）
            $ary_vars_data = array(); //対象の変数を格納する配列
            $ary_module_matter_id = array(); //モジュール素材IDを格納する配列
            $ary_module_matter = array(); //モジュール素材情報を格納する配列
            $ary_policy_id = array(); //対象のPolicyIDを格納する配列
            $ary_policy_file = array(); //対象のpolicyファイルを格納する配列
            $ary_tfe_policy_id = array(); //ITA側で管理するPolicyIDとTFE側で管理するPolicyIDを紐づけるための配列
            $ary_policy_set_policy = array();//対象のPolicySetに紐づくPolicyIDを格納する配列
            $ary_policy_data = array(); //対象のPolicy情報を格納する配列
            $ary_policy_set_id = array(); //対象のWorkspaceに紐づくPolicySetIDを格納する配列
            $ary_policy_set_data = array(); //対象のPolicySet情報を格納する配列
            $intJournalSeqNo = null;
            $exist_flag = false;
            $vars_set_flag = false; //変数追加処理を行うかの判定
            $policy_set_add_flag = false; //PolicySet追加処理を行うかの判定
            $policy_add_flag = false; //Policy追加処理を行うかの判定


            //----------------------------------------------
            // logファイルを生成
            //----------------------------------------------
            $tgt_execution_no_str_pad = str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT );
            $data_type = "out";
            $log_path = $log_save_dir . "/" . $tgt_execution_no_str_pad . "/" . $data_type;
            $error_log = $log_path . "/error.log";
            $plan_log = $log_path . "/plan.log";
            $policyCheck_log = $log_path . "/policyCheck.log";
            $apply_log = $log_path . "/apply.log";

            //log格納ディレクトリを作成
            if(!file_exists($log_path)){
                if(!mkdir($log_path, 0777, true)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }else{
                    if(!chmod($log_path, 0777)){
                        // 警告フラグON
                        $warning_flag = 1;
                        // 例外処理へ
                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                    }
                }
            }

            //error_logファイルを作成
            if(!file_exists($error_log)){
                if(!touch($error_log)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }else{
                    if(!chmod($error_log, 0777)){
                        // 警告フラグON
                        $warning_flag = 1;
                        // 例外処理へ
                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                    }
                }
            }

            //plan_logファイルを作成
            if(!file_exists($plan_log)){
                if(!touch($plan_log)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }else{
                    if(!chmod($plan_log, 0777)){
                        // 警告フラグON
                        $warning_flag = 1;
                        // 例外処理へ
                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                    }
                }
            }

            //policyCheck_logファイルを作成
            if(!file_exists($policyCheck_log)){
                if(!touch($policyCheck_log)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }else{
                    if(!chmod($policyCheck_log, 0777)){
                        // 警告フラグON
                        $warning_flag = 1;
                        // 例外処理へ
                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                    }
                }
            }

            //apply_logファイルを作成
            if(!file_exists($apply_log)){
                if(!touch($apply_log)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }else{
                    if(!chmod($apply_log, 0777)){
                        // 警告フラグON
                        $warning_flag = 1;
                        // 例外処理へ
                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                    }
                }
            }

            //----------------------------------------------
            // トランザクション開始
            //----------------------------------------------
            if( $objDBCA->transactionStart()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001400")) );
            }

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = true;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]トランザクション開始(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60003",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            $temp_array = array('WHERE'=>" EXECUTION_NO = :EXECUTION_NO AND DISUSE_FLAG = '0' AND STATUS_ID = 2" );
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "SELECT FOR UPDATE",
                                                 "EXECUTION_NO",
                                                 $vg_exe_ins_msg_table_name, 
                                                 $vg_exe_ins_msg_table_jnl_name,
                                                 $arrayConfig_terraform,
                                                 $arrayValue_terraform,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $arrayUtnBind['EXECUTION_NO'] = $tgt_execution_no;

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001500")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001600")) );
            }

            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001700")) );
            }

            //----------------------------------------------
            // 作業インスタンス情報件数を取得
            //----------------------------------------------
            while ( $row = $objQueryUtn->resultFetch() ){
                $tgt_execution_row = $row;
            }
            // fetch行数を取得
            $fetch_counter = $objQueryUtn->effectedRowCount();

            // DBアクセス事後処理
            unset($objQueryUtn);

            // 処理対象レコードが特定できない場合は警告を出したうえで次レコードの処理へ
            if( $fetch_counter != 1 ){
                // 警告フラグON
                $warning_flag = 1;

                // 警告メッセージ出力
                // [警告]処理対象レコードをロックできず(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101040",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // ロールバック(念のため)
                if( $objDBCA->transactionRollBack()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001800")) );
                }

                // ロールバック(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101050",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                //----------------------------------------------
                // トランザクション終了
                //----------------------------------------------
                $objDBCA->transactionExit();

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;

                // トランザクション終了(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60006",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // 次レコードの処理へ
                continue;
            }

            //operation_noを定義
            $operation_no = $tgt_execution_row['OPERATION_NO_UAPK'];
            //pattern_idを定義
            $pattern_id = $tgt_execution_row['PATTERN_ID'];
            //RUN_MODEを定義
            $run_mode = $tgt_execution_row['RUN_MODE'];

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]レコードロック(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50012",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }


            //----------------------------------------------
            // PATTERN_IDからMovement一覧(E_TERRAFORM_PATTERN)のレコードを取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_pattern_view_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    PATTERN_ID = {$pattern_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001900")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002000")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter < 1){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002100")) );
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                //workspaceのIDを配列に格納
                $workspace_id = $row['TERRAFORM_WORKSPACE_ID'];

            }
            // DBアクセス事後処理
            unset($objQuery);

            //----------------------------------------------
            // PATTERN_IDからMovement詳細(B_TERRAFORM_PATTERN_LINK)のレコードを取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_pattern_link_table_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    PATTERN_ID = {$pattern_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002200")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002300")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter < 1){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-131010", $pattern_id);
                LocalLogPrint($error_log, $message);

                // 警告フラグON
                $warning_flag = 1;
                // 例外処理へ
                throw new Exception( $message );
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                //module素材のIDを配列に格納
                array_push($ary_module_matter_id, $row['MODULE_MATTER_ID']);
            }
            // DBアクセス事後処理
            unset($objQuery);


            //----------------------------------------------
            // WORKSPACE_IDから対象Workspace(B_TERRAFORM_WORKSPACES)のレコードを取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_workspaces_table_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    WORKSPACE_ID = {$workspace_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002400")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002500")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter < 1){
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002600")) );
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                //organizationIDを定義
                $organization_id = $row['ORGANIZATION_ID'];
                //workspaceNAMEを定義
                $workspace_name = $row['WORKSPACE_NAME'];
            }
            // DBアクセス事後処理
            unset($objQuery);


            //----------------------------------------------
            // ORGANIZATION_IDから対象Organization(B_TERRAFORM_ORGANIZATIONS)のレコードを取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_organization_table_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    ORGANIZATION_ID = {$organization_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002700")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002800")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter < 1){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00002900")) );
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                //organizationIDを定義
                $organization_name = $row['ORGANIZATION_NAME'];
            }
            // DBアクセス事後処理
            unset($objQuery);


            //----------------------------------------------
            // TFE側に対象のOrganizationが存在するかを判定
            //----------------------------------------------
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 200 && $count < $apiRetryCount){
                $apiResponse = get_organizations_list($lv_terraform_hostname, $lv_terraform_token);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 200){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }

            //API結果を判定
            if($statusCode == -2 || $statusCode == 401){
                //初回API実行時のみ、インターフェース情報のhostnameとUserTokenが適切かどうかをチェック。hostnameが不正な場合返り値は-2、UserTokenが不正な場合返り値は401となる。
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142010"); //[API Error]Terraform Enterpriseとの接続に失敗しました。インターフェース情報を確認して下さい。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142011",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }elseif($statusCode != 200){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141010"); //[API Error]Organization情報取得に失敗しました
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141011",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }else{
                $responsContents = $apiResponse['ResponsContents'];
                $exist_flag = false;
                foreach($responsContents['data'] as $data){
                    if($data['id'] == $organization_name){
                        $exist_flag = true;
                    }
                }

                if($exist_flag == false){
                    //error_logにメッセージを追記
                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-131020", $organization_name); //Terraform Enterpriseに対象のOrganizationが登録されていません。(Organization名:{})
                    LocalLogPrint($error_log, $message);

                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception( $message );
                }
            }

            //----------------------------------------------
            // TFE側に対象のWorkspaceが存在するかを判定
            //----------------------------------------------
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 200 && $count < $apiRetryCount){
                $apiResponse = get_workspaces_list($lv_terraform_hostname, $lv_terraform_token ,$organization_name);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 200){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定
            if($statusCode != 200){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141020"); //[API Error]Workspace情報取得に失敗しました
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141021",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }else{
                $responsContents = $apiResponse['ResponsContents'];
                $exist_flag = false;
                foreach($responsContents['data'] as $data){
                    if($data['attributes']['name'] == $workspace_name){
                        $exist_flag = true;
                        //tfe側で管理しているworkspaceのIDを格納
                        $tfe_workspace_id = $data['id'];
                        $tfe_auto_apply = $data['attributes']['auto-apply'];
                    }
                }

                if($exist_flag == false){
                    //error_logにメッセージを追記
                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-131030", $workspace_name); //Terraform Enterpriseに対象のWorkspaceが登録されていません。(WorkspaceName:{})
                    LocalLogPrint($error_log, $message);

                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception( $message );
                }

                //Plan確認の場合、WorkspaceのApplyMethod設定がAuto Applyになっている場合はエラーにする（Plan確認の場合にApplyが実行されてしまうため）
                if($run_mode == 2 && $tfe_auto_apply == true){
                    //error_logにメッセージを追記
                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141205", $workspace_name); //Terraform Enterpriseに対象のWorkspaceが登録されていません。(WorkspaceName:{})
                    LocalLogPrint($error_log, $message);

                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception( $message );
                }

            }


            //----------------------------------------------
            // 投入オペレーションの最終実施日を更新する。
            //----------------------------------------------
            require_once($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
            $dbaobj = new BackyardCommonDBAccessClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);
            $ret = $dbaobj->OperationList_LastExecuteTimestamp_Update($operation_no);
            if($ret === false) {
                $FREE_LOG = $dbaobj->GetLastErrorMsg();
                require ($log_output_php );
                throw new Exception("OperationList update error.");
            }
            unset($dbaobj);


            //----------------------------------------------
            // operation_noとpattern_idから変数名と代入値を取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_vars_data_view_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    OPERATION_NO_UAPK = {$operation_no} "
                  ."AND    PATTERN_ID = {$pattern_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003000")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003100")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter > 0){
                // 1件以上ある場合、レコードFETCH
                while ( $row = $objQuery->resultFetch() ){
                    //VARS_LINK_ID(key)/VARS_NAME/VARS_ENTRYを配列に格納
                    $vars_link_id = $row['MODULE_VARS_LINK_ID'];
                    $vars_name = $row['VARS_NAME'];
                    $vars_entry = $row['VARS_ENTRY'];
                    $sensitive_flag = $row['SENSITIVE_FLAG'];
                    $sensitive_boolean = false;
                    if($sensitive_flag == 1){
                        $sensitive_boolean = false; //1(OFF)ならfalse
                    }elseif($sensitive_flag == 2){
                        $sensitive_boolean = true; //2(ON)ならtrue
                        $vars_entry = ky_decrypt($vars_entry); //具体値をデコード
                    }
                    $ary_vars_data[$vars_link_id] = array('VARS_NAME' => $vars_name, 'VARS_ENTRY' => $vars_entry, 'SENSITIVE_FLAG' => $sensitive_boolean);
                }

                //変数追加処理のフラグをtrueにする
                $vars_set_flag = true;
            }

            // DBアクセス事後処理
            unset($objQuery);


            //--------------------------------------------------------------
            // 代入値設定状態に関係なく、Workspaceに紐づくすべての代入値(Variables)を削除する
            //--------------------------------------------------------------
            //TFEのVariables一覧を取得
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 200 && $count < $apiRetryCount){
                $apiResponse = get_workspace_var_list($lv_terraform_hostname, $lv_terraform_token ,$tfe_workspace_id);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 200){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定
            if($statusCode != 200){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141030"); //[API Error]Variables一覧取得に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141031",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }else{
                $workspaceVarListResponsContents = $apiResponse['ResponsContents'];
                foreach($workspaceVarListResponsContents['data'] as $data){
                    $var_id = $data['id'];
                    //TFEのVariableを削除
                    $statusCode = 0;
                    $count = 0;
                    while ($statusCode != 204 && $count < $apiRetryCount){
                        $apiResponse = delete_workspace_var($lv_terraform_hostname, $lv_terraform_token ,$tfe_workspace_id, $var_id);
                        $statusCode = $apiResponse['StatusCode'];
                        if($statusCode == 204){
                            //返却StatusCodeが正常なので終了
                            break;
                        }else{
                            //返却StatusCodeが異常なので、3秒間sleepして再度実行
                            sleep(3);
                            $count++;
                        }
                    }
                    //API結果を判定
                    if($statusCode != 204){
                        //error_logにメッセージを追記
                        $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141040"); //[API Error]Variablesの削除に失敗しました。
                        LocalLogPrint($error_log, $message);

                        // 異常フラグON
                        $error_flag = 1;
                        // 例外処理へ
                        $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141041",array(__FILE__,__LINE__,$statusCode));
                        throw new Exception( $backyard_log );
                    }
                }
            }


            //--------------------------------------------------------------
            // ログ取得時の文字化け防止用の環境変数登録を実行
            //--------------------------------------------------------------
            //Workspaceに対し環境変数を登録
            $statusCode = 0;
            $count = 0;
            $var_key = "TF_CLI_ARGS";
            $var_value = "-no-color";
            $category = "env"; //環境変数
            $sensitiveFlag = false;
            while ($statusCode != 201 && $count < $apiRetryCount){
                $apiResponse = create_workspace_var($lv_terraform_hostname, $lv_terraform_token, $tfe_workspace_id, $var_key, $var_value, $sensitiveFlag, $category);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 201){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定
            if($statusCode != 201){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141050"); //[API Error]Variablesの登録に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141051",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }

            //--------------------------------------------------------------
            // Movementに紐づく代入値がある場合、代入値(Variables)登録処理を実行
            //--------------------------------------------------------------
            if($vars_set_flag == true){
                foreach($ary_vars_data as $data){
                    $var_key = $data['VARS_NAME'];
                    $var_value = $data['VARS_ENTRY'];
                    $sensitiveFlag = $data['SENSITIVE_FLAG'];
                    $category = "terraform";
                    //key名から変数名の先頭文字を除外
                    $numVarHeadCount = mb_strlen(DF_HOST_VAR_HED, 'UTF-8');
                    $var_key = mb_substr($var_key, $numVarHeadCount);

                    //Workspaceに対し変数を登録
                    $statusCode = 0;
                    $count = 0;
                    while ($statusCode != 201 && $count < $apiRetryCount){
                        $apiResponse = create_workspace_var($lv_terraform_hostname, $lv_terraform_token, $tfe_workspace_id, $var_key, $var_value, $sensitiveFlag, $category);
                        $statusCode = $apiResponse['StatusCode'];
                        if($statusCode == 201){
                            //返却StatusCodeが正常なので終了
                            break;
                        }else{
                            //返却StatusCodeが異常なので、3秒間sleepして再度実行
                            sleep(3);
                            $count++;
                        }
                    }
                    //API結果を判定
                    if($statusCode != 201){
                        //error_logにメッセージを追記
                        $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141060"); //[API Error]Variablesの登録に失敗しました。
                        LocalLogPrint($error_log, $message);

                        // 異常フラグON
                        $error_flag = 1;
                        // 例外処理へ
                        $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141061",array(__FILE__,__LINE__,$statusCode));
                        throw new Exception( $backyard_log );
                    }
                }
            }


            //----------------------------------------------
            // WORKSPACE_IDからPolicySetとWorkspace紐付けテーブル(B_TERRAFORM_POLICYSET_WORKSPACE_LINK)のレコードを取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_policyset_workspace_link_table_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    WORKSPACE_ID = {$workspace_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003200")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003300")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter > 0){
                // 1件以上ある場合、レコードFETCH
                while ( $row = $objQuery->resultFetch() ){
                    //対象のPolicySetIDを配列に格納
                    array_push($ary_policy_set_id, $row['POLICY_SET_ID']);
                }

                //PolicySet追加処理のフラグをtrueにする
                $policy_set_add_flag = true;
            }

            // DBアクセス事後処理
            unset($objQuery);

            //--------------------------------------------------------------
            // 作業実行前に対象のWorkspaceからpolicySetを切り離す
            //--------------------------------------------------------------
            //TFEのPolicySet一覧を取得
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 200 && $count < $apiRetryCount){
                $apiResponse = get_policy_sets_list($lv_terraform_hostname, $lv_terraform_token ,$organization_name);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 200){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定
            if($statusCode != 200){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141110"); //[API Error]PolicySet情報取得に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141111",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }
            $policySetsListResponsContents = $apiResponse['ResponsContents'];

            //PolicySetに紐づくWorkspaceの中で、今回の対象Workspaceがあればすべて切り離す
            $tfe_policy_set_id = ""; //TFE側で管理しているPolicySetID
            $ary_registered_policy_set_workspace = array(); //TFE側に登録済みのPolicySetに紐づいているWorkspace
            foreach($policySetsListResponsContents['data'] as $data){
                $tfe_policy_set_id = $data['id'];

                foreach($data['relationships']['workspaces']['data'] as $workspace_data){
                    //紐づいているworkspaceが無い場合はbreak
                    if(empty($workspace_data)){
                        break;
                    }

                    //紐づいているworkspaceで今回の処理対象のworkspace(id)と一致するものがあれば切り離しを実行
                    if($workspace_data['id'] == $tfe_workspace_id){
                        $ary_registered_policy_set_workspace = array(
                            'data' => array(
                                array(
                                    "id" => $tfe_workspace_id,
                                    "type" => "workspaces"
                                ),
                            )
                        );

                        $statusCode = 0;
                        $count = 0;
                        while ($statusCode != 204 && $count < $apiRetryCount){
                            $apiResponse = delete_relationships_workspace($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_set_id, $ary_registered_policy_set_workspace);
                            $statusCode = $apiResponse['StatusCode'];
                            if($statusCode == 204){
                                //返却StatusCodeが正常なので終了
                                break;
                            }else{
                                //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                sleep(3);
                                $count++;
                            }
                        }
                        //API結果を判定
                        if($statusCode != 204){
                            //error_logにメッセージを追記
                            $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141120"); //[API Error]PolicySetからのWorkspace切り離し処理に失敗しました。
                            LocalLogPrint($error_log, $message);

                            // 異常フラグON
                            $error_flag = 1;
                            // 例外処理へ
                            $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141121",array(__FILE__,__LINE__,$statusCode));
                            throw new Exception( $backyard_log );
                        }
                    }
                }
            }

            //--------------------------------------------------------------
            // Workspaceに紐づくPolicySetがある場合、Policy登録処理を実施
            //--------------------------------------------------------------
            if($policy_set_add_flag == true){
                //----------------------------------------------
                // PolicySetIDからPolicySetとPolicy紐付けテーブル(B_TERRAFORM_POLICYSET_POLICY_LINK)のレコードを取得
                //----------------------------------------------
                $policy_set_id_implode = implode(",", $ary_policy_set_id);
                $sql = "SELECT * "
                      ."FROM   {$vg_terraform_policyset_policy_link_table_name} "
                      ."WHERE  DISUSE_FLAG = '0' "
                      ."AND    POLICY_SET_ID in ({$policy_set_id_implode}) ";
                // SQL準備
                $objQuery = $objDBCA->sqlPrepare($sql);
                if( $objQuery->getStatus()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003400")) );
                }

                // SQL発行
                $r = $objQuery->sqlExecute();
                if (!$r){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003500")) );
                }
                // fetch行数を取得
                $fetch_counter = $objQuery->effectedRowCount();
                if ($fetch_counter > 0){
                    // 1件以上ある場合、レコードFETCH
                    while ( $row = $objQuery->resultFetch() ){
                        //対象のPolicyIDを配列に格納
                        array_push($ary_policy_id, $row['POLICY_ID']);

                        //PolicySetとPolicyの紐付けを配列に格納
                        if(empty($ary_policy_set_policy)){
                            $ary_policy_set_policy[$row['POLICY_SET_ID']] = array();
                        }
                        array_push($ary_policy_set_policy[$row['POLICY_SET_ID']], $row['POLICY_ID']);
                    }

                    //対象PolicyIDの配列から重複を削除
                    $ary_policy_id = array_unique($ary_policy_id);

                    //Policy追加処理のフラグをtrueにする
                    $policy_add_flag = true;
                }

                // DBアクセス事後処理
                unset($objQuery);


                //--------------------------------------------------------------
                // PolicySetIDに紐づくPolicyIDがある場合、Policy登録に必要な情報を取得
                //--------------------------------------------------------------
                if($policy_add_flag == true){
                    //----------------------------------------------
                    // PolicySet管理テーブル(B_TERRAFORM_POLICY_SETS)のレコードを取得
                    //----------------------------------------------
                    $sql = "SELECT * "
                          ."FROM   {$vg_terraform_policy_set_table_name} "
                          ."WHERE  DISUSE_FLAG = '0' "
                          ."AND    POLICY_SET_ID in ({$policy_set_id_implode}) ";
                    // SQL準備
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    if( $objQuery->getStatus()===false ){
                        // 異常フラグON
                        $error_flag = 1;
                        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003600")) );
                    }

                    // SQL発行
                    $r = $objQuery->sqlExecute();
                    if (!$r){
                        // 異常フラグON
                        $error_flag = 1;
                        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003700")) );
                    }
                    // fetch行数を取得
                    $fetch_counter = $objQuery->effectedRowCount();
                    if ($fetch_counter < 1){
                        $error_flag = 1;
                        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003800")) );
                    }
                    // レコードFETCH
                    while ( $row = $objQuery->resultFetch() ){
                        //対象のPolicySetIDを配列に格納
                        $ary_policy_set_data[$row['POLICY_SET_ID']] = array(
                                                                    "policy_set_id" => $row['POLICY_SET_ID'],
                                                                    "policy_set_name" => $row['POLICY_SET_NAME'],
                                                                    "policy_set_note" => $row['NOTE']
                                                                 );
                    }

                    // DBアクセス事後処理
                    unset($objQuery);


                    //----------------------------------------------
                    // Policy管理テーブル(B_TERRAFORM_POLICY)のレコードを取得
                    //----------------------------------------------
                    $policy_id_implode = implode(",", $ary_policy_id);
                    $sql = "SELECT * "
                          ."FROM   {$vg_terraform_policy_table_name} "
                          ."WHERE  DISUSE_FLAG = '0' "
                          ."AND    POLICY_ID in ({$policy_id_implode}) ";
                    // SQL準備
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    if( $objQuery->getStatus()===false ){
                        // 異常フラグON
                        $error_flag = 1;
                        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00003900")) );
                    }

                    // SQL発行
                    $r = $objQuery->sqlExecute();
                    if (!$r){
                        // 異常フラグON
                        $error_flag = 1;
                        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004000")) );
                    }
                    // fetch行数を取得
                    $fetch_counter = $objQuery->effectedRowCount();
                    if ($fetch_counter < 1){
                        $error_flag = 1;
                        // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                        throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004100")) );
                    }
                    // レコードFETCH
                    while ( $row = $objQuery->resultFetch() ){
                        //対象のPolicySetIDを配列に格納
                        $ary_policy_data[$row['POLICY_ID']] = array(
                                                                    "policy_id" => $row['POLICY_ID'],
                                                                    "policy_name" => $row['POLICY_NAME'],
                                                                    "policy_matter_file" => $row['POLICY_MATTER_FILE'],
                                                                    "policy_note" => $row['NOTE']
                                                                 );
                    }

                    // DBアクセス事後処理
                    unset($objQuery);

                    //--------------------------------------------------------------
                    // TFE側のPolicyの登録状態を確認し、API処理を実行
                    //--------------------------------------------------------------
                    //TFEのPolicy一覧を取得
                    $statusCode = 0;
                    $count = 0;
                    while ($statusCode != 200 && $count < $apiRetryCount){
                        $apiResponse = get_policy_list($lv_terraform_hostname, $lv_terraform_token ,$organization_name);
                        $statusCode = $apiResponse['StatusCode'];
                        if($statusCode == 200){
                            //返却StatusCodeが正常なので終了
                            break;
                        }else{
                            //返却StatusCodeが異常なので、3秒間sleepして再度実行
                            sleep(3);
                            $count++;
                        }
                    }
                    //API結果を判定
                    if($statusCode != 200){
                        //error_logにメッセージを追記
                        $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141070"); //[API Error]Policy情報取得に失敗しました。
                        LocalLogPrint($error_log, $message);

                        // 異常フラグON
                        $error_flag = 1;
                        // 例外処理へ
                        $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141071",array(__FILE__,__LINE__,$statusCode));
                        throw new Exception( $backyard_log );
                    }else{
                        //----------------------------------------------
                        // TFEのPolicySet一覧と適用するPolicyを照らし合わせて登録・更新を実行
                        //----------------------------------------------
                        $policyListResponsContents = $apiResponse['ResponsContents'];
                        $trg_policy_matter_path = ""; //アップロードするPolicyコードの格納先
                        foreach($ary_policy_data as $ita_data){
                            $exist_flag = false;
                            $tfe_policy_id = ""; //TFE側で管理しているPolicySetID
                            $trg_policy_matter_path = $vg_terraform_policy_contents_dir . "/" . str_pad($ita_data['policy_id'], $intNumPadding, "0", STR_PAD_LEFT ) . "/" .$ita_data['policy_matter_file'];

                            //policyファイルのpathを配列に格納
                            array_push($ary_policy_file, $trg_policy_matter_path);

                            //PolicyがTFE側に登録済みかどうかをチェック
                            foreach($policyListResponsContents['data'] as $tfe_data){
                                if($tfe_data['attributes']['name'] == $ita_data['policy_name']){
                                    //TFEで管理しているPolicySetのIDを取得
                                    $tfe_policy_id = $tfe_data['id'];

                                    //登録済みフラグをたてる
                                    $exist_flag = true;
                                }
                            }
                            if($exist_flag == true){
                                //Policyが既にTFEに登録されている場合、更新APIを実行する
                                $statusCode = 0;
                                $count = 0;
                                while ($statusCode != 200 && $count < $apiRetryCount){
                                    $apiResponse = update_policy($lv_terraform_hostname, $lv_terraform_token ,$tfe_policy_id, $ita_data['policy_name'], $ita_data['policy_matter_file'], $ita_data['policy_note']);
                                    $statusCode = $apiResponse['StatusCode'];
                                    if($statusCode == 200){
                                        //返却StatusCodeが正常なので終了
                                        break;
                                    }else{
                                        //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                        sleep(3);
                                        $count++;
                                    }
                                }
                                //API結果を判定
                                if($statusCode != 200){
                                    //error_logにメッセージを追記
                                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141080"); //[API Error]Policyの更新に失敗しました。
                                    LocalLogPrint($error_log, $message);

                                    // 異常フラグON
                                    $error_flag = 1;
                                    // 例外処理へ
                                    $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141081",array(__FILE__,__LINE__,$statusCode));
                                    throw new Exception( $backyard_log );
                                }
                                $updatePolicyResponsContents = $apiResponse['ResponsContents'];
                                //API返却結果からTFE用のPolicyIDを取得
                                $tfe_policy_id = $updatePolicyResponsContents['data']['id'];
                                $ary_tfe_policy_id[$ita_data['policy_id']] = $tfe_policy_id;

                                //更新したPolicyにPolicyコードを適用する
                                $statusCode = 0;
                                $count = 0;
                                while ($statusCode != 200 && $count < $apiRetryCount){
                                    $apiResponse = policy_file_upload($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_id, $trg_policy_matter_path);
                                    $statusCode = $apiResponse['StatusCode'];
                                    if($statusCode == 200){
                                        //返却StatusCodeが正常なので終了
                                        break;
                                    }else{
                                        //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                        sleep(3);
                                        $count++;
                                    }
                                }
                                //API結果を判定
                                if($statusCode != 200){
                                    //error_logにメッセージを追記
                                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141100"); //[API Error]Policyコードの適用に失敗しました。
                                    LocalLogPrint($error_log, $message);

                                    // 異常フラグON
                                    $error_flag = 1;
                                    // 例外処理へ
                                    $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141101",array(__FILE__,__LINE__,$statusCode));
                                    throw new Exception( $backyard_log );
                                }

                            }else{
                                //Policyが登録されていない場合、登録APIを実行する
                                $statusCode = 0;
                                $count = 0;
                                while ($statusCode != 201 && $count < $apiRetryCount){
                                    $apiResponse = create_policy($lv_terraform_hostname, $lv_terraform_token ,$organization_name, $ita_data['policy_name'], $ita_data['policy_matter_file'], $ita_data['policy_note']);
                                    $statusCode = $apiResponse['StatusCode'];
                                    if($statusCode == 201){
                                        //返却StatusCodeが正常なので終了
                                        break;
                                    }else{
                                        //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                        sleep(3);
                                        $count++;
                                    }
                                }
                                //API結果を判定
                                if($statusCode != 201){
                                    //error_logにメッセージを追記
                                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141090"); //[API Error]Policyの登録に失敗しました。
                                    LocalLogPrint($error_log, $message);

                                    // 異常フラグON
                                    $error_flag = 1;
                                    // 例外処理へ
                                    $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141091",array(__FILE__,__LINE__,$statusCode));
                                    throw new Exception( $backyard_log );
                                }
                                $createPolicyResponsContents = $apiResponse['ResponsContents'];

                                //API返却結果からTFE用のPolicyIDを取得
                                $tfe_policy_id = $createPolicyResponsContents['data']['id'];
                                $ary_tfe_policy_id[$ita_data['policy_id']] = $tfe_policy_id;

                                //登録したPolicyにPolicyコードを適用する
                                $statusCode = 0;
                                $count = 0;
                                while ($statusCode != 200 && $count < $apiRetryCount){
                                    $apiResponse = policy_file_upload($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_id, $trg_policy_matter_path);
                                    $statusCode = $apiResponse['StatusCode'];
                                    if($statusCode == 200){
                                        //返却StatusCodeが正常なので終了
                                        break;
                                    }else{
                                        //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                        sleep(3);
                                        $count++;
                                    }
                                }
                                //API結果を判定
                                if($statusCode != 200){
                                    //error_logにメッセージを追記
                                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141100"); //[API Error]Policyコードの適用に失敗しました。
                                    LocalLogPrint($error_log, $message);

                                    // 異常フラグON
                                    $error_flag = 1;
                                    // 例外処理へ
                                    $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141101",array(__FILE__,__LINE__,$statusCode));
                                    throw new Exception( $backyard_log );
                                }
                            }
                        }
                    }

                    //----------------------------------------------
                    // TFEのPolicySet一覧と適用するPolicySetを照らし合わせて登録・更新を実行
                    //----------------------------------------------
                    foreach($ary_policy_set_data as $ita_data){
                        $exist_flag = false;
                        $tfe_policy_set_id = ""; //TFE側で管理しているPolicySetID
                        $ary_register_policy_set_workspace = array(); //PolicySetに紐づけるWorkspace
                        $ary_register_policy_set_policy = array(); //PolicySetに紐づけるPolicy
                        $ary_registered_policy_set_policy = array(); //TFE側に登録済みのPolicySetに紐づいているPolicy
                        //PolicySetがTFE側に登録済みかどうかをチェック
                        foreach($policySetsListResponsContents['data'] as $tfe_data){
                            if($tfe_data['attributes']['name'] == $ita_data['policy_set_name']){
                                //TFEで管理しているPolicySetのIDを取得
                                $tfe_policy_set_id = $tfe_data['id'];

                                //登録済みフラグをたてる
                                $exist_flag = true;
                            }
                        }
                        if($exist_flag == true){
                            //PolicySetが既にTFEに登録されている場合、更新APIを実行する
                            $statusCode = 0;
                            $count = 0;
                            while ($statusCode != 200 && $count < $apiRetryCount){
                                $apiResponse = update_policy_set($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_set_id, $ita_data['policy_set_name'], $ita_data['policy_set_note']);
                                $statusCode = $apiResponse['StatusCode'];
                                if($statusCode == 200){
                                    //返却StatusCodeが正常なので終了
                                    break;
                                }else{
                                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                    sleep(3);
                                    $count++;
                                }
                            }
                            //API結果を判定
                            if($statusCode != 200){
                                //error_logにメッセージを追記
                                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141130"); //[API Error]PolicySetの更新に失敗しました。
                                LocalLogPrint($error_log, $message);

                                // 異常フラグON
                                $error_flag = 1;
                                // 例外処理へ
                                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141131",array(__FILE__,__LINE__,$statusCode));
                                throw new Exception( $backyard_log );
                            }
                            $updatePolicySetResponsContents = $apiResponse['ResponsContents'];

                            //紐づいているpolicyをすべて切り離す
                            $ary_registered_policy_set_policy = $updatePolicySetResponsContents['data']['relationships']['policies'];
                            $statusCode = 0;
                            $count = 0;
                            while ($statusCode != 204 && $count < $apiRetryCount){
                                $apiResponse = delete_relationships_policy($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_set_id, $ary_registered_policy_set_policy);
                                $statusCode = $apiResponse['StatusCode'];
                                if($statusCode == 204){
                                    //返却StatusCodeが正常なので終了
                                    break;
                                }else{
                                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                    sleep(3);
                                    $count++;
                                }
                            }
                            //API結果を判定
                            if($statusCode != 204){
                                //error_logにメッセージを追記
                                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141140"); //[API Error]PolicySetからのPolicy切り離し処理に失敗しました。
                                LocalLogPrint($error_log, $message);

                                // 異常フラグON
                                $error_flag = 1;
                                // 例外処理へ
                                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141141",array(__FILE__,__LINE__,$statusCode));
                                throw new Exception( $backyard_log );
                            }

                        }else{
                            //PolicySetが登録されていない場合、登録APIを実行する
                            $statusCode = 0;
                            $count = 0;
                            while ($statusCode != 201 && $count < $apiRetryCount){
                                $apiResponse = create_policy_set($lv_terraform_hostname, $lv_terraform_token ,$organization_name, $ita_data['policy_set_name'], $ita_data['policy_set_note']);
                                $statusCode = $apiResponse['StatusCode'];
                                if($statusCode == 201){
                                    //返却StatusCodeが正常なので終了
                                    break;
                                }else{
                                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                    sleep(3);
                                    $count++;
                                }
                            }
                            //API結果を判定
                            if($statusCode != 201){
                                //error_logにメッセージを追記
                                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141150"); //[API Error]PolicySetの登録に失敗しました。
                                LocalLogPrint($error_log, $message);

                                // 異常フラグON
                                $error_flag = 1;
                                // 例外処理へ
                                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141151",array(__FILE__,__LINE__,$statusCode));
                                throw new Exception( $backyard_log );
                            }
                            $createPolicySetResponsContents = $apiResponse['ResponsContents'];

                            //登録したPolicySetの(TFE側で管理する)IDを取得
                            $tfe_policy_set_id = $createPolicySetResponsContents['data']['id'];

                        }

                        //----------------------------------------------
                        // PolicySetとWorkspaceの紐付け処理
                        //----------------------------------------------
                        $ary_register_policy_set_workspace = array(
                            "data" => array(
                                array(
                                    "id" => $tfe_workspace_id,
                                    "type" => "workspaces"
                                ),
                            ),
                        );

                        $statusCode = 0;
                        $count = 0;
                        while ($statusCode != 204 && $count < $apiRetryCount){
                            $apiResponse = relationships_workspace($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_set_id, $ary_register_policy_set_workspace);
                            $statusCode = $apiResponse['StatusCode'];
                            if($statusCode == 204){
                                //返却StatusCodeが正常なので終了
                                break;
                            }else{
                                //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                sleep(3);
                                $count++;
                            }
                        }
                        //API結果を判定
                        if($statusCode != 204){
                            //error_logにメッセージを追記
                            $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141160"); //[API Error]PolicySetとWorkspaceの紐付けに失敗しました。
                            LocalLogPrint($error_log, $message);

                            // 異常フラグON
                            $error_flag = 1;
                            // 例外処理へ
                            $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141161",array(__FILE__,__LINE__,$statusCode));
                            throw new Exception( $backyard_log );
                        }

                        //----------------------------------------------
                        // PolicySetとPolicyの紐付け処理
                        //----------------------------------------------
                        //APIでPolicyを紐づけるためのContentsを作成
                        $ary_trg_policy_id = $ary_policy_set_policy[$ita_data['policy_set_id']];
                        $ary_register_policy_set_policy = array("data" => array());
                        foreach($ary_trg_policy_id as $trg_policy_id){
                            $add_tfe_policy_data = array(
                                "id" => $ary_tfe_policy_id[$trg_policy_id],
                                "type" => "policies"
                            );
                            array_push($ary_register_policy_set_policy['data'], $add_tfe_policy_data);
                        }

                        $statusCode = 0;
                        $count = 0;
                        while ($statusCode != 204 && $count < $apiRetryCount){
                            $apiResponse = relationships_policy($lv_terraform_hostname, $lv_terraform_token, $tfe_policy_set_id, $ary_register_policy_set_policy);
                            $statusCode = $apiResponse['StatusCode'];
                            if($statusCode == 204){
                                //返却StatusCodeが正常なので終了
                                break;
                            }else{
                                //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                sleep(3);
                                $count++;
                            }
                        }
                        //API結果を判定
                        if($statusCode != 204){
                            //error_logにメッセージを追記
                            $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141170"); //[API Error]PolicySetとPolicyの紐付けに失敗しました。
                            LocalLogPrint($error_log, $message);

                            // 異常フラグON
                            $error_flag = 1;
                            // 例外処理へ
                            $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141171",array(__FILE__,__LINE__,$statusCode));
                            throw new Exception( $backyard_log );
                        }
                    }
                }
            }


            //----------------------------------------------
            // Moduleのファイル名を取得
            //----------------------------------------------
            $module_matter_id_implode = implode(',', $ary_module_matter_id);
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_module_table_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    MODULE_MATTER_ID in ({$module_matter_id_implode}) ";

            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004200")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004300")) );
            }

            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter > 0){
                // 1件以上ある場合、レコードFETCH
                while ( $row = $objQuery->resultFetch() ){
                    $ary_module_matter[$row['MODULE_MATTER_ID']] = array(
                        'matter_name' => $row['MODULE_MATTER_NAME'],
                        'matter_file' => $row['MODULE_MATTER_FILE']
                    );
                }

            }else{
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-131040", $pattern_id); //Movementに紐づくModuleが存在しません(MovementID:{})
                LocalLogPrint($error_log, $message);

                // 警告フラグON
                $warning_flag = 1;
                // 例外処理へ
                throw new Exception( $message );
            }

            // DBアクセス事後処理
            unset($objQuery);


            //----------------------------------------------
            // tar.gzファイルを作成する(TFEアップロード用)
            //----------------------------------------------
            //一時利用ディレクトリの存在をチェックし、なければ作成
            if(!file_exists($tar_temp_save_dir)){
                if(!mkdir($tar_temp_save_dir, 0777, true)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }else{
                    if(!chmod($tar_temp_save_dir, 0777)){
                        // 警告フラグON
                        $warning_flag = 1;
                        // 例外処理へ
                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                    }
                }
            }


            //一時格納先ディレクトリ名を定義
            $tgt_execution_no_str_pad = str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT );
            $tgt_execution_dir = $tar_temp_save_dir . "/" . $tgt_execution_no_str_pad;

            //tar.gzファイル名を定義
            $tar_module_file = 'ModuleFile_' . $tgt_execution_no_str_pad . '.tar.gz';

            //tar.gzファイルの存在をチェックし、すでにある場合は削除
            if(file_exists($tar_temp_save_dir . "/" .$tar_module_file)){
                unlink($tar_temp_save_dir . "/" .$tar_module_file);
            }

            //作業実行Noのディレクトリの存在をチェックし、すでにある場合は削除
            if(file_exists($tgt_execution_dir)){
                exec("/bin/rm -rf " . $tgt_execution_dir);
            }

            //作業実行Noのディレクトリを作成
            if(!mkdir($tgt_execution_dir, 0777, true) ){
                // 警告フラグON
                $warning_flag = 1;
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
            }else{
                if(!chmod($tgt_execution_dir, 0777)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                }
            }

            //作業実行Noディレクトリに、対象のModuleファイルをコピー
            foreach($ary_module_matter as $matter_id => $matter){
                $tgt_matter_no_str_pad = str_pad( $matter_id, $intNumPadding, "0", STR_PAD_LEFT );
                $tgt_matter_file = $matter['matter_file'];
                $cp_cmd = sprintf("/bin/cp -rfp %s %s/.", $vg_terraform_module_contents_dir."/".$tgt_matter_no_str_pad."/".$tgt_matter_file, $tgt_execution_dir);
                system($cp_cmd);
            }

            //作業実行Noディレクトリに、対象のpolicyファイルをコピー
            foreach($ary_policy_file as $policy_file_path){
                $cp_cmd = sprintf("/bin/cp -rfp %s %s/.", $policy_file_path, $tgt_execution_dir);
                system($cp_cmd);
            }

            //tar.gzを作成
            $tar_module_file = 'ModuleFile_' . $tgt_execution_no_str_pad . '.tar.gz';
            $tar_cmd = "cd " . $tar_temp_save_dir . "; tar cvfz " . $tar_module_file . " -C " .$tgt_execution_no_str_pad . " .";
            shell_exec($tar_cmd);

            //tarファイルの存在を確認
            if( !file_exists($tar_temp_save_dir . "/" . $tar_module_file)){
                // 警告フラグON
                $warning_flag = 1;
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121030",array($tgt_execution_no, __FILE__ , __LINE__)));
            }

            //----------------------------------------------
            // tar.gzファイルをTFEにアップロード（plan&applyを実行）
            //----------------------------------------------
            //アップロードURLおよびconfiguration-versionsIDを取得
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 201 && $count < $apiRetryCount){
                $apiResponse = get_upload_url($lv_terraform_hostname, $lv_terraform_token, $tfe_workspace_id);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 201){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定
            if($statusCode != 201){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141180"); //[API Error]ModuleファイルアップロードURLの取得に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141181",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }
            //アップロードURLとcv_idを取得
            $upload_url = $apiResponse['ResponsContents']['data']['attributes']['upload-url'];
            $cv_id = $apiResponse['ResponsContents']['data']['id'];

            //アップロードの実行
            $count = 0;
            while ($count < $apiRetryCount){
                $apiResponse = module_upload($lv_terraform_token, $upload_url, $tar_temp_save_dir."/".$tar_module_file);
                if($apiResponse == ""){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定（正常終了時、$apiResponseに空が返ってくる）
            if($apiResponse != ""){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141190"); //[API Error]Moduleファイルのアップロードに失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141191",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }

            //RUNの作成（planの実行）
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 201 && $count < $apiRetryCount){
                $apiResponse = create_run($lv_terraform_hostname, $lv_terraform_token, $tfe_workspace_id, $cv_id);
                $statusCode = $apiResponse['StatusCode'];
                if($statusCode == 201){
                    //返却StatusCodeが正常なので終了
                    break;
                }else{
                    //返却StatusCodeが異常なので、3秒間sleepして再度実行
                    sleep(3);
                    $count++;
                }
            }
            //API結果を判定
            if($statusCode != 201){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141200"); //RUNの作成に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141201",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }

            //RUN_IDを取得
            $run_id = $apiResponse['ResponsContents']['data']['id'];

            //----------------------------------------------
            // ZIPファイルを作成する(ITAダウンロード用)
            //----------------------------------------------
            //ZIPファイル名を定義
            $in_zip_file_name = 'InputData_' . $tgt_execution_no_str_pad . '.zip';

            //ZIPファイルを作成
            $zip_cmd = "cd " . $tar_temp_save_dir ."/" . $tgt_execution_no_str_pad. "; zip -r " . $tar_temp_save_dir . "/" .$in_zip_file_name . " .";
            shell_exec($zip_cmd);

            //----------------------------------------------
            // ZIPファイルを適切なディレクトリに移動
            //----------------------------------------------
            $in_utn_file_dir = $vg_exe_ins_input_file_dir . "/" . $tgt_execution_no_str_pad;
            if(!is_dir($in_utn_file_dir)){
                // ここ(UTNのdir)だけは再帰的に作成する
                if(!mkdir($in_utn_file_dir, 0777, true)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                }
                if(!chmod($in_utn_file_dir, 0777)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                }
            }
            rename($tar_temp_save_dir . "/" . $in_zip_file_name, $in_utn_file_dir . "/" . $in_zip_file_name);

            //zipファイルの存在を確認
            if(!file_exists($in_utn_file_dir . "/" . $in_zip_file_name)){
                // 警告フラグON
                $warning_flag = 1;
                // 例外処理へ
                throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121040",array($tgt_execution_no, __FILE__ , __LINE__)));
            }

            //----------------------------------------------
            // 一時利用ディレクトリとtar.gzファイルを削除
            //----------------------------------------------
            exec("/bin/rm -rf " . $tgt_execution_dir);
            unlink($tar_temp_save_dir . "/" .$tar_module_file);

            //----------------------------------------------
            // 作業インスタンス情報をSELECT
            //----------------------------------------------
            $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND EXECUTION_NO = ${tgt_execution_no}");
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "SELECT FOR UPDATE",
                                                 "EXECUTION_NO",
                                                 $vg_exe_ins_msg_table_name,
                                                 $vg_exe_ins_msg_table_jnl_name,
                                                 $arrayConfig_terraform,
                                                 $arrayValue_terraform,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004400")) );
            }

            //SQL実行
            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004500")) );
            }

            //作業インスタンス情報を読込み
            while ( $row = $objQueryUtn->resultFetch() ){
                $tgt_row = $row;
            }

            //----------------------------------------------
            // シーケンスをロック
            //----------------------------------------------
            $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004600")) );
            }

            //----------------------------------------------
            // 「C_TERRAFORM_EXE_INS_MNG」の処理対象レコードのステータスを実行中にUPDATE
            //----------------------------------------------
            // 履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004700")) );
            }

            $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
            //----------------------------------------------
            $tgt_row["STATUS_ID"]            = 3;  //実行中ステータス(3)設定
            //----------------------------------------------
            $tgt_row["TIME_START"]           = "DATETIMEAUTO(6)";
            //----------------------------------------------
            $tgt_row["I_TERRAFORM_RUN_ID"]   = $run_id; //TFE側で管理するRUN_IDを設定
            //----------------------------------------------
            $tgt_row["FILE_INPUT"]           = $in_zip_file_name;
            //----------------------------------------------
            $tgt_row["LAST_UPDATE_USER"]     = $db_access_user_id;

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 "EXECUTION_NO",
                                                 $vg_exe_ins_msg_table_name,
                                                 $vg_exe_ins_msg_table_jnl_name,
                                                 $arrayConfig_terraform,
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
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004800")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00004900")) );
            }

            //SQL実行
            $rUtn = $objQueryUtn->sqlExecute();               // 対象レコードを書き込み
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00005000")) );
            }

            //SQL実行(JNL)
            $rJnl = $objQueryJnl->sqlExecute();               // 対象レコードを書き込み
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00005100")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]UPDATE実行(ステータス＝「実行中」)(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50013",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            //----------------------------------------------
            // コミット(レコードロックを解除)
            //----------------------------------------------
            $r = $objDBCA->transactionCommit();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00005200")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]コミット(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50014",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);


            //----------------------------------------------
            // トランザクション終了
            //----------------------------------------------
            $objDBCA->transactionExit();

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]トランザクション終了(作業No.:{$tgt_execution_no})
                $objMTS->getSomeMessage("ITATERRAFORM-STD-60004",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコードの処理ループ終了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50011");
            require ($root_dir_path . $log_output_php );
        }

    }
    catch (Exception $e){
        if( $log_level    === 'DEBUG' ||
            $error_flag   != 0        ||
            $warning_flag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }

        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);

        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                if( !empty( $tgt_execution_no ) ){
                    // ロールバック(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60009",$tgt_execution_no);
                }
                else{
                    // ロールバック
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60010");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    // ロールバックに失敗しました(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101060",$tgt_execution_no);
                }
                else{
                    // ロールバックに失敗しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101070");
                }
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                if( !empty( $tgt_execution_no ) ){
                    // トランザクション終了(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60006",$tgt_execution_no);
                }
                else{
                    // トランザクション終了
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60008");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    // トランザクションの終了時に異常が発生しました(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101020",$tgt_execution_no);
                }
                else{
                    // トランザクションの終了時に異常が発生しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101030");
                }
            }
            require ($root_dir_path . $log_output_php );
        }


        //----------------------------------------------
        // 「C_TERRAFORM_EXE_INS_MNG」の処理対象レコードの処理中にエラーがあった場合、ステータスを「想定外エラー」に設定
        //----------------------------------------------
        if(($error_flag != 0 || $warning_flag != 0) && isset($tgt_execution_no)){
            // ステータスを想定外エラーに設定出来ませんでした。(作業No.:{})
            $ErrorMsg = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101080", $tgt_execution_no);
            try{
                //----------------------------------------------
                // トランザクション開始
                //----------------------------------------------
                if( $objDBCA->transactionStart()===false ){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = true;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]トランザクション開始(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60003",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }

                //----------------------------------------------
                // 作業インスタンス情報をSELECT
                //----------------------------------------------
                $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND EXECUTION_NO = ${tgt_execution_no}");
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "SELECT FOR UPDATE",
                                                     "EXECUTION_NO",
                                                     $vg_exe_ins_msg_table_name,
                                                     $vg_exe_ins_msg_table_jnl_name,
                                                     $arrayConfig_terraform,
                                                     $arrayValue_terraform,
                                                     $temp_array );

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                //SQL実行
                $r = $objQueryUtn->sqlExecute();
                if (!$r){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                //作業インスタンス情報を読込み
                while ( $row = $objQueryUtn->resultFetch() ){
                    $tgt_row = $row;
                }

                $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                $tgt_row["JOURNAL_SEQ_NO"]       = $retArray[0];
                //----------------------------------------------
                $tgt_row["STATUS_ID"]            = 7; // 想定外エラーステータス(3)設定
                //----------------------------------------------
                $tgt_row["TIME_START"]           = "DATETIMEAUTO(6)";
                //----------------------------------------------
                $tgt_row["TIME_END"]             = "DATETIMEAUTO(6)";
                //----------------------------------------------
                $tgt_row["LAST_UPDATE_USER"]     = $db_access_user_id;

                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "UPDATE",
                                                     "EXECUTION_NO",
                                                     $vg_exe_ins_msg_table_name,
                                                     $vg_exe_ins_msg_table_jnl_name,
                                                     $arrayConfig_terraform,
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
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                    $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                //SQL実行
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                //SQL実行(JNL)
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]UPDATE実行(ステータス＝「想定外エラー」)(作業No.:{$tgt_row["EXECUTION_NO"]})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50015",$tgt_row["EXECUTION_NO"]);
                    require ($root_dir_path . $log_output_php );
                }

                //----------------------------------------------
                // コミット(レコードロックを解除)
                //----------------------------------------------
                $r = $objDBCA->transactionCommit();
                if (!$r){
                    // 例外処理へ
                    throw new Exception( $ErrorMsg );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]コミット(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50014",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }

                // DBアクセス事後処理
                if ( isset($objQuery)    ) unset($objQuery);
                if ( isset($objQueryUtn) ) unset($objQueryUtn);
                if ( isset($objQueryJnl) ) unset($objQueryJnl);


                //----------------------------------------------
                // トランザクション終了
                //----------------------------------------------
                $objDBCA->transactionExit();

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]トランザクション終了(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-60004",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }

            }catch(Exception $e){
                // メッセージ出力
                $FREE_LOG = $e->getMessage();
                require ($root_dir_path . $log_output_php );
            }

        }
    }


    //----------------------------------------------
    // 結果出力
    //----------------------------------------------
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(異常)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101090");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        // 常駐プロセスが死なないようにした
        exit(2);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(警告)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-101100");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(正常)
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-50002");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(0);
    }




    //----------------------------------------------
    // logファイルへの出力関数
    //----------------------------------------------
    function LocalLogPrint($log_file, $message){
        if(file_exists($log_file)){
            $filepointer=fopen($log_file, "a");
            flock($filepointer, LOCK_EX);
            fputs($filepointer, $message . "\n" );
            flock($filepointer, LOCK_UN);
            fclose($filepointer);
        }
    }



?>