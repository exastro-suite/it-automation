<?php
header('Content-Type: text/css; charset=utf-8');

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

function itaThemeSelect() {

$theme = htmlspecialchars($_GET['theme'], ENT_QUOTES, "UTF-8");




// Theme color set
// MENU [0]:HEADER [1]:LIST [2]:SELECT TEXT [3]:MAIN MENU TEXT [4]:MAIN MENU HOVER BACK
// TABLE [0]:TH [1]:TH BORDER [2]:TD [3]:TD BORDER [4]:HOVER
// TABLE_F [0]:TH [1]:TH BORDER [2]:TD [3]:TD BORDER
// BUTTON [0]:LINK [1]:HOVER
// HEADING [0]:BACK [1]:BORDER [2]:TEXT
$c = array(
  'BODY' => '333333',
  'HEADER' => '002B62',
  'FOOTER' => '444444',
  'MENU' => array('333333','666666','002B62','000000','FFFFFF'),
  'TEXT' => array('1A1A1A','F2F2F2'),
  'TABLE' => array('888888','666666','F2F2F2','CCCCCC','FFFF8F'),
  'TABLE_F' => array('777777','555555','E2E2E2','AAAAAA'),
  'BUTTON' => array('FF821E','FF640A'),
  'KIZI' => 'FFFFFF',
  'MENU_NAME' => '1A1A1A',
  'HEADING' => array('335581','FFA032','FFFFFF'),
  'BASE' => 'E6E6E6',
  'REVERSE' => array('000000','FFFFFF'),
  'DARKMODE' => 'OFF'
);

// Theme Heading patterns
$headingPatterns = array(
  'DEFAULT' => 'border-left-width:5px;border-left-style:solid;',
  'OASE' => 'border-top-width:2px;border-top-style:solid;border-radius:0 0 4px 4px;background-image:linear-gradient(transparent,rgba(0,0,0,.1));border-bottom:1px solid #CCC;border-left:1px solid #CCC;border-right:1px solid #CCC;'
);

$h = array(
  'KIZI' => $headingPatterns['DEFAULT']
);

// Theme background patterns
$backgroundPatterns = array(
  'ROUND0' => 'background-image: linear-gradient( rgba( 255,255,255,.05 ), transparent );',
  'ROUND1' => 'background-image: linear-gradient( rgba( 255,255,255,.1 ), transparent );',
  'ROUND2' => 'background-image: linear-gradient( rgba( 255,255,255,.2 ), transparent );',
  'ROUND3' => 'background-image: linear-gradient( transparent, rgba( 0,0,0,.2 ) );',
  'MESH1' => 'background-image: repeating-linear-gradient( 90deg, transparent, transparent 100px, rgba(0,0,0,.05) 100px, rgba(0,0,0,.05) 101px, transparent 101px, transparent 200px, rgba(0,0,0,.05) 200px, rgba(0,0,0,.05) 201px), repeating-linear-gradient( 0deg, transparent, transparent 100px, rgba(0,0,0,.05) 100px, rgba(0,0,0,.05) 101px, transparent 101px, transparent 200px, rgba(0,0,0,.05) 200px, rgba(0,0,0,.05) 201px);background-position: 50px 50px;',
  'STRIPE0' => 'background-image: linear-gradient( transparent, transparent 2px, rgba( 255,255,255,.05 ) 2px, rgba( 255,255,255,.05 ) 3px); background-size: 3px 3px;',
  'STRIPE1' => 'background-image: linear-gradient( 90deg, transparent, transparent 50%, rgba( 255,255,255,.2 ) 50%, rgba( 255,255,255,.2 )); background-size: 64px 64px;',
  'STRIPE2' => 'background-image: linear-gradient( transparent, transparent 2px, rgba( 255,255,255,.5 ) 2px, rgba( 255,255,255,.5 ) 3px); background-size: 3px 3px;',
  'CHECK1' => 'background-image: linear-gradient( 45deg, rgba( 255,255,255,.1 ) 25%, transparent 25%, transparent 75%, rgba( 255,255,255,.1 ) 75%),linear-gradient( 45deg, rgba( 255,255,255,.1 ) 25%, transparent 25%, transparent 75%, rgba( 255,255,255,.1 ) 75%);background-size: 64px 64px;background-position: 0 0, 32px 32px;',
  'OASE' => 'background-image: url(./imgs/oase_background.png);background-size: 100% 100%;background-attachment: fixed; background-position: 180px 0;',
  'NONE' => 'background-image: none;'
);

// Theme background set
$b = array(
  'BODY' => $backgroundPatterns['STRIPE0'],
  'HEADER' => $backgroundPatterns['ROUND2'],
  'FOOTER' => $backgroundPatterns['NONE'],
  'MENU' => $backgroundPatterns['ROUND0'],
  'BASE' => $backgroundPatterns['MESH1'],
  'KIZI' => $backgroundPatterns['NONE'],
  'HEADING' => $backgroundPatterns['NONE']
);

switch( $theme ) {
  case 'default':
    break;
    
  case 'green':
    $c['HEADER'] = '42952B';
    $c['BODY'] = '1E4313';
    $c['MENU'] = array('326F20','28591A','28591A','000000','FFFFFF');
    $c['BASE'] = 'BBE7AF';
    $c['HEADING'] = array('42952B','28591A','FFFFFF');
    break;
      
  case 'purple':
    $c['HEADER'] = '995DEE';
    $c['BODY'] = '291940';
    $c['MENU'] = array('452A6B','372256','372256','000000','FFFFFF');
    $c['BASE'] = 'C0A2EB';
    $c['HEADING'] = array('995DEE','5C388F','FFFFFF');
    break;
    
  case 'red':
    $c['HEADER'] = '962027';
    $c['BODY'] = '3C0D10';
    $c['MENU'] = array('71181D','781A1F','781A1F','000000','FFFFFF');
    $c['BASE'] = 'FEAEB3';
    $c['HEADING'] = array('962027','3C0D10','FFFFFF');
    break;
    
  case 'blue':
    $c['HEADER'] = '205A8C';
    $c['BODY'] = '0D2438';
    $c['MENU'] = array('043959','032E47','205A8C','000000','FFFFFF');
    $c['BASE'] = 'DCEAF2';
    $c['HEADING'] = array('205A8C','043959','DCEAF2');
    break;
    
  case 'orange':
    $c['HEADER'] = 'F27F1B';
    $c['BODY'] = '913B09';
    $c['MENU'] = array('F2620F','C24E0C','C24E0C','000000','FFFFFF');
    $c['BASE'] = 'FACCA4';
    $c['HEADING'] = array('F27F1B','F2620F','FFFFFF');
    break;
    
  case 'yellow':
    $c['HEADER'] = 'F2B705';
    $c['BODY'] = '825502';
    $c['MENU'] = array('D98E04','AE7203','825502','000000','FFFFFF');
    $c['BASE'] = 'FAE29B';
    $c['HEADING'] = array('F2B705','F2E205','FFFFFF');
    break;
    
  case 'brown':
    $c['HEADER'] = '593527';
    $c['BODY'] = '261612';
    $c['MENU'] = array('40241E','331D18','331D18','000000','FFFFFF');
    $c['BASE'] = 'A68D85';
    $c['HEADING'] = array('593527','352017','FFFFFF');
    break;
    
  case 'gray':
    $c['HEADER'] = '3A3D3F';
    $c['MENU'] = array('333333','666666','362311','000000','FFFFFF');
    $c['BASE'] = 'D8D8D9';
    $c['HEADING'] = array('464A4D','81888C','FFFFFF');
    break;
  
  case 'cool':
    $c['BODY'] = '233153';
    $c['HEADER'] = '28385E';
    $c['FOOTER'] = '333333';
    $c['MENU'] = array('233153','28385E','233153','000000','FFFFFF');
    $c['BASE'] = 'A9AFBF';
    $c['BUTTON'] = array('516C8D','4B6382');
    $c['KIZI'] = 'D4D7DF';
    $c['MENU_NAME'] = '233153';
    $c['HEADING'] = array('28385E','FF9800','FFFFFF');
    $c['TABLE'] = array('516C8D','425772','EAEDF1','B9C4D1','FFFF8F');
    $c['TABLE_F'] = array('4B6382','667E9B','CBCFD8','ABB8C8');
    $b['KIZI'] = $backgroundPatterns['NONE'];
    $b['HEADING'] = $backgroundPatterns['ROUND3'];
    break;
    
  case 'cute':
    $c['BODY'] = '352628';
    $c['HEADER'] = 'F2295F';
    $c['FOOTER'] = '261E0F';
    $c['MENU'] = array('593F42','473235','FAEFE7','000000','FFFFFF');
    $c['BASE'] = 'F598A9';
    $c['BUTTON'] = array('F2295F','DF2657');
    $c['KIZI'] = 'F2D8C2';
    $c['MENU_NAME'] = '593F42';
    $c['HEADING'] = array('F33A6C','F2CF66','FAEFE7');
    $c['TABLE'] = array('F2AEBB','F48DA0','FCEFF1','FADFE4','F2D8C2');
    $c['TABLE_F'] = array('F27E93','F598A9','FCE5E9','FACBD4');
    $b['HEADER'] = $backgroundPatterns['NONE'];
    $b['MENU'] = $backgroundPatterns['NONE'];
    $b['BASE'] = $backgroundPatterns['STRIPE1'];
    break;
  
  case 'natural':
    $c['BODY'] = '302A20';
    $c['HEADER'] = '302A20';
    $c['FOOTER'] = '483E2F';
    $c['MENU'] = array('60533F','483E2F','EEEEEE','FFFFFF','483E2F');
    $c['BASE'] = '98A24F';
    $c['BUTTON'] = array('98A24F','8C9549');
    $c['TABLE'] = array('78684F','938672','EFEDEA','C9C3B9','FFFF8F');
    $c['TABLE_F'] = array('6E6049','938672','DFDBD5','C9C3B9');
    $c['KIZI'] = 'DDDABF';
    $c['HEADING'] = array('483E2F','616A1C','EEEEEE');
    $b['HEADER'] = $backgroundPatterns['STRIPE0'];
    $b['FOOTER'] = $backgroundPatterns['STRIPE0'];
    $b['HEADING'] = $backgroundPatterns['STRIPE0'];
    $b['MENU'] = $backgroundPatterns['STRIPE0'];
    $b['BASE'] = $backgroundPatterns['STRIPE1'];
    break;
    
  case 'gorgeous':
    $c['BODY'] = '400101';
    $c['HEADER'] = 'BF0413';
    $c['FOOTER'] = '400101';
    $c['MENU'] = array('8C030E','70020B','D99A25','FFDD00','000000');
    $c['BASE'] = '111111';
    $c['BUTTON'] = array('E1AE51','DCA236');
    $c['TABLE'] = array('D99A25','AE7B1E','F7EBD3','E8C27C','F2CF66');
    $c['TABLE_F'] = array('BF751B','995E16','F2E3D1','D9AC76');
    $c['KIZI'] = 'FAF5EB';
    $c['MENU_NAME'] = 'FAF6ED';
    $c['HEADING'] = array('BF0413','D99A25','F2E7AE');
    $b['BASE'] = $backgroundPatterns['CHECK1'];
    $b['KIZI'] = $backgroundPatterns['ROUND2'];
    $b['HEADER'] = $backgroundPatterns['ROUND3'];
    $b['FOOTER'] = $backgroundPatterns['ROUND2'];
    $b['HEADING'] = $backgroundPatterns['ROUND3'];
    break;
    
  case 'oase':
    $c['BODY'] = '444444';
    $c['HEADER'] = '00989B';
    $c['FOOTER'] = '444444';
    $c['MENU'] = array('222222','444444','444444','000000','FFFFFF');
    $c['TEXT'] = array('444444','FFFFFF');
    $c['TABLE'] = array('00B5B8','007A7C','F2F2F2','CCCCCC','FFFF8F');
    $c['TABLE_F'] = array('00989B','007A7C','E2E2E2','CCCCCC');
    $c['HEADING'] = array('FFFFFF','00B5B8','333333');
    $c['BASE'] = 'FFFFFF';
    $c['KIZI'] = 'F2F2F2';
    $c['MENU_NAME'] = '444444';
    $h['KIZI'] = $headingPatterns['OASE'];
    $b['MENU'] = $backgroundPatterns['NONE'];
    $b['BASE'] = $backgroundPatterns['OASE'];
    $b['KIZI'] = $backgroundPatterns['STRIPE2'];
    break;
    
  case 'epoch':
    $c['BODY'] = '313C3A';
    $c['HEADER'] = '1F8C78';
    $c['FOOTER'] = '1F8C78';
    $c['MENU'] = array('5A6361','46504E','46504E','000000','FFFFFF');
    $c['TEXT'] = array('444444','FFFFFF');
    $c['TABLE'] = array('1F8C78','4CA393','F4F9F8','CCCCCC','FFFF8F');
    $c['TABLE_F'] = array('1F8C78','007A7C','F4F9F8','CCCCCC');
    $c['HEADING'] = array('FFFFFF','1F8C78','333333');
    $c['BASE'] = 'FFFFFF';
    $c['KIZI'] = 'F2F2F2';
    $c['MENU_NAME'] = '444444';
    $b['BODY'] = $backgroundPatterns['NONE'];
    $b['HEADER'] = $backgroundPatterns['NONE'];
    $b['MENU'] = $backgroundPatterns['NONE'];
    $b['BASE'] = $backgroundPatterns['NONE'];
    break;
    
  case 'darkmode':
    $c['BODY'] = '1D2123';
    $c['HEADER'] = '272A2C';
    $c['FOOTER'] = '272A2C';
    $c['MENU'] = array('1A1A1A','242424','EEEEEE','EEEEEE','D0D1D1');
    $c['TEXT'] = array('D0D1D1','111');
    $c['TABLE'] = array('2E2E2E','000000','1A1A1A','14181A','400000');
    $c['TABLE_F'] = array('383838','000000','242424','14181A');
    $c['BUTTON'] = array('335581','47668E');
    $c['KIZI'] = '272A2C';
    $c['MENU_NAME'] = 'D0D1D1';
    $c['HEADING'] = array('3A3D3F','335581','EEEEEE');
    $c['BASE'] = '040505';
    $c['REVERSE'] = array('FFFFFF','000000');
    $c['DARKMODE'] = 'ON';
    $b['HEADER'] = $backgroundPatterns['NONE'];
    $b['MENU'] = $backgroundPatterns['NONE'];
    $b['KIZI'] = $backgroundPatterns['NONE'];
    break;
    
  default:
    $theme = 'none';
}



// $color「色（HEX,FFFFFF）」から$targetColor「色（HEX,FFFFFF）に
// 対する$density「密度（％）」を返す。
class color {
  function hex( $standardColor, $density = 100, $targetColor = 'FFFFFF' ) {

    // 密度が不正値の場合、基準カラーをそのまま返す
    if ( $density >= 0 || $density < 100 ) {
      
      // ％反転
      $inversionDensity = 100 - $density;
    
      // RGBに切り分け、16進数から10進数に変換
      $colors = array(
        hexdec( substr( $standardColor, 0, 2 ) ),
        hexdec( substr( $standardColor, 2, 2 ) ),
        hexdec( substr( $standardColor, 4, 2 ) ),
        hexdec( substr( $targetColor, 0, 2 ) ),
        hexdec( substr( $targetColor, 2, 2 ) ),
        hexdec( substr( $targetColor, 4, 2 ) )
      );
      
      // 密度を出す
      $colorDensity = array(
        round( abs( $colors[0] - $colors[3] ) / 100 * $inversionDensity ),
        round( abs( $colors[1] - $colors[4] ) / 100 * $inversionDensity ),
        round( abs( $colors[2] - $colors[5] ) / 100 * $inversionDensity )
      );
      
      // 大小チェック
      for( $i = 0; $i < count( $colorDensity ); $i++ ) {
        if ( $colors[ $i ] > $colors[ $i + 3 ] ) {
          $colorDensity[ $i ] = - $colorDensity[ $i ];
        }
      }

      // 密度を足し、10進数から16進数に変換結合
      $hex = '#' . sprintf('%02s', dechex( $colors[0] + $colorDensity[0] ) )
        . sprintf('%02s', dechex( $colors[1] + $colorDensity[1] ) )
        . sprintf('%02s', dechex( $colors[2] + $colorDensity[2] ) );

      return strtoupper( $hex );
    
    } else {
    
      return strtoupper( $standardColor );
    
    }
  }
}

if ( $theme !== 'none' ) {
  $color = new color();

echo <<< EOF
@charset "utf-8";
/* ********************************************************************* *
 
   IT Automation Theme
 
 * ********************************************************************* */
 
 /* ********************************************************************* *
   COMMON
 * ********************************************************************* */
body {
  background-color: {$color->hex($c['BODY'],100)};
  {$b['BODY']}
}
h1, h2, h3, h4, h5, h6 {
	color: {$color->hex($c['TEXT'][0],100)};
}
a:link {
  color: #005FD8;
}
a:visited {
  color: #005FD8;
}
a:hover {
  color: #D87900;
}
a:active {
  color: #D87900;
}

/* ********************************************************************* *
   HEADER
 * ********************************************************************* */
#HEADER {
  background-color: {$color->hex($c['HEADER'],100)};
  {$b['HEADER']}
}
#HEADER h4 { color: #F5F5F5; }

/* ********************************************************************* *
   FOOTER
 * ********************************************************************* */
#FOOTER {
  background-color: {$color->hex($c['FOOTER'],100)};
  {$b['FOOTER']}
}
#FOOTER a:link, #FOOTER a:visited {
  background-color: {$color->hex($c['BUTTON'][0],100)};
  color: #FFF;
}
#FOOTER a:hover, #FOOTER a:active {
  background-color: {$color->hex($c['BUTTON'][1],100)};
}

