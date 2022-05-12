#!/usr/bin/expect
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
##      リモートホストのファイル削除
##
##  【特記事項】
##      <<引数>>
##      sh ky_ansible_materials_delete.sh %0
##      <<Tower接続情報ファイル>>
##          リモートホスト\t認証方式\tユーザー名\tパスワード\t鍵認証ファイル\t削除ファイルパス\t鍵認証ファイルパスフレーズ\tita-rootパス\t
##      <<環境変数>>
##       HOST:            リモートホスト
##       TYPE:            認証方式
##                          パスワード認証: pass
##                          鍵認証:         key
##                          パスワード省略: none
##       USER:            ユーザー名
##       PASSWD:          パスワード
##       KEY_FILE_PATH:   鍵認証ファイルパス
##       DEST_PATH:       削除ファイルパス
##       RAS_FILE_PASSWD: 鍵認証ファイル パスフレーズ
##                          パスフレーズなし: undefine
##       BASE_DIR:        ita-rootパス
##      <<exit code>>
##       0:   正常
##       他:　異常
##
######################################################################
FILE=$1
IFS="$(echo -e '\t' )"
while read LINE
do
    LINE=($LINE)
    export HOST=${LINE[0]}
    export TYPE=${LINE[1]}
    export USER=${LINE[2]}
    export PASSWD=${LINE[3]}
    export KEY_FILE_PATH=${LINE[4]}
    export DEST_PATH=${LINE[5]}
    export RAS_FILE=${KEY_FILE_PATH}
    export RAS_FILE_PASSWD=${LINE[6]}
    export BASE_DIR=${LINE[7]}
    break
done < ${FILE}
## 鍵認証ファイル パスフレーズが設定されている場合にssh-agentにパスフレーズ登録
if [ "${RAS_FILE_PASSWD}" != "undefine" ]; then
  eval `ssh-agent` 1>/dev/null
  if [ $? -ne 0 ]; then
    echo "failed to startup ssh-agent." >> /dev/stderr
    exit 100
  fi
  # expect
  expect ${BASE_DIR}/backyards/ansible_driver/ky_ansible_ssh_add.exp 1>/dev/null 2>>/dev/stderr
  EXIT_CODE=$?
  if [ ${EXIT_CODE}  -ne 0 ]; then
    # passphrase不正
    echo "Failed to set the passphrase of the private key file(id_ras) in ssh-agent." >> /dev/stderr
    if [ ${EXIT_CODE}  -eq 200 ]; then
      echo "Bad passphrase, Please check the Ansible Automation Controller host list. (host:${HOST})" >> /dev/stderr
    # passphrase不要
    elif [ ${EXIT_CODE}  -eq 201 ]; then
      echo "No passphrase requireds, Please check the Ansible Automation Controller host list. (host:${HOST})" >> /dev/stderr
    else
      echo "Bad private key file(id_ras) or passphrase, Please check the Ansible Automation Controller host list. (host:${HOST})" >> /dev/stderr
    fi
    eval `ssh-agent -k` 1>/dev/null
    exit 100
  fi
fi
expect ${BASE_DIR}/backyards/ansible_driver/ky_ansible_materials_delete.exp
RET_CODE=$?
if [ "${RAS_FILE_PASSWD}" != "undefine" ]; then
  eval `ssh-agent -k` 1>/dev/null
fi
exit ${RET_CODE}
