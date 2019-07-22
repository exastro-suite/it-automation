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
//  【概要】
//      ActiveDirectory ロール/ユーザ同期君
//
//////////////////////////////////////////////////////////////////////

// 起動しているshellの起動判定を正常にするための待ち時間
sleep(1);

////////////////////////////////
// ルートディレクトリを取得   //
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

////////////////////////////////
// $log_output_dirを取得      //
////////////////////////////////
$log_output_dir     = getenv('LOG_DIR');

/* 何らかの理由で[getenv('LOG_DIR')]に値がない場合は、次のステップで直接『$log_output_dir』の値を指定 */
if($log_output_dir == "") {
    $log_output_dir = $root_dir_path . "/logs/backyardlogs/";
}

////////////////////////////////
// $log_file_prefixを作成     //
////////////////////////////////
$log_file_prefix    = basename(__FILE__, ".php") . "_";

////////////////////////////////
// $log_levelを取得           //
////////////////////////////////
$log_level = getenv("LOG_LEVEL"); // 'DEBUG';

////////////////////////////////
// PHPエラー時のログ出力先設定//
////////////////////////////////
$tmpVarTimeStamp    = time();
$logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd", $tmpVarTimeStamp) . ".log";
ini_set("display_errors", "stderr");
ini_set("log_errors",     1);
ini_set("error_log",      $logfile);

////////////////////////////////
// 定数定義                   //
////////////////////////////////
$log_output_php     = "/libs/backyardlibs/backyard_log_output.php";
$php_req_gate_php   = "/libs/commonlibs/common_php_req_gate.php";
$db_connect_php     = "/libs/commonlibs/common_db_connect.php";

$db_access_user_id  = -100031;

$adSyncLib_dir_path = $root_dir_path . "/libs/backyardlibs/ita_base/activedirectory_synchronization/";
require_once($adSyncLib_dir_path . "ExternalAuthSettingsDefinition.php");
$adSyncTableDef_dir_path = $adSyncLib_dir_path . "table_definition/";
require_once($adSyncTableDef_dir_path . "BaseModel.php");
require_once($adSyncTableDef_dir_path . "AAccountListModel.php");
require_once($adSyncTableDef_dir_path . "AAdGroupJudgementModel.php");
require_once($adSyncTableDef_dir_path . "AAdUserJudgementModel.php");
require_once($adSyncTableDef_dir_path . "ARoleAccountLinkListModel.php");
require_once($adSyncTableDef_dir_path . "ARoleListModel.php");
require_once($adSyncTableDef_dir_path . "DAccountListModel.php");
require_once($adSyncTableDef_dir_path . "DRoleListModel.php");
require_once($root_dir_path . "/libs/commonlibs/common_external_auth.php");

/* 外部認証設定ファイルの所定ディレクトリパスを変数に格納 */
$strExternalAuthSettingsFilename = ExternalAuthSettingsDefinition::getFilePath();

////////////////////////////////
// ローカル変数(全体)宣言     //
////////////////////////////////
$warning_flag       = 0; // 警告フラグ(1：警告発生)
$error_flag         = 0; // 異常フラグ(1：異常発生)

