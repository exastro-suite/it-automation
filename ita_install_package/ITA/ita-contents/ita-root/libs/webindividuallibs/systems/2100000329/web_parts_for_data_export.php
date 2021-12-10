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

global $g;
$tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
if(array_key_exists('no', $_GET)){
    $g['page_dir']  = htmlspecialchars($_GET['no'], ENT_QUOTES, "UTF-8");
}

// メニューIDの桁数
define('MENU_ID_LENGTH', 11);
// インポートファイル一つに保存するレコード数
define('MAX_RECORD_CNT', 1000);

// ----DBアクセスを伴う処理
try{
    // DBコネクト
    require_once ($g['root_dir_path'] . '/libs/commonlibs/common_php_req_gate.php');
    // 共通設定取得パーツ
    require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_get_sysconfig.php');
    // メニュー情報取得パーツ
    require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_menu_info.php');

    // access系共通ロジックパーツ01
    $script_name = basename(htmlspecialchars($_SERVER['SCRIPT_NAME'], ENT_QUOTES, "UTF-8"));
    if (strpos($ACRCM_representative_file_name, $script_name) === false) {
        require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_for_access_01.php');
    }

    // データポータビリティ用関数群読み込み
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000329/web_functions_for_data_portability.php');
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000329/web_functions_for_bulk_excel_export.php');
}
catch (Exception $e){
    // ----DBアクセス例外処理パーツ
    require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');
}

// 画面表示の固定値（ラべル）
$headerLabel1 = $g['objMTS']->getSomeMessage('ITAWDCH-STD-30011');
$exportLabel1 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900001');

// Javascript用のメッセージ
$aryImportFilePath[] = $g['objMTS']->getTemplateFilePath('ITABASEC', 'STD', '_js');
$aryImportFilePath[] = $g['objMTS']->getTemplateFilePath('ITAWDCC', 'STD', '_js');
$strTemplateBody = getJscriptMessageTemplate($aryImportFilePath, $g['objMTS']);

// 選択中のメニューを青くするための処理
$menuOn = getMenuOn();

$resultMsg = '';

if (isset($_REQUEST['zip']) === false) { // 初期表示時
    try {
        // メニューグループとメニューを取得
        $menuGroupAry = makeExportCheckbox();

        // loadTableを使っていないメニューを除去する
        $retExportAry = getExportMenuList($menuGroupAry);
        if (empty($retExportAry)) {
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000329_3'));
        }

    } catch (Exception $e) {
        $resultMsg = $e->getMessage();
    }
} else if($_REQUEST['zip'] === 'export') { // エクスポートボタン押下時
    try {

        $dirName = date('YmdHis') . mt_rand();

        $exportMenuIdAry = makeExportDataList($dirName);

        // データ登録
        $taskNo = insertTask();
        $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-2100000329_3', array($taskNo));
        $_SESSION['data_export_task_no'] = $taskNo;

        renameExportDir($dirName, $taskNo);
        $resultFlg = true;

    } catch (Exception $e) {

        $resultMsg = $e->getMessage();
        $resultFlg = true;

    }

} else {

    // 不正アクセスで処理終了
    web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-31'));
    webRequestForceQuitFromEveryWhere(400, 10310201);

}

