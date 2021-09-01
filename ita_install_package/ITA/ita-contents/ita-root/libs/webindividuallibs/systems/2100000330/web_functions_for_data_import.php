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
/**
 * データポータビリティ用関数群
 * 
 */

/**
 * zipファイルをアップロードする
 */
function uploadZipFile(){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    $fileName = $uploadId . '_ita_data.zip';
    $uploadFilePath = $g['root_dir_path'] . '/temp/bulk_excel/import/upload/' . $fileName;
    $uploadPath = $g['root_dir_path'] . '/temp/bulk_excel/import/upload/';
    $importPath = $g['root_dir_path'] . '/temp/bulk_excel/import/import/';

    if (strlen($_FILES['zipfile']['tmp_name']) === 0) {
        // ファイルを指定していないためエラー
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900011'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    $uploadRes = $_FILES['zipfile']['error'];
    switch ($uploadRes) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            // サイズを超えているためエラー
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900010'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_PARTIAL:
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900022'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_NO_FILE:
            // アップロードされなかった
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900009'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_NO_TMP_DIR:
            // テンポラリフォルダがない
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900023'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        case UPLOAD_ERR_CANT_WRITE:
            // 書き込み権限なし
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900007'));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
        default:
            removeFiles($uploadPath);
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900006',
                                                 array(basename(__FILE__), __LINE__)));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // HTTP経由でアップロードされたかを確認
    if (is_uploaded_file($_FILES['zipfile']['tmp_name']) === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900004'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // zipファイルであることを確認
    $objFinfo = new finfo(FILEINFO_MIME_TYPE);
    $ext = array_search($objFinfo->file($_FILES['zipfile']['tmp_name']),
                                        array('zip' => 'application/zip'));
    if ($ext === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_10'));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // ファイル名確認
    if(preg_match("/^[^,\"'\t\/\r\n]*$/s", $_FILES['zipfile']['name']) !== 1){
        web_log("The file name[" . $_FILES['zipfile']['name'] . "] is invalid.");
        throw new Exception($g['objMTS']->getSomeMessage('ITAWDCH-ERR-513'));
    }

    // ファイル移動
    if (move_uploaded_file($_FILES['zipfile']['tmp_name'], $uploadFilePath) === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900019',
                                             array(basename(__FILE__), __LINE__)));
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    $_SESSION['data_portability_upload_file_name'] = $_FILES['zipfile']['name'];

    return;
}

/**
 * zipファイルの中身を確認する
 */
function checkZipFile(){
    global $g;
    $uploadPath = $g['root_dir_path'] . '/temp/bulk_excel/import/upload/';
    $importPath = $g['root_dir_path'] . '/temp/bulk_excel/import/import/';
    $uploadId = $_SESSION['upload_id'];
    $fileName = $uploadId . '_ita_data.zip';

    // zip中身確認
    $fileAry = scandir($uploadPath . $uploadId);
    $fileAry = array_diff($fileAry, array('.', '..'));
    if (count($fileAry) === 0) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_8',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // 必須ファイルの確認
    $needleAry = array('MENU_LIST.txt');
    $errCnt = 0;
    foreach ($needleAry as $value) {
        $res = in_array($value, $fileAry);
        if ($res === false) {
            $errCnt++;
        }
    }
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_14',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_14'));
    }
    $tmp_menu_list = file_get_contents($uploadPath . $uploadId."/MENU_LIST.txt");
    if ($tmp_menu_list == "") {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15'));
    }

    if ($errCnt > 0) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_8',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    // ファイル移動
    $res = copy($uploadPath . $fileName, $importPath . $fileName);
    if ($res === false) {
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        if (file_exists($importPath . $fileName) === true) {
            unlink($importPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        removeFiles($importPath . $uploadId);

        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900039',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    mkdir($importPath . $uploadId);
    $from = $uploadPath . $uploadId;
    $to   = $importPath . '.';
    $cmd = "sudo cp -frp $from $to";
    exec($cmd);
    $errCnt = 0;

    $declare_check_list = array();
    foreach ($fileAry as $file) {
        $filePath = $importPath . $uploadId . '/' . $file;
        $res = file_exists($filePath);
        if ($res === false) {
            $errCnt++;
            break;
        }
        $files = glob("$filePath/*");
        foreach ($files as $file) {
            if ($file != "") {
                $tmpFileAry = explode("/", $file);
                $fileName = $tmpFileAry[count($tmpFileAry) - 1];
                $declare_check_list[] = $fileName;
            }
        }
    }
    $declare_list = array_count_values($declare_check_list);

    foreach ($fileAry as $file) {
        $filePath = $importPath . $uploadId . '/' . $file;

        $res = file_exists($filePath);
        if ($res === false) {
            $errCnt++;
            break;
        }
        // ディレクトリの中身を展開
        // 中身取得
        $files = glob("$filePath/*");
        foreach ($files as $file) {
            // ファイル名にカッコがある可能性があるため、ファイル名のみシングルクォーテーションで囲む

            $cmd = "sudo mv '$file' '$importPath$uploadId'";
            exec($cmd, $output, $return_var);

            if(0 != $return_var){
                return false;
            }
        }
        if (!empty($files)) {
            $cmd = "rm -rf $filePath";
            exec($cmd);
            if(0 != $return_var){
                return false;
            }
        }
    }


    if ($errCnt > 0) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900019',
                                             array(basename(__FILE__), __LINE__)));
        if (file_exists($uploadPath . $fileName) === true) {
            unlink($uploadPath . $fileName);
        }
        if (file_exists($importPath . $fileName) === true) {
            unlink($importPath . $fileName);
        }
        removeFiles($uploadPath . $uploadId);
        removeFiles($importPath . $uploadId);
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    }

    removeFiles($uploadPath . $uploadId);

    return $declare_list;
}

