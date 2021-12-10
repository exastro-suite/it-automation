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
##      FileUploadClomunディレクトリのバックアップ
##
##  【特記事項】
##      <<引数>>
##      sh ky_ansible_FileUploadColumnBackup.sh $1 $2 $3
##        $1: FileUploadClomunディレクトリ ~\uploadcloumns/メニューID
##        $2: バックアップファイル名
##        $3: FileUploadClomunカラム名
#####################################################################
cd $1
RET_CODE=$?
if [ ${RET_CODE} -ne 0 ]; then
    echo "Failed to move to file upload column directory. (directory: $1)"
    exit ${RET_CODE}
fi
tar cvzfp $2 ./$3
RET_CODE=$?
if [ ${RET_CODE} -ne 0 ]; then
    echo "Failed to hack up the file upload column. (directory: $1)"
    exit ${RET_CODE}
fi
exit 0;
