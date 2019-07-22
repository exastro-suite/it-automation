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
//------------------------------------------------//
//------------------------------------------------//
//----ページ初期化                                //
//------------------------------------------------//
//------------------------------------------------//

function initProcess(pageMode){
    var strInfoAreaWrap = 'symphonyInfoShowContainer';
    var objInfoAreaWrap = document.getElementById(strInfoAreaWrap);
    // ページモードを保存
    hiddenDynamicValueSet(objInfoAreaWrap, "pageMode", pageMode)
    
    switch(pageMode){
        case "classEdit":
            var varRequestTarget = getRequestTargetFromQuery("symphony_class_id");
            if( varRequestTarget===null ){
                // ボタン「登録」を配置する
                drawCommandButtons(0);
                
                // シンフォニー№に自動採番を表示する
                printSymphonyInfoArea("10",getSomeMessage("ITABASEC010107"));
                
                // 新規登録モードで、編集を可能状態にする
                editPrepare();
            }
            else{
                // ボタン「編集」を配置する
                drawCommandButtons(2);
                
                // 実行確認前と同じく、表示専用モード[1]で表示させる。
                printSymphonyClass(true, varRequestTarget, "11");
            }
            break;
        case "instanceConstruct":
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
            drawCommandButtons(0);

            break;
        case "instanceMonitor":
            var filter1AreaWrap = 'Filter1_Nakami';
            
            var varRequestTarget = getRequestTargetFromQuery("symphony_instance_id");
            if( varRequestTarget===null ){
                //----作業管理リストから作業№を選択して下さい。
                window.alert( getSomeMessage("ITABASEC010108") );

                // 遷移先URLを作成
                var url = '/default/menu/01_browse.php?no=2100000310';
                
                location.href=url;
            }
            else{
                // ボタンを何も配置しない
                drawCommandButtons(0);
                
                loadSymphonyForMonitor(varRequestTarget);            
            }
            break;
    }
}

//------------------------------------------------//
//------------------------------------------------//
//ページ初期化                                ----//
//------------------------------------------------//
//------------------------------------------------//





//------------------------------------------------//
//------------------------------------------------//
//----シンフォニー(インスタンス)表示              //
//------------------------------------------------//
//------------------------------------------------//

function loadSymphonyForMonitor(symphony_instance_id){
    var strSymphonyInstanceNoArea = 'print_symphony_id';
    
    var objSymphonyInstanceNoArea = document.getElementById(strSymphonyInstanceNoArea);
    objSymphonyInstanceNoArea.innerHTML = symphony_instance_id;
    
    printSymphonyInstance(true);
    
    var interval = document.getElementById('intervalOfDisp').innerHTML;
    
    if( timerID==null ){
        timerID = setInterval( "printSymphonyInstance(true)", interval );
    }
}

//----コールバック相互呼出系
//----予約取消
function bookCancelSymphonyInstance(boolCallProxy, aryResultOfCalledProxy){
    var strSymphonyInstanceNoArea = 'print_symphony_id';
    
    var objSymphonyInstanceNoArea = document.getElementById(strSymphonyInstanceNoArea);
    var symphony_instance_id = objSymphonyInstanceNoArea.innerHTML;
    
    if( boolCallProxy===true ){
        if( symphony_instance_id != '' ){
            if( window.confirm(getSomeMessage("ITABASEC010109",{0:symphony_instance_id})) === true ){
                minorPhase = 12;
                drawCommandButtons(minorPhase);
                proxy.bookCancelSymphonyInstance(symphony_instance_id);
            }
        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" ){
            // 保留解除ボタンを非活性化
            releaseHoldButtonAllControl(true);
            if( aryResultOfCalledProxy[1]=="001" ){
                window.alert(getSomeMessage("ITABASEC010201",{0:symphony_instance_id}));
                //window.alert('予約時間を経過していたので、SymphonyインスタンスID['+symphony_instance_id+']を緊急停止しました。');
            }
            else if( aryResultOfCalledProxy[1]=="000" ){
                window.alert(getSomeMessage("ITABASEC010202",{0:symphony_instance_id}));
                //window.alert('SymphonyインスタンスID['+symphony_instance_id+']の予約を取消しました。');
            }
        }
        else{
            minorPhase = 11;
            drawCommandButtons(minorPhase);
        }
    }
}
//予約取消----

//----緊急停止
function scramSymphonyInstance(boolCallProxy, aryResultOfCalledProxy){
    var strSymphonyInstanceNoArea = 'print_symphony_id';
    
    var objSymphonyInstanceNoArea = document.getElementById(strSymphonyInstanceNoArea);
    var symphony_instance_id = objSymphonyInstanceNoArea.innerHTML;
    
    if( boolCallProxy===true ){
        if( symphony_instance_id != '' ){
            if( window.confirm(getSomeMessage("ITABASEC010110",{0:symphony_instance_id})) === true ){
                minorPhase = 22;
                drawCommandButtons(minorPhase);
                proxy.scramSymphonyInstance(symphony_instance_id);
            }
        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" && aryResultOfCalledProxy[1]=="000" ){
            // 保留解除ボタンを非活性化
            releaseHoldButtonAllControl(true);
            window.alert( getSomeMessage("ITABASEC010203",{0:symphony_instance_id} ));
        }
        else{
            minorPhase = 21;
            drawCommandButtons(minorPhase);
        }
    }
}
//緊急停止----

//----保留解除
function holdReleaseMovementInstance(boolCallProxy, intSeqNo, aryResultOfCalledProxy){
    var strSymphonyInstanceNoArea = 'print_symphony_id';
    
    var objSymphonyInstanceNoArea = document.getElementById(strSymphonyInstanceNoArea);
    var symphony_instance_id = objSymphonyInstanceNoArea.innerHTML;
    
    if( boolCallProxy===true ){
        if( symphony_instance_id != '' ){
            if( window.confirm(getSomeMessage("ITABASEC010204",{0:symphony_instance_id,1:intSeqNo}))===true ){
                releaseHoldButtonControl(intSeqNo,true);
                proxy.holdReleaseMovementInstance(symphony_instance_id, intSeqNo);
            }
        }
    }
    else{
        if( aryResultOfCalledProxy[0]=="000" && aryResultOfCalledProxy[1]=="000" ){
            window.alert( getSomeMessage("ITABASEC010205",{0:symphony_instance_id,1:intSeqNo}) );
        }
    }
}
//保留解除----

