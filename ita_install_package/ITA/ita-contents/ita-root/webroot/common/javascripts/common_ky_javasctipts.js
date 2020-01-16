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
// 検索結果データをPlotly.js用に加工し、描画メソッドをコール
function Graph1_drawBlock(array){
    if(!(array)){
        // 例外処理時にはグラフを表示しない
        GraphDisplaySwich(0);
        Graphs_purge();
        document.getElementById('Graph_msg').innerHTML = getSomeMessage("ITAWDCC90101");
    }
    else{
        var result = {};
        result = JSON.parse(array);
    
        // レコード件数をチェック
        var num_rows = result['num_rows'];
        var graph_vals = result['graph_vals'];
        var date_rows = result['date_rows'];
    
        // メニューごとのツール名
        var tool_name = result['tool_name'];
    
        // メニューごとのステータス名
        var status_name = result['set_status_name'];
    
        var result_status = result['result_status'];
    
        if((num_rows) && (graph_vals)){
            // 配列初期化
            var dataComplete  = {};
            var dataFailed = {};
            var dataUnexpected = {};
            var dataEmage = {};
            var dataCancel = {};
    
            // 配列格納
            var dataComplete = result['AryComplete'];
            var dataFailed = result['AryFailed'];
            var dataUnexpected = result['AryUnexpected'];
            var dataEmage = result['AryEmage'];
            var dataCancel = result['AryCancel'];
    
            var sumComplete = result['SumComplete'];
            var sumFailed = result['SumFailed'];
            var sumUnexpected = result['SumUnexpected'];
            var sumEmage = result['SumEmage'];
            var sumCancel = result['SumCancel'];
    
            var type1 = document.getElementById("g_type1");
            var type2 = document.getElementById("g_type2");
    
            // セッションの初期化
            window.sessionStorage.removeItem(['dataComplete_s']);
            window.sessionStorage.removeItem(['dataFailed_s']);
            window.sessionStorage.removeItem(['dataUnexpected_s']);
            window.sessionStorage.removeItem(['dataEmage_s']);
            window.sessionStorage.removeItem(['dataCancel_s']);
            window.sessionStorage.removeItem(['date_rows_s']);
            window.sessionStorage.removeItem(['status_name_s']);
            window.sessionStorage.removeItem(['tool_name_s']);
    
            // セッションにデータを格納
            window.sessionStorage.setItem('dataComplete_s', JSON.stringify(dataComplete));
            window.sessionStorage.setItem('dataFailed_s', JSON.stringify(dataFailed));
            window.sessionStorage.setItem('dataUnexpected_s', JSON.stringify(dataUnexpected));
            window.sessionStorage.setItem('dataEmage_s', JSON.stringify(dataEmage));
            window.sessionStorage.setItem('dataCancel_s', JSON.stringify(dataCancel));
            window.sessionStorage.setItem('date_rows_s', JSON.stringify(date_rows));
            window.sessionStorage.setItem('status_name_s', JSON.stringify(status_name));
            window.sessionStorage.setItem('tool_name_s', JSON.stringify(tool_name));
    
    
            // 表示グラフの切り替え
            if(type1.checked){
                if(!(date_rows == 1)){
                    drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 1);
                    drawDonutsPie(sumComplete, sumFailed, sumUnexpected, sumEmage, sumCancel);
                }
                else{
                    drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 2);
                    drawDonutsPie(sumComplete, sumFailed, sumUnexpected, sumEmage, sumCancel);
                }
            }
            else if(type2.checked){
                drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 3);
                drawDonutsPie(sumComplete, sumFailed, sumUnexpected, sumEmage, sumCancel);
            }
            else{
                drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 1);
                drawDonutsPie(sumComplete, sumFailed, sumUnexpected, sumEmage, sumCancel);
            }
            var svg_area = document.getElementsByClassName("svg-container");
            for (var i=0;i<svg_area.length;i++) {
                svg_area[i].id = ("svg_area" + i);
            }
            svg_area0.setAttribute('onmouseover', ("Graph_onmouse(1,1)"));
            svg_area0.setAttribute('onmouseout', ("Graph_onmouse(1,0)"));
            svg_area1.setAttribute('onmouseover', ("Graph_onmouse(2,1)"));
            svg_area1.setAttribute('onmouseout', ("Graph_onmouse(2,0)"));
        }
        else if(graph_vals){
            Filter1Tbl_print_async(0);
            GraphDisplaySwich(0);
            Graphs_purge();
        }
        else{
            GraphDisplaySwich(0);
            Graphs_purge();
        }
    }
}

