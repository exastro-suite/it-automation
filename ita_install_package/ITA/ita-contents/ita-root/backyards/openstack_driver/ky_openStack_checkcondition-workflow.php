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
    //  【概要】
    //      OpenStack実行監視
    //
    //////////////////////////////////////////////////////////////////////

//ステータスIDの遷移

     //MNGプロセス
    const CONSTRUCT=1;  // 未実施
    const RESERVE=2;    // 予約中
    const PREPARE=3;    // 準備中（実行君が２にする）
    const EXECUTE_AND_WAITING_COMPLETE=4;    // 子プロセスそれぞれのHEATを投げました。完了待ちです。

      //DETAILプロセス
      const CHILD_FAILED_BY_SCRAM=0;    // 緊急停止ボタンが押された結果、処理をすべてキャンセルしました。
      const CHILD_WAITING_RESPONSE=1;   // HEATの読み込みは完了しました。記載された作業が完了したか、確認中です。
      const CHILD_FAILED_BY_HEAT=2;     // 結果がわかりました。ダメでした。（HEATがイケてない）
      const CHILD_FAILED_BY_OTHER=3;    // 結果が分かりました。なんか別の理由で失敗しました。
      const CHILD_SUCCESS=4;            // 結果が分かりました。成功しました。

    const SCRAM=5;             // 緊急停止ボタンが押されました。
    const SCRAM_COMPLETE=6;    // 緊急停止ボタンが押されました。処理完了しました。
    const FAILURE=7;           // 子プロセスが全部ステータス（３か４）まで進みました。全部失敗しました（これ必要か？）
    const PARTIAL_FAILURE=8;   // 子プロセスが全部ステータス（３か４）まで進みました。一部失敗したやつがいました。
    const COMPLETE=9;          // 子プロセスが全部ステータス（３か４）まで進みました。全部成功です。
    const RESERVE_CANCEL=10;   // 予約キャンセル（便宜上記載したが、本ステータスを確認君が処理することはない


    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) ) {
        $root_dir_temp = array();
        $root_dir_temp = explode( "ita-root", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "ita-root";
    }

    ////////////////////////////////
    // $log_output_dirを取得      //
    ////////////////////////////////
    $log_output_dir = getenv('LOG_DIR');

    ////////////////////////////////
    // $log_file_prefixを作成     //
    ////////////////////////////////
    $log_file_prefix = basename( __FILE__, '.php' ) . "_";

    ////////////////////////////////
    // $log_levelを取得           //
    ////////////////////////////////
    $log_level = getenv('LOG_LEVEL'); // 'DEBUG';

    // PHP エラー時のログ出力先を設定
    $tmpVarTimeStamp = time();
    $logfile = $log_output_dir . "/" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . ".log";

    ini_set('display_errors','On');
    ini_set('log_errors',1);
    ini_set('error_log',$logfile);

    ////////////////////////////////
    // 定数定義                   //
    ////////////////////////////////
    $log_output_php      = '/libs/backyardlibs/backyard_log_output.php';
    $php_req_gate_php    = '/libs/commonlibs/common_php_req_gate.php';
    $db_connect_php      = '/libs/commonlibs/common_db_connect.php';
    $php_functions_php   = '/libs/commonlibs/common_php_functions.php';
    require_once($root_dir_path . $php_functions_php);
    $db_access_user_id   = -100901;

    // Openstack用REST
    require_once($root_dir_path."/libs/backyardlibs/openstack_driver/openStack_RESTCallLib.php");

    ////////////////////////////////
    // ローカル変数(全体)宣言     //
    ////////////////////////////////
    $warning_flag               = 0;        // 警告フラグ(1：警告発生)
    $error_flag                 = 0;        // 異常フラグ(1：異常発生)

    ////////////////////////////////
    // 業務処理開始               //
    ////////////////////////////////

    try {
        ////////////////////////////////
        // 共通モジュールの呼び出し   //
        ////////////////////////////////
        $aryOrderToReqGate = array('DBConnect'=>'LATE');
        require ($root_dir_path . $php_req_gate_php );

        // 開始メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55001");
            require ($root_dir_path . $log_output_php );
        }

        ////////////////////////////////
        // DBコネクト                 //
        ////////////////////////////////
        require ($root_dir_path . $db_connect_php );
        // トレースメッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55003");
            require ($root_dir_path . $log_output_php );
        }

        //作業レコードリスト；
        $executeList=array();
        //作業が準備中、ステータス確認中、緊急停止要請中のものを取得。
        if(1){

            if ( $log_level === 'DEBUG' ) {
                //対象作業リスト取得開始
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56000");
                require ($root_dir_path . $log_output_php );
            }

            $sqlUtnBody = "SELECT * "
                         ."FROM C_OPENST_RESULT_MNG TAB_1 "
                         ."WHERE TAB_1.DISUSE_FLAG = '0' AND STATUS_ID=:STATUS_ID OR STATUS_ID= :STATUS_ID2 OR STATUS_ID= :STATUS_ID3";
            $arrayUtnBind = array(
                "STATUS_ID"=>PREPARE,
                "STATUS_ID2"=>EXECUTE_AND_WAITING_COMPLETE,
                "STATUS_ID3"=>SCRAM
            );

            $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
            if( $objQueryUtn->getStatus()===false ) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
            }
            if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
            }
            $r = $objQueryUtn->sqlExecute();
            if (!$r) {
                $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                    $objQueryUtn->getLastError());
                require ($root_dir_path . $log_output_php );
                // 異常フラグON  例外処理へ
                $error_flag = 1;
                throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
            }
            while ( $row = $objQueryUtn->resultFetch() ) {
                $executeList[] = $row;
            }
        }

        if ( $log_level === 'DEBUG' ) {
            //対象作業ノード処理開始
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56010").count($executeList);
            require ($root_dir_path . $log_output_php );
        }

        //作業NO単位で実行
        foreach ($executeList as $key => $executeNode) {

            if ( $log_level === 'DEBUG' ) {
                //対象作業ノード:
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56020")
                ."[EXECUTION_NO]:".$executeNode['EXECUTION_NO']
                ."[STATUS_ID]:".$executeNode['STATUS_ID'];
                require ($root_dir_path . $log_output_php );
            }

            //////////////////////////////////////////////////////////////////
            // 投入オペレーションの最終実施日を更新する。
            //////////////////////////////////////////////////////////////////
            require_once($root_dir_path . "/libs/backyardlibs/common/common_db_access.php");
            $dbaobj = new BackyardCommonDBAccessClass($objDBCA->getModelChannel(),$objDBCA,$objMTS,$db_access_user_id);
            $ret = $dbaobj->OperationList_LastExecuteTimestamp_Update($executeNode["OPERATION_NO_UAPK"]);
            if($ret === false) {
                $FREE_LOG = $dbaobj->GetLastErrorMsg();
                require ($log_output_php );
                throw new Exception("OperationList update error.");
            }
            unset($dbaobj);

            //REST用の基礎データを格納
            $restData=[
                'templateData'=>"",         //テンプレートデータ。１作業NOで一つ。
                'projectList'=>[]
            ];

            //紐づくテンプレートを取得
            if($executeNode['STATUS_ID']==PREPARE) {

                if ( $log_level === 'DEBUG' ) {
                    //対象テンプレート取得
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56040");
                    require ($root_dir_path . $log_output_php );
                }

                $sqlUtnBody = "SELECT PATTERN_ID,OPENST_TEMPLATE,OPENST_ENVIRONMENT from E_OPENST_PATTERN WHERE PATTERN_ID = :PATTERN_ID";
                $arrayUtnBind = array("PATTERN_ID"=>$executeNode['PATTERN_ID']);

                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                }
                $r = $objQueryUtn->sqlExecute();
                if (!$r) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
                $patternNode="";
                while ( $row = $objQueryUtn->resultFetch() ) {
                    $patternNode = $row;
                }

                if($patternNode=="") {
                    //送信するテンプレートが存在しないのでエラー
                    throw new Exception($objMTS->getSomeMessage("ITAOPENST-ERR-102050"));
                }

                $fileId=str_pad($patternNode['PATTERN_ID'],10,0,STR_PAD_LEFT);
                $templateFile=$patternNode['OPENST_TEMPLATE'];
                $restData['templateData']=file_get_contents($root_dir_path."/uploadfiles/2100070002/OPENST_TEMPLATE/".$fileId."/".$templateFile);

                //環境設定ファイル読み出し（必須カラムではない）
                if($patternNode['OPENST_ENVIRONMENT']!="") {
                    // ファイル中身読み込み
                    $environmentFile=file_get_contents($root_dir_path."/uploadfiles/2100070002/OPENST_ENVIRONMENT/".$fileId."/".$patternNode['OPENST_ENVIRONMENT']);

                    // 文字列置換
                    $restData['templateData']=str_replace('user_data: ""','user_data: "'.$environmentFile.'"',$restData['templateData']);
                }
                unset($templateFile);
                unset($environmentFile);
            }

            //紐づく作業詳細それぞれに対して、紐づくJSONパラメータを取得。
            //MNGで取得した、OPERATIONNOと、PATTERN_IDで絞る。
            if($executeNode['STATUS_ID']==PREPARE) {

                if ( $log_level === 'DEBUG' ) {
                    //プロジェクト毎のJSON取得
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56050");
                    require ($root_dir_path . $log_output_php );
                }

                if ( $log_level === 'DEBUG' ) {
                    //対象作業ノード
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56060").$executeNode['PATTERN_ID'];
                    require ($root_dir_path . $log_output_php );
                }

                $sqlUtnBody = "SELECT SYSTEM_ID, VARS_ENTRY from B_OPENST_VARS_ASSIGN WHERE DISUSE_FLAG = '0' AND PATTERN_ID = :PATTERN_ID AND OPERATION_NO_UAPK = :OPERATION_NO_UAPK";
                $arrayUtnBind = array(
                    "PATTERN_ID"=>$executeNode['PATTERN_ID'],
                    "OPERATION_NO_UAPK"=>$executeNode['OPERATION_NO_UAPK'],
                    );

                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                }
                $r = $objQueryUtn->sqlExecute();
                if (!$r) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
                $patternNode="";
                while ( $row = $objQueryUtn->resultFetch() ) {
                    $rowData=$row['VARS_ENTRY'];
                    $rowData=str_replace("'","\"",$rowData);
                    array_push($restData['projectList'] , [
                        'SYSTEM_ID' => $row['SYSTEM_ID'],
                        'ASSIGN_VAR' => json_decode($rowData,true)
                    ]);
                }

                if ( $log_level === 'DEBUG' ) {
                    //対象作業ノード＿作業完了
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56070").$executeNode['PATTERN_ID'];
                    require ($root_dir_path . $log_output_php );
                }
            }

            //プロジェクトの数だけforを回し、変数を適用し、適用したテンプレートを格納する。
            if($executeNode['STATUS_ID']==PREPARE) {

                if ( $log_level === 'DEBUG' ) {
                    //プロジェクト毎のJSON取得
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56080");
                    require ($root_dir_path . $log_output_php );
                }

                //for project
                for ($i=0; $i <count($restData['projectList']) ; $i++) {

                    if ( $log_level === 'DEBUG' ) {
                        //対象作業ノード
                        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56090").$restData['projectList'][$i]['SYSTEM_ID'];
                        require ($root_dir_path . $log_output_php );
                    }

                    $convetedTemplate=$restData['templateData'];
                    $keytmp = [];
                    for ($j=0; $j <count($restData['projectList'][$i]['ASSIGN_VAR']) ; $j++) {

                        $varNode=$restData['projectList'][$i]['ASSIGN_VAR'][$j];

                        $target=$varNode['parameter'];
                        $replace="";
                        if($varNode['key']==='customKey'){
                            //カスタム値入力の場合、強制的にstringを使用すること
                            $replace=$varNode['value'];
                        }else{
                            //通常のセレクトボックスの場合
                            $replace=($varNode['select']==='string')?$varNode['key']:$varNode['value'];
                        }
                        $convetedTemplate=preg_replace("/".$target."/",$replace,$convetedTemplate,1);

                        //分類がfloatingipの場合
                        if($varNode['category']==='ip'){
                            //floatingip(key)値を格納
                            $keytmp[]=$varNode['key'];
                        }
                    }
                    $restData['projectList'][$i]['convertedTemplate']=$convetedTemplate;
                    unset($convetedTemplate);

                    $restData['projectList'][$i]['floatingip']=$keytmp;
                    unset($keytmp);

                    if ( $log_level === 'DEBUG' ) {
                        //対象作業ノード＿作業完了
                        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56100");
                        require ($root_dir_path . $log_output_php );
                    }
                } //for project end
            }

            // $executeNode['STATUS_ID']==PREPAREバージョン
            //この時点で、RESTに必要なパラメータは全て揃った。
            //厳密には、projectId毎にリクエストするURLが異なるわけだが、それは都度RESTで取得しなければいけない。
            //取得には、masterSyncバッチと同じ手法を使用する。
            //この時点でのパラメータ構成は下記。

            // $restData=[
            //     'templateData'=>"",              //テンプレートデータ。１作業NOで一つ。
            //     'projectList'=>[                 //projectId毎のパラメータセット。
            //         array(
            //         'SYSTEM_ID'=>***,            //projectId
            //         'ASSIGN_VAR'=>***,           //アサインする変数群
            //         'convertedTemplate'=>***,    //テンプレートに変数を適用したもの
            //         ),
            //         // .........
            //     ]
            // ];

            //紐づくプロジェクトそれぞれに対して、紐づくJSONパラメータを取得。
            //MNGで取得した、OPERATIONNOと、PATTERN_ID,今取得したSYSTEM_IDで絞る。
            if($executeNode['STATUS_ID']==EXECUTE_AND_WAITING_COMPLETE || $executeNode['STATUS_ID']==SCRAM) {

                if ( $log_level === 'DEBUG' ) {
                    //既存レコード取得開始
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56110");
                    require ($root_dir_path . $log_output_php );
                }

                if($executeNode['STATUS_ID']==EXECUTE_AND_WAITING_COMPLETE) {
                    $sqlUtnBody = "SELECT * from C_OPENST_RESULT_DETAIL WHERE EXECUTION_NO = :EXECUTION_NO AND STATUS_ID = :STATUS_ID";
                    $arrayUtnBind = array(
                        "EXECUTION_NO"=>$executeNode['EXECUTION_NO'],
                        "STATUS_ID"=>CHILD_WAITING_RESPONSE
                    );
                } else if($executeNode['STATUS_ID']==SCRAM) {
                    $sqlUtnBody = "SELECT * from C_OPENST_RESULT_DETAIL WHERE EXECUTION_NO = :EXECUTION_NO";
                    $arrayUtnBind = array(
                        "EXECUTION_NO"=>$executeNode['EXECUTION_NO']
                    );
                }

                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                }
                $r = $objQueryUtn->sqlExecute();
                if (!$r) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
                $patternNode="";
                while ( $row = $objQueryUtn->resultFetch() ) {

                    $restData['projectList'][]=[
                        'EXECUTION_NO'=>$row['EXECUTION_NO'],
                        'SYSTEM_ID'=>$row['SYSTEM_ID'],
                        'STACK_ID'=>$row['STACK_ID'],
                        'STATUS_ID'=>$row['STATUS_ID'],
                        'STACK_URL'=>$row['STACK_URL'],
                        'RESULT_DETAIL_ID'=>$row['RESULT_DETAIL_ID'],
                    ];
                }

                if ( $log_level === 'DEBUG' ) {
                    //既存レコード取得完了
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56120");
                    require ($root_dir_path . $log_output_php );
                }
            }

            // $executeNode['STATUS_ID']==EXECUTE_AND_WAITING_COMPLETEバージョン
            //この時点で、RESTに必要なパラメータは全て揃った。
            //厳密には、projectId毎にリクエストするURLが異なるわけだが、それは都度RESTで取得しなければいけない。
            //取得には、masterSyncバッチと同じ手法を使用する。
            //この時点でのパラメータ構成は下記。

            // $restData=[
            //     'templateData'=>"",              //テンプレートデータ。１作業NOで一つ。
            //     'projectList'=>[                 //projectId毎のパラメータセット。
            //         array(
            //         'EXECUTION_NO'=>***          //作業実行No
            //         'SYSTEM_ID'=>***,            //projectId
            //         'STACK_ID'=>***,            //projectId
            //         'STACK_URL'=>***,    //HEATスタックの実行状況を確認するAPI
            //         ),
            //         // .........
            //     ]
            // ];

            ////////////////////////////////
            // SYSTEM_ID　<--> projectId 紐付け
            ////////////////////////////////
            if(1){

                if ( $log_level === 'DEBUG' ) {
                    //MasterSyncテーブルから、プロジェクト名リスト取得開始
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56130");
                    require ($root_dir_path . $log_output_php );
                }

                $sqlUtnBody = "SELECT NAME,VALUE from B_OPENST_MASTER_SYNC WHERE TENANT_ID = 'general' AND NAME = 'project'";
                $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                if( $objQueryUtn->getStatus()===false ) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                }
                $r = $objQueryUtn->sqlExecute();
                if (!$r) {
                    $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                        $objQueryUtn->getLastError());
                    require ($root_dir_path . $log_output_php );
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                }
                $patternNode="";
                while ( $row = $objQueryUtn->resultFetch() ) {
                        $rowData=$row['VALUE'];
                        $rowData=str_replace("'","\"",$rowData);
                        $rowData=json_decode($rowData,true);
                }
                for ($i=0; $i <count($restData['projectList']) ; $i++) {
                    for ($j=0; $j <count($rowData['tenants']) ; $j++) {
                        if($restData['projectList'][$i]['SYSTEM_ID']==$rowData['tenants'][$j]['id']) {
                            $restData['projectList'][$i]['SYSTEM_NAME']=$rowData['tenants'][$j]['name'];
                        }
                    }
                }

                if ( $log_level === 'DEBUG' ) {
                    //プロジェクト名リスト取得完了
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56140");
                    require ($root_dir_path . $log_output_php );
                }
            }

            if($executeNode['STATUS_ID']==PREPARE) {

                //プロジェクト数分実施
                for( $i=0; $i < count($restData['projectList']); $i++ ) {
                    for( $j=0; $j < count($restData['projectList'][$i]['floatingip']); $j++ ) {

                        //機器一覧に設定されているfloatingipか確認
                        $sqlUtnBody = "SELECT * from E_STM_LIST WHERE DISUSE_FLAG ='0' AND IP_ADDRESS = :FLOATINGIP";
                        $arrayUtnBind = array(
                            "FLOATINGIP"=>$restData['projectList'][$i]['floatingip'][$j]
                        );

                        //SQL実行
                        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);

                        if( $objQueryUtn->getStatus()===false ) {
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                        }
                        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                        }
                        $r = $objQueryUtn->sqlExecute();
                        if (!$r) {
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                        }
                        // FETCH行数を取得
                        $num_of_rows = $objQueryUtn->effectedRowCount();

                        // レコード無しの場合
                        if( $num_of_rows === 0 ){
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );

                            //失敗ステータス変更
                            $target=array();
                            //特定・更新用
                            $target["RESULT_DETAIL_ID"]=null;
                            $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                            $target["SYSTEM_ID"] =$restData['projectList'][$i]['SYSTEM_ID'];
                            $target["RESPONSE_JSON"] ="";
                            $target["RESPONSE_MESSAGE"] =$objMTS->getSomeMessage("ITAOPENST-ERR-53010").$restData['projectList'][$i]['floatingip'][$j];

                            //更新用
                            $target["TIME_END"] =date("Y-m-d H:i:s");
                            $target["TIME_START"] =$executeNode['TIME_START'];
                            $target["STATUS_ID"]= CHILD_FAILED_BY_OTHER;
                            $target["SYSTEM_NAME"]= $restData['projectList'][$i]["SYSTEM_NAME"];
                            $target["REQUEST_TEMPLATE"]=$restData['projectList'][$i]['convertedTemplate'];

                            resultDetailUpdate($target);
                            resultMngCheck($executeNode);
                            
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00002000")) );
                        }
                    }
                }

                if(1) {
                    if ( $log_level === 'DEBUG' ) {
                        //REST認証情報取得開始
                        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56170");
                        require ($root_dir_path . $log_output_php );
                    }

                    //インターフェース情報取得
                    $sqlUtnBody = "SELECT * "
                                 ."FROM B_OPENST_IF_INFO TAB_1 "
                                 ."WHERE TAB_1.DISUSE_FLAG = '0'";
                    $arrayUtnBind = array();

                    //SQL実行
                    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                    if( $objQueryUtn->getStatus()===false ) {
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                    }
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                    }
                    $r = $objQueryUtn->sqlExecute();
                    if (!$r) {
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                    }

                    //インターフェース情報設定
                    $openstack_if_info = $objQueryUtn->resultFetch();

                    if ( $log_level === 'DEBUG' ) {
                        //REST認証情報取得完了
                        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56180");
                        require ($root_dir_path . $log_output_php );
                    }
                }
                //初期化
                $openst_project_id = [];

                if(1) {
                    //プロジェクト情報取得
                    $sqlUtnBody = "SELECT * FROM B_OPENST_PROJECT_INFO WHERE DISUSE_FLAG = '0'";
                    $arrayUtnBind = array();

                    //SQL実行
                    $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                    if( $objQueryUtn->getStatus()===false ) {
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                    }
                    if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                    }
                    $r = $objQueryUtn->sqlExecute();
                    if (!$r) {
                        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                            $objQueryUtn->getLastError());
                        require ($root_dir_path . $log_output_php );
                        // 異常フラグON  例外処理へ
                        $error_flag = 1;
                        throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                    }
                    //取得したプロジェクトIDを設定
                    while ( $row = $objQueryUtn->resultFetch() ) {
                        $openst_project_id[] = $row['OPENST_PROJECT_ID'];
                    }
                }
                //初期化
                $tmpvalue = [];

                //テナントの数だけAPIを取得する。
                for( $i=0; $i < count($openst_project_id); $i++) {

                    $tenant_id = $openst_project_id[$i];

                    if(1) {
                        //認証リクエスト(テナントID付)
                        $content=[
                            "auth"=>[
                                "passwordCredentials"=>[
                                    "username"=>$openstack_if_info['OPENST_USER'],
                                    "password"=>ky_decrypt($openstack_if_info['OPENST_PASSWORD'])
                                ],
                                "tenantId"=>$tenant_id
                            ]
                        ];
                        $content=json_encode($content,true);

                        $aryResult = array();
                        //OpenStack 認証 REST実行
                        $ret = openstack_rest_call("TOKENS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", "", $content, $aryResult);

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-101010");
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $resData=json_decode($aryResult['ResponsContents'],true);
                            //トークン取得
                            $token=$resData['access']['token']['id'];
                            $response[$tenant_id]['token']=$aryResult['ResponsContents'];
                        }

                        // APIコール先の一覧を取得する
                        $apiArray=[];
                        for ($j=0; $j < count($resData['access']['serviceCatalog']); $j++) {
                            $node=$resData['access']['serviceCatalog'][$j];
                            $name=$node['type'];
                            $value=$node['endpoints'][0]['publicURL'];
                            $apiArray[$value]=$name;
                        }

                        $apiUrl=getApiUrl("network",$apiArray);
                        if($apiUrl!=null) {
                            //IP一覧
                            $aryResult = array();
                            //OpenStack floating ip一覧取得 REST実行
                            $ret = openstack_rest_call("IP", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                            // トレースメッセージ
                            if ( $log_level === 'DEBUG' ) {
                                $FREE_LOG = "IP   Response:".$ret."  URL:".$apiUrl;
                                require ($root_dir_path . $log_output_php );
                            }
                            if($ret != '200') {
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800"));
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception($FREE_LOG);
                            } else {
                                //通信成功時(正常系)
                                $resData=json_decode($aryResult['ResponsContents'],true);
                                $response[$tenant_id]['ip']=$aryResult['ResponsContents'];
                            }
                            $tmpvalue[] = $resData;
                        }
                    }
                }

                $tmp_floatingip = [];

                //テナント分 floating_ip_addressを取得
                for( $i=0; $i < count($tmpvalue); $i++) {
                    $tmp_floatingip[] = array_column($tmpvalue[$i]['floatingips'], 'floating_ip_address');
                }

                $search_floatingip = array_flatten($tmp_floatingip);

                //floating_ip_address取得確認
                if( empty($search_floatingip) ) {
                    //空の場合異常処理
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00002100"));

                    //失敗ステータス変更
                    $target=array();
                    //特定・更新用
                    $target["RESULT_DETAIL_ID"]=null;
                    $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                    $target["SYSTEM_ID"] ="";
                    $target["RESPONSE_JSON"] ="";
                    $target["RESPONSE_MESSAGE"] =$objMTS->getSomeMessage("ITAOPENST-ERR-53020");

                    //更新用
                    $target["TIME_END"] =date("Y-m-d H:i:s");
                    $target["TIME_START"] =$executeNode['TIME_START'];
                    $target["STATUS_ID"]= CHILD_FAILED_BY_OTHER;
                    $target["SYSTEM_NAME"]="";
                    $target["REQUEST_TEMPLATE"]="";

                    resultDetailUpdate($target);
                    resultMngCheck($executeNode);
                    
                    // 異常フラグON  例外処理へ
                    $error_flag = 1;
                    throw new Exception($FREE_LOG);
                }

                //プロジェクト数分実施
                for( $i=0; $i < count($restData['projectList']); $i++ ) {
                    //floating_ip_address検索(Floating IP アドレスプール確認)
                    for( $j=0; $j < count($restData['projectList'][$i]['floatingip']); $j++ ) {
                        $tmpVarResult = array_search($restData['projectList'][$i]['floatingip'][$j], $search_floatingip, true);
                        if( $tmpVarResult === false ) {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00002200"));

                            //失敗ステータス変更
                            $target=array();
                            //特定・更新用
                            $target["RESULT_DETAIL_ID"]=null;
                            $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                            $target["SYSTEM_ID"] =$restData['projectList'][$i]['SYSTEM_ID'];
                            $target["RESPONSE_JSON"] ="";
                            $target["RESPONSE_MESSAGE"] =$objMTS->getSomeMessage("ITAOPENST-ERR-53030").$restData['projectList'][$i]['floatingip'][$j];

                            //更新用
                            $target["TIME_END"] =date("Y-m-d H:i:s");
                            $target["TIME_START"] =$executeNode['TIME_START'];
                            $target["STATUS_ID"]= CHILD_FAILED_BY_OTHER;
                            $target["SYSTEM_NAME"]=$restData['projectList'][$i]["SYSTEM_NAME"];
                            $target["REQUEST_TEMPLATE"]=$restData['projectList'][$i]['convertedTemplate'];

                            resultDetailUpdate($target);
                            resultMngCheck($executeNode);
                            
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        }
                    }
                }

                //テナントの数だけAPIを取得する。
                for( $i=0; $i < count($openst_project_id); $i++) {

                    $tenant_id = $openst_project_id[$i];

                    if(1) {
                        //認証リクエスト(テナントID付)
                        $content=[
                            "auth"=>[
                                "passwordCredentials"=>[
                                    "username"=>$openstack_if_info['OPENST_USER'],
                                    "password"=>ky_decrypt($openstack_if_info['OPENST_PASSWORD'])
                                ],
                                "tenantId"=>$tenant_id
                            ]
                        ];
                        $content=json_encode($content,true);

                        $aryResult = array();
                        //OpenStack 認証 REST実行
                        $ret = openstack_rest_call("TOKENS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", "", $content, $aryResult);

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-101010");
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $resData=json_decode($aryResult['ResponsContents'],true);
                            //トークン取得
                            $token=$resData['access']['token']['id'];
                            $response[$tenant_id]['token']=$aryResult['ResponsContents'];
                        }

                        // APIコール先の一覧を取得する
                        $apiArray=[];
                        for ($j=0; $j < count($resData['access']['serviceCatalog']); $j++) {
                            $node=$resData['access']['serviceCatalog'][$j];
                            $name=$node['type'];
                            $value=$node['endpoints'][0]['publicURL'];
                            $apiArray[$value]=$name;
                        }

                        $apiUrl=getApiUrl("Compute",$apiArray);

                        if($apiUrl!=null) {
                            //サーバ一覧
                            $aryResult = array();
                            $ret = openstack_rest_call("SERVER", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", $apiUrl, $token, "", $aryResult);

                            // トレースメッセージ
                            if ( $log_level === 'DEBUG' ) {
                                $FREE_LOG = "SERVER   Response:".$ret."  URL:".$apiUrl;
                                require ($root_dir_path . $log_output_php );
                            }

                            if($ret != '200') {
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00002300"));
                                // 異常フラグON  例外処理へ
                                $error_flag = 1;
                                throw new Exception($FREE_LOG);
                            } else {
                                //通信成功時(正常系)
                                $resData=json_decode($aryResult['ResponsContents'],true);
                                $response[$tenant_id]['server']=$aryResult['ResponsContents'];
                            }
                        }

                        //空でない場合
                        if( !empty($resData['servers']) ) {

                            for( $j=0; $j < count($restData['projectList']); $j++ ) {
                                for( $k=0; $k < count($restData['projectList'][$j]['floatingip']); $k++) {
                                    for( $l=0; $l < count($resData['servers']); $l++) {

                                        //floatingip割り当ててないサーバは除く
                                        if( count($resData['servers'][$l]['addresses']['private']) == 2 ) {

                                            //floatingip割り当て確認
                                            if( $restData['projectList'][$j]['floatingip'][$k] == $resData['servers'][$l]['addresses']['private'][1]['addr'] ) {
                                                //割り当て済みの場合異常処理
                                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00002400"));

                                                //失敗ステータス変更
                                                $target=array();
                                                //特定・更新用
                                                $target["RESULT_DETAIL_ID"]=null;
                                                $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                                                $target["SYSTEM_ID"] =$restData['projectList'][$j]['SYSTEM_ID'];
                                                $target["RESPONSE_JSON"] ="";
                                                $target["RESPONSE_MESSAGE"] =$objMTS->getSomeMessage("ITAOPENST-ERR-53040").$restData['projectList'][$j]['floatingip'][$k];

                                                //更新用
                                                $target["TIME_END"] =date("Y-m-d H:i:s");
                                                $target["TIME_START"] =$executeNode['TIME_START'];
                                                $target["STATUS_ID"]= CHILD_FAILED_BY_OTHER;
                                                $target["SYSTEM_NAME"]= $restData['projectList'][$j]["SYSTEM_NAME"];
                                                $target["REQUEST_TEMPLATE"]=$restData['projectList'][$j]['convertedTemplate'];

                                                resultDetailUpdate($target);
                                                resultMngCheck($executeNode);

                                                // 異常フラグON  例外処理へ
                                                $error_flag = 1;
                                                throw new Exception($FREE_LOG);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            ////////////////////////////////
            // ここからREST処理
            ////////////////////////////////
            if(1){

                if ( $log_level === 'DEBUG' ) {
                    //REST処理開始
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56150");
                    require ($root_dir_path . $log_output_php );
                }

                for ($i=0; $i <count($restData['projectList']) ; $i++) {
                    $projectNode=$restData['projectList'][$i];

                    if ( $log_level === 'DEBUG' ) {
                        //対象作業ノード
                        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56160")." ".$projectNode['SYSTEM_NAME']." "."[STATUS_ID]:".$executeNode['STATUS_ID'];
                        require ($root_dir_path . $log_output_php );
                    }

                    $detailStatus = "";

                    ////////////////////////////////
                    // OpenStackへの接続情報を得る
                    ////////////////////////////////
                    if(1){

                        if ( $log_level === 'DEBUG' ) {
                            //REST認証情報取得開始
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56170");
                            require ($root_dir_path . $log_output_php );
                        }

                        $sqlUtnBody = "SELECT * "
                                     ."FROM B_OPENST_IF_INFO TAB_1 "
                                     ."WHERE TAB_1.DISUSE_FLAG = '0'";
                        $arrayUtnBind = array();

                        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
                        if( $objQueryUtn->getStatus()===false ) {
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
                        }
                        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
                        }
                        $r = $objQueryUtn->sqlExecute();
                        if (!$r) {
                            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                                $objQueryUtn->getLastError());
                            require ($root_dir_path . $log_output_php );
                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
                        }
                        while ( $row = $objQueryUtn->resultFetch() ) {
                            $openstack_if_info = $row;
                        }

                        if ( $log_level === 'DEBUG' ) {
                            //REST認証情報取得完了
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56180");
                            require ($root_dir_path . $log_output_php );
                        }
                    }

                    ////////////////////////////////
                    // OpenStackに、認証のRESTを行う
                    ////////////////////////////////
                    if(1){

                        if ( $log_level === 'DEBUG' ) {
                            //REST認証開始
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56190");
                            require ($root_dir_path . $log_output_php );
                        }

                        //認証リクエスト（テナントID付）
                        $content=[
                            "auth"=>[
                                "passwordCredentials"=>[
                                    "username"=>$openstack_if_info['OPENST_USER'],
                                    "password"=>ky_decrypt($openstack_if_info['OPENST_PASSWORD'])
                                ],
                                "tenantId"=>$projectNode['SYSTEM_ID']
                            ]
                        ];
                        $content=json_encode($content,true);

                        $aryResult       = array();
                        $ret = openstack_rest_call("TOKENS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "", "", "", $content, $aryResult);

                        if($ret != '200') {
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-101010");

                            // 異常フラグON  例外処理へ
                            $error_flag = 1;
                            throw new Exception($FREE_LOG);
                        } else {
                            //通信成功時（正常系）
                            $resData=json_decode($aryResult['ResponsContents'],true);

                            //トークン取得
                            $projectNode['token']=$resData['access']['token']['id'];
                        }

                        // APIコール先の一覧を取得する
                        $apiArray=[];
                        for ($j=0; $j < count($resData['access']['serviceCatalog']); $j++) {
                            $node=$resData['access']['serviceCatalog'][$j];
                            $name=$node['type'];
                            $value=$node['endpoints'][0]['publicURL'];
                            $apiArray[$value]=$name;
                        }

                        if ( $log_level === 'DEBUG' ) {
                            //REST認証完了
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56200");
                            require ($root_dir_path . $log_output_php );
                        }
                    }

                    ////////////////////////////////
                    // 親プロセスが準備中だったら、OpenStackに、テンプレートを送信するRESTを行う。
                    ////////////////////////////////
                    if($executeNode['STATUS_ID']==PREPARE){

                        if ( $log_level === 'DEBUG' ) {
                            //CREATE_STACK::REST開始
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56210");
                            require ($root_dir_path . $log_output_php );
                        }

                        $apiUrl=getApiUrl("orchestration",$apiArray);

                        if($apiUrl!=null){

                            //disable_rollbackは使わない方針とした。
                            //本オプションは、エラー発生時、OpenStackさんがdelete_stackコマンドを実行し
                            //OpenStackサーバ上にゴミが残らないようにするものだが、
                            //不具合が発生したログもきちんと結果詳細に残す必要があり、使わないという判断に至る。
                            $content=array(
                                "stack_name"=>$projectNode['SYSTEM_NAME'].'_'.$executeNode['EXECUTION_NO']."_".date("Y-m-d-H-i-s"),
                                "template"=>$projectNode['convertedTemplate'],
                                "timeout_mins"=>30 * 60 * 1000,
                            );

                            $content=json_encode($content,true);

                            $aryResult = array();
                            $ret = openstack_rest_call("CREATE_STACK", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "",$apiUrl, $projectNode['token'], $content, $aryResult);

                            //取り急ぎレスポンスは格納する
                            $projectNode['response']=$aryResult['ResponsContents'];

                            // CREATE_STACK
                            // 201 Created(success)
                            // 400 Bad Request(illegal parameterandroid
                            // 401 Uauthorized(no token)
                            // 409 conflict(other

                            if($ret != '201'){

                                // $FREE_LOG = "HEATのRESTに失敗しました。";

                                if($ret=="400"){
                                    // 400 Bad Request(illegal parameter)
                                    $detailStatus=CHILD_FAILED_BY_HEAT;
                                }else if($ret=="401"){
                                    // 401 Uauthorized(no token)
                                    $detailStatus=CHILD_FAILED_BY_OTHER;
                                }else if($ret=="409"){
                                    // 409 conflict(other
                                    $detailStatus=CHILD_FAILED_BY_OTHER;
                                }else if($ret=="-1"){
                                    // -1 RequestURI is empty
                                    $detailStatus=CHILD_FAILED_BY_OTHER;
                                }

                                //レスポンス整形
                                $json=json_decode($aryResult['ResponsContents']['ErrorMessage'],JSON_UNESCAPED_UNICODE);
                                $errorstr=$json['title']."[".$json['code']."]:".$json['error']['message'];

                                $target=array();

                                //特定・更新用
                                $target["RESULT_DETAIL_ID"]=null;
                                $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                                $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];
                                $target["RESPONSE_JSON"] =$aryResult['ResponsContents']['ErrorMessage'];
                                $target["RESPONSE_MESSAGE"] =$errorstr;

                                //更新用
                                $target["TIME_END"] =date("Y-m-d H:i:s");
                                $target["TIME_START"] =$executeNode['TIME_START'];
                                $target["STATUS_ID"]= $detailStatus;
                                $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                                $target["REQUEST_TEMPLATE"]=$projectNode['convertedTemplate'];

                                if ( $log_level === 'DEBUG' ) {

                                    // REST失敗
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-52000").":".$ret.":".$target["RESPONSE_MESSAGE"];
                                    require ($root_dir_path . $log_output_php );

                                    //DETAILステータスを変更します：
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")
                                    ." to ".$detailStatus;
                                    require ($root_dir_path . $log_output_php );

                                }

                                resultDetailUpdate($target);

                                //全部の作業完了の後に、全部失敗or成功になったか確認する
                                // 全て片がついていたら、親のMNGのステータスを変更して終了
                            } else {

                                //レスポンス整形
                                $detailStatus=CHILD_WAITING_RESPONSE;
                                $json=json_decode($aryResult['ResponsContents']['ErrorMessage'],JSON_UNESCAPED_UNICODE);

                                //特定・更新用
                                $target=array();
                                $target["RESULT_DETAIL_ID"]=null;

                                $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                                $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];

                                //ここではJSONは格納しない。この後、すたっくのステータスがSUCCESSかFAILの場合、レスポンスを格納する。
                                // $target["RESPONSE_JSON"] =$aryResult['ResponsContents']['ErrorMessage'];
                                //更新用
                                $target["STACK_ID"]=$json['stack']['id'];
                                $target["STACK_URL"]=$json['stack']['links'][0]['href'];
                                $target["TIME_START"] =$executeNode['TIME_START'];
                                $target["STATUS_ID"]= $detailStatus;
                                $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                                $target["REQUEST_TEMPLATE"]=$projectNode['convertedTemplate'];

                                // resultDetailUpdate($target);

                                if ( $log_level === 'DEBUG') {
                                    //DETAILステータスを変更します：
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")
                                    ." to ".$detailStatus;
                                    require ($root_dir_path . $log_output_php );
                                }

                                resultDetailUpdate($target);
                            }
                        }

                        if ( $log_level === 'DEBUG' ) {
                            //CREATE_STACK::REST完了
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56220");
                            require ($root_dir_path . $log_output_php );
                        }
                    }

                    ////////////////////////////////
                    // 親プロセスが実行中だったら、OpenStackに、送信したテンプレートの状態を確認するRESTを行う。
                    ////////////////////////////////
                    if($executeNode['STATUS_ID']==EXECUTE_AND_WAITING_COMPLETE) {

                        if ( $log_level === 'DEBUG' ) {
                            //SHOW_STACK_DETAILS::REST開始
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56230");
                            require ($root_dir_path . $log_output_php );
                        }

                        $apiUrl=$projectNode['STACK_URL'];
                        $aryResult = array();
                        $ret = openstack_rest_call("SHOW_STACK_DETAILS", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "",$apiUrl, $projectNode['token'], "", $aryResult);

                        // SHOW_STACK_DETAILS
                        // response_codes:
                        // 200 found(success)
                        // 400 Bad Request(illegal parameter)
                        // 401 Uauthorized(no token)
                        // 404 not found
                        // 500 Internal server error

                        if($ret != '200'){

                            if($ret=="400"){
                                // 400 Bad Request(illegal parameter)
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="401"){
                                // 401 Uauthorized(no token)
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="404"){
                                // 409 conflict(other
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="500"){
                                // 409 conflict(other
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="-1"){
                                // -1 RequestURI is empty
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }

                            //レスポンス整形
                            $json=json_decode($aryResult['ResponsContents']['ErrorMessage'],JSON_UNESCAPED_UNICODE);
                            $errorstr=$json['title']."[".$json['code']."]:".$json['error']['message'];

                            $target=array();

                            //特定・更新用
                            $target["RESULT_DETAIL_ID"]=$projectNode['RESULT_DETAIL_ID'];
                            $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                            $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];
                            $target["RESPONSE_JSON"] =$aryResult['ResponsContents']['ErrorMessage'];
                            $target["RESPONSE_MESSAGE"] =$errorstr;

                            //更新用
                            $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                            $target["TIME_END"] =date("Y-m-d H:i:s");
                            $target["STATUS_ID"]= $detailStatus;


                            if ( $log_level === 'DEBUG' ) {
                                // REST失敗
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-52000").":".$ret.":".$target["RESPONSE_MESSAGE"];
                                require ($root_dir_path . $log_output_php );
                            }

                            if ( $log_level === 'DEBUG' && $projectNode['STATUS_ID']!=$detailStatus ) {
                                //DETAILステータスを変更します：
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")." ".$projectNode['STATUS_ID']." to ".$detailStatus;
                                require ($root_dir_path . $log_output_php );
                            }

                            resultDetailUpdate($target);

                        } else {
                            //通信に成功したら

                            $json=json_decode($aryResult['ResponsContents'],JSON_UNESCAPED_UNICODE);

                            if($json['stack']['stack_status']==="CREATE_COMPLETE") {

                                //サーバ作成が完了していたら
                                $detailStatus=CHILD_SUCCESS;

                                $target=array();

                                //特定・更新用
                                $target["RESULT_DETAIL_ID"]=$projectNode['RESULT_DETAIL_ID'];
                                $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                                $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];
                                $target["RESPONSE_JSON"] =$aryResult['ResponsContents'];

                                //更新用
                                $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                                //$target["RESPONSE_MESSAGE"] ="OK";
                                $target["RESPONSE_MESSAGE"] =$json['stack']['stack_status'].'::'.$json['stack']['stack_status_reason'];
                                $target["TIME_END"] =date("Y-m-d H:i:s");
                                $target["STATUS_ID"]= $detailStatus;

                                if ( $log_level === 'DEBUG' && $projectNode['STATUS_ID']!=$detailStatus ) {
                                    //DETAILステータスを変更します：
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")
                                    ." ".$projectNode['STATUS_ID']." to ".$detailStatus;
                                    require ($root_dir_path . $log_output_php );
                                }

                                resultDetailUpdate($target);

                            } else if($json['stack']['stack_status']==="CREATE_IN_PROGRESS") {

                                //作成中だったら何もしない

                            } else {

                                //サーバ作成が失敗していたら（CREATE_FAILURE）
                                //または、想定外エラー
                                $detailStatus=CHILD_FAILED_BY_OTHER;

                                $target=array();

                                //特定・更新用
                                $target["RESULT_DETAIL_ID"]=$projectNode['RESULT_DETAIL_ID'];
                                $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                                $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];
                                $target["RESPONSE_JSON"] =$aryResult['ResponsContents'];

                                //更新用
                                $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                                $target["RESPONSE_MESSAGE"] =$json['stack']['stack_status'].'::'.$json['stack']['stack_status_reason'];
                                $target["TIME_END"] =date("Y-m-d H:i:s");
                                $target["STATUS_ID"]= $detailStatus;

                                if ( $log_level === 'DEBUG' ) {
                                    //REST失敗
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-52000").":".$ret.":".$target["RESPONSE_MESSAGE"];
                                    require ($root_dir_path . $log_output_php );
                                }

                                if ( $log_level === 'DEBUG' && $projectNode['STATUS_ID']!=$detailStatus ) {
                                    //DETAILステータスを変更します：
                                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")
                                    ." ".$projectNode['STATUS_ID']." to ".$detailStatus;
                                    require ($root_dir_path . $log_output_php );
                                }

                                resultDetailUpdate($target);
                            }
                        }

                        if ( $log_level === 'DEBUG' ) {
                            //SHOW_STACK_DETAILS::REST完了
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56240");
                            require ($root_dir_path . $log_output_php );
                        }
                    }

                    ////////////////////////////////
                    // 親プロセスが緊急停止だったら、OpenStackに、作成したスタックを削除するRESTを送信する
                    ////////////////////////////////
                    if($executeNode['STATUS_ID']==SCRAM) {

                        if ( $log_level === 'DEBUG' ) {
                            //DELETE_STACK::REST開始
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56250");
                            require ($root_dir_path . $log_output_php );
                        }

                        $apiUrl=$projectNode['STACK_URL'];
                        $aryResult = array();
                        $ret = openstack_rest_call("DELETE_STACK", $openstack_if_info['OPENST_PROTOCOL'], $openstack_if_info['OPENST_HOSTNAME'], "",$apiUrl, $projectNode['token'], "", $aryResult);

                        // DELETE_STACK
                        // response_codes:
                        // 204 found(success)
                        // 400 Bad Request(illegal parameter)
                        // 401 Uauthorized(no token)
                        // 404 not found
                        // 500 Internal server error
                        if($ret=="404") {
                            //何もしない。当該のURLがまだアクセス可能な状態になっていないと考えられる。

                        } else if($ret != '204') {

                            if($ret=="400"){
                                // 400 Bad Request(illegal parameter)
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="401"){
                                // 401 Uauthorized(no token)
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="404"){
                                // 409 conflict(other
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="500"){
                                // 409 conflict(other
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }else if($ret=="-1"){
                                // -1 RequestURI is empty
                                $detailStatus=CHILD_FAILED_BY_OTHER;
                            }

                            //レスポンス整形
                            $json=json_decode($aryResult['ResponsContents']['ErrorMessage'],JSON_UNESCAPED_UNICODE);
                            $errorstr=$json['title']."[".$ret."]:".$json['error']['message'];

                            $target=array();

                            //特定・更新用
                            $target["RESULT_DETAIL_ID"]=$projectNode['RESULT_DETAIL_ID'];
                            $target["RESPONSE_JSON"] =$aryResult['ResponsContents']['ErrorMessage'];
                            $target["RESPONSE_MESSAGE"] =$errorstr;
                            $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                            $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];

                            //更新用
                            $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                            $target["TIME_END"] =date("Y-m-d H:i:s");
                            $target["STATUS_ID"]= $detailStatus;

                            if ( $log_level === 'DEBUG' ) {
                                //REST失敗
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-52000").":".$ret.":".$target["RESPONSE_MESSAGE"];
                                require ($root_dir_path . $log_output_php );
                            }

                            if ( $log_level === 'DEBUG' && $projectNode['STATUS_ID']!=$detailStatus ) {
                                //DETAILステータスを変更します：
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")
                                ." ".$projectNode['STATUS_ID']." to ".$detailStatus;
                                require ($root_dir_path . $log_output_php );
                            }

                            resultDetailUpdate($target);

                        } else {
                            //通信に成功したら

                            //サーバ作成が完了していたら

                            $detailStatus=CHILD_FAILED_BY_SCRAM;
                            $target=array();

                            //特定・更新用
                            $target["RESULT_DETAIL_ID"]=$projectNode['RESULT_DETAIL_ID'];
                            $target["RESPONSE_JSON"] ='{"status":"scram executed"}';
                            $target["RESPONSE_MESSAGE"] ="scram executed";
                            $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
                            $target["SYSTEM_ID"] =$projectNode['SYSTEM_ID'];

                            //更新用
                            $target["SYSTEM_NAME"]= $projectNode["SYSTEM_NAME"];
                            $target["TIME_END"] =date("Y-m-d H:i:s");
                            $target["STATUS_ID"]= $detailStatus;

                            if ( $log_level === 'DEBUG' && $projectNode['STATUS_ID']!=$detailStatus ){
                                //DETAILステータスを変更します：
                                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56286")
                                ." ".$projectNode['STATUS_ID']." to ".$detailStatus;
                                require ($root_dir_path . $log_output_php );
                            }

                            resultDetailUpdate($target);
                        }

                        if ( $log_level === 'DEBUG' ) {
                            //DELETE_STACK::REST完了
                            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56260");
                            require ($root_dir_path . $log_output_php );
                        }
                    }
                }

                if ( $log_level === 'DEBUG' ) {
                    //REST処理完了
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56270");
                    require ($root_dir_path . $log_output_php );
                }
            }

            ////////////////////////////////
            //  子レコードの状況を元に、親レコードのステータスを更新する。
            ////////////////////////////////
            if(1){

                if ( $log_level === 'DEBUG' ) {
                    //親レコード更新開始
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56280");
                    require ($root_dir_path . $log_output_php );
                }
                $ret=resultMngCheck($executeNode);

                if ( $log_level === 'DEBUG' && $executeNode['STATUS_ID']!=$ret ) {
                    //MNGステータスを変更します：
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56285")
                    ." ".$executeNode['STATUS_ID']." to ".$ret;
                    require ($root_dir_path . $log_output_php );
                }

                if ( $log_level === 'DEBUG' ) {
                    //親レコード更新完了
                    $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56290");
                    require ($root_dir_path . $log_output_php );
                }
            }

            if ( $log_level === 'DEBUG' ) {
                //[完了]対象作業No:
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-56300")
                ."[EXECUTION_NO]:".$executeNode['EXECUTION_NO'];
                require ($root_dir_path . $log_output_php );
            }
        }

        //作業NO単位で実行 END
    }
    catch (Exception $e){
        $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55272");
        require ($root_dir_path . $log_output_php );

        // 例外メッセージ出力
        $FREE_LOG = $e->getMessage();
        require ($root_dir_path . $log_output_php );
        
        // DBアクセス事後処理
        if ( isset($objQuery)    ) unset($objQuery);
        if ( isset($objQueryUtn) ) unset($objQueryUtn);
        if ( isset($objQueryJnl) ) unset($objQueryJnl);
        
        // トランザクションが発生しそうなロジックに入ってからのexceptionの場合は
        // 念のためロールバック/トランザクション終了
        if( $objDBCA->getTransactionMode() ) {
            // ロールバック
            if( $objDBCA->transactionRollBack()=== true ) {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55016");
            } else {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50045");
            }
            require ($root_dir_path . $log_output_php );
            
            // トランザクション終了
            if( $objDBCA->transactionExit()=== true ) {
                //$FREE_LOG = 'トランザクション終了';
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-50047");
            } else {
                $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-50049");
            }
            require ($root_dir_path . $log_output_php );
        }
    }

    ////////////////////////////////
    //// 結果出力               ////
    ////////////////////////////////
    // 処理結果コードを判定してアクセスログを出し分ける
    if( $error_flag != 0 ) {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55267");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(2);
    } elseif( $warning_flag != 0 ) {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-ERR-55268");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(2);
    } else {
        // 終了メッセージ
        if ( $log_level === 'DEBUG' ) {
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-55002");
            require ($root_dir_path . $log_output_php );
        }

        // リターンコード
        exit(0);
    }