//----シンフォニーインスタンス(単一)の（全部/一部）再描画
function printSymphonyInstance(boolCallProxy, aryResultOfCalledProxy){
    var strSymphonyInstanceNoArea = 'print_symphony_id';
    var strFlowPrintAreaWrapArea = 'symphony_area';;
    var strFlowPrintAreaClassName = 'sortable_area';
    var intElementSetLength = 8;
    
    var objSymphonyInstanceNoArea = document.getElementById(strSymphonyInstanceNoArea);
    var symphony_instance_id = objSymphonyInstanceNoArea.innerHTML;
    
    if( boolCallProxy===true ){
        if( symphony_instance_id != '' ){
            proxy.printSymphonyInstance(symphony_instance_id);
        }
    }
    else{
        var strStreamOfSymphonyInfos = aryResultOfCalledProxy[4];
        var strStreamOfMovements = aryResultOfCalledProxy[3];
        
        var arySymInfo = getArrayBySafeSeparator(strStreamOfSymphonyInfos);
        var aryMovement = getArrayBySafeSeparator(strStreamOfMovements);
        
        //----シンフォニーi情報(Mov要素の反映)
        
        var intLengthOrg = aryMovement.length;
        var intLengthDived = intLengthOrg / intElementSetLength;
        
        // シンフォニーi情報の表示展開場所、を取得
        var objFlowPrintArea = $('#'+strFlowPrintAreaWrapArea+' .'+ strFlowPrintAreaClassName).get()[0];
        
        //----描画範囲の確定（全部か一部か）
        
        // 表示展開場所に現在表示されているムーブメントi情報、を取得
        var objNowAddedElements = $('.movement2', objFlowPrintArea).get();
        var boolModeOverride = false;
        if( objNowAddedElements.length == intLengthDived ){
            boolModeOverride = true;
        }
        else{
            //----パターン領域をクリアする
            objFlowPrintArea.innerHTML = '';
            //パターン領域をクリアする----
        }
        //描画範囲の確定（全部か一部か）----
        
        for(var fnv1=1; fnv1<=intLengthDived; fnv1++ ){
            var intBaseIndex = (fnv1 - 1) * intElementSetLength;
            
            var aryTmp1 = {};
            for(var fnv2=0; fnv2<intElementSetLength; fnv2++ ){
                var intGetTagetIndex = intBaseIndex + fnv2;
                aryTmp1[fnv2] = aryMovement[intGetTagetIndex];
            }
            
            var aryTmp2 = getArrayBySafeSeparator(aryTmp1[intElementSetLength - 1]);
            
            // 実行中モード("30")で呼び出し
            var objNewForAdd = makeElementForSortableArea("30", aryTmp1, aryTmp2, arySymInfo[5]);
            
            if( boolModeOverride===true ){
                objNowElementAdd = objNowAddedElements[fnv1 - 1];
                if( objNewForAdd.outerHTML != objNowElementAdd.outerHTML ){
                    objFlowPrintArea.replaceChild(objNewForAdd, objNowElementAdd);
                }
            }
            else{
                objFlowPrintArea.insertBefore(objNewForAdd, null);
            }
        }
        //シンフォニーi情報(Mov要素の反映)----
        
        //----シンフォニーi情報(Mov除くの反映)
        
        //----シンフォニーi情報(予約取消/緊急停止ボタンの反映)
        var minorPhase = 0;
        var strScramExeFlag ='';
        switch(arySymInfo[5]){
            case "1": //未発令
                strScramExeFlag = getSomeMessage("ITABASEC010206");
                break;
            case "2": //発令済
                minorPhase = 22;
                strScramExeFlag = getSomeMessage("ITABASEC010207");
                break;
            default:
                break;
            //緊急停止発令フラグが「発令済」の場合----
        }
        
        switch(arySymInfo[3]){
            case "2": //予約中
                minorPhase = 11;
                break;
            case "9": //予約取消(済)
                minorPhase = 12;
                break;
            case "1": //未実行
            case "3": //実行中
            case "4": //実行中(遅延)
            case "6": //緊急停止
                if( arySymInfo[5]=='1' ){
                    // 緊急停止発令フラグが「未発令」
                    minorPhase = 21;
                }
                else if( arySymInfo[5]=='2' ){
                    // 緊急停止発令フラグが「発令済」
                    minorPhase = 22;
                }
                break;
            case "5": //正常終了
            case "7": //異常終了
            case "8": //想定外エラー
                minorPhase = 22;
                break;
            default:
                break;
        }
        //シンフォニーi情報(予約取消/緊急停止ボタンの反映)----
        
        //----ループ処理を止める
        var boolLoopExit = false;
        switch(arySymInfo[3]){
            case "9": //予約取消(済)
            case "6": //緊急停止
            case "5": //正常終了
            case "7": //異常終了
            case "8": //想定外エラー
                boolLoopExit = true;
                break;
            default:
                break;
        }
        if( boolLoopExit===true ){
            if( timerID!==null ){
                //window.alert('ループを停止させます。');
                clearInterval(timerID);
            }
        }
        //ループ処理を止める----
        
        var strExeStatusLabel = '';
        var intSymStartFlag = 0;
        var intSymEndFlag = 0;
        switch(arySymInfo[3]){
            case "2": //予約中
                strExeStatusLabel = getSomeMessage("ITABASEC010208");
                minorPhase = 11;
                break;
            case "9": //予約取消(済)
                strExeStatusLabel = getSomeMessage("ITABASEC010209");
                minorPhase = 12;
                intSymEndFlag = 3;
                break;
            case "1": //未実行
                strExeStatusLabel = getSomeMessage("ITABASEC010301");
                break;
            case "3": //実行中
                strExeStatusLabel = getSomeMessage("ITABASEC010302");
                intSymStartFlag = 1;
                break;
            case "4": //実行中(遅延)
                strExeStatusLabel = getSomeMessage("ITABASEC010303");
                intSymStartFlag = 1;
                break;
            case "6": //緊急停止
                strExeStatusLabel = getSomeMessage("ITABASEC010304");
                intSymStartFlag = 1;
                intSymEndFlag = 2;
                break;
            case "5": //正常終了
                strExeStatusLabel = getSomeMessage("ITABASEC010305");
                intSymStartFlag = 1;
                intSymEndFlag = 1;
                break;
            case "7": //異常終了
                strExeStatusLabel = getSomeMessage("ITABASEC010306");
                intSymStartFlag = 1;
                intSymEndFlag = 2;
                break;
            case "8": //想定外エラー
                strExeStatusLabel = getSomeMessage("ITABASEC010307");
                intSymStartFlag = 1;
                intSymEndFlag = 2;
                break;
            default:
                break;
        }
        drawCommandButtons(minorPhase);
        
        printSymphonyInfoArea("-1",arySymInfo[0],arySymInfo[1],arySymInfo[2]);
        printOperationInfo(false,arySymInfo[6],arySymInfo[7],arySymInfo[8]);
        
        strExeStatusLabelArea = 'instance_status_area';
        var objExeStatusLabelArea = document.getElementById(strExeStatusLabelArea);
        objExeStatusLabelArea.innerHTML = strExeStatusLabel;

        //実行ユーザ
        exeUserLabelArea = 'execution_status_area';
        var objExeUserLabelArea = document.getElementById(exeUserLabelArea);
        objExeUserLabelArea.innerHTML = arySymInfo[4];
        //実行ユーザ

        strBookTime = arySymInfo[9];
        strBookTimeArea = 'book_time_area';
        var objBookTimeArea = document.getElementById(strBookTimeArea);
        objBookTimeArea.innerHTML = strBookTime;
        
        strScramExeFlagArea = 'scram_exe_flag_area';
        var objBookTimeArea = document.getElementById(strScramExeFlagArea);
        objBookTimeArea.innerHTML = strScramExeFlag;
        //シンフォニーi情報(Mov除くの反映)----
        
        //----シンフォニーのSTART/ENDの表示
        var strClassOfStartMark = 'start';
        var objStartPrintArea =  document.getElementById('startMark');
        if( intSymStartFlag === 1 ){
            strClassOfStartMark = strClassOfStartMark + ' symStatusEnd';
        }
        objStartPrintArea.className = strClassOfStartMark;
        
        var strClassOfEndMark = 'end';
        var objEndPrintArea =  document.getElementById('endMark');
        if ( intSymEndFlag === 1 ){
            strClassOfEndMark = strClassOfEndMark + ' symStatusEnd';
        }
        else if( intSymEndFlag === 2 ){
            strClassOfEndMark = strClassOfEndMark + ' symStatusAbort';
        }
        objEndPrintArea.className = strClassOfEndMark;
        //シンフォニーのSTART/ENDの表示----
        
        //----保留解除ポイントの解除ボタン無効化
        if( 0 < intSymEndFlag ){
            releaseHoldButtonAllControl(true);
        }
        //保留解除ポイントの解除ボタン無効化----
        
    }

}
//シンフォニーインスタンス(単一)の（全部/一部）再描画----

