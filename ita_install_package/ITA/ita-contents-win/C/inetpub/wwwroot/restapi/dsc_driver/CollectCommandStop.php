<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   CollectCommandStop.php
//  [概要]
//     ITA DSCコンソール >作業状況確認画面 緊急停止ボタン押下時処理
//
//  [引渡パラメータ]
//   // 以下のﾊﾟﾗﾒｰﾀを複数ノード対象の停止処理時にdevice.txtを読み込む方式に変更 Debug
//     DSC_DATA_TARGET_IP:       緊急停止処理対象ノードIP            (例:10.197.19.196)
//     DSC_DATA_TARGET_USERNAME: 緊急停止処理対象ノード接続時の認証ユーザー名(例:win2012r2-99-targethost-01\Administrator)
//     DSC_DATA_TARGET_PASSWORD: 緊急停止処理対象ノード接続時の認証パスワード(例:hogehoge)
//
//
//  [返却パラメータ]
//
//     # Description : httpレスポンス・ステータス
//         200:
//         400:Bad Request：渡されたパラメータが異なるなど、要求が正しくない場合に返却される
//         401:Unauthorized：適切な認証情報を提供せず、保護されたリソースに対しアクセスをした場合に返却される
//         404:指定されたリソースが見つからない場合に返却される
//
//         500：Internal Server Error：API 実行時に予期しないエラーが発生した場合に返却される
//
//   [出力ファイル]
//     # 強制停止ファイル（forced.txt）
//     #
//
//
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // ルートディレクトリを取得 //
    $root_dir_temp = array();
    $root_dir_temp = explode( "wwwroot", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "wwwroot";
    
    // 変数 //
    $ResultFileNameStr           = "result";     // 結果ファイル名     (getFileNameAndPath利用)
    $DeviceFileNameStr           = "device";     // デバイスファイル名
    $ForcedFileNameStr           = "forced";     // 強制停止ファイル名
    
    $ErrorLogFileNameStr         = "EmergencyError.log";  // 緊急停止処理エラーログ
    $EmergencyStopLogFileStr     = "EmergencyStop.log";  // 実行結果ログ(PowerShell標準/エラー出力) Debug
    
    $OutFolderNameStr            = 'out';        // ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ
    $InFolderNameStr             = 'in';         // ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ
    
    $DriverDirPath               = 'dsc';
    $OrchestraDirPath            = 'ns';
    
    $StopDSCconfigScriptFileStr  = 'StopDscConfigrationProcess-cim.ps1'; // DSCﾌﾟﾛｾｽ停止 スクリプト
    $GetLCMStatusScriptFileStr   = 'GetDscLocalConfigurationManager-cim.ps1'; // ﾘﾓｰﾄLCMｽﾃｰﾄ確認 スクリプト
    $CommonLibraryFileStr        = 'common_functions.php';
    
    $NodeStatusBusyStr           = "busy";                               // (Get-DscLocalConfigurationManager).LCMStateで取得するﾘﾓｰﾄﾀｰｹﾞｯﾄのｽﾃｰﾀｽ
    $NodeStatusIdleStr           = "Idle";
    $NodeStatusPendingConfigStr  = "PendingConfiguration";
    $NodeStatusPendingRebootStr  = "PendingReboot";
    
    $boolFileChkRun      = TRUE;    // DSC STOP前ファイルチェックフラグ
    $boolProcessStop     = TRUE;    // DSC STOP実行判定フラグ

    $boolExeContinue     = TRUE;    // forcedファイル作成判定
    $boolProcessRun      = TRUE;    // DSCプロセス稼働判定フラグ

    $logs = "";

    // HTTPステータス 定数定義
    define("DSC_HTTP_STS_200",200);
    define("DSC_HTTP_STS_500",500);
    define('ROOT_DIR_PATH',        $root_dir_path);
    define('LOG_DIR',              '\logs\restapilogs\dsc_driver\\');
    define('LOG_PREFIX',           basename( __FILE__, '.php' ) . '_');
    define('NEWLINE',      "\r\n");

    // 共通処理関数要求
    require_once ( ROOT_DIR_PATH . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR . "commonlibs" . DIRECTORY_SEPARATOR . "common_php_req_gate.php" );

    require ( ROOT_DIR_PATH . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR. "restapiindividuallibs" .DIRECTORY_SEPARATOR. "dsc_driver" . DIRECTORY_SEPARATOR . $CommonLibraryFileStr );

    // 処理開始ログ
    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-STD-990031'));

    ////////////////////////
    // HTTPパラメータ取得 //
    ////////////////////////
    $info = array();
    $json_string = file_get_contents('php://input');
    $info = json_decode( $json_string, true );

    // exec.logの中間ファイル $TempFilePathStrを生成
    $TempLogFileHandle = tmpfile();

    // Tempファイル オープンチェック
    if(($TempLogFileHandle) == FALSE){ // システムで管理しているテンプレートファイルがオープンできません。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990013'));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage('ITADSCH-ERR-990013'), NULL);
    }

    $TempFileObject = stream_get_meta_data($TempLogFileHandle);
    $TempFilePathStr = $TempFileObject["uri"];            // FilePath 取得

    // Tempファイル Existsチェック
    if(is_file($TempFilePathStr) === FALSE ){ // システムで管理しているテンプレートファイルが存在しません。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990014'));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage('ITADSCH-ERR-990014'), NULL);   // テンポラリファイルの確保に失敗 ファイルパスなし
    }

    ////////////////////////////
    // HTTPパラメータチェック //
    // RequestHeaderForAuthチェック
    $ResultStatusCode = 0;
    $ret = checkRequestHeaderForAuth($ReqHeaderData,$ResultStatusCode,$logs);
    if( $ret === FALSE ) { // HTTPヘッダーに必要な情報が設定されていません。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage( 'ITADSCH-ERR-990015', $logs) );
        httpRespons($ResultStatusCode,DSC_ERR_HTTP_HEDER, $objMTS->getSomeMessage( 'ITADSCH-ERR-990015', $logs),$TempFilePathStr);
    }

    // checkAuthorizationInfo
    $ret = checkAuthorizationInfo($ReqHeaderData,$ResultStatusCode,$logs);
    if( $ret === FALSE ) {  // ITA-DSC間の認証処理にエラーが発生しました。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990016' ,$logs ) );
        httpRespons($ResultStatusCode,DSC_ERR_AUTH,$objMTS->getSomeMessage('ITADSCH-ERR-990016', $logs ) ,$TempFilePathStr);
    }

    // HTTPパラメータの通知確認
    if(@strlen($info["DSC_PROCESS_ID"]) == 0) { // Collect Command HTTP contents parameter: DSC_PROCESS_ID is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990017') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990017'), $TempFilePathStr);
    }

    if(@strlen($info["DATA_RELAY_STORAGE_TRUNK"]) == 0) { // Collect Command HTTP contents parameter: DATA_RELAY_STORAGE_TRUNK is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990018') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990018'), $TempFilePathStr);
    }

    if(@strlen($info["ORCHESTRATOR_SUB_ID"]) == 0) { // Collect Command HTTP contents parameter: ORCHESTRATOR_SUB_ID is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990019') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990019'), $TempFilePathStr);
    }

    if(@strlen($info["EXE_NO"]) == 0) { // Collect Command HTTP contents parameter: EXE_NO is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990020') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990020'), $TempFilePathStr);
    }

    if(@strlen($info["DSC_DATA_RELAY_STORAGE"]) == 0) { // Collect Command HTTP contents parameter: DSC_DATA_RELAY_STORAGE is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990021') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990021'), $TempFilePathStr);
    }

    // 処理種別確認
    if ( $info["DSC_PROCESS_ID"] === 3 ){ // "緊急停止処理"
        $ProcessingTypeStr = $objMTS->getSomeMessage('ITADSCH-STD-990015');

    }else {
        $outputLogAry = array( $info["EXE_NO"], $info["DSC_PROCESS_ID"] ); // ”緊急停止処理以外の処理種別が確認されました。"
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990024', $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990024', $outputLogAry ), $TempFilePathStr);
    }

    // REST API ログ出力ディレクトリパス作成(ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ:OUT)//
    $log_dir = $info["DSC_DATA_RELAY_STORAGE"] . DIRECTORY_SEPARATOR .$DriverDirPath. DIRECTORY_SEPARATOR .$OrchestraDirPath. DIRECTORY_SEPARATOR . sprintf( "%010s", $info["EXE_NO"] ) . DIRECTORY_SEPARATOR . $OutFolderNameStr;

    // 外部出力ディレクトリの存在チェック //
    if(( is_dir($log_dir)) === FALSE ) { //
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990025", $log_dir ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990025", $log_dir ), $TempFilePathStr);
    }

    // Stop-DSCconfiguration 標準出力先ログファイル
    $EmergencyLogPathStr = $log_dir . DIRECTORY_SEPARATOR . $EmergencyStopLogFileStr;

    // 緊急停止処理ログ(error.log)出力先設定 // C:\inetpub\wwwroot\logs\restapilogs\dsc_driver\EmergencyError.log
    $EmergencyErrorLogFilePathStr = $root_dir_path .DIRECTORY_SEPARATOR. "logs" .DIRECTORY_SEPARATOR. "restapilogs" .DIRECTORY_SEPARATOR. "dsc_driver" .DIRECTORY_SEPARATOR. $ErrorLogFileNameStr;

    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$EmergencyErrorLogFilePathStr );

    if( $boolExeContinue === FALSE ) {
        // 事前チェックでエラーあり
        // 何かエラーコードを返した方が良ければ、設定すること
        httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $logs, $TempFilePathStr);
        exit();
    }

    // RESULTファイル存在チェック （緊急停止処理をする以前に実行処理が終わってしまっていないか？）
    if( $boolFileChkRun === TRUE ) {
        $aryFileName = getFileNameAndPath( $log_dir, $ResultFileNameStr, ".txt" );

        if ( 0 < count($aryFileName) ) { // result.txtファイルが既に存在します";
            $outputLogAry = array( $info["EXE_NO"], $ResultFileNameStr . ".txt" );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990039", $outputLogAry ));
            $boolFileChkRun = FALSE;
        }
    }

    // forcedファイル存在チェック ：すでに緊急停止処理を実行済みだ！
    if( $boolFileChkRun === TRUE ) {
        $aryFileName = getFileNameAndPath( $log_dir, $ForcedFileNameStr, ".txt" );

        if ( 0 < count($aryFileName) ) { // forced.txtファイルが既に存在します
            $outputLogAry = array( $info["EXE_NO"], $ForcedFileNameStr . ".txt" );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990039", $outputLogAry ));
            $boolFileChkRun = FALSE;
        }
    }

    // Configディレクトリパス作成(ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ:IN)// c:\["DSC_DATA_RELAY_STORAGE"]\dsc\ns\["EXE_NO"]\in
    $ConfigDirPath = $info["DSC_DATA_RELAY_STORAGE"] . DIRECTORY_SEPARATOR . $DriverDirPath . DIRECTORY_SEPARATOR. $OrchestraDirPath . DIRECTORY_SEPARATOR . sprintf( "%010s", $info["EXE_NO"] ) . DIRECTORY_SEPARATOR . $InFolderNameStr;

    // configディレクトリの存在チェック //
    if(( is_dir($ConfigDirPath)) === FALSE ) { // Config Directory Not Found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990028", $ConfigDirPath ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990028", $ConfigDirPath ), $TempFilePathStr);
    }

    ///////////////////////////
    //--- ノード情報取得 --> //
    ///////////////////////////
    // Device.txt (対象ﾉｰﾄﾞﾘｽﾄ)パス作成
    $DeviceFilePathStr = $ConfigDirPath . DIRECTORY_SEPARATOR . "host_vars";

    // Device.txt Exists Check getFileNameAndPath-> common_functions
    $aryFileName = getFileNameAndPath( $DeviceFilePathStr, $DeviceFileNameStr, ".txt" );
    if ( 0 > count($aryFileName) ) { // Device.txt(対象ノードリストファイル)が存在しません。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990029", $DeviceFilePathStr ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990029", $DeviceFilePathStr ), $TempFilePathStr); // 停止処理対象の情報がない、RESTAPI をエラーレスポンスで返す
    }

    $DeviceFileNamePathStr = $ConfigDirPath . DIRECTORY_SEPARATOR . "host_vars" . DIRECTORY_SEPARATOR . $DeviceFileNameStr.".txt";

    // Device.txt(対象ﾉｰﾄﾞﾘｽﾄファイル)処理
    $DeviceListStream = file_get_contents($DeviceFileNamePathStr);
    $DeviceListStream = mb_convert_encoding($DeviceListStream, 'UTF-8', 'SJIS-win');
    
    $TempfileHandle = tmpfile();

    if( $TempfileHandle === FALSE ) { // ノード情報 読み込みテンポラリファイルの作成に失敗しました。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990030", $DeviceFileNamePathStr ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990030", $DeviceFileNamePathStr ), $TempFilePathStr);
    }

    $meta = stream_get_meta_data($TempfileHandle);

    if((fwrite( $TempfileHandle, $DeviceListStream )) === FALSE) {  // ノード情報 読み込みテンポラリファイルへの書き込みに失敗しました。
        $outputLogAry = array( $info["EXE_NO"], $TempfileHandle );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990031", $outputLogAry  ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990031", $outputLogAry ), $TempFilePathStr);
    }

    rewind($TempfileHandle);

    try {
        // ターゲットノードリスト取得
        $DeviceFilePoint = new SplFileObject($meta['uri']);
        $DeviceFilePoint->setFlags(SplFileObject::READ_AHEAD | SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE );
    }
    catch(Exception $e){ // Device.txt(対象ノードリストファイル)のノード情報が取得できませんでした。
        $outputLogAry = array( $info["EXE_NO"], $e->getMessage() );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990032", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990032", $outputLogAry ), $TempFilePathStr);
    }

    $StopDeviceInfoAry = array(); // 緊急停止対象ノードリスト
    $TargetNodeIdStr ="";
    $NodeListErrorCount = 0;  // Device.txt情報読込エラーカウント変数

    foreach( $DeviceFilePoint as $NodeList ) {
        if( (preg_match( "/^[0-9]+$/", $NodeList[0])) ){
            // ノード数設定取得
            $DeviceNum = (int)$NodeList[0];
            continue;
        }
        // ノード数設定が不正
        list($TargetNodeIp, $TargetNodeName, $Username, $Password ) = $NodeList;

        // ターゲットノード バリデーション
        if ( !empty($TargetNodeIp)) {
            // IPv4 Check
            $PregResult = validateIP($TargetNodeIp);
            if( $PregResult == FALSE ) { // ノード情報IPアドレスの書式がIPv4フォーマットではありません。
                $NodeListErrorCount++;
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990033", $info["EXE_NO"] ));

            }
            
        } else { // ノード情報 にIPアドレス情報が記述されていません。
            $NodeListErrorCount++; // ノード情報IPアドレスが空
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990034", $info["EXE_NO"] ));
        }
        
        // ノード情報の各項目(ホスト名、ユーザー名、パスワード(IPアドレス以外))が実際の値を持ち,空でないことを確認
        
        if ( empty($TargetNodeName ) ) { // "作業対象ノードのホスト名が入力されていません。(作業No.{})";
                    $NodeListErrorCount++;
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990035", $info["EXE_NO"] ));
        }
        
        if ( empty($Username ) ) {
                    $NodeListErrorCount++;
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990036", $info["EXE_NO"] ));
        }
        
        if ( empty($Password ) ) {
                    $NodeListErrorCount++;
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990037", $info["EXE_NO"] ));
        }
        
        // ホスト名・ユーザーID結合 ユーザー ログオン名作成
        $TargetNodeIdStr = $TargetNodeIp .DIRECTORY_SEPARATOR. $Username;         // TargetNodeIp\Username 2018.04.27 Update  再起動時にホスト名が更新されると接続できない対処
        $StopDeviceInfoAry[] = array($TargetNodeIp, $TargetNodeIdStr, $Password);
        
    }

    unset($DeviceFilePoint);
    fclose($TempfileHandle);
    
    // Device.txtの内容が不正であり停止処理対象へのアクセスへ支障をきたすため REST Return
    if ( $NodeListErrorCount > 0 ) { // Device.txtの内容が不正
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990038", $info["EXE_NO"] ));
        httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990038", $info["EXE_NO"]), $TempFilePathStr);
    }

    //#####################################################################//
    //  DSC実行中プロセス停止処理                                          //
    //#####################################################################//

    $current_path = getcwd();
    $PSGetStateScriptPathStr = $current_path . DIRECTORY_SEPARATOR . $GetLCMStatusScriptFileStr; // GetDscLocalConfigurationManager-cim.ps1
    $PSStopDSCScriptPathStr = $current_path . DIRECTORY_SEPARATOR . $StopDSCconfigScriptFileStr; // StopDscConfigrationProcess-cim.ps1

    $outputLogAry = array( $info["EXE_NO"] );
    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990033", $outputLogAry ));

    $ProcessRunCountInt = 0; // 稼働中のLCMプロセスのカウント
    $StopDSCProcessingErrCountInt = 0; // 停止処理エラーのカウント

    // ﾉｰﾄﾞ(LCM)毎に実行中のDSC処理を検知する
    foreach($StopDeviceInfoAry as $NodeParam ){
        list( $TargetIpStr, $TargetUsernameStr, $TargetPasswordStr) = $NodeParam;
        
        // 実行コマンドライン作成
        $cmd = sprintf("powershell -File $PSGetStateScriptPathStr \"%s\" \"%s\" \"%s\" 2>&1",
                    $TargetIpStr,
                    $TargetUsernameStr,
                    $TargetPasswordStr
                       );

        $array_out = array();
        $return_var = NULL;
        $PowershellLogOutput = NULL;
        // 『検知処理実行』：停止処理前 //
        exec( $cmd, $array_out, $return_var );
        
        // $array_out(powershellの実行結果)を行毎にファイルに出力
        $PowershellLogOutput = implode ( PHP_EOL, $array_out );
        file_put_contents( $TempFilePathStr, $PowershellLogOutput . PHP_EOL, FILE_APPEND );
        
        // 検知後処理判断
        if( $return_var === -1 ){  // (LCMStatus:Busy)ターゲットノードの実行中プロセスを検知
            $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $PowershellLogOutput );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990034", $outputLogAry ));
            
            $ProcessRunCountInt++;
            
            // Stop-DSCconfiguration 実行コマンドライン作成
            $cmd = sprintf("powershell -File $PSStopDSCScriptPathStr \"%s\" \"%s\" \"%s\" 2>&1",
                        $TargetIpStr,
                        $TargetUsernameStr,
                        $TargetPasswordStr
                      );

            $array_out = array();
            $return_var = NULL;
            $PowershellLogOutput = NULL;
            
            // 停止処理実行 //
            exec( $cmd, $array_out, $return_var );
            $PowershellLogOutput = implode ( PHP_EOL, $array_out );
            file_put_contents( $TempFilePathStr, $PowershellLogOutput . PHP_EOL, FILE_APPEND );
            
            // 停止処理後判断
            if($return_var === 0){ // Stop-DSCConfiguration 処理成功
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990035", $outputLogAry ));
            
            }else{
                // Stop-DSCConfiguration 処理エラー
                
                switch($return_var)
                {
                    case 45: // Cimセッションパラメータの暗号化に失敗しました。
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990064", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 46: // Credentialオブジェクトのインスタンス生成に失敗しました。
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990065", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 47: // Cimセッション生成で例外処理が発生しました。
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990066", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 48: // 緊急停止処理でエラーが発生しました。
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990067", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;
                }
            }
            
            //###################################//
            // 停止処理後DSCステータス再検知
            //###################################//
            // LCMのステータスを再度確認し処理が停止していること(Idle)を確認する
            $cmd = sprintf("powershell -File $PSGetStateScriptPathStr \"%s\" \"%s\" \"%s\" 2>&1",
                    $TargetIpStr,
                    $TargetUsernameStr,
                    $TargetPasswordStr
                       );

            $array_out = array();
            $return_var = NULL;
            $PowershellLogOutput = NULL;
            
            exec( $cmd, $array_out, $return_var );
            $PowershellLogOutput = implode ( PHP_EOL, $array_out );
            file_put_contents( $TempFilePathStr, $PowershellLogOutput . PHP_EOL, FILE_APPEND );
            
            // (LCMStatus:Busy)  DSCプロセス停止処理後再検知 停止処理が効いていない 想定外エラーへ移行
            if( $return_var === -1 ){ // 想定外エラー：DSCプロセスの停止処理後もDSCプロセスが稼働し続けています
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990068", $outputLogAry ));
                httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990068", $outputLogAry, $TempFilePathStr));    // 想定外エラーでRESTで返す(EXIT)
                
            // (LCMStatus:idle) DSCプロセス停止後実行プロセスなし -> 停止処理成功
            }elseif($return_var === 0 ){
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990036", $outputLogAry ));
                // $boolProcessRun = FALSE;   // 実行中のプロセスなし
            }

            // 停止処理後DSCプロセス検知処理 処理エラーケースチェック "idle","busy"以外のケース PendingReboot,PendingConfiguration含む
            if( ($return_var !== -1) && ($return_var !== 0) )
            {
                switch($return_var)
                {
                    case -2: // LCMStatus:PendingReboot
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990083", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 50: // LCMStatus:PendingConfiguration
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990084", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 55:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990070", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 56:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990071", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 57:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990072", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;

                    case 58:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990073", $outputLogAry ));
                        $StopDSCProcessingErrCountInt++;
                        break;
                }
            }
        }elseif($return_var === 0 ){ // 緊急停止処理以前の検知処理で(LCMStatus:Idle)作業対象ノードで稼働中のDSCプロセス見つからず
            $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990074", $outputLogAry ));
            
            continue;    // 次のノードへ
        }
        // DSCプロセス検知処理(初回) 実行結果 処理エラー//
        if( ($return_var !== 0) && ($return_var !== -1) )
        {
            
            switch($return_var)
            {
                case -2: // DSCプロセス検知 1回目:エラー：LCMStatus:PendingReboot
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990081", $outputLogAry ));
                    $StopDSCProcessingErrCountInt++;
                    break;

                case 50: // DSCプロセス検知 1回目:エラー：LCMStatus:PendingConfiguration
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990082", $outputLogAry ));
                    $StopDSCProcessingErrCountInt++;
                    break;

                case 55: // DSCプロセス検知 1回目:エラー：Cimセッション引数の暗号化ができませんでした 正しいパスワードを指定してください
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990075", $outputLogAry ));
                    $StopDSCProcessingErrCountInt++;
                    break;

                case 56: // DSCプロセス検知 1回目:エラー：Credential オブジェクト生成に失敗しました 正しいユーザIDを指定してください
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990076", $outputLogAry ));
                    $StopDSCProcessingErrCountInt++;
                    break;

                case 57: // DSCプロセス検知 1回目:エラー：Cimセッション生成に失敗しました 適用するノードのIP・ホスト名/ユーザーID等、パラメータを確認してください。
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990077", $outputLogAry ));
                    $StopDSCProcessingErrCountInt++;
                    break;

                case 58: // DSCプロセス検知 1回目:エラー：LCM状態取得処理を実行できませんでした
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990078", $outputLogAry ));
                    $StopDSCProcessingErrCountInt++;
                    break;
                default:

            }
        }
        // 次ノードへ
        
    } //  実行中DSCプロセス検知&プロセス停止&再プロセスチェック Loop END

    if ( $ProcessRunCountInt > 0 && $StopDSCProcessingErrCountInt === 0 ) { // 1ノードでもLCMが稼働中であり処理エラーなく停止できていれば、Forcedファイルは作成する
        $boolExeContinue === TRUE;
    
    }elseif( $StopDSCProcessingErrCountInt > 0 || $ProcessRunCountInt ===0 ){ // 各ノードの停止処理中に処理エラーがある、または稼働プロセスが１つもなかった場合は想定外エラーとする。
        $boolExeContinue === FALSE;
    }
    
    //#############################//
    // --- forcedファイル作成 ---  //
    //#############################//
    if ($boolExeContinue === TRUE )
    {
        // forcedファイルパス
        $ForcedFilePathStr = $log_dir. DIRECTORY_SEPARATOR .$ForcedFileNameStr. '.txt';

        // すでにforcedファイルが存在する場合、削除する
        if(file_exists($ForcedFilePathStr) === TRUE )
        {
            unlink($ForcedFilePathStr);
            $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990037", $outputLogAry ));
           
        }

        // サイズ0のファイルを作成する
        $boolTouchForcedFile = touch($ForcedFilePathStr);

        if($boolTouchForcedFile === FALSE )
        {
            // forcedファイル作成失敗  プロセスの停止はできているので後続の処理結果返却で受付受理としてレスポンスを返すが、ITA側で想定外エラーとする
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990079", $info["EXE_NO"] ));
           
            $boolExeContinue = TRUE;
        }
        elseif($boolTouchForcedFile === TRUE )
        {
            $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $return_var, $PowershellLogOutput );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990038", $outputLogAry ));
            
            $boolExeContinue = TRUE;
        }
    }
    
    // 処理結果返却
    if( $boolExeContinue === TRUE )
    {
        // 受付受理 正常終了：緊急停止処理を正常に終了します
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990039", $info["EXE_NO"] ));
        
        $TmpLogSjisStr = "";
        $TmpLogSjisStr = file_get_contents($TempFilePathStr);
        
        if( $TmpLogSjisStr === FALSE ) // phpエラーログの確認のため故意に
        {
            $logs = "";
        }
        file_put_contents( $EmergencyLogPathStr, $TmpLogSjisStr, FILE_APPEND );
        $logs = mb_convert_encoding( $TmpLogSjisStr, "UTF-8", "auto");
        
        httpRespons( DSC_HTTP_STS_200, $return_var, $logs, $TempFilePathStr );

    }
    elseif( $boolExeContinue === FALSE )
    {
        // 想定外エラー：緊急停止ファイルの作成ができませんでした
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990080", $info["EXE_NO"] ));
        
        $TmpLogSjisStr = "";
        $TmpLogSjisStr = file_get_contents($TempFilePathStr);
        
        if( $TmpLogSjisStr === FALSE ) // phpエラーログの確認のため故意に
        {
            $logs = "";
        }
        file_put_contents( $EmergencyLogPathStr, $TmpLogSjisStr, FILE_APPEND );
        $logs = mb_convert_encoding( $TmpLogSjisStr, "UTF-8", "auto");
        
        httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $logs, $TempFilePathStr);

    }

    ////////////////////////////////////////////////////////////////////////////////
    //
    // 処理内容
    //   HTTPレスポンス返却
    // パラメータ
    //   $in_ResultStatusCode:   HTTPステータスコード  (DSC_HTTP_STS_500 etc)
    //   $in_status:             処理結果ステータス    (DSC_ERR_HTTP_REQ etc)
    //   $in_logs:               Collect Command の処理メッセージ出力
    //   $in_logfile:            ログファイル
    // 戻り値
    //   なし
    //
    ////////////////////////////////////////////////////////////////////////////////
    function httpRespons($in_ResultStatusCode,$in_status,$in_logs,$in_logfile)
    {
        // ログ情報を出力していたtempファイルの削除処理
        if(is_file($in_logfile) === true)
        {
            @unlink($in_logfile);
        }
    
        $aryRespons = array('ResultCode'=>$in_status,
                            'Logs'=>$in_logs);
                            
        $objJSONOfResultData = json_encode($aryRespons);
        
        if( $objJSONOfResultData === false)
        {
            echo "json_encode error";
        }

        header('Content-Type: application/json; charset=utf-8', true, $in_ResultStatusCode);
        exit($objJSONOfResultData);
    }

?>
