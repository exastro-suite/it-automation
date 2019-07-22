<?php
//////////////////////////////////////////////////////////////////////
//  【概要】
//    ITAからRESTAPIで渡された情報でDSC Configration Commandletを実行する。
//
//  【入力パラメータ】
//    (RESTヘッダー情報)
//     DSC_PROCESS_ID            :処理種別 (1=実行,2=監視（確認）)
//     DATA_RELAY_STORAGE_TRUNK  :ITA側から見たデータストレージパス設定(例：/nec/data_relay_storage/dsc_driver)
//     EXE_NO                    :作業実行番号            (例:0000000001 10桁整数値(0PADINGあり))
//     DSC_DATA_RELAY_STORAGE:   DSC側から見たデータストレージパス設定(例：C:\share)
//     
//    (devie.txt内）
//       (1行目のみ)             :構成適用対象ノード総数
//       $TargetIpStr            :構成適用対象ノードIP            (例:10.197.19.196)
//       $TargetUsernameStr      :構成適用対象ノード接続時の認証ホスト/ユーザー名(例:win2012r2-99-targethost-01\Administrator)
//       $TargetPasswordStr      :構成適用対象ノード接続時の認証パスワード(例:hogehoge)
//       
//  【返却パラメータ】
//     httpレスポンス・ステータス
//     200:         正常
//     400:         HTTPヘッダーの異常が発生した場合に返却される
//     401:         アクセスキー認証で異常が発生した場合に返却される
//     500:         Internal Server Error：API 実行時に予期しないエラーが発生した場合に返却される
//
//     ERROR_CODE:処理結果ステータス
//     DSC_SUCCESS:        正常
//     DSC_ERR_HTTP_REQ:   HTTPパラメータ異常     
//     DSC_ERR_DIR:        DSC Configファイルディレクトリへの移動に失敗
//     DSC_ERR_HTTP_HEDER: HTTPヘッダーに必要な情報がない
//     DSC_ERR_AUTH:       
//     DSC_ERR_CONF:       DSC Configration Commandlet(構成適用)処理に失敗
//     DSC_ERR_TEST:       DSC TEST Commandlet(確認)処理に失敗
//
//     Output Logs
//     error.log  :PHPエラー時のログ出力先 ITA作業状況確認画面へ出力される
//     exec.log   :DSC実行ログ[標準出力/標準エラー出力] ITA作業状況確認画面へ出力される
//     
//     Output Files
//     response.txt  :対象ノード処理状態ファイル
//     result.txt    :実行結果ファイル
//     forced.txt    :緊急停止ファイル
//
/////////////////////////////////////////////////////////////////////////
    
    // ルートディレクトリを取得
    $root_dir_temp = array();
    $root_dir_temp = explode( "wwwroot", dirname(__FILE__) );
    $root_dir_path = $root_dir_temp[0] . "wwwroot";
    
    // 変数 //
    $ResultFileNameStr           = "result.txt";               // 作業結果ファイル
    $DeviceFileNameStr           = "device.txt";               // 構成適用対象ノードリスト
    $ConfigurationFileStr        = "Config-cim.ps1";           // 構成適用ファイル生成スクリプト
    $StartDSCconfigScriptFileStr = "Apply-cim.ps1";            // 構成適用実行スクリプト
    $GetDSCConfigScriptFileStr   = "GetDscLocalConfigurationManager-cim.ps1"; // 対象ノード状態取得スクリプト
    $TestDSCCcnfigScriptFileStr  = "check-cim.ps1";            // 構成適用確認/テストスクリプト
    $SetDSCConfigScriptFileStr   = "SetDscLocalConfigurationManager-cim.ps1"; // 対象ノードLCM設定スクリプト
    $PWPathDelteStr              = "PwDirDelete.ps1";          // PWディレクトリ削除スクリプト
    $RemoveDscConfigDocStr       = "RemoveDscConfigurationDocument-cim.ps1";    // Pemove-DscConfigurationDocumenスクリプト

    $ExecuteLogFileStr           = "exec.log";                 // 実行処理ログ （標準/エラー出力を記録し、ITA Web画面に読み込まれる）
    $ErrorLogFileNameStr         = "error.log";                // 
    $CommonLibraryFileStr        = 'common_functions.php';     // 共通関数ライブラリファイル

    $ResponseFileNameStr         = "response.txt";             // 構成適用対象ノード結果リスト
    $ResponseStatusUnexecStr     = "UNEXECUTED";               // "未実行" (対象ノードステータス)
    $ResponseStatusRunStr        = "RUNNING";                  // "処理実行中"
    $ResponseStatusFailStr       = "FAILED";                   // "処理エラー終了"
    $ResponseStatusSUCCESStr     = "SUCCEED";                  // "処理完了"
    $ResultStatusCode            = 0;                          //
    $StatusCodeInt               = 0;                          // response.txt 第3項目詳細コード
    $ret                         = TRUE;                       // HeaderCheck結果コード
    $logs = "";


    // HTTPステータス 定数定義
    define("DSC_HTTP_STS_200",200);
    define("DSC_HTTP_STS_500",500);

    define('ROOT_DIR_PATH',        $root_dir_path);
    define('LOG_DIR',              '\logs\restapilogs\dsc_driver\\');
    define('LOG_PREFIX',           basename( __FILE__, '.php' ) . '_');
    define('NEWLINE',              "\r\n");

    // 共通処理関数
    require_once ( ROOT_DIR_PATH . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR . "commonlibs" . DIRECTORY_SEPARATOR . "common_php_req_gate.php" );

    require ( ROOT_DIR_PATH . DIRECTORY_SEPARATOR . "libs" . DIRECTORY_SEPARATOR. "restapiindividuallibs" .DIRECTORY_SEPARATOR. "dsc_driver" . DIRECTORY_SEPARATOR . $CommonLibraryFileStr );

    // 処理開始ログ
    outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-STD-990014'));

    ////////////////////////////////
    // HTTPパラメータ取得         //
    ////////////////////////////////
    $info = array();
    $json_string = file_get_contents('php://input');
    $info = json_decode( $json_string, true );

    // $tmp_logfile：exec.logの中間出力ファイル
    $temp_logfileHandle = tmpfile();

    // Tempファイル オープンチェック
    if (($temp_logfileHandle) === FALSE) { // システムで管理しているテンプレートファイルがオープンできません。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990013'));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage('ITADSCH-ERR-990013'), NULL);
    }

    $tempfileObject = stream_get_meta_data($temp_logfileHandle);
    $TempFilePathStr = $tempfileObject["uri"];
    
    // Tempファイル Existsチェック 
    if (is_file($TempFilePathStr) ===FALSE ) { // システムで管理しているテンプレートファイルが存在しません。
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990014'));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage('ITADSCH-ERR-990014'), NULL);   // テンポラリファイルの確保に失敗 ファイルパスなし
    }

    ////////////////////////////
    // HTTPパラメータチェック //
    ////////////////////////////

    $ret = checkRequestHeaderForAuth($ReqHeaderData, $ResultStatusCode, $logs);
    if ( $ret === FALSE ) { // HTTPヘッダーに必要な情報が設定されていません。
        $outputLogAry = array( $info["EXE_NO"], $logs );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage( 'ITADSCH-ERR-990015', $outputLogAry ) );
        httpRespons($ResultStatusCode,DSC_ERR_HTTP_HEDER, $objMTS->getSomeMessage( 'ITADSCH-ERR-990015', $outputLogAry ),$TempFilePathStr);
    }

    $ret = checkAuthorizationInfo($ReqHeaderData, $ResultStatusCode, $logs);
    if ( $ret === FALSE ) { // ITA-DSC間の認証処理でエラーが発生しました。
        $outputLogAry = array( $info["EXE_NO"], $logs );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990016' ,$outputLogAry ) );
        httpRespons($ResultStatusCode,DSC_ERR_AUTH,$objMTS->getSomeMessage('ITADSCH-ERR-990016' , $outputLogAry ) ,$TempFilePathStr);
    }

    // HTTP 各Contents パラメータの有無確認
    if (@strlen($info["DSC_PROCESS_ID"]) == 0) { // Collect Command HTTP contents parameter: DSC_PROCESS_ID is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990017') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990017'), $TempFilePathStr);
    }

    if (@strlen($info["DATA_RELAY_STORAGE_TRUNK"]) == 0) { // Collect Command HTTP contents parameter: DATA_RELAY_STORAGE_TRUNK is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990018') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990018'), $TempFilePathStr);
    }

    if (@strlen($info["ORCHESTRATOR_SUB_ID"]) == 0) { // Collect Command HTTP contents parameter: ORCHESTRATOR_SUB_ID is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990019') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990019'), $TempFilePathStr);
    }

    if (@strlen($info["EXE_NO"]) == 0) { // Collect Command HTTP contents parameter: EXE_NO is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990020') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990020'), $TempFilePathStr);
    }

    if (@strlen($info["DSC_DATA_RELAY_STORAGE"]) == 0) { // Collect Command HTTP contents parameter: DSC_DATA_RELAY_STORAGE is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990021') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990021'), $TempFilePathStr);
    }

    if (@strlen($info["DSC_DATA_CONFIG_DIR"]) == 0) { // Collect Command HTTP contents parameter: DSC_DATA_CONFIG_DIR is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990022') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990022'), $TempFilePathStr);
    }

    if (@strlen($info["DSC_DATA_CONFIG_NAME"]) == 0) { // Collect Command HTTP contents parameter: DSC_DATA_CONFIG_NAME is not found
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990023') .NEWLINE. print_r($info,true));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990023'), $TempFilePathStr);
    }

    // 処理種別確認
    if ( $info["DSC_PROCESS_ID"] === 1 ){ // "実行処理"
        $ProcessingTypeStr = $objMTS->getSomeMessage('ITADSCH-STD-990015');
    }
    elseif ( $info["DSC_PROCESS_ID"] === 2 ) { // "確認処理"
        $ProcessingTypeStr = $objMTS->getSomeMessage('ITADSCH-STD-990016');
    }else {
        $outputLogAry = array( $info["EXE_NO"], $info["DSC_PROCESS_ID"] );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage('ITADSCH-ERR-990024', $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_HTTP_REQ, $objMTS->getSomeMessage('ITADSCH-ERR-990024', $outputLogAry ), $TempFilePathStr);
    }

    //////////////
    // Path設定 //
    //////////////
    // LOG(exec/error)出力設定
    // 外部出力ディレクトリパス作成(ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ:OUT)//
    $log_dir = $info["DSC_DATA_RELAY_STORAGE"] . DIRECTORY_SEPARATOR ."dsc". DIRECTORY_SEPARATOR. "ns" . DIRECTORY_SEPARATOR . sprintf( "%010s", $info["EXE_NO"] ) . DIRECTORY_SEPARATOR ."out";

    if (( is_dir($log_dir)) === FALSE ) { // 外部出力ディレクトリ確認できず
        $outputLogAry = array( $info["EXE_NO"], $log_dir );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990025", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990025", $outputLogAry ), $TempFilePathStr);
    }

    // PHPログ(error.log)出力先設定 //
    $ErrorLogFilePathStr = $log_dir . DIRECTORY_SEPARATOR . $ErrorLogFileNameStr;
    
    // ログ出力時刻
    $tmpTimeStamp = time();
    $logtime = date("Y/m/d H:i:s",$tmpTimeStamp);

    if ((file_put_contents( $ErrorLogFilePathStr, $objMTS->getSomeMessage("ITADSCH-STD-990018", $logtime ) . PHP_EOL )) === FALSE){
        $outputLogAry = array( $info["EXE_NO"], $ErrorLogFilePathStr );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990026", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990026", $outputLogAry ), $TempFilePathStr);
    }

    ini_set('display_errors',0);
    ini_set('log_errors',1);
    ini_set('error_log',$ErrorLogFilePathStr );

    // exec.log （実行処理ログ）出力先パス設定
    $ExecuteLogPathStr = $log_dir . DIRECTORY_SEPARATOR . $ExecuteLogFileStr;

    // response.txt (ノード処理結果) 出力先パス設定
    $ResponseFilePathStr = $log_dir . DIRECTORY_SEPARATOR .$ResponseFileNameStr;

    // Result.txt (Movement処理完了フラグ) 出力先パス設定 (Result.txt)//
    $ResultFilePathStr = $log_dir . DIRECTORY_SEPARATOR .$ResultFileNameStr;

    // Configuration Fileディレクトリパス作成(ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ:IN) 
    $DscConfigPathStr = $info["DSC_DATA_CONFIG_DIR"];   // Full Path(file)

    // Configディレクトリパス作成(ﾃﾞｰﾀﾘﾚｲｽﾄﾚｰｼﾞ:IN) //
    $ConfigDirPath = $info["DSC_DATA_RELAY_STORAGE"] . DIRECTORY_SEPARATOR ."dsc". DIRECTORY_SEPARATOR. "ns" . DIRECTORY_SEPARATOR . sprintf( "%010s", $info["EXE_NO"] ) . DIRECTORY_SEPARATOR . "in";

    // Configディレクトリの存在チェック //
    if ( ( is_dir($ConfigDirPath) ) === FALSE ) {
        $outputLogAry = array( $info["EXE_NO"], $ConfigDirPath );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990028", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990028", $outputLogAry ), $TempFilePathStr);
    }
    ///////////////////////////
    //--- ノード情報取得 --> //
    ///////////////////////////
    // Device.txt (対象ノードリストファイル)パス作成
    $DeviceFilePathStr = $ConfigDirPath . DIRECTORY_SEPARATOR . "host_vars" . DIRECTORY_SEPARATOR . $DeviceFileNameStr;

    if ( ( is_file($DeviceFilePathStr) ) === FALSE ) { // Device.txtが見つかりません
        $outputLogAry = array( $info["EXE_NO"], $DeviceFilePathStr );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990029", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_DIR, $objMTS->getSomeMessage("ITADSCH-ERR-990029", $outputLogAry ), $TempFilePathStr);
    }

    // Device.txt 読み込み処理
    $DeviceInfoStr = file_get_contents($DeviceFilePathStr);
    $DeviceInfoStr = mb_convert_encoding($DeviceInfoStr, 'UTF-8', 'SJIS-win');
    
    $tempfileHandle = tmpfile();
    
    if ($tempfileHandle === FALSE ) { // テンポラリファイルが開けません
        $outputLogAry = array( $info["EXE_NO"], $DeviceFilePathStr );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990030", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990030", $outputLogAry ), $TempFilePathStr);
    }

    $meta = stream_get_meta_data($tempfileHandle);

    if ((fwrite($tempfileHandle, $DeviceInfoStr)) === FALSE ) { // ノード情報のテンポラリファイルへの書込み失敗
        $outputLogAry = array( $info["EXE_NO"], $tempfileHandle );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990031", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990031", $outputLogAry ), $TempFilePathStr);
    }

    rewind($tempfileHandle);

    // ターゲットノードリスト取得
    try {
        $DeviceFileP = new SplFileObject($meta['uri']);
        $DeviceFileP->setFlags(SplFileObject::READ_AHEAD | SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE );

    } catch (Exception $e) { // CSVファイルストリーム取得処理 例外発生
        $outputLogAry = array( $info["EXE_NO"], $e->getMessage() );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990032", $outputLogAry ));
        httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990032", $outputLogAry ),$TempFilePathStr);
    }

    $DeviceInfoAry = array(); // DSC適用対象ノードリスト
    $TargetNodeIdStr ="";     // ログオンアカウント代入用変数
    $NodeListErrorCount = 0;  // Device.txt情報読込エラーカウント変数
    
    foreach($DeviceFileP as $DeviceRow) {
        
        // ノード情報読出 Start
        // Device.txt １行目 ノード数設定 行読出
        if( (preg_match( "/^[0-9]+$/", $DeviceRow[0])) ) {  // 1 or 0 or FALSE
            // ノード数取得
            $DeviceNum = (int)$DeviceRow[0];
            continue;
        }

        // その他行のノード情報 行読出し
        list($Ipaddress, $Hostname, $Username, $Password, $cerfile , $Thumbprint, $Retrytimeout ) = $DeviceRow;	// 2018.05.16 Update

        // IPv4 pregmatch & Exists check
        if ( !empty($Ipaddress) ) {
            $PregResult = validateIP($Ipaddress); // function validateIP IPv4 pregmatch
            if( $PregResult == FALSE ) { // IPv4 address format error
                $NodeListErrorCount++;
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990033", $info["EXE_NO"] ));
            }
            
        } else { // IPv4 address blanked or zero
            $NodeListErrorCount++;
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990034", $info["EXE_NO"] ));
        }

        // ノード情報の各項目(ホスト名、ユーザー名、パスワード(IPアドレス以外))が実際の値を持ち,空でないことを確認
        if ( empty($Hostname ) ) {
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
        
        // ユーザーログオン名作成
        $TargetNodeIdStr = $Ipaddress.DIRECTORY_SEPARATOR.$Username;               // Ipaddress\Username 再起動時にホスト名が更新されると接続できない対処
        $DeviceInfoAry[] = array($Ipaddress, $TargetNodeIdStr, $Password, $Thumbprint, $Retrytimeout);
    } // ノード情報読出 END

    fclose($tempfileHandle);
    unset($DeviceFileP);
    
    if ( $NodeListErrorCount > 0 ) { // Device.txtの内容が不正
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990038", $info["EXE_NO"] ));
        httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990038", $info["EXE_NO"]), $TempFilePathStr);
    }
    /////////////////////////////////
    //-- ノード情報取得END     <---//
    /////////////////////////////////
//<--------------------------------『実行処理（コンパイル/適用処理)』ここから---------------------------------------------------------------------
    //*****************************//
    //--->Configuration処理開始
    //*****************************//
    if ( $info["DSC_PROCESS_ID"] === 1) {
        
        $ReBootData = "";

        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"]);
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990019", $outputLogAry ));
            
        // response.txtファイルの既存検知
        if( file_exists($ResponseFilePathStr) === TRUE ) {
            $outputLogAry = array( $info["EXE_NO"], $ResponseFilePathStr);
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990039", $outputLogAry ));
            httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS,$objMTS->getSomeMessage("ITADSCH-ERR-990039", $outputLogAry), $TempFilePathStr);
        }

        $NodeResponseArray[] = array( $DeviceNum ); // 処理対象ノード数 response.txt １行目挿入
        
        // Response初期化 (IPAddress,UNEXECUTED,0)
        foreach($DeviceInfoAry as $Node) {
            
            $HostIP = $Node[0];
            $TmpTimeStamp = time();
            $NodeResponseArray[] = array( $HostIP, $ResponseStatusUnexecStr, $StatusCodeInt, $TmpTimeStamp );
        }
        
        ///////////////////////
        // response.txt 作成 //
        ///////////////////////
        try {
            $ToResponseFilePutP = new SplFileObject( $ResponseFilePathStr, 'w');
            
        }
        catch (Exception $e) {
            $outputLogAry = array( $info["EXE_NO"], $ResponseFilePathStr, $e->getMessage() );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ));
            httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ), $TempFilePathStr);
        }

        // response.txtへCSV形式でノード情報を書込
        foreach( $NodeResponseArray as $line){
            $ToResponseFilePutP->fputcsv($line, ',');
        }
        
        // 再読込
        $ResponseFileCodeChange = file_get_contents( $ResponseFilePathStr );
        
        // UTF-8->Shift-JIS
        $ResponseFileCodeChange = mb_convert_encoding( $ResponseFileCodeChange, 'SJIS-win', 'UTF-8' );
        
        unset( $ResponseFileCodeChange );
        unset ($ToResponseFilePutP);
        // response.txt 作成終了 //

        // パスワードディレクトリ作成
        $PwPathStr = $ConfigDirPath . DIRECTORY_SEPARATOR . "host_vars" . DIRECTORY_SEPARATOR . "pw";

        /////////////////////////////////////////////////////////
        // 『コンパイル処理』：Cconfigration(MOF生成)処理実行  //
        /////////////////////////////////////////////////////////
        $current_path = getcwd();
        
        // Configuration Processing Script File Path
        $PSScriptFilePathStr = $current_path . DIRECTORY_SEPARATOR . $ConfigurationFileStr; 

        $ConfigPathtmp = $info["DSC_DATA_CONFIG_DIR"];
        $pos = strrpos($ConfigPathtmp, "\\");

        $ConfigPath = substr($ConfigPathtmp, 0, $pos);
        $pos++;
        $ConfigFileName = substr($ConfigPathtmp, $pos);

        foreach( $DeviceInfoAry as $NodePara) {

            list( $TargetIpStr, $TargetUsernameStr, $TargetPasswordStr, $TargetThumbprint) = $NodePara;

            $TargetConfigFileFullPath = $ConfigPath . "\\" . $TargetIpStr . '_' . $ConfigFileName;

            if ( is_file($TargetConfigFileFullPath) === FALSE ) { // Configurationファイルexists check
                $outputLogAry = array( $info["EXE_NO"], $TargetConfigFileFullPath );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990027", $outputLogAry ));
                httpRespons(DSC_HTTP_STS_500, DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990027", $outputLogAry ), $TempFilePathStr );
            }

            $strFiledata = file_get_contents($TargetConfigFileFullPath);
            if( strpos( $strFiledata, 'RebootNodeIfNeeded = $true' ) !== false ) {
                $ReBootData = "True";
            }
            if( strpos( $strFiledata, 'RebootNodeIfNeeded = $false' ) !== false ) {
                $ReBootData = "False";
            }

            // Configuration 実行コマンドライン作成
            $cmd = sprintf("powershell -File $PSScriptFilePathStr \"%s\" \"%s\"  2>&1",
                             $TargetConfigFileFullPath,
                             $info["DSC_DATA_CONFIG_NAME"]
                          );

            $array_out = array();
            $return_var = 0;
            $PowershellLogOutputStr = NULL;
            exec( $cmd, $array_out, $return_var );

            // $array_out(powershellの実行結果)を行毎にファイルに出力
            $PowershellLogOutputStr = implode ( PHP_EOL, $array_out ); 
            file_put_contents( $TempFilePathStr, $PowershellLogOutputStr . PHP_EOL, FILE_APPEND );

            //『コンパイル処理』エラー
            if ( $return_var !== 0 ) {
                switch( $return_var ) {

                    // 各エラーパターン httpResponsでRestAPI Return
                    case 20:
                        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $return_var );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990041", $outputLogAry ));
                        httpRespons(DSC_HTTP_STS_500,DSC_ERR_CONF, $objMTS->getSomeMessage("ITADSCH-ERR-990041", $outputLogAry ),$TempFilePathStr);

                    case 21:
                        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $return_var);
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990042", $outputLogAry));
                        httpRespons(DSC_HTTP_STS_500,DSC_ERR_CONF, $objMTS->getSomeMessage("ITADSCH-ERR-990042", $outputLogAry ),$TempFilePathStr);

                    case 22:
                        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $return_var);
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990043" , $outputLogAry));
                        httpRespons(DSC_HTTP_STS_500,DSC_ERR_CONF, $objMTS->getSomeMessage("ITADSCH-ERR-990043" , $outputLogAry ),$TempFilePathStr);

                    case 23:
                        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $return_var);
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990044", $outputLogAry ));
                        httpRespons(DSC_HTTP_STS_500,DSC_ERR_CONF, $objMTS->getSomeMessage("ITADSCH-ERR-990044", $outputLogAry ),$TempFilePathStr);

                    case 24:
                        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $return_var);
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990045", $outputLogAry ));
                        httpRespons(DSC_HTTP_STS_500,DSC_ERR_CONF, $objMTS->getSomeMessage("ITADSCH-ERR-990045", $outputLogAry ),$TempFilePathStr);

                    default:
                    
                }
            }

        }
        
        // パスワードディレクトリ削除処理を追加
        if( (is_dir($PwPathStr)) === TRUE ) {
            // Configuration Processing Script File Path
            $PSScriptFilePathStr = $current_path . DIRECTORY_SEPARATOR . $PWPathDelteStr; 
            $cmd = sprintf("powershell -File $PSScriptFilePathStr \"%s\" 2>&1",
                             $PwPathStr
                          );

            $array_out = array();
            $return_var = 0;
            $PowershellLogOutputStr = NULL;
            exec( $cmd, $array_out, $return_var );
        }

    //******************************
    //Configuration処理ここまで<---
    //******************************
    $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"]);
    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990020", $outputLogAry ));
        
    //++++++++++++++++++++++++++++++
    //--->適用処理ここから
    //++++++++++++++++++++++++++++++
    // MOFファイル格納ディレクトリパス
    $MofFilePathStr = $ConfigDirPath .DIRECTORY_SEPARATOR. $info["DSC_DATA_CONFIG_NAME"];

    // Set-DscLocalConfigurationManager Loop Start --->
    //**********************************
    // 『LCM設定処理』
    //**********************************
        $current_path = getcwd();
        
        // Set LocalConfigurationManager Script File Path
        $PSScriptFilePathSetLCMStr = $current_path . DIRECTORY_SEPARATOR . $SetDSCConfigScriptFileStr; 

        foreach( $DeviceInfoAry as $NodePara) {

            list( $TargetIpStr, $TargetUsernameStr, $TargetPasswordStr, $TargetThumbprint) = $NodePara;

            if( ( strlen($TargetThumbprint) != 0 ) || ( strlen($ReBootData) != 0 ) ) {

                $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $TargetIpStr);
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990040", $outputLogAry ));

                ///////////////////////////////////////////////////////////////////////////
                // LCM設定処理実行コマンドライン作成（Set-DscLocalConfigurationManager） //
                ///////////////////////////////////////////////////////////////////////////
                $cmd = sprintf("powershell -File $PSScriptFilePathSetLCMStr %s \"%s\" \"%s\" \"%s\" \"%s\" %s 2>&1",
                                 $TargetIpStr,
                                 $TargetUsernameStr,
                                 $TargetPasswordStr,
                                 $MofFilePathStr,
                                 $TargetThumbprint,
                                 $ReBootData
                             );

                $array_out = array();        // PowerShell 標準出力
                $return_var = 0;             // PowerShell 戻値
                $PowershellLogOutputStr = NULL; // 出力先
                exec( $cmd, $array_out, $return_var );

                // $array_out(powershellの実行結果)を行毎にファイルに出力
                $PowershellLogOutputStr = implode ( PHP_EOL, $array_out );
                file_put_contents( $TempFilePathStr, $PowershellLogOutputStr . PHP_EOL, FILE_APPEND );
                
                if( $return_var !== 0 ) {
                    switch($return_var){
                    
                    // DSC  エラーパターン (Case XX :PowerShell 戻値)
                    case 85:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990085", $outputLogAry ));
                        break;

                    case 86:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990086", $outputLogAry ));
                        break;

                    case 87:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990087", $outputLogAry ));
                        break;

                    case 88:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // 作業対象ノード情報取得失敗
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990088", $outputLogAry ));
                        break;

                    case 89:
                        $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // LCM設定失敗
                        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990089", $outputLogAry ));

                    default:
                        break;

                    }

                }
                $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"], $TargetIpStr);
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990041", $outputLogAry ));
            }
        }
        // Set-DscLocalConfigurationManager Loop END <---

    $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"]);
    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990021", $outputLogAry ));

        $current_path = getcwd();
        // Set LocalConfigurationManager Script File Path
        $PSScriptFilePathStartConfigStr = $current_path . DIRECTORY_SEPARATOR . $StartDSCconfigScriptFileStr; 

        //--- 対象ノードへの構成適用 Start-DSCConfiguration Loop Start --->
        foreach( $DeviceInfoAry as $NodePara) { 

            list( $TargetIpStr, $TargetUsernameStr, $TargetPasswordStr) = $NodePara;
            
            // Start-DSCConfiguration 実行コマンドライン作成
            $cmd = sprintf("powershell -File $PSScriptFilePathStartConfigStr %s \"%s\" \"%s\" \"%s\" 2>&1",
                             $TargetIpStr,
                             $TargetUsernameStr,
                             $TargetPasswordStr,
                             $MofFilePathStr
                          );

            ///////////////////////////////////////////////////
            // 『構成適用処理』：Start-DSCConfiguration実行  //
            ///////////////////////////////////////////////////
            $array_out = array();        // PowerShell 標準出力
            $return_var = 0;             // PowerShell 戻値
            $PowershellLogOutputStr = NULL; // 出力先
            exec( $cmd, $array_out, $return_var );

            // $array_out(powershellの実行結果)を行毎にファイルに出力
            $PowershellLogOutputStr = implode ( PHP_EOL, $array_out );
            file_put_contents( $TempFilePathStr, $PowershellLogOutputStr . PHP_EOL, FILE_APPEND );
            

            //『構成適用処理』レスポンスエラー処理
            if( $return_var !== 0 ) {
                $NodeStatusStr = "FAILED";
                switch($return_var){
                
                // エラーパターン ステータスコード付与
                case 12:
                    $outputLogAry = array( $info["EXE_NO"], $MofFilePathStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990046", $outputLogAry ));
                    braek;

                case 15:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990047", $outputLogAry ));
                    braek;

                case 16:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990048", $outputLogAry ));
                    braek;

                case 17:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990049", $outputLogAry ));
                    braek;
                    
                case 18:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990050", $outputLogAry ));
                    braek;

                default:
                    braek;
                }
                $StatusCodeInt = $return_var;
            }
            // 各対象ノードのステータスを"RUNNING"=構成適用処理中へ変更
            if($return_var === 0 ){
                $NodeStatusStr = "RUNNING";
                $StatusCodeInt = -1;
            }

            $TmpTimeStamp = time();
            // response.txt用各ノード状態取得
            $NodeResponseAftArray[] = array($TargetIpStr,$NodeStatusStr,$StatusCodeInt,$TmpTimeStamp);

        } // Start-DSCConfiguration Loop END <---

        /////////////////////////////////////////////////
        // Start-DSCConfiguration実行後response.txt更新 /
        /////////////////////////////////////////////////
        // response.txt 再オープン(状態：Configuration実行後)
        $fileResponse = file_get_contents($ResponseFilePathStr);
        $fileResponse = mb_convert_encoding($fileResponse, 'UTF-8', 'SJIS-win');
        
        $Responsetemp = tmpfile();
        $meta = stream_get_meta_data($Responsetemp);
        
        fwrite($Responsetemp, $fileResponse);
        rewind($Responsetemp);
        
        try{
            $ResponseUpdatetemp = new SplFileObject($meta['uri']);
            $ResponseUpdatetemp->setFlags(SplFileObject::READ_AHEAD | SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE );
        }
        catch (Exception $e) {
            $outputLogAry = array( $info["EXE_NO"], $ResponseFilePathStr, $e->getMessage() );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ));
            httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ), $TempFilePathStr);
        }
        
        $ResponseListAry = array(); // response.txt 入力整形配列
        
        // $ResponseUpdatetempは
        foreach($ResponseUpdatetemp as $row) {
            if( (preg_match( "/^[0-9]+$/", $row[0])) ){
                // ノード数設定行（1行目）をスキップ
                continue;
            }
            else {
                // ノード数設定行 pregmatchエラー
            }
            // Response.txt中の旧ノード情報を配列に取る
            $ResponseListAry[] = $row;
        }
        
        unset($ResponseUpdatetemp);
        fclose($Responsetemp);

        // response.txtのﾉｰﾄﾞIPとDSC実行後のﾉｰﾄﾞIPのマッチングから各処理対象ノードのステータス更新を行う
        foreach($ResponseListAry as &$file_row) {         // 比較元 Response.txtノード情報
            $ResponseNodeIP = $file_row[0];
            
            foreach($NodeResponseAftArray as $List_row) { // 比較対象 Start-DSCConfig実行後ノード情報
                $AftNodeIP = $List_row[0];
                
                if ( $ResponseNodeIP === $AftNodeIP ){
                    
                    $file_row[1] = $List_row[1];
                    $file_row[2] = $List_row[2];
                    
                    break;
                }
            }
        }

        // Response.txtファイル更新
        $OutputResponseAftArray = array();               // Response.txt更新用の配列
        $OutputResponseAftArray[] = array( $DeviceNum ); // 処理ノード数を１行目配置
        
        foreach( $ResponseListAry as $line ){
            $OutputResponseAftArray[] = $line;
        }

        try{
            $ResponseUpdateP = new SplFileObject( $ResponseFilePathStr, 'w');

        }
        catch (Exception $e){
            $outputLogAry = array( $info["EXE_NO"], $ResponseFilePathStr, $e->getMessage() );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ));
            httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ),$TempFilePathStr);
        }

        foreach( $OutputResponseAftArray as $line){

                $ResponseUpdateP->fputcsv($line, ',');
        }
        
        // Charctor Encoding Chenge
        $OutputResponse = file_get_contents( $ResponseFilePathStr );
        $OutputResponse = mb_convert_encoding( $OutputResponse, 'sjis-win', 'UTF-8');
        file_put_contents( $ResponseFilePathStr, $OutputResponse );

        // ファイル閉じる
        unset ($OutputResponseAftArray);
        unset ($ResponseListAry);
        unset ($OutputResponse);
        unset ($ResponseUpdateP);

        // 実行処理終了メッセージ(RESTAPI_log)
        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"],);
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990022", $outputLogAry ));

    } // 実行処理:DSC_PROCESS_ID (1) END 
    

