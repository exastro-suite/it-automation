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

//----クラス定義（同名の関数が競合しないように、クラスの中で定義＞
class RestAPIInfoAdmin{
    protected $aryCallList;
    protected $strRestAPIIdentifyMark;
    protected $strAuthMode;
    protected $strReceptType;

    protected $strRequestURIOnRest;

    protected $arySuccessInfo;
    protected $aryErrorInfo;

    protected $intResultStatusCode;
    protected $strFreeLogForRequestLast;

    protected $aryReqHeaderData;
    protected $aryReceptData;

    protected $strAccessKeyIdOnRest;
    protected $strSecretAccessKeyOnRest;

    protected $strWhiteCtrls;
    protected $intBasicMaxByteLength;
    protected $intBasicMaxChrLength;

    protected $strKeyDirName;
    protected $aryAddSettingFileList;
    protected $aryAddSettingItemList;

    protected $strReqInitTime;

    protected $strBufferRequirePath;
    protected $boolBufferExecute;

    function __construct($strRequestURIOnRest, $arySuccessInfo=array(), $aryErrorInfo=array(), $intResultStatusCode=200, $strKeyDirName="ita-root"){
        $this->aryCallList                = null;
        $this->strRestAPIIdentifyMark     = '';
        $this->strAuthMode                = '0';
        $this->strReceptType              = '0';

        $this->strRequestURIOnRest = $strRequestURIOnRest;

        $this->arySuccessInfo      = $arySuccessInfo;
        $this->aryErrorInfo        = $aryErrorInfo;

        $this->intResultStatusCode        = $intResultStatusCode;
        $this->strFreeLogForRequestLast   = '';

        $this->aryReqHeaderData           = array();
        $this->aryReceptData              = array();
        $this->strAccessKeyIdOnRest       = '';
        $this->strSecretAccessKeyOnRest   = '';

        $this->strWhiteCtrls              = '\r\n\t';
        $this->intBasicMaxByteLength      = 4000;
        $this->intBasicMaxChrLength       = 4000;

        $this->strKeyDirName              = "inetpub";

        if( is_string($strKeyDirName)===true )
        {
            $this->strKeyDirName = $strKeyDirName;
        }
        $this->aryAddSettingFileList      = array();
        $this->aryAddSettingItemList      = array();
        
        $this->strReqInitTime = $this->getMircotime(0);

        $this->strBufferRequirePath = '';
        $this->boolBufferExecute = true;
    }

    // PHPがセーフモードかどうかを調べる
    function checkSafeMode($boolExeContinue){
        if( $boolExeContinue === true )
        {
            if( ini_get('safe_mode')=='1' )
            {
                //----5.3のための処置
                $boolExeContinue                 = false;
                $this->intResultStatusCode       = 500;
                $this->aryErrorInfo['Exception'] = 'PHP is Safe Mode';
                //5.3のための処置----
            }
        }
        return $boolExeContinue;
    }

