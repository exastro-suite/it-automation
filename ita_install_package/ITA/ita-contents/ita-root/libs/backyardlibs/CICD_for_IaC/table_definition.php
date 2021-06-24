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
//      テーブル定義クラス群
//
//////////////////////////////////////////////////////////////////////
class TDBase {

    function getTableName()       { return $this->TableName; }

    function getPKColumnName()    { return $this->PKColumnName; }

    function getJnlTableName()    { return $this->getTableName() . "_JNL"; }

    function getSequenceName()    { return $this->getTableName() . "_RIC"; }

    function getJnlSequenceName() { return $this->getTableName() . "_JSQ"; }

    function getColumndefine()    { return $this->jnlColumndefine; }

     // ジャーナルがないテーブル用
    function getColumndefineWithoutJournal()    { return $this->tblColumndefine; }

    function GetLastErrorMsg()    { return $this->LastErrorMsg; }

    function setConfig($cmDBobj) {
        $cmDBobj->ClearLastErrorMsg();
        $ret = $cmDBobj->getTableDefinition($this->getTableName(),$this->tblColumndefine,$this->jnlColumndefine,$this->PKColumnName);
        if($ret === false) {
            $msg = "";  //getTableDefinition message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$cmDBobj->getLastErrorMsg());
            $this->LastErrorMsg = $FREE_LOG;
            return false;
        }
        return true;
    }

