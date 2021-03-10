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
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・monitor_execution/05_disp_taillog.php、から呼ばれるファイル
//
//////////////////////////////////////////////////////////////////////

// グローバル変数宣言
var timer = null;

//////// 画面生成時に初回実行する処理 ////////
window.onload = function(){

let runFlag = false;

const observerFunction = function() {
        
  // 監視を終了する
  // observer.disconnect();

  const g_objjsonLogFileList = window.parent.document.getElementById("LogFileList").innerHTML;
  const g_objMultipleLog = window.parent.document.getElementById("MultipleLog").innerHTML;
  const g_objLogfileSelectionArea = window.parent.document.getElementById("LogfileSelectionArea");
  const g_objLogSelection = window.parent.document.getElementById("LogSelection");

  if(g_objMultipleLog != 'on') {
       g_objLogfileSelectionArea.style.display ="none";
  } else {
       g_objLogfileSelectionArea.style.display ="block";
       const Last_idx = g_objLogSelection.length;

       if(g_objjsonLogFileList != "") {
           const g_objLogFileList = JSON.parse(g_objjsonLogFileList);
           for (let idx = 0; idx < g_objLogFileList.length; idx++) {
               if(Last_idx > (idx + 1)) {
                   continue;
               }
               if (g_objLogFileList[idx] != null) {
                   let op = document.createElement("option");
                   op.value = g_objLogFileList[idx];  //value値
                   op.text = g_objLogFileList[idx];   //テキスト値
                   g_objLogSelection.appendChild(op);
               }
           }
       }
  }

  // 一度だけはrunさせる
  if ( runFlag === false ) {
    runFlag = true;
    run();
  }

};

const target = window.parent.document.getElementById("disp_execution_area"),
      observer = new MutationObserver( observerFunction ),
      config = { childList: true };

// 対象がすでに表示済みなら実行する
if ( target.innerHTML !== '') {
    observerFunction();
    start();
}

// 対象を監視し変更があったら実行する
observer.observe( target, config );

};


// tail開始ファンクション
function start(){
    // 最新化のインターバルタイム(msec)を取得
    var interval = document.getElementById('interval').innerHTML;
    
    if (timer){
        clearInterval(timer);
    }
    timer = setInterval( run, interval);
    
}