    // サーバー側PHPの連想配列での設定を調べる
    function checkCallSetting($boolExeContinue, $aryCallSetting){
        if( $boolExeContinue === true )
        {
            if( isset($aryCallSetting)===false )
            {
                $boolExeContinue                 = false;
                $this->intResultStatusCode       = 500;
                $this->aryErrorInfo['Exception'] = 'RestAPI construction information is not exists';
            }
        }
        if( $boolExeContinue === true )
        {
            if( is_array($aryCallSetting)===false )
            {
                $boolExeContinue                 = false;
                $this->intResultStatusCode       = 500;
                $this->aryErrorInfo['Exception'] = 'RestAPI construction information format is not correct';
            }
        }
        if( $boolExeContinue === true )
        {
            // ----RestAPI識別子(必須)が設定されているかをチェック
            $strRestAPIIdentifyMark = null;
            if( array_key_exists('API_IDENTIFY', $aryCallSetting)===true )
            {
                $strRestAPIIdentifyMark = $aryCallSetting['API_IDENTIFY'];
            }
            if( is_string($strRestAPIIdentifyMark)===false )
            {
                // 文字列型ではなかった
                $boolExeContinue                 = false;
                $this->intResultStatusCode       = 500;
                $this->aryErrorInfo['Exception'] = 'RestAPI configuration directory information format is not correct';
            }
            else
            {
                 $this->strRestAPIIdentifyMark = $strRestAPIIdentifyMark;
            }
            //RestAPI識別子(必須)が設定されているかをチェック----
        }
        if( $boolExeContinue === true )
        {
            // ----インクルードするファイルリスト（任意）があるかをチェック
            if( array_key_exists('CALL_MODULE_LIST', $aryCallSetting)===true )
            {
                $aryCallList = $aryCallSetting['CALL_MODULE_LIST'];
                if( is_array($aryCallList)===false )
                {
                    // 呼び出すファイルリストが配列型ではなかった
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       = 500;
                    $this->aryErrorInfo['Exception'] = 'RestAPI submodule-list format is not correct';
                }
                else
                {
                    $this->aryCallList = $aryCallList;
                }
            }
            // インクルードするファイルリスト（任意）があるかをチェック----
        }
        if( $boolExeContinue === true )
        {
            // ----パラメータ受取様式（任意）の指定があるかをチェック
            if( array_key_exists('RECEPT_TYPE', $aryCallSetting)===true )
            {
                $strReceptType = $aryCallSetting['RECEPT_TYPE'];
                if( is_string($strReceptType)===false )
                {
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       = 500;
                    $this->aryErrorInfo['Exception'] = 'RestAPI authMode configuration format is not correct';
                }
                else
                {
                    $this->strReceptType = $strReceptType;
                }
            }
            // パラメータ受取様式（任意）の指定があるかをチェック----
        }
        if( $boolExeContinue === true )
        {
            // ----認証様式（任意）の指定があるかをチェック
            if( array_key_exists('AUTH_MODE', $aryCallSetting)===true )
            {
                $strAuthMode = $aryCallSetting['AUTH_MODE'];
                if( is_string($strAuthMode)===false )
                {
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       = 500;
                    $this->aryErrorInfo['Exception'] = 'RestAPI authMode configuration format is not correct';
                }
                else
                {
                    $this->strAuthMode = $strAuthMode;
                }
            }
            // 認証様式（任意）の指定があるかをチェック----
        }
        if( $boolExeContinue === true )
        {
            // ----追加の設定ファイル読み込みリスト（任意）の指定があるかをチェック
            if( array_key_exists('RECEPT_DATA_OVERRIDE', $aryCallSetting)===true )
            {
                $aryReceptData = $aryCallSetting['RECEPT_DATA_OVERRIDE'];
                if( is_array($aryReceptData)===false )
                {
                    // 追加の設定ファイル読み込みリストが配列型ではなかった
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       = 500;
                    $this->aryErrorInfo['Exception'] = 'RestAPI recept-data format by configuration is not correct';
                }
                else
                {
                    $this->aryReceptData     = $aryReceptData;
                }
            }
            // 追加の設定ファイル読み込みリスト（任意）の指定があるかをチェック----
        }
        if( $boolExeContinue === true )
        {
            // ----追加の設定ファイル読み込みリスト（任意）の指定があるかをチェック
            if( array_key_exists('ADD_SETTING_FILE_LIST', $aryCallSetting)===true )
            {
                $aryFileList = $aryCallSetting['ADD_SETTING_FILE_LIST'];
                if( is_array($aryFileList)===false )
                {
                    // 追加の設定ファイル読み込みリストが配列型ではなかった
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       = 500;
                    $this->aryErrorInfo['Exception'] = 'RestAPI additional check setting file-list format is not correct';
                }
                else
                {
                    $this->aryAddSettingFileList     = $aryFileList;
                }
            }
            // 追加の設定ファイル読み込みリスト（任意）の指定があるかをチェック----
        }
        return $boolExeContinue;
    }

