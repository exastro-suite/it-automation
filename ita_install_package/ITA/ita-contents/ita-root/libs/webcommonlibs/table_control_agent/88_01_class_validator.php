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
class Validator {

	protected $strWhiteCtrls;
	protected $intBasicMaxByteLength;
	protected $intBasicMaxChrLength;

	protected $boolLengthCountAsChr;
	protected $strErrMsgValue;
	protected $strModeIdOfLastErr;

	protected $intMinLengthAsVar;
	protected $intMaxLengthAsVar;
	protected $varMinAsNum;
	protected $varMaxAsNum;
	protected $strRegExpFormat;
	protected $strDisplayFormat;

	protected $aryIntMinLengthAsVarForMode;
	protected $aryIntMaxLengthAsVarForMode;
	protected $aryVarMinAsNumForMode;
	protected $aryVarMaxAsNumForMode;
	protected $aryStrRegExpFormatForMode;
	protected $aryStrDisplayFormatForMode;

	protected $strCheckType;
	protected $boolErrShowPrefix;

	function __construct($min=null, $max=null, $strRegexpFormat="", $strDisplayFormat="", $strCheckType="", $boolErrShowPrefix=true){
		$this->strWhiteCtrls = '\r\n\t';
		$this->intBasicMaxByteLength = 4000;
		$this->intBasicMaxChrLength = 4000;
		
		$this->varMinAsNum = $min;
		$this->varMaxAsNum = $max;
		
		$this->strRegExpFormat = $strRegexpFormat;
		$this->strDisplayFormat = $strDisplayFormat;
		
		$this->strCheckType = $strCheckType;
		$this->boolErrShowPrefix = $boolErrShowPrefix;
		
		$this->aryIntMinLengthAsVarForMode = array();
		$this->aryIntMaxLengthAsVarForMode = array();
		$this->aryVarMinAsNumForMode = array();
		$this->aryVarMaxAsNumForMode = array();
		$this->aryStrRegExpFormatForMode = array();
		$this->aryStrDisplayFormatForMode = array();
		
		$this->setMinLength(0);
		$this->setMaxLength(4000);
	
		$this->setLengthCountAsChr(false); //デフォルト（テキストはバイトで長さを測る）
	}

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

	//----文字列としての最小長
	function setMinLength($intValue,$strModeId=""){
		if( is_string($strModeId) === true ){
			if( $strModeId == "" ){
				$this->intMinLengthAsVar = (integer)$intValue;
			}else{
				$this->aryIntMinLengthAsVarForMode[$strModeId] = (integer)$intValue;
			}
		}
	}
	function getMinLength($strModeId=""){
		$retIntMinLength = null;
		if( array_key_exists($strModeId, $this->aryIntMinLengthAsVarForMode) === true ){
			$retIntMinLength = $this->aryIntMinLengthAsVarForMode[$strModeId];
		}else{
			$retIntMinLength = $this->intMinLengthAsVar;
		}
		return $retIntMinLength;
	}
	//文字列としての最小長----
	
	//----文字列としての最大長
	function setMaxLength($intValue,$strModeId=""){
		if( is_string($strModeId) === true ){
			if( $strModeId == "" ){
				$this->intMaxLengthAsVar = (integer)$intValue;
			}else{
				$this->aryIntMaxLengthAsVarForMode[$strModeId] = (integer)$intValue;
			}
		}
	}
	function getMaxLength($strModeId=""){
		$retIntMaxLength = null;
		if( array_key_exists($strModeId, $this->aryIntMaxLengthAsVarForMode) === true ){
			$retIntMaxLength = $this->aryIntMaxLengthAsVarForMode[$strModeId];
		}else{
			$retIntMaxLength = $this->intMaxLengthAsVar;
		}
		return $retIntMaxLength;
	}
	//文字列としての最大長----


	//----テキスト系：UTFの文字数で数えるのか？あるいはバイト数で数えるのか？
	function setLengthCountAsChr($boolValue){
		$this->boolLengthCountAsChr = (boolean)$boolValue;
	}
	function getLengthCountAsChr(){
		return $this->boolLengthCountAsChr;
	}
	//テキスト系：UTFの文字数で数えるのか？あるいはバイト数で数えるのか？----

	//----数値としての最小
	function setMin($varValue,$strModeId=""){
		if( is_string($strModeId) === true ){
			if( $strModeId == "" ){
				$this->varMinAsNum = $varValue;
			}else{
				$this->aryIntMinLengthAsVarForMode[$strModeId] = $varValue;
			}
		}
	}
	function getMin($strModeId=""){
		$retIntMinLength = null;
		if( array_key_exists($strModeId, $this->aryIntMinLengthAsVarForMode) === true ){
			$retIntMinLength = $this->aryIntMinLengthAsVarForMode[$strModeId];
		}else{
			$retIntMinLength = $this->varMinAsNum;
		}
		return $retIntMinLength;
	}
	//数値としての最小----
	
	//----数値としての最大
	function setMax($varValue,$strModeId=""){
		if( is_string($strModeId) === true ){
			if( $strModeId == "" ){
				$this->varMaxAsNum = $varValue;
			}else{
				$this->aryVarMaxAsNumForMode[$strModeId] = $varValue;
			}
		}
	}
	function getMax($strModeId=""){
		$retIntMaxLength = null;
		if( array_key_exists($strModeId, $this->aryVarMaxAsNumForMode) === true ){
			$retIntMaxLength = $this->aryVarMaxAsNumForMode[$strModeId];
		}else{
			$retIntMaxLength = $this->varMaxAsNum;
		}
		return $retIntMaxLength;
	}
	//数値としての最大----

	//----正規表現リスト
	function setRegExp($strRegExp, $strModeId=""){
		if( is_string($strModeId) === true ){
			if( $strModeId == "" ){
				$this->strRegExpFormat = $strRegExp;
			}else{
				$this->aryStrRegExpFormatForMode[$strModeId] = $strRegExp;
			}
		}
	}
	function getRegExp($strModeId=""){
		$retStrRegExp = '';
		if( array_key_exists($strModeId, $this->aryStrRegExpFormatForMode) === true ){
			$retStrRegExp = $this->aryStrRegExpFormatForMode[$strModeId];
		}else{
			$retStrRegExp = $this->strRegExpFormat;
		}
		return $retStrRegExp;
	}
	//正規表現リスト----

	//----メッセージ制御
	function setDisplayFormat($strDisplayFormat, $strModeId=""){
		if( is_string($strModeId) === true ){
			if( $strModeId == "" ){
				$this->strDisplayFormat = $strDisplayFormat;
			}else{
				$this->aryStrDisplayFormatForMode[$strModeId] = $strDisplayFormat;
			}
		}
	}
	function getDisplayFormat($strModeId=""){
		$retStrDisplayFormat = '';
		if( array_key_exists($strModeId, $this->aryStrDisplayFormatForMode) === true ){
			$retStrDisplayFormat = $this->aryStrDisplayFormatForMode[$strModeId];
		}else{
			$retStrDisplayFormat = $this->strDisplayFormat;
		}
		return $retStrDisplayFormat;
	}
	//メッセージ制御----

	function checkBasicValid($value){
		$retBool = true;
		$intMaxAsByteLength = $this->getBasicMaxByteLength();
		if($intMaxAsByteLength < strlen($value)){
			global $g;
			$retBool = false;
			//----入力値の長さがバイトを超えています。";
			$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10101",$intMaxAsByteLength));
		}else{
			$intMaxAsChrLength = $this->intBasicMaxChrLength;
			//----文字長が範囲内かつと禁止文字が使われていないかをチェックする(ここでは一律・文字キャラ数で一旦計算する）
			if( preg_match('/\A['.$this->strWhiteCtrls.'[:^cntrl:]]{0,'.$intMaxAsChrLength.'}\z/u', $value) == 0 ){
				global $g;
				$retBool = false;
				if($intMaxAsChrLength < mb_strlen($value, 'UTF-8')){
					//"入力値の長さが文字を超えています。";
					$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10102",$intMaxAsChrLength));
				}else{
					//"入力値[NULLバイト文字等が含まれた値]が不正です。";
					$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10103"));
				}
			}
			//文字長が範囲内かつと禁止文字が使われていないかをチェックする(ここでは一律・文字キャラ数で一旦計算する）----
		}
		return $retBool;
	}

	function isValid($value, $strNumberForRI){
		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		$retBool = false;
		$strModeId = "";
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;

		if( $this->checkBasicValid($value) == false ){
			$boolExeContinue = false;
		}else{
			$retBool = true;
			if( $retBool === false ){
				$this->setValidRule($this->makeValidRule());
			}
		}
		return $retBool;
	}

	function makeValidRule(){
		//return "バリデーションのルール説明文を作成します。";
		$retStrBody = '';
		return $retStrBody;
	}

	function setValidRule($strValue){
		$this->strErrMsgValue = $strValue;
	}

	function getValidRule(){
		//return "バリデーションのルールを表示します。";
		return $this->strErrMsgValue;
	}

	//----D-TUP/D-Tis系
	function setCheckType($value){
		$this->strCheckType = $value;
	}
	function getCheckType(){
		return $this->strCheckType;
	}
	function setErrShowPrefix($boolValue){
		$this->boolErrShowPrefix = (boolean)$boolValue;
	}
	function getErrShowPrefix(){
		return $this->boolErrShowPrefix;
	}
	//D-TUP/D-Tis系----

}

