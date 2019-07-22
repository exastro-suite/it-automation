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

    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // パラメータを持たないURLは使用できないため、基本コンソールへリダイレクト
    $uri = $_SERVER['REQUEST_URI'];
    if($uri == "/"){
        $grp = $_GET['grp'];
        $url = '/default/mainmenu/01_browse.php?grp=' . $grp;
        header('Location: ' . $url, true, 301);
    }

    // browse系メインメニュー用ロジックパーツ01
    require_once ( $root_dir_path . "/libs/webindividuallibs/systems/mainmenu/web_parts_for_template_mainmenu_01_browse.php");

    // DBアクセスを伴う処理を開始
    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // browse系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");

    // メインメニュー固有ファンクションパーツ
    require_once ( $root_dir_path . "/libs/webindividuallibs/systems/mainmenu/web_php_panel_functions.php");
    
    $strMailTag = "";
    if( 0 < strlen($admin_addr) ){
        $strMailTag = $objMTS->getSomeMessage("ITAWDCH-MNU-1100004",$admin_addr);
    }
    
    $manualLink = "/default/menu/01_browse.php?no=2100000401 target = _blank";

    //各メニュリンク用
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_php_tag_print_functions.php");
    
print <<< EOD
    <!-------------------------------- メインメニュー -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td>{$objMTS->getSomeMessage("ITAWDCH-MNU-1100001")}</td>
            </tr>
        </table>
    </h2>
EOD;

    // ログインユーザごとの表示モードを取得
    $mode = default_mode_select();

    // クラシックモードであれば従来の画面を表示
    if( $mode == "classic"){

print <<< EOD
        <div style="margin-left:10px;margin-top:10px;">
            {$objMTS->getSomeMessage("ITAWDCH-MNU-1100003")}<br>
            {$strMailTag}<br>
        </div>
        <br>
        <br>
EOD;

        printReleasedMainMenuLinks(2, "classic");
    }
    // クラシックモード以外はパネルを表示
    else{

print <<< EOD
    <div style="margin-top: 30px;"></div>
    <ul id="sortable">    
EOD;

    printReleasedMainMenuLinks(0, "large_panel");

print <<< EOD
    </ul>
        <script>
            $(function(){ 
                $("#sortable").sortable({ 
                    handle: "i",
                    animation: true,
                    revert: 150,
                    update: function(e, ui) { 
                        var result = $("#sortable").sortable("toArray", { attribute: "value" }); 
                        panel_sort_update(result); 
                    }
                }); 
            }); 
       </script>
EOD;
    }

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
?>
