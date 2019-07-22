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
//////// ----コールバックファンクション ////////
function callback() {}
callback.prototype = {
    //----Symphony系メソッド
    printSymphonyClass : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            printSymphonyClass(false,ary_result[2],ary_result[3],ary_result[4],ary_result[5]);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[6];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[6];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    printMatchedPatternList : function( result ){

        var strAlertAreaName = 'symphony_message';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            printMatchedPatternList(false,ary_result[2])

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    update_execute : function( result ){

        var strAlertAreaName = 'symphony_message';
        var editCommandAreaWrap = 'symphony_footer';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            symphonyUpdate(false, ary_result);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            // ボタンを再活性化
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    register_execute : function( result ){

        var strAlertAreaName = 'symphony_message';
        var editCommandAreaWrap = 'symphony_footer';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){

            symphonyRegister(false, ary_result);

        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            // ボタンを再活性化
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var resultContentTag = ary_result[3];
            var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
            objAlertArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }

        showForDeveloper(result);
    }
    //Symphony系メソッド----
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
var privilege;
//////// 画面生成時に初回実行する処理 ////////

var proxy = new Db_Access(new callback());

window.onload = function(){
    var initialFilterEl;
    var initialFilter;
    initialFilterEl = document.getElementById('sysInitialFilter');
    privilege = parseInt(document.getElementById('privilege').innerHTML);
    if(initialFilterEl == null){
        initialFilter = 2;
    }
    else{
        initialFilter = initialFilterEl.innerHTML;
    }

    initProcess('classEdit');

    if(privilege != 1){
        var strCommandAreaWrap = 'symphony_footer';
        var objCommandAreaWrap = document.getElementById(strCommandAreaWrap);
        objCommandAreaWrap.innerHTML = '';
    }

    var objFEB = document.getElementById('filter_execute');
    if(initialFilter == 1){
        objFEB.click();
    }
}

//---- ここからカスタマイズした場合の一般メソッド配置域
// ここまでカスタマイズした場合の一般メソッド配置域----
