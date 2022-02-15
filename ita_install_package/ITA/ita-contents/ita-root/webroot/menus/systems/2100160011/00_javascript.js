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
//   limitations under the License
//

//////// ----コールバックファンクション ////////
function callback() {}
callback.prototype = {
    /////////////////////
    // callback: 新規登録
    /////////////////////
    registerTable : function(result){
        var ary_result = getArrayBySafeSeparator(result);
        // 正常の場合

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var result = JSON.parse(ary_result[2]);
            var id  = result['CREATE_MENU_ID'];
            var num = result['MM_STATUS_ID'];
            var string = getSomeMessage("ITACREPAR_1236");
            var log = string + num;
            menuEditorLog.set('done', log );
            window.alert(log);
            location.href = '/default/menu/01_browse.php?no=2100160011&create_menu_id=' + id + '&create_management_menu_id=' + num;
        }
        // バリデーションエラーの場合
        else if( ary_result[0] == "002" || ary_result[0] == "003"){
            // 二回目以降のバリデエラーの場合に前回表示したエラーログを消す
            menuEditorLog.clear();
            menuEditorLog.set('error', ary_result[2] );
            window.alert(getSomeMessage("ITAWDCC90102"));
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: 更新
    /////////////////////
    updateTable : function(result){
        var ary_result = getArrayBySafeSeparator(result);
        // 正常の場合

        if( ary_result[0]=='redirectOrderForHADACClient' ){
            var conductorInstanceId = location.search.split('&');
            ary_result[2] = ary_result[2] + '&' + conductorInstanceId[1] + '&' + conductorInstanceId[2];
            checkTypicalFlagInHADACResult(ary_result);
        }

        if( ary_result[0] == "000" ){
            var result = JSON.parse(ary_result[2]);
            var id  = result['CREATE_MENU_ID'];
            var num = result['MM_STATUS_ID'];
            var string = getSomeMessage("ITACREPAR_1236");
            var log = string + num; 
            menuEditorLog.set('done', log );
            window.alert(log);
            location.href = '/default/menu/01_browse.php?no=2100160011&create_menu_id=' + id + '&create_management_menu_id=' + num;
        }
        // バリデーションエラーの場合
        else if( ary_result[0] == "002" || ary_result[0] == "003"){
            // 二回目以降のバリデエラーの場合に前回表示したエラーログを消す
            menuEditorLog.clear();
            menuEditorLog.set('error', ary_result[2] );
            window.alert(getSomeMessage("ITAWDCC90102"));
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: 入力方式リスト取得
    /////////////////////
    selectInputMethod: function(result){

        var ary_result = getArrayBySafeSeparator(result);
        
        // 正常の場合
        if( ary_result[0] == "000" ){
            menuEditorArray.selectInputMethod = JSON.parse(ary_result[2]);
            selectParamTarget();
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: 作成対象リスト取得
    /////////////////////
    selectParamTarget : function(result){

        var ary_result = getArrayBySafeSeparator(result);

        // 正常の場合
        if( ary_result[0] == "000" ){
            menuEditorArray.selectParamTarget = JSON.parse(ary_result[2]);
            selectParamPurpose();
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: 用途リスト取得
    /////////////////////
    selectParamPurpose : function(result){

        var ary_result = getArrayBySafeSeparator(result);

        // 正常の場合
        if( ary_result[0] == "000" ){
            menuEditorArray.selectParamPurpose = JSON.parse(ary_result[2]);
            selectMenuGroupList();
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: メニューグループリスト取得
    /////////////////////
    selectMenuGroupList : function(result){
        var ary_result = getArrayBySafeSeparator(result);

        // 正常の場合
        if( ary_result[0] == "000" ){
            menuEditorArray.selectMenuGroupList = JSON.parse(ary_result[2]);
            selectPulldownList();
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: プルダウン選択項目リスト取得
    /////////////////////
    selectPulldownList : function(result){
       var ary_result = getArrayBySafeSeparator(result);

       // 正常の場合
       if( ary_result[0] == "000" ){
           menuEditorArray.selectPulldownList = JSON.parse(ary_result[2]);
           selectReferenceSheetType3List();
       }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: パラメータシート参照リスト取得(メニューのみ)
    /////////////////////
    selectReferenceSheetType3List : function(result){
       var ary_result = getArrayBySafeSeparator(result);

       // 正常の場合
       if( ary_result[0] == "000" ){
           menuEditorArray.selectReferenceSheetType3List = JSON.parse(ary_result[2]);
            if ( menuEditorTargetID === '') {
              menuEditor( menuEditorMode, menuEditorArray );
            } else {
              selectMenuInfo( menuEditorTargetID );
            }
       }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: メニュー作成情報関連データ取得
    /////////////////////
    selectMenuInfo : function(result){
        var ary_result = getArrayBySafeSeparator(result);
        // 正常の場合
        if( ary_result[0] == "000" ){
            menuEditorArray.selectMenuInfo = JSON.parse(ary_result[2]);
            selectReferenceItemList(menuEditorArray.selectMenuInfo.item);
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: 参照項目リスト取得
    /////////////////////
    selectReferenceItemList : function(result){
       var ary_result = getArrayBySafeSeparator(result);
       // 正常の場合
       if( ary_result[0] == "000" ){
            menuEditorArray.referenceItemList = JSON.parse(ary_result[2]);
            selectReferenceSheetType3ItemData(menuEditorArray.selectMenuInfo.item);
       }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    },
    /////////////////////
    // callback: パラメータシート参照の項目からメニューIDを取得
    /////////////////////
    selectReferenceSheetType3ItemData : function(result){
        var ary_result = getArrayBySafeSeparator(result);
        // 正常の場合
        if( ary_result[0] == "000" ){
            menuEditorArray.referenceSheetType3ItemData = JSON.parse(ary_result[2]);
            menuEditor( menuEditorMode, menuEditorArray );
        }
        // システムエラーの場合
        else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    }
}


var proxy = new Db_Access(new callback());
var filter_on = false;

window.onload = function(){
}

/////////////////////
// 新規登録
/////////////////////
function registerTable(menuData){
    proxy.registerTable(menuData);
}

/////////////////////
// 更新
/////////////////////
function updateTable(menuData){
    proxy.updateTable(menuData);
}

/////////////////////
// 入力方式リスト取得
/////////////////////
function selectInputMethod(){
    proxy.selectInputMethod();
}

/////////////////////
// 作成対象リスト取得
/////////////////////
function selectParamTarget(){
    proxy.selectParamTarget();
}

/////////////////////
// 用途リスト取得
/////////////////////
function selectParamPurpose(){
    proxy.selectParamPurpose();
}

/////////////////////
// メニューグループリスト取得
/////////////////////
function selectMenuGroupList(){
    proxy.selectMenuGroupList();
}

/////////////////////
// プルダウン選択項目リスト取得
/////////////////////
function selectPulldownList(){
    proxy.selectPulldownList();
}

/////////////////////
// 参照項目リスト取得
/////////////////////
function selectReferenceItemList(itemArray){
    proxy.selectReferenceItemList(itemArray);
}

/////////////////////
// パラメータシート参照リスト取得(メニューのみ)
/////////////////////
function selectReferenceSheetType3List(){
    proxy.selectReferenceSheetType3List();
}

/////////////////////
// パラメータシート参照の項目からメニューIDを取得
/////////////////////
function selectReferenceSheetType3ItemData(itemArray){
    proxy.selectReferenceSheetType3ItemData(itemArray);
}

/////////////////////
// メニュー作成情報関連データ取得
/////////////////////
function selectMenuInfo(createMenuId){
    proxy.selectMenuInfo(createMenuId);
}

/////////////////////
// ロールリストを取得
/////////////////////
function getRoleList() {
    const printRoleListURL = '/common/common_printRoleList.php?user_id=' + gLoginUserID;
    $.ajax({
      type: 'get',
      url: printRoleListURL,
      dataType: 'text'
    }).done( function( result ) {
        menuEditorArray.roleList = JSON.parse( result );
        selectInputMethod();
    }).fail( function( result ) {
        window.alert(getSomeMessage("ITAWDCC90101"));
    });
}

$( function(){

    menuEditorMode = $('#menu-editor').attr('data-editor-mode');
    menuEditorTargetID = $('#menu-editor').attr('data-load-menu-id');
    
    // 各種リストを順次読み込む
    getRoleList();

});