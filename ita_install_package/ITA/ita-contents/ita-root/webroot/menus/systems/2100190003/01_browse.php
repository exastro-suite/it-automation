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
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = $_GET['no'];
    }

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
    $timeStamp_itabase_orchestrator_drive_style_css=filemtime("$root_dir_path/webroot/common/css/itabase_orchestrator_drive_style.css");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");
    $timeStamp_itabase_orchestrator_drive_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_orchestrator_drive.js");
    $timeStamp_style_css = filemtime("$root_dir_path/webroot/menus/systems/2100190003/style.css");

print <<< EOD
    <script>const gLoginUserID = {$g['login_id']};</script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_orchestrator_drive.js?{$timeStamp_itabase_orchestrator_drive_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_orchestrator_drive_style.css?{$timeStamp_itabase_orchestrator_drive_style_css}">
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/menus/systems/2100190003/style.css?{$timeStamp_style_css}">
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    $privilege = "";
    //$strJscriptTemplateBody = "";
    //
    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");
    
    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----
    
    $strDeveloperArea = "";
    
    $varWebRowConfirm = "";
    $varWebRowLimit = "";
    $intTableWidth = 1058;
    $intTableHeight = 600;
    
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310200");

    require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/81_contrast_controle.php");
    //比較定義リスト表示用
    $arrayResult =  gethtmlContrastList(1);
    $ContrastList = $arrayResult[2];

    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysWebRowConfirm" style="display:none" class="text">{$varWebRowConfirm}</div>
    <div id="sysWebRowLimit" style="display:none" class="text">{$varWebRowLimit}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="webStdTableWidth" style="display:none" class="text">{$intTableWidth}</div>
    <div id="webStdTableHeight" style="display:none" class="text">{$intTableHeight}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
{$strDeveloperArea}
EOD;

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
   
    print 
<<< EOD
    <!-------------------------------- 比較実行 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310201")}</div></td>
                <td>
                    <div id="BookingMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="BookingNakami" style="display:block;" class="text">
        <div style="margin-left:10px">
            <br>
            <table border="0">
                <tr>
                    <td style="padding-right:2px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310202")}</td>
EOD;

    echo '<td> <select name="CONTRAST_ID">';
        echo $ContrastList;
    echo '</select> </td>';

    print 
<<< EOD
                    <td style="padding-right:10px;padding-left:10px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310203")}</td>
                    <td> <input id="bookdatetime" name="BASE_TIMESTAMP_0" size="16" maxlength="16" type="text" value="" > </td>
                    <td style="padding-right:10px;padding-left:10px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310204")}</td>
                    <td> <input id="bookdatetime2" name="BASE_TIMESTAMP_1" size="16" maxlength="16" type="text" value="" > </td>

                    <td style="padding-right:10px;padding-left:10px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310205")}</td>
                    <td> <input class="linkBtnInTbl" type="button" value=" {$g['objMTS']->getSomeMessage("ITABASEH-MNU-310206")} " onclick="printHostList()" ></td>
                    <td> <input ins-host-id="host_data" type="hidden" name="HOST_LIST[]" ></td>

                </tr>
                <tr><td></td></tr>
                <tr>
                    <td style="padding-right:2px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310225")}</td>
                    <td>
                        <label id="compare_radio" class="compare_label">
                            <input type="radio" class="compare_radio" value="1" name="OUTPUT_TYPE" for="compare_radio" checked="true">
                            <span class="compare_label_name">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310226")}</span>
                        </label>

                        <label id="compare_radio" class="compare_label">
                            <input type="radio" class="compare_radio" value="2" name="OUTPUT_TYPE" for="compare_radio" >
                            <span class="compare_label_name">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310227")}</span>
                        </label>

                    </td>
                </tr>
            </table>
                <br>
                <input type="button" name="submit" value="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310207")}" onclick="contrastResultHtml()" />
        </div>
    </div>
    <!-------------------------------- 比較実行 -------------------------------->
EOD;

    print 
<<< EOD
    <!-------------------------------- 比較結果 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310208")}</div></td>
                <td>
                    <div id="Filter1_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Filter1_Nakami" style="display:block;" class="text">
        <div style="margin-left:0px">
            <div id="table_alert_area" class="alert_area" style="display:none"></div>
            <div id="table_area" class="table_area">
              {$g['objMTS']->getSomeMessage("ITABASEH-MNU-310209")}
                </div>
            </div>
        </div>
        <div style="margin-left:10px">
        </div>
    </div>
    <!-------------------------------- 比較結果 -------------------------------->
EOD;
    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
