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
$strProviderName = "";
$strProviderImageUrl = "";
$strProviderUserImageUrl = "";
$boolIsLogin = false;
$boolSsoAuthUser = false;
$boolVisibleChangePasswordButton = false;

if ($login_status_flag == 1) {
    //----ログインしている場合
    $boolIsLogin = true;

    $tmpArrayRet = checkLoginRequestForUserAuthInorExt($auth->getUsername(), $objDBCA);
    if ($tmpArrayRet[1] !== null) {
        $strErrMsgBody = $tmpArrayRet[3];
        // アクセスログ出力(想定外エラー)
        // "ERROR:UNEXPECTED, DETAIL:ACCOUNT LOCK CHECK NG END. STATUS IS [{}] . CHECK TRIGGER IS [{}]. DETAIL IS [{}]."
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-12",array($checkStatus,$strCheckTriggerName,$strErrMsgBody)));
        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,10310103);
        exit();
    }
    $boolLocalAuthUser = $tmpArrayRet[0]['AuthUserType']; // 判別結果
    unset($tmpArrayRet);

    // 外部認証判定分岐
    // -----外部認証コンフィグファイルが存在して and 認証対象ユーザー名が外部認証ユーザの場合以外 = "ローカル認証"でログイン中の場合は、パスワード変更ボタンを表示させる 外部認証ユーザーがログイン時には”パスワード変更”ボタンを非表示とする 2018/03/05
    if (!(enableActiveDirectorySync($strExternalAuthSettingsFilename) && $boolLocalAuthUser === false)) { // スキップしてパスワード変更ページボタンを消す
        
        if ($pwc_by_user_forbidden !== '1') {
            $boolVisibleChangePasswordButton = true;
        }
    }
    // SSO認証確認
    $auth_session = $auth->getSessionData();
    if (isset($auth_session['provider_id']) && isset($auth_session['provider_user_id'])) {
        $boolSsoAuthUser = true;
        $boolVisibleChangePasswordButton = false;
        $strProviderName = $auth_session['provider_name'];
        if (!empty($auth_session['provider_logo'])) {
            $strProviderImageUrl = $auth_session['provider_logo'];
        }
        if (!empty($auth_session['provider_user_image_url']))  {
            $strProviderUserImageUrl = $auth_session['provider_user_image_url'];
        }
    }
    unset($auth_session);
    //ログインしている場合----
}

// javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
$timeStamp_ita_icon_png = filemtime("$root_dir_path/webroot/common/imgs/ita_icon.png");
// 初回フィルタ
$varInitialFilter = "";
if (!empty($g['menu_initial_filter'])) {
    $varInitialFilter = $g['menu_initial_filter'];
}

$getCopy = $_GET;
$get_parameter = "";
if("" != http_build_query($getCopy)){
    $get_parameter = "?" . http_build_query($getCopy);
}
$get_parameter = str_replace('+', '%20', $get_parameter);

// 以下コンテンツの表示
?>
    </head>
    <body id="INDEX">
        <div class="wholecontainer">
            <div id="PAGETOP">
                <!--================-->
                <!--　　ヘッダー　　-->
                <!--================-->
                <div id="HEADER">
                  <div class="headerInner">
                    <div class="itaLogo"><img src="<?= $scheme_n_authority ?>/common/imgs/ita_icon.png?<?= $timeStamp_ita_icon_png ?>" alt="Exastro IT Automation" /></div>
                    <div class="headerMenuGroupName"><h1 title="<?= $site_name ?>"><?= $site_name ?></h1></div>


                    <div class="headerInformation"> 

                      <?php if ($boolIsLogin) { ?>

                      <!-- ログイン情報表示エリア -->
                      <div class="itaLoginUserInformation">
                        <div class="itaLoginUserData">
                          <div class="itaLoginUserName"><?= $objMTS->getSomeMessage("ITAWDCH-STD-501",'<span class="userDataText">'.htmlspecialchars($username_jp, ENT_QUOTES, "UTF-8").'</span>') ?></div>
                          <div class="itaLoginUserID"><?= $objMTS->getSomeMessage("ITAWDCH-STD-502",'<span class="userDataText">'.$username.'</span>') ?></div>
                        </div>

                        <?php if ($boolSsoAuthUser) { ?>

                        <!-- SSO認証情報表示エリア  -->
                        <div class="itaLoginUserIcon">
                          <ul class="itaLoginUserIconList">

                            <?php if ($strProviderImageUrl) { ?>

                              <li class="itaLoginUserIconItem"><span class="itaLoginUserIconImage" style="background-image: url(<?= $strProviderImageUrl ?>);" title="<?= $strProviderName ?>"></span></li>

                            <?php } else { ?>

                                <li class="itaLoginUserIconItem"><span class="itaLoginUserIconText"><?= $strProviderName ?></span></li>

                            <?php } ?>
                            <?php if ($strProviderUserImageUrl) { ?>

                              <li class="itaLoginUserIconItem"><span class="itaLoginUserIconImage" style="background-image: url(<?= $strProviderUserImageUrl ?>);" title=""></span></li>

                            <?php } ?>

                          </ul>
                        </div>
                        <!-- /SSO認証情報表示エリア  -->

                        <?php } ?>

                      </div>
                      <!-- /ログイン情報表示エリア -->

                      <?php } ?>

                      <!-- FORM AREA  -->
                      <div class="headerMenu">


                        <!----  ロールボタン  ---->
                        <?php if ($role_button === '1') { ?>
                          <form  style="display:inline" onclick="role_display()" >
                            <input type="button" value="<?= $objMTS->getSomeMessage("ITAWDCH-STD-50017") ?>" />
                          </form>
                        <?php } ?>
                        <!----  /ロールボタン  ---->

                        <?php if ($boolVisibleChangePasswordButton) { ?>
                        <!----  パスワード変更ボタン  ---->
                        <form method="POST" style="display:inline" action="<?= $scheme_n_authority ?>/common/common_change_password_form.php<?= $get_parameter ?>" >
                          <input type="submit" value="<?= $objMTS->getSomeMessage("ITAWDCH-STD-504") ?>" />
                        </form>
                        <!----  /パスワード変更ボタン  ---->

                        <?php } ?>
                        <?php if ($boolIsLogin) { ?>

                        <!----  ログアウトボタン  ---->
                        <form method="POST" style="display:inline" action="<?= $scheme_n_authority ?>/common/common_auth.php<?= $get_parameter ?>" >
                          <input type="submit" name="logout" value="<?= $objMTS->getSomeMessage("ITAWDCH-STD-503") ?>" />
                        </form>
                        <!----  /ログアウトボタン  ---->

                        <?php } ?>

                      </div>
                      <!-- /FORM AREA -->
                    </div>
                  </div>
                </div>
                <!--================-->
                <!--　　メニュー　　-->
                <!--================-->
                <div id="MENU">
                    <h2>Menu</h2>
                    <ul>
<?= $menus ?>
                    </ul>
                </div>
                <hr>
                <!--================-->
                <!--　　記事部分　　-->
                <!--================-->
                <div id="KIZI">
    <div id="sysInitialFilter" style="display:none" class="text"><?= $varInitialFilter ?></div>
