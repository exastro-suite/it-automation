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
 * 【概要】
 *  テーブルに登録されたデータインポートのタスクを実行する
 *
 */

if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}


define('ROOT_DIR_PATH',        $root_dir_path);
define('LOG_LEVEL',            getenv('LOG_LEVEL'));
define('LAST_UPDATE_USER',     -100326); // データポータビリティプロシージャ
define('STATUS_RUNNING',       2); // 実行中
define('STATUS_PROCESSED',     3); // 完了
define('STATUS_FAILURE',       4); // 完了(異常) 
define('LOG_PREFIX',           basename( __FILE__, '.php' ) . '_');
define('LOG_DIR',              '/logs/backyardlogs/');

// ER作成対象外メニュー
$execlusionMenuIdAry = array('2100000306', '2100180003');


try {
    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_php_req_gate.php';
    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_db_connect.php';
    require_once ROOT_DIR_PATH . '/libs/backyardlibs/ita_base/common_data_portability.php';
    require_once ROOT_DIR_PATH . '/libs/webcommonlibs/web_functions_for_menu_info.php';
    require_once ROOT_DIR_PATH . '/libs/webcommonlibs/web_php_functions.php';
    require_once ROOT_DIR_PATH . '/libs/webcommonlibs/web_parts_for_request_init.php';

    $execFlg = false;

    if (LOG_LEVEL === 'DEBUG') {
        // 処理開始ログ
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900003'));
    }
    // DB接続情報取得
    $paramAry = getDbConnectParams();
    define('DB_USER',   $paramAry['user']);
    define('DB_PW',     "'".preg_replace("/'/", "'\"'\"'", $paramAry['password'])."'");
    define('DB_HOST',   $paramAry['host']);
    define('DB_NAME',   $paramAry['dbname']);

    $execFlg = false;

    $recordAry = array();

    // 未実行のレコードを取得する
    $recordAry = getUnexecutedRecord();

    if (is_array($recordAry) === true) {
    } else {
        throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
    }

    if (count($recordAry) > 0) {
        $execFlg = true;
        $res = setExecFlg();
        ////////////////////////////////
        // トランザクション開始       //
        ////////////////////////////////
        if( $objDBCA->transactionStart()===false ){
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAANSIBLEH-ERR-50003",array(__FILE__,__LINE__,"00001000")) );
        }

        ///////////////////////////////
        // ER図情報を作成する
        ///////////////////////////////
        // ER図情報取得
        $erInfo = getERDiagram();

        // 前のデータ削除
        $deleted = deleteERData();
        if ( $deleted == false ) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
            outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
            $res = $objDBCA->transactionRollback();
            if ($res === false) {
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900036',
                                                              array(basename(__FILE__), __LINE__)));
                throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
            }
            throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
        }

        // 新しくデータを生成
        $registed = registERData($erInfo);
        if ( $registed == false )  {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
            outputLog(LOG_PREFIX, "Command=[{$cmd}],Error=[" . print_r($output, true) . "].");
            $res = $objDBCA->transactionRollback();
            if ($res === false) {
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900036',
                                                              array(basename(__FILE__), __LINE__)));
                throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
            }
            throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
        }

        $res = $objDBCA->transactionCommit();
        if ( $res === false ) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900036',
                                                          array(basename(__FILE__), __LINE__)));
            throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
        }
    }

} catch (Exception $e) {
    claerExecFlg();
    outputLog(LOG_PREFIX, $e->getMessage());
}

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


/**
 * ER図用情報を取得する
 *
 * @return   array    $erInfo    ER図の表示に必要な情報
 */
function getERDiagram(){
    global $objDBCA, $execlusionMenuIdAry;

    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_getInfo_LoadTable.php';

    $erInfo = array();

    // 取得可能なメニュー情報
    $menuGroupListInfo = getMenuGroupInfo();

    foreach ($menuGroupListInfo as $menuGroupKey => $menuGroupInfo) {
        $menuGroupId   = strval($menuGroupInfo["MENU_GROUP_ID"]);     // メニューグループID
        $menuGroupName = $menuGroupInfo["MENU_GROUP_NAME"];           // メニューグループ名
        $menuList      = $menuGroupInfo["MENU_LIST"];                 // メニュー一覧

        $loadTableInfo = NULL;
        $shapedMenuInfo = array();
        foreach ($menuList as $menuKey => $menuInfo) {
            $menuId         = sprintf("%010d", ($menuInfo["MENU_ID"]));     // メニューID
            $menuName       = $menuInfo["MENU_NAME"];                       // メニュー名
            $dispSeq        = $menuInfo["DISP_SEQ"];                        // 表示順
            $tmpMenuInfo = array(
                "DISP_SEQ" => $dispSeq,
                "NAME"     => $menuInfo["MENU_NAME"]
            );

            // ER作成対象外の場合はスキップ
            if(in_array($menuId, $execlusionMenuIdAry)){
                continue;
            }

            $getInfoOfLoadTableER = getInfoOfLoadTableForER($menuId);

            if ( !empty($getInfoOfLoadTableER["COLUMNS"]) ) {
                $mergedMenuInfoArray  = array_merge($tmpMenuInfo, $getInfoOfLoadTableER);
                $shapedMenuInfo[]     = $mergedMenuInfoArray;
            }
        }

        // 整形したInfo
        $shapedMenuGroupInfo = array(
            "ID" => $menuGroupId,
            "NAME" => $menuGroupName,
            "MENU" => $shapedMenuInfo
        );

        // 返り値に詰め込んでいく
        $erInfo["MENU_GROUP"][] = $shapedMenuGroupInfo;
    }
    return $erInfo;
}

/**
 * メニューグループ情報の取得
 *
 * @return   array    $retAry    DB接続情報
 */
