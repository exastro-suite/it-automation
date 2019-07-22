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

    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--
    require_once ( $root_dir_path . "/libs/webcommonlibs/table_control_agent/web_parts_for_template_01_browse.php");
    //-- サイト個別PHP要素、ここから--
    //-- サイト個別PHP要素、ここまで--

    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="pageType" style="display:none" class="text">{$pageType}</div>
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysWebRowConfirm" style="display:none" class="text">{$varWebRowConfirm}</div>
    <div id="sysWebRowLimit" style="display:none" class="text">{$varWebRowLimit}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="webStdTableWidth" style="display:none" class="text">{$intTableWidth}</div>
    <div id="webStdTableHeight" style="display:none" class="text">{$intTableHeight}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
{$strDeveloperArea}
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
    <!-------------------------------- 絞込み(表示フィルタ) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-30021")}</div></td>
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
<!--サイト個別html要素、ここから//-->

<!--サイト個別html要素、ここまで---->
            <div id="filter_alert_area" class="alert_area" style="display:none" ></div>
            <div id="filter_area" class="table_area">
            </div>
        </div>
        <div style="margin-left:10px">
{$strHtmlFilter1Commnad}
        </div>
    </div>
    <!-------------------------------- 絞込み(表示フィルタ) -------------------------------->
EOD;
	$listSectionMsg = ($privilege=="1")?$g['objMTS']->getSomeMessage("ITAWDCH-STD-30031"):$g['objMTS']->getSomeMessage("ITAWDCH-STD-30032");
	print
<<<EOD
    <!-------------------------------- 一覧/更新 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td>
                    <div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >{$listSectionMsg}</div>
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
    <!-------------------------------- 一覧/更新 -------------------------------->

EOD;
if($boolShowRegisterArea === true){
//----サイト個別html要素、ここから
    if($strHtmlFilter2Commnad != "" ){

        print 
<<<EOD
    <!-------------------------------- 登録フィルター -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-30051")}</div></td>
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
    <!-------------------------------- 登録フィルター -------------------------------->
EOD;

    }
//サイト個別html要素、ここまで----
    print 
<<<EOD
    <!-------------------------------- 登録 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-30051")}</div></td>
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
    <!-------------------------------- 登録 -------------------------------->
EOD;
}else{
    //----権限がないので何もしない
    //権限がないので何もしない----
}

    $allDumpMsg = ($privilege=="2")?$g['objMTS']->getSomeMessage("ITAWDCH-STD-30061"):$g['objMTS']->getSomeMessage("ITAWDCH-STD-30062");

    print 
<<<EOD
    <!-------------------------------- 全件ダウンロードとファイルアップロード編集 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('AllDumpMidashi','AllDumpNakami');" class="midashi_class" >{$allDumpMsg}</div></td>
                <td>
                    <div id="AllDumpMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('AllDumpMidashi','AllDumpNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
{$strHtmlFileEditCommnad}
    <!-------------------------------- 全件ダウンロードとファイルアップロード編集 -------------------------------->
EOD;

    print 
<<<EOD
<!-- サイト個別html要素、ここから//-->
    <!-------------------------------- ロール情報 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('RoleBrowseMidashi','RoleBrowseNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040091")}</div></td>
                <td>
                    <div id="RoleBrowseMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('RoleBrowseMidashi','RoleBrowseNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="RoleBrowseNakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <h3>{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040092")}</h3>
            <div id="RoleBrowse_header_area"></div>
            <h3>{$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1040093")}</h3>
            <div id="RoleBrowse_area"></div>
        </div>
    </div>
    <!-------------------------------- ロール情報 -------------------------------->
<!-- サイト個別html要素、ここまで//-->
EOD;
    print 
<<<EOD
    <!-------------------------------- 変更履歴 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('Journal1_Midashi','Journal1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-30071")}</div></td>
                <td>
                    <div id="Journal1_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Journal1_Midashi','Journal1_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Journal1_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            {$strHtmlJnlFilterCommnad}
            <div id="journal_alert_area" class="alert_area" style="display:none" ></div>
            <div id="journal_area" class="table_area" ></div>
        </div>
    </div>
    <!-------------------------------- 変更履歴 -------------------------------->
EOD;

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