/**
 * インポートするメニューのチェックボックス作成
 *
 * @return   array     $retImportAry    インポートするメニューのチェックボックス一覧
 */
function makeImportCheckbox($declare_list){
    global $g;
    $path = $g['root_dir_path'] . '/temp/bulk_excel/import/import/';

    require_once $g['root_dir_path']."/libs/backyardlibs/common/common_functions.php";

    if (isset($_SESSION['upload_id']) === false) {
        return;
    }

    $uploadId = $_SESSION['upload_id'];
    $retImportAry = array();

    // 取得したいFILEリストの取得
    $menuIdFile = file_get_contents($path . $uploadId .'/MENU_LIST.txt');

    $tmpMenuIdFileAry = explode("\n", $menuIdFile);
    if (empty($tmpMenuIdFileAry)) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15'));
    }

    $retImportAry = array();
    foreach ($tmpMenuIdFileAry as $menuIdFileInfo) {
        // フォーマットチェック
        if (!preg_match("/^#/",$menuIdFileInfo) && preg_match("/^[0-9]{10}:.*$/", $menuIdFileInfo)) {
            $menuIdFileInfo = explode(":", $menuIdFileInfo);
            $menuId         = $menuIdFileInfo[0];
            $menuFileName   = $menuIdFileInfo[1];
            $menuInfo       = getMenuInfoByMenuId($menuId);

            $menuGroupId    = $menuInfo["MENU_GROUP_ID"];
            $menuGroupName  = $menuInfo["MENU_GROUP_NAME"];
            $menuName       = $menuInfo["MENU_NAME"];

            if (empty($retImportAry) || $menuInfo == false) {
                $declare_key = false;
                $declare_file_name_key = false;
            }
            else {
                if (array_key_exists($menuGroupId, $retImportAry)) {
                    $declare_key = array_search($menuId, array_column($retImportAry[$menuGroupId]["menu"], "menu_id"));
                    $declare_file_name_key = array_search($menuFileName, array_column($retImportAry[$menuGroupId]["menu"], "file_name"));
                    $declare_menu_info = $retImportAry[$menuGroupId]["menu"][$declare_file_name_key];
                } else {
                    $declare_key = false;
                    $declare_file_name_key = false;
                }

            }

            // メニューの存在チェック
            if ($menuInfo == false) {
                $tmpMenuInfo = array(
                    "menu_id"   => $menuId,
                    "menu_name" => $menuName,
                    "disabled"  => true,
                    "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_2'),
                    "file_name" => $menuFileName
                );
                if (array_key_exists($menuGroupId, $retImportAry)) {
                    // メニューグループは存在するがメニューがない場合
                    if ($declare_key === false) {
                        $retImportAry[$menuGroupId]["menu"][] = $tmpMenuInfo;
                    }
                } else {
                    $retImportAry[$menuGroupId] = array(
                        "menu_group_name" => $menuGroupName,
                        "menu"            => array(
                            $tmpMenuInfo
                        )
                    );
                }
            }
            // 権限チェック
            elseif (!canMaintenance($menuId)) {
                $tmpMenuInfo = array(
                    "menu_id"   => $menuId,
                    "menu_name" => $menuName,
                    "disabled"  => true,
                    "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_1'),
                    "file_name" => $menuFileName
                );
                if (array_key_exists($menuGroupId, $retImportAry)) {
                    // メニューグループは存在するがメニューがない場合
                    if ($declare_key === false) {
                        $retImportAry[$menuGroupId]["menu"][] = $tmpMenuInfo;
                    }
                } else {
                    $retImportAry[$menuGroupId] = array(
                        "menu_group_name" => $menuGroupName,
                        "menu"            => array(
                            $tmpMenuInfo
                        )
                    );
                }
            }
            // ファイルの拡張子チェック
            elseif (getExtension($menuFileName) != "scsv" && getExtension($menuFileName) != "xlsx" && $menuFileName != "") {
                $tmpMenuInfo = array(
                    "menu_id"   => $menuId,
                    "menu_name" => $menuName,
                    "disabled"  => true,
                    "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_3'),
                    "file_name" => $menuFileName
                );
                if (array_key_exists($menuGroupId, $retImportAry)) {
                    // メニューグループは存在するがメニューがない場合
                    if ($declare_key === false) {
                        $retImportAry[$menuGroupId]["menu"][] = $tmpMenuInfo;
                    }
                } else {
                    $retImportAry[$menuGroupId] = array(
                        "menu_group_name" => $menuGroupName,
                        "menu"            => array(
                            $tmpMenuInfo
                        )
                    );
                }
            }
            else {
                // ファイルの有無
                if (file_exists($path.$uploadId."/".$menuFileName) && !empty($menuFileName)) {
                    // $retImportAryのなかに該当メニューグループがあるかどうか
                    if (array_key_exists($menuGroupId, $retImportAry)) {
                        // メニューグループは存在するがメニューがない場合
                        // 同名ファイルが複数あった場合
                        if ($declare_list[$menuFileName] > 1) {
                            $tmpMenuInfo = array(
                                "menu_id"   => $menuId,
                                "menu_name" => $menuName,
                                "disabled"  => true,
                                "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_12'),
                                "file_name" => $menuFileName
                            );
                            
                            $declare_menu_info["disabled"] = true;
                            $declare_menu_info["error"] = $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_12');

                            $retImportAry[$menuGroupId]["menu"][] = $tmpMenuInfo;
                            $retImportAry[$menuGroupId]["menu"][$declare_file_name_key] = $declare_menu_info;
                        }
                        elseif ($declare_key !== false) {
                            $tmpMenuInfo = array(
                                "menu_id"   => $menuId,
                                "menu_name" => $menuName,
                                "disabled"  => true,
                                "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_11'),
                                "file_name" => $menuFileName
                            );
                            $declare_menu_info["disabled"] = true;
                            $declare_menu_info["error"] = $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_11');

                            $retImportAry[$menuGroupId]["menu"][] = $tmpMenuInfo;
                            $retImportAry[$menuGroupId]["menu"][$declare_file_name_key] = $declare_menu_info;
                        }
                        else {
                            $retImportAry[$menuGroupId]["menu"][] = array(
                                "menu_id"   => $menuId,
                                "menu_name" => $menuName,
                                "disabled"  => false,
                                "file_name" => $menuFileName
                            );
                        }
                    } else {
                        if ($declare_list[$menuFileName] > 1) {
                            $retImportAry[$menuGroupId] = array(
                                "menu_group_name" => $menuGroupName,
                                "menu"            => array(
                                    array(
                                        "menu_id"   => $menuId,
                                        "menu_name" => $menuName,
                                        "disabled"  => true,
                                        "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_12'),
                                        "file_name" => $menuFileName
                                    )
                                )
                            );
                        } else {
                            $retImportAry[$menuGroupId] = array(
                                "menu_group_name" => $menuGroupName,
                                "menu"            => array(
                                    array(
                                        "menu_id"   => $menuId,
                                        "menu_name" => $menuName,
                                        "disabled"  => false,
                                        "file_name" => $menuFileName
                                    )
                                )
                            );
                        }
                    }
                } else {
                    $tmpMenuInfo = array(
                        "menu_id"   => $menuId,
                        "menu_name" => $menuName,
                        "disabled"  => true,
                        "error"     => $g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_4'),
                        "file_name" => $menuFileName
                    );
                    if (array_key_exists($menuGroupId, $retImportAry)) {
                        // メニューグループは存在するがメニューがない場合
                        if ($declare_key === false) {
                            $retImportAry[$menuGroupId]["menu"][] = $tmpMenuInfo;
                        }
                    } else {
                        $retImportAry[$menuGroupId] = array(
                            "menu_group_name" => $menuGroupName,
                            "menu"            => array(
                                $tmpMenuInfo
                            )
                        );
                    }
                }
            }
        }
    }

    if (empty($retImportAry)) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15'));
    }

    return $retImportAry;
}

