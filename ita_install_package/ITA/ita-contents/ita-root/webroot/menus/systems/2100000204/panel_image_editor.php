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

// Editorに必要なファイルのタイムスタンプを取得
$timeStamp_common_editor_css = filemtime("$root_dir_path/webroot/common/css/common_editor.css");
$timeStamp_html2canvas = filemtime("$root_dir_path/webroot/menus/systems/2100000204/html2canvas.min.js");
$timeStamp_common_editor_js = filemtime("$root_dir_path/webroot/common/javascripts/common_editor.js");

// SVGの一覧を取得
$shape_dir_path = $root_dir_path . '/webroot/menus/systems/2100000204/shape';
$shape_list = glob( $shape_dir_path . '/*' );
$symbol_dir_path = $root_dir_path . '/webroot/menus/systems/2100000204/symbol';
$symbol_list = glob( $symbol_dir_path . '/*' );

print
<<< EOD

<link rel="stylesheet" type="text/css" href="{$scheme_n_authority}/common/css/common_editor.css?{$timeStamp_common_editor_css}">
<script type="text/javascript" src="{$scheme_n_authority}/menus/systems/2100000204/html2canvas.min.js?{$timeStamp_common_superTables_js}"></script>
<script type="text/javascript" src="{$scheme_n_authority}/common/javascripts/common_editor.js?{$timeStamp_common_superTables_js}"></script>

EOD;

?>

<!-- ==================================================================================================== ****
     Editor | Start
**** ====================================================================================================  -->
<div id="workspace" class="workspace" data-editor-type="panel-image">

<div class="workspace-header">
  <ul class="editor-menu">
    <li class="editor-menu-li"><button class="editor-menu-button" data-menu="save-icon-data">Save IPF</button></li>
    <li class="editor-menu-li"><button class="editor-menu-button" data-menu="load-icon-data">Read IPF</button></li>
    <li class="editor-menu-li"><button class="editor-menu-button" data-menu="output-icon-data">Output PNG</button></li>
  </ul>
  <ul class="editor-menu editor-sub-menu">
    <li class="editor-menu-li"><button class="editor-menu-button" data-menu="view-reset">View Reset</button></li>
    <li class="editor-menu-li"><button class="editor-menu-button" data-menu="full-screen">Full Screen</button></li>
  </ul>
</div><!-- / .workspace-header -->

