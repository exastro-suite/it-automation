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
    //  【特記事項】
    //   呼び出し元にて「$log_file_prefix」を設定したうえで呼び出すべし
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
    // 定数宣言                   //
    ////////////////////////////////
    
    // ログ出力先ディレクトリ
    // (指定がない場合のみ)
    if( empty( $log_output_dir ) ) $log_output_dir = $root_dir_path . "/logs/backyardlogs";
    
    // ログファイル名プレフィックス
    // (指定がない場合のみ)
    if ( empty( $log_file_prefix ) ) $log_file_prefix = "default_";
    
    // ログファイル名ポストフィックス
    $log_file_postfix = ".log";
    
    ////////////////////////////////
    // 変数初期化                 //
    ////////////////////////////////
    
    // 変数初期化
    $p_FREE_LOG = "";
    
    // ログファイル名(フルパス)を作成
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . $log_file_postfix;
    
    // フリーログを準備
    if ( isset($FREE_LOG) ) $p_FREE_LOG = $FREE_LOG;
    
    // ログ出力
    $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);
    $filepointer=fopen(  $logfile, "a");
    flock($filepointer, LOCK_EX);
    fputs($filepointer, "[" . $logtime . "]" . $p_FREE_LOG . "\n" );
    flock($filepointer, LOCK_UN);
    fclose($filepointer);
    unset($tmpVarTimeStamp);
?>
