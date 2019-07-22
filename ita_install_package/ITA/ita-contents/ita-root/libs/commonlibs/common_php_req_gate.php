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

    // ----ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    }
    // ルートディレクトリを取得----

    // サイト共通の自作PHPファンクション集を呼び出し
    require_once ($root_dir_path . "/libs/commonlibs/common_php_functions.php");
    require_once ($root_dir_path . "/libs/commonlibs/common_php_classes.php");

    $strReqInitTime = getMircotime();

    if( ini_get('safe_mode')=='1' ){
        echo("PHP is now safemode.");
        exit();
    }

    $objMTS = new MessageTemplateStorage();

    // 注意：オンラインアクセスの場合ここでの作成が代表となるので重要。
    $arrayReqInfo = requestTypeAnalyze();
    if( $arrayReqInfo[0] == "web" ){
        require_once ($root_dir_path . "/libs/webcommonlibs/web_php_functions.php");
        if( empty( $scheme_n_authority ) ){
            // URLのスキーム＆オーソリティを取得
            $protocol = getRequestProtocol();
            $scheme_n_authority = getSchemeNAuthority();
        }
    }
    else if( $arrayReqInfo[0] == "backyard" ){
    }
    else{
        echo("Request type is unexpected.");
        exit();
    }

    // 特別なオーダーがない限り、DBへ接続する
    $tmpBoolDBConnect = true;
    if( isset($aryOrderToReqGate['DBConnect']) ){
        if( $aryOrderToReqGate['DBConnect'] == 'LATE' ){
            $tmpBoolDBConnect = false;
        }
    }
    if( $tmpBoolDBConnect === true ){
        require_once($root_dir_path . "/libs/commonlibs/common_db_connect.php");
    }
    unset($tmpBoolDBConnect);
?>