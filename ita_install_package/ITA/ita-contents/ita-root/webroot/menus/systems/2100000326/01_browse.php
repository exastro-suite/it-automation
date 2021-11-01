<?php
//   Copyright 2021 NEC Corporation
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

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
//    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php");




    global $g;
    // ルートディレクトリを取得
    $tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }

    $param = explode ( "?" , $_SERVER["REQUEST_URI"] , 2 );
    if(count($param) == 2){
        $url_add_param = "&" . $param[1];
    }
    else{
        $url_add_param = "";
    }

    // DBアクセスを伴う処理を開始
    try{
        //----ここから01_系から06_系全て共通
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
        //ここまで01_系から06_系全て共通----

        // browse系共通ロジックパーツ01
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_01.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }

    $strCmdWordAreaOpen = $g['objMTS']->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $g['objMTS']->getSomeMessage("ITAWDCH-STD-252");

    // 共通HTMLステートメントパーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_statement.php");

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    //----JS-MSGテンプレートのリスト作成
    $aryImportFilePath = array();
    $aryImportFilePath[] = $g['objMTS']->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $g['objMTS']->getTemplateFilePath("ITACREPAR","STD","_js");

    $strTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$g['objMTS']);

    // Editorに必要なファイルのタイムスタンプを取得
    $timeStamp_editor_common_css = filemtime("$root_dir_path/webroot/common/css/editor_common.css");
    $timeStamp_er_css = filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/er.css");
    $timeStamp_editor_common_js = filemtime("$root_dir_path/webroot/common/javascripts/editor_common.js");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");

    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <script>const gLoginUserID = {$g['login_id']};</script>
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/editor_common.css?{$timeStamp_editor_common_css}">
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/er.css?{$timeStamp_er_css}">
    
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/editor_common.js?{$timeStamp_editor_common_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
EOD;

    //-- サイト個別PHP要素、ここから--
    print
<<< EOD

<div id="editor" data-relation="on" class="load-wait">
  <div class="editor-inner">



    <div id="editor-header">
      <div class="editor-header-menu">
        <div class="editor-header-main-menu">
          <ul class="editor-menu-list">
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="er-print">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309050")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="er-menu-group">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309051")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="er-relation">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309052")}<span class="check-mark"></span></button></li>
          </ul>
        </div>
        <div class="editor-header-sub-menu">
          <ul class="editor-menu-list">
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="view-all">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309013")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="view-reset">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309014")}</button></li>
          </ul>
        </div>
      </div>
    </div><!-- /#editor-header -->



    <div id="editor-main">

      <div id="editor-body" class="editor-row-resize">

        <div id="editor-edit" class="editor-block">
          <div class="editor-block-inner">
          
            <div id="canvas-visible-area" class="">
              <div id="canvas">
                <div id="art-board">
                </div><!-- / #art-board -->
              </div><!-- / #canvas -->
              
              <div id="editor-display">
                <div id="editor-explanation">
                  <dl class="explanation-list">
                    <dt class="explanation-term"><span class="mouse-icon mouse-wheel"></span>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309053")}</dt>
                    <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309054")}</dd>
                    <dt class="explanation-term"><span class="mouse-icon mouse-right"></span>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309055")}</dt>
                    <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309056")}</dd>
                  </dl>
                </div>
              </div><!-- / #editor-display -->
              
            </div><!-- / #canvas-visible-area -->
            
          </div>
        </div><!-- /#editor-edit -->
      </div><!-- /#editor-body -->
    </div><!-- /#editor-main -->



    <div id="editor-footer">
      <div class="editor-footer-menu">
      </div>
    </div><!-- /#editor-footer -->



  </div>
</div><!-- /#editor -->

EOD;
    //-- サイト個別PHP要素、ここまで--

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
