//   Copyright 2020 NEC Corporation
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
//   メッセージ管理
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const getWidgetMessage = function( id ) {

    const message = {
      '1':getSomeMessage("ITAWDCC92101"), // 削除しますか？
      '2':getSomeMessage("ITAWDCC92102"), // メインメニューは削除できません。
      '3':getSomeMessage("ITAWDCC92103"), // 名称
      '4':getSomeMessage("ITAWDCC92104"), // 横サイズ
      '5':getSomeMessage("ITAWDCC92105"), // 縦サイズ
      '6':getSomeMessage("ITAWDCC92106"), // 一行項目数
      '7':getSomeMessage("ITAWDCC92107"), // 項目
      '8':getSomeMessage("ITAWDCC92108"), // 項目を追加する
      '9':getSomeMessage("ITAWDCC92109"), // サイズを変更するための空きがありません。\nBlankスペースが必要です。
      '10':getSomeMessage("ITAWDCC92110"), // 登録しました。
      '11':getSomeMessage("ITAWDCC92111"), // 登録に失敗しました。
      '12':getSomeMessage("ITAWDCC92112"), // Widgetを追加する
      '13':getSomeMessage("ITAWDCC92113"), // 編集中ですが取り消してもよろしいですか？
      '14':getSomeMessage("ITAWDCC92114"), // 初期状態に戻しますか？
      '15':getSomeMessage("ITAWDCC92115"), // 登録しますか？
      '16':getSomeMessage("ITAWDCC92116"), // 合計
      '17':getSomeMessage("ITAWDCC92117"), // 正常終了
      '18':getSomeMessage("ITAWDCC92118"), // 異常終了
      '19':getSomeMessage("ITAWDCC92119"), // エラー終了
      '20':getSomeMessage("ITAWDCC92120"), // 緊急停止
      '21':getSomeMessage("ITAWDCC92121"), // 予約取消
      '22':getSomeMessage("ITAWDCC92122"), // 実行中
      '23':getSomeMessage("ITAWDCC92123"), // 未実行(予約)
      '24':getSomeMessage("ITAWDCC92124"), // 編集
      '25':getSomeMessage("ITAWDCC92125"), // Widget追加
      '26':getSomeMessage("ITAWDCC92126"), // 登録
      '27':getSomeMessage("ITAWDCC92127"), // リセット
      '28':getSomeMessage("ITAWDCC92128"), // 取消
      '29':getSomeMessage("ITAWDCC92129"), // 期間
      '30':getSomeMessage("ITAWDCC92130"), // リセットしました。
      '31':getSomeMessage("ITAWDCC92131"), // リセットに失敗しました。
      '32':getSomeMessage("ITAWDCC92132"), // タイトルバー
      '33':getSomeMessage("ITAWDCC92133"), // 枠・背景
      '34':getSomeMessage("ITAWDCC92134"), // 表示する
      '35':getSomeMessage("ITAWDCC92135"), // 表示しない
      '36':getSomeMessage("ITAWDCC92136"), // 画像URL
      '37':getSomeMessage("ITAWDCC92137"), // リンクURL
      '38':getSomeMessage("ITAWDCC92154"), // 未実行
      '39':getSomeMessage("ITAWDCC92155"), // 表示できるメニューグループはありません。
      '40':getSomeMessage("ITAWDCC92158"), // インスタンスID
      '41':getSomeMessage("ITAWDCC92159"), // 名称
      '42':getSomeMessage("ITAWDCC92160"), // オペレーション名
      '43':getSomeMessage("ITAWDCC92161"), // ステータス
      '44':getSomeMessage("ITAWDCC92162"), // 予約日時
      '45':getSomeMessage("ITAWDCC92163"), // 予約作業はありません
      '46':getSomeMessage("ITAWDCC92164"), // {{N}}日以内の予約作業はありません。
      '47':getSomeMessage("ITAWDCC92165"), // ※0で全件表示
      '48':getSomeMessage("ITAWDCC92166"), // 期間（日）
      '49':getSomeMessage("ITAWDCC92167"), // 実行まで残り
      '50':getSomeMessage("ITAWDCC92168"), // 日
      '51':getSomeMessage("ITAWDCC92169"), // 時間
      '52':getSomeMessage("ITAWDCC92170"), // 分
      '53':getSomeMessage("ITAWDCC92171"), // リンクリスト保存
      '54':getSomeMessage("ITAWDCC92172"), // リンクリスト読込
      '55':getSomeMessage("ITAWDCC92173"), // ファイルの読み込みに失敗しました。
    };

    if ( message[ id ] ) {
      return message[ id ];
    } else {
      return undefined;
    }

};


//
//
//   Dash board - Widget set
//
//

