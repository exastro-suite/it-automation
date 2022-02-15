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
//////// ----コールバックファンクション ////////
function callback() {}
callback.prototype = {
    //----Conductor系メソッド
    //----Conductor再描画用-----//
    printconductorClass : function( result ){
        conductorUseList.conductorData = result;
        if ( conductorGetMode === 'starting') {
          initEditor('checking');
        }
    },
    //----Movementリスト用-----//
    printMatchedPatternListJson : function( result ){
        conductorUseList.movementList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printConductorList( conductorInstanceID );
        }
    },
    //----個別オペレーションリスト用-----//
    printOperationList : function( result ){
        conductorUseList.operationList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printMatchedPatternListJson( conductorInstanceID );
        }
    },
    //----Callリスト用-----//
    printConductorList : function( result ){
        conductorUseList.conductorCallList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
            proxy.printSymphonyList( conductorInstanceID );
        }
    },
    //---- Symphony Callリスト用-----//
    printSymphonyList : function( result ){
        conductorUseList.symphonyCallList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
            proxy.printConductorStatus( conductorInstanceID );
        }
    },
    //----Conductor登録----//
    printConductorStatus : function( result ){
        //console.log( result );
        if ( typeof(result) === 'string') {
          var ary_result = getArrayBySafeSeparator(result);
          var conductorInstanceId = location.search.split('&');
          ary_result[2] = ary_result[2] + '&' + conductorInstanceId[1];
          checkTypicalFlagInHADACResult(ary_result);
        }

        conductorUseList.conductorStatus = JSON.parse( result );

        //ステータス情報がが無い場合はConductor作業一覧へ飛ばす
        if ( conductorUseList.conductorStatus.length == 0 ) {
            alert( getSomeMessage("ITABASEC020001") );
            var url = '/default/menu/01_browse.php?no=2100180006';
            location.href = url;
        }

        if ( conductorGetMode === 'starting') {
            var conductorID = Number( conductorUseList.conductorStatus.CONDUCTOR_INSTANCE_INFO.CONDUCTOR_CLASS_NO );
            proxy.printNoticeList( conductorID );
            proxy.printconductorClass( conductorID );
        } else {
            $( window ).trigger('conductorStatusUpdate');
        }
    },
    //----Conductor予約取消----//
    bookCancelConducrtorInstance : function( result ){
        //console.log(result);
        if ( result[0] === '000' ) {
          editor.log.set('notice', getSomeMessage("ITABASEC020003",{0:result[2]}));
          proxy.printConductorStatus( conductorInstanceID );
        } else {
          editor.log.set('error', 'Cancel error');
        }
    },
    //----Conductor強制停止----//
    scramConducrtorInstance : function( result ){
      
        var ary_result = getArrayBySafeSeparator(result);
        if( ary_result[0]=='redirectOrderForHADACClient' ){
          var conductorInstanceId = location.search.split('&');
          ary_result[2] = ary_result[2] + '&' + conductorInstanceId[1]
          checkTypicalFlagInHADACResult(ary_result);
      }

        var errorType = '',
            errorMessage = '';
        if ( result[0] === '000' ) {
          errorType = 'notice';
          errorMessage = getSomeMessage("ITABASEC020005",{0:result[2]});
        } else {
          errorType = 'error';
          errorMessage = 'Scram error';
        }
        // バックヤードの周回タイミングに合わせ結果の表示を遅らせる
        setTimeout( function() {
          editor.log.set( errorType, errorMessage );
          proxy.printConductorStatus( conductorInstanceID );
        }, 3000 );
    },
    //----Conductor保留解除----//
    holdReleaseNodeInstance : function( result ){
        var ary_result = getArrayBySafeSeparator(result);
        if( ary_result[0]=='redirectOrderForHADACClient' ){
          var conductorInstanceId = location.search.split('&');
          ary_result[2] = ary_result[2] + '&' + conductorInstanceId[1]
          checkTypicalFlagInHADACResult(ary_result);
      }

        if ( result[0] === '000') {
          editor.log.set('notice', getSomeMessage("ITABASEC020007",{0:result[2]}));
          proxy.printConductorStatus( conductorInstanceID );
        } else {
          editor.log.set('error', 'Pause release error');
        }
    },
    
    // ---- Notice ----- //
    printNoticeList : function( result ) {
      conductorUseList.noticeList = JSON.parse( result );
      if ( conductorGetMode === 'starting') {
        proxy.printNoticeStatusList();
      }
    },
    printNoticeStatusList : function( result ) {
      conductorUseList.noticeStatusList = JSON.parse( result );
      if ( conductorGetMode === 'starting') {
        proxy.printOperationList();
      }
    }
    // Notice ----

}

var proxy = new Db_Access(new callback());

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   エディタ共通初期設定（editor_common.js）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const editor = new itaEditorFunctions();

//パラメータを取得
const conductorInstanceID = editor.getParam('conductor_instance_id');

// DOM読み込み完了
$( function(){
    if ( conductorInstanceID !== null ) {
      // conductor_instance_idが数値無い場合はConductor作業一覧へ飛ばす
      if( isNaN( conductorInstanceID ) === true ){
        alert( getSomeMessage("ITABASEC020001") );
        var url = '/default/menu/01_browse.php?no=2100180006';
        location.href = url;
      }
      // リスト取得開始
      proxy.printNoticeList();
      // タブ切り替え
      editor.tabMenu();
      // 画面縦リサイズ
      editor.rowResize();
    } else {
      // conductor_instance_idが無い場合はConductor作業一覧へ飛ばす
      alert( getSomeMessage("ITABASEC020001") );
      var url = '/default/menu/01_browse.php?no=2100180006';
      location.href = url;
    }
});
