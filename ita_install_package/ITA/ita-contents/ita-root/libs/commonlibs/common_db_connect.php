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
    //    ・ModuleDistictCode(101)
    //
    //////////////////////////////////////////////////////////////////////

    // ----ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    }
    // ルートディレクトリを取得----

    // サイト共通の自作PHPファンクション集を呼び出し
    require_once ($root_dir_path . "/libs/commonlibs/common_php_functions.php");
    require_once ($root_dir_path . "/libs/commonlibs/common_php_classes.php");

    // ----DBコネクト

    $tmpResult = true;
    $objDBCA = null;

    $objDBCA = new DBConnectAgent();
    $tmpResult = $objDBCA->connectOpen();
    $db_model_ch = $objDBCA->getModelChannel();

    // DBコネクト----

    // 異常ハンドリング
    if($tmpResult === false){
        // 結果格納変数を解放
        unset($tmpResult);
        
        // オンラインアクセスの場合
        if( $arrayReqInfo[0] == "web" ){
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-1"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(500,10110101); //101-101-01
            exit();
        }
        // バックヤードアクセスの場合
        else{
            $error_flag = 1;

            // 例外処理へ移行
            throw new Exception( $objMTS->getSomeMessage("ITAWDCH-ERR-2") );
        }
    }
    else{
        // 結果格納変数を解放
        unset($tmpResult);
    }
?>
