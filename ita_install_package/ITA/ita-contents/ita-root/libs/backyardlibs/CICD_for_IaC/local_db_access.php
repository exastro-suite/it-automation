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
require_once ($root_dir_path . '/libs/backyardlibs/common/common_db_access.php');

class LocalDBAccessClass {
    private $objDBCA;
    private $objMTS;
    private $logfile;
    private $log_level;
    private $LastErrorMsg;
    private $root_dir_path;
    private $log_output_php;
    private $cmDBobj;
    private $db_access_user_id;

    function __construct($db_model_ch,$cmDBobj,$objDBCA,$objMTS,$db_access_user_id,$logfile,$log_level){
        $this->db_model_ch       = $db_model_ch;
        $this->objDBCA           = $objDBCA;
        $this->objMTS            = $objMTS;
        $this->logfile           = $logfile;
        $this->log_level         = $log_level;
        $this->cmDBobj           = $cmDBobj;
        $this->db_access_user_id = $db_access_user_id;

        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $this->root_dir_path   = $root_dir_temp[0] . "ita-root";
        $this->log_output_php  = $this->root_dir_path . "/libs/backyardlibs/backyard_log_output.php";
        $this->ClearLastErrorMsg();
    }

    function ClearLastErrorMsg() {
        $this->LastErrorMsg = "";
    }

    function SetLastErrorMsg($errorDetail) {
        $this->LastErrorMsg = $errorDetail;
    }

    function GetLastErrorMsg() {
        $LastMsg = $this->LastErrorMsg;
        $this->ClearLastErrorMsg();
        return($LastMsg);
    }

    // トランザクション開始
    function transactionStart() {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        try {
            if( $this->objDBCA->transactionStart()===false ){
                $msg =  "function:transactionStart failed.";
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            // トレースメッセージ  ログファイルの指定がある場合
            if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
                //[処理]トランザクション開始
                $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2001");
                require ($this->log_output_php );
            }
            return true;
        } catch (Exception $e){
            //  例外発生
            $msg = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1001");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$e->getMessage());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
    }

    // コミット(レコードロックを解除)
    function transactionCommit() {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        try {
            $r = $this->objDBCA->transactionCommit();
            if (!$r){
                $msg = "function:transactionCommit failed.";
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            // トレースメッセージ  ログファイルの指定がある場合
            if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
                //[処理]コミット
                $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2002");
                require ($this->log_output_php );
            }
            return true;
        } catch (Exception $e){
            // 異常発生
            $msg = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1001");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$e->getMessage());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
    }

    function transactionRollBack() {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        try {
            $r = $this->objDBCA->transactionRollBack();
            if (!$r){
                $msg = "function:transactionRollBack failed";
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            // トレースメッセージ  ログファイルの指定がある場合
            if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
                //[処理]ロールバック
                $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2003");
                require ($this->log_output_php );
            }
            return true;
        } catch (Exception $e){
            // 異常発生
            $msg = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1001");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$e->getMessage());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
    }

    // トランザクション終了
    function transactionExit() {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        try {
            if($this->objDBCA->transactionExit() === false){
                $msg = "function:transactionExit failed.";
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            // トレースメッセージ  ログファイルの指定がある場合
            if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
                // [処理]トランザクション終了
                $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2004");
                require ($this->log_output_php );
            }
            return true;
        } catch (Exception $e){
            // 異常発生
            $msg = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1001");
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$e->getMessage());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
    }
    // $sqlBody SELECT SQL文
    //    $sqlBody   = "SELECT * FROM B_CICD_REPOSITORY_LIST WHERE DISUSE_FLAG=:DISUSE_FLAG";
    // $arrayBind  SELECT SQL文内にbind変数がある場合のハッシュ配列
    //    $arrayBind = array("DISUSE_FLAG"=>"0");
    function SelectForSimple($sqlBody,$arrayBind) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $this->cmDBobj->ClearLastErrorMsg();
        $objQuery  = "";
        $ret = $this->cmDBobj->dbaccessExecute($sqlBody, $arrayBind, $objQuery);
        if($ret === false) {
            $msg = "";  //dbaccessExecute message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return $objQuery;
    }
    // $dbAcction  以下の何れか
    //    SELECT / SELECT FOR UPDATE
    // $BindArray
    //   WHERE句を設定
    //     $BindArray = array('WHERE'=>"EXECUTION_NO = :EXECUTION_NO");
    // 戻り値
    //     $retArray[1] SELECT文
    //     $retArray[2] 予備
    //     $retArray[3] 予備
    //     $retArray[4] 予備
    function makeSelectSQLString($dbAcction,$BindArray,$TDobj,$ColumnConfigArray,$ColumnValueArray) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $TableName      = $TDobj->getTableName();
        $JnlTableName   = $TDobj->getJnlTableName();
        $PkeyColumnName = $TDobj->getPKColumnName();

        $retArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             $dbAcction,
                                             $PkeyColumnName,
                                             $TableName,
                                             $JnlTableName,
                                             $ColumnConfigArray, 
                                             $ColumnValueArray,
                                             $BindArray);

        if($retArray[0] === false ){
            $msg = "function:makeSQLForUtnTableUpdate failed.";
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        return $retArray;
    }
    function LockPkeySequence($table_seq_name) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $this->cmDBobj->ClearLastErrorMsg();
        $ret = $this->cmDBobj->dbacceSequenceLock($table_seq_name);
        if($ret === false) {
            $msg = "";  
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        // トレースメッセージ  ログファイルの指定がある場合
        if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
            // [処理]シーケンスロック (Sequence:{})
            $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2011",array($table_seq_name));
            require ($this->log_output_php );
        }
        return true;
    }
          
    // シーケンスをロックし履歴シーケンス払い出し
    function getPkeySequence($table_seq_name) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $this->cmDBobj->ClearLastErrorMsg();
        $ret = $this->cmDBobj->dbacceSequenceLock($table_seq_name);
        if($ret === false) {
            $msg = "";  //dbacceSequenceLock message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        // トレースメッセージ  ログファイルの指定がある場合
        if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
            // [処理]シーケンスロック (Sequence:{})";
            $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2011",array($table_seq_name));
            require ($this->log_output_php );
        }

        $this->cmDBobj->ClearLastErrorMsg();
        $retArray = $this->cmDBobj->dbaccessGetSequence($table_seq_name);
        if($retArray === null) {
            $msg = "";  //dbacceSequenceLock message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        // トレースメッセージ  ログファイルの指定がある場合
        if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
            // [処理]シーケンス採番 (Sequence:{})";
            $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2005",array($table_seq_name));
            require ($this->log_output_php );
        }
        return $retArray;
    }
    function InsertRow($TDobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag=true) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $TableName      = $TDobj->getTableName();
        $JnlTableName   = $TDobj->getJnlTableName();
        $PkeyColumnName = $TDobj->getPKColumnName();

