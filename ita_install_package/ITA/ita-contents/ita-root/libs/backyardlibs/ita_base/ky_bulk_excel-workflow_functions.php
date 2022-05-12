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

/********************************************
* レコードの取得
********************************************/

/**
* 未実行レコードの取得
* 
* @return array $result 未実行レコード一覧
*/

function getUnexecutedBulkExcelTask(){
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

    $sql = "SELECT
                *
            FROM
                B_BULK_EXCEL_TASK
            WHERE
                TASK_STATUS = 1
            AND
                DISUSE_FLAG = 0";

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

    $result = array();
    while ($row = $objQuery->resultFetch()) {
        $result[] = $row;
    }

    return $result;
}

/********************************************
* ステータス更新系
********************************************/

/**
* ステータスの更新
* 
* @param  int   $taskId   タスクID
* @param  int   $statusId ステータスD
* @return boolean         実行結果
*/
function setStatus($taskId, $status, $uploadFile=NULL){
    global $objMTS, $objDBCA, $db_model_ch;
    require_once ROOT_DIR_PATH . '/libs/backyardlibs/common/common_functions.php';

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900025',
                                                      array($taskId, $status,
                                                            basename(__FILE__), __LINE__)));
    }

    try{
        // トランザクション開始
        if ( $objDBCA->transactionStart() === false ) {
            // 確認
            $err_msg = $objMTS->getSomeMessage("ITAWDCH-ERR-11404");
            throw new Exception($errMsg);
        }

        // 通常テーブルで更新
        $sql = "UPDATE
                    B_BULK_EXCEL_TASK
                SET
                    TASK_STATUS      = :TASK_STATUS,
                    LAST_UPDATE_USER = :LAST_UPDATE_USER
                WHERE
                    TASK_ID = :TASK_ID
                AND
                    DISUSE_FLAG = 0
                ";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                              array('B_BULK_EXCEL_TASK', 'B_BULK_EXCEL_TASK_RIC', basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }
        $res = $objQuery->sqlBind(
            array(
                "TASK_STATUS"      => $status,
                "LAST_UPDATE_USER" => LAST_UPDATE_USER,
                "TASK_ID"          => $taskId,
            )
        );
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                                          array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC',
                                                          basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        // 履歴テーブルに登録

        // JOURNAL_SEQ_NO
        $jnlSeqNo = getSequenceIDForBackyards("B_BULK_EXCEL_TASK_JSQ");
        if (empty($jnlSeqNo)) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                                          array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC',
                                                          basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        // JOURNAL_ACTION_CLASS
        $jnlActClass = "UPDATE";
        $sql = "SELECT
                    TASK_ID, TASK_STATUS, TASK_TYPE, ABOLISHED_TYPE, FILE_NAME, RESULT_FILE_NAME, EXECUTE_USER, NOTE, DISP_SEQ, ACCESS_AUTH, DISUSE_FLAG, LAST_UPDATE_USER, LAST_UPDATE_TIMESTAMP
                FROM
                    B_BULK_EXCEL_TASK
                WHERE
                    TASK_ID = :TASK_ID
                AND
                    DISUSE_FLAG = 0";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                              array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
                        outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }
        $res = $objQuery->sqlBind(
            array(
                "TASK_ID" => $taskId
            )
        );
        $res = $objQuery->sqlExecute(
            array(
                "TASK_ID" => $taskId
            )
        );
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        $taskInfo = array();
        while ($row = $objQuery->resultFetch()) {
            $taskInfo[] = $row;
        }

        if (empty($taskInfo)) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        $taskInfo = $taskInfo[0];

        $sql = "INSERT INTO
                    B_BULK_EXCEL_TASK_JNL
                    (JOURNAL_SEQ_NO,
                    JOURNAL_REG_DATETIME,
                    JOURNAL_ACTION_CLASS,
                    TASK_ID,
                    TASK_STATUS,
                    TASK_TYPE,
                    ABOLISHED_TYPE,
                    FILE_NAME,
                    RESULT_FILE_NAME,
                    EXECUTE_USER,
                    DISP_SEQ,
                    ACCESS_AUTH,
                    NOTE,
                    DISUSE_FLAG,
                    LAST_UPDATE_TIMESTAMP,
                    LAST_UPDATE_USER)
                VALUES
                    (:JOURNAL_SEQ_NO,
                    :JOURNAL_REG_DATETIME,
                    :JOURNAL_ACTION_CLASS,
                    :TASK_ID,
                    :TASK_STATUS,
                    :TASK_TYPE,
                    :ABOLISHED_TYPE,
                    :FILE_NAME,
                    :RESULT_FILE_NAME,
                    :EXECUTE_USER,
                    :DISP_SEQ,
                    :ACCESS_AUTH,
                    :NOTE,
                    :DISUSE_FLAG,
                    :LAST_UPDATE_TIMESTAMP,
                    :LAST_UPDATE_USER)";
        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                              array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC', basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        $now = date("Y-m-d H:i:s");
        $res = $objQuery->sqlBind(
            array(
                "JOURNAL_SEQ_NO"        => $jnlSeqNo,
                "JOURNAL_REG_DATETIME"  => date("Y-m-d H:i:s"),
                "JOURNAL_ACTION_CLASS"  => $jnlActClass,
                "TASK_ID"               => $taskInfo["TASK_ID"],
                "TASK_STATUS"           => $status,
                "TASK_TYPE"             => $taskInfo["TASK_TYPE"],
                "ABOLISHED_TYPE"        => $taskInfo["ABOLISHED_TYPE"],
                "FILE_NAME"             => $taskInfo["FILE_NAME"],
                "RESULT_FILE_NAME"      => $taskInfo["RESULT_FILE_NAME"],
                "EXECUTE_USER"          => $taskInfo["EXECUTE_USER"],
                "DISP_SEQ"              => $taskInfo["DISP_SEQ"],
                "ACCESS_AUTH"           => $taskInfo["ACCESS_AUTH"],
                "NOTE"                  => $taskInfo["NOTE"],
                "DISUSE_FLAG"           => $taskInfo["DISUSE_FLAG"],
                "LAST_UPDATE_TIMESTAMP" => $taskInfo["LAST_UPDATE_TIMESTAMP"],
                "LAST_UPDATE_USER"      => LAST_UPDATE_USER
            )
        );
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                                          array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC',
                                                          basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        // シーケンスの更新
        $res = updateSequenceIDForBackyards("B_BULK_EXCEL_TASK_JSQ");

        if ($res === false) {

            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                                          array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC',
                                                          basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        // トランザクションコミット
        if ( $objDBCA->transactionCommit() === false ){
            // 例外処理へ
            throw new Exception( $ErrorMsg );
        }
        // トランザクション終了
        $objDBCA->transactionExit();

        return true;
    }
    catch (Exception $e){
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        if ( $objDBCA->getTransactionMode() ) {
            $objDBCA->transactionRollBack();
        }
        return false;
    }
}

/**
* zipのファイルパスを登録
* 
* @param  int     $taskId     タスクID
* @param  int     $fileName   ファイル名
* @return boolean $result     true/false
*/
function updateExcelZipFileName($taskId, $fileName) {
    global $objMTS, $objDBCA, $db_model_ch;
    $sql = "UPDATE
                B_BULK_EXCEL_TASK
            SET
                FILE_NAME = :FILE_NAME
            WHERE
                TASK_ID = :TASK_ID
            AND
                DISUSE_FLAG = 0
            ";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                          array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC', basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    $res = $objQuery->sqlBind(
        array(
            "TASK_ID"   => $taskId,
            "FILE_NAME" => $fileName
        )
    );
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900053',
                                                      array('A_SEQUENCE', 'B_BULK_EXCEL_TASK_RIC',
                                                      basename(__FILE__), __LINE__)));
        outputLog(LOG_PREFIX, $objQuery->getLastError());
        return false;
    }
    return true;
}

/**
* タスクIDから履歴検索
* 
* @param  int    $taskId   タスクID
* @return string $result   INSERT/UPDATE
*/
function getJNLActClass($sequenceName){
    global $objDBCA, $objMTS;
    $result = "UPDATE";

    // 履歴テーブルから同じタスクIDの履歴を検索

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
        $result = $row;
    }

    return $result;
}

/********************************************
* エクスポート系
********************************************/
/**
 * loadTableの情報取得
 *
 * @param  string    $strMenuIdNumeric    メニューID
 * @return array     $result              オブジェクトテーブル
 */
function getInfoOfLoadTable($strMenuIdNumeric, $aryVariant=array(), &$arySetting=array()){

    $aryValues = array();
    $intErrorType = null;
    $strErrMsg = "";

    $strFxName = __FUNCTION__; // getInfoOfRepresentativeFiles

    $registeredKey = "";
    $strLoadTableFullname = "";

    $objTable = null;

    if(file_exists(ROOT_DIR_PATH . "/webconfs/systems/{$strMenuIdNumeric}_loadTable.php")){
        $strLoadTableFullname = ROOT_DIR_PATH . "/webconfs/systems/{$strMenuIdNumeric}_loadTable.php";
    }
    else if(file_exists(ROOT_DIR_PATH . "/webconfs/sheets/{$strMenuIdNumeric}_loadTable.php")){
        $strLoadTableFullname = ROOT_DIR_PATH . "/webconfs/sheets/{$strMenuIdNumeric}_loadTable.php";
    }
    else if(file_exists(ROOT_DIR_PATH . "/webconfs/users/{$strMenuIdNumeric}_loadTable.php")){
        $strLoadTableFullname = ROOT_DIR_PATH . "/webconfs/users/{$strMenuIdNumeric}_loadTable.php";
    }
    else{
        outputLog(LOG_PREFIX, "loadTable with menuId[{$strMenuIdNumeric}] does not exists.");
        // 例外処理へ
        throw new Exception();
    }

    require_once($strLoadTableFullname);
    $registeredKey = $strMenuIdNumeric;

    if( 0 < strlen($registeredKey) ){
        $objTable = loadTable($registeredKey);

        if($objTable === null){
            // 00_loadTable.phpの読込失敗
            $intErrorType = 101;
            $strErrMsg = "[" . $strLoadTableFullname . "] Analysis Error";
            return false;
        }

        return $objTable;
    }
}

/********************************************
* メニューID系
********************************************/
/**
* エクスポートするメニューIDの一覧取得
* 
* @param  string $taskID
* @return array  $result メニューIDたち
*/
function getExportedMenuIDList($taskId) {
    global $g;
    if (!file_exists(EXPORT_PATH."/".$taskId.'/MENU_ID_LIST')) {
        return false;
    }
    $json = file_get_contents(EXPORT_PATH."/".$taskId.'/MENU_ID_LIST');
    $menuIdAry = json_decode($json, true);
    return $menuIdAry;
}

/********************************************
* 汎用系
********************************************/
/**
* sqlを実行する(簡単なSELECT系)
* 
* @param  string $taskID
* @return array  $result メニューIDたち
*/
function executeSQL($sql) {
    global $objDBCA, $objMTS;

    if (LOG_LEVEL === 'DEBUG') {
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-STD-900009',
                                          array(basename(__FILE__), __LINE__)));
    }

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

    $result = array();
    while ($row = $objQuery->resultFetch()) {
        $result[] = $row;
    }

    return $result;
}