// 棒,折れ線グラフの描画ファンクション
function drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, type){

    // セッションからステータス名、ツール名を取得
    var status_name = JSON.parse( window.sessionStorage.getItem(['status_name_s']));
    var tool_name = JSON.parse( window.sessionStorage.getItem(['tool_name_s']));
    var graph_title = tool_name + getSomeMessage("ITAWDCC10104");

    var data = {};
    var layout= {};

    // 各データの属性を追加
    if((type == 1) || (type == 2)){
        dataComplete.type = 'scatter';
        dataFailed.type = 'scatter';
        dataUnexpected.type = 'scatter';
        dataEmage.type = 'scatter';
        dataCancel.type = 'scatter';
    }
    else if(type == 3){
        dataComplete.type = 'bar';
        dataFailed.type = 'bar';
        dataUnexpected.type = 'bar';
        dataEmage.type = 'bar';
        dataCancel.type = 'bar';
    }
    else{
        dataComplete.type = 'scatter';
        dataFailed.type = 'scatter';
        dataUnexpected.type = 'scatter';
        dataEmage.type = 'scatter';
        dataCancel.type = 'scatter';
    }
    dataComplete.name = status_name[0];
    dataFailed.name = status_name[1];
    dataUnexpected.name = status_name[2];
    dataEmage.name = status_name[3];
    dataCancel.name = status_name[4];

    var data = [dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel];

    var layout = {
        margin: {
            t: 100,
            r: 50,
            b: 45,
            l: 60,
            pad: 5,
            autoexpand: true,
        },
        width: 548,
        height: 430,
        title: graph_title,
        titlefont: {
            size: 22,
        },
        barmode: 'stack',
        legend: {
            font: {
                size: 9,
            },
            "orientation":"h",
            x: 0.0092, y: 1.11, traceorder: 'normal'
        },
        bargap: 0.7,
        line: {
           width: 6,
        },
        yaxis: {
            tickformat: "d",
        },
        xaxis: {
            type: 'date',
            tickformat: "%y/%m/%d",
        },
    };

    var config = {
        displayModeBar: false,
        autosizable: false,
    }

    Plotly.purge('stage');
    Plotly.plot('stage', data, layout, config);

    if(type == 1){
        var update = {
            opacity: 0.8,
            mode: 'lines',
            connectgaps: true,
        };
    }
    else{
        var update = {
            opacity: 0.8,
            mode: 'marker',
            connectgaps: true,
        };
    }

    Plotly.restyle(stage, update);
    $('#stage').hide().fadeIn(300);
}

// 円グラフの描画ファンクション
function drawDonutsPie(sumComplete, sumFailed, sumUnexpected, sumEmage, sumCancel){

    // セッションからステータス名、ツール名を取得
    var status_name = JSON.parse( window.sessionStorage.getItem(['status_name_s']));
    var tool_name = JSON.parse( window.sessionStorage.getItem(['tool_name_s']));
    var graph_title = tool_name + getSomeMessage("ITAWDCC10105");

    var font_size = 18;
    if(graph_title.length >= 30){
        font_size = 12;
    }
    else if(graph_title.length >= 28){
        font_size = 14;
    }
    else if(graph_title.length >= 24){
        font_size = 16;
    }

    var trace = {
        direction: 'clockwise',
        hole: 0.65,
        labels: status_name,
        labelssrc: 'ajspitzner:29:58e2f6',
        marker: {
            line: {
                color: 'rgb(255, 255, 255)',
                width: -0.5
            },
        },
        name: '# of Votes',
        opacity: 0.8,
        pull: 0.01,
        rotation: 0,
        sort: false,
        textfont: {
            color: 'rgb(255, 255, 255)',
            size: 16
        },
        textinfo: 'value+percent',
        textposition: 'inside',
        type: 'pie',
        uid: '81f131',
        values: [sumComplete, sumFailed, sumUnexpected, sumEmage, sumCancel],
        valuessrc: 'ajspitzner:29:035bc3'
    };

    data = [trace];

    layout = {
        margin: {
            t: 95,
            r: 19,
            b: 40,
            l: 46,
            autoexpand: true,
        },
        width: 428,
        height: 430,
        hovermode: 'closest',
        legend: {
            font: {
                size: 10,
            },
            //x: 0.002,
            x: 1.1,
            y: 1.20,
            orientation: 'h',
            traceorder: 'normal'
        },
        annotations: [{
                font: {
                    size: font_size,
                },
                showarrow: false,
                text: graph_title,
                x: 0.5,
                y: 0.5
        }]
    };
    Plotly.purge('donuts_pie');
    Plotly.plot('donuts_pie', data, layout, {displayModeBar: false});
    $('#donuts_pie').hide().fadeIn(300);
}

// グラフの表示ファンクション
function Graphs_print(){

    var filterAreaWrap = 'Filter1_Nakami';
    var printAreaWrap = 'Graph1_Nakami';
    var printAreaHead = 'Graph1_Midashi';
    var filter_data=$('#'+filterAreaWrap+' :input').serializeArray();

    proxy.Filter1Cht_recDraw(filter_data);

    // メッセージ要素を削除する
    var Graph_msg = document.getElementById("Graph_msg");
    Graph_msg.style.display = "none";

    // テーブル表示用領域を開く
    if( checkOpenNow(printAreaWrap)===false ){
        show(printAreaHead, printAreaWrap);
    }
}

//表示したグラフを一旦消す
function Graphs_purge(){
    Plotly.purge('donuts_pie');
    Plotly.purge('stage');
}

//DLボタン、グラフ切り替えスイッチを表示/非表示する
function GraphDisplaySwich(flag){
    var element = document.getElementsByClassName("switch");
    var element2 = document.getElementsByClassName("flex");
    var DL_buttons = document.getElementById("DL_buttons");
    var Graph_msg = document.getElementById("Graph_msg");

    if(flag == 1){
        Graph_msg.style.display = "none";
        for (var i=0;i<element.length;i++) {
            element[i].style.display = "block";
        }
        for (var i=0;i<element2.length;i++) {
            element2[i].style.display = "flex";
        }
        DL_buttons.style.display = "block";
    }
    else{
        for (var i=0;i<element.length;i++) {
            element[i].style.display = "none";
        }
        for (var i=0;i<element2.length;i++) {
            element2[i].style.display = "none";
        }
        DL_buttons.style.display = "none";
        Graph_msg.style.display = "block";
    }
}

