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

INPUT_TEXT=${1}
ITA_DIRECTORY=${2}

while read line
do
    cp -p ${ITA_DIRECTORY}/${line}.service /usr/lib/systemd/system/.
done < ${INPUT_TEXT}

systemctl daemon-reload

while read line
do
    if [ `basename ${line}` ]; then
        systemctl enable `basename ${line}`.service
        systemctl start `basename ${line}`.service
    fi
done < ${INPUT_TEXT}

exit