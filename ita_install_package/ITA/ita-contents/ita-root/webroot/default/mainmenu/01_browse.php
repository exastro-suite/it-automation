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
        $url = '/default/mainmenu/01_browse.php';
        header('Location: ' . $url, true, 301);
        exit;
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
    
    //----JS-MSGテンプレートのリスト作成
    $aryImportFilePath = array();
    $aryImportFilePath[] = $g['objMTS']->getTemplateFilePath("ITAWDCC","STD","_js");
    $strTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$g['objMTS']);
    
    //JS-MSGテンプレートのリスト作成----
    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="messageTemplate" style="display:none" class="text">{$strTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
EOD;

    // ダッシュボード関連ファイルのタイムスタンプを取得
    $timeStamp_dashboard_css = filemtime("$root_dir_path/webroot/default/mainmenu/dashboard.css");
    $timeStamp_dashboard_js = filemtime("$root_dir_path/webroot/default/mainmenu/dashboard.js");
    
    // ダッシュボード関連ファイルの読み込み
print <<< EOD
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/default/mainmenu/dashboard.css?{$timeStamp_dashboard_css}">
    <script type="text/javascript" src="{$scheme_n_authority}/default/mainmenu/dashboard.js?{$timeStamp_dashboard_js}"></script>
EOD;

    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");

    // メインメニュー固有ファンクションパーツ
    require_once ( $root_dir_path . "/libs/webindividuallibs/systems/mainmenu/web_php_panel_functions.php");
    
    $strMailTag = "";
    if( 0 < strlen($admin_addr) ){
        $strMailTag = $objMTS->getSomeMessage("ITAWDCH-MNU-1100004",$admin_addr);
    }
    
    //各メニュリンク用
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_php_tag_print_functions.php");
    
print <<< EOD
<!-------------------------------- ダッシュボード -------------------------------->
<div id="dashboard" data-mode="">

  <style id="dashboard-grid-style"></style>

  <div class="dashboard-header">
    <h2 class="dashboard-title">DASHBOARD</h2>
    <div class="dashboard-menu">
      <div class="dashboard-view-menu">
        <ul class="dashboard-menu-list">
          <li class="dashboard-menu-item"><button class="dashboard-menu-button positive" data-button="edit"></button></li>
        </ul>
      </div>
      <div class="dashboard-edit-menu">
        <ul class="dashboard-menu-list">
          <li class="dashboard-menu-item"><button class="dashboard-menu-button positive" data-button="add"></button></li>
        </ul>
        <ul class="dashboard-menu-list">
          <li class="dashboard-menu-item"><button class="dashboard-menu-button positive" data-button="regist"></button></li>
          <li class="dashboard-menu-item"><button class="dashboard-menu-button negative" data-button="reset"></button></li>
          <li class="dashboard-menu-item"><button class="dashboard-menu-button negative" data-button="cancel"></button></li>
        </ul>
      </div>
    </div>
  </div><!-- /#dashboard-header -->

  <div class="dashboard-body">
    <div class="dashboard-loading"></div>
  </div><!-- /#dashboard-body -->

</div>
EOD;
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");
?>
