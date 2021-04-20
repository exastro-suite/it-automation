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
//      Ansibleインターフェース情報+AnsibleTower情報 ビュー定義クラス
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

class DAnsibleTowerIfinfo extends TableBaseDefinition {

    public static function getTableName() {
        return "D_ANSIBLE_TOWER_IF_INFO";
    }

    protected static $specificColumns = array(
            "ANSIBLE_IF_INFO_ID"            => "",
            "ANSIBLE_HOSTNAME"              => "",
            "ANSIBLE_PROTOCOL"              => "",
            "ANSIBLE_PORT"                  => "",
            "ANSTWR_HOST_ID"                => "",
            "ANSTWR_PROTOCOL"               => "",
            "ANSTWR_PORT"                   => "",
            "ANSIBLE_PROXY_ADDRESS"         => "",
            "ANSIBLE_PROXY_PORT"            => "",
            "ANSIBLE_EXEC_MODE"             => "",
            "ANSIBLE_STORAGE_PATH_LNX"      => "",
            "ANSIBLE_STORAGE_PATH_ANS"      => "",
            "SYMPHONY_STORAGE_PATH_ANS"     => "",
            "ANSIBLE_EXEC_OPTIONS"          => "",
            "ANSIBLE_EXEC_USER"             => "",
            "ANSIBLE_ACCESS_KEY_ID"         => "",
            "ANSIBLE_SECRET_ACCESS_KEY"     => "",
            "ANSTWR_ORGANIZATION"           => "",
            "ANSTWR_AUTH_TOKEN"             => "",
            "ANSTWR_DEL_RUNTIME_DATA"       => "",
            "NULL_DATA_HANDLING_FLG"        => "", 
            "ANSIBLE_NUM_PARALLEL_EXEC"     => "",
            "ANSIBLE_REFRESH_INTERVAL"      => "",
            "ANSIBLE_TAILLOG_LINES"         => "",
            "ANSTWR_HOSTNAME"               => "",
            "ANSTWR_LOGIN_AUTH_TYPE"        => "",
            "ANSTWR_LOGIN_USER"             => "",
            "ANSTWR_LOGIN_PASSWORD"         => "",
            "ANSTWR_LOGIN_SSH_KEY_FILE"     => "",
            "ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE" => "",
            "ANSTWR_ISOLATED_TYPE"          => "",
        );

    public static function getRowDiffKeyColumns() {
        throw new BadMethodCallException("Not implemented.");
    }

    public static function getPKColumnName() {
        return "ANSIBLE_IF_INFO_ID";
    }

}

?>
