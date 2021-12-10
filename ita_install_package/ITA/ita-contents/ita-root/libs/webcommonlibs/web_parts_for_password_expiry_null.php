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
 
    $passwordSettings = getPasswordOtherSettings($user_id,$objDBCA);
    
    $pwExpiration = $passwordSettings["PW_EXPIRATION"]; // パスワード無期限設定
    $deactivatePwChange = $passwordSettings["DEACTIVATE_PW_CHANGE"]; // 初回パスワード再設定無効
    $lastLoginTime = $passwordSettings["LAST_LOGIN_TIME"]; // 最終ログイン日時
    $pwLastUpdTime = $passwordSettings["PW_LAST_UPDATE_TIME"]; // パスワード最終更新日時
    $userId = $passwordSettings["USER_ID"]; // ユーザーID
    $oldPassword = $passwordSettings["PASSWORD"];
    $tempBoolPassWordChange = false;

    $pw_l_up_flg = 0;
    $last_log_flg = 0;
    $strReasonType = "";

    // ----■システム設定情報を用いてパスワードの有効期限の設定がされているかを、チェックする。
    if(isset($pass_word_expiry)){
        // ----設定テーブルに、パスワードの有効期限に関する設定があった場合
        if($pass_word_expiry == "0"){
            $tempIntLength = 0;
        }
        else{
            $tempIntLength = intval($pass_word_expiry);
        }
        // 設定テーブルに、パスワードの有効期限に関する設定があった場合----
    }
    else{
        // ----設定テーブルに、パスワードの有効期限に関する設定がなかった場合
        $tempIntLength = 0;
        // 設定テーブルに、パスワードの有効期限に関する設定がなかった場合----
    }
    // ■システム設定情報を用いてパスワードの有効期限の設定がされているかを、チェックする。----    

    if(0 < $tempIntLength){
        $tmpIntPWLDUnixTime = strtotime($pwLastUpdTime);
        $tempRequestTime = htmlspecialchars($_SERVER["REQUEST_TIME"], ENT_QUOTES, "UTF-8");
        if($tmpIntPWLDUnixTime + ($pass_word_expiry * 86400) < $tempRequestTime ){
            $tempBoolPassWordChange = true;
            $strReasonType = "1";
        }


        if( $pwLastUpdTime == '' || $tempBoolPassWordChange === true ){
            if($pwExpiration != '1'){

                if( $pwLastUpdTime == '' ){
                    $strReasonType = "0";
                }

                if($lastLoginTime != "" || $deactivatePwChange != '1'){
                    // アクセスログ出力(有効期限が切れ)
                    web_log($objMTS->getSomeMessage("ITAWDCH-ERR-34"));
            
                    // パスワード変更画面にリダイレクト
                    $arrayRedirect = array('expiry'=>$strReasonType,'username'=>$username);
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
            }
        } // ユーザーID

    }

    if($lastLoginTime == null && $deactivatePwChange == '1'){
        $pw_l_up_flg = 1;
        $last_log_flg = 1;
    }

    $loginUrl = "";
    if(isset(explode("?", $_SERVER['HTTP_REFERER'])[1])){
        $loginUrl = isset(explode("&", explode("?", $_SERVER['HTTP_REFERER'])[1])[0]) ? explode("&", explode("?", $_SERVER['HTTP_REFERER'])[1])[0] : "";
    }

    if($pwExpiration == '1' && (strpos($loginUrl,'login') !== false) && ($pwLastUpdTime == null || $pwLastUpdTime == "")){

        $pw_l_up_flg = 1;

    }


    if((strpos($loginUrl,'login') !== false) && ($pwLastUpdTime == null || $pwLastUpdTime == "")){
        $pw_l_up_flg = 1;
    }

    if(strpos($loginUrl,'login') !== false){
        $last_log_flg = 1;
    }

    if($last_log_flg == '1' || $pw_l_up_flg == '1'){

        if($pw_l_up_flg == '1'){
            $tmpStrSql = "UPDATE A_ACCOUNT_LIST SET LAST_LOGIN_TIME = SYSDATE(), PW_LAST_UPDATE_TIME = SYSDATE() WHERE USER_ID = :USERID_BV";
            $tmpArrayBind = array('USERID_BV'=>$user_id);
            $objQuery = $objDBCA->sqlPrepare($tmpStrSql);
            $objQuery->sqlBind($tmpArrayBind);
            $r = $objQuery->sqlExecute();
        
            $g['objDBCA'] = $objDBCA;
            $tempArrayRet = updateUserLastLogin($user_id,$oldPassword,$oldPassword,$objDBCA);
            unset($tempArrayRet);
        }else{

            $tmpStrSql = "UPDATE A_ACCOUNT_LIST SET LAST_LOGIN_TIME = SYSDATE() WHERE  USER_ID = :USERID_BV";
            $tmpArrayBind = array('USERID_BV'=>$user_id);
            $objQuery = $objDBCA->sqlPrepare($tmpStrSql);
            $objQuery->sqlBind($tmpArrayBind);
            $r = $objQuery->sqlExecute();
        }
        
    }

?>
