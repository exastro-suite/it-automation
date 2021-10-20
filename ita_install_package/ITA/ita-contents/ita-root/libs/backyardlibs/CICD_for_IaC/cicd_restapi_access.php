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
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・CI/CDのbackyardで呼び出される
//
//////////////////////////////////////////////////////////////////////

class CicdRestAccessAgent {
    
    protected $objMTS;

    function __construct($objMTS=null){
        //----変数を初期化        
        $this->objMTS = $objMTS;
        //変数を初期化----
    }
        
    function getMessageTemplateStorage(){
        return $this->objMTS;
    }

    //----作業実行を登録するRESTAPIの実行
    /*
        IN
            $DriverType,                //ドライバー種別   EX) C_EXT_STM_ID_LEGACY
            $OperationID,               //オペレーションID
            $MovementID,                //MovementID
            $runMode,                   //実行モード

            $UserID,                    //RESTユーザ名
            $UserPW,                    //RESTユーザパスワード
            $Hostname,                  //ITAホスト名
            $Protocol,                  //プロトコル
            $PortNo,                    //ポート番号
        OUT
            Array(
                [0] =>  正常終了:000  / 正常以外：エラーコード
                [1] =>  作業インスタンスID
                [2] =>  UI用メッセージ    EX) 異常時：エラーメッセージ
                [3] =>  Array(
                            2の詳細 or その他
                        ) 
            )  

    */
    function executeMovement( $DriverType,$OperationID,$MovementID,$runMode, $UserID,$UserPW,$Hostname,$Protocol,$PortNo ){
        
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        
        $strExecutionNo = "";
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strRegisterDate = "";
        $strExpectedErrMsgBodyForUI = "";

        $arrRequestContents = array();
        
        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        
        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        $log_output_dir = getenv('LOG_DIR');
        $log_output_php     = '/libs/backyardlibs/backyard_log_output.php';

        $arrExecuteMenuID =array(
            TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY =>      '2100020111',
            TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER =>     '2100020211',
            TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE =>        '2100020312',
            TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_TERRAFORM =>   '2100080009',
        );

        try{

            $objMTS = $this->getMessageTemplateStorage();
            
            // 開始メッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = " CI/CD ECECUTE RESTAPI START";#$objMTS->getSomeMessage("ITAWDCH-STD-50001");
                require ($root_dir_path . $log_output_php );
            }

            $baseParm=array(
                'protocol'           => '',
                'hostName'           => '',
                'portNo'             => '',
                'requestURI'         => '/default/menu/07_rest_api_ver1.php?no=',
                'method'             => 'POST',
                'contentType'        => 'application/json',                
                'accessKeyId'        => '',
                'xCommand'           => '',
                'strParaJsonEncoded' => '',
            );

            //簡易バリデーション（数値）
            if (is_numeric($DriverType) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5000", array($DriverType) );#"ドライバー種別が不正な値です。 ({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( is_numeric($OperationID) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5001", array($OperationID) );#"オペレーションIDの設定値が不正です。({})"";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( is_numeric($MovementID) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5002", array($MovementID) );#"MovementIDの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000300";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( is_numeric($runMode) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5003", array($runMode) );#"実行種別の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000400";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( is_numeric($PortNo) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5004", array($PortNo) );#"ポート番号の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //簡易バリデーション（文字列）
            if ( empty($UserID) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5005");#"ユーザIDの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( empty($UserPW) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5006");#""ユーザPWの設定値が不正です。";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( empty($Hostname) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5007");#"ホスト名の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000800";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( empty($Protocol) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5008");#"プロトコルの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000900";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }elseif( array_search( strtolower($Protocol), array('http','https') ) === false ){
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5008", array($Protocol));#"プロトコルの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000900";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            //対象メニューID取得
            if( isset( $arrExecuteMenuID[ $DriverType ] ) === true ){
                $strMenuid = $arrExecuteMenuID[ $DriverType ];
            }else{
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5000", array($DriverType) );#"ドライバー種別が不正な値です。 ({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00001000";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            //RESTAPI パラメータ生成
            $aryParm = $baseParm;
            $aryParm['protocol']    = $Protocol;
            $aryParm['hostName']    = $Hostname;
            $aryParm['portNo']      = $PortNo;
            $aryParm['accessKeyId'] = base64_encode(  $UserID . ":" . $UserPW );
            $aryParm['requestURI']  = $baseParm['requestURI'] . $strMenuid ;
            $aryParm['xCommand']   = 'EXECUTE';

            $arrRequestContents = array(
                        'MOVEMENT_CLASS_ID' => $MovementID,
                        'OPERATION_ID'      => $OperationID,
                        'RUN_MODE'          => $runMode
            );

            $aryParm['strParaJsonEncoded'] = json_encode($arrRequestContents,
                                              JSON_UNESCAPED_UNICODE
                                             );

            //RESTAPI実行
            $aryRetBody = $this->execute_restapi( $aryParm );

            //RESTAPI結果
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $aryRetBody['ALLResponsContentsView'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                require ($root_dir_path . $log_output_php );
                $FREE_LOG = $aryRetBody['ALLResponsContents'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                require ($root_dir_path . $log_output_php );
            }

            if( $aryRetBody['StatusCode'] == 200 ){
                //作業実行成功
                if( empty( $aryRetBody['ResponsContents']['EXECUTION_NO'] ) !== true ){
                    $strExecutionNo = $aryRetBody['ResponsContents']['EXECUTION_NO'];
                    $aryErrMsgBody = $aryRetBody['ResponsContents'];
                }else{
                    //作業実行失敗
                    $intErrorType = $aryRetBody['ResponsContents']['RESULTCODE'];
                    $strErrMsg = $aryRetBody['ResponsContents']['RESULTINFO'];
                    $aryErrMsgBody = $aryRetBody['ResponsContents'];
                }

            }else{
                    $intErrorType = $aryRetBody['StatusCode'];
                    $aryErrMsgBody = $aryRetBody['ResponsContents'];
                    $strErrMsg = $aryRetBody['ResponsContents']['Exception'];  
            }
        }
        catch (Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $aryErrMsgBody[] = $e->getMessage();

            //エラーメッセージ
            if ( $log_level === 'DEBUG' ){
                require ($root_dir_path . $log_output_php );
            }
        }

        $intErrorType = str_pad($intErrorType, 3, 0, STR_PAD_LEFT);

        $retArray = array($intErrorType,$strExecutionNo,$strErrMsg,$aryErrMsgBody);
        //返り値出力
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = json_encode($retArray,JSON_UNESCAPED_UNICODE );
            require ($root_dir_path . $log_output_php );
        }

        //終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = " CI/CD ECECUTE RESTAPI END ";
            require ($root_dir_path . $log_output_php );
        }

        return $retArray;
    }
    //作業実行を登録するRESTAPIの実行----

    //----資材登録するRESTAPIの実行
    /*
        IN
            $materialType,              //素材種別             ex) const C_MATL_TYPE_ROW_ID_XXXXX       = X;
            $filename,                  //資材名               ex) string
            $base64file,                //base64_encode(資材)
            $RequestData,               //データ配列(テーブルの項目のみ)
            $filterList                 //FILTER条件　array（　対話ファイル種別ID , OS種別ID ）
            $UserName,                  //RESTユーザ名
            $UserID,                    //RESTユーザ名
            $UserPW,                    //RESTユーザパスワード
            $Hostname,                  //ITAホスト名
            $Protocol,                  //プロトコル
            $PortNo,                    //ポート番号
            $NoUpdeteFlg,               //更新不要レコード判定 true:不要 false:必要
        OUT
            Array(
                [0] =>  正常終了:000  / 正常以外：エラーコード
                [1] =>  実行種別
                [2] =>  ID
                [3] =>  UI用メッセージ    EX) 異常時：エラーメッセージ

                [4] =>  Array(
                            2の詳細 or その他
                        ) 
            )  
    */
    function materialsRestAccess( $materialType,$filename,$base64file,$RequestData,$filterList=array() ,$UserName, $UserID,$UserPW,$Hostname,$Protocol,$PortNo , &$NoUpdeteFlg ){
        
        global $root_dir_path;
        global $log_output_php;
        global $log_output_dir;
        global $log_file_prefix;
        global $log_level;
        
        $strMaterialID = "";
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strRegisterDate = "";
        $strExpectedErrMsgBodyForUI = "";

        $arrRequestContents = array();

        $arrDataBody= array();
        $arrDataUploadFile = array();

        $strMenuid ='';
        $fileUploadNo='';
        $fileNameNo='';
        $lastUpdateNo = '';
        $lastUpdateUserNo= '';
        $editType='';
        $RecordLength = 0;
        $restExecFlg = 0;
        $lastUserDiffFlg = 0;
        
        $NoUpdeteFlg = false;

        $strFxName = '([CLASS]'.__CLASS__.',[FUNCTION]'.__FUNCTION__.')';

        if ( empty($root_dir_path) ){
            $root_dir_temp = array();
            $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
            $root_dir_path = $root_dir_temp[0] . "ita-root";
        }
        if ( empty($log_output_dir) ){
            $log_output_dir = getenv('LOG_DIR');
        }
        if ( empty($log_output_php) ){
            $log_output_php     = '/libs/backyardlibs/backyard_log_output.php';
        }
        if ( empty($log_file_prefix) ){
           $log_file_prefix = basename( __FILE__, '.php' ) . "_";
        }
        
        $arrTargetMenuID =array(
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY   =>      '2100020104',
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER  =>      '2100020205',
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE     =>      '2100020303',
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT  =>      '2100040703',
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE =>      '2100040704',
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE   =>      '2100080005',
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY   =>      '2100080006',
        );


        $arrTargeFileColLIST =array(
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY   =>      array('FILTER_KEY'=> '3','UPLOAD'=> '4'),
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER  =>      array('FILTER_KEY'=> array('3','4'),'UPLOAD'=> '5'),
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE     =>      array('FILTER_KEY'=> '3','UPLOAD'=> '4'),
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT  =>      array('FILTER_KEY'=> '3','UPLOAD'=> '4'),
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE =>      array('FILTER_KEY'=> '3','UPLOAD'=> '4'),
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE   =>      array('FILTER_KEY'=> '3','UPLOAD'=> '4'),
            TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY   =>      array('FILTER_KEY'=> '3','UPLOAD'=> '4'),
        );




        try{

            $objMTS = $this->getMessageTemplateStorage();
            // 開始メッセージ
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = " CI/CD MATERIAL EDIT RESTAPI START";#$objMTS->getSomeMessage("ITAWDCH-STD-50001");
                require ($root_dir_path . $log_output_php );
            }

            $baseParm=array(
                'protocol'           => '',
                'hostName'           => '',
                'portNo'             => '',
                'requestURI'         => '/default/menu/07_rest_api_ver1.php?no=',
                'method'             => 'POST',
                'contentType'        => 'application/json',                
                'accessKeyId'        => '',
                'xCommand'           => '',
                'strParaJsonEncoded' => '',
            );

            $arrEditMenuList =array(
                $objMTS->getSomeMessage("ITAWDCH-STD-12202"),   //登録/Register
                $objMTS->getSomeMessage("ITAWDCH-STD-12203"),   //更新/Update
                $objMTS->getSomeMessage("ITAWDCH-STD-12204"),   //廃止/Discard
                $objMTS->getSomeMessage("ITAWDCH-STD-12205"),   //復活/Restore
            );

            //簡易バリデーション（数値）
            if (is_numeric($materialType) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5009", array($materialType) );#"資材タイプの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000100";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( is_numeric($PortNo) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5004", array($PortNo) );#"ポート番号の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            //簡易バリデーション（文字列）
            if ( empty($filename) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5010");#"ファイル名の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            if ( is_array($RequestData) !== true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5012");#"RESTAPIのパラメータが不正です。";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            if ( empty($UserName) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5013");#"ユーザ名の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }

            if ( empty($UserID) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5005");#"ユーザIDの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( empty($UserPW) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5006");#""ユーザPWの設定値が不正です。";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000700";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( empty($Hostname) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5007");#"ホスト名の設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000800";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            if ( empty($Protocol) === true ) {
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5008");#" Protocol is empty. ";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000900";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }elseif( array_search( strtolower($Protocol), array('http','https') ) === false ){
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5008", array($Protocol));#"プロトコルの設定値が不正です。({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00000900";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            //対象メニューID取得 + ファイルアップロードカラムNo取得
            if( isset( $arrTargetMenuID[ $materialType ] ) === true ){
                $strMenuid = $arrTargetMenuID[ $materialType ];
                $fileUploadNo = $arrTargeFileColLIST[ $materialType ]['UPLOAD'];
                $filterInfo   = $arrTargeFileColLIST[ $materialType ]['FILTER_KEY'];

            }else{
                $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5000", array($DriverType) );#"ドライバー種別が不正な値です。 ({})";
                $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                // 例外処理へ
                $strErrStepIdInFx="00001000";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            if( $materialType == TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER ){
                $filterlisst3="";
                $filterlisst4="";
                if( empty( $filterList[3] ) !== true ){
                    $filterlisst3 = $filterList[3];
                }
                if( empty( $filterList[4] ) !== true ){
                    $filterlisst4 = $filterList[4];
                }
                if( empty( $filterlisst3 ) === true || empty( $filterlisst4 ) === true ){
                    $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5017", array($filterlisst3,$filterlisst4) );#"対話種別、OS種別の設定値が不正です。(対話種別：{},OS種別:{})";
                    $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                    // 例外処理へ
                    $strErrStepIdInFx="00001000";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }elseif( is_numeric($filterlisst3) !== true ||  is_numeric( $filterlisst4) !== true ){
                    $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5017", array($filterlisst3,$filterlisst4) );#"対話種別、OS種別の設定値が不正です。(対話種別：{},OS種別:{})";
                    $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                    // 例外処理へ
                    $strErrStepIdInFx="00001000";
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }
            //共通パラメータ
            $aryParm = $baseParm;
            $aryParm['protocol']    = $Protocol;
            $aryParm['hostName']    = $Hostname;
            $aryParm['portNo']      = $PortNo;
            $aryParm['accessKeyId'] = base64_encode(  $UserID . ":" . $UserPW );
            $aryParm['requestURI']  = $baseParm['requestURI'] . $strMenuid ;

            //退避
            $aryParmEdit   = $aryParm;
            $aryParmFilter = $aryParm;
            
            //RESTAPI パラメータ生成 ( FILTER )
            $aryParmFilter['xCommand']   = 'FILTER';

            if( $materialType == TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER ){
                foreach ($filterInfo as $filterkeyNo ) {
                   $arrRequestContents[$filterkeyNo] = array();
                   $arrRequestContents[$filterkeyNo]['LIST'] = array( $filterList[$filterkeyNo] ) ;
                }
            }else{
                $arrRequestContents = array( 
                    $filterInfo => array(
                        'LIST' => array(
                            $filename,
                         )
                    )
                );

            }

            $aryParmFilter['strParaJsonEncoded'] = json_encode($arrRequestContents,
                                              JSON_UNESCAPED_UNICODE
                                             );

            //RESTAPI実行  ( FILTER )
            $aryRetBody = $this->execute_restapi( $aryParmFilter );
            //RESTAPI結果
            if ( $log_level === 'DEBUG' ){
                $FREE_LOG = $aryRetBody['ALLResponsContentsView'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                require ($root_dir_path . $log_output_php );
                $FREE_LOG = $aryRetBody['ALLResponsContents'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                require ($root_dir_path . $log_output_php );
            }

            /*        
                1.レコードなし          
                    レコード登録      
                            
                2.レコードあり(廃止)          
                    素材集のレコード復活      ※復活の場合には、最終更新者はチェックしない。     
                    資材の差分・レコード差分確認      
                        差分あり    2-1     素材集のレコード更新  
                        差分なし    2-2     未処理
                3.レコードあり(!=廃止)            
                    最終更新者が自分(Restユーザ)か確認。
                        不一致の場合はエラー      3-1
                    資材の差分・レコード差分確認      
                        差分あり     3-2    素材集のレコード更新
                        差分なし     3-3    未処理
            */

            if( $aryRetBody['StatusCode'] == 200 ){

                //取得結果件数
                if( isset($aryRetBody['ResponsContents']['CONTENTS']['RECORD_LENGTH']) ){
                    $RecordLength = $aryRetBody['ResponsContents']['CONTENTS']['RECORD_LENGTH'];
                }
                
                $targetNum = 0;
                
                //FILTER成功(レコードなし) [1]
                if( $RecordLength == 0 ){
                    //レコード 0件
                    $editType = $objMTS->getSomeMessage("ITAWDCH-STD-12202"); //登録/Register

                //FILTER成功(レコードあり) 
                }elseif( $RecordLength >= 1 ){

                    $arrDataBody     = $aryRetBody['ResponsContents']['CONTENTS']['BODY']; 
                    $arrDataUploadFile = $aryRetBody['ResponsContents']['CONTENTS']['UPLOAD_FILE'];

                    $lastUpdateNo     = array_search($objMTS->getSomeMessage("ITAWDCH-STD-11901") ,  $arrDataBody[0] );//更新用の最終更新日時
                    $lastUpdateUserNo = array_search($objMTS->getSomeMessage("ITAWDCH-STD-12101") ,  $arrDataBody[0] );//最終更新者

                    //対象のkey指定 
                    if( $RecordLength == 1 ){
                        $targetNum = 1;
                    }else{
                        //1件以上の場合
                        foreach ($arrDataBody as $tmpkey => $tmpval) {
                            if( $tmpkey != 0 ){
                                //廃止でないもの
                                if( $tmpval[1] == "" ){
                                    $targetNum = $tmpkey;
                                }        
                            }
                        }
                        //すべて廃止の場合、最新を対象とする
                        if( $targetNum == 0 ){
                            $targetNum = $RecordLength;
                        }
                    }

                    //レコードあり [2]
                    if( $arrDataBody[$targetNum][1] != "" ){
                        //レコードあり(廃止) 
                         $editType = $objMTS->getSomeMessage("ITAWDCH-STD-12205");   //復活/Restore
                        
                        //ファイル差分ある場合、復活 ＋ 更新（後続）処理  更新のフラグON  1:復活＋更新[2-1] / 0:復活[2-2]
                        if( $arrDataUploadFile[$targetNum][$fileUploadNo] != $base64file ){
                            $restExecFlg = 1; //復活＋更新
                        }

                    }else{
                        //レコードあり [3]
                        $editType = $objMTS->getSomeMessage("ITAWDCH-STD-12203");   //更新/Update
                        
                        // 最終更新者に差分ある場合、復活＋更新処理のフラグON [3-1]
                        #if( $RequestData[$lastUpdateUserNo] != $arrDataBody[1][$lastUpdateUserNo] ){
                        if( $UserName != $arrDataBody[$targetNum][$lastUpdateUserNo] ){
                            $lastUserDiffFlg = 1;
                        }
                        
                        //ファイル差分無しの場合SKIP  更新のフラグON  [3-3]
                        if( $arrDataUploadFile[$targetNum][$fileUploadNo] == $base64file ){
                            $restExecFlg = 2; //SKIP
                        }

                        //RequestData、FILTER結果差分件数チェック
                        $intDiffcnt = 0;
                        foreach ($RequestData as $tmpkey => $tmpval) {
                           // 項目値が空の場合はNULLで返却される。
                           if(array_key_exists($tmpkey, $arrDataBody[$targetNum]) == true ){
                                if( $arrDataBody[$targetNum][$tmpkey] != $tmpval ){
                                    $intDiffcnt++;
                                }
                            }
                        }
                        if( $intDiffcnt != 0 ){
                             $restExecFlg = 0; //SKIP
                        }
                    }

                    //項目名（INFO）削除
                    if( count( $arrDataBody ) != 1 )unset($arrDataBody[0]);

                }else{
                    //レコード 0-1件以外
                        $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5016", array($RecordLength) );#"対象レコードが1件以上である為、処理を中断します。";
                        $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                        // 例外処理へ
                        $strErrStepIdInFx="00001000";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }else{
                    $intErrorType = $aryRetBody['StatusCode'];
                    $aryErrMsgBody = $aryRetBody['ResponsContents'];
                    $strErrMsg = $aryRetBody['ResponsContents']['Exception']; 
            }

            //EDITの実行種別の場合
            if(  array_search($editType, $arrEditMenuList ) !== false ){

                //最終更新者フラグONでない場合、REST実行
                if( $lastUserDiffFlg == 0 ){
                    //RESTPAI実行
                    if( $restExecFlg != 2 ){
                        $arrRequestContentsEdit = array();
                        
                        //EDITパラメータ
                        $tmpRequestData = $RequestData;

                        //実行種別
                        $tmpRequestData[0] = $editType;

                        //ID (登録以外)
                        if( $editType != $objMTS->getSomeMessage("ITAWDCH-STD-12202") ){
                            $tmpRequestData[2]     = $arrDataBody[$targetNum][2];
                            //更新用の最終更新日時
                            $tmpRequestData[$lastUpdateNo]     = $arrDataBody[$targetNum][$lastUpdateNo];
                            #$RequestData[$lastUpdateNo]     = "T_20210618103458338508";
                            $strMaterialID = $arrDataBody[$targetNum][2];
                        }

                        //廃止/復活時のみ
                        if( $editType == $objMTS->getSomeMessage("ITAWDCH-STD-12204")
                           || $editType == $objMTS->getSomeMessage("ITAWDCH-STD-12205")  ){
                            foreach ( $tmpRequestData as $keynum => $tmpval) {
                                if( array_search( $keynum , array( 0,2,$lastUpdateNo ) ) === false ){
                                    unset( $tmpRequestData[$keynum] );
                                }
                            }
                        }

                        ksort($tmpRequestData);
                        $arrRequestContentsEdit[] = $tmpRequestData;
                        
                        //登録/更新時のみ
                        if( $editType == $objMTS->getSomeMessage("ITAWDCH-STD-12202") 
                           ||  $editType == $objMTS->getSomeMessage("ITAWDCH-STD-12203") ){
                            $arrRequestContentsEdit['UPLOAD_FILE'][][$fileUploadNo] = $base64file;
                        }

                        //復活更新の場合の為、退避
                        $tmpParmEdit = $aryParmEdit;
                        $tmpParmEdit['xCommand']   = 'EDIT';
                        $tmpParmEdit['strParaJsonEncoded'] = json_encode($arrRequestContentsEdit,
                                                          JSON_UNESCAPED_UNICODE
                                                         );

                        //RESTAPI実行  ( EDIT  )
                        $aryRetBody = $this->execute_restapi( $tmpParmEdit );

                        //RESTAPI結果
                        if ( $log_level === 'DEBUG' ){
                            $FREE_LOG = $aryRetBody['ALLResponsContentsView'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                            require ($root_dir_path . $log_output_php );
                            $FREE_LOG = $aryRetBody['ALLResponsContents'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                            require ($root_dir_path . $log_output_php );
                        }

                        $resultInfo = array();
                        if( $aryRetBody['StatusCode'] == 200 ){
                            //実行成功
                            if( isset( $aryRetBody['ResponsContents']['LIST']['NORMAL'] ) === true ){
                                foreach ($aryRetBody['ResponsContents']['LIST']['NORMAL'] as $typekey => $arrCt) {
                                    if( $arrCt['ct'] != 0 ){
                                        #$resultInfo[$typekey] = $arrCt['name'] .":". $arrCt['ct'];
                                        if( array_search( $arrCt['name'] , $arrEditMenuList ) != false ){
                                            #$resultInfo[$typekey] = $arrCt['name']."処理が完了しました。";#$arrCt['name'] .":". $arrCt['ct'];
                                            $resultInfo[$typekey] = $aryRetBody['ResponsContents']['LIST']['RAW'][0][2];
                                        }else{
                                            $intErrorType = $aryRetBody['ResponsContents']['LIST']['RAW'][0][0];
                                            #$resultInfo[$typekey] = $editType."処理がエラー発生の為、完了しませんでした。";#$arrCt['name'] .":". $arrCt['ct'];
                                            $resultInfo[$typekey] = $aryRetBody['ResponsContents']['LIST']['RAW'][0][2];
                                        }
                                    }
                                }
                                $strErrMsg =  implode( "," , $resultInfo );
                                $aryErrMsgBody = $aryRetBody['ResponsContents']['LIST']['RAW'][0];
                            }else{
                                //実行失敗
                                $intErrorType = '001';
                                $strErrMsg = implode( "," , $resultInfo );
                                $aryErrMsgBody = $aryRetBody['ResponsContents']['LIST']['RAW'][0];
                            }

                        }else{
                                $intErrorType = $aryRetBody['StatusCode'];
                                $aryErrMsgBody = $aryRetBody['ResponsContents'];
                                $strErrMsg = $aryRetBody['ResponsContents']['Exception'];  
                        }

                        //FILTER再取得取得
                        if( $intErrorType == "000" ||  $intErrorType == "" ){
                            //RESTAPI実行  ( FILTER )
                            $aryRetBody = $this->execute_restapi( $aryParmFilter );
                            //RESTAPI結果
                            if ( $log_level === 'DEBUG' ){
                                $FREE_LOG = $aryRetBody['ALLResponsContentsView'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                                require ($root_dir_path . $log_output_php );
                                $FREE_LOG = $aryRetBody['ALLResponsContents'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                                require ($root_dir_path . $log_output_php );
                            }

                            if( $aryRetBody['StatusCode'] == 200 ){

                                $RecordLength = 0;
                                //取得結果件数
                                if( isset($aryRetBody['ResponsContents']['CONTENTS']['RECORD_LENGTH']) ){
                                    $RecordLength = $aryRetBody['ResponsContents']['CONTENTS']['RECORD_LENGTH'];
                                }
                                
                                //FILTER成功(レコードあり)
                                if( $RecordLength >= 1 ){
                                    $arrDataBody     = $aryRetBody['ResponsContents']['CONTENTS']['BODY']; 
                                    $arrDataUploadFile = $aryRetBody['ResponsContents']['CONTENTS']['UPLOAD_FILE'];

                                    //対象レコードの指定 
                                    if( $RecordLength == 1 ){
                                        $targetNum = 1;
                                    }else{
                                        //1件以上の場合
                                        foreach ($arrDataBody as $tmpkey => $tmpval) {
                                            if( $tmpkey != 0 ){
                                                //廃止でないもの
                                                if( $tmpval[1] == "" ){
                                                    $targetNum = $tmpkey;
                                                }        
                                            }
                                        }
                                        //すべて廃止の場合、最新を対象とする
                                        if( $targetNum == 0 ){
                                            $targetNum = $RecordLength;
                                        }
                                    }
                                    
                                    //レコードあり
                                    if( $arrDataBody[$targetNum][1] == "" ){
                                        $strMaterialID = $arrDataBody[$targetNum][2];
                                    }
                                }
                            }
                        }

                        //復活時、ファイル差分あり時の後続更新処理
                        if( $restExecFlg == 1 && $intErrorType != '000' ){
                            $arrRequestContentsEdit = array();
                            //EDITパラメータ
                            $tmpRequestData = $RequestData;

                            //実行種別
                            $tmpRequestData[0] = $objMTS->getSomeMessage("ITAWDCH-STD-12203");   //更新/Update;

                            $tmpRequestData[2] = $arrDataBody[$targetNum][2];
                            //更新用の最終更新日時
                            $tmpRequestData[$lastUpdateNo]     = $arrDataBody[$targetNum][$lastUpdateNo];
                            $strMaterialID = $arrDataBody[$targetNum][2];

                            ksort($tmpRequestData);
                            $arrRequestContentsEdit[] = $tmpRequestData;
                            
                            //登録/更新時のみ
                            $arrRequestContentsEdit['UPLOAD_FILE'][][$fileUploadNo] = $base64file;

                            //復活更新の場合の為、退避
                            $tmpParmEdit = $aryParmEdit;
                            $tmpParmEdit['xCommand']   = 'EDIT';
                            $tmpParmEdit['strParaJsonEncoded'] = json_encode($arrRequestContentsEdit,
                                                              JSON_UNESCAPED_UNICODE
                                                             );
                            
                            //RESTAPI実行  ( EDIT  )
                            $aryRetBody = $this->execute_restapi( $tmpParmEdit );
                            //RESTAPI結果
                            if ( $log_level === 'DEBUG' ){
                                $FREE_LOG = $aryRetBody['ALLResponsContentsView'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                                require ($root_dir_path . $log_output_php );
                                $FREE_LOG = $aryRetBody['ALLResponsContents'].' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                                require ($root_dir_path . $log_output_php );
                            }

                            $resultInfo = array();
                            if( $aryRetBody['StatusCode'] == 200 ){
                                //実行成功
                                if( isset( $aryRetBody['ResponsContents']['LIST']['NORMAL'] ) === true ){
                                    foreach ($aryRetBody['ResponsContents']['LIST']['NORMAL'] as $typekey => $arrCt) {
                                        if( $arrCt['ct'] != 0 ){
                                            #$resultInfo[$typekey] = $arrCt['name'] .":". $arrCt['ct'];
                                            if( array_search( $arrCt['name'] , $arrEditMenuList ) != false ){
                                                #$resultInfo[$typekey] = $arrCt['name']."処理が完了しました。";#$arrCt['name'] .":". $arrCt['ct'];
                                                $resultInfo[$typekey] = $aryRetBody['ResponsContents']['LIST']['RAW'][0][2];
                                            }else{
                                                $intErrorType = $aryRetBody['ResponsContents']['LIST']['RAW'][0][0];
                                                #$resultInfo[$typekey] = $editType."処理がエラー発生の為、完了しませんでした。";#$arrCt['name'] .":". $arrCt['ct'];
                                                $resultInfo[$typekey] = $aryRetBody['ResponsContents']['LIST']['RAW'][0][2];
                                            }
                                        }
                                    }
                                    $strErrMsg =  implode( "," , $resultInfo );
                                    $aryErrMsgBody = $aryRetBody['ResponsContents']['LIST']['RAW'][0];
                                }else{
                                    //実行失敗
                                    $intErrorType = '001';
                                    $strErrMsg = implode( "," , $resultInfo );
                                    $aryErrMsgBody = $aryRetBody['ResponsContents']['LIST']['RAW'][0];
                                }

                            }else{
                                    $intErrorType = $aryRetBody['StatusCode'];
                                    $aryErrMsgBody = $aryRetBody['ResponsContents'];
                                    $strErrMsg = $aryRetBody['ResponsContents']['Exception'];  
                            }
                        }

                    }else{

                        //ファイル差分無し時にSKIP
                        $NoUpdeteFlg = true;

                        $strMaterialID = $arrDataBody[$targetNum][2];
                        $intErrorType = '000';
                        $strErrMsg = $objMTS->getSomeMessage("ITACICDFORIAC-ERR-5014" );#"項目、ファイルに差分がないため、RESTAPI（EDIT）の実施をSKIPします。";
                    } 
                }else{
                    //レコード 0/1件以外
                        $intErrorType = '002';
                        $strErrMsg =$objMTS->getSomeMessage("ITACICDFORIAC-ERR-5015" );#"最終更新者が異なるため、RESTAPI（EDIT）の実施をSKIPします。";
                        $FREE_LOG = $strErrMsg .' ([FILE]'.__FILE__.',[LINE]'.__LINE__.')';
                        // 例外処理へ
                        $strErrStepIdInFx="00001000";
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
            }
        }
        catch (Exception $e){
            if( $intErrorType===null ) $intErrorType = 501;
            $aryErrMsgBody[] = $e->getMessage();

            //エラーメッセージ
            if ( $log_level === 'DEBUG' ){
                require ($root_dir_path . $log_output_php );
            }
        }

        $intErrorType = str_pad($intErrorType, 3, 0, STR_PAD_LEFT);

        $retArray = array($intErrorType,$editType,$strMaterialID,$strErrMsg,$aryErrMsgBody);
        
        //返り値出力
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = json_encode($retArray,JSON_UNESCAPED_UNICODE );
            require ($root_dir_path . $log_output_php );
        }
        
        //終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = " CI/CD MATERIAL EDIT RESTAPI END ";
            require ($root_dir_path . $log_output_php );
        }


        return $retArray;
    }
    //作業実行を登録するRESTAPIの実行----

    //---RESTAPIの実行
    /*
        $aryParm = array(
                    'protocol'           => '', ex) http / https
                    'hostName'           => '', ex) hostname / XX.XX>XX.XX 
                    'portNo'             => '', ex) 80 / 443 
                    'requestURI'         => '', ex) /default/menu/07_rest_api_ver1.php?no=',
                    'method'             => '', ex) POST
                    'contentType'        => '', ex) application/json                
                    'accessKeyId'        => '', ex) base64_encode("userid:userpassward");
                    'xCommand'           => '', ex) INFO / FILTER / EDIT /EXECUTE /  ...etc
                    'strParaJsonEncoded' => '', ex)  json_encode( array  , JSON_UNESCAPED_UNICODE )
        )
    */
    function execute_restapi( $aryParm ){

        ////////////////////////////////
        // 返却用のArrayを定義        //
        ////////////////////////////////
        $respons_array = array();

        ////////////////////////////////
        // パラメータチェック         //
        ////////////////////////////////
        $check_err_flag = 0;
        if( empty( $aryParm['protocol'] ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "Protocol is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $aryParm['hostName'] ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "HostName is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $aryParm['portNo'] ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "PortNo is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $aryParm['accessKeyId'] ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "AccessKeyId is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $aryParm['requestURI'] ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "RequestURI is empty" );
            $check_err_flag = 1;
        }
        else if( empty( $aryParm['method'] ) ){
            $respons_array['StatusCode']      = ( int ) -1;
            $respons_array['ResponsContents'] = array( "ErrorMessage" => "Method is empty" );
            $check_err_flag = 1;
        }

        if( $check_err_flag == 0 ){
            ////////////////////////////////
            // RequestHeader作成          //
            ////////////////////////////////
            $Header = array("Host: "            . $aryParm['hostName'] . ":" . $aryParm['portNo'],
                            "Content-Type: "    . $aryParm['contentType'],
                            "Authorization: "   . $aryParm['accessKeyId'],
                            "X-Command: "       . $aryParm['xCommand'] ,
                           );

            ////////////////////////////////
            // HTTPコンテキスト作成       //
            ////////////////////////////////
            $HttpContext = array( "http" => array('method'        => $aryParm['method'],
                                                  'header'        => implode("\r\n", $Header),
                                                  'content'       => $aryParm['strParaJsonEncoded'],
                                                  'ignore_errors' => true,
                                                  'timeout'       => 10,

                                                 ),
                                  "ssl" => array('verify_peer' => false,
                                                 'verify_peer_name' => false,
                                                )
                                );

            ////////////////////////////////
            // REST APIアクセス           //
            ////////////////////////////////
            $http_response_header = null;

            #if($devmode == 1 )echo "\n\nRequest URL\n" . $aryParm['protocol'] . "://" . $aryParm['hostName'] . ":" . $aryParm['portNo'] . $aryParm['requestURI'] . "\n";
            $ResponsContents = @file_get_contents( $aryParm['protocol'] . "://" . $aryParm['hostName'] . ":" . $aryParm['portNo'] . $aryParm['requestURI'],
                                                   false,
                                                   stream_context_create($HttpContext) );

            // JSON形式でコンテンツデータとれない事があるのでコンテンツデータをダンプする。必要に応じて呼び元でこのデータをログに出力する。
            ob_start();
            var_dump($ResponsContents);
            $respons_array['ALLResponsContents'] = " REST API ALL Response(DUMP):" . ob_get_contents();
            ob_clean();

            $respons_array['ALLResponsContentsView'] = " REST API ALL Response(VIEW):" . json_encode( json_decode( $ResponsContents, true ),JSON_UNESCAPED_UNICODE );

            ////////////////////////////////
            // 通信結果を判定             //
            ////////////////////////////////
            #if($devmode == 1 )echo print_r($ResponsContents);
            #if($devmode == 1 )echo print_r($http_response_header);

            if( $http_response_header == NULL ){
                $http_response_header =array();
            }

            if( count( $http_response_header ) > 0 ){
                ////////////////////////////////
                // HTTPレスポンスコード取得   //
                ////////////////////////////////
                preg_match('/HTTP\/1\.[0|1|x] ([0-9]{3})/', $http_response_header[0], $matches);
                $status_code = $matches[1];
                $result_array = json_decode( $ResponsContents, true );
                ////////////////////////////////
                // 返却用のArrayを編集        //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) $status_code;
                #if( isset( $result_array['resultdata'])){
                if( $status_code == 200 ){
                    $respons_array['ResponsContents'] = $result_array['resultdata'];
                }else {
                    $respons_array['ResponsContents'] = json_decode( $ResponsContents, true );
                }
                
            } else{
                ////////////////////////////////
                // 返却用のArrayを編集        //
                ////////////////////////////////
                $respons_array['StatusCode']      = ( int ) -2;
                $respons_array['ResponsContents'] = array( "ErrorMessage" => "HTTP Socket Timeout" );
                $respons_array['ResponsContents']['Exception'] = "HTTP Socket Timeout";
            }
           
        }

        ////////////////////////////////
        // 結果を返却                  //
        ////////////////////////////////
        return $respons_array;

    }
    //RESTAPIの実行---

}

?>
