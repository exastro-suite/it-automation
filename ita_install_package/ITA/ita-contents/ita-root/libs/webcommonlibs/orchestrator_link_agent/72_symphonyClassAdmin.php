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

//----(シンフォニークラス編集)素材の読み込み
function printPatternListForEdit($fxVarsStrFilterData){
    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $strPatternListStream = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    //----オーケストレータ—ごとに作業パターンを収集する
    try{
        //----バリデーションチェック(入力形式)
        $objSLTxtVali = new SingleTextValidator(0,256,false);
        if( $objSLTxtVali->isValid($fxVarsStrFilterData) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720102",array($objSLTxtVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objSLTxtVali);
        //バリデーションチェック(入力形式)----
        
        $aryRetBody = getPatternListWithOrchestratorInfo($fxVarsStrFilterData,1);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000200";
            $intErrorType = $aryRetBody[1];
            //
            $aryErrMsgBody = $aryRetBody[2];
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryListSource = $aryRetBody[4];
        $strPatternListStream = makeAjaxProxyResultStream($aryListSource);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $strPatternListStream,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//(シンフォニークラス編集)素材の読み込み----


//----ある１のシンフォニーのクラス定義を表示する
function printOneOfSymphonyClasses($fxVarsIntSymphonyClassId, $fxVarsIntMode){
    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyClassId = "";
    $intMode = "";
    $strStreamOfMovements = "";
    $strStreamOfSymphony = "";
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        $objRBAC = new RoleBasedAccessControl($objDBCA);
        //----シンフォニーが存在するか？
        
        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyClassId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720202",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intSymphonyClassId = $fxVarsIntSymphonyClassId;

        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntMode) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000200";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720203",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        $intMode = $fxVarsIntMode;
        //バリデーションチェック(入力形式)----
        
        //----symphony_ins_noごとに作業パターンの流れを収集する
        //----バリデーションチェック(実質評価)
        $aryRetBody = $objOLA->getInfoFromOneOfSymphonyClasses($fxVarsIntSymphonyClassId, 0);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            //
            if( $aryRetBody[1] === 101 ){
                $intErrorType = 2;
                //
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720204");
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック(実質評価)----
        $aryRowOfSymClassTable = $aryRetBody[4];
        $aryRowOfMovClassTable = $aryRetBody[5];
        
        //----オーケストレータ情報の収集
        
        $aryRetBody = $objOLA->getLiveOrchestratorFromMaster();
        
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryOrcListRow = $aryRetBody[0];
        
        $aryPatternListPerOrc = array();
        //----存在するオーケスト—タ分回る
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $varOrcId = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $varOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            
            $objOLA->addFuncionsPerOrchestrator($varOrcId,$varOrcRPath);
            $aryRetBody = $objOLA->getLivePatternList($varOrcId);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000500";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $aryRow = $aryRetBody[0];
            
            //----オーケストレータカラーを取得
            $aryRetBody = $objOLA->getThemeColorName($varOrcId);
            if( $aryRetBody[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000600";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strThemeColor = $aryRetBody[0];
            //オーケストレータカラーを取得----
            
            $aryPatternListPerOrc[$varOrcId]['ThemeColor'] = $strThemeColor;
        }
        //存在するオーケスト—タ分回る----
        
        //オーケストレータ情報の収集----
        
        //----作業パターンの収集
        
        $aryRetBody = $objOLA->getLivePatternFromMaster();
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryPatternList = $aryRetBody[0];
        
        //作業パターンの収集----
        
        //----ムーブメント情報を固める
        
        //----発見行だけループ
        $aryListSource = array();
        $intCount = 0;
        foreach( $aryRowOfMovClassTable as $row ){
            //対象のMovementのアクセス権をチェック
            $movAccessAuth = false;
            $intPatternId = $row['PATTERN_ID'];

            //対象MovementをSELECT
            $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                   ." WHERE PATTERN_ID = $intPatternId ";
            $objQuery = $objDBCA->sqlPrepare($sql);
            $r = $objQuery->sqlExecute();
            $targetRow = $objQuery->resultFetch();

            $user_id = $g['login_id'];
            $ret  = $objRBAC->getAccountInfo($user_id); 
            list($ret,$permission) = $objRBAC->chkOneRecodeAccessPermission($targetRow);
            if($ret === false) {
                //例外処理へ
                $strErrStepIdInFx="00000700";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            } else {
                if($permission === true) {
                    $movAccessAuth = true;
                }
            }

            // 1:オーケストレータID
            $varOrcIdFromMovClassTable =  $row['ORCHESTRATOR_ID'];
            $aryListSource[] = $varOrcIdFromMovClassTable;

            // 2:作業パターンID
            $varPatternIdFromMovClassTable = $intPatternId;
            $aryListSource[] = $varPatternIdFromMovClassTable;

            // 3:作業パターン(Movement)の名前
            $strPatternName = "";
            if( array_key_exists($intPatternId,$aryPatternList) === true ){
                //----作業パターンが存在している
                if( $aryPatternList[$intPatternId]['ITA_EXT_STM_ID'] == $row['ORCHESTRATOR_ID'] ){
                    //----オーケストレータも同じ
                    if($movAccessAuth == true){
                        $strPatternName = $aryPatternList[$intPatternId]['PATTERN_NAME'];
                    }
                    //オーケストレータも同じ----
                }
                //作業パターンが存在している----
            }
            if( $strPatternName == "" ){
                $strPatternName = $objMTS->getSomeMessage("ITABASEH-ERR-5720205",$row['PATTERN_ID']);
            }
            $aryListSource[] = htmlspecialchars($strPatternName);

            // 4:テーマカラー
            $themeColor = "unknowngray";
            if($movAccessAuth == true){
                $themeColor =  $aryPatternListPerOrc[$row['ORCHESTRATOR_ID']]['ThemeColor'];
            }
            $aryListSource[] = $themeColor;
            
            // 5:楽章番号
            $aryListSource[] = $row['MOVEMENT_SEQ'];

            // 6:説明
            $description = "";
            if($movAccessAuth == true){
                $description = htmlspecialchars($row['DESCRIPTION']);
            }
            $aryListSource[] = $description;
            
            //----保留ポイントの有無
            if( $row['NEXT_PENDING_FLAG'] == '1' ){
                // 保留ポイントあり
                $varNextPendingFlag = 'checkedValue';
            }
            else if( $row['NEXT_PENDING_FLAG'] == '2' ){
                // 保留ポイントなし
                $varNextPendingFlag = '';
            }
            else{
                // ----存在しないはずの値
                
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000800";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                
                //存在しないはずの値-----
            }
            // 7:
            $aryListSource[] = $varNextPendingFlag;
            //保留ポイントの有無----

            // 8:
            $aryListSource[] = $row['OPERATION_NO_IDBH'];

            //上書きオペレーションID
        }
        //発見行だけループ----
        
        //ムーブメント情報を固める----
        
        $strLT4UBody = '';
        if( 0 < strlen($aryRowOfSymClassTable['LUT4U']) ){
            $strLT4UBody = 'T_'.$aryRowOfSymClassTable['LUT4U'];
        }
        
        //----シンフォニー情報を固める
        $arySymphonySource = array(htmlspecialchars($aryRowOfSymClassTable['SYMPHONY_NAME'])
                                    ,htmlspecialchars($aryRowOfSymClassTable['DESCRIPTION'])
                                    ,$strLT4UBody
                                    ,htmlspecialchars($aryRowOfSymClassTable['ACCESS_AUTH'])
        );
        //シンフォニー情報を固める----
        
        $strStreamOfMovements = makeAjaxProxyResultStream($aryListSource);
        $strStreamOfSymphony = makeAjaxProxyResultStream($arySymphonySource);
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    //
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $arrayResult = array($strResultCode,
                         $strDetailCode,
                         $intSymphonyClassId,
                         $intMode,
                         $strStreamOfMovements,
                         $strStreamOfSymphony,
                         nl2br($strExpectedErrMsgBodyForUI)
                         );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $arrayResult;
}
//ある１のシンフォニーのクラス定義を表示する----

//----ある１のシンフォニーの定義を更新する
function symphonyClassUpdateExecute($fxVarsIntSymphonyClassId, $fxVarsAryReceptData, $fxVarsStrSortedData, $fxVarsStrLT4UBody){
    // グローバル変数宣言
    global $g;
    
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "";
    $intSymphonyClassId = '';
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $arrayConfigForSymClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYMPHONY_CLASS_NO"=>"",
        "SYMPHONY_NAME"=>"",
        "DESCRIPTION"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $arrayConfigForMovInsIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_CLASS_NO"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "MOVEMENT_SEQ"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "DESCRIPTION"=>"",
        "SYMPHONY_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);

        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyClassId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720302",array($objIntNumVali->getValidRule()));
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        
        $objSLTxtVali = new SingleTextValidator(0,128,false);
        if( $objSLTxtVali->isValid($fxVarsStrLT4UBody) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000200";
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5720303",array($objSLTxtVali->getValidRule()));
        }
        unset($objSLTxtVali);
        
        $aryExecuteData = convertReceptDataToDataForIUD($fxVarsAryReceptData);
        $aryRetBody = sortedDataDecodeForEdit($fxVarsStrSortedData);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            $intErrorType = $aryRetBody[1];
            if( $aryRetBody[1] < 500 ){
                $strExpectedErrMsgBodyForUI = implode("\n",$aryRetBody[2]);
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $tmpAryMovement = $aryRetBody[0];
        //----バリデーションチェック
        $aryRetBody = validationCheckForTableIUD($tmpAryMovement, $aryExecuteData, $fxVarsIntSymphonyClassId);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            $intErrorType = $aryRetBody[1];
            if( $aryRetBody[1] < 500 ){
                $strExpectedErrMsgBodyForUI = implode("\n",$aryRetBody[2]);
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック----
        //バリデーションチェック(入力形式)----
        
        $aryRetBody = dataConvertForTableIUD($tmpAryMovement, $aryExecuteData);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000500";
            throw new Exception( '01000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryMovement = $aryRetBody[0];
        unset($tmpAryMovement);
        
        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000600";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する（デッドロックを防ぐために、値昇順序））
        
        // ----MOV-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_MOVEMENT_CLASS_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $retArray = getSequenceLockInTrz('C_MOVEMENT_CLASS_MNG_RIC','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // MOV-CLASS-シーケンスを掴む----
        
        // ----SYM-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_SYMPHONY_CLASS_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000900";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $retArray = getSequenceLockInTrz('C_SYMPHONY_CLASS_MNG_RIC','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // -SYM-CLASS-シーケンスを掴む----
        
        // シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する（デッドロックを防ぐために、値昇順序））----
        
        // ----更新前のシンフォニーレコードが、追い越し更新されていないかを調べる
        // 更新前のシンフォニーレコードが、追い越し更新されていないかを調べる----
        
        // ----更新前の各ムーブメントレコードが、追い越し更新されていないかを調べる
        // 更新前の各ムーブメントレコードが、追い越し更新されていないかを調べる----
        
        // バリデーションチェック----
        $aryRetBody = $objOLA->getInfoFromOneOfSymphonyClasses($fxVarsIntSymphonyClassId,1);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001100";
            
            if( $aryRetBody[1] === 101 ){
                $intErrorType = 2;
                
                $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720304");
            }
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        //----レコードが増えたのか、少なくなったのか、を調べる
        $aryRowOfSymClassTable = $aryRetBody[4];
        $aryRowOfMovClassTable = $aryRetBody[5];
        
        $intNewMovementLength = count($aryMovement);
        $intNowMovementLength = count($aryRowOfMovClassTable);
        
        //レコードが増えたのか、少なくなったのか、を調べる----
        
        // ----シンフォニーを更新
        $arrayConfigForIUD = $arrayConfigForSymClassIUD;
        
        if( $fxVarsStrLT4UBody != 'T_'.$aryRowOfSymClassTable['LUT4U'] ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            $intErrorType = 2;
            
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720305");
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );        
        }
        
        $varSymphonyClassNo = $fxVarsIntSymphonyClassId;
        $update_tgt_row     = $aryRowOfSymClassTable;
        
        $update_tgt_row['SYMPHONY_CLASS_NO'] = $fxVarsIntSymphonyClassId;
        $update_tgt_row['SYMPHONY_NAME']     = $aryExecuteData['symphony_name'];
        $update_tgt_row['DESCRIPTION']       = $aryExecuteData['symphony_tips'];
        $update_tgt_row['DISUSE_FLAG']       = '0';
        $update_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];

        if( isset($aryExecuteData['ACCESS_AUTH'] ) ){
            $update_tgt_row['ACCESS_AUTH']       = $aryExecuteData['ACCESS_AUTH'];
        }else{
            $update_tgt_row['ACCESS_AUTH']="";
        }


        $tgtSource_row = $update_tgt_row;
        $sqlType = "UPDATE";
        
        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                            ,$sqlType
                                            ,"SYMPHONY_CLASS_NO"
                                            ,"C_SYMPHONY_CLASS_MNG"
                                            ,"C_SYMPHONY_CLASS_MNG_JNL"
                                            ,$arrayConfigForIUD
                                            ,$tgtSource_row);
        
        if( $retArray[0] === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001300";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];
        
        // ----履歴シーケンス払い出し
        $retArray = getSequenceValueFromTable('C_SYMPHONY_CLASS_MNG_JSQ', 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001400";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        else{
            $varJSeq = $retArray[0];
            $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
        }
        // 履歴シーケンス払い出し----
        
        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
        if( $retArray01[0] !== true || $retArray02[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001500";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($retArray01);
        unset($retArray02);
        // シンフォニーを更新----
        
        // ----ムーブメントを更新
        $arrayConfigForIUD = $arrayConfigForMovInsIUD;
        
        //----すでに存在しているレコード数だけ、まず更新する
        $intFocusIndex = 0;

        foreach($aryRowOfMovClassTable as $arySingleRowOfMovClassTable ){
            $update_tgt_row = $arySingleRowOfMovClassTable;
            if( array_key_exists($intFocusIndex, $aryMovement) === true ){

                $aryDataForMovement = $aryMovement[$intFocusIndex];

                if($aryDataForMovement['OPERATION_NO_IDBH'] != "")
                {
                    $tmpStrOpeNoIDBH = $aryDataForMovement['OPERATION_NO_IDBH'];
                    $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
                    if( $objIntNumVali->isValid($tmpStrOpeNoIDBH) === false ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00002600";
                        $intErrorType = 2;
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733109",array($intFocusIndex + 1,$tmpStrOpeNoIDBH));
                        
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                    unset($objIntNumVali);
    
                    $tmpAryRetBody = $objOLA->getInfoOfOneOperation($tmpStrOpeNoIDBH,1);
                    if( $tmpAryRetBody[1] !== null ){
                        // エラーフラグをON
                        // 例外処理へ
                        $strErrStepIdInFx="00002700";
                        
                        if( $tmpAryRetBody[1] == 101 ){
                            $intErrorType = 2;
                            
                            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733110",array($intFocusIndex + 1));
                            
                            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                        }
                    }
                }

                $update_tgt_row['MOVEMENT_CLASS_NO'] = $arySingleRowOfMovClassTable['MOVEMENT_CLASS_NO'];
                
                $update_tgt_row['MOVEMENT_SEQ']      = $intFocusIndex + 1;
                
                //----作業パターンとオーケストレータ
                $update_tgt_row['PATTERN_ID']        = $aryDataForMovement['PATTERN_ID'];
                $update_tgt_row['ORCHESTRATOR_ID']   = $aryDataForMovement['ORCHESTRATOR_ID'];
                //作業パターンとオーケストレータ----
                
                $update_tgt_row['NEXT_PENDING_FLAG'] = $aryDataForMovement['NEXT_PENDING_FLAG']; //----1あり/2なし
                $update_tgt_row['DESCRIPTION']       = $aryDataForMovement['DESCRIPTION'];

                $update_tgt_row['OPERATION_NO_IDBH'] = $aryDataForMovement['OPERATION_NO_IDBH'];

                $update_tgt_row['DISUSE_FLAG']       = '0';
                $update_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];
            }
            else{
                //----MOV数が少なくなった場合なので、廃止する
                $update_tgt_row['MOVEMENT_CLASS_NO'] = $arySingleRowOfMovClassTable['MOVEMENT_CLASS_NO'];
                
                $update_tgt_row['DISUSE_FLAG']       = '1';
                $update_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];
                //MOV数が少なくなった場合なので、廃止する----
            }

            $tgtSource_row = $update_tgt_row;
            $sqlType = "UPDATE";
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                ,$sqlType
                                                ,"MOVEMENT_CLASS_NO"
                                                ,"C_MOVEMENT_CLASS_MNG"
                                                ,"C_MOVEMENT_CLASS_MNG_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);
            
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001600";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            $retArray = getSequenceValueFromTable('C_MOVEMENT_CLASS_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001700";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varJSeq = $retArray[0];
                $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            // 履歴シーケンス払い出し----
            
            $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
            if( $retArray01[0] !== true || $retArray02[0] !== true ){
                // エラーフラグをON
                $strErrStepIdInFx="00001800";
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);
            // ムーブメントを更新----
            $intFocusIndex += 1;
        }
        //すでに存在しているレコード数だけ、まず更新する-----
        
        if( $intNowMovementLength < $intNewMovementLength ){
            //----MOV数が増えた場合は、増えた分、レコードを追加する
            $intSaveFocusIndex = $intFocusIndex;
            for( $intFocusIndex = $intSaveFocusIndex ; $intFocusIndex < $intNewMovementLength ; $intFocusIndex++ ){
                $retArray = getSequenceValueFromTable('C_MOVEMENT_CLASS_MNG_RIC', 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00001900";
                    
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                else{
                    $varRISeq = $retArray[0];
                }
                $aryDataForMovement = $aryMovement[$intFocusIndex];
                
                $register_tgt_row = array();
                $register_tgt_row['MOVEMENT_CLASS_NO'] = $varRISeq;
                
                $register_tgt_row['MOVEMENT_SEQ']      = $intFocusIndex + 1;
                
                $register_tgt_row['PATTERN_ID']        = $aryDataForMovement['PATTERN_ID'];
                $register_tgt_row['ORCHESTRATOR_ID']   = $aryDataForMovement['ORCHESTRATOR_ID'];
                
                $register_tgt_row['NEXT_PENDING_FLAG'] = $aryDataForMovement['NEXT_PENDING_FLAG']; //----1あり/2なし
                $register_tgt_row['DESCRIPTION']       = $aryDataForMovement['DESCRIPTION'];
                $register_tgt_row['SYMPHONY_CLASS_NO'] = $varSymphonyClassNo;

                $register_tgt_row['OPERATION_NO_IDBH'] = $aryDataForMovement['OPERATION_NO_IDBH'];

                $register_tgt_row['DISUSE_FLAG']       = '0';
                $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];
                
                $tgtSource_row = $register_tgt_row;
                $sqlType = "INSERT";
                
                $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                    ,$sqlType
                                                    ,"SYMPHONY_CLASS_NO"
                                                    ,"C_MOVEMENT_CLASS_MNG"
                                                    ,"C_MOVEMENT_CLASS_MNG_JNL"
                                                    ,$arrayConfigForIUD
                                                    ,$tgtSource_row);
                
                if( $retArray[0] === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002000";
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                
                $sqlUtnBody = $retArray[1];
                $arrayUtnBind = $retArray[2];
                
                $sqlJnlBody = $retArray[3];
                $arrayJnlBind = $retArray[4];
                
                // ----履歴シーケンス払い出し
                $retArray = getSequenceValueFromTable('C_MOVEMENT_CLASS_MNG_JSQ', 'A_SEQUENCE', FALSE );
                if( $retArray[1] != 0 ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002100";
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                else{
                    $varJSeq = $retArray[0];
                    $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
                }
                // 履歴シーケンス払い出し----
                
                $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
                $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
                if( $retArray01[0] !== true || $retArray02[0] !== true ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002200";
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                unset($retArray01);
                unset($retArray02);
            }
            //MOV数が増えた場合は、増えた分、レコードを追加する----
        }
        
        //MOV数が増えた場合は、増えた分、レコードを追加する----
        
        // ----代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする
        $sql = "UPDATE A_PROC_LOADED_LIST "
               ."SET LOADED_FLG = :LOADED_FLG, LAST_UPDATE_TIMESTAMP = :LAST_UPDATE_TIMESTAMP "
               ."WHERE ROW_ID IN (2100020002,2100020004,2100020006,2100080002)";

        $objDBCA->setQueryTime();
        $aryForBind = array('LOADED_FLG' => "0", 'LAST_UPDATE_TIMESTAMP' => $objDBCA->getQueryTime());

        // SQL実行
        $retArray = singleSQLCoreExecute($objDBCA, $sql, $aryForBind, "");
        if( $retArray[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00002201";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // 代入値自動登録設定のbackyard処理の処理済みフラグをOFFにする----

        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            $strErrStepIdInFx="00002300";
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----
        
        $retBool = true;
        $intSymphonyClassId = $varSymphonyClassNo;
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-101080");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101010");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-101090");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101020");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $retArray = array($strResultCode,
                      $strDetailCode,
                      $intSymphonyClassId,
                      nl2br($strExpectedErrMsgBodyForUI)
                      );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//ある１のシンフォニーの定義を更新する----

//----ある１のシンフォニーの定義を新規登録（追加）する
function symphonyClassRegisterExecute($fxVarsIntSymphonyClassId ,$fxVarsAryReceptData, $fxVarsStrSortedData, $fxVarsStrLT4UBody){
    // グローバル変数宣言
    global $g;
    $arrayResult = array();
    $strResultCode = "";
    $strDetailCode = "000";
    $intSymphonyClassId = '';
    $strExpectedErrMsgBodyForUI = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $intErrorType = null;
    $intDetailType = null;
    $aryErrMsgBody = array();
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $aryConfigForSymClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYMPHONY_CLASS_NO"=>"",
        "SYMPHONY_NAME"=>"",
        "DESCRIPTION"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $arySymClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "SYMPHONY_CLASS_NO"=>"",
        "SYMPHONY_NAME"=>"",
        "DESCRIPTION"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $arrayConfigForMovClassIUD = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_CLASS_NO"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "MOVEMENT_SEQ"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "DESCRIPTION"=>"",
        "SYMPHONY_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    ); 
    
    $aryMovClassValueTmpl = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "MOVEMENT_CLASS_NO"=>"",
        "ORCHESTRATOR_ID"=>"",
        "PATTERN_ID"=>"",
        "MOVEMENT_SEQ"=>"",
        "NEXT_PENDING_FLAG"=>"",
        "DESCRIPTION"=>"",
        "SYMPHONY_CLASS_NO"=>"",
        "OPERATION_NO_IDBH"=>"",
        "ACCESS_AUTH"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    
    $strSysErrMsgBody = "";
    $boolInTransactionFlag = false;
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);

        //----バリデーションチェック(入力形式)
        $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>false));
        if( $objIntNumVali->isValid($fxVarsIntSymphonyClassId) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000100";
            //
            $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5720402",array($objIntNumVali->getValidRule()));
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($objIntNumVali);
        
        $objSLTxtVali = new SingleTextValidator(0,128,false);
        if( $objSLTxtVali->isValid($fxVarsStrLT4UBody) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            $strErrStepIdInFx="00000200";
            //
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5720403",array($objSLTxtVali->getValidRule()));
        }
        unset($objSLTxtVali);
        
        $aryExecuteData = convertReceptDataToDataForIUD($fxVarsAryReceptData);
        $aryRetBody = sortedDataDecodeForEdit($fxVarsStrSortedData);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            //
            $intErrorType = $aryRetBody[1];
            if( $aryRetBody[1] < 500 ){
                $strExpectedErrMsgBodyForUI = implode("\n",$aryRetBody[2]);
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $tmpAryMovement = $aryRetBody[0];
        //----バリデーションチェック
        $aryRetBody = validationCheckForTableIUD($tmpAryMovement, $aryExecuteData, $fxVarsIntSymphonyClassId);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000400";
            //
            $intErrorType = $aryRetBody[1];
            if( $aryRetBody[1] < 500 ){
                $strExpectedErrMsgBodyForUI = implode("\n",$aryRetBody[2]);
            }
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        //バリデーションチェック----
        //バリデーションチェック(入力形式)----
        $aryRetBody = dataConvertForTableIUD($tmpAryMovement, $aryExecuteData);
        if( $aryRetBody[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000500";
            throw new Exception( '01000300-([FILE]' . __FILE__ . ',[LINE]' . __LINE__ . ')' );
        }
        $aryMovement = $aryRetBody[0];
        unset($tmpAryMovement);
        
        // ----トランザクション開始
        $varTrzStart = $objDBCA->transactionStart();
        if( $varTrzStart === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000600";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $boolInTransactionFlag = true;
        // トランザクション開始----
        
        // ----シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する（デッドロックを防ぐために、値昇順序））
        
        // ----MOV-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_MOVEMENT_CLASS_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $retArray = getSequenceLockInTrz('C_MOVEMENT_CLASS_MNG_RIC','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000800";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // MOV-CLASS-シーケンスを掴む----
        
        // ----SYM-CLASS-シーケンスを掴む
        $retArray = getSequenceLockInTrz('C_SYMPHONY_CLASS_MNG_JSQ','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000900";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $retArray = getSequenceLockInTrz('C_SYMPHONY_CLASS_MNG_RIC','A_SEQUENCE');
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001000";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        // -SYM-CLASS-シーケンスを掴む----
        
        // シンフォニーとムーブメントのCUR/JNLの、シーケンスを取得する（デッドロックを防ぐために、値昇順序））----
        
        // ----シンフォニーを更新
        
        $register_tgt_row = $arySymClassValueTmpl;
        
        $retArray = getSequenceValueFromTable('C_SYMPHONY_CLASS_MNG_RIC', 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001100";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        else{
            $varRISeq = $retArray[0];
        }
        $varSymphonyClassNo = $varRISeq;
        $register_tgt_row['SYMPHONY_CLASS_NO'] = $varRISeq;
        $register_tgt_row['SYMPHONY_NAME']     = $aryExecuteData['symphony_name'];
        $register_tgt_row['DESCRIPTION']       = $aryExecuteData['symphony_tips'];
        $register_tgt_row['DISUSE_FLAG']       = '0';
        $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];
        
        
        if( isset($aryExecuteData['ACCESS_AUTH'] ) ){
            $register_tgt_row['ACCESS_AUTH']       = $aryExecuteData['ACCESS_AUTH'];
        }else{
            $register_tgt_row['ACCESS_AUTH']="";
        }

        $arrayConfigForIUD = $aryConfigForSymClassIUD;
        $tgtSource_row = $register_tgt_row;
        $sqlType = "INSERT";
        
        $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                            ,$sqlType
                                            ,"SYMPHONY_CLASS_NO"
                                            ,"C_SYMPHONY_CLASS_MNG"
                                            ,"C_SYMPHONY_CLASS_MNG_JNL"
                                            ,$arrayConfigForIUD
                                            ,$tgtSource_row);
        
        if( $retArray[0] === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001200";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $sqlUtnBody = $retArray[1];
        $arrayUtnBind = $retArray[2];
        
        $sqlJnlBody = $retArray[3];
        $arrayJnlBind = $retArray[4];
        
        // ----履歴シーケンス払い出し
        $retArray = getSequenceValueFromTable('C_SYMPHONY_CLASS_MNG_JSQ', 'A_SEQUENCE', FALSE );
        if( $retArray[1] != 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001300";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        else{
            $varJSeq = $retArray[0];
            $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
        }
        // 履歴シーケンス払い出し----
        
        $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
        $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
        if( $retArray01[0] !== true || $retArray02[0] !== true ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001400";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        unset($retArray01);
        unset($retArray02);
        // シンフォニーを更新----
        
        // ----ムーブメントを登録
        $intFocusIndex = 0;
        foreach($aryMovement as $aryDataForMovement){
            // ----ムーブメントを更新
            $register_tgt_row = $aryMovClassValueTmpl;
            
            $retArray = getSequenceValueFromTable('C_MOVEMENT_CLASS_MNG_RIC', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001500";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varRISeq = $retArray[0];
            }
            
            if($aryDataForMovement['OPERATION_NO_IDBH'] != "")
            {
                $tmpStrOpeNoIDBH = $aryDataForMovement['OPERATION_NO_IDBH'];
                $objIntNumVali = new IntNumValidator(null,null,"",array("NOT_NULL"=>true));
                if( $objIntNumVali->isValid($tmpStrOpeNoIDBH) === false ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002600";
                    $intErrorType = 2;
                    $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733109",array($intFocusIndex + 1,$tmpStrOpeNoIDBH));
                    //
                    throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                }
                unset($objIntNumVali);
 
                $tmpAryRetBody = $objOLA->getInfoOfOneOperation($tmpStrOpeNoIDBH,1);
                if( $tmpAryRetBody[1] !== null ){
                    // エラーフラグをON
                    // 例外処理へ
                    $strErrStepIdInFx="00002700";
                    //
                    if( $tmpAryRetBody[1] == 101 ){
                        $intErrorType = 2;
                        //
                        $strExpectedErrMsgBodyForUI = $objMTS->getSomeMessage("ITABASEH-ERR-5733110",array($intFocusIndex + 1));
                        //
                        throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
                    }
                }
            }

            $register_tgt_row = array();
            $register_tgt_row['MOVEMENT_CLASS_NO'] = $varRISeq;
            
            $register_tgt_row['MOVEMENT_SEQ']      = $intFocusIndex + 1;
            
            $register_tgt_row['PATTERN_ID']        = $aryDataForMovement['PATTERN_ID'];
            $register_tgt_row['ORCHESTRATOR_ID']   = $aryDataForMovement['ORCHESTRATOR_ID'];
            
            $register_tgt_row['NEXT_PENDING_FLAG'] = $aryDataForMovement['NEXT_PENDING_FLAG'];
            $register_tgt_row['DESCRIPTION']       = $aryDataForMovement['DESCRIPTION'];
            $register_tgt_row['SYMPHONY_CLASS_NO'] = $varSymphonyClassNo;

            $register_tgt_row['OPERATION_NO_IDBH'] = $aryDataForMovement['OPERATION_NO_IDBH'];

            $register_tgt_row['DISUSE_FLAG']       = '0';
            $register_tgt_row['LAST_UPDATE_USER']  = $g['login_id'];
            
 
            $arrayConfigForIUD = $arrayConfigForMovClassIUD;
            $tgtSource_row = $register_tgt_row;
            $sqlType = "INSERT";
            
            $retArray = makeSQLForUtnTableUpdate($g['db_model_ch']
                                                ,$sqlType
                                                ,"MOVEMENT_CLASS_NO"
                                                ,"C_MOVEMENT_CLASS_MNG"
                                                ,"C_MOVEMENT_CLASS_MNG_JNL"
                                                ,$arrayConfigForIUD
                                                ,$tgtSource_row);
            
            if( $retArray[0] === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001600";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            
            $sqlUtnBody = $retArray[1];
            $arrayUtnBind = $retArray[2];
            
            $sqlJnlBody = $retArray[3];
            $arrayJnlBind = $retArray[4];
            
            // ----履歴シーケンス払い出し
            $retArray = getSequenceValueFromTable('C_MOVEMENT_CLASS_MNG_JSQ', 'A_SEQUENCE', FALSE );
            if( $retArray[1] != 0 ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001700";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            else{
                $varJSeq = $retArray[0];
                $arrayJnlBind['JOURNAL_SEQ_NO'] = $varJSeq;
            }
            // 履歴シーケンス払い出し----
            
            $retArray01 = singleSQLCoreExecute($objDBCA, $sqlUtnBody, $arrayUtnBind, $strFxName);
            $retArray02 = singleSQLCoreExecute($objDBCA, $sqlJnlBody, $arrayJnlBind, $strFxName);
            if( $retArray01[0] !== true || $retArray02[0] !== true ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00001800";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            unset($retArray01);
            unset($retArray02);      
            
            $intFocusIndex += 1;
            
            // ムーブメントを更新----
        }
        // ムーブメントを登録----
        
        // ----トランザクション終了
        $boolResult = $objDBCA->transactionCommit();
        if ( $boolResult === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00001700";
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $objDBCA->transactionExit();
        $boolInTransactionFlag = false;
        // トランザクション終了----

        $retBool = true;
        $intSymphonyClassId = $varSymphonyClassNo;
    }
    catch (Exception $e){
        //----トランザクション中のエラーの場合
        if( $boolInTransactionFlag === true){
            if( $objDBCA->transactionRollBack() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102010");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101030");
            }
            web_log($tmpMsgBody);
            
            // トランザクション終了
            if( $objDBCA->transactionExit() === true ){
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-STD-102020");
            }
            else{
                $tmpMsgBody = $objMTS->getSomeMessage("ITABASEH-ERR-101040");
            }
            web_log($tmpMsgBody);
            unset($tmpMsgBody);
        }
        //トランザクション中のエラーの場合----
        
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
        foreach($aryErrMsgBody as $strFocusErrMsg){
            web_log($strFocusErrMsg);
        }
    }
    $strResultCode = sprintf("%03d", $intErrorType);
    $strDetailCode = sprintf("%03d", $intDetailType);
    $retArray = array($strResultCode,
                      $strDetailCode,
                      $intSymphonyClassId,
                      nl2br($strExpectedErrMsgBodyForUI)
                      );
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
//ある１のシンフォニーの定義を新規登録（追加）する----

function dataConvertForTableIUD($aryMovementOfRawRecept, $aryExecuteData){
    global $g;
    $aryMovementForSqlExecute = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    try{
        if( 0 == count($aryMovementOfRawRecept) ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721102");
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        foreach( $aryMovementOfRawRecept as $aryMovement ){
            //----説明の改行コードを統一
            $varValue = $aryMovement['DESCRIPTION'];
            $strConvedValue = str_replace(array("\r\n","\r"),"\n",$varValue);
            $aryMovement['DESCRIPTION'] = $varValue;
            //説明の改行コードを統一----
            
            //----保留ポイント有無
            if( $aryMovement['NEXT_PENDING_FLAG'] == 'checkedValue' ){
                $aryMovement['NEXT_PENDING_FLAG'] = '1';
            }
            else{
                $aryMovement['NEXT_PENDING_FLAG'] = '2';
            }
            //保留ポイント有無----
            $aryMovementForSqlExecute[] = $aryMovement;
        }
        //ムーブメントのチェック----
        
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($aryMovementForSqlExecute,$intErrorType,$aryErrMsgBody,$strErrMsg);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}

function validationCheckForTableIUD($aryMovementOfRawRecept, $aryExecuteData, $fxVarsIntSymphonyClassId){
    global $g;
    $retBool = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    try{
        $strSymphonyName = '';
        if( array_key_exists("symphony_name",$aryExecuteData) === true ){
            $strSymphonyName = $aryExecuteData["symphony_name"];
        }
        $objSLTxtVali = new SingleTextValidator(1,256,false);
        if( $objSLTxtVali->isValid($strSymphonyName) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721202",array($objSLTxtVali->getValidRule()));
        }
        else{
            // Symphonyクラス名称の一意制約の確認
            $query  = "SELECT SYMPHONY_CLASS_NO,SYMPHONY_NAME ";
            $query .= "FROM C_SYMPHONY_CLASS_MNG ";
            $query .= "WHERE DISUSE_FLAG='0' ";

            $arrayRows = array();
            $row_counter = 0;
            $retArray = singleSQLExecuteAgent($query, array(), $strFxName);
            if( $retArray[0] === true ){
                $objQuery = $retArray[1];
                $count=0;
                // 行取得
                while( $row = $objQuery->resultFetch() ){
                    if( $row !== false ){
                        $count+=1;
                        $arrayRows[] = $row;
                    }
                }
                // ----レコード数を取得
                $row_counter = $count;
                // レコード数を取得----
                unset($objQuery);
            }
            else{
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = 500;
                web_log("SQL ERROR. SQL=[$query], ERROR=[" . $retArray[2] . "]");
                throw new Exception("FILE: " . __FILE__ . ", LINE: " . __LINE__);
            }
            if($row_counter == 0){
                //----1行も存在しなかった
                //1行も存在しなかった----
            }else{
                $symphonyClassNoArray = array();
                foreach($arrayRows as $data){
                    if($fxVarsIntSymphonyClassId != $data['SYMPHONY_CLASS_NO'] && $strSymphonyName === $data['SYMPHONY_NAME']){
                        $symphonyClassNoArray[] = $data['SYMPHONY_CLASS_NO'];
                    }
                }

                if(0 < count($symphonyClassNoArray)){
                    // エラーフラグをON
                    $intErrorType = 2;

                    $tmpMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-603",array($g['objMTS']->getSomeMessage("ITABASEH-MNU-109040"), implode(",", $symphonyClassNoArray)));
                    $tmpMsg .= "[(" . $g['objMTS']->getSomeMessage("ITABASEH-MNU-109070") . ")]";
                    $aryErrMsgBody[] = $tmpMsg;
                }
            }
        }
        unset($objSLTxtVali);
        
        $strSymphonyTips = '';
        if( array_key_exists("symphony_tips",$aryExecuteData) === true ){
            $strSymphonyTips = $aryExecuteData["symphony_tips"];
        }
        $objMLTxtVali = new MultiTextValidator(0,4000);
        if( $objMLTxtVali->isValid($strSymphonyTips) === false ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = 2;
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721203",array($objMLTxtVali->getValidRule()));
        }
        unset($objMLTxtVali);
        
        if( 0 == count($aryMovementOfRawRecept) ){
            // エラーフラグをON
            $intErrorType = 2;
            //
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721204");
        }
        
        //----ムーブメントのチェック
        $aryRetBody = getPatternListWithOrchestratorInfo("",-1);
        if( $aryRetBody[1] !== null ){
            $intErrorType = $aryRetBody[1];
            $aryErrMsgBody = $aryRetBody[2];
        }
        $aryPatternList = $aryRetBody[4];
        
        $objMLTxtVali = new MultiTextValidator(0,4000);
        foreach( $aryMovementOfRawRecept as $aryMovement ){
            //----作業パターンIDとオーケストレータIDの存在をチェック
            $strPatternIdOfFocusMovement = $aryMovement['PATTERN_ID'];
            if( array_key_exists($strPatternIdOfFocusMovement,$aryPatternList) === true ){
                $tmpRow = $aryPatternList[$strPatternIdOfFocusMovement];
                if( $aryMovement['ORCHESTRATOR_ID'] != $tmpRow['ORCHESTRATOR_ID'] ){
                    // エラーフラグをON
                    $intErrorType = 2;
                    
                    $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721205",array($aryMovement['MOVEMENT_SEQ'],$strPatternIdOfFocusMovement));
                }
            }
            else{
                // エラーフラグをON
                $intErrorType = 2;
                
                $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721206",$aryMovement['MOVEMENT_SEQ']);
            }
            //作業パターンIDとオーケストレータIDの存在をチェック----
            
            //----保留ポイントの設置値のチェック
            if( $aryMovement['NEXT_PENDING_FLAG'] != 'checkedValue' && strlen($aryMovement['NEXT_PENDING_FLAG']) !== 0 ){
                // エラーフラグをON
                $intErrorType = 2;
                
                $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721207",$aryMovement['MOVEMENT_SEQ']);
            }
            //保留ポイントの設置値のチェック----
            
            //----説明欄のチェック
            if( $objMLTxtVali->isValid($aryMovement['DESCRIPTION']) === false ){
                // エラーフラグをON
                $intErrorType = 2;
                
                $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721208",array($aryMovement['MOVEMENT_SEQ'],$objMLTxtVali->getValidRule()));
            }
            //説明欄のチェック----
            $aryMovementForSqlExecute[] = $aryMovement;
        }
        unset($objMLTxtVali);
        //----ムーブメントのチェック
        $retBool = true;
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 2;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($retBool,$intErrorType,$aryErrMsgBody,$strErrMsg);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}

function sortedDataDecodeForEdit($fxVarsStrSortedData){
    global $g;
    $aryMovement = array();
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $arySettingForParse = array(
        0=>'MOVEMENT_SEQ'
        ,1=>'ORCHESTRATOR_ID'
        ,2=>'PATTERN_ID'
        ,3=>'NEXT_PENDING_FLAG'
        ,4=>'DESCRIPTION'
        ,5=>'OPERATION_NO_IDBH'
    );
    
    $strSysErrMsgBody = "";
    
    try{
        if( is_string($fxVarsStrSortedData) === false ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000100";
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721302");
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        $aryMovementSortBase = getArrayBySafeSeparator($fxVarsStrSortedData);
        
        if( is_array($aryMovementSortBase) === false ){
            $aryMovementSortBase = array();
        }
        
        //----ループ
        $intFvn1 = 0;
        $intLengthArySettingForParse = count($arySettingForParse);
        $aryMovement = array();
        foreach( $aryMovementSortBase as $value ){
            if( array_key_exists($intFvn1, $arySettingForParse) === false ){
                // エラーフラグをON
                // 例外処理へ
                $strErrStepIdInFx="00000200";
                //
                $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721303");
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strFocusParseKey = $arySettingForParse[$intFvn1];
            $arySingleMovement[$strFocusParseKey] = $value;
            
            $intFvn1 += 1;
            if( $intFvn1 == $intLengthArySettingForParse ){
                $aryMovement[] = $arySingleMovement;
                $arySingleMovement = array();
                $intFvn1 = 0;
            }
        }
        //ループ----
        if( $intFvn1 !== 0 ){
            // エラーフラグをON
            // 例外処理へ
            $strErrStepIdInFx="00000300";
            
            $aryErrMsgBody[] = $objMTS->getSomeMessage("ITABASEH-ERR-5721304");
            //
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
    }
    catch(Exception $e){
        if( $intErrorType === null ) $intErrorType = 2;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($aryMovement,$intErrorType,$aryErrMsgBody,$strErrMsg);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}

function getPatternListWithOrchestratorInfo($fxVarsStrFilterData="",$fxVarsResultType=0){
    global $g;
    $boolRet = false;
    $intErrorType = null;
    $aryErrMsgBody = array();
    $strErrMsg = "";
    $aryListSource = array();
    
    $intControlDebugLevel01=250;
    
    $objMTS = $g['objMTS'];
    $objDBCA = $g['objDBCA'];
    
    $strFxName = '([FUNCTION]'.__FUNCTION__.')';
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    
    $strSysErrMsgBody = "";
    
    try{
        require_once($g['root_dir_path']."/libs/commonlibs/common_ola_classes.php");
        $objOLA = new OrchestratorLinkAgent($objMTS,$objDBCA);
        $obj = new RoleBasedAccessControl($objDBCA);

        $aryRet = $objOLA->getLiveOrchestratorFromMaster();
        if( $aryRet[1] !== null ){
            // エラーフラグをON
            // 例外処理へ
            $intErrorType = $aryRet[1];
            $strErrStepIdInFx="00000100";
            
            throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
        }
        
        $aryOrcListRow = $aryRet[0];
        
        $boolBinaryDistinctOnDTiS = false;
        
        //オーケストレータ情報の収集----
        
        //----存在するオーケスト—タ分回る
        foreach($aryOrcListRow as $arySingleOrcInfo){
            $varOrcId = $arySingleOrcInfo['ITA_EXT_STM_ID'];
            $varOrcRPath = $arySingleOrcInfo['ITA_EXT_LINK_LIB_PATH'];
            
            $objOLA->addFuncionsPerOrchestrator($varOrcId,$varOrcRPath);
            
            $aryRet = $objOLA->getLivePatternList($varOrcId,$fxVarsStrFilterData,$boolBinaryDistinctOnDTiS);
            if( $aryRet[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = $aryRet[1];
                $strErrStepIdInFx="00000200";
                //
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            //
            $aryRow = $aryRet[0];
            
            //----オーケストレータカラーを取得
            $aryRet = $objOLA->getThemeColorName($varOrcId);
            if( $aryRet[1] !== null ){
                // エラーフラグをON
                // 例外処理へ
                $intErrorType = $aryRet[1];
                $strErrStepIdInFx="00000300";
                
                throw new Exception( $strFxName.'-'.$strErrStepIdInFx.'-([FILE]'.__FILE__.',[LINE]'.__LINE__.')' );
            }
            $strThemeColor = $aryRet[0];
            //オーケストレータカラーを取得----
            
            if( $fxVarsResultType === 1 ){
                foreach($aryRow as $arySingleRow){

                    $intPatternId = $arySingleRow['PATTERN_ID'];
                    //
                    // 表示データをSELECT
                    $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                           ." WHERE DISUSE_FLAG='0' "
                           ." AND PATTERN_ID = $intPatternId "
                           ."";
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    $rows = array();

                    $row = $objQuery->resultFetch();

                    $user_id = $g['login_id'];
                    $ret  = $obj->getAccountInfo($user_id); 
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                    } else {
                        if($permission === true) {
                            $aryListSource[] = $varOrcId;
                            $aryListSource[] = $arySingleRow['PATTERN_ID'];
                            $aryListSource[] = $arySingleRow['PATTERN_NAME'];
                            $aryListSource[] = $strThemeColor;
                        }
                    }
                }
            }
            else{
                foreach($aryRow as $arySingleRow){
                    $tmpRow = array();
                    $intPatternId = $arySingleRow['PATTERN_ID'];
                    //
                    $tmpRow['PATTERN_ID']      = $intPatternId;
                    $tmpRow['ORCHESTRATOR_ID'] = $varOrcId;
                    $tmpRow['PATTERN_NAME']    = $arySingleRow['PATTERN_NAME'];
                    $tmpRow['ThemeColor']      = $strThemeColor;
                    //
                    // 表示データをSELECT
                    $sql =  " SELECT * FROM C_PATTERN_PER_ORCH "
                           ." WHERE DISUSE_FLAG='0' "
                           ." AND PATTERN_ID = $intPatternId "
                           ."";
                    $objQuery = $objDBCA->sqlPrepare($sql);
                    $r = $objQuery->sqlExecute();
                    $rows = array();

                    $row = $objQuery->resultFetch();

                    $user_id = $g['login_id'];
                    $ret  = $obj->getAccountInfo($user_id); 
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);

                    if($ret === false) {
                    } else {
                        if($permission === true) {
                            $aryListSource[$intPatternId] = $tmpRow;
                        }
                    }
                }
            }
        }
        //存在するオーケスト—タ分回る----
    }
    catch (Exception $e){
        // エラーフラグをON
        if( $intErrorType === null ) $intErrorType = 500;
        $tmpErrMsgBody = $e->getMessage();
        if( 500 <= $intErrorType ) $strSysErrMsgBody = $objMTS->getSomeMessage("ITAWDCH-ERR-4011",array($strFxName,$tmpErrMsgBody));
        if( 0 < strlen($strSysErrMsgBody) ) web_log($strSysErrMsgBody);
    }
    $retArray = array($boolRet,$intErrorType,$aryErrMsgBody,$strErrMsg,$aryListSource);
    dev_log($objMTS->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
    return $retArray;
}
?>
