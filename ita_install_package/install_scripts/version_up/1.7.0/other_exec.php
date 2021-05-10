<?php
// 対象のファイルを暗号化する

$root_dir_path = $argv[1];

$targetDirList = array("$root_dir_path/ita-root/uploadfiles/2100000303/CONN_SSH_KEY_FILE/",
                       "$root_dir_path/ita-root/uploadfiles/2100000303/WINRM_SSL_CA_FILE/",
                       "$root_dir_path/ita-root/uploadfiles/2100040708/ANSTWR_LOGIN_SSH_KEY_FILE/",
                      );

foreach($targetDirList as $targetDir){

    $targetFileList = list_files($targetDir);
    foreach($targetFileList as $targetFile){
        $src_data =  file_get_contents($targetFile);
        $enc_data = ky_encrypt($src_data);
        $ret = file_put_contents($targetFile, $enc_data);
    }
}

// 暗号化関数
function ky_encrypt($lcStr){
    return str_rot13(base64_encode($lcStr));
}

// ディレクトリ内のファイル一覧取得関数
function list_files($dir){
    $list = array();
    $files = scandir($dir);
    foreach($files as $file){
        if($file == '.' || $file == '..'){
            continue;
        } else if (is_file($dir . $file)){
            $list[] = $dir . $file;
        } else if( is_dir($dir . $file) ) {
            //$list[] = $dir;
            $list = array_merge($list, list_files($dir . $file . DIRECTORY_SEPARATOR));
        }
    }
    return $list;
}

?>