<div class="workspace-body">

  <div class="document-window">
    <div class="canvas-window">
      <div class="canvas">
        <div id="art-board" class="art-board">

          <div class="art-board-border output-ignore"></div>

        </div><!-- / .art-board -->
      </div><!-- / .canvas -->
      
      <div class="canvas-status">
        <ul class="canvas-status-ul">
          <li class="canvas-status-li">
            <dl class="canvas-status-dl">
              <dt class="canvas-status-dt">View Scale : </dt>
              <dd class="canvas-status-dd" id="canvas-status-scale"></dd>
            </dl>
          </li>
          <li class="canvas-status-li">
            <dl class="canvas-status-dl">
              <dt class="canvas-status-dt">View X : </dt>
              <dd class="canvas-status-dd" id="canvas-status-view-x"></dd>
            </dl>
          </li>
          <li class="canvas-status-li">
            <dl class="canvas-status-dl">
              <dt class="canvas-status-dt">View Y : </dt>
              <dd class="canvas-status-dd" id="canvas-status-view-y"></dd>
            </dl>
          </li>
          <li class="canvas-status-li">
            <dl class="canvas-status-dl">
              <dt class="canvas-status-dt">Move X : </dt>
              <dd class="canvas-status-dd" id="canvas-status-move-x"></dd>
            </dl>
          </li>
          <li class="canvas-status-li">
            <dl class="canvas-status-dl">
              <dt class="canvas-status-dt">Move Y : </dt>
              <dd class="canvas-status-dd" id="canvas-status-move-y"></dd>
            </dl>
          </li>
        </ul>
      </div><!-- / .canvas-status -->
  
    </div><!-- / .canvas-window -->
  </div><!-- / .document-window -->

  <div id="panel-container" class="panel-container">

    <div class="panel-group">

      <ul class="panel-tab">
        <li class="panel-tab-li" data-tab-nanme="icon-layer">Layer</li>
        <li class="panel-tab-li" data-tab-nanme="icon-document">Document</li>
      </ul>

      <div id="icon-layer" class="panel icon-layer">

      <ul id="layer-menu" class="layer-menu">
        <li class="layer-menu-li"><button class="layer-menu-button" data-add-layer="text">Text</button></li>
        <li class="layer-menu-li"><button class="layer-menu-button" data-add-layer="symbol">Symbol</button></li>
        <li class="layer-menu-li"><button class="layer-menu-button" data-add-layer="shape">Shape</button></li>
        <li class="layer-menu-li"><button class="layer-menu-button" data-add-layer="image">Image</button></li>
      </ul>

      <ul id="layer-list" class="layer-list"></ul>

      </div>

      <div id="icon-document" class="panel layer-property">

        <div class="property-item">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Name:</th><td class="property-td" colspan="3"><input id="document-name" class="property-text" type="text"></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Width:</th><td class="property-td"><input id="document-width" class="property-number" type="number" value="400"><label for="document-width" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Height:</th><td class="property-td"><input id="document-height" class="property-number" type="number" value="400"><label for="document-height" class="property-input-label">px</label></td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>

    </div><!-- / .panel-group -->

    <div id="layer-property" class="panel-group">

      <ul class="panel-tab">
        <li class="panel-tab-li" data-tab-nanme="layer-property-common">Common</li>
        <li class="panel-tab-li property-type-text" data-tab-nanme="layer-property-ime">IME</li>
        <li class="panel-tab-li property-type-symbol" data-tab-nanme="layer-property-symbol">Symbol</li>
        <li class="panel-tab-li property-type-shape" data-tab-nanme="layer-property-shape">Shape</li>
        <li class="panel-tab-li property-type-not-image" data-tab-nanme="layer-property-border">Border</li>
        <li class="panel-tab-li" data-tab-nanme="layer-property-transform">Transform</li>
        <li class="panel-tab-li property-type-not-image" data-tab-nanme="layer-property-filter">Filter</li>
      </ul>

      <div id="layer-property-common" class="panel layer-property">
        <div class="property-item">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Name:</th><td class="property-td" colspan="3"><input id="property-layer-name" class="property-text" type="text"></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">X:</th><td class="property-td"><input id="property-x" class="property-number" type="number"><label for="property-x" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Y:</th><td class="property-td"><input id="property-y" class="property-number" type="number"><label for="property-y" class="property-input-label">px</label></td>
              </tr>
              <tr class="property-type-not-text">
                <th class="property-th" scope="col">Width:</th><td class="property-td"><input id="property-width" class="property-number" type="number"><label for="property-width" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Height:</th><td class="property-td"><input id="property-height" class="property-number" type="number"><label for="property-height" class="property-input-label">px</label></td>
              </tr>
              <tr class="property-type-not-image">
                <th class="property-th" scope="col">Color<span class="property-type-symbol"> 1</span>:</th><td class="property-td"><div class="property-color-set"><input id="property-color" class="property-color" type="color"><label for="property-color" class="property-color-label"></label></div></td>
                <th class="property-th" scope="col">Opacity<span class="property-type-symbol"> 1</span>:</th><td class="property-td"><input id="property-opacity" class="property-number" type="number"><label for="property-opacity" class="property-input-label">%</label></td>
              </tr>
              <tr class="property-type-symbol symbol-color-2">
                <th class="property-th" scope="col">Color 2:</th><td class="property-td"><div class="property-color-set"><input id="property-color2" class="property-color" type="color"><label for="property-color2" class="property-color-label"></label></div></td>
                <th class="property-th" scope="col">Opacity 2:</th><td class="property-td"><input id="property-opacity2" class="property-number" type="number"><label for="property-opacity2" class="property-input-label">%</label></td>
              </tr>
              <tr class="property-type-symbol symbol-color-3">
                <th class="property-th" scope="col">Color 3:</th><td class="property-td"><div class="property-color-set"><input id="property-color3" class="property-color" type="color"><label for="property-color3" class="property-color-label"></label></div></td>
                <th class="property-th" scope="col">Opacity 3:</th><td class="property-td"><input id="property-opacity3" class="property-number" type="number"><label for="property-opacity3" class="property-input-label">%</label></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="property-item property-type-text">
          <hr class="property-hr">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Text:</th><td class="property-td" colspan="3"><input id="property-text" class="property-text" type="text"></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Font:</th><td class="property-td" colspan="3"><input id="property-font" class="property-text" type="text"></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Size:</th><td class="property-td"><input id="property-font-size" class="property-number" type="number"><label for="property-font-size" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Weight:</th><td class="property-td">
                  <select id="property-font-weight" class="property-select">
                    <option value="normal">Normal</option>
                    <option value="bold">Bold</option>
                  </select></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="property-item property-type-image">
          <hr class="property-hr">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Image:</th>
                <td class="property-td"><button id="property-image-load" class="property-button">Select image</button></td>
                <td colspan="2"></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Size:</th><td class="property-td">
                  <select id="property-image-size" class="property-select">
                    <option value="auto">Auto</option>
                    <option value="full">Full</option>
                    <option value="contain">Contain</option>
                    <option value="cover">Cover</option>
                  </select></td>
                <th class="property-th" scope="col">Repeat:</th><td class="property-td">
                  <select id="property-image-repeat" class="property-select">
                    <option value="no-repeat">No Repeat</option>
                    <option value="repeat">Repeat</option>
                    <option value="repeat-x">Repeat X</option>
                    <option value="repeat-y">Repeat Y</option>
                  </select></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="layer-property-ime" class="panel layer-property">
        <div class="property-item property-type-text">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Text:</th><td class="property-td" colspan="3"><input id="property-text-ime" class="property-text" type="text"></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Type:</th>
                <td class="property-td" colspan="3">
                  <select id="property-ime-type" class="property-select">
                  </select>
                </td>
              </tr>
              <tr>
                <td colspan="4">
                  <div id="editor-ime" class="editor-ime"></div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="layer-property-border" class="panel layer-property">
        <div class="property-item property-type-not-image">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Width:</th><td class="property-td"><input id="property-border-width" class="property-number" type="number"><label for="property-border-width" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Join:</th><td class="property-td">
                  <select id="property-border-join" class="property-select">
                    <option value="miter">Miter</option>
                    <option value="round">Round</option>
                    <option value="bevel">Bevel</option>
                    </select></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Color:</th><td class="property-td"><div class="property-color-set"><input id="property-border-color" class="property-color" type="color"><label for="property-border-color" class="property-color-label"></label></div></td>
                <th class="property-th" scope="col">Opacity:</th><td class="property-td"><input id="property-border-opacity" class="property-number" type="number"><label for="property-border-opacity" class="property-input-label">%</label></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="layer-property-transform" class="panel layer-property">
        <div class="property-item">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Scale X:</th><td class="property-td"><input id="property-scale-x" class="property-number" type="number"><label for="property-scale-x" class="property-input-label">%</label></td>
                <th class="property-th" scope="col">Scale Y:</th><td class="property-td"><input id="property-scale-y" class="property-number" type="number"><label for="property-scale-y" class="property-input-label">%</label></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Rotate:</th><td class="property-td"><input id="property-rotate" class="property-number" type="number"><label for="property-rotate" class="property-input-label">deg</label></td>
                <th class="property-th" scope="col">Skew:</th><td class="property-td"><input id="property-skew" class="property-number" type="number"><label for="property-skew" class="property-input-label">deg</label></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="layer-property-filter" class="panel layer-property">
        <div class="property-item property-type-not-image">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Blur:</th><td class="property-td"><input id="property-filter-blur" class="property-number" type="number"><label for="property-filter-blur" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Opacity:</th><td class="property-td"><input id="property-filter-opacity" class="property-number" type="number"><label for="property-filter-opacity" class="property-input-label">%</label></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Brightness:</th><td class="property-td"><input id="property-filter-brightness" class="property-number" type="number"><label for="property-filter-brightness" class="property-input-label">%</label></td>
                <th class="property-th" scope="col">Contrast:</th><td class="property-td"><input id="property-filter-contrast" class="property-number" type="number"><label for="property-filter-contrast" class="property-input-label">%</label></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Grayscale:</th><td class="property-td"><input id="property-filter-grayscale" class="property-number" type="number"><label for="property-filter-grayscale" class="property-input-label">%</label></td>
                <th class="property-th" scope="col">Sepia:</th><td class="property-td"><input id="property-filter-sepia" class="property-number" type="number"><label for="property-filter-sepia" class="property-input-label">%</label></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">Saturate:</th><td class="property-td"><input id="property-filter-saturate" class="property-number" type="number"><label for="property-filter-saturate" class="property-input-label">%</label></td>
                <th class="property-th" scope="col">Invert:</th><td class="property-td"><input id="property-filter-invert" class="property-number" type="number"><label for="property-filter-invert" class="property-input-label">%</label></td>          
              </tr>
              <tr>
                <th class="property-th" scope="col">Hue-rotate:</th><td class="property-td"><input id="property-filter-hue" class="property-number" type="number"><label for="property-filter-hue" class="property-input-label">deg</label></td>
                <td colspan="2"></td>
              </tr>
            </tbody>
          </table>
          <hr class="property-hr">
          <table class="property-table" aria-describedby="">
            <tbody>
              <tr>
                <th class="property-th" scope="col">Shadow:</th><td class="property-td"><div class="property-color-set"><input id="property-filter-shadow-color" class="property-color" type="color"><label for="property-filter-shadow-color" class="property-color-label"></label></div></td>
                <th class="property-th" scope="col">Blur:</th><td class="property-td"><input id="property-filter-shadow-blur" class="property-number" type="number"><label for="property-filter-shadow-blur" class="property-input-label">px</label></td>
              </tr>
              <tr>
                <th class="property-th" scope="col">X:</th><td class="property-td"><input id="property-filter-shadow-x" class="property-number" type="number"><label for="property-filter-shadow-x" class="property-input-label">px</label></td>
                <th class="property-th" scope="col">Y:</th><td class="property-td"><input id="property-filter-shadow-y" class="property-number" type="number"><label for="property-filter-shadow-y" class="property-input-label">px</label></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div id="layer-property-symbol" class="panel layer-property">
        <div class="property-item property-type-symbol">
          <ul id="editor-symbol-list" class="editor-symbol-list">