    function checkFileSetting($boolExeContinue){
        if( $boolExeContinue === true )
        {
            $strRestAPIIdentifyMark    = $this->strRestAPIIdentifyMark;
            $root_dir_path             = $this->getApplicationRootDirPath();

            switch($this->strAuthMode)
            {
                case 0:
                    $strConfFileOfAccessKeyIdOnRest        = "\\confs\\restapiconfs\\{$strRestAPIIdentifyMark}\\accesskey.txt";
                    $strConfFileOfSecretAccessKeyOnRest    = "\\confs\\restapiconfs\\{$strRestAPIIdentifyMark}\\secret_accesskey.txt";
                    //----ここから固定置きファイルから設定情報を取得

                    // 認証用のアクセスキー
                    $strAccessKeyIdOnRest         = @file_get_contents($root_dir_path . $strConfFileOfAccessKeyIdOnRest);

                    // 認証用の秘密鍵
                    $strSecretAccessKeyOnRest     = @file_get_contents($root_dir_path . $strConfFileOfSecretAccessKeyOnRest);

                    //ここまで固定置きファイルから設定情報を取得----

                    if( $boolExeContinue === true )
                    {
                        //----ここから固定置きファイルからRestAPI設定情報のチェック
                        if( 0 == strlen($strAccessKeyIdOnRest ) )
                        {
                            $boolExeContinue                 = false;
                            $this->intResultStatusCode       =  500;
                            $this->aryErrorInfo['Exception'] = 'Accesskey is not exists on RestAPI server';
                        }
                        else
                        {
                            $this->strAccessKeyIdOnRest      = $this->ky_decrypt($strAccessKeyIdOnRest);
                        }
                    }
                    
                    if( $boolExeContinue === true )
                    {
                        if( 0 == strlen($strSecretAccessKeyOnRest) )
                        {
                            $boolExeContinue                 = false;
                            $this->intResultStatusCode       =  500;
                            
                            $this->aryErrorInfo['Exception'] = 'Secret accesskey is not exists on RestAPI server';
                        }
                        else
                        {
                            $this->strSecretAccessKeyOnRest  = $this->ky_decrypt($strSecretAccessKeyOnRest);
                        }
                    }
                    break;
                default:
                    break;
            }
        }

        if( $boolExeContinue === true )
        {
            // データリレイストレージのルートディレクトリへのパス
            $intFocusIndex = 0;
            
            foreach( $this->aryAddSettingFileList as $strKey=>$strVal)
            {
                $intFocusIndex += 1;
                if( is_string($strKey)===false || is_string($strVal)===false )
                {
                    // キーまたは値の型が文字列型ではない
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       =  500;
                    $this->aryErrorInfo['Exception'] = "Additional setting[{$intFocusIndex}] item is not correct format";
                    break;
                }

                if( 0==strlen($strKey) || 0==strlen($strVal) )
                {
                    // キーまたは値が長さが0
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       =  500;
                    //
                    $this->aryErrorInfo['Exception'] = "Additional setting[{$intFocusIndex}] item length is zero byte";
                    break;
                }

                $strCheckFilePath = "{$root_dir_path}\\confs\\restapiconfs\\{$strRestAPIIdentifyMark}\\{$strVal}";
                if( is_file($strCheckFilePath)===false )
                {
                    // ファイルが存在しなかった
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       =  500;
                    
                    $this->aryErrorInfo['Exception'] = "Additional setting[{$intFocusIndex}] file is not found";
                    break;
                }

                $strFileContent = @file_get_contents($strCheckFilePath);
                if( 0 == strlen($strFileContent) )
                {
                    // ファイルの中身が長さが0だった
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       =  500;
                    $this->aryErrorInfo['Exception'] = "Additional setting[{$intFocusIndex}] file content length is zero byte";
                }
                $this->aryAddSettingItemList[$strKey] = $strFileContent;
                $intFocusIndex += 1;
            }
        }
        return $boolExeContinue;
    }

