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
//      保存期間切れファイル削除
//          指定したディレクトリ直下のファイルのうち、
//          タイムスタンプが保存期間より過去のファイルを削除する。
//
//  【起動パラメータ】
//      php 本スクリプト パラメータファイル
//
//  【パラメータファイル】
//      定義内容
//        p1,p2,p3,p4
//
//        p1: 検索対象ディレクトリ
//
//        p2: ファイル名
//        ワイルドカードで指定可能（すべてのファイルの場合は「*」を設定）
//
//        np3: 配下のディレクトリを削除するかどうか
//        削除する場合は「yes」を設定、「yes」以外が設定された場合は削除されない
//
//        p4: 保存期間（日）
//
//////////////////////////////////////////////////////////////////////

// 起動しているshellの起動判定を正常にするための待ち時間
sleep(5);

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

////////////////////////////////
// PHPエラー時のログ出力先設定//
////////////////////////////////
$tmpVarTimeStamp = time();
$logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

////////////////////////////////
// 定数定義                   //
////////////////////////////////
$log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
$php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
$db_connect_php      = '/libs/commonlibs/common_db_connect.php';

////////////////////////////////
// ローカル変数(全体)宣言     //
////////////////////////////////
$warning_flag               = 0;        // 警告フラグ(1：警告発生)
$error_flag                 = 0;        // 異常フラグ(1：異常発生)

try{
    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require ($root_dir_path . $php_req_gate_php);

    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = "START";  
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require ($root_dir_path . $db_connect_php );
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        $FREE_LOG = "DB Connect success.";  
        LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
    }

    ///////////////////////////////////////////
    // 起動パラメータからパラメータファイル取得
    ///////////////////////////////////////////
    if($argc < 1){
        $error_flag = 1;
        throw new Exception("The startup parameter is not valid.");
    }

    /////////////////////////////////////////////////////////////////////
    // ファイル削除管理のデータを取得する
    /////////////////////////////////////////////////////////////////////
    $sql = "SELECT " .
           " ROW_ID, DEL_DAYS, TARGET_DIR, TARGET_FILE, DEL_SUB_DIR_FLG " .
           "FROM A_DEL_FILE_LIST " .
           "WHERE DISUSE_FLAG='0'";

    $objQuery = $objDBCA->sqlPrepare($sql);

    if($objQuery->getStatus()===false){
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
        unset($objQuery);
        throw new Exception("DB error occurred (TABLE:A_DEL_FILE_LIST).");
    }
    $r = $objQuery->sqlExecute();
    if (!$r){
        LocalLogPrint(basename(__FILE__),__LINE__,$sql);
        LocalLogPrint(basename(__FILE__),__LINE__,$objQuery->getLastError());
        unset($objQuery);
        throw new Exception("DB error occurred (TABLE:A_DEL_FILE_LIST).");
    }

    // 現在時刻を取得
    $nowTime = time();

    /////////////////////////////////////////////////////////////////////
    // ファイル削除管理のデータ数ループ
    /////////////////////////////////////////////////////////////////////
    while ( $row = $objQuery->resultFetch() ){

        // ディレクトリ存在確認
        if(!is_dir($row['TARGET_DIR'])){
            LocalLogPrint(basename(__FILE__),__LINE__,sprintf("Directory is not exists. No=[%s], Directory=[%s]",  $row['ROW_ID'], $row['TARGET_DIR']));
            continue;
        }

        // 比較対象の時刻を計算
        $targetTime = $nowTime - $row['DEL_DAYS'] * 24 * 60;

        // 直下のディレクトリ・ファイルを取得
        $dataArray = glob($row['TARGET_DIR'] . "/" . $row['TARGET_FILE']);

        // 直下のデータ数ループ
        foreach($dataArray as $data){

            // サブディレクトリ削除有無が「しない」の場合、ディレクトリをスキップ
            if($row['DEL_SUB_DIR_FLG'] != "1" && is_dir($data)){
                continue;
            }

            // タイムスタンプを比較して削除
            if(filemtime($data) < $targetTime){
                $output = NULL;
                $cmd = "rm -rf -- '$data' 2>&1";

                exec($cmd, $output, $return_var);

                if(0 != $return_var){
                    LocalLogPrint(basename(__FILE__),__LINE__,sprintf("Error occurred with command [%s]. Error=[%s].", $cmd, print_r($output, true)));
                    continue;
                }
                if ( $log_level === 'DEBUG' ){
                    $FREE_LOG = "[$data] has been deleted.";  
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
            }
        }
    }
}
catch (Exception $e){
    // 例外メッセージ出力
    $FREE_LOG = $e->getMessage();
    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
}

////////////////////////////////
//// 結果出力               ////
////////////////////////////////
// 処理結果コードを判定してアクセスログを出し分ける
if( $error_flag != 0 ){
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        LocalLogPrint(basename(__FILE__),__LINE__,"ERROR END");
    }
    // cron起動なので exit-code 2 で終了
    exit(2);
}
elseif( $warning_flag != 0 ){
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        LocalLogPrint(basename(__FILE__),__LINE__,"WARNING END");
    }        
    // cron起動なので exit-code 1 で終了
    exit(1);
}
else{
    // トレースメッセージ
    if ( $log_level === 'DEBUG' ){
        LocalLogPrint(basename(__FILE__),__LINE__,"NORMAL END");
    }
    exit(0);
}

function LocalLogPrint($p1,$p2,$p3){
    global $log_output_dir;
    global $log_file_prefix;
    global $log_level;
    global $root_dir_path;
    global $log_output_php;

    $FREE_LOG = "FILE:[$p1] LINE:[" . sprintf("%04d", $p2) . "] $p3";
    require ($root_dir_path . $log_output_php);
}

?>