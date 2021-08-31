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
    //      Terraform 作業インスタンス実行後の状態チェック    
    //      対象：ステータス「実行中」および「実行中(遅延)」
    //      処理：TFEに登録された作業の状態をチェックおよびログの取得をする。
    //          　Apply完了後、ステータスを「完了」とし、stateファイルを取得する。
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
    $db_access_user_id                = -101801; // Terraform状態確認プロシージャ
    $tgt_execution_no_array           = array();    // 処理対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_array_without_2 = array();    // 処理対象から準備中を除くEXECUTION_NOのリストを格納
    $tgt_exec_count_array             = array();    // 処理対象の並列実行数のリストを格納
    $tgt_exec_count_array_without_2   = array();    // 処理対象から準備中を除く並列実行数のリストを格納
    $tgt_operation_id_array           = array();    // 処理対象のOPERATION_NO_UAPKのリストを格納
    $tgt_row_array                    = array();    // 処理対象からステータス(3:実行中)か(4:実行中(遅延))のものを格納
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
    require_once ($root_dir_path . $terraform_restapi_php );

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
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70001");
            require ($root_dir_path . $log_output_php );
        }


        //----------------------------------------------
        // DBコネクト
        //----------------------------------------------
        require ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // DBコネクト完了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70003");
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
        $lv_terraform_hostname   = $lv_terraform_if_info['TERRAFORM_HOSTNAME'];
        $lv_terraform_token      = ky_decrypt($lv_terraform_if_info['TERRAFORM_TOKEN']);
        $proxy_setting            = array();
        $proxy_setting['address'] = $lv_terraform_if_info['TERRAFORM_PROXY_ADDRESS'];
        $proxy_setting['port']    = $lv_terraform_if_info['TERRAFORM_PROXY_PORT'];

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
            "I_TERRAFORM_ORGANIZATION_WORKSPACE"=>"",
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
            "ACCESS_AUTH"=>"",
            "DISUSE_FLAG"=>"",
            "NOTE"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        //----------------------------------------------
        // 作業インスタンス情報のステータス条件設定(3:実行中)か(4:実行中(遅延))
        //----------------------------------------------
        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND
                                      (
                                       ( STATUS_ID = 3 ) OR
                                       ( STATUS_ID = 4 )
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
            "I_TERRAFORM_ORGANIZATION_WORKSPACE"=>"",
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
            "ACCESS_AUTH"=>"",
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
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000300")) );
        }

        //SQL実行
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000400")) );
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
        }

        //----------------------------------------------
        // 作業インスタンス情報件数を取得
        //----------------------------------------------
        $num_of_tgt_execution_no = $objQueryUtn->effectedRowCount();   //処理対象のEXECUTION_NOの個数を格納

        // 処理対象レコードが0件の場合は処理終了へ
        if( $num_of_tgt_execution_no < 1 ){
            // 例外処理へ(例外ではないが・・・) 処理対象レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-STD-70004") );
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコード検出(EXECUTION_NO:{$tgt_execution_no_array})
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70005",implode(",", $tgt_execution_no_array ));
            require ($root_dir_path . $log_output_php );
        }

        //ログおよびstateファイル取得用のHTTPコンテキスト作成
        $Header = array( "Authorization: Bearer ". $lv_terraform_token,
                         "Content-Type: application/vnd.api+json");
        $HttpContext = array( "http" => array( "method"        => 'GET',
                                               "header"        => implode("\r\n", $Header),
                                               "ignore_errors" => true));
        //proxy設定
        if($proxy_setting['address'] != ""){
            $address = $proxy_setting['address'];
            if($proxy_setting['port'] != ""){
                $address = $address . ":" . $proxy_setting['port'];
            }
            $HttpContext['http']['proxy'] = $address;
            $HttpContext['http']['request_fulluri'] = true;
        }

        $HttpContext['ssl']['verify_peer']=false;
        $HttpContext['ssl']['verify_peer_name']=false;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコードの処理ループ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70006");
            require ($root_dir_path . $log_output_php );
        }


        //----------------------------------------------
        // ステータス「実行中」「実行中(遅延)」のレコードの数だけループ
        //----------------------------------------------
        foreach($tgt_row_array as $tgt_row){
            $tgt_execution_no = $tgt_row['EXECUTION_NO'];
            $organization_name = "";
            $workspace_name = "";
            $ary_tfe_plan_data = array();
            $ary_tfe_policy_checks_data = array();
            $ary_tfe_apply_data = array();
            $tfe_policy_id = "";
            $ary_tfe_policy_check_id = array();
            $tfe_apply_id = "";
            $status_id = "";
            $make_zip_flag = false;
            $status_update_flag = false;
            $time_limit_check_flag = false;
            $plan_complete_flag = false;
            $policy_check_start_flag = false;
            $policy_check_failed_flag = false;
            $error_log_flag = false;
            $in_zip_file_name = "";
            $error_msg = "";
            $plan_msg = "";
            $policyCheck_msg = "";
            $apply_msg = "";
            $is_confirmable = false;
            $is_discardable = false;

            //RUN_MODEを格納
            $run_mode = $tgt_row['RUN_MODE']; //1:通常 / 2:Plan確認
            //workspaceIDを格納
            $workspace_id = $tgt_row['I_TERRAFORM_WORKSPACE_ID'];
            //RUN_IDを格納
            $tfe_run_id = $tgt_row['I_TERRAFORM_RUN_ID'];

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
            // OrganizationNAMEとWorkspaceNAMEを取得
            //----------------------------------------------
            $sql = "SELECT * "
                  ."FROM   {$vg_terraform_organization_workspace_link_view_name} "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    WORKSPACE_ID = {$workspace_id} ";
            // SQL準備
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000500")) );
            }

            // SQL発行
            $r = $objQuery->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000600")) );
            }
            // fetch行数を取得
            $fetch_counter = $objQuery->effectedRowCount();
            if ($fetch_counter != 1){
                $error_flag = 1;
                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000700")) );
            }

            // レコードFETCH
            while ( $row = $objQuery->resultFetch() ){
                //organizationIDを定義
                $organization_name = $row['ORGANIZATION_NAME'];
                $workspace_name = $row['WORKSPACE_NAME'];
            }
            // DBアクセス事後処理
            unset($objQuery);

            //----------------------------------------------
            // RUN_IDから、plan/applyのIDを取得(API)
            //----------------------------------------------
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 200 && $count < $apiRetryCount){
                $apiResponse = get_run_data($lv_terraform_hostname, $lv_terraform_token, $tfe_run_id, $proxy_setting);
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
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142010"); //[API Error]Terraformとの接続に失敗しました。インターフェース情報を確認して下さい。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142011",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }elseif($statusCode != 200){
                //error_logにメッセージを追記
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141210"); //[API Error]RUNデータの取得に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141211",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }
            $responsContents = $apiResponse['ResponsContents'];

            //RUNのステータスを格納
            $tfe_run_status = $responsContents['data']['attributes']['status'];

            //planの実行IDを格納
            $tfe_plan_id = $responsContents['data']['relationships']['plan']['data']['id'];

            //applyの実行IDを格納
            $tfe_apply_id = $responsContents['data']['relationships']['apply']['data']['id'];

            //applyの実行/中止可能フラグを格納
            $is_confirmable = $responsContents['data']['attributes']['actions']['is-confirmable'];
            $is_discardable = $responsContents['data']['attributes']['actions']['is-discardable'];


            //----------------------------------------------
            // planの詳細データを取得(API)
            //----------------------------------------------
            $statusCode = 0;
            $count = 0;
            while ($statusCode != 200 && $count < $apiRetryCount){
                $apiResponse = get_plan_data($lv_terraform_hostname, $lv_terraform_token, $tfe_plan_id, $proxy_setting);
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
                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141220"); //[API Error]plan詳細情報の取得に失敗しました。
                LocalLogPrint($error_log, $message);

                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141221",array(__FILE__,__LINE__,$statusCode));
                throw new Exception( $backyard_log );
            }
            $responsContents = $apiResponse['ResponsContents'];

            //必要なデータを格納
            $tfe_plan_status = $responsContents['data']['attributes']['status'];
            $tfe_plan_log_read_url = $responsContents['data']['attributes']['log-read-url'];

            //----------------------------------------------
            // planの結果判定スタート
            //----------------------------------------------
            //planのlogファイルを追記
            $tfe_plan_log = file_get_contents($tfe_plan_log_read_url, false, stream_context_create($HttpContext));
            $plan_msg = $plan_msg . $tfe_plan_log . "\n";
            file_put_contents($plan_log, $plan_msg);

            //失敗
            if($tfe_plan_status == "errored"){
                $status_id = 6; //完了(異常)ステータス(6)設定
                $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                $status_update_flag = true; //ステータス更新フラグをtrue

            //成功
            }elseif($tfe_plan_status == "finished"){
                $policy_check_start_flag = true; //policy-checkのデータ取得開始フラグをtrue
                $plan_complete_flag = true; //plan完了フラグをtrue

            //緊急停止
            }elseif($tfe_run_status == "canceled" || $tfe_plan_status == "canceled"){
                $status_id = 8; //緊急停止ステータス(8)設定
                $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                $status_update_flag = true; //ステータス更新フラグをtrue

            //進行中
            }elseif($tfe_plan_status == "running"){
                $time_limit_check_flag = true; //plan実行中の場合、遅延タイマーチェックフラグをtrueにする

            //待機中(queued)
            }else{
                //処理を実行しない
            }

            //----------------------------------------------
            // policy-checkの結果判定スタート
            //----------------------------------------------
            if($plan_complete_flag == true || $policy_check_start_flag == true){
                //----------------------------------------------
                // RUN_IDから、policy-checkのデータを取得(API)
                //----------------------------------------------
                $statusCode = 0;
                $count = 0;
                while ($statusCode != 200 && $count < $apiRetryCount){
                    $apiResponse = get_run_policy_check_data($lv_terraform_hostname, $lv_terraform_token, $tfe_run_id, $proxy_setting);
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
                    $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141230"); //[API Error]policy-check詳細情報の取得に失敗しました。
                    LocalLogPrint($error_log, $message);

                    // 異常フラグON
                    $error_flag = 1;
                    // 例外処理へ
                    $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141231",array(__FILE__,__LINE__,$statusCode));
                    throw new Exception( $backyard_log );
                }
                $responsContents = $apiResponse['ResponsContents'];

                //policyCheckの有無を判定
                if(!empty($responsContents['data'])){
                    //対象policyCheckデータを取得
                    $policy_check_status = $responsContents['data'][0]['attributes']['status'];
                    $policyResult = $responsContents['data'][0]['attributes']['result'];

                    if(!empty($responsContents['data'][0]['links']['output'])){
                        $output = $responsContents['data'][0]['links']['output'];
                        $output_url = "https://".$lv_terraform_hostname.$output."/";

                        //----------------------------------------------
                        // policyCheckのlogファイル(exex)を追記
                        //----------------------------------------------
                        $tfe_policy_log = file_get_contents($output_url, false, stream_context_create($HttpContext));
                        $policyCheck_msg = $policyCheck_msg . $tfe_policy_log . "\n";
                        file_put_contents($policyCheck_log, $policyCheck_msg);

                        //----------------------------------------------
                        // policyCheckの緊急停止判定
                        //----------------------------------------------
                        if($tfe_run_status == 'canceled' && $policy_check_status == 'canceled'){
                            $status_id = 8; //緊急停止ステータス(8)設定
                            $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                            $status_update_flag = true; //ステータス更新フラグをtrue
                            $policy_check_failed_flag = true; //policy-check失敗フラグをtrue

                        }else{
                            //----------------------------------------------
                            // policyCheckの結果判定
                            //----------------------------------------------
                            //失敗
                            if($policyResult['result'] == false){
                                $status_id = 6; //完了(異常)ステータス(6)設定
                                $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                                $status_update_flag = true; //ステータス更新フラグをtrue
                                $policy_check_failed_flag = true; //policy-check失敗フラグをtrue

                            //成功
                            }else{
                                $policy_check_failed_flag = false; //policy-check失敗フラグをfalse(デフォルト)

                            }
                        }
                    }
                }
            }
 
            //----------------------------------------------
            //applyの結果判定スタート
            //----------------------------------------------
            if($plan_complete_flag == true && $policy_check_failed_flag == false){
                //----------------------------------------------
                //apply実行保留判定
                //----------------------------------------------
                //apply実行を実施していない場合
                if($is_confirmable == true && $is_discardable == true){
                    //RUN_MODEによりApplyの実行か破棄かを分岐
                    if($run_mode == 2){
                        //2:Plan確認の場合

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ){
                            // Plan確認のためApplyを実行せず終了します。(作業No.:{})
                            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80011", $tgt_execution_no);
                            require ($root_dir_path . $log_output_php );
                        }

                        //----------------------------------------------
                        // RUN_IDに対してApplyを中止
                        //----------------------------------------------
                        $statusCode = 0;
                        $count = 0;
                        while ($statusCode != 202 && $count < $apiRetryCount){
                            $apiResponse = apply_discard($lv_terraform_hostname, $lv_terraform_token, $tfe_run_id, $proxy_setting);
                            $statusCode = $apiResponse['StatusCode'];
                            if($statusCode == 202){
                                //返却StatusCodeが正常なので終了
                                break;
                            }else{
                                //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                sleep(3);
                                $count++;
                            }
                        }

                        //API結果を判定
                        if($statusCode != 202){
                            //error_logにメッセージを追記
                            $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141260"); //[API Error]policy-check詳細情報の取得に失敗しました。
                            LocalLogPrint($error_log, $message);

                        }else{
                            $responsContents = $apiResponse['ResponsContents'];

                            //ステータスIDをセット
                            $status_id = 5; //ステータス：完了
                            $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                            $status_update_flag = true; //ステータス更新フラグをtrue

                            //apply_logにメッセージを追記
                            $message = "Plan確認のため、Applyは実行されませんでした。";
                            LocalLogPrint($apply_log, $message);
                        }

                    }else{
                        //1:通常(2以外)の場合

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ){
                            // Applyを実行します。(作業No.:{})
                            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80012", $tgt_execution_no);
                            require ($root_dir_path . $log_output_php );
                        }

                        //----------------------------------------------
                        // RUN_IDに対してApplyを実行
                        //----------------------------------------------
                        $statusCode = 0;
                        $count = 0;
                        while ($statusCode != 202 && $count < $apiRetryCount){
                            $apiResponse = apply_execution($lv_terraform_hostname, $lv_terraform_token, $tfe_run_id, $proxy_setting);
                            $statusCode = $apiResponse['StatusCode'];
                            if($statusCode == 202){
                                //返却StatusCodeが正常なので終了
                                break;
                            }else{
                                //返却StatusCodeが異常なので、3秒間sleepして再度実行
                                sleep(3);
                                $count++;
                            }
                        }
                        //API結果を判定
                        if($statusCode != 202){
                            //error_logにメッセージを追記
                            $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141261"); //[API Error]policy-check詳細情報の取得に失敗しました。
                            LocalLogPrint($error_log, $message);
                        }
                        $responsContents = $apiResponse['ResponsContents'];

                    }

                //apply実行を実施している場合
                }else{
                    //apply実行を実施している場合
                    //----------------------------------------------
                    // applyの詳細データを取得(API)
                    //----------------------------------------------
                    $statusCode = 0;
                    $count = 0;
                    while ($statusCode != 200 && $count < $apiRetryCount){
                        $apiResponse = get_apply_data($lv_terraform_hostname, $lv_terraform_token, $tfe_apply_id, $proxy_setting);
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
                        $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141240"); //[API Error]apply詳細情報の取得に失敗しました。
                        LocalLogPrint($error_log, $message);

                        // 異常フラグON
                        $error_flag = 1;
                        // 例外処理へ
                        $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141241",array(__FILE__,__LINE__,$statusCode));
                        throw new Exception( $backyard_log );
                    }
                    $responsContents = $apiResponse['ResponsContents'];

                    //必要なデータを格納
                    $tfe_apply_status = $responsContents['data']['attributes']['status'];
                    $tfe_apply_log_read_url = $responsContents['data']['attributes']['log-read-url'];

                    //----------------------------------------------
                    // applyのlogファイルを追記
                    //----------------------------------------------
                    $tfe_apply_log = file_get_contents($tfe_apply_log_read_url, false, stream_context_create($HttpContext));
                    $apply_msg = $apply_msg . $tfe_apply_log . "\n";
                    file_put_contents($apply_log, $apply_msg);

                    //----------------------------------------------
                    // applyの結果判定
                    //----------------------------------------------
                    //失敗
                    if($tfe_apply_status == "errored"){
                        $status_id = 6; //完了(異常)ステータス(6)設定
                        $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                        $status_update_flag = true; //ステータス更新フラグをtrue

                    //成功
                    }elseif($tfe_apply_status == "finished"){
                        $status_id = 5; //完了ステータス(5)設定
                        $make_zip_flag = true; //ZIPファイル作成フラグをtrue
                        $status_update_flag = true; //ステータス更新フラグをtrue

                        //----------------------------------------------
                        // Stateファイルを取得し格納
                        //----------------------------------------------
                        //stateの一覧取得APIを実行(最新の10件)
                        $statusCode = 0;
                        $count = 0;
                        while ($statusCode != 200 && $count < $apiRetryCount){
                            $apiResponse = get_workspace_state_version($lv_terraform_hostname, $lv_terraform_token, $organization_name, $workspace_name, 10, $proxy_setting);
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
                            $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141250"); //[API Error]stateバージョン情報の取得に失敗しました。
                            LocalLogPrint($error_log, $message);

                            // 異常フラグON
                            $error_flag = 1;
                            // 例外処理へ
                            $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-141251",array(__FILE__,__LINE__,$statusCode));
                            throw new Exception( $backyard_log );
                        }
                        $responsContents = $apiResponse['ResponsContents'];

                        //取得したStateの一覧をループし、RUN-IDが一致する対象を取得
                        $target_run_id = "";
                        $state_download_url = "";
                        $state_file_content = "";
                        foreach($apiResponse['ResponsContents']['data'] as $data){
                            $target_run_id = $data['relationships']['run']['data']['id'];
                            if($tfe_run_id == $target_run_id){
                                $state_download_url = $data['attributes']['hosted-state-download-url'];
                                $state_id = $data['id'];
                                break;
                            }
                        }

                        //stateファイルの中身を取得
                        $state_file_content = file_get_contents($state_download_url, false, stream_context_create($HttpContext));

                        if($state_file_content != false){
                            //stateファイルを生成
                            $state_file_name = $state_id . ".tfstate";
                            $state_file = $log_path . "/" . $state_file_name;
                            if(!file_exists($state_file)){
                                if(!touch($state_file)){
                                    // 警告フラグON
                                    $warning_flag = 1;
                                    // 例外処理へ
                                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121010",array($tgt_execution_no, __FILE__ , __LINE__)));
                                }else{
                                    if(!chmod($state_file, 0777)){
                                        // 警告フラグON
                                        $warning_flag = 1;
                                        // 例外処理へ
                                        throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121020",array($tgt_execution_no, __FILE__ , __LINE__)));
                                    }
                                }
                            }

                            //ファイルに中身を追記
                            file_put_contents($state_file, ky_encrypt($state_file_content), FILE_APPEND);

                            // Conductorからの実行ならOutputsの出力ファイルを作成
                            //----------------------------------------------
                            // Conductorからの実行か判定
                            //----------------------------------------------
                            //SQL作成
                            $sql = "SELECT CONDUCTOR_INSTANCE_NO
                                    FROM   C_TERRAFORM_EXE_INS_MNG
                                    WHERE  DISUSE_FLAG = '0'
                                    AND    I_TERRAFORM_RUN_ID = :I_TERRAFORM_RUN_ID
                                    AND    CONDUCTOR_INSTANCE_NO IS NOT NULL";

                            //SQL準備
                            $objQuery = $objDBCA->sqlPrepare($sql);
                            if( $objQuery->getStatus()===false ){
                                // 異常フラグON
                                $error_flag = 1;
                                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000100")) );
                            }

                            $bindArray = array(
                                "I_TERRAFORM_RUN_ID" => $target_run_id
                            );

                            //SQL発行
                            $r = $objQuery->sqlBind($bindArray);
                            $r = $objQuery->sqlExecute();
                            if (!$r){
                                // 異常フラグON
                                $error_flag = 1;
                                // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                                throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000200")) );
                            }

                            //呼び出し元ConductorのインスタンスNoを取得
                            while ( $row = $objQuery->resultFetch() ){
                                $conductor_instance_no = $row["CONDUCTOR_INSTANCE_NO"];
                            }
                            //FETCH行数を取得
                            $num_of_rows = $objQuery->effectedRowCount();

                            // Conductorから呼び出されていた場合
                            if ($num_of_rows > 0) {

                                //取得したStateの一覧をループし、RUN-IDが一致する対象を取得
                                $target_run_id = "";
                                $state_download_url = "";
                                $state_file_content = "";
                                $cnt = 0;
                                $tgt_cnt = "";
                                foreach($responsContents['data'] as $data){
                                    $target_run_id = $data['relationships']['run']['data']['id'];
                                    if($tfe_run_id == $target_run_id){
                                        $state_download_url = $tfe_apply_log_read_url;
                                        $state_id = $data['id'];
                                        $tgt_cnt = $cnt;
                                        break;
                                    }
                                    $cnt = $cnt + 1;
                                }

                                if ($state_download_url == "") {
                                    //stateファイルの取得に失敗。(作業No:{} FILE:{} LINE:{})
                                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-121050",array($tgt_execution_no, __FILE__ , __LINE__));
                                    require ($root_dir_path . $log_output_php );
                                    $warning_flag = 1;
                                    LocalLogPrint($error_log, $FREE_LOG);
                                } else {
                                    // outpurtsの中身
                                    $outputs = $responsContents['data'][$tgt_cnt]["relationships"]["outputs"]["data"];
                                    // 出力予定の配列
                                    $outputs_data = array();
                                    // outputsの内容の有無を確認
                                    if (count($outputs) > 0) {
                                        foreach ($outputs as $output) {
                                            $state_version_output_id = $output["id"];
                                            // -------------------------------
                                            $statusCode = 0;
                                            $count = 0;
                                            while ($statusCode != 200 && $count < $apiRetryCount){
                                                $outputsApiResponse = get_outputs($lv_terraform_hostname, $lv_terraform_token, $state_version_output_id ,$proxy_setting);
                                                $statusCode = $outputsApiResponse['StatusCode'];
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
                                                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142012"); //[API Error]stateバージョン情報の取得に失敗しました。
                                                LocalLogPrint($error_log, $message);

                                                // 異常フラグON
                                                $error_flag = 1;
                                                // 例外処理へ
                                                $backyard_log = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142013",array(__FILE__,__LINE__,$statusCode));
                                                throw new Exception( $backyard_log );
                                            }
                                            $outputs_name = $outputsApiResponse["ResponsContents"]["data"]["attributes"]["name"];
                                            $outputs_value = $outputsApiResponse["ResponsContents"]["data"]["attributes"]["value"];
                                            $outputs_data[$outputs_name] = $outputs_value;
                                        }
                                        if (!empty($outputs_data)) {
                                            //----------------------------------------------
                                            // データリレイストレージパスの取得
                                            //----------------------------------------------
                                            //SQL作成
                                            $sql = "SELECT CONDUCTOR_STORAGE_PATH_ITA
                                                    FROM   C_CONDUCTOR_IF_INFO
                                                    WHERE  DISUSE_FLAG = '0'";

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

                                            //呼び出し元ConductorのインスタンスNoを取得
                                            while ( $row = $objQuery->resultFetch() ){
                                                $conductor_storage_path = $row["CONDUCTOR_STORAGE_PATH_ITA"];
                                            }
                                            // ConductorインスタンスNoを文字列化
                                            $str_conductor_instance_no = sprintf("%010d",$conductor_instance_no);
                                            // 実行Noを文字列化
                                            $str_tgt_execution_no = sprintf("%010d",$tgt_execution_no);
                                            // outputをjson化
                                            $json = json_encode($outputs_data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
                                            if(json_last_error() !== JSON_ERROR_NONE){
                                                // エラーメッセージをエラーログに出力
                                                $message = $objMTS->getSomeMessage("ITATERRAFORM-ERR-142014",array(json_last_error()));
                                                //error_logにメッセージを追記
                                                LocalLogPrint($error_log, $message);
                                            } else {
                                                file_put_contents("$conductor_storage_path/$str_conductor_instance_no/terraform_output_$str_tgt_execution_no.json", $json);
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            //stateファイルの取得に失敗。(作業No:{} FILE:{} LINE:{})
                            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-ERR-121050",array($tgt_execution_no, __FILE__ , __LINE__));
                            require ($root_dir_path . $log_output_php );
                            $warning_flag = 1;
                        }

                    //緊急停止
                    }elseif($tfe_run_status == 'canceled' || $tfe_apply_status == "canceled"){
                        $status_id = 8; //緊急停止ステータス(8)設定
                        $make_zip_flag = true;
                        $status_update_flag = true;


                    //到達不可(plan/policy-checkの結果、applyを実行しないと判断された場合)
                    }elseif($tfe_apply_status == "unreachable"){
                        $status_id = 5; //完了ステータス(5)設定
                        $make_zip_flag = true;
                        $status_update_flag = true;

                    //進行中
                    }elseif($tfe_apply_status == "running"){
                        $time_limit_check_flag = true; //apply実行中の場合、遅延タイマーチェックフラグをtrueにする

                    //待機中(pending)
                    }else{
                        //処理を実行しない
                    }
                }

            }

            //----------------------------------------------
            // 遅延タイマーチェックフラグがtrueかつ、ステータス更新フラグがfalseの場合
            //----------------------------------------------
            if($time_limit_check_flag == true && $status_update_flag == false){
                $time_limit = $tgt_row['I_TIME_LIMIT'];
                $current_status = $tgt_row['STATUS_ID'];
                //ステータスが「実行中」かつ遅延タイマーが設定されているかをチェック
                if($current_status == 3 && $time_limit != NULL){
                    // 開始時刻(「UNIXタイム.マイクロ秒」)を生成
                    $varTimeDotMirco = convFromStrDateToUnixtime($tgt_row['TIME_START'], true );
                    // 開始時刻(マイクロ秒)＋制限時間(分→秒)＝制限時刻(マイクロ秒)
                    $varTimeDotMirco_limit = $varTimeDotMirco + ($time_limit * 60); //単位（秒）
                    // 現在時刻(「UNIXタイム.マイクロ秒」)を生成
                    $varTimeDotNowStd = getMircotime(0);

                    // 限時刻と現在時刻を比較
                    if( $varTimeDotMirco_limit < $varTimeDotNowStd ){
                        // ステータスを「実行中(遅延)」とする
                        $status_id = 4; //実行中(遅延)(4)設定
                        $status_update_flag = true;

                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ){
                            // [処理]遅延を検出(作業No.:{$tgt_execution_no})
                            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70008",$tgt_execution_no);
                            require ($root_dir_path . $log_output_php );
                        }
                    }
                }
            }


            //----------------------------------------------
            // ZIPファイル作成フラグがtrueの場合
            //----------------------------------------------
            if($make_zip_flag == true){
                //----------------------------------------------
                // ZIPファイルを作成する(ITAダウンロード用)
                //----------------------------------------------
                //ZIPファイル名を定義
                $in_zip_file_name = 'ResultData_' . $tgt_execution_no_str_pad . '.zip';

                //ZIPファイルを作成
                $zip_cmd = "cd " . $log_path . "; zip -r " . "./" .$in_zip_file_name . " .";
                shell_exec($zip_cmd);

                //----------------------------------------------
                // ZIPファイルを適切なディレクトリに移動
                //----------------------------------------------
                $in_utn_file_dir = $vg_exe_ins_result_file_dir . "/" . $tgt_execution_no_str_pad;
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
                rename($log_path . "/" . $in_zip_file_name, $in_utn_file_dir . "/" . $in_zip_file_name);

                //zipファイルの存在を確認
                if(!file_exists($in_utn_file_dir . "/" . $in_zip_file_name)){
                    // 警告フラグON
                    $warning_flag = 1;
                    // 例外処理へ
                    throw new Exception($objMTS->getSomeMessage("ITATERRAFORM-ERR-121040",array($tgt_execution_no, __FILE__ , __LINE__)));
                }
            }

            //----------------------------------------------
            // ステータス更新フラグがtrueの場合
            //----------------------------------------------
            if($status_update_flag == true){
                //----------------------------------------------
                // トランザクション開始
                //----------------------------------------------
                if( $objDBCA->transactionStart()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000800")) );
                }

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = true;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // トランザクション開始(ステータスを変更する処理)(作業No.:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80001", $tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }


                //----------------------------------------------
                // シーケンスをロック
                //----------------------------------------------
                $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00000900")) );
                }

                //----------------------------------------------
                // 「C_TERRAFORM_EXE_INS_MNG」の処理対象レコードのステータスをUPDATE
                //----------------------------------------------
                // 履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001000")) );
                }

                //ステータスが「完了」「完了(以上)」の場合のみ終了日時を追加
                if($status_id == 5 || $status_id == 6){
                    $time_end = "DATETIMEAUTO(6)";
                }else{
                    $time_end = "";
                }

                $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
                //----------------------------------------------
                $tgt_row["STATUS_ID"]        = $status_id;
                //----------------------------------------------
                $tgt_row["TIME_END"]         = $time_end;
                //----------------------------------------------
                $tgt_row["FILE_RESULT"]      = $in_zip_file_name;
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
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001100")) );
                }

                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                    $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001200")) );
                }

                //SQL実行
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001300")) );
                }

                //SQL実行(JNL)
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001400")) );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]UPDATE実行(作業No.:{$tgt_row["EXECUTION_NO"]})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70009",$tgt_row["EXECUTION_NO"]);
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
                    throw new Exception( $objMTS->getSomeMessage("ITATERRAFORM-ERR-101010",array(__FILE__,__LINE__,"00001500")) );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // コミット(ステータスを変更する処理)
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70010");
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
                    // トランザクション終了(ステータスを変更する処理)(作業No.:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80002", $tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }

            }

        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコードの処理ループ終了
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70007");
            require ($root_dir_path . $log_output_php );
        }

    }catch(Exception $e){
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80009",$tgt_execution_no);
                }
                else{
                    // ロールバック
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80011");
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80006",$tgt_execution_no);
                }
                else{
                    // トランザクション終了
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80008");
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80003",$tgt_execution_no);
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70012",$tgt_row["EXECUTION_NO"]);
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70011",$tgt_execution_no);
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-80006",$tgt_execution_no);
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
            $FREE_LOG = $objMTS->getSomeMessage("ITATERRAFORM-STD-70002");
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