/**
 * インポートするメニューのチェックボックス作成
 *
 * @return   array     $retImportAry    インポートするメニューのチェックボックス一覧
 */
function getMenuIdFileList(){
    global $g;
    $path = $g['root_dir_path'] . '/temp/bulk_excel/import/import/';

    require_once $g['root_dir_path']."/libs/backyardlibs/common/common_functions.php";

    if (isset($_SESSION['upload_id']) === false) {
        return;
    }

    $uploadId = $_SESSION['upload_id'];
    $retImportAry = array();

    // 取得したいFILEリストの取得
    $menuIdFile = file_get_contents($path . $uploadId .'/MENU_LIST.txt');

    $tmpMenuIdFileAry = explode("\n", $menuIdFile);

    if (empty($tmpMenuIdFileAry)) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15'));
    }
    
    $retImportAry = array();
    foreach ($tmpMenuIdFileAry as $menuIdFileInfo) {
        // 頭に#がついているものはコメントなのではじく
        if (!preg_match("/^#/",$menuIdFileInfo)) {
            if (!empty($menuIdFileInfo)) {
                $menuIdFileInfo = explode(":", $menuIdFileInfo);
                if (count($menuIdFileInfo) == 2) {
                    $menuId         = $menuIdFileInfo[0];
                    $menuFileName   = $menuIdFileInfo[1];
                    $retImportAry[$menuId] = $menuFileName;
                }
            }
        }
    }
    if (empty($retImportAry)) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_15'));
    }

    return $retImportAry;
}

