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

/**
 * アップロードファイルを配置する
 * 
 * @param    string    $objMTS          メッセージテンプレートクラス
 * @param    string    $base64File      配置するファイル(base64)
 * @param    string    $fileName        配置するファイル名
 * @param    string    $upFilePath      配置先パス
 * @param    string    $upJnlFilePath   配置先パス(履歴用)
 */
function deployUploadFile($objMTS, $base64File, $fileName, $upFilePath, $upJnlFilePath){
    // 配置先パスの存在を調べる
    if(!file_exists($upFilePath)){

        // ディレクトリを作成する
        $orgUmask = umask(0000);
        $result = mkdir($upFilePath, 0777, true);
        umask($orgUmask);
        
        if(true != $result){
            return $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $upFilePath);
        }
    }

    // ファイルを配置先パスに格納する
    $output = NULL;
    $result = file_put_contents($upFilePath . $fileName, base64_decode($base64File));

    if(false === $result){
        return $objMTS->getSomeMessage('ITAMATERIAL-ERR-5009', $upFilePath . $fileName);
    }

    // 配置先パス(履歴用)の存在を調べる
    if(!file_exists($upJnlFilePath)){

        // ディレクトリを作成する
        $orgUmask = umask(0000);
        $result = mkdir($upJnlFilePath, 0777, true);
        umask($orgUmask);
        
        if(true != $result){
            return $objMTS->getSomeMessage('ITAMATERIAL-ERR-5006', $upJnlFilePath);
            return $msg;
        }
    }

    // ファイルを配置先パス(履歴用)に格納する
    $output = NULL;
    $result = file_put_contents($upJnlFilePath . $fileName, base64_decode($base64File));

    if(false === $result){
        return $objMTS->getSomeMessage('ITAMATERIAL-ERR-5009', $upJnlFilePath . $fileName);
        return $msg;
    }

    return true;
}

