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
function initProcess(pageMode){
    switch(pageMode){
        case "instanceConstruct":
        case "instanceConstructWithDR":
            varInitedFlag1 = false;
            varInitedFlag2 = false;
            
            var filter1AreaWrap = 'Filter1_Nakami';
            var filter2AreaWrap = 'Filter2_Nakami';
            
            webPrintRowConfirm = parseInt(document.getElementById('sysWebRowConfirm').innerHTML);
            webPrintRowLimit = parseInt(document.getElementById('sysWebRowLimit').innerHTML);
            
            webStdTableWidth = document.getElementById('webStdTableWidth').innerHTML;
            webStdTableHeight = document.getElementById('webStdTableHeight').innerHTML;
            
            // しばらくお待ち下さいを出す
            var objTableArea = $('#'+filter1AreaWrap+' .table_area').get()[0];
            objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";
            proxy.Filter1Tbl_reload(0);
            
            // しばらくお待ち下さいを出す
            var objTableArea = $('#'+filter2AreaWrap+' .table_area').get()[0];
            objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";
            proxy.Filter2Tbl_reload(0);
            
            // フィルタ部分を隠す
            show('Filter1_Midashi'   ,'Filter1_Nakami'   );
            show('Filter2_Midashi'   ,'Filter2_Nakami'   );
            
            // ボタン「実行」を配置する
            //drawCommandButtons('instanceConstruct',0);
            drawCommandButtons(pageMode,0);
            
            break;
    }
}

function activateExecuteButton(){
    var strCommandAreaWrap = 'orchestratorInfoFooter';
    
    var strOperationNoArea = 'print_operation_no_uapk';;
    var objOperationNoArea = document.getElementById(strOperationNoArea);
    
    var strPatternNoArea = 'print_pattern_id';
    var objPatternNoArea = document.getElementById(strPatternNoArea);
    
    if( objPatternNoArea.innerHTML != '' && objOperationNoArea.innerHTML != '' ){
        // ボタンを活性化する
        setInputButtonDisable(strCommandAreaWrap,'disableAfterPush',false);
    }
}

//----オペレーション情報の表示
function printOperationInfo(boolCallProxy, operation_no_uapk, operation_no_idbh, operation_name){
    var strOperationNoArea = 'print_operation_no_uapk';;
    var strOperationIdArea = 'print_operation_id';
    var strOperationNameArea = 'print_operation_name';
    if( boolCallProxy===true ){
        proxy.printOperationInfo(operation_no_uapk);
    }
    else{
        var objOperationNoArea = document.getElementById(strOperationNoArea);
        objOperationNoArea.innerHTML = operation_no_uapk;
        
        var objOperationIdArea = document.getElementById(strOperationIdArea);
        objOperationIdArea.innerHTML = operation_no_idbh;//aryOperationInfo[0];
        
        var objOperationNameArea = document.getElementById(strOperationNameArea);
        objOperationNameArea.innerHTML = '<span class="inLineTitle" title="' + operation_name + '">' + operation_name + '</span>';
        
        activateExecuteButton();
    }
}
//オペレーション情報の表示----


//----作業パターンの表示
function printPetternInfo(boolCallProxy, pettern_id, pettern_name, timer_length){
    if( boolCallProxy===true ){
        proxy.printPetternInfo(pettern_id);
    }
    else{
        var objPatternNoArea = document.getElementById('print_pattern_id');
        var objPatternNameArea = document.getElementById('print_pattern_name');

        objPatternNoArea.innerHTML = pettern_id;
        objPatternNameArea.innerHTML = '<span class="inLineTitle" title="' + pettern_name + '">' + pettern_name + '</span>';

        activateExecuteButton();
    }
}
//作業パターンの表示----

