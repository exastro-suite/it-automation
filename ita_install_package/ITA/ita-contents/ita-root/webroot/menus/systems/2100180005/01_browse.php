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
    //    ・Symphonyクラスを定義するページの、各種動的機能を呼び出す
    //
    //////////////////////////////////////////////////////////////////////

    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = htmlspecialchars($_GET['no'], ENT_QUOTES, "UTF-8");
    }
    $privilege = "";

    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // browse系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");

        //アクセス権を判定
        if( array_key_exists( "conductor_instance_id", $_GET ) === true ){
            // クエリからconductor_instance_idを取得
            $conductor_instance_id = htmlspecialchars($_GET["conductor_instance_id"], ENT_QUOTES, "UTF-8");
            
            // 整数の場合のみ判定
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($conductor_instance_id) === true ){
                // SQL生成
                $sql = "SELECT  ACCESS_AUTH
                        FROM    C_CONDUCTOR_INSTANCE_MNG
                        WHERE   DISUSE_FLAG = '0'
                        AND     CONDUCTOR_INSTANCE_NO = :CONDUCTOR_INSTANCE_NO_BV ";
                
                $objQuery = $g['objDBCA']->sqlPrepare($sql);
                
                if($objQuery->getStatus()===false){
                    // 例外処理へ
                    throw new Exception();
                }
                
                $objQuery->sqlBind( array( 'CONDUCTOR_INSTANCE_NO_BV'=>$conductor_instance_id ) );
                
                $r = $objQuery->sqlExecute();
                
                if (!$r){
                    // 例外処理へ
                    throw new Exception();
                }

                // ログインユーザーのロール・ユーザー紐づけ情報を内部展開
                $obj = new RoleBasedAccessControl($g['objDBCA']);
                $ret  = $obj->getAccountInfo($g['login_id']);
                if($ret === false) {
                    // 例外処理へ
                    throw new Exception();
                }

                while ( $row = $objQuery->resultFetch() ){
                    // アクセス権を判定
                    list($ret,$permission) = $obj->chkOneRecodeAccessPermission($row);
                    if($ret === false) {
                        // 例外処理へ
                        throw new Exception();
                    } else {
                        if($permission === false) {
                            //アクセス権が無いため、例外処理へ
                            throw new Exception();
                        }
                    }
                }
            }
        }

    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    
    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_editor_conductor_style_css=filemtime("$root_dir_path/webroot/common/css/editor_conductor.css");
    $timeStamp_editor_conductor_js=filemtime("$root_dir_path/webroot/common/javascripts/editor_conductor.js");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");

