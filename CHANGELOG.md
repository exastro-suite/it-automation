Exastro IT Automation 1.3.0 (2020-01-31)
==================================================

インストーラ
---------------
  * 対応OSからRHEL6/CentOS6を削除
  * 対応OSにRHEL8/CentOS8を追加
  * PHP5.6からPHP7.2に変更
  * Python2版のAnsibleからPython3版のAnsibleに変更

全般
---------------
  * Web上の表で、項目の表示・非表示を設定できる機能を追加

管理コンソール
---------------
  * メニューエクスポート、メニューインポートのRestAPIを追加

基本コンソール
---------------
  * Symphony、Operationに紐づく情報をエクスポート・インポートする機能を追加
  * Symphonyクラス編集で設定するMovementのデザインを変更
  * Symphonyクラス編集でMovement名が長い場合の表示を改善
  * Symphonyクラス編集のRestAPIを追加

メニュー作成
---------------
  * パラメータシート作成とマスタ作成をメニュー作成に統合
  * データシート（代入値自動登録で使用しないメニュー）の作成機能を追加

Ansible-Driver
---------------
  * パスワード項目をansible-vaultにより暗号化を行う機能を追加
  * hostsファイルのyaml化
  * Ansible-LegacyRoleで、ロール内のansible.cfgを使用できるように改善
  * AnsibleTowerと連携した際に、Movement毎にvirtualenvを指定できる機能を追加
  * 作業実行、作業状態確認のRestAPIを追加

DSC-Driver
---------------
  * 作業実行、作業状態確認のRestAPIを追加

OpenStack-Driver
---------------
  * 作業実行、作業状態確認のRestAPIを追加
