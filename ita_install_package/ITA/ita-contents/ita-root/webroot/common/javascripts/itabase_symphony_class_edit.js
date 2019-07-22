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
//////////////////////////////////////////////////////////////////////
//
//  【処理概要】
//    ・Symphonyクラスを定義するページの、各種動的機能を提供する
//
//////////////////////////////////////////////////////////////////////

//------------------------------------------------//
//------------------------------------------------//
//----シンフォニークラス編集（登録/更新）         //
//------------------------------------------------//
//------------------------------------------------//
function symphonyReloadForEdit(){
    var exec_flag = true;
    if( window.confirm(getSomeMessage("ITABASEC010101")) == false ){
        exec_flag = false;
    }
    
    if( exec_flag ){
        symphonyLoadForEdit();
    }
}

function symphonyLoadForEdit(){
    var objSymphonyNoArea = document.getElementById('print_symphony_id');
    // 現在のシンフォニーを、読み込みする
    printSymphonyClass(true,objSymphonyNoArea.innerHTML,"10");
    // ボタン「編集」を、ボタン「更新」に変更する
    drawCommandButtons(1);
}

//----コールバック相互呼出系
function symphonyRegister(boolCallProxy, aryResultOfCalledProxy){
    var strAlertAreaName = 'symphony_message';
    var editInfoAreaWrap = 'symphony_header';
    var editCommandAreaWrap = 'symphony_footer';
    
    if( boolCallProxy===true ){
        // registerTableファンクション呼び出し要否フラグ
        var exec_flag = true;
        
        // アラート用エリアを初期化
        var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
        objAlertArea.innerHTML = '';
        objAlertArea.style.display = "none";
        
        // registerTableファンクション呼び出し要否フラグ
        if( window.confirm(getSomeMessage("ITAWDCC20101")) == false ){
            exec_flag = false;
        }
        
        if( exec_flag ){
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',true);
            
            var register_data = $("#"+editInfoAreaWrap+" :input").serializeArray();
            var sorted_Data = collectElementInfoForEdit();

            proxy.register_execute(register_data, sorted_Data);
        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" && aryResultOfCalledProxy[1]=="000" ){
            window.alert(getSomeMessage("ITABASEC010102"));
            var varRequestTarget = aryResultOfCalledProxy[2];
            
            //----ボタン「編集」を配置する
            drawCommandButtons(2);
            
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
            
            materialAreaFunctionOff();
            sortableAreaFunctionOff();
            
            //----実行確認前と同じく、表示専用モード[1]で表示させる。
            printSymphonyClass(true, varRequestTarget, "11");
        }
    }
}

function symphonyUpdate(boolCallProxy, aryResultOfCalledProxy){
    var strAlertAreaName = 'symphony_message';
    var editInfoAreaWrap = 'symphony_header';
    var editCommandAreaWrap = 'symphony_footer';
    
    if( boolCallProxy===true ){
        // updateTableファンクション呼び出し要否フラグ
        var exec_flag = true;
        
        // アラート用エリアを初期化
        var objAlertArea=$('#'+strAlertAreaName+' .alert_area').get()[0];
        objAlertArea.innerHTML = '';
        objAlertArea.style.display = "none";
        
        //----更新を実行してよろしいですか？
        if( window.confirm( getSomeMessage("ITAWDCC20102") ) == false ){
            exec_flag = false;
        }
        
        if( exec_flag ){
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',true);
            
            var update_data = $("#"+editInfoAreaWrap+" :input").serializeArray();
            var sorted_Data = collectElementInfoForEdit();
            
            var objSymphonyNoArea = document.getElementById('print_symphony_id');
            var symphony_class_id = objSymphonyNoArea.innerHTML;
            
            var objSymphonyLT4UArea = document.getElementById('print_symphony_lt4u');
            var symphony_lt4u = objSymphonyLT4UArea.innerHTML;
            
            proxy.update_execute(symphony_class_id, update_data, sorted_Data,symphony_lt4u);
        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" && aryResultOfCalledProxy[1]=="000" ){
            //window.alert("更新しました。");
            window.alert(getSomeMessage("ITABASEC010103"));
            var varRequestTarget = aryResultOfCalledProxy[2];
            
            //----ボタン「編集」を配置する
            drawCommandButtons(2);
            
            setInputButtonDisable(editCommandAreaWrap,'disableAfterPush',false);
            
            materialAreaFunctionOff();
            sortableAreaFunctionOff();
            
            //----実行確認前と同じく、表示専用モード[1]で表示させる。
            printSymphonyClass(true, varRequestTarget,"11");
        }
    }
}

function symphonyUpdateCancel(){
    // 現在のシンフォニーを、読み込みする
    var objSymphonyNoArea = document.getElementById('print_symphony_id');
    printSymphonyClass(true,objSymphonyNoArea.innerHTML,"11");
    // ボタン「編集」を配置する
    drawCommandButtons(2);
    //並べ替え領域の、並べ替え機能をOFFに設定する
    materialAreaFunctionOff();
    //素材一覧をドラッグできる機能をOFFに設定する
    sortableAreaFunctionOff();
}

