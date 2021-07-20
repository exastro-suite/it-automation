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

////////////////////////////////////////////////////////////////////////

/**
  * autoRegistUser
  *   SSO認証されたユーザーを内部DBに登録または更新する
  * @param auth_session
  *   @type array
  *   $auth_session['provider_id',        => $provider_id,
  *                 'provider_user_id'    => $provider_user_id,
  *                 'provider_user_name'  => $provider_user_name,
  *                 'provider_user_email' => $provider_user_email]
  * @discription
  *   provider_id,provider_user_idの組合せでuniqueとして登録済みか調べて未登録なら登録する
  *   廃止済み(DISUSE_FLAG=1)のレコードがあった場合は登録済みで追加登録はしない
  *   登録済み(未廃止)レコードがあった場合provider_user_nameとprovider_user_emailが更新されていたら更新する
  *   未登録の場合、provider_nameをUSERNAME_JPにprovider_user_emailをMAIL_ADDRESSに登録する
  *   ログインID(USERNAME)はprovider_user_emailが存在していたらprovider_user_email、
  *   存在していない場合、provider_user_nameをベースとして半角文字使用不可文字を除外して
  *   既存IDで使用されていないか確認して使用されていなければそれを使用し
  *   使用されていたらランダム8文字を付加して登録する
  *   なお、メールアドレス未登録で名前が全角文字の場合は'user'をベースとして登録する
  *   新規登録時にはroll_id=2100000001を自動的にを割り当てる
  **/
