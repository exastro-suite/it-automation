<?php
//   Copyright 2022 NEC Corporation
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
////ja_JP_UTF-8_ITATERRAFORMCLI_ERR
$ary["ITATERRAFORMCLI-ERR-1"]          = "WARNING:ILLEGAL_ACCESS(Insufficient Privilege).";
$ary["ITATERRAFORMCLI-ERR-101"]        = "RESULT:UNEXPECTED_ERROR(QUERY_NOT_FOUND";
$ary["ITATERRAFORMCLI-ERR-401"]        = "WARNING:NO_QUERY_EXIST[{}]";
$ary["ITATERRAFORMCLI-ERR-402"]        = "ERROR:QUERY_IS_NOT_INTEGER[{}]";
$ary["ITATERRAFORMCLI-ERR-404"]        = "ERROR:UNEXPECTED_ERROR([FILE]{}[LINE]{}[ETC-Code]{})";
$ary["ITATERRAFORMCLI-ERR-501"]        = "{} Parse Error";
$ary["ITATERRAFORMCLI-ERR-502"]        = "{} Execute Error";
$ary["ITATERRAFORMCLI-ERR-503"]        = "{} Select Error";
$ary["ITATERRAFORMCLI-ERR-504"]        = "QUERY_NOT_FOUND(execution_No.)";
$ary["ITATERRAFORMCLI-ERR-505"]        = "QUERY_IS_NOT_INTEGER(execution_No.)";
$ary["ITATERRAFORMCLI-ERR-506"]        = "QUERY_NOT_FOUND(prg_recorder)";
$ary["ITATERRAFORMCLI-ERR-507"]        = "QUERY_IS_NOT_INTEGER(prg_recorder)";
$ary["ITATERRAFORMCLI-ERR-510"]        = "prg_recorder Select Error";
$ary["ITATERRAFORMCLI-ERR-511"]        = "";

$ary["ITATERRAFORMCLI-ERR-101010"]     = "異常発生 ([FILE]{}[LINE]{}[ETC-Code]{})";

$ary["ITATERRAFORMCLI-ERR-200010"]     = "対象の作業No.が見つかりません。\nレコードが廃止されている可能性があります。";
$ary["ITATERRAFORMCLI-ERR-200020"]     = "処理中の対象作業のステータスは緊急停止の実施対象外です。({})";
$ary["ITATERRAFORMCLI-ERR-200030"]     = "緊急停止フラグファイルの作成に失敗しました。";

$ary["ITATERRAFORMCLI-ERR-201010"]     = "拡張子が不正です。";

