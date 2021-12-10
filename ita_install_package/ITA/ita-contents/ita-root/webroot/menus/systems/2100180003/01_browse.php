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
        if( array_key_exists( "conductor_class_id", $_GET ) === true ){
            // クエリからsymphony_instance_idを取得
            $conductor_class_id = htmlspecialchars($_GET["conductor_class_id"], ENT_QUOTES, "UTF-8");

            // 整数の場合のみ判定
            $objIntNumVali = new IntNumValidator(null,null,"","",array("NOT_NULL"=>true));
            if( $objIntNumVali->isValid($conductor_class_id) === true ){
                // SQL生成
                $sql = "SELECT  ACCESS_AUTH
                        FROM    C_CONDUCTOR_EDIT_CLASS_MNG
                        WHERE   DISUSE_FLAG = '0'
                        AND     CONDUCTOR_CLASS_NO = :CONDUCTOR_CLASS_NO_BV ";

                $objQuery = $g['objDBCA']->sqlPrepare($sql);

                if($objQuery->getStatus()===false){
                    // 例外処理へ
                    throw new Exception();
                }

                $objQuery->sqlBind( array( 'CONDUCTOR_CLASS_NO_BV'=>$conductor_class_id ) );

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
    $timeStamp_itabase_symphony_class_info_access_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_symphony_class_info_access.js");
    $timeStamp_itabase_symphony_class_edit_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_symphony_class_edit.js");

print <<< EOD
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script>const gLoginUserID = {$g['login_id']};</script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/editor_conductor.js?{$timeStamp_editor_conductor_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_info_access.js?{$timeStamp_itabase_symphony_class_info_access_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_edit.js?{$timeStamp_itabase_symphony_class_edit_js}"></script>
    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/editor_conductor.css?{$timeStamp_editor_conductor_style_css}">
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
    
    //$strPageInfo = "説明";
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-204040");

    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
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
          <ul class="editor-menu-list conductor-header-menu1">
            <li class="editor-menu-item edit"><button class="editor-menu-button" data-menu="conductor-new">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309007")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button" data-menu="conductor-save">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309008")}</button></li>
            <li class="editor-menu-item edit"><button class="editor-menu-button" data-menu="conductor-read">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309009")}</button></li>
          </ul>
          <ul class="editor-menu-list conductor-header-menu2">
            <li class="editor-menu-item"><button id="button-undo" class="editor-menu-button" data-menu="undo" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309010")}</button></li>
            <li class="editor-menu-item"><button id="button-redo" class="editor-menu-button" data-menu="redo" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309011")}</button></li>
          </ul>
          <ul class="editor-menu-list conductor-header-menu3">
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
                  <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309058")}</dd>
                  <dt class="explanation-term"><span class="mouse-icon mouse-left"></span>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309059")}</dt>
                  <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309060")}</dd>
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
                  <li class="editor-tab-menu-item" data-tab="movement">Movement</li>
                  <li class="editor-tab-menu-item" data-tab="function">Function</li>
                  <li class="editor-tab-menu-item" data-tab="conditional-branch">Conditional branch</li>
                  <li class="editor-tab-menu-item" data-tab="parallel-branch">Parallel branch</li>
                  <li class="editor-tab-menu-item" data-tab="merge">Parallel merge</li>
                  <li class="editor-tab-menu-item" data-tab="status-file-branch">Status file branch</li>
                  <li class="editor-tab-menu-item" data-tab="call">Conductor call</li>
                  <li class="editor-tab-menu-item" data-tab="call_s">Symphony call</li>
                  <li class="editor-tab-menu-item" data-tab="end">End</li>
                  <li class="editor-tab-menu-item" data-tab="multiple">Node</li>
                </ul>
              </div><!-- /.editor-tab-menu -->

              <div class="editor-tab-contents">
                
                <!-- Conductor -->
                <div id="conductor" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">ID :</th>
                          <td class="panel-td" colspan="2"><span id="conductor-class-id" class="panel-span"></span></td>
                        </tr>
                        <tr title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309048")}">
                          <th class="panel-th">Name :</th>
                          <td class="panel-td" colspan="2"><input maxlength="256" id="conductor-class-name" class="edit panel-text" type="text"><span id="conductor-class-name-view" class="view panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Notice :</th>
                          <td class="panel-td"><span id="conductor-notice-status" class="panel-span"></span></td>
                          <td class="panel-td panel-td-button"><button id="conductor-notice-select" class="panel-button">Select</button></td>
                        </tr>
                        <tr class="view">
                          <th class="panel-th">Role :</th>
                          <td class="panel-td" colspan="2"><span id="conductor-view-role" class="panel-span"></span></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group edit">
                      <div class="panel-group-title">Permission role</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Role :</th>
                            <td class="panel-td"><span id="conductor-edit-role" class="panel-span"></span></td>
                          </tr>
                        </tbody>
                      </table>
                      <ul class="panel-button-group">
                        <li class="panel-button-group-item"><button id="conductor-role-select" class="panel-button">Select</button></li>
                      </ul>                      
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="conductor-class-note" class="edit panel-note panel-textarea" spellcheck="false"></textarea>
                      <span id="conductor-class-note-view" class="view panel-note panel-span"></span>
                    </div>                    
                  </div>
                </div>

                <!-- Movement -->
                <div id="movement" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Movement ID :</th>
                          <td class="panel-td"><span id="movement-id" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Orchestrator :</th>
                          <td class="panel-td"><span id="movement-orchestrator" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Name :</th>
                          <td class="panel-td"><span id="movement-name" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Default skip :</th>
                          <td class="panel-td"><input id="movement-default-skip" class="panel-checkbox" type="checkbox"></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Operation select</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Operation :</th>
                            <td class="panel-td"><span id="movement-operation" class="panel-span" data-id="" data-value=""></span></td>
                          </tr>
                        </tbody>
                      </table>
                      <ul class="panel-button-group">
                        <li class="panel-button-group-item"><button id="movement-operation-select" class="panel-button">Select</button></li>
                        <li class="panel-button-group-item"><button id="movement-operation-clear" class="panel-button">Clear</button></li>
                      </ul>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="movement-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- End -->
                <div id="end" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">End status :</th>
                          <td class="panel-td">
                            <div class="end-status-select"></div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="end-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Function -->
                <div id="function" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Type :</th>
                          <td class="panel-td"><span id="function-type" class="panel-span"></span></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="function-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Conditional-branch -->
                <div id="conditional-branch" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Case :</th>
                          <td class="panel-td">
                            <ul class="panel-button-group">
                              <li class="panel-button-group-item"><button class="branch-add panel-button">Add</button></li>
                              <li class="panel-button-group-item"><button class="branch-delete panel-button">Delete</button></li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <hr class="panel-hr">
                    <div id="branch-condition-move">
                    <table id="branch-case-list" class="panel-table ">
                      <tbody>
                      </tbody>
                    </table>
                    <hr class="panel-hr">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Other :</th>
                          <td class="panel-td"><ul id="noset-conditions" class="branch-case"></ul></td>
                        </tr>
                      </tbody>
                    </table>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="branch-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Parallel-branch -->
                <div id="parallel-branch" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Case :</th>
                          <td class="panel-td">
                            <ul class="panel-button-group">
                              <li class="panel-button-group-item"><button class="branch-add panel-button">Add</button></li>
                              <li class="panel-button-group-item"><button class="branch-delete panel-button">Delete</button></li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="marge-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Merge-->
                <div id="merge" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Case :</th>
                          <td class="panel-td">
                            <ul class="panel-button-group">
                              <li class="panel-button-group-item"><button class="branch-add panel-button">Add</button></li>
                              <li class="panel-button-group-item"><button class="branch-delete panel-button">Delete</button></li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="merge-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- status-file-branch -->
                <div id="status-file-branch" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Case :</th>
                          <td class="panel-td">
                            <ul class="panel-button-group">
                              <li class="panel-button-group-item"><button class="branch-add panel-button">Add</button></li>
                              <li class="panel-button-group-item"><button class="branch-delete panel-button">Delete</button></li>
                            </ul>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <hr class="panel-hr">
                    <div id="status-file-case-move">
                    <table id="status-file-case-list" class="panel-table ">
                      <tbody>
                      </tbody>
                    </table>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="status-file-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Conductor call -->
                <div id="call" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Default skip :</th>
                          <td class="panel-td"><input id="conductor-call-default-skip" class="panel-checkbox" type="checkbox"></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Conductor select</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Conductor :</th>
                            <td class="panel-td"><span id="conductor-call-name" class="panel-span" data-id="" data-value=""></span></td>
                          </tr>
                        </tbody>
                      </table>
                      <ul class="panel-button-group">
                        <li class="panel-button-group-item"><button id="conductor-call-select" class="panel-button">Conductor select</button></li>
                        <li class="panel-button-group-item"><button id="conductor-call-clear" class="panel-button">Clear</button></li>
                      </ul>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Operation select</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Operation :</th>
                            <td class="panel-td"><span id="conductor-call-operation" class="panel-span" data-id="" data-value=""></span></td>
                          </tr>
                        </tbody>
                      </table>
                      <ul class="panel-button-group">
                        <li class="panel-button-group-item"><button id="conductor-call-operation-select" class="panel-button">Operation select</button></li>
                        <li class="panel-button-group-item"><button id="conductor-call-operation-clear" class="panel-button">Clear</button></li>
                      </ul>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="conductor-call-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Symphony call -->
                <div id="call_s" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Default skip :</th>
                          <td class="panel-td"><input id="symphony-call-default-skip" class="panel-checkbox" type="checkbox"></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Symphony select</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Symphony :</th>
                            <td class="panel-td"><span id="symphony-call-name" class="panel-span" data-id="" data-value=""></span></td>
                          </tr>
                        </tbody>
                      </table>
                      <ul class="panel-button-group">
                        <li class="panel-button-group-item"><button id="symphony-call-select" class="panel-button">Symphony select</button></li>
                        <li class="panel-button-group-item"><button id="symphony-call-clear" class="panel-button">Clear</button></li>
                      </ul>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Operation select</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Operation :</th>
                            <td class="panel-td"><span id="symphony-call-operation" class="panel-span" data-id="" data-value=""></span></td>
                          </tr>
                        </tbody>
                      </table>
                      <ul class="panel-button-group">
                        <li class="panel-button-group-item"><button id="symphony-call-operation-select" class="panel-button">Operation select</button></li>
                        <li class="panel-button-group-item"><button id="symphony-call-operation-clear" class="panel-button">Clear</button></li>
                      </ul>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <textarea title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309049")}" id="symphony-call-note" class="panel-note panel-textarea" spellcheck="false"></textarea>
                    </div>
                  </div>
                </div>
                
                <!-- Node 複数選択 -->
                <div id="multiple" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="panel-group">
                      <div class="panel-group-title">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309038")}</div>
                      <ul id="node-align" class="panel-button-group">
                        <li class="panel-button-group-item"><button id="node-align-left" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309040")}"><span class="align-icon algin-left"></span></button></li>
                        <li class="panel-button-group-item"><button id="node-align-vertical" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309041")}"><span class="align-icon algin-vertical"></span></button></li>
                        <li class="panel-button-group-item"><button id="node-align-right" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309042")}"><span class="align-icon algin-right"></span></button></li>
                        <li class="panel-button-group-item"><button id="node-align-top" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309043")}"><span class="align-icon algin-top"></span></button></li>
                        <li class="panel-button-group-item"><button id="node-align-horizonal" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309044")}"><span class="align-icon algin-horizonal"></span></button></li>
                        <li class="panel-button-group-item"><button id="node-align-bottom" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309045")}"><span class="align-icon algin-bottom"></span></button></li>
                      </ul>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309039")}</div>
                      <ul id="node-equally-spaced" class="panel-button-group">
                        <li class="panel-button-group-item"><button id="node-equally-spaced-vertical" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309046")}"><span class="align-icon algin-equally-vertical"></span></button></li>
                        <li class="panel-button-group-item"><button id="node-equally-spaced-horizonal" class="panel-button" title="{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309047")}"><span class="align-icon algin-equally-horizonal"></span></button></li>
                      </ul>
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
                  <li class="editor-tab-menu-item" data-tab="movement-list">Movement</li>
                  <li class="editor-tab-menu-item" data-tab="function-list">Function</li>
                </ul>
              </div><!-- /.editor-tab-menu -->

              <div class="editor-tab-contents">

                <div id="movement-list" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="movement-filter">
                      <table class="panel-table">
                        <tbody>
                          <tr class="movement-filter-row">
                            <th class="panel-th">Name Filter :</th>
                            <td class="panel-td"><input id="movement-filter" class="panel-text" type="text" placeholder="Movement Name"><span class="filter-setting-btn" title="Filter setting"></span></td>
                          </tr>
                          <tr class=""movement-filter-id-row">
                            <th class="panel-th">ID Filter :</th>
                            <td class="panel-td"><input id="movement-filter-id" class="panel-text" type="text" placeholder="Movement ID"><span class="filter-setting-btn" title="Filter setting"></span></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="node-table-wrap">
                      <table class="node-table">
                        <thead>
                          <tr>
                            <th class="movement-list-orchestrator" title="Orchestrator"><div class="movement-list-sort" data-sort="ORCHESTRATOR_ID" data-sort-type="number">+</div></th>
                            <th class="movement-list-id" title="Movement ID"><div class="movement-list-sort" data-sort="PATTERN_ID" data-sort-type="number">ID</div></th>
                            <th class="movement-list-name" title="Movement Name"><div class="movement-list-sort" data-sort="PATTERN_NAME" data-sort-type="string">Movement name</div></th>
                          </tr>
                        </thead>
                        <tbody id="movement-list-rows">
                        </tbody>
                      </table>
                    </div>
                    <div id="movement-filter-setting">
                      <div class="movement-filter-setting-inner">
                        <div class="movement-filter-setting-body">
                          <div class="panel-group">
                            <div class="panel-group-title">Filter target</div>
                            <ul class="movement-filter-setting-list">
                              <li><label class="property-label"><input type="radio" name="filter-target" id="filter-target-id"> Movement ID</label></li>
                              <li><label class="property-label"><input type="radio" name="filter-target" id="filter-target-name" checked> Movement Name</label></li>
                            </ul>
                          </div>
                          <div class="panel-group">
                            <div class="panel-group-title">Orchestrator select</div>
                            <ul id="orchestrator-list" class="movement-filter-setting-list">
                            </ul>
                          </div>
                        </div>
                        <div class="movement-filter-setting-footer">
                          <ul class="panel-button-group">
                            <li class="panel-button-group-item"><button id="movement-filter-ok" class="positive panel-button">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309036")}</button></li>
                            <li class="panel-button-group-item"><button id="movement-filter-cancel" class="negative panel-button">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309037")}</button></li>
                          </ul>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div id="function-list" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="node-table-wrap">
                      <table class="node-table">
                        <thead>
                          <tr><th><div>+</div></th><th><div>Function type</div></th></tr>
                        </thead>
                        <tbody>
                          <tr><th><span class="add-node function" data-function-type="end"></span></th><td><div>Conductor end</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="pause"></span></th><td><div>Conductor pause</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="call"></span></th><td><div>Conductor call</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="call_s"></span></th><td><div>Symphony call</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="conditional-branch"></span></th><td><div>Conditional branch</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="parallel-branch"></span></th><td><div>Parallel branch</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="merge"></span></th><td><div>Parallel merge</div></td></tr>
                          <tr><th><span class="add-node function" data-function-type="status-file-branch"></span></th><td><div>Status file branch</div></td></tr>
                          <!--<tr><th><span class="add-node function" data-function-type="blank-node"></span></th><td><div>Blank node</div></td></tr>-->
                        </tbody>
                      </table>
                    </div>
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
          <ul class="editor-menu-list edit">
            <li class="editor-menu-item"><button class="editor-menu-button positive" data-menu="registration">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309018")}</button></li>
          </ul>
          <ul class="editor-menu-list view">
            <li class="editor-menu-item"><button class="editor-menu-button positive" data-menu="edit">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309019")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button positive" data-menu="diversion">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309020")}</button></li>
          </ul>
          <ul class="editor-menu-list update">
            <li class="editor-menu-item"><button class="editor-menu-button positive" data-menu="update">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309021")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button negative" data-menu="refresh">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309022")}</button></li>
            <li class="editor-menu-item"><button class="editor-menu-button negative" data-menu="cancel">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309023")}</button></li>
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