function autoRegistUser($auth_session) {
    global $objMTS, $objAuth;
    $strTablename = 'A_ACCOUNT_LIST';
    $intRoleId = 2100000001;
    $intRegUID = -100030;

    if (empty($auth_session['provider_id']) || empty($auth_session['provider_user_id'])) {
        // sessionデータなし
        $objAuth->isError = true;
        $objAuth->errCode = 'ERR-AUTOREGISTUSER-01';
        $objAuth->errMsg = 'could not get provider_user_id';
        $objAuth->errDetail = print_r($auth_session, true);
        return false;
    }
    $aryUser = find('SELECT USER_ID,USERNAME_JP,MAIL_ADDRESS,DISUSE_FLAG '
                   .'FROM A_ACCOUNT_LIST '
                   .'WHERE AUTH_TYPE= :AUTH_TYPE AND '
                   .'PROVIDER_ID = :PROVIDER_ID AND '
                   .'PROVIDER_USER_ID = :PROVIDER_USER_ID',
                   ['AUTH_TYPE'        => 'sso',
                    'PROVIDER_ID'      => $auth_session['provider_id'],
                    'PROVIDER_USER_ID' => $auth_session['provider_user_id'],
                   ], true);
    // 登録されているか確認
    if (!empty($aryUser) && is_array($aryUser)) {
        // 登録済み
        $strAction = 'UPDATE';

        // 廃止済み確認
        $strDisuseFlag = $aryUser['DISUSE_FLAG'];
        if ($strDisuseFlag === '1') {
            // 廃止済み
            // 廃止済みユーザーは自動復活する
            $strDisuseFlag = '0';
            $strAction = 'L_REVIVE';
        }
        // username_jp,mail_addressが変更されていないか、自動復活でないかチェック
        if ((empty($auth_session['provider_user_name']) || ($aryUser['USERNAME_JP'] === $auth_session['provider_user_name'])) &&
            $aryUser['MAIL_ADDRESS'] === $auth_session['provider_user_email'] &&
            $strAction !== 'L_REVIVE') {
            // 変更なし(provider_user_nameが取得できない(=empty)場合は変更なしとみなす)
            return true;
        }
        // 変更あり(更新)
        query('UPDATE A_ACCOUNT_LIST '
             .'SET '
             .'USERNAME_JP = :USERNAME_JP, '
             .'MAIL_ADDRESS = :MAIL_ADDRESS, '
             .'DISUSE_FLAG = :DISUSE_FLAG, '
             .'LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP, '
             .'LAST_UPDATE_USER = :LAST_UPDATE_USER '
             .'WHERE '
             .'USER_ID = :USER_ID',
             ['USER_ID'          => $aryUser['USER_ID'],
              'USERNAME_JP'      => empty($auth_session['provider_user_name']) ? $aryUser['USERNAME_JP'] : $auth_session['provider_user_name'],
              'MAIL_ADDRESS'     => $auth_session['provider_user_email'],
              'DISUSE_FLAG'      => $strDisuseFlag,
              'LAST_UPDATE_USER' => $intRegUID,
             ], $strTablename, $strAction);
        return true;
    }
    // 新規登録
    $strAction = 'INSERT';
    // ----username(LOGIN_ID) 構築
    $username = '';
    $ext = '';
    if (!empty($auth_session['provider_user_email'])) {
        $username = $auth_session['provider_user_email'];
    } else {
        $username = $auth_session['provider_user_name'];
    }
    // 全角英数字記号を半角に変換=>スペースを'_'に変換=>メールアドレス使用可能文字以外を除外
    $username = preg_replace('/[^a-zA-Z0-9_\.\-=\+@]/','', str_replace([' ','　'],'_',mb_convert_kana($username,'a','UTF-8')));

    // ベースusernameが空文字列の場合は'user-xxxxxxxx'を付与
    if (empty($username) || preg_match('/^_*$/', $username)) {
        $username = 'user';
        $ext = '-'.bin2hex(file_get_contents('/dev/urandom',false,null,0,4)); // ランダムな英数[0-9a-f]8桁
    }
    // ----UNIQUEなusernameを見つける
    $cnt = 0;
    while (find('SELECT count(*) FROM A_ACCOUNT_LIST WHERE USERNAME = :USERNAME', ['USERNAME' => $username.$ext],true) > 0) {
        $cnt++;
        if ($cnt > 10) {
            //無限ループ防止 (10回も重複があることは考えられない)
            $username = '';
            $ext = '';
            break;
        }
        $ext = '-'.bin2hex(file_get_contents('/dev/urandom', false, null, 0, 4));
    }
    // UNIQUEなusernameを見つける----
    $username = $username.$ext;
    if (empty($username)) {
        // ログインID設定失敗
        $objAuth->isError = true;
        $objAuth->errCode = 'ERR-AUTOREGISTUSER-02';
        $objAuth->errMsg = 'can not set random username(login_id)';
        $objAuth->errDetail = print_r($auth_session, true);
        return false;
    }
    // username(LOGIN_ID) 構築----
    // ----ユーザー名(USERNAME_JP)設定
    $username_jp = $auth_session['provider_user_name'];
    if (empty($username_jp)) {
        web_log("WARNING: COULD NOT GET PROVIDER_USER_NAME [username]{$username},[FILE]".__FILE__.",[FUNCTION]".__FUNCTION__.",[LINE]".__LINE__);
        $username_jp = $username;
    }
    // ユーザー名(USERNAME_JP)設定----
    transactionStart();
    // ----USER追加
    $userId = query('INSERT INTO A_ACCOUNT_LIST '
                   .'(USER_ID, USERNAME, USERNAME_JP, MAIL_ADDRESS, AUTH_TYPE, PROVIDER_ID, PROVIDER_USER_ID, PASSWORD, DISUSE_FLAG, NOTE, LAST_LOGIN_TIME, PW_LAST_UPDATE_TIME, LAST_UPDATE_TIMESTAMP, LAST_UPDATE_USER) '
                   .'VALUES '
                   .'(:USER_ID, :USERNAME, :USERNAME_JP, :MAIL_ADDRESS, :AUTH_TYPE, :PROVIDER_ID, :PROVIDER_USER_ID, md5(:PASSWORD), :DISUSE_FLAG, :NOTE, SYSDATE(), :PW_LAST_UPDATE_TIME, :LAST_UPDATE_TIMESTAMP, :LAST_UPDATE_USER)',
                    [
                      'USERNAME'            => $username,
                      'USERNAME_JP'         => $username_jp,
                      'MAIL_ADDRESS'        => $auth_session['provider_user_email'],
                      'PASSWORD'            => 'password',
                      'PW_LAST_UPDATE_TIME' => '9999-12-31 23:59:59.999999',
                      'AUTH_TYPE'           => 'sso',
                      'PROVIDER_ID'         => $auth_session['provider_id'],
                      'PROVIDER_USER_ID'    => $auth_session['provider_user_id'],
                      'DISUSE_FLAG'         => '0',
                      'NOTE'                => '',
                      'LAST_UPDATE_USER'    => $intRegUID,
                    ], $strTablename, $strAction);
    // USER追加----
    // ----ROLEとUSERの紐づけ
    query('INSERT INTO A_ROLE_ACCOUNT_LINK_LIST '
         .'(LINK_ID, USER_ID, ROLE_ID, DISUSE_FLAG,LAST_UPDATE_USER, LAST_UPDATE_TIMESTAMP) '
         .'VALUES '
         .'(:LINK_ID, :USER_ID, :ROLE_ID, :DISUSE_FLAG, :LAST_UPDATE_USER, :LAST_UPDATE_TIMESTAMP) '
         ,['USER_ID'          => $userId,
           'ROLE_ID'          => $intRoleId,
           'DISUSE_FLAG'      => '0',
           'LAST_UPDATE_USER' => $intRegUID,
          ], 'A_ROLE_ACCOUNT_LINK_LIST', 'INSERT');
    // ROLEとUSERの紐づけ----
    transactionCommit();
    return true;
}

