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
//  【処理概要】
//    ・webおよびbackyard共通で呼び出される。
//
//////////////////////////////////////////////////////////////////////
function makeLogiFileOutputString($file,$line,$logstr1,$logstr2) {
    $msg = sprintf("[FILE]:%s [LINE]:%s %s",$file,$line,$logstr1);
    if(strlen($logstr2) != 0) {
        $msg .= "\n" . $logstr2;
    }
    return $msg;
}
function debuglog($file,$line,$title,$data) {
    $dump = var_export($data,true);
    $tmpVarTimeStamp = time();
    $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);

    $log = sprintf("%s:%s:%s:\n--%s--\n[%s]",$logtime,$file,$line,$title,$dump);
    error_log($log);
}
