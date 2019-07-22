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
    //////////////////////////////////////////////////////////////////////
    //
    //  【処理概要】
    //    ・管理コンソールのメニュー関連の情報を取得する
    //
    //////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ここから、ユーザに関する、その他一般的な情報取得に関する機能                                   //
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    // ----あるユーザが、あるメニューグループのうち、表示できるメニューについて、その代表ページとリンクを取得する
    function getFilenameAndMenuNameByMenuGroupID($ACRCM_group_id,$login_status_flag,$username,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $num_rows = 0;

        $menu_name_array = array();
        $menu_id_array = array();

        $strFxName = __FUNCTION__; // getMenuInfosByContentFilename

        try{
            if( $login_status_flag == 1 ){
                // ----ログイン中の場合

                // ログインアカウントが所属するロールに紐付くメニューと、
                // ログイン不要でも見せていいメニューを一覧として表示する

                $sql = "SELECT "
                      ."    A_MENU_LIST.MENU_ID,"
                      ."    A_MENU_LIST.MENU_NAME "
                      ."FROM "
                      ."    A_MENU_LIST "
                      ."WHERE "
                      ."    A_MENU_LIST.MENU_GROUP_ID = :MENU_GROUP_ID_BV "
                      ."    AND "
                      ."    ( "
                      ."        A_MENU_LIST.MENU_ID IN ( SELECT "
                      ."                                     MENU_ID "
                      ."                                 FROM "
                      ."                                     A_ROLE_MENU_LINK_LIST "
                      ."                                 WHERE "
                      ."                                     ROLE_ID IN ( SELECT "
                      ."                                                      ROLE_ID "
                      ."                                                  FROM "
                      ."                                                      A_ROLE_ACCOUNT_LINK_LIST "
                      ."                                                  WHERE "
                      ."                                                      USER_ID = ( SELECT "
                      ."                                                                      USER_ID "
                      ."                                                                  FROM "
                      ."                                                                      A_ACCOUNT_LIST "
                      ."                                                                  WHERE "
                      ."                                                                      USERNAME = :USERNAME_BV "
                      ."                                                                      AND "
                      ."                                                                      DISUSE_FLAG = '0' "
                      ."                                                                 ) "
                      ."                                                      AND "
                      ."                                                      DISUSE_FLAG = '0' "
                      ."                                                ) "
                      ."                                     AND "
                      ."                                     DISUSE_FLAG = '0' "
                      ."                                ) "
                      ."       OR  A_MENU_LIST.LOGIN_NECESSITY = '0' "
                      ."    ) "
                      ."    AND "
                      ."    A_MENU_LIST.DISUSE_FLAG = '0' "
                      ."ORDER BY A_MENU_LIST.DISP_SEQ ";

                $tmpArrayBind = array('MENU_GROUP_ID_BV'=>$ACRCM_group_id, "USERNAME_BV"=>$username);
                // ログイン中の場合----
            }
            else{
                // ----未ログインの場合

                // ログイン不要でも見せていいメニューを一覧として表示する

                $sql = "SELECT "
                      ."    A_MENU_LIST.MENU_ID,"
                      ."    A_MENU_LIST.MENU_NAME "
                      ."FROM "
                      ."    A_MENU_LIST "
                      ."WHERE "
                      ."    A_MENU_LIST.MENU_GROUP_ID = :MENU_GROUP_ID_BV "
                      ."    AND "
                      ."    A_MENU_LIST.LOGIN_NECESSITY = '0' "
                      ."    AND "
                      ."    A_MENU_LIST.DISUSE_FLAG = '0' "
                      ."ORDER BY DISP_SEQ ";

                $tmpArrayBind = array('MENU_GROUP_ID_BV'=>$ACRCM_group_id);
                // 未ログインの場合----
            }
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            while( $row = $objQuery->resultFetch() ){
                $menu_name_array[$num_rows] = $row['MENU_NAME'];
                $menu_id_array[$num_rows] = $row['MENU_ID'];
                $num_rows += 1;
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('MenuNames'=>$menu_name_array,
                           'MenuIds'  =>$menu_id_array);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // あるユーザが、あるメニューグループのうち、表示できるメニューについて、その代表ページとリンクを取得する----

    // ----あるユーザが、あるメニューにもつ権限を取得する
    function getUserPrivilegesForMenuByUsername($ACRCM_id,$username,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        // ----次のもの≪
        //     テーブル【アカウントリスト】、テーブル【ロールアカウント紐付リスト】、テーブル【ロールメニュー紐付リスト】、セッション.ユーザ名
        // ≫を用いて、セッション.ユーザ名のユーザが保有する、リクエストされたメニューに対する権限を取得する。
        //

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $num_rows = 0;
        $privilege = null;

        $strFxName = __FUNCTION__; // getUserPrivilegesForMenuByUsername

        try{
            $sql = "SELECT "
                  ."    PRIVILEGE "
                  ."FROM "
                  ."    A_ROLE_MENU_LINK_LIST "
                  ."WHERE "
                  ."    MENU_ID = :MENU_ID_BV "
                  ."    AND "
                  ."    ROLE_ID IN ( SELECT "
                  ."                     ROLE_ID "
                  ."                 FROM "
                  ."                     A_ROLE_ACCOUNT_LINK_LIST "
                  ."                 WHERE "
                  ."                     USER_ID = (SELECT "
                  ."                                    USER_ID "
                  ."                                FROM "
                  ."                                    A_ACCOUNT_LIST "
                  ."                                WHERE "
                  ."                                    USERNAME = :USERNAME_BV "
                  ."                                    AND "
                  ."                                    DISUSE_FLAG = '0' "
                  ."                               ) "
                  ."                     AND "
                  ."                     DISUSE_FLAG = '0' "
                  ."               ) "
                  ."    AND "
                  ."    DISUSE_FLAG = '0' ";

            $tmpArrayBind = array('MENU_ID_BV'=>$ACRCM_id,'USERNAME_BV'=>$username);

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            while( $row = $objQuery->resultFetch() ){
                if( $row['PRIVILEGE'] == 1 || $row['PRIVILEGE'] == 2 ){
                    //----■セッション.ユーザ名のユーザが保有する、リクエストされたメニューに対する権限を、当該リクエストに対する処理が終了するまでに限って、リクエストに対して与える。
                    $num_rows += 1;
                    if( $row['PRIVILEGE'] == 1 ){
                        // ----メンテナンス可能
                        $privilege = $row['PRIVILEGE'];
                        break;
                        // メンテナンス可能----
                    }
                    else if( $row['PRIVILEGE'] == 2 && $privilege != 1 ){
                        // ----閲覧のみ
                        $privilege = $row['PRIVILEGE'];
                        // 閲覧のみ----
                    }
                    //セッション.ユーザ名のユーザが保有する、リクエストされたメニューに対する権限を、当該リクエストに対する処理が終了するまでに限って、リクエストに対して与える。■----
                }
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'Privilege'=>$privilege);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // あるユーザが、あるメニューにもつ権限を取得する----

    // ----一定範囲のロールを、あるユーザがもっているかを調べる
    function searchOneUserRolesLengthByUsername($username,$searchSomeRoles,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $num_rows = null;

        $arrayTempRow = array();

        $strFxName = __FUNCTION__; // checkIPByPermittedWhiteList

        try{
            $sql = "SELECT "
                  ."    ROLE_ID "
                  ."FROM "
                  ."    A_ROLE_ACCOUNT_LINK_LIST "
                  ."WHERE "
                  ."    DISUSE_FLAG = '0' "
                  ."    AND "
                  ."    ROLE_ID IN (" . $searchSomeRoles . ") "
                  ."    AND "
                  ."    USER_ID = ( SELECT "
                  ."                    USER_ID "
                  ."                FROM "
                  ."                    A_ACCOUNT_LIST "
                  ."                WHERE "
                  ."                    USERNAME = :USERNAME_BV "
                  ."                    AND "
                  ."                DISUSE_FLAG = '0' "
                  ."              ) ";

            $tmpArrayBind = array('USERNAME_BV'=>$username);

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            while( $row = $objQuery->resultFetch() ){
                $num_rows += 1;
                $arrayTempRow[$num_rows - 1] = $row;
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows
                          ,'Items'=>$arrayTempRow);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // 一定範囲のロールを、あるユーザがもっているかを調べる----

    // ----ユーザ名から、ユーザ情報を取得する
    function getUserInfosByUsername($username,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        // ----次のもの≪
        //     テーブル【アカウント】とセッション.ユーザ名
        // ≫を用いて、ユーザの各情報（
        //     ユーザＩＤ、ユーザ名(和名)、パスワード最終更新日時
        // ）、を取得する。
        //

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $num_rows = 0;

        $user_id = null;
        $username_jp = null;
        $user_pw_l_update = null;

        $strFxName = __FUNCTION__; // checkPasswordExpiryOut

        try{
            $db_model_ch = $objDBCA->getModelChannel();

            $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($db_model_ch,"PW_LAST_UPDATE_TIME","DATEDATE",false);
            $strSelectAliasPWLastUpdate = "(CASE WHEN PW_LAST_UPDATE_TIME IS NULL THEN NULL ELSE {$tmpStrSelectPart} END)";
            unset($tmpStrSelectPart);

            $sql = "SELECT "
                  ."    USERNAME_JP, "
                  ."    USER_ID, "
                  ."    {$strSelectAliasPWLastUpdate} ALIAS_PW_LAST_UPDATE "
                  ."FROM "
                  ."    A_ACCOUNT_LIST "
                  ."WHERE "
                  ."    USERNAME = :USERNAME_BV "
                  ."    AND "
                  ."    DISUSE_FLAG = '0' ";

            $tmpAryBind = array('USERNAME_BV'=>$username);

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 例外処理へ
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($tmpAryBind) != "" ){
                // 例外処理へ
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                // 例外処理へ
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            while( $row = $objQuery->resultFetch() ){
                $user_id     = $row['USER_ID'];
                $username_jp = $row['USERNAME_JP'];
                $user_pw_l_update = $row['ALIAS_PW_LAST_UPDATE'];

                $num_rows += 1;
            }
            if( $num_rows !=1 ){
                $intErrorType = 502;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'UserID'=>$user_id,
                           'UserDisplayName'=>$username_jp,
                           'PasswordLastUpdateTime'=>$user_pw_l_update);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // ユーザ名から、ユーザ情報を取得する----

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ここまで、ユーザに関する、その他一般的な情報取得に関する機能                                   //
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ここからユーザ自身による、パスワード変更に関する機能                                           //
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    function updateUserPasswordByUserSelf($strFixedId,$strRawOldPassword,$strRawNewPassword,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：あり           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $boolRetStatus = false;

        $boolTransactionFlag = false;        

        $strFxName = __FUNCTION__; // updateUserPasswordByUserSelf

        $arrayConfig = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "USER_ID"=>"",
            "USERNAME"=>"",
            "PASSWORD"=>"",
            "USERNAME_JP"=>"",
            "MAIL_ADDRESS"=>"",
            "PW_LAST_UPDATE_TIME"=>"DATETIMEAUTO(6)",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        $arrayValueTmpl = array(
            "JOURNAL_SEQ_NO"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "USER_ID"=>"",
            "USERNAME"=>"",
            "PASSWORD"=>"",
            "USERNAME_JP"=>"",
            "MAIL_ADDRESS"=>"",
            "PW_LAST_UPDATE_TIME"=>"",
            "NOTE"=>"",
            "DISUSE_FLAG"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>""
        );

        try{
            $db_model_ch = $objDBCA->getModelChannel();

            if( $objDBCA->transactionStart() !== true ){
                // 例外処理へ
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $boolTransactionFlag = true;

            $arrayValue = $arrayValueTmpl;

            $temp_array = array('WHERE'=>"USER_ID = :USER_ID AND PASSWORD = :PASSWORD AND DISUSE_FLAG IN ('0','1') ");

            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "SELECT FOR UPDATE",
                                            "USER_ID",
                                            "A_ACCOUNT_LIST",
                                            "A_ACCOUNT_LIST_JNL",
                                            $arrayConfig,
                                            $arrayValue,
                                            $temp_array
            );

            $aryResult01 = array();
            if( $retArray[0] === false ){
                // 例外処理へ
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $arrayUtnBind['USER_ID']  = $strFixedId;
            $arrayUtnBind['PASSWORD'] = md5($strRawOldPassword);
            
            $objQuery = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQuery->getStatus()===false ){
                // 例外処理へ
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            if( $objQuery->sqlBind($arrayUtnBind) != "" ){
                // 例外処理へ
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $r = $objQuery->sqlExecute();
            if(!$r){
                // 例外処理へ
                throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            //----発見行だけループ
            while ( $row = $objQuery->resultFetch() ){
                $aryResult01[] = $row;
            }
            //発見行だけループ----
            $intEffectCount = $objQuery->effectedRowCount();
            unset($objQuery);
            
            if( $intEffectCount == 0 ){
                // 例外処理へ
                throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            else if( $intEffectCount == 1 ){
                $arrayValue           = $aryResult01[0];
                $org_disuse_flag      = $arrayValue['DISUSE_FLAG'];
                //
                if( $org_disuse_flag === '1' ){
                    // 例外処理へ
                    $intErrorType = 504;
                    throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
            }
            else{
                // 例外処理へ
                throw new Exception( '00000800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $retArray = getSequenceValueFromTable('JSEQ_A_ACCOUNT_LIST', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // 例外処理へ
                throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $intJnlSeqNo = $retArray[0];
            
            $objDBCA->setQueryTime();
            $strQueryTimeDate = $objDBCA->getQueryTime(1);
            
            $arrayValue['JOURNAL_SEQ_NO']      = $intJnlSeqNo;
            $arrayValue['LAST_UPDATE_USER']    = $strFixedId;
            $arrayValue['PASSWORD']            = md5($strRawNewPassword);
            $arrayValue['PW_LAST_UPDATE_TIME'] = $strQueryTimeDate;
            
            $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                            "UPDATE",
                                            "USER_ID",
                                            "A_ACCOUNT_LIST",
                                            "A_ACCOUNT_LIST_JNL",
                                            $arrayConfig,
                                            $arrayValue
            );

            if( $retArray[0] === false ){
                throw new Exception( '00001000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];

            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

            if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                // 例外処理へ
                throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                // 例外処理へ
                throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            //----SQL実行
            $rUtn = $objQueryUtn->sqlExecute();
            if($rUtn!=true){
                // 例外処理へ
                throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $rJnl = $objQueryJnl->sqlExecute();
            if($rJnl!=true){
                // 例外処理へ
                throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            $r = $objDBCA->transactionCommit();
            if (!$r){
                // 例外処理へ
                throw new Exception( '00001500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $boolTransactionFlag = false;

            $r = $objDBCA->transactionExit();
            if (!$r){
                // 例外処理へ
                throw new Exception( '00001600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $boolRetStatus = true;
        }
        catch (Exception $e){
            if( $boolTransactionFlag === true ){
                $tmpBoolValue = $objDBCA->transactionRollBack();
                if( $tmpBoolValue === false ){
                    $intErrorType = 502;
                }
                else{
                    // トランザクション終了
                    $tmpBoolValue = $objDBCA->transactionExit();
                    if( $tmpBoolValue === false ){
                        $intErrorType = 503;
                    }
                }
            }

            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('ResultStatus'=>$boolRetStatus);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }

    // ----パスワードの有効期限が切れているかどうかを判定する
    function checkLoginPasswordExpiryOut($username,$p_login_pw_l_update,$pass_word_expiry,$objDBCA){

        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：あり           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $boolExpiryOut = false;
        $strReasonType = null;

        $strFxName = __FUNCTION__; // checkPasswordExpiryOut

        try{
            $db_model_ch = $objDBCA->getModelChannel();

            $tempBoolPassWordChange = false;
            $tempRequestTime = $_SERVER["REQUEST_TIME"];
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
                // ----パスワードの有効期限の設定がされている
                
                $strReasonType = "-1";
                
                // ----■パスワード最終更新日時をチェックする。
                if($p_login_pw_l_update == ""){
                    // ----日時が不明である。

                    // ----■テーブル【アカウント履歴】から、リクエストに紐付くユーザがこれまでに設定したパスワード種類数を取得する。

                    $tmpStrSql = "SELECT "
                                ."    DISTINCT PASSWORD "
                                ."FROM "
                                ."    A_ACCOUNT_LIST_JNL "
                                ."WHERE "
                                ."    USERNAME = :USERNAME_BV "
                                ."    AND "
                                ."    USERNAME IS NOT NULL";

                    $objQuery = $objDBCA->sqlPrepare($tmpStrSql);
                    if( $objQuery->getStatus()===false ){
                        throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $tmpArrayBind = array('USERNAME_BV'=>$username);
                    if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                        throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $tmpBoolResult = $objQuery->sqlExecute();
                    if($tmpBoolResult!=true){
                        throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $tmpIntCount1=0;
                    while( $row = $objQuery->resultFetch() ){
                        $tmpIntCount1     += 1;
                    }
                    unset($tmpStrSql);
                    unset($objQuery);
                    unset($tmpArrayBind);
                    unset($tmpBoolResult);
                    
                    // テーブル【アカウント履歴】から、リクエストに紐付くユーザがこれまでに設定したパスワード種類数を取得する。■----

                    // ----■リクエストに紐付くユーザがこれまでに設定したパスワード種類数をチェックする。
                    if($tmpIntCount1 == 0){
                        // ----(2未満だった)何等かの理由で、履歴に、（パスワード）が登録されたレコードが存在していない

                        $strReasonType = "0";

                        $tempBoolPassWordChange = true;

                        // (2未満だった)何等かの理由で、履歴に、（パスワード）が登録されたレコードが存在していない----
                    }
                    else if($tmpIntCount1 == 1){
                        // ---- (2未満だった)初めて、登録されたまま、と評価できる場合（履歴に、パスワードが１種しかない）

                        $tempBoolPassWordChange = true;
                        $strReasonType = "0";

                        // (2未満だった)初めて、登録されたまま、と評価できる場合（履歴に、パスワードが１種しかない）----
                    }
                    else{
                        // ----(2以上だった)場合

                        $tmpStrSelectAreaLUDT = makeSelectSQLPartForDateWildColumn($db_model_ch,"LAST_UPDATE_TIMESTAMP","DATEDATE",false);

                        // ----■テーブル【アカウント履歴】から、リクエストに紐付くユーザの、現在のパスワードが、ユーザによって設定された最後の日時をチェックする。
                        $tmpStrSql = "SELECT "
                                    ."    {$tmpStrSelectAreaLUDT} AS ALIAS_LAST_UPDATE_TIME "
                                    ."FROM "
                                    ."    A_ACCOUNT_LIST_JNL "
                                    ."WHERE "
                                    ."    JOURNAL_SEQ_NO = (SELECT "
                                    ."                          MAX(JOURNAL_SEQ_NO) "
                                    ."                      FROM "
                                    ."                          A_ACCOUNT_LIST_JNL "
                                    ."                      WHERE "
                                    ."                          USERNAME = :USERNAME_BV "
                                    ."                          AND "
                                    ."                          PASSWORD = (SELECT "
                                    ."                                          PASSWORD "
                                    ."                                      FROM "
                                    ."                                          A_ACCOUNT_LIST "
                                    ."                                      WHERE "
                                    ."                                          USERNAME = :USERNAME_BV "
                                    ."                                      AND "
                                    ."                                          DISUSE_FLAG IN ( '0', 'H' ) "
                                    ."                                     ) "
                                    ."                          AND "
                                    ."                          PW_LAST_UPDATE_TIME IS NOT NULL "
                                    ."                     )"; 
                        //今のパスワードと同じ行を、履歴から探して、もっとも古い履歴番号の行を取得する----

                        $tmpArrayBind = array('USERNAME_BV'=>$username);

                        $objQuery = $objDBCA->sqlPrepare($tmpStrSql);
                        if( $objQuery->getStatus()===false ){
                            throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                            throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $tmpBoolResult = $objQuery->sqlExecute();
                        if($tmpBoolResult!=true){
                            throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $tmpIntCount2=0;

                        while( $row = $objQuery->resultFetch() ){
                            $tmpIntCount2 += 1;
                            $tmpIntPWLDUnixTime = strtotime($row["ALIAS_LAST_UPDATE_TIME"]);
                        }
                        unset($tmpStrSelectAreaLUDT);
                        unset($objQuery);
                        unset($tmpArrayBind);
                        unset($tmpBoolResult);
                        
                        // テーブル【アカウント履歴】から、リクエストに紐付くユーザの、現在のパスワードが、ユーザによって設定された最後の日時をチェックする。■----

                        if($tmpIntCount2 == 1){
                            //----日時が判明した。
                            //日時が判明した。----
                        }
                        else{
                            //----日時が不明である。

                            $tempBoolPassWordChange = true;

                            $strReasonType = "0";

                            //日時が不明である。
                        }
                        unset($tmpIntCount2);
                        unset($tmpStrSql);

                        // (2以上だった)場合----
                    }
                    unset($tmpIntCount1);
                    
                    // リクエストに紐付くユーザがこれまでに設定したパスワード種類数をチェックする。■----

                    // 日時が不明である。----
                }
                else{
                    // ----日時が判明した。（正常時）
                    
                    $tmpIntPWLDUnixTime = strtotime($p_login_pw_l_update);
                    
                    // 日時が判明した。（正常時）----
                }
                // ■パスワード最終更新日時をチェックする。----
                
                // ----■パスワード変更基準日時を元に有効期限が切れていないかをチェックする。
                if($strReasonType == "0"){
                    // ----一度も更新された形跡が、見つからなかった場合
                    
                    
                    // 一度も更新された形跡が、見つからなかった場合----
                }
                else{
                    // ----パスワードが変更された日時が発見された場合
                    
                    if($tmpIntPWLDUnixTime + ($tempIntLength * 86400) < $tempRequestTime ){
                        $tempBoolPassWordChange = true;
                        $strReasonType = "1";
                    }
                    unset($tmpIntPWLDUnixTime);
                    
                    // パスワードが変更された日時が発見された場合----
                }
                // パスワード変更基準日時を元に有効期限が切れていないかをチェックする。----
                
                if($tempBoolPassWordChange===true){
                    // ----有効期限が切れている。
                    $boolExpiryOut = true;

                    // 有効期限が切れている。----
                }
                else{
                    // ----有効期限が切れていない。
                    // 有効期限が切れていない。----
                }
                
                // パスワードの有効期限の設定がされている----
            }
            else{

            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('ExpiryOut'=>$boolExpiryOut,
                           'ReasonType'=>$strReasonType);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // パスワードの有効期限が切れているかどうかを判定する----

    // ----ユーザによって、入力された新しいパスワードが再利用禁止期間内かどうかを判定する
    function checkRequirementAsNewUserPassword($varUsername,$varPassword,$pw_reuse_forbid,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $boolAllowUpdate = false;

        $strFxName = __FUNCTION__; // checkRequirementAsNewUserPassword

        try{
            $db_model_ch = $objDBCA->getModelChannel();

            $tmpStrSelectPart = makeSelectSQLPartForDateWildColumn($db_model_ch,"MAX(PW_LAST_UPDATE_TIME)","DATEDATE",false);
            $strSelectAliasPWLastUpdate = "(CASE WHEN MAX(PW_LAST_UPDATE_TIME) IS NULL THEN 'NO-RECORD' ELSE {$tmpStrSelectPart} END)";

            $sql = "SELECT "
                  ."    {$strSelectAliasPWLastUpdate} ALIAS_PW_LAST_UPDATE "
                  ."FROM "
                  ."    A_ACCOUNT_LIST_JNL "
                  ."WHERE "
                  ."    USER_ID IN ( SELECT USER_ID
                                     FROM   A_ACCOUNT_LIST
                                     WHERE  DISUSE_FLAG IN ( '0', 'H' ) )"
                  ."    AND "
                  ."    USERNAME = :USERNAME_BV "
                  ."    AND "
                  ."    PASSWORD = :PASSWORD_BV ";

            $aryTmpBind = array('USERNAME_BV'=>$varUsername,'PASSWORD_BV'=>$varPassword);

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($aryTmpBind) != "" ){
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $boolResult = $objQuery->sqlExecute();
            if($boolResult!=true){
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $row2 = $objQuery->resultFetch();
            $varLastUpdate = $row2['ALIAS_PW_LAST_UPDATE'];
            if( $varLastUpdate == 'NO-RECORD' ){
                //----該当ＰＷは、ユーザの手による更新は過去になかった場合
                $boolAllowUpdate = true;
                //該当ＰＷは、ユーザの手による更新は過去になかった場合----
            }
            else{
                $tempRequestTime = $_SERVER["REQUEST_TIME"];
                $tmpIntPWLDUnixTime = strtotime($varLastUpdate);
                if( $tempRequestTime < $tmpIntPWLDUnixTime + ($pw_reuse_forbid * 86400) ){
                    //----再利用禁止期間を経過していない場合
                    $boolAllowUpdate = false;
                    //再利用禁止期間を経過していない場合----
                }
                else{
                    //----再利用禁止期間を経過した場合
                    $boolAllowUpdate = true;
                    //再利用禁止期間を経過した場合----
                }
            }
            unset($objQuery);
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('Requirement'=>$boolAllowUpdate);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // ユーザによって、入力された新しいパスワードが再利用禁止期間内かどうかを判定する----

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ここまでユーザ自身による、パスワード変更に関する機能                                           //
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ここからユーザのログインに関する機能                                                           //
    ////////////////////////////////////////////////////////////////////////////////////////////////////

    function checkLoginRequestForUserAuth($strUsername, $strUserPass, $pwl_expiry, $pwl_threshold, $pwl_countmax, $objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：なし           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：なし           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $boolCorrectRequest = false;
        $intDetailCodeOfCheck = 0;

        $checkStatus = "error";

        $account_list = null;
        $strFixUserId = null;

        $strFxName = __FUNCTION__; // checkLoginRequestForUserAuth

        try{
            $tmpArrayRet = getUserAccountListForAuth($objDBCA);
            if( $tmpArrayRet[1] !== null ){
                $intErrorType = 502;

                // 例外処理へ
                throw new Exception( $tmpArrayRet[3] );
            }
            $account_list  = $tmpArrayRet[0]['PasswordPerUsername'];
            $fixId_list    = $tmpArrayRet[0]['UsernamePerUserID'];
            unset($tmpArrayRet);

            $aryValiUserId = saLoginTextValidateCheck($strUsername,'/^[a-zA-Z0-9-!"#$%&\'()*+,.\/;<=>?@[\]^\\_`{|}~]+$/',4,30,false);
            if( $aryValiUserId[0] === true ){
                if( array_key_exists($strUsername,$account_list) === true ){
                    // ----レコードが存在している
                    $aryValiUserPw = saLoginTextValidateCheck($strUserPass,'/^[a-zA-Z0-9-!"#$%&\'()*+,.\/:;<=>?@[\]^\\_`{|}~]+$/',8,30,false);
                    if( $aryValiUserPw[0] === true ){
                        //----様式に合致したパスワード入力があった
                        $strPWEncoded = md5($strUserPass);
                        if( strlen($strPWEncoded) === strlen($account_list[$strUsername]) && $strPWEncoded === $account_list[$strUsername] ){
                            //----パスワード合致
                            $checkStatus = "pw_match";
                            //パスワード合致----
                        }
                        else{
                            //----パスワード合致せず
                            $checkStatus = "pw_error";
                            //パスワード合致せず----
                        }
                        //様式に合致したパスワード入力があった----
                    }
                    else{
                        //----様式に合致していないパスワード入力があった
                        $checkStatus = "pw_error_on_syntax";
                        //様式に合致していないパスワード入力があった----
                    }
                    $strFixUserId = array_search($strUsername, $fixId_list, true);
                    $tmpArrayRet = executeLoginLockByUserID($strFixUserId,$checkStatus,$pwl_expiry,$pwl_threshold,$pwl_countmax,$objDBCA);
                    if( $tmpArrayRet[1] !== null ){
                        $intErrorType = 503;

                        // 例外処理へ
                        throw new Exception( $tmpArrayRet[3] );
                    }
                    $checkStatus   = $tmpArrayRet[0]['ResultStatus'];
                    unset($tmpArrayRet);
                    // レコードが存在している----
                }
                else{
                    // ----レコード存在なし
                    $checkStatus = "id_error";
                    // レコード存在なし----
                }
            }
            else{
                $checkStatus = "id_error_on_syntax";
            }
            
            switch($checkStatus){
                case "login_success":
                    $boolCorrectRequest = true;
                    break;
                case "id_error":
                    $intDetailCodeOfCheck = 1;
                    break;
                case "pw_error":
                    $intDetailCodeOfCheck = 2;
                    break;
                case "locked_pw_error":
                    $intDetailCodeOfCheck = 3;
                    break;
                case "locked_pw_match":
                    $intDetailCodeOfCheck = 4;
                    break;
                case "id_error_on_syntax":
                    $intDetailCodeOfCheck = 5;
                    break;
                case "pw_error_on_syntax":
                    $intDetailCodeOfCheck = 6;
                    break;
                default:
                    $intErrorType = 504;

                    $checkStatus = "unexpected(".$checkStatus.")";

                    // 例外処理へ
                    throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('CorrectRequest'=>$boolCorrectRequest,
                           'DetailOfCheck'=>$intDetailCodeOfCheck,
                           'CheckResultType'=>$checkStatus,
                           'PasswordPerUsername'=>$account_list,
                           'UserID'=>$strFixUserId);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }

    function executeLoginLockByUserID($strFixUserId,$checkStatus,$pwl_expiry,$pwl_threshold,$pwl_countmax,$objDBCA){

        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：なし           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：あり           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $retStatus = null;

        $boolTransactionFlag = false;

        $strFxName = __FUNCTION__; // executeLoginLockByUserID

        try{
            $db_model_ch = $objDBCA->getModelChannel();

            if( 1 <= $pwl_threshold && $pwl_expiry != 0 ){
                // ----閾値が1以上の正の整数で設定されている、かつ、ロック期間が0ではない
                if( $objDBCA->transactionStart() !== true ){
                    // 例外処理へ
                    throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                $boolTransactionFlag = true;

                $arrayConfig = array(
                    "JOURNAL_SEQ_NO"=>"",
                    "JOURNAL_ACTION_CLASS"=>"",
                    "JOURNAL_REG_DATETIME"=>"",
                    "LOCK_ID"=>"",
                    "USER_ID"=>"",
                    "LOCKED_TIMESTAMP"=>"DATETIME",
                    "MISS_INPUT_COUNTER"=>"",
                    "NOTE"=>"",
                    "DISUSE_FLAG"=>"",
                    "LAST_UPDATE_TIMESTAMP"=>"",
                    "LAST_UPDATE_USER"=>""
                );

                $arrayValueTmpl = array(
                    "JOURNAL_SEQ_NO"=>"",
                    "JOURNAL_ACTION_CLASS"=>"",
                    "JOURNAL_REG_DATETIME"=>"",
                    "LOCK_ID"=>"",
                    "USER_ID"=>"",
                    "LOCKED_TIMESTAMP"=>"",
                    "MISS_INPUT_COUNTER"=>"",
                    "NOTE"=>"",
                    "DISUSE_FLAG"=>"",
                    "LAST_UPDATE_TIMESTAMP"=>"",
                    "LAST_UPDATE_USER"=>""
                );

                $arrayValue = $arrayValueTmpl;

                $temp_array = array('WHERE'=>"USER_ID = :USER_ID AND DISUSE_FLAG = '0' ");

                $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                                                "SELECT FOR UPDATE",
                                                "USER_ID",
                                                "A_ACCOUNT_LOCK",
                                                "A_ACCOUNT_LOCK_JNL",
                                                $arrayConfig,
                                                $arrayValue,
                                                $temp_array
                );

                $aryResult01 = array();
                if( $retArray[0] === false ){
                    // 例外処理へ
                    throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];

                $arrayUtnBind['USER_ID'] = $strFixUserId;

                $objQuery = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQuery->getStatus()===false ){
                    // 例外処理へ
                    throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                if( $objQuery->sqlBind($arrayUtnBind) != "" ){
                    // 例外処理へ
                    throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                $r = $objQuery->sqlExecute();
                if(!$r){
                    // 例外処理へ
                    throw new Exception( '00000500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }

                //----発見行だけループ
                while ( $row = $objQuery->resultFetch() ){
                    $aryResult01[] = $row;
                }
                //発見行だけループ----
                $intEffectCount = $objQuery->effectedRowCount();
                unset($objQuery);
                unset($row);

                // ----更新フラグをNullへ
                $boolTableUpdate = false;
                // 更新フラグをNullへ----

                $sqlQueryType = ""; 
                $nowMissedCount = null;
                $strLockDateUpdate = "";

                if( $intEffectCount == 0 ){
                    // ----ALテーブルにレコードが存在しなかった
                    $sqlQueryType   = "INSERT";
                    $intMissedCount = 0;
                    $varLockedTimeStamp = null;
                    
                    // ----有効期限内かどうかを判定
                    $boolInOfLockExpiry = saLoginLockCheckInExpiry($pwl_expiry, $varLockedTimeStamp);
                    // 有効期限内かどうかを判定----
                    
                    $tmpAryBody = saLoginLockCheckNowStatus($checkStatus,$pwl_countmax,$pwl_threshold,$intMissedCount,$boolInOfLockExpiry);
                    $retStatus = $tmpAryBody[0];
                    $nowMissedCount = $tmpAryBody[1];
                    $strLockDateUpdate = $tmpAryBody[2];

                    if( $nowMissedCount != $intMissedCount || 0 < strlen($strLockDateUpdate) ){
                        //----レコードを追加
                        $boolTableUpdate = true;
                        $retArray = getSequenceValueFromTable('SEQ_A_ACCOUNT_LOCK', 'A_SEQUENCE', FALSE );
                        if( $retArray[1] != 0 ){
                            // 例外処理へ
                            throw new Exception( '00000600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        $arrayValue['LOCK_ID']          = $retArray[0];
                        $arrayValue['USER_ID']          = $strFixUserId;
                        $arrayValue['DISUSE_FLAG']      = '0';

                        $arrayConfig2 = array(
                            "JOURNAL_SEQ_NO"=>"",
                            "JOURNAL_ACTION_CLASS"=>"",
                            "JOURNAL_REG_DATETIME"=>"",
                            "LOCK_ID"=>"",
                            "USER_ID"=>"",
                            "LOCKED_TIMESTAMP"=>"DATETIME",
                            "MISS_INPUT_COUNTER"=>"",
                            "NOTE"=>"",
                            "DISUSE_FLAG"=>"",
                            "LAST_UPDATE_TIMESTAMP"=>"",
                            "LAST_UPDATE_USER"=>""
                        );

                        $arrayValueTmpl2 = array(
                            "JOURNAL_SEQ_NO"=>"",
                            "JOURNAL_ACTION_CLASS"=>"",
                            "JOURNAL_REG_DATETIME"=>"",
                            "LOCK_ID"=>"",
                            "USER_ID"=>"",
                            "LOCKED_TIMESTAMP"=>"",
                            "MISS_INPUT_COUNTER"=>"",
                            "NOTE"=>"",
                            "DISUSE_FLAG"=>"",
                            "LAST_UPDATE_TIMESTAMP"=>"",
                            "LAST_UPDATE_USER"=>""
                        );

                        $arrayValue2 = $arrayValueTmpl2;

                        $temp_array2 = array('WHERE'=>"USER_ID = :USER_ID AND DISUSE_FLAG = '0' ");
                        
                        $retArray2 = makeSQLForUtnTableUpdate($db_model_ch,
                                                        "SELECT FOR UPDATE",
                                                        "USER_ID",
                                                        "A_ACCOUNT_LOCK",
                                                        "A_ACCOUNT_LOCK_JNL",
                                                        $arrayConfig2,
                                                        $arrayValue2,
                                                        $temp_array2
                        );

                        $aryResult02 = array();
                        if( $retArray2[0] === false ){
                            // 例外処理へ
                            throw new Exception( '00000700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }

                        $sqlUtnBody2 = $retArray2[1];
                        $arrayUtnBind2 = $retArray2[2];

                        $arrayUtnBind2['USER_ID'] = $strFixUserId;

                        $objQuery = $objDBCA->sqlPrepare($sqlUtnBody2);
                        if( $objQuery->getStatus()===false ){
                            // 例外処理へ
                            throw new Exception( '00000800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }

                        if( $objQuery->sqlBind($arrayUtnBind2) != "" ){
                            // 例外処理へ
                            throw new Exception( '00000900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }

                        $r = $objQuery->sqlExecute();
                        if(!$r){
                            // 例外処理へ
                            throw new Exception( '00001000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }

                        //----発見行だけループ
                        while ( $row = $objQuery->resultFetch() ){
                            $aryResult02[] = $row;
                        }
                        //発見行だけループ----
                        $intEffectCount = $objQuery->effectedRowCount();
                        unset($objQuery);
                        unset($row);
                        
                        if( $intEffectCount != 0 ){
                            //----追越挿入

                            // 例外処理へ
                            throw new Exception( '00001100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );

                            //追越挿入----
                        }
                        
                        //レコードを追加----
                    }
                    // ALテーブルにレコードが存在しなかった----
                }
                else if( $intEffectCount == 1 ){
                    // ----レコードが1行存在した場合
                    $sqlQueryType         = "UPDATE"; 

                    $arrayValue           = $aryResult01[0];
                    $intMissedCount       = $arrayValue['MISS_INPUT_COUNTER'];
                    $varLockedTimeStamp   = $arrayValue['LOCKED_TIMESTAMP'];

                    // ----有効期限内かどうかを判定
                    $boolInOfLockExpiry = saLoginLockCheckInExpiry($pwl_expiry, $varLockedTimeStamp);
                    // 有効期限内かどうかを判定----

                    $tmpAryBody = saLoginLockCheckNowStatus($checkStatus,$pwl_countmax,$pwl_threshold,$intMissedCount,$boolInOfLockExpiry);
                    $retStatus = $tmpAryBody[0];
                    $nowMissedCount = $tmpAryBody[1];
                    $strLockDateUpdate = $tmpAryBody[2];

                    if( $nowMissedCount != $intMissedCount || 0 < strlen($strLockDateUpdate) ){
                        $boolTableUpdate   = true;
                    }
                    // レコードが1行存在した場合----
                }
                else{
                    // ----レコードが複数行存在した場合

                    // 例外処理へ
                    throw new Exception( '00001200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );

                    // レコードが複数行存在した場合----
                }
                unset($objQueryUtn);

                if( $boolTableUpdate === false ){
                    // ----ロックテーブルの更新の必要なし
                    if( $intEffectCount == 1 ){
                        // ----ロック解除のためのコミット
                        $r = $objDBCA->transactionCommit();
                        if (!$r){
                            // 例外処理へ
                            throw new Exception( '00001300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                        }
                        // ロック解除のためのコミット----
                    }
                    // ロックテーブルの更新の必要なし----
                }
                else{
                    // ----ロックテーブルの更新の必要あり
                    $retArray = getSequenceValueFromTable('JSEQ_A_ACCOUNT_LOCK', 'A_SEQUENCE', FALSE );
                    if( $retArray[1] != 0 ){
                        // 例外処理へ
                        throw new Exception( '00001400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $arrayValue['JOURNAL_SEQ_NO']     = $retArray[0];
                    $arrayValue['MISS_INPUT_COUNTER'] = $nowMissedCount;

                    $arrayValue['LAST_UPDATE_USER'] = $strFixUserId;

                    // ----閾値と現在の失敗値が等しい場合
                    if( $strLockDateUpdate === "LOCK" ){
                        $arrayValue['LOCKED_TIMESTAMP'] = date("Y/m/d H:i:s",$_SERVER['REQUEST_TIME']);
                    }
                    else if( $strLockDateUpdate === "RESET" ){
                        $arrayValue['LOCKED_TIMESTAMP'] = "";
                    }
                    // 閾値と現在の失敗値が等しい場合----

                    $retArray = makeSQLForUtnTableUpdate($db_model_ch,
                        $sqlQueryType,
                        "LOCK_ID",
                        "A_ACCOUNT_LOCK",
                        "A_ACCOUNT_LOCK_JNL",
                        $arrayConfig,
                        $arrayValue );

                    $sqlUtnBody = $retArray[1];
                    $arrayUtnBind = $retArray[2];

                    $sqlJnlBody = $retArray[3];
                    $arrayJnlBind = $retArray[4];

                    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                    $objQueryJnl = $objDBCA->sqlPrepare($sqlJnlBody);

                    if( $objQueryUtn->getStatus()===false || $objQueryJnl->getStatus()===false ){
                        // 例外処理へ
                        throw new Exception( '00001500-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "" ){
                        // 例外処理へ
                        throw new Exception( '00001600-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    //----SQL実行
                    $rUtn = $objQueryUtn->sqlExecute();
                    if($rUtn!=true){
                        // 例外処理へ
                        throw new Exception( '00001700-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $rJnl = $objQueryJnl->sqlExecute();
                    if($rJnl!=true){
                        // 例外処理へ
                        throw new Exception( '00001800-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }

                    $r = $objDBCA->transactionCommit();
                    if (!$r){
                        // 例外処理へ
                        throw new Exception( '00001900-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    }
                    $boolTransactionFlag = false;

                    unset($objQueryUtn);
                    unset($objQueryJnl);
                    
                    // ロックテーブルの更新の必要あり----
                }
                $r = $objDBCA->transactionExit();
                if (!$r){
                    // 例外処理へ
                    throw new Exception( '00002000-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                // 閾値が1以上の正の整数で設定されている、かつ、ロック期間が0ではない----
            }
            else{
                // ----閾値が1以上の正の整数で設定されていない、かつ、ロック期間が0ではない
                if($checkStatus == "pw_match"){
                    // ----パスワードが合致していた
                    $retStatus = "login_success";
                    // パスワードが合致していた----
                }
                else if( $checkStatus == "pw_error" || $checkStatus == "pw_error_on_syntax" ){
                    // ----パスワードが合致していない
                    $retStatus = $checkStatus;
                    // パスワードが合致していない----
                }
                // 閾値が1以上の正の整数で設定されていない、かつ、ロック期間が0ではない----
            }
        }
        catch (Exception $e){
            if( $boolTransactionFlag === true ){
                $tmpBoolValue = $objDBCA->transactionRollBack();
                if( $tmpBoolValue === false ){
                    $intErrorType = 502;
                }
                else{
                    // トランザクション終了
                    $tmpBoolValue = $objDBCA->transactionExit();
                    if( $tmpBoolValue === false ){
                        $intErrorType = 503;
                    }
                }
            }

            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('ResultStatus'=>$retStatus);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }

    function checkLoginRequestForUserLdapAuth( $strUsername, $strUserPass, $objDBCA ){
        global $root_dir_path;
        global $aryExternalAuthSettings;

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $boolCorrectRequest = false;
        $intDetailCodeOfCheck = 0;

        $checkStatus = "error";

        $account_list = null;
        $strFixUserId = null;

        $strFxName = __FUNCTION__; // checkLoginRequestForUserLdapAuth

        try{
            // checkLoginRequestForUserAuth()との差は「:」
            //$aryValiUserId = saLoginTextValidateCheck($strUsername,'/^[a-zA-Z0-9-!"#$%&\'()*+,.\/:;<=>?@[\]^\\_`{|}~]+$/',4,30,false);
            $aryValiUserId = saLoginTextValidateCheck($strUsername,'/^[a-zA-Z0-9-!"#$%&\'()*+,.\/;<=>?@[\]^\\_`{|}~]+$/',4,30,false);
            if( $aryValiUserId[0] === true ){
                $aryValiUserPw = saLoginTextValidateCheck($strUserPass,'/\S+/',1,30,false);
                if( $aryValiUserPw[0] === true ){
                    //----様式に合致したパスワード入力があった
                    require_once($root_dir_path . "/libs/commonlibs/common_external_auth.php");
                    $ret = externalAuthForWeb($aryExternalAuthSettings, $strUsername, $strUserPass);
                    if($ret === false) {
                        $checkStatus = "id_error"; // id or password error だがid_errorで投げておく
                    } else {
                        $checkStatus = "login_success";
                    }
                    //様式に合致したパスワード入力があった----
                }
                else{
                    //----様式に合致していないパスワード入力があった
                    $checkStatus = "pw_error_on_syntax";
                    //様式に合致していないパスワード入力があった----
                }
            }
            else{
                // -----様式に合致していないユーザー名の入力があった
                $checkStatus = "id_error_on_syntax";
                // 様式に合致していないユーザー名の入力があった-----
            }

            switch($checkStatus){
                case "login_success":
                    $boolCorrectRequest = true;
                    break;
                case "id_error":
                   $intDetailCodeOfCheck = 1;
                   break;
                case "id_error_on_syntax":
                    $intDetailCodeOfCheck = 5;
                    break;
                case "pw_error_on_syntax":
                    $intDetailCodeOfCheck = 6;
                    break;
                default:
                    $intErrorType = 504;

                    $checkStatus = "unexpected(".$checkStatus.")";

                    // 例外処理へ
                    throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                    break;
            }

            if($boolCorrectRequest === true) {
                $dummy_password = md5($strUserPass);
                $account_list = array();
                $account_list[$strUsername] = $dummy_password;
            }
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('CorrectRequest'=>$boolCorrectRequest,
                           'DetailOfCheck'=>$intDetailCodeOfCheck,
                           'CheckResultType'=>$checkStatus,
                           'PasswordPerUsername'=>$account_list,
                         );

        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }

    function checkLoginRequestForUserAuthInorExt( $strUsername, $objDBCA ){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：なし           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：なし           //
        //////////////////////////////////////////////////

        global $aryExternalAuthSettings;

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $boolLocalAuthUser = false;   // ユーザー認証方式種別 true = 内部認証,false = 外部認証(AD連携)

        $PasswordPerUsername_list = null;
        $UsernamePerUserID_list = null;
        $aryLocalAuthUserIdList = array();

        $strFxName = __FUNCTION__; // checkLoginRequestForUserAuthInorExt

        try{
            // -----ローカルのユーザーリストを取得
            $tmpArrayRet = getUserAccountListForAuth($objDBCA);
            if( $tmpArrayRet[1] !== null ){
                $intErrorType = 502;
                throw new Exception( $tmpArrayRet[3] );
            }
            $PasswordPerUsername_list  = $tmpArrayRet[0]['PasswordPerUsername'];
            $UsernamePerUserID_list    = $tmpArrayRet[0]['UsernamePerUserID'];
            unset($tmpArrayRet);
            // ローカルのユーザーリストを取得-----ここまで

            // -----認証対象ユーザーのローカル（内部）/外部認証の判定
            /*  説明：
                ローカル（内部）認証ユーザーとは、外部認証設定ファイル内に記述したUSER_IDに紐づく、AD連携等の外部認証を実施しないユーザーを指す(ITA_DBのA_ACCOUNT_LISTテーブルに固定登録された”-”マイナス数や特定の正数ナンバーを持つユーザー)それ以外は外部認証ユーザーとなる。
            */
            // 入力されたユーザー名がローカル認証ユーザーリスト内に存在するかチェックする
            $strLocalAuthUserIdList = $aryExternalAuthSettings['LocalAuthUserId']['IdList'];
            // アカウントリスト（ローカルDB）に認証対象のユーザーが登録されているかチェック
            if ( array_key_exists( $strUsername, $PasswordPerUsername_list ) === true ){
                // アカウントリストに登録されていた認証対象ユーザーのID値をユーザーIDリストから取得
                $userId = array_search($strUsername, $UsernamePerUserID_list, true);

                // 外部認証設定ファイルに記述された 内部認証を強制するユーザーIDリストを配列化
                $aryLocalAuthUserIdList = explode("," , $strLocalAuthUserIdList);

                if(isSpecialUser($userId, $aryLocalAuthUserIdList) === true) {
                    $boolLocalAuthUser = true;
                } else {
                    $boolLocalAuthUser = false;
                }
            }
            else{
                // アカウントリスト（ローカルDB）に未登録のユーザーはレプリケーションによるユーザー情報連携前・AD登録のみのユーザーである可能性があり、外部認証ユーザとして（AD連携・認証へ)進む
                $boolLocalAuthUser = false;
            }
            // 認証対象ユーザーのローカル（内部）/外部認証の判定-----ここまで

        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('AuthUserType'=>$boolLocalAuthUser);

        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }

    function saLoginLockCheckInExpiry($pwl_expiry, $varLockedTimeStamp){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：なし           //
        // ユーザ定義一般関数の呼び出し：あり           //
        // 環境変数の直接参照          ：あり           //
        // DBの直接参照                ：なし           //
        //////////////////////////////////////////////////

        //----カウンタ値の有効期限を算出
        $retBool = false;
        if( $pwl_expiry < 0 ){
            // ----マイナスの場合は常にカウンタ値はリセットしないために、有効期限内とする
            $retBool = true;
            // マイナス。常にカウンタ値はリセットしないために、有効期限内とする----
        }
        else{
            // ----プラスなので、有効期限の判定
            if( strlen($varLockedTimeStamp) == 0 ){
                // ----最後に失敗した時刻がない場合は、カウントを継続するため、有効期限内とする
                $retBool = true;
                // 最後に失敗した時刻がない場合は、カウントを継続するため、有効期限内とする----
            }
            else{
                $varUnlockTimeStamp = $pwl_expiry + convFromStrDateToUnixtime($varLockedTimeStamp,false);
                $varNowTimeStamp = $_SERVER['REQUEST_TIME'];
                if( $varUnlockTimeStamp <= $varNowTimeStamp ){
                    // ----ロックされてから期間が経過したので、カウントをリセットする。有効期限外とする
                    $retBool = false;
                    // ロックされてから期間が経過したので、カウントをリセットする。有効期限外とする----
                }
                else{
                    // ----ロックされてから期間が経過していないので、カウントを継続するため、有効期限内とする
                    $retBool = true;
                    // ロックされてから期間が経過していないので、カウントを継続するため、有効期限内とする----
                }
            }
            // プラスなので、有効期限の判定----
        }
        return $retBool;
    }

    // ----アカウントリスト取得
    function getUserAccountListForAuth($objDBCA){

        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $num_rows = 0;

        $aryLoginIDAndPWList    = array();
        $aryFixIDAndLoginIDList = array();

        $strFxName = __FUNCTION__; // checkPasswordExpiryOut

        try{
            $sql = "SELECT "
                  ."    USER_ID, "
                  ."    USERNAME, "
                  ."    PASSWORD "
                  ."FROM "
                  ."    A_ACCOUNT_LIST "
                  ."WHERE "
                  ."    DISUSE_FLAG = '0' ";

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $boolResult = $objQuery->sqlExecute();
            if($boolResult!=true){
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            while( $row = $objQuery->resultFetch() ){
                $aryLoginIDAndPWList[$row['USERNAME']]   = $row['PASSWORD'];
                $aryFixIDAndLoginIDList[$row['USER_ID']] = $row['USERNAME'];

                $num_rows += 1;
            }
            unset($objQuery);
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'PasswordPerUsername'=>$aryLoginIDAndPWList,
                           'UsernamePerUserID'=>$aryFixIDAndLoginIDList);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    // アカウントリスト取得----

    function saLoginLockCheckNowStatus($strInputStatus, $pwl_countmax, $pwl_threshold, $intMissedCount, $boolInOfLockExpiry){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：なし           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：なし           //
        //////////////////////////////////////////////////

        $retArrayBody = array();
        $retStrStatus = "";
        $retIntValue = null;
        $retStrLock = "";
        if( $strInputStatus == "pw_match" ){
            // ----ID/PWがマッチしていた
            if( $boolInOfLockExpiry === true && $pwl_threshold <= $intMissedCount ){
                // ----有効期限内かつ閾値以上の失敗
                $retIntValue = $intMissedCount;
                $retStrStatus = "locked_pw_match";
                // 有効期限内かつ閾値以上の失敗----
            }
            else{
                // ----有効期限外または閾値未満の失敗
                $retIntValue = 0;
                $retStrStatus = "login_success";
                if( $intMissedCount != 0 ){
                    $retStrLock = "RESET";
                }
                // 有効期限外または閾値未満の失敗-----
            }
            // ID/PWがマッチしていた----
        }
        else if( $strInputStatus == "pw_error" || $strInputStatus == "pw_error_on_syntax" ){
            $retStrStatus = $strInputStatus;
            $retIntValue = $intMissedCount;
            // ----ID/PWがマッチしていない
            if( $boolInOfLockExpiry === false ){
                // ----有効期限外の場合に、回数と時刻をリセット
                $retIntValue = 0;
                $retStrLock = "RESET";
                // 有効期限外の場合に、回数と時刻をリセット----
            }
            
            if( $retIntValue + 1 <= $pwl_countmax ){
                $retIntValue += 1;
            }
            
            if( 1 <= $pwl_threshold ){
                if( $pwl_threshold <= $retIntValue ){
                    // ----閾値以上の失敗
                    $retStrStatus = "locked_pw_error";
                    if( $pwl_threshold == $retIntValue && $intMissedCount != $retIntValue ){
                        $retStrLock = "LOCK";
                    }
                    // 閾値以上の失敗----
                }
            }
            // ID/PWがマッチしていない----
        }
        $retArrayBody[0] = $retStrStatus;
        $retArrayBody[1] = $retIntValue;
        $retArrayBody[2] = $retStrLock;
        return $retArrayBody;
    }

    function saLoginTextValidateCheck($strCheckTarget, $strRegexpFormat, $intMinLength, $intMaxLength, $boolCountAsChr=false, $strWhiteCtrls='\r\n\t', $intBasicMaxLength=4000){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：なし           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：なし           //
        //////////////////////////////////////////////////

        $retArray = array();
        $retBool = false;
        $retErrCode = 0;
        if( preg_match('/\A['.$strWhiteCtrls.'[:^cntrl:]]{0,'.$intBasicMaxLength.'}\z/u', $strCheckTarget) == 0 ){
            $retBool = false;
            $retErrCode = 1;
        }
        else{
            if(preg_match($strRegexpFormat, $strCheckTarget) === 1){
                if( $boolCountAsChr === true ){
                    //----mbstring.internal_encoding(関数(設定/取得)mb_internal_encoding())に、左右されるので注意
                    if( ($intMaxLength === null || mb_strlen($strCheckTarget, "UTF-8") <= $intMaxLength)
                        && ($intMinLength === null || mb_strlen($strCheckTarget, "UTF-8") >= $intMinLength) ){
                        $retBool = true;
                    }
                    else{
                        $retErrCode = 3;
                    }
                }else{
                    //utfは文字によってバイト数が違うので計算するのにstrlen(bin2hex($value))/2を使う。
                    //----改造前診断時コメント[2014-10-02-1258]＜bin2hex(文字列を16進数表記へ置き換える(かならず2の倍数の文字列になる))＞
                    if( ($intMaxLength === null || strlen(bin2hex($strCheckTarget))/2 <= $intMaxLength)
                        && ($intMinLength === null || strlen(bin2hex($strCheckTarget))/2 >= $intMinLength) ){
                        $retBool = true;
                    }
                    else{
                        $retErrCode = 4;
                    }
                }
            }
            else{
                $retErrCode = 2;
            }
        }
        $retArray[0] = $retBool;
        $retArray[1] = $retErrCode;
        return $retArray;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // ここまでユーザのログインに関する機能                                                           //
    ////////////////////////////////////////////////////////////////////////////////////////////////////
