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
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100000401/web_functions_for_sym_ope_export.php');
}
catch (Exception $e){
    // ----DBアクセス例外処理パーツ
    require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');
}

$resultMsg = '';

try {
    $SymphonyClassList = $_REQUEST['symphony'];
    $OperationClassList = $_REQUEST['operation'];
    $taskNo = registerExportInfo($SymphonyClassList, $OperationClassList);

    $resultMsg = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900058', array($taskNo));
    $_SESSION['data_export_task_no'] = $taskNo;
    $resultFlg = true;

} catch (Exception $e) {

    $resultMsg = $e->getMessage();
    web_log($resultMsg);
    $resultFlg = false;

}
?>