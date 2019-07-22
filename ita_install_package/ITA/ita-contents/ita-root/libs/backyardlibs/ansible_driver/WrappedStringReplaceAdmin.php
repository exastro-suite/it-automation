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

        if( $in_var_heder_id == DF_HOST_VAR_HED){
            // playbook/対話ファイルで使用しているホスト変数取得
            $this->parseWrappedString($in_var_heder_id,$strSourceString,$arrylocalvars);

            // playbookで使用している {% %}で囲まれているホスト変数取得
            $this->getSpecialVARSParsed("BOOK",$strSourceString);
        }
        elseif( $in_var_heder_id == DF_HOST_TPF_HED){
            // playbookで使用しているtemplate変数取得
            $this->serchTPFVars($in_var_heder_id,$strSourceString);
        }
        elseif( $in_var_heder_id == DF_HOST_CPF_HED){
            // playbookで使用しているcopyモジュールの変数取得
            $this->parseWrappedString($in_var_heder_id,$strSourceString,$arrylocalvars);
        }
        elseif( $in_var_heder_id == DF_HOST_GBL_HED){
            // playbook/対話ファイルで使用しているホスト変数取得
            $this->parseWrappedString($in_var_heder_id,$strSourceString,$arrylocalvars);

            // playbookで使用している {% %}で囲まれているホスト変数取得
            $this->getSpecialVARSParsed_GBLVARonly("BOOK",$strSourceString);
        }
        elseif( $in_var_heder_id == DF_HOST_TEMP_GBL_HED){
            // テンプレートファイルで使用しているグローバル変数取得
            $this->parseTPFWrappedString(DF_HOST_GBL_HED,$strSourceString,$arrylocalvars);

            // temprateで使用している {% %}で囲まれているグローバル変数取得
            $this->getSpecialVARSParsed_GBLVARonly("TEMP",$strSourceString);
        }
        else{
            // テンプレートファイルで使用しているホスト変数取得
            $this->parseTPFWrappedString(DF_HOST_VAR_HED,$strSourceString,$arrylocalvars);

            // temprateで使用している {% %}で囲まれているホスト変数取得
            $this->getSpecialVARSParsed("TEMP",$strSourceString);
        }
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

    //----解析結果を取得するプロパティ
    function getParsedResult(){
        return array($this->aryFixedElementFromSourceString, $this->aryReplaceElementFromSourceString);
    }
    //解析結果を取得するプロパティ----

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


    //----ホスト変数解析用のメソッド
    function parseWrappedString($in_var_heder,$in_strSourceString,$arrylocalvars){
        $boolRet = false;

        //
        $this->aryFixedElementFromSourceString = array();
        $this->aryReplaceElementFromSourceString = array();
        //
        $strHeadPattern = $this->getHeadPattern();
        $strTailPattern = $this->getTailPattern();
        //
        $numLengthOfHeadPattern = mb_strlen($strHeadPattern, "UTF-8");
        $numLengthOfTailPattern = mb_strlen($strTailPattern, "UTF-8");

        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        foreach($arry_list as $strSourceString){
            $strSourceString = $strSourceString . "\n";
            // コメント行は読み飛ばす
            if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0){
                $this->aryFixedElementFromSourceString[] = $strSourceString;
                continue;
            }
            // エスケープコード付きの#を一時的に改行に置換
            $wstr = $strSourceString;
            $rpstr  = mb_ereg_replace("\\\\#","\n\n",$wstr);
            // コメント( #)マーク以降の文字列を削除する。
            // #の前の文字がスペースの場合にコメントとして扱う
            $wspstr = explode(" #",$rpstr);
            $strSourceString = $wspstr[0];
            if( is_string($strSourceString)===true ){
                $boolRet = true;
                $strRemainString = $strSourceString;

                do{
                    $numLengthOfRemainString = mb_strlen($strRemainString, "UTF-8");
                    $numResultOfSearchHead = mb_strpos($strRemainString, $strHeadPattern, 0,"UTF-8");
                    if( $numResultOfSearchHead===false ){
                        //----先頭パターンが見つからなかった
                        $this->aryFixedElementFromSourceString[] = $strRemainString;
                        break;
                        //先頭パターンが見つからなかった----
                    }else{
                        $strTempStr1Body = mb_substr($strRemainString, 0, $numResultOfSearchHead);

                        $strTempRemainString = mb_substr($strRemainString, $numResultOfSearchHead + $numLengthOfHeadPattern, $numLengthOfRemainString - $numResultOfSearchHead - $numLengthOfHeadPattern, "UTF-8");
                        $numResultOfSearchTail = mb_strpos($strTempRemainString, $strTailPattern, 0, "UTF-8");
                        if( $numResultOfSearchTail===false ){
                            //----末尾パターンが見つからなかった
                            $this->aryFixedElementFromSourceString[] = $strRemainString;
                            break;
                            //末尾パターンが見つからなかった----
                        }else{
                            $this->aryFixedElementFromSourceString[] = $strTempStr1Body;

                            $strWrappedString = mb_substr($strTempRemainString, 0, $numResultOfSearchTail, "UTF-8");
                            //ローカル予約変数か判定する。
                            foreach( $arrylocalvars as $lvarname ){
                                if($strWrappedString == $lvarname){
                                    //変数名を退避する
                                    $this->aryReplaceElementFromSourceString[] = $strWrappedString;
                                }
                            }
                            //変数名の先頭がユーザー変数を表す文字列となっているか判定
                            if(mb_strpos($strWrappedString,$in_var_heder,0,"UTF-8")===0){
                                // 変数名が英数字と_かチェック これ以外の場合は変数として扱わない
                                $strWrappedString = trim($strWrappedString);
                                $ret = preg_match("/^" . $in_var_heder . "[a-zA-Z0-9_]*$/",$strWrappedString);
                                if($ret === 1){
                                    //変数名を退避する
                                    $this->aryReplaceElementFromSourceString[] = $strWrappedString;
                                }
                           }

                            $numLengthOfTempRemainString = mb_strlen($strTempRemainString, "UTF-8");
                            $strRemainString = mb_substr($strTempRemainString, $numResultOfSearchTail + $numLengthOfTailPattern, $numLengthOfTempRemainString - $numResultOfSearchTail - $numLengthOfTailPattern, "UTF-8");
                        }
                    }
                }while( $numResultOfSearchHead!==false && $numResultOfSearchTail!==false );
            }
        }
        return $boolRet;
    }
    //解析用のメソッド----

    //----template変数解析結果を取得するプロパティ
    function getTPFvarsarrayResult(){
        return array($this->la_aryvarsarray,$this->la_aryErrorinfo);
    }

    //----copy変数解析結果を取得するプロパティ
    function getCPFvarsarrayResult(){
        return array($this->la_aryvarsarray,$this->la_aryErrorinfo);
    }
    //copy解析結果を取得するプロパティ----

    //----テンプレートファイル内のホスト変数解析用のメソッド
    function parseTPFWrappedString($in_var_heder,$in_strSourceString,$arrylocalvars){
        $boolRet = false;

        $this->aryVarsElementFromSourceString = array();

        $strHeadPattern = $this->getHeadPattern();
        $strTailPattern = $this->getTailPattern();
        //
        $numLengthOfHeadPattern = mb_strlen($strHeadPattern, "UTF-8");
        $numLengthOfTailPattern = mb_strlen($strTailPattern, "UTF-8");

        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        foreach($arry_list as $strSourceString){
            if( is_string($strSourceString)===true ){
                $boolRet = true;
                $strRemainString = $strSourceString;

                do{
                    $numLengthOfRemainString = mb_strlen($strRemainString, "UTF-8");
                    $numResultOfSearchHead = mb_strpos($strRemainString, $strHeadPattern, 0,"UTF-8");
                    if( $numResultOfSearchHead===false ){
                        //----先頭パターンが見つからなかった
                        break;
                        //先頭パターンが見つからなかった----
                    }else{
                        $strTempStr1Body = mb_substr($strRemainString, 0, $numResultOfSearchHead);

                        $strTempRemainString = mb_substr($strRemainString, $numResultOfSearchHead + $numLengthOfHeadPattern, $numLengthOfRemainString - $numResultOfSearchHead - $numLengthOfHeadPattern, "UTF-8");
                        $numResultOfSearchTail = mb_strpos($strTempRemainString, $strTailPattern, 0, "UTF-8");
                        if( $numResultOfSearchTail===false ){
                            //----末尾パターンが見つからなかった
                            break;
                            //末尾パターンが見つからなかった----
                        }else{
                            $strWrappedString = mb_substr($strTempRemainString, 0, $numResultOfSearchTail, "UTF-8");
                            //ローカル予約変数か判定する。
                            foreach( $arrylocalvars as $lvarname ){
                                if($strWrappedString == $lvarname){
                                    //変数名を退避する
                                    $this->aryVarsElementFromSourceString[] = $strWrappedString;
                                }
                            }
                            //変数名の先頭がユーザー変数を表す文字列となっているか判定
                            if(mb_strpos($strWrappedString,$in_var_heder,0,"UTF-8")===0){
                                // 変数名が英数字と_かチェック これ以外の場合は変数として扱わない
                                $strWrappedString= trim($strWrappedString);
                                $ret = preg_match("/^" . $in_var_heder . "[a-zA-Z0-9_]*$/",$strWrappedString);
                                if($ret === 1){
                                    //変数名を退避する
                                    $this->aryVarsElementFromSourceString[] = $strWrappedString;
                                }
                            }

                            $numLengthOfTempRemainString = mb_strlen($strTempRemainString, "UTF-8");
                            $strRemainString = mb_substr($strTempRemainString, $numResultOfSearchTail + $numLengthOfTailPattern, $numLengthOfTempRemainString - $numResultOfSearchTail - $numLengthOfTailPattern, "UTF-8");
                        }
                    }
                }while( $numResultOfSearchHead!==false && $numResultOfSearchTail!==false );
            }
        }
        return $boolRet;
    }
    //解析用のメソッド----

    //----テンプレートファイル内のホスト変数解析結果を取得するプロパティ
    function getTPFVARSParsedResult(){
        return $this->aryVarsElementFromSourceString;
    }
    //解析結果を取得するプロパティ----
    function getSpecialVARSParsed($in_type,$in_strSourceString){
        $strChkString = "";

        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        foreach($arry_list as $strSourceString){
            if($in_type == "BOOK"){
                // Playbookの場合はコメント行は読み飛ばす
                if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0){
                    continue;
                }
                // コメント( #)マーク以降の文字列を削除する。
                // #の前の文字がスペースの場合にコメントとして扱う
                $wspstr = explode(" #",$strSourceString);
                $strRemainString = $wspstr[0];
                if( is_string($strRemainString)===true ){
                    $strChkString = $strChkString . $strRemainString;
                }
            }
            else{
                // temprateの場合
                $strChkString = $strChkString . $strSourceString;
            }
        }
        // 改行をスペースにする 制御コードを取除く
        $strChkString = preg_replace("/\n/"," ",$strChkString);
        // tabをスペースにする  制御コードを取除く
        $strChkString = preg_replace("/\t/"," ",$strChkString);
        // {% %}で囲まれている文字列を検索
        $ret = preg_match_all("/{%.+?%}/",$strChkString,$match);
        if($ret !== false){
            // {% %}で囲まれている文字列から変数定義を抜出す
            for($idx1=0;$idx1 < count($match[0]);$idx1++){
                // 変数名　△VAR_xxxx△ を取出す   xxxx::半角英数字か__
                $ret = preg_match_all("/(\s)VAR_[a-zA-Z0-9_]*(\s)/",$match[0][$idx1],$var_match);
                if($ret !== false){
                    for($idx2=0;$idx2 < count($var_match[0]);$idx2++){
                        // playbookかtemprateの判定
                        if($in_type == "BOOK"){
                            //playbookの変数名を退避する
                            $this->aryReplaceElementFromSourceString[] = trim($var_match[0][$idx2]);
                        }
                        else{
                            //temprateの変数名を退避する
                            $this->aryVarsElementFromSourceString[] = trim($var_match[0][$idx2]);
                        }
                    }
                }
            }
        }
    }
    function getIndentPos($in_String){
        if(strlen($in_String)===0){
            return -1;
        }
        $space_count=0;
        $read_array = str_split($in_String);
        for($idx=0;$idx<count($read_array);$idx++){
            // - もスペースとしてカウント
            if($read_array[$idx] == " " || $read_array[$idx] == "-"){
               $space_count++;
            }
            else{
                break;
            }
        }
        return $space_count;
    }
    //----テンプレート変数の抜き出しメソッド
    function serchTPFVars($in_var_heder_id,$in_strSourceString){
        $boolRet = true;

        $this->la_aryErrorinfo = array();
        $this->la_aryvarsarray = array();
    
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
                // 変数名　{{ ???_[a-zA-Z0-9_] }} を取出す
                $ret = preg_match_all("/{{(\s)" . $in_var_heder_id . "[a-zA-Z0-9_]*(\s)}}/",$strRemainString,$var_match);
                if($ret == 1){
                    //変数名を退避する
                    $ret = preg_match_all("/" . $in_var_heder_id . "[a-zA-Z0-9_]*/",$var_match[0][0],$var_name_match);
                    $this->la_aryvarsarray[$line] = trim($var_name_match[0][0]);
                }
            }
        }
        return $boolRet;
    }
    function getSpecialVARSParsed_GBLVARonly($in_type,$in_strSourceString){
        $strChkString = "";

        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        foreach($arry_list as $strSourceString){
            if($in_type == "BOOK"){
                // Playbookの場合はコメント行は読み飛ばす
                if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0){
                    continue;
                }
                // コメント( #)マーク以降の文字列を削除する。
                // #の前の文字がスペースの場合にコメントとして扱う
                $wspstr = explode(" #",$strSourceString);
                $strRemainString = $wspstr[0];
                if( is_string($strRemainString)===true ){
                    $strChkString = $strChkString . $strRemainString;
                }
            }
            else{
                // temprateの場合
                $strChkString = $strChkString . $strSourceString;
            }
        }
        // 改行をスペースにする 制御コードを取除く
        $strChkString = preg_replace("/\n/"," ",$strChkString);
        // tabをスペースにする  制御コードを取除く
        $strChkString = preg_replace("/\t/"," ",$strChkString);
        // {% %}で囲まれている文字列を検索
        $ret = preg_match_all("/{%.+?%}/",$strChkString,$match);
        if($ret !== false){
            // {% %}で囲まれている文字列から変数定義を抜出す
            for($idx1=0;$idx1 < count($match[0]);$idx1++){
                // 変数名　△VAR_xxxx△ を取出す   xxxx::半角英数字か__
                $ret = preg_match_all("/(\s)GBL_[a-zA-Z0-9_]*(\s)/",$match[0][$idx1],$var_match);
                if($ret !== false){
                    for($idx2=0;$idx2 < count($var_match[0]);$idx2++){
                        // playbookかtemprateの判定
                        if($in_type == "BOOK"){
                            //playbookの変数名を退避する
                            $this->aryReplaceElementFromSourceString[] = trim($var_match[0][$idx2]);
                        }
                        else{
                            //temprateの変数名を退避する
                            $this->aryVarsElementFromSourceString[] = trim($var_match[0][$idx2]);
                        }
                    }
                }
            }
        }
    }
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
//   指定された文字列から変数を抜出す。
// パラメータ
//   $in_var_heder_id:    変数名の先頭文字列　TPF_
//   $in_strSourceString: ファイルの内容
//   $ina_varsarray:      取得した変数配列
//                        $ina_varsarray[][行番号][変数名]
// 戻り値
//   true
////////////////////////////////////////////////////////////////////////
function SimpleVerSearch($in_var_heder_id,$in_strSourceString,&$ina_varsarray){
    $ina_varsarray = array();
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
            // 変数名　{{ ???_[a-zA-Z0-9_] }} を取出す
            $ret = preg_match_all("/{{(\s)" . $in_var_heder_id . "[a-zA-Z0-9_]*(\s)}}/",$strRemainString,$var_match);
            if($ret !== false){
                for($idx2=0;$idx2 < count($var_match[0]);$idx2++){
                    //変数名を退避する
                    $array = array();
                    $ret = preg_match_all("/" . $in_var_heder_id . "[a-zA-Z0-9_]*/",$var_match[0][$idx2],$var_name_match);
                    $array[$line] = trim($var_name_match[0][0]);
                    $ina_varsarray[] = $array;
                }
            }
        }
    }
    return true;
}
?>