// グラフの切り替えファンクション
function Graph_change_button(type){

    var dataComplete = JSON.parse( window.sessionStorage.getItem(['dataComplete_s']));
    var dataFailed = JSON.parse( window.sessionStorage.getItem(['dataFailed_s']));
    var dataUnexpected = JSON.parse( window.sessionStorage.getItem(['dataUnexpected_s']));
    var dataEmage = JSON.parse( window.sessionStorage.getItem(['dataEmage_s']));
    var dataCancel = JSON.parse( window.sessionStorage.getItem(['dataCancel_s']));
    var date_rows = JSON.parse( window.sessionStorage.getItem(['date_rows_s']));

    if(type == 1){
        if(!(date_rows == 1)){
            drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 1);
        }
        else{
            drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 2);
        }
    }
    else{
        drawLineGraph(dataComplete, dataFailed, dataUnexpected, dataEmage, dataCancel, 3);
    }
    var svg_area = document.getElementsByClassName("svg-container");
    for (var i=0;i<svg_area.length;i++) {
        svg_area[i].id = ("svg_area" + i);
    }
    svg_area0.setAttribute('onmouseover', ("Graph_onmouse(1,1)"));
    svg_area0.setAttribute('onmouseout', ("Graph_onmouse(1,0)"));
}

// グラフのダウンロードファンクション
function Graph_DL(type){
    var isMSIE = checkIEBrowse();

    //実行結果グラフのダウンロード
    if(type == 1){
        if(isMSIE){
            Plotly.downloadImage(stage, {format: 'svg', width: 550, height: 430, filename: 'symphony_line'});
        }
        else{
            Plotly.downloadImage(stage, {format: 'png', width: 550, height: 430, filename: 'symphony_line'});
        }
    }
    //実行ステータス割合グラフのダウンロード
    else{
        if(isMSIE){
            Plotly.downloadImage(donuts_pie, {format: 'svg', width: 430, height: 430, filename: 'symphony_circle'});
        }
        else{
            Plotly.downloadImage(donuts_pie, {format: 'png', width: 430, height: 430, filename: 'symphony_circle'});
        }
    }
}

// ダウンロードボタンの表示/非表示切り替えファンクション
function Graph_onmouse(type, order){
    var opacity_conf_on = {
         'opacity': '1', '-webkit-opacity': '1', '-moz-opacity': '1',
         'filter': 'alpha(opacity=100)', '-ms-filter': 'alpha(opacity=100)',
         '-webkit-transition': 'opacity 0.3s ease-out', '-moz-transition': 'opacity 0.3s ease-out',
         '-ms-transition': 'opacity 0.3s ease-out', 'transition': 'opacity 0.3s ease-out'
    }
    var opacity_conf_off = {
         'opacity': '0', '-webkit-opacity': '0', '-moz-opacity': '0',
         'filter': 'alpha(opacity=0)', '-ms-filter': 'alpha(opacity=0)',
         '-webkit-transition': 'opacity 0.3s ease-out', '-moz-transition': 'opacity 0.3s ease-out',
         '-ms-transition': 'opacity 0.3s ease-out', 'transition': 'opacity 0.3s ease-out'
    }

    if(type == 1){
        if(order == 1){
            $('#line_dl').css( opacity_conf_on );
        }
        else{
            $('#line_dl').css( opacity_conf_off );
        }
    }
    else{
        if(order == 1){
            $('#pie_dl').css( opacity_conf_on );
        }
        else{
            $('#pie_dl').css( opacity_conf_off );
        }
    }
}

function checkTypicalFlagInHADACResult(ary_result){
    var retBoolContinue = true;
    if( ! ary_result instanceof Array){
        //----配列ではなかった
        //配列ではなかった----
    }else{
        if( ary_result[0]=='redirectOrderForHADACClient' ){
            redirectTo(ary_result[1],ary_result[2],ary_result,3);
            exit();
        }
    }
    return retBoolContinue;
}

function checkTypicalFlagInHAGResult(ary_result){
    var retBoolContinue = true;
    if( ! ary_result instanceof Array){
        //----配列ではなかった
        //配列ではなかった----
    }else{
        if( ary_result[0]=='redirectOrderForHAGClient' ){
            redirectTo(ary_result[1],ary_result[2],ary_result,3);
            exit();
        }
    }
    return retBoolContinue;
}

function redirectToByRedirectAgentForm(objTgtDoc,objRDAFonDoc,strRedirectAgentFormId){
    if( typeof strRedirectAgentFormId != "string" ){
        strRedirectAgentFormId = 'redirectAgent';
    }
    if( typeof objTgtDoc==="undefined" ){
        objTgtDoc = document;
    }
    if( typeof objRDAFonDoc==="undefined" ){
        objRDAFonDoc = document;
    }
    var objRDAFTag = objRDAFonDoc.getElementById(strRedirectAgentFormId);
    if( objRDAFTag!==null ){
        var intElementLength = objRDAFTag.childNodes.length;
        var intFocusElementIndex = 0;
        var ary_send = {};
        for( var fnv1 = 0; fnv1 < intElementLength ; fnv1++ ){
            var objFocus = objRDAFTag.childNodes[fnv1];
            if( objFocus.tagName=="INPUT" ){
                ary_send[intFocusElementIndex] = objFocus.name;
                ary_send[intFocusElementIndex + 1] = objFocus.value;
                intFocusElementIndex = intFocusElementIndex + 2;
            }
        }
        redirectTo(1, objRDAFTag.action, ary_send, 0, objTgtDoc);
    }
}
function redirectTo(mode,redirectUrl,ary_post_key,intkeyStartIndex,objTgtDoc){
    var retBoolContinue = false;
    if( typeof objTgtDoc=== "undefined" ){
        objTgtDoc = document;
    }
    
    var objFlagDiv = objTgtDoc.createElement('div');
    objFlagDiv.setAttribute('style', 'display:none')
    objFlagDiv.setAttribute('id', 'fxRedirectToExecute');
    if ( objTgtDoc.getElementById('fxRedirectToExecute')=== null ){
        objTgtDoc.body.appendChild(objFlagDiv);
        if( mode==1 ){
            //----POST-Redirectモード
            var objDiv = objTgtDoc.createElement('div');
            objDiv.setAttribute('style', 'display:none')
            var objForm = objTgtDoc.createElement('form');
            if( typeof intkeyStartIndex !== "number" ){
                intkeyStartIndex = 0;
            }
            if( intkeyStartIndex < ary_post_key.length ){
                for( var fnv1=intkeyStartIndex; fnv1 <= ary_post_key.length - 1; fnv1++ ){
                    var objInput = objTgtDoc.createElement('input');
                    objInput.setAttribute('type', 'hidden');
                    objInput.setAttribute('name', ary_post_key[fnv1]);
                    objInput.setAttribute('value', ary_post_key[fnv1+1]);
                    objForm.appendChild(objInput);
                }
            }
            objForm.setAttribute('method', 'post');//送信先
            objForm.setAttribute('action', redirectUrl);//送信先
            objTgtDoc.body.appendChild(objDiv);
            objDiv.appendChild(objForm);
            objForm.submit();
            //POST-Redirectモード----
        }
        else{
            window.location.href = redirectUrl;
        }
    }
    //exit();
    exit;
}

