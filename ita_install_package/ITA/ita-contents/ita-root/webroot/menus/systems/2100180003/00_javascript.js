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
    //----Symphony系メソッド
    printSymphonyClass : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            printSymphonyClass(false,ary_result[2],ary_result[3],ary_result[4],ary_result[5]);
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[6];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[6];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    printMatchedPatternList : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            printMatchedPatternList(false,ary_result[2])

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    update_execute_ : function( result ){

        var strAlertAreaName = 'symphony_message';
        var editCommandAreaWrap = 'symphony_footer';

        console.log(result);

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
        console.log(ary_result);
        if( ary_result[0] == "000" ){

            symphonyUpdate(false, ary_result);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            // ボタンを再活性化
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }

        showForDeveloper(result);
    },
    register_execute_ : function( result ){

        var strAlertAreaName = 'symphony_message';
        var editCommandAreaWrap = 'symphony_footer';
        console.log(result);

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);
        console.log(ary_result);
        if( ary_result[0] == "000" ){

            symphonyRegister(false, ary_result);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            // ボタンを再活性化
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);

    }
    //Symphony系メソッド----

    ,
    //----Conductor系メソッド
    //----Conductor再描画用-----//
    printconductorClass : function( result ){
        
        var ary_result = getArrayBySafeSeparator(result);
        if( ary_result[0] == "redirectOrderForHADACClient" ) {
            var conductorClassId = location.search.split('&');
            ary_result[2] = ary_result[2] + '&' + conductorClassId[1];
            checkTypicalFlagInHADACResult(ary_result);
        }

        conductorUseList.conductorData = result;
        if ( conductorGetMode === 'starting') {
          initEditor('view');
        } else {
          $( window ).trigger('conductorReset');
        }
    },
    //----Movementリスト用-----//
    printMatchedPatternListJson : function( result ){
        conductorUseList.movementList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printConductorList();
        }
    },
    //----個別オペレーションリスト用-----//
    printOperationList : function( result ){
        conductorUseList.operationList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printMatchedPatternListJson();
        }
    },
    //----Callリスト用-----//
    printConductorList : function( result ){
        conductorUseList.conductorCallList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          getRoleList();
        }
    }
    ,
    //----Conductorアップデート-----//
    update_execute : function( result ){
        conductorResultMessage('conductorUpdate', result )
    },
    //----Conductor登録----//
    register_execute : function( result ){
        conductorResultMessage('conductorRegister', result );
    },
    printSymphonyList : function( result ){
        conductorUseList.symphonyCallList = JSON.parse( result );
        // Editor起動時
        if ( conductorGetMode === 'starting') {
          if ( conductorClassID !== null ) {
             proxy.printconductorClass( Number( conductorClassID ) );
          } else {
            conductorUseList.conductorData = null;
            initEditor('edit');
          }
        }
    },
    //Conductor系メソッド----
    
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

// ロール一覧を取得する
function getRoleList() {
    const printRoleListURL = '/common/common_printRoleList.php?user_id=' + gLoginUserID;
    $.ajax({
      type: 'get',
      url: printRoleListURL,
      dataType: 'text'
    }).done( function( result ) {
        conductorUseList.roleList = JSON.parse( result );
        if ( conductorGetMode === 'starting') {
          proxy.printSymphonyList();
        }
    }).fail( function( result ) {
    });
}




/* 登録・更新処理結果 */
function conductorResultMessage( type, result ) {
  var logType = '',
      message = '',
      trigger = '';

  var ary_result = getArrayBySafeSeparator(result);
  var conductorClassId = location.search.split('&');
  //セッションが切れた際、url上にコンダクタークラスidがある場合取得。
  if( conductorClassId.length == 2 && ary_result[0] == "redirectOrderForHADACClient"){
    ary_result[2] = ary_result[2] + '&' + conductorClassId[1]
  }
  checkTypicalFlagInHADACResult(ary_result);

  switch( result[0] ) {
    case '000': // Done
      logType = 'done';
      conductorMode('view');
      if ( type === 'conductorRegister') {
        message = getSomeMessage("ITABASEC010102");
      } else if ( type === 'conductorUpdate') {
        message = getSomeMessage("ITABASEC010103");
      }
      trigger = type;
      // 登録完了、再取得する
      proxy.printconductorClass( result[2] );
      break;
    case '002': // Error
      message += getSomeMessage("ITAWDCC90102");
    case '003': // ???
      logType = 'error';
      message += result[3];
      trigger = 'conductorError';
      break;
    default: //System Error
      logType = 'error';
      message += getSomeMessage("ITAWDCC90101");
      trigger = 'conductorSystemError';
      break;      
  }
  // ログエリアにメッセージ表示
  editor.log.set( logType, message );
  // ボタン活性化
  conductorFooterButtonDisabled( false );
  // イベントトリガー
  $( window ).trigger( trigger );
}





//////// ----汎用系ファンクション ////////
function setInputButtonDisable(rangeId,targetClass,toValue){
    if(toValue === true){
        $('#'+rangeId+' .'+targetClass).attr("disabled",true);
    }else{
        $('#'+rangeId+' .'+targetClass).removeAttr("disabled");
    }
}
//////// 汎用系ファンクション---- ////////

//////// テーブルレイアウト設定 ////////
var msgTmpl = {};
var privilege;
//////// 画面生成時に初回実行する処理 ////////