        // 最終更新者を設定
        $ColumnValueArray["LAST_UPDATE_USER"] = $this->db_access_user_id;

        $this->ClearLastErrorMsg();
        $Pkey = $this->getPkeySequence($TDobj->getSequenceName());
        if($Pkey === false) {
            // $this->SetLastErrorMsg 済み 
            $msg = "";  //dbacceSequenceLock message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }
        // ジャーナル通番設定
        $ColumnValueArray[$PkeyColumnName] = $Pkey;

        // ジャーナル追加を判定
        if($JnlInsert_Flag === true) {
            $this->ClearLastErrorMsg();
            $Pkey = $this->getPkeySequence($TDobj->getJnlSequenceName());
            if($Pkey === false) {
                // $this->SetLastErrorMsg 済み 
                $msg = "";  //dbacceSequenceLock message set
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->getLastErrorMsg());
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            // ジャーナル通番設定
            $ColumnValueArray['JOURNAL_SEQ_NO'] = $Pkey;
        }

        $BindArray = array();
        $retArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             "INSERT",
                                             $PkeyColumnName,
                                             $TableName,
                                             $JnlTableName,
                                             $ColumnConfigArray,
                                             $ColumnValueArray,
                                             $BindArray);

        if($retArray[0] === false ){
            $msg = "function:makeSQLForUtnTableUpdate failed.";
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }

