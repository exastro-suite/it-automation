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
    $script_name = basename($_SERVER['SCRIPT_NAME']);
    if (strpos($ACRCM_representative_file_name, $script_name) === false) {
        require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_for_access_01.php');
    }
    require_once  $g['root_dir_path'] . '/webconfs/systems/2100160003_loadTable.php';

    // メニュー作成用関数群読み込み
    require_once($g['root_dir_path'] . '/libs/webindividuallibs/systems/2100160003/web_functions_for_create_menu.php');

}
catch (Exception $e){
    // ----DBアクセス例外処理パーツ
    require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');
}

// 画面表示の固定値（ラべル）
$headerLabel1 = $g['objMTS']->getSomeMessage('ITAWDCH-STD-30011');
$exportLabel1 = $g['objMTS']->getSomeMessage('ITABASEH-MNU-900001');

$resultMsg = '';

if (isset($_REQUEST['create']) === false) { // 初期表示時
    try {
          $menuNameAry = array();
          $menuNameAry = makeMenuCheckbox();//作成可能メニュー一覧取得　web_functions_for_create_menu.php
          if(empty($menuNameAry)){
              $resultMsg = $g['objMTS']->getSomeMessage("ITACREPAR-MNU-102504");//作成対象がありません。
          }

    } catch (Exception $e) {
        $resultMsg = $e->getMessage();
        // ----DBアクセス例外処理パーツ
        // 想定外エラー画面へ遷移
        require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');
    }

} else if($_REQUEST['create'] === 'create') { // メニュー作成ボタン押下時

    $resultMsg ="";
    $resultMsg .="<H3>".$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102506")."</H3>";//メニュー作成を受け付けました
    try {
        $resultMsg .="<form method='POST' action='/default/menu/01_browse.php?no=2100160004' id='create_menu_status_form'>";
        $count = 0;
        foreach($_POST as $key => $value){
            if($key !== 'create'){
                $seq = insertCMStatus($key);//登録処理部分
                $resultMsg .=$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102507").":[".$seq."]<BR>";

                if($count == 0){
                $resultMsg .= " <input type='hidden' id='start_no' name='start_no' value='".$seq."'>";
                }
                $count = $count + 1 ;
            }
        }
        $resultMsg .=" <input type='hidden' id='end_no' name='end_no' value='".$seq."'>";
        $resultMsg .="<input type='submit' value=".$g['objMTS']->getSomeMessage("ITACREPAR-MNU-102508").">";
        $resultMsg .="</form>";
    } catch (Exception $e) {

        $resultMsg = $e->getMessage();
        web_log($resultMsg);
        // ----DBアクセス例外処理パーツ
        // 想定外エラー画面へ遷移
        require_once ($g['root_dir_path'] . '/libs/webcommonlibs/web_parts_db_access_exception.php');

    }

} else {

    // 不正アクセスで処理終了
    web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-31'));
    webRequestForceQuitFromEveryWhere(400, 10310201);

}

