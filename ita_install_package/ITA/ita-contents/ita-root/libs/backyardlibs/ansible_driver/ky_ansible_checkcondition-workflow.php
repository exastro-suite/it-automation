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
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php      = '/libs/commonlibs/common_db_connect.php';
    $ansible_restapi_php = '/libs/commonlibs/common_ansible_restapi.php';
    $ansible_create_files_php        = '/libs/backyardlibs/ansible_driver/CreateAnsibleExecFiles.php';
    $ansible_table_define_php        = '/libs/backyardlibs/ansible_driver/AnsibleTableDefinition.php';
    // AnsibleTower実行モジュール
    $AnsibleTowerExecute_php         = "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerExecute.php";

    // DB更新時のユーザーID設定
    switch($vg_driver_id){
    case DF_LEGACY_DRIVER_ID:
        $db_access_user_id = -100003; // legacy作業実行プロシージャ
        break;
    case DF_LEGACY_ROLE_DRIVER_ID:
        $db_access_user_id = -100011; // legacy作業実行プロシージャ
        break;
    case DF_PIONEER_DRIVER_ID:
        $db_access_user_id = -100005; // Legacy-Role作業実行プロシージャ
        break;
    }

    // REST API URL
    $RequestURI          = "/restapi/ansible_driver/statuscheck.php";
    $Method              = 'GET';
    $intNumPadding       = 10;
    
    $file_subdir_zip_result     = 'FILE_RESULT';
    
    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $tgt_execution_no_array     = array();  // 確認対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_str       = '';       // 確認対象のEXECUTION_NOのリストを格納
    $num_of_tgt_execution_no    = 0;        // 確認対象のEXECUTION_NOの個数を格納
    
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
        // ANSIBLEインタフェース情報を取得
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
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000100")) );
        }
        
        // SQL発行
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
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
                
        ////////////////////////////////////////////////////////////////////////////////////////////////
        // 「作業インスタンステーブル」から確認対象レコードの一意キーを取得(ロック不要)               //
        // ※ITA_EXT_STM_ID：2(Websam Network Automation)                                             //
        ////////////////////////////////////////////////////////////////////////////////////////////////
        // SQL生成
        $sql = "SELECT EXECUTION_NO
                FROM   $vg_exe_ins_msg_table_name
                WHERE  DISUSE_FLAG = '0'
                AND    STATUS_ID IN( 3, 4 )";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000400")) );
        }
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000500")) );
        }
        while ( $row = $objQuery->resultFetch() ){
            // fetch行の情報をarrayに追加
            array_push( $tgt_execution_no_array, $row['EXECUTION_NO'] );
        }
        // fetch行数を取得
        $num_of_tgt_execution_no = $objQuery->effectedRowCount();
        
        // 確認対象レコードが0件の場合は処理終了へ
        if( $num_of_tgt_execution_no < 1 ){
            // 例外処理へ(例外ではないが・・・)
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-STD-50001") );
        }
        
        // DBアクセス事後処理
        unset($objQuery);
        
        // 確認対象のEXECUTION_NOのリストをカンマ区切りの文字列に変換
        $tgt_execution_no_str = implode(",", $tgt_execution_no_array );
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50002",$tgt_execution_no_str);
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////
        // ローカル変数(ループ)宣言   //
        ////////////////////////////////
        $restapi_err_flag           = 0;        // REST APIでの異常フラグ(1：異常発生)
        $tgt_execution_row          = array();  // 単一行SELECTの結果を格納
        $RequestContents            = array();  // REST API向けのリクエストコンテンツ(JSON)を格納
        $ResponseStatus             = '';       // REST APIから返却されたexecutionidを格納
        $ResponseResultdata         = array();  // REST APIから返却されたexecutionidを格納

        $zip_result_file = "";

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////////////////////////////////////
        // 確認対象のEXECUTION_NOだけループ                           //
        ////////////////////////////////////////////////////////////////
        foreach( $tgt_execution_no_array as $tgt_execution_no ){
            // ループ内で利用するローカル変数を初期化
            $restapi_err_flag = 0;
            unset($tgt_execution_row);
            $tgt_execution_row = array();
            unset($RequestContents);
            $RequestContents = array();
            $ResponseStatus = '';
            unset($ResponseResultdata);
            $ResponseResultdata = array();
            $intJournalSeqNo = null;
            $zip_result_file = "";

            ////////////////////////////////
            // トランザクション開始       //
            ////////////////////////////////
            if( $objDBCA->transactionStart()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000600")) );
            }
            
            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = true;
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50004",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            
            ////////////////////////////////////////////////////////////////
            // 該当レコードをSELECT(ロック)                               //
            ////////////////////////////////////////////////////////////////

            $arrayConfig = array();
            CreateExecInstMngArray($arrayConfig);
            SetExecInstMngColumnType($arrayConfig);

            $temp_array = array('WHERE'=>" EXECUTION_NO = :EXECUTION_NO AND DISUSE_FLAG = '0' AND STATUS_ID IN (3, 4) ");
            
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
            
            $arrayUtnBind['EXECUTION_NO'] = $tgt_execution_no;
            
            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            
            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000700")) );
            }
            
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000800")) );
            }
            
            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00000900")) );
            }
            
            while ( $row = $objQueryUtn->resultFetch() ){
                $tgt_execution_row = $row;
            }
            // fetch行数を取得
            $fetch_counter = $objQueryUtn->effectedRowCount();
            
            // DBアクセス事後処理
            unset($objQueryUtn);
            
            // 確認対象レコードが特定できない場合は警告を出したうえで次レコードの処理へ
            if( $fetch_counter != 1 ){
                // 警告フラグON
                $warning_flag = 1;
                
                // 警告メッセージ出力
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50005",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
                
                // ロールバック(念のため)
                if( $objDBCA->transactionRollBack()===false ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
                }

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;
                
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50006",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
                
                // トランザクション終了
                $objDBCA->transactionExit();
                
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50007",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
                
                // 次レコードの処理へ
                continue;
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50008",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            require_once ($root_dir_path . $ansible_create_files_php);

            $ansdrv = new CreateAnsibleExecFiles($vg_driver_id,
                                                 $lv_ans_storage_path_lnx,
                                                 $lv_ans_storage_path_ans,
                                                 $lv_sym_storage_path_ans,
                                                 $vg_legacy_playbook_contents_dir,
                                                 $vg_pioneer_playbook_contents_dir,
                                                 $vg_template_contents_dir,
                                                 $vg_template_contents_dir,
                                                 $vg_copy_contents_dir,
                                                 $vg_ansible_vars_masterDB,
                                                 $vg_ansible_vars_assignDB,
                                                 $vg_ansible_pattern_vars_linkDB,
                                                 $vg_ansible_pho_linkDB,
                                                 $vg_ansible_master_fileDB,
                                                 $vg_ansible_master_file_pkeyITEM,
                                                 $vg_ansible_master_file_nameITEM,
                                                 $vg_ansible_pattern_linkDB,
                                                 $vg_ansible_role_packageDB,
                                                 $vg_ansible_roleDB,
                                                 $vg_ansible_role_varsDB,
                                                 $objMTS,
                                                 $objDBCA);
                
            $tmp_array_dirs = $ansdrv->getAnsibleWorkingDirectories($vg_OrchestratorSubId_dir,$tgt_execution_no);
            $zip_data_source_dir = $tmp_array_dirs[4];
                
            // 実行エンジンを判定
            if($lv_ans_if_info['ANSIBLE_EXEC_MODE'] == DF_EXEC_MODE_ANSIBLE) {

                ////////////////////////////////////////////////////////////////
                // REST APIコール                                             //
                ////////////////////////////////////////////////////////////////            
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50009");
                    require ($root_dir_path . $log_output_php );
                }
                ////////////////////////////////////////////////////////////////
                // REST API向けのリクエストURLを準備                          //
                ////////////////////////////////////////////////////////////////
                $RequestContents
                = array(
                        //データリレイパス
                        'DATA_RELAY_STORAGE_TRUNK'=>$lv_ans_storage_path_ans,
                        //オーケストレータ識別子
                        "ORCHESTRATOR_SUB_ID"=>$vg_OrchestratorSubId,
                        //作業実行ID
                        "EXE_NO"=>$tgt_execution_no);
                        //ドライランモードは不要なので追加しない。
    
                ////////////////////////////////////////////////////////////////
                // REST APIコール                                             //
                ////////////////////////////////////////////////////////////////
                $rest_api_response = ansible_restapi_access( $lv_ans_protocol,
                                                             $lv_ans_hostname,
                                                             $lv_ans_port,
                                                             $lv_ans_access_key_id,
                                                             $lv_ans_secret_access_key,
                                                             $RequestURI,
                                                             $Method,
                                                             $RequestContents );
    
                ////////////////////////////////////////////////////////////////
                // REST API結果判定                                           //
                ////////////////////////////////////////////////////////////////
                $restapi_err_flag = 0;
                $sql_exec_flag =  0;
                $Status = RESTAPIResponsCheck($tgt_execution_no,
                                              $rest_api_response,
                                              $restapi_err_flag);
    
                // REST APIの戻り値が異常の場合にログ出力
                if( $restapi_err_flag != 0 ){
                    //  REST APIの戻り値が異常は$Statusを想定外エラーにしている
                    $sql_exec_flag =  1;
    
                    // 警告フラグON
                    $warning_flag = 1;
                    
                    // 警告メッセージ出力
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50019",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );

                    // REST APIの戻り値を出力
                    ob_start();
                    var_dump($rest_api_response);
                    $FREE_LOG = "REST API Response\n" . ob_get_contents();
                    ob_clean();
                    require ($root_dir_path . $log_output_php );

                }
            } else {
                $sql_exec_flag =  0;
                $UIExecLogPath  = $ansdrv->getAnsible_out_Dir() . "/" . "exec.log";
                $UIErrorLogPath = $ansdrv->getAnsible_out_Dir() . "/" . "error.log";
                $Status = 0;

                ////////////////////////////////////////////////////////////////
                // AnsibleTowerから実行                                       //
                ////////////////////////////////////////////////////////////////
                $ret = AnsibleTowerExecution(DF_CHECKCONDITION_FUNCTION,$lv_ans_if_info,$tgt_execution_row,$ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$Status);

                if( $Status == 5 ||
                    $Status == 6 ||
                    $Status == 7 ||
                    $Status == 8 ) {
                    // 5:正常終了時
                    // 6:完了(異常)
                    // 7:想定外エラー
                    // 8:緊急停止
                    $sql_exec_flag =  1;
                } else {
                    $Status = -1;
                }
            }
            if ( $log_level === 'DEBUG' ){
                // 状態をログに出力
                $FREE_LOG = sprintf("ExecutionNo:%s  Status:%s",$tgt_execution_no,$Status);
                require ($root_dir_path . $log_output_php );
            }
            if( $Status == 5 ||
                $Status == 6 ||
                $Status == 7 ||
                $Status == 8 ||
                $restapi_err_flag != 0 ){
                // 5:正常終了時
                // 6:完了(異常)
                // 7:想定外エラー
                // 8:緊急停止
                
                // zipファイル名を作成
                $zip_result_file = 'ResultData_' . str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT ) . '.zip';

                $tmp_zip_file_name = $zip_result_file;
                $tmp_subdir_name   = $file_subdir_zip_result;
                $tmp_exe_ins_file_dir = $vg_exe_ins_result_file_dir;

                // OSコマンドでzip圧縮する
                $tmp_str_command = "cd " . $zip_data_source_dir . "; zip -r " . $zip_temp_save_dir . "/" . $tmp_zip_file_name . " .";
                shell_exec( $tmp_str_command );

                $strToFUC01FilePerRIValueCurDir = $tmp_exe_ins_file_dir . "/" . $tmp_subdir_name . "/" . str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT );

                $tmp_utn_file_dir  = $strToFUC01FilePerRIValueCurDir;

                // ZIPディレクトリ削除
                system('/bin/rm -rf ' . $tmp_utn_file_dir . ' >/dev/null 2>&1');

                if( !is_dir( $tmp_utn_file_dir ) ){
                    // ここ(UTNのdir)だけは再帰的に作成する
                    if( !mkdir( $tmp_utn_file_dir, 0777,true ) ){
                        // 事前準備を中断
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59051") );
                    }
                    if( !chmod( $tmp_utn_file_dir, 0777 ) ){
                        // 事前準備を中断
                        throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59052") );
                    }
                }

                // zipファイルを正式な置き場に移動
                rename( $zip_temp_save_dir . "/" . $tmp_zip_file_name,
                        $tmp_utn_file_dir . "/" . $tmp_zip_file_name );

                // zipファイルの存在を確認
                if( !file_exists( $tmp_utn_file_dir . "/" . $tmp_zip_file_name ) ){
                    $prepare_err_flag = 1;
                    
                    // 警告メッセージ出力
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59053",array($tgt_execution_no,$tmp_zip_file_name));
                    require ($root_dir_path . $log_output_php );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-59101",array($tgt_execution_no,$tmp_zip_file_name));
                    require ($root_dir_path . $log_output_php );
                }
                unset($tmp_str_command);
                unset($tmp_zip_file_name);
                unset($tmp_subdir_name);
                unset($tmp_utn_file_dir);
                unset($tmp_exe_ins_file_dir);
            }

            ////////////////////////////////////////////////////////////////
            // statusによって処理を分岐                                   //
            ////////////////////////////////////////////////////////////////
            if($Status != -1) {
                // SQL(UPDATE)をEXECUTEする
                $sql_exec_flag =  1;

                ////////////////////////////////////////////////////////////////
                // 「C_EXECUTION_MANAGEMENT」のUPDATE文を作成(成功 or 失敗)   //
                ////////////////////////////////////////////////////////////////
                // クローン作成
                $cln_execution_row = $tgt_execution_row;
                
                // 変数バインド準備
                $cln_execution_row['JOURNAL_SEQ_NO']    = "";                       // 後続でシーケンス払い出しするので一旦ブランク
                $cln_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
                $cln_execution_row['STATUS_ID']         = $Status;
                $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50029",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }
            else{
                ////////////////////////////////////////////////////////////////
                // 遅延を判定                                                 //
                ////////////////////////////////////////////////////////////////
                // 遅延タイマを取得
                $time_limit = $tgt_execution_row['I_TIME_LIMIT'];

                $delay_flag = 0;
                
                // ステータスが実行中(3)、かつ制限時間が設定されている場合のみ遅延判定する
                if( $tgt_execution_row['STATUS_ID'] == 3 && $time_limit != "" ){
                    // 開始時刻(「UNIXタイム.マイクロ秒」)を生成
                    $varTimeDotMirco = convFromStrDateToUnixtime($tgt_execution_row['TIME_START'], true );
                    // 開始時刻(マイクロ秒)＋制限時間(分→秒)＝制限時刻(マイクロ秒)
                    $varTimeDotMirco_limit = $varTimeDotMirco + ($time_limit * 60); //単位（秒）
                    
                    // 現在時刻(「UNIXタイム.マイクロ秒」)を生成
                    $varTimeDotNowStd = getMircotime(0);

                    // 制限時刻と現在時刻を比較
                    if( $varTimeDotMirco_limit < $varTimeDotNowStd ){
                        $delay_flag = 1;
                        
                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ){
                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50030",$tgt_execution_no);
                            require ($root_dir_path . $log_output_php );
                        }
                    }
                }
                
                if( $delay_flag == 0 &&
                    $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50031",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
                
                // 遅延が発生の場合
                if( $delay_flag == 1 ){
                    // SQL(UPDATE)をEXECUTEする
                    $sql_exec_flag = 1;
                    
                    ////////////////////////////////////////////////////////////////
                    // ステータスを判定                                           //
                    ////////////////////////////////////////////////////////////////
                    // ステータスを「実行中(遅延)」とする
                    $Status = 4;
                        
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50034",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                    ////////////////////////////////////////////////////////////////////////
                    // 「C_EXECUTION_MANAGEMENT」のUPDATE文を作成(実行中 or 実行中(遅延)  //
                    ////////////////////////////////////////////////////////////////////////
                    // クローン作成
                    $cln_execution_row = $tgt_execution_row;
                    
                    // 変数バインド準備
                    $cln_execution_row['JOURNAL_SEQ_NO']    = "";                       // 後続でシーケンス払い出しするので一旦ブランク
                    $cln_execution_row['STATUS_ID']         = $Status;
                    $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                    
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50036",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                }
            }
            // SQL(UPDATE)をEXECUTEすると判断した場合
            if( $sql_exec_flag == 1 ){
                ////////////////////////////////////////////////////////////////
                // シーケンスをロック                                         //
                ////////////////////////////////////////////////////////////////
                $retArray = getSequenceLockInTrz($vg_exe_ins_msg_table_jnl_seq,'A_SEQUENCE');
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
                }
                
                // 履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
                }
                
                // シーケンスをバインド準備
                $cln_execution_row['JOURNAL_SEQ_NO']    = $retArray[0];
                $intJournalSeqNo = $retArray[0];
                
                $cln_execution_row['FILE_RESULT'] = $zip_result_file;
                
                ////////////////////////////////////////////////////////////////
                // UPDATEを実行                                               //
                ////////////////////////////////////////////////////////////////
                // SQL作成＋バインド用変数準備

                // SQL作成＋バインド用変数準備
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
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
                }
                
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                    $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
                }
                
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
                }
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50037",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
                
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
                }
                
                ////////////////////////////////////////////////////////////////
                // コミット(レコードロックを解除)                             //
                ////////////////////////////////////////////////////////////////
                $r = $objDBCA->transactionCommit();
                if (!$r){
                    // 異常フラグON
                    $error_flag = 1;
                    
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50039",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
                
                // DBアクセス事後処理
                if ( isset($objQuery)    ) unset($objQuery);
                if ( isset($objQueryUtn) ) unset($objQueryUtn);
                if ( isset($objQueryJnl) ) unset($objQueryJnl);
                
                if( $zip_result_file != "" ){
                    $tmp_utn_file_dir  = $strToFUC01FilePerRIValueCurDir;
                    $tmp_zip_file_name = $zip_result_file;

                    $tmp_jnl_file_dir_trunk = $tmp_utn_file_dir . "/old";
                    $tmp_jnl_file_dir_focus = $tmp_jnl_file_dir_trunk . "/" . str_pad( $intJournalSeqNo, $intNumPadding, "0", STR_PAD_LEFT );

                    // 履歴フォルダへコピー
                    if( !mkdir( $tmp_jnl_file_dir_trunk, 0777 ) ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59851",array($tgt_execution_no, __FILE__ , __LINE__, "00001751"));
                        require ($root_dir_path . $log_output_php );
                    }
                    else{
                        if( !mkdir( $tmp_jnl_file_dir_focus, 0777 ) ){
                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59852",array($tgt_execution_no, __FILE__ , __LINE__, "00001752"));
                            require ($root_dir_path . $log_output_php );
                        }
                        else{
                            $boolCopy = copy( $tmp_utn_file_dir . "/" . $tmp_zip_file_name, $tmp_jnl_file_dir_focus . "/". $tmp_zip_file_name);
                            if( $boolCopy === false ){
                                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59853",array($tgt_execution_no, __FILE__ , __LINE__, "00001753"));
                                require ($root_dir_path . $log_output_php );
                            }
                            else{
                                // トレースメッセージ
                                if ( $log_level === 'DEBUG' ){
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-59801",$tmp_zip_file_name);
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
            }
            
            ////////////////////////////////
            // トランザクション終了       //
            ////////////////////////////////
            $objDBCA->transactionExit();
            
            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;

            // 実行エンジンを判定
            if($lv_ans_if_info['ANSIBLE_EXEC_MODE'] != DF_EXEC_MODE_ANSIBLE) {
                if( $Status == 5 ||
                    $Status == 6 ||
                    $Status == 7 ||
                    $Status == 8 ) {
                        ////////////////////////////////////////////////////////////////
                        // AnsibleTower ゴミ掃除 戻り値は確認しない。
                        ////////////////////////////////////////////////////////////////
                        AnsibleTowerExecution(DF_DELETERESOURCE_FUNCTION,$lv_ans_if_info,$tgt_execution_row,$ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$Status);
                }
            }
            
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50040",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            unset($ansdrv);
        }
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50041");
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50042",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50043");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50044",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50045");
                }
            }
            require ($root_dir_path . $log_output_php );
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                if( !empty( $tgt_execution_no ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50046",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50047");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50048",$tgt_execution_no);
                }
                else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50049");
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

    function RESTAPIResponsCheck($in_execution_no,$in_rest_api_response,&$in_restapi_err_flag){
        global $objMTS;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        global $log_output_php;

        $chk_Status = -1;
        $in_restapi_err_flag = 1;
        ////////////////////////////////////////////////////////////////
        // 結果判定                                                   //
        ////////////////////////////////////////////////////////////////
        if( $in_rest_api_response['StatusCode'] != 200 ){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;

            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50011",$in_execution_no);
            require ($root_dir_path . $log_output_php );

            return $chk_Status;
        }
        ////////////////////////////////////////////////////////////////
        // $rest_api_response['ResponsContents']の返却を確認          //
        ////////////////////////////////////////////////////////////////
        
        // ResponsContentsが取れない場合にワーニングが出るので抑制する。
        if( !@is_array($in_rest_api_response['ResponsContents']) )
        {
            // ResponsContentsが取れない場合は想定外エラーにしない。
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50018",$in_execution_no);
            require ($root_dir_path . $log_output_php );

            // REST APIの戻り値を出力
            $FREE_LOG = $in_rest_api_response[ALLResponsContents];
            require ($root_dir_path . $log_output_php );

            // 結果を実行中にする。
            $chk_Status = -1;
            $in_restapi_err_flag = 0;

            return $chk_Status;
        }
        //////////////////////////////////////////////////////////////////////
        // $rest_api_response['ResponsContents']からstatus情報を取り出し    //
        //////////////////////////////////////////////////////////////////////
        if( !array_key_exists( "status", $in_rest_api_response['ResponsContents'] ) ){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;
            
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50013",$in_execution_no);
            require ($root_dir_path . $log_output_php );

            return $chk_Status;
        }
        // status情報を取り出し
        $ResponseStatus = $in_rest_api_response['ResponsContents']["status"];
                    
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50012",array($in_execution_no,$ResponseStatus));
            require ($root_dir_path . $log_output_php );
        }
                   
        // resultdata情報を取り出し
        $ansibleWfResult = '';
        $ResponseResultdata = '';
        if( array_key_exists( "resultdata", $in_rest_api_response['ResponsContents'] ) ){
            // 取り出し
            $ResponseResultdata = $in_rest_api_response['ResponsContents']["resultdata"];
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50016",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            if( @array_key_exists( "ANSIBLE_WF_RESULT", $ResponseResultdata ) ){
                // 取り出し
                $ansibleWfResult = $ResponseResultdata["ANSIBLE_WF_RESULT"];
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50023",array($in_execution_no,$ansibleWfResult));
                    require ($root_dir_path . $log_output_php );
                }
            }
        }
        else{
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;

            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50017",$in_execution_no);
            require ($root_dir_path . $log_output_php );

            return $chk_Status;

        }
        
        // REST API 戻り値は問題ないのでフラグクリア        
        $in_restapi_err_flag = 0;

        ////////////////////////////////////////////////////////////////
        // statusによって処理を分岐                                   //
        ////////////////////////////////////////////////////////////////
        //完了の場合
        if( $ResponseStatus == "SUCCEED")
        {   //完了の場合はansible実行結果を判定
            if( $ansibleWfResult == '')
            {  //正常終了時
                // ステータスを「完了」とする
                $chk_Status = 5;
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50028",$in_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }
            else
            {  //異常終了時
                // ステータスを「完了(異常)」とする
                $chk_Status = 6;
                
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50027",$in_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }
        }
        //異常の場合
        elseif( $ResponseStatus == "FAILED"){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;
                
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50025",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }
        //緊急停止の場合
        elseif( $ResponseStatus == "FORCED"){
            // ステータスを「緊急停止」とする
            $chk_Status = 8;
                
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50026",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }
        //未実行の場合
        elseif( $ResponseStatus == "NOT RUNNING"){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;
                
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50025",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }
        //実行中の場合
        elseif( $ResponseStatus == "RUNNING"){
            $chk_Status = -1;
        }
        //その他の場合
        else{
            $in_restapi_err_flag = 1;

            // ステータスを「想定外エラー」とする
            $chk_Status = 7;
                
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50025",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }
        return $chk_Status;
    }            
?>
