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
                        nakamiID = $heading.next('.open').find('.text').attr('id');
                    } else if ( $heading.next().is('form') && ( pageNo === '2100000329' || pageNo === '2100000211')) {
                        nakamiID = $heading.next('form').find('#Mix1_Nakami').attr('id');
                    } else if ( $heading.next().is('#import_all') ) {
                        nakamiID = $heading.next('#import_all').find('.text').attr('id');
                    } else if ( $heading.next().is('h3') ) {
                        if ( $heading.next('h3').next().is('.text') ) {
                            nakamiID = $heading.next('h3').next('.text').attr('id');
                        } else if ( $heading.next('h3').next().is('.open') ) {
                            nakamiID = $heading.next('h3').next('.open').attr('id');
                        }
                    } else if ( $heading.next().is('.text') ) {
                        nakamiID = $heading.next('.text').attr('id');
                    }
                    // 開閉できないものとFilterの次に来るもは除外する
                    if ( nakamiID !== undefined ) {
                      if ( !checkOpenNow( nakamiID ) && midashiID !== undefined && !prevNakamiID.match(/^Filter/) ) {                      
                          headingArray[midashiID] = nakamiID;
                      }
                      prevNakamiID = nakamiID;
                    }
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
              '2100000326'
            ],
            href = location.href;
      
      if ( href.match(/\/default\/menu\//) && pageNo !== null && grpNo === null && exclusionPageNo.indexOf( pageNo ) === -1 ) {
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
                      const $heading = $( this );
                      let nakamiID;
                      if ( $heading.next().is('.open') ) {
                          nakamiID = $heading.next('.open').find('.text').attr('id');
                      } else if ( $heading.next().is('form') && ( pageNo === '2100000329' || pageNo === '2100000211')) {
                          nakamiID = $heading.next('form').find('#Mix1_Nakami').attr('id');
                      } else if ( $heading.next().is('#import_all') ) {
                          nakamiID = $heading.next('#import_all').find('.text').attr('id');
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
                          nakamiID = $heading.next('.open').find('.text').attr('id');
                      } else if ( $heading.next().is('form') && ( pageNo === '2100000329' || pageNo === '2100000211')) {
                          nakamiID = $heading.next('form').find('#Mix1_Nakami').attr('id');
                      } else if ( $heading.next().is('#import_all') ) {
                          nakamiID = $heading.next('#import_all').find('.text').attr('id');
                      } else if ( $heading.next().is('h3') ) {
                          if ( $heading.next('h3').next().is('.text') ) {
                              nakamiID = $heading.next('h3').next('.text').attr('id');
                          } else if ( $heading.next('h3').next().is('.open') ) {
                              nakamiID = $heading.next('h3').next('.open').find('.text').attr('id');
                          }
                      } else if ( $heading.next().is('.text') ) {
                          nakamiID = $heading.next('.text').attr('id');
                      }
                      if ( nakamiID !== undefined ) {
                        // 開閉できないものとFilterの次に来るもは除外する
                        if ( midashiID !== undefined && !prevNakamiID.match(/^Filter/) ) {                      
                            headingArray.push([ midashiID, midashiTEXT, nakamiID, '']);
                        } else {
                            headingArray.push([ undefined, midashiTEXT, undefined, 'disabled']);
                        }
                        prevNakamiID = nakamiID;
                      }
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
        
          // 名称での検索用にTableの見出しテキストを配列に格納
          const tableArray = new Array();                
          $area.find('.defaultExplainRow').each( function( rowN ){
            const $tr = $( this );
            if( tableArray[rowN] === undefined ) tableArray[rowN] = new Array();
            $tr.find('th').each( function( colN ){
              const $td = $( this ),
                    text = $td.text(),
                    colspan = ( $td.attr('colspan') === undefined )? 1: $td.attr('colspan'),
                    rowspan = ( $td.attr('rowspan') === undefined )? 1: $td.attr('rowspan');
              
              // 開始列を調べる
              let colStart = 0;
              const numCheck = function( num ){
                if ( tableArray[rowN][num] === undefined ) {
                  colStart = num;
                } else {
                  numCheck( num + 1 );
                }
              };
              numCheck(colN);
              
              // Rowspan - Colspanループ
              for ( let i = 0; i < rowspan; i++ ) {
                const cRow = rowN + i;
                if( tableArray[cRow] === undefined ) tableArray[cRow] = new Array();
                for ( let j = 0; j < colspan; j++ ) {
                  const cCol = colStart + j;
                  // textとrowspanフラグ
                  tableArray[cRow][cCol] = {
                    't': text,
                    'f': i
                  }
                }
              }
            });
          });
          
          // 項目親子関係
          const levelList = new Array(),
                levelLength = tableArray.length,
                itemLength = tableArray[0].length;
          for ( let i = 0; i < itemLength; i++ ) {
            const levelArray = new Array();
            for ( let j = 0; j < levelLength; j++ ) {
              if ( tableArray[j][i].f === 0 ) {
                levelArray.push( tableArray[j][i].t );
              }
            }
            levelList.push( levelArray.join('\\') );
          }
          
          for ( let key in param ) {
            try {
              const idKey = decodeURIComponent( key ),
                    value = param[key];
              let   targetNum = levelList.indexOf(idKey);
              
              // 見つからなかった場合項目名のみでも検索する
              if ( targetNum === -1 ) {
                const itemLength = tableArray[ levelLength - 1 ].length;
                for ( let i = 0; i < itemLength; i++ ) {
                  if ( tableArray[ levelLength - 1 ][i].t === idKey ) {
                    targetNum = i;
                    break;
                  }
                }
              }
              
              if ( targetNum !== -1 ) {
                // 項目名が一致した場合
                const $target = $area.find('tr').last().find('td').eq( targetNum ).find('input, select');
                if ( $target.length >= 2 && value.indexOf('~') !== -1 ) {
                  // 対象が２つの場合かつ、「~」が含まれている場合
                  const inputValues = param[key].split('~'),
                        inputLength = inputValues.length;
                  for ( let i = 0; i < inputLength; i++ ) {
                    $target.eq(i).val( inputValues[i] );
                  }
                } else {
                  $target.val( value );
                }
              } else {
                // 項目名が一致しない場合IDで調べる
                if ( !idKey.match(/\/|%|<|>/) ) {
                  const $target = $area.find('#' + idKey );
                  if ( $target.length ) {
                    $target.val( param[key] );
                  } 
                }
              }
            } catch(e) {
              window.console.error(e);
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
      if ( $filterArea.length ) filterSet( $filterArea );
      if ( $filter2Area.length ) filterSet( $filter2Area );
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
  var ele = document.getElementsByName("COL_IDSOP_8");
  ele[0].value = inner_seq;
  Journal1Tbl_search_async();
  if( document.getElementById("Journal1_Nakami").style.display == "none" ) {
      show('Journal1_Midashi','Journal1_Nakami');
  }
  // Journal1_Midashiのところまでジャンプ
  jumpToSelfHtml('Journal1_Midashi');
}

function journal_async( mode, inner_seq ){
  var ele = document.getElementsByName("COL_IDSOP_8");
  ele[0].value = inner_seq;
  Journal1Tbl_search_async();
  if( document.getElementById("Journal1_Nakami").style.display == "none" ) {
      show('Journal1_Midashi','Journal1_Nakami');
  }
  // Journal1_Midashiのところまでジャンプ
  jumpToSelfHtml('Journal1_Midashi');
}
//////// 変更履歴遷移用ファンクション---- ////////

//////// ----複製用ファンクション ////////
function Mix1_1_duplicate_async( mode, inner_seq ){
  
  var registerAreaWrap = 'Mix2_Nakami';

  // アラート用エリアを初期化
  var objAlertArea = $('#'+registerAreaWrap+' .alert_area').get()[0];
  objAlertArea.innerHTML = '';
  objAlertArea.style.display = "none";

  // registerTableファンクション呼び出し要否フラグ
  var exec_flag = true;

  if( document.getElementById(registerAreaWrap).style.display == "block" ) {
      //----登録中ですが中断してよろしいですか？
      if( window.confirm( getSomeMessage("ITAWDCC20202")) == false ){
        exec_flag = false;
      }
  }

  // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
  restruct_for_IE();

  if( exec_flag ){
      // proxy.registerTable実行
      var registerData = $('#'+registerAreaWrap+' :input').serializeArray();
      proxy.Mix1_1_duplicate( mode, inner_seq, registerData);

      if( document.getElementById(registerAreaWrap).style.display == "none" ) {
          show('Mix2_Midashi',registerAreaWrap);
      }
      // Mix2_Midashiのところまでジャンプ
      jumpToSelfHtml('Mix2_Midashi');
  }
}

function duplicate_async( mode, inner_seq ){

  var registerAreaWrap = 'Mix2_Nakami';

  // アラート用エリアを初期化
  var objAlertArea = $('#'+registerAreaWrap+' .alert_area').get()[0];
  objAlertArea.innerHTML = '';
  objAlertArea.style.display = "none";

  // registerTableファンクション呼び出し要否フラグ
  var exec_flag = true;

  if( document.getElementById(registerAreaWrap).style.display == "block" ) {
      //----登録中ですが中断してよろしいですか？
      if( window.confirm( getSomeMessage("ITAWDCC20202")) == false ){
        exec_flag = false;
      }
  }

  // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
  restruct_for_IE();

  if( exec_flag ){
      // proxy.registerTable実行
      var registerData = $('#'+registerAreaWrap+' :input').serializeArray();
      proxy.Mix1_1_duplicate( mode, inner_seq, registerData);

      if( document.getElementById(registerAreaWrap).style.display == "none" ) {
          show('Mix2_Midashi',registerAreaWrap);
      }
      // Mix2_Midashiのところまでジャンプ
      jumpToSelfHtml('Mix2_Midashi');
  }
}