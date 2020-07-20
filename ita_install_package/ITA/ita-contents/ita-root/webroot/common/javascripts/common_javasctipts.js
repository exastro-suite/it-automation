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

});








