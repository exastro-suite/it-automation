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

// ----------------------------------------------------------------------------------------------------
// Index
// ----------------------------------------------------------------------------------------------------
// 01.エディタ共通
// 02.シンフォニーエディタ
// 03.パネル用画像エディタ
// ----------------------------------------------------------------------------------------------------

$(function(){
'use strict';
/* ---------------------------------------------------------------------------------------------------- *

   01.エディタ共通

 * ---------------------------------------------------------------------------------------------------- */
 
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   関数
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// パラメータ取得用
const getParam = function ( name ) {
  const url = window.location.href,
        regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec( url );
  if( !results ) return null;
  return decodeURIComponent( results[2] );
};

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
}
 
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 初期値
const editorInitial = {
  'version' : '1.0.1',
  'debug' : false,
  'scaling' : 1
};

const debugMode = getParam('debug');
if ( debugMode === "true" ) editorInitial.debug = true;

// エディター値
let editorValue = {
  'scaling' : editorInitial.scaling,
  'oldScaling' : editorInitial.scaling
}

// jQueryオブジェクトをキャッシュ
const $window = $( window ),
      $workspace = $('#workspace'),
      $editorMenu = $workspace.find('.editor-menu'),
      $canvasWindow = $workspace.find('.canvas-window'),
      $canvas = $workspace.find('.canvas'),
      $artBoard = $workspace.find('.art-board'),
      $panelContainer = $('#panel-container'),
      $panelGroup = $workspace.find('.panel-group');

// エディタータイプ（シンフォニー or パネル画像）
const editorType = ( $workspace.attr('data-editor-type') === undefined ) ? 'Unknown' : $workspace.attr('data-editor-type') ;

// キャンバスのサイズ、ポジション
let g_canvasWindow, g_canvas, g_artBoard,
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
// ポジションリセット
const canvasPositionReset = function( duration ) {
    
    if ( duration === undefined ) duration = 0.3;
    
    g_canvasWindow = setSize( $canvasWindow );
    g_canvas = setSize( $canvas );
    g_artBoard = setSize( $artBoard );
    editorValue.scaling = editorInitial.scaling;
    editorValue.oldScaling = editorInitial.scaling;

    g_canvas_p = {
      'x' : Math.round( - ( g_canvas.w / 2 ) + ( g_canvasWindow.w / 2 ) ),
      'y' : Math.round( - ( g_canvas.h / 2 ) + ( g_canvasWindow.h / 2 ) ),
      'cx' : Math.round( - ( g_canvas.w / 2 ) + ( g_canvasWindow.w / 2 ) ),
      'cy' : Math.round( - ( g_canvas.h / 2 ) + ( g_canvasWindow.h / 2 ) )
    };
    g_artBoard_p = {
      'x' : Math.round( ( g_canvas.w / 2 ) - ( g_artBoard.w / 2 ) ),
      'y' : Math.round( ( g_canvas.h / 2 ) - ( g_artBoard.h / 2 ) )
    };
    
    // Start nodeがある場合は基準にする
    if ( $('#node-1.symphony-start').length ) {
      const $start = $('#node-1.symphony-start'),
            adjustPosition = 32; // 調整する端からの距離;
      g_canvas_p.x = -( Number( $start.css('left').replace('px','') ) + g_artBoard_p.x - adjustPosition );
      g_canvas_p.y = -( Number( $start.css('top').replace('px','') ) + g_artBoard_p.y - ( ( g_canvasWindow.h / 2 ) - ( $start.outerHeight() / 2 ) ) );
    }
    
    $canvas.css({
      'left' : g_canvas_p.x,
      'top' : g_canvas_p.y,
      'transform' : 'translate(0,0) scale(' + editorInitial.scaling + ')'
    }).removeClass('small-scale20 small-scale50');
    $artBoard.css({
      'left' : g_artBoard_p.x,
      'top' : g_artBoard_p.y
    });
    
    statusUpdate();
    
    // アニメーションさせる場合は一時的に操作できないようにする
    if ( duration !== 0 ) {
      mode.pause();
      $canvas.css('transition-duration', duration + 's');
      setTimeout( function(){
        $canvas.css('transition-duration', '0s');
        mode.clear();
      }, duration * 1000 );
    }
    
}

// モード変更
let modeType = '';
const modeChange = function( mode, type ) {
    const modeAttr = 'data-mode';
    if ( mode !== 'clear' ) {
      modeType = mode;
      $workspace.attr( modeAttr, mode );
    } else {
      modeType = '';
      $workspace.removeAttr( modeAttr );
    }
    if ( type !== undefined ) {
      $workspace.attr('data-type', type );
    } else {
      $workspace.removeAttr('data-type');
    }
};
// モードチェック
const modeCheck = function( mode ) {
  if ( mode === undefined ) mode = '';
  if ( modeType === mode ) {
    return true;
  } else {
    return false;
  }
};
const mode = {
  'pause' : function() { modeChange('editor-pause'); },
  'canvasMove' : function() { modeChange('canvas-move'); },
  'nodeDrag' : function() { modeChange('node-drag'); },
  'caseDrag' : function() { modeChange('case-drag'); },
  'nodeMove' : function( edgeFlag ) {
    if ( edgeFlag === undefined ) edgeFlag = true;
    if ( edgeFlag === true ) {
      modeChange('node-move');
    } else {
      modeChange('node-noedge-move');
    }
  },
  'nodeSelect' : function() { modeChange('node-select'); },
  'edgeInConnect' : function( type ) { modeChange('edge-in-connect', type ); },
  'edgeOutConnect' : function( type ) { modeChange('edge-out-connect', type ); },
  'clear' : function() { modeChange('clear'); }
};

// ブラウザ判定
const ua = window.navigator.userAgent.toLowerCase();
let browser = '';
if ( ua.indexOf('trident/7') !== -1 || ua.indexOf('msie') !== -1 ) {
  browser = 'ie';
} else if ( ua.indexOf('edge') !== -1 ) {
  browser = 'edge';
}

// CSS filter判定
const $filterCheck = $('<div />');
let filterFlg = false;
if ( $filterCheck.css('filter','blur(1px)') !== undefined ) filterFlg = true;

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   右クリックでキャンバスを移動する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$canvasWindow.on({
    'mousedown.canvas': function( e ) {

      if ( e.buttons === 2 ) {
      
        e.preventDefault();

        const mouseDownPositionX = e.pageX,
              mouseDownPositionY = e.pageY;
              
        let moveX = 0,
            moveY = 0;
            
        mode.canvasMove();

        $window.on({
          'mousemove.canvas': function( e ){

            moveX = e.pageX - mouseDownPositionX;
            moveY = e.pageY - mouseDownPositionY;
            
            $statusViewX.text( Math.round( g_canvas_p.x - g_canvas_p.cx + moveX ) + 'px' );
            $statusViewY.text( Math.round( g_canvas_p.y - g_canvas_p.cy + moveY ) + 'px' );
            $statusMoveX.text( moveX + 'px' );
            $statusMoveY.text( moveY + 'px' );
            
            $canvas.css({
              'transform' : 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(' + editorValue.scaling + ')'
            });

          },
          'contextmenu.canvas': function( e ) {
            if ( editorInitial.debug === false ) e.preventDefault();
            $( this ).off('contextmenu.canvas');
          },
          'mouseup.canvas': function(){
            $( this ).off('mousemove.canvas mouseup.canvas');
            g_canvas_p.x = g_canvas_p.x + moveX;
            g_canvas_p.y = g_canvas_p.y + moveY;
            statusUpdate();
            $canvas.css({
              'left' : g_canvas_p.x,
              'top' : g_canvas_p.y,
              'transform' : 'translate(0,0) scale(' + editorValue.scaling + ')'
            });
            mode.clear();
          }
        });
        
      }
    },
    'contextmenu': function( e ) {
      // コンテキストメニューは表示しない
      if ( editorInitial.debug === false ) e.preventDefault();
    }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   タブ切り替え
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$panelGroup.each( function() {

    const $panelContainer = $( this ),
          $panel = $panelContainer.find('.panel'),
          $tab = $panelContainer.find('.panel-tab-li');

    $panel.hide().eq(0).show();
    $tab.eq(0).addClass('selected');

    $tab.on('click', function() {
      const $clickTab = $( this ),
            openTabName = $clickTab.attr('data-tab-nanme');

      $panel.hide();
      $panelContainer.find('.selected').removeClass('selected');

      $clickTab.addClass('selected');
      $panelContainer.find('#' + openTabName ).show();

    });

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キャンバスの拡縮
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const canvasScaling = function( zoomType, positionX, positionY ){

    const scalingNum = 0.1,
          scalingMax = 5,
          scalingMin = 0.1;

    let scaling = editorValue.scaling;

    if ( positionX === undefined ) positionX = g_canvas_p.x / 2;
    if ( positionY === undefined ) positionY = g_canvas_p.y / 2;

    if ( zoomType === 'in') {
      scaling = ( scaling * 10 + scalingNum * 10 ) / 10;
    } else if ( zoomType === 'out') {
      scaling = ( scaling * 10 - scalingNum * 10 ) / 10;
    }

    if ( scaling > scalingMax ) scaling = scalingMax;
    if ( scaling < scalingMin ) scaling = scalingMin;

    if ( scaling !== editorValue.oldScaling ) {
      const commonX = ( ( g_canvas.w * scaling ) - ( g_canvas.w * editorValue.oldScaling ) ) / 2,
            commonY = ( ( g_canvas.h * scaling ) - ( g_canvas.h * editorValue.oldScaling ) ) / 2,
            adjustX = ( ( g_canvas.w / 2 ) - positionX ) * scalingNum,
            adjustY = ( ( g_canvas.h / 2 ) - positionY ) * scalingNum;
            
      g_canvas_p.cx = Math.round( g_canvas_p.cx - commonX );
      g_canvas_p.cy = Math.round( g_canvas_p.cy - commonY );

      if ( zoomType === 'in') {
        g_canvas_p.x = Math.round( g_canvas_p.x - commonX + adjustX );
        g_canvas_p.y = Math.round( g_canvas_p.y - commonY + adjustY );
      } else if ( zoomType === 'out') {
        g_canvas_p.x = Math.round( g_canvas_p.x - commonX - adjustX );
        g_canvas_p.y = Math.round( g_canvas_p.y - commonY - adjustY );
      }

      $canvas.css({
        'left' : g_canvas_p.x,
        'top' : g_canvas_p.y,
        'transform' : 'scale(' + scaling + ')'
      }).removeClass('small-scale20 small-scale50');
      
      if ( scaling <= 0.5 ) {
        if ( scaling <= 0.2 ) {
          $canvas.addClass('small-scale20');
        } else {
          $canvas.addClass('small-scale50');
        }
      }      
      
      editorValue.scaling = scaling;
      editorValue.oldScaling = scaling;
      statusUpdate();
    }
  
}

// マウスホイールで拡縮
const mousewheelevent = ('onwheel' in document ) ? 'wheel' : ('onmousewheel' in document ) ? 'mousewheel' : 'DOMMouseScroll';
$canvasWindow.on( mousewheelevent, function( e ){

    e.preventDefault();
    
    if ( e.buttons === 0 ) {

      const mousePositionX = ( e.pageX - $( this ).offset().left - g_canvas_p.x ) / editorValue.scaling,
            mousePositionY = ( e.pageY - $( this ).offset().top - g_canvas_p.y ) / editorValue.scaling,
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
//   キャンバスステータス
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const $statusScale = $('#canvas-status-scale'),
      $statusViewX = $('#canvas-status-view-x'),
      $statusViewY = $('#canvas-status-view-y'),
      $statusMoveX = $('#canvas-status-move-x'),
      $statusMoveY = $('#canvas-status-move-y');

const statusUpdate = function() {

    $statusScale.text( Math.round( editorValue.scaling * 100 ) + '%' );
    $statusViewX.text( Math.round( g_canvas_p.x - g_canvas_p.cx ) + 'px' );
    $statusViewY.text( Math.round( g_canvas_p.y - g_canvas_p.cy ) + 'px' );
    $statusMoveX.text('0px');
    $statusMoveY.text('0px');
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   画面フルスクリーン
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// フルスクリーンチェック
const fullScreenCheck = function() {
  if (
        ( document.fullScreenElement !== undefined && document.fullScreenElement === null ) ||
        ( document.msFullscreenElement !== undefined && document.msFullscreenElement === null ) ||
        ( document.mozFullScreen !== undefined && !document.mozFullScreen ) || 
        ( document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen )
      )
  {
    return false;
  } else {
    return true;
  }
}
// フルスクリーン切り替え
const toggleFullScreen = function( elem ) {
  if ( !fullScreenCheck() ) {
    if ( elem.requestFullScreen ) {
      elem.requestFullScreen();
    } else if ( elem.mozRequestFullScreen ) {
      elem.mozRequestFullScreen();
    } else if ( elem.webkitRequestFullScreen ) {
      elem.webkitRequestFullScreen( Element.ALLOW_KEYBOARD_INPUT );
    } else if (elem.msRequestFullscreen) {
      elem.msRequestFullscreen();
    }
  } else {
    if ( document.cancelFullScreen ) {
      document.cancelFullScreen();
    } else if ( document.mozCancelFullScreen ) {
      document.mozCancelFullScreen();
    } else if ( document.webkitCancelFullScreen ) {
      document.webkitCancelFullScreen();
    } else if ( document.msExitFullscreen ) {
      document.msExitFullscreen();
    }
  }
}
// フルスクリーンイベント
document.onfullscreenchange = document.onmozfullscreenchange = document.onwebkitfullscreenchange = document.onmsfullscreenchange = function () {
	if( fullScreenCheck() ){
    $workspace.addClass('fullscreen');
  } else {
    $workspace.removeClass('fullscreen');
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$editorMenu.on('click', 'button', function(){

    const $button = $( this ),
          buttonType = $button.attr('data-menu'),
          buttonDisabledTime = 300;

    // 一定時間ボタンを押せなくする
    $button.prop('disabled', true );

    switch ( buttonType ) {
      case 'view-reset':
        canvasPositionReset();
        break;
      case 'full-screen':
        toggleFullScreen( $workspace.get(0) )
        break;
    }
    
    // buttonDisabledTime ms 後に復活
    if ( buttonType !== 'node-delete' ) {
      setTimeout( function(){
        $button.prop('disabled', false );
      }, buttonDisabledTime );
    }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Window リサイズでキャンバスリセット
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const reiszeEndTime = 200;
let resizeTimerID;
$window.on('resize.editor', function(){

    clearTimeout( resizeTimerID );

    resizeTimerID = setTimeout( function(){
      canvasPositionReset();
    }, reiszeEndTime );

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   エディターのどこにいるか
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$canvasWindow.add( $panelContainer ).on({
  'mouseenter' : function(){ $( this ).addClass('hover'); },
  'mouseleave' : function(){ $( this ).removeClass('hover'); }
});


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   詳細ステータスを表示する（デバッグモード）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
if ( editorInitial.debug === true ) {

    $('#canvas-debug').show();

    $window.on({
      'mousemove': function( e ){
        const mousePositionX = Math.round( ( e.pageX - ( g_artBoard_p.x * editorValue.scaling ) - $canvasWindow.offset().left - g_canvas_p.x ) / editorValue.scaling ),
              mousePositionY = Math.round( ( e.pageY - ( g_artBoard_p.y * editorValue.scaling ) - $canvasWindow.offset().top - g_canvas_p.y ) / editorValue.scaling ),
              viewMouseX = e.pageX - g_canvasWindow.l,
              viewMouseY = e.pageY - g_canvasWindow.t;
        let position = ''
          + 'Canvas Mouse X : ' + mousePositionX + 'px / Canvas Mouse Y : ' + mousePositionY + 'px<br>'
          + 'View Mouse X : ' + viewMouseX + 'px / View Mouse Y : ' + viewMouseY + 'px<br>'
          + 'Page Mouse X : ' + e.pageX + 'px / Page Mouse Y : ' + e.pageY + 'px<br>'
          + 'Canvas offset X : ' + g_canvasWindow.l + 'px / Canvas offset Y : ' + g_canvasWindow.t + 'px<br>'
          + 'Canvas Position X : ' + g_canvas_p.x + 'px / Canvas Position Y : ' + g_canvas_p.y + 'px<br>'
          + 'Art Board Position X : ' + g_artBoard_p.x + 'px / Art Board Position Y : ' + g_artBoard_p.y + 'px<br>';
        $('#canvas-debug-position').html( position );
        $('#canvas-debug-scale').html( 'Canvas Scale : ' + editorValue.scaling );
      }
    });
}





if ( editorType === 'symphony' ) {
/* ---------------------------------------------------------------------------------------------------- *

   02.シンフォニーエディタ

 * ---------------------------------------------------------------------------------------------------- */

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   シンフォニーエディタ初期設定初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 初期値
const panelImageValue = {
  'workspaceHeight' : 670,
  'canvasWidth' : 16400,
  'canvasHeight' : 16400,
  'artboradWidth' : 16000,
  'artboradHeight' : 16000
};

// 終了ステータス
const endStatus = {
  '9' : ['done','正常終了'],
  '6' : ['fail','異常終了'],
  '7' : ['stop','緊急停止'],
  '10' : ['error','準備エラー'],
  '11' : ['error','想定外エラー'],
  '14' : ['skip','Skip終了'],
  '-1' : ['other','その他'],
};

// jQueryオブジェクトをキャッシュ
const $nodeList = $panelGroup.find('.node-table');

// 各種サイズをセット
$workspace.css({
  'height' : panelImageValue.workspaceHeight
});
$canvas.css({
  'width' : panelImageValue.canvasWidth,
  'height' : panelImageValue.canvasHeight
});
$artBoard.css({
  'width' : panelImageValue.artboradWidth,
  'height' : panelImageValue.artboradHeight
});
canvasPositionReset( 0 );

// ID 連番用
let g_NodeCounter = 1,
    g_TerminalCounter = 1,
    g_LineCounter = 1;

// 選択中のNode ID
let g_selectedNodeID = [];

// JSONオブジェクト
let symphonyJSON = new Object();

// Undo Redo用ヒストリー
let symphonyHistory  = [],
    historyCounter = 0;

symphonyJSON['config'] = {
  'editorVersion' : editorInitial.version
}
symphonyJSON['symphony'] = {
  'note' : ''
};

// 未対応処理
if ( browser === 'ie' ) {
  $('.editor-not-available').show();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Undo / Redo
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const history = {

'add' : function( type, node ) {
  const historyObj = new Object;
  Object.assign(historyObj, symphonyJSON[node]);
  symphonyHistory[ historyCounter++ ] = historyObj;
  console.log( symphonyHistory );
},
'back' : function() {},
'clear' : function() {
  symphonyHistory  = [];
  historyCounter = 0;
}

}




////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   シンフォニーエディタ用メニュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$editorMenu.on('click', 'button', function(){

    const buttonType = $( this ).attr('data-menu');
          
    switch ( buttonType ) {
      case 'symphony-register':
        break;
      case 'node-delete':
        nodeRemove( g_selectedNodeID );
        break;
    }

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

$svgArea.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );
$selectArea.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );

// 座標マーカー（デバッグ用）
if ( editorInitial.debug === true ) {
  const $testSVG = $( document.createElementNS( xmlns, 'svg') ),
        $testCircle = $( document.createElementNS( xmlns, 'circle') ),
        $testPath = $( document.createElementNS( xmlns, 'path') );
  
  $testSVG.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );
  $testSVG.attr({
    'id' : 'svg-test',
    'width' : g_artBoard.w,
    'height' : g_artBoard.h
  });
  $testCircle.attr('class', 'test-circle');
  $testPath.attr('class', 'test-path');
  $artBoard.prepend( $testSVG.append( $testPath, $testPath.clone(), $testPath.clone(),
  $testCircle, $testCircle.clone(), $testCircle.clone(), $testCircle.clone(), $testCircle.clone() ) );
}

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

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）削除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const removeEdge = function( edgeID, removeSpeed ) {
  
  if ( removeSpeed === undefined ) removeSpeed = 200;
  
  const $edge = $('#' + edgeID ),
        edge = symphonyJSON[ edgeID ];
  
  // 結線情報を削除
  if ( 'inTerminal' in edge ) {
    $('#' + edge.inTerminal ).removeClass('connected');
    delete symphonyJSON[ edge.inNode ].terminal[ edge.inTerminal ].edge;
    delete symphonyJSON[ edge.inNode ].terminal[ edge.inTerminal ].targetNode;
  }
  if ( 'outTerminal' in edge ) {
    $('#' + edge.outTerminal ).removeClass('connected');
    delete symphonyJSON[ edge.outNode ].terminal[ edge.outTerminal ].edge;
    delete symphonyJSON[ edge.outNode ].terminal[ edge.outTerminal ].targetNode;
  }
  delete symphonyJSON[ edgeID ];
  
  if ( editorInitial.debug === true ) {
    window.console.log( 'REMOVE EDGE ID : ' + edgeID );
  }
  
  $edge.animate({'opacity' : 0 }, removeSpeed, function(){
    $( this ).remove();            
  });
};
$svgArea.on({
  'click' : function(){
    removeEdge( $( this ).closest('.svg-group').attr('id') );
  } 
}, '.svg-select-line');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const newSVG = function() {
    // SVG ID
    const svgID = 'line-' + g_LineCounter;
    g_LineCounter++;

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

    const inNodeID = symphonyJSON[ edgeID ].inNode,
          outNodeID = symphonyJSON[ edgeID ].outNode;
                
    const inTerminal = symphonyJSON[ inNodeID ].terminal[ symphonyJSON[ edgeID ].inTerminal ],
          outTerminal = symphonyJSON[ outNodeID ].terminal[ symphonyJSON[ edgeID ].outTerminal ];
                
    const inX = inTerminal.x,
          inY = inTerminal.y,
          outX = outTerminal.x,
          outY = outTerminal.y;
                      
    $('#' + edgeID ).find('path').attr('d', svgDrawPosition( outX, outY, inX, inY ) );
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）ホバー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$canvasWindow.on({
  'mouseenter' : function(){
    $( this ).attr('class','svg-group hover');
    if ( modeCheck('node-noedge-move') || modeCheck('node-drag') ) {
      $workspace.find('.node.current').css('opacity', .5 );
    }
  },
  'mouseleave' : function(){
    $( this ).attr('class','svg-group');
    if ( modeCheck('node-noedge-move') || modeCheck('node-drag') ) {
      $workspace.find('.node.current').css('opacity', 1 );
    }
  }
},'.svg-group');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   線（Edge,Line）ホバー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const connectEdgeUpdate = function( nodeID ) {
    $('#' + nodeID ).find('.connected').each( function() {
      const terminalID = $( this ).attr('id');
      if ( 'edge' in symphonyJSON[ nodeID ].terminal[ terminalID ] ) {
        const edgeID = symphonyJSON[ nodeID ].terminal[ terminalID ].edge;
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
      
      // path確認用
      /*
      if ( editorInitial.debug === true ) {
        $testSVG = $('#svg-test');
        
        // curveto 座標反転
        const centerRangeX = Math.abs( centerX - curvetoStartX ),
              centerRangeY = Math.abs( centerY - curvetoStartY2 ),
              rX = ( centerX < curvetoStartX ) ? centerX - centerRangeX: centerX + centerRangeX,
              rY = ( centerY < curvetoStartY2 ) ? centerY - centerRangeY: centerY + centerRangeY;
        
        $testSVG.find('.test-circle').eq(0).attr({'r':4,'cx':curvetoStartX,'cy':curvetoStartY1});
        $testSVG.find('.test-circle').eq(1).attr({'r':4,'cx':curvetoStartX,'cy':curvetoStartY2});
        $testSVG.find('.test-circle').eq(2).attr({'r':4,'cx':centerX,'cy':centerY});
        $testSVG.find('.test-circle').eq(4).attr({'r':4,'cx':rX,'cy':rY});
        
        $testSVG.find('.test-circle').eq(3).attr({'r':4,'cx':endStraightLineX - curvetoRangeX,'cy':curvetoEndY1});
        
        $testSVG.find('.test-path').eq(0).attr({'d': svgOrder('M',[[startStraightLineX,startY],[curvetoStartX,curvetoStartY1]])});
        $testSVG.find('.test-path').eq(1).attr({'d': svgOrder('M',[[curvetoStartX,curvetoStartY2],[rX,rY]])});
        $testSVG.find('.test-path').eq(2).attr({'d': svgOrder('M',[[endStraightLineX - curvetoRangeX,curvetoEndY1],[endStraightLineX,endY]])});
      }
      */
      
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

        // path確認用
        /*
        if ( editorInitial.debug === true ) {
          $testSVG = $('#svg-test');

          // curveto 座標反転
          const centerRangeX = Math.abs( centerX - curvetoQX ),
                centerRangeY = Math.abs( centerY - startY ),
                rX = ( centerX < curvetoQX ) ? centerX - centerRangeX: centerX + centerRangeX,
                rY = ( centerY < startY ) ? centerY - centerRangeY: centerY + centerRangeY;

          $testSVG.find('.test-circle').eq(0).attr({'r':4,'cx':0,'cy':0});
          $testSVG.find('.test-circle').eq(1).attr({'r':4,'cx':curvetoQX,'cy':startY});
          $testSVG.find('.test-circle').eq(2).attr({'r':4,'cx':centerX,'cy':centerY});
          $testSVG.find('.test-circle').eq(4).attr({'r':4,'cx':rX,'cy':rY});

          $testSVG.find('.test-circle').eq(3).attr({'r':4,'cx':0,'cy':0});

          $testSVG.find('.test-path').eq(0).attr({'d': svgOrder('M',[[startStraightLineX,startY],[curvetoQX,startY]])});
          $testSVG.find('.test-path').eq(1).attr({'d': svgOrder('M',[[endStraightLineX,endY],[rX,rY]])});
          $testSVG.find('.test-path').eq(2).attr({'d': svgOrder('M',[[0,0],[0,0]])});
        }
        */

        drawPositionArray = [ moveStart, startLine, curvetoQ, centerX + ',' + centerY, endLine, moveEnd ];
      }
    }
    
    if ( editorInitial.debug === true ) {
      $('#canvas-debug-svg').html( 'X Range :' + xRange + ' / Y Range:' + yRange + '<br>' + drawPositionArray.join(' ') );
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

      symphonyJSON[ nodeID ].x = x;
      symphonyJSON[ nodeID ].y = y;
      symphonyJSON[ nodeID ].w = w;
      symphonyJSON[ nodeID ].h = h;
    
    }
    
    // ターミナルの位置情報更新
    let branchCount = 1;
    $node.find('.node-terminal').each( function() {
      const $terminal = $( this ),
            terminalID = $terminal.attr('id'),
            terminalWidth = $terminal.outerWidth() / 2,
            terminalHeight = $terminal.outerHeight() / 2;
      
      // 未定義なら初期化
      if ( symphonyJSON[ nodeID ].terminal[ terminalID ] === undefined ) {
        symphonyJSON[ nodeID ].terminal[ terminalID ] = {};
        if ( $terminal.is('.node-in') ) {
          symphonyJSON[ nodeID ].terminal[ terminalID ].type = 'in';
        } else {
          symphonyJSON[ nodeID ].terminal[ terminalID ].type = 'out';
        }
      }
      
      // 分岐ノードの情報をセット
      if( ( symphonyJSON[ nodeID ].type === 'conditional-branch' &&
          symphonyJSON[ nodeID ].terminal[ terminalID ].type === 'out' ) ||
          ( symphonyJSON[ nodeID ].type === 'merge' &&
          symphonyJSON[ nodeID ].terminal[ terminalID ].type === 'in' ) ) {
        let branchArray = [];
        $terminal.prev('.node-body').find('li').each( function(){
          branchArray.push( $( this ).attr('data-end-status') );
        });
        symphonyJSON[ nodeID ].terminal[ terminalID ].condition = branchArray;
        symphonyJSON[ nodeID ].terminal[ terminalID ].case = branchCount;
        branchCount++;
      }
      
      symphonyJSON[ nodeID ].terminal[ terminalID ].id = terminalID;
      symphonyJSON[ nodeID ].terminal[ terminalID ].x =
        Math.round( symphonyJSON[ nodeID ].x + $terminal.position().left / editorValue.scaling + terminalWidth );
      symphonyJSON[ nodeID ].terminal[ terminalID ].y =
        Math.round( symphonyJSON[ nodeID ].y + $terminal.position().top / editorValue.scaling + terminalHeight );

    });
    
    if ( editorInitial.debug === true ) {
      window.console.log( symphonyJSON );
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const initialNode = function( id, type, name ){

    const nodeID = 'node-' + g_NodeCounter,
          terminalID = 'terminal-';
    g_NodeCounter++;

    let nodeClass = '',
        orchestratorName = '',
        inEdgeClass = 'node-terminal',
        outEdgeClass = 'node-terminal',
        nodeCircle = 'n',
        nodeType = '',
        tempType = 'common';

    // 基本パターン分岐
    switch ( type ) {
      case 'Ansible Legacy':
        nodeClass = 'node-ansible-legacy';
        orchestratorName = 'Ansible Legacy';
        nodeType = 'movement';
        break;
      case 'Ansible Pioneer':
        nodeClass = 'node-ansible-pioneer';
        orchestratorName = 'Ansible Pioneer';
        nodeType = 'movement';
        break;
      case 'Ansible Legacy Role':
        nodeClass = 'node-ansible-legacy-role';
        orchestratorName = 'Ansible Legacy Role';
        nodeType = 'movement';
        break;
      case 'Cobbler':
        nodeClass = 'node-cobbler';
        orchestratorName = 'Cobbler';
        nodeType = 'movement';
        break;
      case 'DSC':
        nodeClass = 'node-dsc';
        orchestratorName = 'DSC';
        nodeType = 'movement';
        break;
      case 'symphony-start':
        name = 'Start';
        nodeCircle = 'S';
        nodeClass = 'symphony-start';
        orchestratorName = 'Symphony';
        inEdgeClass = 'node-cap';
        nodeType = 'start';
        break;
      case 'symphony-end':
        name = 'End';
        nodeCircle = 'E';
        nodeClass = 'symphony-end';
        orchestratorName = 'Symphony';
        outEdgeClass = 'node-cap';
        nodeType = 'end';
        break;
      case 'conditional-branch':
        name = 'Conditional';
        nodeClass = 'function function-conditional';
        orchestratorName = 'Branch';
        tempType = 'conditional-branch';
        nodeType = 'conditional-branch';
        break;
      case 'parallel-branch':
        name = 'Parallel';
        nodeClass = 'function function-parallel';
        orchestratorName = 'Branch';
        tempType = 'parallel-branch';
        nodeType = 'parallel-branch';
        break;
      case 'merge':
        name = 'Merge';
        nodeClass = 'function function-merge';
        tempType = 'merge';
        nodeType = 'merge';
        break;
      case 'symphony-pause':
        name = 'Pause';
        nodeClass = 'function function-pause';
        tempType = 'pause';
        nodeType = 'pause';
        break;
      case 'symphony-call':
        name = '{Not selected}';
        nodeCircle = 'C';
        orchestratorName = 'Symphony call';
        nodeClass = 'symphony-call';
        tempType = 'call';
        nodeType = 'call';
        break;
      case 'blank-node':
        name = '';
        nodeClass = 'function function-blank';
        tempType = 'blank';
        nodeType = 'blank';
        break;
      default:
    }
    
    let nodeHTML = '';
    
    const noteHTML = '<div class="node-note"><div class="node-note-inner"><p></p></div></div>',
          statusHTML = '<div class="node-status"><div class="node-status"><p></p></div></div>',
          skipHTML = '<div class="node-skip"><input type="checkbox"><label>Skip</label></div>';
    
    switch ( tempType ) {
      case 'common':
        nodeHTML = ''
        + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
          + '<div class="node-main">'
            + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="' + inEdgeClass + ' node-in"><span class="hole"><span></span></span></div>'
            + '<div class="node-body">'
              + '<div class="node-circle"><span class="node-gem">' + nodeCircle + '</span><span class="node-running"></span></div>'
              + '<div class="node-type"><span>' + orchestratorName + '</span></div>'
              + '<div class="node-name"><span>' + name + '</span></div>'
            + '</div>'
            + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="' + outEdgeClass + ' node-out"><span class="hole"><span></span></span></div>'
          + '</div>'
          + noteHTML + statusHTML + skipHTML
        + '</div>';
        g_TerminalCounter += 2;
        break;
      case 'call':
        nodeHTML = ''
        + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
          + '<div class="node-main">'
            + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="' + inEdgeClass + ' node-in"><span class="hole"><span></span></span></div>'
            + '<div class="node-body">'
              + '<div class="node-circle"><span class="node-gem">' + nodeCircle + '</span><span class="node-running"></span></div>'
              + '<div class="node-type"><span>' + orchestratorName + '</span></div>'
              + '<div class="node-name"><span class="symphony-name-wrap"><span class="symphony-name">' + name + '</span></span></div>'
            + '</div>'
            + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="' + outEdgeClass + ' node-out"><span class="hole"><span></span></span></div>'
          + '</div>'
          + noteHTML + statusHTML + skipHTML
        + '</div>';
        g_TerminalCounter += 2;
        break;
      case 'conditional-branch':
        nodeHTML = ''
          + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
            + '<div class="node-main">'
              + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="node-terminal node-in">'
                + '<span class="hole"><span></span></span>'
              + '</div>'
              + '<div class="node-body">'
                + '<div class="node-type"><span>' + orchestratorName + '</span></div>'
                + '<div class="node-name"><span>' + name + '</span></div>'
              + '</div>'
              + '<div class="branch-cap branch-out"></div>'
            + '</div>'
            + '<div class="branch-line"><svg></svg></div>'
            + '<div class="node-branch">'
              + '<div class="node-sub">'
                + '<div class="branch-cap branch-in"></div>'
                + '<div class="node-body">'
                  + '<div class="branch-type"><ul><li class="done" data-end-status="9">' + endStatus['9'][1] + '</li></ul></div>'
                + '</div>'
                + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="node-terminal node-out">'
                  + '<span class="hole"><span></span></span>'
                + '</div>'
              + '</div>'
              + '<div class="node-sub default">'
                + '<div class="branch-cap branch-in"></div>'
                + '<div class="node-body">'
                  + '<div class="branch-type"><ul><li class="default" data-end-status="-1">Other</li></ul></div>'
                + '</div>'
                + '<div id="' + ( terminalID  + ( g_TerminalCounter + 2 ) ) + '" class="node-terminal node-out">'
                  + '<span class="hole"><span></span></span>'
                + '</div>'
              + '</div>'
            + '</div>'
            + noteHTML
          + '</div>';
          g_TerminalCounter += 3;
          break;
      case 'parallel-branch':
        nodeHTML = ''
          + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
            + '<div class="node-main">'
              + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="node-terminal node-in">'
                + '<span class="hole"><span></span></span>'
              + '</div>'
              + '<div class="branch-cap branch-out"></div>'
            + '</div>'
            + '<div class="branch-line"><svg></svg></div>'
            + '<div class="node-branch">'
              + '<div class="node-sub">'
                + '<div class="branch-cap branch-in"></div>'
                + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="node-terminal node-out">'
                  + '<span class="hole"><span></span></span>'
                + '</div>'
              + '</div>'
              + '<div class="node-sub">'
                + '<div class="branch-cap branch-in"></div>'
                + '<div id="' + ( terminalID  + ( g_TerminalCounter + 2 ) ) + '" class="node-terminal node-out">'
                  + '<span class="hole"><span></span></span>'
                + '</div>'
              + '</div>'
            + '</div>'
            + noteHTML
          + '</div>';
          g_TerminalCounter += 3;
          break;
      case 'merge':
        nodeHTML = ''
          + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
            + '<div class="node-merge">'
              + '<div class="node-sub">'
                + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="node-terminal node-in">'
                  + '<span class="hole"><span></span></span>'
                + '</div>'
                + '<div class="node-body">'
                  + '<div class="merge-status"><span class="standby">Standby</span></div>'
                + '</div>'
                + '<div class="merge-cap merge-out"></div>'
              + '</div>'
              + '<div class="node-sub">'
                + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="node-terminal node-in">'
                  + '<span class="hole"><span></span></span>'
                + '</div>'
                + '<div class="node-body">'
                  + '<div class="merge-status"><span class="standby">Standby</span></div>'
                + '</div>'
                + '<div class="merge-cap merge-out"></div>'
              + '</div>'
            + '</div>'
            + '<div class="branch-line"><svg></svg></div>'
            + '<div class="node-main">'
              + '<div class="merge-cap merge-in"></div>'
              + '<div id="' + ( terminalID  + ( g_TerminalCounter + 2 ) ) + '" class="node-terminal node-out">'
                + '<span class="hole"><span></span></span>'
              + '</div>'
            + '</div>'
            + noteHTML
          + '</div>';
          g_TerminalCounter += 3;
          break;
        case 'pause':
        nodeHTML = ''
        + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
          + '<div class="node-main">'
            + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="' + inEdgeClass + ' node-in"><span class="hole"><span></span></span></div>'
            + '<div class="node-body">'
              + '<div class="node-name"><span>' + name + '</span></div>'
            + '</div>'
            + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="' + outEdgeClass + ' node-out"><span class="hole"><span></span></span></div>'
          + '</div>'
          + noteHTML + statusHTML + skipHTML
        + '</div>';
        g_TerminalCounter += 2;
        break;
        case 'blank':
        nodeHTML = ''
        + '<div id="' + nodeID + '" class="node ' + nodeClass + '">'
          + '<div class="node-main">'
            + '<div id="' + ( terminalID  + g_TerminalCounter ) + '" class="' + inEdgeClass + ' node-in"><span class="hole"><span></span></span></div>'
            + '<div class="node-body">'
              + '<div class="node-name"><span>' + name + '</span></div>'
            + '</div>'
            + '<div id="' + ( terminalID  + ( g_TerminalCounter + 1 ) ) + '" class="' + outEdgeClass + ' node-out"><span class="hole"><span></span></span></div>'
          + '</div>'
          + noteHTML
        + '</div>';
        g_TerminalCounter += 2;
        break;
        default:
      }
    
    symphonyJSON[ nodeID ] = {
      'type' : nodeType,
      'id' : nodeID,
      'terminal' : {}
    }

    return $( nodeHTML );
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   分岐線追加・更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const branchLine = function( nodeID, setMode ) {

  const branchType = symphonyJSON[ nodeID ].type;

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
    $branchLine.attr('class','branch-line');
    $branchInLine.attr('class','branch-in-line');
    $branchOutLine.attr('class','branch-out-line');
    $branchBackLine.attr('class','branch-back-line');
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
const addBranch = function( nodeID ) {
    const $branchNode = $('#' + nodeID );
    let branchType = '',
        nodeHTML = '',
        panelHTML = '';
    
    if ( $branchNode.is('.function-conditional') ) {
      branchType = 'conditional';
      nodeHTML = ''
        + '<div class="node-sub">'
          + '<div class="branch-cap branch-in"></div>'
          + '<div class="node-body">'
            + '<div class="branch-type"><ul></ul></div>'
          + '</div>'
          + '<div id="terminal-' + g_TerminalCounter + '" class="node-terminal node-out">'
            + '<span class="hole"><span></span></span>'
          + '</div>'
        + '</div>';
      panelHTML = ''
        + '<tr>'
          + '<th class="property-th">Case {{n}} :</th>'
          + '<td class="property-td"><ul class="branch-case"></ul></td>'
        + '</tr>';
    } else if ( $branchNode.is('.function-parallel') ) {
      branchType = 'parallel';
      nodeHTML = ''
        + '<div class="node-sub">'
          + '<div class="branch-cap branch-in"></div>'
          + '<div id="terminal-' + g_TerminalCounter + '" class="node-terminal node-out">'
            + '<span class="hole"><span></span></span>'
          + '</div>'
        + '</div>';
      panelHTML = ''
        + '<tr>'
          + '<th class="property-th">Case :</th>'
          + '<td class="property-td"><ul class="branch-case"></ul></td>'
        + '</tr>';
    } else if ( $branchNode.is('.function-merge') ) {
      branchType = 'merge';
      nodeHTML = ''
        + '<div class="node-sub">'
          + '<div id="terminal-' + g_TerminalCounter + '" class="node-terminal node-in">'
            + '<span class="hole"><span></span></span>'
          + '</div>'
          + '<div class="node-body">'
            + '<div class="merge-status"><span class="standby">Standby</span></div>'
          + '</div>'
          + '<div class="merge-cap merge-out"></div>'
        + '</div>'
    }
    
    if ( branchType !== '' ) {
      // 条件分岐は最大6分岐までにする
      const branchLength = $branchNode.find('.node-sub').length;
      if ( !( branchType === 'conditional' && branchLength > 6 ) ) {
        g_TerminalCounter++;

        if ( branchType === 'conditional' ) {
          $branchNode.find('.node-sub.default').before( nodeHTML );
          panelHTML = panelHTML.replace('{{n}}', branchLength );
          $('#branch-case-list').find('tbody').append( panelHTML );
        } else if ( branchType === 'parallel' ) {
          $branchNode.find('.node-branch').append( nodeHTML );
        } else {
          $branchNode.find('.node-' + branchType ).append( nodeHTML );
        }
        nodeSet( $branchNode );
        branchLine( nodeID );
        connectEdgeUpdate( nodeID );
      } else {
        editorMessage('INFO','MAX 6 Branch.')
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
    } else if ( $branchNode.is('.function-merge') ) {
      branchType = 'merge';
    }
    
    if ( branchType !== '' ) {
      // 分岐は最低２つまで
      if ( $branchNode.find('.node-sub').length > 2 ) {
      
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
        if ( $targetTerminal.find('.node-terminal').is('.connected') ) {
          if ( confirm('This branch is already connected. Do you want to delete it?') ) {
            removeEdge( symphonyJSON[ nodeID ].terminal[ terminalID ].edge );
          } else {
            return false;
          }
        }

        const caseNum = symphonyJSON[ nodeID ].terminal[ terminalID ].case,
              $deleteCase = $('#branch-case-list').find('tbody').find('tr').eq( caseNum - 1 );
        
        // 削除するケースに条件があるか？
        if ( $deleteCase.find('li').length ) {
          // 削除される条件をOtherに移動する
          $deleteCase.find('li').prependTo( $('#noset-conditions') );
        }        
        $deleteCase.remove();
        
        delete symphonyJSON[ nodeID ].terminal[ terminalID ];
        $targetTerminal.remove();
        
        branchLine( nodeID );
        nodeSet( $branchNode );
        panelChange( nodeID );
      }  else {
        editorMessage('INFO','The branch cannot be deleted. You need at least two branches.');        
      }
      
      connectEdgeUpdate( nodeID );
      
    }
};


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   リストからノード追加（ドラッグアンドドロップ）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$nodeList.on('mousedown', 'tbody tr', function( e ){

  if ( e.button === 0 ) {
  
  // 選択したNodeからデータを取得
  const $nodeData = $( this ).find('.add-node');
  
  let addNodeID, addNodeType, addNodeName;
  
  if ( $nodeData.is('.function') ) {
    addNodeID = 'none';
    addNodeType = $nodeData.attr('data-function-type');
    addNodeName = 'function';
  } else {
    addNodeID = $nodeData.attr('data-id');
    addNodeType = $nodeData.attr('data-orchestrator');
    addNodeName = $nodeData.attr('data-name');
  }
  
  // モード変更
  const noNodeInterrupt = [
    'conditional-branch',
    'parallel-branch',
    'merge'
  ]; // 割り込まないノード一覧
  if ( noNodeInterrupt.indexOf( addNodeType ) !== -1 ) {
    mode.nodeMove( true );
  } else {
    mode.nodeMove( false );
  }
  
  const $node = initialNode( addNodeID, addNodeType, addNodeName ),
        nodeID = $node.attr('id'),
        mouseDownPositionX = e.pageX,
        mouseDownPositionY = e.pageY;
  
  $workspace.append( $node );
  
  // 要素の追加を待つ
  $node.ready( function(){
    
    // 分岐ノードの線を描画
    if ( noNodeInterrupt.indexOf( addNodeType ) !== -1 ) {
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
        if ( $canvasWindow.is('.hover') ) {
          $node.css('transform', 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(' + editorValue.scaling + ')');
        } else {
          $node.css('transform', 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(1)');
        }
      },
      'mouseup.dragNode': function( e ){
        $( this ).off('mousemove.dragNode mouseup.dragNode');
        
        mode.clear();
        
        // Canvasの上にいるか
        if ( $canvasWindow.is('.hover') ) {
          
          // Node を アートボードにセットする
          nodeDragTop = nodeDragTop * editorValue.scaling;
          nodeDragLeft = nodeDragLeft * editorValue.scaling;
          
          const artBordPsitionX = ( g_artBoard_p.x * editorValue.scaling ) + g_canvasWindow.l + g_canvas_p.x,
                artBordPsitionY = ( g_artBoard_p.y * editorValue.scaling ) + g_canvasWindow.t + g_canvas_p.y;
          let nodeX = Math.round( ( e.pageX - artBordPsitionX - nodeDragLeft ) / editorValue.scaling ),
              nodeY = Math.round( ( e.pageY - artBordPsitionY - nodeDragTop ) / editorValue.scaling );
          
          $node.appendTo( $artBoard ).removeClass('drag current').css('opacity', 1 );
          
          nodeSet( $node, nodeX, nodeY );
          
          nodeDeselect();
          nodeSelect( nodeID );
          panelChange( nodeID );
          
          // 線の上にいるかチェック
          nodeInterrupt( nodeID );
          
        } else {
          // キャンバス外の場合は消去
          g_NodeCounter -= 1;
          delete symphonyJSON[ nodeID ];
          $node.animate({'opacity' : 0 }, 200, function(){
            $( this ).remove();
          });
        }
        
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
      if ( axis === 'x' ) positionNumber = - g_canvas_p.x - g_artBoard_p.x + ( g_canvasWindow.w / 2 ) - ( width / 2 );
      if ( axis === 'y' ) positionNumber = - g_canvas_p.y - g_artBoard_p.y + ( g_canvasWindow.h / 2 ) - ( height / 2 );
      break;
    case 'top':
      if ( axis === 'y' ) positionNumber = - g_canvas_p.y - g_artBoard_p.y + adjustPosition;
      break;
    case 'bottom':
      if ( axis === 'y' ) positionNumber = - g_canvas_p.y - g_artBoard_p.y + g_canvasWindow.h - height - adjustPosition;
      break;
    case 'left':
      if ( axis === 'x' ) positionNumber = - g_canvas_p.x - g_artBoard_p.x + adjustPosition;
      break;
    case 'right':
      if ( axis === 'x' ) positionNumber = - g_canvas_p.x - g_artBoard_p.x + g_canvasWindow.w - width - adjustPosition;
      break;
  }

  return positionNumber;

}
// newNode function variables.
// x = Number or 'left,center,right'.
// y = Number or 'top,center,bottom'.
const newNode = function( id, type, name, x, y ){

  const $node = initialNode( id, type, name );
  
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
$canvasWindow.on({
  'mouseenter' : function(){ $( this ).addClass('hover'); },
  'mouseleave' : function(){ $( this ).removeClass('hover'); }
},'.node-terminal');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線処理
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

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
  if ( !( 'edge' in symphonyJSON ) ) {
    symphonyJSON[ edgeID ] = {
      'type' : 'edge',
      'id' : edgeID
    };
  }
  
  const $outTerminal =  $('#' + outTerminalID ),
        $inTerminal = $('#' + inTermianlID );
  $outTerminal.add( $inTerminal ).addClass('connected');
  
  
  // 接続状態を紐づけする
  
  // Node out terminal
  Object.assign( symphonyJSON[ outNodeID ]['terminal'][ outTerminalID ], {
    'targetNode' : inNodeID,
    'edge' : edgeID
  });
  
  // Node in terminal
  Object.assign( symphonyJSON[ inNodeID ]['terminal'][ inTermianlID ], {
    'targetNode' : outNodeID,
    'edge' : edgeID
  });
  
  // Edge
  Object.assign( symphonyJSON[ edgeID ], {
    'outNode' : outNodeID,
    'outTerminal' : outTerminalID,
    'inNode' : inNodeID,
    'inTerminal' : inTermianlID
  });
  
  // newの場合結線する
  if ( connectEdgeID === 'new'){
    const outX = symphonyJSON[ outNodeID ].terminal[ outTerminalID ].x,
          outY = symphonyJSON[ outNodeID ].terminal[ outTerminalID ].y,
          inX = symphonyJSON[ inNodeID ].terminal[ inTermianlID ].x,
          inY = symphonyJSON[ inNodeID ].terminal[ inTermianlID ].y;
    $edge.find('path').attr('d', svgDrawPosition( outX, outY, inX, inY ) );
  }

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   結線しているところに割り込む
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const nodeInterrupt = function( nodeID ) {

    if ( $('.svg-group').is('.hover') ) {
            
      const hoverEdgeID = $('.svg-group.hover').attr('id'),
            edgeData = Object.assign({}, symphonyJSON[ hoverEdgeID ]);

      // Terminal数チェック 2で基本Nodeと判定
      if ( Object.keys( symphonyJSON[ nodeID ]['terminal'] ).length === 2 ) { 

        let inTerminalID,outTerminalID;

        // 接続チェック
        for ( let terminals in symphonyJSON[ nodeID ].terminal ) {
          const terminal = symphonyJSON[ nodeID ].terminal[ terminals ];
          if ( terminal.targetNode === undefined || terminal.targetNode === '' ) {
            if ( terminal.type === 'out' ) {
              outTerminalID = terminals;
            } else if ( terminal.type === 'in' ) {
              inTerminalID = terminals;
            }
          } else {
            // 接続済の場合は終了
            return false;
          }
        }

        // Delete Edge
        removeEdge( hoverEdgeID );

        // target Out > current Node In
        nodeConnect('new', edgeData.outNode, edgeData.outTerminal, nodeID, inTerminalID );

        // current Node Out > target In
        nodeConnect('new', nodeID, outTerminalID, edgeData.inNode, edgeData.inTerminal );

      }

    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ノード選択・選択解除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const $nodeDelete = $('#node-delete');

const nodeSelect = function( nodeID ) {
    
    // nodeIDが未指定の場合すべての要素を選択
    if ( nodeID === undefined ) {
    
      for ( const node in symphonyJSON ) {
        if ( 'type' in symphonyJSON[node] && symphonyJSON[node].type !== 'edge' ) {
          nodeSelect( node );
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

      if ( editorInitial.debug === true ) {
        window.console.log( '選択中のノード一覧' );
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
      if ( 'terminal' in  symphonyJSON[ removeNodeID ] ) {
        const terminals = symphonyJSON[ removeNodeID ].terminal;
        for ( let terminal in terminals ) {
          const terminalData = terminals[ terminal ];
          if ( 'edge' in terminalData ) {
            const edge = symphonyJSON[ terminalData.edge ];
            removeEdge( edge.id, 1 );
          }
        }
      }
      // Start（id="node-1"）は削除しない
      if ( removeNodeID !== 'node-1' ) {
        // ノード削除
        $('#' + removeNodeID ).remove();
        history.add( 'remove', removeNodeID );
        delete symphonyJSON[ removeNodeID ];
        panelChange();
      } else {
        editorMessage('INFO','Start node cannot be deleted.');
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

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キーボード操作
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$window.on('keydown', function( e ) {
    
    // キャンバスの上にいるかどうか
    if ( $canvasWindow.is('.hover') && modeCheck() ) {
    
      switch( e.keyCode ) {

        // Ctrl + A
        case 65:
          if ( e.ctrlKey ) {
            e.preventDefault();
            nodeSelect();
          }
          break;
        // Delete
        case 46:
          if ( g_selectedNodeID.length ) {
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
    
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キャンバスマウスダウン処理（ノードの移動、結線、複数選択）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 接続禁止パターン（ out Type : [in Types] ）
const connectablePattern = {
  'start' : ['conditional-branch','end','merge','pause'],
  'conditional-branch' : ['conditional-branch', 'end'],
  'parallel-branch' : ['conditional-branch','parallel-branch','merge','end','pause'],
  'merge' : ['conditional-branch','merge'],
  'pause' : ['end','pause']
};
// 接続可能チェック（接続できる＝True）
const checkConnectType = function( outType, inType ) {
  if ( outType in connectablePattern &&
       connectablePattern[ outType ].indexOf( inType ) !== -1 ) {    
    return false;
  } else {
    return true;
  }
};

$canvasWindow.on('mousedown', function( e ){

    if ( e.buttons === 1 ) {
    
      // 選択を解除しておく
      getSelection().empty();
    
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
          timerMoveFlag = false;

      // 位置移動
      const move = function( callback ) {
        $( window ).on({
          'mousemove.nodeMove': function( e ){

            moveX = e.pageX - mouseDownPositionX;
            moveY = e.pageY - mouseDownPositionY;

            let positionX = e.pageX - g_canvasWindow.l,
                positionY = e.pageY - g_canvasWindow.t;

            // キャンバス外の向き
            // X over
            if ( positionX < 1 ) {
              moveScrollSpeedX = Math.round( -positionX / adjustMoveSpeed );
              scrollDirectionX = 'left';
            } else if ( positionX > g_canvasWindow.w ) {
              moveScrollSpeedX = Math.round( ( positionX - g_canvasWindow.w ) / adjustMoveSpeed ); 
              scrollDirectionX = 'right';
            } else {
              scrollDirectionX = '';
            }
            // Y over
            if ( positionY < 1 ) {
              moveScrollSpeedY = Math.round( -positionY / adjustMoveSpeed );
              scrollDirectionY = 'top';
            } else if ( positionY > g_canvasWindow.h ) {
              moveScrollSpeedY = Math.round( ( positionY - g_canvasWindow.h ) / adjustMoveSpeed ); 
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
            $canvasWindow.off('mouseenter.canvasScroll mouseleave.canvasScroll');

            callback('mouseup');

            clearInterval( nodeMoveScrollTimer );
            statusUpdate();
            mode.clear();
          }
        });
      };

      // キャンバススクロール
      const canvasScroll = function( callback ) {
        $canvasWindow.on({
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

      const scaleMoveSet = function() {
        scaleMoveX = Math.round( ( moveX + scrollX ) / editorValue.scaling );
        scaleMoveY = Math.round( ( moveY + scrollY ) / editorValue.scaling );
        $statusMoveX.text( scaleMoveX + 'px' );
        $statusMoveY.text( scaleMoveY + 'px' );
      };
      
      
      
      // ノードの上でマウスダウン
      if ( $( e.target ).closest('.node').length ) {
      
        // Node移動、新規Edge 共通処理
        e.stopPropagation();

        const $node = $( e.target ).closest('.node'),
              nodeID = $node.attr('id');

        // マウスダウンした場所がTerminalなら新規Edge作成
        if ( $node.find('.node-terminal').is('.hover') ) {

          const $terminal = $node.find('.node-terminal.hover'),
                terminalID = $terminal.attr('id'),
                $edge = newSVG(),
                edgeID = $edge.attr('id'),
                $path = $edge.find('path');

          // 接続済みなら何もしない
          if ( $terminal.is('.connected') ) return false;

          $node.addClass('current');
          $terminal.addClass('connect');

          let connectMode,
              start_p = {
                'x': symphonyJSON[ nodeID ].terminal[ terminalID ].x,
                'y' : symphonyJSON[ nodeID ].terminal[ terminalID ].y
              };
          
          // in or out
          if ( $terminal.is('.node-in') ) {
            connectMode = 'in-out';
            mode.edgeInConnect( symphonyJSON[nodeID].type );
          } else {
            connectMode = 'out-in';
            mode.edgeOutConnect( symphonyJSON[nodeID].type );
          }
          
          const drawLine = function( event ) {
            scaleMoveSet();

            const $targetTerminal = $('.node-terminal.hover');

            // 線を削除
            const clearEdge = function () {
              $node.removeClass('current');
              $terminal.removeClass('connect connected');
              g_LineCounter--;
              $edge.animate({'opacity' : 0 }, 200, function(){
                $( this ).remove();
              });
              $artBoard.find('.forbidden').removeClass('forbidden');
            };          

            let end_p = {
              'x' : start_p.x + scaleMoveX,
              'y' : start_p.y + scaleMoveY
            }

            // ターミナルの上か？ 未接続か？
            if ( $targetTerminal.length && !$targetTerminal.is('.connected') ) {
              
              nodeDeselect();
              panelChange();
              
              const targetTerminalID = $targetTerminal.attr('id'),
                    $targetNode = $targetTerminal.closest('.node'),
                    targetNodeID = $targetNode.attr('id'),
                    targetNodeConnect = ( $targetTerminal.is('.node-out') ) ? 'in-out' : 'out-in';
              
              const startNodeType = symphonyJSON[nodeID].type,
                    endNodeType = symphonyJSON[targetNodeID].type;
              
              // 接続可能チェック
              let connectFlag = true;
              if ( nodeID === targetNodeID ) connectFlag = false; // 違うノード？
              if ( connectMode !== targetNodeConnect ) connectFlag = false; // Out <-> In?
              if ( connectMode === 'out-in') { // 接続可能パターン 
                if ( !checkConnectType( startNodeType, endNodeType ) ) connectFlag = false;
              } else {
                if ( !checkConnectType( endNodeType, startNodeType ) ) connectFlag = false;
              }
              
              if ( connectFlag ) {

                // 中心にスナップ
                end_p.x = symphonyJSON[ targetNodeID ].terminal[ targetTerminalID ].x;
                end_p.y = symphonyJSON[ targetNodeID ].terminal[ targetTerminalID ].y;

                // コネクト処理
                if ( event === 'mouseup' ) {
                  $node.removeClass('current');
                  $terminal.removeClass('connect');
                  $artBoard.find('.forbidden').removeClass('forbidden');

                  // 接続状態を紐づけする
                  if ( connectMode === 'out-in') {
                    nodeConnect( edgeID, nodeID, terminalID, targetNodeID, targetTerminalID );
                    if ( !nodeLoopCheck( nodeID ) ) removeEdge( edgeID );
                  }  else if ( connectMode === 'in-out') {
                    nodeConnect( edgeID, targetNodeID, targetTerminalID, nodeID, terminalID );
                    if ( !nodeLoopCheck( targetNodeID ) ) removeEdge( edgeID );
                  }

                }

              } else {
                $targetTerminal.addClass('forbidden');
                if ( event === 'mouseup' ) {
                  clearEdge();
                  editorMessage('INFO','This combination cannot connect.');
                }
              }

            } else if ( event === 'mouseup' ) {
              clearEdge();
            }

            if ( connectMode === 'out-in') {
              $path.attr('d', svgDrawPosition( start_p.x, start_p.y, end_p.x, end_p.y ) );
            } else if ( connectMode === 'in-out') {
              $path.attr('d', svgDrawPosition( end_p.x, end_p.y, start_p.x, start_p.y ) );
            }

          };
          move( drawLine );
          canvasScroll( drawLine );

          $path.attr('d', svgDrawPosition( start_p.x, start_p.y, start_p.x, start_p.y ) );

        } else {

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
          
          // 選択しているノードのターミナルの数
          const selectNodeTerminalLength = Object.keys( symphonyJSON[ nodeID ].terminal ).length;
          
          // 選択しているノードから移動する線をリスト化する
          const selectNodeMoveLineArray = [];
          for ( let i = 0; i < selectNodeLength; i++ ) {
            const selectNodeID = g_selectedNodeID[ i ];
            // ターミナル数ループ
            for ( let terminalID in symphonyJSON[ selectNodeID ].terminal ) {
              const terminal = symphonyJSON[ selectNodeID ].terminal[ terminalID ];
              if ( 'edge' in terminal ) {
                const edgeID = symphonyJSON[ terminal.edge ].id;
                if ( selectNodeMoveLineArray.indexOf( edgeID ) === -1 ) {
                  selectNodeMoveLineArray.push( edgeID );
                }
              }
            }            
          }
          const selectNodeLineLength = selectNodeMoveLineArray.length;
          
          // 未接続、選択が一つ、ターミナルが２つの場合は割り込み可能にする
          if ( selectNodeLineLength === 0 &&
              selectNodeLength === 1 &&
              selectNodeTerminalLength === 2 ) {
            mode.nodeMove( false );
          } else {
            mode.nodeMove( true );
          }
          
          // パネル変更
          panelChange( nodeID );
          
          // ノード移動処理
          const moveNode = function( event ){

            if ( event === 'mousemove') {

              scaleMoveSet();
              
              // 選択ノードをすべて仮移動
              $canvasWindow.find('.node.selected')
                .css('transform', 'translate3d(' + scaleMoveX + 'px,' + scaleMoveY + 'px,0)');
              
              // 選択ノードに接続している線をすべて移動
              for ( let i = 0; i < selectNodeLineLength; i++ ) {
                const moveLineID = selectNodeMoveLineArray[ i ],
                      inNodeID = symphonyJSON[ moveLineID ].inNode,
                      outNodeID = symphonyJSON[ moveLineID ].outNode;
                
                const inTerminal = symphonyJSON[ inNodeID ].terminal[ symphonyJSON[ moveLineID ].inTerminal ],
                      outTerminal = symphonyJSON[ outNodeID ].terminal[ symphonyJSON[ moveLineID ].outTerminal ];
                
                let inX = inTerminal.x,
                    inY = inTerminal.y,
                    outX = outTerminal.x,
                    outY = outTerminal.y;
                
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
              $node.removeClass('current').css('opacity', 1 );
              // 選択ノード全ての位置確定
              const nodeSetFunc = function( setNodeID ) {
                const beforeX = symphonyJSON[ setNodeID ].x,
                      beforeY = symphonyJSON[ setNodeID ].y;
                nodeSet( $('#' + setNodeID ), scaleMoveX + beforeX, scaleMoveY + beforeY );
              }
              for ( let i = 0; i < selectNodeLength; i++ ) {
                nodeSetFunc( g_selectedNodeID[ i ] );
              }
              // 未接続の基本Nodeを線の上にドラッグで線を消し、ドラッグしたNodeと結線
              if ( modeCheck('node-noedge-move') ) {
                nodeInterrupt( nodeID );
              }
            }

          };

          move( moveNode );
          canvasScroll( moveNode );

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
        
        mode.nodeSelect();
        
        const positionNow = {
          'x' : function ( x ) {
            x = Math.round( ( x - ( g_artBoard_p.x * editorValue.scaling ) - g_canvasWindow.l - g_canvas_p.x ) / editorValue.scaling );
            return x;
          },
          'y' : function ( y ) {
            y = Math.round( ( y - ( g_artBoard_p.y * editorValue.scaling ) - g_canvasWindow.t - g_canvas_p.y ) / editorValue.scaling )
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
            mode.clear();
            // 選択範囲内のノードを選択
            const rect = {
              'left' : x,
              'top' : y,
              'right' : x + w,
              'bottom' : y + h
            };
            for ( let nodeID in symphonyJSON ) {
              if ( 'type' in symphonyJSON[ nodeID ] ) {
                if ( symphonyJSON[ nodeID ].type !== 'edge' ) {
                  const node = {
                    'left' : symphonyJSON[ nodeID ].x,
                    'top' : symphonyJSON[ nodeID ].y,
                    'right' : symphonyJSON[ nodeID ].x + symphonyJSON[ nodeID ].w,
                    'bottom' : symphonyJSON[ nodeID ].y + symphonyJSON[ nodeID ].h
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

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期ノードセット（Start and End）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// newNode( id, type, name, x, y )
newNode('none', 'symphony-start', 'fucntion', 'left', 'center');
newNode('none', 'symphony-end', 'fucntion', 'right', 'center');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Startノードから順番に番号を振る
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

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
    'merge'
  ];
  
  const nodeLoopCheckRecursion = function( next ) {
    if ( flag === true ) {
      const node = symphonyJSON[ next ];

      // 経路ログ
      if ( editorInitial.debug === true ) {
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
              editorMessage('ERROR','Loop error.');
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

const $property = $('#property'),
      $branchCaseList = $('#branch-case-list').find('tbody');

$property.find('.panel-tab').find('li').not('[data-tab-nanme="symphony"]').hide();
$property.find('.symphony-panel').not('#symphony').hide();

const panelChange = function( nodeID ) {

  // 複数選択されている場合はパネルを表示しない
  if ( g_selectedNodeID.length <= 1 ) {
    
    let panelType = '';
    
    // nodeIDが未定義の場合はシンフォニーパネルを表示
    if ( nodeID === undefined ) {
      
      nodeID = 'symphony';
      panelType = 'symphony';
      
    } else {
      
      const nodeType = symphonyJSON[ nodeID ].type;
      
      // 対応したパネルを表示
      switch( nodeType ) {
        case 'movement':
        case 'conditional-branch':
        case 'parallel-branch':
        case 'merge':
        case 'call':
          panelType = nodeType;
          break;
        case 'start':
        case 'end':
        case 'blank':
        case 'pause':
          panelType = 'function';
          break;
        default:
          panelType = 'symphony';
      }
    }
    const $panel = $('#' + panelType );
    $property.find('.panel-tab').find('li[data-tab-nanme="' + panelType + '"]').show().click()
      .siblings().hide();
    
    // Noteのチェック
    if ( 'note' in symphonyJSON[ nodeID ] ) {
      $panel.find('textarea').val( symphonyJSON[ nodeID ].note );
    } else {
      $panel.find('textarea').val('');
    }
    
    // パネルごとの処理
    switch( panelType ) {
      case '':
        break;
      case 'conditional-branch': {
        
        // 分岐の数だけボックスを用意する
        const keys = Object.keys( symphonyJSON[ nodeID ].terminal ).length - 2;
        let listHTML = '';
        for (let i = 0; i < keys; i++ ) {
          listHTML += '<tr><th class="property-th">Case ' + ( i + 1 ) + ' :</th><td class="property-td"><ul class="branch-case"></ul></td></tr>';
        }
        $branchCaseList.html( listHTML );
        
        // 分岐をパネルに反映する
        let terminalConditionArray = []; // 使用中の分岐
        for( let terminalID in symphonyJSON[ nodeID ].terminal ) {
          const terminalData = symphonyJSON[ nodeID ].terminal[ terminalID ];
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
        for ( let key in endStatus ){
          if ( terminalConditionArray.indexOf( key ) === -1 ) {
            nosetConditionHTML += conditionBlockHTML( key );
          }
        }
        $('#noset-conditions').html( nosetConditionHTML );
        break;
      }
      default:
    }

  } else {
    $property.find('.panel-tab li, .symphony-panel').hide();
  }
  
}

/* 分岐パネル */
$property.find('.branch-add').on('click', function(){ addBranch( g_selectedNodeID[ 0 ] ); });
$property.find('.branch-delete').on('click', function(){ removeBranch( g_selectedNodeID[ 0 ] ); });

/* 条件ブロック */
const conditionBlockHTML = function( key ) {
  return '<li class="' + endStatus[ key ][0] + '" data-end-status="' + key + '">' + endStatus[ key ][1] + '</li>';
}

/* 条件状態更新 */
const conditionUpdate = function( nodeID ) {
  $branchCaseList.find('.branch-case').each( function( i ) {
    let conditions = [],
        tergetTerminalID = '';
    $( this ).find('li').each( function(){
      conditions.push( $( this ).attr('data-end-status') );
    });
    // どこの条件か？
    for ( let terminalID in symphonyJSON[ nodeID ].terminal ) {
      const terminal = symphonyJSON[ nodeID ].terminal[ terminalID ];
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
  nodeSet( $('#' + nodeID ) );
  branchLine( nodeID );
  connectEdgeUpdate( nodeID );
}

/* 条件移動 */
$property.find('#conditional-branch').on('mousedown', 'li', function( e ) {

  const $condition = $( this ),
        scrollTop = $window.scrollTop(),
        scrollLeft = $window.scrollLeft(),
        conditionWidth = $condition.outerWidth(),
        conditionHeight = $condition.outerHeight(),
        mousedownPositionX = e.pageX,
        mousedownPositionY = e.pageY;

  let moveX, moveY;
  
  mode.caseDrag();
  
  $property.find('.branch-case').on({
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
      mode.clear();
      if ( $property.find('.branch-case.hover').length ) {
        $property.find('.branch-case.hover').append( $condition );
        $property.find('.branch-case').off().removeClass('hover');
        // 条件反映
        conditionUpdate( g_selectedNodeID[0] );
      }
    }
  });
});

// Note更新
$property.find('textarea').on('input', function() {

  if ( g_selectedNodeID.length === 1 ) {
    const $targetNodeNote = $('#' + g_selectedNodeID[0] ).find('.node-note');
    let noteText = $( this ).val();

    // 入力されたテキスト
    symphonyJSON[ g_selectedNodeID[0] ].note = noteText;
    
    // タグの無害化
    noteText = textEntities( noteText );    
    
    if ( noteText === '' ) {
      $targetNodeNote.hide();
    } else {
      $targetNodeNote.show().find('p').html( noteText );
    }

  } else if ( g_selectedNodeID.length === 0 ) {
    symphonyJSON['symphony'].note = $( this ).val();
  }

});

// Movement Filter
const $movementList = $('#movement-list').find('.node-table');
$('#movement-filter').on('input', function(){
  const $movementFilter = $( this ),
        inputValue = $movementFilter.val(),
        regExp = new RegExp( inputValue, "i");
  
  if ( inputValue !== '' ) {
  $movementList.find('tbody').find('tr').each( function(){
    const $tr = $( this ),
          movementName = $tr.find('td').text();
    if ( regExp.test( movementName ) ) {
      $tr.removeClass('filter-hide');
    } else {
      $tr.addClass('filter-hide');
    }
  });
  } else {
    $movementList.find('.filter-hide').removeClass('filter-hide');
  }
  
});

// Message list
const $messageList = $('#editor-message'),
      messageHeight = 32;
const editorMessage = function( type, message ){
  const $message = $(''
    + '<li class="editor-message-li ' + type.toLocaleLowerCase() + '">'
      + '<dl class="editor-message-dl">'
        + '<dt class="editor-message-dt">' + type + '</dt><dd class="editor-message-dd">' + message + '</dd>'
      + '</dl>'
    + '</li>');
  
  // アニメーションストップ
  $messageList.stop(0,0);
  
  const bottom = $messageList.css('bottom').replace('px','');
  
  $messageList.css('bottom', bottom - messageHeight ).find('ul').append( $message );
  
  $messageList.animate({'bottom':0}, 100, 'linear');
  
  setTimeout( function() {
    // アニメーション後に削除
    $message.addClass('remove').on('animationend webkitAnimationEnd', function(){
      $message.remove();
    });
  }, 3000 );
};




} else if ( editorType === 'panel-image' ) {
/* ---------------------------------------------------------------------------------------------------- *

   03.パネル用画像エディタ

 * ---------------------------------------------------------------------------------------------------- */

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 初期値
const panelImageValue = {
  'workspaceHeight' : 640,
  'canvasWidth' : 2000,
  'canvasHeight' : 2000,
  'artboradWidth' : 400,
  'artboradHeight' : 400,
  'basicWidth' : 400,
  'basicHeight' : 400,
  'documentName' : 'Panel_Image',
  'text' : 'IT Automation',
  'color' : '#F8F8F8',
  'color2' : '#8095B0',
  'fontSize' : '40',
  'shapeColor' : '#002B62',
  'newSymbol' : 'symbol-000001',
  'newShape' : 'shape-000004',
  'saveExtension' : 'ipf',
  'imageFileMaximumSize' : 1048576
};

// 各種サイズをセット
$workspace.css({
  'height' : panelImageValue.workspaceHeight
});
$canvas.css({
  'width' : panelImageValue.canvasWidth,
  'height' : panelImageValue.canvasHeight
});
$artBoard.css({
  'width' : panelImageValue.artboradWidth,
  'height' : panelImageValue.artboradHeight
});
canvasPositionReset( 0 );

// jQuery オブジェクトをキャッシュ
const $layerMenu = $('#layer-menu'),
      $layerList = $('#layer-list'),
      $layerProperty = $('#layer-property'),
      $layerPropertyPanel = $layerProperty.find('.panel'),
      $layerTab = $layerProperty.find('.panel-tab'),
      $artBoardWidth = $('#document-width'),
      $artBoardHeight = $('#document-height');

// inputに初期値を入れる
$artBoardWidth.val( panelImageValue.artboradWidth );
$artBoardHeight.val( panelImageValue.artboradHeight );

// SVG ID 連番用
let g_layerCounter = 1;

// 選択中のレイヤーのID
let g_selectedLayer = '';

// JSONオブジェクト
let artBoardJSON = new Object();

artBoardJSON['config'] = {
  'documentName' : panelImageValue.documentName,
  'documentWidth' : panelImageValue.artboradWidth,
  'documentHeight' : panelImageValue.artboradHeight,
  'editorVersion' : editorInitial.version,
  'layerCounter' : g_layerCounter
}

// 未対応処理
if ( browser === 'ie' ) {
  $('.editor-not-available').show();
}
if ( filterFlg === false || browser === 'edge' ) {
  $layerTab.find('[data-tab-nanme="layer-property-filter"]').hide();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   アートボードサイズ（ドキュメントサイズ）変更
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const artBoardReset = function() {

  let artBoardWidth = $artBoardWidth.val(),
      artBoardHeight = $artBoardHeight.val();
  
  // キャンバスより大きくしない
  if ( panelImageValue.canvasWidth < artBoardWidth ) {
    artBoardWidth = panelImageValue.canvasWidth;
    $artBoardWidth.val( artBoardWidth );
  }
  if ( panelImageValue.canvasHeight < artBoardHeight ) {
    artBoardHeight = panelImageValue.canvasHeight;
    $artBoardHeight.val( artBoardHeight )
  } 
  
  panelImageValue.artboradWidth = artBoardWidth;
  panelImageValue.artboradHeight = artBoardHeight;
  
  $artBoard.css({
    'width' : artBoardWidth,
    'height' : artBoardHeight
  });
  artBoardJSON['config'].documentWidth = artBoardWidth;
  artBoardJSON['config'].documentHeight = artBoardHeight;
  canvasPositionReset();
  
  $artBoard.find('.art-board-layer, .bounding-box').css({
    'left' : -g_artBoard_p.x,
    'top' : -g_artBoard_p.y
  });
}

$artBoardWidth.add( $artBoardHeight ).on('change', function(){
    artBoardReset();
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤー基本
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const initialLayer = function( type ) {

    if ( type !== 'image' ) {

      let layerType = type;
      if ( layerType === 'bounding-box') layerType = 'rect';

      const $layer = $('<div />'),
            xmlns = 'http://www.w3.org/2000/svg',
            layerCanvasSVG = document.createElementNS( xmlns, 'svg'),
            $layerSVG = $( document.createElementNS( xmlns, 'svg') ),
            $layerGroup = $( document.createElementNS( xmlns, 'g') ),
            $layerShape = $( document.createElementNS( xmlns, layerType ) );

      layerCanvasSVG.setAttribute('viewBox', '0 0 ' + g_canvas.w + ' ' + g_canvas.h );

      const $layerCanvasSVG = $( layerCanvasSVG );

      $layer.addClass('art-board-layer').css({
        'left' : -g_artBoard_p.x,
        'top' : -g_artBoard_p.y
      }).append( $layerCanvasSVG.append( $layerSVG.append( $layerGroup.append( $layerShape ) ) ) );

      $layerCanvasSVG.attr({
        'class' : 'layer-canvas-svg',
        'width' : g_canvas.w,
        'height' : g_canvas.h
      });

      $layerSVG.attr({
        'class' : 'layer-svg'
      }).css({
        'overflow' : 'visible'
      });

      $layerGroup.attr('class', 'layer-group').css({
        'transform-origin' : 'center center 0'
      });

      $layerShape.attr({
        'class' : 'layer-shape'
      });

      switch( type ) {
        case 'text':
          $layerShape.text( panelImageValue.text ).attr({
            'x' : '50%'
          });
          $layerGroup.css({
            'text-anchor' : 'middle'
          });
          break;

        case 'symbol':
          $layerSVG.get(0).setAttribute('preserveAspectRatio', 'none');
          $layerSVG.get(0).setAttribute('viewBox', '0 0 ' + panelImageValue.basicWidth + ' ' + panelImageValue.basicHeight );
          break;

        case 'shape':
          $layerSVG.get(0).setAttribute('preserveAspectRatio', 'none');
          $layerSVG.get(0).setAttribute('viewBox', '0 0 ' + panelImageValue.basicWidth + ' ' + panelImageValue.basicHeight );
          break;

        case 'bounding-box':
          $layer.addClass('bounding-box output-ignore').removeClass('art-board-layer');
          $layerSVG.attr('class', 'outer-line');
          $layerSVG.get(0).setAttribute('preserveAspectRatio', 'none');
          $layerSVG.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );
          $layerShape.attr({
            'x' : 0,
            'y' : 0,
            'width' : g_artBoard.w,
            'height' : g_artBoard.h,
            'vector-effect' : 'non-scaling-stroke'
          });
          break;

        default:
      }

      return $layer;
    
    } else {
    
      const imageLayerHTML = ''
      + '<div class="art-board-layer" style="left:-' + g_artBoard_p.x + 'px; top:-' + g_artBoard_p.y + 'px;">'
        + '<div class="layer-canvas-svg" style="width:' + g_canvas.w + 'px; height:' + g_canvas.h + 'px;">'
          + '<div class="layer-svg">'
            + '<div class="layer-shape" style="width:100%; height:100%;"></div>'
          + '</div>'
        + '</div>'
      + '</div>';
            
      return $( imageLayerHTML ); 
    
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   新規レイヤー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const newLayer = function( type ) {

  const $layer = initialLayer( type );
  let   layerID = '';
  
  if( type !== 'bounding-box' ) {
    layerID = 'layer-id-' + g_layerCounter;
    g_layerCounter++;
    artBoardJSON['config'].layerCounter = g_layerCounter;
  } else {
    layerID = 'bounding-box';
  }
  
  $layer.attr('id', layerID );
  
  // 共通初期値設定
  artBoardJSON[ layerID ] = {
      'id' : layerID,
      'type' : type,
      'x' : 0,
      'y' : 0,
      'width' : g_artBoard.w,
      'height' : g_artBoard.h,
      'opacity' : 1,
      'stroke' : '#000000',
      'strokeWidth' : 0,
      'strokeOpacity' : 1,
      'paintOrder' : 'stroke',
      'strokeCap' : 'butt',
      'strokeJoin' : 'miter',
      'translateX' : 0,
      'translateY' : 0,
      'scaleX' : 1,
      'scaleY' : 1,
      'rotate' : 0,
      'skew' : 0,
      'filterBlur' : 0,
      'filterOpacity' : 1,
      'filterBrightness' : 1,
      'filterContrast' : 100,
      'filterGrayscale' : 0,
      'filterSepia' : 0,
      'filterSaturate' : 100,
      'filterInvert' : 0,
      'filterHue' : 0,
      'filterShadowColor' : '#000000',
      'filterShadowBlur' : 0,
      'filterShadowX' : 0,
      'filterShadowY' : 0,
      'zIndex' : 0
    }
    switch( type ) {
      case 'text':
        Object.assign( artBoardJSON[ layerID ], {
          'name' : 'New Text',
          'height' : panelImageValue.fontSize * 1.5,
          'fill' : panelImageValue.color,
          'font' : '',
          'fontsize' : panelImageValue.fontSize,
          'fontweight' : 'normal',
          'text' : panelImageValue.text
        });
        break;

      case 'symbol':
        Object.assign( artBoardJSON[ layerID ], {
          'name' : 'New Symbol',
          'symbolID' : panelImageValue.newSymbol,
          'colorNum' : 2,
          'fill' : panelImageValue.color,
          'fill2' : panelImageValue.color2,
          'opacity2' : 1,
          'fill3' : panelImageValue.shapeColor,
          'opacity3' : 1
        });
        $layer.find('g').attr('class', 'layer-group').html( $('#' + panelImageValue.newSymbol ).find('g').html() );
        break;
        
      case 'shape':
        Object.assign( artBoardJSON[ layerID ], {
          'name' : 'New Shape',
          'shapeID' : panelImageValue.newShape,
          'colorNum' : 1,
          'fill' : panelImageValue.shapeColor
        });
        $layer.find('g').attr('class', 'layer-group').html( $('#' + panelImageValue.newShape ).find('g').html() );
        break;
        
      case 'image':
        Object.assign( artBoardJSON[ layerID ], {
          'name' : 'New Image',
          'imageURL' : '',
          'imageSize' : 'auto',
          'imageRepeat' : 'no-repeat'
        });
        break;

      case 'bounding-box':
        delete artBoardJSON[ layerID ];
        break;

      default:
    }
    
    $artBoard.append( $layer );
    
    if( type !== 'bounding-box' ) {
      setLayerList( layerID );
      selectLayer( layerID );
      updateLayer( layerID );
      zIndexUpdate();
    }
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   artBoardJSONを元にレイヤーを作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const setLayer = function( layerID, mode ) {

    // コピー処理
    if ( mode === 'copy' ) {
      
      const copyID = layerID;
      layerID = 'layer-id-' + g_layerCounter;
      g_layerCounter++;
      artBoardJSON['config'].layerCounter = g_layerCounter;
      
      artBoardJSON[ layerID ] = Object.assign({}, artBoardJSON[ copyID ] );
      artBoardJSON[ layerID ].id = layerID;
      artBoardJSON[ layerID ].name = artBoardJSON[ layerID ].name + ' Copy';
      
    }

    const $layer = initialLayer( artBoardJSON[ layerID ].type );
    
    $layer.attr('id', layerID );
    $artBoard.append( $layer );

    if( artBoardJSON[ layerID ].type === 'symbol' ) {
      $layer.find('g').attr('class', 'layer-group').html( $('#' + artBoardJSON[ layerID ].symbolID ).find('g').html() );
    }
  
    if( artBoardJSON[ layerID ].type === 'shape' ) {
      $layer.find('g').attr('class', 'layer-group').html( $('#' + artBoardJSON[ layerID ].shapeID ).find('g').html() );
    }
  
    setLayerList( layerID );
    selectLayer( layerID );
    updateLayer( layerID );
    zIndexUpdate();

}
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーリストを追加
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const setLayerList = function( layerID ) {

    const layerListHTML = ''
      + '<li class="layer-list-li" data-layer-id="' + layerID + '" data-layer-type="' + artBoardJSON[ layerID ].type + '">'
        + '<span class="layer-drag"></span>'
        + '<span class="layer-eye">'
          + '<input id="layer-eye-check-' + layerID + '" class="layer-eye-check" type="checkbox" checked>'
          + '<label class="layer-eye-label" for="layer-eye-check-' + layerID + '"></label>'
        + '</span>'
        + '<span class="layer-name">' + artBoardJSON[ layerID ].name + '</span>'
        + '<span class="layer-copy"></span>'
        + '<span class="layer-delete"></span>'
      + '</li>';
      
    if ( g_selectedLayer === '' ) {
      $layerList.prepend( layerListHTML );
    } else {
      $layerList.find('.layer-list-li[data-layer-id="' + g_selectedLayer + '"]').before( layerListHTML );
    }
    
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーメニュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$layerMenu.find('button').on('click', function(){
    newLayer( $( this ).attr('data-add-layer') );
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーリストの順番でz-indeをセット
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const zIndexUpdate = function() {
    let zIndex = $layerList.find('.layer-list-li').length;
    $layerList.find('.layer-list-li').each( function(){
      const layerID = $( this ).attr('data-layer-id');
      $('#' + layerID ).css('z-index', zIndex );
      artBoardJSON[ layerID ].zIndex = zIndex;
      zIndex--;
    });
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーリスト
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$layerList.on('click', 'span', function(){

    const $action = $( this );

    if ( $action.is('.layer-drag') ) return false;

      const $li = $action.closest('.layer-list-li'),
            layerID = ( $li.attr('data-layer-id') === undefined ) ? 'none' : $li.attr('data-layer-id');
      if ( layerID !== 'none') {

      const $layer = $('#' + layerID ),
            clickType = $action.attr('class');

      switch ( clickType ) {

        case 'layer-eye':
          $action.find('.layer-eye-check').click();
          if ( $action.find('.layer-eye-check').prop('checked') ) {
            $layer.show();
          } else {
            $layer.hide();
          }
          break;
          
        case 'layer-name':
          selectLayer( layerID );
          break;

        case 'layer-copy':
          setLayer( layerID, 'copy');
          break;

        case 'layer-delete':
          delete artBoardJSON[ layerID ];
          $li.add( $layer ).remove();
          deselectLayer();
          break;

        default:
      }

    }

}).on('mousedown', '.layer-drag', function( e ){
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーの順序を入れ替える
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

    const $layerItem = $( this ).closest('.layer-list-li'),
          mouseDownPositionY = e.pageY;

    $layerItem.css({
      'z-index' : 2,
      'opacity' : 0.3,
      'pointer-events' : 'none'
    });

    // どこの上にいるか
    const offsetY = $layerList.offset().top;

    let $hoverItem;

    $layerList.find('.layer-list-li').on({
      'mouseenter.layer-move' : function(){
        $hoverItem = $( this );
      },
      'mousemove.layer-move' : function( e ){
        const $moveItem = $( this ),
              hoverItemPositionY = $moveItem.position().top,
              hoverItemHeight = $moveItem.outerHeight();  
        if ( e.pageY - ( offsetY + hoverItemPositionY ) > hoverItemHeight / 2 ) {
          $moveItem.removeClass('hover-top').addClass('hover-bottom');
        } else {
          $moveItem.removeClass('hover-bottom').addClass('hover-top');
        }

      },
      'mouseleave.layer-move' : function(){      
        $( this ).removeClass('hover-bottom hover-top');
      }
    });

    $window.on({
      'mousemove.layer-drag' : function( e ){
        // 移動
        $layerItem.css('transform', 'translateY(' + ( e.pageY - mouseDownPositionY ) + 'px)');
      },
      'mouseup.layer-drag' : function(){
        // イベント削除
        $( this ).off('mousemove.layer-drag mouseup.layer-drag');
        $layerList.find('.layer-list-li').off('mouseenter.layer-move mousemove.layer-move mouseleave.layer-move');
        // CSS戻す
        $layerItem.css({
          'transform' : 'translateY(0)',
          'z-index' : 1,
          'opacity' : 1,
          'pointer-events' : 'auto'
        });
        // レイヤー順序入れ替え
        if ( typeof $hoverItem === 'object') {
          if ( $hoverItem.is('.hover-top') ) {
            $layerItem.insertBefore( $hoverItem );
          } else {
            $layerItem.insertAfter( $hoverItem );
          }
          $hoverItem.removeClass('hover-bottom hover-top');
          zIndexUpdate();
        }

      }
    });

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   図形、シンボルを切り替える
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

$workspace.find('.editor-symbol-list-li').on('click', function(){
    const $symbolList = $( this ),
          symbolID = $symbolList.find('svg').attr('id'),
          symbolHTML = $symbolList.find('g').html(),
          $layer = $('#' + g_selectedLayer );
    
    // カラー数
    let symbolColor = $symbolList.find('svg').attr('data-svg-color');
    // カラー数が無い場合は2とする
    if ( symbolColor === undefined ) symbolColor = 2;
    
    symbolColorShow( symbolColor ); 
    $layer.find('g').html( symbolHTML );
    artBoardJSON[ g_selectedLayer ].symbolID = symbolID;
    artBoardJSON[ g_selectedLayer ].colorNum = symbolColor;
    updateLayer( g_selectedLayer );
});

$workspace.find('.editor-shape-list-li').on('click', function(){
    const $shapeList = $( this ),
          shapeID = $shapeList.find('svg').attr('id'),
          shapeHTML = $shapeList.find('g').html(),
          $layer = $('#' + g_selectedLayer );
    
    // カラー数
    let shapeColor = $shapeList.find('svg').attr('data-svg-color');
    // カラー数が無い場合は1とする
    if ( shapeColor === undefined ) shapeColor = 1;
    
    symbolColorShow( shapeColor ); 
    $layer.find('g').html( shapeHTML );
    artBoardJSON[ g_selectedLayer ].shapeID = shapeID;
    artBoardJSON[ g_selectedLayer ].colorNum = shapeColor;
    updateLayer( g_selectedLayer );
});

// 入力カラー数の表示切替
const symbolColorShow = function( symbolColor ) {

    if ( Number( symbolColor ) === 2 ) {
      $layerProperty.find('.symbol-color-3').hide();
      $layerProperty.find('.symbol-color-2').show();      
    } else if ( Number( symbolColor ) === 3 ) {
      $layerProperty.find('.symbol-color-2, .symbol-color-3').show();
    } else {
      $layerProperty.find('.symbol-color-2, .symbol-color-3').hide();
    }

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーを選択・選択解除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const selectLayer = function( layerID, animationFlg ) {

    // セレクト解除
    $layerList.find('.layer-list-selected').removeClass('layer-list-selected');
    $artBoard.find('.layer-selected').removeClass('layer-selected');
    
    // 選択処理
    const $target = $layerList.find('.layer-list-li[data-layer-id="' + layerID + '"]');
    $target.addClass('layer-list-selected');
    
    $('#' + layerID ).addClass('layer-selected');
    
    if ( animationFlg === true ) {
      $layerList.stop(0,0).animate({ scrollTop : $layerList.scrollTop() + $target.position().top }, 300 );
    }
    g_selectedLayer = layerID;
    $layerTab.show().find('.selected').click();
    propertyUpdate();
    updateLayer( layerID );
    
    $boundingBox.show();

}
const deselectLayer = function() {
    $layerList.find('.layer-list-selected').removeClass('layer-list-selected');
    g_selectedLayer = '';
    $layerPropertyPanel.add( $layerTab ).hide();    
    
    $artBoard.find('.layer-selected').removeClass('layer-selected');
    $boundingBox.hide();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   プロパティ表示更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const propertyUpdate = function() {

  if ( g_selectedLayer !== '' ){

    const layerData = artBoardJSON[ g_selectedLayer ];
  
    // Document
    $('#document-name').val( artBoardJSON['config'].documentName );
    
    // Common
    $('#property-layer-name').val( layerData.name );
    $('#property-opacity').val( layerData.opacity * 100 );
    $('#property-width').val( layerData.width );
    $('#property-height').val( layerData.height );
    $('#property-x').val( layerData.x );
    $('#property-y').val( layerData.y );
    if ( layerData.type === 'text') {
      $('#property-color').val( layerData.fill );
    }
  
    // Text
    if ( layerData.type === 'text' ) {
      $layerProperty.find('.property-type-text').show();
      $layerProperty.find('.property-type-not-text').hide();
      $('#property-text').val( layerData.text );
      $('#property-text-ime').val( layerData.text );
      $('#property-font').val( layerData.font );
      $('#property-font-size').val( layerData.fontsize );
      $('#property-font-weight').val( layerData.fontweight );
    } else {
      $layerProperty.find('.property-type-text').hide();
      $layerProperty.find('.property-type-not-text').show();
    }
    
    // Border
    $('#property-border-width').val( layerData.strokeWidth );
    $('#property-border-position').val( layerData.paintOrder );
    $('#property-border-color').val( layerData.stroke );
    $('#property-border-opacity').val( layerData.strokeOpacity * 100 );
    $('#property-border-cap').val( layerData.strokeCap );
    $('#property-border-join').val( layerData.strokeJoin );

    // Transform
    $('#property-scale-x').val( layerData.scaleX * 100 );
    $('#property-scale-y').val( layerData.scaleY * 100 );
    $('#property-rotate').val( layerData.rotate );
    $('#property-skew').val( layerData.skew );

    // Symbol
    if ( layerData.type === 'symbol' ) {
      $layerProperty.find('.property-type-symbol').show();
      symbolColorShow( layerData.colorNum );
      $('#property-color2').val( layerData.fill2 );
      $('#property-opacity2').val( layerData.opacity2 * 100 );
      $('#property-color3').val( layerData.fill3 );
      $('#property-opacity3').val( layerData.opacity3 * 100 );
    } else {
      $layerProperty.find('.property-type-symbol').hide();
    }
    
    // Shape
    if ( layerData.type === 'shape' ) {
      $layerProperty.find('.property-type-shape').show();
      symbolColorShow( layerData.colorNum );
    } else {
      $layerProperty.find('.property-type-shape').hide();
    }

    // Filter
    $('#property-filter-blur').val( layerData.filterBlur );
    $('#property-filter-opacity').val( layerData.filterOpacity * 100 );
    $('#property-filter-brightness').val( layerData.filterBrightness * 100 );
    $('#property-filter-contrast').val( layerData.filterContrast );
    $('#property-filter-grayscale').val( layerData.filterGrayscale );
    $('#property-filter-sepia').val( layerData.filterSepia );
    $('#property-filter-saturate').val( layerData.filterSaturate );
    $('#property-filter-invert').val( layerData.filterInvert );
    $('#property-filter-hue').val( layerData.filterHue );
    $('#property-filter-shadow-color').val( layerData.filterShadowColor );
    $('#property-filter-shadow-blur').val( layerData.filterShadowBlur );
    $('#property-filter-shadow-x').val( layerData.filterShadowX );
    $('#property-filter-shadow-y').val( layerData.filterShadowY );
    
    // Image 
    if ( layerData.type === 'image' ) {
      $layerProperty.find('.property-type-image').show();
      $layerProperty.find('.property-type-not-image').hide();
      $('#property-image-size').val( layerData.imageSize );
      $('#property-image-repeat').val( layerData.imageRepeat );
    } else {
      $layerProperty.find('.property-type-image').hide();
      $layerProperty.find('.property-type-not-image').show();
    }
    
  }  
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Layer 変形更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const updateLayer = function ( layerID ) {

    const $layer = $('#' + layerID ),
          $layerCanvasSVG = $layer.find('.layer-canvas-svg'),
          $layerSVG = $layer.find('.layer-svg'),
          $layerGroup = $layer.find('.layer-group'),
          $layerShape = $layer.find('.layer-shape'),
          layerData = artBoardJSON[ layerID ];
    
    // Transform
    const transform = ''
          + 'translate('+ layerData.translateX +'px,'+ layerData.translateY +'px) '
          + 'rotate(' + layerData.rotate + 'deg) '
          + 'skew(' + layerData.skew + 'deg) ',
          transformScale = 'scale('+ layerData.scaleX +','+ layerData.scaleY +')';
          
    // Filter
    let filter = '';
    if ( filterFlg === true ) {
      if ( layerData.filterBlur !== 0 )  filter += 'blur(' + layerData.filterBlur + 'px) ';
      if ( layerData.filterOpacity !== 1 )  filter += 'opacity(' + layerData.filterOpacity + ') ';
      if ( layerData.filterBrightness !== 1 )  filter += 'brightness(' + layerData.filterBrightness + ') ';
      if ( layerData.filterContrast !== 100 )  filter += 'contrast(' + layerData.filterContrast + '%) ';
      if ( layerData.filterGrayscale !== 0 )  filter += 'grayscale(' + layerData.filterGrayscale + '%) ';
      if ( layerData.filterSepia !== 0 )  filter += 'sepia(' + layerData.filterSepia + '%) ';
      if ( layerData.filterSaturate !== 100 )  filter += 'saturate(' + layerData.filterSaturate + '%) ';
      if ( layerData.filterInvert !== 0 )  filter += 'invert(' + layerData.filterInvert + '%) ';
      if ( layerData.filterHue !== 0 )  filter += 'hue-rotate(' + layerData.filterHue + 'deg) ';
      if ( layerData.filterShadowBlur + layerData.filterShadowX + layerData.filterShadowY > 0 ) {
        filter += 'drop-shadow(' + layerData.filterShadowX + 'px ' + layerData.filterShadowY + 'px ' + layerData.filterShadowBlur + 'px ' + layerData.filterShadowColor + ')';
      }
    }
    
    let layerWidth = layerData.width,
        layerHeight = layerData.height;
    
    // レイヤーリストの名前
    $layerList.find('.layer-list-li[data-layer-id="' + g_selectedLayer + '"]').find('.layer-name').text( layerData.name );

    // フィルターがあれば追加
    if ( filter !== '' ) {
      $layerCanvasSVG.css('filter', filter );
    } else {
      $layerCanvasSVG.css('filter', 'none' );
    }
    
    //  Symbol以外はGroupに線を追加
    if( layerData.type !== 'symbol' && layerData.type !== 'image' ) {
      $layerGroup.css({
        'stroke' : layerData.stroke,
        'stroke-opacity' : ' ' + layerData.strokeOpacity,
        'stroke-width' : layerData.strokeWidth,
        'paint-order' : layerData.paintOrder,
        'stroke-linecap' : layerData.strokeCap,
        'stroke-linejoin' : layerData.strokeJoin
      });
    }
        
    // カラー表示更新
    $('#property-color').next('label').css('background-color', artBoardJSON[ g_selectedLayer ].fill );
    $('#property-border-color').next('label').css('background-color', artBoardJSON[ g_selectedLayer ].stroke );
    $('#property-filter-shadow-color').next('label').css('background-color', artBoardJSON[ g_selectedLayer ].filterShadowColor );
        
    // タイプ別の設定
    if ( layerData.type === 'text') {

      $layerGroup.css({
        'font-size' : layerData.fontsize + 'px',
        'font-weight' : layerData.fontweight,
        'font-family' : '"' + layerData.font + '"'
      });
      $layerShape.text( layerData.text );
      
      // フォント指定後にサイズを調整する
      const textBBox = $layerGroup.find('text').get(0).getBBox();
      if( browser === 'edge' ) {
        artBoardJSON[ layerID ].height = textBBox.height - ( layerData.strokeWidth * 2 );
      } else {
        artBoardJSON[ layerID ].height = textBBox.height;
      }
      artBoardJSON[ layerID ].width = textBBox.width;
      
      // サイズを元に調整
      layerWidth = artBoardJSON[ layerID ].width;
      layerHeight = artBoardJSON[ layerID ].height;
      
      $layerShape.attr({
        'y' : ( layerData.fontsize / 3 ) + ( layerHeight / 2 )
      });
      
    } else if ( layerData.type === 'symbol' ) {
    
      $('#property-color2').next('label').css('background-color', artBoardJSON[ g_selectedLayer ].fill2 );
      $('#property-color3').next('label').css('background-color', artBoardJSON[ g_selectedLayer ].fill3 );
      
      // st0 Border
      $layerGroup.find('.st0').attr('vector-effect', 'non-scaling-stroke').css({
        'fill' : 'none',
        'stroke' : layerData.stroke,
        'stroke-opacity' : ' ' + layerData.strokeOpacity,
        'stroke-width' : layerData.strokeWidth,
        'paint-order' : layerData.paintOrder,
        'stroke-linecap' : layerData.strokeCap,
        'stroke-linejoin' : layerData.strokeJoin
      });
      $layerGroup.find('.st1').css({
        'fill' : layerData.fill2,
        'fill-opacity' : layerData.opacity2
      });
      $layerGroup.find('.st2').css({
        'fill' : layerData.fill3,
        'fill-opacity' : layerData.opacity3
      });
      
    } else if ( layerData.type === 'image' ) {
      let imageSize = layerData.imageSize;
      if ( imageSize === 'full' ) {
        imageSize = '100% 100%';
      }
      $layerShape.css({
        'background-image' : 'url(' + layerData.imageURL + ')',
        'background-size' : imageSize,
        'background-position' : 'center center',
        'background-repeat' : layerData.imageRepeat
      });
    }
    
    // ポジション調整
    let originX = layerData.x + ( g_canvas.w / 2 ),
        originY = layerData.y + ( g_canvas.h / 2 ),
        positionX = layerData.x + ( g_canvas.w / 2 - layerWidth / 2 ),
        positionY = layerData.y + ( g_canvas.h / 2 - layerHeight / 2 );
    
    // 状態適用
    $layerCanvasSVG.css({
      'transform' : transform,
      'transform-origin' : originX + 'px ' + originY + 'px 0'
    });
    if ( layerData.type !== 'image' ) {
      $layerSVG.attr({
        'x' : positionX,
        'y' : positionY,
        'width' : layerWidth,
        'height' : layerHeight
      });
      $layerGroup.css({
        'transform' : transformScale,
        'transform-origin' : 'center center 0',
        'fill' : layerData.fill,
        'fill-opacity' : layerData.opacity
      });
    } else {
      $layerSVG.css({
        'transform' : transformScale,
        'transform-origin' : 'center center 0',
        'position' : 'absolute',
        'left' : positionX + 'px',
        'top' : positionY + 'px',
        'width' : layerWidth + 'px',
        'height' : layerHeight + 'px'
      });
    }
    
    // バウンディングボックス
    $boundingBox.find('.layer-svg').remove();
    
    // テキスト以外は元の要素をコピーして整える
    if ( layerData.type !== 'text') {
      const copyLayer = $( $layerSVG.prop('outerHTML') );
      $boundingBox.find('.layer-canvas-svg').prepend( copyLayer );
      $boundingBox.find('.layer-svg .layer-group').removeAttr('style').children().removeAttr('style').not('.st0').remove();
    }
    $boundingBox.find('.layer-canvas-svg').css({
      'transform' : transform,
      'transform-origin' : originX + 'px ' + originY + 'px'
    })
    .find('.layer-group').css({
      'transform' : transformScale,
      'transform-origin' : "center center 0"
    })
    $boundingBox.find('.layer-svg, .outer-line').attr({
      'x' : positionX - 0.5,
      'y' : positionY - 0.5,
      'width' : layerWidth + 1,
      'height' : layerHeight + 1
    });
    
}
////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   バウンディングボックス サイズ変更
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
/*
const boundingBox = function() {
}
*/


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   入力更新
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$panelContainer.on('input', 'input', function(){

    const $input = $( this ),
          inputType = $input.attr('id');
          
    let inputVal = $input.val();
    
    // ファイル名処理
    if ( inputType === 'document-name' ) {
      // カーソルの位置を取得
      const selectValue = $input.get(0).selectionStart;
      // ファイル名に使用できない文字を置換
      // [先頭の.] [¥] [/] [:] [*] [?] ["] [<] [>] [|] [~]
      inputVal = inputVal.replace(/^\.|~|\*|\:|\?|"|<|>|\||\\/g, '_');
      // input入れ替えてカーソルの位置を戻す
      $input.val( inputVal ).get(0).setSelectionRange( selectValue, selectValue );
    }
    
    // 0以上チェック
    let targetInput = [
      'property-width', 'property-height',
      'property-border-width', 'property-border-opacity',
      'property-opacity', 'property-opacity2', 'property-opacity3',
      'property-filter-opacity', 'property-filter-brightness', 'property-filter-contrast',
      'property-filter-grayscale', 'property-filter-sepia', 'property-filter-saturate',
      'property-filter-invert', 'property-filter-blur',
      'property-filter-shadow-blur',
    ];
    if ( targetInput.indexOf( inputType ) !== -1 ) {
      if ( Number( inputVal ) < 0 ) inputVal = 0;
      $input.val( inputVal );
    }
    // 100以下チェック
    targetInput = [
      'property-border-opacity',
      'property-opacity', 'property-opacity2', 'property-opacity3',
      'property-filter-opacity', 'property-filter-contrast',
      'property-filter-grayscale', 'property-filter-sepia', 'property-filter-saturate',
      'property-filter-invert', 'property-filter-blur',
      'property-filter-shadow-blur',
    ];
    if ( targetInput.indexOf( inputType ) !== -1 ) {
      if ( Number( inputVal ) > 100 ) inputVal = 100;
      $input.val( inputVal );
    }
    // 1以上チェック
    targetInput = [
      'property-font-size'
    ];
    if ( targetInput.indexOf( inputType ) !== -1 ) {
      if ( Number( inputVal ) < 1 ) inputVal = 1;
      $input.val( inputVal );
    }
    // 角度チェック
    targetInput = [
      'property-rotate',
      'property-skew',
      'property-filter-hue'
    ];
    if ( targetInput.indexOf( inputType ) !== -1 ) {
      if ( Number( inputVal ) < -360 ) inputVal = 360;
      if ( Number( inputVal ) > 360 ) inputVal = -360;
      $input.val( inputVal );
    }

    switch( inputType ) {
      // Document
      case 'document-name': artBoardJSON['config'].documentName = inputVal; break;
      // Common
      case 'property-layer-name': artBoardJSON[ g_selectedLayer ].name = inputVal; break;
      case 'property-color': artBoardJSON[ g_selectedLayer ].fill = inputVal; break;     
      case 'property-x': artBoardJSON[ g_selectedLayer ].x = Number( inputVal ); break;
      case 'property-y': artBoardJSON[ g_selectedLayer ].y = Number( inputVal ); break;
      case 'property-width': artBoardJSON[ g_selectedLayer ].width = Number( inputVal ); break;
      case 'property-height': artBoardJSON[ g_selectedLayer ].height = Number( inputVal ); break;
      case 'property-opacity': artBoardJSON[ g_selectedLayer ].opacity = Number( inputVal ) / 100; break;
      // Text
      case 'property-text':
        artBoardJSON[ g_selectedLayer ].text = inputVal;
        $('#property-text-ime').val( inputVal );
        break;
      case 'property-text-ime':
        $('#property-text').val( inputVal ).trigger('input');
        break;
      case 'property-font-size': artBoardJSON[ g_selectedLayer ].fontsize = Number( inputVal ); break;
      case 'property-font': artBoardJSON[ g_selectedLayer ].font = inputVal; break;
      // Border
      case 'property-border-width':artBoardJSON[ g_selectedLayer ].strokeWidth = Number( inputVal ); break;
      case 'property-border-position': artBoardJSON[ g_selectedLayer ].paintOrder = inputVal; break;
      case 'property-border-color': artBoardJSON[ g_selectedLayer ].stroke = inputVal; break;
      case 'property-border-opacity': artBoardJSON[ g_selectedLayer ].strokeOpacity = Number( inputVal ) / 100; break;
      // Transform
      case 'property-scale-x': artBoardJSON[ g_selectedLayer ].scaleX = Number( inputVal ) / 100; break;
      case 'property-scale-y': artBoardJSON[ g_selectedLayer ].scaleY = Number( inputVal ) / 100; break;
      case 'property-rotate':  artBoardJSON[ g_selectedLayer ].rotate = Number( inputVal ); break;
      case 'property-skew': artBoardJSON[ g_selectedLayer ].skew = Number( inputVal ); break;
      // Symbol
      case 'property-symbol-id': artBoardJSON[ g_selectedLayer ].symbolID = inputVal; break;
      case 'property-color2': artBoardJSON[ g_selectedLayer ].fill2 = inputVal; break;
      case 'property-opacity2': artBoardJSON[ g_selectedLayer ].opacity2 = Number( inputVal ) / 100; break;
      case 'property-color3': artBoardJSON[ g_selectedLayer ].fill3 = inputVal; break;
      case 'property-opacity3': artBoardJSON[ g_selectedLayer ].opacity3 = Number( inputVal ) / 100; break;
      // Filter
      case 'property-filter-blur': artBoardJSON[ g_selectedLayer ].filterBlur = Number( inputVal ); break;
      case 'property-filter-opacity': artBoardJSON[ g_selectedLayer ].filterOpacity = Number( inputVal ) / 100; break;
      case 'property-filter-brightness': artBoardJSON[ g_selectedLayer ].filterBrightness = Number( inputVal ) / 100; break;
      case 'property-filter-contrast': artBoardJSON[ g_selectedLayer ].filterContrast = Number( inputVal ); break;
      case 'property-filter-grayscale': artBoardJSON[ g_selectedLayer ].filterGrayscale = Number( inputVal ); break;
      case 'property-filter-sepia': artBoardJSON[ g_selectedLayer ].filterSepia = Number( inputVal ); break;
      case 'property-filter-saturate': artBoardJSON[ g_selectedLayer ].filterSaturate = Number( inputVal ); break;
      case 'property-filter-invert': artBoardJSON[ g_selectedLayer ].filterInvert = Number( inputVal ); break;
      case 'property-filter-hue': artBoardJSON[ g_selectedLayer ].filterHue = Number( inputVal ); break;
      case 'property-filter-shadow-color': artBoardJSON[ g_selectedLayer ].filterShadowColor = inputVal; break;
      case 'property-filter-shadow-blur': artBoardJSON[ g_selectedLayer ].filterShadowBlur = Number( inputVal ); break;
      case 'property-filter-shadow-x': artBoardJSON[ g_selectedLayer ].filterShadowX = Number( inputVal ); break;
      case 'property-filter-shadow-y': artBoardJSON[ g_selectedLayer ].filterShadowY = Number( inputVal ); break;
      // Image
      case 'property-image-url': artBoardJSON[ g_selectedLayer ].imageURL = inputVal; break;
      
      default:
    }

    updateLayer( g_selectedLayer );

}).on('change', 'select', function(){

    const $select = $( this ),
          selectType = $select.attr('id');

    switch( selectType ) {
      case 'property-font-weight':
        artBoardJSON[ g_selectedLayer ].fontweight = $select.find('option:selected').val();
        break;
      case 'property-border-position':
        artBoardJSON[ g_selectedLayer ].paintOrder = $select.find('option:selected').val();
        break;
      case 'property-border-cap':
        artBoardJSON[ g_selectedLayer ].strokeCap = $select.find('option:selected').val();
        break;
      case 'property-border-join':
        artBoardJSON[ g_selectedLayer ].strokeJoin = $select.find('option:selected').val();
        break;
      case 'property-image-size':
        artBoardJSON[ g_selectedLayer ].imageSize = $select.find('option:selected').val();
        break;
      case 'property-image-repeat':
        artBoardJSON[ g_selectedLayer ].imageRepeat = $select.find('option:selected').val();
        break;
    }
    
    updateLayer( g_selectedLayer );
    
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   レイヤーの移動
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$canvasWindow.on('mousedown.layer', '.layer-svg', function( e ){

    e.preventDefault();

    if ( e.button === 0 ) {
    
      e.stopPropagation();

      const $layerSVG = $( this ).closest('.art-board-layer'),
            layerID = $layerSVG.attr('id'),
            beforeX = artBoardJSON[ layerID ].x,
            beforeY = artBoardJSON[ layerID ].y,
            mouseDownPositionX = e.pageX,
            mouseDownPositionY = e.pageY,
            snapPixel = 8;

      let moveX = 0,
          moveY = 0;
          
      selectLayer( layerID, true );

      $( window ).on({
          'mousemove.canvas': function( e ){

            moveX = Math.round( ( e.pageX - mouseDownPositionX ) / editorValue.scaling );
            moveY = Math.round( ( e.pageY - mouseDownPositionY ) / editorValue.scaling );
            
            // センターにスナップ
            if ( beforeX + moveX < snapPixel && beforeX + moveX > -snapPixel ) moveX = - beforeX;
            if ( beforeY + moveY < snapPixel && beforeY + moveY > -snapPixel ) moveY = - beforeY;
            
            $('#property-x').val( beforeX + moveX );
            $('#property-y').val( beforeY + moveY );
            
            $statusMoveX.text( moveX + 'px' );
            $statusMoveY.text( moveY + 'px' );
            
            $layerSVG.css('transform', 'translate3d(' + moveX + 'px,' + moveY + 'px,0)');
          },
          'mouseup.canvas': function(){
            $( this ).off('mousemove.canvas mouseup.canvas');
            $layerSVG.css('transform', 'none');
            artBoardJSON[ layerID ].x = moveX + beforeX;
            artBoardJSON[ layerID ].y = moveY + beforeY;
            updateLayer( layerID );
            propertyUpdate();
            statusUpdate();
          }
        });

    }

}).on('mousedown', function( e ){
    
    if ( e.button === 0 ) {
      deselectLayer();
    }
    
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期表示レイヤー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
newLayer('bounding-box');
const $boundingBox = $('#bounding-box');

newLayer('shape');
newLayer('symbol');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   パネル用画像メニュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$editorMenu.on('click', 'button', function(){

    const buttonType = $( this ).attr('data-menu');
          
    switch ( buttonType ) {
      case 'save-icon-data':
        saveItaIconFile();
        break;
      case 'load-icon-data':
        loadItaIconFile();
        break;
      case 'output-icon-data':
        outputImage();
        break;
    }

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   保存
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const saveItaIconFile = function() {

  const blobText = new Blob( [ JSON.stringify( artBoardJSON ) ], { type : 'text/plain' }),
        $downloadAnchor = $('<a />');
  
  // ファイルネーム
  let filename = artBoardJSON['config'].documentName;
  if ( filename === undefined || filename === '' ) {
    filename = 'noname';
  }
  
  // 一時リンクを作成しダウンロード
  $downloadAnchor.attr({
    'href' : window.URL.createObjectURL( blobText ),
    'download' : filename + '.' + panelImageValue.saveExtension,
    'target' : '_blank'
  });
  $workspace.prepend( $downloadAnchor );
  $downloadAnchor.get(0).click();
  
  // 生成したBlobを削除しておく
  setTimeout( function(){
    $downloadAnchor.remove()
    window.URL.revokeObjectURL( blobText );
  }, 100 );
  
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   読み込み
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const $loadIconInput = $('#load-icon-data-input');

$loadIconInput.on('change', function( e ){

const itaIconFile = e.target.files[0];

if ( itaIconFile ) {
  const fileReader = new FileReader();
  fileReader.readAsText( itaIconFile );
  fileReader.onload = function() {
    
    artBoardJSON = JSON.parse( fileReader.result );
    deselectLayer();
    
    // 全てのレイヤーを消す
    $artBoard.find('.art-board-layer').remove();
    $layerList.find('.layer-list-li').remove();
    
    // アートボードサイズリセット
    $artBoardWidth.val( artBoardJSON['config'].documentWidth );
    $artBoardHeight.val( artBoardJSON['config'].documentHeight );
    artBoardReset();
    
    // カウンター引き継ぎ
    g_layerCounter = artBoardJSON['config'].layerCounter;
    
    // 一旦配列に入れる
    let layerArray = [],
        index = 0;
    for ( let key in artBoardJSON ) {
      if ( key !== 'config' ) {
        layerArray[ index ] = artBoardJSON[ key ];
        index++;
      }
    }

    // z-indexの順番でソートする
    layerArray.sort( function( a, b ) {
      return a.zIndex - b.zIndex;
    });
    
    // レイヤー追加
    for ( let i = 0; i < layerArray.length; i++ ) {
      setLayer( layerArray[ i ].id , 'load' );
    }
  }
}

});

const loadItaIconFile = function() {
  $loadIconInput.val('');
  $loadIconInput.get(0).click();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   キャンバスに書き出し html2canvas
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const $output = $('#image-output'),
      $outputCanvas = $('#image-canvas');

const outputImage = function() {
  
  // html2canvas Options
  const html2canvasOptions = {
    width : g_artBoard.w,
    height : g_artBoard.h,
    backgroundColor : null
  }

  // ファイルネーム
  let filename = artBoardJSON['config'].documentName;
  if ( filename === undefined || filename === '' ) {
    filename = 'noname';
  }
  $output.find('.image-output-name').text( filename + '.png' );
  
  $output.show();
  $outputCanvas.addClass('loading').css({
    'width' : html2canvasOptions.width,
    'height' : html2canvasOptions.height
  });
  $workspace.find('.output-ignore').css('visibility', 'hidden');
  
  // スクロールを一旦リセット
  const scrollTop = $( window ).scrollTop();
  window.scrollTo( 0, 0 );
  
  // 描画位置調整
  $canvas.css('transform', 'scale(1)');
  $canvasWindow.css({
    'position' : 'fixed',
    'left' : -99999,
    'top' : 0
  });
  
  $artBoard.addClass('output');
  
  html2canvas( $artBoard.get( 0 ), html2canvasOptions ).then( function( canvas ) {
      $outputCanvas.removeClass('loading').html( canvas );
      $workspace.find('.output-ignore').css('visibility', 'visible');
      $canvasWindow.removeAttr('style');
      // IE or Edge とそれ以外
      if ( canvas.msToBlob ) {
        $output.find('.download-png').off('click').on('click', function(){
          const blob = canvas.msToBlob();
          window.navigator.msSaveBlob(blob, filename + '.png');
        });
      } else {
        $output.find('.download-png').attr({
          'href' : canvas.toDataURL(),
          'download' : filename + '.png'
        });
      }
      $canvas.css('transform', 'scale(' + editorValue.scaling + ')');
  });
  $( window ).scrollTop( scrollTop );
  
}

$output.on('click', 'button', function(){
    if ( $( this ).is('.cancel') ) {
      $('#image-canvas').find('canvas').remove();
      $artBoard.removeClass('output');
      $output.hide();
    } 
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   特殊文字リスト
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// IME Unicode Start - End
const imeArray = {
  "Currency symbol": ["20A0","20BF"],
  "Other symbol": ["2600","26FF"],
  "Decorative symbol": ["2700","27BF"],
  "Arrow": ["2190","21FF"],
  "Auxiliary arrow A": ["27F0","27FF"],
  "Auxiliary arrow B": ["2900","297F"],
  "Mathematical symbols": ["2200","22FF"],
  "Other mathematical symbol A": ["27C0","27FF"],
  "Other mathematical symbol B": ["2980","29FF"],
  "Auxiliary mathematical symbol": ["2A00","2AFD"],
  "Other symbol and arrow": ["2B00","2BD1"],
  "Egyptian hieroglyph": ["13000","1342F"],
  "Mahjong tile": ["1F000","1F02B"],
  "Domino tile": ["1F030","1F093"],
  "Playing card": ["1F0A0","1F0DF"],
  "Other symbol / emoji": ["1F300","1F5FF"],
  "Emoticon": ["1F600","1F64F"],
  "Traffic / map symbol": ["1F680","1F6F8"]
};

// 配列からSelect Optionを作成
let imeOptions = '';
for ( let key in imeArray ) {
  imeOptions += '<option value="' + key + '">' + key + '</option>';
}
$('#property-ime-type').html( imeOptions ).on('change', function(){
  changeType( $( this ).val() );
});

// IMEリスト出力
const changeType = function( type ) {
    const emojiStartNum = parseInt(imeArray[ type ][0], 16 ),
          emojiEndNum   = parseInt(imeArray[ type ][1], 16 );

    let emojiHTML = '';
    for ( let emoji = emojiStartNum; emoji <= emojiEndNum; emoji++ ) {
      emojiHTML += '<li class="ime-list-li">&#x' + emoji.toString(16) + ';</li>';
    }
    $('#editor-ime').html( '<ul class="ime-list">' + emojiHTML + '</ul>' );
    
    $('#property-ime-type').val( type );
}
changeType('Emoticon');


let inputSelectPositionStart = 0,
    inputSelectPositionEnd = 0;
$('#property-text-ime').on('focus', function() {
  const $input = $( this );
  
  inputSelectPositionStart = $input.get(0).selectionStart;
  inputSelectPositionEnd =  $input.get(0).selectionEnd;
  
  $( window ).on('mouseup.imeText keyup.imeText', function(){
    inputSelectPositionStart = $input.get(0).selectionStart;
    inputSelectPositionEnd =  $input.get(0).selectionEnd;
  });      
 
}).on('blur', function(){
  $( window ).off('mouseup.imeText keyup.imeText');
}).on('mousedown', function(){
  window.getSelection().removeAllRanges();
});

// Textの最後に追加する
$('#editor-ime').on('click', 'li', function(){
  const $text = $layerProperty.find('#property-text'),
        textLength = $text.val().length,
        beforeText = $text.val().substr( 0, inputSelectPositionStart ),
        afterText = $text.val().substr( inputSelectPositionEnd, textLength ),
        emoji = $( this ).text(),
        emojiLength = emoji.length;
  
  // カーソル位置調整
  inputSelectPositionStart += emojiLength;
  inputSelectPositionEnd = inputSelectPositionStart;
  
  $text.val( beforeText + emoji + afterText ).trigger('input');
  window.getSelection().removeAllRanges();
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Image用画像読み込み
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const $loadImageInput = $('#load-image-input'),
      alertMaxSize = ( panelImageValue.imageFileMaximumSize / 1024 ).toLocaleString();

$loadImageInput.on('change', function( e ){

const loadImageFile = e.target.files[0];

if ( loadImageFile ) {
  // サイズチェック
  if ( loadImageFile.size < panelImageValue.imageFileMaximumSize ) {
  
  const imageFileReader = new FileReader();
  imageFileReader.readAsDataURL( loadImageFile );
  imageFileReader.onload = function() {
    
    artBoardJSON[ g_selectedLayer ].imageURL = imageFileReader.result;
    updateLayer( g_selectedLayer );
    propertyUpdate();
  }
  
  } else {
    alert('The specified file exceeds the maximum allowable size of ' + alertMaxSize +  'KB');
  }
}

});

const loadImageFile = function() {
  $loadImageInput.val('');
  $loadImageInput.get(0).click();
}
$('#property-image-load').on('click', function(){
  loadImageFile();
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   一覧/更新テーブル 画像プレビュー
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const $imagePreviousBox = $('<div id="image-previus-box"><img src="#" alt="image previous"></div>');

$('#Mix1_Nakami').on({
    'mouseenter' : function( e ){

      const imageSrc = $( this ).attr('href'),
            scrollTop = $window.scrollTop(),
            scrollLeft = $window.scrollLeft();
            
      $('#INDEX').append( $imagePreviousBox );
      
      $imagePreviousBox.css({
        'left' : e.pageX - scrollLeft,
        'top' : e.pageY - scrollTop
      }).find('img').attr('src', imageSrc );

    },
    'mouseleave' : function(){
      $imagePreviousBox.remove();
    }
}, 'a' );




} else {

  $workspace.remove();

}

// $function
});