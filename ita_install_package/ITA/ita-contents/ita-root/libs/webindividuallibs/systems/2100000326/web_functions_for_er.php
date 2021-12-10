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

// 最終更新者「ER図作成プロシージャ」
define('ACCOUNT_NAME', -100326);

/**
 * ER図用情報を取得する
 *
 * @return   array    $result    ER図の表示に必要な情報
 */
function getERInfo(){
    global $g, $objDBCA, $objMTS;
    $login_id = $g["login_id"]; // ユーザID

    // 返り値
    $result = array("MENU_GROUP" => array());

    // 取得可能なメニューIDの取得
    $canGetMenuGroupList = array();
    $canGetMenuList = array();

    $sql = "SELECT DISTINCT A_MENU_LIST.MENU_ID, MENU_NAME, A_MENU_LIST.MENU_GROUP_ID, MENU_GROUP_NAME, A_MENU_LIST.DISP_SEQ
            FROM A_MENU_LIST
            JOIN A_ROLE_MENU_LINK_LIST
            ON A_MENU_LIST.MENU_ID = A_ROLE_MENU_LINK_LIST.MENU_ID
            LEFT OUTER JOIN A_MENU_GROUP_LIST
            ON A_MENU_LIST.MENU_GROUP_ID = A_MENU_GROUP_LIST.MENU_GROUP_ID
            LEFT OUTER JOIN A_ROLE_ACCOUNT_LINK_LIST
            ON A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID = A_ROLE_MENU_LINK_LIST.ROLE_ID
            WHERE A_ROLE_ACCOUNT_LINK_LIST.USER_ID = :USER_ID
            AND A_MENU_LIST.DISUSE_FLAG = 0
            AND A_ROLE_MENU_LINK_LIST.DISUSE_FLAG = 0
            AND A_MENU_GROUP_LIST.DISUSE_FLAG = 0
            AND A_ROLE_ACCOUNT_LINK_LIST.DISUSE_FLAG = 0
            ORDER BY A_MENU_LIST.MENU_GROUP_ID, A_MENU_LIST.DISP_SEQ, A_MENU_LIST.MENU_ID DESC
            ";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlBind(array("USER_ID" => $login_id));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $tmpSequence = array();
    while ($row = $objQuery->resultFetch()){
        $menuGroupId   = $row["MENU_GROUP_ID"];
        $menuGroupNeme = $row["MENU_GROUP_NAME"];
        $menuId        = sprintf("%010d", ($row["MENU_ID"]));
        $menuName      = $row["MENU_NAME"];

        if ( !in_array($menuGroupId, array_column( $tmpSequence, 'GROUP_ID')) ) {
            $tmpSequence[] = array(
                "GROUP_ID" => $menuGroupId,
                "VALUE"    => 1,
            );
            $dispSeq = 1;
        } else {
            $key = array_search($menuGroupId, array_column( $tmpSequence, 'GROUP_ID'));
            $value = $tmpSequence[$key]["VALUE"] + 1;
            $dispSeq = $value;
            $tmpSequence[$key]["VALUE"] = $value;
        }

        $tableInfo = getMenuTableInfo($menuId);
        if ( !empty($tableInfo) ) {
            $menuTableLinkId = $tableInfo["ROW_ID"];
            $menuInfo = array(
                "ID"         => $menuId,
                "NAME"       => $menuName,
                "TABLE_NAME" => $tableInfo["TABLE_INFO"],
                "DISP_SEQ"   => $dispSeq,
                "COLUMNS"    => getColumnInfo($menuTableLinkId),
                "GROUP_ITEM" => array_merge(getGroupInfo($menuTableLinkId), getItemInfo($menuTableLinkId))
            );
            if ( !in_array( $menuGroupId, array_column( $result["MENU_GROUP"], 'ID')) ) {
                $result["MENU_GROUP"][] = array(
                    "ID"   => $menuGroupId,
                    "NAME" => $menuGroupNeme,
                    "MENU" => array($menuInfo)
                );
            } else {
                $key = array_search( $menuGroupId, array_column( $result["MENU_GROUP"], 'ID'));
                $result["MENU_GROUP"][$key]["MENU"][] = $menuInfo;
            }
        }

    }
    return $result;
}

/**
 * メニュー情報を取得する
 *
 * @param    str      $menuId    メニューID
 * @return   array    $result    メニュー情報
 */
function getMenuTableInfo($menuId){
    global $objDBCA, $objMTS;

    $result = "";

    // メニューIDに紐づく情報取得
    $sql  = "SELECT ROW_ID, TABLE_NAME, VIEW_TABLE_NAME
             FROM B_ER_MENU_TABLE_LINK_LIST
             WHERE MENU_ID = :MENU_ID
             AND DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $objQuery->sqlBind(array("MENU_ID" => $menuId));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        $result = array(
            "ROW_ID"     => $row["ROW_ID"],
            "TABLE_INFO" => array(
                "TABLE" => $row["TABLE_NAME"],
                "VIEW"  => $row["VIEW_TABLE_NAME"],
            )
        );
    }

    return $result;
}