/**
  * getConfigSsoAuth
  * 認証プロバイダーに関する設定をarrayで返却する
  * @param
  *   @providerId
  *   @type int
  * @return
  *   @array
  * @description
  *   SSO認証用のプロバイダー設定情報の配列を返却する
  *   パラメータがある場合は指定providerIdの情報のみを返却し指定なしの場合は全てのプロバイダーの情報を返却する
  **/
function getConfigSsoAuth ($providerId = null) {
    global $scheme_n_authority;

    $logoPrefix = '/uploadfiles/2100000231/LOGO';
    $sql = 'SELECT * FROM A_PROVIDER_LIST WHERE DISUSE_FLAG = :DISUSE_FLAG ';
    $bind = ['DISUSE_FLAG' => '0'];
    if (!empty($providerId)) {
        $sql .= 'AND  PROVIDER_ID = :PROVIDER_ID';
        $bind += ['PROVIDER_ID' => $providerId];
    }
    $aryProvider = find($sql, $bind);
    $aryConfig = [];
    foreach ($aryProvider as $row) {
        $tmpRow = [
                    'providerId'   => $row['PROVIDER_ID'],
                    'providerName' => $row['PROVIDER_NAME'],
                    'authType'     => $row['AUTH_TYPE'],
                    'note'         => $row['NOTE'],
                    'visibleFlag'  => $row['VISIBLE_FLAG'],
                    'disuseFlag'   => $row['DISUSE_FLAG'],
                  ];
        if ($row['LOGO']) {
            $tmpRow['providerLogo'] = sprintf('%s/%010d/%s', $logoPrefix, $row['PROVIDER_ID'], $row['LOGO']);
        }
        $aryProviderAttr = find('SELECT * FROM A_PROVIDER_ATTRIBUTE_LIST WHERE PROVIDER_ID = :PROVIDER_ID AND DISUSE_FLAG = :DISUSE_FLAG', ['PROVIDER_ID' => $row['PROVIDER_ID'], 'DISUSE_FLAG' => '0']);
        foreach ($aryProviderAttr as $attr_row) {
            $tmpRow += [$attr_row['NAME'] => $attr_row['VALUE']];
        }
        // 内容確認
        $strWarn = '';
        if ($tmpRow['authType'] === 'oauth2') {
            $aryWarn = [];
            // ----必須項目チェック
            $aryEmpty = [];
            foreach (['clientId','clientSecret','authorizationUri','accessTokenUri','resourceOwnerUri','id', 'name'] as $k) {
                if (!isset($tmpRow[$k]) || empty($tmpRow[$k])) {
                    $aryEmpty[] = $k;
                }
            }
            if (!empty($aryEmpty)) {
                $aryWarn[] = 'empty or does not exists:'.implode(',',$aryEmpty);
            }
            // 必須項目チェック----
            // ---URI フォーマットチェック
            $aryFormatErr = [];
            foreach (['authorizationUri','accessTokenUri','resourceOwnerUri'] as $k) {
                if (isset($tmpRow[$k]) && !preg_match('|^https*://|', $tmpRow[$k])) {
                    $aryFormatErr[] = $k;
                }
            }
            if (!empty($aryFormatErr)) {
                $aryWarn[] = 'Uri format error:'.implode(',',$aryFormatErr);
            }
            // URI フォーマットチェック----
            // ----proxyのフォーマットチェック(tcp://host:port or http://host;port でない場合はNG)
            if (isset($tmpRow['proxy']) && !empty($tmpRow['proxy']) && !preg_match('!^(tcp|http)://[0-9a-zA-Z\.\-]+:[1-9][0-9]+$!', $tmpRow['proxy'])) {
                $aryWarn[] = 'proxy setting format error';
            }
            // proxyのフォーマットチェック----
            $strWarn = implode(";;", $aryWarn);
        } else {
            $strWarn = 'AUTH_TYPE is invalid';
        }
        if (!empty($strWarn)) {
            // ----web_logへのWARNINGは非活性
            // web_log("WARNING:SSO CONFIG IS INVALID(skip) PROVIDER_ID:{$row['PROVIDER_ID']} {$strWarn} on [FILE]".__FILE__.",[FUNCTION]".__FUNCTION__.",[LINE]".__LINE__);
            // web_logへのWARNINGは非活性----
        } else {
            $aryConfig[] = $tmpRow;
        }
    }
    if (!empty($providerId)) {
        return array_shift($aryConfig);
    }
    return $aryConfig;
}

