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


//////////////////////////////////////////////////////////////////
//  シンフォニークラスRestAPI EDIT (登録、更新、廃止、復活) //
//////////////////////////////////////////////////////////////////

function conductorRegisterFromRest($strCalledRestVer,$strCommand,$objJSONOfReceptedData){

    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;

    $arrayRetBody = array();

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

    $strResultType01 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12202");   //登録/Register
    $strResultType02 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12203");   //更新/Update
    $strResultType03 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12204");   //廃止/Discard
    $strResultType04 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12205");   //復活/Restore
    $strResultType99 = $g['objMTS']->getSomeMessage("ITAWDCH-STD-12206");   //エラー/Error

    $tmpResult  = array();
    $resultdata = array();
    $resultdata_count = array();
    $resultdata_count['register'] = array("name" => $strResultType01,"ct" => 0);
    $resultdata_count['update']   = array("name" => $strResultType02,"ct" => 0);
    $resultdata_count['delete']   = array("name" => $strResultType03,"ct" => 0);
    $resultdata_count['revive']   = array("name" => $strResultType04,"ct" => 0);
    $resultdata_count['error']    = array("name" => $strResultType99,"ct" => 0);

    // 各種ローカル変数を定義
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);


    try{

        if( $objJSONOfReceptedData == array()  ){
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        //パラメータ整形、入力データチェック
        foreach ($objJSONOfReceptedData as $key => $value) {
          // ローカル変数宣言
          $arrayResult = array();
          $objJSONarrChk = 1;
          $strJsonKey ="";
          $arrayReceptData = "";
          $Process_type = "";

          //整形
          $tmpReceptData = $value;
          if( isset($tmpReceptData['edittype'])){
              $Process_type = $tmpReceptData['edittype'];
          }else{
              $strJsonKey = 'edittype';
          }
          if( isset($tmpReceptData['conductor'])){
              $arrayReceptData = $tmpReceptData['conductor'];
          }

          //配列構造のチェック
          switch ($Process_type) {
              //登録
              case $strResultType01:
                  if(isset($tmpReceptData['edittype'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'edittype';
                    break;
                  }
                  if(isset($tmpReceptData['conductor'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'conductor';
                    break;
                  }
                  if(isset($tmpReceptData['config'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'config';
                    break;
                  }
                  break;
              //更新、廃止、復活
              case $strResultType02:
              case $strResultType03:
              case $strResultType04:
                  if(isset($tmpReceptData['edittype'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'edittype';
                    break;
                  }
                  if(isset($tmpReceptData['conductor'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'conductor';
                    break;
                  }
                  if(isset($arrayReceptData['id'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'id';
                    break;
                  }
                  if(isset($arrayReceptData['LUT4U'])){
                    $objJSONarrChk=0;
                  }else{
                    $objJSONarrChk=1;
                    $strJsonKey = 'LUT4U';
                    break;
                  }
                  break;
              default:
                  break;
          }

          //データ構造異常時
          if( $objJSONarrChk != 0 ){
              $intErrorType = 2;
              $strErrStepIdInFx="00000100";
              throw new Exception( $g['objMTS']->getSomeMessage("ITABASEH-ERR-170107") ."[".$strJsonKey."]" );
          }
      }

        //X-command毎の処理
        switch ($strCommand) {
            case 'EDIT':
                #登録種別毎の処理
                foreach ($objJSONOfReceptedData as $key => $value) {
                    $tmparrayResult = array();
                    $Process_type = "";
                    $intShmphonyClassId = "";
                    $arrayReceptData = "";
                    $strSortedData = "";
                    $strLT4UBody = "";

                    $tmpReceptData = $value;
                    $arrayReceptData = $tmpReceptData['conductor'];

                    if( array_key_exists('edittype', $tmpReceptData) ) $Process_type = $tmpReceptData['edittype'];
                    if( array_key_exists('id', $arrayReceptData) ) $intShmphonyClassId = $arrayReceptData['id'];
                    $strSortedData = $tmpReceptData;
                    unset($strSortedData['conductor']);
                    foreach ($strSortedData as $subkey => $subvalue) {
                        if( preg_match('/line-/',$subkey) ){
                            unset($strSortedData[$subkey]);
                        }
                    }
                    unset($strSortedData['conductor']);
                    if( array_key_exists('LUT4U', $arrayReceptData) ) $strLT4UBody = substr_replace($arrayReceptData['LUT4U'], '.', -6, 0);

                    switch ($Process_type) {
                        //登録
                        case $strResultType01:
                            unset($strSortedData['config']);
                            unset($strSortedData['edittype']);
                            foreach ($strSortedData as $key => $value) {
                              if(strpos($key,'node') !== false){
                                $strSortedData['node'][$key] = $value;
                              }
                            }
                            $tmparrayResult = register_execute($arrayReceptData, $strSortedData);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'register');
                            break;
                        //更新
                        case $strResultType02:
                            unset($strSortedData['config']);
                            unset($strSortedData['edittype']);
                            foreach ($strSortedData as $key => $value) {
                              if(strpos($key,'node') !== false){
                                $strSortedData['node'][$key] = $value;
                              }
                            }
                            $tmparrayResult = update_execute($intShmphonyClassId, $arrayReceptData, $strSortedData, $strLT4UBody);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'update');
                            break;
                        //廃止
                        case $strResultType03:
                            $arrayLT4UBody = array();
                            $arrayLT4UBody[0] = array(
                                          "name"  => "UPD_UPDATE_TIMESTAMP",
                                          "value" => $arrayReceptData['LUT4U']);
                            $tmparrayResult = delete_revive_execute(3, $intShmphonyClassId, $arrayLT4UBody);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'delete');
                            break;
                        //復活
                        case $strResultType04:
                            $arrayLT4UBody = array();
                            $arrayLT4UBody[0] = array(
                                          "name"  => "UPD_UPDATE_TIMESTAMP",
                                          "value" => $arrayReceptData['LUT4U']);
                            $tmparrayResult = delete_revive_execute(5, $intShmphonyClassId, $arrayLT4UBody);
                            $resultdata_count = result_chk_count($tmparrayResult,$resultdata_count,'revive');
                            break;
                        default:
                            $intErrorPlaceMark = 1000;
                            $intResultStatusCode = 400;
                            $aryOverrideForErrorData['Error'] = 'Forbidden';
                            web_log($g['objMTS']->getSomeMessage("ITABASEH-ERR-3820101"));
                            throw new Exception( sprintf($strErrorPlaceFmt,$intErrorPlaceMark).'-([FUNCTION]' . $strFxName . ',[FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
                            break;
                    }

                    $tmpResult[] = $tmparrayResult;
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

        $intResultStatusCode = 200;

        // 成功時のデータテンプレを取得
        $aryForResultData = $g['requestByREST']['preResponsContents']['successInfo'];

        $resultdata['LIST']['NORMAL'] = $resultdata_count;
        $resultdata['LIST']['RAW'] = Array();
        $resultdata['LIST']['RAW'] = $tmpResult;

        $aryForResultData['resultdata'] = $resultdata;

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
        web_log($tmpErrMsgBody." ".$intControlDebugLevel01);
        if( $intResultStatusCode === null ) $intResultStatusCode = 500;
        if( $aryPreErrorData !== null ) $aryForResultData['Error'] = $aryPreErrorData;
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }

    $arrayRetBody = array('ResultStatusCode'=>$intResultStatusCode,
                          'ResultData'=>$aryForResultData);

    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return array($arrayRetBody,$intErrorType,$aryErrMsgBody,$strErrMsg);

}

//////////////////////////////////////////
//  (シンフォニークラス編集)クラス登録  //
//////////////////////////////////////////
function register_execute($arrayReceptData, $strSortedData){
    // グローバル変数宣言
    global $g;

    // ローカル変数宣言
    $arrayResult = array();

    require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");
    $arrayResult = conductorClassRegisterExecute(null, $arrayReceptData, $strSortedData, null, 1);

    // 結果判定
    if($arrayResult[0]=="000"){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }else if(intval($arrayResult[0])<500){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
        $arrayResult[3] = str_replace(array(" ", "　", "\n", "\r\n"), "",strip_tags($arrayResult[3]));
    }else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
        $arrayResult[3] = str_replace(array(" ", "　", "\n", "\r\n"), "",strip_tags($arrayResult[3]));
    }
    return $arrayResult;
}

//////////////////////////////////////////
//  (シンフォニークラス編集)クラス更新  //
//////////////////////////////////////////
function update_execute($intConductorClassId, $arrayReceptData, $strSortedData, $strLT4UBody){
    // グローバル変数宣言
    global $g;

    // ローカル変数宣言
    $arrayResult = array();
    //整形

    require_once($g['root_dir_path']."/libs/webcommonlibs/orchestrator_link_agent/74_conductorClassAdmin.php");

    $arrayResult = conductorClassRegisterExecute($intConductorClassId, $arrayReceptData, $strSortedData, $strLT4UBody,1);

    // 結果判定
    if($arrayResult[0]=="000"){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }else if(intval($arrayResult[0])<500){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
    }else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));
    }
    return $arrayResult;
}

/////////////////////////////////
//  deleteTableファンクション  //
/////////////////////////////////
function delete_revive_execute($mode, $innerSeq, $arrayReceptData){
    // グローバル変数宣言
    global $g;

    // ローカル変数宣言
    $arrayResult = array();
    $aryVariant = array();
    $arySetting = array();

    $arratDeleteData = array();

    $arratDeleteData = convertReceptDataToDataForIUD($arrayReceptData);

    $arySetting = array("Mix1_1","fakeContainer_Delete1","fakeContainer_Delete1");

    // 本体ロジックをコール
    require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/table_control_agent/05_deleteTable.php");
    $arrayResult = deleteTableMain($mode, $innerSeq, $arratDeleteData, null, 3, $aryVariant, $arySetting);
    //$arrayResult[3] = $innerSeq;

    // 結果判定
    if($arrayResult[0]=="000"){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-STD-4001",__FUNCTION__));
    }else if(intval($arrayResult[0])<500){
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4002",__FUNCTION__));
    }else{
        web_log( $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4001",__FUNCTION__));

    }
    return $arrayResult;
}

/////////////////////////////////
//  実行状況の(register update delete error)カウント  //
/////////////////////////////////
function result_chk_count($tmparrayResult,$resultdata_count,$Type){
  if($tmparrayResult[0]=="000"){
    $resultdata_count[$Type]['ct']++;
  }else{
    $resultdata_count['error']['ct']++;
  }
  return $resultdata_count;
}

//Conductorクラス情報のJSON見出し行取得----
function conductorJsonGetTitle(){
  // グローバル変数宣言
  global $g;

  $arr_json = array('edittype' => $g['objMTS']->getSomeMessage("ITAWDCH-STD-12201"),
                    'disuse' => $g['objMTS']->getSomeMessage("ITAWDCH-STD-12204"),);

  $arr_json['conductor'] = array('conductor_name' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305070"),
                                 'id' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305040"),
                                 'note' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305090"),
                                 'LUT4U' => $g['objMTS']->getSomeMessage("ITAWDCH-STD-18011"),
                                 'ACCESS_AUTH' => $g['objMTS']->getSomeMessage("ITAWDCH-MNU-1300001")."/".$g['objMTS']->getSomeMessage("ITAWDCH-MNU-1300002"),
                                 'NOTICE_INFO' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305280"),
                               );

  $arr_json['config'] = array('editorVersion' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305100"),
                              'nodeNumber' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305110"),
                              'terminalNumber' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305120"),
                              'edgeNumber' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305130"));

  $arr_json['node'] = array('h' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305140"),
                            'id' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-308001"),
                            'type' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305150"),
                            'END_TYPE' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305270"), //終了種別 #467
                            'PATTERN_ID' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-209105"),
                            'ORCHESTRATOR_ID' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-308005"),
                            'Name' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-108020"),
                            'CALL_CONDUCTOR_ID' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305040"),
                            'CONDUCTOR_NAME' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305070"),
                            'CALL_SYMPHONY_ID' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-206050"),
                            'SYMPHONY_NAME' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-206060"),
                            'OPERATION_NO_IDBH' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-207070"),
                            'SKIP_FLAG' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-308010"),
                            'OPERATION_NAME' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-207080"),
                            'note' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305090"),
                            'w' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305160"),
                            'x' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305170"),
                            'y' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305180"));

  $arr_json['node']['terminal'] = array('case' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-308110"),
                                        'edge' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305190"),
                                        'id' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-308101"),
                                        'targetNode' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305200"),
                                        'type' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305150"),
                                        'condition' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305210"),
                                        'x' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305170"),
                                        'y' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305180"));

  $arr_json['line'] = array('id' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305220"),
                            'type' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305150"),
                            'inNode' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305230"),
                            'outTerminal' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305260"),
                            'inTerminal' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305250"),
                            'outNode' => $g['objMTS']->getSomeMessage("ITABASEH-MNU-305240"));

  return $arr_json;
 }

//Conductorクラス情報の整形＋JSON形式へ----
function convertConductorClassJson($intConductorClassId,$strDdisuse,$getmode=""){

  // グローバル変数宣言
  global $g;

  require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
  $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA']);

    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";

    $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

    //Conductorクラス情報取得
    $aryRetBody = $objOLA->getInfoFromOneOfConductorClass($intConductorClassId, 0,0,1,$getmode);#TERMINALあり

    if( $aryRetBody[1] !== null ){
        // 例外処理へ
        $strErrStepIdInFx="00001000";
        throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
    }
    $boolRet = true;
    $arrConductorData = $aryRetBody[4];
    $arrNodeData = $aryRetBody[5];

    //----作業パターンの収集

    $aryRetBody = $objOLA->getLivePatternFromMaster();
    if( $aryRetBody[1] !== null ){
        // エラーフラグをON
        // 例外処理へ
        $strErrStepIdInFx="00000700";
        //
        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
    }
    $aryPatternList = array();
    foreach ($aryRetBody[0] as $key => $value) {
        $aryPatternList[$value['PATTERN_ID']]=$value;
    }
    //作業パターンの収集----


    $arr_json=array();
    $arr_json['edittype'] = null;
    $arr_json['disuse'] = $strDdisuse;
    $strLT4UBody = "T_" .str_replace('.', '', $arrConductorData['LUT4U']);
    $accessAuth = "";
    if( isset( $arrConductorData['ACCESS_AUTH'] )  == true ){
        $accessAuth=$arrConductorData['ACCESS_AUTH'];
    }
    $arr_json['conductor']=array('conductor_name' => $arrConductorData['CONDUCTOR_NAME'],
                                 'id' => $intConductorClassId,
                                 'note' => $arrConductorData['DESCRIPTION'],
                                 'LUT4U' => $strLT4UBody,
                                 'ACCESS_AUTH' => $accessAuth,
                                 'NOTICE_INFO' => $arrConductorData['NOTICE_INFO'],
                               );

    $intNodeNumber=0;
    $intTerminalNumber=0;
    $intEdgeNumber=0;

    $arr_json['config'] = array('editorVersion' => '1.0.2',
                                'nodeNumber' => $intNodeNumber,
                                'terminalNumber' => $intTerminalNumber,
                                'edgeNumber' => $intEdgeNumber);

    //NODE2成形
    foreach ($arrNodeData as $key => $value) {

        //初期値設定
        $arr_json[$value['NODE_NAME']] = array('h' => '',
                                  'id' => '',
                                  'terminal' => array(),
                                  'type' => '',
                                  'END_TYPE' => '', // 終了タイプ #467
                                  'PATTERN_ID' => '',
                                  'ORCHESTRATOR_ID' => '',
                                  'Name' => '',
                                  'CALL_CONDUCTOR_ID' => '',
                                  'CONDUCTOR_NAME' => '',
                                  'CALL_SYMPHONY_ID' => '',
                                  'SYMPHONY_NAME' => '',
                                  'OPERATION_NO_IDBH' => '',
                                  'SKIP_FLAG' => '',
                                  'OPERATION_NAME' => '',
                                  'note' => '',
                                  'w' => '',
                                  'x' => '',
                                  'y' => '');

        $arr_json[$value['NODE_NAME']]['h'] = $value['POINT_H'];
        $arr_json[$value['NODE_NAME']]['id'] = $value['NODE_NAME'];

        //NODE_TYPE置換
        if( $value['NODE_TYPE_ID'] == 1) $arr_json[$value['NODE_NAME']]['type']="start";
        if( $value['NODE_TYPE_ID'] == 2) $arr_json[$value['NODE_NAME']]['type']="end";
        if( $value['NODE_TYPE_ID'] == 3) $arr_json[$value['NODE_NAME']]['type']="movement";
        if( $value['NODE_TYPE_ID'] == 4) $arr_json[$value['NODE_NAME']]['type']="call";
        if( $value['NODE_TYPE_ID'] == 5) $arr_json[$value['NODE_NAME']]['type']="parallel-branch";
        if( $value['NODE_TYPE_ID'] == 6) $arr_json[$value['NODE_NAME']]['type']="conditional-branch";
        if( $value['NODE_TYPE_ID'] == 7) $arr_json[$value['NODE_NAME']]['type']="merge";
        if( $value['NODE_TYPE_ID'] == 8) $arr_json[$value['NODE_NAME']]['type']="pause";
        if( $value['NODE_TYPE_ID'] == 9) $arr_json[$value['NODE_NAME']]['type']="blank";
        if( $value['NODE_TYPE_ID'] == 10) $arr_json[$value['NODE_NAME']]['type']="call_s";

        //#587
        if( $value['NODE_TYPE_ID'] == 11) $arr_json[$value['NODE_NAME']]['type']="status-file-branch";

        //END個別 #467
        if( $value['NODE_TYPE_ID'] == 2) {
            if( isset($value['END_TYPE']) === true ){
                $arr_json[$value['NODE_NAME']]['END_TYPE']=$value['END_TYPE'];
            }else{
                $arr_json[$value['NODE_NAME']]['END_TYPE']="5";
            }
        }
        
        //Movement個別
        if( $value['NODE_TYPE_ID'] == 3) {
            if( isset( $aryPatternList[$value['PATTERN_ID']] ) ){
                $arr_json[$value['NODE_NAME']]['PATTERN_ID']=$value['PATTERN_ID'];
                $arr_json[$value['NODE_NAME']]['ORCHESTRATOR_ID']=$value['ORCHESTRATOR_ID'];
                $arr_json[$value['NODE_NAME']]['Name']=$aryPatternList[$value['PATTERN_ID']]['PATTERN_NAME'];
            }else{
                if( $getmode == "" ){
                  $arr_json[$value['NODE_NAME']]['PATTERN_ID']=$value['PATTERN_ID'];
                  $arr_json[$value['NODE_NAME']]['ORCHESTRATOR_ID']=$value['ORCHESTRATOR_ID'];
                  $arr_json[$value['NODE_NAME']]['Name']="";
                }else{
                    //廃止済みMovemnt対応
                    $arr_json[$value['NODE_NAME']]['PATTERN_ID']="-";
                    $arr_json[$value['NODE_NAME']]['ORCHESTRATOR_ID']="-";
                    $arr_json[$value['NODE_NAME']]['Name']="-";
                }

            }
        }

        //call個別
        if( $value['NODE_TYPE_ID'] == 4) {
            $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];

            $strConductorName="";
            if( $value['CONDUCTOR_CALL_CLASS_NO'] != "" ){
                //Conductorクラス情報取得
                $aryRetBody = $objOLA->getInfoFromOneOfConductorClass($value['CONDUCTOR_CALL_CLASS_NO'], 0,0,1,$getmode);#TERMINALあり

                if( $aryRetBody[1] !== null ){
                    //廃止済みの場合
                    $strConductorName = "";
                    $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']="---";
                }else{
                    if($aryRetBody[4]['DISUSE_FLAG'] == 1){
                        //廃止済みの場合
                        $strConductorName = "";
                        $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']="---";
                    }else{
                        $strConductorName = $aryRetBody[4]['CONDUCTOR_NAME'];
                    }
                }
            }
            $arr_json[$value['NODE_NAME']]['CONDUCTOR_NAME']=$strConductorName;
        }

        //call(symphony)個別
        if( $value['NODE_TYPE_ID'] == 10) {
            #$arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];
            $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']=$value['CONDUCTOR_CALL_CLASS_NO'];

            $strConductorName="";
            if( $value['CONDUCTOR_CALL_CLASS_NO'] != "" ){
                //Symphonyクラス情報取得
                $aryRetBody = $objOLA->getInfoFromOneOfSymphonyClasses($value['CONDUCTOR_CALL_CLASS_NO'], 0);

                if( $aryRetBody[1] !== null ){
                    //廃止済みの場合
                    $strConductorName = "";
                    #$arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']="---";
                    $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']="---";
                }else{
                    $strConductorName = $aryRetBody[4]['SYMPHONY_NAME'];
                }
            }
            $arr_json[$value['NODE_NAME']]['SYMPHONY_NAME']=$strConductorName;
        }

        //#648 対応
        $objDBCA = $objOLA->getDBConnectAgent();

        $sql = "SELECT * FROM C_CONDUCTOR_INSTANCE_MNG
                WHERE I_CONDUCTOR_CLASS_NO = {$value['CONDUCTOR_CLASS_NO']}
                AND STATUS_ID NOT IN (1,2,3,4)
                AND DISUSE_FLAG = 0
                ";

        //SQL準備
        $objQuery = $objDBCA->sqlPrepare($sql);
        //SQL発行
        $r = $objQuery->sqlExecute();
        $arrEndIns=array();
        while ( $row = $objQuery->resultFetch() ){
            $arrEndIns = $row;
        }

        if( count($arrEndIns) != 0 ){
            if( $value['NODE_TYPE_ID'] == 4 || $value['NODE_TYPE_ID'] == 10) {
                $rows=array();

                if( $getmode == ""){
                    if( $value['NODE_TYPE_ID'] == 4  ){
                        $sql = "SELECT * FROM C_NODE_INSTANCE_MNG TAB_A
                                LEFT JOIN C_NODE_CLASS_MNG TAB_B ON TAB_A.I_NODE_CLASS_NO = TAB_B.NODE_CLASS_NO
                                LEFT JOIN C_CONDUCTOR_INSTANCE_MNG TAB_C ON TAB_B.CONDUCTOR_CLASS_NO = TAB_C.CONDUCTOR_CALLER_NO
                                WHERE TAB_A.CONDUCTOR_INSTANCE_NO IN
                                (SELECT CONDUCTOR_INSTANCE_NO FROM C_CONDUCTOR_INSTANCE_MNG WHERE I_CONDUCTOR_CLASS_NO = {$value['CONDUCTOR_CLASS_NO']} )
                                AND I_NODE_TYPE_ID IN ( {$value['NODE_TYPE_ID']} )
                                AND TAB_A.DISUSE_FLAG = 0
                                ";
                    }

                    if( $value['NODE_TYPE_ID'] == 10  ){
                    $sql = "SELECT * FROM C_NODE_INSTANCE_MNG TAB_A
                            LEFT JOIN C_NODE_CLASS_MNG TAB_B ON TAB_A.I_NODE_CLASS_NO = TAB_B.NODE_CLASS_NO
                            LEFT JOIN C_SYMPHONY_INSTANCE_MNG TAB_C ON TAB_A.CONDUCTOR_INSTANCE_CALL_NO = TAB_C.SYMPHONY_INSTANCE_NO
                            WHERE TAB_A.CONDUCTOR_INSTANCE_NO IN
                            (SELECT CONDUCTOR_INSTANCE_NO FROM C_CONDUCTOR_INSTANCE_MNG WHERE I_CONDUCTOR_CLASS_NO = {$value['CONDUCTOR_CLASS_NO']} )
                            AND I_NODE_TYPE_ID IN ( {$value['NODE_TYPE_ID']} )
                            AND TAB_A.DISUSE_FLAG = 0
                            ";
                    }

                    //SQL準備
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    //SQL発行
                    $r = $objQuery->sqlExecute();

                    while ( $row = $objQuery->resultFetch() ){
                        $rows[$row['NODE_CLASS_NO']] = $row;
                    }

                    if( $value['NODE_TYPE_ID'] == 4 && isset( $rows[$value['NODE_CLASS_NO']] ) ==  true ){
                        $arr_json[$value['NODE_NAME']]['CALL_CONDUCTOR_ID']=$rows[$value['NODE_CLASS_NO']]['CONDUCTOR_INSTANCE_CALL_NO'];
                        $arr_json[$value['NODE_NAME']]['CONDUCTOR_NAME']=$rows[$value['NODE_CLASS_NO']]['I_PATTERN_NAME'];
                    }

                    if( $value['NODE_TYPE_ID'] == 10 && isset( $rows[$value['NODE_CLASS_NO']] ) ==  true ){
                        $arr_json[$value['NODE_NAME']]['CALL_SYMPHONY_ID']=$rows[$value['NODE_CLASS_NO']]['CONDUCTOR_INSTANCE_CALL_NO'];
                        $arr_json[$value['NODE_NAME']]['SYMPHONY_NAME']=$rows[$value['NODE_CLASS_NO']]['I_PATTERN_NAME'];
                    }
                }
            }
        }

        //Movement,call,call_s共通
        if( $value['NODE_TYPE_ID'] == 3 || $value['NODE_TYPE_ID'] == 4 || $value['NODE_TYPE_ID'] == 10 ) {
            $arr_json[$value['NODE_NAME']]['OPERATION_NO_IDBH']=$value['OPERATION_NO_IDBH'];
            $arr_json[$value['NODE_NAME']]['SKIP_FLAG']=$value['SKIP_FLAG'];

            $strOpeName="";
            if( $value['OPERATION_NO_IDBH'] != "" ){
                // ----オペレーションNoからオペレーションの情報を取得する
                $arrayRetBody = $objOLA->getInfoOfOneOperation( $value['OPERATION_NO_IDBH'] );
                if( $arrayRetBody[1] !== null ){
                    //廃止済みの場合
                    $strOpeName = "";
                    $arr_json[$value['NODE_NAME']]['OPERATION_NO_IDBH']="-";
                }else{
                    // オペレーションNoからオペレーションの情報を取得する----
                    $aryRowOfOperationTable = $arrayRetBody[4];
                    $strOpeName = $aryRowOfOperationTable['OPERATION_NAME'];
                }

            }

            $arr_json[$value['NODE_NAME']]['OPERATION_NAME']=$strOpeName;
        }

        $arr_json[$value['NODE_NAME']]['note']=$value['DESCRIPTION'];

        $arr_json[$value['NODE_NAME']]['w']=$value['POINT_W'];
        $arr_json[$value['NODE_NAME']]['x']=$value['POINT_X'];
        $arr_json[$value['NODE_NAME']]['y']=$value['POINT_Y'];

        //NODEカウンタの取得
        $tmpNodeNumber  = intval( str_replace( "node-", "", $value['NODE_NAME'] ));
        if( $intNodeNumber < $tmpNodeNumber )$intNodeNumber=$tmpNodeNumber;

        //TERMINAL整形
        foreach ($value['TERMINAL'] as $tkey => $tval) {
          $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']] = array('case' => '',
                                                'edge' => '',
                                                'id' => '',
                                                'targetNode' => '',
                                                'type' => '',
                                                'condition' => array(),
                                                'x' => '',
                                                'y' => '');

            if( $tval['CASE_NO'] != "" ){
                $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['case']=$tval['CASE_NO'];

                //#587
                if( $tval['CASE_NO'] == "0" && $value['NODE_TYPE_ID'] == 11 ){
                    $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['case'] = "else";
                }

            }
            if($tval['LINE_NAME'] != "" )$arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['edge']=$tval['LINE_NAME'];
            $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['id']=$tval['TERMINAL_CLASS_NAME'];
            $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['targetNode']=$tval['CONNECTED_NODE_NAME'];
            if( $tval['TERMINAL_TYPE_ID'] == 1) $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['type']="in";
            if( $tval['TERMINAL_TYPE_ID'] == 2) $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['type']="out";

            if($tval['CONDITIONAL_ID'] != null ){
                $arrConditionalID = explode(',', $tval['CONDITIONAL_ID']);
                foreach ($arrConditionalID as $tckey => $tcvalue) {
                    $arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['condition'][]=$tcvalue;
                }
            }

            if($tval['POINT_X'] != "" )$arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['x']=$tval['POINT_X'];
            if($tval['POINT_Y'] != "" )$arr_json[$value['NODE_NAME']]['terminal'][$tval['TERMINAL_CLASS_NAME']]['y']=$tval['POINT_Y'];

            //TERMINAL、LINEカウンタの取得
            $tmpTerminalNumber  = intval( str_replace( "terminal-", "", $tval['TERMINAL_CLASS_NAME'] ));
            if( $intTerminalNumber < $tmpTerminalNumber )$intTerminalNumber=$tmpTerminalNumber;
            $tmpEdgeNumber  = intval( str_replace( "line-", "", $tval['LINE_NAME'] ));
            if( $intEdgeNumber < $tmpEdgeNumber )$intEdgeNumber=$tmpEdgeNumber;
        }
    }

    //LINE成形
    foreach ($arrNodeData as $key => $value) {
      foreach ($value['TERMINAL'] as $tkey => $tval) {
        //LINE生成
        $arr_json[$tval['LINE_NAME']]['id']=$tval['LINE_NAME'];
        $arr_json[$tval['LINE_NAME']]['type']="egde";
        if( $tval['TERMINAL_TYPE_ID'] == "1"  && $tval['LINE_NAME'] != "" ){
            $arr_json[$tval['LINE_NAME']]['inTerminal']=$tval['TERMINAL_CLASS_NAME'];
            $arr_json[$tval['LINE_NAME']]['outNode']=$tval['CONNECTED_NODE_NAME'];
        }elseif($tval['TERMINAL_TYPE_ID'] == "2"  && $tval['LINE_NAME'] != "" ){
            $arr_json[$tval['LINE_NAME']]['inNode']=$tval['CONNECTED_NODE_NAME'];
            $arr_json[$tval['LINE_NAME']]['outTerminal']=$tval['TERMINAL_CLASS_NAME'];
        }
      }
    }

    $intNodeNumber++;
    $intTerminalNumber++;
    $intEdgeNumber++;

    $arr_json['config']['editorVersion'] = '1.0.2';
    $arr_json['config']['nodeNumber'] = $intNodeNumber;
    $arr_json['config']['terminalNumber'] = $intTerminalNumber;
    $arr_json['config']['edgeNumber'] = $intEdgeNumber;

    //$arr_json[2] = array_values($arr_json[2]);
    //$arr_json[3] = array_values($arr_json[3]);
    //ksort($arr_json);

    $retArray = $arr_json;
    return $retArray;
}
/////////////////////////////////
// FILTER結果へMovement情報の追加  //
/////////////////////////////////
function filter_add($aryForResultData){

    // グローバル変数宣言
    global $g;

    // 本体ロジックをコール
    $objPtnID     = "";
    $objVarID     = "";
    $objChlVarID  = "";
    $objAssSeqID  = "";

    $tmparyForResultData = $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'];
    require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
    $objOLA = new OrchestratorLinkAgent($g['objMTS'],$g['objDBCA']);

    foreach ($tmparyForResultData as $key => $value) {
        //FILTER結果にのMOVEMENT情報追加
        if ( is_numeric($value[2]) ) {
            $tmpary=array();
            $tmpmovement = select_movment($g['objDBCA'], $g['objMTS'],$value[2], $objPtnID, $objVarID, $objChlVarID, $objAssSeqID);

            foreach ($tmpmovement as $key2 => $value2) {
                $i=0;
                foreach ($value2 as $key3 => $value3) {
                   if ( $key3 == "NEXT_PENDING_FLAG" && $value3 == 1 ) $value3 = "checkedValue";
                   if ( $key3 == "NEXT_PENDING_FLAG" && $value3 == 2 ) $value3 = "";
                   $tmpary[$key2][$i] = $value3;
                   $i++;
                }

            }
            $tmparyForResultData[$key][] = $tmpary;
        }else{
            //FILTER結果にのMOVEMENT情報説明追加
            $tmparyForResultData[$key][] = array(array(
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900100"), //"Orchestrator ID",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900101"), //"Movement ID",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900102"), //"一時停止(OFF:/ON:checkedValue)",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900103"), //"説明",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-900104")  //"オペレーションID(個別指定)",
            ));

        }
    }
    $aryForResultData[0]['ResultData']['resultdata']['CONTENTS']['BODY'] = $tmparyForResultData;

    return $aryForResultData;
}

/////////////////////////////////
// Symphonyクラスに紐づくMovement情報参照  //
/////////////////////////////////
function select_movment($objDBCA, $objMTS, $objPkey, &$objPtnID, &$objVarID, &$objChlVarID, &$objAssSeqID)
{
    // グローバル変数宣言
    global $g;
    $sql =        " SELECT                                      \n " ;
    $sql = $sql . "  ORCHESTRATOR_ID,                           \n " ;
    $sql = $sql . "  PATTERN_ID,                                \n " ;
    $sql = $sql . "  SKIP_FLAG,                                 \n " ;
    $sql = $sql . "  DESCRIPTION,                               \n " ;
    $sql = $sql . "  OPERATION_NO_IDBH                          \n " ;
    $sql = $sql . " FROM                                        \n " ;
    $sql = $sql . "  C_NODE_EDIT_CLASS_MNG                      \n " ;
    $sql = $sql . " WHERE                                       \n " ;
    $sql = $sql . "  CONDUCTOR_CLASS_NO = :CONDUCTOR_CLASS_NO   \n " ;
    $sql = $sql . " AND                                         \n " ;
    $sql = $sql . "   ORCHESTRATOR_ID IS NOT NULL               \n " ;
    $sql = $sql . " AND                                         \n " ;
    $sql = $sql . "   DISUSE_FLAG = 0                           \n " ;

    $objQuery = $objDBCA->sqlPrepare($sql);

    if($objQuery->getStatus()===false){
        web_log($objQuery->getLastError());
        return false;
    }
    $objQuery->sqlBind( array('CONDUCTOR_CLASS_NO'=>$objPkey));
    $r = $objQuery->sqlExecute($sql);

    if (!$r){
        web_log($objQuery->getLastError());

        unset($objQuery);
        return false;
    }
    // FETCH行数を取得
    $num_of_rows = $objQuery->effectedRowCount();

    // レコード無しの場合
    if( $num_of_rows < 1 ){
        unset($objQuery);
        return false;
    }
    $row = $objQuery->resultFetchALL();

    return $row;
}

function chkJson($string) {
    return ( (is_string($string) && (is_object(json_decode($string)) || is_array(json_decode($string))))) ? true : false;
}


?>