class VariableValidator extends Validator {
	protected $varVariantForIsValid;
	protected $objFunctionIsValid;

	function setVariantForIsValid($varVariantForIsValid){
		$this->varVariantForIsValid = $varVariantForIsValid;
	}
	function getVariantForIsValid(){
		return $this->varVariantForIsValid;
	}
	
	function setFunctionForIsValid($objFunctionIsValid){
		if( is_callable($objFunctionIsValid) ){
			$this->objFunctionIsValid = $objFunctionIsValid;
		}
	}
	function getFunctionForIsValid(){
		return $this->objFunctionIsValid;
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		if( is_callable($this->objFunctionIsValid) ){
			$objFunction = $this->objFunctionIsValid;
			return $objFunction($this, $value, $strNumberForRI, $arrayRegData, $arrayVariant);
		}else{
			return false;
		}
	}

}

class NumberValidator extends Validator {
	//Intといいつつ32bit環境では21億以上は勝手にfloat型になる。
	//floatでは複雑な計算しなければ14桁(10兆,10Tera)までの整数は表現できるので
	//たぶんsumぐらいしかしないので14桁を上限としようと思う。

	protected $aryEtcetera;
	protected $strErrAddMsg;
	protected $intDigitScale; //as number　小数点以下の桁数

	function __construct($min=null, $max=null, $strRegexpFormat, $strDisplayFormat, $aryEtcetera=array()){
		global $g;
		$this->aryEtcetera = $aryEtcetera;
		parent::__construct($min, $max, $strRegexpFormat, $strDisplayFormat);
		$this->intDigitScale = 0;
		$this->setMaxLength(14);	//----4バイトなので10文字+符号1文字+3バッファ
		$this->varMinAsNum = $min;
		$this->varMaxAsNum = $max;
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		//----パラメータ「NOT_NULL」が、setRequired(NULL禁止)と役割が重複して、混乱の原因になっている。廃止の方向で[2015-03-10]
		global $g;
		$retBool = false;
		$strModeId = "";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
				if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
					$strModeId = "DTiS_filterDefault";//filter_table
				}
			}
		}
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;
		$this->strErrAddMsg = "";

		$boolExeContinue = true;
		$varNotNull="";

		if( array_key_exists("NOT_NULL",$this->aryEtcetera) === true ){
			$varNotNull = $this->aryEtcetera['NOT_NULL'];
		}
		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}else{
			if( 0==strlen($value) ){
				if( $varNotNull === true ){
					//----NULLを許容しない場合
					$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10202");
					//NULLを許容しない場合----
				}else{
					$retBool = true;
				}
			}else{
				$strRegexpFormat = $this->getRegExp($strModeId);
				$varMinVal = $this->getMin($strModeId);
				$varMaxVal = $this->getMax($strModeId);
				if( preg_match($strRegexpFormat, $value) === 1 ){
					if( ($varMaxVal === null || bccomp($value, $varMaxVal,$this->intDigitScale) != 1) && ($varMinVal === null || bccomp($value, $varMinVal,$this->intDigitScale) != -1) ){
						$retBool = true;
					}else{
						//----範囲を逸脱した
						$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10203",$value);
						//範囲を逸脱した----
					}
				}else{
					//----書式エラー
    				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10104", array($strRegexpFormat)));
                    return false;
					//書式エラー----
				}
			}
			if( $retBool === false ){
				$this->setValidRule($this->makeValidRule());
			}
		}
		return $retBool;
	}

	function makeValidRule(){
		//----パラメータ「NOT_NULL」が、setRequired(NULL禁止)と役割が重複して、混乱の原因になっている。廃止の方向で[2015-03-10]
		global $g;
		$retStrMsgBody = '';

		$ary = array();
		$constraints = "";
		$varNotNull="";

		$varMinVal = $this->getMin($this->strModeIdOfLastErr);
		$varMaxVal = $this->getMax($this->strModeIdOfLastErr);

		if( $varMinVal !== null ){
			//$ary[] = "最小値:".$varMinVal;
			$ary[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10204",$varMinVal);
		}
		if( $varMaxVal !== null ){
			//$ary[] = "最大値:".$varMaxVal;
			$ary[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10205",$varMaxVal);
		}
		if( 0 < count($ary) ){
			$constraints = "(".implode(",",$ary).")";
		}

		if( array_key_exists("NOT_NULL",$this->aryEtcetera) === true ){
			$varNotNull = $this->aryEtcetera['NOT_NULL'];
		}

		$strDisplayFormat = $this->getDisplayFormat($this->strModeIdOfLastErr);

		if( $varNotNull === false ){
			$retStrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10206",array($strDisplayFormat,$constraints,$this->strErrAddMsg));
		}else{
			$retStrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10207",array($strDisplayFormat,$constraints,$this->strErrAddMsg));
		}
		return $retStrMsgBody;
	}

}

class IntNumValidator extends NumberValidator {

	function __construct($min=null, $max=null, $strRegexpFormat="", $strDisplayFormat="", $aryEtcetera=array()){
		global $g;
		if( $min===null ){
			$min=-2147483648;
		}
		if($max===null){
			$max=2147483647;
		}

		if( $strRegexpFormat == "" ){
			$strRegexpFormat='/^0$|^-?[1-9][0-9]*$/s';
		}
		if( $strDisplayFormat == "" ){
			$strDisplayFormat = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10201");
		}
		parent::__construct($min, $max, $strRegexpFormat, $strDisplayFormat, $aryEtcetera);
		$this->intDigitScale = 0;
		$this->setMaxLength(14);	//----4バイトなので10文字+符号1文字+3バッファ
		$this->varMinAsNum = $min;
		$this->varMaxAsNum = $max;
	}

}

class RowIDNoValidator extends IntNumValidator {
	function __construct($min=0, $max=2147483647, $strDisplayFormat="", $aryEtcetera=array()){
		parent::__construct($min, $max, "", $strDisplayFormat, $aryEtcetera);
	}
}

class FloatNumValidator extends NumberValidator {

	function __construct($min=null, $max=null, $intDigitScale=1, $strDisplayFormat="", $aryEtcetera=array()){
		global $g;
		if( $strDisplayFormat == "" ){
			//$strDisplayFormat = "数値";
			$strDisplayFormat = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10401");
		}
		parent::__construct($min, $max, '/^((-[1-9])?[0-9]*|-0)(\.[0-9]{0,'.$intDigitScale.'})?$/s', $strDisplayFormat, $aryEtcetera);
		$this->intDigitScale = $intDigitScale;
		$this->setMaxLength(9);	//----符号+0+小数点+6桁で、9文字
	}

}

class TextValidator extends Validator {

	function __construct($min=0, $max=255, $boolCheckByMbFlag=false, $strRegexpFormat="/^[^\t\r\n]*$/s", $strDisplayFormat=""){
		global $g;
		if( $strDisplayFormat == "" ){
			//$strDisplayFormat = "カンマとダブルクォートとタブと改行以外の文字";
			$strDisplayFormat = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10501");
		}
		parent::__construct(null, null, $strRegexpFormat, $strDisplayFormat);
		$this->setLengthCountAsChr($boolCheckByMbFlag);

		$this->setMinLength($min);
		$this->setMaxLength($max);
		$this->varMinAsNum = null;
		$this->varMinAsNum = null;
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		global $g;
		$retBool = false;
		$strModeId = "";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
				if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
					$strModeId = "DTiS_filterDefault";//filter_table
				}
			}
		}
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;

		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}else{
			$strRegexpFormat = $this->getRegExp($strModeId);
			if(preg_match($strRegexpFormat, $value) === 1){
				$varMinLen = $this->getMinLength($strModeId);
				$varMaxLen = $this->getMaxLength($strModeId);
				if( $this->getLengthCountAsChr() === true ){
					//----mbstring.internal_encoding(関数(設定/取得)mb_internal_encoding())に、左右されるので注意
					if( ($varMaxLen === null || mb_strlen($value, "UTF-8") <= $varMaxLen) && ($varMinLen === null || mb_strlen($value, "UTF-8") >= $varMinLen) ){
						$retBool = true;
					}
				}else{
					//utfは文字によってバイト数が違うので計算するのにstrlen(bin2hex($value))/2を使う。
					//----改造前診断時コメント[2014-10-02-1258]＜bin2hex(文字列を16進数表記へ置き換える(かならず2の倍数の文字列になる))＞
					if( ($varMaxLen === null || strlen(bin2hex($value))/2 <= $varMaxLen) && ($varMinLen === null || strlen(bin2hex($value))/2 >= $varMinLen) ){
						$retBool = true;
					}
				}
			}
            else{
				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10104", array($strRegexpFormat)));
                return false;
            }

			if( $retBool === false ){
				$this->setValidRule($this->makeValidRule());
			}
		}
		return $retBool;
	}

	function makeValidRule(){
		global $g;
		$retStrMsgBody = '';

		$ary = array();
		$constraints = "";
		//$unit = $this->getLengthCountAsChr()?"文字":"バイト";
		$strUnitScale = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10502");
		if( $this->getLengthCountAsChr() === true ){
			//例外として文字数で
			$strUnitScale = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10503");
		}

		$varMinLen = $this->getMinLength($this->strModeIdOfLastErr);
		$varMaxLen = $this->getMaxLength($this->strModeIdOfLastErr);

		if( $varMinLen !== null ){
			//$ary[] = "最小値:".$varMinLen.$unit;
			$ary[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10504",array($varMinLen,$strUnitScale));
		}

		if($varMaxLen !== null){
			//$ary[] = "最大値:".$varMaxLen.$unit;
			$ary[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10505",array($varMaxLen,$strUnitScale));
		}

		if( 0 < count($ary) ){
			$constraints = "(".implode(",",$ary).")";
		}

		$strDisplayFormat = $this->getDisplayFormat($this->strModeIdOfLastErr);

		$retStrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10506",array($strDisplayFormat,$constraints));
		return $retStrMsgBody;
	}

}

class SingleTextValidator extends TextValidator {

	function __construct($min=0, $max=255, $boolCheckByMbFlag=false){
		global $g;
		parent::__construct($min, $max, $boolCheckByMbFlag, "/^[^\t\r\n]*$/s", $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10601"));
	}

}

class LongUserNameValidator extends TextValidator {
	function __construct($min=0, $max=64, $boolCheckByMbFlag=false){
		parent::__construct($min, $max, $boolCheckByMbFlag=false);
	}
}

class FileNameValidator extends TextValidator {

	function __construct($strFSName="ext4"){
		global $g;
		if( $strFSName="ext4" ){
			$min = 0;
			$max = 255;
			$strRegexpFormat = "/^[^,\"\t\/\r\n]*$/s";
			$strDisplayFormat = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10801");
		}else{
			$min = 0;
			$max = 255;
			$strRegexpFormat = "/^[^,\"\t\/\r\n]*$/s";
			$strDisplayFormat = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10802");
		}
		parent::__construct($min, $max, false, $strRegexpFormat, $strDisplayFormat);
	}

}

class MultiTextValidator extends TextValidator {

	function __construct($min=0, $max=2000, $boolCheckByMbFlag=false){
		global $g;
		parent::__construct($min, $max, $boolCheckByMbFlag, "/^[^\t]*$/s", $g['objMTS']->getSomeMessage("ITAWDCH-ERR-10901"));
	}

}

class DateValidator extends Validator {
	//----第一の制約：関数[checkdate]の制約から、グレゴリオ暦1年1月1日から32767年12月31日までが、入力受付可能範囲、となる。
	//----第二の制約：関数[checkdate]の制約


	protected $strErrAddMsg;

	function __construct($min='1000/01/01 00:00:00', $max='9999/12/31 23:59:59' ,$strRegiFormat='#^\d{4}/\d{1,2}/\d{1,2}$#', $strDisplayFormat="yyyy/mm/dd" ){
		parent::__construct($min, $max, $strRegiFormat, $strDisplayFormat);
		$this->setMinLength(10);
		$this->setMaxLength(10);
		$this->setRegExp('#^\d{4}/\d{1,2}/\d{1,2}\s{1}\d{1,2}:\d{1,2}:\d{1,2}$#',"DTiS_richFilterDefault");//"filter_rich"
		$this->getDisplayFormat("yyyy/mm/dd hh:ii:ss","DTiS_richFilterDefault");//"filter_rich"
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		global $g;
		$retBool = false;
		$strModeId = "";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
				if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
					$strModeId = "DTiS_filterDefault";//filter_table
					if(array_key_exists("RICH_FILTER_TYPE", $aryTcaAction)){
						$strModeId = $aryTcaAction["RICH_FILTER_TYPE"];//'filter_rich';
					}
				}
			}
		}
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;

		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}else{
			if( 0==strlen($value) ){
				$retBool = true;
			}else{
				$strRegexpFormat = $this->getRegExp($strModeId);
				if(preg_match($strRegexpFormat, $value) === 1){
					$d = explode("/",$value);
					if(checkdate(intval($d[1]),intval($d[2]),intval($d[0]))===true){
						//----UNIXTIMESTAMP(経過秒数)[INT型]
						$unixTimeStamp = strtotime($value);
						//UNIXTIMESTAMP(経過秒数)[INT型]----
						
						if( $unixTimeStamp == ".000000") {
							$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11001",$value);
						}else{
							$varMinVal = $this->getMin($strModeId);
							$varMaxVal = $this->getMax($strModeId);
							if(($varMaxVal === null || $unixTimeStamp <= strtotime($varMaxVal))
								&& ($varMinVal === null || $unixTimeStamp >= strtotime($varMinVal))){
								$retBool = true;
							}else{
								$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11002",$value);
							}
						}
					}else{
						$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11003",$value);
					}
				}else{
    				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10104", array($strRegexpFormat)));
                    return false;
				}
			}
			if( $retBool === false ){
				$this->setValidRule($this->makeValidRule());
			}
		}
		return $retBool;
	}

	function makeValidRule(){
		global $g;
		$retStrMsgBody = '';

		$varMinVal = $this->getMin($this->strModeIdOfLastErr);
		$varMaxVal = $this->getMax($this->strModeIdOfLastErr);

		$ary = array();
		$constraints = "";
		if($varMinVal !== null){
			//$ary[] = "最小値:".$varMinVal;
			$ary[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11005",$varMinVal);
		}
		if($varMaxVal !== null){
			//$ary[] = "最大値:".$varMaxVal;
			$ary[] = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11006",$varMaxVal);
		}
		if(count($ary)>0){
			$constraints = "(".implode(",",$ary).")";
		}
		
		$strDisplayFormat = $this->getDisplayFormat($this->strModeIdOfLastErr);
		
		$retStrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11007",array($strDisplayFormat,$constraints,$this->strErrAddMsg));
		return $retStrMsgBody;
	}

}

