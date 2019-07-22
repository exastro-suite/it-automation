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

    // リファラ取得(リダイレクト判定のため)
    if(isset($_SERVER["HTTP_REFERER"])){
        $host = $_SERVER['HTTP_REFERER'];
    }else{
        $host = "";
    }

    $tmp_found_flag = 0;
    if(false !== stristr($host, $ACRCM_id)){
        $tmp_found_flag = 1;
    }

    // 同メニューIDのPHPファイルからのリダイレクトでない場合はNGとする----
    if ( $tmp_found_flag == 0 ){
        // アクセスログ出力(リダイレクト判定NG)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-48"));
        
        // 不正操作によるアクセス警告画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,11310201);
        exit();
    }
    unset($tmp_found_flag);
    
    // browse系、access系、reg_n_up系の共有パーツを読み込み
    require_once ($root_dir_path . "/libs/webcommonlibs/web_parts_for_common.php");
?>
