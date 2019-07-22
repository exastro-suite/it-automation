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
//////// ----業務色のないファンクション ////////

//----クラス編集時（登録・更新）＜受信データ反映＞
function dataOperationSetValuesToElementList(strAjaxProxyResponseStream,aryItemKeyInElement){
    //----[aryItemKeyInElement]は連想配列なのでlengthは用いない
    var intDelimitScaleLength = 0;
    for(key in aryItemKeyInElement){
        intDelimitScaleLength += 1;
    }
    //[aryItemKeyInElement]は連想配列なのでlengthは用いない----
    var aryParallelBody = getParallelArrayFromStraightArray(strAjaxProxyResponseStream, intDelimitScaleLength);

    return aryParallelBody;
}
//クラス編集時（登録・更新）＜受信データ反映＞----

//----クラス編集時（登録・更新）＜送信用データ作成＞
function dataOperationGetValuesFromElementList(strElementOwnerAreaClassName, strElementClassName, aryItemKeyInElement){
    // 並べ替え領域[strElementOwnerAreaClassName]から、
    // 並べ替え要素[strElementClassName]の中の値を、
    // 値定義配列[項目識別子:インプットタグ様式の配列[aryItemKeyInElement]]に従って、データを取得する。
    var retValue = '';
    var arySubjectBody = new Array();
    var intSubjectLength = 0;
    var objResult = $('.'+strElementOwnerAreaClassName+' .'+strElementClassName);
    if( objResult !== null ){
        //----データ配列
        var objElements = objResult.get();
        for(var fnv1 = 0; fnv1 < objElements.length; fnv1 ++ ){
            var objFocusElement = objElements[fnv1];
            for(key in aryItemKeyInElement){
                tmpVar = "";
                intSubjectLength = intSubjectLength + 1;
                if( aryItemKeyInElement[key] == "textarea" || aryItemKeyInElement[key] == "inputtext" || aryItemKeyInElement[key] == "statictext"){
                    //----テキストエリア/テキストボックス/固定テキストの場合
                    var tmpVar = dynamicTextValueGet(objFocusElement, key, aryItemKeyInElement[key]);
                    //テキストエリア/テキストボックス/固定テキストの場合----
                }
                else if( aryItemKeyInElement[key] == "checkbox" ){
                    //----チェックボックスの場合
                    var tmpVar = dynamicCheckBoxValueGet(objFocusElement, key);
                    //チェックボックスの場合----
                }
                else if( aryItemKeyInElement[key] == "button" ){
                    //----チェックボックスの場合
                    var tmpVar = dynamicButtonValueGet(objFocusElement, key);
                    //チェックボックスの場合----
                }
                else if( aryItemKeyInElement[key] == "hiddenstatic" ){
                    //----チェックボックスの場合
                    var tmpVar = hiddenDynamicValueGet(objFocusElement, key);
                    //チェックボックスの場合----
                }
                if( tmpVar === null || tmpVar === false ){
                    //----値が取得できなかった場合
                    tmpVar = "";
                    //値が取得できなかった場合---
                }
                arySubjectBody[intSubjectLength - 1] = tmpVar;
            }
        }
        //データ配列----
    }
    if( 0 < intSubjectLength ){
        retValue = makeAjaxProxyRequestStream(arySubjectBody);
    }
    return retValue
}
//クラス編集時（登録・更新）＜送信用データ作成＞----

//----N1xN2の個数の要素をもつ直列配列をN1またはN2で分割して並列配列にする
function getParallelArrayFromStraightArray(strAjaxProxyResponseStream, intDelimitScaleLength){
    var aryResult = new Array();
    //
    var aryStraightBody = getArrayBySafeSeparator(strAjaxProxyResponseStream);
    var intStraightLength = aryStraightBody.length;
    //
    intFocusDivideIndex = 0;
    intFocusIndexInDelimitedArea = 0;
    //
    for(var fnv1=0; fnv1<intStraightLength; fnv1++){
        var intFocusDivideIndex = Math.floor(fnv1/intDelimitScaleLength);
        var intFocusIndexInDelimitedArea = fnv1 % intDelimitScaleLength;
        if( intFocusIndexInDelimitedArea == 0 ){
            var tempArray = new Array();
        }
        tempArray[intFocusIndexInDelimitedArea] = aryStraightBody[fnv1];
        if( intFocusIndexInDelimitedArea===intDelimitScaleLength-1){
            aryResult[intFocusDivideIndex] = tempArray;
        }
    }
    return aryResult;
}
//N1xN2の個数の要素をもつ直列配列をN1またはN2で分割して並列配列にする----