//コールバック相互呼出系----

function releaseHoldButtonAllControl(boolDisabled){
    var strFlowPrintAreaWrapArea = 'symphony_area';;
    var strFlowPrintAreaClassName = 'sortable_area';
    var strMovementClassName = 'movement2';
    var objMovementCollection = $('#'+strFlowPrintAreaWrapArea+' .'+ strFlowPrintAreaClassName+' .'+strMovementClassName).get();
    var intElementSetLength = objMovementCollection.length;
    for(var fnv1=1; fnv1<=intElementSetLength; fnv1++ ){
        releaseHoldButtonControl(fnv1, boolDisabled);
    }
}

function releaseHoldButtonControl(intSeqNo, boolDisabled){
    var boolRet = false;
    var strFlowPrintAreaWrapArea = 'symphony_area';;
    var strFlowPrintAreaClassName = 'sortable_area';
    var strMovementClassName = 'movement2';
    
    var objMovementCollection = $('#'+strFlowPrintAreaWrapArea+' .'+ strFlowPrintAreaClassName+' .'+strMovementClassName).get();
    var intElementSetLength = objMovementCollection.length;
    
    if( 0 < intElementSetLength && intSeqNo <= intElementSetLength ){
        var objMovement= objMovementCollection[intSeqNo - 1];
        
        var objRelaseHoldButtons = $('.pause_release',objMovement).get(); 
        
        if( objRelaseHoldButtons.length === 1 ){
            var objRelaseHoldButton = objRelaseHoldButtons[0];
            objRelaseHoldButton.disabled = boolDisabled;
            boolRet = true;
        }
    }
    return boolRet;
}

function jumpDriverMonitorPage(url, execution_no){
    if( execution_no!='' ){
        //新しいタブで開く
        window.open().location.href=url;
    }
}

//------------------------------------------------//
//------------------------------------------------//
//シンフォニー(インスタンス)表示              ----//
//------------------------------------------------//
//------------------------------------------------//




//------------------------------------------------//
//------------------------------------------------//
//----シンフォニー一般表示                        //
//------------------------------------------------//
//------------------------------------------------//

//----コールバック相互呼出系
//----構築済シンフォニーフローを表示する
function printSymphonyClass(boolCallProxy, symphony_class_id, strModeNumeric, strStreamOfMovements, strStreamOfSymphonyInfos){
    var strFlowPrintAreaWrapArea = 'symphony_area';;
    var strFlowPrintAreaClassName = 'sortable_area';
    var intElementSetLength = 8;
    if( boolCallProxy===true ){
        proxy.printSymphonyClass(symphony_class_id, strModeNumeric);
    }
    else{
        //----シンフォニーclass情報(Mov要素の反映)
        var objFlowPrintArea = $('#'+strFlowPrintAreaWrapArea+' .'+ strFlowPrintAreaClassName).get()[0];
        
        //----パターン領域をクリアする
        objFlowPrintArea.innerHTML = '';
        //パターン領域をクリアする----
        
        var aryMovement = getArrayBySafeSeparator(strStreamOfMovements);
        var intLengthOrg = aryMovement.length;
        var intLengthDived = intLengthOrg / intElementSetLength;
        
        for(var fnv1=1; fnv1<=intLengthDived; fnv1++ ){
            var intBaseIndex = (fnv1 - 1) * intElementSetLength;
            
            var aryTmp1 = {};
            for(var fnv2=0; fnv2<intElementSetLength; fnv2++ ){
                var intGetTagetIndex = intBaseIndex + fnv2;
                aryTmp1[fnv2] = aryMovement[intGetTagetIndex];
            }
            var objNewForAdd = makeElementForSortableArea(strModeNumeric, aryTmp1, {}, "");
            objFlowPrintArea.insertBefore(objNewForAdd, null);
        }
        //シンフォニーclass情報(Mov要素の反映)----

        if( strModeNumeric == "10" )
        {
            //----編集可能状態
            editPrepare();
            deleteButtonFunctionOn();
            //編集可能状態----
        }
        if( strModeNumeric == "10" || strModeNumeric == "11" || strModeNumeric == "20" )
        {
            var arySymInfo = getArrayBySafeSeparator(strStreamOfSymphonyInfos);
            printSymphonyInfoArea(strModeNumeric, symphony_class_id, arySymInfo[0], arySymInfo[1], arySymInfo[2]);
        }

        if( strModeNumeric == "20" ){
            //----実行開始直前の場合
            activateExecuteButton();
            //実行開始直前の場合----
        }
    }
}
//構築済シンフォニーフローを表示する----

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
//コールバック相互呼出系----