/* ********************************************************************* *
   MENU
 * ********************************************************************* */
#MENU h2 {
  background-color: {$color->hex($c['MENU'][0],100)};
  color: #EEE;
}
#MENU li a {
  background-color: {$color->hex($c['MENU'][1],100)};
  {$b['MENU']}
  color: #EEE;
  text-shadow: 1px 1px 0 #333;
}
#MENU li a:hover {
  background-color: {$color->hex($c['MENU'][1],90)};
  {$b['MENU']}
}
#MENU li.menu-on a,
#MENU li.menu-on a:hover {
  background-color: {$color->hex($c['BASE'],100)};
  background-image: none;
  color: {$color->hex($c['MENU'][2],100)};
  cursor: default;
}

/* ********************************************************************* *
   KIZI
 * ********************************************************************* */
#KIZI {
  background-color: {$color->hex($c['BASE'],100)};
  {$b['BASE']}
}
#KIZI h2 {
  background-color: transparent;
}
#KIZI #sysInitialFilter + h2,
#KIZI .midashi_class {
	background-color: {$color->hex($c['HEADING'][0],100)};
	border-color: {$color->hex($c['HEADING'][1],100)};
	color: {$color->hex($c['HEADING'][2],100)};
  {$b['HEADING']}
  {$h['KIZI']}
}
#KIZI .midashi_class:hover {
	background-color: {$color->hex($c['HEADING'][0],90)};
}
#KIZI input[type="button"].showbutton {
  background: none;
  border: none;
  box-shadow: none;
  color: {$color->hex($c['HEADING'][2],100)};
}
#KIZI .text {
	background-color: {$color->hex($c['KIZI'],100)};
	border-color: {$color->hex($c['KIZI'],80,'000000')};
  color: {$color->hex($c['TEXT'][0],100)};
  {$b['KIZI']}
}

