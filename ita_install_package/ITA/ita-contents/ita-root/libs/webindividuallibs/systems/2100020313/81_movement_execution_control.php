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
//      オーケストレータ別の設定記述あり
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////
//  Movement作業実行RestAPI [ INFO / CANCEL / SCRAM ] (Ansible-Legacy) //
////////////////////////////////////////////////////////////////////////
function movementExecutionControlFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

    // グローバル変数宣言
    global $g;

    // 各種ローカル変数を定義
    $intControlDebugLevel01 = 250;

    $arrayRetBody = array();
    $aryRetBody = array();
    $intResultStatusCode = null;
    $aryForResultData = array();
    $aryPreErrorData = null;

    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";

    $strSymphonyInstanceId = "";
    $strExpectedErrMsgBodyForUI = "";

    $strSysErrMsgBody = '';
    $intErrorPlaceMark = "";
    $strErrorPlaceFmt = "%08d";

    $aryOverrideForErrorData = array();

    $output_str = '';
    $error_info = '';
    $warning_info = '';

    $intResultInfoCode="000";//結果コード(正常終了)

    if ( $g['page_dir'] == "2100020111" || $g['page_dir'] == "2100020112" ) $OrchestratorDB="E_ANSIBLE_LNS_EXE_INS_MNG"; //Ansible-Legacy
    if ( $g['page_dir'] == "2100020211" || $g['page_dir'] == "2100020212" ) $OrchestratorDB="E_ANSIBLE_PNS_EXE_INS_MNG"; //Ansible-Pioneer
    if ( $g['page_dir'] == "2100020312" || $g['page_dir'] == "2100020313" ) $OrchestratorDB="E_ANSIBLE_LRL_EXE_INS_MNG"; //Ansible-LegacyRole
    if ( $g['page_dir'] == "2100060009" || $g['page_dir'] == "2100060010" ) $OrchestratorDB="E_DSC_EXE_INS_MNG";         //DSC
    if ( $g['page_dir'] == "2100070004" || $g['page_dir'] == "2100070005" ) $OrchestratorDB="E_OPENST_RESULT_MNG";       //OPENSTACK
    $strExeTableIdForSelect = $OrchestratorDB;      

    $target_execution_no = "";
    $ExecInsResult = array();

    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);


    $ola_common_lib_dir = "libs/webcommonlibs/orchestrator_link_agent";
    require_once($g['root_dir_path']."/".$ola_common_lib_dir."/71_basic_common_lib.php");

    try{

        if( is_array($objJSONOfReceptedData) !== true ){
            $tmpAryOrderData = array();
        }
        else{
            $tmpAryOrderData = $objJSONOfReceptedData;
        }
        //配列から実行NO取得
        list($target_execution_no  , $boolKeyExists) = isSetInArrayNestThenAssign($tmpAryOrderData ,array('EXECUTION_NO') ,null);

        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));

        if( $objIntNumVali->isValid($target_execution_no) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";  
            $strExpectedErrMsgBodyForUI = $g['objMTS']->getSomeMessage("ITABASEH-ERR-806",$objIntNumVali->getValidRule());
        }

        unset($objIntNumVali);
        
        $ExecInsInfo ="";
        //作業Noの情報取得
        $ExecInsInfo = getInfoOfOneExeInstance($target_execution_no,$strExeTableIdForSelect);

        //X-command毎の処理   
        switch ($strCommand) {
            case 'CANCEL':

                    require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/05_bookCancel.php");

                    //----返し値の解析
                    if( !empty($warning_info) || !empty($error_info)){
                        $intResultInfoCode="002";//結果コード(予約取消不可)

                    }

                break;
            case 'SCRAM':
                //緊急停止可能状態(未実行、準備中、実行中、実行中(遅延))
                //Ansible,DSCの場合
                $arrStatusCode = array(1,2,3,4);
                //OpenStackの場合
                if ( $g['page_dir'] == "2100070004" || $g['page_dir'] == "2100070005" )$arrStatusCode = array(1,3,4);
                //ステータスチェック
                if ( array_key_exists('STATUS_ID', $ExecInsInfo) ) {
                    if ( in_array ( $ExecInsInfo['STATUS_ID'] , $arrStatusCode ) ){
                        require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/04_scram.php");
                    }else{
                        $strExpectedErrMsgBodyForUI = $g['objMTS']->getSomeMessage("ITAANSIBLEH-ERR-101030",$ExecInsInfo['STATUS_NAME']);
                        $intResultInfoCode="003";//結果コード(緊急停止不可)                            
                    }
                }else{
                    //存在しない作業No時のエラー用
                    require_once($g['root_dir_path']."/libs/webindividuallibs/systems/".$g['page_dir']."/04_scram.php");
                }

               //----返し値の解析
                if( !empty($warning_info) || !empty($error_info)){
                    $intResultInfoCode="003";//結果コード(緊急停止不可)
                }

                break;
            default:
                $intErrorPlaceMark = 1000;
                $intResultStatusCode = 400;
                $aryOverrideForErrorData['Error'] = 'Forbidden';
                web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            break;
        }

        if( !empty($warning_info) )$strExpectedErrMsgBodyForUI = str_replace(array("\r\n", "\r", "\n"), '', $warning_info); 
              
        $intResultStatusCode = 200;

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];
        // 実行結果を結果へ格納
        $aryForResultData['resultdata'] = array();
        $aryForResultData['resultdata']['EXECUTION_NO'] = $target_execution_no;
        $aryForResultData['resultdata']['RESULTCODE'] = $intResultInfoCode;
        $aryForResultData['resultdata']['RESULTINFO'] = $strExpectedErrMsgBodyForUI;
        
    }
    catch (Exception $e){
        // 失敗時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['errorInfo'];
        foreach($aryOverrideForErrorData as $strKey=>$varVal){
            $aryForResultData[$strKey] = $varVal;
        }
        if( 0 < strlen($strExpectedErrMsgBodyForUI) ){
            $aryPreErrorData[] = $strExpectedErrMsgBodyForUI;
        }
        $tmpErrMsgBody = $e->getMessage();
        dev_log($tmpErrMsgBody, $intControlDebugLevel01);
        if( $intResultStatusCode === null ) $intResultStatusCode = 500;
        if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        $intResultInfoCode   = "";//結果コード(異常終了)
    }

    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);
}

