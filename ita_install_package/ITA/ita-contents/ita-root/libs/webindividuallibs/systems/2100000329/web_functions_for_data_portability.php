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
 * Javascript用メッセージを作成する
 *
 * @param    array    $msgIdAry    画面に表示するメッセージID
 * @return   string   $strJsMsg    JS用メッセージ
 */
function getJsMessages($msgIdAry){
    global $g;
    $aryImportFilePath[] = $g['objMTS']->getTemplateFilePath('ITABASEC', 'STD', '_js');
    $strTemplateBody = getJscriptMessageTemplate($aryImportFilePath, $g['objMTS']);
    $strTemplateBody = str_replace('dysp0', 'delimiter_dysp0', $strTemplateBody);
    $tmpJsMsgAry = array_reverse(explode('delimiter_', $strTemplateBody));
    $strJsMsg = '';
    foreach ($msgIdAry as $id) {
        foreach ($tmpJsMsgAry as $msg) {
            if (strpos($msg, $id) !== false) {
                $strJsMsg .= $msg;
                break;
            }
        }
    }

    return $strJsMsg;
}

/**
 * 選択状態にあるメニューを取得する
 *
 * @return    string    $menuOn    選択状態にするメニューのID
 */
function getMenuOn(){
    $menuOn = '';
    if (isset($_REQUEST['menu_on']) === true && strlen($_REQUEST['menu_on']) > 0) {
        $menuOn = $_REQUEST['menu_on'];
    }

    return $menuOn;
}
