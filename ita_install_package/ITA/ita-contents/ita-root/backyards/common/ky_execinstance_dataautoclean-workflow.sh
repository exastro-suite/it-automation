#!/bin/bash
#   Copyright 2019 NEC Corporation
#
#   Licensed under the Apache License, Version 2.0 (the "License");
#   you may not use this file except in compliance with the License.
#   You may obtain a copy of the License at
#
#       http://www.apache.org/licenses/LICENSE-2.0
#
#   Unless required by applicable law or agreed to in writing, software
#   distributed under the License is distributed on an "AS IS" BASIS,
#   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
#   See the License for the specific language governing permissions and
#   limitations under the License.
#
######################################################################
##
##  【概要】
##      ky_execinstance_dataautoclean-workflow.php
##      の起動スクリプト
##
######################################################################

#----------------------------------------------------#
# ルートディレクトリ取得
#----------------------------------------------------#
MYSELF_PATH=${0}
if [ -n "`readlink  ${MYSELF_PATH}`" ]
then
    MYSELF_PATH="`readlink -f ${MYSELF_PATH}`"
else
    MYSELF_PATH=`echo $(cd $(dirname $0);pwd)`
fi
ROOT_DIR_PATH=`dirname ${MYSELF_PATH} | awk -F'ita-root' '{print $1}'`'ita-root'

#----------------------------------------------------#
# 定数
#----------------------------------------------------#
PROG='ky_execinstance_dataautoclean-workflow'
PROCESS=${ROOT_DIR_PATH}'/backyards/common/ky_execinstance_dataautoclean-workflow.sh'
PHP_PROCEDURE=${ROOT_DIR_PATH}'/backyards/common/ky_execinstance_dataautoclean-workflow.php'
LOG_DIR=${ROOT_DIR_PATH}'/logs/backyardlogs'
PHP_MODULE=${ROOT_DIR_PATH}'/confs/backyardconfs/path_PHP_MODULE.txt'
DB_MODEL=${ROOT_DIR_PATH}'/confs/commonconfs/db_model_string.txt'
ORACLE_ENV_SH=${ROOT_DIR_PATH}'/confs/backyardconfs/path_ORACLE_ENV_SH.txt'
ITA_ENV=${ROOT_DIR_PATH}'/confs/backyardconfs/ita_env'

#----------------------------------------------------#
# パラメータ設定
#----------------------------------------------------#
# ログ出力レベル
LOG_LEVEL=`cat ${ITA_ENV} | grep "^ITA_LOG_LEVEL" | cut -d "=" -f 2`

#----------------------------------------------------#
# グローバル変数宣言
#----------------------------------------------------#
export LOG_LEVEL
export LOG_DIR

# 変数初期化
CHECK=0
    
if [ ! -x ${PROCESS} ]
then
    CHECK=1
    echo "Starting ${PROG}: ERROR (${PROCESS} NOT FOUND OR NO EXECUTE PERMISSION)"
fi
    
if [ ${CHECK} -eq 0 ]
then
    if [ ! -f ${PHP_MODULE} ]
    then
        CHECK=2
        echo "Starting ${PROG}: ERROR (PHP_MODULE DEFINE FILE NOT FOUND)"
    elif [ ! -x `cat ${PHP_MODULE}` ]
    then
        CHECK=3
        echo "Starting ${PROG}: ERROR (PHP_MODULE NOT FOUND OR NO EXECUTE PERMISSION)"
    fi
fi
    
if [ ${CHECK} -eq 0 ]
then
    if [ ! -f ${PHP_PROCEDURE} ]
    then
        CHECK=4
        echo "Starting ${PROG}: ERROR (${PHP_PROCEDURE} NOT FOUND)"
    fi
fi
    
if [ ${CHECK} -eq 0 ]
then
    if [ ${LOG_LEVEL} != 'NORMAL' -a ${LOG_LEVEL} != 'DEBUG' ]
    then
        CHECK=5
        echo "Starting ${PROG}: ERROR (LOG_LEVEL STATEMENT ILLEGAL)"
    fi
fi
    
if [ ${CHECK} -eq 0 ]
then
    if [ ! -d ${LOG_DIR} ]
    then
        CHECK=6
        echo "Starting ${PROG}: ERROR (LOG_DIR NOT FOUND OR NOT DIRECTORY)"
    fi
fi
    
if [ ${CHECK} -eq 0 ]
then
    if [ ! -f ${DB_MODEL} ]
    then
        CHECK=7
        echo "Starting ${PROG}: ERROR (DB MODEL DEFINE FILE NOT FOUND)"
    elif [ `cat ${DB_MODEL}` = "0" ]
    then
        if [ ! -f ${ORACLE_ENV_SH} ]
        then
            CHECK=8
            echo "Starting ${PROG}: ERROR (ORACLE_ENV_SH DEFINE FILE NOT FOUND)"
        elif [ ! -x `cat ${ORACLE_ENV_SH}` ]
        then
            CHECK=9
            echo "Starting ${PROG}: ERROR (ORACLE_ENV_SH NOT FOUND OR NO EXECUTE PERMISSION)"
        else
            # execute oracle_env.sh
            . `cat ${ORACLE_ENV_SH}`
            CHECK=0
        fi
    elif [ `cat ${DB_MODEL}` = "1" ]
    then
        CHECK=0
    else
        CHECK=10
        echo "Starting ${PROG}: ERROR (DB MODEL DEFINITION INCORRECT)"
    fi
fi
    
if [ ${CHECK} -eq 0 ]
then
    # プロセス実行
    `cat ${PHP_MODULE}` ${PHP_PROCEDURE} $1 2>&1 &
        
    # 3秒間のインターバル
    sleep 3
        
    # プロセス確認
    if [ `ps -ef | grep ${PHP_PROCEDURE} | grep -v grep | wc -l` -eq 0 ]
    then
        # メッセージ出力
        echo "Starting ${PROG}: [ NG ]"
            
        # 異常終了
        exit 1
        else
        # メッセージ出力
        echo "Starting ${PROG}: [ OK ]"
            
        # 正常終了
        exit 0
    fi
fi
# 異常終了
exit 1
