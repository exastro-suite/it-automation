<?php
// 対象のファイルを暗号化する

$root_dir_path = $argv[1] . "/ita-root";

// サイト共通の自作PHPファンクション集を呼び出し
require_once ($root_dir_path . "/libs/commonlibs/common_php_functions.php");
require_once ($root_dir_path . "/libs/commonlibs/common_php_classes.php");

// ----DBコネクト
$tmpResult = true;
$objDBCA = null;

$objDBCA = new DBConnectAgent();
$tmpResult = $objDBCA->connectOpen();
// DBコネクト----

// VIEW定義の確認
$sql = "SHOW CREATE VIEW G_UQ_HOST_LIST";

$objQuery = $objDBCA->sqlPrepare($sql);
$result = $objQuery->sqlExecute();

$row = $objQuery->resultFetch();
$viewDifine = $row['Create View'];

$result = strpos($viewDifine, '10000000');

if($result !== false){
    return true;
}

// VIEWの再作成
$sql =
"CREATE OR REPLACE VIEW G_UQ_HOST_LIST AS                                                       " .
"SELECT SYSTEM_ID                                                    AS KY_KEY   ,              " .
"       CONCAT('[H]',HOSTNAME) AS KY_VALUE ,                                                    " .
"       0                                                            AS KY_SOURCE,              " .
"       9223372036854775807                                          AS STRENGTH ,              " .
"       ACCESS_AUTH                                                  AS ACCESS_AUTH,            " .
"       DISUSE_FLAG                                                              ,              " .
"       LAST_UPDATE_TIMESTAMP                                                    ,              " .
"       LAST_UPDATE_USER                                                                        " .
"FROM   C_STM_LIST                                                                              " .
"WHERE  DISUSE_FLAG = '0'                                                                       " .
"UNION                                                                                          " .
"SELECT ROW_ID + 10000000                                                    AS KY_KEY   ,      " .
"       CONCAT('[HG]',HOSTGROUP_NAME)  AS KY_VALUE ,                                            " .
"       1                                                                    AS KY_SOURCE,      " .
"       STRENGTH                                                             AS STRENGTH ,      " .
"       ACCESS_AUTH                                                          AS ACCESS_AUTH,    " .
"       DISUSE_FLAG                                                                      ,      " .
"       LAST_UPDATE_TIMESTAMP                                                            ,      " .
"       LAST_UPDATE_USER                                                                        " .
"FROM   F_HOSTGROUP_LIST                                                                        " .
"WHERE  DISUSE_FLAG = '0'                                                                       ";
;

echo("Change the definition of view[G_UQ_HOST_LIST].\n");

$objQuery = $objDBCA->sqlPrepare($sql);
$result = $objQuery->sqlExecute();

// ホストグループ検索対象を検索
$sql = "SELECT * FROM F_SPLIT_TARGET WHERE DISUSE_FLAG = '0'";

$objQuery = $objDBCA->sqlPrepare($sql);
$result = $objQuery->sqlExecute();

$splitTargetArray = array();
while ($row = $objQuery->resultFetch()){
    $splitTargetArray[] = $row;
}

$tableArray = array();

// ホストグループ検索対象のデータごとにループ
foreach($splitTargetArray as $splitTarget){

    $menuId = sprintf("%010d", $splitTarget['INPUT_MENU_ID']);

    $cmd = "grep setDBMainTableHiddenID ${root_dir_path}/ita-root/webconfs/sheets/${menuId}_loadTable.php";
    $output = NULL;
    exec($cmd, $output, $return_var);

    $tableName = explode("'", $output[0])[1];
    $tableNameJnl = $tableName . "_JNL";

    if(in_array($tableName, $tableArray)){
        continue;
    }
    $tableArray[] = $tableName;

    // パッチ適用
    $sql = "UPDATE $tableName SET KY_KEY=KY_KEY+9990000 WHERE KY_KEY>10000";
    $objQuery = $objDBCA->sqlPrepare($sql);
    $result = $objQuery->sqlExecute();

    $sql = "UPDATE $tableNameJnl SET KY_KEY=KY_KEY+9990000 WHERE KY_KEY>10000";
    $objQuery = $objDBCA->sqlPrepare($sql);
    $result = $objQuery->sqlExecute();

    $sql = "UPDATE F_SPLIT_TARGET SET DIVIDED_FLG='0'";
    $objQuery = $objDBCA->sqlPrepare($sql);
    $result = $objQuery->sqlExecute();
}
?>