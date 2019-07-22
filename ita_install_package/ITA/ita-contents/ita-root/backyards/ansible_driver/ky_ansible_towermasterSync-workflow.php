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
//      作業状態確認ファイル
//      AnsibleTower サーバデータ同期君
//
//  【特記事項】
//      <<引数>>
//       (なし)
//      <<返却値>>
//       (なし)
//
//////////////////////////////////////////////////////////////////////

// 起動しているshellの起動判定を正常にするための待ち時間
sleep(1);

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/DBAccesser.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/LogWriter.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/MessageTemplateStorageHolder.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/RestApiCaller.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/ExecuteDirector.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/AnsibleTowerCommonLib.php");
require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/restapi_command/AnsibleTowerRestApiInstanceGroups.php");

////////////////////////////////
// ログ出力設定
////////////////////////////////
$log_output_dir     = getenv("LOG_DIR");
if(empty($log_output_dir)) {
    $log_output_dir = $root_dir_path . "/logs/backyardlogs";
}
$log_file_prefix    = basename(__FILE__, ".php") . "_";
$tmpVarTimeStamp    = time();
$logfile            = $log_output_dir . "/" . $log_file_prefix . date("Ymd", $tmpVarTimeStamp) . ".log";
ini_set("display_errors", "stderr");
ini_set("log_errors",     1);
ini_set("error_log",      $logfile);

$log_output_php     = $root_dir_path . "/libs/backyardlibs/backyard_log_output.php";
$logger             = LogWriter::getInstance();
$log_level          = getenv("LOG_LEVEL"); // "DEBUG";
$logger->setUp($log_output_php, $log_output_dir, $log_file_prefix, $log_level, $logfile, $logfile);

$msgTplStorage      = MessageTemplateStorageHolder::getMTS();

////////////////////////////////
// DB接続設定
////////////////////////////////
$db_access_user_id  = -121006; // AnsibleTowerサーバデータ同期プロシージャ

////////////////////////////////
// ローカル変数(全体)宣言
////////////////////////////////
$warning_flag       = 0; // 警告フラグ(1：警告発生)
$error_flag         = 0; // 異常フラグ(1：異常発生)

////////////////////////////////
// 共通モジュールの呼び出し
////////////////////////////////
$php_req_gate_php   = $root_dir_path . "/libs/commonlibs/common_php_req_gate.php";
$aryOrderToReqGate  = array("DBConnect" => "LATE"); // DBconnectだけ別タイミングにする
require_once($php_req_gate_php);

////////////////////////////////
// 業務処理開始
////////////////////////////////

$tgt_row_array      = array(); // 処理対象から準備中を除くレコードまるごと格納