/**
 * インポート用zipファイルを解凍する
 */
function unzipImportData(){
    global $g;
    $uploadId = $_SESSION['upload_id'];
    // $fileName = $uploadId . '_ita_data.zip';
    $fileName = $uploadId . '_ita_data.zip';
    $uploadPath = $g['root_dir_path'] . '/temp/bulk_excel/import/upload/';
    $res = file_exists($uploadPath . $fileName);

    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_9',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900003'));
    } else {

        mkdir($uploadPath . $uploadId);

        // zipを展開する
        $output = NULL;
        $cmd = "sudo LC_ALL=ja_JP.UTF-8 unzip -O sjis '" . $uploadPath . $fileName . "' -d '" . $uploadPath . $uploadId . "' 2>&1";
        exec($cmd, $output, $return_var);

        if(0 != $return_var){
            web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
        }
    }
    return;
}

/**
 * インポートするメニューIDのリストを作成する
 */
function makeImportMenuIdList(){
    global $g;
    $tmpMenuIdAry = $_POST;
    unset($tmpMenuIdAry['post_kind']);
    unset($tmpMenuIdAry['menu_on']);
    unset($tmpMenuIdAry['importButton']);
    unset($tmpMenuIdAry['importButton2']);
    $menuIdAry = array();
    $res = array();
    foreach ($tmpMenuIdAry as $key => $v1) {
        $key = str_replace('import_', '', $key);
        foreach ($v1 as $v2) {
            $menuIdAry[] = sprintf("%010d", $v2);
        }
    }

    $filePath = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $_SESSION['upload_id'] . '/IMPORT_MENU_ID_LIST';

    $allMenuIdFileList =  getMenuIdFileList();

    foreach ($menuIdAry as $menuId) {
        if (array_key_exists($menuId, $allMenuIdFileList)) {
            $res[$menuId] = $allMenuIdFileList[$menuId];
        }
    }

    if (empty($res)) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_16',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_16'));
    }

    $json = json_encode($res);
    $res = file_put_contents($filePath , $json);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900060',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_7'));
    }

    return;
}

