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
    //  【引渡パラメータ】
    //      $this->strBufferRequirePath
    //      Type        : string
    //      Description : 型および値を変更しないこと。
    //
    //  【返却パラメータ】
    //      $this->intResultStatusCode
    //      Type        : integer
    //      Description : httpレスポンス・ステータス
    //         400：Bad Request：渡されたパラメータが異なるなど、要求が正しくない場合に返却される
    //         401：Unauthorized：適切な認証情報を提供せず、保護されたリソースに対しアクセスをした場合に返却される
    //         404：指定されたリソースが見つからない場合に返却される
    //         405：Method Not Allowed：要求したリソースがサポートしていない HTTP メソッドを利用した場合に返却される
    //         500：Internal Server Error：API 実行時に予期しないエラーが発生した場合に返却される
    //
    //     $this->arySuccessInfo
    //      Type        : array
    //      Description : 成功時のステータス
    //
    //     $this->aryErrorInfo
    //      Type        : array
    //      Description : エラー発生時のステータス
    //         Error     ：Error message
    //         Exception ：Exception classname
    //         StackTrace：Error StackTrace
    //
    //     $boolExeContinue
    //      Type        : boolean
    //      Description : 
    //
    //////////////////////////////////////////////////////////////////////

    global $vg_log_level;

    if($vg_log_level == 'DEBUG')  $this->RestAPI_log("START[".basename(__FILE__)."]");

    $root_dir_path = $this->getApplicationRootDirPath();

    $aryReceptData                  = $this->getReceptData();

    $in_engine_virtualenv_name      = $aryReceptData['ANS_ENGINE_VIRTUALENV_NAME'];

    $EncodeValue = "";
    $ret = false;
    if(file_exists($in_engine_virtualenv_name) === true) {
        if(is_dir($in_engine_virtualenv_name) ===false) {
            $EncodeValue = "Virtualenv path is not a directory.";
        } else {
            if(file_exists($in_engine_virtualenv_name . "/bin/activate") === false) {
                $EncodeValue = "Virtualenv path is not a virtualenv directory.";
            } else {
                if(file_exists($in_engine_virtualenv_name . "/bin/ansible-playbook") === false) {
                    $EncodeValue = "Ansible environment is not found in the virtualenv path.";
                } else {
                    $ret = true;
                }
           }
        }
    } else {
       $EncodeValue = "Virtualenv path not found.";
    }

    if($ret === true) {
        $this->arySuccessInfo['status'] = "SUCCEED";
    } else {
        $this->arySuccessInfo['status'] = "FAILED";
    }
    $this->intResultStatusCode = 200;
    $this->arySuccessInfo['resultdata'] = $EncodeValue;

    if($vg_log_level == 'DEBUG')  $this->RestAPI_log("END[".basename(__FILE__)."]");
?>
