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
    //----解析結果を保存する配列
    protected $aryFixedElementFromSourceString;
    protected $aryReplaceElementFromSourceString;
    //解析結果を保存する配列----
    function __construct($in_var_heder,
                         $strSourceString,
                   $arrylocalvars=array()){

        //----配列を初期化
        $this->aryFixedElementFromSourceString = array();
        $this->aryReplaceElementFromSourceString = array();
        //配列を初期化----

        $this->parseWrappedString($in_var_heder,$strSourceString,$arrylocalvars);
    }
    //*******************************************************************************************
    //----解析結果を取得するプロパティ
    //*******************************************************************************************
    function getParsedResult(){
        return array($this->aryFixedElementFromSourceString, $this->aryReplaceElementFromSourceString);
    }
    //解析結果を取得するプロパティ----

    //*******************************************************************************************
    //----ホスト変数解析用のメソッド
    //*******************************************************************************************
    function parseWrappedString($in_var_heder,$in_strSourceString,$arrylocalvars){
        $boolRet = false;

        $this->aryFixedElementFromSourceString = array();
        $this->aryReplaceElementFromSourceString = array();

        // 入力データを行単位に分解
        $arry_list = explode("\n",$in_strSourceString);
        foreach($arry_list as $strSourceString){
            $defaultStrSourceString = $strSourceString;
            $strSourceString = $strSourceString . "\n";
            // コメント行は読み飛ばす
            if(mb_strpos($strSourceString,"#",0,"UTF-8") === 0 || mb_strpos($strSourceString,"//",0,"UTF-8") === 0){
                $this->aryFixedElementFromSourceString[] = $strSourceString;
                continue;
            }

            // コメント(#)マーク以降の文字列を削除する。
            $wstr = $strSourceString;
            $wspstr = explode("#",$wstr);
            $strSourceString = $wspstr[0];

            // コメント(//)マーク以降の文字列を削除する。
            $wstr2 = $strSourceString;
            $wspstr2 = explode("//",$wstr2);
            $strSourceString = $wspstr2[0];

            if( is_string($strSourceString)===true ){
                $boolRet = true;

                //対象文字列から始まる文字数を取得
                $numResultOfSearchHead = mb_strpos($strSourceString, $in_var_heder);

                //行の中に対象文字列が無い場合は次へ
                if($numResultOfSearchHead === false){
                    $this->aryFixedElementFromSourceString[] = $defaultStrSourceString;
                    continue;
                }

                //対象文字列から先頭文字列より前にある文字列を削除
                $strCutBeforeString = strstr($strSourceString, $in_var_heder);

                //対象文字列から先頭文字列を削除
                $strCutHeadString = str_replace($in_var_heder, '', $strCutBeforeString);

                //文字列を『"』で区切りった最初の文字列を抽出
                $wspstr3 = explode('"', $strCutHeadString);
                $strWrappedString = $wspstr3[0];

                //スペースを削除
                $strWrappedString = trim($strWrappedString);

                //対象文字列に空文字が入った場合は次へ
                if($strWrappedString == ""){
                    $this->aryFixedElementFromSourceString[] = $defaultStrSourceString;
                    continue;
                }

                //変数名を退避する
                $this->aryReplaceElementFromSourceString[] = $strWrappedString;

            }
        }

        return $boolRet;
    }

    //解析用のメソッド----
}
//----ここまでクラス定義
?>
