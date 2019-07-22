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

    //----特定のIPが、ホワイトリストに記載されているかをチェックする
    function checkIPByPermittedWhiteList($tmpStrSourceIp,$objDBCA){
        //////////////////////////////////////////////////
        // ユーザ定義メソッドの呼び出し：あり           //
        // ユーザ定義一般関数の呼び出し：なし           //
        // 環境変数の直接参照          ：なし           //
        // DBの直接参照                ：あり           //
        //////////////////////////////////////////////////

        // ----■リクエスト元のIPアドレスが、テーブル【IPアドレスホワイトリスト】に登録されているかをチェックする。
        $aryValues = array();
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";
        
        $num_rows = null;
        
        $strFxName = __FUNCTION__; // checkIPByPermittedWhiteList
        
        try{
            if( $tmpStrSourceIp == "" ){
                // 例外処理へ
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            $sql = "SELECT COUNT(*) AS REC_CNT
                    FROM   A_PERMISSIONS_LIST
                    WHERE  IP_ADDRESS = :IP_ADDRESS_BV
                    AND    DISUSE_FLAG = '0' ";

            $tmpArrayBind = array('IP_ADDRESS_BV'=>$tmpStrSourceIp);

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                // 例外処理へ
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            if( $objQuery->sqlBind($tmpArrayBind) != "" ){
                // 例外処理へ
                throw new Exception( '00000300-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000400-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $row = $objQuery->resultFetch();
            
            $num_rows = $row['REC_CNT'];
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    //特定のIPが、ホワイトリストに記載されているかをチェックする----

    //----システム設定テーブルから、設定を取得する
    function getSystemConfigFromConfigList($objDBCA){
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
        $arySYSCON = array();
        
        $strFxName = __FUNCTION__; // getSystemConfigFromConfigList
        
        try{
            $sql = "SELECT "
                  ."    CONFIG_ID, "
                  ."    VALUE "
                  ."FROM "
                  ."    A_SYSTEM_CONFIG_LIST "
                  ."WHERE "
                  ."    DISUSE_FLAG = '0' ";

            //----statement-zone

            $objQuery = $objDBCA->sqlPrepare($sql);
            if( $objQuery->getStatus()===false ){
                throw new Exception( '00000100-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            $tmpBoolResult = $objQuery->sqlExecute();
            if($tmpBoolResult!=true){
                throw new Exception( '00000200-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }
            
            while( $row = $objQuery->resultFetch() ){
                $arySYSCON[$row['CONFIG_ID']] = $row['VALUE'];
                $num_rows += 1;
            }
            unset($objQuery);
            unset($tmpBoolResult);
        }
        catch (Exception $e){
            if( $intErrorType === null ) $intErrorType = 501;
            $tmpErrMsgBody = $e->getMessage();
            $aryErrMsgBody[] = $tmpErrMsgBody;
            $strErrMsg = $tmpErrMsgBody;
        }
        $aryValues = array('rowLength'=>$num_rows,
                           'Items'=>$arySYSCON);
        return array($aryValues,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
    }
    //システム設定テーブルから、設定を取得する----
?>