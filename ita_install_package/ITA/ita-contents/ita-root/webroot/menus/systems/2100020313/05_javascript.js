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
    // 一度だけはrunさせる
    run();
}

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
        // 親ウィンドウの「result_status_id」が完了(5)、完了(異常)(6)、想定外エラー(7)、緊急停止(8)、予約取消(10)の場合
        if( window.parent.result_status_id == 5 ||
            window.parent.result_status_id == 6 ||
            window.parent.result_status_id == 7 ||
            window.parent.result_status_id == 8 ||
            window.parent.result_status_id == 10 ){
            // 以降は実行しない
            stop();
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
        
        var objTailStop = document.getElementById('stop_update');
        if( objTailStop.innerHTML!='abort' ){
            // tail実行
            obj.load('?no=2100020313&execution_no='+execution_no+'&prg_recorder='+prg_record_file_name+'&load=1'+'&match_line_only='+qv_match_line_only+'&filter_string='+filter_string
                     ,null
                     ,function(response, status, xhr){
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
            // 親ウィンドウの「result_status_id」が完了(5)、完了(異常)(6)、想定外エラー(7)、緊急停止(8)、予約取消(10)の場合
            if( window.parent.result_status_id == 5 ||
                window.parent.result_status_id == 6 ||
                window.parent.result_status_id == 7 ||
                window.parent.result_status_id == 8 ||
                window.parent.result_status_id == 10 ){
                // tailを単発実行
                run();
            }
        }
    }
}