#KIZI .menu_name{
  color: {$color->hex($c['MENU_NAME'])};
}

/* ********************************************************************* *
   BUTTON
 * ********************************************************************* */
input[type="button"],
input[type="submit"],
button.linkBtnInTbl {
  background-color: {$color->hex($c['BUTTON'][0],100)};
  border-color: {$color->hex($c['BUTTON'][0],100)};
  box-shadow: 0 2px 0 {$color->hex($c['BUTTON'][0],70,'000000')};
  color: #FFFFFF;
}
input[type="button"]:hover,
input[type="submit"]:hover,
button.linkBtnInTbl:hover {
  background-color: {$color->hex($c['BUTTON'][1],100)};
  border-color: {$color->hex($c['BUTTON'][1],100)};
  box-shadow: 0 1px 0 {$color->hex($c['BUTTON'][1],70,'000000')};
}

input.deleteBtnInTbl[type="button"] {
  background-color: #DD0000;
  border-color: #DD0000;
  box-shadow: 0 2px 0 #BB0000;
}
input.deleteBtnInTbl[type="button"]:hover {
  background-color: #CC0000;
  border-color: #CC0000;
  box-shadow: 0 1px 0 #AA0000;
}

input.deleteBtnInTbl[type="button"][value="復活"],
input.deleteBtnInTbl[type="button"][value="Restore"] {
  background-color: #0078DC;
  border-color: #0078DC;
  box-shadow: 0 2px 0 #0050B4;
}
input.deleteBtnInTbl[type="button"][value="復活"]:hover,
input.deleteBtnInTbl[type="button"][value="Restore"]:hover {
  background: #0064C8;
  border-color: #0064C8;
  box-shadow: 0 1px 0 #0030A1;
}

