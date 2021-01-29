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
$( window ).on({
  'resize': function(){
    relayout();
  },
  'load': function(){
    relayout();
    
    // Window load後に実行
    setTimeout( function() {
        // サブメニュー初期状態設チェック
        if ( $('#FOOTER').find('.heading-status').length ) {
        
            $('#FOOTER').find('.heading-status-button').removeClass('heading-status-button-wait');
            $('#KIZI').find('.text').removeAttr('data-init-close');

            // サブメニュー初期状態設定の登録がないか確認する
            const func = new itaEditorFunctions,
                  pageNo = func.getParam('no'),
                  keyName = 'no' + pageNo + '_submenu_close_id';
            if ( !func.keyCheckLocalStorage( keyName ) ) {
                // 登録がない場合、初期状態をサブメニュー初期状態設定に適用する

                // 閉じているサブメニューを取得する
                const headingArray = {};
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
                    // 開閉できないものとFilterの次に来るもは除外する
                    if ( !checkOpenNow( nakamiID ) && midashiID !== undefined && !prevNakamiID.match(/^Filter/) ) {                      
                        headingArray[midashiID] = nakamiID;
                    }
                    prevNakamiID = nakamiID;
                });
                func.setSessionStorage( keyName, JSON.stringify( headingArray ) );
            }
        }
      }, 100 );
  }
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
    
    set_initial_filter();
    set_layout_setting();
    userNameAllDisplay();
    
});