/**
  * findUser
  * @param auth_session
  *   @type array ['provider_id' => $provider_id,
  *                 'provider_user_id => $provider_user_id]
  * @return username
  *   @type string
  * @description
  *   SSO認証されたuserを検索して存在したらusernameを返却する
  *   (Authの派生classからユーザー定義関数としてcallされることを想定)
  **/
function findUser ($auth_session) {
    if (empty($auth_session['provider_id']) || empty($auth_session['provider_user_id'])) {
        return false;
    }
    return find('SELECT USERNAME FROM A_ACCOUNT_LIST '
               .'WHERE DISUSE_FLAG = :DISUSE_FLAG AND AUTH_TYPE = :AUTH_TYPE AND '
               .'PROVIDER_ID = :PROVIDER_ID AND PROVIDER_USER_ID = :PROVIDER_USER_ID '
               .'LIMIT 1',
               ['DISUSE_FLAG'      => '0',
                'AUTH_TYPE'        => 'sso',
                'PROVIDER_ID'      => $auth_session['provider_id'],
                'PROVIDER_USER_ID' => $auth_session['provider_user_id'],
               ], true);
}

// setRegistFormFunctionに設定する関数
// autoRegistFunctionで登録できなった場合ここに到達する(これが実行されることはないはず)
function registFormFunction ($auth_session=null, $strMsg=null) {
    global $objAuth;
    $objAuth->isError = true;
    $objAuth->errCode = 'ERR-REGISTFORMFUNCTION-01';
    $objAuth->errMsg = 'could not registered user';
    return;
}

