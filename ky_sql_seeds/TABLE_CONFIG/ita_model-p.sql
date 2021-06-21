-- *****************************************************************************
-- *** ***** CI/CD For IaC Create Tables                                     ***
-- *****************************************************************************
-- -------------------------------------------------------
-- -- インターフェース情報
-- -------------------------------------------------------
CREATE TABLE B_CICD_IF_INFO
(
IF_INFO_ROW_ID                    %INT%                             , -- 識別シーケンス項番
-- --
HOSTNAME                          %VARCHR%(128)                     , -- 機器一覧のホスト名に合わせる
PROTOCOL                          %VARCHR%(8)                       ,
PORT                              %INT%                             ,
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (IF_INFO_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CICD_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別
-- --
IF_INFO_ROW_ID                    %INT%                             , -- 識別シーケンス項番
-- --
HOSTNAME                          %VARCHR%(128)                     , -- 機器一覧のホスト名に合わせる
PROTOCOL                          %VARCHR%(8)                       ,
PORT                              %INT%                             ,
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------------
-- -- RestAPIユーザー管理
-- -------------------------------------------------------
CREATE TABLE B_CICD_REST_ACCOUNT_LIST
(
ACCT_ROW_ID                       %INT%                             , -- 識別シーケンス項番
-- --
USER_ID                           %INT%                             , -- ユーザー管理(A_ACCOUNT_LIST) USER_ID
LOGIN_PW                          %VARCHR%(32)                      , -- ユーザー管理(A_ACCOUNT_LIST)に合わせる
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (ACCT_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CICD_REST_ACCOUNT_LIST_JNL
(
JOURNAL_SEQ_NO                    %INT%                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                       , -- 履歴用変更種別
-- --
ACCT_ROW_ID                       %INT%                             , -- 識別シーケンス項番
-- --
USER_ID                           %INT%                             , -- ユーザー管理(A_ACCOUNT_LIST) USER_ID
LOGIN_PW                          %VARCHR%(32)                      , -- ユーザー管理(A_ACCOUNT_LIST)に合わせる
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          %INT%                             , -- 表示順序
NOTE                              %VARCHR%(4000)                    , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                       , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                             , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------
-- -- リモートリポジトリ管理
-- -------------------------------------------------
CREATE TABLE B_CICD_REPOSITORY_LIST
(
REPO_ROW_ID                        %INT%                            , -- 識別シーケンス項番
-- --
REPO_NAME                          %VARCHR%(256)                    , -- リモートリポジトリ名
REMORT_REPO_URL                    %VARCHR%(256)                    , -- リモートリポジトリ(URL)
BRANCH_NAME                        %VARCHR%(256)                    , -- ブランチ
GIT_PROTOCOL_TYPE_ROW_ID           %INT%                            , -- プロトコルタイプ
GIT_REPO_TYPE_ROW_ID               %INT%                            , -- リポジトリタイプ
GIT_USER                           %VARCHR%(128)                    , -- Git ユーザー
GIT_PASSWORD                       %VARCHR%(128)                    , -- Git パスワード
AUTO_SYNC_FLG                      %INT%                            , -- 自動同期有無
SYNC_INTERVAL                      %INT%                            , -- 同期周期(単位:分)
SYNC_STATUS_ROW_ID                 %INT%                            , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
SYNC_LAST_TIME                     %DATETIME6%                      , -- 最終同期時間
PROXY_ADDRESS                      %VARCHR%(128)                    , -- プロキシーアドレス
PROXY_PORT                         %INT%                            , -- プロキシーポート
RETRAY_INTERVAL                    %INT%                            , -- リトライ周期 単位:ms
RETRAY_COUNT                       %INT%                            , -- リトライ回数
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           %INT%                            , -- 表示順序
NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (REPO_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


CREATE TABLE B_CICD_REPOSITORY_LIST_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別
-- --
REPO_ROW_ID                        %INT%                            , -- 識別シーケンス項番
-- --
REPO_NAME                          %VARCHR%(256)                    , -- リモートリポジトリ名
REMORT_REPO_URL                    %VARCHR%(256)                    , -- リモートリポジトリ(URL)
BRANCH_NAME                        %VARCHR%(256)                    , -- ブランチ
GIT_PROTOCOL_TYPE_ROW_ID           %INT%                            , -- プロトコルタイプ
GIT_REPO_TYPE_ROW_ID               %INT%                            , -- リポジトリタイプ
GIT_USER                           %VARCHR%(128)                    , -- Git ユーザー
GIT_PASSWORD                       %VARCHR%(128)                    , -- Git パスワード
AUTO_SYNC_FLG                      %INT%                            , -- 自動同期有無
SYNC_INTERVAL                      %INT%                            , -- 同期周期(単位:分)
SYNC_STATUS_ROW_ID                 %INT%                            , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
SYNC_LAST_TIME                     %DATETIME6%                      , -- 最終同期時間
PROXY_ADDRESS                      %VARCHR%(128)                    , -- プロキシーアドレス
PROXY_PORT                         %INT%                            , -- プロキシーポート
RETRAY_INTERVAL                    %INT%                            , -- リトライ周期 単位:ms
RETRAY_COUNT                       %INT%                            , -- リトライ回数
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           %INT%                            , -- 表示順序
NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------
-- -- 資材管理
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_LIST
(
MATL_ROW_ID                        %INT%                            , -- 識別シーケンス項番
-- --
REPO_ROW_ID                        %INT%                            , -- リモートリポジトリ「インターフェース情報」　Pkey
MATL_FILE_PATH                     %VARCHR% (4096)                  , -- 資材パス
MATL_FILE_TYPE_ROW_ID              %INT%                            , -- 資材タイプ　1:ファイル・2:Rolesディレクトリ
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           %INT%                            , -- 表示順序
NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY (MATL_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CICD_MATERIAL_LIST_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR% (8)                     , -- 履歴用変更種別
-- --
MATL_ROW_ID                        %INT%                            , -- 識別シーケンス項番
-- --
REPO_ROW_ID                        %INT%                            , -- リモートリポジトリ「インターフェース情報」　Pkey
MATL_FILE_PATH                     %VARCHR% (4096)                  , -- 資材パス
MATL_FILE_TYPE_ROW_ID              %INT%                            , -- 資材タイプ　1:ファイル・2:Rolesディレクトリ
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           %INT%                            , -- 表示順序
NOTE                               %VARCHR% (4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR% (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------
-- -- 資材紐付け管理
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_LINK_LIST
(
MATL_LINK_ROW_ID                   %INT%                            , -- 識別シーケンス項番
--
MATL_LINK_NAME                     %VARCHR%(256)                    , -- 素材名
REPO_ROW_ID                        %INT%                            , -- リポジトリ一覧 シーケンス
MATL_ROW_ID                        %INT%                            , -- 資材一覧 シーケンス
MATL_TYPE_ROW_ID                   %INT%                            , -- 紐付け先 素材集タイプ
TEMPLATE_FILE_VARS_LIST            %VARCHR%(8192)                   , -- トンプレート変数定義
DIALOG_TYPE_ID                     %INT%                            , -- 対話種別
OS_TYPE_ID                         %INT%                            , -- OS種別
ACCT_ROW_ID                        %INT%                            , -- Restユーザ
RBAC_FLG_ROW_ID                    %INT%                            , -- アクセス許可ロール付与フラグ　
-- 同期状態
AUTO_SYNC_FLG                      %INT%                            , -- 自動同期有無
SYNC_STATUS_ROW_ID                 %INT%                            , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
SYNC_LAST_TIME                     %DATETIME6%                      , -- 最終同期時間
SYNC_LAST_UPDATE_USER              %INT%                            , -- 最終更新ユーザ
-- デリバリ情報
DEL_OPE_ID                         %INT%                            , -- 構築時のオペレーションID
DEL_MOVE_ID                        %INT%                            , -- 構築時のMovementID
DEL_EXEC_TYPE                      %INT%                            , -- 構築時の実行タイプ　ドライラン
DEL_ERROR_NOTE                     TEXT                             , -- 構築エラー時の内容
DEL_URL                            %VARCHR% (256)                   , -- 構築時の実行結果　URL
--
ACCESS_AUTH                        TEXT                             ,
NOTE                               %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (MATL_LINK_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

CREATE TABLE B_CICD_MATERIAL_LINK_LIST_JNL
(
JOURNAL_SEQ_NO                     %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                      , -- 履歴用変更種別
-- 
MATL_LINK_ROW_ID                   %INT%                            , -- 識別シーケンス項番
--
MATL_LINK_NAME                     %VARCHR%(256)                    , -- 素材名
REPO_ROW_ID                        %INT%                            , -- リポジトリ一覧 シーケンス
MATL_ROW_ID                        %INT%                            , -- 資材一覧 シーケンス
MATL_TYPE_ROW_ID                   %INT%                            , -- 紐付け先 素材集タイプ
TEMPLATE_FILE_VARS_LIST            %VARCHR%(8192)                   , -- トンプレート変数定義
DIALOG_TYPE_ID                     %INT%                            , -- 対話種別
OS_TYPE_ID                         %INT%                            , -- OS種別
ACCT_ROW_ID                        %INT%                            , -- Restユーザ
RBAC_FLG_ROW_ID                    %INT%                            , -- アクセス許可ロール付与フラグ　
-- 同期状態
AUTO_SYNC_FLG                      %INT%                            , -- 自動同期有無
SYNC_STATUS_ROW_ID                 %INT%                            , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
SYNC_LAST_TIME                     %DATETIME6%                      , -- 最終同期時間
SYNC_LAST_UPDATE_USER              %INT%                            , -- 最終更新ユーザ
-- デリバリ情報
DEL_OPE_ID                         %INT%                            , -- 構築時のオペレーションID
DEL_MOVE_ID                        %INT%                            , -- 構築時のMovementID
DEL_EXEC_TYPE                      %INT%                            , -- 構築時の実行タイプ　ドライラン
DEL_ERROR_NOTE                     TEXT                             , -- 構築エラー時の内容
DEL_URL                            %VARCHR% (256)                   , -- 構築時の実行結果　URL
--
ACCESS_AUTH                        TEXT                             ,
NOTE                               %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;

-- -------------------------------------------------
-- -- リポジトリ同期状態マスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_REPO_SYNC_STATUS_NAME
(
SYNC_STATUS_ROW_ID                 %INT%                            ,
-- --
SYNC_STATUS_NAME                   %VARCHR%(32)                     ,
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           %INT%                            , -- 表示順序
NOTE                               %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                            , -- 最終更新ユーザ

PRIMARY KEY (SYNC_STATUS_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_REPO_SYNC_STATUS_NAME_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
-- --
SYNC_STATUS_ROW_ID                %INT%                            ,
-- --
SYNC_STATUS_NAME                  %VARCHR%(32)                     ,
-- --
ACCESS_AUTH                       TEXT                             ,
DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- Git資材ファイルタイプマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_FILE_TYPE_NAME
(
MATL_FILE_TYPE_ROW_ID              %INT%                           , -- 識別シーケンス項番
-- --
MATL_FILE_TYPE_NAME                %VARCHR%(128)                   , -- 資材タイプ名　1:ファイル 2:Rolesディレクトリ
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (MATL_FILE_TYPE_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_MATERIAL_FILE_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                    %INT%                            , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              %DATETIME6%                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              %VARCHR%(8)                      , -- 履歴用変更種別
-- --
MATL_FILE_TYPE_ROW_ID             %INT%                            , -- 識別シーケンス項番
-- --
MATL_FILE_TYPE_NAME               %VARCHR%(128)                    , -- 資材タイプ名　1:ファイル 2:Rolesディレクトリ
-- --
ACCESS_AUTH                       TEXT                             ,
DISP_SEQ                          %INT%                            , -- 表示順序
NOTE                              %VARCHR%(4000)                   , -- 備考
DISUSE_FLAG                       %VARCHR%(1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             %DATETIME6%                      , -- 最終更新日時
LAST_UPDATE_USER                  %INT%                            , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- ITA側素材タイプマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_TYPE_NAME
(
MATL_TYPE_ROW_ID                   %INT%                           , -- 識別シーケンス項番
-- --
MATL_TYPE_NAME                     %VARCHR%(128)                   , -- 資材タイプ名 1:Playbook素材集 2:対話ﾌｧｲﾙ素材集 3:ロールパッケージ管理 4:ﾌｧｲﾙ管理 5:ﾃﾝﾌﾟﾚｰﾄ管理
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (MATL_TYPE_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_MATERIAL_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別
-- --
MATL_TYPE_ROW_ID                   %INT%                           , -- 識別シーケンス項番
-- --
MATL_TYPE_NAME                     %VARCHR%(128)                   , -- 資材タイプ名　1:Playbook素材集 2:対話ﾌｧｲﾙ素材集 3:ロールパッケージ管理 4:ﾌｧｲﾙ管理 5:ﾃﾝﾌﾟﾚｰﾄ管理
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- Gitプロトコルマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_GIT_PROTOCOL_TYPE_NAME
(
GIT_PROTOCOL_TYPE_ROW_ID           %INT%                           , -- 識別シーケンス項番
-- --
GIT_PROTOCOL_TYPE_NAME             %VARCHR%(128)                   , -- プロトコル名 1:https 2:ssh 3:local
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (GIT_PROTOCOL_TYPE_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別
-- --
GIT_PROTOCOL_TYPE_ROW_ID           %INT%                           , -- 識別シーケンス項番
-- --
GIT_PROTOCOL_TYPE_NAME             %VARCHR%(128)                   , -- プロトコル名 1:https 2:ssh 3:local
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- Gitリポジトリタイプマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_GIT_REPOSITORY_TYPE_NAME
(
GIT_REPO_TYPE_ROW_ID               %INT%                           , -- 識別シーケンス項番
-- --
GIT_REPO_TYPE_NAME                 %VARCHR%(128)                   , -- リポジトリタイプ名 1:public　2:private
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (GIT_REPO_TYPE_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_GIT_REPOSITORY_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別
-- --
GIT_REPO_TYPE_ROW_ID               %INT%                           , -- 識別シーケンス項番
-- --
GIT_REPO_TYPE_NAME                 %VARCHR%(128)                   , -- リポジトリタイプ名 1:public　2:private
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- アクセス許可ロール付与フラグマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_RBAC_FLG_NAME
(
RBAC_FLG_ROW_ID                    %INT%                           , -- 識別シーケンス項番
-- --
RBAC_FLG_NAME                      %VARCHR%(16)                    , -- アクセス許可ロール付与フラグ名 1:なし　2:あり
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ

PRIMARY KEY (RBAC_FLG_ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_RBAC_FLG_NAME_JNL
(
JOURNAL_SEQ_NO                     %INT%                           , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               %DATETIME6%                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               %VARCHR%(8)                     , -- 履歴用変更種別
-- --
RBAC_FLG_ROW_ID                    %INT%                           , -- 識別シーケンス項番
-- --
RBAC_FLG_NAME                      %VARCHR%(16)                    , -- アクセス許可ロール付与フラグ名 1:なし　2:あり
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           %INT%                           , -- 表示順序
NOTE                               %VARCHR%(4000)                  , -- 備考
DISUSE_FLAG                        %VARCHR%(1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              %DATETIME6%                     , -- 最終更新日時
LAST_UPDATE_USER                   %INT%                           , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)%%TABLE_CREATE_OUT_TAIL%%;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- 同期状態管理テーブル(履歴なし)
-- -------------------------------------------------
CREATE TABLE T_CICD_SYNC_STATUS (
ROW_ID                             %INT%                           , -- リポジトリ一覧 項番
SYNC_LAST_TIMESTAMP                %DATETIME6%                     , -- 最終同期日時
PRIMARY KEY(ROW_ID)
)%%TABLE_CREATE_OUT_TAIL%%;


-- *****************************************************************************
-- *** ***** CI/CD For IaC Create view                                       ***
-- *****************************************************************************
-- -------------------------------------------------
-- -- リモートリポジトリ管理と同期状態管理テーブルの結合View
-- -------------------------------------------------
CREATE VIEW D_CICD_REPOLIST_SYNCSTS_LINK AS
SELECT
                                                                               -- B_CICD_REPOSITORY_LIST Columns
    TAB_A.*,
                                                                               -- T_CICD_SYNC_STATUS Columns
    TAB_B.ROW_ID                                                             , -- リポジトリ一覧 項番
    TAB_B.SYNC_LAST_TIMESTAMP                                                  -- 最終同期日時
FROM
    B_CICD_REPOSITORY_LIST TAB_A
    LEFT JOIN T_CICD_SYNC_STATUS TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.ROW_ID );

CREATE VIEW D_CICD_REPOLIST_SYNCSTS_LINK_JNL AS
SELECT
                                                                               -- B_CICD_REPOSITORY_LIST Columns
    TAB_A.*,
                                                                               -- T_CICD_SYNC_STATUS Columns
    TAB_B.ROW_ID                                                             , -- リポジトリ一覧 項番
    TAB_B.SYNC_LAST_TIMESTAMP                                                  -- 最終同期日時
FROM
    B_CICD_REPOSITORY_LIST_JNL TAB_A
    LEFT JOIN T_CICD_SYNC_STATUS TAB_B ON ( TAB_A.REPO_ROW_ID = TAB_B.ROW_ID );

-- -------------------------------------------------
-- -- 資材紐付管理のRestユーザ一覧用View
-- -------------------------------------------------
CREATE VIEW D_CICD_ACCT_LINK AS
SELECT
    TAB_A.*,
    TAB_B.USERNAME,
    TAB_B.DISUSE_FLAG AS A_ACCT_DISUSE_FLAG,
    TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM
    B_CICD_REST_ACCOUNT_LIST TAB_A
    LEFT JOIN A_ACCOUNT_LIST TAB_B ON ( TAB_A.USER_ID = TAB_B.USER_ID );


CREATE VIEW D_CICD_ACCT_LINK_JNL AS
SELECT
    TAB_A.*,
    TAB_B.USERNAME,
    TAB_B.DISUSE_FLAG AS A_ACCT_DISUSE_FLAG,
    TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM
    B_CICD_REST_ACCOUNT_LIST_JNL TAB_A
    LEFT JOIN A_ACCOUNT_LIST_JNL TAB_B ON ( TAB_A.USER_ID = TAB_B.USER_ID );


CREATE VIEW B_CICD_MATERIAL_LINK_JOIN AS
SELECT
    T1.*,
    T2.DISUSE_FLAG REPO_DISUSE_FLAG,
    T3.DISUSE_FLAG MATL_DISUSE_FLAG,
    T4.DISUSE_FLAG RACCT_DISUSE_FLAG,
    T5.DISUSE_FLAG DALG_DISUSE_FLAG,
    T6.DISUSE_FLAG OS_DISUSE_FLAG,
    T7.DISUSE_FLAG OPE_DISUSE_FLAG,
    T8.DISUSE_FLAG PTN_DISUSE_FLAG,
    T9.DISUSE_FLAG ACT_DISUSE_FLAG
FROM 
    B_CICD_MATERIAL_LINK_LIST               T1
    LEFT JOIN B_CICD_REPOSITORY_LIST        T2 ON (T1.REPO_ROW_ID    = T2.REPO_ROW_ID)
    LEFT JOIN B_CICD_MATERIAL_LIST          T3 ON (T1.MATL_ROW_ID    = T3.MATL_ROW_ID)
    LEFT JOIN D_CICD_ACCT_LINK              T4 ON (T1.ACCT_ROW_ID    = T4.ACCT_ROW_ID)
    LEFT JOIN B_ANSIBLE_PNS_DIALOG_TYPE     T5 ON (T1.DIALOG_TYPE_ID = T5.DIALOG_TYPE_ID)
    LEFT JOIN B_OS_TYPE                     T6 ON (T1.OS_TYPE_ID     = T6.OS_TYPE_ID)
    LEFT JOIN E_OPERATION_LIST              T7 ON (T1.DEL_OPE_ID     = T7.OPERATION_NO_UAPK)
    LEFT JOIN C_PATTERN_PER_ORCH            T8 ON (T1.DEL_MOVE_ID    = T8.PATTERN_ID)
    LEFT JOIN A_ACCOUNT_LIST                T9 ON (T4.USER_ID        = T9.USER_ID)
WHERE
    T1.DISUSE_FLAG = '0';