function getMenuGroupInfo(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900014',
                                                      array(basename(__FILE__), __LINE__)));
    }

    // メニューグループ一覧の取得
    $sql  = 'SELECT A_MENU_LIST.MENU_GROUP_ID AS MENU_GROUP_ID, MENU_GROUP_NAME
             FROM A_MENU_LIST
             LEFT OUTER JOIN A_MENU_GROUP_LIST
             ON A_MENU_GROUP_LIST.MENU_GROUP_ID = A_MENU_LIST.MENU_GROUP_ID
             WHERE A_MENU_LIST.DISUSE_FLAG = 0
             AND A_MENU_GROUP_LIST.DISUSE_FLAG = 0
             GROUP BY MENU_GROUP_ID';

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
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $menuGroupInfo = array();
    while ($row = $objQuery->resultFetch()) {
        // メニューグループ/メニュー一覧の取得
        $sql  = 'SELECT A_MENU_LIST.MENU_ID AS MENU_ID, A_MENU_LIST.MENU_NAME AS MENU_NAME
                 FROM A_MENU_LIST
                 LEFT OUTER JOIN A_MENU_GROUP_LIST
                 ON A_MENU_GROUP_LIST.MENU_GROUP_ID = A_MENU_LIST.MENU_GROUP_ID
                 WHERE A_MENU_LIST.MENU_GROUP_ID = :MENU_GROUP_ID
                 AND A_MENU_LIST.DISUSE_FLAG = 0
                 AND A_MENU_GROUP_LIST.DISUSE_FLAG = 0
                 ORDER BY A_MENU_LIST.DISP_SEQ, A_MENU_LIST.MENU_ID DESC';
        if (LOG_LEVEL === 'DEBUG') {
            outputLog(LOG_PREFIX, $sql);
        }

        $_objQuery = $objDBCA->sqlPrepare($sql);
        if ($_objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $_objQuery->getLastError());
            return false;
        }
        $_res = $_objQuery->sqlBind(array('MENU_GROUP_ID' => $row["MENU_GROUP_ID"]));
        $_res = $_objQuery->sqlExecute();
        if ($_res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $_objQuery->getLastError());
            return false;
        }

        $tmpMenuArray = array();
        $i = 1;
        while ($_row = $_objQuery->resultFetch()) {
            $tmpMenuArray[] = array(
                "MENU_ID"   => $_row["MENU_ID"],
                "MENU_NAME" => $_row["MENU_NAME"],
                "DISP_SEQ"  => $i,
            );
            $i = $i + 1;
        }
        $tmpMenuGroupArray = array(
            "MENU_GROUP_ID"   => $row["MENU_GROUP_ID"],
            "MENU_GROUP_NAME" => $row["MENU_GROUP_NAME"],
            "MENU_LIST"       => $tmpMenuArray
        );
        $menuGroupInfo[] = $tmpMenuGroupArray;
    }

    return $menuGroupInfo;
}

/**
 * ER用のメニュー情報を取得
 *
 * @return   array    $aryResult    メニュー情報
 */
function getCreatedMenuInfo(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900014',
                                                      array(basename(__FILE__), __LINE__)));
    }

    // メニューグループ一覧の取得
    $sql  = 'SELECT CREATE_MENU_ID
             FROM F_CREATE_MENU_INFO
             WHERE DISUSE_FLAG = 0';

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
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    while ($row = $objQuery->resultFetch()) {
        $aryResult[] = selectMenuInfo($row["CREATE_MENU_ID"]);
    }

    return $aryResult;
}

/**
 * ER情報再生成のため、バックヤードで作成したテーブル情報をクリーニング
 * （ユーザが作成したものは消さない）
 *
 * @return   boolean    削除できたかどうか
 */
function deleteERData(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    // B_ER_DATAのレコード残削除
    $sql = "DELETE FROM B_ER_DATA WHERE LAST_UPDATE_USER = :LAST_UPDATE_USER";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array('LAST_UPDATE_USER' => LAST_UPDATE_USER));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $row_id = getSequence('B_ER_DATA_RIC');
    $result = updateSequence(array('name' => 'B_ER_DATA_RIC', 'value' => '1'));
    if ( $result == false ) {
        return false;
    }


    // B_ER_MENU_TABLE_LINK_LISTのレコード残削除
    $sql = "DELETE FROM B_ER_MENU_TABLE_LINK_LIST WHERE LAST_UPDATE_USER = :LAST_UPDATE_USER";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array('LAST_UPDATE_USER' => LAST_UPDATE_USER));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $row_id = getSequence('B_ER_MENU_TABLE_LINK_LIST_RIC');
    $result = updateSequence(array('name' => 'B_ER_MENU_TABLE_LINK_LIST_RIC', 'value' => '1'));
    if ( $result == false ) {
        return false;
    }

    return true;
}

/**
 * ER情報を登録
 *
 * @param    array    $erBindAry    ER登録用にまとめた情報
 * @return   boolean                登録できたかどうか
 */
function insertERData($erBindAry){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $erBindAry = raplaceColumnName($erBindAry);

    $sql = "INSERT INTO B_ER_DATA (
                ROW_ID,
                MENU_TABLE_LINK_ID,
                COLUMN_ID,
                COLUMN_TYPE,
                PARENT_COLUMN_ID,
                PHYSICAL_NAME,
                LOGICAL_NAME,
                RELATION_TABLE_NAME,
                RELATION_COLUMN_ID,
                DISP_SEQ,
                DISUSE_FLAG,
                LAST_UPDATE_USER,
                LAST_UPDATE_TIMESTAMP
            ) VALUES (
                :ROW_ID,
                :MENU_TABLE_LINK_ID,
                :COLUMN_ID,
                :COLUMN_TYPE,
                :PARENT_COLUMN_ID,
                :PHYSICAL_NAME,
                :LOGICAL_NAME,
                :RELATION_TABLE_NAME,
                :RELATION_COLUMN_ID,
                :DISP_SEQ,
                :DISUSE_FLAG,
                :LAST_UPDATE_USER,
                :LAST_UPDATE_TIMESTAMP
            )";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind($erBindAry);
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    // シーケンスのアップデート
    $sequenceInfo = array(
        "name"  => "B_ER_DATA_RIC",
        "value" => $erBindAry["ROW_ID"] + 1,
    );
    $updated = updateSequence($sequenceInfo);

    if ($updated == false) {
        return false;
    }

    return true;

}

/**
 * ER用にメニューリストを作成
 *
 * @param    array    $erMenuBindAry    ER用メニュー情報
 * @return   boolean                    登録できたかどうか
 */
function insertERMenu($erMenuBindAry){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $sql = "INSERT INTO B_ER_MENU_TABLE_LINK_LIST (
                ROW_ID,
                MENU_ID,
                TABLE_NAME,
                VIEW_TABLE_NAME,
                DISUSE_FLAG,
                LAST_UPDATE_USER,
                LAST_UPDATE_TIMESTAMP
            ) VALUES (
                :ROW_ID,
                :MENU_ID,
                :TABLE_NAME,
                :VIEW_TABLE_NAME,
                :DISUSE_FLAG,
                :LAST_UPDATE_USER,
                :LAST_UPDATE_TIMESTAMP
            )";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind($erMenuBindAry);
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    // シーケンスのアップデート
    $sequenceInfo = array(
        "name"  => "B_ER_MENU_TABLE_LINK_LIST_RIC",
        "value" => $erMenuBindAry["ROW_ID"] + 1,
    );
    $updated = updateSequence($sequenceInfo);

    if ($updated == false) {
        return false;
    }

    return true;

}

/**
 * B_ER_MENU_TABLE_LINK_LISTから該当IDを取得する
 *
 * @param    str    $menuId    メニューID
 * @return   int    $result    該当ROW_ID
 */
function getMenuTableLinkId($menuId){
    global $objDBCA, $objMTS;

    $result = "";

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $sql = "SELECT ROW_ID
            FROM B_ER_MENU_TABLE_LINK_LIST
            WHERE MENU_ID = :MENU_ID";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array("MENU_ID" => $menuId));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    while ($row = $objQuery->resultFetch()) {
        $result = $row["ROW_ID"];
    }

    return $result;
}

