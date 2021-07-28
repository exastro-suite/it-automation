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


    $('#import_form').submit(function(){
        
        var checked_num = $('#Mix2_Nakami .menu:checked').length;
        if(checked_num == 0) {
            $('#importMsg').text(getSomeMessage('ITABASEC090003'));
            return false;
        }

        $('#uploadMsg').text('');
        $('#importMsg').text('');

        if(!confirm(getSomeMessage('ITABASEC090004'))) {
            return false;
        } else {
            if(!$('#import_whole').prop('checked')) {
                if(!confirm(getSomeMessage('ITABASEC090006'))) {
                    return false;
                }
            }
            $('#zipInputSubmit').prop('disabled',true);
        }
    });

    $('#import_form :input').prop('checked', true);

    $('#zipInputSubmit').on('click', function(){
        var f = $('#zipinput');
        var f_length = f[0].files.length;
        if(f_length == 0) {
            $('#uploadMsg').text(getSomeMessage('ITABASEC090005'));
            return false;
        }
    });

    $('#zipInputSubmit').submit(function(){
        $('#uploadMsg').text('');
        $('#importMsg').text('');
        $('#zipInputSubmit').prop('disabled', true);
    });

    $('#Mix2_Nakami .menu:checked').change(function(e){
        var checked_num = $('#Mix2_Nakami .menu:checked').length;
        if(checked_num == 0) {
            $('#importMsg').text(getSomeMessage('ITABASEC090003'));
            $('#importButton').prop("disabled", true);
            return false;
        } else {
            $('#importButton').prop("disabled", false);
            $('#importMsg').text('');
        }
    });
    $("#import_whole, #import_all").change(function(e){
        var checked_num = $('#Mix2_Nakami .menu:checked').length;
        if(checked_num == 0) {
            $('#importMsg').text(getSomeMessage('ITABASEC090003'));
            $('#importButton').prop("disabled", true);
        } else {
            $('#importButton').prop("disabled", false);
            $('#importMsg').text('');
        }
    });

    show('SetsumeiMidashi', 'SetsumeiNakami');
    show('Mix2_Midashi', 'Mix2_Nakami');
});

