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

while read line
do

    SQL_FILE=`dirname ${0}`/../../ITA/ita-sqlscripts/${5}_${line}.sql
    SQL_REPLACE=`dirname "$SQL_FILE"`/replace_${5}_${line}.sql
    
    cp "$SQL_FILE" "$SQL_REPLACE"
    sed -i -e "s:%%%%%ITA_DIRECTORY%%%%%:${6}:g" ${SQL_REPLACE}

   if [ ${line} ]; then
        mysql --show-warnings -u"$2" -p"$3" "$4" < "$SQL_REPLACE" 1>`dirname ${0}`/../log/${line}.log 2>&1
    fi
    
    rm -rf ${SQL_REPLACE}
    
done < `dirname ${0}`/../list/"$1"

exit

