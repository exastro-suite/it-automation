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
$(function(){
    // 横幅をそろえる
    let label_list = document.getElementsByClassName("export_radio_label_block");
    max_width_label = (label_list[0].clientWidth > label_list[2].clientWidth) ? label_list[2].style.width = `${label_list[0].clientWidth}px` : label_list[0].style.width = `${label_list[2].clientWidth}px`;

    var menu_on = $('.menu-on').attr('id');
    $('.menu_on').val(menu_on);
    $( 'input[name="dp_mode"]:radio' ).change( function() {
        if ( $('[name="dp_mode"]:checked').val() == 2 ) {
            $('[name="specified_timestamp"]').prop('disabled', false);
        } else {
            $('[name="specified_timestamp"]').prop('disabled', true);
            $('[name="specified_timestamp"]').val("");
        }
    });


    // エクスポートボタン押下時の挙動
    $('#export_form').submit(function(){
        // 選択されたものをチェック
        var checked_num = $('#Mix1_Nakami :checked').length;
        if(checked_num == 0) {
            $('#exportMsg').text(getSomeMessage('ITABASEC090001'));
            return false;
        }

        // 指定時刻取得
        var specified_timestamp = $('[name="specified_timestamp"]').val();
        var specified_timestamp_text = $('[name="dp_mode"]:checked').val() == 2 ? ` (${specified_timestamp}) ` : ``;
        // 指定時刻のチェック
        if ( $('[name="dp_mode"]:checked').val() == 2 && !specified_timestamp ) {
            $('#exportMsg').text(getSomeMessage('ITABASEC090015'));
            return false;
        }
        // 指定時刻が存在する日付・時刻かチェック
        if ( $('[name="dp_mode"]:checked').val() == 2 && !isDateTime(specified_timestamp)) {
            $('#exportMsg').text(getSomeMessage('ITABASEC090016'));
            return false;
        }
        // アラート
        var confirm_text = `${getSomeMessage('ITABASEC090013')}
${$('[name="dp_mode"]:checked').next().text()}${specified_timestamp_text}/${$('[name="abolished_type"]:checked').next().text()}`;
        if(!confirm(confirm_text)) {
            $('#exportMsg').text('');
            return false;
        }

        if(!$('#export_whole').prop('checked')) {
            if(!confirm(getSomeMessage('ITABASEC090006'))) {
                return false;
            }
        }
    });

    show('SetsumeiMidashi', 'SetsumeiNakami');
});

//////// 日時ピッカー表示用ファンクション ////////
$(function(){
    setDatetimepicker('bookdatetime');
});

function isDateTime (str) {
    let arr = str.split(' ');

    let str_date = arr[0]; // 日付
    let str_time = arr[1]; // 時刻

    if ( isDate(str_date) && isTime(str_time) ) {
        return true;
    } else {
        return false;
    }
};

// 日付の妥当性をチェック
function isDate(str) {
    var arr = str.split("/");
    if (arr.length !== 3) return false;
    const date = new Date(arr[0], arr[1] - 1, arr[2]);
    if (arr[0] !== String(date.getFullYear()) || arr[1] !== ('0' + (date.getMonth() + 1)).slice(-2) || arr[2] !== ('0' + date.getDate()).slice(-2)) {
      return false;
    } else {
      return true;
    }
}

// 時刻の妥当性をチェック
function isTime(str) {
    return str.match(/^([01]?[0-9]|2[0-3]):([0-5][0-9])$/) !== null;
}

