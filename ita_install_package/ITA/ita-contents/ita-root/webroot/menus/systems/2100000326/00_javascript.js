//   Copyright 2021 NEC Corporation
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
    getERJSON : function( result ){
        var ary_result = getArrayBySafeSeparator( result );
        
        // 正常の場合
        if ( ary_result[0] == "000" ){
            createMenuGroupDiagram( JSON.parse( ary_result[2] ));
        }
        // システムエラーの場合
        else {
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
    }
}

var proxy = new Db_Access(new callback());

window.onload = function(){
  proxy.getERJSON();
}



//////// メニューグループ図の作成 ////////
function createMenuGroupDiagram( menuGroupList ) {

'use strict';

const editor = new itaEditorFunctions();

// jQueryオブジェクトをキャッシュ
const $window = $( window ),
      $body = $('body'),
      $editor = $('#editor'),
      $canvasVisibleArea = $('#canvas-visible-area'),
      $canvas = $('#canvas'),
      $artBoard = $('#art-board');

$editor.removeClass('load-wait');

// 初期値
const initialValue = {
  'canvasWidth': 16400,
  'canvasHeight': 16400,
  'artboradWidth': 16000,
  'artboradHeight': 16000,
  'debug': false
};

// エディター値
const editorValue = {
  'scaling' : 1,
  'oldScaling' : 1
};

// メニューグループ非表示 リスト用
let menuGroupHideList = new Array();

// 初期状態で表示しないメニューグループ
menuGroupHideList.push('2100000002');

// リレーションリスト用
let relationList = new Array();

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
    canvasPosition( -g_artBoard_p.x, -g_artBoard_p.y, 1, duration );
    
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

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   マウスホイールで拡縮
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
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

const entityViewAll = function( duration ) {
  
  const canvasWidth = $canvasVisibleArea.width(),
        canvasHeight = $canvasVisibleArea.height();
        
  // 端の座標を求める
  let x1, y1, x2, y2;
  $('.menu-group').each( function(){
    const $mg = $( this ),
          mgW = $mg.outerWidth(),
          mgH = $mg.outerHeight(),
          mgX = Number( $mg.css('left').replace('px','') ),
          mgY = Number( $mg.css('top').replace('px','') );
      // 左上座標
      if ( x1 > mgX || x1 === undefined ) x1 = mgX;
      if ( y1 > mgY || y1 === undefined ) y1 = mgY;
      // 右下座標
      if ( x2 < mgX + mgW || x2 === undefined ) x2 = mgX + mgW;
      if ( y2 < mgY + mgH || y2 === undefined ) y2 = mgY + mgH;
  });
  
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
  
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   SVGエリアの作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const xmlns = 'http://www.w3.org/2000/svg',
      $svgArea = $( document.createElementNS( xmlns, 'svg') ),
      $svgAreaTtranslucent = $( document.createElementNS( xmlns, 'svg') ),
      $svgMark = $( document.createElementNS( xmlns, 'svg') );

const setSvgArea = function() {
  
    $svgArea.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );
    $svgAreaTtranslucent.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );
    $svgMark.get(0).setAttribute('viewBox', '0 0 ' + g_artBoard.w + ' ' + g_artBoard.h );

    $artBoard.prepend( $svgArea, $svgAreaTtranslucent, $svgMark );
    $svgArea.attr({
      'id' : 'svg-area',
      'width' : g_artBoard.w,
      'height' : g_artBoard.h
    });
    $svgAreaTtranslucent.attr({
      'id' : 'svg-translucent',
      'width' : g_artBoard.w,
      'height' : g_artBoard.h
    });
    $svgMark.attr({
      'id' : 'svg-mark',
      'width' : g_artBoard.w,
      'height' : g_artBoard.h
    });

}