$ldapconn = null;
$objDBCA = null;
try {
    // 開始メッセージ
    if($log_level === "DEBUG") {
        $FREE_LOG = "Start procedure.";
        require($root_dir_path . $log_output_php);
    }

    ////////////////////////////////////////////////////////////////
    // EternalAuthSettings.iniから必要なデータを取得
    ////////////////////////////////////////////////////////////////
    // ファイル無ければ終わる
    if(enableActiveDirectorySync($strExternalAuthSettingsFilename) === false) {
        if($log_level === "DEBUG") {
            $FREE_LOG = "No file(" . ExternalAuthSettingsDefinition::getFileName() . "). Don't work ActiveDirectory cooperation.";
            require ($root_dir_path . $log_output_php);
            $FREE_LOG = "End procedure (normal)";
            require($root_dir_path . $log_output_php);
        }
        exit(0);
    }

    // あれば設定ファイル読み込み
    $errorMessage = ""; // エラーあればメッセージを格納する
    $aryExternalAuthSettings = ExternalAuthSettingsDefinition::parse($strExternalAuthSettingsFilename, $errorMessage);
    if($aryExternalAuthSettings === false) {
        $warning_flag = 1;
        /* 外部認証設定パースエラー */
        throw new Exception($errorMessage . "FILE:" . __FILE__ . " LINE:" . __LINE__);
    }

    ////////////////////////////////////////////////////////////////
    // 特別ユーザID/特別ロールIDリスト化
    ////////////////////////////////////////////////////////////////
    $strSpecialUsers = $aryExternalAuthSettings['LocalAuthUserId']['IdList'];
    $arySpecialUsers = explode(",", $strSpecialUsers);

    $strSpecialRoles = $aryExternalAuthSettings['LocalRoleId']['IdList'];
    $arySpecialRoles = explode(",", $strSpecialRoles);

    ////////////////////////////////////////////////////////////////
    // AD認証(ユーザ/ロールのリストとってこれるユーザ)
    ////////////////////////////////////////////////////////////////
    $ldapconn = externalAuthForBackyard($aryExternalAuthSettings);
    if($ldapconn == false) {
        $warning_flag = 1;
        throw new Exception("Connection failed.");
    }
    $baseDn = $aryExternalAuthSettings['Replication_Connect']['basedn'];

    $groupListFromAD = getGroupSyncData($ldapconn, $baseDn);
    $groupDnToInfo = makeGroupDnToInfo($groupListFromAD);
    $userListFromAD = getUserSyncData($ldapconn, $baseDn, $groupDnToInfo);
    $excludedGroupListFromAD = getGroupNotSyncData($ldapconn, $baseDn);

    ////////////////////////////////
    // 共通モジュールの呼び出し   //
    ////////////////////////////////
    $aryOrderToReqGate = array('DBConnect'=>'LATE');
    require($root_dir_path . $php_req_gate_php);

    ////////////////////////////////
    // DBコネクト                 //
    ////////////////////////////////
    require($root_dir_path . $db_connect_php);

    // トレースメッセージ
    if($log_level === "DEBUG") {
        $FREE_LOG = "DB Connect Completed.";
        require ($root_dir_path . $log_output_php );
    }

    ////////////////////////////////
    // トランザクション開始       //
    ////////////////////////////////
    if($objDBCA->transactionStart() === false) {
        $error_flag = 1;
        throw new Exception("Start transaction has failed.") ;
    }

    // トレースメッセージ
    if($log_level === "DEBUG") {
        $FREE_LOG = "[Process] Start transaction.";
        require ($root_dir_path . $log_output_php );
    }

    ///////////////////////////////////////////////////
    // 関連シーケンスをロックする                    //
    ///////////////////////////////////////////////////
    //----デッドロック防止のために、昇順でロック
    $aryTgtOfSequenceLock = array(
        "SEQ_A_ACCOUNT_LIST",
        "JSEQ_A_ACCOUNT_LIST",
        "SEQ_A_ROLE_LIST",
        "JSEQ_A_ROLE_LIST",
        "SEQ_A_ROLE_ACCOUNT_LINK_LIST",
        "JSEQ_A_ROLE_ACCOUNT_LINK_LIST",
    );

    // キーと値の関係を維持しつつ、値を基準に、昇順で並べ替える
    asort($aryTgtOfSequenceLock);

    foreach($aryTgtOfSequenceLock as $strSeqName) {

        $retArray = getSequenceLockInTrz($strSeqName, "A_SEQUENCE");
        if($retArray[1] != 0) {
            $error_flag = 1;
            throw new Exception("Lock sequence has failed. SequenceName: " . $strSeqName);
        }
    }
    //デッドロック防止のために、昇順でロック----

    ////////////////////////////////////////////////////////////////
    // 同期処理
    ////////////////////////////////////////////////////////////////
    //  ユーザー情報連携
    accoutListSync($userListFromAD, $arySpecialUsers);

    //  ロール情報連携
    roleListSync($groupListFromAD, $arySpecialRoles);

    //  ロールユーザー連携
    roleAccountLinkListSync($userListFromAD, $excludedGroupListFromAD, $arySpecialUsers, $arySpecialRoles);

    ////////////////////////////////////////////////////////////////
    // コミット(レコードロックを解除)                             //
    ////////////////////////////////////////////////////////////////
    $r = $objDBCA->transactionCommit();
    if(!$r) {
        $error_flag = 1;
        throw new Exception("Commit has failed.");
    }

    // トレースメッセージ
    if($log_level === "DEBUG") {
        $FREE_LOG = "[Process] Commit.";
        require ($root_dir_path . $log_output_php );
    }

    ////////////////////////////////
    // トランザクション終了       //
    ////////////////////////////////
    $objDBCA->transactionExit();

    // トレースメッセージ
    if($log_level === "DEBUG") {
        $FREE_LOG = "[Process] Transaction completed.";
        require($root_dir_path . $log_output_php);
    }

} catch(Exception $e) {

    $FREE_LOG = "An exception occurred.";
    require($root_dir_path . $log_output_php);

    // 例外メッセージ出力
    $FREE_LOG = $e->getMessage();
    require($root_dir_path . $log_output_php);

    // DBアクセス事後処理
    if(isset($objQuery)   ) unset($objQuery);
    if(isset($objQueryUtn)) unset($objQueryUtn);
    if(isset($objQueryJnl)) unset($objQueryJnl);

    // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
    // 念のためロールバック
    if(empty($objDBCA) == false && $objDBCA->getTransactionMode()) {
        // ロールバック
        if($objDBCA->transactionRollBack() === true) {
            $FREE_LOG = "[Process] Rollback.";
        } else {
            $FREE_LOG = "Rollback has failed.";
        }
        require($root_dir_path . $log_output_php);
    }

} finally {

    // ADへのコネクションが残っていたら解放する
    if($ldapconn != null) {
        ldap_close($ldapconn);
    }
}

