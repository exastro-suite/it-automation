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
//  【処理概要】
//      多段変数最大繰返数メニュー反映ファイル 前準備
//          「多次元変数メンバー管理」のVARS_NAMEが0のレコードのMAX_COL_SEQを
//          「多次元変数最大繰返数管理」に同期させる
//
//  【その他】
//      多次元変数メンバー管理    : B_ANS_LRL_ARRAY_MEMBER
//      多次元変数配列組合せ管理 : B_ANS_LRL_MEMBER_COL_COMB
//      多次元変数最大繰返数管理 : B_ANS_LRL_MAX_MEMBER_COL
//
//////////////////////////////////////////////////////////////////////

/**
 * 多次元変数メンバー管理との同期処理
 */
function syncDefaultMaxMemberCol() {

    global    $log_level;
    global    $objMTS;
    global    $db_model_ch; // 1:MySql
    global    $objDBCA;

    $ansibleRole_maxMemberCol_tblName = "B_ANS_LRL_MAX_MEMBER_COL";
    $ansibleRole_maxMemberCol_pkName = "MAX_COL_SEQ_ID";
    $ansibleRole_maxMemberCol_columns = array(
            // tbl共通・必須
            'JOURNAL_SEQ_NO' => "",
            'JOURNAL_REG_DATETIME' => "",
            'JOURNAL_ACTION_CLASS' => "",
            'DISP_SEQ' => "",
            'NOTE' => "",
            'DISUSE_FLAG' => "",
            'LAST_UPDATE_TIMESTAMP' => "",
            'LAST_UPDATE_USER' => "",

            // tbl個別
            'MAX_COL_SEQ_ID' => "",
            'VARS_NAME_ID' => "",
            'ARRAY_MEMBER_ID' => "",
            'MAX_COL_SEQ' => "",
            'ACCESS_AUTH' => "",
        );


        ////////////////////////////////
        // ロールパッケージ管理のアクセス権取得
        ////////////////////////////////
        $sql = " SELECT DISTINCT             \n"     
             . "   TAB_A.VARS_NAME_ID,       \n"
             . "   TAB_B.VARS_NAME,          \n"
             . "   TAB_D.ROLE_PACKAGE_NAME,  \n"
             . "   TAB_D.ACCESS_AUTH         \n"
             . " FROM                        \n"
             . "   B_ANS_LRL_ARRAY_MEMBER                TAB_A                                                     \n"
             . "   LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_B ON (TAB_A.VARS_NAME_ID    = TAB_B.VARS_NAME_ID)     \n"
             . "   LEFT JOIN B_ANSIBLE_LRL_ROLE_VARS     TAB_C ON (TAB_B.VARS_NAME       = TAB_C.VARS_NAME)        \n"
             . "   LEFT JOIN B_ANSIBLE_LRL_ROLE_PACKAGE  TAB_D ON (TAB_C.ROLE_PACKAGE_ID = TAB_D.ROLE_PACKAGE_ID)  \n"
             . " WHERE                           \n"
             . "   TAB_A.DISUSE_FLAG = '0' &&    \n"
             . "   TAB_B.DISUSE_FLAG = '0' &&    \n"
             . "   TAB_C.DISUSE_FLAG = '0' &&    \n"
             . "   TAB_D.DISUSE_FLAG = '0' &&    \n"
             . "   TAB_A.VARS_NAME   = '0';      \n";

        $RolePkgAccessAuthAry = array();
        if(dbaccessSelect($sql, null, $RolePkgAccessAuthAry) === false) {
            return false;
        }
        // 変数に紐づいているロールパッケージのアクセス権ロールを取得
        $keyid = 'VARS_NAME_ID';
        $VarAccessAuthAry = array();
        foreach($RolePkgAccessAuthAry as $row) {
            if($row['ACCESS_AUTH'] == "") {
                // ロールパッケージ管理のアクセス許可ロールが空白の場合
                // 多段変数最大繰返数のアクセス許可ロールも空白にする。
                $VarAccessAuthAry[$row[$keyid]] = array();
                $VarAccessAuthAry[$row[$keyid]][0] = "";
            } else {
                // ロールパッケージ管理のアクセス許可ロールに空白があったか判定
                if(@count($VarAccessAuthAry[$row[$keyid]]) != 0) {
                    if($VarAccessAuthAry[$row[$keyid]][0] == "") {
                        continue;
                    }
                }
                // ロールパッケージ管理のアクセス許可ロールをORする為の配列生成
                $RoleIDList = explode(',',$row['ACCESS_AUTH']);
                foreach($RoleIDList as $RoleID) {
                    $VarAccessAuthAry[$row[$keyid]][] = $RoleID;
                }
            }
        }
        
        // 変数に紐づいているロールパッケージのアクセス権ロールをCSV文字列に変換
        $VarAccessAuthStrList = array();
        foreach($VarAccessAuthAry as $keyid=>$RoleIDList) {
            // 重複しているロールを取り除く
            $uniqueRoleIDList = array_unique($RoleIDList);
            // ID順でソート
            $VarAccessAuthStrList[$keyid] = RoleIDSort(implode(',',$uniqueRoleIDList));
        }

        unset($varAccessAuthAry);
        unset($RolePkgAccessAuthaAry);
        ////////////////////////////////
        // 対象変数絞込み           //
        ////////////////////////////////

        global $db_access_user_id;
        // 変数名一覧TBLから自身が更新対象であるレコードを取得
        $varsMaster = array();
        $sql = 
              "SELECT \n"
            . "    * \n"
            . "FROM B_ANSIBLE_LRL_VARS_MASTER \n"
            . ";"; // 廃止レコード含む
        if(dbaccessSelect($sql, null, $varsMaster) === false) {
            return false;
        }
        $varsNameIdAlive = array();
        $varsNameIdAll = array();
        foreach($varsMaster as $master) {
            if($master['LAST_UPDATE_USER'] != $db_access_user_id) {
                // DEBUGメッセージ
                if($log_level === 'DEBUG') {
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90226", $master['VARS_NAME']);
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                    $FREE_LOG = $objMTS->getSomeMessage("ITAANSIBLEH-ERR-90227", $master['VARS_NAME']);
                    LocalLogPrint(basename(__FILE__),__LINE__,$FREE_LOG);
                }
                continue;
            }
            if($master['DISUSE_FLAG'] == "0") {
                $varsNameIdAlive[] = $master['VARS_NAME_ID'];
            }

            $varsNameIdAll[] = $master['VARS_NAME_ID'];
        }

        ////////////////////////////////
        // レコード準備               //
        ////////////////////////////////

        // 多次元変数メンバー管理TBLから繰返しを示す VARS_NAME = '0' の要素を取得
        $nominateMaxMemberCol = array();
        if(count($varsNameIdAlive) > 0) {
            $sql = 
                  "SELECT \n"
                . "    VARS_NAME_ID \n"
                . "   ,ARRAY_MEMBER_ID \n"
                . "   ,MAX_COL_SEQ \n"
                . "   ,ACCESS_AUTH \n"
                . "FROM B_ANS_LRL_ARRAY_MEMBER \n"
                . "WHERE VARS_NAME_ID IN (" . implode(", ", $varsNameIdAlive) . ") \n" //   最終更新者が自分であり、廃止ではない変数だけ
                . "AND VARS_NAME = '0' \n"
                . "AND DISUSE_FLAG = '0' \n" // 廃止レコード含まない
                . ";";
            if(dbaccessSelect($sql, null, $nominateMaxMemberCol) === false) {
                return false;
            }
        }

        // 既存の多次元変数最大繰返数管理TBLのレコードを取得
        $currentMaxMemberCol = array();
        if(count($varsNameIdAll) > 0) {
            $sql = 
                  "SELECT \n"
                . "    * \n"
                . "FROM B_ANS_LRL_MAX_MEMBER_COL \n"
                . "WHERE VARS_NAME_ID IN (" . implode(", ", $varsNameIdAll) . ") \n" //     最終更新者が自分である変数だけ（廃止含む）
                . ";"; // 廃止レコード含む
            if(dbaccessSelect($sql, null, $currentMaxMemberCol) === false) {
                return false;
            }
        }

        ////////////////////////////////
        // DB操作（新規／更新／廃止） //
        ////////////////////////////////
        // アクセス権が変更になっている (nominateに有り:廃止レコードなし、currentに有り:廃止レコードあり)
        $updateRecordsRevive = AccessAuthUpdateRecords($nominateMaxMemberCol, $currentMaxMemberCol,$VarAccessAuthStrList);

        if(count($updateRecordsRevive ) > 0 &&
           dbaccessUpdateRevive($ansibleRole_maxMemberCol_tblName, $ansibleRole_maxMemberCol_pkName, $ansibleRole_maxMemberCol_columns, $updateRecordsRevive) === false) {
               // 念のためロールバック
               rollbackTransaction();
               return false;
        }

        // 新規 (nominateに有り:廃止レコードなし、currentに無し:廃止レコードあり)
        $insertRecords = specificArrayDiff_maxMemberCol($nominateMaxMemberCol, $currentMaxMemberCol);

        // ロールパッケージ管理のアクセス権ロールを設定
        AccessAuthUpdate($insertRecords,$VarAccessAuthStrList);

        if(count($insertRecords) > 0 &&
            dbaccessInsert($ansibleRole_maxMemberCol_tblName, $ansibleRole_maxMemberCol_pkName, $ansibleRole_maxMemberCol_columns, $insertRecords) === false) {
            // 念のためロールバック
            rollbackTransaction();
            return false;
        }

        // 復活 (nominateに有り:廃止レコードなし、currentで "廃止")
        $currentMaxMemberCol_disuse = extractRecords($currentMaxMemberCol, array('DISUSE_FLAG' => '1'));
        $updateRecordsRevive = specificArrayMatch_maxMemberCol($currentMaxMemberCol_disuse, $nominateMaxMemberCol);
        // ロールパッケージ管理のアクセス権ロールを設定
        AccessAuthUpdate($updateRecordsRevive,$VarAccessAuthStrList);

        if(count($updateRecordsRevive) > 0 &&
            dbaccessUpdateRevive($ansibleRole_maxMemberCol_tblName, $ansibleRole_maxMemberCol_pkName, $ansibleRole_maxMemberCol_columns, $updateRecordsRevive) === false) {
            // 念のためロールバック
            rollbackTransaction();
            return false;
        }

        // 廃止 (currentに有り、nominateに無し:廃止レコードなし)
        $currentMaxMemberCol_alive = extractRecords($currentMaxMemberCol, array('DISUSE_FLAG' => '0'));
        $updateRecordsDisuse = specificArrayDiff_maxMemberCol($currentMaxMemberCol_alive, $nominateMaxMemberCol);
        // 廃止にする場合はロールパッケージ管理のアクセス権ロールを設定しない

        if(count($updateRecordsDisuse) > 0 &&
            dbaccessUpdateDisuse($ansibleRole_maxMemberCol_tblName, $ansibleRole_maxMemberCol_pkName, $ansibleRole_maxMemberCol_columns, $updateRecordsDisuse) === false) {
            // 念のためロールバック
            rollbackTransaction();
            return false;
        }

    unset($VarAccessAuthStrList);

    return true;

} //----ここまで多次元変数メンバー管理との同期処理

