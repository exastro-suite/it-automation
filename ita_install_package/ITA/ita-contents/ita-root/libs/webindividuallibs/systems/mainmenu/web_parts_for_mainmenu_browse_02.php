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

    $tmpHtmlOfBrowse02Ele1 = "";
    if( $login_status_flag == 1 ){
        //----ログインしている場合

        // AD連携（外部認証） ----
        $tmpArrayRet = checkLoginRequestForUserAuthInorExt($auth->getUsername(), $objDBCA);
        if( $tmpArrayRet[1] !== null ){
            $strErrMsgBody = $tmpArrayRet[3];
            // アクセスログ出力(想定外エラー)
            web_log($objMTS->getSomeMessage("ITAWDCH-ERR-12",array($checkStatus,$strCheckTriggerName,$strErrMsgBody)));
            // 想定外エラー通知画面にリダイレクト
            webRequestForceQuitFromEveryWhere(500,10310103);
            exit();
            break;
        }
        else{
            $boolLocalAuthUser = $tmpArrayRet[0]['AuthUserType']; // 判別結果
        }
        unset($tmpArrayRet);
        // ---- AD連携（外部認証）

        $tmpStrHtmlBody = "";

        // 外部認証判定分岐
        // -----外部認証コンフィグファイルが存在して and 認証対象ユーザー名が外部認証ユーザの場合以外 = "ローカル認証"でログイン中の場合は、パスワード変更ボタンを表示させる 外部認証ユーザーがログイン時には”パスワード変更”ボタンを非表示とする
        if( !(enableActiveDirectorySync($strExternalAuthSettingsFilename) && $boolLocalAuthUser === false ) ) // スキップしてパスワード変更ページボタンを消す
        // AD連携（外部認証） -----
        {
            if( $pwc_by_user_forbidden !== '1' ){
                //----パスワード変更ページを隠さない
                $tmpStrHtmlBody = 
<<< EOD
                                    <form method="POST" style="display:inline" action="{$scheme_n_authority}/common/common_change_password_form.php?grp={$ACRCM_group_id}&no={$ACRCM_id}" >
                                        <input type="submit" value="{$objMTS->getSomeMessage("ITAWDCH-STD-504")}">
                                    </form>　
EOD;
            //パスワード変更ページを隠さない----
            }
        } // 外部認証ユーザーはパスワード変更ページボタン非表示

        $tmpHtmlOfBrowse02Ele1 .= 
<<< EOD

                                    {$objMTS->getSomeMessage("ITAWDCH-STD-501",$username_jp)}
                                    {$objMTS->getSomeMessage("ITAWDCH-STD-502",$username)}
                                    <!----2018/07/19---->
                                    <!----表示モードを変更するセレクトボックス---->
                                    <select id="size_select" style="margin-right:15px;">
                                        <option value="large_panel">{$objMTS->getSomeMessage("ITAWDCH-STD-1007")}</option>
                                        <option value="middle_panel">{$objMTS->getSomeMessage("ITAWDCH-STD-1008")}</option>
                                        <option value="small_panel">{$objMTS->getSomeMessage("ITAWDCH-STD-1009")}</option>
                                        <option value="classic">{$objMTS->getSomeMessage("ITAWDCH-STD-1010")}</option>
                                    </select>
                                    <!----2018/07/19---->
                                    {$tmpStrHtmlBody}
                                    <form method="POST" style="display:inline" action="{$scheme_n_authority}/common/common_auth.php?grp={$ACRCM_group_id}&no={$ACRCM_id}" >
                                        <input type="submit" name="logout" value="{$objMTS->getSomeMessage("ITAWDCH-STD-503")}">
                                    </form>
EOD;
        unset($tmpStrHtmlBody);
        //ログインしている場合----
    }
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_ita_icon_png=filemtime("$root_dir_path/webroot/common/imgs/ita_icon.png");
    $tmpHtmlOfBrowse02Body = 
<<< EOD
    </head>
    <body id="INDEX">
        <div class="wholecontainer">
            <div id="PAGETOP">
                <!--================-->
                <!--　　ヘッダー　　-->
                <!--================-->
                <div id="HEADER">
                    <table width="1220px">
                        <tr>
                            <td>
                                <!----2018/07/19---->
                                <div style="width:190px; height:70px; float:left; display:flex">
                                    <img src="{$scheme_n_authority}/common/imgs/ita_icon.png?{$timeStamp_ita_icon_png}" style="margin-top:7px; height:48px;">
                                    <div class="ita_name">IT<span style="margin-left:5px;"></span>Automation</div>
                                </div>
                                <!----2018/07/19---->
                                <h1>{$site_name}</h1>
                            </td>
                            <td align="right">
                                <h4>
{$tmpHtmlOfBrowse02Ele1}
                                </h4>
                            </td>
                        </tr>
                    </table>
                    <ul id="PAN"><li>index</li></ul>
                </div>
                <hr>
                <!--================-->
                <!--　　メニュー　　-->
                <!--================-->
                <div id="MENU">
                    <h2>Menu</h2>
                    <ul>
{$menus}
                    </ul>
                </div>
                <hr>
                <!--================-->
                <!--　　記事部分　　-->
                <!--================-->
                <div id="KIZI">

EOD;
    print $tmpHtmlOfBrowse02Body;

    unset($tmpHtmlOfBrowse02Body);
    unset($tmpHtmlOfBrowse02Ele1);

    // 初回フィルタ
    $varInitialFilter = is_null($g['menu_initial_filter'])?"":$g['menu_initial_filter'];
    print 
<<< EOD
    <div id="sysInitialFilter" style="display:none" class="text">{$varInitialFilter}</div>
EOD;

?>