//////// ----htmlを用いた値の動的管理 ////////

//----[3]ボタンエリア
function dynamicButtonValueGet(objHtmlTagOfListOwner, strKey){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retValue = false;
    var tagNodeName = "div";
    //
    //----共通引数チェック
    if( typeof strKey==="string" ){
        if( strKey==="" ){
            boolExeCountinue = false;
        }
    }
    else{
        boolExeCountinue = false;
    }
    //共通引数チェック----
    //
    if( boolExeCountinue===true ){
        var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
        if( objNodes.length===0 ){
            //----GET関数共通[要素がなかった場合のみNULL]
            var retValue = null;
            boolExeCountinue = false;
            //GET関数共通[要素がなかった場合のみNULL]----
        }
        else if( 1 != objNodes.length ){
            boolExeCountinue = false;
        }
        else{
            var objNode = objNodes[0];
            if( objNode.nodeType != 1 || objNode.nodeName.toLowerCase() != tagNodeName ){
                boolExeCountinue = false;
            }
            else{
                var objNodesInDiv1 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_btnBdy');
                if( 0 == objNodesInDiv1.length || 1 < objNodesInDiv1.length ){
                    boolExeCountinue = false;
                }
                var objNodesInDiv2 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_btnVal');
                if( 0 == objNodesInDiv2.length || 1 < objNodesInDiv2.length ){
                    boolExeCountinue = false;
                }
            }
        }
    }
    //
    if( boolExeCountinue===true ){
        if( typeof strSetValue==="undefined" ){
            strSetValue = "";
        }
        var objNodeInDiv1 = objNodesInDiv1[0];
        if( objNodeInDiv1.nodeType == 1 ){
            if( objNodeInDiv1.nodeName.toLowerCase() == 'input' ){
                if( objNodeInDiv1.type.toLowerCase() == 'button' ){
                    retValue = '';
                    if( typeof objNodeInDiv1.disabled==="undefined" ){
                    }
                    else{
                        if( objNodeInDiv1.disabled===false ){
                            //----活性化されている場合は隠されている値を返す
                            var objNodeInDiv2 = objNodesInDiv2[0];
                            retValue = objNodeInDiv2.innerHTML;
                            //活性化されている場合は隠されている値を返す----
                        }
                    }
                }
            }
        }
    }
    return retValue;
}

function dynamicButtonValueSet(objHtmlTagOfListOwner, strKey, strSetFaceValue, strHiddenValue, boolDisabled){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retBool = false;
    var tagNodeName = "div";
    //
    //----共通引数チェック
    if( typeof strKey==="string" ){
        if( strKey==="" ){
            boolExeCountinue = false;
        }
    }
    else{
        boolExeCountinue = false;
    }
    //共通引数チェック----
    //
    if( boolExeCountinue===true ){
        var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
        if( objNodes.length===0 || objNodes.length != 1 ){
            boolExeCountinue = false;
        }
        else{
            var objNode = objNodes[0];
            if( objNode.nodeType != 1 || objNode.nodeName.toLowerCase() != tagNodeName ){
                boolExeCountinue = false;
            }
            else{
                var objNodesInDiv1 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_btnBdy');
                if( 0 == objNodesInDiv1.length || 1 < objNodesInDiv1.length ){
                    boolExeCountinue = false;
                }
                var objNodesInDiv2 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_btnVal');
                if( 0 == objNodesInDiv2.length || 1 < objNodesInDiv2.length ){
                    boolExeCountinue = false;
                }
            }
        }
    }
    
    if( boolExeCountinue===true ){
        var objNodeInDiv1 = objNodesInDiv1[0];
        if( objNodeInDiv1.nodeType == 1 ){
            if( objNodeInDiv1.nodeName.toLowerCase() == 'input' ){
                if( objNodeInDiv1.type.toLowerCase() == 'button' ){
                    if( typeof strSetFaceValue==="string" ){
                        //----値の変更をする
                        objNodeInDiv1.value = strSetFaceValue;
                        //値の変更をする----
                    }
                    //
                    if( typeof boolDisabled==="boolean" ){
                        //----値の変更をする
                        objNodeInDiv1.checked = boolDisabled;
                        retBool = true;
                        //値の変更をする----
                    }
                }
            }
        }
        var objNodeInDiv2 = objNodesInDiv2[0];
        if( objNodeInDiv2.nodeType == 1 ){
            if( objNodeInDiv2.nodeName.toLowerCase() == 'div' ){
                if( typeof strHiddenValue==="string" ){
                    //----値の変更をする
                    objNodeInDiv2.innerHTML = strHiddenValue;
                    //値の変更をする----
                }
            }
        }
    }
    return retBool;
}