function AccessAuthUpdateRecords($sourceArray, $targetArray,$VarAccessAuthStrList) {
    $result = array();
    // アクセス権ロールの更新が必要なレコードかを判定
    // $sourceArray:廃止レコードは含まれていない $targetArray:廃止レコードが含まれている
    foreach($sourceArray as $sourceRecord) {
        foreach($targetArray as $targetRecord) {
            // 有効レコードの場合のみアクセス権ロールが必要なレコードとする。
            if($targetRecord['DISUSE_FLAG'] == '0') {
                if($sourceRecord['VARS_NAME_ID']    == $targetRecord['VARS_NAME_ID'] &&
                   $sourceRecord['ARRAY_MEMBER_ID'] == $targetRecord['ARRAY_MEMBER_ID']) {
                    // ロールパッケージ管理のアクセス権ロールと比較
                    $nowAccessAuth = $VarAccessAuthStrList[$targetRecord['VARS_NAME_ID']];
                    if($targetRecord['ACCESS_AUTH'] != $nowAccessAuth) {
                        // アクセス権ロール更新
                        $targetRecord['ACCESS_AUTH'] = $nowAccessAuth;
                        $result[] = $targetRecord;
                    }
                }
            }
        }
    }
    return $result;
}
/**
 * 配列差分レコード取得
 */
