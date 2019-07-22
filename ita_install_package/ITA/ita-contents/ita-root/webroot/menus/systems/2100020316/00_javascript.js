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
function arrayShow(objElements,strDebugPointName){
    if( typeof objElements === "undefined" ){
        strDebugPointName = '[NoNamePoint]';
    }

    if( typeof objElements === "undefined" ){
         alert('[PointName]'+strDebugPointName+'(undefined\n\n)undefined');
    }else if( typeof objElements === "string" ){
        alert('[PointName]'+strDebugPointName+'(string)\n\n'+objElements);
    }else if( typeof objElements === "object" ){
        if( objElements instanceof Array){
            for(var fnv1 = 0; fnv1 < objElements.length; fnv1 ++ ){
                alert('[PointName]'+strDebugPointName+'(object-array-instance)[Index]' + fnv1 + '\n\n'+objElements[fnv1]);
            }
        }else{
            alert('[PointName]'+strDebugPointName+'[Object]');
        }
    }
}

function callback() {}
callback.prototype = {  
    Filter1Tbl_add_selectbox : function( result ){
        var filterAreaWrap = 'Filter1_Nakami';
        var strFilterPrintId = 'Filter1Tbl';
        var containerClassName = 'fakeContainer_Filter1Setting';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultSetTargetSeq = ary_result[2];
        var resultContentTag = ary_result[3];

        var objHtmlSetArea = $('#'+filterAreaWrap+' .'+resultSetTargetSeq).get()[0];

        if( objHtmlSetArea === null ){
            htmlSetExcute = false;
        }else{
            if( ary_result[0] != "000" ){
                htmlSetExcute = false;
                errMsgBody = ary_result[2];
            }
        }

        if( htmlSetExcute == true ){
            //----生成されたセレクトタグ、を埋め込み
            $(objHtmlSetArea).html(resultContentTag);
            //生成されたセレクトタグ、を埋め込み----

            addPullDownBox(filterAreaWrap, strFilterPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter1Tbl_reload : function( result ){
        var filterAreaWrap = 'Filter1_Nakami';
        var strFilterPrintId = 'Filter1Tbl';

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objTableArea=$('#'+filterAreaWrap+' .table_area').get()[0];

        if( objTableArea === null){
            htmlSetExcute = false;
        }else{
            if( ary_result[0] != "000" ){
                htmlSetExcute = false;
                errMsgBody = ary_result[2];
            }
        }

        if( htmlSetExcute == true ){
            objTableArea.innerHTML = resultContentTag;
            adjustTableAuto (strFilterPrintId,
                   "sDefault",
                   "fakeContainer_Filter1Setting",
                   webStdTableHeight,
                   webStdTableWidth );
            linkDateInputHelper(filterAreaWrap);
            if( ary_result[3]==1 ){
                Filter1Tbl_reset_filter(true);
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);

        if( filter_on == false ){
            filter_on = true;
            if(initialFilter == 1){
                Filter1Tbl_search_async('orderFromFilterCmdBtn');
            }
        }
    },
    Filter1Tbl_recCount : function(result){
        var strMixOuterFrameName = 'Mix1_Nakami';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        if( ary_result[0] == "000" ){
            if( ckRangeOfAlert(ary_result[2], webPrintRowLimit) ){
                window.alert(getSomeMessage("ITAWDCC90103",{0:webPrintRowLimit,1:ary_result[2]}));
                // Web表を表示しない
                Filter1Tbl_print_async(0);
            }else{
                if( ckRangeOfConfirm(ary_result[2] , webPrintRowConfirm, webPrintRowLimit) ){
                    if( window.confirm( getSomeMessage("ITAWDCC20201",{0:ary_result[2]})) ){
                        // Web表を表示する
                        Filter1Tbl_print_async(1);
                    }else{
                        // Web表を表示しない
                        Filter1Tbl_print_async(0);
                    }
                }else{
                    // Web表を表示する
                    Filter1Tbl_print_async(1);
                }
            }
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Filter1Tbl_printTable : function(result){
        var strMixOuterFrameName = 'Mix1_Nakami';
        var strMixInnerFramePrefix = 'Mix1_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        var objPrintArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

        if( ary_result[0] == "000" ){

            objPrintArea.innerHTML = resultContentTag;

            adjustTableAuto (strMixInnerFramePrefix+'1',
                            "sDefault",
                            "fakeContainer_Filter1Print",
                            webStdTableHeight,
                            webStdTableWidth );
            adjustTableAuto (strMixInnerFramePrefix+'2',
                            "sDefault",
                            "fakeContainer_ND_Filter1Sub",
                            webStdTableHeight,
                            webStdTableWidth );
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = ary_result[2];
            objAlertArea.style.display = "block";
            objPrintArea.innerHTML = "";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix1_1_updateTable : function( result ){
        var strMixOuterFrameName = 'Mix1_Nakami';
        var strMixInnerFramePrefix = 'Mix1_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];

        if( ary_result[0] == "000" ){

            var objUpdateArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

            switch( ary_result[1] ){
                case "200":
                    // エラーなく更新完了
                case "100":
                    window.alert(ary_result[2]);
                    objUpdateArea.innerHTML = "";
                    Filter1Tbl_search_async();
                    break;
                default:
                    objUpdateArea.innerHTML="";
                    $(objUpdateArea).html(resultContentTag);
                    adjustTableAuto (strMixInnerFramePrefix+'1',
                                    "sDefault",
                                    "fakeContainer_Update1",
                                    webStdTableHeight,
                                    webStdTableWidth );
                    
                    linkDateInputHelper(strMixOuterFrameName);

                    textPrintToBoxes('11','Mix1_1_7');
                    textPrintToBoxes('11','Mix1_1_10');

            }
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            setInputButtonDisable(strMixOuterFrameName,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var objUpdateArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];
            objUpdateArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix1_1_deleteTable : function( result ){
        var strMixOuterFrameName = 'Mix1_Nakami';
        var strMixInnerFramePrefix = 'Mix1_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];

        if( ary_result[0] == "000" ){

            var objDeleteArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

            switch( ary_result[1] ){
                case "210":
                    // エラーなく廃止完了
                case "200":
                    // エラーなく復活完了
                case "100":
                    window.alert(resultContentTag);
                    objDeleteArea.innerHTML = "";
                    Filter1Tbl_search_async();
                    break;
                default:
                    objDeleteArea.innerHTML="";
                    objDeleteArea.insertAdjacentHTML("beforeend",resultContentTag);
                    adjustTableAuto (strMixInnerFramePrefix+'1',
                                    "sDefault",
                                    "fakeContainer_Delete1",
                                    webStdTableHeight,
                                    webStdTableWidth );
            }
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            setInputButtonDisable(strMixOuterFrameName,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var objDeleteArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];
            objDeleteArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }

        showForDeveloper(result);
    },
    Mix2_1_registerTable : function( result ){
        var strMixOuterFrameName = 'Mix2_Nakami';
        var strMixInnerFramePrefix = 'Mix2_';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];

        if( ary_result[0] == "000" ){

            var objRegiterArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

            switch( ary_result[1] ){
                case "100":
                    window.alert(resultContentTag);
                    objRegiterArea.innerHTML = "";
                    Filter1Tbl_search_async();
                    break;
                case "201":
                    // エラーなく登録完了
                default:                
                    objRegiterArea.innerHTML="";
                    $(objRegiterArea).html(resultContentTag);

                    objAlertArea.style.display = "none";
                    adjustTableAuto (strMixInnerFramePrefix+'1',
                                    "sDefault",
                                    "fakeContainer_Register2",
                                    webStdTableHeight,
                                    webStdTableWidth );
                    linkDateInputHelper(strMixOuterFrameName);

                    textPrintToBoxes('11','Mix1_1_7');
                    textPrintToBoxes('11','Mix1_1_10');

            }
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            setInputButtonDisable(strMixOuterFrameName,'disableAfterPush',false);
        }else if( ary_result[0] == "003" ){
            var objRegiterArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];
            objRegiterArea.innerHTML="";
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }

        showForDeveloper(result);
    },
    Journal1Tbl_printJournal : function( result ){
        var strMixOuterFrameName = 'Journal1_Nakami';
        var strMixInnerFrame = 'Journal1Tbl';

        var ary_result = getArrayBySafeSeparator(result);
        checkTypicalFlagInHADACResult(ary_result);

        var resultContentTag = ary_result[2];

        var objAlertArea=$('#'+strMixOuterFrameName+' .alert_area').get()[0];
        objAlertArea.style.display = "none";

        var objPrintArea=$('#'+strMixOuterFrameName+' .table_area').get()[0];

        if( ary_result[0] == "000" ){

            objPrintArea.innerHTML = resultContentTag;

            adjustTableAuto (strMixInnerFrame,
                            "sDefault",
                            "fakeContainer_Journal1Print",
                            webStdTableHeight,
                            webStdTableWidth );
        }else if( ary_result[0] == "002" ){
            window.alert(getSomeMessage("ITAWDCC90102"));
            objAlertArea.innerHTML = resultContentTag;
            objAlertArea.style.display = "block";
            objPrintArea.innerHTML = "";
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //---- ここからカスタマイズした場合の[callback]メソッド配置域
    //----メニュー
    Mix1_1_menu_upd : function( result ){
        var tableTagAreaWrap = 'Mix1_Nakami';
        var strTableTagPrintId = 'Mix1_1';
        var containerClassName = 'fakeContainer_Update1';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';


        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];
            
            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];
            
            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix2_1_menu_reg : function( result ){
        var tableTagAreaWrap = 'Mix2_Nakami';
        var strTableTagPrintId = 'Mix2_1';
        var containerClassName = 'fakeContainer_Register2';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];
            
            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];
            
            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //メニュー----

    //----作業パターン
    Mix1_1_pattern_upd : function( result ){
        var tableTagAreaWrap = 'Mix1_Nakami';
        var strTableTagPrintId = 'Mix1_1';
        var containerClassName = 'fakeContainer_Update1';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);

            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);

            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];

            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];

            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

            var ary_result02 = getArrayBySafeSeparator(ary_element[1]);

            var resultSetTargetSeq = ary_result02[0];
            var resultContentTag = ary_result02[1];

            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];

            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix2_1_pattern_reg : function( result ){
        var tableTagAreaWrap = 'Mix2_Nakami';
        var strTableTagPrintId = 'Mix2_1';
        var containerClassName = 'fakeContainer_Register2';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);

            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);

            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];

            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];

            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

            var ary_result02 = getArrayBySafeSeparator(ary_element[1]);

            var resultSetTargetSeq = ary_result02[0];
            var resultContentTag = ary_result02[1];

            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];

            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //作業パターン----

    //----Value変数 更新
    Mix1_1_vars_upd : function( result ){
        var tableTagAreaWrap = 'Mix1_Nakami';
        var strTableTagPrintId = 'Mix1_1';
        var containerClassName = 'fakeContainer_Update1';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];
            
            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];
            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

            if( ary_result01[2] == "NORMAL_VAR_1"){
                textPrintToBoxes('2','Mix1_1_10');
            }
            else{
                textPrintToBoxes('1','Mix1_1_10');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //----Value変数 登録
    Mix2_1_vars_reg : function( result ){
        var tableTagAreaWrap = 'Mix2_Nakami';
        var strTableTagPrintId = 'Mix2_1';
        var containerClassName = 'fakeContainer_Register2';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];
            
            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];
            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

            if( ary_result01[2] == "NORMAL_VAR_1"){
                textPrintToBoxes('2','Mix2_1_10');
            }
            else{
                textPrintToBoxes('1','Mix2_1_10');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //変数名----

    //----Key変数 更新
    Mix1_1_key_vars_upd : function( result ){
        var tableTagAreaWrap = 'Mix1_Nakami';
        var strTableTagPrintId = 'Mix1_1';
        var containerClassName = 'fakeContainer_Update1';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];
            
            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];
            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

            if( ary_result01[2] == "NORMAL_VAR_1"){
                textPrintToBoxes('2','Mix1_1_7');
            }
            else{
                textPrintToBoxes('1','Mix1_1_7');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //----Key変数 登録
    Mix2_1_key_vars_reg : function( result ){
        var tableTagAreaWrap = 'Mix2_Nakami';
        var strTableTagPrintId = 'Mix2_1';
        var containerClassName = 'fakeContainer_Register2';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);

        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];
            
            var objHtmlSetArea = $('#'+tableTagAreaWrap+' .'+resultSetTargetSeq).get()[0];
            $(objHtmlSetArea).html(resultContentTag);
            addPullDownBox(tableTagAreaWrap, strTableTagPrintId, intMaxWidth, resultSetTargetSeq, containerClassName);

            if( ary_result01[2] == "NORMAL_VAR_1"){
                textPrintToBoxes('2','Mix2_1_7');
            }
            else{
                textPrintToBoxes('1','Mix2_1_7');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //カラム 変数名----


    //----メンバー変数名
    Mix1_1_val_chlVar_upd : function( result ){
        var tableTagAreaWrap = 'Mix1_Nakami';
        var strTableTagPrintId = 'Mix1_1';
        var containerClassName = 'fakeContainer_Update1';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);


        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];

            if( ary_result01[2] == "MEMBER_VAR_0" ){
                textPrintToBoxes('1','Mix1_1_10');
            }
            else if( ary_result01[2] == "MEMBER_VAR_1"){
                textPrintToBoxes('2','Mix1_1_10');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix2_1_val_chlVar_reg : function( result ){
        var tableTagAreaWrap = 'Mix2_Nakami';
        var strTableTagPrintId = 'Mix2_1';
        var containerClassName = 'fakeContainer_Register2';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);


        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];

            if( ary_result01[2] == "MEMBER_VAR_0" ){
                textPrintToBoxes('1','Mix2_1_10');
            }
            else if( ary_result01[2] == "MEMBER_VAR_1"){
                textPrintToBoxes('2','Mix2_1_10');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix1_1_key_chlVar_upd : function( result ){
        var tableTagAreaWrap = 'Mix1_Nakami';
        var strTableTagPrintId = 'Mix1_1';
        var containerClassName = 'fakeContainer_Update1';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);


        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];

            if( ary_result01[2] == "MEMBER_VAR_0" ){
                textPrintToBoxes('1','Mix1_1_7');
            }
            else if( ary_result01[2] == "MEMBER_VAR_1"){
                textPrintToBoxes('2','Mix1_1_7');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    Mix2_1_key_chlVar_reg : function( result ){
        var tableTagAreaWrap = 'Mix2_Nakami';
        var strTableTagPrintId = 'Mix2_1';
        var containerClassName = 'fakeContainer_Register2';

        var intMaxWidth = 650;

        var htmlSetExcute = true;
        var errMsgBody = '';

        var ary_result = getArrayBySafeSeparator(result);

        checkTypicalFlagInHADACResult(ary_result);


        if( ary_result[0] == "000" ){
            var ary_element = getArrayBySafeSeparator(ary_result[2]);
            var ary_result01 = getArrayBySafeSeparator(ary_element[0]);
            var resultSetTargetSeq = ary_result01[0];
            var resultContentTag = ary_result01[1];

            if( ary_result01[2] == "MEMBER_VAR_0" ){
                textPrintToBoxes('1','Mix2_1_7');
            }
            else if( ary_result01[2] == "MEMBER_VAR_1"){
                textPrintToBoxes('2','Mix2_1_7');
            }
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    }
    //メンバー変数名----

    // ここまでカスタマイズした場合の[callback]メソッド配置域----
}

//////// テーブルレイアウト設定 ////////
var pageType;
var privilege;
var initialFilterEl;
var initialFilter;
var webPrintRowLimit;
var webPrintRowConfirm;
var webStdTableWidth;
var webStdTableHeight;
var msgTmpl = {};
//////// 画面生成時に初回実行する処理 ////////

var proxy = new Db_Access(new callback());
var filter_on = false;

window.onload = function(){
    var filter1AreaWrap = 'Filter1_Nakami';
    pageType = document.getElementById('pageType').innerHTML;
    privilege = parseInt(document.getElementById('privilege').innerHTML);
    initialFilterEl = document.getElementById('sysInitialFilter');
    if(initialFilterEl == null){
        initialFilter = 2;
    }
    else{
        initialFilter = initialFilterEl.innerHTML;
    }
    webPrintRowConfirm = parseInt(document.getElementById('sysWebRowConfirm').innerHTML);
    webPrintRowLimit = parseInt(document.getElementById('sysWebRowLimit').innerHTML);
    webStdTableWidth = document.getElementById('webStdTableWidth').innerHTML;
    webStdTableHeight = document.getElementById('webStdTableHeight').innerHTML;
    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+filter1AreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    proxy.Filter1Tbl_reload(0);
// ----サイト個別、事前処理

// サイト個別、事前処理----

    // テーブル表示用領域に初期メッセ時を表示しておく
    //----※ここに一覧が表示されます。
    document.getElementById('table_area').innerHTML = getSomeMessage("ITAWDCC10101");

    if(privilege != 2){
        // 登録の初期HTMLを表示する
        show('Mix2_Midashi' ,'Mix2_Nakami'  );
        Mix2_1_register_async(0);
    }

// ----サイト個別、事前処理
// サイト個別、事前処理----


    show('SetsumeiMidashi'      ,'SetsumeiNakami'       );
    show('Mix1_Midashi'         ,'Mix1_Nakami'          );
    show('AllDumpMidashi'       ,'AllDumpNakami'        );
    show('Journal1_Midashi'     ,'Journal1_Nakami'      );

// ----サイト個別メニュー、ここから
// サイト個別メニュー、ここまで----

}

//////// コールバックファンクション---- ////////

//////// ----セレクトタグ追加ファンクション ////////
function Filter1Tbl_add_selectbox( show_seq ){
    proxy.Filter1Tbl_add_selectbox(show_seq);
}
//////// セレクトタグ追加ファンクション---- ////////

//////// ----表示フィルタリセット用ファンクション ////////
function Filter1Tbl_reset_filter(boolBack){
    // 検索条件をクリア(リセット)
    var filterAreaWrap = 'Filter1_Nakami';
    var strMixOuterFrameName = 'Mix1_Nakami';
    if( boolBack===true ){
        var objHyoujiFlag = $('#'+strMixOuterFrameName+' .hyouji_flag').get()[0];
        if( objHyoujiFlag != null ){
            // すでに一覧が表示されている場合（オートフィルタがonの場合、一覧を最新化する）
            var objFCSL = $('#'+filterAreaWrap+' .filter_ctl_start_limit').get()[0];
            if( objFCSL == null){
            }else{
                if( objFCSL.value == 'on' && objFCSL.checked == true ){
                    // タグが存在し、オートフィルタにチェックが入っている
                    //----再表示しますか？
                    if( window.confirm( getSomeMessage("ITAWDCC20204")) ){
                        Filter1Tbl_search_async();
                    }
                }
            }
        }
    }else{
        proxy.Filter1Tbl_reload(1);
    }
}
//////// 表示フィルタリセット用ファンクション---- ////////

//////// ----Filter1Tbl_search_asyncを呼ぶかどうか判断するファンクション ////////
function Filter1Tbl_pre_search_async(inputedCode){

    // ----Enterキーが押された場合
    if( inputedCode == 13 ){
        Filter1Tbl_search_async('keyInput13');
    }
    // Enterキーが押された場合----
}
//////// Filter1Tbl_search_asyncを呼ぶかどうか判断するファンクション---- ////////

//////// ----フィルタ結果表示呼出ファンクション[1] ////////
function Filter1Tbl_search_async( value1 ){

    var filterAreaWrap = 'Filter1_Nakami';
    var printAreaWrap = 'Mix1_Nakami';
    var printAreaHead = 'Mix1_Midashi';

    var exec_flag = true;

    // 引数を準備
    var filter_data = $("#"+filterAreaWrap+" :input").serializeArray();

    exec_flag = Filter1Tbl_search_control(exec_flag, value1);
    var objUpdTag = $('#'+printAreaWrap+' .editing_flag').get()[0];
    if ( objUpdTag != null ){
        // 更新系(更新/廃止/復活)モード中の場合はSELECTモードに戻っていいか尋ねる
        if( exec_flag == true ){
            //----メンテナンス中ですが中断してよろしいですか？
            if( !window.confirm( getSomeMessage("ITAWDCC20203") ) ){
                exec_flag = false;
            }
        }
    }

    if( exec_flag ){
        // 更新時アラート出力エリアをブランクにしたうえ非表示にする
        var objAlertArea=$('#'+printAreaWrap+' .alert_area').get()[0];
        objAlertArea.innerHTML = "";
        objAlertArea.style.display = "none";

        // テーブル表示用領域を一旦クリアする
        var objTableArea=$('#'+printAreaWrap+' .table_area').get()[0];
        //----※ここに一覧が表示されます。
        objTableArea.innerHTML = "";

        // テーブル表示用領域を開く
        if( checkOpenNow(printAreaWrap)===false ){
            show(printAreaHead, printAreaWrap);
        }

        // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
        restruct_for_IE();

        // proxy.Filter1Tbl_recCount実行
        proxy.Filter1Tbl_recCount(filter_data);
    }
}
//////// フィルタ結果表示呼出ファンクション[1]---- ////////

//////// ----フィルタ結果表示呼出ファンクション[2] ////////
function Filter1Tbl_search_control( exec_flag_var, value1 ){
    var filterAreaWrap = 'Filter1_Nakami';

    var exec_flag_ret = true;

    if( typeof(value1) == 'undefined' ){
        // value1がundefined型の場合
        exec_flag_ret = exec_flag_var;
    }else{
        if( exec_flag_var == false ){
            exec_flag_ret = false;
        }else{
            var objFCSL = $('#'+filterAreaWrap+' .filter_ctl_start_limit').get()[0];

            if(objFCSL == null){
                // 自動開始制御タグがない場合は、システムエラー扱い、とする。
                // システムエラーが発生しました。
                alert( getSomeMessage("ITAWDCC20205") );
                exit;
            }else{
                if( objFCSL.value == 'on' ){
                    // 自動開始制御タグが存在し、オートフィルタ開始の抑制が働いている可能性がある
                    exec_flag_ret = false;
                    if( value1 == 'orderFromFilterCmdBtn' ){
                        // フィルタボタンが押された場合、条件「なし」で開始----
                        exec_flag_ret = true;
                    }else if( value1 == 'idcolumn_filter_default' || value1 == 'keyInput13' ){
                        if( objFCSL.checked == true ){
                            // 自動開始制御タグが存在し、オートフィルタにチェックが入っている
                            exec_flag_ret = true;
                        }
                    }else{
                        exec_flag_ret = true;
                    }
                }
            }
        }
    }
    return exec_flag_ret;
}
//////// フィルタ結果表示呼出ファンクション[2]---- ////////

//////// ----検索条件指定用ファンクション ////////
function Filter1Tbl_print_async( intPrintMode ){

    var filterAreaWrap = 'Filter1_Nakami';
    var printAreaWrap = 'Mix1_Nakami';
    var printAreaHead = 'Mix1_Midashi';

    var filter_data=$('#'+filterAreaWrap+' :input').serializeArray();

    // テーブル表示用領域を開く
    if( checkOpenNow(printAreaWrap)===false ){
        show(printAreaHead, printAreaWrap);
    }

    // しばらくお待ち下さいを出す
    var objTableArea = $('#'+printAreaWrap+' .table_area').get()[0];
    objTableArea.innerHTML = "<div class=\"wait_msg\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

    // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
    restruct_for_IE();

    // proxy.Filter1Tbl_printTable実行
    proxy.Filter1Tbl_printTable(intPrintMode, filter_data);
}
//////// 検索条件指定用ファンクション---- ////////

//////// ----登録初期画面に戻るかどうか判定するファンクション ////////
function Mix2_1_pre_register_async( mode ){

    //----登録中ですが中断してよろしいですか？
    if( window.confirm( getSomeMessage("ITAWDCC20202")) ){
        Mix2_1_register_async(0);
    }

}
//////// 登録初期画面に戻るかどうか判定するファンクション---- ////////

//////// ----登録画面遷移用ファンクション ////////
function Mix2_1_register_async( mode ){

    var registerAreaWrap = 'Mix2_Nakami';

    // アラート用エリアを初期化
    var objAlertArea = $('#'+registerAreaWrap+' .alert_area').get()[0];
    objAlertArea.innerHTML = '';
    objAlertArea.style.display = "none";

    // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
    restruct_for_IE();

    // アラートメッセージ格納変数を初期化
    var alt_str = '';

    // registerTableファンクション呼び出し要否フラグ
    var exec_flag = true;

    // モードによって動きを決定
    switch( mode ){
        case 0 :
            // 初期画面(mode=0)
            // 引数準備必要なし
            break;
        case 1 :
            // 登録フォーム画面(mode=1)
            // 引数準備必要なし
            break;
        case 2 :
            // 登録実行処理＆結果画面(mode=2)
            // 登録時のチェック
            //----登録を実行してよろしいですか？
            if( window.confirm(getSomeMessage("ITAWDCC20101")) == false ){
                exec_flag = false;
            }else{
                setInputButtonDisable(registerAreaWrap,'disableAfterPush',true);
            }
            break;
    }

    if( exec_flag ){
        // proxy.registerTable実行
        var registerData = $('#'+registerAreaWrap+' :input').serializeArray();
        proxy.Mix2_1_registerTable(mode, registerData);
    }
}
//////// 登録画面遷移用ファンクション---- ////////

//////// ----更新画面遷移用ファンクション ////////
function Mix1_1_update_async( mode, inner_seq, updateAreaName ){

    var updateAreaWrap = 'Mix1_Nakami';

    // アラートメッセージ格納変数を初期化
    var alt_str = '';
    // updateTableファンクション呼び出し要否フラグ
    var exec_flag = true;
    // モードによって動きを決定

    switch( mode ){
        case 1 :
            // 更新画面に遷移(mode=1)
            // アラート用エリアを初期化
            var objAlertArea = $('#'+updateAreaWrap+' .alert_area').get()[0];
            objAlertArea.innerHTML = '';
            objAlertArea.style.display = "none";

            // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
            restruct_for_IE();

            break;
        case 2 :
            // 更新画面から一覧に戻る(mode=2)
            // 呼び出し要否フラグをOFF
            exec_flag = false;

            // Filter1Tbl_search_asyncを呼び出し
            Filter1Tbl_search_async();

            break;
        case 3 :
            // 更新画面で実行を押下(mode=3)
            //----更新を実行してよろしいですか？
            if( window.confirm( getSomeMessage("ITAWDCC20102") ) ){
                // アラート用エリアを初期化
                var objAlertArea = $('#'+updateAreaWrap+' .alert_area').get()[0];
                objAlertArea.innerHTML = '';
                objAlertArea.style.display = "none";
                setInputButtonDisable(updateAreaWrap,'disableAfterPush',true);
                // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
                restruct_for_IE();
            }else{
                exec_flag = false;
            }
            break;
    }

    if(exec_flag){
        var updateData = $('#'+updateAreaWrap+' :input').serializeArray();
        //proxy.updateTable実行
        proxy.Mix1_1_updateTable( mode, inner_seq, updateData);
    }
}
//////// 更新画面遷移用ファンクション---- ////////

//////// ----削除画面遷移用ファンクション ////////
function Mix1_1_delete_async( mode, inner_seq ){

    var deleteAreaWrap = 'Mix1_Nakami';

    // アラートメッセージ格納変数を初期化
    var alt_str = '';

    // deleteTableファンクション呼び出し要否フラグ
    var exec_flag = true;

    // モードによって動きを決定
    switch( mode ){
        case 1 :
            // 廃止画面に遷移(mode=1)
            // アラート用エリアを初期化
            var objAlertArea = $('#'+deleteAreaWrap+' .alert_area').get()[0];
            objAlertArea.innerHTML = '';
            objAlertArea.style.display = "none";

            // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
            restruct_for_IE();

            break;
        case 2 :
            // 廃止画面から一覧に戻る(mode=2)
            // 呼び出し要否フラグをOFF
            exec_flag = false;

            // Filter1Tbl_search_asyncを呼び出し
            Filter1Tbl_search_async();

            break;
        case 3 :
            // 廃止画面で実行を押下(mode=3)
            //----廃止してよろしいですか？
            if( window.confirm( getSomeMessage("ITAWDCC20103") ) ){
                // アラート用エリアを初期化
                var objAlertArea = $('#'+deleteAreaWrap+' .alert_area').get()[0];
                objAlertArea.innerHTML = '';
                objAlertArea.style.display = "none";
                setInputButtonDisable(deleteAreaWrap,'disableAfterPush',true);
                // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
                restruct_for_IE();

            }else{
                exec_flag = false;
            }

            break;
        case 4 :
            // 復活画面に遷移(mode=4)
            // アラート用エリアを初期化
            var objAlertArea = $('#'+deleteAreaWrap+' .alert_area').get()[0];
            objAlertArea.innerHTML = '';
            objAlertArea.style.display = "none";

            // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
            restruct_for_IE();

            break;
        case 5 :
            // 復活画面で実行を押下(mode=5)
            //----復活してよろしいですか？
            if( window.confirm( getSomeMessage("ITAWDCC20104") ) ){
                // アラート用エリアを初期化
                var objAlertArea = $('#'+deleteAreaWrap+' .alert_area').get()[0];
                objAlertArea.innerHTML = '';
                objAlertArea.style.display = "none";
                setInputButtonDisable(deleteAreaWrap,'disableAfterPush',true);

                // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
                restruct_for_IE();

            }else{
                exec_flag = false;
            }
            break;
    }

    if(exec_flag){
        var updateData = $('#'+deleteAreaWrap+' :input').serializeArray();
        // proxy.deleteTable実行
        proxy.Mix1_1_deleteTable(mode, inner_seq, updateData);
    }
}
//////// 削除画面遷移用ファンクション---- ////////

//////// ----履歴検索条件クリア(リセット)用ファンクション ////////
function Journal1Tbl_reset_query(){
    var journal1AreaWrap = 'Journal1_Nakami';
    // 検索条件をクリア(リセット)
    $('#'+journal1AreaWrap+' :input:not(:button)').each(function(){this.value=""});
}
//////// 履歴検索条件クリア(リセット)用ファンクション---- ////////

//////// ----search_journal_asyncを呼ぶかどうか判断するファンクション ////////
function Journal1Tbl_pre_search_async(inputedCode){
    if( inputedCode == 13 ){
        Journal1Tbl_search_async();
    }
}
//////// search_journal_asyncを呼ぶかどうか判断するファンクション---- ////////

//////// ----履歴検索条件指定用ファンクション ////////
function Journal1Tbl_search_async(){
    // 履歴検索実施フラグを初期化
    var journal1AreaWrap = 'Journal1_Nakami';
    
    var exec_flag = true;

    // 検索実施フラグがtrueの場合は検索実施
    if( exec_flag == true ){
        // しばらくお待ち下さいを出す
        var objTableArea = $('#'+journal1AreaWrap+' .table_area').get()[0];
        objTableArea.innerHTML = "<div class=\"wait_msg2\" >"+getSomeMessage("ITAWDCC10102")+"</div>";

        var filterData = $('#'+journal1AreaWrap+' :input:not(:button)').serializeArray();
        proxy.Journal1Tbl_printJournal(filterData);
    }
}
//////// 履歴検索条件指定用ファンクション---- ////////

//////// ----汎用系ファンクション ////////
function setInputButtonDisable(rangeId,targetClass,toValue){
    if(toValue === true){
        $('#'+rangeId+' .'+targetClass).attr("disabled",true);
    }else{
        $('#'+rangeId+' .'+targetClass).removeAttr("disabled");
    }
}
//////// 汎用系ファンクション---- ////////

//---- ここからカスタマイズした場合の一般メソッド配置域
function Mix1_1_menu_upd(){
    var rangeId = 'Mix1_1';
    
    var objOpe = document.getElementById('Mix1_1_1');
    proxy.Mix1_1_menu_upd(objOpe.value);
}
function Mix2_1_menu_reg(){
    var rangeId = 'Mix2_1';
    
    var objOpe = document.getElementById('Mix2_1_1');
    proxy.Mix2_1_menu_reg(objOpe.value);
}

function Mix1_1_pattern_upd(){
    // すべての後選択関連カラムを消す
    proxy.Mix1_1_key_vars_upd('');
    textPrintToBoxes('10','Mix1_1_7');
    proxy.Mix1_1_vars_upd('');
    textPrintToBoxes('10','Mix1_1_10');

    var rangeId = 'Mix1_1';
    
    var objPattern = document.getElementById('Mix1_1_4');
    proxy.Mix1_1_pattern_upd(objPattern.value);
}
function Mix2_1_pattern_reg(){
    // すべての後選択関連カラムを消す
    proxy.Mix2_1_key_vars_reg('');
    textPrintToBoxes('10','Mix2_1_7');
    proxy.Mix2_1_vars_reg('');
    textPrintToBoxes('10','Mix2_1_10');

    var rangeId = 'Mix2_1';
    
    var objPattern = document.getElementById('Mix2_1_4');
    proxy.Mix2_1_pattern_reg(objPattern.value);
}

function Mix1_1_vars_upd(){
    // すべての後選択関連カラムを消す
    var rangeId = 'Mix1_1';
    
    var objVars = document.getElementById('Mix1_1_8');

    proxy.Mix1_1_vars_upd(objVars.value);
}
function Mix2_1_vars_reg(){
    // すべての後選択関連カラムを消す
    var rangeId = 'Mix2_1';
    
    var objVars = document.getElementById('Mix2_1_8');

    proxy.Mix2_1_vars_reg(objVars.value);
}

function Mix1_1_key_vars_upd(){
    // すべての後選択関連カラムを消す
    var rangeId = 'Mix1_1';
    
    var objVars = document.getElementById('Mix1_1_5');

    proxy.Mix1_1_key_vars_upd(objVars.value);
}
function Mix2_1_key_vars_reg(){
    // すべての後選択関連カラムを消す
    var rangeId = 'Mix2_1';
    
    var objVars = document.getElementById('Mix2_1_5');

    proxy.Mix2_1_key_vars_reg(objVars.value);
}



function Mix1_1_val_chlVar_upd(){
    var rangeId = 'Mix1_1';
    
    var objVar    = document.getElementById('Mix1_1_8'); //変数名
    var objVar_val = '';
    if( objVar ){
        objVar_val = objVar.value;
    }

    var objChlVar = document.getElementById('Mix1_1_9'); //メンバー変数名
    var objChlVar_val = '';
    if( objChlVar ){
        objChlVar_val = objChlVar.value;
    }

    proxy.Mix1_1_val_chlVar_upd(objVar_val, objChlVar_val);
}
function Mix2_1_val_chlVar_reg(){
    var rangeId = 'Mix2_1';
    
    var objVar    = document.getElementById('Mix2_1_8'); //変数名
    var objVar_val = '';
    if( objVar ){
        objVar_val = objVar.value;
    }

    var objChlVar = document.getElementById('Mix2_1_9'); //メンバー変数名
    var objChlVar_val = '';
    if( objChlVar ){
        objChlVar_val = objChlVar.value;
    }

    proxy.Mix2_1_val_chlVar_reg(objVar_val, objChlVar_val);
}

function Mix1_1_key_chlVar_upd(){
    var rangeId = 'Mix1_1';
    
    var objVar    = document.getElementById('Mix1_1_5'); //変数名
    var objVar_val = '';
    if( objVar ){
        objVar_val = objVar.value;
    }

    var objChlVar = document.getElementById('Mix1_1_6'); //メンバー変数名
    var objChlVar_val = '';
    if( objChlVar ){
        objChlVar_val = objChlVar.value;
    }

    proxy.Mix1_1_key_chlVar_upd(objVar_val, objChlVar_val);
}
function Mix2_1_key_chlVar_reg(){
    var rangeId = 'Mix2_1';
    
    var objVar    = document.getElementById('Mix2_1_5'); //変数名
    var objVar_val = '';
    if( objVar ){
        objVar_val = objVar.value;
    }

    var objChlVar = document.getElementById('Mix2_1_6'); //メンバー変数名
    var objChlVar_val = '';
    if( objChlVar ){
        objChlVar_val = objChlVar.value;
    }

    proxy.Mix2_1_key_chlVar_reg(objVar_val, objChlVar_val);
}

function textPrintToBoxes(mode,strObjIdOfSw1)
{
    var objVars01 = document.getElementById(strObjIdOfSw1);
    if( objVars01 === null ){

    }else{
        switch (mode){
            case "10":
                //----完全にクリアして、入力欄も隠す
                objVars01.value = "";
                objVars01.type = "hidden";
                
                var objVarsAfter01 = document.getElementById('after_'+strObjIdOfSw1);
                objVarsAfter01.style.display = "none";
                
                break;
            case "11":
                //----変数名に合わせて、処理する
                var objInitVarType01 = document.getElementById('init_var_type_'+strObjIdOfSw1);
                
                if( objInitVarType01.innerHTML != "1" ){
                    //(列順序に入力がある)子変数ではない
                    objVars01.type = "hidden";
                    var objVarsAfter01 = document.getElementById('after_'+strObjIdOfSw1);
                    objVarsAfter01.style.display = "block";
                }
                objInitVarType01.innerHTML = "";
                
                break;
            case "1":
                //通常の変数
                objVars01.value = "";
                objVars01.type = "hidden";
                
                var objVarsAfter01 = document.getElementById('after_'+strObjIdOfSw1);
                objVarsAfter01.style.display = "block";
                
                break;
            case "2":
                //配列子変数 
                objVars01.value = "";
                objVars01.type = "text";
                
                var objVarsAfter01 = document.getElementById('after_'+strObjIdOfSw1);
                objVarsAfter01.style.display = "none";
                
                break;
        }
    }
}
