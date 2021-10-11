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
//  資材紐付管理
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
// リモートリポジトリ・資材紐付　同期状態マスタ
//////////////////////////////////////////////////////////////////////
class TD_SYNC_STATUS_NAME_DEFINE {
    // 同期状態:SYNC_STATUS_ROW_ID 値リスト
    public  $objMTS;
    function __construct($objMTS) {
        $this->objMTS = $objMTS;
    }
    //正常
    function NORMAL() {
        return $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2030");//'正常';
    }
    //異常
    function ERROR() {
        return $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2031");//'異常';
    }
    //再開
    function RESTART() {
        return $this->objMTS->getSomeMessage("ITACICDFORIAC-STD-2032");//'再開';
    }
}
class TD_B_CICD_REPO_SYNC_STATUS_NAME  extends TDBase {

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
// 資材紐付管理  紐付先素材タイプマスタ
/////////////////////////////////////////////////////////
class TD_B_CICD_MATERIAL_TYPE_NAME  extends TDBase {
    // 紐付先素材タイプ:MATL_TYPE_ROW_ID  値リスト
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
    // Gitプロトコル:PROTOCOL_TYPE_NAME 値リスト
    const C_GIT_PROTOCOL_TYPE_ROW_ID_HTTPS          = 1;  //https
    const C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_PASS       = 2;  //ssh(パスワード認証)
    const C_GIT_PROTOCOL_TYPE_ROW_ID_LOCAL          = 3;  //Local
    const C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY        = 4;  //ssh(鍵認証パスフレーズあり)
    const C_GIT_PROTOCOL_TYPE_ROW_ID_SSH_KEY_NOPASS = 5;  //ssh(鍵認証パスフレーズなし)

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
    // Gitリポジトリタイプ:Gitリポジトリタイプ 値リスト
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
// アクセス許可ロール付与フラグマスタ
/////////////////////////////////////////////////////////
class TD_B_CICD_RBAC_FLG_NAME  extends TDBase {
    // Gitリポジトリタイプ:Gitリポジトリタイプ 値リスト
    const C_RBAC_FLG_ROW_ID_OFF          = 1;  // なし
    const C_RBAC_FLG_ROW_ID_ON           = 2;  // あり

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
        $this->TableName = "B_CICD_RBAC_FLG_NAME";
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
///////////////////////////////////////////////////
// リモートリポジトリ管理ベースのリレーション先結合
///////////////////////////////////////////////////
class TQ_REPO_LIST_ALL_JOIN {
    function getSql($ansible_driver,$terraform_driver) {
        $OS_TYPE_COLUMN       = "";
        $DIALOG_TYPE_COLUMN   = "";
        $OS_TYPE_JOIN         = "";
        $DIALOG_TYPE_JOIN     = "";
        if($ansible_driver === true) {
            $OS_TYPE_COLUMN     = " T6.OS_TYPE_ID              M_OS_TYPE_ID,
                                    T6.OS_TYPE_NAME            M_OS_TYPE_NAME,
                                    T6.DISUSE_FLAG             OS_DISUSE_FLAG,   \n";
            $DIALOG_TYPE_COLUMN = " T5.DIALOG_TYPE_ID          M_DIALOG_TYPE_ID,
                                    T5.DIALOG_TYPE_NAME        M_DIALOG_TYPE_NAME,
                                    T5.DISUSE_FLAG             DALG_DISUSE_FLAG,   \n";
            $OS_TYPE_JOIN       = " LEFT JOIN B_OS_TYPE                     T6 ON (T1.OS_TYPE_ID     = T6.OS_TYPE_ID) \n";
            $DIALOG_TYPE_JOIN   = " LEFT JOIN B_ANSIBLE_PNS_DIALOG_TYPE     T5 ON (T1.DIALOG_TYPE_ID = T5.DIALOG_TYPE_ID) \n";
        }
        $sql = "SELECT
    T1.*,
    T0.HOSTNAME                M_HOSTNAME,
    T0.PROTOCOL                M_PROTOCOL,
    T0.PORT                    M_PORT,
    T3.MATL_FILE_PATH          M_MATL_FILE_PATH,
    T3.MATL_FILE_TYPE_ROW_ID   M_MATL_FILE_TYPE_ROW_ID,
    T4.USER_ID                 M_REST_USER_ID,
    T4.LOGIN_PW                M_REST_LOGIN_PW,
    T8.ITA_EXT_STM_ID          M_ITA_EXT_STM_ID,
    T9.USERNAME                M_REST_USERNAME,
    T9.USERNAME_JP             M_USERNAME_JP,
    $OS_TYPE_COLUMN
    $DIALOG_TYPE_COLUMN
    T2.DISUSE_FLAG             REPO_DISUSE_FLAG,
    T3.DISUSE_FLAG             MATL_DISUSE_FLAG,
    T4.DISUSE_FLAG             RACCT_DISUSE_FLAG,
    T7.DISUSE_FLAG             OPE_DISUSE_FLAG,
    T8.DISUSE_FLAG             PTN_DISUSE_FLAG,
    T9.DISUSE_FLAG             ACT_DISUSE_FLAG
FROM
    B_CICD_IF_INFO                          T0 ,
    B_CICD_MATERIAL_LINK_LIST               T1
    LEFT JOIN B_CICD_REPOSITORY_LIST        T2 ON (T1.REPO_ROW_ID    = T2.REPO_ROW_ID)
    LEFT JOIN B_CICD_MATERIAL_LIST          T3 ON (T1.MATL_ROW_ID    = T3.MATL_ROW_ID)
    LEFT JOIN D_CICD_ACCT_LINK              T4 ON (T1.ACCT_ROW_ID    = T4.ACCT_ROW_ID)
    $DIALOG_TYPE_JOIN
    $OS_TYPE_JOIN
    LEFT JOIN E_OPERATION_LIST              T7 ON (T1.DEL_OPE_ID     = T7.OPERATION_NO_UAPK)
    LEFT JOIN C_PATTERN_PER_ORCH            T8 ON (T1.DEL_MOVE_ID    = T8.PATTERN_ID)
    LEFT JOIN A_ACCOUNT_LIST                T9 ON (T4.USER_ID        = T9.USER_ID) ";
        return $sql;
    }
}
?>