function activateExecuteButton(){
    var strInfoAreaWrap = 'symphonyInfoShowContainer';
    var objInfoAreaWrap = document.getElementById(strInfoAreaWrap);
    pageMode = hiddenDynamicValueGet(objInfoAreaWrap, "pageMode");
    
    if( pageMode == "instanceConstruct" ){
        //----実行開始ページの場合は、条件を満たしたら、活性化された「ボタン「実行」」を配置する
        var strOperationNoArea = 'print_operation_no_uapk';;
        var objOperationNoArea = document.getElementById(strOperationNoArea);
        
        var strSymphonyNoArea = 'print_symphony_id';
        var objSymphonyNoArea = document.getElementById(strSymphonyNoArea);
        
        if( objSymphonyNoArea.innerHTML != '' && objOperationNoArea.innerHTML != '' ){
            // ボタン「実行」を配置する
            drawCommandButtons(1);
        }
        //実行開始ページの場合は、条件を満たしたら、活性化された「ボタン「実行」」を配置する----
    }
}

function drawCommandButtons(minorPhase){
    //
    var strInfoAreaWrap = 'symphonyInfoShowContainer';
    var objInfoAreaWrap = document.getElementById(strInfoAreaWrap);
    pageMode = hiddenDynamicValueGet(objInfoAreaWrap, "pageMode");
    //
    var strCommandAreaWrap = 'symphony_footer';
    var objCommandAreaWrap = document.getElementById(strCommandAreaWrap);
    
    if( objCommandAreaWrap != null ){
        switch(pageMode){
            case "classEdit":
                objCommandAreaWrap.innerHTML = '';
                //----ユニバーサルデザインに沿わせるためボタン位置を移動
                var objButtonL50 = document.createElement('input');
                objButtonL50.type = 'button';
                objButtonL50.className = 'cmdButton disableAfterPush';
                if( minorPhase==1 ){
                    //objButtonL50.value = '再読込';
                    objButtonL50.value = getSomeMessage("ITABASEC010402");
                    objButtonL50.onclick = new Function( "symphonyReloadForEdit();" );
                }else{
                    objButtonL50 = null;
                }
                if( objButtonL50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonL50, null);
                }
                //ユニバーサルデザインに沿わせるためボタン位置を移動----
                var objButtonR50 = document.createElement('input');
                objButtonR50.type = 'button';
                objButtonR50.className = 'cmdButton disableAfterPush';
                if( minorPhase==0 ){
                    objButtonR50.value = getSomeMessage("ITABASEC010308");
                    objButtonR50.onclick = new Function( "symphonyRegister(true);" );
                }else if( minorPhase==1 ){
                    objButtonR50.value = getSomeMessage("ITABASEC010309");
                    objButtonR50.onclick = new Function( "symphonyUpdate(true);" );
                }else if( minorPhase==2 ){
                    objButtonR50.value = getSomeMessage("ITABASEC010401");
                    objButtonR50.onclick = new Function( "symphonyLoadForEdit();" );
                }else{
                    objButtonR50 = null;
                }
                if( objButtonR50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonR50, null);
                }
                //キャンセルボタン用
                var objButtonCancel50 = document.createElement('input');
                objButtonCancel50.type = 'button';
                objButtonCancel50.className = 'cmdButton disableAfterPush';
                if( minorPhase==1 ){
                    objButtonCancel50.value = getSomeMessage("ITABASEC010411");;
                    objButtonCancel50.onclick = new Function( "symphonyUpdateCancel();" );
                }else{
                    objButtonCancel50 = null;
                }
                if( objButtonCancel50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonCancel50, null);
                }

                break;
            case "instanceConstruct":
                objCommandAreaWrap.innerHTML = '';
                var objButtonR50 = document.createElement('input');
                objButtonR50.type = 'button';
                objButtonR50.className = 'cmdButton disableAfterPush';
                objButtonR50.value = getSomeMessage("ITABASEC010403");
                if( minorPhase==1 ){
                    objButtonR50.disabled = false;
                }else{
                    objButtonR50.disabled = true;
                }
                objButtonR50.onclick = new Function( "symphonyExecute(true);" );
                if( objButtonR50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonR50, null);
                }
                break;
            case "instanceMonitor":
                objCommandAreaWrap.innerHTML = '';
                var objButtonR50 = document.createElement('input');
                objButtonR50.type = 'button';
                objButtonR50.className = 'cmdButton disableAfterPush';
                if( minorPhase==11 || minorPhase==12 ){
                    objButtonR50.value = getSomeMessage("ITABASEC010404");
                    if( minorPhase==12 ){
                        objButtonR50.disabled = true;
                    }
                    objButtonR50.onclick = new Function( "bookCancelSymphonyInstance(true);" );
                }else if( minorPhase==21 || minorPhase==22 ){
                    objButtonR50.value = getSomeMessage("ITABASEC010405");
                    if( minorPhase==22 ){
                        objButtonR50.disabled = true;
                    }
                    objButtonR50.onclick = new Function( "scramSymphonyInstance(true);" );
                }else{
                    objButtonR50 = null;
                }
                if( objButtonR50 != null ){
                    objCommandAreaWrap.insertBefore(objButtonR50, null);
                }
                break;
        }
    }
}

