#!/bin/bash
#   Copyright 2021 NEC Corporation
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
##      ansible_vaultコマンドで指定ファイルを暗号化するbash
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
<<ansible_valut_command>>
RET_CODE=$?
exit ${RET_CODE}
