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


    $('#import_form').submit(function(){
        
        var checked_num = $('#Mix2_Nakami .menu_id_checkbox:checked:not(:disabled)').length;
        if(checked_num == 0) {
            $('#importMsg').text(getSomeMessage('ITABASEC090003'));
            return false;
        }

        $('#uploadMsg').text('');
        $('#importMsg').text('');

        if(!confirm(getSomeMessage('ITABASEC2100000330_1'))) {
            return false;
        } else {
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

    let import_whole = document.getElementById("import_whole");
    let checkboxList = document.getElementsByClassName('menu_id_checkbox');
    for (let i = 0; i < checkboxList.length; i++) {
        let trList = checkboxList[i].parentNode.parentNode;
        trList.addEventListener('click', function(e){
            checkClick(checkboxList[i]);
            for (let l = 0; l < checkboxList.length; l++) {
                if (checkboxList[l].checked == false) {
                    import_whole.checked = false;
                    break;
                }
                import_whole.checked = true;
            }
        });
        checkboxList[i].addEventListener('click', function(e){
            checkClick(checkboxList[i]);
            for (let l = 0; l < checkboxList.length; l++) {
                if (checkboxList[l].checked == false) {
                    import_whole.checked = false;
                    break;
                }
                import_whole.checked = true;
            }
        });
    }

    $("#import_whole").click(function(e){
        if (e.target.checked) {
            for (let i = 0; i < checkboxList.length; i++) {
                if (checkboxList[i].disabled == false) {
                    checkboxList[i].checked = true;
                }
            }
        } else {
            for (let i = 0; i < checkboxList.length; i++) {
                if (checkboxList[i].disabled == false) {
                    checkboxList[i].checked = false;
                }
            }
        }
    });

    $("#import_whole").change(function(e){
        let checked_num = $('#Mix2_Nakami .menu_id_checkbox:checked:not(:disabled)').length;
        if(checked_num == 0) {
            $('#importMsg').text(getSomeMessage('ITABASEC090003'));
            $('#importButton').prop("disabled", true);
        } else {
            $('#importButton').prop("disabled", false);
            $('#importMsg').text('');
        }
    });

    function checkClick(trg) {
        if (!trg.disabled) {
            if (trg.checked == true) {
                trg.checked = false;
            } else if (trg.checked == false) {
                trg.checked = true;
            }
            let checked_num = $('#Mix2_Nakami .menu_id_checkbox:checked:not(:disabled)').length;
            if(checked_num == 0) {
                $('#importMsg').text(getSomeMessage('ITABASEC090003'));
                $('#importButton').prop("disabled", true);
            } else {
                $('#importButton').prop("disabled", false);
                $('#importMsg').text('');
            }
        }
    }

    // インポート可能なメニューがあるかチェック
    importableMenuCheck();
    function importableMenuCheck() {
        let checkboxList = document.getElementsByClassName('menu_id_checkbox');
        let importableMenuFlg = false;
        for (let i = 0; i < checkboxList.length; i++) {
            if (checkboxList[i].disabled == false) {
                importableMenuFlg = true;
            }
        }
        if (!importableMenuFlg) {
            $('#importMsg').text(getSomeMessage('ITABASEC090003'));
            $('#importButton').prop("disabled", true);
            $("#import_whole").prop("disabled", true);
        }
    }

    show('SetsumeiMidashi', 'SetsumeiNakami');
    show('Mix2_Midashi', 'Mix2_Nakami');
});

