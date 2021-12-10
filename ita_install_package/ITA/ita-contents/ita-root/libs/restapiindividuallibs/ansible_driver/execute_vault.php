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

    require_once ($root_dir_path . '/libs/commonlibs/common_ansible_vault.php');
    
    $aryReceptData                  = $this->getReceptData();

    $in_OrchestratorSub_Id          = $aryReceptData['ORCHESTRATOR_SUB_ID'];
    $in_ExeNo                       = $aryReceptData['EXE_NO'];
    $in_DataRelayStorageTrunkPathNS = $aryReceptData['DATA_RELAY_STORAGE_TRUNK'];
    $in_ExecUser                    = $aryReceptData['EXEC_USER'];
    $in_TargetValue                 = $aryReceptData['TARGET_VALUE'];
    $in_TargetValue                 = $this->ky_decrypt($in_TargetValue);
    $in_engine_virtualenv_name      = $aryReceptData['ANS_ENGINE_VIRTUALENV_NAME'];

    $aryOrchestratorList = array('LEGACY_NS'=>'legacy_ns','PIONEER_NS'=>'pioneer_ns','LEGACY_RL'=>'legacy_rl');
    $strOutFolderName    = "/out";

    $aryOcheSubDir       = explode("_",$aryOrchestratorList[$in_OrchestratorSub_Id]);

    $strPadExeNo         = sprintf("%010d",$in_ExeNo);
    $strDRSDirPerExeNoNS = "{$in_DataRelayStorageTrunkPathNS}/{$aryOcheSubDir[0]}/{$aryOcheSubDir[1]}/{$strPadExeNo}";
    $root_dir_path       = $this->getApplicationRootDirPath();
    $strOutPutDirPath    = "{$strDRSDirPerExeNoNS}{$strOutFolderName}";

    $obj = new AnsibleVault();

    // Tower経由の場合のデータリレイストレージが一致しない場合があるので、/tmpにpasswordファイル作成
    $obj->setValutPasswdFileInfo('/tmp','.vault_' . getmypid());

    // ansible-vault passwordファイル作成
    $obj->CraeteValutPasswdFile('',$PasswordFile);

    // ansible-vault 暗号化
    $EncodeValue = "";
    $ret = $obj->Vault($in_ExecUser,$PasswordFile,$in_TargetValue,$EncodeValue,'',$in_engine_virtualenv_name,$strDRSDirPerExeNoNS);
    if($ret === true) {
        $this->arySuccessInfo['status'] = "SUCCEED";
    } else {
        $this->arySuccessInfo['status'] = "FAILED";
    }
    $this->intResultStatusCode = 200;
    $this->arySuccessInfo['resultdata'] = $EncodeValue;

    unlink($PasswordFile);

    unset($obj);

    if($vg_log_level == 'DEBUG')  $this->RestAPI_log("END[".basename(__FILE__)."]");
?>
