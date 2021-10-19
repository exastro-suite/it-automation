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

function itaEditorFunctions() {

'use strict';

const editorFunction = this;

const $window = $( window ),
      $body = $('body'),
      $container = $('.wholecontainer');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ページ移動確認
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

let confirmPageMoveFlag = false;
editorFunction.confirmPageMove = function( flag ) {
    confirmPageMoveFlag = flag;
}
$window.on('beforeunload', function(){
    if ( confirmPageMoveFlag === true ) {
      return '';
    }
});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   言語を<html lang="">から取得
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.getLang = function () {
    return $('html').attr('lang');
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   パラメータ取得
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.getParam = function ( name ) {
    const url = window.location.href,
          regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec( url );
    if( !results ) return null;
    return decodeURIComponent( results[2] );
};

editorFunction.getParamAll = function () {
    const parameters = window.location.search.substr(1).split('&'),
          parameterLength = parameters.length,
          parameterObject = {};
    for ( let i = 0; i < parameterLength; i++ ) {
      const keyValue = parameters[i].split('=');
      parameterObject[ keyValue[0] ] = decodeURIComponent( keyValue[1] );
    }
    return parameterObject;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   テキストの無害化
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.textEntities = function( text, spaceFlag, brFlag ) {
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
    if ( spaceFlag !== false ) spaceFlag = true;
    if ( spaceFlag === true ) text = text.replace(/^\s+|\s+$/g, '');
    if ( brFlag !== false ) brFlag = true;
    if ( brFlag === true ) text = text.replace(/\r?\n/g, '<br>');
    return text;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   値の確認
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.checkValue = function( value ) {
    if ( value === undefined || value === null || value === '' ) {
      return false;
    } else {
      return true;
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   アクションモード
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

const actionModeData = 'data-action-mode';

editorFunction.actionMode = {
  'set' : function( actionMode ) { $body.attr( actionModeData, actionMode ); },
  'clear' : function() { $body.removeAttr( actionModeData ); },
  'check' : function( actionMode ) { return ( actionMode === $body.attr( actionModeData ) ) ? true : false; }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   モーダル
// 
////////////////////////////////////////////////////////////////////////////////////////////////////


// 「ロール」ボタン用のモーダルを開く
editorFunction.displayModalOpen = function( headerTitle, bodyFunc, modalType ) {
    
  if ( typeof bodyFunc !== 'function' ) return false;

  // 初期値
  if ( headerTitle === undefined ) headerTitle = 'Undefined title';
  if ( modalType === undefined ) modalType = 'default';

  $body.addClass('modal-open');
  
  let modalHTML = ''
    + '<div id="editor-modal" class="' + modalType + '">'
      + '<div class="editor-modal-container">'
        + '<div class="editor-modal-header">'
          + '<span class="editor-modal-title">' + editorFunction.textEntities( headerTitle ) + '</span>'
          + '<button class="editor-modal-header-close"></button>'
        + '</div>'
        + '<div class="editor-modal-body">'
          + '<div class="editor-modal-loading"></div>'
        + '</div>'
        + '<div class="editor-modal-footer"></div>'
      + '</div>'
    + '</div>';

  const $editorModal = $( modalHTML ),
        $firstFocus = $editorModal.find('.editor-modal-header-close'),
        $lastFocus = $editorModal.find('.editor-modal-header-close');

  $body.append( $editorModal );
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
        editorFunction.modalClose();
        break;
    }
  });

  $firstFocus.on('click', function() {
    editorFunction.modalClose();
  });
  
  bodyFunc( modalType );

}




// モーダルを開く
editorFunction.modalOpen = function( headerTitle, bodyFunc, modalType ) {
    
    if ( typeof bodyFunc !== 'function' ) return false;

    // 初期値
    if ( headerTitle === undefined ) headerTitle = 'Undefined title';
    if ( modalType === undefined ) modalType = 'default';
    
    $body.addClass('modal-open');
    
    let modalHTML = ''
      + '<div id="editor-modal" class="' + modalType + '">'
        + '<div class="editor-modal-container">'
          + '<div class="editor-modal-header">'
            + '<span class="editor-modal-title">' + editorFunction.textEntities( headerTitle ) + '</span>'
            + '<button class="editor-modal-header-close"></button>'
          + '</div>'
          + '<div class="editor-modal-body">'
            + '<div class="editor-modal-loading"></div>'
          + '</div>'
          + '<div class="editor-modal-footer">'
            + '<ul class="editor-modal-footer-menu">'
              + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button positive" data-button-type="ok" disabled>' + getSomeMessage("ITAWDCC92001") + '</li>'
              + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button negative" data-button-type="cancel" disabled>' + getSomeMessage("ITAWDCC92002") + '</li>'
            + '</ul>'
          + '</div>'
        + '</div>'
      + '</div>';

    const $editorModal = $( modalHTML ),
          $firstFocus = $editorModal.find('.editor-modal-header-close'),
          $lastFocus = $editorModal.find('.editor-modal-footer-menu-button[data-button-type="cancel"]');

    $body.append( $editorModal );
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
          editorFunction.modalClose();
          break;
      }
    });

    $firstFocus.on('click', function() {
      editorFunction.modalClose();
    });
    
    bodyFunc( modalType );

}

// モーダルを閉じる
editorFunction.modalClose = function() {

  const $container = $('.wholecontainer'),
        $editorModal = $('#editor-modal');
  
  if ( $editorModal.length ) {
    $window.off('keyup.modal');
    $editorModal.remove();
    $container.css('filter','none');
    $body.removeClass('modal-open');
  }

}

// モーダルエラー表示
editorFunction.modalError = function( message ) {

  const $modalBody = $('.editor-modal-body');
  
  $modalBody.html('<div class="editor-modal-error"><p>' + message + '</p></div>');

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   タブ切替初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.tabMenu = function() {

  $('.editor-tab').each( function() {
  
    const $tab = $( this ),
          $tabItem = $tab.find('.editor-tab-menu-item'),
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

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   エディタウィンドウ縦リサイズ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.rowResize = function() {

  const blockMinHeightPercent = 0.2, // これ以上小さくしない
        $resizeArea = $('.editor-row-resize');

  $resizeArea.each( function(){
  
    const $resizeArea = $( this ),
          $resizeBar = $resizeArea.find('.editor-row-resize-bar'),
          $resizeBlock = $resizeArea.find('.editor-block'),
          $resizeSection1 = $resizeBlock.eq(0),
          $resizeSection2 = $resizeBlock.eq(1);
    
    $resizeBar.on('mousedown', function( e ) {
    
      // 全ての選択を解除する
      getSelection().removeAllRanges();
      
      editorFunction.actionMode.set('row-resize');
      
      const initialPoint = e.clientY;
      let movePoint = 0, newSection1Height = 0;
      
      // 高さを一旦固定値に
      $resizeBlock.each( function(){
        $( this ).css('height', $( this ).outerHeight() );
      });
      
      const initialSection1Height = newSection1Height = $resizeSection1.outerHeight(),
            initialHeight = $resizeArea.outerHeight(),
            minHeight = Math.floor( initialHeight * blockMinHeightPercent ),
            maxHeight = initialHeight - minHeight;
      
      $window.on({
        'mousemove.rowResize' : function( e ){

          movePoint = e.clientY - initialPoint;
      
          newSection1Height = initialSection1Height + movePoint;
      
          if ( newSection1Height < minHeight ) {
            newSection1Height = minHeight;
            movePoint = minHeight - initialSection1Height;
          } else if ( newSection1Height > maxHeight ) {
            newSection1Height = maxHeight;
            movePoint = maxHeight - initialSection1Height;
          }
      
          $resizeSection1.css('height', newSection1Height );
          $resizeSection2.css('height', initialHeight - newSection1Height );
          $resizeBar.css('transform','translateY(' + movePoint + 'px)');
      
        },
        'mouseup.rowResize' : function(){
          $window.off('mousemove.rowResize mouseup.rowResize');
          editorFunction.actionMode.clear();
          
          // 高さを割合に戻す
          const section1Ratio = newSection1Height / initialHeight * 100;
          $resizeSection1.css('height', section1Ratio + '%' );
          $resizeSection2.css('height', ( 100 - section1Ratio ) + '%' );
          $resizeBar.css({
            'transform' : 'translateY(0)',
            'top' : section1Ratio + '%'
          });
        }
      });   
    });
  
  });

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   画面フルスクリーン
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// フルスクリーンチェック
editorFunction.fullScreenCheck = function() {
  if (
    ( document.fullScreenElement !== undefined && document.fullScreenElement === null ) ||
    ( document.msFullscreenElement !== undefined && document.msFullscreenElement === null ) ||
    ( document.mozFullScreen !== undefined && !document.mozFullScreen ) || 
    ( document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen )
  ) {
    return false;
  } else {
    return true;
  }
};
// フルスクリーン切り替え
editorFunction.fullScreen = function( elem ) {

  if ( elem === undefined ) elem = document.body;
  
  if ( !editorFunction.fullScreenCheck() ) {
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
};
// フルスクリーントリガー
document.onfullscreenchange = document.onmozfullscreenchange = document.onwebkitfullscreenchange = document.onmsfullscreenchange = function () {
  if( editorFunction.fullScreenCheck() ){
    $body.addClass('editor-full-screen');
  } else {
    $body.removeClass('editor-full-screen');
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   ログ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// log type : debug, log, notice, warning, error
let logNumber = 1;

editorFunction.log = {
  'set': function( type, content ) {
  if ( type === undefined || type === 'log' ) type = '';  
  const $log = $('.editor-log'),
        $logTable = $('.editor-log-table').find('tbody');
  const logRowHTML = ''
    + '<tr class="editor-log-row ' + type + '">'
      + '<th class="editor-log-number">' + ( logNumber++ ) +'</th><td class="editor-log-content"><span class="logLevel">' + editorFunction.textEntities( type.toLocaleUpperCase() ) + '</span>' + editorFunction.textEntities( content ) + '</td>'
    + '</tr>';

  $logTable.append( logRowHTML );

  // 一番下までスクロール
  const scrollTop = $log.get(0).scrollHeight - $log.get(0).clientHeight;   
  $log.animate({ scrollTop : scrollTop }, 200 );

  },
  'clear': function() {
    logNumber = 1;
    $('.editor-log-table').find('tbody').empty();
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Webストレージ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// Web Strageが使えるかチェック
const storageAvailable = function( type ) {
    try {
      var storage = window[type],
      x = '__storage_test__';
      storage.setItem( x, x );
      storage.removeItem( x );
      return true;
    }
    catch( e ) {
      return e instanceof DOMException && (
      // everything except Firefox
      e.code === 22 ||
      // Firefox
      e.code === 1014 ||
      // test name field too, because code might not be present
      // everything except Firefox
      e.name === 'QuotaExceededError' ||
      // Firefox
      e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
      // acknowledge QuotaExceededError only if there's something already stored
      storage.length !== 0;
    }
}
editorFunction.localStrageFlag = storageAvailable('localStorage');
editorFunction.sessionStrageFlag = storageAvailable('sessionStorage');

// Local storage
editorFunction.setLocalStorage = function( key, value ) {
    if( editorFunction.localStrageFlag ) {
      try {
        localStorage.setItem( key, JSON.stringify( value ) );
      } catch( e ) {
        // Errorで書き込めなかった場合削除する
        window.console.error('localStorage.setItem( ' + key + ' ) : ' + e.message );
        localStorage.removeItem( key );
      }
    } else {
      return false;
    }
}
editorFunction.getLocalStorage = function( key ) {
    if( editorFunction.localStrageFlag && localStorage.getItem( key ) !== null ) {
      return JSON.parse( localStorage.getItem( key ) );
    } else {
      return false;
    }
}
editorFunction.keyCheckLocalStorage = function( key ) {
    if( editorFunction.localStrageFlag && localStorage.getItem( key ) !== null ) {
      return true;
    } else {
      return false;
    }
}
editorFunction.keyRemoveLocalStorage = function( key ) {
    if( editorFunction.localStrageFlag ) {
      localStorage.removeItem( key )
    }
}

// Session storage
editorFunction.setSessionStorage = function( key, value ) {
    if( editorFunction.sessionStrageFlag ) {
      try {
        sessionStorage.setItem( key, JSON.stringify( value ) );
      } catch( e ) {
        // Errorで書き込めなかった場合削除する
        window.console.error('sessionStorage.setItem( ' + key + ' ) : ' + e.message );
        sessionStorage.removeItem( key );
      }
    } else {
      return false;
    }
}
editorFunction.getSessionStorage = function( key ) {
    if( editorFunction.sessionStrageFlag && sessionStorage.getItem( key ) !== null ) {
      return JSON.parse( sessionStorage.getItem( key ) );
    } else {
      return false;
    }
}
editorFunction.keyCheckSessionStorage = function( key ) {
    if( editorFunction.sessionStrageFlag && sessionStorage.getItem( key ) !== null ) {
      return true;
    } else {
      return false;
    }
}
editorFunction.keyRemoveSessionStorage = function( key ) {
    if( editorFunction.sessionStrageFlag ) {
      sessionStorage.removeItem( key )
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   保存（ダウンロード）
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
editorFunction.downloadText = function( text, extension, fileName ) {

  // ファイルネーム
  if ( !editorFunction.checkValue( fileName ) ) fileName = 'noname';
  if ( !editorFunction.checkValue( extension ) ) extension = 'txt';
  
  const downloadName = fileName + '.' + extension,
        blobText = new Blob( [ text ], { type : 'text/plain' }),
        $downloadAnchor = $('<a />');
  
  if ( window.navigator.msSaveBlob ) {
    window.navigator.msSaveBlob( blobText, downloadName );
    return;
  } 
  
  if ( window.URL ) {
    // 一時リンクを作成しダウンロード
    $downloadAnchor.attr({
      'href' : window.URL.createObjectURL( blobText ),
      'download' : downloadName,
      'target' : '_blank'
    });
    $body.prepend( $downloadAnchor );
    $downloadAnchor.get(0).click();

    // 生成したBlobを削除しておく
    setTimeout( function(){
      $downloadAnchor.remove()
      window.URL.revokeObjectURL( blobText );
    }, 100 );
  } else {
    window.console.error('Save function is not supported.');
  }
  
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   読み込み
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.readText = {
  'set': function() {
    const inputFile = '<div id="read-file"><input id="read-file-input" type="file"></div>';
    $body.append( inputFile );
  },
  'open': function() {
    const $readInputFile = $('#read-file-input');
    $readInputFile.val('').click();
  },
  'setInput': function( extension, readFunction ) {
    const $readInputFile = $('#read-file-input');
    $readInputFile.attr('accept', extension ).on('change', function( e ) {
      const readFile = e.target.files[0];
      if ( readFile ) {
        const fileReader = new FileReader();
        fileReader.readAsText( readFile );
        fileReader.onload = function() {
          readFunction( fileReader.result );
        }
      }
    });
  }
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   入力チェック
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

editorFunction.inputTextValidation = function( parent, target ) {
  
  $( parent ).on({
    'focus input': function() {
      const $input = $( this );
      if ( !$input.is('.input-check-target') ) {
        $input.addClass('input-check-target')
          .wrap('<div class="input-check-wrap"/>').after('<span class="input-check-length"/>').focus();
      }
      const value = $input.val(),
            maxLength = $input.attr('data-max-length');
      let inputLength = value.length;
      if ( inputLength > maxLength ) {
        $input.next('.input-check-length').addClass('input-check-over');
      } else {
        $input.next('.input-check-length').removeClass('input-check-over');
      }
      $input.next('.input-check-length').text( inputLength + ' / ' + maxLength );
    },
    'blur': function() {
      const $input = $( this ),
            value = $input.val(),
            maxLength = $input.attr('data-max-length'),
            inputLength = value.length;
      $input.next('.input-check-length').text('').removeClass('input-check-over');
      if ( inputLength > maxLength ) {
        $input.val( value.slice( 0, maxLength ) );
      }
    }
  }, target );
};

editorFunction.inputNumberValidation = function( parent, target ) {
  
  $( parent ).on({
    'focus': function() {
      const $input = $( this );
      if ( !$input.is('.input-check-target') ) {
        $input.addClass('input-check-target')
          .wrap('<div class="input-check-wrap"/>').after('<span class="input-check-length"/>').focus();
      }
      const min = Number( $input.attr('data-min') ),
            max = Number( $input.attr('data-max') );
      $input.next('.input-check-length').text( min + ' - ' + max );
    },
    'blur': function() {
      const $input = $( this ),
            value = $( this ).val(),
            min = Number( $input.attr('data-min') ),
            max = Number( $input.attr('data-max') );
      if ( value === '' || value < min ) {
        $input.val( min );      
      } else if ( value > max ) {
        $input.val( max );
      }
    }
  }, target );
};

}
