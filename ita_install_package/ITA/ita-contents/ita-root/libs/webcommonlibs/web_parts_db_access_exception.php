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

    // アクセスログ出力(想定外エラー)
    $aryTemp1 = debug_backtrace($limit=1);
    $FREE_LOG = 'ERROR:UNEXPECTED,JUMP FROM ([FILE]'.$aryTemp1[0]['file'].',[LINE]'.$aryTemp1[0]['line'].') TO ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
    require_once ( dirname(__FILE__). "/web_php_functions.php" );

    web_log($FREE_LOG);

    // 想定外エラー通知画面にリダイレクト
    webRequestForceQuitFromEveryWhere(500,10210101); //102-101-01
    exit();
?>
