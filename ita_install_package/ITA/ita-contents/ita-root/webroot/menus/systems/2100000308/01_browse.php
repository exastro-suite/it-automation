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
        $g['page_dir']  =$_GET['no'];
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
        // コメントを解除
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    
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
    $timeStamp_itabase_symphony_style_css=filemtime("$root_dir_path/webroot/common/css/itabase_symphony_style.css");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");
    $timeStamp_itabase_symphony_class_info_access_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_symphony_class_info_access.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_info_access.js?{$timeStamp_itabase_symphony_class_info_access_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/itabase_symphony_style.css?{$timeStamp_itabase_symphony_style_css}">
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    $privilege = "";
    //$strJscriptTemplateBody = "";

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
    
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-205065");
    
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
    <!-------------------------------- スケジューリング -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >スケジューリング</div></td>-->
                <td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-205060")}</div></td>
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
            <!--RedMineチケット1031-->
            <!--
            予約日時を指定する場合は、日時フォーマット(YYYY/MM/DD HH:II)で入力して下さい。
            ブランクの場合は即時実行となります
            -->
            <!--RedMineチケット1031-->
            {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205070")}
            <br>
            <table border="0">
                <tr>
                    <!--<td style="padding-right:10px">予約日時</td>-->
                    <td style="padding-right:10px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-205080")}</td>
                    <td><input id="bookdatetime" type="text" maxlength="16"></td>
                </tr>
            </table>
        </div>
    </div>
    <!-------------------------------- スケジューリング -------------------------------->
EOD;

    print 
<<< EOD
    <!-------------------------------- 絞込み(表示フィルタ[シンフォニー]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >シンフォニー[フィルタ]</div></td>-->
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
                    <!--<div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >シンフォニー[一覧]</div>-->
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
                <!--<td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >オペレーション[フィルタ]</div></td>-->
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
                <!--<td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >オペレーション[一覧]</div></td>-->
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
EOD;

    print 
<<< EOD
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('symphonyExecute_Midashi','symphonyExecute_Nakami');" class="midashi_class" >シンフォニー実行</div></td>-->
                <td><div onClick=location.href="javascript:show('symphonyExecute_Midashi','symphonyExecute_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206040")}</div></td>
                <td>
                    <div id="symphonyExecute_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('symphonyExecute_Midashi','symphonyExecute_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="symphonyExecute_Nakami" style="display:block" class="text">
        <div id="symphonyInfoShowContainer">
            <div class="leftMainArea-TypeA">
                <div id="symphony_header">
                    <!-- ----このDIVの範囲内だけをjQueryで送る -->
                    <div class="sub1">
                        <div class="heightAndWidthFixed01">
                            <!--<label for="symphony_id">SymphonyクラスID</label>　<span id="print_symphony_id"></span><br>-->
                            <label for="symphony_id">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206050")}</label>　<span id="print_symphony_id"></span>
                        </div>
                        <div class="heightAndWidthFixed01">
                            <!--<label for="symphony_name">Symphonyクラス名称</label>　<span id="print_symphony_name"></span>-->
                            <label for="symphony_name">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206060")}</label>　<span id="print_symphony_name"></span>
                        </div>
                    </div>
                    <div class="sub2">
                        <span id="print_shyphony_tips"></span>
                    </div>
                    <div class="sub3">
                        <!--<label>説明</label>-->
                        <label>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206070")}</label>
                    </div>
                    <div style="display:none">
                        <span id="print_sym_hidden_values"></span>
                    </div>
                    <!-- このDIVの範囲内だけをjQueryで送る---- -->
                </div>
                <article class="draggable_area">
                    <div id="symphony_area">
                        <div id="symphony_message">
                        <!--start-->
                            <div id="action_alert_area" class="alert_area" style="display:none" ></div>
                        </div>
                        <div class="start">
                            {$g['objMTS']->getSomeMessage("ITABASEH-MNU-206080")}
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
                <div id="operation_info_area">
                    <div class="heightAndWidthFixed01">
                        <!--<label for="operation_id">オペレーションID</label>　<span id="print_operation_id"></span><br>-->
                        <label for="operation_id">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206090")}</label>　<span id="print_operation_id"></span>
                    </div>
                    <div class="heightAndWidthFixed01">
                        <!--<label for="operation_name">オペレーション名</label>　<span id="print_operation_name"></span>-->
                        <label for="operation_name">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-207010")}</label>　<span id="print_operation_name"></span>
                    </div>
                    <div style="display:none">
                        <span id="print_operation_no_uapk"></span>
                        <span id="print_ope_hidden_values"></span>
                    </div>
                </div>
                <div id="preserve_time_setting_wrapper">
                    <div id="preserve_time_setting_area">
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