let g_EdgeCounter = 0;
const edgeCounter = function() { return g_EdgeCounter++; };

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Entityの作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
function createEntityHTML() {

    relationList = []
    let html = '';
    const menuURL = '/default/mainmenu/01_browse.php?grp=',
          menuGroupURL = '/default/menu/01_browse.php?no=',
          menuGroupLength = menuGroupList.MENU_GROUP.length,
          entityCheck = new Array();
    
    for ( let i = 0; i < menuGroupLength; i++ ) {
    
        const menuGroupData = menuGroupList.MENU_GROUP[i],
              menuGroupID = menuGroupData.ID,
              menuGroupName = editor.textEntities( menuGroupData.NAME, false ).replace(/\s/g, '&nbsp;'),
              menuLength = menuGroupData.MENU.length;

        // Menu順番ソート
        menuGroupData.MENU.sort( function( a, b ){
          return ( Number( a.DISP_SEQ ) > Number( b.DISP_SEQ ) )? 1: -1;
        });
        
        if ( menuGroupHideList.indexOf( menuGroupID ) === -1 ) {

            html += '<div id="menu-group' + menuGroupID + '" class="menu-group">'
                + '<div class="menu-group-header">'
                  + '<div class="menu-group-name">'
                    + '<div class="menu-group-name-inner">'
                      + '<a href="' + menuURL + menuGroupID + '" target="_blank">'+ menuGroupName + '</a>'
                    + '</div>'
                  + '</div>'
                + '</div>'
                + '<div class="menu-group-body">'
                  + '<div class="menu-group-body-inner" style="displya: inline-block;">';

            for ( let j = 0; j < menuLength; j++ ) {

                const menuData = menuGroupData.MENU[j],
                      entityID = menuData.ID,
                      entityTitle = editor.textEntities( menuData.NAME, false ).replace(/\s/g, '&nbsp;'),
                      topColumns = menuData.COLUMNS,
                      groupItem = menuData.GROUP_ITEM;
                
                if ( entityCheck.indexOf( entityID ) === -1 ) {
                
                    entityCheck.push( entityID );

                    html += '<div id="entity' + entityID + '" class="entity">';

                    html += ''
                      + '<div class="entity-header">'
                        + '<div class="entity-name">'
                          + '<a href="' + menuGroupURL + entityID + '" target="_blank">' + entityTitle + '</a>'
                        + '</div>'
                      + '</div>'

                    const columnsHTML = function( columns ){
                      const length = columns.length;
                      for ( let k = 0; k < length; k++ ) {
                        const column = columns[k],
                              type = groupItem[column].TYPE,
                              itemName = editor.textEntities( groupItem[column].LOGICAL_NAME, false );
                        if ( type === 'ITEM' ) {
                          html += '<div id="e' + entityID + '__' + groupItem[column].PHYSICAL_NAME + '" class="entity-item">';
                          html += itemName;
                          html += '</div>';
                          // リレーション情報
                          if ( groupItem[column].RELATION_MENU_ID.length > 0 && groupItem[column].RELATION_COLUMN_ID !== '' ) {
                            relationList.push([
                              groupItem[column].RELATION_MENU_ID,
                              groupItem[column].RELATION_COLUMN_ID,
                              entityID + '__' + groupItem[column].PHYSICAL_NAME
                            ]);
                          }
                        } else if ( type === 'GROUP' ) {
                          html += ''
                            + '<div class="entity-group">'
                              + '<div class="entity-group-name">' + itemName + '</div>';
                          columnsHTML( groupItem[column].COLUMNS );
                          html += '</div>';
                        }
                      }
                    };

                    html += '<div class="entity-body"><div class="entity-body-inner"><div class="entity-table">';
                    columnsHTML( topColumns );
                    html += '</div></div></div>';

                    html += '</div>';
                
                }

            }

            html += '</div></div></div><div id="menu-group' + menuGroupID + '-back" class="menu-group-back"></div>';
        
        }
    
    }

    return html;

}

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
          $svgLineT = $( document.createElementNS( xmlns, 'path') ),
          $svgLineBack = $( document.createElementNS( xmlns, 'path') );
    $svgLine.attr('class', 'svg-line');
    $svgLineT.attr({
      'class': 'svg-line-translucent',
      'id' : svgID + '-t',
    });
    $svgLineBack.attr('class', 'svg-line-back');
    
    // マーカーを作成
    const $circle = $( document.createElementNS( xmlns, 'circle') ),
          $arrow = $( document.createElementNS( xmlns, 'path') );
    $circle.attr({'r': 4, 'class': 'svg-mark'});
    $arrow.attr({'d': '', 'class': 'svg-arrow'});
    
    // SVGエリアに追加
    $svgArea.append( $svgGroup.append( $svgLineBack, $svgLine ) );
    $svgAreaTtranslucent.append( $svgLineT );
    $svgMark.append( $circle, $arrow )
    
    return {'line': $svgGroup, 'mark': $circle, 'arrow': $arrow, 't': $svgLineT };
}

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
//   メニューグループを並べる
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
function setMenuGroupPosition() {

  const interval = 80,
        showMenuLength = menuGroupList.MENU_GROUP.length - menuGroupHideList.length;
  let   colCount = 20; // 1行に入る項目閾値
  
  if ( showMenuLength <= 8 ) {
    colCount = 8;
  } else if ( showMenuLength <= 16 ) {
    colCount = 16;
  }
  
  let c = 0,
      l = 40,
      t = 40,
      mh = 0;

  $('.menu-group').each( function(){
    const $group = $( this ),
          menuCount = $group.find('.entity').length,
          id = $group.attr('id'),
          iw = $group.find('.menu-group-body-inner').outerWidth();
    
    $group.find('.menu-group-header').css('width', iw );
    
    const w = $group.outerWidth(),
          h = $group.outerHeight();
          
    $group.css({
      'left': l,
      'top': t
    });
    $('#' + id + '-back' ).css({
      'width': w,
      'height': h,
      'left': l,
      'top': t
    });
    
    if ( mh < h ) mh = h;
    
    l += interval + w;
    c += menuCount;
    if ( c > colCount ) {
      l = 40;
      t += interval + mh;
      c = 0;
      mh = 0;
    }
    
  });
  return true;
} 

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   項目のサイズと位置の取得
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
function getEntityPosition( $target ) {

    const w = $target.outerWidth(),
          h = $target.outerHeight(),
          x1 = $target.offset().left - g_canvasVisibleArea.l,
          y1 = $target.offset().top - g_canvasVisibleArea.t,
          x2 = x1 + w,
          y2 = y1 + h,
          x3 = $target.closest('.entity').offset().left - g_canvasVisibleArea.l,
          c = y1 + ( h / 2 );
    
    return {'w':w,'h':h,'x1':x1,'y1':y1,'x2':x2,'y2':y2,'x3':x3,'c':c};

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   リレーション接続
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
let relationOutArray, relationInArray;
function connectEntityItem( entityOut, entityIn ) {

    const $entityOut = $('#' + entityOut ),
          $entityIn = $('#' + entityIn ),
          connectID = entityOut + entityIn;
          
    if ( relationOutArray[entityOut] === undefined ) relationOutArray[entityOut] = new Array();
    relationOutArray[entityOut].push(entityIn);
    
    if ( relationInArray[entityIn] === undefined ) relationInArray[entityIn] = new Array();
    relationInArray[entityIn].push(entityOut);
    
    if ( !$entityOut.length || !$entityIn.length ) return false;
    
    $entityOut.addClass('connect connect-out');
    if ( entityOut !== entityIn ) {
      $entityIn.addClass('connect connect-in');
    }
    
    const $svg = newSVG(),
          p1 = getEntityPosition( $entityOut ),
          p2 = getEntityPosition( $entityIn );

    $svg.line.find('path').add( $svg.t ).attr('d', svgDrawPosition( p1.x2, p1.c, p2.x3, p2.c ));
    $svg.mark.attr({'cx': p1.x2, 'cy': p1.c });
    
    $svg.line.add( $svg.mark ).add( $svg.t ).add( $svg.arrow ).attr('data-target', connectID );

    const arrowCenter = ( p2.y1 + p2.h / 2 );
    let arrowD = '';

    // 階層が深い場合は矢印を伸ばす
    if ( Math.abs( p2.x1 - p2.x3 ) > 16 ) {
      arrowD = 'M' + ( p2.x1 + 8 ) + ',' + arrowCenter + ' l-16,-8 0,8 L'
      + p2.x3 + ',' + arrowCenter + ' ' + ( p2.x1 - 8 ) + ',' + arrowCenter + ' l0,8 z';
    } else {
      arrowD = 'M' + ( p2.x1 + 8 ) + ',' + arrowCenter + ' l-16,-8 0,16 z';
    }
    $svg.arrow.attr('d', arrowD );

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   再表示
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const resetArtBoard = function(){
    canvasPositionReset(0);
    $artBoard.find('svg').empty(); // SVGは別途中身を消す必要あり
    $artBoard.empty().prepend( createEntityHTML() );
    setSvgArea();
    $.when(
        setMenuGroupPosition()
    ).done( function(){
        // リレーション
        const relationLength = relationList.length;
        // リレーション情報初期化
        relationOutArray = new Array();
        relationInArray = new Array();
        // リレーション作成
        for ( let i = 0; i < relationLength; i++ ) {
          const relationLength = relationList[i][0].length;
          for ( let j = 0; j < relationLength; j++ ) {
            const start = 'e' + relationList[i][0][j] + '__' + relationList[i][1],
                  end = 'e' + relationList[i][2];
            connectEntityItem( start, end );
          }
        }
        setTimeout( function(){ entityViewAll(0); }, 1 );
    });
};
resetArtBoard();

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   リレーション強調
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 強調解除
const resetRelationStrong = function() {
  const relationMode = $editor.attr('data-relation-mode');
  if ( relationMode !== 'on' ) {
    $editor.find('[data-strong="on"]').removeAttr('data-strong');
  }
};

// 強調固定
const setRelationStrong = function() {
  const relationMode = $editor.attr('data-relation-mode');
  if ( relationMode !== 'on' ) {
    $editor.attr('data-relation-mode', 'on');
    setTimeout( function(){
      $window.on('click.relationMode', function(){
        $window.off('click.relationMode');
        $editor.removeAttr('data-relation-mode');
        $editor.find('[data-strong="on"]').removeAttr('data-strong');
        if ( $editor.find('.entity-item.connect.hover').length ) {
          relationConnect( $editor.find('.entity-item.connect.hover') );
        }
      });
    }, 10 );
  }
};

// リレーション接続
const relationConnect = function( $item ) {
  const relationMode = $editor.attr('data-relation-mode');
  if ( relationMode !== 'on' ) {
    const id = $item.attr('id'),
          flag = $editor.attr('data-relation'),
          type = ( $item.is('.connect-in') )? 'in': 'out',
          itemArray = ( type === 'in')? relationInArray: relationOutArray;

    if ( itemArray[id] !== undefined ) {
      const relationLength = itemArray[id].length;
      $item.attr('data-strong','on');
      for ( let i = 0; i < relationLength; i++ ) {
        const $target = $('#' + itemArray[id][i] );
        if ( flag === 'on' && $target.length ) {
          $target.attr('data-strong', 'on');
          const lineTarget = ( type === 'in')? itemArray[id][i] + id: id + itemArray[id][i];
          $('[data-target="' + lineTarget + '"]').attr('data-strong','on');
        }
      }
    }
  }
};

// 出力
$editor.on({
  'mouseenter': function(){
    const $item = $( this );
    $item.addClass('hover');
    relationConnect( $item );
  },
  'mouseleave': function(){
    $( this ).removeClass('hover');
    resetRelationStrong();
  },
  'click': setRelationStrong
}, '.entity-item.connect');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   表示するメニューグループを選択する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const menuGroupSelectModal = function(){

const $modalBody = $('.editor-modal-body');
  const menuGroupLength = menuGroupList.MENU_GROUP.length;
  if ( menuGroupLength !== 0 ) { 
    let operationListHTML = ''
    + '<div class="modal-table-wrap">'
      + '<table class="modal-table modal-select-table">'
        + '<thead>'
          + '<th class="id"><div class="select-menu-group-all-check"></div></th><th class="name">Menu group name</th>'
        + '</thead>'
        + '<tbody>';

    for ( let i = 0; i < menuGroupLength; i++ ) {
      let checked = ( menuGroupHideList.indexOf( menuGroupList.MENU_GROUP[i].ID ) !== -1 )? '': 'checked';
      operationListHTML += ''
      + '<tr>'
        + '<th><input class="select-menu-group-check" type="checkbox" data-id="' + menuGroupList.MENU_GROUP[i].ID + '" ' + checked + '></th>'
        + '<td><div class="select-menu-group-name">' + menuGroupList.MENU_GROUP[i].NAME + '</div></td>'
      + '</tr>';
    }

    operationListHTML += '</tbody>'
      + '</table>'
    + '</div>';

    $modalBody.html( operationListHTML );

    
    // すべて選択・解除
    const $allCheck = $modalBody.find('.select-menu-group-all-check');
          
    const checkStatus = function(){
      const checkedLength = $modalBody.find('.select-menu-group-check:checked').length;
      if ( checkedLength === menuGroupLength ) {
        $allCheck.attr('data-mode', 'check');
      } else if ( checkedLength > 0 ) { 
        $allCheck.attr('data-mode', 'remove1');
      } else {
        $allCheck.attr('data-mode', 'remove2');
      }
    };
    checkStatus();
    $allCheck.on('click', function(){
      const $check = $modalBody.find('.select-menu-group-check'),
            mode = $allCheck.attr('data-mode');
      if ( mode === 'check' || mode === 'remove1') {
        $check.prop('checked', false );
      } else {
        $check.prop('checked', true );
      }
      checkStatus();
    });
    
    // 選択
    $modalBody.find('.modal-select-table').on('click', 'tr', function(){
      const $check = $( this ).find('.select-menu-group-check');
      if ( $check.prop('checked') === true ) {
        $check.prop('checked', false );      
      } else {
        $check.prop('checked', true );
      }
      checkStatus();
    });

    // 決定・取り消しボタン
    const $modalButton = $('.editor-modal-footer-menu-button');
    $modalButton.prop('disabled', false ).on('click', function() {
      const $button = $( this ),
            btnType = $button.attr('data-button-type');
      switch( btnType ) {
        case 'ok': {
          menuGroupHideList = [];
          $modalBody.find('.select-menu-group-check').each(function(){
            const $check = $( this );
            if ( !$check.prop('checked') ) {
              menuGroupHideList.push( $check.attr('data-id') );
            }
          });
          resetArtBoard();
          editor.modalClose();
          } break;
        case 'cancel':
          editor.modalClose();
          break;
      }
    });
  } else {
    alert('表示可能なメニューグループがありません。');
    editor.modalClose();
  } 
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   リレーションのオン・オフ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const toggleRelation = function() {
    const flag = $editor.attr('data-relation');
    if ( flag === 'on') {
        $editor.attr('data-relation', 'off');
    } else {
        $editor.attr('data-relation', 'on');
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   プリント
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const erPrint = function() {
    // タイトルの変更（ファイル名）
    const $title = $('html').find('title'),
          title = $title.text(),
          showMenuArray = new Array(),
          maxFileNameLength = 128;
    $editor.find('.menu-group').each( function(){
      showMenuArray.push( $( this ).find('.menu-group-name-inner').text() );
    });
    let fileName = showMenuArray.join('_');
    if ( fileName.length > 128 ) {
      fileName = fileName.substr( 0, maxFileNameLength ) + '...';
    }
    const printTitle = 'er(' + fileName + ')';
    $body.addClass('print');
    entityViewAll(0);
    
    $title.text( printTitle );
    window.print();
    
    $title.text( title );
    $body.removeClass('print');
    entityViewAll(0);
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニューボタン
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$editor.find('.editor-menu-button').on('click', function(){
    const $button = $( this ),
          type = $button.attr('data-menu');
    switch( type ) {
       case 'er-print':
          erPrint();
          break;
       case 'er-menu-group':
          editor.modalOpen('Menu group select', menuGroupSelectModal, 'menu-group' );
          break;
       case 'er-relation':
          toggleRelation();
          break;
       case 'view-all':
          entityViewAll();
          break;
       case 'view-reset':
          canvasPositionReset();
          break;
    }
});

}