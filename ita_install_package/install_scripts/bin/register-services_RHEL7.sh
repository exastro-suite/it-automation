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

input_text=${1}

while read line
do
    cp -p `dirname ${0}`/../../ITA/ita-contents/${line}.service /usr/lib/systemd/system/.
done < ${input_text}

systemctl daemon-reload

while read line
do
    if [ `basename ${line}` ]; then
        systemctl enable `basename ${line}`.service
        systemctl start `basename ${line}`.service
    fi
done < ${input_text}

exit