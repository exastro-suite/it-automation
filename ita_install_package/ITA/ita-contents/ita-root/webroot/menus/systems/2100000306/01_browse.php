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
    //  【処理概要】
    //    ・Symphonyクラスを定義するページの、各種動的機能を呼び出す
    //
    //////////////////////////////////////////////////////////////////////

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }
    $privilege = "";

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
    
    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_itabase_symphony_style_css=filemtime("$root_dir_path/webroot/common/css/itabase_symphony_style.css");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");
    $timeStamp_itabase_symphony_class_info_access_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_symphony_class_info_access.js");
    $timeStamp_itabase_symphony_class_edit_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_symphony_class_edit.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_info_access.js?{$timeStamp_itabase_symphony_class_info_access_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_edit.js?{$timeStamp_itabase_symphony_class_edit_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_symphony_style.css?{$timeStamp_itabase_symphony_style_css}">
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    if("1" === $g['menu_autofilter']){
        $checkBoxChecked="checked=\"checked\"";
    }
    else{
        $checkBoxChecked="";
    }
    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");
    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----
    
    $strDeveloperArea = "";
    
    //$strPageInfo = "説明";
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-204040");

    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
{$strDeveloperArea}
EOD;
/*
    print 
<<< EOD
    <!-------------------------------- 説明 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-30011")}</div></td>
                <td>
                    <div id="SetsumeiMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="SetsumeiNakami" style="display:block" class="text">
        <div style="margin-left:10px">
{$strPageInfo}
        </div>
    </div>
    <!-------------------------------- 説明 -------------------------------->
EOD;
*/
    print 
<<< EOD
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('symphonyEdit_Midashi','symphonyEdit_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-204050")}<!-- シンフォニー編集 --></div></td>
                <td>
                    <div id="symphonyEdit_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('symphonyEdit_Midashi','symphonyEdit_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="symphonyEdit_Nakami" style="display:block" class="text">
        <div id="symphonyInfoShowContainer">
            <div class="leftMainArea-TypeA">
                <div id="symphony_header">
                    <!-- ----このDIVの範囲内だけをjQueryで送る -->
                    <div class="sub1">
                        <div class="heightAndWidthFixed01">
                            <label for="symphony_id">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-204060")}<!--SymphonyクラスID--></label>　<span id="print_symphony_id"></span>
                        </div>
                        <div class="heightAndWidthFixed01">
                            <label for="symphony_name">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-204070")}<!--Symphonyクラス名称--></label>　<span id="print_symphony_name"></span>
                        </div>
                        <div style="display:none"><span id="print_symphony_lt4u"></span></div>
                    </div>
                    <div class="sub2">
                        <span id="print_shyphony_tips"></span>
                    </div>
                    <div class="sub3">
                        <label>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-204080")}<!--説明--></label>
                    </div>
                    <div style="display:none">
                        <span id="print_sym_hidden_values"></span>
                    </div>
                    <!-- このDIVの範囲内だけをjQueryで送る---- -->
                </div>
                <article class="draggable_area">
                    <div id="symphony_area">
                        <div id="symphony_message">
                            <div id="action_alert_area" class="alert_area" style="display:none" ></div>
                        </div>
                        <div class="start">
                            {$g['objMTS']->getSomeMessage("ITABASEH-MNU-204090")}<!--start-->
                        </div>
                        <div class="sortable_area"></div>
                        <div class="end">
                            end
                        </div>
                    </div>
                    <div id="symphony_footer">
                    </div>
                </article>
            </div>
            <div class="rightSideBar-TypeA">
                <div id="pattern_filter_area">
                    <!-- ----このDIVの範囲内だけをjQueryで送る -->
                    {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205010")}<!--表示フィルタ-->
                    <p>
                        {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205020")}<!--内容-->
                        <!-- <input id="filter_value" onKeypress="filterAutoSearchCheck()" onKeydown="filterAutoSearchCheck()" onKeyup="filterAutoSearchCheck()"> //-->
                        <input id="filter_value" onKeydown="filterAutoSearchCheck('onKeydown', event.keyCode)" >
                    </p>
                    <p>
                        <input id="filter_auto_mode" type="checkbox" {$checkBoxChecked}>
                        {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205030")}<!--オートフィルタ-->
                    </p>
                    <button id="filter_execute" onClick="printMatchedPatternList(true)">
                        {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205040")}<!--フィルタ-->
                    </button>
                    <button id="filter_clear" onClick="filterConditionClear()">
                        {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205050")}<!--フィルタをクリア-->
                    </button>
                    <!-- このDIVの範囲内だけをjQueryで送る---- -->
                </div>
                <div id="material_area_wrapper">
                    <div id="material_area">
                    </div>
                </div>
            </div>
            <div class="floatEracerBoth"></div>
        </div>
    </div>
EOD;

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
