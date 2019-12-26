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
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000402/web_functions_for_sym_ope_import.php');
}
catch (Exception $e){
    // ----DBアクセス例外処理パーツ
    require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');
}

// javascript用のメッセージを用意する
$aryImportFilePath[] = $g['objMTS']->getTemplateFilePath('ITABASEC', 'STD', '_js');
$strTemplateBody = getJscriptMessageTemplate($aryImportFilePath, $g['objMTS']);

$resultMsg = '';
$retImportAry = array();

if (isset($_REQUEST['post_kind']) === false || strlen($_REQUEST['post_kind']) === 0) { // 初期表示

} else if ($_REQUEST['post_kind'] === 'upload') { // アップロード
    try {

        $_SESSION['upload_id'] = date('YmdHis') . mt_rand();

        // ファイルアップロード
        $fileName = uploadZipFile();

        // zip解凍
        unzipImportData($fileName);

        // zipファイルの中身確認
        checkZipFile($fileName);

        $retImportAry = makeImportCheckbox();

    } catch (Exception $e) {
        web_log($e->getMessage());
        $resultMsg =  $e->getMessage();

        $retImportAry =  $e->getMessage();

    }
} else if($_REQUEST['post_kind'] === 'import') { // インポート

    try {
        if (isset($_SESSION['upload_id']) === true && strlen($_SESSION['upload_id']) > 0) {
            echo "<div id='SetsumeiMidashi'></div>";
            echo "<div id='SetsumeiNakami'></div>";
            echo "<div id='sysJSCmdText01'></div>";
            echo "<div id='sysJSCmdText02'></div>";
            echo "<div id='Mix2_Nakami'></div>";
            echo "<div id='Mix2_Midashi'></div>";
    
            $uploadId = $_SESSION['upload_id'];

            // POSTされたIDリストを作成
            $targetList = makeImportIdList();

            // トランザクション開始
            $varTrzStart = $g['objDBCA']->transactionStart();
            if ($varTrzStart === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900015',
                                                     array(basename(__FILE__), __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }

            // データ登録
            insertTask($targetList, $taskNo, $jnlSeqNo);

            $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900060', array($taskNo));
            $_SESSION['data_import_task_no'] = $taskNo;

            moveImportFile($taskNo, $jnlSeqNo);

            $res = $g['objDBCA']->transactionCommit();
            if ($res === false) {
                web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900036',
                                                     array(basename(__FILE__), __LINE__)));
                throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900074'));
            }
        }

        $resultFlg = true;
        unset($_SESSION['upload_id']);

    } catch (Exception $e) {
        web_log($e->getMessage());
        $res = $g['objDBCA']->transactionExit();
        if ($res === false) {
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900050', array(__FILE__, __LINE__)));
        }
        $resultMsg = $e->getMessage();
        $resultFlg = false;
    }
} else {
    // 不正なアクセス
    web_log($g['objMTS']->getSomeMessage('ITAWDCH-MNU-1140002'));
    webRequestForceQuitFromEveryWhere(400, 10310201);
}