function dynamicButtonControlPut(objHtmlTagOfListOwner, strKey, strSetFaceValue, strHiddenValue, boolDisabled, objScaleObj){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retArray = new Array();
    var retBool = false;
    var objHtmlTagRec = null;
    var tagNodeName = "div";
    //
    //----共通引数チェック
    if( typeof strKey==="string" ){
        if( strKey==="" ){
            boolExeCountinue = false;
        }
        else{
            var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
            if( 0 < objNodes.length ){
                boolExeCountinue = false;
            }
        }
    }
    else{
        boolExeCountinue = false;
    }
    //共通引数チェック----
    //
    if( boolExeCountinue===true ){
        //----存在していないので配置
        if( typeof objScaleObj==="undefined" ){
            //----1番最後に追加
            objScaleObj = null;
            //1番最後に追加----
        }
        if( typeof strSetFaceValue==="undefined" ){
            strSetFaceValue = "";
        }
        if( typeof boolDisabled != "boolean" ){
            boolDisabled = false;
        }
        objHtmlTagRec = document.createElement(tagNodeName);
        objHtmlTagRec.className = strKey;
        objHtmlTagRec.innerHTML = '<input type="button" class="'+strKey+'_btnBdy" value="'+strSetFaceValue+'" /><div class="'+strKey+'_btnVal" style="display:none;">'+strHiddenValue+'</div>';
        objHtmlTagRec.disabled = boolDisabled;
        objHtmlTagOfListOwner.insertBefore(objHtmlTagRec, objScaleObj);
        retBool = true;
        //存在していないので配置----
    }
    retArray[0] = retBool;
    retArray[1] = objHtmlTagRec;
    return retArray;
}
//[3]ボタンエリア----

//----[2]チェックボックスエリア
function dynamicCheckBoxValueGet(objHtmlTagOfListOwner, strKey){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retValue = false;
    var tagNodeName = "div";
    //
    //----共通引数チェック
    if( typeof strKey==="string" ){
        if( strKey==="" ){
            boolExeCountinue = false;
        }
    }
    else{
        boolExeCountinue = false;
    }
    //共通引数チェック----
    //
    if( boolExeCountinue===true ){
        var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
        if( objNodes.length===0 ){
            //----GET関数共通[要素がなかった場合のみNULL]
            var retValue = null;
            boolExeCountinue = false;
            //GET関数共通[要素がなかった場合のみNULL]----
        }
        else if( 1!= objNodes.length ){
            boolExeCountinue = false;
        }
        else{
            var objNode = objNodes[0];
            if( objNode.nodeType != 1 || objNode.nodeName.toLowerCase() != tagNodeName ){
                boolExeCountinue = false;
            }
            else{
                var objNodesInDiv1 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_check');
                if( 0 == objNodesInDiv1.length || 1 < objNodesInDiv1.length ){
                    boolExeCountinue = false;
                }
                var objNodesInDiv2 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_label');
                if( 0 == objNodesInDiv2.length || 1 < objNodesInDiv2.length ){
                    boolExeCountinue = false;
                }
            }
        }
    }
    
    if( boolExeCountinue===true ){
        if( typeof strSetValue==="undefined" ){
            strSetValue = "";
        }
        var objNodeInDiv1 = objNodesInDiv1[0];
        if( objNodeInDiv1.nodeType == 1 ){
            if( objNodeInDiv1.nodeName.toLowerCase() == 'input' ){
                if( objNodeInDiv1.type.toLowerCase() == 'checkbox' ){
                    retValue = '';
                    if( typeof objNodeInDiv1.checked==="undefined" ){
                    }
                    else{
                        if( objNodeInDiv1.checked===true ){
                            //----チェックが入っている場合は値を返す
                            retValue = objNodeInDiv1.value;
                            //チェックが入っている場合は値を返す----
                        }
                    }
                }
            }
        }
    }
    return retValue;
}

