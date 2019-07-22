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

    // web_parts_for_common.phpにて生成した$privilegeを利用して、
    // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定する
    // $privilegeは、1：メンテナンス可能、2：参照のみ、その他：アクセス不可

    if( $privilege != '1' ){
        // アクセスログ出力(不正アクセス操作)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-47"));

        // アクセスフィルタ画面にリダイレクト
        webRequestForceQuitFromEveryWhere(403,11210201);
        exit();
    }
?>