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

    $('#import_form').submit(function(){
        
        var checked_num = $('#Mix2_Nakami :checked').length;
        if(checked_num == 0) {
            window.alert(getSomeMessage('ITABASEC090012'));
            return false;
        }

        $('#uploadMsg').text('');
        $('#importMsg').text('');

        if(!confirm(getSomeMessage('ITABASEC090004'))) {
            return false;
        } else {
            $('#zipInputSubmit').prop('disabled',true);
            $('#importButton').prop('disabled',true);
        }
    });

    $('#import_form :input').prop('checked', true);

    $('#zipInputSubmit').on('click', function(){
        var f = $('#zipinput');
        var f_length = f[0].files.length;
        if(f_length == 0) {
            window.alert(getSomeMessage('ITABASEC090005'));
            return false;
        }
    });

    $('#zipInputSubmit').submit(function(){
        $('#uploadMsg').text('');
        $('#importMsg').text('');
        $('#zipInputSubmit').prop('disabled', true);
        $('#importButton').prop('disabled',true);
    });

    show('SetsumeiMidashi', 'SetsumeiNakami');
    show('Mix2_Midashi', 'Mix2_Nakami');
});

