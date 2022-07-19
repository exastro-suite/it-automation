<?php
//   Copyright 2022 NEC Corporation
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
class CommonTerraformHCL2JSONParse{
    //----解析結果を保存する配列
    protected $aryVariableBlockFromSourceString;
    //解析結果を保存する配列----
    function __construct($root_dir_path, $filepath){

        //----配列を初期化
        $this->aryReplaceElementFromSourceString = array();
        $this->aryVariableBlockFromSourceString = array();
        $this->err = NULL;
        $this->res = true;
        //配列を初期化----
        $this->getMemberVars($filepath, $root_dir_path);
    }
    //*******************************************************************************************
    //----解析結果を取得するプロパティ
    //*******************************************************************************************
    function getParsedResult(){
        return array(
            "res"       => $this->res,
            "variables" => $this->aryVariableBlockFromSourceString,
            "err"       => $this->err,
        );
    }
    //解析結果を取得するプロパティ----

    //*******************************************************************************************
    //----メンバー変数を取得する
    //*******************************************************************************************
    function getMemberVars($filepath, $root_dir_path) {
        global $objMTS;
        // python-hcl2のインストール確認
        $command = "pip show python-hcl2";
        exec($command, $output, $retval);
        if ($retval != 0) {
            $err_detail = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221120");
            $this->res = false;
            $this->err = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221110", array($err_detail));
        }

        if ($this->res != false) {
            $output = "";
            // python3の場所特定
            $command = "sudo which python3";
            exec($command, $output, $retval);
            if ($retval != 0) {
                $this->res = false;
            }
            $python3 = $output[0];
            $output = "";

            // 対象ファイルをパーサーでjson化
            $command = "sudo $python3 $root_dir_path/libs/commonlibs/common_terraform_hcl2json_parse.py '".$filepath."'";
            exec($command, $output, $retval);
            // コマンド失敗
            if ($retval != 0) {
                $this->res = false;
            }
            // エラーの場合
            if (!preg_match('/^\{.*/', $output[0])) {
                $this->res = false;
                $this->err = $objMTS->getSomeMessage("ITATERRAFORM-ERR-221150", array($output[0]));
                $this->command = $command;
                $this->output = $output;
                $this->retval = $retval;
            }
        }

        if ($this->res == true) {
            // tfファイルの配列化
            $pattern = '/\"type\"\:\s(null)/';
            $replacement = '"type": "${null}"';
            $output[0] = preg_replace($pattern, $replacement, $output[0]);

            $json_str = json_decode($output[0], true);
            $variable_list = [];
            // typeの配列化
            if (isset($json_str["variable"])) {
                $variable_list = $json_str["variable"];
            }

            if (is_array($variable_list) && !empty($variable_list)) {
                foreach ($variable_list as $variable_block) {
                    foreach ($variable_block as $variable => $block) {
                        $val_res = [];
                        $nest    = 0;
                        $type = "";
                        $default = [];
                        if (isset($block["type"])) {
                            $type    = $block["type"];
                        }
                        if (isset($block["default"])) {
                            $default = $block["default"];
                        } else {
                            $block["default"] = [];
                        }

                        // typeのエスケープ文字を消去
                        $search = "\\";
                        $replace = "";
                        $typestr = str_replace($search, $replace, $type);

                        $pattern = '/\"(.*?)\"/';
                        $replacement = '\'${1}\'';
                        $typestr = preg_replace($pattern, $replacement, $typestr);

                        $pattern = '/\'(.*?)\'/';
                        $replacement = '"${1}"';
                        $typestr = preg_replace($pattern, $replacement, $typestr);

                        $typestr = '"' . $typestr . '"';

                        // ),)のカンマを削除
                        $pattern = '/\)\,\)/';
                        $replacement = '))';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }

                        // listとset
                        // --------------------------------------------------
                        // list/setの下が
                        // １．代入順序なし、メンバー変数なし
                        // list(string) => list(string)
                        $pattern = '/\"\$\{([a-z]+?)\(([a-z]+)\)\}\"/';
                        $replacement = '"${${1}(${2})}"';

                        // ２．list(list(string)) => ${list(list)} + ${list(string)}
                        $pattern = '/\"\$\{([a-z]+?)\(([a-z]+?)\((.*?)\)\)\}\"/';
                        $replacement = '{"${${1}(${2})}": ["${${2}(${3})}"]}';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }

                        // ３．tuple
                        $pattern = '/\"\$\{([a-z]+?)\(([a-z]+?)\(\[(.*)\]\)\)\}\"/';
                        $replacement = '{"${${1}}": ["${${2}([${3}])}"]}';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }

                        // ４．object
                        $pattern = '/\"\$\{([a-z]+?)\(([a-z]+?)\(\{(.*)\}\)\)\}\"/';
                        $replacement = '{"${${1}}": ["${${2}({${3}})}"]}';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }
                        // --------------------------------------------------

                        // tuple --------------------------------------------
                        // 入れ子はこれで取得できる。
                        $pattern = '/\"\$\{([a-z]+?)\(\[(.*)\]\)\}\"/';
                        $replacement = '{"${${1}}": [${2}]}';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }
                        $pattern = '/\$\{([a-z]+?)\(\[(.*)\]\)\}/';
                        $replacement = '{"${${1}}": [${2}]}';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }
                        // 入れ子以外で並んでいる場合
                        $pattern = '/\"\$\{([a-z]*?)\(\[(.*)\]\)\}\"/';
                        $replacement = '{"${${1}}": [${2}]}';
                        $typestr = preg_replace($pattern, $replacement, $typestr);

                        $pattern = '/\]\)\}\"(.*)\"\$\{([a-z]*?)\(\[(.*)/';
                        $replacement = ']}${1}{"${${2}}": [${3}';
                        $typestr = preg_replace($pattern, $replacement, $typestr);
                        // -------------------------------------------- tuple

                        // object --------------------------------------------
                        // 入れ子はこれで取得できる。
                        $pattern = '/\"\$\{([a-z]+?)\(\{(.*)\}\)\}\"/';
                        $replacement = '{"${${1}}": {${2}}}';
                        while (preg_match($pattern, $typestr)) {
                            $typestr = preg_replace($pattern, $replacement, $typestr);
                        }

                        // 入れ子以外で並んでいる場合
                        $pattern = '/\"\$\{([a-z]*?)\(\{(.*)\}\)\}\"/';
                        $replacement = '{"${${1}}": {${2}}}';
                        $typestr = preg_replace($pattern, $replacement, $typestr);

                        $pattern = '/\}\)\}\"(.*)\"\$\{([a-z]*?)\(\{(.*)/';
                        $replacement = '}}${1}{"${${2}}": {${3}';
                        $typestr = preg_replace($pattern, $replacement, $typestr);
                        // -------------------------------------------- object

                        $pattern = '/\"(.*?)\"\:\s(None)/';
                        $replacement = '"${1}": "${null}"';
                        $typestr = preg_replace($pattern, $replacement, $typestr);

                        $tmp_type = json_decode($typestr, true);

                        if(json_last_error() !== JSON_ERROR_NONE) {
                            $this->res = false;
                            $this->err = json_last_error_msg();
                        }

                        $block["variable"]   = $variable;

                        if (empty($block["default"])) {
                            $block["default"] = "";
                        }

                        // map型が含まれる場合は全部をmapとみなす
                        $is_map = false;
                        $this->isMap($tmp_type, $is_map);
                        if ($is_map) {
                            $tmp_type = '${map}';
                            $block["typeStr"] = $tmp_type;
                        }
                        $block["type"]       = $tmp_type;
                        $moduleInfo = $this->getModuleRecord($block["type"], $block["default"]);
                        if (preg_match('/^\$\{(.*?)\}$/', $moduleInfo["type"], $match)) {
                            $block["typeStr"] = $match[1];
                        }
                        else {
                            $block["typeStr"] = NULL;
                        }

                        $this->aryVariableBlockFromSourceString[]  = $block;
                    }

                }
            }
            $this->res = true;
        }
        // return true;
    }

    //*******************************************************************************************
    //----Module変数紐付けに登録する用の値を取得（type配列の一番上/配列でない場合はtype_arrayを利用）
    //*******************************************************************************************
    function getModuleRecord($type_array, $default_array)
    {
        if (is_array($type_array)) {
            foreach ($type_array as $type_key => $type_value) {
                $first_type_key = $type_key;
                return ["type" => $first_type_key, "default" => $default_array];
            }
        } else {
            $first_type_key = $type_array;
            return ["type" => $first_type_key, "default" => $default_array];
        }
    }

    //*******************************************************************************************
    //----map型判定
    //*******************************************************************************************
    function isMap($type_array, &$is_map=false) {
        $pattern = '/^\$\{map(.*?)\}$/';
        $pattern2 = '/^\$\{map\}$/';
        if (is_array($type_array) && $is_map == false) {
            // mapの存在チェック
            foreach ($type_array as $type_key => $type_value) {
                if (is_array($type_value)) {
                    // もう一周
                    $this->isMap($type_value, $is_map);
                    if (preg_match($pattern, $type_key) || preg_match($pattern2, $type_key)) {
                        $is_map = true;
                    }
                }
                // 最端でループから抜ける
                else {
                    if (preg_match($pattern, $type_key) || preg_match($pattern, $type_value) || preg_match($pattern2, $type_key) || preg_match($pattern2, $type_value)) {
                        $is_map = true;
                    }
                }
            }
        }
        elseif (!is_array($type_array) && $is_map == false)
        {
            if (preg_match($pattern, $type_array) || preg_match($pattern2, $type_array)) {
                $is_map = true;
            }
        }
    }
    //解析用のメソッド----
}
//----ここまでクラス定義
?>