////////////////////////////////
//// 結果出力               ////
////////////////////////////////
// 処理結果コードを判定してアクセスログを出し分ける
if($error_flag != 0) {
    if($log_level === "DEBUG") {
        $FREE_LOG = "End procedure (error)";
        require($root_dir_path . $log_output_php);
    }
    exit(2);

} else if($warning_flag != 0) {
    if($log_level === "DEBUG") {
        $FREE_LOG = "End procedure (warning)";
        require($root_dir_path . $log_output_php);
    }
    exit(2);

} else {
    if($log_level === "DEBUG") {
        $FREE_LOG = "End procedure (normal)";
        require($root_dir_path . $log_output_php);
    }
    exit(0);
}

// main logic end ---

function makeGroupDnToInfo($groupListFromAD) {

    $result = array();

    foreach($groupListFromAD as $groupData) {
        $result[$groupData['dn']]['objectsid'] = $groupData['objectsid'];
        $result[$groupData['dn']]['samaccountname'] = $groupData['samaccountname'];
    }

    return $result;
}

/*****
 * 連携データ取得(ユーザ)
 *
 **/
function getUserSyncData($ldapconn, $baseDn, $groupDnToInfo) {

    global $log_level;

    $filter = "(&(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
    $attribute = array("objectsid", "userprincipalname", "displayname", "mail", "memberof", "dn");
    $searchResult = ldap_search($ldapconn, $baseDn, $filter, $attribute) ;

    /* 「ldap_search」の結果から、エントリを取得する */
    ldap_sort($ldapconn , $searchResult , "userprincipalname"); // 検索結果をソート
    $result = ldap_get_entries($ldapconn , $searchResult);

    $userData = array();
    for($u = 0; $u < $result['count']; $u++) {
        // userprincipalnameが存在しないユーザは連携対象外(ログインできない)
        if(array_key_exists("userprincipalname", $result[$u]) === false) {
            if($log_level === "DEBUG") {
                backyardLog("Ignore replicate user. Cause: 'userprincipalname' is not found." . $result[$u]['dn']);
            }
            continue;
        }

        $data = array();
        $data['uniqueUserName'] = strstr($result[$u]['userprincipalname'][0], "@", true);
        $data['objectsid']      = bin2strSID($result[$u]['objectsid'][0]);

        if(array_key_exists("displayname", $result[$u]) === true) {
            $data['displayname']    = $result[$u]['displayname'][0];
        } else {
            $data['displayname']    = $data['uniqueUserName'];
        }

        if(array_key_exists("mail", $result[$u]) === true) {
            $data['mail']    = $result[$u]['mail'][0];
        } else {
            $data['mail']    = "dummy_from_activedirectory@xxx.bbb.ccc";
        }

        $groups = array();
        if(array_key_exists("memberof", $result[$u])) {
            for($g = 0; $g < $result[$u]['memberof']['count']; $g++) {
                $groupDn = $result[$u]['memberof'][$g];
                if(array_key_exists($groupDn, $groupDnToInfo) === true) {
                    $groupInfo = $groupDnToInfo[$groupDn];
                    $groups[] = $groupInfo;
                } else{
                    if($log_level === "DEBUG") {
                        backyardLog("Ignore user's group. ($groupDn) Cause: Not march replicated group.");
                    }
                }
            }
        }
        $data['memberof'] = $groups;
        $userData[] = $data;
    }

    return $userData ;
}

/*****
 * 連携データ取得(グループ)
 *
 **/