class DateTimeValidator extends DateValidator {
	//----第一の制約：関数[checkdate]の制約から、グレゴリオ暦1年1月1日から32767年12月31日までが、入力受付可能範囲、となる。
	//----第二の制約：関数[checkdate]の制約

	function __construct($min='1000/01/01 00:00:00.000000', $max='9999/12/31 23:59:59.999999'){
		parent::__construct($min, $max, '#^\d{4}/\d{1,2}/\d{1,2}(\s+\d{1,2}:\d{1,2}(:\d{1,2})?)?$#', "yyyy/mm/dd [hh:ii[:ss]]");
		$this->setMinLength(16);
		$this->setMaxLength(19);
		$this->setRegExp('#^\d{4}/\d{1,2}/\d{1,2}\s{1}\d{1,2}:\d{1,2}:\d{1,2}\.{1}[0-9]{6}$#',"DTiS_richFilterDefault");//filter_rich
		$this->getDisplayFormat("yyyy/mm/dd hh:ii:ss.sssuuu","DTiS_richFilterDefault");//filter_rich
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){

		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		global $g;
		$retBool = false;
		$strModeId = "";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
				if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
					$strModeId = "DTiS_filterDefault";//filter_table
					if(array_key_exists("RICH_FILTER_TYPE", $aryTcaAction)){
						$strModeId = $aryTcaAction["RICH_FILTER_TYPE"];//'filter_rich';
					}
				}
			}
		}
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;
		
		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}else{
			if( 0==strlen($value) ){
				$retBool = true;
			}else{
				$strRegexpFormat = $this->getRegExp($strModeId);
				if( preg_match($strRegexpFormat, $value) === 1 ){
					if( strpos($value," ") === false ){
						$value1 = $value;
						$d = explode("/",$value1);
						$value2 = "00:00:00";
						$t = explode(":",$value2);
					}else{
						$array=explode(" ",$value);
						$value1=$array[0];
						$value2=$array[1];
						$d = explode("/",$value1);
						$t = explode(":",$value2);
						if( count($t) == 2 ){
							//----秒を補完
							$t[2]="00";
							//秒を補完----
                                                }
						if(0<=intval($t[0]) && intval($t[0])< 24){
							//----時刻は正常
							if(0<=intval($t[1]) && intval($t[1])<=59){
								//----分は正常
								$tmpArray = explode(".",$t[2]);
								if(0<=intval($tmpArray[0]) && intval($tmpArray[0])<=59){
									//----秒は正常
									//秒は正常----
								}else{
								        //$this->strErrAddMsg = "(入力値[{$value}]は、PHP関数(strtotime)で正常に処理できる範囲外です。)";
								        $this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11101",$value);
									$boolExeContinue = false;
								}
								//分は正常----
							}else{
								//$this->strErrAddMsg = "(入力値[{$value}]は、PHP関数(strtotime)で正常に処理できる範囲外です。)";
								$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11101",$value);
								$boolExeContinue = false;
							}
							//時刻は正常----
						}else{
							//$this->strErrAddMsg = "(入力値[{$value}]は、PHP関数(strtotime)で正常に処理できる範囲外です。)";
							$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11101",$value);
							$boolExeContinue = false;
						}
					}
					
					if( $boolExeContinue === true ){
						if( checkdate(intval($d[1]), intval($d[2]), intval($d[0])) === true ){
							
							//----UNIXTIMESTAMP(経過秒数)[INT型]
							$unixTimeStamp = convFromStrDateToUnixtime($value,true);
							//UNIXTIMESTAMP(経過秒数)[INT型]----
							
							$varMinVal = $this->getMin($strModeId);
							$varMaxVal = $this->getMax($strModeId);

							if( $unixTimeStamp == ".000000") {
								$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11101",$value);
							}else{
								if( ($varMaxVal === null || bccomp($unixTimeStamp, convFromStrDateToUnixtime($varMaxVal,true),6) != 1) 
									&& ($varMinVal === null || bccomp($unixTimeStamp, convFromStrDateToUnixtime($varMinVal,true),6) != -1) ){
									$retBool = true;
								}else{
									$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11102",$value);
								}
							}
						}else{
							$this->strErrAddMsg = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11103",$value);
						}
					}
				}else{
    				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-10104", array($strRegexpFormat)));
                    return false;
				}
			}
			if( $retBool === false ){
				$this->setValidRule($this->makeValidRule());
			}
		}
		return $retBool;
	}

}