function dynamicCheckBoxValueSet(objHtmlTagOfListOwner, strKey, boolChecked, strSetValue, strLabel){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retBool = false;
    var tagNodeName = "div";
    //
    //----共通引数チェック
    if( typeof strKey==="string" ){
        if( strKey==="" ){
            boolExeCountinue = false;
        }
    }
    else{
        boolExeCountinue = false;
    }
    //共通引数チェック----
    //
    if( boolExeCountinue===true ){
        var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
        if( objNodes.length===0 || 1!= objNodes.length ){
            boolExeCountinue = false;
        }
        else{
            var objNode = objNodes[0];
            if( objNode.nodeType != 1 || objNode.nodeName.toLowerCase() != tagNodeName ){
                boolExeCountinue = false;
            }
            else{
                var objNodesInDiv1 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_check');
                if( 0 == objNodesInDiv1.length || 1 < objNodesInDiv1.length ){
                    boolExeCountinue = false;
                }
                var objNodesInDiv2 = objHtmlTagOfListOwner.getElementsByClassName(strKey+'_label');
                if( 0 == objNodesInDiv2.length || 1 < objNodesInDiv2.length ){
                    boolExeCountinue = false;
                }
            }
        }
    }
    
    if( boolExeCountinue===true ){
        var objNodeInDiv1 = objNodesInDiv1[0];
        if( objNodeInDiv1.nodeType == 1 ){
            if( objNodeInDiv1.nodeName.toLowerCase() == 'input' ){
                if( objNodeInDiv1.type.toLowerCase() == 'checkbox' ){
                    if( typeof strSetValue==="string" ){
                        //----値の変更をする
                        objNodeInDiv1.value = strSetValue;
                        //値の変更をする----
                    }
                    
                    if( typeof boolChecked==="boolean" ){
                        //----値の変更をする
                        objNodeInDiv1.checked = boolChecked;
                        retBool = true;
                        //値の変更をする----
                    }
                }
            }
        }
        var objNodeInDiv2 = objNodesInDiv2[0];
        if( objNodeInDiv2.nodeType == 1 ){
            if( objNodeInDiv2.nodeName.toLowerCase() == 'span' ){
                if( typeof strLabel==="string" ){
                    //----値の変更をする
                    objNodeInDiv2.innerHTML = strLabel;
                    //値の変更をする----
                }
            }
        }
    }
    return retBool;
}

function dynamicCheckBoxControlPut(objHtmlTagOfListOwner, strKey, strSetValue, strLabel, objScaleObj){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retArray = new Array();
    var retBool = false;
    var objHtmlTagRec = null;
    var tagNodeName = "div";
    
    //----共通引数チェック
    if( typeof strKey==="string" ){
        if( strKey==="" ){
            boolExeCountinue = false;
        }
        else{
            var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
            if( 0 < objNodes.length ){
                boolExeCountinue = false;
            }
        }
    }
    else{
        boolExeCountinue = false;
    }
    //共通引数チェック----
    
    if( boolExeCountinue===true ){
        //----存在していないので配置
        if( typeof objScaleObj==="undefined" ){
            //----1番最後に追加
            objScaleObj = null;
            //1番最後に追加----
        }
        if( typeof strSetValue==="undefined" ){
            strSetValue = "";
        }
        objHtmlTagRec = document.createElement(tagNodeName);
        objHtmlTagRec.className = strKey;
        objHtmlTagRec.innerHTML = '<input type="checkbox" class="'+strKey+'_check" value="'+strSetValue+'" /><span class="'+strKey+'_label">'+strLabel+'</span>';
        objHtmlTagOfListOwner.insertBefore(objHtmlTagRec, objScaleObj);
        retBool = true;
        //存在していないので配置----
    }
    retArray[0] = retBool;
    retArray[1] = objHtmlTagRec;
    return retArray;
}
//[2]チェックボックスエリア----

