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
    $tmpAry=explode('ita-root', dirname(__FILE__));$root_dir_path=$tmpAry[0].'ita-root';unset($tmpAry);
    if(array_key_exists('no', $_GET)){
        $g['page_dir']  = htmlspecialchars($_GET['no'], ENT_QUOTES, "UTF-8");
    }

    try{
        // DBコネクト
        require_once ( $root_dir_path . "/libs/commonlibs/common_php_req_gate.php");
        
        // 共通設定取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_get_sysconfig.php");
        
        // メニュー情報取得パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_menu_info.php");
        
        // browse系共通ロジックパーツ01
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_01.php");
        
        // メンテナンス可能メニューを参照のみ可能の権限ユーザが見てないか判定するパーツ
        // (この処理は非テンプレートのコンテンツのみに必要)
        // コメントを解除
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_maintenance.php");
    }
    catch (Exception $e){
        // DBアクセス例外処理パーツ
        require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_db_access_exception.php");
    }
    
    // 共通HTMLステートメントパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_statement.php");
    
    //----シンフォニー用
    $aryTmpVariant1 = array();
    $aryTmpSetting1 = array();
    
    $symphony_class_dir = "2100180002";
    require_once($g['root_dir_path'] . "/webconfs/systems/2100180002_loadTable.php");
    $objTable1 = loadTable($symphony_class_dir,$aryTmpVariant1,$aryTmpSetting1);

    $tmpRetArray = getFilterCommandArea($objTable1,$aryTmpVariant1,$aryTmpSetting1,"filter_table","Filter1Tbl");
    $strHtmlFilter1Commnad = $tmpRetArray[1];
    //シンフォニー用----

    //----オペレーション用
    $aryTmpVariant2 = array();
    $aryTmpSetting2 = array();
    
    $op_list_dir = "2100000304";
    require_once($g['root_dir_path'] . "/webconfs/systems/2100000304_loadTable.php");
    $objTable2 = loadTable($op_list_dir,$aryTmpVariant2,$aryTmpSetting2);
    
    $tmpRetArray = getFilterCommandArea($objTable2,$aryTmpVariant2,$aryTmpSetting2,"filter_table","Filter2Tbl");
    $strHtmlFilter2Commnad = $tmpRetArray[1];
    //オペレーション用----
    
    $strCmdWordAreaOpen = $objMTS->getSomeMessage("ITAWDCH-STD-251");
    $strCmdWordAreaClose = $objMTS->getSomeMessage("ITAWDCH-STD-252");
    
    // javascript,css更新時自動で読込みなおす為にファイルのタイムスタンプをパラメーターに持つ
    $timeStamp_editor_conductor_style_css=filemtime("$root_dir_path/webroot/common/css/editor_conductor.css");
    $timeStamp_editor_conductor_js=filemtime("$root_dir_path/webroot/common/javascripts/editor_conductor.js");
    $timeStamp_00_javascript_js=filemtime("$root_dir_path/webroot/menus/systems/{$g['page_dir']}/00_javascript.js");
    $timeStamp_itabase_symphony_class_info_access_js=filemtime("$root_dir_path/webroot/common/javascripts/itabase_symphony_class_info_access.js");