//----作業パターン一覧を、素材領域へ展開する
function printMatchedPatternList(boolCallProxy,strStream){
    var intElementSetLength = 4;
    var strMaterialListArea = 'material_area';
    if( boolCallProxy===true ){
        //----フィルタ条件を送信
        var objFilterData = document.getElementById('filter_value');
        proxy.printMatchedPatternList(objFilterData.value);
        //フィルタ条件を送信----
    }
    else{
        var objMaterialList = document.getElementById(strMaterialListArea);
        var strInnerHtmlBody;
        
        //----パターン領域をクリアする
        objMaterialList.innerHTML = '';
        //パターン領域をクリアする----
        
        //----ムーブメント候補となれる一覧の、フィルタ結果を分析する
        var aryResult = getArrayBySafeSeparator(strStream);
        var intLengthOrg = aryResult.length;
        var intLengthDived = intLengthOrg / intElementSetLength;
        //ムーブメント候補となれる一覧の、フィルタ結果を分析する----
        
        //----フィルタ結果にある要素を、1個ずつ、リストへ追加する
        for(var fnv1=1; fnv1<=intLengthDived; fnv1++ ){
            var intBaseIndex = (fnv1 - 1) * intElementSetLength;
            
            // オーケストレータNOを取得
            var strOrcValue = aryResult[intBaseIndex];
            // 作業IDを取得
            var strPatternValue = aryResult[intBaseIndex+1];
            var strPatternName = aryResult[intBaseIndex+2];
            var strPatternColorClass = aryResult[intBaseIndex+3];
            
            objHtmlNewElement = makeElementForMaterialArea(strOrcValue, strPatternValue, strPatternName,strPatternColorClass);
            objMaterialList.insertBefore(objHtmlNewElement, null);
        }
        //フィルタ結果にある要素を、1個ずつ、リストへ追加する----
        
        //----要素がドラッグできるように設定する
        materialAreaFunctionOn();
        //要素がドラッグできるように設定する----
    }
}
//作業パターン一覧を、素材領域へ展開する----
//コールバック相互呼出系----

//----素材（作業パターン）フィルタ
function filterAutoSearchCheck( strEventType, varFreeValue1 ){
    var objFilterAutoModeControl = document.getElementById('filter_auto_mode');
    if( objFilterAutoModeControl.checked===true ){
        if( strEventType=='onKeydown' ){
            if( varFreeValue1 == 13 ){
                printMatchedPatternList(true);
            }
        }
    }
}
function filterConditionClear(){
    var objFilterInputArea = document.getElementById('filter_value');
    objFilterInputArea.value = '';
}
//素材（作業パターン）フィルタ----

//----ドラッグ＆ドロップされた要素の情報を、タグから集める。
function collectElementInfoForEdit(){
    strSortAreaClassName = 'sortable_area';
    strElementClassName = 'movement2';
    
    var tmpArray = new Array();
    tmpArray.seqNum = "statictext";
    tmpArray.ORCHE = "hiddenstatic";
    tmpArray.PATTERN = "hiddenstatic";
    tmpArray.pause_box = "checkbox";
    tmpArray.tips_box = "textarea";
    tmpArray.ovrd_ope_box = "inputtext";
    var strMoveList = dataOperationGetValuesFromElementList(strSortAreaClassName, strElementClassName, tmpArray);
    return strMoveList;
}
//ドラッグ＆ドロップされた要素の情報を、タグから集める。----

//----素材配置領域の要素作成
function makeElementForMaterialArea(strOrcValue, strPatternValue, strPatternName, strPatternColorClass){
    var strMovementClass = 'movement';
    var strDraggableHandleClass = 'draggable-handle';
    var strPatternWrapClass = 'operation_box';
    
    var objRetHtmlElement = document.createElement("div");
    objRetHtmlElement.className = strMovementClass;
    
    var objHtmlSubElement1 = document.createElement("div");
    objHtmlSubElement1.className = strDraggableHandleClass+' '+strPatternColorClass;
    objRetHtmlElement.insertBefore(objHtmlSubElement1, null);
    
    var objHtmlSubElement2 = document.createElement("div");
    objHtmlSubElement2.className = strPatternWrapClass;

    var tmpInnerHTML = '<span class="inLineTitle" title="'+strPatternName+'">'+strPatternName+'</span>';
    objHtmlSubElement2.innerHTML = tmpInnerHTML;

    objRetHtmlElement.insertBefore(objHtmlSubElement2, null)
    
    hiddenDynamicValueSet(objRetHtmlElement, 'ORCHE', strOrcValue);
    hiddenDynamicValueSet(objRetHtmlElement, 'PATTERN', strPatternValue);
    
    return objRetHtmlElement;
}
//素材配置領域の要素作成----

function editPrepare(){
    //----素材一覧をドラッグできるように設定する
    materialAreaFunctionOn();
    //素材一覧をドラッグできるように設定する----

    //----並べ替え領域の、並べ替え機能をONに設定する
    sortableAreaFunctionOn();
    //並べ替え領域の、並べ替え機能をONに設定する----
}

