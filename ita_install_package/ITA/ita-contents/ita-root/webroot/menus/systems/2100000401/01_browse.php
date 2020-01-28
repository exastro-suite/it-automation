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
        $g['page_dir']  =$_GET['no'];
    }

    try{
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // browse系共通ロジックパーツ01
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_01.php");
        
        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_maintenance.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_statement.php");
    
    //----シンフォニー用
    $aryTmpVariant1 = array();
    $aryTmpSetting1 = array();
    
    $symphony_class_dir = "2100000307";
    require_once($g['root_dir_path'] . "/webconfs/systems/2100000307_loadTable.php");
    $objTable1 = loadTable($symphony_class_dir,$aryTmpVariant1,$aryTmpSetting1);
    
    $tmpRetArray = getFilterCommandArea($objTable1,$aryTmpVariant1,$aryTmpSetting1,"filter_table","Filter1Tbl","FilterConditionTableFormatter");
    $strHtmlFilter1Commnad = $tmpRetArray[1];
    //シンフォニー用----
    
    //----オペレーション用
    $aryTmpVariant2 = array();
    $aryTmpSetting2 = array();
    
    $op_list_dir = "2100000304";
    require_once($g['root_dir_path'] . "/webconfs/systems/2100000304_loadTable.php");
    $objTable2 = loadTable($op_list_dir,$aryTmpVariant2,$aryTmpSetting2);
    
    $tmpRetArray = getFilterCommandArea($objTable2,$aryTmpVariant2,$aryTmpSetting2,"filter_table","Filter2Tbl","FilterConditionTableFormatter");
    $strHtmlFilter2Commnad = $tmpRetArray[1];
    //オペレーション用----
    
    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_itabase_symphony_style_css=filemtime("{$g['root_dir_path']}/webroot/common/css/itabase_symphony_style.css");
    $timeStamp_00_javascript_js=filemtime("{$g['root_dir_path']}/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");
    $timeStamp_itabase_symphony_class_info_access_js=filemtime("{$g['root_dir_path']}/webroot/common/javascripts/itabase_symphony_class_info_access.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_info_access.js?{$timeStamp_itabase_symphony_class_info_access_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_symphony_style.css?{$timeStamp_itabase_symphony_style_css}">
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
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
    
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-900051");
    $strDLbutton = $g['objMTS']->getSomeMessage("ITABASEH-MNU-900001");

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
    <!-------------------------------- 絞込み(表示フィルタ[シンフォニー]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-205090")}</div></td>
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
            <div id="filter_alert_area" class="alert_area" style="display:none" ></div>
            <div id="filter_area" class="table_area">
            </div>
        </div>
        <div style="margin-left:10px">
{$strHtmlFilter1Commnad}
        </div>
    </div>
    <!-------------------------------- 絞込み(表示フィルタ[シンフォニー]) -------------------------------->
EOD;

	print
<<<EOD
    <!-------------------------------- 一覧[シンフォニー] -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td>
                    <div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206010")}</div>
                </td>
                <td>
                    <div id="Mix1_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Mix1_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="table_alert_area" class="alert_area" style="display:none" ></div>
            <div id="table_area" class="table_area" ></div>
        </div>
    </div>
    <!-------------------------------- 一覧[シンフォニー] -------------------------------->
EOD;

    print 
<<<EOD
    <!-------------------------------- 絞込み(表示フィルタ[オペレーション]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206020")}</div></td>
                <td>
                    <div id="Filter2_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Filter2_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="select_alert_area" class="alert_area" style="display:none" ></div>
            <div id="select_area" class="table_area" >
            </div>
        </div>
        <div style="margin-left:10px">
{$strHtmlFilter2Commnad}
        </div>
    </div>
    <!-------------------------------- 絞込み(表示フィルタ[オペレーション]) -------------------------------->
EOD;

    print 
<<<EOD
    <!-------------------------------- 一覧[オペレーション] -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206030")}</div></td>
                <td>
                    <div id="Mix2_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Mix2_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="register_alert_area" class="alert_area" style="display:none" ></div>
            <div id="register_area" class="table_area" ></div>
        </div>
    </div>
    <!-------------------------------- 一覧[オペレーション] -------------------------------->
    <div id="Button_Area" style="display:block" class="text">
      <div id="AlertMsg" style="color: red;"><br/></div>
      <input class="cmdButton disableAfterPush" type="submit" value="{$strDLbutton}" onclick="DataExport()">
    </div>
EOD;

    print 

    //  共通HTMLフッタパーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_footer.php");