/**
 * ユーザ入力分のデータとシーケンスが被らない値を探す
 *
 * @param    str      $sequenceTableName    テーブル名
 *           str      $sequenceName         シーケンス名
 * @return   int      $row_id               次に入力すべきシーケンス
 */
function getNextSequence($sequenceTableName, $sequenceName){
    global $objDBCA, $objMTS;

    $row_id = getSequence($sequenceName);

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    // 被ってはいけないシーケンスリスト
    $NGList = getNGSequenceList($sequenceTableName);

    if (!in_array($row_id, $NGList)) {
        return $row_id;
    }

    while (in_array($row_id, $NGList)) {
        $row_id++;
    }

    return $row_id;
}

/**
 * 入力不可なシーケンス番号の取得
 *
 * @param    str      $sequenceTableName    テーブル名
 * @return   array    $result               すでに値の入っているシーケンス番号
 */
function getNGSequenceList($sequenceTableName){
    global $objDBCA, $objMTS;

    $result = array();

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $primaryKey = getPrimarykey($sequenceTableName);

    $sql = "SELECT $primaryKey
            FROM $sequenceTableName
            WHERE LAST_UPDATE_USER != :LAST_UPDATE_USER";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array("LAST_UPDATE_USER" => LAST_UPDATE_USER));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    while ($row = $objQuery->resultFetch()) {
        $result[] = $row["$primaryKey"];
    }

    return $result;
}

/**
 * 入力不可なレコード取得
 *
 * @param    str      $sequenceTableName    テーブル名
 * @return   array    $result               すでに値の入っているシーケンス番号
 */
function getNGList($menuId){
    global $objDBCA, $objMTS;

    $result = array();

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    //  $sql = "SELECT MENU_ID, TABLE_NAME, VIEW_TABLE_NAME, COLUMN_ID, COLUMN_TYPE
    $sql = "SELECT COLUMN_ID, COLUMN_TYPE
            FROM B_ER_DATA
            LEFT OUTER JOIN B_ER_MENU_TABLE_LINK_LIST
            ON B_ER_DATA.MENU_TABLE_LINK_ID = B_ER_MENU_TABLE_LINK_LIST.ROW_ID
            WHERE B_ER_DATA.LAST_UPDATE_USER != :LAST_UPDATE_USER
            AND MENU_ID = :MENU_ID
            AND B_ER_MENU_TABLE_LINK_LIST.DISUSE_FLAG = 0
            AND B_ER_DATA.DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                          array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(array("LAST_UPDATE_USER" => LAST_UPDATE_USER, "MENU_ID" => $menuId));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    while ($row = $objQuery->resultFetch()) {
        $result[] = $row;
    }

    return $result;
}

/**
 * 主キーのカラム名の取得
 *
 * @param    str    $table_name    テーブル名
 * @return   str    $result        主キーのカラム名
 */
function getPrimarykey($table_name) {
    global $objDBCA, $objMTS;

    $sql = "show index from ".$table_name." where key_name = 'primary'";
    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, "SQL=[$sql].");
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054', array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, "SQL=[$sql].");
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $row = $objQuery->resultFetch();
    return $row['Column_name'];
}

/**
 * MENUとTABLEのリンクをまとめて登録
 *
 * @param    array    $menuGroupsInfo    ER用情報
 * @return   boolean                     登録できたかどうか
 */
function registMenuTableLink($menuGroupsInfo){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    foreach ($menuGroupsInfo as $menuGroupInfo) {
        $menusInfo = $menuGroupInfo["MENU"];

        foreach ($menusInfo as $menuInfo) {
            $menuId        = sprintf("%010d", ($menuInfo["ID"]));
            if ( $menuId != NULL) {
                $tableName     = $menuInfo["TABLE_NAME"];
                $viewTableName = $menuInfo["VIEW_TABLE_NAME"];

                $sql = "INSERT INTO B_ER_MENU_TABLE_LINK_LIST(
                            ROW_ID,
                            MENU_ID,
                            TABLE_NAME,
                            VIEW_TABLE_NAME,
                            DISUSE_FLAG,
                            LAST_UPDATE_USER,
                            LAST_UPDATE_TIMESTAMP
                        ) VALUES (
                            :ROW_ID,
                            :MENU_ID,
                            :TABLE_NAME,
                            :VIEW_TABLE_NAME,
                            :DISUSE_FLAG,
                            :LAST_UPDATE_USER,
                            :LAST_UPDATE_TIMESTAMP
                        )";
                $row_id = getNextSequence("B_ER_MENU_TABLE_LINK_LIST", "B_ER_MENU_TABLE_LINK_LIST_RIC");
                $bindAry = array(
                    "ROW_ID"                => $row_id,
                    "MENU_ID"               => $menuId,
                    "TABLE_NAME"            => $tableName,
                    "VIEW_TABLE_NAME"       => $viewTableName,
                    "DISUSE_FLAG"           => 0,
                    "LAST_UPDATE_USER"      => LAST_UPDATE_USER,
                    "LAST_UPDATE_TIMESTAMP" => date("Y-m-d H:i:s"),
                );
                $objQuery = $objDBCA->sqlPrepare($sql);
                if ($objQuery->getStatus() === false) {
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
                    outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
                    return false;
                }
                $res = $objQuery->sqlBind($bindAry);
                $res = $objQuery->sqlExecute();
                if ($res === false) {
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                                  array(basename(__FILE__), __LINE__)));
                    outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
                    return false;
                }

                $res = updateSequence(array("name" => "B_ER_MENU_TABLE_LINK_LIST_RIC", "value" => $row_id + 1));
                if ($res === false) {
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                                  array(basename(__FILE__), __LINE__)));
                    outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
                    return false;
                }
            }
        }
    }
    return true;
}

/**
 * ER情報をテーブルに登録する処理まとめ
 *
 * @param    array    $erInfo    ER用情報
 * @return   boolean             登録できたかどうか
 */