input[type="button"]:active,
input[type="submit"]:active,
button.linkBtnInTbl:active {
	box-shadow: none;
	transform: translateY( 0 );
}

/* disabled button */
input[type="button"][disabled],
input[type="button"][disabled]:hover,
input[type="button"][disabled]:active,
input[type="submit"][disabled],
input[type="submit"][disabled]:hover,
input[type="submit"][disabled]:active,
button.linkBtnInTbl[disabled],
button.linkBtnInTbl[disabled]:hover,
button.linkBtnInTbl[disabled]:active {
	background-color: {$color->hex($c['TABLE'][2],100)};
	border-color: {$color->hex($c['TABLE'][3],100)};
  color: {$color->hex($c['TEXT'][0],50,$c['TEXT'][1])};
}

/* ********************************************************************* *
   Table
 * ********************************************************************* */
.sDefault th, .sDefault td {
	border-color: {$color->hex($c['TABLE'][3],90)};
}
.sDefault th,
.sDefault-Fixed {
	background-color: {$color->hex($c['TABLE'][0],100)};
	border-color: {$color->hex($c['TABLE'][3],100)};
  color: #FFF;
}
.sDefault td {
  background-color: {$color->hex($c['TABLE'][2],20,$c['REVERSE'][1])};
}

.tableScroll {
background-color: {$color->hex($c['TABLE'][2],100)};
}
.itaTable.tableSettingOpen {
  background-color: {$color->hex($c['TABLE'][2],100)};
}
.itaTableBody table {
  background-color: {$color->hex($c['TABLE'][3],100)};
}

.itaTableBody th {
	background-color: {$color->hex($c['TABLE'][0],100)};
  color: #FFF;
}
th.sortTriggerInTbl:hover {
	background-color: {$color->hex($c['TABLE'][0],90,$c['REVERSE'][1])};
}

.itaTableBody td {
  background-color: {$color->hex($c['TABLE'][2],100)};
  color: {$color->hex($c['TEXT'][0],100)};
}
.itaTableBody tr:nth-child(odd) td {
  background-color: {$color->hex($c['TABLE'][2],20,$c['REVERSE'][1])};
}
.itaTableBody td.likeHeader {
  background-color: {$color->hex($c['TABLE_F'][2],100)};
}
.itaTableBody tr:nth-child(odd) td.likeHeader {
  background-color: {$color->hex($c['TABLE_F'][2],50,$c['REVERSE'][1])};
}
.itaTableBody tr.disuse td,
.itaTableBody tr.disuse td.likeHeader,
.itaTableBody tr.disuse:nth-child(odd) td {
  background-color: {$color->hex($c['TEXT'][0],10,$c['REVERSE'][1])};
  background-image: repeating-linear-gradient( -45deg, transparent, transparent 2px, {$color->hex($c['TEXT'][0],15,$c['REVERSE'][1])} 2px, {$color->hex($c['TEXT'][0],15,$c['REVERSE'][1])} 4px);
  color: {$color->hex($c['TEXT'][0],40,$c['REVERSE'][1])};
}
.fakeContainer_Filter1Print .itaTableBody tr:hover td,
.fakeContainer_Filter2Print .itaTableBody tr:hover td {
  background-color: {$color->hex($c['TABLE'][4],80,$c['REVERSE'][1])};
  background-image: none;
}
.fakeContainer_Filter1Print .itaTableBody tr:hover td.likeHeader,
.fakeContainer_Filter2Print .itaTableBody tr:hover td.likeHeader {
  background-color: {$color->hex($c['TABLE'][4],100)};
  background-image: none;
}

