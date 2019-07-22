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
class RowData {

	private $aryKeystrColIdValstrColVal;  // as array of string その行のデータ
	private $aryKeyvarAnyValstrCssClassName;  //as string tr自体のclass　今は廃止フラグ用

	function __construct($aryKeystrColIdValstrColVal, $strDisuseFlagColId="DISUSE_FLAG"){
		$this->aryKeystrColIdValstrColVal = $aryKeystrColIdValstrColVal;
		$aryKeyvarAnyValstrCssClassName = array();
		if( $this->aryKeystrColIdValstrColVal[$strDisuseFlagColId] === "1" ){
			$this->aryKeyvarAnyValstrCssClassName[] = "disuse";
		}
	}

	function addRowClass($strCssClassName){
		$this->aryKeyvarAnyValstrCssClassName[] = $strCssClassName;
	}

	function getRowClasses(){
		return $this->aryKeyvarAnyValstrCssClassName;
	}

	function getRowData(){
		return $this->aryKeystrColIdValstrColVal;
	}

}

?>