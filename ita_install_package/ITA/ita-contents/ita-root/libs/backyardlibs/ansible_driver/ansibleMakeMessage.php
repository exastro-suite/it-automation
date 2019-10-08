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
/////////////////////////////////////////////////////////////////////////////////////////
// 処理モード
const LC_RUN_MODE_STD                    = "0";   // 標準
const LC_RUN_MODE_VARFILE                = "1";   // 変数定義ファイルの構造チェック

////////////////////////////////////////////////////////////////////////////////
//
// 処理内容
//   処理モードが標準と変数定義ファイルの構造チェックの両方で表示されるメッセージ
//   を生成する。
//
// パラメータ
//   $objMTS:   メッセージオブジェクト
//   $chkmode:  処理モード 標準: 0:LC_RUN_MODE_STD  
//                         変数定義ファイルの構造チェック: 1:LC_RUN_MODE_VARFILE
//   $msgcode:  メッセージコード
//   $paramAry: メッセージパラメータ
//
// 戻り値
//   なし
//
////////////////////////////////////////////////////////////////////////////////
function AnsibleMakeMessage($objMTS,$chkmode,$msgcode,$paramAry=array()) {
    $msgtblary['ITAANSIBLEH-ERR-70044']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70075']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70076']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70077']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70078']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70079']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70080']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70081']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70086']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70087']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70089']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-70090']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-90218']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-90220']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');
    $msgtblary['ITAANSIBLEH-ERR-6000016']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000027' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000028');
    $msgtblary['ITAANSIBLEH-ERR-6000029']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000025' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000026');

    $msgtblary['ITAANSIBLEH-ERR-90090']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000034' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000035');
    $msgtblary['ITAANSIBLEH-ERR-90091']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000034' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000035');
    $msgtblary['ITAANSIBLEH-ERR-6000005']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000036' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000037');
    $msgtblary['ITAANSIBLEH-ERR-6000007']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000036' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000037');
    $msgtblary['ITAANSIBLEH-ERR-6000019']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000038' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000039');

    //ITAANSIBLEH-ERR-6000025: (ロールパッケージ名:{} role:{} file:{} line:{})   ITAANSIBLEH-ERR-6000025: (line:{})
    //ITAANSIBLEH-ERR-6000027: (ロールパッケージ名:{} role:{} file:{} 変数名:{}) ITAANSIBLEH-ERR-6000028: (変数名:{})";
    $msgtblary['ITAANSIBLEH-ERR-70044']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70075']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70076']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70077']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70078']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70079']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70080']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70081']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70086']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70087']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70089']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-70090']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-90218']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-90220']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-6000016']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-6000029']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'3');
    $msgtblary['ITAANSIBLEH-ERR-90090']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-90091']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-6000005']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-6000007']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-6000019']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');

    if( ! isset($msgtblary[$msgcode])) {
        $result_msg = $objMTS->getSomeMessage($msgcode,$paramAry);
    } else {
        foreach($msgtblary[$msgcode]['msgcode'] as $mode=>$code) {
            if($chkmode == $mode) {
                $pram_code = $code;
                break;
            }
        }
        foreach($msgtblary[$msgcode]['paramlist'] as $mode=>$plist) {
            if($chkmode == $mode) {
                $param_list = $plist;
                break;
            }
        }
        if($pram_code != "") {
            switch($param_list) {
            case 'all':
                $parm_msg = $objMTS->getSomeMessage($pram_code,$paramAry);
                $result_msg = $objMTS->getSomeMessage($msgcode,array($parm_msg));
                break;
            default:
                $param_msg = array();
                foreach(explode(',',$param_list) as $param_no) {
                    $param_msg[] = $paramAry[(int)($param_no)];
                }
                $parm_msg = $objMTS->getSomeMessage($pram_code,$param_msg);
                $result_msg = $objMTS->getSomeMessage($msgcode,array($parm_msg));
                break;
            }
        }
    }
    return $result_msg;
}