//<--------------------------------『実行処理』ここまで--------------------------------------------------------------------------

//<--------------------------------『確認とテスト処理』ここから--------------------------------------------------------------------------
    ///////////////////
    // 『確認処理』  //
    //////////////////
    
    if( $info["DSC_PROCESS_ID"] === 2 ){    // 確認処理 :DSC_PROCESS_ID (2) Start
        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"]);
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990023", $outputLogAry ));

        $RunningNodeCount = 0;    // reponse.txtの各ﾉｰﾄﾞｽﾃｰﾀｽで"RUNNING"のﾉｰﾄﾞｶｳﾝﾄ変数
        
        if( is_file($ResponseFilePathStr) === FALSE ) // response.txtファイルがなかった場合
        {
            $outputLogAry = array( $info["EXE_NO"], $ResponseFilePathStr );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990051", $outputLogAry ));
            httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990051", $outputLogAry ),$TempFilePathStr);
        }
        
        $current_path = getcwd();
        
        // ConfigManagerStatus.ps1 Path
        $PSScriptFilePathGetStStr = $current_path . DIRECTORY_SEPARATOR . $GetDSCConfigScriptFileStr;
        // check-cim.ps1 Path
        $PSScriptFilePathTestStr  = $current_path . DIRECTORY_SEPARATOR . $TestDSCCcnfigScriptFileStr;

        // 構成適用Start-DSC直後/初回の適用確認(Test-DSC)後の各ノード状態をResponse.txtから確認
        // 実行後 Response.txt読出と文字コード変換
        
        $TestfileResponse ="";
        
        $TestfileResponse = file_get_contents($ResponseFilePathStr);
        $TestfileResponse = mb_convert_encoding($TestfileResponse, 'UTF-8', 'sjis-win');
        
        // Response.txtの内容をtmpfileへ出力
        $temp = tmpfile();
        $meta = stream_get_meta_data($temp);
        
        fwrite($temp, $TestfileResponse);
        rewind($temp);
        
        // Response.txt(構成適用Start-DSC直後/初回の適用確認(Test-DSC)後の各ノード状態)
        try{
            $TestResponseChecktemp = new SplFileObject($meta['uri']); // Tempファイルパス取得
            $TestResponseChecktemp->setFlags(SplFileObject::READ_AHEAD | SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE );
        }
        catch (Exception $e){
            $outputLogAry = array( $info["EXE_NO"], $ResponseFilePathStr, $e->getMessage() );
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ));
            httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS, $objMTS->getSomeMessage("ITADSCH-ERR-990040", $outputLogAry ),$TempFilePathStr);
        }

        $TestResponseCheckListAry = array(); 
         
        foreach( $TestResponseChecktemp as $CheckNodePara) {
            if( (preg_match( "/^[0-9]+$/", $CheckNodePara[0])) ){
                // ノードカウンタ行（1行目）をスキップ
                continue;
            }
            list( $TargetNodeIp, $TargetNodeStatus, $TagetNodeDetail, $TmpTimeStamp ) = $CheckNodePara;
            
            // RUNNINGステータスのノード以外を除外( "FAILED"=エラー, "SUCCEED"=処理完了を除外 )
            if( $TargetNodeStatus !== 'RUNNING'){
                continue;
            }
            $TestResponseCheckListAry[] = $CheckNodePara;                 // ノード状態リスト
        }
        
        unset($TestResponseChecktemp);
        fclose($temp);
        
        // テスト実行後にRUNNING状態のみのノードリストの認証情報リストを作成
        // Check可能ノードリスト(IPのみ)作成(ステータス:RUNNING)
        $CheckPermitNodeAry = array();
        $CheckPermitNodeTimeStampAry = array();
        foreach( $TestResponseCheckListAry as $CheckNodePar ){
            if( $CheckNodePar[1] === "RUNNING" ){
                $CheckPermitNodeAry[] = $CheckNodePar[0];
                $CheckPermitNodeTimeStampAry[] = $CheckNodePar[3];
                continue;
            }
        }

        $CheckNodeStatusArray = array();
        
        // Get-DscLocalConfigurationManager Loop Start -->
        foreach( $DeviceInfoAry as $TestNodePara ){
            list( $TargetIpStr, $TargetUsernameStr, $TargetPasswordStr, $TargetThumbprint, $TargetRetrytimeout) = $TestNodePara;
            
            // テスト処理可能判断
            if( (array_search($TargetIpStr, $CheckPermitNodeAry)) === FALSE ){
                continue; // チェック処理可能リストにﾉｰﾄﾞIPがなかったら(RUNNINGノード以外)処理除外
            }
            
            //  Get-DscLocalConfigurationManager Cmdlet作成 
            $cmd = sprintf ( "powershell -File $PSScriptFilePathGetStStr %s \"%s\" \"%s\" 2>&1" ,
                                                  $TargetIpStr,
                                                  $TargetUsernameStr,
                                                  $TargetPasswordStr
                           );

            // 『確認処理』：Get-DscLocalConfigurationManager実行  //
            // TEST-DSCConfiguration前にCommandletの実行が可能か対象ノードのステータスを取得する
            $array_out = array();
            $return_var = 0;
            $PowershellLogOutputStr = NULL;
            exec( $cmd, $array_out, $return_var );

            // $array_out(powershellの実行結果)を行毎にファイルに出力
            $PowershellLogOutputStr = implode ( PHP_EOL, $array_out );
            file_put_contents( $TempFilePathStr, $PowershellLogOutputStr . PHP_EOL, FILE_APPEND );
            
            $NodeStatusStr = "";
            $StatusCodeInt = "";
            $logs = "";
            $LastTimeStamp = 0;

            // LcmStatus: 0=="Idle",-1=="Busy", -2=="PendingReboot" 以外(エラー)
            if(( $return_var !== 0 ) && ( $return_var !== -1 ) && ($return_var !== -2)) {
                switch($return_var){
                // DSC  エラーパターン (Case XX :PowerShell 戻値)
                case 50: // DSC処理フリーズ
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990052", $outputLogAry ));
                    break;

                case 55:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990053", $outputLogAry ));
                    break;

                case 56:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990054", $outputLogAry ));
                    break;

                case 57:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990055", $outputLogAry ));
                    break;

                case 58:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // 作業対象ノード情報取得失敗
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990056", $outputLogAry ));

                    break;
                default:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                    break;
                }

                // 2018.05.07 リトライ用にタイムスタンプを比較（5分位はリトライ実施）
                $NowTimeStamp = time();
                $tmpKey = array_search($TargetIpStr, $CheckPermitNodeAry);
                $LastTimeStamp = $CheckPermitNodeTimeStampAry[$tmpKey];

                // 最後に成功してから、XX分経過しているならばエラー それ以外は正常とする
                if( ( $NowTimeStamp - $LastTimeStamp ) > $TargetRetrytimeout ) {
                    $NodeStatusStr = "FAILED";
                    $StatusCodeInt = $return_var; // 50=LcmStatus:"PendingConfiguration"

                    // Configuration Processing Script File Path
                    $PSScriptFilePathStr = $current_path . DIRECTORY_SEPARATOR . $RemoveDscConfigDocStr; 

                    $cmd = sprintf("powershell -File $PSScriptFilePathStr \"%s\" \"%s\" \"%s\" 2>&1",
                                              $TargetIpStr,
                                              $TargetUsernameStr,
                                              $TargetPasswordStr );
                    $array_out = array();
                    $return_var2 = 0;
                    $PowershellLogOutputStr = NULL;
                    exec( $cmd, $array_out, $return_var2 );

                    // $array_out(powershellの実行結果)を行毎にファイルに出力
                    $PowershellLogOutputStr = implode ( PHP_EOL, $array_out );
                    file_put_contents( $TempFilePathStr, $PowershellLogOutputStr . PHP_EOL, FILE_APPEND );

                } else {
                    $NodeStatusStr = "RUNNING";
                    $NodeStatusStr = -1;
                    $return_var = -1;
                }
            }

            if( $LastTimeStamp === 0 ) {
                $LastTimeStamp = time();
            }

            // 対象ノードステータス
            if($return_var === 0 ){ // 処理前もしくは処理完了状態
                $NodeStatusStr = "IDLE";      // status idle
                $StatusCodeInt = $return_var;
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $NodeStatusStr, $StatusCodeInt );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990025", $outputLogAry ));

            }
            elseif($return_var === -1 ){ // 構成適用処理中
                $NodeStatusStr = "RUNNING";   // status busy
                $StatusCodeInt = $return_var;
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $NodeStatusStr, $StatusCodeInt );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990026", $outputLogAry ));

            }
            elseif($return_var === -2 ){ // 再起動要求中(PendingReboot) -2
                $NodeStatusStr = "PENDING";   // -2=LcmStatus:"PendingReboot"
                $StatusCodeInt = $return_var;
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr, $NodeStatusStr, $StatusCodeInt );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990027", $outputLogAry ));

            }
            
            //  Get-DscLocalConfigurationManager 実行後ターゲット状態情報の保存
            $CheckNodeStatusArray[] = array($TargetIpStr,$NodeStatusStr,$StatusCodeInt,$LastTimeStamp);
            
        } // Get-DSCLocalConfigurationManager Node Loop END 確認ループここまで
        
        // テスト実行可能ノードリスト作成
        $ExecPermitNode = array();
        foreach( $CheckNodeStatusArray as $NodePara ){
            if( $NodePara[1] === "IDLE" ){
                $ExecPermitNode[] = $NodePara[0];
                continue;
            }
        }
        
        // 確認処理完了
        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"]);
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990024", $outputLogAry ));
        
