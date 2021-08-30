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

//----ここからクラス定義
class WrappedStringReplaceAdmin{
    protected $strReplacedString;
    
    protected $strHeadPattern;
    protected $strTailPattern;

    //----解析結果を保存する配列
    protected $aryFixedElementFromSourceString;
    protected $aryReplaceElementFromSourceString;
    //解析結果を保存する配列----

    protected $la_aryErrorinfo = array();

    protected $la_aryvarsarray = array();
    
    // テンプレートファイル内のホスト変数退避変数
    protected $aryVarsElementFromSourceString;

    function __construct($in_var_heder_id,$strSourceString, $arrylocalvars=array(), $strHeadPattern="{{ ", $strTailPattern=" }}")
    {
        //----デフォルト値の設定
        $this->strReplacedString = '';
        $this->setHeadPattern($strHeadPattern);
        $this->setTailPattern($strTailPattern);
        //デフォルト値の設定----

        //----配列を初期化
        $this->aryFixedElementFromSourceString = array();
        $this->aryReplaceElementFromSourceString = array();

        $this->aryVarsElementFromSourceString = array();

        //配列を初期化----
    }
    //----解析用のプロパティ
    function setHeadPattern($strValue){
        $boolRet = false;
        if( is_string($strValue)===true ){
            $this->strHeadPattern = $strValue;
            $boolRet = true;
        }
        return $boolRet;
    }
    function getHeadPattern(){
        return $this->strHeadPattern;
    }
    function setTailPattern($strValue){
        $boolRet = false;
        if( is_string($strValue)===true ){
            $this->strTailPattern = $strValue;
            $boolRet = true;
        }
        return $boolRet;
    }
    function getTailPattern(){
        return $this->strTailPattern;
    }
    //解析用のプロパティ----

    //----解析結果利用のためのプロパティ

    //----置き換え結果取得メソッド
    function getReplacedString(){
        return $this->strReplacedString;
    }
    //置き換え結果取得メソッド----

    //解析結果利用のためのプロパティ----

    //----置き換え用のメソッド
    function stringReplace($in_strSourceString,$in_aryReplaceSource){

        $boolRet = false;

        $strHeadPattern = $this->getHeadPattern();
        $strTailPattern = $this->getTailPattern();

        $this->strReplacedString = "";
        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        foreach($arry_list as $strSourceString){
            $strSourceString = $strSourceString . "\n";
            if(mb_strpos($strSourceString,"#", 0, "UTF-8") === 0){
                $this->strReplacedString = $this->strReplacedString . $strSourceString;
            }
            else{
                // エスケープコード付きの#を一時的に改行に置換
                $wstr = $strSourceString;
                $rpstr  = mb_ereg_replace("\\\\#","\n\n",$wstr);
                // コメント( #)マーク以降の文字列を削除した文字列で変数の具体値置換
                // #の前の文字がスペースの場合にコメントとして扱う
                $expstr = explode(" #",$rpstr);
                $strSourceString = $expstr[0];
                unset($expstr[0]);
                // 各変数を具体値に置換

                foreach($in_aryReplaceSource as $key1=>$val1){
                    $var_name = $strHeadPattern . $key1 . $strTailPattern;
                    // 変数検索
                    if(mb_strpos($strSourceString, $var_name, 0, "UTF-8") === false){
                        continue;
                    }
                    // 変数を具体値に置換
                    $repstr  = $this->mb_str_replace($var_name,$val1,$strSourceString);
                    $strSourceString = $repstr;
                }
                // コメント(#)以降の文字列を元に戻す。
                foreach( $expstr as $value ){
                    $strSourceString = $strSourceString . " #" . $value;
                }
                // エスケープコード付きの#を元に戻す。
                $rpstr  = mb_ereg_replace("\n\n","\#",$strSourceString);
                $this->strReplacedString = $this->strReplacedString . $rpstr;
            }            
        }
        return $boolRet;
    }
    //置き換え用のメソッド----

    //----template変数解析結果を取得するプロパティ
    function getTPFvarsarrayResult(){
        return array($this->la_aryvarsarray,$this->la_aryErrorinfo);
    }

