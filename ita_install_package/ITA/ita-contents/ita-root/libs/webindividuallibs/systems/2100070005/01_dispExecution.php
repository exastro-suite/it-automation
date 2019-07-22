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
    
    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $execution_management_dir = dirname($g['page_dir'])."/execution_management";
    
    //----オーケストレータ別の設定記述
    
    $strExeTableIdForSelect = 'E_OPENST_RESULT_MNG';
    
    //オーケストレータ別の設定記述----
    
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    

    $retArrayMng=array();
    $retArrayDetail=array();
    try{
        $sql = "SELECT TAB_A.TIME_START,TAB_A.TIME_END,TAB_A.I_OPERATION_NAME,TAB_A.STATUS_ID,TAB_B.STATUS_NAME FROM C_OPENST_RESULT_MNG TAB_A LEFT OUTER JOIN B_OPENST_STATUS TAB_B ON TAB_A.STATUS_ID =TAB_B.STATUS_ID WHERE EXECUTION_NO = :EXECUTION_NO";
        $tmpAryBind = array( 'EXECUTION_NO'=>$target_execution_no );
        $retArrayMng = singleSQLExecuteAgent($sql, $tmpAryBind, "");

        if( $retArrayMng[0] === true ){
            $result=[
                "mng"=>[],
                "detail"=>[]
            ];

            $objQuery = $retArrayMng[1];
            while($row = $objQuery->resultFetch() ){
                $result['mng'][]=$row;
    
            }
        $sql = "SELECT TAB_A.TIME_START,TAB_A.TIME_END,TAB_A.SYSTEM_NAME,TAB_A.STATUS_ID,TAB_B.STATUS_NAME FROM C_OPENST_RESULT_DETAIL TAB_A LEFT OUTER JOIN B_OPENST_DETAIL_STATUS TAB_B ON TAB_A.STATUS_ID =TAB_B.STATUS_ID WHERE EXECUTION_NO = :EXECUTION_NO"; 
            $retArrayDetail = singleSQLExecuteAgent($sql, array( 'EXECUTION_NO'=>$target_execution_no ), "");

            $objQuery = $retArrayDetail[1];
            while($row = $objQuery->resultFetch() ){
                $result['detail'][]=$row;
            }
        }
        else{
            throw new Exception( '00000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        
    }
    catch (Exception $e){
        //----正常時でも飛ばす版
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        
        // DBアクセス事後処理
        if ( isset($objQuery) )    unset($objQuery);

        if( !empty($output_str) ){
            //----正常（単なる処理省略）
            //正常（単なる処理省略）----
        }
        else if( !empty($warning_info) ){
            //----警告
            //警告----
        }
        else{
            // エラーフラグをON
            if( empty($error_info) ) $error_info = $tmpErrMsgBody;
        }
        //正常時でも飛ばす版----
    }
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-2",__FILE__),$intControlDebugLevel01);
?>
