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

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    // playbook名はパラメータで渡される
    $strResultFileName   = 'result';
    $strForcedFileName   = 'forced';
    $strOutFolderName    = '/out';
    $strInFolderName     = '/in';
    
    $aryOrchestratorList = array('LEGACY_NS'=>'legacy_ns','PIONEER_NS'=>'pioneer_ns','LEGACY_RL'=>'legacy_rl');

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

    $strPadExeNo         = sprintf("%010d",$strExeNo);
    $strDRSDirPerExeNoNS = "{$strDataRelayStorageTrunkPathNS}/{$aryOcheSubDir[0]}/{$aryOcheSubDir[1]}/{$strPadExeNo}";
    $strPlaybookPath     = $strDRSDirPerExeNoNS.$strInFolderName.'/'.$strPlayBookFileName;    // プロセス実行確認用 playbookフルパス

    $boolRunning = true;    // 通過チェックフラグ

    $aryReceptData          = $this->getReceptData();

    if( $boolExeContinue === false ){
        // 事前チェックでエラーあり
        // 何かエラーコードを返した方が良ければ、設定すること
        $this->RestAPI_log("END[".basename(__FILE__)."]");
        exit();
    }

    // ファイルによるplaybook実行中の排他ロック
    // ansible実行中(ansible_playbookコマンド～result.txt作成まで)のロックファイル
    $strExecProcLockFilePath = $strDRSDirPerExeNoNS.$strOutFolderName.'/.ans_exec_proc_lock';
    // ansible_playbookコマンド実行中のロックファイル
    $strRunLockFilePath = $strDRSDirPerExeNoNS.$strOutFolderName.'/.ans_run_lock'; 
    // 緊急停止処理中のロックファイル
    $strForcedLockFilePath = $strDRSDirPerExeNoNS.$strOutFolderName.'/.ans_forced_proc_lock';


    $ExecProcLockfp = fopen($strExecProcLockFilePath,"a");
    if($ExecProcLockfp === false){
        // ansible実行中(ansible_playbookコマンド～result.txt作成まで)のロックファイル作成失敗
        $this->intResultStatusCode = 200;
        $this->arySuccessInfo['status'] = "FAILED";
        // 実行開始処理で異常あり。
        $this->arySuccessInfo['resultdata'] = array("ANSIBLE_WF_RESULT"=>'Exclusive access control abnormality (1).'); 
        // 以降の処理は実施しない
        $boolRunning = false;
    }
    else{
        $ret = flock($ExecProcLockfp ,LOCK_EX|LOCK_NB ,$wouldblock);
        fclose($ExecProcLockfp);
        // 排他ロック中は実行中と判定
        if($wouldblock == 1){
            // プロセスあり
            // 実行中
            $this->intResultStatusCode = 200;
            $this->arySuccessInfo['status'] = "RUNNING";
            // 以降の処理は実施しない
            $boolRunning = false;
        }
        else{
            $ForcedLockfp = fopen($strForcedLockFilePath,"a");
            if($ForcedLockfp === false){
                // 緊急停止処理中のロックファイル作成失敗
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "FAILED";
                // 実行開始処理で異常あり。
                $this->arySuccessInfo['resultdata'] = array("ANSIBLE_WF_RESULT"=>'Exclusive access control abnormality (2).'); 
                // 以降の処理は実施しない
                $boolRunning = false;
            }
            else{
                $ret = flock($ForcedLockfp ,LOCK_EX|LOCK_NB ,$wouldblock);
                fclose($ForcedLockfp);
                // 排他ロック中は実行中と判定
                if($wouldblock == 1){
                    // プロセスあり
                    // 実行中
                    $this->intResultStatusCode = 200;
                    $this->arySuccessInfo['status'] = "RUNNING";
                    // 以降の処理は実施しない
                    $boolRunning = false;
                }
                else{
                    // 排他ファイル削除
                    @unlink($strExecProcLockFilePath);
                    @unlink($strRunLockFilePath);
                    @unlink($strForcedLockFilePath);
                }
            }
        }
    }


    // 実行中を確認後にユーザログ編集

    // pioneerの場合にユーザログ("xxx", )を改行し不要な改行文字を改行コードに置きなえる
    if($strOrchestratorSub_Id == "PIONEER_NS"){
        $in_exec_log   = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.org";
        $out_exec_tmp1 = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.tmp1";
        $out_exec_tmp2 = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.tmp2";
        $out_exec_log  = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log";

        // ユーザログ("xxx", )を改行する
        $cmd = "sed -e 's/\", \"/\",\\n\"/g' " . $in_exec_log  .  " > " . $out_exec_tmp1;
        exec($cmd);
        
        // 改行文字を改行コードに置換える
        $cmd = "sed -e 's/\\\\r\\\\n/\\n/g' "  . $out_exec_tmp1 . " > " . $out_exec_log;
        exec($cmd);

        exec("/bin/rm -f " . $out_exec_tmp1 );
    }
    else{
//       特定のキーワードで改行しlegacyのログを見やすくする

        $in_exec_log   = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.org";
        $out_exec_tmp1 = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.tmp1";
        $out_exec_tmp2 = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.tmp2";
        $out_exec_tmp3 = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log.tmp3";
        $out_exec_log  = $strDRSDirPerExeNoNS . $strOutFolderName . "/exec.log";

        // ログ(", ")  =>  (",\n")を改行する
        $cmd = "sed -e 's/\", \"/\",\\n\"/g' " . $in_exec_log  .  " > " . $out_exec_tmp1;
        exec($cmd);

        // ログ(=> {)  =>  (=> {\n)を改行する
        $cmd = "sed -e 's/=> {/=> {\\n/g' " . $out_exec_tmp1.  " > " . $out_exec_tmp2;
        exec($cmd);

        // ログ(, ")  =>  (,\n")を改行する
        $cmd = "sed -e 's/, \"/,\\n\"/g' " . $out_exec_tmp2 .  " > " . $out_exec_tmp3;
        exec($cmd);
        
        // 改行文字を改行コードに置換える
        $cmd = "sed -e 's/\\\\r\\\\n/\\n/g' "  . $out_exec_tmp3 . " > " . $out_exec_log;
        exec($cmd);

        exec("/bin/rm -f " . $out_exec_tmp1 . " " . $out_exec_tmp2 . " " . $out_exec_tmp3);

    }

    // PIDファイル存在チェック
    if( $boolRunning === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, "", ".pid");

        if ( 1 > count($aryFileName) ){
            // PIDファイルが存在しない
            // ansible-playbookコマンドのプロセス存在チェック
            if (chkAnsibleRunning($strPlaybookPath) === true){
                // 想定外エラー
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "FAILED";
                // 実行開始処理で異常あり。
                $this->arySuccessInfo['resultdata'] = array("ANSIBLE_WF_RESULT"=>'By practice start processing, there was an abnormality (1).');
                // 以降の処理は実施しない
                $boolRunning = false;
            }
            else{
                // 未実行
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "NOT RUNNING";
                // 以降の処理は実施しない
                $boolRunning = false;
            }
        }
    }

    // Resultファイル存在チェック
    if( $boolRunning === true ){
        $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, $strResultFileName, ".txt");
        
        if ( 1 > count($aryFileName) ){
            //Resultファイルが存在しない
            // ansible-playbookコマンドのプロセス存在チェック

            if (chkAnsibleRunning($strPlaybookPath) === true )
            {
                // プロセスあり
                // 実行中
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "RUNNING";
                // 以降の処理は実施しない
                $boolRunning = false;
            }
            else{
                // 想定外エラー
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "FAILED";
                // 実行開始処理で異常あり。
                $this->arySuccessInfo['resultdata'] = array("ANSIBLE_WF_RESULT"=>'By practice start processing, there was an abnormality (2).');
                // 以降の処理は実施しない
                $boolRunning = false;
            }
        }
    }

    // Resultファイル内容チェック
    if( $boolRunning === true ){
        // Resultファイルの中身の想定
        // 異常終了時⇒'PREVENTED'
        // 正常終了時⇒'COMPLETED;codeNum'

        // 読み取り専用で開く
        $fp = fopen($strDRSDirPerExeNoNS.$strOutFolderName.'/'.$strResultFileName.'.txt', 'r');
        // 1行読み込む
        $reLine = fgets($fp);
        // 読み込んだ文字列を','で区切る
        $reLineArray = explode(";", $reLine);
        // ファイルを閉じる
        fclose($fp);

        if ($reLineArray[0] === 'COMPLETED'){
            if ($reLineArray[1] === '0'){
                // 完了（正常）
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "SUCCEED";
            }
            else{
                // pioneerの緊急停止ではplaybookはkillしないで
                // 清家モジュールをkillするので異常終了のステータス
                // が設定されるので、正常終了以外の場合は緊急停止
                // ファイル存在チェック
                $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, $strForcedFileName, ".txt");
                if ( 1 <= count($aryFileName) ){
                    // 緊急停止
                    $this->intResultStatusCode = 200;
                    $this->arySuccessInfo['status'] = "FORCED";
                }
                else{
                    // 完了（異常）
                    $this->intResultStatusCode = 200;
                    $this->arySuccessInfo['status'] = "SUCCEED";
                    $this->arySuccessInfo['resultdata'] = array("ANSIBLE_WF_RESULT"=>'ERROR');
                }
            }
        }
        else{
            // 緊急停止ファイル存在チェック
            $aryFileName = getFileNameAndPath($strDRSDirPerExeNoNS.$strOutFolderName, $strForcedFileName, ".txt");
            
            if ( 1 > count($aryFileName) ){
                // 緊急停止ファイルが存在しない
                // 想定外エラー
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "FAILED";
                // 緊急停止処理で異常あり。
                $this->arySuccessInfo['resultdata'] = array("ANSIBLE_WF_RESULT"=>'By urgent  stop processing, there was an abnormality.');
            }
            else{
                // 緊急停止
                $this->intResultStatusCode = 200;
                $this->arySuccessInfo['status'] = "FORCED";
            
            }
        }
    }

    // 処理結果返却
    if( $boolExeContinue === true ){
        // 受付受理
        $this->intResultStatusCode = 200;
    }

    if($vg_log_level == 'DEBUG')  $this->RestAPI_log("END[".basename(__FILE__)."]");
?>
