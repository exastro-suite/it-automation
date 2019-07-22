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
##      pioneer ansibleモジュール デフォルト文字列検索
##
##  【特記事項】
##      <<引数>>
##       $1    :検索ファイル
##       $2～  :検索文字列(複数指定可能)
##       
##      <<返却値>>
##       0      検索文字列あり
##       1      検索文字列なし
##
######################################################################
STDOUT='/tmp/ita_stdout.'$$
STDERR='/tmp/ita_stderr.'$$
SVSTDERR='/tmp/ita_stderr'
GREPCMD='/tmp/ita_grepcommand'
/bin/rm -f $GREPCMD
# 引数からgrepコマンドを生成
grep_cmd=''
for idx in `seq 2 ${#}`
do
   if [ $idx -eq 2 ]; then
      #先頭の検索文字列の場合にgrepコマンドを生成
      grep_cmd='grep '${2}' '$1
      echo "{{"$grep_cmd"}}\n" >> $GREPCMD
   else
      #先頭以降の検索文字列の場合にパイプでgrepコマンドを結合
      grep_cmd=$grep_cmd' |grep '${2}
      echo "{{"$grep_cmd"}}" >> $GREPCMD
   fi
   shift
done
grep_cmd=${grep_cmd}' | wc -l >'${STDOUT}' 2>'${STDERR}
echo "{{"${grep_cmd}"}}" > $GREPCMD
eval ${grep_cmd}
RET=$?
# grepコマンドが実行出来なかった場合
if [ $RET -ne 0 ]; then
    /bin/cp -fp ${STDERR} ${SVSTDERR}
    EXIT_CODE=$RET
else
    # grepコマンドでエラーになった場合
    if [ -s ${STDERR} ]; then
        /bin/cp -fp ${STDERR} ${SVSTDERR}
        EXIT_CODE=1
    else
        # grepコマンドで検索された行数取得
        CNT=`cat ${STDOUT}`
        if [ ${CNT} -eq 0 ]; then
            # 0行の場合は異常終了
            EXIT_CODE=1
        else
            # 1行でもあれば正常終了
            EXIT_CODE=0
        fi
    fi
fi
/bin/rm -rf ${STDOUT} ${STDERR} >/dev/null 2&>1
exit ${EXIT_CODE}