function jumpToSelfHtml(strElementId){
    var retBool;
    var objJumpTarget = $('#'+strElementId);
    if( objJumpTarget===null ){
        retBool = false;
    }
    else{
        retBool = true;
        var varOffSet = objJumpTarget.offset().top;
        $('html,body').animate({scrollTop:varOffSet}, 500 );
        retBool = true;
    }
    return retBool;
}

// QMファイルアップロード用タグ
function formControlForQMfileUpLoad(objTrigger,strSysIdOFForm,strSysIdOFRM,strSysIdOFUFB,strSysIFOfQSALF,strMsg1,strMsg2){
    if($('#'+strSysIdOFForm+' [name=file]').val().length == 0){
        $('#'+strSysIdOFRM+'').html('<span class=error>'+strMsg1+'</span>');
        return false;
    }
    if( objTrigger.name=='1' ){
        //----イベント登録
        objTrigger.name='2';
        $('#'+strSysIdOFForm+'').submit(function(){
            $('#'+strSysIdOFUFB+'').attr('disabled','disabled');
            $('#'+strSysIdOFRM+'').html(''+strMsg2+'');
            $('#'+strSysIFOfQSALF+'').unbind().bind('load', function(){
                var result = $('#'+strSysIFOfQSALF+'').contents().text();
                checkTypicalFlagInHAGResult(getArrayBySafeSeparator(result));
                var uploadData=$.parseJSON($('#'+strSysIFOfQSALF+'').contents().text());
                if(uploadData.error==0){
                    $('#'+strSysIdOFUFB+'').removeAttr('disabled');
                    $('span[name=filewrapper]', '#'+strSysIdOFForm+'').html($('span[name=filewrapper]', '#'+strSysIdOFForm+'').html());
                    $('#'+strSysIdOFRM+'').html(uploadData.text);
                }else{
                    $('#'+strSysIdOFRM+'').html('<span class=error>'+uploadData.text+'</span>');
                    $('#'+strSysIdOFUFB+'').removeAttr('disabled');
                }
            });
        });

        $('#'+strSysIdOFUFB+'').click(function(){
            $('#'+strSysIdOFForm+'').submit();
            return false;
        });
        //イベント登録----
        $('#'+strSysIdOFForm+'').submit();
    }
}
// FUCファイルアップロード用タグ
function formControlForFUCFileUpLoad(objTrigger,strIdOfForm,strIdOfResultArea,strIdOfIframe,strIdOfFSTOfTmpFile,strIdOfInputButton,strMsg1,strMsg2){
    if($('#'+strIdOfForm+' [name=file]').val().length == 0){
        $('#'+strIdOfResultArea+'').html('<span class=error>'+strMsg1+'</span>');
        return false;
    }
    if(objTrigger.name=='1'){
        //----イベント登録
        objTrigger.name='2';
        $('#'+strIdOfForm+'').submit(function(){
            $('#'+strIdOfResultArea+'').html(''+strMsg2+'');
            $('#'+strIdOfIframe+'').unbind().bind('load', function(){
                var result = $('#'+strIdOfInputButton+'').contents().text();
                checkTypicalFlagInHAGResult(getArrayBySafeSeparator(result));
                var uploadData=$.parseJSON($('#'+strIdOfIframe+'').contents().text());
                if(uploadData.error==0){
                    $('#'+strIdOfFSTOfTmpFile+'').get(0).value=uploadData.tmp_file_name;
                    $('#'+strIdOfResultArea+'').html('<div class=resultPreUpload>'+uploadData.text+'</div>');
                }else{
                    $('#'+strIdOfResultArea+'').html('<span class=error>'+uploadData.text+'</span>');
                }
                $('#'+strIdOfInputButton+'').removeAttr('disabled');
            });
        });
        $('#'+strIdOfInputButton+'').click(function(){
            $('#'+strIdOfInputButton+'').attr('disabled',true);
            $('#'+strIdOfForm+'').submit();
            return false;
        });
        //イベント登録----
        $('#'+strIdOfForm+'').submit();
    }
}

function ckRangeOfAlert(value, min){
    var retBool = false;
    if( isNaN(min) !== true ){
        retBool = ckInRange(value, min, null, {"minEq":false});
    }
    return retBool;
}

function ckRangeOfConfirm(value, min, max){
    var retBool = false;
    if( isNaN(min) !== true ){
        retBool = ckInRange(value, min, max, {"minEq":true,"maxEq":true});
    }
    return retBool;
}

function addPullDownBox(strTableWrapAreaId, strTablePrintId, intMaxWidth, strAdjustTargetSeqNumeric, strContainerDivClassName){
    // adjustWidthOfColumnInSuperTable(strTableWrapAreaId, strTablePrintId, 'psl_', strAdjustTargetSeqNumeric, intMaxWidth, strContainerDivClassName);
    
    // tableの幅を設定しなおす
    var $table = $('#' + strTableWrapAreaId );
    var tableWidth = $table.find('table').outerWidth();
    $table.find('.itaTableBody').css('width', tableWidth );
    
}