/**
 * カラム情報を取得する
 *
 * @param    str      $menuTableLinkId    メニューとテーブルのリンクID
 * @return   array    $result             カラム情報
 */
function getColumnInfo($menuTableLinkId){
    global $objDBCA, $objMTS;

    $result = array();

    // メニューIDに紐づく情報取得
    $sql = "SELECT COLUMN_ID, COLUMN_TYPE, PARENT_COLUMN_ID, PHYSICAL_NAME, LOGICAL_NAME, RELATION_TABLE_NAME, RELATION_COLUMN_ID
            FROM B_ER_DATA
            LEFT OUTER JOIN B_ER_MENU_TABLE_LINK_LIST
            ON B_ER_DATA.MENU_TABLE_LINK_ID = B_ER_MENU_TABLE_LINK_LIST.ROW_ID
            WHERE MENU_TABLE_LINK_ID = :MENU_TABLE_LINK_ID
            AND ( PARENT_COLUMN_ID = '' OR PARENT_COLUMN_ID IS NULL )
            AND B_ER_DATA.DISUSE_FLAG = 0
            AND B_ER_MENU_TABLE_LINK_LIST.DISUSE_FLAG = 0
            ORDER BY B_ER_DATA.DISP_SEQ IS NULL ASC, B_ER_DATA.DISP_SEQ ASC";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $objQuery->sqlBind(array("MENU_TABLE_LINK_ID" => $menuTableLinkId));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        $result[] = $row["COLUMN_ID"];
    }

    return $result;
}

/**
 * グループ情報を取得する
 *
 * @param    str      $menuTableLinkId    メニューとテーブルのリンクID
 * @return   array    $result             グループ情報
 */
function getGroupInfo($menuTableLinkId){
    global $objDBCA, $objMTS;

    $result = array();

    // メニューIDに紐づく情報取得
    $sql = "SELECT COLUMN_ID, COLUMN_TYPE, PARENT_COLUMN_ID, PHYSICAL_NAME, LOGICAL_NAME, RELATION_TABLE_NAME, RELATION_COLUMN_ID
            FROM B_ER_DATA
            LEFT OUTER JOIN B_ER_MENU_TABLE_LINK_LIST
            ON B_ER_DATA.MENU_TABLE_LINK_ID = B_ER_MENU_TABLE_LINK_LIST.ROW_ID
            WHERE MENU_TABLE_LINK_ID = :MENU_TABLE_LINK_ID
            AND COLUMN_TYPE = '1'
            AND B_ER_DATA.DISUSE_FLAG = 0
            AND B_ER_MENU_TABLE_LINK_LIST.DISUSE_FLAG = 0
            ORDER BY B_ER_DATA.DISP_SEQ ASC";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlBind(array("MENU_TABLE_LINK_ID" => $menuTableLinkId));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        $columns = array();
        $group_column_id = $row["COLUMN_ID"];
        // カラム情報の取得
        $sql = "SELECT COLUMN_ID
                FROM B_ER_DATA
                LEFT OUTER JOIN B_ER_MENU_TABLE_LINK_LIST
                ON B_ER_DATA.MENU_TABLE_LINK_ID = B_ER_MENU_TABLE_LINK_LIST.ROW_ID
                WHERE MENU_TABLE_LINK_ID = :MENU_TABLE_LINK_ID
                AND PARENT_COLUMN_ID = :PARENT_COLUMN_ID
                AND B_ER_DATA.DISUSE_FLAG = 0
                AND B_ER_MENU_TABLE_LINK_LIST.DISUSE_FLAG = 0";

        $_objQuery = $objDBCA->sqlPrepare($sql);
        if ($_objQuery->getStatus() === false) {
            web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                 array(__FILE__, __LINE__)));
            web_log($sql);
            web_log($objQuery->getLastError());
            throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
        }

        $_res = $_objQuery->sqlBind(array("MENU_TABLE_LINK_ID" => $menuTableLinkId, "PARENT_COLUMN_ID" => $group_column_id));
        $_res = $_objQuery->sqlExecute();
        if ($_res === false) {
            web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                 array(__FILE__, __LINE__)));
            web_log($sql);
            web_log($objQuery->getLastError());
            throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
        }

        while ($_row = $_objQuery->resultFetch()){
            $columns[] = $_row["COLUMN_ID"];
        }

        // $result[$group_column_id] = array(
        //     "LOGICAL_NAME" => $row["LOGICAL_NAME"],
        //     "PARENT"       => $row["PARENT_COLUMN_ID"],
        //     "COLUMNS"      => $columns,
        // );
        $result[$group_column_id] = array(
            "LOGICAL_NAME" => $row["LOGICAL_NAME"],
            "PARENT"       => $row["PARENT_COLUMN_ID"],
            "COLUMNS"      => $columns,
            "TYPE"         => "GROUP",
        );
    }

    return $result;
}

/**
 * アイテム情報を取得する
 *
 * @param    str      $menuTableLinkId    メニューとテーブルのリンクID
 * @return   array    $result             アイテム情報
 */