class DelBtnValidator extends Validator{
	protected $arySelectList;
	function __construct(){
		global $g;
		$strDisplayFormat = '';
		parent::__construct(null, null, '', $strDisplayFormat);
		$this->arySelectList = array(
			""=>$g['objMTS']->getSomeMessage("ITAWDCH-ERR-11201"),
			"0"=>$g['objMTS']->getSomeMessage("ITAWDCH-ERR-11202"),
			"1"=>$g['objMTS']->getSomeMessage("ITAWDCH-ERR-11203")
		);
		$this->varMinAsNum = null;
		$this->varMinAsNum = null;
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		global $g;
		$retBool = false;
		$strModeId = "";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
			}
		}
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;

		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}else{
			if( 0==strlen($value) ){
				$retBool = true;
			}else{
				if( array_key_exists($value, $this->arySelectList) === true ){
					$retBool = true;
				}else{
				}
			}
			if( $retBool === false ){
				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11204",implode(",",$this->arySelectList)));
			}
		}
		return $retBool;
	}

}

class LinkRequireValidator extends Validator {
	protected $objOwnerColumn;
	protected $strLinkColumnId;
	protected $aryRequireList;

	function __construct($objOwnerColumn, $aryRequireList, $strLinkColumnId){
		parent::__construct(null, null, "", "");
		$this->objOwnerColumn = $objOwnerColumn;
		$this->aryRequireList = $aryRequireList;
		$this->strLinkColumnId = $strLinkColumnId;
		$this->boolErrShowPrefix = false;
	}
	
	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		//----パラメータ「NOT_NULL」が、setRequired(NULL禁止)と役割が重複して、混乱の原因になっている。廃止の方向で[2015-03-10]
		$retBool = true;		
		$strModeId = "";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
				if( $strModeId=="DTUP_singleRecRegister" || $strModeId=="DTUP_singleRecUpdate" || $strModeId=="DTUP_singleRecDelete" ){
					$tmpCheckArray = array();
					foreach($this->aryRequireList as $val){
						$tmpCheckArray[$val] = 1;
					}
					if( array_key_exists($value, $tmpCheckArray) === true ){
						if( 0 == strlen($arrayRegData[$this->strLinkColumnId]) ){
							$retBool = false;
						}
					}
				}
			}
		}
		if( $retBool === false ){
			$this->setValidRule($this->makeValidRule($value,$strModeId));
		}
		return $retBool;
	}

	function makeValidRule($value,$strModeId){
		global $g;
		$retStrBody = '';
		$objTable = $this->objOwnerColumn->getTable();
		$aryObjColumn = $objTable->getColumns();
		$objLinkColumn = $aryObjColumn[$this->strLinkColumnId];
		$strColumnLabel = $objLinkColumn->getColLabel(true);
		if(is_a($this->objOwnerColumn,'IDColumn')){
			$arrayDispSelectTag = $this->objOwnerColumn->getArrayMasterTableByFormatName($strModeId);
			if($arrayDispSelectTag === null){
				if($this->objOwnerColumn->getMasterTableBodyForInput() != ""){
					$arrayDispSelectTag = $this->objOwnerColumn->getMasterTableArrayForInput();
				}else{
					$arrayDispSelectTag = $this->objOwnerColumn->getMasterTableArrayForFilter();
				}
			}
			if( array_key_exists($value, $arrayDispSelectTag) === true ){
				$strDispBody = $arrayDispSelectTag[$value];
			}else{
				$strDispBody = $this->objOwnerColumn->getErrMsgHead()."(".$value.")".$this->objOwnerColumn->getErrMsgTail();
			}
			$retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11301",array($strColumnLabel,$objLinkColumn->getColLabel(true),$strDispBody));
		}else{
			$strDispBody = $value;
			if(strlen($value)==0){
				$strDispBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11302");
			}
			$retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11303",array($strColumnLabel,$objLinkColumn->getColLabel(true),$strDispBody));
		}
		return $retStrBody;
	}

}

class IDValidator extends Validator{
	//指定のIDがマスタテーブルにあるならOKを返す。

	protected $objInfoBaseColumn; 

	function __construct($objInfoBaseColumn=null){
		$strDisplayFormat = '';
		parent::__construct(null, null, '', $strDisplayFormat);
		
		$this->objInfoBaseColumn = $objInfoBaseColumn;
		
		$this->setRegExp('/^0$|^-?[1-9][0-9]*$/s',"DTiS_filterDefault_Num");
		$this->setRegExp("/^[^\t\r\n]*$/s","DTiS_filterDefault_Chr");
		
	}

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
		//----strNumberForRIを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		$retBool = false;
		$strModeId = "";
		$this->setValidRule("");

