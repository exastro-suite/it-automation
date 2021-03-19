<?php
//   Copyright 2021 NEC Corporation
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

//比較定義用のリスト取得
function getContrastList($mode=""){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strExpectedErrMsgBodyForUI = "";
    $strStreamOfContrastList = "";
    // 各種ローカル変数を定義

    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{

        //比較定義取得
        if( $mode == "" ){
            $strQuery = "SELECT  "
                       ." TAB_A.* ,"
                       ." TAB_A.CONTRAST_NAME AS PULLDOWN "
                       ."FROM "
                       ." A_CONTRAST_LIST TAB_A "
                       ."WHERE "
                       ." TAB_A.DISUSE_FLAG IN ('0') "
                       ." ORDER BY TAB_A.CONTRAST_LIST_ID ";   
        }else{    
            $strQuery = "SELECT  "
                       ." TAB_A.* ,"
                       ." TAB_B.MENU_NAME AS CONTRAST_MENU_NAME_1 ,"
                       ." TAB_C.MENU_NAME AS CONTRAST_MENU_NAME_2 ,"
                       ." concat( TAB_A.CONTRAST_LIST_ID ,':', TAB_A.CONTRAST_NAME ,' 【 ' ,TAB_A.CONTRAST_MENU_ID_1 ,':', TAB_B.MENU_NAME ,' - ', TAB_A.CONTRAST_MENU_ID_2 ,':', TAB_C.MENU_NAME ,' 】 ') AS PULLDOWN "
                       ."FROM "
                       ." A_CONTRAST_LIST TAB_A"
                       ." LEFT JOIN A_MENU_LIST TAB_B ON ( TAB_B.MENU_ID = TAB_A.CONTRAST_MENU_ID_1 ) "
                       ." LEFT JOIN A_MENU_LIST TAB_C ON ( TAB_C.MENU_ID = TAB_A.CONTRAST_MENU_ID_2 ) "
                       ."WHERE "
                       ." TAB_A.DISUSE_FLAG IN ('0') "
                       ." ORDER BY TAB_A.CONTRAST_LIST_ID ";            
        }

        $bindkeyVlaue= array();
        $aryRetBody = execsql($strQuery,$bindkeyVlaue);

        if( $aryRetBody[0] == null ){
            //アクセス権
            $objRBAC = new RoleBasedAccessControl($g['objDBCA']);
            $ret  = $objRBAC->getAccountInfo($g['login_id']);
            foreach ($aryRetBody as $key => $targetRow) {
                list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                if($ret === false) {
                    // 例外処理へ
                    $strErrStepIdInFx="00000100";
                    $intErrorType = 1; //システムエラー
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                } else {
                    if($permission !== true) {
                        //アクセス権限を持っていない場合
                        unset($aryRetBody[$key]);
                    }
                }
            }
        }

        $strStreamOfContrastList = $aryRetBody;
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfContrastList,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;

}

//比較定義用のリスト取得
function gethtmlContrastList($mode=""){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strExpectedErrMsgBodyForUI = "";
    $strStreamOfContrastList = "";
    // 各種ローカル変数を定義
    
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{

        //比較定義取得
        $aryRetBody =  getContrastList(1);

        if( $aryRetBody[0] == null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[3];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        //SELECT用のOPTION作成
        $tmpSelectOpt="";
        if( $aryRetBody[2] != array() ){
            foreach ($aryRetBody[2] as $key => $value) {
                if( isset($value['CONTRAST_LIST_ID']) === true ){
                    $tmpcontrast_id=htmlspecialchars($value['CONTRAST_LIST_ID'], ENT_QUOTES, 'UTF-8', false);
                    $tmpcontrast_name=htmlspecialchars($value['PULLDOWN'], ENT_QUOTES, 'UTF-8', false);
                    $selected="";
                    $tmpSelectOpt= $tmpSelectOpt . '<option value=' . $tmpcontrast_id ." ". $selected ." " .'>' . $tmpcontrast_name . '</option>';                    
                }
            }            
        }
        $strStreamOfContrastList = $tmpSelectOpt;
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfContrastList,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
    
}

//ホスト一覧の取得（対象メニューからホスト抽出）
function gethostList( $intContrastid ){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    $strStreamOfContrastList = "";
    // 各種ローカル変数を定義
    #/*
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        //比較定義からメニューID取得
        $strQuery = "SELECT * "
                   ."FROM "
                   ." A_CONTRAST_LIST "
                   ."WHERE "
                   ." DISUSE_FLAG IN ('0') "
                   ." AND CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                   ."ORDER BY CONTRAST_LIST_ID";
        $bindkeyVlaue = array(
            "CONTRAST_LIST_ID" => $intContrastid,
        );
        $aryRetBody = execsql($strQuery,$bindkeyVlaue);
        $contrastDate = $aryRetBody;
        if( $aryRetBody[0] == null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[3];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        //対象メニューのテーブル名取得
        $strQuery = "SELECT * "
                    ."FROM "
                    ." F_MENU_TABLE_LINK TAB_A "
                    ."WHERE "
                    ." TAB_A.DISUSE_FLAG IN ('0') "
                    ." AND TAB_A.MENU_ID = :MENU_ID "
                    ."ORDER BY TAB_A.MENU_TABLE_LINK_ID"
                    ."";
        //メニュー１取得
        $strMenuIDNumeric=$contrastDate[0]['CONTRAST_MENU_ID_1'];
        $bindkeyVlaue = array(
            "MENU_ID" => $strMenuIDNumeric,
        );
        $aryRetBody = execsql($strQuery,$bindkeyVlaue);
        $arrMenuColInfo1 = $aryRetBody;

        if( $aryRetBody[0] == null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[3];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        //メニュー2取得
        $strMenuIDNumeric=$contrastDate[0]['CONTRAST_MENU_ID_2'];
        $bindkeyVlaue = array(
            "MENU_ID" => $strMenuIDNumeric,
        );
        $aryRetBody = execsql($strQuery,$bindkeyVlaue);
        $arrMenuColInfo2 = $aryRetBody;

        if( $aryRetBody[0] == null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[3];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $tmptableList=array();
        foreach ($arrMenuColInfo1 as $key => $arrinfo ){
            if( isset($arrMenuColInfo1[$key]['TABLE_NAME']) === true){
                $tmptableList[]=$arrMenuColInfo1[$key]['TABLE_NAME'];
                $tmptableList[]=$arrMenuColInfo2[$key]['TABLE_NAME'];                
            }
        }
        $tmptableList = array_unique($tmptableList);

        //メニューからホストの一覧抽出
        $tmpRetBody=array();
        foreach ($tmptableList as $tmpTablename ) {
            $strQuery = "SELECT DISTINCT "
                       ." TAB_B.SYSTEM_ID AS SYSTEM_ID ,"      
                       ." TAB_B.HOSTNAME AS HOSTNAME ,"
                       ." TAB_B.ACCESS_AUTH AS ACCESS_AUTH "
                       ."FROM "
                       ." {$tmpTablename} TAB_A "
                       ."LEFT JOIN "
                       ." C_STM_LIST TAB_B ON ( TAB_B.SYSTEM_ID = TAB_A.HOST_ID) "
                       ."WHERE "
                       ." TAB_A.DISUSE_FLAG IN ('0') "
                       ."AND "
                       ." TAB_B.DISUSE_FLAG IN ('0') "
                       ." ORDER BY TAB_B.SYSTEM_ID "
                       ."";
            $bindkeyVlaue = array();
            $aryRetBody = execsql($strQuery,$bindkeyVlaue);
            $tmpRetBody[$tmpTablename] = $aryRetBody;
            if( $aryRetBody[0] == null ){
                // 例外処理へ
                $strErrStepIdInFx="00001000";
                $intErrorType = $aryRetBody[1];
                if( $intErrorType == 2 || $intErrorType == 3 ){
                    $strExpectedErrMsgBodyForUI = $aryRetBody[3];
                }
                throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
            }

        }
        //ホスト一覧整形
        $tmpRet=array();
        foreach ($tmpRetBody as $tmpTablename => $tmparr ) {
            foreach ($tmparr as $key => $arrHost) {
                if( isset($arrHost['SYSTEM_ID']) === true ) {
                    $intsystemid=$arrHost['SYSTEM_ID'];
                    if( isset( $tmpRet[$intsystemid] ) === false ){
                        $tmpRet[$intsystemid] =array(
                            'SYSTEM_ID'    => $arrHost['SYSTEM_ID'],
                            'HOSTNAME'     => $arrHost['HOSTNAME'],
                            'ACCESS_AUTH'  => $arrHost['ACCESS_AUTH'],
                        );                
                    }
                }
            }
        }

        $aryRetBody = array_values($tmpRet);

        //アクセス権
        $objRBAC = new RoleBasedAccessControl($g['objDBCA']);
        $ret  = $objRBAC->getAccountInfo($g['login_id']);
        foreach ($aryRetBody as $key => $targetRow) {
            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
            if($ret === false) {
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                $intErrorType = 1; //システムエラー
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } else {
                if($permission !== true) {
                    //アクセス権限を持っていない場合
                    unset($aryRetBody[$key]);
                }
            }
        }
        $tmpRetBody= array();
        foreach ($aryRetBody as $key => $value) {
            $tmpRetBody[]=array(
                'SYSTEM_ID' => $value['SYSTEM_ID'],
                'HOSTNAME' => $value['HOSTNAME'],
                'DEFAULT' => "",
            );
            
        }
        $strStreamOfContrastList = json_encode($tmpRetBody);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfContrastList,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;

}

//比較結果出力　
function getContrastResult($strContrastListID,$arrBasetime1="",$arrBasetime2="",$strhostlist="" ){
    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strExpectedErrMsgBodyForUI = "";

    // 各種ローカル変数を定義
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);

    $baseParm = array(
        "CONTRAST_LIST_ID" => "",
        "BASE_TIMESTAMP" => array(),
        "HOST_LIST" => array()
    );

    $arrConfigForCMDBbaseIUD = array(
        "ROW_ID" => "",
        "HOST_ID" => "",
        "OPERATION_ID_DISP" => "",
        "OPERATION_ID_NAME_DISP" => "",
        "OPERATION_ID" => "",
        "BASE_TIMESTAMP" => "",
        "LAST_EXECUTE_TIMESTAMP" => "",
        "OPERATION_NAME" => "",
        "OPERATION_DATE" => "",
        "ACCESS_AUTH" => "",
        "NOTE" => "",
        "DISUSE_FLAG" => "",
        "LAST_UPDATE_TIMESTAMP" => "",
        "LAST_UPDATE_USER" => "",
    );

    // 処理開始
    try{

        $baseParm['CONTRAST_LIST_ID'] = $strContrastListID;    
        $baseParm['BASE_TIMESTAMP'] = array($arrBasetime1,$arrBasetime2);
        $arrHostList= explode( ",", $strhostlist );  
        $baseParm['HOST_LIST'] = $arrHostList;

        //比較定義取得
        $strQuery = "SELECT * "
                   ."FROM "
                   ." A_CONTRAST_LIST "
                   ."WHERE "
                   ." DISUSE_FLAG IN ('0') "
                   ." AND CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                   ."ORDER BY CONTRAST_LIST_ID";
        $bindkeyVlaue = array(
            "CONTRAST_LIST_ID" => $strContrastListID,
        );
        $aryRetBody = execsql($strQuery,$bindkeyVlaue);
        $contrastDate = $aryRetBody;

        if( $aryRetBody[0] == null ){
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            $intErrorType = $aryRetBody[1];
            if( $intErrorType == 2 || $intErrorType == 3 ){
                $strExpectedErrMsgBodyForUI = $aryRetBody[3];
            }
            throw new Exception( $strErrStepIdInFx . '-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }

        $arrFileUploadColumnFlg=array();

        //全件一致フラグあり
        if( $contrastDate[0]['ALL_MATCH_FLG'] == 1 ){

            $strQuery = "SELECT "
                        ." TAB_A.* , "
                        ." TAB_B.* , "
                        ." TAB_C.* "
                        ."FROM "
                        ." D_CMDB_MG_MU_COL_LIST TAB_A "
                        ."LEFT JOIN "
                        ." B_CMDB_MENU_COLUMN TAB_B ON ( TAB_A.COLUMN_LIST_ID = TAB_B.COLUMN_LIST_ID) "
                        ."LEFT JOIN "
                        ." F_MENU_TABLE_LINK TAB_C ON  ( TAB_A.MENU_ID = TAB_C.MENU_ID) "
                        ."WHERE "
                        ." TAB_A.DISUSE_FLAG IN ('0') "
                        ." AND TAB_A.MENU_ID = :MENU_ID "
                        ." AND TAB_A.SHEET_TYPE IN ('1','4') "
                        ." AND TAB_B.COL_CLASS   <>  'PasswordColumn' "
                        ."ORDER BY TAB_A.COLUMN_LIST_ID"
                        ."";
            //メニュー１取得
            $strMenuIDNumeric=$contrastDate[0]['CONTRAST_MENU_ID_1'];
            $bindkeyVlaue = array(
                "MENU_ID" => $strMenuIDNumeric,
            );
            $tmpMenuColInfo = execsql($strQuery,$bindkeyVlaue);
            $arrMenuColInfo1 = $tmpMenuColInfo;

            //メニュー2取得
            $strMenuIDNumeric=$contrastDate[0]['CONTRAST_MENU_ID_2'];
            $bindkeyVlaue = array(
                "MENU_ID" => $strMenuIDNumeric,
            );
            $tmpMenuColInfo = execsql($strQuery,$bindkeyVlaue);
            $arrMenuColInfo2 = $tmpMenuColInfo;

            //比較メニュー
            $baseParm['DATA'] = array($arrMenuColInfo1,$arrMenuColInfo2);
                # array(  0 => 比較データ1 ,   1 =>　比較データ2 )

            //項目全件一致項目数チェック
            $intallMatchflg="";
            if( count($arrMenuColInfo1) != count($arrMenuColInfo2) ){
                $intallMatchflg=1;
            }
            
            //項目全件一致項目名チェック
            foreach ($arrMenuColInfo1 as $key => $arrinfo ) {
                if( $arrMenuColInfo1[$key]['COL_TITLE'] != $arrMenuColInfo2[$key]['COL_TITLE'] ){
                    $intallMatchflg=1;
                }
            }
            if( $intallMatchflg == 1 ){
                $strStreamOfContrastResult=array(array(),array());
                $strResultCode = sprintf("%03d", "");
                $strDetailCode = sprintf("%03d", "");
                $arrayResult = array($strResultCode,
                                     $strDetailCode,
                                     $strStreamOfContrastResult,
                                     $strExpectedErrMsgBodyForUI
                                     );
                dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
                return $arrayResult;
            }
            
            //対象データ、対象メニュー、対象カラム、対象タイトル　リスト整理
            $arrContrastData = array();    # array( メニューID => array( 項目名 array( 0 => カラム名　, 1 => カラム型 ) ) )
            $arrContrastColList = array(); # array( 項目名 => array( メニューID => カラム型 ) )
            $arrContrastColOriList = array(); # array(  カラム名 => array( メニューID => 項目名 ) )

            $arrContrastList = array();    # array( メニューID => テーブル名 )
            $arrContrastTableList = array();
                /* array( 0 =>  array ( メニューID => テーブル名 ) , #比較データ1
                      1 =>  array ( メニューID => テーブル名 )   #比較データ2
                    )*/
            $arrContrastTitleList = array();   # array( 項目名 , ...... )
            $arrContrastMenuList = array(
                0 => $contrastDate[0]['CONTRAST_MENU_ID_1'],
                1 => $contrastDate[0]['CONTRAST_MENU_ID_2']
            );    # array(  0 => メニューID  ,   1 => メニューID  )

            //対象メニュー情報から比較用リスト作成
            foreach ( $baseParm['DATA'] as $tmpMenuColInfo ) {
                foreach ($tmpMenuColInfo as $key => $value) {
                    $tmpMenuId =  $value['MENU_ID'];
                    $tmpColName =  $value['COL_NAME'];
                    $tmpColClass =  $value['COL_CLASS'];
                    $tmpColTitle =  $value['COL_TITLE'];
                    $tmpRefTable   =  $value['REF_TABLE_NAME'];
                    $tmpRefPKey =  $value['REF_PKEY_NAME'];
                    $tmpRefColName =  $value['REF_COL_NAME'];

                    $tmpTableName =  str_replace("F_KY_AUTO_TABLE_", "G_KY_AUTO_TABLE_", $value['TABLE_NAME']);
                    $arrContrastData[$tmpMenuId][$tmpColTitle]= array( $tmpColName ,$tmpColClass,$tmpRefTable,$tmpRefPKey,$tmpRefColName);
                    $arrContrastColList[$tmpColTitle][$tmpMenuId] = $tmpColName;
                                
                    $arrContrastColOriList[$tmpColName][$tmpMenuId] = $tmpColTitle;
                    $arrContrastList[$tmpMenuId]=$tmpTableName;
                    if( array_search($tmpColTitle,$arrContrastTitleList) === false )$arrContrastTitleList[]=$tmpColTitle;
                }
                $arrContrastTableList[]=array( $tmpMenuId => $tmpTableName ) ;
            }

            //データ取得（パラメータシート）
            $arrContrastResult = array();
            $tmpContrastResult = array();
            $arrContrastResultALL =   array();
            $tmpBase=array();
            $tmphostList = array();

            $arrBasetime = $baseParm['BASE_TIMESTAMP'];

            foreach ($arrContrastTableList as $intcnt => $tmpContrastList) {
                foreach ($tmpContrastList as $tmpMenuId => $tmpTableName) {
                    $strbasetime = "";
                    $strhostlist = "";
                    //基準値
                    if( isset( $baseParm['BASE_TIMESTAMP'][$intcnt] ) ){
                        if( $baseParm['BASE_TIMESTAMP'][$intcnt] != "" )$strbasetime = $baseParm['BASE_TIMESTAMP'][$intcnt];
                    }

                    //ホスト
                    if( isset( $baseParm['HOST_LIST'] ) && $baseParm['HOST_LIST'] !="" ){
                        $strhostlist = implode( ",", $baseParm['HOST_LIST'] );
                    }

                    $bindkeyVlaue = array();  
                    $strQuery = "SELECT  ";
                    foreach ($arrConfigForCMDBbaseIUD as $tmpcolname => $tmpval) {
                        
                        if( $tmpcolname == "BASE_TIMESTAMP" ){
                            $strQuery = $strQuery ." DATE_FORMAT( TAB_A.BASE_TIMESTAMP ,'%Y/%m/%d %H:%i') AS BASE_TIMESTAMP ,";
                        }else{
                            $strQuery = $strQuery ." TAB_A.$tmpcolname ,";
                        }
                        
                    }
                    foreach ($arrContrastColList as $tmparr) {
                            $tmpcolname = $tmparr[$tmpMenuId];
                            $strQuery = $strQuery ." TAB_A.$tmpcolname ,";
                    }
                    $strQuery = $strQuery ." TAB_B.HOSTNAME ";

                    $tmpnum=0;
                    foreach ($arrContrastData[$tmpMenuId] as $tmpColTitle => $tmpcolinfo) {
                        if( $tmpcolinfo[1] == "FileUploadColumn"  ){
                            $tmpcolname = $tmpcolinfo[0];
                            $tmpRefTable = $tmpcolinfo[2] ;
                            $tmpRefPKey = $tmpcolinfo[3] ;
                            $tmpRefColName = $tmpcolinfo[4] ;
                            $arrFileUploadColumnFlg[$intcnt][$tmpTableName]=$strbasetime;
                        }
                    }
                    

                    $strQuery = $strQuery ." FROM ${tmpTableName} TAB_A "
                               ." LEFT JOIN C_STM_LIST TAB_B ON ( TAB_B.SYSTEM_ID = TAB_A.HOST_ID ) ";
                    
                    $strQuery = $strQuery ."WHERE "
                               ." TAB_A.DISUSE_FLAG = 0 ";

                    //基準日あり
                    if( $strbasetime != "" ){
                        $strQuery = $strQuery
                                ." AND  "
                                ." TAB_A.BASE_TIMESTAMP < cast( :BASE_TIMESTAMP as datetime(6) ) "
                               ."";
                        $bindkeyVlaue = array(
                            "BASE_TIMESTAMP" => $strbasetime,
                        );
                    }

                    //ホスト指定あり
                    if( $strhostlist != "" ){
                        $strQuery = $strQuery
                                ." AND  "
                                ." TAB_A.HOST_ID IN (".$strhostlist .") "
                               ."";
                    }
                    
                    $strQuery = $strQuery. "GROUP BY TAB_A.BASE_TIMESTAMP , TAB_A.HOST_ID  ORDER BY TAB_A.BASE_TIMESTAMP DESC ,TAB_A.HOST_ID ASC";

                    $tmpContrastResult = execsql($strQuery,$bindkeyVlaue);

                    //アクセス権
                    $objRBAC = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $objRBAC->getAccountInfo($g['login_id']);
                    foreach ($tmpContrastResult as $key => $targetRow) {
                        if( isset($targetRow['ROW_ID']) === true ){
                            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                            if($ret === false) {
                                // 例外処理へ
                                $strErrStepIdInFx="00000100";
                                $intErrorType = 1; //システムエラー
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            } else {
                                if($permission !== true) {
                                    //アクセス権限を持っていない場合
                                    unset($tmpContrastResult[$key]);
                                }

                            } 
                        }
                    }

                    $tmpDate = $tmpContrastResult[0];
                    //テンプレート生成
                    if( is_array($tmpDate)  ) {
                        if( count($tmpDate) != count($tmpBase) ){
                            foreach ($tmpDate as $tmpkey => $tmpvalue) {
                                $tmpBase[$intcnt][$tmpkey] = NULL;#"---";
                            }
                        }                        
                    }

                    //整形
                    if( is_array($tmpDate) ) {
                        $tmphostidList=array();
                        foreach ($tmpContrastResult as $tmpContrastRow) {
                            $tmpHOSTID = $tmpContrastRow['HOST_ID'];
                            $tmphostmenuList[$tmpHOSTID]=$tmpMenuId;
                            if( array_search( $tmpHOSTID,$tmphostList ) === false  )$tmphostList[]=$tmpHOSTID;
                            //比較情報全データ

                              //配列準備
                                if( array_key_exists($intcnt, $arrContrastResultALL ) === false  ){
                                    $arrContrastResultALL[$intcnt]=array();
                                }elseif( array_key_exists($tmpHOSTID, $arrContrastResultALL[$intcnt] ) === false  ){
                                    $arrContrastResultALL[$intcnt][$tmpHOSTID]=array();
                                }elseif( array_key_exists($tmpMenuId, $arrContrastResultALL[$intcnt][$tmpHOSTID] ) === false  ){
                                    if( count( $arrContrastResultALL[$intcnt][$tmpHOSTID] ) == 0 )$arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }elseif( count( $arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId] ) == 0 ){
                                   $arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }
                                if( isset($arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId] ) == false ){
                                    $arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId] = $tmpContrastRow;
                                }

                            //比較情報カラムのみ
                                //配列準備
                                if( array_key_exists($intcnt, $arrContrastResult ) === false  ){
                                    $arrContrastResult[$intcnt]=array();
                                }elseif( array_key_exists($tmpHOSTID, $arrContrastResult[$intcnt] ) === false  ){
                                    $arrContrastResult[$intcnt][$tmpHOSTID]=array();
                                }elseif( array_key_exists($tmpMenuId, $arrContrastResult[$intcnt][$tmpHOSTID] ) === false  ){
                                    if( count( $arrContrastResult[$intcnt][$tmpHOSTID] ) == 0 )$arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }elseif( count( $arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId] ) == 0 ){
                                   $arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }
                                //比較対象以外除外
                                foreach ($tmpContrastRow as $tmpkey => $tmpvalue) {
                                    if( strpos( $tmpkey,'KY_AUTO_COL' ) === false ) unset( $tmpContrastRow[$tmpkey] );
                                }
                                if( isset($arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId] ) == false ){
                                    $arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId] = $tmpContrastRow;
                                }
                        }
                    }
                }
            }
        }elseif( $contrastDate[0]['ALL_MATCH_FLG'] == "" ){
        //比較定義詳細あり
            $strQuery = "SELECT "
                        ." TAB_A.* , "
                        ." TAB_B.MENU_ID AS CONTRAST_MENU_ID_1 ,"
                        ." TAB_C.MENU_ID AS CONTRAST_MENU_ID_2 ,"
                        ." TAB_B.COL_NAME AS CONTRAST_MENU_ID_1 ,"
                        ." TAB_C.COL_NAME AS CONTRAST_MENU_ID_2 ,"
                        ." TAB_B.MENU_ID AS CONTRAST_MENU_ID_1 ,"
                        ." TAB_C.MENU_ID AS CONTRAST_MENU_ID_2 ,"
                        ." TAB_B.COL_TITLE AS CONTRAST_COL_TITLE_1 ,"
                        ." TAB_C.COL_TITLE AS CONTRAST_COL_TITLE_2 ,"
                        ." TAB_B.COL_NAME AS CONTRAST_COL_NAME_1 ,"
                        ." TAB_C.COL_NAME AS CONTRAST_COL_NAME_2 ,"
                        ." TAB_B.COL_CLASS AS CONTRAST_COL_CLASS_1 ,"
                        ." TAB_C.COL_CLASS AS CONTRAST_COL_CLASS_2 ,"
                        ." TAB_D.TABLE_NAME AS CONTRAST_TABLE_NAME_1 ,"
                        ." TAB_E.TABLE_NAME AS CONTRAST_TABLE_NAME_2 ,"
                        ." TAB_B.REF_TABLE_NAME AS REF_TABLE_NAME_1 ,"
                        ." TAB_C.REF_TABLE_NAME AS REF_TABLE_NAME_2 ,"
                        ." TAB_B.REF_PKEY_NAME AS REF_PKEY_NAME_1 ,"
                        ." TAB_C.REF_PKEY_NAME AS REF_PKEY_NAME_2 ,"
                        ." TAB_B.REF_COL_NAME AS REF_COL_NAME_1 ,"
                        ." TAB_C.REF_COL_NAME AS REF_COL_NAME_2 "
                        ."FROM "
                        ." A_CONTRAST_DETAIL TAB_A "
                        ."LEFT JOIN "
                        ." B_CMDB_MENU_COLUMN TAB_B ON ( TAB_A.CONTRAST_COL_ID_1 = TAB_B.COLUMN_LIST_ID) "
                        ."LEFT JOIN "
                        ." B_CMDB_MENU_COLUMN TAB_C ON ( TAB_A.CONTRAST_COL_ID_2 = TAB_C.COLUMN_LIST_ID) "
                        ."LEFT JOIN "
                        ." F_MENU_TABLE_LINK TAB_D ON  ( TAB_B.MENU_ID = TAB_D.MENU_ID) "
                        ."LEFT JOIN "
                        ." F_MENU_TABLE_LINK TAB_E ON  ( TAB_C.MENU_ID = TAB_E.MENU_ID) "
                        ."WHERE "
                        ." TAB_A.DISUSE_FLAG IN ('0') "
                        ." AND TAB_A.CONTRAST_LIST_ID = :CONTRAST_LIST_ID "
                        ." AND TAB_B.COL_CLASS   <>  'PasswordColumn' "
                        ." AND TAB_C.COL_CLASS   <>  'PasswordColumn' "
                        ."ORDER BY TAB_A.DISP_SEQ ,TAB_A.CONTRAST_DETAIL_ID"
                        ."";
            //メニュー１取得
            $strMenuIDNumeric=$contrastDate[0]['CONTRAST_LIST_ID'];
            $bindkeyVlaue = array(
                "CONTRAST_LIST_ID" => $strMenuIDNumeric,
            );
            $tmpContrastDetail = execsql($strQuery,$bindkeyVlaue);
            $arrContrastDetail = $tmpContrastDetail;
            
            if( is_array($tmpContrastDetail[0]) !== true ){
                $strStreamOfContrastResult=array(array(),array());
                $strResultCode = sprintf("%03d", "");
                $strDetailCode = sprintf("%03d", "");
                $arrayResult = array($strResultCode,
                                     $strDetailCode,
                                     $strStreamOfContrastResult,
                                     $strExpectedErrMsgBodyForUI
                                     );
                dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
                
                return $arrayResult;
            } 

            $arrMenuColInfo1=array();
            $arrMenuColInfo2=array();
            foreach ($arrContrastDetail as  $tmpContrastDetail) {
                $tmpColTitle   = $tmpContrastDetail['CONTRAST_COL_TITLE'];
                $tmpColMenuid1 = $tmpContrastDetail['CONTRAST_MENU_ID_1'];
                $tmpColMenuid2 = $tmpContrastDetail['CONTRAST_MENU_ID_2'];
                $tmpColName1   = $tmpContrastDetail['CONTRAST_COL_NAME_1'];
                $tmpColName2   = $tmpContrastDetail['CONTRAST_COL_NAME_2'];
                $tmpColTitle1   = $tmpContrastDetail['CONTRAST_COL_TITLE_1'];
                $tmpColTitle2   = $tmpContrastDetail['CONTRAST_COL_TITLE_2'];
                $tmpColclass1  = $tmpContrastDetail['CONTRAST_COL_CLASS_1'];
                $tmpColclass2  = $tmpContrastDetail['CONTRAST_COL_CLASS_2'];
                $tmpTablename1  = $tmpContrastDetail['CONTRAST_TABLE_NAME_1'];
                $tmpTablename2  = $tmpContrastDetail['CONTRAST_TABLE_NAME_2'];
                $tmpRefTable1   = $tmpContrastDetail['REF_TABLE_NAME_1'];
                $tmpRefTable2   = $tmpContrastDetail['REF_TABLE_NAME_2'];
                $tmpRefPKey1  = $tmpContrastDetail['REF_PKEY_NAME_1'];
                $tmpRefPKey2  = $tmpContrastDetail['REF_PKEY_NAME_2'];
                $tmpRefColName1  = $tmpContrastDetail['REF_COL_NAME_1'];
                $tmpRefColName2  = $tmpContrastDetail['REF_COL_NAME_2'];

                $arrMenuColInfo1[] = array(
                    "COL_TITLE" => $tmpColTitle   ,
                    "MENU_ID" => $tmpColMenuid1 ,
                    "COL_NAME" => $tmpColName1   ,
                    "COL_CLASS" => $tmpColclass1  ,
                    "TABLE_NAME" => $tmpTablename1 ,
                    "COL_TITLE_ORI" => $tmpColTitle1 ,
                    "REF_TABLE_NAME" => $tmpRefTable1  ,
                    "REF_PKEY_NAME" => $tmpRefPKey1 ,
                    "REF_COL_NAME" => $tmpRefColName1 ,
                    );
                $arrMenuColInfo2[] = array(
                    "COL_TITLE" => $tmpColTitle   ,
                    "MENU_ID" => $tmpColMenuid2 ,
                    "COL_NAME" => $tmpColName2   ,
                    "COL_CLASS" => $tmpColclass2  ,
                    "TABLE_NAME" => $tmpTablename2 ,
                    "COL_TITLE_ORI" => $tmpColTitle2 ,
                    "REF_TABLE_NAME" => $tmpRefTable2  ,
                    "REF_PKEY_NAME" => $tmpRefPKey2 ,
                    "REF_COL_NAME" => $tmpRefColName2 ,
                    );
            }

            //比較メニュー
            $baseParm['DATA'] = array($arrMenuColInfo1,$arrMenuColInfo2);
                # array(  0 => 比較データ1 ,   1 =>　比較データ2 )

            //対象データ、対象メニュー、対象カラム、対象タイトル　リスト整理
            $arrContrastData = array();    # array( メニューID => array( 項目名 array( 0 => カラム名　, 1 => カラム型 ) ) )
            $arrContrastColList = array(); # array( 項目名 => array( メニューID => カラム型 ) )
            $arrContrastColOriList = array(); # array(  カラム名 => array( メニューID => 項目名 ) )
            $arrContrastList = array();    # array( メニューID => テーブル名 )
            $arrContrastTableList = array();
                /* array( 0 =>  array ( メニューID => テーブル名 ) , #比較データ1
                      1 =>  array ( メニューID => テーブル名 )   #比較データ2
                    )*/
            $arrContrastTitleList = array();   # array( 項目名 , ...... )
            $arrContrastMenuList = array(
                0 => $contrastDate[0]['CONTRAST_MENU_ID_1'],
                1 => $contrastDate[0]['CONTRAST_MENU_ID_2']
            );    # array(  0 => メニューID  ,   1 => メニューID  )


            //対象メニュー情報から比較用リスト作成
            foreach ( $baseParm['DATA'] as $intcnt => $tmpMenuColInfo ) {
                foreach ($tmpMenuColInfo as $key => $value) {
                    $tmpMenuId =  $value['MENU_ID'];
                    $tmpColName =  $value['COL_NAME'];
                    $tmpColClass =  $value['COL_CLASS'];
                    $tmpColTitle =  $value['COL_TITLE'];
                    $tmpColOriTitle =  $value['COL_TITLE_ORI'];
                    $tmpRefTable   =  $value['REF_TABLE_NAME'];
                    $tmpRefPKey =  $value['REF_PKEY_NAME'];
                    $tmpRefColName =  $value['REF_COL_NAME'];

                    $tmpTableName =  str_replace("F_KY_AUTO_TABLE_", "G_KY_AUTO_TABLE_", $value['TABLE_NAME']);
            
                    $arrContrastData[$tmpMenuId][$tmpColTitle]= array( $tmpColName ,$tmpColClass,$tmpRefTable,$tmpRefPKey,$tmpRefColName);

                    $arrContrastColList[$tmpColTitle][$tmpMenuId] = $tmpColName;
                    $arrContrastColOriList[$tmpColName][$tmpMenuId] = $tmpColTitle;
                    
                    if( $arrContrastMenuList[$intcnt] != $tmpMenuId  ){
                        exit;
                    }

                    $arrContrastList[$tmpMenuId]=$tmpTableName;
                    if( array_search($tmpColTitle,$arrContrastTitleList) === false )$arrContrastTitleList[]=$tmpColTitle;
                }
                $arrContrastTableList[]=array( $tmpMenuId => $tmpTableName ) ;
            }

            //データ取得（パラメータシート）
            $arrContrastResult = array();
            $tmpContrastResult = array();
            $arrContrastResultALL =   array();
            $tmpBase=array();
            $tmphostList =   array();

            $arrBasetime = $baseParm['BASE_TIMESTAMP'];

            foreach ($arrContrastTableList as $intcnt => $tmpContrastList) {
                foreach ($tmpContrastList as $tmpMenuId => $tmpTableName) {
                    $strbasetime = "";
                    $strhostlist = "";
                    //基準値
                    if( isset( $baseParm['BASE_TIMESTAMP'][$intcnt] ) ){
                        if( $baseParm['BASE_TIMESTAMP'][$intcnt] != "" )$strbasetime = $baseParm['BASE_TIMESTAMP'][$intcnt];
                    }

                    //ホスト
                    if( isset( $baseParm['HOST_LIST'] ) && $baseParm['HOST_LIST'] !="" ){
                        $strhostlist = implode( ",", $baseParm['HOST_LIST'] );
                    }

                    //ベース
                    $bindkeyVlaue = array();  
                    $strQuery = "SELECT  ";
                    foreach ($arrConfigForCMDBbaseIUD as $tmpcolname => $tmpval) {
                        if( $tmpcolname == "BASE_TIMESTAMP" ){
                            $strQuery = $strQuery ." DATE_FORMAT( TAB_A.BASE_TIMESTAMP ,'%Y/%m/%d %H:%i') AS BASE_TIMESTAMP ,";
                        }else{
                            $strQuery = $strQuery ." TAB_A.$tmpcolname ,";
                        }
                    }
                    foreach ($arrContrastColList as $tmparr) {
                        $tmpcolname = $tmparr[$tmpMenuId];
                        $strQuery = $strQuery ." TAB_A.$tmpcolname ,";

                    }
                    $strQuery = $strQuery ." TAB_B.HOSTNAME ";

                    $tmpnum=0;
                    foreach ($arrContrastData[$tmpMenuId] as $tmpColTitle => $tmpcolinfo) {
                        if( $tmpcolinfo[1] == "FileUploadColumn"  ){
                            $tmpcolname = $tmpcolinfo[0];
                            $tmpRefTable = $tmpcolinfo[2] ;
                            $tmpRefPKey = $tmpcolinfo[3] ;
                            $tmpRefColName = $tmpcolinfo[4] ;
                            $arrFileUploadColumnFlg[$intcnt][$tmpTableName]=$strbasetime;
                        }
                    }

                    $strQuery = $strQuery ." FROM ${tmpTableName} TAB_A "
                               ." LEFT JOIN C_STM_LIST TAB_B ON ( TAB_B.SYSTEM_ID = TAB_A.HOST_ID ) ";

                    $strQuery = $strQuery ."WHERE "
                               ." TAB_A.DISUSE_FLAG = 0 ";

                    //基準日あり
                    if( $strbasetime != "" ){
                        $strQuery = $strQuery
                                ." AND  "
                                ." TAB_A.BASE_TIMESTAMP < cast( :BASE_TIMESTAMP as datetime(6) ) "
                               ."";
                        $bindkeyVlaue = array(
                            "BASE_TIMESTAMP" => $strbasetime,
                        );
                    }

                    //ホスト指定あり
                    if( $strhostlist != "" ){
                        $strQuery = $strQuery
                                ." AND  "
                                ." TAB_A.HOST_ID IN (".$strhostlist .") "
                               ."";
                    }
                    
                    $strQuery = $strQuery. "GROUP BY TAB_A.BASE_TIMESTAMP , TAB_A.HOST_ID  ORDER BY TAB_A.BASE_TIMESTAMP DESC ,TAB_A.HOST_ID ASC";

                    $tmpContrastResult = execsql($strQuery,$bindkeyVlaue);

                    //アクセス権
                    $objRBAC = new RoleBasedAccessControl($g['objDBCA']);
                    $ret  = $objRBAC->getAccountInfo($g['login_id']);
                    foreach ($tmpContrastResult as $key => $targetRow) {

                        if( isset($targetRow['ROW_ID']) === true ){
                            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                            if($ret === false) {
                                // 例外処理へ
                                $strErrStepIdInFx="00000100";
                                $intErrorType = 1; //システムエラー
                                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                            } else {
                                if($permission !== true) {
                                    //アクセス権限を持っていない場合
                                    unset($tmpContrastResult[$key]);
                                }
                            }
                        }
                    }

                    $tmpDate = $tmpContrastResult[0];
                    //テンプレート生成
                    if( is_array($tmpDate)  ) {
                        if( count($tmpDate) != count($tmpBase) ){
                            foreach ($tmpDate as $tmpkey => $tmpvalue) {
                                $tmpBase[$intcnt][$tmpkey] = NULL;#"---";
                            }
                        }                        
                    }

                    //整形
                    if( is_array($tmpDate) ) {
                        $tmphostidList=array();
                        foreach ($tmpContrastResult as $tmpContrastRow) {
                            $tmpHOSTID = $tmpContrastRow['HOST_ID'];
                            $tmphostmenuList[$tmpHOSTID]=$tmpMenuId;
                            if( array_search( $tmpHOSTID,$tmphostList ) === false  )$tmphostList[]=$tmpHOSTID;
                            //比較情報全データ

                              //配列準備
                                if( array_key_exists($intcnt, $arrContrastResultALL ) === false  ){
                                    $arrContrastResultALL[$intcnt]=array();
                                }elseif( array_key_exists($tmpHOSTID, $arrContrastResultALL[$intcnt] ) === false  ){
                                    $arrContrastResultALL[$intcnt][$tmpHOSTID]=array();
                                }elseif( array_key_exists($tmpMenuId, $arrContrastResultALL[$intcnt][$tmpHOSTID] ) === false  ){
                                    if( count( $arrContrastResultALL[$intcnt][$tmpHOSTID] ) == 0 )$arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }elseif( count( $arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId] ) == 0 ){
                                   $arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }
                                if( isset($arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId] ) == false ){
                                    $arrContrastResultALL[$intcnt][$tmpHOSTID][$tmpMenuId] = $tmpContrastRow;
                                }

                            //比較情報カラムのみ
                                //配列準備
                                if( array_key_exists($intcnt, $arrContrastResult ) === false  ){
                                    $arrContrastResult[$intcnt]=array();
                                }elseif( array_key_exists($tmpHOSTID, $arrContrastResult[$intcnt] ) === false  ){
                                    $arrContrastResult[$intcnt][$tmpHOSTID]=array();
                                }elseif( array_key_exists($tmpMenuId, $arrContrastResult[$intcnt][$tmpHOSTID] ) === false  ){
                                    if( count( $arrContrastResult[$intcnt][$tmpHOSTID] ) == 0 )$arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }elseif( count( $arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId] ) == 0 ){
                                   $arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId]=array();
                                }
                                //比較対象以外除外
                                foreach ($tmpContrastRow as $tmpkey => $tmpvalue) {
                                    if( strpos( $tmpkey,'KY_AUTO_COL' ) === false ) unset( $tmpContrastRow[$tmpkey] );
                                }
                                if( isset($arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId] ) == false ){
                                    $arrContrastResult[$intcnt][$tmpHOSTID][$tmpMenuId] = $tmpContrastRow;
                                }

                        }
                    }
                }
            }
        }else{
            $tmpBase=array();
        }

        //比較用データ整形

        //比較対象テンプレート0件
        if( count($tmpBase) == 0 ){
            $strStreamOfContrastResult=array(array(),array());
            $strResultCode = sprintf("%03d", "");
            $strDetailCode = sprintf("%03d", "");
            $arrayResult = array($strResultCode,
                                 $strDetailCode,
                                 $strStreamOfContrastResult,
                                 $strExpectedErrMsgBodyForUI
                                 );
            dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
            return $arrayResult;

        //比較対象テンプレート1件
        }elseif( count($tmpBase) == 1 ){
            //比較対象データ欠け時、ダミーデータ
            $tmpConfigForCMDBbaseIUD = $arrConfigForCMDBbaseIUD;
            $tmpMenuId1 = array_keys($arrContrastTableList[0]);
            $tmpMenuId2 = array_keys($arrContrastTableList[1]);
            $tmpArrCol=array();
            if( isset($tmpBase[0]) ){
                $tmpArrCol = array();
                foreach ($arrContrastColList as $tmpColTitle => $arrIcolInfo) {
                    $tmpcolname = $arrIcolInfo[$tmpMenuId2[0]];
                    $tmpArrCol[$tmpcolname] = "";
                }
                $tmpBase[1]=array_merge_recursive($tmpConfigForCMDBbaseIUD,$tmpArrCol);
            }else{
                $tmpArrCol = array();
                foreach ($arrContrastColList as $tmpColTitle => $arrIcolInfo) {
                    $tmpcolname = $arrIcolInfo[$tmpMenuId1[0]];
                    $tmpArrCol[$tmpcolname] = "";
                }
                $tmpBase[0]=array_merge_recursive($tmpConfigForCMDBbaseIUD,$tmpArrCol);
            }
        }

        //比較対象あり
        if( count($arrContrastResultALL) >= 1 ){
            //データ無時のダミーデータ
            foreach ( array(0,1) as $key =>  $value) {
                if( isset( $arrContrastResult[$value] ) === false){
                    foreach ($tmphostmenuList as $tmpHOSTID => $tmpvalue) {
                        if( $value == 0 )$tmpMenuId = array_keys($arrContrastTableList[0]);
                        if( $value == 1 )$tmpMenuId = array_keys($arrContrastTableList[1]);
                        $tmpMenuId =$tmpMenuId[0];

                        //比較情報カラムのみ
                        if( isset($arrContrastResult[$value]) == false )$arrContrastResult[$value]=array();
                        if( isset($arrContrastResult[$value][$tmpHOSTID]) == false )$arrContrastResult[$value][$tmpHOSTID]=array();
                        if( isset($arrContrastResult[$value][$tmpHOSTID][$tmpMenuId]) == false )$arrContrastResult[$value][$tmpHOSTID][$tmpMenuId]=$tmpBase[$key];
                        foreach ( $arrContrastResult[$value][$tmpHOSTID][$tmpMenuId] as $tmpkey => $tmpvalue) {
                            if( strpos( $tmpkey,'KY_AUTO_COL' ) === false ) unset(  $arrContrastResult[$value][$tmpHOSTID][$tmpMenuId][$tmpkey] );
                        }
                        //比較情報全データ
                        if( isset($arrContrastResultALL[$value]) == false )$arrContrastResultALL[$value]=array();
                        if( isset($arrContrastResultALL[$value][$tmpHOSTID]) == false )$arrContrastResultALL[$value][$tmpHOSTID]=array();
                        if( isset($arrContrastResultALL[$value][$tmpHOSTID][$tmpMenuId]) == false )$arrContrastResultALL[$value][$tmpHOSTID][$tmpMenuId]=$tmpBase[$key];

                    }    
                }
            }

            foreach ($tmphostList as $tmpHOSTID) {
                foreach ( array(0,1) as $key =>  $value) {
                    if( $value == 0 )$tmpMenuId = array_keys($arrContrastTableList[0]);
                    if( $value == 1 )$tmpMenuId = array_keys($arrContrastTableList[1]);
                    $tmpMenuId =$tmpMenuId[0];

                    //比較情報カラムのみ
                        if( isset($arrContrastResult[$value]) == false )$arrContrastResult[$value]=array();
                        if( isset($arrContrastResult[$value][$tmpHOSTID]) == false )$arrContrastResult[$value][$tmpHOSTID]=array();
                        if( isset($arrContrastResult[$value][$tmpHOSTID][$tmpMenuId]) == false )$arrContrastResult[$value][$tmpHOSTID][$tmpMenuId]=$tmpBase[$value];

                        foreach ( $arrContrastResult[$value][$tmpHOSTID][$tmpMenuId] as $tmpkey => $tmpvalue) {
                            if( strpos( $tmpkey,'KY_AUTO_COL' ) === false ) unset(  $arrContrastResult[$value][$tmpHOSTID][$tmpMenuId][$tmpkey] );
                        }

                    //比較情報全データ
                        if( isset($arrContrastResultALL[$value]) == false )$arrContrastResultALL[$value]=array();
                        if( isset($arrContrastResultALL[$value][$tmpHOSTID]) == false )$arrContrastResultALL[$value][$tmpHOSTID]=array();
                        if( isset($arrContrastResultALL[$value][$tmpHOSTID][$tmpMenuId]) == false )$arrContrastResultALL[$value][$tmpHOSTID][$tmpMenuId]=$tmpBase[$key];                        
                    }

            }

            $arrFileUploadColList = array();
            foreach ($arrContrastResultALL as $intcnt => $temarr) {
                foreach ($temarr as $hostname => $tmpmenucol ) {
                    foreach ($tmpmenucol as $menuid => $arrcol) {
                        foreach ($arrContrastData[$menuid] as $arrColInfo) {
                            if( $arrColInfo[1] == "FileUploadColumn" ){
                                foreach ($arrcol as $colneme => $colvalue) {
                                    if( $arrColInfo[0] == $colneme ){
                                        $arrFileUploadColList[$intcnt][$hostname][$menuid][$colneme]=$colvalue;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //ファイルアップロードカラムのファイルパスの情報取得
            $aryUploadColumnDir=array();
            foreach ($arrFileUploadColList as $intcnt => $temarr) {
                foreach ($temarr as $hostname => $tmpmenucol ) {
                    foreach ($tmpmenucol as $menuid => $colInfo) {
                        //loadtable読み込み
                        $MenuID = sprintf('%010d', $menuid );
                        $g['root_dir_path'] = "/exastro/ita-root/";
                        $sheetFile = "{$g['root_dir_path']}/webconfs/sheets/{$MenuID}_loadTable.php";

                        if(file_exists($sheetFile)){
                            require_once($sheetFile);
                        }
                        
                        $registeredKey = $MenuID;
                        $aryVariant = array();
                        $arySetting = array();

                        if( 0 < strlen($registeredKey) ){
                            $objTable = loadTable($registeredKey,$aryVariant,$arySetting);
                            if($objTable === null){
                                // 00_loadTable.phpの読込失敗
                                $intErrorType = 101;
                                $strErrMsg = "[" . $strLoadTableFullname . "] Analysis Error";
                            }
                        }

                        if( $objTable !== null ){
                            $aryColumns = $objTable->getColumns();

                            if( is_a($objTable,"TemplateTableForReview") === true ){
                                    #
                            }else{
                                //----標準テーブル
                                $strUTNRIColumnId = $objTable->getRowIdentifyColumnID();
                                $strJNLRIColumnId = $objTable->getRequiredJnlSeqNoColumnID();
                                
                                $aryRequiredColumnId = array(
                                    "RowIdentify"    =>$strUTNRIColumnId
                                    ,"Disuse"        =>$objTable->getRequiredDisuseColumnID()
                                    ,"RowEditByFile" =>$objTable->getRequiredRowEditByFileColumnID()
                                    ,"UpdateButton"  =>$objTable->getRequiredUpdateButtonColumnID()
                                    
                                    ,"Note"          =>$objTable->getRequiredNoteColumnID()

                                    ,"LastUpdateDate"=>$objTable->getRequiredLastUpdateDateColumnID()
                                    ,"LastUpdateUser"=>$objTable->getRequiredLastUpdateUserColumnID()
                                    ,"UpdateDate4U"  =>$objTable->getRequiredUpdateDate4UColumnID()

                                    ,"JnlSeqNo"      =>$strJNLRIColumnId
                                    ,"JnlRegTime"    =>$objTable->getRequiredJnlRegTimeColumnID()
                                    ,"JnlRegClass"   =>$objTable->getRequiredJnlRegClassColumnID()
                                    ,"UtnSeqName"    =>$aryColumns[$strUTNRIColumnId]->getSequenceID()
                                    ,"JnlSeqName"    =>$aryColumns[$strJNLRIColumnId]->getSequenceID()
                                
                                );
                                
                                $strUTNTableId = $objTable->getDBMainTableHiddenID();
                                $strJNLTableId = $objTable->getDBJournalTableHiddenID();
                                if( 0 < strlen($strUTNTableId) && 0 < strlen($strJNLTableId) ){
                                    $strUTNViewId = $objTable->getDBMainTableBody();
                                    $strJNLViewId = $objTable->getDBJournalTableBody();
                                    $strHiddenTableMode = true;
                                }
                                else{
                                    $strUTNTableId = $objTable->getDBMainTableBody();
                                    $strJNLTableId = $objTable->getDBJournalTableBody();
                                }

                                //標準テーブル----
                            }
                            
                            //----カラムインスタンスの取得
                            foreach($aryColumns as $strColumnId=>$objColumn){
                                $boolAddInfo = false;
                                if( in_array($strColumnId,$aryRequiredColumnId) === false ){
                                    //----必須カラムではない任意カラム
                                    if( $strHiddenTableMode === true ){
                                        //----VIEWを表示、TABLEを更新させる設定の場合
                                        if( $objColumn->isDBColumn() === true && $objColumn->isHiddenMainTableColumn() ){
                                            $boolAddInfo = true;
                                        }
                                        //VIEWを表示、TABLEを更新させる設定の場合----
                                    }
                                    else{
                                        //----TABLEを表示/更新させる設定の場合
                                        if( $objColumn->isDBColumn() === true ){
                                            $boolAddInfo = true;
                                        }
                                        //----TABLEを表示/更新させる設定の場合
                                    }
                                    if( $boolAddInfo === true ){
                                        $aryColumnInfo01[] = array($strColumnId,$objColumn->getColLabel(true));
                                        if("FileUploadColumn" ===  get_class($objColumn)){
                                            $aryUploadColumnDir[$intcnt][$menuid][$strColumnId] = $objColumn->getLRPathPackageRootToBranchPerFUC();
                                        }
                                    }
                                    else{
                                        $aryColumnInfo02[] = array($strColumnId,$objColumn->getColLabel(true));
                                    }
                                    //必須カラムではない任意カラム----
                                }
                            }
                        }
                    }
                }
            }

            $arrFilediffList = array();
            $arrFilediffList2 = array();
            foreach ($arrContrastResultALL as $intcnt => $temarr) {
                foreach ($temarr as $hostname => $tmpmenucol ) {
                    foreach ($tmpmenucol as $menuid => $arrcol) {
                        foreach ($arrContrastData[$menuid] as $arrColInfo) {
                            if( $arrColInfo[1] == "FileUploadColumn" ){
                                foreach ($arrcol as $colneme => $colvalue) {
                                    if( $arrColInfo[0] == $colneme ){

                                        //項目名変換
                                        $columname = $arrContrastColOriList[$colneme][$menuid];

                                        //ファイルのパス要素
                                        $tmpmenupath = $aryUploadColumnDir[$intcnt][$menuid][$arrColInfo[0]];
                                        $tmprowid = sprintf('%010d', $arrcol['ROW_ID'] );
                                        $tmpfilename = $arrcol[$colneme];

                                        //基準日
                                        $strbasetime="";
                                        if( $baseParm['BASE_TIMESTAMP'][$intcnt] != "" ){
                                            $strbasetime = $baseParm['BASE_TIMESTAMP'][$intcnt];
                                        }
                                        //基準日無しの場合、最新のファイルのパス
                                        if( $strbasetime == "" ){
                                            $tmpfilepath = $g['root_dir_path'].$tmpmenupath."/" .$tmprowid. "/".$tmpfilename;
                                            if( $tmpfilename != "" ){    
                                                $arrFilediffList[$intcnt][$hostname][$menuid][$colneme]=$tmpfilepath;
                                            }else{
                                                $arrFilediffList[$intcnt][$hostname][$menuid][$colneme]="";
                                            }
                                        }else{
                                            //履歴検索
                                            if( $tmpfilename != "" ){
                                                //ベース
                                                $bindkeyVlaue = array();

                                                $tmpTableName = array_keys($arrFileUploadColumnFlg[$intcnt]);
                                                $tmpTableName = $tmpTableName[0]; 
                                                $strQuery = "SELECT * ";
                                                $strQuery = $strQuery ." FROM ${tmpTableName}_JNL TAB_A ";
                                                $strQuery = $strQuery ."WHERE "
                                                           ." TAB_A.DISUSE_FLAG = 0 ";
                                                $strQuery = $strQuery
                                                        ." AND  "
                                                        ." TAB_A.BASE_TIMESTAMP < cast( :BASE_TIMESTAMP as datetime(6) ) "
                                                        ." AND  "
                                                        ." TAB_A.ROW_ID = :ROW_ID "
                                                        ." AND  "
                                                        ." TAB_A.${colneme} = :FILE_NAME "
                                                        ."";
                                                $bindkeyVlaue = array(
                                                    "BASE_TIMESTAMP" => $strbasetime,
                                                    "ROW_ID" => $arrcol['ROW_ID'],
                                                    "FILE_NAME" => $tmpfilename
                                                );
                                                $strQuery = $strQuery. " ORDER BY TAB_A.LAST_UPDATE_TIMESTAMP DESC LIMIT 1";

                                                $tmpfileinfo = execsql($strQuery,$bindkeyVlaue);
                                                
                                                $tmpJnlSecNo = sprintf('%010d', $tmpfileinfo[0]['JOURNAL_SEQ_NO'] );
                                                $tmpfilepath = $g['root_dir_path'].$tmpmenupath."/" .$tmprowid. "/old/".$tmpJnlSecNo . "/" .$tmpfilename;
                                                $arrFilediffList[$intcnt][$hostname][$menuid][$colneme]=$tmpfilepath;

                                            }else{
                                                 $arrFilediffList[$intcnt][$hostname][$menuid][$colneme]="";
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $tmpdiffResult=array();
            $arrdiffResult=array();
            $arrtdlist=array();
            $arrtdlistflg=array();
            $rowNo=1;

            $tmpaytdlist=array(
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310210"),#"比較項番", 
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310211"),#"結果",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310212"),#"ホスト名",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310213"),#"メニュー名称",
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310214"),#"No"
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310215"),#"オペレーション名",,
                $g['objMTS']->getSomeMessage("ITABASEH-MNU-310216"),#"基準日",
            );

            $tmparrtdlist = array_merge_recursive($tmpaytdlist, $arrContrastTitleList);
            $arrtdtitlelist = $tmparrtdlist;
            //比較
            foreach ($arrContrastColList as $colname => $tmpvalue) {
                foreach ($tmphostList as $hostname ) {
                    //比較対象メニューID、カラム、値の取得
                    $tmpMenuid1 = $arrContrastMenuList[0];
                    $tmpMenuid2 = $arrContrastMenuList[1];
                    $tmpColneme1 = $tmpvalue[$tmpMenuid1];
                    $tmpColneme2 = $tmpvalue[$tmpMenuid2];
                    $tmpColvalue1 ="";
                    $tmpColvalue2 ="";

                    //メニュー1の値
                    if( isset( $arrContrastResult[0][$hostname][$tmpMenuid1][$tmpColneme1] ) ){
                        $tmpColvalue1 = $arrContrastResult[0][$hostname][$tmpMenuid1][$tmpColneme1];
                    }
                    //メニュー2の値
                    if( isset( $arrContrastResult[1][$hostname][$tmpMenuid2][$tmpColneme2] ) ){
                        $tmpColvalue2 = $arrContrastResult[1][$hostname][$tmpMenuid2][$tmpColneme2];
                    }

                    ####ファイルアップロードカラム関連追加予定
                    $tmpfilepath1="";
                    $tmpfilepath2="";
                    if( isset( $arrFilediffList[0][$hostname][$tmpMenuid1][$tmpColneme1] ) ){
                        $tmpfilepath1 = $arrFilediffList[0][$hostname][$tmpMenuid1][$tmpColneme1];
                    }
                    if( isset( $arrFilediffList[1][$hostname][$tmpMenuid2][$tmpColneme2] ) ){
                        $tmpfilepath2 = $arrFilediffList[1][$hostname][$tmpMenuid2][$tmpColneme2];
                    }

                    //ファイルがある場合
                    $intFileDiffFlg = 0;
                    if( $tmpfilepath1 != "" || $tmpfilepath2 != "" ){
                        if( file_exists($tmpfilepath1) == true || file_exists($tmpfilepath2) == true ){
                            $intFileDiffFlg = 1;
                            $syscmd = "diff " . " '" . $tmpfilepath1 . "' '" . $tmpfilepath2 . "'";   
                            $ret = `${syscmd}`;

                            if( $ret != 0 ){
                                #差分あり
                                unset($ret);
                                $intFileDiffFlg = 2;
                            }
                        }
                    }

                    //比較
                    $resultcode="";
                    if( $tmpColvalue1 != $tmpColvalue2 )$resultcode = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310217");#"差分あり";
                    $tmpdiffResult[$hostname][$colname] = $resultcode;

                    //比較（diff結果内容の上書き）
                    $resultcode="";
                    if( $intFileDiffFlg == 1 ){
                        
                        $tmpfilename1 = basename($tmpfilepath1);
                        $tmpfilename2 = basename($tmpfilepath2);
                        if( $tmpfilename1 == $tmpfilename2 ){
                            $resultcode = "";    
                        }else{
                            $resultcode = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310217");#"差分あり";
                        }
                        $tmpdiffResult[$hostname][$colname] = $resultcode;

                    }elseif( $intFileDiffFlg == 2 ){
                        $resultcode = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310217");#"差分あり";
                        $tmpdiffResult[$hostname][$colname] = $resultcode;
                    }
                    
                    //差分対象リスト
                    $arrtdlistflg[$hostname][$tmpMenuid1]=$tmpBase[0];
                    $arrtdlistflg[$hostname][$tmpMenuid2]=$tmpBase[1];
                }
            }

            //表示用リスト
            $resultflgList=array();
            foreach ($arrContrastResultALL as $tmpContrastResultALL) {
                foreach ($tmpContrastResultALL as $HostID => $tmpMenuResult) {
                    foreach ($tmpMenuResult as $MenuID => $tmparrInfo) {
                        $resultflgList=array();
                        $tmparrtdlist=array(
                                $HostID,
                                $MenuID,
                                $tmparrInfo['ROW_ID'],
                                $tmparrInfo['OPERATION_ID'],
                                $tmparrInfo['BASE_TIMESTAMP'],
                        );
                        foreach ($tmparrInfo as $col => $val) {
                            if(  strpos( $col,'KY_AUTO_COL' ) !== false ){
                                $tmparrtdlist[$col] = $val;
                                foreach ($arrContrastColList as $tmpcolname => $tmparrcolmun) {
                                    foreach ($tmparrcolmun as $tmpcolmunname) {
                                        if( $col == $tmpcolmunname ) $resultflgList[$HostID][$col] = $tmpdiffResult[$HostID][$tmpcolname];

                                    }
                                }
                            }
                        }
                        $arrtdlist[$HostID][$MenuID][]=$tmparrtdlist;
                    }
                }
            }
        }

        //比較結果
        //結果フラグ
        foreach ($arrtdlist as $hostname => $arrInfo) {
            $tmpMenuid1 = $arrContrastMenuList[0];
            $tmpMenuid2 = $arrContrastMenuList[1];

            if( count( $arrInfo[$tmpMenuid1] ) == 2  ){
                $arrInfo1 = $arrInfo[$tmpMenuid1][0];
                $arrInfo2 = $arrInfo[$tmpMenuid1][1];
            }else{
                $arrInfo1 = $arrInfo[$tmpMenuid1][0];
                $arrInfo2 = $arrInfo[$tmpMenuid2][0];                
            }

            foreach ($arrContrastColList as $tmpcolname => $tmparrcolmun) {
                $colname1 = $tmparrcolmun[$tmpMenuid1];
                $colname2 = $tmparrcolmun[$tmpMenuid2];

                $tmpColvalue1 = $arrInfo1[$colname1];
                $tmpColvalue2 = $arrInfo2[$colname2];

                $resultcode="";
                if( $tmpColvalue1 != $tmpColvalue2 )$resultcode = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310217");#"差分あり";

                if( isset( $resultflgList[$hostname][$colname1] ) ){
                    $resultcode = $resultflgList[$hostname][$colname1];

                }elseif( isset($resultflgList[$hostname][$colname2]) ){
                    $resultcode = $resultflgList[$hostname][$colname2];
                }
                
                if( isset( $arrdiffResult[$hostname])  !== true  ){
                    $arrdiffResult[$hostname] = $resultcode;
                }elseif( $arrdiffResult[$hostname] ==  ""  && $resultcode != "" ){
                    $arrdiffResult[$hostname] = $resultcode;
                }
                if( $tmpMenuid1 != $tmpMenuid2 ){
                    $arrtdlistflg[$hostname][$tmpMenuid1][$colname1] = $resultcode;
                    $arrtdlistflg[$hostname][$tmpMenuid2][$colname2] = $resultcode;
                }else{
                    $arrtdlistflg[$hostname][$tmpMenuid1][$colname1] = $resultcode;
                }
            }
        }

        // 表示用データ　array( ホスト名 => メニューID　=> array(　項目 => 値　,,,, ) )
        $arrtdlistcolname = $arrtdlist;
        foreach ($arrContrastData as $MenuID => $arrcol ) {
            foreach ($arrcol as $colname => $arrcolInfo ) {
                foreach ($arrtdlistcolname as $hostname => $arrtd ) {
                    foreach ($arrtd as $MenuID2 => $arrval) {
                        foreach ($arrval as $tdno => $val) {
                            if( array_key_exists($arrcolInfo[0], $val) ){
                                $arrtdlistcolname[$hostname][$MenuID2][$tdno][$colname]=$val[$arrcolInfo[0]];    
                            }                       
                        }
                    }   
                }
            }
        }
        foreach ($arrContrastColOriList as $colname => $val) {
            foreach ($arrtdlistcolname as $hostname => $arrtd ) {
                foreach ($arrtd as $MenuID => $arrval) {
                    foreach ($arrval as $tdno => $val) {    
                        if( array_key_exists($colname, $arrtdlistcolname[$hostname][$MenuID][$tdno]) ){
                            unset($arrtdlistcolname[$hostname][$MenuID][$tdno][$colname]);
                        }
                    }
                }   
            }
        }
        // 結果用データ[カラム名→項目名変換]　array( ホスト名 => メニューID　=> array(　項目名 => 結果　,,,, ) )
        $arrtdlistflgcolname = $arrtdlistflg;
        foreach ($arrContrastData as $MenuID => $arrcol ) {
            foreach ($arrcol as $colname => $arrcolInfo ) {
                foreach ($arrtdlistflgcolname as $hostname => $arrtd ) {
                    foreach ($arrtd as $MenuID2 => $arrval) {
                        foreach ($arrval as $tdno => $val) {                            
                            if( $arrcolInfo[0] == $tdno ){
                                $arrtdlistflgcolname[$hostname][$MenuID2][$colname]=$val;    
                            }                      
                        }
                    }   
                }
            }
        }
        foreach ($arrContrastColOriList as $colname => $val) {
            foreach ($arrtdlistflgcolname as $hostname => $arrtd ) {
                foreach ($arrtd as $MenuID => $arrval) {
                    foreach ($arrval as $tdno => $val) {    
                        if( array_key_exists($MenuID, $arrtdlistflgcolname[$hostname]) ){
                            if( strpos($colname,'KY_AUTO_COL_') !== false  ){
                                unset($arrtdlistflgcolname[$hostname][$MenuID][$colname]);
                            }else{
                                 if( array_key_exists($colname, $arrtdlistflgcolname[$hostname][$MenuID]) === false  ){
                                    unset($arrtdlistflgcolname[$hostname][$MenuID][$colname]);
                                } 
                            }
                        }
                    }
                }   
            }
        }

        //アクセス権　IDカラム変換
        foreach ($arrtdlistcolname as $hostid => $arrval) {
            foreach ($arrval as $MenuID => $arrtd ) {
                foreach ($arrtd as $tmpkey => $arrval2 ) {
                    foreach ($arrval2 as $tmpcol => $tmpval ) {
                        //比較項目
                        if( isset( $arrContrastData[$MenuID][$tmpcol] ) === true ){
                            
                            $tmpTableName = $arrContrastData[$MenuID][$tmpcol][2];
                            $tmpcolid     = $arrContrastData[$MenuID][$tmpcol][3];
                            $tmpcolName   = $arrContrastData[$MenuID][$tmpcol][4];

                            if( $tmpTableName != "" && $tmpcolid != "" && $tmpcolName != ""){
                                $strQuery = "SELECT * FROM "
                                           ." {$tmpTableName}  "
                                           ."WHERE "
                                           ." DISUSE_FLAG IN ('0') "
                                           ."AND "
                                           ."  {$tmpcolid} = :{$tmpcolid} "
                                           ."";

                                $bindkeyVlaue= array(
                                    $tmpcolid => $tmpval
                                );
                                $aryRetBody = execsql($strQuery,$bindkeyVlaue);

                                $ret  = $objRBAC->getAccountInfo($g['login_id']);
                                foreach ($aryRetBody as $key => $targetRow) {
                                    if( isset($targetRow[$tmpcolid]) === true ){
                                        list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                                        if($ret === false) {
                                            // 例外処理へ
                                            $strErrStepIdInFx="00000100";
                                            $intErrorType = 1; //システムエラー
                                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                        } else {
                                            if($permission !== true) {
                                                $arrtdlistcolname[$hostid][$MenuID][$tmpkey][$tmpcol]=$g['objMTS']->getSomeMessage("ITABASEH-MNU-310218",array($tmpval));
                                                #"ID変換失敗(".$tmpval.")";
                                            }else{
                                                $arrtdlistcolname[$hostid][$MenuID][$tmpkey][$tmpcol]=$targetRow[$tmpcolName];
                                            }
                                        }
                                    }
                                }
                            }
                        }else{
                            $strQuery = "";
                            $tmpTableName = "";
                            $tmpcolid     = "";
                            $tmpcolName   = "";   
                            //ホスト名、オペレーション,メニュー
                            switch ($tmpcol) {
                                case 0:
                                    $tmpTableName = "C_STM_LIST";
                                    $tmpcolid     = "SYSTEM_ID";
                                    $tmpcolName   = "HOSTNAME";
                                    break;                                
                                case 1:                                   
                                    $strQuery = "SELECT count(*) AS COUNT ,"
                                               ." TAB_C.MENU_NAME "
                                               ."FROM "
                                               ." A_ROLE_MENU_LINK_LIST TAB_A "
                                               ."LEFT "
                                               ." JOIN A_ROLE_ACCOUNT_LINK_LIST TAB_B ON (TAB_A.ROLE_ID=TAB_B.ROLE_ID) "
                                               ."LEFT "
                                               ." JOIN A_MENU_LIST TAB_C ON (TAB_A.MENU_ID=TAB_C.MENU_ID) "
                                               ."WHERE "
                                               ." TAB_A.DISUSE_FLAG IN ('0') "
                                               ."AND "
                                               ." TAB_B.DISUSE_FLAG IN ('0') "
                                               ."AND "
                                               ."  TAB_B.USER_ID = :USER_ID "
                                               ."AND "
                                               ."  TAB_A.MENU_ID = :MENU_ID "
                                               ."";
                                    $bindkeyVlaue= array(
                                        "USER_ID" => $g['login_id'],
                                        "MENU_ID" => $tmpval
                                    );
                                    $aryRetBody = execsql($strQuery,$bindkeyVlaue);

                                    foreach ($aryRetBody as $key => $targetRow) {
                                        if( isset($targetRow['COUNT']) === true ){
                                            if( $targetRow['COUNT'] == 0 ) {
                                                $arrtdlistcolname[$hostid][$MenuID][$tmpkey][$tmpcol]=$g['objMTS']->getSomeMessage("ITABASEH-MNU-310218",array($tmpval));#"ID変換失敗(".$tmpval.")";
                                            } else {
                                                $arrtdlistcolname[$hostid][$MenuID][$tmpkey][$tmpcol]=$targetRow['MENU_NAME'];
                                            }
                                        }
                                    }


                                    break;    
                                case 3:
                                    $tmpTableName = "C_OPERATION_LIST";
                                    $tmpcolid     = "OPERATION_NO_UAPK";
                                    $tmpcolName   = "OPERATION_NAME";
                                    break;                                
                                default:                         
                                    break;
                            }

                            if( $tmpTableName != ""  ){
                                $strQuery = "SELECT * FROM "
                                           ." {$tmpTableName}  "
                                           ."WHERE "
                                           ." DISUSE_FLAG IN ('0') "
                                           ."AND "
                                           ."  {$tmpcolid} = :{$tmpcolid} "
                                           ."";

                                $bindkeyVlaue= array(
                                    $tmpcolid => $tmpval
                                );
                                $aryRetBody = execsql($strQuery,$bindkeyVlaue);

                                $ret  = $objRBAC->getAccountInfo($g['login_id']);

                                foreach ($aryRetBody as $key => $targetRow) {
                                    if( isset($targetRow[$tmpcolid]) === true ){
                                        list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
                                        if($ret === false) {
                                            // 例外処理へ
                                            $strErrStepIdInFx="00000100";
                                            $intErrorType = 1; //システムエラー
                                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                                        } else {
                                            if($permission !== true) {
                                                $arrtdlistcolname[$hostid][$MenuID][$tmpkey][$tmpcol]=$g['objMTS']->getSomeMessage("ITABASEH-MNU-310218",array($tmpval));#"ID変換失敗(".$tmpval.")";
                                            }else{
                                                $arrtdlistcolname[$hostid][$MenuID][$tmpkey][$tmpcol]=$targetRow[$tmpcolName];
                                            }
                                        }
                                    }
                                }
                            } 
                        }
                    }
                }
            }
        }

        $arrConstResult= array();
        $arrConstResult[]=$arrtdtitlelist;
        $arrConstResultflg= array();
        $arrConstResultflg[]=$arrtdtitlelist;
        $resultflgList=array();
        $rowNo=1;
        foreach ($arrtdlistcolname as $hostname => $arrInfo ) {
            $samemenucnt=0;
            foreach ($arrInfo as $MenuID =>$tmpcolval) {
                if( count( $tmpcolval ) == 2){

                    foreach ($tmpcolval as $arrcolval) {
                        $tmpConstResult= array();
                        $tmpConstResult[]=$rowNo;
                        $tmpConstResult[]=$arrdiffResult[$hostname];

                        $tmpConstResultflg= array();
                        $tmpConstResultflg[]="";#$rowNo;

                        $flgval="";
                        if( $arrdiffResult[$hostname] != "" )$flgval="1";
                        $tmpConstResultflg[]=$flgval;

                        foreach ($arrcolval as $tmpcol =>  $value) {
                            $tmpConstResult[]=$value;
                            $flgval="";
                            if( isset( $arrtdlistflgcolname[$hostname][$MenuID][$tmpcol] )  &&  $arrtdlistflgcolname[$hostname][$MenuID][$tmpcol] != "" )$flgval="1";
                            $tmpConstResultflg[]=$flgval;
                        }
                        $samemenucnt++;
                        $rowNo++;
                        $arrConstResult[]=$tmpConstResult;
                        $arrConstResultflg[]=$tmpConstResultflg;
                    }
                }else{
                    $tmpConstResult= array();
                    $tmpConstResult[]=$rowNo;
                    $tmpConstResult[]=$arrdiffResult[$hostname];


                    $tmpConstResultflg= array();
                    $tmpConstResultflg[]="";#$rowNo;
                    $flgval="";
                    if( $arrdiffResult[$hostname] != "" )$flgval="1";
                    $tmpConstResultflg[]=$flgval;

                    foreach ($tmpcolval[0] as $tmpcol =>  $value) {
                        $tmpConstResult[]=$value;  

                        $flgval="";
                        if( isset( $arrtdlistflgcolname[$hostname][$MenuID][$tmpcol] )  &&  $arrtdlistflgcolname[$hostname][$MenuID][$tmpcol] != "" )$flgval="1";
                        $tmpConstResultflg[]=$flgval;                                  
                    }
                    $arrConstResult[]=$tmpConstResult;
                    $arrConstResultflg[]=$tmpConstResultflg;                        
                }

                $tmpConstResultflg= array();
                $tmpConstResult= array();
                $rowNo++;

            }                        
        }
  
        $strStreamOfContrastResult = array($arrConstResult,$arrConstResultflg);

    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strStreamOfContrastResult,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
    

}

//比較結果のテーブル出力
function gethtmlContrast($strContrastListID,$arrBasetime1,$arrBasetime2,$strhostlist){

    global $g;

    // 各種ローカル定数を定義
    $intControlDebugLevel01 = 250;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intOperationNo = "";
    $strStreamOfOperation = "";
    $strExpectedErrMsgBodyForUI = "";
    $strStreamOfContrastList = "";
    // 各種ローカル変数を定義
    #/*
    $intErrorType = null;
    $intDetailType = null;
    
    $strSysErrMsgBody = "";
    $strErrStepIdInFx = "";
    
    $strExpectedErrMsgBodyForUI = "";
    
    $strFxName = __FUNCTION__;
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-1",__FILE__),$intControlDebugLevel01);
    
    // 処理開始
    try{
        
        $arrayResult = getContrastResult($strContrastListID,$arrBasetime1,$arrBasetime2,$strhostlist);
        
        //比較対象データ有
        if( isset($arrayResult[2][0][0] ) ){
            //項目名
            $arrtdtitlelist = $arrayResult[2][0][0];
            //データ
            $arrtdmainlist = $arrayResult[2][0];
            unset($arrtdmainlist[0]);
            //フラグ
            $arrtdmainflglist = $arrayResult[2][1];
            $arrcntstrwidth = array();
        
            //HTML生成
            $strhtml="";
            $strhtml = $strhtml .
<<<EOD
    <div class="fakeContainer_Filter1Print">
        <div id="Mix1_1_itaTable" class="itaTable def tableSticky">
            <div id="Mix1_1_itaTableBody" class="itaTableBody" >
                <div class="tableScroll">
                    <table id="Mix1_1">
                        <tbody>
                            <tr class="defaultExplainRow">
EOD;

            //項目名表示
            $int =0;
            foreach ( $arrtdtitlelist as $tmpcolname) {
                $strhtml = $strhtml .
<<<EOD
<th scope="col" onclick="tableSort(1, this, 'Mix1_1', {$int} ,nsort,'sortMarkWrap','sortNotSelected','sortSelectedAsc','sortSelectedDesc');" class="sortTriggerInTbl "><span class="generalBold">{$tmpcolname}</span><span class="sortMarkWrap"><span class="sortNotSelected"></span></span></th>
EOD;
                $int++;
            }
            $strhtml = $strhtml . '</tr>';
           
            //比較結果
            foreach ($arrtdmainlist as $tmpkey => $tmpval ) {
                
                $strhtml = $strhtml .'<tr valign="top" >';
                $int =0;
                foreach ($tmpval as $colnum => $value) {
                        $strhtml = $strhtml .'<td id="cell_print_table_'.$tmpkey.'_'.$int.'" >';
                        $strhtml = $strhtml .'<div class="tdInner">';
                        if( $arrtdmainflglist[$tmpkey][$colnum] != "" ) $strhtml = $strhtml . '<span class="filter_match">';
                            $strhtml = $strhtml . ''.$value .'' ;
                        if( $arrtdmainflglist[$tmpkey][$colnum] != "" ) $strhtml = $strhtml . '</span>';
                        $strhtml = $strhtml .'</div>';
                        $strhtml = $strhtml .'</td>';
                        $int++;
                }
                $strhtml = $strhtml .'</tr>';
            }
            $strhtml = $strhtml .
<<<EOD
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

EOD;


            //比較結果出力用のフォーム（Excel）
            $strhtml = $strhtml .
<<< EOD
        <!-------------------------------- 比較結果出力(Excel) -------------------------------->
        <form name="reqExcelDL" action="/default/menu/03_create_excel.php?no=2100190003"  target="_blank" method="POST">
EOD;
            $strhtml = $strhtml . '<input style="display:none;" name="CONTRAST_ID" value="'. $strContrastListID .'">';
            $strhtml = $strhtml . '<input style="display:none;" name="BASE_TIMESTAMP_0" value="'. $arrBasetime1 .'">';
            $strhtml = $strhtml . '<input style="display:none;" name="BASE_TIMESTAMP_1" value="'. $arrBasetime2 .'">';
            $strhtml = $strhtml . '<input style="display:none;" name="HOST_LIST" value="'. $strhostlist .'">';
            $strhtml = $strhtml . 
<<< EOD
            <input type="submit" value="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310219")}">
            <input type="hidden" name="filteroutputfiletype" value="excel">
            <input type="hidden" name="FORMATTER_ID" value="excel">
        </form>
        <!-------------------------------- 比較結果出力(Excel) -------------------------------->
EOD;
            //比較結果出力用のフォーム（CSV）
            $strhtml = $strhtml .
<<< EOD
        <!-------------------------------- 比較結果出力(CSV) -------------------------------->
        <form name="reqExcelDL" action="/default/menu/03_create_excel.php?no=2100190003"  target="_blank" method="POST">
EOD;
            $strhtml = $strhtml . '<input style="display:none;" name="CONTRAST_ID" value="'. $strContrastListID .'">';
            $strhtml = $strhtml . '<input style="display:none;" name="BASE_TIMESTAMP_0" value="'. $arrBasetime1 .'">';
            $strhtml = $strhtml . '<input style="display:none;" name="BASE_TIMESTAMP_1" value="'. $arrBasetime2 .'">';
            $strhtml = $strhtml . '<input style="display:none;" name="HOST_LIST" value="'. $strhostlist .'">';
            $strhtml = $strhtml . 
<<< EOD
            <input type="submit" value="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-310220")}">
            <input type="hidden" name="filteroutputfiletype" value="csv">
            <input type="hidden" name="FORMATTER_ID" value="csv">
        </form>
        <!-------------------------------- 比較結果出力(CSV) -------------------------------->
EOD;
        }else{
            //比較対象データ無し
            $strhtml = $g['objMTS']->getSomeMessage("ITABASEH-MNU-310221");#"指定した条件で比較対象となるデータがありません";
        }
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType===null ) $intErrorType = 500;
        
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strhtml,
                         $strExpectedErrMsgBodyForUI
                         );
    dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;

}

//SQL実行　（クエリー、バインド）
function execsql($strQuery,$bindkeyVlaue){
    global $g;
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryDataSet = array();
    $aryForBind = array();
    $strFxName = "";
    
    foreach ($bindkeyVlaue as $key => $value) {
        $aryForBind[$key] = $value;
    }

    if(  is_array($aryForBind) == 1 ){
        $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);
        if( $aryRetBody[0] === true ){
            $objQuery = $aryRetBody[1];
            while($row = $objQuery->resultFetch() ){
                $aryDataSet[]= $row;
            }
            unset($objQuery);
            $retBool = true;
        }else{
            $intErrorType = 500;
            $intRowLength = -1;
        }
    }
    $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryDataSet);
    if ($aryDataSet != array() )return $retArray[4];
    return $retArray;
};


//デバッグ用
function debug_output($g,$d_array,$fnc=" ",$lin=" "){
    $file = $g['root_dir_path'] . "/temp/debug" .date('Ymd') . ".txt";
    file_put_contents($file, PHP_EOL . date('Y-m-d H:i:s') . " " . $fnc ." " . $lin . PHP_EOL, FILE_APPEND);
    error_log(print_r($d_array, true), 3, $file);
    echo('<pre>');
    print_r( $d_array );
    echo('</pre>');

}
?>