//----並べ替え等の、メインの配置領域用の要素作成
function makeElementForSortableArea(strModeNumeric,aryInfoOfClass,aryInfoOfInstance, strInsAbortFlag){
    var strMovementClass = 'arrow movement2';
    var strDraggableHandleClass = 'draggable-handle';
    var strPatternWrapClass = 'operation_box';
    
    var strPatternNoteClass = 'tips_box';
    var strTextAreaClass = 'tips_box_textarea';
    var strDescAreaLeftClass = 'areaLeft';
    var strDescAreaRightClass = 'areaRight';
    var strSkipCheckClass = 'skip_box';
    
    var strPauseBoxClass = 'pause_box';
    var strSeqNoWrapClass = 'seqNum';
    
    var strDeleteButtonClass = 'delete';
    var strFaceOfDeleteButton = getSomeMessage("ITABASEC010406");
    
    var strReleaseButtonClass = 'holdRelease';
    var strFaceOfReleaseButton = getSomeMessage("ITABASEC010407");
    
    var strFaceOfFlagHold = getSomeMessage("ITABASEC010408");
    var strExplainOfNote = getSomeMessage("ITABASEC010409");
    
    var strFaceOfFlagSkip = getSomeMessage("ITABASEC010410");
    
    //----クラス系情報
    var strOrcValue = aryInfoOfClass[0];
    var strPatternValue = aryInfoOfClass[1];
    var strPatternName = aryInfoOfClass[2];
    var strPatternColorClass = aryInfoOfClass[3];
    var intSeqNo = aryInfoOfClass[4];
    var strNote = aryInfoOfClass[5];
    var strFlagChecked = aryInfoOfClass[6];
    var strMoveOvrdOpeNoIDBH = aryInfoOfClass[7];
    //クラス系情報----

    //----インスタンス系情報
    var strInsStatus = aryInfoOfInstance[0];
    var strInsFlagHoldReleased = aryInfoOfInstance[1];
    var strExecutionNo = aryInfoOfInstance[2];
    var strJumpUrl = aryInfoOfInstance[3];
    var strAbortReceptFlag = aryInfoOfInstance[4];
    var strInsExeSkipFlag = aryInfoOfInstance[5];
    var strTimeStart = aryInfoOfInstance[6];
    var strTimeEnd = aryInfoOfInstance[7];
    var strOvrdOpeNoIDBH = aryInfoOfInstance[8];
    var strOvrdOpeName = aryInfoOfInstance[9];
    //インスタンス系情報----
    
    var strMarkFaceBody = '';
    var strMarkFaceClassName = '';
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            strMarkFaceBody = intSeqNo;
            strMarkFaceClassName = strSeqNoWrapClass;
            break;
        case "30": //実行状態確認モード
            strMarkFaceClassName = 'statusIcon';
            var strStatusCaption = '';
            var strStatusCapSht = '';
            var strInsStatusColorClass = 'movStatusNotStart';
            switch(strInsStatus){
                case "1": //未実行
                    strStatusCaption = getSomeMessage("ITABASEC010501");
                    strInsStatusColorClass = 'movStatusNotStart';
                    strStatusCapSht = '';//'WAIT';
                    if( strInsExeSkipFlag == '2' ){
                        strInsStatusColorClass = 'movStatusSkip';
                        strStatusCapSht = 'SKIP';
                    }
                    break;
                case "10": //準備エラー
                    strInsStatusColorClass = 'movStatusAbort';
                    //strStatusCaption = '準備エラー';
                    strStatusCaption = getSomeMessage("ITABASEC010502");
                    strStatusCapSht = 'ERROR';
                    break;
                case "2": //準備中
                    strInsStatusColorClass = 'movStatusRunningOnTime';
                    strStatusCaption = getSomeMessage("ITABASEC010503");
                    strStatusCapSht = 'RUNNING';
                    break;
                case "3": //実行中
                    strInsStatusColorClass = 'movStatusRunningOnTime';
                    strStatusCaption = getSomeMessage("ITABASEC010504");
                    strStatusCapSht = 'RUNNING';
                    break;
                case "4": //実行中(遅延)
                    strInsStatusColorClass = 'movStatusRunningDelay';
                    strStatusCaption = getSomeMessage("ITABASEC010505");
                    strStatusCapSht = 'DELAYED';
                    break;
                case "6": //異常終了
                    strInsStatusColorClass = 'movStatusAbort';
                    strStatusCaption = getSomeMessage("ITABASEC010506");
                    strStatusCapSht = 'ERROR';
                    break;
                case "5": //実行完了
                    strInsStatusColorClass = 'movStatusEnd';
                    strStatusCaption = getSomeMessage("ITABASEC010507");
                    strStatusCapSht = 'DONE';
                    break;
                case "8": //保留中
                    strInsStatusColorClass = 'movStatusEnd';
                    strStatusCaption = getSomeMessage("ITABASEC010508");
                    strStatusCapSht = 'DONE';
                    break;
                case "9": //正常終了
                    strStatusCaption = getSomeMessage("ITABASEC010509"); 
                    strInsStatusColorClass = 'movStatusEnd';
                    strStatusCapSht = 'DONE';
                    break;
                case "7": //緊急停止
                    strInsStatusColorClass = 'movStatusAbort';
                    strStatusCaption = getSomeMessage("ITABASEC010601");
                    strStatusCapSht = 'ABORT';
                    break;
                case "11": //想定外エラー
                    strInsStatusColorClass = 'movStatusAbort';
                    strStatusCaption = getSomeMessage("ITABASEC010602");
                    strStatusCapSht = 'ERROR';
                    break;
                case "12": //Skip完了
                    strInsStatusColorClass = 'movStatusSkip';
                    strStatusCaption = getSomeMessage("ITABASEC010702");
                    strStatusCapSht = 'SKIP';
                    break;
                case "13": //Skip後保留中
                    strInsStatusColorClass = 'movStatusSkip';
                    strStatusCaption = getSomeMessage("ITABASEC010703");
                    strStatusCapSht = 'SKIP';
                    break;
                case "14": //Skip完了
                    strInsStatusColorClass = 'movStatusSkip';
                    strStatusCaption = getSomeMessage("ITABASEC010704");
                    strStatusCapSht = 'SKIP';
                    break;
                default:
                    break;
            }
            strMarkFaceBody = strStatusCapSht;
            break;
        default:
            break;
    }
    //インスタンス系の埋め込み情報----
    
    var objRetHtmlElement = document.createElement("div");
    objRetHtmlElement.className = strMovementClass;
    objRetHtmlElement.style = 'display: block;';
    
    //----ムーブメント
    var objHtmlSubElement1 = document.createElement("div");
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            objHtmlSubElement1.className = strDraggableHandleClass+' '+strPatternColorClass;
            break;
        case "30": //実行状態確認モード
            objHtmlSubElement1.className = strDraggableHandleClass+' '+strInsStatusColorClass;
            break;
        default:
            break;
    }
    objRetHtmlElement.insertBefore(objHtmlSubElement1, null);
    //ムーブメント----
    
    //----作業パターン名
    var objHtmlSubElement2 = document.createElement("div");
    objHtmlSubElement2.className = strPatternWrapClass;
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            var tmpInnerHTML = '<span class="inLineTitle" title="'+strPatternName+'">'+strPatternName+'</span>';
            objHtmlSubElement2.innerHTML = tmpInnerHTML;
            break;
        case "30": //実行状態確認モード
            var tmpInnerHTML = '['+intSeqNo+']<span class="ttc_'+strPatternColorClass+'">●</span>';
            tmpInnerHTML = tmpInnerHTML + '<span class="inLineTitle" title="'+strPatternName+'">'+strPatternName+'</span>';
            objHtmlSubElement2.innerHTML = tmpInnerHTML;
            break;
    }
    objRetHtmlElement.insertBefore(objHtmlSubElement2, null);
    //作業パターン名----
    
    //----説明
    var objHtmlSubElement3 = document.createElement("div");
    objHtmlSubElement3.className = strPatternNoteClass;
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
            var objHtmlSubElement3_1 = document.createElement("textarea");
            objHtmlSubElement3_1.className = strTextAreaClass;
            objHtmlSubElement3_1.title = strExplainOfNote;
            objHtmlSubElement3_1.innerHTML = strNote;
            objHtmlSubElement3_1.disabled = false;
            objHtmlSubElement3.insertBefore(objHtmlSubElement3_1, null);

            //----上書きオペレーション入力ボックス
            var objHtmlSubElement3_3 = document.createElement("div");
            objHtmlSubElement3_3.className = strDescAreaRightClass;
            var objHtmlSubElement3_3_1 = document.createElement("div");
            addOvrdOpeInputBox(objHtmlSubElement3_3_1,0,strMoveOvrdOpeNoIDBH);
            if (strMoveOvrdOpeNoIDBH == "") {
                objHtmlSubElement3_3_1.onclick = new Function( "addOvrdOpeInputBox(this,1,'');" );
            }
            objHtmlSubElement3_3.insertBefore(objHtmlSubElement3_3_1, null);
            //上書きオペレーション入力ボックス----

            objHtmlSubElement3.insertBefore(objHtmlSubElement3_3, null);

            break;
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            var objHtmlSubElement3_1 = document.createElement("textarea");
            objHtmlSubElement3_1.className = strDescAreaLeftClass;//strTextAreaClass;
            objHtmlSubElement3_1.title = strExplainOfNote;
            objHtmlSubElement3_1.innerHTML = strNote;
            objHtmlSubElement3_1.disabled = true;
            objHtmlSubElement3.insertBefore(objHtmlSubElement3_1, null);

            if( strModeNumeric == "20" ){
                //----実行直前確認モード
                var objHtmlSubElement3_2 = document.createElement("div");
                objHtmlSubElement3_2.className = strDescAreaRightClass;
                
                //----Skip用チェックボックス
                var objHtmlSubElement3_2_1 = document.createElement("div");
                objHtmlSubElement3_2_1.className = strSkipCheckClass;
                
                var objHtmlSubElement3_2_1_1 = document.createElement("input");
                objHtmlSubElement3_2_1_1.className = "skip_box_check";
                objHtmlSubElement3_2_1_1.type = 'checkbox';
                objHtmlSubElement3_2_1_1.value = 'checkedValue';
                objHtmlSubElement3_2_1.insertBefore(objHtmlSubElement3_2_1_1, null);
                
                var objHtmlSubElement3_2_1_2 = document.createElement("label");
                objHtmlSubElement3_2_1_2.className = 'skip_box_label';
                objHtmlSubElement3_2_1_2.innerHTML = strFaceOfFlagSkip;
                objHtmlSubElement3_2_1.insertBefore(objHtmlSubElement3_2_1_2, null);
                
                objHtmlSubElement3_2.insertBefore(objHtmlSubElement3_2_1, null);
                //Skip用チェックボックス----
                
                //----上書きオペレーション入力ボックス
                var objHtmlSubElement3_3 = document.createElement("div");
                objHtmlSubElement3_3.className = strDescAreaRightClass;
                var objHtmlSubElement3_3_1 = document.createElement("div");
                addOvrdOpeInputBox(objHtmlSubElement3_3_1,0,strMoveOvrdOpeNoIDBH);
                if (strMoveOvrdOpeNoIDBH == "") {
                     objHtmlSubElement3_3_1.onclick = new Function( "addOvrdOpeInputBox(this,1,'');" );
                }
                objHtmlSubElement3_3.insertBefore(objHtmlSubElement3_3_1, null);
                //上書きオペレーション入力ボックス----
                objHtmlSubElement3_2.insertBefore(objHtmlSubElement3_3, null);

                objHtmlSubElement3.insertBefore(objHtmlSubElement3_2, null);

                //実行直前確認モード----
            }
            if( strModeNumeric == "11" ){
                //----上書きオペレーション入力ボックス
                var objHtmlSubElement3_3 = document.createElement("div");
                objHtmlSubElement3_3.className = strDescAreaRightClass;
                var objHtmlSubElement3_3_1 = document.createElement("div");
                addOvrdOpeInputBox(objHtmlSubElement3_3_1,2,strMoveOvrdOpeNoIDBH);
                objHtmlSubElement3_3.insertBefore(objHtmlSubElement3_3_1, null);
                //上書きオペレーション入力ボックス----
                objHtmlSubElement3.insertBefore(objHtmlSubElement3_3, null);
            }
            
            break;
        case "30": //実行状態確認モード
            var objHtmlSubElement3_1 = document.createElement("div");
            objHtmlSubElement3_1.className = 'areaLeft';//strTextAreaClass;
            objHtmlSubElement3.insertBefore(objHtmlSubElement3_1, null);
            
            var objHtmlSubElement3_1_1 = document.createElement("textarea");
            objHtmlSubElement3_1_1.className = strDescAreaLeftClass;
            objHtmlSubElement3_1_1.title = strExplainOfNote;
            objHtmlSubElement3_1_1.innerHTML = strNote;
            objHtmlSubElement3_1_1.disabled = true;
            objHtmlSubElement3_1.insertBefore(objHtmlSubElement3_1_1, null);

            var objHtmlSubElement3_2 = document.createElement("div");
            objHtmlSubElement3_2.className = strDescAreaRightClass;//strTextAreaClass;
            //objHtmlSubElement3_2.innerHTML = strStatusCaption + '/' + strTimeStart + '/' + strTimeEnd;
            //objHtmlSubElement3_2.innerHTML = '開始日時　'+ strTimeStart + '<br>終了日時　' + strTimeEnd;
            var tmpInnerHTML = getSomeMessage("ITABASEC010603") + strTimeStart + '<br>' + getSomeMessage("ITABASEC010604") + strTimeEnd;
            if( strOvrdOpeNoIDBH != '' ){
                tmpInnerHTML = tmpInnerHTML + '<div class="inLineDiv">' + getSomeMessage("ITABASEC010607") + strOvrdOpeNoIDBH + '</div>';
                tmpInnerHTML = tmpInnerHTML + '<div class="inLineDiv">' + getSomeMessage("ITABASEC010608");
                tmpInnerHTML = tmpInnerHTML + '<span class="inLineTitle" title="'+strOvrdOpeName+'">'+strOvrdOpeName+'</span>';
                tmpInnerHTML = tmpInnerHTML + '</div>';
            }
            objHtmlSubElement3_2.innerHTML = tmpInnerHTML;
            objHtmlSubElement3.insertBefore(objHtmlSubElement3_2, null);
            break;
        default:
            break;
    }
    objRetHtmlElement.insertBefore(objHtmlSubElement3, null);
    //説明----
    
    //----保留解除ポイント
    var objHtmlSubElement4 = document.createElement("div");
    objHtmlSubElement4.className = strPauseBoxClass;
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
            var objHtmlSubElement4_1 = document.createElement("input");
            objHtmlSubElement4_1.className = "pause_box_check";
            objHtmlSubElement4_1.type = 'checkbox';
            objHtmlSubElement4_1.value = 'checkedValue';
            if( strFlagChecked=='checkedValue' ){
                objHtmlSubElement4_1.checked = true;
            }
            objHtmlSubElement4.insertBefore(objHtmlSubElement4_1, null);
            var objHtmlSubElement4_2 = document.createElement("label");
            objHtmlSubElement4_2.className = 'pause_box_label';
            objHtmlSubElement4_2.innerHTML = strFaceOfFlagHold;
            objHtmlSubElement4.insertBefore(objHtmlSubElement4_2, null);
            break;
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            if( strFlagChecked=='checkedValue' ){
                var objHtmlSubElement4_2 = document.createElement("label");
                objHtmlSubElement4_2.className = 'pause_exists_label';
                objHtmlSubElement4_2.innerHTML = strFaceOfFlagHold;
                objHtmlSubElement4.insertBefore(objHtmlSubElement4_2, null);
            }
            break;
        case "30": //実行状態確認モード
            if( strFlagChecked=='checkedValue' ){
                var objHtmlSubElement4_2 = document.createElement("input");
                objHtmlSubElement4_2.className = 'pause_release';//strReleaseButtonClass;
                objHtmlSubElement4_2.type = 'button';
                objHtmlSubElement4_2.value = strFaceOfReleaseButton;
                if( strInsFlagHoldReleased=='2' ){
                    objHtmlSubElement4_2.disabled = true;
                }
                objHtmlSubElement4_2.onclick = new Function( "holdReleaseMovementInstance(true, "+intSeqNo+");" );
                objHtmlSubElement4.insertBefore(objHtmlSubElement4_2, null);
            }
            break;
        default:
            break;
    }
    objRetHtmlElement.insertBefore(objHtmlSubElement4, null);
    //保留解除ポイント----
    
    //----●の中身
    var objHtmlSubElement5 = document.createElement("div");
    objHtmlSubElement5.className = strMarkFaceClassName;
    objHtmlSubElement5.innerHTML = strMarkFaceBody;
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            break;
        case "30": //実行状態確認モード
            if( strExecutionNo!='' ){
                objHtmlSubElement5.style.cursor = 'pointer';
                objHtmlSubElement5.onclick = new Function( "jumpDriverMonitorPage('"+strJumpUrl+"',"+strExecutionNo+");" );
            }
            objHtmlSubElement5.title = strStatusCaption;
            break;
        default:
            break;
    }
    objRetHtmlElement.insertBefore(objHtmlSubElement5, null);
    //●の中身----
    
    //----削除ボタン
    switch(strModeNumeric){
        case "10": //編集時(編集)モード
            //フロー編集時用（ムーブメント要素削除ボタン）
            var objHtmlSubElement6 = document.createElement("button");
            objHtmlSubElement6.className = strDeleteButtonClass;
            objHtmlSubElement6.innerHTML = strFaceOfDeleteButton;
            objRetHtmlElement.insertBefore(objHtmlSubElement6, null);
            break;
        case "11": //編集時(表示)モード
        case "20": //実行直前確認モード
            break;
        case "30": //実行状態確認モード
            break;
        default:
            break;
    }
    //削除ボタン----
    
    hiddenDynamicValueSet(objRetHtmlElement, 'ORCHE', strOrcValue);
    hiddenDynamicValueSet(objRetHtmlElement, 'PATTERN', strPatternValue);
    
    return objRetHtmlElement;
}
//並べ替え等の、メインの配置領域用の要素作成----