/**
 * 入力値チェック
 */
function checkInputFormat(){
    global $g;
    $errFlg = 0;
    if (strlen($_POST['menu_on']) === 0 || ctype_alnum($_POST['menu_on']) === false) {
        $errFlg = 1;
    }

    $requestAry = $_POST;
    unset($requestAry['post_kind']);
    unset($requestAry['menu_on']);
    unset($requestAry['importButton']);
    unset($requestAry['importButton2']);
    foreach ($requestAry as $menuGroupId =>$menuIds) {
        $menuGroupId = str_replace('import_', '', $menuGroupId);
        if (ctype_digit($menuGroupId) === false || strlen($menuGroupId) > MENU_ID_LENGTH) {
            $errFlg = 1;
        }
        foreach ($menuIds as $menuId) {
            if (ctype_digit($menuId) === false || strlen($menuId) > MENU_ID_LENGTH) {
                $errFlg = 1;
            }
        }
    }

    if ($errFlg === 1) {
        // 不正なアクセス
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-MNU-1140002'));
        webRequestForceQuitFromEveryWhere(400, 10310201);
    }

    return;
}

/**
 * zipファイルをuploadfilesに移動する
 */
function moveZipFile($taskNo){
    global $g;

    $uploadId = $_SESSION['upload_id'];
    $dpDir = $g['root_dir_path'].'/uploadfiles/2100000331';

    if (file_exists($dpDir) === false) {
        $res = mkdir($dpDir, 0777, true);
        if ($res === false) {
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_5',
                                                 array(basename(__FILE__), __LINE__)));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_7'));
        }
    } elseif (substr(sprintf('%o', fileperms($dpDir)), -4) != 0777) {
        $cmd = "sudo chmod 777 $dpDir";
        exec($cmd, $output, $return_var);
        if(0 != $return_var){
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_5',
                                                 array(basename(__FILE__), __LINE__)));
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_7'));
        }
    }

    $src = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $uploadId . '_ita_data.zip';
    $dst = $dpDir . '/' . $taskNo . '_' . $_SESSION['data_portability_upload_file_name'];
    $res = copy($src, $dst);
    if ($res === false) {
        web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_6',
                                             array(basename(__FILE__), __LINE__)));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_7'));
    }

    unset($_SESSION['data_portability_upload_file_name']);
}

