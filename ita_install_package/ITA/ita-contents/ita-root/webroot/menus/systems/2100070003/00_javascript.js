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
//更新時フィールドのの具体値を示すID
var modalTarget="#Mix1_1_4";
//更新時フィールドのの作業パターンを示すID
var templateTarget="#Mix1_1_2";
var projectTarget="#Mix1_1_3";
//新規登録時フィールドの具体値を表すID
var registModalTarget="#Mix2_1_4";
//新規登録時フィールドのの作業パターンを示すID
var registTemplateTarget="#Mix2_1_2";
var registProjectTarget="#Mix2_1_3";

//////// ----コールバックファンクション ////////
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


            //ITAから出力されたHTMLを操作する。
            //JSON値に関しては整形したものを表示する。
            //本機能は表示部分にのみ作用するもので、データ保存等には影響を与えない。
            //列の番号等は固定値を使用しているので、調整に応じ変更すること。
            var obj=$("#Mix1_1 td[id$='6']");
            for (var i = obj.length - 1; i >= 0; i--) {
                var $list=$(obj[i]);

                var str=$list.text();
                if(str===""){

                }else{
                    str=str.split("'").join("\"");

                    if(isJson(str)){
                        str=JSON.parse(str);

                        $textarea=$("<textarea/>")
                            .text(JSON.stringify(str,null,2))
                            .css({"width":"380"})
                            .attr({"rows":"10","disabled":true});

                        $list.empty()
                            .append($textarea);
                    }
                }
            }

            $(".Mix1_16").attr("width","400");
            $("#Mix1_Nakami").find(".fakeContainer_Filter1Print").css("height","300px");
            $("#Mix1_Nakami").find(".sData").css({"height":"250px","width":"1053px"});

            //対象プロジェクトの欄のIDを、プロジェクト名に置き換える
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

                    //以下、追加実装
                    //具体値の項目は非表示にする
                    $dom=$(modalTarget);
                    $dom.hide();

                    //とりあえず対象のレコードのパターンIDを取得する
                    //リレーションが曖昧なので、何かしらとれたことでOKとする
                    $dom.parent().css({"text-align":"center","padding-top":"10px"});
                    $dom.parent().append("<span class='modalOpen' command='"+modalTarget+"' data=''>"+
                        getSomeMessage("ITAOPENSTC102010")+"</span>");
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

                    //以下、追加実装
                    //具体値の項目は非表示にする
                    $dom=$(registModalTarget);
                    $dom.hide();

                    //とりあえず対象のレコードのパターンIDを取得する
                    //リレーションが曖昧なので、何かしらとれたことでOKとする
                    $dom.parent().css({"text-align":"center","padding-top":"10px"});
                    $dom.parent().append("<span class='modalOpen' command='"+registModalTarget+"' data=''>"+
                        getSomeMessage("ITAOPENSTC102010")+"</span>");

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
    }
    //---- ここからカスタマイズした場合の[callback]メソッド配置域
    ,
    //----オペレーション
    Mix1_1_operation_upd : function( result ){
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
    Mix2_1_operation_reg : function( result ){
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
    }
    //オペレーション----
    ,
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
        }else{
            window.alert(getSomeMessage("ITAWDCC90101"));
        }
        showForDeveloper(result);
    },
    //作業パターン----

    // ここまでカスタマイズした場合の[callback]メソッド配置域----

    getOpenStackMasterData : function( result ){

        if(isJson(result)){
            masterApiResponse=JSON.parse(result);
            if(masterApiResponse.host.length > 0) {
                modalOpen();
            } else{
                alert(getSomeMessage("ITAOPENSTC102020"));
            }
        }else{
            alert(getSomeMessage("ITAOPENSTC102030"));
        }
    },
    validateHeat : function( result ){
        alert(getSomeMessage("ITAOPENSTC102040"));
    }
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


//OpenStackからのマスタデータを格納
var masterApiResponse="";
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
        //objTableArea.innerHTML = getSomeMessage("ITAWDCC10101");
        objTableArea.innerHTML = "";

        // テーブル表示用領域を開く
        //if( document.getElementById(printAreaWrap).style.display == "none" ){
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
    //if( document.getElementById(printAreaWrap).style.display == "none" ){
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