$ary["ITATERRAFORMCLI-ERR-202010"]     = "紐付対象メニューに項目が未登録です。";
$ary["ITATERRAFORMCLI-ERR-202020"]     = "紐付対象メニュー一覧にメニューが未登録です。";
$ary["ITATERRAFORMCLI-ERR-202030"]     = "登録方式が未選択です。";
$ary["ITATERRAFORMCLI-ERR-202040"]     = "登録方式が範囲外です。";
$ary["ITATERRAFORMCLI-ERR-202050"]     = "Movement-Module紐付に登録されているModuleに変数が未登録です。";
$ary["ITATERRAFORMCLI-ERR-202060"]     = "Key変数とVal変数のMovementが一致しません。";
$ary["ITATERRAFORMCLI-ERR-202070"]     = "メニューグループ:メニュー:項目が未選択です。";
$ary["ITATERRAFORMCLI-ERR-202080"]     = "Movementが未選択です。";
$ary["ITATERRAFORMCLI-ERR-202090"]     = "登録方式:「{}型」を設定した場合、{}変数の情報は設定できません。";
$ary["ITATERRAFORMCLI-ERR-202100"]     = "Movementが未登録です。";
$ary["ITATERRAFORMCLI-ERR-202110"]     = "変数(Key)が未選択です。";
$ary["ITATERRAFORMCLI-ERR-202120"]     = "変数(Key)が未登録です。";
$ary["ITATERRAFORMCLI-ERR-202130"]     = "変数(Value)が未選択です。";
$ary["ITATERRAFORMCLI-ERR-202140"]     = "変数(Value)が未登録です。";
$ary["ITATERRAFORMCLI-ERR-202150"]     = "次の項目が、[項番]:({})のレコードと重複しています。\n[(Movement),(変数),(メンバー変数),(代入順序)]";
$ary["ITATERRAFORMCLI-ERR-202160"]     = "Key変数とValue変数に同じ[変数,メンバー変数,代入順序]の入力はできません。";
$ary["ITATERRAFORMCLI-ERR-202170"]     = "[Movement,変数名]の組み合わせが不正です。";
$ary["ITATERRAFORMCLI-ERR-202180"]     = "登録方式:「Key型」を設定した場合、Value変数のHCL設定はONに変更できません。";
$ary["ITATERRAFORMCLI-ERR-202190"]     = "HCL設定がONの場合[メンバー変数,代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202200"]     = "[Movement,変数名]が一致している場合、HCL設定はONまたはOFFに統一してください。(Key変数はデフォルトでOFFとする。)";
$ary["ITATERRAFORMCLI-ERR-202210"]     = "次の項目が、[項番]:({})のレコードと重複しています。\n[(Movement),(変数),(HCL設定)]";
$ary["ITATERRAFORMCLI-ERR-202220"]     = "選択されたKey変数では、[メンバー変数,代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202230"]     = "変数のタイプがmap型の場合、Key変数に登録できません。";
$ary["ITATERRAFORMCLI-ERR-202240"]     = "選択されたKey変数では、[メンバー変数]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202250"]     = "選択されたKey変数では、[代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202260"]     = "選択されたKey変数では、[メンバー変数]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202270"]     = "選択されたKey変数では、[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202280"]     = "選択されたKey変数では、[メンバー変数]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202290"]     = "選択されたKey変数のメンバー変数では、[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202300"]     = "選択されたKey変数では、[メンバー変数,代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202310"]     = "選択されたValue変数では、[メンバー変数,代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202320"]     = "変数のタイプがmap型の場合、HCL設定はONにして下さい。";
$ary["ITATERRAFORMCLI-ERR-202330"]     = "選択されたValue変数では、[メンバー変数]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202340"]     = "選択されたValue変数では、[代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202350"]     = "選択されたValue変数では、[メンバー変数]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-202360"]     = "選択されたValue変数では、[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202370"]     = "選択されたValue変数では、[メンバー変数]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202380"]     = "選択されたValue変数のメンバー変数では、[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202390"]     = "選択されたValue変数では、[メンバー変数,代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-202400"]     = "[変数名,メンバー変数]の組み合わせが不正です。";
$ary["ITATERRAFORMCLI-ERR-202410"]     = "[変数名,メンバー変数]の組み合わせが不正です。";

$ary["ITATERRAFORMCLI-ERR-203010"]     = "Movement-Module紐付に登録されているModuleに変数が未登録です。";
$ary["ITATERRAFORMCLI-ERR-203020"]     = "Movementが未選択です。";
$ary["ITATERRAFORMCLI-ERR-203030"]     = "変数が未選択です。";
$ary["ITATERRAFORMCLI-ERR-203040"]     = "Movementが未登録です。";
$ary["ITATERRAFORMCLI-ERR-203050"]     = "[オペレーション,Movement,変数名]の組み合わせが不正です。";
$ary["ITATERRAFORMCLI-ERR-203060"]     = "[変数名,メンバー変数]の組み合わせが不正です。";
$ary["ITATERRAFORMCLI-ERR-203070"]     = "HCL設定がONの場合、[メンバー変数,代入順序]は入力不要です。";
$ary["ITATERRAFORMCLI-ERR-203080"]     = "[オペレーション,Movement,変数名]が一致している場合、HCL設定はONまたはOFFに統一してください。";
$ary["ITATERRAFORMCLI-ERR-203090"]     = "[オペレーション,Movement,変数名]が一致している場合、HCL設定はONまたはOFFに統一してください。";
$ary["ITATERRAFORMCLI-ERR-203100"]     = "[オペレーション,Movement,変数名]が一致している場合、Sensitive設定はONまたはOFFに統一してください。";
$ary["ITATERRAFORMCLI-ERR-203110"]     = "[オペレーション,Movement,変数名]が一致している場合、Sensitive設定はONまたはOFFに統一してください。";
$ary["ITATERRAFORMCLI-ERR-203120"]     = "[メンバー変数,代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-203130"]     = "[代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-203140"]     = "[メンバー変数]は必須です。";
$ary["ITATERRAFORMCLI-ERR-203150"]     = "[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-203160"]     = "[メンバー変数]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-203170"]     = "[メンバー変数,代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-203180"]     = "[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-203190"]     = "[代入順序]は入力できません。";
$ary["ITATERRAFORMCLI-ERR-203200"]     = "[代入順序]は必須です。";
$ary["ITATERRAFORMCLI-ERR-203210"]     = "[メンバー変数]は必須です。";
$ary["ITATERRAFORMCLI-ERR-203220"]     = "変数のタイプがmap型の場合、HCL設定はONにして下さい。";