    function authExecute($boolExeContinue){
        if( $boolExeContinue === true )
        {
            switch($this->strAuthMode)
            {
                case 0:
                    $boolExeContinue = $this->checkRequestHeaderForAuth($boolExeContinue);
                    $boolExeContinue = $this->checkAuthorizationInfo($boolExeContinue);
                    break;
                default:
                    break;
            }
        }
        return $boolExeContinue;
    }

    function checkRequestHeaderForAuth($boolExeContinue){
        //----リクエストヘッダを全て取得
        if( $boolExeContinue === true )
        {
            $aryReqHeaderRaw = getallheaders();

            if( $aryReqHeaderRaw === false )
            {
                $boolExeContinue                 = false;
                $this->intResultStatusCode       =  500;
                $this->aryErrorInfo['Exception'] = 'Request header unknown error';
            }
        }
        //リクエストヘッダを全て取得----
        //----http(s)リクエストヘッダに所定の項目があるかをチェック
        if( $boolExeContinue === true )
        {
            if( $boolExeContinue === true )
            {
                if( array_key_exists('Content-Type', $aryReqHeaderRaw) !== true )
                {
                    $boolExeContinue = false;
                    $this->aryErrorInfo['Exception'] = 'Required request header item[Content-Type] is not exists';
                }
            }
            if( $boolExeContinue === true )
            {
                if( array_key_exists('X-Umf-Api-Version', $aryReqHeaderRaw) !== true )
                {
                    $boolExeContinue = false;
                    $this->aryErrorInfo['Exception'] = 'Required request header item[X-UMF-API-Version] is not exists';
                }
            }
            if( $boolExeContinue === true )
            {
                if( array_key_exists('Date', $aryReqHeaderRaw) !== true )
                {
                    $boolExeContinue = false;
                    $this->aryErrorInfo['Exception'] = 'Required request header item[Date] is not exists';
                }
            }
            if( $boolExeContinue === true )
            {
                if( array_key_exists('Authorization', $aryReqHeaderRaw) !== true )
                {
                    $boolExeContinue = false;
                    $this->aryErrorInfo['Exception'] = 'Required request header item[Authorization] is not exists';
                }
            }
            
            if( $boolExeContinue === false )
            {
                //----所定項目のいずれかが欠落しており、要求が正しくない、と評価する
                $this->intResultStatusCode       =  400;
                //所定項目のいずれかが欠落しており、要求が正しくない、と評価する----
            }
        }
        //http(s)リクエストヘッダに所定の項目があるかをチェック----
        
        //----http(s)リクエストヘッダに所定の項目ごとに値のチェック
        if( $boolExeContinue === true )
        {
            
            $this->aryReqHeaderData = $aryReqHeaderRaw;
        }
        return $boolExeContinue;
    }