/**
* タスクの登録
* 
* @return string    $taskId    タスクNo
*/
function insertBulkExcelTask(){
    global $g, $objDBCA, $objMTS;

    $errMsg = ""; // エラーメッセージ

    // タスクの登録
    $sql = "INSERT INTO
                B_BULK_EXCEL_TASK(
                    TASK_ID,
                    TASK_STATUS,
                    TASK_TYPE,
                    ABOLISHED_TYPE,
                    FILE_NAME,
                    EXECUTE_USER,
                    DISP_SEQ,
                    NOTE,
                    ACCESS_AUTH,
                    DISUSE_FLAG,
                    LAST_UPDATE_USER,
                    LAST_UPDATE_TIMESTAMP
                )
            VALUES
                (
                    :TASK_ID,
                    :TASK_STATUS,
                    :TASK_TYPE,
                    :ABOLISHED_TYPE,
                    :FILE_NAME,
                    :EXECUTE_USER,
                    :DISP_SEQ,
                    :NOTE,
                    :ACCESS_AUTH,
                    :DISUSE_FLAG,
                    :LAST_UPDATE_USER,
                    :LAST_UPDATE_TIMESTAMP
                )";
    
    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $taskId = getSequenceID("B_BULK_EXCEL_TASK_RIC");
    if ($taskId == false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $filePath = $taskId . '_' . $_SESSION['data_portability_upload_file_name'];

    $res = $objQuery->sqlBind(
        array(
            "TASK_ID"               => $taskId,
            "TASK_STATUS"           => 1,
            "TASK_TYPE"             => 2,
            "ABOLISHED_TYPE"        => NULL,
            "FILE_NAME"             => $filePath,
            "EXECUTE_USER"          => $g["login_id"],
            "DISP_SEQ"              => NULL,
            "NOTE"                  => "",
            "ACCESS_AUTH"           => "",
            "DISUSE_FLAG"           => 0,
            "LAST_UPDATE_USER"      => $g["login_id"],
            "LAST_UPDATE_TIMESTAMP" => date('Y-m-d H:i:s')
        )
    );
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    // シーケンス番号の更新
    $res = updateSequenceID("B_BULK_EXCEL_TASK_RIC");
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    // タスクの履歴の登録
    // 登録したタスク情報の取得
    $taskDetail = getTaskDetail($taskId);

    if ($taskDetail == false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    } else {
        $taskDetail = $taskDetail[0];
    }

    // シーケンス番号の取得
    $jnlTaskId = getSequenceID("B_BULK_EXCEL_TASK_JSQ");

    $sql = "INSERT INTO
                B_BULK_EXCEL_TASK_JNL(
                    JOURNAL_SEQ_NO,
                    JOURNAL_ACTION_CLASS,
                    JOURNAL_REG_DATETIME,
                    TASK_ID,
                    TASK_STATUS,
                    TASK_TYPE,
                    ABOLISHED_TYPE,
                    FILE_NAME,
                    EXECUTE_USER,
                    DISP_SEQ,
                    NOTE,
                    DISUSE_FLAG,
                    LAST_UPDATE_TIMESTAMP,
                    LAST_UPDATE_USER
                )
            VALUES
                (
                    :JOURNAL_SEQ_NO,
                    :JOURNAL_ACTION_CLASS,
                    :JOURNAL_REG_DATETIME,
                    :TASK_ID,
                    :TASK_STATUS,
                    :TASK_TYPE,
                    :ABOLISHED_TYPE,
                    :FILE_NAME,
                    :EXECUTE_USER,
                    :DISP_SEQ,
                    :NOTE,
                    :DISUSE_FLAG,
                    :LAST_UPDATE_TIMESTAMP,
                    :LAST_UPDATE_USER
                )";
    
    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    $res = $objQuery->sqlBind(
        array(
            'JOURNAL_SEQ_NO'        => $jnlTaskId,
            'JOURNAL_ACTION_CLASS'  => "INSERT",
            'JOURNAL_REG_DATETIME'  => date("Y-m-d H:i:s"),
            'TASK_ID'               => $taskDetail["TASK_ID"],
            'TASK_TYPE'             => $taskDetail["TASK_TYPE"],
            'TASK_STATUS'           => $taskDetail["TASK_STATUS"],
            'ABOLISHED_TYPE'        => $taskDetail["ABOLISHED_TYPE"],
            'FILE_NAME'             => $taskDetail["FILE_NAME"],
            'EXECUTE_USER'          => $taskDetail["EXECUTE_USER"],
            'DISP_SEQ'              => $taskDetail["DISP_SEQ"],
            'NOTE'                  => $taskDetail["NOTE"],
            'DISUSE_FLAG'           => $taskDetail["DISUSE_FLAG"],
            'LAST_UPDATE_TIMESTAMP' => $taskDetail["LAST_UPDATE_TIMESTAMP"],
            'LAST_UPDATE_USER'      => $taskDetail["LAST_UPDATE_USER"]
        )
    );
    $res = $objQuery->sqlExecute();

    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    // シーケンス番号の更新
    $res = updateSequenceID("B_BULK_EXCEL_TASK_JSQ");
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    return $taskId;
}

/**
* タスクの取得
* 
* @param  int     $taskId    タスクID
* @return boolean            実行結果
*/
function getTaskDetail($taskId) {
    global $objDBCA, $objMTS;

    $result = array();

    $sql = "SELECT
                *
            FROM
                B_BULK_EXCEL_TASK
            WHERE
                TASK_ID = :TASK_ID
            AND
                DISUSE_FLAG = 0";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlBind(
        array(
            "TASK_ID" => $taskId
        )
    );
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        $result[] = $row;
    }

    return $result;
}

