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
    //$ary[6000027] = "(ロールパッケージ名:{} ロール:{} file:{} 変数名:{})";
    //$ary[6000028] = "(テンプレート変数名:{} 変数名:{})";

    //$ary[70086] = "変数定義の解析に失敗しました。{}"
    $msgtblary['ITAANSIBLEH-ERR-70086']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000027' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000028');
    //$ary[70087] = "変数定義が想定外なので解析に失敗しました。{}";
    $msgtblary['ITAANSIBLEH-ERR-70087']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-70101' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-70102');
    //$ary[70089] = "繰返階層の変数定義が一致していません。{}";
    $msgtblary['ITAANSIBLEH-ERR-70089']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000027' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000028');
    //$ary[70090] = "代入順序が複数必要な変数定義になっています。{}";
    $msgtblary['ITAANSIBLEH-ERR-70090']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000027' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000028');
    //$ary[90218] = "繰返構造の繰返数が99999999を超えてた定義です。{}";
    $msgtblary['ITAANSIBLEH-ERR-90218']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000027' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000028');
    // $ary[6000027] = "(ロールパッケージ名:{} ロール:{} file:{} 変数名:{})";
    // $ary[6000028] = "(テンプレート変数名:{} 変数名:{})";
    // $ary[6000016] = "変数が二重定義されています。{}";
    $msgtblary['ITAANSIBLEH-ERR-6000016']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000027' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000028');
    // $ary[90090] = "ファイル埋込変数がファイル管理に登録されていません。{}";
    // $ary[6000034] = "(ロール:{} Playbook:{} line:{} ファイル埋込変数:{})";
    // $ary[6000035] = "(line:{} ファイル埋込変数:{})";
    $msgtblary['ITAANSIBLEH-ERR-90090']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000034' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000035');
    // $ary[90091] = "ファイル埋込変数に紐づくファイルがファイル管理に登録されていません。 {}";
    // $ary[6000034] = "(ロール:{} Playbook:{} line:{} ファイル埋込変数:{})";
    // $ary[6000035] = "(line:{} ファイル埋込変数:{})";
    $msgtblary['ITAANSIBLEH-ERR-90091']['msgcode']   = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000034' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000035');
    // $ary[6000036] = "(ロール:{} PlayBook:{} line:{} テンプレート埋込変数:{})";
    // $ary[6000037] = "(line:{} テンプレート埋込変数:{})";
    // $ary[6000005] = "テンプレート埋込変数に紐づくファイルがテンプレート管理に登録されていません。{})";
    $msgtblary['ITAANSIBLEH-ERR-6000005']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000036' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000037');

    // $ary[6000007] = "テンプレート埋込変数がテンプレート管理に登録されていません。{}";
    // $ary[6000037] = "(line:{} テンプレート埋込変数:{})";
    // $ary[6000005] = "テンプレート埋込変数に紐づくファイルがテンプレート管理に登録されていません。{})";
    $msgtblary['ITAANSIBLEH-ERR-6000007']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000036' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000037');

    // $ary[6000038] = "(ロール:{} PlayBook:{} line:{} グローバル変数:{})";
    // $ary[6000039] = "(line:{} グローバル変数:{})";
    // $ary[6000019] = "グローバル変数がグローバル管理に登録されていません。 {})";
    $msgtblary['ITAANSIBLEH-ERR-6000019']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-6000038' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-6000039');
    // $ary[70095] = "変数名が不正です。{}";
    // $ary[70096] = "(ロールパッケージ名:{} ロール:{} file:{} 変数名:{})";
    // $ary[70097] = "(テンプレート変数名:{} 変数名:{})";
    $msgtblary['ITAANSIBLEH-ERR-70095']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-70096' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-70097');

    // $ary[70098] = "メンバー変数名が不正です。メンバー変数名に 「 ．(ドット)  [  ] 」の3記号は使用できません。{}";
    // $ary[70099] = "(ロールパッケージ名:{} ロール:{} file:{} 変数名:{} メンバー変数名:{})";
    // $ary[70100] = "(テンプレート変数名:{} 変数名:{} メンバー変数名:{})";
    $msgtblary['ITAANSIBLEH-ERR-70098']['msgcode'] = array(LC_RUN_MODE_STD=>'ITAANSIBLEH-ERR-70099' ,
                                                             LC_RUN_MODE_VARFILE=>'ITAANSIBLEH-ERR-70100');

    $msgtblary['ITAANSIBLEH-ERR-70086']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3');
    $msgtblary['ITAANSIBLEH-ERR-70087']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0');
    $msgtblary['ITAANSIBLEH-ERR-70089']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3');
    $msgtblary['ITAANSIBLEH-ERR-70090']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3');
    $msgtblary['ITAANSIBLEH-ERR-90218']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3');
    $msgtblary['ITAANSIBLEH-ERR-6000016']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3');
    $msgtblary['ITAANSIBLEH-ERR-90090']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-90091']['paramlist']    = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-6000005']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-6000007']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-6000019']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'2,3');
    $msgtblary['ITAANSIBLEH-ERR-70095']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3');
    $msgtblary['ITAANSIBLEH-ERR-70098']['paramlist']  = array(LC_RUN_MODE_STD=>'all' ,LC_RUN_MODE_VARFILE=>'0,3,4');

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
