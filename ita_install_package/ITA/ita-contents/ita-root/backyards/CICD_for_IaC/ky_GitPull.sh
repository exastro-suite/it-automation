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
TYPE=$1
CLONE_REPO=$2
BRANCH=$3
REMOTE_USER=$4
REMOTE_PASSWORD=$5

if [ $BRANCH == "__undefine_branch__"  ]; then
   BRANCH=""
fi

cd $CLONE_REPO

git checkout $BRANCH
STATUS=$?
if [ $STATUS -ne 0 ]; then
    echo "git checkout $BRANCH failed."
    exit $STATUS
fi

CMD="git pull"

if [ "${TYPE}" = "pass" ]; then
    expect -c "
    set timeout 5 
    spawn $CMD
    expect {
        \"Cloning into\" {
            exp_continue
        } \"Username for \" {
            send \"${REMOTE_USER}\n\"
            exp_continue
        } \"Password for \" {
            send \"${REMOTE_PASSWORD}\n\"
            exp_continue
        } \"remote: \" {
            set timeout -1
            exp_continue
        } timeout {
            exit 200
        } eof {
            catch wait result
    
            set OS_ERROR [ lindex \$result 2 ]
            if { \$OS_ERROR == -1 } {
                exit 255
            }
            set STATUS [ lindex \$result 3 ]
            exit \$STATUS
    
        } default {
            catch wait result
    
            set OS_ERROR [ lindex \$result 2 ]
            if { \$OS_ERROR == -1 } {
                exit 255
            }
            set STATUS [ lindex \$result 3 ]
            exit \$STATUS
    
        }
    }"
else
    expect -c "
    set timeout 5 
    spawn $CMD
    expect {
        \"Cloning into\" {
            exp_continue
        } \"Username for \" {
            exit 201
        } \"Password for \" {
            exit 202
        } \"remote: \" {
            set timeout -1
            exp_continue
        } timeout {
            exit 200
        } eof {
            catch wait result
    
            set OS_ERROR [ lindex \$result 2 ]
            if { \$OS_ERROR == -1 } {
                exit 255
            }
            set STATUS [ lindex \$result 3 ]
            exit \$STATUS
    
        } default {
            catch wait result
    
            set OS_ERROR [ lindex \$result 2 ]
            if { \$OS_ERROR == -1 } {
                exit 255
            }
            set STATUS [ lindex \$result 3 ]
            exit \$STATUS
    
        }
    }"
fi
STATUS=$?
exit $STATUS

