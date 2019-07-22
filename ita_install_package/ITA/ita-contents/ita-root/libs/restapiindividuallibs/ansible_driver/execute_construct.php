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

    if($vg_log_level == 'DEBUG')      $this->RestAPI_log("START[".basename(__FILE__)."]");

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    // playbook名はパラメータで渡される
    $strOutFolderName    = '/out';
    $strInFolderName     = '/in';

    // 外部変数取り見込
    global $root_dir_path;
    $php_command = @file_get_contents($root_dir_path . "/confs/backyardconfs/path_PHP_MODULE.txt");

    // 改行コードが付いている場合に取り除く
    $php_command = str_replace("\n","",$php_command);

    //CM $aryOrchestratorList = array('LEGACY_NS'=>'legacy_ns','PIONEER_NS'=>'pioneer_ns');
    $aryOrchestratorList = array('LEGACY_NS'=>'legacy_ns','PIONEER_NS'=>'pioneer_ns','LEGACY_RL'=>'legacy_rl');

    $aryReceptData                  = $this->getReceptData();   // 設定ファイル情報読み込み

    $strOrchestratorSub_Id          = $aryReceptData['ORCHESTRATOR_SUB_ID'];
    $strExeNo                       = $aryReceptData['EXE_NO'];
    $strDataRelayStorageTrunkPathNS = $aryReceptData['DATA_RELAY_STORAGE_TRUNK'];

    $strExecCount                   = $aryReceptData['PARALLEL_EXE'];  // 並列実行数 0:デフォルト !0:並列実行数

    $strRunMode                     = $aryReceptData['RUN_MODE'];   //ドライランモード 1:通常 2:ドライラン

    $strExecUser                    = $aryReceptData['EXEC_USER'];  //ansible-playbook実行時のユーザー名

    // オーケストレータ名で親playbook名を決める
    switch($strOrchestratorSub_Id){
    case 'LEGACY_NS':
    case 'PIONEER_NS':
        $strPlayBookFileName = 'playbook.yml';
        break;
    case 'LEGACY_RL':
        $strPlayBookFileName = 'site.yml';
        break;
    }

    $aryOcheSubDir                  = explode("_",$aryOrchestratorList[$strOrchestratorSub_Id]);

    $strPadExeNo         = sprintf("%010d",$strExeNo);
    $strDRSDirPerExeNoNS = "{$strDataRelayStorageTrunkPathNS}/{$aryOcheSubDir[0]}/{$aryOcheSubDir[1]}/{$strPadExeNo}";
    $root_dir_path       = $this->getApplicationRootDirPath();
    $strOutPutDirPath    = "{$strDRSDirPerExeNoNS}{$strOutFolderName}";
    
    // playbook ファイル存在チェック
    if( $boolExeContinue === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strInFolderName, $strPlayBookFileName , "");

        if ( 1 !== count($aryFileName) ){
            // ファイルが存在しない
            $boolExeContinue           = false;
            $this->intResultStatusCode = 500;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("Playbook.yml is not found.");
        }
    }

    // hosts ファイル存在チェック
    if( $boolExeContinue === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strInFolderName, "hosts", "");
        
        if ( 1 !== count($aryFileName) ){
            // ファイルが存在しない
            $boolExeContinue           = false;
            $this->intResultStatusCode = 500;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("hosts file is not found.");
        }
    }

    // オプションパラメータファイル存在チェック
    if( $boolExeContinue === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strInFolderName, "AnsibleExecOption.txt", "");

        if ( 1 !== count($aryFileName) ){
            // ファイルが存在しない
            $boolExeContinue           = false;
            $this->intResultStatusCode = 500;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("ansible execut option file is not found.");
        }
    }
    
    // outフォルダ内 ファイル存在チェック
    if( $boolExeContinue === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, "", "");

        if ( 0 < count($aryFileName) ){
            // フォルダにファイルが存在する
            $boolExeContinue           = false;
            $this->intResultStatusCode = 500;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("There are some files in Output folder.");
        }
    }

    // ky_build_and_follow_side_Ansible.php起動
    if( $boolExeContinue === true ){
        $strChildPHPProccessCommand = "{$php_command} {$root_dir_path}/backyards/ansible_driver/ky_build_and_follow_side_Ansible.php {$strDRSDirPerExeNoNS} {$strOutPutDirPath} {$strOrchestratorSub_Id} {$strRunMode} {$strExecCount} {$strExecUser} > /dev/null &";

        $err = exec($strChildPHPProccessCommand,$arry_out,$return_var);
        // エラー処理を入れているがバックグラウンド起動なので戻り判定は略意味なし
        // エラー時は、apatchのerror_logにメッセージが出力される
        if($return_var != 0){
            // プロセス起動エラー
            $boolExeContinue           = false;
            $this->intResultStatusCode = 500;
            $this->aryErrorInfo['StackTrace'] = '[FILE]'.__FILE__.',[LINE]'.__LINE__;
            $this->RestAPI_log("process execute has failed.");
        }
        else{
            // 受付受理
            $this->intResultStatusCode = 200;
        }
    }

    if($vg_log_level == 'DEBUG')     $this->RestAPI_log("END[".basename(__FILE__)."]"); 

?>