    //----copy変数解析結果を取得するプロパティ
    function getCPFvarsarrayResult(){
        return array($this->la_aryvarsarray,$this->la_aryErrorinfo);
    }
    //copy解析結果を取得するプロパティ----

    function mb_str_replace($search, $replace, $haystack, $encoding="UTF-8"){
        // 検索文字列の文字数取得
        $search_len = mb_strlen($search, $encoding);
        // 置換文字列の文字数取得
        $replace_len = mb_strlen($replace, $encoding);

        // マッチング
        $offset = mb_strpos($haystack, $search);
        // 一致した場合
        while ($offset !== FALSE){
            // 差替え処理
            $haystack = mb_substr($haystack, 0, $offset).$replace.mb_substr($haystack, $offset + $search_len);
            $offset = mb_strpos($haystack, $search, $offset + $replace_len);
        }
        return $haystack;
    }
}
//----ここまでクラス定義

////////////////////////////////////////////////////////////////////////
// 概要
//   指定された文字列から変数(Fillter付)を抜出す。
// パラメータ
//   $in_var_heder_id:    変数名の先頭文字列　TPF_
//   $in_strSourceString: ファイルの内容
//   $ina_varsLineArray:  取得した変数位置配列
//                        $ina_varsarray[][行番号][変数名]
//   $ina_varsarray:      取得した変数配列
//                        $ina_varsarray[][変数名]
// 戻り値
//   true
////////////////////////////////////////////////////////////////////////
function SimpleFillterVerSearch($in_var_heder_id,$in_strSourceString,&$ina_varsLineArray,&$ina_varsArray,$arrylocalvars,$FillterVars=false){
    $ina_varsLineArray= array();
    $ina_varsArray= array();
    // Fillter定義されている変数も抜出すか判定
    if($FillterVars === true) {
        $tailmarke = "([\s]}}|[\s]+\|)";
    } else {
        $tailmarke = "[\s]}}";
    }
    // 入力データを行単位に分解
    $arry_list = explode("\n",$in_strSourceString);
    $line = 0;
    foreach($arry_list as $strSourceString){
        // 行番号
        $line = $line + 1;

        $strSourceString = $strSourceString . "\n";
        // コメント行は読み飛ばす
        if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0){
            continue;
        }
        // エスケープコード付きの#を一時的に改行に置換
        $wstr = $strSourceString;
        // コメント( #)マーク以降の文字列を削除する。
        // #の前の文字がスペースの場合にコメントとして扱う
        $wspstr = explode(" #",$wstr);
        $strRemainString = $wspstr[0];
        if( is_string($strRemainString)===true ){
            //空行は読み飛ばす
            if(strlen(trim($strRemainString)) == 0){
                continue;
            }
            // 変数名　{{ ???_[a-zA-Z0-9_] | Fillter function }} を取出す
            $ret = preg_match_all("/{{[\s]" . $in_var_heder_id . "[a-zA-Z0-9_]*" . $tailmarke . "/",$strRemainString,$var_match);
            if($ret !== false){
                for($idx2=0;$idx2 < count($var_match[0]);$idx2++){
                    //変数名を退避する
                    $array = array();
                    $ret = preg_match_all("/" . $in_var_heder_id . "[a-zA-Z0-9_]*/",$var_match[0][$idx2],$var_name_match);
                    $array[$line] = trim($var_name_match[0][0]);
                    $ina_varsLineArray[] = $array;
                    $ina_varsArray[]     = trim($var_name_match[0][0]);
                }
            }
            // 予約変数　{{ 予約変数 | Fillter function }}　の抜き出し
            foreach($arrylocalvars as $localvarname) {
                $ret = preg_match_all("/{{[\s]" . $localvarname . $tailmarke . "/",$strRemainString,$var_match);
                if($ret !== false){
                    for($idx2=0;$idx2 < count($var_match[0]);$idx2++){
                        //変数名を退避する
                        $array = array();
                        $ret = preg_match_all("/" . $localvarname . "/",$var_match[0][$idx2],$var_name_match);
                        $array[$line] = trim($var_name_match[0][0]);
                        $ina_varsLineArray[] = $array;
                        $ina_varsArray[]     = trim($var_name_match[0][0]);
                    }
                }
            }
        }
    }
    return true;
}
?>