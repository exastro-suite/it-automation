-- *****************************************************************************
-- *** ***** CI/CD For IaC Create Tables                                     ***
-- *****************************************************************************
-- -------------------------------------------------------
-- -- インターフェース情報
-- -------------------------------------------------------
CREATE TABLE B_CICD_IF_INFO
(
IF_INFO_ROW_ID                    INT                               , -- 識別シーケンス項番
-- --
HOSTNAME                          VARCHAR (128)                     , -- 機器一覧のホスト名に合わせる
PROTOCOL                          VARCHAR (8)                       ,
PORT                              INT                               ,
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          INT                               , -- 表示順序
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (IF_INFO_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_CICD_IF_INFO_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別
-- --
IF_INFO_ROW_ID                    INT                               , -- 識別シーケンス項番
-- --
HOSTNAME                          VARCHAR (128)                     , -- 機器一覧のホスト名に合わせる
PROTOCOL                          VARCHAR (8)                       ,
PORT                              INT                               ,
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          INT                               , -- 表示順序
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------------------------------------
-- -- RestAPIユーザー管理
-- -------------------------------------------------------
CREATE TABLE B_CICD_REST_ACCOUNT_LIST
(
ACCT_ROW_ID                       INT                               , -- 識別シーケンス項番
-- --
USER_ID                           INT                               , -- ユーザー管理(A_ACCOUNT_LIST) USER_ID
LOGIN_PW                          VARCHAR (32)                      , -- ユーザー管理(A_ACCOUNT_LIST)に合わせる
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          INT                               , -- 表示順序
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (ACCT_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_CICD_REST_ACCOUNT_LIST_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別
-- --
ACCT_ROW_ID                       INT                               , -- 識別シーケンス項番
-- --
USER_ID                           INT                               , -- ユーザー管理(A_ACCOUNT_LIST) USER_ID
LOGIN_PW                          VARCHAR (32)                      , -- ユーザー管理(A_ACCOUNT_LIST)に合わせる
-- --
ACCESS_AUTH                       TEXT                              ,
DISP_SEQ                          INT                               , -- 表示順序
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------------------------------
-- -- リモートリポジトリ管理
-- -------------------------------------------------
CREATE TABLE B_CICD_REPOSITORY_LIST
(
REPO_ROW_ID                        INT                              , -- 識別シーケンス項番
-- --
REPO_NAME                          VARCHAR (256)                    , -- リモートリポジトリ名
REMORT_REPO_URL                    VARCHAR (256)                    , -- リモートリポジトリ(URL)
BRANCH_NAME                        VARCHAR (256)                    , -- ブランチ
GIT_PROTOCOL_TYPE_ROW_ID           INT                              , -- プロトコルタイプ
GIT_REPO_TYPE_ROW_ID               INT                              , -- リポジトリタイプ
GIT_USER                           VARCHAR (128)                    , -- Git ユーザー
GIT_PASSWORD                       VARCHAR (128)                    , -- Git パスワード
AUTO_SYNC_FLG                      INT                              , -- 自動同期有無
SYNC_INTERVAL                      INT                              , -- 同期周期(単位:分)
SYNC_STATUS_ROW_ID                 VARCHAR (16)                     , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
PROXY_ADDRESS                      VARCHAR (128)                    , -- プロキシーアドレス
PROXY_PORT                         INT                              , -- プロキシーポート
RETRAY_INTERVAL                    INT                              , -- リトライ周期 単位:ms
RETRAY_COUNT                       INT                              , -- リトライ回数
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           INT                              , -- 表示順序
NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY (REPO_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


CREATE TABLE B_CICD_REPOSITORY_LIST_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別
-- --
REPO_ROW_ID                        INT                              , -- 識別シーケンス項番
-- --
REPO_NAME                          VARCHAR (256)                    , -- リモートリポジトリ名
REMORT_REPO_URL                    VARCHAR (256)                    , -- リモートリポジトリ(URL)
BRANCH_NAME                        VARCHAR (256)                    , -- ブランチ
GIT_PROTOCOL_TYPE_ROW_ID           INT                              , -- プロトコルタイプ
GIT_REPO_TYPE_ROW_ID               INT                              , -- リポジトリタイプ
GIT_USER                           VARCHAR (128)                    , -- Git ユーザー
GIT_PASSWORD                       VARCHAR (128)                    , -- Git パスワード
AUTO_SYNC_FLG                      INT                              , -- 自動同期有無
SYNC_INTERVAL                      INT                              , -- 同期周期(単位:分)
SYNC_STATUS_ROW_ID                 VARCHAR (16)                     , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
PROXY_ADDRESS                      VARCHAR (128)                    , -- プロキシーアドレス
PROXY_PORT                         INT                              , -- プロキシーポート
RETRAY_INTERVAL                    INT                              , -- リトライ周期 単位:ms
RETRAY_COUNT                       INT                              , -- リトライ回数
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           INT                              , -- 表示順序
NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------------------------------
-- -- 資材管理
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_LIST
(
MATL_ROW_ID                        INT                              , -- 識別シーケンス項番
-- --
REPO_ROW_ID                        INT                              , -- リモートリポジトリ「インターフェース情報」　Pkey
MATL_FILE_PATH                     VARCHAR  (4096)                  , -- 資材パス
MATL_FILE_TYPE_ROW_ID              INT                              , -- 資材タイプ　1:ファイル・2:Rolesディレクトリ
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           INT                              , -- 表示順序
NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY (MATL_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_CICD_MATERIAL_LIST_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR  (8)                     , -- 履歴用変更種別
-- --
MATL_ROW_ID                        INT                              , -- 識別シーケンス項番
-- --
REPO_ROW_ID                        INT                              , -- リモートリポジトリ「インターフェース情報」　Pkey
MATL_FILE_PATH                     VARCHAR  (4096)                  , -- 資材パス
MATL_FILE_TYPE_ROW_ID              INT                              , -- 資材タイプ　1:ファイル・2:Rolesディレクトリ
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           INT                              , -- 表示順序
NOTE                               VARCHAR  (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR  (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------------------------------
-- -- 資材紐付け管理
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_LINK_LIST
(
MATL_LINK_ROW_ID                   INT                              , -- 識別シーケンス項番
--
MATL_LINK_NAME                     VARCHAR (256)                    , -- 素材名
REPO_ROW_ID                        INT                              , -- リポジトリ一覧 シーケンス
MATL_ROW_ID                        INT                              , -- 資材一覧 シーケンス
MATL_TYPE_ROW_ID                   INT                              , -- 紐付け先 素材集タイプ
TEMPLATE_FILE_VARS_LIST            VARCHAR (8192)                   , -- トンプレート変数定義
DIALOG_TYPE_ID                     INT                              , -- 対話種別
OS_TYPE_ID                         INT                              , -- OS種別
ACCT_ROW_ID                        INT                              , -- Restユーザ
RBAC_FLG_ROW_ID                    INT                              , -- アクセス許可ロール付与フラグ　
-- 同期状態
AUTO_SYNC_FLG                      INT                              , -- 自動同期有無
SYNC_STATUS_ROW_ID                 VARCHAR (16)                     , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
SYNC_LAST_TIME                     DATETIME(6)                      , -- 最終同期時間
SYNC_LAST_UPDATE_USER              INT                              , -- 最終更新ユーザ
-- デリバリ情報
DEL_OPE_ID                         INT                              , -- 構築時のオペレーションID
DEL_MOVE_ID                        INT                              , -- 構築時のMovementID
DEL_EXEC_TYPE                      INT                              , -- 構築時の実行タイプ　ドライラン
DEL_ERROR_NOTE                     TEXT                             , -- 構築エラー時の内容
DEL_URL                            VARCHAR  (256)                   , -- 構築時の実行結果　URL
--
ACCESS_AUTH                        TEXT                             ,
NOTE                               VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                        VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (MATL_LINK_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_CICD_MATERIAL_LINK_LIST_JNL
(
JOURNAL_SEQ_NO                     INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                      , -- 履歴用変更種別
-- 
MATL_LINK_ROW_ID                   INT                              , -- 識別シーケンス項番
--
MATL_LINK_NAME                     VARCHAR (256)                    , -- 素材名
REPO_ROW_ID                        INT                              , -- リポジトリ一覧 シーケンス
MATL_ROW_ID                        INT                              , -- 資材一覧 シーケンス
MATL_TYPE_ROW_ID                   INT                              , -- 紐付け先 素材集タイプ
TEMPLATE_FILE_VARS_LIST            VARCHAR (8192)                   , -- トンプレート変数定義
DIALOG_TYPE_ID                     INT                              , -- 対話種別
OS_TYPE_ID                         INT                              , -- OS種別
ACCT_ROW_ID                        INT                              , -- Restユーザ
RBAC_FLG_ROW_ID                    INT                              , -- アクセス許可ロール付与フラグ　
-- 同期状態
AUTO_SYNC_FLG                      INT                              , -- 自動同期有無
SYNC_STATUS_ROW_ID                 VARCHAR (16)                     , -- 同期状態
SYNC_ERROR_NOTE                    TEXT                             , -- 同期エラー時の内容
SYNC_LAST_TIME                     DATETIME(6)                      , -- 最終同期時間
SYNC_LAST_UPDATE_USER              INT                              , -- 最終更新ユーザ
-- デリバリ情報
DEL_OPE_ID                         INT                              , -- 構築時のオペレーションID
DEL_MOVE_ID                        INT                              , -- 構築時のMovementID
DEL_EXEC_TYPE                      INT                              , -- 構築時の実行タイプ　ドライラン
DEL_ERROR_NOTE                     TEXT                             , -- 構築エラー時の内容
DEL_URL                            VARCHAR  (256)                   , -- 構築時の実行結果　URL
--
ACCESS_AUTH                        TEXT                             ,
NOTE                               VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                        VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- -------------------------------------------------
-- -- リポジトリ同期状態マスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_REPO_SYNC_STATUS_NAME
(
SYNC_STATUS_ROW_ID                 INT                              ,
-- --
SYNC_STATUS_NAME                   VARCHAR (32)                     ,
-- --
ACCESS_AUTH                        TEXT                             ,
DISP_SEQ                           INT                              , -- 表示順序
NOTE                               VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                        VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                   INT                              , -- 最終更新ユーザ

PRIMARY KEY (SYNC_STATUS_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_REPO_SYNC_STATUS_NAME_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
-- --
SYNC_STATUS_ROW_ID                INT                              ,
-- --
SYNC_STATUS_NAME                  VARCHAR (32)                     ,
-- --
ACCESS_AUTH                       TEXT                             ,
DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- Git資材ファイルタイプマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_FILE_TYPE_NAME
(
MATL_FILE_TYPE_ROW_ID              INT                             , -- 識別シーケンス項番
-- --
MATL_FILE_TYPE_NAME                VARCHAR (128)                   , -- 資材タイプ名　1:ファイル 2:Rolesディレクトリ
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (MATL_FILE_TYPE_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_MATERIAL_FILE_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
-- --
MATL_FILE_TYPE_ROW_ID             INT                              , -- 識別シーケンス項番
-- --
MATL_FILE_TYPE_NAME               VARCHAR (128)                    , -- 資材タイプ名　1:ファイル 2:Rolesディレクトリ
-- --
ACCESS_AUTH                       TEXT                             ,
DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- ITA側素材タイプマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_MATERIAL_TYPE_NAME
(
MATL_TYPE_ROW_ID                   INT                             , -- 識別シーケンス項番
-- --
MATL_TYPE_NAME                     VARCHAR (128)                   , -- 資材タイプ名 1:Playbook素材集 2:対話ﾌｧｲﾙ素材集 3:ロールパッケージ管理 4:ﾌｧｲﾙ管理 5:ﾃﾝﾌﾟﾚｰﾄ管理
DRIVER_TYPE                        INT                             , -- ドライバタイプ　1:ansible　2:terraform
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (MATL_TYPE_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_MATERIAL_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別
-- --
MATL_TYPE_ROW_ID                   INT                             , -- 識別シーケンス項番
-- --
MATL_TYPE_NAME                     VARCHAR (128)                   , -- 資材タイプ名　1:Playbook素材集 2:対話ﾌｧｲﾙ素材集 3:ロールパッケージ管理 4:ﾌｧｲﾙ管理 5:ﾃﾝﾌﾟﾚｰﾄ管理
DRIVER_TYPE                        INT                             , -- ドライバタイプ　1:ansible　2:terraform
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- Gitプロトコルマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_GIT_PROTOCOL_TYPE_NAME
(
GIT_PROTOCOL_TYPE_ROW_ID           INT                             , -- 識別シーケンス項番
-- --
GIT_PROTOCOL_TYPE_NAME             VARCHAR (128)                   , -- プロトコル名 1:https 2:ssh 3:local
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (GIT_PROTOCOL_TYPE_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別
-- --
GIT_PROTOCOL_TYPE_ROW_ID           INT                             , -- 識別シーケンス項番
-- --
GIT_PROTOCOL_TYPE_NAME             VARCHAR (128)                   , -- プロトコル名 1:https 2:ssh 3:local
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- Gitリポジトリタイプマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_GIT_REPOSITORY_TYPE_NAME
(
GIT_REPO_TYPE_ROW_ID               INT                             , -- 識別シーケンス項番
-- --
GIT_REPO_TYPE_NAME                 VARCHAR (128)                   , -- リポジトリタイプ名 1:public　2:private
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (GIT_REPO_TYPE_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_GIT_REPOSITORY_TYPE_NAME_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別
-- --
GIT_REPO_TYPE_ROW_ID               INT                             , -- 識別シーケンス項番
-- --
GIT_REPO_TYPE_NAME                 VARCHAR (128)                   , -- リポジトリタイプ名 1:public　2:private
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- アクセス許可ロール付与フラグマスタ
-- -------------------------------------------------
CREATE TABLE B_CICD_RBAC_FLG_NAME
(
RBAC_FLG_ROW_ID                    INT                             , -- 識別シーケンス項番
-- --
RBAC_FLG_NAME                      VARCHAR (16)                    , -- アクセス許可ロール付与フラグ名 1:なし　2:あり
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ

PRIMARY KEY (RBAC_FLG_ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- ----履歴系テーブル作成
CREATE TABLE B_CICD_RBAC_FLG_NAME_JNL
(
JOURNAL_SEQ_NO                     INT                             , -- 履歴用シーケンス
JOURNAL_REG_DATETIME               DATETIME(6)                     , -- 履歴用変更日時
JOURNAL_ACTION_CLASS               VARCHAR (8)                     , -- 履歴用変更種別
-- --
RBAC_FLG_ROW_ID                    INT                             , -- 識別シーケンス項番
-- --
RBAC_FLG_NAME                      VARCHAR (16)                    , -- アクセス許可ロール付与フラグ名 1:なし　2:あり
-- --
ACCESS_AUTH                        TEXT                            ,
DISP_SEQ                           INT                             , -- 表示順序
NOTE                               VARCHAR (4000)                  , -- 備考
DISUSE_FLAG                        VARCHAR (1)                     , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP              DATETIME(6)                     , -- 最終更新日時
LAST_UPDATE_USER                   INT                             , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------
-- -- 同期状態管理テーブル(履歴なし)
-- -------------------------------------------------
CREATE TABLE T_CICD_SYNC_STATUS (
ROW_ID                             INT                             , -- リポジトリ一覧 項番
SYNC_LAST_TIMESTAMP                DATETIME(6)                     , -- 最終同期日時
PRIMARY KEY(ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


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

CREATE VIEW B_CICD_MATERIAL_TYPE_NAME_ANS AS
SELECT 
    * 
FROM 
    B_CICD_MATERIAL_TYPE_NAME 
WHERE 
    DRIVER_TYPE=1;

CREATE VIEW B_CICD_MATERIAL_TYPE_NAME_ANS_JNL AS
SELECT 
    * 
FROM 
    B_CICD_MATERIAL_TYPE_NAME_JNL 
WHERE 
    DRIVER_TYPE=1;

CREATE VIEW B_CICD_MATERIAL_TYPE_NAME_TERRA AS
SELECT 
    * 
FROM 
    B_CICD_MATERIAL_TYPE_NAME 
WHERE 
    DRIVER_TYPE=2;

CREATE VIEW B_CICD_MATERIAL_TYPE_NAME_TERRA_JNL AS
SELECT 
    * 
FROM 
    B_CICD_MATERIAL_TYPE_NAME_JNL 
WHERE 
    DRIVER_TYPE=2;

CREATE VIEW B_CICD_MATERIAL_TYPE_NAME_NULL AS
SELECT 
    * 
FROM 
    B_CICD_MATERIAL_TYPE_NAME 
WHERE 
    DRIVER_TYPE = null;

CREATE VIEW B_CICD_MATERIAL_TYPE_NAME_NULL_JNL AS
SELECT 
    * 
FROM 
    B_CICD_MATERIAL_TYPE_NAME_JNL 
WHERE 
    DRIVER_TYPE = null;INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_IF_INFO_RIC',2,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_IF_INFO_JSQ',2,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_REST_ACCOUNT_LIST_RIC',1,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_REST_ACCOUNT_LIST_JSQ',1,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_REPOSITORY_LIST_RIC',1,'2100120001',2100120001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_REPOSITORY_LIST_JSQ',1,'2100120001',2100120002,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_LIST_RIC',1,'2100120002',2100120003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_LIST_JSQ',1,'2100120002',2100120004,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_LINK_LIST_RIC',1,'2100120003',2100120005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_LINK_LIST_JSQ',1,'2100120003',2100120006,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_REPO_SYNC_STATUS_NAME_RIC',4,NULL,2100120100,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_REPO_SYNC_STATUS_NAME_JSQ',4,NULL,2100120101,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_FILE_TYPE_NAME_RIC',4,NULL,2100120102,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_FILE_TYPE_NAME_JSQ',4,NULL,2100120103,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_TYPE_NAME_RIC',7,NULL,2100120104,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_MATERIAL_TYPE_NAME_JSQ',7,NULL,2100120105,'履歴テーブル用',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_GIT_PROTOCOL_TYPE_NAME_RIC',4,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_GIT_PROTOCOL_TYPE_NAME_JSQ',4,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_GIT_REPOSITORY_TYPE_NAME_RIC',3,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_GIT_REPOSITORY_TYPE_NAME_JSQ',3,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('T_CICD_SYNC_STATUS_RIC',1,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('T_CICD_SYNC_STATUS_JSQ',1,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_RBAC_FLG_NAME_RIC',3,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_CICD_RBAC_FLG_NAME_JSQ',3,NULL,NULL,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120001,'CI/CD for IaC','CI/CD for IaC',200,'CI/CD for IaC','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-160015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120001,'CI/CD for IaC','CI/CD for IaC',200,'CI/CD for IaC','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120001,2100120001,'リモートリポジトリ管理',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120001,2100120001,'リモートリポジトリ管理',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120002,2100120001,'資材管理',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120002,2100120001,'資材管理',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120003,2100120001,'資材紐付け管理',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120003,2100120001,'資材紐付け管理',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120004,2100120001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120004,2100120001,'インターフェース情報',NULL,NULL,NULL,1,0,1,1,10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120005,2100120001,'Restユーザー管理',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120005,2100120001,'Restユーザー管理',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-130000,'c07','5ebbc37e034d6874a2af59eb04beaa52','CICD For IaC Git同期プロシージャ',NULL,NULL,'CICD For IaC Git同期プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-130000,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-130000,'c07','5ebbc37e034d6874a2af59eb04beaa52','CICD For IaC Git同期プロシージャ',NULL,NULL,'CICD For IaC Git同期プロシージャ','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120001,1,2100120001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120001,1,2100120001,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120002,1,2100120002,1,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120002,1,2100120002,1,'システム管理者','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120003,1,2100120003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120003,1,2100120003,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120004,1,2100120004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120004,1,2100120004,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100120005,1,2100120005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-120005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100120005,1,2100120005,1,'システム管理者','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_REPO_SYNC_STATUS_NAME (SYNC_STATUS_ROW_ID,SYNC_STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'正常',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_REPO_SYNC_STATUS_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYNC_STATUS_ROW_ID,SYNC_STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'正常',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_REPO_SYNC_STATUS_NAME (SYNC_STATUS_ROW_ID,SYNC_STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'異常',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_REPO_SYNC_STATUS_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYNC_STATUS_ROW_ID,SYNC_STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'異常',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_REPO_SYNC_STATUS_NAME (SYNC_STATUS_ROW_ID,SYNC_STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'再開',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_REPO_SYNC_STATUS_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,SYNC_STATUS_ROW_ID,SYNC_STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'再開',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_MATERIAL_FILE_TYPE_NAME (MATL_FILE_TYPE_ROW_ID,MATL_FILE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'ファイル',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_FILE_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_FILE_TYPE_ROW_ID,MATL_FILE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'ファイル',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_FILE_TYPE_NAME (MATL_FILE_TYPE_ROW_ID,MATL_FILE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Rolesディレクトリ',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_FILE_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_FILE_TYPE_ROW_ID,MATL_FILE_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Rolesディレクトリ',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Ansible-Legacyコンソール/Playbook素材集',1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Ansible-Legacyコンソール/Playbook素材集',1,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Ansible-Pioneerコンソール/対話ファイル素材集',1,2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Ansible-Pioneerコンソール/対話ファイル素材集',1,2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Ansible-LegacyRoleコンソール/ロールパッケージ管理',1,3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Ansible-LegacyRoleコンソール/ロールパッケージ管理',1,3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Ansible共通コンソール/ファイル管理',1,4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Ansible共通コンソール/ファイル管理',1,4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Ansible共通コンソール/テンプレート管理',1,5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'Ansible共通コンソール/テンプレート管理',1,5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Terraformコンソール/Module素材',2,6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'Terraformコンソール/Module素材',2,6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME (MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Terraformコンソール/Policy管理',2,7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_MATERIAL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MATL_TYPE_ROW_ID,MATL_TYPE_NAME,DRIVER_TYPE,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'Terraformコンソール/Policy管理',2,7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_IF_INFO (IF_INFO_ROW_ID,HOSTNAME,PROTOCOL,PORT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'127.0.0.1','http',80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,IF_INFO_ROW_ID,HOSTNAME,PROTOCOL,PORT,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'127.0.0.1','http',80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME (GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'https',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'https',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME (GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'ssh',2,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'ssh',2,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME (GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'local',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_PROTOCOL_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_PROTOCOL_TYPE_ROW_ID,GIT_PROTOCOL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'local',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_GIT_REPOSITORY_TYPE_NAME (GIT_REPO_TYPE_ROW_ID,GIT_REPO_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'public',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_REPOSITORY_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_REPO_TYPE_ROW_ID,GIT_REPO_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'public',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_REPOSITORY_TYPE_NAME (GIT_REPO_TYPE_ROW_ID,GIT_REPO_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'private',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_GIT_REPOSITORY_TYPE_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,GIT_REPO_TYPE_ROW_ID,GIT_REPO_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'private',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_CICD_RBAC_FLG_NAME (RBAC_FLG_ROW_ID,RBAC_FLG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'なし',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_RBAC_FLG_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RBAC_FLG_ROW_ID,RBAC_FLG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'なし',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_RBAC_FLG_NAME (RBAC_FLG_ROW_ID,RBAC_FLG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'あり',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_CICD_RBAC_FLG_NAME_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RBAC_FLG_ROW_ID,RBAC_FLG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'あり',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