function getGroupSyncData($ldapconn, $baseDn) {

    // group type の値
    $array_createdBy = array("user" => 0, "system" => 1);
    $array_class = array("security" => -2147483648, /* 配布は除外 "distribution" => 0*/);
    $array_scope = array("global" => 2, "domainLocal" => 4, "universal" => 8);

    $useGroupType = array();
    foreach($array_createdBy as $key => $createdBy) {
        foreach($array_class as $key => $class) {
            foreach($array_scope as $key => $scope) {
                $useGroupType[] = $scope + $class + $createdBy;
            }
        }
    }
    $strGroupTypeFilter = "|";
    foreach($useGroupType as $typeValue) {
        $strGroupTypeFilter .= "(grouptype=$typeValue)";
    }

    $filter = "(&(objectClass=group)($strGroupTypeFilter))";
    $attribute = array("objectsid", "samaccountname", "dn");
    $searchResult = ldap_search($ldapconn, $baseDn, $filter, $attribute) ;

    /* 「ldap_search」の結果から、エントリを取得する */
    ldap_sort($ldapconn , $searchResult , "samaccountname"); // 検索結果をソート
    $result = ldap_get_entries($ldapconn , $searchResult);

    $groupData = array();
    for($g = 0; $g < $result["count"]; $g++) {
        $data = array();
        $data['samaccountname'] = $result[$g]['samaccountname'][0];
        $data['dn'] = $result[$g]['dn'];
        $data['objectsid'] = bin2strSID($result[$g]['objectsid'][0]);
        $groupData[] = $data;
    }

    return $groupData;
}

/*****
 * 連携"外" データ取得(配布グループ)
 *
 **/
function getGroupNotSyncData($ldapconn, $baseDn) {

    // group type の値
    $array_createdBy = array("user" => 0, "system" => 1);
    $array_class = array("distribution" => 0); // 配布グループを除外対象とする
    $array_scope = array("global" => 2, "domainLocal" => 4, "universal" => 8);

    $useGroupType = array();
    foreach($array_createdBy as $key => $createdBy) {
        foreach($array_class as $key => $class) {
            foreach($array_scope as $key => $scope) {
                $useGroupType[] = $scope + $class + $createdBy;
            }
        }
    }
    $strGroupTypeFilter = "|";
    foreach($useGroupType as $typeValue) {
        $strGroupTypeFilter .= "(grouptype=$typeValue)";
    }

    $filter = "(&(objectClass=group)($strGroupTypeFilter))";
    $attribute = array("objectsid");
    $searchResult = ldap_search($ldapconn, $baseDn, $filter, $attribute) ;

    /* 「ldap_search」の結果から、エントリを取得する */
    ldap_sort($ldapconn , $searchResult , "objectsid"); // 検索結果をソート
    $result = ldap_get_entries($ldapconn , $searchResult);

    $excludedGroupData = array();
    for($g = 0; $g < $result["count"]; $g++) {
        $excludedGroupData[] = bin2strSID($result[$g]['objectsid'][0]);
    }

    return $excludedGroupData;
}

/*****
 * SIDデータ変換
 *
 **/
function bin2strSID($binary_SID) {

    $strSID = bin2hex($binary_SID);

    return $strSID;
}

/*****
 * ユーザー情報連携
 *
 **/
