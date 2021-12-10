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

$getCopy = $_GET;
unset($getCopy['login']);
$get_parameter = "";
if("" != http_build_query($getCopy)){
    $get_parameter = "?" . http_build_query($getCopy);
}
$get_parameter = str_replace('+', '%20', $get_parameter);

if(array_key_exists("no", $getCopy)){
    $ASJTM_representative_file_name = "/default/menu/01_browse.php{$get_parameter}";
}
else{
    $ASJTM_representative_file_name = "/default/mainmenu/01_browse.php{$get_parameter}";
}

?>