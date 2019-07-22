#!/bin/sh
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
#
######################################################################
##
##  【概要】
##      任意のシェルスクリプトを無限ループする
##
##
######################################################################

#----------------------------------------------------#
# パラメータ設定
#----------------------------------------------------#
LOCK=${1}
SH_SCRIPT=${2}
LOG_DIR=${3}
INTERVAL=${4}
LOG_LEVEL=${5}

#----------------------------------------------------#
# PROCESS名を取得
#----------------------------------------------------#
PROCESS_NAME=`basename ${0}`

#----------------------------------------------------#
# SH_SCRIPT名を取得
#----------------------------------------------------#
SH_SCRIPT_NAME=`basename ${SH_SCRIPT}`

#----------------------------------------------------#
# ログファイル名を作成
#----------------------------------------------------#
# スクリプトファイル名から拡張子を削除
LOG_NAME_PREFIX=${PROCESS_NAME%.*}

#----------------------------------------------------#
# グローバル変数宣言
#----------------------------------------------------#
export LOG_DIR
export LOG_NAME_PREFIX
export LOG_LEVEL
export PROCESS_NAME
export SH_SCRIPT_NAME

#----------------------------------------------------#
# ログ出力＆プロセス終了ファンクション
#
# [動作]
# 本ファンクションは2つの役割を持つ。
#   ・ログ出力
#   ・プロセス終了
# ログレベルと終了コードにより動作が確定される。
# [終了コード] [ログレベル] ⇒ [プロセス終了] [ログ出力]
#  -1           NORMAL      ⇒  ×             ×
#  -1           DEBUG       ⇒  ×             ○
#  -1以外       NORMAL      ⇒  ○             ○
#  -1以外       DEBUG       ⇒  ○             ○
#----------------------------------------------------#
function commonFunction
{
    # パラメータ格納
    P_RetCode=$1    # 終了コード
                    # ("-1"の場合は終了しない。またLOG_LEVELが'NORMAL'の場合はログを出さない)
    P_Message=$2    # メッセージ本文
    
    if [ ${P_RetCode} -ne -1 -o "${LOG_LEVEL}" = 'DEBUG' ]
    then
        # ログファイル名を作成
        LOG_NAME=${LOG_DIR}"/"${LOG_NAME_PREFIX}"_"`date '+%Y%m%d'`".log"
        
        # メッセージ出力
        MESSAGE="["`date '+%Y/%m/%d %H:%M:%S'`"][${PROCESS_NAME}][${SH_SCRIPT_NAME}][$$]${P_Message}"
        echo ${MESSAGE} >> ${LOG_NAME}
    fi
    
    if [ ${P_RetCode} -ne -1 ]
    then
        MESSAGE="["`date '+%Y/%m/%d %H:%M:%S'`"][${PROCESS_NAME}][${SH_SCRIPT_NAME}][$$]Process Abort(Error:[Line]${P_RetCode})"
        echo ${MESSAGE} >> ${LOG_NAME}
        exit ${P_RetCode}
    fi
}

#----------------------------------------------------#
# 開始メッセージ
#----------------------------------------------------#
commonFunction -1 "Process : Start"

#----------------------------------------------------#
# 多重起動防止
#----------------------------------------------------#
COMM="$0 $*"
if [ $$ != `pgrep -fo "${COMM}"` ]
then
    commonFunction ${LINENO} "Process : Multiple start-up prevention check NG"
fi
commonFunction -1 "Process : Multiple start-up prevention check OK"

#----------------------------------------------------#
# シェルスクリプト実行
#----------------------------------------------------#
error_flag=0
commonFunction -1 "Loop : Start"

while :
do
    # ロックファイル存在確認
    if [ ! -f ${LOCK} ]
    then
        commonFunction -1 "Loop : Break(Lock-file removed)"
        RET_CD=0
        break
    fi
    # シェルスクリプト実行
    commonFunction -1 "Shell-Script : Execute"
    ${SH_SCRIPT}
    RET_CD=$?
    if [ ${RET_CD} -ne 0 ]
    then
        commonFunction -1 "Shell-Script : Abort(Error：[code]${RET_CD})"
        error_flag=1
        break
    fi
    commonFunction -1 "Shell-Script : Result OK"
    
    # インターバル(sec)だけスリープ
    sleep ${INTERVAL}
done

if [ ${error_flag} -eq 0 ]
then
    commonFunction -1 "Loop : Finish"
    commonFunction -1 "Process : Finish"
    exit 0
else
    commonFunction ${LINENO} "Loop : Abort"
fi