function set_widget( widgetInfo ) {

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widget情報
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const getWidgetItem = function( widgetID ) {

    const widget = {
      '1': {
        'widget_id': '1',
        'name': 'main_menu',
        'display_name': getSomeMessage("ITAWDCC92138"),
        'description': '',
        'colspan': '3',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '1',
        'data': {
          'menu_col_number': '6'
        }
      },
      '2': {
        'widget_id': '2',
        'name': 'menu_set',
        'display_name': getSomeMessage("ITAWDCC92139"),
        'description': getSomeMessage("ITAWDCC92140"),
        'colspan': '3',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '0',
        'data': {
          'menu_col_number': '6'
        }
      },
      '3': {
        'widget_id': '3',
        'name': 'shortcut',
        'display_name': getSomeMessage("ITAWDCC92141"),
        'description': getSomeMessage("ITAWDCC92142"),
        'colspan': '3',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '0',
        'data': {
          'list': [],
          'link_col_number': '2'
        }
      },
      '4': {
        'widget_id': '4',
        'name': 'movement_number',
        'display_name': 'Movement',
        'description': getSomeMessage("ITAWDCC92143"),
        'colspan': '1',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '1'
      },
      '5': {
        'widget_id': '5',
        'name': 'work_status',
        'display_name': getSomeMessage("ITAWDCC92144"),
        'description': getSomeMessage("ITAWDCC92145"),
        'colspan': '1',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '1'
      },
      '6': {
        'widget_id': '6',
        'name': 'work_result',
        'display_name': getSomeMessage("ITAWDCC92146"),
        'description': getSomeMessage("ITAWDCC92147"),
        'colspan': '1',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '1',
      },
      '7': {
        'widget_id': '7',
        'name': 'work_history',
        'display_name': getSomeMessage("ITAWDCC92148"),
        'description': getSomeMessage("ITAWDCC92149"),
        'colspan': '3',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '1',
        'data': {
          'period': '28'
        }
      },/*
      '8': {
        'widget_id': '8',
        'name': 'html_embed',
        'display_name': getSomeMessage("ITAWDCC92150"),
        'description': getSomeMessage("ITAWDCC92151"),
        'colspan': '1',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '0',
        'data': {
          'html': '<p style="padding:16px">New widget.</p>'
        }
      },*/
      '9': {
        'widget_id': '9',
        'name': 'image',
        'display_name': getSomeMessage("ITAWDCC92152"),
        'description': getSomeMessage("ITAWDCC92153"),
        'colspan': '1',
        'rowspan': '1',
        'display': '1',
        'title': '0',
        'background': '0',
        'unique': '0',
        'data': {
          'image': '/common/imgs/widget_default_image.png',
          'link': '',
          'target': '_blank'
        }
      },
      //#488 start
      '10': {
        'widget_id': '10',
        'name': 'reserve_symphony_conductor',
        'display_name': getSomeMessage("ITAWDCC92156"),
        'description': getSomeMessage("ITAWDCC92157"),
        'colspan': '3',
        'rowspan': '1',
        'display': '1',
        'title': '1',
        'background': '1',
        'unique': '1',
        'data': {
          'days': '14',
          'symphony': '1',
          'conductor': '1'
        }
      }
      //#488 end
    };

    if( widgetID === undefined ) {
      return widget;
    } else {
      if ( widget[ widgetID ] ) {
        return widget[ widgetID ];
      } else {
        return undefined;
      }
    }

};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期設定
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 初期配置 Widgetを返す
const getInitialWidget = function() {
  
    // 初期配置 Widget [ Widget ID, エリア, 行, 列 ]
    const setInitialWidget = [
            [1,0,0,0],
            [4,1,0,0],
            [5,1,0,1],
            [6,1,0,2],
            [7,1,1,0]
          ];
    
    const setInitialWidgetLength = setInitialWidget.length,
          initialWidget = [];

    for ( let i = 0; i < setInitialWidgetLength; i++ ) {
      initialWidget[i] = getWidgetItem( setInitialWidget[i][0] );
      initialWidget[i]['area'] = String( setInitialWidget[i][1] );
      initialWidget[i]['row'] = String( setInitialWidget[i][2] );
      initialWidget[i]['col'] = String( setInitialWidget[i][3] );
    }

    return initialWidget;

};

// gridが使えるかチェックする
const gridCheck = function() {
  const $checkDiv = $('<div/>');
  $checkDiv.css('display','grid');
  if ( $checkDiv.css('display') !== 'grid') {
    return false;
  } else {
    return true;
  }
};

// エディター共通機能呼び出し
const editor = new itaEditorFunctions;

// jQuery objectキャッシュ
const $window = $( window ),
      $kizi = $('#KIZI'),
      $dashboard = $('#dashboard'),
      $dashboardGridStyle = $('#dashboard-grid-style'),
      $dashboardBody = $dashboard.find('.dashboard-body');

// Initial value
const maxAreaNumber = 2,
      maxColumnNumber = 2, // 最大列数（0から）
      menuGroupURL = '/default/mainmenu/01_browse.php?grp=';

// ショートカットリンクID
let shortcutCount = 0;

// 編集データを入れておく
const widgetTemp = {};

// Widgetの位置情報
const dashboardAreaArray = new Array;

// モード変更（0:view or 1:edit）
let dashboardMode = '';
const dashboardModeChange = function( mode ) {
  if ( mode === 0 ) {
    $dashboard.attr('data-mode', 'view');
    dashboardMode = 'view';
  } else {
    $dashboard.attr('data-mode', 'edit');
    dashboardMode = 'edit';
  }
};
dashboardModeChange(0);

// アクション変更
let dashboardAction = '';
const dashboardActionChange = function( action ) {
  switch ( action ) {
    case 0:
      $dashboard.attr('data-action', 'none');
      dashboardAction = 'none';
      break;
    case 1:
      $dashboard.attr('data-action', 'menu-move');
      dashboardAction = 'menu-move';
      break;
    case 2:
      $dashboard.attr('data-action', 'widget-move');
      dashboardAction = 'widget-move';
      break;
    case 3:
      $dashboard.attr('data-action', 'restriction');
      dashboardAction = 'restriction';
      break;
    case 4:
      $dashboard.attr('data-action', 'link-move');
      dashboardAction = 'link-move';
      break;
  }
};
dashboardActionChange(0);

// 変更があった際の処理
let changeFlag = false;
const setChangeFlag = function( flag ) {
    changeFlag = flag;
}

// 各種ボタンにテキストをセット
$dashboard.find('.dashboard-menu-button').each(function(){
    const $button = $( this ),
          buttonType = $button.attr('data-button');
    switch( buttonType ) {
        case 'edit': $button.text( getWidgetMessage('24') ); break;
        case 'add': $button.text( getWidgetMessage('25') ); break;
        case 'regist': $button.text( getWidgetMessage('26') ); break;
        case 'reset': $button.text( getWidgetMessage('27') ); break;
        case 'cancel': $button.text( getWidgetMessage('28') ); break;
    }
});


// 表示しているメニューグループIDを取得する
const openMenuGroupID = editor.getParam('grp');

// コンソールログ
const log = function() {
    const argumentslength = arguments.length;
    for ( let i = 0; i < argumentslength; i++ ) {
      // window.console.log( arguments[i] );
    }
}

// 選択を解除する
const deselection = function() {
    getSelection().removeAllRanges();
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widget HTML
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// ショートカットリストHTML
const getWidgetShortcutHTML = function( shortcutList ) {
    const shortcutLength = shortcutList.length;
    let shortcutHTML = '<ul class="shortcut-list">';
    for ( let i = 0; i < shortcutLength; i++ ) {
      const href = encodeURI( shortcutList[i]['url'] ),
            target = editor.textEntities( shortcutList[i]['target'], false ),
            text = editor.textEntities( shortcutList[i]['name'], false ).replace(/^ | $/g, '&nbsp');
      shortcutHTML += '<li class="shortcut-item" data-link-id="' + shortcutCount + '">'
        + '<a class="shortcut-link" href="' + href + '" target="' + target + '">' + text + '</a>'
        + '</li>';
      shortcutCount++;
    }
    shortcutHTML += '</ul>';
    
    return shortcutHTML;
};

// Widget基本HTML
const getWidgetHTML = function( widgetSetID, widgetData ) {

    const loadingWaitHTML = '<div class="widget-loading"></div>';

    // Widget個別HTML
    let contentHTML = '',
        dataHTML = '';
    switch ( widgetData['widget_id'] ) {
      // メインメニュー、メニューセット
      case '1':
        if ( Object.keys( widgetInfo['menu'] ).length !== 0 ) {
          contentHTML = '<ul class="widget-menu-list">' + widgetMenu( widgetSetID ) + '</ul>';
        } else {
          contentHTML = ''
          + '<div id="dashboard-error-message">'
            + '<p class="dashboard-error-message-text">' + getWidgetMessage('39') + '</p>'
          + '</div>';
        }
        dataHTML = ' data-menu-col="' + widgetData['data']['menu_col_number'] + '"';
        break;
      case '2':
        contentHTML = '<ul class="widget-menu-list">' + widgetMenu( widgetSetID ) + '</ul>';
        dataHTML = ' data-menu-col="' + widgetData['data']['menu_col_number'] + '"';
        break;
      // ショートカット
      case '3':
        contentHTML = getWidgetShortcutHTML( widgetData['data']['list'] );
        dataHTML = ' data-link-col="' + widgetData['data']['link_col_number'] + '"';
        break;
      // Movemnt数
      case '4':
      case '5':
      case '6':
        contentHTML = loadingWaitHTML;
        break;      
      case '7':
        contentHTML = loadingWaitHTML;
        dataHTML = ' data-period="' + widgetData['data']['period'] + '"';
        break;
      // HTML埋め込み
      case '8':
        contentHTML = widgetData['data']['html'];
        widgetTemp[widgetSetID] = {'html': contentHTML };
        break;
      // 画像埋め込み
      case '9':
        contentHTML = '<img src="' + encodeURI( widgetData['data']['image'] ) + '" class="widget-image">';
        if ( widgetData['data']['link'] !== '') {
          contentHTML = '<a class="widget-image-link" href="' + encodeURI( widgetData['data']['link'] ) + '" target="' + editor.textEntities( widgetData['data']['target'] ) + '">' + contentHTML + '</a>'
        }
        break;
      case '10':
        contentHTML = loadingWaitHTML;
        dataHTML = ' data-days="' + widgetData['data']['days'] + '"';
        dataHTML += ' data-display-symphony="' + widgetData['data']['symphony'] + '"';
        dataHTML += ' data-display-conductor="' + widgetData['data']['conductor'] + '"';
        break;
      default:
        contentHTML = '{contents}';
    }
    // Widget枠
    const widgetHTML = ''
    + '<div id="' + widgetSetID + '" '
        + 'style="grid-area:' + widgetSetID + '" '
        + 'class="widget-grid" '
        + 'data-widget-id="' + widgetData['widget_id'] + '" '
        + 'data-widget-display="' + widgetData['display'] + '" '
        + 'data-widget-title="' + widgetData['title'] + '" '
        + 'data-widget-background="' + widgetData['background'] + '" '
        + 'data-rowspan="' + widgetData['rowspan'] + '" '
        + 'data-colspan="' + widgetData['colspan'] + '"' + dataHTML + '>'
      + '<div class="widget">'
        + '<div class="widget-header">'
          + '<div class="widget-move-knob"></div>'
          + '<div class="widget-name"><span class="widget-name-inner">' + editor.textEntities( widgetData['display_name'] ) + '</span>'
            + '<div class="widget-edit-menu">'
              + '<ul class="widget-edit-menu-list">'
                + '<li class="widget-edit-menu-item"><button class="widget-edit-button widget-edit" data-type="edit"></button></li>'
                + '<li class="widget-edit-menu-item"><button class="widget-edit-button widget-display" data-type="display"></button></li>'
                + '<li class="widget-edit-menu-item"><button class="widget-edit-button widget-delete" data-type="delete"></button></li>'
              + '</ul>'
            + '</div>'
          + '</div>'
        + '</div>'
        + '<div class="widget-body">' + contentHTML + '</div>'
      + '</div>'
    + '</div>';
    
    return widgetHTML;
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Set IDを返す
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
let widgetCount = 0,
    widgetBlankCount = 0;

const getSetID = function() {
  const setIdList = [];
  $dashboardBody.find('.widget-grid').each( function(){
    setIdList.push( $( this ).attr('id') );
  });
  
  let setID = 'widget' + widgetCount++;
  while( true ) {
    if ( setIdList.indexOf( setID ) === -1 ) {
      break;
    }
    setID = 'widget' + widgetCount++;
  }  
  
  return setID;
}

const getBlankSetID = function() {  
  return 'blank' + widgetBlankCount++;
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   widgetInfoからWidgetを配置する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const initialSet = function() {
  // Widget情報が無ければ初期値を読み込む
  if ( widgetInfo['widget'] && widgetInfo['widget'].length === 0 ) {
    widgetInfo['widget'] = getInitialWidget();
  }
  
  const widgetList = widgetInfo['widget'];
  
  // 読み込みフラグ [Movement,Status,result-History]
  const loadFlag = [ false, false, false, false ];
  // gridフラグ
  const gridFlag = gridCheck();
  
  if ( widgetList ) {
    const widgetHTMLArray = new Array;
    
    const widgetLength = widgetList.length;
    for ( let i = 0; i < widgetLength; i++ ) {
      const widgetItem = widgetList[i],
            a = Number( widgetItem['area'] ),
            r = Number( widgetItem['row'] ),
            c = Number( widgetItem['col'] ),
            rs = Number( widgetItem['rowspan'] ),
            cs = Number( widgetItem['colspan'] );
      let   widgetSetID;
      
      // gird未対応ブラウザはメニュー系のWidget以外は表示しない
      if ( gridFlag === true || ['1','2','3'].indexOf( widgetItem['widget_id'] ) !== -1 ) {
      
      // 別データの読み込みが必要なWidget
      switch ( widgetItem['widget_id'] ) {
        case '4': loadFlag[0] = true; break;
        case '5': loadFlag[1] = true; break;
        case '6': 
        case '7': loadFlag[2] = true; break;
        //#488 start
        case '10': loadFlag[3] = widgetItem['data']['days']; break;
        //#488 end
      }      
      
      if ( widgetItem['set_id'] ) {
        widgetSetID = widgetItem['set_id'];
      } else {
        widgetSetID = getSetID();
        widgetItem['set_id'] = widgetSetID;
      }
      
      // Areaセット
      if ( widgetHTMLArray[a] === undefined ) {
        widgetHTMLArray[a] = '<div id="dashboard-area' + a + '" class="dashboard-area">';
      }
      
      // Widget HTMLを作成
      widgetHTMLArray[a] += getWidgetHTML( widgetSetID, widgetItem );
      
      // 位置情報
      if ( dashboardAreaArray[a] === undefined ) {
        dashboardAreaArray[a] = new Array;
      }
      for ( let j = 0; j < rs; j++ ) {
        if ( dashboardAreaArray[a][r+j] === undefined ) {
          dashboardAreaArray[a][r+j] = new Array;
        }
        for ( let k = 0; k < cs; k++ ) {
          let column = c + k;
          if ( column > maxColumnNumber ) break;
          dashboardAreaArray[a][r+j][column] = widgetSetID;
        }
      }
      }
    }
    
    // div 閉じる
    const widgetHTMLArrayLength = widgetHTMLArray.length;
    for ( let i = 0; i < widgetHTMLArrayLength; i++ ) {
      widgetHTMLArray[i] += '</div>';
    }
    // Areaが空の場合追加する
    for ( let i = 0; i < maxAreaNumber; i++ ) {
      if ( dashboardAreaArray[i] === undefined ) {
        dashboardAreaArray[i] = new Array(1);
        dashboardAreaArray[i][0] = new Array( maxColumnNumber );
        widgetHTMLArray[i] = '<div id="dashboard-area' + i + '" class="dashboard-area"></div>';
      }
    }    
    // WidgetのHTMLセット
    $dashboardBody.html( widgetHTMLArray.join('') );
    // Blank追加用DIV
    $dashboardBody.append('<div class="add-blank"></div>');
    // 隙間をblankで埋める
    widgetCheckBlank();
    // Widgetの位置を更新
    updatePosition();
    // Movement数読み込み
    if ( loadFlag[0] ) get_movement();
    // 作業状況読み込み
    if ( loadFlag[1] ) get_work_info();
    // 作業結果読み込み
    if ( loadFlag[2] ) get_work_result();
    //#488 start
    // Symphony/Conductor 読み込み
    if ( loadFlag[3] ) {
      const days = loadFlag[3];
      get_symphony_conductor( days );
    }
    //#488 end
    }
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widget
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// WidgetIDからsetIDの一覧を返す
const getWidgetSetID = function( widgetID ) {
  const widgetSetIdList = new Array;
  
  $dashboardBody.find('.widget-grid').each( function(){
    const $widget = $( this );
    if ( $widget.attr('data-widget-id') === widgetID ) {
      widgetSetIdList.push( $( this ).attr('id') );
    }
  });  
  if ( widgetSetIdList.length !== 0 ) {
    return widgetSetIdList;
  } else {
    return undefined;
  }
};

// setIDからWidgetの情報を返す
const getWidgetData = function( setID ) {

    const $widget = $('#' + setID );
    
    if ( $widget.length ) {
      const widgetID = $widget.attr('data-widget-id'),
            newWidgetInfo = getWidgetItem( widgetID ),
            displayName = $widget.find('.widget-name-inner').text(),
            rowspan = $widget.attr('data-rowspan'),
            colspan = $widget.attr('data-colspan'),
            position = getWidgetPosition( setID ),
            display = $widget.attr('data-widget-display'),
            title = $widget.attr('data-widget-title'),
            background = $widget.attr('data-widget-background');
      
      // 登録しないデータは削除する
      delete newWidgetInfo['description'];
      delete newWidgetInfo['unique'];
      
      newWidgetInfo['set_id'] = setID;
      newWidgetInfo['display_name'] = displayName;
      newWidgetInfo['rowspan'] = rowspan;
      newWidgetInfo['colspan'] = colspan;
      if ( position !== undefined ) {
        newWidgetInfo['area'] = String( position[0] );
        newWidgetInfo['row'] = String( position[1] );
        newWidgetInfo['col'] = String( position[2] );
      }
      newWidgetInfo['display'] = display;
      newWidgetInfo['title'] = title;
      newWidgetInfo['background'] = background;
      
      // Widget別
      switch( widgetID ) {
        case '1':
        case '2':
          newWidgetInfo['data']['menu_col_number'] = $widget.attr('data-menu-col');
          break;
        case '3': {
          newWidgetInfo['data']['list'] = [];
          $widget.find('.shortcut-link').each(function(i){
            const $link = $( this );
            newWidgetInfo['data']['list'][i] = {
              'name': $link.text(),
              'url': decodeURI( $link.attr('href') ),
              'target': $link.attr('target')
            };
          });
          newWidgetInfo['data']['link_col_number'] = $widget.attr('data-link-col');
          } break;
        case '7':
          newWidgetInfo['data']['period'] = $widget.attr('data-period');
          break;
        case '8':
          newWidgetInfo['data']['html'] = widgetTemp[ setID ]['html'];
          break;
        case '9': {
          const link = decodeURI( $widget.find('.widget-image-link').attr('href') ),
                target = $widget.find('.widget-image-link').attr('target')
          newWidgetInfo['data']['image'] = decodeURI( $widget.find('.widget-image').attr('src') );
          newWidgetInfo['data']['link'] = ( link === undefined || link === 'undefined')? '': link;
          newWidgetInfo['data']['target'] = ( target === undefined || link === 'undefined')? '': target;
          console.log(newWidgetInfo['data']['link'])
          } break;
        case '10':
          newWidgetInfo['data']['days'] = $widget.attr('data-days');
          newWidgetInfo['data']['symphony'] = $widget.attr('data-display-symphony');
          newWidgetInfo['data']['conductor'] = $widget.attr('data-display-conductor');
          break;
      }

      return newWidgetInfo;
    } else {
      return undefined;
    }
};

// 隙間をblankで埋める
const widgetCheckBlank = function() {
  let widgetBlankHTMLArray = new Array;
  const dashboardAreaArrayLength = dashboardAreaArray.length;
  for ( let i = 0; i < dashboardAreaArrayLength; i++ ) {
    widgetBlankHTMLArray[i] = '';
    const rowLength = dashboardAreaArray[i].length;
    for ( let j = 0; j < rowLength; j++ ) {
      if ( dashboardAreaArray[i][j] === undefined ) {
        dashboardAreaArray[i][j] = new Array;
      }
      for ( let k = 0; k <= maxColumnNumber; k++ ) {
        if ( dashboardAreaArray[i][j][k] === undefined ) {
          const blankID = getBlankSetID();
          dashboardAreaArray[i][j][k] = blankID;
          widgetBlankHTMLArray[i] += ''
          + '<div id="' + blankID + '" style="grid-area:' + blankID + '" class="widget-blank-grid" data-rowspan="1" data-colspan="1">'
            + '<div class="widget-blank"></div>'
          + '</div>';
        }
      }
    }
    $('#dashboard-area' + i ).append( widgetBlankHTMLArray[i] );
  }
};

// dashboardAreaArrayから指定のsetIDをundefinedに
const widgetPositionDelete = function( setID ) {
  const dashboardAreaArrayLength = dashboardAreaArray.length;
  for ( let i = 0; i < dashboardAreaArrayLength; i++ ) {
    const rowLength = dashboardAreaArray[i].length;
    for ( let j = 0; j < rowLength; j++ ) {
      const colLength = dashboardAreaArray[i][j].length;
      for ( let k = 0; k < colLength; k++ ) {
        if ( dashboardAreaArray[i][j][k] === setID ) {
          dashboardAreaArray[i][j][k] = undefined;
        }
      }
    }
  }
}

// 指定blankにWidgetをセットする
const widgetPositionChange = function( blankID, setID ) {
  const p = getWidgetPosition( blankID );
  setWidgetSpan( p[0], p[1], p[2], setID );
}

// WidgetのRowspan,Colspan分埋める
const setWidgetSpan = function( area, row, col, setID ) {
  const widgetData = getWidgetData( setID ),
        rowspan = widgetData['rowspan'],
        colspan = widgetData['colspan'];
  for ( let j = 0; j < rowspan; j++ ) {
    const rowPlus = row + j;
    if ( dashboardAreaArray[area][rowPlus] === undefined ) {
      dashboardAreaArray[area][rowPlus] = new Array;
    }
    for ( let k = 0; k < colspan; k++ ) {
      const colPlus = col + k;
      if ( colPlus > maxColumnNumber ) break;
      // 対象がBlankの場合要素を削除する
      const blankCheckID = dashboardAreaArray[area][rowPlus][colPlus];
      if ( blankCheckID !== undefined && blankCheckID.match(/^blank/) ) {
        $('#' + blankCheckID ).remove();
      }
      dashboardAreaArray[area][rowPlus][colPlus] = setID;
    }
  }
};

const widgetMenuButton = function() {

  const $button = $( this ),
        $widget = $( this ).closest('.widget-grid'),
        buttonType = $button.attr('data-type'),
        setID = $widget.attr('id'),
        widgetData = getWidgetData( setID ),
        widgetID = widgetData['widget_id'];

  switch( buttonType ) {
  // 削除
  case 'delete':
    if ( confirm( getWidgetMessage('1') ) ) {
      // メインメニューは削除しない
      if ( widgetID === '1') {
        alert( getWidgetMessage('2') );
        return false;
      }
      // メニューセットの場合、中のメニューをメインメニューに移動する
      if ( widgetID === '2') {
        const mainMenuSetID = getWidgetSetID('1');
        $('#' + mainMenuSetID[0] ).find('.widget-menu-list ')
          .append( $widget.find('.widget-menu-list ').html() );
      }
      widgetPositionDelete( setID );
      widgetCheckBlank();
      updatePosition();
      setChangeFlag( true );
      $widget.remove();
    }
    break;
  // 表示・非表示切り替え
  case 'display':
    if ( $widget.attr('data-widget-display') === '0' ) {
      $widget.attr('data-widget-display', '1');
    } else {
      $widget.attr('data-widget-display', '0');
    }
    setChangeFlag( true );
    break;
  // 編集
  case 'edit':
    editor.modalOpen( getWidgetMessage('24'), editWidget.bind( null, setID ), 'edit-widget');
    break;
  }

};

// Widgetボタンイベント
$dashboardBody.on('click.widgitMenu', '.widget-edit-button', widgetMenuButton );

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widgetを追加する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const addWidget = function( widgetID ) {

  const widgetData = getWidgetItem( widgetID );

  if ( widgetData !== undefined ) {
    const widgetSetID = getSetID(),
          rowspan = Number( widgetData['rowspan'] ),
          colspan = Number( widgetData['colspan'] );
    
    widgetData['set_id'] = widgetSetID;
    
    // 追加できる空きを調べる [ Area, BlankID ]
    const checkAddBlankID = function() {
      const dashboardAreaArrayLength = dashboardAreaArray.length;
      for ( let i = 0; i < dashboardAreaArrayLength; i++ ) {
        const rowLength = dashboardAreaArray[i].length;
        for ( let j = 0; j < rowLength; j++ ) {
          const colLength = dashboardAreaArray[i][j].length;
          for ( let k = 0; k < colLength; k++ ) {
            const checkID = dashboardAreaArray[i][j][k];
            if ( checkID.match(/^blank/) ) {
              let blankFlag = true;
              for ( let l = 0; l < rowspan; l++ ) {
                if ( dashboardAreaArray[i][j+l] !== undefined ) {
                  for ( let m = 0; m < colspan; m++ ) {
                    const checkSpanID = dashboardAreaArray[i][j+l][k+m]; 
                    if ( checkSpanID === undefined || !checkSpanID.match(/^blank/) ) {
                      blankFlag = false;
                    }
                  }
                }
              }
              if ( blankFlag ) {
                return [ i, checkID ];
              }
            }
          }
        }
      }
      return ['', ''];
    }
    
    // 追加する場所とWidgetHTML
    const addSetData = checkAddBlankID(),
          widgetHTML = getWidgetHTML( widgetSetID, widgetData );

    // 追加できる空きがない場合最初のエリアの最後に追加
    if ( addSetData[1] === '') {
      for ( let i = 0; i < rowspan; i++ ) {
        const rowData = [];
        for ( let j = 0; j < colspan; j++ ) {
            rowData.push( widgetSetID );
        }
        dashboardAreaArray[0].push( rowData );
        $dashboardBody.children().eq(0).append( widgetHTML );
      }
    } else {
      $dashboardBody.find('#dashboard-area' + addSetData[0]).append( widgetHTML );
      widgetPositionChange( addSetData[1], widgetSetID );
    }
    
    // 追加したWidgetの値を取得する
    switch( widgetID ) {
      // Movement数読み込み
      case '4': get_movement(); break;
      // 作業状況読み込み
      case '5': get_work_info(); break;
      // 作業結果読み込み
      case '6':
      case '7': get_work_result(); break;
      // Symphony/Conductort読み込み
      case '10': {
        let days = widgetData['data']['days'];
        if ( days === undefined || days === '') days = '0';
        get_symphony_conductor( days );
        } break;
    }
    
    widgetCheckBlank();
    updatePosition();
    setChangeFlag( true );
  }
};

const addWidgetModal = function() {
  const $modalBody = $('.editor-modal-body'),
        widgetList = getWidgetItem();
  let widgetSelectHTML = '<div class="widget-select"><table class="widget-select-table"><tbody>';
  
  for ( let key in widgetList ) {
    if ( key !== '1') {
      // ユニークチェック
      let uniqueClass =  '';
      if ( widgetList[key]['unique'] === '1' ) {
        if ( $dashboard.find('.widget-grid[data-widget-id="' + widgetList[key]['widget_id'] + '"]').length ) {
          uniqueClass =  ' disabled';
        }
      }
      widgetSelectHTML += ''
        + '<tr class="widget-select-row' + uniqueClass + '">'
          + '<th class="widget-select-name">'
            + '<label class="widget-select-label"><input class="widget-select-radio" type="radio" name="widget-radio" value="' + widgetList[key]['widget_id'] + '"' + uniqueClass + '>' + widgetList[key]['display_name'] + '</label>'
          + '</th>'
          +'<td class="widget-select-description">' + widgetList[key]['description'] + '</td>'
        + '</tr>'
    }
  }
  widgetSelectHTML += '</tbody></table></div>'
  
  $modalBody.html( widgetSelectHTML );
  
  // 行クリックで選択
  $modalBody.find('.widget-select-table').on('click', '.widget-select-row', function(){
    const $radio = $( this ).find('.widget-select-radio');
    $radio.prop('checked', true );
  });
  
  // 決定・取り消しボタン
  const $modalButton = $('.editor-modal-footer-menu-button');
  $modalButton.prop('disabled', false ).on('click', function() {
    const $button = $( this ),
          btnType = $button.attr('data-button-type');
    switch( btnType ) {
      case 'ok':
        addWidget( $modalBody.find('.widget-select-radio:checked').val() );
        editor.modalClose();
        break;
      case 'cancel':
        editor.modalClose();
        break;
    }
  });
  
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widgetを編集する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const editWidget = function( setID ) {
  const $modalBody = $('.editor-modal-body'),
        $widget = $('#' + setID ),
        widgetData = getWidgetData( setID );

  let widgetEditHTML = '<div class="widget-edit"><table class="widget-edit-table"><tbody>';
  
  const getRadioHTML = function( name, item, checkedValue ) {
    const itemLength = item.length;
    let radioHTML = '';
    for ( let i = 0; i < itemLength; i++ ) {
      const checked = ( item[i][1] === checkedValue )? ' checked' : '';
      radioHTML += '<div class="widget-edit-radio-wrap">'
        + '<input class="widget-edit-radio" id="radio' + name + i + '" type="radio" name="' + name + '" value="' + item[i][1] + '"' + checked + '>'
        + '<label class="widget-edit-label" for="radio' + name + i + '">' + item[i][0] + '</label>'
      + '</div>';
    }
    return radioHTML;
  };
  const getRadioChecked = function( name ) {
    return $modalBody.find('.widget-edit-radio[name="' + name + '"]:checked').val();
  };
  const getRowHTML = function( name, body ) {
    return '<tr><th class="widget-edit-name">' + name + '</th><td class="widget-edit-body">' + body + '</td></tr>';
  };
  
  // 基本設定
  widgetEditHTML += ''
    + getRowHTML( getWidgetMessage('3'), '<input data-max-length="32" class="edit-input-text edit-display-name" type="text" value="' + editor.textEntities( widgetData['display_name'] ) + '">')
    + getRowHTML( getWidgetMessage('4'), getRadioHTML('colspan',[[1,1],[2,2],[3,3]], Number( widgetData['colspan'] ) ) )
    + getRowHTML( getWidgetMessage('5'), getRadioHTML('rowspan',[[1,1],[2,2],[3,3],[4,4],[5,5]], Number(widgetData['rowspan'] ) ) )
    + getRowHTML( getWidgetMessage('32'), getRadioHTML('title',[[getWidgetMessage('34'),1],[getWidgetMessage('35'),0]], Number(widgetData['title'] ) ) )
    + getRowHTML( getWidgetMessage('33'), getRadioHTML('background',[[getWidgetMessage('34'),1],[getWidgetMessage('35'),0]], Number(widgetData['background'] ) ) );
  
  // Widget別HTML
  switch( widgetData['widget_id'] ) {
    case '1':
    case '2':
      widgetEditHTML += getRowHTML( getWidgetMessage('6'), getRadioHTML('menu-col', [[1,1],[2,2],[4,4],[6,6],[8,8]], Number( widgetData['data']['menu_col_number'] ) ) );
      break;
    case '3': {
      const getShortcutInputRow = function( name, url, target ) {
        return '<tr class="edit-shortcut-row">'
          + '<td class="edit-shortcut-cell edit-shortcut-name"><input data-max-length="32" class="edit-shortcut-input edit-shortcut-input-name" type="text" value="' + editor.textEntities( name, false ) + '"></td>'
          + '<td class="edit-shortcut-cell edit-shortcut-url"><input data-max-length="256" class="edit-shortcut-input edit-shortcut-input-url" type="text" value="' + editor.textEntities( url, false ) + '"></td>'
          + '<td class="edit-shortcut-cell edit-shortcut-target"><input data-max-length="16" class="edit-shortcut-input edit-shortcut-input-target" type="text" value="' + editor.textEntities( target, false ) + '"></td>'
          + '<td class="edit-shortcut-cell edit-shortcut-remove"><button type="button" class="edit-shortcut-button" data-type="remove"><span class="cross-mark"></span></button></td>'
        + '</tr>';
      };
      
      widgetEditHTML += getRowHTML( getWidgetMessage('6'), getRadioHTML('link-col', [[1,1],[2,2],[3,3],[4,4]], Number( widgetData['data']['link_col_number'] ) ) );
      
      const listLength = widgetData['data']['list'].length;
      let shortcutHTML = ''
      + '<ul class="edit-shortcut-menu">'
        + '<li class="edit-shortcut-menu-item edit-shortcut-menu-separate"><button type="button" class="edit-shortcut-button" data-type="add">' + getWidgetMessage('8') + '</button></li>'
        + '<li class="edit-shortcut-menu-item"><button type="button" class="edit-shortcut-button" data-type="save">' + getWidgetMessage('53') + '</button></li>'
        + '<li class="edit-shortcut-menu-item"><button type="button" class="edit-shortcut-button" data-type="read">' + getWidgetMessage('54') + '</button><input type="file" class="edit-shortcut-file"></li>'
      + '</ul>'
      + '<table class="edit-shortcut-table">'
        + '<thead><tr>'
          + '<th class="edit-shortcut-cell edit-shortcut-name">' + getWidgetMessage('3') + '</th>'
          + '<th class="edit-shortcut-cell edit-shortcut-url">URL</th>'
          + '<th class="edit-shortcut-cell edit-shortcut-target">Target</th>'
          + '<th class="edit-shortcut-cell edit-shortcut-remove"><span class="cross-mark"></span></th>'
        + '</tr></thead><tbody>';
      
      for ( let i = 0; i < listLength; i++ ) {
        shortcutHTML += getShortcutInputRow(
          widgetData['data']['list'][i]['name'],
          widgetData['data']['list'][i]['url'],
          widgetData['data']['list'][i]['target']
        );
      }
      // 空行を追加
      shortcutHTML += getShortcutInputRow('','','');
      shortcutHTML += '</tbody></table>';
      
      widgetEditHTML += getRowHTML( getWidgetMessage('7'), shortcutHTML );
      
      // 読み込み
      $modalBody.on('change', '.edit-shortcut-file', function(e){
        const file = e.target.files[0];
        if ( file ) {
          const fileReader = new FileReader();
          fileReader.readAsText( file );
          fileReader.onload = function() {
            
            try {
              linkList = JSON.parse( fileReader.result );
              const $body = $modalBody.find('.edit-shortcut-table > tbody'),
                    linkLength = linkList.length;
              $body.empty();
              for ( let i = 0; i < linkLength; i++ ) {
                const name = linkList[i]['name'],
                      url = linkList[i]['url'],
                      target = linkList[i]['target'];
                $body.append( getShortcutInputRow( name, url, target ) );
              }
            } catch(e) {
              alert( getWidgetMessage('55') );
              return false;
            }
          };
        }
      });
      
      // ボタン
      $modalBody.on('click', '.edit-shortcut-button', function(){
        const $button = $( this ),
              type = $button.attr('data-type');
        switch( type ) {
          case 'add':
            $modalBody.find('.edit-shortcut-table > tbody').append( getShortcutInputRow('','','') );
            break;
          case 'remove':
            $button.closest('.edit-shortcut-row').remove();
            break;  
          case 'save':
            // リンクリストの作成
            const linkList = [];
            $modalBody.find('.edit-shortcut-table > tbody > tr').each(function(i){
              const $tr = $( this ),
                    name = $tr.find('.edit-shortcut-input-name').val();
              if ( name !== '') {
                linkList[i] = {
                  'name': name,
                  'url': $tr.find('.edit-shortcut-input-url').val(),
                  'target': $tr.find('.edit-shortcut-input-target').val()
                };
              }
            });
            
            //　作成したリストをダウンロードする
            const blobText = new Blob( [ JSON.stringify( linkList ) ], { type : 'text/plain' }),
                  $downloadAnchor = $('<a />'),
                  userName = $('#HEADER').find('.itaLoginUserName > .userDataText').text().slice( 0, 128 ),
                  fileName = userName + '_link-list.txt';

            // 一時リンクを作成しダウンロード
            $downloadAnchor.attr({
              'href' : window.URL.createObjectURL( blobText ),
              'download' : fileName,
              'target' : '_blank'
            });
            $modalBody.prepend( $downloadAnchor );
            $downloadAnchor.get(0).click();

            // 生成したBlobを削除しておく
            setTimeout( function(){
              $downloadAnchor.remove()
              window.URL.revokeObjectURL( blobText );
            }, 100 );
            break;
          case 'read':
            const $input = $modalBody.find('.edit-shortcut-file');
            $input.val('');
            $input.get(0).click();
            break;
        }
      });
      } break;
    case '7':
      widgetEditHTML += getRowHTML( getWidgetMessage('29'), '<input data-min="1" data-max="365" class="edit-input-number edit-period-number" type="number" value="' + widgetData['data']['period'] + '">');
      break;
    case '8':
      widgetEditHTML += getRowHTML('HTML', '<textarea class="widget-edit-textarea" id="edit-html">' + editor.textEntities( widgetTemp[setID]['html'] ) + '</textarea>');
      break;
    case '9':
      widgetEditHTML += getRowHTML( getWidgetMessage('36'), '<input data-max-length="256" class="edit-input-text edit-image-url" type="text" value="' + editor.textEntities(  widgetData['data']['image'] ) + '">');
      widgetEditHTML += getRowHTML('Link URL', '<input data-max-length="256" class="edit-input-text edit-image-link" type="text" value="' + editor.textEntities(  widgetData['data']['link'] ) + '">');
      widgetEditHTML += getRowHTML('Link target', '<input data-max-length="16" class="edit-input-text edit-image-target" type="text" value="' + editor.textEntities( widgetData['data']['target'] ) + '">');
      break;
    case '10':
      widgetEditHTML += getRowHTML( getWidgetMessage('48') + '<br><span class="edit-input-note">' + getWidgetMessage('47') + '</span>', '<input data-min="0" data-max="365" class="edit-input-number edit-days-number" type="number" value="' + widgetData['data']['days'] + '">');
      widgetEditHTML += getRowHTML('Symphony', getRadioHTML('display-symphony',[[getWidgetMessage('34'),1],[getWidgetMessage('35'),0]], Number(widgetData['data']['symphony'] ) ) );
      widgetEditHTML += getRowHTML('Condcutor', getRadioHTML('display-conductor',[[getWidgetMessage('34'),1],[getWidgetMessage('35'),0]], Number(widgetData['data']['conductor'] ) ) );
      break;
  }
 
  widgetEditHTML += '</tbody></table></div>'
  $modalBody.html( widgetEditHTML );
  
  // 入力チェック
  editor.inputTextValidation('.editor-modal-body', '.edit-input-text, .edit-shortcut-input');
  editor.inputNumberValidation('.editor-modal-body', '.edit-input-number');
  
  // 決定・取り消しボタン
  const $modalButton = $('.editor-modal-footer-menu-button');
  $modalButton.prop('disabled', false ).on('click', function() {
    const $button = $( this ),
          btnType = $button.attr('data-button-type');
    switch( btnType ) {
      case 'ok': {
        // サイズが大きくなる場合に空きがあるかチェックする
        const newRowspan = getRadioChecked('rowspan'),
              newColspan = getRadioChecked('colspan');              
        if ( newRowspan > widgetData['rowspan'] || newColspan > widgetData['colspan']) {
          const area = widgetData['area'],
                row = Number( widgetData['row'] ),
                col = Number( widgetData['col'] );
          for ( let i = 0; i < newRowspan; i++ ) {
            if ( dashboardAreaArray[area][row + i] !== undefined ) {
              for ( let j = 0; j < newColspan; j++ ) {
                const checkID = dashboardAreaArray[area][row + i][col + j];
                if ( checkID === undefined || ( !checkID.match(/^blank/) && checkID !== setID ) ) {
                  alert( getWidgetMessage('9') );
                  return false;
                }
              }
            }
          } 
        }
        // 名称
        $widget.find('.widget-name-inner').text( $modalBody.find('.edit-display-name').val() );
        // Widget基本設定
        $widget.attr({
          'data-rowspan': newRowspan,
          'data-colspan': newColspan,
          'data-widget-title': getRadioChecked('title'),
          'data-widget-background': getRadioChecked('background')
        });
        // Widget別
        switch( widgetData['widget_id'] ) {
          case '1':
          case '2':
            $widget.attr('data-menu-col', getRadioChecked('menu-col') );
            break;
          case '3': {
            const shortcutList = new Array;
            $modalBody.find('.edit-shortcut-row').each(function(i){
              const $row = $( this ),
                    name = $row.find('.edit-shortcut-input-name').val(),
                    url = $row.find('.edit-shortcut-input-url').val(),
                    target = $row.find('.edit-shortcut-input-target').val();
              if ( name !== '' && name !== undefined ) {
                shortcutList[i] = {
                  'name': name,
                  'url': ( url !== '')? url : '#',
                  'target': target
                }
              }
            });
            $widget.attr('data-link-col', getRadioChecked('link-col') );
            $widget.find('.widget-body').html( getWidgetShortcutHTML( shortcutList ) );
            } break;
          case '7': {
            const period = $modalBody.find('.edit-period-number').val();
            // 変更があったらグラフを更新する
            if ( period !== widgetData['data']['period'] ) {
              $widget.attr('data-period', period );
              get_work_result();
            }
            } break;
          case '8': {
              const html = $('#edit-html').val();
              $widget.find('.widget-body').html( html );
              widgetTemp[setID] = {
                'html': html
              }
            } break;
          case '9': {
            const image = $modalBody.find('.edit-image-url').val(),
                  link = $modalBody.find('.edit-image-link').val(),
                  target = $modalBody.find('.edit-image-target').val();
            let contentHTML = '<img src="' + encodeURI( image ) + '" class="widget-image">';
            if ( link !== '') {
              contentHTML = '<a class="widget-image-link" href="' + encodeURI( link ) + '" target="' + editor.textEntities( target ) + '">' + contentHTML + '</a>'
            }
            $widget.find('.widget-body').html( contentHTML );
            } break;
          case '10': {
            $widget.attr({
              'data-display-symphony': getRadioChecked('display-symphony'),
              'data-display-conductor': getRadioChecked('display-conductor')
            });
            let days = $modalBody.find('.edit-days-number').val();
            if ( days === undefined || days === '') days = '0';
            // 変更があったらグラフを更新する
            if ( days !== widgetData['data']['days'] ) {
              $widget.attr('data-days', days );
              get_symphony_conductor( days );
            }
            } break;
        }
        // 位置を再セット
        widgetPositionDelete( setID );
        setWidgetSpan(
          Number( widgetData['area']),
          Number( widgetData['row']),
          Number(widgetData['col']),
          setID
        );
        widgetCheckBlank();
        updatePosition();
        editor.modalClose();
        }
        break;
      case 'cancel':
        editor.modalClose();
        break;
    }
  });
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   枠外移動スクロール
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
let scrollTimer = false;

const onScrollEvent = function() {
    const scrollSpeed = 40;
    
    const dashboardScroll = function( direction ) {
      if ( scrollTimer === false ) {
        scrollTimer = setInterval( function() {
          const scrollTop = $dashboardBody.scrollTop();
          let scrollWidth = ( direction === 'bottom' ) ? scrollSpeed : -scrollSpeed;
          $dashboardBody.stop(0,0).animate({ scrollTop : scrollTop + scrollWidth }, scrollSpeed, 'linear');
        }, scrollSpeed );
      }
    };
    
    $window.on('mousemove.dashboardScroll', function( e ){
      // 上か下か判定
      const height = $dashboardBody.outerHeight(),
            offsetTop = $dashboardBody.offset().top;
      if ( e.pageY < offsetTop ) {
        dashboardScroll('top');
      } else if ( e.pageY > offsetTop + height ) {
        dashboardScroll('bottom');
      }  else {
        clearInterval( scrollTimer );
        scrollTimer = false;
      }
    });    
};

const offScrollEvent = function() {
    $window.off('mousemove.dashboardScroll');
    clearInterval( scrollTimer );
    scrollTimer = false;
}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widget移動
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 移動できる空きをチェックする
const checkMovableBlank = function( setID ) {
  const dashboardAreaArrayLength = dashboardAreaArray.length;
  for ( let i = 0; i < dashboardAreaArrayLength; i++ ) {
    const rowLength = dashboardAreaArray[i].length;
    for ( let j = 0; j < rowLength; j++ ) {
      const colLength = dashboardAreaArray[i][j].length;
      for ( let k = 0; k < colLength; k++ ) {
        if ( dashboardAreaArray[i][j][k].match(/^blank/) ) {
          if ( checkMovableBlankSpan( i, j, k, setID ) ) {
            $('#' + dashboardAreaArray[i][j][k] ).addClass('movable-blank');
          }
        }
      }
    }
  }
};
const checkMovableBlankSpan = function( area, row, col, setID ) {
  const widgetData = getWidgetData( setID ),
        rowspan = widgetData['rowspan'],
        colspan = widgetData['colspan'];
  for ( let j = 0; j < rowspan; j++ ) {
    const rowPlus = row + j;
    if ( dashboardAreaArray[area][rowPlus] === undefined ) return false;
    for ( let k = 0; k < colspan; k++ ) {
      const colPlus = col + k;
      if ( colPlus > maxColumnNumber ||
        dashboardAreaArray[area][rowPlus][colPlus].match(/^widget/) ) {
        return false;
      }
    }
  }
  return true;
};

$dashboardBody.on('mousedown.widgetMove', '.widget-move-knob', function( e ){

  if ( dashboardAction === 'none' && dashboardMode === 'edit') {
  
    const $widget = $( this ).closest('.widget-grid'),
          setID = $widget.attr('id'),
          widgetData = getWidgetData( setID ),
          widgetWidth = $widget.outerWidth(),
          widgetHeight = $widget.outerHeight(),
          initialArea = $widget.closest('.dashboard-area').attr('id'),
          initialID = 'blank' + widgetBlankCount,
          positionTop = e.pageY - $window.scrollTop(),
          positionLeft = e.pageX - $window.scrollLeft();
    let   targetID = initialID;
    
    dashboardActionChange(2);
    addWidgetBarHide();
    deselection();
    widgetPositionDelete( setID );
    widgetCheckBlank();
    updatePosition();
    checkMovableBlank( setID );
    onScrollEvent();
    
    $widget.addClass('widget-move').css({
      'left': 0,
      'top': 0,
      'transform': 'translate3d(' + positionLeft + 'px,' + positionTop + 'px,0)',
      'width': widgetWidth,
      'height': widgetHeight
    });

    // Rowspanが1の場合は置き換わるブランクの高さを調整する
    const $initialBlank = $('#' + initialID ).find('.widget-blank');
    if ( widgetData['rowspan'] === '1' ) {
      $initialBlank.css('height', widgetHeight + 'px');
    }

    $dashboardBody.find('.movable-blank').on({
      'mouseenter.widgetMove': function(){ targetID = $( this ).attr('id'); },
      'mouseleave.widgetMove': function(){ targetID = initialID; }
    });

    $window.on({
      'mousemove.widgetMove': function( e ){
        const movePositionTop = e.pageY - $window.scrollTop(),
              movePositionLeft = e.pageX - $window.scrollLeft();
        $widget.css({
          'transform': 'translate3d(' + movePositionLeft + 'px,' + movePositionTop + 'px,0)'
        });
      },
      'mouseup.widgetMove': function( e ){
        $window.off('mousemove.widgetMove mouseup.widgetMove');
        $dashboardBody.find('.movable-blank')
          .off('mouseenter.widgetMove mouseleave.widgetMove').removeClass('movable-blank');
        $widget.removeClass('widget-move').attr('style', 'grid-area:' + setID );
        $initialBlank.removeAttr('style');
        // 違うエリアの上ならHTMLを移動する
        const $targetArea = $( e.target ).closest('.dashboard-area');
        if ( initialID !== targetID && initialArea !== $targetArea.attr('id') ) {
          $widget.prependTo( $targetArea );
        }
        
        offScrollEvent();
        dashboardActionChange(0);
        widgetPositionChange( targetID, setID );
        widgetCheckBlank();
        updatePosition();
        setChangeFlag( true );
      }
    });
  
  }

});

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Widgetの位置を更新する
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const updatePosition = function() {
  const widgetStyleArray = new Array,
        widgetGlidArray = new Array;
  if ( dashboardAreaArray !== undefined ) {
    const dashboardAreaLength = dashboardAreaArray.length;
    for ( let i = 0; i < dashboardAreaLength; i++ ) {
      if ( dashboardAreaArray[i] !== undefined ) {
        const dashboardRowLength = dashboardAreaArray[i].length;
        if ( widgetGlidArray[i] === undefined ) {
          widgetGlidArray[i] = new Array;
        }
        for ( let j = 0; j < dashboardRowLength; j++ ) {
          widgetGlidArray[i][j] = '"' + dashboardAreaArray[i][j].join(' ') + '"';
        }
        widgetStyleArray[i] = widgetGlidArray[i].join('');
      }
    } 
  }
  // Grid style
  const widgetStyleLength = widgetStyleArray.length;
  for ( let i = 0; i < widgetStyleLength; i++ ) {
    widgetStyleArray[i] = ''
    + '#dashboard-area' + i + '{'
      + 'grid-template-areas:' + widgetStyleArray[i] + ';}';
  }
  $dashboardGridStyle.html( widgetStyleArray.join('') );
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニュー作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
const widgetMenu = function( menuName ) {
  const menuList = new Array,
        menuHTML = new Array;
  // ソートするため配列に変換
  let i = 0;
  for ( let key in widgetInfo['menu'] ) {
    menuList[i] = widgetInfo['menu'][key];
    menuList[i]['id'] = ('0000000000' + key ).slice( -10 );
    i++;
  }
  if ( menuList ) {
    // メニューをorder順にソートする
    menuList.sort( function( a, b ) {
      if ( Number(a.order) > Number(b.order) ) {
        return 1;
      } else {
        return -1;
      }
    });
    // メニューリスト作成
    const menuListLength = menuList.length;
    for ( let i = 0; i < menuListLength; i++ ) {
      // positionがnullの場合widget0（メインメニュー）とする
      if ( menuList[i].position === null || menuList[i].position === '' ) {
        menuList[i].position = 'widget0';
      }
      if ( menuList[i].position === menuName ) {
        const opneMenuClass = ( openMenuGroupID === menuList[i]['id'] )? ' current-menu-group': '';
        const menuItemHTML = ''
        + '<li class="widget-menu-item' + opneMenuClass + '" data-menu-id="' + menuList[i]['id'] + '">'
          + '<a class="widget-menu-link" href="' + menuGroupURL + menuList[i]['id'] +'">'
            + '<span class="widget-menu-icon">'
              + '<img class="widget-menu-image" src="' + menuList[i]['icon'] + '" alt="' + menuList[i]['name'] + '">'
            + '</span>'
            + '<span class="widget-menu-name">' + menuList[i]['name'] + '</span>'
          + '</a>'
        + '</li>'
        menuHTML.push( menuItemHTML );
      }
    }
    return menuHTML.join('');
  }
};

// メニューグループ名表示
$dashboardBody.on({
  'mouseenter': function() {
    const $item = $( this ),
          $link = $item.find('.widget-menu-link'),
          $icon = $item.find('.widget-menu-icon'),
          $name = $link.find('.widget-menu-name'),
          itemWidth = $link.width(),
          itemImageHeight = $icon.outerHeight(),
          positionX = $link.offset().left - window.pageXOffset,
          positionY = $link.offset().top - window.pageYOffset;
    $item.addClass('link-hover');
    $name.css('min-width', itemWidth );

    // 位置を調整する
    const documentPadding = 4,
          itemTextWidth = $name.outerWidth(),
          itemTextHeight = $name.outerHeight(),
          diffWidtf = ( itemTextWidth - itemWidth ) / 2,
          documentWidth = document.body.clientWidth,
          documentHeight = document.documentElement.clientHeight;
    let positionLeft = positionX - diffWidtf,
        positionTop = itemImageHeight + positionY + documentPadding;
    // Left check
    if ( positionLeft <= documentPadding ) {
      positionLeft = documentPadding;
    }
    // Right check
    if ( positionLeft + itemTextWidth > documentWidth ) {
      positionLeft = documentWidth - itemTextWidth - documentPadding;
    }
    // Bottom check
    if ( positionTop + itemTextHeight > documentHeight ) {
      positionTop = positionY - itemTextHeight - documentPadding;
    }        
    $name.css({
      'top': positionTop,
      'left': positionLeft,
      'bottom': 'auto',
      'min-width': itemWidth
    });
    $dashboardBody.on('scroll.menuName', function(){
      $item.removeClass('link-hover');
      $item.find('.widget-menu-name').css({
        'top': 'auto',
        'left': 0,
        'bottom': 0,
        'min-width': 'auto'
      });
    });
  },
  'mouseleave': function() {
    $dashboardBody.off('scroll.menuName');
    const $item = $( this );
    $item.removeClass('link-hover');
    $item.find('.widget-menu-name').css({
      'top': 'auto',
      'left': 0,
      'bottom': 0,
      'min-width': 'auto'
    });
  }
}, '.widget-menu-item');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Blank追加・削除
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 対象のWidgetの上下にBrankを追加できるかチェック
let blankAddTopBottomFlag = '', blankAddWidgetSetID = '';

$dashboardBody.on({
  'mouseenter.blankAdd': function(){
    if ( dashboardAction === 'none' && dashboardMode === 'edit') {
      const $widget = $( this ),
            $area = $widget.closest('.dashboard-area'),
            setID = $widget.attr('id'),
            rowspan = Number( $widget.attr('data-rowspan') ),
            widgetHeight = $widget.outerHeight(),
            barWidth = $area.outerWidth(),
            barLeft = $area.position().left,
            barTop = $widget.position().top,
            position = getWidgetPosition( setID ),
            area = position[0],
            row = position[1],
            colLength = dashboardAreaArray[area][row].length,
            barShowArea = 8,
            areaPadding = 8;
      let scrollTop = $dashboardBody.scrollTop();
      
      if ( blankAddWidgetSetID !== setID ) {
        blankAddWidgetSetID = setID;
        blankAddTopBottomFlag = '';
      }
      
      // 削除可能なブランクかチェック
      if ( dashboardAreaArray[area].length > 1 ) {
        if ( dashboardAreaArray[area][row].join('').indexOf('widget') === -1 ) {
          for ( let i = 0; i < colLength; i++ ) {
            $('#' + dashboardAreaArray[area][row][i] ).addClass('remove-blank');
          }
        }
      }

      // Widgetの上か下かそれ以外
      const middleBarHide = function() {
        $('.add-blank').css('display', 'none');
      }
      const topBottomBarSet = function( direction ){
        const topBottomNum = ( direction === 'top')? -1 : rowspan,
              topBottomAdd = ( direction === 'top')? 0 : rowspan,
              topBottomPositionTop = ( direction === 'top')?
                barTop - areaPadding + scrollTop :
                barTop - areaPadding + scrollTop + widgetHeight;
      
        let addFlag = true;
        for ( let i = 0; i < colLength; i++ ) {
          const currentCol = dashboardAreaArray[area][row][i],
                checkRow = dashboardAreaArray[area][row+topBottomNum];
          if ( checkRow !== undefined ) {
            if ( currentCol === checkRow[i] ) {
              addFlag = false;
              break;
            }
          }
        }

        if ( addFlag === true ) {
          $('.add-blank').css({
            'display': 'block',
            'width': barWidth - ( areaPadding * 2 ),
            'left': barLeft + areaPadding,
            'top': topBottomPositionTop
          }).attr({
            'data-area': area,
            'data-row': row + topBottomAdd
          });
        } else {
          middleBarHide();
        }
      };
      $widget.on('mousemove.blankAdd', function( e ){
        if ( e.pageY - $widget.offset().top > widgetHeight - barShowArea ) {
          if ( blankAddTopBottomFlag !== 'bottom') {
            blankAddTopBottomFlag = 'bottom';
            topBottomBarSet( blankAddTopBottomFlag );
          }
        } else if ( e.pageY - $widget.offset().top < barShowArea ) {
          if ( blankAddTopBottomFlag !== 'top') {
            blankAddTopBottomFlag = 'top';
            topBottomBarSet( blankAddTopBottomFlag );
          }
        } else {
          if ( blankAddTopBottomFlag !== 'middle') {
            blankAddTopBottomFlag = 'middle';
            middleBarHide();
          }
        }
      });
    }
  },
  'mouseleave.blankAdd': function(){
    $( this ).off('mousemove.blankAdd');
    $dashboardBody.find('.remove-blank').removeClass('remove-blank');
  }
}, '.widget-grid, .widget-blank-grid');

// 枠の外に出たら消す
$dashboardBody.on({
  'mouseleave.blankAdd': function(){
    if ( dashboardAction === 'none' && dashboardMode === 'edit') {
      addWidgetBarHide();
    }
  },
  'mousemove.blankAdd': function( e ){
    if ( dashboardAction === 'none' && dashboardMode === 'edit') {
      if ( e.target.className === 'dashboard-body' ||
           e.target.className === 'dashboard-area' ) {
        addWidgetBarHide();
      }
    }
  }
});

// Blankを追加
$dashboardBody.on('click.blankAdd', '.add-blank', function(){
  const $addBlank = $( this ),
        area = $addBlank.attr('data-area'),
        row = $addBlank.attr('data-row');

  dashboardAreaArray[area].splice( row, 0, []);
  widgetCheckBlank();
  updatePosition();
});

// Blankを削除
$dashboardBody.on('click.removeAdd', '.remove-blank', function(){
  if ( dashboardAction === 'none' && dashboardMode === 'edit') {
    const $blank = $( this ),
          setID = $blank.attr('id'),
          position = getWidgetPosition( setID ),
          area = position[0],
          row = position[1],
          colLength = dashboardAreaArray[area][row].length;
    
    for ( let i = 0; i < colLength; i++ ) {  
      $('#' + dashboardAreaArray[area][row][i] ).remove();
    }
    dashboardAreaArray[area].splice( row, 1 );
    addWidgetBarHide();
    updatePosition();
  }
});

// +Blank barを非表示
const addWidgetBarHide = function() {
    $dashboardBody.find('.add-blank').removeAttr('style');
    $dashboardBody.find('.widget-grid, .widget-blank-grid').off('mousemove.blankAdd');
    blankAddTopBottomFlag = '';
    blankAddWidgetSetID = '';
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   個別メニュー移動
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
$dashboardBody.on({
  'click': function( e ) {
    // エディットモードの場合クリックを無効化
    if ( dashboardMode === 'edit' ) {
      e.preventDefault();
    }
  },
  'mousedown': function( e ){
    if ( dashboardAction === 'none' && dashboardMode === 'edit') {
      e.preventDefault();
      
      onScrollEvent();
      deselection();
      addWidgetBarHide();
      
      const $item = $( this );
      
      let listType, itemType, idName, itemWidth, itemHeight;
      
      // .widget-menu-link or .shortcut-link
      if ( $item.is('.widget-menu-link') ) {
        dashboardActionChange(1);
        listType = '.widget-menu-list';
        itemType = '.widget-menu-item';
        idName = 'data-menu-id';
        const $itemImage = $item.find('.widget-menu-image');
        itemWidth = $itemImage.width();
        itemHeight = $itemImage.height();
      } else {
        dashboardActionChange(4);
        listType = '.shortcut-list';
        itemType = '.shortcut-item';
        idName = 'data-link-id';
        itemWidth = $item.outerWidth();
        itemHeight = $item.outerHeight();
      }      
      
      const $itemWrap = $item.closest( itemType ),
            mouseDownLeft = e.pageX,
            mouseDownTop = e.pageY,
            clickPositionLeft = mouseDownLeft - $item.offset().left,
            clickPositionTop = mouseDownTop - $item.offset().top;
      
      let scrollTop = $window.scrollTop(),
          scrollLeft = $window.scrollLeft(),
          left = mouseDownLeft - scrollLeft - clickPositionLeft,
          top = mouseDownTop - scrollTop - clickPositionTop;
      
      $item.removeClass('link-hover');
      const $clone = $itemWrap.clone( false );
      
      $item.addClass('move-wait');
      $itemWrap.addClass('current');
      $clone.addClass('move').css({
        'width': itemWidth,
        'height': itemHeight,
        'transform': 'translate3d(' + left + 'px,' + top + 'px,0)',
      });
      $kizi.append( $clone );
      
      // どのメニューグループの上か
      let targetID = $itemWrap.attr( idName ),
          leftRight = 0;
      $dashboardBody.find( itemType ).on({
        'mouseenter.menuItemMove1': function(){ targetID = $( this ).attr( idName ); },
        'mousemove.menuItemMove1': function( e ){
          // 左右どちらか判定
          const $target = $( this ),
                width = $target.width();
          if ( !$target.is('.current') ) {
            if ( e.pageX - $target.offset().left > width / 2 ) {
              leftRight = 1;
              $target.removeClass('left').addClass('right');
            } else {
              leftRight = 0;
              $target.removeClass('right').addClass('left');
            }
          }
        },
        'mouseleave.menuItemMove1': function(){
          targetID = null;
          $( this ).removeClass('left right');
        }
      });
      
      // どのWidgetの上か
      let targetWidget = $item.closest('.widget-grid').attr('id');
      $dashboardBody.find('.widget-grid').on({
        'mouseenter.menuItemMove2': function(){ targetWidget = $( this ).attr('id'); },
        'mouseleave.menuItemMove2': function(){ targetWidget = null; }
      });
      
      $window.on({
        'mousemove.menuItemMove': function( e ) {
          scrollTop = $window.scrollTop();
          scrollLeft = $window.scrollLeft();
          left = e.pageX - scrollLeft - clickPositionLeft;
          top = e.pageY - scrollTop - clickPositionTop;
          $clone.css({
            'transform': 'translate3d(' + left + 'px,' + top + 'px,0)'
          });
        },
        'mouseup.menuItemMove': function() {
          offScrollEvent();
          dashboardActionChange(0);
          $window.off('mousemove.menuItemMove mouseup.menuItemMove');
          $dashboardBody.find( itemType )
            .off('mouseenter.menuItemMove1 mousemove.menuItemMove1 mouseleave.menuItemMove1');
          $dashboardBody.find('.widget-grid')
            .off('mouseenter.menuItemMove2 mouseleave.menuItemMove2');
          $item.removeClass('move-wait');
          $itemWrap.removeClass('current');
          $clone.remove();
          // 対象があれば移動する
          if ( targetWidget !== null ) {
            if ( targetID !== null ) {
              const $targetMenuGroup = $dashboardBody.find( itemType + '[' + idName + '="' + targetID + '"]');
              $targetMenuGroup.removeClass('left right');
              if ( leftRight === 0 ) {
                $itemWrap.insertBefore( $targetMenuGroup );
              } else {
                $itemWrap.insertAfter( $targetMenuGroup );
              }
            } else {
              $itemWrap.appendTo( $('#' + targetWidget ).find( listType ) );
            }
            setChangeFlag( true );
          }
        }
      });
    }
  },
}, '.widget-menu-link, .shortcut-link');

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   登録
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// Widget set ID から位置を返す
const getWidgetPosition = function( setID ) {
  const dashboardAreaArrayLength = dashboardAreaArray.length;
  for ( let i = 0; i < dashboardAreaArrayLength; i++ ) {
    const rowLength = dashboardAreaArray[i].length;
    for ( let j = 0; j < rowLength; j++ ) {
      const colLength = dashboardAreaArray[i][j].length;
      for ( let k = 0; k < colLength; k++ ) {
        if ( dashboardAreaArray[i][j][k] === setID ) {
          return [ i, j, k ];
        }
      }
    }
  }
  return undefined;
};

// リセットデータ作成
const createResetWidgetInfo = function() {

  // Widget情報消去
  widgetInfo['widget'] = [];
  
  // Menu order更新
  $dashboardBody.find('.widget-menu-item').each(function(){
    const $item = $( this ),
          menuID = Number( $item.attr('data-menu-id') );

    // Order
    if ( widgetInfo['menu'][menuID] ) {
      widgetInfo['menu'][menuID]['order'] = '';
      widgetInfo['menu'][menuID]['position'] = '';
    }
  });
  
  log( 'Regist widget info -----', widgetInfo );
  registWidgetInfo( JSON.stringify( widgetInfo ) );

};

// 登録データ作成
const createNewWidgetInfo = function() {

  // Widget情報更新
  widgetInfo['widget'] = [];
  $dashboardBody.find('.widget-grid').each( function(){
    widgetInfo['widget'].push( getWidgetData( $( this ).attr('id') ) );
  });
  
  // Menu order更新
  $dashboardBody.find('.widget-menu-item').each(function(){
    const $item = $( this ),
          menuID = Number( $item.attr('data-menu-id') ),
          order = $item.index(),
          position = $item.closest('.widget-grid').attr('id');
    // Order
    if ( widgetInfo['menu'][menuID] ) {
      widgetInfo['menu'][menuID]['order'] = order;
      widgetInfo['menu'][menuID]['position'] = position;
    }
  });
  
  log( 'Regist widget info -----', widgetInfo );
  registWidgetInfo( JSON.stringify( widgetInfo ) );
};

// Widget情報登録
let registDoneMessage = '',
    registFailMessage = '';
const registWidgetInfo = function( registData ) {
  // 登録処理結果イベント
  $window.on({
    'registWidgetInfoDone': function(){
      alert( registDoneMessage );
      // 再読み込み
      window.location.reload();
    },
    'registWidgetInfoFail': function(){
      $window.off('registWidgetInfoDone registWidgetInfoFail');
      alert( registFailMessage );
      releaseRestriction();
    }
  });
  regist_widget_info( registData );
};

// 操作制限
const setRestriction = function() {
  // メニューボタンをdisabledに
  $dashboard.find('.dashboard-menu-button').prop('disabled', true );
  dashboardActionChange(3);
};
const releaseRestriction = function() {
  // メニューボタンをdisabledに
  $dashboard.find('.dashboard-menu-button').prop('disabled', false );
  dashboardActionChange(0);
};

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   メニューボタン
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

if ( gridCheck() === true ) {
$dashboard.find('.dashboard-menu').find('.dashboard-menu-button').on('click', function(){
  const $button = $( this ),
        type = $button.attr('data-button');
  
  switch( type ) {
    case 'add':
      editor.modalOpen( getWidgetMessage('12'), addWidgetModal, 'add-widget');
      break;
    case 'edit':
      dashboardModeChange( 1 );
      break;
    case 'cancel':
      if ( changeFlag === true ) {
        if ( confirm( getWidgetMessage('13') ) ) {
          setRestriction();
          // 再読み込み
          window.location.reload();
        }
      } else {
        dashboardModeChange( 0 );
      }      
      break;
    case 'reset':
      if ( confirm( getWidgetMessage('14') ) ) {
        setRestriction();
        registDoneMessage = getWidgetMessage('30');
        registFailMessage = getWidgetMessage('31');
        createResetWidgetInfo();
      }
      break;
    case 'regist':
      if ( confirm( getWidgetMessage('15') ) ) {
        setRestriction();
        registDoneMessage = getWidgetMessage('10');
        registFailMessage = getWidgetMessage('11');
        createNewWidgetInfo();
      }
      break;
  }
});
} else {
$dashboard.attr('data-grid','false').find('.dashboard-menu').find('.dashboard-menu-button').remove();
}


////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   初期配置
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

initialSet();

} // function set_widget()




////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   Symphony, Conductor 予約リスト
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
function setSymphonyConductorList( resultData ) {
  const editor = new itaEditorFunctions;
  
  const widgetID = 10,
        $target = $('.widget-grid[data-widget-id="' + widgetID + '"]').eq(0),
        days = $target.attr('data-days');
  
  let tableHTML = '';
  
  for ( const type in resultData ) {
    const title = ( type === 'conductor')? 'Conductor': 'Symphony',
          name = ( type === 'conductor')? 'conductor_name': 'symphony_name',
          url =  ( type === 'conductor')?
            '/default/menu/01_browse.php?no=2100180005&conductor_instance_id=':
            '/default/menu/01_browse.php?no=2100000309&symphony_instance_id=',
          idLength = Object.keys( resultData[type] ).length;
    
    tableHTML += ''
    + '<div class="widget-sub-name reserve-' + type + '">' + title + '</div>'
    + '<div class="dashboard-table-wrap reserve-' + type + '">';
  
    if ( idLength > 0 ) {
      tableHTML += ''
      + '<table class="dashboard-table">'
        + '<thead>'
          + '<tr>'
            + '<th>' + getWidgetMessage('40') + ' / ' + getWidgetMessage('41') + '</th>'
            + '<th>' + getWidgetMessage('42') + '</th>'
            + '<th>' + getWidgetMessage('43') + '</th>'
            + '<th>' + getWidgetMessage('44') + '</th>'
            + '<th>' + getWidgetMessage('49') + '</th>'
          + '</tr>'
        + '</thead>'
        + '</tbody>';
      
      // ソート用配列の作成
      const tableArray = [];
      for ( const id in resultData[type] ) {
        tableArray.push({
          'id': id,
          'name': resultData[type][id][name],
          'operation_name': resultData[type][id].operation_name,
          'status': resultData[type][id].status,
          'time_book': resultData[type][id].time_book
        });
      }
      // 日時でソート
      tableArray.sort(function( a, b ){
        if ( a.time_book > b.time_book ) {
          return 1;          
        } else {
          return -1;
        }
      });
      
      const tableArrayLength = tableArray.length;
      for ( let i = 0; i < tableArrayLength; i++ ) {
        tableHTML += ''
        + '<tr class="reserve-row">'
          + '<td class="dashboard-table-cell-wrap"><a class="rID" href="' + url + tableArray[i].id + '" target="_blank">' + tableArray[i].id + '</a>' + editor.textEntities( tableArray[i].name ) + '</td>'
          + '<td class="dashboard-table-cell-wrap">' + editor.textEntities( tableArray[i].operation_name ) + '</td>'
          + '<td class="dashboard-table-cell-nowrap"><span class="dashboard-reserve-status"><span class="dashboard-reserve-status-icon"></span>' + editor.textEntities( tableArray[i].status ) + '</span></td>'
          + '<td class="dashboard-table-cell-nowrap reserve-date">' + tableArray[i].time_book + '</td>'
          + '<td class="dashboard-table-cell-nowrap reserve-count-down"></td>'
        + '</tr>';
      }
      
      tableHTML += '</tbody></table>';
 
    } else {
      if ( days === 0 ) {
        tableHTML += '<p class="dashboard-text">' + getWidgetMessage('45') + '</p>';
      } else {
        const daysMessage = getWidgetMessage('46').replace(/{{N}}/, days );
        tableHTML += '<p class="dashboard-text">' + daysMessage + '</p>';
      }
    }
    tableHTML += '</div>';
  }
  
  $target.find('.widget-body').html( tableHTML );
  
  // カウントダウンする
  const zP = function( text, digit ){
    const num = ('0000000000' + text ).slice( -digit ).replace(/^(0+)/,'<span class="zero">$1</span>');
    return num;    
  };
  const coundDown = function(){
    // タイマーを停止する
    const $reserveWidget = $('.widget-grid[data-widget-id="' + widgetID + '"]').eq(0),
          widgetTimerID = Number( $reserveWidget.attr('data-timer-id'));
    if ( widgetTimerID !== intervalID ) {
      clearInterval( intervalID );
      return false;
    }
    
    const today = new Date();
    $reserveWidget.find('.reserve-date').each(function(){
      const $date = $( this ),
            time = new Date( $date.text() ),
            diff = time - today;
      
      const day = ( diff >= 0 )? Math.floor( diff / (24 * 60 * 60 * 1000) ): 0,
            hour = ( diff >= 0 )? Math.floor(( diff % (24 * 60 * 60 * 1000)) / (60 * 60 * 1000)): 0,
            min = ( diff >= 0 )? Math.floor(( diff % (24 * 60 * 60 * 1000)) / (60 * 1000)) % 60: 0,
            html = '<span class="rd">'+zP(day,3)+'</span>' + getWidgetMessage('50') + '<span class="rd">'+zP(hour,2)+'</span>' + getWidgetMessage('51') + '<span class="rd">'+zP(min,2)+'</span>' + getWidgetMessage('52');
      
      if ( diff <= 0 ) {
        $date.closest('tr').addClass('running').removeClass('shortly');
      } else if ( day == 0 ) {
        $date.closest('tr').addClass('shortly');
      }
      
      $date.next().html( html );
    });
  };
  
  const intervalID = setInterval( coundDown, 60000 );
  $target.attr('data-timer-id', intervalID );
  coundDown();
  
  
}



////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   円グラフ作成
// 
////////////////////////////////////////////////////////////////////////////////////////////////////

// 角度から円周の座標を返す
function setPiePosition( x1, y1, r, a ) {
    const x2 = x1 + r * Math.cos( a * ( Math.PI / 180 ) ),
          y2 = y1 + r * Math.sin( a * ( Math.PI / 180 ) );
    return [x2,y2];
}

// 表示テスト用ダミーデータ
const gWidgetDummyFlag = false;
function movementDummyData() {
    return {
    "Ansible Legacy":["ansible-legacy", 1],
    "Ansible Pioneer":["ansible-pioneer", 2],
    "Ansible Legacy Role":["ansible-legacy-role", 200],
    "Terraform":["terraform", 4],
    "OpenStack":["openstack", 3],
    };
}
function statusDummyData() {
    const status = {};
    status[ getWidgetMessage('22') ] = ["runing",15,10,5];
    status[ getWidgetMessage('23') ] = ["schedule",18,12,6];
    status[ getWidgetMessage('38') ] = ["waiting",5,2,3];
    return status;
}
function resultDummyData() {
    const result = {};
    result[ getWidgetMessage('17') ] = ["done",850,434,416];
    result[ getWidgetMessage('18') ] = ["fail",30,15,15];
    result[ getWidgetMessage('19') ] = ["error",27,13,14];
    result[ getWidgetMessage('20') ] = ["stop",25,12,13];
    result[ getWidgetMessage('21') ] = ["cancel",28,13,15];
    return result;
}
function setPieChart( resultData, type ) {
  
  // Movement一覧URL
  const movementListURL = {
    'ansible-legacy': '/default/menu/01_browse.php?no=2100020103',
    'ansible-pioneer': '/default/menu/01_browse.php?no=2100020203',
    'ansible-legacy-role': '/default/menu/01_browse.php?no=2100020306',
    'terraform': '/default/menu/01_browse.php?no=2100080004'
  };
      
  let widgetID, pieChartData, pieChartTitle = '';

  switch( type ) {
    case 'movement': {
      widgetID = 4;
      pieChartData = {};
      pieChartTitle = 'Movement';
      for ( let key in resultData ) {
        const className = resultData[key]['name'].replace(/\s/g, '-').toLocaleLowerCase();
        pieChartData[ resultData[key]['name'] ] = [ className, resultData[key]['number'] ];
        if ( movementListURL[ className ] !== undefined ) {
          pieChartData[ resultData[key]['name'] ][2] = movementListURL[ className ];
        } else {
          pieChartData[ resultData[key]['name'] ][2] = undefined;
        }
      }
      // ダミーデータ読み込み
      if ( gWidgetDummyFlag === true ) pieChartData = movementDummyData();
      }
      break;
    case 'status':
      widgetID = 5;
      pieChartTitle = 'Status';
      
      pieChartData = {};
      pieChartData[ getWidgetMessage('22') ] = ['runing',0,0,0];
      pieChartData[ getWidgetMessage('23') ] = ['schedule',0,0,0];
      pieChartData[ getWidgetMessage('38') ] = ['waiting',0,0,0];
      
      for ( let type in resultData ) {
        for ( let key in resultData[type] ) {
          const status = resultData[type][key]['status'],
                typeNumber = ( type === 'conductor')?2:3;
          if ( pieChartData[status] !== undefined ) {
            pieChartData[status][typeNumber] += 1;
            pieChartData[status][1] += 1;
          }
        }
      }
      // ダミーデータ読み込み
     if ( gWidgetDummyFlag === true ) pieChartData = statusDummyData();
      break;
    case 'result':
      widgetID = 6;
      pieChartTitle = 'Result';
      
      pieChartData = {};
      pieChartData[ getWidgetMessage('17') ] = ['done',0,0,0];
      pieChartData[ getWidgetMessage('18') ] = ['fail',0,0,0];
      pieChartData[ getWidgetMessage('19') ] = ['error',0,0,0];
      pieChartData[ getWidgetMessage('20') ] = ['stop',0,0,0];
      pieChartData[ getWidgetMessage('21') ] = ['cancel',0,0,0];

      for ( let type in resultData ) {
        for ( let key in resultData[type] ) {
          const status = resultData[type][key]['status'],
                typeNumber = ( type === 'conductor')?2:3;
          if ( pieChartData[status] !== undefined ) {
            pieChartData[status][typeNumber] += 1;
            pieChartData[status][1] += 1;
          }
        }
      }
      // ダミーデータ読み込み
      if ( gWidgetDummyFlag === true ) pieChartData = resultDummyData();
      break;
    default:
      return false;
  }

  const xmlns = 'http://www.w3.org/2000/svg',
        radius = 50,  // 円グラフ半径
        strokeWidth = 20,  // 円グラフ線幅
        circumference = radius * 2 * 3.14, // 円周（半径×２×円周率）
        cxcy = radius + strokeWidth,
        viewBox = cxcy * 2;
  
  let totalNumber = 0;
  
  // 円グラフ
  const $pieChartSvg = $( document.createElementNS( xmlns, 'svg') );
  $pieChartSvg.get(0).setAttribute('viewBox', '0 0 ' + viewBox + ' ' + viewBox );
  $pieChartSvg.attr('class','pie-chart-svg');
  
  // 合計値
  for ( let key in pieChartData ) {
    totalNumber += pieChartData[key][1];
  }
  
  // Table
  const conductorListURL = '/default/menu/01_browse.php?no=2100180006',
        symphonyListURL = '/default/menu/01_browse.php?no=2100000310',
        statusInputID = 'Filter1Tbl_4';
  let tableHTML = '<div class="number-table-wrap"><table class="number-table"><thead>';
  // thead
  tableHTML += '<tr><th>' + pieChartTitle + '</th>';
  if ( type !== 'movement') {
    tableHTML += '<th>CON</th><th>SYM</th>'
  }
  tableHTML += '<th>SUM</th></tr></thead><tbody>'
  
  const zeroCheck = function( num ) {
    return ( num === 0 )? '<span class="zero">0</span>': num;
  };
  
  // tbody
  for ( let key in pieChartData ) {
    tableHTML += '<tr data-type="' + pieChartData[key][0] + '"><th><span class="pie-chart-usage ' + type + '-' + pieChartData[key][0] + '"></span>' + key + '</th>';
    if ( type !== 'movement') {
      // Conductor
      if ( pieChartData[key][2] !== 0 ) {
        tableHTML += '<td><a href="' + conductorListURL + '&filter=on&' + statusInputID + '=' + encodeURIComponent( key ) + '" target="_blank" title="' + key + '">'
        + pieChartData[key][2] + '</a></td>';
      } else {
        tableHTML += '<td>' + zeroCheck( pieChartData[key][2] ) + '</td>';
      }
      // Symphony
      if ( pieChartData[key][3] !== 0 ) {
        tableHTML += '<td><a href="' + symphonyListURL + '&filter=on&' + statusInputID + '=' + encodeURIComponent( key ) + '" target="_blank" title="' + key + '">'
        + pieChartData[key][3] + '</a></td>';
      } else {
        tableHTML += '<td>' + zeroCheck( pieChartData[key][3] ) + '</td>';
      }
      // Sum
      tableHTML += '<td>' + zeroCheck( pieChartData[key][1] ) + '</a></td></tr>';
    } else {
      // Movement
      if ( pieChartData[key][2] !== undefined ) {
        tableHTML += '<td><a href="' + pieChartData[key][2] + '" target="_blank" title="' + key + '">' + pieChartData[key][1] + '</a></td></tr>';
      } else {
        tableHTML += '<td>' + zeroCheck( pieChartData[key][1] ) + '</td></tr>';
      }
    }
  }
  tableHTML += '</tbody></table></div>';
  
  // 割合表示
  const $pieChartRatioSvg = $( document.createElementNS( xmlns, 'svg') );
  $pieChartRatioSvg.get(0).setAttribute('viewBox', '0 0 ' + viewBox + ' ' + viewBox );
  $pieChartRatioSvg.attr('class','pie-chart-ratio-svg');
  
  // 各項目
  const outSideNamber = [];
  let serialWidthNumber = 0,
      serialAngleNumber = -90,
      outsideGroupCheck = 0,
      outsideGroupNumber = -1,
      checkGroupText = '';

  if ( totalNumber !== 0 ) {
    for ( let key in pieChartData ) {
      const $pieChartCircle = $( document.createElementNS( xmlns, 'circle') ),
            $pieChartText = $( document.createElementNS( xmlns, 'text') );
      // 割合・幅の計算
      const className = 'circle-' + pieChartData[key][0],
            number = pieChartData[key][1],
            ratio = number / totalNumber,
            angle = 360 * ratio;

      let   ratioWidth = Math.round( circumference * ratio * 1000 ) / 1000;
      if ( serialWidthNumber + ratioWidth > circumference ) ratioWidth = circumference - serialWidthNumber;

      const remainingWidth =  Math.round( ( circumference - ( serialWidthNumber + ratioWidth ) ) * 1000 ) / 1000;

      // stroke-dasharrayの形に整える
      let strokeDasharray = '';
      if ( serialWidthNumber === 0 ) {
        strokeDasharray = ratioWidth + ' '+ remainingWidth + ' 0 0';
      } else {
        strokeDasharray = '0 ' + serialWidthNumber + ' ' + ratioWidth + ' '+ remainingWidth;
      }
      // 属性登録
      $pieChartCircle.attr({
        'cx': cxcy,
        'cy': cxcy,
        'r': radius,
        'class': 'pie-chart-circle ' + className,
        'style': 'stroke-dasharray:0 0 0 '+ circumference,
        'data-style': strokeDasharray,
        'data-type': pieChartData[key][0]
      });
      // 追加
      $pieChartSvg.append( $pieChartCircle );
      // Movementの場合リンクを追加する
      if ( type === 'movement') {
        const $pieChartLink = $( document.createElementNS( xmlns, 'a') );
        $pieChartLink.attr({
          'href': movementListURL[ pieChartData[key][0] ],
          'xlink:href': movementListURL[ pieChartData[key][0] ],
          'target': '_blank'
        });
        $pieChartCircle.wrap( $pieChartLink );
      }
      // 割合追加
      if ( ratio > 0 ) {
        const textAngle = serialAngleNumber + ( angle / 2 ),
              centerPosition = setPiePosition( cxcy, cxcy, radius, textAngle );
        let ratioClass = 'pie-chart-ratio ' + className,
            x = centerPosition[0],
            y = centerPosition[1];
        
        const displayRatio = Math.round( ratio * 1000 ) / 10;
        
        // 特定値以下の場合は表示の調整をする
        if ( displayRatio < 2.5 ) {
          if ( outsideGroupCheck === 0 ) {
            checkGroupText += '@'; // グループフラグ
            outsideGroupNumber++;
            outSideNamber[outsideGroupNumber] = new Array();
          }
          outsideGroupCheck = 1;
          outSideNamber[outsideGroupNumber].push( [ratioClass,textAngle,displayRatio] );
        } else {
          // 30%以下の場合グループを分けない
          if ( displayRatio > 30 ) {
            outsideGroupCheck = 0;
            checkGroupText += 'X';
          }
          if ( displayRatio < 10 ) {
            ratioClass += ' rotate';
            let rotateAngle = textAngle;
            if ( textAngle > 90 ) rotateAngle = rotateAngle + 180;
            $pieChartText.attr('transform', 'rotate('+rotateAngle+','+x+','+y+')' );
             y += 1.5; //ベースライン調整
          } else {
             y += 2.5;
          }
          $pieChartText.html( displayRatio + '<tspan class="ratio-space"> </tspan><tspan class="ratio-mark">%</tspan>' ).attr({
            'x': x,
            'y': y,
            'text-anchor': 'middle',
            'class': ratioClass
          });
          $pieChartRatioSvg.append( $pieChartText );
        }
      }
      // スタート幅
      serialWidthNumber += ratioWidth;
      serialAngleNumber += angle;
      if ( serialWidthNumber > circumference ) serialWidthNumber = circumference;
    }
    // 2.5%以下は外側に表示する
    let outSideGroupLength = outSideNamber.length;
    if ( outSideNamber.length > 0 ) {
      // 最初と最後が繋がる場合、最初のグループを最後に結合する
      if ( checkGroupText.length > 2 && checkGroupText.slice( 0, 1 ) === '@' && checkGroupText.slice( -1 ) === '@' ) {
        outSideNamber[ outSideGroupLength - 1] = outSideNamber[ outSideGroupLength - 1].concat( outSideNamber[0] );
        outSideNamber.shift();
        outSideGroupLength = outSideNamber.length;
      }
      for ( let i = 0; i < outSideGroupLength; i++ ) {console.log(outSideNamber[i])
        const outSideNamberLength = outSideNamber[i].length;
        if ( outSideNamberLength > 0 ) {
          const maxOutWidth = 14;
          // 配列の真ん中から処理する
          let arrayNumber = Math.floor( ( outSideNamberLength - 1 ) / 2 );
          for ( let j = 0; j < outSideNamberLength; j++ ) {
            arrayNumber = ( ( j + 1 ) % 2 !== 0 )? arrayNumber - j: arrayNumber + j; 
            if ( outSideNamber[i][arrayNumber] !== undefined ) {
              const $pieChartText = $( document.createElementNS( xmlns, 'text') ),
                    $pieChartLine = $( document.createElementNS( xmlns, 'line') ),
                    count = Math.floor( j / 2 ),
                    position = radius + maxOutWidth;
              let textAnchor = 'middle',
                  ratioClass = outSideNamber[i][arrayNumber][0]  + ' outside',
                  angle = outSideNamber[i][arrayNumber][1],
                  ratio = outSideNamber[i][arrayNumber][2],
                  newAngle = angle,
                  lineStartPositionAngle,
                  rotetaNumber,
                  verticalPositionNumber = 0;

              // 横位置調整
              const setAngle = 16 * count + 8,
                    setLineAngle = ( Number.isInteger( ratio ) )? 4: 6;
              if ( ( j + 1 ) % 2 !== 0 ) {
                newAngle -= setAngle;
                lineStartPositionAngle = newAngle + setLineAngle;
              } else {
                newAngle += setAngle;
                lineStartPositionAngle = newAngle - setLineAngle;
              }

              if ( newAngle > 0 && newAngle < 180 ) {
                verticalPositionNumber = 4;
                rotetaNumber = newAngle + 270;
              } else {
                rotetaNumber = newAngle + 90;
              }

              const outsidePosition = setPiePosition( cxcy, cxcy, position, newAngle ),
                    x = outsidePosition[0],
                    y = outsidePosition[1],
                    lineStartPosition = setPiePosition( cxcy, cxcy, position, lineStartPositionAngle ),
                    x1 = lineStartPosition[0],
                    y1 = lineStartPosition[1],
                    lineEndPosition = setPiePosition( cxcy, cxcy, radius + strokeWidth / 2 - 2, angle ),
                    x2 = lineEndPosition[0],
                    y2 = lineEndPosition[1];

              $pieChartLine.attr({
                'x1': x1,
                'y1': y1,
                'x2': x2,
                'y2': y2,
                'class': 'pie-chart-ratio-line'
              });
              $pieChartText.html( ratio + '<tspan class="ratio-space"> </tspan><tspan class="ratio-mark">%</tspan>' ).attr({
                'x': x,
                'y': y + verticalPositionNumber,
                'text-anchor': textAnchor,
                'class': ratioClass,
                'transform': 'rotate(' + rotetaNumber + ',' + x + ',' +y + ')'
              });
              $pieChartRatioSvg.append( $pieChartText, $pieChartLine );
            }
          }
        }
      }
    }
  } else {
   // 0件の場合
    const $pieChartCircle = $( document.createElementNS( xmlns, 'circle') );
    $pieChartCircle.attr({
      'cx': cxcy,
      'cy': cxcy,
      'r': radius,
      'class': 'pie-chart-circle circle-zero'
    });
    $pieChartSvg.append( $pieChartCircle );
  }

  // テキスト
  const $pieChartTotalSvg = $( document.createElementNS( xmlns, 'svg') );
  $pieChartTotalSvg.get(0).setAttribute('viewBox', '0 0 ' + viewBox + ' ' + viewBox );
  $pieChartTotalSvg.attr('class','pie-chart-total-svg');
  
  const $pieChartName = $( document.createElementNS( xmlns, 'text') ).text( pieChartTitle ).attr({
    'class': 'pie-chart-total-name', 'x': '50%', 'y': '35%',
  });
  const $pieChartNumber = $( document.createElementNS( xmlns, 'text') ).text( totalNumber ).attr({
    'class': 'pie-chart-total-number', 'x': '50%', 'y': '50%',
  });
  const $pieChartTotal = $( document.createElementNS( xmlns, 'text') ).text('Total').attr({
    'class': 'pie-chart-total-text', 'x': '50%', 'y': '60%',
  });  
  $pieChartTotalSvg.append( $pieChartName, $pieChartNumber, $pieChartTotal );
  
  const $pieChartHTML = $('<div class="pie-chart start"><div class="pie-chart-inner"></div></div>' + tableHTML );
  $pieChartHTML.find('.pie-chart-inner').append( $pieChartTotalSvg, $pieChartSvg, $pieChartRatioSvg );
  
  $('.widget-grid[data-widget-id="' + widgetID + '"]').eq(0).find('.widget-body').html( $pieChartHTML );
  
  // 円グラフアニメーション
  $pieChartSvg.ready( function(){
    setTimeout( function() {
      const $circles = $pieChartSvg.find('.pie-chart-circle'),
            circleLength = $circles.length;
      let circleAnimationCount = 0;
      $pieChartRatioSvg.css('opacity','1');
      $circles.each( function(){
        const $circle = $( this );
        if ( $circle.attr('data-style') !== undefined ) {
          $circle.attr('style', 'stroke-dasharray:' + $circle.attr('data-style') );
        }
      }).on({
        'transitionend webkitTransitionEnd': function() {
          // 全てのアニメーションが終わったら
          circleAnimationCount++;
          if ( circleAnimationCount >= circleLength ) {
            $pieChartHTML.removeClass('start');
            $circles.on({
              'mouseenter': function(){
                const $enter = $( this ),
                      dataType = $enter.attr('data-type');
                if ( dataType !== undefined ) {
                  $enter.closest('.widget-body').find('tr[data-type="' + dataType + '"]').addClass('emphasis');
                  $enter.css('stroke-width','25');
                }
              },
              'mouseleave': function(){
                const $leave = $( this ),
                      dataType = $leave.attr('data-type');
                if ( dataType !== undefined ) {
                  $leave.css('stroke-width','20');
                  $leave.closest('.widget-body').find('.emphasis').removeClass('emphasis');
                }
              }
            });
          }        
        }
      });
      
    }, 100 );
  });
  

}

////////////////////////////////////////////////////////////////////////////////////////////////////
//
//   作業履歴 積み上げグラフ
// 
////////////////////////////////////////////////////////////////////////////////////////////////////
function workHistoryDummyData() {
return [
[[30,26,2,0,1,1],[18,16,1,0,0,1],[12,10,1,0,1,0]],
[[26,24,0,0,1,1],[15,14,0,0,1,0],[11,10,0,0,0,1]],
[[39,36,1,1,0,1],[17,16,1,0,0,0],[22,20,0,1,0,1]],
[[34,31,1,0,1,1],[17,16,0,0,0,1],[17,15,1,0,1,0]],
[[37,36,1,0,0,0],[19,19,0,0,0,0],[18,17,1,0,0,0]],
[[41,35,1,2,1,2],[18,16,0,1,0,1],[23,19,1,1,1,1]],
[[37,34,0,2,0,1],[17,16,0,1,0,0],[20,18,0,1,0,1]],
[[35,29,1,2,1,2],[16,13,0,1,1,1],[19,16,1,1,0,1]],
[[26,22,0,2,1,1],[14,12,0,1,0,1],[12,10,0,1,1,0]],
[[42,38,0,1,1,2],[22,19,0,1,1,1],[20,19,0,0,0,1]],
[[24,23,1,0,0,0],[12,11,1,0,0,0],[12,12,0,0,0,0]],
[[36,30,2,1,2,1],[18,16,1,0,1,0],[18,14,1,1,1,1]],
[[31,25,1,2,1,2],[13,10,1,1,0,1],[18,15,0,1,1,1]],
[[38,36,0,2,0,0],[21,20,0,1,0,0],[17,16,0,1,0,0]],
[[35,29,1,2,1,2],[20,17,1,1,0,1],[15,12,0,1,1,1]],
[[32,30,1,0,0,1],[12,12,0,0,0,0],[20,18,1,0,0,1]],
[[33,30,1,0,1,1],[18,16,1,0,1,0],[15,14,0,0,0,1]],
[[32,30,1,0,1,0],[20,19,0,0,1,0],[12,11,1,0,0,0]],
[[36,31,1,1,2,1],[14,11,0,1,1,1],[22,20,1,0,1,0]],
[[29,27,2,0,0,0],[13,12,1,0,0,0],[16,15,1,0,0,0]],
[[34,31,1,1,0,1],[22,20,1,1,0,0],[12,11,0,0,0,1]],
[[24,21,0,2,0,1],[12,11,0,1,0,0],[12,10,0,1,0,1]],
[[44,38,2,1,2,1],[21,18,1,0,1,1],[23,20,1,1,1,0]],
[[36,33,2,0,1,0],[19,18,1,0,0,0],[17,15,1,0,1,0]],
[[39,34,1,1,2,1],[20,16,1,1,1,1],[19,18,0,0,1,0]],
[[41,34,2,2,2,1],[19,16,1,1,1,0],[22,18,1,1,1,1]],
[[32,25,2,1,2,2],[18,15,1,0,1,1],[14,10,1,1,1,1]],
[[37,32,2,1,1,1],[22,19,1,0,1,1],[15,13,1,1,0,0]]
];
}
function workHistory( result ) {
  
const widgetID = 7,
      $target = $('.widget-grid[data-widget-id="' + widgetID + '"]').eq(0),
      resultText = [
        [ getWidgetMessage('16'),'sum'],
        [ getWidgetMessage('17'),'done'],
        [ getWidgetMessage('18'),'fail'],
        [ getWidgetMessage('19'),'error'],
        [ getWidgetMessage('20'),'stop'],
        [ getWidgetMessage('21'),'cancel']
      ],
      histryDay = new Array(),
      period = Number( $target.attr('data-period') ),
      date = new Date(),
      year = ('000' + date.getFullYear() ).slice( -4 ),
      month = ('0' + ( date.getMonth() + 1 )).slice( -2 ),
      day = ('0' + date.getDate() ).slice( -2 ),
      today = new Date( year +'-'+ month +'-'+ day );

let histryData = new Array();

// 履歴配列初期化
for ( let i = 0; i < period; i++ ) {
  // histryDay = ["年","月",日"];
  // histryData = ["合計","conductor","symphony"]
  // ["合計","正常終了","異常終了","エラー終了","緊急停止","予約取消"]
  histryData[ i ] = [[ 0, 0, 0, 0, 0, 0 ], [ 0, 0, 0, 0, 0, 0 ], [ 0, 0, 0, 0, 0, 0 ]];
  histryDay[ i ] = [ date.getFullYear(), date.getMonth() + 1, date.getDate() ];
  date.setDate( date.getDate() - 1 );
}

// 日別にカウントする
for ( let type in result ) {
  for ( let key in result[type] ) {
    const status = result[type][key]['status'],
          targetDay = new Date( result[type][key]['end'].slice( 0, 10 ) ),
          days = ( today - targetDay ) / 86400000,
          typeNumber = ( type === 'conductor')? 1:2;
          
    let resultNumber;

    if ( days < period ) {
      switch( status ) {
        case resultText[1][0]: resultNumber = 1; break;
        case resultText[2][0]: resultNumber = 2; break;
        case resultText[3][0]: resultNumber = 3; break;
        case resultText[4][0]: resultNumber = 4; break;
        case resultText[5][0]: resultNumber = 5; break;
        default: resultNumber = 0;
      }
      if ( resultNumber !== 0 ) {
        histryData[ days ][ typeNumber ][ resultNumber ] += 1;
        histryData[ days ][ typeNumber ][0] += 1;
        histryData[ days ][0][ resultNumber ] += 1;
        histryData[ days ][0][0] += 1;
      }
    }
  }
}

// ダミーデータ入れ替え
if ( gWidgetDummyFlag === true ) histryData = workHistoryDummyData();

const historyClass = ( period > 99 )? ' period-many': '';
let historyHTML = '<div class="stacked-graph'+ historyClass + '">';

// 最大値を求める
let maxNumber = 0;
const historyLength = histryData.length;
for ( let i = 0; i < historyLength; i++ ) {
  if ( histryData[i][0][0] > maxNumber ) maxNumber = histryData[i][0][0];
}

// グラフ縦軸
const digit = String( maxNumber ).length, // 桁数
      digitNumber = Math.pow( 5, digit - 1 ), 
      graphMaxNumber = Math.ceil( maxNumber / digitNumber ) * digitNumber;

historyHTML += '<ol class="stacked-graph-vertical-axis">';
const verticalAxisLength = graphMaxNumber / digitNumber;
for( let i = 0; i <= verticalAxisLength; i++ ) {
  historyHTML += '<li class="stacked-graph-vertical-axis-item">' + ( i * digitNumber ) + '</li>';
}
historyHTML += '</ol>';
  
// グラフ本体
historyHTML += '<ol class="stacked-graph-horizontal-axis">';
for ( let i = historyLength - 1; i >= 0; i-- ) {
  const sum = histryData[i][0][0],
        sumPer = Math.round( sum / graphMaxNumber * 100 ),
        donePer = Math.round( histryData[i][0][1] / sum * 100 ),
        failPer = Math.round( histryData[i][0][2] / sum * 100 ),
        errorPer = Math.round( histryData[i][0][3] / sum * 100 ),
        stopPer = Math.round( histryData[i][0][4] / sum * 100 ),
        cancelPer = Math.round( histryData[i][0][5] / sum * 100 );
  
  // 期間ごとに表示する日付を制限する
  let showDayArray = new Array,
      day = histryDay[i][2];
  if ( period >= 300 ) {
    showDayArray = [1];
  } else if ( period >= 200 ) {
    showDayArray = [1,15];
  } else if ( period >= 100 ) {
    showDayArray = [1,10,20];
  } else if ( period >= 50 ) {
    showDayArray = [1,5,10,15,20,25];
  }
  
  if ( period >= 50 ) {
    if ( showDayArray.indexOf( day ) === -1 ) {
      day = '';
    } else {
      if ( day === 1 ) {
        day = histryDay[i][1] + '/' + histryDay[i][2];
      } else {
        day = histryDay[i][2];
      }
    }
  }  
  
  historyHTML += ''
    + '<li class="stacked-graph-item">'
      + '<dl class="stacked-graph-item-inner" data-id="' + i + '">'
        + '<dt class="stacked-graph-item-title"><span class="day-number">' + day + '</span></dt>'
        + '<dd class="stacked-graph-bar">';
  if ( sum !== 0 ) {
    historyHTML += ''
          + '<ul class="stacked-graph-bar-group" data-style="' + sumPer + '%">'
            + '<li class="stacked-graph-bar-item result-done" style="height: ' + donePer + '%"></li>'
            + '<li class="stacked-graph-bar-item result-fail" style="height: ' + failPer + '%"></li>'
            + '<li class="stacked-graph-bar-item result-error" style="height: ' + errorPer + '%"></li>'
            + '<li class="stacked-graph-bar-item result-stop" style="height: ' + stopPer + '%"></li>'
            + '<li class="stacked-graph-bar-item result-cancel" style="height: ' + cancelPer + '%"></li>'
          + '</ul>';
  }
  historyHTML += ''
        + '</dd>'
      + '</dl>'
    + '</li>';
}
  historyHTML += '</ol></div></div><div class="stacked-graph-popup"></div>';
  
  $target.find('.widget-body').html( historyHTML );
  
  setTimeout( function() {
    $target.find('.stacked-graph-bar-group').each( function(){
      const $bar = $( this );
      $bar.attr('style', 'height:' + $bar.attr('data-style') );
    });
    
    const zeroCheck = function( num ) {
      return ( num === 0 )? '<span class="zero">0</span>': num;
    };
  
    // 棒グラフ詳細表示
    $('.stacked-graph-item-inner').on({
      'mouseenter': function() {
        const $bar = $( this ),
              $pop = $('.stacked-graph-popup'),
              dataID = $bar.attr('data-id'),
              resultData = histryData[dataID],
              mode = $('#dashboard').attr('data-mode');
        
        if ( mode !== 'view') return false;
        
        const conductorListURL = '/default/menu/01_browse.php?no=2100180006',
              symphonyListURL = '/default/menu/01_browse.php?no=2100000310',
              statusInputID = 'Filter1Tbl_4',
              StartDateID = 'Filter1Tbl_11__S',
              EndDataID = 'Filter1Tbl_11__E';
        
        const setResult = function(){
            // Table
            let tableHTML = ''
              + '<div class="stacked-graph-popup-close"></div>'
              + '<div class="stacked-graph-popup-date">' + histryDay[dataID][0] + '/' + histryDay[dataID][1] + '/' + histryDay[dataID][2] + '</div>'
              + '<div class="number-table-wrap"><table class="number-table"><thead>';
            // thead
            tableHTML += '<tr><th>Result</th><th>CON</th><th>SYM</th><th>SUM</th></tr></thead><tbody>';
            // tbody
            const resultTextLength = resultText.length,
                  param = '&filter=on',
                  paramDate = ''
                  + '&' + StartDateID + '=' + histryDay[dataID][0] + '/' + histryDay[dataID][1] + '/' + histryDay[dataID][2]
                  + '&' + EndDataID + '=' + histryDay[dataID][0] + '/' + histryDay[dataID][1] + '/' + ( histryDay[dataID][2] + 1 );
            for( let i = 1; i < resultTextLength; i++ ) {
              const paramTarget = '&' + statusInputID + '=' + encodeURIComponent( resultText[i][0] );
              // Status
              tableHTML += '<tr><th><span class="pie-chart-usage result-' + resultText[i][1] + '"></span>' + resultText[i][0] + '</th>';
              // Conductor
              if ( resultData[1][i] !== 0 ) {
                tableHTML += '<td><a href="' + conductorListURL + param + paramTarget + paramDate + '" target="_blank" title="' + resultText[i][0] + '">'
                + resultData[1][i] + '</a></td>';
              } else {
                tableHTML += '<td>' + zeroCheck( resultData[1][i] ) + '</td>';
              }
              // Symphony
              if ( resultData[2][i] !== 0 ) {
                tableHTML += '<td><a href="' + symphonyListURL + param + paramTarget + paramDate + '" target="_blank" title="' + resultText[i][0] + '">'
                + resultData[2][i] + '</a></td>';
              } else {
                tableHTML += '<td>' + zeroCheck( resultData[2][i] ) + '</td>';
              }
              // Sum
              tableHTML += '<td>' + zeroCheck( resultData[0][i] ) + '</td></tr>';
            }
            // 計
            tableHTML += '<tr>'
              + '<th>' + resultText[0][0] + '</th>'
              + '<td><a href="' + conductorListURL + param + paramDate + '" target="_blank" title="' + resultText[0][0] + '">' + resultData[1][0] + '</a></td>'
              + '<td><a href="' + symphonyListURL + param + paramDate + '" target="_blank" title="' + resultText[0][0] + '">' + resultData[2][0] + '</a></td>'
              + '<td>' + zeroCheck( resultData[0][0] ) + '</td>'
              + '</tr>';
            
            tableHTML += '</tbody></table></div>';
            $pop.html( tableHTML );
            $pop.find('.stacked-graph-popup-close').on('click', function(){
              $pop.removeClass('fixed').html('').hide();
            });
        };
        
        const setPopPosition = function( pageX, pageY ) {
            const $window = $( window ),
                  scrollTop = $window.scrollTop(),
                  scrollLeft = $window.scrollLeft(),
                  windowWidth = $window.width(),
                  popupWidth = $pop.outerWidth();

            let leftPosition = pageX - scrollLeft;

            // 右側チェック
            if ( leftPosition + ( popupWidth / 2 ) > windowWidth ) {
              leftPosition = leftPosition - (( leftPosition + ( popupWidth / 2 ) ) - windowWidth );
            }
            // 左側チェック
            if ( leftPosition - ( popupWidth / 2 ) < 0 ) {
              leftPosition = popupWidth / 2;
            }

            $pop.show().css({
              'left': leftPosition,
              'top': pageY - scrollTop - 16
            });
        };
        
        $bar.on('click.stackedGraphPopup', function( e ){
          if ( $pop.is('.fixed') ) {
            setPopPosition( e.pageX, e.pageY );
            setResult();
          }
          $pop.toggleClass('fixed');
        });
        
        $( window ).on('mousemove.stackedGraphPopup', function( e ) {
          if ( !$pop.is('.fixed') ) {
            setPopPosition( e.pageX, e.pageY );
            setResult();
          }
        });
      },
      'mouseleave': function() {
        $('.stacked-graph-popup').not('.fixed').html('').hide();
        $( this ).off('click.stackedGraphPopup');
        $( window ).off('mousemove.stackedGraphPopup');
      }
    });
  }, 100 );
}