// ----共通DB操作系関数
function find ($strSql='', $aryBind=[], $boolFirst=false) {
    global $objDBCA, $root_dir_path;
    $trace = debug_backtrace();

    if (empty($strSql)) {
        return;
    }
    try {
        if (!$objQuery = $objDBCA->sqlPrepare($strSql)) {
            // 例外処理へ
            throw new Exception ('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
        }
        if (!empty($aryBind)) {
            if ($objQuery->sqlBind($aryBind) !== "") {
                // 例外処理へ
                throw new Exception ('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
            }
        }
        if (!$objQuery->sqlExecute()) {
            // 例外処理へ
            throw new Exception ('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        exception("FROM([FILE]{$trace[0]['file']},[FUNCTION]{$trace[0]['function']},[LINE]{$trace[0]['line']}) TO({$message})");
    }
    if ($boolFirst) {
        $row = $objQuery->resultFetch();
        if (is_array($row) && count($row) === 1) {
          return array_shift($row);
        }
        return $row;
    }
    $aryResults = [];
    while ($aryRow = $objQuery->resultFetch()) {
        $aryResults[] = $aryRow;
    }
    return $aryResults;
}

function query ($strSql='', $aryBind=[], $strTablename='', $strAction='') {
    $trace = debug_backtrace();
    if (empty($strSql)) {
        return;
    }
    if (empty($strTablename) || empty($strAction)) {
        backendQuery($strSql, $aryBind);
        return;
    }
    if (!in_array($strAction, ['INSERT','UPDATE','L_DELETE','L_REVIVE'])) {
        return;
    }
    $now = find('SELECT now(6)', null, true);
    if (in_array('NOW()', $aryBind) || in_array('now()', $aryBind)) {
        foreach ($aryBind as $k=>$v) {
            if ($v === 'now()' || $v === 'NOW()') {
                $aryBind[$k] = $now;
            }
        }
    }
    if (!isset($aryBind['LAST_UPDATE_TIMESTAMP']) || empty($aryBind['LAST_UPDATE_TIMESTAMP'])) {
        $aryBind['LAST_UPDATE_TIMESTAMP'] = $now;
    }
    $SEQ = find("SELECT VALUE FROM A_SEQUENCE WHERE NAME = :NAME LIMIT 1 FOR UPDATE", ['NAME' => "SEQ_{$strTablename}"], true);
    $JSEQ = find("SELECT VALUE FROM A_SEQUENCE WHERE NAME = :NAME LIMIT 1 FOR UPDATE", ['NAME' => "JSEQ_{$strTablename}"], true);
    if (in_array('SEQ()', $aryBind) || in_array('seq()', $aryBind)) {
        foreach ($aryBind as $k=>$v) {
            if ($v === 'SEQ()' || $v === 'seq()') {
                $aryBind[$k] = $SEQ;
            }
        }
    }
    $aryFields = find("SHOW FIELDS FROM `{$strTablename}`");
    $aryTableinfo = ['fields' => [], 'pkey' => ''];
    foreach ($aryFields as $row) {
        $aryTableInfo['fields'][] = $row['Field'];
        if ($row['Key'] === 'PRI') {
            $aryTableInfo['pkey'] = $row['Field'];
        }
    }
    $strJnlSql = "INSERT INTO {$strTablename}_JNL SELECT :JOURNAL_SEQ_NO as JOURNAL_SEQ_NO, :JOURNAL_REG_DATETIME as JOURNAL_REG_DATETIME, :JOURNAL_ACTION_CLASS as JOURNAL_ACTION_CLASS, ".implode(", ", $aryTableInfo['fields'])." FROM {$strTablename} WHERE ".$aryTableInfo['pkey']." = :PKEY ";
    if ($strAction === 'INSERT') {
        backendQuery("UPDATE A_SEQUENCE SET VALUE = :SEQ WHERE NAME = :NAME", ['SEQ' => $SEQ + 1, 'NAME' => "SEQ_{$strTablename}"]);
        $aryBind[$aryTableInfo['pkey']]  = $SEQ;
    }
    $aryJnlBind = ['JOURNAL_SEQ_NO'       => $JSEQ,
                   'JOURNAL_REG_DATETIME' => $now,
                   'JOURNAL_ACTION_CLASS' => $strAction,
                   'PKEY'                 => $aryBind[$aryTableInfo['pkey']],
                  ];
    //error_log('backendQuery('.$strSql.','.print_r($aryBind, true).')');
    backendQuery($strSql,$aryBind);
    backendQuery($strJnlSql, $aryJnlBind);
    backendQuery("UPDATE A_SEQUENCE SET VALUE = :JSEQ WHERE NAME = :NAME", ['JSEQ' => $JSEQ + 1, 'NAME' => "JSEQ_{$strTablename}"]);
    return $SEQ;
}

function backendQuery ($strSql='', $aryBind=[]) {
    global $objDBCA;
    $trace = debug_backtrace();
    if (empty($strSql)) {
        return;
    }
    try {
        if (!$objQuery = $objDBCA->sqlPrepare($strSql)) {
            // 例外処理へ
            throw new Exception ('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
        }
        if (!empty($aryBind)) {
            if ($objQuery->sqlBind($aryBind) !== "") {
                // 例外処理へ
                throw new Exception ('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
            }
        }
        if (!$objQuery->sqlExecute()) {
            // 例外処理へ
            throw new Exception ('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
        }
    } catch (Exception $e) {
        // 例外処理
        $message = $e->getMessage();
        $objDBCA->transactionRollBack();
        exception("FROM([FILE]{$trace[1]['file']},[FUNCTION]{$trace[1]['function']},[LINE]{$trace[1]['line']}) "
                 ."TO([FILE]{$trace[0]['file']},[FUNCTION]{$trace[0]['function']},[LINE]{$trace[0]['line']}) "
                 ."ON({$message})");
    }
}

function transactionStart() {
    global $objDBCA;
    $trace = debug_backtrace();

    try {
        if (!$objDBCA->transactionStart()) {
            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
        }
    } catch (Exception $e) {
        // 例外処理
        $message = $e->getMessage();
        $objDBCA->transactionRollBack();
        exception("FROM([FILE]{$trace[0]['file']},[FUNCTION]{$trace[0]['function']},[LINE]{$trace[0]['line']}) {$message}");
    }
}

function transactionCommit() {
    global $objDBCA;
    $trace = debug_backtrace();

    try {
        if (!$objDBCA->transactionCommit()) {
            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[FUNCTION]'.__FUNCTION__.',[LINE]'.__LINE__);
        }
    } catch (Exception $e) {
        // 例外処理
        $objDBCA->transactionRollBack();
        exception("FROM([FILE]{$trace[0]['file']},[FUNCTION]{$trace[0]['function']},[LINE]{$trace[0]['line']})");
    }
}

/**
  * 共通exception
  */
function exception ($message) {
    web_log("ERROR:UNEXPECTED {$message}");

    // 想定外エラー通知画面にリダイレクト
    webRequestForceQuitFromEveryWhere(500,10210101);
    exit();
}
// 共通DB操作系関数----

// ----エラーページ表示
function error_notice ($strErrorMsg = null) {
    global $g;

    // HTTP Response Code
    http_response_code(401);

    // ルートディレクトリを取得
    $root_dir_path = $g['root_dir_path'];
    if (empty($root_dir_path)) {
        $root_dir_temp = [];
        $root_dir_temp = explode('ita-root', dirname(__FILE__));
        $root_dir_path = "{$root_dir_temp[0]}ita-root";
    }
    $aryOrderToReqGate = [];
    $aryOrderToReqGate['DBConnect'] = 'LATE';
    require $root_dir_path."/libs/commonlibs/common_php_req_gate.php";

    $title_name = 'error notice';
    $design_type = 'default';
    //include $root_dir_path."/libs/webcommonlibs/web_parts_html_statement.php";
    //echo $strMsg;

    // 管理者連絡先を読み込み
    $ADMIN_OFFICE = file_get_contents($root_dir_path."/confs/webconfs/admin_mail_addr.txt");
    $strMailTag = "";
    if (!empty($ADMIN_OFFICE)) {
        $strMailTag = $objMTS->getSomeMessage("ITAWDCH-MNU-4010003",$ADMIN_OFFICE);
    }
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_favicon_ico=filemtime("$root_dir_path/webroot/common/imgs/favicon.ico");
    $title_name = $objMTS->getSomeMessage("ITAWDCH-MNU-1230001");
    $design_type = 'default';
    // ここから本文
    ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language" content="ja">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta http-equiv="content-style-type" content="text/css">
    <link rel="shortcut icon" href="<?= $scheme_n_authority ?>/common/imgs/favicon.ico?<?= $timeStamp_favicon_ico ?>" type="image/vnd.microsoft.icon">
    <title><?= $objMTS->getSomeMessage("ITAWDCH-MNU-4010001") ?></title>
</head>
<body>

    <br>
    <?= $objMTS->getSomeMessage("ITAWDCH-MNU-4010002") ?><br>
    <?= $strMailTag ?><br>

    <?php if ($strErrorMsg) { ?>

    <br>
    <?= $strErrorMsg ?>

    <?php } ?>

</body>
</html>
<?php
exit;
}
// エラーページ表示----
