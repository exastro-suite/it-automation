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
// 資材紐付管理 入力チェックバリデータ  インストール状態でチェックが変わるので、インストール状態でチェックが変わるので、資材紐付時にも通す。
function MatlLinkColumnValidator1($ColumnValueArray,$RepoId,$MatlLinkId,$objMTS,&$retStrBody,$ansible_driver,$terraform_driver) {
    $retBool = true;
    // 紐付け先 素材集タイプ毎の必須入力チェック
    switch($ColumnValueArray['MATL_TYPE_ROW_ID']['COMMIT']) {
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
        if(strlen($ColumnValueArray['DIALOG_TYPE_ID']['COMMIT']) == 0) {
            $ColumnName = $objMTS->getSomeMessage("ITACICDFORIAC-MNU-1200030800");
            // 紐付先資材タイプがAnsible-Pioneerコンソール/対話ファイル素材集の場合は必須項目です。(項目:{}) (資材紐付管理 項番:{})
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2025",array($ColumnName,$MatlLinkId));
            $retBool = false;
        }
        if(strlen($ColumnValueArray['OS_TYPE_ID']['COMMIT']) == 0) {
            $ColumnName = $objMTS->getSomeMessage("ITACICDFORIAC-MNU-1200030900");
            // 紐付先資材タイプがAnsible-Pioneerコンソール/対話ファイル素材集の場合は必須項目です。(項目:{}) (資材紐付管理 項番:{})
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2025",array($ColumnName,$MatlLinkId));
            $retBool = false;
        }
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
        if(strlen($ColumnValueArray['MATL_LINK_NAME']['COMMIT']) != 0) {
            $ret = preg_match("/^CPF_[_a-zA-Z0-9]+$/", $ColumnValueArray['MATL_LINK_NAME']['COMMIT']);
            if($ret != 1) {
                // "紐付先資材タイプがAnsible共通コンソール／ファイル管理の場合の紐付先資材名は正規表記(/^CPF_[_a-zA-Z0-9]+$/)に一致するデータを入力してください。";
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2022",array($MatlLinkId));
                $retBool = false;
            }
        }
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
        if(strlen($ColumnValueArray['MATL_LINK_NAME']['COMMIT']) != 0) {
            $ret = preg_match("/^TPF_[_a-zA-Z0-9]+$/", $ColumnValueArray['MATL_LINK_NAME']['COMMIT']);
            if($ret != 1) {
                // 紐付先資材タイプがAnsible共通コンソール／テンプレート管理の場合の紐付先資材名は正規表記(/^TPF_[_a-zA-Z0-9]+$/)に一致するデータを入力してください。
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2023",array($MatlLinkId));
                $retBool = false;
            }
        }
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
        if(strlen($ColumnValueArray['MATL_LINK_NAME']['COMMIT']) != 0) {
            $ret = preg_match("/^[a-zA-Z0-9_\-]+$/", $ColumnValueArray['MATL_LINK_NAME']['COMMIT']);
            if($ret != 1) {
                // 紐付先資材タイプがTerraformコンソール/Policy管理の場合、紐付先資材名は正規表記(/^[a-zA-Z0-9_\-]+$/)に一致するデータを入力してください。(資材紐付 項番:{})";
                if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
                $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2072",array($MatlLinkId));
                $retBool = false;
            }
        }
        break;
    }  
        
    // オペレーションIDとMovementの未入力チェック
    $ColumnNameOpe  = $objMTS->getSomeMessage("ITACICDFORIAC-MNU-1200031600");
    $ColumnNameMove = $objMTS->getSomeMessage("ITACICDFORIAC-MNU-1200031700");
    if(strlen($ColumnValueArray['DEL_OPE_ID']['COMMIT']) != 0) {
        if(strlen($ColumnValueArray['DEL_MOVE_ID']['COMMIT']) == 0) {
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            // オペレーションが選択されている場合は必須項目です。(項目:{}) (資材紐付管理 項番:{})
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2024",array($ColumnNameMove,$MatlLinkId));
            $retBool = false;
        }
    } 
    if(strlen($ColumnValueArray['DEL_MOVE_ID']['COMMIT']) != 0) {
        if(strlen($ColumnValueArray['DEL_OPE_ID']['COMMIT']) == 0) {
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            // Movementが選択されている場合は必須項目です。(項目:{}) (資材紐付管理 項番:{})
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2026",array($ColumnNameOpe,$MatlLinkId));
            $retBool = false;
        }
    } 
    return $retBool;
}
// 紐付先資材タイプと資材パスの組み合わせチェック
function MatlLinkColumnValidator2($MatlTypeId,$MatlFileTypeId,$objMTS,$RepoId,$MatlLinkId,&$retStrBody) {
    switch($MatlTypeId) {
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
        if(TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_FILE != $MatlFileTypeId) {
            //"紐付先資材タイプがAnsible-LegacyRoleコンソール/パッケージ管理以外の場合、資材パスはファイルパスを選択して下さい。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2037",array($RepoId,$MatlLinkId));
            return false;
        }
        break;
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
        if(TD_B_CICD_MATERIAL_FILE_TYPE_NAME::C_MATL_FILE_TYPE_ROW_ID_ROLES != $MatlFileTypeId) {
            //紐付先資材タイプがAnsible-LegacyRoleコンソール/パッケージ管理の場合、資材パスはRolesディレクトリパスを選択して下さい。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2036",array($RepoId,$MatlLinkId));
            return false;
        }
        break;
    }
    return true;
}

