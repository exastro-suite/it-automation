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

global $g;
$tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
if(array_key_exists('no', $_GET)){
    $g['page_dir']  = $_GET['no'];
}

// メニューIDの桁数
define('MENU_ID_LENGTH', 11);

try{
    // DBコネクト
    require_once ( $g['root_dir_path'] . '/libs/commonlibs/common_php_req_gate.php');
    // 共通設定取得パーツ
    require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_get_sysconfig.php');
    // メニュー情報取得パーツ
    require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_menu_info.php');

    // access系共通ロジックパーツ01
    $script_name = basename($_SERVER['SCRIPT_NAME']);
    if (strpos($ACRCM_representative_file_name, $script_name) === false) {
        if (strpos($_SERVER['HTTP_REFERER'], $script_name) === false) {
            require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_for_access_01.php');
        }
    }

    // データポータビリティ用関数群読み込み
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000211/web_functions_for_data_portability.php');
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000212/web_functions_for_data_import.php');
}
catch (Exception $e){
    // ----DBアクセス例外処理パーツ
    require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');
}

// 最終更新者「データポータビリティプロシージャ」
define('ACCOUNT_NAME', -100024);

// 画面表示の固定値（ラべル）を用意する
$headerLabel1 = $g['objMTS']->getSomeMessage('ITAWDCH-STD-30011');
$uploadLabel1 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900002');
$uploadLabel2 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900010');
$importLabel1 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900003');
$importLabel2 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900004');
$importLabel3 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900019');

// javascript用のメッセージを用意する
$aryImportFilePath[] = $g['objMTS']->getTemplateFilePath('ITABASEC', 'STD', '_js');
$strTemplateBody = getJscriptMessageTemplate($aryImportFilePath, $g['objMTS']);

$menuOn = getMenuOn();

$resultMsg = '';
$retImportAry = array();

if (isset($_REQUEST['post_kind']) === false || strlen($_REQUEST['post_kind']) === 0) { // 初期表示
    try {

    } catch (Exception $e) {
        web_log($e->getMessage());
        $resultMsg = $e->getMessage();
    }
} else if ($_REQUEST['post_kind'] === 'upload') { // アップロード
    try {

        $_SESSION['upload_id'] = date('YmdHis') . mt_rand();

        // ファイルアップロード
        uploadZipFile();

        // zip解凍
        unzipImportData();

        // zipファイルの中身確認
        checkZipFile();

        $retImportAry = makeImportCheckbox();

    } catch (Exception $e) {
        web_log($e->getMessage());
        $resultMsg =  $e->getMessage();
        $retImportAry =  $e->getMessage();
    }
} else if($_REQUEST['post_kind'] === 'import') { // インポート
    class DBException extends Exception{}

    try {

        if (isset($_SESSION['upload_id']) === true && strlen($_SESSION['upload_id']) > 0) {
    echo "<div id='SetsumeiMidashi'></div>";
    echo "<div id='SetsumeiNakami'></div>";
    echo "<div id='sysJSCmdText01'></div>";
    echo "<div id='sysJSCmdText02'></div>";
    echo "<div id='Mix2_Nakami'></div>";
    echo "<div id='Mix2_Midashi'></div>";
    
            $uploadId = $_SESSION['upload_id'];

            if(isset($_REQUEST['importButton'])){
                $importType = 1;
            }
            else{
                $importType = 2;
            }

            // 入力値チェック
            checkInputFormat();

            // POSTされたメニューIDリストを作成
            makeImportMenuIdList();

            // データ登録
            $taskNo = insertTask($importType);
            $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900009', array($taskNo));
            $_SESSION['data_import_task_no'] = $taskNo;

            renameImportFiles($taskNo);

            moveZipFile($taskNo);

            $dirPath = $g['root_dir_path'] . '/temp/data_import/import/' . $uploadId;
            removeFiles($dirPath);;

            $filePath = $g['root_dir_path'] . '/temp/data_import/import/' . $taskNo . '_ita_data.zip';
            if (file_exists($filePath) === true) {
                unlink($filePath);
            }
        }

        $resultFlg = true;
        unset($_SESSION['upload_id']);

    } catch (DBException $e) {
        web_log($e->getMessage());
        $res = $g['objDBCA']->transactionRollBack();
        if ($res === false) {
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900050', array(__FILE__, __LINE__)));
            throw new DBException($g['objMTS']->getSomeMessage('ITABASEH-ERR-900002'));
        }
        $resultMsg = $e->getMessage();
        $resultFlg = false;

    } catch (Exception $e) {
        web_log($e->getMessage());
        $resultMsg = $e->getMessage();
        $resultFlg = false;
    }
} else {
    // 不正なアクセス
    web_log($g['objMTS']->getSomeMessage('ITAWDCH-MNU-1140002'));
    webRequestForceQuitFromEveryWhere(400, 10310201);
}
