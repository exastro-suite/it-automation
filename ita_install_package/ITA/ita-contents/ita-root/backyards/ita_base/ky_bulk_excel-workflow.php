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
 *  テーブルに登録されたExcel一括エクスポート/インポートのタスクを実行する
 *
 */

if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode('ita-root', dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . 'ita-root';
}


define('ROOT_DIR_PATH',        $root_dir_path);
define('EXPORT_PATH',          ROOT_DIR_PATH . '/temp/bulk_excel/export');
define('IMPORT_PATH',          ROOT_DIR_PATH . '/temp/bulk_excel/import/');
define('BACKUP_PATH',          ROOT_DIR_PATH . '/temp/data_import/backup/');
define('UPLOADFILES_PATH',     ROOT_DIR_PATH . '/temp/data_import/uploadfiles/');
define('DST_PATH',             ROOT_DIR_PATH . '/uploadfiles/2100000331');
define('RESULT_PATH',          ROOT_DIR_PATH . '/uploadfiles/2100000331/FILE_RESULT');
define('LOG_DIR',              '/logs/backyardlogs/');
define('LOG_LEVEL',            getenv('LOG_LEVEL'));
define('LAST_UPDATE_USER',     -100331); // Excel一括実行プロシージャ
define('STATUS_RUNNING',       2); // 実行中
define('STATUS_PROCESSED',     3); // 完了
define('STATUS_FAILURE',       4); // 完了(異常) 
define('LOG_PREFIX',           basename( __FILE__, '.php' ) . '_');

define('SKIP_SERVICE_FILE',         ROOT_DIR_PATH . '/temp/data_import/skip_all_service');
define('SKIP_SERVICE_INTERVAL',     10);

