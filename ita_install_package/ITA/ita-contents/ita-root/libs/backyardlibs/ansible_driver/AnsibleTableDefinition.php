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
////////////////////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・Ansible テーブル構造定義
//
////////////////////////////////////////////////////////////////////////////////////

// C_ANSIBLE_LNS_EXE_INS_MNG/C_ANSIBLE_PNS_EXE_INS_MNG/C_ANSIBLE_LRL_EXE_INS_MNG
function CreateExecInstMngArray(&$ary) {
    $ary = array();
    $ary["JOURNAL_SEQ_NO"]               = "";
    $ary["JOURNAL_REG_DATETIME"]         = ""; 
    $ary["JOURNAL_ACTION_CLASS"]         = ""; 
    $ary["EXECUTION_NO"]                 = ""; 
    $ary["SYMPHONY_NAME"]                = ""; 
    $ary["EXECUTION_USER"]               = ""; 
    $ary["STATUS_ID"]                    = ""; 
    $ary["SYMPHONY_INSTANCE_NO"]         = "";
    $ary["PATTERN_ID"]                   = "";
    $ary["I_PATTERN_NAME"]               = "";
    $ary["I_TIME_LIMIT"]                 = "";
    $ary["I_ANS_HOST_DESIGNATE_TYPE_ID"] = "";
    $ary["I_ANS_PARALLEL_EXE"]           = "";
    $ary["I_ANS_WINRM_ID"]               = "";
    $ary["I_ANS_PLAYBOOK_HED_DEF"]       = "";
    $ary["I_ANS_EXEC_OPTIONS"]           = "";
    $ary["OPERATION_NO_UAPK"]            = "";
    $ary["I_OPERATION_NAME"]             = "";
    $ary["I_OPERATION_NO_IDBH"]          = "";
    $ary["TIME_BOOK"]                    = "";
    $ary["TIME_START"]                   = "";
    $ary["TIME_END"]                     = "";
    $ary["FILE_INPUT"]                   = "";
    $ary["FILE_RESULT"]                  = "";
    $ary["RUN_MODE"]                     = "";
    $ary["EXEC_MODE"]                    = "";
    $ary["DISP_SEQ"]                     = "";
    $ary["NOTE"]                         = "";
    $ary["DISUSE_FLAG"]                  = "";
    $ary["LAST_UPDATE_TIMESTAMP"]        = "";
    $ary["LAST_UPDATE_USER"]             = "";
}

// E_ANSIBLE_LNS_EXE_INS_MNG/E_ANSIBLE_PNS_EXE_INS_MNG/E_ANSIBLE_LRL_EXE_INS_MNG
function CreateExecInstMngViewArray(&$ary) {
    $ary = array();
    CreateExecInstMngArray($ary);
    $ary["STATUS_NAME"]                  = "";
    $ary["ANS_HOST_DESIGNATE_TYPE_NAME"] = "";
    $ary["ANS_WINRM_FLAG_NAME"]          = "";
    $ary["RUN_MODE_NAME"]                = "";
    $ary["EXEC_MODE_NAME"]               = "";
}

function SetExecInstMngColumnType(&$ary) {
    $ary["TIME_BOOK"]                    = "DATETIME";
    $ary["TIME_START"]                   = "DATETIME";
    $ary["TIME_END"]                     = "DATETIME";
}

?>

