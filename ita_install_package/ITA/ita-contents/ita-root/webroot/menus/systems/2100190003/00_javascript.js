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
    contrastResultHtml : function(result){
        var strMixOuterFrameName = 'Filter1_Nakami';

        var filterAreaWrap = 'Filter1_Nakami';
        var printAreaWrap = 'table_area';
        var printAreaHead = 'Filter1_Midashi';

         var strMixInnerFramePrefix = 'Mix1_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        var objPrintArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

        if( ary_result[0] == "000" ){
            if( checkOpenNow(printAreaWrap)===false ){
                show(printAreaHead, printAreaWrap);
            }
            objPrintArea.innerHTML = resultContentTag;

            adjustTableAuto (strMixInnerFramePrefix+'1',
                            "sDefault",
                            "fakeContainer_Filter1Print",
                            webStdTableHeight,
                            webStdTableWidth );
            adjustTableAuto (strMixInnerFramePrefix+'2',
                            "sDefault",
                            "fakeContainer_ND_Filter1Sub",
                            webStdTableHeight,
                            webStdTableWidth );

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
            objPrintArea.innerHTML = "";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);

        $(function() {
          var objResultArea = $('#Mix1_1');
          $("#Mix1_1_itaTableBody").css('width', objResultArea.innerWidth() );
        });
    },
    printHostList : function(result){
        var strMixOuterFrameName = 'Filter1_Nakami';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resulthostlist = ary_result[2];
        
        var inputDataValue = 'ins-host-id="host_data"';

        const modal = new itaEditorFunctions ;
        if( ary_result[0] == "000" ){
              if ( resulthostlist !== '') {

                const modalHostList = function() {

                  
                  const roleList = JSON.parse( resulthostlist ),
                        $input = $('input[' + inputDataValue + ']');
                  if ( $input.length ) {
                      const initValue = $input.val();
                      
                      // 決定時の処理    
                      const okEvent = function( newRoleList ) {
                        $input.val( newRoleList );
                        
                        const $target = $input.closest('.tdInner'),
                              inputHTML = newRoleList + $input.prop('outerHTML');
                        $target.html( inputHTML ).trigger('roleChange');
                        
                        //checkOverfrowText( $target );
                        modal.modalClose();
                      };
                      // キャンセル時の処理    
                      const cancelEvent = function( newRoleList ) {
                        modal.modalClose();
                      };
                      setHostSelectModalBody( roleList, initValue, okEvent, cancelEvent, 'name');              
                  }
                };
                modal.modalOpen('Host select', modalHostList,'host-selet-modal');
              } else {
                  modal.modalError('Failed to get the list.');
              }
              
        }else if( ary_result[0] == "002" ){
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    }
}

//////// ----汎用系ファンクション ////////
function setInputButtonDisable(rangeId,targetClass,toValue){
    if(toValue === true){
        $('#'+rangeId+' .'+targetClass).attr("disabled",true);
    }else{
        $('#'+rangeId+' .'+targetClass).removeAttr("disabled");
    }
}
//////// 汎用系ファンクション---- ////////

//////// テーブルレイアウト設定 ////////
var msgTmpl = {};
//////// 画面生成時に初回実行する処理 ////////

var webPrintRowLimit;
var webPrintRowConfirm;

var webStdTableWidth;
var webStdTableHeight;

var varInitedFlag1;
var varInitedFlag2;
var initialFilterEl;
var initialFilter;

var proxy = new Db_Access(new callback());

window.onload = function(){
    initialFilterEl = document.getElementById('sysInitialFilter');
    if(initialFilterEl == null){
        initialFilter = 2;
    }
    else{
        initialFilter = initialFilterEl.innerHTML;
    }
    //initProcess('instanceConstructWithDR');
    show('SetsumeiMidashi','SetsumeiNakami');
    
    $('[name="CONTRAST_ID"]').select2({
        dropdownAutoWidth: true,
        width: '300'
    })

}


//////// 日時ピッカー表示用ファンクション ////////
$(function(){
    setDatetimepicker('bookdatetime');
    setDatetimepicker('bookdatetime2');
});

//---- ここからカスタマイズした場合の一般メソッド配置域
// ここまでカスタマイズした場合の一般メソッド配置域----