/* .tableSticky */
.itaTable.tableSticky tr.defaultExplainRow th {
  box-shadow: 0 0 0 1px {$color->hex($c['TABLE'][1],100)};
}
.itaTable.tableSticky tr.defaultExplainRow th.thSticky {
	background-color: {$color->hex($c['TABLE_F'][0],100)};
  box-shadow: 0 0 0 1px {$color->hex($c['TABLE_F'][1],100)};
}
.itaTable.tableSticky tr.defaultExplainRow th.thSticky.sortTriggerInTbl:hover {
	background-color: {$color->hex($c['TABLE_F'][0],90,$c['REVERSE'][1])};
}
.itaTable.tableSticky td.likeHeader {
  box-shadow: 0 0 0 1px {$color->hex($c['TABLE_F'][3],100)};
}
.itaTable.tableSticky .fixedBorder {
  border-color: {$color->hex($c['TABLE_F'][0],100)};
}

/* table 外枠 */
.itaTableBody::before,
.itaTableBody::after,
.itaTable .tableScroll::before,
.itaTable .tableScroll::after {
  border-color: #111;
}

/* ********************************************************************* *
   Symphony Editor
 * ********************************************************************* */
#symphonyInfoShowContainer .leftMainArea-TypeA,
#symphonyInfoShowContainer .rightSideBar-TypeA {
  background-color: {$color->hex($c['TABLE'][2],100)};
  border-color: {$color->hex($c['TABLE'][2],20,$c['REVERSE'][1])}!important;
  color: {$color->hex($c['TEXT'][0],100)};
}
#symphony_header, #symphony_footer, #operation_info_area,
#symphonyInfoShowContainer .leftMainArea-TypeA,
#symphonyInfoShowContainer .rightSideBar-TypeA,
#operation_info_area .heightAndWidthFixed01,
#pattern_filter_area, #symphony_area, #material_area_wrapper {
  border-color: {$color->hex($c['REVERSE'][0],30,$c['REVERSE'][1])}!important;
}
#symphony_header,
#symphonyInfoShowContainer .rightSideBar-TypeA {
	border-top-color: {$color->hex($c['HEADER'],80)}!important;
}
.movement:nth-child(2n+1) {
  background-color: {$color->hex($c['TABLE'][2],100)}!important;
}
.movement .operation_box .inLineTitle {
  color: {$color->hex($c['TEXT'][0],100)}!important;
}
#material_area, #pattern_filter_area {
  background-color: {$color->hex($c['REVERSE'][1])};
}
#operation_info_area .heightAndWidthFixed01 label {
  color: {$color->hex($c['HEADER'],100)}!important;
}
/* ********************************************************************* *
   Other.
 * ********************************************************************* */
.menu_border {
  box-shadow: 0 0 0 4px {$color->hex($c['TEXT'][1],100)};
}
.mm_text {
  color: {$color->hex($c['MENU'][3],100)};
}
.mm_list:hover .mm_text {
  background-color: {$color->hex($c['MENU'][4],100)};
}
#gateLogin, #gateLogout, #gateChangePw {
  background-color: {$color->hex($c['KIZI'],100)};
  border-color: {$color->hex($c['KIZI'],80,'000000')};
  color: {$color->hex($c['TEXT'][0],100)};
}
#gateLogin h2, #gateChangePw h2, #gateLogout h2 {
	background-color: {$color->hex($c['HEADING'][0],100)};
	color: {$color->hex($c['HEADING'][2],100)};
}
#create_menu_form h3,
#export_form .export_all_div {
	background-color: {$color->hex($c['TABLE'][0],100)};
	border-color: {$color->hex($c['TABLE'][1],100)};
  color: #FFF;
}
#create_menu_form h3 + div,
#export_form .export_all_div + div {
  background-color: {$color->hex($c['TABLE'][2],20,$c['REVERSE'][1])};
	border-color: {$color->hex($c['TABLE'][1],100)};
}
#create_menu_form label:hover,
#export_form label:hover {
  background-color: {$color->hex($c['TABLE'][4],100)};
}
#import_form .import_all_div {
	background-color: {$color->hex($c['TABLE'][0],100)};
	border-color: {$color->hex($c['TABLE'][1],100)};
  color: #FFF;
}
#import_form .import_all_div + div {
  background-color: {$color->hex($c['TABLE'][2],20,$c['REVERSE'][1])};
	border-color: {$color->hex($c['TABLE'][1],100)};
}
#import_form label:hover {
  background-color: {$color->hex($c['TABLE'][4],100)};
}

#KIZI .widget {
background-color: {$color->hex($c['KIZI'],20)};
}
#KIZI .widget-header {
background-color: {$color->hex($c['KIZI'],40)};
}
#KIZI .dashboard-table thead th {
background-color: {$color->hex($c['HEADER'],100)};
}
EOF;


if ( $theme == 'gorgeous' ) {

echo <<< EOF
/* ********************************************************************* *
   Gorgeous
 * ********************************************************************* */
#KIZI .widget-menu-item {
background-color: #F2F2F2;
}

#KIZI .number-table-wrap,
#KIZI .stacked-graph,
#KIZI .widget-sub-name {
background-color: #FFF;
}

EOF;
}

if ( $theme == 'epoch' ) {

echo <<< EOF
/* ********************************************************************* *
   EPOCH
 * ********************************************************************* */
#HEADER,
#FOOTER {
background-image: linear-gradient( -45deg, rgba(255,255,255,.1) 50%, rgba(255,255,255,0) 50%);
}
#KIZI {
padding-top: 0!important;
padding-right: 0!important;
padding-left: 0!important;
}
html.ita-fixed-layout #KIZI {
padding-top: 80px!important;
}
#MENU h2 {
margin-bottom: 4px;
}
#MENU li {
margin-bottom: 4px;
}
#MENU li.menu-on a,
#MENU li.menu-on a:hover {
padding-right: 10px;
background-color: #6F7775;
color: #FFF;
}
#KIZI .text {
margin: 0;
background-color: #FFF;
border: none;
border-radius: 0;
box-shadow: none;
border-bottom: 1px solid #79BAAE;
}
#KIZI h2 {
margin: 0;
border-bottom: 1px solid #79BAAE;
}
#KIZI #sysInitialFilter + h2,
#KIZI .midashi_class {
background-color: #D2E8E4;
border: none;
color: #1F8C78;
}
#KIZI #sysInitialFilter + h2:hover,
#KIZI .midashi_class:hover {
background-color: #D2E8E4;
}
.itaTable.tableSticky .fixedBorder {
border-color: #1F8C78;
}
#KIZI h2.menu_name {
padding: 24px 24px 12px;
}
#KIZI h2.menu_name + form > div {
margin: 0!important;
}
#KIZI h2.menu_name + form .text {
margin-bottom: 32px;
padding: 16px 24px;
}
#KIZI h2.menu_name + form input[type="submit"] {
margin-left: 24px;
}
EOF;
}