    function checkAuthorizationInfo($boolExeContinue){
        //----定数の宣言
        $strCRLF = "\r\n";
        //定数の宣言----

        //----ここから認証
        if( $boolExeContinue === true )
        {
            // リクエストで送られてきた情報
            $strHeaderAuthorization     = $this->aryReqHeaderData['Authorization'];
            $strHeaderDate              = $this->aryReqHeaderData['Date'];

            $strRequestURIOnRest        = $this->strRequestURIOnRest;

            // Restサーバー上にある情報
            $strAccessKeyIdOnRest       = $this->strAccessKeyIdOnRest;
            $strSecretAccessKeyOnRest   = $this->strSecretAccessKeyOnRest;

            $aryTempData = explode("SharedKeyLite {$strAccessKeyIdOnRest}:", $strHeaderAuthorization);
            if( count($aryTempData) != 2 )
            {
                $boolExeContinue                 = false;
                $this->aryErrorInfo['Exception'] = 'Authorization infomation format error';
            }
            else
            {

                $tmpStrStringToSignOnRest = $strHeaderDate . $strCRLF . $strRequestURIOnRest;
                $tmpStrSignatureOnRest = shell_exec( 'echo -e -n "' . $tmpStrStringToSignOnRest . '" | openssl dgst -sha256 -binary -hmac ' . $strSecretAccessKeyOnRest . ' | openssl base64' );

                if( $tmpStrSignatureOnRest!==$aryTempData[1]."\n" && strlen($tmpStrSignatureOnRest)==strlen($aryTempData[1]."\n") )
                {
                    //----一致しなかった
                    $boolExeContinue                 = false;
                    $this->aryErrorInfo['Exception'] = 'Authorization infomation is not correct';
                    //一致しなかった----
                }
                unset($tmpStrStringToSignOnRest);
                unset($tmpStrSignatureOnRest);
            }

            if( $boolExeContinue=== false )
            {
                //----適切な認証情報を提供せず、保護されたリソースに対しアクセスした場合に返却するコード
                $this->intResultStatusCode       =  401;
                //適切な認証情報を提供せず、保護されたリソースに対しアクセスした場合に返却するコード----
            }
            //ここまで認証----
        }
        //http(s)リクエストヘッダに所定の項目ごとに値のチェック----
        return $boolExeContinue;
    }

    function requireWrap(){
        $boolExeContinue = true;
        $boolRequire = require($this->strBufferRequirePath);
        if( $boolRequire===false )
        {
            // 呼び出し失敗
            $boolExeContinue = false;
            $this->intResultStatusCode       = 500;
            $this->aryErrorInfo['Exception'] = 'RestAPI submodule['.$this->strBufferRequirePath.'] call error';
        }
        else
        {
            if(isset($boolExeContinue)===false)
            {
                // 規定の変数を破壊
                $boolExeContinue                 = false;
                $this->intResultStatusCode       = 500;
                $this->aryErrorInfo['Exception'] = 'RestAPI submodule['.$this->strBufferRequirePath.'] coding is not collect';
            }
            else
            {
                // 規定の変数の型を破壊
                if(is_bool($boolExeContinue)===false)
                {
                    $boolExeContinue                 = false;
                    $this->intResultStatusCode       = 500;
                    $this->aryErrorInfo['Exception'] = 'RestAPI submodule['.$this->strBufferRequirePath.'] coding is not collect';
                }
            }
        }
        return $boolExeContinue;
    }

    function callSubModules($boolExeContinue, $aryCallList=null){
        //----ここからメイン処理
        if( $boolExeContinue === true )
        {
            $strRestAPIIdentifyMark    = $this->strRestAPIIdentifyMark;
            $root_dir_path             = $this->getApplicationRootDirPath();

            if( is_array($aryCallList)===true )
            {
                $intFocusIndex = 0;
                foreach($aryCallList as $varKey=>$varString )
                {
                    if( is_string($varString)===true )
                    {
                        $this->strBufferRequirePath = "{$root_dir_path}\\libs\\restapiindividuallibs\\{$strRestAPIIdentifyMark}\\{$varString}";
                        if( is_file($this->strBufferRequirePath)===true )
                        {
                            $boolRequire = $this->requireWrap();
                            if( $boolRequire===false )
                            {
                                $boolExeContinue = false;
                                break;
                            }
                        }
                        else
                        {
                            //ファイルが存在しなかった
                            $boolExeContinue                 = false;
                            $this->intResultStatusCode       = 500;
                            $this->aryErrorInfo['Exception'] = "RestAPI submodule file[{$intFocusIndex}] is not found";
                            break;
                        }
                    }
                    else
                    {
                        // 文字列ではなかった
                        $boolExeContinue                 = false;
                        $this->intResultStatusCode       = 500;
                        $this->aryErrorInfo['Exception'] = "RestAPI submodule file[{$intFocusIndex}] name is unknown";
                        break;
                    }
                    $intFocusIndex += 1;
                }
            }
        }
        //ここまでメイン処理----
        return $boolExeContinue;
    }

