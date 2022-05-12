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
    //---- ここからカスタマイズした場合の[callback]メソッド配置域
    get_widget_info : function( result ) {
      set_widget( result );
    },
    regist_widget_info : function( result ) {
      if ( typeof(result) === 'string') {
        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
      }
      if ( result[0] === '000') {
        $( window ).trigger('registWidgetInfoDone');
      } else {
        $( window ).trigger('registWidgetInfoFail');
      }
    },
    get_movement : function( result ) {
      if ( typeof(result) === 'string') {
        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
      }
      setPieChart( result, 'movement');
    },
    get_work_info : function( result ) {
      if ( typeof(result) === 'string') {
        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
      }
      setPieChart( result, 'status');
    },
    get_work_result : function( result ) {
      if ( typeof(result) === 'string') {
        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
      }
      workHistory( result );
      setPieChart( result, 'result');
    },
    get_symphony_conductor: function( result ) {
      if ( typeof(result) === 'string') {
        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
      }
      setSymphonyConductorList( result );
    }
    // ここまでカスタマイズした場合の[callback]メソッド配置域----
}

//////// 画面生成時に初回実行する処理 ////////

var proxy = new Db_Access(new callback());

//////// ----汎用系ファンクション ////////
function get_widget_info() {
    proxy.get_widget_info();
}
function regist_widget_info( widgetJSON ) {
    proxy.regist_widget_info( widgetJSON );
}
function get_movement() {
    proxy.get_movement();
}
function get_work_info() {
    proxy.get_work_info();
}
function get_work_result() {
    proxy.get_work_result();
}
function get_symphony_conductor( days ) {
  if ( days === undefined || days === '' || days === '0') {
    proxy.get_symphony_conductor();
  } else {
    proxy.get_symphony_conductor( days );
  }
}
//////// 汎用系ファンクション---- ////////

//---- ここからカスタマイズした場合の一般メソッド配置域
$(function(){
  get_widget_info();
});
// ここまでカスタマイズした場合の一般メソッド配置域----