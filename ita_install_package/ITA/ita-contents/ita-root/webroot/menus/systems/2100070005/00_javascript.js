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

// MNGプロセス
    const CONSTRUCT = "1"; // 未実施
    const RESERVE   = "2"; // 予約中
    const PREPARE   = "3"; // 準備中（実行君が２にする）
    const EXECUTE_AND_WAITING_COMPLETE="4"; // 子プロセスそれぞれのHEATを投げました。完了待ちです。

      //DETAILプロセス
      const CHILD_FAILED_BY_SCRAM  ="0"; // 緊急停止ボタンが押された結果、処理をすべてキャンセルしました。
      const CHILD_WAITING_RESPONSE ="1"; // HEATの読み込みは完了しました。記載された作業が完了したか、確認中です。
      const CHILD_FAILED_BY_HEAT   ="2"; // 結果がわかりました。ダメでした。（HEATがイケてない）
      const CHILD_FAILED_BY_OTHER  ="3"; // 結果が分かりました。なんか別の理由で失敗しました。
      const CHILD_SUCCESS          ="4"; // 結果が分かりました。成功しました。

    const SCRAM           = "5";  // 緊急停止ボタンが押されました。 
    const SCRAM_COMPLETE  = "6";  // 緊急停止ボタンが押されました。 
    const FAILUIRE        = "7";  // 子プロセスが全部ステータス（３か４）まで進みました。全部失敗しました（これ必要か？）
    const PARTIAL_FAILURE = "8";  // 子プロセスが全部ステータス（３か４）まで進みました。一部失敗したやつがいました。
    const COMPLETE        = "9";  // 子プロセスが全部ステータス（３か４）まで進みました。全部成功です。
    const RESERVE_CANCEL  = "10"; // 予約が取り消されました。

