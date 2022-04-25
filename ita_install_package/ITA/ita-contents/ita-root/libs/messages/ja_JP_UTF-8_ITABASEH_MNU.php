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
////ja_JP_UTF-8_ITABASEH_MNU
$ary["ITABASEH-MNU-101020"]         = "作業対象ホストの情報をメンテナンス(閲覧/登録/更新/廃止)できます。<BR>各オーケストレータの実行前に作業対象ホストに応じた必要情報を登録してください。";
$ary["ITABASEH-MNU-101030"]         = "管理システム項番";
$ary["ITABASEH-MNU-101040"]         = "機器一覧";
$ary["ITABASEH-MNU-101050"]         = "機器一覧";
$ary["ITABASEH-MNU-101060"]         = "HW機器種別";
$ary["ITABASEH-MNU-101070"]         = "HW機器の種別を選択します。\n・NW(ネットワーク)\n・ST(ストレージ)\n・SV(サーバー)";
$ary["ITABASEH-MNU-101080"]         = "ホスト名";
$ary["ITABASEH-MNU-101081"]         = "ホスト名が不正です。数値のホスト名は設定出来ません。";
$ary["ITABASEH-MNU-101090"]         = "[最大長]128バイト";
$ary["ITABASEH-MNU-102010"]         = "IPアドレス";
$ary["ITABASEH-MNU-102020"]         = "[最大長]15バイト\nxxx.xxx.xxx.xxxの形式で入力してください。";
$ary["ITABASEH-MNU-102024"]         = "Ansible利用情報";
$ary["ITABASEH-MNU-102025"]         = "Pioneer利用情報";
$ary["ITABASEH-MNU-102026"]         = "Ansible Automation Controller利用情報";
$ary["ITABASEH-MNU-102030"]         = "プロトコル";
$ary["ITABASEH-MNU-102040"]         = "Ansible-Pioneerにて機器ログインする際のプロトコルです。";
$ary["ITABASEH-MNU-102050"]         = "ログインユーザID";
$ary["ITABASEH-MNU-102060"]         = "[最大長]30バイト";
$ary["ITABASEH-MNU-102061"]         = "ログインパスワード";
$ary["ITABASEH-MNU-102062"]         = "管理";
$ary["ITABASEH-MNU-102063"]         = "●の場合、ログインパスワードは必須です。";
$ary["ITABASEH-MNU-102064"]         = "●の場合、ログインパスワードの有効期限を無効にします。";
$ary["ITABASEH-MNU-102065"]         = "●の場合、初回ログイン時にログインパスワードの再設定を行いません。";
$ary["ITABASEH-MNU-102070"]         = "ログインパスワード";
$ary["ITABASEH-MNU-102071"]         = "ログインパスワード管理を●とする場合、ログインパスワードの入力は必須です。";
$ary["ITABASEH-MNU-102072"]         = "ログインパスワード管理を●としない場合、ログインパスワードの入力は禁止です。";
$ary["ITABASEH-MNU-102073"]         = "認証方式がパスワード認証の場合、ログインパスワードの入力は必須です。";
$ary["ITABASEH-MNU-102074"]         = "認証方式がパスワード認証の場合、ログインパスワードの管理は必須です。";
$ary["ITABASEH-MNU-102075"]         = "認証方式の入力値が不正です。";
$ary["ITABASEH-MNU-102080"]         = "[最大長]128バイト";
$ary["ITABASEH-MNU-102085"]         = "Legacy/Role利用情報";
$ary["ITABASEH-MNU-102088"]         = "認証方式";
$ary["ITABASEH-MNU-102089"]         = "Ansibleから機器へ接続する際の認証方式を選択します。
・パスワード認証
　ログインパスワードの管理で●の選択と、ログインパスワードの入力が必須です。
・鍵認証（パスフレーズなし）
　ssh秘密鍵ファイルのアップロードが必須です。
・鍵認証（パスフレーズあり）
　ssh秘密鍵ファイルのアップロードと、パスフレーズの入力が必須です。
・鍵認証（鍵交換済み）
　ssh秘密鍵ファイルのアップロードは必要ありません。
・パスワード認証（winrm)
　必要に応じてWinRM接続情報を入力します。
尚、パスワード認証（winrm)以外の認証方式の場合、機器側に以下の設定が必要です。
ログインユーザの sudo 権限を NOPASSWD付で /etc/sudoers に設定する必要があります。";
$ary["ITABASEH-MNU-102090"]         = "OS種別";
$ary["ITABASEH-MNU-102100"]         = "LANG";
$ary["ITABASEH-MNU-102101"]         = "Pioneerの対話ファイルを実行する際の文字エンコード(LANG)を選択します。 空白の場合はutf-8扱いとなります。";
$ary["ITABASEH-MNU-102110"]         = "EtherWakeOnLan";
$ary["ITABASEH-MNU-102120"]         = "電源ON";
$ary["ITABASEH-MNU-102130"]         = "電源ON";
$ary["ITABASEH-MNU-102140"]         = "MACアドレス";
$ary["ITABASEH-MNU-102150"]         = "[最大長]17バイト";
$ary["ITABASEH-MNU-102160"]         = "ネットワークデバイス名";
$ary["ITABASEH-MNU-102170"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-103010"]         = "Ansible-Pioneerにて対象機器のOS種別ごとに対話ファイルを使い分けるために利用します。";
$ary["ITABASEH-MNU-103015"]         = "Cobbler利用情報";
$ary["ITABASEH-MNU-103020"]         = "プロファイル";
$ary["ITABASEH-MNU-103030"]         = "[元データ]Cobblerコンソール/プロファイルリスト";
$ary["ITABASEH-MNU-103040"]         = "Interface";
$ary["ITABASEH-MNU-103050"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-103051"]         = "接続タイプ";
$ary["ITABASEH-MNU-103052"]         = "Ansible Automation Controller認証情報の接続タイプを設定します。通常はmachineを選択します。ansible_connectionをloclaに設定する必要があるネットワーク機器の場合にNetworkを選択します。";
$ary["ITABASEH-MNU-103060"]         = "MACアドレス";
$ary["ITABASEH-MNU-103070"]         = "[最大長]17バイト";
$ary["ITABASEH-MNU-103080"]         = "Netmask";
$ary["ITABASEH-MNU-103090"]         = "[最大長]15バイト";
$ary["ITABASEH-MNU-104010"]         = "Gateway";
$ary["ITABASEH-MNU-104020"]         = "[最大長]15バイト";
$ary["ITABASEH-MNU-104030"]         = "Static";
$ary["ITABASEH-MNU-104040"]         = "[最大長]32バイト";
$ary["ITABASEH-MNU-104050"]         = "表示順序";
$ary["ITABASEH-MNU-104060"]         = "表示順序の制御用";
$ary["ITABASEH-MNU-104070"]         = "オペレーション一覧をメンテナンス(閲覧/登録/更新/廃止)できます。 ";
$ary["ITABASEH-MNU-104080"]         = "No.";
$ary["ITABASEH-MNU-104090"]         = "オペレーション一覧";
$ary["ITABASEH-MNU-104101"]         = "SCRAB利用情報";
$ary["ITABASEH-MNU-104111"]         = "ポート番号";
$ary["ITABASEH-MNU-104112"]         = "OS種別がLinux系の場合は、ssh接続する際のポート番号です。
OS種別がWindows系の場合は、WinRM接続する際のポート番号です。
通常は
ssh接続の場合:22
WinRM接続の場合:5985";
$ary["ITABASEH-MNU-104121"]         = "OS種別";
$ary["ITABASEH-MNU-104122"]         = "構築対象ノードのOS種別です。";
$ary["ITABASEH-MNU-104131"]         = "データ連携";
$ary["ITABASEH-MNU-104132"]         = "SCARBのサーバー情報とデータ連携する場合に「●」を選択して下さい。";
$ary["ITABASEH-MNU-104141"]         = "ホスト指定方式";
$ary["ITABASEH-MNU-104142"]         = "構築対象ノードの指定方法です。";
$ary["ITABASEH-MNU-104151"]         = "認証方式";
$ary["ITABASEH-MNU-104152"]         = "SCARBとの認証方式を指定して下さい。
OS種別がWindows系の場合は、PowerShellのバージョンを選択してください。
PowerShellバージョン4以前
PowerShellバージョン5以降
OS種別がLinux系の場合は、以下の認証方式より選択してください。
パスワード認証
ssh鍵認証
sshコンフィグファイル";
$ary["ITABASEH-MNU-104161"]         = "ssh秘密鍵ファイル";
$ary["ITABASEH-MNU-104162"]         = "認証方式でssh鍵認証を指定した場合に鍵認証ファイルのパスを入力します。
鍵認証ファイルはSCRABサーバーに配置されている必要があります。";
$ary["ITABASEH-MNU-104171"]         = "sshコンフィグファイル";
$ary["ITABASEH-MNU-104172"]         = "認証方式でsshコンフィグファイルを指定した場合にsshコンフィグファイルのパスを入力します。
sshコンフィグファイルはSCRABサーバーに配置されている必要があります。";
$ary["ITABASEH-MNU-104201"]         = "OpenAudIT利用情報";
$ary["ITABASEH-MNU-104211"]         = "接続種別";
$ary["ITABASEH-MNU-104212"]         = "";
$ary["ITABASEH-MNU-104213"]         = "Community";
$ary["ITABASEH-MNU-104214"]         = "";
$ary["ITABASEH-MNU-104215"]         = "ユーザネーム";
$ary["ITABASEH-MNU-104216"]         = "";
$ary["ITABASEH-MNU-104217"]         = "パスワード";
$ary["ITABASEH-MNU-104218"]         = "";
$ary["ITABASEH-MNU-104219"]         = "KEYファイル";
$ary["ITABASEH-MNU-104220"]         = "";
$ary["ITABASEH-MNU-104221"]         = "Security name";
$ary["ITABASEH-MNU-104222"]         = "";
$ary["ITABASEH-MNU-104223"]         = "Security level";
$ary["ITABASEH-MNU-104224"]         = "";
$ary["ITABASEH-MNU-104225"]         = "Authentication protocol";
$ary["ITABASEH-MNU-104226"]         = "";
$ary["ITABASEH-MNU-104227"]         = "Authentication passphrase";
$ary["ITABASEH-MNU-104228"]         = "";
$ary["ITABASEH-MNU-104229"]         = "Privacy protocol";
$ary["ITABASEH-MNU-104230"]         = "";
$ary["ITABASEH-MNU-104231"]         = "Privacy passphrase";
$ary["ITABASEH-MNU-104232"]         = "";
$ary["ITABASEH-MNU-104501"]         = "DSC利用情報";
$ary["ITABASEH-MNU-104502"]         = "証明書ファイル";
$ary["ITABASEH-MNU-104503"]         = "証資格情報を暗号化する場合に証明書ファイルを入力します。";
$ary["ITABASEH-MNU-104504"]         = "サムプリント";
$ary["ITABASEH-MNU-104505"]         = "証資格情報を暗号化する場合にサムプリントを入力します。";
$ary["ITABASEH-MNU-104600"]         = "WinRM接続情報";
$ary["ITABASEH-MNU-104605"]         = "ポート番号";
$ary["ITABASEH-MNU-104606"]         = "WindowsServerにWinRM接続する際のポート番号を入力します。
未指定の場合はデフォルト(http:5985)でのWinRM接続となります。";
$ary["ITABASEH-MNU-104610"]         = "サーバー証明書";
$ary["ITABASEH-MNU-104611"]         = "WindowsServerにhttpsでWinRM接続する際のサーバー証明書を入力します。
Pythonのバージョンが2.7以降でhttpsのサーバー証明書の検証を行わない場合、インベントリファイル追加オプションに下記のパラメータを入力して下さい。
    ansible_winrm_server_cert_validation: ignore";
$ary["ITABASEH-MNU-104615"]         = "接続オプション";
$ary["ITABASEH-MNU-104616"]         = "プロトコルがsshの場合\n/etc/ansible/ansible.cfgのssh_argsに設定しているsshオプション以外のオプションを設定したい場合>、設定したいオプションを入力します。\n(例)\n    sshコンフィグファイルを指定する場合\n      -F /root/.ssh/ssh_config\n\nプロトコルがtelnetの場合\ntelnet接続時のオプションを設定したい場合、設定したいオプションを入力します。\n(例)\n    ポート番号を11123に指定する場合\n      11123";
$ary["ITABASEH-MNU-104620"]         = "インベントリファイル\n追加オプション";
$ary["ITABASEH-MNU-104621"]         = "ITAが設定していないインベントリファイルのオプションパラメータをyaml形式で入力します。
(例)
    ansible_connection: network_cli
    ansible_network_os: nxos";
$ary["ITABASEH-MNU-104630"]         = "インスタンスグループ名";
$ary["ITABASEH-MNU-104631"]         = "Ansible Automation Controllerのインベントリに設定するインスタンスグループを指定します。";
$ary["ITABASEH-MNU-105010"]         = "オペレーション一覧";
$ary["ITABASEH-MNU-105020"]         = "オペレーション名";
$ary["ITABASEH-MNU-105030"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-105040"]         = "実施予定日時";
$ary["ITABASEH-MNU-105050"]         = "システム的には利用していません。";
$ary["ITABASEH-MNU-105060"]         = "オペレーションID";
$ary["ITABASEH-MNU-105070"]         = "オペレーションID(自動採番)";
$ary["ITABASEH-MNU-105075"]         = "最終実行日時";
$ary["ITABASEH-MNU-105076"]         = "オペレーションを実行した実績の日時";
$ary["ITABASEH-MNU-105080"]         = "表示順序";
$ary["ITABASEH-MNU-105090"]         = "表示順序の制御用";
$ary["ITABASEH-MNU-106010"]         = "選択";
$ary["ITABASEH-MNU-106020"]         = "OS種別をメンテナンス(閲覧/登録/更新/廃止)できます。 ";
$ary["ITABASEH-MNU-106030"]         = "OS種別ID";
$ary["ITABASEH-MNU-106040"]         = "Ansible(共通)OS種別マスタ";
$ary["ITABASEH-MNU-106050"]         = "Ansible(共通)OS種別マスタ";
$ary["ITABASEH-MNU-106060"]         = "OS種別名";
$ary["ITABASEH-MNU-106070"]         = "バージョンまで含めることをお勧めします。\n(例)RHEL7.2";
$ary["ITABASEH-MNU-106075"]         = "機器種別";
$ary["ITABASEH-MNU-106080"]         = "SV";
$ary["ITABASEH-MNU-106090"]         = "";
$ary["ITABASEH-MNU-107010"]         = "NW";
$ary["ITABASEH-MNU-107020"]         = "";
$ary["ITABASEH-MNU-107030"]         = "ST";
$ary["ITABASEH-MNU-107040"]         = "";
$ary["ITABASEH-MNU-107050"]         = "表示順序";
$ary["ITABASEH-MNU-107060"]         = "表示順序の制御用";
$ary["ITABASEH-MNU-107070"]         = "Movementとオーケストレータの関連付けを閲覧できます。";
$ary["ITABASEH-MNU-107080"]         = "Movement ID";
$ary["ITABASEH-MNU-107090"]         = "Movement一覧";
$ary["ITABASEH-MNU-108010"]         = "Movement一覧";
$ary["ITABASEH-MNU-108020"]         = "Movement名";
$ary["ITABASEH-MNU-108030"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-108040"]         = "オーケストレータ";
$ary["ITABASEH-MNU-108050"]         = "使用するオーケストレータが表示されます。";
$ary["ITABASEH-MNU-108060"]         = "遅延タイマー";
$ary["ITABASEH-MNU-108070"]         = "Movementが指定期間(分)を遅延した場合にステータスを遅延として警告します。";
$ary["ITABASEH-MNU-108075"]         = "Ansible利用情報";
$ary["ITABASEH-MNU-108080"]         = "ホスト指定形式";
$ary["ITABASEH-MNU-108090"]         = "構築対象ノードの指定方法です。";
$ary["ITABASEH-MNU-108091"]         = "並列実行数";
$ary["ITABASEH-MNU-108092"]         = "NULLまたは正の整数";
$ary["ITABASEH-MNU-108100"]         = "WinRM接続";
$ary["ITABASEH-MNU-108110"]         = "構築対象ノードがWindowsServerでWinRM接続する場合に選択します。";
$ary["ITABASEH-MNU-108120"]         = "gather_facts";
$ary["ITABASEH-MNU-108130"]         = "Playbook実行時に構築対象ノードの情報(gather_facts)を取得したい場合に選択します。";
$ary["ITABASEH-MNU-108200"]         = "OpenStack利用情報";
$ary["ITABASEH-MNU-108210"]         = "HEATテンプレート";
$ary["ITABASEH-MNU-108220"]         = "実行するHEATテンプレートをアップロードします。[最大サイズ]4GB";
$ary["ITABASEH-MNU-108230"]         = "環境設定ファイル";
$ary["ITABASEH-MNU-108240"]         = "HEATテンプレート実行後、実行されるスクリプトファイルをアップロードします。[最大サイズ]4GB";
$ary["ITABASEH-MNU-108241"]         = "Tower利用情報";
$ary["ITABASEH-MNU-108242"]         = "virtualenv";
$ary["ITABASEH-MNU-108243"]         = "virtualenvで構築されているansible実行環境をディレクトリで表示しています。\n実行したいansible実行環境を選択します。\n未選択の場合はAnsible Automation Controllerインストール時にインストールされたansible実行環境を使用します。";
$ary["ITABASEH-MNU-108300"]         = "DSC利用情報";
$ary["ITABASEH-MNU-108310"]         = "エラーリトライタイムアウト";
$ary["ITABASEH-MNU-108320"]         = "Movementが指定時間（秒）を超えてエラーが継続した場合にステータスを異常とします。";
$ary["ITABASEH-MNU-109006"]         = "ssh秘密鍵ファイル";
$ary["ITABASEH-MNU-109007"]         = "鍵認証する場合のssh秘密鍵ファイルを入力します。
アップロードしたファイルは暗号化されて保存されます。
登録後にダウンロードした場合、暗号化されたファイルがダウンロードされます。";
$ary["ITABASEH-MNU-109008"]         = "パスフレーズ";
$ary["ITABASEH-MNU-109009"]         = "ssh秘密鍵ファイルに設定されているパスフレーズを入力します。";
$ary["ITABASEH-MNU-109010"]         = "ansible-vaultで暗号化されたssh秘密鍵ファイル";
$ary["ITABASEH-MNU-109011"]         = "ssh鍵認証情報";
$ary["ITABASEH-MNU-109030"]         = "Symphonyクラスを閲覧できます。<br>「詳細」を押下するとSymphonyクラス編集メニューに遷移します。";
$ary["ITABASEH-MNU-109040"]         = "SymphonyクラスID";
$ary["ITABASEH-MNU-109050"]         = "Symphonyクラス一覧";
$ary["ITABASEH-MNU-109060"]         = "Symphonyクラス一覧";
$ary["ITABASEH-MNU-109070"]         = "Symphony名称";
$ary["ITABASEH-MNU-109080"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-109090"]         = "説明";
$ary["ITABASEH-MNU-111010"]         = "Terraform利用情報";
$ary["ITABASEH-MNU-111020"]         = "Organization:Workspace";
$ary["ITABASEH-MNU-111030"]         = "対象のOrganization:Workspaceです。";
$ary["ITABASEH-MNU-120001"]         = "項目";
$ary["ITABASEH-MNU-120002"]         = "項目タイプ";
$ary["ITABASEH-MNU-120003"]         = "親項目";
$ary["ITABASEH-MNU-120004"]         = "物理名";
$ary["ITABASEH-MNU-120005"]         = "論理名";
$ary["ITABASEH-MNU-120006"]         = "関連テーブル名";
$ary["ITABASEH-MNU-120007"]         = "関連項目";
$ary["ITABASEH-MNU-120008"]         = "テーブル名";
$ary["ITABASEH-MNU-120009"]         = "ビュー名";
$ary["ITABASEH-MNU-120010"]         = "ER図メニュー管理";
$ary["ITABASEH-MNU-120011"]         = "項目。メニューIDとマルチユニークです。";
$ary["ITABASEH-MNU-120012"]         = "項目タイプには以下の状態が存在します。\n・グループ\n・アイテム";
$ary["ITABASEH-MNU-120013"]         = "項目IDがグループ配下にある場合、グループの項目IDを設定する。";
$ary["ITABASEH-MNU-120014"]         = "項目の物理名";
$ary["ITABASEH-MNU-120015"]         = "項目の論理名";
$ary["ITABASEH-MNU-120016"]         = "関連のあるテーブル名";
$ary["ITABASEH-MNU-120017"]         = "関連のある項目";
$ary["ITABASEH-MNU-120018"]         = "必須項目 メニューに紐づくテーブル名です。";
$ary["ITABASEH-MNU-120019"]         = "メニューに紐づくテーブルのビューです。";
$ary["ITABASEH-MNU-120020"]         = "ER図に関する設定を行います。表示するメニュー情報を設定します。";
$ary["ITABASEH-MNU-120021"]         = "ER図に関する設定を行います。表示するメニュー内の項目を設定します。";
$ary["ITABASEH-MNU-120022"]         = "ER図項目管理";
$ary["ITABASEH-MNU-201010"]         = "詳細表示";
$ary["ITABASEH-MNU-201020"]         = "詳細";
$ary["ITABASEH-MNU-201030"]         = "表示順序";
$ary["ITABASEH-MNU-201040"]         = "表示順序の制御用";
$ary["ITABASEH-MNU-201050"]         = "選択";
$ary["ITABASEH-MNU-201060"]         = "Symphony作業一覧(実行履歴)を閲覧できます。 <br>「詳細」を押下するとSymphony作業確認メニューに遷移します。";
$ary["ITABASEH-MNU-201070"]         = "SymphonyインスタンスID";
$ary["ITABASEH-MNU-201080"]         = "Symphony作業一覧";
$ary["ITABASEH-MNU-201090"]         = "Symphony作業一覧";
$ary["ITABASEH-MNU-201110"]         = "実行ユーザ";
$ary["ITABASEH-MNU-201120"]         = "[元データ]ユーザ管理";
$ary["ITABASEH-MNU-202010"]         = "詳細表示";
$ary["ITABASEH-MNU-202020"]         = "詳細";
$ary["ITABASEH-MNU-202030"]         = "Symphony名称";
$ary["ITABASEH-MNU-202040"]         = "[元データ]Symphonyクラス一覧";
$ary["ITABASEH-MNU-202050"]         = "オペレーション";
$ary["ITABASEH-MNU-202060"]         = "[元データ]オペレーション一覧";
$ary["ITABASEH-MNU-202070"]         = "オペレーション名";
$ary["ITABASEH-MNU-202080"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-202090"]         = "ステータス";
$ary["ITABASEH-MNU-202100"]         = "保留ステータス";
$ary["ITABASEH-MNU-203010"]         = "ステータスには以下の状態が存在します。\n
・未実行\n
・未実行(予約)\n
・実行中\n
・実行中(遅延)\n
・正常終了\n
・緊急停止\n
・異常終了\n
・想定外エラー\n
・予約取消";
$ary["ITABASEH-MNU-203020"]         = "緊急停止発令フラグ";
$ary["ITABASEH-MNU-203030"]         = "[元データ]Symphony作業確認";
$ary["ITABASEH-MNU-203040"]         = "予約日時";
$ary["ITABASEH-MNU-203050"]         = "[形式]YYYY/MM/DD HH:MM";
$ary["ITABASEH-MNU-203060"]         = "開始日時";
$ary["ITABASEH-MNU-203070"]         = "[形式]YYYY/MM/DD HH:MM:SS";
$ary["ITABASEH-MNU-203080"]         = "終了日時";
$ary["ITABASEH-MNU-203090"]         = "[形式]YYYY/MM/DD HH:MM:SS";
$ary["ITABASEH-MNU-203091"]         = "通知ログ";
$ary["ITABASEH-MNU-203092"]         = "ログファイルを出力します。";
$ary["ITABASEH-MNU-203093"]         = "保留ステータスには以下の状態が存在します。\n
・一時停止中";
$ary["ITABASEH-MNU-203094"]         = "一時停止中";
$ary["ITABASEH-MNU-203095"]         = "未発令";
$ary["ITABASEH-MNU-203096"]         = "発令済";
$ary["ITABASEH-MNU-204010"]         = "表示順序";
$ary["ITABASEH-MNU-204020"]         = "表示順序の制御用";
$ary["ITABASEH-MNU-204030"]         = "選択";
$ary["ITABASEH-MNU-204040"]         = "説明";
$ary["ITABASEH-MNU-204050"]         = "Symphony編集";
$ary["ITABASEH-MNU-204060"]         = "SymphonyクラスID";
$ary["ITABASEH-MNU-204070"]         = "Symphony名称";
$ary["ITABASEH-MNU-204071"]         = "Permission role";
$ary["ITABASEH-MNU-204080"]         = "Note";
$ary["ITABASEH-MNU-204090"]         = "start";
$ary["ITABASEH-MNU-205010"]         = "Display filter";
$ary["ITABASEH-MNU-205020"]         = "Name Fillter";
$ary["ITABASEH-MNU-205030"]         = "Auto filter";
$ary["ITABASEH-MNU-205040"]         = "Filter";
$ary["ITABASEH-MNU-205050"]         = "Clear filter";
$ary["ITABASEH-MNU-205060"]         = "スケジューリング";
$ary["ITABASEH-MNU-205065"]         = "Symphonyを実行できます。<BR>・即時実行<BR>・予約実行<BR>が可能です。<BR>実行時にはSymphonyクラスIDとオペレーションIDを選択して下さい。";
$ary["ITABASEH-MNU-205070"]         = "予約日時を指定する場合は、日時フォーマット(YYYY/MM/DD HH:II)で入力して下さい。
ブランクの場合は即時実行となります";
$ary["ITABASEH-MNU-205080"]         = "予約日時";
$ary["ITABASEH-MNU-205090"]         = "Symphony[フィルタ]";
$ary["ITABASEH-MNU-206010"]         = "Symphony[一覧]";
$ary["ITABASEH-MNU-206020"]         = "オペレーション[フィルタ]";
$ary["ITABASEH-MNU-206030"]         = "オペレーション[一覧]";
$ary["ITABASEH-MNU-206040"]         = "Symphony実行";
$ary["ITABASEH-MNU-206050"]         = "SymphonyクラスID";
$ary["ITABASEH-MNU-206060"]         = "Symphony名称";
$ary["ITABASEH-MNU-206070"]         = "Note";
$ary["ITABASEH-MNU-206080"]         = "start";
$ary["ITABASEH-MNU-206090"]         = "オペレーションID";
$ary["ITABASEH-MNU-207010"]         = "オペレーション名";
$ary["ITABASEH-MNU-207020"]         = "Symphony作業確認";
$ary["ITABASEH-MNU-207030"]         = "SymphonyインスタンスID";
$ary["ITABASEH-MNU-207040"]         = "Symphony名称";
$ary["ITABASEH-MNU-207050"]         = "Note";
$ary["ITABASEH-MNU-207060"]         = "start";
$ary["ITABASEH-MNU-207070"]         = "オペレーションID";
$ary["ITABASEH-MNU-207080"]         = "オペレーション名";
$ary["ITABASEH-MNU-207090"]         = "ステータス";
$ary["ITABASEH-MNU-208010"]         = "予約日時";
$ary["ITABASEH-MNU-208020"]         = "緊急停止命令";
$ary["ITABASEH-MNU-209000"]         = "Symphonyクラスに紐付Movementを閲覧出来ます。";
$ary["ITABASEH-MNU-209001"]         = "Movement class ID";
$ary["ITABASEH-MNU-209002"]         = "Symphony紐付Movement一覧";
$ary["ITABASEH-MNU-209003"]         = "Orchestrator ID";
$ary["ITABASEH-MNU-209004"]         = "Movement ID";
$ary["ITABASEH-MNU-209005"]         = "Sequence no";
$ary["ITABASEH-MNU-209006"]         = "Pause";
$ary["ITABASEH-MNU-209007"]         = "Description";
$ary["ITABASEH-MNU-209008"]         = "Symphony class no";
$ary["ITABASEH-MNU-209100"]         = "Symphonyインスタンスに紐付くMovementインスタンスを閲覧出来ます。";
$ary["ITABASEH-MNU-209101"]         = "Symphony instance id";
$ary["ITABASEH-MNU-209102"]         = "Movementインスタンス一覧";
$ary["ITABASEH-MNU-209103"]         = "Movement class no";
$ary["ITABASEH-MNU-209104"]         = "Orchestrator id";
$ary["ITABASEH-MNU-209105"]         = "Pattern id";
$ary["ITABASEH-MNU-209106"]         = "Pattern name";
$ary["ITABASEH-MNU-209107"]         = "Time limit";
$ary["ITABASEH-MNU-209108"]         = "Ansible host designate type id";
$ary["ITABASEH-MNU-209109"]         = "Ansible winrm id";
$ary["ITABASEH-MNU-209110"]         = "DSC retry timeout";
$ary["ITABASEH-MNU-209111"]         = "Movement sequence number";
$ary["ITABASEH-MNU-209112"]         = "Flag of next Pending";
$ary["ITABASEH-MNU-209113"]         = "Description";
$ary["ITABASEH-MNU-209114"]         = "Symphony instance no";
$ary["ITABASEH-MNU-209115"]         = "Execution no";
$ary["ITABASEH-MNU-209116"]         = "Status id";
$ary["ITABASEH-MNU-209117"]         = "Flag of abort recepted";
$ary["ITABASEH-MNU-209118"]         = "Start time";
$ary["ITABASEH-MNU-209119"]         = "End time";
$ary["ITABASEH-MNU-209120"]         = "Flag to hold release";
$ary["ITABASEH-MNU-209121"]         = "Flag to skip execution";
$ary["ITABASEH-MNU-209122"]         = "Overwrite operation no";
$ary["ITABASEH-MNU-209123"]         = "Overwrite operation name";
$ary["ITABASEH-MNU-209124"]         = "Overwrite operation id";
$ary["ITABASEH-MNU-211000"]         = "代入値自動登録設定に紐付けるメニューをメンテナンス(閲覧/登録/更新/廃止)できます。";
$ary["ITABASEH-MNU-211001"]         = "項番";
$ary["ITABASEH-MNU-211002"]         = "紐付対象メニュー";
$ary["ITABASEH-MNU-211003"]         = "紐付対象メニュー";
$ary["ITABASEH-MNU-211004"]         = "メニューグループ";
$ary["ITABASEH-MNU-211005"]         = "ID";
$ary["ITABASEH-MNU-211006"]         = "登録・更新時は当該項目は更新対象ではない。(メニューIDを更新すること)";
$ary["ITABASEH-MNU-211007"]         = "名称";
$ary["ITABASEH-MNU-211008"]         = "登録・更新時は当該項目は更新対象ではない。(メニューIDを更新すること)";
$ary["ITABASEH-MNU-211009"]         = "メニュー";
$ary["ITABASEH-MNU-211010"]         = "ID";
$ary["ITABASEH-MNU-211011"]         = "登録・更新時は当該項目は更新対象ではない。(メニューグループ:メニューを更新すること)";
$ary["ITABASEH-MNU-211012"]         = "名称";
$ary["ITABASEH-MNU-211013"]         = "登録・更新時は当該項目は更新対象ではない。(メニューグループ:メニューを更新すること)";
$ary["ITABASEH-MNU-211014"]         = "メニューグループ:メニュー";
$ary["ITABASEH-MNU-211015"]         = "シートタイプ";
$ary["ITABASEH-MNU-211016"]         = "アクセス許可ロール有無";
$ary["ITABASEH-MNU-212000"]         = "紐付対象メニューテーブル管理";
$ary["ITABASEH-MNU-212001"]         = "項番";
$ary["ITABASEH-MNU-212002"]         = "紐付対象メニューテーブル管理";
$ary["ITABASEH-MNU-212003"]         = "紐付対象メニューテーブル管理";
$ary["ITABASEH-MNU-212004"]         = "メニュー";
$ary["ITABASEH-MNU-212005"]         = "テーブル名";
$ary["ITABASEH-MNU-212006"]         = "主キー";
$ary["ITABASEH-MNU-213000"]         = "紐付対象メニューカラム管理";
$ary["ITABASEH-MNU-213001"]         = "項番";
$ary["ITABASEH-MNU-213002"]         = "紐付対象メニューカラム管理";
$ary["ITABASEH-MNU-213003"]         = "紐付対象メニューカラム管理";
$ary["ITABASEH-MNU-213004"]         = "メニュー";
$ary["ITABASEH-MNU-213005"]         = "カラム";
$ary["ITABASEH-MNU-213006"]         = "項目名";
$ary["ITABASEH-MNU-213007"]         = "参照テーブル";
$ary["ITABASEH-MNU-213008"]         = "参照主キー";
$ary["ITABASEH-MNU-213009"]         = "参照カラム";
$ary["ITABASEH-MNU-213010"]         = "表示順";
$ary["ITABASEH-MNU-213011"]         = "クラス";
$ary["ITABASEH-MNU-214001"]         = "オペレーションが保存期間切れのデータを削除する情報をメンテナンス(閲覧/登録/更新/廃止)できます。 ";
$ary["ITABASEH-MNU-214002"]         = "項番";
$ary["ITABASEH-MNU-214003"]         = "オペレーション削除管理";
$ary["ITABASEH-MNU-214004"]         = "オペレーション削除管理";
$ary["ITABASEH-MNU-214005"]         = "論理削除日数";
$ary["ITABASEH-MNU-214006"]         = "";
$ary["ITABASEH-MNU-214007"]         = "物理削除日数";
$ary["ITABASEH-MNU-214008"]         = "";
$ary["ITABASEH-MNU-214009"]         = "テーブル名";
$ary["ITABASEH-MNU-214010"]         = "";
$ary["ITABASEH-MNU-214011"]         = "主キーカラム名";
$ary["ITABASEH-MNU-214012"]         = "";
$ary["ITABASEH-MNU-214013"]         = "オペレーションIDカラム名";
$ary["ITABASEH-MNU-214014"]         = "";
$ary["ITABASEH-MNU-214015"]         = "データストレージパス取得SQL";
$ary["ITABASEH-MNU-214016"]         = "";
$ary["ITABASEH-MNU-214017"]         = "履歴データパス1";
$ary["ITABASEH-MNU-214018"]         = "";
$ary["ITABASEH-MNU-214019"]         = "履歴データパス2";
$ary["ITABASEH-MNU-214020"]         = "";
$ary["ITABASEH-MNU-214021"]         = "履歴データパス3";
$ary["ITABASEH-MNU-214022"]         = "";
$ary["ITABASEH-MNU-214023"]         = "履歴データパス4";
$ary["ITABASEH-MNU-214024"]         = "";
$ary["ITABASEH-MNU-215001"]         = "ファイルが保存期間切れのデータを削除する情報をメンテナンス(閲覧/登録/更新/廃止)できます。 ";
$ary["ITABASEH-MNU-215002"]         = "項番";
$ary["ITABASEH-MNU-215003"]         = "ファイル削除管理";
$ary["ITABASEH-MNU-215004"]         = "ファイル削除管理";
$ary["ITABASEH-MNU-215005"]         = "削除日数";
$ary["ITABASEH-MNU-215006"]         = "";
$ary["ITABASEH-MNU-215007"]         = "削除対象ディレクトリ";
$ary["ITABASEH-MNU-215008"]         = "";
$ary["ITABASEH-MNU-215009"]         = "削除対象ファイル";
$ary["ITABASEH-MNU-215010"]         = "";
$ary["ITABASEH-MNU-215011"]         = "サブディレクトリ削除有無";
$ary["ITABASEH-MNU-215012"]         = "";
$ary["ITABASEH-MNU-301010"]         = "ADグループ判定";
$ary["ITABASEH-MNU-301020"]         = "項番";
$ary["ITABASEH-MNU-301030"]         = "ADグループ判定";
$ary["ITABASEH-MNU-301040"]         = "ADグループ判定";
$ary["ITABASEH-MNU-301050"]         = "ADグループ識別子";
$ary["ITABASEH-MNU-301060"]         = "ADグループ識別子";
$ary["ITABASEH-MNU-301070"]         = "ITAロール";
$ary["ITABASEH-MNU-301080"]         = "ITAロール";
$ary["ITABASEH-MNU-302010"]         = "ADユーザ判定";
$ary["ITABASEH-MNU-302020"]         = "項番";
$ary["ITABASEH-MNU-302030"]         = "ADユーザ判定";
$ary["ITABASEH-MNU-302040"]         = "ADユーザ判定";
$ary["ITABASEH-MNU-302050"]         = "ADユーザ識別子";
$ary["ITABASEH-MNU-302060"]         = "ADユーザ識別子";
$ary["ITABASEH-MNU-302070"]         = "ITAユーザ";
$ary["ITABASEH-MNU-302080"]         = "ITAユーザ";
$ary["ITABASEH-MNU-303000"]         = "Symphonyのインターフェース情報をメンテナンス(閲覧/更新)できます。 <br>本メニューは必ず1レコード>である必要があります。";
$ary["ITABASEH-MNU-303010"]         = "No";
$ary["ITABASEH-MNU-303020"]         = "Symphonyインターフェース情報";
$ary["ITABASEH-MNU-303030"]         = "Symphonyインターフェース情報";
$ary["ITABASEH-MNU-303040"]         = "データリレイストレージパス";
$ary["ITABASEH-MNU-303050"]         = "ITA側のSymphonyインスタンス毎の共有ディレクトリです。";
$ary["ITABASEH-MNU-303060"]         = "状態監視周期(単位ミリ秒)";
$ary["ITABASEH-MNU-303070"]         = "Symphony実行時の作業状況をリフレッシュする間隔です。\n環境毎にチューニングを要しますが、通常は3000ミリ秒程度が推奨値です。";
$ary["ITABASEH-MNU-304000"]         = "Conductorのインターフェース情報をメンテナンス(閲覧/更新)できます。 <br>本メニューは必ず1レコードである必要があります。";
$ary["ITABASEH-MNU-304010"]         = "No";
$ary["ITABASEH-MNU-304020"]         = "Conductorインターフェース情報";
$ary["ITABASEH-MNU-304030"]         = "Conductorインターフェース情報";
$ary["ITABASEH-MNU-304040"]         = "データリレイストレージパス";
$ary["ITABASEH-MNU-304050"]         = "ITA側Conductorインスタンス毎の共有ディレクトリです。";
$ary["ITABASEH-MNU-304060"]         = "状態監視周期(単位ミリ秒)";
$ary["ITABASEH-MNU-304070"]         = "Conductor実行時の作業状況をリフレッシュする間隔です。\n環境毎にチューニングを要しますが、通常は3000ミリ秒程度が推奨値です。";
$ary["ITABASEH-MNU-305030"]         = "Conductorクラスを閲覧できます。<br>「詳細」を押下するとConductorクラス編集メニューに遷移します。";
$ary["ITABASEH-MNU-305040"]         = "ConductorクラスID";
$ary["ITABASEH-MNU-305050"]         = "Conductorクラス一覧";
$ary["ITABASEH-MNU-305060"]         = "Conductorクラス一覧";
$ary["ITABASEH-MNU-305070"]         = "Conductor名称";
$ary["ITABASEH-MNU-305080"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-305090"]         = "説明";
$ary["ITABASEH-MNU-305100"]         = "Editor情報";
$ary["ITABASEH-MNU-305110"]         = "Node数";
$ary["ITABASEH-MNU-305120"]         = "Terminal数";
$ary["ITABASEH-MNU-305130"]         = "Edge数";
$ary["ITABASEH-MNU-305140"]         = "高さ";
$ary["ITABASEH-MNU-305150"]         = "種別";
$ary["ITABASEH-MNU-305160"]         = "幅";
$ary["ITABASEH-MNU-305170"]         = "横軸";
$ary["ITABASEH-MNU-305180"]         = "縦軸";
$ary["ITABASEH-MNU-305190"]         = "紐付Line";
$ary["ITABASEH-MNU-305200"]         = "紐付Node";
$ary["ITABASEH-MNU-305210"]         = "状態";
$ary["ITABASEH-MNU-305220"]         = "Line id";
$ary["ITABASEH-MNU-305230"]         = "内側接続Node";
$ary["ITABASEH-MNU-305240"]         = "外側接続Node";
$ary["ITABASEH-MNU-305250"]         = "内側接続Terminal";
$ary["ITABASEH-MNU-305260"]         = "外側接続Terminal";
$ary["ITABASEH-MNU-305270"]         = "終了種別";
$ary["ITABASEH-MNU-305280"]         = "通知設定";
$ary["ITABASEH-MNU-306010"]         = "Conductor作業一覧(実行履歴)を閲覧できます。 <br>「詳細」を押下するとConductor作業確認メニューに遷移します。";
$ary["ITABASEH-MNU-306020"]         = "ConductorインスタンスID";
$ary["ITABASEH-MNU-306030"]         = "Conductor作業一覧";
$ary["ITABASEH-MNU-306040"]         = "Conductor作業一覧";
$ary["ITABASEH-MNU-306050"]         = "Conductor名称";
$ary["ITABASEH-MNU-306060"]         = "[元データ]Conductorクラス一覧";
$ary["ITABASEH-MNU-306070"]         = "緊急停止発令フラグ";
$ary["ITABASEH-MNU-306080"]         = "[元データ]Conductor作業確認";
$ary["ITABASEH-MNU-307001"]         = "Conductorをスケジュールにしたがって定期的に実行させることができます。<br>対象のConductor, operationを選択し、「スケジュール設定」から詳細な設定を入力してください。";
$ary["ITABASEH-MNU-307002"]         = "定期作業実行ID";
$ary["ITABASEH-MNU-307003"]         = "Conductor定期作業実行";
$ary["ITABASEH-MNU-307004"]         = "Conductor定期作業実行";
$ary["ITABASEH-MNU-307005"]         = "作業一覧確認";
$ary["ITABASEH-MNU-307006"]         = "ステータス";
$ary["ITABASEH-MNU-307007"]         = "ステータスには以下の状態が存在します。\n
・準備中\n
・稼働中\n
・完了\n
・不整合エラー\n
・紐付けエラー\n
・想定外エラー\n
・Conductor廃止\n
・operation廃止";
$ary["ITABASEH-MNU-307008"]         = "スケジュール設定";
$ary["ITABASEH-MNU-307009"]         = "スケジュール";
$ary["ITABASEH-MNU-307010"]         = "次回実行日付";
$ary["ITABASEH-MNU-307011"]         = "開始日付";
$ary["ITABASEH-MNU-307012"]         = "終了日付";
$ary["ITABASEH-MNU-307013"]         = "周期";
$ary["ITABASEH-MNU-307014"]         = "間隔";
$ary["ITABASEH-MNU-307015"]         = "週番号";
$ary["ITABASEH-MNU-307016"]         = "曜日";
$ary["ITABASEH-MNU-307017"]         = "日";
$ary["ITABASEH-MNU-307018"]         = "時間";
$ary["ITABASEH-MNU-307019"]         = "作業停止期間";
$ary["ITABASEH-MNU-307020"]         = "開始";
$ary["ITABASEH-MNU-307021"]         = "終了";
$ary["ITABASEH-MNU-307022"]         = "Conductor名称";
$ary["ITABASEH-MNU-307023"]         = "[元データ]・Conductor一覧";
$ary["ITABASEH-MNU-307024"]         = "オペレーション名";
$ary["ITABASEH-MNU-307025"]         = "[元データ]オペレーション一覧";
$ary["ITABASEH-MNU-307026"]         = "自動入力";
$ary["ITABASEH-MNU-307027"]         = "実行ユーザ";
$ary["ITABASEH-MNU-307028"]         = "Conductorを実行するユーザ（登録/更新したユーザが自動的に入力されます）";
$ary["ITABASEH-MNU-308000"]         = "Conductorクラスに紐付されたNodeを閲覧出来ます。";
$ary["ITABASEH-MNU-308001"]         = "Node class id";
$ary["ITABASEH-MNU-308002"]         = "Conductor紐付Node一覧";
$ary["ITABASEH-MNU-308003"]         = "Node name";
$ary["ITABASEH-MNU-308004"]         = "Node type id";
$ary["ITABASEH-MNU-308005"]         = "Orchestrator id";
$ary["ITABASEH-MNU-308006"]         = "Pattern id";
$ary["ITABASEH-MNU-308007"]         = "Conductor class no";
$ary["ITABASEH-MNU-308008"]         = "Conductor call class no";
$ary["ITABASEH-MNU-308009"]         = "Operation no idbh";
$ary["ITABASEH-MNU-308010"]         = "Skip flag";
$ary["ITABASEH-MNU-308100"]         = "Nodeクラスに紐付されたTerminalを閲覧出来ます。";
$ary["ITABASEH-MNU-308101"]         = "Terminal class id";
$ary["ITABASEH-MNU-308102"]         = "Node紐付Terminal一覧";
$ary["ITABASEH-MNU-308104"]         = "Terminal type id";
$ary["ITABASEH-MNU-308105"]         = "Terminal name";
$ary["ITABASEH-MNU-308106"]         = "Node class no";
$ary["ITABASEH-MNU-308107"]         = "Conductor class no";
$ary["ITABASEH-MNU-308108"]         = "Conductor node name";
$ary["ITABASEH-MNU-308109"]         = "Conditional id";
$ary["ITABASEH-MNU-308110"]         = "Case no";
$ary["ITABASEH-MNU-308200"]         = "Conductorインスタンスを閲覧出来ます。";
$ary["ITABASEH-MNU-308201"]         = "Conductor instance id";
$ary["ITABASEH-MNU-308202"]         = "Conductorインスタンス一覧";
$ary["ITABASEH-MNU-308203"]         = "I Conductor class no";
$ary["ITABASEH-MNU-308204"]         = "Operation no uapk";
$ary["ITABASEH-MNU-308205"]         = "Status id";
$ary["ITABASEH-MNU-308206"]         = "Execution user";
$ary["ITABASEH-MNU-308207"]         = "Abort execution flg";
$ary["ITABASEH-MNU-308208"]         = "Conductor ncall flg";
$ary["ITABASEH-MNU-308209"]         = "Conductor caller no";
$ary["ITABASEH-MNU-308210"]         = "Time book";
$ary["ITABASEH-MNU-308211"]         = "Time start";
$ary["ITABASEH-MNU-308212"]         = "Time end";
$ary["ITABASEH-MNU-308300"]         = "Nodeインスタンスを閲覧出来ます。";
$ary["ITABASEH-MNU-308301"]         = "Node instance id";
$ary["ITABASEH-MNU-308302"]         = "Nodeインスタンス一覧";
$ary["ITABASEH-MNU-308303"]         = "I node class no";
$ary["ITABASEH-MNU-309001"]         = "Conductorを実行できます。<BR>・即時実行<BR>・予約実行<BR>が可能です。<BR>実行時にはConductorクラスIDとオペレーションIDを選択して下さい。";
$ary["ITABASEH-MNU-309002"]         = "Conductor[フィルタ]";
$ary["ITABASEH-MNU-309003"]         = "Conductor[一覧]";
$ary["ITABASEH-MNU-309004"]         = "Conductor実行";
$ary["ITABASEH-MNU-309005"]         = "ConductorクラスID";
$ary["ITABASEH-MNU-309006"]         = "Conductor名称";
$ary["ITABASEH-MNU-309007"]         = "新規";
$ary["ITABASEH-MNU-309008"]         = "保存";
$ary["ITABASEH-MNU-309009"]         = "読込";
$ary["ITABASEH-MNU-309010"]         = "取り消し";
$ary["ITABASEH-MNU-309011"]         = "やり直し";
$ary["ITABASEH-MNU-309012"]         = "ノード削除";
$ary["ITABASEH-MNU-309013"]         = "全体表示";
$ary["ITABASEH-MNU-309014"]         = "表示リセット";
$ary["ITABASEH-MNU-309015"]         = "フルスクリーン";
$ary["ITABASEH-MNU-309016"]         = "フルスクリーン解除";
$ary["ITABASEH-MNU-309017"]         = "ログ";
$ary["ITABASEH-MNU-309018"]         = "登録";
$ary["ITABASEH-MNU-309019"]         = "編集";
$ary["ITABASEH-MNU-309020"]         = "流用新規";
$ary["ITABASEH-MNU-309021"]         = "更新";
$ary["ITABASEH-MNU-309022"]         = "再読込";
$ary["ITABASEH-MNU-309023"]         = "キャンセル";
$ary["ITABASEH-MNU-309024"]         = "実行";
$ary["ITABASEH-MNU-309025"]         = "予約取消";
$ary["ITABASEH-MNU-309026"]         = "緊急停止";
$ary["ITABASEH-MNU-309027"]         = "Conductor名称";
$ary["ITABASEH-MNU-309028"]         = "投入データ一式(zip)";
$ary["ITABASEH-MNU-309029"]         = "結果データ一式（zip）";
$ary["ITABASEH-MNU-309030"]         = "download(.zip)";
$ary["ITABASEH-MNU-309031"]         = "投入データ一式(zip)";
$ary["ITABASEH-MNU-309032"]         = "結果データ一式（zip）";
$ary["ITABASEH-MNU-309033"]         = "download(.zip)";
$ary["ITABASEH-MNU-309034"]         = "投入データ一式（zip）です。";
$ary["ITABASEH-MNU-309035"]         = "結果データ一式（zip）です。";
$ary["ITABASEH-MNU-309036"]         = "決定";
$ary["ITABASEH-MNU-309037"]         = "取消";
$ary["ITABASEH-MNU-309038"]         = "整列";
$ary["ITABASEH-MNU-309039"]         = "等間隔";
$ary["ITABASEH-MNU-309040"]         = "水平方向左に整列";
$ary["ITABASEH-MNU-309041"]         = "水平方向中央に整列";
$ary["ITABASEH-MNU-309042"]         = "水平方向右に整列";
$ary["ITABASEH-MNU-309043"]         = "垂直方向上に整列";
$ary["ITABASEH-MNU-309044"]         = "垂直方向中央に整列";
$ary["ITABASEH-MNU-309045"]         = "垂直方向下に整列";
$ary["ITABASEH-MNU-309046"]         = "垂直方向等間隔に分布";
$ary["ITABASEH-MNU-309047"]         = "水平方向等間隔に分布";
$ary["ITABASEH-MNU-309048"]         = "Conductor名称を入力します。[最大長]256バイト";
$ary["ITABASEH-MNU-309049"]         = "説明を入力します。[最大長]8192バイト";
$ary["ITABASEH-MNU-309050"]         = "プリント";
$ary["ITABASEH-MNU-309051"]         = "メニューグループ選択";
$ary["ITABASEH-MNU-309052"]         = "リレーション";
$ary["ITABASEH-MNU-309053"]         = "マウスホイール";
$ary["ITABASEH-MNU-309054"]         = "画面の拡大・縮小";
$ary["ITABASEH-MNU-309055"]         = "マウス右ドラッグ";
$ary["ITABASEH-MNU-309056"]         = "画面の移動";
$ary["ITABASEH-MNU-309057"]         = "マウス左クリック";
$ary["ITABASEH-MNU-309058"]         = "Node選択・接続線削除";
$ary["ITABASEH-MNU-309059"]         = "マウス左ドラッグ";
$ary["ITABASEH-MNU-309060"]         = "Node移動・複数選択";
$ary["ITABASEH-MNU-309061"]         = "Node選択";
$ary["ITABASEH-MNU-309062"]         = "Node選択・作業状態確認";
$ary["ITABASEH-MNU-310000"]         = "比較定義を閲覧できます。こちらで定義した情報を元に比較実行を行います。<br>以下が比較対象メニューとして利用できます。<br>・パラメータシート（ホスト/オペレーションあり）";
$ary["ITABASEH-MNU-310001"]         = "比較定義";
$ary["ITABASEH-MNU-310002"]         = "比較定義名";
$ary["ITABASEH-MNU-310003"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-310004"]         = "比較対象メニュー1";
$ary["ITABASEH-MNU-310005"]         = "比較対象メニュー2";
$ary["ITABASEH-MNU-310006"]         = "[元データ]紐付対象メニュー";
$ary["ITABASEH-MNU-310007"]         = "全件一致";
$ary["ITABASEH-MNU-310008"]         = "比較対象メニューの項目名が完全一致する場合、●を選択。
一致しない場合、比較定義詳細の設定が必要となります。";
$ary["ITABASEH-MNU-310100"]         = "比較定義詳細を閲覧できます。<br>比較対象項目(表示項目名、比較対象項目の紐付)の設定を行います。";
$ary["ITABASEH-MNU-310101"]         = "比較定義詳細";
$ary["ITABASEH-MNU-310102"]         = "比較定義名";
$ary["ITABASEH-MNU-310103"]         = "[元データ]比較定義";
$ary["ITABASEH-MNU-310104"]         = "表示項目名";
$ary["ITABASEH-MNU-310105"]         = "[最大長]256バイト";
$ary["ITABASEH-MNU-310106"]         = "対象カラム1";
$ary["ITABASEH-MNU-310107"]         = "対象カラム2";
$ary["ITABASEH-MNU-310108"]         = "[元データ]紐付対象メニューカラム管理";
$ary["ITABASEH-MNU-310109"]         = "メニューを選択して下さい";
$ary["ITABASEH-MNU-310110"]         = "表示順";
$ary["ITABASEH-MNU-310111"]         = "比較実行時の表示順";
$ary["ITABASEH-MNU-310200"]         = "比較定義で設定した情報を元に、パラメータシートの比較を実行します。<br>比較実行のパラメータは以下です。※基準日を指定しない場合、一番最新の基準日のデータとなります。<br>・比較定義<br>・基準日1(比較対象メニュー1の基準日を指定)<br>・基準日2(比較対象メニュー2の基準日を指定)<br>・対象ホスト";
$ary["ITABASEH-MNU-310201"]         = "比較実行";
$ary["ITABASEH-MNU-310202"]         = "比較定義：";
$ary["ITABASEH-MNU-310203"]         = "基準日1：";
$ary["ITABASEH-MNU-310204"]         = "基準日2：";
$ary["ITABASEH-MNU-310205"]         = "対象ホスト：";
$ary["ITABASEH-MNU-310206"]         = "選択";
$ary["ITABASEH-MNU-310207"]         = "比較";
$ary["ITABASEH-MNU-310208"]         = "比較結果";
$ary["ITABASEH-MNU-310209"]         = "※ここに、パラメータシートの比較実行結果が出力されます。";
$ary["ITABASEH-MNU-310210"]         = "比較項番";
$ary["ITABASEH-MNU-310211"]         = "結果";
$ary["ITABASEH-MNU-310212"]         = "ホスト名";
$ary["ITABASEH-MNU-310213"]         = "メニュー名称";
$ary["ITABASEH-MNU-310214"]         = "No";
$ary["ITABASEH-MNU-310215"]         = "オペレーション名";
$ary["ITABASEH-MNU-310216"]         = "基準日";
$ary["ITABASEH-MNU-310217"]         = "差分あり";
$ary["ITABASEH-MNU-310218"]         = "ID変換失敗({})";
$ary["ITABASEH-MNU-310219"]         = "Excel出力";
$ary["ITABASEH-MNU-310220"]         = "CSV出力";
$ary["ITABASEH-MNU-310221"]         = "指定した条件で比較対象となるデータがありません";
$ary["ITABASEH-MNU-310222"]         = "メイリオ";
$ary["ITABASEH-MNU-310223"]         = "差分なし";
$ary["ITABASEH-MNU-310224"]         = "ID変換失敗(";
$ary["ITABASEH-MNU-310225"]         = "出力内容：";
$ary["ITABASEH-MNU-310226"]         = "全件出力";
$ary["ITABASEH-MNU-310227"]         = "差分のみ";
$ary["ITABASEH-MNU-311000"]         = 'PHPのcURL 関数を使用して、通知処理を行います。<br>
■Webhookを利用した通知の設定例<br>
▼Teams / Slack　の例 <br>
&nbsp -通知先(CURLOPT_URL) の入力例: <br>
&nbsp&nbsp&nbsp&nbsp各サービスのWebhook URL を入力<br>
&nbsp -ヘッダー(CURLOPT_HTTPHEADER)) の入力例: <br>
&nbsp&nbsp&nbsp&nbsp[ "Content-Type: application/json" ]<br>
&nbsp -メッセージ(CURLOPT_POSTFIELDS)) の入力例: <br>
&nbsp&nbsp&nbsp&nbsp{"text": "通知名：__NOTICE_NAME__,  &lt;br&gt;Conductor名称: __CONDUCTOR_NAME__,  &lt;br&gt; ConductorインスタンスID:__CONDUCTOR_INSTANCE_ID__,&lt;br&gt; ステータス: __STATUS_NAME__, &lt;br&gt; 作業URL: __JUMP_URL__, &lt;br&gt; "}<br><br>

※メッセージ(CURLOPT_POSTFIELDS)の入力形式、改行の表記方法については、各サービスのWebhookによるメッセージの送信についてご参照ください。<br>
<br>
■各設定項目について<br>
<table>
    <thead>
        <tr><td>&nbsp</td><td>入力項目</td><td>:</td><td>説明</td></tr>
    </thead>
    <tbody>
    <tr><td>&nbsp</td><td>通知先(CURLOPT_URL) </td><td>:</td><td>通知先のURLを入力してください。</td></tr>
    <tr><td>&nbsp</td><td>ヘッダー(CURLOPT_HTTPHEADER) </td><td>:</td><td>ヘッダーを入力してください</td></tr>
    <tr><td>&nbsp</td><td>メッセージ(CURLOPT_POSTFIELDS) </td><td>:</td><td>通知内容を入力してください</td></tr>
    <tr><td>&nbsp</td><td>PROXY / URL(CURLOPT_PROXY) </td><td>:</td><td>PROXYの設定が必要な場合、URLを入力してください。</td></tr>
    <tr><td>&nbsp</td><td>PROXY / PORT(CURLOPT_PROXYPORT) </td><td>:</td><td>PROXYの設定が必要な場合、PORTを入力してください。</td></tr>
    <tr><td>&nbsp</td><td>作業確認URL(FQDN) </td><td>:</td><td>作業確認用URLの予約変数で使用する,FQDNを入力してください</td></tr>
    <tr><td>&nbsp</td><td>その他 </td><td>:</td><td>JSON形式で入力してください。<br>（使用できるオプションについては、curl_setopt() に対応するもののみ使用可能です。<br>詳しくは、PHPのcURL 関数についてご参照ください。</td></tr>
    </tbody>
</table>
<br>
■メッセージ(CURLOPT_POSTFIELDS)にて以下の予約変数が利用可能です。　<br>
<table>
    <thead>
        <tr><td>&nbsp</td><td>予約変数</td><td>:</td><td>項目名</td></tr>
    </thead>
    <tbody>
        <tr><td>&nbsp</td><td>__CONDUCTOR_INSTANCE_ID__ </td><td>:</td><td>ConductorインスタンスID　</td></tr>
        <tr><td>&nbsp</td><td>__CONDUCTOR_NAME__ </td><td>:</td><td>Conductorインスタンス名　</td></tr>
        <tr><td>&nbsp</td><td>__OPERATION_ID__ </td><td>:</td><td>オペレーションID　</td></tr>
        <tr><td>&nbsp</td><td>__OPERATION_NAME__ </td><td>:</td><td>オペレーション名　</td></tr>
        <tr><td>&nbsp</td><td>__STATUS_ID__ </td><td>:</td><td>ステータスID　</td></tr>
        <tr><td>&nbsp</td><td>__STATUS_NAME__ </td><td>:</td><td>ステータス名　</td></tr>
        <tr><td>&nbsp</td><td>__EXECUTION_USER__ </td><td>:</td><td>実行ユーザー　</td></tr>
        <tr><td>&nbsp</td><td>__TIME_BOOK__ </td><td>:</td><td>予約日時　</td></tr>
        <tr><td>&nbsp</td><td>__TIME_START__ </td><td>:</td><td>開始日時　</td></tr>
        <tr><td>&nbsp</td><td>__TIME_END__ </td><td>:</td><td>終了日時　</td></tr>
        <tr><td>&nbsp</td><td>__JUMP_URL__ </td><td>:</td><td>作業確認URL　</td></tr>
        <tr><td>&nbsp</td><td>__NOTICE_NAME__ </td><td>:</td><td>通知名称　</td></tr>
    </tbody>
</table>
';
$ary["ITABASEH-MNU-311001"]         = "Conductor通知先定義";
$ary["ITABASEH-MNU-311002"]         = "通知名称";
$ary["ITABASEH-MNU-311003"]         = "[最大長]128バイト";
$ary["ITABASEH-MNU-311004"]         = "HTTPリクエストオプション";
$ary["ITABASEH-MNU-311005"]         = "通知先(CURLOPT_URL)";
$ary["ITABASEH-MNU-311006"]         = "通知先のURLです。";
$ary["ITABASEH-MNU-311007"]         = "ヘッダー(CURLOPT_HTTPHEADER)";
$ary["ITABASEH-MNU-311008"]         = "HTTP ヘッダフィールドをJSON形式で入力してください。";
$ary["ITABASEH-MNU-311009"]         = "メッセージ(CURLOPT_POSTFIELDS)";
$ary["ITABASEH-MNU-311010"]         = "通知先のサービスの仕様に沿って入力してください。";
$ary["ITABASEH-MNU-311011"]         = "PROXY";
$ary["ITABASEH-MNU-311012"]         = "URL(CURLOPT_PROXY)";
$ary["ITABASEH-MNU-311013"]         = "PROXYの設定が必要な場合、URLを入力してください。";
$ary["ITABASEH-MNU-311014"]         = "PORT(CURLOPT_PROXYPORT)";
$ary["ITABASEH-MNU-311015"]         = "PROXYの設定が必要な場合、PORTを入力してください。";
$ary["ITABASEH-MNU-311016"]         = "作業確認URL(FQDN)";
$ary["ITABASEH-MNU-311017"]         = "作業確認用URLの予約変数で使用する,FQDNを入力してください。\n例:\nhttp://<FQDN>\nhttps://<FQDN>";
$ary["ITABASEH-MNU-311018"]         = "その他";
$ary["ITABASEH-MNU-311019"]         ='JSON形式で入力してください。\n使用できるオプションについては、curl_setopt() のオプションに対応するものは使用可能です。PHPのcURL 関数について参照してください。
例：{"CURLOPT_CONNECTTIMEOUT": 10}';
$ary["ITABASEH-MNU-311020"]         = "抑止期間";
$ary["ITABASEH-MNU-311021"]         = "開始日時";
$ary["ITABASEH-MNU-311022"]         = "Conductor作業実行時に、開始日時以降であれば、通知を抑止します。";
$ary["ITABASEH-MNU-311023"]         = "終了日時";
$ary["ITABASEH-MNU-311024"]         = "Conductor作業実行時に、終了日時以前であれば、通知を抑止します。";
$ary["ITABASEH-MNU-900001"]         = "エクスポート";
$ary["ITABASEH-MNU-900002"]         = "アップロード";
$ary["ITABASEH-MNU-900003"]         = "インポート";
$ary["ITABASEH-MNU-900004"]         = "インポートするファイルをアップロードしてください。";
$ary["ITABASEH-MNU-900005"]         = "ファイルをアップロードしてください。";
$ary["ITABASEH-MNU-900006"]         = "メニューエクスポート";
$ary["ITABASEH-MNU-900007"]         = "メニューインポート";
$ary["ITABASEH-MNU-900008"]         = "エクスポート・インポート管理";
$ary["ITABASEH-MNU-900009"]         = "メニューインポート処理を受け付けました。<br>実行No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900010"]         = "下記の機能を提供しています。<br>・エクスポート/インポートデータの閲覧";
$ary["ITABASEH-MNU-900011"]         = "下記の機能を提供しています。<br>・インポートデータのアップロード<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするデータを圧縮したkymファイルをアップロードしてください。<br><br>・メニューインポート<br>&nbsp;&nbsp;&nbsp;&nbsp;インポート可能なメニューが一覧表示されます。<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするメニューを選択し、インポートボタンをクリックしてください。<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするデータの状態はメニューインポート管理で確認できます。";
$ary["ITABASEH-MNU-900012"]         = "下記の機能を提供しています。<br>&nbsp;&nbsp;&nbsp;&nbsp;・メニューエクスポート<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;データをエクスポートするメニューを選択し、エクスポートボタンをクリックしてください。<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;モード<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・環境移行<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;指定メニューのすべてのデータをエクスポートします。インポート先のデータをすべて置き換えます。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・時刻指定<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;指定時刻以降のデータのみエクスポートします。インポート先のデータとIDが被った場合はエクスポートしたデータが優先してインポートされます。<br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;廃止情報<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・廃止を含む<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;廃止したレコードを含めてエクスポートします。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・廃止を除く<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;廃止したレコードを除いてエクスポートします。";
$ary["ITABASEH-MNU-900013"]         = "実行No.";
$ary["ITABASEH-MNU-900014"]         = "ステータス";
$ary["ITABASEH-MNU-900015"]         = "ファイル名";
$ary["ITABASEH-MNU-900016"]         = "ステータスには以下の状態が存在します。\n・未実行\n・実行中\n・完了\n・完了(異常)";
$ary["ITABASEH-MNU-900017"]         = "自動登録のため編集不可。";
$ary["ITABASEH-MNU-900018"]         = "すべてのメニュー";
$ary["ITABASEH-MNU-900019"]         = "インポート(廃止を除く)";
$ary["ITABASEH-MNU-900020"]         = "インポート種別";
$ary["ITABASEH-MNU-900021"]         = "インポート種別には以下が存在します。\n・通常\n・廃止を除く";
$ary["ITABASEH-MNU-900022"]         = "処理種別";
$ary["ITABASEH-MNU-900023"]         = "処理種別には以下が存在します。\n・エクスポート\n・インポート";
$ary["ITABASEH-MNU-900024"]         = "メニューエクスポート処理を受け付けました。<br>実行No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900025"]         = "モード";
$ary["ITABASEH-MNU-900026"]         = "環境移行";
$ary["ITABASEH-MNU-900027"]         = "時刻指定";
$ary["ITABASEH-MNU-900028"]         = "廃止情報";
$ary["ITABASEH-MNU-900029"]         = "廃止を含む";
$ary["ITABASEH-MNU-900030"]         = "廃止を除く";
$ary["ITABASEH-MNU-900031"]         = "モードには以下が存在します。\n・環境移行\n・時刻指定";
$ary["ITABASEH-MNU-900032"]         = "廃止情報には以下が存在します。\n・廃止を含む\n・廃止を除く";
$ary["ITABASEH-MNU-900033"]         = "指定時刻";
$ary["ITABASEH-MNU-900034"]         = "モードが時刻指定の場合、指定時刻以降のレコードがエクスポート/インポートされます。";
$ary["ITABASEH-MNU-900035"]         = "実行ユーザ";
$ary["ITABASEH-MNU-900036"]         = "エクスポート処理またはインポート処理を実行したユーザが表示されます。";
$ary["ITABASEH-MNU-900051"]         = "下記の機能を提供しています。<br>&nbsp;&nbsp;・Symphony/オペレーションエクスポート<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;エクスポートするSymphonyとオペレーションを選択し、エクスポートボタンをクリックしてください。";
$ary["ITABASEH-MNU-900052"]         = "下記の機能を提供しています。<br>・Symphony/オペレーションのインポートデータのアップロード<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;インポートするデータを圧縮したkym2ファイルをアップロードしてください。<br><br>・Symphony/オペレーションインポート<br>&nbsp;&nbsp;&nbsp;&nbsp;インポート可能なSymphony/オペレーションが一覧表示されます。<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするSymphony/オペレーションを選択し、インポートボタンをクリックしてください。<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするデータの状態はSymphony/オペレーションエクスポート・インポート管理で確認できます。";
$ary["ITABASEH-MNU-900053"]         = "すべてのオペレーション";
$ary["ITABASEH-MNU-900054"]         = "すべてのSymphony";
$ary["ITABASEH-MNU-900055"]         = "下記の機能を提供しています。<br>・エクスポート/インポートデータの閲覧";
$ary["ITABASEH-MNU-900056"]         = "Symphony/オペレーションエクスポート・インポート管理";
$ary["ITABASEH-MNU-900057"]         = "Symphony/オペレーションエクスポート";
$ary["ITABASEH-MNU-900058"]         = "Symphony/オペレーションエクスポート処理を受け付けました。<br>実行No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900059"]         = "Symphony/オペレーションインポート";
$ary["ITABASEH-MNU-900060"]         = "Symphony/オペレーションインポート処理を受け付けました。<br>実行No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-900100"]         = "Orchestrator ID";
$ary["ITABASEH-MNU-900101"]         = "Movement ID";
$ary["ITABASEH-MNU-900102"]         = "一時停止(OFF:/ON:checkedValue)";
$ary["ITABASEH-MNU-900103"]         = "説明";
$ary["ITABASEH-MNU-900104"]         = "オペレーションID(個別指定)";
$ary["ITABASEH-MNU-910001"]         = "バージョン情報";
$ary["ITABASEH-MNU-910002"]         = "ドライバ";
$ary["ITABASEH-MNU-910003"]         = "バージョン";
$ary["ITABASEH-MNU-910004"]         = "Exastro IT Automation Version";
$ary["ITABASEH-MNU-910005"]         = "インストール済ドライバ";
$ary["ITABASEH-MNU-920001"]         = "Symphonyをスケジュールにしたがって定期的に実行させることができます。<br>対象のsymphony, operationを選択し、「スケジュール設定」から詳細な設定を入力してください。";
$ary["ITABASEH-MNU-920002"]         = "定期作業実行ID";
$ary["ITABASEH-MNU-920003"]         = "Symphony定期作業実行";
$ary["ITABASEH-MNU-920004"]         = "Symphony定期作業実行";
$ary["ITABASEH-MNU-920005"]         = "作業一覧確認";
$ary["ITABASEH-MNU-920006"]         = "ステータス";
$ary["ITABASEH-MNU-920007"]         = "ステータスには以下の状態が存在します。\n
・準備中\n
・稼働中\n
・完了\n
・不整合エラー\n
・紐付けエラー\n
・想定外エラー\n
・symphony廃止\n
・operation廃止";
$ary["ITABASEH-MNU-920008"]         = "スケジュール設定";
$ary["ITABASEH-MNU-920009"]         = "スケジュール";
$ary["ITABASEH-MNU-920010"]         = "次回実行日付";
$ary["ITABASEH-MNU-920011"]         = "開始日付";
$ary["ITABASEH-MNU-920012"]         = "終了日付";
$ary["ITABASEH-MNU-920013"]         = "周期";
$ary["ITABASEH-MNU-920014"]         = "間隔";
$ary["ITABASEH-MNU-920015"]         = "週番号";
$ary["ITABASEH-MNU-920016"]         = "曜日";
$ary["ITABASEH-MNU-920017"]         = "日";
$ary["ITABASEH-MNU-920018"]         = "時間";
$ary["ITABASEH-MNU-920019"]         = "作業停止期間";
$ary["ITABASEH-MNU-920020"]         = "開始";
$ary["ITABASEH-MNU-920021"]         = "終了";
$ary["ITABASEH-MNU-920022"]         = "Symphony名称";
$ary["ITABASEH-MNU-920023"]         = "[元データ]Symphonyクラス一覧";
$ary["ITABASEH-MNU-920024"]         = "オペレーション名";
$ary["ITABASEH-MNU-920025"]         = "[元データ]オペレーション一覧";
$ary["ITABASEH-MNU-920026"]         = "自動入力";
$ary["ITABASEH-MNU-920027"]         = "実行ユーザ";
$ary["ITABASEH-MNU-920028"]         = "Symphonyを実行するユーザ（登録/更新したユーザが自動的に入力されます）";
$ary["ITABASEH-MNU-2100000329_1"]   = "下記の機能を提供しています。<br>・Excel一括エクスポート<br>&nbsp;&nbsp;&nbsp;&nbsp;データをエクスポートするメニューを選択し、エクスポートボタンをクリックしてください。<br>廃止情報<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・全レコード<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;全てのレコードをエクスポートします。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・廃止を除く<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;廃止したレコードを除いてエクスポートします。<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;・廃止のみ<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;廃止したレコードのみエクスポートします。";
$ary["ITABASEH-MNU-2100000329_2"]   = "Excel一括エクスポート";
$ary["ITABASEH-MNU-2100000329_3"]   = "Excel一括エクスポート処理を受け付けました。<br>実行No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-2100000329_4"]   = "全レコード";
$ary["ITABASEH-MNU-2100000329_5"]   = "廃止を除く";
$ary["ITABASEH-MNU-2100000329_6"]   = "廃止のみ";
$ary["ITABASEH-MNU-2100000330_1"]   = "インポート対象";
$ary["ITABASEH-MNU-2100000330_2"]   = "メニューグループ名";
$ary["ITABASEH-MNU-2100000330_3"]   = "メニュー名";
$ary["ITABASEH-MNU-2100000330_4"]   = "メニューID";
$ary["ITABASEH-MNU-2100000330_5"]   = "ファイル名";
$ary["ITABASEH-MNU-2100000330_6"]   = "エラー内容";
$ary["ITABASEH-MNU-2100000330_7"]   = "下記の機能を提供しています。<br>・インポートデータのアップロード<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするデータを圧縮したzipファイルをアップロードしてください。<br><br>・Excel一括インポート<br>&nbsp;&nbsp;&nbsp;&nbsp;インポート可能なメニューが一覧表示されます。<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするメニューを選択し、インポートボタンをクリックしてください。<br>&nbsp;&nbsp;&nbsp;&nbsp;インポートするデータの状態はExcel一括エクスポート・インポート管理で確認できます。";
$ary["ITABASEH-MNU-2100000330_8"]   = "インポートを実行しますか？\n※MENU_LIST.txtに記載されている順にインポート作業を実行します。\n複数のメニューデータをインポートする場合は、データの整合性をご理解頂いた上で実行するようお願いいたします。";
$ary["ITABASEH-MNU-2100000330_9"]   = "Excel一括インポート";
$ary["ITABASEH-MNU-2100000330_10"]  = "Excel一括インポート処理を受け付けました。<br>実行No.：[<strong>{}</strong>]";
$ary["ITABASEH-MNU-2100000331_1"]   = "下記の機能を提供しています。<br>・Excel一括エクスポート・インポートデータの閲覧";
$ary["ITABASEH-MNU-2100000331_2"]   = "Excel一括エクスポート・インポート管理";
$ary["ITABASEH-MNU-2100000331_3"]   = "廃止情報には以下が存在します。\n・全レコード\n・廃止を除く\n・廃止のみ";
$ary["ITABASEH-MNU-2100000331_4"]   = "実行ユーザ";
$ary["ITABASEH-MNU-2100000331_5"]   = "エクスポート処理またはインポート処理を実行したユーザが表示されます。";
?>
