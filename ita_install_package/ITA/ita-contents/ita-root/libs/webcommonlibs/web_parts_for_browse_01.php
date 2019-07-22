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

    // browse系、access系、reg_n_up系の共有パーツを読み込み
    require_once ($root_dir_path . "/libs/webcommonlibs/web_parts_for_common.php");

    // メニュー用の配列を準備
    $menu_name_array = array();
    $menu_id_array = array();

    // メニュー生成
    if( $login_status_flag == 1 ){
        $tmpStrUserName = $username;
    }
    else{
        $tmpStrUserName = null;
    }
    $tmpAryRetBody = getFilenameAndMenuNameByMenuGroupID($ACRCM_group_id,$login_status_flag,$tmpStrUserName,$objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        $tmpErrMsgBody = $tmpAryRetBody[3];

        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-25",$tmpErrMsgBody));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(400,11110101);
    }
    if(count($tmpAryRetBody[0]['MenuNames']) < 1){
        // アクセスログ出力(不正アクセス操作)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-47"));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(403,11210201);
    }

    $menu_name_array = $tmpAryRetBody[0]['MenuNames'];
    $menu_id_array = $tmpAryRetBody[0]['MenuIds'];
    unset($tmpStrUserName);
    unset($tmpAryRetBody);

    // メニュー名カウンタを初期化
    $menu_num = 0;

    $menus ="";

    // メインメニュー追加
    $menus .= "<li id=\"MENU00\">";
    $menus .= "<a href=\"/default/mainmenu/01_browse.php?grp=" . $ACRCM_group_id . "\">" . trim($objMTS->getSomeMessage("ITAWDCH-MNU-1100001")) . "</a></li>\n";
    foreach($menu_name_array as $menu_name){
        $menu_num_zeropad = sprintf('%02d', $menu_num);
        if(array_key_exists('no', $_GET) && $_GET['no'] == sprintf("%010d", $menu_id_array[$menu_num])){
            $menus .= "<li id=\"MENU" . $menu_num_zeropad . "\" class=\"menu-on\">";
        }
        else{
            $menus .= "<li id=\"MENU" . $menu_num_zeropad . "\">";
        }
        $menus .= "<a href=\"/default/menu/01_browse.php?no=" . sprintf("%010d", $menu_id_array[$menu_num]) . "\">" . $menu_name . "</a></li>\n";
        $menu_num++;
    }
?>
