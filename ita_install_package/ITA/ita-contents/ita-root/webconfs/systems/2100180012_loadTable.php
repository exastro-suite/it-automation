<?php
//   Copyright 2021 NEC Corporation
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
$tmpFx = function (&$aryVariant=array(),&$arySetting=array()){
        global $g;

        $arrayWebSetting = array();
        $arrayWebSetting['page_info'] = $g['objMTS']->getSomeMessage("ITABASEH-MNU-311000");

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

        $table = new TableControlAgent('C_CONDUCTOR_NOTICE_INFO','NOTICE_ID', 'No', 'C_CONDUCTOR_NOTICE_INFO_JNL', $tmpAry);
        $tmpAryColumn = $table->getColumns();
        $tmpAryColumn['NOTICE_ID']->setSequenceID('C_CONDUCTOR_NOTICE_INFO_RIC');
        $tmpAryColumn['JOURNAL_SEQ_NO']->setSequenceID('C_CONDUCTOR_NOTICE_INFO_JSQ');
        unset($tmpAryColumn);
        // ----VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定
        // VIEWをコンテンツソースにする場合、構成する実体テーブルを更新するための設定----

        // マルチユニーク制約
        //$table->addUniqueColumnSet(array('','','',''));
         
        // QMファイル名プレフィックス
        $table->setDBMainTableLabel($g['objMTS']->getSomeMessage("ITABASEH-MNU-311001"));//'Conductor通知先定義'
        // エクセルのシート名
        $table->getFormatter('excel')->setGeneValue('sheetNameForEditByFile', $g['objMTS']->getSomeMessage("ITABASEH-MNU-311001") );//'Conductor通知先定義'

        //---- 検索機能の制御
        $table->setGeneObject('AutoSearchStart',true);
        // 検索機能の制御----

        $table->setAccessAuth(true);    // データごとのRBAC設定

        //'通知名称'
        $objVldt = new SingleTextValidator(0,128,false);
        $c = new TextColumn('NOTICE_NAME',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311002"));//'通知名称'
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311003"));//エクセル・ヘッダでの説明
        $c->setRequired(true);
        $c->setUnique(true);
        $c->setValidator($objVldt);
        $table->addColumn($c);

        //'HTTPリクエストオプション'
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-311004"));#'HTTPリクエストオプション'
            //'通知先(CURLOPT_URL)'
            $objVldt = new SingleTextValidator(0,8192,false);
            $objVldt->setRegexp('/^https?:\/\/[\w!?\/+\-_~;.,*&@#$%()[\]]+/');
            $c = new TextColumn('NOTICE_URL',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311005"));//'通知先(CURLOPT_URL)'
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311006"));//エクセル・ヘッダでの説明
            $c->setRequired(true);
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            //'ヘッダー(CURLOPT_HTTPHEADER)'
            $c = new MultiTextColumn('HEADER',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311007"));//'ヘッダー(CURLOPT_HTTPHEADER)'
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311008"));//エクセル・ヘッダでの説明
            $c->setRequired(true);
            $cg->addColumn($c);

            //'メッセージ(CURLOPT_POSTFIELDS)'
            $c = new MultiTextColumn('FIELDS',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311009"));//'メッセージ(CURLOPT_POSTFIELDS)'
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311010"));//エクセル・ヘッダでの説明
            $c->setRequired(true);
            $cg->addColumn($c);
        $table->addColumn($cg);

        //'PROXY'
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-311011"));//'PROXY'
            //'URL(CURLOPT_PROXY)'
            $objVldt = new SingleTextValidator(0,8192,false);
            $c = new TextColumn('PROXY_URL',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311012"));//'URL(CURLOPT_PROXY)'
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311013"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);

            //'PORT(CURLOPT_PROXYPORT)'
            $objVldt = new IntNumValidator(1,65535);  
            $c = new NumColumn('PROXY_PORT',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311014"));//'PORT(CURLOPT_PROXYPORT)'
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311015"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $c->setSubtotalFlag(false);
            $cg->addColumn($c);

        $table->addColumn($cg);

        //'その他(OTHER)'
        $objVldt = new MultiTextValidator(0,128,false);
        $c = new MultiTextColumn('FQDN',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311016"));//'作業確認URL(FQDN)'
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311017"));//エクセル・ヘッダでの説明
        $c->setValidator($objVldt);
        $table->addColumn($c);

        //'その他(OTHER)'
        $objVldt = new MultiTextValidator(0,8192,false);
        $c = new MultiTextColumn('OTHER',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311018"));//'その他'
        $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311019"));//エクセル・ヘッダでの説明
        $c->setValidator($objVldt);
        $table->addColumn($c);

        //'抑止期間'
        $cg = new ColumnGroup($g['objMTS']->getSomeMessage("ITABASEH-MNU-311020"));//'抑止期間'
            //'開始日時'
            $objVldt = new DateTimeValidator(null,null);
            $c = new DateTimeColumn('SUPPRESS_START',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311021"));//'開始日時'
            $c->setHiddenMainTableColumn(true);
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311022"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);
            //'終了日時'
            $objVldt = new DateTimeValidator(null,null);
            $c = new DateTimeColumn('SUPPRESS_END',$g['objMTS']->getSomeMessage("ITABASEH-MNU-311023"));//'終了日時'
            $c->setHiddenMainTableColumn(true);
            $c->setDescription($g['objMTS']->getSomeMessage("ITABASEH-MNU-311024"));//エクセル・ヘッダでの説明
            $c->setValidator($objVldt);
            $cg->addColumn($c);
        $table->addColumn($cg);
            
        $table->fixColumn();


        //----組み合わせバリデータ----

        //JSON形式チェック[ ヘッダー,メッセージ,その他 ]
        $tmpAryColumn = $table->getColumns();
        $objLU4UColumn = $tmpAryColumn[$table->getRequiredUpdateDate4UColumnID()];

        $objFunction = function($objClientValidator, $value, $strNumberForRI, $arrayRegData, $arrayVariant){
            global $g;
            $retBool = true;
            $retStrBody = '';

            $strModeId = "";
            $modeValue_sub = "";

            $query = "";

            $boolExecuteContinue = true;
            $boolSystemErrorFlag = false;

            $aryVariantForIsValid = $objClientValidator->getVariantForIsValid();

            if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
                if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                    $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                    $strModeId = $aryTcaAction["ACTION_MODE"];
                }
            }

            if($strModeId == "DTUP_singleRecDelete"){
                //----更新前のレコードから、各カラムの値を取得
                $strHeaderjson = isset($arrayVariant['edit_target_row']['HEADER'])?
                                            $arrayVariant['edit_target_row']['HEADER']:null;
                $strFieldsjson       = isset($arrayVariant['edit_target_row']['FIELDS'])?
                                            $arrayVariant['edit_target_row']['FIELDS']:null;
                $strOtherjson        = isset($arrayVariant['edit_target_row']['OTHER'])?
                                            $arrayVariant['edit_target_row']['OTHER']:null;

                $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
                if( $modeValue_sub == "on" ){
                    //----廃止の場合はチェックしない
                    $boolExecuteContinue = false;
                    //廃止の場合はチェックしない----
                }else{
                    $boolSystemErrorFlag = true;
                }

                //更新前のレコードから、各カラムの値を取得----
            }else if( $strModeId == "DTUP_singleRecUpdate" || $strModeId == "DTUP_singleRecRegister" ){
                $strHeaderjson       = array_key_exists('HEADER',$arrayRegData)?
                                               $arrayRegData['HEADER']:null;
                $strFieldsjson             = array_key_exists('FIELDS',$arrayRegData)?
                                               $arrayRegData['FIELDS']:null;
                $strOtherjson              = array_key_exists('OTHER',$arrayRegData)?
                                               $arrayRegData['OTHER']:null;
            }

            //----呼出元がUIがRestAPI/Excel/CSVかを判定
            //呼出元がUIがRestAPI/Excel/CSVかを判定----

            //----JSON形式チェック
            if( $boolExecuteContinue === true && $boolSystemErrorFlag === false){
                //対象
                $arrjsonchkList = array(
                    $g['objMTS']->getSomeMessage("ITABASEH-MNU-311004")." / ".$g['objMTS']->getSomeMessage("ITABASEH-MNU-311007") => $strHeaderjson,
                    $g['objMTS']->getSomeMessage("ITABASEH-MNU-311004")." / ".$g['objMTS']->getSomeMessage("ITABASEH-MNU-311009")  => $strFieldsjson,
                    $g['objMTS']->getSomeMessage("ITABASEH-MNU-311018") => $strOtherjson,
                );

                foreach ( $arrjsonchkList as $tmpkey => $tmpval ) {
                    //空の時、抑止
                    if( $tmpval != "" ){
                        //JSON判定
                        $chkResultJson = json_decode($tmpval);
                        if ( !$chkResultJson ) {
                            if( $retStrBody == ""){
                                $retStrBody = $g['objMTS']->getSomeMessage("ITABASEH-ERR-311000",array($tmpkey) );//'{}：JSONではありません';#
                            }else{
                                $retStrBody .="\n" . $g['objMTS']->getSomeMessage("ITABASEH-ERR-311000",array($tmpkey) );//'{}：JSONではありません';#
                            }
                            $boolExecuteContinue = false;
                            $retBool = false; 
                        }                        
                    }
                }
            }
            //JSON形式チェック----

            if( $boolSystemErrorFlag === true ){
                $retBool = false;
                //----システムエラー
                $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-3001");
            }

            if($retBool===false){
                $objClientValidator->setValidRule($retStrBody);
            }
            return $retBool;
        };

        $objVarVali = new VariableValidator();
        $objVarVali->setErrShowPrefix(false);
        $objVarVali->setFunctionForIsValid($objFunction);
        $objVarVali->setVariantForIsValid(array());

        $objLU4UColumn->addValidator($objVarVali);
        //組み合わせバリデータ----


        $table->setGeneObject('webSetting', $arrayWebSetting);
        return $table;
};
loadTableFunctionAdd($tmpFx,__FILE__);
unset($tmpFx);
?>
