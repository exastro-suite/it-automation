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
    //  【機能】
    //   ITAのシステム一覧のリストを取得し、ファイルに記録する。
    //
    //////////////////////////////////////////////////////////////////////

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
    $file_name          = 'cobbler_system_List';
    $file_name_temp     = 'cobbler_system_List_temp';

    $tbl_name           = 'E_STM_LIST'; //管理システム名＝ホスト名のビュー
    $tbl_key            = 'SYSTEM_ID';//$tbl_nameのキー
    $tbl_name_jnl       = 'C_STM_LIST_JNL';
    $pro_tbl_name       = 'C_COBBLER_PROFILE';
    $pro_key            = 'COBBLER_PROFILE_ID';//$pro_tbl_nameのキー
    $pro_name           = 'COBBLER_PROFILE_NAME';//プロファイル名を登録するカラムの名前
    $pro_tbl_name_jnl   = 'C_COBBLER_PROFILE_JNL';
    $get_strage_php     = $root_dir_path . '/libs/backyardlibs/cobbler_driver/cobbler_driver_getRelayDirectory.php';

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)
    $row_array                  = array();  //システム一覧一件分の情報を持つ配列
    $file_folder                = '';
    $temp_file_dir              = '';
    $file_dir                   = '';

    try{
        ////////////////////////////////
        // 業務処理開始               //
        ////////////////////////////////

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = false;

        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            //"プロシージャ開始"
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ( $root_dir_path . $db_connect_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2002");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2001", array(__LINE__)) );
        }

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = true;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2003");
            require ($root_dir_path . $log_output_php );
        }

        //データリレーストレージのディレクトリをDBより取得し、$file_nameと結合
        require ( $get_strage_php );
        $file_dir = $file_folder . "/" . $file_name;
        $temp_file_dir = $file_folder . "/" . $file_name_temp;

        ////////////////////////////////////////////////////////////////
        // 該当レコードをSELECT                                       //
        ////////////////////////////////////////////////////////////////
        $arrayConfig = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "SYSTEM_ID"=>"",
            "HARDAWRE_TYPE_ID"=>"",
            "HOSTNAME"=>"",
            "IP_ADDRESS"=>"",
            "SYSTEM_NAME"=>"",
            "COBBLER_PROFILE_ID"=>"",
            "INTERFACE_TYPE"=>"",
            "MAC_ADDRESS"=>"",
            "NETMASK"=>"",
            "GATEWAY"=>"",
            "STATIC"=>""
        );

        //すべての有効なレコードを取得 サーバーのみを取得(HARDAWRE_TYPE_ID = '1')
        $temp_array = array('WHERE'=>" DISUSE_FLAG = '0' AND HARDAWRE_TYPE_ID = '1'");

        $arrayValue = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "SYSTEM_ID"=>"",
            "HARDAWRE_TYPE_ID"=>"",
            "HOSTNAME"=>"",
            "IP_ADDRESS"=>"",
            "SYSTEM_NAME"=>"",
            "COBBLER_PROFILE_ID"=>"",
            "INTERFACE_TYPE"=>"",
            "MAC_ADDRESS"=>"",
            "NETMASK"=>"",
            "GATEWAY"=>"",
            "STATIC"=>""
        );

        $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                             'SELECT',
                                             $tbl_key,
                                             $tbl_name,
                                             $tbl_name_jnl,
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
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2002", array(__LINE__)) );
        }

        $r = $objQueryUtn->sqlExecute();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2003", array(__LINE__)) );
        }

        //何かしらのエラーで一時ファイルが作られていた場合は消去
        if(file_exists($temp_file_dir)){
            if(!unlink($temp_file_dir)){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2004", array($temp_file_dir)) );
            }
        }

        if(!file_exists($file_folder)){
            if(!mkdir($file_folder)){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2005", array($file_folder)) );
            }
        }

        //ファイルオープン
        $open_resource = fopen($temp_file_dir, 'a');
        if (!$open_resource){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2006", array($temp_file_dir)) );
        }

        //ファイルをロック
        $lock_result = flock($open_resource, 'LOCK_EX');

        // レコードFETCH
        while ( $row = $objQueryUtn->resultFetch() ){
            //######cobblerプロファイル名を取得。登録がない場合、廃止されている場合は空文字列を入れておく。
            $profile_name = '';
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

            //すべての有効なレコードを取得
            $temp_array = array('WHERE'=>" DISUSE_FLAG = '0' AND COBBLER_PROFILE_ID = :COBBLER_PROFILE_ID");

            $arrayValue = array(
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

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                 'SELECT',
                                                 $pro_key,
                                                 $pro_tbl_name,
                                                 $pro_tbl_name_jnl,
                                                 $arrayConfig,
                                                 $arrayValue,
                                                 $temp_array );

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            $arrayUtnBind[$pro_key] = $row["COBBLER_PROFILE_ID"];

            $proObjQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

            if( $proObjQueryUtn->getStatus()===false ){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2007", array(__LINE__)) );
            }

            if( $proObjQueryUtn->sqlBind($arrayUtnBind) != "" ){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2008", array(__LINE__)) );
            }

            $r = $proObjQueryUtn->sqlExecute();
            if (!$r){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2009", array($sqlUtnBody,__LINE__)) );
            }

            $fetch_counter = $proObjQueryUtn->effectedRowCount();
            //1件ではないときは空文字列のまま（2件以上はあり得ないので0件のとき：廃止されている場合、登録されていない場合）
            if($fetch_counter == 1){
                $profile_row = array();
                // レコードFETCH
                while ( $result_row = $proObjQueryUtn->resultFetch() ){
                    $profile_row = $result_row;
                }
                $profile_name = $profile_row[$pro_name];
            }

            // DBアクセス事後処理
            if ( isset($proObjQueryUtn) ) unset($proObjQueryUtn);
            //######プロファイル名取得終了

            $row_array = array();
            //１データにつき一行を作成
            $row_array["SYSTEM_NAME"]          = $row["SYSTEM_NAME"];
            $row_array["HOSTNAME"]             = $row["HOSTNAME"];
            $row_array["IP_ADDRESS"]           = $row["IP_ADDRESS"];
            $row_array["COBBLER_PROFILE_NAME"] = $profile_name;
            $row_array["INTERFACE_TYPE"]       = $row["INTERFACE_TYPE"];
            $row_array["MAC_ADDRESS"]          = $row["MAC_ADDRESS"];
            $row_array["NETMASK"]              = $row["NETMASK"];
            $row_array["GATEWAY"]              = $row["GATEWAY"];
            $row_array["STATIC"]               = $row["STATIC"];

            //ファイル書き込み
            $write_result = fwrite($open_resource, json_encode($row_array)."\n");
            if (!$write_result){
                // 異常フラグON
                $error_flag = 1;
                // 例外処理へ
                throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2010", array($temp_file_dir,__LINE__)) );
            }
        }

        //ファイルクローズ
        $close_result = fclose($open_resource);
        if (!$close_result){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2011", array($temp_file_dir,__LINE__)) );
        }

        //////////////////////////////////////////////////////////
        //  旧ファイルを消去し、一時ファイルをリネームする。    //
        //////////////////////////////////////////////////////////
        //旧ファイルを消去 cobbler側で見ているタイミングで消去できない可能性があるのでスパンを取る。
        if(file_exists($file_dir)){
            $unlink_result = false;
            $roop_time = 0;
            while(!$unlink_result){
                $unlink_result = unlink($file_dir);
                if(!$unlink_result){
                    sleep(3);
                    if($roop_time > 9)//3秒おきに10回ループしても消去できなかったらエラー
                    {
                        // 異常フラグON
                        $error_flag = 1;
                        // 例外処理へ
                        throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2012", array($file_dir,__LINE__)) );
                    }
                    $roop_time++;
                }
            }
        }

        //一時ファイルをリネーム
        if(!rename($temp_file_dir, $file_dir)){
            // メッセージ出力 旧ファイルは消去されているはずなので、一時ファイルは消さずにおいておく。
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-2013", array($temp_file_dir,__LINE__));
            require (root_dir_path . $log_output_php );
        }

        // DBアクセス事後処理
        unset($objQueryUtn);

        ////////////////////////////////////////////////////////////////
        // 作業終了　一応ロールバック                                 //
        ////////////////////////////////////////////////////////////////
        $r = $objDBCA->transactionRollBack();
        if (!$r){
            // 異常フラグON
            $error_flag = 1;
            // 例外処理へ
            throw new Exception( $objMTS->getSomeMessage("ITACBLH-ERR-2014", array(__LINE__)) );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2004");
            require ($root_dir_path . $log_output_php );
        }

        // DBアクセス事後処理
        if ( isset($objQueryUtn) ) unset($objQueryUtn);

        ////////////////////////////////
        // トランザクション終了       //
        ////////////////////////////////
        $objDBCA->transactionExit();

        // トランザクションフラグ(初期値はfalse)
        $transaction_flag = false;

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2005");
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

        //エラー発生時、一時ファイルを消去する。
        if(file_exists($temp_file_dir)){
            if(!unlink($temp_file_dir)){
                // メッセージ出力
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-2015", array(__LINE__));
                require ($log_output_php );
            }
        }

        // DBアクセス事後処理
        if ( isset($objQueryUtn) ) unset($objQueryUtn);

        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $transaction_flag ){
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ){
                //$FREE_LOG = '[処理]ロールバック';
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2006");
            }
            else{
                //"ロールバックに失敗しました"
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-2016", array(__LINE__));
            }
            require ($root_dir_path . $log_output_php );

            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ){
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-STD-2005");
            }
            else{
                $FREE_LOG = $objMTS->getSomeMessage("ITACBLH-ERR-2017", array(__LINE__));
            }
            require ($root_dir_path . $log_output_php );
        }
    }
?>
