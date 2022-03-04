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

// Conductorで使用する各種リスト
// 00_javascriptでも使用する
let conductorUseList = {};

// DBから値を取得したときの分岐用
let conductorGetMode = 'starting';

// Editorモード
let conductorEditorMode = '';

// Editorの初期設定
function initEditor( mode ) {
    
    conductorUseList.orchestratorName = {
      '0': 'Unknown',
      '3': 'Ansible Legacy',
      '4': 'Ansible Pioneer',
      '5': 'Ansible Legacy Role',
      '6': 'Cobbler',
      '8': 'DSC',
      '9': 'OpenStack',
      '10': 'Terraform'
    };

    conductorMode( mode );
    
    // Movementリストを作成
    const $movementList = $('#movement-list'),
          $movementListRows = $('#movement-list-rows'),
          movementList = conductorUseList.movementList,
          movementLength = movementList.length;
    
    // Orchestratorリストを作成
    const orchestratorList = new Array();
    for ( let i = 0; i < movementLength; i++ ) {
      if ( orchestratorList.indexOf( movementList[i].ORCHESTRATOR_ID ) === -1 ) {
        orchestratorList.push( movementList[i].ORCHESTRATOR_ID );
      }
    }
    const orchestratorLength = orchestratorList.length;
    let orchestratorListHTML = '',
        orchestratorListStyle = '';
    for ( let i = 0; i < orchestratorLength; i++ ) {
      $movementList.attr('data-orche' + orchestratorList[i], true );
      orchestratorListHTML += '<li>'
      + '<label class="property-label">'
        + '<input type="checkbox" id="orchestrator' + orchestratorList[i] + '" name="filter-orchestrator" checked> '
        + conductorUseList.orchestratorName[orchestratorList[i]] + '</label></li>';
      orchestratorListStyle += '#movement-list[data-orche' + orchestratorList[i] + '="false"] .orche' + orchestratorList[i]
      + ' {display:none!important;}';
    }
    $movementList.prepend('<style>' + orchestratorListStyle + '</style>');
    $('#orchestrator-list').html( orchestratorListHTML );
    
    // Movementリストをソートする
    // [0: {PATTERN_ID: "1", ORCHESTRATOR_ID: "3", PATTERN_NAME: "Ansible", ThemeColor: "orange"}]
    // name（対象）,sort（asc or desc）,type（string or number）
    const sortMovementList = function( name, sort, type ) {
      let movementListHTML = '';
      movementList.sort( function( a, b ){
        if ( type === 'string') {
          const al = a[ name ].toLowerCase(),
                bl = b[ name ].toLowerCase();
          if ( sort === 'desc') {
            if( al < bl ) return 1;
            if( al > bl ) return -1;
          } else if ( sort === 'asc') {
            if( al > bl ) return 1;
            if( al < bl ) return -1;
          }
        } else if ( type === 'number') {
          if ( sort === 'desc') {
            return Number( b[ name ] ) - Number( a[ name ] );
          } else if ( sort === 'asc') {
            return Number( a[ name ] ) - Number( b[ name ] );
          }
        }
      });
      for ( let i = 0; i < movementLength; i++ ) {
        let orcheName = conductorUseList.orchestratorName[ movementList[i].ORCHESTRATOR_ID ];
        if ( orcheName === undefined ) orcheName = 'Unknown';
        const orchestrator = orcheName.toLocaleLowerCase().replace(/\s/g, '-');
        movementListHTML += ''
          + '<tr class="orche' + movementList[i].ORCHESTRATOR_ID + '">'
            + '<th class="movement-list-orchestrator" title="' + orcheName + '"><span class="add-node '+ orchestrator +'" '
            + 'data-id="' + movementList[i].PATTERN_ID + '"></span></th>'
            + '<th class="movement-list-id"><div>' + movementList[i].PATTERN_ID + '</div></th>'
            + '<td class="movement-list-name"><div>' + movementList[i].PATTERN_NAME + '</div></td>'
          + '</tr>';
      }
      $movementListRows.html( movementListHTML );
      if ( $movementList.attr('data-filter') === 'name') {
        $movementFilter.trigger('input');
      } else {
        $movementIdFilter.trigger('input');
      }
    };
    
    // Movement Filter
    const $movementFilter = $('#movement-filter'),
          $movementIdFilter = $('#movement-filter-id');
    const movementFilter = function( inputValue, target ) {
      // スペースでsplit
      const valueArray = inputValue.split(/[\x20\u3000]+/),
            valueArrayLength = valueArray.length;
    
      // IDだった場合
      if ( target === '.movement-list-id') {
        for ( let i = 0; i < valueArrayLength; i++ ) {
          // 数値にならない場合消す
          if ( isNaN ( Number( valueArray[i] ) ) ) {
             valueArray[i] = '';
          } else if ( valueArray[i] !== '') {
            // 完全一致用
            valueArray[i] = '^' + valueArray[i] + '$';
          }
        }
      }
      // or結合
      inputValue = valueArray.filter(function(v){return v !== '';}).join('|');

      const regExp = new RegExp( inputValue, "i");

      if ( inputValue !== '' ) {
        $movementList.find('.node-table tbody').find('tr').each( function(){
          const $tr = $( this ),
                movementName = $tr.find( target ).text();
          if ( regExp.test( movementName ) ) {
            $tr.removeClass('filter-hide');
          } else {
            $tr.addClass('filter-hide');
          }
        });
      } else {
        $movementList.find('.filter-hide').removeClass('filter-hide');
      }
    };
    $movementFilter.on('input', function(){
      movementFilter( $(this).val(), '.movement-list-name');
    });
    $movementIdFilter.on('input', function(){
      movementFilter($(this).val(), '.movement-list-id');
    });
    
    // ソート
    $movementList.find('.movement-list-sort').on('click', function(){
      const $sort = $( this ),
            sortTarget = $sort.attr('data-sort'),
            sortType = $sort.attr('data-sort-type'),
            sort = $sort.is('.asc')? 'desc': 'asc';
      $movementList.find('.asc, .desc').removeClass('asc desc');
      $sort.addClass( sort );
      sortMovementList( sortTarget, sort, sortType );
    });
    
    // デフォルトはID昇順
    $movementList.attr('data-filter', 'name');
    $movementList.find('.movement-list-id .movement-list-sort').addClass('asc');
    sortMovementList('PATTERN_ID', 'asc', 'number');
    
    // Filter Setting画面
    const $filterSetting = $('#movement-filter-setting');
    // Filter設定オープン
    $movementList.find('.filter-setting-btn').on('click', function(){
      $filterSetting.show();
      // Text入力欄反映
      const inputType = $movementList.attr('data-filter');
      if ( inputType === 'name') {
        $('#filter-target-name').prop('checked', true );
      } else if ( inputType === 'id') {
        $('#filter-target-id').prop('checked', true );
      }
      // Orchestratorチェックボックスの反映
      for ( let i = 0; i < orchestratorLength; i++ ) {
        const flag = ( $movementList.attr('data-orche' + orchestratorList[i] ) === 'true' )? true: false;
        $('#orchestrator' + orchestratorList[i] ).prop('checked', flag );
      }
    });
    
    // Filter設定キャンセル
    $('#movement-filter-cancel').on('click', function(){
      $filterSetting.hide();
    });
    
    // Filter設定決定
    $('#movement-filter-ok').on('click', function(){
      const inputType = $filterSetting.find('[name="filter-target"]:checked').attr('id');
      if ( inputType === 'filter-target-name') {
        $movementList.attr('data-filter', 'name');
        $movementFilter.trigger('input');
      } else if ( inputType === 'filter-target-id') {
        $movementList.attr('data-filter', 'id');
        $movementIdFilter.trigger('input');
      }
      for ( let i = 0; i < orchestratorLength; i++ ) {
        $movementList.attr('data-orche' + orchestratorList[i], $('#orchestrator' + orchestratorList[i] ).prop('checked') );
      }
      $filterSetting.hide();
    });
    
    // Editor実行
    conductorEditor();  
}

function conductorFooterButtonDisabled( disabledFlag ) {
  $('#editor-footer').find('.editor-menu-button').prop('disabled', disabledFlag );
}

function conductorMode( mode, config ) {
  if ( config === undefined ) {
    config = '';
  }
  conductorEditorMode = mode;
  $('#editor').attr({
    'data-editor-mode': conductorEditorMode,
    'data-editor-config': config
  });
  $('#editor-mode').text( conductorEditorMode.toUpperCase() ); 
}