if ( $c['DARKMODE'] == 'ON' ) {

echo <<< EOF
/* ********************************************************************* *
   DARKMODE
 * ********************************************************************* */

/* Scroll Bar */
body, .tableScroll { scrollbar-color: #000F23 #666666; } /* for Firefox */ 
::-webkit-scrollbar { width: auto; height: auto; }
::-webkit-scrollbar-button { background: #444444; }
::-webkit-scrollbar-corner { background: #333333; }
::-webkit-scrollbar-thumb { background: #666666; }
::-webkit-scrollbar-track-piece { background: #000F23; }

/* Input */
input[name="symphony_name"],
#filter_value,
input[type="text"],
input[type="password"],
textarea,
select,
#KIZI textarea,
#KIZI select,
#KIZI option,
#KIZI input[type="text"],
#KIZI input[type="password"],
#KIZI .richFilterSelectListCaller,
.select2-container--default .select2-search--dropdown .select2-search__field,
.select2-container--default .select2-selection--single,
.select2-dropdown,
#PAGETOP #KIZI .menu-column-config-table textarea,
#PAGETOP #KIZI .menu-column-config-table select,
#PAGETOP #KIZI .menu-column-config-table input[type="text"],
#PAGETOP #KIZI .menu-column-config-table input[type="number"],
#PAGETOP #KIZI .config-id {
  background-color: #000000!important;
  border: 1px solid #666666!important;
  color: #EEE!important;
}
input[name="symphony_name"]:focus,
#filter_value:focus,
input[type="text"]:focus,
input[type="password"]:focus,
textarea:focus,
select:focus,
#KIZI textarea:focus,
#KIZI select:focus,
#KIZI option:focus,
#KIZI input[type="text"]:focus,
#KIZI input[type="password"]:focus,
#KIZI .richFilterSelectListCaller:focus,
.select2-container--default .select2-search--dropdown .select2-search__field:focus,
.select2-container--default .select2-selection--single:focus,
.select2-dropdown:focus,
#PAGETOP #KIZI .menu-column-config-table textarea:focus,
#PAGETOP #KIZI .menu-column-config-table select:focus,
#PAGETOP #KIZI .menu-column-config-table input[type="text"]:focus,
#PAGETOP #KIZI .menu-column-config-table input[type="number"]:focus,
#PAGETOP #KIZI .config-id:focus {
background-color: #272B38!important;
border-color: #4F80FF!important;
}
.select2-container--default .select2-selection--multiple {
background-color: #000000!important;
border: 1px solid #666666!important;
color: #EEE!important;
}
#filter_area .select2-container .select2-search--inline .select2-search__field::placeholder,
#select_area .select2-container .select2-search--inline .select2-search__field::placeholder {
color: #AAA;
}
#filter_area .select2-container--default.select2-container--focus .select2-selection--multiple,
#select_area .select2-container--default.select2-container--focus .select2-selection--multiple {
background-color: #272B38!important;
border-color: #4F80FF!important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
background-color: rgba( 255,255,255,.1 )!important;
border-color: rgba( 255,255,255,.2 )!important;
}
.select2-search__field {
color: #EEE!important;
}
.password_eye {
background-position: 0 -64px;
}
.password_eye.password_see {
background-position: -16px -64px;
}
/* #symphony_footer disabled button */
#symphony_footer input[type="button"][disabled],
#symphony_footer input[type="button"][disabled]:hover,
#symphony_footer input[type="button"][disabled]:active,
#symphony_footer input[type="submit"][disabled],
#symphony_footer input[type="submit"][disabled]:hover,
#symphony_footer input[type="submit"][disabled]:active {
	background-color: rgba( 0,0,0,.5 );
	border-color:  rgba( 0,0,0,.8 );
}

/* Movement 作業実行 */
#KIZI #orchestratorInfoHeader,
#KIZI #orchestratorInfoFooter {
background-color: #272A2C;
color: #D0D1D1;
}
#KIZI #operation_info_area {
background-color: rgba( 0,0,0,.2 );
}
#KIZI #operation_info_area .heightAndWidthFixed01 label {
color: #D0D1D1!important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
background-color: #444;
}

#KIZI .richFilterSelectListCaller:hover {
    background-color: #222222;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
  color: #EEE;
}
input[type="text"]:focus, input[type="password"]:focus, textarea:focus, select:focus,
#KIZI input[type="text"]:focus, #KIZI input[type="password"]:focus, #KIZI textarea:focus, #KIZI select:focus {
  background-color: #111111;
  border-color: #0070FF;
  color: #EEE;
}
#symphony_area {
  background: #000000!important;
}
.movement2,
.draggable_area .movement.ui-draggable-dragging,
.highlight {
  background-color: #333333!important;
  border-color: #666666!important;
}
.start, .end,
.start::after, .end::after,
.arrow::after, .highlight::after {
  background-color: #666666!important;
}
.movement2 .tips_box .areaRight .inLineDiv {
background-color: #444!important;
border-color: #777!important;
}

/* -------------------------------------------------- *

   DASHBOARD

 * -------------------------------------------------- */
