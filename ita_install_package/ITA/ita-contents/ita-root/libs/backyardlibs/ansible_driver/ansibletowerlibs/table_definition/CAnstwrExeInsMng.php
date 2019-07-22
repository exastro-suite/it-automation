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
//////////////////////////////////////////////////////////////////////
//
//  【概要】
//      インターフェース情報 テーブル定義クラス
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if (empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/table_definition/TableBaseDefinition.php");   

class CAnstwrExeInsMng extends TableBaseDefinition {

    public static function getTableName() {
        global $vg_exe_ins_msg_table_name;
        return $vg_exe_ins_msg_table_name;
    }

    protected static $specificColumns = array(
            "EXECUTION_NO"                 => "",
            "EXECUTION_USER"               => "",
            "STATUS_ID"                    => "",
            "SYMPHONY_INSTANCE_NO"         => "",
            "PATTERN_ID"                   => "",
            "I_PATTERN_NAME"               => "",
            "I_TIME_LIMIT"                 => "",
            "I_ANS_HOST_DESIGNATE_TYPE_ID" => "",
            "I_ANS_PARALLEL_EXE"           => "",
            "I_ANS_WINRM_ID"               => "",
            "I_ANS_PLAYBOOK_HED_DEF"       => "",
            "I_ANS_EXEC_OPTIONS"           => "",
            "OPERATION_NO_UAPK"            => "",
            "I_OPERATION_NAME"             => "",
            "I_OPERATION_NO_IDBH"          => "",
            "TIME_BOOK"                     => "DATETIME",
            "TIME_START"                    => "DATETIME",
            "TIME_END"                      => "DATETIME",
            "FILE_INPUT"                   => "",
            "FILE_RESULT"                  => "",
            "RUN_MODE"                     => "",
            "EXEC_MODE"                    => "",
        );

    public static function getRowDiffKeyColumns() {
        throw new BadMethodCallException("Not implemented.");
    }

    public static function getPKColumnName() {
        return "EXECUTION_NO";
    }
}

?>
