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

/* ---------------------------------------------------------------------------------------------------- *

   01.エディタ共通

 * ---------------------------------------------------------------------------------------------------- */
 
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

// エディター値
let editorValue = {
  'scaling' : editorInitial.scaling,
  'oldScaling' : editorInitial.scaling
}

// jQueryオブジェクトをキャッシュ
const $workspace = $('#workspace'),
      $editorMenu = $workspace.find('.editor-menu'),
      $canvasWindow = $workspace.find('.canvas-window'),
      $canvas = $workspace.find('.canvas'),
      $artBoard = $workspace.find('.art-board'),
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
  'h' : $obj.height()
  }
}
const canvasPositionReset = function() {

    g_canvasWindow = setSize( $canvasWindow );
    g_canvas = setSize( $canvas );
    g_artBoard = setSize( $artBoard );
    editorValue.scaling = editorInitial.scaling;
    editorValue.oldScaling = editorInitial.scaling;

    g_canvas_p = {
      'x' : - ( g_canvas.w / 2 ) + ( g_canvasWindow.w / 2 ),
      'y' : - ( g_canvas.h / 2 ) + ( g_canvasWindow.h / 2 ),
      'cx' : - ( g_canvas.w / 2 ) + ( g_canvasWindow.w / 2 ),
      'cy' : - ( g_canvas.h / 2 ) + ( g_canvasWindow.h / 2 )
    };
    g_artBoard_p = {
      'x' : ( g_canvas.w / 2 ) - ( g_artBoard.w / 2 ),
      'y' : ( g_canvas.h / 2 ) - ( g_artBoard.h / 2 )
    };
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
    
}

// モード変更
const modeChange = function( mode ) {
    const modeAttr = 'data-mode';
    if ( mode !== 'clear' ) {
      $workspace.attr( modeAttr, mode );
    } else {
      $workspace.removeAttr( modeAttr );
    }
}
const mode = {
  'canvasMove' : function() { modeChange('canvas-move'); },
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

      if ( e.button === 2 ) {
      
        e.preventDefault();

        const mouseDownPositionX = e.pageX,
              mouseDownPositionY = e.pageY;
              
        let moveX = 0,
            moveY = 0;
            
        mode.canvasMove();

        $( window ).on({
          'mousemove.canvas': function( e ){

            moveX = e.pageX - mouseDownPositionX;
            moveY = e.pageY - mouseDownPositionY;
            
            $statusViewX.text( Math.floor( g_canvas_p.x - g_canvas_p.cx + moveX ) + 'px' );
            $statusViewY.text( Math.floor( g_canvas_p.y - g_canvas_p.cy + moveY ) + 'px' );
            $statusMoveX.text( moveX + 'px' );
            $statusMoveY.text( moveY + 'px' );
            
            $canvas.css({
              'transform' : 'translate3d(' + moveX + 'px,' + moveY + 'px,0) scale(' + editorValue.scaling + ')'
            });

          },
          'contextmenu.canvas': function( e ) {
            e.preventDefault();
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
      e.preventDefault();
    },
    'mouseenter': function() {
      
    },
    'mouseleave': function() {

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
            
      g_canvas_p.cx = g_canvas_p.cx - commonX;
      g_canvas_p.cy = g_canvas_p.cy - commonY;

      if ( zoomType === 'in') {
        g_canvas_p.x = g_canvas_p.x - commonX + adjustX;
        g_canvas_p.y = g_canvas_p.y - commonY + adjustY;
      } else if ( zoomType === 'out') {
        g_canvas_p.x = g_canvas_p.x - commonX - adjustX;
        g_canvas_p.y = g_canvas_p.y - commonY - adjustY;
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

    const mousePositionX = ( e.pageX - $( this ).offset().left - g_canvas_p.x ) / editorValue.scaling,
          mousePositionY = ( e.pageY - $( this ).offset().top - g_canvas_p.y ) / editorValue.scaling,
          delta = e.originalEvent.deltaY ? - ( e.originalEvent.deltaY ) : e.originalEvent.wheelDelta ? e.originalEvent.wheelDelta : - ( e.originalEvent.detail );

    if ( e.shiftKey ) {
      // 横スクロール
      if ( delta < 0 ){
        //
      } else {
        //
      }

    } else {
      // 縦スクロール
      if ( delta < 0 ){
        canvasScaling( 'out', mousePositionX, mousePositionY );
      } else {
        canvasScaling( 'in', mousePositionX, mousePositionY);
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

    $statusScale.text( Math.floor( editorValue.scaling * 100 ) + '%' );
    $statusViewX.text( Math.floor( g_canvas_p.x - g_canvas_p.cx ) + 'px' );
    $statusViewY.text( Math.floor( g_canvas_p.y - g_canvas_p.cy ) + 'px' );
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
          buttonDisabledTime = 1000;

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

    setTimeout( function(){
      $button.prop('disabled', false );
    }, buttonDisabledTime );

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Window リサイズでキャンバスリセット
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const reiszeEndTime = 200;
let resizeTimerID;
$( window ).on('resize.editor', function(){

    clearTimeout( resizeTimerID );

    resizeTimerID = setTimeout( function(){
      canvasPositionReset();
    }, reiszeEndTime );

});





if ( editorType === 'symphony' ) {
/* ---------------------------------------------------------------------------------------------------- *

   02.シンフォニーエディタ

 * ---------------------------------------------------------------------------------------------------- */

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 初期値
const panelImageValue = {
  'workspaceHeight' : 800,
  'canvasWidth' : 5400,
  'canvasHeight' : 5400,
  'artboradWidth' : 5000,
  'artboradHeight' : 5000
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
canvasPositionReset();




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
canvasPositionReset();

// jQuery オブジェクトをキャッシュ
const $layerMenu = $('#layer-menu'),
      $layerList = $('#layer-list'),
      $panelContainer = $('#panel-container'),
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

    $( window ).on({
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

            moveX = Math.floor( ( e.pageX - mouseDownPositionX ) / editorValue.scaling );
            moveY = Math.floor( ( e.pageY - mouseDownPositionY ) / editorValue.scaling );
            
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
            $window = $( window ),
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