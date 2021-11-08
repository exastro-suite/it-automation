<?php
//   Copyright 2021 NEC Corporation
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

/**
* バックヤード共通関数まとめ
*/

/********************************************
* シーケンス系
********************************************/

/**
* シーケンスの更新
*
* @param  string  $sequenceName      シーケンス名
* @param  int     $requestSequenceID 更新したい値
* @return boolean                    更新できたかどうか
*/
function updateSequenceIDForBackyards($sequenceName, $requestSequenceID=NULL){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $sql = "UPDATE
                A_SEQUENCE
            SET
                VALUE = :VALUE,
                LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE
                NAME = :NAME";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    // 指定IDがなければA_SEQUENCEから取得する
    if ($requestSequenceID == NULL) {
        $requestSequenceID = getSequenceID($sequenceName) + 1;
    }

    $objDBCA->setQueryTime();
    $res = $objQuery->sqlBind(
        array(
            "VALUE"                 => $requestSequenceID,
            "LAST_UPDATE_TIMESTAMP" => $objDBCA->getQueryTime(),
            "NAME"                  => $sequenceName,
        )
    );
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    return true;
}

/**
* シーケンスIDの取得
*
* @param  string $sequenceName シーケンス名
* @return string $result       シーケンスID
*/
function  getSequenceIDForBackyards($sequenceName){
    global $objDBCA, $objMTS;
    $result = array();

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900014',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $sql = "SELECT
                VALUE
            FROM
                A_SEQUENCE
            WHERE
                NAME = :NAME
            ";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array('NAME' => $sequenceName));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    while ($row = $objQuery->resultFetch()) {
        $result = $row["VALUE"];
    }

    return $result;
}


/********************************************
* service系
********************************************/
/**
 * 処理済みフラグをクリアする
 *
 * @param  string  $procName フラグを0にしたいサービス名
 * @return boolean           実行できたかどうか
 */
function claerExecFlg($procName){
    global $objDBCA, $objMTS;

    $sql = "UPDATE
                A_PROC_LOADED_LIST
            SET
                LOADED_FLG = 0,
                LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE
                PROC_NAME = :PROC_NAME";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $objDBCA->setQueryTime();
    $res = $objQuery->sqlBind(
        array(
            "LAST_UPDATE_TIMESTAMP" => $objDBCA->getQueryTime(),
            "PROC_NAME"             => $procName,
        )
    );
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    return true;
}

/**
 * 処理済みフラグを処理済みにする
 *
 * @param  string  $procName フラグを0にしたいサービス名
 * @return boolean           実行できたかどうか
 */
function setExecFlg($procName){
    global $objDBCA, $objMTS;

    $sql = "UPDATE
                A_PROC_LOADED_LIST
            SET
                LOADED_FLG = 1,
                LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE
                PROC_NAME = :PROC_NAME";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $objDBCA->setQueryTime();
    $res = $objQuery->sqlBind(
        array(
            "LAST_UPDATE_TIMESTAMP" => $objDBCA->getQueryTime(),
            "PROC_NAME"             => $procName,
        )
    );
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    return true;
}


/********************************************
* DB接続
********************************************/
/**
 * DB接続情報を取得する
 *
 * @return   array    $retAry    DB接続情報
 */
function getDbConnectParams(){
    global $g, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900012',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $path = ROOT_DIR_PATH . '/confs/commonconfs/db_connection_string.txt';
    $tmp = file_get_contents($path);
    $tmp = ky_decrypt($tmp);
    $tmpAry = explode(';', $tmp);
    $retAry = array();
    foreach ($tmpAry as $param) {
        if (strpos($param, 'dbname') === false) {
            $retAry['host'] = str_replace('host=', '', $param);
        } else {
            $retAry['dbname'] = str_replace('mysql:dbname=', '', $param);
        }
    }

    $tmp = ROOT_DIR_PATH . '/confs/commonconfs/db_username.txt';
    $retAry['user'] = ky_decrypt(file_get_contents($tmp));
    $tmp = ROOT_DIR_PATH . '/confs/commonconfs/db_password.txt';
    $retAry['password'] = ky_decrypt(file_get_contents($tmp));

    return $retAry;
}


/********************************************
* ZIPファイル操作
********************************************/
/**
 * ディレクトリ配下をzipに固める
 *
 * @param  string  $dirPath     zipにするディレクトリのある個所
 * @param  string  $dstPath     zipを保管する場所+ファイル名
 * @return boolean              実行できたかどうか
 */
function zip($dirPath, $dstPath, $zipPath=null) {
    $result = false;
    $res = exec("cd {$dirPath} && zip -r {$dstPath} {$zipPath}");
    // $res = exec("cd {$dirPath} && zip -r {$dstPath} {$zipPath}");
    if ($res == 0) {
        $result = true;
    }

    return $result;
}

/**
 * 対象パスにあるzipを解凍する
 *
 * @param  string  $zipPath     zipの場所
 * @param  string  $dstPath     解凍したzipを展開する場所
 * @param  string  $zipPath     ここ配下を固める
 * @return boolean              実行できたかどうか
 */
function unZip($zipPath, $dstName){
    $result = false;
    $res = exec("unzip -r {$dstName} {$zipPath}");
    if ($res == 0) {
        $result = true;
    }
    return $result;
}

/********************************************
* 検索系
********************************************/
/**
 * メニューIDからメニューグループIDを検索
 *
 * @param  string  $menuId         メニューID
 * @return string  $result         メニューグループID
 */