function constructExecutionNo(boolCallProxy, execution_no, ary_vars, menu_id){
    if( boolCallProxy===true ){
        var strAnsiblePatternNoArea = 'print_pattern_id';
        var objAnsiblePatternNoArea = document.getElementById(strAnsiblePatternNoArea);
        var pattern_id = objAnsiblePatternNoArea.innerHTML;
        //
        var strOperationNoArea = 'print_operation_no_uapk';;
        var objOperationNoArea = document.getElementById(strOperationNoArea);
        var operation_no = objOperationNoArea.innerHTML;
        //
        var bookdatetime = document.getElementById('bookdatetime').value;
        //
        var exec_flag = false;
        var confirm_flag = true;
        var str_confirm_message = '';
        //
        var run_mode = '';
        if( typeof ary_vars != "object" ){
            ary_vars = {};
        }
        var tmp_var = ary_vars.RUN_MODE;
        if( typeof tmp_var == "undefined" ){
            //
        }else if( typeof tmp_var == "number" ){
            run_mode = tmp_var;
        }
        //
        switch(run_mode){
            case 1: //"通常実行":
               str_confirm_message = getSomeMessage("ITABASEC010701");
               break;
            case 2: //"ドライラン"
               str_confirm_message = getSomeMessage("ITABASEC010705");
               break;
            default:
               confirm_flag = false;
               break;
        }
        
        if( pattern_id != '' && operation_no != '' ){

        }else{
            confirm_flag = false;
        }
        
        if( confirm_flag === true ){
            if( window.confirm( str_confirm_message ) ){
                exec_flag = true;
            }
        }
        //
        if( exec_flag === true ){
            proxy.orchestratorExecute(pattern_id, operation_no, bookdatetime, ary_vars);
        }
    }
    else{
        if( typeof execution_no!=="undefined" ){
            var url = '/default/menu/01_browse.php?no=' + menu_id + '&execution_no=' + execution_no;

            // 作業状態確認メニューに遷移
            location.href=url;
        }
    }

}

function drawCommandButtons(pageMode,minorPhase){
    //
    var strCommandAreaWrap = 'orchestratorInfoFooter';
    var objCommandAreaWrap = document.getElementById(strCommandAreaWrap);
    if( objCommandAreaWrap != null ){
        switch(pageMode){
            case "instanceConstruct":
                objCommandAreaWrap.innerHTML = '';
                var objButtonR50 = document.createElement('input');
                objButtonR50.type = 'button';
                objButtonR50.className = 'cmdButton disableAfterPush';
                // 実行
                objButtonR50.value = getSomeMessage("ITABASEC020101");
                strCallFunctionName = 'orchestratorExecute';
                //}
                if( minorPhase==1 ){
                    objButtonR50.disabled = false;
                }else{
                    objButtonR50.disabled = true;
                }
                objButtonR50.onclick = new Function( strCallFunctionName+"(true);" );
                if( objButtonR50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonR50, null);
                }
                break;
            case "instanceConstructWithDR":
                objCommandAreaWrap.innerHTML = '';
                var objButtonL50 = document.createElement('input');
                objButtonL50.type = 'button';
                objButtonL50.className = 'cmdButton disableAfterPush';
                // ドライラン
                objButtonL50.value = getSomeMessage("ITABASEC020102");
                strCallFunctionName = 'orchestratorDryrun';
                if( minorPhase==1 ){
                    objButtonL50.disabled = false;
                }else{
                    objButtonL50.disabled = true;
                }
                objButtonL50.onclick = new Function( strCallFunctionName+"(true);" );
                if( objButtonL50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonL50, null);
                }
                var objButtonR50 = document.createElement('input');
                objButtonR50.type = 'button';
                objButtonR50.className = 'cmdButton disableAfterPush';
                // 実行
                objButtonR50.value = getSomeMessage("ITABASEC020101");
                strCallFunctionName = 'orchestratorExecute';
                //}
                if( minorPhase==1 ){
                    objButtonR50.disabled = false;
                }else{
                    objButtonR50.disabled = true;
                }
                objButtonR50.onclick = new Function( strCallFunctionName+"(true);" );
                if( objButtonR50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonR50, null);
                }
                break;
        }
    }
}

