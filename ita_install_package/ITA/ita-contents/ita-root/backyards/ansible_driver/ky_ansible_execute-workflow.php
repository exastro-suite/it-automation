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
    //      Ansible 作業インスタンス実行
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
    $ansible_table_define_php        = '/libs/backyardlibs/ansible_driver/AnsibleTableDefinition.php';
    $DBaccess_php                    = "/libs/backyardlibs/common/common_db_access.php";  
    $comDBaccess_php                 = "/libs/backyardlibs/ansible_driver/ky_ansible_execute-workflow_common.php";

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag                    = 0; // 警告フラグ(1：警告発生)
    $error_flag                      = 0; // 異常フラグ(1：異常発生)

    $db_access_user_id               = -100020; // Ansible作業実行プロシージャ

    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    
    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require_once ($root_dir_path . $php_req_gate_php );
        require_once ($root_dir_path . $ansible_table_define_php);
        require_once ($root_dir_path . $DBaccess_php);
        require_once ($root_dir_path . $comDBaccess_php);

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
        $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);

        /////////////////////////////////////////////////////////////////
        // 処理中/実行中の作業インスタンスの実行プロセス起動確認
        /////////////////////////////////////////////////////////////////
        $ret = ChildProcessExistCheck($dbobj);
        if($ret === false) {
            // 作業インスタンスの実行プロセスの起動確認が失敗しました。(作業No.:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50074");

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $FREE_LOG );
        }

        /////////////////////////////////////////////////////////////////
        // 実行中の作業インスタンス数取得
        /////////////////////////////////////////////////////////////////
        $sqlBody = "SELECT *
                    FROM   D_ANSIBLE_EXE_INS_MNG
                    WHERE  STATUS_ID in (2,3,4) AND DISUSE_FLAG = '0' ";

        $dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $objQuery  = "";
        $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50069"); 
            require ($root_dir_path . $log_output_php );

            // ログ出力
            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                   array(basename(__FILE__),__LINE__,"00000100")),
                                                                   $dbobj->getLastErrorMsg());

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $FREE_LOG );
        }
        $num_of_run_instance = $objQuery->effectedRowCount();

        unset($objQuery);

        /////////////////////////////////////////////////////////////////
        // 未実行の作業インスタンス取得
        /////////////////////////////////////////////////////////////////
        $execute_list = array();
        $sqlBody = "SELECT *
                    FROM   D_ANSIBLE_EXE_INS_MNG
                    WHERE  DISUSE_FLAG = '0' AND
                           (
                             ( TIME_BOOK IS NULL AND STATUS_ID = 1 ) OR
                             ( TIME_BOOK <= NOW(6) AND STATUS_ID = 9 )
                           )";

        $dbobj->ClearLastErrorMsg();
        $arrayBind = array();
        $objQuery  = "";
        $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            // ログ出力
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50064");
            require ($root_dir_path . $log_output_php );

            $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                  array(basename(__FILE__),__LINE__,"00000100")),
                                                                  $dbobj->getLastErrorMsg());

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception( $FREE_LOG );
        }

        // 処理対象レコードが0件の場合は処理終了へ
        if( $objQuery->effectedRowCount() < 1 ){

            // 例外処理へ(例外ではないが・・・)
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-STD-51003") );
        }

        // 作業インスタンス実行順リストを生成
        $tgt_execution_no_array       = array();
        $tgt_execution_info_array     = array();
        $tgt_execute_order_array      = array();
        while ( $row = $objQuery->resultFetch() ){
            $id = $row['DRIVER_NAME'] .':' . $row['EXECUTION_NO'];
            $tgt_execution_no_array[]                      = $id;
            $tgt_execution_info_array[$id]['DRIVER_ID']    = $row['DRIVER_ID'];
            $tgt_execution_info_array[$id]['DRIVER_NAME']  = $row['DRIVER_NAME'];
            $tgt_execution_info_array[$id]['EXECUTION_NO'] = $row['EXECUTION_NO'];

            // 予約時間+最終更新日+作業番号でリスト生成
            $tgt_execute_order_array[$id] = $row['LAST_UPDATE_TIMESTAMP'] . "-" . sprintf("%010d",$row['EXECUTION_NO']);
            if(strlen($row['TIME_BOOK']) != 0) {
                if($row['LAST_UPDATE_TIMESTAMP'] < $row['TIME_BOOK']) {
                    $tgt_execute_order_array[$id] = $row['TIME_BOOK'] . "-" . sprintf("%010d",$row['EXECUTION_NO']);
                }
            }
        }
        // 作業インスタンス実行順リストのソート
        asort($tgt_execute_order_array);

        // DBアクセス事後処理
        unset($objQueryUtn);

        ////////////////////////////////////////////////////////////////
        // ANSIBLEインタフェース情報を取得                            //
        ////////////////////////////////////////////////////////////////
        $lv_ans_if_info = array();
        $ret = cm_getAnsibleInterfaceInfo($dbobj,'-',$lv_ans_if_info,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }
        
        // 並列実行数
        $lv_num_of_parallel_exec  = $lv_ans_if_info['ANSIBLE_NUM_PARALLEL_EXEC'];

        ////////////////////////////////////////////////////////////////////////////////
        // 処理実行順に対象作業インスタンスを実行
        ////////////////////////////////////////////////////////////////////////////////
        foreach( $tgt_execute_order_array as $id => $sort_key ){

            // 並列実行数判定
            if($num_of_run_instance >= $lv_num_of_parallel_exec) {
                break;
            }
            $num_of_run_instance++;

            ////////////////////////////////////////////////////////////////
            // ドライバに対応した変数の読み込み
            ////////////////////////////////////////////////////////////////
            require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php");

            switch($tgt_execution_info_array[$id]['DRIVER_ID']) {
            case DF_LEGACY_DRIVER_ID:
                require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_setenv.php");
                break;
            case DF_LEGACY_ROLE_DRIVER_ID:
                require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_role_setenv.php");
                break;
            case DF_PIONEER_DRIVER_ID:
                require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_pioneer_setenv.php");
                break;
            }

            // 対象作業インスタンスを実行 
            $ret = instance_execution($tgt_execution_info_array[$id]);
            if($ret === false) {
                if($objDBCA->getTransactionMode()) {
                    ////////////////////////////////////////////////////////////////
                    // トランザクション終了
                    ////////////////////////////////////////////////////////////////
                    cm_transactionExit('-');
                }

                // 異常フラグON
                $error_flag = 1;
                break;
            }
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
            $ret = cm_transactionRollBack('-',$FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
            }
            // トランザクション終了
            cm_transactionExit('-');
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
    function instance_execution($in_execution_info_array) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $db_access_user_id;

        $tgt_execution_no = $in_execution_info_array['EXECUTION_NO'];
        $tgt_driver_name  = $in_execution_info_array['DRIVER_NAME'];

        try {
            ////////////////////////////////////////////////////////////////
            // ドライバに対応した変数の読み込み
            ////////////////////////////////////////////////////////////////
            require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php");

            switch($in_execution_info_array['DRIVER_ID']) {
            case DF_LEGACY_DRIVER_ID:
                require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_setenv.php");
                break;
            case DF_LEGACY_ROLE_DRIVER_ID:
                require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_role_setenv.php");
                break;
            case DF_PIONEER_DRIVER_ID:
                require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_pioneer_setenv.php");
                break;
            }

            $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS, $db_access_user_id);

            ////////////////////////////////////////////////////////////////
            // トランザクション開始
            ////////////////////////////////////////////////////////////////
            $ret = cm_transactionStart($tgt_execution_no,$FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                return false;
            }

            /////////////////////////////////////////////////////////////////
            // 処理対象の作業インスタンス情報取得
            /////////////////////////////////////////////////////////////////
            $ret = cm_getEexecutionInstanceRow($dbobj,$tgt_execution_no,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$tgt_execution_row,$FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                return false;
            }

            ////////////////////////////////////////////////////////////////
            // シーケンスをロックし履歴シーケンス採番
            ////////////////////////////////////////////////////////////////
            $dbobj->ClearLastErrorMsg();
            $intJournalSeqNo = cm_dbaccessGetSequence($dbobj,$vg_exe_ins_msg_table_jnl_seq,$tgt_execution_no,$FREE_LOG);
            if($intJournalSeqNo === false) {
                require ($root_dir_path . $log_output_php );
                return false;
            }
            // 未実行状態で緊急停止出来るようにしているので
            // 未実行状態かを判定
            if(($tgt_execution_row["STATUS_ID"] != 1) &&
               ($tgt_execution_row["STATUS_ID"] != 9)) {
                $FREE_LOG = "Emergency stop in unexecuted state.(execution_no: $tgt_execution_no)";
                require ($root_dir_path . $log_output_php );
                return false;
            }
    
            ////////////////////////////////////////////////////////////////
            // 処理対象の作業インスタンスのステータスを処理中に設定
            ////////////////////////////////////////////////////////////////
            $tgt_execution_row["JOURNAL_SEQ_NO"]   = $intJournalSeqNo;
            $tgt_execution_row["STATUS_ID"]        = 2;
            $tgt_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;

            $ret = cm_InstanceRecodeUpdate($dbobj,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$tgt_execution_row, $FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                return false;
            }

            ////////////////////////////////////////////////////////////////
            // コミット(レコードロックを解除)
            ////////////////////////////////////////////////////////////////
            $ret = cm_transactionCommit($tgt_execution_no,$FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
                return false;
            }

            ////////////////////////////////////////////////////////////////
            // トランザクション終了
            ////////////////////////////////////////////////////////////////
            cm_transactionExit($tgt_execution_no);

            $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");

            // 改行コードが付いている場合に取り除く
            $php_command = str_replace("\n","",$php_command);

            // ログファイル名(フルパス)を作成
            $tmpVarTimeStamp = time();
            $log_file_postfix = ".log";
            $logfile = $log_output_dir .'/' . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . $log_file_postfix;

            $cmd = sprintf("%s %s%s %s %010s %s-%010s > /dev/null &",
                            $php_command,
                            $root_dir_path,
                            "/backyards/ansible_driver/ky_ansible_execute-child-workflow.php",
                            $vg_driver_id,
                            $tgt_execution_no,
                            $tgt_driver_name,
                            $tgt_execution_no);

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]処理対象インスタンス 実行プロセス起動(作業No.:Legacy:16)
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50077",array($tgt_driver_name,$tgt_execution_no)); 
                require ($root_dir_path . $log_output_php );
            }

            // プロセス起動 バックグラウンドで起動しているのでエラーは判定不可。エラー情報はログファイルにリダイレクト
            exec($cmd,$arry_out,$return_var);

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                // [処理]処理対象インスタンス 実行プロセス起動終了(作業No.:Legacy:16)
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50078",array($tgt_driver_name,$tgt_execution_no)); 
                require ($root_dir_path . $log_output_php );
            }
            return true;
        } catch (Exception $e){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
            return false;
        }
    }
    function ChildProcessExistCheck($dbobj) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $db_access_user_id;

        ////////////////////////////////////////////////////////////////
        // ドライバに対応した変数の読み込み
        ////////////////////////////////////////////////////////////////
        require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php");

        try {
            // psコマンドでky_ansible_execute-child-workflow.phpの起動リストを作成
            // psコマンドがマレに起動プロセスリストを取りこぼすことがあるので3回分を作成
            $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50073");
            $strBuildCommand     = "ps -efw|grep ky_ansible_execute-child-workflow.php|grep -v grep";
            exec($strBuildCommand,$ps_array1,$ret);

            usleep(50000);   // sleep 50ms

            $strBuildCommand     = "ps -efw|grep ky_ansible_execute-child-workflow.php|grep -v grep";
            exec($strBuildCommand,$ps_array2,$ret);

            usleep(100000);  // sleep 100ms

            $strBuildCommand     = "ps -efw|grep ky_ansible_execute-child-workflow.php|grep -v grep";
            exec($strBuildCommand,$ps_array3,$ret);

            /////////////////////////////////////////////////////////////////
            // 実行中の作業インスタンス数取得
            /////////////////////////////////////////////////////////////////
            $sqlBody = "SELECT *
                        FROM   D_ANSIBLE_EXE_INS_MNG
                        WHERE  STATUS_ID in (2,3,4) AND DISUSE_FLAG = '0' ";

            $dbobj->ClearLastErrorMsg();
            $arrayBind = array();
            $objQuery  = "";
            $ret = $dbobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
            if($ret === false) {
                // ログ出力
                $FREE_LOG = sprintf("%s\n%s", $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",
                                                                       array(basename(__FILE__),__LINE__,"00000100")),
                                                                       $dbobj->getLastErrorMsg());

                require ($root_dir_path . $log_output_php );

                throw new Exception($objMTS->getSomeMessage("ITAANSIBLEH-ERR-50069")); 
            }
            while ( $row = $objQuery->resultFetch() ){
                switch($row['DRIVER_ID']) {
                case DF_LEGACY_DRIVER_ID:
                    require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_setenv.php");
                    break;
                case DF_LEGACY_ROLE_DRIVER_ID:
                    require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_legacy_role_setenv.php");
                    break;
                case DF_PIONEER_DRIVER_ID:
                    require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_pioneer_setenv.php");
                    break;
                }
                $tgt_execution_no     = $row['EXECUTION_NO'];
                $tgt_driver_name      = $row['DRIVER_NAME'];
                $tgt_ChildProcessName = sprintf("%s-%010s",$tgt_driver_name,$tgt_execution_no);
                $tgt_hit = false;
                foreach($ps_array1 as $line) {
                    $ret = preg_match("/$tgt_ChildProcessName/",$line);
                    if($ret == 1){
                        $tgt_hit = true;
                        break;
                    }
                }
                if($tgt_hit === false) {
                    foreach($ps_array2 as $line) {
                        $ret = preg_match("/$tgt_ChildProcessName/",$line);
                        if($ret == 1){
                            $tgt_hit = true;
                            break;
                        }
                    }
                    if($tgt_hit === false) {
                        foreach($ps_array3 as $line) {
                            $ret = preg_match("/$tgt_ChildProcessName/",$line);
                            if($ret == 1){
                                $tgt_hit = true;
                                break;
                            }
                        }
                    }
                }
                if($tgt_hit === true) {
                    continue; 
                }

                // 作業インスタンスの状態が処理中/実行中でプロセスが存在していない
                // 作業インスタンスの状態を想定外エラーに設定する。
                // 作業インスタンスの実行プロセスが起動していません。(作業No.:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50071",array($tgt_driver_name,$tgt_execution_no)); 
                require ($root_dir_path . $log_output_php );
                // "ステータスを想定外エラーに設定出来ませんでした。(作業No.:{})
                $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50072",array($tgt_driver_name,$tgt_execution_no));
                ////////////////////////////////////////////////////////////////
                // トランザクション開始
                ////////////////////////////////////////////////////////////////
                $ret = cm_transactionStart($tgt_execution_no,$FREE_LOG);
                if($ret === false) {
                    require ($root_dir_path . $log_output_php );
                    throw new Exception($ErrorMsg);
                }

                /////////////////////////////////////////////////////////////////
                // 処理対象の作業インスタンス情報取得
                /////////////////////////////////////////////////////////////////
                $tgt_execution_row = array();
                $ret = cm_getEexecutionInstanceRow($dbobj,$tgt_execution_no,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$tgt_execution_row,$FREE_LOG);
                if($ret === false) {
                    require ($root_dir_path . $log_output_php );
                    throw new Exception($ErrorMsg);
                }
    
                ////////////////////////////////////////////////////////////////
                // シーケンスをロックし履歴シーケンス採番
                ////////////////////////////////////////////////////////////////
                $dbobj->ClearLastErrorMsg();
                $intJournalSeqNo = cm_dbaccessGetSequence($dbobj,$vg_exe_ins_msg_table_jnl_seq,$tgt_execution_no,$FREE_LOG);
                if($intJournalSeqNo === false) {
                    require ($root_dir_path . $log_output_php );
                    throw new Exception($ErrorMsg);
                }
        
                ////////////////////////////////////////////////////////////////
                // 処理対象の作業インスタンスのステータスを想定外エラーに設定
                ////////////////////////////////////////////////////////////////
                $tgt_execution_row["JOURNAL_SEQ_NO"]   = $intJournalSeqNo;
                if(strlen(trim($tgt_execution_row['TIME_START'])) == 0) {
                    $tgt_execution_row['TIME_START']         = "DATETIMEAUTO(6)";
                }
                $tgt_execution_row['TIME_END']         = "DATETIMEAUTO(6)";
                $tgt_execution_row["STATUS_ID"]        = 7;
                $tgt_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;
    
    
                $ret = cm_InstanceRecodeUpdate($dbobj,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$tgt_execution_row, $FREE_LOG);
                if($ret === false) {
                    require ($root_dir_path . $log_output_php );
                    throw new Exception($ErrorMsg);
                }
    
                ////////////////////////////////////////////////////////////////
                // コミット(レコードロックを解除)
                ////////////////////////////////////////////////////////////////
                $ret = cm_transactionCommit($tgt_execution_no,$FREE_LOG);
                if($ret === false) {
                    require ($root_dir_path . $log_output_php );
                    throw new Exception($ErrorMsg);
                }
                ////////////////////////////////////////////////////////////////
                // トランザクション終了
                ////////////////////////////////////////////////////////////////
                cm_transactionExit($tgt_execution_no);

                // ステータスを想定外エラーに設定しました。(作業No.:{}:{})
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50075",array($tgt_driver_name,$tgt_execution_no)); 
                require ($root_dir_path . $log_output_php );
    
            }
            return true;
        } catch (Exception $e){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
            return false;
        }
    }
?>
