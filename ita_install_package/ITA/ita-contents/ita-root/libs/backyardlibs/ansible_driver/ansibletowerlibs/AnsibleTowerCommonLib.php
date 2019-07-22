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
////////////////////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・AnsibleTower 共通ライブラリ
//
////////////////////////////////////////////////////////////////////////////////////

if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/setenv.php");  

function addPadding($num) {
    return str_pad($num, INT_NUM_PADDING, "0", STR_PAD_LEFT );
}

function createRelayedDataZIP($tgt_execution_no, $zipFilePrefix, $zip_data_source_dir, $zip_save_base_dir) {

    global $zip_temp_save_dir;

    $zip_filename = $zipFilePrefix . addPadding($tgt_execution_no) . '.zip' ;

    $zip_temp_filepath = $zip_temp_save_dir . "/" . $zip_filename;

    // OSコマンドでzip圧縮する
    $temp_str_command = "cd " . $zip_data_source_dir . "; zip -r " . $zip_temp_filepath . " .";
    shell_exec($temp_str_command);

    $exeins_utn_file_dir = $zip_save_base_dir . "/" . addPadding($tgt_execution_no);
    $zip_filepath = $exeins_utn_file_dir . "/" . $zip_filename;

    system('/bin/rm -rf ' . $exeins_utn_file_dir . ' > /dev/null 2>&1');

    if(!is_dir($exeins_utn_file_dir)) {
        // ここ(UTNのdir)だけは再帰的に作成する
        if(!mkdir( $exeins_utn_file_dir, 0777, true)) {
            // 事前準備を中断
            throw new Exception("Faild to mkdir ($exeins_utn_file_dir)");
        }
        if(!chmod($exeins_utn_file_dir, 0777)) {
            // 事前準備を中断
            throw new Exception("Faild to chmod ($exeins_utn_file_dir)");
        }
    }

    // zipファイルを正式な置き場に移動
    rename($zip_temp_filepath, $zip_filepath);

    // zipファイルの存在を確認
    if(!file_exists($zip_filepath)) {
        throw new Exception("Faild to make ZIP of input files. (" . $zip_filepath .  ")");
    }

    return array($zip_filename, $exeins_utn_file_dir);
}

function moveRelayedDataZIPtoJnlDir($intJournalSeqNo, $zip_filename, $exeins_utn_file_dir) {

    $exeins_zip_filepath = $exeins_utn_file_dir . "/" . $zip_filename;

    $jnl_file_dir_trunk = $exeins_utn_file_dir . "/old";
    $jnl_file_dir_focus = $jnl_file_dir_trunk . "/" . addPadding($intJournalSeqNo);

    // 履歴フォルダへコピー
    if( !mkdir( $jnl_file_dir_trunk, 0777 ) ){
        return array(false, "Faild to copy result file to journal directory. Cause of mkdir(old)");
    }

    if( !mkdir( $jnl_file_dir_focus, 0777 ) ){
        return array(false, "Faild to copy result file to journal directory. Cause of mkdir(old/xxxxxx)");
    }

    $jnl_zip_filepath = $jnl_file_dir_focus . "/" . $zip_filename;

    $boolCopy = copy($exeins_zip_filepath, $jnl_zip_filepath);
    if( $boolCopy === false ){
        return array(false, "Faild to copy result file to journal directory. Cause of copy to " . $jnl_zip_filepath);
    }

    return array(true, "Copy result file to journal directory." . $jnl_zip_filepath);
}

function getNewestExeInsJnlId($tgt_execution_no) {
    global $vg_exe_ins_msg_table_name_jnl;

    $objDBCA = $GLOBALS['objDBCA']; // 呼び出しが統一しきれておらず、、、

    $sql = "SELECT JOURNAL_SEQ_NO
            FROM $vg_exe_ins_msg_table_name_jnl 
            WHERE EXECUTION_NO = :EXECUTION_NO
            ORDER BY JOURNAL_SEQ_NO DESC;";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus() === false) {
        throw new Exception("FUNCTION: " . __FUNCTION__ . " sql: " . $sql . " ERROR: " . $objQuery->getLastError());
    }

    $objQuery->sqlBind(array('EXECUTION_NO' => $tgt_execution_no));
    if(isset($arrayBind) && $objQuery->sqlBind($arrayBind) != "") {
        throw new Exception("FUNCTION: " . __FUNCTION__ . " sql: " . $sql . " ERROR: " . $objQuery->getLastError());
    }

    $r = $objQuery->sqlExecute();
    if(!$r) {
        throw new Exception("FUNCTION: " . __FUNCTION__ . " sql: " . $sql . " ERROR: " . $objQuery->getLastError());
    }

    while($row = $objQuery->resultFetch()) {
        $aryRow[] = $row;
    }

    if(count($aryRow) < 1) {
        throw new Exception("FUNCTION: " . __FUNCTION__ . " sql: " . $sql . " ERROR: no exe_ins_jnl row.");
    }
    $rowOfExeInsJnl = $aryRow[0]; // 最新1レコードのみ

    $intJournalSeqNo = $rowOfExeInsJnl['JOURNAL_SEQ_NO'];

    return $intJournalSeqNo;
}

// 確認君でログを吐き、作業確認でそのログをtail描画するためのファイル入出力排他処理用
function getSemaphoreKey() {
    return sem_get(ftok(__FILE__, "p"), 1); // ftokの引数は一意に決まればいいので任意
}

// 実行君(の中のprepare)と確認君での緊急停止監視
function isScrammedExecution($dbAccess, $tgt_execution_no) {

    global $vg_exe_ins_msg_table_name;

    $row = $dbAccess->selectRow($vg_exe_ins_msg_table_name, $tgt_execution_no);

    if($row['STATUS_ID'] == SCRAM) {
        return true;
    }

    return false;
}

/* <START> グローバル変数の情報をデータベースより取得する----------------------------------------- */
function getDBGlobalVars(
     &$ina_global_vars_list // グローバル変数のリスト
    ,&$in_msgstr            // メッセージ用の変数
){
    global $objDBCA;
    global $objMTS;

    $sql
     = "                      SELECT"
     . "                         VARS_NAME"
     . "                        ,VARS_ENTRY"
     . "                      FROM"
     . "                         B_ANSTWR_GLOBAL_VARS"
     . "                      WHERE"
     . "                         DISUSE_FLAG = '0'";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if($objQuery->getStatus()===false) {
        $in_msgstr = 'DB access error' . var_export(array(basename(__FILE__),__LINE__) , true);

        return false;
    }
    $r = $objQuery->sqlExecute();
    if (!$r) {
        $in_msgstr = 'DB access error' . var_export(array(basename(__FILE__),__LINE__) , true);
        $in_msgstr = $in_msgstr . "\n" . $objQuery->getLastError();

        unset($objQuery);

        return false;
    }

    $ina_global_vars_list = array();

    while ( $row = $objQuery->resultFetch() ) {
        $ina_global_vars_list[$row['VARS_NAME']] = $row['VARS_ENTRY'];
    }

    // DBアクセス事後処理
    unset($objQuery);

    return true;
}
/* < END > グローバル変数の情報をデータベースより取得する-------------------------------- */