//----[1]テキストエリア
function dynamicTextValueGet(objHtmlTagOfListOwner, strKey, strTextType){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retValue = false;
    var tagNodeName = "div";
    //
    if( typeof strKey==="string" && typeof strTextType==="string" ){
        if( strTextType != 'textarea' && strTextType != 'inputtext' && strTextType != 'statictext' ){
            boolExeCountinue = false;
        }
        else{
            var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
            if( 0 == objNodes.length ){
                //----GET関数共通[要素がなかった場合のみNULL]
                retValue = null;
                boolExeCountinue = false;
                //GET関数共通[要素がなかった場合のみNULL]----
            }
            else if ( 1 != objNodes.length ){
                boolExeCountinue = false;
            }
            else{
                var objNode = objNodes[0];
                if( objNode.nodeType != 1 || objNode.nodeName.toLowerCase() != tagNodeName ){
                    boolExeCountinue = false;
                }
            }
        }
    }
    if( boolExeCountinue===true ){
        if( strTextType == 'textarea' ){
            var objNodesInDiv1 = objNode.getElementsByClassName(strKey+'_textarea');
            if( 1 == objNodesInDiv1.length ){
                retValue = objNodesInDiv1[0].value;
            }
        }
        else if( strTextType == 'inputtext'){
            var objNodesInDiv1 = objNode.getElementsByClassName(strKey+'_input');
            if( 1 == objNodesInDiv1.length ){
                retValue = objNodesInDiv1[0].value;
            }
        }
        else if( strTextType == 'statictext'){
            retValue = objNode.innerHTML;
        }
    }
    return retValue;
}

function dynamicTextValueSet(objHtmlTagOfListOwner, strKey, strSetValue, strTextType, boolAdd){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retBool = false;
    var tagNodeName = "div";
    //
    if( typeof strKey==="string" && typeof strTextType==="string" ){
        if( strTextType != 'textarea' && strTextType != 'inputtext' && strTextType != 'statictext' ){
            boolExeCountinue = false;
        }
        else{
            var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
            if( 0 == objNodes.length ||  1 != objNodes.length ){
                boolExeCountinue = false;
            }
            else{
                var objNode = objNodes[0];
                if( objNode.nodeType != 1 || objNode.nodeName.toLowerCase() != tagNodeName ){
                    boolExeCountinue = false;
                }
            }
        }
    }
    if( boolExeCountinue===true ){
        if( typeof strSetValue!=="string" ){
            strSetValue = "";
        }
        if( strTextType == 'textarea' ){
            var objNodesInDiv1 = objNode.getElementsByClassName(strKey+'_textarea');
            if( 1 == objNodesInDiv1.length ){
                retBool = true;
                objNodesInDiv1[0].innerHTML = strSetValue;
            }
        }
        else if( strTextType == 'inputtext'){
            var objNodesInDiv1 = objNode.getElementsByClassName(strKey+'_input');
            if( 1 == objNodesInDiv1.length ){
                retBool = true;
                objNodesInDiv1[0].value = strSetValue;
            }
        }
        else if( strTextType == 'statictext'){
            objNodes[0].innerHTML = strSetValue;
        }
    }
    return retBool;
}

