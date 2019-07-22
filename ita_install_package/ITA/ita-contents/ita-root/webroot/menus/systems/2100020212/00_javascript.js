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
//////// グローバル変数定義 ////////
// 緊急停止実施フラグ
var scramed_frag = false;
// ステータス
var result_status_id;

//////// コールバックファンクション ////////
function callback() {}
    callback.prototype =
    {
        dispExecution : function(result){
            var ary_result = getArrayBySafeSeparator(result);
            checkTypicalFlagInHADACResult(ary_result);
            if ( result.match(/^unexpected_error/) ){
                // リダイレクト先URLを取得
                var redirect_url = result.substr(16);
                
                // 作業管理メニューにリダイレクト
                location.href=redirect_url;
            }
            else{
                document.getElementById('disp_execution_area').innerHTML = result;
                
                adjustTableAuto ( "DbTable_Yobi0",
                               "sDefault",
                               "fakeContainer_Yobi0",
                               600,
                               900);
                
                // ステータスIDを取得
                result_status_id = document.getElementById('status_id').innerHTML;
                
                // ステータスIDが完了(5)、完了(異常)(6)、想定外エラー(7)、緊急停止(8)、予約取消(10)の場合
                if( result_status_id == 5  ||
                    result_status_id == 6  ||
                    result_status_id == 7  ||
                    result_status_id == 8  ||
                    result_status_id == 10 ){
                    // ファンクション「disp_execution()」のループを停止
                    clearInterval(timerID);
                    
                    // 緊急停止ボタンを非活性にする
                    document.getElementById('scrumTryExecute').disabled = true;
                    
                    // ステータスIDが想定外エラー(7)でない場合
                    if( result_status_id != 7 ){
                        // 進行状況のtailをストップ
                        cntr=1;
                        while( document.getElementById("TailWindow_"+cntr) != null ){
                            document.getElementById("TailWindow_"+cntr).contentWindow.stop();
                            
                            cntr++;
                        }
                    }
                }
                // ステータスIDが実行中(3)、実行中(遅延)(4)の場合
                else if( result_status_id == 3 ||
                         result_status_id == 4 ){
                    if( scramed_frag == false ){
                        // ボタンを活性にする
                        document.getElementById('scrumTryExecute').disabled = false;
                    }
                }
                // ステータスIDが未実行(予約)(9)でない場合
                if( result_status_id != 9 ){
                    if( document.getElementById('book_cancel_button') != null ){
                        document.getElementById('book_cancel_button').disabled = true;
                    }
                }
            }
        },
        ScramExecution : function(result){
            var ary_result = getArrayBySafeSeparator(result);
            checkTypicalFlagInHADACResult(ary_result);
            if ( result.match(/^unexpected_error/) ){
                // エラーメッセージ表示
                //システムエラーが発生しました
                window.alert( getSomeMessage("ITAWDCC90101") );
            }
            else if ( result.match(/^warning/) ){
                // 警告メッセージを取得
                var message = result.substr(7);
                
                // 警告メッセージ表示
                window.alert( message );
            }
            else{
                // 正常メッセージ表示
                window.alert( result );
            }
        },
        BookCancel : function(result){
            var ary_result = getArrayBySafeSeparator(result);
            checkTypicalFlagInHADACResult(ary_result);
            if ( result.match(/^unexpected_error/) ){
                // エラーメッセージ表示
                //システムエラーが発生しました
                window.alert( getSomeMessage("ITAWDCC90101") );
            }
            else if ( result.match(/^warning/) ){
                // 警告メッセージを取得
                var message = result.substr(7);
                
                // 警告メッセージ表示
                window.alert( message );
            }
            else{
                // 正常メッセージ表示
                window.alert( result );
            }
        }
    }
var proxy = new Db_Access(new callback());

//////// 対象作業の情報を取得するファンクション ////////
function disp_execution(){
    // 変数定義(target_execution_noを取得)
    var target_execution_no = document.getElementById('target_execution_no').innerHTML;
    
    // HTML_AJAXファンクションをコール
    proxy.dispExecution( target_execution_no );
}

//////// 緊急停止を実施するファンクション ////////
function scrum_execution(){
    // '緊急停止してよろしいですか？'
    if( window.confirm( getSomeMessage("ITAANSIBLEC103030")) ){
        // ボタンを非活性にする
        document.getElementById('scrumTryExecute').disabled = true;
        
        //  緊急停止実施フラグをONにする
        scramed_frag = true;
        
        // 変数定義(target_execution_noを取得)
        var target_execution_no = document.getElementById('target_execution_no').innerHTML;
        
        // HTML_AJAXファンクションをコール
        proxy.ScramExecution( target_execution_no );
    }
}

//////// 予約取消を実施するファンクション ////////
function book_cancel(){
    //'予約を取り消してよろしいですか？'
    if( window.confirm(getSomeMessage("ITAANSIBLEC103040")) ){
        // ボタンを非活性にする
        document.getElementById('book_cancel_button').disabled = true;
        
        // 変数定義(target_execution_noを取得)
        var target_execution_no = document.getElementById('target_execution_no').innerHTML;
        
        // HTML_AJAXファンクションをコール
        proxy.BookCancel( target_execution_no );
    }
}

//////// 画面生成時に初回実行する処理 ////////
window.onload = function(){
    // クエリ存在判定
    var execution_no = getQuerystring("execution_no");
    
    // 作業№が取得されなかった場合
    if ( execution_no.length == 0 ){
        // 警告ポップアップを表示
        //----作業管理リストから作業№を選択して下さい。
        window.alert(getSomeMessage("ITAANSIBLEC103050"));
        
        // 遷移先URLを作成
        //var url = '../execution_management/01_browse.php';
        var url = '/default/menu/01_browse.php?no=2100020213';
        
        // 作業状態確認メニューに遷移
        location.href=url;
    }
    else{
        // 進行状況のtailをスタート
        cntr=1;
        while( document.getElementById("TailWindow_"+cntr) != null ){
            document.getElementById("TailWindow_"+cntr).contentWindow.start();
            
            cntr++;
        }
        
        var interval = document.getElementById('intervalOfDisp').innerHTML;
        
        // ファンクション「disp_execution()」をループ呼び出し
        disp_execution();
        timerID = setInterval( "disp_execution()", interval );
    }
    show('SetsumeiMidashi','SetsumeiNakami');
}