		$boolExeContinue = true;

		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}

		if( $boolExeContinue===true ){
			if( is_a($this->objInfoBaseColumn,'IDColumn')===true ){
				//----IDColumnがセットされていた場合
				$retBool = $this->isValidForIDColumn($value, $strNumberForRI, $arrayRegData, $arrayVariant);
				//IDColumnがセットされていた場合----
			}else if( is_a($this->objInfoBaseColumn,'IDRelaySearchColumn')===true ){
				//----IDRelaySearchColumnがセットされていた場合
				$retBool = $this->isValidForRelaySearchColumn($value, $strNumberForRI, $arrayRegData, $arrayVariant);
				//IDRelaySearchColumnがセットされていた場合----
			}
		}
		return $retBool;
	}

	function setInfoBaseColumn(Column $objInfoBaseColumn){
		$this->objInfoBaseColumn = $objInfoBaseColumn;
	}

	function getInfoBaseColumn(){
		return $this->objInfoBaseColumn;
	}

	function isValidForIDColumn($value, $strNumberForRI, $arrayRegData, $arrayVariant){
		global $g;
		$retBool = false;
		$arrayDispSelectTag = array();
		$strMsgMode = "";
		$strDispValue = "";

		$strModeId="";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
			}
		}
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;

		if( strlen($value) == 0 ){
			$boolExeContinue = false;
			if( $this->objInfoBaseColumn->getTempBuffer() === null ){
				//----ブラウザからの場合
				$retBool = true;
				//ブラウザからの場合----
			}else{
				//----ファイルからの場合
				$strDispValue = $this->objInfoBaseColumn->getTempBuffer();
				//
				if( strlen($strDispValue) == 0 ){
					//----ファイルのカラムに空文字が入っていた場合
					$retBool = true;
					//ファイルのカラムに空文字が入っていた場合----
				}else{
					//----ファイルのカラムに文字列の長さ1以上の値が入っていた場合
					$retBool = false;
					//ファイルのカラムに文字列の長さ1以上の値が入っていた場合----
				}
				//ファイルからの場合----
			}
		}

		if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
			if(array_key_exists("RICH_FILTER_TYPE", $aryTcaAction)){
				$strModeId = $aryTcaAction["RICH_FILTER_TYPE"];//'filter_rich';
			}
			$strMsgMode = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11401");
			if($boolExeContinue === true){
				if( $strModeId==="DTiS_richFilterDefault" ){
					//----リッチフィルタは、広く受け入れない
					$arrayDispSelectTag = $this->objInfoBaseColumn->getArrayMasterTableByFormatName($strModeId);
					if( $arrayDispSelectTag === null ){
						if( $strModeId=="DTiS_journalPrint" ){
							$arrayDispSelectTag = $this->objInfoBaseColumn->getMasterTableArrayFromJournalTable();
						}else{
							$arrayDispSelectTag = $this->objInfoBaseColumn->getMasterTableArrayFromMainTable();
						}
					}
					$retBool = array_key_exists($value, $arrayDispSelectTag);
					//リッチフィルタは、広く受け入れない----
				}else{
					//----テキスト検索は、正規表現で規制する
					$strRegexpFormat = $this->getRegExp($strModeId);
					if( strlen($strRegexpFormat)==0 ){
						$minorMode = $this->objInfoBaseColumn->getMasterDisplayColumnType();
						if( $minorMode===0){
							// 表示列が数値型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Num");
						}else{
							// 表示列が文字列型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Chr");
						}
					}
					if( preg_match($strRegexpFormat, $value)===1 ){
						$retBool = true;
					}else{
						$retBool = false;
					}
					//テキスト検索は、正規表現で規制する----
				}
			}
		}else if( $strModeId=="DTUP_singleRecRegister" || $strModeId=="DTUP_singleRecUpdate" || $strModeId=="DTUP_singleRecDelete" ){
			if( $strModeId=="DTUP_singleRecRegister" ){
				//$strMsgMode = "登録";
				$strMsgMode = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11402");
			}else if( $strModeId=="DTUP_singleRecUpdate" ){
				//$strMsgMode = "更新";
				$strMsgMode = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11403");
			}else if( $strModeId=="DTUP_singleRecDelete" ){
				//$strMsgBodyMode01 = "レコードの廃止/復活";
				$modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];
				if( $modeValue_sub=="on" ){
					$strMsgMode = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11406");
				}else if( $modeValue_sub=="off" ){
					$strMsgMode = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11407");
				}
			}
			if($boolExeContinue === true){
				$arrayDispSelectTag = $this->objInfoBaseColumn->getArrayMasterTableByFormatName($strModeId);
				if($arrayDispSelectTag === null){
					if($this->objInfoBaseColumn->getMasterTableBodyForInput() != ""){
						$arrayDispSelectTag = $this->objInfoBaseColumn->getMasterTableArrayForInput();
					}else{
						$arrayDispSelectTag = $this->objInfoBaseColumn->getMasterTableArrayForFilter();
					}
				}
				$retBool =  array_key_exists($value, $arrayDispSelectTag);
			}
		}

		if( $retBool === false ){
			$this->setValidRule($this->makeValidRule($value, $strDispValue, $strMsgMode, $arrayDispSelectTag));
		}
		return $retBool;
	}

	function isValidForRelaySearchColumn($value, $strNumberForRI, $arrayRegData, $arrayVariant){
		$retBool = false;
		$arrayDispSelectTag = array();
		$strMsgMode = "";
		$strDispValue = "";

		$strModeId="";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
			}
		}
		$this->strModeIdOfLastErr = $strModeId;

		$boolExeContinue = true;

		if( $strModeId=="DTiS_recCount" || $strModeId=="DTiS_currentPrint" || $strModeId=="DTiS_journalPrint" ){
			
			if(array_key_exists("RICH_FILTER_TYPE", $aryTcaAction)){
				$strModeId = $aryTcaAction["RICH_FILTER_TYPE"];
			}
			
			if($boolExeContinue === true){
				if( $this->objInfoBaseColumn->getAddSelectTagPrintType()===0 ){
					//----仮想マスタモード
					if( $strModeId==="DTiS_richFilterDefault" ){
						//----リッチフィルタだが、仮想のマスタの鍵キーとなる列の型次第で、正規表現で規制する
						$minorMode = $this->objInfoBaseColumn->getPrimeMasterDisplayColumnType();
						if( $minorMode===0 ){
							// 表示列が数値型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Num");
						}else{
							// 表示列が文字列型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Chr");
						}
						if( preg_match($strRegexpFormat, $value)===1 ){
							$retBool = true;
						}else{
							$retBool = false;
						}
						//リッチフィルタだが、仮想のマスタの鍵キーとなる列の型次第で、正規表現で規制する----
					}else{
						//----テキスト検索は、正規表現で規制する
						$minorMode = $this->objInfoBaseColumn->getPrimeMasterDisplayColumnType();
						if( $minorMode===0 ){
							// 表示列が数値型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Num");
						}else{
							// 表示列が文字列型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Chr");
						}

						if( preg_match($strRegexpFormat, $value)===1 ){
							$retBool = true;
						}else{
							$retBool = false;
						}
						//テキスト検索は、正規表現で規制する----
					}
					//仮想マスタモード----
				}else{
					//----通常マスタモード
					if( $strModeId==="DTiS_richFilterDefault" ){
						//----リッチフィルタは、広く受け入れない
						$arrayDispSelectTag = $this->objInfoBaseColumn->getPrimeMasterTableArray();
						$retBool = array_key_exists($value, $arrayDispSelectTag);
						//リッチフィルタは、広く受け入れない----
					}else{
						//----テキスト検索は、正規表現で規制する
						$minorMode = $this->objInfoBaseColumn->getPrimeMasterDisplayColumnType();
						if( $minorMode===0 ){
							// 表示列が数値型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Num");
						}else{
							// 表示列が文字列型の場合
							$strRegexpFormat = $this->getRegExp("DTiS_filterDefault_Chr");
						}

						if( preg_match($strRegexpFormat, $value)===1 ){
							$retBool = true;
						}else{
							$retBool = false;
						}
						//テキスト検索は、正規表現で規制する----
					}
					//通常マスタモード----
				}
			}
		}

		if( $retBool===false ){
			$this->setValidRule($this->makeValidRule($value, $strDispValue, $strMsgMode, $arrayDispSelectTag));
		}
		return $retBool;
	}

	function makeValidRule($value, $strDispValue, $strMsgMode, $arrayDispSelectTag){
		global $g;
		$retStrMsgBody = '';
		if( $strDispValue == "" ){
			//----ファイルからではない場合
			if( array_key_exists($value, $arrayDispSelectTag) === true ){
				$strDispValue = $arrayDispSelectTag[$value];
			}
			//ファイルからではない場合----
		}
		if( $strDispValue != "" ){
			$retStrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11404");
		}else{
			$retStrMsgBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11405");
		}
		return $retStrMsgBody;
	}

}

class EditLockValidator extends Validator {
	protected $objColumn;
	protected $objTable;

	protected $strEditTableBody;
	protected $strEditTableRIColumnId;
	protected $strEditTableLockTgtColumnId;
	protected $strEditTableEditStatusColumnId;
	protected $strEditTableApplyUserColumnId;
	protected $strEditTableDisuseFlagColumnId;

	protected $strResultTableBody;
	protected $strResultTableLockTgtColumnId;
	protected $strResultTableDisuseFlagColumnId;

	protected $strErrMsgValue;
	
	protected $strPageType;

	function __construct(EditStatusControlIDColumn $objColumn, TemplateTableForReview $objTable){
		parent::__construct(null, null, "", "");
		
		$this->setErrShowPrefix(false);
		
		$this->objColumn = $objColumn;
		$this->strEditTableEditStatusColumnId = $objColumn->getID();
		
		$this->objTable = $objTable;
		
		// 編集テーブル
		$this->strEditTableBody = $objTable->getDBMainTableBody();
		$this->strEditTableRIColumnId = $objTable->getRIColumnID();
		$this->strEditTableLockTgtColumnId = $objTable->getLockTargetColumnID();
		$this->strEditTableApplyUserColumnId = $objTable->getApplyUserColumnID();
		$this->strEditTableDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID();
		
		// 結果テーブル
		$this->strResultTableBody = $objTable->getDBResultTableBody();
		$this->strResultTableLockTgtColumnId = $objTable->getLockTargetColumnID();
		$this->strResultTableDisuseFlagColumnId = $objTable->getRequiredDisuseColumnID();
		
		$this->setPageType($objTable->getPageType());
		
		//----廃止復活時にも、バリデーションチェックを走らせるためのフラグをON
	}

	function setPageType($pageType){
		$this->strPageType = $pageType;
	}

	function getPageType(){
		return $this->strPageType;
	}