$(function() {
  var objResultArea = $('#Mix1_1');
  $("#Mix1_1_itaTableBody").css('width', objResultArea.innerWidth() );
});


//////////////////////////////////////////////////////
// 比較結果出力
//////////////////////////////////////////////////////
//比較実行
function contrastResultHtml(){
    //window.confirm( "---" );
    var filterAreaWrap = 'Filter1_Nakami';
    var printAreaWrap = 'table_area';
    var printAreaHead = 'Filter1_Midashi';

    var strMixOuterFrameName = 'Filter1_Nakami';
    var objTableArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

    // テーブル表示用領域を開く
    if( checkOpenNow(printAreaWrap) === false ){
        show(printAreaHead, printAreaWrap);
    }

    var intContrastid = $('[name="CONTRAST_ID"]').val();
    var strBaseTime0  = $('[name="BASE_TIMESTAMP_0"]').val();
    var strBaseTime1  = $('[name="BASE_TIMESTAMP_1"]').val();
    var arrhostlist   = $('[name="HOST_LIST[]"]').val();
    var strhostlist   = arrhostlist;
    
    var outputType  = $('[name="OUTPUT_TYPE"]:checked').val();

   if ( Array.isArray(arrhostlist) === true ) {
        var strhostlist = arrhostlist.join(',');
    }

    objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
    restruct_for_IE();

    proxy.contrastResultHtml( intContrastid , strBaseTime0 , strBaseTime1 , strhostlist , outputType );

}
//////////////////////////////////////////////////////
// ホスト一覧取得・選択
//////////////////////////////////////////////////////
//ホスト一覧取得
function printHostList(){
    var intContrastid = $('[name="CONTRAST_ID"]').val();
    proxy.printHostList( intContrastid );
}

// モーダル Body HTML
function setHostSelectModalBody( roleList, initData, okCallback, cancelCallBack, valueType ) {

      if ( valueType === undefined ) valueType = 'id';
      const $modalBody = $('.editor-modal-body');

      let roleSelectHTML = ''
      + '<div class="modal-table-wrap">'
        + '<form id="modal-role-select">'
        + '<table class="modal-table modal-select-table">'
          + '<thead>'
            + '<th class="select">Select</th><th class="id">ID</th><th class="name">Name</th>'
          + '</thead>'
          + '<tbody>';

      // 入力値を取得する
      const checkList = ( initData !== null || initData !== undefined )? initData.split(','): [''];

      const roleLength = roleList.length;
      for ( let i = 0; i < roleLength; i++ ) {
        const roleName = roleList[i]['HOSTNAME'],
              hideRoleName = getSomeMessage("ITAWDCC92008");
        
        var strhostlist = $('input:hidden[name="HOST_LIST[]"]').val();
        var arrhostlist = strhostlist.split(',');
        var tmproleID = roleList[i]['SYSTEM_ID'];
        
        if ( roleName !== hideRoleName ) {
          const roleID = roleList[i]['SYSTEM_ID'],
                checkValue = ( valueType === 'name')? roleName: roleID,
                checkedFlag = ( checkList.indexOf( checkValue ) !== -1 || arrhostlist.indexOf( tmproleID ) !== -1)? ' checked': '',
                value = ( valueType === 'name')? roleName: roleID;
          roleSelectHTML += '<tr>'
          + '<th><input value="' + roleID + '" class="modal-checkbox" type="checkbox"' + checkedFlag + '></th>'
          + '<th>' + roleID + '</th><td>' + roleName + '</td></tr>';
        
        }
         
      }

      roleSelectHTML += ''      
          + '</tbody>'
        + '</table>'
        + '</form>'
      + '</div>';

      $modalBody.html( roleSelectHTML );

      // 行で選択
      $modalBody.find('.modal-select-table').on('click', 'tr', function(){
        const $tr = $( this ),
              checked = $tr.find('.modal-checkbox').prop('checked');
        if ( checked ) {
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
          case 'ok':
            // 選択しているチェックボックスを取得
            let checkboxArray = new Array;
            $modalBody.find('.modal-checkbox:checked').each( function(){
              checkboxArray.push( $( this ).val() );
            });
            const newRoleList = checkboxArray.join(',');
            okCallback( newRoleList );
            break;
          case 'cancel':
            cancelCallBack();
            break;
        }
      });
}

