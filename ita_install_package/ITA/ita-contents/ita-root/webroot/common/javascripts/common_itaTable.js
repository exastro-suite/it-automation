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

function itaTable( tableID ){

//////////////////////////////////////////////////
//
//   初期設定
//
var $itaTable = $( '#' + tableID );

// パラメータと親fakeContainerから固有key作成
var getParam = function( name ) { 
  var url = window.location.href,
      regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
  var results = regex.exec( url );
  if( !results ) return null;
  return decodeURIComponent( results[2] );
}
var pageNumber = getParam('no');
pageNumber = pageNumber !== null ? pageNumber : '9999999999';

var fakeContainerClass = $itaTable.closest('div[class*="fakeContainer_"]').attr('class').split(' '),
    fakeContainer = 'table';
for( var j = 0; j < fakeContainerClass.length; j++ ) {
  if( fakeContainerClass[j].indexOf('fakeContainer_') != -1 ) {
    fakeContainer = fakeContainerClass[j].replace('fakeContainer_', '');
    break;
  }
}

// 状態保存用の固有Key
var tableKey = 'No' + pageNumber + '_' + fakeContainer + '_' + tableID;

var tHeadClass = '.defaultExplainRow'; // テーブル見出しclass
var rowHeadClass = '.likeHeader'; // 行見出しclass

// ID名
var itaTableWrapID = tableID + '_itaTable'; // テーブルラップID
var itaTableBodyID = tableID + '_itaTableBody'; // テーブルラップID
var itaTableFooterID = tableID + '_itaTableFooter'; // テーブルラップID

var tableSettingID = tableID + '_tableSetting'; // テーブル設定id

var tableSettingOpenID = tableID + '_tableSettingOpen'; // 列リストstyle id
var fixedRowHeadStyleID = tableID + '_fixedRowHeadStyle'; // 行見出し固定style id

var colHideStyleID = tableID + '_colHideStyle'; // 列非表示style id
var fixedTopBorderID = tableID + '_fixedTopBorder'; // 固定線Left id
var fixedLeftBorderID = tableID + '_fixedLeftBorder'; // 固定線Left id
var fixedRightBorderID = tableID + '_fixedRightBorder'; // 固定線Right id

// IE,Edge判定
var userAgent = window.navigator.userAgent.toLowerCase();

if( userAgent.indexOf('msie') != -1 || userAgent.indexOf('trident') != -1 ) {
  userAgent = 'ie';
} else if( userAgent.indexOf('edge') != -1 ) {
  userAgent = 'edge';
} else {
  userAgent = 'def';
}

// Tableを囲む
$itaTable.wrap('<div id="' + itaTableWrapID + '" class="itaTable ' + userAgent +'"><div id="' + itaTableBodyID + '" class="itaTableBody"><div class="tableScroll"></div></div></div>');

// Tableフッターと固定線の追加
var tableFooterHTML = ''
    + '<div id="' + itaTableFooterID + '" class="itaTableFooter">'
    + '<div class="itaTableFooterMenu"><div class="itaTableFooterMenuInner">'
      + '<ul>'
        + '<li><button id="' + tableSettingOpenID + '">Table setting<span style="display:none"></span></button></li>'
      + '</li>'
    + '</div></div>'
    + '<style id="' + fixedRowHeadStyleID + '"></style>'
    + '<style id="' + colHideStyleID + '"></style>'
    + '</div>'
    + '<div id="' + fixedTopBorderID + '" class="fixedBorder top"></div>'
    + '<div id="' + fixedLeftBorderID + '" class="fixedBorder left"></div>'
    + '<div id="' + fixedRightBorderID + '" class="fixedBorder right"></div>';

// Table設定要素の追加
var tableSettingHTML = ''
    + '<div id="' + tableSettingID + '" class="itaTableSetting"><div class="itaTableSettingInner">'
      + '<div class="tableSettingBody"></div>'
      + '<div class="tableSettingFooter">'
        + '<ul>'
          + '<li><button class="apply">Apply</button></li>'
          + '<li><button class="close">Close</button></li>'
          + '<li><button class="reset">Reset</button></li>'
        + '</ul>'
      + '</div>'
    + '</div></div>';

var $itaTableWrap = $('#' + itaTableWrapID ),
    $itaTableBody = $('#' + itaTableBodyID );
    
$itaTableBody.append( tableFooterHTML );
$itaTableWrap.append( tableSettingHTML );

var $tableSetting = $('#' + tableSettingID ),
    $tableSettingOpen = $('#' + tableSettingOpenID ),
    $fixedRowHeadStyle = $('#' + fixedRowHeadStyleID ),
    $colHideStyle = $('#' + colHideStyleID ),
    $fixedTopBorder = $('#' + fixedTopBorderID ),
    $fixedLeftBorder = $('#' + fixedLeftBorderID ),
    $fixedRightBorder = $('#' + fixedRightBorderID );

//////////////////////////////////////////////////
//
//   ログ出力用
//
var logFlag = false;
var log = function() {
    if ( logFlag === true ) {
      for ( var i = 0; i < arguments.length; i++ ) {
        window.console.log( JSON.stringify( arguments[i] ) );
      }
    }
}

//////////////////////////////////////////////////
//
//   WebStorageが使えるかチェックする
//
var storageAvailable = function( type ) {
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
var sessionStorageFlag = storageAvailable('sessionStorage'),
    localStrageFlag = storageAvailable('localStorage');

//////////////////////////////////////////////////
//
//   position:sticky;が使えるかチェックする
//
var positionStickyFlag = false,
    $stickyCheck = $('<div/>').css('position','sticky');
if( $stickyCheck.css('position') === 'sticky' ) positionStickyFlag = true;

//////////////////////////////////////////////////
//
//   チェック結果ログ出力
//
log(
'Table Key / ' + tableKey,
'sessionStorage / ' + sessionStorageFlag,
'localStorage / ' + localStrageFlag,
'position:sticky; / ' + positionStickyFlag
);

//////////////////////////////////////////////////
//
//   見出しを固定する（ "position:sticky;" を追加）
//
var fixedTableHeadCounter = 0; // Tableヘッダー行数
var fixedLeftRowHeadCounter = 0; // 行左側の見出し数
var fixedRightRowHeadCounter = 0; // 行右側の見出し数
var fixedBorderTopPosition = 0; // 上側の固定線の位置
var fixedBorderLeftPosition = 0; // 左側の固定線の位置
var fixedBorderRightPosition = 0; // 右側の固定線の位置

var fixedSize = 1,
    rowSpanHeight = 0,
    fixedStyleHTML = '';

// テーブルの見出しの数を調べStyleを作成する
$itaTable.find( tHeadClass ).each( function(){
    
    var trHeight = $( this ).outerHeight();
    
    fixedTableHeadCounter++;
    rowSpanHeight += trHeight;
    fixedStyleHTML += ''
      + '#' + tableID + ' ' + tHeadClass + ':nth-child(' + fixedTableHeadCounter + ') th {'
        + 'top:' + fixedSize + 'px;'
      + '}'
      + '#' + tableID + ' ' + tHeadClass + ' th[data-rowspan="' + fixedTableHeadCounter + '"] {'
        + 'height:' + rowSpanHeight + 'px;'
      + '}';
    fixedSize += trHeight + 1;
    rowSpanHeight++;
});
fixedBorderTopPosition = fixedSize - 1;

// 行左側の見出しの数を調べStyleを作成する
fixedSize = 1;
$itaTable.find('tr').not( tHeadClass ).eq( 0 ).find('td').each( function( i ){
    if( $( this ).is( rowHeadClass ) ) {
      fixedLeftRowHeadCounter++;
      fixedStyleHTML += ''
        + '#' + tableID + ' td:nth-child(' + fixedLeftRowHeadCounter + '){'
          + 'left:' + fixedSize + 'px;'
        + '}'
        + '#' + tableID + ' tr' + tHeadClass + ':first-child th:nth-child(' + fixedLeftRowHeadCounter + '){'
          + 'left:' + fixedSize + 'px;'
        + '}';
      $itaTable.find( tHeadClass ).eq( 0 ).find('th').eq( i ).addClass('thSticky left');
      fixedSize += $( this ).outerWidth() + 1;
    } else {
      fixedBorderLeftPosition = fixedSize - 1;
      return false;
    }
});

// 行右側の見出しの数を調べStyleを作成する
fixedSize = 1;
$( $itaTable.find('tr').not( tHeadClass ).eq( 0 ).find('td').get().reverse() ).each( function( i ){
    if( $( this ).is( rowHeadClass ) ) {
      fixedRightRowHeadCounter++;
      fixedStyleHTML += ''
        + '#' + tableID + ' td:nth-last-child(' + fixedRightRowHeadCounter + '){'
          + 'right:' + fixedSize + 'px;'
        + '}'
        + '#' + tableID + ' tr' + tHeadClass + ':first-child th:nth-last-child(' + fixedRightRowHeadCounter + '){'
          + 'right:' + fixedSize + 'px;'
        + '}';
      $( $itaTable.find( tHeadClass ).eq( 0 ).find('th').get().reverse() ).eq( i ).addClass('thSticky right');
      fixedSize += $( this ).outerWidth() + 1;
    } else {
      fixedBorderRightPosition = fixedSize - 1;
      return false;
    }
});


var $tableScroll = $itaTableBody.find('.tableScroll');

// 固定線位置更新
var fixedBorderUpdate = function(){

  $itaTableBody.css('width', 'auto');
  
  var tableScrollElement = $tableScroll.get(0);
  var scrollWidth = tableScrollElement.offsetWidth - tableScrollElement.clientWidth; // スクロールバーのサイズ
  var tableScrollHeight = tableScrollElement.clientHeight; // Table表示部分の高さ
  
  $fixedTopBorder.css({
    'top' : fixedBorderTopPosition,
    'width' : 'calc( 100% - ' + scrollWidth + 'px )'
  });
  $fixedLeftBorder.css({ 
    'left' : fixedBorderLeftPosition,
    'height' : tableScrollHeight
  });
  $fixedRightBorder.css({
    'right' : fixedBorderRightPosition + scrollWidth,
    'height' : tableScrollHeight
  });
  $itaTableBody.css('width', $itaTable.outerWidth() + scrollWidth );
  
}
fixedBorderUpdate();

// スクロールチェック
var scrollCheck = function( $scroll ){
    var clientWidth = $scroll.get(0).clientWidth,
        tableWidth = $scroll.find('table').outerWidth(),
        scrollLeft = $scroll.scrollLeft();
    // 左
    if( $scroll.scrollLeft() > 0 ) {
      $itaTableBody.addClass('scrollLeft');
    } else {
      $itaTableBody.removeClass('scrollLeft');
    }
    // 右
    if( ( clientWidth + scrollLeft >= tableWidth ) || ( clientWidth == tableWidth ) ) {
      $itaTableBody.removeClass('scrollRight');
    } else {
      $itaTableBody.addClass('scrollRight');
    }
}
scrollCheck( $tableScroll );

$tableScroll.on('scroll', function(){
    scrollCheck( $( this ) );
});

// class,style追加
if( positionStickyFlag === true ) {
    $itaTableWrap.addClass('tableSticky');
    $fixedRowHeadStyle.html( fixedStyleHTML );
}



//////////////////////////////////////////////////
//
//   列の表示・非表示
//

var colNumberID = 0; // セルに連番でID付与する
var dataMaxLevel = fixedTableHeadCounter;
var headerListHTML = '';

var $line1 = $itaTable.find('tr').eq( 0 );

// 列数を取得
var colCount = 0;
$line1.find('th').each(function(){
  colCount += $( this ).attr('colspan') !== undefined ? Number( $( this ).attr('colspan') ) : 1;
});

// セル情報取得
var thDataFunc = function( $th ) {
    var titleText = $th.text(),
        requiredFlg = false,
        colspanNum = $th.attr('colspan') !== undefined ? Number( $th.attr('colspan') ) : 1,
        rowspanNum = $th.attr('rowspan') !== undefined ? Number( $th.attr('rowspan') ) : 1;
  
    if( $th.find('.input_required').length ) requiredFlg = true;
    
    return [ titleText, colspanNum, rowspanNum, requiredFlg ];
}

// テーブルからリスト作成
var trDataFunc = function( $tr, start, end, level, parentStat, parentID ) {

    dataMaxLevel = ( dataMaxLevel < level ) ? level : dataMaxLevel;
    headerListHTML += '\n<ul class="level' + level + '" data-level="' + level + '">\n';

    var colNumber = parentStat,
        nextStart = 0;

    $tr.find('th').slice( start, end ).each( function(){

      var $th = $( this );
      
      var data = thDataFunc( $th ),
          nextRowCount = $tr.nextAll('tr.defaultExplainRow').length;

      var dataColStart = colNumber,
          dataColEnd = colNumber + data[1] - 1,
          inputID = 'col' + colNumberID,
          listClass = 'level' + level,
          inputStatus = 'checked';
      
      // 固定部分は非表示にする
      if( $th.is('.thSticky') ) listClass += ' disabled';
      // 固定部分および必須はDisabled
      if( $th.is('.thSticky') || data[3] === true ) inputStatus += ' disabled';
      
      $th.attr('data-col-id', tableID + '_col' + colNumberID );
      colNumberID++;

      headerListHTML += ''
      + '<li class="' + listClass + '">'
        + '<span>'
          + '<input type="checkbox" id="' + tableID + '_' + inputID + '" class="" data-parent-id="' + tableID + '_' + parentID + '" data-colspan="' + data[1] + '" data-level="' + level + '" data-col-start="' + dataColStart + '" data-col-end="' + dataColEnd + '" ' + inputStatus + '>'
      + '<label for="' + tableID + '_' + inputID + '">' + data[0] + '</label>';
      
      // 次の行があるか？
      if ( ( data[1] > 1 || data[2] <= nextRowCount ) && nextRowCount > 0 ) {
        
        // 開閉ボタン追加
        headerListHTML += '<button class="open">+</button></span>';
        
        // 次の行の列数を調べる
        var nextRowCounter = 0,
            nextRowSum = 0;

        $tr.next('tr.defaultExplainRow').find('th').slice( nextStart, nextStart + data[1] ).each( function(){
          var nextTh = thDataFunc( $( this ) );
          nextRowSum += nextTh[1];
          nextRowCounter++;
          if( nextRowSum >= data[1] ) return false;
        });

        trDataFunc( $tr.next(), nextStart, nextStart + nextRowCounter, level + 1, dataColStart, inputID );
        nextStart += nextRowCounter;
      } else {
        headerListHTML += '</span>';
      }
      headerListHTML += '</li>\n';
      colNumber += data[1];

    });

    headerListHTML += '</ul>\n';

}
trDataFunc( $line1, 0, colCount, 1, 1, 'none');

// 設定用 HTML
var itaTableSettingBodyHTML = '';

// Stickyが使える場合は専用の項目を追加
if( positionStickyFlag === true ) {
itaTableSettingBodyHTML += ''
+ '<dl class="headingFixed">'
  + '<dt>'
    + 'Heading Fixed'
  + '</dt>'
  + '<dd>'
    + '<ul class="level1">'
      + '<li class="level1"><span><input type="checkbox" id="' + tableID + '_fixedHeadingTop" class="" data-fixed-type="top" checked><label for="' + tableID + '_fixedHeadingTop">Top Heading Fixed</label></span></li>'
      + '<li class="level1"><span><input type="checkbox" id="' + tableID + '_fixedHeadingLeft" class="" data-fixed-type="left" checked><label for="' + tableID + '_fixedHeadingLeft">Left Heading Fixed</label></span></li>'
      + '<li class="level1"><span><input type="checkbox" id="' + tableID + '_fixedHeadingRight" class="" data-fixed-type="right" checked><label for="' + tableID + '_fixedHeadingRight">Right Heading Fixed</label></span></li>'
    + '</ul>'
  + '</dd>'
+ '</dl>';
}

// 取得したリストを追加
itaTableSettingBodyHTML += ''
+ '<dl class="colCheckList">'
  + '<dt>'
    + 'Show or Hide'
  + '</dt>'
  + '<dd>'
    + headerListHTML
  + '</dd>'
+ '</dl>';
var $itaTableSettingBodyHTML = $( itaTableSettingBodyHTML );
$tableSetting.find('.tableSettingBody').html( itaTableSettingBodyHTML );
// 追加されるのを待ち、幅を取得する
var tableSettingWidth = 0;
$itaTableSettingBodyHTML.ready(function(){
    tableSettingWidth = $tableSetting.outerWidth();
    $tableSetting.css('width', tableSettingWidth ).find('ul.level1 ul').hide();
});

// チェックボックス選択
$tableSetting.find('input[type="checkbox"]').on('change', function(){

    var $input = $( this );

    // チェックが変わった時兄弟要素を確認し親の状態を変える
    var checkSiblingsInput = function( $checkInput ){
    
      // 親要素が無ければ終了する
      var $parentInput = $checkInput.closest('ul').siblings('span').find('input');
      if ( $parentInput.length == 0 ) return false;
      
      // 自分も含めた兄弟要素
      var $siblingInput = $checkInput.add( $checkInput.closest('li').siblings('li').children('span').find('input') );
      
      // inputの数
      var inputCount = $siblingInput.length,
          inputCheckCount = $siblingInput.filter(':checked').length,
          inputNoCheckCount = $siblingInput.filter('.noCheck').length;log(inputNoCheckCount);
                    
      // 兄弟全てチェックが外れていれば親のチェックも外す
      if ( inputCheckCount == 0 ) {
        $parentInput.prop('checked', false );
      } else {
        $parentInput.prop('checked', true );
      }

      // 兄弟に1つでも未チェックがあれば親のチェックの色を変える
      if ( inputCount - inputCheckCount > 0 || inputNoCheckCount > 0 ) {
        $parentInput.addClass('noCheck');
      } else {
        $parentInput.removeClass('noCheck');
      }
      
      // 再帰
      checkSiblingsInput( $parentInput );
    }

    if( $input.prop('checked') ) {
      // 子要素すべてをチェックする
      $input.removeClass('noCheck').closest('span').next('ul').find('input').not(':disabled').removeClass('noCheck').prop('checked', true );
      checkSiblingsInput( $input );
    } else {
			// 子要素に未チェックがあればすべてチェックにする
			if( $input.is('.noCheck') ) {
				$input.removeClass('noCheck').prop('checked', true )
					.closest('span').next('ul').find('input').not(':disabled').removeClass('noCheck').prop('checked', true );
			} else {
				// 子要素すべてのチェックを外す
				$input.closest('span').next('ul').find('input').not(':disabled').prop('checked', false );
				// 子にinput:disabledがあればチェックしなおす。
				if ( $input.closest('span').next('ul').find('input:disabled').length ) $input.addClass('noCheck').prop('checked', true );
			}
			checkSiblingsInput( $input );
    }
    
    // Edgeで色の状態が再描画されないための応急処置
    if( userAgent == 'edge' ) {
      $tableSetting.find('.tableSettingBody').hide().show();
    }

});

$tableSettingOpen.on('click', function(){

    $itaTableBody.css('max-width', 'calc( 100% - ' + tableSettingWidth + 'px )');
    $itaTableWrap.addClass('tableSettingOpen');
    setTimeout( function(){
      fixedBorderUpdate();
      scrollCheck( $tableScroll );
    }, 350 );

});
// Table Setting ボタン
$tableSetting.find('button').on('click', function(){
  
  var $button = $( this ),
      buttonType = $button.attr('class').split(' ');

  if( buttonType.indexOf('reset') >= 0 ){
    $tableSetting.find('input').removeClass('noCheck').prop('checked', true );
  }

  if( buttonType.indexOf('close') >= 0 ){
    $itaTableWrap.removeClass('tableSettingOpen');
    $itaTableBody.css('max-width', '100%');
    fixedBorderUpdate();
    scrollCheck( $tableScroll );
  }
  
  // 適用ボタン
  if( buttonType.indexOf('apply') >= 0 ){
    // 表示する列が無い場合はアラートを出す
    if ( $tableSetting.find('.colCheckList input[type="checkbox"]:checked').length ) {
      tableHideFucn( dataMaxLevel );
      saveCheckStatus( tableKey );
    } else {
      alert('No columns to display.')
    }
  }

  if( buttonType.indexOf('open') >= 0 ){
    if( $button.is('.on') ) {
      $button.closest('span').next('ul').hide();
      $button.text('+').removeClass('on');
    } else {
      $button.closest('span').next('ul').show();
      $button.text('-').addClass('on');
    }
  }
  
});

// 見出しを固定するかチェック
var headingFixed = function() {
    $tableSetting.find('.headingFixed input').each( function(){
      var fixedType = $( this ).attr('data-fixed-type');
      if( $( this ).prop('checked') ) {
        $itaTableWrap.removeClass('noFixed_' + fixedType );
      } else {
        $itaTableWrap.addClass('noFixed_' + fixedType );
      }
    });
}

// セルの表示・非表示用配列
var tablebodyDisplayFlagArray = new Array( colCount );
for ( var i = 0; i < tablebodyDisplayFlagArray.length; i++ ) {
  tablebodyDisplayFlagArray[ i ] = true;
}

// rowspan調整
var rowspanInit = function() {
    $itaTable.find( tHeadClass ).find('th').each( function(){
      // デフォルトのrowspanをdata-rowspanに入れる
      $( this ).attr('data-rowspan', $( this ).attr('rowspan') );
    });
}
rowspanInit();

var rowspanAdjustment = function() {
    // 空行をカウントする
    var trHiddenCount = 0;
    $itaTable.find( tHeadClass ).each( function(){
      var $headerTr = $( this );
      if( $headerTr.find('th').not('.tableSettingHidden').length == 0 ) {
         $headerTr.addClass('tableSettingHidden');
         trHiddenCount++;
      }
    });
    // 空行の数を元にrowspanを調整
    $itaTable.find( tHeadClass ).find('th').each( function(){
      var defRowspan = Number( $( this ).attr('data-rowspan') );
      var rowspan = ( defRowspan - trHiddenCount ) <= 0 ? 1 : defRowspan - trHiddenCount ;
      $( this ).attr('rowspan', rowspan );
    });
}

// チェックリストを元にセルを表示・非表示
var tableHideFucn = function( listLevel ) {
  $itaTable.find( tHeadClass ).removeClass('tableSettingHidden');
  $tableSetting.find('.colCheckList ul[data-level="' + listLevel + '"]').each( function(){
    $( this ).children('li').each( function(){

      var $input = $( this ).children('span').find('input'),
          $targetTh = $('th[data-col-id="' + $input.attr('id')+ '"]'),
          $parentTh = $('th[data-col-id="' + $input.attr('data-parent-id') + '"]');

      var startCol = Number( $input.attr('data-col-start') ) - 1,
          endCol =  Number( $input.attr('data-col-end') ) - 1;

      // colspan処理
      var colspanAdjust = function( $colspanAdjustInput, $colspanAdjustTarget, $colspanAdjustParent, changeNumber, colspanAdjustFlag ) {

          var parentID = $colspanAdjustInput.attr('data-parent-id'),
              $parentInput = $('#' + parentID );

          // 親要素が無ければ終了
          if ( $colspanAdjustInput.attr('data-parent-id') == tableID + '_none') return false;

          var parentThColspan = Number( ( $colspanAdjustParent.attr('colspan') !== undefined ) ? $colspanAdjustParent.attr('colspan') : 1 );
          if ( colspanAdjustFlag == '-') {
            $colspanAdjustParent.attr('colspan', parentThColspan - changeNumber );
          } else if ( colspanAdjustFlag == '+') {
            $colspanAdjustParent.removeClass('tableSettingHidden').attr('colspan', parentThColspan + changeNumber );
          }

          // 再帰
          colspanAdjust( $parentInput, $colspanAdjustParent, $('th[data-col-id="' + $parentInput.attr('data-parent-id') + '"]'), changeNumber, colspanAdjustFlag );
      }

      var targetThColspan = Number( ( $targetTh.attr('colspan') !== undefined ) ? $targetTh.attr('colspan') : 1 );

      if( !$input.prop('checked') ) {
        if( !$targetTh.is('.tableSettingHidden') ) {
          colspanAdjust( $input, $targetTh, $parentTh, targetThColspan, '-');
          for ( var i = startCol; i <= endCol; i++ ) {
            tablebodyDisplayFlagArray[ i ] = false;
          }
          $targetTh.addClass('tableSettingHidden');
        }
      } else {
        if( $targetTh.is('.tableSettingHidden') ) {
          colspanAdjust( $input, $targetTh, $parentTh, targetThColspan, '+');
          for ( var j = startCol; j <= endCol; j++ ) {
            tablebodyDisplayFlagArray[ j ] = true;
          }
          $targetTh.removeClass('tableSettingHidden');
        }
      }
    });
  });
  
  // 子列があれば再帰、なければ非表示処理
  if( listLevel > 1 ) {
    tableHideFucn( listLevel - 1 );
  } else {
    
    var hideCount = 0;
    var styleCode = '';
    for ( var i = 0; i <= tablebodyDisplayFlagArray.length; i++ ) {
      if ( tablebodyDisplayFlagArray[ i ] === false ) {
        styleCode += '#' + tableID + ' td:nth-child(' + ( i + 1 ) + '){ display: none; }';
        hideCount++;
      }
    }
    $colHideStyle.html( styleCode );
    
    // 非表示列数を表示
    $tableSettingOpen.find('span').text( hideCount );
    if ( hideCount > 0 ) {
			var hiddenTitle = ' columns hidden.';
			if( hideCount == 1 ) hiddenTitle = ' column hidden.';
      $tableSettingOpen.find('span').attr('title', hideCount + hiddenTitle ).show();
    } else {
      $tableSettingOpen.find('span').attr('title', '').hide();
    }
    
    // 各種調整
		setTimeout( function(){
			rowspanAdjustment();
			fixedBorderUpdate();
			scrollCheck( $tableScroll );
			headingFixed();
		}, 1 );
    
  }
}



//////////////////////////////////////////////////
//
//   状態の保存・読み込み
//

// Local Storageに値をセットする
var setLocalStorage = function( key, value ) {
    if( localStrageFlag ) {
      localStorage.setItem( key, JSON.stringify( value ) );
    } else {
      return false;
    }
}

// Local Storageから値をゲットする
var getLocalStorage = function( key ) {
    if( localStrageFlag && localStorage.getItem( key ) !== null ) {
      return JSON.parse( localStorage.getItem( key ) );
    } else {
      return false;
    }
}

// チェックリストを保存する
var saveCheckStatus = function( key ) {
    var checkStatusArray = [];    
    // 簡易チェック用
    checkStatusArray[0] = [ colNumberID, colCount ];
    $tableSetting.find('input').each( function( i ){
      var $this = $( this );
      checkStatusArray[ i + 1 ] = [ $this.attr('id'), $this.attr('class'), $this.prop('checked') ];
    });
    log('Local Storage Set. Key[' + key + '].');
    setLocalStorage( key, checkStatusArray );
}

// チェックリストを読み込んで適用する
var loadCheckStatus = function( key ) {
    var checkList = getLocalStorage( key );
    if( checkList !== false ) {
      // 簡易チェック
      if( checkList[0][0] !== colNumberID && checkList[0][1] !== colCount  ) {
        log('Local Storage Get. Key[' + key + ']. error.');
        return false;
      }
      // checkboxの更新
      for( var i = 1; i < checkList.length; i++ ) {
        $('#' + checkList[i][0] ).addClass( checkList[i][1] ).prop('checked', checkList[i][2] );
      }
      log('Local Storage Get. Key[' + key + '].');
      tableHideFucn( dataMaxLevel );
      headingFixed();
    } else {
      log('Local Storage Key[' + key + '] Not found.');
    }
}
loadCheckStatus( tableKey );



//////////////////////////////////////////////////
//
//   その他
//

// テーブル内のselectが変更された時にテーブルのサイズを更新する
$itaTable.find('select').on('change', function(){
    setTimeout( function(){
      fixedBorderUpdate();
      scrollCheck( $tableScroll );
    }, 1 );
});

// Windowサイズが変更された時にテーブルのサイズを更新する
var resizeTimer = false;    
    $( window ).on('resize', function() {
      if ( resizeTimer !== false ) {
        clearTimeout( resizeTimer );
      }
      resizeTimer = setTimeout( function(){
        fixedBorderUpdate();
        scrollCheck( $tableScroll );
    }, 100 );
});

// Edge対策Tableを再描画する
if ( userAgent == 'edge' ){
    $itaTable.hide();
    setTimeout( function(){
      $itaTable.show();
    }, 10 );
}

}