	function isValid($value, $strNumberForRIOnEditTable=null, $arrayRegData=null, &$arrayVariant=array()){
		global $g;
		
		$intControlDebugLevel01=250;
		$intControlDebugLevel02=250;
		
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		
		$retBool = true;
		$strModeId="";
		if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
			if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
				$aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
				$strModeId = $aryTcaAction["ACTION_MODE"];
			}
		}
		$this->setValidRule("");
		$this->strModeIdOfLastErr = $strModeId;

		$strPageType = $this->getPageType();

		$boolExeContinue = true;

		if( $this->checkBasicValid($value) == false ){
			//----NULLバイトやコントロール文字が入っていた場合
			$boolExeContinue = false;
			//NULLバイトやコントロール文字が入っていた場合----
		}

		if( $boolExeContinue === true ){
			if( $strModeId=="DTiS_RecCount" || $strModeId=="DTiS_CurrentPrint" || $strModeId=="DTiS_JournalPrint" ){
				$strMsgBodyMode01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11501");
				$arrayDispSelectTag = $this->objColumn->getArrayMasterTableByFormatName($strModeId);
				if($arrayDispSelectTag===null){
					$boolKeyCheck = array_key_exists($value, $this->objColumn->getMasterTableArrayForFilter());
				}else{
					$boolKeyCheck = array_key_exists($value, $arrayDispSelectTag);
				}
				
				if($boolKeyCheck === false){
					$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11502",$value));
				}
				
				$retBool = $boolKeyCheck;
				$boolExeContinue = false;
				
			}else if( $strModeId=="DTUP_singleRecRegister" || $strModeId=="DTUP_singleRecUpdate" || $strModeId=="DTUP_singleRecDelete" ){
				if( $strModeId=="DTUP_singleRecRegister" ){
				}else{
					if( $strModeId=="DTUP_singleRecUpdate" ){
					}else if( $strModeId=="DTUP_singleRecDelete" ){
						$modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];
					}
					$editTgtRow = $arrayVariant['edit_target_row'];
					if($value == ""){
						$value = $editTgtRow[$this->objColumn->getID()];
					}
				}
				$strMsgBodyMode01 = $arrayVariant['action_sub_order']['actionNameOnUI'];
				
				$arrayDispSelectTag = $this->objColumn->getArrayMasterTableByFormatName($strModeId);
				if($arrayDispSelectTag===null){
					$boolKeyCheck = array_key_exists($value, $this->objColumn->getMasterTableArrayForFilter());
				}else{
					$boolKeyCheck = array_key_exists($value, $arrayDispSelectTag);
				}
				
				if($boolKeyCheck === false){
					$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11505",$value));
					$retBool = $boolKeyCheck;
					$boolExeContinue = false;
				}else{
					dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				}
				
			}else{
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
			}
		}

		if($boolExeContinue === true){
			dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
			
			$arrayBaseSelect = $this->objColumn->getMasterTableArrayForFilter();
			
			$arrayObjColumn = $this->objTable->getColumns();
			
			//----ロック対象のカラム
			$objREBFColumn             = $arrayObjColumn[$this->objTable->getRequiredRowEditByFileColumnID()];
			$objRIColumn               = $arrayObjColumn[$this->strEditTableRIColumnId];
			$objEditTableLockTgtColumn = $arrayObjColumn[$this->strEditTableLockTgtColumnId];
			//ロック対象のカラム----
			
			if($strPageType=="apply"){
				//----申請者用ページの場合
				
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				
				if( $strModeId=="DTUP_singleRecRegister" || $strModeId=="DTUP_singleRecUpdate" ){
					//----新規登録または更新の場合
					
					$retBool = false;
					
					//----編集ステータスの値キーが渡されなかった場合はエラー
					if(array_key_exists($this->strEditTableEditStatusColumnId,$arrayRegData)===true){
					}else{
						//$this->strErrMsgValue = "{$arrayObjColumn[$this->strEditTableEditStatusColumnId]->getColLabel()}の入力がありません。";
						$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11506",$arrayObjColumn[$this->strEditTableEditStatusColumnId]->getColLabel()));
						
						$boolExeContinue = false;
						$retBool = false;
					}
					//編集ステータスの値キーが渡されなかった場合はエラー----
					
					if( $strModeId=="DTUP_singleRecRegister" ){
						dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
						
						//----結果テーブルのRI値（予定値）のチェック
						if(array_key_exists($this->strEditTableLockTgtColumnId, $arrayRegData)===true){
							$strNumberForRIOnResultTable = $arrayRegData[$this->strEditTableLockTgtColumnId];
							
							$objMultiValidator = $objEditTableLockTgtColumn->getValidator();
							if($objMultiValidator->isValid($strNumberForRIOnResultTable, $strNumberForRIOnEditTable, $arrayRegData, $arrayVariant)===false){
								$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11507",$objEditTableLockTgtColumn->getColLabel()));
								$boolExeContinue = false;
								$retBool = false;
							}else{
								if(array_key_exists("TABLE_IUD_SOURCE",$arrayVariant)===true){
									if( $arrayVariant["TABLE_IUD_SOURCE"]=="queryMaterialFile" ){
										if( strlen($strNumberForRIOnResultTable)==0 ){
											if( $objREBFColumn->getFocusEditType()==$this->objTable->getActionNameOfApplyRegistrationForUpdate() ){
												//----QMファイルでのレコード登録（修正申請）で、免許番号が長さ0の場合はエラーにする
												//｛｝の入力がありません
												$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11508",$arrayObjColumn[$this->strEditTableLockTgtColumnId]->getColLabel()));
												
												$boolExeContinue = false;
												$retBool = false;
												//QMファイルでのレコード登録（修正申請）で、免許番号が長さ0の場合はエラーにする----
											}
										}else{
											if( $objREBFColumn->getFocusEditType()==$this->objTable->getActionNameOfApplyRegistrationForNew() ){
												//----QMファイルでのレコード登録（新規申請）で、免許番号が長さ1以上の場合はエラーにする
												//｛｝の入力はできません
												$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11511",$arrayObjColumn[$this->strEditTableLockTgtColumnId]->getColLabel()));
												
												$boolExeContinue = false;
												$retBool = false;
												//QMファイルでのレコード登録（新規申請）で、免許番号が長さ1以上の場合はエラーにする----
											}
										}
									}
								}
							}
						}else{
							$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11508",$arrayObjColumn[$this->strEditTableLockTgtColumnId]->getColLabel()));
							
							$boolExeContinue = false;
							$retBool = false;
						}
						//結果テーブルのRI値（予定値）のチェック----
						
						//----編集ステータスが「編集中」と指定されなかった場合はエラー
						$intEditStatusValue = $arrayRegData[$this->strEditTableEditStatusColumnId];
						if( $intEditStatusValue==1 ){
							//----「編集中」が指定された
							//「編集中」が指定された----
						}else{
							//----「編集中」が指定されなかった
							$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11509"));
							
							$boolExeContinue = false;
							$retBool = false;
							//「編集中」が指定されなかった----
						}
						//編集ステータスが「編集中」と指定されなかった場合はエラー----
						
						dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
					}else if( $strModeId=="DTUP_singleRecUpdate" ){
						dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
						
						//----免許番号が変更されていないことを確認できなかったらエラー
						if(array_key_exists($this->strEditTableLockTgtColumnId, $editTgtRow)===true){
							$strNumberForRIOnResultTable = $editTgtRow[$this->strEditTableLockTgtColumnId];
							if(array_key_exists($this->strEditTableLockTgtColumnId, $arrayRegData)===true){
								//----エクセルなどの場合を想定
								if($strNumberForRIOnResultTable != $arrayRegData[$this->strEditTableLockTgtColumnId]){
									$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11515",$arrayObjColumn[$this->strEditTableLockTgtColumnId]->getColLabel()));
									$boolExeContinue = false;
									$retBool = false;
								}
								//エクセルなどの場合を想定----
							}
						}else{
							$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11516",$arrayObjColumn[$this->strEditTableLockTgtColumnId]->getColLabel()));
							
							$boolExeContinue = false;
							$retBool = false;
						}
						//免許番号が変更されていないことを確認できなかったらエラー----
						dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
					}
					
					if($boolExeContinue === true){
						
						if( $strModeId=="DTUP_singleRecRegister" ){
							dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
							
							$strMsgBodyMode01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11510");
							
							if( 0==strlen($strNumberForRIOnResultTable) ){
								//----あらたに承認者に番号を払いださせたい場合
								
								//----以降の処理は不要
								$boolExeContinue = false;
								//以降の処理は不要----
								
								$retBool = true;
								
								//あらたに承認者に番号を払いださせたい場合----
							}else{
								//----承認者がすでに払い出した番号のデータを更新する申請をしたい場合
								dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
								
								//----払出承認番号を保存するテーブルを調べる
								$arrayResult = $this->checkExistLockTargetInResultTable($strNumberForRIOnResultTable,$arrayVariant);
								$boolExeContinue = $arrayResult[0];
								if($boolExeContinue === false){
									$retBool = $arrayResult[1];
								}
								//払出承認番号を保存するテーブルを調べる----
								
								if($boolExeContinue === true){
									//----申請データ保存テーブルに、編集したいLockNoを対象とする、編集中または申請中の行があるか？
									$arrayTempRet = $this->checkExistLockTargetInEditTable($strNumberForRIOnResultTable);
									$dlcCounter1 = $arrayTempRet[0];
									$arrayRow = $arrayTempRet[1];
									//申請データ保存テーブルに、編集したいLockNoを対象とする、編集中または申請中の行があるか？----
									
									if($dlcCounter1 == 0){
										//----1行も存在していない
										$boolExeContinue = false;
										$retBool = true;
										//1行も存在していない----
									}else if(1 < $dlcCounter1){
										//----複数行存在している
										$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11512"));
										$boolExeContinue = false;
										$retBool = false;
										//複数行存在している----
									}else{
										//----1行発見
										$boolExeContinue = false;
										$retBool = false;
										
										$aryTmpMsg = array(
											$objEditTableLockTgtColumn->getColLabel(),
											$strNumberForRIOnResultTable,
											$objRIColumn->getColLabel(),
											$arrayRow[0][$this->strEditTableRIColumnId],
											$arrayBaseSelect[$arrayRow[0][$this->objColumn->getID()]],
											$strMsgBodyMode01
										);
										$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11513",$aryTmpMsg));
										unset($aryTmpMsg);
										
										//1行発見----
									}
								}
								dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
								//承認者がすでに払い出した番号のデータを更新する申請をしたい場合----
							}
							
							dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
						}else if( $strModeId=="DTUP_singleRecUpdate" ){
							dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
							
							$strMsgBodyMode01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11514");
							
							//----更新対象のレコードの、現在の編集ステータスの値がレコード更新をしていいものかをチェックする
							if(array_key_exists($this->strEditTableEditStatusColumnId, $editTgtRow)===true){
								$intNowEditStatus = $editTgtRow[$this->strEditTableEditStatusColumnId];
								if($intNowEditStatus == 1 || $intNowEditStatus == 2){
									//----現在レコードの最終更新者と、アクセスしてきたユーザのIDが同じかどうかをチェックする
									if( $editTgtRow[$this->strEditTableApplyUserColumnId]===$g['login_id'] ){
									}else{
										$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11529"));
										$boolExeContinue = false;
										$retBool = false;
									}
									//現在レコードの最終更新者と、アクセスしてきたユーザのIDが同じかどうかをチェックする----
								}else{
									$aryTmpMsg = array(
										$objRIColumn->getColLabel(),
										$editTgtRow[$objRIColumn->getID()],
										$this->objColumn->getColLabel(),
										$this->objTable->getStatusNameOnEdit(),
										$this->objTable->getStatusNameOfWaitForAccept()
									);
									$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11517",$aryTmpMsg));
									unset($aryTmpMsg);
									
									$boolExeContinue = false;
									$retBool = false;
								}
							}else{
								$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11518",$arrayObjColumn[$this->strEditTableEditStatusColumnId]->getColLabel()));
								
								$boolExeContinue = false;
								$retBool = false;
							}
							//更新対象のレコードの、現在の編集ステータスの値がレコード更新をしていいものかをチェックする----
							
							if($boolExeContinue === true){
								if( 0==strlen($strNumberForRIOnResultTable) ){
									//----あらたに承認者に番号を払いださせたい場合
									
									//----以降の処理は不要
									$boolExeContinue = false;
									//以降の処理は不要----
									//
									$retBool = true;
									
									//あらたに承認者に番号を払いださせたい場合----
								}else{
									//----承認者がすでに払い出した番号のデータを更新する申請をしたい場合
									dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
									list($retBool,$boolExeContinue) = $this->checkExistRecordByLockTargetNoForUpdate($strNumberForRIOnEditTable,$strNumberForRIOnResultTable,$arrayVariant,$strMsgBodyMode01);
									//承認者がすでに払い出した番号のデータを更新する申請をしたい場合----
								}
							}
							
						}
						
					}
					
					//新規登録または更新の場合----
				}else{
					//----新規登録または更新ではない場合(廃止または復活など)
					//新規登録または更新ではない場合(廃止または復活など)----
				}
				
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				//申請者用ページの場合----
			}else if($strPageType=="confirm"){
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
				
				if( $strModeId=="DTUP_singleRecRegister" ){
					$strMsgBodyMode01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11521");
					
					$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11522",$strMsgBodyMode01));
					
					$boolExeContinue = false;
					$retBool = false;
				}else if( $strModeId=="DTUP_singleRecUpdate" ){
					$strMsgBodyMode01 = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11523");
					
					$editTgtRow = $arrayVariant['edit_target_row'];
					
					if($boolExeContinue === true){
						if(array_key_exists($this->strEditTableLockTgtColumnId, $editTgtRow)===true){
							$strNumberForRIOnResultTable = $editTgtRow[$this->strEditTableLockTgtColumnId];
						}else{
							$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11524",$arrayObjColumn[$this->strEditTableLockTgtColumnId]->getColLabel()));
							
							$boolExeContinue = false;
							$retBool = false;
						}
					}
					
					if(array_key_exists($this->strEditTableEditStatusColumnId, $editTgtRow)===true){
						$intNowEditStatus = $editTgtRow[$this->strEditTableEditStatusColumnId];
						if($intNowEditStatus == 2 ){
							//----申請中の行である場合
							
							if( 0==strlen($strNumberForRIOnResultTable) ){
								//----あらたに承認者に番号を払いださせるための申請の場合
								//あらたに承認者に番号を払いださせるための申請の場合----
							}else{
								//----すでに承認者した番号を更新するための申請の場合
								list($retBool,$boolExeContinue) = $this->checkExistRecordByLockTargetNoForUpdate($strNumberForRIOnEditTable,$strNumberForRIOnResultTable,$arrayVariant,$strMsgBodyMode01);
								//すでに承認者した番号を更新するための申請の場合----
							}
							//
							//申請中の行である場合----
						}else{
							//----申請中ではない場合
							
							$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11525"));
							
							$boolExeContinue = false;
							$retBool = false;
							
							//----申請中ではない場合
						}
					}else{
						$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11526",$this->objTable->getStatusNameOfWaitForAccept()));
						
						$boolExeContinue = false;
						$retBool = false;
					}
					
				}
				
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
			}else{
				dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
			}
			
			//----廃止/復活の場合
			if( $strModeId=="DTUP_singleRecDelete" ){
				//----復活の場合のみ、チェックする
				//---ステータスが編集中または申請中のLockNoが払い出されたレコードを、復活する場合
				if( $modeValue_sub=="off" ){
					// 復活オーダーである
					$intNowEditStatus = $editTgtRow[$this->strEditTableEditStatusColumnId];
					if( $intNowEditStatus==1 || $intNowEditStatus==2 ) {
						// 編集中 または 申請中である
						$strNumberForRIOnResultTable = $editTgtRow[$this->strEditTableLockTgtColumnId];
						if( 0 < strlen($strNumberForRIOnResultTable) ){
							// 承認済レコードである
							list($retBool,$boolExeContinue) = $this->checkExistRecordByLockTargetNoForUpdate($strNumberForRIOnEditTable,$strNumberForRIOnResultTable,$arrayVariant,$strMsgBodyMode01);
						}
					}
				}
				//ステータスが編集中または申請中のLockNoが払い出されたレコードを、復活する場合----
				//復活の場合のみ、チェックする----
			}
			//廃止/復活の場合----
		}else{
			dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-5",array($strFxName,__FILE__,__LINE__)),$intControlDebugLevel01);
		}
		
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		
		return $retBool;
	}

	function checkExistRecordByLockTargetNoForUpdate($strNumberForRIOnEditTable,$strNumberForRIOnResultTable,$arrayVariant,$strMsgBodyMode01){
		global $g;
		
		//----承認テーブルに該当承認番号の(活性中)レコードがあり、かつ、
		//----編集テーブルに該当承認番号の(活性中)レコード（指定値($strNumberForRIOnEditTable)ではない(編集中/申請中)レコード）がないこと、を確認
		
		$intControlDebugLevel01=250;
		$intControlDebugLevel02=250;
		
        $strFxName = __FUNCTION__;
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-3",array(__FILE__,$strFxName)),$intControlDebugLevel01);

		$arrayObjColumn = $this->objTable->getColumns();
		$arrayBaseSelect = $this->objColumn->getMasterTableArrayForFilter();

		$objEditTableLockTgtColumn = $arrayObjColumn[$this->strEditTableLockTgtColumnId];
		$objRIColumn               = $arrayObjColumn[$this->strEditTableRIColumnId];
		$objEditTableLockTgtColumn = $arrayObjColumn[$this->strEditTableLockTgtColumnId];

		$aryRet = array();
		$retBool = null;
		$boolExeContinue = null;

		//----払出承認番号を保存するテーブルを調べる
		$arrayResult = $this->checkExistLockTargetInResultTable($strNumberForRIOnResultTable,$arrayVariant);
		$boolExeContinue = $arrayResult[0];
		if($boolExeContinue === false){
			$retBool = $arrayResult[1];
		}
		//払出承認番号を保存するテーブルを調べる----
		
		if($boolExeContinue === true){
			//----申請データ保存テーブルに、編集したいLockNoを対象とする、編集中または申請中の行があるか？
			$arrayTempRet = $this->checkExistLockTargetInEditTable($strNumberForRIOnResultTable);
			$dlcCounter1 = $arrayTempRet[0];
			$arrayRow = $arrayTempRet[1];
			//申請データ保存テーブルに、編集したいLockNoを対象とする、編集中または申請中の行があるか？----
		
			if($dlcCounter1 == 0){
				//----1行も存在していない
				$boolExeContinue = false;
				$retBool = true;
				//1行も存在していない----
			}else if(1 < $dlcCounter1){
				//----複数行存在している
				
				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11519"));
				
				$boolExeContinue = false;
				$retBool = false;
				//複数行存在している----
			}else{
				//----1行発見
				if($arrayRow[0][$this->strEditTableRIColumnId]==$strNumberForRIOnEditTable){
					//----DBに入っている主キーと同じ
					$boolExeContinue = false;
					$retBool = true;
					//DBに入っている主キーと同じ----
				}else{
					$aryTmpMsg = array(
						$objEditTableLockTgtColumn->getColLabel(),
						$strNumberForRIOnResultTable,
						$objRIColumn->getColLabel(),
						$arrayRow[0][$this->strEditTableRIColumnId],
						$arrayBaseSelect[$arrayRow[0][$this->objColumn->getID()]],
						$strMsgBodyMode01
					);
					$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11520",$aryTmpMsg));
					unset($aryTmpMsg);
					
					$boolExeContinue = false;
					$retBool = false;
				}
				//1行発見----
			}
		}
		$aryRet[0] = $retBool;
		$aryRet[1] = $boolExeContinue;
		
        dev_log($g['objMTS']->getSomeMessage("ITAWDCH-STD-4",array(__FILE__,$strFxName)),$intControlDebugLevel01);
		
		return $aryRet;
	}

	//----編集テーブルからの調査	
	function checkExistLockTargetInEditTable($strNumberForRIOnResultTable){
		//----申請データ保存テーブルに、編集したいLockNoを対象とする、編集中または申請中の行があるか？
		global $g;
		
		$arrayRet = array();
		
		//----編集用テーブル
		$sqlBody2 = "SELECT "
				   ."    {$this->strEditTableRIColumnId} "
				   ."    ,{$this->strEditTableEditStatusColumnId} "
				   ."FROM "
				   ."    {$this->strEditTableBody} "
				   ."WHERE "
				   ."    {$this->strEditTableLockTgtColumnId} = :{$this->strEditTableLockTgtColumnId} "
				   ."    AND "
				   ."    {$this->strEditTableEditStatusColumnId} IN (1,2) "
				   ."    AND "
				   ."    {$this->strEditTableDisuseFlagColumnId} IN ('0') ";
		//編集用テーブル----
		
		$objQuery = $g['objDBCA']->sqlPrepare($sqlBody2);
		$arrayElement = array(
			$this->strEditTableLockTgtColumnId=>$strNumberForRIOnResultTable
		);
		
		$objQuery->sqlBind($arrayElement);
		$r = $objQuery->sqlExecute();
		
		$arrayRow=array();
		$dlcCounter1 = 0;
		
		while ( $row =  $objQuery->resultFetch() ){
			// ----ここから結果データ作成
			
			$dlcCounter1 += 1;
			$arrayRow[$dlcCounter1 - 1] = $row;
			
			// ここまで結果データ作成----
		}
		
		unset($objQuery);
		
		$arrayRet[0] = $dlcCounter1;
		$arrayRet[1] = $arrayRow;
		
		return $arrayRet;
		
		//申請データ保存テーブルに、編集したいLockNoを対象とする、編集中または申請中の行があるか？----
	}
	//編集テーブルからの調査----

	//----結果テーブルからの調査
	function checkExistLockTargetInResultTable($strNumberForRIOnResultTable,&$arrayVariant){
		//----廃止されていないことが条件
		global $g;
		
		$arrayResult = array();
		$boolExeContinue = true;
		$retBool = null;
		
		list($tmpBoolCheckSkip,$tmpKeyExists)=isSetInArrayNestThenAssign($arrayVariant,array("action_sub_order","checkExistLockTargetSkip"));
		if($tmpBoolCheckSkip!==true){
			
			$arrayObjColumn = $this->objTable->getColumns();
			
			$sqlBody1 = "SELECT "
			           ."    {$this->strEditTableRIColumnId} "
			           ."    ,{$this->strResultTableLockTgtColumnId} "
			           ."FROM "
			           ."    {$this->strResultTableBody} "
			           ."WHERE "
			           ."    {$this->strResultTableLockTgtColumnId} = :{$this->strResultTableLockTgtColumnId} "
			           ."    AND "
			           ."    {$this->strResultTableDisuseFlagColumnId} IN ('0') ";
			//廃止されていないことが条件----
			
			$objQuery = $g['objDBCA']->sqlPrepare($sqlBody1);
			$arrayElement = array(
				$this->strEditTableLockTgtColumnId=>$strNumberForRIOnResultTable
			);
			$objQuery->sqlBind($arrayElement);
			$r = $objQuery->sqlExecute();
			
			$arrayRow=array();
			$dlcCounter1 = 0;
			
			while ( $row = $objQuery->resultFetch() ){
				// ----ここから結果データ作成
				
				$dlcCounter1 += 1;
				$arrayRow[$dlcCounter1 - 1] = $row;
				
				// ここまで結果データ作成----
			}
			
			unset($objQuery);
			
			if($dlcCounter1 == 0){
				//----1行も存在していない
				
				$aryTmpMsg = array(
					$arrayObjColumn[$this->strResultTableLockTgtColumnId]->getColLabel(),
					$strNumberForRIOnResultTable
				);
				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11527",$aryTmpMsg));
				unset($aryTmpMsg);
				
				$boolExeContinue = false;
				$retBool = false;
				
				//1行も存在していない----
			}else if(1 < $dlcCounter1){
				//----複数行存在している
				
				$aryTmpMsg = array(
					$arrayObjColumn[$this->strResultTableLockTgtColumnId]->getColLabel(),
					$strNumberForRIOnResultTable
				);
				$this->setValidRule($g['objMTS']->getSomeMessage("ITAWDCH-ERR-11528",$aryTmpMsg));
				$boolExeContinue = false;
				$retBool = false;
				
				//複数行存在している----
			}else{
				//----1行発見
				
				//1行発見----
			}
		}
		$arrayResult[0] = $boolExeContinue;
		$arrayResult[1] = $retBool;
		return $arrayResult;
	}
	//結果テーブルからの調査----

}

