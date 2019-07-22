//   Copyright 2019 NEC Corporation
//
//   Licensed under the Apache License, Version 2.0 (the "License");
//   you may not use this file except in compliance with the License.
//   You may obtain a copy of the License at
//
//       http://www.apache.org/licenses/LICENSE-2.0
//
//   Unless required by applicable law or agreed to in writing, software
//   distributed under the License is distributed on an "AS IS" BASIS,
//   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//   See the License for the specific language governing permissions and
//   limitations under the License.
//
//////// 画面生成時に初回実行する処理 ////////
window.onload = function(){
    // 一覧を出力
    search_async();
}

//////// コールバックファンクション ////////
function callback() {}
    callback.prototype = {  printTable : function(result){
                                var ary_result = getArrayBySafeSeparator(result);
                                checkTypicalFlagInHADACResult(ary_result);
                                document.getElementById('table_area').innerHTML = result;
                                adjustTableAuto ( "DbTable",
                                               "sDefault",
                                               "fakeContainer_Table",
                                               600,
                                               900);

                            }
                         }
var proxy = new Db_Access(new callback());

//////// 一覧表示用ファンクション ////////
function search_async(){
    // しばらくお待ち下さいを出す
    document.getElementById('table_area').innerHTML = "<div id=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";
    
    //proxy.printTable実行
    proxy.printTable();
}
