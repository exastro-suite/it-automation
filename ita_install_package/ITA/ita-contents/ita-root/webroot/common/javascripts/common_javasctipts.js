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


// レイアウト初期化
$( window ).on('load resize', function(){
    relayout();
});

$( function() {
        
    // パスワード入力マスク解除
    $('#KIZI, #gateLoginContainer, #gateChangePw').on({
      'mousedown' : function(){
        var $eye = $( this ),
            $input = $eye.prev('input');
            
        $eye.addClass('password_see');
        $input.blur().attr('type', 'text');
        
        $( window ).on({
          'mouseup.passwordEye' : function(){
            $( this ).off('mouseup.passwordEye');
            $input.attr('type', 'password').focus();
            $eye.removeClass('password_see');
          }
        });
      }
    }, '.password_eye');
    
    // メインメニュー メニュー名表示
    $('#sortable .mm_list').on({
      'mouseenter': function() {
        var $item = $( this ),
            itemWidth = $item.width(),
            itemHeight = $item.height(),
            positionX = $item.offset().left - window.pageXOffset,
            positionY = $item.offset().top - window.pageYOffset;
        $item.addClass('itemHover').find('.mm_text').css({
          'min-width': itemWidth
        });
        
        // 位置を調整する
        var documentPadding = 4,
            itemTextWidth = $item.find('.mm_text').outerWidth(),
            itemTextHeight = $item.find('.mm_text').outerHeight(),
            diffWidt = ( itemTextWidth - itemWidth ) / 2,
            positionLeft = positionX - diffWidt,
            positionTop = itemHeight + positionY + documentPadding,
            documentWidth = document.body.clientWidth,
            documentHeight = document.documentElement.clientHeight;
        // Left check
        if ( positionLeft <= documentPadding ) positionLeft = documentPadding;
        // Right check
        if ( positionLeft + itemTextWidth > documentWidth ) {
          positionLeft = documentWidth - itemTextWidth - documentPadding;
        }
        // Bottom check
        if ( positionTop + itemTextHeight > documentHeight ) {
          positionTop = positionY - itemTextHeight - documentPadding;
        }        
        $item.find('.mm_text').css({
          'top': positionTop,
          'left': positionLeft,
          'bottom': 'auto',
          'min-width': itemWidth
        });
      },
      'mouseleave': function() {
        $( this ).removeClass('itemHover').find('.mm_text').css({
          'top': 'auto',
          'left': 0,
          'bottom': 0
        });
      }
    });
    
    set_layout_setting();
});

