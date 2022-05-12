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
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // 作業Noを起動パラメータで受け取る
    $tgt_driver_id       = $argv[1];  // ドライバ区分 L:Legacy P:Pioneer R:Legacy-Role
    $tgt_execution_no    = $argv[2];  // execution no 10桁
    $tgt_execution_id    = $argv[3];  // ドライバ名:execution no 10桁

    // ドライバに対応した変数の読み込み
    require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php");

    require ($root_dir_path . "/libs/commonlibs/common_required_check.php");

    switch($tgt_driver_id) {
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


    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";
    $log_file_prefix = str_replace( "ky_ansible", 'ky_' . $vg_log_driver_name, $log_file_prefix);

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL');

    ////////////////////////////////
    // 作業状態確認インターバル   //
    ////////////////////////////////
    $interval  = getenv('INTERVAL');
    if($interval === false) {
        $interval = 3;
    }

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
    $ansible_restapi_php             = '/libs/commonlibs/common_ansible_restapi.php';
    $ansible_create_files_php        = '/libs/backyardlibs/ansible_driver/CreateAnsibleExecFiles.php';
    $ansible_table_define_php        = '/libs/backyardlibs/ansible_driver/AnsibleTableDefinition.php';
    $AnsibleTowerExecute_php         = "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerExecute.php";
    $DBaccess_php                    = "/libs/backyardlibs/common/common_db_access.php";
    $comDBaccess_php                 = "/libs/backyardlibs/ansible_driver/ky_ansible_execute-workflow_common.php";

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
    
    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag             = 0;          // 警告フラグ(1：警告発生)
    $error_flag               = 0;          // 異常フラグ(1：異常発生)
    $intJournalSeqNo          = "";         // 作業インスタンス履歴 シーケンス番号
    $tgt_execution_row        = array();    // 作業インスタンス配列
    $cln_execution_row        = array();    // 作業インスタンス更新用配列

    ////////////////////////////////
    // REST API接続function定義   //
    ////////////////////////////////
    require_once ($root_dir_path . $ansible_restapi_php );

    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    
    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );
        require_once ($root_dir_path . $ansible_table_define_php);
        require_once ($root_dir_path . $AnsibleTowerExecute_php);
        require_once ($root_dir_path . $DBaccess_php);
        require_once ($root_dir_path . $ansible_create_files_php);
        require_once ($root_dir_path . $comDBaccess_php);

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            //[50052] = "[処理]プロシージャ開始 (作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50052",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
        }
        
        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //$ary[50056] = "[処理]DBコネクト完了 (作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50056",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
        }

        $dbobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);

        ////////////////////////////////////////////////////////////////
        // ANSIBLEインタフェース情報を取得
        ////////////////////////////////////////////////////////////////
        $lv_ans_if_info = array();
        $ret = cm_getAnsibleInterfaceInfo($dbobj,$tgt_execution_no,$lv_ans_if_info,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // Symphonyインタフェース情報を取得                           //
        ////////////////////////////////////////////////////////////////
        $lv_Symphony_if_info = array();
        $ret = cm_getSymphonyInterfaceInfo($dbobj,'-',$lv_Symphony_if_info,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // Conductorインタフェース情報を取得                          //
        ////////////////////////////////////////////////////////////////
        $lv_Conductor_if_info = array();
        $ret = cm_getConductorInterfaceInfo($dbobj,'-',$lv_Conductor_if_info,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション開始
        ////////////////////////////////////////////////////////////////
        $ret = cm_transactionStart($tgt_execution_no,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }

        /////////////////////////////////////////////////////////////////
        // 処理対象の作業インスタンス情報取得
        /////////////////////////////////////////////////////////////////
        $ret = cm_getEexecutionInstanceRow($dbobj,$tgt_execution_no,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$tgt_execution_row,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }
        ////////////////////////////////////////////////////////////////
        // 投入オペレーションの最終実施日を更新する。                  
        ////////////////////////////////////////////////////////////////
        require_once($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
        $dbaobj = new BackyardCommonDBAccessClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);
        $ret = $dbaobj->OperationList_LastExecuteTimestamp_Update($tgt_execution_row["OPERATION_NO_UAPK"]);
        if($ret === false) {

            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50059",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
            $FREE_LOG = $dbaobj->GetLastErrorMsg();
            $error_flag = 1;   throw new Exception($FREE_LOG);
        }
        unset($dbaobj);

        ////////////////////////////////////////////////////////////////
        // シーケンスをロックし履歴シーケンス採番  
        ////////////////////////////////////////////////////////////////
        $dbobj->ClearLastErrorMsg();
        $intJournalSeqNo = cm_dbaccessGetSequence($dbobj,$vg_exe_ins_msg_table_jnl_seq,$tgt_execution_no,$FREE_LOG);
        if($intJournalSeqNo === false) {
            $error_flag = 1; throw new Exception($FREE_LOG);
            
        }

        ////////////////////////////////////////////////////////////////
        // コミット(レコードロックを解除)                             //
        ////////////////////////////////////////////////////////////////
        if(cm_transactionCommit($tgt_execution_no,$FREE_LOG) !== true) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // トランザクション終了
        ////////////////////////////////////////////////////////////////
        cm_transactionExit($tgt_execution_no);

        // クローン作製
        $cln_execution_row = $tgt_execution_row;
        $cln_execution_row['JOURNAL_SEQ_NO']    = $intJournalSeqNo;
        $cln_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
        $cln_execution_row['STATUS_ID']         = "7";  //想定外エラーに設定しておく
        $cln_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;

        //////////////////////////////////////////////////////////////////
        // inディレクトリ生成クラス生成
        //////////////////////////////////////////////////////////////////
        require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_global_variables.php");

        $ansdrv = new CreateAnsibleExecFiles($tgt_driver_id,
                                             $lv_ans_if_info['ANSIBLE_STORAGE_PATH_LNX'],
                                             $lv_ans_if_info['ANSIBLE_STORAGE_PATH_ANS'],  
                                             $lv_ans_if_info['SYMPHONY_STORAGE_PATH_ANS'],
                                             $lv_ans_if_info['CONDUCTOR_STORAGE_PATH_ANS'],
                                             $lv_Symphony_if_info["SYMPHONY_STORAGE_PATH_ITA"],
                                             $lv_Conductor_if_info["CONDUCTOR_STORAGE_PATH_ITA"],
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
                                             $lv_ans_if_info,
                                             $tgt_execution_no,
                                             $cln_execution_row['I_ENGINE_VIRTUALENV_NAME'],
                                             $cln_execution_row['I_ANSIBLE_CONFIG_FILE'],
                                             $objMTS,
                                             $objDBCA);

        //////////////////////////////////////////////////////////////////
        // 処理対象の作業インスタンス実行                            
        //////////////////////////////////////////////////////////////////
        $TowerHostList = array();
        $ret = instance_execution($dbobj,$ansdrv,$lv_ans_if_info,$TowerHostList,$tgt_driver_id,$tgt_execution_no,$intJournalSeqNo,$tgt_execution_row,$cln_execution_row);
        
        ////////////////////////////////////////////////////////////////
        // 処理対象の作業インスタンスのステータス更新
        ////////////////////////////////////////////////////////////////
        $ret = cm_InstanceRecodeUpdate($dbobj,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$cln_execution_row,$FREE_LOG);
        if($ret === false) {
            $error_flag = 1; throw new Exception( $FREE_LOG );
        }

        ////////////////////////////////////////////////////////////////
        // ステータスが実行中以外は終了
        ////////////////////////////////////////////////////////////////
        if($cln_execution_row['STATUS_ID'] != '3') {
            throw new Exception( "" );
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // $ary[50069] = "[処理]処理対象インスタンス 作業確認開始(作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50069",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
        }

        $ststus_update = true;
        $cln_execution_row        = array();    // 作業インスタンス更新用配列初期化
        $tgt_execution_row        = array();    // 作業インスタンス更新用配列初期化
        $intJournalSeqNo          = "";         // 作業インスタンス履歴 シーケンス番号初期化
        while(true){
            sleep($interval);
        
            if($ststus_update === true) {
                unset($cln_execution_row);
                unset($tgt_execution_row);
                unset($intJournalSeqNo);
                ////////////////////////////////////////////////////////////////
                // トランザクション開始
                ////////////////////////////////////////////////////////////////
                $ret = cm_transactionStart($tgt_execution_no,$FREE_LOG);
                if($ret === false) {
                    $error_flag = 1; throw new Exception( $FREE_LOG );
                }

                ////////////////////////////////////////////////////////////////
                // シーケンスをロックし履歴シーケンス採番
                ////////////////////////////////////////////////////////////////
                $dbobj->ClearLastErrorMsg();
                $intJournalSeqNo = cm_dbaccessGetSequence($dbobj,$vg_exe_ins_msg_table_jnl_seq,$tgt_execution_no,$FREE_LOG);
                if($intJournalSeqNo === false) {
                    $error_flag = 1; throw new Exception($FREE_LOG);
                }

                ////////////////////////////////////////////////////////////////
                // コミット(レコードロックを解除)                             
                ////////////////////////////////////////////////////////////////
                if(cm_transactionCommit($tgt_execution_no,$FREE_LOG) !== true) {
                    $error_flag = 1; throw new Exception( $FREE_LOG );
                }

                ////////////////////////////////////////////////////////////////
                // トランザクション終了
                ////////////////////////////////////////////////////////////////
                cm_transactionExit($tgt_execution_no);
 
                /////////////////////////////////////////////////////////////////
                // 処理対象の作業インスタンス情報取得
                /////////////////////////////////////////////////////////////////
                $ret = cm_getEexecutionInstanceRow($dbobj,$tgt_execution_no,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$tgt_execution_row,$FREE_LOG);
                if($ret === false) {
                    $error_flag = 1; throw new Exception( $FREE_LOG );
                }

                // クローン作製
                $cln_execution_row = $tgt_execution_row;
            }

            $db_update_need = false;
            $ret = instance_checkcondition($dbobj,$ansdrv,$lv_ans_if_info,$TowerHostList,$tgt_driver_id,$tgt_execution_no,$intJournalSeqNo,$tgt_execution_row,$cln_execution_row,$db_update_need);
            
            $ststus_update = false;
            // ステータスが更新されたか判定
            //if($cln_execution_row['STATUS_ID'] != $tgt_execution_row['STATUS_ID']) {
            if(($cln_execution_row['STATUS_ID'] != $tgt_execution_row['STATUS_ID']) ||
               ($db_update_need == true)) {
                $ststus_update = true;
                ////////////////////////////////////////////////////////////////
                // 処理対象の作業インスタンスのステータス更新
                // トランザクションは使用しない。
                ////////////////////////////////////////////////////////////////
                $ret = cm_InstanceRecodeUpdate($dbobj,$vg_exe_ins_msg_table_name,$vg_exe_ins_msg_table_jnl_name,$cln_execution_row,$FREE_LOG);
                if($ret === false) {
                    $error_flag = 1; throw new Exception( $FREE_LOG );
                }
            }
            if( $cln_execution_row['STATUS_ID'] == 5 ||
                $cln_execution_row['STATUS_ID'] == 6 ||
                $cln_execution_row['STATUS_ID'] == 7 ||
                $cln_execution_row['STATUS_ID'] == 8 ) {
                break;
            }
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50070",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
        }

    }
    catch (Exception $e){
        // 作業インスタンスの状態が処理中/実行中で例外が発生した場合の状態遷移は実装しない。
        // 親プロセスで想定外エラーを設定する。
        if( $log_level    === 'DEBUG' ||
            $error_flag   != 0        ||
            $warning_flag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            $ret = cm_transactionRollBack($tgt_execution_no,$FREE_LOG);
            if($ret === false) {
                require ($root_dir_path . $log_output_php );
            }
            // トランザクション終了
            cm_transactionExit($tgt_execution_no);
        }
    }
    
    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            //$ary[50054] = "[処理]プロシージャ終了(異常) (作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50054",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
        }
        
        exit(2);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            //$ary[50055] = "[処理]プロシージャ終了(警告) (作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50055",array($tgt_execution_no));
            require ($root_dir_path . $log_output_php );
        }
        
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            //$ary[50053] = "[処理]プロシージャ終了(正常) (作業No.:{})";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50053",array($tgt_execution_no));
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
                                            $conductor_instance_no,
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
                                            $in_root_dir_path,$in_log_output_php,
                                            $in_ans_if_info){
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
        $pioneer_template_host_vars = array();
        $vault_vars       = array(); 
        $vault_host_vars_file_list = array(); 

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
                                                   $in_operation_id,
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
                                                   $symphony_instance_no,
                                                   $conductor_instance_no
                                                   );
        if($ret <> true){
            // 例外処理へ
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00010004"));
            require ($in_root_dir_path . $in_log_output_php );
               
            return false;
        }

        ///////////////////////////////////////////////////////////////////////////////////////
        // Ansible Engin /virtualenv Path確認
        ///////////////////////////////////////////////////////////////////////////////////////
        $ret = $in_ansdrv->AnsibleEnginVirtualenvPathCheck();
        if($ret === false) {
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
                                         $hostinfolist,
                                         $in_winrm_id, 
                                         $in_ans_if_info);
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
            //   $pioneer_template_host_vars:
            //                      変数一覧返却配列 pioneer template用 変数一覧返却配列 (passwordColumnの具体値がansible-vaultで暗号化)
            //                      [ホスト名(IP)][ 変数名 ]=>具体値
            //   $vault_vars:       PasswordCoulumn変数一覧(Pioneer用)
            //                      [ 変数名 ] = {{ 変数名 }} 
            //   $ina_vault_host_vars_file_list:  PasswordCoulumn変数のみのホスト変数一覧(Pioneer用)
            //                      [ホスト名(IP)][ 変数名 ] = 具体値
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
                                            $pioneer_template_host_vars,
                                            $vault_vars,
                                            $vault_host_vars_file_list,
                                            $host_child_vars,
                                            $DB_child_vars_master,
                                            $in_ans_if_info);
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
                                                $MultiArray_vars_list,
                                                $All_vars_list,
                                                $in_ans_if_info);

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
                                                     $pioneer_template_host_vars,
                                                     $vault_vars, 
                                                     $vault_host_vars_file_list,
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

    function getAnsiblePlaybookOptionParameter($OptionParameter,&$JobTemplatePropertyParameterAry,&$JobTemplatePropertyNameAry,&$ErrorMsgAry,&$ParamAryExc)
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

        // 除外リスト定義
        $ExcList = array();
        // 除外された場合のリスト定義
        $ParamAryExc = $ParamAry;

        // KEY SHRT_KEYが混在した場合の対応
        $KeyShortChk = array();

        // tags skipのvalue用の配列
        $TagSkipValueKey = array();
        $TagSkipValueKeyS = array();

        foreach($JobTemplatePropertyInfo as $JobTemplatePropertyRecode) {

            // 除外リストの初期化
            $ExcList = array();

            // KEY SHORT_KEYチェック用配列の初期化
            $KeyShortChk = array();

            // tags skipのvalue用の配列の初期化
            $TagSkipValueKey = array();
            $TagSkipValueKeyS = array();

            $JobTemplatePropertyNameAry[$JobTemplatePropertyRecode['PROPERTY_NAME']]= 0;   
            if(strlen(trim( $JobTemplatePropertyRecode['KEY_NAME'] )) != 0) {
                $ret = makeJobTemplateProperty($JobTemplatePropertyRecode['KEY_NAME'],
                                               $JobTemplatePropertyRecode['PROPERTY_TYPE'],
                                               $JobTemplatePropertyRecode['PROPERTY_NAME'],
                                               $ParamAry,
                                               $ErrorMsgAry,
                                               $ExcList,
                                               $TagSkipValueKey,
                                               $VerboseCnt);


                // 重複データの場合のみ
                $exclst_cnt = count($ExcList);
                $i=0;
                if($exclst_cnt >= 1){
                    foreach($ExcList as $exc_ele) {
                        // 最後のデータは削除しない
                        if($exclst_cnt - 1 === $i){
                            // KEYのチェックデータ格納
                            array_push($KeyShortChk,$exc_ele);
                            break;
                        }
                        $j=0;
                        foreach($ParamAryExc as $elementAry){
                            // 除外リストと一致した場合
                            if(strcmp($exc_ele,$elementAry) ===  0){
                                // 要素を削除
                                unset($ParamAryExc[$j]);
                                $ParamAryExc = array_values($ParamAryExc);
                                break;
                            }
                            $j++;
                        }
                        $i++;
                    }
                }

                if($ret === false) {
                    $result=false;
                }

                // 除外リストの初期化
                $ExcList = array();

            }
            if(strlen(trim( $JobTemplatePropertyRecode['SHORT_KEY_NAME'] )) != 0) {
                $ret = makeJobTemplateProperty($JobTemplatePropertyRecode['SHORT_KEY_NAME'],
                                               $JobTemplatePropertyRecode['PROPERTY_TYPE'],
                                               $JobTemplatePropertyRecode['PROPERTY_NAME'],
                                               $ParamAry,
                                               $ErrorMsgAry,
                                               $ExcList,
                                               $TagSkipValueKeyS,
                                               $VerboseCnt);

                // 重複データの場合のみ
                $exclst_cnt = count($ExcList);
                $i=0;
                if($exclst_cnt >= 1){
                    foreach($ExcList as $exc_ele) {
                        // 最後のデータは削除しない
                        if($exclst_cnt - 1 === $i){
                            // KEY SHORTのチェックデータ格納
                            array_push($KeyShortChk,$exc_ele);
                            break;
                        }
                        $j=0;
                        foreach($ParamAryExc as $elementAry){
                            // 除外リストと一致した場合
                            if(strcmp($exc_ele,$elementAry) ===  0){
                                // 要素を削除
                                unset($ParamAryExc[$j]);
                                $ParamAryExc = array_values($ParamAryExc);
                                break;
                            }
                            $j++;
                        }
                        $i++;
                    }
                }

                if($ret === false) {
                    $result=false;
                }
            }

            // KEY SHORTのチェック
            $k = 0;
            if(count($KeyShortChk) >= 2) {
            // KEY SHORTそれぞれ存在する場合,先頭データを削除
                foreach($ParamAryExc as $ParamAryExcKeyChk){
                    if(strcmp($ParamAryExcKeyChk,$KeyShortChk[0]) === 0){
                        unset($ParamAryExc[$k]);
                        $ParamAryExc = array_values($ParamAryExc);
                        break;
                    }
                    if(strcmp($ParamAryExcKeyChk,$KeyShortChk[1]) === 0){
                        unset($ParamAryExc[$k]);
                        $ParamAryExc = array_values($ParamAryExc);
                        break;
                    }
                    $k++;
                }
            }

            // tags,skipの場合','区切りに修正する
            if((strcmp('--tags=',$JobTemplatePropertyRecode['KEY_NAME']) === 0) || (strcmp('--skip-tags=',$JobTemplatePropertyRecode['KEY_NAME']) === 0)) {
            // tags,skipの場合、','区切りにしてデータを渡す（文字列整形）
                $ValuesParam='';
                $l=0;
                $m=0;
                foreach($ParamAry as $ParamAryTmpTabSkip){
                    $ChkParamString = '-' . $ParamAryTmpTabSkip . ' ';
                    // KEYのtagsのvalueを取得
                    if((preg_match('/^' . '--tags=' . '/', $ChkParamString) === 1) && (strcmp('--tags=',$JobTemplatePropertyRecode['KEY_NAME']) === 0)) {
                        $ValuesParam = $ValuesParam . $TagSkipValueKey[$l] .',';
                        $l++;
                    }
                    // KEYのskipのvalueを取得
                    if((preg_match('/^' . '--skip-tags=' . '/', $ChkParamString) === 1) && (strcmp('--skip-tags=',$JobTemplatePropertyRecode['KEY_NAME']) === 0)) {
                        $ValuesParam = $ValuesParam . $TagSkipValueKey[$l] .',';
                        $l++;
                    }
                    // KEY SHORTのtagsのvalueを取得
                    if((preg_match('/^' . '-t(\s)+' . '/', $ChkParamString) === 1) && (strcmp('-t(\s)+',$JobTemplatePropertyRecode['SHORT_KEY_NAME']) === 0)) {
                        $ValuesParam = $ValuesParam . $TagSkipValueKeyS[$m] .',';
                        $m++;
                    }
                }
                // 末尾の','を削除
                $ValuesParam = rtrim($ValuesParam, ',');

                // リストのデータを書き換え
                $n=0;
                foreach($ParamAryExc as $ParamAryTmpKeyChg){
                    $ChkParamStringChg = '-' . $ParamAryTmpKeyChg . ' ';
                    if((preg_match('/^' . '--tags=' . '/', $ChkParamStringChg) === 1) && (strcmp('--tags=',$JobTemplatePropertyRecode['KEY_NAME']) === 0)) {
                        // 要素を書き換え
                        $ParamAryExc[$n] = '-tags=' . $ValuesParam;
                        break;
                    }
                    if((preg_match('/^' . '--skip-tags=' . '/', $ChkParamStringChg) === 1) && (strcmp('--skip-tags=',$JobTemplatePropertyRecode['KEY_NAME']) === 0)) {
                        // 要素を書き換え
                        $ParamAryExc[$n] = '-skip-tags=' . $ValuesParam;
                        break;
                    }
                    if((preg_match('/^' . '-t(\s)+' . '/', $ChkParamStringChg) === 1) && (strcmp('-t(\s)+',$JobTemplatePropertyRecode['SHORT_KEY_NAME']) === 0)) {
                        // 要素を書き換え
                        $ParamAryExc[$n] = 't ' . $ValuesParam;
                        break;
                    }
                    $n++;
                }
            }

            // JobTemplatePropertyParameterAryの作成
            if(strlen(trim( $JobTemplatePropertyRecode['KEY_NAME'] )) != 0) {
                $ret = makeJobTemplatePropertyParameterAry($JobTemplatePropertyRecode['KEY_NAME'],
                                                           $JobTemplatePropertyRecode['PROPERTY_TYPE'],
                                                           $JobTemplatePropertyRecode['PROPERTY_NAME'],
                                                           $JobTemplatePropertyParameterAry,
                                                           $ParamAryExc,
                                                           $VerboseCnt);
            }
            if(strlen(trim( $JobTemplatePropertyRecode['SHORT_KEY_NAME'] )) != 0) {
                $ret = makeJobTemplatePropertyParameterAry($JobTemplatePropertyRecode['SHORT_KEY_NAME'],
                                                           $JobTemplatePropertyRecode['PROPERTY_TYPE'],
                                                           $JobTemplatePropertyRecode['PROPERTY_NAME'],
                                                           $JobTemplatePropertyParameterAry,
                                                           $ParamAryExc,
                                                           $VerboseCnt);
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

    function makeJobTemplateProperty($KeyString,$PropertyType,$PropertyName,$ParamAry,&$ErrorMsgAry,&$ExcList,&$TagSkipValueKey,&$VerboseCnt) {
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

                // $ChkParamStringを除外リストに設定(追加)
                array_push($ExcList,$ParamString);

                switch($PropertyType) {
                case DF_JobTemplateKeyValueProperty:
                    if(@strlen(@trim($PropertyAry[1])) == 0) {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000001",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 

                    if(preg_match('/-f/',$KeyString)){
                        if(is_numeric(trim($PropertyAry[1])) !== true){
                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000003",array($ChkParamString));
                            $ErrorMsgAry[] = $FREE_LOG;
                            $result = false;
                            break;
                        }
                    }

                    # tags skipの対応
                    if((strcmp($KeyString,'--tags=') === 0) || (strcmp($KeyString,'-t(\s)+') === 0) ||
                       (strcmp($KeyString,'--skip-tags=') === 0)) {
                        array_push($TagSkipValueKey,trim($PropertyAry[1]));
                    }
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
                    $VerboseCnt = $VerboseCnt + @strlen(@trim($ParamString));
                    break; 
                case DF_JobTemplatebooleanTrueProperty:
                    if(@strlen(@trim($PropertyAry[1])) != 0)
                    {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000003",array($ChkParamString));
                        $ErrorMsgAry[] = $FREE_LOG;
                        $result = false;
                        break;
                    } 
                    break; 
                case DF_JobTemplateExtraVarsProperty:
                    if(@strlen(@trim($PropertyAry[1])) == 0)
                    {
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000001",array($ChkParamString));
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
                    break; 
                }
            }
        }
        return $result;
    }

    function makeJobTemplatePropertyParameterAry($KeyString,$PropertyType,$PropertyName,&$JobTemplatePropertyParameterAry,$ParamAry,$VerboseCnt) {
        global $objMTS;
        $result = true;

        foreach($ParamAry as $ParamString) {
            $ChkParamString = '-' . $ParamString . ' ';
            $ret = preg_match('/^' . $KeyString . '/', $ChkParamString);
            if($ret === 1)
            {

                $PropertyAry = preg_split('/^' . $KeyString . '/', $ChkParamString);

                switch($PropertyType) {
                case DF_JobTemplateKeyValueProperty:
                    $JobTemplatePropertyParameterAry[$PropertyName] = trim($PropertyAry[1]);
                    break;
                case DF_JobTemplateVerbosityProperty:
                    if($VerboseCnt >= 6) {
                        $VerboseCnt = 5;
                    }
                    $JobTemplatePropertyParameterAry[$PropertyName] = $VerboseCnt;
                    break; 
                case DF_JobTemplatebooleanTrueProperty:
                    $JobTemplatePropertyParameterAry[$PropertyName] = true;
                    break; 
                case DF_JobTemplateExtraVarsProperty:
                    $ExtVarString = trim($PropertyAry[1]);
                    $ExtVarString = trim($ExtVarString,"\"");
                    $ExtVarString = trim($ExtVarString,"\'");
                    $ExtVarString = str_replace("\\n","\n",$ExtVarString);
                    $JobTemplatePropertyParameterAry[$PropertyName] = $ExtVarString;
                    break; 
                }
            }
        }
        return $result;
    }
    function makeExtraVarsParameter(&$ExtVarString) {

        $ExtVarString = trim($ExtVarString,"\'");
        $ExtVarString = trim($ExtVarString,"\"");
        $ExtVarString = str_replace("\\n","\n",$ExtVarString);

        // JSON形式のチェック
        $chk_json = json_decode($ExtVarString,true);
        if($chk_json !== null) {
            return true;
        }

        // YAML形式のチェック
        $val = @yaml_parse($ExtVarString);
        if($val !== false) {
            return true;
        }

        return false;
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
    // 処理対象の作業インスタンス実行
    function instance_execution($dbobj,$in_ansdrv,$in_ans_if_info,&$TowerHostList,$in_driver_id,$in_execution_no,$in_JournalSeqNo,$in_execution_row,&$out_execution_row) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $db_access_user_id;
        global $warning_flag;
        global $error_flag;

        global $root_dir_path;
        global $log_output_php;

        global $comDBaccess_php;
        // 再読み込み
        require_once ($root_dir_path . $comDBaccess_php);

        $RequestURI                      = "/restapi/ansible_driver/construct.php";
        $Method                          = 'POST';
        $rh_abort_file_name              = 'RHABORT';
        $intNumPadding                   = 10;
        
        $file_subdir_zip_input           = 'FILE_INPUT';
        $zip_temp_save_dir               = $root_dir_path . '/temp';
        $zip_input_file                  = "";
        $zip_input_file_dir              = "";
 
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51070",array($in_execution_no));
            require ($root_dir_path . $log_output_php );
        }
        // 処理対象のドライランモードのリストを格納
        $tgt_run_mode = $in_execution_row['RUN_MODE'];

        // 処理対象の並列実行数のリストを格納 (pioneer)
        if(strlen($in_execution_row['I_ANS_PARALLEL_EXE']) == 0){
            $tgt_exec_count = '0';
        } else{
            $tgt_exec_count = $in_execution_row['I_ANS_PARALLEL_EXE'];
        }

        // symphonyインスタンス番号を退避
        $tgt_symphony_instance_no = $in_execution_row['SYMPHONY_INSTANCE_NO'];

        // conductorインスタンス番号を退避
        $tgt_conductor_instance_no = $in_execution_row['CONDUCTOR_INSTANCE_NO'];
        
        require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_global_variables.php");

        try {
        
            // ANSIBLEインタフェース情報をローカル変数に格納
            $lv_ans_storage_path_lnx  = $in_ans_if_info['ANSIBLE_STORAGE_PATH_LNX'];
            $lv_ans_storage_path_ans  = $in_ans_if_info['ANSIBLE_STORAGE_PATH_ANS'];
            $lv_sym_storage_path_ans  = $in_ans_if_info['SYMPHONY_STORAGE_PATH_ANS'];
            $lv_ans_protocol          = $in_ans_if_info['ANSIBLE_PROTOCOL'];
            $lv_ans_hostname          = $in_ans_if_info['ANSIBLE_HOSTNAME'];
            $lv_ans_port              = $in_ans_if_info['ANSIBLE_PORT'];
            $lv_ans_access_key_id     = $in_ans_if_info['ANSIBLE_ACCESS_KEY_ID'];
            $lv_ans_secret_access_key = ky_decrypt( $in_ans_if_info['ANSIBLE_SECRET_ACCESS_KEY'] );
            $lv_ansible_exec_options  = $in_ans_if_info['ANSIBLE_EXEC_OPTIONS'];
            $lv_anstwr_organization   = $in_ans_if_info['ANSTWR_ORGANIZATION'];
            $lv_anstwr_auth_token     = $in_ans_if_info['ANSTWR_AUTH_TOKEN'];
            $lv_ans_exec_user         = $in_ans_if_info['ANSIBLE_EXEC_USER'];
            $lv_ans_exec_mode         = $in_ans_if_info['ANSIBLE_EXEC_MODE'];

            $proxySetting              = array();
            $proxySetting['address']   = $in_ans_if_info["ANSIBLE_PROXY_ADDRESS"];
            $proxySetting['port']      = $in_ans_if_info["ANSIBLE_PROXY_PORT"];

            // Towerの接続情報
            $lv_anstwr_protocol       = $in_ans_if_info['ANSTWR_PROTOCOL'];
            $lv_anstwr_hostname       = $in_ans_if_info['ANSTWR_HOSTNAME'];
            $lv_anstwr_port           = $in_ans_if_info['ANSTWR_PORT'];

            if(strlen(trim($lv_ans_exec_user)) == 0) {
                // ansible-playbookの実行ユーザーのデフォルトを設定
                $lv_ans_exec_user = 'root';
            }

            $prepare_err_flag            = 0;        // 準備段階での異常フラグ(1：異常発生)
            $restapi_err_flag            = 0;        // REST APIでの異常フラグ(1：異常発生)
            $RequestContents             = array();  // REST API向けのリクエストコンテンツ(JSON)を格納
            
            $prepare_err_flag = 0;
            $restapi_err_flag = 0;
 
            $exec_mode             = $in_execution_row["EXEC_MODE"];
            $exec_playbook_hed_def = $in_execution_row["I_ANS_PLAYBOOK_HED_DEF"];
            $exec_option           = $in_execution_row["I_ANS_EXEC_OPTIONS"];
            $winrm_flg             = "";
            switch($in_driver_id){
            case DF_LEGACY_DRIVER_ID:
            case DF_LEGACY_ROLE_DRIVER_ID:
                $winrm_flg = $in_execution_row["I_ANS_WINRM_ID"];
                break;
            case DF_PIONEER_DRIVER_ID:
                break;
            }

            // Ansibleコマンド実行ユーザー設定
            $in_ansdrv->setAnsibleExecuteUser($lv_ans_exec_user);

            // データベースからansibleで実行する情報取得し実行ファイル作成
            $ret = CreateAnsibleExecFilesfunction($in_driver_id,
                                                  $in_ansdrv,
                                                  $in_execution_no,
                                                  $tgt_symphony_instance_no,
                                                  $tgt_conductor_instance_no,
                                                  $in_execution_row["PATTERN_ID"],
                                                  $in_execution_row["OPERATION_NO_UAPK"],
                                                  // ホストアドレス指定方式（I_ANS_HOST_DESIGNATE_TYPE_ID）
                                                  // null or 1 がIP方式 2 がホスト名方式
                                                  $in_execution_row["I_ANS_HOST_DESIGNATE_TYPE_ID"],
                                                  // pioneerにはI_ANS_WINRM_IDがないので変数に変更
                                                  $winrm_flg,
                                                  $exec_mode,
                                                  $exec_playbook_hed_def,
                                                  $exec_option,
                                                  $vg_OrchestratorSubId_dir,
                                                  $root_dir_path,$log_output_php,
                                                  $in_ans_if_info);
            if($ret !== true) {
                $prepare_err_flag = 1;
            }

            $tmp_array_dirs = $in_ansdrv->getAnsibleWorkingDirectories($vg_OrchestratorSubId_dir,$in_execution_no);
            $zip_data_source_dir = $tmp_array_dirs[3];

            $JobTemplatePropertyParameterAry  = array();
            $JobTemplatePropertyNameAry       = array();
            $ErrorMsgAry                      = array();

            if($prepare_err_flag == 0){
                if($lv_ans_exec_mode == DF_EXEC_MODE_ANSIBLE) {
                    if(strlen(trim($lv_ans_access_key_id)) == 0) {
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000100");
                        $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }
                    if(strlen(trim($lv_ans_secret_access_key)) == 0) {
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000101");
                        $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }
                } else {
                    if(strlen(trim($lv_anstwr_auth_token)) == 0) {
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-6000102");
                        $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }

                    if(strlen(trim($lv_anstwr_hostname)) == 0) {
                        $item = $objMTS->getSomeMessage("ITAANSIBLEH-MNU-1203041");
                        $ErrorMsg = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-2004",array($item));
                        $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
                        $prepare_err_flag = 1;
                    }
                    // Git関連情報 必須入力確認
                    $chkObj  = new TowerHostListGitInterfaceParameterCheck();
                    $ValueColumnName= 'Value';
                    $ColumnArray = array();
                    $ColumnArray['ANS_GIT_HOSTNAME'][$ValueColumnName]             = $in_ans_if_info['ANS_GIT_HOSTNAME'];
                    $ColumnArray['ANS_GIT_USER'][$ValueColumnName]                 = $in_ans_if_info['ANS_GIT_USER'];
                    $ColumnArray['ANS_GIT_SSH_KEY_FILE'][$ValueColumnName]         = $in_ans_if_info['ANS_GIT_SSH_KEY_FILE'];
                    $RequiredCloumnName = 'Required';
                    $ColumnArray['ANS_GIT_HOSTNAME'][$RequiredCloumnName]          = true;
                    $ColumnArray['ANS_GIT_USER'][$RequiredCloumnName]              = true;
                    $ColumnArray['ANS_GIT_SSH_KEY_FILE'][$RequiredCloumnName]      = true;
                    $DelFlagCloumnName = 'del_flag_cloumn';
                    $ColumnArray['ANS_GIT_HOSTNAME'][$DelFlagCloumnName]           = "";
                    $ColumnArray['ANS_GIT_USER'][$DelFlagCloumnName]               = "";
                    $ColumnArray['ANS_GIT_SSH_KEY_FILE'][$DelFlagCloumnName]       = "";

                    // エラーメッセージに表示するカラム名設定
                    $MyNameCloumnName = 'CloumnName';
                    $errormsg    = sprintf("%s/%s",   $objMTS->getSomeMessage('ITAANSIBLEH-MNU-1200010000'),
                                                      $objMTS->getSomeMessage("ITAANSIBLEH-MNU-1200010100"));
                    $ColumnArray['ANS_GIT_HOSTNAME'][$MyNameCloumnName]     = sprintf("%s",$objMTS->getSomeMessage('ITAANSIBLEH-ERR-2004',array($errormsg)));
                    $errormsg    = sprintf("%s/%s/%s",$objMTS->getSomeMessage('ITAANSIBLEH-MNU-1200010000'),
                                                      $objMTS->getSomeMessage("ITAANSIBLEH-MNU-1200010200"),
                                                      $objMTS->getSomeMessage("ITAANSIBLEH-MNU-1200010300"));
                    $ColumnArray['ANS_GIT_USER'][$MyNameCloumnName]         = sprintf("%s",$objMTS->getSomeMessage('ITAANSIBLEH-ERR-2004',array($errormsg)));
                    $errormsg    = sprintf("%s/%s/%s",$objMTS->getSomeMessage('ITAANSIBLEH-MNU-1200010000'),
                                                      $objMTS->getSomeMessage("ITAANSIBLEH-MNU-1200010200"),
                                                      $objMTS->getSomeMessage("ITAANSIBLEH-MNU-1200010400"));
                    $ColumnArray['ANS_GIT_SSH_KEY_FILE'][$MyNameCloumnName] = sprintf("%s",$objMTS->getSomeMessage('ITAANSIBLEH-ERR-2004',array($errormsg)));

                    $retBool = $chkObj->ParameterCheck($lv_ans_exec_mode, $ColumnArray, $ValueColumnName, $MyNameCloumnName, $RequiredCloumnName);
                    if($retBool !== true) {
                        $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$retBool);
                        $prepare_err_flag = 1;
                    }
                }
            }

            if($prepare_err_flag == 0){
                // ansible-playbookのオプションパラメータを確認
                getMovementAnsibleExecOption($in_execution_row["PATTERN_ID"],$MovementAnsibleExecOption);
                $OptionParameter = $lv_ansible_exec_options . ' ' . $MovementAnsibleExecOption;

                $OptionParameter = str_replace("--verbose","-v",$OptionParameter);

                // Tower実行の場合にオプションパラメータをチェックする。
                if($lv_ans_exec_mode != DF_EXEC_MODE_ANSIBLE) {

                    // Pioneerの場合の並列実行数のパラメータ設定 
                    switch($in_driver_id){
                    case DF_PIONEER_DRIVER_ID:
                        if((strlen(trim($tgt_exec_count)) != 0) &&
                           (trim($tgt_exec_count) != '0')) {
                            $OptionParameter .= sprintf(" -f %s ",$tgt_exec_count); 
                        }
                        break;
                    }

                    // 重複除外用のオプションパラメータ
                    $ParamAryExc = array();

                    $ret = getAnsiblePlaybookOptionParameter($OptionParameter,$JobTemplatePropertyParameterAry,$JobTemplatePropertyNameAry,$ErrorMsgAry,$ParamAryExc);
                    if($ret === false)
                    {
                        $prepare_err_flag = 1;
                        foreach($ErrorMsgAry as $ErrorMsg) {
                            $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$ErrorMsg);
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
                /////////////////////////////////////////////////////
                // 投入データ用ZIPファイル作成                     //
                /////////////////////////////////////////////////////
                $ret = fileCreateZIPFile($zip_data_source_dir,
                                         $in_execution_no,
                                         $vg_exe_ins_input_file_dir,
                                         $zip_temp_save_dir,
                                         $file_subdir_zip_input,
                                         'InputData_',
                                         $zip_input_file,
                                         $zip_input_file_dir,
                                         "ITAANSIBLEH-ERR-58051",
                                         "ITAANSIBLEH-ERR-58052",
                                         "ITAANSIBLEH-ERR-58053",
                                         "ITAANSIBLEH-STD-58101");
                if($ret === true) {

                    $out_execution_row['FILE_INPUT']        = $zip_input_file;

                    /////////////////////////////////////////////////////
                    // 投入データ用ZIP 履歴ファイル作成                //
                    /////////////////////////////////////////////////////
                    $ret = fileCreateHistoryZIPFile($in_execution_no,
                                                    $zip_input_file,
                                                    $zip_input_file_dir,
                                                    $in_JournalSeqNo);
                    if($ret === false) {
                        // ZIPファイル作成の作成に失敗しても警告フラグを設定し先に進む
                        $warning_flag = 1;
                    }
                } else {
                    // ZIPファイル作成の作成に失敗しても警告フラグを設定し先に進む
                    $warning_flag = 1;
                }
            }

            // 準備で異常がなければREST APIをコール
            // 実行エンジンを判定
            if($lv_ans_exec_mode == DF_EXEC_MODE_ANSIBLE) {
                if($prepare_err_flag == 0){
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51066",array($in_execution_no));
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
                            "EXE_NO"=>$in_execution_no,
                            "PARALLEL_EXE"=>$tgt_exec_count,
                            "RUN_MODE"=>$tgt_run_mode,
                            "EXEC_USER"=>$lv_ans_exec_user,
                            //Ansible Engin Virtualenv Path
                            'ANS_ENGINE_VIRTUALENV_NAME'=>$in_ansdrv->GetEngineVirtualenvName());

                    $rest_api_response = ansible_restapi_access( $lv_ans_protocol,
                                                                 $lv_ans_hostname,
                                                                 $lv_ans_port,
                                                                 $lv_ans_access_key_id,
                                                                 $lv_ans_secret_access_key,
                                                                 $RequestURI,
                                                                 $Method,
                                                                 $RequestContents,
                                                                 $proxySetting );


                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51067",array($in_execution_no,$rest_api_response['StatusCode']));
                        require ($root_dir_path . $log_output_php );
                    }

                
                    ////////////////////////////////////////////////////////////////
                    // 結果判定                                                   //
                    ////////////////////////////////////////////////////////////////
                    if( $rest_api_response['StatusCode'] != 200 ){
                        // REST APIでの異常フラグをON
                        $restapi_err_flag = 1;
                    
                        // 異常メッセージ
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-51068",array($in_execution_no));
                        $in_ansdrv->LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                        require ($root_dir_path . $log_output_php );
                        $FREE_LOG = print_r($rest_api_response,true);
                        require ($root_dir_path . $log_output_php );
                    }
                }

                ////////////////////////////////////////////////////////////////
                // 作業インスタンス情報の処理結果設定                          //
                ////////////////////////////////////////////////////////////////
                // 正常(REST APIコールでHTTPレスポンスが200)の場合
                if( $prepare_err_flag == 0 &&
                    $restapi_err_flag == 0 ){
                        
                    // 変数バインド準備
                    $out_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
                    $out_execution_row['STATUS_ID']         = "3";
                        
                }
                // 異常 or 警告の場合
                else{
                    // 警告フラグON
                    $warning_flag = 1;
                        
                    // 変数バインド準備
                    $out_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
                    $out_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
                    $out_execution_row['STATUS_ID']         = "7";
                        
                }
            } else {
                if($prepare_err_flag == 0){
                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51068",$in_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                    ////////////////////////////////////////////////////////////////
                    // REST APIコール                                             //
                    ////////////////////////////////////////////////////////////////
                    $UIExecLogPath  = $in_ansdrv->getAnsible_out_Dir() . "/" . "exec.log";
                    $UIErrorLogPath = $in_ansdrv->getAnsible_out_Dir() . "/" . "error.log";

                    ////////////////////////////////////////////////////////////////
                    // AnsibleTowerから実行                                       //
                    ////////////////////////////////////////////////////////////////
                    $MultipleLogMark = "";
                    $MultipleLogFileJsonAry = ""; // 定義のみ値は返却されない
                    // $Statusは未使用
                    $TowerHostList = array();
                    $ret = AnsibleTowerExecution(DF_EXECUTION_FUNCTION,$in_ans_if_info,$TowerHostList,$out_execution_row,$in_ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$MultipleLogMark,$MultipleLogFileJsonAry,$Status,$JobTemplatePropertyParameterAry,$JobTemplatePropertyNameAry);

                    if ( $log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51069",$in_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                    // マルチログか判定
                    if($MultipleLogMark != "") {
                        if($out_execution_row['MULTIPLELOG_MODE'] != $MultipleLogMark) {
                            $out_execution_row['MULTIPLELOG_MODE'] = $MultipleLogMark;
                        }
                    }
                }
                ////////////////////////////////////////////////////////////////
                // 作業インスタンス情報の処理結果設定                          //
                ////////////////////////////////////////////////////////////////
                if($prepare_err_flag == 0){
                    // STATUS_ID/TIME_STARTはAnsibleTowerExecution内で設定
                }
                else {
                    // 警告フラグON
                    $warning_flag = 1;
                                                
                    // 変数バインド準備
                    $out_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
                    $out_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
                    $out_execution_row['STATUS_ID']         = "7";
                }
            }

            // トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-51071",array($in_execution_no,$restapi_err_flag));
                require ($root_dir_path . $log_output_php );
            }

            return true;
        }
        catch (Exception $e){
            // 異常フラグON
            $error_flag = 1;
 
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );

            // 変数バインド準備
            $out_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
            $out_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
            $out_execution_row['STATUS_ID']         = "7";
            return false;
        }
    }
    function instance_checkcondition($dbobj,$in_ansdrv,$in_ans_if_info,$TowerHostList,$in_driver_id,$in_execution_no,$in_JournalSeqNo,$in_execution_row,&$out_execution_row,&$db_update_need) {
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $db_access_user_id;
        global $warning_flag;
        global $error_flag;

        global $root_dir_path;
        global $log_output_php;

        global $comDBaccess_php;
        // 再読み込み
        require_once ($root_dir_path . $comDBaccess_php);
    
        $restapi_err_flag           = 0;        // REST APIでの異常フラグ(1：異常発生)
        $RequestContents            = array();  // REST API向けのリクエストコンテンツ(JSON)を格納
        $ResponseStatus             = '';       // REST APIから返却されたexecutionidを格納
        $ResponseResultdata         = array();  // REST APIから返却されたexecutionidを格納

        $sql_exec_flag =  0;
                
        $RequestURI                 = "/restapi/ansible_driver/statuscheck.php";
        $Method                     = 'GET';
        $intNumPadding              = 10;
    
        $file_subdir_zip_result     = 'FILE_RESULT';
        $zip_temp_save_dir          = $root_dir_path . '/temp';
        $zip_result_file            = "";
        $zip_result_file_dir        = "";

        require ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_global_variables.php");

        try {
            // ANSIBLEインタフェース情報をローカル変数に格納
            $lv_ans_storage_path_lnx  = $in_ans_if_info['ANSIBLE_STORAGE_PATH_LNX'];
            $lv_ans_storage_path_ans  = $in_ans_if_info['ANSIBLE_STORAGE_PATH_ANS'];
            $lv_sym_storage_path_ans  = $in_ans_if_info['SYMPHONY_STORAGE_PATH_ANS'];
            $lv_ans_protocol          = $in_ans_if_info['ANSIBLE_PROTOCOL'];
            $lv_ans_hostname          = $in_ans_if_info['ANSIBLE_HOSTNAME'];
            $lv_ans_port              = $in_ans_if_info['ANSIBLE_PORT'];
            $lv_ans_access_key_id     = $in_ans_if_info['ANSIBLE_ACCESS_KEY_ID'];
            $lv_ans_secret_access_key = ky_decrypt( $in_ans_if_info['ANSIBLE_SECRET_ACCESS_KEY'] );
            $lv_ansible_exec_options  = $in_ans_if_info['ANSIBLE_EXEC_OPTIONS'];
            $lv_anstwr_organization   = $in_ans_if_info['ANSTWR_ORGANIZATION'];
            $lv_anstwr_auth_token     = $in_ans_if_info['ANSTWR_AUTH_TOKEN'];
            $lv_ans_exec_user         = $in_ans_if_info['ANSIBLE_EXEC_USER'];
            $lv_ans_exec_mode         = $in_ans_if_info['ANSIBLE_EXEC_MODE'];

            $proxySetting              = array();
            $proxySetting['address']   = $in_ans_if_info["ANSIBLE_PROXY_ADDRESS"];
            $proxySetting['port']      = $in_ans_if_info["ANSIBLE_PROXY_PORT"];

            // Towerの接続情報
            $lv_anstwr_protocol       = $in_ans_if_info['ANSTWR_PROTOCOL'];
            $lv_anstwr_hostname       = $in_ans_if_info['ANSTWR_HOSTNAME'];
            $lv_anstwr_port           = $in_ans_if_info['ANSTWR_PORT'];

            if(strlen(trim($lv_ans_exec_user)) == 0) {
                // ansible-playbookの実行ユーザーのデフォルトを設定
                $lv_ans_exec_user = 'root';
            }

            $tmp_array_dirs = $in_ansdrv->getAnsibleWorkingDirectories($vg_OrchestratorSubId_dir,$in_execution_no);
            $zip_data_source_dir = $tmp_array_dirs[4];
                
            // 実行エンジンを判定
            if($lv_ans_exec_mode == DF_EXEC_MODE_ANSIBLE) {

                ////////////////////////////////////////////////////////////////
                // REST APIコール                                             //
                ////////////////////////////////////////////////////////////////            
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50071",array($in_execution_no));
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
                        "EXE_NO"=>$in_execution_no);
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
                                                             $RequestContents,
                                                             $proxySetting );
    
                ////////////////////////////////////////////////////////////////
                // REST API結果判定                                           //
                ////////////////////////////////////////////////////////////////
                $restapi_err_flag = 0;
                $sql_exec_flag =  0;
                $Status = RESTAPIResponsCheck($in_execution_no,
                                              $rest_api_response,
                                              $restapi_err_flag);
    
                // REST APIの戻り値が異常の場合にログ出力
                if( $restapi_err_flag != 0 ){
                    //  REST APIの戻り値が異常は$Statusを想定外エラーにしている
                    $sql_exec_flag =  1;
    
                    // 警告フラグON
                    $warning_flag = 1;
                    
                    // 警告メッセージ出力
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50019",$in_execution_no);
                    require ($root_dir_path . $log_output_php );

                    // REST APIの戻り値を出力
                    $FREE_LOG = "REST API Response\n" . print_r($rest_api_response,true);
                    require ($root_dir_path . $log_output_php );

                }
            } else {
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50073",array($in_execution_no));
                    require ($root_dir_path . $log_output_php );
                }

                $sql_exec_flag =  0;
                $UIExecLogPath  = $in_ansdrv->getAnsible_out_Dir() . "/" . "exec.log";
                $UIErrorLogPath = $in_ansdrv->getAnsible_out_Dir() . "/" . "error.log";
                $Status = 0;

                ////////////////////////////////////////////////////////////////
                // AnsibleTowerから実行                                       //
                ////////////////////////////////////////////////////////////////
                $MultipleLogMark = "";
                $MultipleLogFileJsonAry = "";
                $ret = AnsibleTowerExecution(DF_CHECKCONDITION_FUNCTION,$in_ans_if_info,$TowerHostList,$out_execution_row,$in_ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$MultipleLogMark,$MultipleLogFileJsonAry,$Status);

                // マルチログか判定
                if($MultipleLogMark != "") {
                    if($out_execution_row['MULTIPLELOG_MODE'] != $MultipleLogMark) {
                        $out_execution_row['MULTIPLELOG_MODE'] = $MultipleLogMark;
                        $db_update_need = true;
                    }
                }
                // マルチログファイルリスト
                if($MultipleLogFileJsonAry!= "") {
                    if($out_execution_row['LOGFILELIST_JSON'] != $MultipleLogFileJsonAry) {
                        $out_execution_row['LOGFILELIST_JSON'] = $MultipleLogFileJsonAry;
                        $db_update_need = true;
                    }
                }
                // マルチログファイルの情報をDBに反映
                if($db_update_need === true) {
                    $out_execution_row['JOURNAL_SEQ_NO']    = $in_JournalSeqNo;
                    $out_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                }

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
                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50074",array($in_execution_no));
                    require ($root_dir_path . $log_output_php );
                }

            }
            if ( $log_level === 'DEBUG' ){
                // 状態をログに出力
                $FREE_LOG = sprintf("ExecutionNo:%s  Status:%s",$in_execution_no,$Status);
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

                /////////////////////////////////////////////////////
                // 実行結果ファイルをTowerから転送
                /////////////////////////////////////////////////////
                // 実行エンジンを判定
                if($lv_ans_exec_mode != DF_EXEC_MODE_ANSIBLE) {
                    // 戻り値は確認しない。
                    $MultipleLogMark = "";        // 定義のみ値は返却されない
                    $MultipleLogFileJsonAry = ""; // 定義のみ値は返却されない
                    AnsibleTowerExecution(DF_RESULTFILETRANSFER_FUNCTION,$in_ans_if_info,$TowerHostList,$in_execution_row,$in_ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$MultipleLogMark,$MultipleLogFileJsonAry,$Status);
                }

                /////////////////////////////////////////////////////
                // 結果データ用ZIPファイル作成                     //
                /////////////////////////////////////////////////////
                $ret = fileCreateZIPFile($zip_data_source_dir,
                                         $in_execution_no,
                                         $vg_exe_ins_result_file_dir,
                                         $zip_temp_save_dir,
                                         $file_subdir_zip_result,
                                         'ResultData_',
                                         $zip_result_file,
                                         $zip_result_file_dir,
                                         'ITAANSIBLEH-ERR-59051',
                                         'ITAANSIBLEH-ERR-59052',
                                         'ITAANSIBLEH-ERR-59053',
                                         'ITAANSIBLEH-STD-59101');
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
                
                // 変数バインド準備
                $out_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
                $out_execution_row['STATUS_ID']         = $Status;
                $out_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                
            }
            else{
                ////////////////////////////////////////////////////////////////
                // 遅延を判定                                                 //
                ////////////////////////////////////////////////////////////////
                // 遅延タイマを取得
                $time_limit = $in_execution_row['I_TIME_LIMIT'];

                $delay_flag = 0;
                
                // ステータスが実行中(3)、かつ制限時間が設定されている場合のみ遅延判定する
                if( $in_execution_row['STATUS_ID'] == 3 && $time_limit != "" ){
                    // 開始時刻(「UNIXタイム.マイクロ秒」)を生成
                    $varTimeDotMirco = convFromStrDateToUnixtime($in_execution_row['TIME_START'], true );
                    // 開始時刻(マイクロ秒)＋制限時間(分→秒)＝制限時刻(マイクロ秒)
                    $varTimeDotMirco_limit = $varTimeDotMirco + ($time_limit * 60); //単位（秒）
                    
                    // 現在時刻(「UNIXタイム.マイクロ秒」)を生成
                    $varTimeDotNowStd = getMircotime(0);

                    // 制限時刻と現在時刻を比較
                    if( $varTimeDotMirco_limit < $varTimeDotNowStd ){
                        $delay_flag = 1;
                        
                        // トレースメッセージ
                        if ( $log_level === 'DEBUG' ){
                            //$ary[50030] = "[処理]遅延を検出しました。(作業No.:{})";
                            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50030",$in_execution_no);
                            require ($root_dir_path . $log_output_php );
                        }
                    }
                }
                
                if( $delay_flag == 0 &&
                    $log_level === 'DEBUG' ){
                    //$ary[50031] = "[処理]遅延無し。(作業No.:{})";
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50031",$in_execution_no);
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
                        
                    ////////////////////////////////////////////////////////////////////////
                    // 「C_EXECUTION_MANAGEMENT」のUPDATE文を作成(実行中 or 実行中(遅延)  //
                    ////////////////////////////////////////////////////////////////////////
                    
                    // 変数バインド準備
                    $out_execution_row['STATUS_ID']         = $Status;
                    $out_execution_row['LAST_UPDATE_USER']  = $db_access_user_id;
                    
                }
            }
            // SQL(UPDATE)をEXECUTEすると判断した場合
            if( $sql_exec_flag == 1 ){

                // 遅延中以外の場合に結果データ用ZIP 履歴ファイル作成
                if($out_execution_row['STATUS_ID'] != 4) {

                    /////////////////////////////////////////////////////
                    // 結果データ用ZIP 履歴ファイル作成                //
                    /////////////////////////////////////////////////////
                    $ret = fileCreateHistoryZIPFile($in_execution_no,
                                                    $zip_result_file,
                                                    $zip_result_file_dir,
                                                    $in_JournalSeqNo);

                    $out_execution_row['FILE_RESULT'] = $zip_result_file;
                }
                $out_execution_row['JOURNAL_SEQ_NO']  = $in_JournalSeqNo;
            }
            
            // 実行エンジンを判定
            if($lv_ans_exec_mode != DF_EXEC_MODE_ANSIBLE) {
                if( $Status == 5 ||
                    $Status == 6 ||
                    $Status == 7 ||
                    $Status == 8 ) {
                    ////////////////////////////////////////////////////////////////
                    // AnsibleTower ゴミ掃除 戻り値は確認しない。
                    ////////////////////////////////////////////////////////////////
                    if($log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50075",$in_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }

                    // 戻り値は確認しない。
                    $MultipleLogMark = "";        // 定義のみ値は返却されない
                    $MultipleLogFileJsonAry = ""; // 定義のみ値は返却されない
                    AnsibleTowerExecution(DF_DELETERESOURCE_FUNCTION,$in_ans_if_info,$TowerHostList,$in_execution_row,$in_ansdrv->getAnsible_out_Dir(),$UIExecLogPath,$UIErrorLogPath,$MultipleLogMark,$MultipleLogFileJsonAry,$Status);

                    if($log_level === 'DEBUG' ){
                        $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50076",$in_execution_no);
                        require ($root_dir_path . $log_output_php );
                    }
                }
            }
            return true;
        }
        catch (Exception $e){
            // 異常フラグON
            $error_flag = 1;
 
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );

            // 変数バインド準備
            $out_execution_row['TIME_START']        = "DATETIMEAUTO(6)";
            $out_execution_row['TIME_END']          = "DATETIMEAUTO(6)";
            $out_execution_row['STATUS_ID']         = "7";
            return false;
        }
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
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-50072",array($in_execution_no,$ResponseStatus));
            require ($root_dir_path . $log_output_php );
        }
                   
        // resultdata情報を取り出し
        $ansibleWfResult = '';
        $ResponseResultdata = '';
        if( array_key_exists( "resultdata", $in_rest_api_response['ResponsContents'] ) ){
            // 取り出し
            $ResponseResultdata = $in_rest_api_response['ResponsContents']["resultdata"];

            if( @array_key_exists( "ANSIBLE_WF_RESULT", $ResponseResultdata ) ){
                // 取り出し
                $ansibleWfResult = $ResponseResultdata["ANSIBLE_WF_RESULT"];
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
            }
            else
            {  //異常終了時
                // ステータスを「完了(異常)」とする
                $chk_Status = 6;
            }
        }
        //異常の場合
        elseif( $ResponseStatus == "FAILED"){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;
        }
        //緊急停止の場合
        elseif( $ResponseStatus == "FORCED"){
            // ステータスを「緊急停止」とする
            $chk_Status = 8;
        }
        //未実行の場合
        elseif( $ResponseStatus == "NOT RUNNING"){
            // ステータスを「想定外エラー」とする
            $chk_Status = 7;
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
        }
        return $chk_Status;
    }            
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //  入力/結果ZIPファイル作成
    // パラメータ
    //  $in_zip_data_source_dir:     inディレクトリ
    //  $in_execution_no:            作業実行番号
    //  $in_exe_ins_input_file_dir:  ZIPファイル格納ホームディレクトリ
    //                               $root_dir_path . "/uploadfiles/21000xxxxx";
    //  $in_zip_temp_save_dir:       作業ディレクトリ      = $root_dir_path . '/temp';
    //  $in_zip_subdir:              入力/出力ディレクトリ
    //                                 入力:FILE_INPUT   出力:FILE_RESULT
    //  $in_zip_file_pfx:            入力/出力ファイルブラフィックス
    //                                 入力:InputData_   出力:ResultData_
    //  $in_zip_file_name:           ZIPファイル名返却
    //  $in_utn_file_dir:            ZIPファイル格納ディレクトリ 
    //                                  $in_exe_ins_input_file_dir/作業実行番号
    //
    //  ZIPファイルディレクトリ構成
    //   /uploadfiles/21000xxxxx/FILE_INPUT/作業番号(10桁)/InputData_0000000001.zip
    //   /uploadfiles/21000xxxxx/FILE_INPUT/作業番号(10桁)/old/履歴通番/InputData_0000000001.zip
    //
    // 戻り値
    //   true:正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////
    function fileCreateZIPFile($in_zip_data_source_dir,
                               $in_execution_no,
                               $in_exe_ins_input_file_dir,
                               $in_zip_temp_save_dir,
                               $in_zip_subdir,
                               $in_zip_file_pfx,
                              &$in_zip_file_name,
                              &$in_utn_file_dir,
                               $msg_code_1,
                               $msg_code_2,
                               $msg_code_3,
                               $msg_code_4){
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        
        $intNumPadding = 10;

        if( count( glob( $in_zip_data_source_dir . "/"."*" ) ) > 0 ){
            //----ZIPファイルを作成する
            $in_zip_file_name = $in_zip_file_pfx . str_pad( $in_execution_no, $intNumPadding, "0", STR_PAD_LEFT ) . '.zip';

            // OSコマンドでzip圧縮する
            $tmp_str_command = "cd " . $in_zip_data_source_dir . "; zip -r " . $in_zip_temp_save_dir . "/" . $in_zip_file_name . " .";
            $tmp_str_command .= " -x ssh_key_files\/* -x winrm_ca_files\/*";

            shell_exec( $tmp_str_command );

            $in_utn_file_dir = $in_exe_ins_input_file_dir . "/" . $in_zip_subdir . "/" . str_pad( $in_execution_no, $intNumPadding, "0", STR_PAD_LEFT );

            if( ! is_dir( $in_exe_ins_input_file_dir) ){
                if( !mkdir( $in_exe_ins_input_file_dir, 0777,true) ){
                    // 事前準備を中断
                    $FREE_LOG = $objMTS->getSomeMessage($msg_code_1,array($in_execution_no));
                    require ($root_dir_path . $log_output_php );

                    return false;
                }
                if( !chmod( $in_exe_ins_input_file_dir, 0777 ) ){
                    // 事前準備を中断
                    $FREE_LOG = $objMTS->getSomeMessage($msg_code_2,array($in_execution_no));
                    require ($root_dir_path . $log_output_php );
                    return false;
                }
            } else {
                if( !chmod( $in_exe_ins_input_file_dir, 0777 ) ){
                    // 事前準備を中断
                    $FREE_LOG = $objMTS->getSomeMessage($msg_code_2,array($in_execution_no));
                    require ($root_dir_path . $log_output_php );
                    return false;
                }
            }

            if( !is_dir( $in_utn_file_dir ) ){
                // ここ(UTNのdir)だけは再帰的に作成する
                if( !mkdir( $in_utn_file_dir, 0777,true) ){
                    // 事前準備を中断
                    $FREE_LOG = $objMTS->getSomeMessage($msg_code_1,array($in_execution_no));
                    require ($root_dir_path . $log_output_php );

                    return false;
                }
                if( !chmod( $in_utn_file_dir, 0777 ) ){
                    // 事前準備を中断
                    $FREE_LOG = $objMTS->getSomeMessage($msg_code_2,array($in_execution_no));
                    require ($root_dir_path . $log_output_php );
                    return false;
                }
            }

            // zipファイルを正式な置き場に移動
            rename( $in_zip_temp_save_dir . "/" . $in_zip_file_name,
                    $in_utn_file_dir . "/" . $in_zip_file_name );

            // zipファイルの存在を確認
            if( !file_exists( $in_utn_file_dir . "/" . $in_zip_file_name ) ){
                $prepare_err_flag = 1;
                
                $FREE_LOG = $objMTS->getSomeMessage($msg_code_3,array($in_execution_no));
                require ($root_dir_path . $log_output_php );

                return false;
            }
        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage($msg_code_4,array($in_execution_no,basename($in_zip_file_name)));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //  履歴用入力/結果ZIPファイル作成
    // パラメータ
    //  $in_zip_file_name:           ZIPファイル名返却
    //  $in_utn_file_dir:            ZIPファイル格納ディレクトリ 
    //                                  $in_exe_ins_input_file_dir/作業実行番号
    //  $in_intJournalSeqNo:         ジャーナル通番
    //
    //  ZIPファイルディレクトリ構成
    //  　/uploadfiles/21000xxxxx/FILE_RESULT/作業番号(10桁)/ResultData_0000000001.zip
    //  　/uploadfiles/21000xxxxx/FILE_RESULT/作業番号(10桁)/old/履歴通番(10桁)/ResultData_0000000001.zip
    //
    // 戻り値
    //   true:正常　false:異常
    ////////////////////////////////////////////////////////////////////////////////    
    function fileCreateHistoryZIPFile($in_execution_no,$in_zip_file_name,$in_utn_file_dir,$in_intJournalSeqNo){
        global $objDBCA;
        global $objMTS;
        global $db_model_ch;
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $intNumPadding = 10;

        $tmp_jnl_file_dir_trunk = $in_utn_file_dir . "/old";
        $tmp_jnl_file_dir_focus = $tmp_jnl_file_dir_trunk . "/" . str_pad( $in_intJournalSeqNo, $intNumPadding, "0", STR_PAD_LEFT );

        // 履歴フォルダへコピー
        if( is_dir($tmp_jnl_file_dir_trunk) === false){
            if( !mkdir( $tmp_jnl_file_dir_trunk, 0777 ) ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59851",array($in_execution_no));
                require ($root_dir_path . $log_output_php );
                return false;
            }
        }
        if( is_dir($tmp_jnl_file_dir_focus) === false){
            if( !mkdir( $tmp_jnl_file_dir_focus, 0777 ) ){
                $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59852",array($in_execution_no));
                require ($root_dir_path . $log_output_php );
                return false;
            }
        }
        $boolCopy = copy( $in_utn_file_dir . "/" . $in_zip_file_name, $tmp_jnl_file_dir_focus . "/". $in_zip_file_name);
        if( $boolCopy === false ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-59853",array($in_execution_no));
            require ($root_dir_path . $log_output_php );
            return false;
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //$ary[59101] = "[処理]結果ディレクトリを圧縮(作業No.:{} 圧縮ファイル:{}) ";
            $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-STD-59801",array($in_execution_no,basename($in_zip_file_name)));
            require ($root_dir_path . $log_output_php );
        }

        return true;
    }
?>
