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
/**
 * データインポート管理処理
 */
try {
    require_once $g['root_dir_path'] . '/libs/commonlibs/common_php_req_gate.php';

    // 設定期間を過ぎても実行中のレコード件数を取得する
    $taskStatus = 2;
    $num = file_get_contents($g['root_dir_path'] . '/confs/backyardconfs/ita_base/data_portability_running_limit.txt');
    $param = '-' . trim($num) . ' seconds';
    $now = strtotime($param);
    $limitDatetime = date('Y-m-d H:i:s', $now) . '.000000';

    $sql  = 'SELECT COUNT(*) AS COUNT';
    $sql .= ' FROM B_DP_STATUS';
    $sql .= ' WHERE TASK_STATUS = ' . $taskStatus;
    $sql .= " AND LAST_UPDATE_TIMESTAMP < '" . $limitDatetime . "'";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                                         array(basename(__FILE__), __LINE__)));
    }

    $res = $objQuery->sqlExecute();
    if ($res === false) {
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                                         array(basename(__FILE__), __LINE__)));
    }
    
    while ($row = $objQuery->resultFetch()){
        $runningCnt = $row['COUNT'];
    }
    
    $runningMsg = '';
    if ($runningCnt > 0) {
        $runningMsg  = '<span style="color: red; padding-left: 10px;">';
        $runningMsg .= $g['objMTS']->getSomeMessage('ITABASEH-ERR-900064');
        $runningMsg .= '</span>';
    }
} catch (Exception $e) {
    web_log($e->getMessage());
    webRequestForceQuitFromEveryWhere(500,10310101);
}
