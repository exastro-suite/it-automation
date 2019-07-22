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
    
    $symphony_instance_dir = "2100000310";
    require_once($g['root_dir_path'] . "/webconfs/systems/2100000310_loadTable.php");
    $objTable1 = loadTable($symphony_instance_dir,$aryTmpVariant1,$aryTmpSetting1);
    
    $tmpRetArray = getFilterCommandArea($objTable1,$aryTmpVariant1,$aryTmpSetting1,"filter_table","Filter1Tbl","FilterConditionTableFormatter");
    $strHtmlFilter1Commnad = $tmpRetArray[1];
    //シンフォニー用----
    
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
    
    $interval = 3000;  //エラー等の場合の初期値

    $strQuery = "SELECT SYMPHONY_REFRESH_INTERVAL FROM C_SYMPHONY_IF_INFO WHERE DISUSE_FLAG = '0'";
    $aryForBind = array();
    $strFxName  = "";
    $tmpStrInterVal = "";
    $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
    if( $aryRetBody[0] === true ){
        $objQuery = $aryRetBody[1];
        if($objQuery->effectedRowCount() == 0) {
            web_log($objMTS->getSomeMessage("ITABASEH-ERR-900067"));
        } else {
            if($objQuery->effectedRowCount() == 1) {
                $row = $objQuery->resultFetch();
                $tmpStrInterVal = $row['SYMPHONY_REFRESH_INTERVAL'];
            } else {
                web_log($objMTS->getSomeMessage("ITABASEH-ERR-900068"));
            }
        }
        unset($objQuery);
    }else{
        web_log($objMTS->getSomeMessage("ITABASEH-ERR-1990009",array("C_SYMPHONY_IF_INFO")));
    }
    if( 0 < strlen($tmpStrInterVal) ){
        if( is_numeric($tmpStrInterVal) === true ){
            $tmpIntInterVal = intval($tmpStrInterVal);
            if( $tmpIntInterVal <= 0 ){
                //----[WARNING: SETTING SYMPHONY INSTANCE MONITOR INTERVAL IS LESS THAN OVER EQUAL TO 0.]
                web_log($objMTS->getSomeMessage("ITABASEH-ERR-1030010"));
            }
            else{
                $interval = $tmpIntInterVal;
            }
        }
        else{
            //----[WARNING: SETTING SYMPHONY INSTANCE MONITOR INTERVAL IS NOT NUMERIC.]
            web_log($objMTS->getSomeMessage("ITABASEH-ERR-1030030"));
        }
    }
    unset($tmpStrInterVal);
    unset($tmpIntInterVal);
    
    //$strPageInfo = "説明";
    $strPageInfo = "";
    
    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="intervalOfDisp" style="display:none" class="text">{$interval}</div>
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
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('symphonyMonitor_Midashi','symphonyMonitor_Nakami');" class="midashi_class" >{$objMTS->getSomeMessage("ITABASEH-MNU-207020")}</div></td>
                <td>
                    <div id="symphonyMonitor_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('symphonyMonitor_Midashi','symphonyMonitor_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="symphonyMonitor_Nakami" style="display:block" class="text">
        <div id="symphonyInfoShowContainer">
            <div class="leftMainArea-TypeA">
                <div id="symphony_header">
                    <!-- ----このDIVの範囲内だけをjQueryで送る -->
                    <div class="sub1">
                        <div class="heightAndWidthFixed01">
                            <label for="symphony_id">{$objMTS->getSomeMessage("ITABASEH-MNU-207030")}</label>　<span id="print_symphony_id"></span>
                        </div>
                        <div class="heightAndWidthFixed01">
                            <label for="symphony_name">{$objMTS->getSomeMessage("ITABASEH-MNU-207040")}</label>　<span id="print_symphony_name"></span>
                        </div>
                    </div>
                    <div class="sub2">
                        <span id="print_shyphony_tips"></span>
                    </div>
                    <div class="sub3">
                        <label>{$objMTS->getSomeMessage("ITABASEH-MNU-207050")}</label>
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
                        <div id="startMark" class="start">
                            <!--start-->
                            {$objMTS->getSomeMessage("ITABASEH-MNU-207060")}
                        </div>
                        <div class="sortable_area"></div>
                        <div id="endMark" class="end">
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
                        <label for="operation_id">{$objMTS->getSomeMessage("ITABASEH-MNU-207070")}</label>　<span id="print_operation_id"></span>
                    </div>
                    <div class="heightAndWidthFixed01">
                        <label for="operation_name">{$objMTS->getSomeMessage("ITABASEH-MNU-207080")}</label>　<span id="print_operation_name"></span>
                    </div>
                    <div style="display:none">
                        <span id="print_operation_no_uapk"></span>
                        <span id="print_ope_hidden_values"></span>
                    </div>
                </div>
                <div id="instance_status_wrapper">
                    <label for="instance_status_area">{$objMTS->getSomeMessage("ITABASEH-MNU-207090")}</label>　<span id="instance_status_area"></span><br>
                    <label for="execution_status_area">{$objMTS->getSomeMessage("ITABASEH-MNU-201110")}</label>　<span id="execution_status_area"></span><br>
                    <label for="book_time_area">{$objMTS->getSomeMessage("ITABASEH-MNU-208010")}</label>　<span id="book_time_area"></span><br>
                    <label for="scram_exe_flag_area">{$objMTS->getSomeMessage("ITABASEH-MNU-208020")}</label>　<span id="scram_exe_flag_area"></span>
                </div>
            </div>
            <div class="floatEracerBoth"></div>
        </div>
    </div>
EOD;

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