function adjustWidthOfColumnInSuperTable(strTableWrapAreaId, strTablePrintId, strClassOfColumnIdentifyPrefix, strAdjustTargetSeqNumeric, intMaxWidth,  strContainerDivClassName){
    //----埋め込み先のクラス名
    var commonClassName = strTablePrintId+strAdjustTargetSeqNumeric;
    //埋め込み先のクラス名----

    var strAdjustRulerClassName = strClassOfColumnIdentifyPrefix + strAdjustTargetSeqNumeric;

    var objAdjustRulerForWidth = $('#'+strTableWrapAreaId+' .'+strAdjustRulerClassName).get()[0];
    
    var objHCols = $('#'+strTablePrintId+'-Headers col.'+commonClassName);
    var objMCols = $('#'+strTablePrintId+'-Main col.'+commonClassName);
    var objColH = objHCols.get()[0];

    var objBaseDiv = $('#'+strTableWrapAreaId+' .sBase').get()[0];

    var strDataKey = "col_"+strAdjustTargetSeqNumeric+"_orgWidth";
    //----元々の列幅とリストの幅を取得する
    var intOrgWidth = hiddenDynamicValueGet(objBaseDiv, strDataKey);
    if( intOrgWidth === null ){
        intOrgWidth = objColH.width;
        hiddenDynamicValueSet(objBaseDiv, strDataKey, intOrgWidth);
    }
    var intNewWidth = objAdjustRulerForWidth.scrollWidth;
    //元々の列幅とリストの幅を取得する----

    //----生成されたセレクトタグの幅をもとに新しい幅を算出
    intNewWidth = intNewWidth + 40;
    //生成されたセレクトタグの幅をもとに新しい幅を算出----
    //
    if(intNewWidth < intOrgWidth ){
        //----元のタグの幅より狭い場合は、元の幅を維持
        intNewWidth = intOrgWidth;
        //元のタグの幅より狭い場合は、元の幅を維持----
    }else{
        if( intMaxWidth != 0 && intMaxWidth < intNewWidth ){
            //----広くなりすぎる場合は、intMaxWidthに制限
            intNewWidth = intMaxWidth;
            objAdjustRulerForWidth.style.width = intMaxWidth;
            //広くなりすぎる場合は、intMaxWidthに制限----
        }
    }

    objHCols.removeAttr("width");
    objHCols.attr("width",intNewWidth);
    objMCols.removeAttr("width");
    objMCols.attr("width",intNewWidth);

    var objFC = $('#'+strTableWrapAreaId+' .'+strContainerDivClassName);
    var intFCWidth = objFC.get()[0].scrollWidth;

    var objSTsData = $('#'+strTableWrapAreaId+' .sData');
    objSTsData.get()[0].style.width = eval(intFCWidth - 4) + 'px';
}

function showForDeveloper(result){
}

// ----テーブル整形用のファンクション定義
function adjust_table( table_id,
                       skin_name,
                       container_name,
                       max_table_height,
                       max_table_width ,
                       dummy1,
                       dummy2,
                       strHeaderTrClassName ){
    adjustTableAuto(table_id,
                       skin_name,
                       container_name,
                       max_table_height,
                       max_table_width ,
                       strHeaderTrClassName);

}