#KIZI #dashboard .dashboard-loading::after {
background-color: #444;
background-image: linear-gradient( -45deg, transparent 25%, rgba( 0,0,0,.1 ) 25%, rgba( 0,0,0,.1 ) 50%, transparent 50%, transparent 75%, rgba( 0,0,0,.1 ) 75% );
box-shadow: 0 0 4px rgba( 0,0,0,.5 ) inset,
            0 0 8px rgba( 0,0,0,.2 ),
            0 0 0 2px #555;
}
#KIZI #dashboard .dashboard-header {
background-color: #222;
}
#KIZI #dashboard .dashboard-menu-list {
border-left: 1px solid #555;
border-right: 1px solid #111;
}
#KIZI #dashboard .dashboard-menu-list:first-child {
border-left: none;
}
#KIZI #dashboard .dashboard-menu-list:last-child {
borrder-right: none;
}
#KIZI #dashboard .widget {
background-color: #222;
border: 1px solid #444;
box-shadow: 0 0 4px rgba( 0,0,0,.2 );
}
#KIZI #dashboard  .widget-header {
background: linear-gradient( rgba(0,0,0,0),rgba(0,0,0,.5));
border-bottom: 1px solid #444;
}
#KIZI #dashboard[data-mode="view"] .widget-move-knob::before {
background-color: #111;
border-top: 1px solid rgba( 0,0,0,.5 );
border-left: 1px solid rgba( 0,0,0,.5 );
border-right: 1px solid rgba( 255,255,255,.3 );
border-bottom: 1px solid rgba( 255,255,255,.3 );
}
#KIZI #dashboard[data-mode="edit"] .widget-move-knob::before {
background-image: linear-gradient( 90deg, rgba( 255,255,255,.2 ) 50%, transparent 50% );
}
#KIZI #dashboard .widget-edit-button:hover {
background-color: #333;
}
#KIZI #dashboard .widget-edit-button:active {
background-color: #444;
}
#KIZI #dashboard .widget-blank {
height: 100%;
background-color: rgba( 255,255,255,.1 );
border-radius: 4px;
animation: widgetBlank .3s;
}
@keyframes widgetBlank {
from { transform: scale(.7); opacity: 0; }
to   { transform: scale(1); opacity: 1; }
}
#KIZI #dashboard .widget-blank-grid.movable-blank .widget-blank {
background-color: #222;
}
#KIZI #dashboard .widget-blank-grid.movable-blank .widget-blank::after {
background-color: rgba( 96,198,13,.1 );
border: 2px solid rgba( 96,198,13,.1 );
}
#KIZI #dashboard .widget-blank-grid.movable-blank .widget-blank:hover::after {
background-color: rgba( 255,100,10,.1 );
border-color: rgba( 255,100,10,.1 );
}
#KIZI #dashboard[data-action="none"] .remove-blank .widget-blank {
background-color: #222;
}
#KIZI #dashboard[data-action="none"] .remove-blank .widget-blank::after {
background-color: rgba( 255,0,0,.2 );
border: 2px solid rgba( 255,0,0,.2 );
}

#KIZI #dashboard .add-blank {
border-left: 2px solid #4F80FF;
border-right: 2px solid #4F80FF;
}
#KIZI #dashboard .add-blank::before {
background-color: #4F80FF;
border: 2px solid #4F80FF;
color: #FFF;
}
#KIZI #dashboard .add-blank:active::before {
background-color: #7299FF;
}
#KIZI #dashboard .add-blank::after {
content: '';
display: block;
width: 100%; height: 2px;
background-color: #4F80FF;
}
#KIZI #dashboard .widget-loading::before {
border: 8px solid #EEE;
}
#KIZI #dashboard .widget-loading::after {
border-bottom-color: #CCC;
}
#KIZI #dashboard .widget-menu-list,
#KIZI #dashboard .shortcut-list {
background-color: rgba( 255,255,255,.05 );
border: 1px solid transparent;
}
#KIZI #dashboard[data-action="menu-move"] .widget-menu-list,
#KIZI #dashboard[data-action="link-move"] .shortcut-list {
background-color: rgba( 96,198,13,.1 );
border-color: rgba( 96,198,13,.1 );
}
#KIZI #dashboard[data-action="menu-move"] .widget-grid[data-widget-id="1"]:hover .widget-menu-list,
#KIZI #dashboard[data-action="menu-move"] .widget-grid[data-widget-id="2"]:hover .widget-menu-list,
#KIZI #dashboard[data-action="link-move"] .widget-grid[data-widget-id="3"]:hover .shortcut-list {
background-color: rgba( 255,100,10,.1 );
border-color: rgba( 255,100,10,.1 );
}
#KIZI #dashboard .widget-menu-item.left::before,
#KIZI #dashboard .widget-menu-item.right::before,
#KIZI #dashboard .shortcut-item.left::before,
#KIZI #dashboard .shortcut-item.right::before{
background-color: #4F80FF;
}
#KIZI #dashboard .widget-menu-item.left::after,
#KIZI #dashboard .widget-menu-item.right::after,
#KIZI #dashboard .shortcut-item.left::after,
#KIZI #dashboard .shortcut-item.right::after{
border-top: 2px solid #4F80FF;
border-bottom: 2px solid #4F80FF;
}
#KIZI #dashboard .widget-menu-item.move,
#KIZI #dashboard .shortcut-item.move {
box-shadow: 4px 4px 16px rgba( 0,0,0,.6 );
}
#KIZI #dashboard .widget-menu-link:visited,
#KIZI #dashboard .widget-menu-link:link {
color: #EEE;
}
#KIZI #dashboard .widget-menu-item.link-hover .widget-menu-link .widget-menu-name {
background-image: linear-gradient( #222, #333 );
border: 1px solid #444;
box-shadow: 0 0 0 1px #666, 0 2px 4px 0 rgba( 0, 0, 0, 0.3 );
color: #EEE;
}
#KIZI #dashboard .shortcut-link:link,
#KIZI #dashboard .shortcut-link:visited {
background-color: #222;
border: 1px solid #444;
color: #EEE;
}
#KIZI #dashboard .shortcut-link:hover,
#KIZI #dashboard .shortcut-link:active {
background-color: #333;
}
#KIZI #dashboard .shortcut-link::before {
background-color: rgba( 255,255,255,.2 );
}
#KIZI #dashboard .shortcut-link:hover::before,
#KIZI #dashboard .shortcut-link:active::before {
background-color: rgba( 255,255,255,.4 );
}
#KIZI .circle-zero {
stroke: rgba( 255,255,255,.1 );
} 
#KIZI #dashboard .pie-chart-total-name,
#KIZI #dashboard .pie-chart-total-text {
fill: #BBB;
}
#KIZI #dashboard .pie-chart-total-number {
fill: #999;
}
#KIZI #dashboard .number-table th,
#KIZI #dashboard .number-table td,
#KIZI #dashboard .dashboard-table th,
#KIZI #dashboard .dashboard-table td {
border-top: 1px solid #444;
border-bottom: 1px solid #444;
}
#KIZI #dashboard .number-table thead th,
#KIZI #dashboard .dashboard-table thead th {
background-color: rgba( 255,255,255,.05 );
}
#KIZI .dashboard-table tr:nth-of-type(odd) {
background-color: transparent;
}
#KIZI #dashboard .number-table .emphasis th,
#KIZI #dashboard .number-table .emphasis td {
background-color: rgba( 255,255,170,.2 );
}
#KIZI #dashboard .zero {
color: #666!important;
}
#KIZI #dashboard .stacked-graph-vertical-axis {
border-right: 1px solid #666;
}
#KIZI #dashboard .stacked-graph-vertical-axis-item::before {
background-color: #555;
}
#KIZI #dashboard .stacked-graph-item-title {
color: #EEE;
}
#KIZI #dashboard .stacked-graph-bar {
border-right: 1px dashed #555;
border-left: 1px solid transparent;
}
#KIZI #dashboard .stacked-graph-item-inner:hover {
background-color: rgba( 255,255,170,.2 );
}
#KIZI #dashboard .stacked-graph-popup {
background-color: #111;
border: 1px solid #666;
box-shadow: 2px 2px 8px rgba( 0,0,0,.3 );
}
#KIZI .widget-name-inner,
#KIZI .number-table,
#KIZI .stacked-graph-vertical-axis-item,
#KIZI .stacked-graph-popup {
color: #EEE;
}
#KIZI .widget-edit::after {
background-position: -60px -80px;
}
#KIZI .widget-display::after {
  background-position: -20px -80px;
}
#KIZI .widget-grid[data-widget-display="0"] .widget-display::after {
background-position: 0 -80px;
}
#KIZI .widget-delete::after {
background-position: -40px -80px;
}
#KIZI .number-table th,
#KIZI .number-table td {
background-color: transparent;
}
#KIZI .widget-sub-name {
border-color: #555;
color: #EEE;
}
#KIZI .dashboard-text {
background-color: transparent;
border-color: #555;
color: #AAA;
}
#KIZI .dashboard-table-cell-wrap,
#KIZI .dashboard-table-cell-nowrap{
color: #EEE;
}
#KIZI .rID {
background-color: #111;
}
#KIZI .rd {
background-color: #111;
border-color: #444;
color: #EEE
}
#KIZI .pie-chart.start {
background-color: transparent!important;
}
/* -------------------------------------------------- *

   メニューエディタ

 * -------------------------------------------------- */
