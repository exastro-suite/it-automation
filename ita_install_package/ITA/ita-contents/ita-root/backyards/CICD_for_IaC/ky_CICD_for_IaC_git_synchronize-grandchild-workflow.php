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
//      CICD For IaC Git リモートリポジトリ同期処理　孫プロセス(資材紐付管理単位)
//
//   function 
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
    // 起動パラメータ取得   リモードリポジトリ項番 資材紐管理項番 RestUserID Movement実行有無
    ///////////////////////////////////////////////////////////////
    if($argc != 5) {
        echo ("Invalid parameter count");
        exit(2);
    }
    if(is_numeric($argv[1]) === false) {
        echo ("Invalid parameter argv[1]");
        exit(2);
    }
    $RepoId = (int)$argv[1];
    if(is_numeric($argv[2]) === false) {
        echo ("Invalid parameter argv[2]");
        exit(2);
    }
    $MatlLinkId = (int)$argv[2];
    if(is_numeric($argv[3]) === false) {
        echo ("Invalid parameter argv[3]");
        exit(2);
    }
    $RestUserId = (int)$argv[3];
    if(is_numeric($argv[4]) === false) {
        echo ("Invalid parameter argv[4]");
        exit(2);
    }
    $DelvExecFlg = (int)$argv[4];   // 0:Movement実行なし　1:Movement実行あり
    if($DelvExecFlg == 0) {
        $DelvExecFlg = false;
    } else {
        $DelvExecFlg = true;
    }


    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename("ky_CICD_for_IaC_git_synchronize-child-workflow.php", '.php' ) . "_" . $RepoId . "_";

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

    $MatlLinkUpdate_Flg              = false;        // 資材紐付管理状態更新　状態 false:未更新 true:更新済
    // 資材紐付管理 同期状態/デリバリ状態に記録する情報退避変数
    $UIRestUserId                    = $RestUserId;   // 資材紐付管理  RestユーザID
    $UIDelvExecInsNo                 = "";           // 資材紐付管理　デリバリー作業インスタンスNo
    $UIDelvExecMenuId                = "";           // 資材紐付管理　デリバリー作業メニューID
    $UIMatlUpdateStatusID            = "";           // 資材紐付管理　同期状態
    $UIMatlUpdateStatusDisplayMsg    = "";           // 資材紐付管理　同期結果メッセージ
    $UIDelvStatusDisplayMsg          = "";           // 資材紐付管理　デリバリー結果メッセージ 
    $AddRepoIdMatlLinkIdStr          = "";           // ログメッセージに追加するメッセージ
    $AddMatlLinkIdStr                = "";           // ログメッセージに追加するメッセージ

    $db_access_user_id               = -130000;

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require_once ($root_dir_path . $php_req_gate_php );


        $AddRepoIdMatlLinkIdStr = $objMTS->getSomeMessage("ITACICDFORIAC-STD-3000",array($RepoId,$MatlLinkId));
        $AddMatlLinkIdStr       = $objMTS->getSomeMessage("ITACICDFORIAC-STD-3001",array($RepoId,$MatlLinkId));

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50001");
            $FREE_LOG .= $AddRepoIdMatlLinkIdStr;
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
        require_once ($root_dir_path . '/libs/backyardlibs/CICD_for_IaC/cicd_restapi_access.php');


        global $db_model_ch;
        global $objDBCA;
        global $objMTS;

        $SyncStatusNameobj = new TD_SYNC_STATUS_NAME_DEFINE($objMTS);

        $cmDBobj = new CommonDBAccessCoreClass($db_model_ch,$objDBCA,$objMTS,$db_access_user_id);

        $DBobj = new LocalDBAccessClass($db_model_ch,$cmDBobj,$objDBCA,$objMTS,$db_access_user_id,$logfile,$log_level,$RepoId);

        $LFCobj = new LocalFilesControl();       

        global  $TDMatlLinkobj;
        global  $LFCobj;
        global  $AddRepoIdMatlLinkIdStr;
        global  $AddMatlLinkIdStr;

        $TDMatlLinkobj = new TD_B_CICD_MATERIAL_LINK_LIST();
        $ret = $TDMatlLinkobj->setConfig($cmDBobj);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // "データベースのアクセスに失敗しました。";
            $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
            $logstr .= $AddRepoIdMatlLinkIdStr;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$TDMatlLinkobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }

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
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1015");
            $logstr   .= $AddRepoIdMatlLinkIdStr;
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
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
            $logstr   .= $AddRepoIdMatlLinkIdStr;
            $addlogstr = $DBobj->GetLastErrorMsg();
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );
        }

        ///////////////////////////////////////////////////
        // 関連シーケンスをロックする                    //
        ///////////////////////////////////////////////////
        $aryTgtOfSequenceLock = array();
        $aryTgtOfSequenceLock[] = $TDMatlLinkobj->getSequenceName();
        $aryTgtOfSequenceLock[] = $TDMatlLinkobj->getJnlSequenceName();
        asort($aryTgtOfSequenceLock);
        foreach($aryTgtOfSequenceLock as $strSeqName){
            //シーケンスロック
            $ret = $DBobj->LockPkeySequence($strSeqName);
            if($ret === false) {
                // UIに表示するエラーメッセージ設定
                setDefaultUIDisplayMsg();

                // 異常フラグON
                $error_flag = 1;

                // シーケンスロックに失敗しました。
                $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1004"); //2003
                $logstr   .= $AddRepoIdMatlLinkIdStr;
                $addlogstr = $DBobj->GetLastErrorMsg();
                $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
                throw new Exception( $FREE_LOG );
            }
        }

        /////////////////////////////////////////////////////////////////
        // リモートリポジトリ管理の情報を取得
        /////////////////////////////////////////////////////////////////
        $ret = MailLinkExecute($RepoId,$MatlLinkId);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            // 紐付資材の更新に失敗しました。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
            $logstr    = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2045",array($RepoId,$MatlLinkId)); 
            $addlogstr = $ret;
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$addlogstr);
            throw new Exception( $FREE_LOG );

        }

        /////////////////////////////////////////////////////////////////
        // 資材紐付管理　同期状態・デリバリ状態更新
        /////////////////////////////////////////////////////////////////
        if($MatlLinkUpdate_Flg === false) {

            $reyAry = getUIMatlSyncStatus();
            $UpdateColumnAry  = array();
            $UpdateColumnAry['SYNC_STATUS_ROW_ID']     = $reyAry[2];
            $UpdateColumnAry['SYNC_ERROR_NOTE']        = $reyAry[0];
            $UpdateColumnAry['SYNC_LAST_TIME']         = date("Y/m/d H:i:s",time())  . ".000000";
            $UpdateColumnAry['SYNC_LAST_UPDATE_USER']  = $UIRestUserId;
            $UpdateColumnAry['DEL_ERROR_NOTE']         = $reyAry[1];
            $UpdateColumnAry['DEL_EXEC_INS_NO']        = $reyAry[3];
            $UpdateColumnAry['DEL_MENU_NO']            = $reyAry[4];

            $ret = UpdateMatlLinkSyncStatus($MatlLinkId,$UpdateColumnAry);
            if($ret !== true)  {
                // 異常フラグON
                $error_flag = 1;

                // UIに表示するエラーメッセージ設定
                setDefaultUIDisplayMsg();

                // データベースの更新に失敗しました。
                $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1007",array($AddMatlLinkIdStr));
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$ret);
                throw new Exception($FREE_LOG);
            }
        }

        /////////////////////////////////////////////////////////////////
        // 資材管理のジャーナルシーケンスのロックを開放しないと
        // 別ブランチのプロセスがシーケンスロックで止まってしまうので
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
            $logstr .= $AddRepoIdMatlLinkIdStr;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            throw new Exception($FREE_LOG);
        }
        // commit後に資材紐付管理　同期状態・デリバリ状態更新 マーク
        $MatlLinkUpdate_Flg     = true;
     
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
                $logstr .= $AddRepoIdMatlLinkIdStr;
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
                require ($root_dir_path . $log_output_php );
            }
            // トランザクション終了
            $ret = $DBobj->transactionExit();
        }
    }
    if($MatlLinkUpdate_Flg === false) {
        $reyAry = getUIMatlSyncStatus();
        $UpdateColumnAry  = array();
        $UpdateColumnAry['SYNC_STATUS_ROW_ID']     = $reyAry[2];
        $UpdateColumnAry['SYNC_ERROR_NOTE']        = $reyAry[0];
        $UpdateColumnAry['SYNC_LAST_TIME']         = date("Y/m/d H:i:s",time())  . ".000000";
        $UpdateColumnAry['SYNC_LAST_UPDATE_USER']  = $UIRestUserId;
        $UpdateColumnAry['DEL_ERROR_NOTE']         = $reyAry[1];
        $UpdateColumnAry['DEL_EXEC_INS_NO']        = $reyAry[3];
        $UpdateColumnAry['DEL_MENU_NO']            = $reyAry[4];

        $ret = UpdateMatlLinkSyncStatus($MatlLinkId,$UpdateColumnAry);
        if($ret !== true) {

            // 異常フラグON
            $error_flag = 1;

            // UIに表示するエラーメッセージ設定
            setDefaultUIDisplayMsg();

            // データベースの更新に失敗しました。(リポジトリ項番:{});
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1007",array($AddMatlLinkIdStr));
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
        // 呼び元でexit codeをチェックしているので 0 でexit 
        exit(0);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-ERR-50002");
            require ($root_dir_path . $log_output_php );
        }
        // 呼び元でexit codeをチェックしているので 0 でexit 
        exit(0);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITAWDCH-STD-50002");
            require ($root_dir_path . $log_output_php );
        }
        
        exit(0);
    }

    function setDefaultUIDisplayMsg() {
        global $objMTS;
        global $UIMatlUpdateStatusDisplayMsg;
        global $UIDelvStatusDisplayMsg;
        global $UIMatlUpdateStatusID;
        global $UIDelvExecInsNo;
        global $UIDelvExecMenuId;
        global $DelvExecFlg;
        global $SyncStatusNameobj;
    
        $UIMatlUpdateStatusDisplayMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
        if($DelvExecFlg === true) {
            $UIDelvStatusDisplayMsg       = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4001");
        } else {
            $UIDelvStatusDisplayMsg       = "";
        }
        $UIMatlUpdateStatusID         = $SyncStatusNameobj->ERROR();
        $UIDelvExecInsNo              = "";
        $UIDelvExecMenuId             = "";
    }

    function setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId) {
        global $objMTS;
        global $UIMatlUpdateStatusDisplayMsg;
        global $UIDelvStatusDisplayMsg;
        global $UIMatlUpdateStatusID;
        global $UIDelvExecInsNo;
        global $UIDelvExecMenuId;
        global $DelvExecFlg;

        $UIMatlUpdateStatusDisplayMsg = $UIMatlSyncMsg;
        if($UIDelvMsg == "def") {
            if($DelvExecFlg === true) {
                $UIDelvStatusDisplayMsg       = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4001");
            } else {
                $UIDelvStatusDisplayMsg       = "";
            }
        } else {
            $UIDelvStatusDisplayMsg       = $UIDelvMsg;
        }
        $UIMatlUpdateStatusID         = $SyncSts;
        $UIDelvExecInsNo              = $DelvExecInsNo;
        $UIDelvExecMenuId             = $DelvExecMenuId;
    }
    function getUIMatlSyncStatus() {
        global $UIMatlUpdateStatusDisplayMsg;
        global $UIDelvStatusDisplayMsg;
        global $UIMatlUpdateStatusID;
        global $UIDelvExecInsNo;
        global $UIDelvExecMenuId;
        $ary = array();
        $ary[] = $UIMatlUpdateStatusDisplayMsg;
        $ary[] = $UIDelvStatusDisplayMsg;
        $ary[] = $UIMatlUpdateStatusID;
        $ary[] = $UIDelvExecInsNo;
        $ary[] = $UIDelvExecMenuId;
        return $ary;
    }

    function UpdateMatlLinkSyncStatus($MatlLinkId,$UpdateColumnAry) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;
        global $AddRepoIdMatlLinkIdStr;
        global $AddMatlLinkIdStr;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $TDMatlLinkobj;

        $ret = UpdateMatlLinkRecode($MatlLinkId,$UpdateColumnAry);
        return $ret;
    }
    function UpdateMatlLinkRecode($MatlLinkId,$UpdateColumnAry) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;
        global $AddRepoIdMatlLinkIdStr;
        global $AddMatlLinkIdStr;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $TDMatlLinkobj;

        $dbAcction      = "SELECT";
        $BindArray      = array('WHERE'=>"MATL_LINK_ROW_ID=:MATL_LINK_ROW_ID");

        $ColumnConfigArray = $TDMatlLinkobj->getColumnDefine();
        $ColumnValueArray  = $TDMatlLinkobj->setColumnConfigAttr();
        $objQueryArray = $DBobj->makeSelectSQLString($dbAcction,$BindArray,$TDMatlLinkobj,$ColumnConfigArray,$ColumnValueArray);
        $arrayBind = array();
        $arrayBind = array("MATL_LINK_ROW_ID"=>$MatlLinkId);
        $objQuery  = $DBobj->SelectForSimple($objQueryArray[1],$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
            $logstr .= $AddRepoIdMatlLinkIdStr;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }
        $num_of_rows = $objQuery->effectedRowCount();
        if($objQuery->effectedRowCount() != 1) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");   // 2004
            $logstr .= $AddRepoIdMatlLinkIdStr;
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
                $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2028",array($AddMatlLinkIdStr));
                require ($root_dir_path . $log_output_php );
            }
            return true;
        }
        $BindArray      = array();
        $ColumnConfigArray = $TDMatlLinkobj->setColumnConfigAttr();
        $ColumnValueArray  = $row;
        $JnlInsert_Flag    = true;
        $ret = $DBobj->UpdateRow($BindArray,$TDMatlLinkobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            // "データベースのアクセスに失敗しました。";
            $logstr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");   // 2004
            $logstr .=  $AddRepoIdMatlLinkIdStr;
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,$DBobj->GetLastErrorMsg());
            return $FREE_LOG;
        }

        //トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = $objMTS->getSomeMessage("ITACICDFORIAC-STD-2027",array($AddMatlLinkIdStr));
            require ($root_dir_path . $log_output_php );
        }
        return true;
    }

    function getTargetMatlLinkRow($RepoId,$MatlLinkId,&$tgtMatlLinkRow) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;
        global $AddRepoIdMatlLinkIdStr;
        global $AddMatlLinkIdStr;

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
                          T1.MATL_LINK_ROW_ID  =  :MATL_LINK_ROW_ID ";

        $arrayBind                       = array();
        $arrayBind['MATL_LINK_ROW_ID']   = $MatlLinkId;

        $objQuery = $DBobj->SelectForSimple($sqlBody,$arrayBind);
        if($objQuery === false) {
            // 異常フラグON
            $error_flag = 1;
            
            // データベースのアクセスに失敗しました。
            $logstr  = $logstr = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-1005");
            $logstr .= $AddRepoIdMatlLinkIdStr;
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
    function CreateZipFile($inRolesDir,&$outRolesDir,&$zipFileName) {
        $outRolesDir  = "/tmp/zipdir_" . getmypid();
        exec("/bin/rm -rf " . $outRolesDir);
        exec("/bin/mkdir -p " . $outRolesDir);
        $inRolesDir = preg_replace("/\/roles$/","",$inRolesDir);
        $zipFileName = basename($inRolesDir) .".zip";
        $cmd = "cd $inRolesDir;zip -r $outRolesDir/$zipFileName *";
        exec($cmd,$output,$retcode);
        if($retcode != 0) {
            $ret = implode("\n",$output);
            return $ret;
        }
        return true;
    }
    function MailLinkExecute($RepoId,$MatlLinkId) {
        global $cmDBobj;
        global $DBobj;
        global $error_flag;
        global $warning_flag;
        global $AddRepoIdMatlLinkIdStr;
        global $AddMatlLinkIdStr;

        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        global $objMTS;
        global $objDBCA;
        global $TDMatlLinkobj;
        global $ansible_driver;
        global $terraform_driver;
        global $LFCobj;
        global $MatlLinkUpdate_Flg;
        global $DelvExecFlg;
        global $SyncStatusNameobj;

        // 資材紐付管理より対象レコード取得
        $tgtMatlLinkRow = array();
        $ret = getTargetMatlLinkRow($RepoId,$MatlLinkId,$tgtMatlLinkRow);
        if($ret !== true) {
            // 異常フラグON
            $error_flag = 1;

            $LogStr  = "";
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,$ret);
            // 想定外のエラー
            $UIMatlSyncMsg   = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }

        // 処理対象か判定
        if(count($tgtMatlLinkRow) == 0)  {
            // 対象レコードなし
            // 異常フラグON
            $error_flag = 1;

            // 対象レコードなし
            // 資材紐付管理の対象レコードが見つかりません。資材紐付処理をスキップします。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})";
            $LogStr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2040",array($RepoId,$MatlLinkId));
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
            $UIMatlSyncMsg   = $LogStr;
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        } else {
             // データ移送
             $row = $tgtMatlLinkRow[0];

             // 廃止レコードか判定
             if($row['DISUSE_FLAG'] == '1') {
                 // UIで廃止できてしまうので、廃止されていたらexitする。
                 exit(0);
//                 // 異常フラグON
//                 $error_flag = 1;
//
//                 // 資材紐付管理が廃止状態です。資材紐付処理をスキップします。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})";
//                 $LogStr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2041",array($RepoId,$MatlLinkId));
//                 $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
//                 $UIMatlSyncMsg   = $LogStr;
//                 $UIDelvMsg       = "def";
//                 $SyncSts         = TD_B_CICD_REPO_SYNC_STATUS_NAME::C_SYNC_STATUS_ROW_ID_ERROR;
//                 $DelvExecInsNo   = "";
//                 $DelvExecMenuId  = "";
//                 setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
//                 return $FREE_LOG;
             } else {
                 // 自動同期が無効か判定
                 if($row['AUTO_SYNC_FLG'] == TD_B_CICD_MATERIAL_LINK_LIST::C_AUTO_SYNC_FLG_OFF) {
                     // UIで自動同期を無効にできてしまうので、無効にされていたらexitする。
                     exit(0);
//                     // 異常フラグON
//                     $error_flag = 1;
//
//                     // 対象レコードなし
//                     // 資材紐付管理の自動同期が無効です。資材紐付処理をスキップします。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})";
//                     $LogStr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2042",array($RepoId,$MatlLinkId));
//                     $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
//                     $UIMatlSyncMsg   = $LogStr;
//                     $UIDelvMsg       = "def";
//                     $SyncSts         = TD_B_CICD_REPO_SYNC_STATUS_NAME::C_SYNC_STATUS_ROW_ID_ERROR;
//                     $DelvExecInsNo   = "";
//                     $DelvExecMenuId  = "";
//                     setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
//                     return $FREE_LOG;
                 }
             }
        }

        /////////////////////////////////////////////////////////
        // 資材紐付管理 紐付先資材タイプとインストール状態をチェック
        /////////////////////////////////////////////////////////
        $LogStr = "";
        $ret = MatlLinkColumnValidator4($RepoId,$MatlLinkId,$row['MATL_TYPE_ROW_ID'],$objMTS,$LogStr,$ansible_driver,$terraform_driver);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
            $UIMatlSyncMsg   = $LogStr;
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }

        /////////////////////////////////////////////////////////
        // 紐付先資材タイプと資材パスの組み合わせチェック
        /////////////////////////////////////////////////////////
        $LogStr = "";
        $ret = MatlLinkColumnValidator2($row['MATL_TYPE_ROW_ID'],$row['M_MATL_FILE_TYPE_ROW_ID'],$objMTS,$RepoId,$MatlLinkId,$LogStr);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
            $UIMatlSyncMsg   = $LogStr;
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }

        /////////////////////////////////////////////////////////
        // 紐付先資材タイプとMovemnetの組み合わせチェック
        /////////////////////////////////////////////////////////
        $LogStr = "";
        $ret = MatlLinkColumnValidator5($RepoId,$MatlLinkId,$row['M_ITA_EXT_STM_ID'],$row['MATL_TYPE_ROW_ID'],$objMTS,$LogStr);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
            $UIMatlSyncMsg   = $LogStr;
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }

        /////////////////////////////////////////////////////////
        // 対象レコードのリレーション先確認
        /////////////////////////////////////////////////////////
        $LogStr = "";
        $ret = MatlLinkColumnValidator3($row,$objMTS,$LogStr);
        if($ret === false) {
            // 異常フラグON
            $error_flag = 1;

            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
            $UIMatlSyncMsg   = $LogStr;
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }

        //////////////////////////////////////////////////////////////////
        // 対象資材ファイルをbase64でエンコード
        //////////////////////////////////////////////////////////////////
        $inDir = $LFCobj->getLocalCloneDir($RepoId);
        $tgtFileName = $inDir . "/" . $row['M_MATL_FILE_PATH'];
        if( ! file_exists($tgtFileName)) {
            // 異常フラグON
            $error_flag = 1;

            // 資材ファイルがローカルクローンディレクトリ内に見つかりません。((リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{} 資材ファイル:{})
            $LogStr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2043",array($RepoId,$MatlLinkId,$tgtFileName));
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
            // 想定外のエラー
            $UIMatlSyncMsg   = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }
        $outRolesDir = "";
        $zipFileName = "";
        // ファイルタイプを判定
        if($row['M_MATL_FILE_TYPE_ROW_ID'] == TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_ROLES) {
             // rolesディレクトリの場合
             $cloneRepoDir = $LFCobj->getLocalCloneDir($RepoId);
             $rolesDir = $cloneRepoDir . "/" . $row['M_MATL_FILE_PATH'];
             $ret = CreateZipFile($rolesDir,$outRolesDir,$zipFileName);
             if($ret !== true) {
                 // 異常フラグON
                 $error_flag = 1;

                 // zipファイルの作成に失敗しました。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{} 資材ファイル:{})
                 $LogStr  = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2043",array($RepoId,$MatlLinkId,$tgtFileName));
                 $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,$ret);
                 // 想定外のエラー
                 $UIMatlSyncMsg   = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
                 $UIDelvMsg       = "def";
                 $SyncSts         = $SyncStatusNameobj->ERROR();
                 $DelvExecInsNo   = "";
                 $DelvExecMenuId  = "";
                 setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
                 return $FREE_LOG;
             }
             $tgtFileName = $outRolesDir . "/" . $zipFileName;
        }
        $tgtFileBase64enc = base64_encode(file_get_contents($tgtFileName));
        // zipファイル削除
        $cmd = "/bin/rm -rf $outRolesDir";
        exec($cmd);

        //////////////////////////////////////////////////////////////////
        // Restユーザのデフォルトアクセス権のあるロール名リストを取得
        //////////////////////////////////////////////////////////////////
        $DefaultAccessRoleString = "";
        if(strlen($row['RBAC_FLG_ROW_ID']) == 0){
            $row['RBAC_FLG_ROW_ID'] = TD_B_CICD_RBAC_FLG_NAME::C_RBAC_FLG_ROW_ID_OFF;
        }
        if($row['RBAC_FLG_ROW_ID'] == TD_B_CICD_RBAC_FLG_NAME::C_RBAC_FLG_ROW_ID_ON) {
            $RoleList = array();
            $obj = new RoleBasedAccessControl($objDBCA);
            $DefaultAccessRoleString = $obj->getDefaultAccessRoleString($row['M_REST_USER_ID'],'NAME',true);  // 廃止を含む
            unset($obj);
            if($DefaultAccessRoleString === false) {
                // 異常フラグON
                $error_flag = 1;

                $LogStr  = "Failed get Role information. (User ID : " . $row['M_REST_USER_ID'] . ")";
                $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,"");
                // 想定外のエラー
                $UIMatlSyncMsg   = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-4000");
                $UIDelvMsg       = "def";
                $SyncSts         = $SyncStatusNameobj->ERROR();
                $DelvExecInsNo   = "";
                $DelvExecMenuId  = "";
                setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
                return $FREE_LOG;
            }
        }

        switch($row['MATL_TYPE_ROW_ID']) {
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
            list($RequestData,$filterList) = setPlaybookListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2047",array($RepoId,$MatlLinkId));
            break;
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
            list($RequestData,$filterList) = setDialogListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2048",array($RepoId,$MatlLinkId));
            break;
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
            list($RequestData,$filterList) = setRolePackeageListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2049",array($RepoId,$MatlLinkId));
            break;
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
            list($RequestData,$filterList) = setContentListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2050",array($RepoId,$MatlLinkId));
            break;
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
            list($RequestData,$filterList) = setTemplateListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2051",array($RepoId,$MatlLinkId));
            break;
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2052",array($RepoId,$MatlLinkId));
            list($RequestData,$filterList) = setModuleListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            break;
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2053",array($RepoId,$MatlLinkId));
            list($RequestData,$filterList) = setPolicyListAttr($row,$tgtFileName,$DefaultAccessRoleString);
            break;
        }

        /////////////////////////////////////////////////////////
        // 資材更新
        /////////////////////////////////////////////////////////
        $objCRAA = new CicdRestAccessAgent($objMTS);
        $NoUpdateFlg = true;
        $retAry = $objCRAA->materialsRestAccess($row['MATL_TYPE_ROW_ID'], 
                                                $row['MATL_LINK_NAME'],
                                                $tgtFileBase64enc, 
                                                $RequestData, 
                                                $filterList, 
                                                $row['M_USERNAME_JP'], 
                                                $row['M_REST_USERNAME'], 
                                                ky_decrypt($row['M_REST_LOGIN_PW']),
                                                $row['M_HOSTNAME'],
                                                $row['M_PROTOCOL'],
                                                $row['M_PORT'],
                                                $NoUpdateFlg);
        if($retAry[0] == "000") {
            // 差分なし判定
            if($NoUpdateFlg === true) {
                // 資材に差分がなく、同期状態が正常でない場合は、状態を正常に設定
                if($row['SYNC_STATUS_ROW_ID'] != $SyncStatusNameobj->NORMAL()) {
                    $LogStr  = $ErrorMsgHeder;
                    $UIMatlSyncMsg   = "";
                    $UIDelvMsg       = "";
                    $SyncSts         = $SyncStatusNameobj->NORMAL();
                    $DelvExecInsNo   = "";
                    $DelvExecMenuId  = "";
                    setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
                    return true;
                } else {
                    // 状態更新が不要であることをマーク
                    $MatlLinkUpdate_Flg = true;
                    return true;
                }
            }
        } else {
            // 異常フラグON
            $error_flag = 1;
    
            $LogStr  = $ErrorMsgHeder;
            $AddLogStr = var_export($retAry,true);
            $FREE_LOG  = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,$AddLogStr);
            $UIMatlSyncMsg   = $ErrorMsgHeder . "\n" . var_export($retAry,true);
            $UIDelvMsg       = "def";
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }

        // 作業実行が不要な場合
        if($DelvExecFlg === false) {
            $UIMatlSyncMsg   = "";
            $UIDelvMsg       = "";
            $SyncSts         = $SyncStatusNameobj->NORMAL();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return true;
        }

        /////////////////////////////////////////////////////////
        // 実行種別(ドライラン)設定
        /////////////////////////////////////////////////////////
        if(strlen($row['DEL_EXEC_TYPE']) == 0) {
            $runMode = 1;
        } else {
            $runMode = 2;
        }

        switch($row['M_ITA_EXT_STM_ID']) {
        case TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY:
            $UIDelvExecMenuId = "2100020112";
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2054",array($RepoId,$MatlLinkId));
            break;
        case TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE:
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2055",array($RepoId,$MatlLinkId));
            $UIDelvExecMenuId = "2100020313";
            break;
        case TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER:
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2056",array($RepoId,$MatlLinkId));
            $UIDelvExecMenuId = "2100020212";
            break;
        case TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_TERRAFORM:
            $ErrorMsgHeder = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2057",array($RepoId,$MatlLinkId));
            $UIDelvExecMenuId = "2100080010";
            break;
        }

        /////////////////////////////////////////////////////////
        // Movement実行
        /////////////////////////////////////////////////////////
        $retAry = $objCRAA->executeMovement($row['M_ITA_EXT_STM_ID'], 
                                            $row['DEL_OPE_ID'], 
                                            $row['DEL_MOVE_ID'], 
                                            $runMode,
                                            $row['M_REST_USERNAME'], 
                                            ky_decrypt($row['M_REST_LOGIN_PW']),
                                            $row['M_HOSTNAME'],
                                            $row['M_PROTOCOL'],
                                            $row['M_PORT']);

        if($retAry[0] == "000") {
            $UIMatlSyncMsg   = "";
            $UIDelvMsg       = "";
            $SyncSts         = $SyncStatusNameobj->NORMAL();
            $DelvExecInsNo   = $retAry[1];
            $DelvExecMenuId  = $UIDelvExecMenuId;
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
        } else {
            // 異常フラグON
            $error_flag = 1;

            $LogStr          = $ErrorMsgHeder;
            $AddLogStr       = var_export($retAry,true);
            $FREE_LOG        = makeLogiFileOutputString(basename(__FILE__),__LINE__,$LogStr,$AddLogStr);
            $UIMatlSyncMsg   = "";
            $UIDelvMsg       = $ErrorMsgHeder . "\n" . $AddLogStr;
            $SyncSts         = $SyncStatusNameobj->ERROR();
            $DelvExecInsNo   = "";
            $DelvExecMenuId  = "";
            setUIMatlSyncStatus($UIMatlSyncMsg,$UIDelvMsg,$SyncSts,$DelvExecInsNo,$DelvExecMenuId);
            return $FREE_LOG;
        }
        return true;
    }

    // ファイル管理
    function setContentListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['MATL_LINK_NAME'];
        $RequestData[4] = basename($tgtFileName);
        $RequestData[5] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['MATL_LINK_NAME'];
        return [$RequestData , $FilterList];
    }

    // テンプレート管理
    function setTemplateListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['MATL_LINK_NAME'];
        $RequestData[4] = basename($tgtFileName);
        $RequestData[5] = $row['TEMPLATE_FILE_VARS_LIST'];
        $RequestData[6] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['MATL_LINK_NAME'];
        return [$RequestData , $FilterList];
    }
    // Playbook素材集
    function setPlaybookListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['MATL_LINK_NAME'];
        $RequestData[4] = basename($tgtFileName);
        $RequestData[5] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['MATL_LINK_NAME'];
        return [$RequestData , $FilterList];
    }

    //対話ファイル素材集
    function setDialogListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['M_DIALOG_TYPE_NAME'];
        $RequestData[4] = $row['M_OS_TYPE_NAME'];
        $RequestData[5] = basename($tgtFileName);
        $RequestData[6] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['M_DIALOG_TYPE_ID'];
        $FilterList[4]  = $row['M_OS_TYPE_ID'];
        return [$RequestData , $FilterList];
    }

    // ロールパッケージ管理
    function setRolePackeageListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['MATL_LINK_NAME'];
        $RequestData[4] = basename($tgtFileName);
        $RequestData[5] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['MATL_LINK_NAME'];
        return [$RequestData , $FilterList];
    }

    // Module素材
    function setModuleListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['MATL_LINK_NAME'];
        $RequestData[4] = basename($tgtFileName);
        $RequestData[5] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['MATL_LINK_NAME'];
        return [$RequestData , $FilterList];
    }

    // Policy素材
    function setPolicyListAttr($row,$tgtFileName,$DefaultAccessRoleString) {
        $RequestData    = array();
        $RequestData[3] = $row['MATL_LINK_NAME'];
        $RequestData[4] = basename($tgtFileName);
        $RequestData[5] = $DefaultAccessRoleString;
        $FilterList     = array();
        $FilterList[3]  = $row['MATL_LINK_NAME'];
        return [$RequestData , $FilterList];
    }
?>
