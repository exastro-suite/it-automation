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
    //  【特記事項】
    //    ・MessageTemplateStorage::getSomeMessageを用いて、メッセージを埋め込まないこと。
    //
    //  【処理概要】
    //    ・管理コンソールのメニュー関連の情報を取得する
    //
    //////////////////////////////////////////////////////////////////////

    function getInfoOfRepresentativeFiles($objDBCA){
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

        $aryRecord = array();

        $num_rows = 0;

        $strFxName = __FUNCTION__; // getInfoOfRepresentativeFiles

        try{

            $sql = "SELECT MENU_GROUP_ID, "
                  ."       MENU_GROUP_NAME, "
                  ."       DISP_SEQ "
                  ."FROM   A_MENU_GROUP_LIST "
                  ."WHERE  DISUSE_FLAG = '0' ";
            
            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            while( $row = $objQuery->resultFetch() ){
                $num_rows += 1;
                $aryRecord[] = $row;
            }

            unset($objQuery);
            unset($tmpArrayBind);
            unset($tmpBoolResult);
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'InfoOfRepresentativeFilenames'=>$aryRecord
                            );
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    function getMenuGroupNameByMenuGroupID($strGroupIdNumeric,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        // テーブル【メニューグループリスト】から、リクエストされたPHPが所属するメニューの、メニューグループ名を取得する。
        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $num_rows = 0;

        $ACRCM_group_name = null;

        $strFxName = __FUNCTION__; // getMenuGroupNameByMenuGroupID

        try{
            $sql = "SELECT MENU_GROUP_NAME "
                  ."FROM   A_MENU_GROUP_LIST "
                  ."WHERE  MENU_GROUP_ID = :MENU_GROUP_ID_BV "
                  ."AND    DISUSE_FLAG = '0' ";

            $tmpArrayBind = array('MENU_GROUP_ID_BV'=>$strGroupIdNumeric);

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                // 例外処理へ
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            while( $row = $objQuery->resultFetch() ){
                $ACRCM_group_name = $row['MENU_GROUP_NAME'];
                $num_rows += 1;
            }
            if( $num_rows !=1 ){
                $intErrorType = 502;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            unset($objQuery);
            unset($tmpArrayBind);
            unset($tmpBoolResult);
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'MenuGroupName'=>$ACRCM_group_name);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }

    //----2018/06/11
    function getMenuInfo($strMenuId, $objDBCA){
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
        $aryRecord = array();
        $num_rows = 0;
        $strFxName = __FUNCTION__;

        try{
            $sql = "SELECT MENU_ID, "
                  ."       MENU_GROUP_ID, "
                  ."       MENU_NAME, "
                  ."       LOGIN_NECESSITY, "
                  ."       SERVICE_STATUS, "
                  ."       AUTOFILTER_FLG, "
                  ."       INITIAL_FILTER_FLG, "
                  ."       WEB_PRINT_LIMIT, "
                  ."       WEB_PRINT_CONFIRM, "
                  ."       XLS_PRINT_LIMIT, "
                  ."       DISP_SEQ "
                  ."FROM   A_MENU_LIST "
                  ."WHERE  DISUSE_FLAG = '0' "
                  ."AND    MENU_ID = :MENU_ID ";

            $tmpArrayBind = array('MENU_ID'=>$strMenuId);

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
                $aryRecord[] = $row;
            }

            if( $num_rows !=1 ){
                $intErrorType = 502;
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            unset($objQuery);
            unset($tmpArrayBind);
            unset($tmpBoolResult);
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'data'=>$aryRecord
                            );
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
?>