//----RedMineチケット1030
//----上書オペレーション入力欄の追加
function addOvrdOpeInputBox(objAddTgt,intMode, strOverOpeNoIDBH){
    var strOvrdOpeBoxClass = 'ovrd_ope_box';
    var strFaceOfOvrdOpeBox = getSomeMessage("ITABASEC010609");
    
    var inputBoxType = 'hidden';
    if( (intMode == 0 || intMode == 2) && strOverOpeNoIDBH == "") {
        var tgtInnerHTML = '<span class="textExistsLikeSprit">'+strFaceOfOvrdOpeBox+'</span>';
        var inputBoxType = 'hidden';
        var labelInnerHTML = '';
    }
    else{
        var tgtInnerHTML = '';
        var inputBoxType = 'text';
        var labelInnerHTML = strFaceOfOvrdOpeBox;
    }
    
    var objHtmlSubElement3_2_2 = objAddTgt;
    objHtmlSubElement3_2_2.innerHTML = tgtInnerHTML;
    objHtmlSubElement3_2_2.className = strOvrdOpeBoxClass;
    
    var objHtmlSubElement3_2_2_1 = document.createElement("label");
    objHtmlSubElement3_2_2_1.className = 'ovrd_ope_box_label';
    objHtmlSubElement3_2_2_1.innerHTML = labelInnerHTML;
    objHtmlSubElement3_2_2.insertBefore(objHtmlSubElement3_2_2_1, null);
    
    var objHtmlSubElement3_2_2_2 = document.createElement("input");
    objHtmlSubElement3_2_2_2.className = "ovrd_ope_box_input";
    objHtmlSubElement3_2_2_2.type = inputBoxType;

    objHtmlSubElement3_2_2_2.maxLength = 10;
    if( intMode == 2 ) {
        objHtmlSubElement3_2_2_2.disabled = true;
    }
    if( (intMode == 0 || intMode == 2 ) && strOverOpeNoIDBH.length != 0){
        objHtmlSubElement3_2_2_2.value = strOverOpeNoIDBH
    }

    objHtmlSubElement3_2_2.insertBefore(objHtmlSubElement3_2_2_2, null);

    objHtmlSubElement3_2_2.onclick = null;
}

