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
    //  【パラメータ】
    //      IN  : 有り
    //            (1) （オーケストレータx作業実行IDごとに存在する）起動するプレイブックのあるディレクトリ[データリレーストレージ]
    //            (2) （オーケストレータx作業実行IDごとに存在する）PIDを保存するディレクトリ[アンシブル側ストレージ]
    //
    //      OUT : 無し
    //
    //////////////////////////////////////////////////////////////////////
    
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ){
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }
    
    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // PHP エラー時のログ出力先   //
    ////////////////////////////////
    $log_output_dir  = $root_dir_path . '/logs/restapilogs/ansible_driver';
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";
    // ログ出力フラグ /etc/sysconfig/httpdより取得
    $log_level = @getenv('ANSIBLE_RESTAPI_LOG_LEVEL');


    ////////////////////////////////
    // PHP エラー時のログ出力を設定
    ////////////////////////////////
    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $ansible_common_php  = '/libs/restapiindividuallibs/ansible_driver/common_functions.php';


    $strPlayBookFileName = '/playbook.yml';
    $strInFolderName     = '/in';
    $strOutFolderName    = '/out';
    $strTempFolderName   = '/.tmp';
    $strExecshellName    = $strTempFolderName .'/.playbook_execute_shell.sh';

    // 実行shellのテンプレート
    $strExecshellTemplateName = $root_dir_path . '/backyards/ansible_driver/ky_ansible_playbook_command_shell_template.sh';
    $strSSHAddShellName       = $root_dir_path . '/backyards/ansible_driver/ky_ansible_ssh_add.exp';
    $strDecodeSSHAgentconfigFileName    = $strTempFolderName . '/.sshAgentConfig.txt';
    $strEncodeSSHAgentconfigFileName    = $strTempFolderName . '/.sshAgentConfig.enc';
    $strLogFileName                     = $strTempFolderName . '/playbook_execute_shell.log';

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    $aryFileName                = array();

    $strBuildCommand            = '';
    $objDescriptorspec          = null;
    $aryPipe                    = array();

    $resProcess                 = null;

    $aryStatusOnBegin           = array();
    $intFollowTargetPid         = null;

    $boolTouchResult            = null;

    $strStatusForFile           = '';

    $boolCloseProcess           = null;
    $strResultFilePath          = '';
    $boolFilePut                = null;

    $strWakedScriptFileName     = $argv[0];  // 起動されたPHPスクリプトのファイル名
    $strDRSRootPlayBookDirPath  = $argv[1];  // データリレーストレージにある、起動するプレイブックのあるディレクトリ
    $strDataFollowDirPath       = $argv[2];  // 作業実行IDごとにPIDを保存するディレクトリ

    // Legacy-Role対応
    $strOrchestratorSub_Id      = $argv[3];  // オーケストレータID　'LEGACY_NS'/'PIONEER_NS'/'LEGACY_RL'
    $strRunMode                 = $argv[4];  // ドライランモード 1:通常 2:ドライラン

    // 並列実行数対応
    $strExecCount               = $argv[5];  // 並列実行数 0:デフォルト !0:並列実行数

    // ansible-playbook実行ユーザー
    $strExecUser                = $argv[6];

    // Virtualenv name 
    $strEngineVirtualenvName  = $argv[7];

    // Legacy-Roleの場合はplaybookをsite.ymlにする。
    if($strOrchestratorSub_Id == "LEGACY_RL"){
        $strPlayBookFileName = '/site.yml';
    }

    // ドライランモードの場合のansible-playbookのパラメータを設定する。
    $stransibleplaybook_options = '';
    if($strRunMode == '2'){
        $stransibleplaybook_options = '--check';
    }

    // 並列実行数対応
    if($strExecCount != '0'){
        $stransibleplaybook_options = $stransibleplaybook_options . ' ';
        $stransibleplaybook_options = $stransibleplaybook_options . '--forks ' . $strExecCount;
    }

    $strExecLog          = 'exec.log.org';
    $strErrorLog         = 'error.log';


    require_once ($root_dir_path . '/libs/commonlibs/common_ansible_vault.php');
    $vaultobj = new AnsibleVault();
    list($ret,$dir,$file,$password) = $vaultobj->getValutPasswdFileInfo();

    // ansible-vault パスワードファイル生成
    $ret = $vaultobj->CraeteValutPasswdFile($strDRSRootPlayBookDirPath,
                                            $vault_password_file);
    if($ret === false) {
        throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00050001");
    }
    unset($vaultobj);

    try{
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        require_once ($root_dir_path . $ansible_common_php );

        // トレースメッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = "MAIN PROCCESS IS STARTED.";
            require ($root_dir_path . $log_output_php );
        }

        // ファイルによるplaybook実行中の排他ロック
        // エラー時のロック解除はしない。プロセス終了でロック解除
        // ansible実行中(ansible_playbookコマンド～result.txt作成まで)のロックファイル
        $strExecProcLockFilePath = "{$strDataFollowDirPath}/.ans_exec_proc_lock";
        $ExecProcLockfp = fopen($strExecProcLockFilePath,"w");
        if($ExecProcLockfp === false){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000051");
        }
        flock($ExecProcLockfp ,LOCK_EX ,$wouldblock);

        // ansible_playbookコマンド実行中のロックファイル
        $strRunLockFilePath = "{$strDataFollowDirPath}/.ans_run_lock";

        $RunLockfp = fopen($strRunLockFilePath,"w");
        if($RunLockfp === false){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000050");
        }
        flock($RunLockfp,LOCK_EX ,$wouldblock);
        
        // outフォルダフルパス
        $strOutDirPath = $strDRSRootPlayBookDirPath.$strOutFolderName;
        // 0は使用しない(標準入力はない)が、コマンドの出力先は添え字1に、エラーは添え字2に固定
        // 双方向ではないものの proc_open を使うのは proc_get_statusなど後続の関数を利用するため
        $objDescriptorspec = [
                                0 => ["pipe", "r"],
                                1 => ["file", "{$strOutDirPath}/{$strExecLog}",  "w"],
                                2 => ["file", "{$strOutDirPath}/{$strErrorLog}", "a"],
                            ];

        // 実行shellのパス
        $strExecshellName =  $strDRSRootPlayBookDirPath.$strExecshellName;
        // ssh-agentへの秘密鍵ファイルのパスフレーズ登録に必要な情報ファイルのパス(暗号化)
        $strDecodeSSHAgentconfigFileName = $strDRSRootPlayBookDirPath.$strDecodeSSHAgentconfigFileName;
        // ssh-agentへの秘密鍵ファイルのパスフレーズ登録に必要な情報ファイルのパス(復号化)
        $strEncodeSSHAgentconfigFileName = $strDRSRootPlayBookDirPath.$strEncodeSSHAgentconfigFileName;
        // ログファイル
        $strLogFileName              = $strDRSRootPlayBookDirPath.$strLogFileName;
        // 作業実行ベースディレクトリ
        $strCurrentPath   =  $strDRSRootPlayBookDirPath.$strInFolderName;

        // ssh-agentへの秘密鍵ファイルのパスフレーズ登録が必要か判定
        $sshAgentExec = "NONE";
        if(file_exists($strDecodeSSHAgentconfigFileName)) {
            if(filesize($strDecodeSSHAgentconfigFileName) != 0) {
                $sshAgentExec = "RUN";
                // ssh-agentへの秘密鍵ファイルのパスフレーズ登録に必要な情報ファイルの復号化
                $ret = ky_file_decrypt($strDecodeSSHAgentconfigFileName,$strEncodeSSHAgentconfigFileName);
                if($ret === false) {
                    // 異常フラグON
                    $error_flag = 1;

                    // 例外処理へ
                    throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000020");
                }
            }
        }
        // hostsフルパス
        $strhosts = $strDRSRootPlayBookDirPath.$strInFolderName.'/hosts';
        // playbookフルパス
        $strPlaybookPath = $strDRSRootPlayBookDirPath.$strInFolderName.$strPlayBookFileName;

        // ansible-playbookコマンド実行時のオプションパラメータファイルパス
        $stroptionfile = $strDRSRootPlayBookDirPath.$strInFolderName.'/AnsibleExecOption.txt';
        // ansible-playbookコマンド実行時のオプション取得
        $stroptions = file_get_contents($stroptionfile);

        $path = sprintf('%s/confs/commonconfs/path_ANSIBLE_MODULE.txt',$root_dir_path);
        // 改行コードが付いている場合に取り除く
        $ansible_path = file_get_contents($path);
        $ansible_path = str_replace("\n","",$ansible_path);

        // virtualenv が設定されている場合、ansible_vaultのパスを空白にする。
        if($strEngineVirtualenvName != "__undefine__") {
            $virtualenv_flg = "__define__";
            $strEngineVirtualenvName .= "/bin/activate";
            $ansible_path = "";
        } else {
            $virtualenv_flg = "__undefine__";
            $strEngineVirtualenvName = "__undefine__";
            $ansible_path .= "/";
        }

        // Ansible実行するshellを作成
        $strBuildCommand     .= "{$ansible_path}ansible-playbook {$stroptions} -i {$strhosts} {$stransibleplaybook_options} --vault-password-file {$vault_password_file} {$strPlaybookPath}";

        // sshAgentの設定とPlaybookを実行するshellのテンプレートを読み込み
        $strShell = file_get_contents($strExecshellTemplateName);
        if($strShell === false) {
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000020");
        }
        // テンプレート内の変数を実値に置き換え
        $strShell = str_replace('<<sshAgentConfigFile>>'      ,$strEncodeSSHAgentconfigFileName,$strShell);
        $strShell = str_replace('<<logFile>>'                 ,$strLogFileName                 ,$strShell);
        $strShell = str_replace('<<ssh_add_script_path>>'     ,$strSSHAddShellName             ,$strShell);
        $strShell = str_replace('<<in_directory_path>>'       ,$strCurrentPath                 ,$strShell);
        $strShell = str_replace('<<ansible_playbook_command>>',$strBuildCommand                ,$strShell);
        $strShell = str_replace('<<sshAgentExec>>'            ,$sshAgentExec                   ,$strShell);
        $strShell = str_replace('<<virtualenv_path>>'         ,escapeshellarg($strEngineVirtualenvName) ,$strShell);

        // Ansible実行shell作成
        $boolFilePut = file_put_contents($strExecshellName, $strShell);
        if( $boolFilePut===false ){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000010");
        }
        if( !chmod( $strExecshellName, 0777 ) ){
            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000020");
        }

        // Ansible実行Commnad発行
        $strBuildCommand     = "sudo -u {$strExecUser} -i {$strExecshellName} {$virtualenv_flg}";

        $resProcess = proc_open($strBuildCommand, $objDescriptorspec, $aryPipe);

        // 起動できたかを確認する
        if (is_resource($resProcess)===false ){

            // vault パスワードファイル削除
            @unlink($vault_password_file);
            @unlink($strEncodeSSHAgentconfigFileName);

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000100");
        }

        // ansible-playbookコマンドの実行ステータスを取得
        $aryStatusOnBegin = proc_get_status($resProcess);

        // ansible-playbookステータスからプロセスIDを取得
        $intFollowTargetPid    = $aryStatusOnBegin['pid'];

        // PIDファイルを作成する
        $boolTouchResult = touch("{$strDataFollowDirPath}/{$intFollowTargetPid}.pid");
        if ( $boolTouchResult===false ){

            // vault パスワードファイル削除
            @unlink($vault_password_file);
            @unlink($strEncodeSSHAgentconfigFileName);

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000200");
        }

        // アンシブルが終了するまで待つ
        // ansible-playbookの終了ステータス = $ansible_return
        pcntl_waitpid($intFollowTargetPid, $refIntReturnStatus);

        // vault パスワードファイル削除
        @unlink($vault_password_file);
        @unlink($strEncodeSSHAgentconfigFileName);

        // プロセスが終了した以降の処理
        if ( pcntl_wifexited($refIntReturnStatus)===true ){
            // 正常終了した場合

            // リターンコードを取得する
            $intExitCodeOnCmd = pcntl_wexitstatus($refIntReturnStatus);

            $strStatusForFile = 'COMPLETED;';

            // COMPLETEの処理
            $strFileBody = "{$strStatusForFile}{$intExitCodeOnCmd}";

        }
        else{
            // 正常終了していない(exitステータスまで取得できなかった)場合

            $strStatusForFile = 'PREVENTED';

            // PREVENTの場合の処理
            $strFileBody = $strStatusForFile;

        }

        // ファイルによるplaybook実行中の排他解除
        fclose($RunLockfp);

        $strResultFilePath = "{$strDataFollowDirPath}/result.txt";
        if( is_file($strResultFilePath)===true ){
            // すでにファイルが存在していた

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000300");
        }


        $boolFilePut = file_put_contents($strResultFilePath, $strFileBody, LOCK_EX);
        if( $boolFilePut===false ){
            // 結果ファイルの作成に失敗した

            // 異常フラグON
            $error_flag = 1;

            // 例外処理へ
            throw new Exception('[FILE]'.__FILE__.',[LINE]'.__LINE__.',[PLACE]'."00000400");
        }

        // ansible実行中(ansible_playbookコマンド～result.txt作成まで)の排他解除
        fclose($ExecProcLockfp);

        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = "MAIN PROCCESS IS COMPLETED.";
            require ($root_dir_path . $log_output_php );
        }
    }
    catch (Exception $e){
        if ( $error_flag   != 0        ||
            $warning_flag != 0        ){
            // メッセージ出力
            $FREE_LOG = $e->getMessage();
            require ($root_dir_path . $log_output_php );
        }
    }

    // 結果出力
    // 処理結果コードを判定してアクセスログを出し分ける
    if ( $error_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = "PROCEDURE END(ERROR)";
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(1);
    }
    elseif( $warning_flag != 0 ){
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = "PROCEDURE END(WARNING)";
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(2);
    }
    else{
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ){
            $FREE_LOG = "PROCEDURE END(COMPLETED)";
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(0);
    }

    function ky_file_decrypt($src_file,$dest_file) {
        $src_data =  file_get_contents($src_file);
        if($src_data === false) {
            return false;
        }
        $dec_data = base64_decode(str_rot13($src_data));
        $ret = file_put_contents($dest_file, $dec_data);
        if($ret === false) {
            return false;
        }
        return true;
    }

?>