//----> 確認君ここまで---------------------------------------------------------------

//---->?TEST君(IDLE状態のノードに対してTest-DSCを試行する)ここから-------------------
        
        ////////////////////////////////////////////////
        // 『テスト処理』：実行                       //
        ////////////////////////////////////////////////
        // テスト処理開始
        $outputLogAry = array( $info["EXE_NO"], $info["DSC_DATA_CONFIG_DIR"]);
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990028", $outputLogAry ));
        
        $TestNodeResponseAftArray = array();

        /// ノード毎 確認処理ループ     # $DeviceInfoAry = 全対象Node認証情報配列
        foreach( $DeviceInfoAry as $TestNodePara ){
            list( $TargetIpStr, $TargetUsernameStr, $TargetPasswordStr) = $TestNodePara;
            
            // テスト処理可能判断
            if((array_search($TargetIpStr, $ExecPermitNode)) === FALSE ){
                continue; // テスト処理可能リストに対象ノードＩＰがなかったら(RUNNING/FAILEDノード)処理除外
            }
            
            //  Test-DSCconfigration Cmdlet作成 
            $cmd = sprintf ( "powershell -File $PSScriptFilePathTestStr %s \"%s\" \"%s\" 2>&1" ,
                                                  $TargetIpStr,
                                                  $TargetUsernameStr,
                                                  $TargetPasswordStr
                           );

            // TEST-DSCConfiguration実行  //
            $array_out = array();
            $return_var = NULL;
            $PowershellLogOutputStr = NULL;
            exec( $cmd, $array_out, $return_var );

            // $array_out(powershellの実行結果)を行毎にファイルに出力
            $PowershellLogOutputStr = implode( PHP_EOL, $array_out );
            file_put_contents( $TempFilePathStr, $PowershellLogOutputStr . PHP_EOL, FILE_APPEND );

            $NodeStatusStr = "";
            $StatusCodeInt = "";
            
//            if( !($return_var === 0) )
            if( $return_var !== 0 )
            {
                $tmpTimeStamp = time();
                $NodeStatusStr = "FAILED"; // 構成適用処理に失敗
                switch($return_var){
                
                // DSC  エラーパターン (Case XX :PowerShell 戻値)
                case 35:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // Cimセッションパラメータの暗号化失敗
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990057", $outputLogAry ));
                    break;

                case 36:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // Credentialオブジェクトのインスタンス生成失敗
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990058", $outputLogAry ));
                    break;

                case 37:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // Cimセッション生成で例外エラー
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990059", $outputLogAry ));
                    break;

                case 39:
                    $outputLogAry = array( $info["EXE_NO"], $TargetIpStr ); // 作業対象ノードの現在の構成とLCM内の構成情報に差異がある
                    outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990060", $outputLogAry ));
                    break;

                default:
                    break;
                }
                
            }
            if($return_var === 0 ){ // LCMの構成情報と作業対象ノードの構成は一致
                $NodeStatusStr = "SUCCEED";
                $StatusCodeInt = 0;
                $tmpTimeStamp = time();
                $outputLogAry = array( $info["EXE_NO"], $TargetIpStr );
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990030", $outputLogAry ));
            }
            
            // Response TEST-DSCConfiguration 後のターゲット状態情報の収集)テスト実行されたノードだけしかこの配列には存在しない。
            $TestNodeResponseAftArray[] = array($TargetIpStr,$NodeStatusStr,$StatusCodeInt,$tmpTimeStamp);
            
        } // TEST-DSCConfiguration Node Loop END 
        
        // Test実行処理の結果(ステータス)を$CheckNodeStatusArray(確認君の結果)へマージ更新するIDLE-> SUCCEED or FAILED
        foreach($CheckNodeStatusArray as &$CheckList_row) {
            $ListIpStr  = $CheckList_row[0];
            
            foreach($TestNodeResponseAftArray as $Exec_row) {
                $ExecIpStr = $Exec_row[0];
                // ﾉｰﾄﾞの状態,詳細ｺｰﾄﾞの更新
                if ($ListIpStr === $ExecIpStr){
                    
                    $CheckList_row[1] = $Exec_row[1];
                    $CheckList_row[2] = $Exec_row[2];
                    $CheckList_row[3] = $Exec_row[3];
                    
                    break;
                }
            }
        }
        
        // TODO このResponse.txtの読出しは、Check前の読出し（$TestResponseCheckListAry）にまとめられるかもしれない
        // TEST-DSCConfig実行後 Response.txt読出し
        $TestfileResponse = file_get_contents($ResponseFilePathStr);
        
        $TestfileResponse = mb_convert_encoding($TestfileResponse, 'UTF-8', 'sjis-win');
        
        // Response.txtの内容(適用処理直後)をtmpfileへ出力
        $temp = tmpfile();
        $meta = stream_get_meta_data($temp);
        
        fwrite($temp, $TestfileResponse);
        rewind($temp);
        
        $TestResponseUpdatetemp = new SplFileObject($meta['uri']);
        $TestResponseUpdatetemp->setFlags(SplFileObject::READ_AHEAD | SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE );
        
        $TestResponseListAry = array(); // Response.txt(構成適用後ノード状態記録ファイル)の情報をいれる配列を用意
        
        foreach($TestResponseUpdatetemp as $row) {
            if( (preg_match( "/^[0-9]+$/", $row[0])) ){
                // ノードカウンタ行（1行目）をスキップ
                continue;
            }
            $TestResponseListAry[] = $row;                 //ファイル側配列として格納
        }
        
        fclose($temp);
        unset($TestResponseUpdatetemp);

        // 読みだしたResponse.txtのノード情報を直前のテスト(確認)処理結果から更新
        foreach($TestResponseListAry as &$file_row) {
            $ResponseTxtIP = $file_row[0];
            
            foreach($CheckNodeStatusArray as $List_row) {
                $TestResponseListIP = $List_row[0];
                // ﾉｰﾄﾞの状態,詳細ｺｰﾄﾞの更新
                if ($ResponseTxtIP === $TestResponseListIP ){
                    
                    $file_row[1] = $List_row[1];
                    $file_row[2] = $List_row[2];
                    $file_row[3] = $List_row[3];

                    break;
                }
            }
        }

        // Response.txt の更新用配列を作成
        $TestOutputResponseAftArray = array(); // Response.txt更新用の配列
        $TestOutputResponseAftArray[] = array( $DeviceNum ); // 処理ノード数 １行目に挿入
        // ノード情報更新とResult出力フラグチェック
        foreach( $TestResponseListAry as $line){
            $TestOutputResponseAftArray[] = $line;
            // $line[1] = ﾉｰﾄﾞのｽﾃｰﾀｽが未だに処理継続中のﾉｰﾄﾞをｶｳﾝﾄする
            if($line[1] === $ResponseStatusRunStr ){
                 $RunningNodeCount++;
            }
        }

        // ノード情報更新配列をCSV形式でファイルに書き出す
        $TestResponseUpdate = new SplFileObject( $ResponseFilePathStr, 'w');
        foreach( $TestOutputResponseAftArray as $line){
        
            $TestResponseUpdate->fputcsv($line, ',');
        }
        $TestResponseUpdate = NULL;
        
        //  再読み込み
        $TestOutputResponse = file_get_contents( $ResponseFilePathStr );
        
        // UTF-8からShift-JISへファイル全変換
        $TestOutputResponse = mb_convert_encoding( $TestOutputResponse, 'SJIS-win', 'UTF-8');
        // Shift-JISフォーマットとして再書き込み
        file_put_contents( $ResponseFilePathStr, $TestOutputResponse );

        unset($TestResponseListAry);
        unset($TestOutputResponse);
        
        //*************
        // Result.txt出力
        //************* 全対象ﾉｰﾄﾞ全ての処理結果が出た後にResult.txtの出力
        if($RunningNodeCount === 0 ){
            // COMPLETEの処理 （処理完了）$return_varがEXITｺｰﾄﾞを取得している 
            $strStatusForFile = 'COMPLETED;';
            $strFileBody = "{$strStatusForFile}{$RunningNodeCount}";

            if( is_file($ResultFilePathStr) === TRUE ) 
            {
                $outputLogAry = array( $info["EXE_NO"], $ResultFilePathStr ); // result.txtファイルが既に存在していた
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990039", $outputLogAry ));
                httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS,$logs,$TempFilePathStr);

            }
            
            $boolFilePut = file_put_contents($ResultFilePathStr, $strFileBody, LOCK_EX);
            
            if( $boolFilePut === FALSE )
            {
                $outputLogAry = array( $info["EXE_NO"], $ResultFilePathStr ); // 結果ファイル(result.txt)ファイルの新規作成失敗
                outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990061", $outputLogAry ));
                httpRespons(DSC_HTTP_STS_500,DSC_ERR_STATUS,$logs,$TempFilePathStr);
            }
            
        }
        
        // テスト処理完了
        $outputLogAry = array( $info["EXE_NO"], $ResultFilePathStr );
        outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-STD-990029", $outputLogAry ));

    } // 確認処理:END