print <<< EOD
    <script>const gLoginUserID = {$g['login_id']};</script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?client=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/default/menu/02_access.php?stub=all&no={$g['page_dir']}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/editor_conductor.js?{$timeStamp_editor_conductor_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/menus/systems/{$g['page_dir']}/00_javascript.js?{$timeStamp_00_javascript_js}"></script>
    <script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/itabase_symphony_class_info_access.js?{$timeStamp_itabase_symphony_class_info_access_js}"></script>

    <link rel="Stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/editor_conductor.css?{$timeStamp_editor_conductor_style_css}">
    <style>
      #KIZI { height: auto; padding: 24px 24px 48px; }
      #editor { height: 800px; }
      #KIZI .midashi_class.conductor_midashi:hover { cursor: default; font-weight: normal; }
    </style>
EOD;

    // browse系共通ロジックパーツ02
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_for_browse_02.php");
    
    $privilege = "";
    //$strJscriptTemplateBody = "";

    //----メッセージtmpl作成準備
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITAWDCC","STD","_js");
    $aryImportFilePath[] = $objMTS->getTemplateFilePath("ITABASEC","STD","_js");
    $strJscriptTemplateBody = getJscriptMessageTemplate($aryImportFilePath,$objMTS);
    //メッセージtmpl作成準備----
    
    $strDeveloperArea = "";
    
    $varWebRowConfirm = "";
    $varWebRowLimit = "";
    $intTableWidth = 1058;
    $intTableHeight = 600;
    
    $strPageInfo = $g['objMTS']->getSomeMessage("ITABASEH-MNU-309001");
    
    print 
<<< EOD
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
    <div id="privilege" style="display:none" class="text">{$privilege}</div>
    <div id="sysWebRowConfirm" style="display:none" class="text">{$varWebRowConfirm}</div>
    <div id="sysWebRowLimit" style="display:none" class="text">{$varWebRowLimit}</div>
    <div id="sysJSCmdText01" style="display:none" class="text">{$strCmdWordAreaOpen}</div>
    <div id="sysJSCmdText02" style="display:none" class="text">{$strCmdWordAreaClose}</div>
    <div id="webStdTableWidth" style="display:none" class="text">{$intTableWidth}</div>
    <div id="webStdTableHeight" style="display:none" class="text">{$intTableHeight}</div>
    <div id="messageTemplate" style="display:none" class="text">{$strJscriptTemplateBody}</div>
    <!-------------------------------- ユーザ・コンテンツ情報 -------------------------------->
{$strDeveloperArea}
EOD;

    print 
<<< EOD
    <!-------------------------------- 説明 -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td><div onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITAWDCH-STD-30011")}</div></td>
                <td>
                    <div id="SetsumeiMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('SetsumeiMidashi','SetsumeiNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="SetsumeiNakami" style="display:block" class="text">
        <div style="margin-left:10px">
{$strPageInfo}
        </div>
    </div>
    <!-------------------------------- 説明 -------------------------------->
EOD;

    print 
<<< EOD
    <!-------------------------------- スケジューリング -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >スケジューリング</div></td>-->
                <td><div onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-205060")}</div></td>
                <td>
                    <div id="BookingMidashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('BookingMidashi','BookingNakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="BookingNakami" style="display:block;" class="text">
        <div style="margin-left:10px">
            <!--RedMineチケット1031-->
            <!--
            予約日時を指定する場合は、日時フォーマット(YYYY/MM/DD HH:II)で入力して下さい。
            ブランクの場合は即時実行となります
            -->
            <!--RedMineチケット1031-->
            {$g['objMTS']->getSomeMessage("ITABASEH-MNU-205070")}
            <br>
            <table border="0">
                <tr>
                    <!--<td style="padding-right:10px">予約日時</td>-->
                    <td style="padding-right:10px">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-205080")}</td>
                    <td><input id="bookdatetime" type="text" maxlength="16"></td>
                </tr>
            </table>
        </div>
    </div>
    <!-------------------------------- スケジューリング -------------------------------->
EOD;

    print 
<<< EOD
    <!-------------------------------- 絞込み(表示フィルタ[シンフォニー]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >シンフォニー[フィルタ]</div></td>-->
                <td><div onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309002")}</div></td>
                <td>
                    <div id="Filter1_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Filter1_Midashi','Filter1_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Filter1_Nakami" style="display:block;" class="text">
        <div style="margin-left:0px">
            <div id="filter_alert_area" class="alert_area" style="display:none" ></div>
            <div id="filter_area" class="table_area">
            </div>
        </div>
        <div style="margin-left:10px">
{$strHtmlFilter1Commnad}
        </div>
    </div>
    <!-------------------------------- 絞込み(表示フィルタ[シンフォニー]) -------------------------------->
EOD;

	print
<<<EOD
    <!-------------------------------- 一覧[シンフォニー] -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <td>
                    <!--<div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >シンフォニー[一覧]</div>-->
                    <div onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309003")}</div>
                </td>
                <td>
                    <div id="Mix1_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Mix1_Midashi','Mix1_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Mix1_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="table_alert_area" class="alert_area" style="display:none" ></div>
            <div id="table_area" class="table_area" ></div>
        </div>
    </div>
    <!-------------------------------- 一覧[シンフォニー] -------------------------------->
EOD;

    print 
<<<EOD
    <!-------------------------------- 絞込み(表示フィルタ[オペレーション]) -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >オペレーション[フィルタ]</div></td>-->
                <td><div onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206020")}</div></td>
                <td>
                    <div id="Filter2_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Filter2_Midashi','Filter2_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Filter2_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="select_alert_area" class="alert_area" style="display:none" ></div>
            <div id="select_area" class="table_area" >
            </div>
        </div>
        <div style="margin-left:10px">
{$strHtmlFilter2Commnad}
        </div>
    </div>
    <!-------------------------------- 絞込み(表示フィルタ[オペレーション]) -------------------------------->
EOD;

    print 
<<<EOD
    <!-------------------------------- 一覧[オペレーション] -------------------------------->
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >オペレーション[一覧]</div></td>-->
                <td><div onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" class="midashi_class" >{$g['objMTS']->getSomeMessage("ITABASEH-MNU-206030")}</div></td>
                <td>
                    <div id="Mix2_Midashi" align="right">
                        <input type="button" value="{$strCmdWordAreaClose}" class="showbutton" onClick=location.href="javascript:show('Mix2_Midashi','Mix2_Nakami');" >
                    </div>
                </td>
            </tr>
        </table>
    </h2>
    <div id="Mix2_Nakami" style="display:block" class="text">
        <div style="margin-left:10px">
            <div id="register_alert_area" class="alert_area" style="display:none" ></div>
            <div id="register_area" class="table_area" ></div>
        </div>
    </div>
    <!-------------------------------- 一覧[オペレーション] -------------------------------->
EOD;

    print 
<<< EOD
    <h2>
        <table width="100%">
            <tr>
                <!--<td><div onClick=location.href="javascript:show('symphonyExecute_Midashi','symphonyExecute_Nakami');" class="midashi_class" >シンフォニー実行</div></td>-->
                <td><div class="midashi_class conductor_midashi">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309004")}</div></td>
            </tr>
        </table>
    </h2>
    <div id="symphonyExecute_Nakami" style="display:block" class="text">
    
    <!--================--> 
    <!--　　エディタ　　--> 
    <!--================-->
<div id="editor" class="load-wait" data-editor-mode="edit">
  <div class="editor-inner">



    <div id="editor-header">
      <div id="editor-mode"></div>
      <div class="editor-header-menu">
        <div class="editor-header-main-menu">
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
                  <dd class="explanation-description">{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309061")}</dd>
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
                        <tr>
                          <th class="panel-th">Name :</th>
                          <td class="panel-td" colspan="2"><span id="conductor-class-name-view" class="panel-span"></span></td>
                        </tr>
                        <tr>
                          <th class="panel-th">Notice :</th>
                          <td class="panel-td"><span id="conductor-notice-status" class="panel-span"></span></td>
                          <td class="panel-td panel-td-button"><button id="conductor-notice-select" class="panel-button">Select</button></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="conductor-class-note-view" class="panel-note panel-span"></span>
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
                          <th class="panel-th">Skip :</th>
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
                      <span id="movement-note" class="panel-note panel-span"></span>
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
                            <span id="end-status" class="panel-span"></span>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="end-note" class="panel-note panel-span"></span>
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
                      <span id="function-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Conditional-branch -->
                <div id="conditional-branch" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="branch-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Parallel-branch -->
                <div id="parallel-branch" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="marge-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Merge-->
                <div id="merge" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="merge-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- status-file-branch -->
                <div id="status-file-branch" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <div id="status-file-case-move">
                    <table id="status-file-case-list" class="panel-table ">
                      <tbody>
                      </tbody>
                    </table>
                    </div>
                    <div class="panel-group">
                      <div class="panel-group-title">Note</div>
                      <span id="status-file-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Conductor call -->
                <div id="call" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Skip :</th>
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
                      <span id="conductor-call-note" class="panel-note panel-span"></span>
                    </div>
                  </div>
                </div>
                
                <!-- Symphony call -->
                <div id="call_s" class="editor-tab-body">
                  <div class="editor-tab-body-inner">
                    <table class="panel-table">
                      <tbody>
                        <tr>
                          <th class="panel-th">Skip :</th>
                          <td class="panel-td"><input id="symphony-call-default-skip" class="panel-checkbox" type="checkbox"></td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="panel-group">
                      <div class="panel-group-title">Conductor select</div>
                      <table class="panel-table">
                        <tbody>
                          <tr>
                            <th class="panel-th">Conductor :</th>
                            <td class="panel-td"><span id="symphony-call-name" class="panel-span" data-id="" data-value=""></span></td>
                          </tr>
                        </tbody>
                      </table>
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
                      <span id="symphony-call-note" class="panel-note panel-span"></span>
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
          <ul class="editor-menu-list edit">
            <li class="editor-menu-item"><button class="editor-menu-button positive" data-menu="execute" disabled>{$g['objMTS']->getSomeMessage("ITABASEH-MNU-309024")}</button></li>
          </ul>
        </div>
        <div class="editor-footer-sub-menu"></div>
      </div>
    </div><!-- /#editor-footer -->



  </div><!-- /#editor -->
</div>
    </div>
EOD;

    //  共通HTMLフッタパーツ
    require_once ( $root_dir_path . "/libs/webcommonlibs/web_parts_html_footer.php");


?>