function addOvrdOpeInputBoxEditOnly(objAddTgt,intMode, strOverOpeNoIDBH){
    var strOvrdOpeBoxClass = 'ovrd_ope_edit_only';
    var strFaceOfOvrdOpeBox = getSomeMessage("ITABASEC010609");
    
    var inputBoxType = 'hidden';
    if( (intMode == 0 || intMode == 2) && strOverOpeNoIDBH == "") {
        var tgtInnerHTML = '<span class="textExistsLikeSprit">'+strFaceOfOvrdOpeBox+'</span>';
        var inputBoxType = 'hidden';
        var labelInnerHTML = '';
    }
    else{
        var tgtInnerHTML = '';
        var inputBoxType = 'text';
        var labelInnerHTML = strFaceOfOvrdOpeBox;
    }
    
    var objHtmlSubElement3_2_2 = objAddTgt;
    objHtmlSubElement3_2_2.innerHTML = tgtInnerHTML;
    objHtmlSubElement3_2_2.className = strOvrdOpeBoxClass;
    
    var objHtmlSubElement3_2_2_1 = document.createElement("label");
    objHtmlSubElement3_2_2_1.className = 'ovrd_ope_box_label';
    objHtmlSubElement3_2_2_1.innerHTML = labelInnerHTML;
    objHtmlSubElement3_2_2.insertBefore(objHtmlSubElement3_2_2_1, null);
    
    var objHtmlSubElement3_2_2_2 = document.createElement("input");
    objHtmlSubElement3_2_2_2.className = "ovrd_ope_box_input";
    objHtmlSubElement3_2_2_2.type = inputBoxType;
    objHtmlSubElement3_2_2_2.maxLength = 10;
    if( intMode == 2 ) {
        objHtmlSubElement3_2_2_2.disabled = true;
    }
    if( (intMode == 0 || intMode == 2 ) && strOverOpeNoIDBH.length != 0){
        objHtmlSubElement3_2_2_2.value = strOverOpeNoIDBH
    }
    objHtmlSubElement3_2_2.insertBefore(objHtmlSubElement3_2_2_2, null);

    objHtmlSubElement3_2_2.onclick = null;
}

