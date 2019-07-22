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
//      テーブル定義 基底クラス
//
//////////////////////////////////////////////////////////////////////

abstract class TableBaseDefinition {

    public static abstract function getTableName();

    public static function getJnlTableName() {
        return static::getTableName() . "_JNL";
    }

    public static function getSequenceName() {
        return static::getTableName() . "_RIC";
    }

    public static function getJnlSequenceName() {
        return static::getTableName() . "_JSQ";
    }

    protected static $specificColumns = array();

    public static function getColumnSettings() {

        $resultArray = array_merge(
            static::$specificColumns,
            array(
                "DISP_SEQ"                  => "",
                "NOTE"                      => "",
                "DISUSE_FLAG"               => "",
                "LAST_UPDATE_TIMESTAMP"     => "DATETIME",
                "LAST_UPDATE_USER"          => "",
            )
        );

        return $resultArray;
    }

    public static function getColumnSettingsWithJNL() {

        $resultArray = array_merge(
            array(
                "JOURNAL_SEQ_NO"            => "",
                "JOURNAL_REG_DATETIME"      => "DATETIME",
                "JOURNAL_ACTION_CLASS"      => "",
            ),
            static::getColumnSettings()
        );

        return $resultArray;
    }

    public static function getColumns() {

        $resultArray = array();
        foreach(static::getColumnSettings() as $key => $value) {
            $resultArray[$key] = "";
        }
        return $resultArray;
    }

    public static function getColumnsWithJNL() {

        $resultArray = array();
        foreach(static::getColumnSettingsWithJNL() as $key => $value) {
            $resultArray[$key] = "";
        }
        return $resultArray;
    }

    public static abstract function getRowDiffKeyColumns();

    public static abstract function getPKColumnName();
}

?>