print <<< EOD
    <script>const gLoginUserID = {$g['login_id']};</script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/editor_conductor.js?{$timeStamp_editor_conductor_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>

    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/editor_conductor.css?{$timeStamp_editor_conductor_style_css}">
    <style>
      #conductor-parameter { height: 70%; }
      #editor-panel .editor-row-resize-bar { top: 70%; }
      .editor-block:last-child { height: 30%; }
    </style>
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    if("1" === $g['menu_autofilter']){
        $checkBoxChecked="checked=\"checked\"";
    }
    else{
        $checkBoxChecked="";
    }
    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");
    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----
    
    $strDeveloperArea = "";
    
    $interval = 3000;  //エラー等の場合の初期値

    $strQuery = "SELECT CONDUCTOR_REFRESH_INTERVAL FROM C_CONDUCTOR_IF_INFO WHERE DISUSE_FLAG = '0'";

    $aryForBind = array();
    $strFxName  = "";
    $tmpStrInterVal = "";
    $aryRetBody = singleSQLExecuteAgent($strQuery, $aryForBind, $strFxName);


    if( $aryRetBody[0] === true ){
        $objQuery = $aryRetBody[1];

        

        if($objQuery->effectedRowCount() == 0) {
            web_log($objMTS->getSomeMessage("ITABASEH-ERR-170103"));
            

        } else {
            if($objQuery->effectedRowCount() == 1) {
                $row = $objQuery->resultFetch();
                $tmpStrInterVal = $row['CONDUCTOR_REFRESH_INTERVAL'];
            } else {
                web_log($objMTS->getSomeMessage("ITABASEH-ERR-170104"));
            }
        }
        unset($objQuery);
    }else{
        web_log($objMTS->getSomeMessage("ITABASEH-ERR-1990009",array("C_CONDUCTOR_IF_INFO")));
    }
    if( 0 < strlen($tmpStrInterVal) ){
        if( is_numeric($tmpStrInterVal) === true ){
            $tmpIntInterVal = intval($tmpStrInterVal);
            if( $tmpIntInterVal <= 0 ){
                //----[WARNING: SETTING CONDUCTOR INSTANCE MONITOR INTERVAL IS LESS THAN OVER EQUAL TO 0.]
                web_log($objMTS->getSomeMessage("ITABASEH-ERR-170105"));
            }
            else{
                $interval = $tmpIntInterVal;
            }
        }
        else{
            //----[WARNING: SETTING CONDUCTOR INSTANCE MONITOR INTERVAL IS NOT NUMERIC.]
            web_log($objMTS->getSomeMessage("ITABASEH-ERR-170106"));
        }
    }    unset($tmpStrInterVal);
    unset($tmpIntInterVal);
    
    //$strPageInfo = "説明";
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-204040");

    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="intervalOfDisp" style="display:none" class="text">{$interval}</div>
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
{$strDeveloperArea}
EOD;


    print 
<<< EOD

    <!--================--> 
    <!--　　エディタ　　--> 
    <!--================-->
<div id="editor" class="load-wait" data-editor-mode="edit">
  <div class="editor-inner">



    <div id="editor-header">
      <div id="editor-mode"></div>
      <div class="editor-header-menu">
        <div class="editor-header-main-menu">
          <ul class="editor-menu-list edit">
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="conductor-new">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309007")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="conductor-save">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309008")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="conductor-read">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309009")}</button></li>
          </ul>
          <ul class="editor-menu-list">
            <li class="editor-menu-item"><button id="button-undo" class="editor-menu-button" data-menu="undo" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309010")}</button></li>
            <li class="editor-menu-item"><button id="button-redo" class="editor-menu-button" data-menu="redo" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309011")}</button></li>
          </ul>
          <ul class="editor-menu-list">
            <li class="editor-menu-item"><button id="node-delete-button" class="editor-menu-button" data-menu="node-delete" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309012")}</button></li>
          </ul>
        </div>
        <div class="editor-header-sub-menu">
          <ul class="editor-menu-list">
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="view-all">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309013")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="view-reset">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309014")}</button></li>
            <li class="editor-menu-item full-screen-hide"><button class="editor-menu-button" data-menu="full-screen-on">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309015")}</button></li>
            <li class="editor-menu-item full-screen-show"><button class="editor-menu-button" data-menu="full-screen-off">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309016")}</button></li>
          </ul>
        </div>
      </div>
    </div><!-- /#editor-header -->



    <div id="editor-main">

      <div id="editor-body" class="editor-row-resize">

        <div id="editor-edit" class="editor-block">
          <div class="editor-block-inner">
          
            <div id="canvas-visible-area">
              <div id="canvas">
                <div id="art-board">      
                </div><!-- / .art-board -->
              </div><!-- / .canvas -->
            </div><!-- / .canvas-visible-area -->
            
            <div id="editor-display">
              <div id="editor-explanation">
                <dl class="explanation-list">
                  <dt class="explanation-term"><span class="mouse-icon mouse-left"></span>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309057")}</dt>
                  <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309062")}</dd>
                  <dt class="explanation-term"><span class="mouse-icon mouse-wheel"></span>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309053")}</dt>
                  <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309054")}</dd>
                  <dt class="explanation-term"><span class="mouse-icon mouse-right"></span>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309055")}</dt>
                  <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309056")}</dd>
                </dl>
              </div>
            </div><!-- / #editor-display -->
            
          </div>
        </div><!-- /#editor-edit -->

        <div class="editor-row-resize-bar"></div>

        <div id="editor-info" class="editor-block">
          <div class="editor-block-inner">

            <div class="editor-tab">
            
              <div class="editor-tab-menu">
                <ul class="editor-tab-menu-list">
                  <li class="editor-tab-menu-item" data-tab="log">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309017")}</li>
                </ul>
              </div><!-- /.editor-tab-menu -->

              <div class="editor-tab-contents">

                <div id="log" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="editor-log">
                      <table class="editor-log-table">
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>            

              </div><!-- /.editor-tab-contents -->
              
            </div><!-- /.editor-tab -->

          </div>
        </div><!-- /#editor-info -->

      </div><!-- /#editor-body -->

      <div id="editor-panel" class="editor-row-resize">
      
        <div id="conductor-parameter" class="editor-block">
          <div class="editor-block-inner">
          
            <div class="editor-tab">
            
              <div class="editor-tab-menu">
                <ul class="editor-tab-menu-list">
                  <li class="editor-tab-menu-item" data-tab="conductor">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309027")}</li>
                  <li class="editor-tab-menu-item" data-tab="node">Node</li>
                </ul>
              </div><!-- /.editor-tab-menu -->

              <div class="editor-tab-contents">
                
                <!-- Conductor -->
                <div id="conductor" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Conductor instance ID :</th>
                          <td class="panel-td"><span id="conductor-instance-id" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Conductor name :</th>
                          <td class="panel-td"><span id="conductor-class-name-view" class="view panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Status :</th>
                          <td class="panel-td"><span id="conductor-instance-status" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Pause status :</th>
                          <td class="panel-td"><span id="conductor-instance-pause" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Start time :</th>
                          <td class="panel-td"><span id="conductor-instance-start" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">End time :</th>
                          <td class="panel-td"><span id="conductor-instance-end" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Execution user :</th>
                          <td class="panel-td"><span id="conductor-instance-user" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Reservation date :</th>
                          <td class="panel-td"><span id="conductor-instance-reservation" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Emergency stop :</th>
                          <td class="panel-td"><span id="conductor-instance-emergency" class="panel-span"></span></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="conductor-class-note-view" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Node -->
                <div id="node" class="editor-tab-body" data-node-type="">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Node type :</th>
                          <td class="panel-td"><span id="node-type" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Node instance ID :</th>
                          <td class="panel-td"><span id="node-instance-id" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Node name :</th>
                          <td class="panel-td"><span id="node-name" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Status :</th>
                          <td class="panel-td"><span id="node-status" class="panel-span"></span></td>
                        </tr>
                        <tr class="type-status-file">
                          <th class="panel-th">Status file:</th>
                          <td class="panel-td"><span id="node-status-file" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Start time :</th>
                          <td class="panel-td"><span id="node-start" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">End time :</th>
                          <td class="panel-td"><span id="node-end" class="panel-span"></span></td>
                        </tr>
                        <tr class="type-movement">
                          <th class="panel-th">Operation status :</th>
                          <td class="panel-td"><span id="node-Jump" class="panel-span"></span></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group type-movement">
                      <div class="panel-group-title">Specified individually operation</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Operation ID :</th>
                            <td class="panel-td"><span id="node-oepration-id" class="panel-span"></span></td>
                          </tr>
                          <tr>
                            <th class="panel-th">Operation name :</th>
                            <td class="panel-td"><span id="node-operation-name" class="panel-span"></span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="function-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>

              </div><!-- /.editor-tab-contents -->
              
            </div><!-- /.editor-tab -->
            
          </div>
        </div>

        <div class="editor-row-resize-bar"></div>
        
        <div class="editor-block">
          <div class="editor-block-inner">
          
            <div class="editor-tab">
            
              <div class="editor-tab-menu">
                <ul class="editor-tab-menu-list">
                  <li class="editor-tab-menu-item" data-tab="select-operation">Operation</li>
                </ul>
              </div><!-- /.editor-tab-menu -->

              <div class="editor-tab-contents">

                <div id="select-operation" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Operation ID :</th>
                          <td class="panel-td"><span id="select-operation-id" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Operation name :</th>
                          <td class="panel-td"><span id="select-operation-name" class="panel-span"></span></td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>             

              </div><!-- /.editor-tab-contents -->
              
            </div><!-- /.editor-tab -->
            
          </div>
        </div>
        
      </div><!-- /#editor-panel -->

    </div><!-- /#editor-main -->



    <div id="editor-footer">
      <div class="editor-footer-menu">
        <div class="editor-footer-main-menu">
          <ul class="editor-menu-list">
            <li class="editor-menu-item"><button id="cansel-instance" class="editor-menu-button positive" data-menu="cansel-instance" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309025")}</button></li>
            <li class="editor-menu-item"><button id="scram-instance" class="editor-menu-button positive" data-menu="scram-instance" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309026")}</button></li>
          </ul>
        </div>
        <div class="editor-footer-sub-menu"></div>
      </div>
    </div><!-- /#editor-footer -->



  </div><!-- /#editor -->
</div>

EOD;

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");

?>