function accoutListSync($userListFromAD, $arySpecialUsers) {

    global $log_level;
    global $objDBCA;
    global $db_access_user_id;

    try {
        $user_ids = array();

        //  既存レコード検索
        $select_account = new DAccountListModel($objDBCA, $db_access_user_id);
        $update_account = new AAccountListModel($objDBCA, $db_access_user_id);
        $user_judgement = new AAdUserJudgementModel($objDBCA, $db_access_user_id);

        foreach($userListFromAD as $adUser) {
            $conditions = array("AD_USER_SID = '" . $adUser['objectsid'] . "'");

            $accounts = $select_account->find($conditions, true);
            if(count($accounts) > 0) {
                //  既存レコードがある場合は更新処理
                $data = $accounts[0]; // SID一意のため必ず1レコード

                // ITA上の同一USERNAMEが生きている場合はその重複先を廃止にする(AD側のデータ優先)
                $ret = discardAccountByName($adUser['uniqueUserName'], $data['USER_ID'], $arySpecialUsers);
                if($ret === false) {
                    // 重複先が特別ユーザの場合、廃止に失敗してここ
                    if($log_level === "DEBUG") {
                        backyardLog("Can't replicate. This user is designated as a special user.: " . $adUser['uniqueUserName']);
                    }
                } else {
                    //  更新項目
                    //  ユーザ名、ユーザー名（表示名）もしくはメールアドレスが既存データと食い違いがあったら更新
                    if(!isSpecialUser($data['USER_ID'], $arySpecialUsers) &&
                        (
                            $data['DISUSE_FLAG']    === "1" ||
                            $data['USERNAME']       != $adUser['uniqueUserName'] ||
                            $data['USERNAME_JP']    != $adUser['displayname'] ||
                            $data['MAIL_ADDRESS']   != $adUser['mail']
                        )
                    ) {
                        $data['USERNAME']           = $adUser['uniqueUserName'];
                        $data['USERNAME_JP']        = $adUser['displayname'];
                        $data['MAIL_ADDRESS']       = $adUser['mail'];
                        $data['DISUSE_FLAG']        = "0";

                        $update_account->updateRow($data);
                    }

                    // 使用ユーザIDを退避
                    $user_id = $data['USER_ID'];
                    $user_ids[] = $user_id;
                }

            } else {
                //  既存レコードが存在しない場合には新規登録
                // ITA上の同一USERNAMEが生きている場合はその重複先を廃止にする(AD側のデータ優先)
                $ret = discardAccountByName($adUser['uniqueUserName'], null, $arySpecialUsers);
                if($ret === false) {
                    // 重複先が特別ユーザの場合、廃止に失敗してここ
                    if($log_level === "DEBUG") {
                        backyardLog("Can not replicate. This user is designated as a special user.: " . $adUser['uniqueUserName']);
                    }
                } else {
                    $data['USERNAME']               = $adUser['uniqueUserName'];
                    $data['PASSWORD']               = md5(uniqid());
                    $data['USERNAME_JP']            = $adUser['displayname'];
                    $data['MAIL_ADDRESS']           = $adUser['mail'];
                    $data['PW_LAST_UPDATE_TIME']    = "9999/12/31 23:59:59";
                    $data['DISUSE_FLAG']            = "0";

                    // 使用ユーザIDを退避
                    $user_id = $update_account->insertRow($data);
                    $user_ids[] = $user_id;

                    $judgeData['AD_USER_SID']       = $adUser['objectsid'];
                    $judgeData['ITA_USER_ID']       = $user_id;
                    $judgeData['DISUSE_FLAG']       = "0";
                    $user_judgement->insertRow($judgeData);
                }
            }
        }

        // 削除対応
        $conditions = empty($user_ids) ? null : array("USER_ID NOT IN (" . implode(",", $user_ids) . ")");
        $delete_records = $select_account->find($conditions);

        foreach((array)$delete_records as $delete_target) {

            // 特別ユーザーを削除対象から除外
            if(isSpecialUser($delete_target['USER_ID'], $arySpecialUsers) === true) {
                if($log_level === "DEBUG") {
                    backyardLog("Do not discard. This user is designated as a special user.: " . $delete_target['USERNAME'] . "[" . $delete_target['USER_ID'] . "]");
                }
                continue;
            }

            $conditions = array("USER_ID = " . $delete_target['USER_ID']);
            $accounts = $select_account->find($conditions);
            $data = $accounts[0]; // ID指定なので必ず1レコード

            $data['DISUSE_FLAG']                = "1";
            $update_account->updateRow($data);

            if(isset($data['USER_JUDGE_ID']) === true) {
                $user_judgement->updateRow($data);
            }
        }
    } catch(Exception $e) {
        throw new Exception("Error occurred. :" . $e->getMessage());
    }
}

function discardAccountByName($userName, $selfUserId, $arySpecialUsers) {

    global $objDBCA;
    global $db_access_user_id;

    try {
        $select_account = new DAccountListModel($objDBCA, $db_access_user_id);
        $update_account = new AAccountListModel($objDBCA, $db_access_user_id);
        $user_judgement = new AAdUserJudgementModel($objDBCA, $db_access_user_id);

        $conditions = array();
        $conditions[] = "USERNAME = '" . $userName . "'";
        if(isset($selfUserId) === true) {
            $conditions[] = "USER_ID <> " . $selfUserId;
        }
        $accounts = $select_account->find($conditions);

        if(count($accounts) > 0) {
            $discardAccount = $accounts[0]; // ユニーク制約により有効なアカウントは1つである

            if(isSpecialUser($discardAccount['USER_ID'], $arySpecialUsers) === true) {
                // 特別ユーザだった場合はfalse返して廃止しない
                return false;
            }

            $discardAccount['DISUSE_FLAG'] = "1";

            $update_account->updateRow($discardAccount);

            if(isset($discardAccount['USER_JUDGE_ID']) === true) {
                $user_judgement->updateRow($discardAccount);
            }
        }
    } catch(Exception $e) {
        throw new Exception("Error occurred. :" . $e->getMessage());
    }

    return true;
}