function adjustTableAuto( table_id,
                       skin_name,
                       container_name,
                       max_table_height,
                       max_table_width,
                       strHeaderTrClassName ){
    //ポリシー（関数内でjQueryは利用禁止。旧ブラウザではlength関数を使った場合等で怪しい挙動をすることがある）
    if ( document.getElementById(table_id) != null ){
        if( strHeaderTrClassName == '' || typeof strHeaderTrClassName !== 'string'){
            strHeaderTrClassName = 'defaultExplainRow';
        }
        var header_row_num = 0;
        var objAll1=document.getElementsByTagName("*");
        for(var fnv1 in objAll1){
            if(objAll1[fnv1].id == table_id){
                var objTableRaw = objAll1[fnv1];
                for(var fnv2=0; fnv2<objTableRaw.childNodes.length; fnv2++){
                    if( objTableRaw.childNodes[fnv2].tagName == 'TBODY' ){
                        var objTBodyOfRT = objTableRaw.childNodes[fnv2];
                        for(var fnv3=0; fnv3<objTBodyOfRT.childNodes.length; fnv3++){
                            if( objTBodyOfRT.childNodes[fnv3].tagName == 'TR' ){
                                if( objTBodyOfRT.childNodes[fnv3].className == strHeaderTrClassName ){
                                    header_row_num +=1;
                                }
                            }
                        }
                        break;
                    }
                }
                break;
            }
        }

        // ITA Tableを適用する
        itaTable( table_id );
        
        /*
        var table_height = document.getElementById(table_id+'_data').offsetHeight;
        var table_width  = document.getElementById(table_id+'_data').offsetWidth;
        var all=document.getElementsByTagName("*");
        var height_adjust_flag;
        var width_adjust_flag;
        var adjust_px_1;
        var adjust_px_2;
        var adjust_px_3;

        //テーブル(table_id+'_data')は、
        //<class=fakeContainer_XXXXXSetting>.<class=sBase>.<class=sData>.<class=sDataInner>.<table class=sDefault sDefault-Main>
        //の中にある。

        if( table_height <= max_table_height ){
            //----stによって構築されたテーブルの高さが、設定された最大高より小さかった場合（フィルターテーブルは、ほぼココに入る）
            height_adjust_flag = 1;
            adjust_px_3 = 2;//2
        }
        else{
            //----stによって構築されたテーブルの高さが、、設定された最大高より大きかった場合（縦スクロール発生時）
            height_adjust_flag = 0;
            adjust_px_3 = 18;
        }
        var boolAdjustHeight = true;
        if( table_width + adjust_px_3 <= max_table_width ){
            width_adjust_flag = 1;
        }
        else if( table_width < max_table_width ){
            width_adjust_flag = 1;
            adjust_px_3 = 0;
        }
        else{
        }
        if( boolAdjustHeight===true )
        {
            //----stによって構築されたテーブルの幅が、設定された最大幅より大きかった場合（横スクロール発生時）
            adjust_px_1 = 18;
            var header_rows_length  = 0;
            var totalHeaderHeight = 0;
            for(var i in all){
                if(all[i].className == container_name){
                    for( j=0; j<all[i].childNodes.length; j++){
                        if(all[i].childNodes[j].className == 'sBase'){
                            for( k=0; k<all[i].childNodes[j].childNodes.length; k++){
                                if(all[i].childNodes[j].childNodes[k].className == 'sHeader'){
                                    var objHeaderTable = all[i].childNodes[j].childNodes[k].childNodes[0].childNodes[0];
                                    for( l=0; l<objHeaderTable.childNodes.length; l++){
                                        if(objHeaderTable.childNodes[l].tagName == 'TBODY'){
                                            var objTBody = objHeaderTable.childNodes[l];
                                            for( m=0; m<objTBody.childNodes.length; m++){
                                                if(objTBody.childNodes[m].className == strHeaderTrClassName){
                                                    header_rows_length = header_rows_length +1;
                                                    totalHeaderHeight = totalHeaderHeight + objTBody.childNodes[m].offsetHeight;
                                                }
                                            }
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                            break;
                        } 
                    }
                    break;
                }
            }
            adjust_px_2 = totalHeaderHeight;
        }
        
        for(var i in all){
            if(all[i].className == container_name){
                if( height_adjust_flag == 1 ){
                    var table_height_1 = table_height + adjust_px_1;
                    all[i].style.height = table_height_1 + 'px';
                }
                
                //----高さの調整
                for( j=0; j<all[i].childNodes.length; j++){
                    if(all[i].childNodes[j].className == 'sBase'){
                        for( k=0; k<all[i].childNodes[j].childNodes.length; k++){
                            if(all[i].childNodes[j].childNodes[k].className == 'sData'){
                                if( height_adjust_flag == 1 ){
                                    var table_height_2 = table_height - adjust_px_2 + adjust_px_1;
                                    all[i].childNodes[j].childNodes[k].style.height = table_height_2 + 'px';
                                }
                            }
                        }
                    }
                }
                //高さの調整----
                
                //----幅の調整
                for( j=0; j<all[i].childNodes.length; j++){
                    if(all[i].childNodes[j].className == 'sBase'){
                        for( k=0; k<all[i].childNodes[j].childNodes.length; k++){
                            if(all[i].childNodes[j].childNodes[k].className == 'sData'){
                                if( width_adjust_flag == 1 ){
                                    var table_width_2 = table_width + adjust_px_3;
                                    all[i].childNodes[j].childNodes[k].style.width = table_width_2 + 'px';
                                }
                            }
                        }
                    }
                }
                //幅の調整----
            }
        }
        */
   
    }
}
// テーブル整形用のファンクション定義----


function DateInputEventRelay(dateText,objTrigger,intCh){
   var triggerObjId = $(objTrigger).attr('id');
   var objElements = $('#'+triggerObjId+'Agt'+intCh);
   if( objElements.get().length==1 ){
       var objAgent = objElements.get()[0];
       if( objAgent!==null ){
          objAgent.click();
       }
   }
}

function linkDateInputHelper(idOfTableWrapArea,
                             classNameOfDatePickerUserTag,
                             classNameOfDateTimePickerUserTag){
    if( classNameOfDatePickerUserTag == '' || typeof classNameOfDatePickerUserTag !== 'string'){
        classNameOfDatePickerUserTag = 'callDatePicker';
    }
    if( classNameOfDateTimePickerUserTag == '' || typeof classNameOfDateTimePickerUserTag !== 'string'){
        classNameOfDateTimePickerUserTag = 'callDateTimePicker';
    }

    var objElements1 = $('#'+idOfTableWrapArea+' .'+classNameOfDatePickerUserTag);
    var objLen1 = objElements1.length;
    for(var Fnv1=0; Fnv1<objLen1; Fnv1++ ){
        var objFocusBody = objElements1.get()[Fnv1];
        var objFocusId = objFocusBody.id;
        var objFocusId = objFocusBody.id;
        var objElements3 = $('#'+idOfTableWrapArea+' .'+objFocusBody.name+'hide');
        var objPara = objElements3.get()[0];
        var varPara = objPara.innerHTML;
        var aryPara = varPara.split(",");
        var objFxVar = {format:'Y/m/d',timepicker:false,lang:aryPara[0],
            onChangeYear:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'11');},
            onChangeMonth:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'12');},
            onChangeDatetime:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'13');},
            onSelectDate:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'21');},
            onSelectTime:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'22');},
            onClose:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'99');}
        };
        $('#'+objFocusId).datetimepicker(objFxVar);
    }

    var objElements2 = $('#'+idOfTableWrapArea+' .'+classNameOfDateTimePickerUserTag);
    var objLen2 = objElements2.length;
    for(var Fnv1=0; Fnv1<objLen2; Fnv1++ ){
        var objFocusBody = objElements2.get()[Fnv1];
        var objFocusId = objFocusBody.id;
        var objElements3 = $('#'+idOfTableWrapArea+' .'+objFocusBody.name+'hide');
        var objPara = objElements3.get()[0];
        var varPara = objPara.innerHTML;
        var aryPara = varPara.split(",");
        var valFormat = 'Y/m/d H:i:s';
        if(aryPara[1]=='1'){
            valFormat = 'Y/m/d H:i';
        }
        var intStep = eval(aryPara[2]);
        var objFxVar = {lang:aryPara[0],format:valFormat,step:intStep,
            onChangeYear:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'11');},
            onChangeMonth:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'12');},
            onChangeDatetime:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'13');},
            onSelectDate:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'21');},
            onSelectTime:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'22');},
            onClose:function(dateText,objTgt){DateInputEventRelay(dateText,objTgt,'99');}
        };
        $('#'+objFocusId).datetimepicker(objFxVar);
    }

}