//----Yamazaki-Main-Made-Zone

//----削除ボタンの機能を付加する
function deleteButtonFunctionOn(){
    $('button.delete').on("click", function() {
        $(this).parents("div.movement2").remove();
        $('div.movement2').each(function() {
            $(this).children('div.seqNum').html($(this).index() + 1);
        });
    });
}
//削除ボタンの機能を付加する----


//----並べ替え領域の、並べ替え機能をONに設定する
function sortableAreaFunctionOn(){
    /* 並べ替えエリア（シンフォニー作成エリア）の定義 */
    $('.sortable_area').sortable({
        cancel : "input, button",
        containment : ".draggable_area",
        placeholder : "highlight",
        scroll : true,
        opacity : "0.6",
        handle : ".draggable-handle, .seqNum",
        axis : "y",

        // 並べ替え開始時の動作
        start : function(e, ui) {
            var $item = $(this).data().uiSortable.currentItem;
            $item.removeClass("arrow");
            $item.find("label,.delete,input").toggle();
        },

        // 並べ替え完了直前の動作
        beforeStop : function(e, ui) {
            var $item = $(this).data().uiSortable.currentItem;
            $item.addClass("arrow");
            $item.find("label,.delete,input").toggle();

        },

        // 並べ替え完了時の動作
        stop : function(e, ui) {
            // 通番の表示
            $('div.movement2').each(function() {
                $(this).children('div.seqNum').html($(this).index() + 1);
            });
        },

        // 素材一覧から要素を受け取った時の動作
        receive : function(e, ui) {
            // [D]escription
            var strDAreaWrapperClass = 'tips_box';
            var strDAreaClass = 'tips_box_textarea';
            //var strDAreaTitle = '備考';
            var strDAreaTitle = getSomeMessage("ITABASEC010104");

            // [H]old[P]oint[S]etting
            var strHPSAreaWrapperClass = 'pause_box';
            var strHPSAreaBodyClass = strHPSAreaWrapperClass + '_check';
            var strHPSAreaLabelClass = strHPSAreaWrapperClass + '_label';
            //var strHPSAreaLabelFace = '一時停止';
            var strHPSAreaLabelFace = getSomeMessage("ITABASEC010105");

            // [M]ovement[D]eter[B]utton
            var strMDBBodyClass = 'delete';
            //var strMDBBodyFace = '削除';
            var strMDBBodyFace = getSomeMessage("ITABASEC010106");

            // [M]ovement[S]equence[L]abel
            var strMSLBodyClass = 'seqNum';

            // 「備考欄」「保留チェックボックス」「通番表示領域」の追加＆クラス付け替え
            var strFaceOfOvrdOpeBox = getSomeMessage("ITABASEC010609");
            var str1 = '<div class="'+strDAreaWrapperClass+'"><textarea class="'+strDAreaClass+'" title="'+strDAreaTitle+'"></textarea><div class="areaRight"><div class="ovrd_ope_box"><span class="textExistsLikeSprit" onclick="addOvrdOpeInputBoxEditOnly(this,1,\'\');">'+strFaceOfOvrdOpeBox+'<label class="ovrd_ope_box_label"></label><input class="ovrd_ope_box_input" type="hidden" maxLength=10 ></span></div></div></div>';

            var str2 = '<div class="'+strHPSAreaWrapperClass+'"><input type="checkbox" class="'+strHPSAreaBodyClass+'" value="checkedValue"><label class="'+strHPSAreaLabelClass+'">'+strHPSAreaLabelFace+'</label></div>';
            var str3 = '<div class="'+strMSLBodyClass+'"></div>';

            var $item = $(this).data().uiSortable.currentItem;
            $item.removeClass("movement");
            $item.addClass("movement2");
            $item.append(str1 + str2 + str3);

            // 「削除ボタン」の追加（仮）
            $item.append('<button class="'+strMDBBodyClass+'">'+strMDBBodyFace+'</button>')

            // 「削除ボタン」の振る舞い
            deleteButtonFunctionOn();

        },
    });
}
//並べ替え領域の、並べ替え機能をONに設定する----

//----並べ替え領域の、並べ替え機能をOFFに設定する
function sortableAreaFunctionOff(){
    $('.sortable_area').sortable("destroy");
}
//並べ替え領域の、並べ替え機能をOFFに設定する----

//----素材一覧をドラッグできる機能を付加する
function materialAreaFunctionOn(){
    $('.movement').draggable({
        connectToSortable : ".sortable_area",
        containment : ".draggable_area",
        helper : "clone",
        opacity : "0.8",
        scroll : false,
        handle : ".draggable-handle",
        appendTo : "article",
    });
}
//素材一覧をドラッグできる機能を付加する----

//----素材一覧をドラッグできる機能をOFFに設定する
function materialAreaFunctionOff(){
    $('.movement').draggable("destroy");
}
//素材一覧をドラッグできる機能をOFFに設定する----


//Yamazaki-Main-Made-Zone----

//------------------------------------------------//
//------------------------------------------------//
//シンフォニークラス編集（登録/更新）         ----//
//------------------------------------------------//
//------------------------------------------------//