function registERData($erInfo){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    if ( !array_key_exists("MENU_GROUP", $erInfo) ) {
        return false;
    }

    $menuGroupsInfo    = $erInfo["MENU_GROUP"];

    $sequenceTableName = "B_ER_DATA";
    $sequenceName     = "B_ER_DATA_RIC";
    $res = registMenuTableLink($menuGroupsInfo);
    if ($res == false) {
        return $res;
    }

    foreach ($menuGroupsInfo as $menuGroupInfo) {
        $menusInfo = $menuGroupInfo["MENU"];

        foreach ($menusInfo as $menuInfo) {
            // GROUPの内容をインサート
            $menuId        = sprintf("%010d", ($menuInfo["ID"]));
            $tableName     = $menuInfo["TABLE_NAME"];
            $viewTableName = $menuInfo["VIEW_TABLE_NAME"];
            $groups        = $menuInfo["GROUP"];
            $items         = $menuInfo["ITEM"];

            $existsGroup = !empty($groups);

            $menuTableLinkId = getMenuTableLinkId($menuId);
            if ($menuTableLinkId == false) {
                return false;
            }
            // ITEMの内容をインサート
            foreach ($items as $item_id => $item) {
                // ROW_IDの値を取得
                $row_id = getNextSequence($sequenceTableName, $sequenceName);

                // グループの存在チェック
                $parentColumnId = "";
                if ($existsGroup) {
                    foreach ($groups as $group_id => $group) {
                        $groupsColumns = $group["COLUMNS"];
                        if ( in_array( $item_id, $group["COLUMNS"] ) ) {
                            $parentColumnId = $group_id;
                        }
                    }
                }

                $erBindAry = array(
                    "ROW_ID"                => $row_id,
                    "MENU_TABLE_LINK_ID"    => $menuTableLinkId,
                    "COLUMN_ID"             => $item_id,
                    "COLUMN_TYPE"           => "2",
                    "PARENT_COLUMN_ID"      => $parentColumnId,
                    "PHYSICAL_NAME"         => $item["PHYSICAL_NAME"],
                    'LOGICAL_NAME'          => $item["LOGICAL_NAME"],
                    'RELATION_TABLE_NAME'   => $item["RELATION_TABLE_NAME"],
                    'RELATION_COLUMN_ID'    => $item["RELATION_COLUMN_ID"],
                    "DISP_SEQ"              => $item["DISP_SEQ"],
                    "DISUSE_FLAG"           => 0,
                    'LAST_UPDATE_USER'      => LAST_UPDATE_USER,
                    "LAST_UPDATE_TIMESTAMP" => date("Y-m-d H:i:s"),
                );

                $inserResult = insertERData($erBindAry);
                if ($inserResult == false) {
                    return false;
                }
            }

            if ($existsGroup) {
                foreach ($groups as $group_id => $group) {
                    $row_id = getNextSequence($sequenceTableName, $sequenceName);

                    $erBindAry = array(
                        "ROW_ID"                => $row_id,
                        "MENU_TABLE_LINK_ID"    => $menuTableLinkId,
                        "COLUMN_ID"             => $group_id,
                        "COLUMN_TYPE"           => "1",
                        "PARENT_COLUMN_ID"      => $group["PARENT"],
                        "PHYSICAL_NAME"         => "",
                        "LOGICAL_NAME"          => $group["LOGICAL_NAME"],
                        "RELATION_TABLE_NAME"   => "",
                        "RELATION_COLUMN_ID"    => "",
                        "DISP_SEQ"              => $group["DISP_SEQ"],
                        "DISUSE_FLAG"           => 0,
                        'LAST_UPDATE_USER'      => LAST_UPDATE_USER,
                        "LAST_UPDATE_TIMESTAMP" => date("Y-m-d H:i:s"),
                    );

                    $inserResult = insertERData($erBindAry);
                    if ($inserResult == false) {
                        return false;
                    }
                }
            }
        }
    }
    return true;
}

/**
 * ER用にloadTableを取得
 *
 * @param    str      $strMenuIdNumeric    MENU_ID
 * @return   array    $result              ER図用にカスタムしたloadTable
 */
