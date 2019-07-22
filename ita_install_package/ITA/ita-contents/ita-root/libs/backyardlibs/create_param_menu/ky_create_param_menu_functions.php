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
/**
 * 【処理内容】
 *    関数定義
 */


/**
 * ログを出力する
 * 
 * @param    string    $msg    出力するメッセージ
 */
function outputLog($msg){
    global $logPrefix;

    $bt = debug_backtrace();
    $file = basename($bt[0]['file']);
    $line = sprintf("%04d", $bt[0]['line']);

    $dt = '[' . date('Y/m/d H:i:s') . '][' . $file . '][' . $line . ']';
    $msg = $dt . $msg . "\n";
    $filePath = LOG_DIR . $logPrefix . date('Ymd') . '.log';
    error_log($msg, 3, $filePath);
}

