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
////ja_JP_UTF-8_ITACICDFORIAC_MNU
$ary["ITACICDFORIAC-MNU-1200010000"]   = "リモートリポジトリのクローンをITAに生成する為の情報をメンテナンス(閲覧/登録/更新/廃止)できます。<BR>
※1 「リモートリポジト URL」と「ブランチ」は、Gitのクローンコマンドに渡す引数の値を設定してください。<BR>
　　git clone 「リモートリポジトリURL」「ローカルリポジトリパス」 -b「ブランチ」<BR>
　　「ローカルリポジトリパス」は /「ITAインストールディレクトリ」/ita-root/repositorys/0000000001(項番 右詰10桁)になります。";
$ary["ITACICDFORIAC-MNU-1200010001"]   = "項番";
$ary["ITACICDFORIAC-MNU-1200010002"]   = "リモートリポジトリ";
$ary["ITACICDFORIAC-MNU-1200010003"]   = "リモートリポジトリ";
$ary["ITACICDFORIAC-MNU-1200010100"]   = "リモートリポジトリ名";
$ary["ITACICDFORIAC-MNU-1200010101"]   = "ITAでリモートリポジトリを示す名称を入力して下さい。
[最大長] 256バイト";
$ary["ITACICDFORIAC-MNU-1200010200"]   = "リモートリポジトリ(URL)";
$ary["ITACICDFORIAC-MNU-1200010201"]   = "git cloneコマンドに指定するクローン元のリポジトリを入力してください。
[最大長] 256バイト";
$ary["ITACICDFORIAC-MNU-1200010300"]   = "ブランチ";
$ary["ITACICDFORIAC-MNU-1200010301"]   = "ブランチ名を入力して下さい。
[最大長] 256バイト";
$ary["ITACICDFORIAC-MNU-1200010400"]   = "プロトコル";
$ary["ITACICDFORIAC-MNU-1200010401"]   = "リモートリポジトリと接続するプロトコルを選択して下さい。
リモートリポジトリとhttpsで接続する場合、httpsを選択して下さい。
ローカルのGitの場合、Localを選択して下さい。";
$ary["ITACICDFORIAC-MNU-1200010500"]   = "Visibilityタイプ";
$ary["ITACICDFORIAC-MNU-1200010501"]   = "リモートリポジトリのVisibilityタイプを選択して下さい。
プロトコルでprivayeを選択した場合、Visibilityタイプの選択は必須です。";
$ary["ITACICDFORIAC-MNU-1200010600"]   = "Git アカウント情報";
$ary["ITACICDFORIAC-MNU-1200010700"]   = "ユーザ";
$ary["ITACICDFORIAC-MNU-1200010701"]   = "Gitのユーザを入力して下さい。
VisibilityタイプでPrivateを選択した場合、ユーザの入力は必須です。
[最大長] 128バイト";
$ary["ITACICDFORIAC-MNU-1200010800"]   = "パスワード";
$ary["ITACICDFORIAC-MNU-1200010801"]   = "Gitのcloneコマンドを実行した際に求められるパスワードを入力してください。
VisibilityタイプでPrivateを選択した場合、パスワードの入力は必須です。
[最大長] 128バイト
尚、GitHubでは2021年8月13日でパスワード認証が廃止されます。
https://github.blog/2020-12-15-token-authentication-requirements-for-git-operations/
パスワード認証が廃止されているGitHubを利用している場合、Gitアカウント情報のパスワードには、自身で個人アクセストークンを作成し入力して下さい。
個人アクセストークン作成方法
https://docs.github.com/ja/github/authenticating-to-github/keeping-your-account-and-data-secure/creating-a-personal-access-token";
$ary["ITACICDFORIAC-MNU-1200010810"]   = "ssh接続情報";
$ary["ITACICDFORIAC-MNU-1200010820"]   = "パスワード";
$ary["ITACICDFORIAC-MNU-1200010821"]   = "Gitのcloneコマンドを実行した際に求められるLinuxユーザーのパスワードを入力してください。
プロトコルでsshパスワード認証を選択している場合、パスワードの入力は必須です。
[最大長] 128バイト";
$ary["ITACICDFORIAC-MNU-1200010830"]   = "パスフレーズ";
$ary["ITACICDFORIAC-MNU-1200010831"]   = "Gitのcloneコマンドを実行した際に求められる秘密鍵ファイルに設定されているパスフレーズを入力してください。
プロトコルでssh鍵認証(パスフレーズあり)を選択している場合、パスフレーズの入力は必須です。
[最大長] 128バイト";
$ary["ITACICDFORIAC-MNU-1200010840"]   = "接続パラメータ";
$ary["ITACICDFORIAC-MNU-1200010841"]   = "Gitのcloneコマンドを実行時に環境変数「GIT_SSH_COMMAND」に設定するパラメータを設定します。
GIT_SSH_COMMANDは、Git2.3以降のバージョンで設定出来る環境変数です。ITAサーバにインストールされているGItバージョンがGit2.3より古い場合は、設定されたパラメータが無効になります。
環境変数「GIT_SSH_COMMAND」はデフォルトで下記のパラメータを設定してます。
'UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no'
設定されたパラメータは、この後ろに追加されます。
[最大長] 512バイト";
$ary["ITACICDFORIAC-MNU-1200010900"]   = "Proxy";
$ary["ITACICDFORIAC-MNU-1200011000"]   = "Address";
$ary["ITACICDFORIAC-MNU-1200011001"]   = "Proxyサーバを利用する場合、Proxyサーバのアドレスを入力して下さい。
[最大長] 128バイト";
$ary["ITACICDFORIAC-MNU-1200011100"]   = "Port";
$ary["ITACICDFORIAC-MNU-1200011101"]   = "Proxyサーバを利用する場合、Proxyサーバのポートを入力して下さい。";
$ary["ITACICDFORIAC-MNU-1200011200"]   = "リモートリポジトリ同期情報";
$ary["ITACICDFORIAC-MNU-1200011300"]   = "自動同期";
$ary["ITACICDFORIAC-MNU-1200011301"]   = "リモートリポジトリとの同期を自動で行うかを選択して下さい。
有効：入力された周期でリモートリポジトリとの同期を行います。
無効：リモートリポジトリとの同期は自動で行いません。";
$ary["ITACICDFORIAC-MNU-1200011400"]   = "周期(秒)";
$ary["ITACICDFORIAC-MNU-1200011401"]   = "リモートリポジトリとの同期を自動で行う周期を入力して下さい。
未入力時のデフォルトは60秒です。
単位:秒";
$ary["ITACICDFORIAC-MNU-1200011500"]   = "リモートリポジトリ同期状態";
$ary["ITACICDFORIAC-MNU-1200011600"]   = "状態";
$ary["ITACICDFORIAC-MNU-1200011601"]   = "自動同期で有効を設定した場合、リモートリポジトリとの同期状態を正常/異常で表示します。";
$ary["ITACICDFORIAC-MNU-1200011700"]   = "詳細情報";
$ary["ITACICDFORIAC-MNU-1200011701"]   = "自動同期で有効を設定した場合、リモートリポジトリとの同期でエラーが発生した場合、エラー情報が表示されます。";
$ary["ITACICDFORIAC-MNU-1200011800"]   = "最終日時";
$ary["ITACICDFORIAC-MNU-1200011801"]   = "最後にリモートリポジトリと同期した日時が表示されます。";
$ary["ITACICDFORIAC-MNU-1200011900"]   = "再開";
$ary["ITACICDFORIAC-MNU-1200012000"]   = "通信リトライ情報";
$ary["ITACICDFORIAC-MNU-1200012100"]   = "回数";
$ary["ITACICDFORIAC-MNU-1200012101"]   = "通信に失敗した場合、通信をリトライする回数を入力して下さい。
未入力時のデフォルトは3回です。";
$ary["ITACICDFORIAC-MNU-1200012200"]   = "周期(ms)";
$ary["ITACICDFORIAC-MNU-1200012201"]   = "通信に失敗した場合、通信をリトライする周期を入力して下さい。
未入力時のデフォルトは1000msです。
単位:ms";
$ary["ITACICDFORIAC-MNU-1200020000"]   = "リモートリポジトリに登録されていた資材が表示されます。<BR>
フィルタ表示されている資材を一括ダウンロードすることもできます。";
$ary["ITACICDFORIAC-MNU-1200020001"]   = "項番";
$ary["ITACICDFORIAC-MNU-1200020002"]   = "リモートリポジトリ資材";
$ary["ITACICDFORIAC-MNU-1200020003"]   = "リモートリポジトリ資材";
$ary["ITACICDFORIAC-MNU-1200020100"]   = "リモートリポジトリ名";
$ary["ITACICDFORIAC-MNU-1200020101"]   = "[元データ]リモートリポジトリ";
$ary["ITACICDFORIAC-MNU-1200020200"]   = "資材パス";
$ary["ITACICDFORIAC-MNU-1200020201"]   = "リモートリポジトリ内の資材パス
[元データ]リモートリポジトリ";
$ary["ITACICDFORIAC-MNU-1200020300"]   = "資材タイプ";
$ary["ITACICDFORIAC-MNU-1200020301"]   = "資材タイプ";
$ary["ITACICDFORIAC-MNU-1200030000"]   = "リモートリポジトリの資材と他コンソールで使用する資材との紐付をメンテナンス(閲覧/登録/更新/廃止)できます。
紐付先の資材はリモートリポジトリ側の資材が更新される度に自動反映されるようになります。";
$ary["ITACICDFORIAC-MNU-1200030001"]   = "項番";
$ary["ITACICDFORIAC-MNU-1200030002"]   = "資材紐付";
$ary["ITACICDFORIAC-MNU-1200030003"]   = "資材紐付";
$ary["ITACICDFORIAC-MNU-1200030100"]   = "紐付先資材名";
$ary["ITACICDFORIAC-MNU-1200030101"]   = "紐付先に登録する資材名を入力してください。
紐付先資材タイプで選択している資材に入力した資材名が存在しない場合、新規登録されます。
紐付先資材タイプで選択している資材に入力した資材名が廃止レコードの場合、レコードを復活し資材を更新します。
[最大長] 256バイト";
$ary["ITACICDFORIAC-MNU-1200030200"]   = "Git リポジトリ(From)";
$ary["ITACICDFORIAC-MNU-1200030300"]   = "リモートリポジトリ";
$ary["ITACICDFORIAC-MNU-1200030301"]   = "[元データ]リモートリポジトリ";
$ary["ITACICDFORIAC-MNU-1200030400"]   = "資材パス";
$ary["ITACICDFORIAC-MNU-1200030401"]   = "[元データ]リモートリポジトリ資材";
$ary["ITACICDFORIAC-MNU-1200030500"]   = "Exastro IT automation(To)";
$ary["ITACICDFORIAC-MNU-1200030600"]   = "紐付先資材タイプ";
$ary["ITACICDFORIAC-MNU-1200030601"]   = "紐付先の資材タイプを選択します。";
$ary["ITACICDFORIAC-MNU-1200030700"]   = "Ansible-Pioneer";
$ary["ITACICDFORIAC-MNU-1200030800"]   = "対話種別";
$ary["ITACICDFORIAC-MNU-1200030801"]   = "紐付先の資材タイプで対話ファイル素材集を選択している場合、対話種別の選択は必須です。
[元データ]ansible-pioneerコンソール/対話種別リスト";
$ary["ITACICDFORIAC-MNU-1200030900"]   = "OS種別";
$ary["ITACICDFORIAC-MNU-1200030901"]   = "紐付先の資材タイプで対話ファイル素材集を選択している場合、OS種別マスタの選択は必須です。
[元データ]ansible-pioneerコンソール/OS種別マスタ";
$ary["ITACICDFORIAC-MNU-1200031000"]   = "素材同期情報";
$ary["ITACICDFORIAC-MNU-1200031100"]   = "自動同期";
$ary["ITACICDFORIAC-MNU-1200031101"]   = "リモートリポジトリとの同期を自動を行うかを有効/無効で設定して下さい。
有効：入力された周期でリモートリポジトリとの同期を行います。
無効：リモートリポジトリとの同期は自動で行いません。";
$ary["ITACICDFORIAC-MNU-1200031200"]   = "状態";
$ary["ITACICDFORIAC-MNU-1200031201"]   = "自動同期で有効を設定した場合、リモートリポジトリとの最新の同期状態を正常/異常/再開で表示します。";
$ary["ITACICDFORIAC-MNU-1200031300"]   = "詳細情報";
$ary["ITACICDFORIAC-MNU-1200031301"]   = "自動同期で有効を設定した場合、リモートリポジトリとの同期でエラーが発生した場合、エラー情報が表示されます。";
$ary["ITACICDFORIAC-MNU-1200031400"]   = "最終日時";
$ary["ITACICDFORIAC-MNU-1200031401"]   = "最後にリモートリポジトリと同期した日時";
$ary["ITACICDFORIAC-MNU-1200031500"]   = "デリバリ情報";
$ary["ITACICDFORIAC-MNU-1200031600"]   = "オペレーション";
$ary["ITACICDFORIAC-MNU-1200031601"]   = "[元データ]基本コンソール/オペレーション一覧";
$ary["ITACICDFORIAC-MNU-1200031700"]   = "Movement";
$ary["ITACICDFORIAC-MNU-1200031701"]   = "[元データ]各コンソール/Movement一覧";
$ary["ITACICDFORIAC-MNU-1200031800"]   = "ドライラン";
$ary["ITACICDFORIAC-MNU-1200031801"]   = "Movementをドライランで実行したい場合、●を選択して下さい。
デフォルトはドライランでは実行しません。";
$ary["ITACICDFORIAC-MNU-1200031900"]   = "作業インスタンスNo";
$ary["ITACICDFORIAC-MNU-1200031901"]   = "Movementを実行した際に採番された作業インスタンスNoです。";
$ary["ITACICDFORIAC-MNU-1200032000"]   = "実行ログインID";
$ary["ITACICDFORIAC-MNU-1200032001"]   = "資材紐付を行うユーザを選択して下さい。
[元データ]登録アカウント";
$ary["ITACICDFORIAC-MNU-1200032100"]   = "詳細情報";
$ary["ITACICDFORIAC-MNU-1200032101"]   = "デリバリでエラーが発生した場合、エラー情報が表示されます。";
$ary["ITACICDFORIAC-MNU-1200032200"]   = "アクセス許可ロール付与";
$ary["ITACICDFORIAC-MNU-1200032201"]   = "各素材集に追加・更新するレコードに付与するアクセス許可ロールの設定内容を選択します。
なし：空白（アクセス許可ロールなし）
あり：Restユーザに紐付ているロールでデフォルトでアクセスを許可しているロールを付与
未選択時のデフォルトは「なし」です。";
$ary["ITACICDFORIAC-MNU-1200032300"]   = "テンプレート管理";
$ary["ITACICDFORIAC-MNU-1200032400"]   = "変数定義";
$ary["ITACICDFORIAC-MNU-1200032401"]   = "テンプレート素材で使用している変数(VAR_)の構造をYAML形式で定義します。
変数の構造は以下の３種類で定義可能です。
・変数名に対して具体値を１つ定義できる変数
    例
  　　VAR_sample:
・変数名に対して具体値を複数定義できる変数
    例
    　VAR_sample: []
・階層化された変数
    例
      VAR_sample:
        name:
        value:";
$ary["ITACICDFORIAC-MNU-1200032500"]   = "作業実行確認メニューID";
$ary["ITACICDFORIAC-MNU-1200032501"]   = "作業実行確認メニューID";
$ary["ITACICDFORIAC-MNU-1200032600"]   = "作業状態確認";
$ary["ITACICDFORIAC-MNU-1200032601"]   = "作業状態確認";
$ary["ITACICDFORIAC-MNU-1200032800"]   = "最終実行ログインID";
$ary["ITACICDFORIAC-MNU-1200032801"]   = "資材紐付を行ったユーザです。";
$ary["ITACICDFORIAC-MNU-1200035001"]   = "リポジトリを選択して下さい";
$ary["ITACICDFORIAC-MNU-1200035002"]   = "紐付先資材タイプを選択して下さい";
$ary["ITACICDFORIAC-MNU-1200040000"]   = "ITAとRestAPIを行う為の接続インターフェース情報をメンテナンス(閲覧/更新)できます。";
$ary["ITACICDFORIAC-MNU-1200040001"]   = "項番";
$ary["ITACICDFORIAC-MNU-1200040002"]   = "インターフェース情報";
$ary["ITACICDFORIAC-MNU-1200040003"]   = "インターフェース情報";
$ary["ITACICDFORIAC-MNU-1200040100"]   = "ホスト名";
$ary["ITACICDFORIAC-MNU-1200040101"]   = "ITAのホスト名(またはIPアドレス)を入力して下さい。
https通信の場合、基本的にホスト名が推奨
[最大長]128バイト";
$ary["ITACICDFORIAC-MNU-1200040400"]   = "プロトコル";
$ary["ITACICDFORIAC-MNU-1200040401"]   = "http/httpsのいずれかを入力して下さい。
通常はhttps
です。";
$ary["ITACICDFORIAC-MNU-1200040500"]   = "ポート";
$ary["ITACICDFORIAC-MNU-1200040501"]   = "接続ポートを入力して下さい。
通常は
httpの場合:80
httpsの場合:443
です。";
$ary["ITACICDFORIAC-MNU-1200050000"]   = "資材紐付を実行するユーザ情報をメンテナンス(閲覧/登録/更新/廃止)できます。 
資材紐付を実行を行うユーザは、管理コンソール/ユーザ管理に登録しておく必要があります。";
$ary["ITACICDFORIAC-MNU-1200050001"]   = "項番";
$ary["ITACICDFORIAC-MNU-1200050002"]   = "登録アカウント";
$ary["ITACICDFORIAC-MNU-1200050003"]   = "登録アカウント";
$ary["ITACICDFORIAC-MNU-1200050004"] = "Exastro IT Automationアカウント";
$ary["ITACICDFORIAC-MNU-1200050100"]   = "ログインID";
$ary["ITACICDFORIAC-MNU-1200050101"]   = " 資材紐付を実行するログインIDを選択して下さい。
 [元データ]管理コンソール/ユーザ管理";
$ary["ITACICDFORIAC-MNU-1200050200"]   = "ログインPW";
$ary["ITACICDFORIAC-MNU-1200050201"]   = " ログインIDのパスワードを入力して下さい。
 文字数は8～30バイト、半角英数字と利用可能な記号（!#$%&'()*+./;<=>?@[]^\_`{|}~）で入力して下さい。";
$ary["ITACICDFORIAC-MNU-1200050202"]   = "半角英数字と利用可能な記号（!\"#$%&'()*+,./:;<=>?@[]^\\_`{|}）";
?>
