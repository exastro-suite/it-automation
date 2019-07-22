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
    // 言語モードを取得する。
     
    if(@count($g['objMTS']) != 0){
        switch($g['objMTS']->getLanguageMode()){
            case "ja_JP":$strLang = "ja";break;
            default:$strLang = "en";   // ja_JP以外は英語に設定
        }
    }
    else{
        $strLang="en";
    }

    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_common_style_css=filemtime("$root_dir_path/webroot/common/css/common_style.css");
    $timeStamp_common_superTables_css=filemtime("$root_dir_path/webroot/common/css/common_superTables.css");
    $timeStamp_jquery_ui_1_10_4_custom_min_css=filemtime("$root_dir_path/webroot/common/css/jquery-ui-1.10.4.custom.min.css");
    $timeStamp_jquery_datetimepicker_css=filemtime("$root_dir_path/webroot/common/css/jquery.datetimepicker.css");
    $timeStamp_select2_css=filemtime("$root_dir_path/webroot/common/css/select2.css");
    $timeStamp_common_superTables_js=filemtime("$root_dir_path/webroot/common/javascripts/common_superTables.js");
    $timeStamp_common_tableSort_js=filemtime("$root_dir_path/webroot/common/javascripts/common_tableSort.js");
    $timeStamp_common_valueControllers_js=filemtime("$root_dir_path/webroot/common/javascripts/common_valueControllers.js");
    $timeStamp_common_ky_javasctipts_js=filemtime("$root_dir_path/webroot/common/javascripts/common_ky_javasctipts.js");
    $timeStamp_jquery_1_11_3_js=filemtime("$root_dir_path/webroot/common/javascripts/jquery-1.11.3.js");
    $timeStamp_jquery_ui_1_10_4_custom_min_js=filemtime("$root_dir_path/webroot/common/javascripts/jquery-ui-1.10.4.custom.min.js");
    $timeStamp_jquery_datetimepicker_js=filemtime("$root_dir_path/webroot/common/javascripts/jquery.datetimepicker.js");
    $timeStamp_select2_js=filemtime("$root_dir_path/webroot/common/javascripts/select2.js");
    $timeStamp_plotly_latest_min_js=filemtime("$root_dir_path/webroot/common/javascripts/plotly-latest.min.js");
    $timeStamp_favicon_ico=filemtime("$root_dir_path/webroot/common/imgs/favicon.ico");

print <<< EOD
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
    <html lang="ja">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta http-equiv="Content-Language" content="ja">
        <meta http-equiv="Content-Script-Type" content="text/javascript">
        <meta http-equiv="content-style-type" content="text/css">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>{$title_name}</title>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_superTables.js?{$timeStamp_common_superTables_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_tableSort.js?{$timeStamp_common_tableSort_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_valueControllers.js?{$timeStamp_common_valueControllers_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_ky_javasctipts.js?{$timeStamp_common_ky_javasctipts_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/jquery-1.11.3.js?{$timeStamp_jquery_1_11_3_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/jquery-ui-1.10.4.custom.min.js?{$timeStamp_jquery_ui_1_10_4_custom_min_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/jquery.datetimepicker.js?{$timeStamp_jquery_datetimepicker_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/select2.js?{$timeStamp_select2_js}"></script>
        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/plotly-latest.min.js?{$timeStamp_plotly_latest_min_js}"></script>
        <script type="text/javascript">
        $(window).load(function (){relayout()});
        $(window).resize(function (){relayout()});
        </script>
        <link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/common_style.css?{$timeStamp_common_style_css}">
        <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/common_superTables.css?{$timeStamp_common_superTables_css}">
        <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/jquery-ui-1.10.4.custom.min.css?{$timeStamp_jquery_ui_1_10_4_custom_min_css}">
        <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/jquery.datetimepicker.css?{$timeStamp_jquery_datetimepicker_css}">
        <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/select2.css?{$timeStamp_select2_css}">
        <link rel="shortcut icon" href="{$scheme_n_authority}/common/imgs/favicon.ico?{$timeStamp_favicon_ico}" type="image/vnd.microsoft.icon">
        <span style="display:none;" id="HTML_AJAX_LOADING"></span>
        <!-- #1241 2107/09/14 datetimepickerなどで使用する言語モードを設定 -->
        <div id="LanguageMode" style="display:none" class="text">{$strLang}</div>

EOD;
?>
