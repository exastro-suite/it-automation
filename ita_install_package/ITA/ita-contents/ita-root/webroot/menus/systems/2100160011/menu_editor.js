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


// モード判定用
let menuEditorMode = '';

// 読み込み対象ID
let menuEditorTargetID = '';

// 各種リスト用配列
let menuEditorArray = {};

// datetimepicker用のlanguage判定
let objLangDiv = document.getElementById('LanguageMode');
let LangStream = 'en';
if( typeof objLangDiv != "undefined" ){
  LangStream = objLangDiv.innerHTML;
}

// テキストの無害化
const textEntities = function( text, flag ) {
  if ( flag === undefined ) flag = 0;
    const entities = [
      ['&', 'amp'],
      ['\"', 'quot'],
      ['\'', 'apos'],
      ['<', 'lt'],
      ['>', 'gt'],
    ];
    for ( var i = 0; i < entities.length; i++ ) {
      text = text.replace( new RegExp( entities[i][0], 'g'), '&' + entities[i][1] + ';' );
    }
    if ( flag !== 1 ) {
    text = text.replace(/^\s+|\s+$/g, '');
    text = text.replace(/\r?\n/g, '<br>');
    }
    return text;
};

// ログ表示
let menuEditorLogNumber = 1;
const menuEditorLog = {
  // log type : debug, log, notice, warning, error
  'set': function( type, content ) {
  $('.editor-tab-menu-list-item[data-tab="menu-editor-log"]').click();
  if ( type === undefined || type === '' ) type = 'log';
  
  const $menuEditorLog = $('.editor-log'),
        $menuEditorLogTable = $menuEditorLog.find('tbody');
        
  let logRowHTML = ''
    + '<tr class="editor-log-row ' + type + '">'
      + '<th class="editor-log-number">' + ( menuEditorLogNumber++ ) +'</th><td class="editor-log-content">';
  if ( type !== 'log') logRowHTML += '<span class="logLevel">' + textEntities( type.toLocaleUpperCase() ) + '</span>'
  
  logRowHTML += content + '</td></tr>';

  $menuEditorLogTable.append( logRowHTML );

  // 一番下までスクロール
  const scrollTop = $menuEditorLog.get(0).scrollHeight - $menuEditorLog.get(0).clientHeight;   
  $menuEditorLog.animate({ scrollTop : scrollTop }, 200 );

  },
  'clear': function() {
    menuEditorLogNumber = 1;
    $('.editor-log').find('tbody').empty();
  }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   モーダル
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// モーダルを開く
function itaModalOpen( headerTitle, bodyFunc, modalType, target = "" ) {
    if ( typeof bodyFunc !== 'function' ) return false;

    // 初期値
    if ( headerTitle === undefined ) headerTitle = 'Undefined title';
    if ( modalType === undefined ) modalType = 'default';

    const $window = $( window ),
          $body = $('body'),
          $container = $('.wholecontainer');
    
    let footerHTML1;
    
    if ( modalType === 'help' ) {
      footerHTML = ''
      + '<div class="editor-modal-footer">'
        + '<ul class="editor-modal-footer-menu">'
          + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button negative" data-button-type="close">' + getSomeMessage("ITAWDCC92003") + '</li>'
        + '</ul>'
      + '</div>'
    } else {
      footerHTML = ''
      + '<div class="editor-modal-footer">'
        + '<ul class="editor-modal-footer-menu">'
          + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button positive" data-button-type="ok">' + getSomeMessage("ITAWDCC92001") + '</li>'
          + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button negative" data-button-type="cancel">' + getSomeMessage("ITAWDCC92002") + '</li>'
        + '</ul>'
      + '</div>'
    }
    
    
    let modalHTML = ''
      + '<div id="editor-modal" class="' + modalType + '">'
        + '<div class="editor-modal-container">'
          + '<div class="editor-modal-header">'
            + '<span class="editor-modal-title">' + headerTitle + '</span>'
            + '<button class="editor-modal-header-close"></button>'
          + '</div>'
          + '<div class="editor-modal-body">'
            + '<div class="editor-modal-loading"></div>'
          + '</div>'
          + footerHTML
        + '</div>'
      + '</div>';

    const $editorModal = $( modalHTML ),
          $firstFocus = $editorModal.find('.editor-modal-header-close'),
          $lastFocus = $editorModal.find('.editor-modal-footer-menu-button[data-button-type="cancel"]');

    $body.append( $editorModal );
    $container.css('filter','blur(2px)');
    $firstFocus.focus();

    $window.on('keydown.modal', function( e ) {
      
      switch ( e.keyCode ) {
        case 9: // Tabでの移動をモーダル内に制限する
          {
            const $focusElement = $( document.activeElement );
            if ( $focusElement.is( $firstFocus ) && e.shiftKey ) {
              e.preventDefault();
              $lastFocus.focus();
            } else if ( $focusElement.is( $lastFocus ) && !e.shiftKey ) {
              e.preventDefault();
              $firstFocus.focus();
            }
          }
          break;
        case 27: // Escでモーダルを閉じる
          itaModalClose();
          break;
      }
    });

    $firstFocus.on('click', function() {
      itaModalClose();
    });
    if ( modalType === 'help' ) {
      $editorModal.find('.editor-modal-footer-menu-button[data-button-type="close"]').on('click', itaModalClose );
    }
    
    if(target != ""){
      bodyFunc(target);
    }else{
      bodyFunc();
    }

}

// モーダルを閉じる
function itaModalClose() {

  const $window = $( window ),
        $container = $('.wholecontainer'),
        $editorModal = $('#editor-modal');
  
  if ( $editorModal.length ) {
    $window.off('keyup.modal');
    $editorModal.remove();
    $container.css('filter','blur(0)');
  }

}

// モーダルエラー表示
function itaModalError( message ) {

  const $modalBody = $('.editor-modal-body');
  
  $modalBody.html('<div class="editor-modal-error"><p>' + message + '</p></div>');

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   タブ切替初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

function itaTabMenu() {

  $('.editor-tab').each( function() {
  
    const $tab = $( this ),
          $tabItem = $tab.find('.editor-tab-menu-list-item'),
          $tabBody = $tab.find('.editor-tab-body');

    $tabItem.eq(0).addClass('selected');
    $tabBody.eq(0).addClass('selected');
    
    $tabItem.on('click', function() {
      const $clickTab = $( this ),
            $openTab = $('#' + $clickTab.attr('data-tab') );
            
      $tab.find('.selected').removeClass('selected');
      $clickTab.add( $openTab ).addClass('selected');
    });
    
  });

}

const menuEditor = function() {

'use strict';

// jQueryオブジェキャッシュ
const $window = $( window ),
      $html = $('html'),
      $body = $('body'),
      $menuEditor = $('#menu-editor'),
      $menuEditWindow = $('#menu-editor-edit'),
      $menuTable = $menuEditor.find('.menu-table'),
      $property = $('#property');

// タブ
itaTabMenu();

// 読み込み完了
$menuEditor.removeClass('load-editor');

// テキスト
const languageText = {
'0000':[getSomeMessage("ITACREPAR_1202"),''],
'0001':[getSomeMessage("ITACREPAR_1203"),''],
'0002':[getSomeMessage("ITACREPAR_1204"),''],
'0003':[getSomeMessage("ITACREPAR_1205"),''],
'0004':[getSomeMessage("ITACREPAR_1206"),''],
'0005':[getSomeMessage("ITACREPAR_1207"),''],
'0006':[getSomeMessage("ITACREPAR_1208"),''],
'0007':[getSomeMessage("ITACREPAR_1209"),''],
'0008':[getSomeMessage("ITACREPAR_1210"),''],
'0009':[getSomeMessage("ITACREPAR_1211"),''],
'0010':[getSomeMessage("ITACREPAR_1212"),''],
'0011':[getSomeMessage("ITACREPAR_1213"),''],
'0012':[getSomeMessage("ITACREPAR_1214"),''],
'0013':[getSomeMessage("ITACREPAR_1215"),''],
'0014':[getSomeMessage("ITACREPAR_1216"),''],
'0015':[getSomeMessage("ITACREPAR_1217"),''],
'0016':[getSomeMessage("ITACREPAR_1218"),''],
'0017':[getSomeMessage("ITACREPAR_1219"),''],
'0018':[getSomeMessage("ITACREPAR_1220"),''],
'0019':[getSomeMessage("ITACREPAR_1221"),''],
'0020':[getSomeMessage("ITACREPAR_1222"),''],
'0021':[getSomeMessage("ITACREPAR_1223"),''],
'0022':[getSomeMessage("ITACREPAR_1224"),''],
'0023':[getSomeMessage("ITACREPAR_1225"),''],
'0024':[getSomeMessage("ITACREPAR_1226"),''],
'0025':[getSomeMessage("ITACREPAR_1227"),''],
'0026':[getSomeMessage("ITACREPAR_1228"),''],
'0027':[getSomeMessage("ITACREPAR_1229"),''],
'0028':[getSomeMessage("ITACREPAR_1230"),''],
'0029':[getSomeMessage("ITACREPAR_1231"),''],
'0030':[getSomeMessage("ITACREPAR_1232"),''],
'0031':[getSomeMessage("ITACREPAR_1233"),''],
'0032':[getSomeMessage("ITACREPAR_1234"),''],
'0033':[getSomeMessage("ITACREPAR_1235"),''],
'0034':[getSomeMessage("ITACREPAR_1238"),''],
'0035':[getSomeMessage("ITACREPAR_1239"),''],
'0036':[getSomeMessage("ITACREPAR_1240"),''],
'0037':[getSomeMessage("ITACREPAR_1241"),''],
'0038':[getSomeMessage("ITACREPAR_1242"),''],
'0039':[getSomeMessage("ITACREPAR_1243"),''],
'0040':[getSomeMessage("ITACREPAR_1244"),''],
'0041':[getSomeMessage("ITACREPAR_1245"),''],
'0042':[getSomeMessage("ITACREPAR_1246"),''],
'0043':[getSomeMessage("ITACREPAR_1252"),''],
'0044':[getSomeMessage("ITACREPAR_1253"),''],
'0045':[getSomeMessage("ITACREPAR_1254"),''],
'0046':[getSomeMessage("ITACREPAR_1276"),''],
'0047':[getSomeMessage("ITACREPAR_1280"),''],
'0048':[getSomeMessage("ITACREPAR_1285"),''],
'0049':[getSomeMessage("ITACREPAR_1286"),''],
'0050':[getSomeMessage("ITACREPAR_1287"),''],
'0051':[getSomeMessage("ITACREPAR_1296"),''],
'0052':[getSomeMessage("ITACREPAR_1297"),'']
}
// テキスト呼び出し用
const textCode = function( code ) {
  return languageText[code][0];
};

// 項目別ダミーテキスト（value:[ja,en,type]）
const selectDummyText = {
  '0' : ['','',''],
  '1' : [getSomeMessage("ITACREPAR_1204"),'','string'],
  '2' : [getSomeMessage("ITACREPAR_1205") +'<br>' + getSomeMessage("ITACREPAR_1205"),'','string'],
  '3' : ['0','0','number'],
  '4' : ['0.0','0.0','number'],
  '5' : ['2020/01/01 00:00','2020/01/01 00:00','string'],
  '6' : ['2020/01/01','2020/01/01','string'],
  '7' : ['','','select'],
  '8' : [getSomeMessage("ITACREPAR_1237"),'','string'],
  '9' : [getSomeMessage("ITACREPAR_1247"),'','string'],
  '10' : [getSomeMessage("ITACREPAR_1248"),'','string'],
  '11' : [getSomeMessage("ITACREPAR_1298"),'','string']
};

const titleHeight = 32;

// 各種IDから名称を返す
const listIdName = function( type, id ) {
  let list,idKey,nameKey,name;
  if ( type === 'input') {
    list = menuEditorArray.selectInputMethod;
    idKey = 'INPUT_METHOD_ID';
    nameKey = 'INPUT_METHOD_NAME';
  } else if ( type === 'pulldown') {
    list = menuEditorArray.selectPulldownList;
    idKey = 'LINK_ID';
    nameKey = 'LINK_PULLDOWN';
  } else if ( type === 'target') {
    list = menuEditorArray.selectParamTarget;
    idKey = 'TARGET_ID';
    nameKey = 'TARGET_NAME';
  } else if ( type === 'use') {
    list = menuEditorArray.selectParamPurpose;
    idKey = 'PURPOSE_ID';
    nameKey = 'PURPOSE_NAME';
  } else if ( type === 'group') {
    list = menuEditorArray.selectMenuGroupList;
    idKey = 'MENU_GROUP_ID';
    nameKey = 'MENU_GROUP_NAME';
  } else if ( type === 'role') {
    list = menuEditorArray.roleList;
    idKey = 'ROLE_ID';
    nameKey = 'ROLE_NAME';
  }

  const listLength = list.length;
  for ( let i = 0; i < listLength; i++ ) {
    if ( Number( list[i][idKey] ) === Number( id ) ) {
      name = list[i][nameKey];
      return name;
    }
  }
  return null;  
};

let modeDisabled = '';
if ( menuEditorMode === 'view') modeDisabled = ' disabled';

let modeKeepData = '';
if ( menuEditorMode === 'edit') modeKeepData = ' disabled';

let onHover = ' on-hover';
if ( menuEditorMode === 'edit') onHover = '';

let disbledCheckbox = '';
if ( menuEditorMode === 'edit') disbledCheckbox = ' disabled-checkbox';

// HTML
const columnHeaderHTML = ''
  + '<div class="menu-column-move" title="' + textEntities(getSomeMessage("ITACREPAR_1257"),1) + '"></div>'
  + '<div class="menu-column-title on-hover" title="' + textEntities(getSomeMessage("ITACREPAR_1256"),1) + '">'
    + '<input class="menu-column-title-input" type="text" value=""'+modeDisabled+'>'
    + '<span class="menu-column-title-dummy"></span>'
  + '</div>'
  + '<div class="menu-column-function">'
    + '<div class="menu-column-delete on-hover" title="' + textEntities(getSomeMessage("ITACREPAR_1258"),1) + '"></div>'
    + '<div class="menu-column-copy on-hover" title="' + textEntities(getSomeMessage("ITACREPAR_1259"),1) + '"></div>'
  + '</div>';

const columnEmptyHTML = ''
  + '<div class="column-empty"><p>Empty</p></div>';

const columnGroupHTML = ''
  + '<div class="menu-column-group" data-group-id="">'
    + '<div class="menu-column-group-header">'
      + columnHeaderHTML
    + '</div>'
    + '<div class="menu-column-group-body">'
    + '</div>'
  + '</div>';
  
const columnRepeatHTML = ''
  + '<div class="menu-column-repeat">'
    + '<div class="menu-column-repeat-header">'
      + '<div class="menu-column-move"></div>'
      + '<div class="menu-column-repeat-number on-hover" title="' + textEntities(getSomeMessage("ITACREPAR_1260"),1) + '">REPEAT : <input class="menu-column-repeat-number-input" data-min="2" data-max="99" value="2" type="number"'+modeDisabled+'></div>'
    + '</div>'
    + '<div class="menu-column-repeat-body">'
    + '</div>'
    + '<div class="menu-column-repeat-footer">'
      + '<div class="menu-column-function">'
        + '<div class="menu-column-delete"></div>'
      + '</div>'
    + '</div>'
  + '</div>';

// 入力方式 select
const selectInputMethodData = menuEditorArray.selectInputMethod,
      selectInputMethodDataLength = selectInputMethodData.length;
let inputMethodHTML = '';
for ( let i = 0; i < selectInputMethodDataLength ; i++ ) {
  inputMethodHTML += '<option value="' + selectInputMethodData[i].INPUT_METHOD_ID + '">' + selectInputMethodData[i].INPUT_METHOD_NAME + '</option>';
}

// プルダウン選択 select
const selectPulldownListData = menuEditorArray.selectPulldownList,
      selectPulldownListDataLength = selectPulldownListData.length;
let selectPulldownListHTML = '';
for ( let i = 0; i < selectPulldownListDataLength ; i++ ) {
  selectPulldownListHTML += '<option value="' + selectPulldownListData[i].LINK_ID + '">' + selectPulldownListData[i].LINK_PULLDOWN + '</option>';
}

//パラメータシート参照 select
const type3PulldownListData = menuEditorArray.selectReferenceSheetType3List,
      type3PulldownListDataLength = type3PulldownListData.length;
let type3PulldownListHTML = '';
for ( let i = 0; i < type3PulldownListDataLength ; i++ ) {
  type3PulldownListHTML += '<option value="' + type3PulldownListData[i].MENU_ID + '">' + type3PulldownListData[i].MENU_NAME_PULLDOWN + '</option>';
}

// 作成対象 select
if ( menuEditorMode !== 'view') {
    const selectParamTargetData = menuEditorArray.selectParamTarget,
          selectParamTargetDataLength = selectParamTargetData.length;
    let selectParamTargetHTML = '';
    for ( let i = 0; i < selectParamTargetDataLength ; i++ ) {
      selectParamTargetHTML += '<option value="' + selectParamTargetData[i].TARGET_ID + '">' + selectParamTargetData[i].TARGET_NAME + '</option>';
    }
    $('#create-menu-type').html( selectParamTargetHTML );
}

const columnHTML = ''
  + '<div class="menu-column" data-rowpan="1" data-item-id="" style="min-width: 180px">'
    + '<div class="menu-column-header">'
      + columnHeaderHTML
    + '</div>'
    + '<div class="menu-column-body">'
      + '<div class="menu-column-type" title="' + textEntities(getSomeMessage("ITACREPAR_1261"),1) + '">'
        + '<select class="menu-column-type-select"'+modeDisabled+''+modeKeepData+'>' + inputMethodHTML + '</select>'
      + '</div>'
      + '<div class="menu-column-config">'
        + '<table class="menu-column-config-table" date-select-value="1">'
          + '<tr class="multiple single link" title="' + textEntities(getSomeMessage("ITACREPAR_1262"),1) + '">'
            + '<th>' + textCode('0011') + '<span class="input_required">*</span></th>'
            + '<td><input class="config-number max-byte" type="number" data-min="1" data-max="8192" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="multiple single" title="' + textEntities(getSomeMessage("ITACREPAR_1263"),1) + '">'
            + '<th>' + textCode('0012') + '</th>'
            + '<td><input class="config-text regex" type="text" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-int" title="' + textEntities(getSomeMessage("ITACREPAR_1264"),1) + '">'
            + '<th>' + textCode('0013') + '</th>'
            + '<td><input class="config-number int-min-number" data-min="-2147483648" data-max="2147483647" type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-int" title="' + textEntities(getSomeMessage("ITACREPAR_1265"),1) + '">'
            + '<th>' + textCode('0014') + '</th>'
            + '<td><input class="config-number int-max-number" data-min="-2147483648" data-max="2147483647"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float" title="' + textEntities(getSomeMessage("ITACREPAR_1266"),1) + '">'
            + '<th>' + textCode('0013') + '</th>'
            + '<td><input class="config-number float-min-number" data-min="-99999999999999" data-max="99999999999999"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float" title="' + textEntities(getSomeMessage("ITACREPAR_1267"),1) + '">'
            + '<th>' + textCode('0014') + '</th>'
            + '<td><input class="config-number float-max-number" data-min="-99999999999999" data-max="99999999999999"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float" title="' + textEntities(getSomeMessage("ITACREPAR_1268"),1) + '">'
            + '<th>' + textCode('0015') + '</th>'
            + '<td><input class="config-number digit-number" data-min="1" data-max="14" type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="select" title="' + textEntities(getSomeMessage("ITACREPAR_1269"),1) + '">'
            + '<th>' + textCode('0016') + '<span class="input_required">*</span></th>'
            + '<td>'
              + '<select class="config-select pulldown-select"'+modeDisabled+''+modeKeepData+'>' + selectPulldownListHTML + '</select>'
            + '</td>'
          + '</tr>'
          + '<tr class="select reference" title="' + textEntities(getSomeMessage("ITACREPAR_1275"),1) + '">'
            + '<th rowspan="2">' + textCode('0043') + '</th>'
            + '<td><span type="text" class="config-text reference-item property-span" type="text" data-reference-item-id '+modeDisabled+''+modeKeepData+'></span></td>'
          + '</tr>'
          + '<tr class="select reference" title="' + textEntities(getSomeMessage("ITACREPAR_1275"),1) + '">'
            + '<td><button class="reference-item-select property-button" '+modeDisabled+''+modeKeepData+'>' + textCode('0045') + '</button></td>'
          + '</tr>'
          + '<tr class="password" title="' + textEntities(getSomeMessage("ITACREPAR_1262"),1) + '">'
            + '<th>' + textCode('0011') + '<span class="input_required">*</span></th>'
            + '<td><input class="config-number password-max-byte" type="number" data-min="1" data-max="8192" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="file" title="' + textEntities(getSomeMessage("ITACREPAR_1270"),1) + '">'
            + '<th>' + textCode('0042') + '<span class="input_required">*</span></th>'
            + '<td><input class="config-number file-max-size" data-min="1" data-max="4294967296"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '</tr>'
          + '<tr class="type3" title="' + textEntities(getSomeMessage("ITACREPAR_1299"),1) + '">'
            + '<th>' + textCode('0051') + '<span class="input_required">*</span></th>'
            + '<td>'
              + '<select class="config-select type3-reference-menu"'+modeDisabled+''+modeKeepData+'>' + type3PulldownListHTML + '</select>'
            + '</td>'
          + '</tr>'
          + '<tr class="type3" title="' + textEntities(getSomeMessage("ITACREPAR_1299"),1) + '">'
            + '<th>' + textCode('0052') + '<span class="input_required">*</span></th>'
            + '<td class="type3-item-area">'
              + '<select class="config-select type3-reference-item"'+modeDisabled+''+modeKeepData+'></select>'
            + '</td>'
          + '</tr>'
          + '<tr class="single" title="' + textEntities(getSomeMessage("ITACREPAR_1289"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><input class="config-text single-default-value" type="text" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="multiple" title="' + textEntities(getSomeMessage("ITACREPAR_1289"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><textarea class="config-textarea multiple-default-value"'+modeDisabled+'></textarea></td>'
          + '</tr>'
          + '<tr class="number-int" title="' + textEntities(getSomeMessage("ITACREPAR_1290"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><input class="config-number int-default-value" data-min="-2147483648" data-max="2147483647" type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float" title="' + textEntities(getSomeMessage("ITACREPAR_1291"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><input class="config-number float-default-value" data-min="-99999999999999" data-max="99999999999999" type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="date-time" title="' + textEntities(getSomeMessage("ITACREPAR_1292"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><input size="19" maxlength="19" class="config-text datetime-default-value callDateTimePicker" type="text" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="date" title="' + textEntities(getSomeMessage("ITACREPAR_1292"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><input size="10" maxlength="10" class="config-text date-default-value callDateTimePicker2" type="text" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="link" title="' + textEntities(getSomeMessage("ITACREPAR_1293"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td><input class="config-text link-default-value" type="text" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="select" title="' + textEntities(getSomeMessage("ITACREPAR_1294"),1) + '">'
            + '<th>' + textCode('0050') + '</th>'
            + '<td class="pulldown-default-area">'
              + '<select class="config-select pulldown-default-select"'+modeDisabled+'></select>'
            + '</td>'
          + '</tr>'
          + '<tr class="single multiple number-int number-float date-time date password select file link">'
            + '<td colspan="2">'
              + '<label class="required-label'+onHover+'" title="' + textEntities(getSomeMessage("ITACREPAR_1271"),1) + '"><input class="config-checkbox required'+disbledCheckbox+'" type="checkbox"'+modeDisabled+''+modeKeepData+'><span></span>' + textCode('0017') + '</label>'
              + '<label class="unique-label'+onHover+'" title="' + textEntities(getSomeMessage("ITACREPAR_1272"),1) + '"><input class="config-checkbox unique'+disbledCheckbox+'" type="checkbox"'+modeDisabled+''+modeKeepData+'><span></span>' + textCode('0018') + '</label>'
            + '</td>'
          + '</tr>'
          + '<tr class="all" title="' + textEntities(getSomeMessage("ITACREPAR_1273"),1) + '">'
            + '<td colspan="2"><div class="config-textarea-wrapper"><textarea class="config-textarea explanation"'+modeDisabled+'></textarea><span>' + textCode('0019') + '</span></div></td>'
          + '</tr>'
          + '<tr class="all" title="' + textEntities(getSomeMessage("ITACREPAR_1274"),1) + '">'
            + '<td colspan="2"><div class="config-textarea-wrapper"><textarea class="config-textarea note"'+modeDisabled+'></textarea><span>' + textCode('0020') + '</span></div></td>'
          + '</tr>'
        + '</table>'
      + '</div>'
    + '</div>'
    + '<div class="column-resize"></div>'
  + '</div>';




// カウンター
let itemCounter = 1,
    groupCounter = 1,
    repeatCounter = 1;

// Hover
const hoverElements = '.on-hover';
$menuTable.on({
  'mouseenter' : function() {
    if ( menuEditorMode !== 'view') $( this ).addClass('hover');
  },
  'mouseleave' : function() {
    if ( menuEditorMode !== 'view') $( this ).removeClass('hover');
  }
}, hoverElements );

const modeChange = function( mode ) {
  if ( mode !== undefined ) {
    $body.attr('data-mode', mode );
    $menuTable.addClass('hover-disabled');
  } else {
    $body.attr('data-mode', '' );
    $menuTable.removeClass('hover-disabled');
  }
}
const mode = {
  blockResize : function() { modeChange('blockResize'); },
  columnResize : function() { modeChange('columnResize'); },
  columnMove : function() { modeChange('columnMove'); },
  clear : function() { modeChange(); }
};
    
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   取り消し、やり直し
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const $undoButton = $('#button-undo'),
      $redoButton = $('#button-redo'),
      maxHistroy = 10; // 最大履歴数

let workHistory = [''],
    workCounter = 0;

// 取り消し、やり直しボタンチェック
const historyButtonCheck = function() {
    if ( workHistory[ workCounter - 1 ] !== undefined ) {
      $undoButton.prop('disabled', false );
    } else {
      $undoButton.prop('disabled', true );
    }
    if ( workHistory[ workCounter + 1 ] !== undefined ) {
      $redoButton.prop('disabled', false );
    } else {
      $redoButton.prop('disabled', true );
    }
};

// 履歴管理
const history = {
  'add' : function() {
    workCounter++;
    const $clone = $menuTable.clone();
    $clone.find('.hover').removeClass('hover');
    workHistory[ workCounter ] = $clone.html();

    // 履歴追加後の履歴を削除する
    if ( workHistory[ workCounter + 1 ] !== undefined ) {
      workHistory.length = workCounter + 1;
    } 
    // 最大履歴数を超えた場合最初の履歴を削除する
    if ( workHistory.length > maxHistroy ) {
      workHistory.shift();
      workCounter--;
    }

    historyButtonCheck();
  },
  'undo' : function() {
    workCounter--;
    $menuTable.html( workHistory[ workCounter ] );
    historyButtonCheck();
    previewTable();
    resetSelect2( $menuTable );
    resetDatetimepicker( $menuTable );
    resetEventPulldownDefaultValue( $menuTable );
    resetEventPulldownParameterSheetReference( $menuTable );
    updateUniqueConstraintDispData();
  },
  'redo' : function() {
    workCounter++;
    $menuTable.html( workHistory[ workCounter ] );
    historyButtonCheck();
    previewTable();
    resetSelect2( $menuTable );
    resetDatetimepicker( $menuTable );
    resetEventPulldownDefaultValue( $menuTable );
    resetEventPulldownParameterSheetReference( $menuTable );
    updateUniqueConstraintDispData();
  },
  'clear' : function() {
    workCounter = 0;
    workHistory = [];
    workHistory[ workCounter ] = $menuTable.html();
    historyButtonCheck();
  }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   列の追加
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const addColumn = function( $target, type, number, loadData, previewFlag, emptyFlag ) {

  if ( loadData === undefined ) loadData = false;
  if ( previewFlag === undefined ) previewFlag = true;
  if ( emptyFlag === undefined ) emptyFlag = true;

  let html = '',
      id = '',
      title = '',
      name;
  
  switch( type ) {
    case 'item':
      html = columnHTML;
      name = loadData['ITEM_NAME'];
      id = 'i' + number;
      title = textCode('0000');
      break;
    case 'group':
      html = columnGroupHTML;
      name = loadData['COL_GROUP_NAME']
      id = 'g' + number;
      title = textCode('0001');
      break;
    case 'repeat':
      html = columnRepeatHTML;
      id = 'r' + number;
      break;
  }

  const $addColumn = $( html ),
        $addColumnInput = $addColumn.find('.menu-column-title-input');
  
  $target.append( $addColumn );
  // プルダウンにselect2を適用する
  $target.find('.config-select').select2();
  
  $addColumn.attr('id', id );

  if ( type !== 'item' && emptyFlag === true ) {
    $addColumn.find('.menu-column-group-body, .menu-column-repeat-body').html( columnEmptyHTML );
  }
  
  if ( loadData === false ) {
    // 自動付加する名前が被ってないかチェックする
    const checkName = function( name ) {
      let nameList = [];
      $menuEditor.find('.menu-column-title-input').each( function( i ){
        nameList[ i ] = $( this ).val();
      });
      let condition = true;
      while( condition ) {
        if ( nameList.indexOf( name ) !== -1 ) {
          number++;
          name = title + ' ' + number;
        } else {
          condition = false;
        }
      }
      return name;
    }
    $addColumnInput.val( checkName( title + ' ' + number ) );
  } else {
    $addColumnInput.val( name );
  }
  
  titleInputChange( $addColumnInput );
  columnHeightUpdate();
  
  if ( previewFlag === true ) {
    history.add();
    previewTable();
  }
  
  // 追加した項目に合わせスクロールさせる
  const editorWindowWidth = $menuEditWindow.outerWidth(),
        tableWidth = $menuEditWindow.find('.menu-table-wrapper').width()
  if ( editorWindowWidth < tableWidth ) {
    $menuEditWindow.children().stop(0,0).animate({'scrollLeft': tableWidth - editorWindowWidth }, 200 );
  }

  //日付と時日時の初期値入力欄にdatetimepickerを設定
  $addColumn.find(".callDateTimePicker").datetimepicker({format:'Y/m/d H:i:s', step:5, lang:LangStream});
  $addColumn.find(".callDateTimePicker2").datetimepicker({timepicker:false, format:'Y/m/d', lang:LangStream});

  emptyCheck();

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   参照項目選択
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

//参照項目を選択するモーダル表示イベント
$menuEditor.on('click', '.reference-item-select', function() {
  itaModalOpen(textCode('0049'), modalReferenceItemList, 'reference' , $(this));
});

//選択項目変更時、参照項目を空にする
$menuEditor.on('change', '.pulldown-select', function(){
  const $input = $(this).closest('.menu-column-config-table').find('.reference-item');
  $input.attr('data-reference-item-id', '');
  $input.html('');
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   プルダウン選択の初期値
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const setEventPulldownDefaultValue = function($item){
  $item.on('change', '.menu-column-type-select, .pulldown-select', function(){
    let typeId;

    if($(this).hasClass('menu-column-type-select')){
      typeId = $(this).val();
    }

    if($(this).hasClass('pulldown-select')){
      typeId = $item.find('.menu-column-type-select').val();
    }

    //「プルダウン選択」時のみ
    if(typeId == 7){
      getpulldownDefaultValueList($item, "");
    }

  });
}

const getpulldownDefaultValueList = function($item, defaultValue = ""){
      let loadNowSelect = '<option value="">'+getSomeMessage("ITACREPAR_1295")+'</option>';
      let faildSelect = '<option value="">'+getSomeMessage("ITACREPAR_1288")+'</option>';
      $item.find('.pulldown-default-select').html(loadNowSelect); //最初に読み込み中メッセージのセレクトボックスを挿入
      const selectLinkId = $item.find('.pulldown-select option:selected').val();

      //「選択項目」のメニューで初期値として利用可能な値のリストを作成する。
      let selectDefaultValueList;
      const printselectDefaultValueURL = '/common/common_printSelectDefaultValue.php?link_id=' + selectLinkId + '&user_id=' +gLoginUserID;
      $.ajax({
        type: 'get',
        url: printselectDefaultValueURL,
        dataType: 'text'
      }).done( function( result ) {
          if(JSON.parse( result ) == 'failed'){
            selectDefaultValueList = null;
            //エラーメッセージ入りセレックとボックスを挿入
            $item.find('.pulldown-default-select').html(faildSelect);
            history.add(); //historyを更新
          }else{
            //選択可能な参照項目の一覧を取得し、セレクトボックスを生成
            selectDefaultValueList = JSON.parse( result );
            if ( selectDefaultValueList[0] == 'redirectOrderForHADACClient' ) {
              window.alert( selectDefaultValueList[2] );
              var redirectUrl = selectDefaultValueList[1][1] + location.search.replace('?','&');
              return redirectTo(selectDefaultValueList[1][0], redirectUrl, selectDefaultValueList[1][2]);
            }
            const selectPulldownDefaultListLength = selectDefaultValueList.length;
            let selectPulldownDefaultListHTML = '<option value=""></option>'; //一つ目に空を追加
            let defaultCheckFlg = false;
            for ( let i = 0; i < selectPulldownDefaultListLength ; i++ ) {
              if(defaultValue == selectDefaultValueList[i].id){
                selectPulldownDefaultListHTML += '<option value="' + selectDefaultValueList[i].id + '" selected>' + selectDefaultValueList[i].value + '</option>';
                defaultCheckFlg = true;
              }else{
                selectPulldownDefaultListHTML += '<option value="' + selectDefaultValueList[i].id + '">' + selectDefaultValueList[i].value + '</option>';
              }
            }
            //デフォルト値を持っているが一致するレコードが無い場合、ID変換失敗(ID)の選択肢を追加。
            if(defaultCheckFlg == false && defaultValue){
              selectPulldownDefaultListHTML += '<option value="' + defaultValue + '" selected>' + getSomeMessage("ITACREPAR_1255", {0:defaultValue}) + '</option>';
            }
            $item.find('.pulldown-default-select').html(selectPulldownDefaultListHTML);
            history.add(); //historyを更新
          }

      }).fail( function( result ) {
        selectDefaultValueList = null;
        //エラーメッセージ入りセレックとボックスを挿入
        $item.find('.pulldown-default-select').html(faildSelect);
        history.add(); //historyを更新
      });
}

const resetEventPulldownDefaultValue = function($menuTable){
  const $item = $menuTable.find('.menu-column');
  $item.each(function(){
    setEventPulldownDefaultValue($(this));
  });

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   パラメータシート参照の項目取得
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const setEventPulldownParameterSheetReference = function($item){
  $item.on('change', '.menu-column-type-select, .type3-reference-menu', function(){
    let typeId;

    if($(this).hasClass('menu-column-type-select')){
      typeId = $(this).val();
    }

    if($(this).hasClass('type3-reference-menu')){
      typeId = $item.find('.menu-column-type-select').val();
    }

    //「パラメータシート参照」時のみ
    if(typeId == 11){
      getpulldownParameterSheetReferenceList($item, "");
    }

  });
}

const getpulldownParameterSheetReferenceList = function($item, itemId = ""){
  let loadNowSelect = '<option value="">'+getSomeMessage("ITACREPAR_1295")+'</option>';
  let faildSelect = '<option value="">'+getSomeMessage("ITACREPAR_1300")+'</option>';
  $item.find('.type3-reference-item').html(loadNowSelect); //最初に読み込み中メッセージのセレクトボックスを挿入
  const selectMenuId = $item.find('.type3-reference-menu option:selected').val();

  //「選択項目」のメニューで初期値として利用可能な値のリストを作成する。
  let selectParameterSheetReferenceList;
  const printselectParameterSheetReferenceURL = '/common/common_printParameterSheetReference.php?menu_id=' + selectMenuId + '&user_id=' +gLoginUserID;
  $.ajax({
    type: 'get',
    url: printselectParameterSheetReferenceURL,
    dataType: 'text'
  }).done( function( result ) {
      if(JSON.parse( result ) == 'failed'){
        selectParameterSheetReferenceList = null;
        //エラーメッセージ入りセレックとボックスを挿入
        $item.find('.type3-reference-item').html(faildSelect);
        history.add(); //historyを更新
      }else{
        //選択可能な参照項目の一覧を取得し、セレクトボックスを生成
        selectParameterSheetReferenceList = JSON.parse( result );
        const selectParameterSheetReferenceListLength = selectParameterSheetReferenceList.length;
        let selectParameterSheetReferenceListHTML = '<option value=""></option>'; //一つ目に空を追加
        let referenceCheckFlg = false;
        for ( let i = 0; i < selectParameterSheetReferenceListLength ; i++ ) {
          if(itemId == selectParameterSheetReferenceList[i].itemId){
            selectParameterSheetReferenceListHTML += '<option value="' + selectParameterSheetReferenceList[i].itemId + '" selected>' + selectParameterSheetReferenceList[i].itemPulldown + '</option>';
            referenceCheckFlg = true;
          }else{
            selectParameterSheetReferenceListHTML += '<option value="' + selectParameterSheetReferenceList[i].itemId + '">' + selectParameterSheetReferenceList[i].itemPulldown + '</option>';
          }
        }
        //選択項目を持っているが一致するレコードが無い場合、ID変換失敗(ID)の選択肢を追加。
        if(referenceCheckFlg == false && itemId){
          selectParameterSheetReferenceListHTML += '<option value="' + itemId + '" selected>' + getSomeMessage("ITACREPAR_1255", {0:itemId}) + '</option>';
        }
        $item.find('.type3-reference-item').html(selectParameterSheetReferenceListHTML);
        history.add(); //historyを更新
      }

  }).fail( function( result ) {
    selectParameterSheetReferenceList = null;
    //エラーメッセージ入りセレックとボックスを挿入
    $item.find('.type3-reference-item').html(faildSelect);
    history.add(); //historyを更新
  });
}

const resetEventPulldownParameterSheetReference = function($menuTable){
  const $item = $menuTable.find('.menu-column');
  $item.each(function(){
    setEventPulldownParameterSheetReference($(this));
  });

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$menuEditor.find('.menu-editor-menu-button').on('click', function() {
  const $button = $( this ),
        buttonType = $button.attr('data-menu-button');
  switch ( buttonType ) {
    case 'newColumn':
      const currentItemCounter = itemCounter;
      addColumn( $menuTable, 'item', itemCounter++ );
      const $newColumnTarget = $menuEditor.find('#i'+currentItemCounter);
      //editの場合disabledを外す。
      if(menuEditorMode === 'edit'){
        $newColumnTarget.find('.menu-column-type-select').prop('disabled', false); //カラムタイプ
        $newColumnTarget.find('.config-select'+'.pulldown-select').prop('disabled', false); //選択項目
        $newColumnTarget.find('.config-text'+'.reference-item').prop('disabled', false); //参照項目
        $newColumnTarget.find('.config-select'+'.type3-reference-menu').prop('disabled', false); //パラメータシート参照(メニュー選択)
        $newColumnTarget.find('.config-select'+'.type3-reference-item').prop('disabled', false); //パラメータシート参照(項目選択)
        $newColumnTarget.find('.reference-item-select').prop('disabled', false); //参照項目を選択ボタン
        $newColumnTarget.find('.config-checkbox'+'.required').prop('disabled', false); //必須
        $newColumnTarget.find('.config-checkbox'+'.unique').prop('disabled', false); //一意制
        $newColumnTarget.find('.config-checkbox'+'.required').removeClass('disabled-checkbox'); //必須のチェックボックスの色約
        $newColumnTarget.find('.config-checkbox'+'.unique').removeClass('disabled-checkbox'); //一意制約のチェックボックスの色
        $newColumnTarget.find('.required-label').addClass('on-hover'); //必須のカーソル動作
        $newColumnTarget.find('.unique-label').addClass('on-hover'); //一意制約のカーソル動作
      }
      //プルダウン選択の初期値を取得するイベントを設定
      setEventPulldownDefaultValue($newColumnTarget);
      //パラメータシート参照の選択項目を取得するイベントを設定
      setEventPulldownParameterSheetReference($newColumnTarget);
      break;
    case 'newColumnGroup':
      addColumn( $menuTable, 'group', groupCounter++ );
      break;
    case 'newColumnRepeat':
      if ( $menuTable.find('.menu-column-repeat').length !== 0 ) return false;
      $button.prop('disabled', true );
      addColumn( $menuTable, 'repeat', repeatCounter );
      break;
    case 'undo':
      history.undo();
      break;
    case 'redo':
      history.redo();
      break;
    case 'registration':
      if ( !window.confirm(getSomeMessage("ITACREPAR_1201") ) ) return false;
      createRegistrationData('registration');
      break;
    case 'update-initialize':
      //メニュー作成状態が「未作成」の場合、windowメッセージを変更
      if(menuEditorArray['selectMenuInfo']['menu']['MENU_CREATE_STATUS'] == 1){
        if ( !window.confirm(getSomeMessage("ITACREPAR_1201")) ) return false;
      }else{
        if ( !window.confirm(getSomeMessage("ITACREPAR_1250")) ) return false;
      }
      createRegistrationData('update-initialize');
      break;
    case 'update':
      if ( !window.confirm(getSomeMessage("ITACREPAR_1249") ) ) return false;
      createRegistrationData('update');
      break;
    case 'initialize':
    case 'reload-initialize':
      // 初期化モードで開きなおす
      location.href = '/default/menu/01_browse.php?no=2100160011&create_menu_id=' + menuEditorTargetID + '&mode=initialize';
      break;
    case 'edit':
    case 'reload':
      // 編集モードで開きなおす
      location.href = '/default/menu/01_browse.php?no=2100160011&create_menu_id=' + menuEditorTargetID + '&mode=edit';
      break;
    case 'diversion':
      // 流用新規モードで開きなおす
      location.href = '/default/menu/01_browse.php?no=2100160011&create_menu_id=' + menuEditorTargetID + '&mode=diversion';
      break;
    case 'cancel':
      // 閲覧モードで開きなおす
      location.href = '/default/menu/01_browse.php?no=2100160011&create_menu_id=' + menuEditorTargetID;
      break;
  }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   タイトル入力幅調整
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const titleInputChange = function( $input ) {
  const inputValue = $input.val();
  $input.next().text( inputValue );
  const inputWidth = $input.next().outerWidth() + 6;
  $input.attr('value', inputValue ).css('width', inputWidth );
};
$('.menu-column-title-input').each( function(){
  titleInputChange( $(this) );
});
$menuEditor.on({
  'input' : function () {
    if ( $( this ).is('.menu-column-title-input') ) {
      titleInputChange( $( this ) );
    }
  },
  'change' : function() {
    if ( $( this ).is('.menu-column-title-input') ) {
      history.add();
      previewTable();
      updateUniqueConstraintDispData();
    }
  },
  'focus' : function() {
    // $(this).focus().select(); Edge対応版
    $( this ).click( function(){
      $( this ).select();
    });
  },
  'blur' : function() {
    getSelection().removeAllRanges();
  },
  'mousedown' : function( e ) {
    e.stopPropagation();
  }
}, '.menu-column-title-input, .menu-column-repeat-number-input');

// input欄外でも選択可能にする
$menuEditor.on({
  'mousedown' : function() {
    if ( menuEditorMode !== 'view') {
      const $input = $( this );
      setTimeout( function(){
        $input.find('.menu-column-title-input, .menu-column-repeat-number-input').focus().select();
      }, 1 );
    }
  }
}, '.menu-column-title, .menu-column-repeat-number');

$menuEditor.on({
  'focus' : function() {
    $( this ).addClass('text-in');
  },
  'blur' : function() {
    if ( $( this ).val() === '' ) {
      $( this ).removeClass('text-in');
    }
  }
}, '.config-textarea');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   設定内容をHTMLに反映させる（履歴HTML用）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$menuEditor.on('change', '.config-text', function() {
  $( this ).attr('value', $( this ).val() );
  previewTable();
  history.add();
});
$menuEditor.on('change', '.config-number, .menu-column-repeat-number-input, .property-number', function() {
  const $input = $( this ),
        min = Number( $input.attr('data-min') ),
        max = Number( $input.attr('data-max') );
  let value = $input.val();
  
  // 桁数が未入力の場合、最大値を入れる
  if ( $input.is('.digit-number') && value === '') {
    value = max;
  }
  if ( min !== undefined && value < min ) value = min;
  if ( max !== undefined && value > max ) value = max;
  
  $input.val( value ).attr('value', value );
  
  previewTable();
  history.add();
});
$menuEditor.on('change', '.config-textarea', function() {
  $( this ).text( $( this ).val() );
  previewTable();
  history.add();
});
$menuEditor.on('change', '.config-checkbox', function() {
  $( this ).attr('checked', $( this ).prop('checked') );
  previewTable();
  history.add();
});
$menuEditor.on('change', '.config-select', function() {
  const $select = $( this ),
        value = $select.val();
  // selectedを削除してからだと画面に反映されない？
  $select.find('option[value="' + value + '"]').attr('selected', 'selected');
  $select.find('option').not('[value="' + value + '"]').attr('selected', false);
  previewTable();
  history.add();
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目の種類によって入力項目を切り替える
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$menuEditor.on('change', '.menu-column-type-select', function() {
  const $select = $( this ),
        $config = $select.closest('.menu-column-type')
          .next('.menu-column-config').find('.menu-column-config-table'),
        value = $select.val();
  $config.attr('date-select-value', value );
  $select.find('option[value="' + value + '"]').attr('selected', true);
  $select.find('option').not('[value="' + value + '"]').attr('selected', false);
  previewTable();
  history.add();
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目、グループの移動
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$menuEditor.on('mousedown', '.menu-column-move', function( e ){

  // 左クリックチェック
  if (e.which !== 1 ) return false;
  
  // 選択を解除しておく
  getSelection().removeAllRanges();
  
  mode.columnMove();
  
  const $column = $( this ).closest('.menu-column, .menu-column-group, .menu-column-repeat'),
        $columnClone = $column.clone( false ),
        $targetArea = $('.menu-column, .menu-column-group-header, .menu-column-repeat-header, .menu-column-repeat-footer, .column-empty'),
        scrollTop = $window.scrollTop(),
        scrollLeft = $window.scrollLeft(),
        knobWidth = $( this ).outerWidth(),
        knobHeight = $( this ).outerHeight(),
        mousedownPositionX = e.pageX,
        mousedownPositionY = e.pageY,
        editorX = $menuEditor.offset().left,
        editorWidth = $menuEditWindow.outerWidth();
  
  // 何を移動するか
  const moveColumnType = $column.attr('class');
  $menuTable.attr('data-move-type', moveColumnType );
        
  // スクロール可能か調べる
  const $scrollArea = $menuEditWindow.find('.menu-editor-block-inner'),
        scrollWidth = $scrollArea.get(0).scrollWidth + 2,
        scrollAreaWidth = $scrollArea.outerWidth(),
        scrollFlag = ( scrollWidth > scrollAreaWidth ) ? true : false,
        scrollSpeed = 40;
        
  let scrollTimer = false;

  const scrollMove = function( direction ) {
    if ( scrollTimer === false ) {
      scrollTimer = setInterval( function() {
        const scrollLeft = $scrollArea.scrollLeft();
        let scrollWidth = ( direction === 'right' ) ? scrollSpeed : -scrollSpeed;
        $scrollArea.stop(0,0).animate({ scrollLeft : scrollLeft + scrollWidth }, 40, 'linear' );
      }, 40 );
    }
  };
  
  let $hoverTarget = null,
      hoverTargetWidth, hoverTargetLeft,
      moveX, moveY;
  
  $column.addClass('move-wait');
  
  // 移動用ダミーオブジェ追加
  $menuEditor.append( $columnClone );
  $columnClone.addClass('move').css({
    'left' : ( mousedownPositionX - scrollLeft - knobWidth / 2 ) + 'px',
    'top' : ( mousedownPositionY - scrollTop - knobHeight / 2 ) + 'px'
  });
  
  // ターゲットの左か右かチェックする
  const leftRightCheck = function( mouseX ) {
    if ( $hoverTarget !== null ) {
      if ( $hoverTarget.parent().is('.menu-column-repeat') ) {
        // リピート
        const $repeatColumn = $hoverTarget.parent('.menu-column-repeat');
        if ( $hoverTarget.is('.menu-column-repeat-header')
             && !$repeatColumn.prev().is( $column ) ) {
          $repeatColumn.addClass('left');
        } else if ( $hoverTarget.is('.menu-column-repeat-footer')
             && !$repeatColumn.next().is( $column ) ) {
          $repeatColumn.addClass('right');
        }
      } else if ( $hoverTarget.is('.column-empty') ) {
        // 空エリアの場合何もしない
        return false;
      } else {
        // その他
        const mousePositionX = mouseX - hoverTargetLeft;
        if ( hoverTargetWidth / 2 > mousePositionX ) {
          $hoverTarget.removeClass('right');
          if ( !$hoverTarget.prev().is( $column ) ) {
            $hoverTarget.addClass('left');
          }
        } else {
          $hoverTarget.removeClass('left');
          if ( !$hoverTarget.next().is( $column ) ) {
            $hoverTarget.addClass('right');
          }
        }
      }
    }
  }
  
  // どこの上にいるか
  $targetArea.on({
    'mouseenter.columnMove' : function( e ){
      e.stopPropagation();
      // 対象情報
      $hoverTarget = $( this );
      hoverTargetWidth = $hoverTarget.outerWidth();
      hoverTargetLeft = scrollLeft + $hoverTarget.offset().left;
      // 対象が自分以外かどうか
      if ( !$hoverTarget.is( $column ) ) {
        if ( $hoverTarget.is('.menu-column-group-header') ) {
          $hoverTarget = $hoverTarget.closest('.menu-column-group');
        }
        $hoverTarget.addClass('hover');
        $hoverTarget.parents('.menu-column-group, .menu-column-repeat-body').eq(0).addClass('hover-parent');
      } else {
        $hoverTarget = null;
      }
      
      leftRightCheck( e.pageX );
    },
    'mouseleave.columnMove' : function(){
      $hoverTarget = null;
      $menuTable.find('.hover, .hover-parent, .left, .right').removeClass('hover hover-parent left right');
    }
  });
  
  let moveTime = '';
  
  $window.on({
    'mousemove.columnMove' : function( e ) {
      // 仮移動
      if ( moveTime === '') {
        moveX = e.pageX - mousedownPositionX;
        moveY = e.pageY - mousedownPositionY;
        $columnClone.css('transform', 'translate(' + moveX + 'px,' + moveY + 'px)');
        leftRightCheck( e.pageX );
      
        // 枠の外に移動
        if ( scrollFlag === true ) {
          if ( editorX > e.pageX ) {
            scrollMove('left');
          } else if ( editorX + editorWidth < e.pageX ) {
            scrollMove('right');
          } else if ( scrollTimer ){
            clearInterval( scrollTimer );
            scrollTimer = false;
          }
        }

        moveTime = setTimeout( function() {
          moveTime = '';
        }, 16.667 );
      }
    },
    'mouseup.columnMove' : function() {
      // 対象があれば移動する
      if ( $hoverTarget !== null ) {
        // 移動した際にグループの中身が空になるか判定
        const $parentGroup = $column.parent().closest('.menu-column-group, .menu-column-repeat');
        let emptyFlag = false;
        if ( $parentGroup.length && $column.siblings().length === 0 ) {
          emptyFlag = true;
        }
        // 移動する or 空のグループに追加
        if ( $hoverTarget.is('.column-empty') ) {
          $hoverTarget.closest('.menu-column-group-body, .menu-column-repeat-body').html('').append( $column );
        } else {
          // 右か左か
          if ( $hoverTarget.parent().is('.menu-column-repeat') ) {
            $hoverTarget = $hoverTarget.closest('.menu-column-repeat');
          }
          if ( $hoverTarget.is('.left') ) {
            $column.insertBefore( $hoverTarget );
          } else if ( $hoverTarget.is('.right') ) {
            $column.insertAfter( $hoverTarget );
          }
        }
        // グループが空ならEmpty追加
        if ( emptyFlag === true ) {
          $parentGroup.find('.menu-column-group-body, .menu-column-repeat-body').html( columnEmptyHTML );
        }
        // 高さ更新
        columnHeightUpdate();
      }
      $column.removeClass('move-wait');
      $columnClone.remove();
      $menuTable.find('.hover, .hover-parent, .left, .right').removeClass('hover hover-parent left right');
      $menuTable.removeAttr('data-move-type', moveColumnType );
      $window.off('mousemove.columnMove mouseup.columnMove');
      $targetArea.off('mouseenter.columnMove mouseleave.columnMove');
      clearInterval( scrollTimer );
      mode.clear();
      // 移動した場合のみ履歴追加
      if ( $hoverTarget !== null ) {
        history.add();
      }
      previewTable();
    }
  });
  
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目、グループの削除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 項目が空かチェックする
const emptyCheck = function() {
  const itemLength = $menuTable.find('.menu-column, .menu-column-group, .menu-column-repeat').length;
  if ( itemLength === 0 ) {
    $menuTable.html('<div class="no-set column-empty"><p>Empty</p></div>');
  } else {
    $menuTable.find('.no-set').remove();
  }
};
// リピートがあるかチェックする
const repeatCheck = function() {
  const $repeatButton = $('.menu-editor-menu-button[data-menu-button="newColumnRepeat"]'),
        type = $('#create-menu-type').val();
  // パラメータシートかつ、縦メニュー利用有無チェック
  if ( ( type === '1' || type === '3' ) && $('#create-menu-use-vertical').prop('checked') ) {
    if ( $menuTable.find('.menu-column-repeat').length === 0 ) {
      $repeatButton.prop('disabled', false );
    } else {
      $repeatButton.prop('disabled', true );
    }
  } else {
    $repeatButton.prop('disabled', true );
  }
};

$menuEditor.on('click', '.menu-column-delete', function(){
  const $column = $( this ).closest('.menu-column, .menu-column-group, .menu-column-repeat');
  // 親列グループが空になる場合
  const $parentGroup = $column.parent().closest('.menu-column-group, .menu-column-repeat');
  if ( $parentGroup.length && $column.siblings().length === 0 ) {
    $parentGroup.find('.menu-column-group-body, .menu-column-repeat-body').html( columnEmptyHTML );
  }
  $column.remove();
  
  if ( $menuEditor.find('.menu-column, .menu-column-group, .menu-column-repeat').length ) {
    // 高さ更新
    columnHeightUpdate();
  }
  history.add();
  emptyCheck();
  repeatCheck();
  previewTable();
  const columnId = $column.attr('id');
  deleteUniqueConstraintDispData(columnId); //一意制約(複数項目)で削除した項目を除外する。
  
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目、グループの複製
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// select2を再適用
const resetSelect2 = function( $target ) {
    if ( $target.find('.select2-container').length ) {
      // select2要素を削除
      $target.find('.config-select').removeClass('select2-hidden-accessible').removeAttr('tabindex aria-hidden');
      $target.find('.select2-container').remove();
      // select2を再適用
      $target.find('.config-select').select2();
    }
};

// datetimepickerを再適用
const resetDatetimepicker = function ( $target ) {
    if ( $target.find('.callDateTimePicker').length ) {
      //既存の要素を削除
      $target.find(".callDateTimePicker").datetimepicker("destroy");
      $target.find(".callDateTimePicker2").datetimepicker("destroy");
      $('.xdsoft_datetimepicker').remove();
      // datetimepickeを再適用
      $target.find(".callDateTimePicker").datetimepicker({format:'Y/m/d H:i:s', step:5, lang:LangStream});
      $target.find(".callDateTimePicker2").datetimepicker({timepicker:false, format:'Y/m/d', lang:LangStream});

    }
}

$menuEditor.on('click', '.menu-column-copy', function(){
  const $column = $( this ).closest('.menu-column, .menu-column-group');
  
  // リピートを含む要素はコピーできないようにする
  if ( $column.find('.menu-column-repeat').length ) {
    alert(textCode('0035'));
    return false;
  }
  
  const $clone = $column.clone();
  $column.after( $clone );  
  
  // 追加を待つ
  $clone.ready( function() {
    
    resetSelect2( $clone );
    
    $clone.find('.hover').removeClass('hover');
    
    // IDをプラス・名前にコピー番号を追加
    $clone.find('.menu-column-title-input').each( function() {
      const $input = $( this ),
            title = $input.val(),
            $eachColumn = $input.closest('.menu-column, .menu-column-group');
      
      if ( $eachColumn.is('.menu-column') ) {
        const i = itemCounter++;
        $input.val( title + '(' + i + ')' );
        $eachColumn.attr({
          'id': 'i' + i,
          'data-item-id': ''
        });
      } else if ( $eachColumn.is('.menu-column-group') ) {
        const g = groupCounter++;
        $input.val( title + '(' + g + ')' );
        $eachColumn.attr({
          'id': 'g' + g,
          'data-group-id': ''
        });
      }
      $input.attr('value', $input.val() );
      titleInputChange( $input );
    });

    //日付と時日時の初期値入力欄にdatetimepickerを設定
    $clone.find(".callDateTimePicker").datetimepicker({format:'Y/m/d H:i:s', step:5, lang:LangStream});
    $clone.find(".callDateTimePicker2").datetimepicker({timepicker:false, format:'Y/m/d', lang:LangStream});
    // プルダウン選択の初期値取得eventを再適用する
    resetEventPulldownDefaultValue( $menuTable );
    // パラメータ参照の項目取得eventを再適用する
    resetEventPulldownParameterSheetReference( $menuTable );

    history.add();
    previewTable();
  });
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   行数を調べる
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const rowNumberCheck = function(){
  let maxLevel = 1;
  $menuTable.find('.menu-column, .column-empty').each( function(){
    const $column = $( this ),
          columnLevel = $column.parents('.menu-column-group').length + 1;
    if ( maxLevel < columnLevel ) maxLevel = columnLevel;
  });
  return maxLevel;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   列の高さ更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const columnHeightUpdate = function(){

  const maxLevel = rowNumberCheck();
  
  // 列の高さ調整
  $menuTable.find('.menu-column').each( function(){
    const $column = $( this ),
          columnLevel = $column.parents('.menu-column-group').length,
          rowspan = maxLevel - columnLevel;
    $column.attr('data-rowpan', rowspan );
    $column.find('.menu-column-header').css('height', titleHeight * rowspan );
  });
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   リピート数変更
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$menuTable.on({
  'click' : function() {
    $( this ).addClass()
  }
}, '.menu-column-repeat-number');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   プレビュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// プレビュー用HTML
const sortMark = '<span class="sortMarkWrap"><span class="sortNotSelected"></span></span>',
      tHeadHeaderLeftHTML = ''
        + '<th rowspan="{{rowspan}}" class="thSticky left"><span class="generalBold">No</span>' + sortMark + '</th>',
      tHeadParameterHeaderLeftHTML = ''
        + '<th rowspan="{{rowspan}}"><span class="generalBold">' + textCode('0021') + '</span>' + sortMark + '</th>'
        + '<th colspan="4"><span class="generalBold">' + textCode('0022') + '</span></th>'
        + '<th colspan="{{colspan}}"><span class="generalBold">' + textCode('0023') + '</span></th>',
      tHeadOperationrHeaderLeftHTML = ''
        + '<th colspan="4"><span class="generalBold">' + textCode('0022') + '</span></th>'
        + '<th colspan="{{colspan}}"><span class="generalBold">' + textCode('0023') + '</span></th>',  
      tHeadParameterOpeHeaderLeftHTML = ''
        + '<th rowspan="{{rowspan}}"><span class="generalBold">' + textCode('0024') + '</span>' + sortMark + '</th>'
        + '<th rowspan="{{rowspan}}"><span class="generalBold">' + textCode('0025') + '</span>' + sortMark + '</th>'
        + '<th rowspan="{{rowspan}}"><span class="generalBold">' + textCode('0026') + '</span>' + sortMark + '</th>'
        + '<th rowspan="{{rowspan}}"><span class="generalBold">' + textCode('0027') + '</span>' + sortMark + '</th>',
      tHeadHeaderRightHTML = ''
        + '<th rowspan="{{rowspan}}" class="sortTriggerInTbl" ><span class="generalBold">' + textCode('0028') + '</span>' + sortMark + '</th>'
        + '<th rowspan="{{rowspan}}" class="sortTriggerInTbl thSticky right" ><span class="generalBold">' + textCode('0029') + '</span>' + sortMark + '</th>'
        + '<th rowspan="{{rowspan}}" class="sortTriggerInTbl thSticky right"><span class="generalBold">' + textCode('0030') + '</span>' + sortMark + '</th>',
      tBodyHeaderLeftHTML = ''
        + '<td class="likeHeader number">{{id}}</td>',
      tBodyParameterHeaderLeftHTML = ''
        + '<td>192.168.0.1</td>'
        + '<td>' + textCode('0031') + '</td>'
        + '<td>2020/01/01 00:00</td>'
        + '<td>2020/01/01 00:00</td>'
        + '<td></td>',
      tBodyOperationHeaderLeftHTML = ''
        + '<td>' + textCode('0031') + '</td>'
        + '<td>2020/01/01 00:00</td>'
        + '<td>2020/01/01 00:00</td>'
        + '<td></td>',
      tBodyHeaderRightHTML = ''
        + '<td></td>'
        + '<td class="likeHeader">2020/01/01 00:00:00</td>'
        + '<td class="likeHeader">' + textCode('0032') + '</td>';

// リピートを含めた子の列数を返す
const childColumnCount = function( $column, type ) {
  let counter = $column.find('.menu-column, .column-empty').length;
  const menuColumnBody = $column.find('.menu-column');
  menuColumnBody.each(function(){
    const selectTypeValue = $(this).find('.menu-column-type-select').val();
    //プルダウン選択の場合、参照項目の数だけcounterを追加
    if(selectTypeValue == '7'){
        const referenceItem = $(this).find('.reference-item');
        referenceItem.each(function(){
            //リピート内の場合はカウントしない
            if($(this).parents('.menu-column-repeat').length == 0){
                const referenceItemValue = $( this ).attr('data-reference-item-id');
                if(referenceItemValue != null && referenceItemValue != ""){ //空もしくはundefinedではない場合
                    const referenceItemAry = referenceItemValue.split(',');
                    counter = counter + referenceItemAry.length;
                }
            }

            //リピート内かつグループ内の場合はカウント
            if(type == 'group' && $column.parents('.menu-column-repeat').length != 0 && $column.find('.menu-column-group-header').length != 0){
                const referenceItemValue = $( this ).attr('data-reference-item-id');
                if(referenceItemValue != null && referenceItemValue != ""){ //空もしくはundefinedではない場合
                    const referenceItemAry = referenceItemValue.split(',');
                    counter = counter + referenceItemAry.length;
                }
            }
        });
    }
  });

  $column.find('.menu-column-repeat').each( function() {
    const columnLength = $( this ).find('.menu-column, .column-empty').length;
    if ( columnLength !== 0 ) {
        const repeatNumberInput = Number( $( this ).find('.menu-column-repeat-number-input').val());
        let referenceItemCount = 0;
        //プルダウン選択の場合、参照項目の数だけcounterを追加
        const repeatMenuColumnBody = $(this).find('.menu-column');
        repeatMenuColumnBody.each(function(){
            const repeatSelectTypeValue = $(this).find('.menu-column-type-select').val();
            if(repeatSelectTypeValue == '7'){
                const referenceItem = $(this).find('.reference-item');
                referenceItem.each(function(){
                    const referenceItemValue = $(this).attr('data-reference-item-id');
                    if(referenceItemValue != null && referenceItemValue != ""){ //空もしくはundefinedではない場合
                        const referenceItemAry = referenceItemValue.split(',');
                        referenceItemCount = referenceItemCount + Number( referenceItemAry.length );
                    }
                });
            }
        });

        counter = counter + ( (columnLength * (repeatNumberInput -1)) + ((referenceItemCount) * repeatNumberInput) );
    }
  });

  return counter;
}

// プレビューを表示する
const previewTable = function(){
  
  let tableArray = [],
      tbodyArray = [],
      tableHTML = '',
      tbodyNumber = 3,
      maxLevel = rowNumberCheck();
  
  // パラメータシート or データシート
  const previewType = Number( $property.attr('data-menu-type') );
    
  // エディタ要素をTableに変換
  const tableAnalysis = function( $cols, repeatCount ) {

    // 自分の階層を調べる
    const currentFloor = $cols.children().parents('.menu-column-group').length;
    // 配列がUndefinedなら初期化
    if ( tableArray[ currentFloor ] === undefined ) tableArray[ currentFloor ] = [];
    // 子セルを調べる
    $cols.children().each( function(){
        const $column = $( this );

        if ( $column.is('.menu-column') ) {
          // 項目ここから
            const selectTypeValue = $column.find('.menu-column-type-select').val();
            // Head
            const rowspan = $column.attr('data-rowpan');
            let itemHTML = '<th rowspan="' + rowspan + '" class="sortTriggerInTbl">'
                           + textEntities( $column.find('.menu-column-title-input').val() );
            if ( repeatCount > 1 ) {
              itemHTML += '[' + repeatCount + ']';
            }
            itemHTML += sortMark + '</th>';
            tableArray[ currentFloor ].push( itemHTML );

            //プルダウン選択の参照項目
            if(selectTypeValue == '7'){
                const referenceItemValue = $column.find('.reference-item').attr('data-reference-item-id');
                const referenceItemName = $column.find('.reference-item').html();
                if(referenceItemValue != null && referenceItemValue != ""){ //空もしくはundefinedではない場合
                    const referenceItemAry = referenceItemValue.split(',');
                    const referenceItemNameAry = referenceItemName.split(',');
                    const referenceItemLength = referenceItemAry.length;
                    for ( let i = 0; i < referenceItemLength; i++ ) {
                        let referenceItemHTML = '<th rowspan="' + rowspan + '" class="sortTriggerInTbl">'+referenceItemNameAry[i];
                        if ( repeatCount > 1 ) {
                            referenceItemHTML += '[' + repeatCount + ']';
                        }
                        referenceItemHTML += sortMark + '</th>';
                        tableArray[ currentFloor ].push( referenceItemHTML );
                    }
                }
            }

            // Body
            let dummyText = selectDummyText[ selectTypeValue ][ 0 ],
                dummyType = selectDummyText[ selectTypeValue ][ 2 ];
            if ( dummyType === 'select' ) {
              dummyText = $column.find('.pulldown-select').find('option:selected').text();
            }
            tbodyArray.push('<td class="' + dummyType + '">' + dummyText + '</td>');

            //プルダウン選択の参照項目
            if(selectTypeValue == '7'){
                const referenceItemValue = $column.find('.reference-item').attr('data-reference-item-id');
                const referenceItemName = $column.find('.reference-item').html();
                if(referenceItemValue != null && referenceItemValue != ""){ //空もしくはundefinedではない場合
                    const referenceItemAry = referenceItemValue.split(',');
                    const referenceItemNameAry = referenceItemName.split(',');
                    const referenceItemLength = referenceItemAry.length;
                    for ( let i = 0; i < referenceItemLength; i++ ) {
                        const referenceItemHTML = '<td class="' + 'reference' + '">' + textCode('0044') + '</td>';
                        tbodyArray.push(referenceItemHTML);
                    };
                }
            }

          // Item end
        } else if ( $column.is('.menu-column-repeat') ) {
          // リピート
            const repeatNumber = $column.find('.menu-column-repeat-number-input').val();
            if ( $column.find('.menu-column, .menu-column-group').length ) {
                for ( let i = 1; i <= repeatNumber; i++ ) {
                  repeatCount = i;
                  tableAnalysis( $column.children('.menu-column-repeat-body'), repeatCount );
                }
                repeatCount = 0;
            } else {
                const rowspan = maxLevel - currentFloor;
                for ( let i = 1; i <= repeatNumber; i++ ) {
                  tableArray[ currentFloor ].push('<th class="empty" rowspan="' + rowspan + '">Empty</th>');
                  tbodyArray.push('<td class="empty">Empty</td>');
                }
            }
          // Repeat end
        } else if ( $column.is('.menu-column-group') ) {
          // グループ
            const colspan = childColumnCount( $column, 'group' ),
                  groupTitle = textEntities( $column.find('.menu-column-title-input').eq(0).val() ),
                  regexTitle = new RegExp( '<th colspan=".+">' + groupTitle + '<\/th>'),
                  tableArrayLength = tableArray[ currentFloor ].length - 1;
            let groupHTML = '<th colspan="' + colspan + '">' + groupTitle + '</th>';
            if ( repeatCount > 1 && tableArray[ currentFloor ][ tableArrayLength ].match( regexTitle ) ) {
              tableArray[ currentFloor ][ tableArrayLength ] = '<th colspan="' + ( colspan * repeatCount ) + '">' + groupTitle + '</th>';
            } else {
              tableArray[ currentFloor ].push( groupHTML );
            }
            tableAnalysis( $column.children('.menu-column-group-body'), repeatCount );
          // Group end
        } else if ( $column.is('.column-empty') ) {
          // 空
            const rowspan = maxLevel - currentFloor;
            tableArray[ currentFloor ].push('<th class="empty" rowspan="' + rowspan + '">Empty</th>');
            tbodyArray.push('<td>Empty</td>');
          // Empty end
        }

    });
    
  };

  // 解析スタート
  tableAnalysis ( $menuTable, 0 );
  
  // thead HTMLを生成
  const itemLength = childColumnCount( $menuTable, 'menu' );

  if ( previewType === 1 || previewType === 3 ) {
    maxLevel++;
    tableArray.unshift('');
  }
  const tableArrayLength = tableArray.length;
  for ( let i = 0; i < tableArrayLength; i++ ) {
    tableHTML += '<tr class="defaultExplainRow">';
    if ( i === 0 ) {
      tableHTML += tHeadHeaderLeftHTML.replace(/{{rowspan}}/g, maxLevel );
      if ( previewType === 1 ) {
        tableHTML += tHeadParameterHeaderLeftHTML
          .replace(/{{rowspan}}/g, maxLevel )
          .replace(/{{colspan}}/g, itemLength );
      }
      if ( previewType === 3 ) {
        tableHTML += tHeadOperationrHeaderLeftHTML
          .replace(/{{rowspan}}/g, maxLevel )
          .replace(/{{colspan}}/g, itemLength );
      }
    }
    if ( i === 1 && previewType === 1 || i === 1 && previewType === 3 ) {
      tableHTML += tHeadParameterOpeHeaderLeftHTML.replace(/{{rowspan}}/g, maxLevel - 1 );
    }
    tableHTML += tableArray[i];
    if ( i === 0 ) {
      tableHTML += tHeadHeaderRightHTML.replace(/{{rowspan}}/g, maxLevel );
    }
  }
  
  for ( let i = 1; i <= tbodyNumber; i++ ) {
    tableHTML += '<tr>' + tBodyHeaderLeftHTML.replace('{{id}}', i );
    if ( previewType === 1 ) {
      tableHTML += tBodyParameterHeaderLeftHTML;
    }
    if ( previewType === 3 ) {
      tableHTML += tBodyOperationHeaderLeftHTML;
    }
    tableHTML += tbodyArray.join() + tBodyHeaderRightHTML + '</tr>';
  }
  
  // プレビュー更新
  $('#menu-editor-preview').find('tbody').html( tableHTML );
  
}; 

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   パネル関連
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 作成対象セレクト
let beforeSelectType = '1';
$('#create-menu-type').on('change', function(){
  const $select = $( this ),
        menuType = $select.val();
  // データタイプに変更した場合、リピートをチェックする
  if ( menuType === '2') {
    const repeatFlag = repeatRemoveConfirm();
    if ( repeatFlag === true ) {
      history.clear();
    } else if ( repeatFlag === false) {
      // 選択を戻す
      $select.val( beforeSelectType );
      return false; 
    }    
  }
  beforeSelectType = menuType;
  $property.attr('data-menu-type', menuType );
  repeatCheck();
  previewTable();
});

// 縦メニュー利用有無チェックボックス
$('#create-menu-use-vertical').on('change', function(){
  const $checkBox = $( this );
  if ( !$checkBox.prop('checked') ) {
    const repeatFlag = repeatRemoveConfirm();
    if ( repeatFlag === true ) {
      history.clear();
    } else if ( repeatFlag === false ) {
      // チェックしなおす
      $checkBox.prop('checked', true );
      return false;
    }
  }
  repeatCheck();
});

// リピートを解除するか確認する
const repeatRemoveConfirm = function() {
    // リピートを使用しているか？
    const $repeat = $menuEditor.find('.menu-column-repeat').eq(0);
    if ( $repeat.length ) {
      if ( confirm( textCode('0034')) ) {
        // リピートが空か？
        if ( $repeat.children('.menu-column-repeat-body').children('.column-empty').length ) {
          $repeat.remove();
        } else {
          // 中身をリピートと入れ替える
          $repeat.replaceWith( $repeat.children('.menu-column-repeat-body').html() );
          // select2を再適用する
          resetSelect2( $menuTable );
          // datetimepickerを再適用する
          resetDatetimepicker( $menuTable );
          // プルダウン選択の初期値取得eventを再適用する
          resetEventPulldownDefaultValue( $menuTable );
          // パラメータ参照の項目取得eventを再適用する
          resetEventPulldownParameterSheetReference( $menuTable );
        }
        emptyCheck();
        previewTable();
        return true;
      } else {
        return false;
      }
    } else {
      return undefined;
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目横幅変更
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const $columnResizeLine = $('#column-resize'),
      defMinWidth = 180;
$menuEditor.on('mousedown', '.column-resize', function( e ) {
  
  // 左クリックチェック
  if ( e.which !== 1 ) return false;
  
  mode.columnResize();
  
  const $column = $( this ).closest('.menu-column'),
        width = $column.outerWidth(),
        positionX = $column.offset().left - $menuEditor.offset().left - 1,
        mouseDownX = e.pageX;
        
  let minWidth;
  
  $columnResizeLine.show().css({
    'left' : positionX,
    'width' : width
  });
  
  $window.on({
    'mousemove.columnResize' : function( e ) {
      const moveX = e.pageX - mouseDownX;
      minWidth = width + moveX;
      if ( defMinWidth > minWidth ) minWidth = defMinWidth;
      $columnResizeLine.show().css({
        'width' : minWidth
      });
    },
    'mouseup.columnResize' : function() {
      $window.off('mouseup.columnResize mousemove.columnResize');
      mode.clear();
      $columnResizeLine.hide();
      $column.css('min-width', minWidth );
      // サイズが変わったら履歴追加
      if ( width !== $column.outerWidth() ) {
        history.add();
      }
    }
  });

});


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   エディタウィンドウリサイズ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$('#menu-editor-row-resize').on('mousedown', function( e ){

  // 全ての選択を解除する
  getSelection().removeAllRanges();
  mode.blockResize();

  const $resizeBar = $( this ),
        $resizeBlock = $menuEditor.find('.menu-editor-block'),
        $section1 = $resizeBlock.eq(0),
        $section2 = $resizeBlock.eq(1),
        initialPoint = e.clientY,
        minHeight = 64;

  let movePoint = 0,
      newSection1Height = 0;
      
  // 高さを一旦固定値に
  $resizeBlock.each( function(){
    $( this ).css('height', $( this ).outerHeight() );
  });

  const initialSection1Height = newSection1Height = $section1.outerHeight(),
        initialHeight = initialSection1Height + $section2.outerHeight(),
        maxHeight = initialHeight - minHeight;

  $window.on({
    'mousemove.sizeChange' : function( e ){

      movePoint = e.clientY - initialPoint;
      
      newSection1Height = initialSection1Height + movePoint;
      
      if ( newSection1Height < minHeight ) {
        newSection1Height = minHeight;
        movePoint = minHeight - initialSection1Height;
      } else if ( newSection1Height > maxHeight ) {
        newSection1Height = maxHeight;
        movePoint = maxHeight - initialSection1Height;
      }
      
      $section1.css('height', newSection1Height );
      $section2.css('height', initialHeight - newSection1Height );
      $resizeBar.css('transform','translateY(' + movePoint + 'px)');
      
    },

    'mouseup.sizeChange' : function(){
      $window.off('mousemove.sizeChange mouseup.sizeChange');
      mode.clear();

      // 高さを割合に戻す
      const section1Ratio = newSection1Height / initialHeight * 100;
      $section1.css('height', section1Ratio + '%' );
      $section2.css('height', ( 100 - section1Ratio ) + '%' );
      $resizeBar.css({
        'transform' : 'translateY(0)',
        'top' : section1Ratio + '%'
      });

    }
  });   

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  メニューグループ選択
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const menuGroupBody = function() {

  const menuGroupData = menuEditorArray.selectMenuGroupList,
        menuListRowLength = menuGroupData.length,
        menuGroupType = ['for-input','for-substitution','for-reference'],
        menuGroupAbbreviation = [textCode('0036'),textCode('0037'),textCode('0038')],
        menuGroupTypeLength = menuGroupType.length;

  let html = ''
  + '<div id="menu-group-list" class="modal-table-wrap">'
    + '<table class="modal-table">'
      + '<thead>'
        + '<tr>';

  // header Radio
  for ( let i = 0; i < menuGroupTypeLength; i++ ) {
    html += '<th class="radio ' + menuGroupType[i] + '" checked>' + menuGroupAbbreviation[i] + '</th>' 
  }
  // header Title
  html += '<th class="id">ID</th>'
        + '<th class="name">' + textCode('0039') + '</th>';

  html += '</tr></thead><tbody><tr>';

  // Unselected Radio
  for ( let i = 0; i < menuGroupTypeLength; i++ ) {
    const radioID = 'radio-' + menuGroupType[i] + '-0';
    html += ''
    + '<th class="radio ' + menuGroupType[i] + '">'
      + '<span class="menu-group-radio">'
        + '<input type="radio" class="select-menu radio-number-0" id="' + radioID + '" name="' + menuGroupType[i] + '" value="unselected" data-name="unselected" checked>' 
        + '<label class="select-menu-label" for="' + radioID + '"></label>'
      + '</span>'
    + '</th>' 
  }

  html += '<td class="unselected" >-</td>'
        + '<td class="unselected" >Unselected</td></tr>';

  // body List
  for ( let i = 0; i < menuListRowLength; i++ ) {
    html += '<tr>';
    // body Radio
    for ( let j = 0; j < menuGroupTypeLength; j++ ) {
      const radioClass = 'select-menu radio-number-' + menuGroupData[i]['MENU_GROUP_ID'],
            radioID = 'radio-' + menuGroupType[j] + '-' + menuGroupData[i]['MENU_GROUP_ID'];
      html += ''
      + '<th class="radio ' + menuGroupType[j] + '">'
        + '<span class="menu-group-radio">'
          + '<input type="radio" class="' + radioClass +'" id="' + radioID + '" name="' + menuGroupType[j] + '" value="' + menuGroupData[i]['MENU_GROUP_ID'] + '" data-name="' + menuGroupData[i]['MENU_GROUP_NAME'] + '">'
          + '<label class="select-menu-label" for="' + radioID + '"></label>'
        + '</span>'
      + '</th>' 
    }
    // Menu group Data
    html += '<td class="id">' + menuGroupData[i]['MENU_GROUP_ID'] + '</td>'
          + '<td class="name">' + textEntities( menuGroupData[i]['MENU_GROUP_NAME'] ) + '</td>';

    html += '</tr>';      
  }

  html += '</tbody></table></div>'

  // モーダルにBodyをセット
  const $modalBody = $('.editor-modal-body');
  $modalBody.html( html ).on('change', '.select-menu', function(){
    const $input = $( this ),
          menuID = $input.attr('value'),
          neme = $input.attr('name'),
          checkClass = 'checked-row checked-' + neme;
    $('.checked-' + neme ).removeClass( checkClass )
      .find('.select-menu').prop('disabled', false );

    if ( menuID !== 'unselected' ) {
      $('.radio-number-' + menuID ).closest('tr').addClass( checkClass )
        .find('.select-menu').not(':checked').prop('disabled', true );
    }
  });

  // 選択状態をRadioボタンに反映する
  $('#menu-group').find('.property-span:visible').each( function(){
    const $item = $( this ),
          type = $item.attr('id').replace('create-menu-',''),
          id = $item.attr('data-id');
    if ( id !== '' ) {
      $modalBody.find('input[name="' + type + '"]').filter('[value="' + id + '"]').prop('checked', true).change();
    }
  });    

  // 決定・取り消しボタン
  const $modalButton = $('.editor-modal-footer-menu-button');
  $modalButton.on('click', function() {
    const $button = $( this ),
          type = $button.attr('data-button-type');
    switch( type ) {
      case 'ok':
        // チェック状態を対象メニューグループ選択に反映する
        $('.select-menu:checked').each( function() {
          const $checked = $( this ),
                checkedType = $checked.attr('name');
          let checkedID = $checked.val(),
              checkedName = $checked.attr('data-name');
          if ( checkedID === 'unselected'){
            checkedID = checkedName = ''
          }
          $('#create-menu-' + $checked.attr('name') ).text( checkedName ).attr({
            'data-id' :  checkedID,
            'data-value' : checkedName
          });
          // 縦メニュー値があるか確認
          if ( checkedType === 'vertical' ) {
            if ( checkedID !== '') {
              $property.attr('data-vertical-menu', true );
            } else {
              $property.attr('data-vertical-menu', false );
            }
          }
        });
        itaModalClose();
        break;
      case 'cancel':
        itaModalClose();
        break;
    }
  });

};

// 対象メニューグループ モーダルを開く
const $menuGroupSlectButton = $('#create-menu-group-select');
$menuGroupSlectButton.on('click', function() {
  let type;
  // パラメータシートorデータシート
  if ( $('#create-menu-type').val() === '1' || $('#create-menu-type').val() === '3' ) {
    type = 'parameter-sheet';
  } else {
    type = 'data-sheet';
  }
  itaModalOpen( textCode('0033'), menuGroupBody, type );
});

// 縦メニューヘルプ
const verticalMenuHelp = function() {
  const $modalBody = $('.editor-modal-body');
  $modalBody.html( $('#vertical-menu-description').html() );
};
$('#vertical-menu-help').on('click', function() {
  itaModalOpen( textCode('0040'), verticalMenuHelp, 'help');
});

// カンマ区切りロールIDリストからロールNAMEリストを返す
const getRoleListIdToName = function( roleListText ) {
  if ( roleListText !== undefined && roleListText !== '') {
    const roleList = roleListText.split(','),
          roleListLength = roleList.length,
          roleNameList = new Array;

    for ( let i = 0; i < roleListLength; i++ ) {
      const roleName = listIdName('role', roleList[i]);
      if ( roleName !== null ) {
        const hideRoleName = getSomeMessage("ITAWDCC92008");
        if ( roleName !== hideRoleName ) {
          roleNameList.push( roleName );
        } else {
          roleNameList.push( roleName + '(' + roleList[i] + ')');
        }
      } else {
        roleNameList.push( getSomeMessage("ITAWDCC92007") + '(' + roleList[i] + ')');
      }
    }
    return roleNameList.join(', ');
  } else {
    return '';
  }
};
// カンマ区切りロールIDリストからID変換失敗を除いたロールIDを返す
const getRoleListValidID = function( roleListText ) {
  if ( roleListText !== undefined && roleListText !== '' ) {
    const roleList = roleListText.split(','),
          roleListLength = roleList.length,
          roleIdList = new Array;
    for ( let i = 0; i < roleListLength; i++ ) {
      const roleName = listIdName('role', roleList[i]);
      if ( roleName !== null ) {
        roleIdList.push( roleList[i] );
      }
    }
    return roleIdList.join(',');
  } else {
    return '';
  }
};
// ロールセレクト
const modalRoleList = function() {
  const $input = $('#permission-role-name-list');
  const initRoleList = ( $input.attr('data-role-id') === undefined )? '': $input.attr('data-role-id');
  // 決定時の処理    
  const okEvent = function( newRoleList ) {
    $input.text(　getRoleListIdToName( newRoleList ) ).attr('data-role-id', newRoleList );
    itaModalClose();
  };
  // キャンセル時の処理    
  const cancelEvent = function( newRoleList ) {
    itaModalClose();
  };

  setRoleSelectModalBody( menuEditorArray.roleList, initRoleList, okEvent, cancelEvent );
  
};
// ロールセレクトモーダルを開く
const $roleSlectButton = $('#permission-role-select');
$roleSlectButton.on('click', function() {
  itaModalOpen(textCode('0048'), modalRoleList, 'role');
});


//参照項目セレクト
const modalReferenceItemList = function($target) {
  const $input = $target.closest('.menu-column-config-table').find('.reference-item');
  const initItemList = ( $input.attr('data-reference-item-id') === undefined )? '': $input.attr('data-reference-item-id');
  const selectLinkId = $target.closest('.menu-column-config-table').find('.pulldown-select option:selected').val();

  // 決定時の処理    
  const okEvent = function( newItemList, extractItemList ) {
    $input.attr('data-reference-item-id', newItemList );
    //newItemListのIDから項目名に変換
    const newItemListArray = newItemList.split(',');
    const newItemNameListArray = [];
    newItemListArray.forEach(function(id){
      extractItemList.forEach(function(data){
        if(data['ITEM_ID'] == id){
          newItemNameListArray.push(data['ITEM_NAME']);
        }
      });
    });

    //カンマ区切りの文字列に変換に参照項目上に表示
    var newItemNameList = newItemNameListArray.join(',');
    $input.html(newItemNameList);

    previewTable();
    itaModalClose();
  };
  // キャンセル時の処理    
  const cancelEvent = function( newItemList ) {
    itaModalClose();
  };
  // 閉じる時の処理
  const closeEvent = function ( newItemList ) {
    itaModalClose();
  }

  //選択されている「プルダウン選択」で選択可能な参照項目のみを取得する
  let targetReferenceItem;
  const printReferenceItemURL = '/common/common_printReferenceItem.php?link_id=' + selectLinkId + '&user_id=' +gLoginUserID;
  $.ajax({
    type: 'get',
    url: printReferenceItemURL,
    dataType: 'text'
  }).done( function( result ) {
      //選択可能な参照項目の一覧を取得
      targetReferenceItem = JSON.parse( result );
      if ( targetReferenceItem[0] == 'redirectOrderForHADACClient' ) {
        window.alert( targetReferenceItem[2] );
        var redirectUrl = targetReferenceItem[1][1] + location.search.replace('?','&');
        return redirectTo(targetReferenceItem[1][0], redirectUrl, targetReferenceItem[1][2]);   
      }
      setRerefenceItemSelectModalBody(targetReferenceItem, initItemList, okEvent, cancelEvent, closeEvent);

  }).fail( function( result ) {
    targetReferenceItem = null;
    setRerefenceItemSelectModalBody(targetReferenceItem, initItemList, okEvent, cancelEvent, closeEvent);

  });

}

//一意制約(複数項目)
const modalUniqueConstraint = function() {
  //現在の設定値
  const $input = $('#unique-constraint-list');
  const initmodalUniqueConstraintList = ( $input.attr('data-unique-list') === undefined )? '': $input.attr('data-unique-list');

  //表示されている項目のデータを格納
  let $columnItems = $menuTable.find('.menu-column');
  let columnItemData = [];
  let i = 0;
  $columnItems.each(function(){
    let targetItem = $(this);
    let targetItemData = {};
    let columnId = "";
    columnId = targetItem.attr('id');
    let itemName = "";
    itemName = targetItem.find('.menu-column-title-input').val();
    let itemId = "";
    itemId = targetItem.attr('data-item-id');
    targetItemData = {
      'columnId': columnId,
      'itemName': itemName,
      'itemId': itemId
    };

    columnItemData[i] = targetItemData;
    i++;
  });


  // 決定時の処理    
  const okEvent = function(currentUniqueConstraintArray) {
    const uniqueConstraintData = getUniqueConstraintDispData(currentUniqueConstraintArray);
    const uniqueConstraintConv = uniqueConstraintData.conv;
    const uniqueConstraintName = uniqueConstraintData.name;
    $input.attr('data-unique-list', uniqueConstraintConv); //一意制約のIDの組み合わせをセット
    $input.text(uniqueConstraintName); //一意制約の項目名の組み合わせをセット

    //現在の設定値を更新
    menuEditorArray['unique-constraints-current'] = currentUniqueConstraintArray;

    itaModalClose();
  };
  // キャンセル時の処理    
  const cancelEvent = function() {
    itaModalClose();
  };
  // 閉じる時の処理
  const closeEvent = function ( ) {
    itaModalClose();
  }

  setUniqueConstraintModalBody(columnItemData, initmodalUniqueConstraintList, okEvent, cancelEvent, closeEvent);
  
};
// 一意制約(複数項目)選択のモーダルを開く
const $multiSetUniqueSlectButton = $('#unique-constraint-select');
$multiSetUniqueSlectButton.on('click', function() {
  itaModalOpen( textCode('0047'), modalUniqueConstraint, 'unique' );
});

//一意制約の登録用のcolumnID連結文字列と、表示用の項目名を作成する
const getUniqueConstraintDispData = function(uniqueConstraintArrayData){
  let uniqueConstraintDispData = {
    "conv" : "",
    "name" : ""
  };

  let uniqueConstraintLength = uniqueConstraintArrayData.length;

  if(uniqueConstraintLength == 0){
    return uniqueConstraintDispData;
  }

  let uniqueConstraintConv = "";
  let uniqueConstraintName = "";

  for (let i = 0; i < uniqueConstraintLength; i++){
      let targetIdLength = uniqueConstraintArrayData[i].length;
      let idPatternConv = "";
      let idPatternName = "";
      if(targetIdLength != 0){
        for (let j = 0; j < targetIdLength; j++){
          for (let columnId in uniqueConstraintArrayData[i][j]){
            if(idPatternConv == ""){
              idPatternConv = columnId;
            }else{
              idPatternConv = idPatternConv + "-" + columnId;
            }

            if(idPatternName == ""){
              idPatternName = uniqueConstraintArrayData[i][j][columnId];
            }else{
              idPatternName = idPatternName + "," + uniqueConstraintArrayData[i][j][columnId];
            }

          }
        }

        //columnID部分の文字列を結合
        if(uniqueConstraintConv == ""){
          uniqueConstraintConv = idPatternConv;
        }else{
          uniqueConstraintConv = uniqueConstraintConv + "," + idPatternConv;
        }

        //項目名部分の文字列を結合
        if(uniqueConstraintName == ""){
            idPatternName = "(" + idPatternName + ")";
            uniqueConstraintName = idPatternName;
        }else{
            idPatternName = "(" + idPatternName + ")";
            uniqueConstraintName = uniqueConstraintName + "," + idPatternName;
        }
      }
  }

  uniqueConstraintDispData.conv = uniqueConstraintConv;
  uniqueConstraintDispData.name = uniqueConstraintName;

  return uniqueConstraintDispData;

}

//項目を削除したとき、一意制約(複数項目)にその項目が含まれていた場合削除する。
const deleteUniqueConstraintDispData = function(targetColumnId){
  let currentUniqueConstraintData = menuEditorArray['unique-constraints-current'];
  if(currentUniqueConstraintData != null){
    let newCurrentUniqueConstraintData = currentUniqueConstraintData;
    let uniqueConstraintLength = currentUniqueConstraintData.length;
    for (let i = 0; i < uniqueConstraintLength; i++){
        let targetIdLength = currentUniqueConstraintData[i].length;
        for (let j = 0; j < targetIdLength; j++){
          for (let columnId in currentUniqueConstraintData[i][j]){
            if(targetColumnId == columnId){
              newCurrentUniqueConstraintData[i].splice(j, 1); //削除した項目の配列を除外
            }
          }
        }
    }

    //組み合わせの中身が空になった場合、その配列を除外する。
    let newUniqueConstraintLength = newCurrentUniqueConstraintData.length;
    for (let i = 0; i < newUniqueConstraintLength; i++){
      if(newCurrentUniqueConstraintData[i] != undefined){
        if(newCurrentUniqueConstraintData[i].length == 0){
          newCurrentUniqueConstraintData.splice(i, 1);
        }
      }
    }

    //更新後の値をページに反映
    const uniqueConstraintData = getUniqueConstraintDispData(newCurrentUniqueConstraintData);
    const uniqueConstraintConv = uniqueConstraintData.conv;
    const uniqueConstraintName = uniqueConstraintData.name;
    const $input = $('#unique-constraint-list');
    $input.attr('data-unique-list', uniqueConstraintConv); //一意制約のIDの組み合わせをセット
    $input.text(uniqueConstraintName); //一意制約の項目名の組み合わせをセット

    //新しい配列をセット
    menuEditorArray['unique-constraints-current'] = newCurrentUniqueConstraintData;
  }

}

//項目名が変更されるアクションがあったとき、一意制約(複数項目)で表示している項目名をセットしなおす。
const updateUniqueConstraintDispData = function(){
  let currentUniqueConstraintData = menuEditorArray['unique-constraints-current'];
  if(currentUniqueConstraintData != null){
    let newCurrentUniqueConstraintData = currentUniqueConstraintData;
    let uniqueConstraintLength = currentUniqueConstraintData.length;
    for (let i = 0; i < uniqueConstraintLength; i++){
        let targetIdLength = currentUniqueConstraintData[i].length;
        for (let j = 0; j < targetIdLength; j++){
          for (let columnId in currentUniqueConstraintData[i][j]){
            let $itemNameArea = $menuTable.find('#'+columnId).find('.menu-column-title-input');
            if($itemNameArea.length != 0){
              let itemName = $itemNameArea.val();
              newCurrentUniqueConstraintData[i][j] = {[columnId] : itemName}; //項目名を再設定
            }
          }
        }
    }

    //更新後の値をページに反映
    const uniqueConstraintData = getUniqueConstraintDispData(newCurrentUniqueConstraintData);
    const uniqueConstraintConv = uniqueConstraintData.conv;
    const uniqueConstraintName = uniqueConstraintData.name;
    const $input = $('#unique-constraint-list');
    $input.attr('data-unique-list', uniqueConstraintConv); //一意制約のIDの組み合わせをセット
    $input.text(uniqueConstraintName); //一意制約の項目名の組み合わせをセット

    //新しい配列をセット
    menuEditorArray['unique-constraints-current'] = newCurrentUniqueConstraintData;
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  登録情報作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const createRegistrationData = function( type ){

  // Emptyが一つでもある場合は終了
  if ( $menuTable.find('.column-empty').length ) {
    alert('Empty error.');
    return false;
  }

  let createMenuJSON = {
    'menu' : {},
    'group' : {},
    'item' : {},
    'repeat' : {},
    'type' : {}
  };
  
  // Order用カウンター
  let itemCount = 0;
  
  // メニュー作成情報
  createMenuJSON['menu'] = getPanelParameter();
  if ( menuEditorMode === 'initialize' || menuEditorMode === 'edit') {
    createMenuJSON['menu']['LAST_UPDATE_TIMESTAMP'] = menuEditorArray.selectMenuInfo['menu']['LAST_UPDATE_TIMESTAMP'];
  }
  
  // CREATE_ITEM_IDからKEYを返す
  const CREATE_ITEM_ID_to_KEY = function( itemID ) {
    for ( let key in menuEditorArray.selectMenuInfo['item'] ) {
      if ( menuEditorArray.selectMenuInfo['item'][ key ]['CREATE_ITEM_ID'] === itemID ) {
        return menuEditorArray.selectMenuInfo['item'][ key ]['ITEM_NAME'];
      }
    }
  }
  // リピート項目チェック（名前とグループからCREATE_ITEM_IDとLAST_UPDATE_TIMESTAMPを返す）
  const repeatItemCheckID = function( groupID, itemName ) {
    for ( let key in menuEditorArray.selectMenuInfo['item'] ) {
      if ( menuEditorArray.selectMenuInfo['item'][ key ]['COL_GROUP_ID'] === groupID &&
           menuEditorArray.selectMenuInfo['item'][ key ]['ITEM_NAME'] === itemName ) {
        // リピートで作成された項目かチェック
        if ( menuEditorArray.selectMenuInfo['item'][ key ]['REPEAT_ITEM'] === true ) {
          return [
            menuEditorArray.selectMenuInfo['item'][ key ]['CREATE_ITEM_ID'],
            menuEditorArray.selectMenuInfo['item'][ key ]['LAST_UPDATE_TIMESTAMP']
          ];
        }
      }
    }
    // 見つからない場合はnullを返す
    return [ null, null ];
  }
  // COL_GROUP_IDからKEYを返す
  const COL_GROUP_ID_to_KEY = function( groupID ) {
    for ( let key in menuEditorArray.selectMenuInfo['group'] ) {
      if ( menuEditorArray.selectMenuInfo['group'][ key ]['COL_GROUP_ID'] === groupID ) {
        return menuEditorArray.selectMenuInfo['group'][ key ]['COL_GROUP_NAME'];
      }
    }
  }
  // リピート項目チェック（名前からCREATE_ITEM_IDとLAST_UPDATE_TIMESTAMPを返す）
  const repeatGroupCheckID = function( groupName ) {
    for ( let key in menuEditorArray.selectMenuInfo['group'] ) {
      if ( menuEditorArray.selectMenuInfo['group'][ key ]['COL_GROUP_NAME'] === groupName ) {
        // リピートで作成された項目かチェック
        if ( menuEditorArray.selectMenuInfo['group'][ key ]['REPEAT_GROUP'] === true ) {
          return menuEditorArray.selectMenuInfo['group'][ key ]['COL_GROUP_ID'];
        }
      }
    }
    // 見つからない場合はnullを返す
    return [ null, null ];
  }
  
  const tableAnalysis = function( $cols, repeatCount ) {
    
    // 子セルを調べる
    $cols.children().each( function(){
      const $column = $( this );
      
      if ( $column.is('.menu-column') ) {
          // 項目ここから
            const order = itemCount++,
                  selectTypeValue = $column.find('.menu-column-type-select').val();
            let key = $column.attr('id'),
                repeatFlag = false,
                CREATE_ITEM_ID = $column.attr('data-item-id'),
                LAST_UPDATE_TIMESTAMP = null;

            if ( CREATE_ITEM_ID === '') CREATE_ITEM_ID = null;
            if ( menuEditorMode === 'initialize' || menuEditorMode === 'edit' ) {
              if ( menuEditorArray.selectMenuInfo['item'][key] ) {
                LAST_UPDATE_TIMESTAMP = menuEditorArray.selectMenuInfo['item'][key]['LAST_UPDATE_TIMESTAMP'];
              }
            }
            // 親カラムグループ
            let parentArray = [];
            $column.parents('.menu-column-group').each( function() {
              parentArray.unshift( $( this ).find('.menu-column-title-input').val() );
            });
            const parents = parentArray.join('/');
            let   parentsID = $column.closest('.menu-column-group').attr('data-group-id');
            if ( parentsID === undefined ) parentsID = null;
            // 項目名
            let itemName = $column.find('.menu-column-title-input').val();
            if ( repeatCount > 1 ) {
              itemName += '[' + repeatCount + ']';
              repeatFlag = true;
              key = key + '[' + repeatCount + ']';

              // 更新時のリピート項目チェック
              if ( menuEditorMode === 'initialize' || menuEditorMode === 'edit' ) {
                const originalBeforeName = CREATE_ITEM_ID_to_KEY( CREATE_ITEM_ID ),
                      repeatItemData = repeatItemCheckID( parentsID, originalBeforeName + '[' + repeatCount + ']');
                CREATE_ITEM_ID = repeatItemData[0];
                LAST_UPDATE_TIMESTAMP = repeatItemData[1];
              }

            }
            // JSONデータ
            createMenuJSON['item'][key] = {
              'CREATE_ITEM_ID' : CREATE_ITEM_ID,
              'MENU_NAME' : createMenuJSON['menu']['MENU_NAME'],
              'ITEM_NAME' : itemName,
              'DISP_SEQ' : order,
              'REQUIRED' : (selectTypeValue == '11') ? false : $column.find('.required').prop('checked'), //パラメータシート参照の場合「必須」はfalse
              'UNIQUED' : (selectTypeValue == '11') ? false : $column.find('.unique').prop('checked'),　//パラメータシート参照の場合「一意制約」はfalse
              'COL_GROUP_ID' : parents,
              'INPUT_METHOD_ID' : selectTypeValue,
              'DESCRIPTION' : $column.find('.explanation').val(),
              'NOTE' : $column.find('.note').val(),
              'REPEAT_ITEM' : repeatFlag,
              'MIN_WIDTH' : $column.css('min-width'),
              'LAST_UPDATE_TIMESTAMP' : LAST_UPDATE_TIMESTAMP
            }
            // 項目タイプ
            switch ( selectTypeValue ) {
              case '1':
                createMenuJSON['item'][key]['MAX_LENGTH'] = $column.find('.max-byte').val();
                createMenuJSON['item'][key]['PREG_MATCH'] = $column.find('.regex').val();
                createMenuJSON['item'][key]['SINGLE_DEFAULT_VALUE'] = $column.find('.single-default-value').val();
                break;
              case '2':
                createMenuJSON['item'][key]['MULTI_MAX_LENGTH'] = $column.find('.max-byte').val();
                createMenuJSON['item'][key]['MULTI_PREG_MATCH'] = $column.find('.regex').val();
                createMenuJSON['item'][key]['MULTI_DEFAULT_VALUE'] = $column.find('.multiple-default-value').val();
                break;
              case '3':
                createMenuJSON['item'][key]['INT_MIN'] = $column.find('.int-min-number').val();
                createMenuJSON['item'][key]['INT_MAX'] = $column.find('.int-max-number').val();
                createMenuJSON['item'][key]['INT_DEFAULT_VALUE'] = $column.find('.int-default-value').val();
                break;
              case '4':
                createMenuJSON['item'][key]['FLOAT_MIN'] = $column.find('.float-min-number').val();
                createMenuJSON['item'][key]['FLOAT_MAX'] = $column.find('.float-max-number').val();
                createMenuJSON['item'][key]['FLOAT_DIGIT'] = $column.find('.digit-number').val();
                createMenuJSON['item'][key]['FLOAT_DEFAULT_VALUE'] = $column.find('.float-default-value').val();
                break;
              case '5':
                createMenuJSON['item'][key]['DATETIME_DEFAULT_VALUE'] = $column.find('.datetime-default-value').val();
                break;
              case '6':
                createMenuJSON['item'][key]['DATE_DEFAULT_VALUE'] = $column.find('.date-default-value').val();
                break;
              case '7':
                createMenuJSON['item'][key]['OTHER_MENU_LINK_ID'] = $column.find('.pulldown-select').val();
                createMenuJSON['item'][key]['PULLDOWN_DEFAULT_VALUE'] = $column.find('.pulldown-default-select').val();
                createMenuJSON['item'][key]['REFERENCE_ITEM'] = $column.find('.reference-item').attr('data-reference-item-id');
                break;
              case '8':
                createMenuJSON['item'][key]['PW_MAX_LENGTH'] = $column.find('.password-max-byte').val();
                break;
              case '9':
                createMenuJSON['item'][key]['UPLOAD_MAX_SIZE'] = $column.find('.file-max-size').val();
                break;
              case '10':
                createMenuJSON['item'][key]['LINK_LENGTH'] = $column.find('.max-byte').val();
                createMenuJSON['item'][key]['LINK_DEFAULT_VALUE'] = $column.find('.link-default-value').val();
                break;
              case '11':
                createMenuJSON['item'][key]['TYPE3_REFERENCE'] = $column.find('.type3-reference-item').val();
                break;
            }
          // Item end
        } else if ( $column.is('.menu-column-repeat') ) {
          // リピート
            const repeatNumber = $column.find('.menu-column-repeat-number-input').val();
            if ( $column.find('.menu-column, .menu-column-group').length ) {
                // リピートの回数繰り返す
                for ( let i = 1; i <= repeatNumber; i++ ) {
                  repeatCount = i;
                  tableAnalysis( $column.children('.menu-column-repeat-body'), repeatCount );
                }
                repeatCount = 0;
                // リピート内項目リスト
                let columns = [];
                $column.children('.menu-column-repeat-body').children().each( function() {
                  columns.push( $( this ).attr('id') );
                });
                // リピートJSON
                createMenuJSON['repeat']['r1'] = {
                  'COLUMNS' : columns,
                  'REPEAT_CNT' : repeatNumber
                }
                if ( menuEditorMode === 'initialize' || menuEditorMode === 'edit' ) {
                  if ( menuEditorArray.selectMenuInfo['repeat']['r1'] && menuEditorArray.selectMenuInfo['repeat']['r1']['LAST_UPDATE_TIMESTAMP'] ) {
                    createMenuJSON['repeat']['LAST_UPDATE_TIMESTAMP'] = menuEditorArray.selectMenuInfo['repeat']['r1']['LAST_UPDATE_TIMESTAMP'];
                  }
                }
            }
          // Repeat end
        } else if ( $column.is('.menu-column-group') ) {
          // グループ
            let groupID = $column.attr('data-group-id'),
                groupName = $column.find('.menu-column-title-input').val(),
                key = $column.attr('id'),
                parents = '',
                parentArray = [],
                columns = [],
                repeatFlag = false;
             // グループ名
            if ( repeatCount > 1 ) {
              groupName += '[' + repeatCount + ']';
              repeatFlag = true;
              key = key + '[' + repeatCount + ']';

              // 更新時のリピート項目チェック
              if ( menuEditorMode === 'initialize' || menuEditorMode === 'edit' ) {
                const originalBeforeName = COL_GROUP_ID_to_KEY( groupID ),
                      repeatGroupID = repeatGroupCheckID( originalBeforeName + '[' + repeatCount + ']');
                groupID = repeatGroupID;
              }

            }
            // 親グループ
            $column.parents('.menu-column-group').each( function() {
              parentArray.unshift( $( this ).find('.menu-column-title-input').val() );
            });
            parents = parentArray.join('/');
            // グループ内項目リスト
            $column.children('.menu-column-group-body').children().each( function() {
              columns.push( $( this ).attr('id') );
            });
            // グループJSON
            createMenuJSON['group'][key] = {
              'COL_GROUP_ID' : groupID,
              'COL_GROUP_NAME' : groupName,
              'PARENT' : parents,
              'COLUMNS' : columns,
              'REPEAT_GROUP' : repeatFlag,
            }
            tableAnalysis( $column.children('.menu-column-group-body'), repeatCount );
          // Group end
        }     

    });

  };
    
  // トップ階層のカラム情報
  let topColumns = [];
  $menuTable.children().each( function() {
    topColumns.push( $( this ).attr('id') );
  });
  createMenuJSON['menu']['columns'] = topColumns;

  //メニュー作成タイプを格納
  createMenuJSON['type'] = type;

  // 解析スタート
  tableAnalysis ( $menuTable, 0 );
  
  // JSON変換
  const menuData = JSON.stringify( createMenuJSON );

  if ( type === 'registration' ) {
    registerTable(menuData);
  } else if ( type === 'update-initialize' || type === 'update') {
    updateTable(menuData);
  }

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   再表示
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const loadMenu = function() {
    
    const loadJSON = menuEditorArray.selectMenuInfo;
    // 流用新規時に引き継がない項目
    if ( menuEditorMode === 'diversion' ){
      loadJSON['menu']['CREATE_MENU_ID'] = null;
      loadJSON['menu']['MENU_NAME'] = null;
      loadJSON['menu']['DISP_SEQ'] = null;
      loadJSON['menu']['LAST_UPDATE_TIMESTAMP'] = null;
      loadJSON['menu']['LAST_UPDATE_TIMESTAMP_FOR_DISPLAY'] = null;
      loadJSON['menu']['LAST_UPDATE_USER'] = null;
      loadJSON['menu']['DESCRIPTION'] = null;
      loadJSON['menu']['NOTE'] = null;
    }

    // パネル情報表示
    setPanelParameter( loadJSON );
    
    // エディタクリア
    $menuTable.html('');
    
    // エディタ表示
    const recursionMenuTable = function( $target, column ) {
      
      let columns = ('columns' in column )? 'columns' : 'COLUMNS';
      
      const length = column[ columns ].length;

      for ( let i = 0; i < length; i++ ) {

        const id = column[ columns ][i],
              type = id.substr(0,1),
              number = id.substr(1);

        if ( type === 'g' ) {
          // グループ
          addColumn( $target, 'group', number, loadJSON['group'][id], false, false );
          const groupData = loadJSON['group'][id],
                $group = $('#' + id );
          
          $group.attr('data-group-id', groupData['COL_GROUP_ID'] );
          
          recursionMenuTable( $group.children('.menu-column-group-body'), groupData );
          
        } else if ( type === 'r' ) {
          // リピート
          addColumn( $target, 'repeat', number, loadJSON['repeat'][id], false, false );
          const repeatData = loadJSON['repeat'][id],
                $repeat = $('#' + id );
          
          $repeat.find('.menu-column-repeat-number-input').val( repeatData['REPEAT_CNT'] ).change();
          
          recursionMenuTable( $repeat.children('.menu-column-repeat-body'), repeatData );
          
        } else {
          // 項目
          addColumn( $target, 'item', number, loadJSON['item'][id], false );
          const itemData = loadJSON['item'][id],
                $item = $('#' + id );        

          $item.css('min-width', itemData['min-width'] );

          $item.attr('data-item-id', itemData['CREATE_ITEM_ID'] );
          $item.find('.menu-column-type-select').val( itemData['INPUT_METHOD_ID'] ).change();
          $item.find('.required').prop('checked', itemData['REQUIRED'] ).change();
          $item.find('.unique').prop('checked', itemData['UNIQUED'] ).change();

          let descriptionText = itemData['DESCRIPTION'] === null ? '' : itemData['DESCRIPTION'];
          let noteText = itemData['NOTE'] === null ? '' : itemData['NOTE'];

          $item.find('.explanation').val( descriptionText ).change();
          if ( descriptionText !== '' ) $item.find('.explanation').addClass('text-in');
          $item.find('.note').val( noteText ).change();
          if ( noteText !== '' ) $item.find('.note').addClass('text-in');

          switch ( itemData['INPUT_METHOD_ID'] ) {
            case '1':
              $item.find('.max-byte').val( itemData['MAX_LENGTH'] ).change();
              $item.find('.regex').val( itemData['PREG_MATCH'] ).change();
              $item.find('.single-default-value').val( itemData['SINGLE_DEFAULT_VALUE'] ).change();
              break;
            case '2':
              $item.find('.max-byte').val( itemData['MULTI_MAX_LENGTH'] ).change();
              $item.find('.regex').val( itemData['MULTI_PREG_MATCH'] ).change();
              $item.find('.multiple-default-value').val( itemData['MULTI_DEFAULT_VALUE'] ).change();
              break;
            case '3':
              $item.find('.int-min-number').val( itemData['INT_MIN'] ).change();
              $item.find('.int-max-number').val( itemData['INT_MAX'] ).change();
              $item.find('.int-default-value').val( itemData['INT_DEFAULT_VALUE'] ).change();
              break;
            case '4':
              $item.find('.float-min-number').val( itemData['FLOAT_MIN'] ).change();
              $item.find('.float-max-number').val( itemData['FLOAT_MAX'] ).change();
              $item.find('.digit-number').val( itemData['FLOAT_DIGIT'] ).change();
              $item.find('.float-default-value').val( itemData['FLOAT_DEFAULT_VALUE'] ).change();
              break;
            case '5':
              $item.find('.datetime-default-value').val( itemData['DATETIME_DEFAULT_VALUE'] ).change();
              break;
            case '6':
              $item.find('.date-default-value').val( itemData['DATE_DEFAULT_VALUE'] ).change();
              break;
            case '7':
              $item.find('.pulldown-select').val( itemData['OTHER_MENU_LINK_ID'] ).change();
              getpulldownDefaultValueList($item, itemData['PULLDOWN_DEFAULT_VALUE']); //「プルダウン選択」の初期値として選べる値を取得し、初期値に設定されているものをselectedにする。
              $item.find('.reference-item').attr('data-reference-item-id', itemData['REFERENCE_ITEM']).change();
              //newItemListのIDから項目名に変換
              if(itemData['REFERENCE_ITEM'] != null){
                const newItemListArray = itemData['REFERENCE_ITEM'].split(',');
                const newItemNameListArray = [];
                newItemListArray.forEach(function(id){
                  let existsFlg = false;
                  menuEditorArray.referenceItemList.forEach(function(data){
                    if(data['ITEM_ID'] == id){
                      newItemNameListArray.push(data['ITEM_NAME']);
                      existsFlg = true;
                    }
                  });
                  //referenceItemListに存在しない参照項目はID変換失敗(ID)を表示させる。
                  if(existsFlg == false){
                    newItemNameListArray.push(getSomeMessage("ITACREPAR_1255", {0:id}));
                  }
                });
                //重複を排除
                let setNewItemNameList = new Set(newItemNameListArray);
                let setNewItemNameListArray = Array.from(setNewItemNameList);
                //カンマ区切りの文字列に変換に参照項目上に表示
                var newItemNameList = setNewItemNameListArray.join(',');
                $item.find('.reference-item').html( newItemNameList ).change();
              }
              break;
            case '8':
              $item.find('.password-max-byte').val( itemData['PW_MAX_LENGTH'] ).change();
              break;
            case '9':
              $item.find('.file-max-size').val( itemData['UPLOAD_MAX_SIZE'] ).change();
              break;
            case '10':
              $item.find('.max-byte').val( itemData['LINK_LENGTH'] ).change();
              $item.find('.link-default-value').val( itemData['LINK_DEFAULT_VALUE'] ).change();
              break;
            case '11':
              let type3ReferenceMenuId = menuEditorArray.referenceSheetType3ItemData[itemData['TYPE3_REFERENCE']];
              $item.find('.type3-reference-menu').val( type3ReferenceMenuId ).change();
              getpulldownParameterSheetReferenceList($item, itemData['TYPE3_REFERENCE']); //「プルダウン選択」の初期値として選べる値を取得し、初期値に設定されているものをselectedにする。
              break;          }
          //プルダウン選択の初期値を取得するイベントを設定
          setEventPulldownDefaultValue($item);
          //パラメータシート参照の選択項目を取得するイベントを設定
          setEventPulldownParameterSheetReference($item);
        }

      }
    };
    recursionMenuTable( $menuTable, loadJSON['menu'] );

    history.clear();
    emptyCheck();
    previewTable();

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニューデータ取得・セット
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const getPanelParameter = function() {
// 入力値を取得する
    const parameterArray = {};
    
    parameterArray['CREATE_MENU_ID'] = $('#create-menu-id').attr('data-value'); // 項番
    parameterArray['MENU_NAME'] = $('#create-menu-name').val(); // メニュー名
    parameterArray['TARGET'] = $('#create-menu-type').val(); // 作成対象
    parameterArray['DISP_SEQ'] = $('#create-menu-order').val(); // 表示順序
    parameterArray['LAST_UPDATE_TIMESTAMP_FOR_DISPLAY'] = $('#create-menu-last-modified').attr('data-value'); // 最終更新日時
    parameterArray['LAST_UPDATE_USER'] = $('#create-last-update-user').attr('data-value'); // 最終更新者
    parameterArray['DESCRIPTION'] = $('#create-menu-explanation').val(); // 説明
    parameterArray['ACCESS_AUTH'] = getRoleListValidID( $('#permission-role-name-list').attr('data-role-id') ); // ロール
    parameterArray['UNIQUE_CONSTRAINT'] = $('#unique-constraint-list').attr('data-unique-list'); //一意制約(複数項目)
    parameterArray['NOTE'] = $('#create-menu-note').val(); // 備考
    
    // 作成対象別項目
    const type = parameterArray['TARGET'];
    if ( type === '1' || type === '3') {
      // パラメータシート
        if ( type === '1' ) {
          // ホストグループ利用有無
          const hostgroup = $('#create-menu-use-host-group').prop('checked');
          if ( hostgroup ) {
            parameterArray['PURPOSE'] = menuEditorArray.selectParamPurpose[1]['PURPOSE_ID'];
          } else {
            parameterArray['PURPOSE'] = menuEditorArray.selectParamPurpose[0]['PURPOSE_ID'];
          }
        } else {
          parameterArray['PURPOSE'] = null;
        }
        // 縦メニュー利用有無
        const vertical = $('#create-menu-use-vertical').prop('checked');
        if ( vertical ) {
          parameterArray['VERTICAL'] = '1';
        } else {
          parameterArray['VERTICAL'] = null;
        }
        parameterArray['MENUGROUP_FOR_INPUT'] = $('#create-menu-for-input').attr('data-id'); // 入力用
        parameterArray['MENUGROUP_FOR_SUBST'] = $('#create-menu-for-substitution').attr('data-id'); // 代入値
        parameterArray['MENUGROUP_FOR_VIEW'] = $('#create-menu-for-reference').attr('data-id'); // 参照用
    } else if ( type === '2') {
      // データシート
        parameterArray['PURPOSE'] = null;
        parameterArray['MENUGROUP_FOR_INPUT'] = $('#create-menu-for-input').attr('data-id'); // 入力用
    }
    // undefined, ''をnullに
    for ( let key in parameterArray ) {
      if ( parameterArray[key] === undefined || parameterArray[key] === '') {
        parameterArray[key] = null;
      }
    }
    parameterArray['number-item'] = itemCounter;
    parameterArray['number-group'] = groupCounter;
    
    return parameterArray;
};

const setPanelParameter = function( setData ) {
  // nullを空白に
  for ( let key in setData['menu'] ) {
    if ( setData['menu'][key] === null ) {
      setData['menu'][key] = '';
    }
  }
  // パネルに値をセットする
    const type = setData['menu']['TARGET'];
    $property.attr('data-menu-type', type );  
    
    if ( menuEditorMode !== 'diversion' ){
      // 項番
      $('#create-menu-id')
        .attr('data-value', setData['menu']['CREATE_MENU_ID'] )
        .text( setData['menu']['CREATE_MENU_ID'] );
      // 最終更新日時
      $('#create-menu-last-modified')
        .attr('data-value', setData['menu']['LAST_UPDATE_TIMESTAMP_FOR_DISPLAY'] )
        .text( setData['menu']['LAST_UPDATE_TIMESTAMP_FOR_DISPLAY'] );
      // 最終更新者
      $('#create-last-update-user')
        .attr('data-value', setData['menu']['LAST_UPDATE_USER'] )
        .text( setData['menu']['LAST_UPDATE_USER'] );
    }
    // ロール
    const roleList = ( setData['menu']['ACCESS_AUTH'] === undefined )? '': setData['menu']['ACCESS_AUTH'];
    $('#permission-role-name-list')
      .attr('data-role-id', roleList )
      .text( getRoleListIdToName( roleList ) );

    // 一意制約(複数項目)
    const initUniqueConstraintData = getUniqueConstraintDispData(setData['menu']['unique-constraints-current']);
    const initUniqueConstraintConv = initUniqueConstraintData.conv;
    const initUniqueConstraintName = initUniqueConstraintData.name;
    $('#unique-constraint-list')
      .text(initUniqueConstraintName)
      .attr('data-unique-list', initUniqueConstraintConv);
    menuEditorArray['unique-constraints-current'] = setData['menu']['unique-constraints-current']; //更新用に格納しなおす

    // エディットモード別
    if ( menuEditorMode === 'view') {
      $('#create-menu-name').text( setData['menu']['MENU_NAME'] ); // メニュー名
      $('#create-menu-type').text( listIdName('target', setData['menu']['TARGET'] )); // 作成対象
      $('#create-menu-order').text( setData['menu']['DISP_SEQ'] ); // 表示順序
      $('#create-menu-explanation').text( setData['menu']['DESCRIPTION'] );  // 説明
      $('#create-menu-note').text( setData['menu']['NOTE'] ); // 備考
    } else {
      $('#create-menu-name').val( setData['menu']['MENU_NAME'] ); // メニュー名
      $('#create-menu-type').val( setData['menu']['TARGET'] ); // 作成対象
      $('#create-menu-order').val( setData['menu']['DISP_SEQ'] ); // 表示順序
      $('#create-menu-explanation').val( setData['menu']['DESCRIPTION'] );  // 説明
      $('#create-menu-note').val( setData['menu']['NOTE'] ); // 備考
    } 
  
    // 作成対象項目別
    if ( type === '1' || type === '3') {
      // パラメータシート
        if ( type === '1') {
          // ホストグループ利用有無
          if ( setData['menu']['PURPOSE'] === '2' ) {
            if ( menuEditorMode === 'view') {
              $('#create-menu-use-host-group').text(textCode('0041'));
            } else {
              $('#create-menu-use-host-group').prop('checked', true );
            }
          }
        }
        // 縦メニュー利用有無
        if ( setData['menu']['VERTICAL'] === '1') {
          if ( menuEditorMode === 'view') {
            $('#create-menu-use-vertical').text(textCode('0041'));
          } else {
            $('#create-menu-use-vertical').prop('checked', true );
          }
        }
        // 入力用
        $('#create-menu-for-input')
          .attr('data-id', setData['menu']['MENUGROUP_FOR_INPUT'] )
          .text( listIdName( 'group', setData['menu']['MENUGROUP_FOR_INPUT'] ));
        // 代入値自動登録用
        $('#create-menu-for-substitution')
          .attr('data-id', setData['menu']['MENUGROUP_FOR_SUBST'] )
          .text( listIdName( 'group', setData['menu']['MENUGROUP_FOR_SUBST'] ));
        // 参照用
        $('#create-menu-for-reference')
          .attr('data-id', setData['menu']['MENUGROUP_FOR_VIEW'] )
          .text( listIdName( 'group', setData['menu']['MENUGROUP_FOR_VIEW'] ));
    } else if ( type === '2') {
      // データシート
        // 入力用
        $('#create-menu-for-input')
          .attr('data-id', setData['menu']['MENUGROUP_FOR_INPUT'] )
          .text( listIdName( 'group', setData['menu']['MENUGROUP_FOR_INPUT'] ));
    }

    //「メニュー作成状態」が2(作成済み)の場合は、メニュー名入力欄を非活性にする。
    if(menuEditorMode != 'diversion'){
      if(setData['menu']['MENU_CREATE_STATUS'] == 2){
        $('#create-menu-name').prop('disabled', true);
      }
    }

    //「メニュー作成状態」が1（未作成）の場合に各種ボタンを操作
    if(setData['menu']['MENU_CREATE_STATUS'] == 1){
      $('[data-menu-button="edit"]').parent().remove(); //「編集」ボタンを削除
      $('[data-menu-button="initialize"]').text(textCode('0046')); //「初期化」ボタンを「作成」に名称変更
      $('[data-menu-button="update-initialize"]').text(textCode('0046')); //「作成(初期化)」ボタンを「作成」に名称変更
    }

    itemCounter = setData['menu']['number-item'] + 1;
    groupCounter = setData['menu']['number-group'] + 1;
    repeatCounter = 1;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期表示
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// メニューグループ初期値
const initialMenuGroup = function() {

  const forInputID = '2100011610', // 入力用
        forSubstitutionID = '2100011611', // 代入値自動登録用
        forReference = '2100011612', // 参照用
        forInputName = listIdName( 'group', forInputID ),
        forSubstitutionName = listIdName( 'group', forSubstitutionID ),
        forReferenceName = listIdName( 'group', forReference );
  
  // 入力用
  if ( forInputName !== null ) {
    $('#create-menu-for-input')
      .attr('data-id', forInputID )
      .text( forInputName );
  }
  // 代入値自動登録用
  if ( forSubstitutionName !== null ) {
    $('#create-menu-for-substitution')
      .attr('data-id', forSubstitutionID )
      .text( forSubstitutionName );
  }
  // 参照用
  if ( forReferenceName !== null ) {
    $('#create-menu-for-reference')
      .attr('data-id', forReference )
      .text( forReferenceName );
  }
  // ACCESS_AUTHの初期値を入れる
  if ( menuEditorArray.roleList !== undefined ) {
    const roleDefault = new Array,
          roleLength = menuEditorArray.roleList.length;
    for ( let i = 0; i < roleLength; i++ ) {
      if ( menuEditorArray.roleList[i]['DEFAULT'] === 'checked') {
        roleDefault.push( menuEditorArray.roleList[i]['ROLE_ID'] );
      } 
    }
    const newRoleList = roleDefault.join(',');
    $('#permission-role-name-list').text(　getRoleListIdToName( newRoleList ) ).attr('data-role-id', newRoleList );
  }
};


if ( menuEditorMode === 'new' ) {
  initialMenuGroup();
  const currentItemCounter = itemCounter;
  addColumn( $menuTable, 'item', itemCounter++ );
  //プルダウン選択の初期値を取得するイベントを設定
  const $newColumnTarget = $menuEditor.find('#i'+currentItemCounter);
  setEventPulldownDefaultValue($newColumnTarget);
  //パラメータシート参照の選択項目を取得するイベントを設定
  setEventPulldownParameterSheetReference($newColumnTarget);
} else {
  loadMenu();
}
repeatCheck();
history.clear();

};