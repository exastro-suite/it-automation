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

    // ----■ログイン成功後に表示させたいメニューのＩＤが、リクエストのGETクエリーに含まれているかをチェックする。
    if( isset($_GET['no']) ){
        $req_menu_id = $_GET['no'];
    }

    $ASJTM_grp_id = "";
    if( isset($_GET['grp']) ){
        $ASJTM_grp_id = sprintf("%010d", $_GET['grp']);
    }

    if( !isset($req_menu_id) ){
        // ----含まれていない。

        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-18"));
        
        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,10910101);
        exit();

        //含まれていない。----
    }
    else if("" == $req_menu_id){

        if( isset($_GET['grp']) ){
            $ASJTM_id = "";
        }
        else{
            // ----■リクエストのGETクエリーに含まれていた、ログイン成功後に表示させたいメニューのＩＤが、数字又は数値形式の文字列ではなかった。

            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-19"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,10910102);
            exit();

            // リクエストのGETクエリーに含まれていた、ログイン成功後に表示させたいメニューのＩＤが、数字又は数値形式の文字列ではなかった。■----
        }
    }
    else{
        $ASJTM_id = sprintf("%010d", addslashes($req_menu_id));
    }

    if("" !== $ASJTM_id){
        $ASJTM_representative_file_name = "/default/menu/01_browse.php?no=" . $ASJTM_id;
    }
    else{
        $ASJTM_representative_file_name = "/default/mainmenu/01_browse.php?grp=" . $ASJTM_grp_id;
    }
?>