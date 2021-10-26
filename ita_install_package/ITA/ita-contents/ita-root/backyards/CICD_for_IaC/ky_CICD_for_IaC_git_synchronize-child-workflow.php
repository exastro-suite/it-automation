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
///////////////////////////////////////////////////////////////////////////////
//
//  【概要】
//      CICD For IaC Git リモートリポジトリ同期処理　子プロセス(リモートリポジトリ単位)
//
//   function 
//      ExceptionRecive
//      getRolesPath
//      getLocalCloneFileList
//      GitPull
//      CreateLocalClone
//      LocalCloneRemoteRepoChk
//      LocalCloneBranchChk
//      getRepoListRow
//      setDefaultUIDisplayMsg
//      setUIDisplayMsg
//      makeReturnArray
//      analysisReturnArray
//      LockPkeySequence
//      getMatlListRecodes
//      MatlListMerge
//      MatlListDisuseUpdate
//      MatlListInsert
//      MatlListRolesRecodeUpdate
//      UpdateSyncStatusRecode
//      UpdateRepoListSyncStatus
//      UpdateRepoListRecode
//      getAuthType
//
///////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    ////////////////////////////////////////////////////////////////
    // 起動パラメータ取得   LINE_リモードリポジトリ項番 同期モード
    ///////////////////////////////////////////////////////////////
    if($argc != 3) {
        echo ("Invalid parameter count");
        exit(2);
    }
    $ary = explode("_", $argv[1]);
    if(count($ary) != 2) {
        echo ("Invalid parameter argv[1]");
        exit(2);
    }
    if(is_numeric($ary[1]) === false) {
        echo ("Invalid parameter argv[1]");
        exit(2);
    }
    $RepoId = (int)$ary[1];
    $MatlListUpdateExeFlg            = false;  // 資材一覧更新判定 true: リモートリポジトリに差分がなくても資材一覧の更新  false: 差分がなければ資材一覧の更新しない
    if($argv[2] == "Normal") {
        $MatlListUpdateExeFlg = false;
    } else {
        $MatlListUpdateExeFlg = true;
    }

    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_" . $RepoId . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL');

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

    $UIDisplayMsg                    = "";     // UIに表示するメッセージ

    $RepoListSyncStatusUpdate_Flg    = false;  // リモートリポジトリ管理状態更新　状態 false:未更新 true:更新済

    $SyncTimeUpdate_Flg              = false;  // 最終同期時間更新　状態 false:未更新 true:更新済

    $CloneExeFlg                     = false;  // Git Clone実施判定         true:実施 false:未実施

    $MargeExeFlg                     = true;   // Git pullで変更検出判定    true:差分あり　false:差分なし

    $db_access_user_id               = -130000;

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

        $DBobj = new LocalDBAccessClass($db_model_ch,$cmDBobj,$objDBCA,$objMTS,$db_access_user_id,$logfile,$log_level,$RepoId);

        $LFCobj = new LocalFilesControl();       

        $TDMatlobj = new TD_B_CICD_MATERIAL_LIST();

        $ret = $TDMatlobj->setConfig($cmDBobj);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDMatlobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }

        $TDRepoobj = new TD_B_CICD_REPOSITORY_LIST();
        $ret = $TDRepoobj->setConfig($cmDBobj);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDMatlobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }

        $TDMatlLinkobj = new TD_B_CICD_MATERIAL_LINK_LIST();
        $ret = $TDMatlLinkobj->setConfig($cmDBobj);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDMatlobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }

        $TDSyncStsobj = new TD_T_CICD_SYNC_STATUS();

        ///////////////////////////////////////////////////
        // トランザクションロック待ち時間設定
        ///////////////////////////////////////////////////
        $ret = $DBobj->setInnodbLockWaitTimeout();
        if($ret !== true) {
            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

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
        // GitCommand結果解析文字列ファイル読み込み
        ///////////////////////////////////////////////////
        $GitCmdRsltParsStrFileNamePath = $LFCobj->getGitCmdRsltParsStrFileNamePath();
        $GitCmdRsltParsAry = parse_ini_file($GitCmdRsltParsStrFileNamePath, true);
        if($GitCmdRsltParsAry === false) {
            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // ファイル読み込み失敗
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1014",array($GitCmdRsltParsStrFileNamePath)); 
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDMatlobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        } 

        ///////////////////////////////////////////////////
        // トランザクション開始
        ///////////////////////////////////////////////////
        $ret = $DBobj->transactionStart();
        if($ret !== true) {
            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // 異常フラグON
            $error_flag = 1;

            // "トランザクション処理に失敗しました。";
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  //2002
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }

        /////////////////////////////////////////////////////////////////
        // git制御を並列で動かす為にシーケンスロックはしないで
        // リモートリポジトリ管理の情報を取得
        /////////////////////////////////////////////////////////////////
        $RepoListRow = array();
        $ret = getRepoListRow($RepoId,$RepoListRow);
        if($ret !== true) {
            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "リモートリポジトリ管理のアクセスに失敗しました。
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1018");  //2016
            $addlogstr = $ret;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception($FREE_LOG);
        }
        // リモートリポジトリ管理の排他処理をしていないので
        // UIから廃止または自動同期を無効にしていないかチェックする。
        if(($RepoListRow['DISUSE_FLAG'] == '1') ||
           ($RepoListRow['AUTO_SYNC_FLG'] == TD_B_CICD_REPOSITORY_LIST::C_AUTO_SYNC_FLG_OFF)) {
            // exit
            exit(0);
        }
        // 同期状態が異常でないことを確認する。 
        if($RepoListRow['SYNC_STATUS_ROW_ID'] == $SyncStatusNameobj->ERROR()) {
            // exit
            exit(0);
        }
        // 同期状態が再開か空白の場合
        if(($RepoListRow['SYNC_STATUS_ROW_ID'] == $SyncStatusNameobj->RESTART()) ||
           (strlen($RepoListRow['SYNC_STATUS_ROW_ID']) == 0)) {
            $MatlListUpdateExeFlg = true;
        } 

        $cloneRepoDir = $LFCobj->getLocalCloneDir($RepoId);
        $libPath      = $LFCobj->getLocalShellDir();
        $Gitobj = new ControlGit($RepoId, $RepoListRow['REMORT_REPO_URL'], $RepoListRow['BRANCH_NAME'], $cloneRepoDir, $RepoListRow['GIT_USER'],  $RepoListRow['GIT_PASSWORD'], $RepoListRow['SSH_PASSWORD'], $RepoListRow['SSH_PASSPHRASE'], $RepoListRow['SSH_EXTRA_ARGS'], $libPath, $objMTS, $RepoListRow['RETRAY_COUNT'], $RepoListRow['RETRAY_INTERVAL'], $RepoListRow['PROXY_ADDRESS'], $RepoListRow['PROXY_PORT'], $GitCmdRsltParsAry);

        try {
            // Gitのバージョンをチェックしssh接続パラメータを設定する。
            $ret = LocalsetSshExtraArgs();

            // 戻り値チェック不要
            // ローカルクローンディレクトリ有無判定
            $ret = $Gitobj->LocalCloneDirCheck();
            if($ret === false) {
                // ローカルクローンディレクトリなし
                $CloneExeFlg  = true;
            }
            if($CloneExeFlg === false) {

                // トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    //[処理]ローカルクローンのリモートリポジトリとブランチ確認 (リモートリポジトリ項番:{})
                    $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2007",$RepoId);
                    require ($root_dir_path . $log_output_php );
                }

                /////////////////////////////////////////////////////////////////
                // ローカルクローンのリモートリポジトリ(URL)が正しいか判定
                /////////////////////////////////////////////////////////////////
                $ret = LocalCloneRemoteRepoChk($RepoId);
                if($ret === false) {
                    // リモートリポジトリ不一致
                    $CloneExeFlg  = true;

                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        //[処理]リモートリポジトリ管理のリモートリポジトリ(URL)の変更検出 (リモートリポジトリ項番:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2014",$RepoId);
                        require ($root_dir_path . $log_output_php );
                    }
                }
            }
            if($CloneExeFlg === false) {
                /////////////////////////////////////////////////////////////////
                // ローカルクローンのブランチが正しいか判定
                /////////////////////////////////////////////////////////////////
                $ret = LocalCloneBranchChk($RepoId,$RepoListRow);
                if($ret === false) {
                    // ブランチ不一致
                    $CloneExeFlg  = true;
 
                    // トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]リモートリポジトリ管理のブランチの変更検出 (リモートリポジトリ項番:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2029",$RepoId);
                        require ($root_dir_path . $log_output_php );
                    }
                }
            }
            if($CloneExeFlg === true) {
                /////////////////////////////////////////////////////////////////
                // ローカルクローン作成
                /////////////////////////////////////////////////////////////////
                $ret = CreateLocalClone($RepoId,$RepoListRow);

                /////////////////////////////////////////////////////////////////
                // Git ファイル一覧取得
                /////////////////////////////////////////////////////////////////
                $GitFiles  = array();
                $ret = getLocalCloneFileList($RepoId,$GitFiles);

                /////////////////////////////////////////////////////////////////
                // rolesディレクトリ取得
                /////////////////////////////////////////////////////////////////
                $RolesPath = array();
                $RolesPath = getRolesPath($RepoId,$GitFiles);

            } else {
                /////////////////////////////////////////////////////////////////
                // Git差分抽出(git pull) 
                /////////////////////////////////////////////////////////////////
                $pullResultAry = array();
                $UpdateFiles   = array();
                $MargeExeFlg   = false;

                $ret = GitPull($RepoId,$pullResultAry,$UpdateFiles,$MargeExeFlg,$RepoListRow);
                if(($MargeExeFlg === true) ||
                   ($MatlListUpdateExeFlg === true)) {

                    // Git pullの差分抽出が不完全なので、Git Cloneした場合と同等の処理を行う
                    /////////////////////////////////////////////////////////////////
                    // Git ファイル一覧取得
                    /////////////////////////////////////////////////////////////////
                    $GitFiles  = array();
                    $ret = getLocalCloneFileList($RepoId,$GitFiles);

                    /////////////////////////////////////////////////////////////////
                    // rolesディレクトリ取得
                    /////////////////////////////////////////////////////////////////
                    $RolesPath = array();
                    $RolesPath = getRolesPath($RepoId,$GitFiles);
                }
            }
            // 資材管理更新
            if(($MargeExeFlg === true) ||
               ($MatlListUpdateExeFlg === true)) {

                /////////////////////////////////////////////////////////////////
                // 資材管理にGit ファイル情報を登録
                /////////////////////////////////////////////////////////////////

                //トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2024",array($RepoId));
                    require ($root_dir_path . $log_output_php );
                }

                $MatlListRecodes = array();
                $ret = getMatlListRecodes($RepoId,$MatlListRecodes);

                ///////////////////////////////////////////////////
                // 関連シーケンスをロックする                    //
                ///////////////////////////////////////////////////
                $aryTgtOfSequenceLock = array();
                $aryTgtOfSequenceLock[] = $TDMatlobj->getSequenceName();
                $aryTgtOfSequenceLock[] = $TDMatlobj->getJnlSequenceName();
                $aryTgtOfSequenceLock[] = $TDRepoobj->getSequenceName();
                $aryTgtOfSequenceLock[] = $TDRepoobj->getJnlSequenceName();

                $ret = LockPkeySequence($aryTgtOfSequenceLock);

                $ret = MatlListMerge($RepoId,$MatlListRecodes, $RolesPath, $GitFiles);

                $ret = MatlListRolesRecodeUpdate($RepoId);

                ///////////////////////////////////////////////////
                // 資材一覧を更新したタイミングでコミット
                ///////////////////////////////////////////////////
                $ret = $DBobj->transactionCommit();
                if($ret !== true) {
                    // 異常フラグON
                    $error_flag = 1;
        
                    // "トランザクション処理に失敗しました。";
                    $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  // 2002
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

                    // UIに表示するメッセージ
                    // 想定外のエラーか発生しました。
                    $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

                    // 戻り値編集
                    $retary = array();
                    $RetCode = -1;
                    $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
                    throw new Exception($retary);
                }

                //トレースメッセージ
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2025",array($RepoId));
                    require ($root_dir_path . $log_output_php );
                }

            }
        } catch (Exception $e){
            // 例外処理
            if( $objDBCA->getTransactionMode() ){
                ///////////////////////////////////////////////////
                // 一旦ロールバック
                ///////////////////////////////////////////////////
                $ret = $DBobj->transactionRollBack();
                if($ret === false) {
                    // 異常フラグON
                    $error_flag = 1;

                    // "トランザクション処理に失敗しました。";
                    $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  // 2002
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                    require ($root_dir_path . $log_output_php );
                }
            }

            // 異常フラグON($error_flag)  UIに表示するエラーメッセージ設定(setUIDisplayMsg)  throw new Exception
            ExceptionRecive($RepoId,$e->getMessage(),__FILE__,__LINE__);
        }

        // トランザクション再開
        $ret = $DBobj->transactionExit();
        if($ret !== true) {
            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // 異常フラグON
            $error_flag = 1;

            // "トランザクション処理に失敗しました。";
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  //2002
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }

        $ret = $DBobj->transactionStart();
        if($ret !== true) {
            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // 異常フラグON
            $error_flag = 1;

            // "トランザクション処理に失敗しました。";
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  //2002
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }

        /////////////////////////////////////////////////////////////////
        // 同期状態テーブル 処理時間更新
        /////////////////////////////////////////////////////////////////
        if($SyncTimeUpdate_Flg === false) {
            $ret = UpdateSyncStatusRecode($RepoId);
            if($ret !== true) {
                 // 異常フラグON
                 $error_flag = 1;

                // UIに表示するエラーメッセージ設定
                setDefaultUIDisplayMsg();

                // データベースの更新に失敗しました。(リポジトリ項番:{});
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1007",array($RepoId)); //3001
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$ret);
                throw new Exception($FREE_LOG);
            }
        }
        if($RepoListSyncStatusUpdate_Flg === false) {
            $SyncStatus = $SyncStatusNameobj->NORMAL();
            $ret = UpdateRepoListSyncStatus($RepoId,$SyncStatus);
            if($ret !== true)  {
                // 異常フラグON
                $error_flag = 1;

                // UIに表示するエラーメッセージ設定
                setDefaultUIDisplayMsg();

                // データベースの更新に失敗しました。(リポジトリ項番:{});
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1007",array($RepoId)); //3001
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$ret);
                throw new Exception($FREE_LOG);
            }
        }

        /////////////////////////////////////////////////////////////////
        // 資材管理のジャーナルシーケンスのロックを開放しないと
        // 別リポジトリのプロセスがシーケンスロックで止まってしまうので
        // 資材管理を更新したタイミングでトランザクションを終了する。
        /////////////////////////////////////////////////////////////////
        $ret = $DBobj->transactionCommit();
        if($ret !== true) {
            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "トランザクション処理に失敗しました。";
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  // 2002
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }
        // commit後に同期状態テーブル 処理時間更新とリモートリポジトリ管理の状態を更新をマーク
        $SyncTimeUpdate_Flg           = true;
        $RepoListSyncStatusUpdate_Flg = true;

        ///////////////////////////////////////////////////
        // トランザクション終了
        ///////////////////////////////////////////////////
        $ret = $DBobj->transactionExit();
        ///////////////////////////////////////////////////
        // トランザクション開始  
        // シーケンスロックはしないて、資材紐付管理に登録
        // されている資材を展開
        ///////////////////////////////////////////////////
        $ret = $DBobj->transactionStart();
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // "トランザクション処理に失敗しました。";
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  //2002
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }

        ///////////////////////////////////////////////////
        // 資材紐付管理に登録されている資材を展開
        ///////////////////////////////////////////////////
        //$MargeExeFlg          = true;
        //$MatlListUpdateExeFlg = false;
     
        $ret = MatlLinkExecute($RepoId,$MargeExeFlg,$MatlListUpdateExeFlg);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2003");  
            $logaddstr = $ret;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
            throw new Exception($FREE_LOG);
        }
        /////////////////////////////////////////////////////////////////
        // トランザクションを終了
        /////////////////////////////////////////////////////////////////
        $ret = $DBobj->transactionCommit();
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            // "トランザクション処理に失敗しました。";
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }

    } catch (Exception $e){

        // メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ){
            // ロールバック
            $ret = $DBobj->transactionRollBack();
            if($ret === false) {
                // 例外処理へ
                // "トランザクション処理に失敗しました。";
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  // 2002
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                require ($root_dir_path . $log_output_php );
            }
            // トランザクション終了
            $ret = $DBobj->transactionExit();
        }
    }
    if($SyncTimeUpdate_Flg === false) {
        $ret = UpdateSyncStatusRecode($RepoId);
        if($ret !== true) {

            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // データベースの更新に失敗しました。(リポジトリ項番:{});
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1007",array($RepoId)); //3001
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$ret);
            // ログ出力
            require ($root_dir_path . $log_output_php );
        }
    }
    if($RepoListSyncStatusUpdate_Flg === false) {
        if(strlen($UIDisplayMsg) == 0) {
            $SyncStatus = $SyncStatusNameobj->NORMAL();
        } else {
            $SyncStatus = $SyncStatusNameobj->ERROR();
        }
        $ret = UpdateRepoListSyncStatus($RepoId,$SyncStatus);
        if($ret !== true)  {
            // 異常フラグON
            $error_flag = 1;

            // データベースの更新に失敗しました。(リポジトリ項番:{});
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1007",array($RepoId)); //3001
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$ret);
            // ログ出力
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

    function ExceptionRecive($RepoId,$ExceptionMessage,$file,$line,$ExceptionExec=true) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        $retAry = $ExceptionMessage;
        $retlogstr = "";
        $UIMsg     = "";
        $RetCode   = "";
        analysisReturnArray($retAry,$RetCode,$retlogstr,$UIMsg);

        // UIに表示するエラーメッセージ設定
        setUIDisplayMsg($UIMsg);

        // 異常フラグON
        $error_flag = 1;

        // 例外処理へ
        // 想定外のエラーか発生しました。(リモートリポジトリ項番:{})
        $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1008",array($RepoId));
        $FREE_LOG = makeLogiFileOutputString(basename($file),$line,$logstr,$retlogstr);
        if($ExceptionExec === true) {
            throw new Exception($FREE_LOG);
        } else {
            require ($root_dir_path . $log_output_php );
        }
   }

    function getRolesPath($RepoId,$GitFiles) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        $RolesPath = array();
        foreach($GitFiles as $FilePath) {
            $FilePath = "/" . $FilePath;
            $ret = mb_strpos($FilePath,'/roles/');
            if( $ret === false) {
                // 最上位階層のRolesは無視
                continue;
            }
            $pathNestAry = mb_split('/',$FilePath);
            $pathNestAry[0] = "/";
            $path = "";
            for($idx=0;$idx < count($pathNestAry) ;$idx++) {
                switch($idx){
                case 0:
                    $path .= $pathNestAry[$idx];
                    break;
                case 1:
                    // 1階層目がrolesの場合は除外
                    // /roles/xxx/xxx/xxx
                    $path .= $pathNestAry[$idx];
                    break;
                default:
                    $path .= "/" . $pathNestAry[$idx];
                    if($pathNestAry[$idx] == 'roles') {
                        // 最終階層がrolesの場合は除外
                        // /xxxx/xxxxx/roles
                        if(($idx+1) != count($pathNestAry)) {
                            $addPath = mb_substr($path,1);
                            $RolesPath[$addPath] = 0;
                        }
                    }
                    break;
                }
            }
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]ローカルクローンのRolesディレクトリ取得 (リモートリポジトリ項番:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2009",$RepoId);
            require ($root_dir_path . $log_output_php );
        }
        return $RolesPath;
    }

    function getLocalCloneFileList($RepoId,&$GitFiles) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        $ret = $Gitobj->GitLsFiles($GitFiles);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // Git ls-files commandに失敗しました。
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1026");

            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            // Git ls-files commandに失敗しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1026");
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]ローカルクローンのファイルリスト取得 (リモートリポジトリ項番:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2008",$RepoId);
            require ($root_dir_path . $log_output_php );
        }

        return true;
    }

    function GitPull($RepoId,&$pullResultAry,&$UpdateFiles,&$UpdateFlg,$RepoListRow) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        // 認証方式か判定
        $AuthTypeName = getAuthType($RepoListRow);

        $ret = $Gitobj->GitPull($pullResultAry,$AuthTypeName,$UpdateFlg);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;
            // Git pull commandに失敗しました。
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1030");

            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            // Git pull commandに失敗しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1030");
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        // Git Pullで変更ファイルを解析する処理が不完全なので、変更の有無だけを判定
//        $UpdateFlg = false;
//        $ret = $Gitobj->GitPullResultAnalysis($pullResultAry,$UpdateFiles,$UpdateFlg);
//        if($ret !== true) {
//            // 異常フラグON
//            $error_flag = 1;
//            // Git pull command の結果解析に失敗しました。
//            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1025");
//
//            $logaddstr = $Gitobj->GetLastErrorMsg();
//            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);
//
//            // UIに表示するメッセージ
//            // Git pull command の結果解析に失敗しました。
//            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1025");
//            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();
//
//            // 戻り値編集
//            $retary = array();
//            $RetCode = -1;
//            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
//            throw new Exception($retary);
//        }
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]リモートリポジトリの差分確認 (リモートリポジトリ項番:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2023",array($RepoId,var_export($UpdateFlg,true)));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    function CreateLocalClone($RepoId,$RepoListRow) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        $cloneRepoDir = $LFCobj->getLocalCloneDir($RepoId);
        $ret = $Gitobj->LocalCloneDirClean($Gitobj);
        if($ret === false) {
            // 該当のリモートリポジトリに紐づいている資材を資材一覧から廃止。
            // 戻りは確認しない
            MatlListRecodeDisuse($RepoId);

            // 異常フラグON
            $error_flag = 1;

            // ローカルクローンディレクトリの作成に失敗しました。
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1006",$cloneRepoDir);  //2008
            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            // ローカルクローンディレクトリの作成に失敗しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1006",$cloneRepoDir);
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary = array();
            $RetCode = false;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //"[処理]ローカルクローンディレクトリ作成(リモートリポジトリ項番:{}))
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2016",$RepoId);
            require ($root_dir_path . $log_output_php );
        }

        // 認証方式か判定
        $AuthTypeName = getAuthType($RepoListRow);

        $ret = $Gitobj->GitClone($AuthTypeName);
        if($ret !== true) {
            // 該当のリモートリポジトリに紐づいている資材を資材一覧から廃止。
            // 戻りは確認しない
            MatlListRecodeDisuse($RepoId);

            // 異常フラグON
            $error_flag = 1;

            // Git clone commandに失敗しました。
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1019");  //2008

            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            // Git clone commandに失敗しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1019");
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary = array();
            $RetCode = false;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            //[処理]ローカルクローン作成 (リモートリポジトリ項番:{})
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2018",$RepoId);
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    function LocalCloneRemoteRepoChk($RepoId) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        // ローカルクローンのリモートリポジトリ確認
        $ret = $Gitobj->GitRemoteChk();
        if($ret === -1) {
            // Git remote command error
            // 異常フラグON
            $error_flag = 1;

            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1023");

            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            // Git remote commandに失敗しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1023");
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary  = array();
            $RetCode = false;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        } else {
            return $ret;
        }
    }

    function LocalCloneBranchChk($RepoId,$RepoListRow) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        // 認証方式か判定
        $AuthTypeName = getAuthType($RepoListRow);

        // ローカルクローンのブランチ確認
        $ret = $Gitobj->GitBranchChk($AuthTypeName);

        if($ret === -1) {
            // Git remote command error
            // 異常フラグON
            $error_flag = 1;

            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1024");
            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            // Git branch commandに失敗しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1024");
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary  = array();
            $RetCode = false;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        } else {
            return $ret;
        }
    }
    function getRepoListRow($RepoId,&$RepoListRow) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $TDRepoobj;
        global $TDSyncStsobj;

        // 廃止かは判定しない。
        $sqlBody   = "SELECT 
                        TAB_A.*
                      FROM
                        %s TAB_A
                      WHERE
                        TAB_A.REPO_ROW_ID = :REPO_ROW_ID ";
        $sqlBody = sprintf($sqlBody,$TDRepoobj->getTableName(),$TDSyncStsobj->getTableName());
        $arrayBind = array("REPO_ROW_ID"=>$RepoId);
        $objQuery  = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // データベースのアクセスに失敗しました。
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005"); // 2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return($FREE_LOG);
        }
        $RepoListRow = array();
        if($objQuery->effectedRowCount() != 1) {
            // 異常フラグON
            $error_flag = 1;

            // リモートリポジトリ管理のレコードが見つかりませんでした。(リポジトリ項番:{})
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1010",$RepoId); //3004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,"");
            return($FREE_LOG);
        }
        while($row = $objQuery->resultFetch()) {
            $RepoListRow = $row;
        }
        return true;
    }

    function setDefaultUIDisplayMsg() {
        global $objMTS;
        global $UIDisplayMsg;
        $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
    }

    function setUIDisplayMsg($UIMsg) {
        global $UIDisplayMsg;
        $UIDisplayMsg = $UIMsg;
    }

    function makeReturnArray($RetCode,$LogStr,$UIMsg) {
        $ary = array();
        $ary['RetCode'] = $RetCode;
        $ary['log']     = $LogStr;
        $ary['UImsg']   = $UIMsg;
        return json_encode($ary);
    }

    function analysisReturnArray($retJson,&$RetCode,&$Logstr,&$UIMsg) {
        $retAry = json_decode($retJson,true);
        $Logstr  = "";
        $UIMsg   = "";
        $RetCode = "";
        $RetCode = $retAry['RetCode'];
        $Logstr  = $retAry['log'];
        $UIMsg   = $retAry['UImsg'];
    }
    
    function LockPkeySequence($aryTgtOfSequenceLock) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        // デッドロック防止のために、昇順でロック
        // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える 
        asort($aryTgtOfSequenceLock);
        foreach($aryTgtOfSequenceLock as $strSeqName){
            //シーケンスロック
            $ret = $DBobj->LockPkeySequence($strSeqName);
            if($ret === false) {
                // 異常フラグON
                $error_flag = 1;

                // シーケンスロックに失敗しました。
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1004"); //2003
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

                // UIに表示するメッセージ
                // 想定外のエラーか発生しました。
                $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

                // 戻り値編集
                $retary = array();
                $RetCode = -1;
                $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
                throw new Exception($retary);
            }
        }
        return true;
    }
    function getMatlListRecodes($RepoId,&$MatlListRecodes) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        global $TDMatlobj;

        $dbAcction         = "SELECT";
        $BindArray         = array('WHERE'=>"REPO_ROW_ID=:REPO_ROW_ID");
        $ColumnConfigArray = $TDMatlobj->setColumnConfigAttr();
        $ColumnValueArray  = $TDMatlobj->getColumnDefine();
        $objQueryArray = $DBobj->makeSelectSQLString($dbAcction,$BindArray,$TDMatlobj,$ColumnConfigArray,$ColumnValueArray);
        if($objQueryArray === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

            // UIに表示するメッセージ
            // 想定外のエラーか発生しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        $arrayBind = array("REPO_ROW_ID"=>$RepoId);
        $objQuery  = $DBobj->SelectForSimple($objQueryArray[1],$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

            // UIに表示するメッセージ
            // 想定外のエラーか発生しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        $num_of_rows = $objQuery->effectedRowCount();
        $MatlListRecodes = array();
        while($row = $objQuery->resultFetch()) {
            if($row['DISUSE_FLAG'] == '1') {
                // レコードに対してなにもしないことをマーク
                $row['RECODE_ACCTION'] = 'none';
            } else {
                // 廃止をマーク
                $row['RECODE_ACCTION'] = 'disuse';
            }
            // 資材一覧更新状態
            $MatlListRecodes[$row['MATL_FILE_TYPE_ROW_ID']][$row['MATL_FILE_PATH']] = $row;
        }
        return true;
    }

    function MatlListMerge($RepoId,&$MatlListRecodes,$RolesPath,$GitFiles) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        global $TDMatlobj;

        // rolesディレクトリの増減確認
        foreach($RolesPath as $path=>$dummy) {
            $FileType = TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_ROLES;
            if(isset($MatlListRecodes[$FileType][$path])) {
                // 廃止確認
                if($MatlListRecodes[$FileType][$path]['DISUSE_FLAG'] == '0') {
                    // 廃止ではないので、レコードはそのまま残すのだ、配列から削除
                    unset($MatlListRecodes[$FileType][$path]);
                } else {
                    // 廃止なので、レコード復活のマークを設定
                    $MatlListRecodes[$FileType][$path]['RECODE_ACCTION'] = 'use';
                    $MatlListRecodes[$FileType][$path]['DISUSE_FLAG'] = '0';
                }
            } else {
                // レコードの項目値設定
                $MatlListRecodes[$FileType][$path]['MATL_ROW_ID'] = 0;
                $MatlListRecodes[$FileType][$path]['REPO_ROW_ID'] = $RepoId;
                $MatlListRecodes[$FileType][$path]['MATL_FILE_PATH'] = $path;
                $MatlListRecodes[$FileType][$path]['MATL_FILE_TYPE_ROW_ID'] = $FileType;
                // 新規なので、レコード追加のマークを設定
                $MatlListRecodes[$FileType][$path]['RECODE_ACCTION'] = 'Insert';
            }
        }

        // ファイルの増減確認
        foreach($GitFiles as $path) {
            $FileType = TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_FILE;
            if(isset($MatlListRecodes[$FileType][$path])) {
                // 廃止確認
                if($MatlListRecodes[$FileType][$path]['DISUSE_FLAG'] == '0') {
                    // 廃止ではないので、レコードはそのまま残すのだ、配列から削除
                    unset($MatlListRecodes[$FileType][$path]);
                } else {
                    // 廃止なので、レコード復活のマークを設定
                    $MatlListRecodes[$FileType][$path]['RECODE_ACCTION'] = 'use';
                    $MatlListRecodes[$FileType][$path]['DISUSE_FLAG']    = '0';
                }
            } else {
                // レコードの項目値設定
                $MatlListRecodes[$FileType][$path]['MATL_ROW_ID'] = 0;
                $MatlListRecodes[$FileType][$path]['REPO_ROW_ID'] = $RepoId;
                $MatlListRecodes[$FileType][$path]['MATL_FILE_PATH'] = $path;
                $MatlListRecodes[$FileType][$path]['MATL_FILE_TYPE_ROW_ID'] = $FileType;
                // 新規なので、レコード追加のマークを設定
                $MatlListRecodes[$FileType][$path]['RECODE_ACCTION'] = 'Insert';
            }
        }

        // 増減分を資材管理に反映
        foreach($MatlListRecodes as $FileType=>$Recodes) {
            foreach($Recodes as $path=>$row) {
                switch($row['RECODE_ACCTION']) {
                case 'none':
                    break;
                case 'disuse':
                    unset($row['RECODE_ACCTION']);
                    // 資材管理のレコード廃止
                    $ret = MatlListDisuseUpdate($row,'1');

                    //トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]資材一覧　レコード廃止 (資材:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2022",array($path));
                        require ($root_dir_path . $log_output_php );
                    }
                    break;
                case 'Insert':
                    unset($row['RECODE_ACCTION']);

                    // 資材管理にレコード追加
                    $ret = MatlListInsert($row);

                    //トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]資材一覧　レコード追加 (資材:{});
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2020",array($path));
                        require ($root_dir_path . $log_output_php );
                    }
                    break;
                case 'use':
                    unset($row['RECODE_ACCTION']);

                    // 資材管理のレコード復活
                    $ret = MatlListDisuseUpdate($row,'0');

                    //トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]資材一覧　レコード復活 (資材:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2021",array($path));
                        require ($root_dir_path . $log_output_php );
                    }
                    break;
                }
            }
        }
        return true;
    }

    function MatlListDisuseUpdate($row,$Disuse) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $TDMatlobj;

        $sqlBody   = "SELECT * FROM " . $TDMatlobj->getTableName() . " WHERE MATL_ROW_ID=:MATL_ROW_ID";
        $arrayBind = array();
        $arrayBind = array("MATL_ROW_ID"=>$row['MATL_ROW_ID']);
        $objQuery  = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

            // UIに表示するメッセージ
            // 想定外のエラーか発生しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();
        $row = $objQuery->resultFetch();

        $ColumnConfigArray                = $TDMatlobj->setColumnConfigAttr();
        $ColumnValueArray                 = $TDMatlobj->getColumnDefine();
        $ColumnValueArray                 = $row;
        $ColumnValueArray["DISUSE_FLAG"]  = $Disuse;
        $JnlInsert_Flag                   = true;   // 履歴出力あり

        $BindArray = array();
        $ret = $DBobj->UpdateRow($BindArray,$TDMatlobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

            // UIに表示するメッセージ
            // 想定外のエラーか発生しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        return true;
    }

    function MatlListInsert($row) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $TDMatlobj;

        $ColumnConfigArray = $TDMatlobj->setColumnConfigAttr();
        $ColumnValueArray  = $TDMatlobj->getColumnDefine();
        $ColumnValueArray["REPO_ROW_ID"]           = $row['REPO_ROW_ID'];
        $ColumnValueArray["MATL_FILE_PATH"]        = $row['MATL_FILE_PATH'];
        $ColumnValueArray["MATL_FILE_TYPE_ROW_ID"] = $row['MATL_FILE_TYPE_ROW_ID'];
        $ColumnValueArray["DISUSE_FLAG"]           = 0;
        $JnlInsert_Flag                            = true;   // 履歴出力あり

        $ret = $DBobj->InsertRow($TDMatlobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

            // UIに表示するメッセージ
            // 想定外のエラーか発生しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        return true;
    }

    function MatlListRolesRecodeUpdate($RepoId) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        global $TDMatlobj;

        $sqlBody   = "SELECT 
                          TAB_A.*,
                          (  
                              SELECT 
                                  COUNT(*) 
                              FROM 
                                  %s TAB_B 
                              WHERE 
                                    TAB_B.MATL_FILE_PATH LIKE CONCAT(TAB_A.MATL_FILE_PATH,'/%s') 
                                AND TAB_B.MATL_FILE_TYPE_ROW_ID=:FILE_TYPE_ID
                                AND TAB_B.REPO_ROW_ID=:REPO_ROW_ID
                                AND TAB_B.DISUSE_FLAG ='0'
                         ) AS FILE_COUNTT
                      FROM
                         %s TAB_A 
                      WHERE 
                             TAB_A.MATL_FILE_TYPE_ROW_ID=:ROLRS_TYPE_ID 
                         AND TAB_A.REPO_ROW_ID = :REPO_ROW_ID";
        $sqlBody   = sprintf($sqlBody,$TDMatlobj->getTableName(),"%",$TDMatlobj->getTableName());
        $arrayBind = array();
        $arrayBind = array('FILE_TYPE_ID'=>TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_FILE,
                           'ROLRS_TYPE_ID'=>TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_ROLES,
                           'REPO_ROW_ID'=>$RepoId);
        $objQuery  = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // データベースのアクセスに失敗しました。
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());

            // UIに表示するメッセージ
            // 想定外のエラーか発生しました。
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");

            // 戻り値編集
            $retary = array();
            $RetCode = -1;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        }
        // FETCH行数を取得
        $num_of_rows = $objQuery->effectedRowCount();
        while($row = $objQuery->resultFetch()) {
            $Acction = "";
            // 有効レコードでroles配下にファイルなし
            if(($row['DISUSE_FLAG'] == 0) && ($row['FILE_COUNTT'] == 0)) {
                $row['DISUSE_FLAG'] = 1;  // 廃止レコードにする。
                $Acction = "disuse";
            // 廃止レコードでroles配下にファイルあり
            } else if (($row['DISUSE_FLAG'] == 1) && ($row['FILE_COUNTT'] != 0)) {
                $row['DISUSE_FLAG'] = 0;  // 有効レコードにする。
                $Acction = "use";
            }
            if($Acction != "") {
                $ColumnConfigArray                = $TDMatlobj->setColumnConfigAttr();
                $ColumnValueArray                 = $TDMatlobj->getColumnDefine();
                unset($row['FILE_COUNTT']);
                $ColumnValueArray                 = $row;
                $JnlInsert_Flag                   = true;   // 履歴出力あり
    
                $BindArray = array();
                $ret = $DBobj->UpdateRow($BindArray,$TDMatlobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
                if($ret === false) {
                    // 異常フラグON
                    $error_flag = 1;
    
                    // "データベースのアクセスに失敗しました。";
                    $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");  //2004
                    $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
    
                    // UIに表示するメッセージ
                    // 想定外のエラーか発生しました。
                    $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
    
                    // 戻り値編集
                    $retary = array();
                    $RetCode = -1;
                    $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
                    throw new Exception($retary);
                }
                if($Acction == "use") {
                    //トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]資材一覧　レコード復活 (資材:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2021",array($row['MATL_FILE_PATH']));
                        require ($root_dir_path . $log_output_php );
                    }
                } else {
                    //トレースメッセージ
                    if ( $log_level === 'DEBUG' ){
                        // [処理]資材一覧　レコード廃止 (資材:{})
                        $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2022",array($row['MATL_FILE_PATH']));
                        require ($root_dir_path . $log_output_php );
                    }
                }
            }
        }
        return true;
    }
    function UpdateSyncStatusRecode($RepoId) {
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

        $TDSyncSts = new TD_T_CICD_SYNC_STATUS();
        $UpdateColumnAry = array();
        $UpdateColumnAry['ROW_ID']              = $RepoId;
        $UpdateColumnAry['SYNC_LAST_TIMESTAMP'] = date("Y/m/d H:i:s",time())  . ".000000";
        $sqlBody = sprintf("UPDATE %s SET ",$TDSyncSts->getTableName());
        $columnstr = "";
        $wherestr = "";
        foreach($UpdateColumnAry as $column=>$value) {
            if($column == 'ROW_ID') {
                $wherestr = " WHERE ROW_ID=:ROW_ID "; 
                continue;
            }
            if(strlen($columnstr) != 0) { $columnstr = $columnstr . " , "; }
            $columnstr .= sprintf(" %s=:%s ",$column,$column);
        }
        // 同期状態テーブル更新
        $sqlBody   = $sqlBody .  $columnstr . $wherestr;
        $arrayBind = $UpdateColumnAry;
        $ret = $DBobj->dbaccessExecute($sqlBody, $arrayBind);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");   // 2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }        

        //トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2026",array($RepoId));
            require ($root_dir_path . $log_output_php );
        }

        return true;
    }
    function UpdateRepoListSyncStatus($RepoId,$SyncStatus) {
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
        global $UIDisplayMsg;
        global $SyncStatusNameobj;

        $UpdateColumnAry                       = array();
        $UpdateColumnAry['REPO_ROW_ID']        = $RepoId;
        $UpdateColumnAry['SYNC_STATUS_ROW_ID'] = $SyncStatus;
        if($SyncStatus == $SyncStatusNameobj->NORMAL()) {
            $UpdateColumnAry['SYNC_ERROR_NOTE']    = "";
        } else {
            $UpdateColumnAry['SYNC_ERROR_NOTE']    = $UIDisplayMsg;
        }
        $ret = UpdateRepoListRecode($RepoId,$UpdateColumnAry);
        return $ret;
    }
    function UpdateRepoListRecode($RepoId,$UpdateColumnAry) {
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
        global $TDRepoobj;

        $dbAcction      = "SELECT";
        $BindArray      = array('WHERE'=>"REPO_ROW_ID=:REPO_ROW_ID");

        $ColumnConfigArray = $TDRepoobj->getColumnDefine();
        $ColumnValueArray  = $TDRepoobj->setColumnConfigAttr();
        $objQueryArray = $DBobj->makeSelectSQLString($dbAcction,$BindArray,$TDRepoobj,$ColumnConfigArray,$ColumnValueArray);
        $arrayBind = array();
        $arrayBind = array("REPO_ROW_ID"=>$UpdateColumnAry['REPO_ROW_ID']);
        $objQuery  = $DBobj->SelectForSimple($objQueryArray[1],$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");   // 2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        $num_of_rows = $objQuery->effectedRowCount();
        if($objQuery->effectedRowCount() != 1) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");   // 2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        $row = $objQuery->resultFetch();
        $Update = false;
        foreach($UpdateColumnAry as $column=>$value) {
            // 更新が必要か判定
            if($row[$column] != $value) {
                $row[$column] = $value;
                $Update = true;
            }
        }
        if($Update === false) {
            //トレースメッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2028",array($RepoId));
                require ($root_dir_path . $log_output_php );
            }
            return true;
        }
        $BindArray      = array();
        $ColumnConfigArray = $TDRepoobj->setColumnConfigAttr();
        $ColumnValueArray  = $row;
        $JnlInsert_Flag    = true;
        $ret = $DBobj->UpdateRow($BindArray,$TDRepoobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");   // 2004
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }

        //トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2027",array($RepoId));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }
    function getAuthType($RepoListRow) {
        // httpsの場合に認証が必要か判定
        switch($RepoListRow["GIT_PROTOCOL_TYPE_ROW_ID"]) {
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_HTTPS:
            switch($RepoListRow["GIT_REPO_TYPE_ROW_ID"]) {
            case TD_B_CICD_GIT_REPOSITORY_TYPE_NAME::C_GIT_REPO_TYPE_ROW_ID_PUBLIC:
                $PassAuth = "httpNoUserAuth";
                break;
            case TD_B_CICD_GIT_REPOSITORY_TYPE_NAME::C_GIT_REPO_TYPE_ROW_ID_PRIVATE:
                $PassAuth = "httpUserAuth";
                break;
            default:
                $PassAuth = "httpNoUserAuth";
                break;
            }
            break;
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_LOCAL:
            $PassAuth = "httpNoUserAuth";
            break;
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_PASS:
            $PassAuth = "sshPassAuth";
            break;
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY:
            $PassAuth = "sshKeyAuthPass";
            break;
        case TD_B_CICD_GIT_PROTOCOL_TYPE_NAME::C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY_NOPASS:
            $PassAuth = "sshKeyAuthNoPass";
            break;
        }
        return $PassAuth;
    }

    function MatlLinkExecute($RepoId,$MargeExeFlg,$MatlListUpdateExeFlg) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $logfile;

        global $objMTS;
        global $LFCobj;
        global $SyncStatusNameobj;

        /////////////////////////////////////////////////////////////////
        // ＤＢは更新のみなのでトランザクションは使用しない
        /////////////////////////////////////////////////////////////////

        /////////////////////////////////////////////////////////////////
        // 資材紐付管理から処理対象のレコード取得
        /////////////////////////////////////////////////////////////////
        $tgtMatlLinkRow   = array();

        $ret = getTargetMatlLinkRow($RepoId,$tgtMatlLinkRow);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;
            
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2002"); 
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$ret);
            return $FREE_LOG;
        }

        foreach($tgtMatlLinkRow as $row) {
            $go = false;
            if(($MargeExeFlg === true) || ($MatlListUpdateExeFlg === true)) {
                // 同期状態が異常以外の場合を判定
                if($row['SYNC_STATUS_ROW_ID'] != $SyncStatusNameobj->ERROR()) {
                    $go = true;
                }
            } else {
                // 同期状態が再開か空白の場合を判定
                if(($row['SYNC_STATUS_ROW_ID'] == $SyncStatusNameobj->RESTART()) ||
                   (strlen($row['SYNC_STATUS_ROW_ID']) == 0)) {
                    $go = true;
                }
            }
            if($go === true) {

                $DelvFlg = 0;
                if((strlen($row['DEL_OPE_ID']) != 0) &&
                    (strlen($row['DEL_MOVE_ID']) != 0)) {
                    $DelvFlg = 1;
                }

                ///////////////////////////////////////////////////////////////////////////////////////
                // 資材紐付を行う孫プロセス起動
                ///////////////////////////////////////////////////////////////////////////////////////
                $MatlLinkId = $row['MATL_LINK_ROW_ID'];
                $RestUserId = $row['ACCT_ROW_ID'];
                $ret = ExecuteGrandChildProcess($RepoId,$MatlLinkId,$RestUserId,$DelvFlg);
            }
        }
        return true;
    }
    function ExecuteGrandChildProcess($RepoId,$MatlLinkId,$RestUserId,$DelvFlg) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        global $logfile;

        global $objMTS;
        global $LFCobj;

        $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");

        // 改行コードが付いている場合に取り除く
        $php_command = str_replace("\n","",$php_command);

        $cmd = sprintf("%s %s/backyards/CICD_for_IaC/%s %s %s %s %s 2>&1",
                       $php_command,
                       $root_dir_path,
                       $LFCobj->getGrandChildProcessExecName(),
                       $RepoId,$MatlLinkId,$RestUserId,$DelvFlg);

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2010",array($RepoId,$MatlLinkId)); 
            require ($root_dir_path . $log_output_php );
        }
        // 孫プロセス起動 
        $output     = array();
        $return_var = "";
        exec($cmd,$output,$return_var);
        if($return_var == 0) {
            // トレースメッセージ
            if ( $log_level === 'DEBUG' ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2019",array($RepoId,$MatlLinkId)); 
                require ($root_dir_path . $log_output_php );
            }
        } else {
            // 孫プロセスのエラーを検出してもログだけ出して先に進む 
            // ワーニングフラグON
            $warning_flag =1;
 
            $logaddstr = implode("\n",$output);
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2001",array($RepoId,$MatlLinkId,$logaddstr)); 
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    function getTargetMatlLinkRow($RepoId,&$tgtMatlLinkRow) {
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
        global $TDMatlLinkobj;

        $tgtRepoListRow = array();
        
        // ansible/terraformのリリースファイル有無確認(インストール状態確認)
        $wanted_filename = "ita_ansible-driver";
        $ansible_driver  = false;
        if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
            $ansible_driver = true;
        }
        $wanted_filename = "ita_terraform-driver";
        $terraform_driver  = false;
        if(file_exists($root_dir_path . "/libs/release/" . $wanted_filename)) {
            $terraform_driver = true;
        }

        // 資材紐付管理取得
        // 条件: 廃止レコード以外　自動同期が有効　
        // 同期状態は呼出元で判定
        $Tobj = new TQ_REPO_LIST_ALL_JOIN($ansible_driver,$terraform_driver);
        $sqlBody = $Tobj->getSql($ansible_driver,$terraform_driver);
        $sqlBody = $sqlBody . 
                   " WHERE 
                          T1.REPO_ROW_ID       =  :REPO_ROW_ID
                      AND T1.DISUSE_FLAG       =  '0'
                      AND T1.AUTO_SYNC_FLG     =  :AUTO_SYNC_FLG";

        $arrayBind                       = array();
        $arrayBind['REPO_ROW_ID']        = $RepoId;
        $arrayBind['AUTO_SYNC_FLG']      = TD_B_CICD_MATERIAL_LINK_LIST::C_AUTO_SYNC_FLG_ON;

        $objQuery = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;
            
            // データベースのアクセスに失敗しました。
            $logstr  = $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        //$num_of_rows = $objQuery->effectedRowCount();
        $tgtMatlLinkRow = array();
        while ( $row = $objQuery->resultFetch() ){
            $tgtMatlLinkRow[] = $row;
        }
        unset($objQuery);
        return true;
    }
    
    // 資材一覧を廃止
    function MatlListRecodeDisuse($RepoId) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        global $TDMatlobj;

        // 資材一覧のレコードを全て廃止
        $ret = MatlListRecodeDisuseUpdate($RepoId);

        // トランザクションをコミット・ロールバック
        if($ret === true) {
            $ret = $DBobj->transactionCommit();
            if($ret !== true) {
                // Clone異常時の処理なのでログを出力してReturn;
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  // 2002
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                require ($root_dir_path . $log_output_php );
                return false;
            }
        } else {
            $ret = $DBobj->transactionRollBack();
            if($ret !== true) {
                // Clone異常時の処理なのでログを出力してReturn;
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1003");  // 2002
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                require ($root_dir_path . $log_output_php );
                return false;
            }
        }
        return true;
    }
    function MatlListRecodeDisuseUpdate($RepoId) {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        global $TDMatlobj;

        $dbAcction         = "SELECT";
        $BindArray         = array('WHERE'=>"REPO_ROW_ID=:REPO_ROW_ID AND DISUSE_FLAG='0'");
        $ColumnConfigArray = $TDMatlobj->setColumnConfigAttr();
        $ColumnValueArray  = $TDMatlobj->getColumnDefine();
        $objQueryArray = $DBobj->makeSelectSQLString($dbAcction,$BindArray,$TDMatlobj,$ColumnConfigArray,$ColumnValueArray);
        if($objQueryArray === false) {
            // Clone異常時の処理なのでログを出力してReturn;
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            require ($root_dir_path . $log_output_php );
            return false;

        }
        $arrayBind = array("REPO_ROW_ID"=>$RepoId);
        $objQuery  = $DBobj->SelectForSimple($objQueryArray[1],$arrayBind);
        if($objQuery === false) {
            // Clone異常時の処理なのでログを出力してReturn;
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            require ($root_dir_path . $log_output_php );
            return false;
        }
        while($row = $objQuery->resultFetch()) {
            $ColumnConfigArray                = $TDMatlobj->setColumnConfigAttr();
            $ColumnValueArray                 = $TDMatlobj->getColumnDefine();
            $ColumnValueArray                 = $row;
            $ColumnValueArray["DISUSE_FLAG"]  = '1';
            $JnlInsert_Flag                   = true;   // 履歴出力あり

            $BindArray = array();
            $ret = $DBobj->UpdateRow($BindArray,$TDMatlobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
            if($ret === false) {
                // Clone異常時の処理なのでログを出力してReturn;
                $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                require ($root_dir_path . $log_output_php );
                return false;
            }
        }
        return true;
    }
    function LocalsetSshExtraArgs() {
        global $cmDBobj;
        global $DBobj;
        global $LFCobj;
        global $Gitobj;
        global $error_flag;
        global $warning_flag;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;

        // Gitのバージョンをチェックしssh接続パラメータを設定する。
        $ret = $Gitobj->setSshExtraArgs();
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1033");

            $logaddstr = $Gitobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$logaddstr);

            // UIに表示するメッセージ
            $UIDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1033");
            $UIDisplayMsg .= "\n" . $Gitobj->GetGitCommandLastErrorMsg();

            // 戻り値編集
            $retary  = array();
            $RetCode = false;
            $retary = makeReturnArray($RetCode,$FREE_LOG,$UIDisplayMsg);
            throw new Exception($retary);
        } else {
            return $ret;
        }
    }
?>