function specificArrayDiff_maxMemberCol($sourceArray, $targetArray) {

    $result = array();

    foreach($sourceArray as $sourceRecord) {
        if(!isContained_maxMemberCol($sourceRecord, $targetArray)) {
            $result[] = $sourceRecord;
        }
    }

    return $result;
}

/**
 * 配列から必要なレコードのみを抽出する
 * 本機能限定のあまり使い回せない関数
 */
function extractRecords($sourceArray, $conditionArray) {

    $workArray = $sourceArray;

    foreach($conditionArray as $key => $value) {
        $tmpArray = array();

        foreach($workArray as $workRecord) {
            if($workRecord[$key] == $value) {
                $tmpArray[] = $workRecord;
            }
        }

        $workArray = $tmpArray;
    }
    return $workArray;
}

/**
 * 配列一致レコード取得
 */
function specificArrayMatch_maxMemberCol($sourceArray, $targetArray) {

    $result = array();

    foreach($sourceArray as $sourceRecord) {
        if(isContained_maxMemberCol($sourceRecord, $targetArray)) {
            $result[] = $sourceRecord;
        }
    }

    return $result;
}

/**
 * 多次元変数レコードの比較用
 * 'VARS_NAME_ID' と 'ARRAY_MEMBER_ID' の2要素をキーとする
 */
