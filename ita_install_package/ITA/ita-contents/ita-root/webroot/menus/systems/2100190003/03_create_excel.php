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

// メニューID取得
$_GET_Id = "";
if(array_key_exists('no', $_GET)){
    $_GET_Id = htmlspecialchars($_GET['no']);
}


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

    //PhpSpreadsheet関連
    require_once "vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Collection\CellsFactory;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
    use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
    use PhpOffice\PhpSpreadsheet\Cell\DataType;
    use PhpOffice\PhpSpreadsheet\Settings;
    use PhpOffice\PhpSpreadsheet\Style\Fill;
    use PhpOffice\PhpSpreadsheet\Style\Border;

    global $g;

    require_once($root_dir_path."/libs/webindividuallibs/systems/".$_GET_Id."/81_contrast_controle.php");

    try{

        if ( isset($_POST['CONTRAST_ID']) )$intContrastid=htmlspecialchars($_POST['CONTRAST_ID']);
        if ( isset($_POST['BASE_TIMESTAMP_0']) )$strBaseTime0=htmlspecialchars($_POST['BASE_TIMESTAMP_0']);
        if ( isset($_POST['BASE_TIMESTAMP_1']) )$strBaseTime1=htmlspecialchars($_POST['BASE_TIMESTAMP_1']);
        if ( isset($_POST['HOST_LIST']) )$strhostlist = htmlspecialchars($_POST['HOST_LIST']);   
        if ( isset($_POST['FORMATTER_ID']) )$strFormat = htmlspecialchars($_POST['FORMATTER_ID']);

        if ( isset($_POST['CONTRAST_ID']) === true ){
            //比較定義リスト表示用
            $arrayResult =  getContrastList($intContrastid,$strBaseTime0,$strBaseTime1,$strhostlist);
            $arrContrastList = $arrayResult[2];

            $strContrastName="";
            foreach ($arrContrastList as $arrContrast) {
                if( $arrContrast['CONTRAST_LIST_ID'] == $intContrastid )$strContrastName = $arrContrast['CONTRAST_LIST_ID']."_".$arrContrast['CONTRAST_NAME'] ;
            }
            $outputdate=date('YmdHis');
            $outputfilename = $strContrastName . "_" . $outputdate;

            //比較結果取得 
            $arrayResult =  getContrastResult($intContrastid,$strBaseTime0,$strBaseTime1,$strhostlist);

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


//CSV出力
function exportCSV($filename, $data){
    $fp = fopen('php://output', 'w');

    stream_filter_append($fp, 'convert.iconv.UTF-8/CP932//TRANSLIT', STREAM_FILTER_WRITE);

    foreach ($data as $row) {
        fputcsv($fp, $row, ',', '"');
    }
    fclose($fp);

    header('Content-Type: application/octet-stream');
    header("Content-Disposition: attachment; filename={$filename}");
    header('Content-Transfer-Encoding: binary');
    exit;
}

//Excel出力
function exportExcel($filename, $data, $flgdata){
    
    global $g;
    
    //行の英字取得
    $maxRow = count( $data );
    $maxCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex( count($data[0]) );
    
    //開始、終了セル
    $startCell = "A1";
    $endCell = $maxCol.$maxRow;

    //初期設定
    $spreadsheet = new Spreadsheet();
    $spreadsheet->getDefaultStyle()->getFont()->setName(  $g['objMTS']->getSomeMessage("ITABASEH-MNU-310222",__FUNCTION__) ); #メイリオ/Arial(jp/en)
    $spreadsheet->getDefaultStyle()->getFont()->setSize( 8 );

    //アクティブシートを取得
    $sheet = $spreadsheet->getActiveSheet();
    //グリッド非表示
    $sheet->setShowGridlines(false);
    //列固定
    $sheet->freezePane( 'D2' );

    //配列からセルへデータ格納
    $sheet->fromArray($data, NULL, 'A1', true);

    //スタイルの設定
    //ヘッダー設定
    $startheaderCell = $startCell;
    $endheaderCell = $maxCol."1";
    $targetCell= "${startheaderCell}:${endheaderCell}";

    //ヘッダー、背景色、文字色設定
    $sheet->getStyle($targetCell)->getFont()->getColor()->setRGB('FFFFFF');
    $sheet->getStyle($targetCell)->getFill()->setFillType(Fill::FILL_SOLID);
    $sheet->getStyle($targetCell)->getFill()->getStartColor()->setRGB('0045A7');

    //差分強調表示
    foreach ( $flgdata as $rownum => $arrcol ) {
        foreach ($arrcol as $colnum => $contrastflg) {

            //対象セル、値
            $cellname =  \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex( $colnum + 1 ) . ($rownum + 1) ;
            $cellval = $sheet->getCell($cellname)->getValue();

            //比較対象項目のみ文字列表記
            if( $colnum > 6 ){
                $sheet->setCellValueExplicit($cellname,$cellval,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }

            //強調表示
            if( $contrastflg == 1 ){
                $sheet->getStyle($cellname)->getFont()->getColor()->setRGB('ff0000');
                $sheet->getStyle($cellname)->getFont()->setBold(true);
            }
        }
    }

    //格子設定
    $targetCell= "${startCell}:${endCell}";
    $sheet->getStyle($targetCell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle($targetCell)->getBorders()->getAllBorders()->getColor()->setRGB('7f7f7f');

    //折り返し設定
    $targetS = "H2";
    $targetCell= "${targetS}:${endCell}";
    $sheet->getStyle($targetCell)->getAlignment()->setWrapText(true);
    $sheet -> setAutoFilter( $sheet -> calculateWorksheetDimension() );

    //幅、高さ設定
    $maxCol=count($data[0]);
    for($i_col = 1; $i_col <= $maxCol; ++$i_col){
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i_col))->setAutoSize(true);
    }
    $sheet->calculateColumnWidths();
    for($i_col = 1; $i_col <= $maxCol; ++$i_col){
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i_col))->setAutoSize(false);
    }
    for($i_col = 1; $i_col <= $maxCol; ++$i_col){
        $width = $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i_col))->getWidth();

        //フィルタ▼幅調整
        $width = $width + 4.5;
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i_col))->setWidth($width);  

    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    exit;
}