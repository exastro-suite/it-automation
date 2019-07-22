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
//  【テンプレートバージョン】
//   7.0
//
//  【特記事項】
//      表記規則
//      ・インデントは半角スペースx4
//      ・関数名宣言行に『 { 』は配置する。
//      ・if文の宣言行に『 { 』を配置する。
//      ・elseは『 } 』と同じ行に配置する。
//      その他
//      ・スケルトンディレクトリへの配置用
//      ・テンプレートバージョン5.0.1よりも
//        loadTable設定者が定義した任意JScript関数を呼び出しやすい
//        シンプル版(5.0)をメニュー作成ツール用として採用
//      ・チケット953による改修で、メニュー作成ツール用の版番号を5.1系へ変更
//
//////////////////////////////////////////////////////////////////////

//////// ----コールバックファンクション ////////
function callback() {}
callback.prototype = {

    // メインメニューパネルの並び順をDBに格納・更新する
    panel_sort_update : function(result){
        //alert('wake-callback-fx(panel_sort_update)');
    },

    // 画面読み込み時の表示モードを読み込む
    default_mode_select : function(result){

        var select = document.getElementById('size_select');

        if (select){ 
            for(i = 0; i < select.options.length; i++){ 
                if (select.options[i].value == result){ 
                    select[i].selected = true; 
                    break; 
                } 
            }  
        }
        if (result == "classic"){
            return;
        }
        else{
            menumode_change();
        }
    },

    // 表示モードをDBに格納・更新する
    mode_select_input : function(mode){

        // クラシックモードとの切り替え時には画面の再読み込みを行う
        if((mode == "classic") || !(document.getElementById('sortable'))){
            var select = document.getElementById('size_select');
            select.disabled = true;
            location.reload();
        }
    }

    //---- ここからカスタマイズした場合の[callback]メソッド配置域

    // ここまでカスタマイズした場合の[callback]メソッド配置域----
}

//////// 画面生成時に初回実行する処理 ////////

var proxy = new Db_Access(new callback());

// 画面読み込み時の処理
onload=function(){

    default_mode_select();
    var select = document.getElementById('size_select');

    // 表示モード切替時の処理
    select.onchange = function(){
        if (!(document.getElementById('sortable'))){
            var selectedItem = select.options[ select.selectedIndex ];
            var mode = selectedItem.value;
        }
        else{
            var mode = menumode_change();
        }
        mode_select_input(mode);
    }
}

//////// ----セレクトタグ追加ファンクション ////////

function panel_sort_update( result ){
    proxy.panel_sort_update(result);
}

function default_mode_select( result ){
    proxy.default_mode_select(result);
}

function mode_select_input( mode ){
    proxy.mode_select_input(mode);
}

//////// 表示モード切替ファンクション---- ////////


function menumode_change(){

    var select = document.getElementById('size_select');
    var panel_1 = document.getElementsByClassName('mm_icon');
    var panel_2 = document.getElementsByClassName('mm_list');
    var panel_3 = document.getElementsByClassName('mm_text');
    var panel_4 = document.getElementsByClassName('drag_img');
    var panel_5 = document.getElementById('sortable');

    // 選択されているoption要素を取得する
    var selectedItem = select.options[ select.selectedIndex ];
    var mode = "large_panel";

    if(selectedItem.value == "large_panel"){
        mode = "large_panel";
        for (var i=0;i<panel_1.length;i++) {
            panel_1[i].style.width = "140px";
            panel_1[i].style.height = "140px";
            panel_1[i].style.borderradius = "10px";
            panel_2[i].style.width = "140px";
            panel_2[i].style.height = "120px";
            panel_3[i].style.width = "140px";
            panel_3[i].style.height = "20px";
            panel_3[i].style.margin = "140px 0 0 0";
            panel_4[i].style.width = "25px";
            panel_4[i].style.height = "25px";
        }
        panel_5.style.height = "850px";
    }
    else if(selectedItem.value == "middle_panel"){
        mode = "middle_panel";
        for (var i=0;i<panel_1.length;i++) {
            panel_1[i].style.width = "100px";
            panel_1[i].style.height = "100px";
            panel_1[i].style.borderradius = "6px";
            panel_2[i].style.width = "100px";
            panel_2[i].style.height = "80px";
            panel_3[i].style.width = "100px";
            panel_3[i].style.height = "20px";
            panel_3[i].style.margin = "100px 0 0 0";
            panel_4[i].style.width = "15px";
            panel_4[i].style.height = "15px";
        }
        panel_5.style.height = "650px";
    }
    else if(selectedItem.value == "small_panel"){
        mode = "small_panel";
        for (var i=0;i<panel_1.length;i++) {
            panel_1[i].style.width = "60px";
            panel_1[i].style.height = "60px";
            panel_1[i].style.borderradius = "3px";
            panel_2[i].style.width = "60px";
            panel_2[i].style.height = "40px";
            panel_3[i].style.width = "60px";
            panel_3[i].style.height = "20px";
            panel_3[i].style.margin = "60px 0 0 0";
            panel_4[i].style.width = "10px";
            panel_4[i].style.height = "10px";
            panel_4[i].style.margin = "3px";
        }
        panel_5.style.height = "450px";
    }
    else if(selectedItem.value == "classic"){
        mode = "classic";
    }
    else{
    }
    return mode;
}


//////// ----汎用系ファンクション ////////
//////// 汎用系ファンクション---- ////////

//---- ここからカスタマイズした場合の一般メソッド配置域
// ここまでカスタマイズした場合の一般メソッド配置域----
