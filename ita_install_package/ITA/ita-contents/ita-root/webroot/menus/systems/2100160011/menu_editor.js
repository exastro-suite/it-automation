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

// テキストの無害化
const textEntities = function( text ) {
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
    text = text.replace(/^\s+|\s+$/g, '');
    text = text.replace(/\r?\n/g, '<br>');
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

// common_editor_func.js
itaTabMenu();

// 読み込み完了
$menuEditor.removeClass('load-editor');

// 言語 0:ja 1:en
let languageCode = 0;
const lang = $html.attr('lang');
if ( lang === 'en' ) languageCode = 1;
      
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
'0033':[getSomeMessage("ITACREPAR_1235"),'']
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

// HTML
const columnHeaderHTML = ''
  + '<div class="menu-column-move"></div>'
  + '<div class="menu-column-title on-hover">'
    + '<input class="menu-column-title-input" type="text" value=""'+modeDisabled+'>'
    + '<span class="menu-column-title-dummy"></span>'
  + '</div>'
  + '<div class="menu-column-function">'
    + '<div class="menu-column-delete on-hover"></div>'
    + '<div class="menu-column-copy on-hover"></div>'
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
      + '<div class="menu-column-repeat-number on-hover">REPEAT : <input class="menu-column-repeat-number-input" data-min="1" data-max="99" value="2" type="number"'+modeDisabled+'></div>'
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


if ( menuEditorMode !== 'view') {
    // 作成対象 select
    const selectParamTargetData = menuEditorArray.selectParamTarget,
          selectParamTargetDataLength = selectParamTargetData.length;
    let selectParamTargetHTML = '';
    for ( let i = 0; i < selectParamTargetDataLength ; i++ ) {
      selectParamTargetHTML += '<option value="' + selectParamTargetData[i].TARGET_ID + '">' + selectParamTargetData[i].TARGET_NAME + '</option>';
    }
    $('#create-menu-type').html( selectParamTargetHTML );

    // 用途 select
    const selectParamPurposeData = menuEditorArray.selectParamPurpose,
          selectParamPurposeDataLength = selectParamPurposeData.length;
    let selectParamPurposeHTML = '';
    for ( let i = 0; i < selectParamPurposeDataLength ; i++ ) {
      selectParamPurposeHTML += '<option value="' + selectParamPurposeData[i].PURPOSE_ID + '">' + selectParamPurposeData[i].PURPOSE_NAME + '</option>';
    }
    $('#create-menu-use').html( selectParamPurposeHTML );
}


const columnHTML = ''
  + '<div class="menu-column" data-rowpan="1" data-item-id="">'
    + '<div class="menu-column-header">'
      + columnHeaderHTML
    + '</div>'
    + '<div class="menu-column-body">'
      + '<div class="menu-column-type">'
        + '<select class="menu-column-type-select"'+modeDisabled+'>' + inputMethodHTML + '</select>'
      + '</div>'
      + '<div class="menu-column-config">'
        + '<table class="menu-column-config-table" date-select-value="1">'
          + '<tr class="multiple single">'
            + '<th>' + textCode('0011') + '<span class="input_required">*</span></th>'
            + '<td><input class="config-number max-byte" type="number" data-min="1" data-max="8192" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="multiple single">'
            + '<th>' + textCode('0012') + '</th>'
            + '<td><input class="config-text regex" type="text" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-int">'
            + '<th>' + textCode('0013') + '</th>'
            + '<td><input class="config-number int-min-number" data-min="-2147483648" data-max="2147483647" type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-int">'
            + '<th>' + textCode('0014') + '</th>'
            + '<td><input class="config-number int-max-number" data-min="-2147483648" data-max="2147483647"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float">'
            + '<th>' + textCode('0013') + '</th>'
            + '<td><input class="config-number float-min-number" data-min="-99999999999999" data-max="99999999999999"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float">'
            + '<th>' + textCode('0014') + '</th>'
            + '<td><input class="config-number float-max-number" data-min="-99999999999999" data-max="99999999999999"  type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="number-float">'
            + '<th>' + textCode('0015') + '</th>'
            + '<td><input class="config-number digit-number" data-min="1" data-max="14" type="number" value=""'+modeDisabled+'></td>'
          + '</tr>'
          + '<tr class="select">'
            + '<th>' + textCode('0016') + '<span class="input_required">*</span></th>'
            + '<td>'
              + '<select class="config-select pulldown-select"'+modeDisabled+'>' + selectPulldownListHTML + '</select>'
            + '</td>'
          + '</tr>'
          + '<tr class="all">'
            + '<td colspan="2">'
              + '<label class="required-label on-hover"><input class="config-checkbox required" type="checkbox"'+modeDisabled+'><span></span>' + textCode('0017') + '</label>'
              + '<label class="unique-label on-hover"><input class="config-checkbox unique" type="checkbox"'+modeDisabled+'><span></span>' + textCode('0018') + '</label>'
            + '</td>'
          + '</tr>'
          + '<tr class="all">'
            + '<td colspan="2"><div class="config-textarea-wrapper"><textarea class="config-textarea explanation"'+modeDisabled+'></textarea><span>' + textCode('0019') + '</span></div></td>'
          + '</tr>'
          + '<tr class="all">'
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

let workHistroy = [''],
    workCounter = 0;

// 取り消し、やり直しボタンチェック
const historyButtonCheck = function() {
    if ( workHistroy[ workCounter - 1 ] !== undefined ) {
      $undoButton.prop('disabled', false );
    } else {
      $undoButton.prop('disabled', true );
    }
    if ( workHistroy[ workCounter + 1 ] !== undefined ) {
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
    workHistroy[ workCounter ] = $clone.html();

    // 履歴追加後の履歴を削除する
    if ( workHistroy[ workCounter + 1 ] !== undefined ) {
      workHistroy.length = workCounter + 1;
    } 
    // 最大履歴数を超えた場合最初の履歴を削除する
    if ( workHistroy.length > maxHistroy ) {
      workHistroy.shift();
      workCounter--;
    }
    
    historyButtonCheck();
  },
  'undo' : function() {
    workCounter--;
    $menuTable.html( workHistroy[ workCounter ] );
    historyButtonCheck();
    previewTable();
  },
  'redo' : function() {
    workCounter++;
    $menuTable.html( workHistroy[ workCounter ] );
    historyButtonCheck();
    previewTable();
  },
  'clear' : function() {
    workCounter = 0;
    workHistroy = [];
    workHistroy[ workCounter ] = $menuTable.html();
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
  
  $addColumn.attr('id', id );

  if ( type !== 'item' && emptyFlag === true ) {
    $addColumn.find('.menu-column-group-body, .menu-column-repeat-body').html( columnEmptyHTML );
  }
  
  if ( loadData === false ) {
    $addColumnInput.val( title + ' ' + number );
  } else {
    $addColumnInput.val( name );
  }
  
  titleInputChange( $addColumnInput );
  columnHeightUpdate();
  
  if ( previewFlag === true ) {
    history.add();
    previewTable();
  }
  
  emptyCheck();

};

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
      addColumn( $menuTable, 'item', itemCounter++ );
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
    case 'update':
      if ( !window.confirm(getSomeMessage("ITACREPAR_1201") ) ) return false;
      createRegistrationData('update');
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
$menuEditor.on('change', '.config-number, .menu-column-repeat-number-input', function() {
  const $input = $( this ),
        min = Number( $input.attr('data-min') ),
        max = Number( $input.attr('data-max') );
  let value = $input.val();
  
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
  if ( $menuTable.find('.menu-column-repeat').length === 0 ) {
    $('.menu-editor-menu-button[data-menu-button="newColumnRepeat"]').prop('disabled', false );
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
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目、グループの複製
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$menuEditor.on('click', '.menu-column-copy', function(){
  const $column = $( this ).closest('.menu-column, .menu-column-group');
  
  // リピートを含む要素はコピーできないようにする
  if ( $column.find('.menu-column-repeat').length ) {
    alert('"Repeat" cannot be duplicate.');
    return false;
  }
  
  const $clone = $column.clone();
  
  // クローン追加
  $column.after( $clone );
  
  // 追加を待つ
  $clone.ready( function() {
    $clone.find('.hover').removeClass('hover');
    
    // IDをプラス
    /*
    $clone.find('.menu-column-repeat').each( function() {
      const r = repeatCounter++;
      $( this ).attr('id', 'r' + r );
    });
    */
    // IDをプラス・名前にコピー番号を追加
    $clone.find('.menu-column-title-input').each( function() {
      const $input = $( this ),
            title = $input.val(),
            $eachColumn = $input.closest('.menu-column, .menu-column-group');
      
      if ( $eachColumn.is('.menu-column') ) {
        const i = itemCounter++;
        $input.val( title + '(' + i + ')' );
        $eachColumn.attr('id', 'i' + i );
      } else if ( $eachColumn.is('.menu-column-group') ) {
        const g = groupCounter++;
        $input.val( title + '(' + g + ')' );
        $eachColumn.attr('id', 'g' + g );
      }
      $input.attr('value', $input.val() );
      titleInputChange( $input );
    });

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
      tBodyHeaderRightHTML = ''
        + '<td></td>'
        + '<td class="likeHeader">2020/01/01 00:00:00</td>'
        + '<td class="likeHeader">' + textCode('0032') + '</td>';

// リピートを含めた子の列数を返す
const childColumnCount = function( $column ) {
  let counter = $column.find('.menu-column, .column-empty').length;
  $column.find('.menu-column-repeat').each( function() {
    const columnLength = $( this ).find('.menu-column').length;
    if ( columnLength !== 0 ) {
      counter = counter + ( columnLength * ( Number( $( this ).find('.menu-column-repeat-number-input').val() ) - 1 ) );
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
  
  const previewType = $('#create-menu-type').val();
  
  // エディタ要素をTableに変換
  const tableAnalysis = function( $cols ) {
  
    // 兄弟要素配列
    let trArray = [];
    // 自分の階層を調べる
    const currentFloor = $cols.children().parents('.menu-column-group').length;
    
    $cols.children().each( function(){
      const $column = $( this );
      if ( $column.is('.menu-column, .menu-column-repeat') ) {
        // 項目・リピート
        const columnHTML = function( $targetColumn, repeatNumber ) {
          if ( repeatNumber === undefined  ) repeatNumber = 0;
          const rowspan = $targetColumn.attr('data-rowpan');
          let columnHTML = '<th rowspan="' + rowspan + '" class="sortTriggerInTbl">';
          columnHTML += textEntities( $targetColumn.find('.menu-column-title-input').val() );
          if ( repeatNumber > 1 ) {
            columnHTML += '[' + repeatNumber + ']';
          }
          columnHTML += sortMark + '</th>';
          trArray.push( columnHTML );
          // ダミーテキスト
          const selectTypeValue = $targetColumn.find('.menu-column-type-select').val();

          let dummyText = selectDummyText[ selectTypeValue ][ languageCode ],
              dummyType = selectDummyText[ selectTypeValue ][ 2 ];
              if ( dummyType === 'select' ) {
            dummyText = $targetColumn.find('.config-select').find('option:selected').text();
          }
          tbodyArray.push('<td class="' + dummyType + '">' + dummyText + '</td>');

        }
        
        if ( $column.is('.menu-column-repeat') ) {
          if ( $column.find('.menu-column').length ) {
            const repeatNumber = $column.find('.menu-column-repeat-number-input').val();
            for ( let i = 1; i <= repeatNumber; i++ ) {
              $column.find('.menu-column').each( function() {
                columnHTML( $( this ), i );
              });
            }
          } else {
            const rowspan = maxLevel - currentFloor;
            trArray.push('<th class="empty" rowspan="' + rowspan + '">Empty</th>');
            tbodyArray.push('<td>Empty</td>');
          }
        } else {
          columnHTML( $column );
        }
        
      } else if ( $column.is('.menu-column-group') ) {
        // グループ
        const colspan = childColumnCount( $column );        
        trArray.push('<th colspan="' + colspan + '">' + textEntities( $column.find('.menu-column-title-input').eq(0).val() ) + '</th>');
        tableAnalysis( $column.children('.menu-column-group-body') );
      
      } else if ( $column.is('.column-empty') ) {
        
        const rowspan = maxLevel - currentFloor;
        trArray.push('<th class="empty" rowspan="' + rowspan + '">Empty</th>');
        tbodyArray.push('<td>Empty</td>');
        
      }
    });

    // 配列がUndefinedなら初期化
    if ( tableArray[ currentFloor ] === undefined ) tableArray[ currentFloor ] = [];
  
    tableArray[ currentFloor ].push( trArray.join() );
  };
  
  // 解析スタート
  tableAnalysis ( $menuTable );
  
  // thead HTMLを生成
  const itemLength = childColumnCount( $menuTable );
  
  if ( previewType === 'parameter-sheet') {
    maxLevel++;
    tableArray.unshift('');
  }   
  const tableArrayLength = tableArray.length;
  for ( let i = 0; i < tableArrayLength; i++ ) {
    tableHTML += '<tr class="defaultExplainRow">';
    if ( i === 0 ) {
      tableHTML += tHeadHeaderLeftHTML.replace(/{{rowspan}}/g, maxLevel );
      if ( previewType === 'parameter-sheet') {
        tableHTML += tHeadParameterHeaderLeftHTML
          .replace(/{{rowspan}}/g, maxLevel )
          .replace(/{{colspan}}/g, itemLength );
      }
    }
    if ( i === 1 && previewType === 'parameter-sheet') {
      tableHTML += tHeadParameterOpeHeaderLeftHTML.replace(/{{rowspan}}/g, maxLevel - 1 );
    }
    tableHTML += tableArray[i];
    if ( i === 0 ) {
      tableHTML += tHeadHeaderRightHTML.replace(/{{rowspan}}/g, maxLevel );
    }
  }
  
  for ( let i = 1; i <= tbodyNumber; i++ ) {
    tableHTML += '<tr>' + tBodyHeaderLeftHTML.replace('{{id}}', i );
    if ( previewType === 'parameter-sheet') {
      tableHTML += tBodyParameterHeaderLeftHTML;
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

$('#create-menu-type').on('change', function(){
  $property.attr('data-menu-type', $( this ).val() );
  previewTable();
});
$('#create-menu-use').on('change', function(){
  $property.attr('data-host-type', $( this ).val() );
});
$('#create-menu-vertical-menu').on('change', function(){
  const verticalFlag = ( $( this ).val() !== '0' ) ? true : false;
  $property.attr('data-vertical-menu', verticalFlag );
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目横幅変更
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const $columnResizeLine = $('#column-resize'),
      defMinWidth = 160;
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
        menuItem = ['id','name'],
        menuListRowLength = menuGroupData.length,
        menuGroupType = ['host','host-group','reference','vertical','data-sheet'],
        menuGroupAbbreviation = ['Host','Host group','Reference','Vertical','Data sheet'],
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
        + '<th class="name">Menu group name</th>';

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
          + '<td class="name">' + menuGroupData[i]['MENU_GROUP_NAME'] + '</td>';

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
  if ( $('#create-menu-type').val() === '1') {
    // ホストorホストグループ
    if ( $('#create-menu-use').val() === '1' ) {
      type = 'host';
    } else {
      type = 'host-group';
    }
  } else {
    type = 'data-sheet';
  }
  itaModalOpen( textCode('0033'), menuGroupBody, type );
});

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
    'repeat' : {}
  };
  
  // Order用カウンター
  let itemCount = 0;
  
  // メニュー作成情報
  createMenuJSON['menu'] = menuParameter('get');
  if ( menuEditorMode === 'edit' ) {
    createMenuJSON['menu']['LAST_UPDATE_TIMESTAMP'] = menuEditorArray.selectMenuInfo['menu']['LAST_UPDATE_TIMESTAMP'];
  }
    
  const tableAnalysis = function( $cols ) {
      
    $cols.children().each( function(){
      const $column = $( this );
      if ( $column.is('.menu-column, .menu-column-repeat') ) {
        // 項目・リピート
        const columnHTML = function( $targetColumn, repeatNumber ) {
        
          if ( repeatNumber === undefined  ) repeatNumber = 0;menuParameter

          const order = itemCount++,
                selectTypeValue = $targetColumn.find('.menu-column-type-select').val();
          let key = $targetColumn.attr('id'),
              repeatFlag = false;

          // 項目名
          let itemName = $targetColumn.find('.menu-column-title-input').val();
          if ( repeatNumber > 1 ) {
            itemName += '[' + repeatNumber + ']';
            repeatFlag = true;
            key = key + '[' + repeatNumber + ']'
          }
          // カラムグループ
          let parents = '',
              parentArray = [];
          $targetColumn.parents('.menu-column-group').each( function() {
            parentArray.unshift( $( this ).find('.menu-column-title-input').val() );
          });
          parents = parentArray.join('/');
                           
          createMenuJSON['item'][key] = {
            'CREATE_ITEM_ID' : $column.attr('data-item-id'),
            'MENU_NAME' : createMenuJSON['menu']['MENU_NAME'],
            'ITEM_NAME' : itemName,
            'DISP_SEQ' : order,
            'REQUIRED' : $targetColumn.find('.required').prop('checked'),
            'UNIQUED' : $targetColumn.find('.unique').prop('checked'),
            'COL_GROUP_ID' : parents,
            'INPUT_METHOD_ID' : selectTypeValue,
            'DESCRIPTION' : $targetColumn.find('.explanation').val(),
            'NOTE' : $targetColumn.find('.note').val(),
            'REPEAT_ITEM' : repeatFlag,
            'MIN_WIDTH' : $targetColumn.css('min-width')
          }

          if ( menuEditorMode === 'edit' ) {
            if (menuEditorArray.selectMenuInfo['item'][key]) {
              createMenuJSON['item'][key]['LAST_UPDATE_TIMESTAMP'] = menuEditorArray.selectMenuInfo['item'][key]['LAST_UPDATE_TIMESTAMP'];
            }
          }
          
        
          switch ( selectTypeValue ) {
            case '1':
              createMenuJSON['item'][key]['MAX_LENGTH'] = $targetColumn.find('.max-byte').val();
              createMenuJSON['item'][key]['PREG_MATCH'] = $targetColumn.find('.regex').val();
              break;
            case '2':
              createMenuJSON['item'][key]['MULTI_MAX_LENGTH'] = $targetColumn.find('.max-byte').val();
              createMenuJSON['item'][key]['MULTI_PREG_MATCH'] = $targetColumn.find('.regex').val();
              break;
            case '3':
              createMenuJSON['item'][key]['INT_MIN'] = $targetColumn.find('.int-min-number').val();
              createMenuJSON['item'][key]['INT_MAX'] = $targetColumn.find('.int-max-number').val();
              break;
            case '4':
              createMenuJSON['item'][key]['FLOAT_MIN'] = $targetColumn.find('.float-min-number').val();
              createMenuJSON['item'][key]['FLOAT_MAX'] = $targetColumn.find('.float-max-number').val();
              createMenuJSON['item'][key]['FLOAT_DIGIT'] = $targetColumn.find('.digit').val();
              break;
            case '7':
              createMenuJSON['item'][key]['OTHER_MENU_LINK_ID'] = $targetColumn.find('.pulldown-select').val();
              break;
          }
          
        }
        
        if ( $column.is('.menu-column-repeat') ) {
          if ( $column.find('.menu-column').length ) {
            const repeatNumber = $column.find('.menu-column-repeat-number-input').val(),
                  repeatKey = $column.attr('id');
            let columns = [];
            for ( let i = 1; i <= repeatNumber; i++ ) {
              $column.find('.menu-column').each( function() {
                const repeatColumn = $( this );
                if ( i === 1 ) columns.push( repeatColumn.attr('id') );
                columnHTML( repeatColumn, i );
              });
            }
            createMenuJSON['repeat'][repeatKey] = {
              'COLUMNS' : columns,
              'REPEAT_CNT' : repeatNumber
            }

            if ( menuEditorMode === 'edit' ) {
              createMenuJSON['repeat']['LAST_UPDATE_TIMESTAMP'] = menuEditorArray.selectMenuInfo['repeat']['LAST_UPDATE_TIMESTAMP'];
            }
          }
        } else {
          columnHTML( $column );
        }
        
      } else if ( $column.is('.menu-column-group') ) {
        // グループ
        const name = $column.children('.menu-column-group-header').find('.menu-column-title-input').val(),
              key = $column.attr('id'),
              id = $column.attr('data-group-id');
        let parents = '',
            parentArray = [],
            columns = [];
        $column.parents('.menu-column-group').each( function() {
          parentArray.unshift( $( this ).find('.menu-column-title-input').val() );
        });
        parents = parentArray.join('/');
        
        $column.children('.menu-column-group-body').children().each( function() {
          columns.push( $( this ).attr('id') );
        });
        createMenuJSON['group'][key] = {
          'COL_GROUP_ID' : $column.attr('data-group-id'),
          'COL_GROUP_NAME' : name,
          'PARENT' : parents,
          'COLUMNS' : columns
        }
        tableAnalysis( $column.children('.menu-column-group-body') );
      
      }
    });

  };
    
  // トップ階層のカラム情報
  let topColumns = [];
  $menuTable.children().each( function() {
    topColumns.push( $( this ).attr('id') );
  });
  createMenuJSON['menu']['columns'] = topColumns;

  // 解析スタート
  tableAnalysis ( $menuTable );

  // JSON変換
  const menuData = JSON.stringify( createMenuJSON );
  
  if ( type === 'registration' ) {
    registerTable(menuData);
  } else if ( type === 'update' ) {
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

    // 流用新規はメニュー名を空白にする
    if ( menuEditorMode === 'diversion' ){
      loadJSON['menu']['MENU_NAME'] = '';
    }

    // パネル情報表示
    menuParameter('set', loadJSON );
    
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
              break;
            case '2':
              $item.find('.max-byte').val( itemData['MULTI_MAX_LENGTH'] ).change();
              $item.find('.regex').val( itemData['MULTI_PREG_MATCH'] ).change();
              break;
            case '3':
              $item.find('.int-min-number').val( itemData['INT_MIN'] ).change();
              $item.find('.int-max-number').val( itemData['INT_MAX'] ).change();
              break;
            case '4':
              $item.find('.float-min-number').val( itemData['FLOAT_MIN'] ).change();
              $item.find('.float-max-number').val( itemData['FLOAT_MAX'] ).change();
              $item.find('.digit').val( itemData['FLOAT_DIGIT'] ).change();
              break;
            case '7':
              $item.find('.pulldown-select').val( itemData['OTHER_MENU_LINK_ID'] ).change();
              break;
          }
        }

      }
    };
    recursionMenuTable( $menuTable, loadJSON['menu'] );

    history.clear();
    previewTable();

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニューデータ取得・セット
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const menuParameter = function( type, setData ) {

    const menuParameterList = {
      'CREATE_MENU_ID' : ['create-menu-id', 'span'],
      'MENU_NAME' : ['create-menu-name', 'text'],
      'TARGET' : ['create-menu-type', 'select'],
      'DISP_SEQ' : ['create-menu-order', 'number'],
      'PURPOSE' : ['create-menu-use', 'select'],
      'LAST_UPDATE_TIMESTAMP_FOR_DISPLAY' : ['create-menu-last-modified', 'span'],
      'LAST_UPDATE_USER' : ['create-last-update-user', 'span'],
      'MENUGROUP_FOR_H' : ['create-menu-host', 'span'],
      'MENUGROUP_FOR_HG' : ['create-menu-host-group', 'span'],
      'MENUGROUP_FOR_VIEW' : ['create-menu-reference', 'span'],
      'MENUGROUP_FOR_CONV' : ['create-menu-vertical', 'span'],
      'MENUGROUP_FOR_CMDB' : ['create-menu-data-sheet', 'span'],
      'DESCRIPTION' : ['create-menu-explanation', 'textarea'],
      'NOTE' : ['create-menu-note', 'textarea']
    };
    // 流用する場合はID、表示順序、更新日時、更新者、説明、備考をnullに
    if ( menuEditorMode === 'diversion' ){
      delete menuParameterList['CREATE_MENU_ID'];
      delete menuParameterList['DISP_SEQ'];
      delete menuParameterList['LAST_UPDATE_TIMESTAMP'];
      delete menuParameterList['LAST_UPDATE_TIMESTAMP_FOR_DISPLAY'];
      delete menuParameterList['LAST_UPDATE_USER'];
      delete menuParameterList['DESCRIPTION'];
      delete menuParameterList['NOTE'];
    }

    if ( type === 'get' ) {

      let parameterArray = {};
      for ( let key in menuParameterList ) {
        const $target = $( '#' + menuParameterList[ key ][0] );
        if ( $target.is(':visible') ) {
          if ( menuParameterList[ key ][1] === 'span' ) {
            if ( key === 'CREATE_MENU_ID' || key === 'LAST_UPDATE_TIMESTAMP' || key === 'LAST_UPDATE_USER') {
              parameterArray[ key ] = $target.attr('data-value');
            } else {
              parameterArray[ key ] = $target.attr('data-id');
            }
          } else {
            parameterArray[ key ] = $target.val();
          }
        } else {
          parameterArray[ key ] = null;
        }
      }
      parameterArray['number-item'] = itemCounter;
      parameterArray['number-group'] = groupCounter;

      return parameterArray;

    } else if ( type === 'set' ) {
      if ( menuEditorMode === 'view') {
        const veticalFlag = ( setData['menu']['MENUGROUP_FOR_CONV'] !== null )? true : false;
        $property.attr({
          'data-menu-type': setData['menu']['TARGET'],
          'data-host-type': setData['menu']['PURPOSE'],
          'data-vertical-menu': veticalFlag
        });
        const viewModePanelInfo = [
          ['#create-menu-id', setData['menu']['CREATE_MENU_ID'] ],
          ['#create-menu-name', setData['menu']['MENU_NAME'] ],
          ['#create-menu-type', listIdName( 'target', setData['menu']['TARGET'] )],
          ['#create-menu-order', setData['menu']['DISP_SEQ'] ],
          ['#create-menu-use', listIdName( 'use', setData['menu']['PURPOSE'] )],
          ['#create-menu-host', listIdName( 'group', setData['menu']['MENUGROUP_FOR_H'] )],
          ['#create-menu-host-group', listIdName( 'group', setData['menu']['MENUGROUP_FOR_HG'] )],
          ['#create-menu-reference', listIdName( 'group', setData['menu']['MENUGROUP_FOR_VIEW'] )],
          ['#create-menu-vertical', listIdName( 'group', setData['menu']['MENUGROUP_FOR_CONV'] )],
          ['#create-menu-data-sheet', listIdName( 'group', setData['menu']['MENUGROUP_FOR_CMDB'] )],
          ['#create-menu-explanation', setData['menu']['DESCRIPTION'] ],
          ['#create-menu-note', setData['menu']['NOTE'] ],
          ['#create-menu-last-modified', setData['menu']['LAST_UPDATE_TIMESTAMP_FOR_DISPLAY'] ],
          ['#create-last-update-user', setData['menu']['LAST_UPDATE_USER']]
        ],
          viewModePanelInfoLength = viewModePanelInfo.length;
              
        for ( let i = 0; i < viewModePanelInfoLength; i++ ) {
          let panelText = viewModePanelInfo[i][1];
          if ( panelText === null ) panelText = ''
          $( viewModePanelInfo[i][0] ).text( panelText );
        }
      } else {
        for ( let key in menuParameterList ) {
          const $target = $( '#' + menuParameterList[ key ][0] );
          if ( menuEditorMode !== 'view') {
            if ( menuParameterList[ key ][1] === 'span' ) {
              $target.attr('data-value', setData['menu'][ key ] );
              let panelText = '';
              if ( $target.closest('#menu-group').length ) {
                $target.attr('data-id', setData['menu'][ key ] );
                panelText = listIdName( 'group', setData['menu'][ key ] );
              } else {
                panelText = setData['menu'][ key ];
              }
              if ( panelText === null ) panelText = '';
              $target.text( panelText );
            } else {
              $target.val( setData['menu'][ key ] ).change();
            }
          } else {
            let panelText = setData['menu'][ key ];
            if ( panelText === null ) panelText = '';
            $target.text( panelText );
          }
        }
      }
      itemCounter = setData['menu']['number-item'] + 1;
      groupCounter = setData['menu']['number-group'] + 1;
      repeatCounter = 1;
    }
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期表示
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

if ( menuEditorMode === 'new' ) {
  addColumn( $menuTable, 'item', itemCounter++ );
} else {
  loadMenu();
}
history.clear();

};