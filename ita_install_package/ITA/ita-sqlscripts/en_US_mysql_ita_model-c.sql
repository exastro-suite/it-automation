-- *****************************************************************************
-- *** ***** Ansible Common Tables                                           ***
-- *****************************************************************************
CREATE TABLE B_ANSIBLE_STATUS
(
STATUS_ID                         INT                              ,

STATUS_NAME                       VARCHAR (32)                     ,

DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (STATUS_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_STATUS_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

STATUS_ID                         INT                              ,

STATUS_NAME                       VARCHAR (32)                     ,

DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_IF_INFO
(
-- 主キー
ANSIBLE_IF_INFO_ID              INT                               ,
-- Ansible 接続情報
ANSIBLE_HOSTNAME                VARCHAR (128)                     , -- Ansible 接続ホスト名
ANSIBLE_PROTOCOL                VARCHAR (8)                       , -- Ansible 接続プロトコル
ANSIBLE_PORT                    INT                               , -- Ansible 接続ポート
-- Tower 接続情報
ANSTWR_HOST_ID                  INT                               , -- Tower 接続ホスト名
ANSTWR_PROTOCOL                 VARCHAR (8)                       , -- Tower 接続プロトコル
ANSTWR_PORT                     INT                               , -- Tower 接続ポート
-- 共通
ANSIBLE_EXEC_MODE               INT                               , -- 実行モード 1:ansible/2:ansible tower
ANSIBLE_STORAGE_PATH_LNX        VARCHAR (256)                     ,
ANSIBLE_STORAGE_PATH_ANS        VARCHAR (256)                     ,
SYMPHONY_STORAGE_PATH_ANS       VARCHAR (256)                     ,
CONDUCTOR_STORAGE_PATH_ANS      VARCHAR (256)                     ,
ANSIBLE_EXEC_OPTIONS            VARCHAR (512)                     , -- ansible-playbook実行時のオプションパラメータ
ANSIBLE_PROXY_ADDRESS           VARCHAR (128)                     ,
ANSIBLE_PROXY_PORT              INT                               ,
-- ansible独自情報
ANSIBLE_EXEC_USER               VARCHAR (64)                      , -- ansible-playbook実行ユーザー
ANSIBLE_ACCESS_KEY_ID           VARCHAR (64)                      , 
ANSIBLE_SECRET_ACCESS_KEY       VARCHAR (64)                      , 
-- ansible Tower独自情報
ANSTWR_ORGANIZATION             VARCHAR (512)                      , -- 組織名
ANSTWR_AUTH_TOKEN               VARCHAR (256)                     , -- 接続トークン
ANSTWR_DEL_RUNTIME_DATA         INT                               , 
-- 共通
NULL_DATA_HANDLING_FLG          INT                               , -- Null値の連携 1:有効　2:無効
ANSIBLE_NUM_PARALLEL_EXEC       INT                               , -- 並列実行数
ANSIBLE_REFRESH_INTERVAL        INT                               , 
ANSIBLE_TAILLOG_LINES           INT                               , 
-- Git連携先情報
ANS_GIT_HOSTNAME                VARCHAR (128)                     , -- バックヤードサーバー「Ansible_Driver」 接続ホスト名
ANS_GIT_USER                    VARCHAR (128)                     , -- バックヤードサーバー「Ansible_Driver」 Gitリポジトリ アクセス ユーザー
ANS_GIT_SSH_KEY_FILE            VARCHAR (256)                     , -- バックヤードサーバー「Ansible_Driver」 Gitリポジトリ アクセス 公開鍵
ANS_GIT_SSH_KEY_FILE_PASSPHRASE text                              , -- バックヤードサーバー「Ansible_Driver」 Gitリポジトリ アクセス 公開鍵 パスフレーズ
--
DISP_SEQ                        INT                               , -- 表示順序
ACCESS_AUTH                     TEXT                              ,
NOTE                            VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                     VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                INT                               , -- 最終更新ユーザ
PRIMARY KEY (ANSIBLE_IF_INFO_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_IF_INFO_JNL
(
JOURNAL_SEQ_NO                  INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME            DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS            VARCHAR (8)                       , -- 履歴用変更種別
-- 主キー
ANSIBLE_IF_INFO_ID              INT                               ,
-- Ansible 接続情報
ANSIBLE_HOSTNAME                VARCHAR (128)                     , -- Ansible 接続ホスト名
ANSIBLE_PROTOCOL                VARCHAR (8)                       , -- Ansible 接続プロトコル
ANSIBLE_PORT                    INT                               , -- Ansible 接続ポート
-- Tower 接続情報
ANSTWR_HOST_ID                  INT                               , -- Tower 接続ホスト名
ANSTWR_PROTOCOL                 VARCHAR (8)                       , -- Tower 接続プロトコル
ANSTWR_PORT                     INT                               , -- Tower 接続ポート
-- 共通
ANSIBLE_EXEC_MODE               INT                               , -- 実行モード 1:ansible/2:ansible tower
ANSIBLE_STORAGE_PATH_LNX        VARCHAR (256)                     ,
ANSIBLE_STORAGE_PATH_ANS        VARCHAR (256)                     ,
SYMPHONY_STORAGE_PATH_ANS       VARCHAR (256)                     ,
CONDUCTOR_STORAGE_PATH_ANS      VARCHAR (256)                     ,
ANSIBLE_EXEC_OPTIONS            VARCHAR (512)                     , -- ansible-playbook実行時のオプションパラメータ
ANSIBLE_PROXY_ADDRESS           VARCHAR (128)                     ,
ANSIBLE_PROXY_PORT              INT                               ,
-- ansible独自情報
ANSIBLE_EXEC_USER               VARCHAR (64)                      , -- ansible-playbook実行ユーザー
ANSIBLE_ACCESS_KEY_ID           VARCHAR (64)                      , 
ANSIBLE_SECRET_ACCESS_KEY       VARCHAR (64)                      , 
-- ansible Tower独自情報
ANSTWR_ORGANIZATION             VARCHAR (512)                      , -- 組織名
ANSTWR_AUTH_TOKEN               VARCHAR (256)                     , -- 接続トークン
ANSTWR_DEL_RUNTIME_DATA         INT                               , 
-- 共通
NULL_DATA_HANDLING_FLG          INT                               , -- Null値の連携 1:有効　2:無効
ANSIBLE_NUM_PARALLEL_EXEC       INT                               , -- 並列実行数
ANSIBLE_REFRESH_INTERVAL        INT                               , 
ANSIBLE_TAILLOG_LINES           INT                               , 
-- Git連携先情報
ANS_GIT_HOSTNAME                VARCHAR (128)                     , -- バックヤードサーバー「Ansible_Driver」 接続ホスト名
ANS_GIT_USER                    VARCHAR (128)                     , -- バックヤードサーバー「Ansible_Driver」 Gitリポジトリ アクセス ユーザー
ANS_GIT_SSH_KEY_FILE            VARCHAR (256)                     , -- バックヤードサーバー「Ansible_Driver」 Gitリポジトリ アクセス 公開鍵
ANS_GIT_SSH_KEY_FILE_PASSPHRASE text                              , -- バックヤードサーバー「Ansible_Driver」 Gitリポジトリ アクセス 公開鍵 パスフレーズ
--
DISP_SEQ                        INT                               , -- 表示順序
ACCESS_AUTH                     TEXT                              ,
NOTE                            VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                     VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                INT                               , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----


-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_HOST ( 
  ANSTWR_HOST_ID                  INT                               ,
  ANSTWR_HOSTNAME                 VARCHAR (128)                     , -- ホスト名/IPアドレス
  ANSTWR_LOGIN_AUTH_TYPE          INT                               , -- 認証方式 パスワード認証/鍵認証
  ANSTWR_LOGIN_USER               VARCHAR (30)                      , -- ユーザー
  ANSTWR_LOGIN_PASSWORD           TEXT                              , -- パスワード
  ANSTWR_LOGIN_SSH_KEY_FILE       VARCHAR (256)                     , -- 鍵ファイル
  ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE  TEXT                        ,
  ANSTWR_ISOLATED_TYPE            INT                               , -- 1:isolated tower 
-- 
  DISP_SEQ                        INT                               ,
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    ,
  DISUSE_FLAG                     VARCHAR (1)                       ,
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       ,
  LAST_UPDATE_USER                INT                               ,
  PRIMARY KEY (ANSTWR_HOST_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_HOST_JNL ( 
  JOURNAL_SEQ_NO                  INT                               ,
  JOURNAL_REG_DATETIME            DATETIME(6)                       ,
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       ,
-- 
  ANSTWR_HOST_ID                  INT                               ,
  ANSTWR_HOSTNAME                 VARCHAR (128)                     , -- ホスト名/IPアドレス
  ANSTWR_LOGIN_AUTH_TYPE          INT                               , -- 認証方式 パスワード認証/鍵認証
  ANSTWR_LOGIN_USER               VARCHAR (30)                      , -- ユーザー
  ANSTWR_LOGIN_PASSWORD           TEXT                              , -- パスワード
  ANSTWR_LOGIN_SSH_KEY_FILE       VARCHAR (256)                     , -- 鍵ファイル
  ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE  TEXT                        ,
  ANSTWR_ISOLATED_TYPE            INT                               , -- 1:isolated tower 
-- 
  DISP_SEQ                        INT                               ,
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    ,
  DISUSE_FLAG                     VARCHAR (1)                       ,
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       ,
  LAST_UPDATE_USER                INT                               ,
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_CREDENTIAL_TYPE ( 
  CREDENTIAL_TYPE_ID              INT                               ,
  CREDENTIAL_TYPE_NAME            VARCHAR (256)                     ,
   
  DISP_SEQ                        INT                               ,
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    ,
  DISUSE_FLAG                     VARCHAR (1)                       ,
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       ,
  LAST_UPDATE_USER                INT                               ,
  PRIMARY KEY (CREDENTIAL_TYPE_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_CREDENTIAL_TYPE_JNL ( 
  JOURNAL_SEQ_NO                  INT                               ,
  JOURNAL_REG_DATETIME            DATETIME(6)                       ,
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       ,
-- 
  CREDENTIAL_TYPE_ID              INT                               ,
  CREDENTIAL_TYPE_NAME            VARCHAR (256)                     ,
-- 
  DISP_SEQ                        INT                               ,
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    ,
  DISUSE_FLAG                     VARCHAR (1)                       ,
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       ,
  LAST_UPDATE_USER                INT                               ,
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_RUN_MODE
(
RUN_MODE_ID                       INT                              , -- 識別シーケンス

RUN_MODE_NAME                     VARCHAR (32)                     ,

DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (RUN_MODE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_RUN_MODE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

RUN_MODE_ID                       INT                              , -- 識別シーケンス

RUN_MODE_NAME                     VARCHAR (32)                     ,

DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- 変数タイプマスタ
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_VARS_TYPE
(
VARS_TYPE_ID                      INT                              , -- 識別シーケンス

VARS_TYPE_NAME                    VARCHAR (64)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_VARS_TYPE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_TYPE_ID                      INT                              , -- 識別シーケンス

VARS_TYPE_NAME                    VARCHAR (64)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ------------------------------
-- -- ファイル管理
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_CONTENTS_FILE
(
CONTENTS_FILE_ID                  INT                              , -- ファイルID
CONTENTS_FILE_VARS_NAME           VARCHAR (256)                    , -- 変数名
CONTENTS_FILE                     VARCHAR (256)                    , -- コンテンツ ファイル名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (CONTENTS_FILE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----
-- ----履歴系テーブル作成
CREATE TABLE B_ANS_CONTENTS_FILE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

CONTENTS_FILE_ID                  INT                              , -- ファイルID
CONTENTS_FILE_VARS_NAME           VARCHAR (256)                    , -- 変数名
CONTENTS_FILE                     VARCHAR (256)                    , -- コンテンツ ファイル名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ------------------------------
-- -- テンプレート管理
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_TEMPLATE_FILE
(
ANS_TEMPLATE_ID                   INT                              ,

ANS_TEMPLATE_VARS_NAME            VARCHAR (256)                    ,
ANS_TEMPLATE_FILE                 VARCHAR (256)                    ,
VAR_STRUCT_ANAL_JSON_STRING_FILE  VARCHAR (100)                    , -- 変数解析結果を保存する為のFileUploadカラム(隠し)
VARS_LIST                         VARCHAR (8192)                   , -- 変数定義
ROLE_ONLY_FLAG                    VARCHAR (1)                      , -- 多段変数定義有無　1:定義有

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ANS_TEMPLATE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TEMPLATE_FILE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ANS_TEMPLATE_ID                   INT                              ,

ANS_TEMPLATE_VARS_NAME            VARCHAR (256)                    ,
ANS_TEMPLATE_FILE                 VARCHAR (256)                    ,
VAR_STRUCT_ANAL_JSON_STRING_FILE  VARCHAR (100)                    , -- 変数解析結果を保存する為のFileUploadカラム(隠し)
VARS_LIST                         VARCHAR (8192)                   , -- 変数定義
ROLE_ONLY_FLAG                    VARCHAR (1)                      , -- 多段変数定義有無　1:定義有

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ------------------------------
-- -- Pioner LANGマスタ
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_PNS_LANG_MASTER
(
ID                                INT                              ,

NAME                              VARCHAR (64)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_PNS_LANG_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
ID                                INT                              ,

NAME                              VARCHAR (64)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ------------------------------
-- -- 実行モード（エンジン）
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_EXEC_MODE
(
ID                                INT                              ,

NAME                              VARCHAR (32)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_EXEC_MODE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
ID                                INT                              ,

NAME                              VARCHAR (32)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWER_RUNDATA_DEL_FLAG
(
FLAG_ID                           INT                               , -- 識別シーケンス
FLAG_NAME                         VARCHAR (32)                      , -- 表示名
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY (FLAG_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWER_RUNDATA_DEL_FLAG_JNL
(
JOURNAL_SEQ_NO                    INT                               , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                       , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                       , -- 履歴用変更種別
FLAG_ID                           INT                               , -- 識別シーケンス
FLAG_NAME                         VARCHAR (32)                      , -- 表示名
DISP_SEQ                          INT                               , -- 表示順序
ACCESS_AUTH                       TEXT                              ,
NOTE                              VARCHAR (4000)                    , -- 備考
DISUSE_FLAG                       VARCHAR (1)                       , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                       , -- 最終更新日時
LAST_UPDATE_USER                  INT                               , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ------------------------------
-- -- Towerインスタンスグループ
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_INSTANCE_GROUP ( 
  INSTANCE_GROUP_ITA_MANAGED_ID   INT                               , 
  INSTANCE_GROUP_NAME             VARCHAR (512)                     , 
  INSTANCE_GROUP_ID               INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (INSTANCE_GROUP_ITA_MANAGED_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_INSTANCE_GROUP_JNL ( 
  JOURNAL_SEQ_NO                  INT                               , 
  JOURNAL_REG_DATETIME            DATETIME(6)                       , 
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       , 
  INSTANCE_GROUP_ITA_MANAGED_ID   INT                               , 
  INSTANCE_GROUP_NAME             VARCHAR (512)                     , 
  INSTANCE_GROUP_ID               INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----

-- ------------------------------
-- -- Tower VIRTUALENVマスタ
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_VIRTUALENV ( 
  ROW_ID                          INT                               , 
  VIRTUALENV_NAME                 VARCHAR (512)                     , 
  VIRTUALENV_NO                   INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (ROW_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_VIRTUALENV_JNL ( 
  JOURNAL_SEQ_NO                  INT                               , 
  JOURNAL_REG_DATETIME            DATETIME(6)                       , 
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       , 
  ROW_ID                          INT                               , 
  VIRTUALENV_NAME                 VARCHAR (512)                     , 
  VIRTUALENV_NO                   INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----

-- ------------------------------
-- -- Tower 実行環境マスタ
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_EXECUTION_ENVIRONMENT ( 
  ROW_ID                          INT                               , 
  EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                     , 
  EXECUTION_ENVIRONMENT_NO        INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (ROW_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_EXECUTION_ENVIRONMENT_JNL ( 
  JOURNAL_SEQ_NO                  INT                               , 
  JOURNAL_REG_DATETIME            DATETIME(6)                       , 
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       , 
  ROW_ID                          INT                               , 
  EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                     , 
  EXECUTION_ENVIRONMENT_NO        INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----

-- ------------------------------
-- -- Tower 組織名マスタ
-- ------------------------------
-- ----更新系テーブル作成
CREATE TABLE 
B_ANS_TWR_ORGANIZATION ( 
  ROW_ID                          INT                               , 
  ORGANIZATION_NAME               VARCHAR (512)                     , 
  ORGANIZATION_ID                 INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (ROW_ID) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE 
B_ANS_TWR_ORGANIZATION_JNL ( 
  JOURNAL_SEQ_NO                  INT                               , 
  JOURNAL_REG_DATETIME            DATETIME(6)                       , 
  JOURNAL_ACTION_CLASS            VARCHAR (8)                       , 
  ROW_ID                          INT                               , 
  ORGANIZATION_NAME               VARCHAR (512)                     , 
  ORGANIZATION_ID                 INT                               , 
  DISP_SEQ                        INT                               , 
  ACCESS_AUTH                     TEXT                              ,
  NOTE                            VARCHAR (4000)                    , 
  DISUSE_FLAG                     VARCHAR (1)                       , 
  LAST_UPDATE_TIMESTAMP           DATETIME(6)                       , 
  LAST_UPDATE_USER                INT                               , 
  PRIMARY KEY (JOURNAL_SEQ_NO) 
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8; 
-- 履歴系テーブル作成----


-- ----------------------------------------------------------------------------------------
-- -- ansible-playbookのオプションパラメータとAnsible Tower JobTemplate プロパティの紐づけ
-- ----------------------------------------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_TWR_JOBTP_PROPERTY
(
ROWID                             INT                              ,
--
KEY_NAME                          VARCHAR (64)                     , -- ansible-playbook パラメータ名
SHORT_KEY_NAME                    VARCHAR (32)                     , -- ansible-playbook ショートパラメータ名
PROPERTY_TYPE                     VARCHAR (64)                     , -- パラメータタイプ
                                                                     -- 1: KeyValue     -key  value/--key==value
                                                                     -- 2: Verbosity    -v...
                                                                     -- 3: booleanTrue  -D         /--deff
                                                                     -- 4: ExtraVars    -e value   /-extra-vars=value
PROPERTY_NAME                     VARCHAR (64)                     , -- Tower JobTemplateプロパティ名
TOWERONLY                         INT                              , -- 0:Ansible/Tower共通　1:Tower独自パラメータ
--
DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ROWID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_TWR_JOBTP_PROPERTY_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
--
ROWID                             INT                              ,
--
KEY_NAME                          VARCHAR (64)                     , -- ansible-playbook パラメータ名
SHORT_KEY_NAME                    VARCHAR (32)                     , -- ansible-playbook ショートパラメータ名
PROPERTY_TYPE                     VARCHAR (64)                     , -- パラメータタイプ
                                                                     -- 1: KeyValue     -key  value/--key==value
                                                                     -- 2: Verbosity    -v...
                                                                     -- 3: booleanTrue  -D         /--deff
                                                                     -- 4: ExtraVars    -e value   /-extra-vars=value
PROPERTY_NAME                     VARCHAR (64)                     , -- Tower JobTemplateプロパティ名
TOWERONLY                         INT                              , -- 0:Ansible/Tower共通　1:Tower独自パラメータ
--
DISP_SEQ                          INT                              , -- 表示順序
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----------------------------------------------------------
-- - ansible playbook等でのGBL/TPF/CPL変数利用リスト
-- ----------------------------------------------------------
CREATE TABLE B_ANS_COMVRAS_USLIST
(
ROW_ID                            INT                              ,
-- ----
FILE_ID                           INT                              , -- ファイル種別　1:playbook/2:対話ファイル/3:ロールパッケージ/4:テンプレートファイル
VRA_ID                            INT                              , -- 変数種別      1:GBL/2:CPF/3:TPF
CONTENTS_ID                       INT                              , -- コンテンツID  該当ファイルが格納されているデータベースのPkeyID
VAR_NAME                          VARCHAR (256)                    , -- 変数名
REVIVAL_FLAG                      VARCHAR (1)                      , -- 復活時の有効レコードフラグ
-- ----
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_ANS_COMVRAS_USLIST_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
-- ----
ROW_ID                            INT                              ,
-- ----
FILE_ID                           INT                              , -- ファイル種別　1:playbook/2:対話ファイル/3:ロールパッケージ/4:テンプレートファイル
VRA_ID                            INT                              , -- 変数種別      1:GBL/2:CPF/3:TPF
CONTENTS_ID                       INT                              , -- コンテンツID  該当ファイルが格納されているデータベースのPkeyID
VAR_NAME                          VARCHAR (256)                    , -- 変数名
REVIVAL_FLAG                      VARCHAR (1)                      , -- 復活時の有効レコードフラグ
-- ----
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- ----------------------------------------------------------
-- - ansible playbook等でのGBL/TPF/CPL変数利用リスト 変数種別マスタ
-- ----------------------------------------------------------
CREATE TABLE B_ANS_COMVRAS_USLIST_V_ID
(
ROW_ID                            INT                              ,
-- ----
NAME                              VARCHAR (64)                     , -- 変数種別　1:GBL/2:CPF/3:TPF
-- ----
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_ANS_COMVRAS_USLIST_V_ID_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
-- ----
ROW_ID                            INT                              ,
-- ----
NAME                              VARCHAR (64)                     , -- 変数種別　1:GBL/2:CPF/3:TPF
-- ----
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;


-- ----------------------------------------------------------
-- - ansible playbook等でのGBL/TPF/CPL変数利用リスト ファイル種別マスタ
-- ----------------------------------------------------------
CREATE TABLE B_ANS_COMVRAS_USLIST_F_ID
(
ROW_ID                            INT                              ,
-- ----
NAME                              VARCHAR (64)                     , -- ファイル種別　1:playbook/2:対話ファイル/3:ロールパッケージ/4:テンプレートファイル
-- ----
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_ANS_COMVRAS_USLIST_F_ID_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別
-- ----
ROW_ID                            INT                              ,
-- ----
NAME                              VARCHAR (64)                     , -- ファイル種別　1:playbook/2:対話ファイル/3:ロールパッケージ/4:テンプレートファイル
-- ----
DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- *****************************************************************************
-- ***  Ansible Common Tables *****                                          ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Ansible Common Views                                            ***
-- *****************************************************************************

CREATE VIEW D_ANS_TWR_HOST     AS 
SELECT
  * 
FROM 
  B_ANS_TWR_HOST
WHERE
  ANSTWR_ISOLATED_TYPE is NULL;
  
CREATE VIEW D_ANS_TWR_HOST_JNL AS 
SELECT
  * 
FROM 
  B_ANS_TWR_HOST_JNL
WHERE
  ANSTWR_ISOLATED_TYPE is NULL;

CREATE VIEW D_ANSIBLE_TOWER_IF_INFO AS 
SELECT 
  TAB_A.*,
  TAB_B.ANSTWR_HOSTNAME,
  TAB_B.ANSTWR_LOGIN_AUTH_TYPE,
  TAB_B.ANSTWR_LOGIN_USER,
  TAB_B.ANSTWR_LOGIN_PASSWORD,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE,
  TAB_B.ANSTWR_ISOLATED_TYPE
FROM
  B_ANSIBLE_IF_INFO           TAB_A
  LEFT JOIN (
             SELECT * 
             FROM B_ANS_TWR_HOST 
             WHERE DISUSE_FLAG = '0'
            ) TAB_B ON ( TAB_A.ANSTWR_HOST_ID = TAB_B.ANSTWR_HOST_ID );
  
CREATE VIEW D_ANSIBLE_TOWER_IF_INFO_JNL AS 
SELECT 
  TAB_A.*,
  TAB_B.ANSTWR_HOSTNAME,
  TAB_B.ANSTWR_LOGIN_AUTH_TYPE,
  TAB_B.ANSTWR_LOGIN_USER,
  TAB_B.ANSTWR_LOGIN_PASSWORD,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE,
  TAB_B.ANSTWR_LOGIN_SSH_KEY_FILE_PASSPHRASE,
  TAB_B.ANSTWR_ISOLATED_TYPE
FROM
  B_ANSIBLE_IF_INFO_JNL         TAB_A
  LEFT JOIN (
             SELECT * 
             FROM B_ANS_TWR_HOST_JNL
             WHERE DISUSE_FLAG = '0'
            ) TAB_B ON ( TAB_A.ANSTWR_HOST_ID = TAB_B.ANSTWR_HOST_ID );

CREATE VIEW D_TOWER_LOGIN_AUTH_TYPE AS
SELECT 
  *
FROM 
  B_LOGIN_AUTH_TYPE
WHERE 
  LOGIN_AUTH_TYPE_ID <= 4;

CREATE VIEW D_TOWER_LOGIN_AUTH_TYPE_JNL AS
SELECT 
  *
FROM 
  B_LOGIN_AUTH_TYPE_JNL
WHERE 
  LOGIN_AUTH_TYPE_ID <= 4;

-- *****************************************************************************
-- ***  Ansible Common View *****                                            ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Ansible Legacy Tables                                           ***
-- *****************************************************************************
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_PLAYBOOK
(
PLAYBOOK_MATTER_ID                INT                              ,

PLAYBOOK_MATTER_NAME              VARCHAR (256)                     ,
PLAYBOOK_MATTER_FILE              VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (PLAYBOOK_MATTER_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_PLAYBOOK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

PLAYBOOK_MATTER_ID                INT                              ,

PLAYBOOK_MATTER_NAME              VARCHAR (256)                     ,
PLAYBOOK_MATTER_FILE              VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_PATTERN_LINK
(
LINK_ID                           INT                              ,

PATTERN_ID                        INT                              ,
PLAYBOOK_MATTER_ID                INT                              ,
INCLUDE_SEQ                       INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

LINK_ID                           INT                              ,

PATTERN_ID                        INT                              ,
PLAYBOOK_MATTER_ID                INT                              ,
INCLUDE_SEQ                       INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_PHO_LINK
(
PHO_LINK_ID                       INT                              ,

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (PHO_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_PHO_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

PHO_LINK_ID                       INT                              ,

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_VARS_MASTER
(
VARS_NAME_ID                      INT                              ,

VARS_NAME                         VARCHAR (256)                    ,
VARS_DESCRIPTION                  VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_NAME_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_VARS_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_NAME_ID                      INT                              ,

VARS_NAME                         VARCHAR (256)                    ,
VARS_DESCRIPTION                  VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANS_LNS_PTN_VARS_LINK  
(
VARS_LINK_ID                      INT                              ,

PATTERN_ID                        INT                              ,
VARS_NAME_ID                      INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LNS_PTN_VARS_LINK_JNL  
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_LINK_ID                      INT                              ,

PATTERN_ID                        INT                              ,
VARS_NAME_ID                      INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_VARS_ASSIGN
(
ASSIGN_ID                         INT                              ,

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,
VARS_LINK_ID                      INT                              ,
VARS_ENTRY                        text                             ,
VARS_ENTRY_FILE                   text                             ,
SENSITIVE_FLAG                    VARCHAR (1)                      ,
VARS_ENTRY_USE_TPFVARS            VARCHAR (1)                      , -- 具体値のTPF変数設定有無　1:設定あり　他:設定なし
ASSIGN_SEQ                        INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ASSIGN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LNS_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ASSIGN_ID                         INT                              ,

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,
VARS_LINK_ID                      INT                              ,
VARS_ENTRY                        text                             ,
VARS_ENTRY_FILE                   text                             ,
SENSITIVE_FLAG                    VARCHAR (1)                      ,
VARS_ENTRY_USE_TPFVARS            VARCHAR (1)                      , -- 具体値のTPF変数設定有無　1:設定あり　他:設定なし
ASSIGN_SEQ                        INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_ANSIBLE_LNS_EXE_INS_MNG
(
EXECUTION_NO                      INT                              ,

EXECUTION_USER                    VARCHAR (80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR (256)                    , -- シンフォニークラス名
STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                              ,
I_ANS_PARALLEL_EXE                INT                              ,
I_ANS_WINRM_ID                    INT                              ,
I_ANS_PLAYBOOK_HED_DEF            VARCHAR (512)                    ,
I_ANS_EXEC_OPTIONS                VARCHAR (512)                    ,
OPERATION_NO_UAPK                 INT                              ,
I_OPERATION_NAME                  VARCHAR (256)                    ,
I_OPERATION_NO_IDBH               INT                              ,
I_VIRTUALENV_NAME                 VARCHAR (512)                    , -- tower virtualenv path
I_ENGINE_VIRTUALENV_NAME          VARCHAR (512)                    , -- ansible  virtualenv path
I_EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                    , -- AAP 実行環境
I_ANSIBLE_CONFIG_FILE             VARCHAR (512)                    , -- ansible.cfg アップロードカラム
TIME_BOOK                         DATETIME(6)                      ,
TIME_START                        DATETIME(6)                      ,
TIME_END                          DATETIME(6)                      ,
FILE_INPUT                        VARCHAR (1024)                   ,
FILE_RESULT                       VARCHAR (1024)                   ,
RUN_MODE                          INT                              , -- ドライランモード 1:通常 2:ドライラン
EXEC_MODE                         INT                              , -- 実行モード 1:ansible/2:ansible tower
MULTIPLELOG_MODE                  INT                              , -- マルチログモード 1:マルチログモード　他:シングルログモード
LOGFILELIST_JSON                  VARCHAR (8000)                   , -- マルチログモード時のログファイル名リスト(json形式の配列)

CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID

COLLECT_STATUS                    INT                              , -- 収集ステータス
COLLECT_LOG                       VARCHAR (1024)                   , -- 収集ログ(テキスト形式)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (EXECUTION_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_ANSIBLE_LNS_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

EXECUTION_NO                      INT                              ,

EXECUTION_USER                    VARCHAR (80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR (256)                    , -- シンフォニークラス名
STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                              ,
I_ANS_PARALLEL_EXE                INT                              ,
I_ANS_WINRM_ID                    INT                              ,
I_ANS_PLAYBOOK_HED_DEF            VARCHAR (512)                    ,
I_ANS_EXEC_OPTIONS                VARCHAR (512)                    ,
OPERATION_NO_UAPK                 INT                              ,
I_OPERATION_NAME                  VARCHAR (256)                    ,
I_OPERATION_NO_IDBH               INT                              ,
I_VIRTUALENV_NAME                 VARCHAR (512)                    , -- tower virtualenv path
I_ENGINE_VIRTUALENV_NAME          VARCHAR (512)                    , -- ansible  virtualenv path
I_EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                    , -- AAP 実行環境
I_ANSIBLE_CONFIG_FILE             VARCHAR (512)                    , -- ansible.cfg アップロードカラム
TIME_BOOK                         DATETIME(6)                      ,
TIME_START                        DATETIME(6)                      ,
TIME_END                          DATETIME(6)                      ,
FILE_INPUT                        VARCHAR (1024)                   ,
FILE_RESULT                       VARCHAR (1024)                   ,
RUN_MODE                          INT                              , -- ドライランモード 1:通常 2:ドライラン
EXEC_MODE                         INT                              , -- 実行モード 1:ansible/2:ansible tower
MULTIPLELOG_MODE                  INT                              , -- マルチログモード 1:マルチログモード　他:シングルログモード
LOGFILELIST_JSON                  VARCHAR (8000)                   , -- マルチログモード時のログファイル名リスト(json形式の配列)

CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID

COLLECT_STATUS                    INT                              , -- 収集ステータス
COLLECT_LOG                       VARCHAR (1024)                   , -- 収集ログ(テキスト形式)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- *****************************************************************************
-- *** Ansible Legacy Tables *****                                           ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Ansible Legacy Views                                            ***
-- *****************************************************************************
-- 003-051:
CREATE VIEW D_ANSIBLE_LNS_INS_STATUS     AS 
SELECT * 
FROM B_ANSIBLE_STATUS;

CREATE VIEW D_ANSIBLE_LNS_INS_STATUS_JNL AS 
SELECT * 
FROM B_ANSIBLE_STATUS_JNL;

CREATE VIEW D_ANSIBLE_LNS_IF_INFO     AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO;

CREATE VIEW D_ANSIBLE_LNS_IF_INFO_JNL AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO_JNL;

CREATE VIEW D_ANSIBLE_LNS_INS_RUN_MODE     AS 
SELECT * 
FROM B_ANSIBLE_RUN_MODE;

CREATE VIEW D_ANSIBLE_LNS_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_ANSIBLE_RUN_MODE_JNL;

CREATE VIEW D_ANSIBLE_LNS_PLAYBOOK AS 
SELECT  PLAYBOOK_MATTER_ID      ,
        PLAYBOOK_MATTER_NAME    ,
        CONCAT(PLAYBOOK_MATTER_ID,':',PLAYBOOK_MATTER_NAME) PLAYBOOK,
        PLAYBOOK_MATTER_FILE    ,
        DISP_SEQ                ,
        ACCESS_AUTH             ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_ANSIBLE_LNS_PLAYBOOK;

CREATE VIEW D_ANSIBLE_LNS_PLAYBOOK_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        PLAYBOOK_MATTER_ID      ,
        PLAYBOOK_MATTER_NAME    ,
        CONCAT(PLAYBOOK_MATTER_ID,':',PLAYBOOK_MATTER_NAME) PLAYBOOK,
        PLAYBOOK_MATTER_FILE    ,
        DISP_SEQ                ,
        ACCESS_AUTH             ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_ANSIBLE_LNS_PLAYBOOK_JNL;

CREATE VIEW E_ANSIBLE_LNS_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 3;

CREATE VIEW E_ANSIBLE_LNS_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 3;

CREATE VIEW D_ANS_LNS_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
CREATE VIEW D_ANS_LNS_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LNS_PTN_VARS_LINK_JNL TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
-- 構造名ポストフィックス(_VFS)=「View-For-P(ulldownSelect)」
-- 登録/更新用なので、結合するテーブルのレコードが廃止されていたら、レコードとして扱わない
CREATE VIEW D_ANS_LNS_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;

CREATE VIEW E_ANSIBLE_LNS_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_ANSIBLE_LNS_EXE_INS_MNG       TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE VIEW E_ANSIBLE_LNS_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_ANSIBLE_LNS_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_ANSIBLE_LNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE VIEW E_OPE_FOR_PULLDOWN_LNS
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       TAB_A.OPERATION            ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.PHO_LINK_ID          ,
       TAB_B.DISUSE_FLAG           DISUSE_FLAG_2
FROM 
    E_OPERATION_LIST TAB_A
    LEFT JOIN B_ANSIBLE_LNS_PHO_LINK TAB_B ON (TAB_A.OPERATION_NO_UAPK = TAB_B.OPERATION_NO_UAPK)
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
    AND
    TAB_B.PHO_LINK_ID IS NOT NULL 
    AND 
    TAB_B.DISUSE_FLAG IN ('0')
;

-- *****************************************************************************
-- *** Ansible Legacy Views *****                                            ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** Ansible Pioneer Tables                                          ***
-- *****************************************************************************
-- ----更新系テーブル作成
CREATE TABLE B_OS_TYPE
(
OS_TYPE_ID                        INT                       ,

OS_TYPE_NAME                      VARCHAR (256)             ,
HARDAWRE_TYPE_SV                  INT                       ,
HARDAWRE_TYPE_ST                  INT                       ,
HARDAWRE_TYPE_NW                  INT                       ,

DISP_SEQ                          INT                       , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              VARCHAR (4000)            , -- 備考
DISUSE_FLAG                       VARCHAR (1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)               , -- 最終更新日時
LAST_UPDATE_USER                  INT                       , -- 最終更新ユーザ

PRIMARY KEY (OS_TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_OS_TYPE_JNL
(
JOURNAL_SEQ_NO                    INT                       , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)               , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)               , -- 履歴用変更種別

OS_TYPE_ID                        INT                       ,

OS_TYPE_NAME                      VARCHAR (256)             ,
HARDAWRE_TYPE_SV                  INT                       ,
HARDAWRE_TYPE_ST                  INT                       ,
HARDAWRE_TYPE_NW                  INT                       ,

DISP_SEQ                          INT                       , -- 表示順序
ACCESS_AUTH                       TEXT                      ,
NOTE                              VARCHAR (4000)            , -- 備考
DISUSE_FLAG                       VARCHAR (1)               , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)               , -- 最終更新日時
LAST_UPDATE_USER                  INT                       , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----



-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_DIALOG_TYPE
(
DIALOG_TYPE_ID                    INT                              , -- 識別シーケンス

DIALOG_TYPE_NAME                  VARCHAR (256)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (DIALOG_TYPE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_DIALOG_TYPE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

DIALOG_TYPE_ID                    INT                              , -- 識別シーケンス

DIALOG_TYPE_NAME                  VARCHAR (256)                     ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_DIALOG
(
DIALOG_MATTER_ID                  INT                              , -- 識別シーケンス

DIALOG_TYPE_ID                    INT                              ,
OS_TYPE_ID                        INT                              ,
DIALOG_MATTER_FILE                VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (DIALOG_MATTER_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_DIALOG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

DIALOG_MATTER_ID                  INT                              , -- 識別シーケンス

DIALOG_TYPE_ID                    INT                              ,
OS_TYPE_ID                        INT                              ,
DIALOG_MATTER_FILE                VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_PATTERN_LINK
(
LINK_ID                           INT                              , -- 識別シーケンス

PATTERN_ID                        INT                              ,
DIALOG_TYPE_ID                    INT                              ,
INCLUDE_SEQ                       INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

LINK_ID                           INT                              , -- 識別シーケンス

PATTERN_ID                        INT                              ,
DIALOG_TYPE_ID                    INT                              ,
INCLUDE_SEQ                       INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_PHO_LINK
(
PHO_LINK_ID                       INT                              , -- 識別シーケンス

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (PHO_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_PHO_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

PHO_LINK_ID                       INT                              , -- 識別シーケンス

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_VARS_MASTER
(
VARS_NAME_ID                      INT                              , -- 識別シーケンス

VARS_NAME                         VARCHAR (256)                    ,
VARS_DESCRIPTION                  VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_NAME_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_VARS_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_NAME_ID                      INT                              , -- 識別シーケンス

VARS_NAME                         VARCHAR (256)                    ,
VARS_DESCRIPTION                  VARCHAR (256)                    ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANS_PNS_PTN_VARS_LINK  
(
VARS_LINK_ID                      INT                              ,

PATTERN_ID                        INT                              ,
VARS_NAME_ID                      INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_PNS_PTN_VARS_LINK_JNL  
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_LINK_ID                      INT                              ,

PATTERN_ID                        INT                              ,
VARS_NAME_ID                      INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_VARS_ASSIGN
(
ASSIGN_ID                         INT                              , -- 識別シーケンス

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,
VARS_LINK_ID                      INT                              ,
VARS_ENTRY                        text                             ,
VARS_ENTRY_FILE                   text                             ,
SENSITIVE_FLAG                    VARCHAR (1)                      ,
VARS_ENTRY_USE_TPFVARS            VARCHAR (1)                      , -- 具体値のTPF変数設定有無　1:設定あり　他:設定なし
ASSIGN_SEQ                        INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ASSIGN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_PNS_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ASSIGN_ID                         INT                              , -- 識別シーケンス

OPERATION_NO_UAPK                 INT                              ,
PATTERN_ID                        INT                              ,
SYSTEM_ID                         INT                              ,
VARS_LINK_ID                      INT                              ,
VARS_ENTRY                        text                             ,
VARS_ENTRY_FILE                   text                             ,
SENSITIVE_FLAG                    VARCHAR (1)                      ,
VARS_ENTRY_USE_TPFVARS            VARCHAR (1)                      , -- 具体値のTPF変数設定有無　1:設定あり　他:設定なし
ASSIGN_SEQ                        INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- ----更新系テーブル作成
CREATE TABLE C_ANSIBLE_PNS_EXE_INS_MNG
(
EXECUTION_NO                      INT                              , -- 識別シーケンス
EXECUTION_USER                    VARCHAR (80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR (256)                    , -- シンフォニークラス名

STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                              ,
I_ANS_PARALLEL_EXE                INT                              ,
I_ANS_WINRM_ID                    INT                              ,
I_ANS_PLAYBOOK_HED_DEF            VARCHAR (512)                    ,
I_ANS_EXEC_OPTIONS                VARCHAR (512)                    ,
OPERATION_NO_UAPK                 INT                              ,
I_OPERATION_NAME                  VARCHAR (256)                    ,
I_OPERATION_NO_IDBH               INT                              ,
I_VIRTUALENV_NAME                 VARCHAR (512)                    , -- tower virtualenv path
I_ENGINE_VIRTUALENV_NAME          VARCHAR (512)                    , -- ansible  virtualenv path
I_EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                    , -- AAP 実行環境
I_ANSIBLE_CONFIG_FILE             VARCHAR (512)                    , -- ansible.cfg アップロードカラム
TIME_BOOK                         DATETIME(6)                      ,
TIME_START                        DATETIME(6)                      ,
TIME_END                          DATETIME(6)                      ,
FILE_INPUT                        VARCHAR (1024)                   ,
FILE_RESULT                       VARCHAR (1024)                   ,
RUN_MODE                          INT                              , -- ドライランモード 1:通常 2:ドライラン
EXEC_MODE                         INT                              , -- 実行モード 1:ansible/2:ansible tower
MULTIPLELOG_MODE                  INT                              , -- マルチログモード 1:マルチログモード　他:シングルログモード
LOGFILELIST_JSON                  VARCHAR (8000)                   , -- マルチログモード時のログファイル名リスト(json形式の配列)
CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID

COLLECT_STATUS                    INT                              , -- 収集ステータス
COLLECT_LOG                       VARCHAR (1024)                   , -- 収集ログ(テキスト形式)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (EXECUTION_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_ANSIBLE_PNS_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

EXECUTION_NO                      INT                              , -- 識別シーケンス
EXECUTION_USER                    VARCHAR (80)                     , -- 実行ユーザ
SYMPHONY_NAME                     VARCHAR (256)                    , -- シンフォニークラス名

STATUS_ID                         INT                              ,
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              ,
I_PATTERN_NAME                    VARCHAR (256)                    ,
I_TIME_LIMIT                      INT                              ,
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                              ,
I_ANS_PARALLEL_EXE                INT                              ,
I_ANS_WINRM_ID                    INT                              ,
I_ANS_PLAYBOOK_HED_DEF            VARCHAR (512)                    ,
I_ANS_EXEC_OPTIONS                VARCHAR (512)                    ,
OPERATION_NO_UAPK                 INT                              ,
I_OPERATION_NAME                  VARCHAR (256)                    ,
I_OPERATION_NO_IDBH               INT                              ,
I_VIRTUALENV_NAME                 VARCHAR (512)                    , -- tower virtualenv path
I_ENGINE_VIRTUALENV_NAME          VARCHAR (512)                    , -- ansible  virtualenv path
I_EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                    , -- AAP 実行環境
I_ANSIBLE_CONFIG_FILE             VARCHAR (512)                    , -- ansible.cfg アップロードカラム
TIME_BOOK                         DATETIME(6)                      ,
TIME_START                        DATETIME(6)                      ,
TIME_END                          DATETIME(6)                      ,
FILE_INPUT                        VARCHAR (1024)                   ,
FILE_RESULT                       VARCHAR (1024)                   ,
RUN_MODE                          INT                              , -- ドライランモード 1:通常 2:ドライラン
EXEC_MODE                         INT                              , -- 実行モード 1:ansible/2:ansible tower
MULTIPLELOG_MODE                  INT                              , -- マルチログモード 1:マルチログモード　他:シングルログモード
LOGFILELIST_JSON                  VARCHAR (8000)                   , -- マルチログモード時のログファイル名リスト(json形式の配列)
CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID

COLLECT_STATUS                    INT                              , -- 収集ステータス
COLLECT_LOG                       VARCHAR (1024)                   , -- 収集ログ(テキスト形式)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- *****************************************************************************
-- *** Ansible Pioneer Tables *****                                          ***
-- *****************************************************************************



-- *****************************************************************************
-- *** ***** Ansible Pioneer Views                                           ***
-- *****************************************************************************
CREATE VIEW D_OS_TYPE 
AS 
SELECT * 
FROM B_OS_TYPE;

CREATE VIEW D_OS_TYPE_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL;

CREATE VIEW D_OS_TYPE_SV 
AS 
SELECT * 
FROM B_OS_TYPE 
WHERE HARDAWRE_TYPE_SV=1;

CREATE VIEW D_OS_TYPE_SV_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL 
WHERE HARDAWRE_TYPE_SV=1;

CREATE VIEW D_OS_TYPE_ST 
AS 
SELECT * 
FROM B_OS_TYPE 
WHERE HARDAWRE_TYPE_ST=1;

CREATE VIEW D_OS_TYPE_ST_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL 
WHERE HARDAWRE_TYPE_ST=1;

CREATE VIEW D_OS_TYPE_NW 
AS 
SELECT * 
FROM B_OS_TYPE 
WHERE HARDAWRE_TYPE_NW=1;

CREATE VIEW D_OS_TYPE_NW_JNL 
AS 
SELECT * 
FROM B_OS_TYPE_JNL 
WHERE HARDAWRE_TYPE_NW=1;


CREATE VIEW D_ANSIBLE_PNS_INS_STATUS     AS 
SELECT * 
FROM B_ANSIBLE_STATUS;

CREATE VIEW D_ANSIBLE_PNS_INS_STATUS_JNL AS 
SELECT * 
FROM B_ANSIBLE_STATUS_JNL;

CREATE VIEW D_ANSIBLE_PNS_IF_INFO     AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO;

CREATE VIEW D_ANSIBLE_PNS_IF_INFO_JNL AS 
SELECT * 
FROM B_ANSIBLE_IF_INFO_JNL;

CREATE VIEW D_ANSIBLE_PNS_INS_RUN_MODE     AS 
SELECT * 
FROM B_ANSIBLE_RUN_MODE;

CREATE VIEW D_ANSIBLE_PNS_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_ANSIBLE_RUN_MODE_JNL;

CREATE VIEW D_ANSIBLE_PNS_DIALOG_TYPE     AS 
SELECT * 
FROM B_ANSIBLE_PNS_DIALOG_TYPE;

CREATE VIEW D_ANSIBLE_PNS_DIALOG_TYPE_JNL AS 
SELECT * 
FROM B_ANSIBLE_PNS_DIALOG_TYPE_JNL;

CREATE VIEW D_ANSIBLE_PNS_DIALOG AS 
SELECT  DIALOG_MATTER_ID        ,
        CONCAT(DIALOG_MATTER_ID,':',DIALOG_MATTER_FILE) DIALOG,
        DIALOG_MATTER_FILE      ,
        DISP_SEQ                ,
        ACCESS_AUTH             ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM    B_ANSIBLE_PNS_DIALOG;

CREATE VIEW D_ANSIBLE_PNS_DIALOG_JNL AS 
SELECT  JOURNAL_SEQ_NO          ,
        JOURNAL_REG_DATETIME    ,
        JOURNAL_ACTION_CLASS    ,
        DIALOG_MATTER_ID        ,
        CONCAT(DIALOG_MATTER_ID,':',DIALOG_MATTER_FILE) DIALOG,
        DIALOG_MATTER_FILE      ,
        DISP_SEQ                ,
        ACCESS_AUTH             ,
        NOTE                    ,
        DISUSE_FLAG             ,
        LAST_UPDATE_TIMESTAMP   ,
        LAST_UPDATE_USER
FROM B_ANSIBLE_PNS_DIALOG_JNL;

CREATE VIEW E_ANSIBLE_PNS_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        ANS_EXEC_OPTIONS              ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 4;

CREATE VIEW E_ANSIBLE_PNS_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        ANS_EXEC_OPTIONS              ,
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 4;

CREATE VIEW D_ANS_PNS_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 
FROM B_ANS_PNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;

CREATE VIEW D_ANS_PNS_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 
FROM B_ANS_PNS_PTN_VARS_LINK_JNL TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
-- 構造名ポストフィックス(_VFS)=「View-For-P(ulldownSelect)」
-- 登録/更新用なので、結合するテーブルのレコードが廃止されていたら、レコードとして扱わない
CREATE VIEW D_ANS_PNS_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_PNS_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;

CREATE VIEW E_ANSIBLE_PNS_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_ANSIBLE_PNS_EXE_INS_MNG       TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE VIEW E_ANSIBLE_PNS_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_ANSIBLE_PNS_EXE_INS_MNG_JNL   TAB_A
LEFT JOIN E_ANSIBLE_PNS_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_PNS_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE VIEW E_OPE_FOR_PULLDOWN_PNS
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       TAB_A.OPERATION            ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.PHO_LINK_ID          ,
       TAB_B.DISUSE_FLAG           DISUSE_FLAG_2,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM 
    E_OPERATION_LIST TAB_A
    LEFT JOIN B_ANSIBLE_PNS_PHO_LINK TAB_B ON (TAB_A.OPERATION_NO_UAPK = TAB_B.OPERATION_NO_UAPK)
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
    AND
    TAB_B.PHO_LINK_ID IS NOT NULL 
    AND 
    TAB_B.DISUSE_FLAG IN ('0')
;

-- *****************************************************************************
-- *** Ansible Pioneer Views *****                                           ***
-- *****************************************************************************


-- *****************************************************************************
-- *** ***** Ansible Legacy Role Tables                                      ***
-- *****************************************************************************

-- -------------------------------------------------------
-- T-0001 作業インスタンス
-- -------------------------------------------------------
CREATE TABLE C_ANSIBLE_LRL_EXE_INS_MNG
(
EXECUTION_NO                      INT                              ,
EXECUTION_USER                    VARCHAR (80)                     , -- 作業パターン名
SYMPHONY_NAME                     VARCHAR (256)                    , -- シンフォニークラス名

STATUS_ID                         INT                              , -- 状態
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              , -- 作業パターン
I_PATTERN_NAME                    VARCHAR (256)                    , -- 作業パターン名
I_TIME_LIMIT                      INT                              , -- 遅延タイマ
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                              , -- ホスト指定方式
I_ANS_PARALLEL_EXE                INT                              , -- 並列実行数
I_ANS_WINRM_ID                    INT                              , -- WINRM接続
I_ANS_PLAYBOOK_HED_DEF            VARCHAR (512)                    ,
I_ANS_EXEC_OPTIONS                VARCHAR (512)                    ,
OPERATION_NO_UAPK                 INT                              , -- オペレーションNo
I_OPERATION_NAME                  VARCHAR (256)                    , -- オペレーション名
I_OPERATION_NO_IDBH               INT                              , -- オペレーションID
I_VIRTUALENV_NAME                 VARCHAR (512)                    , -- tower virtualenv path
I_ENGINE_VIRTUALENV_NAME          VARCHAR (512)                    , -- ansible  virtualenv path
I_EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                    , -- AAP 実行環境
I_ANSIBLE_CONFIG_FILE             VARCHAR (512)                    , -- ansible.cfg アップロードカラム
TIME_BOOK                         DATETIME(6)                      , -- 予約日時
TIME_START                        DATETIME(6)                      , -- 開始日時
TIME_END                          DATETIME(6)                      , -- 終了日時
FILE_INPUT                        VARCHAR (1024)                   , -- 投入データ格納ファイル(ZIP形式)
FILE_RESULT                       VARCHAR (1024)                   , -- 結果データ格納ファイル(ZIP形式)
RUN_MODE                          INT                              , -- ドライランモード 1:通常 2:ドライラン
EXEC_MODE                         INT                              , -- 実行モード 1:ansible/2:ansible tower
MULTIPLELOG_MODE                  INT                              , -- マルチログモード 1:マルチログモード　他:シングルログモード
LOGFILELIST_JSON                  VARCHAR (8000)                   , -- マルチログモード時のログファイル名リスト(json形式の配列)
CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID

COLLECT_STATUS                    INT                              , -- 収集ステータス
COLLECT_LOG                       VARCHAR (1024)                   , -- 収集ログ(テキスト形式)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (EXECUTION_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE C_ANSIBLE_LRL_EXE_INS_MNG_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

EXECUTION_NO                      INT                              ,
EXECUTION_USER                    VARCHAR (80)                     , -- 作業パターン名
SYMPHONY_NAME                     VARCHAR (256)                    , -- シンフォニークラス名

STATUS_ID                         INT                              , -- 状態
SYMPHONY_INSTANCE_NO              INT                              ,
PATTERN_ID                        INT                              , -- 作業パターン
I_PATTERN_NAME                    VARCHAR (256)                    , -- 作業パターン名
I_TIME_LIMIT                      INT                              , -- 遅延タイマ
I_ANS_HOST_DESIGNATE_TYPE_ID      INT                              , -- ホスト指定方式
I_ANS_PARALLEL_EXE                INT                              , -- 並列実行数
I_ANS_WINRM_ID                    INT                              , -- WINRM接続
I_ANS_PLAYBOOK_HED_DEF            VARCHAR (512)                    ,
I_ANS_EXEC_OPTIONS                VARCHAR (512)                    ,
OPERATION_NO_UAPK                 INT                              , -- オペレーションNo
I_OPERATION_NAME                  VARCHAR (256)                    , -- オペレーション名
I_OPERATION_NO_IDBH               INT                              , -- オペレーションID
I_VIRTUALENV_NAME                 VARCHAR (512)                    , -- tower virtualenv path
I_ENGINE_VIRTUALENV_NAME          VARCHAR (512)                    , -- ansible  virtualenv path
I_EXECUTION_ENVIRONMENT_NAME      VARCHAR (512)                    , -- AAP 実行環境
I_ANSIBLE_CONFIG_FILE             VARCHAR (512)                    , -- ansible.cfg アップロードカラム
TIME_BOOK                         DATETIME(6)                      , -- 予約日時
TIME_START                        DATETIME(6)                      , -- 開始日時
TIME_END                          DATETIME(6)                      , -- 終了日時
FILE_INPUT                        VARCHAR (1024)                   , -- 投入データ格納ファイル(ZIP形式)
FILE_RESULT                       VARCHAR (1024)                   , -- 結果データ格納ファイル(ZIP形式)
RUN_MODE                          INT                              , -- ドライランモード 1:通常 2:ドライラン
EXEC_MODE                         INT                              , -- 実行モード 1:ansible/2:ansible tower
MULTIPLELOG_MODE                  INT                              , -- マルチログモード 1:マルチログモード　他:シングルログモード
LOGFILELIST_JSON                  VARCHAR (8000)                   , -- マルチログモード時のログファイル名リスト(json形式の配列)
CONDUCTOR_NAME                    VARCHAR (256)                    , -- コンダクタ名
CONDUCTOR_INSTANCE_NO             INT                              , -- コンダクタ インスタンスID

COLLECT_STATUS                    INT                              , -- 収集ステータス
COLLECT_LOG                       VARCHAR (1024)                   , -- 収集ログ(テキスト形式)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0002 ロールパッケージ管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_ROLE_PACKAGE
(
ROLE_PACKAGE_ID                   INT                              , -- 識別シーケンス

ROLE_PACKAGE_NAME                 VARCHAR (256)                    , -- ロールパッケージ名
ROLE_PACKAGE_FILE                 VARCHAR (256)                    , -- ロールパッケージファイル(ZIP形式)
VAR_STRUCT_ANAL_JSON_STRING_FILE  VARCHAR (100)                    , -- 変数解析結果を保存する為のFileUploadカラム(隠し)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ROLE_PACKAGE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_ROLE_PACKAGE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ROLE_PACKAGE_ID                   INT                              , -- 識別シーケンス

ROLE_PACKAGE_NAME                 VARCHAR (256)                    , -- ロールパッケージ名
ROLE_PACKAGE_FILE                 VARCHAR (256)                    , -- ロールパッケージファイル(ZIP形式)
VAR_STRUCT_ANAL_JSON_STRING_FILE  VARCHAR (100)                    , -- 変数解析結果を保存する為のFileUploadカラム(隠し)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------

-- -------------------------------------------------------
-- T-0003 ロール名管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_ROLE
(
ROLE_ID                           INT                              , -- 識別シーケンス

ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージ名
ROLE_NAME                         VARCHAR (1024)                   , -- ロール名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ROLE_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_ROLE_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ROLE_ID                           INT                              , -- 識別シーケンス

ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージ名
ROLE_NAME                         VARCHAR (1024)                   , -- ロール名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0004 ロール変数管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_ROLE_VARS
(
VARS_NAME_ID                      INT                              , -- 識別シーケンス

ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージ名
ROLE_ID                           INT                              , -- ロール名
VARS_NAME                         VARCHAR (256)                    , -- 変数名
VARS_ATTRIBUTE_01                 INT                              , -- 変数属性
                                                                     -- -- 1:一般変数
                                                                     -- -- 2:複数具体値変数
                                                                     -- -- 3:多次元変数

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_NAME_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_ROLE_VARS_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_NAME_ID                      INT                              , -- 識別シーケンス

ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージ名
ROLE_ID                           INT                              , -- ロール名
VARS_NAME                         VARCHAR (256)                    , -- 変数名
VARS_ATTRIBUTE_01                 INT                              , -- 変数属性
                                                                     -- -- 1:一般変数
                                                                     -- -- 2:複数具体値変数
                                                                     -- -- 3:多次元変数

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0005 作業パターン詳細
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_PATTERN_LINK
(
LINK_ID                           INT                              ,

PATTERN_ID                        INT                              , -- 作業パターンID
ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージ名
ROLE_ID                           INT                              , -- ロールID
INCLUDE_SEQ                       INT                              , -- include順序

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_PATTERN_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

LINK_ID                           INT                              ,

PATTERN_ID                        INT                              , -- 作業パターンID
ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージ名
ROLE_ID                           INT                              , -- ロールID
INCLUDE_SEQ                       INT                              , -- include順序

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0006 変数一覧
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_VARS_MASTER
(
VARS_NAME_ID                      INT                              ,

VARS_NAME                         VARCHAR (256)                    , -- 変数名
VARS_ATTRIBUTE_01                 INT                              , 
VARS_DESCRIPTION                  VARCHAR (256)                    , -- 変数説明

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_NAME_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_VARS_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_NAME_ID                      INT                              ,

VARS_NAME                         VARCHAR (256)                    , -- 変数名
VARS_ATTRIBUTE_01                 INT                              , 
VARS_DESCRIPTION                  VARCHAR (256)                    , -- 変数説明

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0006-0002 子変数一覧
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_CHILD_VARS
(
CHILD_VARS_NAME_ID                INT                              , -- 識別シーケンス

PARENT_VARS_NAME_ID               INT                              , -- 親の変数名ID
CHILD_VARS_NAME                   VARCHAR (1024)                   , -- 変数名
ARRAY_MEMBER_ID                   INT                              , -- 多次元変数メンバー管理  Pkey
ASSIGN_SEQ_NEED                   INT                              , -- 代入順序の入力有(1)/無(null)
COL_SEQ_NEED                      INT                              , -- 列順序の入力有(1)/無(null)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (CHILD_VARS_NAME_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_CHILD_VARS_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

CHILD_VARS_NAME_ID                INT                              , -- 識別シーケンス

PARENT_VARS_NAME_ID               INT                              , -- 親の変数名ID
CHILD_VARS_NAME                   VARCHAR (1024)                   , -- 変数名
ARRAY_MEMBER_ID                   INT                              , -- 多次元変数メンバー管理  Pkey
ASSIGN_SEQ_NEED                   INT                              , -- 代入順序の入力有(1)/無(null)
COL_SEQ_NEED                      INT                              , -- 列順序の入力有(1)/無(null)

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----


-- -------------------------------------------------------
-- T-0007 作業パターン変数紐付管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LRL_PTN_VARS_LINK
(
VARS_LINK_ID                      INT                              ,

PATTERN_ID                        INT                              , -- 作業パターン
VARS_NAME_ID                      INT                              , -- 変数

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARS_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LRL_PTN_VARS_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARS_LINK_ID                      INT                              ,

PATTERN_ID                        INT                              , -- 作業パターン
VARS_NAME_ID                      INT                              , -- 変数

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0008 代入値管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_VARS_ASSIGN
(
ASSIGN_ID                         INT                              ,

OPERATION_NO_UAPK                 INT                              , -- オペレーション
PATTERN_ID                        INT                              , -- 作業パターン
SYSTEM_ID                         INT                              , -- 機器(ホスト)
VARS_LINK_ID                      INT                              , -- 作業パターン変数紐付
COL_SEQ_COMBINATION_ID            INT                              , -- 多次元変数配列組合せ管理 Pkey
VARS_ENTRY                        text                             , -- 具体値
VARS_ENTRY_FILE                   text                             ,
SENSITIVE_FLAG                    VARCHAR (1)                      ,
VARS_ENTRY_USE_TPFVARS            VARCHAR (1)                      , -- 具体値のTPF変数設定有無　1:設定あり　他:設定なし
ASSIGN_SEQ                        INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ASSIGN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_VARS_ASSIGN_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ASSIGN_ID                         INT                              ,

OPERATION_NO_UAPK                 INT                              , -- オペレーション
PATTERN_ID                        INT                              , -- 作業パターン
SYSTEM_ID                         INT                              , -- 機器(ホスト)
VARS_LINK_ID                      INT                              , -- 作業パターン変数紐付
COL_SEQ_COMBINATION_ID            INT                              , -- 多次元変数配列組合せ管理 Pkey
VARS_ENTRY                        text                             , -- 具体値
VARS_ENTRY_FILE                   text                             ,
SENSITIVE_FLAG                    VARCHAR (1)                      ,
VARS_ENTRY_USE_TPFVARS            VARCHAR (1)                      , -- 具体値のTPF変数設定有無　1:設定あり　他:設定なし
ASSIGN_SEQ                        INT                              ,

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------


-- -------------------------------------------------------
-- T-0009 作業対象ホスト管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_PHO_LINK
(
PHO_LINK_ID                       INT                              ,

OPERATION_NO_UAPK                 INT                              , -- オペレーション
PATTERN_ID                        INT                              , -- 作業パターン
SYSTEM_ID                         INT                              , -- ホスト

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (PHO_LINK_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANSIBLE_LRL_PHO_LINK_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

PHO_LINK_ID                       INT                              ,

OPERATION_NO_UAPK                 INT                              , -- オペレーション
PATTERN_ID                        INT                              , -- 作業パターン
SYSTEM_ID                         INT                              , -- ホスト

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----
-- END----------------------------------------------------

-- -------------------------------------------------------
-- T-0010 変数具体値管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LRL_ROLE_VARSVAL
(
VARSVAL_ID                        INT                              , -- 識別シーケンス

ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージID
ROLE_ID                           INT                              , -- ロールID
VAR_TYPE                          INT                              , -- 変数タイプ 1:一般変数 2:複数具体値変数 3:配列変数
VARS_NAME_ID                      INT                              , -- 変数名/配列変数名
COL_SEQ_COMBINATION_ID            INT                              , -- 変数名
ASSIGN_SEQ                        INT                              , -- 代入順序
VARS_VALUE                        VARCHAR (8192)                   , -- 具体値

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (VARSVAL_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LRL_ROLE_VARSVAL_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

VARSVAL_ID                        INT                              , -- 識別シーケンス

ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージID
ROLE_ID                           INT                              , -- ロールID
VAR_TYPE                          INT                              , -- 変数タイプ 1:一般変数 2:複数具体値変数 3:配列変数
VARS_NAME_ID                      INT                              , -- 変数名/配列変数名
COL_SEQ_COMBINATION_ID            INT                              , -- 変数名
ASSIGN_SEQ                        INT                              , -- 代入順序
VARS_VALUE                        VARCHAR (8192)                   , -- 具体値

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- T-0011 多次元変数メンバー管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LRL_ARRAY_MEMBER
(
ARRAY_MEMBER_ID                   INT                              , -- 識別シーケンス

VARS_NAME_ID                      INT                              , -- 変数名一覧 Pkey
PARENT_VARS_KEY_ID                INT                              , -- 親メンバー変数へのキー 
VARS_KEY_ID                       INT                              , -- 自メンバー変数のキー
VARS_NAME                         VARCHAR (256)                    , -- メンバー変数名　　0:配列変数を示す
ARRAY_NEST_LEVEL                  INT                              , -- 階層 1～
ASSIGN_SEQ_NEED                   INT                              , -- 代入順序有無　1:必要　初期値:NULL
COL_SEQ_NEED                      INT                              , -- 列順序有無  　1:必要　初期値:NULL
MEMBER_DISP                       INT                              , -- 代入値管理系の表示有無　1:必要　初期値:NULL
MAX_COL_SEQ                       INT                              , -- 最大繰返数
VRAS_NAME_PATH                    VARCHAR (512)                    , -- メンバー変数の階層パス
VRAS_NAME_ALIAS                   VARCHAR (1024)                   , -- メンバー変数名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ARRAY_MEMBER_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LRL_ARRAY_MEMBER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ARRAY_MEMBER_ID                   INT                              , -- 識別シーケンス

VARS_NAME_ID                      INT                              , -- 変数名一覧 Pkey
PARENT_VARS_KEY_ID                INT                              , -- 親メンバー変数へのキー 
VARS_KEY_ID                       INT                              , -- 自メンバー変数のキー
VARS_NAME                         VARCHAR (256)                    , -- メンバー変数名　　0:配列変数を示す
ARRAY_NEST_LEVEL                  INT                              , -- 階層 1～
ASSIGN_SEQ_NEED                   INT                              , -- 代入順序有無　1:必要　初期値:NULL
COL_SEQ_NEED                      INT                              , -- 列順序有無  　1:必要　初期値:NULL
MEMBER_DISP                       INT                              , -- 代入値管理系の表示有無　1:必要　初期値:NULL
MAX_COL_SEQ                       INT                              , -- 最大繰返数
VRAS_NAME_PATH                    VARCHAR (512)                    , -- メンバー変数の階層パス
VRAS_NAME_ALIAS                   VARCHAR (1024)                   , -- メンバー変数名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----


-- -------------------------------------------------------
-- T-0012 多次元変数最大繰返数管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LRL_MAX_MEMBER_COL
(
MAX_COL_SEQ_ID                    INT                              , -- 識別シーケンス

VARS_NAME_ID                      INT                              , -- 変数名一覧 Pkey
ARRAY_MEMBER_ID                   INT                              , -- 多次元変数メンバー管理 Pkey
MAX_COL_SEQ                       INT                              , -- 最大繰返数

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (MAX_COL_SEQ_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LRL_MAX_MEMBER_COL_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

MAX_COL_SEQ_ID                    INT                              , -- 識別シーケンス

VARS_NAME_ID                      INT                              , -- 変数名一覧 Pkey
ARRAY_MEMBER_ID                   INT                              , -- 多次元変数メンバー管理 Pkey
MAX_COL_SEQ                       INT                              , -- 最大繰返数

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----


-- -------------------------------------------------------
-- T-0013 多次元変数配列組合せ管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LRL_MEMBER_COL_COMB
(
COL_SEQ_COMBINATION_ID            INT                              , -- 識別シーケンス

VARS_NAME_ID                      INT                              , -- 変数名一覧 Pkey
ARRAY_MEMBER_ID                   INT                              , -- 多次元変数メンバー管理 Pkey
COL_COMBINATION_MEMBER_ALIAS      VARCHAR (4000)                   , -- プルダウン表示メンバー変数
COL_SEQ_VALUE                     VARCHAR (4000)                   , -- すべての列順序

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (COL_SEQ_COMBINATION_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LRL_MEMBER_COL_COMB_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

COL_SEQ_COMBINATION_ID            INT                              , -- 識別シーケンス

VARS_NAME_ID                      INT                              , -- 変数名一覧 Pkey
ARRAY_MEMBER_ID                   INT                              , -- 多次元変数メンバー管理 Pkey
COL_COMBINATION_MEMBER_ALIAS      VARCHAR (4000)                   , -- プルダウン表示メンバー変数
COL_SEQ_VALUE                     VARCHAR (4000)                   , -- すべての列順序

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- T-0015 ロールパッケージ読替変数管理(メニュー対象外)
-- -------------------------------------------------------
CREATE TABLE B_ANS_LRL_RP_REP_VARS_LIST
(
ROW_ID                            INT                              , -- 識別シーケンス
ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージID
ROLE_ID                           INT                              , -- ロールID
REP_VARS_NAME                     VARCHAR (256)                    , -- 読替変数名
ANY_VARS_NAME                     VARCHAR (256)                    , -- 任意変数名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (ROW_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

CREATE TABLE B_ANS_LRL_RP_REP_VARS_LIST_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

ROW_ID                            INT                              , -- 識別シーケンス
ROLE_PACKAGE_ID                   INT                              , -- ロールパッケージID
ROLE_ID                           INT                              , -- ロールID
REP_VARS_NAME                     VARCHAR (256)                    , -- 読替変数名
ANY_VARS_NAME                     VARCHAR (256)                    , -- 任意変数名

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;

-- END----------------------------------------------------

-- *****************************************************************************
-- *** ***** Ansible Legacy Role Tables                                      ***
-- *****************************************************************************

-- *****************************************************************************
-- *** ***** Ansible Legacy Role Views                                       ***
-- *****************************************************************************
-- -------------------------------------------------------
-- V-0001 ステータス一覧
-- -------------------------------------------------------
CREATE VIEW D_ANSIBLE_LRL_INS_STATUS     AS 
SELECT * 
FROM B_ANSIBLE_STATUS;

CREATE VIEW D_ANSIBLE_LRL_INS_STATUS_JNL AS 
SELECT * 
FROM B_ANSIBLE_STATUS_JNL;
-- END----------------------------------------------------

CREATE VIEW D_ANSIBLE_LRL_INS_RUN_MODE     AS 
SELECT * 
FROM B_ANSIBLE_RUN_MODE;

CREATE VIEW D_ANSIBLE_LRL_INS_RUN_MODE_JNL AS 
SELECT * 
FROM B_ANSIBLE_RUN_MODE_JNL;

-- -------------------------------------------------------
-- V-0002 作業パターン一覧
-- -------------------------------------------------------
CREATE VIEW E_ANSIBLE_LRL_PATTERN AS 
SELECT 
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 5;

CREATE VIEW E_ANSIBLE_LRL_PATTERN_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                ,
        JOURNAL_REG_DATETIME          ,
        JOURNAL_ACTION_CLASS          ,
        PATTERN_ID                    ,
        PATTERN_NAME                  ,
        CONCAT(PATTERN_ID,':',PATTERN_NAME) PATTERN,
        ITA_EXT_STM_ID                ,
        TIME_LIMIT                    ,
        ANS_HOST_DESIGNATE_TYPE_ID    ,
        ANS_PARALLEL_EXE              ,
        ANS_WINRM_ID                  ,
        ANS_PLAYBOOK_HED_DEF          ,
        ANS_EXEC_OPTIONS              ,
        ANS_VIRTUALENV_NAME           ,
        ANS_ENGINE_VIRTUALENV_NAME    ,
        ANS_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
        ANS_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
        DISP_SEQ                      ,
        ACCESS_AUTH                   ,
        NOTE                          ,
        DISUSE_FLAG                   ,
        LAST_UPDATE_TIMESTAMP         ,
        LAST_UPDATE_USER
FROM C_PATTERN_PER_ORCH_JNL TAB_A
WHERE TAB_A.ITA_EXT_STM_ID = 5;
-- END----------------------------------------------------

-- -------------------------------------------------------
-- V-0003 作業インスタンス情報
-- -------------------------------------------------------
CREATE VIEW E_ANSIBLE_LRL_EXE_INS_MNG AS
SELECT 
         TAB_A.EXECUTION_NO              ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER
FROM C_ANSIBLE_LRL_EXE_INS_MNG       TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;

CREATE VIEW E_ANSIBLE_LRL_EXE_INS_MNG_JNL AS 
SELECT 
         TAB_A.JOURNAL_SEQ_NO            ,
         TAB_A.JOURNAL_REG_DATETIME      ,
         TAB_A.JOURNAL_ACTION_CLASS      ,
         TAB_A.EXECUTION_NO              ,
         TAB_A.SYMPHONY_NAME             ,
         TAB_A.EXECUTION_USER            ,
         TAB_A.STATUS_ID                 ,
         TAB_C.STATUS_NAME               ,
         TAB_A.SYMPHONY_INSTANCE_NO      ,
         TAB_A.PATTERN_ID                ,
         TAB_A.I_PATTERN_NAME            ,
         TAB_A.I_TIME_LIMIT              ,
         TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID ,
         TAB_E.HOST_DESIGNATE_TYPE_NAME    ANS_HOST_DESIGNATE_TYPE_NAME,
         TAB_A.I_ANS_PARALLEL_EXE        ,
         TAB_A.I_ANS_WINRM_ID            ,
         TAB_A.I_ANS_PLAYBOOK_HED_DEF    ,
         TAB_A.I_ANS_EXEC_OPTIONS        ,
         TAB_F.FLAG_NAME                   ANS_WINRM_FLAG_NAME,
         TAB_A.OPERATION_NO_UAPK         ,
         TAB_A.I_OPERATION_NAME          ,
         TAB_A.I_OPERATION_NO_IDBH       ,
         TAB_A.I_VIRTUALENV_NAME         ,
         TAB_A.I_ENGINE_VIRTUALENV_NAME  ,
         TAB_A.I_EXECUTION_ENVIRONMENT_NAME, -- AAP 実行環境
         TAB_A.I_ANSIBLE_CONFIG_FILE       , -- ansible.cfg アップロードカラム
         TAB_A.TIME_BOOK                 ,
         TAB_A.TIME_START                ,
         TAB_A.TIME_END                  ,
         TAB_A.FILE_INPUT                ,
         TAB_A.FILE_RESULT               ,
         TAB_A.RUN_MODE                  ,
         TAB_D.RUN_MODE_NAME             ,
         TAB_A.EXEC_MODE                 ,
         TAB_G.NAME AS EXEC_MODE_NAME    ,
         TAB_A.MULTIPLELOG_MODE          ,
         TAB_A.LOGFILELIST_JSON          ,
         TAB_A.CONDUCTOR_NAME            ,
         TAB_A.CONDUCTOR_INSTANCE_NO     ,
         TAB_A.DISP_SEQ                  ,
         TAB_A.ACCESS_AUTH               ,
         TAB_A.NOTE                      ,
         TAB_A.DISUSE_FLAG               ,
         TAB_A.LAST_UPDATE_TIMESTAMP     ,
         TAB_A.LAST_UPDATE_USER           
FROM C_ANSIBLE_LRL_EXE_INS_MNG_JNL TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_B.PATTERN_ID = TAB_A.PATTERN_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_STATUS   TAB_C ON ( TAB_A.STATUS_ID = TAB_C.STATUS_ID )
LEFT JOIN D_ANSIBLE_LRL_INS_RUN_MODE TAB_D ON ( TAB_A.RUN_MODE = TAB_D.RUN_MODE_ID )
LEFT JOIN B_HOST_DESIGNATE_TYPE_LIST TAB_E ON ( TAB_A.I_ANS_HOST_DESIGNATE_TYPE_ID = TAB_E.HOST_DESIGNATE_TYPE_ID )
LEFT JOIN D_FLAG_LIST_01             TAB_F ON ( TAB_A.I_ANS_WINRM_ID = TAB_F.FLAG_ID )
LEFT JOIN B_ANSIBLE_EXEC_MODE        TAB_G ON ( TAB_A.EXEC_MODE = TAB_G.ID )
;
-- END----------------------------------------------------

-- -------------------------------------------------------
-- V-0005-1 ロールパッケージ一覧
-- -------------------------------------------------------
CREATE VIEW D_ANSIBLE_LRL_ROLE_PKG_LIST AS 
SELECT 
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME) ROLE_PACKAGE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE_PACKAGE TAB_A
;
CREATE VIEW D_ANSIBLE_LRL_ROLE_PKG_LIST_JNL AS 
SELECT
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME) ROLE_PACKAGE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE_PACKAGE_JNL TAB_A
;
-- END----------------------------------------------------

-- -------------------------------------------------------
-- V-0005-2 ロール一覧
-- -------------------------------------------------------
CREATE VIEW D_ANSIBLE_LRL_ROLE_LIST AS 
SELECT 
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_ID,':',TAB_A.ROLE_NAME) ROLE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE     TAB_A
;

CREATE VIEW D_ANSIBLE_LRL_ROLE_LIST_JNL AS 
SELECT 
        TAB_A.*                       ,
        CONCAT(TAB_A.ROLE_ID,':',TAB_A.ROLE_NAME) ROLE_NAME_PULLDOWN
FROM B_ANSIBLE_LRL_ROLE_JNL TAB_A
;
-- END----------------------------------------------------

-- -------------------------------------------------------
-- V-0006 代入値管理コンボ間リンク
-- -------------------------------------------------------
-- 構造名ポストフィックス(_VFS)=「View-For-P(ulldownSelect)」
-- 登録/更新用なので、結合するテーブルのレコードが廃止されていたら、レコードとして扱わない
CREATE VIEW D_ANS_LRL_PTN_VARS_LINK_VFP AS 
SELECT 
        TAB_A.VARS_LINK_ID            ,
        TAB_A.PATTERN_ID              ,
        TAB_B.PATTERN_NAME            ,
        TAB_A.VARS_NAME_ID            ,
        TAB_C.VARS_NAME               ,
        TAB_C.VARS_ATTRIBUTE_01       ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER        ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LRL_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
WHERE TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;
-- END----------------------------------------------------

-- -------------------------------------------------------
-- V-0007 代入値管理コンボ間リンク2
-- -------------------------------------------------------
CREATE VIEW D_ANS_LRL_PTN_VARS_LINK AS 
SELECT 
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        TAB_C.VARS_ATTRIBUTE_01             ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LRL_PTN_VARS_LINK     TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
CREATE VIEW D_ANS_LRL_PTN_VARS_LINK_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.VARS_LINK_ID                  ,
        TAB_A.PATTERN_ID                    ,
        TAB_B.PATTERN_NAME                  ,
        TAB_A.VARS_NAME_ID                  ,
        TAB_C.VARS_NAME                     ,
        TAB_C.VARS_ATTRIBUTE_01             ,
        CONCAT(TAB_A.VARS_LINK_ID,':',TAB_C.VARS_NAME) VARS_LINK_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANS_LRL_PTN_VARS_LINK_JNL TAB_A
LEFT JOIN E_ANSIBLE_LRL_PATTERN      TAB_B ON ( TAB_A.PATTERN_ID = TAB_B.PATTERN_ID )
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_C ON ( TAB_A.VARS_NAME_ID = TAB_C.VARS_NAME_ID )
;
-- END----------------------------------------------------


-- -------------------------------------------------------
-- V-0007-0002 子変数
-- -------------------------------------------------------
-- 構造名ポストフィックス(_VFS)=「View-For-P(ulldownSelect)」
-- 登録/更新用なので、結合するテーブルのレコードが廃止されていたら、レコードとして扱わない
CREATE VIEW D_ANS_LRL_CHILD_VARS_VFP AS 
SELECT 
        TAB_A.CHILD_VARS_NAME_ID      ,
        TAB_A.CHILD_VARS_NAME         ,
        TAB_A.PARENT_VARS_NAME_ID     ,
        TAB_A.ARRAY_MEMBER_ID         ,
        TAB_A.ASSIGN_SEQ_NEED         ,
        TAB_A.COL_SEQ_NEED            ,
        TAB_B.VARS_NAME               ,
        TAB_B.VARS_ATTRIBUTE_01       ,
        TAB_C.VARS_LINK_ID            ,
        CONCAT(TAB_A.CHILD_VARS_NAME_ID,':',TAB_A.CHILD_VARS_NAME) CHILD_VARS_PULLDOWN,
        TAB_A.DISP_SEQ                ,
        TAB_A.ACCESS_AUTH             ,
        TAB_A.NOTE                    ,
        TAB_A.DISUSE_FLAG             ,
        TAB_A.LAST_UPDATE_TIMESTAMP   ,
        TAB_A.LAST_UPDATE_USER
FROM B_ANSIBLE_LRL_CHILD_VARS         TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_B ON ( TAB_A.PARENT_VARS_NAME_ID = TAB_B.VARS_NAME_ID )
LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_C ON ( TAB_B.VARS_NAME_ID = TAB_C.VARS_NAME_ID)
WHERE TAB_B.VARS_ATTRIBUTE_01 IN (3)
AND TAB_A.DISUSE_FLAG = '0'
AND TAB_B.DISUSE_FLAG = '0'
AND TAB_C.DISUSE_FLAG = '0'
;
-- END----------------------------------------------------
-- ReMiTicket1091----

-- -------------------------------------------------------
-- V-0007-0003 子変数
-- -------------------------------------------------------
CREATE VIEW D_ANS_LRL_CHILD_VARS AS 
SELECT 
        TAB_A.CHILD_VARS_NAME_ID            ,
        TAB_A.CHILD_VARS_NAME               ,
        TAB_A.PARENT_VARS_NAME_ID           ,
        TAB_B.VARS_NAME                     ,
        TAB_B.VARS_ATTRIBUTE_01             ,
        TAB_C.VARS_LINK_ID                  ,
        CONCAT(TAB_A.CHILD_VARS_NAME_ID,':',TAB_A.CHILD_VARS_NAME) CHILD_VARS_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANSIBLE_LRL_CHILD_VARS         TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_B ON ( TAB_A.PARENT_VARS_NAME_ID = TAB_B.VARS_NAME_ID )
LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_C ON ( TAB_B.VARS_NAME_ID = TAB_C.VARS_NAME_ID)
WHERE TAB_B.VARS_ATTRIBUTE_01 IN (3)
;
CREATE VIEW D_ANS_LRL_CHILD_VARS_JNL AS 
SELECT 
        JOURNAL_SEQ_NO                      ,
        JOURNAL_REG_DATETIME                ,
        JOURNAL_ACTION_CLASS                ,
        TAB_A.CHILD_VARS_NAME_ID            ,
        TAB_A.CHILD_VARS_NAME               ,
        TAB_A.PARENT_VARS_NAME_ID           ,
        TAB_B.VARS_NAME                     ,
        TAB_B.VARS_ATTRIBUTE_01             ,
        TAB_C.VARS_LINK_ID                  ,
        CONCAT(TAB_A.CHILD_VARS_NAME_ID,':',TAB_A.CHILD_VARS_NAME) CHILD_VARS_PULLDOWN,
        TAB_A.DISP_SEQ                      ,
        TAB_A.ACCESS_AUTH                   ,
        TAB_A.NOTE                          ,
        TAB_A.DISUSE_FLAG                   ,
        TAB_A.LAST_UPDATE_TIMESTAMP         ,
        TAB_A.LAST_UPDATE_USER              ,
        TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
        TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02
FROM B_ANSIBLE_LRL_CHILD_VARS_JNL     TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER   TAB_B ON ( TAB_A.PARENT_VARS_NAME_ID = TAB_B.VARS_NAME_ID )
LEFT JOIN B_ANS_LRL_PTN_VARS_LINK TAB_C ON ( TAB_B.VARS_NAME_ID = TAB_C.VARS_NAME_ID)
WHERE TAB_B.VARS_ATTRIBUTE_01 IN (3)
;
-- END----------------------------------------------------

-- -------------------------------------------------------
-- V-0007-0004 オペレーション絞込
-- -------------------------------------------------------
CREATE VIEW E_OPE_FOR_PULLDOWN_LRL
AS 
SELECT TAB_A.OPERATION_NO_UAPK    ,
       TAB_A.OPERATION_NAME       ,
       TAB_A.OPERATION_DATE       ,
       TAB_A.OPERATION_NO_IDBH    ,
       TAB_A.OPERATION            ,
       TAB_A.DISP_SEQ             ,
       TAB_A.ACCESS_AUTH          ,
       TAB_A.NOTE                 ,
       TAB_A.DISUSE_FLAG          ,
       TAB_A.LAST_UPDATE_TIMESTAMP,
       TAB_A.LAST_UPDATE_USER     ,
       TAB_B.PHO_LINK_ID          ,
       TAB_B.DISUSE_FLAG           DISUSE_FLAG_2,
       TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01
FROM 
    E_OPERATION_LIST TAB_A
    LEFT JOIN B_ANSIBLE_LRL_PHO_LINK TAB_B ON (TAB_A.OPERATION_NO_UAPK = TAB_B.OPERATION_NO_UAPK)
WHERE
    TAB_A.DISUSE_FLAG IN ('0') 
    AND
    TAB_B.PHO_LINK_ID IS NOT NULL 
    AND 
    TAB_B.DISUSE_FLAG IN ('0')
;

-- -------------------------------------------------------
-- V-0008 K社 代入値管理Rest用　代入値関連リスト
-- -------------------------------------------------------

-- -------------------------------------------------------
-- V-0011 多次元変数メンバー管理
-- -------------------------------------------------------
CREATE VIEW D_ANS_LRL_ARRAY_MEMBER AS
SELECT
    ARRAY_MEMBER_ID                 ,
    
    VARS_NAME_ID                    ,
    PARENT_VARS_KEY_ID              ,
    VARS_KEY_ID                     ,
    VARS_NAME                       ,
    ARRAY_NEST_LEVEL                ,
    ASSIGN_SEQ_NEED                 ,
    COL_SEQ_NEED                    ,
    MEMBER_DISP                     ,
    MAX_COL_SEQ                     ,
    VRAS_NAME_PATH                  ,
    VRAS_NAME_ALIAS                 ,
    CASE VRAS_NAME_ALIAS
        WHEN '0' THEN '-'
        ELSE VRAS_NAME_ALIAS
    END VRAS_NAME                   ,
    
    DISP_SEQ                        ,
    ACCESS_AUTH                     ,
    NOTE                            ,
    DISUSE_FLAG                     ,
    LAST_UPDATE_TIMESTAMP           ,
    LAST_UPDATE_USER                
FROM
    B_ANS_LRL_ARRAY_MEMBER
;

CREATE VIEW D_ANS_LRL_ARRAY_MEMBER_JNL AS
SELECT
    JOURNAL_SEQ_NO                  ,
    JOURNAL_REG_DATETIME            ,
    JOURNAL_ACTION_CLASS            ,
    
    ARRAY_MEMBER_ID                 ,
    
    VARS_NAME_ID                    ,
    PARENT_VARS_KEY_ID              ,
    VARS_KEY_ID                     ,
    VARS_NAME                       ,
    ARRAY_NEST_LEVEL                ,
    ASSIGN_SEQ_NEED                 ,
    COL_SEQ_NEED                    ,
    MEMBER_DISP                     ,
    MAX_COL_SEQ                     ,
    VRAS_NAME_PATH                  ,
    VRAS_NAME_ALIAS                 ,
    CASE VRAS_NAME_ALIAS
        WHEN '0' THEN '-'
        ELSE VRAS_NAME_ALIAS
    END VRAS_NAME                   ,
    
    DISP_SEQ                        ,
    ACCESS_AUTH                     ,
    NOTE                            ,
    DISUSE_FLAG                     ,
    LAST_UPDATE_TIMESTAMP           ,
    LAST_UPDATE_USER                
FROM
    B_ANS_LRL_ARRAY_MEMBER_JNL
;

-- -------------------------------------------------------
-- V-0013 多次元変数配列組合せ管理
-- -------------------------------------------------------
CREATE VIEW D_ANS_LRL_MEMBER_COL_COMB AS
SELECT
    COL_SEQ_COMBINATION_ID          ,
    
    VARS_NAME_ID                    ,
    ARRAY_MEMBER_ID                 ,
    COL_COMBINATION_MEMBER_ALIAS    ,
    COL_SEQ_VALUE                   ,
    CONCAT(COL_SEQ_COMBINATION_ID,':',COL_COMBINATION_MEMBER_ALIAS) COMBINATION_MEMBER,
    
    DISP_SEQ                        ,
    ACCESS_AUTH                     ,
    NOTE                            ,
    DISUSE_FLAG                     ,
    LAST_UPDATE_TIMESTAMP           ,
    LAST_UPDATE_USER                
FROM
    B_ANS_LRL_MEMBER_COL_COMB
;

CREATE VIEW D_ANS_LRL_MEMBER_COL_COMB_JNL AS
SELECT
    JOURNAL_SEQ_NO                  ,
    JOURNAL_REG_DATETIME            ,
    JOURNAL_ACTION_CLASS            ,
    
    COL_SEQ_COMBINATION_ID          ,
    
    VARS_NAME_ID                    ,
    ARRAY_MEMBER_ID                 ,
    COL_COMBINATION_MEMBER_ALIAS    ,
    COL_SEQ_VALUE                   ,
    CONCAT(COL_SEQ_COMBINATION_ID,':',COL_COMBINATION_MEMBER_ALIAS) COMBINATION_MEMBER,
    
    DISP_SEQ                        ,
    ACCESS_AUTH                     ,
    NOTE                            ,
    DISUSE_FLAG                     ,
    LAST_UPDATE_TIMESTAMP           ,
    LAST_UPDATE_USER                
FROM
    B_ANS_LRL_MEMBER_COL_COMB_JNL
;

-- -------------------------------------------------------
-- --T4-0004 Legacy 代入値自動登録設定
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LNS_VAL_ASSIGN (
COLUMN_ID                      INT                     , -- 識別シーケンス
MENU_ID                        INT                     , -- メニューID
COLUMN_LIST_ID                 INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     INT                     , -- 作業パターンID
VAL_VARS_LINK_ID               INT                     , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         INT                     , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 INT                     , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         INT                     , -- Value値　列順序
KEY_VARS_LINK_ID               INT                     , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         INT                     , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 INT                     , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         INT                     , -- Key値　列順序
NULL_DATA_HANDLING_FLG         INT                     , -- Null値の連携

DISP_SEQ                       INT                     , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           VARCHAR (4000)          , -- 備考
DISUSE_FLAG                    VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER               INT                     , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LNS_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                 INT                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)             , -- 履歴用変更種別

COLUMN_ID                      INT                     , -- 識別シーケンス
MENU_ID                        INT                     , -- メニューID
COLUMN_LIST_ID                 INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     INT                     , -- 作業パターンID
VAL_VARS_LINK_ID               INT                     , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         INT                     , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 INT                     , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         INT                     , -- Value値　列順序
KEY_VARS_LINK_ID               INT                     , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         INT                     , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 INT                     , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         INT                     , -- Key値　列順序
NULL_DATA_HANDLING_FLG         INT                     , -- Null値の連携

DISP_SEQ                       INT                     , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           VARCHAR (4000)          , -- 備考
DISUSE_FLAG                    VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER               INT                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- --T4-0005 Legacy Role 代入値自動登録設定
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_LRL_VAL_ASSIGN (
COLUMN_ID                      INT                     , -- 識別シーケンス
MENU_ID                        INT                     , -- メニューID
COLUMN_LIST_ID                 INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     INT                     , -- 作業パターンID
VAL_VARS_LINK_ID               INT                     , -- Value値　作業パターン変数紐付
VAL_COL_SEQ_COMBINATION_ID     INT                     , -- 多次元変数配列組合せ管理 Pkey
VAL_ASSIGN_SEQ                 INT                     , -- Value値　代入順序
KEY_VARS_LINK_ID               INT                     , -- Key値　作業パターン変数紐付
KEY_COL_SEQ_COMBINATION_ID     INT                     , -- 多次元変数配列組合せ管理 Pkey
KEY_ASSIGN_SEQ                 INT                     , -- Key値　代入順序
NULL_DATA_HANDLING_FLG         INT                     , -- Null値の連携

DISP_SEQ                       INT                     , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           VARCHAR (4000)          , -- 備考
DISUSE_FLAG                    VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER               INT                     , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_LRL_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                 INT                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)             , -- 履歴用変更種別

COLUMN_ID                      INT                     , -- 識別シーケンス
MENU_ID                        INT                     , -- メニューID
COLUMN_LIST_ID                 INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     INT                     , -- 作業パターンID
VAL_VARS_LINK_ID               INT                     , -- Value値　作業パターン変数紐付
VAL_COL_SEQ_COMBINATION_ID     INT                     , -- 多次元変数配列組合せ管理 Pkey
VAL_ASSIGN_SEQ                 INT                     , -- Value値　代入順序
KEY_VARS_LINK_ID               INT                     , -- Key値　作業パターン変数紐付
KEY_COL_SEQ_COMBINATION_ID     INT                     , -- 多次元変数配列組合せ管理 Pkey
KEY_ASSIGN_SEQ                 INT                     , -- Key値　代入順序
NULL_DATA_HANDLING_FLG         INT                     , -- Null値の連携

DISP_SEQ                       INT                     , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           VARCHAR (4000)          , -- 備考
DISUSE_FLAG                    VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER               INT                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- --T4-0006  Pioneer 代入値自動登録設定
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_PNS_VAL_ASSIGN (
COLUMN_ID                      INT                     , -- 識別シーケンス
MENU_ID                        INT                     , -- メニューID
COLUMN_LIST_ID                 INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     INT                     , -- 作業パターンID
VAL_VARS_LINK_ID               INT                     , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         INT                     , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 INT                     , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         INT                     , -- Value値　列順序
KEY_VARS_LINK_ID               INT                     , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         INT                     , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 INT                     , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         INT                     , -- Key値　列順序
NULL_DATA_HANDLING_FLG         INT                     , -- Null値の連携

DISP_SEQ                       INT                     , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           VARCHAR (4000)          , -- 備考
DISUSE_FLAG                    VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER               INT                     , -- 最終更新ユーザ
PRIMARY KEY(COLUMN_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_PNS_VAL_ASSIGN_JNL
(
JOURNAL_SEQ_NO                 INT                     , -- 履歴用シーケンス
JOURNAL_REG_DATETIME           DATETIME(6)             , -- 履歴用変更日時
JOURNAL_ACTION_CLASS           VARCHAR (8)             , -- 履歴用変更種別

COLUMN_ID                      INT                     , -- 識別シーケンス
MENU_ID                        INT                     , -- メニューID
COLUMN_LIST_ID                 INT                     , -- CMDB処理対象メニューカラム一覧の識別シーケンス
COL_TYPE                       INT                     , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
PATTERN_ID                     INT                     , -- 作業パターンID
VAL_VARS_LINK_ID               INT                     , -- Value値　作業パターン変数紐付
VAL_CHILD_VARS_LINK_ID         INT                     , -- Value値　作業パターンメンバー変数紐付
VAL_ASSIGN_SEQ                 INT                     , -- Value値　代入順序
VAL_CHILD_VARS_COL_SEQ         INT                     , -- Value値　列順序
KEY_VARS_LINK_ID               INT                     , -- Key値　作業パターン変数紐付
KEY_CHILD_VARS_LINK_ID         INT                     , -- Key値　作業パターンメンバー変数紐付
KEY_ASSIGN_SEQ                 INT                     , -- Key値　代入順序
KEY_CHILD_VARS_COL_SEQ         INT                     , -- Key値　列順序
NULL_DATA_HANDLING_FLG         INT                     , -- Null値の連携

DISP_SEQ                       INT                     , -- 表示順序
ACCESS_AUTH                    TEXT                    ,
NOTE                           VARCHAR (4000)          , -- 備考
DISUSE_FLAG                    VARCHAR (1)             , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP          DATETIME(6)             , -- 最終更新日時
LAST_UPDATE_USER               INT                     , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----

-- -------------------------------------------------------
-- --V4-0006 Legacy 代入値自動登録設定メニュー用　VIEW
-- -------------------------------------------------------
CREATE VIEW D_ANS_LNS_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_CHILD_VARS_LINK_ID         , -- Value値　作業パターンメンバー変数紐付
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.VAL_CHILD_VARS_COL_SEQ         , -- Value値　列順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_CHILD_VARS_LINK_ID         , -- Key値　作業パターンメンバー変数紐付
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.KEY_CHILD_VARS_COL_SEQ         , -- Key値　列順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID  REST_VAL_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_VARS_LINK_ID  REST_KEY_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_LNS_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE VIEW D_ANS_LNS_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_CHILD_VARS_LINK_ID         , -- Value値　作業パターンメンバー変数紐付
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.VAL_CHILD_VARS_COL_SEQ         , -- Value値　列順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_CHILD_VARS_LINK_ID         , -- Key値　作業パターンメンバー変数紐付
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.KEY_CHILD_VARS_COL_SEQ         , -- Key値　列順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID  REST_VAL_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_VARS_LINK_ID  REST_KEY_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_LNS_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

-- -------------------------------------------------------
-- --V4-0007 Legacy Role 代入値自動登録設定メニュー用　VIEW
-- -------------------------------------------------------
CREATE VIEW D_ANS_LRL_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID             REST_COLUMN_LIST_ID,             -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID           REST_VAL_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.VAL_COL_SEQ_COMBINATION_ID REST_VAL_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Value値　多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_VARS_LINK_ID           REST_KEY_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_COL_SEQ_COMBINATION_ID REST_KEY_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Key値　多次元変数配列組合せ管理 Pkey
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_LRL_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE VIEW D_ANS_LRL_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_COL_SEQ_COMBINATION_ID     , -- 多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID             REST_COLUMN_LIST_ID,             -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID           REST_VAL_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.VAL_COL_SEQ_COMBINATION_ID REST_VAL_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Value値　多次元変数配列組合せ管理 Pkey
       TAB_A.KEY_VARS_LINK_ID           REST_KEY_VARS_LINK_ID,           -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_COL_SEQ_COMBINATION_ID REST_KEY_COL_SEQ_COMBINATION_ID, -- REST/EXCEL/CSV用　Key値　多次元変数配列組合せ管理 Pkey
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_LRL_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

-- -------------------------------------------------------
-- --V4-0008 Pioneer 代入値自動登録設定メニュー用　VIEW
-- -------------------------------------------------------
CREATE VIEW D_ANS_PNS_VAL_ASSIGN AS 
SELECT 
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_CHILD_VARS_LINK_ID         , -- Value値　作業パターンメンバー変数紐付
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.VAL_CHILD_VARS_COL_SEQ         , -- Value値　列順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_CHILD_VARS_LINK_ID         , -- Key値　作業パターンメンバー変数紐付
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.KEY_CHILD_VARS_COL_SEQ         , -- Key値　列順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID  REST_VAL_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_VARS_LINK_ID  REST_KEY_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_PNS_VAL_ASSIGN TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

CREATE VIEW D_ANS_PNS_VAL_ASSIGN_JNL AS 
SELECT TAB_A.JOURNAL_SEQ_NO                 ,
       TAB_A.JOURNAL_REG_DATETIME           ,
       TAB_A.JOURNAL_ACTION_CLASS           ,
       TAB_A.COLUMN_ID                      , -- 識別シーケンス
       TAB_A.MENU_ID                        , -- メニューID
       TAB_A.COLUMN_LIST_ID                 , -- CMDB処理対象メニューカラム一覧の識別シーケンス
       TAB_A.COL_TYPE                       , -- カラムタイプ　1/空白:Value型　2:Key-Value型　
       TAB_A.PATTERN_ID                     , -- 作業パターンID
       TAB_A.VAL_VARS_LINK_ID               , -- Value値　作業パターン変数紐付
       TAB_A.VAL_CHILD_VARS_LINK_ID         , -- Value値　作業パターンメンバー変数紐付
       TAB_A.VAL_ASSIGN_SEQ                 , -- Value値　代入順序
       TAB_A.VAL_CHILD_VARS_COL_SEQ         , -- Value値　列順序
       TAB_A.KEY_VARS_LINK_ID               , -- Key値　作業パターン変数紐付
       TAB_A.KEY_CHILD_VARS_LINK_ID         , -- Key値　作業パターンメンバー変数紐付
       TAB_A.KEY_ASSIGN_SEQ                 , -- Key値　代入順序
       TAB_A.KEY_CHILD_VARS_COL_SEQ         , -- Key値　列順序
       TAB_A.NULL_DATA_HANDLING_FLG         , -- Null値の連携
       TAB_B.MENU_GROUP_ID                  ,
       TAB_B.MENU_GROUP_ID     MENU_GROUP_ID_CLONE,
       TAB_C.MENU_GROUP_NAME                ,
       TAB_A.MENU_ID           MENU_ID_CLONE,
       TAB_A.MENU_ID           MENU_ID_CLONE_02,
       TAB_B.MENU_NAME                      ,
       TAB_A.COLUMN_LIST_ID    REST_COLUMN_LIST_ID,      -- REST/EXCEL/CSV用　CMDB処理対象メニューグループ+メニュー+カラム一覧の識別シーケンス
       TAB_A.VAL_VARS_LINK_ID  REST_VAL_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Value値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.KEY_VARS_LINK_ID  REST_KEY_VARS_LINK_ID,    -- REST/EXCEL/CSV用　Key値　作業パターン+変数名(作業パターン変数紐付)
       TAB_A.DISP_SEQ                       ,
       TAB_A.ACCESS_AUTH                    ,
       TAB_A.NOTE                           ,
       TAB_A.DISUSE_FLAG                    ,
       TAB_A.LAST_UPDATE_TIMESTAMP          ,
       TAB_A.LAST_UPDATE_USER 
FROM B_ANS_PNS_VAL_ASSIGN_JNL TAB_A
LEFT JOIN A_MENU_LIST TAB_B ON (TAB_A.MENU_ID = TAB_B.MENU_ID)
LEFT JOIN A_MENU_GROUP_LIST TAB_C ON (TAB_B.MENU_GROUP_ID = TAB_C.MENU_GROUP_ID);

-- -------------------------------------------------------
-- -- Ansible 共通 グローバル変数管理
-- -------------------------------------------------------
-- ----更新系テーブル作成
CREATE TABLE B_ANS_GLOBAL_VARS_MASTER
(
GBL_VARS_NAME_ID                  INT                              , -- 識別シーケンス

VARS_NAME                         VARCHAR (128)                    , -- グローバル変数名
VARS_ENTRY                        VARCHAR (8192)                   , -- 具体値
VARS_DESCRIPTION                  VARCHAR (256)                    , -- 変数説明

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ

PRIMARY KEY (GBL_VARS_NAME_ID)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 更新系テーブル作成----

-- ----履歴系テーブル作成
CREATE TABLE B_ANS_GLOBAL_VARS_MASTER_JNL
(
JOURNAL_SEQ_NO                    INT                              , -- 履歴用シーケンス
JOURNAL_REG_DATETIME              DATETIME(6)                      , -- 履歴用変更日時
JOURNAL_ACTION_CLASS              VARCHAR (8)                      , -- 履歴用変更種別

GBL_VARS_NAME_ID                  INT                              , -- 識別シーケンス

VARS_NAME                         VARCHAR (128)                    , -- グローバル変数名
VARS_ENTRY                        VARCHAR (8192)                   , -- 具体値
VARS_DESCRIPTION                  VARCHAR (256)                    , -- 変数説明

DISP_SEQ                          INT                              , -- 表示順序
ACCESS_AUTH                       TEXT                             ,
NOTE                              VARCHAR (4000)                   , -- 備考
DISUSE_FLAG                       VARCHAR (1)                      , -- 廃止フラグ
LAST_UPDATE_TIMESTAMP             DATETIME(6)                      , -- 最終更新日時
LAST_UPDATE_USER                  INT                              , -- 最終更新ユーザ
PRIMARY KEY(JOURNAL_SEQ_NO)
)ENGINE = InnoDB, CHARSET = utf8, COLLATE = utf8_bin, ROW_FORMAT=COMPRESSED ,KEY_BLOCK_SIZE=8;
-- 履歴系テーブル作成----


-- 索引作成

-- -- ansible共通 追加Index
CREATE        INDEX IND_B_ANS_GLOBAL_VARS_MASTER_01   ON B_ANS_GLOBAL_VARS_MASTER      (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_STATUS_01           ON B_ANSIBLE_STATUS              (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_IF_INFO_01          ON B_ANSIBLE_IF_INFO             (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_RUN_MODE_01         ON B_ANSIBLE_RUN_MODE            (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_VARS_TYPE_01            ON B_ANS_VARS_TYPE               (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_TEMPLATE_FILE_01        ON B_ANS_TEMPLATE_FILE           (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_TEMPLATE_FILE_02        ON B_ANS_TEMPLATE_FILE           (ANS_TEMPLATE_VARS_NAME,DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_CONTENTS_FILE_01        ON B_ANS_CONTENTS_FILE           (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_CONTENTS_FILE_02        ON B_ANS_CONTENTS_FILE           (CONTENTS_FILE_VARS_NAME,DISUSE_FLAG);

-- -- Legacy 追加Index
CREATE        INDEX IND_B_ANSIBLE_LNS_PLAYBOOK_01     ON B_ANSIBLE_LNS_PLAYBOOK        (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_PATTERN_LINK_01 ON B_ANSIBLE_LNS_PATTERN_LINK    (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_PATTERN_LINK_02 ON B_ANSIBLE_LNS_PATTERN_LINK    (PATTERN_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_PHO_LINK_01     ON B_ANSIBLE_LNS_PHO_LINK        (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_PHO_LINK_02     ON B_ANSIBLE_LNS_PHO_LINK        (OPERATION_NO_UAPK,PATTERN_ID,SYSTEM_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_VARS_MASTER_01  ON B_ANSIBLE_LNS_VARS_MASTER     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_VARS_MASTER_02  ON B_ANSIBLE_LNS_VARS_MASTER     (VARS_NAME);
CREATE        INDEX IND_B_ANS_LNS_PTN_VARS_LINK_01    ON B_ANS_LNS_PTN_VARS_LINK       (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LNS_PTN_VARS_LINK_02    ON B_ANS_LNS_PTN_VARS_LINK       (PATTERN_ID ,VARS_NAME_ID);
CREATE        INDEX IND_B_ANS_LNS_PTN_VARS_LINK_03    ON B_ANS_LNS_PTN_VARS_LINK       (PATTERN_ID ,VARS_LINK_ID ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LNS_PTN_VARS_LINK_04    ON B_ANS_LNS_PTN_VARS_LINK       (VARS_LINK_ID ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_VARS_ASSIGN_01  ON B_ANSIBLE_LNS_VARS_ASSIGN     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_VARS_ASSIGN_03  ON B_ANSIBLE_LNS_VARS_ASSIGN     (OPERATION_NO_UAPK,PATTERN_ID,SYSTEM_ID,VARS_LINK_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_VARS_ASSIGN_04  ON B_ANSIBLE_LNS_VARS_ASSIGN     (OPERATION_NO_UAPK,PATTERN_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LNS_VARS_ASSIGN_05  ON B_ANSIBLE_LNS_VARS_ASSIGN     (VARS_ENTRY_USE_TPFVARS,DISUSE_FLAG);
CREATE        INDEX IND_C_ANSIBLE_LNS_EXE_INS_MNG_01  ON C_ANSIBLE_LNS_EXE_INS_MNG     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LNS_VAL_ASSIGN_01       ON B_ANS_LNS_VAL_ASSIGN          (DISUSE_FLAG);

-- -- Pioneer 追加Index
CREATE        INDEX IND_B_ANSIBLE_PNS_DIALOG_TYPE_01  ON B_ANSIBLE_PNS_DIALOG_TYPE     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_DIALOG_01       ON B_ANSIBLE_PNS_DIALOG          (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_PATTERN_LINK_01 ON B_ANSIBLE_PNS_PATTERN_LINK    (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_PATTERN_LINK_02 ON B_ANSIBLE_PNS_PATTERN_LINK    (PATTERN_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_PHO_LINK_01     ON B_ANSIBLE_PNS_PHO_LINK        (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_PHO_LINK_02     ON B_ANSIBLE_PNS_PHO_LINK        (OPERATION_NO_UAPK,PATTERN_ID,SYSTEM_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_VARS_MASTER_01  ON B_ANSIBLE_PNS_VARS_MASTER     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_VARS_MASTER_02  ON B_ANSIBLE_PNS_VARS_MASTER     (VARS_NAME);
CREATE        INDEX IND_B_ANS_PNS_PTN_VARS_LINK_01    ON B_ANS_PNS_PTN_VARS_LINK       (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_PNS_PTN_VARS_LINK_02    ON B_ANS_PNS_PTN_VARS_LINK       (PATTERN_ID ,VARS_NAME_ID);
CREATE        INDEX IND_B_ANS_PNS_PTN_VARS_LINK_03    ON B_ANS_PNS_PTN_VARS_LINK       (PATTERN_ID ,VARS_LINK_ID ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_VARS_ASSIGN_01  ON B_ANSIBLE_PNS_VARS_ASSIGN     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_VARS_ASSIGN_03  ON B_ANSIBLE_PNS_VARS_ASSIGN     (OPERATION_NO_UAPK,PATTERN_ID,SYSTEM_ID,VARS_LINK_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_VARS_ASSIGN_04  ON B_ANSIBLE_PNS_VARS_ASSIGN     (OPERATION_NO_UAPK,PATTERN_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_PNS_VARS_ASSIGN_05  ON B_ANSIBLE_PNS_VARS_ASSIGN     (VARS_ENTRY_USE_TPFVARS,DISUSE_FLAG);
CREATE        INDEX IND_C_ANSIBLE_PNS_EXE_INS_MNG_01  ON C_ANSIBLE_PNS_EXE_INS_MNG     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_PNS_VAL_ASSIGN_01       ON B_ANS_PNS_VAL_ASSIGN          (DISUSE_FLAG);


-- -- Role 追加Index
CREATE        INDEX IND_C_ANSIBLE_LRL_EXE_INS_MNG_01  ON C_ANSIBLE_LRL_EXE_INS_MNG     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_ROLE_PACKAGE_01 ON B_ANSIBLE_LRL_ROLE_PACKAGE    (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_ROLE_01         ON B_ANSIBLE_LRL_ROLE            (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_ROLE_VARS_01    ON B_ANSIBLE_LRL_ROLE_VARS       (DISUSE_FLAG);
CREATE UNIQUE INDEX IND_B_ANSIBLE_LRL_ROLE_VARS_02    ON B_ANSIBLE_LRL_ROLE_VARS       (ROLE_PACKAGE_ID, ROLE_ID , VARS_NAME);
CREATE        INDEX IND_B_ANSIBLE_LRL_PATTERN_LINK_01 ON B_ANSIBLE_LRL_PATTERN_LINK    (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_PATTERN_LINK_02 ON B_ANSIBLE_LRL_PATTERN_LINK    (PATTERN_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_VARS_MASTER_01  ON B_ANSIBLE_LRL_VARS_MASTER     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_CHILD_VARS_01   ON B_ANSIBLE_LRL_CHILD_VARS      (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_CHILD_VARS_02   ON B_ANSIBLE_LRL_CHILD_VARS      (ARRAY_MEMBER_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LRL_PTN_VARS_LINK_01    ON B_ANS_LRL_PTN_VARS_LINK       (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LRL_PTN_VARS_LINK_02    ON B_ANS_LRL_PTN_VARS_LINK       (PATTERN_ID ,VARS_LINK_ID ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_VARS_ASSIGN_01  ON B_ANSIBLE_LRL_VARS_ASSIGN     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_VARS_ASSIGN_02  ON B_ANSIBLE_LRL_VARS_ASSIGN     (OPERATION_NO_UAPK  ,PATTERN_ID  ,SYSTEM_ID  ,VARS_LINK_ID  ,COL_SEQ_COMBINATION_ID ,DISUSE_FLAG ,ASSIGN_SEQ);
CREATE        INDEX IND_B_ANSIBLE_LRL_VARS_ASSIGN_03  ON B_ANSIBLE_LRL_VARS_ASSIGN     (OPERATION_NO_UAPK,PATTERN_ID,SYSTEM_ID,VARS_LINK_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_VARS_ASSIGN_04  ON B_ANSIBLE_LRL_VARS_ASSIGN     (OPERATION_NO_UAPK,PATTERN_ID,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_VARS_ASSIGN_05  ON B_ANSIBLE_LRL_VARS_ASSIGN     (VARS_ENTRY_USE_TPFVARS,DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_PHO_LINK_01     ON B_ANSIBLE_LRL_PHO_LINK        (DISUSE_FLAG);
CREATE        INDEX IND_B_ANSIBLE_LRL_PHO_LINK_02     ON B_ANSIBLE_LRL_PHO_LINK        (OPERATION_NO_UAPK  ,PATTERN_ID  ,SYSTEM_ID  ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LRL_ROLE_VARSVAL_01     ON B_ANS_LRL_ROLE_VARSVAL        (DISUSE_FLAG);
CREATE UNIQUE INDEX IND_B_ANS_LRL_ROLE_VARSVAL_02     ON B_ANS_LRL_ROLE_VARSVAL        (ROLE_PACKAGE_ID, ROLE_ID, VAR_TYPE, VARS_NAME_ID, COL_SEQ_COMBINATION_ID, ASSIGN_SEQ );
CREATE        INDEX IND_B_ANS_LRL_ARRAY_MEMBER_01     ON B_ANS_LRL_ARRAY_MEMBER        (DISUSE_FLAG);
CREATE UNIQUE INDEX IND_B_ANS_LRL_ARRAY_MEMBER_02     ON B_ANS_LRL_ARRAY_MEMBER        (VARS_NAME_ID ,PARENT_VARS_KEY_ID ,VARS_KEY_ID ,VARS_NAME ,ARRAY_NEST_LEVEL ,ASSIGN_SEQ_NEED ,COL_SEQ_NEED ,MEMBER_DISP ,VRAS_NAME_PATH ,MAX_COL_SEQ);
CREATE        INDEX IND_B_ANS_LRL_ARRAY_MEMBER_03     ON B_ANS_LRL_ARRAY_MEMBER        (VARS_NAME_ID ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LRL_MAX_MEMBER_COL_01   ON B_ANS_LRL_MAX_MEMBER_COL      (DISUSE_FLAG);
CREATE UNIQUE INDEX IND_B_ANS_LRL_MAX_MEMBER_COL_02   ON B_ANS_LRL_MAX_MEMBER_COL      (VARS_NAME_ID ,ARRAY_MEMBER_ID ,DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LRL_MEMBER_COL_COMB_01  ON B_ANS_LRL_MEMBER_COL_COMB     (DISUSE_FLAG);
CREATE        INDEX IND_B_ANS_LRL_MEMBER_COL_COMB_02  ON B_ANS_LRL_MEMBER_COL_COMB     (ARRAY_MEMBER_ID);
CREATE        INDEX IND_B_ANS_LRL_RP_REP_VARS_LIST_01 ON B_ANS_LRL_RP_REP_VARS_LIST    (DISUSE_FLAG);
CREATE UNIQUE INDEX IND_B_ANS_LRL_RP_REP_VARS_LIST_02 ON B_ANS_LRL_RP_REP_VARS_LIST    (ROLE_PACKAGE_ID ,ROLE_ID ,REP_VARS_NAME ,ANY_VARS_NAME);
CREATE        INDEX IND_B_ANS_LRL_VAL_ASSIGN_01       ON B_ANS_LRL_VAL_ASSIGN          (DISUSE_FLAG);

-- -------------------------------------------------------
-- -- Ansible プルダウンを動的に生成しいるメニュー項目のREST対応
-- -------------------------------------------------------
-- -------------------------------------------------------
-- Pioneer 代入値管理用 View
-- -------------------------------------------------------
CREATE VIEW D_B_ANSIBLE_PNS_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_PNS_VARS_ASSIGN TAB_A;
CREATE VIEW D_B_ANSIBLE_PNS_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_PNS_VARS_ASSIGN_JNL TAB_A;

-- -------------------------------------------------------
-- Pioneer 代入値管理/代入値自動登録用 REST API対応
--         Movement+変数名  リスト用 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_PNS_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_PNS_PTN_VARS_LINK             TAB_A
  LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';


CREATE VIEW E_ANS_PNS_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_PNS_PTN_VARS_LINK_JNL             TAB_A
  LEFT JOIN B_ANSIBLE_PNS_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- Pioneer 代入値管理/代入値自動登録用 REST API対応
--         ホスト一覧 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_PNS_STM_LIST AS
SELECT
  TAB_A.*                             ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_PNS_PHO_LINK  TAB_A
  LEFT JOIN C_STM_LIST    TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_PNS_STM_LIST_JNL AS
SELECT
  TAB_A.*                             ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_PNS_PHO_LINK_JNL TAB_A
  LEFT JOIN C_STM_LIST_JNL   TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- Legacy 代入値管理用 View
-- -------------------------------------------------------
CREATE VIEW D_B_ANSIBLE_LNS_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_LNS_VARS_ASSIGN TAB_A;
CREATE VIEW D_B_ANSIBLE_LNS_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID
FROM
  B_ANSIBLE_LNS_VARS_ASSIGN_JNL TAB_A;

-- -------------------------------------------------------
-- Legacy 代入値管理/代入値自動登録用 REST API対応
--        Movement+変数名  リスト用 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_LNS_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LNS_PTN_VARS_LINK             TAB_A
  LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_LNS_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_C.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LNS_PTN_VARS_LINK_JNL             TAB_A
  LEFT JOIN B_ANSIBLE_LNS_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- Legacy  代入値管理/代入値自動登録用 REST API対応
--         ホスト一覧 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_LNS_STM_LIST AS
SELECT
  TAB_A.*                              ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LNS_PHO_LINK  TAB_A
  LEFT JOIN C_STM_LIST    TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_LNS_STM_LIST_JNL AS
SELECT
  TAB_A. *                             ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01  ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LNS_PHO_LINK_JNL TAB_A
  LEFT JOIN C_STM_LIST_JNL TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';
  
-- -------------------------------------------------------
-- legacy Role Movement詳細用 View
-- -------------------------------------------------------
CREATE VIEW D_B_ANSIBLE_LRL_PATTERN_LINK AS
SELECT
  TAB_A.*,
  TAB_A.ROLE_ID REST_ROLE_ID
FROM
  B_ANSIBLE_LRL_PATTERN_LINK TAB_A;

CREATE VIEW D_B_ANSIBLE_LRL_PATTERN_LINK_JNL AS
SELECT
  TAB_A.*,
  TAB_A.ROLE_ID REST_ROLE_ID
FROM
  B_ANSIBLE_LRL_PATTERN_LINK_JNL TAB_A;
  
-- -------------------------------------------------------
-- Legacy Role Movement詳細 REST API対応
--             ROLE_PACKAGE_ID+ROLE_IDリスト用 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_LRL_PKG_ROLE_LIST AS
SELECT
  TAB_A.*                             ,
  TAB_B.ROLE_ID                       ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME,':',TAB_B.ROLE_ID,':',TAB_B.ROLE_NAME) ROLE_PACKAGE_PULLDOWN
FROM
  B_ANSIBLE_LRL_ROLE_PACKAGE   TAB_A
  LEFT JOIN B_ANSIBLE_LRL_ROLE TAB_B ON ( TAB_B.ROLE_PACKAGE_ID = TAB_A.ROLE_PACKAGE_ID )
WHERE
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_A.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_LRL_PKG_ROLE_LIST_JNL AS 
SELECT
  TAB_A.*                             ,
  TAB_B.ROLE_ID                       ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_A.ROLE_PACKAGE_ID,':',TAB_A.ROLE_PACKAGE_NAME,':',TAB_B.ROLE_ID,':',TAB_B.ROLE_NAME) ROLE_PACKAGE_PULLDOWN
FROM
  B_ANSIBLE_LRL_ROLE_PACKAGE_JNL   TAB_A
  LEFT JOIN B_ANSIBLE_LRL_ROLE_JNL TAB_B ON ( TAB_B.ROLE_PACKAGE_ID = TAB_A.ROLE_PACKAGE_ID )
WHERE
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_A.DISUSE_FLAG = '0';

  
-- -------------------------------------------------------
-- Legacy Role 代入値管理用 View
-- -------------------------------------------------------
CREATE VIEW D_B_ANSIBLE_LRL_VARS_ASSIGN AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID,
  TAB_A.COL_SEQ_COMBINATION_ID  REST_COL_SEQ_COMBINATION_ID
FROM
  B_ANSIBLE_LRL_VARS_ASSIGN TAB_A;
  
CREATE VIEW D_B_ANSIBLE_LRL_VARS_ASSIGN_JNL AS
SELECT
  TAB_A.*,
  TAB_A.SYSTEM_ID               REST_SYSTEM_ID,
  TAB_A.VARS_LINK_ID            REST_VARS_LINK_ID,
  TAB_A.COL_SEQ_COMBINATION_ID  REST_COL_SEQ_COMBINATION_ID
FROM
  B_ANSIBLE_LRL_VARS_ASSIGN_JNL TAB_A;

-- -------------------------------------------------------
-- Legacy Role 代入値管理/代入値自動登録用 REST API対応
--             Movement+変数名  リスト用 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_LRL_PTN_VAR_LIST AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LRL_PTN_VARS_LINK             TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_LRL_PTN_VAR_LIST_JNL AS
SELECT DISTINCT
  TAB_A.VARS_LINK_ID                  ,
  TAB_A.PATTERN_ID                    ,
  TAB_A.VARS_NAME_ID                  ,
  TAB_A.DISP_SEQ                      ,
  TAB_C.ACCESS_AUTH                   ,
  TAB_A.NOTE                          ,
  TAB_A.DISUSE_FLAG                   ,
  TAB_A.LAST_UPDATE_TIMESTAMP         ,
  TAB_A.LAST_UPDATE_USER              ,
  TAB_A.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_02 ,
  CONCAT(TAB_A.PATTERN_ID,':',TAB_C.PATTERN_NAME,':',TAB_A.VARS_LINK_ID,':',TAB_B.VARS_NAME) PTN_VAR_PULLDOWN
FROM
  B_ANS_LRL_PTN_VARS_LINK_JNL             TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
  LEFT JOIN C_PATTERN_PER_ORCH_JNL        TAB_C ON ( TAB_A.PATTERN_ID   = TAB_C.PATTERN_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0' AND
  TAB_C.DISUSE_FLAG = '0';

-- -------------------------------------------------------
-- Legacy Role 代入値管理/代入値自動登録用 REST API対応
--             変数名+メンバー変数  リスト用 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_LRL_VAR_MEMBER_LIST AS
SELECT DISTINCT
  TAB_A.*,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.VARS_NAME,'.',TAB_A.COL_SEQ_COMBINATION_ID,':',TAB_A.COL_COMBINATION_MEMBER_ALIAS) VAR_MEMBER_PULLDOWN
FROM
  B_ANS_LRL_MEMBER_COL_COMB               TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER     TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_LRL_VAR_MEMBER_LIST_JNL AS
SELECT DISTINCT
  TAB_A.*,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.VARS_NAME,'.',TAB_A.COL_SEQ_COMBINATION_ID,':',TAB_A.COL_COMBINATION_MEMBER_ALIAS) VAR_MEMBER_PULLDOWN
FROM
  B_ANS_LRL_MEMBER_COL_COMB_JNL           TAB_A
  LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER_JNL TAB_B ON ( TAB_A.VARS_NAME_ID = TAB_B.VARS_NAME_ID )
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';


-- -------------------------------------------------------
-- Legacy  代入値管理/代入値自動登録用 REST API対応
--         ホスト一覧 View
-- -------------------------------------------------------
CREATE VIEW E_ANS_LRL_STM_LIST AS
SELECT
  TAB_A.*  ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LRL_PHO_LINK  TAB_A
  LEFT JOIN C_STM_LIST    TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW E_ANS_LRL_STM_LIST_JNL AS
SELECT
  TAB_A.*  ,
  TAB_B.ACCESS_AUTH AS ACCESS_AUTH_01 ,
  CONCAT(TAB_B.SYSTEM_ID,':',TAB_B.HOSTNAME) HOST_PULLDOWN
FROM
  B_ANSIBLE_LRL_PHO_LINK_JNL TAB_A
  LEFT JOIN C_STM_LIST_JNL TAB_B ON (TAB_A.SYSTEM_ID = TAB_B.SYSTEM_ID)
WHERE
  TAB_A.DISUSE_FLAG = '0' AND
  TAB_B.DISUSE_FLAG = '0';

CREATE VIEW D_ANS_LRL_MAX_MEMBER_COL AS
SELECT
   TAB_A.*,
   TAB_B.VARS_NAME     AS DISP_VARS_NAME,
   CASE TAB_C.VRAS_NAME_ALIAS
        WHEN '0' THEN '-'
        ELSE VRAS_NAME_ALIAS
   END DISP_VRAS_NAME_ALIAS
FROM      B_ANS_LRL_MAX_MEMBER_COL   TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER  TAB_B ON ( TAB_A.VARS_NAME_ID    = TAB_B.VARS_NAME_ID    )
LEFT JOIN D_ANS_LRL_ARRAY_MEMBER     TAB_C ON ( TAB_A.ARRAY_MEMBER_ID = TAB_C.ARRAY_MEMBER_ID );

CREATE VIEW D_ANS_LRL_MAX_MEMBER_COL_JNL AS
SELECT
   TAB_A.*,
   TAB_B.VARS_NAME     AS DISP_VARS_NAME,
   CASE TAB_C.VRAS_NAME_ALIAS
        WHEN '0' THEN '-'
        ELSE VRAS_NAME_ALIAS
   END DISP_VRAS_NAME_ALIAS
FROM      B_ANS_LRL_MAX_MEMBER_COL_JNL   TAB_A
LEFT JOIN B_ANSIBLE_LRL_VARS_MASTER_JNL  TAB_B ON ( TAB_A.VARS_NAME_ID    = TAB_B.VARS_NAME_ID    )
LEFT JOIN D_ANS_LRL_ARRAY_MEMBER_JNL     TAB_C ON ( TAB_A.ARRAY_MEMBER_ID = TAB_C.ARRAY_MEMBER_ID );

-- -------------------------------------------------------
-- 共通  各作業インスタンスの結合版
-- -------------------------------------------------------
CREATE VIEW D_ANSIBLE_EXE_INS_MNG     AS 
SELECT
  'Legacy'      as DRIVER_NAME, 'L' as DRIVER_ID, EXECUTION_NO, STATUS_ID, TIME_BOOK, DISUSE_FLAG, LAST_UPDATE_TIMESTAMP
FROM
  C_ANSIBLE_LNS_EXE_INS_MNG
WHERE
  DISUSE_FLAG = '0'
UNION
SELECT
  'Pioneer'     as DRIVER_NAME, 'P' as DRIVER_ID, EXECUTION_NO, STATUS_ID, TIME_BOOK, DISUSE_FLAG, LAST_UPDATE_TIMESTAMP
FROM
  C_ANSIBLE_PNS_EXE_INS_MNG
WHERE
  DISUSE_FLAG = '0'
UNION
SELECT
  'Legacy-Role' as DRIVER_NAME, 'R' as DRIVER_ID, EXECUTION_NO, STATUS_ID, TIME_BOOK, DISUSE_FLAG, LAST_UPDATE_TIMESTAMP
FROM
  C_ANSIBLE_LRL_EXE_INS_MNG
WHERE
  DISUSE_FLAG = '0';
INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_IF_INFO_RIC',2,'2100040702',2100210001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_IF_INFO_JSQ',2,'2100040702',2100210002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_GLOBAL_VARS_MASTER_RIC',1,'2100040706',2100210003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_GLOBAL_VARS_MASTER_JSQ',1,'2100040706',2100210004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_CONTENTS_FILE_RIC',1,'2100040703',2100210005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_CONTENTS_FILE_JSQ',1,'2100040703',2100210006,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TEMPLATE_FILE_RIC',1,'2100040704',2100210007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TEMPLATE_FILE_JSQ',1,'2100040704',2100210008,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_COMVRAS_USLIST_RIC',1,'2100040707',2100210009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_COMVRAS_USLIST_JSQ',1,'2100040707',2100210010,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_HOST_RIC',1,'2100040708',2100210011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_HOST_JSQ',1,'2100040708',2100210012,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_PLAYBOOK_RIC',1,'2100020104',2100220001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_PLAYBOOK_JSQ',1,'2100020104',2100220002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_PHO_LINK_RIC',1,'2100020108',2100220003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_PHO_LINK_JSQ',1,'2100020108',2100220004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_PATTERN_LINK_RIC',1,'2100020105',2100220005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_PATTERN_LINK_JSQ',1,'2100020105',2100220006,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_VARS_MASTER_RIC',1,'2100020106',2100220007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_VARS_MASTER_JSQ',1,'2100020106',2100220008,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LNS_PTN_VARS_LINK_RIC',1,'2100020107',2100220009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LNS_PTN_VARS_LINK_JSQ',1,'2100020107',2100220010,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_VARS_ASSIGN_RIC',1,'2100020109',2100220011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LNS_VARS_ASSIGN_JSQ',1,'2100020109',2100220012,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_ANSIBLE_LNS_EXE_INS_MNG_RIC',1,'2100020113',2100220013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_ANSIBLE_LNS_EXE_INS_MNG_JSQ',1,'2100020113',2100220014,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_ROLE_PACKAGE_RIC',1,'2100020303',2100220015,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_ROLE_PACKAGE_JSQ',1,'2100020303',2100220016,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_ROLE_RIC',1,'2100020304',2100220017,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_ROLE_JSQ',1,'2100020304',2100220018,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_ROLE_VARS_RIC',1,'2100020305',2100220019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_ROLE_VARS_JSQ',1,'2100020305',2100220020,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_ANSIBLE_LRL_EXE_INS_MNG_RIC',1,'2100020314',2100220021,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_ANSIBLE_LRL_EXE_INS_MNG_JSQ',1,'2100020314',2100220022,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_PATTERN_LINK_RIC',1,'2100020307',2100220023,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_PATTERN_LINK_JSQ',1,'2100020307',2100220024,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_PHO_LINK_RIC',1,'2100020310',2100220025,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_PHO_LINK_JSQ',1,'2100020310',2100220026,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_VARS_MASTER_RIC',1,'2100020308',2100220027,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_VARS_MASTER_JSQ',1,'2100020308',2100220028,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_CHILD_VARS_RIC',1,'2100020315',2100220029,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_CHILD_VARS_JSQ',1,'2100020315',2100220030,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_PTN_VARS_LINK_RIC',1,'2100020309',2100220031,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_PTN_VARS_LINK_JSQ',1,'2100020309',2100220032,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_VARS_ASSIGN_RIC',1,'2100020311',2100220033,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_LRL_VARS_ASSIGN_JSQ',1,'2100020311',2100220034,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LNS_VAL_ASSIGN_RIC',1,'2100020115',2100220035,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LNS_VAL_ASSIGN_JSQ',1,'2100020115',2100220036,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_VAL_ASSIGN_RIC',1,'2100020316',2100220037,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_VAL_ASSIGN_JSQ',1,'2100020316',2100220038,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_ROLE_VARSVAL_RIC',1,'2100020317',2100220039,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_ROLE_VARSVAL_JSQ',1,'2100020317',2100220040,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_ARRAY_MEMBER_RIC',1,'2100020318',2100220041,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_ARRAY_MEMBER_JSQ',1,'2100020318',2100220042,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_MAX_MEMBER_COL_RIC',1,'2100020319',2100220043,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_MAX_MEMBER_COL_JSQ',1,'2100020319',2100220044,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_MEMBER_COL_COMB_RIC',1,'2100020320',2100220045,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_MEMBER_COL_COMB_JSQ',1,'2100020320',2100220046,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_RP_REP_VARS_LIST_RIC',1,'2100020322',2100220047,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_LRL_RP_REP_VARS_LIST_JSQ',1,'2100020322',2100220048,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_OS_TYPE_RIC',1,'2100000302',2100230001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_OS_TYPE_JSQ',1,'2100000302',2100230002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_DIALOG_TYPE_RIC',1,'2100020204',2100230003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_DIALOG_TYPE_JSQ',1,'2100020204',2100230004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_DIALOG_RIC',1,'2100020205',2100230005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_DIALOG_JSQ',1,'2100020205',2100230006,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_PHO_LINK_RIC',1,'2100020209',2100230007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_PHO_LINK_JSQ',1,'2100020209',2100230008,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_PATTERN_LINK_RIC',1,'2100020206',2100230009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_PATTERN_LINK_JSQ',1,'2100020206',2100230010,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_VARS_MASTER_RIC',1,'2100020207',2100230011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_VARS_MASTER_JSQ',1,'2100020207',2100230012,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_PNS_PTN_VARS_LINK_RIC',1,'2100020208',2100230013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_PNS_PTN_VARS_LINK_JSQ',1,'2100020208',2100230014,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_VARS_ASSIGN_RIC',1,'2100020210',2100230015,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_PNS_VARS_ASSIGN_JSQ',1,'2100020210',2100230016,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_ANSIBLE_PNS_EXE_INS_MNG_RIC',1,'2100020213',2100230017,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('C_ANSIBLE_PNS_EXE_INS_MNG_JSQ',1,'2100020213',2100230018,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_PNS_VAL_ASSIGN_RIC',1,'2100020214',2100230019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_PNS_VAL_ASSIGN_JSQ',1,'2100020214',2100230020,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_STATUS_RIC',11,NULL,2100290001,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_STATUS_JSQ',11,NULL,2100290002,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_RUN_MODE_RIC',3,NULL,2100290003,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_RUN_MODE_JSQ',3,NULL,2100290004,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_VARS_TYPE_RIC',4,NULL,2100290005,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_VARS_TYPE_JSQ',4,NULL,2100290006,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSTWR_INSTANCE_GROUP_RIC',1,NULL,2100290007,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSTWR_INSTANCE_GROUP_JSQ',1,NULL,2100290008,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWER_RUNDATA_DEL_FLAG_RIC',1,NULL,2100290009,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWER_RUNDATA_DEL_FLAG_JSQ',1,NULL,2100290010,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_EXEC_MODE_RIC',1,NULL,2100290011,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANSIBLE_EXEC_MODE_JSQ',1,NULL,2100290012,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_INSTANCE_GROUP_RIC',1,NULL,2100290013,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_INSTANCE_GROUP_JSQ',1,NULL,2100290014,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_JOBTP_PROPERTY_RIC',12,NULL,2100290015,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_JOBTP_PROPERTY_JSQ',12,NULL,2100290016,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_COMVRAS_USLIST_V_ID_RIC',4,NULL,2100290017,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_COMVRAS_USLIST_V_ID_JSQ',4,NULL,2100290018,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_COMVRAS_USLIST_F_ID_RIC',5,NULL,2100290019,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_COMVRAS_USLIST_F_ID_JSQ',5,NULL,2100290020,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_VIRTUALENV_RIC',1,NULL,2100290021,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_VIRTUALENV_JSQ',1,NULL,2100290022,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_ORGANIZATION_RIC',2,NULL,2100290023,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_ORGANIZATION_JSQ',2,NULL,2100290024,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_CREDENTIAL_TYPE_RIC',20,NULL,2100290025,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_CREDENTIAL_TYPE_JSQ',20,NULL,2100290026,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_PNS_LANG_MASTER_RIC',3,NULL,2100290027,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_PNS_LANG_MASTER_JSQ',3,NULL,2100290028,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_EXECUTION_ENVIRONMENT_RIC',1,NULL,2100290029,NULL,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_SEQUENCE (NAME,VALUE,MENU_ID,DISP_SEQ,NOTE,LAST_UPDATE_TIMESTAMP) VALUES('B_ANS_TWR_EXECUTION_ENVIRONMENT_JSQ',1,NULL,2100290030,'for the history table.',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020000,'Ansible Common','anscmn.png',70,'Ansible Common','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20000,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020000,'Ansible Common','anscmn.png',70,'Ansible Common','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020001,'Ansible-Legacy','anslgc.png',80,'Ansible-Legacy','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20001,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020001,'Ansible-Legacy','anslgc.png',80,'Ansible-Legacy','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020002,'Ansible-Pioneer','anspnr.png',90,'Ansible-Pioneer','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20002,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020002,'Ansible-Pioneer','anspnr.png',90,'Ansible-Pioneer','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST (MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020003,'Ansible-LegacyRole','anslr.png',100,'Ansible-LegacyRole','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_GROUP_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_GROUP_ID,MENU_GROUP_NAME,MENU_GROUP_ICON,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020003,'Ansible-LegacyRole','anslr.png',100,'Ansible-LegacyRole','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020103,2100020001,'Movement list',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20103,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020103,2100020001,'Movement list',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020104,2100020001,'Playbook files',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20104,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020104,2100020001,'Playbook files',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020105,2100020001,'Movement playbook link',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20105,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020105,2100020001,'Movement playbook link',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020106,2100020001,'Variable name list',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20106,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020106,2100020001,'Variable name list',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020107,2100020001,'Movement variable association list',NULL,NULL,NULL,1,0,1,2,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20107,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020107,2100020001,'Movement variable association list',NULL,NULL,NULL,1,0,1,2,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020108,2100020001,'Target host',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20108,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020108,2100020001,'Target host',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020109,2100020001,'Substitution value list',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20109,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020109,2100020001,'Substitution value list',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020111,2100020001,'Execution',NULL,NULL,NULL,1,0,1,1,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20111,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020111,2100020001,'Execution',NULL,NULL,NULL,1,0,1,1,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020112,2100020001,'Check operation status',NULL,NULL,NULL,1,0,2,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20112,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020112,2100020001,'Check operation status',NULL,NULL,NULL,1,0,2,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020113,2100020001,'Execution list',NULL,NULL,NULL,1,0,1,2,150,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20113,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020113,2100020001,'Execution list',NULL,NULL,NULL,1,0,1,2,150,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020203,2100020002,'Movement list',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20203,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020203,2100020002,'Movement list',NULL,NULL,NULL,1,0,1,1,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020204,2100020002,'Dialog type list',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20204,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020204,2100020002,'Dialog type list',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000302,2100020002,'OS type master',NULL,NULL,NULL,1,0,1,1,45,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-302,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000302,2100020002,'OS type master',NULL,NULL,NULL,1,0,1,1,45,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020205,2100020002,'Dialog files',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20205,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020205,2100020002,'Dialog files',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020206,2100020002,'Movement dialogue type link',NULL,NULL,NULL,1,0,1,1,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20206,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020206,2100020002,'Movement dialogue type link',NULL,NULL,NULL,1,0,1,1,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020207,2100020002,'Variable name list',NULL,NULL,NULL,1,0,1,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20207,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020207,2100020002,'Variable name list',NULL,NULL,NULL,1,0,1,2,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020208,2100020002,'Movement variable association list',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20208,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020208,2100020002,'Movement variable association list',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020209,2100020002,'Target host',NULL,NULL,NULL,1,0,1,2,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20209,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020209,2100020002,'Target host',NULL,NULL,NULL,1,0,1,2,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020210,2100020002,'Substitution value list',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20210,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020210,2100020002,'Substitution value list',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020211,2100020002,'Execution',NULL,NULL,NULL,1,0,1,1,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20211,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020211,2100020002,'Execution',NULL,NULL,NULL,1,0,1,1,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020212,2100020002,'Check operation status',NULL,NULL,NULL,1,0,2,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20212,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020212,2100020002,'Check operation status',NULL,NULL,NULL,1,0,2,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020213,2100020002,'Execution list',NULL,NULL,NULL,1,0,1,2,150,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20213,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020213,2100020002,'Execution list',NULL,NULL,NULL,1,0,1,2,150,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020303,2100020003,'Role package list',NULL,NULL,NULL,1,0,1,1,65,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20303,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020303,2100020003,'Role package list',NULL,NULL,NULL,1,0,1,1,65,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020304,2100020003,'Role name list',NULL,NULL,NULL,1,0,1,2,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20304,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020304,2100020003,'Role name list',NULL,NULL,NULL,1,0,1,2,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020305,2100020003,'Role variable name list',NULL,NULL,NULL,1,0,1,2,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20305,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020305,2100020003,'Role variable name list',NULL,NULL,NULL,1,0,1,2,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020306,2100020003,'Movement list',NULL,NULL,NULL,1,0,1,1,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20306,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020306,2100020003,'Movement list',NULL,NULL,NULL,1,0,1,1,60,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020307,2100020003,'Movement role link',NULL,NULL,NULL,1,0,1,1,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20307,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020307,2100020003,'Movement role link',NULL,NULL,NULL,1,0,1,1,70,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020308,2100020003,'Variable name list',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20308,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020308,2100020003,'Variable name list',NULL,NULL,NULL,1,0,1,2,80,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020309,2100020003,'Movement variable association list',NULL,NULL,NULL,1,0,1,2,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20309,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020309,2100020003,'Movement variable association list',NULL,NULL,NULL,1,0,1,2,90,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020310,2100020003,'Target host',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20310,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020310,2100020003,'Target host',NULL,NULL,NULL,1,0,1,2,100,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020311,2100020003,'Substitution value list',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20311,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020311,2100020003,'Substitution value list',NULL,NULL,NULL,1,0,1,2,110,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020312,2100020003,'Execution',NULL,NULL,NULL,1,0,1,1,120,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20312,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020312,2100020003,'Execution',NULL,NULL,NULL,1,0,1,1,120,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020313,2100020003,'Check operation status',NULL,NULL,NULL,1,0,2,2,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20313,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020313,2100020003,'Check operation status',NULL,NULL,NULL,1,0,2,2,130,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020314,2100020003,'Execution list',NULL,NULL,NULL,1,0,1,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20314,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020314,2100020003,'Execution list',NULL,NULL,NULL,1,0,1,2,140,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020315,2100020003,'Member variable list',NULL,NULL,NULL,1,0,1,2,150,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20315,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020315,2100020003,'Member variable list',NULL,NULL,NULL,1,0,1,2,150,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040702,2100020000,'Interface information',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20502,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040702,2100020000,'Interface information',NULL,NULL,NULL,1,0,1,1,20,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040706,2100020000,'Global variable list',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20506,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040706,2100020000,'Global variable list',NULL,NULL,NULL,1,0,1,2,30,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040703,2100020000,'File list',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20507,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040703,2100020000,'File list',NULL,NULL,NULL,1,0,1,1,40,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040704,2100020000,'template list',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20508,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040704,2100020000,'template list',NULL,NULL,NULL,1,0,1,1,50,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020115,2100020001,'Substitution value auto-registration setting',NULL,NULL,NULL,1,0,1,2,95,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20115,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020115,2100020001,'Substitution value auto-registration setting',NULL,NULL,NULL,1,0,1,2,95,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020214,2100020002,'Substitution value auto-registration setting',NULL,NULL,NULL,1,0,1,2,85,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20214,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020214,2100020002,'Substitution value auto-registration setting',NULL,NULL,NULL,1,0,1,2,85,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020316,2100020003,'Substitution value auto-registration setting',NULL,NULL,NULL,1,0,1,2,95,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20316,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020316,2100020003,'Substitution value auto-registration setting',NULL,NULL,NULL,1,0,1,2,95,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020317,2100020003,'Variable specific value list',NULL,NULL,NULL,1,0,1,2,160,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20317,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020317,2100020003,'Variable specific value list',NULL,NULL,NULL,1,0,1,2,160,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020318,2100020003,'Nested variable member list',NULL,NULL,NULL,1,0,1,2,170,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20318,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020318,2100020003,'Nested variable member list',NULL,NULL,NULL,1,0,1,2,170,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020319,2100020003,'Nested variable list',NULL,NULL,NULL,1,0,1,2,92,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20319,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020319,2100020003,'Nested variable list',NULL,NULL,NULL,1,0,1,2,92,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020320,2100020003,'Nested variable array combination list',NULL,NULL,NULL,1,0,1,2,180,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20320,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020320,2100020003,'Nested variable array combination list',NULL,NULL,NULL,1,0,1,2,180,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020322,2100020003,'Reading variable list',NULL,NULL,NULL,1,0,1,2,1000,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20322,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020322,2100020003,'Reading variable list',NULL,NULL,NULL,1,0,1,2,1000,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040707,2100020000,'Common variable use list',NULL,NULL,NULL,1,0,1,1,290,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40707,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040707,2100020000,'Common variable use list',NULL,NULL,NULL,1,0,1,1,290,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST (MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040708,2100020000,'Ansible Automation Controller host list',NULL,NULL,NULL,1,0,1,2,25,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_MENU_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,MENU_ID,MENU_GROUP_ID,MENU_NAME,WEB_PRINT_LIMIT,WEB_PRINT_CONFIRM,XLS_PRINT_LIMIT,LOGIN_NECESSITY,SERVICE_STATUS,AUTOFILTER_FLG,INITIAL_FILTER_FLG,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40708,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040708,2100020000,'Ansible Automation Controller host list',NULL,NULL,NULL,1,0,1,2,25,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100003,'a3c','5ebbc37e034d6874a2af59eb04beaa52','Legacy status checking procedure',NULL,NULL,NULL,NULL,'Legacy status checking procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100003,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100003,'a3c','5ebbc37e034d6874a2af59eb04beaa52','Legacy status checking procedure',NULL,NULL,NULL,NULL,'Legacy status checking procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100004,'a3e','5ebbc37e034d6874a2af59eb04beaa52','Legacy execution procedure',NULL,NULL,NULL,NULL,'Legacy execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100004,'a3e','5ebbc37e034d6874a2af59eb04beaa52','Legacy execution procedure',NULL,NULL,NULL,NULL,'Legacy execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100005,'a4c','5ebbc37e034d6874a2af59eb04beaa52','Pioneer  status checking procedure',NULL,NULL,NULL,NULL,'Pioneer  status checking procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100005,'a4c','5ebbc37e034d6874a2af59eb04beaa52','Pioneer  status checking procedure',NULL,NULL,NULL,NULL,'Pioneer  status checking procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100006,'a4e','5ebbc37e034d6874a2af59eb04beaa52','Pioneer execution procedure',NULL,NULL,NULL,NULL,'Pioneer execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100006,'a4e','5ebbc37e034d6874a2af59eb04beaa52','Pioneer execution procedure',NULL,NULL,NULL,NULL,'Pioneer execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100008,'a0z','5ebbc37e034d6874a2af59eb04beaa52','Ansible  association management procedure',NULL,NULL,NULL,NULL,'Ansible  association management procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100008,'a0z','5ebbc37e034d6874a2af59eb04beaa52','Ansible  association management procedure',NULL,NULL,NULL,NULL,'Ansible  association management procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100009,'a3a','5ebbc37e034d6874a2af59eb04beaa52','Legacy variable update procedure',NULL,NULL,NULL,NULL,'Legacy variable update procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100009,'a3a','5ebbc37e034d6874a2af59eb04beaa52','Legacy variable update procedure',NULL,NULL,NULL,NULL,'Legacy variable update procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100010,'a4a','5ebbc37e034d6874a2af59eb04beaa52','Pioneer variable update procedure',NULL,NULL,NULL,NULL,'Pioneer variable update procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100010,'a4a','5ebbc37e034d6874a2af59eb04beaa52','Pioneer variable update procedure',NULL,NULL,NULL,NULL,'Pioneer variable update procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100011,'a6c','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole status checking procedure',NULL,NULL,NULL,NULL,'LegacyRole status checking procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100011,'a6c','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole status checking procedure',NULL,NULL,NULL,NULL,'LegacyRole status checking procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100012,'a6e','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole execution procedure',NULL,NULL,NULL,NULL,'LegacyRole execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100012,'a6e','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole execution procedure',NULL,NULL,NULL,NULL,'LegacyRole execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100013,'a6a','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole variable update procedure',NULL,NULL,NULL,NULL,'LegacyRole variable update procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100013,'a6a','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole variable update procedure',NULL,NULL,NULL,NULL,'LegacyRole variable update procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100015,'a7b','5ebbc37e034d6874a2af59eb04beaa52','Ansible work history regular discard procedure',NULL,NULL,NULL,NULL,'Ansible work history regular discard procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100015,'a7b','5ebbc37e034d6874a2af59eb04beaa52','Ansible work history regular discard procedure',NULL,NULL,NULL,NULL,'Ansible work history regular discard procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100017,'a7b','5ebbc37e034d6874a2af59eb04beaa52','Legacy substitution value auto-registration setting procedure',NULL,NULL,NULL,NULL,'Legacy substitution value auto-registration setting procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100017,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100017,'a7b','5ebbc37e034d6874a2af59eb04beaa52','Legacy substitution value auto-registration setting procedure',NULL,NULL,NULL,NULL,'Legacy substitution value auto-registration setting procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100018,'a7c','5ebbc37e034d6874a2af59eb04beaa52','Pioneer substitution value auto-registration setting procedure',NULL,NULL,NULL,NULL,'Pioneer substitution value auto-registration setting procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100018,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100018,'a7c','5ebbc37e034d6874a2af59eb04beaa52','Pioneer substitution value auto-registration setting procedure',NULL,NULL,NULL,NULL,'Pioneer substitution value auto-registration setting procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100019,'a7d','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole substitution value auto-registration setting procedure',NULL,NULL,NULL,NULL,'LegacyRole substitution value auto-registration setting procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100019,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100019,'a7d','5ebbc37e034d6874a2af59eb04beaa52','LegacyRole substitution value auto-registration setting procedure',NULL,NULL,NULL,NULL,'LegacyRole substitution value auto-registration setting procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-121006,'a10f','5ebbc37e034d6874a2af59eb04beaa52','Ansible Automation Controller data sync procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-121006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-121006,'a10f','5ebbc37e034d6874a2af59eb04beaa52','Ansible Automation Controller data sync procedure',NULL,NULL,NULL,NULL,NULL,'H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST (USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100020,'a4e','5ebbc37e034d6874a2af59eb04beaa52','Ansible execution procedure',NULL,NULL,NULL,NULL,'Ansible execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ACCOUNT_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,USER_ID,USERNAME,PASSWORD,USERNAME_JP,MAIL_ADDRESS,PW_EXPIRATION,DEACTIVATE_PW_CHANGE,AUTH_TYPE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-100020,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',-100020,'a4e','5ebbc37e034d6874a2af59eb04beaa52','Ansible execution procedure',NULL,NULL,NULL,NULL,'Ansible execution procedure','H',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000302,1,2100000302,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-302,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000302,1,2100000302,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020103,1,2100020103,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20103,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020103,1,2100020103,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020104,1,2100020104,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20104,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020104,1,2100020104,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020105,1,2100020105,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20105,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020105,1,2100020105,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020106,1,2100020106,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20106,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020106,1,2100020106,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020107,1,2100020107,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20107,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020107,1,2100020107,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020110,1,2100020108,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20110,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020110,1,2100020108,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020109,1,2100020109,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20109,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020109,1,2100020109,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020111,1,2100020111,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20111,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020111,1,2100020111,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020112,1,2100020112,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20112,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020112,1,2100020112,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020113,1,2100020113,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20113,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020113,1,2100020113,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020115,1,2100020115,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20115,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020115,1,2100020115,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020203,1,2100020203,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20203,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020203,1,2100020203,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020204,1,2100020204,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20204,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020204,1,2100020204,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020205,1,2100020205,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20205,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020205,1,2100020205,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020206,1,2100020206,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20206,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020206,1,2100020206,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020207,1,2100020207,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20207,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020207,1,2100020207,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020208,1,2100020208,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20208,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020208,1,2100020208,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020210,1,2100020209,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20210,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020210,1,2100020209,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020209,1,2100020210,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20209,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020209,1,2100020210,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020211,1,2100020211,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20211,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020211,1,2100020211,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020212,1,2100020212,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20212,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020212,1,2100020212,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020213,1,2100020213,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20213,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020213,1,2100020213,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020214,1,2100020214,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20214,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020214,1,2100020214,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020303,1,2100020303,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20303,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020303,1,2100020303,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020304,1,2100020304,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20304,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020304,1,2100020304,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020305,1,2100020305,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20305,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020305,1,2100020305,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020306,1,2100020306,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20306,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020306,1,2100020306,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020307,1,2100020307,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20307,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020307,1,2100020307,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020308,1,2100020308,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20308,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020308,1,2100020308,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020309,1,2100020309,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20309,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020309,1,2100020309,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020310,1,2100020310,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20310,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020310,1,2100020310,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020311,1,2100020311,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20311,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020311,1,2100020311,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020312,1,2100020312,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20312,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020312,1,2100020312,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020313,1,2100020313,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20313,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020313,1,2100020313,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020314,1,2100020314,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20314,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020314,1,2100020314,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020315,1,2100020315,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20315,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020315,1,2100020315,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020316,1,2100020316,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20316,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020316,1,2100020316,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020317,1,2100020317,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20317,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020317,1,2100020317,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020318,1,2100020318,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20318,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020318,1,2100020318,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020319,1,2100020319,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20319,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020319,1,2100020319,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020320,1,2100020320,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20320,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020320,1,2100020320,2,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100020322,1,2100020322,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-20322,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100020322,1,2100020322,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040702,1,2100040702,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40702,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040702,1,2100040702,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040712,1,2100040706,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40712,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040712,1,2100040706,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040703,1,2100040703,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40703,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040703,1,2100040703,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040704,1,2100040704,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40704,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040704,1,2100040704,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040707,1,2100040707,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40707,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040707,1,2100040707,1,'System Administrator','1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100040708,1,2100040708,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-40708,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100040708,1,2100040708,1,'System Administrator','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020103,2100000002,2100020103,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020103,2100000002,2100020103,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020108,2100000002,2100020108,1,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020108,2100000002,2100020108,1,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020109,2100000002,2100020109,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020109,2100000002,2100020109,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020203,2100000002,2100020203,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020203,2100000002,2100020203,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020209,2100000002,2100020209,1,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000013,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020209,2100000002,2100020209,1,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020210,2100000002,2100020210,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000014,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020210,2100000002,2100020210,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020306,2100000002,2100020306,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000015,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020306,2100000002,2100020306,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020310,2100000002,2100020310,1,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000016,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020310,2100000002,2100020310,1,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST (LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2101020311,2100000002,2100020311,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_ROLE_MENU_LINK_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,LINK_ID,ROLE_ID,MENU_ID,PRIVILEGE,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-1000017,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2101020311,2100000002,2100020311,2,'oase action','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000004,3600,7200,'B_ANSIBLE_LNS_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Target host(Ansible-Legacy)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000004,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000004,3600,7200,'B_ANSIBLE_LNS_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Target host(Ansible-Legacy)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000005,3600,7200,'B_ANSIBLE_LNS_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(Ansible-Legacy)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000005,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000005,3600,7200,'B_ANSIBLE_LNS_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(Ansible-Legacy)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000006,3600,7200,'C_ANSIBLE_LNS_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT ANSIBLE_STORAGE_PATH_LNX AS PATH FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100020113/FILE_INPUT/','uploadfiles/2100020113/FILE_RESULT/','/__data_relay_storage__/legacy/ns/',NULL,'Execution list(Ansible-Legacy)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000006,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000006,3600,7200,'C_ANSIBLE_LNS_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT ANSIBLE_STORAGE_PATH_LNX AS PATH FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100020113/FILE_INPUT/','uploadfiles/2100020113/FILE_RESULT/','/__data_relay_storage__/legacy/ns/',NULL,'Execution list(Ansible-Legacy)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000007,3600,7200,'B_ANSIBLE_LRL_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Target host(Ansible-Role)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000007,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000007,3600,7200,'B_ANSIBLE_LRL_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Target host(Ansible-Role)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000008,3600,7200,'B_ANSIBLE_LRL_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(Ansible-Role)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000008,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000008,3600,7200,'B_ANSIBLE_LRL_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(Ansible-Role)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000009,3600,7200,'C_ANSIBLE_LRL_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT ANSIBLE_STORAGE_PATH_LNX AS PATH FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100020314/FILE_INPUT/','uploadfiles/2100020314/FILE_RESULT/','/__data_relay_storage__/legacy/rl/',NULL,'Execution list(Ansible-Role)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000009,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000009,3600,7200,'C_ANSIBLE_LRL_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT ANSIBLE_STORAGE_PATH_LNX AS PATH FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100020314/FILE_INPUT/','uploadfiles/2100020314/FILE_RESULT/','/__data_relay_storage__/legacy/rl/',NULL,'Execution list(Ansible-Role)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000010,3600,7200,'B_ANSIBLE_PNS_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Target host(Ansible-Pioneer)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000010,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000010,3600,7200,'B_ANSIBLE_PNS_PHO_LINK','PHO_LINK_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Target host(Ansible-Pioneer)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000011,3600,7200,'B_ANSIBLE_PNS_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(Ansible-Pioneer)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000011,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000011,3600,7200,'B_ANSIBLE_PNS_VARS_ASSIGN','ASSIGN_ID','OPERATION_NO_UAPK',NULL,NULL,NULL,NULL,NULL,'Substitution value list(Ansible-Pioneer)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST (ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2100000012,3600,7200,'C_ANSIBLE_PNS_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT ANSIBLE_STORAGE_PATH_LNX AS PATH FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100020213/FILE_INPUT/','uploadfiles/2100020213/FILE_RESULT/','/__data_relay_storage__/pioneer/ns/',NULL,'Execution list(Ansible-Pioneer)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO A_DEL_OPERATION_LIST_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,LG_DAYS,PH_DAYS,TABLE_NAME,PKEY_NAME,OPE_ID_COL_NAME,GET_DATA_STRAGE_SQL,DATA_PATH_1,DATA_PATH_2,DATA_PATH_3,DATA_PATH_4,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(-2100000012,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2100000012,3600,7200,'C_ANSIBLE_PNS_EXE_INS_MNG','EXECUTION_NO','OPERATION_NO_UAPK','SELECT ANSIBLE_STORAGE_PATH_LNX AS PATH FROM B_ANSIBLE_IF_INFO WHERE DISUSE_FLAG="0"','uploadfiles/2100020213/FILE_INPUT/','uploadfiles/2100020213/FILE_RESULT/','/__data_relay_storage__/pioneer/ns/',NULL,'Execution list(Ansible-Pioneer)','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100020001,'ky_legacy_varsautolistup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100020002,'ky_legacy_valautostup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100020003,'ky_pioneer_varsautolistup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100020004,'ky_pioneer_valautostup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100020005,'ky_legacy_role_varsautolistup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));

INSERT INTO A_PROC_LOADED_LIST (ROW_ID,PROC_NAME,LOADED_FLG,LAST_UPDATE_TIMESTAMP) VALUES(2100020006,'ky_legacy_role_valautostup-workflow','0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'));


INSERT INTO B_ANSIBLE_IF_INFO (ANSIBLE_IF_INFO_ID,ANSIBLE_STORAGE_PATH_LNX,ANSIBLE_STORAGE_PATH_ANS,SYMPHONY_STORAGE_PATH_ANS,CONDUCTOR_STORAGE_PATH_ANS,ANSIBLE_HOSTNAME,ANSIBLE_PROTOCOL,ANSIBLE_PORT,ANSTWR_HOST_ID,ANSTWR_PROTOCOL,ANSTWR_PORT,ANSIBLE_EXEC_MODE,ANSIBLE_EXEC_OPTIONS,ANSIBLE_EXEC_USER,ANSIBLE_ACCESS_KEY_ID,ANSIBLE_SECRET_ACCESS_KEY,ANSTWR_ORGANIZATION,ANSTWR_AUTH_TOKEN,ANSTWR_DEL_RUNTIME_DATA,NULL_DATA_HANDLING_FLG,ANS_GIT_HOSTNAME,ANS_GIT_USER,ANS_GIT_SSH_KEY_FILE,ANS_GIT_SSH_KEY_FILE_PASSPHRASE,ANSIBLE_NUM_PARALLEL_EXEC,ANSIBLE_REFRESH_INTERVAL,ANSIBLE_TAILLOG_LINES,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/ansible_driver','%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/ansible_driver','%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/symphony','%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/conductor','exastro-it-automation','https','443',NULL,'https','443','1','-v',NULL,'AccessKeyId','H2IwpzI0DJAwMKAmF2I5','Default',NULL,1,'2',NULL,'awx','rsa_awx_key',NULL,100,3000,1000,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_IF_INFO_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ANSIBLE_IF_INFO_ID,ANSIBLE_STORAGE_PATH_LNX,ANSIBLE_STORAGE_PATH_ANS,SYMPHONY_STORAGE_PATH_ANS,CONDUCTOR_STORAGE_PATH_ANS,ANSIBLE_HOSTNAME,ANSIBLE_PROTOCOL,ANSIBLE_PORT,ANSTWR_HOST_ID,ANSTWR_PROTOCOL,ANSTWR_PORT,ANSIBLE_EXEC_MODE,ANSIBLE_EXEC_OPTIONS,ANSIBLE_EXEC_USER,ANSIBLE_ACCESS_KEY_ID,ANSIBLE_SECRET_ACCESS_KEY,ANSTWR_ORGANIZATION,ANSTWR_AUTH_TOKEN,ANSTWR_DEL_RUNTIME_DATA,NULL_DATA_HANDLING_FLG,ANS_GIT_HOSTNAME,ANS_GIT_USER,ANS_GIT_SSH_KEY_FILE,ANS_GIT_SSH_KEY_FILE_PASSPHRASE,ANSIBLE_NUM_PARALLEL_EXEC,ANSIBLE_REFRESH_INTERVAL,ANSIBLE_TAILLOG_LINES,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/ansible_driver','%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/ansible_driver','%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/symphony','%%%%%ITA_DIRECTORY%%%%%/data_relay_storage/conductor','exastro-it-automation','https','443',NULL,'https','443','1','-v',NULL,'AccessKeyId','H2IwpzI0DJAwMKAmF2I5','Default',NULL,1,'2',NULL,'awx','rsa_awx_key',NULL,100,3000,1000,1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANSIBLE_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Normal',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Normal',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_RUN_MODE (RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Dry run',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_RUN_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,RUN_MODE_ID,RUN_MODE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Dry run',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_VARS_TYPE (VARS_TYPE_ID,VARS_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Generic variable',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_VARS_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,VARS_TYPE_ID,VARS_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Generic variable',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_VARS_TYPE (VARS_TYPE_ID,VARS_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Array-type variable',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_VARS_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,VARS_TYPE_ID,VARS_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Array-type variable',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_VARS_TYPE (VARS_TYPE_ID,VARS_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Nested variable',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_VARS_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,VARS_TYPE_ID,VARS_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Nested variable',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Ansible Legacy','ansible_driver/legacy/ns',2100020113,'C_ANSIBLE_LNS_EXE_INS_MNG','1',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Ansible Legacy','ansible_driver/legacy/ns',2100020113,'C_ANSIBLE_LNS_EXE_INS_MNG','1',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Ansible Pioneer','ansible_driver/pioneer/ns',2100020213,'C_ANSIBLE_PNS_EXE_INS_MNG','1',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Ansible Pioneer','ansible_driver/pioneer/ns',2100020213,'C_ANSIBLE_PNS_EXE_INS_MNG','1',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER (ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Ansible Legacy Role','ansible_driver/legacy/rl',2100020314,'C_ANSIBLE_LRL_EXE_INS_MNG','1',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ITA_EXT_STM_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ITA_EXT_STM_ID,ITA_EXT_STM_NAME,ITA_EXT_LINK_LIB_PATH,MENU_ID,EXEC_INS_MNG_TABLE_NAME,LOG_TARGET,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'Ansible Legacy Role','ansible_driver/legacy/rl',2100020314,'C_ANSIBLE_LRL_EXE_INS_MNG','1',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Unexecuted',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Unexecuted',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Preparing',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Preparing',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Executing',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Executing',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Executing (delay)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Executing (delay)',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'Completed',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'Completed',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'Completed (error)',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'Completed (error)',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Unexpected error',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'Unexpected error',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'Emergency stop',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'Emergency stop',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'Unexecuted (schedule)',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'Unexecuted (schedule)',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS (STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Schedule cancellation',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_STATUS_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,STATUS_ID,STATUS_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'Schedule cancellation',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_PNS_LANG_MASTER (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'utf-8',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_PNS_LANG_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'utf-8',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_PNS_LANG_MASTER (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'shift_jis',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_PNS_LANG_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'shift_jis',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_PNS_LANG_MASTER (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'euc',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_PNS_LANG_MASTER_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'euc',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANSIBLE_EXEC_MODE (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Ansible Core',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_EXEC_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Ansible Core',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_EXEC_MODE (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Ansible Tower',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_EXEC_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Ansible Tower',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_EXEC_MODE (ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Ansible Automation Controller',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANSIBLE_EXEC_MODE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Ansible Automation Controller',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'--forks=','-f(\\s)+','1','forks','0',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'--forks=','-f(\\s)+','1','forks','0',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'--limit=','-l(\\s)+','1','limit','0',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'--limit=','-l(\\s)+','1','limit','0',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,NULL,'-(v)+(\\s)+','2','verbosity','0',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,NULL,'-(v)+(\\s)+','2','verbosity','0',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'--extra-vars=','-e(\\s)+','4','extra_vars','0',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'--extra-vars=','-e(\\s)+','4','extra_vars','0',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'--tags=','-t(\\s)+','1','job_tags','0',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'--tags=','-t(\\s)+','1','job_tags','0',5,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'--skip-tags=',NULL,'1','skip_tags','0',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'--skip-tags=',NULL,'1','skip_tags','0',6,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'--start-at-task=',NULL,'1','start_at_task','0',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'--start-at-task=',NULL,'1','start_at_task','0',7,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'--become(\\s)+','-b(\\s)+','3','become_enabled','0',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'--become(\\s)+','-b(\\s)+','3','become_enabled','0',8,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'--diff(\\s)+','-D(\\s)+','3','diff_mode','0',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'--diff(\\s)+','-D(\\s)+','3','diff_mode','0',9,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'--use_fact_cache(\\s)+','-ufc(\\s)+','3','use_fact_cache','1',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'--use_fact_cache(\\s)+','-ufc(\\s)+','3','use_fact_cache','1',10,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,'--job_slice_count=','-jsc(\\s)+','1','job_slice_count','1',11,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',11,'--job_slice_count=','-jsc(\\s)+','1','job_slice_count','1',11,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY (ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,'--allow_simultaneous(\\s)+','-as(\\s)+','3','allow_simultaneous','1',12,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_JOBTP_PROPERTY_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROWID,KEY_NAME,SHORT_KEY_NAME,PROPERTY_TYPE,PROPERTY_NAME,TOWERONLY,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',12,'--allow_simultaneous(\\s)+','-as(\\s)+','3','allow_simultaneous','1',12,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_TWER_RUNDATA_DEL_FLAG (FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'delete',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWER_RUNDATA_DEL_FLAG_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,FLAG_ID,FLAG_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'delete',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_COMVRAS_USLIST_V_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'GBL',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_V_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'GBL',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_V_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'CPF',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_V_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'CPF',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_V_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'TPF',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_V_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'TPF',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_COMVRAS_USLIST_F_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Playbook',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Playbook',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Dialog file',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Dialog file',2,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'Role package',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'Role package',3,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID (ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Template file',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_COMVRAS_USLIST_F_ID_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Template file',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_TWR_ORGANIZATION (ROW_ID,ORGANIZATION_NAME,ORGANIZATION_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'Default',1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_ORGANIZATION_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,ROW_ID,ORGANIZATION_NAME,ORGANIZATION_ID,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'Default',1,NULL,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);

INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,'machine',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(1,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',1,'machine',1,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(2,'Vault',3,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',2,'Vault',3,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(3,'network',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',3,'network',4,NULL,'0',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(4,'Amazon Web Services',5,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',4,'Amazon Web Services',5,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(5,'OpenStack',6,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',5,'OpenStack',6,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(6,'VMware vCenter',7,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',6,'VMware vCenter',7,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(7,'Red Hat Satellite 6',8,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',7,'Red Hat Satellite 6',8,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(8,'Red Hat CloudForms',9,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',8,'Red Hat CloudForms',9,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(9,'Google Compute Engine',10,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',9,'Google Compute Engine',10,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(10,'Microsoft Azure Resource Manager',11,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',10,'Microsoft Azure Resource Manager',11,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(11,'GitHub Personal Access Token',12,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',11,'GitHub Personal Access Token',12,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(12,'Insights',14,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(14,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',12,'Insights',14,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(13,'Red Hat Virtualization',15,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(15,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',13,'Red Hat Virtualization',15,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(14,'Ansible Tower',16,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(16,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',14,'Ansible Tower',16,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(15,'OpenShift or Kubernetes API Bearer Token',17,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(17,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',15,'OpenShift or Kubernetes API Bearer Token',17,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(16,'CyberArk AIM Central Credential Provider Lookup',18,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(18,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',16,'CyberArk AIM Central Credential Provider Lookup',18,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(17,'Microsoft Azure Key Vault',19,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(19,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',17,'Microsoft Azure Key Vault',19,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(18,'CyberArk Conjur Secret Lookup',20,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(20,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',18,'CyberArk Conjur Secret Lookup',20,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(19,'HashiCorp Vault Secret Lookup',21,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(21,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',19,'HashiCorp Vault Secret Lookup',21,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE (CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(20,'HashiCorp Vault Signed SSH',22,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);
INSERT INTO B_ANS_TWR_CREDENTIAL_TYPE_JNL (JOURNAL_SEQ_NO,JOURNAL_REG_DATETIME,JOURNAL_ACTION_CLASS,CREDENTIAL_TYPE_ID,CREDENTIAL_TYPE_NAME,DISP_SEQ,NOTE,DISUSE_FLAG,LAST_UPDATE_TIMESTAMP,LAST_UPDATE_USER) VALUES(22,STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),'INSERT',20,'HashiCorp Vault Signed SSH',22,NULL,'1',STR_TO_DATE('2015/04/01 10:00:00.000000','%Y/%m/%d %H:%i:%s.%f'),1);


COMMIT;
