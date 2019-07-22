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
 * 【概要】
 *    ExternalAuthSettings ファイル定義クラス
 */

////////////////////////////////
// ルートディレクトリを取得   //
////////////////////////////////
if(empty($root_dir_path)) {
    $root_dir_temp = array();
    $root_dir_temp = explode( "ita-root", dirname(__FILE__));
    $root_dir_path = $root_dir_temp[0] . "ita-root";
}
require_once($root_dir_path . "/libs/commonlibs/common_external_auth.php");

class ExternalAuthSettingsDefinition {

    public static $FILE_NAME = "ExternalAuthSettings.ini";

    public static function getFileName() {
        return self::$FILE_NAME;
    }

    public static function getFilePath() {

        global $root_dir_path;

        return $root_dir_path . "/confs/webconfs/" . self::$FILE_NAME;
    }

    public static function parse($filePath, &$errorMessage) {

        // 外部認証設定パース
        $aryExternalAuthSettings = parse_ini_file($filePath, true);
        if($aryExternalAuthSettings === false) {
            $errorMessage = "Error: Unexpected, Detail: ExternalAuthSettings parse failed.";
            return false;
        }

        // パースしたデータのキーチェック
        $innerErrorMessage = "";
        $ret = self::validateSettingsData($aryExternalAuthSettings, $innerErrorMessage);
        if($ret === false) {
            // 外部認証設定記述エラー
            $errorMessage = $innerErrorMessage;
            return false;
        }

        return $aryExternalAuthSettings;
    }

    private static function validateSettingsData(&$aryExternalAuthSettings, &$validationErrorMessage) {

        // DomainControllerだけ特殊
        $domainController_sectionNames = array(
            "DomainController_1",
            "DomainController_2",
            "DomainController_3",
        );

        $domainController_checkKey = array(
            "host" => "is_string",
            "port" => "integerNumericValidator",
            "reconnection_count" => "integerNumericValidator",
            "connect_timelimit" => "integerNumericValidator",
            "search_timelimit" => "integerNumericValidator",
            "basedn" => "is_string",
        );

        $domainControllers = array();
        $tmpMessage = "";
        foreach($domainController_sectionNames as $dcSectionName) {

            if(array_key_exists($dcSectionName, $aryExternalAuthSettings)) {
                // Sectionが存在した場合...
                $dc = $aryExternalAuthSettings[$dcSectionName];

                // 内部設定値チェック
                $keyCheckErrorMessage = "";
                $ret = self::keyValueCheckInSection($dc, $domainController_checkKey, $keyCheckErrorMessage);
                if($ret === true) {
                    $dc['domain'] = self::rephraseBaseDn($dc['basedn']);
                    $domainControllers[] = $dc;
                } else {
                    $tmpMessage .= "\n'" . $dcSectionName . "' was ignored. Cause: " . $keyCheckErrorMessage;
                }
            }
        }

        if(count($domainControllers) === 0){
            $validationErrorMessage = "Error: Not found the required section. Required any one of the following sections. [DomainController_1] or [DomainController_2] or [DomainController_3]"
                . (empty($tmpMessage) ? "" : $tmpMessage);
            return false;
        }

        // チェックを通ったドメインコントローラを再格納する
        $aryExternalAuthSettings['targetDomainControllers'] = $domainControllers;

        // Other Sections Title
        $required_sections = array(
            // 認証方式の定義
            "Authentication_method" => array("AuthMode" => "integerNumericValidator"), // 0/1判定する?
            // レプリケーション用のユーザー認証情報および探索開始ノード（basedn）の定義
            "Replication_Connect"   => array("ConnectionUser" => "is_string",
                                             "UserPassword" => "is_string",
                                             "basedn" => "is_string",),
            // スペシャルとは別にITAローカルを優先するローカル・ユーザーID情報（同期対象外ユーザー）
            "LocalAuthUserId"       => array("IdList" => "is_string"),
            // スペシャルとは別にITAローカルを優先するローカル・ロールID情報（同期対象外ロール）
            "LocalRoleId"           => array("IdList" => "is_string"),
        );

        $validationResult = true;
        $tmpMessage = "";
        foreach($required_sections as $sectionName => $checkKey) {
            if(array_key_exists($sectionName, $aryExternalAuthSettings)) {

                $section = $aryExternalAuthSettings[$sectionName];

                // 内部設定値チェック
                $keyCheckErrorMessage = "";
                $ret = self::keyValueCheckInSection($section, $checkKey, $keyCheckErrorMessage);
                if($ret === false) {
                    $tmpMessage .= "\n'" . $sectionName . "' has error. " . $keyCheckErrorMessage;
                    $validationResult = false;
                }
            } else {
                $tmpMessage .= "\nNot found the required section. [SectionName: " . $sectionName . "]";
                $validationResult = false;
            }
        }

        if($validationResult === false) {
            $validationErrorMessage = "Error: There are invalid sections." . $tmpMessage;
            return false;
        }
    }

    /* ExternalAuthSettingファイル読み込んだドメインコントローラー情報の値をチェックする */
    private static function keyValueCheckInSection($section, $checkKey, &$keyCheckErrorMessage) {

        foreach($checkKey as $keyName => $validator) {

            if(array_key_exists($keyName, $section) === false){
                $keyCheckErrorMessage = "Error: Not found the required key. [" . $keyName . "]";
                return false;
            }

            $checkKeyValue = $section[$keyName];

            /* ※以下条件に 「$keyName != 'IdList'」があるのは、特別ユーザーおよび特別ロールの空値を許容する為 */
            if(empty($checkKeyValue) === true && $keyName != 'IdList') {
                $keyCheckErrorMessage = "Error: Empty value of the required item. [" . $keyName . "]";
                return false;
            }

            if($validator($checkKeyValue) === false) {
                $keyCheckErrorMessage = "Error: Invalid value. KeyName: [" . $keyName . "] Value: [" . $checkKeyValue . "]";
                return false;
            }
        }

        return true;
    }

    /* basednの記述からドメイン名を作成する */
    private static function rephraseBaseDn($basedn) {

        $resultDomain = "";

        $relativeDNs = explode(",", $basedn);
        foreach($relativeDNs as $dn) {
            $dn = trim($dn);
            if(stripos($dn, "dc=") === 0) {
                $resultDomain .= (empty($resultDomain)) ? "" : ".";
                $resultDomain .= str_ireplace("dc=", "", $dn);
            }
        }

        return $resultDomain;
    }
}