function isContained_maxMemberCol($source, $targetArray) {

    foreach($targetArray as $targetRecord) {
        if($source['VARS_NAME_ID']     == $targetRecord['VARS_NAME_ID'] &&
            $source['ARRAY_MEMBER_ID'] == $targetRecord['ARRAY_MEMBER_ID']) {
            return true;
        }
    }
    return false;
}

function AccessAuthUpdate(&$targetArray,$VarAccessAuthStrList) {
    foreach($targetArray as $PkeyID=>$targetRecord) {
        $key = $targetRecord['VARS_NAME_ID'];
        // ロールパッケージ管理のアクセス権ロールと比較
        if(array_key_exists($targetRecord['VARS_NAME_ID'],$VarAccessAuthStrList)) {
            if(isset($VarAccessAuthStrList[$targetRecord['VARS_NAME_ID']])) {
                $nowAccessAuth = $VarAccessAuthStrList[$targetRecord['VARS_NAME_ID']];
            } else {
                $nowAccessAuth = "";
            }
            // アクセス権ロールに差異がある
            if($targetRecord['ACCESS_AUTH'] != $nowAccessAuth) {
                $targetArray[$PkeyID]['ACCESS_AUTH'] = $nowAccessAuth;
            }
        }
    }
}

function RoleIDSort($RoleIDStr) {
    if($RoleIDStr == "") {
        $SortRoleIDStr = "";
    } else {
        $RoleIDAry = explode(',',$RoleIDStr);
        // ID順でソート
        asort($RoleIDAry);
        $SortRoleIDStr = implode(',',$RoleIDAry);
    }
    return $SortRoleIDStr;
}    
?>