function set_layout_setting() {    
    // editor_common.jsが読み込まれているかチェック
    if ( typeof( itaEditorFunctions ) !== 'undefined') {
      const func = new itaEditorFunctions,
            grpNo = func.getParam('grp'),
            pageNo = func.getParam('no'),
            exclusionPageNo = [
              '2100180003','2100180005',
              '2100160011',
              '2100000211','2100000212','2100000401','2100000402'
            ];
      
      if ( pageNo !== null && grpNo === null && exclusionPageNo.indexOf( pageNo ) === -1 ) {
          const $html = $('html'),
                $footerUL = $('#FOOTER').find('ul'),
                layoutKeyName = 'ita_layout',
                headingKeyName = 'no' + pageNo + '_submenu_close_id',
                fixedLayoutClassName = 'ita-fixed-layout';          

          // FOOTERにLayout切り替えボタン、見出し開閉状態変更ボタンを追加する
          let layoutButtonHTML = ''
          + '<li class="fixed-layout"><button class="footer-menu-button fixed-layout-button"></button></li>';
          
          // h2とdiv.textがあるかチェック
          const submenuFlag = ( $('#KIZI').find('h2').length && $('#KIZI').find('div.text').length )? true: false;
          if ( submenuFlag ) {
              layoutButtonHTML += ''
              + '<li class="heading-status"><button class="footer-menu-button heading-status-button heading-status-button-wait"></button></li>';
          }
          
          $footerUL.append( layoutButtonHTML );

          // Local storageに登録があれば読み込む
          if ( func.keyCheckLocalStorage( layoutKeyName ) ) {
              if ( func.getLocalStorage( layoutKeyName ) === 'fixed') {
                  $footerUL.find('.fixed-layout-button').addClass('on');
                  $html.addClass( fixedLayoutClassName );
              }
          }
          
          if ( submenuFlag ) {
              // Local storageに登録があれば読み込む
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
              $footerUL.find('.heading-status-button').on('click', function(){
                  
                  // ロード完了前なら終了
                  if ( $( this ).is('.heading-status-button-wait') ) return false;
                  
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
                      const headingArrayLength = headingArray.length;
                      
                      let headingCloseList;
                      if ( func.keyCheckLocalStorage( headingKeyName ) ) {
                        headingCloseList = JSON.parse( func.getLocalStorage( headingKeyName ) );
                      } else if ( func.keyCheckSessionStorage( headingKeyName ) ) {
                        headingCloseList = JSON.parse( func.getSessionStorage( headingKeyName ) );
                      } else {
                        headingCloseList = null;
                      }
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
                              + '<th><input class="modal-checkbox" type="checkbox" style="opacity:.3;" disabled></th>'
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
                            $modalBody.find('.modal-checkbox').not(':disabled').each( function(){
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
          }

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

// フィルター初期値設定
function set_initial_filter(){
  const $filterArea = $('#filter_area'),
        $filter2Area = $('#select_area');
  // フィルターがあるかチェック
  if ( $filterArea.length || $filter2Area.length ) {
    // 共通ファンクション呼び出し URLからパラメータ取得
    const func = new itaEditorFunctions,
          param = func.getParamAll();
    
    // パラメータからnoを削除
    if ( param['no'] !== undefined ) delete param['no'];
    
    // フィルターに値をセットしてフィルタボタンをクリック
    const filterSet = function( $area ) {
      const filterArea = $area.get(0);
      // フィルターエリアにフィルターが表示されたら
      const observer = new MutationObserver( function(){
        // フィルターが表示されているか確認する
        if ( $area.find('div[class^="fakeContainer_Filter"]').length ) {
          for ( let key in param ) {
            const $target = $area.find('#' + key );
            if ( $target.length && $target.is('input[type="text"]') ) {
              $target.val( param[key] );
            }
          }
          // Filterボタンをクリック
          $area.closest('.text').find('input[name="display_list_btn"]').click();
          // 監視を解除
          observer.disconnect();
        }
      });
      // フィルターエリアの監視を開始
      observer.observe( filterArea, { childList: true });
    };

    // フィルターフラグをチェック
    if ( param['filter'] !== undefined && param['filter'] === 'on' ) {
      delete param['filter'];
      filterSet( $filterArea );
    }
    if ( param['filter2'] !== undefined && param['filter2'] === 'on' ) {
      delete param['filter2'];
      filterSet( $filter2Area );
    }
  }
}

// ユーザ名があふれている場合ホバーで全表示
function userNameAllDisplay() {
  const headerBackColor = $('#HEADER').css('background-color');
  $('.userDataText').each( function(){
    const $userName = $( this ),
          offsetWidth = $userName.get(0).offsetWidth,
          scrollWidth = $userName.get(0).scrollWidth;
    // 文字があふれているかチェックする
    if ( offsetWidth < scrollWidth ) {
      $userName.after('<div class="userNameFull">' + $userName.text() + '</div>');
      const $userNameFull = $('.userNameFull'),
            position = $userName.position().left,
            padding = 4;
      $userNameFull.css({
        'background-color': headerBackColor,
        'left': position - padding
      });
      $userName.on({
        'mouseenter': function(){
          $userNameFull.parent('div').css('z-index','100');
          $userNameFull.show();
        },
        'mouseleave': function(){
          $userNameFull.parent('div').css('z-index','auto');
          $userNameFull.hide();
        }
      });  
    }
  });
}

//////// ----変更履歴遷移用ファンクション ////////
function Mix1_1_journal_async( mode, inner_seq ){
  var ele = document.getElementsByName("COL_IDSOP_7");
  ele[0].value = inner_seq;
  Journal1Tbl_search_async();
  if( document.getElementById("Journal1_Nakami").style.display == "none" ) {
      show('Journal1_Midashi','Journal1_Nakami');
  }
  // Journal1_Midashiのところまでジャンプ
  jumpToSelfHtml('Journal1_Midashi');
}

function journal_async( mode, inner_seq ){
  var ele = document.getElementsByName("COL_IDSOP_7");
  ele[0].value = inner_seq;
  Journal1Tbl_search_async();
  if( document.getElementById("Journal1_Nakami").style.display == "none" ) {
      show('Journal1_Midashi','Journal1_Nakami');
  }
  // Journal1_Midashiのところまでジャンプ
  jumpToSelfHtml('Journal1_Midashi');
}
//////// 変更履歴遷移用ファンクション---- ////////