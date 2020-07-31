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

    //----------------------------------------------
    // インタフェース情報からAPIを実行するためのインタフェース情報を取得
    //----------------------------------------------
    function getInterfaceInfo(){
        $retArray = array();
        $retArray[0] = false; //boolean
        $retArray[1] = array(); //sql
        $retArray[2] = ""; //errorメッセージ

        try{

            $sql = "SELECT *
                    FROM   B_TERRAFORM_IF_INFO
                    WHERE  DISUSE_FLAG = '0' ";
            $tmpAryBind = array();
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind,  __FUNCTION__);

            if( $retArray[0] === true ){
                $intTmpRowCount=0;
                $showTgtRow = array();
                $objQuery =& $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    if($intTmpRowCount==1){
                        $showTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                if( $selectRowLength != 1 ){
                    throw new Exception( '00000100-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objQuery);
            }
            else{
                throw new Exception( '00000200-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[1] = $showTgtRow;

        }catch(Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $tmpErrMsgBody = $e->getMessage();
            $retArray[2] = $tmpErrMsgBody;

        }

        return($retArray);

    }




    //----------------------------------------------
    // Organizationsテーブルからデータを取得
    //----------------------------------------------
    function getOrganizationData($organizationID){
        $retArray = array();
        $retArray[0] = false; //boolean
        $retArray[1] = array(); //sql
        $retArray[2] = ""; //errorメッセージ

        try{
            $sql = "SELECT *
                    FROM   B_TERRAFORM_ORGANIZATIONS
                    WHERE  DISUSE_FLAG = '0' AND ORGANIZATION_ID = :ORGANIZATION_ID";
            $tmpAryBind = array('ORGANIZATION_ID' => $organizationID);
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, __FUNCTION__);
            if( $retArray[0] === true ){
                $intTmpRowCount=0;
                $showTgtRow = array();
                $objQuery =& $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    if($intTmpRowCount==1){
                        $showTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                if( $selectRowLength != 1 ){
                    throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objQuery);
            }
            else{
                throw new Exception( '00000400-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[1] = $showTgtRow;

        }catch(Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[0] = false;
            $tmpErrMsgBody = $e->getMessage();
            $retArray[2] = $tmpErrMsgBody;
        }

        return($retArray);
    }



    //----------------------------------------------
    // Workspacesテーブルからデータを取得
    //----------------------------------------------
    function getWorkspaceData($workspaceID){
        $retArray = array();
        $retArray[0] = false; //boolean
        $retArray[1] = array(); //sql
        $retArray[2] = ""; //errorメッセージ

        try{
            $sql = "SELECT *
                    FROM   B_TERRAFORM_WORKSPACES
                    WHERE  DISUSE_FLAG = '0' AND WORKSPACE_ID = :WORKSPACE_ID";
            $tmpAryBind = array('WORKSPACE_ID' => $workspaceID);
            $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, __FUNCTION__);
            if( $retArray[0] === true ){
                $intTmpRowCount=0;
                $showTgtRow = array();
                $objQuery =& $retArray[1];
                while($row = $objQuery->resultFetch() ){
                    if($row !== false){
                        $intTmpRowCount+=1;
                    }
                    if($intTmpRowCount==1){
                        $showTgtRow = $row;
                    }
                }
                $selectRowLength = $intTmpRowCount;
                if( $selectRowLength != 1 ){
                    throw new Exception( '00000500-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                }
                unset($objQuery);
            }
            else{
                throw new Exception( '00000600-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[1] = $showTgtRow;

        }catch(Exception $e){
            // DBアクセス事後処理
            if ( isset($objQuery)    ) unset($objQuery);
            if ( isset($objQueryUtn) ) unset($objQueryUtn);
            if ( isset($objQueryJnl) ) unset($objQueryJnl);

            $retArray[0] = false;
            $tmpErrMsgBody = $e->getMessage();
            $retArray[2] = $tmpErrMsgBody;
        }

        return($retArray);
    }





?>