function getApiUrl($category,$apiArray){

    $category=mb_strtolower($category);
    foreach($apiArray as $key => $value){

        if(preg_match("/$category/",$value)){
            return $key;
        }
    }
    return null;
}

//子プロセスのステータス進捗を確認し、必要であれば親のステータスを更新する。
function resultMngCheck($executeNode){

    global $objDBCA;
    global $root_dir_path;
    global $log_output_php;
    global $objMTS;

    //子プロセスのステータスを取得する。
    if(1) {

        $status_id_list=array();

        $sqlUtnBody = "SELECT STATUS_ID "
                     ."FROM C_OPENST_RESULT_DETAIL "
                     ."WHERE DISUSE_FLAG=0 AND EXECUTION_NO = :EXECUTION_NO ";
        $arrayUtnBind = array("EXECUTION_NO"=>$executeNode['EXECUTION_NO']);

        $objQueryUtn = $objDBCA->sqlPrepare($sqlUtnBody);
        if( $objQueryUtn->getStatus()===false ) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001700")) );
        }
        if( $objQueryUtn->sqlBind($arrayUtnBind) != "" ) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001800")) );
        }
        $r = $objQueryUtn->sqlExecute();
        if (!$r) {
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,
                                $objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
            // 異常フラグON  例外処理へ
            $error_flag = 1;
            throw new Exception( $objMTS->getSomeMessage("ITAOPENST-ERR-50003",array(__FILE__,__LINE__,"00001900")) );
        }
        while ( $row = $objQueryUtn->resultFetch() ) {
            $status_id_list[] = $row['STATUS_ID'];
        }
    }

    //重複を除去
    $status_id_list=array_unique($status_id_list);
    $changeStatusId="";

    //重複を除去したあとの配列に関して走査を行う。
    if(count($status_id_list)>0){
        if(in_array(CHILD_WAITING_RESPONSE,$status_id_list)){
            // ひとつでもCHILD_WAITING_RESPONSEがあったら
            $changeStatusId=EXECUTE_AND_WAITING_COMPLETE;
        }else{
            // CHILD_WAITING_RESPONSEが無かったら
            if(count($status_id_list)===1 && in_array(CHILD_SUCCESS,$status_id_list)){
                //全部成功だったら
                $changeStatusId=COMPLETE;
            }else if(count($status_id_list)>1 && in_array(CHILD_SUCCESS,$status_id_list)){
                //CHILD_SUCCESSがひとつでもあって、それ以外も存在したら
                $changeStatusId=PARTIAL_FAILURE;
            }else if(count($status_id_list)===1 && in_array(CHILD_FAILED_BY_SCRAM,$status_id_list)){
                //緊急停止ボタンが押されたら
                $changeStatusId=SCRAM_COMPLETE;
            }else if(!in_array(CHILD_SUCCESS,$status_id_list)){
                //CHILD_SUCCESSが無かったら
                $changeStatusId=FAILURE;
            }
        }
    }else{
        // 子レコードが存在しないか、子レコードが作成される前に緊急停止コマンドが発生した場合になった場合,ここに飛ぶ
        // ステータスを変更して終了
        if($executeNode['STATUS_ID']==SCRAM){
            $changeStatusId=SCRAM_COMPLETE;
        }else{
            $changeStatusId=FAILURE;

            $target = array();
            $target["RESULT_DETAIL_ID"]= null;
            $target["EXECUTION_NO"] = $executeNode['EXECUTION_NO'];
            $target["STATUS_ID"] = CHILD_FAILED_BY_OTHER;
            // Movement詳細が登録されていません
            $target["RESPONSE_MESSAGE"] = $objMTS->getSomeMessage("ITAOPENST-ERR-102060");;
            $target["TIME_START"] = $executeNode['TIME_START'];
            $target["TIME_END"] = date("Y-m-d H:i:s");
            resultDetailUpdate($target);
        }
    }

    //親のステータスを変更するべきであれば、変更を行う。
    if($changeStatusId!="" && $changeStatusId!=$executeNode['STATUS_ID']) {

        $target=array();
        //特定・更新用
        $target["EXECUTION_NO"] =$executeNode['EXECUTION_NO'];
        $target["STATUS_ID"]= $changeStatusId;

        if(in_array($changeStatusId,[COMPLETE,PARTIAL_FAILURE,FAILURE,SCRAM_COMPLETE])) {
            //更新用
            $target["TIME_END"] =date("Y-m-d H:i:s");
        }
        resultMnglUpdate($target);
    }
    if($changeStatusId==""){
        $changeStatusId=$executeNode['STATUS_ID'];
    }

    return $changeStatusId;
}