// ある値が、最小値から最大値の範囲にあるかを判定するファンクション
function ckInRange(value, min, max, sumType){
    var retBool = false;
    var retExeCon = true;
    var boolMinEq = false;
    var boolMaxEq = false;
    var tmpVar = null;
    if( typeof sumType=== "undefined" ){
        sumType = {};
    }
    tmpVar = sumType["minEq"];
    if( typeof tmpVar === "undefined" ){
        boolMinEq = false; 
    }else{
        boolMinEq = tmpVar;
    }
    tmpVar = sumType["maxEq"];
    if( typeof tmpVar === "undefined" ){
        boolMaxEq = false; 
    }
    else{
        boolMaxEq = tmpVar;
    }
    if( retExeCon === true ){
        //alert('最小値との比較');
        if( isNaN(min) === true || min === null ){
            //alert('最小値の指定なし:'+min+':'+value);
        }
        else{
            if( value == min ){
                //alert('最小値['+min+']と等しい')
                if( boolMinEq !== true ){
                    //alert('最小値と等しく、最小値と等しい場合に範囲内とする設定ではない');
                    retExeCon = false;
                }
            }
            else{
                if( value < min ){
                    retExeCon = false;
                }
            }
        }
    }
    if( retExeCon === true ){
        //alert('最大値との比較');
        if( isNaN(max) === true || max === null ){
            //alert('最大値の指定なし:'+max+':'+value);
        }else{
            if( value == max ){
                //alert('最大値['+max+']と等しい')
                if( boolMaxEq !== true ){
                    //alert('最大値と等しく、最大値と等しい場合に範囲内とする設定ではない');
                    retExeCon = false;
                }
            }
            else{
                if( max < value ){
                    retExeCon = false;
                }
            }
        }
    }
    if( retExeCon === true ){
        retBool = true;
    }
    return retBool;
}

// 入力された値が日付でYYYY/MM/DD形式になっているか調べる
function ckDate(datestr){
    // 正規表現による書式チェック
    if(!datestr.match(/^\d{4}\/\d{2}\/\d{2}$/)){
        return false;
    }
    var vYear = datestr.substr(0, 4) - 0;
    var vMonth = datestr.substr(5, 2) - 1; // Javascriptは、0-11で表現
    var vDay = datestr.substr(8, 2) - 0;
    // 月,日の妥当性チェック
    if(vMonth >= 0 && vMonth <= 11 && vDay >= 1 && vDay <= 31){
        var vDt = new Date(vYear, vMonth, vDay);
        if(isNaN(vDt)){
            return false;
        }
        else if(vDt.getFullYear() == vYear && vDt.getMonth() == vMonth && vDt.getDate() == vDay){
            return true;
        }
        else{
            return false;
        }
    }
    else{
        return false;
    }
}

// クエリーストリングを取得する
function getQuerystring(key, default_){
    if (default_==null){
        default_="";
    }
    key = key.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regex = new RegExp("[\\?&]"+key+"=([^&#]*)");
    var qs = regex.exec(window.location.href);
    if(qs == null){
        return default_;
    }
    else{
        return qs[1];
    }
}

// 画像の最大サイズ(幅/高さ)を超えないように調整する
function fitImageSize( target_class_name, max_width, max_height ){
    for (i in document.images){
        if (document.images[i].className != target_class_name ) continue ;
        if (document.images[i].width > document.images[i].height && document.images[i].width > max_width){
            document.images[i].width = max_width ;
        }
        else{
            if (document.images[i].height > max_height){
                document.images[i].height = max_height ;
            }
        }
    }
}

//////// IE専用の画面再構築用ファンクション ////////
function kaihei_for_IE( midashi, nakami ){
    var change_midashi = document.getElementById(midashi);  // 見出しの部分のタグ
    var display_naiyou = document.getElementById(nakami);   // 非表示させたい部分のタグ
    var ele1 = change_midashi;
    var ele2 = display_naiyou;
    var strOpen = document.getElementById('sysJSCmdText01').innerHTML;
    var strClose = document.getElementById('sysJSCmdText02').innerHTML;
    // 一回目
    if( ele2.style.display == "block" ){
        var button_tag = ele1.innerHTML;
        button_tag = button_tag.replace( strClose , strOpen );
        ele1.innerHTML = button_tag;
        ele2.style.display = "none";
    }
    else{
        var button_tag = ele1.innerHTML;
        button_tag = button_tag.replace( strOpen , strClose );
        ele1.innerHTML = button_tag;
        ele2.style.display = "block";
    }
    // 二回目
    if( ele2.style.display == "block" ){
        var button_tag = ele1.innerHTML;
        button_tag = button_tag.replace( strClose , strOpen );
        ele1.innerHTML = button_tag;
        ele2.style.display = "none";
    }
    else{
        var button_tag = ele1.innerHTML;
        button_tag = button_tag.replace( strOpen , strClose );
        ele1.innerHTML = button_tag;
        ele2.style.display = "block";
    }
}


//////// IEのときだけ全見開きを開閉して画面を再構築するファンクション ////////
function restruct_for_IE(){
}


