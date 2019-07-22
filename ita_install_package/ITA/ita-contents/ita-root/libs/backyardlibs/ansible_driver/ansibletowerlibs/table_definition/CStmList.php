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
//      機器一覧 テーブル定義クラス
//
//////////////////////////////////////////////////////////////////////

////////////////////////////////
// ルートディレクトリを取得
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode("ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}

require_once($root_dir_path . "/libs/backyardlibs/ansible_driver/ansibletowerlibs/table_definition/TableBaseDefinition.php");   

class CStmList extends TableBaseDefinition {

    public static function getTableName() {
        return "C_STM_LIST";
    }

    protected static $specificColumns = array(
            "SYSTEM_ID"                       => "",
            "HARDAWRE_TYPE_ID"                => "",
            "HOSTNAME"                        => "",
            "IP_ADDRESS"                      => "",
            "ETH_WOL_MAC_ADDRESS"             => "",
            "ETH_WOL_NET_DEVICE"              => "",
            "PROTOCOL_ID"                     => "",
            "LOGIN_USER"                      => "",
            "LOGIN_PW_HOLD_FLAG"              => "",
            "LOGIN_PW"                        => "",
            "LOGIN_AUTH_TYPE"                 => "",
            "WINRM_PORT"                      => "",
            "WINRM_SSL_CA_FILE"               => "",
            "OS_TYPE_ID"                      => "",
            "SSH_EXTRA_ARGS"                  => "",
            "HOSTS_EXTRA_ARGS"                => "",
            "SYSTEM_NAME"                     => "",
            "COBBLER_PROFILE_ID"              => "",
            "INTERFACE_TYPE"                  => "",
            "MAC_ADDRESS"                     => "",
            "NETMASK"                         => "",
            "GATEWAY"                         => "",
            "STATIC"                          => "",
            "CONN_SSH_KEY_FILE"               => "",
            "DSC_CERTIFICATE_FILE"            => "",
            "DSC_CERTIFICATE_THUMBPRINT"      => "",
            "ANSTWR_INSTANCE_GRP_ITA_MNG_ID"  => "",
        );

    public static function getRowDiffKeyColumns() {
        throw new BadMethodCallException("Not implemented.");
    }

    public static function getPKColumnName() {
        return "SYSTEM_ID";
    }

}

?>