function conductorEditor() {

'use strict';

// 言語
const language = editor.getLang();

// 読み込み用input set
editor.readText.set();

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   コンダクターエディタ初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// Conductor,Operation, Movement IDから名前を返す
const listIdName = function( type, id ) {
  if ( id !== undefined ) {
    let list,idKey,nameKey,name;
    if ( type === 'conductor') {
      list = conductorUseList.conductorCallList;
      idKey = 'CONDUCTOR_CLASS_NO';
      nameKey = 'CONDUCTOR_NAME';
    } else if ( type === 'operation') {
      list = conductorUseList.operationList;
      idKey = 'OPERATION_NO_IDBH';
      nameKey = 'OPERATION_NAME';
    } else if ( type === 'movement') {
      list = conductorUseList.movementList;
      idKey = 'PATTERN_ID';
      nameKey = 'PATTERN_NAME';
    } else if ( type === 'symphony') {
      list = conductorUseList.symphonyCallList;
      idKey = 'SYMPHONY_CLASS_NO';
      nameKey = 'SYMPHONY_NAME';
    } else if ( type === 'role') {
      list = conductorUseList.roleList;
      idKey = 'ROLE_ID';
      nameKey = 'ROLE_NAME';
    }
    
    if ( list !== undefined ) {
      const listLength = list.length;
      for ( let i = 0; i < listLength; i++ ) {
        if ( Number( list[i][idKey] ) === Number( id ) ) {
          if ( type === 'movement') {
            name = [ list[i][nameKey], list[i]['ORCHESTRATOR_ID'] ];
          } else {
            name = list[i][nameKey];
          }
          return name;
        }
      }
      return undefined; 
    }
  } else {
    return undefined;  
  }
};

// メッセージ
const messageText = {
  "0001": {
    "ja": getSomeMessage("ITABASEC020112"),
    "en": getSomeMessage("ITABASEC020112"),
    "type": "notice"},
  "0002": {
    "ja": getSomeMessage("ITABASEC020113"),
    "en": getSomeMessage("ITABASEC020113"),
    "type": "notice"},
  "0003": {
    "ja": getSomeMessage("ITABASEC020114"),
    "en": getSomeMessage("ITABASEC020114"),
    "type": "notice"},
  "0004": {
    "ja": getSomeMessage("ITABASEC020115"),
    "en": getSomeMessage("ITABASEC020115"),
    "type": "notice"},
  "0005": {
    "ja": getSomeMessage("ITABASEC020116"),
    "en": getSomeMessage("ITABASEC020116"),
    "type": "warning"},
  "0006": {
    "ja": getSomeMessage("ITABASEC020117"),
    "en": getSomeMessage("ITABASEC020117"),
    "type": "notice"},
  "0007": {
    "ja": getSomeMessage("ITABASEC020118"),
    "en": getSomeMessage("ITABASEC020118"),
    "type": "warning"},
  "1001": {
    "ja": getSomeMessage("ITABASEC020119"),
    "en": getSomeMessage("ITABASEC020119"),
    "type": "error"},
  "1002": {
    "ja": getSomeMessage("ITABASEC020120"),
    "en": getSomeMessage("ITABASEC020120"),
    "type": "done"},
  "2001": {
    "ja": getSomeMessage("ITABASEC020121"),
    "en": getSomeMessage("ITABASEC020121"),
    "type": "confirm"},
  "2002": {
    "ja": getSomeMessage("ITABASEC020122"),
    "en": getSomeMessage("ITABASEC020122"),
    "type": "confirm"}
};
const message = function( messageID ) {
  if ( messageText[ messageID ].type === 'alert' ) {
    return alert( messageText[ messageID ][ language ] );
  } else if ( messageText[ messageID ].type === 'confirm' ) {
    return confirm( messageText[ messageID ][ language ] );
  } else {
    editor.log.set( messageText[ messageID ].type, messageText[ messageID ][ language ] );
  }
};

// Conductorステータス
const conductorStatus = {
  '1': ['wait', getSomeMessage("ITABASEC020301")],    //未実行:1
  '2': ['wait', getSomeMessage("ITABASEC020302")],    //未実行(予約):2
  '3': ['running', getSomeMessage("ITABASEC020303")],    //実行中:3
  '4': ['running', getSomeMessage("ITABASEC020304")],    //実行中(遅延):4
  '5': ['done', getSomeMessage("ITABASEC020305")],    //正常終了:5
  '6': ['stop', getSomeMessage("ITABASEC020306")],    //緊急停止:6
  '7': ['fail', getSomeMessage("ITABASEC020307")],    //異常終了:7
  '8': ['error', getSomeMessage("ITABASEC020308")],    //想定外エラー:8
  '9': ['cancel', getSomeMessage("ITABASEC020309")],    //予約取消:9
  '10': ['error', getSomeMessage("ITABASEC020310")],  //想定外エラー(ループ):10
  '11': ['error', getSomeMessage("ITABASEC020311")]   //警告終了:11
};

// Nodeステータス
const nodeStatus = {
  '1': ['wait', getSomeMessage("ITABASEC010501") ], // 未実行
  '2': ['running', getSomeMessage("ITABASEC010503") ], // 準備中
  '3': ['running', getSomeMessage("ITABASEC010504") ], // 実行中
  '4': ['running', getSomeMessage("ITABASEC010505") ], // 実行中（遅延）
  '5': ['done', getSomeMessage("ITABASEC010507") ], // 実行完了
  '6': ['fail', getSomeMessage("ITABASEC010306") ], // 異常終了
  '7': ['stop', getSomeMessage("ITABASEC010304") ], // 緊急停止
  '8': ['pause',getSomeMessage("ITABASEC020111") ], //停止中
  '9': ['done', getSomeMessage("ITABASEC010305") ], // 正常終了
  '10': ['error', getSomeMessage("ITABASEC010502") ], // 準備エラー
  '11': ['error', getSomeMessage("ITABASEC010307") ], // 想定外エラー
  '12': ['skip', getSomeMessage("ITABASEC010702") ], // Skip完了
  '13': ['skip', getSomeMessage("ITABASEC010703") ], // Skip後保留中
  '14': ['skip', getSomeMessage("ITABASEC010704") ], // Skip完了
  '15': ['warning', getSomeMessage("ITABASEC020311") ], // 警告終了
};

// Movementステータス
const movementEndStatus = {
  '6': ['fail', getSomeMessage("ITABASEC010306") ], // 異常終了
  '7': ['stop', getSomeMessage("ITABASEC010304") ], // 緊急停止
  '9': ['done', getSomeMessage("ITABASEC010305") ], // 正常終了
  '10': ['error', getSomeMessage("ITABASEC010502") ], // 準備エラー
  '11': ['error', getSomeMessage("ITABASEC010307") ], // 想定外エラー
  '14': ['skip', getSomeMessage("ITABASEC010702") ], // Skip完了
  '15': ['warning', getSomeMessage("ITABASEC020311") ], // 警告終了
  '9999': ['other', 'Other'],
};

// End flag
const endType = {
  '5': ['done', getSomeMessage("ITABASEC020305")], // 正常
  '11': ['warning', getSomeMessage("ITABASEC020311")], // 警告
  '7': ['error', getSomeMessage("ITABASEC020307")] // 異常
};

// MERGEテータス
const mergeStatus = {
  '0': ['standby'],
  '1': ['waiting'],
  '2': ['complete'],
  '3': ['unused']
};

// PAUSEステータス
const pauseStatus = {
  '0': ['standby','PAUSE'],
  '1': ['pause','PAUSE'],
  '2': ['resume','RESUME'],
};

// jQueryオブジェクトをキャッシュ
const $window = $( window ),
      $body = $('body'),
      $editor = $('#editor'),
      $canvasVisibleArea = $('#canvas-visible-area'),
      $canvas = $('#canvas'),
      $artBoard = $('#art-board');

$editor.removeClass('load-wait');
conductorGetMode = '';

// 初期値
const initialValue = {
  'canvasWidth' : 16400,
  'canvasHeight' : 16400,
  'artboradWidth' : 16000,
  'artboradHeight' : 16000,
  'debug' : false
};
if ( editor.getParam('debug') === "true" ) initialValue.debug = true;

// エディター値
let editorValue = {
  'scaling' : 1,
  'oldScaling' : 1,
  'setStorage' : false
};

// 接続禁止パターン（ out Type : [in Types] ）
const connectablePattern = {
  'start' : ['conditional-branch','merge','pause','status-file-branch'],
  'conditional-branch' : ['conditional-branch','merge','status-file-branch'],
  'parallel-branch' : ['conditional-branch','parallel-branch','pause','status-file-branch'],
  'status-file-branch': ['conditional-branch','pause','status-file-branch','merge'],
  'merge' : ['conditional-branch','merge','status-file-branch'],
  'pause' : ['pause','end','status-file-branch'],
  'call_s' : ['status-file-branch'],
  'call' : ['status-file-branch']
};

// ID 連番用
let g_NodeCounter = 1,
    g_TerminalCounter = 1,
    g_EdgeCounter = 1;

// 選択中のNode ID
let g_selectedNodeID = [];

// Conductor構造データ
let conductorData;

const setInitialConductorData = function() {
  conductorData = {};
  conductorData['config'] = {
    'nodeNumber' : g_NodeCounter,
    'terminalNumber' : g_TerminalCounter,
    'edgeNumber' : g_EdgeCounter
  }
  conductorData['conductor'] = {
    'id': null,
    'conductor_name': null,
    'note': null,
    'LUT4U': null
  };
  // ACCESS_AUTHの初期値を入れる
  if ( conductorUseList.roleList !== undefined ) {
    const roleDefault = new Array,
          roleLength = conductorUseList.roleList.length;
    for ( let i = 0; i < roleLength; i++ ) {
      if ( conductorUseList.roleList[i]['DEFAULT'] === 'checked') {
        roleDefault.push( conductorUseList.roleList[i]['ROLE_ID'] );
      } 
    }
    conductorData['conductor']['ACCESS_AUTH'] = roleDefault.join(',');
  }
}
setInitialConductorData();

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キャンバス共通
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

let g_canvasVisibleArea, g_canvas, g_artBoard,
    g_canvas_p = { 'x' : 0, 'y' : 0 },
    g_artBoard_p = { 'x' : 0, 'y' : 0 };

// キャンバスの位置・サイズをセットする
const setSize = function ( $obj ) {
  return {
  'w' : $obj.width(),
  'h' : $obj.height(),
  'l' : $obj.offset().left,
  't' : $obj.offset().top
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  指定位置にキャンバスを移動する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const canvasPosition = function( positionX, positionY, scaling, duration ) {

    if ( duration === undefined ) duration = 0.3;

    // アニメーションさせる場合は一時的に操作できないようにする
    if ( duration !== 0 ) {
      editor.actionMode.set('editor-pause');
      const moveX = positionX - g_canvas_p.x,
            moveY = positionY - g_canvas_p.y;
      $canvas.css({
        'transform': 'translate(' + moveX + 'px,' + moveY + 'px) scale(' + scaling + ')',
        'transition-duration': duration + 's'
      });
      setTimeout( function(){
        $canvas.css({
          'left': positionX,
          'top': positionY,
          'transform': 'translate(0,0) scale(' + scaling + ')',
          'transition-duration': '0s'
        });
        editor.actionMode.clear();
      }, duration * 1000 );
    } else {
      $canvas.css({
        'left': positionX,
        'top': positionY,
        'transform': 'translate(0,0) scale(' + scaling + ')'
      });
    }
    
    $canvas.removeAttr('data-scale');

    if ( scaling <= 0.1 ) {
      $canvas.attr('data-scale','10');
    } else if ( scaling <= 0.25 ) {
      $canvas.attr('data-scale','25');
    } else if ( scaling <= 0.5 ) {
      $canvas.attr('data-scale','50');
    } else if ( scaling <= 0.75 ) {
      $canvas.attr('data-scale','75');
    }
    
    g_canvas_p.x = positionX;
    g_canvas_p.y = positionY;
    editorValue.scaling = scaling;
    editorValue.oldScaling = scaling;
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//  キャンバスのポジションをリセット
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const canvasPositionReset = function( duration ) {
    
    if ( duration === undefined ) duration = 0.3;
    
    g_canvasVisibleArea = setSize( $canvasVisibleArea );
    g_canvas = setSize( $canvas );
    g_artBoard = setSize( $artBoard );
    
    g_artBoard_p = {
      'x' : Math.round( ( g_canvas.w / 2 ) - ( g_artBoard.w / 2 ) ),
      'y' : Math.round( ( g_canvas.h / 2 ) - ( g_artBoard.h / 2 ) )
    };
    $artBoard.css({
      'left' : g_artBoard_p.x,
      'top' : g_artBoard_p.y
    });
    
    let resetX, resetY;
    if ( $('#node-1.conductor-start').length ) {
      // Start nodeがある場合は基準にする
      const $start = $('#node-1.conductor-start'),
            adjustPosition = 32; // 端Padding
      resetX = -( Number( $start.css('left').replace('px','') ) + g_artBoard_p.x - adjustPosition );
      resetY = -( Number( $start.css('top').replace('px','') ) + g_artBoard_p.y - ( ( g_canvasVisibleArea.h / 2 ) - ( $start.outerHeight() / 2 ) ) );
    } else {
      // キャンバスのセンター
      resetX = Math.round( - ( g_canvas.w / 2 ) + ( g_canvasVisibleArea.w / 2 ) );
      resetY = Math.round( - ( g_canvas.h / 2 ) + ( g_canvasVisibleArea.h / 2 ) );
    }
    canvasPosition( resetX, resetY, 1, duration );
    
}

// 各種サイズをセット
$canvas.css({
  'width' : initialValue.canvasWidth,
  'height' : initialValue.canvasHeight
});
$artBoard.css({
  'width' : initialValue.artboradWidth,
  'height' : initialValue.artboradHeight
});
canvasPositionReset( 0 );

// ウィンドウリサイズで表示エリアのサイズを再取得
const reiszeEndTime = 200;
let resizeTimerID;
$window.on('resize.editor', function(){

    clearTimeout( resizeTimerID );

    resizeTimerID = setTimeout( function(){
      g_canvasVisibleArea = setSize( $canvasVisibleArea );
    }, reiszeEndTime );

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   右クリックでキャンバスを移動する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$canvasVisibleArea.on({
    'mousedown.canvas': function( e ) {

      if ( e.buttons === 2 ) {
      
        e.preventDefault();
        
        editor.actionMode.set('canvas-move');

        const mouseDownPositionX = e.pageX,
              mouseDownPositionY = e.pageY;
              
        let moveX = 0,
            moveY = 0;

        $window.on({
          'mousemove.canvas': function( e ){

            moveX = e.pageX - mouseDownPositionX;
            moveY = e.pageY - mouseDownPositionY;
            
            $canvas.css({
              'transform' : 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(' + editorValue.scaling + ')'
            });

          },
          'contextmenu.canvas': function( e ) {
            if ( initialValue.debug === false ) e.preventDefault();
            $( this ).off('contextmenu.canvas');
          },
          'mouseup.canvas': function(){
            $( this ).off('mousemove.canvas mouseup.canvas');
            editor.actionMode.clear();
            
            g_canvas_p.x = g_canvas_p.x + moveX;
            g_canvas_p.y = g_canvas_p.y + moveY;
            
            $canvas.css({
              'left' : g_canvas_p.x,
              'top' : g_canvas_p.y,
              'transform' : 'translate(0,0) scale(' + editorValue.scaling + ')'
            });
          }
        });
        
      }
    },
    'contextmenu': function( e ) {
      // コンテキストメニューは表示しない
      if ( initialValue.debug === false ) e.preventDefault();
    }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キャンバスの拡縮
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const scalingArray = [
        0.025, 0.05, 0.075,
        0.1, 0.2, 0.25, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9,
        1, 1.25, 1.5 , 1.75, 2, 2.5, 3, 4, 5, 6, 7, 8, 9
      ], // マウススクロール拡縮倍率
      scalingArrayLength = scalingArray.length - 1;

const canvasScaling = function( zoomType, positionX, positionY ){

    if ( positionX === undefined ) positionX = g_canvas_p.x / 2 / editorValue.scaling;
    if ( positionY === undefined ) positionY = g_canvas_p.y / 2 / editorValue.scaling;
    
    let scaling = editorValue.scaling,
        scalingNum = scalingArray.indexOf( scaling );

    if ( zoomType === 'in') {
      if ( scalingNum === -1 ) {
        for ( let i = scalingArrayLength - 1; i >= 0; i-- ) {
          if ( scaling > scalingArray[ i ] ) {
            scalingNum = i;
            break;
          }
        }
      }
      scalingNum = ( scalingNum < scalingArrayLength ) ? scalingNum + 1 : scalingArrayLength;
      scaling = scalingArray[ scalingNum ];
    } else if ( zoomType === 'out') {
      if ( scalingNum === -1 ) {
        for ( let i = 0; i < scalingArrayLength; i++ ) {
          if ( scaling < scalingArray[ i ] ) {
            scalingNum = i;
            break;
          }
        }
      }
      scalingNum = ( scalingNum > 1 ) ? scalingNum - 1 : 0;
      scaling = scalingArray[ scalingNum ];
    } else if ( typeof zoomType === 'number') {
      scaling = zoomType;
    }

    if ( scaling !== editorValue.oldScaling ) {
      const commonX = ( ( g_canvas.w * scaling ) - ( g_canvas.w * editorValue.oldScaling ) ) / 2,
            commonY = ( ( g_canvas.h * scaling ) - ( g_canvas.h * editorValue.oldScaling ) ) / 2,
            adjustX = ( ( g_canvas.w / 2 ) - positionX ) * Math.abs( scaling - editorValue.oldScaling ),
            adjustY = ( ( g_canvas.h / 2 ) - positionY ) * Math.abs( scaling - editorValue.oldScaling );
      
      if ( zoomType === 'in') {
        positionX= Math.round( g_canvas_p.x - commonX + adjustX );
        positionY = Math.round( g_canvas_p.y - commonY + adjustY );
      } else if ( zoomType === 'out') {
        positionX = Math.round( g_canvas_p.x - commonX - adjustX );
        positionY = Math.round( g_canvas_p.y - commonY - adjustY );
      }

      canvasPosition( positionX, positionY, scaling, 0 );
      
    }
  
}

// マウスホイールで拡縮
const mousewheelevent = ('onwheel' in document ) ? 'wheel' : ('onmousewheel' in document ) ? 'mousewheel' : 'DOMMouseScroll';
$canvasVisibleArea.on( mousewheelevent, function( e ){

    e.preventDefault();
    
    if ( e.buttons === 0 ) {

      const mousePositionX = Math.floor( ( e.pageX - $( this ).offset().left - g_canvas_p.x ) / editorValue.scaling ),
            mousePositionY = Math.floor( ( e.pageY - $( this ).offset().top - g_canvas_p.y ) / editorValue.scaling ),
            delta = e.originalEvent.deltaY ? - ( e.originalEvent.deltaY ) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : - ( e.originalEvent.detail );

      if ( e.shiftKey ) {
      } else {
        // 縦スクロール
        if ( delta < 0 ){
          canvasScaling( 'out', mousePositionX, mousePositionY );
        } else {
          canvasScaling( 'in', mousePositionX, mousePositionY);
        }

      }
    
    }

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   全体表示
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const nodeViewAll = function( duration ) {
  
  const canvasWidth = $canvasVisibleArea.width(),
        canvasHeight = $canvasVisibleArea.height();
        
  // 端の座標を求める
  let x1, y1, x2, y2;
  for ( let key in conductorData ) {
    if ( RegExp('^node-').test( key ) ) {
      let nodeX1 = Number( conductorData[ key ].x ),
          nodeY1 = Number( conductorData[ key ].y ),
          nodeX2 = Number( conductorData[ key ].x ) + Number( conductorData[ key ].w ),
          nodeY2 = Number( conductorData[ key ].y ) + Number( conductorData[ key ].h );
      // Note分調整
      const note = conductorData[ key ].note;
      if ( editor.checkValue( note ) ) {
        const $note = $('#' + key ).find('.node-note'),
              notePosition = 16,
              noteWidth = $note.outerWidth(),
              noteHeight = $note.outerHeight();
        if ( noteWidth > Number( conductorData[ key ].w ) ) {
          const noteXNum = ( noteWidth - Number( conductorData[ key ].w ) ) / 2;
          nodeX1 = nodeX1 - noteXNum;
          nodeX2 = nodeX2 + noteXNum;
        }
        nodeY1 = nodeY1 - noteHeight - notePosition;
      }
      // 左上座標
      if ( x1 > nodeX1 || x1 === undefined ) x1 = nodeX1;
      if ( y1 > nodeY1 || y1 === undefined ) y1 = nodeY1;
      // 右下座標
      if ( x2 < nodeX2 || x2 === undefined ) x2 = nodeX2;
      if ( y2 < nodeY2 || y2 === undefined ) y2 = nodeY2;
    }
  }
  
  // センター座標と表示倍率
  const adjustPosition = 32, // 端Padding
        viewWidth = x2 - x1 + ( adjustPosition * 2 ),
        viewHeight = y2 - y1 + ( adjustPosition * 2 ),
        centerX = x1 + ( ( x2 - x1 ) / 2 ),
        centerY = y1 + ( ( y2 - y1 ) / 2 ),
        scalingVertical = Math.floor( canvasWidth / viewWidth * 1000 ) / 1000,
        scalingHorizontal = Math.floor( canvasHeight / viewHeight * 1000 ) / 1000,
        scaling = ( scalingVertical < scalingHorizontal ) ? scalingVertical : scalingHorizontal;
  
  // 全体を表示する
  canvasPosition(
    Math.floor( ( -centerX - g_artBoard_p.x ) * scaling ) + ( $canvasVisibleArea.width() / 2 ),
    Math.floor( ( -centerY - g_artBoard_p.y ) * scaling ) + ( $canvasVisibleArea.height() / 2 ),
    scaling, duration
  );

  // 確認用
  if ( initialValue.debug === true ) {
    window.console.log(  '----- View all -----' );
    window.console.log(  'x1 : ' + x1 + ' , y1 : ' + y1 + ' , x2 : ' + x2 + ' , y2 : ' + y2 );
    window.console.log(  'View Width : ' + viewWidth + ' , View Height : ' + viewHeight );
    window.console.log(  'Center X : ' + centerX + ' , Center Y : ' + centerY );
    window.console.log(  'Vertical Scaling : ' + scalingVertical + ' , Horizontal Scaling : ' + scalingHorizontal );
  }
  
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   マウス位置
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$canvasVisibleArea.add( $('#editor-panel') ).on({
  'mouseenter' : function(){ $( this ).addClass('hover'); },
  'mouseleave' : function(){ $( this ).removeClass('hover'); }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   SVGエリアの作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const xmlns = 'http://www.w3.org/2000/svg',
      $svgArea = $( document.createElementNS( xmlns, 'svg') ),
      $selectArea = $( document.createElementNS( xmlns, 'svg') ),
      $selectBox = $( document.createElementNS( xmlns, 'rect') );

const setSvgArea = function() {
  
    $svgArea.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );
    $selectArea.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );

    $artBoard.prepend( $svgArea, $selectArea.append( $selectBox ) );
    $svgArea.attr({
      'id' : 'svg-area',
      'width' : g_artBoard.w,
      'height' : g_artBoard.h
    });
    $selectArea.attr({
      'id' : 'select-area',
      'width' : g_artBoard.w,
      'height' : g_artBoard.h
    });

}
setSvgArea();

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）削除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const removeEdge = function( edgeID, removeSpeed ) {
  
  if ( removeSpeed === undefined ) removeSpeed = 200;
  
  const $edge = $('#' + edgeID ),
        edge = conductorData[ edgeID ];
  
  // 結線情報を削除
  if ( 'inTerminal' in edge ) {
    $('#' + edge.inTerminal ).removeClass('connected connect-a');
    delete conductorData[ edge.inNode ].terminal[ edge.inTerminal ].edge;
    delete conductorData[ edge.inNode ].terminal[ edge.inTerminal ].targetNode;
  }
  if ( 'outTerminal' in edge ) {
    $('#' + edge.outTerminal ).removeClass('connected connect-a');
    delete conductorData[ edge.outNode ].terminal[ edge.outTerminal ].edge;
    delete conductorData[ edge.outNode ].terminal[ edge.outTerminal ].targetNode;
  }
  delete conductorData[ edgeID ];
  
  if ( initialValue.debug === true ) {
    window.console.log( 'REMOVE EDGE ID : ' + edgeID );
  }
  
  editor.actionMode.set('editor-pause');
  $edge.animate({'opacity' : 0 }, removeSpeed, function(){
    $( this ).remove();
    editor.actionMode.clear();
  });
  
};

// 線をクリックで削除する（Editモードのみ）
$artBoard.on({
  'click' : function(){
    if ( !editor.actionMode.check('editor-pause') && conductorEditorMode === 'edit' ) {
      const edgeID = $( this ).closest('.svg-group').attr('id');
      conductorHistory.edgeRemove( edgeID );
      removeEdge( edgeID );
      updateConductorData();
    }
  } 
}, '.svg-select-line');


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const newSVG = function( svgID ) {
    // SVG ID
    if ( svgID === undefined ) {
      svgID = 'line-' + edgeCounter();
    }

    // グループを作成
    const $svgGroup = $( document.createElementNS( xmlns, 'g') );
    $svgGroup.attr({
      'id' : svgID,
      'class' : 'svg-group'
    });

    // パスを作成
    const $svgLine = $( document.createElementNS( xmlns, 'path') ),
          $svgLineInside = $( document.createElementNS( xmlns, 'path') ),
          $svgLineOutside = $( document.createElementNS( xmlns, 'path') ),
          $svgLineBack = $( document.createElementNS( xmlns, 'path') ),
          $svgSelectLine = $( document.createElementNS( xmlns, 'path') );
    $svgLine.attr('class', 'svg-line');
    $svgLineInside.attr('class', 'svg-line-inside');
    $svgLineOutside.attr('class', 'svg-line-outside');
    $svgLineBack.attr('class', 'svg-line-back');
    $svgSelectLine.attr('class', 'svg-select-line');

    // SVGエリアに追加
    $svgArea.append( $svgGroup.append( $svgLineBack, $svgLineOutside, $svgLineInside, $svgLine, $svgSelectLine ) );
      
    return $svgGroup;
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）位置更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const edgeUpdate = function( edgeID ) {

    const inNodeID = conductorData[ edgeID ].inNode,
          outNodeID = conductorData[ edgeID ].outNode;
                
    const inTerminal = conductorData[ inNodeID ].terminal[ conductorData[ edgeID ].inTerminal ],
          outTerminal = conductorData[ outNodeID ].terminal[ conductorData[ edgeID ].outTerminal ];
                
    const inX = Number( inTerminal.x ),
          inY = Number( inTerminal.y ),
          outX = Number( outTerminal.x ),
          outY = Number( outTerminal.y );
                      
    $('#' + edgeID ).find('path').attr('d', svgDrawPosition( outX, outY, inX, inY ) );
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）ホバー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// Editモードのみホバーでクラス付与
$canvasVisibleArea.on({
  'mouseenter' : function(){
    if ( conductorEditorMode === 'edit') {
      const $edge = $( this );
      if ( !editor.actionMode.check('node-move') || $edge.attr('data-interrupt') === 'true' ) {
        $edge.attr('class','svg-group hover');
      }
      if ( $edge.attr('data-interrupt') === 'true' ) {
        $editor.find('.node.current').css('opacity', .5 );
      }
    }
  },
  'mouseleave' : function(){
    if ( conductorEditorMode === 'edit') {
      $( this ).attr('class','svg-group');
      $editor.find('.node.current').css('opacity', 'inherit');
    }
  }
},'.svg-group');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線済みの線の更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const connectEdgeUpdate = function( nodeID ) {
    $('#' + nodeID ).find('.connected').each( function() {
      const terminalID = $( this ).attr('id');
      if ( 'edge' in conductorData[ nodeID ].terminal[ terminalID ] ) {
        const edgeID = conductorData[ nodeID ].terminal[ terminalID ].edge;
        edgeUpdate( edgeID );
      }
    });
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   SVG 座標
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// SVG命令文
const svgOrder = function( order, position ) {
    let pathData = [];
    for( let i = 0; i < position.length; i++ ){
      pathData.push( position[ i ].join(',') );
    }
    return order + pathData.join(' ');
};

// SVG座標調整
const svgDrawPosition = function( startX, startY, endX, endY ) {

    let drawPositionArray = [];
    
    // 中間点
    const centerX = Math.round( ( startX + endX ) / 2 ),
          centerY = Math.round( ( startY + endY ) / 2 );
            
    // 対象との距離
    const xRange = startX - endX,
          yRange = startY - endY;
    
    // 対象との絶対距離
    const xAbsoluteRange = Math.abs( xRange ),
          yAbsoluteRange = Math.abs( yRange );
    
    // Terminalからの直線距離
    let terminalStraightLineRange = 8;
    
    // 直線距離X座標
    const startStraightLineX = startX + terminalStraightLineRange,
          endStraightLineX = endX - terminalStraightLineRange;
    
    // SVG命令（共通）
    const moveStart = svgOrder('M', [[ startX, startY]] ),
          startLine = svgOrder('L', [[ startStraightLineX, startY]] ),
          moveEnd = svgOrder('L', [[ endX, endY]] );          
    
    if ( yAbsoluteRange > 32 && xRange > -96 ) {
      // Back Line
      let curvetoRangeX = Math.round( xAbsoluteRange / 3 ),
          curvetoStartY1 = Math.round( startY - yRange / 20 ),
          curvetoEndY1 = Math.round( endY + yRange / 20 ),
          curvetoStartY2 = Math.round( startY - yRange / 3 );
          
      if ( curvetoRangeX < 32 ) curvetoRangeX = 32;
      if ( curvetoRangeX > 128 ) curvetoRangeX = 128;
      if ( yAbsoluteRange < 128 && xRange > 0 ) {
        let adjustY = ( yRange > 0 ) ? yRange - 128: yRange + 128;
        curvetoStartY2 = curvetoStartY2 + Math.round( adjustY / 3 );
      }
      
      if ( xAbsoluteRange > 256 && yAbsoluteRange < 256 ) {
      // Straight S Line
      const curvetoStart = svgOrder('C', [[ startStraightLineX + 96, startY],[ startStraightLineX + 96, centerY ],[ startStraightLineX, centerY ]] ),
            centerLine = svgOrder('L',[[ endStraightLineX, centerY ]]),
            curvetoEnd = svgOrder('C', [[ endStraightLineX - 96, centerY ],[ endStraightLineX - 96, endY ],[ endStraightLineX, endY ]]);
      
      drawPositionArray = [ moveStart, startLine, curvetoStart, centerLine, curvetoEnd, moveEnd ];
      
      } else {
      // S Line
      const curvetoStartX = startStraightLineX + curvetoRangeX,
            curvetoStart = svgOrder('C', [[ curvetoStartX, curvetoStartY1],[ curvetoStartX, curvetoStartY2 ],[ centerX, centerY ]] ),
            curvetoEnd = svgOrder('S', [[ endStraightLineX - curvetoRangeX, curvetoEndY1 ],[ endStraightLineX, endY ]]);
      
      drawPositionArray = [ moveStart, startLine, curvetoStart, curvetoEnd, moveEnd ];
      }
      
    } else {
    
      if ( xRange > 0 ) {
        
        let curvetoRangeX = Math.round( xAbsoluteRange / 3 );
        if ( curvetoRangeX < 32 ) curvetoRangeX = 32;
        if ( curvetoRangeX > 128 ) curvetoRangeX = 128;
        // C Line
        const centerAdjust = Math.round( curvetoRangeX / 3 * 2 ),
              curvetoStartX = startStraightLineX + curvetoRangeX,
              curvetoStart = svgOrder('C', [[ curvetoStartX, startY],[ curvetoStartX, startY + centerAdjust ],[ centerX, centerY + centerAdjust ]] ),
              curvetoEnd = svgOrder('S', [[ endStraightLineX - curvetoRangeX, endY ],[ endStraightLineX, endY ]]);
                    
        drawPositionArray = [ moveStart, startLine, curvetoStart, curvetoEnd, moveEnd ];
      
      } else {

        let curvetoQX = startStraightLineX + Math.round( yAbsoluteRange / 3 );
        if ( curvetoQX > centerX ) curvetoQX = centerX;

        const curvetoQ = svgOrder('Q', [[ curvetoQX, startY]] ),
              endLine = svgOrder('T', [[ endStraightLineX, endY]] );

        drawPositionArray = [ moveStart, startLine, curvetoQ, centerX + ',' + centerY, endLine, moveEnd ];
      }
    }

    return drawPositionArray.join(' ');

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード位置とターミナルの座標確定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 位置情報登録
const nodeSet = function( $node, x, y ){

    const nodeID = $node.attr('id'),
          w = $node.width(),
          h = $node.height();
    
    // x と y が未定義なら位置情報を更新しない
    if ( x !== undefined && y !== undefined ) {
      
      // 念のため数値化
      x = Number( x );
      y = Number( y );
    
      // アートボードの中か？
      if ( x < 1 ) x = 0;
      if ( x + w > g_artBoard.w ) x = g_artBoard.w - w;
      if ( y < 1 ) y = 0;
      if ( y + h > g_artBoard.h ) y = g_artBoard.h - h;

      // 位置確定
      $node.css({
        'left' : x,
        'top' : y,
        'transform' : 'none'
      });

      conductorData[ nodeID ].x = x;
      conductorData[ nodeID ].y = y;
    
    }
    
    conductorData[ nodeID ].w = w;
    conductorData[ nodeID ].h = h;

    // ターミナルの位置情報更新
    let branchCount = 1;
    $node.find('.node-terminal').each( function() {
      const $terminal = $( this ),
            terminalID = $terminal.attr('id'),
            terminalWidth = $terminal.outerWidth() / 2,
            terminalHeight = $terminal.outerHeight() / 2;
      
      // 未定義なら初期化
      if ( conductorData[ nodeID ].terminal[ terminalID ] === undefined ) {
        conductorData[ nodeID ].terminal[ terminalID ] = {};
        if ( $terminal.is('.node-in') ) {
          conductorData[ nodeID ].terminal[ terminalID ].type = 'in';
        } else {
          conductorData[ nodeID ].terminal[ terminalID ].type = 'out';
        }
      }
      
      const nodeType = conductorData[ nodeID ].type,
            terminalType = conductorData[ nodeID ].terminal[ terminalID ].type;
      
      // 分岐ノードの情報をセット
      if (
        ( nodeType === 'conditional-branch' && terminalType === 'out' ) ||
        ( nodeType === 'parallel-branch' && terminalType === 'out' ) ||
        ( nodeType === 'status-file-branch' && terminalType === 'out' ) ||
        ( nodeType === 'merge' && terminalType === 'in' )
      ) {
        // 条件分岐Case情報をセット
        if ( nodeType === 'conditional-branch' ) {
          let branchArray = [];
          $terminal.prev('.node-body').find('li').each( function(){
            branchArray.push( $( this ).attr('data-end-status') );
          });
          conductorData[ nodeID ].terminal[ terminalID ].condition = branchArray;
        }
        if ( conductorData[ nodeID ].terminal[ terminalID ].case !== 'else') {
          conductorData[ nodeID ].terminal[ terminalID ].case = branchCount++;
        }
      }
      
      conductorData[ nodeID ].terminal[ terminalID ].id = terminalID;
      conductorData[ nodeID ].terminal[ terminalID ].x =
        Math.round( Number( conductorData[ nodeID ].x ) + $terminal.position().left / editorValue.scaling + terminalWidth );
      conductorData[ nodeID ].terminal[ terminalID ].y =
        Math.round( Number( conductorData[ nodeID ].y ) + $terminal.position().top / editorValue.scaling + terminalHeight );

    });
    
};

// ノードジェムのテキストが溢れているか確認し調整する
const nodeGemCheck = function( $node ) {
    const $gem = $node.find('.node-gem');
    if ( $gem.length ) {
        const gemWidth = $gem.width(),
              gemTextWidth = $gem.find('.node-gem-inner').width();
        if ( gemWidth < gemTextWidth ) {
            const scale = Math.floor( gemWidth / gemTextWidth * 1000 ) / 1000;
            $node.find('.node-gem-inner').css('transform','translateX(-50%) scale(' + scale + ')');
        }
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノードを指定位置に移動する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const nodeMoveSet = function( nodeID, x, y, position ) {
  
  if ( position === undefined ) position = 'absolute';
  if ( position === 'relative' ) {
    if ( Array.isArray( nodeID ) ) {
      const nodeIdLength = nodeID.length;
      let nodeEdgeArray = [];
      for ( let i = 0; i < nodeIdLength; i++ ) {
        const moveX = Number( conductorData[ nodeID[i] ].x ) + x,
              moveY = Number( conductorData[ nodeID[i] ].y ) + y;
        for ( let terminalID in conductorData[ nodeID[i] ]['terminal'] ) {
          const terminal = conductorData[ nodeID[i] ]['terminal'][ terminalID ];
          if ( 'edge' in terminal ) {
            const edgeID = conductorData[ terminal['edge'] ]['id'];
            if ( nodeEdgeArray.indexOf( edgeID ) === -1 ) {
              nodeEdgeArray.push( edgeID );
            }
          }
        }
        nodeSet( $('#' + nodeID[i] ), moveX, moveY );
      }
      const nodeEdgeLength = nodeEdgeArray.length;
      for ( let i = 0; i < nodeEdgeLength; i++ ) {
        edgeUpdate( nodeEdgeArray[i] );
      }      
    } else {
      const moveX = Number( conductorData[ nodeID ].x ) + x,
            moveY = Number( conductorData[ nodeID ].y ) + y;
      nodeSet( $('#' + nodeID ), moveX, moveY );
      connectEdgeUpdate( nodeID );
    }
  } else {
    nodeSet( $('#' + nodeID ), x, y );
    connectEdgeUpdate( nodeID );
  }

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// Counter
const nodeCounter = function() { return g_NodeCounter++; };
const terminalCounter = function() { return g_TerminalCounter++; };
const edgeCounter = function() { return g_EdgeCounter++; };

// Terminal HTMLを返す
const createTerminalHTML = function( terminalInOut, terminalID ) {

    let terminalHTML;

    if ( terminalID !== undefined ) {
      terminalHTML = ''
        + '<div id="' + terminalID + '" class="node-terminal node-' + terminalInOut + '">'
          + '<span class="connect-mark"></span>'
          + '<span class="hole"><span class="hole-inner"></span></span>'
        + '</div>';
    } else {
      // terminalIDの指定がない場合はCap
      terminalHTML = '<div class="node-cap node-' + terminalInOut + '"></div>'
    }

    return terminalHTML;

};

// In or Out のTerminal ID を返す。
const terminalInOutID = function( terminals, terminalInOut ) {
  let terminalIDList = [];
  for ( let key in terminals ) {
    if ( terminals[ key ].type === terminalInOut ) {
      terminalIDList.push( key );
    }
  }
  return terminalIDList;
}

const mergeStatusHTML = function() {
  let html = ''
    + '<div class="node-body">'
      + '<div class="merge-status" data-status="standby">'
        + '<ul class="merge-status-list">';
  for ( let statusID in mergeStatus ) {
    html += '<li class="merge-status-item merge-status-' + mergeStatus[ statusID ][ 0 ] +'">' + mergeStatus[ statusID ][ 0 ].toUpperCase() + '</li>';
  }
  html += '</ul></div></div>';
  return html;
}

const pauseStatusHTML = function() {
  let html = ''
    + '<div class="node-body">'
      + '<div class="pause-status" data-status="standby">'
        + '<ul class="pause-status-list">';
  for ( let statusID in pauseStatus ) {
    html += '<li class="pause-status-item pause-status-' + pauseStatus[ statusID ][ 0 ] +'">' + pauseStatus[ statusID ][ 1 ] + '</li>';
  }
  html += '</ul></div><div class="pause-resume"><button title="Resume" class="pause-resume-button" tabindex="-1" disabled></button></div></div>';
  return html;
}

// Node $( HTML )を返す
const createNodeHTML = function( nodeID ) {

    const nodeData = conductorData[ nodeID ];

    const nodeText = {
      'start' : ['S', 'Conductor', 'Start', 'conductor-start'],
      'end' : ['E', 'Conductor', 'End', 'conductor-end'],
      'pause' : ['', '', 'Pause', 'function function-pause'],
      'call' : ['Cc', 'Conductor call', 'Not selected', 'conductor-call'],
      'call_s' : ['Sc', 'Symphony call', 'Not selected', 'symphony-call'],
      'conditional-branch' : ['', '', '', 'function function-conditional'],
      'parallel-branch' : ['', '', '', 'function function-parallel'],
      'status-file-branch' : ['', '', '', 'function function-status-file'],
      'merge' : ['', '', '', 'function function-merge']
    };

    let nodeHTML = '',
        typeCheck = [],
        nodeClass = ['node'],
        attrData = [];

    if ( nodeData.type !== 'movement') {
      nodeClass.push( nodeText[ nodeData.type ][ 3 ] );
    }

    // Merge
    if ( nodeData.type === 'merge') {
      const terminalIDList = terminalInOutID( nodeData.terminal, 'in'),
            terminalLength = terminalIDList.length;
      nodeHTML += '<div class="node-merge">'
      for ( let i = 0; i < terminalLength; i++ ) {
        nodeHTML += '<div class="node-sub">' + createTerminalHTML('in', terminalIDList[ i ] );

        // Merge status
        nodeHTML += mergeStatusHTML();

        nodeHTML += '<div class="branch-cap branch-out"></div></div>';
      }
      nodeHTML += '</div>'
      + '<div class="branch-line"><svg></svg></div>';
    }

    // Node main
    nodeHTML += '<div class="node-main">';

    // Terminal in CAP
    typeCheck = ['start', 'merge'];
    if ( typeCheck.indexOf( nodeData.type ) !== -1 ) {
      nodeHTML += createTerminalHTML('in');
    } else {
      const terminalInID = terminalInOutID( nodeData.terminal, 'in');
      nodeHTML += createTerminalHTML('in', terminalInID[0] );
    }

    // Node body
    nodeHTML += '<div class="node-body">';

    let nodeCircle, nodeType, nodeName;
    if ( nodeData.type === 'movement') {
      nodeCircle = nodeData.PATTERN_ID;
      const movementData = listIdName('movement', nodeCircle );
      // IDからオーケストラと名前を設定する
      if ( movementData !== undefined ) {
        conductorData[ nodeID ]['ORCHESTRATOR_ID'] = movementData[1];
        conductorData[ nodeID ]['Name'] = movementData[0];     
      } else {
        // 見つからない場合
        conductorData[ nodeID ]['ORCHESTRATOR_ID'] = 0;
        conductorData[ nodeID ]['Name'] = 'Unknown';
      }
      nodeType = conductorUseList.orchestratorName[ conductorData[ nodeID ]['ORCHESTRATOR_ID'] ];
      if ( nodeType === undefined ) nodeType = 'Unknown';
      nodeName = conductorData[ nodeID ]['Name'];   
      nodeClass.push('node-' + nodeType.toLocaleLowerCase().replace(/\s/g, '-') );
    } else {
      nodeCircle = nodeText[ nodeData.type ][0];
      nodeType = nodeText[ nodeData.type ][1];
      nodeName = nodeText[ nodeData.type ][2];
    }
    
    if ( nodeData.type === 'end') {
      const endStatus = endType[conductorData[ nodeID ].END_TYPE][1],
            endID = endType[conductorData[ nodeID ].END_TYPE][0];
      if ( conductorData[ nodeID ].END_TYPE !== '5') {
        nodeName += ' : ' + endStatus;
      }
      attrData.push('data-end-status="' + endID + '"');
    }

    // Node circle & Node type
    typeCheck = ['start', 'end', 'movement', 'call', 'call_s'];
    if ( typeCheck.indexOf( nodeData.type ) !== -1 ) {
      nodeHTML += ''
      + '<div class="node-circle">'
        + '<span class="node-gem"><span class="node-gem-inner node-gem-length-' + nodeCircle.length + '">' + nodeCircle + '</span></span>'
        + '<span class="node-running"></span>'
        + '<span class="node-result" data-result-text="" data-href="#"></span>'
        + '<span class="node-end-status"><span class="node-end-status-inner"></span></span>'
      + '</div>'
      + '<div class="node-type"><span>' + nodeType + '</span></div>';
    }
    // Node name
    typeCheck = ['start', 'end', 'movement'];
    if ( typeCheck.indexOf( nodeData.type ) !== -1 ) {
      nodeHTML += '<div class="node-name"><span>' + nodeName + '</span></div>';
    }
    if ( nodeData.type === 'call' ) {
      if ( editor.checkValue( nodeData['CALL_CONDUCTOR_ID'] ) ) {
        nodeClass.push('call-select');
        nodeName = '[' + nodeData['CALL_CONDUCTOR_ID'] + ']:' + listIdName('conductor', nodeData['CALL_CONDUCTOR_ID'] );
        if ( conductorEditorMode === 'checking') nodeName = listIdName('conductor', nodeData['CALL_CONDUCTOR_ID'] );
      }
      nodeHTML += '<div class="node-name"><span class="select-conductor-name"><span class="select-conductor-name-inner">' + nodeName + '</span></span></span></div>';
    }
    if ( nodeData.type === 'call_s' ) {
      if ( editor.checkValue( nodeData['CALL_SYMPHONY_ID'] ) ) {
        nodeClass.push('call-select');
        nodeName = '[' + nodeData['CALL_SYMPHONY_ID'] + ']:' + listIdName('symphony', nodeData['CALL_SYMPHONY_ID'] );
        if ( conductorEditorMode === 'checking') nodeName = listIdName('symphony', nodeData['CALL_SYMPHONY_ID'] );
      }
      nodeHTML += '<div class="node-name"><span class="select-symphony-name"><span class="select-symphony-name-inner">' + nodeName + '</span></span></span></div>';
    }
    // Pause
    if ( nodeData.type === 'pause' ) {
      nodeHTML += pauseStatusHTML();
    }
    // Status file
    if ( nodeData.type === 'status-file-branch' ) {
      nodeHTML += '<div class="node-type"><span>Status file</span></div>'
        + '<div class="node-name">'
          + '<span class="status-file-result"><span class="status-file-result-inner"></span></span>'
        + '</div>';
    }
    
    // Node body END
    nodeHTML += '</div>';

    // Terminal out CAP
    typeCheck = ['end', 'parallel-branch', 'conditional-branch', 'status-file-branch'];
    if ( typeCheck.indexOf( nodeData.type ) !== -1 ) {
      nodeHTML += createTerminalHTML('out');
    } else {
      const terminalOutID = terminalInOutID( nodeData.terminal, 'out');
      nodeHTML += createTerminalHTML('out', terminalOutID[0] );
    }

    // Node main END
    nodeHTML += '</div>';

    // Branch
    typeCheck = ['parallel-branch', 'conditional-branch', 'status-file-branch'];
    if ( typeCheck.indexOf( nodeData.type ) !== -1 ) {
      nodeHTML += '<div class="branch-line"><svg></svg></div>'
      + '<div class="node-branch">';
      const terminalIDList = terminalInOutID( nodeData.terminal, 'out'),
            terminalLength = terminalIDList.length;
      let caseNumberHTML = {};
      for ( let i = 0; i < terminalLength; i++ ) {
        let conditionList = nodeData.terminal[ terminalIDList[ i ] ].condition,
            caseNumber = nodeData.terminal[ terminalIDList[ i ] ].case;
        
        if ( caseNumber === undefined ) caseNumber = 'undefined' + i;
        
        if ( conditionList !== undefined && Number( conditionList[0] ) === 9999 ) {
          caseNumberHTML[ caseNumber ] = '<div class="node-sub default">';
        } else if ( nodeData.terminal[ terminalIDList[ i ] ].case === 'else') {
          caseNumberHTML[ caseNumber ] = '<div class="node-sub default">';
        } else {
          caseNumberHTML[ caseNumber ] = '<div class="node-sub">';
        }
        caseNumberHTML[ caseNumber ] += '<div class="branch-cap branch-in"></div>';
        
        // ムーブメント結果条件
        if ( nodeData.type === 'conditional-branch' ) {
          const conditionLength = conditionList.length;
          caseNumberHTML[ caseNumber ] += '<div class="node-body">'
          + '<div class="branch-type"><ul>';
          for ( let j = 0; j < conditionLength; j++ ) {
            const conditionID = conditionList[ j ];
            let conditionClass = movementEndStatus[ conditionID ][0];
            caseNumberHTML[ caseNumber ] += '<li class="' + conditionClass + '" data-end-status="' + conditionID + '">' + movementEndStatus[ conditionID ][1] + '</li>';
          }
          caseNumberHTML[ caseNumber ] += '</ul></div>'
          + '</div>';
        }
        // ステータスファイル分岐
        if ( nodeData.type === 'status-file-branch' ) {
          if ( conditionList === undefined ) conditionList = [''];
          caseNumberHTML[ caseNumber ] += statuFileBranchBodyHTML( caseNumber, (caseNumber === 'else')? true: false, conditionList.join('') );
        }        
        caseNumberHTML[ caseNumber ] += createTerminalHTML('out', terminalIDList[ i ] ) + '</div>';
      }
      
      for ( const html in caseNumberHTML ) {
        nodeHTML += caseNumberHTML[html];
      }
      nodeHTML += '</div>';
    }

    // Note
    let noteText = nodeData['note'];
    if ( editor.checkValue( noteText ) ) {
      noteText = editor.textEntities( noteText );
      nodeHTML += '<div class="node-note note-open"><div class="node-note-inner"><p>' + noteText + '</p></div></div>';
    } else {
      nodeHTML += '<div class="node-note"><div class="node-note-inner"><p></p></div></div>';
    }

    // Skip, Status, Operation
    typeCheck = ['movement', 'call', 'call_s'];
    if ( typeCheck.indexOf( nodeData.type ) !== -1 ) {
      // Default skip
      let nodeCheckedType = '',
          skipFlag = false;
      // 個別Operation
      let nodeOperationData = '',
          selectOperationID = '',
          selectOperationName = '';
      // 作業確認の場合はステータス情報を参照する
      if ( conductorEditorMode === 'checking') {
        skipFlag = ( conductorUseList.conductorStatus['NODE_INFO'][ nodeID ]['SKIP'] === '2' ) ? true : false;
        selectOperationID = conductorUseList.conductorStatus['NODE_INFO'][ nodeID ]['OPERATION_ID'];
        selectOperationName = conductorUseList.conductorStatus['NODE_INFO'][ nodeID ]['OPERATION_NAME'];
      } else {
        skipFlag = ( Number( nodeData.SKIP_FLAG ) === 1 ) ? true : false;
        selectOperationID = nodeData['OPERATION_NO_IDBH'];
        selectOperationName = listIdName('operation', selectOperationID );
      }
      
      if ( skipFlag ) {
        nodeCheckedType = ' checked';
        nodeClass.push('skip');
      }
      if ( editor.checkValue( selectOperationID ) ) {
        nodeClass.push('operation');
        nodeOperationData = '[' + selectOperationID + ']:' + selectOperationName;
        if ( conductorEditorMode === 'checking') nodeOperationData = selectOperationName;
      }
      nodeHTML += ''
      + '<div class="node-skip"><input class="node-skip-checkbox" tabindex="-1" type="checkbox"' + nodeCheckedType + '><label class="node-skip-label">Skip</label></div>'
      + '<div class="node-operation">'
        + '<dl class="node-operation-body">'
          + '<dt class="node-operation-name">OP</dt>'
          + '<dd class="node-operation-data">' + nodeOperationData + '</dd>'
        + '</dl>'
        + '<div class="node-operation-border"></div>'
      + '</div>'
      + '<div class="node-status"><p></p></div>';
    }

    // Node wrap
    nodeHTML = '<div id="' + nodeID + '" class="' + nodeClass.join(' ') + '" ' + attrData.join(' ') + '>' + nodeHTML + '</div>';

    return $( nodeHTML );

}

// 新規ノードの初期値
const initialNode = function( nodeType, movementID ){

    const nodeID = 'node-' + nodeCounter();
    let typeCheck;
    conductorData[ nodeID ] = {
      'type' : nodeType,
      'id' : nodeID,
      'terminal' : {}
    }
    
    // Start, Merge 以外
    typeCheck = ['start', 'merge'];
    if ( typeCheck.indexOf( nodeType ) === -1 ) {
      const inTerminalID = 'terminal-' + terminalCounter();
      conductorData[ nodeID ]['terminal'][ inTerminalID ] = {
        'id' : inTerminalID,
        'type' : 'in'
      }
    }
    
    // Merge
    typeCheck = ['merge'];
    if ( typeCheck.indexOf( nodeType ) !== -1 ) {
      const inTerminalID1 = 'terminal-' + terminalCounter(),
            inTerminalID2 = 'terminal-' + terminalCounter();
      conductorData[ nodeID ]['terminal'][ inTerminalID1 ] = {
        'id' : inTerminalID1,
        'type' : 'in'
      }
      conductorData[ nodeID ]['terminal'][ inTerminalID2 ] = {
        'id' : inTerminalID2,
        'type' : 'in'
      }
    }
    
    // Branch
    typeCheck = ['parallel-branch', 'conditional-branch', 'status-file-branch'];
    if ( typeCheck.indexOf( nodeType ) !== -1 ) {
      const outTerminalID1 = 'terminal-' + terminalCounter(),
            outTerminalID2 = 'terminal-' + terminalCounter();
      conductorData[ nodeID ]['terminal'][ outTerminalID1 ] = {
        'id' : outTerminalID1,
        'type' : 'out'
      }
      conductorData[ nodeID ]['terminal'][ outTerminalID2 ] = {
        'id' : outTerminalID2,
        'type' : 'out'
      }
      if ( nodeType === 'conditional-branch') {
        conductorData[ nodeID ]['terminal'][ outTerminalID1 ]['condition'] = [ 9 ];
        conductorData[ nodeID ]['terminal'][ outTerminalID2 ]['condition'] = [ 9999 ];
      } else if ( nodeType === 'status-file-branch') {
        conductorData[ nodeID ]['terminal'][ outTerminalID1 ]['case'] = 1;
        conductorData[ nodeID ]['terminal'][ outTerminalID2 ]['case'] = 'else';
      }
    }
      
    typeCheck = ['end', 'parallel-branch', 'conditional-branch', 'status-file-branch'];
    if ( typeCheck.indexOf( nodeType ) === -1 ) {
      const outTerminalID = 'terminal-' + terminalCounter();
      conductorData[ nodeID ]['terminal'][ outTerminalID ] = {
        'id' : outTerminalID,
        'type' : 'out'
      }
    }
    
    if ( nodeType === 'movement' && movementID !== undefined ) {
      conductorData[ nodeID ]['PATTERN_ID'] = movementID;
      conductorData[ nodeID ]['SKIP_FLAG'] = 0;
      conductorData[ nodeID ]['OPERATION_NO_IDBH'] = null;      
    }
    
    if ( nodeType === 'call' ) {
      conductorData[ nodeID ]['SKIP_FLAG'] = 0;
      conductorData[ nodeID ]['CALL_CONDUCTOR_ID'] = null;
      conductorData[ nodeID ]['OPERATION_NO_IDBH'] = null;
    }
    
    if ( nodeType === 'call_s' ) {
      conductorData[ nodeID ]['SKIP_FLAG'] = 0;
      conductorData[ nodeID ]['CALL_SYMPHONY_ID'] = null;
      conductorData[ nodeID ]['OPERATION_NO_IDBH'] = null;
    }
    
    if ( nodeType === 'end' ) {
      conductorData[ nodeID ]['END_TYPE'] = '5';
    }

    return createNodeHTML( nodeID );
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   分岐線追加・更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const branchLine = function( nodeID, setMode ) {
  
  const branchType = conductorData[ nodeID ].type;

  const $branchNode = $('#' + nodeID ),
        $branchSVG = $branchNode.find('svg');
  
  // 一旦リセット
  $branchSVG.css('height', 8 ).attr('height', 8 ).empty();
  
  // サイズ決定
  const width = 40,
        height = $branchNode.height() + 2;
        
  $branchSVG.attr({
    'width' : width,
    'height' : height
  }).css({
    'width' : width,
    'height' : height
  }).get(0)
  .setAttribute('viewBox', '0 0 ' + width + ' ' + height );
  
  const terminalHeight = $branchNode.find('.node-main').height() - 16,
        terminalPosition = ( height - terminalHeight ) / 2,
        lineInterval = $branchNode.find('.node-sub').length + 1;

  $branchNode.find('.node-sub').each( function( index ){
  
    const $subNode = $( this ).find('.node-terminal'),
          terminalID = $subNode.attr('id'),
          $branchLine = $( document.createElementNS( xmlns, 'path') ),
          $branchInLine = $( document.createElementNS( xmlns, 'path') ),
          $branchOutLine = $( document.createElementNS( xmlns, 'path') ),
          $branchBackLine = $( document.createElementNS( xmlns, 'path') ),
          endY = terminalPosition + ( terminalHeight / lineInterval * ( index + 1 ) );
    
    let startY;
    if ( setMode === 'drop' ) {
      startY = $subNode.position().top + ( $subNode.height() / 2 ) + 1;
    } else {
      startY = Math.round( $subNode.position().top / editorValue.scaling ) + ( $subNode.height() / 2 ) + 1;
    }
    
    let order;
    
    // 追加
    $branchSVG.prepend( $branchBackLine );
    $branchSVG.append( $branchOutLine, $branchInLine, $branchLine );
    // class
    const terminalClass = terminalID + '-branch-line';
    $branchLine.attr('class','branch-line ' + terminalClass );
    $branchInLine.attr('class','branch-in-line ' + terminalClass );
    $branchOutLine.attr('class','branch-out-line ' + terminalClass );
    $branchBackLine.attr('class','branch-back-line ' + terminalClass );
    // 座標設定
    if ( branchType === 'merge' ) {
      order = svgOrder('M',[[0,startY]]) + svgOrder('C',[[30,startY],[width-30,endY],[width,endY]]);
    } else {
      order = svgOrder('M',[[width,startY]]) + svgOrder('C',[[width-30,startY],[30,endY],[0,endY]]);
    }
    
    $branchLine.attr('d', order );
    $branchInLine.attr('d', order );
    $branchOutLine.attr('d', order );
    $branchBackLine.attr('d', order );
  });
    
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード分岐追加・削除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const statuFileBranchBodyHTML = function( index, elseFlag, value ){
  if ( index === undefined ) index = 2;
  if ( value === undefined ) value = '';
  value = $('<div/>', {'text': value }).html();
  
  let html = '<div class="node-body">';
  if ( elseFlag === true ) {
    html += '<div class="branch-if-type branch-if-else">else</div>';
  } else {
    const ifText = ( Number( index ) === 1 )? 'if': 'else if';
    html += '<div class="branch-if-type">' + ifText + '</div>'
    + '<div class="branch-if-body"><span class="branch-if-value"><span class="branch-if-value-inner">' + value + '</span></span></div>';
  }
  html += '</div>';
  return html;
};
const addBranch = function( nodeID ) {
    const $branchNode = $('#' + nodeID );
    let branchType = '',
        nodeHTML = '<div class="node-sub">';
    
    if ( $branchNode.is('.function-conditional') ) {
      branchType = 'conditional';
      nodeHTML += ''
          + '<div class="branch-cap branch-in"></div>'
          + '<div class="node-body">'
            + '<div class="branch-type"><ul></ul></div>'
          + '</div>'
          + createTerminalHTML('out', 'terminal-' + g_TerminalCounter );
    } else if ( $branchNode.is('.function-parallel') ) {
      branchType = 'parallel';
      nodeHTML += ''
          + '<div class="branch-cap branch-in"></div>'
          + createTerminalHTML('out', 'terminal-' + g_TerminalCounter );
    } else if ( $branchNode.is('.function-status-file') ) {
      branchType = 'status-file';
      nodeHTML += ''
          + '<div class="branch-cap branch-in"></div>'
          + statuFileBranchBodyHTML()
          + createTerminalHTML('out', 'terminal-' + g_TerminalCounter );
    } else if ( $branchNode.is('.function-merge') ) {
      branchType = 'merge';
      nodeHTML += ''
          + createTerminalHTML('in', 'terminal-' + g_TerminalCounter )
          + mergeStatusHTML()
          + '<div class="merge-cap merge-out"></div>';
    }
    nodeHTML += '</div>';
    
    if ( branchType !== '' ) {
      // 条件分岐は最大6分岐までにする
      const branchLength = $branchNode.find('.node-sub').length;
      if ( !( branchType === 'conditional' && branchLength > 6 ) ) {
        g_TerminalCounter++;

        if ( branchType === 'conditional' || branchType === 'status-file' ) {
          $branchNode.find('.node-sub.default').before( nodeHTML );
        } else if ( branchType === 'parallel' ) {
          $branchNode.find('.node-branch').append( nodeHTML );
        } else {
          $branchNode.find('.node-' + branchType ).append( nodeHTML );
        }
        
        const beforeNodeData = $.extend( true, {}, conductorData[ nodeID ] );
        nodeSet( $branchNode );
        const afterNodeData = $.extend( true, {}, conductorData[ nodeID ] );
        conductorHistory.branch( beforeNodeData, afterNodeData );
        
        panelChange( nodeID );
        branchLine( nodeID );
        connectEdgeUpdate( nodeID );
      } else {
        message('0002');
      }
    }
};
const removeBranch = function( nodeID, terminalID ) {

    const $branchNode = $('#' + nodeID );
    let branchType = '';
    if ( $branchNode.is('.function-conditional') ) {
      branchType = 'conditional';
    } else if ( $branchNode.is('.function-parallel') ) {
      branchType = 'parallel';
    } else if ( $branchNode.is('.function-status-file') ) {
      branchType = 'status-file';
    } else if ( $branchNode.is('.function-merge') ) {
      branchType = 'merge';
    }
    
    if ( branchType !== '' ) {
      const branchNum = $branchNode.find('.node-sub').length,
            connectNum = $branchNode.find('.node-sub .connected').length;
      
      // 分岐は最低２つ
      if ( branchNum > 2 ) {
      
        // 未接続の分岐があるか？
        if ( branchNum !== connectNum ) {

          let $targetTerminal;
          // terminalIDが未定義なら最後の未接続の要素
          if ( terminalID === undefined ) {
            $targetTerminal = $branchNode.find('.node-terminal').not('.connected').closest('.node-sub').not('.default').eq(-1);
            if ( !$targetTerminal.length ) return false;
            terminalID = $targetTerminal.find('.node-terminal').attr('id');
          } else {
            $targetTerminal.find('#' + terminalID ).closest('.node-sub');
          }

          // 接続済みの場合は確認する
          /*
          if ( $targetTerminal.find('.node-terminal').is('.connected') ) {
            if ( confirm('This branch is already connected. Do you want to delete it?') ) {
              removeEdge( conductorData[ nodeID ].terminal[ terminalID ].edge );
            } else {
              return false;
            }
          }
          */

          const caseNum = conductorData[ nodeID ].terminal[ terminalID ].case,
                $deleteCase = $('#branch-case-list').find('tbody').find('tr').eq( caseNum - 1 );

          // 削除するケースに条件があるか？
          if ( $deleteCase.find('li').length ) {
            // 削除される条件をOtherに移動する
            $deleteCase.find('li').prependTo( $('#noset-conditions') );
          }
          $deleteCase.remove();

          delete conductorData[ nodeID ].terminal[ terminalID ];
          $targetTerminal.remove();
          
          branchLine( nodeID );
          const beforeNodeData = $.extend( true, {}, conductorData[ nodeID ] );
          nodeSet( $branchNode );
          const afterNodeData = $.extend( true, {}, conductorData[ nodeID ] );
          conductorHistory.branch( beforeNodeData, afterNodeData );
          panelChange( nodeID );
          connectEdgeUpdate( nodeID );

          // Status file branchで削除したのが最初のTerminalの場合名称を修正する
          if ( caseNum === 1 && branchType === 'status-file') {
            $branchNode.find('.branch-if-type').eq(0).text('if');
          }

        } else {
          message('0004');
        }
      
      } else {
        message('0003');
      }
      
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   リストからノード追加（ドラッグアンドドロップ）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
let nodeAddFlag = false;
$('.node-table').on('mousedown', 'tbody tr', function( e ){

  if ( e.button === 0 && nodeAddFlag === false && conductorEditorMode === 'edit') {
  
  // 選択を解除する
  getSelection().removeAllRanges();
  nodeAddFlag = true;
  
  // 選択したNodeからデータを取得
  const $nodeData = $( this ).find('.add-node');
  
  let addNodeType, addMovementID = '';
  
  if ( $nodeData.is('.function') ) {
    addNodeType = $nodeData.attr('data-function-type');
  } else {
    addNodeType = 'movement';
    addMovementID = $nodeData.attr('data-id');
  }
  
  // 分岐ノード一覧
  const branchNode = [
    'conditional-branch',
    'parallel-branch',
    'status-file-branch',
    'merge',
  ]; 
  
  // モード変更
  editor.actionMode.set('node-move');
  
  const $node = initialNode( addNodeType, addMovementID ),
        nodeID = $node.attr('id'),
        mouseDownPositionX = e.pageX,
        mouseDownPositionY = e.pageY;
  
  $editor.append( $node );
  
  // 要素の追加を待つ
  $node.ready( function(){
  
    nodeGemCheck( $node );  
    nodeInterruptCheck( nodeID );
    
    // 分岐ノードの線を描画
    if ( branchNode.indexOf( addNodeType ) !== -1 ) {
      branchLine( nodeID, 'drop');
    }
          
    let nodeDragTop = $node.height() / 2,
        nodeDragLeft = 72;
    
    $node.addClass('drag current').css({
      'left' : Math.round( e.pageX - $window.scrollLeft() - nodeDragLeft ),
      'top' : Math.round( e.pageY - $window.scrollTop() - nodeDragTop ),
      'transform-origin' : nodeDragLeft + 'px 50%'
    });
    $window.on({
      'mousemove.dragNode': function( e ){

        const moveX = Math.round( ( e.pageX - mouseDownPositionX ) ),
              moveY = Math.round( ( e.pageY - mouseDownPositionY ) );
        if ( $canvasVisibleArea.is('.hover') ) {
          $node.css('transform', 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(' + editorValue.scaling + ')');
        } else {
          $node.css('transform', 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(1)');
        }
      },
      'mouseup.dragNode': function( e ){
        $( this ).off('mousemove.dragNode mouseup.dragNode');
        
        editor.actionMode.clear();
        
        // Canvasの上にいるか
        if ( $canvasVisibleArea.is('.hover') ) {
          
          // Node を アートボードにセットする
          nodeDragTop = nodeDragTop * editorValue.scaling;
          nodeDragLeft = nodeDragLeft * editorValue.scaling;
          
          const artBordPsitionX = ( g_artBoard_p.x * editorValue.scaling ) + g_canvasVisibleArea.l + g_canvas_p.x,
                artBordPsitionY = ( g_artBoard_p.y * editorValue.scaling ) + g_canvasVisibleArea.t + g_canvas_p.y;
          let nodeX = Math.round( ( e.pageX - artBordPsitionX - nodeDragLeft ) / editorValue.scaling ),
              nodeY = Math.round( ( e.pageY - artBordPsitionY - nodeDragTop ) / editorValue.scaling );
          
          $node.appendTo( $artBoard ).removeClass('drag current').css('opacity', 'inherit');
          
          nodeSet( $node, nodeX, nodeY );
          
          nodeDeselect();
          nodeSelect( nodeID );
          panelChange( nodeID );
          
          // 線の上にいるかチェック
          const interruptFlag = nodeInterrupt( nodeID );
          
          conductorHistory.nodeSet( nodeID, interruptFlag );          
          updateConductorData();
          nodeAddFlag = false;
          
        } else {
          // キャンバス外の場合は消去
          g_NodeCounter -= 1;
          delete conductorData[ nodeID ];
          $node.animate({'opacity' : 0 }, 200, function(){
            $( this ).remove();
            nodeAddFlag = false;
          });
        }
        
        nodeInterruptCheckClear();
        
      }
    });
  });
  
  }
  
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   新規ノード追加
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// キャンバスの表示されている部分から位置を設定する
const visiblePosition = function( axis, position, width, height ) {

  const adjustPosition = 32; // 調整する端からの距離
  
  let positionNumber = 0;
  
  switch( position ) {
    case 'center':
      if ( axis === 'x' ) positionNumber = - g_canvas_p.x - g_artBoard_p.x + ( g_canvasVisibleArea.w / 2 ) - ( width / 2 );
      if ( axis === 'y' ) positionNumber = - g_canvas_p.y - g_artBoard_p.y + ( g_canvasVisibleArea.h / 2 ) - ( height / 2 );
      break;
    case 'top':
      if ( axis === 'y' ) positionNumber = - g_canvas_p.y - g_artBoard_p.y + adjustPosition;
      break;
    case 'bottom':
      if ( axis === 'y' ) positionNumber = - g_canvas_p.y - g_artBoard_p.y + g_canvasVisibleArea.h - height - adjustPosition;
      break;
    case 'left':
      if ( axis === 'x' ) positionNumber = - g_canvas_p.x - g_artBoard_p.x + adjustPosition;
      break;
    case 'right':
      if ( axis === 'x' ) positionNumber = - g_canvas_p.x - g_artBoard_p.x + g_canvasVisibleArea.w - width - adjustPosition;
      break;
  }

  return positionNumber;

}
// newNode function variables.
// x = Number or 'left,center,right'.
// y = Number or 'top,center,bottom'.
const newNode = function( type, x, y ){

  const $node = initialNode( type );
  
  // アートボードにNode追加
  $artBoard.append( $node );
  
  // 要素の追加を待つ
  $node.ready( function(){
    
    const width = $node.width(),
          height = $node.height();
    
    // x, yが数値以外の場合
    if ( x.typeof !== Number ) x = visiblePosition( 'x', x, width, height );
    if ( y.typeof !== Number ) y = visiblePosition( 'y', y, width, height );
    
    // 位置情報をセット
    nodeSet( $node, x, y );
    
  });

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ターミナルホバー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$canvasVisibleArea.on({
  'mouseenter' : function(){ $( this ).addClass('hover'); },
  'mouseleave' : function(){ $( this ).removeClass('hover'); }
},'.node-terminal');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線処理
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 登録済みの線を再描画する（読み込み時など）
const edgeDraw = function( edgeID ) {

  const $edge = newSVG( edgeID );
  $edge.attr('data-connected', 'connected');
  
  const outNodeID = conductorData[ edgeID ].outNode,
        outTerminalID = conductorData[ edgeID ].outTerminal,
        inNodeID = conductorData[ edgeID ].inNode,
        inTermianlID = conductorData[ edgeID ].inTerminal;
  
  $('#' + outTerminalID ).addClass('connected');
  $('#' + inTermianlID ).addClass('connected');

  const outX = Number( conductorData[ outNodeID ].terminal[ outTerminalID ].x ),
        outY = Number( conductorData[ outNodeID ].terminal[ outTerminalID ].y ),
        inX = Number( conductorData[ inNodeID ].terminal[ inTermianlID ].x ),
        inY = Number( conductorData[ inNodeID ].terminal[ inTermianlID ].y );
  
  $edge.find('path').attr('d', svgDrawPosition( outX, outY, inX, inY ) );
};

// 登録済みの線を再描画する（Undo/Redo）
const edgeConnect = function( edgeID ) {
  
  const outNodeID = conductorData[ edgeID ].outNode,
        outTerminalID = conductorData[ edgeID ].outTerminal,
        inNodeID = conductorData[ edgeID ].inNode,
        inTermianlID = conductorData[ edgeID ].inTerminal;

  // 接続状態を紐づけする
  conductorData[ outNodeID ]['terminal'][ outTerminalID ].targetNode = inNodeID;
  conductorData[ outNodeID ]['terminal'][ outTerminalID ].edge = edgeID;
  conductorData[ inNodeID ]['terminal'][ inTermianlID ].targetNode = outNodeID;
  conductorData[ inNodeID ]['terminal'][ inTermianlID ].edge = edgeID;
  
  edgeDraw( edgeID );
};

// connectEdgeID = 'new'で新規
const nodeConnect = function( connectEdgeID, outNodeID, outTerminalID, inNodeID, inTermianlID ) {

  let $edge, edgeID;

  if ( connectEdgeID === 'new'){
    $edge = newSVG();
    edgeID = $edge.attr('id');
  } else {
    $edge = $('#' + connectEdgeID );
    edgeID = connectEdgeID
  }
  $edge.attr('data-connected','connected');
  
  // 登録の無いEdgeの場合登録する
  if ( !( 'edge' in conductorData ) ) {
    conductorData[ edgeID ] = {
      'type' : 'edge',
      'id' : edgeID
    };
  }
  
  const $outTerminal =  $('#' + outTerminalID ),
        $inTerminal = $('#' + inTermianlID );
  $outTerminal.add( $inTerminal ).addClass('connected');
  
  
  // 接続状態を紐づけする
  conductorData[ outNodeID ]['terminal'][ outTerminalID ].targetNode = inNodeID;
  conductorData[ outNodeID ]['terminal'][ outTerminalID ].edge = edgeID;
  conductorData[ inNodeID ]['terminal'][ inTermianlID ].targetNode = outNodeID;
  conductorData[ inNodeID ]['terminal'][ inTermianlID ].edge = edgeID;
  
  // Edge
  conductorData[ edgeID ].outNode = outNodeID;
  conductorData[ edgeID ].outTerminal = outTerminalID;
  conductorData[ edgeID ].inNode = inNodeID;
  conductorData[ edgeID ].inTerminal = inTermianlID;

  // newの場合結線する
  if ( connectEdgeID === 'new'){
    const outX = Number( conductorData[ outNodeID ].terminal[ outTerminalID ].x ),
          outY = Number( conductorData[ outNodeID ].terminal[ outTerminalID ].y ),
          inX = Number( conductorData[ inNodeID ].terminal[ inTermianlID ].x ),
          inY = Number( conductorData[ inNodeID ].terminal[ inTermianlID ].y );
    $edge.find('path').attr('d', svgDrawPosition( outX, outY, inX, inY ) );
  }
  
  return edgeID;

};
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   接続できるターミナルチェック
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const edgeConnectCheck = function( currentNodeID, inOut ) {
    let conectCount = 0;
    for ( let nodeID in conductorData ) {
      if ( RegExp('^node-').test( nodeID ) && nodeID !== currentNodeID ) {
        
        let outNodeID, inNodeID, targetTerminal;
        
        if ( inOut === 'out-in') {
          outNodeID = currentNodeID;
          inNodeID = nodeID;
          targetTerminal = 'in';
        } else if( inOut === 'in-out') {
          outNodeID = nodeID;
          inNodeID = currentNodeID;
          targetTerminal = 'out';
        }
        
        // 接続可能チェック
        if ( checkConnectType( conductorData[ outNodeID ].type, conductorData[ inNodeID ].type ) ) {
          const terminals = terminalInOutID( conductorData[ nodeID ].terminal, targetTerminal ),
                terminalLength = terminals.length;
          for ( let i = 0; i < terminalLength; i++ ) {
            if ( !('targetNode' in conductorData[ nodeID ].terminal[ terminals[ i ] ] ) ) {
              $('#' + terminals[ i ] ).addClass('wait-connect');
              conectCount++;
            }
          } 
        }
        
      }
    }
    if ( conectCount === 0 ) message('0001');
}
const edgeConnectCheckClear = function() {
    $('.wait-connect').removeClass('wait-connect');
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線しているところに割り込む
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 割り込み出来る線かどうかチェックする
const nodeInterruptCheck = function( nodeID ) {
    
    // 割り込みしないノード
    const exclusionNode = ['start', 'end'];
    if ( exclusionNode.indexOf( conductorData[ nodeID].type ) !== -1 ) return false;
    
    // 複数選択されていたら終了
    if ( g_selectedNodeID.length > 1 ) return false; 
    
    // 1つでも接続済みであれば終了
    const $node = $('#' + nodeID );
    if ( $node.find('.connected').length ) return false;
    
    // 全ての線をチェック
    for ( let edgeID in conductorData ) {
      if ( RegExp('^line-').test( edgeID ) ) {
        const outNodeID = conductorData[ edgeID ].outNode,
              inNodeID = conductorData[ edgeID ].inNode;
        if (
          checkConnectType( conductorData[ outNodeID ].type, conductorData[ nodeID ].type ) &&
          checkConnectType( conductorData[ nodeID ].type, conductorData[ inNodeID ].type )
        ) {
          $('#' + edgeID ).attr('data-interrupt', 'true');
        }
      }
    }
};
const nodeInterruptCheckClear = function() {
    $('.svg-group').filter('[data-interrupt="true"]').removeAttr('data-interrupt');
}

// 割り込む
const nodeInterrupt = function( nodeID ) {
   
    const $hoverEdge = $('.svg-group[data-interrupt="true"].hover');
    if ( $hoverEdge.length ) {
            
      const hoverEdgeID = $hoverEdge.attr('id'),
            edgeData = conductorData[ hoverEdgeID ];
      
      let outTerminalID, inTerminalID;
      
      const outTerminals = terminalInOutID( conductorData[ nodeID ].terminal, 'out'),
            outTerminalLength = outTerminals.length,
            inTerminals = terminalInOutID( conductorData[ nodeID ].terminal, 'in'),
            inTerminalLength = inTerminals.length;

      // 分岐ノードはCase1に接続する
      const branchNodeCheck = ['parallel-branch', 'conditional-branch', 'status-file-branch'];
      if ( branchNodeCheck.indexOf( conductorData[ nodeID ].type ) !== -1 ) {
        for ( let i = 0; i < outTerminalLength; i++ ) {
          if ( Number( conductorData[ nodeID ]['terminal'][ outTerminals[ i ] ].case ) === 1 ) {
            outTerminalID = outTerminals[ i ];
            break;
          }
        }
      } else {
        outTerminalID = outTerminals[0];
      }
      if ( conductorData[ nodeID ].type === 'merge' ) {
        for ( let i = 0; i < inTerminalLength; i++ ) {
          if ( Number( conductorData[ nodeID ]['terminal'][ inTerminals[ i ] ].case ) === 1 ) {
            inTerminalID = inTerminals[ i ];
            break;
          }
        }
      } else {
        inTerminalID = inTerminals[0];
      }
      
      // conductorHistory用に削除するedgeをコピーしておく
      const removeEdgeData = $.extend( true, {}, conductorData[ hoverEdgeID ] );
      
      // Delete Edge
      removeEdge( hoverEdgeID, 0 );
      // target Out > current Node In
      const newEdge1 = nodeConnect('new', edgeData.outNode, edgeData.outTerminal, nodeID, inTerminalID );
      // current Node Out > target In
      const newEdge2 = nodeConnect('new', nodeID, outTerminalID, edgeData.inNode, edgeData.inTerminal );
      
      conductorHistory.interrupt( removeEdgeData, newEdge1, newEdge2 );
      
      // 割り込みしたら True
      return true;
    } else {
      return false;
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード選択・選択解除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const $nodeDelete = $('#node-delete-button');

const nodeSelect = function( nodeID ) {
    
    // nodeIDが未指定の場合すべての要素を選択
    if ( nodeID === undefined ) {
    
      for ( let key in conductorData ) {
        if ( RegExp('^node-').test( key ) ) {
          nodeSelect( key );
        }
      }
      panelChange();
    
    } else {
    
      const $node = $('#' + nodeID );
      $node.addClass('selected');

      // 選択中のノード一覧
      if ( g_selectedNodeID.indexOf( nodeID ) === -1 ) {
        g_selectedNodeID.push( nodeID );
      }

      if ( !( g_selectedNodeID[0] === 'node-1' && g_selectedNodeID.length === 1 ) ) {
        $nodeDelete.prop('disabled', false );
      }

      if ( initialValue.debug === true ) {
        window.console.log('----- Select node list -----' );
        window.console.log( g_selectedNodeID );
      }
    
    }
    
}
const nodeDeselect = function( nodeID ) {
    
    if ( nodeID === undefined ) {
      // nodeID が未指定の場合すべての選択を解除
      g_selectedNodeID = [];
      $('.node.selected').removeClass('selected');
    } else {
      // 指定IDの選択を解除
      const deselectNo = g_selectedNodeID.indexOf( nodeID );
      if ( deselectNo !== -1 ) {
        g_selectedNodeID.splice( deselectNo, 1 );
        $('#' + nodeID ).removeClass('selected');
      }
    }
    
    if ( !g_selectedNodeID.length || ( g_selectedNodeID[0] === 'node-1' && g_selectedNodeID.length === 1 ) ) {
      $nodeDelete.prop('disabled', true );
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード削除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const nodeRemove = function( nodeID ) {
    
    const nodeRemoveFunc = function( removeNodeID ) {
      // 接続している線があれば削除する
      if ( 'terminal' in  conductorData[ removeNodeID ] ) {
        const terminals = conductorData[ removeNodeID ].terminal;
        for ( let terminal in terminals ) {
          const terminalData = terminals[ terminal ];
          if ( 'edge' in terminalData ) {
            const edge = conductorData[ terminalData.edge ];
            removeEdge( edge.id, 0 );
          }
        }
      }
      // Start（id="node-1"）は削除しない
      if ( removeNodeID !== 'node-1' ) {
        // ノード削除
        $('#' + removeNodeID ).remove();
        delete conductorData[ removeNodeID ];
        panelChange();
      } else {
        // message('0006');
      }
    }
    
    // 配列かどうか判定
    if ( $.isArray( nodeID ) ) {
      const nodeLength = nodeID.length;
      if ( nodeLength ) {
        for ( let i = 0; i < nodeLength; i++ ) {
          nodeRemoveFunc( nodeID[ i ] );
        }
      }
    
    } else {
      nodeRemoveFunc( nodeID );
    }
    
    // 選択を解除する
    nodeDeselect();
    panelChange();
    
    updateConductorData();

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キーボード操作
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$window.on('keydown', function( e ) {
    
    // Edit時のみ
    if ( conductorEditorMode === 'edit') {
    
      // キャンバスの上にいるかどうか
      if ( $canvasVisibleArea.is('.hover') && editor.actionMode.check() ) {

        // 十字キー
        if ( e.keyCode >= 37 && e.keyCode <= 40 ) {
          if ( g_selectedNodeID.length ) {
            let x=0,y=0;
            switch( e.keyCode ) {
              case 37: x = -1; break;
              case 38: y = -1; break;
              case 39: x = 1; break;
              case 40: y = 1; break;
            }
            if ( e.shiftKey ) {
              x = x * 10;
              y = y * 10;
            }
            conductorHistory.move( g_selectedNodeID, x, y );
            nodeMoveSet( g_selectedNodeID, x, y, 'relative');
          }      
        }
        switch( e.keyCode ) {

          // Ctrl + A
          case 65:
            if ( e.ctrlKey ) {
              e.preventDefault();
              nodeSelect();
            }
            break;
          // Ctrl + Z
          case 90:
            if ( e.ctrlKey ) {
              e.preventDefault();
              conductorHistory.undo();
            }
            break;
          // Ctrl + Y
          case 89:
            if ( e.ctrlKey ) {
              e.preventDefault();
              conductorHistory.redo();
            }
            break;
          // Delete
          case 46:
            if ( g_selectedNodeID.length ) {
              conductorHistory.nodeRemove( g_selectedNodeID );
              nodeRemove( g_selectedNodeID );
            }
            break;
          // +  
          case 107:
            if ( g_selectedNodeID.length === 1 ) {
              addBranch( g_selectedNodeID[ 0 ] );
            }
            break;
          // -  
          case 109:
            if ( g_selectedNodeID.length === 1 ) {
              removeBranch( g_selectedNodeID[ 0 ] );
            }
            break;
          default:    
        }

      }
    
    }
    
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キャンバスマウスダウン処理（ノードの移動、結線、複数選択）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 接続可能チェック（接続できる＝True）
const checkConnectType = function( outType, inType ) {
  if ( outType in connectablePattern &&
       connectablePattern[ outType ].indexOf( inType ) !== -1 ) {    
    return false;
  } else {
    return true;
  }
};

$canvasVisibleArea.on('mousedown', function( e ){

    if ( e.buttons === 1 ) {
    
      // Viewモードは何もしない
      if ( conductorEditorMode === 'view') return false;
      
      // Skipチェックボックス
      if ( $( e.target ).closest('.node-skip').length && conductorEditorMode !== 'checking') {
        nodeDeselect();
        const $node = $( e.target ).closest('.node'),
              nodeID = $node.attr('id');
        nodeCheckStatus( nodeID );
        nodeSelect( nodeID );
        panelChange( nodeID );
        return false;
      }
      
      // Pauseボタン
      if ( $( e.target ).is('.pause-resume-button') ) {
        return false;
      }
    
      // 選択を解除しておく
      getSelection().removeAllRanges();
    
      const mouseDownPositionX = e.pageX,
            mouseDownPositionY = e.pageY,
            scrollFrame = Math.floor( 1000 / 60 );

      let moveX = 0, moveY = 0,
          scrollX = 0, scrollY = 0,
          scaleMoveX = 0, scaleMoveY = 0,
          scrollDirectionX = '', scrollDirectionY = '',
          moveScrollSpeedX = 0, moveScrollSpeedY = 0,
          minScrollSpeed = 4, maxScrollSpeed = 60,
          adjustMoveSpeed = 20,
          nodeMoveScrollTimer = false,
          timerMoveFlag = false,
          moveFlag = false;

      // 位置移動
      const move = function( callback ) {
        $( window ).on({
          'mousemove.nodeMove': function( e ){
            
            moveFlag = true;
            
            moveX = e.pageX - mouseDownPositionX;
            moveY = e.pageY - mouseDownPositionY;

            let positionX = e.pageX - g_canvasVisibleArea.l,
                positionY = e.pageY - g_canvasVisibleArea.t;

            // キャンバス外の向き
            // X over
            if ( positionX < 1 ) {
              moveScrollSpeedX = Math.round( -positionX / adjustMoveSpeed );
              scrollDirectionX = 'left';
            } else if ( positionX > g_canvasVisibleArea.w ) {
              moveScrollSpeedX = Math.round( ( positionX - g_canvasVisibleArea.w ) / adjustMoveSpeed ); 
              scrollDirectionX = 'right';
            } else {
              scrollDirectionX = '';
            }
            // Y over
            if ( positionY < 1 ) {
              moveScrollSpeedY = Math.round( -positionY / adjustMoveSpeed );
              scrollDirectionY = 'top';
            } else if ( positionY > g_canvasVisibleArea.h ) {
              moveScrollSpeedY = Math.round( ( positionY - g_canvasVisibleArea.h ) / adjustMoveSpeed ); 
              scrollDirectionY = 'bottom';
            } else {
              scrollDirectionY = '';
            }

            if ( timerMoveFlag === false ) {
              callback('mousemove');
            }
          },
          'mouseup.nodeMove': function(){
            $( this ).off('mousemove.nodeMove mouseup.nodeMove');
            $canvasVisibleArea.off('mouseenter.canvasScroll mouseleave.canvasScroll');

            callback('mouseup');

            clearInterval( nodeMoveScrollTimer );
            editor.actionMode.clear();
          }
        });
      };

      // キャンバススクロール
      const canvasScroll = function( callback ) {
        $canvasVisibleArea.on({
          'mouseenter.canvasScroll' : function(){
            timerMoveFlag = false;
            clearInterval( nodeMoveScrollTimer );
            nodeMoveScrollTimer = false;
          },
          'mouseleave.canvasScroll' : function(){
            if ( nodeMoveScrollTimer === false ) {
              nodeMoveScrollTimer = setInterval( function(){
                timerMoveFlag = true;

                if ( moveScrollSpeedX < minScrollSpeed ) moveScrollSpeedX = minScrollSpeed;
                if ( moveScrollSpeedY < minScrollSpeed ) moveScrollSpeedY = minScrollSpeed;
                if ( moveScrollSpeedX > maxScrollSpeed ) moveScrollSpeedX = maxScrollSpeed;
                if ( moveScrollSpeedY > maxScrollSpeed ) moveScrollSpeedY = maxScrollSpeed;

                // X scroll
                if ( scrollDirectionX === 'left' ) {
                  scrollX = scrollX - moveScrollSpeedX;
                  g_canvas_p.x = g_canvas_p.x + moveScrollSpeedX;
                } else if ( scrollDirectionX === 'right' ) {
                  scrollX = scrollX + moveScrollSpeedX;
                  g_canvas_p.x = g_canvas_p.x - moveScrollSpeedX;
                }
                // Y scroll
                if ( scrollDirectionY === 'top' ) {
                  scrollY = scrollY - moveScrollSpeedY;
                  g_canvas_p.y = g_canvas_p.y + moveScrollSpeedY;
                } else if ( scrollDirectionY === 'bottom' ) {
                  scrollY = scrollY + moveScrollSpeedY;
                  g_canvas_p.y = g_canvas_p.y - moveScrollSpeedY;
                }

                $canvas.css({
                  'left' : g_canvas_p.x,
                  'top' : g_canvas_p.y
                });

                callback('mousemove');

              }, scrollFrame );
            }
          }
        });
      };
      
      // 移動座標をセット
      const scaleMoveSet = function() {
        scaleMoveX = Math.round( ( moveX + scrollX ) / editorValue.scaling );
        scaleMoveY = Math.round( ( moveY + scrollY ) / editorValue.scaling );
      };
    
      // ノードの上でマウスダウン
      if ( $( e.target ).closest('.node').length ) {
        
        // Node移動、新規Edge 共通処理
        e.stopPropagation();

        const $node = $( e.target ).closest('.node'),
              nodeID = $node.attr('id');

        // マウスダウンした場所がTerminalなら新規Edge作成
        if ( $node.find('.node-terminal').is('.hover') && conductorEditorMode === 'edit') {

          const $terminal = $node.find('.node-terminal.hover'),
                terminalID = $terminal.attr('id'),
                $edge = newSVG(),
                edgeID = $edge.attr('id'),
                $path = $edge.find('path');

          // 接続済みなら何もしない
          if ( $terminal.is('.connected') ) return false;

          $node.addClass('current');
          $terminal.addClass('connect connect-a');

          let connectMode,
              start_p = {
                'x': Number( conductorData[ nodeID ].terminal[ terminalID ].x ),
                'y' : Number( conductorData[ nodeID ].terminal[ terminalID ].y )
              };
          
          if ( $terminal.is('.node-in') ) {
            connectMode = 'in-out';
          } else {
            connectMode = 'out-in';
          }
          
          editor.actionMode.set('edge-connect');
          
          // 接続可能な対象を調査
          edgeConnectCheck( nodeID, connectMode );
          
          const drawLine = function( event ) {

            scaleMoveSet();
            let end_p = {
              'x' : start_p.x + scaleMoveX,
              'y' : start_p.y + scaleMoveY
            }

            // 接続可能なターミナルの上かチェック
            const $targetTerminal = $('.node-terminal.wait-connect.hover');
            if ( $targetTerminal.length ) {
              
              nodeDeselect();
              panelChange();
              
              const targetTerminalID = $targetTerminal.attr('id'),
                    $targetNode = $targetTerminal.closest('.node'),
                    targetNodeID = $targetNode.attr('id');              
              
              // 中心にスナップ
              end_p.x = Number( conductorData[ targetNodeID ].terminal[ targetTerminalID ].x );
              end_p.y = Number( conductorData[ targetNodeID ].terminal[ targetTerminalID ].y );

              // コネクト処理
              if ( event === 'mouseup' ) {
                $node.removeClass('current');
                $terminal.removeClass('connect');
                $artBoard.find('.forbidden').removeClass('forbidden');

                // 接続状態を紐づけする
                if ( connectMode === 'out-in') {
                  nodeConnect( edgeID, nodeID, terminalID, targetNodeID, targetTerminalID );
                }  else if ( connectMode === 'in-out') {
                  nodeConnect( edgeID, targetNodeID, targetTerminalID, nodeID, terminalID );
                }
                $('#' + targetTerminalID ).addClass('connect-a');

                // ループするか条件分岐がマージしていないかチェックする
                if (
                  ( connectMode === 'out-in' && !nodeLoopCheck( nodeID ) ) ||
                  ( connectMode === 'in-out' && !nodeLoopCheck( targetNodeID) ) ||
                  ( !nodeConditionalToMergeCheck() )
                ) {
                  removeEdge( edgeID );
                } else {
                  // 接続確定
                  conductorHistory.connect( edgeID );
                  updateConductorData();
                }
                edgeConnectCheckClear();
              }

            } else if ( event === 'mouseup' ) {
              edgeConnectCheckClear();
              $node.removeClass('current');
              $terminal.removeClass('connect connected connect-a');
              g_EdgeCounter--;
              $edge.animate({'opacity' : 0 }, 200, function(){
                $( this ).remove();
              });
            }
            
            // 線を更新
            if ( connectMode === 'out-in') {
              $path.attr('d', svgDrawPosition( start_p.x, start_p.y, end_p.x, end_p.y ) );
            } else if ( connectMode === 'in-out') {
              $path.attr('d', svgDrawPosition( end_p.x, end_p.y, start_p.x, start_p.y ) );
            }

          };
          move( drawLine );
          canvasScroll( drawLine );

          $path.attr('d', svgDrawPosition( start_p.x, start_p.y, start_p.x, start_p.y ) );

        } else if( conductorEditorMode === 'edit') {

          // Nodeの移動
          $node.addClass('current');
          
          // 選択状態かどうか
          if ( !$node.is('.selected') ) { 
            // Shiftキーが押されていれば選択を解除しない
            if ( !e.shiftKey ) {
              nodeDeselect();
              panelChange();
            }
            // ノード Selected
            nodeSelect( nodeID );
          } else {
             // 選択状態かつShiftキーが押されていれば選択を解除し終了
            if ( e.shiftKey ) {
              nodeDeselect( nodeID );
              panelChange();
              return false;
            }
          }
          
          // 選択しているノードの数
          const selectNodeLength = g_selectedNodeID.length;
          
          // 選択しているノードから移動する線をリスト化する
          const selectNodeMoveLineArray = [];
          for ( let i = 0; i < selectNodeLength; i++ ) {
            const selectNodeID = g_selectedNodeID[ i ];
            // ターミナル数ループ
            for ( let terminalID in conductorData[ selectNodeID ].terminal ) {
              const terminal = conductorData[ selectNodeID ].terminal[ terminalID ];
              if ( 'edge' in terminal ) {
                const edgeID = conductorData[ terminal.edge ].id;
                if ( selectNodeMoveLineArray.indexOf( edgeID ) === -1 ) {
                  selectNodeMoveLineArray.push( edgeID );
                }
              }
            }            
          }
          const selectNodeLineLength = selectNodeMoveLineArray.length;
          
          editor.actionMode.set('node-move');
          nodeInterruptCheck( nodeID );
          
          // パネル変更
          panelChange( nodeID );
          
          // ノード移動処理
          const moveNode = function( event ){

            if ( event === 'mousemove') {
            
              scaleMoveSet();
              
              // 選択ノードをすべて仮移動
              $canvasVisibleArea.find('.node.selected')
                .css('transform', 'translate3d(' + scaleMoveX + 'px,' + scaleMoveY + 'px,0)');
              
              // 選択ノードに接続している線をすべて移動
              for ( let i = 0; i < selectNodeLineLength; i++ ) {
                const moveLineID = selectNodeMoveLineArray[ i ],
                      inNodeID = conductorData[ moveLineID ].inNode,
                      outNodeID = conductorData[ moveLineID ].outNode;
                
                const inTerminal = conductorData[ inNodeID ].terminal[ conductorData[ moveLineID ].inTerminal ],
                      outTerminal = conductorData[ outNodeID ].terminal[ conductorData[ moveLineID ].outTerminal ];
                
                let inX = Number( inTerminal.x ),
                    inY = Number( inTerminal.y ),
                    outX = Number( outTerminal.x ),
                    outY = Number( outTerminal.y );
                
                // 選択中のノードなら移動させる
                if ( g_selectedNodeID.indexOf( inNodeID ) !== -1 ) {
                  inX += scaleMoveX;
                  inY += scaleMoveY;
                }
                if ( g_selectedNodeID.indexOf( outNodeID ) !== -1 ) {
                  outX += scaleMoveX;
                  outY += scaleMoveY;
                }
                      
                $('#' + selectNodeMoveLineArray[ i ] ).find('path')
                  .attr('d', svgDrawPosition( outX, outY, inX, inY ) );
              }

            } else if ( event === 'mouseup') {
              $node.removeClass('current').css('opacity', 'inherit');
              
              // 移動しているか？
              if ( moveFlag === true ) {
                // 選択ノード全ての位置確定
                const nodeSetFunc = function( setNodeID ) {
                  const beforeX = Number( conductorData[ setNodeID ].x ),
                        beforeY = Number( conductorData[ setNodeID ].y );
                  nodeSet( $('#' + setNodeID ), scaleMoveX + beforeX, scaleMoveY + beforeY );
                }
                for ( let i = 0; i < selectNodeLength; i++ ) {
                  nodeSetFunc( g_selectedNodeID[ i ] );
                }

                // 割り込み判定
                const interruptFlag = nodeInterrupt( nodeID );
                
                conductorHistory.move( g_selectedNodeID, scaleMoveX, scaleMoveY, interruptFlag );
                updateConductorData();
              }
              
              nodeInterruptCheckClear();
            }

          };

          move( moveNode );
          canvasScroll( moveNode );

        } else {
          // Editモード以外は選択するのみ
          if ( !$node.is('.selected') ) { 
            nodeDeselect();
            nodeSelect( nodeID );
            panelChange( nodeID );
          }
        }


      // 線の上
      } else if ( $( e.target ).is('.svg-select-line') ) {
      
        nodeDeselect();
        panelChange();
      
      // その他
      } else {
        
        // 全ての選択を解除
        if ( !e.shiftKey ) nodeDeselect();
        panelChange();
        
        // Editモードなら範囲選択
        if ( conductorEditorMode === 'edit') {
        
          editor.actionMode.set('node-select');

          const positionNow = {
            'x' : function ( x ) {
              x = Math.round( ( x - ( g_artBoard_p.x * editorValue.scaling ) - g_canvasVisibleArea.l - g_canvas_p.x ) / editorValue.scaling );
              return x;
            },
            'y' : function ( y ) {
              y = Math.round( ( y - ( g_artBoard_p.y * editorValue.scaling ) - g_canvasVisibleArea.t - g_canvas_p.y ) / editorValue.scaling )
              return y;
            }
          };
          const rectX = positionNow.x( mouseDownPositionX ) - 0.5,
                rectY = positionNow.y( mouseDownPositionY ) - 0.5;

          let x,y,w,h;

          const rectDraw = function( event ) {

            if ( event === 'mousemove') {

              scaleMoveSet();

              if ( scaleMoveX < 0 ) {
                x = rectX + scaleMoveX;
                w = -scaleMoveX;
              } else {
                x = rectX;
                w = scaleMoveX;
              }
              if ( scaleMoveY < 0 ) {
                y = rectY + scaleMoveY;
                h = -scaleMoveY;
              } else {
                y = rectY;
                h = scaleMoveY;
              }
              $selectArea.find('rect').attr({
                'x' : x, 'y' : y, 'width' : w, 'height' : h
              });
            } else if ( event === 'mouseup') {
              $selectArea.find('rect').attr({
                'x' : 0, 'y' : 0, 'width' : 0, 'height' : 0
              });
              editor.actionMode.clear();
              // 選択範囲内のノードを選択
              const rect = {
                'left' : x,
                'top' : y,
                'right' : x + w,
                'bottom' : y + h
              };
              for ( let nodeID in conductorData ) {
                if ( 'type' in conductorData[ nodeID ] ) {
                  if ( conductorData[ nodeID ].type !== 'edge' ) {
                    const node = {
                      'left' : Number( conductorData[ nodeID ].x ),
                      'top' : Number( conductorData[ nodeID ].y ),
                      'right' : Number( conductorData[ nodeID ].x ) + Number( conductorData[ nodeID ].w ),
                      'bottom' : Number( conductorData[ nodeID ].y ) + Number( conductorData[ nodeID ].h )
                    };
                    // 判定
                    if ( ( node.top < rect.bottom && rect.top < node.bottom ) &&
                         ( node.left < rect.right && rect.left < node.right ) ) {
                      // 選択状態を反転
                      if ( g_selectedNodeID.indexOf( nodeID ) === -1 ) {
                        nodeSelect( nodeID );
                      } else {
                        nodeDeselect( nodeID );
                      }
                      if ( g_selectedNodeID.length === 1 ) {
                        panelChange( nodeID );
                      } else {
                        panelChange();
                      }
                    }
                  }
                }
              } 
            }
          };
          move( rectDraw );
          canvasScroll( rectDraw );
        }
 
      }
    
    }

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線時、条件分岐がマージされてないかチェックする
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 調査開始ノードID一覧を返す
const conditionalBranchID = function() {
  let conditionalBranchIdList = [];
  for ( let nodeID in conductorData ) {
    if ( conductorData[ nodeID ].type === 'conditional-branch' || conductorData[ nodeID ].type === 'start') {
        conditionalBranchIdList.push( nodeID );
    }
  }
  
  // 条件分岐以外の開始ノードを条件分岐をさかのぼって調べる
  const startNodeCheck = function( nodeID ) {
    if ( conductorData[ nodeID ].type === "merge" ) return false;
    const terminals = terminalInOutID( conductorData[ nodeID ]['terminal'], 'in'),
          terminalLength = terminals.length;
    for ( let i = 0; i < terminalLength; i++ ) {
      const terminal = conductorData[ nodeID ]['terminal'][ terminals[i] ];
      if ('targetNode' in terminal ) {
        startNodeCheck( terminal.targetNode );
      } else {
        if ( conditionalBranchIdList.indexOf( nodeID ) === -1 ) {
          conditionalBranchIdList.push( nodeID );
        }
      }
    }
  };
  const conditionalBranchength = conditionalBranchIdList.length;
  for ( let i = 0; i < conditionalBranchength; i++ ) {
    startNodeCheck( conditionalBranchIdList[ i ] );
  }
  
  return conditionalBranchIdList;
}
const nodeConditionalToMergeCheck = function() {
  let nodeConditionalToMergeFlag = true,
      mergeCheckArray = {};
  const conditionalBranches = conditionalBranchID(),
        conditionalBranchLenght = conditionalBranches.length;
  
  const nodeConditionalToMergeRecursion = function( nodeID, conditionalID, parallelBranchFlag ) {
    const terminals = terminalInOutID( conductorData[ nodeID ]['terminal'], 'out'),
          terminalLength = terminals.length;
    if ( conductorData[ nodeID ].type === 'parallel-branch') parallelBranchFlag = true;
    if ( conductorData[ nodeID ].type === 'merge') {
      if ( parallelBranchFlag === true ) {
        if ( nodeID in mergeCheckArray ) {
          if ( mergeCheckArray[ nodeID ] !== conditionalID ) {
            message('0007');
            nodeConditionalToMergeFlag = false;
          }
        } else {
          mergeCheckArray[ nodeID ] = conditionalID;
        }
      } else {
        message('0007');
        nodeConditionalToMergeFlag = false;
      }
    }
    
    if ( nodeConditionalToMergeFlag !== false ) {
    for ( let i = 0; i < terminalLength; i++ ) {
      const terminal = conductorData[ nodeID ]['terminal'][ terminals[i] ];
      if ('targetNode' in terminal ) {
        const targetNodeID = terminal.targetNode,
              targetNodeType = conductorData[ targetNodeID ].type;
        if ( targetNodeType !== 'conditional-branch') {
          if ( conductorData[ nodeID ].type === 'conditional-branch') {
            nodeConditionalToMergeRecursion( targetNodeID, terminals[i], false );
          } else {
            nodeConditionalToMergeRecursion( targetNodeID, conditionalID, parallelBranchFlag );
          }
        }
      }
    }
    }
  };
  
  for ( let i = 0; i < conditionalBranchLenght; i++ ) {
    nodeConditionalToMergeRecursion( conditionalBranches[ i ] );
  }
  
  return nodeConditionalToMergeFlag;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線時ノードループ調査
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const nodeLoopCheck = function( nodeID ) {
  
  let flag = true,
      nodeArray = [],
      mergeArray = [];
  
  // 重複にカウントしないノード
  const noCountNode = [
    'conditional-branch',
    'parallel-branch',
    'status-file-branch',
    'merge'
  ];
  
  if ( initialValue.debug === true ) window.console.log('----- Route check -----');
  
  const nodeLoopCheckRecursion = function( next ) {
    if ( flag === true ) {
      const node = conductorData[ next ];

      // 経路ログ
      if ( initialValue.debug === true ) {
        window.console.log( 'ID:' + next + ' / ' + node.type );
      }

      if ( noCountNode.indexOf( node.type ) === -1 ) {
        nodeArray.push( next );
      }
      
      // ターミナルの数だけループ
      for ( let terminals in node.terminal ) {
        if ( node.terminal[ terminals ].type === 'out' && 'targetNode' in node.terminal[ terminals ] ) {
          next = node.terminal[ terminals ].targetNode;
          // すでに通過したマージの先はチェックしない
          if ( mergeArray.indexOf( next ) === -1 ) {
            // 同じIDが見つかれば終了
            if ( nodeArray.indexOf( next ) !== -1 ) {
              flag = false;
              message('0005');
              return false;
            } else if ( next !== undefined ) {
              // 次があれば再帰
              nodeLoopCheckRecursion( next );
            }
          }
        }
      }
      
      if ( 'merge' === node.type ) {
        mergeArray.push( next );
      }
    }
  };
  nodeLoopCheckRecursion( nodeID );
  return flag;  
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   パネル関連
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const $conductorParameter = $('#conductor-parameter'),
      $branchCaseList = $('#branch-case-list').find('tbody'),
      $statusFileCaseList = $('#status-file-case-list').find('tbody');

$conductorParameter.find('.editor-tab-menu-item').not('[data-tab="conductor"]').hide();

// End node : 終了時のステータスを選択
const endStatusSlect = function(){
  const $end = $('#end').find('.end-status-select');
  
  // Radio選択HTML
  let html = '<ul class="end-status-select-list">';
  const order = [ 5, 11, 7 ],
        orderLength = order.length;
  for ( let i = 0; i < orderLength; i++ ) {
    const id = 'end-status-' + endType[order[i]][0]
    html += ''
    + '<li class="end-status-select-item">'
      + '<input class="end-status-select-radio" type="radio" name="end-status" id="' + id + '" value="' + order[i] + '">'
      + '<label class="end-status-select-label" for="' + id + '">' + endType[order[i]][1] + '</label>'
    + '</li>';
  }
  $end.html( html );
  
  // 選択イベント
  $end.find('.end-status-select-radio').on('change', function(){
    // 選択されているノードが一つかどうか
    if ( g_selectedNodeID.length <= 1 ) {
      const nodeID = g_selectedNodeID[0],
            $node = $('#' + nodeID ),
            val = $( this ).val(),
            nodeName = ( val === '5')? 'End': 'End : ' + endType[val][1];
      $node.attr('data-end-status', endType[val][0] )
        .find('.node-name > span').text( nodeName );
      conductorData[nodeID].END_TYPE = $( this ).val();
      
      // サイズを更新
      nodeSet( $node );
    }
  });
}
endStatusSlect();

// パネルをnodeIDのものに変更する
const panelChange = function( nodeID ) {

  // 複数選択されている場合はパネルを表示しない
  if ( g_selectedNodeID.length <= 1 ) {
    
    let panelType = '';
    
    // nodeIDが未定義の場合はシンフォニーパネルを表示
    if ( nodeID === undefined ) {
      
      nodeID = 'conductor';
      panelType = 'conductor';
      
    } else if ( conductorEditorMode === 'checking') {
      // 作業確認の場合はすべてNodeとする
      panelType = 'node';
    } else {
      
      const nodeType = conductorData[ nodeID ].type;
      
      // 対応したパネルを表示
      switch( nodeType ) {
        case 'movement':
        case 'conditional-branch':
        case 'parallel-branch':
        case 'status-file-branch':
        case 'merge':
        case 'call':
        case 'call_s':
        case 'end':
          panelType = nodeType;
          break;
        case 'start':
        case 'blank':
        case 'pause':
          panelType = 'function';
          break;
        default:
          panelType = 'conductor';
      }
    }
    const $panel = $('#' + panelType );
    $conductorParameter.find('.editor-tab').find('li[data-tab="' + panelType + '"]').show().click()
      .siblings().hide();
    
    // Noteのチェック
    const $noteTextArea = $panel.find('.panel-note');
    const setNodeNote = function( text ) {
      if ( conductorEditorMode === 'edit' ) {
        $noteTextArea.val( text );
      } else {
        $noteTextArea.text( text );
      }
    }
    if ( 'note' in conductorData[ nodeID ] ) {
      let noteText = conductorData[ nodeID ].note;
      if ( !editor.checkValue( noteText ) ) {
        noteText = '';
      }
      setNodeNote( noteText );
    } else {
      setNodeNote('')
    }
    
    // 個別Operation表示
    const panelOperation = function( target ) {
      let operation = conductorData[ nodeID ].OPERATION_NO_IDBH;
      if ( editor.checkValue( operation ) ) {
        const operationName = listIdName('operation',operation );
        operation = '[' + operation + ']:' + operationName;
      } else {
        operation = '';
      }
      $( target ).text( operation );
    }
    
    // パネルごとの処理
    switch( panelType ) {
      case 'movement': {
          $('#movement-id').text( conductorData[ nodeID ].PATTERN_ID );
          $('#movement-orchestrator').text( conductorUseList.orchestratorName[ conductorData[ nodeID ].ORCHESTRATOR_ID ] );
          $('#movement-name').text( conductorData[ nodeID ].Name );
          panelOperation('#movement-operation');          
          // Skip
          const nodeChecked = ( Number( conductorData[ nodeID ].SKIP_FLAG ) === 1 ) ? true : false;
          $('#movement-default-skip').prop('checked', nodeChecked );
        }
        break;
      case 'call': {
          let callConductor = conductorData[ nodeID ].CALL_CONDUCTOR_ID;
          if ( editor.checkValue( callConductor ) ) {
            const conductorName = listIdName('conductor',callConductor );
            callConductor = '[' + callConductor + ']:' + conductorName;
          } else {
            callConductor = '';
          }
          $('#conductor-call-name').text( callConductor );
          panelOperation('#conductor-call-operation');
          // Skip
          const nodeChecked = ( Number( conductorData[ nodeID ].SKIP_FLAG ) === 1 ) ? true : false;
          $('#conductor-call-default-skip').prop('checked', nodeChecked );
        }
        break;
      case 'call_s': {
          let callSymphony = conductorData[ nodeID ].CALL_SYMPHONY_ID;
          if ( editor.checkValue( callSymphony ) ) {
            const symphonyName = listIdName('symphony',callSymphony );
            callSymphony = '[' + callSymphony + ']:' + symphonyName;
          } else {
            callSymphony = '';
          }
          $('#symphony-call-name').text( callSymphony );
          panelOperation('#symphony-call-operation');
          // Skip
          const nodeChecked = ( Number( conductorData[ nodeID ].SKIP_FLAG ) === 1 ) ? true : false;
          $('#symphony-call-default-skip').prop('checked', nodeChecked );
        }
        break;
      case 'end':
        // End status
        if ( conductorEditorMode === 'execute' ) {
          $('#end-status').text(endType[conductorData[ nodeID ].END_TYPE][1]);
        } else {
          $('#end').find('.end-status-select-radio').val([conductorData[ nodeID ].END_TYPE]);
        }
        break;
      case 'function':
        $('#function-type').text( conductorData[ nodeID ].type );
        break;
      case 'conditional-branch': {
        
        // 分岐の数だけボックスを用意する
        const keys = Object.keys( conductorData[ nodeID ].terminal ).length - 2;

        let listHTML = '';
        for (let i = 0; i < keys; i++ ) {
          listHTML += '<tr><th class="panel-th">Case ' + ( i + 1 ) + ' :</th><td class="panel-td"><ul class="branch-case"></ul></td></tr>';
        }
        $branchCaseList.html( listHTML );
        
        // 分岐をパネルに反映する
        let terminalConditionArray = []; // 使用中の分岐
        for( let terminalID in conductorData[ nodeID ].terminal ) {
          const terminalData = conductorData[ nodeID ].terminal[ terminalID ];
          if ( terminalData.type === 'out') {
            const conditionLength = terminalData.condition.length;
            let conditionHTML = '';
            for ( let i = 0; i < conditionLength; i++ ) {
              const key = terminalData.condition[ i ];
              terminalConditionArray.push( key );
              conditionHTML += conditionBlockHTML( key );
            }
            $branchCaseList.find('.branch-case').eq( terminalData.case - 1 ).html( conditionHTML );
          }
        }
        // 未使用分岐をセット
        let nosetConditionHTML = '';
        for ( let key in movementEndStatus ){
          if ( terminalConditionArray.indexOf( key ) === -1 ) {
            nosetConditionHTML += conditionBlockHTML( key );
          }
        }
        $('#noset-conditions').html( nosetConditionHTML );
        break;
      }
      case 'status-file-branch': {
        const terminals = conductorData[ nodeID ].terminal,
              outTerminals = Object.keys( terminals ).map(function(k){
                return terminals[k];
              }).filter(function(v){
                if ( v.case !== undefined && v.case !== 'else') {
                  return true;
                }
              }).sort(function(a,b){
                if ( a.case > b.case ) {
                  return 1;
                } else if ( a.case < b.case ) {
                  return -1;
                } else {
                  return 0;
                }
              });
              
        const terminalLength = outTerminals.length;
        let listHTML = '';
        for (let i = 0; i < terminalLength; i++ ) {
          const ifName = ( i === 0 )? 'if':'else if',
                value = ( outTerminals[i].condition !== undefined )? outTerminals[i].condition.join(' '): '';
          listHTML += ''
          + '<tr>'
            + '<th class="panel-th">' + ifName + ' :</th>'
            + '<td class="panel-td">';
          if ( conductorEditorMode === 'execute' ) {
            listHTML +=  '<span class="panel-span">' + value + '</span>';
          } else {
            listHTML +=  '<input value="' + value + '" maxlength="256" class="status-file-input panel-text" type="text" data-terminal="' + outTerminals[i].id + '" title="">';
          }
          listHTML +=  '</td>'
          + '</tr>';
        }
        $statusFileCaseList.html( listHTML );
        break;
      }
      case 'node':
        // パネル情報更新
        const nodeInfo = conductorUseList.conductorStatus['NODE_INFO'][ nodeID ];
        const panelNodeInfo = [
          ['#node-type', conductorData[ nodeID ].type ],
          ['#node-instance-id', nodeInfo.NODE_INSTANCE_NO ],
          ['#node-name', nodeInfo.NODE_NAME ],
          ['#node-status', nodeStatus[ nodeInfo.STATUS ][1] ],
          ['#node-status-file', nodeInfo.STATUS_FILE ],
          ['#node-start', nodeInfo.TIME_START ],
          ['#node-end', nodeInfo.TIME_END ],
          ['#node-oepration-id', nodeInfo.OPERATION_ID ],
          ['#node-operation-name', nodeInfo.OPERATION_NAME ]
        ];

        const panelNodeInfoLength = panelNodeInfo.length;

        for ( let i = 0; i < panelNodeInfoLength; i++ ) {
          if ( panelNodeInfo[ i ][ 1 ] === null || panelNodeInfo[ i ][ 1 ] === undefined ) panelNodeInfo[ i ][ 1 ] = '';
          $( panelNodeInfo[ i ][ 0 ] ).text( panelNodeInfo[ i ][ 1 ] );
        }
        // Jump
        if ( 'JUMP' in nodeInfo ) {
          const jumpURL = nodeInfo.JUMP;
          $('#node-Jump').html('<a href="' + jumpURL + '" target="_blank">' + getSomeMessage("ITABASEC020123") + '</a>');
        } else {
          $('#node-Jump').empty();
        }
        break;
      default:
    }

  } else {
    // Nodeが複数選択されている場合
    $conductorParameter.find('.editor-tab').find('li[data-tab="multiple"]').show().click()
      .siblings().hide();
  }
  
}


// 選択されたノードを整列する
const numberCompare = function( a, b, mode ) {
  if ( a === null && b === null ) return false;
  a = ( a === null )? b: a;
  b = ( b === null )? a: b;
  if ( mode === 's') {
    return ( a < b )? a: b;
  } else {
    return ( a < b )? b: a;
  }
};
$conductorParameter.find('#node-align').on('click', '.panel-button', function() {
  const alignType = $( this ).attr('id').replace('node-align-',''),
        selectLength = g_selectedNodeID.length;
  
  let pointX1 = null,
      pointY1 = null,
      pointX2 = null,
      pointY2 = null;
  
  // 取り消し、やり直し用の移動前移動後の座標を入れる
  const nodePosition = {
    'before': {},
    'after': {}
  };

  // 基準になる位置を求める
  for ( let i = 0; i < selectLength; i++) {
    const nodeID = g_selectedNodeID[i],
          x = conductorData[nodeID].x,
          y = conductorData[nodeID].y,
          w = conductorData[nodeID].w,
          h = conductorData[nodeID].h;
    nodePosition['before'][nodeID] = {
      'x': x,
      'y': y
    };
    switch( alignType ) {
      case 'left':
        pointX1 = numberCompare( pointX1, x, 's');
        break;
      case 'vertical':
        pointX1 = numberCompare( pointX1, x, 's');
        pointX2 = numberCompare( pointX2, x + w );
        break;
      case 'right':
        pointX1 = numberCompare( pointX1, x + w );
        break;
      case 'top':
        pointY1 = numberCompare( pointY1, y, 's');
        break;
      case 'horizonal':
        pointY1 = numberCompare( pointY1, y, 's');
        pointY2 = numberCompare( pointY2, y + h );
        break;
      case 'bottom':
        pointY1 = numberCompare( pointY1, y + h );
        break;
    }
  }
  
  // 整列する
  for ( let i = 0; i < selectLength; i++ ) {
    const nodeID = g_selectedNodeID[i],
          x = conductorData[nodeID].x,
          y = conductorData[nodeID].y,
          w = conductorData[nodeID].w,
          h = conductorData[nodeID].h;
    let nx, ny;
    switch( alignType ) {
      case 'left':
        nx = pointX1;
        ny = y;
        break;
      case 'vertical':
        nx = pointX1 + (( pointX2 - pointX1 ) / 2 ) - ( w / 2 );
        ny = y;
        break;
      case 'right':
        nx = pointX1 - w;
        ny = y;
        break;
      case 'top':
        nx = x;
        ny = pointY1;
        break;
      case 'horizonal':
        nx = x;
        ny = pointY1 + (( pointY2 - pointY1 ) / 2 ) - ( h / 2 );
        break;
      case 'bottom':
        nx = x;
        ny = pointY1 - h;
        break;
    }
    nodeMoveSet( nodeID, nx, ny );
    nodePosition['after'][nodeID] = {
      'x': nx,
      'y': ny
    }
  }
  
  conductorHistory.align( nodePosition );
  updateConductorData();
    
});


// 選択されたノードを等間隔に分布する
$conductorParameter.find('#node-equally-spaced').on('click', '.panel-button', function() {
  const alignType = $( this ).attr('id').replace('node-equally-spaced-',''),
        selectLength = g_selectedNodeID.length;

  // 取り消し、やり直し用の移動前移動後の座標を入れる
  const nodePosition = {
    'before': {},
    'after': {}
  };
  
  // 縦か横か？
  const dXY = ( alignType === 'vertical')? 'y': 'x',
        dWH = ( alignType === 'vertical')? 'h': 'w';
  
  // 選択されているノードが2以下の場合は何もしない
  if ( g_selectedNodeID < 3 ) return false;
  
  // Node
  const nodeArray = new Array( selectLength );
  for ( let i = 0; i < selectLength; i++ ) {
    const nodeID = g_selectedNodeID[i];
    nodeArray[i] = {
      'id': nodeID,
      'x': conductorData[nodeID].x,
      'y': conductorData[nodeID].y,
      'h': conductorData[nodeID].h,
      'w': conductorData[nodeID].w
    };
    nodePosition['before'][nodeID] = {
      'x': conductorData[nodeID].x,
      'y': conductorData[nodeID].y
    };
  }

  // 一番下のノードを調べる
  nodeArray.sort( function( a, b ){
    if ( a[dXY] + a[dWH] < b[dXY] + b[dWH] ) return -1;
    if ( a[dXY] + a[dWH] > b[dXY] + b[dWH] ) return 1;
    return 0;
  });
  let s2 = nodeArray[ selectLength - 1 ][dXY];
  
  // 一番上のノードを調べる
  nodeArray.sort( function( a, b ){
    if ( a[dXY] < b[dXY] ) return -1;
    if ( a[dXY] > b[dXY] ) return 1;
    return 0;
  });
  let s1 = nodeArray[ 0 ][dXY] + nodeArray[ 0 ][dWH];
  
  let positionRange = ( s2 - s1 > 0 )? s2 - s1: 0;
  
// 分布範囲がノードの大きさより小さいかどうか
  let nodeWidth = 0;
  for ( let i = 1; i < selectLength - 1; i++ ) {
    nodeWidth += conductorData[nodeArray[i].id][dWH];
  }
  if ( nodeWidth < positionRange ) {
    const equallySpaceWidth = Math.round(( positionRange - nodeWidth ) / ( selectLength - 1 ));
    let equallySpaceSum = s1 + equallySpaceWidth;
    for ( let i = 1; i < selectLength - 1; i++ ) {
      const x = ( alignType === 'vertical')? nodeArray[i].x: equallySpaceSum,
            y = ( alignType === 'vertical')? equallySpaceSum: nodeArray[i].y;
      nodeMoveSet( nodeArray[i].id, x, y );
      nodePosition['after'][nodeArray[i].id] = {
        'x': x,
        'y': y
      }
      equallySpaceSum += nodeArray[i][dWH] + equallySpaceWidth;
    }
  } else {
    // 分布範囲がノードサイズより小さい場合はノードのセンターで分布する
    s1 = s1 - ( nodeArray[0][dWH] / 2 );
    s2 = s2 + ( nodeArray[ selectLength - 1 ][dWH] / 2 );
    positionRange = ( s2 - s1 > 0 )? s2 - s1: 0;
    const equallySpaceWidth = Math.round( positionRange / ( selectLength - 1 ));
    let equallySpaceSum = s1 + equallySpaceWidth;
    for ( let i = 1; i < selectLength - 1; i++ ) {
      const x = ( alignType === 'vertical')? nodeArray[i].x: equallySpaceSum - ( nodeArray[i].w / 2 ),
            y = ( alignType === 'vertical')? equallySpaceSum - ( nodeArray[i].h / 2 ): nodeArray[i].y;
      nodeMoveSet( nodeArray[i].id, x, y );
      nodePosition['after'][nodeArray[i].id] = {
        'x': x,
        'y': y
      }
      equallySpaceSum += equallySpaceWidth;
    }
  }
  
  conductorHistory.align( nodePosition );
  updateConductorData();
  
});



// Conductor name
$conductorParameter.find('#conductor-class-name').on('change', function() {
  conductorData['conductor'].conductor_name = $( this ).val();
});

// 分岐パネル
$conductorParameter.find('.branch-add').on('click', function(){ addBranch( g_selectedNodeID[ 0 ] ); });
$conductorParameter.find('.branch-delete').on('click', function(){ removeBranch( g_selectedNodeID[ 0 ] ); });

// 条件ブロック
const conditionBlockHTML = function( key ) {
  return '<li class="' + movementEndStatus[ key ][0] + '" data-end-status="' + key + '">' + movementEndStatus[ key ][1] + '</li>';
}

// 条件状態更新
const conditionUpdate = function( nodeID ) {
  $branchCaseList.find('.branch-case').each( function( i ) {
    let conditions = [],
        tergetTerminalID = '';
    $( this ).find('li').each( function(){
      conditions.push( $( this ).attr('data-end-status') );
    });
    // どこの条件か？
    for ( let terminalID in conductorData[ nodeID ].terminal ) {
      const terminal = conductorData[ nodeID ].terminal[ terminalID ];
      if ( terminal.case === i + 1 ) {
        tergetTerminalID = terminal.id;
      }
    }
    // 条件を追加
    const conditionLength = conditions.length;
    let terminalConditionHTML = '';
    for ( let i = 0; i < conditionLength; i++ ) {
      terminalConditionHTML += conditionBlockHTML( conditions[ i ] );
    }
    $('#' + tergetTerminalID ).prev('.node-body').find('ul').html( terminalConditionHTML );
  });
  
  branchLine( nodeID );
  
  const beforeNodeData = $.extend( true, {}, conductorData[ nodeID ] );
  nodeSet( $('#' + nodeID ) );
  const afterNodeData = $.extend( true, {}, conductorData[ nodeID ] );
  conductorHistory.branch( beforeNodeData, afterNodeData );
  
  connectEdgeUpdate( nodeID );

}

// Skipチェックボックス状態変更
const nodeCheckStatus = function( nodeID ) {
  const $node = $('#' + nodeID ),
        $checkbox = $node.find('.node-skip-checkbox'),
        checkFlag = $checkbox.prop('checked');
  if ( checkFlag ) {
    $node.removeClass('skip');
    $checkbox.prop('checked', false );
    conductorData[ nodeID ].SKIP_FLAG = 0;
  } else {
    $checkbox.prop('checked', true );
    $node.addClass('skip');
    conductorData[ nodeID ].SKIP_FLAG = 1;
  }
  
}

// Skipチェックボックス
$conductorParameter.find('.panel-checkbox').on('change', function() {
  if ( g_selectedNodeID.length === 1 ) {
    nodeCheckStatus( g_selectedNodeID[0] );
  }
});

// Modal用tr
const modalTr = function( trList, idKey, nameKey ) {
  const trListLength = trList.length;
  let trHTML = '';
  for ( let i = 0; i < trListLength; i++ ) {
    trHTML += '<tr data-id="' + trList[i][idKey] + '" data-name="' + trList[i][nameKey] + '">'
    + '<th>' + trList[i][idKey] + '</th><td>' + trList[i][nameKey] + '</td></tr>';
  }
  return trHTML;
};

// 個別オペレーションセレクト
const operationUpdate = function( nodeID, id, name ) {
  const $node = $('#' + nodeID );
  if ( id !== 0 ) { 
    $node.addClass('operation');
    conductorData[ nodeID ].OPERATION_NO_IDBH = id;
    $node.find('.node-operation-data').text('[' + id + ']:' + name );
  } else {
    $node.removeClass('operation');
    conductorData[ nodeID ].OPERATION_NO_IDBH = null;
    $node.find('.node-operation-data').text('');
  }
  panelChange( nodeID );
};

// Callコンダクターセレクト
const callConductorUpdate = function( nodeID, id, name ) {
  const $node = $('#' + nodeID );
  if ( id !== 0 ) { 
    conductorData[ nodeID ].CALL_CONDUCTOR_ID = id;
    $node.addClass('call-select').find('.select-conductor-name-inner').text('[' + id + ']:' + name );
  } else {
    conductorData[ nodeID ].CALL_CONDUCTOR_ID = null;
    $node.removeClass('call-select').find('.select-conductor-name-inner').text('Not selected');
  }
  nodeSet( $('#' + nodeID ) );
  connectEdgeUpdate( nodeID );
  panelChange( nodeID );
};

// Callシンフォニーセレクト
const callSymphonyUpdate = function( nodeID, id, name ) {
  const $node = $('#' + nodeID );
  if ( id !== 0 ) { 
    conductorData[ nodeID ].CALL_SYMPHONY_ID = id;
    $node.addClass('call-select').find('.select-symphony-name-inner').text('[' + id + ']:' + name );
  } else {
    conductorData[ nodeID ].CALL_SYMPHONY_ID = null;
    $node.removeClass('call-select').find('.select-symphony-name-inner').text('Not selected');
  }
  nodeSet( $('#' + nodeID ) );
  connectEdgeUpdate( nodeID );
  panelChange( nodeID );
};

const modalSelectList = function( type ) {
  const $modalBody = $('.editor-modal-body');
  let operationListHTML = ''
  + '<div class="modal-table-wrap">'
    + '<table class="modal-table modal-select-table">'
      + '<thead>'
        + '<th class="id">ID</th><th class="name">Name</th>'
      + '</thead>'
      + '<tbody>'
        + '<tr data-id="0" data-name="unselected" class="selected"><th>-</th><td>Unselected</td></tr>';
  if ( type === 'operation') {
    operationListHTML += modalTr( conductorUseList.operationList, 'OPERATION_NO_IDBH','OPERATION_NAME')
  } else if ( type === 'conductor') {
    operationListHTML += modalTr( conductorUseList.conductorCallList, 'CONDUCTOR_CLASS_NO','CONDUCTOR_NAME')
  } else if ( type === 'symphony') {
    operationListHTML += modalTr( conductorUseList.symphonyCallList, 'SYMPHONY_CLASS_NO','SYMPHONY_NAME')
  }
  operationListHTML += ''
      + '</tbody>'
    + '</table>'
  + '</div>';
  
  $modalBody.html( operationListHTML );
  
  // 選択
  $modalBody.find('.modal-select-table').on('click', 'tr', function(){
    const $tr = $( this ),
          dataID = $tr.attr('data-id'),
          dataName = $tr.attr('data-name');
    $modalBody.find('tbody').find('.selected').removeClass('selected');
    $tr.addClass('selected');
  });
  
  // 決定・取り消しボタン
  const $modalButton = $('.editor-modal-footer-menu-button');
  $modalButton.prop('disabled', false ).on('click', function() {
    const $button = $( this ),
          btnType = $button.attr('data-button-type');
    switch( btnType ) {
      case 'ok':
        const nodeID = g_selectedNodeID[0],
              $selectTr = $modalBody.find('tbody').find('.selected'),
              dataID = Number( $selectTr.attr('data-id') ),
              dataName = $selectTr.attr('data-name');
        if ( type === 'operation') {
          operationUpdate( nodeID, dataID, dataName );
        } else if ( type === 'conductor') {
          callConductorUpdate( nodeID, dataID, dataName );
        } else if ( type === 'symphony') {
          callSymphonyUpdate( nodeID, dataID, dataName );
        }
        editor.modalClose();
        break;
      case 'cancel':
        editor.modalClose();
        break;
    }
  });
  
};

const modalRoleList = function() {

  const initRoleList = conductorData['conductor']['ACCESS_AUTH'];
  // 決定時の処理    
  const okEvent = function( newRoleList ) {
    conductorData['conductor']['ACCESS_AUTH'] = newRoleList;
    $('#conductor-edit-role').text(　getRoleListIdToName( newRoleList ) );
    editor.modalClose();
  };
  // キャンセル時の処理    
  const cancelEvent = function( newRoleList ) {
    editor.modalClose();
  };
  
  setRoleSelectModalBody( conductorUseList.roleList, initRoleList, okEvent, cancelEvent );
  
};

// 通知設定が登録済みか表示する
const noticeStatusUpdate = function() {
  const $noticeStatus = $('#conductor-notice-status');
  if ( conductorData.conductor.NOTICE_INFO !== undefined &&
       Object.keys( conductorData.conductor.NOTICE_INFO ).length ) {
    $noticeStatus.text(getSomeMessage("ITAWDCC92174"));
  } else {
    $noticeStatus.text('');
  }
};
// 通知設定モーダル
const modalNoticeList = function() {
  const $modalBody = $('.editor-modal-body'),
        $modalButton = $('.editor-modal-footer-menu-button'),
        noticeLength = conductorUseList.noticeList.length,
        statusLength = conductorUseList.noticeStatusList.length,
        noticeListURL = '/default/menu/01_browse.php?no=2100180012',
        hideNoticeName = getSomeMessage("ITAWDCC92008"); // ********
  let noticelistHTML = '';
  
  // チェック状態
  const noticeInfo = ( conductorEditorMode === 'checking')?
          conductorUseList.conductorStatus.CONDUCTOR_INSTANCE_INFO.NOTICE_INFO:
          conductorData.conductor.NOTICE_INFO,
        noticeCheckData = {};
  if ( noticeInfo !== undefined ) {
    for ( const key in noticeInfo ) {
      noticeCheckData[key] = noticeInfo[key].split(',');
    }
  }
  
  // EDITモード以外の場合決定ボタンを削除
  if ( conductorEditorMode !== 'edit') {
    $modalButton.filter('[data-button-type="ok"]').remove();
  }
  
  // 通知一覧が登録済みか？
  if ( noticeLength > 0 ) {
    const noticeNmaeWidth = 30,
          noticeStatusWidth = Math.round( ( 100 - noticeNmaeWidth ) / statusLength * 1000 ) / 1000;
    noticelistHTML += ''
    + '<div class="modal-table-wrap">'
      + '<form id="notice-list-form">'
      + '<table class="modal-table modal-select-table modal-notice-select-table">'
        + '<thead>'
          + '<tr>'
            + '<th class="notice-name" scope="col" style="width:' + noticeNmaeWidth + '%">' + getSomeMessage("ITABASEC020300") + '</th>';
    
    for ( let i = 0; i < statusLength; i++ ) {
      const statusName = editor.textEntities( conductorUseList.noticeStatusList[i].STATUS_NAME ),
            statusID = conductorUseList.noticeStatusList[i].STATUS_ID;
      noticelistHTML += '<th class="notice-status" scope="col" style="width:' + noticeStatusWidth + '%">'
      + '<span class="notice-status-bar notice-status-' + statusID + '"></span>'
      + statusName
      + '</th>';
    }
    noticelistHTML += '</tr></thead><tbody>'

    for ( let i = 0; i < noticeLength; i++ ) {
      const noticeID = conductorUseList.noticeList[i].NOTICE_ID,
            noticeName = editor.textEntities( conductorUseList.noticeList[i].NOTICE_NAME );
      if ( noticeName === hideNoticeName ) {
        noticelistHTML += '<tr class="hide-notice-row">'
        + '<th class="notice-name" scope="row">'
        + noticeName + '(' + noticeID + ')';
      } else {
        noticelistHTML += '<tr>'
        + '<th class="notice-name" scope="row">'
        + noticeName;
      }
      for ( let j = 0; j < statusLength; j++ ) {
        const statusValue = conductorUseList.noticeStatusList[j].STATUS_ID,
              statusID = 'notice' + noticeID + '-' + statusValue,
              check = ( noticeCheckData[noticeID] !== undefined && noticeCheckData[noticeID].indexOf( statusValue ) !== -1 )? true: false;

        noticelistHTML += '<td class="notice-status">';
        if ( noticeName !== hideNoticeName ) {
          if ( conductorEditorMode === 'edit') {
            // EDITモード
            const checked = ( check )? ' checked': '';
            noticelistHTML += '<div class="modal-checkbox-wrap">'
            + '<input id="' + statusID + '" class="modal-checkbox" type="checkbox" name="' + noticeID + '" value="' + statusValue + '"' + checked + '>'
            + '<label for="' + statusID + '" class="modal-checkbox-label">'
            + '</label></div>';
          } else {
            noticelistHTML += '<div class="modal-checkbox-wrap">';
            if ( check ) {
              noticelistHTML += '<span class="modal-checkbox-check-mark"></span>';
            }
            noticelistHTML += '</div>';
          }
        } else {
          noticelistHTML += '<div class="modal-checkbox-wrap"><span class="modal-checkbox-check-hide">-</span></div>';
        }
        noticelistHTML += '</td>';
      }
      noticelistHTML += '</tr>'
    }
    noticelistHTML += '</tbody>'
      + '</table></form>'
    + '</div>';
  } else {
    //noticelistHTML += '<div class="">通知先の登録が。<a ref="' + noticeListURL + '">Conductor通知一覧</a>より登録してください。</div>';
    noticelistHTML += '<div class="notice-blank-messege">' + getSomeMessage("ITAWDCC92175") + '<br>'
    + getSomeMessage("ITAWDCC92176") + '(<a href="' + noticeListURL + ' " target="_blank" rel="noopener noreferrer">'+  getSomeMessage("ITAWDCC92177") + '</a>)</div>';
  }
  
  $modalBody.html( noticelistHTML );
  
  // 決定・取り消しボタン
  $modalButton.prop('disabled', false ).on('click', function() {
    const $button = $( this ),
          btnType = $button.attr('data-button-type');
    switch( btnType ) {
      case 'ok':
        if ( conductorEditorMode === 'edit') {
          // チェック状態を取得
          const noticeCheck = {};
          $modalBody.find('.modal-checkbox:checked').each(function(){
            const $check = $( this ),
                  cName = $check.attr('name'),
                  cVal = $check.val();
            if ( noticeCheck[ cName ] === undefined ) {
              noticeCheck[ cName ] = cVal;
            } else {
              noticeCheck[ cName ] += ',' + cVal;
            }
          });
          conductorData.conductor.NOTICE_INFO = noticeCheck;
          noticeStatusUpdate();
          editor.modalClose();
        }
        break;
      case 'cancel':
        editor.modalClose();
        break;
    }
  });
  
};

// Notice select
$('#conductor-notice-select').on('click', function(){
  editor.modalOpen('Notice list', modalNoticeList, 'notice' );
});
// Role select
$('#conductor-role-select').on('click', function(){
  editor.modalOpen('Permission role select', modalRoleList, 'role' );
});
// Movement operation select
$('#movement-operation-select').on('click', function(){
  editor.modalOpen('Select movement operation', modalSelectList, 'operation' );
});
// Call conductor operation select
$('#conductor-call-operation-select').on('click', function(){
  editor.modalOpen('Select call conductor operation', modalSelectList, 'operation' );
});
// Call symphony operation select
$('#symphony-call-operation-select').on('click', function(){
  editor.modalOpen('Select call symphony operation', modalSelectList, 'operation' );
});
// Call conductor select
$('#conductor-call-select').on('click', function(){
  editor.modalOpen('Select call conductor', modalSelectList, 'conductor' );
});
// Call symphony select
$('#symphony-call-select').on('click', function(){
  editor.modalOpen('Select call symphony', modalSelectList, 'symphony' );
});
// Call conductor operation clear
$('#conductor-call-operation-clear, #movement-operation-clear, #symphony-call-operation-clear').on('click', function(){
  operationUpdate( g_selectedNodeID[0], 0 );
});
// Call conductor clear
$('#conductor-call-clear').on('click', function(){
 callConductorUpdate( g_selectedNodeID[0], 0 );
});
// Call symphony clear
$('#symphony-call-clear').on('click', function(){
 callSymphonyUpdate( g_selectedNodeID[0], 0 );
});


// 条件移動
$conductorParameter.find('#branch-condition-move').on('mousedown', 'li', function( e ) {

  const $condition = $( this ),
        scrollTop = $window.scrollTop(),
        scrollLeft = $window.scrollLeft(),
        conditionWidth = $condition.outerWidth(),
        conditionHeight = $condition.outerHeight(),
        mousedownPositionX = e.pageX,
        mousedownPositionY = e.pageY;

  let moveX, moveY;
  
  editor.actionMode.set('case-drag');
  
  $conductorParameter.find('.branch-case').on({
    'mouseenter' : function() { $( this ).addClass('hover'); },
    'mouseleave' : function() { $( this ).removeClass('hover'); }
  })
  
  $condition.css({
    'pointer-events' : 'none',
    'display' : 'block',
    'position' : 'fixed',
    'left' : ( mousedownPositionX - scrollLeft - conditionWidth / 2 ) + 'px',
    'top' : ( mousedownPositionY - scrollTop - conditionHeight / 2 ) + 'px',
    'z-index' : 99999
  });
  
  $window.on({
    'mousemove.conditionMove' : function( e ) {
      moveX = e.pageX - mousedownPositionX;
      moveY = e.pageY - mousedownPositionY;
      $condition.css('transform', 'translate(' + moveX + 'px,' + moveY + 'px)');
    },
    'mouseup.conditionMove' : function() {
      $condition.removeAttr('style');
      $window.off('mousemove.conditionMove mouseup.conditionMove');
      editor.actionMode.clear();
      if ( $conductorParameter.find('.branch-case.hover').length ) {
        $conductorParameter.find('.branch-case.hover').append( $condition );
        $conductorParameter.find('.branch-case').off().removeClass('hover');
        // 条件反映
        conditionUpdate( g_selectedNodeID[0] );
      }
    }
  });
});

// Status file 条件値入力
$conductorParameter.find('#status-file-case-list').on('input', '.status-file-input', function() {
  const $input = $( this ),
        terminalID = $input.attr('data-terminal'),
        val = $input.val(),      
        $terminal = $('#' + terminalID );
  
  // 値をセット
  if ( conductorData[ g_selectedNodeID[0] ].type === 'status-file-branch') {
    conductorData[ g_selectedNodeID[0] ].terminal[terminalID].condition = [val];
    $terminal.prev('.node-body').find('.branch-if-value-inner').text( val );
  }
});

// Note更新
$conductorParameter.find('.panel-textarea').on('input', function() {

  if ( g_selectedNodeID.length === 1 ) {
    const $targetNodeNote = $('#' + g_selectedNodeID[0] ).find('.node-note');
    let noteText = $( this ).val();

    // 入力されたテキスト
    conductorData[ g_selectedNodeID[0] ].note = noteText;
    
    // タグの無害化
    noteText = editor.textEntities( noteText );
    
    if ( noteText === '' ) {
      $targetNodeNote.removeClass('note-open');
    } else {
      $targetNodeNote.addClass('note-open').find('p').html( noteText );
    }

  } else if ( g_selectedNodeID.length === 0 ) {
    conductorData['conductor'].note = $( this ).val();
  }

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   取り消し、やり直し
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const $undoButton = $('#button-undo'),
      $redoButton = $('#button-redo'),
      maxHistroy = 10; // 最大履歴数

let workHistroy = [],
    workInterrupt = [],
    workCounter = 0;

// 取り消し、やり直しボタンチェック
const historyButtonCheck = function() {
    if ( workHistroy[ workCounter - 1 ] !== undefined ) {
      $undoButton.prop('disabled', false );
    } else {
      $undoButton.prop('disabled', true );
    }
    if ( workHistroy[ workCounter ] !== undefined ) {
      $redoButton.prop('disabled', false );
    } else {
      $redoButton.prop('disabled', true );
    }
};

// 履歴数の調整
const historyControl = function() {
    // 履歴追加後の履歴を削除する
    if ( workHistroy[ workCounter ] !== undefined ) {
      workHistroy.length = workCounter;
    } 
    // 最大履歴数を超えた場合最初の履歴を削除する
    if ( workHistroy.length > maxHistroy ) {
      workHistroy.shift();
      workCounter--;
    }
    historyButtonCheck();
};

//
const interruptRedo = function( interruptData ) {
  removeEdge( interruptData[0].id, 0 );
  conductorData[ interruptData[1].id ] = interruptData[1];
  edgeConnect( interruptData[1].id );
  conductorData[ interruptData[2].id ] = interruptData[2];
  edgeConnect( interruptData[2].id );
};

const branchUndoRedo = function( nodeID, nodeData ) {
  $('#' + nodeID ).remove();
  delete conductorData[ nodeID ];
  conductorData[ nodeID ] = nodeData;
  const $branchNode = createNodeHTML( nodeID );
  
  $artBoard.append( $branchNode );
  nodeSet( $branchNode, conductorData[ nodeID ].x, conductorData[ nodeID ].y );
  branchLine( nodeID );
  // 接続チェック
  for ( let terminalID in conductorData[ nodeID ]['terminal'] ) {
    if ( 'edge' in conductorData[ nodeID ]['terminal'][ terminalID ] ) {
      $branchNode.find('#' + terminalID ).addClass('connected');
    }
  }
  
  
  connectEdgeUpdate( nodeID );
};

// 履歴管理
const conductorHistory = {
  // 割り込んだ際にEdgeを保存しておく
  'interrupt': function( removeEdgeData, newEdge1, newEdge2 ) {
    const newEdge1Data = $.extend( true, {}, conductorData[ newEdge1 ] ),
          newEdge2Data = $.extend( true, {}, conductorData[ newEdge2 ] );;
    workInterrupt = [ removeEdgeData, newEdge1Data, newEdge2Data ];
  },
  // リストからノードセット
  'nodeSet': function( nodeID, interruptFlag ) {
    const nodeDataCopy = $.extend( true, {}, conductorData[ nodeID ] );
    if ( interruptFlag === false ) {
      workInterrupt = [];
    }
    workHistroy[ workCounter++ ] = {
      'type': 'nodeSet',
      'data': {
        'interruptFlag': interruptFlag,
        'nodeData': nodeDataCopy,
        'interrupt': workInterrupt
      }
    };

    historyControl();
  },
  // 移動
  'move': function( nodeID, x, y, interruptFlag ) {
    if ( interruptFlag === false ) {
      workInterrupt = [];
    }
    workHistroy[ workCounter++ ] = {
      'type': 'move',
      'data': {
        'interruptFlag': interruptFlag,
        'nodeID': nodeID,
        'x': x,
        'y': y,
        'interrupt': workInterrupt
      }
    };
    historyControl();
  },
  // 整列
  'align': function( position ) {
    const wc = workCounter++,
          nodeIdLength = position['before'].length;
    workHistroy[ wc ] = {
      'type': 'align',
      'data': position
    };
    historyControl();
  },
  // 分岐の増減など
  'branch': function( beforeNodeData, afterNodeData ) {
    if (
        workHistroy[ workCounter - 1 ] !== undefined &&
        workHistroy[ workCounter - 1 ].type === 'branch' &&
        workHistroy[ workCounter - 1 ]['data'].nodeID === afterNodeData.id
    ) {
      workCounter--;
      workHistroy[ workCounter++ ]['data'].afterNodeData = afterNodeData;
    } else {
      workHistroy[ workCounter++ ] = {
        'type': 'branch',
        'data': {
          'nodeID': afterNodeData.id,
          'beforeNodeData': beforeNodeData,
          'afterNodeData': afterNodeData
        }
      };
    }
    historyControl(); 
  },
  // Edge接続
  'connect': function( edgeID ) {
    const edgeDataCopy = $.extend( true, {}, conductorData[ edgeID ] );
    workHistroy[ workCounter++ ] = {
      'type': 'connect',
      'data': {
        'edgeID': edgeID,
        'edgeData': edgeDataCopy
      }
    };
    historyControl();
  },
  // Nodeを削除
  'nodeRemove': function( nodeID ) {
    let nodeIdCopy;
    if ( Array.isArray( nodeID ) ) {
      nodeIdCopy = $.extend( true, [], nodeID );
    } else {
      nodeIdCopy = [ nodeID ];
    }        
    // 接続している線の一覧を作成
    let connectEdgeList = [];
    const nodeIdLength = nodeIdCopy.length;
    for ( let i = 0; i < nodeIdLength; i++ ) {
      if ( 'terminal' in  conductorData[ nodeIdCopy[i] ] ) {
        const terminals = conductorData[ nodeIdCopy[i] ].terminal;
        for ( let terminal in terminals ) {
          const terminalData = terminals[ terminal ];
          if ( 'edge' in terminalData ) {
            if ( connectEdgeList.indexOf( terminalData.edge ) === -1 ) {
              connectEdgeList.push( terminalData.edge );
            }
          }
        }
      }
    }    
    // 削除するnode, edgeをコピーする
    const removeConductorList = nodeIdCopy.concat( connectEdgeList ),
          removeConductorLength = removeConductorList.length;
    let removeConductorData = {}
    for ( let i = 0; i < removeConductorLength; i++ ) {
      removeConductorData[ removeConductorList[i] ] = $.extend( true, {}, conductorData[ removeConductorList[i] ] );
    }    
    workHistroy[ workCounter++ ] = {
      'type': 'nodeRemove',
      'data': {
        'removeData': removeConductorData,
        'removeNodeIdList': nodeIdCopy
      }
    };
    historyControl();
  },
  // Edgeを削除
  'edgeRemove': function( edgeID ) {
    const edgeDataCopy = $.extend( true, {}, conductorData[ edgeID ] );
    workHistroy[ workCounter++ ] = {
      'type': 'edgeRemove',
      'data': {
        'removeEdgeID': edgeID,
        'removeEdgeData': edgeDataCopy
      }
    };
    historyControl();
  },
  'undo': function() {
    if ( workHistroy[ workCounter - 1 ] !== undefined ) {
      workCounter--;
      const undo = workHistroy[ workCounter ];
      nodeDeselect();
      panelChange();

      switch( undo['type'] ) {
        case 'nodeSet':
          nodeRemove( undo['data']['nodeData'].id );
          if ( undo['data']['interruptFlag'] === true ) {
            conductorData[ undo['data']['interrupt'][0].id ] = undo['data']['interrupt'][0];
            edgeConnect( undo['data']['interrupt'][0].id );
          }
          break;
        case 'branch':
          branchUndoRedo( undo['data'].nodeID, undo['data'].beforeNodeData );
          break;
        case 'move':
          nodeMoveSet( undo['data']['nodeID'], -undo['data']['x'], -undo['data']['y'], 'relative');
          if ( undo['data']['interruptFlag'] === true ) {
            removeEdge( undo['data']['interrupt'][1].id, 0 );
            removeEdge( undo['data']['interrupt'][2].id, 0 );
            conductorData[ undo['data']['interrupt'][0].id ] = undo['data']['interrupt'][0];
            edgeConnect( undo['data']['interrupt'][0].id );
          }
          break;
        case 'align':
          for ( const id in undo['data']['before'] ) {
            nodeMoveSet( id, undo['data']['before'][id].x, undo['data']['before'][id].y );
          }
          break;
        case 'connect':
          removeEdge( undo['data']['edgeID'], 0 );
          break;
        case 'nodeRemove':
          $.extend( conductorData, undo['data']['removeData'] );
          nodeReSet( undo['data']['removeData'] );
          break;
        case 'edgeRemove':
          conductorData[ undo['data']['removeEdgeID'] ] = undo['data']['removeEdgeData'];
          edgeConnect( undo['data']['removeEdgeID'] );
          break;
      }  

      historyButtonCheck();
    }
  },
  'redo': function() {
    if ( workHistroy[ workCounter ] !== undefined ) {
      const redo = workHistroy[ workCounter++ ];
      nodeDeselect();
      panelChange();

      switch( redo['type'] ) {
        case 'nodeSet':
          
          const nodeID = redo['data']['nodeData'].id;
          conductorData[ nodeID ] = redo['data']['nodeData'];
          
          const $node = createNodeHTML( nodeID );
          $artBoard.append( $node );
          nodeSet( $node, conductorData[ nodeID ].x, conductorData[ nodeID ].y );
          
          const nodeCheck = ['merge', 'conditional-branch', 'parallel-branch', 'status-file-branch'];
          if ( nodeCheck.indexOf( conductorData[ nodeID ].type ) !== -1 ) {
            $node.ready( function(){
              branchLine( nodeID );
            });
          }
          if ( redo['data']['interruptFlag'] === true ) {
            interruptRedo( redo['data']['interrupt'] );
          }
          break;
        case 'branch':
          branchUndoRedo( redo['data'].nodeID, redo['data'].afterNodeData );
          break;
        case 'move':
          nodeMoveSet( redo['data']['nodeID'], redo['data']['x'], redo['data']['y'], 'relative');
          if ( redo['data']['interruptFlag'] === true ) {
            interruptRedo( redo['data']['interrupt'] );
          }
          break;
        case 'align':
          for ( const id in redo['data']['after'] ) {
            nodeMoveSet( id, redo['data']['after'][id].x, redo['data']['after'][id].y );
          }
          break;
        case 'connect':
          conductorData[ redo['data']['edgeID'] ] = redo['data']['edgeData'];
          edgeConnect( redo['data']['edgeID'] );
          break;
        case 'nodeRemove':
          nodeRemove( redo['data']['removeNodeIdList'] );
          break;
        case 'edgeRemove':
          removeEdge( redo['data']['removeEdgeID'], 0 );
          break;
      }

      historyButtonCheck();  
    }
  },
  'clear': function() {
    workCounter = 0;
    workHistroy = [];
    
    historyButtonCheck();
  }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$('.editor-header-menu').on('click', '.editor-menu-button', function(){

    const $button = $( this ),
          buttonType = $button.attr('data-menu'),
          buttonDisabledTime = 300;
    
    switch ( buttonType ) {
      case 'conductor-test-run':
        conductorTestRun();
        break;
      case 'conductor-new':
        if ( message('2002') ) {
          clearConductor();
          InitialSetNode();
          canvasPositionReset( 0 );
        }
        break;
      case 'conductor-save':
        editor.downloadText( JSON.stringify( conductorData ), 'icd', conductorData['conductor'].conductor_name  );
        break;
      case 'conductor-read':
        editor.readText.open();
        break;
      case 'undo':
        conductorHistory.undo();
        break;
      case 'redo':
        conductorHistory.redo();
        break;
      case 'node-delete':
        conductorHistory.nodeRemove( g_selectedNodeID );
        nodeRemove( g_selectedNodeID );
        break;
      case 'view-all':
        nodeViewAll();
        break;
      case 'view-reset':
        canvasPositionReset();
        break;
      case 'full-screen-on':
      case 'full-screen-off':
        editor.fullScreen( $body.get(0) );
        break;
    }
    // Undo Redoは別管理
    if ( ['undo','redo'].indexOf( buttonType ) === -1 ) {
      // 一定時間ボタンを押せなくする
      $button.prop('disabled', true );

      if ( buttonType !== 'node-delete' ) {
        $button.addClass('active');
        // buttonDisabledTime ms 後に復活
        setTimeout( function(){
          $button.removeClass('remove').prop('disabled', false );
        }, buttonDisabledTime );
      }
    }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   conductorデータ更新・確認
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const updateConductorData = function() {
      if ( initialValue.debug === true ) {
        window.console.group('Conductor data');
        window.console.log('----- Conductor data -----');
        window.console.log( conductorData );
        window.console.log( JSON.stringify( conductorData ) );
        window.console.groupEnd('Conductor data');
      }
      
      // 変更があった場合のページ移動時処理フラグ
      editor.confirmPageMove( true );
      editorValue.setStorage = true;
      
      // カウンターを更新
      conductorData['config'].nodeNumber = g_NodeCounter;
      conductorData['config'].terminalNumber = g_TerminalCounter;
      conductorData['config'].edgeNumber = g_EdgeCounter;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   conductorの保存と読み込み
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// カンマ区切りロールIDリストからロールNAMEリストを返す
const getRoleListIdToName = function( roleListText ) {
  if ( roleListText !== undefined && roleListText !== '' ) {
    const roleList = roleListText.split(','),
          roleListLength = roleList.length,
          roleNameList = new Array;
    for ( let i = 0; i < roleListLength; i++ ) {
      const roleName = listIdName('role', roleList[i]);
      if ( roleName !== undefined ) {
        const hideRoleName = getSomeMessage("ITAWDCC92008");
        if ( roleName !== hideRoleName ) {
          roleNameList.push( roleName );
        } else {
          roleNameList.push( roleName + '(' + roleList[i] + ')');
        }
      } else {
        // ID変換失敗
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
      if ( roleName !== undefined ) {
        roleIdList.push( roleList[i] );
      }
    }
    return roleIdList.join(',');
  } else {
    return '';
  }
};

const panelConductorReset = function() {
  $('#conductor-class-id').text('');
  if ( conductorEditorMode === 'edit' || conductorEditorMode === 'view') {
    $('#conductor-class-name').val('');
    $('#conductor-class-note').val('');
    $('#conductor-edit-role').text( getRoleListIdToName( conductorData['conductor']['ACCESS_AUTH'] ) );
  }
  $('#conductor-class-name-view').text('');
  $('#conductor-view-role').text( getRoleListIdToName( conductorData['conductor']['ACCESS_AUTH'] ) );
  
  noticeStatusUpdate();
};

// リセット
const clearConductor = function() {
    // 選択を解除
    nodeDeselect();
    // 全て消す
    $svgArea.empty();
    $artBoard.find('.node').remove()
    // カウンターリセット
    g_NodeCounter = 1;
    g_TerminalCounter = 1;
    g_EdgeCounter = 1;
    // 履歴クリア
    conductorHistory.clear();
    // LocalStorage削除
    editor.keyRemoveLocalStorage('conductor-edit-temp');
    // 変更フラグリセット
    editor.confirmPageMove( false );
    editorValue.setStorage = false;
    // 初期値
    setInitialConductorData();
    canvasPositionReset(0);
    // パネル情報
    panelChange();
    panelConductorReset();
}
// ローカルストレージに保存する
const saveConductor = function( saveConductorData ) {
    if ( editorValue.setStorage === true ) {
      if ( initialValue.debug === true ) {
        window.console.group('Set local strage');
        window.console.log('----- Set local strage -----');
        window.console.log( saveConductorData );
        window.console.log( JSON.stringify( saveConductorData ) );
        window.console.groupEnd('Set local strage');
      }    
      editor.setLocalStorage('conductor-edit-temp', saveConductorData );
    }
};
// 新規登録時のみ、ページを移動する際にローカルストレージに保存する
$window.on('beforeunload', function(){
  if ( conductorEditorMode === 'edit' && $editor.attr('data-editor-config') !== 'update') {
    saveConductor( conductorData );
  }
});
// 再描画
const selectConductor = function( result ) {
  clearConductor();
  try {
    loadConductor( JSON.parse( result ) );
  } catch( e ) {
    window.console.error( e );
    editor.log.set('error','JSON parse error.');
  }
}
// 読み込みイベントコールバック
const readConductor = function( result ) {
  clearConductor();
  try {
    loadConductor( JSON.parse( result ), 'edit');
  } catch( e ) {
    window.console.error( e );
    editor.log.set('error','JSON parse error.');
  }
}
editor.readText.setInput( '.icd' ,readConductor );

const nodeReSet = function( reSetConductorData ) {
    $editor.addClass('load-conductor');
    editor.actionMode.set('editor-pause');
    
    // Nodeを再配置
    let readyCounter = 0;
    for ( let nodeID in reSetConductorData ) {
      if ( RegExp('^node-').test( nodeID ) ) {
        if ( nodeID === 'node-1') {
          if ( $('#node-1').length ) {
            $('#node-1').remove();
          }
        }
        const $node = createNodeHTML( nodeID );
        $artBoard.append( $node );
        nodeGemCheck( $node );
        nodeSet( $node, reSetConductorData[ nodeID ].x, reSetConductorData[ nodeID ].y );
        
        // 分岐ノード
        const nodeCheck = ['merge', 'conditional-branch', 'parallel-branch', 'status-file-branch'];
        if ( nodeCheck.indexOf( reSetConductorData[ nodeID ].type ) !== -1 ) {
          readyCounter++;
          $node.ready( function() {
            branchLine( nodeID );
            readyCounter--;
          });
        }
      }
    }

    // 完了チェック
    let timeoutCounter = 3000,
        loopCheckTime = 100;
    const loadComplete = function() {
      if ( readyCounter <= 0 ) {
        // Edge再接続
        try {
          for ( let edgeID in reSetConductorData ) {
            if ( RegExp('^line-').test( edgeID ) ) {
              edgeConnect( edgeID );
            }
          }
        } catch( e ) {
          message('1001');
          window.console.error( e );
        }
        editor.actionMode.clear();
        $editor.removeClass('load-conductor');
        // 描画終了トリガー
        $window.trigger('conductorDrawEnd');
      } else {
        timeoutCounter = timeoutCounter - loopCheckTime;
        if ( timeoutCounter > 0 ) {
          setTimeout( loadComplete, loopCheckTime );
        } else {
          readyCounter = 0;
          loadComplete();
        }
      }
      // 作業確認時リザルトマークにイベントを付ける
      if ( conductorEditorMode === 'checking') {
        $canvasVisibleArea.find('.node-result').on({
          'mouseenter': function(){
            const $result = $( this ),
                  href = $result.attr('data-href');
            $result.addClass('mouseenter');
          },
          'mouseleave': function(){
            $( this ).removeClass('mouseenter');
          },
          'click': function(){
            const $result = $( this ),
                  href = encodeURI( $result.attr('data-href') );
            if ( href !== '#') {
              open( href, '_blank') ;
            }
          }
        });
      }
    };
    loadComplete();
}


// 読む込む
const loadConductor = function( loadConductorData, mode ) {
    conductorData = loadConductorData;
    
    if ( initialValue.debug === true ) {
      window.console.group('Get conductor data');
      window.console.log('----- Get conductor data -----');
      window.console.log( conductorData );
      window.console.log( JSON.stringify( conductorData ) );
      window.console.groupEnd('Get conductor data');
    }
    
    
    try {
      if ( mode === 'edit') {
        // 読み込みデータはIDと追い越し判定用日時はリセットする
        conductorData['conductor'].id = null;
        conductorData['conductor'].LUT4U = null;
        conductorMode('edit');
      }
      g_NodeCounter = conductorData['config'].nodeNumber;
      g_TerminalCounter = conductorData['config'].terminalNumber;
      g_EdgeCounter = conductorData['config'].edgeNumber;

      // Conductor情報
      const conductorID = conductorData['conductor'].id;
      if ( conductorID !== '' && conductorID !== null ) {
        $('#conductor-class-id').text( conductorID );
      } else {
        $('#conductor-class-id').text('Auto numbering');
      }
      let conductorNoteText = conductorData['conductor'].note;
      if ( !editor.checkValue( conductorNoteText ) ) {
        conductorNoteText = '';
      }
      if ( conductorEditorMode === 'edit' || conductorEditorMode === 'view' ) {
        $('#conductor-class-name').val( conductorData['conductor'].conductor_name );
        $('#conductor-class-note').val( conductorNoteText );
        $('#conductor-edit-role').text( getRoleListIdToName( conductorData['conductor'].ACCESS_AUTH ) );
      }
      $('#conductor-class-name-view').text( conductorData['conductor'].conductor_name );
      $('#conductor-class-note-view').text( conductorNoteText );
      $('#conductor-view-role').text( getRoleListIdToName( conductorData['conductor'].ACCESS_AUTH ) );
      
      noticeStatusUpdate();
      nodeReSet( conductorData );
      nodeViewAll( 0 );
      
    } catch( e ) {
      window.console.error( e );
      editor.actionMode.clear();
      $editor.removeClass('load-conductor');
      clearConductor();
      message('1001');
       
      setTimeout( function(){
        alert( getSomeMessage("ITABASEC020008") );
        var url = '/default/menu/01_browse.php?no=2100180002';
        location.href = url;
      }, 1 );
    }
    
};

// Conductorデータ再取得 > 再表示する
$window.on('conductorReset', function(){  
  selectConductor( conductorUseList.conductorData );
  if ( conductorEditorMode === 'edit') {
    conductorMode('view');
  }
  if ( conductorEditorMode === 'execute') {
    executeButtonCheck();
  }
});

// 作業状態確認ポップアップ（移動拡縮対応）



// 作業確認ポップアップイベント
const itaPopup = function( $target, id ) {
  
  const popupID = 'popup-' + id;
  let $popup;
  
  // 各ノード個別の作業状況確認ポップアップ追加
  if ( $('#' + popupID ).length ) {
    $popup = $('#' + popupID );
  } else {
    $popup = $('<div/>').attr('id', popupID ).addClass('itaPopup')
      .text( getSomeMessage("ITABASEC020123") ).css('display','none');
    if ( !$target.is('.resultPopup') ) {
      $body.append( $popup );
    }
  }
  
  // ノードの状態で表示・非表示を切り替える
  if ( $target.is('.node-jump') ) {
    $popup.css('visibility','visible');
  } else {
    $popup.css('visibility','hidden');
  }
  
  if ( !$target.is('.resultPopup') ) {
    // 画面を移動しても追従するようにする
    $target.addClass('resultPopup').on({
      'mouseenter': function(){
        const $this = $( this );
        $popup.css('display','block');

        // 位置更新
        const updatePosition = function() {
          const mpx = $this.offset().left + ( ( $this.outerWidth() / 2 ) * editorValue.scaling ),
                mpy = $this.offset().top - ( 4 * editorValue.scaling )
          $popup.css({ left: mpx, top: mpy });
        };
        updatePosition();
        // マウスムーブとスクロールでも位置を更新する
        $this.on('mousemove', updatePosition )
          .on( mousewheelevent, function(){
          setTimeout( function(){ updatePosition(); }, 1 );
        });
      },
      'mouseleave': function(){
        const $this = $( this );
        $popup.css('display','none');
        $this.off('mousemove ' + mousewheelevent );
      }
    });
  }

};


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   作業確認　Conducotr画面とパネルの情報を更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 00_javascript.js( proxy.printConductorStatus( conductorInstanceID ) )
// conductorUseList.conductorStatus
let pollingTimerID = '';
const conductorStatusUpdate = function( exeNumber ) {

  // ポーリングタイム（ms）
  //const intervalTime = 3000;
  var tmpintervalTime = 3000;
  if( document.getElementById('intervalOfDisp') !== null ) {
    var tmpintervalTime = document.getElementById('intervalOfDisp').innerHTML;
    if( isNaN( tmpintervalTime ) === true ) {
       var tmpintervalTime = 3000;
    }
  }
  const intervalTime = tmpintervalTime;
 

  
  // 最初（exeNumberが0）だけ実行する処理
  if ( exeNumber === 0 ) {
    // proxy.printConductorStatusでTrigger
    $window.on('conductorStatusUpdate', function(){
      conductorStatusUpdate(1);
    });
    // ボタンを一旦非表示に
    $('#cansel-instance, #scram-instance').hide();
  }

  // パネル情報更新
  const conductorInfo = conductorUseList.conductorStatus['CONDUCTOR_INSTANCE_INFO'];
  const panelConducotrInfo = [
    ['#conductor-instance-id', conductorInfo.CONDUCTOR_INSTANCE_ID ],
    ['#conductor-instance-status', conductorStatus[ conductorInfo.STATUS_ID ][1] ],
    ['#conductor-instance-pause', conductorInfo.PAUSE_STATUS ],
    ['#conductor-instance-start', conductorInfo.TIME_START ],
    ['#conductor-instance-end', conductorInfo.TIME_END ],
    ['#conductor-instance-user', conductorInfo.EXECUTION_USER ],
    ['#conductor-instance-reservation', conductorInfo.TIME_BOOK ],
    ['#conductor-instance-emergency', conductorInfo.ABORT_EXECUTE_FLAG ],
    ['#select-operation-id', conductorInfo.OPERATION_NO_IDBH ],
    ['#select-operation-name', conductorInfo.OPERATION_NAME ]
  ];
  // 選択されている場合はそのノードのパネルを表示する
  if ( g_selectedNodeID.length >= 1 ) {
    panelChange( g_selectedNodeID[0] );
  }
  
  const panelConducotrInfoLength = panelConducotrInfo.length;
  
  for ( let i = 0; i < panelConducotrInfoLength; i++ ) {
    if ( panelConducotrInfo[ i ][ 1 ] === null ) panelConducotrInfo[ i ][ 1 ] = '';
    $( panelConducotrInfo[ i ][ 0 ] ).text( panelConducotrInfo[ i ][ 1 ] );
  }  
  
  // Node情報更新
  const nodeInfo = conductorUseList.conductorStatus['NODE_INFO'],
        nodeInfoLength = nodeInfo.length;
  
  // 条件分岐で選ばれなかった分岐以降を半透明にする
  const nextNodeUnused = function( edgeID ) {

    const nextNodeID = conductorData[ edgeID ].inNode,
          nextNodeType = conductorData[ nextNodeID ].type;
    
    $('#' + edgeID ).attr('data-status','run-unused');
    
    nodeUnused( nextNodeID );

  };
  const nodeUnused = function( nodeID ) {
    const nodeData = conductorData[ nodeID ],
          outTerminals = terminalInOutID( nodeData['terminal'], 'out'),
          outTerminalLength = outTerminals.length,
          $node = $('#' + nodeID );
    $node.addClass('run-unused');
    conductorData[ nodeID ].endStatus = true;
    for ( let i = 0; i < outTerminalLength; i++ ) {
      nextNodeUnused( nodeData['terminal'][ outTerminals[ i ] ].edge );
    }
  };
  const condionalBranchCheck = function( nodeID ) {
    // 一つ前のノードの結果をチェックする
    const inTerminal = terminalInOutID( conductorData[ nodeID ].terminal, 'in'),
          tergetNodeID = conductorData[ nodeID ].terminal[ inTerminal[0] ].targetNode;
    let   nodeStatus = nodeInfo[ tergetNodeID ].STATUS;
    
    // 一部のNodeステータスをMovementステータスに合わせる
    if ( nodeStatus === '5') nodeStatus = '9';
    if ( nodeStatus === '12' || nodeStatus === '13') nodeStatus = '14';
    
    // 終了しているかチェックする
    if ( ['6','7','9','10','11','14','15','9999'].indexOf( nodeStatus ) !== -1 ) {
      conductorData[ nodeID ].endStatus = true;
      const inTerminalID = terminalInOutID( conductorData[ nodeID ].terminal, 'in'),
            outTerminals = terminalInOutID( conductorData[ nodeID ].terminal, 'out'),
            outTerminalLength = outTerminals.length,
            $branchNode = $('#' + nodeID );
      let otherFlag = true,
          otherTerminal;
      $branchNode.addClass('running');
      $('#' + conductorData[ nodeID ].terminal[ inTerminal[0] ].edge ).attr('data-status', 'running');
      for ( let i = 0; i < outTerminalLength; i++ ) {
        const terminal = conductorData[ nodeID ]['terminal'][ outTerminals[ i ] ];
        if ( terminal['condition'][0] !== '9999' ) {
          if ( terminal['condition'].indexOf( nodeStatus ) !== -1 ) {
            otherFlag = false;
          } else {
            $('#' + terminal.id ).closest('.node-sub').addClass('run-unused');
            $branchNode.find( '.' + terminal.id + '-branch-line').attr('data-status', 'unused');
            nextNodeUnused( terminal.edge );
          }
        } else {
          otherTerminal = terminal;
        }
      }
      if ( otherFlag !== true ) {
        $('#' + otherTerminal.id ).closest('.node-sub').addClass('run-unused');
        $branchNode.find( '.' + otherTerminal.id + '-branch-line').attr('data-status', 'unused');
        nextNodeUnused( otherTerminal.edge );
      }
    }
  };
  
  // Status file blanchの状態を更新する
  const statusFileBranch = function( nodeID ) {
    const $branchNode = $('#' + nodeID ),
          inTerminalsID = terminalInOutID( conductorData[ nodeID ].terminal, 'in'),
          inTerminal = conductorData[ nodeID ].terminal[ inTerminalsID[0] ],
          prevNodeID = inTerminal.targetNode,
          prevNodeStatus = nodeInfo[ prevNodeID ].STATUS,
          prevNodeStatusFile = nodeInfo[ prevNodeID ].STATUS_FILE;
    
    // 前のNodeが終了しているかチェック
    if ( ['5','9','12','13','14','15'].indexOf( prevNodeStatus ) !== -1 ) {
      conductorData[ nodeID ].endStatus = true;
      const $prevEdge = $('#' + inTerminal.edge ),
            terminals = Object.keys( conductorData[ nodeID ].terminal ).map(function(k){
                    return conductorData[ nodeID ].terminal[k];
                }),
            outTerminals = terminals.filter(function(v){
                    if ( v.case !== undefined && v.case !== 'else') return true;
                }).sort(function(a,b){
                    if ( a.case > b.case ) {
                        return 1;
                    } else if ( a.case < b.case ) {
                        return -1;
                    } else {
                        return 0;
                    }
                }),
            outTerminalLength = outTerminals.length,
            elseTerminal = terminals.filter(function(v){
                    if ( v.case === 'else') return true;
                });
      
      // Caseの順番にStatus fileの値とConditionの値をチェックする
      let matchTerminalID = undefined;
      for ( let i = 0; i < outTerminalLength; i++ ) {
        if ( outTerminals[i].condition.join('') === prevNodeStatusFile && matchTerminalID === undefined ) {
          $('#' + outTerminals[i].id ).closest('.node-sub').attr('data-match', 'true');
          matchTerminalID = outTerminals[i].id;
        } else {
          $('#' + outTerminals[i].id ).closest('.node-sub').addClass('run-unused');
          $branchNode.find( '.' + outTerminals[i].id + '-branch-line').attr('data-status', 'unused');
          nextNodeUnused( outTerminals[i].edge );
        }
      }
      // マッチしなかったらelse
      if ( matchTerminalID === undefined ) {
        matchTerminalID = elseTerminal[0].id;
      } else {
        $('#' + elseTerminal[0].id ).closest('.node-sub').addClass('run-unused');
        $branchNode.find( '.' + elseTerminal[0].id + '-branch-line').attr('data-status', 'unused');
        nextNodeUnused( elseTerminal[0].edge );
      }

      $branchNode.addClass('running');
      if ( prevNodeStatusFile === undefined ) {
        $branchNode.attr('data-status-file', 'unknown').find('.status-file-result-inner').text('Unknown');
      } else {
        $branchNode.attr('data-status-file', 'known').find('.status-file-result-inner').text( prevNodeStatusFile );
      }
      
      $prevEdge.attr('data-status', 'running');
    }
  };
  
  // 並列マージの状態を更新する
  const parallelMergeCheck = function( nodeID ) {
    const inTerminals = terminalInOutID( conductorData[ nodeID ].terminal, 'in'),
          inTerminalLength = inTerminals.length,
          $node = $('#' + nodeID );
    let   waitingCount = 0;
    for ( let i = 0; i < inTerminalLength; i++ ) {
      const tergetNodeID = conductorData[ nodeID ].terminal[ inTerminals[i] ].targetNode;
      // 終了しているかチェックする
      if ( ['5','9','12','13','14','15'].indexOf( nodeInfo[ tergetNodeID ].STATUS ) !== -1 ) {
        waitingCount++;
        $node.addClass('running');
        $('#' + inTerminals[i] ).next().find('.merge-status').attr('data-status', 'waiting');
        $('#' + conductorData[ nodeID ].terminal[ inTerminals[i] ].edge ).attr('data-status', 'running');
      }      
    }
    // 全て待機状態ならコンプリートにする
    if ( inTerminalLength === waitingCount ) {
      $node.find('.merge-status').attr('data-status', 'complete');
    }
  };
  
  // Movement、Call、Endの状態を更新する
  const movementCheck = function( nodeID ) {
  
    const nodeInfo = conductorUseList.conductorStatus['NODE_INFO'][ nodeID ],
          nodeData = conductorData[ nodeID ],
          $node = $('#' + nodeID ),
          inTerminalID = terminalInOutID( conductorData[ nodeID ]['terminal'], 'in'),
          $inEdge = $('#' + conductorData[ nodeID ]['terminal'][ inTerminalID[0] ].edge );
    
    let endMessage = '';
    
    // 作業結果URLがあれば追加する
    const nodeJump = function(){
      if ( nodeInfo.JUMP ) {
        if ( !$node.find('.node-result').is('.node-jump') ) {
          $node.find('.node-result').addClass('node-jump').attr({
            'data-href': nodeInfo.JUMP
          });
        }
      }
    };
    
    switch( nodeInfo.STATUS ) {
      case '1':
        itaPopup( $node.find('.node-result'), $node.attr('id') );
        return false;
      case '2':
        // 準備中
        nodeJump();
        $node.addClass('ready');
        itaPopup( $node.find('.node-result'), $node.attr('id') );
        $inEdge.attr('data-status', 'running');
        return false;
      case '3':
      case '4':
        // 実行中
        nodeJump();
        $node.removeClass('ready').addClass('running');
        itaPopup( $node.find('.node-result'), $node.attr('id') );
        $inEdge.attr('data-status', 'running');
        return false;
      case '5':
      case '9':
        endMessage = 'DONE';
        break;
      case '7':
        endMessage = 'STOP';
        break;
      case '6':
      case '10':
      case '11':
        endMessage = 'ERROR';
        break;
      case '12':
      case '13':
      case '14':
        endMessage = 'SKIP';
        break;
      case '15':
        endMessage = 'WARN';
        break;
    }
    nodeJump();
    $inEdge.attr('data-status', 'running');
    $node.removeClass('ready').addClass('complete').attr('data-result', nodeInfo.STATUS );
    itaPopup( $node.find('.node-result'), $node.attr('id') );
    $node.find('.node-result').attr('data-result-text', endMessage );
    conductorData[ nodeID ].endStatus = true;
  };
  
  
  // ParallelBranchの状態をチェックする
  const parallelBranchCheck = function( nodeID ) {
    const inTerminal = terminalInOutID( conductorData[ nodeID ].terminal, 'in'),
          tergetNodeID = conductorData[ nodeID ].terminal[ inTerminal[0] ].targetNode;
    // 終了しているかチェックする
    if ( ['5','9','12','13','14'].indexOf( nodeInfo[ tergetNodeID ].STATUS ) !== -1 ) {
      $('#' + nodeID ).addClass('running');
      $('#' + conductorData[ nodeID ].terminal[ inTerminal[0] ].edge ).attr('data-status', 'running');
    }
  };
  
  
  // Pauseの状態をチェックする
  const pauseCheck = function( nodeID ) {
  
    const nodeInfo = conductorUseList.conductorStatus['NODE_INFO'][ nodeID ],
          nodeData = conductorData[ nodeID ],
          $node = $('#' + nodeID ),
          $pauseButton = $node.find('.pause-resume-button'),
          inTerminalID = terminalInOutID( conductorData[ nodeID ]['terminal'], 'in'),
          $inEdge = $('#' + conductorData[ nodeID ]['terminal'][ inTerminalID[0] ].edge );
    
    switch( nodeInfo.STATUS ) {
      case '8':
        $node.addClass('running');
        $inEdge.attr('data-status', 'running');
        conductorData[ nodeID ].endStatus = true;
        $node.find('.pause-status').attr('data-status', 'pause');
        editor.log.set('notice', 'Pause => Node instance : ' + nodeInfo.NODE_INSTANCE_NO );
        
        $pauseButton.prop('disabled', false ).on('click', function() {
          if ( confirm( getSomeMessage("ITABASEC020006",{0:conductorInstanceID})) ) {
            clearTimeout( pollingTimerID );
            $pauseButton.prop('disabled', true ).off();
            $node.find('.pause-status').attr('data-status', 'resume');
            proxy.holdReleaseNodeInstance( nodeInfo.NODE_INSTANCE_NO );
          }
        });
        break;
      case '9':
        $node.addClass('running');
        $inEdge.attr('data-status', 'running');
        conductorData[ nodeID ].endStatus = true;
        $node.find('.pause-status').attr('data-status', 'resume');
        break;            
    }
  };
  
  
  
  const nodeStatusUpdate = function() {
    for ( let nodeID in nodeInfo ) {
      const nodeData = conductorData[ nodeID ];
      // nodeData.endStatusがある場合はスキップ
      if ( !nodeData.endStatus ) {
        switch ( nodeData.type ) {
          case 'conditional-branch':
            condionalBranchCheck( nodeID );
            break;
          case 'parallel-branch':
            parallelBranchCheck( nodeID );
            break;
          case 'status-file-branch':
            statusFileBranch( nodeID );
            break;
          case 'merge':
            parallelMergeCheck( nodeID );
            break;
          case 'movement':
          case 'call':
          case 'call_s':
          case 'end':
            movementCheck( nodeID );
            break;
          case 'start':
            nodeData.endStatus = true;
            $('#' + nodeID ).addClass('running');
            break;
          case 'pause':
            pauseCheck( nodeID );
            break;
        }
      }
    }
  };
  
  // ポーリングタイマー
  const pollingTimer = function() {
    pollingTimerID = setTimeout( function(){
      proxy.printConductorStatus( conductorInstanceID );
    }, intervalTime );
  };
  
  // 実行状況別
  switch( conductorInfo.STATUS_ID ) {
    case '1':
      // 未実行
      $('#scram-instance').show().prop('disabled', false );
      $('#cansel-instance').prop('disabled', true ).hide();
      pollingTimer();
      break;
    case '2':
      // 未実行（予約）
      $('#cansel-instance').show().prop('disabled', false );
      $('#scram-instance').prop('disabled', true ).hide();
      pollingTimer();
      break;
    case '3':
      // 準備中
      $('#scram-instance').show().prop('disabled', true );
    case '4':
      // 実行中
      $('#scram-instance').show().prop('disabled', false );
      $('#cansel-instance').prop('disabled', true ).hide();
      nodeStatusUpdate();
      pollingTimer();
      break;
    case '5':
    case '6':
    case '7':
    case '8':
    case '10':
    case '11':
      // 終了
      $('#scram-instance').prop('disabled', true );
      nodeStatusUpdate();
      $editor.addClass('run-complete');
      break;
    case '9':
      // 予約取消
      break;
  }


  //インスタンスログ表示
  switch( conductorInfo.STATUS_ID ) {
    // 実行中-終了
    case '3':
    case '4':
    case '5':
    case '6':
    case '7':
    case '8':
    case '10':
      let logflgList = [];
      if ( conductorInfo.EXEC_LOG !== "" ){
        //editor-tab-contentsの表示とEXEC_LOGの差分の重複判定
        var execLogMessages = conductorInfo.EXEC_LOG.split('\n');
        $(".editor-log-content").each(function(lineNo, tmpmessage){
            if( $(tmpmessage).text() != "" ){
              //出力済み取得+タグ除去 //[logtype]XXXXX
              var tmphtmlmsg = $(tmpmessage).text();
              tmphtmlmsg = tmphtmlmsg.replace( /\[ERROR\]|\[NOTICE\]|\[WARNING\]/g , "" );

              //execLogMessages重複判定
              execLogMessages.forEach(function(execlog, index) {
                if ( index in logflgList !== true ){
                  logflgList[ index ] = 0;
                }
                if( execlog != '' ){
                  //タグ除去 /[logtype]　XXXXX
                  execlog = execlog.replace( /\[ERROR\] |\[NOTICE\] |\[WARNING\] /g , "" );
                  //重複判定
                  if ( tmphtmlmsg.indexOf( execlog ) != -1) {
                    logflgList[ index ] = 1;
                  }
                }
              });
            }
        });

        //重複無し出力
        execLogMessages.forEach(function(execlog, index) {
          if( execlog != '' ){
            var arrlogtype = execlog.split(' ');
            var logtype = arrlogtype[0].replace( /\[|\]/g , "" );
            var editorogtype = logtype.toLowerCase();
            //[logtype] 削除
            execlog = execlog.replace( '[' + logtype + '] ' , "" );
            //実施中-
            if ( logflgList.length !== 0 ) {
              if( logflgList[ index ] == 0 ){
                editor.log.set( editorogtype ,execlog );                
              }                
            }else{
            //完了時
                editor.log.set( editorogtype ,execlog );  
            }
          }
        });
      }
      break;
  }

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期ノードセット（Start and End）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 基本初期表示
const InitialSetNode = function() {

  newNode('start', 'left', 'center');
  newNode('end', 'right', 'center');
  panelConductorReset();
  $('#conductor-class-id').text('Auto numbering');
  
};

if ( conductorEditorMode !== 'execute') {
  if ( conductorUseList.conductorData !== null ) {
    if ( conductorEditorMode === 'checking') {
      // 作業確認画面更新イベント登録
      $window.on('conductorDrawEnd', function(){
        $window.off('conductorDrawEnd');
        conductorStatusUpdate( 0 );
      });
    }
    loadConductor( JSON.parse( conductorUseList.conductorData ) );
  } else if ( editor.keyCheckLocalStorage('conductor-edit-temp') ) {
    loadConductor( editor.getLocalStorage('conductor-edit-temp'), 'edit');
  } else {
    InitialSetNode();
  }
}

// 登録ボタンなど
$('#editor-footer').on('click', '.editor-menu-button ', function(){
  const $button = $( this ),
        type = $button.attr('data-menu');
        
  nodeDeselect();
  panelChange();
  
  if ( conductorEditorMode === 'execute') {
    if ( type === 'execute') {
      // 実行しますか？
      if ( window.confirm(getSomeMessage("ITABASEC010701")) ) {
        const selectConductorID = $('#conductor-class-id').text(),
              selectOperationID = $('#select-operation-id').text(),
              selectData = $('#bookdatetime').val();
        conductorFooterButtonDisabled( true );
        proxy.conductorExecute( selectConductorID, selectOperationID, selectData, JSON.stringify( conductorData ) );
      }
    }
  } else {
    switch( type ) {
      case 'registration':
        // 登録しますか？
        if ( window.confirm(getSomeMessage("ITAWDCC20101") ) ) {
          conductorFooterButtonDisabled( true );
          proxy.register_execute( JSON.stringify( conductorData ) );
        }
        break;
      case 'edit':
        conductorMode('edit','update');
        break;
      case 'diversion':
        // 流用しますか？
        if ( window.confirm(getSomeMessage("ITABASEC020000") ) ) {
          // 流用する場合は下記の項目はnullに
          conductorData['conductor'].id = null;
          conductorData['conductor'].LUT4U = null;
          conductorData['conductor'].conductor_name = null;
          conductorData['conductor'].note = null;
          conductorMode('edit');
          panelConductorReset();
          $('#conductor-class-id').text('Auto numbering');
          // パラメータの無い履歴を追加する
          history.pushState( null, null, '/default/menu/01_browse.php?no=2100180003');
        }
        break;
      case 'update':
        // 更新しますか？
        if ( window.confirm(getSomeMessage("ITAWDCC20102") )  ) {
          conductorFooterButtonDisabled( true );
          // ID変換失敗したIDを除く
          conductorData['conductor']['ACCESS_AUTH'] = getRoleListValidID( conductorData['conductor']['ACCESS_AUTH'] );         
          // 更新する
          proxy.update_execute( conductorData['conductor'].id, JSON.stringify( conductorData ), conductorData['conductor'].LUT4U );
        }
        break;
      case 'refresh':
        // 再読込しますか？
        if ( window.confirm(getSomeMessage("ITABASEC010101") ) ) {
          proxy.printconductorClass( conductorData['conductor'].id );
        }
        break;
      case 'cancel':
        // キャンセル確認無し
        selectConductor( conductorUseList.conductorData );
        conductorMode('view');
        break;
      case 'cansel-instance':
        //予約取消
        if ( window.confirm(getSomeMessage("ITABASEC020002",{0:conductorInstanceID}))) {
          conductorFooterButtonDisabled( true );
          proxy.bookCancelConducrtorInstance( conductorInstanceID );
        }
        break;
      case 'scram-instance':
        // 強制停止
        if ( window.confirm(getSomeMessage("ITABASEC020004",{0:conductorInstanceID}))) {
          clearTimeout( pollingTimerID );
          conductorFooterButtonDisabled( true );
          proxy.scramConducrtorInstance( conductorInstanceID );
        }
        break;
    }
  }
});

}
