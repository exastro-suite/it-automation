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
###########################################################
#
#【概要】
# Pioneer版REST APIで起動したpioneerモジュールを停止する。
# 起動方法
#   ky_pionner_kill_side_Ansible.sh $1 $2
#   $1:親プロセスID
#   $2:OUTディレクトリ
#
###########################################################
killpstree(){
    # pioneerモジュールだけkillする。
    files="$2/*"
    fileary=()
    for filepath in $files; do
      if [ -f $filepath ] ; then
        fileary+=("$filepath")
      fi
    done
    for tgtfile in ${fileary[@]}; do
      echo $tgtfile
      if [[ $tgtfile =~ pioneer.[0-9*] ]] ; then
        PID=`cat $tgtfile`
        TGTPID=`ps $PID |grep pioneer_module.py|grep -v grep|wc -l`
        if [ $TGTPID -ne 0 ]; then
          kill -s 15 $PID
        fi
      fi
    done
}
while :
do
    # 実行ログを残す為に、pioneerモジュールだけkillする。
    # なので、並列実行数により実行待ちとなっているホストがある場合があり、
    # 親プロセスが停止するまで子プロセスを監視する。 
    MYPID=`ps $1|grep $1|grep -v grep|wc -l`
    if [ $MYPID -ne 0 ]; then
        killpstree $1 $2
    else
        break
    fi 
    sleep 1
done
exit 0
