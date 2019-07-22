<?php
////////////////////////////////////////////////////////////////////////////////////
//  【概要】
//     common_functions.php (関数)
//     DSC RESTAPI 共通モジュール
//
//    F0001 checkAuthorizationInfo
//    F0002 checkRequestHeaderForAuth
//    F0003 getFileNameAndPath
//    F0004 outputLog
//
//  【備考】
//     6/8 OpenSSLによるITA/RESTAPI間の暗号認証が成功しないため、暗号文の生成処理をPHPのcrypt関数で代替する。
//     
////////////////////////////////////////////////////////////////////////////////////
    // 処理結果ステータス
    define("DSC_SUCCESS"         ,"0");
    define("DSC_ERR_HTTP_REQ"    ,"1000");  // HTTPパラメータ異常
    define("DSC_ERR_DIR"         ,"1001");  // DSCディレクトリの認識エラー
    define("DSC_ERR_HTTP_HEDER"  ,"1002");  // HTTPヘッダーに必要な情報がない
    define("DSC_ERR_AUTH"        ,"1003");  // アクセスキー認証エラー
    define("DSC_ERR_CONF"        ,"1004");  // DSC コンフィグレーション(実行)エラー
    define("DSC_ERR_STATUS"      ,"1005");  // DSC 実行ステータスエラー
    define("DSC_ERR_TEST"        ,"1006");  // DSC テスト(確認)エラー
    define("DSC_ERR_STOP"        ,"1007");  // DSC 実行ステータスエラー
    define("DSC_ERR_TAR_DEL"     ,"2000");  // Collect Commandで作成したZIPファイルの削除失敗時のrmコマンドの戻り値への加算値

    ////////////////////////////////////////////////////////////////////////////////
    // F0001
    // 処理内容
    //   RESTAPI コールのアクセスキー認証を行う
    // パラメータ
    //   $ina_ReqHeaderData:        HTTPヘッダー情報
    //   $in_ResultStatusCode:      HTTPレスポンスで通知するレスポンスコード (異常時のみ)
    //   $in_Exception:             HTTPレスポンスで通知するエラーメッセージ (異常時のみ)
    //
    // 備考
    //   $root_dir_path:            C:\inetpub\wwwroot
    //   
    // 戻り値
    //   true: 正常 FALSE:異常
    ////////////////////////////////////////////////////////////////////////////////
    function checkAuthorizationInfo( $ina_ReqHeaderData, &$in_ResultStatusCode, &$in_Exception )
    {
        global   $root_dir_path;

        $strCRLF = "\r\n";

        $in_ResultStatusCode = 0;
        $in_Exception        = "";

        // リクエストで送られてきた情報
        $strHeaderAuthorization     = $ina_ReqHeaderData['Authorization'];
        $strHeaderDate              = $ina_ReqHeaderData['Date'];
        $strRequestURIOnRest        = $_SERVER['PHP_SELF'];
        
        // サーバー上にある認証情報取得
        $strConfFileOfAccessKeyIdOnRest     = "\\confs\\restapiconfs\\dsc_driver\\accesskey.txt";

        $strAccessKeyIdOnRest               = file_get_contents( $root_dir_path . $strConfFileOfAccessKeyIdOnRest);

        $strAccessKeyIdOnRest               = ky_decrypt($strAccessKeyIdOnRest);

        $strConfFileOfSecretAccessKeyOnRest = "\\confs\\restapiconfs\\dsc_driver\\secret_accesskey.txt";

        $strSecretAccessKeyOnRest           = file_get_contents( $root_dir_path . $strConfFileOfSecretAccessKeyOnRest);

        $strSecretAccessKeyOnRest           = ky_decrypt($strSecretAccessKeyOnRest);

        $aryTempData = explode("SharedKeyLite {$strAccessKeyIdOnRest}:", $strHeaderAuthorization);

        if( count($aryTempData) != 2 )
        {
            $in_ResultStatusCode = 401;
            $in_Exception        = 'Authorization infomation format error';
            return FALSE;
        }

        $tmpStrStringToSignOnRest = $strHeaderDate . $strCRLF . $strRequestURIOnRest;
        
        // PHP cryptによる代替処理 6/6 saito
        $tmpStrSignatureOnRest = crypt( $tmpStrStringToSignOnRest, $strSecretAccessKeyOnRest);
        
        if( $tmpStrSignatureOnRest!==$aryTempData[1] )
        {
            $in_ResultStatusCode = 401;
            $in_Exception        = 'Authorization infomation is not correct';
            
            unset($strSecretAccessKeyOnRest);
            unset($tmpStrStringToSignOnRest);
            unset($tmpStrSignatureOnRest);
            return FALSE;
        }

        unset($strSecretAccessKeyOnRest);
        unset($tmpStrStringToSignOnRest);
        unset($tmpStrSignatureOnRest);

        return true;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0002
    // 処理内容
    //   HTTPヘッダーに必要な情報が設定されているか確認
    // パラメータ
    //   $ina_ReqHeaderData:     HTTPヘッダー情報を格納
    //   $in_ResultStatusCode:   HTTPレスポンスで通知するレスポンスコード (異常時のみ)
    //   $in_Exception:          HTTPレスポンスで通知するエラー詳細       (異常時のみ)
    //
    // 戻り値
    //   true: 正常 FALSE:異常
    ////////////////////////////////////////////////////////////////////////////////
    function checkRequestHeaderForAuth(&$ina_ReqHeaderData,&$in_ResultStatusCode,&$in_Exception)
    {
        $in_ResultStatusCode = 0;
        $in_Exception        = "";

        //リクエストヘッダ取得
        $ina_ReqHeaderData = getallheaders();

        if( $ina_ReqHeaderData === FALSE )
        {
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Request header unknown error';
            return FALSE;
        }
        //----http(s)リクエストヘッダに所定の項目があるかをチェック
        if( array_key_exists('Host', $ina_ReqHeaderData) !== true )
        {
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Host] is not exists';
            return FALSE;
        }
        
        if( array_key_exists('Content-Type', $ina_ReqHeaderData) !== true )
        {
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Content-Type] is not exists';
            return FALSE;
        }

        if( array_key_exists('X-Umf-Api-Version', $ina_ReqHeaderData) !== true )
        {
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[X-UMF-API-Version] is not exists';
            return FALSE;
        }
        
        if( array_key_exists('Date', $ina_ReqHeaderData) !== true )
        {
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Date] is not exists';
            return FALSE;
        }
        
        if( array_key_exists('Authorization', $ina_ReqHeaderData) !== true )
        {
            $in_ResultStatusCode = 400;
            $in_Exception        = 'Required request header item[Authorization] is not exists';
            return FALSE;
        }
        return true;
    }
    
    // 管理ログ出力ファンクション
    function DebugLogPrint($p1,$p2,$p3)
    {
        global $logfile;

        $tmpVarTimeStamp = time();
        $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);
        $filepointer=fopen(  $logfile, "a");
        flock($filepointer, LOCK_EX);
        fputs($filepointer, "[" . $logtime . "]" . $p1 . ":" . $p2 . ":" . $p3 . "\n" );
        flock($filepointer, LOCK_UN);
        fclose($filepointer);
        unset($tmpVarTimeStamp);
    }
    // 簡易IPv4 Preg_match
    function validateIP($Ipaddress){
        return inet_pton($Ipaddress) !== FALSE;
    }
    ////////////////////////////////////////////////////////////////////////////////
    // F0003
    // 【処理内容】
    //   ファイル存在チェック
    // 【パラメータ】
    // $strSearchDirPath
    // $strFilePrefix=''
    // $strFilePostFix=''
    //
    // 【戻り値】
    //   $aryFileList
    ////////////////////////////////////////////////////////////////////////////////
    function getFileNameAndPath($strSearchDirPath, $strFilePrefix='', $strFilePostFix=''){
        
        $boolExecuteContinue = true;
        $aryFileList = array();
        $target_file_path = "";
        if( is_string($strSearchDirPath)===FALSE )
        {
            $boolExecuteContinue = FALSE;
        }
        if( is_string($strFilePrefix)===FALSE )
        {
            $boolExecuteContinue = FALSE;
        }
        if( is_string($strFilePostFix)===FALSE )
        {
            $boolExecuteContinue = FALSE;
        }
        if( $boolExecuteContinue===true )
        {
            $aryFile = scandir($strSearchDirPath);
            foreach($aryFile as $strFileObjectName){
                if( 0 < strlen($strFilePrefix) )
                {
                    if( strpos($strFileObjectName,$strFilePrefix)!==0 )
                    {
                        //----見つからなかった、または、先頭ではなかった
                        continue;
                        //見つからなかった、または、先頭ではなかった----
                    }
                }
                if( 0 < strlen($strFilePostFix) )
                {
                    if( strpos(strrev($strFileObjectName),strrev($strFilePostFix))!==0 )
                    {
                        //----見つからなかった、または、末尾ではなかった
                        continue;
                        //見つからなかった、または、末尾ではなかった----
                    }
                }
                $strCheckPath = $strSearchDirPath .DIRECTORY_SEPARATOR. $strFileObjectName;
                if ( is_file($strCheckPath)===true )
                {
                    $aryFileList[] = $strCheckPath;
                }
            }
        }
        return $aryFileList;
    }
    
    ////////////////////////////////////////////////////////////////////////////////
    // F0004
    // 【処理内容】
    //   RESTAPI ログ出力
    //   Windows環境版
    // 【パラメータ】
    //      * @param    string    $msg    出力するメッセージ
    //
    // 【戻り値】
    //
    // 【備考】
    //   RESTAPI ログパス C:\inetpub\wwwroot\logs\restapilogs\dsc_driver\
    //
    ////////////////////////////////////////////////////////////////////////////////
    function outputLog($prefix, $msg)
    {
        $dt = '[' . date('Y/m/d H:i:s') . ']';
        $msg = $dt . $msg . "\r\n";
        $filePath = ROOT_DIR_PATH . LOG_DIR . $prefix . date('Ymd') . '.log';
        error_log($msg, 3, $filePath);
    }
    
?>