//MNGレコードのINSERT、UPDATEを管理（本バッチで使用されているのは、実質UPDATEのみとなる）
function resultMnglUpdate($array){

    global $objDBCA;
    global $objMTS;
    global $root_dir_path;
    global $log_file_prefix;
    global $log_output_php;
    global $db_access_user_id;

    //対象を探してロック
    $db_model_ch = $objDBCA->getModelChannel();
        $arrayConfig = array(
            "DISP_SEQ"=>"",
            "DISUSE_FLAG"=>"",
            "EXECUTION_NO"=>"",
            "SYMPHONY_NAME"=>"",
            "EXECUTION_USER"=>"",
            "HEAT_INPUT"=>"",
            "HEAT_RESULT"=>"",
            "I_OPERATION_NAME"=>"",
            "I_OPERATION_NO_IDBH"=>"",
            "I_PATTERN_NAME"=>"",
            "I_TIME_LIMIT"=>"",
            "JOURNAL_ACTION_CLASS"=>"",
            "JOURNAL_REG_DATETIME"=>"",
            "JOURNAL_SEQ_NO"=>"",
            "LAST_UPDATE_TIMESTAMP"=>"",
            "LAST_UPDATE_USER"=>"",
            "NOTE"=>"",
            "OPERATION_NO_UAPK"=>"",
            "PATTERN_ID"=>"",
            "RUN_MODE"=>"",
            "STATUS_ID"=>"",
            "TIME_BOOK"=>"",
            "TIME_END"=>"",
            "TIME_START"=>""
        );
        $temp_array = array('WHERE'=>" DISUSE_FLAG = 0 AND EXECUTION_NO = :EXECUTION_NO");
        $arrayValue = $arrayConfig;
        $bindArray["EXECUTION_NO"] =$array['EXECUTION_NO'];

        $tbl_key = "EXECUTION_NO";
        $tbl_name = "C_OPENST_RESULT_MNG";
        $tbl_name_jnl = "C_OPENST_RESULT_MNG_JNL";
        $do = "SELECT FOR UPDATE";

        try{
            require($root_dir_path."/libs/backyardlibs/openstack_driver/openStack_driver_selectTable.php");
        }catch(Exception $e){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,$e->getMessage());
            require ($root_dir_path . $log_output_php );
            $error_flag = 1;
            return false;
        }

        //結果は1件か0件のはず
        switch (count($tgt_execution_row)){
        case 0:
            $do = "INSERT";
            $cln_execution_row = array();
            $cln_execution_row[$tbl_key] = lockAndGetSequence($tbl_name."_RIC");
            $cln_execution_row["DISUSE_FLAG"] = 0;
            $cln_execution_row['JOURNAL_SEQ_NO'] = lockAndGetSequence($tbl_name."_JSQ");
            $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;
            $cln_execution_row=array_merge($cln_execution_row,$array);

            break;
        case 1:
            $do = "UPDATE";
            $cln_execution_row = $tgt_execution_row[0];
            $cln_execution_row['JOURNAL_SEQ_NO'] = lockAndGetSequence($tbl_name."_JSQ");
            $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;
            $cln_execution_row=array_merge($cln_execution_row,$array);
            break;
        default:
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
            require ($root_dir_path . $log_output_php );
        }

        //投入実行
        try{
            require($root_dir_path."/libs/backyardlibs/openstack_driver/openStack_driver_executeTable.php");
        }catch(Exception $e){
            $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,$e->getMessage());
            require ($root_dir_path . $log_output_php );
            $error_flag = 1;
            return false;
        }

        return true;
}

