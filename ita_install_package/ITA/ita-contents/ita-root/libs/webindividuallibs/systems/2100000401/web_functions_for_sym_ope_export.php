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
 * Symphony/オペレーションエクスポート用関数群
 *
 */

/**
 * データインポート管理テーブル更新処理
 */
function registerExportInfo($SymphonyClassList, $OperationClassList){
    global $g;

    $strResultCode = "000";
    $strDetailCode = "000";
    $strOutputStr = "";
    $transactionFlg = false;

    // トランザクション開始
    $varTrzStart = $g['objDBCA']->transactionStart();
    if ($varTrzStart === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900015',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $transactionFlg = true;

    $resArray = getSequenceLockInTrz('B_DP_SYM_OPE_STATUS_RIC','A_SEQUENCE');
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900052',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $resArray = getSequenceLockInTrz('B_DP_SYM_OPE_STATUS_JSQ','A_SEQUENCE');
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900052',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_JSQ', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    // 作業No.を取得する
    $sql = "SELECT VALUE FROM A_SEQUENCE WHERE NAME = 'B_DP_SYM_OPE_STATUS_RIC'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log("DB Error=[" . $objQuery->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_RIC', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $seqAry = array();
    while ($row = $objQuery->resultFetch()){
        $seqAry[] = $row;
    }

    $p_execution_utn_no = $seqAry[0]['VALUE'];

    // Jnl№を取得する
    $resArray = array();
    $resArray = getSequenceValueFromTable('B_DP_SYM_OPE_STATUS_JSQ', 'A_SEQUENCE', FALSE);
    if ($resArray[1] != 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900053',
                                             array('A_SEQUENCE', 'B_DP_SYM_OPE_STATUS_JSQ', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $p_execution_jnl_no = $resArray[0];

    $arrayConfig = array(
        'JOURNAL_SEQ_NO' => '',
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => '',
        'TASK_STATUS' => '',
        'DP_TYPE' => '',
        'FILE_NAME' => '',
        'DISP_SEQ' => '',
        'NOTE' => '',
        'DISUSE_FLAG' => '',
        'LAST_UPDATE_TIMESTAMP' => '',
        'LAST_UPDATE_USER' => ''
    );

    $arrayValue = array(
        'JOURNAL_SEQ_NO' => $p_execution_jnl_no,
        'JOURNAL_ACTION_CLASS' => '',
        'JOURNAL_REG_DATETIME' => '',
        'TASK_ID' => $p_execution_utn_no,
        'TASK_STATUS' => 1,
        'DP_TYPE' => 1,
        'FILE_NAME' => '',
        'DISP_SEQ' => '',
        'NOTE' => "SymphonyID:{$SymphonyClassList}\nOperationID:{$OperationClassList}",
        'DISUSE_FLAG' => '0',
        'LAST_UPDATE_TIMESTAMP' => '',
        'LAST_UPDATE_USER' => $g['login_id']
    );

    $resAry = makeSQLForUtnTableUpdate(
                  $g['db_model_ch'],
                  'INSERT',
                  'TASK_ID',
                  'B_DP_SYM_OPE_STATUS',
                  'B_DP_SYM_OPE_STATUS_JNL',
                  $arrayConfig,
                  $arrayValue
              );
    if ($resAry[0] === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900046',
                                             array('B_DP_SYM_OPE_STATUS', basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $sqlUtnBody = $resAry[1];
    $arrayUtnBind = $resAry[2];
    $sqlJnlBody = $resAry[3];
    $arrayJnlBind = $resAry[4];

    $objQueryUtn = $g['objDBCA']->sqlPrepare($sqlUtnBody);
    $objQueryJnl = $g['objDBCA']->sqlPrepare($sqlJnlBody);

    if ($objQueryUtn->getStatus() === false || $objQueryJnl->getStatus() === false) {
        web_log("DB Error=[" . $objQueryUtn->getLastError() . "]");
        web_log("DB Error=[" . $objQueryJnl->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    if ($objQueryUtn->sqlBind($arrayUtnBind) != "" || $objQueryJnl->sqlBind($arrayJnlBind) != "") {
        web_log("DB Error=[" . $objQueryUtn->getLastError() . "]");
        web_log("DB Error=[" . $objQueryJnl->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $rUtn = $objQueryUtn->sqlExecute();
    if ($rUtn != true) {
        web_log("DB Error=[" . $objQueryUtn->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_SYM_OPE_STATUS')));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $rJnl = $objQueryJnl->sqlExecute();
    if ($rJnl != true) {
        web_log("DB Error=[" . $objQueryJnl->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_SYM_OPE_STATUS_JNL')));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    // 更新系のシーケンスを増やす
    $p_execution_utn_next_no = $p_execution_utn_no + 1;
    $sql = "UPDATE A_SEQUENCE set VALUE = :value WHERE NAME = 'B_DP_SYM_OPE_STATUS_RIC'";
    $objQuery = $g['objDBCA']->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    
    $res = $objQuery->sqlBind(array('value' => $p_execution_utn_next_no));
    if ($res != "") {
        web_log("DB Error=[" . $objQuery->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900054',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlExecute();
    if ($res != true) {
        web_log("DB Error=[" . $objQuery->getLastError() . "]");
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900055',
                                             array(basename(__FILE__), __LINE__, 'B_DP_SYM_OPE_STATUS')));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $g['objDBCA']->transactionCommit();
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900036',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $g['objDBCA']->transactionExit();
    $transactionFlg = false;

    return $p_execution_utn_no;
}