    function receptDataImport($boolExeContinue){
        if( $boolExeContinue === true )
        {
            switch($this->strReceptType)
            {
                case 0:
                    //----JSONで送られてきたパラメータを取得
                    $strTempJsonString = file_get_contents('php:/'.'/input');
                    $objJSONOfReceptedData = json_decode($strTempJsonString, true, 512, 0);
                    if( is_array($objJSONOfReceptedData) !== true )
                    {
                        $boolExeContinue = false;
                        $this->intResultStatusCode       = 400;
                        $this->aryErrorInfo['Exception'] = 'JSON format is not correct';
                    }
                    else
                    {
                        $this->aryReceptData = $objJSONOfReceptedData;
                    }
                    //JSONで送られてきたパラメータを取得----
                    break;
                default:
                    break;
            }
        }
        return $boolExeContinue;
    }

    // RO：解析結果1
    function getCallList(){
        return $this->aryCallList;
    }
    // RO：解析結果2
    function getResultStatusCode(){
        return $this->intResultStatusCode;
    }
    // RO：解析結果3
    function getValueFromAddSetting($strKey){
        $strValue = null;
        if( is_string($strKey)===true )
        {
            // 文字列型で指定された
            if( array_key_exists($strKey, $this->aryAddSettingItemList)===true )
            {
                // キーがあった場合、値を返す
                $strValue = $this->aryAddSettingItemList[$strKey];
            }
        }
        return $strValue;
    }
    // RO：解析結果4
    function getReceptData(){
        return $this->aryReceptData;
    }
    // RO：解析結果5
    function getReqHeaderData(){
        return $this->aryReqHeaderData;
    }

    // ----I/O：解析結果1
    function setErrorInfo($aryErrorInfo){
        if( is_string($aryErrorInfo)===true ){
            $this->aryErrorInfo = $aryErrorInfo;
        }
    }
    function getErrorInfo(){
        return $this->aryErrorInfo;
    }
    // I/O：解析結果1----

    // ----I/O：解析結果2
    function setSuccessInfo($arySuccessInfo){
        if( is_string($arySuccessInfo)===true ){
            $this->arySuccessInfo = $arySuccessInfo;
        }
    }
    function getSuccessInfo(){
        return $this->arySuccessInfo;
    }
    // I/O：解析結果2----

    // ----I/O：解析結果3
    function setFreeLogForRequestLast($strFreeLogForRequestLast){
        if( is_string($strFreeLogForRequestLast)===true ){
            $this->strFreeLogForRequestLast = $strFreeLogForRequestLast;
        }
    }
    function getFreeLogForRequestLast(){
        return $this->strFreeLogForRequestLast;
    }
    // I/O：解析結果3----


    function getRequestInitTime(){
        return $this->strReqInitTime;
    }

    //----基本バリデーター
    function setBasicWhiteCtrls($strValue){
        $this->strWhiteCtrls = $strValue;
    }
    function getBasicWhiteCtrls(){
        return $this->strWhiteCtrls;
    }

    //----バイナリデータとしての最大長
    function setBasicMaxByteLength($intValue){
        $this->intBasicMaxByteLength = (integer)$intValue;
    }
    function getBasicMaxByteLength(){
        return $this->intBasicMaxByteLength;
    }
    //バイナリデータとしての最大長----

    //----文字列としての最大長
    function setBasicMaxChrLength($intValue){
        $this->intBasicMaxChrLength = (integer)$intValue;
    }
    function getBasicMaxChrLength(){
        return $this->intBasicMaxChrLength;
    }
    //文字列としての最大長----