        $sqlCurBody   = $retArray[1];
        $arrayCurBind = $retArray[2];
        $sqlJnlBody   = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $ret = $this->cmDBobj->dbaccessExecute($sqlCurBody, $arrayCurBind);
        if($ret === false) {
            $msg = "";  //dbaccessExecute message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }

        // ジャーナル追加を判定
        if($JnlInsert_Flag === true) {
            $ret = $this->cmDBobj->dbaccessExecute($sqlJnlBody, $arrayJnlBind);
            if($ret === false) {
                $msg = "";  //dbaccessExecute message set
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            return true;
        }
    }

    function UpdateRow($BindArray,$TDobj,$ColumnConfigArray,$ColumnValueArray,$JnlInsert_Flag=true) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        // 最終更新者を設定
        $ColumnValueArray["LAST_UPDATE_USER"] = $this->db_access_user_id;

        // ジャーナル追加を判定
        if($JnlInsert_Flag === true) {
            $this->ClearLastErrorMsg();
            $Pkey = $this->getPkeySequence($TDobj->getJnlSequenceName());
            if($Pkey === false) {
                // $this->SetLastErrorMsg 済み 
                $msg = "";  //dbacceSequenceLock message set
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->getLastErrorMsg());
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
            // ジャーナル通番設定
            $ColumnValueArray['JOURNAL_SEQ_NO'] = $Pkey;
        }

        $TableName      = $TDobj->getTableName();
        $JnlTableName   = $TDobj->getJnlTableName();
        $PkeyColumnName = $TDobj->getPKColumnName();

        $retArray = makeSQLForUtnTableUpdate($this->db_model_ch,
                                             "UPDATE",
                                             $PkeyColumnName,
                                             $TableName,
                                             $JnlTableName,
                                             $ColumnConfigArray,
                                             $ColumnValueArray,
                                             $BindArray);

        if($retArray[0] === false ){
            $msg = "function:makeSQLForUtnTableUpdate failed.";
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,"");
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }

        $sqlCurBody   = $retArray[1];
        $arrayCurBind = $retArray[2];
        $sqlJnlBody   = $retArray[3];
        $arrayJnlBind = $retArray[4];

        $this->cmDBobj->ClearLastErrorMsg();
        $ret = $this->cmDBobj->dbaccessExecute($sqlCurBody, $arrayCurBind);
        if($ret === false) {
            $msg = "";  //dbaccessExecute message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
        }

        // ジャーナル追加を判定
        if($JnlInsert_Flag === true) {
            $this->cmDBobj->ClearLastErrorMsg();
            $ret = $this->cmDBobj->dbaccessExecute($sqlJnlBody, $arrayJnlBind);
            if($ret === false) {
                $msg = "";  //dbaccessExecute message set
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            }
        }
        return true;
    }
    function  dbaccessExecute($sqlBody, $arrayBind) {
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;

        $this->cmDBobj->ClearLastErrorMsg();
        $ret = $this->cmDBobj->dbaccessExecute($sqlBody, $arrayBind);
        if($ret === false) {
            $msg = "";  //dbaccessExecute message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
            $this->SetLastErrorMsg($FREE_LOG);
            return false;
       }
       return true;
   }
   function  setInnodbLockWaitTimeout() {
       global $root_dir_path;
       global $log_output_php;
       global $log_output_dir;
       global $log_file_prefix;
       global $log_level;

       $file = $root_dir_path . "/confs/backyardconfs/CICD_For_IaC/innodb_lock_wait_timeout.txt";
       if(file_exists($file)) {
            $TimerValue = file_get_contents($file);
            if($TimerValue === false) {
                // ファイルの読み込みに失敗しました。(file:{})
                $logstr = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1014",array($file)); //3008
                $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,"");
                $this->SetLastErrorMsg($FREE_LOG);
                return false;
            } else {
                // 改行コードが付いている場合に取り除く
                $TimerValue = str_replace("\n","",$TimerValue);
            }
       } else {
           // ファイルが見つかりませんでした。(file:{})
           $logstr = $this->objMTS->getSomeMessage("ITACICDFORIAC-ERR-1013",array($file)); //3007
           $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$logstr,"");
           $this->SetLastErrorMsg($FREE_LOG);
           return false;
       }
       $this->cmDBobj->ClearLastErrorMsg();
       $arrayBind = array();
       $sqlBody = sprintf("SET innodb_lock_wait_timeout=%s",$TimerValue);
       $ret = $this->cmDBobj->dbaccessExecute($sqlBody, $arrayBind);
       if($ret === false) {
           $msg = "";  //dbaccessExecute message set
           $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$this->cmDBobj->getLastErrorMsg());
           $this->SetLastErrorMsg($FREE_LOG);
           return false;
       }
       // トレースメッセージ  ログファイルの指定がある場合
       if (( $this->log_level === 'DEBUG' ) && (strlen($this->logfile) != 0)) {
           // "[処理]トランザクションロック待ちタイマを設定しました。(innodb_lock_wait_timeout:{})
           $FREE_LOG = $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2017",array($TimerValue));
           require ($this->log_output_php );
       }
       return true;
   }
}
?>