/*****
 * ロール情報連携
 *
 **/
function roleListSync($groupListFromAD, $arySpecialRoles) {

    global $log_level;
    global $objDBCA;
    global $db_access_user_id;

    try {
        $role_ids = array();

        //  既存レコード検索
        $select_role = new DRoleListModel($objDBCA, $db_access_user_id);
        $update_role = new ARoleListModel($objDBCA, $db_access_user_id);
        $group_judgement = new AAdGroupJudgementModel($objDBCA, $db_access_user_id);

        foreach($groupListFromAD as $adGroup) {
            $conditions = array("AD_GROUP_SID = '" . $adGroup['objectsid'] . "'");

            $roles = $select_role->find($conditions, true);
            if(count($roles) > 0) {
                //  既存レコードがある場合は更新処理
                $data = $roles[0]; // SID一意のため必ず1レコード

                // ITA上の同一ROLE_NAMEが生きている場合はその重複先を廃止にする(AD側のデータ優先)
                $ret = discardRoleByName($adGroup['samaccountname'], $data['ROLE_ID'], $arySpecialRoles);
                if($ret === false) {
                    // 重複先が特別ロールの場合、廃止に失敗してここ
                    if($log_level === "DEBUG") {
                        backyardLog("Can't replicate. This role is designated as a special role.: " . $adGroup['samaccountname']);
                    }
                } else {
                    //  更新項目
                    //  グループ名が既存データと食い違いがあったら更新可否フラグを立てる。
                    if(isSpecialRole($data['ROLE_ID'], $arySpecialRoles) === false &&
                        (
                            $data['DISUSE_FLAG']    === "1" ||
                            $data['ROLE_NAME']      != $adGroup['samaccountname']
                        )
                    ) {
                        $data['ROLE_NAME']          = $adGroup['samaccountname'];
                        $data['DISUSE_FLAG']        = "0";

                        $update_role->updateRow($data);
                    }

                    // 使用ロールIDを退避
                    $role_id = $data['ROLE_ID'];
                    $role_ids[] = $role_id;
                }
            } else {
                //  既存レコードが存在しない場合には新規登録

                // ITA上の同一ROLE_NAMEが生きている場合はその重複先を廃止にする(AD側のデータ優先)
                $ret = discardRoleByName($adGroup['samaccountname'], null, $arySpecialRoles);
                if($ret === false) {
                    // 重複先が特別ロールの場合ここ
                    if($log_level === "DEBUG") {
                        backyardLog("Can not replicate. This role is designated as a special role.: " . $adGroup['samaccountname']);
                    }
                } else {
                    $data['ROLE_NAME']              = $adGroup['samaccountname'];
                    $data['DISUSE_FLAG']            = "0";

                    // 使用ロールIDを退避
                    $role_id = $update_role->insertRow($data);
                    $role_ids[] = $role_id;

                    $judgeData['AD_GROUP_SID']      = $adGroup['objectsid'];
                    $judgeData['ITA_ROLE_ID']       = $role_id;
                    $judgeData['DISUSE_FLAG']       = "0";
                    $group_judgement->insertRow($judgeData);
                }
            }
        }

        // 削除対応
        $conditions = empty($role_ids) ? null : array("ROLE_ID NOT IN (" . implode(",", $role_ids) . ")");
        $delete_records = $select_role->find($conditions);

        foreach((array)$delete_records as $delete_target) {

            // 特別ロールを削除対象から除外
            if(isSpecialRole($delete_target['ROLE_ID'], $arySpecialRoles) === true) {
                if($log_level === "DEBUG") {
                    backyardLog("Do not discard. This role is designated as a special role.: " . $delete_target['ROLE_NAME'] . "[" . $delete_target['ROLE_ID'] . "]");
                }
                continue;
            }

            $conditions = array("ROLE_ID = " . $delete_target['ROLE_ID']);
            $roles = $select_role->find($conditions);
            $data = $roles[0]; // ID指定なので必ず1レコード

            $data['DISUSE_FLAG']                = "1";
            $update_role->updateRow($data);

            if(isset($data['ROLE_JUDGE_ID']) === true) {
                $group_judgement->updateRow($data);
            }
        }
    } catch(Exception $e) {
        throw new Exception("Error occurred. :" . $e->getMessage());
    }
}