    function checkBasicValid($value,&$refErrorCode=0){
        //----$refErrorCode=1:入力値の長さが規定バイトを超えています。
        //----$refErrorCode=2:入力値の長さが規定文字数を超えています。
        //----$refErrorCode=3:"入力値[NULLバイト文字等が含まれた値]が不正です。
        $retBool = true;
        $intMaxAsByteLength = $this->getBasicMaxByteLength();
        if($intMaxAsByteLength < strlen($value)){
            //----入力値の長さが規定バイトを超えています。";
            $retBool = false;
            $refErrorCode = 1;
        }else{
            $intMaxAsChrLength = $this->intBasicMaxChrLength;
            if( preg_match('/\A['.$this->strWhiteCtrls.'[:^cntrl:]]{0,'.$intMaxAsChrLength.'}\z/u', $value) == 0 ){
                $retBool = false;
                if($intMaxAsChrLength < mb_strlen($value, 'UTF-8')){
                    //"入力値の長さが規定文字数を超えています。";
                    $refErrorCode = 2;
                }else{
                    //"入力値[NULLバイト文字等が含まれた値]が不正です。";
                    $refErrorCode = 3;
                }
            }
        }
        return $retBool;
    }
    //基本バリデーター----

    function getApplicationRootDirPath(){
        $aryDirName = array();
        $aryDirName = explode($this->strKeyDirName, dirname(__FILE__));
        return $aryDirName[0].$this->strKeyDirName;
    }

    //----REST-API-LOG系
    function RestAPI_log($FREE_LOG){
        RestAPICommonLog($FREE_LOG);
    }
    //REST-API-LOG系----

    //----汎用系
    
    // ----簡易暗号化・復号化ファンクション
    function ky_encrypt($lcStr){
        // 暗号化
        return str_rot13(base64_encode($lcStr));
    }

    function ky_decrypt($lcStr){
        // 復号化
        return base64_decode(str_rot13($lcStr));
    }
    // 簡易暗号化・復号化ファンクション----

    function ky_phpProcessSleep($lcIntSec){
        // 簡易スリープ
        $lcIntStartTimeSec = time();
        do{
        } while(time() < $lcIntStartTimeSec + $lcIntSec);
        return true;
    }
    
    //----時刻取得系
    function getMircotime($mode=0){
        //----$mode[0:Unixtimestamp/1:YmdHis/2:Y/m/d H:i:s]
        $strFormat = "";
        $arrayStr = explode(" ", microtime());
        if( $mode == 2 ){
            $strFormat = "Y/m/d H:i:";
        }else if( $mode == 1 ){
            $strFormat = "YmdHi";
        }
        if( $strFormat == "" ){
            $ret = $arrayStr[1].".".substr(str_replace("0.","",$arrayStr[0]),0,6);
        }else{
            $sec = date("s",$arrayStr[1]) + $arrayStr[0];
            $ret = date($strFormat, $arrayStr[1]).$sec;
        }
        return $ret;
    }

    function convFromStrDateToUnixtime($str,$boolPlusMirco=false){
        //----$str[YYYYMMDDNNSS(.000000)||YYYY/MM/DD HH:NN:SS(.000000)]
        //----$boolPlusMirco:マイクロ秒付記モード
        $array = explode(".", $str);
        $intTime = strtotime($array[0]);
        $decTime = "";
        if($boolPlusMirco === true){
            if( isset($array[1]) === true ){
                $decTime = ".".sprintf('%06d', $array[1]);
            }else{
                $decTime = ".000000";
            }
        }
        $ret = $intTime.$decTime;
        return $ret;
    }

    function convFromUnixtimeToStrDate($str,$boolPlusMirco=false,$mode=0){
        //----$str[unixtimestamp(.000000)]
        //----$boolPlusMirco:マイクロ秒付記モード
        //----$mode[0:Unixtimestamp||1:YmdHis||2:Y/m/d H:i:s]
        $strFormat = "";
        if($boolPlusMirco === true){
            $array = explode(".", $str);
            $intTime = date($array[0]);
            $decTime = ".".sprintf('%06d', $array[1]);
        }else{
            $intTime = $str;
            $decTime = ".000000";
        }
        if( $mode == 2 ){
            $strFormat = "Y/m/d H:i:";
        }else if( $mode == 1 ){
            $strFormat = "YmdHi";
        }
        if( $strFormat == "" ){
            $ret = $intTime.$decTime;
        }else{
            $sec = date("s",$intTime).$decTime;
            $ret = date($strFormat, $intTime).$sec;
        }
        return $ret;
    }
    //時刻取得系----