function Mix1_1_operation_upd(){
    // すべての後選択関連カラムを消す
    proxy.Mix1_1_pattern_upd('');

    var rangeId = 'Mix1_1';

    var objOpe = document.getElementById('Mix1_1_1');
    proxy.Mix1_1_operation_upd(objOpe.value);
}
function Mix2_1_operation_reg(){

    // すべての後選択関連カラムを消す
//    proxy.Mix2_1_pattern_reg('');

    var rangeId = 'Mix2_1';

    var objOpe = document.getElementById('Mix2_1_1');
    proxy.Mix2_1_operation_reg(objOpe.value);
}

function Mix1_1_pattern_upd(){
        return;
    // すべての後選択関連カラムを消す
    var rangeId = 'Mix1_1';

    var objPattern = document.getElementById('Mix1_1_2');
    proxy.Mix1_1_pattern_upd(objPattern.value);
}
function Mix2_1_pattern_reg(){
    return;
    // すべての後選択関連カラムを消す
    var rangeId = 'Mix2_1';

    var objPattern = document.getElementById('Mix2_1_2');
    proxy.Mix2_1_pattern_reg(objPattern.value);
}
// ここまでカスタマイズした場合の一般メソッド配置域----


//以下、今回OpenStack用に新規追加した関数群

//値代入モーダルのバリデートエラー時のモーダル
function warningModalOpen(){

    //空値だったら警告モーダルを表示
    $("#comfirmArea").dialog({
        modal:true,
        buttons:[
            {
                text:getSomeMessage("ITAOPENSTC102050"),
                click:function(){
                    $("#comfirmArea").dialog("close");
                    $("#editArea").dialog("close");
                }
            },
            {
                text:getSomeMessage("ITAOPENSTC102060"),
                click:function(){
                    $("#comfirmArea").dialog("close");
                }
            },  
        ]
    });
}

//値代入モーダルのfloatingip重複時のモーダル
function errorDupModalOpen(){

    //重複だったらエラーモーダルを表示
    $("#DuplicationArea").dialog({
        modal:true,
        buttons:[
            {
                text:getSomeMessage("ITAOPENSTC102050"),
                click:function(){
                    $("#DuplicationArea").dialog("close");
                }
            },
        ]
    });
}
//値代入モーダル floatingipの「識別名/ID」欄で識別名を選択した時のモーダル
function errorSelModalOpen(){

    //識別名だったらエラーモーダルを表示
    $("#SelectArea").dialog({
        modal:true,
        buttons:[
            {
                text:getSomeMessage("ITAOPENSTC102050"),
                click:function(){
                    $("#SelectArea").dialog("close");
                }
            },
        ]
    });
}

//パターンが選択されていない時のダイアログ
function noPatternModalOpen(){

    $("#noPatternArea").dialog({
        modal:true,
        buttons:[
            {
                text:getSomeMessage("ITAOPENSTC102050"),
                click:function(){
                    $("#noPatternArea").dialog("close");
                }
            },
        ]
    });
}

//プロジェクトが選択されていない時のダイアログ
function noProjectModalOpen(){

    $("#noProjectArea").dialog({
        modal:true,
        buttons:[
            {
                text:getSomeMessage("ITAOPENSTC102050"),
                click:function(){
                    $("#noProjectArea").dialog("close");
                }
            },
        ]
    });
}

function prepareValidateHeat(){

    //テンプレートデータ取得
    var template=masterApiResponse.template;
  
    // 画面上の値を置き換え
    $("#rowWrapper .row").each(function(){
        var $dom=$(this);

        var prev=$dom.children(".parameter").val();

        var after="";
        if($dom.children(".select").val()==="string"){
            //string
            after=$dom.children(".input").children(".value").children("option:selected").text() || $dom.children(".input").children().val();
        }else{
            //ID
            after=$dom.children(".input").children().val();
        }

        template=template.split(prev).join(after);
    });

    var patternId=masterApiResponse.data;
    var projectId=masterApiResponse.projectData;
    proxy.validateHeat(patternId,projectId,template);
}

