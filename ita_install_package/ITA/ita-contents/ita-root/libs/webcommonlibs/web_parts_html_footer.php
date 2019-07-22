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

    $tmpStrFooterTitle = "";
    $tmpStrUlInner = "";
    $tmpHtmlOfFooterBody = "";

    if( $admin_addr == "" ){
        $tmpStrFooterTitle = "　";
    }
    else{
        $tmpStrFooterTitle = "{$objMTS->getSomeMessage("ITAWDCH-STD-601")}";
        $tmpStrUlInner = "<li id=\"FOOTER02\"><address><a href={$admin_addr}>{$objMTS->getSomeMessage("ITAWDCH-STD-602")}</a></address></li>";
    }

    $tmpHtmlOfFooterBody = 
<<< EOD

                    <hr>
                </div>
                <!--================-->
                <!--　　フッター　　-->
                <!--================-->
                <div id="FOOTER">
                    <h2>{$tmpStrFooterTitle}</h2>
                    <ul>{$tmpStrUlInner}</ul>
                </div>
            </div>
        </div>
    </body>
    </html>
EOD;
    print $tmpHtmlOfFooterBody;

    unset($tmpHtmlOfFooterBody);
    unset($tmpStrFooterTitle);
    unset($tmpStrUlInner);

    // アクセスログ出力(正常)
    web_log($objMTS->getSomeMessage("ITAWDCH-STD-603"));
?>
