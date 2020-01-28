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

/**
 * Symphony/オペレーションエクスポート画面
 *
 */
global $g;
// ルートディレクトリを取得
$tmpAry=explode('ita-root', dirname(__FILE__));$g['root_dir_path']=$tmpAry[0].'ita-root';unset($tmpAry);
if(array_key_exists('no', $_GET)){
    $g['page_dir']  = $_GET['no'];
}

$param = explode ( "?" , $_SERVER["REQUEST_URI"] , 2 );
if(count($param) == 2){
    $url_add_param = "&" . $param[1];
}
else{
    $url_add_param = "";
}

// DBアクセスを伴う処理を開始
try{
    //----ここから01_系から06_系全て共通
    // DBコネクト
    require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
    // 共通設定取得パーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
    // メニュー情報取得パーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
    //ここまで01_系から06_系全て共通----

    // browse系共通ロジックパーツ01
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_01.php");
}
catch (Exception $e){
    // DBアクセス例外処理パーツ
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
}

// 共通HTMLステートメントパーツ
require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_html_statement.php");

// browse系共通ロジックパーツ02
require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_browse_02.php");

require_once ( $g['root_dir_path'] . "/libs/webindividuallibs/systems/2100000401/web_parts_for_sym_ope_export.php");
// アクセスログ出力(RESULT:SUCCESS)
web_log($g['objMTS']->getSomeMessage('ITAWDCH-STD-603'));
$task_no = '';
if (isset($_SESSION['data_export_task_no']) === true && strlen($_SESSION['data_export_task_no']) > 0) {
    $task_no = $_SESSION['data_export_task_no'];
}
?>
<h2><?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900057'); ?></h2>
<?php if ($resultFlg === true): ?>
<form method="post" action="/default/menu/01_browse.php?no=2100000403&task_no=<?php echo $task_no; ?>">
    <div style="margin:10px 0 0 10px;">
        <p><?php echo $resultMsg; ?></p>
        <input type="submit" value="<?php echo $g['objMTS']->getSomeMessage('ITABASEH-MNU-900056'); ?>">
    </div>
</form>
<?php else: ?>
<form method="post" action="/default/menu/01_browse.php?no=<?php echo $g['page_dir']; ?>">
    <div style="margin:10px 0 0 10px;">
        <p><?php echo $resultMsg; ?></p>
        <input type="submit" value="<?php echo $g['objMTS']->getSomeMessage('ITAWDCH-MNU-1040060'); ?>">
    </div>
</form>
<?php endif;?>
<?php
// 共通HTMLフッタパーツ
require_once ( $g['root_dir_path'] . '/libs/webcommonlibs/web_parts_html_footer.php');
?>