$ary["ITATERRAFORMCLI-ERR-204010"]     = "パラメータチェックエラー";
$ary["ITATERRAFORMCLI-ERR-204020"]     = "対象の作業No.が見つかりません。\nレコードが廃止されている可能性があります。";
$ary["ITATERRAFORMCLI-ERR-204030"]     = "処理中の対象作業のステータスは予約取消できません。({})";

$ary["ITATERRAFORMCLI-ERR-205010"]     = "リソースの削除のタスク登録に失敗しました。(FILE:{} LINE:{} StatusCode:{})";
$ary["ITATERRAFORMCLI-ERR-205020"]     = "項目が不正です。(FILE:{} LINE:{} 対象:{})";

$ary["ITATERRAFORMCLI-ERR-206010"]     = "トランザクションの終了時に異常が発生しました";
$ary["ITATERRAFORMCLI-ERR-206020"]     = "ロールバックに失敗しました";
$ary["ITATERRAFORMCLI-ERR-206030"]     = "プロシージャ終了(異常)";
$ary["ITATERRAFORMCLI-ERR-206040"]     = "プロシージャ終了(警告)";
$ary["ITATERRAFORMCLI-ERR-206050"]     = "Moduleファイルが未登録です。処理をスキップします。(Module:{})";
$ary["ITATERRAFORMCLI-ERR-206060"]     = "例外発生";
$ary["ITATERRAFORMCLI-ERR-206070"]     = "システムで管理しているModuleファイルが存在しません。(ModuleID:{}  ファイル名:{})";
$ary["ITATERRAFORMCLI-ERR-206080"]     = "DBアクセス異常が発生しました。(file:{}line:{})";
$ary["ITATERRAFORMCLI-ERR-206090"]     = "関連データベースの変更があるか確認に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-206100"]     = "関連データベースの更新の反映完了の登録に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-206110"]     = "バックヤード処理(valautostup-workflow)起動の登録に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-206120"]     = "変数ネスト管理の最大繰り返し数の登録に失敗しました。(FILE:{} LINE:{})";
$ary["ITATERRAFORMCLI-ERR-206130"]     = "変数ネスト管理の最大繰り返し数の更新に失敗しました。(FILE:{} LINE:{} ID:{})";
$ary["ITATERRAFORMCLI-ERR-206140"]     = "変数ネスト管理の最大繰り返し数の廃止に失敗しました。(FILE:{} LINE:{} ID:{})";
$ary["ITATERRAFORMCLI-ERR-206150"]     = "メンバー変数の登録に失敗しました。(FILE:{} LINE:{})";
$ary["ITATERRAFORMCLI-ERR-206160"]     = "メンバー変数の更新に失敗しました。(FILE:{} LINE:{} ID:{})";
$ary["ITATERRAFORMCLI-ERR-206170"]     = "メンバー変数の廃止に失敗しました。(FILE:{} LINE:{} ID:{})";