////-------------------------------->『確認処理』ここまで

///<-------『後処理』(実行/確認処理共通)ここから---------------------->
    /////////////////////////////////////////////
    // 『後処理』(Tempファイルのログファイル出力等)//
    /////////////////////////////////////////////

    $TmpLogSjisStr = "";
    $TmpLogSjisStr = file_get_contents($TempFilePathStr);
    
    if( $TmpLogSjisStr === FALSE ) // phpエラーログの確認のため故意に
    {
        $logs = "";
    }

    
    // PowerShell出力時の文字コード"SJIS"をJSONエンコード前の"UTF-8"に変換する (httpRespons前処理)
    $logs = mb_convert_encoding( $TmpLogSjisStr, "UTF-8", "auto");

    /////////////////////////
    // 処理終了（正常終了）//
    /////////////////////////
    httpRespons( DSC_HTTP_STS_200, $StatusCodeInt, $logs, $TempFilePathStr );

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
    function httpRespons( $in_ResultStatusCode, $in_status, $in_logs, $in_logfile)
    {
    
        global $ExecuteLogPathStr;
        global $objMTS;
        
        $TmpLogSjisStr = file_get_contents($in_logfile);
        if( $TmpLogSjisStr === FALSE ) // phpエラーログの確認のため故意に
        {
            $logs = "";
        }
        file_put_contents( $ExecuteLogPathStr, $TmpLogSjisStr, FILE_APPEND );
        
        // システムテンポラリファイルを削除して閉じる
        if(is_file($in_logfile) === true)
        {
            @unlink($in_logfile);
        }

        // REST 格納情報作成
        $aryRespons = array('ResultCode'=>$in_status,
                            'Logs'=>$in_logs);
                            
        $objJSONOfResultData = json_encode($aryRespons);
        
        if( $objJSONOfResultData === false) {
            $outputLogAry = array( $info["EXE_NO"], $ExecuteLogPathStr ); // 
            outputLog(LOG_PREFIX, $objMTS->getSomeMessage("ITADSCH-ERR-990083", $outputLogAry ));
        
        }

        header('Content-Type: application/json; charset=utf-8', true, $in_ResultStatusCode);
        exit($objJSONOfResultData);
    }
    
// END
?>
