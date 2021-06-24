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
$(function(){
    var menu_on = $('.menu-on').attr('id');
    $('.menu_on').val(menu_on);


    // エクスポートボタン押下時の挙動
    $('#export_form').submit(function(){
        // 選択されたものをチェック
        var checked_num = $('#Mix1_Nakami :checked').length;
        if(checked_num == 0) {
            $('#exportMsg').text(getSomeMessage('ITABASEC090001'));
            return false;
        }

        // アラート
        var confirm_text = `${getSomeMessage('ITABASEC090013')}
${$('[name="abolished_type"]:checked').next().text()}`;
        if(!confirm(confirm_text)) {
            $('#exportMsg').text('');
            return false;
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

