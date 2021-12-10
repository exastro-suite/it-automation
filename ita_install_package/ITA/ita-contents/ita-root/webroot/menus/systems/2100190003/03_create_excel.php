<?php
//   Copyright 2021 NEC Corporation
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
//    ・比較結果のCSV,EXCEL出力
//
//////////////////////////////////////////////////////////////////////

$tmpAry=explode('ita-root', dirname(__FILE__));
$root_dir_path=$tmpAry[0].'ita-root';
unset($tmpAry);
$fileName = basename(__FILE__);

    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // browse系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");
        
        if( isset( $_POST['CONTRAST_ID'] ) === true ){
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($_POST['CONTRAST_ID']) !== true ){
                //パラメータ不正時
                throw new Exception();
            }
        }else{
            //パラメータ不正時
            throw new Exception();
        }
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }


    global $g;

    require_once($root_dir_path."/libs/webindividuallibs/systems/2100190003/81_contrast_controle.php");

    try{

        if ( isset($_POST['CONTRAST_ID']) )$intContrastid=htmlspecialchars($_POST['CONTRAST_ID']);
        if ( isset($_POST['BASE_TIMESTAMP_0']) )$strBaseTime0=htmlspecialchars($_POST['BASE_TIMESTAMP_0']);
        if ( isset($_POST['BASE_TIMESTAMP_1']) )$strBaseTime1=htmlspecialchars($_POST['BASE_TIMESTAMP_1']);
        if ( isset($_POST['HOST_LIST']) )$strhostlist = htmlspecialchars($_POST['HOST_LIST']);   
        if ( isset($_POST['FORMATTER_ID']) )$strFormat = htmlspecialchars($_POST['FORMATTER_ID']);
        if ( isset($_POST['OUTPUT_TYPE']) )$outputType = htmlspecialchars($_POST['OUTPUT_TYPE']);

        if ( isset($_POST['CONTRAST_ID']) === true ){
            //比較定義リスト表示用
            $arrayResult =  getContrastList(1);
            $arrContrastList = $arrayResult[2];

            $strContrastName="";
            foreach ($arrContrastList as $arrContrast) {
                if( $arrContrast['CONTRAST_LIST_ID'] == $intContrastid )$strContrastName = $arrContrast['CONTRAST_LIST_ID']."_".$arrContrast['CONTRAST_NAME'] ;
            }

            //出力時、ファイル名文字数制限
            $charlimit=128;
            if( mb_strlen($strContrastName) > $charlimit ){
                //ファイル名短縮                
                $strContrastName = mb_substr($strContrastName, 0, $charlimit, "UTF-8");
            }

            $outputdate=date('YmdHis');
            $outputfilename = $strContrastName . "_" . $outputdate;

            //比較結果取得 
            $arrayResult =  getContrastResult($intContrastid,$strBaseTime0,$strBaseTime1,$strhostlist,$outputType);

            //出力処理
            switch ( $strFormat ) {
                case 'csv':
                    $ext = "csv";
                    $outputfilename = $outputfilename .".". $ext;
                    //表示用データ
                    $arrContrastResult = $arrayResult[2][0];
                    exportCSV($outputfilename,$arrContrastResult);
                    break;
                case 'excel':
                    $ext = "xlsx";
                    $outputfilename = $outputfilename .".". $ext;
                    //表示用データ
                    $arrContrastResult = $arrayResult[2][0];
                    //強調表示用フラグデータ
                    $arrContrastflg = $arrayResult[2][1];
                    exportExcel($outputfilename,$arrContrastResult,$arrContrastflg);
                    break;
                default:
                    //パラメータ不正時
                    throw new Exception(); 
                    break;
            }        
        }else{
            //パラメータ不正時
            throw new Exception();
        }
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
