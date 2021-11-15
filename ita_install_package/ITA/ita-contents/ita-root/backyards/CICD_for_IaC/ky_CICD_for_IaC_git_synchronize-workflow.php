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
//      CICD For IaC Gitリモートリポジトリ同期 親プロセス
//
//      function 
//        ExecuteChildProcess
//        getTargetRepoListRow
//        chkRepoListAndSyncStatusRow
//        getRunningChildProcess
//        chkRunningChildProcess
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
    $php_req_gate_php                = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php                  = '/libs/commonlibs/common_db_connect.php';
    $log_output_php                  = '/libs/backyardlibs/backyard_log_output.php';

    /////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag                    = 0; // 警告フラグ(1：警告発生)
    $error_flag                      = 0; // 異常フラグ(1：異常発生)

    $db_access_user_id               = -130000;

    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////
    
    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require_once ($root_dir_path . $php_req_gate_php );

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

        ///////////////////////////////////////////////////
        // ファイル組み込み
        ///////////////////////////////////////////////////
        require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");

        require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_functions.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_db_access.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/table_definition.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/local_definition.php');
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/ControlGit.php');

        global $db_model_ch;
        global $objDBCA;
        global $objMTS;

        $SyncStatusNameobj = new TD_SYNC_STATUS_NAME_DEFINE($objMTS);

        $cmDBobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);

        $DBobj = new LocalDBAccessClass($db_model_ch,$cmDBobj,$objDBCA,$objMTS,$db_access_user_id,$logfile,$log_level);

        $LFCobj = new LocalFilesControl();

        ///////////////////////////////////////////////////
        // トランザクションロック待ち時間設定
        ///////////////////////////////////////////////////
        $ret = $DBobj->setInnodbLockWaitTimeout();
        if($ret !== true) {
            // 異常フラグON 
            $error_flag = 1;

            // 例外処理へ
            // トランザクションロック待ち時間(innodb_lock_wait_timeout)の設定に失敗しました。
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1015");  //3009
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }
     
        ///////////////////////////////////////////////////
        // トランザクション開始
        ///////////////////////////////////////////////////
        $ret = $DBobj->transactionStart();
        if($ret !== true) {
            // 異常フラグON 
            $error_flag = 1;

            // 例外処理へ
            // "トランザクション処理に失敗しました。";
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");   // 2002
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }

        /////////////////////////////////////////////////////////////////
        // 参照のみなのでシーケンスロックはしない
        /////////////////////////////////////////////////////////////////

        /////////////////////////////////////////////////////////////////
        // リポジトリ管理と同期状態管理テーブルのレコード紐付け確認
        ////////////////////////////////////////////////////////////////
        $ret = chkRepoListAndSyncStatusRow();

        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            // リモートリポジトリ管理と同期状態テーブルのアクセスに失敗しました。
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1016");   // 2014
            $addlogstr = $DBobj->GetLastErrorMsg();
            $addlogstr = $ret;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception($FREE_LOG);
        }

        /////////////////////////////////////////////////////////////////
        // 同期実行中リモートリポジトリの子プロセス取得
        /////////////////////////////////////////////////////////////////
        $ps_array = array();
        $ret = getRunningChildProcess($ps_array);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "同期処理中のリモートリポジトリ管理の確認に失敗しました。
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1017"); // 2015
            $addlogstr = $ret;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception($FREE_LOG);
        }

        /////////////////////////////////////////////////////////////////
        // リポジトリ管理から処理対象のリポジトリ取得
        /////////////////////////////////////////////////////////////////
        $tgtRepoListRow   = array();
        $ret = getTargetRepoListRow($tgtRepoListRow);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "リモートリポジトリ管理のアクセスに失敗しました。
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1018");  //2016
            $addlogstr = $ret;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception($FREE_LOG);
        }
        foreach($tgtRepoListRow as $row) {

            // テーブルの排他処理をしていないのでchkRepoListAndSyncStatusRowからgetTargetRepoListRowの間で
            // リモートリポジトリ管理にレコードが追加された場合のガード処理
            if($row['REPO_ROW_ID'] != $row['ROW_ID']) {
                // 次回の周期で処理
                continue;
            }
            // 現在時間
            $ExecuteTime = time();
            $RepoId      = $row['REPO_ROW_ID'];
            switch($row['SYNC_STATUS_ROW_ID']){
            // 同期状態を判定
            case $SyncStatusNameobj->NORMAL():   // 正常
                $ExecMode = "Normal";   // 資材一覧の更新はしない。
                break;
            default:
                $ExecMode =  "Remake";  // リモートリポジトリに差分がなくても資材一覧の更新する
                break;
            }
            // 現在時間 >= 前回同期時間 + 同期周期
            $Child_go = false;
            if(strlen($row["SYNC_INTERVAL"]) == 0) {
                 $row["SYNC_INTERVAL"] = 60;
            }
            if((int)$ExecuteTime >= ((int)$row["SYNC_LAST_UNIXTIME"] + (int)$row["SYNC_INTERVAL"])) {

                $Child_go = true;

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    // [処理]リモートリポジトリの同期時間検出(リポジトリ項番:{}))"
                    $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2013",array($RepoId)); 
                    require ($root_dir_path . $log_output_php );
                }

                ///////////////////////////////////////////////////////////////////////////////////////
                // 処理対象のリモートリポジトリに対して同期実行中リモートリポジトリの子プロセス確認
                ///////////////////////////////////////////////////////////////////////////////////////
                $ret = chkRunningChildProcess($RepoId,$ps_array);
                if($ret === true) {
                    continue;
                }

                ///////////////////////////////////////////////////////////////////////////////////////
                // 処理対象のリモートリポジトリに対して同期処理する子プロセス起動
                ///////////////////////////////////////////////////////////////////////////////////////
                $ret = ExecuteChildProcess($RepoId,$ExecMode);
                // 戻り値確認不要
            }
        }

        $ret = $DBobj->transactionCommit();
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;
            
            // 例外処理へ
            // "トランザクション処理に失敗しました。";
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003"); //2002
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }

    }
    catch (Exception $e){
        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            $ret = $DBobj->transactionRollBack();
            if($ret === false) {
                // 異常フラグON
                $error_flag = 1;

                // "トランザクション処理に失敗しました。";
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");   // 2002
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                require ($root_dir_path . $log_output_php );
            }
            // トランザクション終了
            $ret = $DBobj->transactionExit();
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

    function ExecuteChildProcess($RepoId,$EcecMode) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $LFCobj;

        $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");

        // 改行コードが付いている場合に取り除く
        $php_command = str_replace("\n","",$php_command);

        $ChildProcessFileName = $LFCobj->getChildProcessExecName();
        $cmd = sprintf("%s %s/backyards/CICD_for_IaC/%s LINE_%010s %s > /dev/null &",
                       $php_command,
                       $root_dir_path,
                       $LFCobj->getChildProcessExecName(),
                       $RepoId,
                       $EcecMode);

        // プロセス起動 バックグラウンドで起動しているのでエラーは判定不可。
        exec($cmd,$arry_out,$return_var);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            // [処理]Gitリポジトリ同期プロセス起動(リポジトリ項番:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2006",array($RepoId,$EcecMode)); 
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    function getTargetRepoListRow(&$tgtRepoListRow) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $SyncStatusNameobj;

        $tgtRepoListRow = array();
        $TDRepoobj    = new TD_B_CICD_REPOSITORY_LIST();
        $TDSyncStsobj = new TD_T_CICD_SYNC_STATUS();
        
        // リポジトリ管理取得
        // 条件: 廃止レコード以外　自動同期が有効　同期状態が異常以外 同期状態.同期実行状態が未実行
        $sqlBody = "SELECT 
                      TAB_A.*,
                      TAB_B.ROW_ID AS ROW_ID,
                      CASE WHEN TAB_B.SYNC_LAST_TIMESTAMP IS NULL THEN '0' 
                           ELSE UNIX_TIMESTAMP(TAB_B.SYNC_LAST_TIMESTAMP) END SYNC_LAST_UNIXTIME
                    FROM 
                      %s  TAB_A
                      LEFT JOIN  %s TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.ROW_ID )
                    WHERE 
                          TAB_A.DISUSE_FLAG         =  '0'
                      AND TAB_A.AUTO_SYNC_FLG       =  :AUTO_SYNC_FLG
                      AND (TAB_A.SYNC_STATUS_ROW_ID <> :SYNC_STATUS_ROW_ID OR TAB_A.SYNC_STATUS_ROW_ID is NULL)";

        $sqlBody = sprintf($sqlBody,$TDRepoobj->getTableName(),$TDSyncStsobj->getTableName());
          
        $arrayBind                       = array();
        $arrayBind['AUTO_SYNC_FLG']      = TD_B_CICD_REPOSITORY_LIST::C_AUTO_SYNC_FLG_ON;
        $arrayBind['SYNC_STATUS_ROW_ID'] = $SyncStatusNameobj->ERROR();

        $objQuery = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;
            
            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        $num_of_rows = $objQuery->effectedRowCount();
        if($num_of_rows === 0) {
            // リポジトリなし
            unset($objQuery);
            return true;
        }
        $tgtRepoListRow = array();
        while ( $row = $objQuery->resultFetch() ){
            $tgtRepoListRow[] = $row;
        }
        unset($objQuery);
        return true;
    }

    function chkRepoListAndSyncStatusRow() {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        $TDRepoobj    = new TD_B_CICD_REPOSITORY_LIST();
        $TDSyncStsobj = new TD_T_CICD_SYNC_STATUS();
        //リポジトリ管理にあるリモートリポジトリで同期状態管理テーブルにないリモートリポジトリを探す。
        $sqlBody   = "SELECT 
                        TAB_A.*,
                        TAB_B.ROW_ID AS SYNC_STATUS_ROW_ID
                      FROM
                        %s TAB_A
                        LEFT JOIN %s TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.ROW_ID )";
        $sqlBody = sprintf($sqlBody,$TDRepoobj->getTableName(),$TDSyncStsobj->getTableName());
        $arrayBind = array();
        $objQuery  = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        $SyncStatusAdd = array();
        while($row = $objQuery->resultFetch()) {
            if(strlen($row['SYNC_STATUS_ROW_ID']) == 0) {
                $SyncStatusAdd[] = $row['REPO_ROW_ID'];
            }
        }

        // 同期状態管理テーブルにあるリモートリポジトリでリポジトリ管理にないリモートリポジトリを探す。
        // 廃止状態はチェックしない
        $sqlBody   = "SELECT
                        TAB_A.ROW_ID AS SYNC_STATUS_ROW_ID,
                        TAB_A.*,
                        TAB_B.REPO_ROW_ID
                      FROM 
                        %s   TAB_A
                        LEFT JOIN %s TAB_B ON ( TAB_A.ROW_ID = TAB_B.REPO_ROW_ID )";
        $sqlBody = sprintf($sqlBody,$TDSyncStsobj->getTableName(),$TDRepoobj->getTableName());
        $arrayBind = array();
        $objQuery  = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        $SyncStatusDel = array();
        while($row = $objQuery->resultFetch()) {
            if(strlen($row['REPO_ROW_ID']) == 0) {
                $SyncStatusDel[] = $row['SYNC_STATUS_ROW_ID'];
            }
        }

        $ret = $TDSyncStsobj->setConfigWithoutJournal($cmDBobj);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDSyncStsobj->GetLastErrorMsg());
            return $FREE_LOG;
        }

        // 同期状態管理テーブルに必要なレコード追加
        foreach($SyncStatusAdd as $repoId) {
            $dbAcction = "INSERT";
            $ColumnValueArray   = $TDSyncStsobj->getColumndefineWithoutJournal();
            $ColumnConfigArray  = $TDSyncStsobj->setColumnConfigAttrWithoutJournal();

            // 最終同期日時をNULLに設定
            $ColumnValueArray['ROW_ID'] = $repoId;
            $ColumnValueArray['SYNC_LAST_TIMESTAMP'] = null;
            $BindArray         = array();

            $objQueryArray = $DBobj->makeSelectSQLString($dbAcction,$BindArray,$TDSyncStsobj,$ColumnConfigArray,$ColumnValueArray);
            if($objQueryArray === false) {
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                // "データベースのアクセスに失敗しました。";
                $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                return $FREE_LOG;
            }
            $sqlBody = $objQueryArray[1];
            $arrayBind = $objQueryArray[2];
            $ret = $DBobj->dbaccessExecute($sqlBody, $arrayBind);
            if($ret === false) {
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                // "データベースのアクセスに失敗しました。";
                $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                return $FREE_LOG;
            }
        }
        // 同期状態管理テーブルに不要なレコード削除
        foreach($SyncStatusDel as $repoId) {
            $sqlBody = sprintf("DELETE FROM %s WHERE ROW_ID=%s",
                                $TDSyncStsobj->getTableName(),
                                $repoId);
            $arrayBind = array();
            $ret = $DBobj->dbaccessExecute($sqlBody, $arrayBind);
            if($ret === false) {
                // 異常フラグON
                $error_flag = 1;

                // 例外処理へ
                // "データベースのアクセスに失敗しました。";
                $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                return $FREE_LOG;
            }
        }
        return true;
    }
    function getRunningChildProcess(&$ps_array) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $LFCobj;

        // usleep time (ms)
        $sleep_time = 3;

        $ChildProcessFileName = $LFCobj->getChildProcessExecName();

        // psコマンドでky_CICD_for_IaC_git_synchronize-child-workflow.phpの起動リストを作成
        // psコマンドがマレに起動プロセスリストを取りこぼすことがあるので3回分を作成
        $strBuildCommand   = "ps -efw|grep $ChildProcessFileName 2>&1";
        $ps_array1 = array();
        $ret       = 0;
        exec($strBuildCommand,$ps_array1,$ret);
        if($ret != 0) {
            $logstr = $strBuildCommand;
            $logstr .= implode("\n",$ps_array1);
            return $logstr;
        }

        $sleep_time = 50000;
        usleep($sleep_time);

        $ps_array2 = array();
        $ret       = 0;
        exec($strBuildCommand,$ps_array2,$ret);
        if($ret != 0) {
            $logstr = $strBuildCommand;
            $logstr .= implode("\n",$ps_array2);
            return $logstr;
        }

        $sleep_time = 100000;
        usleep($sleep_time);

        $ps_array3 = array();
        $ret       = 0;
        exec($strBuildCommand,$ps_array3,$ret);
        if($ret != 0) {
            $logstr = $strBuildCommand;
            $logstr .= implode("\n",$ps_array3);
            return $logstr;
        }

        $ps_array = array();
        $ps_array[] = $ps_array1;
        $ps_array[] = $ps_array2;
        $ps_array[] = $ps_array3;

        return true;
    }

    function chkRunningChildProcess($RepoId,$ps_array) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $LFCobj;

        $ps_array1 = $ps_array[0];
        $ps_array2 = $ps_array[1];
        $ps_array3 = $ps_array[2];

        // 処理中リモートリポジトリを示す起動パラメータ
        $ChildProcessExecParam = sprintf("LINE_%010s",$RepoId);

        // 子プロセス起動確認
        $tgt_hit = false;
        foreach($ps_array1 as $line) {
            $ret = preg_match("/$ChildProcessExecParam/",$line);
            if($ret == 1){
                $tgt_hit = true;
                break;
            }
        }
        if($tgt_hit === false) {
            foreach($ps_array2 as $line) {
                $ret = preg_match("/$ChildProcessExecParam/",$line);
                if($ret == 1){
                    $tgt_hit = true;
                    break;
                }
            }
            if($tgt_hit === false) {
                foreach($ps_array3 as $line) {
                    $ret = preg_match("/$ChildProcessExecParam/",$line);
                    if($ret == 1){
                        $tgt_hit = true;
                        break;
                    }
                }
            }
        }
        if($tgt_hit === true) {
            // 子プロセス起動中
            return true;
        }
        // 子プロセス未起動 
        return false;
    }
?>