function set_layout_setting() {    
    // editor_common.jsが読み込まれているかチェック
    if ( typeof( itaEditorFunctions ) !== 'undefined') {
      const func = new itaEditorFunctions,
            pageNo = func.getParam('no'),
            exclusionPageNo = [
              '2100180003','2100180005',
              '2100160011',
              '2100000211','2100000212','2100000401','2100000402'
            ];
      
      if ( pageNo !== null && exclusionPageNo.indexOf( pageNo ) === -1 ) {
          const $html = $('html'),
                $footerUL = $('#FOOTER').find('ul'),
                layoutKeyName = 'ita_layout',
                headingKeyName = 'no' + pageNo + '_submenu_close_id',
                fixedLayoutClassName = 'ita-fixed-layout';          

          // FOOTERにLayout切り替えボタン、見出し開閉状態変更ボタンを追加する
          const layoutButtonHTML = ''
          + '<li class="fixed-layout"><button class="footer-menu-button fixed-layout-button"></button></li>'
          + '<li class="heading-status"><button class="footer-menu-button heading-status-button"></button></li>';
          
          $footerUL.append( layoutButtonHTML );

          // Local storageに登録があれば読み込む
          if ( func.keyCheckLocalStorage( layoutKeyName ) ) {
              if ( func.getLocalStorage( layoutKeyName ) === 'fixed') {
                  $footerUL.find('.fixed-layout-button').addClass('on');
                  $html.addClass( fixedLayoutClassName );
              }
          }
          if ( func.keyCheckLocalStorage( headingKeyName ) ) {
              const headingCloseList = JSON.parse( func.getLocalStorage( headingKeyName ) );
              // 指定のサブメニューを閉じる
              for ( let midashiID in headingCloseList ) {
                show( midashiID, headingCloseList[midashiID] );
              }
              // 00_javascriptのwindow.load後のshow()を無視するため属性を付ける
              let prevNakamiID = '';
              $('#KIZI').find('h2').each( function(){
                  const $heading = $( this ),
                        midashiID = $heading.find('.showbutton').closest('div').attr('id');
                  let nakamiID;
                  if ( $heading.next().is('.open') ) {
                      nakamiID = $heading.next('.open').find('.text').attr('id')
                  } else {
                      nakamiID = $heading.next('.text').attr('id');
                  }
                  if ( prevNakamiID !== undefined && !prevNakamiID.match(/^Filter/) ) {
                      $('#' + nakamiID ).attr('data-init-close','on');
                  }
                  prevNakamiID = nakamiID;
              });
          }
          
          $('#Filter1Tbl_2').val('1');

          $footerUL.find('.heading-status-button').on('click', function(){
              const headingArray = new Array();
          
              // 見出しとIDの取得
              let prevNakamiID = '';
              $('#KIZI').find('h2').each( function(){
                  const $heading = $( this ),
                        midashiID = $heading.find('.showbutton').closest('div').attr('id'),
                        midashiTEXT = $heading.find('.midashi_class').text();
                  let nakamiID;
                  if ( $heading.next().is('.open') ) {
                      nakamiID = $heading.next('.open').find('.text').attr('id')
                  } else {
                      nakamiID = $heading.next('.text').attr('id');
                  }
                  // 開閉できないものとFilterの次に来るもは除外する
                  if ( midashiID !== undefined && !prevNakamiID.match(/^Filter/) ) {                      
                      headingArray.push([ midashiID, midashiTEXT, nakamiID, '']);
                  } else {
                      headingArray.push([ undefined, midashiTEXT, undefined, 'disabled']);
                  }
                  prevNakamiID = nakamiID;
              });
              
              // 開閉状態変更リスト作成
              const subMenuSelect = function() {
                  const $modalBody = $('.editor-modal-body');
                  // 見出しテーブル作成
                  let headingListHtml = ''
                  + '<div class="modal-table-wrap">'
                    + '<table class="modal-table modal-select-table">'
                      + '<thead>'
                        + '<th class="select">' + getSomeMessage("ITAWDCC92005") + '</th><th class="name">' + getSomeMessage("ITAWDCC92006") + '</th>'
                      + '</thead>'
                      + '<tbody>';
                  const headingArrayLength = headingArray.length,
                        headingCloseList = ( func.keyCheckLocalStorage( headingKeyName ) )? JSON.parse( func.getLocalStorage( headingKeyName ) ): null;
                  for ( let i = 0; i < headingArrayLength; i++ ) {
                      // チェック状態確認
                      let checkedStatus = '';
                      if ( headingCloseList !== null && headingArray[i][0] !== undefined ) {
                          if ( headingCloseList[headingArray[i][0]] ) {
                              checkedStatus = '';
                          } else {
                              checkedStatus = ' checked';
                          }
                      }
                      if ( headingArray[i][0] !== undefined ) {
                          headingListHtml += '<tr>'
                          + '<th><input value="' + headingArray[i][0] + '" data-nakami="' + headingArray[i][2] + '" class="modal-checkbox" type="checkbox"' + checkedStatus + '></th>'
                          + '<td>' + headingArray[i][1] + '</td></tr>';
                      } else {
                          headingListHtml += '<tr class="disabled">'
                          + '<th></th>'
                          + '<td>' + headingArray[i][1] + '</td></tr>';
                      }
                  }
                  headingListHtml += '</tbody></table>';
                  
                  $modalBody.html( headingListHtml );
                  
                  // 行で選択
                  $modalBody.find('.modal-select-table').on('click', 'tr', function(){
                    const $tr = $( this ),
                          checked = $tr.find('.modal-checkbox').prop('checked');
                    if ( !$tr.is('.disabled') && checked ) {
                      $tr.find('.modal-checkbox').prop('checked', false );
                    } else {
                      $tr.find('.modal-checkbox').prop('checked', true );
                    }
                  });
                  
                  // 決定・取り消しボタン
                  const $modalButton = $('.editor-modal-footer-menu-button');
                  $modalButton.prop('disabled', false ).on('click', function() {
                    const $button = $( this ),
                          btnType = $button.attr('data-button-type');
                    switch( btnType ) {
                      case 'ok': {
                        const registArray = {};
                        $modalBody.find('.modal-checkbox').each( function(){
                          const $checkbox = $( this ),
                                midashiID = $checkbox.val(),
                                nakamiID = $checkbox.attr('data-nakami'),
                                checkFlag = $checkbox.prop('checked');
                          if ( !checkFlag ) {
                            registArray[midashiID] = nakamiID;
                          }
                          if ( checkOpenNow( nakamiID ) !== checkFlag  ) {
                            show( midashiID, nakamiID );
                          } 
                        });
                        func.setLocalStorage( headingKeyName, JSON.stringify( registArray ) );
                        func.modalClose();
                        } break;
                      case 'cancel':
                        func.modalClose();
                        break;
                    }
                  });
              }              
              func.modalOpen( getSomeMessage("ITAWDCC92004"), subMenuSelect,'init-sub-menu')
          });          

          // Layoutタイプを切り替えてLocal storageに登録する
          $footerUL.find('.fixed-layout-button').on('click', function(){
            const $button = $( this );
            if ( $button.is('.on') ) {
              $button.removeClass('on');
              $html.removeClass( fixedLayoutClassName );
              func.setLocalStorage( layoutKeyName, 'default');
            } else {
              $button.addClass('on');
              $html.addClass( fixedLayoutClassName );
              func.setLocalStorage( layoutKeyName, 'fixed');
            }
          });
        }
    }
}






