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

    if($vg_log_level == 'DEBUG')   $this->RestAPI_log("START[".basename(__FILE__)."]");

    // 外部変数取り見込
    global $root_dir_path;

    $strResultFileName   = 'result';
    $strForcedFileName   = 'forced';
    $strOutFolderName    = '/out';
    $strInFolderName     = '/in';
    //pioneerのオーケストレータ種別はなくなる
    $aryOrchestratorList = array('LEGACY_NS'=>'legacy_ns','PIONEER_NS'=>'pioneer_ns','LEGACY_RL'=>'legacy_rl');

    $boolFileChkRun      = true;    // pkill前ファイルチェックフラグ
    $boolPkillRun        = true;    // pkill実行判定フラグ

    // PIDファイルパス取得
    $aryReceptData                  = $this->getReceptData();

    $strOrchestratorSub_Id          = $aryReceptData['ORCHESTRATOR_SUB_ID'];
    $strExeNo                       = $aryReceptData['EXE_NO'];
    $strDataRelayStorageTrunkPathNS = $aryReceptData['DATA_RELAY_STORAGE_TRUNK'];

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

    $strPadExeNo            = sprintf("%010d",$strExeNo);
    $strDRSDirPerExeNoNS    = "{$strDataRelayStorageTrunkPathNS}/{$aryOcheSubDir[0]}/{$aryOcheSubDir[1]}/{$strPadExeNo}";

    $strPlaybookPath = $strDRSDirPerExeNoNS.$strInFolderName.'/'.$strPlayBookFileName;    // プロセス実行確認用 playbookフルパス

    if( $boolExeContinue === false ){
        // 事前チェックでエラーあり
        // 何かエラーコードを返した方が良ければ、設定すること
        $this->RestAPI_log("END[".basename(__FILE__)."]");
        exit();
    }

    // 緊急停止処理中のロックファイル
    $strForcedLockFilePath = $strDRSDirPerExeNoNS.$strOutFolderName.'/.ans_forced_proc_lock';
    if($boolFileChkRun === true ){
        $ForcedLockfp = fopen($strForcedLockFilePath,"a");
        if($ForcedLockfp === false){
            // 緊急停止処理中のロックファイル作成失敗
            $this->RestAPI_log('Exclusive access control abnormality (1).');
            // Pkill実行、forcedファイル作成をおこなわない
            $boolFileChkRun  = false;
        }
        else{
            // 緊急停止処理中のロック
            $ret = flock($ForcedLockfp ,LOCK_EX ,$wouldblock);
        }
    }

    $exec_pid = -1;
    // PIDファイル存在チェック
    if( $boolFileChkRun === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, "", ".pid");


        if ( 1 > count($aryFileName) ){
            // PIDファイルが見つかりません
            $this->RestAPI_log("The file was not found:<kind of the file>pid file");
            $boolFileChkRun = false;
        }
        $exec_pid = basename( $aryFileName[0] , '.pid' );
    }

    // RESULTファイル存在チェック
    if( $boolFileChkRun === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, $strResultFileName, ".txt");
        if ( 0 < count($aryFileName) ){
            // RESULTファイルが既に存在します
            $this->RestAPI_log("The file already exists:<file name>".$strResultFileName.".txt");
            $boolFileChkRun = false;
        }
    }

    // forcedファイル存在チェック
    if( $boolFileChkRun === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, $strForcedFileName, ".txt");
        if ( 0 < count($aryFileName) ){
            // forcedファイルが既に存在します
            $this->RestAPI_log("The file already exists:<file name>".$strForcedFileName.".txt");
            $boolFileChkRun = false; // #1244 2017/08/21 Append start
        }
    }

    if($boolFileChkRun === false){
        $boolExeContinue = false;
        $boolPkillRun = false;
    }

    if($boolFileChkRun === true){
        // ファイルによるplaybook実行中の排他ロック
        // ansible実行中(ansible_playbookコマンド～result.txt作成まで)のロックファイル
        $strExecProcLockFilePath = $strDRSDirPerExeNoNS.$strOutFolderName.'/.ans_exec_proc_lock';
        // ansible_playbookコマンド実行中のロックファイル
        $strRunLockFilePath = $strDRSDirPerExeNoNS.$strOutFolderName.'/.ans_run_lock';
    
        $RunLockfp = fopen($strRunLockFilePath,"a");
        if($RunLockfp === false){
            // ansible_playbookコマンド実行中のロックファイルの生成失敗
            $this->RestAPI_log('Exclusive access control abnormality (2).');
            // Pkill実行、forcedファイル作成をおこなわない
            $boolExeContinue = false;
            $boolFileChkRun  = false;
            $boolPkillRun    = false;
        }
        else{
            $ret = flock($RunLockfp ,LOCK_EX|LOCK_NB ,$wouldblock);
            fclose($RunLockfp);
            // 排他ロック中は実行中と判定
            if($wouldblock == 0){
                // Ansible-playbookコマンドが実行されていない
                $this->RestAPI_log("The ansible process is not running.");
                // Pkill実行、forcedファイル作成をおこなわない
                $boolExeContinue = false;
                $boolFileChkRun  = false;
                $boolPkillRun    = false;
            }
        }
    }

    // プロセス強制終了
    if ($boolPkillRun === true){
        $this->RestAPI_log("ansible-playbook command kill");

        if($strOrchestratorSub_Id == "PIONEER_NS"){
            $cmd = "sudo " . $root_dir_path . "/backyards/ansible_driver/ky_pionner_kill_side_Ansible.sh $exec_pid $strDRSDirPerExeNoNS$strOutFolderName";
            exec($cmd ,$output, $rtnKill);
        }
        else{
            exec("sudo pkill -KILL {$strPlaybookPath} -f", $output, $rtnKill);
    
            // プロセス強制終了実行結果チェック
            if ($rtnKill === false){
                // pkillコマンド実行失敗
                $this->RestAPI_log("Failed in the forced end of the process. error code:".$rtnKill);
                $boolFileChkRun  = false;
                $boolPkillRun    = false;
            }
        }
    }

    // ---- forcedファイル作成
    if ($boolPkillRun === true){
        // forcedファイル作成
        $strNewFileTempName = $strDRSDirPerExeNoNS.$strOutFolderName."/".$strForcedFileName.".txt";

        // すでにforcedファイルが存在する場合、削除する
        if(file_exists($strNewFileTempName) === true){
            unlink($strNewFileTempName);
        }

        // サイズ0のファイルを作成する
        $boolTouchResult = touch($strNewFileTempName);
        if($boolTouchResult === false){
            // forcedファイル作成失敗
            $this->RestAPI_log("File making was failed in:<file name>".$strForcedFileName.".txt");
        }
    }

    // 緊急停止処理中のロック解除
    if($ForcedLockfp !== false){
        fclose($ForcedLockfp);
    }

    // 処理結果返却
    if( $boolExeContinue === true ){
        // 受付受理
        $this->intResultStatusCode = 200;
    }
    else{
        // 想定外エラー
        $this->intResultStatusCode = 500;
    }

    if($vg_log_level == 'DEBUG')   $this->RestAPI_log("END[".basename(__FILE__)."]");
?>