function getInfoOfLoadTableForER($strMenuIdNumeric,&$aryVariant=array(), &$arySetting=array()){

    $aryValues = array();
    $intErrorType = null;
    $strErrMsg = "";

    $strFxName = __FUNCTION__;

    $registeredKey = "";
    $strLoadTableFullname = "";

    $objTable = null;

    $strHiddenTableMode = false;
    
    $aryInfoOfTable = array();
    $strPageType = "";
    
    $strUTNTableId = "";
    $strJNLTableId = "";
    $strUTNViewId = "";
    $strJNLViewId = "";

    $strUTNRIColumnId = "";
    $strJNLRIColumnId = "";

    // 最終的にリターンする値
    $result = array(
        "ID"       => $strMenuIdNumeric,
        // "NAME"     => array(),
        // "DISP_SEQ" => array(),
        "COLUMNS"  => array(),
        "GROUP"    => array(),
        "ITEM"     => array(),
    );

    try{
        if(file_exists(ROOT_DIR_PATH . "/webconfs/systems/{$strMenuIdNumeric}_loadTable.php")){
            $strLrWebRootToThisPageDir = "/systems/{$strMenuIdNumeric}";
        }
        else if(file_exists(ROOT_DIR_PATH . "/webconfs/sheets/{$strMenuIdNumeric}_loadTable.php")){
            $strLrWebRootToThisPageDir = "/sheets/{$strMenuIdNumeric}";
        }
        else if(file_exists(ROOT_DIR_PATH . "/webconfs/users/{$strMenuIdNumeric}_loadTable.php")){
            $strLrWebRootToThisPageDir = "/users/{$strMenuIdNumeric}";
        }
        else{
            // 例外処理へ
            throw new Exception( '([FUNCTION]' . $strFxName . ',[FILE]' . basename(__FILE__) . ',[LINE]' . __LINE__ . ')\n' .
            'loadTable with menuId[' . $strMenuIdNumeric . '] does not exists.');
        }
        
        if( strlen($registeredKey) == 0 ){
            //----まだ登録されていない
            $strLoadTableFullname = ROOT_DIR_PATH . "/webconfs{$strLrWebRootToThisPageDir}_loadTable.php";
            if( file_exists($strLoadTableFullname)===true ){
                require_once($strLoadTableFullname);
                $registeredKey = $strMenuIdNumeric;
            }
            else{
                // 00_loadTable.phpが存在しない場合 
                $intErrorType = 100;
                throw new Exception( '([FUNCTION]' . $strFxName . ',[FILE]' . basename(__FILE__) . ',[LINE]' . __LINE__ . ')\n' .
                "[" . $strLoadTableFullname . "] not exists");
            }
            //まだ登録されていない----
        }
        if( 0 < strlen($registeredKey) ){
            $objTable = loadTable($registeredKey,$aryVariant,$arySetting);
            if($objTable === null){
                // 00_loadTable.phpの読込失敗
                $intErrorType = 101;
                $strErrMsg = "[" . $strLoadTableFullname . "] Analysis Error";
            }
        }
        if( $objTable !== null ){
            $aryColumns = $objTable->getColumns();
            
            if( is_a($objTable,"TemplateTableForReview")=== true ){
                //----ReView用テーブル
                $strPageType = $objTable->getPageType();
                
                $tmpStrRIColumn = "";
                $tmpStrLockTargetColumn = "";
                foreach($aryColumns as $strColumnId=>$objColumn){
                    if( is_a($objColumn,"RowIdentifyColumn") === true ){
                        $tmpStrRIColumn = $objColumn->getID();
                        continue;
                    }
                    if( is_a($objColumn,"LockTargetColumn") === true ){
                        $tmpStrLockTargetColumn = $objColumn->getID();
                        continue;
                    }
                }
                $strUTNRIColumnId = $tmpStrRIColumn;
                $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();
                
                $strLockTargetColumnId = $tmpStrLockTargetColumn;
                unset($tmpStrRIColumn);
                unset($tmpStrLockTargetColumn);
                
                $aryRequiredColumnId = array(
                    "RowIdentify"    =>$strUTNRIColumnId
                    
                    ,"LockTarget"    =>$strLockTargetColumnId
                    ,"EditStatus"    =>$objTable->getEditStatusColumnID()
                    
                    ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                    ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                    ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                    
                    ,"Note"          =>$objTable->getRequiredNoteColumnID()
                    
                    ,"ApplyUpdate"   =>$objTable->getApplyUpdateColumnID()
                    ,"ApplyUser"     =>$objTable->getApplyUserColumnID()
                    ,"ConfirmUpdate" =>$objTable->getConfirmUpdateColumnID()
                    ,"ConfirmUser"   =>$objTable->getConfirmUserColumnID()
                    
                    ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                    ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                    ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                    ,"JnlSeqNo"      =>$strJNLRIColumnId
                    ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                    ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                );
                
                if( $strPageType == "apply" || $strPageType == "confirm" ){
                    $strUTNTableId = $objTable->getDBMainTableHiddenID();
                    $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                    if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                        $strUTNViewId = $objTable->getDBMainTableBody();
                        $strJNLViewId = $objTable->getDBJournalTableBody();
                        $strHiddenTableMode = true;
                    }
                    else{
                        $strUTNTableId = $objTable->getDBMainTableBody();
                        $strJNLTableId = $objTable->getDBJournalTableBody();
                    }
                }
                else{
                    $strUTNTableId = $objTable->getDBResultTableHiddenID();
                    $strJNLTableId = $objTable->getDBResultJournalTableHiddenID();
                    if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                        $strHiddenTableMode = true;
                    }
                    else{
                        $strUTNTableId = $objTable->getDBResultTableBody();
                        $strJNLTableId = $objTable->getDBResultJournalTableBody();
                    }
                }
                
                $aryInfoOfTable = array("PAGE_TYPE"        =>$strPageType
                                       ,"UTN"              =>array("OBJECT_ID"           =>$strUTNTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strUTNRIColumnId
                                                                  ,"VIEW_ID"             =>$strUTNViewId
                                                                   )
                                       ,"JNL"              =>array("OBJECT_ID"           =>$strJNLTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strJNLRIColumnId
                                                                  ,"VIEW_ID"             =>$strJNLViewId
                                                                   )
                                       ,"UTN_ROW_INDENTIFY"=>$strUTNRIColumnId
                                       ,"JNL_SEQ_NO"       =>$strJNLRIColumnId
                                       ,"REQUIRED_COLUMNS" =>$aryRequiredColumnId
                                        );
                //ReView用テーブル----
            }
            else{
                //----標準テーブル
                $strUTNRIColumnId = $objTable->getRowIdentifyColumnID();
                $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();
                
                $aryRequiredColumnId = array(
                    "RowIdentify"    =>$strUTNRIColumnId
                    ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                    ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                    ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                    
                    ,"Note"          =>$objTable->getRequiredNoteColumnID()

                    ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                    ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                    ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                    ,"JnlSeqNo"      =>$strJNLRIColumnId
                    ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                    ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                
                );
                
                $strUTNTableId = $objTable->getDBMainTableHiddenID();
                $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                    $strUTNViewId = $objTable->getDBMainTableBody();
                    $strJNLViewId = $objTable->getDBJournalTableBody();
                    $strHiddenTableMode = true;
                }
                else{
                    $strUTNTableId = $objTable->getDBMainTableBody();
                    $strJNLTableId = $objTable->getDBJournalTableBody();
                }
                $aryInfoOfTable = array("PAGE_TYPE"        =>$strPageType
                                       ,"UTN"              =>array("OBJECT_ID"           =>$strUTNTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strUTNRIColumnId
                                                                  ,"VIEW_ID"             =>$strUTNViewId
                                                                   )
                                       ,"JNL"              =>array("OBJECT_ID"           =>$strJNLTableId
                                                                  ,"ROW_INDENTIFY_COLUMN"=>$strJNLRIColumnId
                                                                  ,"VIEW_ID"             =>$strJNLViewId
                                                                   )
                                       ,"UTN_ROW_INDENTIFY"=>$strUTNRIColumnId
                                       ,"JNL_SEQ_NO"       =>$strJNLRIColumnId
                                       ,"REQUIRED_COLUMNS" =>$aryRequiredColumnId
                                        );
                //標準テーブル----
            }

            //必須カラムのID----
            
            //----カラムインスタンスの取得
            
            $ColLabelForERList = array();
            // PRIMARY類
            $ColLabelForERList[] = array(
                "PHYSICAL_NAME" => $objTable->getRowIdentifyColumnID(),
                "ITEM"          => $objTable->getRowIdentifyColumnLabel(),
                "DISP_SEQ"      => 1,
            );
            $d  = 2;
            foreach($aryColumns as $strColumnId => $objColumn){
                $boolAddInfo = false;
                if( in_array($strColumnId,$aryRequiredColumnId) === false ){
                    //----必須カラムではない任意カラム
                    if( $strHiddenTableMode === true ){
                        //----VIEWを表示、TABLEを更新させる設定の場合
                        if( $objColumn->isDBColumn() === true && $objColumn->isHiddenMainTableColumn() ){
                            $boolAddInfo = true;
                        }
                        //VIEWを表示、TABLEを更新させる設定の場合----
                    }
                    else{
                        //----TABLEを表示/更新させる設定の場合
                        if( $objColumn->isDBColumn() === true ){
                            $boolAddInfo = true;
                        }
                        //----TABLEを表示/更新させる設定の場合
                    }

                    // 表示項目のみ対象
                    $strLinkFormatterId = "print_table";
                    if ($objColumn->getOutputType($strLinkFormatterId)->isVisible()) {
                        $columnClass = get_class($objColumn);
                        if( strpos($columnClass,'btn') === false && strpos($columnClass,'Btn') === false && strpos($columnClass,'button') === false && strpos($columnClass,'Button') === false){
                            $ColLabelForER = $objColumn->getColLabelForER(true, $strColumnId, $objColumn, $d);
                            $ColLabelForERList[] = $ColLabelForER;
                            $d = $d + 1;
                        }
                    }
                }
            }

            // COLUMN
            $colListArray = array(); // 最終的にメニュー配下で持っているカラムリスト

            // グループのチェック/書き出し
            $g = 1;
            $groupColumnListArray = array(); // グループがある場合、ITEMのIDを入れておく
            $groupListArray       = array(); // グループがある場合、グループのIDを管理する
            $groupDetailListArray = array(); // グループがある場合、グループIDとグループ名の対応リストを入れておく
            $childColListArray    = array();

            $NGList = getNGList($strMenuIdNumeric);

            foreach ($ColLabelForERList as $colsInfo) {
                $fullName = "";
                // グループがあるかどうか判定
                if ( !empty($colsInfo["GROUP"]) ) {
                    foreach ($colsInfo["GROUP"] as $key => $colInfo) {
                        if ($key == 0) {
                            $fullName = "$colInfo";
                        } else {
                            $fullName = "$fullName/$colInfo";
                        }

                        // すでに入ってるグループかどうか判定、なければ入れる。
                        if ( !in_array( $fullName, array_column( $groupDetailListArray, 'FULL_NAME')) ) {
                            $gID                          = "G$g"; // グループID
                            $groupListArray[]             = "G$g"; // グループIDリストにin

                            if ( in_array( $gID, array_column( $NGList, 'COLUMN_ID')) ) {
                                while (in_array( $gID, array_column( $NGList, 'COLUMN_ID'))) {
                                    $g++;
                                    $gID = "G$g";
                                }
                            }
                            // グループ情報を管理
                            $groupDetailListArray[] = array(
                                "ID"        => $gID,
                                "NAME"      => $colInfo,
                                "FULL_NAME" => $fullName
                            );


                            // 親
                            $parent = "";
                            if ($key > 0) {
                                $parentName = $colsInfo["GROUP"][$key-1];

                                $parentFullName = "";
                                for ($p = 0; $p < $key; $p++) {
                                    if ($parentFullName == "") {
                                        $parentFullName = $colsInfo["GROUP"][$p];
                                    } else {
                                        $parentFullName = "$parentFullName/" . $colsInfo["GROUP"][$p];
                                    }
                                }
                                $parentIndex = array_search($parentFullName, array_column( $groupDetailListArray, 'FULL_NAME'));
                                $parent = $groupDetailListArray[$parentIndex]["ID"];
                                $result["GROUP"][$parent]["COLUMNS"][] = $gID;
                            }

                            // グループにin
                            $result["GROUP"][$gID] = array(
                                "LOGICAL_NAME" => $colInfo,
                                "PARENT"       => $parent,
                                "COLUMNS"      => array(),
                                "DISP_SEQ"     => $colsInfo["DISP_SEQ"],
                            );
                            $g = $g + 1;
                        }
                    }
                }
            }

            // ITEMのチェック
            $i = 1;
            $itemListArray       = array();
            $itemDetailListArray = array();
            $dispSeqArray        = array();
            foreach ($ColLabelForERList as $colsInfo) {
                $iID = "I$i"; // ITEMのID
                $fullName = "";
                if ( in_array( $iID, array_column( $NGList, 'COLUMN_ID')) ) {
                    while (in_array( $iID, array_column( $NGList, 'COLUMN_ID'))) {
                        $i++;
                        $iID = "I$i";
                    }
                }

                // グループがある場合
                if ( !empty($colsInfo["GROUP"]) ) {
                    foreach ($colsInfo["GROUP"] as $key => $colInfo) {
                        if ($fullName == "") {
                            $fullName = $colInfo;
                        } else {
                            $fullName = "$fullName/$colInfo";
                        }
                    }

                    $trgGroup = $colsInfo["GROUP"][count($colsInfo["GROUP"]) - 1];
                    // グループがある場合は対象グループ配下のカラムリストに入る
                    $groupNum = array_search($fullName, array_column( $groupDetailListArray, 'FULL_NAME'));

                    $groupKey = $groupDetailListArray[$groupNum]["ID"];
                    $result["GROUP"][$groupKey]["COLUMNS"][] = $iID;
                    // PARENTがあれば上位層のものを辿って入れる。
                    if (!in_array($groupKey, $colListArray) && $result["GROUP"][$groupKey]["PARENT"] != "") {
                        $parentKey = $result["GROUP"][$groupKey]["PARENT"];
                        if (!in_array($parentKey, $colListArray) && $result["GROUP"][$parentKey]["PARENT"] == "") {
                            $colListArray[] = $parentKey;
                        }
                    }
                    else if (!in_array($groupKey, $colListArray) && $result["GROUP"][$groupKey]["PARENT"] == "") {
                        $colListArray[] = $groupKey;
                    }
                }
                else {
                    // グループがないので1階層目に入れる
                    $colListArray[] = $iID;
                }

                $relation_table_name = "";
                $relation_column_id  = "";
                if ( !empty($colsInfo["OBJ_COLUMN"]) ) {
                    $objColumn = $colsInfo["OBJ_COLUMN"];
                    if("IDColumn" === get_class($objColumn) || "LinkIDColumn" === get_class($objColumn)){
                        $relation_table_name = $objColumn->getMasterTableIDForFilter();
                        $relation_column_id  = $objColumn->getDispColumnIDOfMaster();
                    }
                }

                $result["ITEM"][$iID] = array(
                    "PHYSICAL_NAME"       => $colsInfo["PHYSICAL_NAME"],
                    "LOGICAL_NAME"        => $colsInfo["ITEM"],
                    "RELATION_TABLE_NAME" => $relation_table_name,
                    "RELATION_COLUMN_ID"  => $relation_column_id,
                    "DISP_SEQ"            => $colsInfo["DISP_SEQ"],
                );
                $i = $i + 1;
            }

            $result["COLUMNS"]         = $colListArray;
            $result["TABLE_NAME"]      = $aryInfoOfTable["UTN"]["OBJECT_ID"];
            $result["VIEW_TABLE_NAME"] = $aryInfoOfTable["UTN"]["VIEW_ID"];
        }
    }
    catch (Exception $e){
        if( $intErrorType === null ) $intErrorType = 501;
        $tmpErrMsgBody = $e->getMessage();
        $strErrMsg = $tmpErrMsgBody;
    }

    return $result;
}

