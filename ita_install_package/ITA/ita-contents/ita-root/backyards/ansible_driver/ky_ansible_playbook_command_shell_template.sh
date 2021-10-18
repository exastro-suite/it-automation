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
##      ssh-agentに秘密鍵ファイルのパスフレーズを登録し
##      ansible-playbookコマンドでplaybookを実行するbashの
##      テンプレートファイル
##
##  【特記事項】
##      <<引数>>
##       $ virtualenv path
##      <<exit code>>
##       0:   正常
##       他:　異常
##
######################################################################
# virtualenv pathの有無判定
if [ $1 != "__undefine__" ]; then
    # virtualenvに切り替え
    source <<virtualenv_path>> 2>> /dev/stderr
    EXIT_CODE=$?
    if [ ${EXIT_CODE} -ne 0 ]; then
        echo "$1/bin/activate command failed." >> /dev/stderr
    fi
fi
# 秘密鍵ファイルとパスフレーズが格納されているファイル
FILE='<<sshAgentConfigFile>>'
# ログファイル
TMP_LOGFILE='<<logFile>>'
SSHAGENT_EXEC='<<sshAgentExec>>'
if [ "${SSHAGENT_EXEC}" = "RUN" ]; then
  eval `ssh-agent` 1>/dev/null
  if [ $? -ne 0 ]; then
    echo "failed to startup ssh-agent." >> /dev/stderr
    exit 100
  fi
  ## ssh-agent PIDを退避 呼び元で、このPIDを元にssh-agentをkillする。
  IFS="$(echo -e '\t' )"
  while read LINE
  do
    LINE=($LINE)
    export HOST=${LINE[0]}
    export RAS_FILE=${LINE[1]}
    export RAS_FILE_PASSWD=${LINE[2]}
    # expect
    expect <<ssh_add_script_path>> 1>/dev/null 2>>/dev/stderr
    EXIT_CODE=$?
    if [ ${EXIT_CODE}  -ne 0 ]; then
      # passphrase不正
      echo "Failed to set the passphrase of the private key file(id_ras) in ssh-agent." >> /dev/stderr
      if [ ${EXIT_CODE}  -eq 200 ]; then
          echo "Bad passphrase, Please check the device list. (host:${HOST})" >> /dev/stderr
      # passphrase不要
      elif [ ${EXIT_CODE}  -eq 201 ]; then
          echo "No passphrase requireds, Please check the device list. (host:${HOST})" >> /dev/stderr
      else
          echo "Bad private key file(id_ras) or passphrase, Please check the device list. (host:${HOST})" >> /dev/stderr
      fi
      eval `ssh-agent -k` 1>/dev/null
      exit 100
    fi
  done < ${FILE}
fi

# roleでansible.cfgを有効にする為にinをカレントディレクトリにしてAnsible実行するshellを作成
cd <<in_directory_path>>
#/usr/local/bin/ansible-playbook -vvv  -i /exastro/data_relay_storage/ansible_driver/legacy/ns/0000000096/in/hosts  --vault-password-file /exastro/data_relay_storage/ansible_driver/legacy/ns/0000000096/.tmp/.tmpkey /exastro/data_relay_storage/ansible_driver/legacy/ns/0000000096/in/playbook.yml
<<ansible_playbook_command>>
RET_CODE=$?
if [ "${SSHAGENT_EXEC}" = "RUN" ]; then
  eval `ssh-agent -k` 1>/dev/null
fi
exit ${RET_CODE}