<?php
$symbol_list_html = '';

ob_start();
  // Symbolの一覧を作成
  foreach( $symbol_list as $symbol ){
    echo '<li class="editor-symbol-list-li">';
    require_once ( $symbol );
    echo '</li>';
  }

  $symbol_list_html = ob_get_contents();
ob_end_clean();

echo $symbol_list_html;
?>
          </ul>
        </div>
      </div>
      
      <div id="layer-property-shape" class="panel layer-property">
        <div class="property-item property-type-shape">
          <ul id="editor-shape-list" class="editor-shape-list">
<?php
$shape_list_html = '';

ob_start();
  // Shapeの一覧を作成
  foreach( $shape_list as $shape ){
    echo '<li class="editor-shape-list-li">';
    require_once ( $shape );
    echo '</li>';
  }

  $shape_list_html = ob_get_contents();
ob_end_clean();

echo $shape_list_html;
?>
          </ul>
        </div>
      </div>

    </div><!-- / .panel-group -->

  </div><!-- / .panel-container -->

</div><!-- / .workspace-body -->

<div id="image-output" class="image-output">

  <div class="image-output-body">
  
    <div class="image-output-main">
      <div id="image-canvas" class="image-canvas"></div>
    </div><!-- / .image-output-main -->

    <div class="image-output-sub">
    
      <div class="image-output-name"></div>
    
      <ul class="image-output-menu">
        <li class="image-output-menu-li"><a href="#" class="image-output-button download-png">Download PNG</a></li>
        <li class="image-output-menu-li"><button class="image-output-button cancel">Cancel</button></li>
      </ul>
    </div><!-- / .image-output-sub -->
    
  </div><!-- / .image-output-body -->

</div><!-- / .image-output -->

<div class="editor-hidden">
  <div class="load-icon-data"><input id="load-icon-data-input" type="file" accept=".ipf"></div>
  <div class="load-image"><input id="load-image-input" type="file" accept="image/*"></div>
</div><!-- / .editor-hidden -->

<div class="editor-not-available">
  <p>Not available for current browser.</p>
</div><!-- / .editor-not-available -->

</div><!-- / .workspace -->
<!-- ==================================================================================================== ****
     Editor | End
**** ====================================================================================================  -->