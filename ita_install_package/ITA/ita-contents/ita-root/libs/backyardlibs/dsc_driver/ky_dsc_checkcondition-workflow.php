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
    // 定数定義                    //
    ////////////////////////////////
    $log_output_php          = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php        = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php          = '/libs/commonlibs/common_db_connect.php';
    $dsc_restapi_php         = '/libs/commonlibs/common_dsc_restapi.php';
    $dsc_create_files_php    = '/libs/backyardlibs/dsc_driver/CreateDscExecFiles.php';

    ////////////////////////////////
    // REST API ResultCode 定義   //
    ////////////////////////////////
    define("DSC_SUCCESS"               ,"0");
    define("DCS_ERR_HTTP_REQ"        ,"1000");  // HTTPパラメータ異常
    define("DSC_ERR_DSC_DIR"         ,"1001");  // DSCインストールディレクトリへの移動に失敗
    define("DSC_ERR_HTTP_HEDER"      ,"1002");  // HTTPヘッダーに必要な情報がない
    define("DSC_ERR_AUTH"            ,"1003");  // DSC RESTAPI 認証エラー
    define("DSC_ERR_DSC_CONF"        ,"1004");  // DSC コンフィグレーション(実行)エラー
    define("DSC_ERR_DSC_TEST"        ,"1005");  // DSC テスト(確認)エラー
    define("DSC_ERR_TAR_DEL"         ,"2000");  // Collect Commandで作成したZIPファイルの削除失敗時のrmコマンドの戻り値への加算値

    // DB更新時のユーザーID設定
    switch($vg_driver_id){
    case DF_DSC_DRIVER_ID:
        $db_access_user_id   = -100801; // DSC状態確認プロシージャ
        break;
    }
    $RequestURI              = "/restapi/dsc_driver/CollectCommandExecute.php";  // REST API URI
    $Method                  = 'GET';
    $intNumPadding           = 10;
    $exe_code                = '2';     // 確認プロセス

    $file_subdir_zip_result     = 'FILE_RESULT';

    ////////////////////////////////
    // ローカル変数(全体)宣言          //
    ////////////////////////////////
    $warning_flag            = 0;        // 警告フラグ(1：警告発生)
    $error_flag              = 0;        // 異常フラグ(1：異常発生)
    $tgt_execution_no_array  = array();  // 確認対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_str    = '';       // 確認対象のEXECUTION_NOのリストを格納
    $num_of_tgt_execution_no = 0;        // 確認対象のEXECUTION_NOの個数を格納
    $configlist              = array();  // Configリソースリスト

    ////////////////////////////////
    // REST API接続function定義     //
    ////////////////////////////////
    require_once ($root_dir_path . $dsc_restapi_php );

    ///////////////////////////////
    // 業務処理開始                //
    ///////////////////////////////

    // トランザクションフラグ(初期値はfalse)
    $transaction_flag = false;

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                    //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // DBコネクト完了
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        /////////////////////////////////
        // DSCインタフェース情報取得
        /////////////////////////////////
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
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000100")) );
        }

        //----------------------------------------------
        // SQL発行
        //----------------------------------------------
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000200")) );
        }

        // レコードFETCH
        //----------------------------------------------
    	// 廃止フラグOFFの全レコード処理(FETCH
        //----------------------------------------------
        while ( $row = $objQuery->resultFetch() ){
            $lv_dsc_if_info = $row;
        }
        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();

        // レコード無しの場合は「DSCインタフェース情報」が登録されていないので以降の処理をスキップ
        // 常駐は継続させたいので異常フラグは立てない。
        if( $num_of_rows === 0 ){
            // 警告フラグON
            $warning_flag = 1;

            // 例外処理へ：DSCインタフェース情報レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-56000") );
        }
        //----------------------------------------------
        // 「DSCインタフェース情報」が重複登録されている場合も以降の処理をスキップ
        //----------------------------------------------
        else if( $num_of_rows > 1 ){
            // 異常フラグON
            $warning_flag = 1;

            // 例外処理へ：DSCインタフェース情報レコードが単一行でない
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-56001") );
        }

        // DBアクセス事後処理
        unset($objQuery);

        //----------------------------------------------
        // DSCインタフェース情報をローカル変数に格納
        //----------------------------------------------
        $lv_dsc_storage_path_lnx  = $lv_dsc_if_info['DSC_STORAGE_PATH_LNX'];
        $lv_dsc_storage_path_dsc  = $lv_dsc_if_info['DSC_STORAGE_PATH_DSC'];

        $lv_sym_storage_path_dsc  = $lv_dsc_if_info['SYMPHONY_STORAGE_PATH_DSC'];

        $lv_dsc_protocol          = $lv_dsc_if_info['DSC_PROTOCOL'];
        $lv_dsc_hostname          = $lv_dsc_if_info['DSC_HOSTNAME'];
        $lv_dsc_port              = $lv_dsc_if_info['DSC_PORT'];
        $lv_dsc_access_key_id     = $lv_dsc_if_info['DSC_ACCESS_KEY_ID'];
        $lv_dsc_secret_access_key = ky_decrypt( $lv_dsc_if_info['DSC_SECRET_ACCESS_KEY'] );

        ////////////////////////////////////////////////////////////////////////////////////////////////
        // 「作業インスタンステーブル」から確認対象レコードの一意キーを取得(ロック不要)                                       //
        ////////////////////////////////////////////////////////////////////////////////////////////////
        //----------------------------------------------
        // SQL生成 実行中=3 実行中（遅延）=4を取得
        //----------------------------------------------
        $sql = "SELECT EXECUTION_NO
                FROM   $vg_exe_ins_msg_table_name
                WHERE  DISUSE_FLAG = '0'
                AND    STATUS_ID IN( 3, 4 )";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if($objQuery->getStatus()===false){
            // 異常フラグON
            $error_flag = 1;

            // 異常発生
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000400")) );
        }
        //----------------------------------------------
        // SQL実行
        //----------------------------------------------
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 異常発生
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000500")) );
        }
        //----------------------------------------------
        // 対象レコードを格納(row)
        //----------------------------------------------
        while ( $row = $objQuery->resultFetch() ){
            // fetch行の情報をarrayに追加
            array_push( $tgt_execution_no_array, $row['EXECUTION_NO'] );    //処理対象レコードまるごと格納
        }
        // fetch行数を取得
        //----------------------------------------------
    	// 作業インスタンス情報件数を取得
        //----------------------------------------------
        $num_of_tgt_execution_no = $objQuery->effectedRowCount();

        // 確認対象レコードが0件の場合は処理終了へ
        if( $num_of_tgt_execution_no < 1 ){
            // 例外処理へ(例外ではないが・・・)
            // 確認対象レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-STD-50001") );
        }

        // DBアクセス事後処理
        unset($objQuery);

        //----------------------------------------------
        // 確認対象のEXECUTION_NOのリストをカンマ区切りの文字列に変換
        //----------------------------------------------
        $tgt_execution_no_str = implode(",", $tgt_execution_no_array );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 確認対象レコード検出(作業番号:{$tgt_execution_no_str})
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50002",$tgt_execution_no_str);
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // ローカル変数(ループ)宣言   //
        ////////////////////////////////
        $restapi_err_flag           = 0;        // REST APIでの異常フラグ(1：異常発生)
        $tgt_execution_row          = array();  // 単一行SELECTの結果を格納
        $RequestContents            = array();  // REST API向けのリクエストコンテンツ(JSON)を格納
        $ResponseStatus             = '';       // REST APIから返却された処理結果(値)を格納
        $ResponseResultdata         = array();  // REST APIから返却された処理情報文字列(JSON)を格納
        $tgt_exec_row               = array();  // 確認対象のEXECUTION_NOのリスト

        $zip_result_file = "";

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 確認対象レコードの処理ループ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////////////////////////////////////
        // 確認対象のEXECUTION_NOだけループ                           //
        ////////////////////////////////////////////////////////////////
        foreach( $tgt_execution_no_array as $tgt_execution_no ){
            // ループ内で利用するローカル変数を初期化
            $prepare_err_flag = 0;                    // 事前準備中 エラーフラグ
            $restapi_err_flag = 0;
            unset($tgt_execution_row);
            $tgt_execution_row = array();
            unset($RequestContents);
            $RequestContents = array();
            $ResponseStatus = '';
            unset($ResponseResultdata);
            $ResponseResultdata = array();
            $intJournalSeqNo = null;
            unset($tgt_exec_row);
            $zip_result_file = "";

            ////////////////////////////////
            // トランザクション開始       //
            ////////////////////////////////
            if( $objDBCA->transactionStart()===false ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000600")) );
            }

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = true;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50004",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            ////////////////////////////////////////////////////////////////
            // 該当レコードをSELECT(ロック)                               //
            ////////////////////////////////////////////////////////////////
            //----------------------------------------------
            // 作業インスタンス情報 configのSQl生成
            //----------------------------------------------
            $arrayConfig_dsc = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "EXECUTION_NO"=>"",
                "SYMPHONY_NAME"=>"",
                "EXECUTION_USER"=>"",
                "STATUS_ID"=>"",
                "SYMPHONY_INSTANCE_NO"=>"",
                "PATTERN_ID"=>"",
                "OPERATION_NO_UAPK"=>"",
                "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
                "I_PATTERN_NAME"=>"",
                "I_OPERATION_NAME"=>"",
                "FILE_INPUT"=>"",
                "FILE_RESULT"=>"",
                "I_OPERATION_NO_IDBH"=>"",
                "RUN_MODE"=>"",
                "TIME_BOOK"=>"DATETIME",
                "TIME_START"=>"DATETIME",
                "TIME_END"=>"DATETIME",
                "I_TIME_LIMIT"=>"",
                "I_ANS_PARALLEL_EXE"=>"",
                "I_DSC_RETRY_TIMEOUT"=>"",
                "DISUSE_FLAG"=>"",
                "NOTE"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );

            $temp_array = array('WHERE'=>" EXECUTION_NO = :EXECUTION_NO AND DISUSE_FLAG = '0' AND STATUS_ID IN (3, 4) ");

            //----------------------------------------------
            // 作業インスタンス情報 ValueのSQl生成
            //----------------------------------------------
            $arrayValue_dsc = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "EXECUTION_NO"=>"",
                "SYMPHONY_NAME"=>"",
                "EXECUTION_USER"=>"",
                "STATUS_ID"=>"",
                "SYMPHONY_INSTANCE_NO"=>"",
                "PATTERN_ID"=>"",
                "OPERATION_NO_UAPK"=>"",
                "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
                "I_PATTERN_NAME"=>"",
                "I_OPERATION_NAME"=>"",
                "FILE_INPUT"=>"",
                "FILE_RESULT"=>"",
                "I_OPERATION_NO_IDBH"=>"",
                "RUN_MODE"=>"",
                "TIME_BOOK"=>"",
                "TIME_START"=>"",
                "TIME_END"=>"",
                "I_TIME_LIMIT"=>"",
                "I_ANS_PARALLEL_EXE"=>"",
                "I_DSC_RETRY_TIMEOUT"=>"",
                "DISUSE_FLAG"=>"",
                "NOTE"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );

            switch($vg_driver_id){
            case DF_DSC_DRIVER_ID:
                //----------------------------------------------
    	        // 作業インスタンス情報をSELECT
                //----------------------------------------------
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "SELECT FOR UPDATE",
                                                     "EXECUTION_NO",
                                                     $vg_exe_ins_msg_table_name,
                                                     $vg_exe_ins_msg_table_jnl_name,
                                                     $arrayConfig_dsc,
                                                     $arrayValue_dsc,
                                                     $temp_array );
                break;
            }

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $arrayUtnBind['EXECUTION_NO'] = $tgt_execution_no;

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000700")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000800")) );
            }

            //----------------------------------------------
            // SQL実行
            //----------------------------------------------
            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000900")) );
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
                // [警告]確認対象レコードをロックできず(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50005",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // ロールバック(念のため)
                if( $objDBCA->transactionRollBack()===false ){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
                }

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;

                // ロールバック(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50006",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // トランザクション終了
                $objDBCA->transactionExit();

                // トランザクション終了(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50007",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // 次レコードの処理へ
                continue;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]レコードロック(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50008",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            ////////////////////////////////////////////////////////////////
            // ターゲット情報取得                                              //
            ////////////////////////////////////////////////////////////////
           //--------------------------------------------------------------
            // 作業対象ホストテーブル情報取得(B_DSC_PHO_LINK)
            //--------------------------------------------------------------
        	// 作業対象ホストテーブル検索条件設定
        	$Status_id = $tgt_execution_row['STATUS_ID'];
        	$pattern_id = $tgt_execution_row['PATTERN_ID'];
            $operation_id = $tgt_execution_row['OPERATION_NO_UAPK'];

            $retry_timeout = $tgt_execution_row["I_DSC_RETRY_TIMEOUT"];
            if( $retry_timeout === NULL ){
                $retry_timeout = 0;
            }

            require_once ($root_dir_path . $dsc_create_files_php);
            $dscdrv = new CreateDscExecFiles($vg_driver_id,
                                             $lv_dsc_storage_path_lnx,
                                             $lv_dsc_storage_path_dsc,
                                             $lv_sym_storage_path_dsc,
                                             $vg_dsc_resource_contents_dir,
                                             $vg_dsc_vars_masterDB,
                                             $vg_dsc_vars_assignDB,
                                             $vg_dsc_pattern_vars_linkDB,
                                             $vg_dsc_pho_linkDB,
                                             $vg_dsc_master_fileDB,
                                             $vg_dsc_master_file_pkeyITEM,
                                             $vg_dsc_master_file_nameITEM,
                                             $vg_dsc_powershell_file_dir,
                                             $vg_dsc_param_file_dir,
                                             $vg_dsc_import_file_dir,
                                             $vg_dsc_configdata_file_dir,
                                             $vg_dsc_cmpoption_file_dir,
                                             $vg_dsc_certificate_file_dir,
                                             $objMTS,
                                             $objDBCA);

            // host_varsディレクトリパスを設定
            $tmp_array_dirs = $dscdrv->getDscWorkingDirectories($vg_OrchestratorSubId_dir,$tgt_execution_no);
            $dscdrv->setDsc_host_vars_Dir($tmp_array_dirs[7]);

            //----------------------------------------------------------------------
            //DSCで実行するHOST情報をデータベースより取得する。
            //----------------------------------------------------------------------
            $ret = $dscdrv->getDBHostList($pattern_id,
                                             $operation_id,
                                             $hostlist,
                                             $hostprotocollist,
                                             $hostostypelist,
                                             $hostinfolist,
                                             $retry_timeout);

            if($ret <> true){
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003") );
            }

            //--------------------------------------------------------------
            // REST API インターフェース情報格納
            //--------------------------------------------------------------
        	$lv_dsc_process_id = $exe_code;                                // 実行番号設定
            $ret = getDBDscConfigList($objDBCA,$pattern_id,$configlist);
            $lv_dsc_config_name = $configlist['RESOURCE_MATTER_NAME'];
            $lv_dsc_config_file = $configlist['RESOURCE_MATTER_FILE'];
        	$lv_dsc_config_dir  = $lv_dsc_storage_path_dsc . "\dsc" . "\\" . "ns" . "\\" . sprintf("%010s",$tgt_execution_no) . "\\" . "in" . "\\" . $lv_dsc_config_file ;
            //--------------------------------------------------------------
            // 緊急停止チェック
            //--------------------------------------------------------------
            $lv_dsc_forced_file  = $lv_dsc_if_info['DSC_STORAGE_PATH_LNX'] . "/" . "dsc" . "/" . "ns" . "/" . sprintf("%010s",$tgt_execution_no) . "/" . "out" . "/" . "forced.txt" ;  // 緊急停止ファイル設定 20170630
            if( file_exists( $lv_dsc_forced_file ) ){
                $Status = 8 ;
            }

        	////////////////////////////////////////////////////////////////
            // REST APIコール                                               //
            ////////////////////////////////////////////////////////////////
            // トレースメッセージ
            if ( $log_level === 'DEBUG' )
            {   // [処理]REST APIコール開始(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50009", $tgt_execution_no );
                require ($root_dir_path . $log_output_php );
            }

            ////////////////////////////////////////////////////////////////
            // REST API向けのリクエストURLを準備                                 //
            ////////////////////////////////////////////////////////////////
            $RequestContents
            = array(
                    // DSC処理コード
                    'DSC_PROCESS_ID'=>$lv_dsc_process_id,
                    // データリレイパス
                    'DATA_RELAY_STORAGE_TRUNK'=>$lv_dsc_storage_path_lnx,
                    //オーケストレータ識別子
                    "ORCHESTRATOR_SUB_ID"=>$vg_OrchestratorSubId_dir,
                    //作業実行ID
                    "EXE_NO"=>$tgt_execution_no,
                    //データストレージパス
                    "DSC_DATA_RELAY_STORAGE"=>$lv_dsc_storage_path_dsc,
                    //コンフィグファイルパス
                    "DSC_DATA_CONFIG_DIR"=>$lv_dsc_config_dir,
                    //コンフィグファイル名
                    "DSC_DATA_CONFIG_NAME"=>$lv_dsc_config_name
                    );

            ////////////////////////////////////////////////////////////////
            // REST APIコール                                             //
            ////////////////////////////////////////////////////////////////
            // #1209 2017/06/30 Append start
            $lv_dsc_responce_file  = $lv_dsc_if_info['DSC_STORAGE_PATH_LNX'] . "/" . "dsc" . "/" . "ns" . "/" . sprintf("%010s",$tgt_execution_no) . "/" . "out" . "/" . "response.txt" ;  // 並列実行 作業対象ノード状態ファイル 20170727

            $Status = 0 ;
            if( $Status != 8 )
        	{
                $rest_api_response = dsc_restapi_access( $lv_dsc_protocol,
                                                             $lv_dsc_hostname,
                                                             $lv_dsc_port,
                                                             $lv_dsc_access_key_id,
                                                             $lv_dsc_secret_access_key,
                                                             $RequestURI,
                                                             $Method,
                                                             $RequestContents );

                ////////////////////////////////////////////////////////////////
                // REST API結果判定                                           //
                ////////////////////////////////////////////////////////////////
                $restapi_err_flag = 0;
                $sql_exec_flag =  0;                   // DB更新フラグOFF設定

                $Status = RESTAPIResponsCheck($lv_dsc_responce_file,
                                              $tgt_execution_no,
                                              $rest_api_response,
                                              $restapi_err_flag);

                // REST APIの戻り値が異常の場合にログ出力
                if( $restapi_err_flag != 0 ){
                    //----------------------------------------------
                    //  REST APIの戻り値が異常は$Statusを想定外エラーにしている
                    //----------------------------------------------
                    $sql_exec_flag =  1;                  // DB更新フラグON設定

                    // 警告フラグON
                    $warning_flag = 1;

                    // 警告メッセージ出力
                    // [警告]REST APIが異常(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50019",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );

                    //----------------------------------------------
                    // REST APIの戻り値をLOG出力
                    //----------------------------------------------
                    ob_start();
                    var_dump($rest_api_response);
                    $FREE_LOG = "REST API Response\n" . ob_get_contents();
                    ob_clean();
                    require ($root_dir_path . $log_output_php );

                }
        	}

            //----------------------------------------------
            // REST API結果判定
            //
            //      5:正常終了時
            //      6:完了(異常)
            //      7:想定外エラー
            //      8:緊急停止
            //----------------------------------------------
            if( $Status == 5 ||
                $Status == 6 ||
                $Status == 7 ||
                $Status == 8 ||
                $restapi_err_flag != 0 ){
                $tmp_array_dirs = $dscdrv->getDscWorkingDirectories($vg_OrchestratorSubId_dir,$tgt_execution_no);
                $zip_data_source_dir = $tmp_array_dirs[4];
                unset($dscdrv);

                //----------------------------------------------
                // zipファイル名を作成
                //----------------------------------------------
                $zip_result_file = 'ResultData_' . str_pad( $tgt_execution_no, $intNumPadding, "0", STR_PAD_LEFT ) . '.zip';

                $tmp_zip_file_name = $zip_result_file;
                $tmp_subdir_name   = $file_subdir_zip_result;
                $tmp_exe_ins_file_dir = $vg_exe_ins_result_file_dir;

                //----------------------------------------------
                // OSコマンドでzip圧縮する
                //----------------------------------------------
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
                        // 結果ファイルディレクトリ(file_result)の作成に失敗
                        throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-59051") );
                    }
                    if( !chmod( $tmp_utn_file_dir, 0777 ) ){
                        // 事前準備を中断
                        // 結果ファイルディレクトリ(file_result)のパーミション変更に失敗
                        throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-59052") );
                    }
                }

                //----------------------------------------------
                // zipファイルを正式な置き場に移動
                //----------------------------------------------
                rename( $zip_temp_save_dir . "/" . $tmp_zip_file_name,
                        $tmp_utn_file_dir . "/" . $tmp_zip_file_name );

                //----------------------------------------------
                // zipファイルの存在を確認
                //----------------------------------------------
                if( !file_exists( $tmp_utn_file_dir . "/" . $tmp_zip_file_name ) ){
                    $prepare_err_flag = 1;

                    // 警告メッセージ出力
                    // [警告]結果ディレクトリの圧縮に失敗 (作業No:{$tgt_execution_no} 圧縮ファイル:{$tmp_zip_file_name})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-59053",array($tgt_execution_no,$tmp_zip_file_name));
                    require ($root_dir_path . $log_output_php );
                }

               // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]結果ディレクトリを圧縮([作業No.]:{$tgt_execution_no} 圧縮ファイル:{$tmp_zip_file_name})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-59101",array($tgt_execution_no,$tmp_zip_file_name));
                    require ($root_dir_path . $log_output_php );
                }
                unset($tmp_str_command);
                unset($tmp_zip_file_name);
                unset($tmp_subdir_name);
                unset($tmp_utn_file_dir);
                unset($tmp_exe_ins_file_dir);
            }

            ////////////////////////////////////////////////////////////////
            // API結果(statusによって処理を分岐                                 //
            ////////////////////////////////////////////////////////////////
            if( $Status != -1){
                // SQL(UPDATE)をEXECUTEする
                $sql_exec_flag =  1;                // DB更新フラグON設定

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
                    // [処理]完了orエラー向けのUPDATE文を生成(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50029",$tgt_execution_no);
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
                            // [処理]遅延を検出(作業No.:{$tgt_execution_no})
                            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50030",$tgt_execution_no);
                            require ($root_dir_path . $log_output_php );
                        }
                    }
                }

                if( $delay_flag == 0 &&
                    $log_level === 'DEBUG' ){
                    // [処理]遅延無し(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50031",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }

                // 遅延が発生の場合
                if( $delay_flag == 1 ){
                    // SQL(UPDATE)をEXECUTEする
                    $sql_exec_flag = 1;                      // DB更新フラグON設定

                    ////////////////////////////////////////////////////////////////
                    // ステータスを判定                                           //
                    ////////////////////////////////////////////////////////////////
                    // ステータスを「実行中(遅延)」とする
                    $Status = 4;

                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]ステータスを「実行中(遅延)」とします(作業No.:{$tgt_execution_no})
                        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50034",$tgt_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                    ////////////////////////////////////////////////////////////////////////
                    // 「C_EXECUTION_MANAGEMENT」のUPDATE文を作成(実行中 or 実行中(遅延)  //
                    ////////////////////////////////////////////////////////////////////////
                    // クローン作成
                    $cln_execution_row = $tgt_execution_row;

                    // 変数バインド準備
                    $cln_execution_row['JOURNAL_SEQ_NO']    = "";                       // 後続処理でシーケンス払い出しするので一旦ブランク
                    $cln_execution_row['STATUS_ID']         = $Status;
                    $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;

                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]実行中 or 実行中(遅延)向けのUPDATE文を生成(作業No.:{$tgt_execution_no})
                        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50036",$tgt_execution_no);
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
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
                }

                // 履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
                }

                // シーケンスをバインド準備
                $cln_execution_row['JOURNAL_SEQ_NO'] = $retArray[0];
                $intJournalSeqNo = $retArray[0];
                $cln_execution_row['FILE_RESULT'] = $zip_result_file;

                ////////////////////////////////////////////////////////////////
                // UPDATEを実行                                               //
                ////////////////////////////////////////////////////////////////
                // SQL作成＋バインド用変数準備
                //----------------------------------------------
                // 作業インスタンス情報 config2のSQl生成
                //----------------------------------------------
                $arrayConfig2_dsc = array(
                    "JOURNAL_SEQ_NO"=>"",
                    "JOURNAL_ACTION_CLASS"=>"",
                    "JOURNAL_REG_DATETIME"=>"",
                    "EXECUTION_NO"=>"",
                    "SYMPHONY_NAME"=>"",
                    "EXECUTION_USER"=>"",
                    "STATUS_ID"=>"",
                    "SYMPHONY_INSTANCE_NO"=>"",
                    "PATTERN_ID"=>"",
                    "OPERATION_NO_UAPK"=>"",
                    "I_ANS_HOST_DESIGNATE_TYPE_ID"=>"",
                    "I_PATTERN_NAME"=>"",
                    "I_OPERATION_NAME"=>"",
                    "FILE_INPUT"=>"",
                    "FILE_RESULT"=>"",
                    "I_OPERATION_NO_IDBH"=>"",
                    "RUN_MODE"=>"",
                    "TIME_BOOK"=>"DATETIME",
                    "TIME_START"=>"DATETIME",
                    "TIME_END"=>"DATETIME",
                    "I_TIME_LIMIT"=>"",
                    "I_ANS_PARALLEL_EXE"=>"",
                    "I_DSC_RETRY_TIMEOUT"=>"",
                    "DISUSE_FLAG"=>"",
                    "NOTE"=>"",
                    "LAST_UPDATE_TIMESTAMP"=>"",
                    "LAST_UPDATE_USER"=>""
                );

                switch($vg_driver_id){
                case DF_DSC_DRIVER_ID:
                    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                         "UPDATE",
                                                         "EXECUTION_NO",
                                                         $vg_exe_ins_msg_table_name,
                                                         $vg_exe_ins_msg_table_jnl_name,
                                                         $arrayConfig2_dsc,
                                                         $cln_execution_row );
                    break;
                }

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
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
                }

                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                    $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
                }

                //----------------------------------------------
                // DB UPDATE  SQL実行
                //----------------------------------------------
                $rUtn = $objQueryUtn->sqlExecute();
                if($rUtn!=true){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]UPDATE実行(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50037",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }

                //----------------------------------------------
                //  DB UPDATE  SQL実行(ジャーナル)
                //----------------------------------------------
                $rJnl = $objQueryJnl->sqlExecute();
                if($rJnl!=true){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
                }

                ////////////////////////////////////////////////////////////////
                // コミット(レコードロックを解除)                             //
                ////////////////////////////////////////////////////////////////
                $r = $objDBCA->transactionCommit();
                if (!$r){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]コミット(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50039",$tgt_execution_no);
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
                        // [処理]履歴ファイル作成の失敗を検知 (作業No:{$tgt_execution_no} FILE:{} LINE:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-59851",array($tgt_execution_no, __FILE__ , __LINE__, "00001751"));
                        require ($root_dir_path . $log_output_php );
                    }
                    else{
                        if( !mkdir( $tmp_jnl_file_dir_focus, 0777 ) ){
                            // [処理]履歴ファイル作成の失敗を検知 (作業No:{$tgt_execution_no} FILE:{} LINE:{})
                            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-59852",array($tgt_execution_no, __FILE__ , __LINE__, "00001752"));
                            require ($root_dir_path . $log_output_php );
                        }
                        else{
                            $boolCopy = copy( $tmp_utn_file_dir . "/" . $tmp_zip_file_name, $tmp_jnl_file_dir_focus . "/". $tmp_zip_file_name);
                            if( $boolCopy === false ){
                                // [処理]履歴ファイル作成の失敗を検知 (作業No:{$tgt_execution_no} FILE:{} LINE:{})
                                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-59853",array($tgt_execution_no, __FILE__ , __LINE__, "00001753"));
                                require ($root_dir_path . $log_output_php );
                            }
                            else{
                                // トレースメッセージ
                                if ( $log_level === 'DEBUG' ){
                                    // [処理]履歴ファイル作成([作業No.]:{} ファイル名:{})
                                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-59801",array($tgt_execution_no, $tmp_zip_file_name ));
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

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]トランザクション終了(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50040",$tgt_execution_no );
                require ($root_dir_path . $log_output_php );
            }
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 確認対象レコードの処理ループ終了(作業No.:{$tgt_execution_no})
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50041",$tgt_execution_no );
            require ($root_dir_path . $log_output_php );
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50042",$tgt_execution_no);
                }
                else{
                    // ロールバック
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50043");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    // ロールバックに失敗しました(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50044",$tgt_execution_no);
                }
                else{
                    // ロールバックに失敗しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50045");
                }
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                if( !empty( $tgt_execution_no ) ){
                    // トランザクション終了(作業No.({$tgt_execution_no}))
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50046",$tgt_execution_no);
                }
                else{
                    //  トランザクション終了
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50047");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    // トランザクションの終了時に異常が発生しました(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50048",$tgt_execution_no);
                }
                else{
                    // トランザクションの終了時に異常が発生しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50049");
                }
            }
            require ($root_dir_path . $log_output_php );
        }
    }
    /////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(異常)
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            // プロシージャ終了(警告)
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(0);
    }
    //******************************************************************************************
    // RESTAPI  結果判定処理
    //******************************************************************************************
    //----RedMineチケット#1248 Start 並列処理 2017/08/30
    function RESTAPIResponsCheck($in_dsc_responce_file,$in_execution_no,$in_rest_api_response,&$in_restapi_err_flag){
        global $objMTS;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        global $log_output_php;
        $chk_Status = -1;                                // 実行中
        $in_restapi_err_flag = 1;
        $restapi_check = -1;


        ////////////////////////////////////////////////////////////////
        // 結果判定                                                   //
        ////////////////////////////////////////////////////////////////
    	if( $in_rest_api_response['StatusCode'] != 200 ){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;

            // [処理]REST APIコール異常(作業No.:{$in_execution_no})
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50011",$in_execution_no);
            require ($root_dir_path . $log_output_php );

            return $chk_Status;
        }
        //----RedMineチケット#1248 End 並列処理 2017/08/30
        //-------------------------------------------------------------
        // 全機器分応答情報を確認
        // restapi_check_flag：全ホスト結果判定
        //    0:未実行
        //    1:全機器正常終了
        //    2:実行中機器あり
        //    3:全機器想定外エラー
        //-------------------------------------------------------------
        //----RedMineチケット#1248 Start 並列処理 2017/08/30
        //*************************************************************
        //  ターゲット毎のResponse.txtをチェックする
        //*************************************************************
        if( file_exists( $in_dsc_responce_file ) === false ){
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50018",$in_execution_no);
            require ($root_dir_path . $log_output_php );

            // REST APIの戻り値を出力
            $FREE_LOG = $in_rest_api_response[ALLResponsContents];
            require ($root_dir_path . $log_output_php );

            // 結果を実行中にする。
            $chk_Status = -1;
            $in_restapi_err_flag = 0;

            return $chk_Status;
        }

        //-------------------------------------------------------------
        RESTAPIAllCheck($in_dsc_responce_file , $restapi_check);  // Response.txtファイル内の各作業対象ノードの処理状態から1Movement処理の結果を判定する
        //-------------------------------------------------------------

        // result.txt情報を取り出し
        //----------------------------------------------
        // REST API 戻り値は問題ないのでフラグクリア  TS...
        //----------------------------------------------
        $in_restapi_err_flag = 0;
        
        //完了の場合
        if( $restapi_check == 1 ) // 全機器正常終了か？
        {   //完了の場合はdsc実行結果を判定
            //----------------------------------------------
            // ステータスを「完了」とする
            //----------------------------------------------
            $chk_Status = 5;
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]ステータスを「完了」とします(作業No.:{$in_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50028",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
            return $chk_Status;
        }
        //異常の場合
        //全機器想定外エラーの場合
        if( $restapi_check == 3 )  // 全機器想定外エラーか？
	    {
	        //----------------------------------------------
            // ステータスを「想定外エラー」とする
            //----------------------------------------------
            $chk_Status = 7;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]ステータスを「想定外エラー」とします(作業No.:{$in_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50025",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }
        //実行中機器あり場合
        if( $restapi_check == 2 )  // 実行中機器ありか？
        {
            //----------------------------------------------
            // 結果を実行中にする。
            //----------------------------------------------
            $chk_Status = -1;
            $in_restapi_err_flag = 0;
        }
        //その他の場合
        else{
            //----------------------------------------------
            // ステータスを「想定外エラー」とする
            //----------------------------------------------
            $in_restapi_err_flag = 1;
            $chk_Status = 7;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]ステータスを「想定外エラー」とします(作業No.:{$in_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50025",$in_execution_no);
                require ($root_dir_path . $log_output_php );
            }
        }
        return $chk_Status;
    }

    //******************************************************************************************
    // RESTAPI処理結果判定
    //
    // restapi_check_flag：全ホスト結果判定
    //    0:未実行
    //    1:全機器正常終了
    //    2:実行中機器あり
    //    3:全機器想定外エラー
    //
    //******************************************************************************************
    function RESTAPIAllCheck($dsc_responce_file , &$in_restapi_check ){
        $sucdata    = 'SUCCEED';
        $rundata    = 'RUNNING';
        $faidata    = 'FAILED';
        $pendingdata = 'PENDING';
        $arraycode  = array();               // Response情報
        $aryresp = file($dsc_responce_file);
        $devcnt = (int)$aryresp[0];
        $check = 0;                          // 判定結果初期化
        $suc = 0;
        $run = 0;
        $fai = 0;
    	$i = 1;
        for ( $n = 0 ; $n < $devcnt ; $n ++ ){
            $pos = strpos($aryresp[$i], ',');
        	$code = substr($aryresp[$i],$pos+1,7);
            if ( $code == $sucdata){
                $suc ++ ;
            }
            else if ( $code == $rundata){
                $run ++ ;
            } else if ( $code == $pendingdata ) {
                $run ++ ;
            } else{
                $fai ++ ;
            }
            $i ++ ;
        }

        if ( $suc == $devcnt ){
            $check = 1;                        // 全機器正常終了
        }
        else if ( $run != 0){
            $check = 2;                        // 実行中機器あり
        }
        else if ( $fai == ($devcnt - $suc )){
            $check = 3;                        // 全機器想定外エラー
        }

        $in_restapi_check = $check;
        return ;
    }
    //----------------------------------------------
    // 作業インスタンス情報 configのSQl生成
    //----------------------------------------------
     function getDBDscConfigList($objDBCA,$in_pattern_id,&$ina_child_resources){
        $sql = "SELECT \n" .
               "TBL_1.LINK_ID, \n" .
               "TBL_1.RESOURCE_MATTER_ID, \n" .
               "TBL_1.INCLUDE_SEQ, \n" .
               "TBL_2.RESOURCE_MATTER_FILE, \n" .
               "TBL_2.RESOURCE_MATTER_NAME, \n" .
               "TBL_2.DISUSE_FLAG \n" .
               "FROM \n" .
               "  ( \n" .
               "    SELECT \n" .
               "      TBL3.LINK_ID, \n" .
               "      TBL3.PATTERN_ID, \n" .
               "      TBL3.RESOURCE_MATTER_ID, \n" .
               "      TBL3.INCLUDE_SEQ \n" .
               "    FROM \n" .
               "      B_DSC_PATTERN_LINK TBL3 \n" .
               "    WHERE \n" .
               "      TBL3.PATTERN_ID  = :PATTERN_ID AND \n" .
               "      TBL3.DISUSE_FLAG = '0' \n" .
               "  )TBL_1 \n" .
               "LEFT OUTER JOIN B_DSC_RESOURCE TBL_2 ON \n" .
               "      ( TBL_1.RESOURCE_MATTER_ID = TBL_2.RESOURCE_MATTER_ID) \n" .
               "ORDER BY TBL_1.INCLUDE_SEQ; \n";

        $objQuery = $objDBCA->sqlPrepare($sql);
        $objQuery->sqlBind( array('PATTERN_ID'=>$in_pattern_id));

        $r = $objQuery->sqlExecute();
        $configrow = $objQuery->resultFetch();
        $ina_child_resources = $configrow;
        // DBアクセス事後処理
        unset($objQuery);

        return true;
    }

 ?>
