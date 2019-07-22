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
    //  【特記事項】
    //    ・ModuleDistictCode(110)
    //   common_auth.php(代表)からのみ呼び出されることを前提とする
    //   変数「$ASJTM_id」はcommon_auth.phpの所属メニューではないことを前提とする
    //
    //////////////////////////////////////////////////////////////////////

    // common_auth_config.phpの読み込み
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_auth_config.php");

    $tmpStrTitleId = "";
    $tmpStrTitleText = "";
    $tmpStrArticleBody = "";

    $auth = null;
    saLoginExecute($auth, $objDBCA, $ACRCM_id, true);

    // ----■ログアウト処理を行うフラグが、リクエストのPOSTクエリーに含まれているかをチェックする。
    if(isset($_POST['logout'])){
        // ----■ログイン処理を行うフラグが、リクエストのGETクエリーに含まれているかをチェックする。
        if( isset($_GET['login']) ){
            // ----ログインとログアウトの対立オーダー

            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-23"));

            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,11010101);
            exit();

            // ログインとログアウトの対立オーダー----
        }
        else{
            //----ログアウト
            $tmpStrTitleId = "gateLogout";
            $tmpStrTitleText = $objMTS->getSomeMessage("ITAWDCH-STD-1006");
            if($auth->checkAuth()){
                // ----ログイン中の場合
                $auth->logout();
                $tmpStrArticleBody .= "<p>{$objMTS->getSomeMessage("ITAWDCH-STD-1102")}</p>\n";
                $tmpStrArticleBody .= "<p><a href=\"{$_SERVER['PHP_SELF']}?login&grp={$ASJTM_grp_id}&no={$ASJTM_id}\">{$objMTS->getSomeMessage("ITAWDCH-STD-1103")}</a></p>\n";
                // ログイン中の場合----
            }
            else{
                // ----ログインしていないユーザからの、ログアウト-リクエスト
                $tmpStrArticleBody .= "<p>{$objMTS->getSomeMessage("ITAWDCH-STD-1104")}</p>\n";
                $tmpStrArticleBody .= "<p><a href=\"{$_SERVER['PHP_SELF']}?login&grp={$ASJTM_grp_id}&no={$ASJTM_id}\">{$objMTS->getSomeMessage("ITAWDCH-STD-1001")}</a></p>\n";
                // ログインしていないユーザからの、ログアウト-リクエスト----
            }
            //ログアウト----
        }
        // ログイン処理を行うフラグが、リクエストのGETクエリーに含まれているかをチェックする。■----

    }
    // ログアウト処理を行うフラグが、リクエストのPOSTクエリーに含まれているかをチェックする。■----
    else if(!isset($_GET['login'])){
        //----ログインのオーダもない
        $tmpStrTitleId = "gateLogin";
        $tmpStrTitleText = $objMTS->getSomeMessage("ITAWDCH-STD-1001");
        $tmpStrArticleBody .= "<p><a href=\"{$_SERVER['PHP_SELF']}?login&no={$ASJTM_id}\">{$objMTS->getSomeMessage("ITAWDCH-STD-1001")}</a></p>\n";
        //ログインのオーダもない----
    }
    else{
        //----ログインのオーダがある
        $tmpStrTitleId = "gateLogin";
        $tmpStrTitleText = $objMTS->getSomeMessage("ITAWDCH-STD-1001");
        $tmpStrArticleBody = $g['tmpBuffer_AUTH'];
        unset($g['tmpBuffer_AUTH']);
        //ログインのオーダがある----
    }

    if($auth->checkAuth()){
        // ----ログイン中の場合

        if( $_SERVER["PHP_SELF"] == $ASJTM_representative_file_name ){
            //----common_auth自身だった場合
            
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-24"));
            
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(400,11010102);
            exit();
            //common_auth自身だった場合----
        }
        else{
            // ----common_auth自身ではなかった場合

            // ----■ログイン成功後に表示させたいメニューのＩＤをもつ、代表フラグが有効になっている、ファイル名のファイルにリダイレクトする
            insideRedirectCodePrint($ASJTM_representative_file_name,0);
            // ログイン成功後に表示させたいメニューのＩＤをもつ、代表フラグが有効になっている、ファイル名のファイルにリダイレクトする■----

            // common_auth自身ではなかった場合----
        }

        // ----ログイン中の場合
    }

    //2019/01/11 javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_common_auth_00_javascript_js=filemtime("$root_dir_path/webroot/common/javascripts/common_auth_00_javascript.js");
    $timeStamp_ita_icon_png=filemtime("$root_dir_path/webroot/common/imgs/ita_icon.png");

    $tmpHtmlOfAuth02Body = 
<<< EOD

        <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_auth_00_javascript.js?{$timeStamp_common_auth_00_javascript_js}"></script>
    </head>
    <body id="INDEX">
        <div class="wholecontainer">
            <div id="PAGETOP">
                <!--================-->
                <!--　　ヘッダー　　-->
                <!--================-->
                <div id="HEADER">
                    <div style="width:190px; height:70px; float:left; display:flex">
                        <img src="{$scheme_n_authority}/common/imgs/ita_icon.png?{$timeStamp_ita_icon_png}" style="margin-top:7px; height:48px;">
                        <div class="ita_name">IT<span style="margin-left:5px;"></span>Automation</div>
                    </div>
                    <ul id="PAN"><li>index</li></ul>
                </div>
                <hr>
                <!--================-->
                <!--　　記事部分　　-->
                <!--================-->
                <div id="{$tmpStrTitleId}">
                <h2>{$tmpStrTitleText}</h2>
                    <!-- start log-in-out gate text//-->
                    <div class="text">
{$tmpStrArticleBody}
                    </div>
                    <!-- end log-in-out gate text//-->
EOD;


    print $tmpHtmlOfAuth02Body;

    unset($tmpHtmlOfAuth02Body);

?>
