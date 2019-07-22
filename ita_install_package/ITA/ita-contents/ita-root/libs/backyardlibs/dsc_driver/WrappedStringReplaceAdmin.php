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

    function __construct($in_var_heder_id,
                         $strSourceString,
                   $arrylocalvars=array(),
                    $strHeadPattern="{{ ",
                     $strTailPattern=" }}" ){
        //----デフォルト値の設定
        $this->strReplacedString = '';
        //
        $this->setHeadPattern($strHeadPattern);
        $this->setTailPattern($strTailPattern);
        //デフォルト値の設定----
        //
        //----配列を初期化
        $this->aryFixedElementFromSourceString = array();
        $this->aryReplaceElementFromSourceString = array();

        //配列を初期化----
        if( $in_var_heder_id == DF_HOST_VAR_HED){
            $this->parseWrappedString($in_var_heder_id,$strSourceString,$arrylocalvars);

        }
        elseif( $in_var_heder_id == DF_HOST_CDT_HED){
            $this->parseWrappedString($in_var_heder_id,$strSourceString,$arrylocalvars);
        }
    }
    //*******************************************************************************************
    //----解析用のプロパティ
    //*******************************************************************************************
    function setHeadPattern($strValue){
        $boolRet = false;
        if( is_string($strValue)===true ){
            $this->strHeadPattern = $strValue;
            $boolRet = true;
        }
        return $boolRet;
    }
    //*******************************************************************************************
    function getHeadPattern(){
        return $this->strHeadPattern;
    }
    //*******************************************************************************************
    function setTailPattern($strValue){
        $boolRet = false;
        if( is_string($strValue)===true ){
            $this->strTailPattern = $strValue;
            $boolRet = true;
        }
        return $boolRet;
    }
    //*******************************************************************************************
    function getTailPattern(){
        return $this->strTailPattern;
    }
    //解析用のプロパティ----

    //----解析結果利用のためのプロパティ

    //*******************************************************************************************
    //----解析結果を取得するプロパティ
    //*******************************************************************************************
    function getParsedResult(){
        return array($this->aryFixedElementFromSourceString, $this->aryReplaceElementFromSourceString);
    }
    //解析結果を取得するプロパティ----

    //*******************************************************************************************
    //----置き換え結果取得メソッド
    //*******************************************************************************************
    function getReplacedString(){
        return $this->strReplacedString;
    }
    //置き換え結果取得メソッド----

    //解析結果利用のためのプロパティ----

    //*******************************************************************************************
    //----置き換え用のメソッド
    //*******************************************************************************************
    function stringReplace($in_strSourceString,$in_aryReplaceSource){

        $boolRet = false;

        $strHeadPattern = $this->getHeadPattern();
        $strTailPattern = $this->getTailPattern();

        $this->strReplacedString = "";
        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        //-------------------------------------------------------------------
        // Config全データをチェック変換
        //-------------------------------------------------------------------
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
                //-------------------------------------------------------------------
                // 各変数を具体値に置換
                //-------------------------------------------------------------------
                foreach($in_aryReplaceSource as $key1=>$val1){
                    // 変数情報を作成
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

    //*******************************************************************************************
    //----NODE置き換え用のメソッド
    //*******************************************************************************************
    function NodeReplace($in_strSourceString,$in_Node,$in_hostname){

        $boolRet = false;

        $strHeadPattern = $this->getHeadPattern();
        $strTailPattern = $this->getTailPattern();

        $this->strReplacedString = "";
        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        //-------------------------------------------------------------------
        // Config全データをチェック変換
        //-------------------------------------------------------------------
        foreach($arry_list as $strSourceString){
                $var_name = $in_Node;
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
                //-------------------------------------------------------------------
                // 各変数を具体値に置換
                //-------------------------------------------------------------------
                // 変数検索
                if(mb_strpos($strSourceString, $in_Node, 0, "UTF-8") === false){
                }
                else{
                    // 変数を具体値に置換
                    $repstr  = $this->mb_str_replace($var_name,$in_hostname,$strSourceString);
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

    //*******************************************************************************************
    //----ホスト変数解析用のメソッド
    //*******************************************************************************************
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
    function mb_str_replace($search, $replace, $haystack, $encoding="UTF-8")
    {
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

    //解析用のメソッド----
}
//----ここまでクラス定義
?>