function discardRoleByName($roleName, $selfRoleId, $arySpecialRoles) {

    global $objDBCA;
    global $db_access_user_id;

    try {
        $select_role = new DRoleListModel($objDBCA, $db_access_user_id);
        $update_role = new ARoleListModel($objDBCA, $db_access_user_id);
        $group_judgement = new AAdGroupJudgementModel($objDBCA, $db_access_user_id);

        $conditions = array();
        $conditions[] = "ROLE_NAME = '" . $roleName . "'";
        if(isset($selfRoleId) === true) {
            $conditions[] = "ROLE_ID <> " . $selfRoleId;
        }
        $roles = $select_role->find($conditions);

        if(count($roles) > 0) {
            $discardRole = $roles[0]; // ユニーク制約により有効なアカウントは1つである

            if(isSpecialRole($discardRole['ROLE_ID'], $arySpecialRoles) === true) {
                // 特別ロールだった場合はfalse返して廃止しない
                return false;
            }

            $discardRole['DISUSE_FLAG'] = "1";

            $update_role->updateRow($discardRole);

            if(isset($discardRole['ROLE_JUDGE_ID']) === true) {
                $group_judgement->updateRow($discardRole);
            }
        }
    } catch(Exception $e) {
        throw new Exception("Error occurred. :" . $e->getMessage());
    }
}

/*****
 * ロールユーザー連携
 *
 **/