    function getSourceIPAddress($boolValueForIpCheck=true){
        //----ipv4のみ(
        //----XFFに基本的にはある、というスタンス。その他を調べるのはオマケ
        $strPattern = "/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/";
        $retVarValue = "";
        $p_SOURCE_IP = "";
        $aryRemoteAddressInfo = array();

        // 8項目
        $aryCheckKey = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_VIA',
            'HTTP_SP_HOST',
            'HTTP_FROM',
            'HTTP_FORWARDED',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        );

        foreach($aryCheckKey as $strFocusCheckKey)
        {
            $strTmpValue = "";
            if( array_key_exists($strFocusCheckKey, $_SERVER ) )
            {
                $strTmpValue = $_SERVER[$strFocusCheckKey];
                $aryExploded = explode(",", $strTmpValue);
                $strCheckValue = $aryExploded[0];
                $strCheckValue = str_replace(" ","", $strCheckValue);
                if( preg_match($strPattern, $strCheckValue)===1 )
                {
                    if($p_SOURCE_IP == "" )
                    {
                        $p_SOURCE_IP = $strCheckValue;
                    }
                }
            }
            $aryRemoteAddressInfo[] = $strTmpValue;
        }
        if( $boolValueForIpCheck===false )
        {
            // ----ログ用
            $retVarValue = array();
            $retVarValue[0] = $p_SOURCE_IP;
            $retVarValue[1] = $aryRemoteAddressInfo;
            // ログ用----
        }
        else
        {
            // ----IPチェック用
            $retVarValue = $p_SOURCE_IP;
            // IPチェック用----
        }
        return $retVarValue;
    }
    //汎用系----
}
//クラス定義（同名の関数が競合しないように、クラスの中で定義＞----
function RestAPICommonLog($FREE_LOG)
{    
    ////////////////////////////////
    // ルートディレクトリを取得   //
    ////////////////////////////////
    if ( empty($root_dir_path) )
    {
        $root_dir_temp = array();
        $root_dir_temp = explode( "inetpub", dirname(__FILE__) );
        $root_dir_path = $root_dir_temp[0] . "inetpub";
    }
    
    ////////////////////////////////
    // 定数宣言                   //
    ////////////////////////////////
    
    // ログ出力先ディレクトリ
    // (指定がない場合のみ)
    $log_output_dir = $log_dir_path . "\\logs\\restapilogs\\testplayer_driver";
    
    // ログファイル名プレフィックス
    // (指定がない場合のみ)
    $log_file_prefix = "restapi_";
    
    // ログファイル名ポストフィックス
    $log_file_postfix = ".log";
    
    ////////////////////////////////
    // 変数初期化                 //
    ////////////////////////////////
    
    // 変数初期化
    $p_FREE_LOG = "";
    
    // ログファイル名(フルパス)を作成
    $tmpVarTimeStamp = time();
    $logfile = $root_dir_path . "\\" . $log_output_dir . "\\" . $log_file_prefix . date("Ymd",$tmpVarTimeStamp) . $log_file_postfix;
    
    // フリーログを準備
    if ( isset($FREE_LOG) ) $p_FREE_LOG = $FREE_LOG;
    
    // ログ出力
    $logtime = date("Y/m/d H:i:s",$tmpVarTimeStamp);
    $filepointer=fopen(  $logfile, "a");
    flock($filepointer, LOCK_EX);
    fputs($filepointer, "[" . $logtime . "]" . $p_FREE_LOG . "\n" );
    flock($filepointer, LOCK_UN);
    fclose($filepointer);
    unset($tmpVarTimeStamp);
}

?>
