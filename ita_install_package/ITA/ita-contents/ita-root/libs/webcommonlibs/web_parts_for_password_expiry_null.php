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

    $tmpAryRetBody = checkLoginPasswordExpiryOut($username,$p_login_pw_l_update,$pass_word_expiry,$objDBCA);
    if( $tmpAryRetBody[1] !== null ){
        $tmpErrMsgBody = $tmpAryRetBody[3];

        // アクセスログ出力(想定外エラー)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-35",$tmpErrMsgBody));

        // 想定外エラー通知画面にリダイレクト
        webRequestForceQuitFromEveryWhere(500,10710101);
        exit();
    }
    if( $tmpAryRetBody[0]['ExpiryOut'] === true ){
        // アクセスログ出力(有効期限が切れ)
        web_log($objMTS->getSomeMessage("ITAWDCH-ERR-34"));

        // パスワード変更画面にリダイレクト
        $arrayRedirect = array('expiry'=>$tmpAryRetBody[0]['ReasonType'],'username'=>$username);
        webRequestForceQuitFromEveryWhere(
            401,
            10710501,
            array('InsideRedirectMode'=>1,
                  'MenuID'=>$ACRCM_id,
                  'MenuGroupID'=>$ACRCM_group_id,
                  'ValueForPost'=>$arrayRedirect
                 )
        );
        exit();
    }
    unset($tmpAryRetBody);
?>