function dynamicTextControlPut(objHtmlTagOfListOwner, strKey, strSetValue, strTextType, objScaleObj){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retArray = new Array();
    var retBool = false;
    var objHtmlTagRec = null;
    var tagNodeName = "div";
    //
    if( typeof strKey==="string" && typeof strTextType==="string" ){
        if( strTextType != 'textarea' && strTextType != 'inputtext' && strTextType != 'statictext' ){
            boolExeCountinue = false;
        }
        else{
            var objNodes = objHtmlTagOfListOwner.getElementsByClassName(strKey);
            if( 0 < objNodes.length ){
                //----存在しているので何もしない
                boolExeCountinue = false;
                //存在しているので何もしない----
            }
        }
    }
    else{
        boolExeCountinue = false;
    }
    
    if( boolExeCountinue===true ){
        //----存在していないので配置
        if( typeof objScaleObj==="undefined" ){
            //----1番最後に追加
            objScaleObj = null;
            //1番最後に追加----
        }
        if( typeof strSetValue!=="string" ){
            strSetValue = "";
        }
        objHtmlTagRec = document.createElement(tagNodeName);
        objHtmlTagRec.className = strKey;
        objHtmlTagOfListOwner.insertBefore(objHtmlTagRec, objScaleObj);
        if( strTextType == 'textarea' ){
            retBool = true;
            var objChildHtmlTag1 = document.createElement('textarea');
            objChildHtmlTag1.innerHTML = strSetValue;
            objChildHtmlTag1.className = strKey+'_textarea';
            objHtmlTagRec.insertBefore(objChildHtmlTag1, null);
        }
        else if( strTextType == 'inputtext'){
            retBool = true;
            var objChildHtmlTag1 = document.createElement('input');
            objChildHtmlTag1.value = strSetValue;
            objChildHtmlTag1.className = strKey+'_input';
            objChildHtmlTag1.type = 'text';
            objHtmlTagRec.insertBefore(objChildHtmlTag1, null);
        }
        else if( strTextType == 'statictext'){
            retBool = true;
            objHtmlTagRec.innerHTML = strSetValue;
        }
        retBool = true;
        //存在していないので配置----
    }
    retArray[0] = retBool;
    retArray[1] = objHtmlTagRec;
    return retArray;
}
//[1]テキストエリア----

//----[0]隠し値群
function hiddenDynamicValueGet(objHtmlTagOfListOwner, strKey, strValueBankClassName){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retValue=null;
    if( typeof strValueBankClassName!=="string" ){
        strValueBankClassName = "hiddenDynamicValues";
    }
    if( typeof strKey==="string" ){
        if( objHtmlTagOfListOwner===null ){
            boolExeCountinue = false;
        }
    }
    else{
        boolExeCountinue = false;
    }
    if(  boolExeCountinue===true ){
        if( 0 < objHtmlTagOfListOwner.childNodes.length ){
            if( objHtmlTagOfListOwner.childNodes[0].nodeType == 1 ){
                if( objHtmlTagOfListOwner.childNodes[0].className!=strValueBankClassName ){
                    boolExeCountinue = false;
                }
            }
            else{
                boolExeCountinue = false;
            }
        }
        else{
            boolExeCountinue = false;
        }
    }
    //
    if(  boolExeCountinue===true ){
        //----表示しないDIV「key付子要素を追加する要素」を発見した
        var objHtmlTagList  = objHtmlTagOfListOwner.childNodes[0];
        var varRecordDivLen = objHtmlTagList.childNodes.length;
        var fnv1;
        var boolKeyExists=false;
        for( fnv1=0; fnv1<varRecordDivLen; fnv1++){
            if( objHtmlTagList.childNodes[fnv1].nodeType == 1 ){
                if( objHtmlTagList.childNodes[fnv1].className==strKey ){
                    boolKeyExists = true;
                    retValue = objHtmlTagList.childNodes[fnv1].innerHTML;
                    break;
                }
            }
        }
        //表示しないDIV「key付子要素を追加する要素」を発見した----
    }
    return retValue;
}

