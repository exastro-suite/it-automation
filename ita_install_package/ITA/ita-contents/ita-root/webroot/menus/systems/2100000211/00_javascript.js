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
    var menu_on = $('.menu-on').attr('id');
    $('.menu_on').val(menu_on);

    $('#export_form').submit(function(){
        var checked_num = $('#Mix1_Nakami :checked').length;
        if(checked_num == 0) {
            $('#exportMsg').text(getSomeMessage('ITABASEC090001'));
            return false;
        }

        if(!confirm(getSomeMessage('ITABASEC090002'))) {
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