function getMenuGroupIdByMenuId($menuId){
    global $objDBCA, $objMTS;

    $sql = "SELECT
                MENU_GROUP_ID
            FROM
                A_MENU_LIST
            WHERE
                MENU_ID = :MENU_ID
            AND
                DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(
        array(
            "MENU_ID" => $menuId,
        )
    );

    $resObj = $objQuery->sqlExecute();
    if ($resObj === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $result = array();
    while ($row = $objQuery->resultFetch()) {
        $result = $row["MENU_GROUP_ID"];
    }

    return $result;
}

/**
 * メニュー情報
 *
 * @param  string  $menuId         メニューID
 * @return string  $result         メニューグループID
 */
function getMenuInfoByMenuId($menuId){
    global $objDBCA, $objMTS;

    $sql = "SELECT
                MENU_NAME, A_MENU_LIST.MENU_GROUP_ID, MENU_GROUP_NAME
            FROM
                A_MENU_LIST
            LEFT OUTER JOIN
                A_MENU_GROUP_LIST
            ON
                A_MENU_LIST.MENU_GROUP_ID = A_MENU_GROUP_LIST.MENU_GROUP_ID
            WHERE
                A_MENU_LIST.MENU_ID = :MENU_ID
            AND
                A_MENU_LIST.DISUSE_FLAG = 0
            AND
                A_MENU_GROUP_LIST.DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(
        array(
            "MENU_ID" => $menuId,
        )
    );

    $resObj = $objQuery->sqlExecute();
    if ($resObj === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    while ($row = $objQuery->resultFetch()) {
        $result = $row;
    }
    if (empty($result)) {
        $result = false;
    }
    return $result;
}

/********************************************
* 権限判定
********************************************/
/**
 * メニューIDとユーザIDから権限情報を取得する
 *
 * @param  string  $menuId         メニューID
 * @param  string  $userId         ユーザID
 * @return string  $result         メニューグループID
 */
function getPrivilegeAuthByUserId($menuId, $userId){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $sql = "SELECT
                PRIVILEGE
            FROM
                A_ACCOUNT_LIST
            LEFT OUTER JOIN
                A_ROLE_ACCOUNT_LINK_LIST
            ON
                A_ACCOUNT_LIST.USER_ID = A_ROLE_ACCOUNT_LINK_LIST.USER_ID
            LEFT OUTER JOIN
                A_ROLE_MENU_LINK_LIST
            ON
                A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID = A_ROLE_MENU_LINK_LIST.ROLE_ID
            LEFT OUTER JOIN
                A_MENU_LIST
            ON
                A_MENU_LIST.MENU_ID = A_ROLE_MENU_LINK_LIST.MENU_ID
            WHERE
                A_ACCOUNT_LIST.USER_ID = :USER_ID
            AND
                A_ROLE_MENU_LINK_LIST.MENU_ID = :MENU_ID
            AND
                A_ACCOUNT_LIST.DISUSE_FLAG = 0
            AND
                A_ROLE_ACCOUNT_LINK_LIST.DISUSE_FLAG = 0
            AND
                A_ROLE_MENU_LINK_LIST.DISUSE_FLAG = 0
            AND
                A_MENU_LIST.DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);

    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(
        array(
            "USER_ID" => $userId,
            "MENU_ID" => $menuId
        )
    );

    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $result = "";
    while ($row = $objQuery->resultFetch()) {
        $result = $row["PRIVILEGE"];
    }

    if (empty($result)) {
        $result = false;
    }

    return $result;
}

/**
 * メニューIDとユーザIDから権限情報を取得する
 *
 * @param  string  $userId         ユーザID
 * @return string  $result         メニューグループID
 */
function getUserRole($userId){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $sql);
    }

    $sql = "SELECT
                MENU_GROUP_ID
            FROM
                A_ACCOUNT_LIST
            LEFT OUTER JOIN
                A_ROLE_ACCOUNT_LINK_LIST
            ON
                A_ACCOUNT_LIST.USER_ID = A_ROLE_ACCOUNT_LINK_LIST.USER_ID
            LEFT OUTER JOIN
                A_ROLE_MENU_LINK_LIST
            ON
                A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID = A_ROLE_MENU_LINK_LIST.ROLE_ID
            WHERE
                USER_ID = :USER_ID
            AND
                DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(
        array(
            "USER_ID" => $userId,
        )
    );

    $resObj = $objQuery->sqlExecute();
    if ($resObj === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $result = array();
    while ($row = $objQuery->resultFetch()) {
        $result = $row["MENU_GROUP_ID"];
    }

    return $result;
}

/**
 * ユーザIDからユーザ名を取得する
 *
 * @param  string  $userId         ユーザID
 * @return string  $userName       ユーザ名
 */
function getUserName($userId) {
    global $objDBCA, $objMTS;

    $sql = "SELECT
                USERNAME_JP
            FROM
                A_ACCOUNT_LIST
            WHERE
                USER_ID = :USER_ID
            AND
                DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage(
            'ITABASEH-ERR-900054',
            array(basename(__FILE__), __LINE__)
        ));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $res = $objQuery->sqlBind(
        array(
            "USER_ID" => $userId,
        )
    );

    $resObj = $objQuery->sqlExecute();
    if ($resObj === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage(
            'ITABASEH-ERR-900054',
            array(basename(__FILE__), __LINE__)
        ));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $result = array();
    while ($row = $objQuery->resultFetch()) {
        $result = $row["USERNAME_JP"];
    }

    return $result;
}