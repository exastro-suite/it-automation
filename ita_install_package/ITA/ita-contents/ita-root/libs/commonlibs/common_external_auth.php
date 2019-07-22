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
//    ・AD連携（外部認証）用ファンクション
//
//////////////////////////////////////////////////////////////////////

function enableActiveDirectorySync($filePath) {

    // ファイル有無チェック
    $ret = file_exists($filePath);
    if($ret === false) {
        return false;
    }

    // 外部認証設定パース
    $aryExternalAuthSettings = @parse_ini_file($filePath, true);
    if($aryExternalAuthSettings === false) {
        return false;
    }

    // パースしたデータが存在するか
    $ret = empty($aryExternalAuthSettings);
    if($ret === true) {
        return false;
    }

    return true;
}

function externalAuthForBackyard($aryExternalAuthSettings) {

    $user = $aryExternalAuthSettings['Replication_Connect']['ConnectionUser'];
    $password = $aryExternalAuthSettings['Replication_Connect']['UserPassword'];

    $ldapconn = externalAuthCommon($aryExternalAuthSettings, $user, $password, "backyardLog");

    return $ldapconn;
}

function externalAuthForWeb(&$aryExternalAuthSettings, $user, $password) {

    $ldapconn = externalAuthCommon($aryExternalAuthSettings, $user, $password, "web_log");

    if($ldapconn !== false) {

        $principal = $user . "@" . $aryExternalAuthSettings['successdDcInfo']['domain'];

        $filter = "(&(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2))(userPrincipalName=$principal))";
        $attribute = array("dn");
        $searchResult = ldap_search($ldapconn, $aryExternalAuthSettings['successdDcInfo']['basedn'], $filter, $attribute) ;

        /* 「ldap_search」の結果から、エントリを取得する */
        $result = ldap_get_entries($ldapconn , $searchResult);

        if($result['count'] !== 1) {
            web_log("Not get only one account info. (not found or more than one)");
            return false;
        }
    }

    return $ldapconn;
}

function externalAuthCommon(&$aryExternalAuthSettings, $user, $password, $logFunction) {

    foreach($aryExternalAuthSettings['targetDomainControllers'] as $tgtDc) {
        // LDAP認証(外部認証)処理を実行するActiveDirectoryサーバのホスト情報:(IPv4)
        $ldapHost             = $tgtDc['host'];
        // LDAP認証(外部認証)処理を実行するActiveDirectoryサーバのLDAPポート
        $ldapPort             = $tgtDc['port'];
        // LDAP認証(外部認証)処理を実行するActiveDirectoryサーバに対して接続試行をする最大回数
        $ldapConnectTryCount  = $tgtDc['reconnection_count'];
        // LDAP認証(外部認証)処理を実行するActiveDirectoryサーバに対して接続待機をする最大時間
        $ldapConnectTimeLimit = $tgtDc['connect_timelimit'];
        // LDAP認証(外部認証)処理を実行するActiveDirectoryサーバに対して接続待機をする最大時間
        $ldapSearchTimeLimit  = $tgtDc['search_timelimit'];

        /* バインド時に使用するプロトコルバージョンの情報 */
        if(array_key_exists("connect_protocolversion", $tgtDc) === true &&
            integerNumericValidator($tgtDc['connect_protocolversion'])) {
            $ldapProtocolVersion = $tgtDc['connect_protocolversion'];
        } else {
            $ldapProtocolVersion = 3;
            $tgtDc['connect_protocolversion'] = 3;
        }

        $ldapconn = @ldap_connect($ldapHost, $ldapPort);
        if($ldapconn == false){
            /* host:port の構文エラー */
            $logFunction("Error: Setting value is invalid.");
            continue;
        }

        /* LDAP サーバから返される照会 (referral) 情報への自動従順設定（※ActiveDirectoryでは原則的にfalseを設定する） */
        $ret = @ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, false);
        if($ret === false) {
            $logFunction("Setting error. (referral): " . ldap_error($ldapconn));
            continue;
        }

        /* 使用するプロトコルバージョンを設定する */
        $ret = @ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, $ldapProtocolVersion);
        if($ret === false) {
            $logFunction("Setting error. (protocol_version): " . ldap_error($ldapconn));
            continue;
        }

        /* ADサーバでのLDAP最大検索待機時間(秒単位)を設定する。0はADでの検索時間を制限しないことを意味する。初期値は「0」 */
        if($ldapSearchTimeLimit > 0) {
            $ret = @ldap_set_option($ldapconn, LDAP_OPT_TIMELIMIT, $ldapSearchTimeLimit);
            if($ret === false) {
                $logFunction("Setting error. (search_timelimit): " . ldap_error($ldapconn));
                continue;
            }
        }

        /* ADサーバ接続時のタイムアウトまでの最大待ち時間を設定する。指定された値が最大待ち時間(秒単位) となる。 */
        if($ldapConnectTimeLimit > 0) {
            $ret = @ldap_set_option($ldapconn, LDAP_OPT_NETWORK_TIMEOUT, $ldapConnectTimeLimit);
            if($ret === false) {
                $logFunction("Setting error. (connect_timelimit): " . ldap_error($ldapconn));
                continue;
            }
        }

        $userprincipalname = $user . "@" . $tgtDc['domain'];

        // connect実行(tryCountまで繰り返す)
        for($count = 0; $count < $ldapConnectTryCount; $count++){
            /* LDAPに接続する */
            $ldapbind = @ldap_bind($ldapconn, $userprincipalname, $password);
            if($ldapbind === false){
                /* 認証エラー */
                $logFunction("Error: Unexpected, Detail: Authentication failed. (" . ldap_error($ldapconn) . "[" . ldap_errno($ldapconn) . "])");

                if(ldap_errno($ldapconn) === 49){
                    /* ID/Pass error */
                    return false;
                }else{
                    /* その他 error */
                    continue;
                }
            } else {
                // 接続に成功したドメインコントローラ情報を保持する
                $aryExternalAuthSettings['successdDcInfo'] = $tgtDc;
                return $ldapconn;
            }
        }
    }

    // 全ての接続に失敗するとここに来る
    $logFunction("Error: Connection failed to all domain controllers.");
    return false;
}

function integerNumericValidator($value) {

    if(is_numeric($value) === false || strpos($value, ".") === true) {
        return false;
    }

    return true;
}

// 特別ユーザ判定
function isSpecialUser($userId, $arySpecialUsers) {

    // Administrator
    if($userId == 1) {
        return true;
    }

    // ExternalAuthSettingsで指定の特別ユーザ
    if(in_array($userId, $arySpecialUsers) === true) {
        return true;
    }

    // バックヤード系のユーザ
    if($userId < 0) {
        return true;
    }

    return false;
}

// 特別ロール判定
function isSpecialRole($roleId, $arySpecialRoles) {

    // 管理者
    if($roleId == 1) {
        return true;
    }

    // ExternalAuthSettingsで指定の特別ロール
    if(in_array($roleId, $arySpecialRoles) === true) {
        return true;
    }

    if($roleId < 0) {
        return true;
    }

    return false;
}
