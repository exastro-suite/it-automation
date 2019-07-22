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
        
        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)

    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    
    //----作業パターン用
    $aryTmpVariant1 = array();
    $aryTmpSetting1 = array();
    
    $ansible_legacy_pattern_dir = "2100070002";
    require_once($g['root_dir_path'] . "/webconfs/systems/{$ansible_legacy_pattern_dir}_loadTable.php");
    $objTable1 = loadTable($ansible_legacy_pattern_dir,$aryTmpVariant1,$aryTmpSetting1);
    
    $tmpRetArray = getFilterCommandArea($objTable1,$aryTmpVariant1,$aryTmpSetting1,"filter_table","Filter1Tbl","FilterConditionTableFormatter");
    $strHtmlFilter1Commnad = $tmpRetArray[1];
    //作業パターン用----
    
    //----オペレーション用
    $aryTmpVariant2 = array();
    $aryTmpSetting2 = array();
    
    $op_dir = "2100000304";
    require_once($g['root_dir_path'] . "/webconfs/systems/{$op_dir}_loadTable.php");
    $objTable2 = loadTable($op_dir,$aryTmpVariant2,$aryTmpSetting2);
    
    $tmpRetArray = getFilterCommandArea($objTable2,$aryTmpVariant2,$aryTmpSetting2,"filter_table","Filter2Tbl","FilterConditionTableFormatter");
    $strHtmlFilter2Commnad = $tmpRetArray[1];
    //オペレーション用----
    
    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    

    $timeStamp_itabase_orchestrator_drive_style_css=filemtime("$root_dir_path/webroot/common/css/itabase_orchestrator_drive_style.css");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");
    $timeStamp_itabase_orchestrator_drive_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_orchestrator_drive.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_orchestrator_drive.js?{$timeStamp_itabase_orchestrator_drive_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_orchestrator_drive_style.css?{$timeStamp_itabase_orchestrator_drive_style_css}">
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    $privilege = "";
    
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
    
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-STD-10908003");

    
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
                <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908005")}</div></td>
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
    <!-------------------------------- スケジューリング -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >スケジューリング</div></td>-->
                <td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908010")}</div></td>
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
            <!--
            予約日時を指定する場合は、日時フォーマット(YYYY/MM/DD HH:II)で入力して下さい。
            ブランクの場合は即時実行となります
            -->
            {$g['objMTS']->getSomeMessage("ITABASEH-STD-10908015")}
            <br>
            <table border="0">
                <tr>
                    <!--<td style="padding-right:10px">予約日時</td>-->
                    <td style="padding-right:10px">{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908020")}</td>
                    <td><input id="bookdatetime" type="text" maxlength="16"></td>
                </tr>
            </table>
        </div>
    </div>
    <!-------------------------------- スケジューリング -------------------------------->
EOD;

    print 
<<< EOD
    <!-------------------------------- 絞込み(表示フィルタ[作業パターン]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >作業パターン[フィルタ]</div></td>-->
                <td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908025")}</div></td>
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
    <!-------------------------------- 絞込み(表示フィルタ[作業パターン]) -------------------------------->
EOD;

	print
<<<EOD
    <!-------------------------------- 一覧[作業パターン] -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td>
                    <!--<div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >作業パターン[一覧]</div>-->
                    <div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908030")}</div>
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
    <!-------------------------------- 一覧[作業パターン] -------------------------------->
EOD;

    print 
<<<EOD
    <!-------------------------------- 絞込み(表示フィルタ[オペレーション]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >オペレーション[フィルタ]</div></td>-->
                <td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908035")}</div></td>
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
                <!--<td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >オペレーション[一覧]</div></td>-->
                <td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908040")}</div></td>
                
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
EOD;

    print 
<<< EOD
    <div id="orchestratorInfoShowContainer">
        <div id="orchestratorInfoHeader">
            <div class="leftMainArea-TypeA">
                <div id="pattern_info_area">
                    <div class="sub1">
                        <div class="heightAndWidthFixed01">
                             <!--<label for="print_pattern_id">作業パターンID</label>　<span id="print_pattern_id"></span><br>-->
                             <label for="print_pattern_id">{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908045")}</label>　<span id="print_pattern_id"></span>
                        </div>
                        <div class="heightAndWidthFixed01">
                             <!--<label for="print_pattern_name">作業パターン名</label>　<span id="print_pattern_name"></span>-->
                             <label for="print_pattern_name">{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908050")}</label>　<span id="print_pattern_name"></span>
                        </div>
                    </div>
                    <div style="display:none">
                        <span id="print_pattern_hidden_values"></span>
                    </div>
                </div>

            </div>
            <div class="rightSideBar-TypeA">
                <div id="operation_info_area">
                    <div class="heightAndWidthFixed01">
                         <!--<label for="operation_id">オペレーションID</label>　<span id="print_operation_id"></span><br>-->
                         <label for="operation_id">{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908055")}</label>　<span id="print_operation_id"></span>
                    </div>
                    <div class="heightAndWidthFixed01">
                         <!--<label for="operation_name">オペレーション名</label>　<span id="print_operation_name"></span>-->
                         <label for="operation_name">{$g['objMTS']->getSomeMessage("ITABASEH-STD-10908060")}</label>　<span id="print_operation_name"></span>
                    </div>
                    <div style="display:none">
                        <span id="print_operation_no_uapk"></span>
                        <span id="print_ope_hidden_values"></span>
                    </div>
                </div>
            </div>
            <div class="floatEracerBoth"></div>
        </div>
        <div id="orchestrator_message">
            <div id="action_alert_area" class="alert_area" style="display:none" ></div>
        </div>
        <br>
        <br>
        <div id="orchestratorInfoFooter">
        </div>
    </div>
EOD;

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