function roleAccountLinkListSync($userListFromAD, $excludedGroupListFromAD, $arySpecialUsers, $arySpecialRoles) {

    global $log_level;
    global $objDBCA;
    global $db_access_user_id;

    try {
        $link_ids = array();

        foreach($userListFromAD as $adUser) {
            //  ユーザー情報検索
            $account = new DAccountListModel($objDBCA, $db_access_user_id);
            $conditions = array("AD_USER_SID = '" . $adUser['objectsid'] . "'");

            $accounts = $account->find($conditions, true); // 廃止含む

            if(count($accounts) < 1) {
                // AD上に存在するが、ITA上の既存のユーザが特別ユーザのため登録されていない場合の対応
                $conditions = array("USERNAME = '" . $adUser['uniqueUserName'] . "'");
                $accounts_checktarget = $account->find($conditions, true); // 廃止含む

                $isSpecialUserFlag = false;
                foreach($accounts_checktarget as $target) {
                    if(isSpecialUser($target['USER_ID'], $arySpecialUsers)) {
                        $isSpecialUserFlag = true;
                        break;
                    }
                }
                if($isSpecialUserFlag === true) {
                    // 特別ユーザにかかわるロールユーザ紐付けは更新対象外
                    if($log_level === "DEBUG") {
                        backyardLog("Can not replicate. This user is designated as a special user.: " . $adUser['uniqueUserName']);
                    }
                    continue;
                } else {
                    // 特別ユーザでもなく登録されていないユーザが存在する場合はエラー
                    throw new Exception("User is not found.: " . $adUser['uniqueUserName']);
                }
            }

            // 特別ユーザに関わるロールユーザ紐付けは更新対象外
            if(isSpecialUser($accounts[0]['USER_ID'], $arySpecialUsers) === true) {
                if($log_level === "DEBUG") {
                    backyardLog("Can not replicate. This user is designated as a special user.: " . $adUser['uniqueUserName']);
                }
                continue;
            }

            // ユーザが廃止されている紐付けは更新対象外
            if($accounts[0]['DISUSE_FLAG'] === "1") {
                if($log_level === "DEBUG") {
                    backyardLog("Do not replicate. This user is discard.: " . $adUser['uniqueUserName']);
                }
                continue;
            }

            foreach($adUser['memberof'] as $groupInfo) {

                // AD上の配布グループは除外
                if(in_array($groupInfo['objectsid'], $excludedGroupListFromAD) === true) {
                    continue;
                }

                //  ロール情報検索
                $role = new DRoleListModel($objDBCA, $db_access_user_id);
                $conditions = array("AD_GROUP_SID = '" . $groupInfo['objectsid'] . "'");

                $roles = $role->find($conditions, true); // 廃止含む

                if(count($roles) < 1) {
                    // AD上に存在するが、ITA上の既存のロールが特別ロールのため登録されていない場合の対応
                    $conditions = array("ROLE_NAME = '" . $groupInfo['samaccountname'] . "'");
                    $roles_checktarget = $role->find($conditions, true); // 廃止含む

                    $isSpecialRoleFlag = false;
                    foreach($roles_checktarget as $target) {
                        if(isSpecialRole($target['ROLE_ID'], $arySpecialRoles)) {
                            $isSpecialRoleFlag = true;
                            break;
                        }
                    }
                    if($isSpecialRoleFlag === true) {
                        // 特別ロールにかかわるロールユーザ紐付けは更新対象外
                        if($log_level === "DEBUG") {
                            backyardLog("Can not replicate. This role is designated as a special role.: " . $groupInfo['samaccountname']);
                        }
                        continue;
                    } else {
                        // 特別ロールでもなく登録されていないロールが存在する場合はエラー
                        throw new Exception("Role is not found.: " . $groupInfo['samaccountname']);
                    }
                }

                // 特別ロールに関わるロールユーザ紐付けは更新対象外
                if(isSpecialRole($roles[0]['ROLE_ID'], $arySpecialRoles) === true) {
                    if($log_level === "DEBUG") {
                        backyardLog("Can not replicate. This role is designated as a special role.: " . $groupInfo['samaccountname']);
                    }
                    continue;
                }

                // ロールが廃止されている紐付けは更新対象外
                if($roles[0]['DISUSE_FLAG'] === "1") {
                    if($log_level === "DEBUG") {
                        backyardLog("Do not replicate. This role is discard.: " . $groupInfo['samaccountname']);
                    }
                    continue;
                }

                //  既存レコード検索
                $role_account_link = new ARoleAccountLinkListModel($objDBCA, $db_access_user_id);

                $conditions = array(
                    "ROLE_ID = " . $roles[0]['ROLE_ID'],
                    "USER_ID = " . $accounts[0]['USER_ID']
                );

                $role_account_links = $role_account_link->find($conditions, true);
                if(count($role_account_links) > 0) {
                    $data = $role_account_links[0];

                    if ($data['DISUSE_FLAG'] == "1" && 
                        isSpecialUser($data['USER_ID'], $arySpecialUsers) === false && 
                        isSpecialRole($data['ROLE_ID'], $arySpecialRoles) === false) {
                        $data['DISUSE_FLAG'] = "0";

                        $role_account_link->updateRow($data);
                    }
                    $link_id = $data['LINK_ID'];
                    $link_ids[] = $link_id;
                } else {
                    //  既存レコードが存在しない場合には新規登録
                    $data['ROLE_ID'] = $roles[0]['ROLE_ID'];
                    $data['USER_ID'] = $accounts[0]['USER_ID'];
                    $data['DISUSE_FLAG'] = "0";

                    $link_id = $role_account_link->insertRow($data);
                    $link_ids[] = $link_id;
                    $journal_action_class = "INSERT";
                }
            }
        }

        /**
         * 特別ユーザー/特別ロール連携レコードを削除対象から除外
         */
        $linkList = new ARoleAccountLinkListModel($objDBCA, $db_access_user_id);

        //  削除対象link_idを取得
        $conditions = empty($link_ids) ? null : array("LINK_ID NOT IN (" . implode(", ", $link_ids) . ")");
        $deleteLists = $linkList->find($conditions);
        foreach((array)$deleteLists as $deleteRow) {

            // 特別ユーザは除外
            if(isSpecialUser($deleteRow['USER_ID'], $arySpecialUsers) === true) {
                continue;
            }

            // 特別ロールは除外
            if(isSpecialRole($deleteRow['ROLE_ID'], $arySpecialRoles) === true) {
                continue;
            }

            $data = $deleteRow;
            $data['DISUSE_FLAG'] = "1";

            $linkList->updateRow($data) ;
        }
    } catch(Exception $e) {
        throw new Exception("Error occurred.: " . $e->getMessage());
    }
}

function backyardLog($message, $needTrace = false) {

    global $log_output_dir;
    global $log_file_prefix;
    global $root_dir_path;
    global $log_output_php;

    ob_start(); 
    debug_print_backtrace(); 
    $trace = ob_get_contents(); 
    ob_end_clean(); 

    // Remove first item from backtrace as it's this function which is redundant. 
    $trace = preg_replace('/^#0\s+' . __FUNCTION__ . "[^\n]*\scalled at/", '', $trace, 1);

    // Renumber backtrace items. 
    $trace = preg_replace('/^#(\d+)/me', '\'#\' . ($1 - 1)', $trace); 

    if($needTrace === false) {
        $trace = preg_replace('/#(\d+)\s.*/', '', $trace);
        $trace = preg_replace('/^\n/m', '', $trace);
        $trace = preg_replace('/\n+/m', '', $trace);
    }

    $FREE_LOG = $message . $trace;
    require ($root_dir_path . $log_output_php);
}
