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
    /////////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  【機能】
    //   cobblerのプロファイル名のリストファイルを取得し、ITAのデータベースの更新・登録・削除を行う。
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////

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
    $log_level = getenv('LOG_LEVEL');

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php     = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php   = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php     = '/libs/commonlibs/common_db_connect.php';
    $db_access_user_id  = -100007; //cobbler同期管理プロシージャ
    $file_name          = 'cobbler_profile_List';

    $tbl_name           = 'C_COBBLER_PROFILE';
    $tbl_key            = 'COBBLER_PROFILE_ID'; //$tbl_nameのキー
    $file_key           = 'COBBLER_PROFILE_NAME'; //ファイルに記載してあるプロファイル名と比較するカラム
    $tbl_name_jnl       = 'C_COBBLER_PROFILE_JNL';
    $seq_name_jsq       = 'C_COBBLER_PROFILE_JSQ';
    $seq_name_ric       = 'C_COBBLER_PROFILE_RIC';
    $executeTablePath   = $root_dir_path . '/libs/backyardlibs/cobbler_driver/cobbler_driver_executeTable.php';
    $get_strage_php     = $root_dir_path . '/libs/backyardlibs/cobbler_driver/cobbler_driver_getRelayDirectory.php';

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $insert_flg                 = 0;        // INSERTとなるレコードかどうか
    $file_exist_keys            = array();  // ファイルに格納されているレコードのキーとなるカラムの値
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $tgt_execution_no_array     = array();  // 確認対象のEXECUTION_NOのリストを格納
    $tgt_execution_no_str       = '';       // 確認対象のEXECUTION_NOのリストを格納
    $num_of_tgt_execution_no    = 0;        // 確認対象のEXECUTION_NOの個数を格納
    $file_folder                = '';       //データリレーストレージのディレクトリのアドレス
    $file_dir                   = '';       // データリレーストレージのファイルのアドレス

    ////////////////////////////////
    // ファンクション宣言         //
    ////////////////////////////////
    //テーブルの次の項番を取得
    function lockAndGetSequence($is_jnl, $objMTS){
        global $seq_name_jsq, $seq_name_ric;

        $seq_name = $is_jnl == 1 ? $seq_name_jsq : $seq_name_ric;

        $retArray = getSequenceLockInTrz($seq_name,'A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1001", array(__LINE__)) );
        }

        // 履歴シーケンス払い出し
        $retArray = getSequenceValueFromTable($seq_name, 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1002", array(__LINE__)) );
        }

        //次の項番を返す
        return $retArray[0];
    }


    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    try{
        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = false;

        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1002");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1003", array(__LINE__)) );
        }

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1003");
            require ($root_dir_path . $log_output_php );
        }

        //データリレーストレージのディレクトリをDBより取得し、$file_nameと結合
        require ( $get_strage_php );
        $file_dir = $file_folder . "/" . $file_name;

        //SQL作成ファンクションで使用する配列を設定しておく
        $arrayConfig = array(
                "JOURNAL_SEQ_NO"=>"",
                "JOURNAL_ACTION_CLASS"=>"",
                "JOURNAL_REG_DATETIME"=>"",
                "COBBLER_PROFILE_ID"=>"",
                "COBBLER_PROFILE_NAME"=>"",
                "DISP_SEQ"=>"",
                "NOTE"=>"",
                "DISUSE_FLAG"=>"",
                "LAST_UPDATE_TIMESTAMP"=>"",
                "LAST_UPDATE_USER"=>""
            );

        ////////////////////////////////////////////////////////////////
        // UPDATE,INSERTを実行                                        //
        ////////////////////////////////////////////////////////////////
        //ファイル全体を取得 cobbler側で一時ファイルと入れ替えているタイミングで消えていることがあるため、スパンを取る。
        $file_in_one = false;//読み込み結果が来ない間はループ
        $roop_time = 0;
        while(!$file_in_one){
            $file_in_one = file($file_dir);
            if(!$file_in_one){
                // ファイルが存在し、かつそのファイルサイズが0の場合はデータが空として以降の処理を継続
                if(is_file($file_dir) && (filesize($file_dir) === 0)){
                    break;
                }

                sleep(3);
                if($roop_time > 9)    //3秒おきに10回ループしても読み込めなかったら処理スキップ
                {
                    // 例外処理へ
                    throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1004", array($file_dir, __LINE__)) );
                }
                $roop_time++;
            }
        }

        //ファイルのレコード分ループ
        $i = 1;
        $str_keys = '';
        foreach($file_in_one as $record){
            $insert_flag = 0;
            $key = trim($record);//keyとなる値を取る

            if($key === '')continue;//空の場合はスルー

            //廃止フラグを立てるレコード検索用のものを作っておく
            $file_exist_keys["KEY$i"] = $key;//バインドするためのキーを連想配列で格納
            $str_keys .= ":KEY$i,";//SQLのバインド先部分の文字列を作成
            $i++;

            ////////////////////////////////////////////////////////////////
            // 該当レコードをSELECT(ロック)                               //
            ////////////////////////////////////////////////////////////////
            $temp_array = array('WHERE'=>" $file_key = :$file_key ");       //DISUSE_FLAGの立っているものからも検索する

            //条件なし
            $arrayValue = $arrayConfig;

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 "SELECT FOR UPDATE",
                                                 $tbl_key,
                                                 $tbl_name,
                                                 $tbl_name_jnl,
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $arrayUtnBind[$file_key] = $key;

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

            //生成したクエリをチェックする。
            if( $objQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1005", array(__LINE__)) );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1006", array(__LINE__)) );
            }

            $r = $objQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1007", array($sqlUtnBody,__LINE__)) );
            }

            $tgt_execution_row = array();
            // レコードFETCH
            while ( $row = $objQueryUtn->resultFetch() ){
                $tgt_execution_row = $row;
            }
            // fetch行数を取得
            $fetch_counter = $objQueryUtn->effectedRowCount();

            // DBアクセス事後処理
            unset($objQueryUtn);

            // 対象レコードがない場合はINSERT、複数ある場合は異常
            if( $fetch_counter == 1 ){
                ////////////////////////////////////////////////////////////////
                ///UPDATEが必要か判定(廃止フラグを降ろす場合)                ///
                ////////////////////////////////////////////////////////////////
                //廃止フラグが立っていた場合のみファイルに存在しているものをUPDATE
                if($tgt_execution_row['DISUSE_FLAG'] == 0){
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1004", array($tgt_execution_row['COBBLER_PROFILE_NAME'],__LINE__));
                        require ($root_dir_path . $log_output_php );
                    }
                    continue;
                }
                //insertフラグoff
                $insert_flag = 0;
            }
            elseif($fetch_counter == 0){
                ////////////////////////////////////////////////////////////////
                ///DBにレコードが存在していなければINSERT                    ///
                ////////////////////////////////////////////////////////////////
                //insertフラグON
                $insert_flag = 1;
            }
            else{
                ////////////////////////////////////////////////////////////////
                ///DBにレコードが2件以上存在しているときは無視しておく       ///
                ////////////////////////////////////////////////////////////////
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-1011", array($tgt_execution_row['COBBLER_PROFILE_NAME'],__LINE__));
                require ($root_dir_path . $log_output_php );

                // 次レコードの処理へ
                continue;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1005", array($tgt_execution_row['COBBLER_PROFILE_NAME']));
                require ($root_dir_path . $log_output_php );
            }

            ////////////////////////////////////////////////////////////////////////
            // 「U_TEST_I0411」のUPDATEもしくはINSERTのSQLを作成                  //
            ////////////////////////////////////////////////////////////////////////
            // クローン作成
            $cln_execution_row = $tgt_execution_row;

            $do = 'UPDATE';

            // 変数バインド準備
            $cln_execution_row['JOURNAL_SEQ_NO']    = lockAndGetSequence(1, $objMTS);  // シーケンス払い出し
            $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
            $cln_execution_row['DISUSE_FLAG'] = '0';                            // UPDATE・INSERTされるものは必ず廃止フラグを降ろす

            //INSERTの場合
            if($insert_flag){
                $do ='INSERT';
                 //次の項番を取得
                $cln_execution_row[$tbl_key] = lockAndGetSequence(0, $objMTS);
                $cln_execution_row['COBBLER_PROFILE_NAME'] = $key;
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                if($insert_flag){
                    $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1006", array($tgt_execution_row['COBBLER_PROFILE_NAME']));
                }else{
                    $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1007", array($tgt_execution_row['COBBLER_PROFILE_NAME']));
                }
                require ($root_dir_path . $log_output_php );
            }

            //テーブル更新・登録
            require ( $executeTablePath );
        }

        ////////////////////////////////////////////////////////////////
        // 廃止フラグの成立を実行                                     //
        ////////////////////////////////////////////////////////////////

        if(strlen($str_keys) == 0) {
            $file_key_condition = " $file_key IS NULL ";
        } else {
            $file_key_condition = " ( $file_key NOT IN ( " . substr($str_keys, 0, -1) . " ) or $file_key IS NULL) ";
        }
        $temp_array = array('WHERE'=>$file_key_condition . " AND DISUSE_FLAG = '0' ");

        //条件なし
        $arrayValue = $arrayConfig;

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             "SELECT FOR UPDATE",
                                             $tbl_key,
                                             $tbl_name,
                                             $tbl_name_jnl,
                                             $arrayConfig,
                                             $arrayValue,
                                             $temp_array );

        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        //ループで作成した、ファイルに存在しているキー
        foreach($file_exist_keys as $key => $value){
            $arrayUtnBind[$key] = $value;
        }

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

        //生成したクエリをチェックする。
        if( $objQueryUtn->getStatus()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1005", array(__LINE__)) );
        }

        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1006", array(__LINE__)) );
        }

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1007", array($sqlUtnBody,__LINE__)) );
        }

        $tgt_execution_rows = array();
        // レコードFETCH
        while ( $row = $objQueryUtn->resultFetch() ){
            $tgt_execution_rows[] = $row;
        }
        // DBアクセス事後処理
        unset($objQueryUtn);

        ////////////////////////////////////////////////////////////////
        // 消滅データの廃止フラグをUPDATE                             //
        ////////////////////////////////////////////////////////////////
        foreach($tgt_execution_rows as $tgt_execution_row){
            // クローン作成
            $cln_execution_row = $tgt_execution_row;
            $do = 'UPDATE';

            // 変数バインド準備
            $cln_execution_row['JOURNAL_SEQ_NO']    = lockAndGetSequence(1, $objMTS); // シーケンス払い出し
            $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
            $cln_execution_row['DISUSE_FLAG'] = '1';                            // 廃止フラグを立てる

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1010", array($tgt_execution_row['COBBLER_PROFILE_NAME']));
                require ($root_dir_path . $log_output_php );
            }

            //テーブル更新
            require ( $executeTablePath );
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionCommit();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1008", array(__LINE__)) );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //$FREE_LOG = '[作業№]' . $tgt_execution_no . '[処理]コミット';
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1010");
            require ($root_dir_path . $log_output_php );
        }

        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);

        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        if( $objDBCA->transactionExit()=== true ){
            // トランザクションフラグ(初期値はfalse)
            $transaction_flag = false;

            if ( $log_level === 'DEBUG' ){
                //$FREE_LOG = 'トランザクション終了';
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1009");
                require ($root_dir_path . $log_output_php );
            }
        }
        else{
            // 異常フラグON
            $error_flag = 1;
            //"トランザクションの終了時に異常が発生しました"
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-1010", array(__LINE__)) );
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
        if( $transaction_flag ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                //$FREE_LOG = '[処理]ロールバック';
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1008");
            }
            else{
                // 異常フラグON
                $error_flag = 1;

                //"ロールバックに失敗しました"
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-1009", array(__LINE__));
            }
            if( $log_level    === 'DEBUG' ||
                $error_flag   != 0        ||
                $warning_flag != 0        ){
                // メッセージ出力
                require ($root_dir_path . $log_output_php );
            }

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                //$FREE_LOG = 'トランザクション終了';
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-1009");
            }
            else{
                // 異常フラグON
                $error_flag = 1;

                //"トランザクションの終了時に異常が発生しました"
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-1010", array(__LINE__));
            }
            if( $log_level    === 'DEBUG' ||
                $error_flag   != 0        ||
                $warning_flag != 0        ){
                // メッセージ出力
                require ($root_dir_path . $log_output_php );
            }
        }
    }
?>