class RequiredForConfirmValidator extends Validator {

    protected $objTable;

    function __construct(TemplateTableForReview $objTable){
        $this->objTable = $objTable;
        $this->setErrShowPrefix(false);
    }

	function isValid($value, $strNumberForRI=null, $arrayRegData=null, &$arrayVariant=array()){
        global $g;
        $retBool = true;
        $retStrBody = '';

        $strModeId = "";
        $modeValue_sub = "";

        $query = "";

        if(array_key_exists("TCA_PRESERVED", $arrayVariant)){
            if(array_key_exists("TCA_ACTION", $arrayVariant["TCA_PRESERVED"])){
                $aryTcaAction = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"];
                $strModeId = $aryTcaAction["ACTION_MODE"];
            }
        }

        if( $strModeId != "" ){
            //----更新前のレコード内容（登録時は空配列）
            $editTgtRow = $arrayVariant['edit_target_row'];
            //更新前のレコード内容（登録時は空配列）----

            $objTable = $this->objTable;
            $tmpAryColumn = $objTable->getColumns();

            //----ページタイプ
            $strPageType = $objTable->getPageType();
            //ページタイプ----

            if($strModeId == "DTUP_singleRecRegister" ){
            
            }else if($strModeId == "DTUP_singleRecUpdate"){
                if( array_key_exists($objTable->getEditStatusColumnID(), $arrayRegData)===true ){
                    if( "3" == $arrayRegData[$objTable->getEditStatusColumnID()] ){
                        //----承認する場合
                        
                        $arrayColumnName = array();
                        $arrayNameNullForbidden = array();
                        foreach($tmpAryColumn as $objFocusColumn){
                            if( $objFocusColumn->isRequired() && $objFocusColumn->isUpdateRequireExcept()!==true ){
                                $arrayNameNullForbidden[] = $objFocusColumn->getID();
                            }
                        }
                        foreach($arrayNameNullForbidden as $strValue){
                            $boolCheck = false;
                            //----送信されてきているか？
                            if(array_key_exists($strValue,$arrayRegData)===false){
                                $boolCheck = true;
                            }else if(strlen($arrayRegData[$strValue])===0){
                                $boolCheck = true;
                            }
                            //送信されてきているか？----
                            if($boolCheck === true){
                                //----送信されてきていないが、すでにレコードの中に値は存在しているか？
                                $boolInputNull = false;
                                if(array_key_exists($strValue,$editTgtRow)===false){
                                    $boolInputNull = true;
                                }else if(strlen($editTgtRow[$strValue])===0){
                                    $boolInputNull = true;
                                }
                                if($boolInputNull===true){
                                    $objClientValidator->setErrShowPrefix(false);
                                    $arrayColumnName[] = "(".str_replace(array("<br>","<br/>","<br />"),"・",$tmpAryColumn[$strValue]->getColLabel(true)).")";
                                }
                                //送信されてきていないが、すでにレコードの中に値は存在しているか？----
                            }
                        }
                        if(0 < count($arrayColumnName)){
                            //$retStrBody = "以下の項目は承認時には必須です。\n";
                            $retStrBody = $g['objMTS']->getSomeMessage("ITAWDCH-ERR-11601");
                            $retStrBody.= "[".implode(",",$arrayColumnName)."]\n";
                            $retBool = false;
                        }
                        //承認する場合----
                    }
                }
            }else if( $strModeId=="DTUP_singleRecDelete" ){
                $modeValue_sub = $arrayVariant["TCA_PRESERVED"]["TCA_ACTION"]["ACTION_SUB_MODE"];//['mode_sub'];("on"/"off")
            }
        }
        if($retBool===false){
            $objClientValidator->setValidRule($retStrBody);
        }
        return $retBool;
    }
}

