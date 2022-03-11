#!/usr/bin/env python3
#   Copyright 2022 NEC Corporation
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

import pprint
import hcl2
import json
import sys
if __name__=='__main__':
    filename = sys.argv[1]
    # php側でファイルの存在チェック
    try:
        with open(filename, 'r') as file:
            # tfファイルの文法エラー
            dict = hcl2.load(file)
            json_str = json.dumps(dict)

            res = json_str
            # dict型をjson化する
    except Exception as e:
        res = e

    print(res)