function getItemInfo($menuTableLinkId){
    global $objDBCA, $objMTS;

    $result = array();

    // メニューIDに紐づく情報取得
    $sql = "SELECT B_ER_DATA.ROW_ID, B_ER_MENU_TABLE_LINK_LIST.MENU_ID, B_ER_MENU_TABLE_LINK_LIST.TABLE_NAME, COLUMN_ID, COLUMN_TYPE, PARENT_COLUMN_ID, PHYSICAL_NAME, LOGICAL_NAME, RELATION_TABLE_NAME, RELATION_COLUMN_ID
            FROM B_ER_DATA
            LEFT OUTER JOIN B_ER_MENU_TABLE_LINK_LIST
            ON B_ER_DATA.MENU_TABLE_LINK_ID = B_ER_MENU_TABLE_LINK_LIST.ROW_ID
            WHERE MENU_TABLE_LINK_ID = :MENU_TABLE_LINK_ID
            AND COLUMN_TYPE = '2'
            AND B_ER_DATA.DISUSE_FLAG = 0
            AND B_ER_MENU_TABLE_LINK_LIST.DISUSE_FLAG = 0
            ORDER BY B_ER_DATA.DISP_SEQ ASC";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $objQuery->sqlBind(array("MENU_TABLE_LINK_ID" => $menuTableLinkId));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        $relationTableName = preg_replace( '/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $row["RELATION_TABLE_NAME"]);
        $relationMenuIdList = array();
        $relationColumnId = $row["RELATION_COLUMN_ID"];
        if ($relationTableName != "") {
            $relationMenuIdList = getRelationMenuIdList($relationTableName);
        }
        if (count($relationMenuIdList) < 1) {
            $relationColumnId  = "";
        }
        else {
            // オペレーションIDとオペレーション名とホスト名は表示しない。後日改修予定
            if ( $row["RELATION_COLUMN_ID"] == "HOSTNAME" ) {
                $key = array_search("2100000303", $relationMenuIdList);
                if ( $key !== false ) {
                    array_splice($relationMenuIdList, $key, 1);
                }
            } elseif ( $row["PHYSICAL_NAME"] == "HOSTNAME" ) {
                if ( $row["MENU_ID"] == "2100000303" ) {
                    $relationMenuIdList = array();
                }
            } elseif ( $row["RELATION_COLUMN_ID"] == "OPERATION_NO_IDBH" || $row["RELATION_COLUMN_ID"] == "OPERATION_NAME" ) {
                $key = array_search("2100000304", $relationMenuIdList);
                if ( $key !== false ) {
                    array_splice($relationMenuIdList, $key, 1);
                }
            } elseif ( $row["PHYSICAL_NAME"] == "OPERATION_NO_IDBH" || $row["PHYSICAL_NAME"] == "OPERATION_NAME" ) {
                if ( $row["MENU_ID"] == "2100000304" ) {
                    $relationMenuIdList = array();
                }
            }
        }
        $result[$row["COLUMN_ID"]] = array(
            "PHYSICAL_NAME"       => $row["PHYSICAL_NAME"],
            "LOGICAL_NAME"        => $row["LOGICAL_NAME"],
            "RELATION_TABLE_NAME" => $relationTableName,
            "RELATION_COLUMN_ID"  => $relationColumnId,
            "RELATION_MENU_ID"    => $relationMenuIdList,
            "TYPE"                => "ITEM",
        );
    }

    return $result;
}

/**
 * 関連メニューIDの取得
 *
 * @param    なし
 * @return   なし
 */
function getRelationMenuIdList($tableName) {
    global $objDBCA, $objMTS;

    $result = array();

    $sql = "SELECT B_ER_MENU_TABLE_LINK_LIST.ROW_ID, B_ER_MENU_TABLE_LINK_LIST.MENU_ID
            FROM B_ER_MENU_TABLE_LINK_LIST
            LEFT OUTER JOIN B_ER_DATA
            ON B_ER_DATA.MENU_TABLE_LINK_ID = B_ER_MENU_TABLE_LINK_LIST.ROW_ID
            WHERE (TABLE_NAME = :TABLE_NAME OR VIEW_TABLE_NAME = :TABLE_NAME)
            AND COLUMN_TYPE = '2'
            AND B_ER_DATA.DISUSE_FLAG = 0
            AND B_ER_MENU_TABLE_LINK_LIST.DISUSE_FLAG = 0
            GROUP BY B_ER_MENU_TABLE_LINK_LIST.ROW_ID
            ORDER BY B_ER_DATA.DISP_SEQ ASC";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $objQuery->sqlBind(array("TABLE_NAME" => $tableName));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        $result[] = sprintf("%010d", ($row["MENU_ID"]));
    }

    return $result;
}

/**
 * タスクの挿入
 *
 * @param    なし
 * @return   なし
 */
function insertTask(){
    global $objDBCA, $objMTS;

    $sql = "UPDATE A_PROC_LOADED_LIST
            SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP
            WHERE PROC_NAME = 'ky_create_er-workflow'";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $objDBCA->setQueryTime();
    $res = $objQuery->sqlBind(array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime()));
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }
    return true;
}
