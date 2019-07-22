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
    //  【処理概要】
    //    「資材一括ダウンロード」ボタン押下時に、画面に表示されている資材を
    //    ZIPで固めてダウンロード可能とする。
    //
    //////////////////////////////////////////////////////////////////////

    // ----DBアクセスを伴う処理
    try{
        //----ここから01_系から06_系全て共通
        // DBコネクト
        require_once ( $g['root_dir_path'] . "/libs/commonlibs/common_php_req_gate.php");
        // 共通設定取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        // メニュー情報取得パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_menu_info.php");
        //ここまで01_系から06_系全て共通----

        // access系共通ロジックパーツ01
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_for_access_01.php");

    }
    catch (Exception $e){
        // ----DBアクセス例外処理パーツ
        require_once ( $g['root_dir_path'] . "/libs/webcommonlibs/web_parts_db_access_exception.php");
        // DBアクセス例外処理パーツ----
    }

    $aryVariant = array('search_filter_data'=>$_POST['filter_data']);

    // ----ローカル変数宣言
    $ACRCM_id = "UNKNOWN";

    $strErrMsgBodyToHtmlUI = "";
    $strErrMsgBodyToWAL = "";

    $tmpDir = "";
    $zip = NULL;

    $resultArray = array();
    // ローカル変数宣言----

    try{
        //----メニューIDの取得
        list($ACRCM_id,$boolKeyExists) = isSetInArrayNestThenAssign($g,array('menu_id'),"undefined");
        //メニューIDの取得----

        require_once ("{$g['root_dir_path']}/libs/webindividuallibs/systems/2100150004/98_searchTableFunc.php");
        $result = searchFunc($aryVariant, $resultArray, $strErrMsgBodyToHtmlUI);

        if(true !== $result){
            throw new Exception("");
        }

        if(0 === count($resultArray)){
            $strErrMsgBodyToHtmlUI = "the data may have been updated.";
            throw new Exception($strErrMsgBodyToHtmlUI);
        }

        // 重複している場合は項番が大きいほうを採用する
        $tmpArray = $resultArray;
        $resultArray = array();
        foreach($tmpArray as $tmpData){

            $fileIdArray = array_column($resultArray, 'FILE_ID');

            $matchId = array_search($tmpData['FILE_ID'], $fileIdArray);
            if(false !== $matchId){
                $resultArray[$matchId] = $tmpData;
            }
            else{
                $resultArray[] = $tmpData;
            }
        }

        // 最新時間を取得
        $now = \DateTime::createFromFormat("U.u", sprintf("%6F", microtime(true)));
        $nowTime = date("YmdHis") . $now->format("u");

        $tmpDir = $g['root_dir_path'] . "/temp/" . $nowTime;

        // 作業用ディレクトリ作成
        $output = NULL;
        $cmd = "mkdir -m 777 -p -- '" . $tmpDir . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            throw new Exception("An error is occurred in command[" . $cmd . "].error=[" . print_r($output, true) . "]");
        }

        // ZIPファイルパス
        $zipFilePath = $tmpDir . "/" . $nowTime . ".zip";

        $zip = new ZipArchive;
        if(true != $zip->open($zipFilePath, ZipArchive::CREATE)){
            throw new Exception("Zip file[" . $zipFilePath . "] could not be open.");
        }

        // 取得した件数分ループ
        foreach($resultArray as $row){

            $fileId = $row['FILE_ID'];

            $query01 = "SELECT FILE_NAME_FULLPATH "
                        ." FROM G_FILE_MASTER "
                        ." WHERE FILE_ID = :FILE_ID "
                        ." AND DISUSE_FLAG = '0' ";

            $aryForBind01['FILE_ID'] = $fileId;

            // SQL発行
            $retArray01 = singleSQLExecuteAgent($query01, $aryForBind01, "");

            if( $retArray01[0] === true ){
                $objQuery01 =& $retArray01[1];
                $intCount01 = 0;
                $aryDiscover01 = array();
                while($row01 = $objQuery01->resultFetch()){
                    $intCount01 += 1;
                    $aryDiscover01[] = $row01;
                }
                unset($objQuery01);

                if(1 === $intCount01){
                    $fileNameFullpath = $aryDiscover01[0]['FILE_NAME_FULLPATH'];
                }
                else{
                    throw new Exception("table[F_FILE_MASTER] may have been updated.");
                }
            }
            else{
                throw new Exception("");
            }

            // コピー元ファイルパス
            $fromPath = $g['root_dir_path'] . "/uploadfiles/2100150101/RETURN_FILE/" . sprintf("%010d", $row['FILE_M_ID']) . "/" . $row['RETURN_FILE'];

            // コピー先ファイルパス
            $toPath = substr($fileNameFullpath , 1);
            $encToPath = mb_convert_encoding($toPath, 'SJIS', 'UTF-8');

            // ファイルコピー
            $result = $zip->addFile($fromPath, $encToPath);

            if(true != $result){
                throw new Exception("File[" . $fromPath . "] could not added to zipfile[" . $zipFilePath . "].error=[" . $result . "]");
            }
        }

        $zip->close();
        $zip = NULL;

        // ----MIMEタイプの設定
        printHeaderForProvideFileStream("material_" . date("YmdHis") . ".zip");
        // MIMEタイプの設定----

        // out of memoryエラーが出る場合に出力バッファリングを無効
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        ob_start();

        // ファイル出力
        if ($file = fopen($zipFilePath, 'rb')) {
            while(!feof($file) and (connection_status() == 0)) {
                echo fread($file, '4096'); //指定したバイト数ずつ出力
                ob_flush();
            }
            ob_flush();
            fclose($file);
        }
        ob_end_clean();

        // 作業用ディレクトリ削除
        $output = NULL;
        $cmd = "rm -rf -- '" . $tmpDir . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            throw new Exception("An error is occurred in command[" . $cmd . "].error=[" . print_r($output, true) . "]");
        }
    }
    //----エラー発生時
    catch (Exception $e){
        // ZIPファイルをクローズする
        if(NULL != $zip){
            $zip->close();
        }

        // 作業ディレクトリを削除する
        if(file_exists($tmpDir)){
            $output = NULL;
            $cmd = "rm -rf -- '" . $tmpDir . "' 2>&1";
            exec($cmd, $output, $return_var);
        }

        // ----一般訪問ユーザに見せてよいメッセージを作成
        if("" === $strErrMsgBodyToHtmlUI){
            // システムエラーが発生しました。
            $strErrMsgBodyToHtmlUI = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
            // 一般訪問ユーザに見せてよいメッセージを作成----
        }

        header("Content-Type: text/html; charset=UTF-8");
        print 
<<< EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<script language="JavaScript">
window.onload = function(){
    var strMsg = document.getElementById('msgbody').innerHTML;
    alert(strMsg);
}
</script>
<div id="msgbody" style="display:none">
{$strErrMsgBodyToHtmlUI}
</div>
</body>
</html>
EOD;

        $tmpErrMsgBody = $e->getMessage();

        if("" !== $tmpErrMsgBody){
            $strErrMsgBodyToWAL = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-303",array($ACRCM_id, "ZIP", $tmpErrMsgBody));
        }

        // アクセスログへ記録
        if( 0 < strlen($strErrMsgBodyToWAL) ) web_log($strErrMsgBodyToWAL);

        //エラー発生時----
        return false;
    }

    // ----アクセスログ出力
    web_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-603"));
    // アクセスログ出力----
    return true;
?>
