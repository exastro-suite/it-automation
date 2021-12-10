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

    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");
    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----

    $strTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);


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

    print 
<<<EOD
    <!-------------------------------- モーダルウィンドウ -------------------------------->

<style>
/* jQuery UI Dialog */
.ui-widget-overlay {
    background: rgba( 0,0,0,.5 );
    opacity: 1;
}
.ui-dialog {
    padding: 0;
    background: #444;
    border: none;
    border-radius: 0;
    box-shadow: 0 0 32px rgba( 0,0,0,.75 );
    color: #EEE;
}
.ui-dialog .input_required {
    display: inline-block;
    padding-right: 0.25em;
    color: #EE0;
}
.ui-dialog *,
.ui-dialog *::before,
.ui-dialog *::after {
    box-sizing: border-box;
}
.ui-dialog .ui-dialog-titlebar {
    overflow: hidden;
    position: relative;
    height: 32px;
    padding: 0;
    background: linear-gradient( #4A4A4A, #444 );
    border: none;
    border-radius: 0;
}
.ui-dialog .ui-dialog-title {
    position: absolute;
    left: 0; top: 0;
    z-index: 2;
    width: auto; height: 32px;
    max-width: calc( 100% - 64px );
    margin: 0; padding: 0 20px 0 16px;
    background: linear-gradient( #4A4A4A, #444 );
    line-height: 32px;
}
.ui-dialog .ui-dialog-titlebar::before {
    content: '';
    display: block;
    position: absolute;
    left: 8px; top: 6px;
    z-index: 1;
    width: calc( 100% - 16px );
    height: 24px;
    background: linear-gradient( #222 1px, #555 1px, #555 2px , transparent 2px );
    background-size: 6px 6px;
}
.ui-dialog .ui-dialog-titlebar-close {
    position: absolute;
    right: 0; top: 0;
    z-index: 3;
    width: 40px; height: 32px;
    min-width: inherit;
    margin: 0; padding: 0;
    background: linear-gradient( #4A4A4A, #444 );
    border: none;
    border-radius: 0;
}
.ui-dialog .ui-dialog-titlebar-close.ui-state-hover {
    background: linear-gradient( #C22, #C00 );
}
.ui-dialog .ui-dialog-titlebar-close::before,
.ui-dialog .ui-dialog-titlebar-close::after {
    content: '';
    display: block;
    position: absolute;
    left: 50%; top: 50%;
    z-index: 2;
    width: 50%; height: 2px;
    background-color: #EEE;
}
.ui-dialog .ui-dialog-titlebar-close::before {
    transform: translate(-50%,-50%) rotate(45deg);
}
.ui-dialog .ui-dialog-titlebar-close::after {
    transform: translate(-50%,-50%) rotate(-45deg);
}
.ui-dialog .ui-dialog-titlebar-close span {
    display: none;
}
.ui-dialog .ui-dialog-content {
    width: calc( 100% - 8px )!important;
    margin: 0 auto; padding: 24px 16px 8px;
    background: #555;
    border: 1px solid rgba( 0,0,0,.75 );
    box-shadow: 1px 1px 0 0 rgba( 255,255,255,.1 ) inset,
                -1px -1px 0 0 rgba( 0,0,0,.05 ) inset;
    color: #EEE;
}
.ui-dialog .ui-dialog-buttonpane {
    margin: 0; padding: 4px;
    background: linear-gradient( #444, #3A3A3A );
    border: none;
}
.ui-dialog .ui-dialog-buttonpane button {
    display: inline-block;
    padding: 4px 16px 3px;
    background: #4D9E0A;
    border: 1px solid #4D9E0A;
    border-radius: 2px;
    color: #EEE;
    line-height: 1;
    font-size: 12px;
    cursor: pointer;
    transition-duration: .1s;
}
.ui-dialog .ui-dialog-buttonpane button:nth-child(2) {
    background-color: #444;
    border: 1px solid #4D9E0A;
    color: #4D9E0A;
}
.ui-button-text-only .ui-button-text {
    padding: 0;
}
.ui-dialog .ui-dialog-buttonpane button:hover {
    background-color: #60C60D;
    border-color: #60C60D;
}
.ui-dialog .ui-dialog-buttonpane button:nth-child(2):hover {
    background-color: #444;
    border-color: #60C60D;
    color: #60C60D;
}
.ui-dialog .ui-dialog-buttonpane button:active {
    transform: scale( 0.925 );
}

/* editAre */
#editArea, #requiredMessage,
#startDateError, #endDateError, #compareDateError,
#exeStopStartDateError, #exeStopEndDateError,
#bothExeStopDateError, #compareExeStopDateError,
#intervalError, #weekNumberError, #dayOfWeekError,
#dayError, #timeError,
#editArea .patternDay, #editArea .patternDayOfWeek,
#editArea .patternWeekNumber, #editArea .patternTime,
#editArea .patternDayOfWeekHead {
    display: none;
}
#editArea .inputDatetime {
    display: inline-block;
    padding-right: 16px;
}
#editArea .datetimeArea,
#editArea .scheduleBox,
#editArea .noteArea,
#editArea .executionStopDateArea {
    position: relative;
    z-index: 1;
    width: 100%;
    margin-bottom: 24px; padding: 12px 12px 8px;
    background-image: linear-gradient( #555, #555 8px, #585858 );
    border: solid 1px #333;
    box-shadow: 1px 1px 0 0 rgba( 255,255,255,.1 ),
                1px 1px 0 0 rgba( 255,255,255,.1 ) inset;
}
#editArea .scheduleBox {
    display: flex;
    padding: 8px 0;
}
#editArea .boxTitle {
    position: absolute;
    display: inline-block;
    left: 8px; top: -0.5em;
    padding: 0 8px;
    line-height: 1;
    background: #555;
    color: #CCC;
}
#editArea .scheduleBox .scheduleSelectArea,
#editArea .scheduleBox .scheduleDetailArea {
    display: table-cell;
    width: 50%;
    padding: 8px 16px 4px;
}
#editArea .scheduleBox .scheduleSelectArea {
    border-right: 1px solid #333;
}
#editArea .scheduleBox .scheduleDetailArea {
    border-left: 1px solid rgba( 255,255,255,.1 );
}
#editArea textarea,
#editArea input[type="text"],
#editArea select {
    font-family: Consolas, "メイリオ", Meiryo, Osaka, "ＭＳ Ｐゴシック", "MS PGothic", sans-serif;
    height: 24px;
    margin: 0; padding: 0 8px;
    background-color: #333;
    border: 1px solid #666;
    line-height: 24px;
    vertical-align: middle;
    color: #EEE;
}
#editArea textarea:focus,
#editArea input[type="text"]:focus,
#editArea select:focus {
    border-color: #5172A9;
    box-shadow: 0 0 1px #5172A9;
}
#editArea .scheduleSelectList {
    margin: 0; padding: 0;
    list-style: none;
}
#editArea .scheduleSelectList li {
    position: relative;
    z-index: 1;
    margin-bottom: 4px;
    background-color: rgba( 0,0,0,.05 );
}
#editArea .scheduleSelectList li:last-child {
    margin-bottom: 0;
}
#editArea .scheduleSelectList input {
    position: absolute;
    left: -9999px;
}
#editArea .scheduleSelectArea label {
    display: block;
    padding: 2px 8px 1px 2em;
    border: 1px solid rgba( 0,0,0,0 );
    cursor: pointer;
}
#editArea .scheduleSelectArea label:hover {
    background-color: rgba( 0,0,0,.1 );
}
#editArea .scheduleSelectArea input:checked ~ label {
    cursor: default;
}
#editArea .scheduleSelectArea input:focus ~ label {
    border-color: #5172A9;
    box-shadow: 0 0 1px #5172A9;
}
#editArea .scheduleSelectArea input:checked ~ label:hover {
    background-color: transparent;
}
#editArea .scheduleSelectArea label::before,
#editArea .scheduleSelectArea label::after {
    content: '';
    display: block;
    position: absolute;
    z-index: 2;
    left: .5em; top: 50%;
    width: 1em; height: 1em;
    transform: translateY(-50%);
    border: 1px solid #111;
    border-radius: 50%;
}
#editArea .scheduleSelectArea label::before {
    background-color: #444;
    box-shadow: 0 0 .6em rgba( 0,0,0,.6 ) inset,
    -1px -1px 0 1px rgba( 0,0,0,.15 ),
    1px 1px 0 1px rgba( 255,255,255,.05 );
}
#editArea .scheduleSelectArea label::after {
    transition-duration: .2s;
}
#editArea .scheduleSelectList input:checked ~ label::after {
    background-color: #60C60D;
    box-shadow: 0 0 .3em rgba( 0,0,0,.8 ) inset,
    -1px -1px 0 1px rgba( 0,0,0,.05 ),
    1px 1px 0 1px rgba( 255,255,255,.01 );
}
#editArea .scheduleDetail {
    margin-bottom: 8px;
}
#editArea .detailHead {
    display: inline-block;
    min-width: 80px;
}
#editArea .patternWeekNumber,
#editArea .patternDayOfWeek{
    display: inline-block;
}
#editArea .noteArea {
    margin-bottom: 16px;
}
#editArea .noteArea textarea{
    width: 100%; min-height: 80px;
    resize: vertical;
}
#editArea .requiredMessage {
    display: block;
    padding-top: 8px;
    text-align: right;
}
#editArea .errorMessageArea span {
    overflow: hidden;
    display: block;
    margin-bottom: 4px; padding: 8px 16px 7px;
    background-color: #600;
    background-image: repeating-linear-gradient( -45deg, #600, #600 4px, #6C0000 4px, #6C0000 8px );
    border: 1px solid #900;
    color: #DDD;
    animation: errorMessage .5s forwards;
}
#editArea .errorMessageArea span:last-child {
    margin-top: 0;
}
#editArea .errorMessageArea span::before {
    content: '!';
    display: inline-block;
    margin-right: 8px;
    font-weight: bold;
    color: #EE0;
}
@keyframes errorMessage {
  0% { transform: translateX(-30px); }
 10% { transform: translateX( 20px); }
 25% { transform: translateX(-10px); }
 45% { transform: translateX(  5px); }
 70% { transform: translateX( -5px); }
100% { transform: translateX(  0  ); }
}
</style>