/**
* SCSVに変換する必要があるか
* @param  string   $menuId     メニューID
* @param  array    $objTable
* @param  array    $aryVariant フィルタ
* @return array    $dumpInfo    dump用情報
**/
function getDumpFormat($menuId, $objTable, $aryVariant){
    global $g, $objDBCA, $objMTS;

    $menuRows = getMenuRows($objTable, $aryVariant);
    $is_scsv  = isSCSV($menuRows, $menuId);

    if ($is_scsv) {
        $dumpInfo = array(
            "filter_data" => array(
            ),
            "filteroutputfiletype" => "csv",
            "FORMATTER_ID"         => "csv",
        );
    } else {
        $dumpInfo = array(
            "filter_data" => array(
            ),
            "filteroutputfiletype" => "excel",
            "FORMATTER_ID"         => "excel",
        );
    }

    return $dumpInfo;
}

/**
* Excelファイルのエクスポート
* 
* libs/webcommonlibs/table_controle_agent/08_dumpToFile.phpからコピー＆改修
* @param    
* @return   string   $filePath   エクセルファイルのパス
**/
    function exportBulkExcelData($task, $menuGroupId, $menuId, $aryToArea, $objTable, $taskId, $dumpInfo=array(), $aryVariant=array(), &$arySetting=array(), $strApiFlg=false){
        global $g, $objMTS, $objDBCA;

        $g["login_id"] = $task["EXECUTE_USER"];

        $strPrivilege = getPrivilegeAuthByUserId($menuId, $task["EXECUTE_USER"]);
        $g["privilege"] = $strPrivilege;
        $g["menu_id"] = $menuId;

        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        $intControlDebugLevel02=250;

        $varRetBody = null;
        $aryUploadFile = array();

        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";

        $aryFxResultErrMsgBody = array();

        $intErrorPlaceMark = null;
        $strErrorPlaceFmt = "%08d";

        $boolConDisposition = 1;
        $intUnixTime = time();

        $refRetKeyExists = null;
        $boolKeyExists = null;

        $strOutputFileType = null;

        $objFunction01ForOverride = null;
        $objFunction02ForOverride = null;
        $objFunction03ForOverride = null;
        $objFunction04ForOverride = null;

        $ACRCM_id = "UNKNOWN";
        // ローカル変数宣言----

        $strErrMsgBodyToHtmlUI = "";
        $strErrMsgBodyToWAL = "";

        $filterData = array($task, $menuGroupId, $menuId, $aryToArea, $objTable, $taskId);
        $boolBinaryDistinctOnDTiS = null;

        $strFxName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----出力先の取得(エラーメッセージの出力先などに関連するので、最初に判定)
            list($strToAreaType,$boolKeyExists) = isSetInArrayNestThenAssign($aryToArea,array('to_area_type'),null);
            switch($strToAreaType){
                case "toFile":
                case "toReturn":
                    break;
                default:
                    $strToAreaType = "toStd";
            }

            // 廃止情報
            switch ($task["ABOLISHED_TYPE"]) {
                case '2':
                    // 廃止情報の取得 廃止情報を除く
                    $arrayObjColumn = $objTable->getColumns();
                    foreach($arrayObjColumn as $objColumn){
                        if ($objColumn->getID() == "DISUSE_FLAG") {
                            $col_idsop = $objColumn->getIDSOP();
                        }
                    }
                    $aryVariant = array(
                        "search_filter_data" => array(
                            $col_idsop => array("0"),
                        )
                    );
                    break;
                case '3':
                    // 廃止情報の取得 廃止情報のみ
                    $arrayObjColumn = $objTable->getColumns();
                    foreach($arrayObjColumn as $objColumn){
                        if ($objColumn->getID() == "DISUSE_FLAG") {
                            $col_idsop = $objColumn->getIDSOP();
                        }
                    }
                    $aryVariant = array(
                        "search_filter_data" => array(
                            $col_idsop => array("1"),
                        )
                    );
                    break;
                default:
                    // 全てのレコード
                    break;
            }
            
            if (empty($dumpInfo)) {
                $dumpInfo = getDumpFormat($menuId, $objTable, $aryVariant);
            }

            //出力先の取得(エラーメッセージの出力先などに関連するので、最初に判定)----

            //----メニューIDの取得
            list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('system_variant_function','vars','ACRCM_id'),"");

            if( $boolKeyExists === false ){
                list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
            }
            //メニューIDの取得----

            //----権限の取得/判定
            list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('DTiS_PRIVILEGE'),null);
            if( $boolKeyExists === false ){
                list($strPrivilege,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('privilege'),null);
            }
            switch($strPrivilege){
                case "1":
                case "2":
                    break;
                default:
                    $intErrorType = 1;
                    $intErrorPlaceMark = 100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
            //権限の取得/判定----

            //----出力するファイル形式を判別する
            list($strOutputFileType,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('dumpDataFromTable','vars','strOutputFileType'),"");
            if( $strOutputFileType == "" ){
                list($strOutputFileType,$boolKeyExists) = isSetInArrayNestThenAssign($dumpInfo,array('filteroutputfiletype'),"");
                if( $boolKeyExists === false ){
                    $intErrorType = 601;
                    $intErrorPlaceMark = 200;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }

            switch($strOutputFileType){
                case "csv":
                    break;
                case "excel":
                    break;
                default:
                    $intErrorType = 601;
                    $intErrorPlaceMark = 300;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
            //出力するファイル形式を判別する----
            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorType = 501;
                $intErrorPlaceMark = 400;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }

            //----テーブル設定の調査
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorType = 501;
                $intErrorPlaceMark = 500;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            //テーブル設定の調査----

            list($strFormatterId,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('dumpDataFromTable','vars','strFormatterId'),"");
            if( $strFormatterId == "" ){
                list($strFormatterId,$boolKeyExists) = isSetInArrayNestThenAssign($dumpInfo,array('FORMATTER_ID'),"");
            }

            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "ListFormatter") !== true ){
                // ----リストフォーマッタクラスではない
                $intErrorType = 501;
                $intErrorPlaceMark = 600;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // リストフォーマッタクラスではない----
            }

            $optAllHidden=false;
            if( array_key_exists("optionAllHidden",$aryVariant) === true ){
                $optAllHidden = $aryVariant['optionAllHidden'];
            }

            //----PHPのタイムアウトを再設定する(単位は秒)
            $intDTFTimeLimit = $objTable->getFormatter($strFormatterId)->getGeneValue("intDTFTimeLimit",$refRetKeyExists);
            if( $intDTFTimeLimit===null && $refRetKeyExists===false ){
                $intDTFTimeLimit = $objTable->getGeneObject("intDTFTimeLimit",$refRetKeyExists);
            }
            if( $intDTFTimeLimit!==null && is_int($intDTFTimeLimit)===true ){
                set_time_limit($intDTFTimeLimit);
            }
            //PHPのタイムアウトを再設定する(単位は秒)----

            //----PHPのセッションごとの最大占有メモリの設定(単位はM(メガ))
            $intDTFMemoryLimit = $objTable->getFormatter($strFormatterId)->getGeneValue("intDTFMemoryLimit",$refRetKeyExists);
            if( $intDTFMemoryLimit===null && $refRetKeyExists===false ){
                $intDTFMemoryLimit = $objTable->getGeneObject("intDTFMemoryLimit",$refRetKeyExists);
            }

            if( $intDTFMemoryLimit!==null && is_int($intDTFMemoryLimit) ){
                ini_set("memory_limit",strval($intDTFMemoryLimit)."M");
            }
            //PHPのセッションごとの最大占有メモリの設定(単位はM(メガ))----

            //----[EXCEL]出力するコンテンツの種類、を判別する
            if( $strOutputFileType == "excel" ){
                $strPrintTypeMode = "normal";
                $boolNoSelectMode = false;
                list($tmpStrRequestUserClass,$boolKeyExists) = isSetInArrayNestThenAssign($dumpInfo,array('requestuserclass'),null);
                if( $boolKeyExists === true ){
                    if( $tmpStrRequestUserClass == "forDeveloper" ){
                        list($tmpVarDevLogDeveloper,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('dev_log_developer'),null);
                        //
                        if( 1 <= $tmpVarDevLogDeveloper ){
                            //----開発者用エクセル
                            $strPrintTypeMode = "forDeveloper";
                            $boolNoSelectMode = true;
                        }
                        else{
                            // 権限が不足している
                            $intErrorType = 1;
                            $intErrorPlaceMark = 700;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        unset($tmpVarDevLogDeveloper);
                    }
                    else if( $tmpStrRequestUserClass == "visitor" ){
                        // SafeCSV用作成用のエクセル
                        $strPrintTypeMode = "forVisitor";
                        $boolNoSelectMode = true;
                    }
                    unset($tmpStrRequestUserClass);
                    //
                    //開発者用エクセルの出力をするか----
                }
                else{
                    list($tmpStrRequestClass,$boolKeyExists) = isSetInArrayNestThenAssign($dumpInfo,array('requestcontentclass'),null);
                    if( $boolKeyExists === true ){
                        if( $tmpStrRequestClass == "noselect" ){
                            // 新規登録用エクセル
                            $boolNoSelectMode = true;
                        }
                        else{
                            $intErrorType = 601;
                            $intErrorPlaceMark = 800;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    unset($tmpStrRequestClass);
                }
            }
            //[EXCEL]出力するコンテンツの種類、を判別する----

            $aryFunctionForOverride = $objTable->getGeneObject("functionsForOverride", $refRetKeyExists);
            $arrayObjColumn = $objTable->getColumns();

            //----SQL作成
            if( array_key_exists("directSQL",$aryVariant) === true ){
                //----直で実行するSQLが指定された
                $sql = $aryVariant['directSQL']['sqlBody'];
                if(isset($aryVariant['directSQL']['bindBody'])===true){
                    $arrayFileterBody = $aryVariant['directSQL']['bindBody'];
                }else{
                    $arrayFileterBody = array();
                }
                //直で実行するSQLが指定された----
            }
            else{
                //----通常モード
                if( array_key_exists("search_filter_data",$aryVariant) === true ){
                    //----検出条件が指定された場合

                    //----大文字小文字と全角半角を無視する設定かを調べる
                    $boolBinaryDistinctOnDTiS = $objTable->getFormatter($strFormatterId)->getGeneValue("binaryDistinctOnDTiS",$refRetKeyExists);
                    if( $boolBinaryDistinctOnDTiS === null && $refRetKeyExists === false ){
                        $boolBinaryDistinctOnDTiS = $objTable->getGeneObject("binaryDistinctOnDTiS",$refRetKeyExists);
                    }
                    if( is_bool($boolBinaryDistinctOnDTiS) === false ){
                        $boolBinaryDistinctOnDTiS = false;
                    }

                    // 大文字小文字と全角半角を無視する設定かを調べる----

                    //----検出条件、を解析する

                    $filterData = $aryVariant['search_filter_data'];

                    if( isset($aryVariant["TCA_PRESERVED"])===false ){
                        $aryVariant["TCA_PRESERVED"] = array();
                    }
                    $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]=array("ACTION_MODE"=>"DTiS_currentPrint");
                    $aryVariant["TCA_PRESERVED"]["userRawInput"] = $filterData;

                    //----必須チェックなどを事前にしたい場合は、ここで差し替え
                    if( $aryFunctionForOverride !== null ){
                         list($tmpObjFunction01ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"DTiSFilterCheckValid"),null);
                         unset($tmpBoolKeyExist);
                         if( is_callable($tmpObjFunction01ForOverride) === true ){
                             $objFunction01ForOverride = $tmpObjFunction01ForOverride;
                         }
                         unset($tmpObjFunction01ForOverride);
                    }
                    //必須チェックなどを事前にしたい場合は、ここで差し替え----

                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeDTiSValidateCheck($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                        if( $arrayTmp[0] === false ){
                            $intErrorType = $arrayTmp[1];
                            $intErrorPlaceMark = 900;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    //----バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）
                    if( $objFunction01ForOverride === null ){
                        // $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting);
                        $tmpAryRet = DTiSFilterCheckValid($objTable, $strFormatterId, $filterData, $aryVariant, $arySetting, $strApiFlg);
                    }
                    else{
                        $tmpAryRet = $objFunction01ForOverride($objTable, $strFormatterId, $filterData, $aryVariant);
                    }
                    if( $tmpAryRet[1] !== null ){
                        $intErrorType = $tmpAryRet[1];
                        $aryFxResultErrMsgBody = $tmpAryRet[2];
                        $intErrorPlaceMark = 1000;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //バリデーションチェックは、かならず、あいまいモードにする前に行うこと(IDColumnの問題があるので）----

                    //検出条件、を解析する----

                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->beforeDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                        if( $arrayTmp[0] === false ){
                            $intErrorType = $arrayTmp[1];
                            $intErrorPlaceMark = 1100;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }

                    if( $boolBinaryDistinctOnDTiS === true ){
                        //----全角半角を区別しない、という設定ではない（loadTableで例外的な設定がされている）
                        $boolFocusRet= dbSearchResultNormalize();
                        if( $boolFocusRet === false ){
                            $intErrorType = 602;
                            $intErrorPlaceMark = 1200;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        // 全角半角を区別しない、という設定ではない（loadTableで例外的な設定がされている）----
                    }
                    else{
                        //----全角半角を区別しない、という設定（デフォルト）

                        //----DB面で、大文字小文字と全角半角を無視する設定
                        $boolFocusRet= dbSearchResultExpand();
                        if( $boolFocusRet === false ){
                            $intErrorType = 603;
                            $intErrorPlaceMark = 1300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        //DB面で、大文字小文字と全角半角を無視する設定----

                        //全角半角を区別しない、という設定（デフォルト）----
                    }

                    //検出条件が指定された場合----
                }else{
                    //----検出条件が指定されなかった場合
                    $boolBinaryDistinctOnDTiS = false;
                    //検出条件が指定されなかった場合----
                }

                $arrayFileterBody = $objTable->getFilterArray($boolBinaryDistinctOnDTiS);

                // ----generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]
                $sql = generateSelectSql2(2, $objTable, $boolBinaryDistinctOnDTiS);
                // generateSelectSql2呼び出し[Where句に各カラムの名前が記述され、値の部分が置換される前のSQLが作成される]----

                //通常モード----
            }

            //SQL作成----

            if( $strOutputFileType == "csv" ){
                if( $optAllHidden === true ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $strTmpFilename = makeUniqueTempFilename($g['root_dir_path'] . "/temp", "temp_csv_dl" . date("YmdHis", $intUnixTime) . "_" . mt_rand());
                
                $strCsvHeaderStream = "";
                
                $strPrintOrderCsvFormatterId = $strFormatterId;
                
                $objCsvFormatter = $objTable->getFormatter($strPrintOrderCsvFormatterId);
                
                if( is_a($objCsvFormatter, "CSVFormatter") === false ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                if( $objCsvFormatter->getGeneValue("csvFieldRowHide",$refRetKeyExists) !== false ){
                    $objCsvFormatter->setGeneValue("csvFieldRowAdd",true);
                    $objCsvFormatter->setGeneValue("csvRecordShowAdd",false);
                    $strCsvHeaderStream .= $objTable->getPrintFormat($strFormatterId);
                }
                $strCSVOutputFileType = $objCsvFormatter->getGeneValue("outputFileType",$refRetKeyExists);
                if( $strCSVOutputFileType == "SafeCSV" ){
                    $strDLFilename = $objCsvFormatter->makeLocalFileName(".scsv",$intUnixTime);
                }
                else{
                    $strCSVOutputFileType = "NormalCSV";
                    $strDLFilename = $objCsvFormatter->makeLocalFileName(".csv",$intUnixTime);
                }
                if( $strDLFilename === null ){
                    $intErrorType = 501;
                    $intErrorPlaceMark = 1800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                if( $aryFunctionForOverride !== null ){
                    list($tmpObjFunction03ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"writeToFile"),null);
                    unset($tmpBoolKeyExist);
                    if( is_callable($tmpObjFunction03ForOverride) === true ){
                        $objFunction03ForOverride = $tmpObjFunction03ForOverride;
                    }
                    unset($tmpObjFunction03ForOverride);
                }
                //----暫定ファイルへ本体行を書き込み
                
                //----暫定ファイルの作成
                if( $objCsvFormatter->fileOpen($strTmpFilename) !== true ){
                    $intErrorType = 604;
                    $intErrorPlaceMark = 1900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                if( $objCsvFormatter->fileStreamAdd($strCsvHeaderStream) !== true ){
                    $intErrorType = 604;
                    $intErrorPlaceMark = 2000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                $tmpAryRet = $objCsvFormatter->writeToFile($sql,
                                                           $arrayFileterBody,
                                                           $objTable,
                                                           $objFunction03ForOverride,
                                                           $strFormatterId,
                                                           $filterData,
                                                           $aryVariant,
                                                           $arySetting);

                if( $tmpAryRet[1] !== null ){
                    $intErrorType = $tmpAryRet[1];
                    $aryFxResultErrMsgBody = $tmpAryRet[2];
                    unset($tmpAryRet);
                    $intErrorPlaceMark = 2100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $num_rows = $tmpAryRet[0];

                if( array_key_exists("directSQL",$aryVariant) === true ){
                    //----直で実行するSQLが指定された
                    //直で実行するSQLが指定された----
                }
                else{
                    //----通常モード
                    if( $boolBinaryDistinctOnDTiS === true ){
                    }else{
                        $boolFocusRet = dbSearchResultNormalize();
                        if($boolFocusRet === false){
                            $intErrorType = 500;
                            $intErrorPlaceMark = 2200;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    
                    foreach($arrayObjColumn as $objColumn){
                        $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                        if( $arrayTmp[0] === false ){
                            $intErrorType = $arrayTmp[1];
                            $intErrorPlaceMark = 2300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //通常モード----
                }
                
                if( $objCsvFormatter->fileClose() !== true ){
                    $intErrorType = 604;
                    $intErrorPlaceMark = 2400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                //暫定ファイルへ本体行を書き込み----

                //暫定ファイルの作成----

                dev_log("output.before:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);

                if( $strToAreaType == "toFile" ){
                    $varRetBody = $strTmpFilename;
                }
                else if( $strToAreaType == "toStd" ){
                    if( headers_sent() === true ){
                        $intErrorType = 605;
                        $intErrorPlaceMark = 2500;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    // ----MIMEタイプの設定
                    // MIMEタイプの設定----

                    // 標準出力へ出力
                    $strCsvHeaderStream = file_get_contents( $strTmpFilename );

                    $menuInfo = getMenuInfoByMenuId($menuId);
                    $menuGroupId = $menuInfo["MENU_GROUP_ID"];
                    $tmpMenuGroupNameAry = explode("/", $menuInfo["MENU_GROUP_NAME"]);
                    $menuGroupName = implode("_", $tmpMenuGroupNameAry);
                    $dirName = $menuGroupId."_".$menuGroupName;
                    // windowsのフォルダ名制限対策で200文字以内に収める
                    if (strlen($dirName) > 200) {
                        $dirName = substr($dirName, 0, 200);
                    }

                    $strCsvHeaderStream .= $objTable->getPrintFormat($strFormatterId);
                    if (!is_dir($g["root_dir_path"]."/temp/bulk_excel/export/$taskId/tmp_zip/")) {
                        $res = mkdir($g["root_dir_path"]."/temp/bulk_excel/export/$taskId/tmp_zip/");
                        if ($res == false) {
                            throw new Exception("Error Processing Request", 1);
                        }
                    }
                    if (!is_dir($g["root_dir_path"]."/temp/bulk_excel/export/$taskId/tmp_zip/$dirName")) {
                        $res = mkdir($g["root_dir_path"]."/temp/bulk_excel/export/$taskId/tmp_zip/$dirName");
                        if ($res == false) {
                            throw new Exception("Error Processing Request", 1);
                        }
                    }
                    $tmpFilePath = $g["root_dir_path"]."/temp/bulk_excel/export/$taskId/tmp_zip/$dirName/$strDLFilename";
                    file_put_contents($tmpFilePath, $strCsvHeaderStream);

                    return $tmpFilePath;
                }
                else{
                    $intErrorType = 501;
                    $intErrorPlaceMark = 2600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                dev_log("output.after:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);

                //CSV出力----
            }else{
                // ----デフォルト（EXCELでの出力）

                $strTmpFilename = makeUniqueTempFilename($g['root_dir_path'] . "/temp", "temp_excel_dl" . date("YmdHis", $intUnixTime) . "_" . mt_rand());

                list($intXlsLimit,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('dumpDataFromTable','vars','intXlsLimit'),"");
                if( $intXlsLimit == "" ){
                    list($intXlsLimit,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_xls_limit'),null);
                }

                if( $optAllHidden !== true ){
                    if( $strPrintTypeMode === "forDeveloper" || $strPrintTypeMode == "forVisitor" ){
                        $strLinkExcelFormatterIDOnNN = $objTable->getFormatter($strFormatterId)->getGeneValue("linkExcelFormatterIDOnNN",$refRetKeyExists); //NotNormal
                        if( $strLinkExcelFormatterIDOnNN===null && $refRetKeyExists===false ){
                            $strLinkExcelFormatterIDOnNN = $objTable->getGeneObject("linkExcelFormatterIDOnNN",$refRetKeyExists); //NotNormal
                        }
                        if( $strLinkExcelFormatterIDOnNN===null ){
                            $strLinkExcelFormatterIDOnNN="excel";
                        }
                        
                        $objExcelFormatter = $objTable->getFormatter($strLinkExcelFormatterIDOnNN);
                        if( $objExcelFormatter === null ){
                            $intErrorType = 501;
                            $intErrorPlaceMark = 2700;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $strPrintOrderExcelFormatterId = $strLinkExcelFormatterIDOnNN;
                    }
                    else{
                        $strPrintOrderExcelFormatterId = $strFormatterId;
                    }

                    $objExcelFormatter = $objTable->getFormatter($strPrintOrderExcelFormatterId);

                    if( is_a($objExcelFormatter, "ExcelFormatter") === false ){
                        $intErrorType = 501;
                        $intErrorPlaceMark = 2800;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $objExcelFormatter->cashModeAdjust();

                    // ----XLSXファイル名の設定
                    $strDLFilename = $objExcelFormatter->makeLocalFileName(".xlsx",$intUnixTime);

                    if( $strDLFilename === null ){
                        $intErrorType = 501;
                        $intErrorPlaceMark = 2900;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    // XLSXファイル名の設定----

                    //----出力先を確定
                    if( $strToAreaType == "toFile" ){
                        // ファイルへ出力
                        $strTmpFilename = "$strTmpFilename";
                        $objExcelFormatter->setExportFilePath($strTmpFilename);
                    }
                    else if( $strToAreaType == "toStd" ){
                        // 標準出力へ出力
                        // $objExcelFormatter->setExportFilePath($strTmpFilename);
                        $objExcelFormatter->setExportFilePath("php:/"."/output");
                    }
                    else{
                        $intErrorType = 501;
                        $intErrorPlaceMark = 3000;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //出力先を確定----

                    if( $boolNoSelectMode === false ){
                        $intFetchCount = 0;

                        $tmpRefBoolSetting = true;
                        $objExcelFormatter->getSheetNameForEditSheet($tmpRefBoolSetting);
                        if( $objExcelFormatter===false ){
                            $intErrorType = 501;
                            $intErrorPlaceMark = 3100;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        unset($tmpRefBoolSetting);

                        dev_log("add.before:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);
                        $objExcelFormatter->workBookCreate();
                        $objExcelFormatter->editHelpWorkSheetAdd();
                        $objExcelFormatter->editWorkSheetHeaderCreate();
                        dev_log("sql.before:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);

                        if( $aryFunctionForOverride !== null ){
                            list($tmpObjFunction03ForOverride,$tmpBoolKeyExist)=isSetInArrayNestThenAssign($aryFunctionForOverride,array("dumpDataFromTable",$strFormatterId,"selectResultFetch"),null);
                            unset($tmpBoolKeyExist);
                            if( is_callable($tmpObjFunction03ForOverride) === true ){
                                $objFunction03ForOverride = $tmpObjFunction03ForOverride;
                            }
                            unset($tmpObjFunction03ForOverride);
                        }

                        $tmpAryRet = $objExcelFormatter->selectResultFetch($sql,
                                                                           $arrayFileterBody,
                                                                           $objTable,
                                                                           $intXlsLimit,
                                                                           $objFunction03ForOverride,
                                                                           $strFormatterId,
                                                                           $filterData,
                                                                           $aryVariant,
                                                                           $arySetting);

                        if( $tmpAryRet[1] !== null ){
                            $intErrorType = $tmpAryRet[1];
                            $aryFxResultErrMsgBody = $tmpAryRet[2];
                            unset($tmpAryRet);
                            $intErrorPlaceMark = 3200;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $num_rows = $tmpAryRet[0];

                        if( array_key_exists("directSQL",$aryVariant) === true ){
                            //----直で実行するSQLが指定された
                            //直で実行するSQLが指定された----
                        }
                        else{
                            //----通常モード
                            if( $boolBinaryDistinctOnDTiS === true ){
                            }else{
                                $boolFocusRet= dbSearchResultNormalize();
                                if($boolFocusRet === false){
                                    $intErrorType = 500;
                                    $intErrorPlaceMark = 3300;
                                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                            }
                            //
                            foreach($arrayObjColumn as $objColumn){
                                $arrayTmp = $objColumn->afterDTiSAction($strFormatterId, $boolBinaryDistinctOnDTiS, $filterData, $aryVariant);
                                if( $arrayTmp[0] === false ){
                                    $intErrorType = $arrayTmp[1];
                                    $intErrorPlaceMark = 3400;
                                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                                }
                            }
                            //通常モード----
                        }
                        $objExcelFormatter->editWorkSheetRecordAdd();
                        $objExcelFormatter->editWorkSheetTailerFix();
                        // エクセル出力,全件ダウンロードの場合
                        $objExcelFormatter->ValidationDataWorkSheetAdd(); // カラム列範囲ヴァリデーション
                        dev_log("add.after:memory_get_peak_usage(TRUE):".memory_get_peak_usage(TRUE),$intControlDebugLevel02);
                    }
                    else{
                        $num_rows = 0;

                        if( $strPrintTypeMode === "forDeveloper" || $strPrintTypeMode == "forVisitor" ){
                            //----特殊エクセルのダウンロードの場合
                            $objExcelFormatter->setGeneValue("minorPrintTypeMode",$strPrintTypeMode);
                            $objExcelFormatter->setPrintTargetListFormatterID($strFormatterId);
                            //特殊エクセルのダウンロードの場合----
                        }
                        $objExcelFormatter->workBookCreate();
                        $objExcelFormatter->editHelpWorkSheetAdd();
                        $objExcelFormatter->editWorkSheetHeaderCreate();
                        $objExcelFormatter->editWorkSheetRecordAdd();
                        $objExcelFormatter->editWorkSheetTailerFix();

                        // 新規登録の場合
                        $objExcelFormatter->ValidationDataWorkSheetTailerFix();// カラム列範囲ヴァリデーション
                    }
                }
                else{
                    $num_rows = 0;
                }

                $boolFileDumpExecute = true;

                if( $intXlsLimit !== null && $intXlsLimit < $num_rows ){
                    //----ダウンロード制限行数を超えた
                    if( $strPrintTypeMode != "forDeveloper" && $strPrintTypeMode != "forVisitor" ){
                        //----通常のエクセルのダウンロードの場合
                        $boolFileDumpExecute = false;
                        //通常のエクセルのダウンロードの場合----
                    }
                    else{
                        //----開発者用エクセルのダウンロードの場合
                        //開発者用エクセルのダウンロードの場合----
                    }
                    //ダウンロード制限行数を超えた----
                }

                if( $boolFileDumpExecute === false ){
                    //----ダウンロード制限行数を超えた
                    $intErrorType = 301;
                    $intErrorPlaceMark = 3600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //ダウンロード制限行数を超えた----
                }
                else{
                    // ----デフォルト（EXCELでの出力）

                    if( $strToAreaType == "toStd" ){
                        // ----MIMEタイプの設定
                        printHeaderForProvideFileStream($strDLFilename,"",null);
                        // MIMEタイプの設定----
                    }

                    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);

                    //----このメソッド内で、出力する。
                    if ($dumpInfo["filteroutputfiletype"] == "excel" && $dumpInfo["FORMATTER_ID"] == "csv") {
                        $result = $objTable->getPrintFormatBulkExcel("excel", null, null, $taskId, $strDLFilename, $menuId);
                    } else {
                        $result = $objTable->getPrintFormatBulkExcel($strFormatterId, null, null, $taskId, $strDLFilename, $menuId);
                    }
                    return $result;
                }
            }
        }
        catch (Exception $e){
            //----エラー発生時

            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);

            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorType){
                case 1: // 表示権限がない
                    // 表示権限がありません。
                    $strErrMsgBodyToHtmlUI = $objMTS->getSomeMessage("ITAWDCH-ERR-311");
                    break;
                case 2: // バリデーションエラー系
                    // バリデーションエラーが発生しました。
                    $strErrMsgBodyToHtmlUI = $objMTS->getSomeMessage("ITAWDCH-ERR-312");
                    break;
                case 301: // ダウンロード制限行数を超えた
                    $strErrMsgBodyToHtmlUI = $objMTS->getSomeMessage("ITAWDCH-ERR-313",array($num_rows, $intXlsLimit));
                    // "WARNING:DETAIL:(DUMP TO FILE. MENU:[｛｝] TYPE EXCEL) DOWNLOAD LIMIT OVER. ";
                    $strErrMsgBodyToWAL = $objMTS->getSomeMessage("ITAWDCH-ERR-301",$ACRCM_id);
                    break;
                default: // システムエラーが発生しました。
                    $strErrMsgBodyToHtmlUI = $objMTS->getSomeMessage("ITAWDCH-ERR-3001",$intErrorType);
                    // ERROR:UNEXPECTED, DETAIL:(DUMP TO FILE. MENU:[｛｝] PRINTMODE:[｛｝] ERROR[｛｝]) 
                    $strErrMsgBodyToWAL = $objMTS->getSomeMessage("ITAWDCH-ERR-303",array($ACRCM_id, $strOutputFileType, $intErrorType));
                    break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----

            $aryErrMsgBody[] = $strErrMsgBodyToHtmlUI;

            // アクセスログへ記録
            if( 0 < strlen($strErrMsgBodyToWAL) ) outputLog(LOG_PREFIX, $strErrMsgBodyToWAL);

            //エラー発生時----
        }
        //----大量行のダウンロードに備えて、タイムリミットを「30」に戻す
        //set_time_limit(30);
        //大量行のダウンロードに備えて、タイムリミットを「30」に戻す----

        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        return array($varRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg, $aryUploadFile);
    }
    
    /**
    * エクセルまとめzipをインポートする
    * 
    * @param int    $taskId    タスクID
    * @param        $objTable
    * @param array  $files     ファイル情報
    * @param string $menuId    メニューID
    * @param int    $userId    ユーザID
    * libs/webcommonlibs/table_controle_agent/07_TableIUbyFIle.phpからコピー＆改修
    */
    function importBulkExcel($taskId, $objTable, $files, $menuId, $userId, &$aryVariant=array(), &$arySetting=array()){
        global $g, $objDBCA, $objMTS;

        require_once(ROOT_DIR_PATH."/libs/webcommonlibs/table_control_agent/99_functions2.php");
        require_once(ROOT_DIR_PATH."/libs/webcommonlibs/table_control_agent/03_registerTable.php");
        require_once(ROOT_DIR_PATH."/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
        require_once(ROOT_DIR_PATH."/libs/webcommonlibs/table_control_agent/04_updateTable.php");

        $g["login_id"] = $userId;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;

        $pblStrFileAllTailMarks = "";

        //----受付拡張子(エクセル)の設定
        $pblStrExcelFileTailMarks = ".xlsx,.xlsm";
        //受付拡張子(エクセル)の設定----

        //----受付拡張子(CSV系)の設定
        $pblStrCsvFileTailMarks = ".csv,.scsv";
        //受付拡張子(CSV系)の設定----

        $ret_str = '';
        $intErrorStatus = 0;
        $modeFileCh = -1;
        $upOrgFilename = '';

        $ACRCM_id = "UNKNOWN";

        $refRetKeyExists = false;
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        try{
            //----メニューIDの取得
            list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('system_variant_function','vars','ACRCM_id'),"");
            if( $boolKeyExists === false ){
                $ACRCM_id = $menuId;
                $boolKeyExists = true;
            }
            //メニューIDの取得----

            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                $intErrorStatus = 501;
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }

            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                $intErrorStatus = 501;
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }

            $strFormatterId = "all_dump_table";
            $objListFormatter = $objTable->getFormatter($strFormatterId);
            if( is_a($objListFormatter, "QMFileSendAreaFormatter") !== true ){
                // ----QMFileSendAreaFormatterクラスではない
                $intErrorStatus = 501;
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // QMFileSendAreaFormatterクラスではない----
            }
            //テーブル設定の調査----

            $varErrorOfFileupload = $files['file']['error'];

            if( $varErrorOfFileupload != 0 ){
                //----1:php.iniによるファイルサイズ超過/2:name属性MAX_FILE_SIZEによるファイルサイズ超過
                $intErrorStatus = 201;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //1:php.iniによるファイルサイズ超過/2:name属性MAX_FILE_SIZEによるファイルサイズ超過----
            }
            else{
                $upTmpFileFullname = $files['file']['tmp_name'];
                $upOrgFilename = $files['file']['name'];
                dev_log("[{$upTmpFileFullname}] is uploaded_file.", $intControlDebugLevel01);
            }

            if( $modeFileCh == -1 ){

                $flag_ExcelHidden = $objTable->getFormatter($strFormatterId)->getGeneValue("linkExcelHidden",$refRetKeyExists);
                if( $flag_ExcelHidden===null && $refRetKeyExists===false ){
                    $flag_ExcelHidden = $objTable->getGeneObject("linkExcelHidden",$refRetKeyExists);
                }
                if( $flag_ExcelHidden !== true ){
                    //----全件DL領域で、エクセルを無条件で隠す設定ではない場合
                    $pblStrFileAllTailMarks.=$pblStrExcelFileTailMarks;
                    foreach(explode(",",$pblStrExcelFileTailMarks) as $tailFileMark){
                        if(mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0){
                            $modeFileCh = 0;
                            break;
                        }
                    }
                    //全件DL領域で、エクセルを無条件で隠す設定ではない場合----
                }
                else{
                    //----全件DL領域で、エクセルを無条件で隠す設定の場合
                    foreach(explode(",",$pblStrExcelFileTailMarks) as $tailFileMark){
                        if(mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0){
                            $intErrorStatus = 203;
                            throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //全件DL領域で、エクセルを無条件で隠す設定の場合----
                }
            }


            if( $modeFileCh == -1 ){
                //----ロードテーブルからの設定を取得
                $flag_CSVShow = $objTable->getFormatter($strFormatterId)->getGeneValue("linkCSVFormShow",$refRetKeyExists);
                if( $flag_CSVShow===null && $refRetKeyExists===false ){
                    $flag_CSVShow = $objTable->getGeneObject("linkCSVFormShow",$refRetKeyExists);
                }
                //ロードテーブルからの設定を取得----

                if( $flag_CSVShow !== false ){
                    //----無条件でCSVを隠す、という設定ではない
                    $rowLength = countTableRowLength($objTable, $aryVariant);
                    $intXlsLimit = isset($g['menu_xls_limit'])?$g['menu_xls_limit']:null;

                    if( $intXlsLimit !== null && $intXlsLimit < $rowLength ){
                        $flag_CSVShow = true;
                    }
                    //無条件でCSVを隠す、という設定ではない----
                }

                if( $flag_CSVShow === true ){
                    //----CSVが隠されていない場合
                    if( $pblStrFileAllTailMarks != "" ){
                        $pblStrFileAllTailMarks.=",";
                    }
                    $pblStrFileAllTailMarks.=$pblStrCsvFileTailMarks;
                    foreach(explode(",",$pblStrCsvFileTailMarks) as $tailFileMark){
                        if( mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0 ){
                            $modeFileCh = 1;
                            break;
                        }
                    }
                    //CSVが隠されていない場合----
                }
                else{
                    //----CSVが隠されている場合
                    foreach(explode(",",$pblStrExcelFileTailMarks) as $tailFileMark){
                        if( mb_strpos(strrev($upOrgFilename),strrev($tailFileMark),0,'UTF-8') === 0 ){
                            $intErrorStatus = 204;
                            throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    //CSVが隠されている場合----
                }
            }

            $extension = getExtension($files['file']['name']);
            if ($extension == "xlsx") {
                $modeFileCh = 0;
            } else if ($extension == "scsv") {
                $modeFileCh = 1;
            }
            
            $aryVariant['objTable'] = $objTable;
            $aryVariant['tableIUDByQMFile']  = array('vars'=>array('strUpTmpFileFullname'=>$upTmpFileFullname,'strOrgFileNameOfUpTmpFile'=>$upOrgFilename));

            $aryRetBody = tableIUDByQMFile($menuId, $userId, null, null, $modeFileCh, $strFormatterId, $aryVariant);

            $ret_str = $aryRetBody[0];

            // リザルトファイルの作成
            $menuInfo = getMenuInfoByMenuId($menuId);
            $msg = $menuInfo["MENU_GROUP_ID"]."_".$menuInfo["MENU_GROUP_NAME"].":".$menuId."_".$menuInfo["MENU_NAME"]."\n";

            $strErrCountExplainTail = $objMTS->getSomeMessage("ITAWDCH-ERR-285");

            $msg .= $objMTS->getSomeMessage("ITAWDCH-STD-451",$files['file']['name'])."\n";
            foreach ($aryRetBody[4] as $executeName => $detail) {
                $msg .= $detail['name'].":    ".$detail['ct'].$strErrCountExplainTail."\n";
            }
            foreach ($aryRetBody[5] as $cnt => $row) {
                if ($row[0] != "000") {
                    $msg .= "line: ".$cnt."  ".$row[2]."\n";
                }
            }

            dumpResultMsg($msg, $taskId);
            $intErrorStatus = $aryRetBody[1];
            
            if( $intErrorStatus !== null ){
                throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $intErrorStatus = 0;
            
            return array("result" => true);
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            outputLog(LOG_PREFIX, $tmpErrMsgBody);
            
            // ----一般訪問ユーザに見せてよいメッセージを作成
            switch($intErrorStatus){

                case 201 :
                    switch($varErrorOfFileupload){
                        case 1  : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-251");break;
                        case 2  : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-252");break; 
                        case 3  : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-253");break;
                        case 4  : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-254");break;
                        default : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-3001");break;
                    }
                    break;
                case 202 : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-1001");break;
                case 203 : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-1002");break;
                case 204 : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-1003");break;
                case 205 : $ret_str .= $objMTS->getSomeMessage("ITAWDCH-ERR-1004",$pblStrFileAllTailMarks);break; //受付外範囲の拡張子

                default : $ret_str .= getMessageFromResultOfTableIUDByQMFile($intErrorStatus,0);break;
            }
            // 一般訪問ユーザに見せてよいメッセージを作成----
            if( 0 < $userId ){
                //----ロードテーブルカスタマイズ向けメッセージを作成
                $tmp_DevStr = "";
                switch($intErrorStatus){
                    case 201 :
                        switch($varErrorOfFileupload){
                            case 6  : $tmp_DevStr .= $objMTS->getSomeMessage("ITAWDCH-ERR-255");break;
                            case 7  : $tmp_DevStr .= $objMTS->getSomeMessage("ITAWDCH-ERR-256");break;
                            case 8  : $tmp_DevStr .= $objMTS->getSomeMessage("ITAWDCH-ERR-257");break;
                            default : break;
                        }
                        break;
                    case 202 : case 203 : case 204 : break;

                    default : $tmp_DevStr = getMessageFromResultOfTableIUDByQMFile($intErrorStatus,1);break;
                }
                if( 0 < strlen($tmp_DevStr) ) dev_log($tmp_DevStr, $intControlDebugLevel01);
                unset($tmp_DevStr);
                //ロードテーブルカスタマイズ向けメッセージを作成----
            }
        }

        $response = array();
        $res["ret_str"] = $ret_str;
        $res["taskId"] = $taskId;
        $response['text'] = nl2br($ret_str);
        $response['error'] = $intErrorStatus;

        return array("result" => false, "response" => $response);
    }

    function getMessageFromResultOfTableIUDByQMFile($intErrorStatus,$intMode=0){
        global $objMTS;

        $retStrBody = "";

        if( $intMode == 0 ){
            switch($intErrorStatus){
                case   1 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1117");break; // メンテナンス権限がありません。

                case 351 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1112");break; // エクセル固有サイズover 旧(812)
                case 352 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1107");break; // エクセルのシート名が不正 旧(807)
                case 353 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1108");break; // 列が一致しません。最新のフォーマットを使用してください。 旧(808)

                case 361 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1113");break; // CSV系固有サイズover 旧(813)
                case 362 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1103");break; // ファイル内容が、ファイル拡張子（scsv）の形式と合致しません。 旧(803)
                case 363 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1104");break; // ファイル拡張子が（scsv）の場合の、CSV系ファイルでのファイルアップロード編集が許可されていません。旧(804)
                case 364 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1105");break; // ファイル拡張子が（scsv）の場合の、CSV系ファイルでのファイルアップロード編集が許可されていません。旧(805)
                case 365 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1106");break; // CSVファイルでのファイルアップロード編集が許可されていません。旧(806)
                case 366 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1109");break; // アップロード用のCSV系ファイルではありません。旧(809)
                case 367 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1110");break; // フォーマットが一致しません。最新版のCSV系ファイルを使用してください。旧(810)
                case 368 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1111");break; // 不正なフォーマットです。旧(811)

                case 371 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1114");break; // JSON固有サイズover
                case 372 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1115");break; // フォーマットが一致しません。最新版のJSONファイルを使用してください。
                case 373 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1116");break; // 不正なフォーマットです。

                case 801 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1101");break; // ファイルがアップロードされていません。
                case 802 : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-1102");break; // ファイルのアップロードでエラーが発生しました。

                default : $retStrBody = $objMTS->getSomeMessage("ITAWDCH-ERR-3001");break;
            }
        }
        else if( $intMode == 1 ){
            switch($intErrorStatus){
                case 351 : case 352 : case 353 : break;
                case 361 : case 362 : case 363 : case 364 : case 365 : case 366 : case 367 : case 368 : break;
                case 371 : case 372 : case 373 : break;

                case 801 : case 802 : break;
            }
        }
        return $retStrBody;
    }

    function tableIUDByQMFile($menuId, $userId, $strIUDSourceFullname, $varLoadTableSetting=null, $intModeFileCh=0, $strQMFileSendAreaFormatterId, &$aryVariant=array(), &$arySetting=array()){
        global $g, $objDBCA, $objMTS;
        // ----ローカル変数宣言
        $intControlDebugLevel01=250;
        //
        // return値
        $strRetStrBody = "";
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $aryRawResultOfEditExecute = array();
        $aryNormalResultOfEditExecute = array();

        $tmparrRetResults=array();
        //
        $root_dir_path = ROOT_DIR_PATH;
        $page_dir = $menuId;

        $intErrorPlaceMark = null;
        $strErrorPlaceFmt = "%08d";

        $varTrzStart = null;
        $varCommit   = null;
        $varRollBack = null;
        $varTrzExit  = null;

        $row_id_info = '';

        $aryVariant["TABLE_IUD_SOURCE"] = "queryMaterialFile";

        $strHtmlRowDelimiter = "\n";
        $strFileRowDelimiter = "\r\n";

        //----デフォルトのファイルアップロード先
        $editSourceDir = "{$root_dir_path}/logs/update_by_file";
        $editErrorLogDir = "{$root_dir_path}/temp/update_by_file_error";
        //デフォルトのファイルアップロード先----
        $refRetKeyExists = false;
        $uploadFiles = array();
        $arrTempFiles=array();
        // ローカル変数宣言----

        $strFxName = __FUNCTION__;
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        //----大量行のアップロードに備えて、タイムリミット「なし」を原則とする
        //set_time_limit(0);
        //大量行のアップロードに備えて、タイムリミット「なし」を原則とする----
        try{
            //----権限の取得/判定
            // そのメニューに対してどのような権限があるのか判定
            $strPrivilege = getPrivilegeAuthByUserId($menuId, $userId);
            $g["privilege"] = $strPrivilege;

            switch($strPrivilege){
                case "1":
                    break;
                case "2":
                default:
                    // ----0は権限がないので出力しない
                    $intErrorType = 1;
                    $intErrorPlaceMark = 100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
                    // 0は権限がないので出力しない----
            }
            //権限の取得/判定----

            if( is_array($aryVariant) !== true || is_array($arySetting) !== true ){
                //----引数の型が不正
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 150;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                //引数の型が不正----
            }
            if( array_key_exists("objTable",$aryVariant) === true ){
                $objTable = $aryVariant['objTable'];
            }
            else{
                $systemFile = ROOT_DIR_PATH."/webconfs/systems/{$page_dir}_loadTable.php";
                $sheetFile = ROOT_DIR_PATH."/webconfs/sheets/{$page_dir}_loadTable.php";
                $userFile = ROOT_DIR_PATH."/webconfs/users/{$page_dir}_loadTable.php";
                if(file_exists($systemFile)){
                    require_once($systemFile);
                }
                else if(file_exists($sheetFile)){
                    require_once($sheetFile);
                }
                else if(file_exists($userFile)){
                    require_once($userFile);
                }
                else{
                    $intErrorType = 901;
                    throw new Exception( 'ERROR LOADING (' . $page_dir . '}_loadTable.php)-[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $objTable = loadTable($varLoadTableSetting);
            }
            if( gettype($objTable) != "object" ){
                // ----TCAクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 200;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            if( is_a($objTable, "TableControlAgent") !== true ){
                // ----TCAクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 300;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            
            if( is_string($strQMFileSendAreaFormatterId) !== true ){
                // ----TCAクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 400;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // TCAクラスではない----
            }
            $objQMFSALFormatter = $objTable->getFormatter($strQMFileSendAreaFormatterId);
            if( $objQMFSALFormatter === null ){
                // ----存在しないフォーマッタ----
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 500;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // 存在しないフォーマッタ----
            }
            if( is_a($objQMFSALFormatter, "QMFileSendAreaFormatter") !== true ){
                // ----CurrentTableFormatterクラスではない
                // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                $intErrorType = 701;
                $intErrorPlaceMark = 600;
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                // CurrentTableFormatterクラスではない----
            }

            switch($intModeFileCh){
                case 0:
                    $strModeMark = "excel";
                    break;
                case 1:
                    $strModeMark = "csv";
                    break;
                default:
                    // 許容されない引数範囲(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 100; 
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }

            // $strOutputFileType = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("outputFileType",$refRetKeyExists);

            $unixStartTimeStamp = time();
            $strLogTimeStamp = date("YmdHis", $unixStartTimeStamp);

            if( 0 == strlen($strIUDSourceFullname) ){
                list($strUpTmpFileFullname,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('tableIUDByQMFile','vars','strUpTmpFileFullname'),"");
                if( $boolKeyExists === false || 0 == strlen($strUpTmpFileFullname) ){
                    // 許容されない引数不足(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                list($strOrgFileNameOfUpTmpFile,$boolKeyExists) = isSetInArrayNestThenAssign($aryVariant,array('tableIUDByQMFile','vars','strOrgFileNameOfUpTmpFile'),"");
                if( $boolKeyExists === false || 0 == strlen($strOrgFileNameOfUpTmpFile) ){
                    // 許容されない引数不足(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                if( file_exists($strUpTmpFileFullname) == false ){
                    //----アップロードされて作成された一時ファイルが存在しない
                    $intErrorType = 801;
                    $intErrorPlaceMark = 900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //アップロードされて作成された一時ファイルが存在しない----
                }


                // ファイル名にカッコがある場合の処理
                $bk_strUpTmpFileFullname = $strUpTmpFileFullname;

                $tmpStrUpTmpFileFullnameAry = explode("/", $strUpTmpFileFullname);
                $lastIndex = count($tmpStrUpTmpFileFullnameAry)-1;
                $tmpStrUpTmpFileFullname = $tmpStrUpTmpFileFullnameAry[$lastIndex];
                // ファイル名を配列から抜く
                unset($tmpStrUpTmpFileFullnameAry[$lastIndex]);
                // $strFileReceptUniqueNumber用
                $tmpStrUpTmpFileFullnameAry[] = "'".$tmpStrUpTmpFileFullname."'";
                // シングルクォーテーション付きのファイル名を配列を追加、結合する
                $strUpTmpFileFullname = implode("/", $tmpStrUpTmpFileFullnameAry);

                // $strMovedFileFullname用
                $tmpStrMovedFileFullnameAry[] = "'".$tmpStrUpTmpFileFullname;
                // シングルクォーテーション付きのファイル名を配列を追加、結合する
                $strUpTmpFileFullnameForMoved = implode("/", $tmpStrMovedFileFullnameAry);

                $tmpStrMovedFileFullnameAry2[] = rawurlencode($tmpStrUpTmpFileFullname);
                $strUpTmpFileFullnameForMoved2 = implode("/", $tmpStrMovedFileFullnameAry2);


                $strFileReceptUniqueNumber = $strModeMark."_".$strLogTimeStamp."_".basename($bk_strUpTmpFileFullname);
                $strMovedFileFullname = $editSourceDir."/".$tmpStrUpTmpFileFullname.".log";


                $res = rename($bk_strUpTmpFileFullname, $editSourceDir."/".$tmpStrUpTmpFileFullname.".log");
                if (!$res) {
                    //----ファイルの移動に失敗した
                    $intErrorType = 802;
                    $intErrorPlaceMark = 1000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //ファイルの移動に失敗した----
                } else {
                    $strIUDSourceFullname = $strMovedFileFullname;
                }
            }
            else{
                if( file_exists($strIUDSourceFullname) == false ){
                    //----指定されたファイルが存在しなかった
                    // 許容されない引数内容(製造元内部開発者であっても、指定禁止なので、システムエラーに位置付)
                    $intErrorType = 701;
                    $intErrorPlaceMark = 1100;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //指定されたファイルが存在しなかった----
                }
                $strOrgFileNameOfUpTmpFile = basename($strIUDSourceFullname);
                $strFileReceptUniqueNumber = $strModeMark."_".$strLogTimeStamp."_".$strOrgFileNameOfUpTmpFile;
            }

            if( $intModeFileCh == 0 ){
                //----EXCELモード
                $dlcOrderMode = 1;
                //
                $refRetKeyExists = false;
                $strLinkFormatterId = $objQMFSALFormatter->getGeneValue("linkExcelFormatterID",$refRetKeyExists);
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = $objTable->getGeneObject("linkExcelFormatterID",$refRetKeyExists);
                }
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = "excel";
                }
                //
                if( $strLinkFormatterId === null){
                    //----エクセル用のフォーマットIDがnullだった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1200;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エクセル用のフォーマットIDがnullだった----
                }
                $objListFormatter = $objTable->getFormatter($strLinkFormatterId);
                if( $objListFormatter === null ){
                    //----存在しないフォーマッタ
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1300;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //存在しないフォーマッタ----
                }
                if( is_a($objListFormatter, "ExcelFormatter") !== true ){
                    //----エクセルフォーマッタ系ではなかった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1400;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エクセルフォーマッタ系ではなかった----
                }

                $intTempFilesize = filesize($strIUDSourceFullname);
                $intMaxFileSize = $objQMFSALFormatter->getGeneValue("linkExcelMaxFileSize",$refRetKeyExists);
                if( $intMaxFileSize === null && $refRetKeyExists===false ){
                    $intMaxFileSize = $objTable->getGeneObject("linkExcelMaxFileSize",$refRetKeyExists);
                }
                if( $intMaxFileSize === null || is_int($intMaxFileSize) === false ){
                    $intMaxFileSize = 10*1024*1024;
                }else{
                    if( $intMaxFileSize < 0 ){
                        $intMaxFileSize = 10*1024*1024;
                    }
                }

                if( $intMaxFileSize < $intTempFilesize ){
                    //----許容されたサイズ以上のファイルがアップロードされた
                    $intErrorType = 351;
                    $intErrorPlaceMark = 1500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //許容されたサイズ以上のファイルがアップロードされた----
                }

                $objListFormatter->cashModeAdjust();

                $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xlsx");
                $objWorkBook = $objReader->load($strIUDSourceFullname);
                $objWorkBook->setActiveSheetIndex(0);
                //
                $expAddBody01 = $objMTS->getSomeMessage("ITAWDCH-ERR-280");

                $output_logfile_prefix = "tableIUDByExcel_exec_";
                //EXCELモード----
            }
            else if( $intModeFileCh == 1 ){
                //----CSVモード
                $dlcOrderMode = 2;
                //
                $strLinkFormatterId = $objQMFSALFormatter->getGeneValue("linkCSVFormatterID",$refRetKeyExists);
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = $objTable->getGeneObject("linkCSVFormatterID", $refRetKeyExists);
                }
                if( $strLinkFormatterId === null && $refRetKeyExists === false ){
                    $strLinkFormatterId = "csv";
                }
                //
                if( $strLinkFormatterId === null){
                    //----エクセル用のフォーマットIDがnullだった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //エクセル用のフォーマットIDがnullだった----
                }
                $objListFormatter = $objTable->getFormatter($strLinkFormatterId);
                if( $objListFormatter === null ){
                    //----存在しないフォーマッタ
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1700;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //存在しないフォーマッタ----
                }
                if( is_a($objListFormatter, "CSVFormatter") !== true ){
                    //----CSVフォーマッタ系ではなかった
                    $intErrorType = 801;
                    $intErrorPlaceMark = 1800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //CSVフォーマッタ系ではなかった----
                }
                //
                $intTempFilesize = filesize($strIUDSourceFullname);
                $intMaxFileSize = $objQMFSALFormatter->getGeneValue("linkCSVMaxFileSize",$refRetKeyExists);
                if( $intMaxFileSize === null && $refRetKeyExists === false ){
                    $intMaxFileSize = $objTable->getGeneObject("linkCSVMaxFileSize",$refRetKeyExists);
                }
                if( $intMaxFileSize === null || is_int($intMaxFileSize) === false ){
                    $intMaxFileSize = 20*1024*1024;
                }else{
                    if( $intMaxFileSize < 0 ){
                        $intMaxFileSize = 20*1024*1024;
                    }
                }
                //
                if( $intMaxFileSize  < $intTempFilesize ){
                    //----許容されたサイズ以上のファイルがアップロードされた
                    $intErrorType = 361;
                    $intErrorPlaceMark = 1900;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    //許容されたサイズ以上のファイルがアップロードされた----
                }
                //
                $strOutputFileType = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("outputFileType",$refRetKeyExists);
                //
                $boolCheckScsvType = false;
                if( $strOutputFileType == "SafeCSV" ){
                    //----ダウンロードタイプがSafeCSV形式の場合
                    if( preg_match('/\.scsv$/',$strOrgFileNameOfUpTmpFile) === 1 ){
                        $boolSafeSCSV2 = false;
                        //
                        $objSCSV = new SafeCSVAdminForPHP();
                        $boolSafeSCSV2 = $objSCSV->checkSafeCSV2($strIUDSourceFullname);
                        //
                        if( $boolSafeSCSV2 === false ){
                            //----この時点でエラー終了
                            $intErrorType = 362;
                            $intErrorPlaceMark = 2000;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //この時点でエラー終了----
                        }
                        else{
                            $intRecordRowStartIndex = $objSCSV->getRecordRowStart();
                            //
                            $miFileHandle=fopen($strIUDSourceFullname,"r");
                            $miLineIndex=0;
                            $aryRowFromCsv = array();
                            while(! feof($miFileHandle)){
                                $miLineIndex+=1;
                                $miReadBody=fgets($miFileHandle);
                                if( $intRecordRowStartIndex <= $miLineIndex ){
                                    if( $miReadBody != "" ){
                                        //----CSVの行を、$aryRowFromCsv[]へ格納「フィールド名の行およびデータ本体行」
                                        $arraySingle = $objSCSV->makeRowArrayFromSafeCSVRecordRow($miReadBody);
                                        $aryRowFromCsv[]=$arraySingle;
                                        //CSVの行を、$aryRowFromCsv[]へ格納「フィールド名の行およびデータ本体行」----
                                    }
                                }
                            }
                            fclose($miFileHandle);
                        }
                        unset($objSCSV);
                    }
                    else{
                        //----この時点でエラー終了
                        $intErrorType = 363;
                        $intErrorPlaceMark = 2100;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //この時点でエラー終了----
                    }
                    $output_logfile_prefix = "tableIUDBySafeCSV_exec_";
                    //ダウンロードタイプがSafeCSV形式の場合----
                }
                else{
                    //----ダウンロードタイプが通常CSV形式の場合
                    if( preg_match('/\.scsv$/',$strOrgFileNameOfUpTmpFile) === 1 ){
                        //----この時点でエラー終了
                        $intErrorType = 364;
                        $intErrorPlaceMark = 2200;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //この時点でエラー終了----
                    }
                    else{
                        $boolTestModeForCSVUpload = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("testModeForCSVUpload",$refRetKeyExists);

                        if( $boolTestModeForCSVUpload === true ){
                            //----ここから動作保証範囲外
                            //
                            $tmpFileFp =  fopen($strIUDSourceFullname,"r");
                            //
                            $csv_row_counter = 0;
                            //
                            //----CSVの行を、$aryRowFromCsv[]へ格納
                            $aryRowFromCsv = array();
                            while( $aryRowFromCsv[$csv_row_counter] = fgetcsv($tmpFileFp,0,',','"') ){
                                //----行番号を作成
                                $csv_row_counter = $csv_row_counter + 1;
                                //行番号を作成----
                            }
                            //CSVの行を、$aryRowFromCsv[]へ格納----
                            
                            fclose($tmpFileFp);
                            
                            //ここまで動作保証範囲外----
                        }
                        else{
                            //----この時点でエラー終了
                            $intErrorType = 365;
                            $intErrorPlaceMark = 2300;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            //この時点でエラー終了----
                        }
                    }
                    $output_logfile_prefix = "tableIUDByCSV_exec_";
                    //ダウンロードタイプが通常CSV形式の場合----
                }
                
                //----CSVの行を、$aryRowFromCsv[]へ格納
                $csv_row_counter = count($aryRowFromCsv);
                //CSVの行を、$aryRowFromCsv[]へ格納----

                //ファイルを開いて配列へ格納----
                
                //"※上記の行数はExcel上の行番号です。\n";
                $expAddBody01 = $objMTS->getSomeMessage("ITAWDCH-ERR-281");
                //CSVモード----
            }

            $arrayObjColumn = $objTable->getColumns();

            if( $intModeFileCh == 0 ){
                //----EXCELモード
                $objWorkSheet = $objWorkBook->getActiveSheet();

                //----Excelの書式のチェック
                //----シート名
                $strSheetName = $objTable->getFormatter($strLinkFormatterId)->getGeneValue("sheetNameForEditByFile",$refRetKeyExists);

                if( $strSheetName == "" ){
                    $strSheetName = $objTable->getDBMainTableLabel();
                }

                // 31文字に短縮する
                $strSheetName = mb_substr($strSheetName, 0, 31, "UTF-8");

                // Excelシートの予約語「履歴」の場合はそのまま使用できないため後ろに_を付与する
                if($strSheetName == $objMTS->getSomeMessage("ITAWDCH-STD-16211")){
                    $strSheetName .= "_";
                }

                if( $strSheetName != $objWorkSheet->getTitle() ){
                    //dev_log("エクセルのシート名が不正です。\n",$intControlDebugLevel01);
                    $intErrorType = 352;
                    $intErrorPlaceMark = 2800;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                //シート名----
                //Excelの書式のチェック----
            }

            if( $intModeFileCh == 0 ){
                //----EXCELモード
                $intLengthOfHeaderRows = $objTable->getColGroup()->getHRowCount($strLinkFormatterId) - 1;
                $intRowNoOfFirstBodyRow = $intLengthOfHeaderRows + 8;
                $intColNoOfLastColumn = $objWorkSheet->getHighestDataColumn();
                $intRowNoOfLastBodyRow = $objWorkSheet->getHighestDataRow();
                $excelHeaderData = $objWorkSheet->rangeToArray(ExcelFormatter::cr2s(3, $intRowNoOfFirstBodyRow - 1).":".$intColNoOfLastColumn.($intRowNoOfFirstBodyRow - 1));
                //
                $boolLabelFull = false;
                //EXCELモード----
            }
            else if( $intModeFileCh == 1 ){
                //----CSVモード
                $intRowNoOfFirstBodyRow = 1;
                if( array_key_exists(0,$aryRowFromCsv) === false ){
                    $aryRowFromCsv = array(array());
                }
                $intColNoOfLastColumn = count($aryRowFromCsv[0]);
                $intRowNoOfLastBodyRow = $csv_row_counter - 1;
                $csvHeaderData = $aryRowFromCsv[0];
                //
                $boolLabelFull = false;
                //CSVモード----
            }

            $lcRequiredDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID();
            $lcRequiredRowEditByFileColumnId = $objTable->getRequiredRowEditByFileColumnID();
            $lcRowIdentifyColumnId = $objTable->getRowIdentifyColumnID();

            $objREBFColumn = $arrayObjColumn[$lcRequiredRowEditByFileColumnId];
            $objRIColumn = $arrayObjColumn[$lcRowIdentifyColumnId];

            $lcNDBExecuteColumnID = $objREBFColumn->getID();    //"ROW_EDIT_BY_FILE" "EXEC_TYPE"
            $lcNDBExecuteColumnName = $objREBFColumn->getColLabel();    //"実行処理種別"
            //$lcNDBExecuteColumnSynonym = $objREBFColumn->getIDSOP();

            //----配列初期化
            $tableHeaderId = array($lcNDBExecuteColumnID);
            $tableHeaderNm = array($lcNDBExecuteColumnName);
            //$tableHeaderSy = array($lcNDBExecuteColumnSynonym);
            //配列初期化----

            foreach($arrayObjColumn as $objColumn){
                if( $objColumn->getID() == $lcRequiredRowEditByFileColumnId ){
                    continue;
                }
                else if( $objColumn->getOutputType($strLinkFormatterId)->isVisible() === false ){
                    continue;
                }
                else{
                    $tableHeaderId[] = $objColumn->getID();
                    $tableHeaderNm[] = $objColumn->getColLabel($boolLabelFull);
                }
            }

            //----列の一致チェック
            if( $intModeFileCh == 0 ){
                //----EXCELモード
                for( $fnv1 = 1; $fnv1 < count($tableHeaderId); ++$fnv1 ){
                    if($arrayObjColumn[$tableHeaderId[$fnv1]]->getColLabel() != $excelHeaderData[0][$fnv1]){
                        //dev_log("列が一致しません。最新のフォーマットを使用してください。(".$arrayObjColumn[$tableHeaderId[$fnv1]]->getColLabel(true).",".$excelHeaderData[0][$fnv1].")\n", $intControlDebugLevel01);
                        $intErrorType = 353;
                        $intErrorPlaceMark = 2900;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                $upload_log_print = $objQMFSALFormatter->getGeneValue("uploadLogPrint",$refRetKeyExists);
                //EXCELモード----
            }
            else if( $intModeFileCh == 1 ){
                //----CSVモード

                if( $csvHeaderData[0] == $lcNDBExecuteColumnID ){
                    $arrayCheckHeader =& $tableHeaderId;
                }
                else if( $csvHeaderData[0] == $lcNDBExecuteColumnName ){
                    $arrayCheckHeader =& $tableHeaderNm;
                }
                else{
                    $intErrorType = 366;
                    $intErrorPlaceMark = 3000;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                $dlcLTColumnCount = count($arrayCheckHeader);
                $dlcCSVHeaderDataColumnCount = count($csvHeaderData);

                if( $dlcLTColumnCount == $dlcCSVHeaderDataColumnCount ){
                    for( $fnv1 = 0; $fnv1 < $dlcLTColumnCount ; $fnv1++ ){
                        if( $arrayCheckHeader[$fnv1] != $csvHeaderData[$fnv1] ){
                            $intErrorType = 367;
                            $intErrorPlaceMark = 3100;
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                    }
                    $upload_log_print = $objQMFSALFormatter->getGeneValue("uploadLogPrint",$refRetKeyExists);
                }
                else{
                    $intErrorType = 368;
                    $intErrorPlaceMark = 3200;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                
                //CSVモード----
            }

            //列の一致チェック----
            
            //----エラー出力形式が個別には設定されていなかった
            if( $upload_log_print === null ){
                $upload_log_print = $objTable->getGeneObject("uploadLogPrint",$refRetKeyExists);
            }
            //エラー出力形式が個別には設定されていなかった----

            if( $upload_log_print !== "toHtml" ){
                $upload_log_print = "toFile";
            }
            else if( $upload_log_print == "toFile" ){
                $strLogRowHead="";
                $strLogRowColSepa="\t";
                $strLogRowTail=$strFileRowDelimiter;
                $strSepaIdInfoAndError="\n";
            }
            $strLineExplainHead = $objMTS->getSomeMessage("ITAWDCH-ERR-282");
            $strLineExplainTail = $objMTS->getSomeMessage("ITAWDCH-ERR-283");
            
            if( $objTable->getCommitSpanOnTableIUDByFile() === 0 ){
                //----トランザクション開始
                $varTrzStart = $objDBCA->transactionStart();
                if( $varTrzStart !== true ){
                    $intErrorType = 901;
                    $intErrorPlaceMark = 3500;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                if( $objTable->inTrzLockSequences($arrayObjColumn)===false ){
                    $intErrorType = 902;
                    $intErrorPlaceMark = 3600;
                    throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                foreach($arrayObjColumn as $objColumn){
                    $arrayTmp = $objColumn->afterTrzStartAction($aryVariant);
                    if( $arrayTmp[0] === false ){
                        $intErrorType = 903;
                        $intErrorPlaceMark = 3700;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                }
                //トランザクション開始----
            }

            //----bodyTop行目から最後までループ
            for($row_i = $intRowNoOfFirstBodyRow; $row_i <= $intRowNoOfLastBodyRow; ++$row_i){
                $inputArray = array();
                if( $intModeFileCh == 0 ){
                    //----EXCELモード

                    $excelBodyData = array();
                    for($focusCol = 0; $focusCol < count($tableHeaderId) ; $focusCol++ ){
                        $excelBodyData[] = $objWorkSheet->getCellByColumnAndRow(3+$focusCol,$row_i)->getValue();
                    }

                    //----第1引数の配列値をキーに、第2引数の配列値を値とする連想配列を形成
                    $inputArray = array_combine($tableHeaderId, $excelBodyData);
                    //第1引数の配列値をキーに、第2引数の配列値を値とする連想配列を形成----

                    //EXCELモード----
                }
                else if( $intModeFileCh == 1 ){
                    //----CSVモード
                    for($dlcFnv2 = 0; $dlcFnv2 < $intColNoOfLastColumn; $dlcFnv2++ ){
                        $colKey = $tableHeaderId[$dlcFnv2];
                        $inputArray[$colKey] = $aryRowFromCsv[$row_i][$dlcFnv2];
                    }
                    //CSVモード----
                }
                //----テーブルへのアクセスを実行

                $arrayRetResult = $objREBFColumn->editExecute($inputArray, $dlcOrderMode, $aryVariant);             
                //DB更新後処理用の情報取得（全行Commit時用）
                $tmparrRetResults[]=$arrayRetResult;
                unset($arrayRetResult[99]);
                //テーブルへのアクセスを実行----
                //
                $aryRawResultOfEditExecute[$row_i] = $arrayRetResult[4];
                //
                if( $arrayRetResult[0] === false ){
                    //----エラーあり
                    if( $arrayRetResult[2] != "" ){
                        //----CSV系の場合
                        $row_id_info = "";
                        $rowIdValue = $arrayRetResult[3];
                        $objIntNumVali = new IntNumValidator(null,null);
                        if( $objIntNumVali->isValid($rowIdValue)===false ){
                            $rowIdValue = "";
                        }
                        if( 0 < strlen($rowIdValue ) ){
                            $row_id_info = $objMTS->getSomeMessage("ITAWDCH-ERR-291",array($objRIColumn->getColLabel(true),$rowIdValue));
                        }
                    }
                    //エラーあり----
                }
                else{
                    //----エラーなし
                    //エラーなし----
                }
            }
            //bodyTop行目から最後までループ----

            $aryNormalResultOfEditExecute = $objREBFColumn->getResultCount();

            //----結果出力
            $strRetStrBody = $objMTS->getSomeMessage("ITAWDCH-STD-451",$strOrgFileNameOfUpTmpFile);
            $strResultList = "";
            $aryResultCountList = array();
            
            $intSuccess =0;
            $intError =0;
            
            $strErrCountExplainHead = $objMTS->getSomeMessage("ITAWDCH-ERR-284");
            $strErrCountExplainTail = $objMTS->getSomeMessage("ITAWDCH-ERR-285");
            
            foreach($aryNormalResultOfEditExecute as $strKey=>$aryData){
                $strResultList .= $strErrCountExplainHead.sprintf("%s:%10d",$aryData['name'],$aryData['ct']).$strErrCountExplainTail."\n";
                $aryResultCountList[] = array($aryData['name'],$aryData['ct'],$strErrCountExplainHead.sprintf("%s:%10d",$aryData['name'],$aryData['ct']).$strErrCountExplainTail."\n");
                if( $strKey == "error" ){
                    $intError += $aryData['ct'];
                }
                else{
                    $intSuccess += $aryData['ct'];
                }
            }
            //commispant（全行:0）時
            if( $objTable->getCommitSpanOnTableIUDByFile() === 0 ){
                //エラー１件以上
                if( $aryNormalResultOfEditExecute['error']['ct'] != 0){
                    //集計結果変換
                    $typect = 0;
                    foreach($aryNormalResultOfEditExecute as $strKey=>$aryData){
                        if($strKey != 'error' ){
                            $typect = $typect + $aryNormalResultOfEditExecute[$strKey]['ct'];
                            $aryNormalResultOfEditExecute[$strKey]['ct']=0;
                        }
                    }
                    $aryNormalResultOfEditExecute['error']['ct'] = $aryNormalResultOfEditExecute['error']['ct'] + $typect ;
                }
            } 

            if( $objTable->getCommitSpanOnTableIUDByFile() === 0 ){
                if( 0 === $intError ){
                    //----トランザクション終了
                    $varCommit = $objDBCA->transactionCommit();
                    if( $varCommit !== true ){
                        $intErrorType = 904;
                        $intErrorPlaceMark = 3800;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $varTrzExit = $objDBCA->transactionExit();
                    if( $varTrzExit === false ){
                        $intErrorType = 905;
                        $intErrorPlaceMark = 3900;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $strRetStrBody .= $strResultList;
                    //----トランザクション終了
                }
                else{
                    //----1件でもエラーがあったらロールバック
                    //----ロールバックする
                    $varRollBack = $objDBCA->transactionRollBack();
                    if( $varRollBack === false ){
                        //----1回目のロールバックが失敗してしまった場合
                        $intErrorType = 906;
                        $intErrorPlaceMark = 4000;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        //1回目のロールバックが失敗してしまった場合----
                    }
                    $varTrzExit = $objDBCA->transactionExit();
                    if( $varTrzExit === false ){
                        $intErrorType = 907;
                        $intErrorPlaceMark = 4100;
                        throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    //ロールバックする----
                    $strRetStrBody .= $objMTS->getSomeMessage("ITAWDCH-ERR-304");
                    //1件でもエラーがあったらロールバック----
                }
            }
            else{
                $strRetStrBody .= $strResultList;
            }
            
            if( $varRollBack !== true ){
                $refValue = array(
                               "caller"=>"tableIUDByQMFile",
                               "ordMode"=>$dlcOrderMode,
                               "updateResource"=>$strFileReceptUniqueNumber,
                               "request_time"=>$unixStartTimeStamp,
                               "resultList"=>$aryResultCountList,
                               "intSuccess"=>$intSuccess,
                               "intError"=>$intError
                            );
                
                $objTable->commonEventHandlerExecute($refValue);
            }
            
        }
        catch (Exception $e){
            $tmpErrMsgBody = $e->getMessage();
            dev_log($tmpErrMsgBody, $intControlDebugLevel01);
            outputLog(LOG_PREFIX, $tmpErrMsgBody);
            outputLog(LOG_PREFIX, $intControlDebugLevel01);
            if( 901 <= $intErrorType ) outputLog(LOG_PREFIX, $tmpErrMsgBody);
        }
        //
        //----大量行のアップロードに備えて、タイムリミットを「30」に戻す
        //大量行のアップロードに備えて、タイムリミットを「30」に戻す----
        // $strErrorStreamFromEditExecute = "";仮
        
        //結果出力----
        dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

        return array($strRetStrBody,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryNormalResultOfEditExecute,$aryRawResultOfEditExecute);
    }

    /**
    * dump結果のファイル作成
    * 
    * @param  string  $msg       エラーメッセージ
    * @param  int     $taskId    タスクID
    * @return boolean            true/false
    */
    function dumpResultMsg($msg, $taskId) {
        global $g, $objMTS, $objDBCA;

        $uploadDir = ROOT_DIR_PATH."/uploadfiles/2100000331/FILE_RESULT";
        $resultFileName = "ResultData_$taskId.log";
        $uploadFilePath = "$uploadDir/$resultFileName";

        // ファイルの作成
        if (!file_exists($uploadFilePath)) {
            $res = file_put_contents("$uploadFilePath", "$msg\n");
            if ($res == false) {
                return false;
            }
        } else {
            $res = file_put_contents("$uploadFilePath", "$msg\n", FILE_APPEND);
            if ($res == false) {
                return false;
            }
        }
        return true;
    }

    /**
    * dump結果ファイルの登録
    * 
    * @param  int    $taskId    タスクID
    * @return boolean
    */
    function registerResultFile($taskId) {
        global $g, $objMTS, $objDBCA;

        $uploadDir = ROOT_DIR_PATH."/uploadfiles/2100000331/FILE_RESULT";
        $resultFileName = "ResultData_$taskId.log";
        $uploadFilePath = "$uploadDir/$resultFileName";

        // ファイル名をレコードに登録
        $sql = "
            UPDATE
                B_BULK_EXCEL_TASK
            SET
                RESULT_FILE_NAME = :RESULT_FILE_NAME
            WHERE
                TASK_ID = :TASK_ID
            AND
                DISUSE_FLAG = 0";

        $objQuery = $objDBCA->sqlPrepare($sql);
        if ($objQuery->getStatus() === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                              array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
                        outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }
        $res = $objQuery->sqlBind(
            array(
                "TASK_ID"          => $taskId,
                "RESULT_FILE_NAME" => $resultFileName
            )
        );
        $res = $objQuery->sqlExecute();
        if ($res === false) {
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                                          array(basename(__FILE__), __LINE__)));
            outputLog(LOG_PREFIX, $sql);
            outputLog(LOG_PREFIX, $objQuery->getLastError());
            throw new Exception( $ErrorMsg );
        }

        return true;
    }

    /**
    * scsv判定
    * 
    * @param  int    $menuRows       取得件数
    * @param  int    $menuId         メニューID
    * @return boolean  $result       SCSVかどうか
    */
    function isSCSV($menuRows, $menuId) {
        global $g, $objMTS, $objDBCA;

        $result = false;
        $xls_print_limit = NULL;

        // Excel表示件数の取得
        $sql = "
            SELECT
                XLS_PRINT_LIMIT
            FROM
                A_MENU_LIST
            WHERE
                MENU_ID = :MENU_ID
            AND
                DISUSE_FLAG = 0
        ";

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

        while ($row = $objQuery->resultFetch()) {
            if ($row["XLS_PRINT_LIMIT"] != NULL) {
                $xls_print_limit = $row["XLS_PRINT_LIMIT"];
            }
        }
        if ($xls_print_limit != NULL) {
            if ($menuRows > $xls_print_limit) {
                $result = true;
            }
        }

        return $result;
    }

    /**
    * メニューで表示されるレコードの件数取得
    * 
    * @param  array    $objTable       オブジェクトテーブル
    * @param  array    $aryVariant     検索情報
    * @return boolean  $result         件数
    */
    function getMenuRows($objTable, $tmpAryVariant=array()) {
        global $g, $objMTS, $objDBCA;

        $result = array();
        $aryVariant = array();
        $arySetting = array();

        if (!empty($tmpAryVariant)) {
            foreach ($tmpAryVariant["search_filter_data"] as $colName => $colValue) {
                foreach ($colValue as $value) {
                    $aryVariant[] = array(
                        "name"  => $colName,
                        "value" => $value
                    );
                }
            }
        }
        $aryVariant[] = array(
            "name" => "filter_ctl_start_limit",
            "value" => "on"
        );

        $arrayRecCountData = array();
        $arrayRecCountData = convertReceptDataToDataForFilter($aryVariant);

        require_once ( ROOT_DIR_PATH . "/libs/webcommonlibs/table_control_agent/01_recCount.php");
        $aryOverride = array("Mix1_1","fakeContainer_Filter1Print","Mix1_2","fakeContainer_ND_Filter1Sub");
        $arrayResult = recCountMain($objTable, "print_table", $arrayRecCountData, $aryVariant, $arySetting, $aryOverride);
        $objTable->setData(array());

        if ($arrayResult[0] == "002") {
            $result = false;
        } else {
            $result = $arrayResult[2];
        }

        return $result;
    }

    /**
    * 拡張子の取得
    * 
    * @param  int      $fileName       ファイル名
    * @return boolean  $extension      拡張子
    */function getExtension($fileName) {
        global $g, $objDBCA, $objMTS;
        $tmpFileNameAry = explode(".", $fileName);
        $extension = $tmpFileNameAry[count($tmpFileNameAry)-1];

        return $extension;
    }
