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
/** テキスト取得用関数 **/
function getStr(node){
    var result_str = '';
    if(node === void 0){
        //alert('fx[getStr]:引数の値がundefinedです');
    }else{
        var cn = node.childNodes;
        if(cn){
            for(var idx=0;idx<cn.length;idx++){
                cnv = cn[idx].nodeValue;
                if( typeof(cnv) == "string" ){
                    result_str += cnv;
                }else{
                    result_str += getStr(cn[idx]);
                }
            }
        }else{
        }
    }
    return result_str;
}

/** 数値取得用関数 **/
function getNum(num){
  if( num.match(/^(\-?((\d{1,3}(,\d\d\d)+)|\d+)(\.\d+)?)(.*)$/i) ){
    nn = RegExp.$1;
    nv = RegExp.$6;
    if(nn == ''){
      nn = '0';
    }
    return(new Array(nn,nv));
  }else{
    return(new Array('0',num));
  }
}

/** 数値でのソート用比較関数 **/
var nsort = function(a,b){
  aa=getNum(a);
  ba=getNum(b);
  a = Number(aa[0].replace(/,/g,''));
  b = Number(ba[0].replace(/,/g,''));
  if( a == b ){
    if( aa[1] == ba[1] ){
      return 0;
    }else if( aa[1] > ba[1] ){
      return 1;
    }else{
      return -1;
    }
  }else if( a > b ){
    return 1;
  }else{
    return -1;
  }
}

//反転用フラグ
var rev = new Array(); 

function tableSort(header_rows, objTriggerTag, tablebodyid_prefix, num, sfunc, wrapClass, noSelectClass, ascClass, descClass){

    var tablebodyid = tablebodyid_prefix + '_data';

    var HTable = objTriggerTag.parentNode.parentNode;
    var HTableID = HTable.id;

    // tbodyにIDがない場合はテーブルのIDを取得
    if( HTableID == '' ){
        HTable.id = HTable.parentNode.id;
        HTableID = HTable.id;
    }else{
    }

    //sDataTableのtbodyのオブジェクト、テーブルIDを取得
    if(!document.getElementById(tablebodyid)){
        //alert(tablebodyid + "テーブルbodyのidが存在せず");
        return -100;
    }

    var DTblbody = document.getElementById(tablebodyid);
    var DTblbodyID = DTblbody.id;

    //反転用のフラグの初期化
    if(rev[HTableID] == undefined ){
        rev[HTableID] = Object();
        rev[HTableID].n = -1;
    }

    if( typeof noSelectClass !== "string" ){
        noSelectClass = "sortNotSelected";
    }

    if( typeof ascClass !== "string" ){
        ascClass = "sortSelectedAsc";
    }

    if( typeof descClass !== "string" ){
        descClass = "sortSelectedDesc";
    }

    var strClassName = ascClass;
    //反転用のフラグと三角の追加・削除
    if(rev[HTableID].n == num){
        rev[HTableID].f = !rev[HTableID].f;
        if( rev[HTableID].f === false ){
        }else{
            var strClassName = descClass;
        }
    }else{
        rev[HTableID].n = num;
        rev[HTableID].f = false;
    }

    //----すべてマークを削除する
    var objMarkCollection = $('#'+tablebodyid_prefix+'-Headers .'+wrapClass);
    for(i=0;i<objMarkCollection.length;i++){
        var objFocusWrap = objMarkCollection.get()[i];
        for(ii=0;ii<objFocusWrap.childNodes.length; ii++){
            var objFocus = objFocusWrap.childNodes[ii];
            if( objFocus.className == ascClass || objFocus.className == descClass ){
                objFocus.outerHTML = '<span class="'+noSelectClass+'"></span>';
            }
        }
    }
    var objMarkCollection = $('#'+tablebodyid_prefix+'_data .'+wrapClass);
    for(i=0;i<objMarkCollection.length;i++){
        var objFocusWrap = objMarkCollection.get()[i];
        for(ii=0;ii<objFocusWrap.childNodes.length; ii++){
            var objFocus = objFocusWrap.childNodes[ii];
            if( objFocus.className == ascClass || objFocus.className == descClass ){
                objFocus.outerHTML = '<span class="'+noSelectClass+'"></span>';
            }
        }
    }
    //すべてマークを削除する----

    for(i=0;i<objTriggerTag.childNodes.length; i++){
        var objFocusWrap = objTriggerTag.childNodes[i];
        if( objFocusWrap.className == wrapClass ){
            for(ii=0;ii<objFocusWrap.childNodes.length; ii++){
                var objFocus = objFocusWrap.childNodes[ii];
                if( objFocus.className == noSelectClass ){
                    objFocus.outerHTML = '<span class="'+strClassName+'"></span>';
                }
            }
            break;
        }
    }

    // ソート用の配列初期化
    var xbox = new Array();
    var rows = DTblbody.rows;
    var rows_num = rows.length - 1;
    
    //----ソート対象の値 と 行オブジェクトを取得
    for(i=0;i<rows_num - (header_rows - 1);i++){
        xbox[i] = Object(getStr(rows[i+header_rows].cells[num]));
        xbox[i].row = rows[i+header_rows];
    }
    //ソート対象の値 と 行オブジェクトを取得----

    //----ソート実行
    if( typeof(sfunc) == 'function' ){
        //関数定義あり
        xbox.sort(sfunc);
    }else{
        //関数定義なし(文字列としてソート)
        xbox.sort(function (a,b){
            if( a == b ){
                return 0;
            }else if( a > b ){
                return 1;
            }else{
                return -1;
            }
        });
    }
    //ソート実行----

    // 反転フラグチェック
    if( rev[HTableID].f ){
        xbox.reverse();
    }

    //結果をテーブルに反映
    for(i=0; i<rows_num - (header_rows - 1); i++){
        DTblbody.appendChild(xbox[i].row);
    }

}