#KIZI #menu-editor-edit .menu-editor-block-inner {
box-shadow: 0 0 4px #000 inset !important;
background: linear-gradient( transparent, transparent 99px, #111 99px, #111 100px ),
linear-gradient( 90deg, transparent, transparent 99px, #111 99px, #111 100px ),
#000 !important;
background-size: 100px 100px, 100px 100px !important;
}
#KIZI #menu-editor-preview {
background-color: #000;
}
#PAGETOP #KIZI input[type="text"].menu-column-title-input:active,
#PAGETOP #KIZI input[type="text"].menu-column-title-input:focus {
background-color: #18264D!important;
}
#KIZI .menu-table {
background-image: none;
background-color: #222;
}
#KIZI .menu-column-group-header {
background-color: #333;
border-color: #222;
}
#KIZI .menu-column-header,
#KIZI .menu-column-config {
background-color: #555;
}
#KIZI .menu-column-type {
background-color: #333;
}
#KIZI .menu-column-config-table th,
#KIZI .menu-column-config-table td {
background-color: #333;
border-color: #666;
color: #EEE;
}
#KIZI .menu-column-config-table th {
background-color: #444;
}
#KIZI .column-empty {
background-color: #666!important;
}
#KIZI .column-empty p,
#KIZI #menu-editor-info .empty {
background-color: #333!important;
background-image: linear-gradient( -45deg, rgba( 255,255,255,.1 ) 25%, transparent 25%, transparent 50%, rgba( 255,255,255,.1 ) 50%, rgba( 255,255,255,.1 ) 75%, transparent 75%, transparent)!important;
background-size: 8px 8px!important;
border: 1px solid #555!important;
color: #CCC!important;
}
#KIZI .column-resize::after {
background-color: #888;
}
#KIZI .required-label.hover,
#KIZI .unique-label.hover {
background-color: rgba( 255,255,255,.05 );
border-color: rgba( 255,255,255,.1 );
}


/* -------------------------------------------------- *

   メニューエディタ

 * -------------------------------------------------- */
#KIZI #art-board {
            position: absolute;
            left: 0;
            top: 0;
            z-index: 2;
            background: linear-gradient( transparent, transparent 99px, #111 99px, #111 100px ),
            linear-gradient( 90deg, transparent, transparent 99px, #111 99px, #111 100px ),
            linear-gradient( transparent, transparent 49px, #292929 49px, #292929 50px ),
            linear-gradient( 90deg, transparent, transparent 49px, #292929 49px, #292929 50px ),
            linear-gradient( transparent, transparent 9px, #292929 9px, #292929 10px ),
            linear-gradient( 90deg, transparent, transparent 9px, #292929 9px, #292929 10px ),
            #333;
            background-size: 100px 100px, 100px 100px, 50px 50px, 50px 50px, 10px 10px, 10px 10px;
            }
#KIZI #canvas[data-scale="50"] #art-board {
            background: linear-gradient( transparent, transparent 98px, #111 98px, #111 100px ),
            linear-gradient( 90deg, transparent, transparent 98px, #111 98px, #111 100px ),
            linear-gradient( transparent, transparent 48px, #292929 48px, #292929 50px ),
            linear-gradient( 90deg, transparent, transparent 48px, #292929 48px, #292929 50px ),
            #333;
            background-size: 100px 100px, 100px 100px, 50px 50px, 50px 50px;
            }
#KIZI #canvas[data-scale="25"] #art-board {
background: linear-gradient( transparent, transparent 96px, #292929 96px, #292929 100px ),
            linear-gradient( 90deg, transparent, transparent 96px, #292929 96px, #292929 100px ),
            #333;
background-size: 100px 100px, 100px 100px;
}
#KIZI  #canvas[data-scale="10"] #art-board {
background: #333;
}
#KIZI .svg-line-back {
stroke: #000;
}
#KIZI .branch-line .branch-back-line {
stroke: #000;
}

EOF;

}

}

}

itaThemeSelect();

?>
