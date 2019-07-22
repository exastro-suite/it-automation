<?php

    // ----ルートディレクトリを取得
    if ( empty($root_dir_path) )
    {
        $root_dir_temp = array();
        $root_dir_temp = explode( "wwwroot", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "wwwroot";
        unset($root_dir_temp);
    }
    
    // ルートディレクトリを取得----

    require_once ($root_dir_path . "\libs\commonlibs\common_php_functions.php");
    require_once ($root_dir_path . "\libs\commonlibs\common_php_classes.php");

    $strReqInitTime = getMircotime();

    if( ini_get('safe_mode')=='1' )
    {
        echo("PHP is now safemode.");
        exit();
    }

    $objMTS = new MessageTemplateStorage();

    // 注意：オンラインアクセスの場合ここでの作成が代表となるので重要。
    $arrayReqInfo = requestTypeAnalyze();
    if( $arrayReqInfo[0] == "web" )
    {
        require_once ($root_dir_path . "\libs\webcommonlibs\web_php_functions.php"); // #1281 2017/10/20
        if( empty( $scheme_n_authority ) )
        {
            // URLのスキーム＆オーソリティを取得
            $protocol = getRequestProtocol();
            $scheme_n_authority = getSchemeNAuthority();
        }
    }
    else if( $arrayReqInfo[0] == "backyard" )
    {
    
    }
    else
    {
        echo("Request type is unexpected.");
        exit();
    }

    // 特別なオーダーがない限り、DBへ接続する
    // DSC サーバ側ではDB参照は想定されないので、特別なオーダーとし非接続設定とする DSC対応 2017/09/03
    $tmpBoolDBConnect = FALSE;
    if( isset($aryOrderToReqGate['DBConnect']) )
    {
        if( $aryOrderToReqGate['DBConnect'] == 'LATE' )
        {
            $tmpBoolDBConnect = false;
        }
    }

    if( $tmpBoolDBConnect === true )
    {
        require_once($root_dir_path . "\libs\commonlibs\common_db_connect.php");
               
    }
    unset($tmpBoolDBConnect);

?>