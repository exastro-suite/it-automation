
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

function getPatternListWithOrchestratorInfo($fxVarsStrFilterData="",$fxVarsResultType=0){
    global $g;
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryListSource = array();
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        $obj = new RoleBasedAccessControl($objDBCA);

        $aryRet = $objOLA->getLiveOrchestratorFromMaster();
        if( $aryRet[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = $aryRet[1];
            $strErrStepIdInFx="00000100";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $aryOrcListRow = $aryRet[0];
        
        $boolBinaryDistinctOnDTiS = false;
        
        //オーケストレータ情報の収集----
        
        //----存在するオーケスト—タ分回る
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $varOrcId = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $varOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            
            $objOLA->addFuncionsPerOrchestrator($varOrcId,$varOrcRPath);
            
            $aryRet = $objOLA->getLivePatternList($varOrcId,$fxVarsStrFilterData,$boolBinaryDistinctOnDTiS);
            if( $aryRet[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = $aryRet[1];
                $strErrStepIdInFx="00000200";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //
            $aryRow = $aryRet[0];
            
            //----オーケストレータカラーを取得
            $aryRet = $objOLA->getThemeColorName($varOrcId);
            if( $aryRet[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = $aryRet[1];
                $strErrStepIdInFx="00000300";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strThemeColor = $aryRet[0];
            //オーケストレータカラーを取得----
            
            if( $fxVarsResultType === 1 ){
                foreach($aryRow as $arySingleRow){

                    $intPatternId = $arySingleRow['PATTERN_ID'];
                    //
                    // 表示データをSELECT
                    $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                           ." WHERE DISUSE_FLAG='0' "
                           ." AND PATTERN_ID = $intPatternId "
                           ."";
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    $rows = array();

                    $row = $objQuery->resultFetch();

                    $user_id = $g['login_id'];
                    $ret  = $obj->getAccountInfo($user_id); 
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                    } else {
                        if($permission === true) {
                            $aryListSource[] = $varOrcId;
                            $aryListSource[] = $arySingleRow['PATTERN_ID'];
                            $aryListSource[] = $arySingleRow['PATTERN_NAME'];
                            $aryListSource[] = $strThemeColor;
                        }
                    }
                }
            }
            else{
                foreach($aryRow as $arySingleRow){
                    $tmpRow = array();
                    $intPatternId = $arySingleRow['PATTERN_ID'];
                    //
                    $tmpRow['PATTERN_ID']      = $intPatternId;
                    $tmpRow['ORCHESTRATOR_ID'] = $varOrcId;
                    $tmpRow['PATTERN_NAME']    = $arySingleRow['PATTERN_NAME'];
                    $tmpRow['ThemeColor']      = $strThemeColor;

                    // 表示データをSELECT
                    $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                           ." WHERE DISUSE_FLAG='0' "
                           ." AND PATTERN_ID = $intPatternId "
                           ."";
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    $rows = array();

                    $row = $objQuery->resultFetch();

                    $user_id = $g['login_id'];
                    $ret  = $obj->getAccountInfo($user_id); 
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                    } else {
                        if($permission === true) {
                            $aryListSource[$intPatternId] = $tmpRow;
                        }
                    }
                }
            }
        }
        //存在するオーケスト—タ分回る----
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryListSource);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}

//----Conductorのパラメータの整形
function nodeDateDecodeForEdit($fxVarsStrSortedData){
    global $g;
    $aryMovement = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    $strSysErrMsgBody = "";

    $intLengthArySettingForParse = count($fxVarsStrSortedData);

    $aryMovement = array();
    //node分繰り返し
    $aryNode = array();
    $arrpatternDel = array('/__proto__/');
    $arrpatternPrm = array('/node/','/id/','/type/','/note/','/condition/','/case/','/x/','/y/','/w/','/h/','/edge/','/targetNode/','/PATTERN_ID/','/ORCHESTRATOR_ID/','/OPERATION_NO_IDBH/','/SYMPHONY_CALL_CLASS_NO/','/SKIP_FLAG/','/CONDUCTOR_CALL_CLASS_NO/','/CALL_CONDUCTOR_ID/','/CALL_SYMPHONY_ID/','/ACCESS_AUTH/'  );

    foreach( $fxVarsStrSortedData as $nodename => $nodeinfo ){
        //　nodeの処理開始
        if( strpos($nodename,'node-') !== false  ){
            foreach ($nodeinfo as $key => $value) {
                #nodeパラメータ整形
                $ASD = preg_replace( $arrpatternPrm, "" , $key );
                if( $ASD == "" ){
                    if( is_array($value) ){
                        foreach ($value as $optionkey => $optionval) {
                            $aryNode[$nodename][$optionkey]=$optionval;
                        }
                    }else{
                        $aryNode[$nodename][$key]=$value;
                    }
                    #terminalパラメータ
                }elseif( strpos($key,'terminal') !== false  ){
                    foreach ($value as $terminalname => $terminalarr) {
                        if( is_array($terminalarr) ){
                            #terminalパラメータ整形
                            foreach ($terminalarr as $terminalkey => $terminalinfo) {
                                $ZXC = preg_replace( $arrpatternDel, "" , $terminalkey);
                                if( is_array($terminalinfo) && isset($terminalarr['condition'])){
                                    foreach ($terminalinfo as $arrterminalval)$aryNode[$nodename][$key][$terminalname][$terminalkey][] = $arrterminalval;
                                 }elseif( $ZXC != ""  ){
                                    if( !is_array($terminalinfo) && strlen($terminalkey) >= 1){
                                        $aryNode[$nodename][$key][$terminalname][$terminalkey] = $terminalinfo ;
                                    }
                                }
                            }
                        }
                    }            
                }
            }                
        }
    }

    return $aryNode;

}
//Conductorのパラメータの整形----

//----ある１のConductorの定義を新規登録（追加）する
function conductorClassRegisterExecute($fxVarsIntConductorClassId ,$fxVarsAryReceptData, $fxVarsStrSortedData, $fxVarsStrLT4UBody,$getmode="", $fxVarsaryOptionOrderOverride=array()){

    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "000";
    $intConductorClassId = '';
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
   
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $aryConfigForSymClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONDUCTOR_NAME"=>"",
        "DESCRIPTION"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $arySymClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONDUCTOR_NAME"=>"",
        "DESCRIPTION"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $arrayConfigForNodeClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "NODE_CLASS_NO"=>"",
        "NODE_NAME"=>"",
        "NODE_TYPE_ID"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "CONDUCTOR_CALL_CLASS_NO"=>"",
        "DESCRIPTION"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "SKIP_FLAG"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "POINT_W"=>"",
        "POINT_H"=>"",
        "DISP_SEQ"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryNodeClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "NODE_CLASS_NO"=>"",
        "NODE_NAME"=>"",
        "NODE_TYPE_ID"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "CONDUCTOR_CALL_CLASS_NO"=>"",
        "DESCRIPTION"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "SKIP_FLAG"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "POINT_W"=>"",
        "POINT_H"=>"",
        "DISP_SEQ"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    

    $arrayConfigForTermClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "TERMINAL_CLASS_NO"=>"",
        "TERMINAL_CLASS_NAME"=>"",
        "TERMINAL_TYPE_ID"=>"",
        "NODE_CLASS_NO"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONNECTED_NODE_NAME"=>"",
        "LINE_NAME"=>"",
        "TERMINAL_NAME"=>"",
        "CONDITIONAL_ID"=>"",
        "CASE_NO"=>"",
        "DESCRIPTION"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "DISP_SEQ"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryTermClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "TERMINAL_CLASS_NO"=>"",
        "TERMINAL_CLASS_NAME"=>"",
        "TERMINAL_TYPE_ID"=>"",
        "NODE_CLASS_NO"=>"",
        "CONDUCTOR_CLASS_NO"=>"",
        "CONNECTED_NODE_NAME"=>"",
        "LINE_NAME"=>"",
        "TERMINAL_NAME"=>"",
        "CONDITIONAL_ID"=>"",
        "CASE_NO"=>"",
        "DESCRIPTION"=>"",
        "POINT_X"=>"",
        "POINT_Y"=>"",
        "DISP_SEQ"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );

    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
 
    #$getmode= 1;
    //Conductor対象テーブル先
    if( $getmode != "" ){
        //クラス編集時 (2100180003)
        $arrTableName=array(
            "conductor"     => "C_CONDUCTOR_EDIT_CLASS_MNG",
            "node"          => "C_NODE_EDIT_CLASS_MNG",
            "terminal"      => "C_NODE_TERMINALS_EDIT_CLASS_MNG"
        ); 
    }else{
        //クラス状態保存 (2100180004)
        $arrTableName=array(
            "conductor"     => "C_CONDUCTOR_CLASS_MNG",
            "node"          => "C_NODE_CLASS_MNG",
            "terminal"      => "C_NODE_TERMINALS_CLASS_MNG"
        ); 
    }

    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        $objRBAC = new RoleBasedAccessControl($objDBCA);

        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>false));
        if( $objIntNumVali->isValid($fxVarsIntConductorClassId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170003",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        
        $objSLTxtVali = new SingleTextValidator(0,128,false);
        if( $objSLTxtVali->isValid($fxVarsStrLT4UBody) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000200";
            //
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5720403",array($objSLTxtVali->getValidRule()));
        }
        unset($objSLTxtVali);
    

        #Symfony-nodeパラメータ整形
        $aryExecuteData = $fxVarsAryReceptData;
        $aryNodeData = $objOLA->nodeDateDecodeForedit($fxVarsStrSortedData);
        #'start','end','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank'
    

        $strErrMsg="";
        //Conductorクラス名称
        $strConductorName = '';
        if( array_key_exists("conductor_name",$aryExecuteData) === true ){
            $strConductorName = $aryExecuteData["conductor_name"];
        }
        $objSLTxtVali = new SingleTextValidator(1,256,false);
        if( $objSLTxtVali->isValid($strConductorName) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170000",array($objSLTxtVali->getValidRule()));
        }else{
            //クラス編集時のみ
            if(  $getmode != "" ){
                //Conductor Name 重複チェック
                $strQuery = "SELECT"
                            ." * "
                            ." FROM "
                            ." ${arrTableName['conductor']} "
                            ."WHERE "
                            ." DISUSE_FLAG IN ('0') "
                            ."AND CONDUCTOR_NAME = :CONDUCTOR_NAME "
                            ."";
                $tmpDataSet = array();
                $tmpForBind = array();
                $tmpForBind['CONDUCTOR_NAME']=$fxVarsAryReceptData['conductor_name'];

                $tmpRetBody = singleSQLExecuteAgent($strQuery, $tmpForBind, $strFxName);

                if( $tmpRetBody[0] === true ){
                    $objQuery = $tmpRetBody[1];
                    while($tmprow = $objQuery->resultFetch() ){
                        if( $tmprow['CONDUCTOR_CLASS_NO'] != $fxVarsIntConductorClassId )$tmpDataSet[]= $tmprow['CONDUCTOR_CLASS_NO'];
                    }
                    unset($objQuery);
                }else{
                    $intErrorType = 500;
                    $intRowLength = -1;
                }
                $aryclass = $tmpDataSet;

                if( $aryclass != array() ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001200";
                    $intErrorType = 2;                
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITAWDCH-ERR-603",array($g['objMTS']->getSomeMessage("ITABASEH-MNU-309005"), implode(",", $aryclass))) . "[(" . $objMTS->getSomeMessage("ITABASEH-MNU-305070") . ")]";
                    
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );        
                }
            }
        }
        unset($objSLTxtVali);
        
        $strConductorTips = '';
        if( array_key_exists("note",$aryExecuteData) === true ){
            $strConductorTips = $aryExecuteData["note"];
        }
        $objMLTxtVali = new MultiTextValidator(0,4000);
        if( $objMLTxtVali->isValid($strConductorTips) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-5721203",array($objMLTxtVali->getValidRule()));
        }
        unset($objMLTxtVali);

        #312
        if( array_key_exists("NOTICE_INFO",$aryExecuteData) === true ){
            if(is_array($aryExecuteData['NOTICE_INFO']) === true ){
                //通知設定ありの場合
                if( count( $aryExecuteData['NOTICE_INFO'] ) != 0 ){
                    $steNoticeList = implode( ",", array_keys($aryExecuteData['NOTICE_INFO']) );

                    //通知の存在チェック
                    $retArray = $objOLA->getNoticeInfo( $steNoticeList );                
                    if( count($retArray[4]) == 0 ){
                        // エラーフラグをON
                        // 例外処理へ
                        $intErrorType = 2;
                        $tmpnoticeIDs = implode(",",  array_keys($retArray[2]) );
                        $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-5733205",array($tmpnoticeIDs));//"選択された通知が不正です。(". $tmpnoticeIDs .")";

                    }elseif( count($retArray[2]) != 0  ){
                        // エラーフラグをON
                        // 例外処理へ
                        $intErrorType = 2;
                        $tmpnoticeIDs = implode(",",  array_keys($retArray[2]) );
                        $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-5733205",array($tmpnoticeIDs));//"選択された通知が不正です。(". $tmpnoticeIDs .")";

                    }
                }                
            }
        }


        if( $strErrMsg != "" ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            $intErrorType = 2;
            $strExpectedErrMsgBodyForUI = $strErrMsg;#implode("\n",$strErrMsg);
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(入力形式)----
      
        //----バリデーションチェック(NODE形式)
        //接続先判定用ノードのリスト作成
        $arrNodeChkList=array();
        foreach ($aryNodeData as $key => $value) {
            $arrNodeChkList[]=$value['id'];
        }

        //各ノードの接続状態
        foreach ($aryNodeData as $key => $value) {
            if( isset($value['terminal']) ){
                foreach ( $value['terminal'] as $terminalname => $terminalnameinfo) {
                    //ノードの接続、接続先の有無判定
                    if( isset($terminalnameinfo['targetNode']) ){
                        if( $terminalnameinfo['targetNode'] == "" ){
                            $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170001");
                        }else{
                            if( array_search( $terminalnameinfo['targetNode'], $arrNodeChkList) === false ){
                                 $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170002");
                            }
                        }
                    }else{
                        $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170002");                    
                    }
                    if( isset($terminalnameinfo['edge']) ){
                        if( $terminalnameinfo['edge'] == "" ){
                            $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170002");
                        }
                    }else{
                        $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170002");
                    }
                }
            }else{
                $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170002");
            }

            if( $strErrMsg != "" ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $strErrMsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } 
        }

        //conditional-branchのステータス状態
        foreach ($aryNodeData as $key => $value) {
            if( $value['type'] == "conditional-branch" ){
                foreach ( $value['terminal'] as $terminalname => $terminalnameinfo) {
                    if( $terminalnameinfo['type'] == "out" ){
                        if( isset($terminalnameinfo['condition']) ){                    
                            if( $terminalnameinfo['condition'] == array() ){
                                //"Conditional branch - Caseの設定が不正です。"
                                $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170044");
                            }
                        }else{
                                //"Conditional branch - Caseの設定が不正です。"
                                $strErrMsg=$objMTS->getSomeMessage("ITABASEH-ERR-170044");      
                        }
                    }
                }
            }
            if( $strErrMsg != "" ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $strErrMsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } 
        }

        //各ノードの備考
        foreach ($aryNodeData as $key => $value) {
            $strNodeNote = '';
            if( array_key_exists("note",$value) === true ){
                $strNodeNote = $value["note"];
            }
            $objMLTxtVali = new MultiTextValidator(0,4000);
            if( $objMLTxtVali->isValid($strNodeNote) === false ){
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = 2;
                $strErrMsg = $objMTS->getSomeMessage("ITABASEH-ERR-5721203",array($objMLTxtVali->getValidRule()));
            }
            unset($objMLTxtVali);

            if( $strErrMsg != "" ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $strErrMsg;
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } 
        }

        $aryRetBody = checkNodeUseCaseValidate($aryNodeData);
    
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            //
            $intErrorType = $aryRetBody[1];
            if( $aryRetBody[1] < 500 ){
                $strExpectedErrMsgBodyForUI = implode("\n",$aryRetBody[2]);
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(NODE形式)----
      

        //----追い越しチェック　
        if( $fxVarsIntConductorClassId != "" ){
            $aryRetBody = $objOLA->getInfoOfOneConductor($fxVarsIntConductorClassId, 0 ,$getmode);
 
            $aryRowOfSymClassTable=$aryRetBody[4];

            //REST対応　更新用の最終更新日時形式変換
            $fxVarsStrLT4UBody = str_replace('T_', '', $fxVarsStrLT4UBody);
            if(strpos($fxVarsStrLT4UBody,'.') !== false){
              $fxVarsStrLT4UBody = wordwrap($fxVarsStrLT4UBody, 14, '.', true);
            }
            
            //追い越しチェック　
            if( 'T_'.$fxVarsStrLT4UBody != 'T_'.$aryRowOfSymClassTable['LUT4U'] ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001200";
                $intErrorType = 2;
                
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720305");
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );        
            }

            
        }
        //追い越しチェック----
        //----バリデーションチェック(NODE毎詳細)
        $aryMovement  = $aryNodeData;
        foreach($aryMovement as $aryDataForMovement){

            //個別オペレーションのチェック、取得
            if( !isset($aryDataForMovement['OPERATION_NO_IDBH']) )$aryDataForMovement['OPERATION_NO_IDBH']="";
            if( !isset($aryDataForMovement['PATTERN_ID']) )$aryDataForMovement['PATTERN_ID']="";
            if($aryDataForMovement['OPERATION_NO_IDBH'] != "")
            {
                $tmpStrOpeNoIDBH = $aryDataForMovement['OPERATION_NO_IDBH'];
                $tmpStrPatternID = $aryDataForMovement['PATTERN_ID'];
                $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));

                //movement/call/call_sにオペレーション個別指定がある場合は、個別に指定されたオペレーションのIDをチェック対象とする
                if($aryDataForMovement['type'] == "movement" || $aryDataForMovement['type'] == "call" || $aryDataForMovement['type'] == "call_s"){
                    $nodeId = $aryDataForMovement['id'];
                    if(!empty($fxVarsaryOptionOrderOverride)){
                        $overrideOperationId = $fxVarsaryOptionOrderOverride[$nodeId]['OPERATION_NO_IDBH'];
                        if(isset($overrideOperationId)){
                            $tmpStrOpeNoIDBH = $overrideOperationId;
                        }
                    }
                }

                if( $objIntNumVali->isValid($tmpStrOpeNoIDBH) === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002600";
                    $intErrorType = 2;
                    if($aryDataForMovement['type'] == "call"){
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170020",array($aryDataForMovement['CALL_CONDUCTOR_ID'],$tmpStrOpeNoIDBH)); //ConductorCall - オペレーションIDの値が不正です。(Conductor:{} オペレーションID:{})
                    }elseif($aryDataForMovement['type'] == "call_s"){
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170021",array($aryDataForMovement['CALL_CONDUCTOR_ID'],$tmpStrOpeNoIDBH)); //ConductorCall - オペレーションIDの値が不正です。(Conductor:{} オペレーションID:{})
                    }else{
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170004",array($tmpStrPatternID,$tmpStrOpeNoIDBH)); //"Movement - オペレーションIDの値が不正です。(MovementID:{} オペレーションID:{})";
                    }

                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                unset($objIntNumVali);

                $tmpAryRetBody = $objOLA->getInfoOfOneOperation($tmpStrOpeNoIDBH,1);

                if( $tmpAryRetBody[1] !== null ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002700";
                    //
                    if( $tmpAryRetBody[1] == 101 ){
                        $intErrorType = 2;
                        if($aryDataForMovement['type'] == "call"){
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170022",array($aryDataForMovement['CALL_CONDUCTOR_ID'])); //ConductorCall - オペレーションIDが存在している必要があります。(Conductor:{})
                        }elseif($aryDataForMovement['type'] == "call_s"){
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170023",array($aryDataForMovement['CALL_SYMPHONY_ID'])); //SymphonyCall - オペレーションIDが存在している必要があります。(Symphony:{})
                        }else{
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170005",array($tmpStrPatternID)); //Movement - オペレーションIDが存在している必要があります。(Movement:{})
                        }

                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
                
                //オペレーションのアクセス権チェック対応
                $arrOpList = $tmpAryRetBody[4];
                $user_id = $g['login_id'];
                $ret  = $objRBAC->getAccountInfo($user_id);
                list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($arrOpList);
                if($ret === false) {
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00000200";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                } else {
                    if($permission !== true) {
                        //アクセス権限を持っていない場合
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002710";
                        if($aryDataForMovement['type'] == "call"){
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170024"); //ConductorCall - 指定できないオペレーションIDです。
                        }elseif($aryDataForMovement['type'] == "call_s"){
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170025"); //SymphonyCall - 指定できないオペレーションIDです。
                        }else{
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170026"); //Movement - 指定できないオペレーションIDです。
                        }

                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }

            }
            
            if( !isset( $aryDataForMovement['CALL_CONDUCTOR_ID'] ) )$aryDataForMovement['CALL_CONDUCTOR_ID']="";
            if( !isset( $aryDataForMovement['note'] ) )$aryDataForMovement['note']="";
            if( !isset( $aryDataForMovement['NEXT_PENDING_FLAG'] ) )$aryDataForMovement['NEXT_PENDING_FLAG']="";
            if( !isset( $aryDataForMovement['DESCRIPTION'] ) )$aryDataForMovement['DESCRIPTION']="";
            if( !isset( $aryDataForMovement['ORCHESTRATOR_ID'] ) )$aryDataForMovement['ORCHESTRATOR_ID']="";
            if( !isset( $aryDataForMovement['PATTERN_ID'] ) )$aryDataForMovement['PATTERN_ID']="";
            if( !isset( $aryDataForMovement['OPERATION_NO_IDBH'] ) )$aryDataForMovement['OPERATION_NO_IDBH']="";
            if( !isset( $aryDataForMovement['SKIP_FLAG'] ) )$aryDataForMovement['SKIP_FLAG']="";
            if( !isset( $aryDataForMovement['NEXT_PENDING_FLAG'] ) )$aryDataForMovement['NEXT_PENDING_FLAG']="";
            if( !isset( $aryDataForMovement['CALL_SYMPHONY_ID'] ) )$aryDataForMovement['CALL_SYMPHONY_ID']="";

            //廃止済みMovement対応
            if( $aryDataForMovement['type'] == "movement" ){
                if (  ( $aryDataForMovement['ORCHESTRATOR_ID'] == "" || !is_numeric( $aryDataForMovement['ORCHESTRATOR_ID'] ) ) &&
                      ( $aryDataForMovement['PATTERN_ID'] == "" || !is_numeric( $aryDataForMovement['PATTERN_ID'] ) ) 
                ){
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170013");
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }else{
                    $aryRetBody = getPatternListWithOrchestratorInfo("",-1);
                    if( $aryRetBody[1] !== null ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00000200";
                        $intErrorType = $aryRetBody[1];
                        //
                        $aryErrMsgBody = $aryRetBody[2];
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    $arrMVList = $aryRetBody[4];

                    if( !isset($arrMVList[ $aryDataForMovement['PATTERN_ID'] ]) ){
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170013");
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }

            }

            //CALL呼び出し値有無
            if( $aryDataForMovement['type'] == "call" && ( $aryDataForMovement['CALL_CONDUCTOR_ID'] == "" || !is_numeric( $aryDataForMovement['CALL_CONDUCTOR_ID'] ) ) ){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170006",array($fxVarsIntConductorClassId));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
   
            //CALL呼び出しのループ簡易バリデーション
            if( $fxVarsIntConductorClassId != "" ){
                if ( $fxVarsIntConductorClassId == $aryDataForMovement['CALL_CONDUCTOR_ID']){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170006",array($fxVarsIntConductorClassId));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }

            //CALL呼び出しのループバリデーション
            if( $aryDataForMovement['type'] == "call" && is_numeric( $aryDataForMovement['CALL_CONDUCTOR_ID'] ) ){

                    if( $fxVarsIntConductorClassId != "" ){
                        $getmode = 1;
                        $retArray = $objOLA->getInfoFromOneOfConductorClass($aryDataForMovement['CALL_CONDUCTOR_ID'], 0,0,0,$getmode);#TERMINALあり
                        $conductorDataList = $retArray[4];
                        $tmpNodeLists = $retArray[5];
                        foreach ($tmpNodeLists as $key => $value) {
                            if( ( $value["NODE_TYPE_ID"] == 4 ) && ( $fxVarsIntConductorClassId == $value["CONDUCTOR_CALL_CLASS_NO"] ) ){
                                $intErrorType = 2;
                                $strErrStepIdInFx="00002800";
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170014",array($fxVarsIntConductorClassId));
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );    
                            }
                        }

                        //廃止済みConductor対応
                        if($conductorDataList['DISUSE_FLAG'] == 1){
                            $intErrorType = 2;
                            $strErrStepIdInFx="00002810";
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170027"); //ConductorCall - 指定できないConductorクラスIDです。
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }

                        //Conductorアクセス権チェック対応
                        $user_id = $g['login_id'];
                        $ret  = $objRBAC->getAccountInfo($user_id);
                        list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($conductorDataList);
                        if($ret === false) {
                            // エラーフラグをON
                            // 例外処理へ
                            $strErrStepIdInFx="00000200";
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        } else {
                            if($permission !== true) {
                                //アクセス権限を持っていない場合
                                $intErrorType = 2;
                                $strErrStepIdInFx="00002820";
                                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170027"); //ConductorCall - 指定できないConductorクラスIDです。
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            }
                        }

                    }

                    $arrConductorList = array();
                    $aryRetBody = ConductorCallLoopValidator( $objOLA,$aryDataForMovement['CALL_CONDUCTOR_ID'],$arrConductorList );

                    if( $aryRetBody === false  ) {
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170014",array($fxVarsIntConductorClassId));
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
            }
 
            //CALL呼び出し値有無(symphony)
            if( $aryDataForMovement['type'] == "call_s" && ( $aryDataForMovement['CALL_SYMPHONY_ID'] == "" || !is_numeric( $aryDataForMovement['CALL_SYMPHONY_ID'] ) ) ){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170015");
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            //廃止済みSymphony対応
            if( $aryDataForMovement['type'] == "call_s" ){
                    $aryRetBody = $objOLA->getInfoOfOneSymphony($aryDataForMovement['CALL_SYMPHONY_ID'],-1);
                    
                    if( $aryRetBody[1] !== null ){
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170015");
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    $arrMVList = $aryRetBody[4];

                    if( !isset($arrMVList['SYMPHONY_CLASS_NO']) ){
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170015");
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
            }

            //symphonyアクセス権チェック対応
            if( $aryDataForMovement['type'] == "call_s" ){
                $aryRetBody = $objOLA->getInfoOfOneSymphony($aryDataForMovement['CALL_SYMPHONY_ID'],-1);

                    if( $aryRetBody[1] !== null ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00000200";
                        $intErrorType = $aryRetBody[1];
                        //
                        $aryErrMsgBody = $aryRetBody[2];
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    $arrMVList = $aryRetBody[4];
                    $user_id = $g['login_id'];
                    $ret  = $objRBAC->getAccountInfo($user_id);
                    list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($arrMVList);
                    if($ret === false) {
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00000200";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    } else {
                        if($permission !== true) {
                            //アクセス権限を持っていない場合
                            $intErrorType = 2;
                            $strErrStepIdInFx="00002830";
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170015");
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                    }

            }

            //ENDノード終了タイプ #467 
            if( $aryDataForMovement['type'] == "end"  ){
                if( $aryDataForMovement['END_TYPE'] != "" ){
                    if( array_search($aryDataForMovement['END_TYPE'], array(5,7,11) ) === false ){
                        $intErrorType = 2;
                        $strErrStepIdInFx="00002800";
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170039");#"ENDノード終了タイプが不正です。";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }                    
                }
            }

            //Status-File-Branchノード の条件(if/elseif)重複チェック/caseチェック #587
            if( $aryDataForMovement['type'] == "status-file-branch"  ){
                $arrConditionalVal = array();
                $arrConditionalcaseNo = array();
                $strChkMsg = "";

                foreach ( $aryDataForMovement['terminal'] as $tmpterminalkey => $tmpArrterminal) {
                    if( $tmpArrterminal['type'] == "out" && $tmpArrterminal['case'] != "else"){
                        if( array_key_exists("condition", $tmpArrterminal ) ){
                            
                            $strconditionalval = $tmpArrterminal['condition'][0];
                            $intconditionalCaseNo = $tmpArrterminal['case'];

                            if( $strconditionalval != "" ){
                                //if / elseif の値のcaseNoチェック
                                if( array_search($intconditionalCaseNo, $arrConditionalcaseNo ) === false){
                                    $arrConditionalcaseNo[] = $intconditionalCaseNo;
                                }else{
                                    $strChkMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170040");
                                    #"Status file branch - 条件分岐が不正です。";
                                }   
                                //if / elseif の値の重複チェック
                                if( array_search($strconditionalval, $arrConditionalVal ) === false){
                                    $arrConditionalVal[] = $strconditionalval;
                                }else{
                                     $strChkMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170041");
                                     #"Status file branch - 条件分岐(if/elseif)に重複した値が設定されています。";
                                }
                            }else{
                                //if / elseif 条件無し
                                $strChkMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170042");
                                #"Status file branch - 条件分岐(if/elseif)に値が設定されていません。";
                            }

                        }else{
                            //if / elseif 条件無し
                            $strChkMsg = $objMTS->getSomeMessage("ITABASEH-ERR-170042");
                            #"Status file branch - 条件分岐(if/elseif)に値が設定されていません。";                         
                        } 
                    }
                }

                if( $strChkMsg != "" ){
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $strChkMsg ;
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }                
            }

        }
         //-バリデーションチェック(NODE毎詳細)---

        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000600";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----

        //---登録処理    
        if( $fxVarsIntConductorClassId != "" ){
            $arrayResult = $objOLA->conductorClassRegister($fxVarsIntConductorClassId ,$fxVarsAryReceptData, $fxVarsStrSortedData, $fxVarsStrLT4UBody,$getmode);
        }else{
            $arrayResult = $objOLA->conductorClassRegister(null, $fxVarsAryReceptData, $fxVarsStrSortedData, null,$getmode);            
        }
            
        if( $arrayResult[0] == "000" ){
            $intShmphonyClassId = $arrayResult[2];
        }else{
            $intErrorType = $arrayResult[0];
            $intDetailType = $arrayResult[1];
            $aryErrMsgBody[]=$arrayResult[3];
            $intShmphonyClassId= $arrayResult[2];
            $strExpectedErrMsgBodyForUI = $arrayResult[3];

            $strErrStepIdInFx="00000500";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //登録処理---

        //----バリデーションチェック(CALL呼び出しのループバリデーション)
        $aryMovement  = $aryNodeData;
        foreach($aryMovement as $aryDataForMovement){

            //CALL呼び出しのループバリデーション
            if( $aryDataForMovement['type'] == "call" && is_numeric( $aryDataForMovement['CALL_CONDUCTOR_ID'] ) ){
                if( $intShmphonyClassId != "" ){
                    $getmode = 1;
                    $retArray = $objOLA->getInfoFromOneOfConductorClass($aryDataForMovement['CALL_CONDUCTOR_ID'], 0,0,0,$getmode);#TERMINALあり
                    $tmpNodeLists = $retArray[5];
                    foreach ($tmpNodeLists as $key => $value) {
                        if( ( $value["NODE_TYPE_ID"] == 4 ) && ( $intShmphonyClassId == $value["CONDUCTOR_CALL_CLASS_NO"] ) ){
                            $intErrorType = 2;
                            $strErrStepIdInFx="00002800";
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170014",array($intShmphonyClassId));
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );    
                        }
                    }
                }

                $arrConductorList = array();
                $aryRetBody = ConductorCallLoopValidator( $objOLA,$aryDataForMovement['CALL_CONDUCTOR_ID'],$arrConductorList );

                if( $aryRetBody === false  ) {
                    $intErrorType = 2;
                    $strErrStepIdInFx="00002800";
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170014",array($intShmphonyClassId));
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }        
            }
        }
         //-バリデーションチェック(CALL呼び出しのループバリデーション)---

        // ----代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする
        $sql = "UPDATE A_PROC_LOADED_LIST "
               ."SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
               ."WHERE ROW_ID IN (2100020002,2100020004,2100020006,2100080002)";

        $objDBCA->setQueryTime();
        $aryForBind = array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime());

        // SQL実行
        $retArray = singleSQLCoreExecute($objDBCA, $sql, $aryForBind, "");
        if( $retArray[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001600";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // 代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする----

        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----

        $retBool = true;
        $intConductorClassId = $intShmphonyClassId;

    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102010");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101030");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102020");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101040");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $retArray = array($strResultCode,
                      $strDetailCode,
                      $intConductorClassId,
                      nl2br($strExpectedErrMsgBodyForUI)
                      );

    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//ある１のConductorの定義を新規登録（追加）する----


//----NODEの接続先（IN/OUT）のバリデーション
function checkNodeUseCaseValidate($aryNodeData){

    global $g;
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    

    $arrNodeVariList=array();
    $arrNodeVariList['start']=array(
        "in"=>array(),
        "out"=>array('movement','call','call_s','parallel-branch','blank')
    );
    $arrNodeVariList['end']=array(
        "in"=>array('movement','call','call_s','conditional-branch','merge','pause','blank','status-file-branch'),
        "out"=>array()
    );
    $arrNodeVariList['movement']=array(
        "in"=>array('start','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank','status-file-branch'),
        "out"=>array('end','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank','status-file-branch')
    );
    $arrNodeVariList['call']=array(
        "in"=>array('start','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank','status-file-branch'),
        "out"=>array('end','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank')
    );
    $arrNodeVariList['call_s']=array(
        "in"=>array('start','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank','status-file-branch'),
        "out"=>array('end','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank')
    );
    $arrNodeVariList['parallel-branch']=array(
        "in"=>array('start','movement','call','call_s','conditional-branch','merge','pause','blank','status-file-branch'),
        "out"=>array('movement','call','call_s','blank')
    );
    $arrNodeVariList['conditional-branch']=array(
        "in"=>array('movement','call','call_s'),
        "out"=>array('end','movement','call','call_s','parallel-branch','pause','blank')
    );
    $arrNodeVariList['merge']=array(
        "in"=>array('movement','call','call_s','pause','blank'),
        "out"=>array('end','movement','call','call_s','parallel-branch','pause','blank')
    );
    $arrNodeVariList['pause']=array(
        "in"=>array('movement','call','call_s','parallel-branch','merge','blank','status-file-branch'),
        "out"=>array('end','movement','call','call_s','parallel-branch','merge','blank')
    );
    $arrNodeVariList['blank']=array(
        "in"=>array('start','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank'),
        "out"=>array('start','movement','call','call_s','parallel-branch','conditional-branch','merge','pause','blank')
    );

    // #587
    $arrNodeVariList['status-file-branch']=array(
        "in"=>array('movement'),
        "out"=>array('end','movement','call','call_s','parallel-branch','pause','blank')
    );

    try{
        foreach ($aryNodeData as $key => $value) {
            if(isset($value['type'])){
                $nodetype = $value['type'];
            }
            $arrTerminal=array();
            if(isset($value['terminal'])){
                foreach ($value['terminal'] as $tkey => $tvalue) {
                    if(isset($tvalue['type'])){
                        $arrTerminal[$tvalue['type']]=$tvalue['targetNode'];
                    }else{
                        $aryErrMsgBody[] = $objMTS->getSomeMessage("ITAWDCH-ERR-26000",array($nodetype));
                        $strErrStepIdInFx="00000300";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
            }else{
                $aryErrMsgBody[] = $objMTS->getSomeMessage("ITAWDCH-ERR-26000",array($nodetype));
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            foreach ($arrTerminal as $tkey => $tvalue) {
                $nodetype2 = $aryNodeData[$tvalue]['type'];
                
                if(isset($arrNodeVariList[$nodetype][$tkey])){
                    $retNodeValidate = in_array($nodetype2,$arrNodeVariList[$nodetype][$tkey]);
                    if( $retNodeValidate == false ){
                            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITAWDCH-ERR-26000",array($nodetype));
                            $strErrStepIdInFx="00000300";
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }

            }

        }
        $retBool = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 2;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }

    $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);

    return $retArray;

}
//NODEの接続先（IN/OUT）のバリデーション----


//----ある１のConductorのクラス定義を表示する
function printOneOfonductorClasses($fxVarsIntSymphonyClassId, $fxVarsIntMode){
    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyClassId = "";
    $intMode = "";
    $strStreamOfMovements = "";
    $strStreamOfSymphony = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        //----Conductorが存在するか？
        
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyClassId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170000",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intSymphonyClassId = $fxVarsIntSymphonyClassId;

        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntMode) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000200";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170007",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intMode = $fxVarsIntMode;
        //バリデーションチェック(入力形式)----
        
        //----symphony_ins_noごとに作業パターンの流れを収集する
        //----バリデーションチェック(実質評価)
        $aryRetBody = $objOLA->getInfoFromOneOfConductorClass($fxVarsIntSymphonyClassId, 0);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            //
            if( $aryRetBody[1] === 101 ){
                $intErrorType = 2;
                //
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-170008");
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(実質評価)----
        $aryRowOfSymClassTable = $aryRetBody[4];
        $aryRowOfMovClassTable = $aryRetBody[5];
        
        //----オーケストレータ情報の収集
        
        $aryRetBody = $objOLA->getLiveOrchestratorFromMaster();
        
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryOrcListRow = $aryRetBody[0];
        
        $aryPatternListPerOrc = array();
        //----存在するオーケスト—タ分回る
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $varOrcId = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $varOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            
            $objOLA->addFuncionsPerOrchestrator($varOrcId,$varOrcRPath);
            $aryRetBody = $objOLA->getLivePatternList($varOrcId);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRow = $aryRetBody[0];
            
            //----オーケストレータカラーを取得
            $aryRetBody = $objOLA->getThemeColorName($varOrcId);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strThemeColor = $aryRetBody[0];
            //オーケストレータカラーを取得----
            
            $aryPatternListPerOrc[$varOrcId]['ThemeColor'] = $strThemeColor;
        }
        //存在するオーケスト—タ分回る----
        
        //オーケストレータ情報の収集----
        
        //----作業パターンの収集
        
        $aryRetBody = $objOLA->getLivePatternFromMaster();
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryPatternList = $aryRetBody[0];
        
        //作業パターンの収集----
        

        //----Conductor-Node-TerminalクラスのJSON形式の取得
        
        $aryRetBody = $objOLA->convertConductorClassJson($fxVarsIntSymphonyClassId);

        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $strnodeJson = $aryRetBody[5];
        
        //Conductor-Node-TerminalクラスのJSON形式の取得----

        $strLT4UBody = '';
        if( 0 < strlen($aryRowOfSymClassTable['LUT4U']) ){
            $strLT4UBody = 'T_'.$aryRowOfSymClassTable['LUT4U'];
        }
        

    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    //
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyClassId,
                         $intMode,
                         $strnodeJson,
                         $strLT4UBody,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//ある１のConductorのクラス定義を表示する----


//----Movement一覧のリスト
function printPatternListForEditJSON($fxVarsStrFilterData){
    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strPatternListStream = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    //----オーケストレータ—ごとに作業パターンを収集する
    try{
        //----バリデーションチェック(入力形式)
        $objSLTxtVali = new SingleTextValidator(0,256,false);
        if( $objSLTxtVali->isValid($fxVarsStrFilterData) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720102",array($objSLTxtVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objSLTxtVali);
        //バリデーションチェック(入力形式)----
        
        $aryRetBody = getPatternListWithOrchestratorInfo($fxVarsStrFilterData,0);
        
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            $intErrorType = $aryRetBody[1];
            //
            $aryErrMsgBody = $aryRetBody[2];
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryListSource = array_values($aryRetBody[4]);

        $strPatternListStream = json_encode($aryListSource,JSON_UNESCAPED_UNICODE);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strPatternListStream,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    return $arrayResult;
}
//Movement一覧のリスト----

//Callループチェック処理呼び出し用
function ConductorCallLoopValidator( $objOLA,$intConductorclass,$arrConductorList=array() ){
    $aryRetBody = checkCallLoopValidator ($objOLA,$intConductorclass,$arrConductorList );
    return $aryRetBody;

}

//Callループチェック処理
function checkCallLoopValidator( $objOLA,$intConductorclass,$arrConductorList=array() ){
    $getmode = 1;
    $retArray = $objOLA->getInfoFromOneOfConductorClass($intConductorclass, 0,0,0,$getmode);#TERMINALあり
    $tmpNodeLists = $retArray[5];
    $arrCallLists=array();
    //重複排除
    foreach ($tmpNodeLists as $key => $value) {
        if( $value["NODE_TYPE_ID"] == 4 ){
            $arrCallLists[$value["CONDUCTOR_CALL_CLASS_NO"]]=$value["CONDUCTOR_CALL_CLASS_NO"];
        }
    }
    foreach ($arrCallLists as $key => $value) {
        $arrConductorList[$intConductorclass][$value]=$value;
        $retArray2 = $objOLA->getInfoFromOneOfConductorClass($value, 0,0,0,$getmode);#TERMINALあり
        $tmpNodeLists2 = $retArray2[5];
        $arrCallLists2=array();
        //重複排除
        foreach ($tmpNodeLists2 as $key2 => $value2) {
            if( $value2["NODE_TYPE_ID"] == 4 ){
                $arrCallLists2[$value2["CONDUCTOR_CALL_CLASS_NO"]]=$value2["CONDUCTOR_CALL_CLASS_NO"];
            }
        }
        foreach ($arrCallLists2 as $key2 => $value2) {
            if( !isset( $arrConductorList[$intConductorclass] ) ){
                $arrConductorList[$intConductorclass][$value2]=$value2;
                $arrConductorList = checkCallLoopValidator ( $objOLA,$value2,$arrConductorList );
            }elseif( !isset( $arrConductorList[$intConductorclass][$value2] ) ){
                $arrConductorList[$intConductorclass][$value2]=$value2;
                $arrConductorList = checkCallLoopValidator (  $objOLA,$value2,$arrConductorList );
            }else{
                // 例外処理へ
                return false;
            }         
        }
    }

    return $arrConductorList;
}
            

?>
