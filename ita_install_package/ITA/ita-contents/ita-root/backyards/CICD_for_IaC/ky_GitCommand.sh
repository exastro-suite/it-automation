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
CLONE_REPO=$1
COMMAND=$2
BRANCH=$3

if [ $BRANCH == "__undefine_branch__"  ]; then
   BRANCH=""
fi

cd $CLONE_REPO

git checkout $BRANCH
STATUS=$?
if [ $STATUS -ne 0 ]; then
   echo "git checkout "$BRANCH" failed. ("$STATUS")"
   exit 200
fi
git $COMMAND
STATUS=$?
exit $STATUS