/*
 * 未実行レコードを取得する
 */
function getUnexecutedRecord(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $sql = "SELECT LOADED_FLG
            FROM A_PROC_LOADED_LIST
            WHERE LOADED_FLG = '0'
            AND PROC_NAME = 'ky_create_er-workflow'";

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
    $resObj = $objQuery->sqlExecute();
    if ($resObj === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $resAry = array();
    while ($row = $objQuery->resultFetch()) {
        $resAry[] = $row;
    }

    return $resAry;
}

/**
 * シーケンスを取得
 *
 * @param    str      $sequenceName    シーケンス用のNAME
 * @return   array    $result          シーケンス番号
 */
function getSequence($sequenceName){
    global $objDBCA, $objMTS;
    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900014',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $sql  = 'SELECT NAME,VALUE FROM A_SEQUENCE
             WHERE NAME = :name';

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
    $res = $objQuery->sqlBind(array('name' => $sequenceName));
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
        $result = $row['VALUE'];
    }

    return $result;
}

/**
 * シーケンス番号を更新する
 *
 * @param    array    $paramAry    各テーブルのシーケンス名とシーケンス番号
 * $paramAray = array(
 *     "name" => "",
 *     "value" => ""
 * );
 */
function updateSequence($paramAry){
    global $objDBCA, $objMTS;
    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900014',
                                                      array(basename(__FILE__), __LINE__)));
    }

    $sql  = 'SELECT NAME,VALUE FROM A_SEQUENCE';
    $sql .= ' WHERE NAME = :name';

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
    $res = $objQuery->sqlBind(array('name' => $paramAry['name']));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $count = 0;
    while ($row = $objQuery->resultFetch()) {
        $count++;
    }

    $last_update_timestamp = date("Y-m-d H:i:s");
    if(1 === $count){
        $sql = "UPDATE A_SEQUENCE
                SET VALUE = :value,
                LAST_UPDATE_TIMESTAMP = '$last_update_timestamp'
                WHERE NAME = :name";

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
        $res = $objQuery->sqlBind($paramAry);
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900017',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
    }

    else{

        $sql = "INSERT INTO A_SEQUENCE(NAME,VALUE,LAST_UPDATE_TIMESTAMP)
                VALUES(:name,:value, '$last_update_timestamp')";

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
        $res = $objQuery->sqlBind($paramAry);
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900017',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
            return false;
        }
    }

    return true;
}