/**
 * 指定したディレクトリ内のディレクトリとファイル一覧を取得する
 */
function getDirFileList($dir) {
    $retAry = array();
    $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir,
                        FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST);
                    
    foreach ($iterator as $fileinfo) {
        $retAry[] = $fileinfo->getPathname();
    }

    return $retAry;
}

/**
 * 再帰的にディレクトリとファイルをコピーする
 */
function recursiveCopyFiles($srcPath, $dstPath){
    global $g;

    if(!is_dir($dstPath)){
        $res = mkdir($dstPath);
        if ($res === false) {
            web_log($g['objMTS']->getSomeMessage('ITABASEH-ERR-900039',
                                                 array(basename(__FILE__), __LINE__)));
            restoreTable();
            throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-2100000330_7'));
        }
    }

    $output = NULL;
    $cmd = "sudo cp -rp " . $srcPath . "/* " . $dstPath . "/. 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }
    return;
}

/**
 * 指定したディレクトリ内を再帰的に削除する
 */
function removeFiles($path){
    global $g;

    $output = NULL;
    $cmd = "sudo rm -rf $path 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($cmd);
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }

    return;
}

/**
 * ディテクトリを再帰的にコピーする
 *
 * @param    int    $taskNo
 */
function renameImportFiles($taskNo){
    global $g;
    $src = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $_SESSION['upload_id'];
    $dst = $g['root_dir_path'] . '/temp/bulk_excel/import/import/' . $taskNo;

    $output = NULL;
    $cmd = "sudo cp -frp $src $dst 2>&1";

    exec($cmd, $output, $return_var);

    if(0 != $return_var){
        web_log($g['objMTS']->getSomeMessage('ITAWDCH-ERR-2001', array(print_r($output, true))));
        throw new Exception($g['objMTS']->getSomeMessage('ITABASEH-ERR-900001'));
    }
}

/**
 * インポート可能かの権限チェック
 *
 * @param    int    $menuId
 * @return   boolean
 */
function canMaintenance($menuId) {
    global $g, $objMTS, $objDBCA;

    $result = false;
    $userId = $g["login_id"];

    $sql = "
        SELECT
            PRIVILEGE
        FROM
            A_ROLE_MENU_LINK_LIST
        LEFT OUTER JOIN
            A_ROLE_ACCOUNT_LINK_LIST
        ON
            A_ROLE_MENU_LINK_LIST.ROLE_ID = A_ROLE_ACCOUNT_LINK_LIST.ROLE_ID
        WHERE
            USER_ID = :USER_ID
        AND
            MENU_ID = :MENU_ID
        AND
            A_ROLE_MENU_LINK_LIST.DISUSE_FLAG = 0
        AND
            A_ROLE_ACCOUNT_LINK_LIST.DISUSE_FLAG = 0
    ";

    $objQuery = $objDBCA->sqlPrepare($sql);
    if ($objQuery->getStatus() === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }
    $res = $objQuery->sqlBind(
        array(
            "USER_ID" => $userId,
            "MENU_ID" => $menuId
        )
    );
    $res = $objQuery->sqlExecute();
    if ($res === false) {
        web_log($objMTS->getSomeMessage('ITABASEH-ERR-900054',
                                             array(__FILE__, __LINE__)));
        web_log($sql);
        web_log($objQuery->getLastError());
        throw new Exception($objMTS->getSomeMessage('ITABASEH-ERR-900066'));
    }

    while ($row = $objQuery->resultFetch()){
        if ($row["PRIVILEGE"] == 1) {
            $result = true;
        }
    }

    return $result;
}

/**
* 拡張子の取得
* 
* @param  int      $fileName       ファイル名
* @return boolean  $extension      拡張子
*/function getExtension($fileName) {
    global $g, $objDBCA, $objMTS;
    $tmpFileNameAry = explode(".", $fileName);
    $extension = $tmpFileNameAry[count($tmpFileNameAry)-1];

    return $extension;
}