var proxy = new Db_Access(new callback());
/*
window.onload = function(){
    var initialFilterEl;
    var initialFilter;
    initialFilterEl = document.getElementById('sysInitialFilter');
    privilege = parseInt(document.getElementById('privilege').innerHTML);
    if(initialFilterEl == null){
        initialFilter = 2;
    }
    else{
        initialFilter = initialFilterEl.innerHTML;
    }

    initProcess('classEdit');

    if(privilege != 1){
        var strCommandAreaWrap = 'symphony_footer';
        var objCommandAreaWrap = document.getElementById(strCommandAreaWrap);
        objCommandAreaWrap.innerHTML = '';
    }

    var objFEB = document.getElementById('filter_execute');
    if(initialFilter == 1){
        objFEB.click();
    }
}
*/

//---- ここからカスタマイズした場合の一般メソッド配置域


//--表示関連
//----コールバック相互呼出系 作業パターン一覧の展開
function printMatchedPatternListJson(boolCallProxy,aryResultOfCalledProxy){
    console.log("printMatchedPatternListJson");
    if( boolCallProxy===true ){
        proxy.printMatchedPatternListJson();
    }
    else{
        console.log(aryResultOfCalledProxy);
    }
}
//----コールバック相互呼出系 オペレーションン一覧の展開
function printOperationList(boolCallProxy,aryResultOfCalledProxy){
    console.log("printOperationList");
    if( boolCallProxy===true ){
        proxy.printOperationList();
    }else{
        console.log(aryResultOfCalledProxy);
    }

}
//----コールバック相互呼出系 Call一覧の展開
function printConductorList(boolCallProxy,aryResultOfCalledProxy){
    console.log("printConductorList");
    if( boolCallProxy===true ){
        proxy.printConductorList();
    }else{
        console.log(aryResultOfCalledProxy);
    }
}
//----コールバック相互呼出系 再描画用JSONの展開
function printconductorClass(boolCallProxy,aryResultOfCalledProxy){
    console.log("printconductorClass");
    if( boolCallProxy===true ){
        var intConductorClassId = "";
        proxy.printconductorClass(intConductorClassId);
    }else{

    }
}
//----コールバック相互呼出系 Call一覧の展開
function printSymphonyList(boolCallProxy,aryResultOfCalledProxy){
    console.log("printSymphonyList");
    if( boolCallProxy===true ){
        proxy.printSymphonyList();
    }else{
        console.log(aryResultOfCalledProxy);
    }
}

// ここまでカスタマイズした場合の一般メソッド配置域----


/*
//--処理関連 不要？
//----コールバック相互呼出系 Conductor登録
function conductorRegister(boolCallProxy, aryResultOfCalledProxy){
    var strAlertAreaName = 'conductor_message';
    var editInfoAreaWrap = 'conductor_header';
    var editCommandAreaWrap = 'conductor_footer';
    
    if( boolCallProxy===true ){
        // registerTableファンクション呼び出し要否フラグ
        var exec_flag = true;
        
        // アラート用エリアを初期化
        var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
        objAlertArea.innerHTML = '';
        objAlertArea.style.display = "none";
        
        // registerTableファンクション呼び出し要否フラグ
        if( window.confirm(getSomeMessage("ITAWDCC20101")) == false ){
            exec_flag = false;
        }
        
        if( exec_flag ){
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',true);
            
            var register_data = "";
            proxy.register_execute(register_data);
        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" && aryResultOfCalledProxy[1]=="000" ){
            window.alert(getSomeMessage("ITABASEC010102"));
            var varRequestTarget = aryResultOfCalledProxy[2];
            
            //----ボタン「編集」を配置する
            drawCommandButtons(2);
            
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
            
            materialAreaFunctionOff();
            sortableAreaFunctionOff();
            
            //----実行確認前と同じく、表示専用モード[1]で表示させる。
            printConductorClass(true, varRequestTarget, "11");
        }
    }
}
//----コールバック相互呼出系 Conductor更新
function conductorUpdate(boolCallProxy, aryResultOfCalledProxy){
    var strAlertAreaName = 'conductor_message';
    var editInfoAreaWrap = 'conductor_header';
    var editCommandAreaWrap = 'conductor_footer';
    
    if( boolCallProxy===true ){
        // updateTableファンクション呼び出し要否フラグ
        var exec_flag = true;
        
        // アラート用エリアを初期化
        var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
        objAlertArea.innerHTML = '';
        objAlertArea.style.display = "none";
        
        //----更新を実行してよろしいですか？
        if( window.confirm( getSomeMessage("ITAWDCC20102") ) == false ){
            exec_flag = false;
        }
        
        if( exec_flag ){
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',true);

            var conductor_class_id = "";
            var update_data = "";
            var conductor_lt4u = "";
            
                var objConductorNoArea = document.getElementById('print_conductor_id');
                var conductor_class_id = objConductorNoArea.innerHTML;
                
                var objConductorLT4UArea = document.getElementById('print_conductor_lt4u');
                var conductor_lt4u = objConductorLT4UArea.innerHTML;
            
            proxy.update_execute(conductor_class_id, update_data, conductor_lt4u);

        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" && aryResultOfCalledProxy[1]=="000" ){
            //window.alert("更新しました。");
            window.alert(getSomeMessage("ITABASEC010103"));
            var varRequestTarget = aryResultOfCalledProxy[2];
            
            //----ボタン「編集」を配置する
            drawCommandButtons(2);
            
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
            
            materialAreaFunctionOff();
            sortableAreaFunctionOff();
            
            //----実行確認前と同じく、表示専用モード[1]で表示させる。
            printConductorClass(true, varRequestTarget,"11");
        }
    }
}

*/

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   エディタ共通初期設定（editor_common.js）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const editor = new itaEditorFunctions();

//パラメータを取得
const conductorClassID = editor.getParam('conductor_class_id');

// DOM読み込み完了
$( function(){
    // リスト取得開始
    proxy.printNoticeList( conductorClassID )
    // タブ切り替え
    editor.tabMenu();
    // 画面縦リサイズ
    editor.rowResize();
    
});