$dbAccess = null;
$restApiCaller = null;
try {
    // 開始メッセージ
    $logger->debug(" = Start Procedure. =");

    ////////////////////////////////
    // DBコネクト
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("db connect.");
    $dbAccess = new DBAccesser($db_access_user_id);
    $dbAccess->connect();

    ////////////////////////////////
    // インターフェース情報を取得する
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("Get interface info.");
    $ifInfoRows = $dbAccess->selectRows("B_ANSIBLE_IF_INFO");   

    $num_of_rows = count($ifInfoRows);
    // 設定無しの場合
    if($num_of_rows === 0) {
        throw new Exception("No records in if_info.");
    // 重複登録の場合
    } elseif($num_of_rows > 1) {
        throw new Exception("More than one record in if_info.");
    }
   
    // 実行エンジンがAnsible Towerの場合のみ処理続行
    if($ifInfoRows[0]['ANSIBLE_EXEC_MODE'] != 2) {
        exit(0);
    }

    if(strlen(trim($ifInfoRows[0]['ANSTWR_AUTH_TOKEN'])) == 0) {
        exit(0);
    }

    $ansibleTowerIfInfo = $ifInfoRows[0];
    $logger->trace('$ansibleTowerIfInfo : ' . var_export($ansibleTowerIfInfo, true));

    ////////////////////////////////
    // ITA側の既に登録済みのインスタンスグループ情報を取得する
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("Get instance groups from table.");
    $instanceGroupRows = $dbAccess->selectRows("B_ANS_TWR_INSTANCE_GROUP", true);   

    ////////////////////////////////
    // RESTの認証
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("Authorize AnsibleTower.");

    $restApiCaller = new RestApiCaller($ansibleTowerIfInfo['ANSIBLE_PROTOCOL'],    
                                        $ansibleTowerIfInfo['ANSIBLE_HOSTNAME'],
                                        $ansibleTowerIfInfo['ANSIBLE_PORT'],
                                        $ansibleTowerIfInfo['ANSTWR_AUTH_TOKEN']); // 暗号復号は内部処理

    $response_array = $restApiCaller->authorize();
    if($response_array['success'] == false) {
        $logger->trace("URL: " . $ansibleTowerIfInfo['ANSIBLE_PROTOCOL'] . "://"
                                . $ansibleTowerIfInfo['ANSIBLE_HOSTNAME'] . ":"
                                . $ansibleTowerIfInfo['ANSIBLE_PORT'] . "\n"
                                . "TOKEN: " . $ansibleTowerIfInfo['ANSTWR_AUTH_TOKEN'] . "\n");
        throw new Exception("Faild to authorize to ansible_tower. " . $response_array['responseContents']['errorMessage']);
    }

    ////////////////////////////////
    // Towerのインスタンスグループ情報を取得する
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("Get instance groups from ansible_tower.");

    $response_array = AnsibleTowerRestApiInstanceGroups::getAll($restApiCaller);
    if($response_array['success'] == false) {
        throw new Exception("Faild to get data from ansible_tower. " . $response_array['responseContents']['errorMessage']);
    }

    $logger->trace("DEBUG_LOG| FILE: " . __FILE__ . "| LINE: " . __LINE__ . "| \n" . var_export($response_array['responseContents'], true));

    ////////////////////////////////
    // トランザクション開始
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("Begin transaction.");

    $dbAccess->beginTransaction();

    // --------------------------------------------------
    // 
    // 名称ユニークとしてデータ更新の比較のキーをNameとした。
    // クラスタ初期構築時に、"Tower"がデフォルトのはずがID:2が振られ、その他のグループも法則性が無くランダムにIDが振られるようなので、
    // もしクラスタを再構築した際に振られるIDが変わってしまう危険性があると判断したため。
    // ITAもTowerも、ユーザは画面上では名称しか気にしないので、そこを間違えなければ内部のIDは差し換わってもいいはず。
    // 
    // --------------------------------------------------

    // 既存データの検索用Idカラムを作成する
    $instanceGroupNames_inTable = array_column($instanceGroupRows, 'INSTANCE_GROUP_NAME');

    $livingIds = array();
    $tableName = "B_ANS_TWR_INSTANCE_GROUP";  
    // 新規追加 or 更新
    foreach($response_array['responseContents'] as $instanceGroupData_fromTower) {

        $target_index = array_search($instanceGroupData_fromTower['name'], $instanceGroupNames_inTable);
        if($target_index === false) {
            // 見つからない場合は新規
            $logger->trace("new record.");
            $newRow = array(
                "INSTANCE_GROUP_NAME" => $instanceGroupData_fromTower['name'],
                "INSTANCE_GROUP_ID"   => $instanceGroupData_fromTower['id'],
            );

            $rowId = $dbAccess->insertRow($tableName, $newRow);
            $livingIds[] = $rowId;
        } else {
            // 見つかるのであれば更新の可能性 ... 差分があれば更新/復活
            $updateRow = $instanceGroupRows[$target_index];
            if($instanceGroupData_fromTower['id'] != $updateRow['INSTANCE_GROUP_ID']) {
                $logger->trace("update record. [name: " . $updateRow['INSTANCE_GROUP_NAME'] . "]");
                $updateRow['INSTANCE_GROUP_ID'] = $instanceGroupData_fromTower['id'];
                $updateRow['DISUSE_FLAG'] = '0';
                $dbAccess->updateRow($tableName, $updateRow);
            }

            if($updateRow['DISUSE_FLAG'] != '0') {
                $updateRow['DISUSE_FLAG'] = '0';
                $dbAccess->updateRow($tableName, $updateRow);
            }
            $livingIds[] = $updateRow['INSTANCE_GROUP_ITA_MANAGED_ID'];
        }
    }

    // 廃止
    foreach($instanceGroupRows as $row) {

        if($row['DISUSE_FLAG'] != "0") {
            // 既に廃止されているレコードは対象外。
            continue;
        }

        if(in_array($row['INSTANCE_GROUP_ITA_MANAGED_ID'], $livingIds)) {
            // 登録されている場合はなにもしない。
            continue;
        }

        $logger->trace("discard record. [name: " . $row['INSTANCE_GROUP_NAME'] . "]");
        $row['DISUSE_FLAG'] = "1";
        $dbAccess->updateRow($tableName, $row);
    }

    // 名称でデータ操作しているので、最後にIDユニークチェックを入れておきたい
    $livingRows = $dbAccess->selectRows("B_ANS_TWR_INSTANCE_GROUP"); 
    $checker = array();
    $dummy_value = 1;
    foreach($livingRows as $data) {
        if(array_key_exists($data['INSTANCE_GROUP_ID'], $checker)) {
            throw new Exception("Duplicate entry [" . $data['INSTANCE_GROUP_ID'] . "] for 'INSTANCE_GROUP_ID'");
        }
        $checker[$data['INSTANCE_GROUP_ID']] = $dummy_value;
    }

    ////////////////////////////////
    // トランザクション終了
    ////////////////////////////////
    // トレースメッセージ
    $logger->debug("Commit transaction.");

    $dbAccess->commit();

} catch (Exception $e) {

    $error_flag = 1;
    $logger->error("Exception occurred.");

    // 例外メッセージ出力
    $logger->error($e->getMessage());
    $logger->trace($e->getTraceAsString());

    if($dbAccess->inTransaction()) {
        // ロールバック
        if($dbAccess->rollback() === true) {
            $logger->error("Rollback.");
        } else {
            $logger->error("Faild to rollback.");
        }
    }
} finally {

    if(!empty($dbAccess)) {
        $dbAccess = null;
    }

    if(!empty($restApiCaller)) {
        $restApiCaller = null;
    }
}

////////////////////////////////
//// 結果出力
////////////////////////////////
// 処理結果コードを判定してアクセスログを出し分ける
if($error_flag != 0) {
    // 終了メッセージ
    $logger->error(" = Finished Procedure. [state: ERROR] = ");
    exit(2);
} elseif($warning_flag != 0) {
    // 終了メッセージ
    $logger->warn (" = Finished Procedure. [state: WARNING] = ");
    exit(2);
} else {
    // 終了メッセージ
    $logger->debug(" = Finished Procedure. [state: SUCCESS] = ");
    exit(0);
}

// end Main Logic