//////// 表示/非表示ファンクション ////////
var slideOpenSpeed = 0;
function show( midashi, nakami ){
    
    // console.log( slideOpenSpeed + ' / ' + midashi + ' / ' + nakami );
    
    // div class.openで囲まれていなかったら囲む
    if( !$('#' + nakami ).closest('.open').length > 0 ){
        $('#' + nakami ).wrap('<div class="open"></div>');
    }
    
    var nowOpenFlag = checkOpenNow(nakami);
    
    if( nowOpenFlag == 1 ){
        openToClose( midashi, nakami, slideOpenSpeed );
    }
    else{
        closeToOpen( midashi, nakami, slideOpenSpeed );
    }

    // IEのときだけ全見開きを開閉して画面を再構築するファンクションを呼び出し
    restruct_for_IE();
}

function openToClose( midashi, nakami, speed ){
    var change_midashi = document.getElementById(midashi);  // 見出しの部分のタグ
    var display_naiyou = document.getElementById(nakami);   // 非表示させたい部分のタグ
    var ele1 = change_midashi;
    var ele2 = display_naiyou;
    var strOpen = document.getElementById('sysJSCmdText01').innerHTML;
    var strClose = document.getElementById('sysJSCmdText02').innerHTML;
    var isMSIE = checkIEBrowse();

    var button_tag = ele1.innerHTML;
    button_tag = button_tag.replace( strClose , strOpen );
    ele1.innerHTML = button_tag;
    
    if ( speed === undefined ) speed = 500;
    $('#' + nakami ).closest('.open').slideUp( speed, function(){

    if(isMSIE){
        //ele2.scrollHeightの値を保存
        //divDynamicValueSet(nakami,"JSDVScrollHeight",ele2.scrollHeight);
        ele2.style.visibility = "hidden";
        ele2.style.height = 0;
    }
    else{
        ele2.style.display = "none";
    }
    
    } );
}

function closeToOpen( midashi, nakami, speed ){
    var change_midashi = document.getElementById(midashi);  // 見出しの部分のタグ
    var display_naiyou = document.getElementById(nakami);   // 非表示させたい部分のタグ
    var ele1 = change_midashi;
    var ele2 = display_naiyou;
    var strOpen = document.getElementById('sysJSCmdText01').innerHTML;
    var strClose = document.getElementById('sysJSCmdText02').innerHTML;
    var isMSIE = checkIEBrowse();
    
    var button_tag = ele1.innerHTML;
    button_tag = button_tag.replace( strOpen , strClose );
    ele1.innerHTML = button_tag;
    
    if(isMSIE){
        ele2.style.visibility = "visible";
        ele2.style.height = "auto";
    }
    else{
        ele2.style.display = "block";
    }
    if ( speed === undefined ) speed = 500;
    $('#' + nakami ).show().closest('.open').slideDown( speed );
}

function checkOpenNow( nakami ){
    //----今、開いている場合は、true/閉じている場合は、false、を返す
    var display_naiyou = document.getElementById(nakami);   // 非表示させたい部分のタグ
    var ele2 = display_naiyou;
    var isMSIE = checkIEBrowse();
    var boolOpenNow = true;
    if(isMSIE){
        if( ele2.style.visibility == "hidden" ){
            boolOpenNow=false;
        }
    }
    else{
        if( ele2.style.display == "none" ){
            boolOpenNow=false;
        }
    }
    return boolOpenNow;
}

function checkIEBrowse(){
    retBool = false;
    var userAgent = window.navigator.userAgent.toLowerCase();
    if( userAgent.match(/(msie|MSIE)/) || userAgent.match(/(T|t)rident/) ){
        retBool = true;
    }
    return retBool;
}

//////// 改行コード削除ファンクション ////////
function remove_newline( text ){
    var result_string = "";
    result_string = text.replace(/\r/g, "");
    result_string = result_string.replace(/\n/g, "");
    return result_string;
}

//////// 画面レイアウトの動的調整 ////////
function relayout() {
    var wh = $(window).height() - 135;//減算する値は#KIZI以外の高さ要素(CSSと連動)
    var mh = $('#MENU').outerHeight();
    var window_h = $(window).height();
    var heaer_h = $('#HEADER').height();
    var footer_h = $('#FOOTER').height();
    var menu_header_h = $('#MENU h2').height();

    $('#MENU').css('height', window_h - heaer_h - footer_h - menu_header_h -10 + 'px');
    $('#MENU').css({overflow:'auto'});

    
    // 読み込み完了1秒後に開閉速度を変える
    if ( slideOpenSpeed == 0 ) setTimeout( function(){ slideOpenSpeed = 500; }, 1000 );
    
    //#KIZIの高さを指定することでFOOTER位置と同時に配色も整える
    if (wh < mh)//KIZIのheightをMENUより小さくしない
        $('#KIZI').css('min-height', mh + 'px');
    else
        $('#KIZI').css('min-height', wh + 'px');
}

//////// 何もしないダミーファンクション ////////
function do_nothing( ){
    null;
}

//////////////////////////////////////////////////////
// 予約実行日時のdatetimepickerの設定共通化
// idOfTableWrapArea: 予約実行日時の要素名
//////////////////////////////////////////////////////
function setDatetimepicker(idOfTableWrapArea){
   var objLangDiv = document.getElementById('LanguageMode');
   if( typeof objLangDiv === "undefined" ){
       var LangStream = "en";          // 言語設定がされていない場合のデフォルトを英語に設定
   }
   var LangStream = objLangDiv.innerHTML;
   $('#'+idOfTableWrapArea).datetimepicker({ lang:LangStream, step:10 });
}

function Click_Change_Color(obj) {
    if(obj.style.backgroundColor == ''){
        obj.style.backgroundColor = '#ffff00'
    }
    else{
        obj.style.backgroundColor = ''
    }
}
