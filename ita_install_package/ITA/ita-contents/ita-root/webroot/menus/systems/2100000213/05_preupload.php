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

$tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);

// DBアクセスを伴う処理を開始
try{
    //----ここから01_系から06_系全て共通
    // DBコネクト
    require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
    // 共通設定取得パーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
    // メニュー情報取得パーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
    //ここまで01_系から06_系全て共通----

    // browse系共通ロジックパーツ01
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");
}
catch (Exception $e){
    // DBアクセス例外処理パーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
}

$menuId = filter_input(INPUT_GET, 'no');
$fileName = filter_input(INPUT_GET, 'fn');
if (preg_match('/^[a-z0-9._]+$/', $fileName)) {
    $filePath = $root_dir_path . "/uploadfiles/{$menuId}/{$fileName}";

    if (file_exists($filePath) === true) {
        $content_length = filesize($filePath);
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Length: ".$content_length);
        header("Content-Type: application/octet-stream");
        header('Content-Transfer-Encoding: binary');
        header("Connection: close");
        ob_end_flush();
        readfile($filePath);
    } else {
        webRequestForceQuitFromEveryWhere(400, 10310201);
    }
} else {
    webRequestForceQuitFromEveryWhere(400, 10310201);
}
web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-603"));

