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
    // ログ出力フラグ追加 /etc/sysconfig/httpdより取得
    $vg_log_level = @getenv('ANSIBLE_RESTAPI_LOG_LEVEL');

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    $aryCallSetting = array('API_IDENTIFY'=>'ansible_driver',
                            'CALL_MODULE_LIST'=>array('common_functions.php','common_check.php', 'execute_statuscheck.php')
                            ,'RECEPT_TYPE'=>'0'
                            ,'AUTH_MODE'=>'0'
                      );
    require($root_dir_path ."/libs/restapicommonlibs/restapi_req_gate.php");
?>