try {
    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_php_req_gate.php';
    require_once ROOT_DIR_PATH . '/libs/commonlibs/common_db_connect.php';
    require_once ROOT_DIR_PATH . '/libs/backyardlibs/common/common_functions.php';
    require_once ROOT_DIR_PATH . '/libs/backyardlibs/ita_base/common_data_portability.php';
    require_once ROOT_DIR_PATH . '/libs/backyardlibs/ita_base/ky_bulk_excel-workflow_functions.php';
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

   
    // 未実行のレコードを取得する
    $unExecutedTaskAry = getUnexecutedBulkExcelTask();
    if (is_array($unExecutedTaskAry) === true) {
    } else {
        throw new Exception($objMTS->getSomeMessage('ITABASEH-STD-900005'));
    }

    foreach ($unExecutedTaskAry as $task) {
        // 実行フラグ
        $execFlg = true;

        $res = setStatus($task['TASK_ID'], STATUS_RUNNING);
        if ($res === false) {
            $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                              array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
            outputLog(LOG_PREFIX, $logMsg);
            setStatus($task['TASK_ID'], STATUS_FAILURE);
            continue;
        }

        // エクスポート
        if ($task["TASK_TYPE"] == 1) {
            $taskId = $task["TASK_ID"];
            $userId = $task["EXECUTE_USER"];
            $privilegeErr = false;

            $includingScsvFlg = false; // エクスポートするファイル内にSCSVファイルがあるか
            // タスクIDでディレクトリづくり
            if (!is_dir(EXPORT_PATH."/$taskId")) {
                mkdir(EXPORT_PATH."/$taskId");
            }
            if (!is_dir(EXPORT_PATH."/$taskId/tmp_zip")) {
                mkdir(EXPORT_PATH."/$taskId/tmp_zip");
            }
            if (!is_dir(EXPORT_PATH."/$taskId")) {
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                              array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                outputLog(LOG_PREFIX, $logMsg);
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }
            // オーナー変更
            $output = NULL;
            $cmd = "chown -R  apache:apache ".EXPORT_PATH."/$taskId";
            exec($cmd, $output, $return_var);
            if(0 != $return_var){
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                              array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                outputLog(LOG_PREFIX, $logMsg);
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }

            // メニューID取得
            $menuIdArray = getExportedMenuIDList($taskId);
            if ($menuIdArray == false) {
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }

            $fileNameList = "";

            $request = array();
            foreach ($menuIdArray as $menuId) {
                // ファイル名が重複しないためにsleep
                sleep(1);
                $privilege = getPrivilegeAuthByUserId($menuId, $userId);
                if ($privilege == 1 || $privilege == 2) {
                    $objTable = getInfoOfLoadTable($menuId);
                    $menuInfo = getMenuInfoByMenuId($menuId);

                    // メニュー周りの情報
                    $menuGroupId   = $menuInfo["MENU_GROUP_ID"];
                    $menuGroupName = $menuInfo["MENU_GROUP_NAME"];
                    $menuName      = $menuInfo["MENU_NAME"];

                    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

                    $filePath = exportBulkExcelData($task, $menuGroupId, $menuId, array(), $objTable, $taskId);

                    // ファイルリスト
                    $fileName = explode("/", $filePath)[count(explode("/", $filePath)) - 1];

                    if (!array_key_exists($menuGroupId, $request)) {
                        $request[$menuGroupId] = array(
                            "menu_group_name" => $menuGroupName,
                            "menu" => array(
                                array(
                                    "menu_id"   => $menuId,
                                    "menu_name" => $menuName
                                )
                            )
                        );
                    } else {
                        $request[$menuGroupId]["menu"][] = array(
                            "menu_id"   => $menuId,
                            "menu_name" => $menuName
                        );
                    }

                    if (getExtension($fileName) == "scsv") {
                        $dumpInfo = array(
                            "filter_data" => array(
                            ),
                            "filteroutputfiletype" => "excel",
                            "FORMATTER_ID"         => "csv",
                            "requestuserclass"     => "visitor"
                        );
                        $filePath = exportBulkExcelData($task, $menuGroupId, $menuId, array(), $objTable, $taskId, $dumpInfo);
                    }

                    if (!$includingScsvFlg && getExtension($fileName) == "scsv") {
                        $includingScsvFlg = true;
                    }
                    $fileNameList .= "#$menuGroupId($menuGroupName)\n$menuId:$fileName\n";
                } else {
                    // 権限エラー
                    if (!$privilegeErr) {
                        $privilegeErr = true;
                    }
                    // ResultDataの出力
                    $resFilePath = RESULT_PATH."/ResultData_$taskId.log";
                    $menuInfo    = getMenuInfoByMenuId($menuId);
                    $title       = $menuInfo["MENU_GROUP_ID"]."_".$menuInfo["MENU_GROUP_NAME"].":".$menuId."_".$menuInfo["MENU_NAME"];
                    $msg         = $title."\n".$objMTS->getSomeMessage('ITABASEH-ERR-2100000329_4', array($menuId));

                    dumpResultMsg($msg, $taskId);
                }
            }

            if ($privilegeErr) {
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-2100000329_4', array($menuId));
                outputLog(LOG_PREFIX, $logMsg);
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }

            // ファイル一覧をJSONに変換
            $tmpExportPath = EXPORT_PATH."/$taskId/tmp_zip";
            $fileputflg = file_put_contents($tmpExportPath . '/MENU_LIST.txt', $fileNameList);

            // パスの有無を確認
            if (!is_dir(DST_PATH)) {
                $res = mkdir(DST_PATH, 0777);
                if ($res == false) {
                    // ステータスを完了(異常)にする
                    $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                                      array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                    outputLog(LOG_PREFIX, $logMsg);
                    setStatus($task['TASK_ID'], STATUS_FAILURE);
                    continue;
                }
            } elseif (substr(sprintf('%o', fileperms(DST_PATH)), -4) != 0777) {
                chmod(DST_PATH, 0777);
            }

            // scsvファイル編集キットを同梱する
            if ($includingScsvFlg) {
                $editBakerPath = ROOT_DIR_PATH."/webroot/webdbcore/editorBaker.zip";
                $res = exec("unzip $editBakerPath -d ".EXPORT_PATH."/$taskId/tmp_zip");
                if ($res != 0) {
                    // ステータスを完了(異常)にする
                    $logMsg = $objMTS->getSomeMessage('ITACREPAR-ERR-5010', array($editBakerPath, EXPORT_PATH."/$taskId/tmp_zip"));
                    outputLog(LOG_PREFIX, $logMsg);
                    outputLog(LOG_PREFIX, EXPORT_PATH."/$taskId/editorBaker.zip");
                    setStatus($task['TASK_ID'], STATUS_FAILURE);
                    continue;
                }
            }

            // ZIPを固める
            $dstFileName = "ITA_FILES_".date("YmdHis").".zip"; // zipのファイル名
            $dstFilePath = DST_PATH."/$dstFileName";
            $res = zip(EXPORT_PATH."/$taskId/tmp_zip", $dstFilePath, ".");
            if ($res == false) {
                // ステータスを完了(異常)にする
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                                  array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                outputLog(LOG_PREFIX, $logMsg);
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }

            // ZIP名をタスクのレコードに登録
            $res = updateExcelZipFileName($taskId, $dstFileName);
            if ($res == false) {
                // ステータスを完了(異常)にする
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                                  array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                outputLog(LOG_PREFIX, $logMsg);
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }

            // ステータスを完了にする
            $res = setStatus($task['TASK_ID'], STATUS_PROCESSED);
            if ($res === false) {
                // ステータスを完了(異常)にする
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                                  array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                outputLog(LOG_PREFIX, $logMsg);
                setStatus($task['TASK_ID'], STATUS_FAILURE);
                continue;
            }
        }
        // インポート
        elseif ($task["TASK_TYPE"] == 2) {
            $taskId = $task["TASK_ID"];
            $userId = $task["EXECUTE_USER"];
            
            if (substr(sprintf('%o', fileperms(DST_PATH)), -4) != 0777) {
                chmod(DST_PATH, 0777);
            }

            if (!is_dir(RESULT_PATH)) {
                $res = mkdir(RESULT_PATH, 0777);
            }

            $targetImportPath = IMPORT_PATH."import/".$taskId;

            $menuIdFileList = json_decode(file_get_contents($targetImportPath . '/IMPORT_MENU_ID_LIST'));
            foreach ($menuIdFileList as $menuId => $fileName) {
                $privilege = getPrivilegeAuthByUserId($menuId, $userId);
                if ($privilege == 1) {
                    $objTable = getInfoOfLoadTable(strval($menuId));
                    if (!file_exists(ROOT_DIR_PATH."/temp/bulk_excel/import/import/$taskId/$fileName") || $fileName == "") {
                        // ファイルがないエラー
                        $resFilePath = RESULT_PATH."/ResultData_$taskId.log";
                        $menuInfo    = getMenuInfoByMenuId($menuId);
                        $title       = $menuInfo["MENU_GROUP_ID"]."_".$menuInfo["MENU_GROUP_NAME"].":".$menuId."_".$menuInfo["MENU_NAME"];
                        $msg         = $title."\n".$objMTS->getSomeMessage("ITAWDCH-ERR-1101")."\n";

                        dumpResultMsg($msg, $taskId);
                        continue;
                    }

                    $files = array(
                        "file" => array(
                            "name"     => "$fileName",
                            "type"     => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                            "tmp_name" => ROOT_DIR_PATH."/temp/bulk_excel/import/import/$taskId/$fileName",
                            "error"    => "",
                            "size"     => filesize(ROOT_DIR_PATH."/temp/bulk_excel/import/import/$taskId/$fileName")
                        )
                    );

                    // アップロード
                    $resArray = importBulkExcel($taskId, $objTable, $files, $menuId, $task["EXECUTE_USER"]);

                    if ($resArray["result"] == false) {
                        // ステータスを完了(異常)にする
                        $msg         = $resArray["response"]["text"];
                        if ($msg == "") {
                            $msg = $objMTS->getSomeMessage("ITAWDCH-ERR-3001");
                        }
                        dumpResultMsg($msg."\n", $taskId);
                        continue;
                    }
                } else {
                    // 権限エラー
                    $resFilePath = RESULT_PATH."/ResultData_$taskId.log";
                    $menuInfo    = getMenuInfoByMenuId($menuId);
                    $title       = $menuInfo["MENU_GROUP_ID"]."_".$menuInfo["MENU_GROUP_NAME"].":".$menuId."_".$menuInfo["MENU_NAME"];
                    $msg         = $title."\n".$objMTS->getSomeMessage("ITABASEH-ERR-2100000330_1")."\n";

                    dumpResultMsg($msg, $taskId);
                }
            }

            // ファイル名の登録
            $res = registerResultFile($taskId);
            if (!$res) {
                // ステータスを完了(異常)にする
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                                  array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                $res = setStatus($task['TASK_ID'], STATUS_FAILURE);
                outputLog(LOG_PREFIX, $logMsg);
                continue;
            }

            // ステータスを完了にする
            $res = setStatus($task['TASK_ID'], STATUS_PROCESSED);
            if ($res === false) {
                // ステータスを完了(異常)にする
                $logMsg = $objMTS->getSomeMessage('ITABASEH-ERR-900046',
                                                  array('B_BULK_EXCEL_TASK',basename(__FILE__), __LINE__));
                $res = setStatus($task['TASK_ID'], STATUS_FAILURE);
                outputLog(LOG_PREFIX, $logMsg);
                continue;
            }
        }
    }
} catch (Exception $e) {
    outputLog(LOG_PREFIX, $e->getMessage());
}
