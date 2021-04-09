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

    global $g;
    // ルートディレクトリを取得
    $tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = htmlspecialchars($_GET['no'], ENT_QUOTES, "UTF-8");
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

    //----デフォルトのロードテーブル関数をコレクション
    $systemFile = "{$g['root_dir_path']}/webconfs/systems/{$g['page_dir']}_loadTable.php";
    $sheetFile = "{$g['root_dir_path']}/webconfs/sheets/{$g['page_dir']}_loadTable.php";
    $userFile = "{$g['root_dir_path']}/webconfs/users/{$g['page_dir']}_loadTable.php";
    $existLoadTable = false;
    if(file_exists($systemFile)){
        require_once($systemFile);
        $existLoadTable = true;
    }
    else if(file_exists($sheetFile)){
        require_once($sheetFile);
        $existLoadTable = true;
    }
    else if(file_exists($userFile)){
        require_once($userFile);
        $existLoadTable = true;
    }
    $retArray = array();
    if(true === $existLoadTable){
        $retArray = getTCAConfig();
    }

    $objDefaultTable = (array_key_exists('objTable', $retArray)) ? $retArray['objTable'] : null;
    $privilege = (array_key_exists('objTable', $retArray)) ? $retArray['privilege'] : null;
    $pageType = (array_key_exists('objTable', $retArray)) ? $retArray['pageType'] : null;
    $strPageInfo = (array_key_exists('objTable', $retArray)) ? $retArray['PageInfoArea'] : null;
    $strDeveloperArea = (array_key_exists('objTable', $retArray)) ? $retArray['DeveloperArea'] : null;
    $strHtmlFilter1Commnad = (array_key_exists('objTable', $retArray)) ? $retArray['FilterCmdArea'] : null;
    $strHtmlFilter2Commnad = (array_key_exists('objTable', $retArray)) ? $retArray['RegisterFilterArea'] : null;
    $boolShowRegisterArea = (array_key_exists('objTable', $retArray)) ? $retArray['RegisterAreaShow'] : null;
    $strHtmlFileEditCommnad = (array_key_exists('objTable', $retArray)) ? $retArray['QMFileAreaCmd'] : null;
    $strTemplateBody = (array_key_exists('objTable', $retArray)) ? $retArray['JscriptTmpl'] : null;
    $strHtmlJnlFilterCommnad = (array_key_exists('objTable', $retArray)) ? $retArray['JnlSearchFilterCmdArea'] : null;

    $varWebRowLimit = (array_key_exists('objTable', $retArray)) ? $retArray['WebPrintRowLimit'] : null;
    $varWebRowConfirm = (array_key_exists('objTable', $retArray)) ? $retArray['WebPrintRowConfirm'] : null;

    $intTableWidth = (array_key_exists('objTable', $retArray)) ? $retArray['WebStdTableWidth'] : null;
    $intTableHeight = (array_key_exists('objTable', $retArray)) ? $retArray['WebStdTableHeight'] : null;

    $strCmdWordAreaOpen = $g['objMTS']->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $g['objMTS']->getSomeMessage("ITAWDCH-STD-252");

    // 共通HTMLステートメントパーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_statement.php");

    $jsSystemFile = "/menus/systems/{$g['page_dir']}/00_javascript.js";
    $jsSheetFile = "/menus/sheets/{$g['page_dir']}/00_javascript.js";
    $jsUserFile = "/menus/users/{$g['page_dir']}/00_javascript.js";
    if(file_exists("{$g['root_dir_path']}/webroot" . $jsSystemFile)){
        $jsFile = "{$g['scheme_n_authority']}" . $jsSystemFile;
        $jsFile_Absolute_path = "{$g['root_dir_path']}/webroot" . $jsSystemFile;
    }
    else if(file_exists("{$g['root_dir_path']}/webroot" . $jsSheetFile)){
        $jsFile = "{$g['scheme_n_authority']}" . $jsSheetFile;
        $jsFile_Absolute_path = "{$g['root_dir_path']}/webroot" . $jsSheetFile;
    }
    else if(file_exists("{$g['root_dir_path']}/webroot" . $jsUserFile)){
        $jsFile = "{$g['scheme_n_authority']}" . $jsUserFile;
        $jsFile_Absolute_path = "{$g['root_dir_path']}/webroot" . $jsUserFile;
    }
    else{
        $jsFile = "{$g['scheme_n_authority']}/default/menu/00_javascript.js";
        $jsFile_Absolute_path = "{$g['root_dir_path']}/webroot/default/menu/00_javascript.js";
    }

    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_00_javascript_js = filemtime("$jsFile_Absolute_path");

    print 
<<< EOD
    <script>const gLoginUserID = {$g['login_id']};</script>    
    <script type="text/javascript" src="/default/menu/02_access.php?client=all$url_add_param"></script>
    <script type="text/javascript" src="/default/menu/02_access.php?stub=all$url_add_param"></script>
    <script type="text/javascript" src="{$jsFile}?$timeStamp_00_javascript_js"></script>
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
?>
