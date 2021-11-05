<?php
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
//    ・WebDBCore機能を用いたWebページの中核設定を行う。
//
//////////////////////////////////////////////////////////////////////

$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
    global $g;

    $arrayWebSetting = array();
    $arrayWebSetting['page_info'] =  $g['objMTS']->getSomeMessage('ITABASEH-MNU-920001');

/*--------↑
投入オペレーション一覧情報
*/
    $tmpAry = array(
        'TT_SYS_01_JNL_SEQ_ID'=>'JOURNAL_SEQ_NO',
        'TT_SYS_02_JNL_TIME_ID'=>'JOURNAL_REG_DATETIME',
        'TT_SYS_03_JNL_CLASS_ID'=>'JOURNAL_ACTION_CLASS',
        'TT_SYS_04_NOTE_ID'=>'NOTE',
        'TT_SYS_04_DISUSE_FLAG_ID'=>'DISUSE_FLAG',
        'TT_SYS_05_LUP_TIME_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TT_SYS_06_LUP_USER_ID'=>'LAST_UPDATE_USER',
        'TT_SYS_NDB_ROW_EDIT_BY_FILE_ID'=>'ROW_EDIT_BY_FILE',
        'TT_SYS_NDB_UPDATE_ID'=>'WEB_BUTTON_UPDATE',
        'TT_SYS_NDB_LUP_TIME_ID'=>'UPD_UPDATE_TIMESTAMP'
    );

    $table = new TableControlAgent('C_REGULARLY_LIST','REGULARLY_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920002'), 'C_REGULARLY_LIST_JNL', $tmpAry);
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['REGULARLY_ID']->setSequenceID('C_REGULARLY_LIST_RIC');
    $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_REGULARLY_LIST_JSQ');
    unset($tmpAryColumn);

    $table->setJsEventNamePrefix(true);

    // QMファイル名プレフィックス
    $table->setDBMainTableLabel($g['objMTS']->getSomeMessage('ITABASEH-MNU-920003'));

    // エクセルのシート名
    $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile',$g['objMTS']->getSomeMessage('ITABASEH-MNU-920004'));

    $table->setAccessAuth(true);    // データごとのRBAC設定


    //Symphony作業一覧へのリンクボタン
    $c = new LinkButtonColumn('detail_show', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920005'), $g['objMTS']->getSomeMessage('ITABASEH-MNU-920005'), 'jumpToSymphonyIntList', array('this'));
    $c->getOutputType('update_table')->setVisible(false); //登録時は非表示
    $c->getOutputType('register_table')->setVisible(false); //登録時は非表示
    $table->addColumn($c);

    //symphonyクラスID
    $c = new IDColumn('SYMPHONY_CLASS_NO',$g['objMTS']->getSomeMessage('ITABASEH-MNU-920022'), 'C_SYMPHONY_CLASS_MNG', 'SYMPHONY_CLASS_NO', 'SYMPHONY_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('SYMPHONY_CLASS_NO'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920023')); //エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('C_SYMPHONY_CLASS_MNG_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('SYMPHONY_CLASS_NO');
    $c->setJournalDispIDOfMaster('SYMPHONY_NAME');
    $c->setRequired(true); //登録/更新時には、入力必須
    $table->addColumn($c);

    //オペレーションID
    $c = new IDColumn('OPERATION_NO_IDBH',$g['objMTS']->getSomeMessage('ITABASEH-MNU-920024'), 'C_OPERATION_LIST', 'OPERATION_NO_IDBH', 'OPERATION_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('OPERATION_NO_UAPK'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920025')); //エクセル・ヘッダでの説明
    $c->setJournalTableOfMaster('C_OPERATION_LIST_JNL');
    $c->setJournalSeqIDOfMaster('JOURNAL_SEQ_NO');
    $c->setJournalLUTSIDOfMaster('LAST_UPDATE_TIMESTAMP');
    $c->setJournalKeyIDOfMaster('OPERATION_NO_UAPK');
    $c->setJournalDispIDOfMaster('OPERATION_NAME');
    $c->setRequired(true); //登録/更新時には、入力必須
    $table->addColumn($c);

    //ステータス
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
        if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
            $exeQueryData[$objColumn->getID()] = 1; //ステータス：準備中
        }else if( $modeValue=="DTUP_singleRecDelete" ){
            $exeQueryData[$objColumn->getID()] = 1; //ステータス：準備中
        }
        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $c = new IDColumn('STATUS_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920006'), 'B_REGULARLY_STATUS', 'REGULARLY_STATUS_ID', 'REGULARLY_STATUS_NAME', '');
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920007')); //エクセル・ヘッダでの説明
    $c->setAllowSendFromFile(false); //エクセル/CSVからのアップロードは不可能
    $c->getOutputType('update_table')->setAttr('data-sch', 'statusID'); //data属性を追加（モーダルから入力欄への値セット用）
    $strWebUIText = $g['objMTS']->getSomeMessage('ITABASEH-MNU-920026');
    $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText))); /* 更新時は「自動入力」を表示 */
    $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText))); /* 登録時は「自動入力」を表示 */
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction); //初期値を1(準備中)に
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('STATUS_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_REGULARLY_STATUS_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'REGULARLY_STATUS_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'REGULARLY_STATUS_NAME',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //実行ユーザ
    $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
        global $g;
        $boolRet = true;
        $intErrorType = null;
        $aryErrMsgBody = array();
        $strErrMsg = "";
        $strErrorBuf = "";

        $executionUserId = $g['login_id'];
        $exeQueryData[$objColumn->getID()] = $executionUserId;

        $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
        return $retArray;
    };
    $c = new IDColumn('EXECUTION_USER_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920027'), 'D_ACCOUNT_LIST', 'USER_ID', 'USERNAME_JP', '');
    $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920028')); //エクセル・ヘッダでの説明
    $c->setAllowSendFromFile(false); //エクセル/CSVからのアップロードは不可能
    $strWebUIText = $g['objMTS']->getSomeMessage('ITABASEH-MNU-920026');
    $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText))); /* 更新時は「自動入力」を表示 */
    $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText))); /* 登録時は「自動入力」を表示 */
    $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction); //実行ユーザを自動入力
    $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
    $objOT->setFirstSearchValueOwnerColumnID('EXECUTION_USER_ID');
    $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'D_ACCOUNT_LIST_JNL',
        'TTT_SEARCH_KEY_COLUMN_ID'=>'USER_ID',
        'TTT_GET_TARGET_COLUMN_ID'=>'USERNAME_JP',
        'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
        'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
        'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
        )
    );
    $objOT->setTraceQuery($aryTraceQuery);
    $c->setOutputType('print_journal_table',$objOT);
    $table->addColumn($c);

    //スケジュール設定ボタン
    $c = new LinkButtonColumn('', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920008'), $g['objMTS']->getSomeMessage('ITABASEH-MNU-920008'), 'regularlyPWattern', array());
    $outputType = new OutputType(new TabHFmt(), new StaticTextTabBFmt(""));
    $outputType->setVisible(false); //一覧時は非表示
    $c->setOutputType("print_table", $outputType);
    $outputType = new OutputType(new TabHFmt(), new LinkButtonTabBFmt());
    $c->setOutputType("update_table", $outputType);
    $c->setEvent("update_table", "onClick", "setSchedule", array()); //ボタン押下時のイベント
    $c->setOutputType("register_table", $outputType);
    $c->setEvent("register_table", "onClick", "setSchedule", array()); //ボタン押下時のイベント
    $c->setDBColumn(false);
    $table->addColumn($c);

    //スケジュール
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage('ITABASEH-MNU-920009'));
        //次回実行日付
        $tmpObjFunction = function($objColumn, $strEventKey, &$exeQueryData, &$reqOrgData=array(), &$aryVariant=array()){
            $boolRet = true;
            $intErrorType = null;
            $aryErrMsgBody = array();
            $strErrMsg = "";
            $strErrorBuf = "";

            $modeValue = $aryVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_MODE"];
            if( $modeValue=="DTUP_singleRecRegister" || $modeValue=="DTUP_singleRecUpdate" ){
                $exeQueryData[$objColumn->getID()] = null; //nullにする
            }else if( $modeValue=="DTUP_singleRecDelete" ){
                $exeQueryData[$objColumn->getID()] = null; //nullにする
            }
            $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$strErrorBuf);
            return $retArray;
        };
        $c = new DateTimeColumn('NEXT_EXECUTION_DATE', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920010'), 'DATETIME', 'DATETIME', false);
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920010'));//エクセル・ヘッダでの説明
        $c->setSecondsInputOnIU(false); //UI表示時に秒を非表示
        $c->setSecondsInputOnFilter(false); //フィルタ表示時に秒を非表示
        $c->setAllowSendFromFile(false); //エクセル/CSVからのアップロードは不可能
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleNextDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $strWebUIText = $g['objMTS']->getSomeMessage('ITABASEH-MNU-920026');
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText))); /* 更新時は「自動入力」を表示 */
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new StaticTextTabBFmt($strWebUIText))); /* 登録時は「自動入力」を表示 */
        $c->setFunctionForEvent('beforeTableIUDAction',$tmpObjFunction); //初期値をnullに
        $cg->addColumn($c);

        //開始日付
        $c = new DateTimeColumn('START_DATE', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920011'), 'DATETIME', 'DATETIME', false);
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920011'));//エクセル・ヘッダでの説明
        $c->setRequired(true); //登録/更新時には、入力必須
        $c->setSecondsInputOnIU(false); //UI表示時に秒を非表示
        $c->setSecondsInputOnFilter(false); //フィルタ表示時に秒を非表示
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleStartDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'scheduleStartDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

        //終了日付
        $c = new DateTimeColumn('END_DATE', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920012'), 'DATETIME', 'DATETIME', false);
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920012'));//エクセル・ヘッダでの説明
        $c->setSecondsInputOnIU(false); //UI表示時に秒を非表示
        $c->setSecondsInputOnFilter(false); //フィルタ表示時に秒を非表示
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleEndDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'scheduleEndDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

        //周期
        $c = new IDColumn('REGULARLY_PERIOD_ID', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920013'), 'B_REGULARLY_PERIOD', 'REGULARLY_PERIOD_ID', 'REGULARLY_PERIOD_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('REGULARLY_PERIOD_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920013'));//エクセル・ヘッダでの説明
        $c->setRequired(true);//登録/更新時には、入力必須
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleRegularlyPeriod'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'scheduleRegularlyPeriod'); //data属性を追加（モーダルから入力欄への値セット用）
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('REGULARLY_PERIOD_ID');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_REGULARLY_PERIOD_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'REGULARLY_PERIOD_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'REGULARLY_PERIOD_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

        //間隔
        $c = new NumColumn('EXECUTION_INTERVAL', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920014'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920014'));//エクセル・ヘッダでの説明
        $c->setRequired(true);//登録/更新時には、入力必須
        $c->setSubtotalFlag(false);
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleExecutionInterval'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'scheduleExecutionInterval'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

        //週番号
        $c = new IDColumn('PATTERN_WEEK_NUMBER', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920015'), 'B_WEEK_NUMBER', 'WEEK_NUMBER_ID', 'WEEK_NUMBER_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('WEEK_NUMBER_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920015'));//エクセル・ヘッダでの説明
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'schedulePatternWeekNumber'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'schedulePatternWeekNumber'); //data属性を追加（モーダルから入力欄への値セット用）
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('PATTERN_WEEK_NUMBER');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_WEEK_NUMBER_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'WEEK_NUMBER_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'WEEK_NUMBER_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

        //曜日
        $c = new IDColumn('PATTERN_DAY_OF_WEEK', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920016'), 'B_DAY_OF_WEEK', 'DAY_OF_WEEK_ID', 'DAY_OF_WEEK_NAME', '', array('SELECT_ADD_FOR_ORDER'=>array('DAY_OF_WEEK_ID'), 'ORDER'=>'ORDER BY ADD_SELECT_1'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920016'));//エクセル・ヘッダでの説明
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'schedulePatternDayOfWeek'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'schedulePatternDayOfWeek'); //data属性を追加（モーダルから入力欄への値セット用）
        $objOT = new TraceOutputType(new ReqTabHFmt(), new TextTabBFmt());
        $objOT->setFirstSearchValueOwnerColumnID('PATTERN_DAY_OF_WEEK');
        $aryTraceQuery = array(array('TRACE_TARGET_TABLE'=>'B_DAY_OF_WEEK_JNL',
            'TTT_SEARCH_KEY_COLUMN_ID'=>'DAY_OF_WEEK_ID',
            'TTT_GET_TARGET_COLUMN_ID'=>'DAY_OF_WEEK_NAME',
            'TTT_JOURNAL_SEQ_NO'=>'JOURNAL_SEQ_NO',
            'TTT_TIMESTAMP_COLUMN_ID'=>'LAST_UPDATE_TIMESTAMP',
            'TTT_DISUSE_FLAG_COLUMN_ID'=>'DISUSE_FLAG'
            )
        );
        $objOT->setTraceQuery($aryTraceQuery);
        $c->setOutputType('print_journal_table',$objOT);
        $cg->addColumn($c);

        //日
        $c = new TextColumn('PATTERN_DAY', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920017'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920017'));//エクセル・ヘッダでの説明
        $c->setValidator(new IntNumValidator(1,31)); //バリデーション設定（1～31までの半角数字）
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'schedulePatternDay'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'schedulePatternDay'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

        //時間
        $c = new TextColumn('PATTERN_TIME', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920018'));
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920018'));//エクセル・ヘッダでの説明
        $strPattern = "/^$|^(0[0-9]{1}|1{1}[0-9]{1}|2{1}[0-3]{1}):(0[0-9]{1}|[1-5]{1}[0-9]{1})$/"; //hh:mm形式の正規表現
        $objVldt = new TextValidator(0, 5, false, $strPattern, "hh:mm");
        $objVldt->setRegexp("/^[^,\"\r\n]*$/s","DTiS_filterDefault");
        $c->setValidator($objVldt);

        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'schedulePatternTime'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'schedulePatternTime'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

    $table->addColumn($cg);

    //実行停止期間
    $cg = new ColumnGroup($g['objMTS']->getSomeMessage('ITABASEH-MNU-920019'));
        //実行停止開始日付
        $c = new DateTimeColumn('EXECUTION_STOP_START_DATE', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920020'), 'DATETIME', 'DATETIME', false);
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920020'));//エクセル・ヘッダでの説明
        $c->setSecondsInputOnIU(false); //UI表示時に秒を非表示
        $c->setSecondsInputOnFilter(false); //フィルタ表示時に秒を非表示
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleExecutionStopStartDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'scheduleExecutionStopStartDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

        //実行停止終了日付
        $c = new DateTimeColumn('EXECUTION_STOP_END_DATE', $g['objMTS']->getSomeMessage('ITABASEH-MNU-920021'), 'DATETIME', 'DATETIME', false);
        $c->setDescription($g['objMTS']->getSomeMessage('ITABASEH-MNU-920021'));//エクセル・ヘッダでの説明
        $c->setSecondsInputOnIU(false); //UI表示時に秒を非表示
        $c->setSecondsInputOnFilter(false); //フィルタ表示時に秒を非表示
        $c->setOutputType('update_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('update_table')->setAttr('data-sch', 'scheduleExecutionStopEndDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $c->setOutputType('register_table', new OutputType(new ReqTabHFmt(), new TextHiddenInputTabBFmt(''))); //テキストと、隠しテキストインプットを出力する
        $c->getOutputType('register_table')->setAttr('data-sch', 'scheduleExecutionStopEndDate'); //data属性を追加（モーダルから入力欄への値セット用）
        $cg->addColumn($c);

    $table->addColumn($cg);

    $table->fixColumn();

    //備考にdata属性を追加
    $tmpAryColumn = $table->getColumns();
    $tmpAryColumn['NOTE']->getOutputType('update_table')->setAttr('data-sch', 'dataNote'); //data属性を追加（モーダルから入力欄への値セット用）
    $tmpAryColumn['NOTE']->getOutputType('register_table')->setAttr('data-sch', 'dataNote'); //data属性を追加（モーダルから入力欄への値セット用）

    $table->setGeneObject('webSetting', $arrayWebSetting);
    return $table;


};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