<div id="editArea" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200001")}">
    <span class="ui-helper-hidden-accessible"><input type="image"/></span>

    <div class="datetimeArea">
      <span class="boxTitle">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200002")}</span>
        <div class="inputDatetime">
            <span class="input_required">*</span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200003")}:
            <input size="16" maxlength="16" class="callDateTimePicker" type="text" name="startDate" value="">
        </div>
        <div class="inputDatetime">
            {$objMTS->getSomeMessage("ITAWDCH-MNU-1200004")}:
            <input size="16" maxlength="16" class="callDateTimePicker" type="text" name="endDate" value="">
        </div>
    </div>

    <div class="scheduleBox">
        <span class="boxTitle">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200005")}</span>
        <div class="scheduleSelectArea">
        
          <ul class="scheduleSelectList">
            <li>
              <input id="period_1" type="radio" name="period" value="1" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200006")}" checked>
              <label for="period_1">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200006")}</label>
            </li>
            <li>
              <input id="period_2" type="radio" name="period" value="2" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200007")}">
              <label for="period_2">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200007")}</label>
            </li>
            <li>
              <input id="period_3" type="radio" name="period" value="3" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200008")}">
              <label for="period_3">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200008")}</label>
            </li>
            <li>
              <input id="period_4" type="radio" name="period" value="4" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200009")}">
              <label for="period_4">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200009")}</label>
            </li>
            <li>
              <input id="period_5" type="radio" name="period" value="5" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200010")}">
              <label for="period_5">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200010")}</label>
            </li>
            <li>
              <input id="period_6" type="radio" name="period" value="6" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200011")}">
              <label for="period_6">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200011")}</label>
            </li>
          </ul>        

        </div>

        <div class="scheduleDetailArea">
            <div class="scheduleDetail executionInterval">
                <span class="detailHead"><span class="input_required">*</span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200012")}:</span>
                <input size="2" maxlength="2" type="text" name="executionInterval" value="">
                <span class="interval_str">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200013")}</span>
            </div>

            <span class="patternDayOfWeekHead detailHead"><span class="input_required">*</span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200014")}:</span>
            <div class="scheduleDetail patternWeekNumber">
                <select name="patternWeekNumber">
                <option value="1" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200015")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200015")}</option>
                <option value="2" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200016")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200016")}</option>
                <option value="3" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200017")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200017")}</option>
                <option value="4" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200018")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200018")}</option>
                <option value="5" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200019")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200019")}</option>
                </select>
            </div>

            <div class="scheduleDetail patternDayOfWeek">
                <select name="patternDayOfWeek">
                <option value="1" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200020")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200020")}</option>
                <option value="2" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200021")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200021")}</option>
                <option value="3" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200022")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200022")}</option>
                <option value="4" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200023")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200023")}</option>
                <option value="5" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200024")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200024")}</option>
                <option value="6" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200025")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200025")}</option>
                <option value="7" title="{$objMTS->getSomeMessage("ITAWDCH-MNU-1200026")}">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200026")}</option>
                </select>
            </div>

            <div class="scheduleDetail patternDay">
                <span class="detailHead"><span class="input_required">*</span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200027")}:</span>
                <input size="2" maxlength="2" type="text" name="patternDay" value="">
                <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200007")}</span>
            </div>

            <div class="scheduleDetail patternTime">
                <span class="detailHead"><span class="input_required">*</span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200028")}:</span>
                <input size="6" maxlength="5" class="callDateTimePicker2" type="text" name="patternTime" value="">
            </div>
        </div>
    </div>

    <div class="executionStopDateArea">
        <span class="boxTitle">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200029")}</span>
        <input size="16" maxlength="16" class="callDateTimePicker" type="text" name="exeStopStartDate" value="">
        <span>～</span>
        <input size="16" maxlength="16" class="callDateTimePicker" type="text" name="exeStopEndDate" value="">
    </div>

    <div class="noteArea">
        <span class="boxTitle">{$objMTS->getSomeMessage("ITAWDCH-MNU-1200030")}</span>
        <textarea maxlength="4000" rows="5" cols="60" name="note"></textarea>
    </div>

    <div class="errorMessageArea">
        <div id="requiredMessage" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200101")}</span>
        </div>

        <div id="startDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200102")}</span>
        </div>

        <div id="endDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200103")}</span>
        </div>

        <div id="compareDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200104")}</span>
        </div>

        <div id="exeStopStartDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200105")}</span>
        </div>

        <div id="exeStopEndDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200106")}</span>
        </div>

        <div id="bothExeStopDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200107")}</span>
        </div>

        <div id="compareExeStopDateError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200108")}</span>
        </div>

        <div id="intervalError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200109")}</span>
        </div>

        <div id="weekNumberError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200110")}</span>
        </div>

        <div id="dayOfWeekError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200111")}</span>
        </div>

        <div id="dayError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200112")}</span>
        </div>

        <div id="timeError" class="errorMessage">
            <span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200113")}</span>
        </div>
    </div>

    <span class="requiredMessage"><span class="input_required">*</span>{$objMTS->getSomeMessage("ITAWDCH-MNU-1200031")}</span>
</div>
    <!-------------------------------- モーダルウィンドウ -------------------------------->

EOD;



    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