function hiddenDynamicValueSet(objHtmlTagOfListOwner, strKey, strSetValue, strValueBankClassName){
    //----共通定義変数
    var boolExeCountinue = true;
    //共通定義変数----
    //
    var retBool = false;
    //
    if( typeof strValueBankClassName!=="string" ){
        strValueBankClassName = "hiddenDynamicValues";
    }
    if( typeof strKey==="string" ){
        if( objHtmlTagOfListOwner===null ){
            boolExeCountinue = false;
        }
    }
    else{
        boolExeCountinue = false;
    }
    //
    if( boolExeCountinue===true ){
        if( typeof strSetValue!=="string" ){
            strSetValue = "";
        }
        var boolAddDiv = false;
        var beforeObj;
        if( objHtmlTagOfListOwner.childNodes.length==0 ){
            boolAddDiv = true;
            beforeObj = null;
        }
        else if( objHtmlTagOfListOwner.childNodes[0].className!=strValueBankClassName ){
            boolAddDiv = true;
            beforeObj = objHtmlTagOfListOwner.childNodes[0];
        }
        else{
            boolAddDiv = false;
            var objHtmlTagList = objHtmlTagOfListOwner.childNodes[0];
        }
        
        if( boolAddDiv===true ){
            //----表示しないDIV「key付子要素を追加する要素」を追加する
            var parentObj = objHtmlTagOfListOwner.parentNode;
            //
            var objHtmlTagList = document.createElement("div");
            objHtmlTagList.style.display = "none";
            objHtmlTagList.className = 'hiddenDynamicValues';
            //
            objHtmlTagOfListOwner.insertBefore(objHtmlTagList, beforeObj);
            //表示しないDIV「key付子要素を追加する要素」を追加する----
        }
        
        var varRecordDivLen = objHtmlTagList.childNodes.length;
        var fnv1;
        var boolKeyFound = false;
        for( fnv1=0; fnv1<varRecordDivLen; fnv1++){
            if( objHtmlTagList.childNodes[fnv1].nodeType == 1 ){
                var objChild = objHtmlTagList.childNodes[fnv1];
                if( objChild.className == strKey ){
                    boolKeyFound = true;
                    if( strSetValue===null ){
                        objHtmlTagList.removeChild(objChild);
                    }
                    else{
                        objChild.innerHTML = strSetValue;
                    }
                    retBool = true;
                    break;
                }
            }
        }
        if( boolKeyFound===false ){
            var objHtmlTagRec = document.createElement("div");
            objHtmlTagRec.className = strKey;
            objHtmlTagRec.innerHTML = strSetValue;
            objHtmlTagList.insertBefore(objHtmlTagRec, null);
            retBool = true;
        }
    }
    return retBool;
}
//[0]隠し値群----
//////// htmlを用いた値の動的管理---- ////////

//////// ----メッセージ出力用ファンクション ////////
function getSomeMessage(strTextId,varDataResource,tmplLocation){
    var strRetMsgBody;
    var strText;
    var defaultTmpl;
    var msgTmpl = {};
    
    if( typeof tmplLocation != "string" ){
        tmplLocation = 'messageTemplate';
    }
    var objTmplDiv = document.getElementById(tmplLocation);
    if( typeof objTmplDiv === "undefined" ){
        //----タグが見つからなかった
        //タグが見つからなかった----
    }
    else{
        var dataStream = objTmplDiv.innerHTML;
        //----区切りパターンを発見して、分割。
        var markpos = dataStream.indexOf(";");
        if( dataStream.length != 0 || markpos != -1 ){
            var sepaMark = dataStream.substring(0,markpos+1);
            var arySet = dataStream.split(sepaMark);
            for(var miFnv1=1; miFnv1<arySet.length;miFnv1++){
                var tmpVal = arySet[miFnv1];
                if( typeof tmpVal !== "undefined" ){
                    var tmpAry = tmpVal.split(":");
                    
                    if( typeof tmpAry[1] !== "undefined" ){
                        var tmpStr = '';
                        for(var miFnv2 = 0; miFnv2 < tmpAry.length - 1; miFnv2 ++ ){
                            tmpStr =  tmpStr + tmpAry[miFnv2 + 1];
                        }
                        msgTmpl[tmpAry[0]] =  tmpStr;
                    }
                }
            }
        }
        //区切りパターンを発見して、分割。----
    }
    strText = msgTmpl[strTextId];
    
    if( typeof strText === "undefined" ){
        var  strRetMsgBody = 'Message id is not found.(Called-ID['+strTextId+'])';
    }else{
        var aryTmplText = strText.split("{}");
        if( aryTmplText.length == 1 ){
            //----埋め込み領域なし（そのまま返す）
            strRetMsgBody = strText;
            //埋め込み領域なし（そのまま返す）----
        }
        else{
            //----埋め込み領域あり
            if( ! varDataResource instanceof Array){
                if(typeof varDataResource === "string" ){
                    varDataResource = {0:varDataResource};
                }
                else{
                    varDataResource = {0:""};
                }
            }
            //埋め込み領域あり----
            strRetMsgBody = aryTmplText[0];
            for(fnv1=0; fnv1<=aryTmplText.length-1; fnv1++){
                if(typeof varDataResource[fnv1] === "undefined" ){
                    if( 0 < fnv1 ){
                        strRetMsgBody = strRetMsgBody + aryTmplText[fnv1];
                    }
                }
                else{
                    if( 0 < fnv1 ){
                        strRetMsgBody = strRetMsgBody + aryTmplText[fnv1] + varDataResource[fnv1];
                    }
                    else{
                        strRetMsgBody = strRetMsgBody + varDataResource[fnv1];
                    }
                }
            }
        }
    }
    return strRetMsgBody;
}
//////// メッセージ出力用ファンクション---- ////////

