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
PROXYURL=$1
TYPE=$2
REMOTE_REPO=$3
CLONE_REPO=$4
BRANCH=$5
REMOTE_USER=$6
REMOTE_PASSWORD=$7
SSH_PASSWORD=$8
SSH_PASS_PASSPHRASE=$9
SSH_EXTRAARG=${10}

export GIT_SSH_COMMAND=${SSH_EXTRAARG}

if [ $PROXYURL != "__undefine__" ]; then
   export HTTP_PROXY="${PROXYURL}"
   export HTTPS_PROXY="${PROXYURL}"
fi

if [ $BRANCH = "__undefine_branch__"  ]; then
   BRANCH=""
else
   BRANCH="-b "$BRANCH
fi

CMD="git clone $REMOTE_REPO $CLONE_REPO  $BRANCH"
# ssh の接続がhttpより時間がかかる模様 set timeoutを10に統一
if [ "${TYPE}" = "httpUserAuth" ]; then
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
elif [ "${TYPE}" = "httpNoUserAuth" ]; then
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
elif [ "${TYPE}" = "sshPassAuth" ]; then
    expect -c "
    set timeout 5 
    spawn $CMD
    expect {
        \"Cloning into\" {
            exp_continue
        } \"continue connecting\" {
            send \"yes\n\"
            exp_continue
        } \"passphrase for key \" {
            exit 203
        } \"Username for \" {
            exit 201
        } \"password:\" {
            send \"${SSH_PASSWORD}\n\"
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
elif [ "${TYPE}" = "sshKeyAuthPass" ]; then
    expect -c "
    set timeout 10 
    spawn $CMD
    expect {
        \"Cloning into\" {
            exp_continue
        } \"passphrase for key \*:\" {
            send \"${SSH_PASS_PASSPHRASE}\n\"
            exp_continue
        } \"Username for \" {
            exit 201
        } \"password:\" {
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
elif [ "${TYPE}" = "sshKeyAuthNoPass" ]; then
    expect -c "
    set timeout 10 
    spawn $CMD
    expect {
        \"Cloning into\" {
            exp_continue
        } \"passphrase for key \" {
            exit 203
        } \"Username for \" {
            exit 201
        } \"password:\" {
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

