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
    //      作業実行ファイル
    //      openstack実行監視(対象の作業NOを持つレコードのステータスを２（準備中）とし、開始時間を記載する)
    //      1:対象行をロック
    //      2:対象行のステータスをPREPARE（準備中）に変更し、開始時間を記録する。
    // 　　　　 続く処理は作業状態確認ファイルで行う（openstackにPOSTする～）
    //      exception時のロジックは若干長く書いてあるが、実ロジックは少ない
    //
    //////////////////////////////////////////////////////////////////////
    const PREPARE=3; // 準備中（作業実行ファイルが２にする）
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL'); // 'DEBUG';

    // PHP エラー時のログ出力先を設定
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php                  = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php                = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                  = '/libs/commonlibs/common_db_connect.php';

    $db_access_user_id = -100902; //
    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag                     = 0;          // 警告フラグ(1：警告発生)
    $error_flag                       = 0;          // 異常フラグ(1：異常発生)

    $tgt_row_array          = array();    // 処理対象から準備中を除くレコードまるごと格納

    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////

    // トランザクションフラグ(初期値はfalse)
    $transaction_flag = false;

    try {
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50003");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////////////////////////////////////////////////////////////////////
        // 作業インスタンステーブルから処理対象レコードの一意キーを取得(レコードロック)
        ////////////////////////////////////////////////////////////////////////////////////////////////

        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00000400")) );
        }

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51001");
            require ($root_dir_path . $log_output_php );
        }

        $arrayConfig_openst = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "EXECUTION_NO"=>"",
            "SYMPHONY_NAME"=>"",
            "EXECUTION_USER"=>"",
            "STATUS_ID"=>"",
            "PATTERN_ID"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_PATTERN_NAME"=>"",
            "I_OPERATION_NAME"=>"",
            "HEAT_INPUT"=>"",
            "HEAT_RESULT"=>"",
             "I_OPERATION_NO_IDBH"=>"",
            "RUN_MODE"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "HEAT_INPUT"=>"",
            "HEAT_RESULT"=>"",
            "I_TIME_LIMIT"=>"",
            "DISUSE_FLAG"=>"",
            "NOTE"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $temp_array = array('WHERE'=>"DISUSE_FLAG = '0' AND
                                      (
                                       ( TIME_BOOK IS NULL AND STATUS_ID = 1 ) OR
                                       ( TIME_BOOK <= :KY_DB_DATETIME(6): AND STATUS_ID = 2 )
                                      )");

        $arrayValue_openst = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "EXECUTION_NO"=>"",
            "SYMPHONY_NAME"=>"",
            "EXECUTION_USER"=>"",
            "STATUS_ID"=>"",
            "PATTERN_ID"=>"",
            "OPERATION_NO_UAPK"=>"",
            "I_PATTERN_NAME"=>"",
            "I_OPERATION_NAME"=>"",
            "HEAT_INPUT"=>"",
            "HEAT_RESULT"=>"",
            "I_OPERATION_NO_IDBH"=>"",
            "RUN_MODE"=>"",
            "TIME_BOOK"=>"",
            "TIME_START"=>"",
            "TIME_END"=>"",
            "HEAT_INPUT"=>"",
            "HEAT_RESULT"=>"",
            "I_TIME_LIMIT"=>"",
            "DISUSE_FLAG"=>"",
            "NOTE"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "SELECT FOR UPDATE",
                                                 "EXECUTION_NO",
                                                 "C_OPENST_RESULT_MNG",
                                                 "C_OPENST_RESULT_MNG_JNL",
                                                 $arrayConfig_openst,
                                                 $arrayValue_openst,
                                                 $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00000500")) );
        }

        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00000600")) );
        }

        while ( $row = $objQueryUtn->resultFetch() ) {

                array_push( $tgt_row_array, $row );
        }
        // fetch行数を取得
        $num_of_tgt_execution_no = $objQueryUtn->effectedRowCount();

        // 処理対象レコードが0件の場合は処理終了へ
        if( $num_of_tgt_execution_no < 1 ) {
            // トランザクション終了
            if( $objDBCA->transactionExit()===false ) {
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00000700")) );
            }
            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51002");
                require ($root_dir_path . $log_output_php );
            }

            // 終了メッセージ
            if ( $log_level === 'DEBUG' ) {
              $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
              require ($root_dir_path . $log_output_php );
            }

            // リターンコード
            exit(0);
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51005");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////////////////////////////////////
        // シーケンスをロック                                         //
        ////////////////////////////////////////////////////////////////
        $retArray = getSequenceLockInTrz("C_OPENST_RESULT_MNG_JSQ",'A_SEQUENCE');
        if( $retArray[1] != 0 ) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00000800")) );
        }

        ////////////////////////////////////////////////////////////////
        // 処理対象から準備中ステータスを除いたEXECUTION_NOだけループ //
        ////////////////////////////////////////////////////////////////
        foreach( $tgt_row_array as $tgt_row ) {

            ////////////////////////////////////////////////////////////////////////////////////////////////
            // 「C_EXECUTION_MANAGEMENT」の処理対象レコードのステータスを準備中にUPDATE                   //
            ////////////////////////////////////////////////////////////////////////////////////////////////

            $retArray = getSequenceValueFromTable("C_OPENST_RESULT_MNG_JSQ", 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ) {
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00000900")) );
            }

            $tgt_row["JOURNAL_SEQ_NO"]   = $retArray[0];
            $tgt_row["STATUS_ID"]        = PREPARE;
            $tgt_row["LAST_UPDATE_USER"] = $db_access_user_id;
            $tgt_row["TIME_START"] =date("Y-m-d H:i:s");

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                     "UPDATE",
                                                     "EXECUTION_NO",
                                                     "C_OPENST_RESULT_MNG",
                                                     "C_OPENST_RESULT_MNG_JNL",
                                                     $arrayConfig_openst,
                                                     $tgt_row,
                                                     $temp_array );
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false ||
                $objQueryJnl->getStatus()===false ) {
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ||
                $objQueryJnl->sqlBind($arrayJnlBind) != "" ) {
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001100")) );
            }

            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true) {
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001200")) );
            }

            $rJnl = $objQueryJnl->sqlExecute();

            if($rJnl!=true) {
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001300")) );
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51006",$tgt_row["EXECUTION_NO"]);
                require ($root_dir_path . $log_output_php );
            }
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51007");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001400")) );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51008");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = false;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51080");
            require ($root_dir_path . $log_output_php );
        }
    }
    catch (Exception $e){

        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55272");
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
        if( $objDBCA->getTransactionMode() ) {
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ) {
                if( !empty( $tgt_execution_no ) ) {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51081",$tgt_execution_no);
                } else {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51082");
                }
            } else {
                if( !empty( $tgt_execution_no ) ) {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-51083",$tgt_execution_no);
                } else {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-51084");
                }
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ) {
                if( !empty( $tgt_execution_no ) ) {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51085",$tgt_execution_no);
                } else {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-51086");
                }
            } else {
                if( !empty( $tgt_execution_no ) ) {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-51087",$tgt_execution_no);
                } else {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-51089");
                }
            }
            require ($root_dir_path . $log_output_php );
        }
    }

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ) {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50001");
            require ($root_dir_path . $log_output_php );
        }
        // 常駐プロセスが死なないようにした
        exit(2);
    }
    elseif( $warning_flag != 0 ) {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            require ($root_dir_path . $log_output_php );
        }
        exit(2);
    } else {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }
        exit(0);
    }
?>