//値代入モーダル
function modalOpen(){

    $("#editArea").dialog({
        width:1100,
        height:700,
        modal:true,
        draggable:true,
        buttons:[
        {
            text:getSomeMessage("ITAOPENSTC102070"),
            click:function(){

                //validation
                var count=0;
                $(".value").each(function(){
                    var value=$(this).val();
                    if(value=="" || value==null){
                        count++;
                    }
                })

                if(count>0){

                    //警告モーダル開く
                    warningModalOpen();

                }else{
                    //バリデートで問題が無ければ、元のフィールドにJSONを返す
                    var array=[];

                    var array2=[];
                    var array3=[];
                    var count2=0;
                    var errorflg1=0;
                    var errorflg2=0;

                    $("#rowWrapper .row").each(function(){
                        var $dom=$(this);
                        var node={
                            "parameter":$dom.children(".parameter").val(),
                            "category":$dom.children(".category").val(),
                            "key":$dom.children(".input").children(".value").children("option:selected").text() ||  "customKey",
                            "value":$dom.children(".input").children().val(),
                            "select":$dom.children(".select").val()
                        }
                        array.push(node);

                        // floatingipの場合
                        if( $dom.children(".category").val() === "ip" ){
                            count2++;
                            var tmp=$dom.children(".input").children(".value").children("option:selected").text() ||  "customKey";
                            var tmp2=$dom.children(".select").val();
                            array2.push(tmp);
                            array3.push(tmp2);
                        }
                    });

                    // floatingip selectチェック "ID"でない場合エラー
                    if ( count2 >= 1 ){
                        for(var i = 0; i < array3.length; i++) {
                            if( array3[i] !== "id" ) {
                                errorflg1 = 1;
                                errorSelModalOpen();
                            }
                        }
                    }
                    // floatingip重複チェック
                    if( count2 > 1 ){
                        for(var i = 0; i < array2.length; i++) {
                            if(array2.indexOf(array2[i]) !== array2.lastIndexOf(array2[i])) {
                                errorflg2 = 1;
                                errorDupModalOpen();
                            }
                        }
                    }

                    //フィールド固定
                    // masterApiResponse.commandは、更新の場合には更新のターゲットID、新規の場合には新規のターゲットIDが入っている
                    if( errorflg1 != 1 && errorflg2 != 1 ){
                        $dom=$(masterApiResponse.command);
                        $dom.attr("value",JSON.stringify(array).split("\"").join("'"));

                        $(this).dialog("close");
                    }
                }
            }
        },
        {
            //text:"キャンセル",
            text:getSomeMessage("ITAOPENSTC102080"),
            click:function(){
                $(this).dialog("close");
            }
        },
        ]
    }).addClass('flex-container');

    // テンプレートデータ読み込み
    var template=masterApiResponse.template;
    template=template.split("\n").join("<br/>");
    $("#templateArea").html(template);

    //パラメータテーブルを作成
    $('#rowWrapper').empty();

    var array= masterApiResponse.template.match(/({{)(\s)(VAR_)(.*)(\s)(}})/g);

    if(array!=null){
        for (var i = 0; i < array.length; i++) {
            var node=array[i];

            //テンプレートプレビューのパラメータに着色を行う（力技）
            var $html=$("#templateArea").html();
            $html=$html.replace(new RegExp(node, 'g'),'<span style="color:red;font-weight:bolder">'+node+'</span>');
            $("#templateArea").html($html);

            $('#rowWrapper').append('<div class="row">'+
                    '<input class="parameter" style="width:150px;color:red;font-weight:bolder;text-align:center;" type="text" value="'+node+'"/>'+
                    '<select class="category" style="width:150px;">'+
                        //'<option value="project">プロジェクト</option>'+
                        '<option value="project">'       + getSomeMessage("ITAOPENSTC102090") + '</option>' +
                        //'<option value="image">イメージ</option>'+
                        '<option value="image">'         + getSomeMessage("ITAOPENSTC102100") + '</option>' +
                        //'<option value="flavor">フレーバ</option>'+
                        '<option value="flavor">'        + getSomeMessage("ITAOPENSTC102110") + '</option>' +
                        //'<option value="ip">FloatingIP</option>'+
                        '<option value="ip">'            + getSomeMessage("ITAOPENSTC102120") + '</option>' +
                        //'<option value="avail">アベイラビリティゾーン</option>'+
                        '<option value="avail">'         + getSomeMessage("ITAOPENSTC102130") + '</option>' +
                        //'<option value="securityGroup">セキュリティグループ</option>'+
                        '<option value="securityGroup">' + getSomeMessage("ITAOPENSTC102140") + '</option>' +
                        //'<option value="keyPair">キーペア</option>'+
                        '<option value="keyPair">'       + getSomeMessage("ITAOPENSTC102150") + '</option>' +
                        //'<option value="server">起動中インスタンス</option>'+
                        '<option value="server">'        + getSomeMessage("ITAOPENSTC102160") + '</option>' +
                        //'<option value="networks">ネットワーク</option>'+
                        '<option value="networks">'      + getSomeMessage("ITAOPENSTC102170") + '</option>' +
                        //'<option value="routers">ルータ</option>'+
                        '<option value="routers">'       + getSomeMessage("ITAOPENSTC102180") + '</option>' +
                        //'<option value="volumes">ボリューム</option>'+
                        '<option value="volumes">'       + getSomeMessage("ITAOPENSTC102190") + '</option>' +
                        //'<option value="host">ITAホスト</option>'+
                        '<option value="host">'          + getSomeMessage("ITAOPENSTC102220") + '</option>' +
                        //'<option value="custom">カスタム値</option></select>'+
                        '<option value="custom">'        + getSomeMessage("ITAOPENSTC102230") + '</option>' +
                    '</select>'+
                    '<span class="input">'+
                        '<input class="value" style="width:150px;" type="text">'+
                    '</span>'+
                    '<select class="select" style="width:73px;">'+
                        //'<option value="string" selected>識別名</option>'+
                        '<option value="string" selected>' + getSomeMessage("ITAOPENSTC102240") + '</option>' +
                        //'<option value="id">ID</option></select>'+
                        '<option value="id">' + getSomeMessage("ITAOPENSTC102250") + '</option>' +
                    '</select>'+
                '</div>');
        }

        //既にパラメータが存在していたら、画面に反映する
        // masterApiResponse.commandは、更新の場合には更新のターゲットID、新規の場合には新規のターゲットIDが入っている

        if(isJson($(masterApiResponse.command).attr("value").split("'").join("\""))){

            var array=JSON.parse($(masterApiResponse.command).attr("value").split("'").join("\"") || "[]");

            $("#rowWrapper .row").each(function(index){
                var paramName=$(this).children(".parameter").val();
                var node=array.slice(index,index+1);
                //反映するべきパラメータが存在したら
                if(node.length>0){
                    $(this).children(".category").val(node[0].category);
                    //.valueのinput項目にタグを設定
                    modalSelectManage($(this).children(".category"));

                    //.valueに値設定
                    $(this).children(".input").children(".value").val(node[0].value);

                    if(node[0].select !=null){
                        //.valueに値設定
                        $(this).children(".select").val(node[0].select);
                    }

                }else{
                    //無かったら、
                    modalSelectManage($(this).children(".category"));
                    //プロジェクトの一番目の項目を選択した状態にする
                    $(this).children(".input").children(".value").children("option").eq(0).attr("selected","selected");
                }
            });
        }

        //パラメータが存在しない場合に、デフォルトの親分類に応じてセレクタを切り替える
        else{
            $("#rowWrapper .row").each(function(){
                modalSelectManage($(this).children(".category"));
            });
        }
    }
}