//////// ----区切り管理用ファンクション ////////

//----クライアントからの送信用
//----動的区切り作成用
function makeAjaxProxyRequestStream(aryRequestElement){
    var strSafeSepa = makeSafeSeparator(aryRequestElement);
    return strSafeSepa + aryRequestElement.join(strSafeSepa);
}

function makeSafeSeparator(strStream, strEscAfterHead, strTailMark, boolRandom){
    var strForbiddenStr='';
    var boolEscRequire=false;
    var randomNum='';
    var aryForCheck;
    if( typeof strStream == "string" ){
        aryForCheck = new Array(strStream);
    }
    else{
        aryForCheck = strStream;
    }
    if( typeof strEscAfterHead != "string" ){
        strEscAfterHead = "ss";
    }
    if( typeof strTailMark != "string" ){
        strTailMark = ";";
    }
    if( typeof boolRandom != "boolean" ){
        boolRandom = false;
    }
   
    if( boolRandom===true ){
        randomNum = Math.floor( Math.random() ) * 256 + 1;
    }
    var strForbiddenStr = strEscAfterHead + randomNum + strTailMark;
    for(var fvn1=0; fvn1 < aryForCheck.length; fvn1++){
        var checkDataSourceBody = aryForCheck[fvn1];
        if( checkDataSourceBody.indexOf(strForbiddenStr, 0) != -1 ){
            //----含まれていた場合
            boolEscRequire=true;
            break;
            //含まれていた場合----
        }
    }
    if( boolEscRequire===true ){
        var searchCount=0;
        var varSearchResult;
        var strSearchPattern;
        do
        {
            searchCount = searchCount + 1;
            strSearchPattern= strEscAfterHead + randomNum + searchCount + strTailMark;
            for(var fvn1=0; fvn1 < aryForCheck.length; fvn1++){
                var checkDataSourceBody = aryForCheck[fvn1];
                varSearchResult = checkDataSourceBody.indexOf(strSearchPattern, 0);
                if( varSearchResult!= -1 ){
                    //----含まれていた
                    break;
                }
            }
        } while(varSearchResult!=-1);
        strForbiddenStr = strSearchPattern;
    }
    return strForbiddenStr;
}
//動的区切り作成用----
//クライアントからの送信用----

//----クライアントでの受信解析用
//----動的区切り解析用
function getArrayBySafeSeparator(strStream, strTailMark){
    var retVar;
    if( typeof strTailMark != "string" ){
        strTailMark = ";";
    }
    var varPos = strStream.indexOf(strTailMark, 0);
    if( varPos != - 1 ){
        var strSepa = strStream.substring(0,varPos + strTailMark.length);
        var strData = strStream.substring(varPos + strTailMark.length ,strStream.length + varPos + strTailMark.length);
        retVar = strData.split(strSepa);
    }
    else{
        retVar = strStream;
    }
    return retVar;
}
//動的区切り解析用----
//クライアントでの受信解析用----

//////// 区切り管理用ファンクション---- ////////

//////// 業務色のないファンクション---- ////////