/**
 * プライマリキーをもとにレコードを一件取得する
 *
 * @param    string    $id                プライマリキー
 * @return   array     $retAry            取得したレコード  
 */
function getRecordById($id){
    global $objDBCA, $objMTS;

    $errFlg = 0;
    $sql  = 'SELECT LOADED_FLG
             FROM A_PROC_LOADED_LIST
             WHERE LOADED_FLG = "0"
             AND PROC_NAME = "ky_create_er-workflow"';

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

    $res = $objQuery->sqlBind(array('TASK_ID' => $id));
    if ($res != '') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                      array(baename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $sql);
                    outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }

    $retAry = array();
    while ($row = $objQuery->resultFetch()) {
        $retAry[] = $row;
    }

    if (count($retAry) === 0) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900048',
                                                      array(basename(__FILE__), __LINE__, $id)));
        return false;
    }

    return $retAry;
}


/**
 * 処理済みフラグをクリアする
 *
 * @param    なし
 * @return   なし
 */
function setExecFlg(){
    global $objDBCA, $objMTS;

    $sql = "UPDATE A_PROC_LOADED_LIST
            SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE PROC_NAME = 'ky_create_er-workflow'";

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
    $res = $objQuery->sqlBind(array('LOADED_FLG' => "1", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime()));
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
 * 処理済みフラグをクリアする
 *
 * @param    なし
 * @return   なし
 */
function claerExecFlg(){
    global $objDBCA, $objMTS;

    $sql = "UPDATE A_PROC_LOADED_LIST
            SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE PROC_NAME = 'ky_create_er-workflow'";

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
    $res = $objQuery->sqlBind(array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime()));
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
 * 関連テーブル名と関連カラムをリストに従って変換する
 *
 * @param    array    $erBindAry    変換前の情報
 * @return   array    $erBindAry    変換後の情報
 */
function raplaceColumnName($erBindAry){
    // 変換リスト
    $replaceColumnList = array(
        // 変換例
        // array(
        //     "BEFORE_TABLE_NAME"  => "BEFORE_TABLE",
        //     "BEFORE_COLUMN_ID" => "BEFORE_COLUMN",
        //     "AFTER_TABLE_NAME"   => "AFTER_TABLE",
        //     "AFTER_COLUMN_ID"  => "AFTER_COLUMN",
        // ),
        array(
            "BEFORE_TABLE_NAME" => "G_CREATE_ITEM_INFO",
            "BEFORE_COLUMN_ID"  => "LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "F_CREATE_ITEM_INFO",
            "AFTER_COLUMN_ID"   => "ITEM_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_MENU_LIST",
            "BEFORE_COLUMN_ID"  => "MENU_PULLDOWN",
            "AFTER_TABLE_NAME"  => "D_MENU_LIST",
            "AFTER_COLUMN_ID"   => "MENU_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_MENU_LIST",
            "BEFORE_COLUMN_ID"  => "MENU_PULLDOWN",
            "AFTER_TABLE_NAME"  => "D_MENU_LIST",
            "AFTER_COLUMN_ID"   => "MENU_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_MENU_LIST",
            "BEFORE_COLUMN_ID"  => "MENU_PULLDOWN",
            "AFTER_TABLE_NAME"  => "D_MENU_LIST",
            "AFTER_COLUMN_ID"   => "MENU_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK",
            "BEFORE_COLUMN_ID"  => "ORGANIZATION_WORKSPACE",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_WORKSPACES",
            "AFTER_COLUMN_ID"   => "WORKSPACE_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_POLICY_SETS",
            "BEFORE_COLUMN_ID"  => "POLICY_SET",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_POLICY_SETS",
            "AFTER_COLUMN_ID"   => "POLICY_SET_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_POLICY",
            "BEFORE_COLUMN_ID"  => "POLICY",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_POLICY",
            "AFTER_COLUMN_ID"   => "POLICY_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_POLICY_SETS",
            "BEFORE_COLUMN_ID"  => "POLICY_SET",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_POLICY_SETS",
            "AFTER_COLUMN_ID"   => "POLICY_SET_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK",
            "BEFORE_COLUMN_ID"  => "ORGANIZATION_WORKSPACE",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_WORKSPACES",
            "AFTER_COLUMN_ID"   => "WORKSPACE_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_TERRAFORM_PATTERN",
            "BEFORE_COLUMN_ID"  => "PATTERN",
            "AFTER_TABLE_NAME"  => "E_TERRAFORM_PATTERN",
            "AFTER_COLUMN_ID"   => "PATTERN_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_MODULE",
            "BEFORE_COLUMN_ID"  => "MODULE",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_MODULE",
            "AFTER_COLUMN_ID"   => "MODULE_MATTER_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_TERRAFORM_PATTERN",
            "BEFORE_COLUMN_ID"  => "PATTERN",
            "AFTER_TABLE_NAME"  => "E_TERRAFORM_PATTERN",
            "AFTER_COLUMN_ID"   => "PATTERN_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_PTN_VARS_LINK",
            "BEFORE_COLUMN_ID"  => "VARS_LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_MODULE_VARS_LINK",
            "AFTER_COLUMN_ID"   => "VARS_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_PTN_VARS_LINK",
            "BEFORE_COLUMN_ID"  => "VARS_LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_MODULE_VARS_LINK",
            "AFTER_COLUMN_ID"   => "VARS_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_TERRAFORM_PATTERN",
            "BEFORE_COLUMN_ID"  => "PATTERN",
            "AFTER_TABLE_NAME"  => "E_TERRAFORM_PATTERN",
            "AFTER_COLUMN_ID"   => "PATTERN_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_PTN_VARS_LINK",
            "BEFORE_COLUMN_ID"  => "VARS_LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_MODULE_VARS_LINK",
            "AFTER_COLUMN_ID"   => "VARS_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CMDB_MENU_LIST_CONTRAST",
            "BEFORE_COLUMN_ID"  => "MENU_PULLDOWN",
            "AFTER_TABLE_NAME"  => "D_CMDB_MENU_LIST",
            "AFTER_COLUMN_ID"   => "MENU_ID_CLONE_02"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CONTRAST_LIST",
            "BEFORE_COLUMN_ID"  => "PULLDOWN",
            "AFTER_TABLE_NAME"  => "A_CONTRAST_LIST",
            "AFTER_COLUMN_ID"   => "CONTRAST_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CMDB_MG_MU_COL_LIST_CONTRAST",
            "BEFORE_COLUMN_ID"  => "MENU_COL_TITLE_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_CMDB_MENU_COLUMN",
            "AFTER_COLUMN_ID"   => "COL_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "G_FILE_MASTER",
            "BEFORE_COLUMN_ID"  => "FILE_NAME_FULLPATH",
            "AFTER_TABLE_NAME"  => "G_FILE_MASTER",
            "AFTER_COLUMN_ID"   => "FILE_ID_CLONE",
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_MENU_LIST",
            "BEFORE_COLUMN_ID"  => "MENU_GROUP_ID",
            "AFTER_TABLE_NAME"  => "A_MENU_GROUP_LIST",
            "AFTER_COLUMN_ID"   => "MENU_GROUP_ID",
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_MENU_LIST",
            "BEFORE_COLUMN_ID"  => "MENU_GROUP_NAME",
            "AFTER_TABLE_NAME"  => "A_MENU_GROUP_LIST",
            "AFTER_COLUMN_ID"   => "MENU_GROUP_NAME",
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CMDB_TARGET_MENU_LIST",
            "BEFORE_COLUMN_ID"  => "MENU_PULLDOWN",
            "AFTER_TABLE_NAME"  => "A_MENU_LIST",
            "AFTER_COLUMN_ID"   => "MENU_NAME",
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANS_LNS_PTN_VARS_LINK",
            "BEFORE_COLUMN_ID"  => "VARS_LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_ANS_LNS_PTN_VARS_LINK",
            "AFTER_COLUMN_ID"   => "VARS_NAME_ID"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANS_LRL_ARRAY_MEMBER",
            "BEFORE_COLUMN_ID"  => "VRAS_NAME",
            "AFTER_TABLE_NAME"  => "B_ANS_LRL_ARRAY_MEMBER",
            "AFTER_COLUMN_ID"   => "VARS_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANS_LRL_MEMBER_COL_COMB",
            "BEFORE_COLUMN_ID"  => "COMBINATION_MEMBER",
            "AFTER_TABLE_NAME"  => "B_ANS_LRL_MEMBER_COL_COMB",
            "AFTER_COLUMN_ID"   => "COL_COMBINATION_MEMBER_ALIAS"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANS_PNS_PTN_VARS_LINK",
            "BEFORE_COLUMN_ID"  => "VARS_LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_ANS_PNS_PTN_VARS_LINK",
            "AFTER_COLUMN_ID"   => "VARS_NAME_ID"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANS_TWR_HOST",
            "BEFORE_COLUMN_ID"  => "ANSTWR_HOSTNAME",
            "AFTER_TABLE_NAME"  => " B_ANS_TWR_HOST",
            "AFTER_COLUMN_ID"   => "ANSTWR_HOSTNAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANSIBLE_LRL_ROLE_LIST",
            "BEFORE_COLUMN_ID"  => "ROLE_NAME_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_ANSIBLE_LRL_ROLE",
            "AFTER_COLUMN_ID"   => "ROLE_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANSIBLE_LRL_ROLE_PKG_LIST",
            "BEFORE_COLUMN_ID"  => "ROLE_PACKAGE_NAME_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_ANSIBLE_LRL_ROLE_PACKAGE",
            "AFTER_COLUMN_ID"   => "ROLE_PACKAGE_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_ANS_LRL_PTN_VARS_LINK",
            "BEFORE_COLUMN_ID"  => "VARS_LINK_PULLDOWN",
            "AFTER_TABLE_NAME"  => "B_ANSIBLE_LRL_VARS_MASTER",
            "AFTER_COLUMN_ID"   => "VARS_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CMDB_MENU_COLUMN_SHEET_TYPE_4",
            "BEFORE_COLUMN_ID"  => "COL_TITLE",
            "AFTER_TABLE_NAME"  => "B_CMDB_MENU_COLUMN",
            "AFTER_COLUMN_ID"   => "COL_TITLE"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CMDB_MENU_COLUMN_SHEET_TYPE_1_PIONEER",
            "BEFORE_COLUMN_ID"  => "COL_TITLE",
            "AFTER_TABLE_NAME"  => "B_CMDB_MENU_COLUMN",
            "AFTER_COLUMN_ID"   => "COL_TITLE"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_CMDB_MENU_COLUMN_SHEET_TYPE_1",
            "BEFORE_COLUMN_ID"  => "COL_TITLE",
            "AFTER_TABLE_NAME"  => "B_CMDB_MENU_COLUMN",
            "AFTER_COLUMN_ID"   => "COL_TITLE"
        ),
        array(
            "BEFORE_TABLE_NAME" => "D_TERRAFORM_ORGANIZATION_WORKSPACE_LINK",
            "AFTER_TABLE_NAME"  => "B_TERRAFORM_WORKSPACES",
            "BEFORE_COLUMN_ID"  => "ORGANIZATION_WORKSPACE",
            "AFTER_COLUMN_ID"   => "WORKSPACE_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_OPERATION_LIST",
            "AFTER_TABLE_NAME"  => "C_OPERATION_LIST",
            "BEFORE_COLUMN_ID"  => "OPERATION",
            "AFTER_COLUMN_ID"   => "OPERATION_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_STM_LIST",
            "BEFORE_COLUMN_ID"  => "HOST_PULLDOWN",
            "AFTER_TABLE_NAME"  => "C_STM_LIST",
            "AFTER_COLUMN_ID"   => "HOSTNAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_ANSIBLE_LNS_PATTERN",
            "BEFORE_COLUMN_ID"  => "PATTERN",
            "AFTER_TABLE_NAME"  => "E_ANSIBLE_LNS_PATTERN",
            "AFTER_COLUMN_ID"   => "PATTERN_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_ANSIBLE_LRL_PATTERN",
            "BEFORE_COLUMN_ID"  => "PATTERN",
            "AFTER_TABLE_NAME"  => "E_ANSIBLE_LRL_PATTERN",
            "AFTER_COLUMN_ID"   => "PATTERN_NAME"
        ),
        array(
            "BEFORE_TABLE_NAME" => "E_ANSIBLE_PNS_PATTERN",
            "BEFORE_COLUMN_ID"  => "PATTERN",
            "AFTER_TABLE_NAME"  => "E_ANSIBLE_PNS_PATTERN",
            "AFTER_COLUMN_ID"   => "PATTERN_NAME"
        ),
    );

    $tmpAry = array_column($replaceColumnList, 'BEFORE_TABLE_NAME');
    $tableName = preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $erBindAry["RELATION_TABLE_NAME"]);
    $beforeRelationColumnId = preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $erBindAry["RELATION_COLUMN_ID"]);

    // 対象テーブルと名前の一致するインデックスリスト
    $trgIndexList = array_keys($tmpAry, $tableName);

    foreach ($trgIndexList as $index) {
        if ($replaceColumnList[$index]["BEFORE_COLUMN_ID"] == $beforeRelationColumnId) {
            $erBindAry["RELATION_TABLE_NAME"] = $replaceColumnList[$index]["AFTER_TABLE_NAME"];
            $erBindAry["RELATION_COLUMN_ID"]  = $replaceColumnList[$index]["AFTER_COLUMN_ID"];
        }
    }

    return $erBindAry;
}