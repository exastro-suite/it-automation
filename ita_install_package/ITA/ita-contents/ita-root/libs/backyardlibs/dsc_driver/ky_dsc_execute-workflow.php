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
    //    Windows PowerShell Desired State Configuration 処理管理
    //
    // インタフェース情報(HTTP接続情報)
    //       $lv_dsc_storage_path_lnx                     ITAデータストレージ
    //       $lv_dsc_storage_path_dsc                     DSCデータストレージ
    //       $lv_dsc_protocol                             プロトコル
    //       $lv_dsc_hostname                             DSCホスト名
    //       $lv_dsc_port                                 ポート
    //       $lv_dsc_access_key_id                        アクセスキー
    //       $lv_dsc_secret_access_key                    認証キー
    //       $RequestURI                                  HTTPコールURI (/restapi/dsc_driver/CollectCommandExecute.php)
    //       $Method                                      メッソド(POST)
    //       $RequestContents()                           コンテンツ配列
    // コンテンツ一覧
    //       RequestContents[DSC_PROCESS]                   DSC処理コード（'1'；実行 '2'：監視）
    //       RequestContents[DATA_RELAY_STORAGE_TRUNK]      データリレイパス
    //       RequestContents[ORCHESTRATOR_SUB_ID]           オーケストレータ識別子
    //       RequestContents[EXE_NO]                        作業実行ID
    //       RequestContents[DSC_DATA_RELAY_STORAGE]        DSCデータストレージ
    //       RequestContents[DSC_DATA_TARGET_HOSTNAME]      ターゲットホスト名
    //       RequestContents[DSC_DATA_TARGET_IP]            ターゲットＩＰ
    //       RequestContents[DSC_DATA_TARGET_USERNAME]      接続ユーザ名
    //       RequestContents[DSC_DATA_TARGET_PASSWORD]      接続パスワード
    //       RequestContents[DSC_DATA_CONFIG_DIR]           コンフィグレーションパス名
    //       RequestContents[DSC_DATA_CONFIG_NAME]          コンフィグレーション名
    //
    /////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////

    $log_output_php                   = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php                 = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                   = '/libs/commonlibs/common_db_connect.php';
    $dsc_restapi_php                  = '/libs/commonlibs/common_dsc_restapi.php';
    $dsc_create_files_php             = '/libs/backyardlibs/dsc_driver/CreateDscExecFiles.php';
    $RequestURI                       = "/restapi/dsc_driver/CollectCommandExecute.php";          // REST API URI

    ////////////////////////////////
    // REST API ResultCode 定義   //
    ////////////////////////////////
    define("DSC_SUCCESS"               ,"0");
    define("DCS_ERR_HTTP_REQ"        ,"1000");  // HTTPパラメータ異常
    define("DSC_ERR_DSC_DIR"         ,"1001");  // DSCインストールディレクトリへの移動に失敗
    define("DSC_ERR_HTTP_HEDER"      ,"1002");  // HTTPヘッダーに必要な情報がない
    define("DSC_ERR_AUTH"            ,"1003");  // アクセスキー認証エラー
    define("DSC_ERR_DSC_CONF"        ,"1004");  // DSC コンフィグレーション(実行)エラー
    define("DSC_ERR_DSC_TEST"        ,"1005");  // DSC テスト(確認)エラー
    define("DSC_ERR_TAR_DEL"         ,"2000");  // Collect Commandで作成したZIPファイルの削除失敗時のrmコマンドの戻り値への加算値

    $vg_resource_user_var_name = "__loginuser__";

    //----------------------------------------------
    // ドライバチェックして実行IDを設定
    //----------------------------------------------
    switch($vg_driver_id){
    case DF_DSC_DRIVER_ID:               // ID = "D"
        $db_access_user_id = -100802;    // DSC作業実行プロシージャ
        break;
    }

    $Method                           = 'POST';
    $rh_abort_file_name               = 'RHABORT';
    $intNumPadding                    = 10;
    $exe_code                         = '1';        // 実行プロセス

    $file_subdir_zip_input            = 'FILE_INPUT';

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag                     = 0;          // 警告フラグ(1：警告発生)
    $error_flag                       = 0;          // 異常フラグ(1：異常発生)
    $tgt_execution_no_array           = array();    // 処理対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_array_without_2 = array();    // 処理対象から準備中を除くEXECUTION_NOのリストを格納

    $tgt_run_mode_array               = array();    // 処理対象のドライランモードのリストを格納
    $tgt_run_mode_no_array_without_2  = array();    // 処理対象から準備中を除くドライランモードのリストを格納

    $tgt_exec_count_array             = array();    // 処理対象の並列実行数のリストを格納
    $tgt_exec_count_array_without_2   = array();    // 処理対象から準備中を除く並列実行数のリストを格納

    $tgt_operation_id_array           = array();    // 処理対象のOPERATION_NO_UAPKのリストを格納
    $tgt_row_array                    = array();    // 処理対象のレコードまるごと格納
    $tgt_row_array_without_2          = array();    // 処理対象から準備中を除くレコードまるごと格納
    $num_of_tgt_execution_no          = 0;          // 処理対象のEXECUTION_NOの個数を格納

    $tgt_config_dir                   = "";          // 処理対象のEXECUTION_NOの個数を格納

    $tgt_Contents                     = array();    // REST API Contents領域
    $aryDscWorkingDir                 = array();    // フォルダパス格納領域

    $tgt_symphony_instance_no_array   = array();    // 処理対象のSymphonyインス>タンス番号のリストを格納

    ////////////////////////////////
    // REST API接続function定義   //
    ////////////////////////////////
    require_once ($root_dir_path . $dsc_restapi_php );


    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////

    // トランザクションフラグ(初期値はfalse)
    $transaction_flag = false;

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し          //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level == 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                    //
        ////////////////////////////////
        //----------------------------------------------
        // DB接続を行う
        //----------------------------------------------
        require ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level == 'DEBUG' ){
            //----------------------------------------------
            // DBコネクト完了
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        /////////////////////////////////////////////////////////
        // DSCインタフェース情報を取得                                //
        ////////////////////////////////////////////////////////
        //----------------------------------------------
        // SQL作成
        //----------------------------------------------
        $sql = "SELECT *
                FROM   $vg_info_table_name
                WHERE  DISUSE_FLAG = '0' ";

        // SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        if( $objQuery->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;

            // データベースアクセス異常 (FILE:{} LINE:{} 詳細:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-56200",array(__FILE__,__LINE__,$objQuery->getLastError()));
            require ($root_dir_path . $log_output_php );

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000100")) );
        }

        //----------------------------------------------
    	// SQL実行(全レコード)
        //----------------------------------------------
        $r = $objQuery->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // データベースアクセス異常 (FILE:{} LINE:{} 詳細:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-56200",array(__FILE__,__LINE__,$objQuery->getLastError()));
            require ($root_dir_path . $log_output_php );

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000200")) );
        }

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

            // DSCインタフェース情報レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-56000") );
        }
        //----------------------------------------------
        // 「DSCインタフェース情報」が重複登録されている場合も以降の処理をスキップ
        // 常駐は継続させたいので異常フラグは立てない。
        //----------------------------------------------
        else if( $num_of_rows > 1 ){
            // 異常フラグON
            $warning_flag = 1;

            // DSCインタフェース情報レコードが単一行でない
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
        $lv_dsc_secret_access_key = ky_decrypt($lv_dsc_if_info['DSC_SECRET_ACCESS_KEY']);
        $lv_dsc_storage_path_dsc  = $lv_dsc_if_info['DSC_STORAGE_PATH_DSC'];

        ////////////////////////////////////////////////////////////////////////////////////////////////
        // 作業インスタンステーブルから処理対象レコードの一意キーを取得(レコードロック)
        ////////////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;

            // 異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000400")) );
        }

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // トランザクション開始(ステータスを「準備中」に変更する処理)
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51001");
            require ($root_dir_path . $log_output_php );
        }

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

        //----------------------------------------------
    	// 作業インスタンス情報のステータス条件設定(1)か(9)
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

        //----------------------------------------------
    	// DRIVER IDで分岐
        //----------------------------------------------
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

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000500")) );
        }

        //----------------------------------------------
        // SQL実行
        //----------------------------------------------
        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000600")) );
        }
        //----------------------------------------------
    	// 作業インスタンス情報を読込み
        //----------------------------------------------
        while ( $row = $objQueryUtn->resultFetch() ){
            // fetch行の情報をarrayに追加
            array_push( $tgt_execution_no_array, $row['EXECUTION_NO'] );

            if(strlen($row['I_ANS_PARALLEL_EXE']) == 0){
                $tgt_exec_count_array[$row['EXECUTION_NO']] = '0';
            }
            else{
                $tgt_exec_count_array[$row['EXECUTION_NO']] = $row['I_ANS_PARALLEL_EXE'];
            }

            array_push( $tgt_row_array, $row );         //作業インスタンス情報をレコードまるごと格納

            // symphonyインスタンス番号を退避
            $tgt_symphony_instance_no_array[$row['EXECUTION_NO']] = $row['SYMPHONY_INSTANCE_NO'];

            //----------------------------------------------
            // ステータス＝準備中（２）チェック
            //----------------------------------------------
            if( $row['STATUS_ID'] != 2 ){
                // fetch行の情報をarrayに追加
                array_push( $tgt_execution_no_array_without_2, $row['EXECUTION_NO'] );

                if(strlen($row['I_ANS_PARALLEL_EXE']) == 0){
                    $tgt_exec_count_array_without_2[$row['EXECUTION_NO']] = '0';
                }
                else{
                    $tgt_exec_count_array_without_2[$row['EXECUTION_NO']] = $row['I_ANS_PARALLEL_EXE'];
                }

                array_push( $tgt_row_array_without_2, $row );        //処理対象から準備中を除くレコードまるごと格納
            }
        }
        // fetch行数を取得
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

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000700")) );
            }

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // トランザクション終了(ステータスを「準備中」に変更する処理)
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51002");
                require ($root_dir_path . $log_output_php );
            }

            // 例外処理へ(例外ではないが・・・) 処理対象レコード無し
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-STD-51003") );
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコード検出(EXECUTION_NO:{$tgt_execution_no_array})
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51004",implode(",", $tgt_execution_no_array ));
            require ($root_dir_path . $log_output_php );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // ステータス「準備中」へのUPDATEループ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51005");
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
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000800")) );
        }

        ////////////////////////////////////////////////////////////////
        // 処理対象から準備中ステータスを除いたEXECUTION_NOだけループ //
        ////////////////////////////////////////////////////////////////
        foreach( $tgt_row_array_without_2 as $tgt_row )      // 処理対象レコードから準備中(2)以外を抽出
        {
            ////////////////////////////////////////////////////////////////////////////////////////////////
            // 「C_EXECUTION_MANAGEMENT」の処理対象レコードのステータスを準備中にUPDATE                   //
            ////////////////////////////////////////////////////////////////////////////////////////////////
            $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00000900")) );
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            //----------------------------------------------
            $tgt_row["STATUS_ID"]        = 2;                           // 準備中ステータス（２）設定
            //----------------------------------------------
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;

            //----------------------------------------------
            // DRIVER IDで分岐
            //----------------------------------------------
            switch($vg_driver_id){
            case DF_DSC_DRIVER_ID:
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "UPDATE",
                                                     "EXECUTION_NO",
                                                     $vg_exe_ins_msg_table_name,
                                                     $vg_exe_ins_msg_table_jnl_name,
                                                     $arrayConfig_dsc,
                                                     $tgt_row,
                                                     $temp_array );
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
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
            }

            //----------------------------------------------
            // SQL実行
            //----------------------------------------------
            $rUtn = $objQueryUtn->sqlExecute();               // 対象レコードを書き込み
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
            }

            //----------------------------------------------
        	// SQL実行(JNL)
            //----------------------------------------------
            $rJnl = $objQueryJnl->sqlExecute();               // 対象レコードを書き込み
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]UPDATE実行(ステータス＝「準備中」)(作業No.:{$tgt_row["EXECUTION_NO"]})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51006",$tgt_row["EXECUTION_NO"]);
                require ($root_dir_path . $log_output_php );
            }
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // ステータス「準備中」へのUPDATEループ終了
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51007");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                                        //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // コミット(ステータスを「準備中」に変更する処理)
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51008");
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
            // トランザクション終了(ステータスを「準備中」に変更する処理)
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51009");
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
            // 処理対象レコードの処理ループ開始
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51010");
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
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001500")) );
            }

            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = true;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]トランザクション開始(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51011",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            $temp_array = array('WHERE'=>" EXECUTION_NO = :EXECUTION_NO AND DISUSE_FLAG = '0' AND STATUS_ID = 2" );
            //----------------------------------------------
            // DRIVER IDで分岐
            //----------------------------------------------
            switch($vg_driver_id){
            case DF_DSC_DRIVER_ID:
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
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001600")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }

            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
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
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51012",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // ロールバック(念のため)
                if( $objDBCA->transactionRollBack()===false ){
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }

                // ロールバック(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51013",$tgt_execution_no);

                require ($root_dir_path . $log_output_php );

                //----------------------------------------------
                // トランザクション終了
                //----------------------------------------------
                $objDBCA->transactionExit();

                // トランザクションフラグ(初期値はfalse)
                $transaction_flag = false;

                // トランザクション終了(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51014",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );

                // 次レコードの処理へ
                continue;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]レコードロック(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51015",$tgt_execution_no);
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
                require ($log_output_php );
                throw new Exception("OperationList update error.");
            }
            unset($dbaobj);


            //////////////////////////////////////////////////////////////////
            // データベースからDSCで実行する情報取得                                  //
            //////////////////////////////////////////////////////////////////
            // クラス生成

            //----------------------------------------------
            // CreateDscExecFiles.phpインクルード
            //----------------------------------------------
            require_once ($root_dir_path . $dsc_create_files_php);

            //----------------------------------------------
            // DRIVER IDで分岐  //
            //----------------------------------------------
        	switch($vg_driver_id){
            case DF_DSC_DRIVER_ID:
                $winrm_flg = "1";                   // Windowsフラグ設定
                break;
            }

            //----------------------------------------------
            // インスタンスオブジェクト生成
            //----------------------------------------------
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

            $dscdrv->setDsc_storage_path($lv_dsc_storage_path_dsc);
            
            $retry_timeout = $tgt_execution_row["I_DSC_RETRY_TIMEOUT"];
            if( $retry_timeout === NULL ){
                $retry_timeout = 0;
            }

            // DBからDSCで実行する情報取得しファイル作成
	        $ret = CreateDscExecFilesfunction($vg_driver_id,
                                                  $dscdrv,
                                                  $tgt_execution_no,
                                                  $tgt_symphony_instance_no_array[$tgt_execution_no],
                                                  $tgt_execution_row["STATUS_ID"],
                                                  $tgt_execution_row["PATTERN_ID"],
                                                  $tgt_execution_row["OPERATION_NO_UAPK"],
                                                  $tgt_execution_row["I_ANS_HOST_DESIGNATE_TYPE_ID"],
                                                  $tgt_execution_row["I_DSC_RETRY_TIMEOUT"],
                                                  $winrm_flg,
                                                  $vg_OrchestratorSubId_dir,
                                                  $root_dir_path,
                                                  $log_output_php,
                                                  $aryDscWorkingDir,
                                                  $tgt_Contents);

            //----------------------------------------------
            // Configファイルパス作成
            //----------------------------------------------
            $tmp_array_dirs = $dscdrv->getDscWorkingDirectories($vg_OrchestratorSubId_dir,$tgt_execution_no);

            $zip_data_source_dir = $tmp_array_dirs[3];
            unset($dscdrv);
            if($ret === false){
                $prepare_err_flag = 1;
            }
            else{
       	        if( count( glob( $zip_data_source_dir . "/"."*" ) ) > 0 ){
                    //----------------------------------------------
                    // ZIPファイルを作成する
                    //----------------------------------------------
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
                            // 入力ファイルディレクトリ(file_in)の作成に失敗
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-58051") );
                        }
                        if( !chmod( $tmp_utn_file_dir, 0777 ) ){
                            // 事前準備を中断
                            // 入力ファイルディレクトリ(file_in)のパーミション変更に失敗
                            throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-58052") );
                        }
                    }

                    // zipファイルを正式な置き場に移動
                    rename( $zip_temp_save_dir . "/" . $tmp_zip_file_name,
                            $tmp_utn_file_dir . "/" . $tmp_zip_file_name );

                    // zipファイルの存在を確認
                    if( !file_exists( $tmp_utn_file_dir . "/" . $tmp_zip_file_name ) ){
                        $prepare_err_flag = 1;

                        // 警告メッセージ出力
                        // [警告]入力ディレクトリの圧縮に失敗(作業No.:{} 圧縮ファイル:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-58053",array($tgt_execution_no,$tmp_zip_file_name));
                        require ($root_dir_path . $log_output_php );
                    }

                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]入力ディレクトリを圧縮([作業No.]:{} 圧縮ファイル:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-58101",array($tgt_execution_no,$tmp_zip_file_name));
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
            if( $prepare_err_flag === 0 ){
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]REST APIコール開始(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51066",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
                //--------------------------------------------------------------
                // REST API コンテンツ
                //--------------------------------------------------------------
                $lv_dsc_process_id = $exe_code;
                $lv_dsc_target_hostname  = $tgt_Contents[4];
                $lv_dsc_target_ip  = $tgt_Contents[5];
                $lv_dsc_config_name = $tgt_Contents[9];                     // コンフィグ名設定  
                $tgt_Contents[3] = $lv_dsc_if_info['DSC_STORAGE_PATH_DSC']; // 画面変更に伴う修正(if情報) 
                $str =  $tgt_Contents[8];                                   // 画面変更に伴う修正(if情報) 
                $rstr = str_replace("/", "\\", $str);                       // Windows separator対応
                $lv_dsc_config_dir = $tgt_Contents[3] . $tmp_array_dirs[6] . "\\" . $tgt_Contents[8];

                ////////////////////////////////////////////////////////////////
                // REST APIコール                                               //
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
                    //----------------------------------------------
                    // REST APIコール
                    //----------------------------------------------
                    $rest_api_response = dsc_restapi_access( $lv_dsc_protocol,
                                                         $lv_dsc_hostname,
                                                         $lv_dsc_port,
                                                         $lv_dsc_access_key_id,
                                                         $lv_dsc_secret_access_key,
                                                         $RequestURI,
                                                         $Method,
                                                         $RequestContents );


                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]REST APIコール終了(作業No.:{$tgt_execution_no} HTTPレスポンスコード:{$rest_api_response['StatusCode']})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51067",array($tgt_execution_no,$rest_api_response['StatusCode']));
                    require ($root_dir_path . $log_output_php );
                }

                ////////////////////////////////////////////////////////////////
                // 結果判定                                                   //
                ////////////////////////////////////////////////////////////////
                // REST API ResponseResultCode取り出し
                $ResponseResultCode = '';
                if( $rest_api_response['StatusCode'] != 200 ){
                    // REST APIでの異常フラグをON
                    $restapi_err_flag = 1;

                    // 異常メッセージ
                    // [処理]REST APIコール異常(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51068",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理](作業No.:{} REST APIエラーフラグ:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51071",array($tgt_execution_no,$restapi_err_flag));
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
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003300")) );
            }

            // 履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable($vg_exe_ins_msg_table_jnl_seq, 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003400")) );
            }

            ////////////////////////////////////////////////////////////////
            // 「C_EXECUTION_MANAGEMENT」をUPDATE                           //
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
                    // [処理]正常向けのUPDATE文を生成(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51072",$tgt_execution_no);
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
                //----------------------------------------------
                $cln_execution_row['STATUS_ID']         = "7";    // 想定外エラー
                //----------------------------------------------
                $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;

                $cln_execution_row['FILE_INPUT']        = $zip_input_file;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]異常向けのUPDATE文を生成(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51074",$tgt_execution_no);
                    require ($root_dir_path . $log_output_php );
                }
            }
            $intJournalSeqNo = $retArray[0];

            // SQL作成＋バインド用変数準備
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
                "DISUSE_FLAG"=>"",
                "NOTE"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );

            //----------------------------------------------
            // DRIVER IDで分岐
            //----------------------------------------------
            switch($vg_driver_id){
            case DF_DSC_DRIVER_ID:
                //----------------------------------------------
                // DB UPDATE SQL作成
                //----------------------------------------------
                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "UPDATE",
                                                     "EXECUTION_NO",
                                                     $vg_exe_ins_msg_table_name,
                                                     $vg_exe_ins_msg_table_jnl_name,
                                                     $arrayConfig2_dsc,
                                                     $cln_execution_row );
                break;
            }

            $sqlUtnBody   = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $sqlJnlBody   = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn  = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl  = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003500")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003600")) );
            }

            //----------------------------------------------
            // DB UPDATE  SQL実行
            //----------------------------------------------
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003700")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]UPDATE実行(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51075",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            //----------------------------------------------
            // DB UPDATE  SQL実行(ジャーナル)
            //----------------------------------------------
            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003800")) );
            }

            ////////////////////////////////////////////////////////////////
            // コミット(レコードロックを解除)                             //
            ////////////////////////////////////////////////////////////////
            $r = $objDBCA->transactionCommit();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00003900")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]コミット(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51077",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            //
            if( $zip_input_file != "" ){
                $tmp_utn_file_dir  = $strToFUC01FilePerRIValueCurDir;
                $tmp_zip_file_name = $zip_input_file;

                $tmp_jnl_file_dir_trunk = $tmp_utn_file_dir . "/old";
                $tmp_jnl_file_dir_focus = $tmp_jnl_file_dir_trunk . "/" . str_pad( $intJournalSeqNo, $intNumPadding, "0", STR_PAD_LEFT );

                // 履歴フォルダへコピー
                if( !mkdir( $tmp_jnl_file_dir_trunk, 0777 ) ){
                    // [処理]ファイル作成の失敗を検知(作業No.:{} FILE:{} LINE:{} ETC-Code:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50050",array($tgt_execution_no, __FILE__ , __LINE__, "00001751"));
                    require ($root_dir_path . $log_output_php );
                }
                else{
                    if( !mkdir( $tmp_jnl_file_dir_focus, 0777 ) ){
                        // [処理]ファイル作成の失敗を検知(作業No.:{} FILE:{} LINE:{} ETC-Code:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50050",array($tgt_execution_no, __FILE__ , __LINE__, "00001752"));
                        require ($root_dir_path . $log_output_php );
                    }
                    else{
                        $boolCopy = copy( $tmp_utn_file_dir . "/" . $tmp_zip_file_name, $tmp_jnl_file_dir_focus . "/". $tmp_zip_file_name);
                        if( $boolCopy === false ){
                            // [処理]ファイル作成の失敗を検知(作業No.:{} FILE:{} LINE:{} ETC-Code:{})
                            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50050",array($tgt_execution_no, __FILE__ , __LINE__, "00001753"));
                            require ($root_dir_path . $log_output_php );
                        }
                        else{
                            // トレースメッセージ
                            if ( $log_level === 'DEBUG' ){
                                // [処理]履歴ファイル作成(作業No.:{$tgt_execution_no} ファイル名:{$tmp_zip_file_name})
                                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-50051",array($tgt_execution_no, $tmp_zip_file_name));
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
                // [処理]トランザクション終了(作業No.:{$tgt_execution_no})
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51078",$tgt_execution_no);
                require ($root_dir_path . $log_output_php );
            }

        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // 処理対象レコードの処理ループ終了
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51080");
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
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51081",$tgt_execution_no);
                }
                else{
                    // ロールバック
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51082");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    // ロールバックに失敗しました(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51083",$tgt_execution_no);
                }
                else{
                    // ロールバックに失敗しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51084");
                }
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                if( !empty( $tgt_execution_no ) ){
                    // トランザクション終了(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51085",$tgt_execution_no);
                }
                else{
                    // トランザクション終了
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-STD-51086");
                }
            }
            else{
                if( !empty( $tgt_execution_no ) ){
                    // トランザクションの終了時に異常が発生しました(作業No.:{$tgt_execution_no})
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51087",$tgt_execution_no);
                }
                else{
                    // トランザクションの終了時に異常が発生しました
                    $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-51089");
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
            // プロシージャ終了(正常)
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(0);
    }
    //////////////////////////////////////////////////////////////////
    // データベースからDSCで実行する情報取得
    //////////////////////////////////////////////////////////////////
	function CreateDscExecFilesfunction($in_driver_id,
                                            $in_dscdrv,
                                            $in_execution_no,
                                            $symphony_instance_no,
                                            $in_status_id,
                                            $in_pattern_id,
                                            $in_operation_id,
                                            $in_hostaddres_type,
                                            $in_retry_timeout,
                                            $in_winrm_id,
                                            $in_OrchestratorSubId_dir,
                                            $in_root_dir_path,
                                            $in_log_output_php,
                                            &$aryDscWorkingDir,
                                            &$DscContents){
        global $objMTS;
        global $log_level;
        global $log_output_dir;
        global $log_file_prefix;
        global $root_dir_path;
        global $log_output_php;

        $hostlist         = array();
        $hostprotocollist = array();
        $hostostypelist   = array();
        $resourcelist     = array();
        $resourceName     = array();
        $dialogfilelist   = array();
        $host_vars        = array();
        $host_child_vars      = array();
        $DB_child_vars_master = array();
        $hostinfolist      = array();
        
        $powershellfile   = array();
        $paramfile        = array();
        $importfile       = array();
        $configdatafile   = array();
        $configdataName   = array();
        $cmpoptiontfile   = array();
        // 2018.03.22 Add End

        //----------------------------------------------------------------------
        //データリレイストレージフォルダ作成(RESTAPI関連ファイル配置)
        //----------------------------------------------------------------------
        $ret = $in_dscdrv->CreateDscWorkingDir($in_OrchestratorSubId_dir,
                                                   $in_execution_no,
                                                   $in_status_id,
                                                   $in_hostaddres_type,
                                                   $in_winrm_id,
                                                   $aryDscWorkingDir,
                                                   $symphony_instance_no
                                                   );
        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010004"));
            require ($in_root_dir_path . $in_log_output_php );

            return false;
        }
        ///////////////////////////////////////////////////////////////////////////////////////
        // データベースから作業対象ホストの情報を取得（接続時認証情報）
        // $hostlist:              ホスト一覧返却配列
        //                         [管理システム項番]=[ホスト名(IP)]
        // $hostprotcollist:       ホスト毎プロトコル一覧返却配列
        //                         [ホスト名(IP)][ホスト名][PROTOCOL_NAME][LOGIN_USER]=LOGIN_PASSWD
        // $hostostypelist:        ホスト毎OS種別一覧返却配列
        //                         [ホスト名(IP)]=$row[OS種別]
        // 
        // 既存のデータが重なるが、今後の開発はこの変数を使用する。
        // $hostinfolist:          機器一覧ホスト情報配列
        //                         [ホスト名(IP)]= HOSTNAME=>''             ホスト名
        //                                       PROTOCOL_ID=>''          接続プロトコル
        //                                       LOGIN_USER=>''           ログインユーザー名
        //                                       LOGIN_PW_HOLD_FLAG=>''   パスワード管理フラグ
        //                                                                1:管理(●)   0:未管理
        //                                       LOGIN_PW=>''             パスワード
        //                                                                パスワード管理が1の場合のみ有効
        //                                       LOGIN_AUTH_TYPE=>''      認証方式
        //                                                                2:パスワード認証 1:鍵認証
        //                                       WINRM_PORT=>''           WinRM接続プロトコル
        //                                       OS_TYPE_ID=>''           OS種別
        ///////////////////////////////////////////////////////////////////////////////////////
        //----------------------------------------------------------------------
        //DSCで実行するHOST情報をデータベースより取得する。
        //----------------------------------------------------------------------
        $ret = $in_dscdrv->getDBHostList($in_pattern_id,
                                         $in_operation_id,
                                         $hostlist,
                                         $hostprotocollist,
                                         $hostostypelist,
                                         $hostinfolist,
                                         $in_retry_timeout);
        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010000"));
            require ($in_root_dir_path . $in_log_output_php);

            return false;
        }

        switch($in_driver_id){
        case DF_DSC_DRIVER_ID:
            /////////////////////////////////////////////////////////////////////////////
            // データベースからリソースファイル（Config素材）を取得
            //   $resourcelist:     リソースファイル返却配列
            //                      [INCLUDE順序][素材管理Pkey]=>リソースファイル
            /////////////////////////////////////////////////////////////////////////////
            $ret = $in_dscdrv->getDBDscResourceList($in_pattern_id,$resourcelist,$resourceName);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );

                return false;
            }

            foreach( $resourcelist as $config ){
                $conkey = key($config);               // LOGINN USER NAME
                $con = $config[$conkey];               // LOGINN USER NAME
            }
            foreach( $resourceName as $conname ){
                $namekey = key($conname);               // LOGINN USER NAME
                $configname = $conname[$namekey];       // LOGINN USER NAME
            }

            // Power Shell File
            $ret = $in_dscdrv->getDBDscPowerShellFileData($in_pattern_id,$powershellfile);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );

                return false;
            }

            foreach( $powershellfile as $id => $powershellfiledata ){
                $pwsfileid = $id;
                $pwsfile = $powershellfiledata;
            }

            // Param File
            $ret = $in_dscdrv->getDBDscParamFileData($in_pattern_id,$paramfile);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );

                return false;
            }

            foreach( $paramfile as $id => $paramfiledata ){
                $prmid = $id;
                $prmfile = $paramfiledata;
            }

            // Import File
            $ret = $in_dscdrv->getDBDscImportFileData($in_pattern_id,$importfile);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );

                return false;
            }

            foreach( $importfile as $id => $importfiledata ){
                $impid = $id;
                $impfile = $importfiledata;
            }

            // コンフィグデータ File
            $ret = $in_dscdrv->getDBDscConfigDataFileData($in_pattern_id,$configdatafile,$configdataName);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );

                return false;
            }

            foreach( $configdatafile as $id => $configdatafile ){
                $cnfdataid = $id;
                $cnfdatafile = $configdatafile;
            }

            foreach( $configdataName as $id => $condataname ){
                $condatanamekey = $id;
                $configdataname = $condataname;
            }

            // コンパイルオプション File
            $ret = $in_dscdrv->getDBDscCmpOptionFileData($in_pattern_id,$cmpoptiontfile);
            if($ret <> true){
                // 例外処理へ
                $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010001"));
                require ($in_root_dir_path . $in_log_output_php );

                return false;
            }

            foreach( $cmpoptiontfile as $id => $cmpoptiontfiledata ){
                $cmpid = $id;
                $cmpfile = $cmpoptiontfiledata;
            }

            //=========================================================
            $ipaddr = "";                                                //
            $DscContents[0] = $aryDscWorkingDir[5];                      // DATA_RELAY_PASS
            $DscContents[1] = $in_OrchestratorSubId_dir;                 // ORCHESTRATOR_ID
            $DscContents[2] = $in_execution_no;                          // EXE_NO
            $DscContents[3] = $aryDscWorkingDir[5];                      // DSC_DATA_RELAY
            $DscContents[4] = "";                                        // TARGET_HOST_NAME
            $DscContents[5] = $ipaddr;                                   // TARGET_IP_ADDRESS
            $DscContents[6] = "";                                        // TARGET_USER_NAME #1209 画面変更に伴う修正  (if情報・機器一覧) 20170630
            $DscContents[7] = "";                                        // TARGET_PASSWORD  #1209 画面変更に伴う修正  (if情報・機器一覧) 20170630
            $DscContents[8] = $con;                                      // CONFIG_PASS 0525修正
            $DscContents[9] = $configname;                               // CONFIG_NAME 0802修正
            $DscContents[10] = $pwsfileid;                               // POWERSHELL_FILE_ID
            $DscContents[11] = $pwsfile;                                 // POWERSHELL_FILE
            $DscContents[12] = $prmid;                                   // PARAM_FILE_ID
            $DscContents[13] = $prmfile;                                 // PARAM_FILE
            $DscContents[14] = $impid;                                   // IMPORT_FILE_ID
            $DscContents[15] = $impfile;                                 // IMPORT_FILE
            $DscContents[16] = $cnfdataid;                               // CONFIGDATA_FILE_ID
            $DscContents[17] = $cnfdatafile;                             // CONFIGDATA_FILE
            $DscContents[18] = $configdataname;                          // CONFIGDATA_NAME
            $DscContents[19] = $cmpid;                                   // CMPOPTION_FILE_ID
            $DscContents[20] = $cmpfile;                                 // CMPOPTION_FILE
            $DscContents[21] = $in_retry_timeout;                        // 2018.05.11 Add
            //=========================================================

            break;
        }

        /////////////////////////////////////////////////////////////////////////////
        // データベースから変数情報を取得する。
        //   $host_vars:        変数一覧返却配列
        //                      [ホスト名(IP)][ 変数名 ]=>具体値
        //
        //   $host_child_vars   配列変数一覧返却配列(変数一覧に配列変数含む)
        //                      [ホスト名(IP)][ 変数名 ][列順序][メンバー変数]=[具体値]
        //   $DB_child_vars_master:
        //                      メンバー変数マスタの配列変数のメンバー変数リスト返却
        //                      [ 変数名 ][メンバー変数名]=0
        //
        /////////////////////////////////////////////////////////////////////////////
        $ret = $in_dscdrv->getDBVarList($in_pattern_id,
                                        $in_operation_id,
                                        $host_vars,
                                        $host_child_vars,
                                        $DB_child_vars_master);

        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010003"));
            require ($in_root_dir_path . $in_log_output_php );

            return false;
        }
        $ret = $in_dscdrv->addSystemvars($host_vars,$hostprotocollist);

        //----------------------------------------------
        // DSCで実行するファイル作成(ホスト定義ファイル,リソースファイル,Configファイル等)
        //----------------------------------------------
        $ret = $in_dscdrv->CreateDscWorkingFiles($hostlist,
                                                     $host_vars,
                                                     $resourcelist,
                                                     $hostprotocollist,
                                                     $hostinfolist,
                                                     $host_child_vars,
                                                     $DB_child_vars_master,
                                                     $DscContents);

        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITADSCH-ERR-50003",array(__FILE__,__LINE__,"00010005"));
            require ($in_root_dir_path . $in_log_output_php );

            return false;
        }
        return true;
    }

?>