    // ジャーナルがないテーブル用
    function setConfigWithoutJournal($cmDBobj) {
        $cmDBobj->ClearLastErrorMsg();
        $ret = $cmDBobj->getTableDefinitionWithoutJournal($this->getTableName(),$this->tblColumndefine,$this->PKColumnName);
        if($ret === false) {
            $msg = "";  //getTableDefinition message set
            $FREE_LOG = makeLogiFileOutputString(basename(__FILE__),__LINE__,$msg,$cmDBobj->getLastErrorMsg());
            $this->LastErrorMsg = $FREE_LOG;
            return false;
        }
        return true;
    }
}
//////////////////////////////////////////////////////////////////////
//  I/F情報
//////////////////////////////////////////////////////////////////////
class TD_B_CICD_IF_INFO extends TDBase {
    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_IF_INFO";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
//////////////////////////////////////////////////////////////////////
//  リポジトリ管理
//////////////////////////////////////////////////////////////////////
class TD_B_CICD_REPOSITORY_LIST  extends TDBase {
    // 自動同期状態:AUTO_SYNC_FLG  値リスト
    const C_AUTO_SYNC_FLG_ON          = 1;  // 有効
    const C_AUTO_SYNC_FLG_OFF         = 2;  // 無効

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_REPOSITORY_LIST";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
//////////////////////////////////////////////////////////////////////
//  資材管理
//////////////////////////////////////////////////////////////////////
class TD_B_CICD_MATERIAL_LIST  extends TDBase {
    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_MATERIAL_LIST";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}

//////////////////////////////////////////////////////////////////////
//  資材紐付け管理
//////////////////////////////////////////////////////////////////////
class TD_B_CICD_MATERIAL_LINK_LIST  extends TDBase {
    // 自動同期状態:AUTO_SYNC_FLG  値リスト
    const C_AUTO_SYNC_FLG_ON          = 1;  // 有効
    const C_AUTO_SYNC_FLG_OFF         = 2;  // 無効

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_MATERIAL_LINK_LIST";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
//////////////////////////////////////////////////////////////////////
//  Movement一覧(C_PATTERN_PER_ORCH)
//////////////////////////////////////////////////////////////////////
class TD_C_PATTERN_PER_ORCH  extends TDBase {
    // オーケストレータタイプ:ITA_EXT_STM_ID  値リスト
    const C_EXT_STM_ID_LEGACY         = 3;  //Ansible Legacy
    const C_EXT_STM_ID_PIONEER        = 4;  //Ansible Pioneer
    const C_EXT_STM_ID_ROLE           = 5;  //Ansible Legacy Role
    const C_EXT_STM_ID_TERRAFORM      = 10; //Terraform

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "C_PATTERN_PER_ORCH";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
//////////////////////////////////////////////////////////////////////
// リポジトリ同期状態マスタ
//////////////////////////////////////////////////////////////////////
class TD_B_CICD_REPO_SYNC_STATUS_NAME  extends TDBase {
    // 同期状態:SYNC_STATUS_ROW_ID 値リスト
    const C_SYNC_STATUS_ROW_ID_NORMAL   = '正常';
    const C_SYNC_STATUS_ROW_ID_ERROR    = '異常';
    const C_SYNC_STATUS_ROW_ID_RESTART  = '再開';

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_REPO_SYNC_STATUS_NAME";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
/////////////////////////////////////////////////////////
// Git資材ファイルタイプマスタ
/////////////////////////////////////////////////////////
class TD_B_CICD_MATERIAL_FILE_TYPE_NAME  extends TDBase {
    // 同期状態:MATL_FILE_TYPE_ROW_ID 値リスト
    const C_MATL_FILE_TYPE_ROW_ID_FILE       = 1;  //ファイル
    const C_MATL_FILE_TYPE_ROW_ID_ROLES      = 2;  //Roles

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_MATERIAL_FILE_TYPE_NAME";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
/////////////////////////////////////////////////////////
// ITA側素材タイプマスタ
/////////////////////////////////////////////////////////
class TD_B_CICD_MATERIAL_TYPE_NAME  extends TDBase {
    // ITA素材タイプ:MATL_TYPE_ROW_ID  値リスト
    const C_MATL_TYPE_ROW_ID_LEGACY	  = 1;  //Playbook素材集
    const C_MATL_TYPE_ROW_ID_PIONEER      = 2;  //対話ファイル素材集
    const C_MATL_TYPE_ROW_ID_ROLE         = 3;  //ロールパッケージ管理
    const C_MATL_TYPE_ROW_ID_CONTENT      = 4;  //ファイル管理
    const C_MATL_TYPE_ROW_ID_TEMPLATE     = 5;  //テンプレート管理
    const C_MATL_TYPE_ROW_ID_MODULE       = 6;  //Module素材
    const C_MATL_TYPE_ROW_ID_POLICY       = 7;  //Policy管理

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_MATERIAL_TYPE_NAME";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
/////////////////////////////////////////////////////////
// Gitプロトコルマスタ
/////////////////////////////////////////////////////////
class TD_B_CICD_GIT_PROTOCOL_TYPE_NAME  extends TDBase {
    // 同期状態:MATL_FILE_TYPE_ROW_ID 値リスト
    const C_GIT_PROTOCOL_TYPE_ROW_ID_HTTPS       = 1;  //https
    const C_GIT_PROTOCOL_TYPE_ROW_ID_SSH         = 2;  //ssh  現在未サポート
    const C_GIT_PROTOCOL_TYPE_ROW_ID_LOCAL       = 3;  //Local

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_GIT_PROTOCOL_TYPE_NAME";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
/////////////////////////////////////////////////////////
// Gitリポジトリタイプマスタ
/////////////////////////////////////////////////////////
class TD_B_CICD_GIT_REPOSITORY_TYPE_NAME  extends TDBase {
    // 同期状態:MATL_FILE_TYPE_ROW_ID 値リスト
    const C_GIT_REPO_TYPE_ROW_ID_PUBLIC          = 1;  // Public
    const C_GIT_REPO_TYPE_ROW_ID_PRIVATE         = 2;  // Private

    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "B_CICD_GIT_REPOSITORY_TYPE_NAME";
    }

    function setColumnConfigAttr() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefine();
        return $array;
    }
}
/////////////////////////////////////////////////////////
// 同期状態管理テーブル(履歴なし)
/////////////////////////////////////////////////////////
class TD_T_CICD_SYNC_STATUS  extends TDBase {
    public  $PKColumnName;
    public  $tblColumndefine;
    public  $jnlColumndefine;
    public  $LastErrorMsg;
    public  $TableName;

    function __construct(){
        $this->PKColumnName;
        $this->tblColumndefine = array();
        $this->jnlColumndefine = array();
        $this->LastErrorMsg = "";
        $this->TableName = "T_CICD_SYNC_STATUS";
    }

    function setColumnConfigAttrWithoutJournal() {
        // DATETIME型など型の設定が必要なカラムの設定
        //$array["DATETIME"] = "DATETIME";
        $array = $this->getColumndefineWithoutJournal();
        $array["SYNC_LAST_TIMESTAMP"] = "DATETIME";
        return $array;
    }
}

?>
