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

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   モーダル
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// モーダルを開く
function itaModalOpen( headerTitle, bodyFunc, modalType ) {
    
    if ( typeof bodyFunc !== 'function' ) return false;

    // 初期値
    if ( headerTitle === undefined ) headerTitle = 'Undefined title';
    if ( modalType === undefined ) modalType = 'default';

    const $window = $( window ),
          $body = $('body'),
          $container = $('.wholecontainer');
    
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
          + '<div class="editor-modal-footer">'
            + '<ul class="editor-modal-footer-menu">'
              + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button positive" data-button-type="ok">決定</li>'
              + '<li class="editor-modal-footer-menu-item"><button class="editor-modal-footer-menu-button negative" data-button-type="cancel">取消</li>'
            + '</ul>'
          + '</div>'
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
    
    bodyFunc();

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

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Webストレージ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////