class MultiValidator {

	protected $validators;
	protected $lastErrors;
	protected $errShowPrefixs;
	protected $intValidatorLength;

	function __construct(){
		$this->validators = array();
		$this->lastErrors = array();
		$this->errShowPrefixs = array();
		$this->intValidatorLength = 0;
	}

	function addValidator(Validator $validator){
		$this->validators[] = $validator;
		$this->intValidatorLength += 1;
	}

	function getAllValidator(){
		return $this->validators;
	}

	function isValid(&$value, &$wkpk, &$arrayRegData=null, &$refArrayVariant=array()){
		//----wkpkを使っているのは、MultiValidator/UniqueValidatorのみ[2014-09-08-1255時点]
		$flag = true;
		$this->lastErrors = array();
		$this->errShowPrefixs = array();
		foreach($this->validators as $validator){
			if($validator->isValid($value, $wkpk, $arrayRegData, $refArrayVariant) === false){
				$flag = false;
				
				$this->lastErrors[] = $validator->getValidRule();
				$this->errShowPrefixs[] = $validator->getErrShowPrefix();
			}
		}
		return $flag;
	}

	function getMax(){
		//持ってるvalidatorのなかで一番小さいMaxを採用する
		$max = 99999999999999;
		foreach($this->validators as $validator){
			if($validator->getMax() != null && $max > $validator->getMax()){
				$max = $validator->getMax();
			}
		}
		return $max;
	}

	function getMin(){
		//持ってるvalidatorのなかで一番大きいMinを採用する
		$min = -99999999999999;
		foreach($this->validators as $validator){
			if($validator->getMin() != null && $min < $validator->getMin()){
				$min = $validator->getMin();
			}
		}
		return $min;
	}

	function getMaxLength($strModeId){
		//持ってるvalidatorのなかで一番小さいMaxLengthを採用する
		$max = 99999999999999;
		foreach($this->validators as $validator){
			if($validator->getMaxLength($strModeId) != null && $max > $validator->getMaxLength($strModeId)){
				$max = $validator->getMaxLength($strModeId);
			}
		}
		return $max;
	}

	function getMinLength(){
		//持ってるvalidatorのなかで一番大きいMinを採用する
		$min = -99999999999999;
		foreach($this->validators as $validator){
			if($validator->getMinLength() != null && $min < $validator->getMinLength()){
				$min = $validator->getMinLength();
			}
		}
		return $min;
	}

	function getValidRule(){
		return $this->lastErrors;
	}

	function getShowPrefixs(){
		return $this->errShowPrefixs;
	}

	function getValidatorLength(){
		return $this->intValidatorLength;
	}

}

?>