function getInfoOfOneExeInstance($target_execution_no,$strExeTableIdForSelect){
    // グローバル変数宣言
    global $g;
    $strFxName = __FUNCTION__;

    // レコードをSELECT
    $strSelectLastUpdateTimestamp1 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.TIME_BOOK"            ,"DATEDATE",false);
    $strSelectLastUpdateTimestamp2 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.TIME_START"           ,"DATEDATE",false);
    $strSelectLastUpdateTimestamp3 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.TIME_END"             ,"DATEDATE",false);
    $strSelectLastUpdateTimestamp4 = makeSelectSQLPartForDateWildColumn($g['db_model_ch'],"TAB_A.LAST_UPDATE_TIMESTAMP","DATEDATE",false);

    $strConnectString1 = makeStringConnectForSQLPart($g['db_model_ch'],array("'('","TAB_A.LAST_UPDATE_USER","')'"));

    //----オーケストレータ別の設定記述 
    switch ($strExeTableIdForSelect) {
        case 'E_DSC_EXE_INS_MNG':

                $sql = "SELECT  TAB_A.EXECUTION_NO,
                                TAB_A.SYMPHONY_NAME,
                                TAB_A.EXECUTION_USER,
                                TAB_A.PATTERN_ID,
                                TAB_A.I_PATTERN_NAME,
                                TAB_A.I_TIME_LIMIT,
                                TAB_A.ANS_HOST_DESIGNATE_TYPE_NAME,
                                TAB_A.I_ANS_PARALLEL_EXE,
                                TAB_A.STATUS_ID,
                                TAB_A.OPERATION_NO_UAPK,
                                TAB_A.I_OPERATION_NAME,
                                TAB_A.I_OPERATION_NO_IDBH,
                                TAB_A.STATUS_NAME,
                                {$strSelectLastUpdateTimestamp1} AS TIME_BOOK,
                                {$strSelectLastUpdateTimestamp2} AS TIME_START,
                                {$strSelectLastUpdateTimestamp3} AS TIME_END,
                                TAB_A.FILE_INPUT,
                                TAB_A.FILE_RESULT,
                                TAB_A.RUN_MODE_NAME,
                                TAB_A.NOTE,
                                {$strSelectLastUpdateTimestamp4} AS LAST_UPDATE_TIMESTAMP,
                                CASE TAB_B.USERNAME_JP WHEN NULL THEN {$strConnectString1}
                                                           ELSE TAB_B.USERNAME_JP
                                                           END AS LAST_UPDATE_USER
                            FROM    {$strExeTableIdForSelect} TAB_A
                            LEFT JOIN A_ACCOUNT_LIST             TAB_B ON (TAB_A.LAST_UPDATE_USER = TAB_B.USER_ID)
                            WHERE   TAB_A.DISUSE_FLAG = '0'
                            AND     TAB_A.EXECUTION_NO = :EXECUTION_NO_BV ";
            break;

        case 'E_OPENST_RESULT_MNG':
                $sql = "SELECT   TAB_A.TIME_START,
                         TAB_A.TIME_END,
                         TAB_A.I_OPERATION_NAME,
                         TAB_A.STATUS_ID,TAB_B.STATUS_NAME
                FROM C_OPENST_RESULT_MNG TAB_A 
                LEFT OUTER JOIN B_OPENST_STATUS TAB_B ON TAB_A.STATUS_ID =TAB_B.STATUS_ID 
                WHERE EXECUTION_NO = :EXECUTION_NO_BV";

            break;

        case 'E_ANSIBLE_LNS_EXE_INS_MNG':
        case 'E_ANSIBLE_PNS_EXE_INS_MNG':
        case 'E_ANSIBLE_LRL_EXE_INS_MNG':
            //----オーケストレータ別の設定記述
            $sql = "SELECT  TAB_A.EXECUTION_NO,
                            TAB_A.SYMPHONY_NAME,
                            TAB_A.EXECUTION_USER,
                            TAB_A.PATTERN_ID,
                            TAB_A.I_PATTERN_NAME,
                            TAB_A.I_TIME_LIMIT,
                            TAB_A.ANS_HOST_DESIGNATE_TYPE_NAME,
                            TAB_A.I_ANS_PARALLEL_EXE,
                            TAB_A.ANS_WINRM_FLAG_NAME,
                            TAB_A.STATUS_ID,
                            TAB_A.OPERATION_NO_UAPK,
                            TAB_A.I_OPERATION_NAME,
                            TAB_A.I_OPERATION_NO_IDBH,
                            TAB_A.STATUS_NAME,
                            {$strSelectLastUpdateTimestamp1} AS TIME_BOOK,
                            {$strSelectLastUpdateTimestamp2} AS TIME_START,
                            {$strSelectLastUpdateTimestamp3} AS TIME_END,
                            TAB_A.FILE_INPUT,
                            TAB_A.FILE_RESULT,
                            TAB_A.RUN_MODE_NAME,
                            TAB_A.I_ANS_PLAYBOOK_HED_DEF,
                            TAB_A.I_ANS_EXEC_OPTIONS,
                            TAB_A.EXEC_MODE,
                            TAB_A.EXEC_MODE_NAME,

                            TAB_A.NOTE,
                            {$strSelectLastUpdateTimestamp4} AS LAST_UPDATE_TIMESTAMP,
                            CASE TAB_B.USERNAME_JP WHEN NULL THEN {$strConnectString1}
                                                       ELSE TAB_B.USERNAME_JP
                                                       END AS LAST_UPDATE_USER
                        FROM    {$strExeTableIdForSelect} TAB_A
                        LEFT JOIN A_ACCOUNT_LIST             TAB_B ON (TAB_A.LAST_UPDATE_USER = TAB_B.USER_ID)
                        WHERE   TAB_A.DISUSE_FLAG = '0'
                        AND     TAB_A.EXECUTION_NO = :EXECUTION_NO_BV ";

            break;
        default:

            break;
    }
    //オーケストレータ別の設定記述----   

    $tmpAryBind = array( 'EXECUTION_NO_BV'=> $target_execution_no );
    $retArray = singleSQLExecuteAgent($sql, $tmpAryBind, $strFxName);
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
        unset($objQuery);
    }

    return $showTgtRow;
      
}






?>