function modalSelectManage($this){

    if($this.val()==="custom"){
        $this.next(".input").html('<input class="value" style="width:215px;" type="text">');
        $this.next().next(".select").hide();
    }
    else{

        // セレクトボックスを作成
        var $select=$("<select class='value' style='width:220px;'/>");

        //オプションを作成する関数を定義
        var func=(function(list,name,value){
            var optionStr="";
            if(list!=null){

                list.sort(function(a,b){
                    return (a[name]<b[name])?-1:1;
                });
                for (var i =0; i<list.length; i++) {
                    var node=list[i];
                    var selected=(i==0)?'selected':'';
                    optionStr+='<option value="'+node[value]+'" '+selected+'>'+node[name]+'</option>';
                } 
            }
            return optionStr;
        });
        //何故かkeypairだけ階層構造が異なるので、専用の関数を定義
        var keyFunc=(function(list){
            var optionStr="";
            if(list!=null){ 
                list.sort(function(a,b){
                    return (a.keypair.name<b.keypair.name)?-1:1;
                });
                for (var i =0; i<list.length; i++) {
                    var node=list[i];
                    var selected=(i==0)?'selected':'';
                    optionStr+='<option value="'+node.keypair.name+'" '+selected+'>'+node.keypair.name+'</option>';
                } 
            }
            return optionStr;
        });

        //機器一覧との連動
        var floatFunc=(function(flist,hlist,fname,hname,value){
            var optionStr="";
            if(flist!=null && hlist!=null){
                flist.sort(function(a,b){
                    return (a[fname]<b[fname])?-1:1;
                });
                hlist.sort(function(a,b){
                    return (a[hname]<b[hname])?-1:1;
                });
                for( var i = 0, j = 0, k = 0; i < flist.length; ) {
                    if( j == hlist.length ){
                        break;
                    }
                    var fnode = flist[i];
                    var hnode = hlist[j];
                    var selected=(k==0)?'selected':'';
                    if( fnode[fname] == hnode[hname] ){
                        optionStr+='<option value="'+fnode[value]+'" '+selected+'>'+fnode[fname]+'</option>';
                        i += 1;
                        j += 1;
                        k += 1;
                    } else if( fnode[fname] < hnode[hname] ){
                        i += 1;
                    } else {
                        j += 1;
                    }
                }
            }
            return optionStr;
        });

        $option="";
        if($this.val()==="project"){
            $option=func(masterApiResponse.project.tenants,'name','id');
        }else if($this.val()==="image"){
            $option=func(masterApiResponse.image.images,'name','name');
        }else if($this.val()==="flavor"){
            $option=func(masterApiResponse.flavor.flavors,'name','name');
        }else if($this.val()==="ip"){
            $option=floatFunc(masterApiResponse.ip.floatingips,masterApiResponse.host,'floating_ip_address','IP_ADDRESS','id');
        }else if($this.val()==="avail"){
            $option=func(masterApiResponse.avail.availabilityZoneInfo,'zoneName','zoneName');
        }else if($this.val()==="securityGroup"){
            $option=func(masterApiResponse.securityGroup.security_groups,'name','name');
        }else if($this.val()==="keyPair"){
            $option=keyFunc(masterApiResponse.keypair.keypairs);
        }else if($this.val()==="server"){
            $option=func(masterApiResponse.server.servers,'name','name');
        }else if($this.val()==="networks"){
            $option=func(masterApiResponse.networks.networks,'name','name');
        }else if($this.val()==="routers"){
            $option=func(masterApiResponse.routers.routers,'name','name');
        }else if($this.val()==="volumes"){
            $option=func(masterApiResponse.volume.volumes,'name','name');
        }else if($this.val()==="host"){
            $option=func(masterApiResponse.host,'HOSTNAME','IP_ADDRESS');
        }

        $select.append($option);
        $this.next(".input").html($select);
        $this.next().next(".select").show();
        $this.next().next(".select").children("option").eq(0).attr("selected","selected");
    }
}