//----ムーブメントを除去した、シンフォニー情報の表示
function printSymphonyInfoArea(strModeNumeric, symphony_id, symphony_name, symphony_tips, symphony_lt4u){
    var arySymphonyInfo;
    var objSymphonyNoText;
    //
    var objSymphonyNoArea = document.getElementById('print_symphony_id');
    var objSymphonyNameArea = document.getElementById('print_symphony_name');
    var objSymphonyTipsArea = document.getElementById('print_shyphony_tips');
    var objSymphonyLT4UArea = document.getElementById('print_symphony_lt4u');
    var objSymphonyInfoArea = document.getElementById('symphony_header');
    //
    //----シンフォニーNo.を出力
    if( typeof symphony_id==="undefined" ){
        symphony_id = '';
    }
    
    objSymphonyNoText = symphony_id;
    objSymphonyNoArea.innerHTML = objSymphonyNoText;
    //シンフォニーNo.を出力----
    
    //----その他項目
    if( typeof symphony_name==="undefined" ){
        symphony_name = '';
    }
    if( typeof symphony_tips==="undefined" ){
        symphony_tips = '';
    }
    if( typeof symphony_lt4u==="undefined" ){
        symphony_lt4u = '';
    }
    
    if( strModeNumeric == "10" )
    {
        // 更新作業用のモード
        
        // シンフォニー名、を出力
        // プロパティvalueに代入すると<>などが表示されないので、タグの直書
        objSymphonyNameArea.innerHTML = '<input name="symphony_name" value="'+symphony_name+'">'; 
        
        // 説明、を出力
        objSymphonyTipsArea.innerHTML = '';
        var objHtmlSubElement1_2 = document.createElement("textarea");
        objHtmlSubElement1_2.name = 'symphony_tips'; //nameは小文字で
        objHtmlSubElement1_2.innerHTML = symphony_tips;
        objSymphonyTipsArea.insertBefore(objHtmlSubElement1_2, null);
        
        objSymphonyLT4UArea.innerHTML = symphony_lt4u;
    }
    else{
        // 閲覧用のモード
        
        // シンフォニー名、を出力
        objSymphonyNameArea.innerHTML = symphony_name;
        objSymphonyNameArea.title = symphony_name;
        
        // 説明、を出力
        objSymphonyTipsArea.innerHTML = '';
        var objHtmlSubElement1_2 = document.createElement("textarea");
        objHtmlSubElement1_2.innerHTML = symphony_tips;
        objHtmlSubElement1_2.disabled = true;
        objSymphonyTipsArea.insertBefore(objHtmlSubElement1_2, null);
        //alert(symphony_lt4u); //T-Stamp
    }
    
    //その他項目----
}
//ムーブメントを除去した、シンフォニー情報の表示----

//----クエリから編集するターゲットのシンフォニー№を取得する
function getRequestTargetFromQuery(checkRequestKey,boolGenericMode){
    var retValue = null;
    var checkKey;
    if( typeof checkRequestKey==="string" ){
        checkKey = checkRequestKey;
        var checkTargetValue = getQuerystring(checkKey);
        // シンフォニークラス№が取得された場合
        if ( checkTargetValue.length > 0 ){
            if( typeof boolGenericMode!==true ){
                // 整数チェック
                if( checkTargetValue.match( /^[-]?[0-9]+(\.[0-9]+)?$/ ) ){
                    retValue = checkTargetValue;
                }
            }
        }
    }
    // クエリからシンフォニークラス№を取得
    return retValue;
}
//クエリから編集するターゲットのシンフォニー№を取得する----

//------------------------------------------------//
//------------------------------------------------//
//シンフォニー一般表示                        ----//
//------------------------------------------------//
//------------------------------------------------//