//////// コールバックファンクション ////////
function callback() {}
    callback.prototype =
    {
        //確認君で変更されたステータスをただただバー表示するだけ。
        dispExecution : function(result){
            if ( result.match(/^unexpected_error/) ){
                // リダイレクト先URLを取得
                var redirect_url = result.substr(16);

                // 作業管理メニューにリダイレクト
                location.href=redirect_url;
                console.error("redirect_url");
            }
            else{
                if(isJson(result)){
                    var response=JSON.parse(result);
                    var mngNode=response.mng[0];
                    var node=mngNode;
                    var timeFrom=(node.TIME_START!=null)?node.TIME_START.split(".")[0]:"";
                    var tmeTo=(node.TIME_END!=null)?node.TIME_END.split(".")[0]:"";
                    var detailArray=response.detail.sort(function(a, b) {
                        return b.SYSTEM_NAME < a.SYSTEM_NAME;
                    });

                    //manage
                    //var $html=$('<h3>全体の進捗:</h3>'+
                    var $html=$('<h3>' + getSomeMessage("ITAOPENSTC101010") + '</h3>'+
                        '<div class="timeArea">'+
                            '<div class="subText">'+(node.I_OPERATION_NAME || getSomeMessage("ITAOPENSTC101020"))+'</div>'+
                            '<div class="timeTo">'+tmeTo+'</div>'+
                            '<div class="timeFrom">'+timeFrom+'</div>'+
                            '<div style="clear:both"></div>'+
                            '<div class="progressBar"></div>'+
                            '<div class="message">'+node.STATUS_NAME+'</div>'+
                        '</div>');

                    //開始から終了の間だけ、緊急停止ボタンがアクティブ
                    if($.inArray(node.STATUS_ID,[CONSTRUCT,PREPARE,EXECUTE_AND_WAITING_COMPLETE])>=0){
                        $("#scrumTryExecute").prop("disabled","");
                    }else{
                        $("#scrumTryExecute").prop("disabled","disabeld");
                    }

                    //異常時はメッセージが赤
                    if($.inArray(node.STATUS_ID,[FAILUIRE,PARTIAL_FAILURE,SCRAM,SCRAM_COMPLETE])>=0){
                        $html.find(".message").css("color","red");
                    }

                    $progressStatus = 0;
                    if(node.STATUS_ID != "10") {
                        $progressStatus = parseInt(node.STATUS_ID);
                    }
                    $html.find(".progressBar").progressbar({
                        "value" : $progressStatus,
                        "max"   : 6
                    });

                    //異常時
                    if($.inArray(node.STATUS_ID,[FAILUIRE,PARTIAL_FAILURE,SCRAM,SCRAM_COMPLETE])>=0){
                        //(partial)failureはプログレスバーが赤
                        $html.find(".ui-progressbar-value").css({
                            "background-image":"URL('/common/imgs/jqimg/ui-bg_diagonals-thick_18_b81900_40x40.png')",
                            "opacity":"1",
                            "border-color":"red"
                        });

                    }else if($.inArray(node.STATUS_ID,[COMPLETE])>=0){
                        //(partial)failureはプログレスバーが緑
                        $html.find(".ui-progressbar-value").css({
                            "background-color":"green"
                        });

                    }

                    if($.inArray(node.STATUS_ID,[FAILUIRE,PARTIAL_FAILURE,COMPLETE,SCRAM_COMPLETE,RESERVE_CANCEL])>=0){
                        clearInterval(timerID);
                        $(".dispLog").show();
                    }
                    $(".header").empty().append($html);

                    //detail
                    $(".detail").empty()

                    if(detailArray.length>0){
                        $(".detail").append("<h3>" + getSomeMessage("ITAOPENSTC101030") + "</h3>");
                        for (var i =0; i<detailArray.length;i++) {

                            var node=detailArray[i];
                            var timeFrom=(node.TIME_START!=null)?node.TIME_START.split(".")[0]:"";
                            var tmeTo=(node.TIME_END!=null)?node.TIME_END.split(".")[0]:"";

                            var $html=$('<div class="projectArea">'+
                                    '<div class="timeArea">'+
                                        '<div class="subText">'+(node.SYSTEM_NAME || getSomeMessage("ITAOPENSTC101020"))+'</div>'+
                                        '<div class="timeTo">'+tmeTo+'</div>'+
                                        '<div class="timeFrom">'+timeFrom+'</div>'+
                                        '<div style="clear:both"></div>'+
                                        '<div class="progressBar"></div>'+
                                        '<div class="message">'+node.STATUS_NAME+'</div>'+
                                    '</div>'+
                                '</div>');

                            if($.inArray(node.STATUS_ID,[CHILD_FAILED_BY_HEAT,CHILD_FAILED_BY_OTHER,CHILD_FAILED_BY_SCRAM])>=0){
                                $html.find(".message").css("color","red");
                            }
                            $html.find(".progressBar").progressbar({
                                value:parseInt(node.STATUS_ID),
                                "max":4
                            });
                            if($.inArray(node.STATUS_ID,[CHILD_FAILED_BY_HEAT,CHILD_FAILED_BY_OTHER,CHILD_FAILED_BY_SCRAM])>=0){
                                $html.find(".ui-progressbar-value").css({
                                    "background-image":"URL('/common/imgs/jqimg/ui-bg_diagonals-thick_18_b81900_40x40.png')",
                                    "opacity":"1",
                                    "border-color":"red"
                                });
                            }else if($.inArray(node.STATUS_ID,[CHILD_FAILED_BY_HEAT,CHILD_FAILED_BY_OTHER,CHILD_FAILED_BY_SCRAM])>=0){
                                $html.find(".ui-progressbar-value").css({
                                    "background-color":"green"
                                });
                            }

                            $(".detail").append($html);
                        }
                    }
                }else{
                    alert(getSomeMessage("ITAOPENSTC101040"));
                }
            }
        },
        scramExecution : function(result){
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
//OpenStackでは、作成中のすべてのスタックを削除する
function scrum_execution(){
    // '緊急停止してよろしいですか？'
    if( window.confirm( getSomeMessage("ITABASEC010111") ) )
    {
        // ボタンを非活性にする
        document.getElementById('scrumTryExecute').disabled = true;
        
        //  緊急停止実施フラグをONにする
        scramed_frag = true;
        
        // 変数定義(target_execution_noを取得)
        var target_execution_no = document.getElementById('target_execution_no').innerHTML;
        
        // HTML_AJAXファンクションをコール
        proxy.scramExecution( target_execution_no );
    }
}

//////// 予約取消を実施するファンクション ////////
function book_cancel(){
    //'予約を取り消してよろしいですか？'
    if( window.confirm( getSomeMessage("ITABASEC010112") ) )
    {
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
        window.alert(getSomeMessage("ITABASEC010113") );
        
        // 遷移先URLを作成
        var url = '/default/menu/01_browse.php?no=2100070006';
        
        // 作業状態確認メニューに遷移
        location.href=url;
    }
    else{
        var interval = document.getElementById('intervalOfDisp').innerHTML;
        
        // ファンクション「disp_execution()」をループ呼び出し
        disp_execution();
        timerID = setInterval( "disp_execution()", interval );
    }
    show('SetsumeiMidashi','SetsumeiNakami');
}

function isJson(arg){
    arg=(typeof(arg)=="function")?arg():arg;
    if(typeof(arg)!="string"){return false;}
    try{arg=(!JSON)?eval("("+arg+")"):JSON.parse(arg);return true;}catch(e){return false;}
}
$(function(){

    //ログ表示を押したら、結果管理の画面にGETで遷移
    $(document).on("click",".dispLog",function(){
        var execution_no=$('#target_execution_no').text();
        location.href="/default/menu/01_browse.php?no=2100070006&execution_no="+execution_no;

    });
    $(document).on("click","#scrumTryExecute",function(){
        scrum_execution();
    });
})