//DETAILレコードのINSERT、UPDATEを管理
function resultDetailUpdate($array){

    global $objDBCA;
    global $objMTS;
    global $root_dir_path;
    global $log_file_prefix;
    global $log_output_php;
    global $db_access_user_id;

    //対象を探してロック
    $db_model_ch = $objDBCA->getModelChannel();
    $arrayConfig = array(
        "JOURNAL_SEQ_NO"=>"",
        "JOURNAL_ACTION_CLASS"=>"",
        "JOURNAL_REG_DATETIME"=>"",
        "RESULT_DETAIL_ID"=>"",
        "EXECUTION_NO"=>"",
        "EXECUTION_USER"=>"",
		"SYMPHONY_NAME"=>"",
        "STATUS_ID"=>"",
        "STACK_ID"=>"",
        "STACK_URL"=>"",
        "SYSTEM_ID"=>"",
        "SYSTEM_NAME"=>"",
        "REQUEST_TEMPLATE"=>"",
        "RESPONSE_JSON"=>"",
        "RESPONSE_MESSAGE"=>"",
        "TIME_START"=>"",
        "TIME_END"=>"",
        "DISP_SEQ"=>"",
        "NOTE"=>"",
        "DISUSE_FLAG"=>"",
        "LAST_UPDATE_TIMESTAMP"=>"",
        "LAST_UPDATE_USER"=>""
    );
    $arrayValue = $arrayConfig;

    if(isset($array['RESULT_DETAIL_ID'])){
        $temp_array = array('WHERE'=>" DISUSE_FLAG = 0 AND RESULT_DETAIL_ID = :RESULT_DETAIL_ID");
        $bindArray["RESULT_DETAIL_ID"] =$array['RESULT_DETAIL_ID'];
    }else{
        $temp_array = array('WHERE'=>" DISUSE_FLAG = 0 AND EXECUTION_NO = :EXECUTION_NO"
            ." AND SYSTEM_ID = :SYSTEM_ID");
        $bindArray["EXECUTION_NO"] =$array['EXECUTION_NO'];
        $bindArray["SYSTEM_ID"] =$array['SYSTEM_ID'];
    }

    $tbl_key = "RESULT_DETAIL_ID";
    $tbl_name = "C_OPENST_RESULT_DETAIL";
    $tbl_name_jnl = "C_OPENST_RESULT_DETAIL_JNL";
    $do = "SELECT FOR UPDATE";

    try{
        require($root_dir_path."/libs/backyardlibs/openstack_driver/openStack_driver_selectTable.php");
    }catch(Exception $e){
        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,$e->getMessage());
        require ($root_dir_path . $log_output_php );
        $error_flag = 1;
        return false;
    }
    //結果は1件か0件のはず

    switch (count($tgt_execution_row)){
    case 0:
        $do = "INSERT";

        // nullが入っているRESULT_DETAIL_IDは除去
        unset($array['RESULT_DETAIL_ID']);

        $cln_execution_row = array();
        $cln_execution_row[$tbl_key] = lockAndGetSequence($tbl_name."_RIC");
        $cln_execution_row["DISUSE_FLAG"] = 0;
        $cln_execution_row['JOURNAL_SEQ_NO'] = lockAndGetSequence($tbl_name."_JSQ");
        $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;
        $cln_execution_row=array_merge($cln_execution_row,$array);

        break;
    case 1:
        $do = "UPDATE";
        $cln_execution_row = $tgt_execution_row[0];
        $cln_execution_row['JOURNAL_SEQ_NO'] = lockAndGetSequence($tbl_name."_JSQ");
        $cln_execution_row["LAST_UPDATE_USER"] = $db_access_user_id;
        $cln_execution_row=array_merge($cln_execution_row,$array);

        break;
    default:
        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,$objQueryUtn->getLastError());
        require ($root_dir_path . $log_output_php );
        $error_flag = 1;
        throw new Exception($objQueryUtn->getLastError());
    }

    if(!empty($cln_execution_row['REQUEST_TEMPLATE'])) {
        $safeStr = binarySafeAndSubstr($cln_execution_row['REQUEST_TEMPLATE'], 2014);
        $cln_execution_row['REQUEST_TEMPLATE'] = $safeStr;
    }

    if(!empty($cln_execution_row['RESPONSE_JSON'])) {
        $safeStr = binarySafeAndSubstr($cln_execution_row['RESPONSE_JSON'], 4000);
        $cln_execution_row['RESPONSE_JSON'] = $safeStr;
    }

    if(!empty($cln_execution_row['RESPONSE_MESSAGE'])) {
        $safeStr = binarySafeAndSubstr($cln_execution_row['RESPONSE_MESSAGE'], 4000);
        $cln_execution_row['RESPONSE_MESSAGE'] = $safeStr;
    }

    //投入実行
    try{
        require($root_dir_path."/libs/backyardlibs/openstack_driver/openStack_driver_executeTable.php");
    }catch(Exception $e){
        $FREE_LOG = sprintf("FILE:%s LINE:%s %s",basename(__FILE__),__LINE__,$e->getMessage());
        require ($root_dir_path . $log_output_php );
        $error_flag = 1;
        return false;
    }

    return true;
}