$ary["ITATERRAFORMCLI-ERR-207010"]     = "DBアクセス異常が発生しました。(file:{}line:{})";
$ary["ITATERRAFORMCLI-ERR-207020"]     = "関連データベースの変更があるか確認に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207030"]     = "関連データベースの更新の反映完了の登録に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207040"]     = "バックヤード処理(varsautolistup-workflow)起動の登録に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207050"]     = "代入値自動登録設定からのカラム毎の変数情報の取得に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207060"]     = "代入値管理の読込に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207070"]     = "代入値管理への変数の具体値登録に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207080"]     = "代入値管理からの不要データの削除に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207090"]     = "代入値自動登録設定に登録されている紐付対象メニューが廃止されています。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207100"]     = "代入値自動登録設定に登録されている紐付対象メニューの項目情報が廃止されています。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207110"]     = "代入値自動登録設定に登録されているMovementがMovement-Module紐付に登録されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207120"]     = "代入値自動登録設定に登録されている紐付対象メニューのテーブル名が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207130"]     = "代入値自動登録設定に紐付く紐付対象メニューの主キー名が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207140"]     = "代入値自動登録設定に登録されている紐付対象メニューの項目情報が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207150"]     = "代入値自動登録設定に登録されている紐付対象メニューの項目名が取得出来ません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207160"]     = "代入値自動登録設定に登録されている登録方式が範囲外の値で設定されています。このレコードを処理対象外にします。(代入値自動登録設定 項番:{} 登録方式:{})";
$ary["ITATERRAFORMCLI-ERR-207170"]     = "代入値自動登録設定に変数が設定されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{} 変数区分:{})";
$ary["ITATERRAFORMCLI-ERR-207180"]     = "代入値自動登録設定に登録されている変数とMovementの組合せはMovement-Module紐付でMovementを紐付けていないか、Movement-Module紐付で紐付けているModuleでは使用されていません。このレコードを処対象外にします。(代入値自動登録設定 項番:{} 変数区分:{})";
$ary["ITATERRAFORMCLI-ERR-207190"]     = "代入値自動登録設定に登録されている変数はModuleに登録されているでは使用されていません。このレコードを処理対象外にします。(代入値自動登録設定 項番:{} 変数区分:{})";
$ary["ITATERRAFORMCLI-ERR-207200"]     = "カラム情報がない紐付対象メニューです。この紐付対象メニューは処理対象外にします。(MENU_ID:{})";
$ary["ITATERRAFORMCLI-ERR-207210"]     = "紐付対象メニューの情報取得に失敗しました。この紐付対象メニューは処理対象外にします。(MENU_ID:{})";
$ary["ITATERRAFORMCLI-ERR-207220"]     = "紐付対象メニューにデータが登録されていません。(MENU_ID:{})";
$ary["ITATERRAFORMCLI-ERR-207230"]     = "紐付対象メニューにオペレーションIDのカラムが設定されていません。このレコードを処理対象外とします。(MENU_ID:{} 紐付対象メニュー 項番:{})";
$ary["ITATERRAFORMCLI-ERR-207240"]     = "代入値自動登録設定の項番:{}と項番:{}のオペレーションとホストが重複しています。代入値自動登録設定の項番:{}を処理対象外にしました。(オペレーションID:{} 変数区分:{})";
$ary["ITATERRAFORMCLI-ERR-207250"]     = "紐付対象メニューの具体値が設定されていません。このレコードを処理対象外とします。(MENU_ID:{} 紐付対象メニュー 項番:{} 項目名:{})";
$ary["ITATERRAFORMCLI-ERR-207260"]     = "紐付対象メニューの具体値が空白です。(MENU_ID:{} 紐付対象メニュー 項番:{} 項目名:{})";
$ary["ITATERRAFORMCLI-ERR-207270"]     = "Terraformインタフェース情報レコード無し";
$ary["ITATERRAFORMCLI-ERR-207280"]     = "トランザクションスタートが失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207290"]     = "シーケンスロックに失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207300"]     = "トランザクションのコミットに失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207310"]     = "例外が発生しました。";
$ary["ITATERRAFORMCLI-ERR-207320"]     = "ロールバックに失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207330"]     = "トランザクションの終了時に異常が発生しました。";
$ary["ITATERRAFORMCLI-ERR-207340"]     = "マスタデータのアクセス許可ロールの取得に失敗しました。";
$ary["ITATERRAFORMCLI-ERR-207350"]     = "オペレーション・Movement一覧のアクセス許可ロールが適合しないので、このデータの処理をスキップします。(メニュー:{} カラム:{} オペレーション:{}({}) Movement:{}({}))";

?>