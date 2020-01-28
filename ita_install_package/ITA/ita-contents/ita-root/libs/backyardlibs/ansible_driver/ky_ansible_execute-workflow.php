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

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php                  = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php                = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                  = '/libs/commonlibs/common_db_connect.php';
    $ansible_restapi_php             = '/libs/commonlibs/common_ansible_restapi.php';
    $ansible_create_files_php        = '/libs/backyardlibs/ansible_driver/CreateAnsibleExecFiles.php';
    $ansible_table_define_php        = '/libs/backyardlibs/ansible_driver/AnsibleTableDefinition.php';
    // REST API Request URL
    $RequestURI                      = "/restapi/ansible_driver/construct.php";
    // AnsibleTower実行モジュール
    $AnsibleTowerExecute_php         = "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerExecute.php";

    // 対話ファイルに埋め込まれるリモートログインのパスワード用変数の名前
    $vg_dialog_passwd_var_name = "__loginpassword__";
    // 子playbookに埋め込まれるリモートログインのユーザー用変数の名前
    $vg_playbook_user_var_name = "__loginuser__";
    
    // DB更新時のユーザーID設定
    // Legacy-Role対応
    switch($vg_driver_id){
    case DF_LEGACY_DRIVER_ID:
        $db_access_user_id = -100004; // legacy作業実行プロシージャ
        break;
    case DF_LEGACY_ROLE_DRIVER_ID:
        $db_access_user_id = -100012; // legacy作業実行プロシージャ
        break;
    case DF_PIONEER_DRIVER_ID:
        $db_access_user_id = -100006; // Legacy-Role作業実行プロシージャ
        break;
    }
    
    $Method                          = 'POST';
    $rh_abort_file_name              = 'RHABORT';
    $intNumPadding                   = 10;
    
    $file_subdir_zip_input            = 'FILE_INPUT';
    
    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag                     = 0;          // 警告フラグ(1：警告発生)
    $error_flag                       = 0;          // 異常フラグ(1：異常発生)
    $tgt_execution_no_array           = array();    // 処理対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_array_without_2 = array();    // 処理対象から準備中を除くEXECUTION_NOのリストを格納

    //ドライランモード
    $tgt_run_mode_array               = array();    // 処理対象のドライランモードのリストを格納
    $tgt_run_mode_no_array_without_2  = array();    // 処理対象から準備中を除くドライランモードのリストを格納

    //並列実行数
    $tgt_exec_count_array             = array();    // 処理対象の並列実行数のリストを格納
    $tgt_exec_count_array_without_2   = array();    // 処理対象から準備中を除く並列実行数のリストを格納

    $tgt_operation_id_array           = array();    // 処理対象のOPERATION_NO_UAPKのリストを格納
    $tgt_row_array                    = array();    // 処理対象のレコードまるごと格納
    $tgt_row_array_without_2          = array();    // 処理対象から準備中を除くレコードまるごと格納
    $num_of_tgt_execution_no          = 0;          // 処理対象のEXECUTION_NOの個数を格納
    
    // Symphonyインスタンス番号
    $tgt_symphony_instance_no_array   = array();    // 処理対象のSymphonyインスタンス番号のリストを格納

    ////////////////////////////////
    // REST API接続function定義   //
    ////////////////////////////////
    require_once ($root_dir_path . $ansible_restapi_php );


    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    
    // トランザクションフラグ(初期値はfalse)
    $transaction_flag = false;
    
    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        require_once ($root_dir_path . $ansible_table_define_php);
        require_once ($root_dir_path . $AnsibleTowerExecute_php);

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////////////////////////////////////
        // ANSIBLEインタフェース情報を取得                            //
        ////////////////////////////////////////////////////////////////
        // SQL作成
        $sql = "SELECT *
                FROM   $vg_info_table_name
                WHERE  DISUSE_FLAG = '0' ";
        
        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;

            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(__FILE__,__LINE__,$objQuery->getLastError()));
            require ($root_dir_path . $log_output_php );
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000100")) );
        }
        
        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56200",array(__FILE__,__LINE__,$objQuery->getLastError()));
            require ($root_dir_path . $log_output_php );

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000200")) );
        }
        
        // レコードFETCH
        while ( $row = $objQuery->resultFetch() ){
            $lv_ans_if_info = $row;
        }
        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        // レコード無しの場合は「ANSIBLEインタフェース情報」が登録されていないので以降の処理をスキップ
        // 常駐は継続させたいので異常フラグは立てない。
        if( $num_of_rows === 0 ){
            // 警告フラグON
            $warning_flag = 1;
            
            // 例外処理へ：'ANSIBLEインタフェース情報レコード無し'
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56000") );
        }
        // 「ANSIBLEインタフェース情報」が重複登録されている場合も以降の処理をスキップ
        // 常駐は継続させたいので異常フラグは立てない。
        else if( $num_of_rows > 1 ){
            // 異常フラグON
            $warning_flag = 1;
            
            // 例外処理へ：'ANSIBLEインタフェース情報レコードが単一行でない'
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56001") );
        }
        
        // DBアクセス事後処理
        unset($objQuery);
        
        // ANSIBLEインタフェース情報をローカル変数に格納
        $lv_ans_storage_path_lnx  = $lv_ans_if_info['ANSIBLE_STORAGE_PATH_LNX'];
        $lv_ans_storage_path_ans  = $lv_ans_if_info['ANSIBLE_STORAGE_PATH_ANS'];
        $lv_sym_storage_path_ans  = $lv_ans_if_info['SYMPHONY_STORAGE_PATH_ANS'];
        $lv_ans_protocol          = $lv_ans_if_info['ANSIBLE_PROTOCOL'];
        $lv_ans_hostname          = $lv_ans_if_info['ANSIBLE_HOSTNAME'];
        $lv_ans_port              = $lv_ans_if_info['ANSIBLE_PORT'];
        $lv_ans_access_key_id     = $lv_ans_if_info['ANSIBLE_ACCESS_KEY_ID'];
        $lv_ans_secret_access_key = ky_decrypt( $lv_ans_if_info['ANSIBLE_SECRET_ACCESS_KEY'] );

        // ansible-playbookコマンド実行時のオプションパラメータ取得
        $lv_ansible_exec_options  = $lv_ans_if_info['ANSIBLE_EXEC_OPTIONS'];

        $lv_anstwr_organization   = $lv_ans_if_info['ANSTWR_ORGANIZATION'];
        $lv_anstwr_auth_token     = $lv_ans_if_info['ANSTWR_AUTH_TOKEN'];
        $lv_ans_exec_user         = $lv_ans_if_info['ANSIBLE_EXEC_USER'];
        if(strlen(trim($lv_ans_exec_user)) == 0) {
            $lv_ans_exec_user = 'root';
        }
        
        ////////////////////////////////////////////////////////////////////////////////////////////////
        // 作業インスタンステーブルから処理対象レコードの一意キーを取得(レコードロック)
        ////////////////////////////////////////////////////////////////////////////////////////////////
        
        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000400")) );
        }
        
        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51001");
            require ($root_dir_path . $log_output_php );
        }

        $arrayConfig = array();
        CreateExecInstMngArray($arrayConfig);
        SetExecInstMngColumnType($arrayConfig);

        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND
                                      (
                                       ( TIME_BOOK IS NULL AND STATUS_ID = 1 ) OR
                                       ( STATUS_ID = 2 ) OR 
                                       ( TIME_BOOK <= :KY_DB_DATETIME(6): AND STATUS_ID = 9 )
                                      )");

        $arrayValue = array();
        CreateExecInstMngArray($arrayValue);

        $retArray = makeSQLForUtnTableUpdate($db_model_ch, 
                                             "SELECT FOR UPDATE", 
                                             "EXECUTION_NO", 
                                             $vg_exe_ins_msg_table_name, 
                                             $vg_exe_ins_msg_table_jnl_name, 
                                             $arrayConfig, 
                                             $arrayValue, 
                                             $temp_array );
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000500")) );
        }
        
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000600")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ){
            // fetch行の情報をarrayに追加
            array_push( $tgt_execution_no_array, $row['EXECUTION_NO'] );

            $tgt_run_mode_array[$row['EXECUTION_NO']] = $row['RUN_MODE'];

            if(strlen($row['I_ANS_PARALLEL_EXE']) == 0){
                $tgt_exec_count_array[$row['EXECUTION_NO']] = '0';
            }
            else{
                $tgt_exec_count_array[$row['EXECUTION_NO']] = $row['I_ANS_PARALLEL_EXE'];
            }

            // symphonyインスタンス番号を退避
            $tgt_symphony_instance_no_array[$row['EXECUTION_NO']] = $row['SYMPHONY_INSTANCE_NO'];
            
            array_push( $tgt_row_array, $row );
            
            if( $row['STATUS_ID'] != 2 ){
                // fetch行の情報をarrayに追加
                array_push( $tgt_execution_no_array_without_2, $row['EXECUTION_NO'] );

                // ドライランモード
                $tgt_run_mode_no_array_without_2[$row['EXECUTION_NO']] = $row['RUN_MODE'];

                // 並列実行数
                if(strlen($row['I_ANS_PARALLEL_EXE']) == 0){
                    $tgt_exec_count_array_without_2[$row['EXECUTION_NO']] = '0';
                }
                else{
                    $tgt_exec_count_array_without_2[$row['EXECUTION_NO']] = $row['I_ANS_PARALLEL_EXE'];
                }

                array_push( $tgt_row_array_without_2, $row );
            }
        }
        // fetch行数を取得
        $num_of_tgt_execution_no = $objQueryUtn->effectedRowCount();
        
        // 処理対象レコードが0件の場合は処理終了へ
        if( $num_of_tgt_execution_no < 1 ){
            // トランザクション終了
            if( $objDBCA->transactionExit()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000700")) );
            }
            
            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51002");
                require ($root_dir_path . $log_output_php );
            }
            
            // 例外処理へ(例外ではないが・・・)
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-STD-51003") );
        }
        
        // DBアクセス事後処理
        unset($objQueryUtn);
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51004",implode(",", $tgt_execution_no_array ));
            require ($root_dir_path . $log_output_php );
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51005");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////////////////////////////////////
        // シーケンスをロック                                         //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000800")) );
        }
        
        ////////////////////////////////////////////////////////////////
        // 処理対象から準備中ステータスを除いたEXECUTION_NOだけループ //
        ////////////////////////////////////////////////////////////////
        foreach( $tgt_row_array_without_2 as $tgt_row ){
            ////////////////////////////////////////////////////////////////////////////////////////////////
            // 「C_EXECUTION_MANAGEMENT」の処理対象レコードのステータスを準備中にUPDATE                   //
            ////////////////////////////////////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000900")) );
            }
            
            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["STATUS_ID"]        = 2;
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
            
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 "EXECUTION_NO",
                                                 $vg_exe_ins_msg_table_name,
                                                 $vg_exe_ins_msg_table_jnl_name,
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
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
            }
            
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
            }
            
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
            }
            
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51006",$tgt_row["EXECUTION_NO"]);
                require ($root_dir_path . $log_output_php );
            }
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51007");
            require ($root_dir_path . $log_output_php );
        }
        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51008");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();
        
        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = false;
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51009");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////
        // ローカル変数(ループ)宣言   //
        ////////////////////////////////
        $prepare_err_flag            = 0;        // 準備段階での異常フラグ(1：異常発生)
        $restapi_err_flag            = 0;        // REST APIでの異常フラグ(1：異常発生)
        $tgt_execution_row           = array();  // 単一行SELECTの結果を格納
        $RequestContents             = array();  // REST API向けのリクエストコンテンツ(JSON)を格納
        
        $zip_input_file              = "";
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51010");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////////////////////////////////////
        // 準備中に変更(＋最初から準備中)のEXECUTION_NOだけループ     //
        ////////////////////////////////////////////////////////////////
        foreach( $tgt_execution_no_array as $tgt_execution_no ){
            // ループ内で利用するローカル変数を初期化
            $prepare_err_flag = 0;
            $restapi_err_flag = 0;
            unset($tgt_execution_row);
            $tgt_execution_row = array();
            unset($RequestContents);
            $RequestContents = array();
            $intJournalSeqNo = null;
            $zip_input_file = "";
            
            ////////////////////////////////
            // トランザクション開始       //
            ////////////////////////////////
            if( $objDBCA->transactionStart()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
            }

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = true;
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51011",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            
            $temp_array = array('WHERE'=>" EXECUTION_NO = :EXECUTION_NO AND DISUSE_FLAG = '0' AND STATUS_ID = 2" );
            // 外部システムが「Websam Network Automation」(ITA_EXT_STM_ID = 2)に絞込み
            
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "SELECT FOR UPDATE",
                                                     "EXECUTION_NO",
                                                     $vg_exe_ins_msg_table_name, 
                                                     $vg_exe_ins_msg_table_jnl_name,
                                                     $arrayConfig,
                                                     $arrayValue,
                                                     $temp_array );
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $arrayUtnBind['EXECUTION_NO'] = $tgt_execution_no;
            
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            
            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
            }
            
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }
            
            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
            }
            
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
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51012",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
                
                // ロールバック(念のため)
                if( $objDBCA->transactionRollBack()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
                
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51013",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
                
                // トランザクション終了
                $objDBCA->transactionExit();

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;
                
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51014",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
                
                // 次レコードの処理へ
                continue;
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51015",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            //////////////////////////////////////////////////////////////////
            // 投入オペレーションの最終実施日を更新する。
            //////////////////////////////////////////////////////////////////
            require_once($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
            $dbaobj = new BackyardCommonDBAccessClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);
            $ret = $dbaobj->OperationList_LastExecuteTimestamp_Update($tgt_execution_row["OPERATION_NO_UAPK"]);
            if($ret === false) {
                $FREE_LOG = $dbaobj->GetLastErrorMsg();
                require ($root_dir_path . $log_output_php );
                throw new Exception("OperationList update error.");
            }
            unset($dbaobj);

            //////////////////////////////////////////////////////////////////
            // データベースからansibleで実行する情報取得
            //////////////////////////////////////////////////////////////////
            // クラス生成

            require_once ($root_dir_path . $ansible_create_files_php);

            $exec_mode             = $tgt_execution_row["EXEC_MODE"];
            $exec_playbook_hed_def = $tgt_execution_row["I_ANS_PLAYBOOK_HED_DEF"];
            $exec_option           = $tgt_execution_row["I_ANS_EXEC_OPTIONS"];
            switch($vg_driver_id){
            case DF_LEGACY_DRIVER_ID:
            case DF_LEGACY_ROLE_DRIVER_ID:
                $winrm_flg = $tgt_execution_row["I_ANS_WINRM_ID"];
                break;
            case DF_PIONEER_DRIVER_ID:
                $winrm_flg = "";
                break;
            }

            $ansdrv = new CreateAnsibleExecFiles($vg_driver_id,
                                                 $lv_ans_storage_path_lnx,
                                                 $lv_ans_storage_path_ans,
                                                 $lv_sym_storage_path_ans,
                                                 $vg_legacy_playbook_contents_dir,
                                                 $vg_pioneer_playbook_contents_dir,
            // ITA側で管理している legacy用 テンプレートファイル格納先ディレクトリ追加
                                                 $vg_template_contents_dir,
            // ITA側で管理している Pioneer用 テンプレートファイル格納先ディレクトリ追加
                                                 $vg_template_contents_dir,
            // ITA側で管理している copyファイル格納先ディレクトリ
                                                 $vg_copy_contents_dir,
                                                 $vg_ansible_vars_masterDB,
                                                 $vg_ansible_vars_assignDB,
                                                 $vg_ansible_pattern_vars_linkDB,
                                                 $vg_ansible_pho_linkDB,
                                                 $vg_ansible_master_fileDB,
                                                 $vg_ansible_master_file_pkeyITEM,
                                                 $vg_ansible_master_file_nameITEM,
                                                 // Legacy-Role対応
                                                 $vg_ansible_pattern_linkDB,
                                                 $vg_ansible_role_packageDB,
                                                 $vg_ansible_roleDB,
                                                 $vg_ansible_role_varsDB,
                                                 $objMTS,
                                                 $objDBCA);

            // Ansibleコマンド実行ユーザー設定
            $ansdrv->setAnsibleExecuteUser($lv_ans_exec_user);


            // データベースからansibleで実行する情報取得し実行ファイル作成
            $ret = CreateAnsibleExecFilesfunction($vg_driver_id,
                                                  $ansdrv,
                                                  $tgt_execution_no,
                                                  $tgt_symphony_instance_no_array[$tgt_execution_no],
                                                  $tgt_execution_row["PATTERN_ID"],
                                                  $tgt_execution_row["OPERATION_NO_UAPK"],
                                                  // ホストアドレス指定方式（I_ANS_HOST_DESIGNATE_TYPE_ID）追加 
                                                  // null or 1 がIP方式 2 がホスト名方式
                                                  $tgt_execution_row["I_ANS_HOST_DESIGNATE_TYPE_ID"],
                                                  // 対象ホストがwindowsかを判別する項目追加
                                                  // pioneerにはI_ANS_WINRM_IDがないので変数に変更
                                                  $winrm_flg,
                                                  $exec_mode,
                                                  $exec_playbook_hed_def,
                                                  $exec_option,
                                                  $vg_OrchestratorSubId_dir,
                                                  $root_dir_path,$log_output_php);

            if($ret !== true) {
                $prepare_err_flag = 1;
            }
            //getAnsible_in_Dir()
            //getAnsible_out_Dir()
            $tmp_array_dirs = $ansdrv->getAnsibleWorkingDirectories($vg_OrchestratorSubId_dir,$tgt_execution_no);
            $zip_data_source_dir = $tmp_array_dirs[3];

            $JobTemplatePropertyParameterAry  = array();
            $JobTemplatePropertyNameAry       = array();
            $ErrorMsgAry                      = array();

            if($prepare_err_flag == 0){
                if($lv_ans_if_info['ANSIBLE_EXEC_MODE'] == DF_EXEC_MODE_ANSIBLE) {
                    if(strlen(trim($lv_ans_access_key_id)) == 0) {
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000100");
                        $ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }
                    if(strlen(trim($lv_ans_secret_access_key)) == 0) {
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000101");
                        $ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }
                } else {
                    if(strlen(trim($lv_anstwr_auth_token)) == 0) {
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000102");
                        $ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }
                }
            }

            if($prepare_err_flag == 0){
                // ansible-playbookのオプションパラメータを確認
                getMovementAnsibleExecOption($tgt_execution_row["PATTERN_ID"],$MovementAnsibleExecOption);
                $OptionParameter = $lv_ansible_exec_options . ' ' . $MovementAnsibleExecOption;

                // Tower実行の場合にオプションパラメータをチェックする。
                if($lv_ans_if_info['ANSIBLE_EXEC_MODE'] != DF_EXEC_MODE_ANSIBLE) {

                    // Pioneerの場合の並列実行数のパラメータ設定 
                    switch($vg_driver_id){
                    case DF_PIONEER_DRIVER_ID:
                        if((strlen(trim($tgt_exec_count_array[$tgt_execution_no])) != 0) &&
                           (trim($tgt_exec_count_array[$tgt_execution_no]) != '0')) {
                            $OptionParameter .= sprintf(" -f %s ",$tgt_exec_count_array[$tgt_execution_no]); 
                        }
                        break;
                    }

                    $ret = getAnsiblePlaybookOptionParameter($OptionParameter,$JobTemplatePropertyParameterAry,$JobTemplatePropertyNameAry,$ErrorMsgAry);
                    if($ret === false)
                    {
                        $prepare_err_flag = 1;
                        foreach($ErrorMsgAry as $ErrorMsg) {
                            $ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        }
                    }
                }
            }
            if($prepare_err_flag == 0){
                // ansible-playbookコマンド実行時のオプションパラメータを共有ディレクトリのファイルに出力
                $fp=fopen($zip_data_source_dir . "/AnsibleExecOption.txt" , "w");
                fputs($fp, $OptionParameter);
                fclose($fp);
            }

            if($prepare_err_flag == 0){
                if( count( glob( $zip_data_source_dir . "/"."*" ) ) > 0 ){
                    //----ZIPファイルを作成する
                    $zip_input_file = 'InputData_' . str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT ) . '.zip';

                    $tmp_zip_file_name = $zip_input_file;
                    $tmp_subdir_name   = $file_subdir_zip_input;
                    $tmp_exe_ins_file_dir = $vg_exe_ins_input_file_dir;

                    // OSコマンドでzip圧縮する
                    $tmp_str_command = "cd " . $zip_data_source_dir . "; zip -r " . $zip_temp_save_dir . "/" . $tmp_zip_file_name . " .";
                    shell_exec( $tmp_str_command );

                    $strToFUC01FilePerRIValueCurDir = $tmp_exe_ins_file_dir . "/" . $tmp_subdir_name . "/" . str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT );

                    $tmp_utn_file_dir  = $strToFUC01FilePerRIValueCurDir;

                    // ZIPディレクトリ削除
                    system('/bin/rm -rf ' . $tmp_utn_file_dir . ' >/dev/null 2>&1');

                    if( !is_dir( $tmp_utn_file_dir ) ){
                        // ここ(UTNのdir)だけは再帰的に作成する
                        if( !mkdir( $tmp_utn_file_dir, 0777,true) ){
                            // 事前準備を中断
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-58051") );
                        }
                        if( !chmod( $tmp_utn_file_dir, 0777 ) ){
                            // 事前準備を中断
                            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-58052") );
                        }
                    }

                    // zipファイルを正式な置き場に移動
                    rename( $zip_temp_save_dir . "/" . $tmp_zip_file_name,
                            $tmp_utn_file_dir . "/" . $tmp_zip_file_name );

                    // zipファイルの存在を確認
                    if( !file_exists( $tmp_utn_file_dir . "/" . $tmp_zip_file_name ) ){
                        $prepare_err_flag = 1;
                        
                        // 警告メッセージ出力
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-58053",array($tgt_execution_no,$tmp_zip_file_name));
                        require ($root_dir_path . $log_output_php );
                    }

                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-58101",array($tgt_execution_no,$tmp_zip_file_name));
                        require ($root_dir_path . $log_output_php );
                    }
                    unset($tmp_str_command);
                    unset($tmp_zip_file_name);
                    unset($tmp_subdir_name);
                    unset($tmp_utn_file_dir);
                    unset($tmp_exe_ins_file_dir);
                    //ZIPファイルを作成する----
                }
            }
            
            // 準備で異常がなければREST APIをコール
            // 実行エンジンを判定
            if($lv_ans_if_info['ANSIBLE_EXEC_MODE'] == DF_EXEC_MODE_ANSIBLE) {
                if($prepare_err_flag == 0){
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51066",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                    ////////////////////////////////////////////////////////////////
                    // REST APIコール                                             //
                    ////////////////////////////////////////////////////////////////
                    $RequestContents 
                    = array(
                            // データリレイパス
                            'DATA_RELAY_STORAGE_TRUNK'=>$lv_ans_storage_path_ans, 
                            //オーケストレータ識別子
                            "ORCHESTRATOR_SUB_ID"=>$vg_OrchestratorSubId,
                            //作業実行ID
                            "EXE_NO"=>$tgt_execution_no,
                            "PARALLEL_EXE"=>$tgt_exec_count_array[$tgt_execution_no],
                            "RUN_MODE"=>$tgt_run_mode_array[$tgt_execution_no],
                            "EXEC_USER"=>$lv_ans_exec_user);
                        
                    $rest_api_response = ansible_restapi_access( $lv_ans_protocol,
                                                                 $lv_ans_hostname,
                                                                 $lv_ans_port,
                                                                 $lv_ans_access_key_id,
                                                                 $lv_ans_secret_access_key,
                                                                 $RequestURI,
                                                                 $Method,
                                                                 $RequestContents );


                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51067",array($tgt_execution_no,$rest_api_response['StatusCode']));
                        require ($root_dir_path . $log_output_php );
                    }

                
                    ////////////////////////////////////////////////////////////////
                    // 結果判定                                                   //
                    ////////////////////////////////////////////////////////////////
                    if( $rest_api_response['StatusCode'] != 200 ){
                        // REST APIでの異常フラグをON
                        $restapi_err_flag = 1;
                    
                        // 異常メッセージ
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51068",$tgt_execution_no);
                        $ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                        require ($root_dir_path . $log_output_php );
                        $FREE_LOG = print_r($rest_api_response,true);
                        require ($root_dir_path . $log_output_php );
                    }
                }

                ////////////////////////////////////////////////////////////////
                // シーケンスをロック                                         //
                ////////////////////////////////////////////////////////////////
                $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                        
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003300")) );
                }
                    
                // 履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                        
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003400")) );
                }
                $intJournalSeqNo = $retArray[0];

                ////////////////////////////////////////////////////////////////
                // 「C_EXECUTION_MANAGEMENT」をUPDATE                         //
                ////////////////////////////////////////////////////////////////
                // 正常(REST APIコールでHTTPレスポンスが200)の場合
                if( $prepare_err_flag == 0 &&
                    $restapi_err_flag == 0 ){
                    // クローン作製
                    $cln_execution_row = $tgt_execution_row;
                        
                    // 変数バインド準備
                    $cln_execution_row['JOURNAL_SEQ_NO']    = $retArray[0];
                    $cln_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
                    $cln_execution_row['STATUS_ID']         = "3";
                    $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                        
                    $cln_execution_row['FILE_INPUT']        = $zip_input_file;
                        
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51072",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                }
                // 異常 or 警告の場合
                else{
                    // 警告フラグON
                    $warning_flag = 1;
                        
                    // クローン作製
                    $cln_execution_row = $tgt_execution_row;
                        
                    // 変数バインド準備
                    $cln_execution_row['JOURNAL_SEQ_NO']    = $retArray[0];
                    $cln_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
                    $cln_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
                    $cln_execution_row['STATUS_ID']         = "7";
                    $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                        
                    $cln_execution_row['FILE_INPUT']        = $zip_input_file;
                        
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51074",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                }
            } else {
                if($prepare_err_flag == 0){
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51066",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                    ////////////////////////////////////////////////////////////////
                    // REST APIコール                                             //
                    ////////////////////////////////////////////////////////////////
                    $UIExecLogPath  = $ansdrv->getAnsible_out_Dir() . "/" . "exec.log";
                    $UIErrorLogPath = $ansdrv->getAnsible_out_Dir() . "/" . "error.log";
                    ////////////////////////////////////////////////////////////////
                    // AnsibleTowerから実行                                       //
                    ////////////////////////////////////////////////////////////////
                    // $Statusは未使用
                    $ret = AnsibleTowerExecution(DF_EXECUTION_FUNCTION,$lv_ans_if_info,$tgt_execution_row,$ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$Status,$JobTemplatePropertyParameterAry,$JobTemplatePropertyNameAry);
                }

                ////////////////////////////////////////////////////////////////
                // シーケンスをロック                                         //
                ////////////////////////////////////////////////////////////////
                $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                        
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003300")) );
                }
                    
                // 履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                        
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003400")) );
                }
                if($prepare_err_flag == 0){
                    $intJournalSeqNo = $retArray[0];

                    // クローン作製
                    $cln_execution_row = $tgt_execution_row;

                    $cln_execution_row['JOURNAL_SEQ_NO']    = $retArray[0];
                    $cln_execution_row['FILE_INPUT']        = $zip_input_file;
                }
                else {
                    // 警告フラグON
                    $warning_flag = 1;
                        
                    // クローン作製
                    $cln_execution_row = $tgt_execution_row;
                        
                    // 変数バインド準備
                    $cln_execution_row['JOURNAL_SEQ_NO']    = $retArray[0];
                    $cln_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
                    $cln_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
                    $cln_execution_row['STATUS_ID']         = "7";
                    $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                        
                    $cln_execution_row['FILE_INPUT']        = $zip_input_file;
                }
            }
            unset($ansdrv);

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51071",array($tgt_execution_no,$restapi_err_flag));
                require ($root_dir_path . $log_output_php );
            }
            
            
            $arrayConfig2 = array();
            CreateExecInstMngArray($arrayConfig2);
            SetExecInstMngColumnType($arrayConfig2);
            
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "UPDATE",
                                                 "EXECUTION_NO",
                                                 $vg_exe_ins_msg_table_name,
                                                 $vg_exe_ins_msg_table_jnl_name,
                                                 $arrayConfig2,
                                                 $cln_execution_row );
            
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
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003500")) );
            }
            
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003600")) );
            }
            
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003700")) );
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51075",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003800")) );
            }
                        
            ////////////////////////////////////////////////////////////////
            // コミット(レコードロックを解除)                             //
            ////////////////////////////////////////////////////////////////
            //$r = $objDBCA->transactionRollBack();
            $r = $objDBCA->transactionCommit();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00003900")) );
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51077",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);
            
            if( $zip_input_file != "" ){
                $tmp_utn_file_dir  = $strToFUC01FilePerRIValueCurDir;
                $tmp_zip_file_name = $zip_input_file;

                $tmp_jnl_file_dir_trunk = $tmp_utn_file_dir . "/old";
                $tmp_jnl_file_dir_focus = $tmp_jnl_file_dir_trunk . "/" . str_pad( $intJournalSeqNo, $intNumPadding, "0", STR_PAD_LEFT );

                // 履歴フォルダへコピー
                if( !mkdir( $tmp_jnl_file_dir_trunk, 0777 ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50050",array($tgt_execution_no, __FILE__ , __LINE__, "00001751"));
                    require ($root_dir_path . $log_output_php );
                }
                else{
                    if( !mkdir( $tmp_jnl_file_dir_focus, 0777 ) ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50050",array($tgt_execution_no, __FILE__ , __LINE__, "00001752"));
                        require ($root_dir_path . $log_output_php );
                    }
                    else{
                        $boolCopy = copy( $tmp_utn_file_dir . "/" . $tmp_zip_file_name, $tmp_jnl_file_dir_focus . "/". $tmp_zip_file_name);
                        if( $boolCopy === false ){
                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50050",array($tgt_execution_no, __FILE__ , __LINE__, "00001753"));
                            require ($root_dir_path . $log_output_php );
                        }
                        else{
                            // トレースメッセージ
                            if ( $log_level === 'DEBUG' ){
                                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50051",$tmp_zip_file_name);
                                require ($root_dir_path . $log_output_php );
                            }
                        }
                    }
                }
                unset($tmp_jnl_file_dir_focus);
                unset($tmp_jnl_file_dir_trunk);
                unset($tmp_utn_file_dir);
                unset($tmp_zip_file_name);
            }
            
            ////////////////////////////////
            // トランザクション終了       //
            ////////////////////////////////
            $objDBCA->transactionExit();

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51078",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51080");
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51081",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51082");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51083",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51084");
                }
            }
            require ($root_dir_path . $log_output_php );
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                if( !empty( $tgt_execution_no ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51085",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51086");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51087",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51089");
                }
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(2);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(0);
    }
    //////////////////////////////////////////////////////////////////
    // データベースからansibleで実行する情報取得
    //////////////////////////////////////////////////////////////////
    function CreateAnsibleExecFilesfunction($in_driver_id,
                                            $in_ansdrv,
                                            $in_execution_no,
                                            $symphony_instance_no,
                                            $in_pattern_id,
                                            $in_operation_id,
                                            //ホストアドレス方式追加
                                            $in_hostaddres_type,
                                            // 対象ホストがwindowsかを判別する項目追加
                                            $in_winrm_id,
                                            $in_exec_mode,
                                            $in_exec_playbook_hed_def,
                                            $in_exec_option,
                                            $in_OrchestratorSubId_dir,
                                            $in_root_dir_path,$in_log_output_php){
        global $objMTS;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        global $log_output_php;

        $hostlist         = array();
        $hostprotocollist = array();
        $hostostypelist   = array(); 
        $playbooklist     = array();
        $dialogfilelist   = array();
        $host_vars        = array();

        $host_child_vars      = array();
        $DB_child_vars_master = array();

        // Legacy-Role対応
        $rolenamelist     = array();
        $role_rolenamelist = array();
        $role_rolevarslist = array();
        $role_roleglobalvarslist = array();

        $MultiArray_vars_list = array();
        $All_vars_list = array();
        //機器一覧ホスト情報
        $hostinfolist      = array();

        $def_vars_list = array();
        $def_array_vars_list = array();

        $ret = $in_ansdrv->CreateAnsibleWorkingDir($in_OrchestratorSubId_dir,
                                                   $in_execution_no,
                                                   $in_hostaddres_type,
                                                   // 対象ホストがwindowsかを判別する項目追加
                                                   $in_winrm_id,
                                                   // Legacy-Role時のみ必要な項目
                                                   // ロールパッケージファイルディレクトリ
                                                   $root_dir_path . '/' . DF_ROLE_PACKAGE_FILE_CONTENTS_DIR,
                                                   // 作業パターンID
                                                   $in_pattern_id,
                                                   // ロール内 ロール名リスト返却
                                                   // [ロール名]
                                                   $role_rolenamelist,
                                                   // ロール内 変数リスト返却
                                                   // [ロール名][変数名]=0
                                                   $role_rolevarslist,
                                                   // ロール内 グローバル変数リスト返却
                                                   // [ロール名][グローバル変数名]=0
                                                   $role_roleglobalvarslist,
                                                   // ロールパッケージ管理 Pkey 返却
                                                   $role_rolepackage_id,
                                                   $def_vars_list,
                                                   $def_array_vars_list,
                                                   $symphony_instance_no
                                                   );
        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010004"));
            require ($in_root_dir_path . $in_log_output_php );
               
            return false;
        }
        ///////////////////////////////////////////////////////////////////////////////////////
        // データベースから処理対象ホストの情報を取得
        // $hostlist:              ホスト一覧返却配列
        //                         [管理システム項番]=[ホスト名(IP)]
        // $hostprotcollist:       ホスト毎プロトコル一覧返却配列
        //                         [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD  
        // $hostostypelist:        ホスト毎OS種別一覧返却配列
        //                         [ホスト名(IP)]=$row[OS種別] 
        // #1073 下記を追加--------------------------------------------------------------------
        // 既存のデータが重なるが、今後の開発はこの変数を使用する。
        // $hostinfolist:          機器一覧ホスト情報配列
        //                         [ホスト名(IP)]=HOSTNAME=>''             ホスト名
        //                                        PROTOCOL_ID=>''          接続プロトコル
        //                                        LOGIN_USER=>''           ログインユーザー名
        //                                        LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
        //                                                                 1:管理(●)   0:未管理
        //                                        LOGIN_PW=>''             パスワード
        //                                                                 パスワード管理が1の場合のみ有効
        //                                        LOGIN_AUTH_TYPE=>''      Ansible認証方式
        //                                                                 2:パスワード認証 1:鍵認証
        //                                        WINRM_PORT=>''           WinRM接続プロトコル
        //                                        OS_TYPE_ID=>''           OS種別
        //                                        SSH_EXTRA_ARGS=>         SSHコマンド 追加パラメータ
        //                                        SSH_KEY_FILE=>           SSH秘密鍵ファイル
        //                                        SYSTEM_ID=>              項番
        //                                        WINRM_SSL_CA_FILE=>      サーバー証明書ファイル
        //                                        HOSTS_EXTRA_ARGS=>       インベントリファイル 追加パラメータ
        //
        ///////////////////////////////////////////////////////////////////////////////////////
        $ret = $in_ansdrv->getDBHostList($in_pattern_id,
                                         $in_operation_id,
                                         $hostlist,
                                         $hostprotocollist,
                                         $hostostypelist,
                                         $hostinfolist);
        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010000"));
            require ($in_root_dir_path . $in_log_output_php);
               
            return false;
        }

        switch($in_driver_id){
        case DF_LEGACY_DRIVER_ID:
            /////////////////////////////////////////////////////////////////////////////
            // データベースからPlayBookファイルを取得
            //   $playbooklist:     子PlayBookファイル返却配列
            //                      [INCLUDE順序][素材管理Pkey]=>素材ファイル
            /////////////////////////////////////////////////////////////////////////////
            $ret = $in_ansdrv->getDBLegacyPlaybookList($in_pattern_id,
                                                       $playbooklist);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );
               
                return false;
            }
            break;
        case DF_PIONEER_DRIVER_ID:
            /////////////////////////////////////////////////////////////////////////////
            // データベースから対話ファイルを取得
            //   $dialogfilelist:   子PlayBookファイル返却配列
            //                      [ホスト名(IP)][INCLUDE順番][素材管理Pkey]=対話ファイル
            /////////////////////////////////////////////////////////////////////////////
            $ret = $in_ansdrv->getDBPioneerDialogFileList($in_pattern_id,
                                                          $in_operation_id,
                                                          $dialogfilelist,
                                                          $hostostypelist);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010002"));
                require ($in_root_dir_path . $in_log_output_php );
               
                return false;
            }
            break;
        case DF_LEGACY_ROLE_DRIVER_ID:
            /////////////////////////////////////////////////////////////////////////////
            // データベースからロール名を取得
            //   $rolenamelist:     ロール名返却配列
            //                      [実行順序][ロールID(Pkey)]=>ロール名
            /////////////////////////////////////////////////////////////////////////////
            $ret = $in_ansdrv->getDBLegactRoleList($in_pattern_id,$rolenamelist);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );
               
                return false;
            }
            break;
        }

        // Legacy-Role 多次元配列　恒久版対応
        switch($in_driver_id){
        case DF_LEGACY_DRIVER_ID:
        case DF_PIONEER_DRIVER_ID:
            /////////////////////////////////////////////////////////////////////////////
            // データベースから変数情報を取得する。
            //   $host_vars:        変数一覧返却配列
            //                      [ホスト名(IP)][ 変数名 ]=>具体値
            // #1081 2016/11/04 Append strat  
            //   $host_child_vars   配列変数一覧返却配列(変数一覧に配列変数含む)
            //                      [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
            //   $DB_child_vars_master: 
            //                      メンバー変数マスタの配列変数のメンバー変数リスト返却
            //                      [ 変数名 ][メンバー変数名]=0
            // #1081 2016/11/04 Append end
            /////////////////////////////////////////////////////////////////////////////
            $ret = $in_ansdrv->getDBVarList($in_pattern_id,
                                            $in_operation_id,
                                            $host_vars,
                                            $host_child_vars,
                                            $DB_child_vars_master);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010003"));
                require ($in_root_dir_path . $in_log_output_php );
                   
                return false;
            }
            break;
        case DF_LEGACY_ROLE_DRIVER_ID:
            /////////////////////////////////////////////////////////////////////////////
            // データベースから変数情報を取得する。
            //   $host_vars:        変数一覧返却配列
            //                      [ホスト名(IP)][ 変数名 ]=>具体値
            /////////////////////////////////////////////////////////////////////////////
            $ret = $in_ansdrv->getDBRoleVarList($in_pattern_id,
                                                $in_operation_id,
                                                $host_vars,
                                                $MultiArray_vars_list,$All_vars_list);   // #1200 2017/06/19 Append

            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010003"));
                require ($in_root_dir_path . $in_log_output_php );
                   
                return false;
            }
            break;
        }

        $ret = $in_ansdrv->addSystemvars($host_vars,$hostprotocollist);

        // Legacy-Role 多次元配列　恒久版対応
        // ansibleで実行するファイル作成
        $ret = $in_ansdrv->CreateAnsibleWorkingFiles($hostlist,
                                                     $host_vars,
                                                     $playbooklist,
                                                     $dialogfilelist,
                                                     // Legacy-Role対応
                                                     $rolenamelist,
                                                     $role_rolenamelist,
                                                     $role_rolevarslist,
                                                     $role_roleglobalvarslist,
                                                     $hostprotocollist,
                                                     $hostinfolist,
                                                     $host_child_vars,
                                                     $DB_child_vars_master,
                                                     $MultiArray_vars_list,
                                                     $def_vars_list,
                                                     $def_array_vars_list,
                                                     $in_exec_mode,
                                                     $in_exec_playbook_hed_def,
                                                     $in_exec_option); 
        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010005"));
            require ($in_root_dir_path . $in_log_output_php );
               
            return false;
        }
        return true;
    }

    function getAnsiblePlaybookOptionParameter($OptionParameter,&$JobTemplatePropertyParameterAry,&$JobTemplatePropertyNameAry,&$ErrorMsgAry)
    {
        global $objMTS;

        $result                        = true;
        $ErrorMsgAry                   = array();
        $JobTemplatePropertyInfo       = array();

        // Towerが扱えるオプションパラメータ取得
        $ret = getJobTemplateProperty($JobTemplatePropertyInfo);
        if($ret !== true)
        {
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-56100",array(basename(__FILE__),__LINE__));
            $ErrorMsgAry[] = $FREE_LOG;
            return false;
        }
        $param  = "-__dummy__ " . trim($OptionParameter) . ' ';
        $ParamAry = preg_split("/((\s)-)/", $param);
        // 無効なオプションパラメータが設定されていないか判定
        foreach($ParamAry as $ParamString) {
            if(trim($ParamString) == '-__dummy__') {
                continue;
            }

            $hit = false;
            $ChkParamString = '-' . $ParamString . ' ';
            foreach($JobTemplatePropertyInfo as $JobTemplatePropertyRecode) {
                $KeyString = trim($JobTemplatePropertyRecode['KEY_NAME']);
                if(trim($KeyString) != "") {
                    $ret = preg_match('/^' . $KeyString . '/', $ChkParamString);
                    if($ret != 0) {
                        $hit = true;
                        break;
                    }
                }
                $KeyString = trim($JobTemplatePropertyRecode['SHORT_KEY_NAME']);
                if(trim($KeyString) != "") {
                    $ret = preg_match('/^' . $KeyString . '/', $ChkParamString);
                    if($ret != 0) {
                        $hit = true;
                        break;
                    }
                }
            }
            if($hit === false) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000104",array($ChkParamString));
                $ErrorMsgAry[] = $FREE_LOG;
            }
        }
        if(count($ErrorMsgAry) != 0) {
            return false;
        }

        foreach($JobTemplatePropertyInfo as $JobTemplatePropertyRecode) {
            $JobTemplatePropertyNameAry[$JobTemplatePropertyRecode['PROPERTY_NAME']]= 0;   
            if(strlen(trim( $JobTemplatePropertyRecode['KEY_NAME'] )) != 0) {
                $ret = makeJobTemplateProperty($JobTemplatePropertyRecode['KEY_NAME'],
                                               $JobTemplatePropertyRecode['PROPERTY_TYPE'],
                                               $JobTemplatePropertyRecode['PROPERTY_NAME'],
                                               $JobTemplatePropertyParameterAry,
                                               $ParamAry,
                                               $ErrorMsgAry);
                if($ret === false) {
                    $result=false;
                }
            }
            if(strlen(trim( $JobTemplatePropertyRecode['SHORT_KEY_NAME'] )) != 0) {
                $ret = makeJobTemplateProperty($JobTemplatePropertyRecode['SHORT_KEY_NAME'],
                                               $JobTemplatePropertyRecode['PROPERTY_TYPE'],
                                               $JobTemplatePropertyRecode['PROPERTY_NAME'],
                                               $JobTemplatePropertyParameterAry,
                                               $ParamAry,
                                               $ErrorMsgAry);
                if($ret === false) {
                    $result=false;
                }
            }
        }
        return $result;
    }

    function getJobTemplateProperty(&$in_JobTemplatePropertyInfo) {

        global $root_dir_path;
        global $db_model_ch;
        global $objDBCA;
        global $objMTS;

        require_once ($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");

        //$dbobj = new BackyardCommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,-1);
        $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,-1);

        $in_JobTemplatePropertyInfo = array();

        $sql =            " SELECT * FROM B_ANS_TWR_JOBTP_PROPERTY ";
        $sql = $sql .     " WHERE    DISUSE_FLAG = '0'             ";

        $arrayBind = array();
        $objQuery  = array();

        $dbobj->ClearLastErrorMsg();
        $ret = $dbobj->dbaccessExecute($sql, $arrayBind ,$objQuery);
        if($ret === false) {
            return $dbobj->GetLastErrorMsg();
        }

        while($row = $objQuery->resultFetch()) {
            $in_JobTemplatePropertyInfo[] = array('KEY_NAME'      =>$row['KEY_NAME'],
                                                  'SHORT_KEY_NAME'=>$row['SHORT_KEY_NAME'],
                                                  'PROPERTY_TYPE' =>$row['PROPERTY_TYPE'],
                                                  'PROPERTY_NAME' =>$row['PROPERTY_NAME'],
                                                  'TOWERONLY'     =>$row['TOWERONLY']);
        }
        unset($objQuery);
        unset($dbobj);
        return true;
    }

    function makeJobTemplateProperty($KeyString,$PropertyType,$PropertyName,&$JobTemplatePropertyParameterAry,$ParamAry,&$ErrorMsgAry) {
        global $objMTS;
        $result = true;
        foreach($ParamAry as $ParamString) {
            $ChkParamString = '-' . $ParamString . ' ';
            $ret = preg_match('/^' . $KeyString . '/', $ChkParamString);
            if($ret === 1)
            {

                $PropertyAry = preg_split('/^' . $KeyString . '/', $ChkParamString);
                //6000001 = "値が設定されていないオプションパラメータがあります。(パラメータ: {})";
                //6000002 = "重複しているオプションパラメータがあります。(パラメータ: {})";
                //6000003 = "不正なオプションパラメータがあります。(パラメータ: {})";
                switch($PropertyType) {
                case DF_JobTemplateKeyValueProperty:
                    if(@strlen(@trim($PropertyAry[1])) == 0) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000001",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 
                    if(isset($JobTemplatePropertyParameterAry[$PropertyName])) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000002",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    }

                    $JobTemplatePropertyParameterAry[$PropertyName] = trim($PropertyAry[1]);
                    break;
                case DF_JobTemplateVerbosityProperty:
                    $PropertyAry = preg_split('/^(v)*/', $ParamString);
                    if(@strlen(@trim($PropertyAry[1])) != 0)
                    {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000003",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 
                    if(isset($JobTemplatePropertyParameterAry[$PropertyName])) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000002",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    }
                    if(@strlen(@trim($ParamString)) >= 6) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000003",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 
                    $JobTemplatePropertyParameterAry[$PropertyName] = strlen(trim($ParamString));
                    break; 
                case DF_JobTemplatebooleanTrueProperty:
                    if(@strlen(@trim($PropertyAry[1])) != 0)
                    {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000003",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 
                    if(isset($JobTemplatePropertyParameterAry[$PropertyName])) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000002",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    }
                    $JobTemplatePropertyParameterAry[$PropertyName] = true;
                    break; 
                case DF_JobTemplateExtraVarsProperty:
                    if(@strlen(@trim($PropertyAry[1])) == 0)
                    {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000001",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 
                    if(isset($JobTemplatePropertyParameterAry[$PropertyName])) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000002",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    }
                    $ExtVarString = trim($PropertyAry[1]);
                    $ret = makeExtraVarsParameter($ExtVarString);
                    if($ret === false) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000003",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    }
                    $JobTemplatePropertyParameterAry[$PropertyName] = $ExtVarString;
                    break; 
                }
            }
        }
        return $result;
    }
    function makeExtraVarsParameter(&$ExtVarString) {
        $String = " " . $ExtVarString . " ";
        $ValList = preg_split("/(\s)+(\S)+(\s)*=(\s)*/", $String);
        if(count($ValList) > 1)
        {
            // 先頭に空が入るので取り除く
            if(strlen(trim($ValList[0])) == 0)
            {
                unset($ValList[0]);
            }
        }
        // 具体値の設定を確認
        $Val = array();
        foreach($ValList as $Val) {
            if(strlen(trim($Val)) == 0) {
                return false;
            }
            $ValAry[] = $Val;
        }
        $VarCount = preg_match_all("/(\s)+(\S)+(\s)*=(\s)*/", $String,$VarList);
        if($VarCount == 0) {
            return false;
        }
        if(count($ValAry) != $VarCount) {
            return false;
        }
        $idx = 0;
        $ExtVarString = "";
        foreach($VarList[0] as $VarName)
        {
            $VarName = preg_split("/(\s)*=(\s)*/", $VarName);
            $CR = "";
            if(strlen($ExtVarString) != 0)
                $CR = "\n";
            $ExtVarString  .= $CR . trim($VarName[0]) . ': ' .  $ValAry[$idx];
            $idx++;
        }
        return true;
    }
    function getMovementAnsibleExecOption($Pattern_id,&$ExecOption) {

        global $root_dir_path;
        global $db_model_ch;
        global $objDBCA;
        global $objMTS;

        require_once ($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
        $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,-1);

        $in_JobTemplatePropertyInfo = array();

        $sql =            " SELECT ANS_EXEC_OPTIONS FROM C_PATTERN_PER_ORCH ";
        $sql = $sql .     " WHERE    PATTERN_ID = $Pattern_id ";

        $arrayBind = array();
        $objQuery  = array();

        $dbobj->ClearLastErrorMsg();
        $ret = $dbobj->dbaccessExecute($sql, $arrayBind ,$objQuery);
        if($ret === false) {
            return $dbobj->GetLastErrorMsg();
        }

        while($row = $objQuery->resultFetch()) {
            $ExecOption = $row['ANS_EXEC_OPTIONS'];
        }
        unset($objQuery);
        unset($dbobj);
        return true;
    }
?>