// 資材紐付管理 リレーション先レコードチェックバリデータ  資材紐付実行時
function MatlLinkColumnValidator3($row,$objMTS,&$retStrBody) {

    $retBool = true;
    // リモートリポジトリ管理が廃止レコードか判定
    if(@strlen($row['REPO_ROW_ID']) != 0) {
        if((@strlen($row['REPO_DISUSE_FLAG']) == 0) || ($row['REPO_DISUSE_FLAG'] == '1')) {
            // 資材紐付管理のリモートリポジトリがリモートリポジトリ管理に登録されていません。(資材紐付管理 項番:{}　リモートリポジトリ管理 項番:{})"
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2030",array($row['MATL_LINK_ROW_ID'],$row['REPO_ROW_ID']));
            $retBool = false;
        }
    }

    // 資材一覧が廃止レコードか判定
    if(@strlen($row['MATL_ROW_ID']) != 0) {
        if((@strlen($row['MATL_DISUSE_FLAG']) == 0) || ($row['MATL_DISUSE_FLAG'] == '1')) {
            // 資材紐付管理の資材パスが資材管理に登録されていません。(資材紐付管理 項番:{}　資材管理 項番:{})";
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2031",array($row['MATL_LINK_ROW_ID'],$row['MATL_ROW_ID']));
            $retBool = false;
        }
    }

    // Restユーザーが廃止レコードか判定
    if(@strlen($row['ACCT_ROW_ID']) != 0) {
        if((@strlen($row['RACCT_DISUSE_FLAG']) == 0) || ($row['RACCT_DISUSE_FLAG'] == '1')) {
            //資材紐付管理のRestユーザがRestユーザ管理に登録されていません。(資材紐付管理 項番:{}　Restユーザ管理 項番:{})"
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2032",array($row['MATL_LINK_ROW_ID'],$row['ACCT_ROW_ID']));
            $retBool = false;
        }
        // ユーザ管理が廃止レコードか判定
        if((@strlen($row['ACT_DISUSE_FLAG']) == 0) || ($row['ACT_DISUSE_FLAG'] == '1')) {
            //資材紐付管理のRestユーザが管理コンソール／ユーザ管理に登録されていません。(資材紐付管理 項番:{}　ユーザ管理 項番:{})"
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2033",array($row['MATL_LINK_ROW_ID'],$row['M_REST_USER_ID']));
            $retBool = false;
        }
    }

    // オペレーションIDとMovemnetIDが両方設定さけている場合に オペレーションIDとMovemnetIDが廃止レコードか判定
    if((@strlen($row['DEL_OPE_ID']) != 0) && (@strlen($row['DEL_MOVE_ID']) != 0)) {
        if((@strlen($row['OPE_DISUSE_FLAG']) == 0) || ($row['OPE_DISUSE_FLAG'] == '1')) {
            //資材紐付管理のオペレーションが基本コンソール／オペレーション一覧に登録されていません。(資材紐付管理 項番:{}　オペレーション一覧 項番:{})"
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2034",array($row['MATL_LINK_ROW_ID'],$row['DEL_OPE_ID']));
            $retBool = false;
        }
        if((@strlen($row['PTN_DISUSE_FLAG']) == 0) || ($row['PTN_DISUSE_FLAG'] == '1')) {
            //資材紐付管理のMovementが基本コンソール／Movement一覧に登録されていません。(資材紐付管理 項番:{}　Movement一覧 項番:{})"
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2035",array($row['MATL_LINK_ROW_ID'],$row['DEL_MOVE_ID']));
            $retBool = false;
        }
    }
    
    // 紐付先資材タイプが対話ファイル素材集の場合 対話種別とOS種別の廃止レコードか判定
    switch($row['MATL_TYPE_ROW_ID']) {   // 紐付先資材タイプが対話ファイル素材集
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:
        // 対話種別が廃止レコードか判定
        if((@strlen($row['DALG_DISUSE_FLAG']) == 0) || ($row['DALG_DISUSE_FLAG'] == '1')) {
            // 資材紐付管理の対話種別がPioneerコンソール／対話種別リストに登録されていません。(資材紐付管理 項番:{}　対話種別リスト 項番:{})
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2038",array($row['MATL_LINK_ROW_ID'],$row['M_DIALOG_TYPE_ID']));
            $retBool = false;
        }
        // OSが廃止レコードか判定
        if((@strlen($row['OS_DISUSE_FLAG']) == 0) || ($row['OS_DISUSE_FLAG'] == '1')) {
            // 資材紐付管理のOS種別がPioneerコンソール／OS種別マスタに登録されていません。(資材紐付管理 項番:{}　OS種別マスタ 項番:{})
            if(strlen($retStrBody) != 0) { $retStrBody .= "\n"; }
            $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2039",array($row['MATL_LINK_ROW_ID'],$row['M_OS_TYPE_ID']));
            $retBool = false;
        }
        break;
    }
    return $retBool;
}
// 資材紐付管理 紐付先資材タイプとインストール状態をチェック  インストール状態でチェックが変わるので、資材紐付時にも通す。
function MatlLinkColumnValidator4($RepoId,$MatlLinkId,$MatlTypeId,$objMTS,&$retStrBody,$ansible_driver,$terraform_driver) {
    $retBool = true;
    if($ansible_driver === false) {
        switch($MatlTypeId) {
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
             // Ansibleドライバーがインストールされていないので、選択されている紐付先資材タイプは処理出来ません。(リモートリポジトリ管理:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2028",array($RepoId,$MatlLinkId));
             return false;
        }
    }
    if($terraform_driver === false) {
        switch($MatlTypeId) {
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
        case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
             // Terrahomeドライバーがインストールされていないので、選択されている紐付先資材タイプは処理出来ません。(リモートリポジトリ管理:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2027",array($RepoId,$MatlLinkId));
             return false;
        }
    }
    return true;
}
// 資材紐付管理 紐付先資材タイプとMovementタイプをチェック  
function MatlLinkColumnValidator5($RepoId,$MatlLinkId,$ExtStnId,$MatlTypeId,$objMTS,&$retStrBody) {
    if(strlen($ExtStnId) == 0) {
        return true;
    }
    $retBool = true;
    switch($MatlTypeId) {
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_LEGACY:       //Playbook素材集
        if($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY) {
             // 選択されている紐付先資材タイプとMovementの組み合わせが不正です。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2046",array($RepoId,$MatlLinkId));
             return false;
        }
        break;  
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_PIONEER:      //対話ファイル素材集
        if($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER) {
             // 選択されている紐付先資材タイプとMovementの組み合わせが不正です。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2046",array($RepoId,$MatlLinkId));
             return false;
        }
        break;  
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_ROLE:         //ロールパッケージ管理
        if($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE) {
             // 選択されている紐付先資材タイプとMovementの組み合わせが不正です。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2046",array($RepoId,$MatlLinkId));
             return false;
        }
        break;  
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_CONTENT:      //ファイル管理
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_TEMPLATE:     //テンプレート管理
        if(($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_LEGACY)  &&
           ($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_PIONEER) &&
           ($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_ROLE)) {
             // 選択されている紐付先資材タイプとMovementの組み合わせが不正です。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2046",array($RepoId,$MatlLinkId));
             return false;
        }
        break;  
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_MODULE:       //Module素材
    case TD_B_CICD_MATERIAL_TYPE_NAME::C_MATL_TYPE_ROW_ID_POLICY:       //Policy管理
        if($ExtStnId != TD_C_PATTERN_PER_ORCH::C_EXT_STM_ID_TERRAFORM) {
             // 選択されている紐付先資材タイプとMovementの組み合わせが不正です。(リモートリポジトリ管理 項番:{} 資材紐付管理 項番:{})
             $retStrBody .= $objMTS->getSomeMessage("ITACICDFORIAC-ERR-2046",array($RepoId,$MatlLinkId));
             return false;
        }
        break;  
    }
    return true;
}
?>
