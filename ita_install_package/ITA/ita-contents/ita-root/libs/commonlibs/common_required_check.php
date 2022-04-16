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
if ( empty($root_dir_path) ){
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

// 共通モジュールをロード
require_once ($root_dir_path . "/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php");

class AuthTypeParameterRequiredCheck {
    public  $chkType_Loadtable_DeviceList       = '1';
    public  $chkType_Loadtable_TowerHostList    = '2';
    public  $chkType_WorkflowExec_DevaiceList   = '3';
    public  $chkType_WorkflowExec_TowerHostList = '4';
    private $errMsgCodeAry;

    function __construct(){
        $this->errMsgCodeAry = array();
        // ERROR_TYPE1: (xxxx)認証方式がパスワード認証の場合、パスワードは必須項目です。
        // ERROR_TYPE2: (xxxx)認証方式が鍵認証の場合、公開鍵ファイルは必須項目です。";
        // ERROR_TYPE3: (xxxx)認証方式が鍵認証(パスフレーズあり)の場合、公開鍵ファイルは必須項目です。
        // ERROR_TYPE4: (xxxx)認証方式が鍵認証(パスフレーズあり)の場合、パスフレーズは必須項目です。
        // ERROR_TYPE5: (xxxx)認証方式が選択されていません。
        // ERROR_TYPE6: (xxxx)認証方式がパスワード認証の場合、ログインパスワードの管理は必須項目です。
        // ERROR_TYPE7: (xxxx)認証方式が不正です。
        // ERROR_TYPE8: (xxxx)ログインユーザーIDが未入力です。
        // ERROR_TYPE9: 機器一覧の認証方式が不正です。pioneerでパスワード認証(winrm)は対応していません。(host:{})";
        // ERROR_TYPE10: 機器一覧のPioneer利用情報のプロトコルが選択されていません。(host:{})
        // ERROR_TYPE11: 機器一覧のPioneer利用情報のプロトコルが不正です。(host:{})
        // $AuthType 1:機器一覧ロードテーブル
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE1'] = "ITAANSIBLEH-ERR-56270";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE2'] = "ITAANSIBLEH-ERR-56271";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE3'] = "ITAANSIBLEH-ERR-56272";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE4'] = "ITAANSIBLEH-ERR-56273";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE5'] = "ITAANSIBLEH-ERR-56274";
        // 未使用
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE6'] = "ITAANSIBLEH-ERR-56275";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE7'] = "ITAANSIBLEH-ERR-56276";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE8'] = "ITAANSIBLEH-ERR-56277";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE9'] = "ITAANSIBLEH-ERR-56278";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE10']= "ITAANSIBLEH-ERR-56279";
        $this->errMsgCodeAry[$this->chkType_Loadtable_DeviceList]['ERROR_TYPE11']= "ITAANSIBLEH-ERR-56280";

        // $AuthType 2:Towerホスト一覧ロードテーブル
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE1'] = "ITAANSIBLEH-ERR-56270";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE2'] = "ITAANSIBLEH-ERR-56271";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE3'] = "ITAANSIBLEH-ERR-56272";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE4'] = "ITAANSIBLEH-ERR-56273";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE5'] = "ITAANSIBLEH-ERR-56274";
        // 未使用
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE6'] = "ITAANSIBLEH-ERR-56275";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE7'] = "ITAANSIBLEH-ERR-56276";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE8'] = "ITAANSIBLEH-ERR-56277";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE9'] = "ITAANSIBLEH-ERR-56278";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE10']= "ITAANSIBLEH-ERR-56279";
        $this->errMsgCodeAry[$this->chkType_Loadtable_TowerHostList]['ERROR_TYPE11']= "ITAANSIBLEH-ERR-56280";

        // $AuthType 3:作業実行 機器一覧チェック
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE1'] = "ITAANSIBLEH-ERR-56230";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE2'] = "ITAANSIBLEH-ERR-56231";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE3'] = "ITAANSIBLEH-ERR-56232";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE4'] = "ITAANSIBLEH-ERR-56233";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE5'] = "ITAANSIBLEH-ERR-56234";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE6'] = "ITAANSIBLEH-ERR-56235";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE7'] = "ITAANSIBLEH-ERR-56236";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE8'] = "ITAANSIBLEH-ERR-56237";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE9'] = "ITAANSIBLEH-ERR-56238";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE10']= "ITAANSIBLEH-ERR-56239";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_DevaiceList]['ERROR_TYPE11']= "ITAANSIBLEH-ERR-56240";

        // $AuthType 4:作業実行 Towerホスト一覧チェック
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE1'] = "ITAANSIBLEH-ERR-56250";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE2'] = "ITAANSIBLEH-ERR-56251";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE3'] = "ITAANSIBLEH-ERR-56252";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE4'] = "ITAANSIBLEH-ERR-56253";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE5'] = "ITAANSIBLEH-ERR-56254";
        // 未使用
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE6'] = "ITAANSIBLEH-ERR-56255";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE7'] = "ITAANSIBLEH-ERR-56256";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE8'] = "ITAANSIBLEH-ERR-56257";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE9'] = "ITAANSIBLEH-ERR-56258";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE10']= "ITAANSIBLEH-ERR-56259";
        $this->errMsgCodeAry[$this->chkType_WorkflowExec_TowerHostList]['ERROR_TYPE11']= "ITAANSIBLEH-ERR-56260";
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   機器一覧とTowerホスト一覧で選択されている認証方式の必須入力チェック
    // パラメータ
    //   $AuthType:            呼び元区分
    //                            2:Towerホスト一覧ロードテーブル　
    //                            4:作業実行 Towerホスト一覧チェック
    //                            1:機器一覧ロードテーブル　
    //                            3:作業実行 機器一覧チェック 
    //    $objMTS:               メッセージクラスオブジェクト
    //    $errMsgParameterAry:   エラーメッセージのパラメータ配列
    //                           機器一覧
    //                             array(機器一覧['HOSTNAME'])
    //                           Towerホスト一覧
    //                             array(Towerホスト一覧['ANSTWR_HOSTNAME'])
    //    $strAuthMode:          認証方式
    //                            1:鍵認証 
    //                            2:パスワード認証 
    //                            3:鍵認証(鍵交換済み) 
    //                            4:鍵認証(パスフレーズあり)
    //    $strLoginUser:         ログインユーザー
    //    $strPasswdHoldFlag:    パスワード管理
    //    $strPasswd:            パスワード
    //    $strsshKeyFile:        公開鍵ファイル
    //    $strPassphrase:        パスフレーズ 
    //    $DriverID:             ドライバ識別子
    //                             DF_LEGACY_DRIVER_ID
    //                             DF_LEGACY_ROLE_DRIVER_ID
    //                             DF_PIONEER_DRIVER_ID
    //    $strProtocolID:        Pioneerプロトコル
    //                               "": 未選択
    //                               1:  telnet
    //                               2:  ssh
    //
    // 戻り値
    //   true:   正常
    //   他:     エラー
    ////////////////////////////////////////////////////////////////////////////////
    function DeviceListAuthTypeRequiredParameterCheck($chkType,$objMTS,$errMsgParameterAry,$strAuthMode,$strLoginUser,$strPasswdHoldFlag,$strPasswd,$strsshKeyFile,$strPassphrase,$DriverID,$strProtocolID) {
        $result = "";

        // ログインユーザーIDの入力チェック
        switch($strAuthMode) {
        case DF_LOGIN_AUTH_TYPE_KEY:        //認証方式:鍵認証(パスフレーズなし) 
        case DF_LOGIN_AUTH_TYPE_PW:         //認証方式:パスワード認証
        case DF_LOGIN_AUTH_TYPE_KEY_EXCH:   //認証方式:鍵認証(鍵交換済み)
        case DF_LOGIN_AUTH_TYPE_KEY_PP_USE: //認証方式:鍵認証(パスフレーズあり) 
        case DF_LOGIN_AUTH_TYPE_PW_WINRM:   //認証方式:パスワード認証(winrm)
            if($strLoginUser == "") {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE8'];
                if(strlen($result) != 0) $result .= "\n";
                $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            break;
        }

        // 認証方式毎の必須入力チェック
        switch($strAuthMode) {
        case DF_LOGIN_AUTH_TYPE_KEY: //認証方式:鍵認証(パスフレーズなし) 
            if($strsshKeyFile == "") {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE2'];
                if(strlen($result) != 0) $result .= "\n";
                $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            break;
        case DF_LOGIN_AUTH_TYPE_PW:       //認証方式:パスワード認証
        case DF_LOGIN_AUTH_TYPE_PW_WINRM: //認証方式:パスワード認証(winrm)
            // 機器一覧ロードテーブルからの場合、ロードテーブルで既存のチェック処理があるので、そこでチェック
            if($chkType == $this->chkType_WorkflowExec_DevaiceList) {
                if($strPasswd == "") {
                    $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE1'];
                    if(strlen($result) != 0) $result .= "\n";
                    $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                }
                if($strPasswdHoldFlag == "") {
                    $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE6'];
                    if(strlen($result) != 0) $result .= "\n";
                    $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                }
            }
            break;
        case DF_LOGIN_AUTH_TYPE_KEY_EXCH:   //認証方式:鍵認証(鍵交換済み)
            break;
        case DF_LOGIN_AUTH_TYPE_KEY_PP_USE: //認証方式:鍵認証(パスフレーズあり) 
            if($strsshKeyFile == "") {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE3'];
                $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            if($strPassphrase == "") {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE4'];
                if(strlen($result) != 0) $result .= "\n";
                $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
        case "":  //認証方式: 未選択
            break;
        default:
            ///認証方式が不正
            $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE7'];
            if(strlen($result) != 0) $result .= "\n";
            $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            break;
        }
        // 作業実行からの場合に実行ドライバとPioneerプロトコルと認証方式の組み合わせ確認
        if($chkType == $this->chkType_WorkflowExec_DevaiceList) {
            switch($DriverID) {
            case DF_LEGACY_DRIVER_ID:      // Legacy
            case DF_LEGACY_ROLE_DRIVER_ID: // Role
                // 認証方式選択チェック
                if($strAuthMode == "") {
                    $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE5'];
                    if(strlen($result) != 0) $result .= "\n";
                    $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                }
                break;
            case DF_PIONEER_DRIVER_ID:    // pioneer
                switch($strProtocolID) {
                case '2':  // ssh
                    switch($strAuthMode) {
                    case DF_LOGIN_AUTH_TYPE_PW_WINRM: //認証方式:パスワード認証(winrm)
                        $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE9'];
                        if(strlen($result) != 0) $result .= "\n";
                        $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                        break;
                    case '':
                        $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE5'];
                        if(strlen($result) != 0) $result .= "\n";
                        $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                        break;
                    }
                    break;
                case '1':  // telnet
                    switch($strAuthMode) {
                    case DF_LOGIN_AUTH_TYPE_KEY:        //認証方式:鍵認証(パスフレーズなし) 
                    case DF_LOGIN_AUTH_TYPE_PW:         //認証方式:パスワード認証
                    case DF_LOGIN_AUTH_TYPE_KEY_EXCH:   //認証方式:鍵認証(鍵交換済み)
                    case DF_LOGIN_AUTH_TYPE_KEY_PP_USE: //認証方式:鍵認証(パスフレーズあり) 
                    case DF_LOGIN_AUTH_TYPE_PW_WINRM:   //認証方式:パスワード認証(winrm)
                    case '':
                        break;
//                    case DF_LOGIN_AUTH_TYPE_PW_WINRM:   //認証方式:パスワード認証(winrm)
//                        $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE9'];
//                        if(strlen($result) != 0) $result .= "\n";
//                        $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
//                        break;
                    }
                    break;
                case '':
                    //pioneer利用情報のプロトコル未選択
                    $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE10'];
                    if(strlen($result) != 0) $result .= "\n";
                    $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                    break;
                default:
                    ///認証方式が不正
                    $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE11'];
                    if(strlen($result) != 0) $result .= "\n";
                    $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
                    break;
                }
                break;
            }
        }
        if(strlen($result) == 0) $result = true;
        return $result;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Towerホスト一覧で選択されている認証方式の必須入力チェック
    // パラメータ
    //   $chkType:            呼び元区分
    //                            2:Towerホスト一覧ロードテーブル　
    //                            4:作業実行 Towerホスト一覧チェック
    //                            1:機器一覧ロードテーブル　
    //                            3:作業実行 機器一覧チェック 
    //    $objMTS:               メッセージクラスオブジェクト
    //    $errMsgParameterAry:   エラーメッセージのパラメータ配列
    //                           機器一覧
    //                             array(機器一覧['HOSTNAME'])
    //                           Towerホスト一覧
    //                             array(Towerホスト一覧['ANSTWR_HOSTNAME'])
    //    $strAuthMode:          認証方式
    //                            1:鍵認証 
    //                            2:パスワード認証 
    //                            3:鍵認証(鍵交換済み) 
    //                            4:鍵認証(パスフレーズあり)
    //    $strPasswd:            パスワード
    //    $strsshKeyFile:        公開鍵ファイル
    //    $strPassphrase:        パスフレーズ
    //
    // 戻り値
    //   true:   正常
    //   他:     エラー
    ////////////////////////////////////////////////////////////////////////////////
    function TowerHostListAuthTypeRequiredParameterCheck($chkType,$objMTS,$errMsgParameterAry,$strAuthMode,$strPasswd,$strsshKeyFile,$strPassphrase,$del_password_arr=null) {

        $result = "";
        switch($strAuthMode) {
        case DF_LOGIN_AUTH_TYPE_KEY:        //認証方式:鍵認証 
            if($strsshKeyFile == "") {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE2'];
                if(strlen($result) != 0) $result .= "\n";
                $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            break;
        case DF_LOGIN_AUTH_TYPE_PW:         //認証方式:パスワード認証
            if($strPasswd == "" || (is_array($del_password_arr) && in_array("del_password_flag_COL_IDSOP_12",$del_password_arr))) {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE1'];
                if(strlen($result) != 0) $result .= "\n";
                $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            break;
        case DF_LOGIN_AUTH_TYPE_KEY_EXCH:   //認証方式:鍵認証(鍵交換済み)
            break;
        case DF_LOGIN_AUTH_TYPE_KEY_PP_USE: //認証方式:鍵認証(パスフレーズあり) 
            if($strsshKeyFile == "") {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE3'];
                if(strlen($result) != 0) $result .= "\n";
                $result = $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            if($strPassphrase == "" || (is_array($del_password_arr) && in_array("del_password_flag_COL_IDSOP_14",$del_password_arr))) {
                $error_cde = $this->errMsgCodeAry[$chkType]['ERROR_TYPE4'];
                if(strlen($result) != 0) $result .= "\n";
                $result .= $objMTS->getSomeMessage($error_cde,$errMsgParameterAry);
            }
            break;
        }
        if(strlen($result) == 0) $result = true;
        return $result;
    }
}
class TowerHostListGitInterfaceParameterCheck {
    function getColumnDataFunction($strModeId,$columnName, $Type, $DelFlagCloumnName, $arrayVariant, $arrayRegData) {
        $UIbase = "";
        $DBbase = "";
        switch($strModeId){
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
        case "DTUP_singleRecDelete":
            $UIbase   = array_key_exists($columnName,$arrayRegData)?$arrayRegData[$columnName]:null;
            break;
        }
        switch($strModeId){
        case "DTUP_singleRecUpdate":
        case "DTUP_singleRecRegister":
        case "DTUP_singleRecDelete":
            $DBbase   = isset($arrayVariant['edit_target_row'][$columnName])?$arrayVariant['edit_target_row'][$columnName]:null;
            break;
        }
        $ret_array = array();
        $ret_array['UI'] = $UIbase;
        $ret_array['DB'] = $DBbase;
        // DBに反映されるデータ
        // PasswordColumnの場合
        // 更新されていない場合はarrayRegDataはNullになるので設定済みのパスワード($arrayVariant['edit_target_row'])取得
        $del_flag = false;
        // 削除チェックボタン有無判定
        if(! empty($Type[$DelFlagCloumnName])) {
            if( isset($arrayRegData[$Type[$DelFlagCloumnName]])) {
                // 削除チェックボタンの状態判定
                if($arrayRegData[$Type[$DelFlagCloumnName]] == "on"){
                    $del_flag = true;
                }
            }
        }
        if(! empty($Type[$DelFlagCloumnName])) {
            if(strlen($ret_array['UI'])==0) {
                $ret_value = $ret_array['DB'];
            } else {
                $ret_value = $ret_array['UI'];
            }
        } else {
            $ret_value = $ret_array['UI'];
        }
        if($del_flag === true) {
            $ret_value = "";
        }
        return $ret_value;
    }

    ////////////////////////////////////////////////////////////////////////////////
    // 処理内容
    //   Towerホスト一覧で設定されているGit接続情報の入力チェック
    //
    //   
    // 戻り値
    //   true:   正常
    //   他:     エラー
    ////////////////////////////////////////////////////////////////////////////////
    function ParameterCheck($strExecMode, $ColumnArray ,$ValueColumnName, $MyNameCloumnName, $RequiredCloumnName) {

        global $g;
        global $root_dir_path;
        $retBool = true;
        $retStrBody = '';

        require_once ($root_dir_path . '/libs/backyardlibs/ansible_driver/ky_ansible_common_setenv.php' );

        if($strExecMode == DF_EXEC_MODE_AAC) {
            // 実行エンジンがAnsible Automation Controlleの場合、必須入力の項目チェック
            foreach($ColumnArray as $item=>$Type) {
                if(($ColumnArray[$item][$ValueColumnName] == "") && ($ColumnArray[$item][$RequiredCloumnName] === true)) {
                    $errormsg = $Type[$MyNameCloumnName];
                    if(strlen($retStrBody) != 0) { $retStrBody .= "\n";}
                    $retStrBody .= $errormsg;
                    $retBool = false;
                }
            }
        }
        if($retBool === false) {
            return $retStrBody;
        }
        return $retBool;
    }
}
?>