function lockAndGetSequence($tblName){
    $retArray = getSequenceLockInTrz($tblName,'A_SEQUENCE');
    if( $retArray[1] != 0 ){
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception( "a" );
    }

    // 履歴シーケンス払い出し
    $retArray = getSequenceValueFromTable($tblName, 'A_SEQUENCE', FALSE );
    if( $retArray[1] != 0 ){
        // 異常フラグON
        $error_flag = 1;
        // 例外処理へ
        throw new Exception( "b" );
    }

    //次の項番を返す
    return $retArray[0];
}

function binarySafeAndSubstr($data, $length) {

    global $objMTS;
    global $root_dir_path;
    global $log_file_prefix;
    global $log_output_php;
    global $log_level;

    if(searchNullByte($data)) {

        if ($log_level === 'DEBUG') {
            // バイナリデータのため、代替文字列に置換します "(binary)"
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-57010");
            require ($root_dir_path . $log_output_php );
        }

        return "(binary)";
    }
    // mb_detect_encoding() === false でテキストか判定するか？ 他に判定方法は？

    if(strlen($data) > $length) {

        if ($log_level === 'DEBUG') {
            // 文字列が長すぎるため、末尾をカットします
            $FREE_LOG = $objMTS->getSomeMessage("ITAOPENST-STD-57020");
            require ($root_dir_path . $log_output_php );
        }

        return substr($data, 0, $length -3) . "...";
    }

    return $data;
}

//多次元配列から単配列へ変換
function array_flatten($array){
    $result = array();
        foreach($array as $val){
            if(is_array($val)){
                $result = array_merge($result, array_flatten($val));
            }else{
                $result[]=$val;
            }
        }
    return $result;
}
?>