function isJson(arg){
    arg=(typeof(arg)=="function")?arg():arg;
    if(typeof(arg)!="string"){return false;}
    try{arg=(!JSON)?eval("("+arg+")"):JSON.parse(arg);return true;}catch(e){return false;}
}

$(function(){
    //大カテゴリの選択項目が変更になったら、値のセレクトボックスを更新する
    $(document).on('change',".category",function(){
        modalSelectManage($(this));
    })

    //モーダルを開いたらマスタデータと紐づくテンプレートデータを取得する
    $(document).on('click','.modalOpen',function(){

        var targetID="#"+$(this).prev().attr("id");

        if(targetID==modalTarget){

            if($(templateTarget).children("option:selected").val()==null){
                //パターンが選択されていない警告のモーダル
                noPatternModalOpen();

            }else if($(projectTarget).children("option:selected").val()==null){
                 //プロジェクトが選択されていない警告のモーダル
                noProjectModalOpen();

            }else{
                var patternId=$(templateTarget).children("option:selected").val();
                var projectId=$(projectTarget).children("option:selected").val();
                //update
                proxy.getOpenStackMasterData(patternId,projectId,$(this).attr("command"));
            }
        }else{
            //regist
            if($(registTemplateTarget).children("option:selected").val()==null){
                //パターンが選択されていない警告のモーダル
                noPatternModalOpen();

            }else if($(registProjectTarget).children("option:selected").val()==null){
                 //プロジェクトが選択されていない警告のモーダル
                noProjectModalOpen();
            }else{
                var patternId=$(registTemplateTarget).children("option:selected").val();
                var projectId=$(registProjectTarget).children("option:selected").val();

                proxy.getOpenStackMasterData(patternId,projectId,$(this).attr("command"));
            }
        }
    });
})