// tail実行ファンクション
function run(){
    // exec_flag定義
    // ※true：実行、false：実行せず
    var exec_flag = true;
    
    // クエリから作業№を取得
    var execution_no = getQuerystring("execution_no");
    
    // 作業№が取得できない場合
    if ( execution_no.length <= 0 ){
        // 実行しない
        exec_flag = false;
    }
    // 作業№が取得できる場合
    else{
        // 数値でない場合
        if( !execution_no.match( /^[-]?[0-9]+(\.[0-9]+)?$/ ) ){
            // 実行しない
            exec_flag = false;
        }
    }
    
    // クエリから進行記録ファイル名を取得
    var prg_record_file_name = getQuerystring("prg_recorder");
    
    // 進行記録ファイル名が取得できない場合
    if ( prg_record_file_name.length <= 0 ){
        // 実行しない
        exec_flag = false;
    }

    if( exec_flag == true ){
        var send_exec_status_id = document.getElementById('send_exec_status_id').innerHTML;
        if(prg_record_file_name == 1) {
            elem_name ='exec_log_get_status';
        } else {
            elem_name ='error_log_get_status';
        }
        // ログ取得ステータスを取得
        var log_status_id = document.getElementById(elem_name).innerHTML;

        // ステータスが完了(5)、完了(異常)(6)、想定外エラー(7)、緊急停止(8)、予約取消(10)の場合
        if( send_exec_status_id == 5 ||
            send_exec_status_id == 6 ||
            send_exec_status_id == 7 ||
            send_exec_status_id == 8 ||
            send_exec_status_id == 10 ){
               // ステータスが実行中以降でログ取得が出来ている場合にタイマ停止
               if(log_status_id == 'on') {
                   // 以降は実行しない
                   stop();
               }
        }
    }

    // exec_flagがtrueの場合
    if( exec_flag == true ){
        // フィルタ文字列を取得(URLエンコードも実施)
        var filter_string = document.getElementById('filter_string').value;
        filter_string = encodeURIComponent(filter_string);
        
        // 対象オブジェクト定義
        var obj = $('#console');
        
        // フィルタ種類の判別
        var qv_match_line_only = "off";
        var match_line_only = document.getElementById('match_line_only').checked;
        if( match_line_only === true ){
            qv_match_line_only = "on";
        }

        let g_objLogSelection = window.parent.document.getElementById("LogSelection");
        const num = g_objLogSelection.selectedIndex;
        const SelectedFile = g_objLogSelection.options[num].value;

        var objTailStop = document.getElementById('stop_update');
        if( objTailStop.innerHTML!='abort' ){
            // tail実行
            obj.load('?no=2100020313&SelectedFile='+SelectedFile+'&execution_no='+execution_no+'&prg_recorder='+prg_record_file_name+'&load=1'+'&match_line_only='+qv_match_line_only+'&filter_string='+filter_string
                     ,null
                     ,function(response, status, xhr){
                           // 未実行やerrorなどのケースでステータスが未表示の場合がある。
                           // ステータスが取得できない場合があるので実際のステータスを設定
                           for (  var i = 0;  i < 10;  i++  ) {
                              obj = window.parent.document.getElementById('status_id');
                              if(obj != null) {
                                  var status_id = obj.innerHTML;
                                  break;
                              }
                           }

                           // ステータスを取得
                           var stridx = response.search(/[0-9]+<\/div>$/);
                           if(stridx != -1) {
                              var status_html = response.substr(stridx);
                              var status_id = status_html.replace(/<\/div>/g, '');
                              var numstr = status_id.search(/^[0-9]+$/);
                              if(numstr == -1) {
                                 // ステータスが取得できなかった場合
                                 console.log("Faild to get execute status.(execution no %s)",execution_no);
                                 console.log(response);
                              }
                           }

                           // ステータスを更新
                           var send_exec_status_id = document.getElementById('send_exec_status_id');
                           send_exec_status_id.innerHTML = status_id;

                           // 状態が完了(5)、完了(異常)(6)、想定外エラー(7)、緊急停止(8)、予約取消(10)の場合
                           if( status_id == 5 ||
                               status_id == 6 ||
                               status_id == 7 ||
                               status_id == 8 ||
                               status_id == 10 ){
                               if(prg_record_file_name == 1) {
                                   elem_name ='exec_log_get_status';
                               } else {
                                   elem_name ='error_log_get_status';
                               }
                               // ログ取得ステータスを更新
                               var log_status_id = document.getElementById(elem_name);
                               log_status_id.innerHTML = 'on';
                          }
                          if( response.indexOf('tail_show') == -1 ){
                              //----以降、アクセスをしないフラグを立てる
                              var objTailStop = document.getElementById('stop_update');
                              objTailStop.innerHTML = 'abort';
                              //以降、アクセスをしないフラグを立てる----
                              
                              var objRedirectFlag = document.getElementById('redirectAgent');
                              if( objRedirectFlag != null ){
                                  //----01_browseのリダイレクト受付フラグを確認する
                                  redirectToByRedirectAgentForm(window.parent.parent.document,document);
                                  //01_browseのリダイレクト受付フラグを確認する----
                              }
                          }
                          // スクロールを一番下にする
                          scrollLocationAdjust();
                      }
                    );
        }
        // スクロールを一番下にする
    }
}

function scrollLocationAdjust(){
    // 対象オブジェクト定義
    var obj = $('#console');
    if( obj.length != 0 ){
        if( document.getElementById('before_height') != null ){
            if( document.getElementById('before_height').innerHTML != obj.get(0).scrollHeight ){
                obj.scrollTop(obj.get(0).scrollHeight);
            }
        }
        
    }
    
    // 高さをメモ
    if( document.getElementById('before_height') != null ){
        document.getElementById('before_height').innerHTML = obj.get(0).scrollHeight;
    }
}

// tail終了ファンクション
function stop(){
    if (timer){
        clearInterval(timer);
    }
}

// pre_filterファンクション
function pre_filter(kcode){
    // キーコード判定(Enterキーの場合)
    if( kcode == 13 ){
        // フィルタ文字列を取得(URLエンコードも実施)
        if( document.getElementById('filter_string') != null ){
            var send_exec_status_id = document.getElementById('send_exec_status_id').innerHTML;
            // ifreamの「send_exec_status_id」が完了(5)、完了(異常)(6)、想定外エラー(7)、緊急停止(8)、予約取消(10)の場合
            if( send_exec_status_id == 5 ||
                send_exec_status_id == 6 ||
                send_exec_status_id == 7 ||
                send_exec_status_id == 8 ||
                send_exec_status_id == 10 ){
                // tailを単発実行
                run();
            }
        }
    }
}
