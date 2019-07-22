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
    
    // ルートディレクトリを取得
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    // アクセスログ出力
    $aryOrderToReqGate = array();
    $aryOrderToReqGate['DBConnect'] = 'LATE';
    require( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php" );
    web_log("");
    
    // 管理者連絡先を読み込み
    $ADMIN_OFFICE = file_get_contents( $root_dir_path . "/confs/webconfs/admin_mail_addr.txt" );
    $strMailTag = "";
    if( 0 < strlen($ADMIN_OFFICE) ){
        $strMailTag = $objMTS->getSomeMessage("ITAWDCH-MNU-1140003",$ADMIN_OFFICE);
    }
    
    //不正アクセス
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_favicon_ico=filemtime("$root_dir_path/webroot/common/imgs/favicon.ico");
    
    // ここから本文(PHP)
    print 
<<< EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="Content-Language" content="ja">
    <meta http-equiv="Content-Script-Type" content="text/javascript">
    <meta http-equiv="content-style-type" content="text/css">
    <link rel="shortcut icon" href="{$scheme_n_authority}/common/imgs/favicon.ico?{$timeStamp_favicon_ico}" type="image/vnd.microsoft.icon">
    <title>{$objMTS->getSomeMessage("ITAWDCH-MNU-1140001")}</title>
</head>
<body>
    <br>
    {$objMTS->getSomeMessage("ITAWDCH-MNU-1140002")}<br>
    {$strMailTag}<br>
</body>
</html>
EOD;
?>
